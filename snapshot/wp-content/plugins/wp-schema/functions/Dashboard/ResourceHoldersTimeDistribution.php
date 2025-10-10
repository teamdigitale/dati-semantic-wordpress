<?php

// Register shortcode
add_shortcode('DASHBOARD-TITOLARI_RISORSE_TEMPO', 'wp_schema_print_right_holder_time_distribution_chart');

// Register AJAX action
add_action('wp_ajax_fetch_period_data', 'wp_schema_fetch_period_data');
add_action('wp_ajax_nopriv_fetch_period_data', 'wp_schema_fetch_period_data');
add_action('wp_ajax_get_server_time', 'wp_schema_get_server_time');
add_action('wp_ajax_nopriv_get_server_time', 'wp_schema_get_server_time');

function wp_schema_get_server_time() {
    header('Content-Type: application/json');
    try {
        $now = new DateTime('now', new DateTimeZone('Europe/Rome'));
        $response = [
            'date' => $now->format('d-m-Y'), // Format: DD-MM-YYYY
            'time' => $now->format('H:i:s'), // Format: HH:mm:ss
            'timezone' => $now->getTimezone()->getName(),
            'offset' => $now->getOffset(),
            'timestamp' => $now->getTimestamp(),
            'iso' => $now->format('c') // ISO 8601 format
        ];
        wp_send_json($response);
    } catch (Exception $e) {
        wp_send_json([
            'error' => $e->getMessage(),
            'timestamp' => time(),
            'iso' => date('c')
        ]);
    }
}

function wp_schema_fetch_period_data() {
    header('Content-Type: application/json');
    
    $period = isset($_POST['period']) ? $_POST['period'] : 'all';
    
    // Calculate date and granularity based on period
    $today = new DateTime('now', new DateTimeZone('Europe/Rome'));
    $date = '';
    $granularity = 'MONTHS';
    
    // API call
    $api_url = WP_SCHEMA_API_BASE_URL . 'dashboard/aggregated-count-data?dimension=RIGHT_HOLDER,RESOURCE_TYPE';
    if (!empty($date)) {
        $api_url .= '&date=' . $date;
    }
    if (!empty($granularity)) {
        $api_url .= '&granularity=' . $granularity;
    }
    
    // Get rights holder names
    $rights_holders_url = WP_SCHEMA_API_BASE_URL . 'semantic-assets/rights-holders';
    $rights_holders_response = wp_remote_get($rights_holders_url);
    $rights_holders_data = [];
    
    if (!is_wp_error($rights_holders_response)) {
        $rights_holders_body = wp_remote_retrieve_body($rights_holders_response);
        $rights_holders = json_decode($rights_holders_body, true);
        if (is_array($rights_holders)) {
            foreach ($rights_holders as $holder) {
                if (isset($holder['identifier']) && isset($holder['name']['it'])) {
                    $rights_holders_data[$holder['identifier']] = $holder['name']['it'];
                }
            }
        }
    }

    // Fetch data for all years
    $all_data = [];
    $years = ['2023', '2024', '2025'];
    $months = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
    $mesi_ita = [
        '01' => 'Gennaio', '02' => 'Febbraio', '03' => 'Marzo', '04' => 'Aprile',
        '05' => 'Maggio', '06' => 'Giugno', '07' => 'Luglio', '08' => 'Agosto',
        '09' => 'Settembre', '10' => 'Ottobre', '11' => 'Novembre', '12' => 'Dicembre'
    ];

    foreach ($years as $year) {
        $year_url = $api_url . '&date=' . $year;
        $year_response = wp_remote_get($year_url);
        if (!is_wp_error($year_response)) {
            $year_body = wp_remote_retrieve_body($year_response);
            $year_data = json_decode($year_body, true);
            if (isset($year_data['rows']) && is_array($year_data['rows'])) {
                foreach ($year_data['rows'] as $row) {
                    if (count($row) >= 4) {
                        $month = $row[0];
                        $holder = $row[1];
                        $type = strtolower($row[2]);
                        $count = intval($row[3]);
                        
                        $holder_name = isset($rights_holders_data[$holder]) ? $rights_holders_data[$holder] : $holder;
                        $month_key = $month; // Format: MM-YYYY
                        
                        if (!isset($all_data[$holder_name])) {
                            $all_data[$holder_name] = [];
                        }
                        if (!isset($all_data[$holder_name][$month_key])) {
                            $all_data[$holder_name][$month_key] = [
                                'Ontologie' => 0,
                                'Vocabolari' => 0,
                                'Schemi dati' => 0
                            ];
                        }
                        
                        switch($type) {
                            case 'ontology':
                                $all_data[$holder_name][$month_key]['Ontologie'] = $count;
                                break;
                            case 'controlled vocabulary':
                                $all_data[$holder_name][$month_key]['Vocabolari'] = $count;
                                break;
                            case 'schema':
                                $all_data[$holder_name][$month_key]['Schemi dati'] = $count;
                                break;
                        }
                    }
                }
            }
        }
    }

    // Generate monthly distribution table HTML
    $table_html = '<div style="margin-top: 20px; overflow-x: auto;">';
    $table_html .= '<h4>Distribuzione Mensile delle Risorse per Titolare</h4>';
    $table_html .= '<table class="custom-table" style="min-width: 100%;">';
    
    // Header row with months
    $table_html .= '<thead><tr><th>Titolare</th>';
    foreach ($years as $year) {
        foreach ($months as $month) {
            $month_key = $month . '-' . $year;
            $table_html .= '<th>' . $mesi_ita[$month] . ' ' . $year . '</th>';
        }
    }
    $table_html .= '</tr></thead>';
    
    // Data rows
    $table_html .= '<tbody>';
    foreach ($all_data as $holder => $monthly_data) {
        $table_html .= '<tr><td>' . htmlspecialchars($holder) . '</td>';
        foreach ($years as $year) {
            foreach ($months as $month) {
                $month_key = $month . '-' . $year;
                $data = $monthly_data[$month_key] ?? ['Ontologie' => 0, 'Vocabolari' => 0, 'Schemi dati' => 0];
                $total = $data['Ontologie'] + $data['Vocabolari'] + $data['Schemi dati'];
                $table_html .= '<td>' . $total . '</td>';
            }
        }
        $table_html .= '</tr>';
    }
    $table_html .= '</tbody></table></div>';

    // Continue with the original response
    $response = wp_remote_get($api_url, [
        'timeout' => 30,
        'headers' => [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ],
        'sslverify' => false
    ]);
    
    if (is_wp_error($response)) {
        wp_send_json([
            'error' => $response->get_error_message()
        ]);
    }
    
    $body = wp_remote_retrieve_body($response);
    $decoded = json_decode($body, true);
    
    if (json_last_error() === JSON_ERROR_NONE) {
        wp_send_json($decoded);
    } else {
        wp_send_json([
            'error' => 'Invalid JSON response'
        ]);
    }
}

function wp_schema_print_right_holder_time_distribution_chart() {
    // Regular page load continues here...
    $period = isset($_GET['period']) ? $_GET['period'] : 'all';
    
    // Calculate date and granularity based on period
    $today = new DateTime();
    $date = '';
    $granularity = 'MONTHS';
    
    switch($period) {
        case '3months':
            // Per gli ultimi 3 mesi, prendiamo solo i dati del 2025
            $date = '2025';
            break;
        case '6months':
            // Per gli ultimi 6 mesi, prendiamo solo i dati del 2025
            $date = '2025';
            break;
        case '1year':
            // Per l'ultimo anno, prendiamo solo i dati del 2025
            $date = '2025';
            break;
        default:
            // Per 'all', non specifichiamo una data per ottenere tutti i dati
            $date = '';
    }

    // Debug information
    $debug_info = [
        'period' => $period,
        'today' => $today->format('Y-m-d'),
        'calculated_date' => $date,
        'granularity' => $granularity,
        'api_url' => WP_SCHEMA_API_BASE_URL . 'dashboard/aggregated-count-data?dimension=RIGHT_HOLDER,RESOURCE_TYPE'
    ];

    // API call
    $api_url = WP_SCHEMA_API_BASE_URL . 'dashboard/aggregated-count-data?dimension=RIGHT_HOLDER,RESOURCE_TYPE';
    if (!empty($date)) {
        $api_url .= '&date=' . $date;
    }
    if (!empty($granularity)) {
        $api_url .= '&granularity=' . $granularity;
    }
    $response = wp_remote_get($api_url);
    if (is_wp_error($response)) {
        echo "<p>Errore nella chiamata all'API: " . esc_html($response->get_error_message()) . "</p>";
        return;
    }
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    // Get rights holder names
    $rights_holders_url = WP_SCHEMA_API_BASE_URL . 'semantic-assets/rights-holders';
    $rights_holders_response = wp_remote_get($rights_holders_url);
    $rights_holders_data = [];
    
    if (!is_wp_error($rights_holders_response)) {
        $rights_holders_body = wp_remote_retrieve_body($rights_holders_response);
        $rights_holders = json_decode($rights_holders_body, true);
        if (is_array($rights_holders)) {
            foreach ($rights_holders as $holder) {
                if (isset($holder['identifier']) && isset($holder['name']['it'])) {
                    $rights_holders_data[$holder['identifier']] = $holder['name']['it'];
                }
            }
        }
    }

    // Parse data
    $holders = [];
    $series_data = [
        'Ontologie' => [],
        'Vocabolari' => [],
        'Schemi dati' => [],
    ];
    $type_map = [
        'ontology' => 'Ontologie',
        'controlled vocabulary' => 'Vocabolari',
        'schema' => 'Schemi dati',
    ];

    if (isset($data['rows']) && is_array($data['rows'])) {
        foreach ($data['rows'] as $row) {
            $holder = $row[1]; // index 1 for rights holder
            $type = strtolower($row[2]); // index 2 for resource type
            $count = intval($row[3]);    // index 3 for value
            
            // Use Italian name if available, otherwise use identifier
            $holder_name = isset($rights_holders_data[$holder]) ? $rights_holders_data[$holder] : $holder;
            
            if (!in_array($holder_name, $holders)) {
                $holders[] = $holder_name;
            }
            if (isset($type_map[$type])) {
                $series_data[$type_map[$type]][$holder_name] = $count;
            }
        }
    }

    // Calcola il totale per ogni titolare
    $totali = [];
    foreach ($holders as $holder) {
        $totali[$holder] =
            ($series_data['Ontologie'][$holder] ?? 0) +
            ($series_data['Vocabolari'][$holder] ?? 0) +
            ($series_data['Schemi dati'][$holder] ?? 0);
    }
    // Filtra solo quelli con almeno una risorsa
    $totali = array_filter($totali, function($tot) { return $tot > 0; });
    // Sort holders from largest to smallest
    uasort($totali, function($a, $b) { return $b - $a; });
    $holders_sorted = array_keys($totali);
    // Ricostruisci i dati delle serie secondo il nuovo ordine
    $ontologie = [];
    $vocabolari = [];
    $schemi = [];
    foreach ($holders_sorted as $holder) {
        $ontologie[] = $series_data['Ontologie'][$holder] ?? 0;
        $vocabolari[] = $series_data['Vocabolari'][$holder] ?? 0;
        $schemi[] = $series_data['Schemi dati'][$holder] ?? 0;
    }
    $holders = $holders_sorted;

    // Filtra solo i titolari con almeno una risorsa
    $holders = array_filter($holders, function($holder) use ($series_data) {
        return ($series_data['Ontologie'][$holder] ?? 0) +
               ($series_data['Vocabolari'][$holder] ?? 0) +
               ($series_data['Schemi dati'][$holder] ?? 0) > 0;
    });

    // Save the 10 largest for default selection
    $default_selected = array_slice($holders_sorted, 0, 10);
    // Reverse order to show from largest at top to smallest at bottom
    $holders = array_reverse($holders_sorted);
    $ontologie = array_reverse($ontologie);
    $vocabolari = array_reverse($vocabolari);
    $schemi = array_reverse($schemi);

    // Consistent colors with the reference
    $colori = [
        'Ontologie' => '#0043E3', // blue
        'Vocabolari' => '#077F7B', // green
        'Schemi dati' => '#EBA704', // orange
    ];

    // Calculate dynamic height based on number of holders
    $height = max(400, count($holders) * 48);

    // Pass data to JS
    $series_data_js = [
        'holders' => $holders,
        'series_data' => $series_data,
        'totali' => $totali,
        'default_selected' => array_reverse($default_selected) // inverti per matchare l'ordine di holders
    ];
    ?>
    <link rel="stylesheet" href="<?php echo plugins_url('wp-bootstrap-italia/css/bootstrap-italia.min.css'); ?>">
    <script src="<?php echo plugins_url('wp-bootstrap-italia/js/bootstrap-italia.bundle.min.js'); ?>"></script>
    <style>
      .nav-tabs .nav-item { flex: 1 1 0; text-align: center; }
      .nav-tabs { display: flex; border-bottom: 2px solid #e5e9ec; }
      .nav-tabs .nav-link { width: 100%; border: none; border-bottom: 2px solid transparent; color: #5c6f82; font-size: 1.2rem; font-weight: 600; background: none; }
      .nav-tabs .nav-link.active { color: #0066cc; border-bottom: 2px solid #0066cc; background: none; }
      .nav-tabs .nav-link:focus {
        outline: 3px solid #0066cc;
        outline-offset: 2px;
      }
      .custom-table { border-collapse: separate; border-spacing: 0; width: 100%; font-size: 0.95rem; }
      .custom-table th { background: #e5e9ec; color: #2c3e50; font-weight: 600; border: none; padding: 12px 8px; }
      .custom-table td { border: none; padding: 12px 8px; }
      .custom-table tbody tr:nth-child(even) { background: #f5f6f7; }
      .custom-table tbody tr:nth-child(odd) { background: #fff; }
      .custom-table tr { transition: background 0.2s; }
      .custom-table thead tr { border-bottom: 2px solid #e5e9ec; }
      @media (max-width: 600px) {
        .custom-table { font-size: 0.85rem; }
        .holder-multiselect-row, .holder-multiselect-row-table {
          flex-direction: column !important;
          align-items: stretch !important;
        }
        .custom-multiselect-filter-btn {
          margin-left: 0 !important;
          margin-top: 10px;
          width: 100%;
        }
      }
      /* Multiselect styles (come prima) */
      .custom-multiselect { position: relative; display: inline-block; min-width: 320px; max-width: 420px; font-size: 15px; }
      .custom-multiselect-btn { border: 1px solid #C5C7CD; border-radius: 6px; padding: 8px 32px 8px 12px; background: #fff; width: 100%; text-align: left; cursor: pointer; position: relative; }
      .custom-multiselect-btn:after { content: ''; position: absolute; right: 12px; top: 50%; width: 16px; height: 16px; background: url('data:image/svg+xml;utf8,<svg fill=\"%23666\" height=\"20\" viewBox=\"0 0 20 20\" width=\"20\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M7.293 8.293a1 1 0 0 1 1.414 0L10 9.586l1.293-1.293a1 1 0 1 1 1.414 1.414l-2 2a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 0-1.414z\"/></svg>') no-repeat center center; transform: translateY(-50%); pointer-events: none; }
      .custom-multiselect-list { display: none; position: absolute; z-index: 1000; background: #fff; border: 1px solid #C5C7CD; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); max-height: 320px; overflow-y: auto; width: 100%; margin-top: 2px; }
      .custom-multiselect-list.open { display: block; }
      .custom-multiselect-item { padding: 8px 12px; cursor: pointer; display: flex; align-items: center; gap: 8px; }
      .custom-multiselect-item:hover { background: #f5f6f7; }
      .custom-multiselect-checkbox { accent-color: #0043E3; width: 18px; height: 18px; }
      .custom-multiselect-label { flex: 1; font-size: 15px; color: #222; font-weight: 500; white-space: normal; }
      .custom-multiselect-footer { padding: 8px 12px; border-top: 1px solid #e5e9ec; background: #f5f6f7; text-align: right; }
      .custom-multiselect-filter-btn { background: #fff; border: 1px solid #0043E3; color: #0043E3; border-radius: 6px; padding: 6px 18px; font-size: 15px; font-weight: 600; cursor: pointer; margin-left: 12px; transition: background 0.2s, color 0.2s; }
      .custom-multiselect-filter-btn:hover { background: #0043E3; color: #fff; }
      .custom-multiselect-btn:focus {
        outline: 3px solid #0066cc;
        outline-offset: 2px;
      }
      .custom-multiselect-item:focus {
        outline: 3px solid #0066cc;
        outline-offset: 2px;
      }
      .custom-multiselect-filter-btn:focus {
        outline: 3px solid #0066cc;
        outline-offset: 2px;
      }
      .period-selector {
        display: inline-block;
        margin-right: 16px;
      }
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
        .holder-multiselect-row, .holder-multiselect-row-table {
          flex-direction: column !important;
          align-items: stretch !important;
        }
        .period-selector {
          margin-right: 0 !important;
          margin-bottom: 10px;
          width: 100%;
        }
        .period-selector select {
          width: 100%;
        }
      }
      .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
      }
      /* Miglioramento del contrasto per i colori del grafico */
      .custom-multiselect-checkbox {
        accent-color: #0055FF; /* Colore più scuro per migliore contrasto */
      }
      .custom-multiselect-filter-btn {
        background: #fff;
        border: 1px solid #0055FF; /* Colore più scuro per migliore contrasto */
        color: #0055FF;
      }
      .custom-multiselect-filter-btn:hover {
        background: #0055FF;
        color: #fff;
      }
      /* Miglioramento contrasto per testo */
      .custom-table th {
        color: #000000;
      }
      
      .custom-table td {
        color: #000000;
      }
      
      /* Stile per stato di caricamento */
      .loading {
        position: relative;
      }
      
      .loading::after {
        content: "Caricamento in corso...";
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(255, 255, 255, 0.9);
        padding: 1rem;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      }
      
      .status-message {
        position: fixed;
        top: 1rem;
        right: 1rem;
        padding: 1rem;
        background: #fff;
        border: 1px solid #0066cc;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        z-index: 1000;
      }
      
      .status-message:not(:empty) {
        display: block;
      }
      
      .status-message:empty {
        display: none;
      }

      #chart-titolari-tempo {
        width: 100% !important;
        min-width: 300px;
      }
    </style>
    <!-- FILTRI UNICI -->
    <div class="holder-multiselect-row" style="display:flex;gap:16px;align-items:center;margin-bottom:20px;">
      <div class="period-selector">
        <select id="period-select">
          <option value="all" <?php echo $period === 'all' ? 'selected' : ''; ?>>Dall'inizio</option>
          <option value="1year" <?php echo $period === '1year' ? 'selected' : ''; ?>>Ultimo anno</option>
          <option value="6months" <?php echo $period === '6months' ? 'selected' : ''; ?>>Ultimi 6 mesi</option>
          <option value="3months" <?php echo $period === '3months' ? 'selected' : ''; ?>>Ultimi 3 mesi</option>
        </select>
      </div>
      <div class="custom-multiselect" id="holder-multiselect-container" role="combobox" aria-expanded="false" aria-haspopup="listbox" aria-controls="holder-multiselect-list">
        <div class="custom-multiselect-btn" id="holder-multiselect-btn" role="combobox" aria-expanded="false" aria-haspopup="listbox" aria-controls="holder-multiselect-list">Seleziona titolari...</div>
        <div class="custom-multiselect-list" id="holder-multiselect-list" role="listbox" aria-multiselectable="true"></div>
      </div>
      <button class="custom-multiselect-filter-btn" id="holder-multiselect-filter">Filtra</button>
    </div>
    <div class="it-tabs it-tabs-primary" style="margin-bottom:40px;">
      <div id="status-message" class="status-message" role="status" aria-live="polite"></div>
      <ul class="nav nav-tabs" id="resourceHoldersTimeTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="resource-holders-time-grafico-tab" data-bs-toggle="tab" data-bs-target="#resource-holders-time-grafico" type="button" role="tab" aria-controls="resource-holders-time-grafico" aria-selected="true">Grafico</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="resource-holders-time-tabella-tab" data-bs-toggle="tab" data-bs-target="#resource-holders-time-tabella" type="button" role="tab" aria-controls="resource-holders-time-tabella" aria-selected="false">Tabella</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="resource-holders-time-info-tab" data-bs-toggle="tab" data-bs-target="#resource-holders-time-info" type="button" role="tab" aria-controls="resource-holders-time-info" aria-selected="false">Info</button>
        </li>
      </ul>
      <div class="tab-content" id="resourceHoldersTimeTabsContent" style="background:#fff; border-radius:8px; box-shadow:0 1px 4px rgba(0,0,0,0.04); padding: 24px 12px;">
        <div class="tab-pane fade show active" id="resource-holders-time-grafico" role="tabpanel" aria-labelledby="resource-holders-time-grafico-tab">
          <div style="font-size:20px;font-weight:600;margin-bottom:10px;">Distribuzione delle risorse tra i Titolari (nel tempo)</div>
          <!-- FILTRI RIMOSSI DA QUI -->
          <div id="chart-titolari-tempo" 
               style="width: 100%; height: <?php echo $height; ?>px; margin:auto; background:#fff; border-radius:8px; box-shadow:0 1px 4px rgba(0,0,0,0.04);"
               role="img"
               aria-label="Grafico della distribuzione delle risorse tra i titolari"
               aria-describedby="chart-description"></div>
          <div id="chart-description" class="sr-only">
            Questo grafico mostra la distribuzione delle risorse semantiche (ontologie, vocabolari e schemi dati) tra i vari titolari.
            I dati sono rappresentati con barre impilate, dove ogni barra rappresenta un titolare e le sezioni colorate rappresentano i diversi tipi di risorse.
            Le barre sono ordinate dal titolare con il minor numero di risorse (in alto) al titolare con il maggior numero di risorse (in basso).
            I colori utilizzati sono: blu per le ontologie, verde per i vocabolari e arancione per gli schemi dati.
            È possibile utilizzare i tasti freccia su e giù per navigare tra i titolari e i tasti freccia sinistra e destra per navigare tra i tipi di risorse.
          </div>
        </div>
        <div class="tab-pane fade" id="resource-holders-time-tabella" role="tabpanel" aria-labelledby="resource-holders-time-tabella-tab">
          <div style="font-size:20px;font-weight:600;margin-bottom:10px;">Tabella dati</div>
          <!-- FILTRI RIMOSSI DA QUI -->
          <div class="table-responsive">
            <table class="custom-table" id="holder-table" role="grid" aria-label="Tabella della distribuzione delle risorse tra i titolari">
              <thead>
                <tr>
                  <th scope="col">Titolare</th>
                  <th scope="col">Ontologie</th>
                  <th scope="col">Vocabolari</th>
                  <th scope="col">Schemi dati</th>
                  <th scope="col">Totale</th>
                </tr>
              </thead>
              <tbody>
                <!-- Popolato da JS -->
              </tbody>
            </table>
          </div>
        </div>
        <div class="tab-pane fade" id="resource-holders-time-info" role="tabpanel" aria-labelledby="resource-holders-time-info-tab">
          <p><strong>DESCRIZIONE GENERALE</strong></p>
          <p>Questa dashboard mostra la distribuzione delle risorse semantiche tra i vari titolari.</p>

          <p><strong>PERIODI TEMPORALI</strong></p>
          <p>Puoi visualizzare i dati per diversi periodi temporali:</p>
          <ul>
            <li>Dall'inizio: mostra il numero totale di risorse per ogni titolare</li>
            <li>Ultimo anno: mostra le risorse aggiunte nell'ultimo anno</li>
            <li>Ultimi 6 mesi: mostra le risorse aggiunte negli ultimi 6 mesi</li>
            <li>Ultimi 3 mesi: mostra le risorse aggiunte negli ultimi 3 mesi</li>
          </ul>

          <p><strong>FUNZIONALITÀ</strong></p>
          <ul>
            <li>Selezione titolari: puoi selezionare quali titolari visualizzare utilizzando il menu a tendina</li>
            <li>Visualizzazione grafico: mostra la distribuzione delle risorse in formato grafico a barre impilate</li>
            <li>Visualizzazione tabella: mostra i dati in formato tabellare con i totali per ogni categoria</li>
            <li>Ordinamento: i titolari sono ordinati dal più piccolo al più grande in base al numero totale di risorse</li>
          </ul>
        </div>
      </div>
    </div>
    <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
      // Funzione per annunciare cambiamenti agli screen reader
      function announceToScreenReader(message) {
        var statusMessage = document.getElementById('status-message');
        statusMessage.textContent = message;
        setTimeout(function() {
          statusMessage.textContent = '';
        }, 3000);
      }
      
      // Funzione per gestire il caricamento
      function setLoading(isLoading) {
        var chartDom = document.getElementById('chart-titolari-tempo');
        if (isLoading) {
          chartDom.classList.add('loading');
          announceToScreenReader('Caricamento dei dati in corso...');
        } else {
          chartDom.classList.remove('loading');
          announceToScreenReader('Dati caricati con successo');
        }
      }
      
      // --- DATA ---
      var data = <?php echo json_encode($series_data_js); ?>;
      var colori = <?php echo json_encode($colori); ?>;
      var rightsHoldersMap = <?php echo json_encode($rights_holders_data); ?>;
      // Funzione per ottenere il nome leggibile del titolare
      function getHolderDisplayName(holder) {
        return rightsHoldersMap[holder] || holder;
      }

      // Funzione per recuperare i dati per periodo (AJAX)
      function fetchDataForPeriod(period) {
        setLoading(true);
        var today = new Date();
        var date;

        // Calculate reference dates for each period
        var referenceDates = {
            '3months': {
                month: '03',
                year: '2025',
                label: 'Ultimi 3 mesi'
            },
            '6months': {
                month: '12',
                year: '2024',
                label: 'Ultimi 6 mesi'
            },
            '1year': {
                month: '05',
                year: '2024',
                label: 'Ultimo anno'
            },
            'all': {
                month: String(today.getMonth() + 1).padStart(2, '0'),
                year: today.getFullYear(),
                label: 'Dall\'inizio'
            }
        };

        // Get current month key
        var currentMonth = String(today.getMonth() + 1).padStart(2, '0');
        var currentYear = today.getFullYear();
        var currentMonthKey = currentMonth + '-' + currentYear;

        // Create form data
        var formData = new FormData();
        formData.append('action', 'fetch_period_data');
        formData.append('period', period);

        return fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status);
            }
            return response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    throw new Error('Invalid JSON response');
                }
            });
        })
        .then(newData => {
            if (newData.error) {
                throw new Error(newData.error);
            }

            // Process new data
            var holders = [];
            var series_data = {
                'Ontologie': {},
                'Vocabolari': {},
                'Schemi dati': {}
            };

            if (newData.rows) {
                // First, collect all holders
                newData.rows.forEach(row => {
                    var holder = row[1];
                    if (!holders.includes(holder)) {
                        holders.push(holder);
                    }
                });

                // Then calculate incremental values for each holder
                holders.forEach(holder => {
                    var currentValue = { ontologie: 0, vocabolari: 0, schemi: 0 };
                    var refValue = { ontologie: 0, vocabolari: 0, schemi: 0 };

                    // Get current value (from current month)
                    newData.rows.forEach(row => {
                        if (row[0] === currentMonthKey && row[1] === holder) {
                            switch(row[2].toLowerCase()) {
                                case 'ontology':
                                    currentValue.ontologie = parseInt(row[3]);
                                    break;
                                case 'controlled vocabulary':
                                    currentValue.vocabolari = parseInt(row[3]);
                                    break;
                                case 'schema':
                                    currentValue.schemi = parseInt(row[3]);
                                    break;
                            }
                        }
                    });

                    // Get reference value if not 'all'
                    if (period !== 'all') {
                        var ref = referenceDates[period];
                        var refMonthKey = ref.month + '-' + ref.year;

                        newData.rows.forEach(row => {
                            if (row[0] === refMonthKey && row[1] === holder) {
                                switch(row[2].toLowerCase()) {
                                    case 'ontology':
                                        refValue.ontologie = parseInt(row[3]);
                                        break;
                                    case 'controlled vocabulary':
                                        refValue.vocabolari = parseInt(row[3]);
                                        break;
                                    case 'schema':
                                        refValue.schemi = parseInt(row[3]);
                                        break;
                                }
                            }
                        });
                    }

                    // Calculate final values
                    if (period === 'all') {
                        series_data['Ontologie'][holder] = currentValue.ontologie;
                        series_data['Vocabolari'][holder] = currentValue.vocabolari;
                        series_data['Schemi dati'][holder] = currentValue.schemi;
                    } else {
                        series_data['Ontologie'][holder] = currentValue.ontologie - refValue.ontologie;
                        series_data['Vocabolari'][holder] = currentValue.vocabolari - refValue.vocabolari;
                        series_data['Schemi dati'][holder] = currentValue.schemi - refValue.schemi;
                    }
                });
            }

            // Calculate totals for sorting
            var totali = {};
            holders.forEach(holder => {
                totali[holder] = 
                    (series_data['Ontologie'][holder] || 0) +
                    (series_data['Vocabolari'][holder] || 0) +
                    (series_data['Schemi dati'][holder] || 0);
            });

            // Filtra solo i titolari con almeno una risorsa
            holders = holders.filter(holder => 
              (series_data['Ontologie'][holder] || 0) +
              (series_data['Vocabolari'][holder] || 0) +
              (series_data['Schemi dati'][holder] || 0) > 0
            );

            // Sort holders by total (ascending)
            holders.sort((a, b) => totali[a] - totali[b]);

            return {
                holders: holders,
                series_data: series_data,
                totali: totali
            };
        })
        .catch(error => {
            setLoading(false);
            announceToScreenReader('Si è verificato un errore durante il caricamento dei dati');
        });
      }
      
      // --- PERIOD SELECTION ---
      var currentPeriod = '<?php echo $period; ?>';
      
      // --- MULTISELECT GRAFICO E TABELLA (cross-tab) ---
      var holders = data.holders;
      var totali = data.totali;
      var defaultSelected = data.default_selected;
      var selected = new Set(defaultSelected); // Unico set condiviso

      // Funzione per sincronizzare il multiselect e aggiornare grafico/tabella
      function syncSelectionsAndUpdate() {
        renderMultiselect('holder-multiselect-container', 'holder-multiselect-list', 'holder-multiselect-btn', selected, syncSelectionsAndUpdate);
        updateChart();
        updateTable();
      }

      // Modifica renderMultiselect per accettare un callback onChange
      function renderMultiselect(containerId, listId, btnId, selectedSet, onChange) {
        var list = document.getElementById(listId);
        var btn = document.getElementById(btnId);
        
        // Remove old event listeners
        var newList = list.cloneNode(true);
        list.parentNode.replaceChild(newList, list);
        list = newList;
        
        var newBtn = btn.cloneNode(true);
        btn.parentNode.replaceChild(newBtn, btn);
        btn = newBtn;
        
        list.innerHTML = '';
        
        if (!data || !data.holders) {
          return;
        }
        // Aggiungi "Seleziona tutti" come prima voce
        var selectAllItem = document.createElement('div');
        selectAllItem.className = 'custom-multiselect-item';
        selectAllItem.setAttribute('role', 'option');
        selectAllItem.setAttribute('aria-selected', selectedSet.size === data.holders.length);
        var selectAllCheckbox = document.createElement('input');
        selectAllCheckbox.type = 'checkbox';
        selectAllCheckbox.className = 'custom-multiselect-checkbox';
        selectAllCheckbox.value = '__all__';
        selectAllCheckbox.id = 'checkbox-all-' + listId;
        selectAllCheckbox.setAttribute('aria-label', 'Seleziona tutti i titolari');
        selectAllCheckbox.checked = selectedSet.size === data.holders.length;
        var selectAllLabel = document.createElement('label');
        selectAllLabel.className = 'custom-multiselect-label';
        selectAllLabel.textContent = 'Seleziona tutti';
        selectAllLabel.setAttribute('for', 'checkbox-all-' + listId);
        selectAllItem.appendChild(selectAllCheckbox);
        selectAllItem.appendChild(selectAllLabel);
        selectAllItem.addEventListener('click', function(e) {
          if (e.target !== selectAllCheckbox) selectAllCheckbox.checked = !selectAllCheckbox.checked;
          if (selectAllCheckbox.checked) {
            data.holders.forEach(h => selectedSet.add(h));
          } else {
            selectedSet.clear();
          }
          updateBtnLabel();
          if (onChange) onChange();
        });
        selectAllCheckbox.addEventListener('click', function(e) {
          e.stopPropagation();
          if (selectAllCheckbox.checked) {
            data.holders.forEach(h => selectedSet.add(h));
          } else {
            selectedSet.clear();
          }
          updateBtnLabel();
          if (onChange) onChange();
        });
        list.appendChild(selectAllItem);
        // Fine "Seleziona tutti"
        data.holders.forEach(function(holder) {
          var item = document.createElement('div');
          item.className = 'custom-multiselect-item';
          item.setAttribute('role', 'option');
          item.setAttribute('aria-selected', selectedSet.has(holder));
          var checkbox = document.createElement('input');
          checkbox.type = 'checkbox';
          checkbox.className = 'custom-multiselect-checkbox';
          checkbox.value = holder;
          checkbox.id = 'checkbox-' + holder + '-' + listId;
          checkbox.setAttribute('aria-label', 'Seleziona ' + getHolderDisplayName(holder));
          if (selectedSet.has(holder)) checkbox.checked = true;
          var label = document.createElement('label');
          label.className = 'custom-multiselect-label';
          label.textContent = getHolderDisplayName(holder);
          label.setAttribute('for', 'checkbox-' + holder + '-' + listId);
          item.appendChild(checkbox);
          item.appendChild(label);
          item.addEventListener('click', function(e) {
            if (e.target !== checkbox) checkbox.checked = !checkbox.checked;
            if (checkbox.checked) selectedSet.add(holder); else selectedSet.delete(holder);
            item.setAttribute('aria-selected', checkbox.checked);
            updateBtnLabel();
            if (onChange) onChange();
          });
          checkbox.addEventListener('click', function(e) {
            e.stopPropagation();
            if (checkbox.checked) selectedSet.add(holder); else selectedSet.delete(holder);
            item.setAttribute('aria-selected', checkbox.checked);
            updateBtnLabel();
            if (onChange) onChange();
          });
          list.appendChild(item);
        });
        
        // Dropdown logic
        btn.setAttribute('role', 'combobox');
        btn.setAttribute('aria-expanded', 'false');
        btn.setAttribute('aria-haspopup', 'listbox');
        btn.setAttribute('aria-controls', listId);
        
        list.setAttribute('role', 'listbox');
        list.setAttribute('aria-multiselectable', 'true');
        
        btn.addEventListener('click', function(e) {
          var isExpanded = list.classList.toggle('open');
          btn.setAttribute('aria-expanded', isExpanded);
        });
        
        document.addEventListener('click', function(e) {
          if (!btn.contains(e.target) && !list.contains(e.target)) {
            list.classList.remove('open');
            btn.setAttribute('aria-expanded', 'false');
          }
        });
        
        // Keyboard navigation
        list.addEventListener('keydown', function(e) {
          var items = list.querySelectorAll('.custom-multiselect-item');
          var currentIndex = Array.from(items).indexOf(document.activeElement);
          
          switch(e.key) {
            case 'ArrowDown':
              e.preventDefault();
              if (currentIndex < items.length - 1) {
                items[currentIndex + 1].focus();
              }
              break;
            case 'ArrowUp':
              e.preventDefault();
              if (currentIndex > 0) {
                items[currentIndex - 1].focus();
              }
              break;
            case 'Enter':
            case ' ':
              e.preventDefault();
              if (document.activeElement.classList.contains('custom-multiselect-item')) {
                document.activeElement.click();
              }
              break;
            case 'Escape':
              e.preventDefault();
              list.classList.remove('open');
              btn.setAttribute('aria-expanded', 'false');
              break;
          }
        });
        
        // Mostra i selezionati nel bottone
        function updateBtnLabel() {
          var arr = data.holders.filter(h => selectedSet.has(h));
          var label = '';
          if (arr.length === 0) {
            label = 'Nessun titolare selezionato';
          } else if (arr.length === 1) {
            label = getHolderDisplayName(arr[0]);
          } else if (arr.length === data.holders.length) {
            label = 'Tutti i titolari selezionati';
          } else {
            label = arr.length + ' titolari selezionati';
          }
          btn.textContent = label;
          btn.setAttribute('aria-label', label);
        }
        updateBtnLabel();
      }

      // --- GRAFICO ---
      var chartDom = document.getElementById('chart-titolari-tempo');
      var myChart = echarts.init(chartDom);
      
      function updateChart() {
        var selectedArr = data.holders.filter(h => selected.has(h));
        var ontologie = selectedArr.map(h => data.series_data.Ontologie[h] || 0);
        var vocabolari = selectedArr.map(h => data.series_data.Vocabolari[h] || 0);
        var schemi = selectedArr.map(h => data.series_data['Schemi dati'][h] || 0);
        var height = Math.max(400, selectedArr.length * 48);
        chartDom.style.height = height + 'px';
        myChart.resize();
        
        // Sort holders from smallest to largest
        var sortedData = selectedArr.map((holder, index) => ({
          holder: holder,
          total: ontologie[index] + vocabolari[index] + schemi[index],
          ontologie: ontologie[index],
          vocabolari: vocabolari[index],
          schemi: schemi[index]
        })).sort((a, b) => a.total - b.total);
        
        var option = {
          color: [colori['Ontologie'], colori['Vocabolari'], colori['Schemi dati']],
          title: { show: false },
          tooltip: { 
            trigger: 'axis', 
            axisPointer: { type: 'shadow' },
            formatter: function(params) {
              var result = params[0].name + '<br/>';
              var total = 0;
              params.forEach(function(param) {
                result += param.seriesName + ': ' + param.value + '<br/>';
                total += param.value;
              });
              result += '<b>Totale: ' + total + '</b>';
              return result;
            }
          },
          legend: {
            data: ['Ontologie', 'Vocabolari', 'Schemi dati'],
            bottom: 10,
            left: 'left',
            itemWidth: 12,
            itemHeight: 12,
            icon: 'circle',
            textStyle: { fontWeight: 600, fontSize: 13 }
          },
          grid: {
            left: 220,
            right: 30,
            top: 40,
            bottom: 80
          },
          xAxis: {
            type: 'value',
            name: 'Numero risorse',
            nameLocation: 'middle',
            nameGap: 35,
            axisLine: { lineStyle: { color: '#bbb' } },
            axisLabel: { fontSize: 15 },
            splitNumber: 5,
            axisLabel: {
              fontSize: 15,
              formatter: function (value) {
                return Number.isInteger(value) ? value : '';
              }
            }
          },
          yAxis: {
            type: 'category',
            data: sortedData.map(d => getHolderDisplayName(d.holder)),
            axisLine: { lineStyle: { color: '#bbb' } },
            axisLabel: {
              fontSize: 12,
              interval: 0,
              lineHeight: 13,
              color: '#222',
              fontWeight: 500,
              formatter: function(value) {
                var maxLineLength = 32;
                var words = value.split(' ');
                var lines = [];
                var currentLine = '';
                for (var i = 0; i < words.length; i++) {
                  if ((currentLine + words[i]).length > maxLineLength) {
                    lines.push(currentLine.trim());
                    currentLine = words[i] + ' ';
                  } else {
                    currentLine += words[i] + ' ';
                  }
                }
                lines.push(currentLine.trim());
                return lines.join('\n');
              }
            },
            splitLine: {
              show: true,
              lineStyle: {
                color: '#eee'
              }
            }
          },
          series: [
            {
              name: 'Ontologie',
              type: 'bar',
              stack: 'totale-tempo',
              data: sortedData.map(d => d.ontologie),
              barWidth: 32,
              emphasis: {
                focus: 'series'
              }
            },
            {
              name: 'Vocabolari',
              type: 'bar',
              stack: 'totale-tempo',
              data: sortedData.map(d => d.vocabolari),
              barWidth: 32,
              emphasis: {
                focus: 'series'
              }
            },
            {
              name: 'Schemi dati',
              type: 'bar',
              stack: 'totale-tempo',
              data: sortedData.map(d => d.schemi),
              barWidth: 32,
              emphasis: {
                focus: 'series'
              }
            }
          ]
        };
        myChart.setOption(option);
        
        // Restore opacity
        chartDom.style.opacity = '1';
      }
      
      // --- TABELLA ---
      function updateTable() {
        var selectedArr = data.holders.filter(h => selected.has(h));
        var ontologie = selectedArr.map(h => data.series_data.Ontologie[h] || 0);
        var vocabolari = selectedArr.map(h => data.series_data.Vocabolari[h] || 0);
        var schemi = selectedArr.map(h => data.series_data['Schemi dati'][h] || 0);
        
        // Sort data from largest to smallest (descending)
        var sortedData = selectedArr.map((holder, index) => ({
          holder: holder,
          total: ontologie[index] + vocabolari[index] + schemi[index],
          ontologie: ontologie[index],
          vocabolari: vocabolari[index],
          schemi: schemi[index]
        })).sort((a, b) => b.total - a.total);
        
        var tbody = document.querySelector('#holder-table tbody');
        tbody.innerHTML = '';
        sortedData.forEach(function(data) {
          var tr = document.createElement('tr');
          tr.innerHTML = '<td>' + getHolderDisplayName(data.holder) + '</td>' +
            '<td>' + data.ontologie + '</td>' +
            '<td>' + data.vocabolari + '</td>' +
            '<td>' + data.schemi + '</td>' +
            '<td>' + data.total + '</td>';
          tbody.appendChild(tr);
        });
      }
      
      // Update filter button click handler (unico)
      document.getElementById('holder-multiselect-filter').addEventListener('click', function() {
        var newPeriod = document.getElementById('period-select').value;
        if (newPeriod !== currentPeriod) {
          currentPeriod = newPeriod;
          fetchDataForPeriod(newPeriod)
            .then(newData => {
              data = newData;
              holders = newData.holders;
              // Aggiorna la selezione: mantieni solo i titolari ancora presenti
              var newSelected = new Set();
              selected.forEach(holder => {
                if (holders.includes(holder)) {
                  newSelected.add(holder);
                }
              });
              if (newSelected.size === 0) {
                holders.slice(0, 10).forEach(holder => newSelected.add(holder));
              }
              selected = newSelected;
              syncSelectionsAndUpdate();
              setLoading(false);
            })
            .catch(error => {
              setLoading(false);
              announceToScreenReader('Si è verificato un errore durante il caricamento dei dati');
            });
        } else {
          syncSelectionsAndUpdate();
          setLoading(false);
        }
        document.getElementById('holder-multiselect-list').classList.remove('open');
      });

      // Inizializzazione cross-tab
      syncSelectionsAndUpdate();
      
      // Fetch initial data
      fetchDataForPeriod(currentPeriod)
        .then(newData => {
          data = newData;
          holders = newData.holders;
          // Calcola i totali per ordinare
          var totali = {};
          holders.forEach(holder => {
            totali[holder] = 
              (newData.series_data['Ontologie'][holder] || 0) +
              (newData.series_data['Vocabolari'][holder] || 0) +
              (newData.series_data['Schemi dati'][holder] || 0);
          });
          // Ordina per totale discendente e seleziona i primi 10
          var sortedHolders = holders.sort((a, b) => totali[b] - totali[a]);
          selected = new Set(sortedHolders.slice(0, 10));
          syncSelectionsAndUpdate();
          setLoading(false);
        })
        .catch(error => {
          console.error('Error loading initial data:', error);
          setLoading(false);
        });

      window.addEventListener('resize', function() { myChart.resize(); });

      // Aggiungi gestione della tastiera per il grafico
      chartDom.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          // Implementa la navigazione del grafico con la tastiera
          var currentIndex = 0;
          var items = myChart.getOption().yAxis[0].data;
          
          function focusItem(index) {
            var holder = items[index];
            var total = data.series_data.Ontologie[holder] + 
                       data.series_data.Vocabolari[holder] + 
                       data.series_data['Schemi dati'][holder];
            
            // Annuncia i dettagli allo screen reader
            announceToScreenReader(
              getHolderDisplayName(holder) + 
              '. Totale risorse: ' + total + 
              '. Ontologie: ' + (data.series_data.Ontologie[holder] || 0) +
              '. Vocabolari: ' + (data.series_data.Vocabolari[holder] || 0) +
              '. Schemi dati: ' + (data.series_data['Schemi dati'][holder] || 0)
            );
            
            // Aggiorna il tooltip
            myChart.dispatchAction({
              type: 'showTip',
              seriesIndex: 0,
              dataIndex: index
            });
          }
          
          switch(e.key) {
            case 'ArrowDown':
              if (currentIndex < items.length - 1) {
                currentIndex++;
                focusItem(currentIndex);
              }
              break;
            case 'ArrowUp':
              if (currentIndex > 0) {
                currentIndex--;
                focusItem(currentIndex);
              }
              break;
            case 'ArrowRight':
              e.preventDefault();
              // Naviga tra i tipi di risorse
              break;
            case 'ArrowLeft':
              e.preventDefault();
              // Naviga tra i tipi di risorse
              break;
          }
        }
      });

      // More robust fix: resize chart when "Chart" tab is shown
      function resizeChartOnTabShow() {
        setTimeout(function() {
          var chartDom = document.getElementById('chart-titolari-tempo');
          myChart.resize();
        }, 150);
      }
      // Bootstrap 5: evento sul bottone della tab
      document.getElementById('resource-holders-time-grafico-tab').addEventListener('click', function() {
      });
      // Fallback: evento sul contenuto della tab (per sicurezza)
      document.getElementById('resource-holders-time-grafico').addEventListener('shown.bs.tab', resizeChartOnTabShow);
      // Per Bootstrap Italia o altri casi, anche su 'shown.bs.collapse'
      document.getElementById('resource-holders-time-grafico').addEventListener('shown.bs.collapse', resizeChartOnTabShow);

      if (window.ResizeObserver) {
        var chartDom = document.getElementById('chart-titolari-tempo');
        var resizeObserver = new ResizeObserver(function() {
          myChart.resize();
        });
        resizeObserver.observe(chartDom);
      }
    });
    </script>
    <?php
}