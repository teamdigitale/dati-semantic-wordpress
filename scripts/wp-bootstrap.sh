#!/bin/bash
# wp-bootstrap.sh - bootstrap WordPress in container (async, postStart)
#
# Scopo e comportamento principale:
# - Viene eseguito in background dal lifecycle.postStart del container.
# - Attende che il DB sia raggiungibile e garantisce che `wp-config.php`
#   esista (se possibile lo crea con `wp config create`).
# - In ambienti di produzione può iniettare early MySQL SSL defines se
#   il certificato CA è presente nel container.
# - Applica in modo idempotente il blocco `WORDPRESS_CONFIG_EXTRA` se
#   fornito tramite la variabile d'ambiente corrispondente.
# - Esegue un'operazione distruttiva (reset DB + import .wpress) SOLO quando
#   viene rilevato un trigger di bootstrap diverso dall'ultimo processato.
#
# Trigger e persistenza dello stato (esempio e note operative):
# - La Deployment aggiunge l'annotation `wordpress-bootstrap-trigger` sul pod template.
#   Esempio: `wordpress-bootstrap-trigger: "bootstrap-20251212-95-773e4e1"`.
# - Questa annotation viene mappata in container sulla variabile d'ambiente
#   `WORDPRESS_BOOTSTRAP_TRIGGER` tramite `valueFrom.fieldRef` (vedi manifest).
# - Lo script confronta `WORDPRESS_BOOTSTRAP_TRIGGER` con il contenuto di
#   `/var/lib/wp-bootstrap/last-bootstrap-trigger` (persistente, montato da PVC
#   con `subPath=wp-bootstrap-state`).
# - `updateImage.sh` imposta automaticamente l'annotation a `bootstrap-<imageTag>`
#   quando aggiorna l'immagine: in questo modo il trigger è legato alla versione
#   dell'immagine appena deployata.
# - Dopo un bootstrap completato con successo lo script chiama
#   `mark_bootstrap_success()` che sovrascrive il file di stato con il trigger
#   corrente. Successivi riavvii del pod salteranno la restore finché il trigger
#   non cambia.
#
# Nota importante: l'initContainer pulisce `/var/www/html` ad ogni start, ma
# il percorso di stato `/var/lib/wp-bootstrap` è su PVC/subPath e NON viene
# rimosso dall'initContainer; questo permette di mantenere memoria del
# bootstrap precedente.
#
set -euo pipefail

WP_PATH="/var/www/html"
WP_CONFIG="$WP_PATH/wp-config.php"
CONTENT_FILE="/tmp/content.wpress"
MARKER_IMPORTED="$WP_PATH/.wpress_imported"
STATE_DIR="/var/lib/wp-bootstrap"
STATE_TRIGGER_FILE="$STATE_DIR/last-bootstrap-trigger"
PLUGIN_DIR="/tmp/plugins"
PLUGIN_ZIP="$PLUGIN_DIR/all-in-one-wp-migration-unlimited-extension.zip"

# ENV
SITE_URL="${WORDPRESS_SITE_URL:-}"
DB_NAME="${WORDPRESS_DB_NAME:-}"
DB_USER="${WORDPRESS_DB_USER:-}"
DB_PASSWORD="${WORDPRESS_DB_PASSWORD:-}"
DB_HOST="${WORDPRESS_DB_HOST:-}"
DB_PREFIX="${WORDPRESS_TABLE_PREFIX:-wp_}"
WP_CONFIG_EXTRA="${WORDPRESS_CONFIG_EXTRA:-}"
BOOTSTRAP_TRIGGER="${WORDPRESS_BOOTSTRAP_TRIGGER:-}"

# Marker per il blocco extra in wp-config.php
MARK_START="/* BEGIN WORDPRESS_CONFIG_EXTRA */"
MARK_END="/* END WORDPRESS_CONFIG_EXTRA */"

log() { echo "[$(date +'%Y-%m-%d %H:%M:%S')] [bootstrap] $*"; }

log "DEBUG: DB_USER='${DB_USER:-}'"
log "DEBUG: BOOTSTRAP_TRIGGER='${BOOTSTRAP_TRIGGER:-}'"

# === PROD: abilita SSL MySQL solo per l'ambiente PROD ===
# Nota: se presente il CA, costruiamo un blocco PHP da inserire presto in wp-config.php
MYSQL_SSL_CA="/etc/ssl/mysql/azure-mysql-ca-cert.pem"

# DB_SSL_DEFINES will contain PHP define() lines to be injected early in wp-config.php
DB_SSL_DEFINES=""

if [ "${DB_USER:-}" = "pd_ndc_wp_ddl" ]; then
    log "PROD environment detected — checking MySQL SSL certificate..."
    if [ -f "$MYSQL_SSL_CA" ]; then
        log "MySQL SSL certificate found at $MYSQL_SSL_CA — will enable MySQL SSL (in wp-config.php early)."
        DB_SSL_DEFINES=$(cat <<PHP
/* BEGIN MYSQL SSL DEFINES */
define( 'MYSQL_CLIENT_FLAGS', MYSQLI_CLIENT_SSL );
define( 'MYSQL_SSL_CA', '$MYSQL_SSL_CA' );
/* END MYSQL SSL DEFINES */
PHP
)
    else
        log "WARNING: PROD environment detected but SSL certificate not found at $MYSQL_SSL_CA — SSL will NOT be enabled"
        log "This may cause connection issues in production!"
    fi
else
    log "DEV/TEST environment detected — MySQL SSL will NOT be enabled"
fi

has_db_ssl_defines() {
    [ -n "$DB_SSL_DEFINES" ]
}

# Applica `WORDPRESS_CONFIG_EXTRA` in modo idempotente (rimuove il blocco precedente e lo ricrea)
apply_wp_config_extra() {
    if [ -z "${WP_CONFIG_EXTRA:-}" ]; then
        log "No WORDPRESS_CONFIG_EXTRA set, skipping."
        return 0
    fi

    if [ ! -f "$WP_CONFIG" ]; then
        log "wp-config.php non trovato; non posso applicare WORDPRESS_CONFIG_EXTRA ora."
        return 1
    fi

    log "Applying WORDPRESS_CONFIG_EXTRA in an idempotent way..."

    # Rimuovi blocco precedente se esiste
    awk -v s="$MARK_START" -v e="$MARK_END" '
    BEGIN{inblock=0}
    {
      if (index($0,s)) { inblock=1; next }
      if (index($0,e)) { inblock=0; next }
      if (!inblock) print
    }' "$WP_CONFIG" > "${WP_CONFIG}.tmp" && mv "${WP_CONFIG}.tmp" "$WP_CONFIG"

    # Appende il nuovo blocco alla fine del file
    {
        printf "\n%s\n" "$MARK_START"
        printf "%s\n" "$WP_CONFIG_EXTRA"
        printf "%s\n" "$MARK_END"
    } >> "$WP_CONFIG"

    log "WORDPRESS_CONFIG_EXTRA applied."
    return 0
}

# Inserisce le define SSL DB *early* in wp-config.php (prima del "stop editing")
apply_db_ssl_defines_early() {
    has_db_ssl_defines || return 0
    [ -f "$WP_CONFIG" ] || return 1

    if grep -q "MYSQL_CLIENT_FLAGS" "$WP_CONFIG" >/dev/null 2>&1; then
        log "MySQL SSL defines already present in wp-config.php, skipping injection."
        return 0
    fi

    log "Injecting MySQL SSL defines early in wp-config.php"

    awk -v ssl="$DB_SSL_DEFINES" '
    BEGIN{done=0}
    /stop editing/ && !done {
        print ssl
        done=1
    }
    { print }
    ' "$WP_CONFIG" > "${WP_CONFIG}.tmp" && mv "${WP_CONFIG}.tmp" "$WP_CONFIG"

    log "MySQL SSL defines injected."
    return 0
}

# Wrapper che garantisce la presenza delle DB SSL defines (no-op se non richieste)
ensure_db_ssl_defines() {
    has_db_ssl_defines || return 0
    [ -f "$WP_CONFIG" ] || return 1

    if grep -q "MYSQL_CLIENT_FLAGS" "$WP_CONFIG" >/dev/null 2>&1; then
        return 0
    fi

    apply_db_ssl_defines_early
}

# Trigger/state gate per decidere se eseguire il bootstrap distruttivo (import) o saltarlo.
# Il deploy pipeline (es. `updateImage.sh`) imposta l'annotation
# `wordpress-bootstrap-trigger` a un valore del tipo `bootstrap-<imageTag>`.
# Questo valore viene passato al container tramite `WORDPRESS_BOOTSTRAP_TRIGGER`.
#
# La funzione confronta il trigger corrente con il valore memorizzato in
# `$STATE_TRIGGER_FILE` (montato su PVC con subPath). Comportamento:
# - Se non esiste file di stato => ritorna 0 (eseguire bootstrap)
# - Se il trigger corrente è diverso dall'ultimo salvato => ritorna 0
# - Se il trigger è uguale all'ultimo salvato => ritorna 1 (skip)
#
# Nota operativa: il file di stato è montato in `/var/lib/wp-bootstrap` via PVC
# e non viene sovrascritto dall'initContainer che pulisce `/var/www/html`.
should_run_bootstrap() {
    mkdir -p "$STATE_DIR"

    if [ -z "${BOOTSTRAP_TRIGGER:-}" ]; then
        log "No WORDPRESS_BOOTSTRAP_TRIGGER set, skipping destructive bootstrap."
        return 1
    fi

    if [ ! -f "$STATE_TRIGGER_FILE" ]; then
        log "No previous bootstrap state found; bootstrap will run."
        return 0
    fi

    LAST_TRIGGER=$(cat "$STATE_TRIGGER_FILE" 2>/dev/null || true)
    if [ "$LAST_TRIGGER" = "$BOOTSTRAP_TRIGGER" ]; then
        log "Bootstrap already processed (trigger=$BOOTSTRAP_TRIGGER); skipping restore."
        return 1
    fi

    log "Bootstrap trigger changed from '${LAST_TRIGGER:-<none>}' to '$BOOTSTRAP_TRIGGER'; bootstrap will run."
    return 0
}

mark_bootstrap_success() {
    # Scrive il trigger corrente su disco per evitare re-import su restart
    mkdir -p "$STATE_DIR"
    printf '%s\n' "$BOOTSTRAP_TRIGGER" > "$STATE_TRIGGER_FILE"
}

# Ensure/crea wp-config.php usando wp-cli se possibile
ensure_wp_config() {
    if [ -f "$WP_CONFIG" ]; then
        log "wp-config.php esiste già."
        if has_db_ssl_defines; then
            log "Adding SSL definitions to existing wp-config.php..."
            if ! ensure_db_ssl_defines; then
                log "Failed to insert SSL definitions, trying alternative method..."
                sed -i "1a\\$DB_SSL_DEFINES" "$WP_CONFIG"
            fi
            log "SSL definitions added to existing wp-config.php."
        fi
        return 0
    fi

    # Se non abbiamo almeno DB_NAME e DB_USER, non possiamo creare il config
    if [ -z "${DB_NAME}" ] || [ -z "${DB_USER}" ]; then
        log "Impossibile creare wp-config.php: manca WORDPRESS_DB_NAME o WORDPRESS_DB_USER."
        return 1
    fi

    log "wp-config.php non trovato: provo a crearne uno con wp-cli..."

    # Creazione base del wp-config.php
    wp config create \
        --dbname="$DB_NAME" \
        --dbuser="$DB_USER" \
        --dbpass="$DB_PASSWORD" \
        --dbhost="$DB_HOST" \
        --dbprefix="$DB_PREFIX" \
        --allow-root --path="$WP_PATH"

    if [ -f "$WP_CONFIG" ]; then
        log "wp-config.php creato correttamente. Applying early DB SSL defines if needed."
        ensure_db_ssl_defines || log "Attenzione: non è stato possibile iniettare le DB SSL defines subito dopo la creazione del wp-config.php."
        return 0
    else
        log "Creazione wp-config.php FALLITA."
        return 1
    fi
}

bootstrap_wp() {
    # Flusso principale (asincrono): gate -> attesa DB -> reset/install/import
    log "Bootstrap async started."

    if ! should_run_bootstrap; then
        log "Bootstrap skipped by trigger/state gate."
        return 0
    fi

    ensure_db_ssl_defines || log "Attenzione: non è stato possibile applicare le DB SSL defines all'inizio."

    # Attendi il DB (max 60s)
    log "Waiting for WordPress DB connection (max 60s)..."
    TIMEOUT=60
    END=$((SECONDS + TIMEOUT))
    DB_OK=false
    set +e
    while [ $SECONDS -lt $END ]; do
        # Se wp-config.php non esiste, wp db query fallirà: tenta comunque di creare wp-config.php se possibile
        if [ ! -f "$WP_CONFIG" ]; then
            ensure_wp_config || true
        else
            ensure_db_ssl_defines || true
        fi

        if wp db query 'SELECT 1' --allow-root --path="$WP_PATH" >/dev/null 2>&1; then
            DB_OK=true
            break
        fi

        log "DB not ready, retrying..."
        sleep 3
    done
    set -e

    if [ "$DB_OK" = false ]; then
        log "DB not reachable — skipping bootstrap."
        return 0
    fi

    # Se wp-config.php esiste ora, applica sempre le extra (idempotente)
    if [ -f "$WP_CONFIG" ]; then
        apply_wp_config_extra || log "Attenzione: non è stato possibile applicare WORDPRESS_CONFIG_EXTRA in questa fase."
    else
        log "wp-config.php ancora mancante dopo la connessione DB. Procedo comunque."
    fi

    # Reset DB e installare WordPress
    log "DB OK — resetting WordPress..."
    wp db reset --yes --allow-root --path="$WP_PATH"

    log "Running wp core install..."
    wp core install \
        --url="$SITE_URL" \
        --title="Dev WP" \
        --admin_user="admin" \
        --admin_password="admin" \
        --admin_email="admin@example.com" \
        --skip-email \
        --allow-root \
        --path="$WP_PATH"

    # Se per qualche motivo sono state aggiunte le extra dopo l'install, riproviamo ad applicarle
    if [ -f "$WP_CONFIG" ]; then
        apply_wp_config_extra || true
    fi

    # Installa plugin incluso nello image
    if [ -f "$PLUGIN_ZIP" ]; then
        log "Installing AI1WM Unlimited plugin..."
        wp plugin install "$PLUGIN_ZIP" --activate --allow-root --path="$WP_PATH"
        log "Updating AI1WM Unlimited plugin (if available)..."
        wp plugin update all-in-one-wp-migration-unlimited-extension --allow-root --path="$WP_PATH" || log "Plugin update failed or not available"
    else
        log "Plugin zip non presente ($PLUGIN_ZIP) — skipping plugin install."
    fi

    log "Waiting 10s before running import..."
    sleep 10

    if [ -f "$CONTENT_FILE" ]; then
        log "Copying .wpress into ai1wm-backups..."
        mkdir -p "$WP_PATH/wp-content/ai1wm-backups"
        cp "$CONTENT_FILE" "$WP_PATH/wp-content/ai1wm-backups/"

        log "Importing .wpress..."
        wp ai1wm restore "$(basename "$CONTENT_FILE")" --yes --allow-root --path="$WP_PATH"

        log "Regenerating permalinks..."
        wp rewrite flush --hard --allow-root --path="$WP_PATH"

        # Oxygen Builder regeneration
        if [ ! -f "$WP_PATH/wp-content/mu-plugins/oxygen-regenerate.php" ]; then
            log "WARNING: oxygen-regenerate.php mu-plugin not found! Skipping Oxygen regeneration."
        else
            DOMAIN=$(echo "$SITE_URL" | sed -E 's|^https?://||' | sed 's|/.*||')
            if [ -n "$DOMAIN" ]; then
                log "Configuring Oxygen domain: $DOMAIN"
                if [ -n "$SITE_URL" ]; then
                    wp option update oxygen_regenerate_site_domain "$DOMAIN" --allow-root --url="$SITE_URL" --path="$WP_PATH" 2>/dev/null || true
                else
                    wp option update oxygen_regenerate_site_domain "$DOMAIN" --allow-root --path="$WP_PATH" 2>/dev/null || true
                fi
            fi
            log "Running Oxygen regeneration..."
            if [ -n "$SITE_URL" ]; then
                WP_REGENERATE_CMD="wp oxygen regenerate --force-css --allow-root --url=\"$SITE_URL\" --path=\"$WP_PATH\""
            else
                WP_REGENERATE_CMD="wp oxygen regenerate --force-css --allow-root --path=\"$WP_PATH\""
            fi
            if eval "$WP_REGENERATE_CMD" 2>&1; then
                log "Oxygen regeneration completed successfully."
            else
                log "Oxygen regeneration completed with warnings (check output above)."
            fi
        fi

        touch "$MARKER_IMPORTED"
        mark_bootstrap_success
        log "Import completed."
    else
        log "No .wpress found, skipping restore."
    fi

    # Assicura ownership corretta
    if command -v chown >/dev/null 2>&1; then
        log "Setting ownership $WP_PATH -> www-data:www-data (if applicabile)..."
        chown -R www-data:www-data "$WP_PATH" || true
    fi

    log "Bootstrap finished."
}

# Lancia in background
bootstrap_wp &

log "Async bootstrap launched."

exit 0
