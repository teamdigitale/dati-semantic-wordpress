<?php

// Register shortcode
add_shortcode('DASHBOARD-EVOLUZIONE_RISORSE_STACKED', 'wp_schema_print_resource_trend_stacked_chart');

function wp_schema_print_resource_trend_stacked_chart() {
    // API call for yearly data
    $api_url = WP_SCHEMA_API_BASE_URL . 'dashboard/aggregated-count-data?dimension=RESOURCE_TYPE';
    $response = wp_remote_get($api_url);
    if (is_wp_error($response)) {
        echo "<p>Errore nella chiamata all'API: " . esc_html($response->get_error_message()) . "</p>";
        return;
    }
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    // Prepare data for the chart
    $x_labels = [];
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
            $period = $row[0];
            $type = strtolower($row[1]);
            $count = intval($row[2]);
            if (!in_array($period, $x_labels)) {
                $x_labels[] = $period;
            }
            if (isset($type_map[$type])) {
                $series_data[$type_map[$type]][$period] = $count;
            }
        }
    }
    sort($x_labels);

    $ontologie = [];
    $vocabolari = [];
    $schemi = [];
    foreach ($x_labels as $label) {
        $ontologie[] = $series_data['Ontologie'][$label] ?? 0;
        $vocabolari[] = $series_data['Vocabolari'][$label] ?? 0;
        $schemi[] = $series_data['Schemi dati'][$label] ?? 0;
    }

    // Consistent colors with the original chart
    $colori = [
        'Ontologie' => '#0043E3', // blue
        'Vocabolari' => '#077F7B', // green
        'Schemi dati' => '#EBA704', // orange
    ];

    // Print the container and ECharts script
    ?>
    <style>
      /* Tab distribuiti in larghezza */
      .nav-tabs .nav-item { flex: 1 1 0; text-align: center; }
      .nav-tabs { display: flex; border-bottom: 2px solid #e5e9ec; }
      .nav-tabs .nav-link { 
        width: 100%; 
        border: none; 
        border-bottom: 2px solid transparent; 
        color: #2c3e50;
        font-size: 1.2rem; 
        font-weight: 600; 
        background: none; 
        padding: 12px 8px;
      }
      .nav-tabs .nav-link.active { 
        color: #0043E3;
        border-bottom: 2px solid #0043E3; 
        background: none; 
      }
      .nav-tabs .nav-link:focus {
        outline: 2px solid #0043E3;
        outline-offset: 2px;
      }
      /* Tabella stile reference */
      .custom-table { 
        border-collapse: separate; 
        border-spacing: 0; 
        width: 100%; 
        font-size: 0.95rem; 
      }
      .custom-table th { 
        background: #e5e9ec; 
        color: #2c3e50; 
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
      /* Spazio extra per la leggenda sotto il grafico */
      #chart-anni-stacked { margin-bottom: 60px !important; }
      /* Font più piccolo per la leggenda ECharts */
      .echarts-legend, .echarts-legend-item, .echarts-legend-text { font-size: 13px !important; }
      /* Responsive: più spazio su mobile */
      @media (max-width: 600px) {
        #chart-anni-stacked { margin-bottom: 110px !important; }
        .custom-table { font-size: 0.85rem; }
      }
      /* Focus styles */
      *:focus {
        outline: 2px solid #0043E3;
        outline-offset: 2px;
      }
      /* Skip link for keyboard users */
      .skip-link {
        position: absolute;
        top: -40px;
        left: 0;
        background: #0043E3;
        color: white;
        padding: 8px;
        z-index: 100;
      }
      .skip-link:focus {
        top: 0;
      }
    </style>
    <a href="#resource-evolution-grafico-stacked" class="skip-link">Vai al contenuto principale</a>
    <div class="it-tabs it-tabs-primary" style="margin-bottom:40px;">
      <ul class="nav nav-tabs" id="resourceEvolutionTabsStacked" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" 
                  id="resource-evolution-grafico-stacked-tab" 
                  data-bs-toggle="tab" 
                  data-bs-target="#resource-evolution-grafico-stacked" 
                  type="button" 
                  role="tab" 
                  aria-controls="resource-evolution-grafico-stacked" 
                  aria-selected="true"
                  aria-label="Visualizza il grafico dell'evoluzione delle risorse">Grafico</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" 
                  id="resource-evolution-tabella-stacked-tab" 
                  data-bs-toggle="tab" 
                  data-bs-target="#resource-evolution-tabella-stacked" 
                  type="button" 
                  role="tab" 
                  aria-controls="resource-evolution-tabella-stacked" 
                  aria-selected="false"
                  aria-label="Visualizza la tabella dei dati">Tabella</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" 
                  id="resource-evolution-info-stacked-tab" 
                  data-bs-toggle="tab" 
                  data-bs-target="#resource-evolution-info-stacked" 
                  type="button" 
                  role="tab" 
                  aria-controls="resource-evolution-info-stacked" 
                  aria-selected="false"
                  aria-label="Visualizza le informazioni sulla dashboard">Info</button>
        </li>
      </ul>
      <div class="tab-content" id="resourceEvolutionTabsContentStacked" style="background:#fff; border-radius:8px; box-shadow:0 1px 4px rgba(0,0,0,0.04); padding: 24px 12px;">
        <div class="tab-pane fade show active" 
             id="resource-evolution-grafico-stacked" 
             role="tabpanel" 
             aria-labelledby="resource-evolution-grafico-stacked-tab">
          <div style="font-size:20px;font-weight:600;margin-bottom:10px;">Evoluzione delle risorse (Grafico a barre impilate)</div>
          <div id="chart-anni-stacked" 
               style="width: 100%; height: 350px; margin:auto;"
               role="img"
               aria-label="Grafico a barre impilate che mostra l'evoluzione delle risorse semantiche nel tempo, suddivise per tipologia (Ontologie, Vocabolari, Schemi dati)"></div>
        </div>
        <div class="tab-pane fade" 
             id="resource-evolution-tabella-stacked" 
             role="tabpanel" 
             aria-labelledby="resource-evolution-tabella-stacked-tab">
          <div style="font-size:20px;font-weight:600;margin-bottom:10px;">Tabella dati</div>
          <div class="table-responsive">
            <table class="custom-table" aria-label="Tabella dei dati sull'evoluzione delle risorse semantiche">
              <thead>
                <tr>
                  <th scope="col">Anno</th>
                  <th scope="col">Ontologie</th>
                  <th scope="col">Vocabolari</th>
                  <th scope="col">Schemi dati</th>
                  <th scope="col">Totale</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($x_labels as $i => $anno): ?>
                  <tr>
                    <th scope="row"><?php echo esc_html($anno); ?></th>
                    <td><?php echo esc_html($ontologie[$i]); ?></td>
                    <td><?php echo esc_html($vocabolari[$i]); ?></td>
                    <td><?php echo esc_html($schemi[$i]); ?></td>
                    <td><?php echo esc_html($ontologie[$i] + $vocabolari[$i] + $schemi[$i]); ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="tab-pane fade" 
             id="resource-evolution-info-stacked" 
             role="tabpanel" 
             aria-labelledby="resource-evolution-info-stacked-tab">
          <div role="region" aria-label="Informazioni sulla dashboard">
            <p><strong>DESCRIZIONE GENERALE</strong></p>
            <p>Questa dashboard mostra l'evoluzione cumulativa delle risorse semantiche nel tempo, suddivise per tipologia (Ontologie, Vocabolari, Schemi dati).</p>

            <p><strong>VISUALIZZAZIONE</strong></p>
            <p>Il grafico presenta:</p>
            <ul>
              <li>Un grafico a barre impilate che mostra l'andamento temporale delle risorse</li>
              <li>Tre serie di dati, una per ogni tipologia di risorsa, con colori distintivi:
                <ul>
                  <li>Ontologie: blu</li>
                  <li>Vocabolari: verde</li>
                  <li>Schemi dati: arancione</li>
                </ul>
              </li>
              <li>L'asse X mostra gli anni</li>
              <li>L'asse Y mostra il numero cumulativo di risorse</li>
            </ul>

            <p><strong>FUNZIONALITÀ</strong></p>
            <ul>
              <li>Interattività: è possibile interagire con il grafico per visualizzare i dettagli specifici di ogni barra</li>
              <li>Legenda: permette di attivare/disattivare la visualizzazione di singole tipologie di risorse</li>
              <li>Tooltip: al passaggio del mouse mostra i valori dettagliati per ogni anno, incluso il totale</li>
              <li>Visualizzazione tabellare: mostra i dati in formato numerico per una consultazione precisa</li>
            </ul>

            <p><strong>INTERPRETAZIONE</strong></p>
            <p>Il grafico permette di:</p>
            <ul>
              <li>Analizzare la crescita complessiva delle risorse semantiche nel tempo</li>
              <li>Confrontare l'evoluzione delle diverse tipologie di risorse</li>
              <li>Identificare periodi di particolare crescita o stasi</li>
              <li>Valutare la distribuzione delle risorse tra le diverse tipologie</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        var chartDom = document.getElementById('chart-anni-stacked');
        var myChart = echarts.init(chartDom);
        var option = {
            color: ['<?php echo $colori['Ontologie']; ?>', '<?php echo $colori['Vocabolari']; ?>', '<?php echo $colori['Schemi dati']; ?>'],
            title: { show: false },
            tooltip: { 
                trigger: 'axis',
                axisPointer: {
                    type: 'shadow'
                },
                formatter: function(params) {
                    var total = 0;
                    var result = params[0].axisValue + '<br/>';
                    params.forEach(function(param) {
                        result += param.marker + ' ' + param.seriesName + ': ' + param.value + '<br/>';
                        total += param.value;
                    });
                    result += '<br/><strong>Totale: ' + total + '</strong>';
                    return result;
                }
            },
            legend: {
                data: ['Ontologie', 'Vocabolari', 'Schemi dati'],
                bottom: 0,
                left: 60,
                itemWidth: 8,
                itemHeight: 8,
                textStyle: { fontWeight: 600, fontSize: 13 },
                icon: 'circle'
            },
            grid: {
                left: 60, right: 30, top: 40, bottom: 60
            },
            xAxis: {
                type: 'category',
                data: <?php echo json_encode($x_labels); ?>,
                name: 'Anno',
                nameLocation: 'middle',
                nameGap: 20,
                axisLine: { lineStyle: { color: '#2c3e50' } },
                axisLabel: { fontSize: 14 },
                splitLine: {
                    show: true,
                    lineStyle: {
                        color: '#e5e9ec',
                        type: 'solid'
                    }
                }
            },
            yAxis: {
                type: 'value',
                name: 'Numero risorse',
                nameLocation: 'middle',
                nameGap: 50,
                min: 0,
                axisLine: { lineStyle: { color: '#2c3e50' } },
                axisLabel: { fontSize: 14 }
            },
            series: [
                {
                    name: 'Ontologie',
                    type: 'bar',
                    stack: 'Total',
                    data: <?php echo json_encode($ontologie); ?>,
                    emphasis: {
                        focus: 'series'
                    }
                },
                {
                    name: 'Vocabolari',
                    type: 'bar',
                    stack: 'Total',
                    data: <?php echo json_encode($vocabolari); ?>,
                    emphasis: {
                        focus: 'series'
                    }
                },
                {
                    name: 'Schemi dati',
                    type: 'bar',
                    stack: 'Total',
                    data: <?php echo json_encode($schemi); ?>,
                    emphasis: {
                        focus: 'series'
                    }
                }
            ]
        };
        myChart.setOption(option);
        window.addEventListener('resize', function() { myChart.resize(); });

        // Keyboard navigation for the chart
        chartDom.addEventListener('keydown', function(e) {
            var currentIndex = x_labels.indexOf(myChart.getOption().xAxis[0].data[0]);
            switch(e.key) {
                case 'ArrowLeft':
                    if (currentIndex > 0) {
                        myChart.dispatchAction({
                            type: 'showTip',
                            seriesIndex: 0,
                            dataIndex: currentIndex - 1
                        });
                    }
                    break;
                case 'ArrowRight':
                    if (currentIndex < x_labels.length - 1) {
                        myChart.dispatchAction({
                            type: 'showTip',
                            seriesIndex: 0,
                            dataIndex: currentIndex + 1
                        });
                    }
                    break;
            }
        });
    });
    </script>
    <?php
}