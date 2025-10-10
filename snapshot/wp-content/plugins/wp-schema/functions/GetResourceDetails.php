<?php 

add_shortcode('get-resource-details', 'render_resource_details');
add_shortcode('get-resource-array', 'render_resource_array');
add_shortcode('get-resource-debug', 'render_resource_debug');

// Individual field shortcodes
add_shortcode('resource-assetIri', 'render_resource_assetIri');
add_shortcode('resource-title', 'render_resource_title');
add_shortcode('resource-description', 'render_resource_description');
add_shortcode('resource-type', 'render_resource_type');
add_shortcode('resource-modifiedOn', 'render_resource_modifiedOn');
add_shortcode('resource-issuedOn', 'render_resource_issuedOn');
add_shortcode('resource-versionInfo', 'render_resource_versionInfo');
add_shortcode('resource-rightsHolder', 'render_resource_rightsHolder');
add_shortcode('resource-labels', 'render_resource_labels');
add_shortcode('resource-status', 'render_resource_status');
add_shortcode('resource-contactPoint-iri', 'render_resource_contactPoint_iri');
add_shortcode('resource-contactPoint-summary', 'render_resource_contactPoint_summary');
add_shortcode('resource-publishers', 'render_resource_publishers');
add_shortcode('resource-creators', 'render_resource_creators');
add_shortcode('resource-languages', 'render_resource_languages');
add_shortcode('resource-themes', 'render_resource_themes');
add_shortcode('resource-type-button', 'render_resource_type_button');
add_shortcode('resource-type-css', 'render_resource_type_css');
add_shortcode('resource-accrualPeriodicity', 'render_resource_accrualPeriodicity');
add_shortcode('resource-prefix', 'render_resource_prefix');
add_shortcode('resource-projects', 'render_resource_projects');
add_shortcode('resource-API-button', 'render_resource_API_button');
add_shortcode('resource-SPARQL-button', 'render_resource_SPARQL_button');
add_shortcode('resource-swagger', 'render_resource_swagger');
add_shortcode('resource-schemaeditor', 'render_resource_schemaeditor');

// Global variable to store the current resource data
global $current_resource_data;
$current_resource_data = null;

function render_resource_details() {
    global $current_resource_data;
    
    if (!isset($_GET['uri']) || empty($_GET['uri'])) {
        return '';
    }

    $uri = $_GET['uri'];
    $encoded_uri = urlencode($uri);
    $cache_key = md5($uri);
    
    // Try to get from cache first
    $data = get_resource_transient($cache_key);
    $cache_hit = ($data !== false);
    
    if (!$cache_hit) {
        // Cache miss - call API
        if (!defined('WP_SCHEMA_API_BASE_URL')) {
            return '';
        }
        
        $api_url = WP_SCHEMA_API_BASE_URL . 'semantic-assets/by-iri?iri=' . $encoded_uri;
        $response = wp_remote_get($api_url);
        
        if (is_wp_error($response)) {
            return '';
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        if ($status_code !== 200) {
            return '';
        }
        
        $body = wp_remote_retrieve_body($response);
        if (empty($body)) {
            return '';
        }
        
        $data = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return '';
        }
        
        // Save to cache for 1 hour
        set_resource_transient($cache_key, $data, 3600);
    }
    
    // Store data globally for other shortcodes
    $current_resource_data = $data;
    
    // Clean expired cache entries
    purge_expired_resource_transients();
    
    return '';
}

function render_resource_array() {
    global $current_resource_data;
    
    if ($current_resource_data === null) {
        return '<div class="error">Nessun dato risorsa disponibile. Usa prima [get-resource-details].</div>';
    }
    
    ob_start();
    echo '<h3>Dati Risorsa:</h3>';
    echo '<pre>';
    print_r($current_resource_data);
    echo '</pre>';
    
    // Debug completo - Array con tutti i valori API
    echo '<h3>🔍 Debug Completo - Tutti i valori API:</h3>';
    echo '<div style="background: #f8f9fa; padding: 20px; border: 1px solid #dee2e6; border-radius: 5px; margin: 20px 0;">';
    echo '<h4>📊 Array completo restituito dall\'API:</h4>';
    echo '<pre style="background: #fff; padding: 15px; border: 1px solid #ddd; border-radius: 3px; overflow-x: auto; font-size: 12px;">';
    echo htmlspecialchars(json_encode($current_resource_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo '</pre>';
    
    echo '<h4>📋 Chiavi disponibili:</h4>';
    echo '<ul>';
    foreach ($current_resource_data as $key => $value) {
        $type = gettype($value);
        $preview = '';
        
        if (is_array($value)) {
            $preview = ' (Array con ' . count($value) . ' elementi)';
        } elseif (is_string($value)) {
            $preview = ' (Stringa: "' . substr($value, 0, 50) . (strlen($value) > 50 ? '...' : '') . '")';
        } elseif (is_bool($value)) {
            $preview = ' (Boolean: ' . ($value ? 'true' : 'false') . ')';
        } elseif (is_numeric($value)) {
            $preview = ' (Numero: ' . $value . ')';
        }
        
        echo '<li><strong>' . esc_html($key) . ':</strong> ' . $type . $preview . '</li>';
    }
    echo '</ul>';
    
    echo '<h4>🌐 URL API utilizzato:</h4>';
    if (isset($_GET['uri']) && !empty($_GET['uri'])) {
        $uri = $_GET['uri'];
        $encoded_uri = urlencode($uri);
        $api_url = WP_SCHEMA_API_BASE_URL . 'semantic-assets/by-iri?iri=' . $encoded_uri;
        echo '<p><code>' . esc_html($api_url) . '</code></p>';
    }
    
    echo '</div>';
    
    return ob_get_clean();
}

function render_resource_debug() {
    global $current_resource_data;
    
    ob_start();
    echo '<h3>Debug Informazioni:</h3>';
    echo '<div class="debug-info">';
    
    // Check if URI parameter exists
    if (isset($_GET['uri']) && !empty($_GET['uri'])) {
        echo '<p><strong>✅ URI presente:</strong> ' . htmlspecialchars($_GET['uri']) . '</p>';
        
        // Check API configuration
        if (defined('WP_SCHEMA_API_BASE_URL')) {
            echo '<p><strong>✅ Configurazione API:</strong> ' . htmlspecialchars(WP_SCHEMA_API_BASE_URL) . '</p>';
        } else {
            echo '<p><strong>❌ Configurazione API:</strong> Non definita</p>';
        }
        
        // Check cache status
        $uri = $_GET['uri'];
        $cache_key = md5($uri);
        $cached_data = get_resource_transient($cache_key);
        
        if ($cached_data !== false) {
            echo '<p><strong>✅ Cache:</strong> Dati presenti in cache</p>';
        } else {
            echo '<p><strong>❌ Cache:</strong> Nessun dato in cache</p>';
        }
        
        // Check if data is loaded
        if ($current_resource_data !== null) {
            echo '<p><strong>✅ Dati caricati:</strong> Sì</p>';
            echo '<p><strong>📊 Campi disponibili:</strong> ' . count($current_resource_data) . '</p>';
            
            // Show available fields
            echo '<p><strong>📋 Campi principali:</strong></p>';
            echo '<ul>';
            $main_fields = ['title', 'description', 'type', 'modifiedOn', 'issuedOn', 'rightsHolder', 'themes'];
            foreach ($main_fields as $field) {
                if (isset($current_resource_data[$field])) {
                    echo '<li><strong>' . $field . ':</strong> Presente</li>';
                } else {
                    echo '<li><strong>' . $field . ':</strong> Non presente</li>';
                }
            }
            echo '</ul>';
        } else {
            echo '<p><strong>❌ Dati caricati:</strong> No - Usa [get-resource-details] prima</p>';
        }
        
    } else {
        echo '<p><strong>❌ URI:</strong> Parametro mancante</p>';
    }
    
    // Check cache table
    global $wpdb;
    $table = $wpdb->prefix . 'ResourceTransients';
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table'") == $table;
    
    if ($table_exists) {
        $cache_count = $wpdb->get_var("SELECT COUNT(*) FROM $table");
        echo '<p><strong>✅ Tabella cache:</strong> Esiste (' . $cache_count . ' elementi)</p>';
    } else {
        echo '<p><strong>❌ Tabella cache:</strong> Non esiste</p>';
    }
    
    echo '</div>';
    return ob_get_clean();
}

// Individual field shortcode functions
function render_resource_assetIri() {
    global $current_resource_data;
    return isset($current_resource_data['assetIri']) ? $current_resource_data['assetIri'] : '';
}

function render_resource_title() {
    global $current_resource_data;
    return isset($current_resource_data['title']) ? $current_resource_data['title'] : '';
}

function render_resource_description() {
    global $current_resource_data;
    return isset($current_resource_data['description']) ? $current_resource_data['description'] : '';
}

function render_resource_type() {
    global $current_resource_data;
    return isset($current_resource_data['type']) ? $current_resource_data['type'] : '';
}

function render_resource_modifiedOn() {
    global $current_resource_data;
    if (!isset($current_resource_data['modifiedOn']) || empty($current_resource_data['modifiedOn'])) {
        return '';
    }
    $date = DateTime::createFromFormat('Y-m-d', $current_resource_data['modifiedOn']);
    return $date ? $date->format('d/m/Y') : $current_resource_data['modifiedOn'];
}

function render_resource_issuedOn() {
    global $current_resource_data;
    if (!isset($current_resource_data['issuedOn']) || empty($current_resource_data['issuedOn'])) {
        return '';
    }
    $date = DateTime::createFromFormat('Y-m-d', $current_resource_data['issuedOn']);
    return $date ? $date->format('d/m/Y') : $current_resource_data['issuedOn'];
}

function render_resource_versionInfo() {
    global $current_resource_data;
    return isset($current_resource_data['versionInfo']) ? $current_resource_data['versionInfo'] : '';
}

function render_resource_rightsHolder() {
    global $current_resource_data;
    if (!isset($current_resource_data['rightsHolder']) || !is_array($current_resource_data['rightsHolder'])) {
        return '';
    }
    $holder = $current_resource_data['rightsHolder'];
    if (isset($holder['iri']) && isset($holder['summary'])) {
        return '<a href="' . esc_url($holder['iri']) . '" alt="' . esc_attr($holder['summary']) . '">' . esc_html($holder['summary']) . '</a>';
    }
    return '';
}

function render_resource_labels() {
    global $current_resource_data;
    if (!isset($current_resource_data['labels']) || !is_array($current_resource_data['labels'])) {
        return '';
    }
    
    $odd_labels = [];
    foreach ($current_resource_data['labels'] as $index => $label) {
        if ($index % 2 == 1) { // Solo indici dispari (1, 3, 5, ...)
            $odd_labels[] = '<li>' . esc_html($label) . '</li>';
        }
    }
    
    if (empty($odd_labels)) {
        return '';
    }
    
    return '<ul>' . implode('', $odd_labels) . '</ul>';
}

function render_resource_status() {
    global $current_resource_data;
    if (!isset($current_resource_data['status']) || !is_array($current_resource_data['status'])) {
        return '';
    }
    return implode(', ', $current_resource_data['status']);
}

function render_resource_contactPoint_iri() {
    global $current_resource_data;
    if (!isset($current_resource_data['contactPoint']) || !is_array($current_resource_data['contactPoint']) || !isset($current_resource_data['contactPoint']['iri'])) {
        return '';
    }
    $iri = $current_resource_data['contactPoint']['iri'];
    return '<a href="' . esc_url($iri) . '" alt="' . esc_attr($iri) . '">' . esc_html($iri) . '</a>';
}

function render_resource_contactPoint_summary() {
    global $current_resource_data;
    if (!isset($current_resource_data['contactPoint']) || !is_array($current_resource_data['contactPoint']) || !isset($current_resource_data['contactPoint']['summary'])) {
        return '';
    }
    $summary = $current_resource_data['contactPoint']['summary'];
    $display_text = str_replace('mailto:', '', $summary);
    return '<a href="' . esc_url($summary) . '" alt="' . esc_attr($display_text) . '">' . esc_html($display_text) . '</a>';
}

function render_resource_publishers() {
    global $current_resource_data;
    if (!isset($current_resource_data['publishers']) || !is_array($current_resource_data['publishers'])) {
        return '';
    }
    
    $links = [];
    foreach ($current_resource_data['publishers'] as $publisher) {
        if (isset($publisher['iri']) && isset($publisher['summary'])) {
            $links[] = '<a href="' . esc_url($publisher['iri']) . '" alt="' . esc_attr($publisher['summary']) . '">' . esc_html($publisher['summary']) . '</a>';
        }
    }
    return implode(', ', $links);
}

function render_resource_creators() {
    global $current_resource_data;
    if (!isset($current_resource_data['creators']) || !is_array($current_resource_data['creators'])) {
        return '';
    }
    
    $links = [];
    foreach ($current_resource_data['creators'] as $creator) {
        if (isset($creator['iri']) && isset($creator['summary'])) {
            $links[] = '<a href="' . esc_url($creator['iri']) . '" alt="' . esc_attr($creator['summary']) . '">' . esc_html($creator['summary']) . '</a>';
        }
    }
    return implode(', ', $links);
}

function render_resource_languages() {
    global $current_resource_data;
    if (!isset($current_resource_data['languages']) || !is_array($current_resource_data['languages'])) {
        return '';
    }
    
    $language_names = [];
    foreach ($current_resource_data['languages'] as $language_url) {
        if (strpos($language_url, '/ITA') !== false) {
            $language_names[] = 'Italiano';
        } elseif (strpos($language_url, '/ENG') !== false) {
            $language_names[] = 'Inglese';
        } else {
            $language_names[] = $language_url; // fallback per lingue non riconosciute
        }
    }
    return implode(', ', $language_names);
}

function render_resource_themes() {
    global $current_resource_data;
    if (!isset($current_resource_data['themes']) || !is_array($current_resource_data['themes'])) {
        return '';
    }
    
    $theme_labels = [];
    foreach ($current_resource_data['themes'] as $theme_url) {
        $label = ThemeDataHelper::getLabelItByUrl($theme_url);
        if ($label) {
            $theme_labels[] = $label;
        } else {
            $theme_labels[] = $theme_url; // fallback se non trovato nel mapping
        }
    }
    return implode(', ', $theme_labels);
}

function render_resource_type_button() {
    global $current_resource_data;
    if (!isset($current_resource_data['type'])) {
        return '<button class="btn cnd-btn-primary" aria-label="Tipologia non disponibile">Tipologia non disponibile</button>';
    }
    
    $type = $current_resource_data['type'];
    
    switch ($type) {
        case 'ONTOLOGY':
            return '<button class="btn cnd-btn-primary" aria-label="Ontologia">Ontologia</button>';
        case 'CONTROLLED_VOCABULARY':
            return '<button class="btn cnd-btn-primary" aria-label="Vocabolario Controllato">Vocabolario Controllato</button>';
        case 'SCHEMA':
            return '<button class="btn cnd-btn-primary" aria-label="Schema">Schema</button>';
        default:
            return '<button class="btn cnd-btn-primary" aria-label="Tipologia non disponibile">Tipologia non disponibile</button>';
    }
}

function render_resource_type_css() {
    global $current_resource_data;
    if (!isset($current_resource_data['type'])) {
        return '<style>
.cnd-btn-primary { 
         background-color: #000;         
}
.cnd-dtl-bg {  
        background-color: #FFF;        
 }         
.cnd-progress-cnt {             
         background-color: #DDD;         
}         
.cnd-progress-bar {             
         background-color: #000;         
}     
</style>';
    }
    
    $type = $current_resource_data['type'];
    
    switch ($type) {
        case 'ONTOLOGY':
            return '<style>
.cnd-btn-primary { 
         background-color: #0043E3;         
}
.cnd-dtl-bg {  
        background-color: #F2F7FC;        
 }         
.cnd-progress-cnt {             
         background-color: #D3D9EE;         
}         
.cnd-progress-bar {             
         background-color: #0043E3;         
}     
</style>';
        case 'CONTROLLED_VOCABULARY':
            return '<style>
.cnd-btn-primary { 
         background-color: #077F7B;         
}
.cnd-dtl-bg {  
        background-color: #F7FFFF;        
 }         
.cnd-progress-cnt {             
         background-color: #52E0DB;         
}         
.cnd-progress-bar {             
         background-color: #09AFA9;         
}     
</style>';
        case 'SCHEMA':
            return '<style>
.cnd-btn-primary { 
         background-color: #EBA704;         
}
.cnd-dtl-bg {  
        background-color: #FFFDF6;        
 }         
.cnd-progress-cnt {             
         background-color: #FFF8E6;         
}         
.cnd-progress-bar {             
         background-color: #FFB400;         
}     
</style>';
        default:
            return '<style>
.cnd-btn-primary { 
         background-color: #000;         
}
.cnd-dtl-bg {  
        background-color: #FFF;        
 }         
.cnd-progress-cnt {             
         background-color: #DDD;         
}         
.cnd-progress-bar {             
         background-color: #000;         
}     
</style>';
    }
}

function render_resource_accrualPeriodicity() {
    global $current_resource_data;
    if (!isset($current_resource_data['accrualPeriodicity']) || empty($current_resource_data['accrualPeriodicity'])) {
        return '';
    }
    
    $accrualPeriodicity = $current_resource_data['accrualPeriodicity'];
    
    // Map IRREG to "Irregolare"
    if ($accrualPeriodicity === 'http://publications.europa.eu/resource/authority/frequency/IRREG') {
        return 'Irregolare';
    }
    
    return '';
}

function render_resource_prefix() {
    global $current_resource_data;
    return isset($current_resource_data['prefix']) ? $current_resource_data['prefix'] : '';
}

function render_resource_projects() {
    global $current_resource_data;
    if (!isset($current_resource_data['projects']) || !is_array($current_resource_data['projects'])) {
        return '';
    }
    
    $links = [];
    foreach ($current_resource_data['projects'] as $project) {
        if (isset($project['iri']) && isset($project['summary'])) {
            $links[] = '<a href="' . esc_url($project['iri']) . '" alt="' . esc_attr($project['summary']) . '">' . esc_html($project['summary']) . '</a>';
        }
    }
    return implode(', ', $links);
}

function render_resource_API_button() {
    global $current_resource_data;
    
    if (!isset($current_resource_data['assetIri']) || empty($current_resource_data['assetIri'])) {
        return '';
    }
    
    // Show button only if type is CONTROLLED_VOCABULARY
    if (!isset($current_resource_data['type']) || $current_resource_data['type'] !== 'CONTROLLED_VOCABULARY') {
        return '';
    }
    
    $assetIri = $current_resource_data['assetIri'];
    
    // URL encode the assetIri for use in query parameter
    $encoded_assetIri = urlencode($assetIri);
    
    // Get the current WordPress site URL
    $site_url = home_url();
    
    // Build the API docs URL
    $api_url = $site_url . '/api-docs/?vocabIri=' . $encoded_assetIri;
    
    // Generate the button HTML
    $button_html = sprintf(
        '<a id="link-208-135" class="ct-link cnd-btn-bx-lgt-ng" href="%s" data-focus-mouse="false"><div id="text_block-209-135" class="ct-text-block">Usa API</div></a>',
        esc_url($api_url)
    );
    
    return $button_html;
}

function render_resource_SPARQL_button() {
    global $current_resource_data;
    
    if (!isset($current_resource_data['assetIri']) || empty($current_resource_data['assetIri'])) {
        return '';
    }
    
    // Show button only if type is NOT SCHEMA
    if (isset($current_resource_data['type']) && $current_resource_data['type'] === 'SCHEMA') {
        return '';
    }
    
    $assetIri = $current_resource_data['assetIri'];
    
    // Get the current WordPress site URL
    $site_url = home_url();
    
    // Build the SPARQL query URL
    $sparql_query = "select distinct ?prop ?value where { <{$assetIri}> ?prop ?value}";
    $encoded_query = urlencode($sparql_query);
    $sparql_url = 'https://schema.gov.it/sparql?qtxt=' . $encoded_query;
    
    // Generate the button HTML
    $button_html = sprintf(
        '<a id="link-208-136" class="ct-link cnd-btn-bx-lgt-ng" href="%s" target="_blank" data-focus-mouse="false"><div id="text_block-209-136" class="ct-text-block">Usa SPARQL</div></a>',
        esc_url($sparql_url)
    );
    
    return $button_html;
}

function render_resource_swagger() {
    global $current_resource_data;

    // Check if data is available
    if ($current_resource_data === null) {
        return '';
    }

    // Check if type is SCHEMA
    if (!isset($current_resource_data['type']) || $current_resource_data['type'] !== 'SCHEMA') {
        return '';
    }

    // Check if there are distributions
    if (!isset($current_resource_data['distributions']) || !is_array($current_resource_data['distributions'])) {
        return '';
    }

    // Search for downloadUrl in distributions
    $yaml_url = '';
    foreach ($current_resource_data['distributions'] as $distribution) {
        if (isset($distribution['downloadUrl']) && !empty($distribution['downloadUrl'])) {
            // Check if it's a YAML file
            if (strpos($distribution['downloadUrl'], '.yaml') !== false || strpos($distribution['downloadUrl'], '.yml') !== false) {
                $yaml_url = $distribution['downloadUrl'];
                break;
            }
        }
    }

    // Se non troviamo un URL YAML, non mostriamo nulla
    if (empty($yaml_url)) {
        return '';
    }

    // Genera un ID univoco per il container
    $container_id = 'swagger-resource-' . uniqid();

    ob_start();
    ?>
    <h2 id="headline-348-135" class="ct-headline h2-ttl-rsc">Editor dello schema API</h2><br />
    <div id="<?php echo $container_id; ?>" class="swagger-container" style="min-height: 400px; border-radius: 8px;">
    </div>

    <link rel="stylesheet" type="text/css" href="<?php echo plugin_dir_url(dirname(__FILE__)) . 'assets/css/swagger-ui.css'; ?>" />
    <script src="<?php echo plugin_dir_url(dirname(__FILE__)) . 'assets/js/swagger-ui-bundle.js'; ?>"></script>

    <!-- CSS per rimuovere lo scroll interno di Swagger UI e rendere responsive -->
    <style>
    .swagger-container {
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: hidden !important;
    }

    .swagger-container .swagger-ui {
        height: auto !important;
        overflow-y: visible !important;
        overflow-x: hidden !important;
        padding: 5px 0 !important;
        width: 100% !important;
        max-width: 100% !important;
    }

    .swagger-container .swagger-ui .wrapper {
        height: auto !important;
        overflow-y: visible !important;
        overflow-x: hidden !important;
        width: 100% !important;
        max-width: 100% !important;
    }

    /* Forza il word-wrap per URL lunghi e contenuti JSON - CSS molto aggressivo */
    .swagger-container .swagger-ui *,
    .swagger-container .swagger-ui *:before,
    .swagger-container .swagger-ui *:after {
        word-wrap: break-word !important;
        word-break: break-all !important;
        overflow-wrap: break-word !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
    }

    /* Specifico per i modelli e esempi - targeting più specifico */
    .swagger-container .swagger-ui .model,
    .swagger-container .swagger-ui .model-box,
    .swagger-container .swagger-ui .model-example,
    .swagger-container .swagger-ui .example,
    .swagger-container .swagger-ui pre,
    .swagger-container .swagger-ui code,
    .swagger-container .swagger-ui span,
    .swagger-container .swagger-ui .model span,
    .swagger-container .swagger-ui .model-box span,
    .swagger-container .swagger-ui .property span {
        word-wrap: break-word !important;
        word-break: break-all !important;
        overflow-wrap: break-word !important;
        white-space: pre-wrap !important;
        max-width: 100% !important;
        overflow-x: hidden !important;
        display: block !important;
        width: 100% !important;
    }

    /* Forza il wrapping anche per elementi inline */
    .swagger-container .swagger-ui span[style*="display: inline"],
    .swagger-container .swagger-ui span[style*="display:inline"] {
        display: block !important;
        width: 100% !important;
        word-wrap: break-word !important;
        word-break: break-all !important;
        overflow-wrap: break-word !important;
    }

    /* Per le tabelle e contenitori */
    .swagger-container .swagger-ui table,
    .swagger-container .swagger-ui .opblock,
    .swagger-container .swagger-ui .opblock-body,
    .swagger-container .swagger-ui .opblock-section {
        max-width: 100% !important;
        overflow-x: hidden !important;
    }

    /* Per i link e URL */
    .swagger-container .swagger-ui a,
    .swagger-container .swagger-ui .url {
        word-wrap: break-word !important;
        word-break: break-all !important;
        overflow-wrap: break-word !important;
        max-width: 100% !important;
    }

    /* Allineamento delle freccette nei model-box */
    .swagger-container .swagger-ui .model-box-control {
        display: flex !important;
        align-items: center !important;
        vertical-align: middle !important;
    }

    .swagger-container .swagger-ui .model-toggle {
        display: inline-block !important;
        vertical-align: middle !important;
        margin-top: 0 !important;
        margin-bottom: 0 !important;
    }

    .swagger-container .swagger-ui .model-title {
        display: inline-block !important;
        vertical-align: middle !important;
        margin-top: 0 !important;
        margin-bottom: 0 !important;
    }

    .swagger-container .swagger-ui .pointer {
        display: inline-block !important;
        vertical-align: middle !important;
    }

    .swagger-container .swagger-ui .model-box {
        display: inline-block !important;
        vertical-align: middle !important;
    }

    /* Nascondi l'URL del file YAML */
    .swagger-ui .info .link {
        display: none !important;
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const containerId = '<?php echo $container_id; ?>';
        const container = document.getElementById(containerId);

        if (container) {
            SwaggerUIBundle({
                url: '<?php echo esc_url($yaml_url); ?>',
                dom_id: '#' + containerId,
                layout: "BaseLayout",
                deepLinking: true,
                showExtensions: true,
                showCommonExtensions: true,
                tryItOutEnabled: true,
                docExpansion: "list",
                defaultModelsExpandDepth: 1,
                defaultModelExpandDepth: 1,
                onComplete: function() {
                    // Rimuovi completamente lo scroll e le limitazioni di altezza
                    setTimeout(function() {
                        // Rimuovi limitazioni dal container principale
                        container.style.maxHeight = 'none';
                        container.style.overflowY = 'visible';
                        container.style.height = 'auto';
                        container.style.overflowX = 'hidden';

                        // Rimuovi limitazioni dal contenuto Swagger
                        const swaggerContent = container.querySelector('.swagger-ui');
                        if (swaggerContent) {
                            swaggerContent.style.height = 'auto';
                            swaggerContent.style.overflowY = 'visible';
                            swaggerContent.style.overflowX = 'hidden';
                            swaggerContent.style.maxHeight = 'none';
                            swaggerContent.style.width = '100%';
                            swaggerContent.style.maxWidth = '100%';
                        }

                        // Rimuovi limitazioni dal wrapper
                        const wrapper = container.querySelector('.swagger-ui .wrapper');
                        if (wrapper) {
                            wrapper.style.height = 'auto';
                            wrapper.style.overflowY = 'visible';
                            wrapper.style.overflowX = 'hidden';
                            wrapper.style.maxHeight = 'none';
                            wrapper.style.width = '100%';
                            wrapper.style.maxWidth = '100%';
                        }

                        // Forza il word-wrap su tutti gli elementi con testo lungo
                        const allElements = container.querySelectorAll('*');
                        allElements.forEach(function(element) {
                            element.style.wordWrap = 'break-word';
                            element.style.wordBreak = 'break-all';
                            element.style.overflowWrap = 'break-word';
                            element.style.maxWidth = '100%';
                            element.style.boxSizing = 'border-box';
                            
                            // If it's a span with long text, force it to be block
                            // Ma NON per le freccette e elementi di controllo
                            if (element.tagName === 'SPAN' && element.textContent && element.textContent.length > 50) {
                                // Escludi le freccette e elementi di controllo
                                if (!element.classList.contains('model-toggle') && 
                                    !element.classList.contains('model-title') && 
                                    !element.classList.contains('pointer') &&
                                    !element.classList.contains('model-box')) {
                                    element.style.display = 'block';
                                    element.style.width = '100%';
                                    element.style.whiteSpace = 'pre-wrap';
                                }
                            }
                        });

                        // Allineamento specifico per i model-box-control
                        const modelControls = container.querySelectorAll('.model-box-control');
                        modelControls.forEach(function(control) {
                            control.style.display = 'flex';
                            control.style.alignItems = 'center';
                            control.style.verticalAlign = 'middle';
                        });

                        // Allineamento per le freccette
                        const modelToggles = container.querySelectorAll('.model-toggle');
                        modelToggles.forEach(function(toggle) {
                            toggle.style.display = 'inline-block';
                            toggle.style.verticalAlign = 'middle';
                            toggle.style.marginTop = '0';
                            toggle.style.marginBottom = '0';
                        });
                    }, 1000);
                }
            });
        }
    });
    </script>
    <?php
    return ob_get_clean();
}

function render_resource_schemaeditor() {
    global $current_resource_data;

    // Check if data is available
    if ($current_resource_data === null) {
        return '';
    }

    // Check if type is SCHEMA
    if (!isset($current_resource_data['type']) || $current_resource_data['type'] !== 'SCHEMA') {
        return '';
    }

    // Check if there are distributions
    if (!isset($current_resource_data['distributions']) || !is_array($current_resource_data['distributions'])) {
        return '';
    }

    // Search for downloadUrl in distributions
    $yaml_url = '';
    foreach ($current_resource_data['distributions'] as $distribution) {
        if (isset($distribution['downloadUrl']) && !empty($distribution['downloadUrl'])) {
            // Check if it's a YAML file
            if (strpos($distribution['downloadUrl'], '.yaml') !== false || strpos($distribution['downloadUrl'], '.yml') !== false) {
                $yaml_url = $distribution['downloadUrl'];
                break;
            }
        }
    }

    // Se non troviamo un URL YAML, non mostriamo nulla
    if (empty($yaml_url)) {
        return '';
    }

    // Use the schema-editor shortcode with the found YAML URL
    return do_shortcode('[schema-editor url="' . esc_url($yaml_url) . '" viewer="true"]');
}