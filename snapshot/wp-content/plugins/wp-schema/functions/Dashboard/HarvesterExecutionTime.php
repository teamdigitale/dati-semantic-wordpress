<?php
// Shortcode: [DASHBOARD-TEMPO_ESECUZIONE_HARVESTER]
add_shortcode('DASHBOARD-TEMPO_ESECUZIONE_HARVESTER', 'wp_schema_print_tempo_esecuzione_harvester');

function wp_schema_print_tempo_esecuzione_harvester() {
    // API call
    $api_url = WP_SCHEMA_API_BASE_URL . 'dashboard/aggregated-time-data';
    $response = wp_remote_get($api_url);
    if (is_wp_error($response)) {
        echo "<p>Errore nella chiamata all'API: " . esc_html($response->get_error_message()) . "</p>";
        return;
    }
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    // Extract available years
    $anni = array_map(function($row) { return $row[0]; }, $data['rows']);
    rsort($anni); // Sort years in descending order
    $default_anno = $anni[0]; // Most recent year

    // Inline styles for the gray box
    ?>
    <style>
    .harvester-box {
        background: #e9eef2;
        border-radius: 6px;
        padding: 18px 32px 18px 32px;
        display: inline-block;
        min-width: 320px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        margin-top: 24px;
        vertical-align: top;
        text-align: center;
    }
    .harvester-box-title {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 12px;
        color: #222;
        text-align: center;
    }
    .harvester-metrics {
        display: flex;
        gap: 32px;
        justify-content: center;
        margin-top: 8px;
    }
    .harvester-metric {
        text-align: center;
    }
    .harvester-metric-value {
        font-size: 2.2em;
        font-weight: 700;
        color: #222;
        line-height: 1.1;
    }
    .harvester-metric-label {
        font-size: 0.95em;
        color: #2a4b6a;
        font-weight: 600;
        letter-spacing: 0.5px;
        margin-top: 2px;
    }
    .harvester-select {
        padding: 4px 10px;
        font-size: 15px;
        border-radius: 5px;
        border: 1px solid #bfc7d1;
        background: #fff;
        margin-left: 6px;
        min-width: 120px;
    }
    .harvester-select:focus {
        outline: 2px solid #0066cc;
        outline-offset: 2px;
    }
    .harvester-label {
        font-weight: 500;
        margin-right: 4px;
    }
    .harvester-filter-btn {
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
    .harvester-filter-btn:hover {
        background: #0043E3;
        color: #fff;
    }
    .harvester-filter-btn:focus {
        outline: 3px solid #0066cc;
        outline-offset: 2px;
    }
    /* Ensure sufficient color contrast */
    .harvester-metric-value {
        color: #000000;
    }
    .harvester-metric-label {
        color: #1a365d;
    }
    .harvester-controls {
        display: flex;
        align-items: center;
        gap: 16px;
        justify-content: center;
        margin-bottom: 12px;
        width: 100%;
    }
    </style>
    <div style="display:flex;flex-direction:column;align-items:center;" role="region" aria-label="Tempo esecuzione Harvester">
        <div class="harvester-controls">
            <select id="anno-harvester" class="harvester-select" aria-label="Seleziona anno">
                <?php foreach ($anni as $a): ?>
                    <option value="<?php echo esc_attr($a); ?>" <?php echo ($a === $default_anno) ? 'selected' : ''; ?>><?php echo esc_html($a); ?></option>
                <?php endforeach; ?>
            </select>
            <button class="harvester-filter-btn" id="harvester-filter-btn">Filtra</button>
        </div>
        <div class="harvester-box" id="harvester-box" role="region" aria-label="Metriche tempo esecuzione">
            <h2 class="harvester-box-title">Tempo esecuzione Harvester</h2>
            <div class="harvester-metrics" id="harvester-metrics" role="list" aria-label="Metriche statistiche">
                <!-- JS fills here -->
            </div>
        </div>
    </div>
    <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        var dataRows = <?php echo json_encode($data['rows']); ?>;
        var anni = <?php echo json_encode($anni); ?>;
        var box = document.getElementById('harvester-metrics');
        var selectAnno = document.getElementById('anno-harvester');
        var filterBtn = document.getElementById('harvester-filter-btn');

        function renderMetrics(anno) {
            var found = dataRows.find(function(row) { return row[0] == anno; });
            if (!found) {
                box.innerHTML = '<div role="alert">Nessun dato disponibile</div>';
                return;
            }
            var min = found[1];
            var max = found[5];
            var avg = found[6];
            box.innerHTML = `
                <div class="harvester-metric" role="listitem">
                    <div class="harvester-metric-value" aria-label="Media: ${parseInt(avg)} secondi">${parseInt(avg)}</div>
                    <div class="harvester-metric-label">AVG (s)</div>
                </div>
                <div class="harvester-metric" role="listitem">
                    <div class="harvester-metric-value" aria-label="Minimo: ${min} secondi">${min}</div>
                    <div class="harvester-metric-label">MIN (s)</div>
                </div>
                <div class="harvester-metric" role="listitem">
                    <div class="harvester-metric-value" aria-label="Massimo: ${max} secondi">${max}</div>
                    <div class="harvester-metric-label">MAX (s)</div>
                </div>
            `;
        }

        filterBtn.addEventListener('click', function() {
            renderMetrics(selectAnno.value);
        });

        // Initialize with the first year
        renderMetrics(selectAnno.value);
    });
    </script>
<?php
}

// Shortcode: [DASHBOARD-TEMPO_MEDIO_HARVESTING_ANNO]
add_shortcode('DASHBOARD-TEMPO_MEDIO_HARVESTING_ANNO', 'wp_schema_print_tempo_medio_harvesting_anno');

function wp_schema_print_tempo_medio_harvesting_anno() {
    // API call
    $api_url = WP_SCHEMA_API_BASE_URL . 'dashboard/aggregated-time-data';
    $response = wp_remote_get($api_url);
    if (is_wp_error($response)) {
        echo "<p>Errore nella chiamata all'API: " . esc_html($response->get_error_message()) . "</p>";
        return;
    }
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    // Prepare data for boxplot
    $anni = [];
    $boxData = [];
    if (isset($data['rows']) && is_array($data['rows'])) {
        foreach ($data['rows'] as $row) {
            // row: [year, min, p25, median, p75, max, average, count]
            $anno = $row[0];
            // Forziamo i valori a quelli della tabella (come nello specchietto)
            $min = isset($row[1]) ? floatval($row[1]) : 0;
            $p25 = isset($row[2]) ? floatval($row[2]) : 0;
            $median = isset($row[3]) ? floatval($row[3]) : 0;
            $p75 = isset($row[4]) ? floatval($row[4]) : 0;
            $max = isset($row[5]) ? floatval($row[5]) : 0;
            $anni[] = $anno;
            $boxData[] = [$min, $p25, $median, $p75, $max];
        }
    }
    ?>
    <style>
      .nav-tabs .nav-item { flex: 1 1 0; text-align: center; }
      .nav-tabs { display: flex; border-bottom: 2px solid #e5e9ec; }
      .nav-tabs .nav-link { 
          width: 100%; 
          border: none; 
          border-bottom: 2px solid transparent; 
          color: #1a365d; 
          font-size: 1.2rem; 
          font-weight: 600; 
          background: none; 
          padding: 12px;
      }
      .nav-tabs .nav-link:focus {
          outline: 2px solid #0066cc;
          outline-offset: 2px;
      }
      .nav-tabs .nav-link.active { 
          color: #0066cc; 
          border-bottom: 2px solid #0066cc; 
          background: none; 
      }
      .custom-table { 
          border-collapse: separate; 
          border-spacing: 0; 
          width: 100%; 
          font-size: 0.95rem; 
      }
      .custom-table th { 
          background: #e5e9ec; 
          color: #000000; 
          font-weight: 600; 
          border: none; 
          padding: 12px 8px; 
      }
      .custom-table td { 
          border: none; 
          padding: 12px 8px; 
      }
      .custom-table tbody tr:nth-child(even) { background: #f5f6f7; }
      .custom-table tbody tr:nth-child(odd) { background: #fff; }
      .custom-table tr { transition: background 0.2s; }
      .custom-table thead tr { border-bottom: 2px solid #e5e9ec; }
      .custom-table caption {
          caption-side: top;
          text-align: left;
          font-weight: 600;
          margin-bottom: 8px;
      }
    </style>
    <div class="it-tabs it-tabs-primary" style="margin-bottom:40px;" role="tablist">
      <ul class="nav nav-tabs" id="harvesterTimeTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="harvester-time-grafico-tab" data-bs-toggle="tab" data-bs-target="#harvester-time-grafico" type="button" role="tab" aria-controls="harvester-time-grafico" aria-selected="true">Grafico</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="harvester-time-tabella-tab" data-bs-toggle="tab" data-bs-target="#harvester-time-tabella" type="button" role="tab" aria-controls="harvester-time-tabella" aria-selected="false">Tabella</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="harvester-time-info-tab" data-bs-toggle="tab" data-bs-target="#harvester-time-info" type="button" role="tab" aria-controls="harvester-time-info" aria-selected="false">Info</button>
        </li>
      </ul>
      <div class="tab-content" id="harvesterTimeTabsContent" style="background:#fff; border-radius:8px; box-shadow:0 1px 4px rgba(0,0,0,0.04); padding: 24px 12px;">
        <div class="tab-pane fade show active" id="harvester-time-grafico" role="tabpanel" aria-labelledby="harvester-time-grafico-tab">
          <h2 style="font-size:20px;font-weight:600;margin-bottom:10px;">Tempo medio harvesting per anno</h2>
          <div id="chart-tempo-medio-harvesting-anno" style="width:100%;height:380px;background:#fff;border-radius:8px;" role="img" aria-label="Grafico del tempo medio di harvesting per anno"></div>
        </div>
        <div class="tab-pane fade" id="harvester-time-tabella" role="tabpanel" aria-labelledby="harvester-time-tabella-tab">
          <h2 style="font-size:20px;font-weight:600;margin-bottom:10px;">Tabella dati</h2>
          <div class="table-responsive">
            <table class="custom-table" id="harvester-time-table" aria-label="Dati statistici del tempo di harvesting per anno">
              <caption>Dati statistici del tempo di harvesting per anno</caption>
              <thead>
                <tr>
                  <th scope="col">Anno</th>
                  <th scope="col">Min (s)</th>
                  <th scope="col">P25 (s)</th>
                  <th scope="col">Mediana (s)</th>
                  <th scope="col">P75 (s)</th>
                  <th scope="col">Max (s)</th>
                </tr>
              </thead>
              <tbody>
                <!-- Popolato da JS -->
              </tbody>
            </table>
          </div>
        </div>
        <div class="tab-pane fade" id="harvester-time-info" role="tabpanel" aria-labelledby="harvester-time-info-tab">
          <p><strong>Cosa mostra questa dashboard</strong><br>
          Questa dashboard visualizza quanto tempo impiega il sistema Harvester a raccogliere e integrare dati dai repository GitHub, anno per anno. Puoi vedere i valori minimi, massimi, medi e la distribuzione statistica dei tempi di esecuzione.</p>

          <p><strong>Come funziona l'Harvesting</strong><br>
          L'Harvester controlla se ci sono novità nei repository GitHub. Se trova dei cambiamenti, scarica i dati aggiornati, li analizza e aggiorna le informazioni nei database.</p>

          <p><strong>Cosa significano le metriche</strong><br>
          <b>Min</b>: tempo minimo di esecuzione registrato<br>
          <b>P25</b>: primo quartile (25° percentile)<br>
          <b>Mediana</b>: valore centrale della distribuzione<br>
          <b>P75</b>: terzo quartile (75° percentile)<br>
          <b>Max</b>: tempo massimo di esecuzione registrato
          </p>

          <p><strong>Da cosa dipendono le tempistiche</strong><br>
          Il tempo necessario per completare l'harvesting può cambiare in base alla quantità di dati da analizzare, al numero di modifiche trovate e alle prestazioni del server. Il sistema è progettato per essere efficiente e veloce, evitando operazioni superflue.</p>
        </div>
      </div>
    </div>
    <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        var anni = <?php echo json_encode($anni); ?>;
        var boxData = <?php echo json_encode($boxData); ?>;
        // DEBUG: Controllo dati passati al grafico
        console.log('anni:', anni);
        console.log('boxData:', boxData);
        var chartDom = document.getElementById('chart-tempo-medio-harvesting-anno');
        var myChart = echarts.init(chartDom);
        var option = {
            backgroundColor: '#fff',
            title: { show: false },
            tooltip: {
                trigger: 'item',
                formatter: function(param) {
                    return [
                        '<b>Anno: ' + anni[param.dataIndex] + '</b>',
                        'Min: ' + param.data[0] + ' s',
                        'P25: ' + param.data[1] + ' s',
                        'Mediana: ' + param.data[2] + ' s',
                        'P75: ' + param.data[3] + ' s',
                        'Max: ' + param.data[4] + ' s'
                    ].join('<br>');
                }
            },
            grid: { left: 60, right: 30, top: 40, bottom: 40 },
            xAxis: {
                type: 'category',
                data: anni,
                name: 'Anno',
                nameLocation: 'middle',
                nameGap: 30,
                axisLabel: { fontSize: 15 },
                axisLine: { lineStyle: { color: '#000000' } }
            },
            yAxis: {
                type: 'value',
                name: 'Tempo (s)',
                nameLocation: 'middle', // o 'center' se preferisci
                nameGap: 45,            // puoi aumentare se serve
                nameRotate: 90,         // verticale
                nameTextStyle: {
                    fontSize: 12,
                    fontWeight: 'normal',
                    color: '#1a365d'
                },
                axisLabel: { fontSize: 15 },
                axisLine: { lineStyle: { color: '#000000' } }
            },
            series: [
                {
                    name: 'Tempo medio harvesting',
                    type: 'boxplot',
                    data: boxData,
                    itemStyle: {
                        color: 'rgba(0, 102, 204, 0.25)',
                        borderColor: '#0066cc',
                        borderWidth: 1
                    },
                    boxWidth: [40, 60],
                    lineStyle: {
                        width: 1,
                        color: '#0066cc'
                    },
                    tooltip: {
                        formatter: function(param) {
                            // Usa le etichette della tabella
                            return [
                                '<b>Anno: ' + anni[param.dataIndex] + '</b>',
                                'Min: ' + boxData[param.dataIndex][0] + ' s',
                                'P25: ' + boxData[param.dataIndex][1] + ' s',
                                'Mediana: ' + boxData[param.dataIndex][2] + ' s',
                                'P75: ' + boxData[param.dataIndex][3] + ' s',
                                'Max: ' + boxData[param.dataIndex][4] + ' s'
                            ].join('<br>');
                        }
                    }
                }
            ]
        };
        myChart.setOption(option);

        // Popola la tabella
        var tbody = document.querySelector('#harvester-time-table tbody');
        tbody.innerHTML = '';
        anni.forEach(function(anno, index) {
            var data = boxData[index];
            var tr = document.createElement('tr');
            tr.innerHTML = '<th scope="row">' + anno + '</th>' +
                          '<td>' + data[0] + '</td>' +
                          '<td>' + data[1] + '</td>' +
                          '<td>' + data[2] + '</td>' +
                          '<td>' + data[3] + '</td>' +
                          '<td>' + data[4] + '</td>';
            tbody.appendChild(tr);
        });

        window.addEventListener('resize', function() { myChart.resize(); });
    });
    </script>
    <?php
}

// Shortcode: [DASHBOARD-TEMPO_TOTALE_HARVESTER]
add_shortcode('DASHBOARD-TEMPO_TOTALE_HARVESTER', 'wp_schema_print_tempo_totale_harvester');

function wp_schema_print_tempo_totale_harvester() {
    // Static cross filter
    $cross_filters = ['data'];
    $selected_filter = $cross_filters[0];

    // API call
    $api_url = WP_SCHEMA_API_BASE_URL . 'dashboard/aggregated-time-data?dimension=REPOSITORY_URL';
    $response = wp_remote_get($api_url);
    if (is_wp_error($response)) {
        echo "<p>Errore nella chiamata all'API: " . esc_html($response->get_error_message()) . "</p>";
        return;
    }
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    // Find all available years
    $anni = [];
    if (isset($data['rows']) && is_array($data['rows'])) {
        foreach ($data['rows'] as $row) {
            if (!in_array($row[0], $anni)) {
                $anni[] = $row[0];
            }
        }
    }
    rsort($anni); // Sort years in descending order
    $default_anno = $anni[0] ?? ''; // Most recent year

    ?>
    <style>
    .harvester-box-totale {
        background: #e9eef2;
        border-radius: 6px;
        padding: 18px 32px 18px 32px;
        display: inline-block;
        min-width: 320px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        margin-left: 24px;
        vertical-align: top;
    }
    .harvester-box-title-totale {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 12px;
        color: #000000;
    }
    .harvester-metric-value-totale {
        font-size: 2.6em;
        font-weight: 700;
        color: #000000;
        line-height: 1.1;
        text-align: center;
    }
    .harvester-metric-label-totale {
        font-size: 1em;
        color: #1a365d;
        font-weight: 600;
        letter-spacing: 0.5px;
        margin-top: 2px;
        text-align: center;
    }
    .harvester-select-totale {
        padding: 4px 10px;
        font-size: 15px;
        border-radius: 5px;
        border: 1px solid #bfc7d1;
        background: #fff;
        margin-left: 6px;
        min-width: 120px;
    }
    .harvester-select-totale:focus {
        outline: 2px solid #0066cc;
        outline-offset: 2px;
    }
    .harvester-label-totale {
        font-weight: 500;
        margin-right: 4px;
    }
    </style>
    <div style="display:flex;align-items:flex-start;gap:24px;" role="region" aria-label="Tempo totale esecuzione Harvester">
        <div>
            <select id="anno-totale-harvester" class="harvester-select-totale" aria-label="Seleziona anno">
                <?php foreach ($anni as $a): ?>
                    <option value="<?php echo esc_attr($a); ?>" <?php echo ($a === $default_anno) ? 'selected' : ''; ?>><?php echo esc_html($a); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="harvester-box-totale" id="harvester-box-totale" role="region" aria-label="Metrica tempo totale">
            <h2 class="harvester-box-title-totale">Tempo esecuzione Harvester</h2>
            <div class="harvester-metric-value-totale" id="harvester-metric-value-totale" role="status" aria-live="polite"></div>
            <div class="harvester-metric-label-totale">TOTALE (s)</div>
        </div>
    </div>
    <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        var dataRows = <?php echo json_encode($data['rows']); ?>;
        var selectAnno = document.getElementById('anno-totale-harvester');
        var valueBox = document.getElementById('harvester-metric-value-totale');

        function renderTotale(anno) {
            var totale = 0;
            dataRows.forEach(function(row) {
                if (row[0] == anno) {
                    var avg = parseFloat(row[4]);
                    var count = parseInt(row[5]);
                    totale += avg * count;
                }
            });
            valueBox.textContent = Math.round(totale);
            valueBox.setAttribute('aria-label', 'Tempo totale: ' + Math.round(totale) + ' secondi');
        }

        selectAnno.addEventListener('change', function() {
            renderTotale(this.value);
        });

        // Initialize with the first year
        renderTotale(selectAnno.value);
    });
    </script>
<?php
}