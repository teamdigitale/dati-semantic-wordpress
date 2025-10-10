<?php

// Register shortcode
add_shortcode('DASHBOARD-TITOLARI_RISORSE', 'wp_schema_print_right_holder_distribution_chart');

function wp_schema_print_right_holder_distribution_chart() {
    // API call
    $api_url = WP_SCHEMA_API_BASE_URL . 'dashboard/aggregated-count-data?dimension=RIGHT_HOLDER,RESOURCE_TYPE&date=2025';
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

    // Prepare data for each series (fill with 0 if missing)
    $ontologie = [];
    $vocabolari = [];
    $schemi = [];
    foreach ($holders as $holder) {
        $ontologie[] = $series_data['Ontologie'][$holder] ?? 0;
        $vocabolari[] = $series_data['Vocabolari'][$holder] ?? 0;
        $schemi[] = $series_data['Schemi dati'][$holder] ?? 0;
    }

    // Consistent colors with the reference
    $colori = [
        'Ontologie' => '#0043E3', // blue
        'Vocabolari' => '#077F7B', // green
        'Schemi dati' => '#EBA704', // orange
    ];

    // Calculate dynamic height based on number of holders
    $height = max(400, count($holders) * 48);

    // Print the container and ECharts script
    ?>
    <div style="margin-bottom:40px;">
        <div style="font-size:20px;font-weight:600;margin-bottom:10px;">Distribuzione delle risorse tra i Titolari</div>
        <div id="chart-titolari" style="width: 100%; height: <?php echo $height; ?>px; margin:auto; background:#fff; border-radius:8px; box-shadow:0 1px 4px rgba(0,0,0,0.04);"></div>
    </div>
    <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        var chartDom = document.getElementById('chart-titolari');
        var myChart = echarts.init(chartDom);
        var option = {
            color: ['<?php echo $colori['Ontologie']; ?>', '<?php echo $colori['Vocabolari']; ?>', '<?php echo $colori['Schemi dati']; ?>'],
            title: { show: false },
            tooltip: { trigger: 'axis', axisPointer: { type: 'shadow' } },
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
                axisLabel: { fontSize: 15 }
            },
            yAxis: {
                type: 'category',
                data: <?php echo json_encode($holders); ?>,
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
                    stack: 'totale',
                    data: <?php echo json_encode($ontologie); ?>,
                    barWidth: 32
                },
                {
                    name: 'Vocabolari',
                    type: 'bar',
                    stack: 'totale',
                    data: <?php echo json_encode($vocabolari); ?>,
                    barWidth: 32
                },
                {
                    name: 'Schemi dati',
                    type: 'bar',
                    stack: 'totale',
                    data: <?php echo json_encode($schemi); ?>,
                    barWidth: 32
                }
            ]
        };
        myChart.setOption(option);
        window.addEventListener('resize', function() { myChart.resize(); });
    });
    </script>
    <?php
}
