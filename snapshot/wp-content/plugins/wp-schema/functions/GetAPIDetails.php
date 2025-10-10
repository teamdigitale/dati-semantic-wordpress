<?php 

add_shortcode('resource-get-API-details', 'render_api_details');
add_shortcode('resource-get-API-array', 'render_api_array');
add_shortcode('resource-get-API-debug', 'render_api_debug');

// Global variable to store the current API data
global $current_api_data;
$current_api_data = null;

function render_api_details() {
    global $current_api_data;
    
    if (!isset($_GET['vocabIri']) || empty($_GET['vocabIri'])) {
        return '';
    }

    $vocabIri = $_GET['vocabIri'];
    $encoded_iri = urlencode($vocabIri);
    $cache_key = 'api_' . md5($vocabIri);
    
    // Try to get from cache first
    $data = get_resource_transient($cache_key);
    $cache_hit = ($data !== false);
    
    if (!$cache_hit) {
        // Cache miss - call API
        if (!defined('WP_SCHEMA_API_BASE_URL')) {
            return '';
        }
        
        $api_url = WP_SCHEMA_API_BASE_URL . 'semantic-assets/by-iri?iri=' . $encoded_iri;
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
    $current_api_data = $data;
    
    // Clean expired cache entries
    purge_expired_resource_transients();
    
    return '';
}

function render_api_array() {
    global $current_api_data;
    
    if ($current_api_data === null) {
        return '<div class="error">Nessun dato API disponibile. Usa prima [resource-get-API-details].</div>';
    }
    
    ob_start();
    
    // Formattazione HTML per le istruzioni API
    $title = isset($current_api_data['title']) ? $current_api_data['title'] : 'Vocabolario Controllato';
    $agency_id = isset($current_api_data['agencyId']) ? $current_api_data['agencyId'] : '';
    $key_concept = isset($current_api_data['keyConcept']) ? $current_api_data['keyConcept'] : '';
    $endpoint_url = isset($current_api_data['endpointUrl']) ? $current_api_data['endpointUrl'] : '';
    
    // Tronca il titolo a 15 caratteri e aggiungi i puntini
    $truncated_title = strlen($title) > 15 ? substr($title, 0, 15) . '...' : $title;
    $truncated_api_title = strlen($title) > 15 ? substr($title, 0, 15) . '...' : $title;
    
    // Header con breadcrumb e titolo
    echo '<div class="ct-div-block">';
    echo '<div class="ct-div-block">';
    
    // Breadcrumb
    echo '<div class="ct-code-block">';
    echo '<nav aria-label="breadcrumb">';
    echo '<ol class="breadcrumb">';
    echo '<li class="breadcrumb-item"><a href="' . home_url() . '" aria-label="Vai alla home">Homepage</a></li>';
    echo '<li class="breadcrumb-item"><a href="' . home_url('/search/') . '" aria-label="catalogo">Catalogo</a></li>';
    
    // Link alla pagina della risorsa
    if (isset($_GET['vocabIri']) && !empty($_GET['vocabIri'])) {
        $vocabIri = $_GET['vocabIri'];
        $encoded_iri = urlencode($vocabIri);
        $resource_url = home_url('/semantic-assets/details/?uri=' . $encoded_iri);
        echo '<li class="breadcrumb-item"><a href="' . esc_url($resource_url) . '" aria-label="Vai alla risorsa">' . esc_html($truncated_title) . '</a></li>';
    } else {
        echo '<li class="breadcrumb-item">' . esc_html($truncated_title) . '</li>';
    }
    
    echo '<li class="breadcrumb-item active" aria-current="page">Come utilizzare le API per ' . esc_html($truncated_api_title) . '</li>';
    echo '</ol>';
    echo '</nav>';
    echo '</div>';
    
    echo '</div>';
    echo '<div class="ct-div-block">';
    
    // Titolo principale
    echo '<h1 class="ct-headline h1-ttl">Come utilizzare le API per ' . esc_html($title) . '</h1>';
    
    echo '</div>';
    echo '</div>';
    
    if (!empty($agency_id) && !empty($key_concept)) {
        echo '<p>Nell\'utilizzo degli endpoint per le voci del Vocabolario Controllato, utilizzare:</p>';
        echo '<ul>';
        echo '<li>il valore <code>' . esc_html($agency_id) . '</code> per <code>agency_id</code> ;</li>';
        echo '<li>il valore <code>' . esc_html($key_concept) . '</code> per <code>key_concept</code> ;</li>';
        echo '</ul>';
        
        if (!empty($endpoint_url)) {
            echo '<p style="margin-bottom: 50px;">L\'endpoint avrà quindi la forma: <code>' . esc_html($endpoint_url) . '</code>.</p>';
        }
    }
    
    // Dati API disponibili per altri shortcode
    return ob_get_clean();
}

function render_api_debug() {
    global $current_api_data;
    
    ob_start();
    echo '<h3>Debug Informazioni API:</h3>';
    echo '<div class="debug-info">';
    
    // Check if vocabIri parameter exists
    if (isset($_GET['vocabIri']) && !empty($_GET['vocabIri'])) {
        echo '<p><strong>✅ vocabIri presente:</strong> ' . htmlspecialchars($_GET['vocabIri']) . '</p>';
        
        // Check API configuration
        if (defined('WP_SCHEMA_API_BASE_URL')) {
            echo '<p><strong>✅ Configurazione API:</strong> ' . htmlspecialchars(WP_SCHEMA_API_BASE_URL) . '</p>';
        } else {
            echo '<p><strong>❌ Configurazione API:</strong> Non definita</p>';
        }
        
        // Check cache status
        $vocabIri = $_GET['vocabIri'];
        $cache_key = 'api_' . md5($vocabIri);
        $cached_data = get_resource_transient($cache_key);
        
        if ($cached_data !== false) {
            echo '<p><strong>✅ Cache:</strong> Dati presenti in cache</p>';
        } else {
            echo '<p><strong>❌ Cache:</strong> Nessun dato in cache</p>';
        }
        
        // Check if data is loaded
        if ($current_api_data !== null) {
            echo '<p><strong>✅ Dati caricati:</strong> Sì</p>';
            echo '<p><strong>📊 Campi disponibili:</strong> ' . count($current_api_data) . '</p>';
            
            // Show available fields
            echo '<p><strong>📋 Campi principali:</strong></p>';
            echo '<ul>';
            $main_fields = ['title', 'description', 'type', 'modifiedOn', 'issuedOn', 'rightsHolder', 'themes', 'assetIri'];
            foreach ($main_fields as $field) {
                if (isset($current_api_data[$field])) {
                    echo '<li><strong>' . $field . ':</strong> Presente</li>';
                } else {
                    echo '<li><strong>' . $field . ':</strong> Non presente</li>';
                }
            }
            echo '</ul>';
        } else {
            echo '<p><strong>❌ Dati caricati:</strong> No - Usa [resource-get-API-details] prima</p>';
        }
        
    } else {
        echo '<p><strong>❌ vocabIri:</strong> Parametro mancante</p>';
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