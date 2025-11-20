#!/bin/bash
set -euo pipefail

WP_PATH="/var/www/html"
CONTENT_FILE="/tmp/content.wpress"
MARKER="$WP_PATH/.wpress_imported"

DB_HOST="${WORDPRESS_DB_HOST}"
DB_USER="${WORDPRESS_DB_USER}"
DB_PASSWORD="${WORDPRESS_DB_PASSWORD}"
DB_NAME="${WORDPRESS_DB_NAME}"
SITE_URL="https://wp-ndc-dev.apps.cloudpub.testedev.istat.it"

echo "=== WordPress auto-install & .wpress import ==="

# Avvia WP core in modalità "setup" (docker-entrypoint standard)
docker-entrypoint.sh true

# --- Attendi DB disponibile ---
echo "Waiting for DB at $DB_HOST ..."
for i in {1..60}; do
    if mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" -e "SELECT 1;" >/dev/null 2>&1; then
        echo "Database reachable"
        break
    fi
    echo "Database not ready ($i/60), retrying..."
    sleep 2
    if [ $i -eq 60 ]; then
        echo "ERROR: Database not reachable after 2 minutes"
        exit 1
    fi
done

# --- Azzera e ricrea DB ---
echo "Dropping & creating database $DB_NAME ..."
mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" -e "DROP DATABASE IF EXISTS \`$DB_NAME\`; CREATE DATABASE \`$DB_NAME\`;"

# --- Installa WordPress ---
echo "Installing WordPress core..."
wp core install \
    --url="$SITE_URL" \
    --title="Dev WP" \
    --admin_user="admin" \
    --admin_password="admin" \
    --admin_email="admin@example.com" \
    --skip-email \
    --allow-root

# --- Importa contenuto .wpress ---
if [ -f "$CONTENT_FILE" ]; then
    echo "Found .wpress file: $CONTENT_FILE"
    wp plugin install all-in-one-wp-migration --activate --allow-root
    wp ai1wm import "$CONTENT_FILE" --yes --allow-root
    echo "Import completed"
    touch "$MARKER"
    rm -f "$CONTENT_FILE"
else
    echo "No .wpress file found at $CONTENT_FILE, skipping import"
fi

# --- Avvia Apache ---
echo "Starting Apache..."
exec docker-entrypoint.sh "$@"
