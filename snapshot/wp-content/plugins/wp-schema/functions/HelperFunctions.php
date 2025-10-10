<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

function get_assets($q, $type_params, $rightsHolder_params, $theme_params, $sortBy, $direction, $offset, $limit, $columns) {
    // Build API URL
    $api_url = WP_SCHEMA_API_BASE_URL .'semantic-assets';

    // Base query parameters
    $query_params = array(
        'sortBy'    => $sortBy,
        'direction' => $direction,
        'offset'    => $offset,
        'limit'     => $limit,
        'q'         => $q,
    );

    // Build query parts array
    $query_parts = array();
    foreach ($query_params as $key => $value) {
        if ($value !== '') {
            $query_parts[] = urlencode($key) . '=' . urlencode($value);
        }
    }

    // Add multiple value parameters
    if (!empty($type_params)) {
        foreach ($type_params as $value) {
            $query_parts[] = urlencode('type') . '=' . urlencode($value);
        }
    }
    if (!empty($rightsHolder_params)) {
        foreach ($rightsHolder_params as $value) {
            $query_parts[] = urlencode('rightsHolder') . '=' . urlencode($value);
        }
    }
    if (!empty($theme_params)) {
        foreach ($theme_params as $value) {
            $query_parts[] = urlencode('theme') . '=' . urlencode($value);
        }
    }

    // Build query string
    $query_string = implode('&', $query_parts);

    // Build full API URL
    $api_url_with_params = $api_url . '?' . $query_string;

    // Call the API
    $response = wp_remote_get($api_url_with_params);

    if (is_wp_error($response)) {
        // Handle error
        $error_message = $response->get_error_message();
        return array(
            'totalResults'         => 0,
            'items_html'           => "<p>Errore nella chiamata all'API: $error_message</p>",
            'load_more_button_html' => ''
        );
    } else {
        // Decode JSON response
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['data']) && is_array($data['data'])) {
            // Get total number of results
            $totalResults = isset($data['totalCount']) ? intval($data['totalCount']) : count($data['data']);

            $items_html = '';

            // Build HTML for each result
            foreach ($data['data'] as $item) {
                $items_html .= '<div class="col">';
                $items_html .= ' <div class="cnd-srch-res-box">';
                $items_html .= '  <div class="cnd-src-res-item">';
                $items_html .= '    <div class="cnd-cr-hd">';
                $items_html .= '      <div class="cnd-crd-srch-rs-top">';

                // Button with class and text based on type
                $type = isset($item['type']) ? $item['type'] : '';
                $type_text = '';
                $type_class = '';

                if ($type == 'ONTOLOGY') {
                    $type_text = 'Ontologia';
                    $type_class = 'btn-ontology';
                } elseif ($type == 'CONTROLLED_VOCABULARY') {
                    $type_text = 'Vocabolario controllato';
                    $type_class = 'btn-vocabulary';
                } elseif ($type == 'SCHEMA') {
                    $type_text = 'Schema';
                    $type_class = 'btn-schema';
                }

                if ($type_text != '') {
                    $items_html .= '<button class="btn ' . esc_attr($type_class) . '">' . esc_html($type_text) . '</button>';
                }

                // Show status chip if available
                if (isset($item['status']) && is_array($item['status']) && !empty($item['status'])) {
                    $status = $item['status'][0];
                    $items_html .= '<div class="item-status">';
                    $items_html .= get_status_chip($status);
                    $items_html .= '</div>';
                }
                
                $items_html .= '    </div>'; // close div.space-between

                // Title in h3 tag
                $title = isset($item['title']) ? $item['title'] : '';
                $items_html .= '<h3 class="cnd-crd-ttl">' . esc_html($title) . '</h3>';

                // Description limited to two lines
                $description = isset($item['description']) ? $item['description'] : '';
                $items_html .= '<p class="cnd-description cnd-crd-dsc">' . esc_html($description) . '</p>';

                $items_html .= '  </div>'; // close div.cnd-cr-hd

                // RightsHolder summary with link to iri
                if (isset($item['rightsHolder']) && is_array($item['rightsHolder'])) {
                    $rightsHolder_summary = isset($item['rightsHolder']['summary']) ? $item['rightsHolder']['summary'] : '';
                    $rightsHolder_iri = isset($item['rightsHolder']['iri']) ? $item['rightsHolder']['iri'] : '';
                    $plugin_url = plugin_dir_url(dirname(__FILE__));
                    $items_html .= '<div class="cnd-cr-body"><div class="cnd-crd-lst"><img src="'.$plugin_url.'assets/images/User-Circle.svg" class="cnd-ico-ct" alt="Icona Titolari"><div>' . esc_html($rightsHolder_summary) . '</div></div>';
                }

                // Themes chips
                if (isset($item['themes']) && is_array($item['themes']) && count($item['themes']) > 0) {
                    $items_html .= '<div class="cnd-themes-list"><div class="cnd-themes-list-pdv"><img src="'.$plugin_url.'assets/images/Folder.svg" class="cnd-ico-ct" alt="Icona Categorie">';
                
                    // Print first theme name
                    $first_theme = $item['themes'][0];
                    $first_theme_name = ThemeDataHelper::getLabelItByUrl($first_theme);
                    $items_html .= esc_html($first_theme_name);
                    
                    $items_html .= '</div>';
                    // If there are more themes, add chip with "+n"
                    $remaining_count = count($item['themes']) - 1;
                    if ($remaining_count > 0) {
                        $items_html .= '<span class="chip-small">+' . $remaining_count . '</span>';
                    }
                
                    $items_html .= '</div></div>';
                }

                $items_html .= '  <div class="space-between">';

                // "Esplora" button with correct URL
                $assetIri = isset($item['assetIri']) ? $item['assetIri'] : '';
                $exploreUrl = home_url('/semantic-assets/details/?uri=' . rawurlencode($assetIri));
                $items_html .= '<a href="' . esc_url($exploreUrl) . '" class="btn btn-main">Esplora</a>';

                $items_html .= '  </div>'; // close div.space-between

                $items_html .= '  </div>'; // close div.search-result-item
                $items_html .= '<div class="cnd-src-res-date">'; // open div.cnd-src-res-date
                    $items_html .= '<div class="cnd-pre-res-date"><img src="'.$plugin_url.'assets/images/Date.svg" class="cnd-ico-ct" alt="Icona Categorie">  A catalogo dal</div>';

                    // issuedOn date in dd/mm/yy format
                    $issuedOn = isset($item['issuedOn']) ? $item['issuedOn'] : '';
                    $formattedDate = '';
                    if ($issuedOn != '') {
                        $date = DateTime::createFromFormat('Y-m-d', $issuedOn);
                        if ($date) {
                            $formattedDate = $date->format('d/m/y');
                        } else {
                            $formattedDate = esc_html($issuedOn);
                        }
                        $items_html .= '<span class="cnd-date">' . esc_html($formattedDate) . '</span>';
                    }

                $items_html .= '</div>'; // close div.cnd-src-res-date
                $items_html .= '</div>'; // close div.search-result-date
                $items_html .= '</div>'; // close div.col
            }

            // Add "Show more results" button if there are more results
            $load_more_button_html = '';
            if (($offset + $limit) < $totalResults) {
                $load_more_button_html .= '<div class="load-more-div"><div class="load-more-container">';
                $load_more_button_html .= '<button class="btn btn-primary" id="loadMoreButton" onclick="loadMoreResults()">Mostra più risultati</button>';
                $load_more_button_html .= '</div></div>';
            }

            return array(
                'totalResults'         => $totalResults,
                'items_html'           => $items_html,
                'load_more_button_html' => $load_more_button_html
            );
        } else {
            // If there are no results
            return array(
                'totalResults'         => 0,
                'items_html'           => '<div class="cnd-no-results">Nessun risultato trovato.</div>',
                'load_more_button_html' => ''
            );
        }
    }
}