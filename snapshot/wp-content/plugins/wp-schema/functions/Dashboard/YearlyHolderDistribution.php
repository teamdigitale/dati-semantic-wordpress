<?php 

// Register shortcode
add_shortcode('DASHBOARD-TITOLARI_RISORSE_ANNO', 'wp_schema_print_yearly_distribution_chart');

// Map month numbers to Italian names
$mesi_ita = [
    '01' => 'Gennaio', '02' => 'Febbraio', '03' => 'Marzo', '04' => 'Aprile',
    '05' => 'Maggio', '06' => 'Giugno', '07' => 'Luglio', '08' => 'Agosto',
    '09' => 'Settembre', '10' => 'Ottobre', '11' => 'Novembre', '12' => 'Dicembre'
];

if (!function_exists('format_mese_ita')) {
    // Format month in Italian
    function format_mese_ita($m) {
        $mesi_ita = [
            '01' => 'Gennaio', '02' => 'Febbraio', '03' => 'Marzo', '04' => 'Aprile',
            '05' => 'Maggio', '06' => 'Giugno', '07' => 'Luglio', '08' => 'Agosto',
            '09' => 'Settembre', '10' => 'Ottobre', '11' => 'Novembre', '12' => 'Dicembre'
        ];
        
        if (empty($m)) {
            error_log('Empty month passed to format_mese_ita');
            return '';
        }
        
        $parts = explode('-', $m);
        if (count($parts) !== 2 || !isset($mesi_ita[$parts[0]])) {
            error_log('Invalid month format: ' . $m);
            return $m;
        }
        
        return $mesi_ita[$parts[0]] . ' ' . $parts[1];
    }
}

if (!function_exists('wp_schema_print_weekly_distribution_chart')) {
    function wp_schema_print_weekly_distribution_chart() {
        // API call: get all available data (no month filter)
        $api_url = WP_SCHEMA_API_BASE_URL . 'dashboard/aggregated-count-data?dimension=RIGHT_HOLDER,RESOURCE_TYPE&granularity=WEEKS_IN_MONTH&date=03-2025';
        $response = wp_remote_get($api_url);
        if (is_wp_error($response)) {
            echo "<p>Errore nella chiamata all'API: " . esc_html($response->get_error_message()) . "</p>";
            return;
        }
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        // Parse data to extract months, holders, and weeks
        $months = [];
        $holders = [];
        $weeks = [];
        $raw = [];

        $type_map = [
            'ontology' => 'Ontologie',
            'controlled vocabulary' => 'Vocabolari',
            'schema' => 'Schemi dati',
        ];

        if (isset($data['rows']) && is_array($data['rows'])) {
            foreach ($data['rows'] as $row) {
                // Check that the row is a valid array with all required fields
                if (!is_array($row) || count($row) < 4 || 
                    !isset($row[0]) || !isset($row[1]) || !isset($row[2]) || !isset($row[3])) {
                    error_log('Invalid row: ' . print_r($row, true));
                    continue;
                }

                // Extract and validate data
                $week = trim($row[0]);
                $holder = trim($row[1]);
                $type = strtolower(trim($row[2]));
                $count = intval($row[3]);

                // Check that the week has the correct format
                if (!preg_match('/^W\d+-\d{2}-\d{4}$/', $week)) {
                    error_log('Invalid week format: ' . $week);
                    continue;
                }

                // Extract month from week
                $month = substr($week, 3, 2) . '-' . substr($week, 6, 4);

                // Check that the type is valid
                if (!isset($type_map[$type])) {
                    error_log('Invalid type: ' . $type);
                    continue;
                }

                // Add data only if all are valid
                if (!in_array($month, $months)) $months[] = $month;
                if (!in_array($holder, $holders)) $holders[] = $holder;
                if (!in_array($week, $weeks)) $weeks[] = $week;

                $raw[] = [
                    'month' => $month,
                    'holder' => $holder,
                    'week' => $week,
                    'type' => $type_map[$type],
                    'count' => $count
                ];
            }
        }
        // Sort months from most recent to oldest
        usort($months, function($a, $b) {
            $ad = DateTime::createFromFormat('m-Y', $a);
            $bd = DateTime::createFromFormat('m-Y', $b);
            return $bd <=> $ad;
        });
        sort($holders);
        sort($weeks);

        // Consistent colors with the reference
        $colori = [
            'Ontologie' => '#0043E3', // blue
            'Vocabolari' => '#077F7B', // green
            'Schemi dati' => '#EBA704', // orange
        ];

        // Print selects and chart container
        ?>
        <div style="margin-bottom:40px;">
            <div style="font-size:20px;font-weight:600;margin-bottom:10px;">Distribuzione delle risorse tra i Titolari</div>
            <div style="display:flex;gap:32px;margin-bottom:20px;">
                <div>
                    <label for="mese-select" style="font-weight:500;">Mese</label><br>
                    <select id="mese-select" style="padding:6px 12px;font-size:15px;">
                        <?php foreach ($months as $m): ?>
                            <option value="<?php echo esc_attr($m); ?>"><?php echo esc_html(format_mese_ita($m)); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="holder-select" style="font-weight:500;">Titolare</label><br>
                    <select id="holder-select" style="padding:6px 12px;font-size:15px;">
                        <?php foreach ($holders as $h): ?>
                            <option value="<?php echo esc_attr($h); ?>"><?php echo esc_html($h); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div id="chart-settimane" style="width: 100%; max-width: 900px; height: 400px; margin:auto; background:#fff; border-radius:8px; box-shadow:0 1px 4px rgba(0,0,0,0.04);"></div>
        </div>
        <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            var rawData = <?php echo json_encode($raw); ?>;
            var weeksAll = <?php echo json_encode($weeks); ?>;
            var colori = <?php echo json_encode($colori); ?>;
            var chartDom = document.getElementById('chart-settimane');
            var myChart = echarts.init(chartDom);

            function updateChart() {
                var mese = document.getElementById('mese-select').value;
                var holder = document.getElementById('holder-select').value;
                
                // Filter data by month and holder
                var filtered = rawData.filter(function(row) {
                    return row.month === mese && row.holder === holder;
                });

                // Prepare data for selected weeks
                var settimane = [];
                var ontologie = [];
                var vocabolari = [];
                var schemi = [];
                
                // Get weeks for the selected month
                var settimaneDelMese = weeksAll.filter(function(week) {
                    return (week.substr(3, 2) + '-' + week.substr(6, 4)) === mese;
                });

                // If there are no weeks for this month, show a message
                if (settimaneDelMese.length === 0) {
                    myChart.setOption({
                        title: {
                            text: 'Nessun dato disponibile per il periodo selezionato',
                            left: 'center',
                            top: 'center',
                            textStyle: {
                                fontSize: 16,
                                color: '#666'
                            }
                        },
                        series: []
                    });
                    return;
                }

                settimaneDelMese.forEach(function(week) {
                    settimane.push(week.replace(/W(\d+)-(\d+)-(\d+)/, '$1ª sett.'));
                    var foundOnt = filtered.find(r => r.week === week && r.type === 'Ontologie');
                    var foundVoc = filtered.find(r => r.week === week && r.type === 'Vocabolari');
                    var foundSch = filtered.find(r => r.week === week && r.type === 'Schemi dati');
                    ontologie.push(foundOnt ? foundOnt.count : 0);
                    vocabolari.push(foundVoc ? foundVoc.count : 0);
                    schemi.push(foundSch ? foundSch.count : 0);
                });

                var option = {
                    title: { show: false },
                    tooltip: { trigger: 'axis', axisPointer: { type: 'shadow' } },
                    legend: {
                        data: ['Ontologie', 'Vocabolari', 'Schemi dati'],
                        bottom: 10,
                        left: 'center',
                        itemWidth: 16,
                        itemHeight: 8,
                        textStyle: { fontWeight: 600, fontSize: 15 }
                    },
                    grid: {
                        left: 60, right: 30, top: 40, bottom: 80
                    },
                    xAxis: {
                        type: 'category',
                        data: settimane,
                        name: 'Settimane',
                        nameLocation: 'middle',
                        nameGap: 35,
                        axisLine: { lineStyle: { color: '#bbb' } },
                        axisLabel: { fontSize: 15 }
                    },
                    yAxis: {
                        type: 'value',
                        name: 'Numero risorse',
                        nameLocation: 'middle',
                        nameGap: 50,
                        min: 0,
                        axisLine: { lineStyle: { color: '#bbb' } },
                        axisLabel: { fontSize: 15 }
                    },
                    series: [
                        {
                            name: 'Ontologie',
                            type: 'bar',
                            data: ontologie,
                            barWidth: 32,
                            color: colori['Ontologie']
                        },
                        {
                            name: 'Vocabolari',
                            type: 'bar',
                            data: vocabolari,
                            barWidth: 32,
                            color: colori['Vocabolari']
                        },
                        {
                            name: 'Schemi dati',
                            type: 'bar',
                            data: schemi,
                            barWidth: 32,
                            color: colori['Schemi dati']
                        }
                    ]
                };
                myChart.clear();
                myChart.setOption(option, true);
                myChart.resize();
            }

            document.getElementById('mese-select').addEventListener('change', updateChart);
            document.getElementById('holder-select').addEventListener('change', updateChart);

            updateChart(); // initialize
            window.addEventListener('resize', function() { myChart.resize(); });
        });
        </script>
        <?php
    }
}

function wp_schema_print_yearly_distribution_chart() {
    // API call: get all available data (no month filter)
    $api_url = WP_SCHEMA_API_BASE_URL . 'dashboard/aggregated-count-data?dimension=RIGHT_HOLDER,RESOURCE_TYPE&granularity=MONTHS&year=2025';
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

    // Parse data to extract years, holders, and months
    $anni = [];
    $holders = [];
    $raw = [];
    $mesi = ['01','02','03','04','05','06','07','08','09','10','11','12'];
    $mesi_iniziali = ['G','F','M','A','M','G','L','A','S','O','N','D'];

    $type_map = [
        'ontology' => 'Ontologie',
        'controlled vocabulary' => 'Vocabolari',
        'schema' => 'Schemi dati',
    ];

    if (isset($data['rows']) && is_array($data['rows'])) {
        foreach ($data['rows'] as $row) {
            if (!is_array($row) || count($row) < 4 || 
                !isset($row[0]) || !isset($row[1]) || !isset($row[2]) || !isset($row[3])) {
                error_log('Invalid row: ' . print_r($row, true));
                continue;
            }
            $month = trim($row[0]); // format MM-YYYY
            $holder = trim($row[1]);
            $type = strtolower(trim($row[2]));
            $count = intval($row[3]);
            $parts = explode('-', $month);
            if (count($parts) !== 2) continue;
            $anno = $parts[1];
            // Use Italian name if available, otherwise use identifier
            $holder_name = isset($rights_holders_data[$holder]) ? $rights_holders_data[$holder] : $holder;
            if (!in_array($anno, $anni)) $anni[] = $anno;
            if (!in_array($holder_name, $holders)) $holders[] = $holder_name;
            $raw[] = [
                'anno' => $anno,
                'mese' => $parts[0],
                'holder' => $holder_name,
                'type' => $type_map[$type] ?? $type,
                'count' => $count
            ];
        }
    }
    // Sort years from most recent
    rsort($anni);
    sort($holders);

    // Consistent colors with the reference
    $colori = [
        'Ontologie' => '#0043E3', // blue
        'Vocabolari' => '#077F7B', // green
        'Schemi dati' => '#EBA704', // orange
    ];

    ?>
    <style>
    .dashboard-select-custom {
        border: 1px solid #C5C7CD;
        border-radius: 6px;
        padding: 8px 32px 8px 12px;
        font-size: 15px;
        background: #fff url('data:image/svg+xml;utf8,<svg fill="%23666" height="20" viewBox="0 0 20 20" width="20" xmlns="http://www.w3.org/2000/svg"><path d="M7.293 8.293a1 1 0 0 1 1.414 0L10 9.586l1.293-1.293a1 1 0 1 1 1.414 1.414l-2 2a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 0-1.414z"/></svg>') no-repeat right 12px center/16px 16px;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        transition: border-color 0.2s;
        min-width: 180px;
        max-width: 320px;
        display: inline-block !important;
    }
    .dashboard-select-custom:focus {
        border-color: #0043E3;
        outline: none;
        box-shadow: 0 0 0 2px #e6edfa;
    }
    label[for^="anno-select-annuale"], label[for^="holder-select-annuale"] {
        font-weight: 500;
        margin-bottom: 2px;
        display: inline-block;
    }
    </style>
    <div style="margin-bottom:40px;">
        <div style="font-size:20px;font-weight:600;margin-bottom:10px;">Distribuzione delle risorse tra i Titolari</div>
        <div style="display:flex;gap:32px;margin-bottom:20px;">
            <div>
                <label for="anno-select-annuale">Anno</label><br>
                <select id="anno-select-annuale" class="dashboard-select-custom">
                    <?php foreach ($anni as $a): ?>
                        <option value="<?php echo esc_attr($a); ?>"><?php echo esc_html($a); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="holder-select-annuale">Titolare</label><br>
                <select id="holder-select-annuale" class="dashboard-select-custom">
                    <?php foreach ($holders as $h): ?>
                        <option value="<?php echo esc_attr($h); ?>"><?php echo esc_html($h); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div id="chart-anno" style="width: 100%; height: 400px; margin:auto; background:#fff; border-radius:8px; box-shadow:0 1px 4px rgba(0,0,0,0.04);"></div>
    </div>
    <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        var rawData = <?php echo json_encode($raw); ?>;
        var mesi = <?php echo json_encode($mesi); ?>;
        var mesiIniziali = <?php echo json_encode($mesi_iniziali); ?>;
        var colori = <?php echo json_encode($colori); ?>;
        var chartDom = document.getElementById('chart-anno');
        var myChart = echarts.init(chartDom);

        function updateHolderSelect() {
            var anno = document.getElementById('anno-select-annuale').value;
            var titolari = rawData.filter(function(row) { return row.anno === anno; })
                .map(function(row) { return row.holder; });
            var unique = Array.from(new Set(titolari)).sort();
            var holderSelect = document.getElementById('holder-select-annuale');
            var currentHolder = holderSelect.value; // Store current selection
            
            holderSelect.innerHTML = '';
            unique.forEach(function(h) {
                var opt = document.createElement('option');
                opt.value = h;
                opt.textContent = h;
                holderSelect.appendChild(opt);
            });
            
            // Try to maintain the previous selection if it exists in the new list
            if (unique.includes(currentHolder)) {
                holderSelect.value = currentHolder;
            } else if (unique.length > 0) {
                holderSelect.value = unique[0];
            }
            
            // ALWAYS ADD THE EVENT LISTENER AFTER RESET
            holderSelect.removeEventListener('change', updateChart); // avoid duplicates
            holderSelect.addEventListener('change', updateChart);
        }

        function updateChart() {
            var anno = document.getElementById('anno-select-annuale').value;
            var holder = document.getElementById('holder-select-annuale').value;

            var ontologie = [];
            var vocabolari = [];
            var schemi = [];
            mesi.forEach(function(mese, idx) {
                var foundOnt = rawData.find(r => r.anno === anno && r.holder === holder && r.mese === mese && r.type === 'Ontologie');
                var foundVoc = rawData.find(r => r.anno === anno && r.holder === holder && r.mese === mese && r.type === 'Vocabolari');
                var foundSch = rawData.find(r => r.anno === anno && r.holder === holder && r.mese === mese && r.type === 'Schemi dati');
                ontologie.push(foundOnt ? foundOnt.count : 0);
                vocabolari.push(foundVoc ? foundVoc.count : 0);
                schemi.push(foundSch ? foundSch.count : 0);
            });

            // If all series are empty, show message and clear chart
            var isEmpty = ontologie.every(v => v === 0) && vocabolari.every(v => v === 0) && schemi.every(v => v === 0);
            if (isEmpty) {
                myChart.clear();
                myChart.setOption({
                    title: {
                        text: 'Nessun dato disponibile per la selezione',
                        left: 'center',
                        top: 'center',
                        textStyle: {
                            fontSize: 16,
                            color: '#666'
                        }
                    },
                    series: []
                });
                return;
            }

            var option = {
                color: [colori['Ontologie'], colori['Vocabolari'], colori['Schemi dati']],
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
                    left: 60, right: 30, top: 40, bottom: 80
                },
                xAxis: {
                    type: 'category',
                    data: mesiIniziali,
                    name: 'Anno',
                    nameLocation: 'middle',
                    nameGap: 35,
                    axisLine: { lineStyle: { color: '#bbb' } },
                    axisLabel: { fontSize: 15 }
                },
                yAxis: {
                    type: 'value',
                    name: 'Numero risorse',
                    nameLocation: 'middle',
                    nameGap: 50,
                    min: 0,
                    axisLine: { lineStyle: { color: '#bbb' } },
                    axisLabel: { fontSize: 15 }
                },
                series: [
                    {
                        name: 'Ontologie',
                        type: 'bar',
                        data: ontologie,
                        barWidth: 12,
                        color: colori['Ontologie'],
                        barCategoryGap: '50%'
                    },
                    {
                        name: 'Vocabolari',
                        type: 'bar',
                        data: vocabolari,
                        barWidth: 12,
                        color: colori['Vocabolari'],
                        barCategoryGap: '50%'
                    },
                    {
                        name: 'Schemi dati',
                        type: 'bar',
                        data: schemi,
                        barWidth: 12,
                        color: colori['Schemi dati'],
                        barCategoryGap: '50%'
                    }
                ]
            };
            myChart.setOption(option);
        }

        document.getElementById('anno-select-annuale').addEventListener('change', function() {
            updateHolderSelect();
            updateChart();
        });

        // Initialization
        updateHolderSelect();
        updateChart();
        window.addEventListener('resize', function() { myChart.resize(); });
    });
    </script>
    <?php
}

