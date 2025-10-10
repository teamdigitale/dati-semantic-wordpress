<?php
/**
 * Plugin Name: Matomo Stats JSON API
 * Description: Provides a JSON endpoint with aggregated data from Matomo.
 * Version: 1.0
 * Author: Il Tuo Nome
 */

// Register custom endpoint: /wp-json/matomo/v1/stats
add_action('rest_api_init', function () {
    register_rest_route('matomo/v1', '/stats', [
        'methods' => 'GET',
        'callback' => 'get_matomo_stats_json',
        'permission_callback' => '__return_true'
    ]);
});

/**
 * Executes a request to the Matomo API
 *
 * @param string $method The API method to call
 * @param array $params Additional request parameters
 * @param string $segment Additional segment for the request
 * @return array|WP_Error The response data or an error
 */
function matomo_fetch($method, $params = [], $segment = '') {
    $common = [
        'idSite' => MATOMO_SITE_ID,
        'period' => 'day',
        'date' => 'today',
        'format' => 'json',
        'token_auth' => MATOMO_TOKEN,
        'module' => 'API',
        'method' => $method
    ];

    // Add segment if provided
    if (!empty($segment)) {
        $common['segment'] = $segment;
    }

    $postData = array_merge($common, $params);
    
    $ch = curl_init(MATOMO_API_URL);
    if ($ch === false) {
        error_log('Failed to initialize cURL');
        return new WP_Error('matomo_curl_init_error', 'Failed to initialize cURL');
    }

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($postData),
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_USERPWD => MATOMO_USER . ':' . MATOMO_PASS,
        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_FAILONERROR => true
    ]);
    
    $result = curl_exec($ch);
    $error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($error) {
        error_log('Matomo API Error: ' . $error);
        return new WP_Error('matomo_api_error', $error);
    }
    
    if ($http_code !== 200) {
        error_log('Matomo API HTTP Error: ' . $http_code . ' - ' . $result);
        return new WP_Error('matomo_api_http_error', "HTTP $http_code: $result");
    }

    $decoded_result = json_decode($result, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('Matomo API JSON decode error: ' . json_last_error_msg());
        return new WP_Error('matomo_json_error', 'Failed to decode API response');
    }
    
    return $decoded_result;
}

/**
 * Calls Matomo and builds the JSON response
 *
 * @return WP_REST_Response|WP_Error
 */
function get_matomo_stats_json(WP_REST_Request $request = null) {
    $response = [];
    $errors = [];

    try {
        // Period parameters
        $year = $request ? $request->get_param('year') : null;
        $month = $request ? $request->get_param('month') : null;
        $time_range = $request ? $request->get_param('time_range') : 'all';

        // Valorizza periodo/data in base al filtro
        switch($time_range) {
            case 'all':
                $period = 'month';
                $date = 'last1000';
                break;
            case 'year':
                $period = 'month';
                $date = 'last12';
                break;
            case '6months':
                $period = 'month';
                $date = 'last6';
                break;
        }

        // Validate time range
        $valid_time_ranges = ['all', 'year', '6months'];
        if (!in_array($time_range, $valid_time_ranges)) {
            $time_range = 'all';
        }
        
        // Set historical data parameters based on time range
        $historical_period = 'month';
        $historical_date = 'last12'; // default
        
        switch($time_range) {
            case 'all':
                $historical_date = 'last1000'; // Matomo's way to get all available data
                break;
            case 'year':
                $historical_date = 'last12';
                break;
            case '6months':
                $historical_date = 'last6';
                break;
        }

        // Define segments for internal/external visitors
        $external_segment = 'visitIp<10.18.0.1,visitIp>10.18.255.254';

        // Fetch historical data
        $historical = matomo_fetch('VisitsSummary.get', [
            'period' => $historical_period,
            'date' => $historical_date
        ], $external_segment);
        
        if (is_wp_error($historical)) {
            $errors[] = 'Failed to fetch historical data: ' . $historical->get_error_message();
        } else {
            $response['historical'] = $historical;
        }

        // Data collection with error handling
        $summary = matomo_fetch('VisitsSummary.get', ['period' => $period, 'date' => $date], $external_segment);
        if (is_wp_error($summary)) {
            $errors[] = 'Failed to fetch summary data: ' . $summary->get_error_message();
        } else {
            $response['summary'] = $summary;
        }

        $pages = matomo_fetch('Actions.getPageUrls', [
            'period' => $period,
            'date' => $date,
            'flat' => 1
        ], $external_segment);
        if (is_wp_error($pages)) {
            $errors[] = 'Failed to fetch pages data: ' . $pages->get_error_message();
        } else {
            $response['pages'] = $pages;
        }

        $referrers = matomo_fetch('Referrers.getReferrerType', ['period' => $period, 'date' => $date], $external_segment);
        if (is_wp_error($referrers)) {
            $errors[] = 'Failed to fetch referrers data: ' . $referrers->get_error_message();
        } else {
            $response['referrers'] = $referrers;
        }

        if (!empty($errors)) {
            return new WP_Error('matomo_api_error', implode(', ', $errors), ['status' => 500]);
        }

        return rest_ensure_response($response);
    } catch (Exception $e) {
        error_log('Matomo Stats Error: ' . $e->getMessage());
        return new WP_Error('matomo_internal_error', 'An internal error occurred', ['status' => 500]);
    }
}

// Register shortcode
add_shortcode('MATOMO-VISUALIZZAZIONI', 'display_matomo_stats');

function display_matomo_stats() {
    try {
        // Get parameters from query string with validation
        $selected_time_range = isset($_GET['time_range']) ? sanitize_text_field($_GET['time_range']) : 'all';

        // Validate time range
        $valid_time_ranges = ['all', 'year', '6months'];
        if (!in_array($selected_time_range, $valid_time_ranges)) {
            $selected_time_range = 'all';
        }

        // Generate HTML for selectors and AJAX container
        $html = '<div class="matomo-stats-container">';
        $html .= '<div class="matomo-filters">';
        
        // Titolo sopra il grafico
        $html .= '<div style="font-size:20px;font-weight:600;margin-bottom:10px;">Andamento delle visualizzazioni</div>';
        // Select periodo grafico in linea, subito dopo il titolo
        $html .= '<div style="display:flex;gap:16px;align-items:center;margin-bottom:10px;">';
        $html .= '<div class="period-selector">';
        $html .= '<select id="time-range-select">';
        $time_ranges = [
            'all' => 'Dall\'inizio',
            'year' => 'Ultimo anno',
            '6months' => 'Ultimi 6 mesi'
        ];
        foreach ($time_ranges as $value => $label) {
            $selected = ($value == $selected_time_range) ? 'selected' : '';
            $html .= "<option value='" . esc_attr($value) . "' $selected>" . esc_html($label) . "</option>";
        }
        $html .= '</select>';
        $html .= '</div>';
        $html .= '<button id="filter-button" class="custom-multiselect-filter-btn">Filtra</button>';
        $html .= '</div>';

        // Add Chart.js library
        $html .= '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';
        
        // Add canvas for the chart
        $html .= '<div class="matomo-chart-container" style="margin: 20px 0; height: 400px;">';
        $html .= '<canvas id="matomoChart"></canvas>';
        $html .= '</div>';

        // AJAX container for chart
        $html .= '<div id="matomo-ajax-content"></div>';

        // Titolo e filtro risorse
        $html .= '<div class="matomo-resources-filter" style="margin-top:40px;">';
        $html .= '<div style="font-size:20px;font-weight:600;margin-bottom:10px;">Le 5 risorse più visitate</div>';
        $html .= '<div style="display:flex;gap:16px;align-items:center;">';
        $html .= '<div class="period-selector">';
        $html .= '<select id="resource-time-range-select">';
        foreach ($time_ranges as $value => $label) {
            $html .= "<option value='" . esc_attr($value) . "'" . ($value == $selected_time_range ? ' selected' : '') . ">" . esc_html($label) . "</option>";
        }
        $html .= '</select>';
        $html .= '</div>';
        $html .= '<button id="resource-filter-button" class="custom-multiselect-filter-btn">Filtra</button>';
        $html .= '</div>';
        $html .= '</div>';

        // AJAX container for resource list
        $html .= '<div id="matomo-resources-content"></div>';

        // CSS
        $html .= '<style>
            .matomo-stats-container { font-family: Arial, sans-serif; max-width: 1200px; margin: 20px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
            .matomo-filters { margin-bottom: 20px; }
            .matomo-filters select { margin-right: 10px; padding: 5px; }
            .period-selector { display: inline-block; margin-right: 16px; }
            .period-selector select {
                border: 1px solid #C5C7CD;
                border-radius: 6px;
                padding: 8px 32px 8px 12px;
                background: #fff;
                font-size: 15px;
                color: #222;
                font-weight: 500;
                cursor: pointer;
                min-width: 160px;
            }
            .period-selector select:focus {
                outline: 3px solid #0066cc;
                outline-offset: 2px;
            }
            @media (max-width: 600px) {
                .period-selector {
                    margin-right: 0 !important;
                    margin-bottom: 10px;
                    width: 100%;
                }
                .period-selector select {
                    width: 100%;
                }
            }
            .custom-multiselect-filter-btn {
                background: #fff;
                border: 1px solid #0043E3;
                color: #0043E3;
                border-radius: 6px;
                padding: 6px 18px;
                font-size: 15px;
                font-weight: 600;
                cursor: pointer;
                margin-left: 12px;
                transition: background 0.2s, color 0.2s;
            }
            .custom-multiselect-filter-btn:hover {
                background: #0043E3;
                color: #fff;
            }
            .custom-multiselect-filter-btn:focus-visible {
                outline: 2px solid #0043E3;
                outline-offset: 2px;
            }
            .matomo-row { display: flex; align-items: center; margin-bottom: 15px; padding: 10px; border-bottom: 1px solid #eee; }
            .matomo-url { flex: 1; margin-right: 20px; font-size: 14px; color: #333; word-break: break-all; }
            .matomo-graph { margin-top: 15px; }
            .matomo-dots {
                flex: 2;
                min-width: 100px;
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 1px;
                max-width: 600px;
            }
            .dot {
                display: inline-block;
                width: 7px;
                height: 7px;
                background: #0066cc;
                border-radius: 50%;
                margin: 1px;
            }
            .matomo-count { width: 80px; text-align: right; font-weight: bold; color: #0066cc; font-size: 14px; }
            .matomo-legend { margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; font-size: 14px; color: #666; }
            .legend-item { display: flex; align-items: center; }
            .legend-item .dot { width: 14px; height: 14px; margin-right: 10px; }
            .error { color: #dc3545; padding: 10px; margin: 10px 0; border: 1px solid #dc3545; border-radius: 4px; }
        </style>';

        $html .= '</div>';

        // Print JS outside PHP string
        ?>
        <script>
        let matomoChart = null;

        function renderMatomoContent(data) {
            try {
                let html = "";
                
                // Render historical chart
                if (data.historical && typeof data.historical === 'object') {
                    // Trasforma l'oggetto in array ordinato per data
                    const historicalArray = Object.entries(data.historical)
                        .filter(([_, v]) => v && typeof v === 'object' && Object.keys(v).length > 0) // solo mesi con dati
                        .map(([k, v]) => ({ label: k, nb_visits: v.nb_visits || 0 }))
                        .sort((a, b) => a.label.localeCompare(b.label));

                    if (historicalArray.length > 0) {
                        const ctx = document.getElementById('matomoChart').getContext('2d');
                        if (matomoChart) matomoChart.destroy();

                        const labels = historicalArray.map(item => item.label);
                        const visits = historicalArray.map(item => item.nb_visits);

                        matomoChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Visualizzazioni',
                                    data: visits,
                                    borderColor: '#0066cc',
                                    backgroundColor: 'rgba(0, 102, 204, 0.1)',
                                    tension: 0.4,
                                    fill: true
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    title: {
                                        display: true,
                                        text: 'Andamento delle visualizzazioni'
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        title: {
                                            display: true,
                                            text: 'Numero di visualizzazioni'
                                        }
                                    },
                                    x: {
                                        title: {
                                            display: true,
                                            text: 'Mese'
                                        }
                                    }
                                }
                            }
                        });
                    } else {
                        console.warn('No historical data for chart');
                    }
                } else {
                    console.warn('No historical data for chart'); // DEBUG
                }

                document.getElementById("matomo-ajax-content").innerHTML = html;
            } catch (error) {
                console.error('Error rendering Matomo content:', error);
                document.getElementById("matomo-ajax-content").innerHTML = '<div class="error">Si è verificato un errore nel caricamento dei dati.</div>';
            }
        }

        function renderMatomoResources(data) {
            try {
                // Unify all page arrays into a single array if data.pages is an object
                let allPages = [];
                if (Array.isArray(data.pages)) {
                    allPages = data.pages;
                } else if (typeof data.pages === 'object' && data.pages !== null) {
                    allPages = Object.values(data.pages).flat();
                }
                let html = '';
                html += `<div class=\"matomo-graph\">`;
                // Raggruppa per URL e somma le visite
                const urlMap = {};
                allPages.forEach(page => {
                    const url = page.url ? page.url : page.label;
                    if (!url.startsWith('https://schema.gov.it/semantic-assets/details?')) return;
                    if (!urlMap[url]) {
                        urlMap[url] = { ...page, nb_visits: parseInt(page.nb_visits) || 0 };
                    } else {
                        urlMap[url].nb_visits += parseInt(page.nb_visits) || 0;
                    }
                });
                const groupedPages = Object.values(urlMap)
                    .sort((a, b) => (b.nb_visits || 0) - (a.nb_visits || 0))
                    .slice(0, 5);
                groupedPages.forEach(page => {
                    const url = page.url ? page.url : page.label;
                    const visits = parseInt(page.nb_visits) || 0;
                    html += `<div class=\"matomo-row\">`;
                    html += `<div class=\"matomo-url\"><a href=\"${url}\" target=\"_blank\" rel=\"noopener\">${url}</a></div>`;
                    html += `<div class=\"matomo-count\">${visits}</div>`;
                    html += `</div>`;
                });
                html += `</div>`;
                document.getElementById("matomo-resources-content").innerHTML = html;
            } catch (error) {
                document.getElementById("matomo-resources-content").innerHTML = '<div class="error">Si è verificato un errore nel caricamento delle risorse.</div>';
            }
        }

        async function fetchMatomoStats(timeRange) {
            try {
                const url = `/wp-json/matomo/v1/stats?time_range=${encodeURIComponent(timeRange)}`;
                document.getElementById("matomo-ajax-content").innerHTML = '<div>Caricamento...</div>';
                
                // Aggiungi timeout e abort controller
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 30000); // 30 secondi timeout
                
                const res = await fetch(url, {
                    signal: controller.signal,
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                
                clearTimeout(timeoutId);
                
                if (!res.ok) {
                    throw new Error(`HTTP error! status: ${res.status}`);
                }
                const data = await res.json();
                renderMatomoContent(data);
            } catch (error) {
                if (error.name === 'AbortError') {
                    document.getElementById("matomo-ajax-content").innerHTML = '<div class="error">Timeout: La richiesta ha impiegato troppo tempo. Riprova.</div>';
                } else {
                    document.getElementById("matomo-ajax-content").innerHTML = '<div class="error">Si è verificato un errore nel recupero dei dati.</div>';
                }
            }
        }

        async function fetchMatomoResources(timeRange) {
            try {
                const url = `/wp-json/matomo/v1/stats?time_range=${encodeURIComponent(timeRange)}`;
                document.getElementById("matomo-resources-content").innerHTML = '<div>Caricamento risorse...</div>';
                
                // Aggiungi timeout e abort controller
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 30000); // 30 secondi timeout
                
                const res = await fetch(url, {
                    signal: controller.signal,
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                
                clearTimeout(timeoutId);
                
                if (!res.ok) {
                    throw new Error(`HTTP error! status: ${res.status}`);
                }
                const data = await res.json();
                renderMatomoResources(data);
            } catch (error) {
                if (error.name === 'AbortError') {
                    document.getElementById("matomo-resources-content").innerHTML = '<div class="error">Timeout: La richiesta ha impiegato troppo tempo. Riprova.</div>';
                } else {
                    document.getElementById("matomo-resources-content").innerHTML = '<div class="error">Si è verificato un errore nel recupero delle risorse.</div>';
                }
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            try {
                // Chart controls
                const timeRangeSel = document.getElementById("time-range-select");
                const filterButton = document.getElementById("filter-button");
                function updateMatomoStats() {
                    fetchMatomoStats(timeRangeSel.value);
                }
                filterButton.addEventListener("click", updateMatomoStats);
                fetchMatomoStats(timeRangeSel.value);

                // Resource list controls
                const resourceTimeRangeSel = document.getElementById("resource-time-range-select");
                const resourceFilterButton = document.getElementById("resource-filter-button");
                function updateMatomoResources() {
                    fetchMatomoResources(resourceTimeRangeSel.value);
                }
                resourceFilterButton.addEventListener("click", updateMatomoResources);
                fetchMatomoResources(resourceTimeRangeSel.value);
            } catch (error) {
                console.error('Error initializing Matomo stats:', error);
            }
        });
        </script>
        <?php
        return $html;
    } catch (Exception $e) {
        error_log('Matomo Stats Display Error: ' . $e->getMessage());
        return '<div class="error">Si è verificato un errore nel caricamento delle statistiche.</div>';
    }
}