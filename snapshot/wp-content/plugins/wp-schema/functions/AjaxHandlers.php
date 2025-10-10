<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

add_action('wp_ajax_load_more_results', 'load_more_results');
add_action('wp_ajax_nopriv_load_more_results', 'load_more_results');

function load_more_results() {
    // Ensure the request is AJAX
    if ( ! defined('DOING_AJAX') || ! DOING_AJAX ) {
        wp_die();
    }

    // Get parameters from POST request
    $q = isset($_POST['q']) ? sanitize_text_field($_POST['q']) : '';
    $type_params      = isset($_POST['type']) ? array_map('sanitize_text_field', $_POST['type']) : array();
    $rightsHolder_params = isset($_POST['rightsHolder']) ? array_map('sanitize_text_field', $_POST['rightsHolder']) : array();
    $theme_params     = isset($_POST['theme']) ? array_map('sanitize_text_field', $_POST['theme']) : array();
    $sortBy           = isset($_POST['sortBy']) ? sanitize_text_field($_POST['sortBy']) : 'TITLE';
    $direction        = isset($_POST['direction']) ? sanitize_text_field($_POST['direction']) : 'ASC';
    $columns          = isset($_POST['columns']) ? intval($_POST['columns']) : 3;
    $offset           = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
    $limit            = isset($_POST['limit']) ? intval($_POST['limit']) : ($columns == 2 ? 6 : 9);

    // Get new results using get_assets()
    $results = get_assets($q, $type_params, $rightsHolder_params, $theme_params, $sortBy, $direction, $offset, $limit, $columns);

    if (empty($results['items_html'])) {
        $results['items_html'] = '<div class="search-noresults">
                                    <img src="' . plugin_dir_url(dirname(__FILE__)) . 'assets/images/no-result.svg" alt="No results"><br>
                                    <h3 class="h3-nr">Nessun risultato trovato</h3>
                                    <p class="txt-nr">La ricerca non ha prodotto nessun risultato, modifica i filtri o prova un\'altra chiave di ricerca.</p>
                                  </div>';
        $results['load_more_button_html'] = '';
    }

    // Return HTML and total results as JSON
    wp_send_json_success(array(
        'items_html'           => $results['items_html'],
        'load_more_button_html' => $results['load_more_button_html'],
        'totalResults'         => $results['totalResults']
    ));
}

add_action('wp_ajax_load_search_results', 'load_search_results');
add_action('wp_ajax_nopriv_load_search_results', 'load_search_results');

function load_search_results() {
    // Get parameters from POST request
    $q = isset($_POST['q']) ? sanitize_text_field($_POST['q']) : '';
    $type_params = isset($_POST['type']) ? array_map('sanitize_text_field', $_POST['type']) : [];
    $rightsHolder_params = isset($_POST['rightsHolder']) ? array_map('sanitize_text_field', $_POST['rightsHolder']) : [];
    $theme_params = isset($_POST['theme']) ? array_map('sanitize_text_field', $_POST['theme']) : [];
    $sortBy = isset($_POST['sortBy']) ? sanitize_text_field($_POST['sortBy']) : 'TITLE';
    $direction = isset($_POST['direction']) ? sanitize_text_field($_POST['direction']) : 'ASC';
    $columns = isset($_POST['columns']) ? intval($_POST['columns']) : 3;

    // Debug: log received parameters
    error_log('Received parameters: ' . print_r($_POST, true));

    // Get results
    $results = get_assets($q, $type_params, $rightsHolder_params, $theme_params, $sortBy, $direction, 0, 12, $columns);

    if (empty($results['items_html'])) {
        $results['items_html'] = '<div class="search-noresults">
                                    <img src="' . plugin_dir_url(dirname(__FILE__)) . 'assets/images/no-result.svg" alt="No results"><br>
                                    <h3 class="h3-nr">Nessun risultato trovato</h3>
                                    <p class="txt-nr">La ricerca non ha prodotto nessun risultato, modifica i filtri o prova un\'altra chiave di ricerca.</p>
                                  </div>';
        $results['load_more_button_html'] = '';
    }

    // Debug: log generated results
    error_log('Generated results: ' . print_r($results, true));

    wp_send_json_success([
        'items_html' => $results['items_html'],
        'totalResults' => $results['totalResults'],
        'load_more_button_html' => $results['load_more_button_html']
    ]);
}