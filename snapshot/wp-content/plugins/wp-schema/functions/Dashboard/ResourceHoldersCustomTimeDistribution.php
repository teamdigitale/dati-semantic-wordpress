<?php 

// Check if wp-bootstrap-italia is active
if (!function_exists('wp_bootstrap_italia_enqueue_assets')) {
    add_action('admin_notices', function() {
        echo '<div class="error"><p>Il plugin WP Bootstrap Italia è richiesto per la dashboard dei titolari delle risorse.</p></div>';
    });
    return;
}

// Register shortcode
add_shortcode('DASHBOARD-TITOLARI_RISORSE_CUSTOM', 'wp_schema_print_distribution_chart_custom');

// Map month numbers to Italian names
$mesi_ita_custom = [
    '01' => 'Gennaio', '02' => 'Febbraio', '03' => 'Marzo', '04' => 'Aprile',
    '05' => 'Maggio', '06' => 'Giugno', '07' => 'Luglio', '08' => 'Agosto',
    '09' => 'Settembre', '10' => 'Ottobre', '11' => 'Novembre', '12' => 'Dicembre'
];

// Format month in Italian
function format_mese_ita_custom($m) {
    $mesi_ita_custom = [
        '01' => 'Gennaio', '02' => 'Febbraio', '03' => 'Marzo', '04' => 'Aprile',
        '05' => 'Maggio', '06' => 'Giugno', '07' => 'Luglio', '08' => 'Agosto',
        '09' => 'Settembre', '10' => 'Ottobre', '11' => 'Novembre', '12' => 'Dicembre'
    ];
    
    if (empty($m)) {
        return '';
    }
    
    $parts = explode('-', $m);
    if (count($parts) !== 2 || !isset($mesi_ita_custom[$parts[0]])) {
        return $m;
    }
    
    return $mesi_ita_custom[$parts[0]] . ' ' . $parts[1];
}

function wp_schema_print_distribution_chart_custom() {
    // API call: get all available data (no month filter)
    $api_url = WP_SCHEMA_API_BASE_URL . 'dashboard/aggregated-count-data?dimension=RIGHT_HOLDER,RESOURCE_TYPE&granularity=MONTHS';
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
        color: #222;
    }
    .dashboard-select-custom:focus {
        border-color: #0043E3;
        outline: none;
        box-shadow: 0 0 0 2px #e6edfa;
    }
    .dashboard-select-custom:focus-visible {
        outline: 2px solid #0043E3;
        outline-offset: 2px;
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
    .nav-tabs .nav-item { flex: 1 1 0; text-align: center; }
    .nav-tabs { display: flex; border-bottom: 2px solid #e5e9ec; }
    .nav-tabs .nav-link { 
        width: 100%; 
        border: none; 
        border-bottom: 2px solid transparent; 
        color: #5c6f82; 
        font-size: 1.2rem; 
        font-weight: 600; 
        background: none;
        padding: 12px;
    }
    .nav-tabs .nav-link.active { 
        color: #0066cc; 
        border-bottom: 2px solid #0066cc; 
        background: none; 
    }
    .nav-tabs .nav-link:focus-visible {
        outline: 2px solid #0066cc;
        outline-offset: 2px;
    }
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
        text-align: left;
    }
    .custom-table td, .custom-table tbody th[scope="row"] {
        border: none;
        padding: 12px 8px;
        color: #222;
        background: inherit;
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
        color: #222;
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
    @media (max-width: 600px) {
        .holder-multiselect-row {
            flex-direction: column !important;
            align-items: stretch !important;
        }
        .custom-multiselect-filter-btn {
            margin-left: 0 !important;
            margin-top: 10px;
            width: 100%;
        }
    }
    </style>
    <!-- FILTRI UNICI -->
    <div class="holder-multiselect-row" style="display:flex;gap:32px;margin-bottom:20px;">
        <div>
            <span class="sr-only" id="anno-select-label">Seleziona l'anno</span>
            <select id="anno-select-custom" class="dashboard-select-custom" aria-labelledby="anno-select-label" aria-expanded="false" aria-haspopup="listbox">
                <?php foreach ($anni as $a): ?>
                    <option value="<?php echo esc_attr($a); ?>"><?php echo esc_html($a); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <span class="sr-only" id="holder-select-label">Seleziona il titolare</span>
            <select id="holder-select-custom" class="dashboard-select-custom" aria-labelledby="holder-select-label" aria-expanded="false" aria-haspopup="listbox">
                <option value="" disabled selected>Seleziona titolari...</option>
                <?php foreach ($holders as $h): ?>
                    <option value="<?php echo esc_attr($h); ?>"><?php echo esc_html($h); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button class="custom-multiselect-filter-btn" id="filter-btn-custom" aria-label="Applica i filtri selezionati">Filtra</button>
    </div>
    <div class="it-tabs it-tabs-primary" style="margin-bottom:40px;">
        <ul class="nav nav-tabs" id="resourceHoldersCustomTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="resource-holders-custom-grafico-tab" data-bs-toggle="tab" data-bs-target="#resource-holders-custom-grafico" type="button" role="tab" aria-controls="resource-holders-custom-grafico" aria-selected="true" aria-label="Visualizzazione grafico">Grafico</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="resource-holders-custom-tabella-tab" data-bs-toggle="tab" data-bs-target="#resource-holders-custom-tabella" type="button" role="tab" aria-controls="resource-holders-custom-tabella" aria-selected="false" aria-label="Visualizzazione tabella">Tabella</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="resource-holders-custom-info-tab" data-bs-toggle="tab" data-bs-target="#resource-holders-custom-info" type="button" role="tab" aria-controls="resource-holders-custom-info" aria-selected="false" aria-label="Informazioni sulla dashboard">Info</button>
            </li>
        </ul>
        <div class="tab-content" id="resourceHoldersCustomTabsContent" style="background:#fff; border-radius:8px; box-shadow:0 1px 4px rgba(0,0,0,0.04); padding: 24px 12px;">
            <div class="tab-pane fade show active" id="resource-holders-custom-grafico" role="tabpanel" aria-labelledby="resource-holders-custom-grafico-tab">
                <h2 style="font-size:20px;font-weight:600;margin-bottom:10px;">Distribuzione delle risorse tra i Titolari</h2>
                <div id="chart-custom" style="width: 100%; height: 400px; margin:auto; background:#fff; border-radius:8px; box-shadow:0 1px 4px rgba(0,0,0,0.04);" role="img" aria-label="Grafico della distribuzione delle risorse" aria-describedby="chart-description"></div>
                <div id="chart-description" class="sr-only">
                    Grafico a barre impilate che mostra la distribuzione mensile delle risorse semantiche per titolare. 
                    Le barre sono colorate in blu per le ontologie, verde per i vocabolari e arancione per gli schemi dati.
                    Utilizzare i controlli sopra il grafico per filtrare i dati per anno e titolare.
                </div>
            </div>
            <div class="tab-pane fade" id="resource-holders-custom-tabella" role="tabpanel" aria-labelledby="resource-holders-custom-tabella-tab">
                <h2 style="font-size:20px;font-weight:600;margin-bottom:10px;">Tabella dati</h2>
                <div class="table-responsive">
                    <table class="custom-table" id="holder-table-custom" aria-label="Tabella della distribuzione delle risorse">
                        <caption>Dettaglio mensile delle risorse per titolare</caption>
                        <thead>
                            <tr>
                                <th scope="col">Mese</th>
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
            <div class="tab-pane fade" id="resource-holders-custom-info" role="tabpanel" aria-labelledby="resource-holders-custom-info-tab">
                <section aria-labelledby="desc-heading">
                    <h3 id="desc-heading" style="font-size:16px;font-weight:600;margin-bottom:8px;">DESCRIZIONE GENERALE</h3>
                    <p>Questa dashboard mostra la distribuzione mensile delle risorse semantiche per titolare.</p>
                </section>

                <section aria-labelledby="filtri-heading">
                    <h3 id="filtri-heading" style="font-size:16px;font-weight:600;margin-bottom:8px;">FILTRI</h3>
                    <p>Puoi filtrare i dati per:</p>
                    <ul>
                        <li>Anno: seleziona l'anno di interesse</li>
                        <li>Titolare: seleziona il titolare di cui visualizzare i dati</li>
                    </ul>
                </section>

                <section aria-labelledby="funz-heading">
                    <h3 id="funz-heading" style="font-size:16px;font-weight:600;margin-bottom:8px;">FUNZIONALITÀ</h3>
                    <ul>
                        <li>Visualizzazione grafico: mostra la distribuzione mensile delle risorse in formato grafico a barre impilate</li>
                        <li>Visualizzazione tabella: mostra i dati in formato tabellare con i totali per ogni categoria</li>
                        <li>Aggiornamento dati: i dati vengono aggiornati solo dopo aver cliccato il pulsante "Filtra"</li>
                    </ul>
                </section>
            </div>
        </div>
    </div>
    <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        var rawData = <?php echo json_encode($raw); ?>;
        var mesi = <?php echo json_encode($mesi); ?>;
        var mesiIniziali = <?php echo json_encode($mesi_iniziali); ?>;
        var colori = <?php echo json_encode($colori); ?>;
        var chartDom = document.getElementById('chart-custom');
        
        if (!chartDom) {
            return;
        }
        
        var myChart = echarts.init(chartDom);

        // Enhance keyboard navigation for tabs
        document.querySelectorAll('.nav-link').forEach(tab => {
            tab.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.click();
                } else if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    var nextTab = this.parentElement.nextElementSibling?.querySelector('.nav-link');
                    if (nextTab) nextTab.focus();
                } else if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    var prevTab = this.parentElement.previousElementSibling?.querySelector('.nav-link');
                    if (prevTab) prevTab.focus();
                }
            });
        });

        // Enhance select accessibility
        document.querySelectorAll('.dashboard-select-custom').forEach(select => {
            select.addEventListener('click', function() {
                this.setAttribute('aria-expanded', this.getAttribute('aria-expanded') === 'true' ? 'false' : 'true');
            });
            
            select.addEventListener('blur', function() {
                this.setAttribute('aria-expanded', 'false');
            });
        });

        function updateHolderSelect() {
            var anno = document.getElementById('anno-select-custom').value;
            var titolari = rawData.filter(function(row) { return row.anno === anno; })
                .map(function(row) { return row.holder; });
            var unique = Array.from(new Set(titolari)).sort();
            var holderSelect = document.getElementById('holder-select-custom');
            var currentHolder = holderSelect.value;
            
            holderSelect.innerHTML = '<option value="" disabled selected>Seleziona titolari...</option>';
            unique.forEach(function(h) {
                var opt = document.createElement('option');
                opt.value = h;
                opt.textContent = h;
                holderSelect.appendChild(opt);
            });
            
            if (unique.includes(currentHolder)) {
                holderSelect.value = currentHolder;
            } else if (unique.length > 0) {
                holderSelect.value = unique[0];
            }

            // Announce the change to screen readers
            var announcement = document.createElement('div');
            announcement.setAttribute('aria-live', 'polite');
            announcement.setAttribute('class', 'sr-only');
            announcement.textContent = 'Opzioni del titolare aggiornate per l\'anno ' + anno;
            document.body.appendChild(announcement);
            setTimeout(() => announcement.remove(), 1000);
        }

        function updateTableHolderSelect() {
            var anno = document.getElementById('anno-select-custom').value;
            var titolari = rawData.filter(function(row) { return row.anno === anno; })
                .map(function(row) { return row.holder; });
            var unique = Array.from(new Set(titolari)).sort();
            var holderSelect = document.getElementById('holder-select-custom');
            var currentHolder = holderSelect.value;
            
            holderSelect.innerHTML = '<option value="" disabled selected>Seleziona titolari...</option>';
            unique.forEach(function(h) {
                var opt = document.createElement('option');
                opt.value = h;
                opt.textContent = h;
                holderSelect.appendChild(opt);
            });
            
            if (unique.includes(currentHolder)) {
                holderSelect.value = currentHolder;
            } else if (unique.length > 0) {
                holderSelect.value = unique[0];
            }

            // Announce the change to screen readers
            var announcement = document.createElement('div');
            announcement.setAttribute('aria-live', 'polite');
            announcement.setAttribute('class', 'sr-only');
            announcement.textContent = 'Opzioni del titolare aggiornate per l\'anno ' + anno;
            document.body.appendChild(announcement);
            setTimeout(() => announcement.remove(), 1000);
        }

        function getMonthlyDeltas(rawData, anno, holder, type, mesi) {
            let prev = 0;
            return mesi.map(function(mese, idx) {
                let found = rawData.find(r => r.anno === anno && r.holder === holder && r.mese === mese && r.type === type);
                let curr = found ? found.count : 0;
                let delta = idx === 0 ? curr : curr - prev;
                prev = curr;
                return delta < 0 ? 0 : delta; // evita valori negativi
            });
        }

        function updateChart() {
            var anno = document.getElementById('anno-select-custom').value;
            var holder = document.getElementById('holder-select-custom').value;

            var ontologie = getMonthlyDeltas(rawData, anno, holder, 'Ontologie', mesi);
            var vocabolari = getMonthlyDeltas(rawData, anno, holder, 'Vocabolari', mesi);
            var schemi = getMonthlyDeltas(rawData, anno, holder, 'Schemi dati', mesi);

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

            var mesiItaAbbr = [
                "Gen.", "Feb.", "Mar.", "Apr.", "Mag.", "Giu.",
                "Lug.", "Ago.", "Set.", "Ott.", "Nov.", "Dic."
            ];

            var option = {
                color: [colori['Ontologie'], colori['Vocabolari'], colori['Schemi dati']],
                title: { show: false },
                tooltip: { 
                    trigger: 'axis', 
                    axisPointer: { type: 'shadow' },
                    formatter: function(params) {
                        var result = params[0].name + '<br/>';
                        params.forEach(function(param) {
                            result += param.seriesName + ': ' + param.value + '<br/>';
                        });
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
                    left: 80, // o 100
                    right: 30,
                    top: 40,
                    bottom: 80
                },
                xAxis: {
                    type: 'category',
                    data: mesiItaAbbr,
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
                    minInterval: 1, // <--- aggiungi questa riga!
                    axisLine: { lineStyle: { color: '#bbb' } },
                    axisLabel: { 
                        fontSize: 15,
                        formatter: function (value) {
                            return value.toLocaleString('it-IT');
                        }
                    }
                    // interval: 1, // <-- puoi rimuovere o commentare questa riga
                    // splitNumber: 5 // <-- opzionale, puoi rimuovere per lasciare auto
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

        function updateTable() {
            var anno = document.getElementById('anno-select-custom').value;
            var holder = document.getElementById('holder-select-custom').value;
            var tbody = document.querySelector('#holder-table-custom tbody');
            if (!tbody) {
                return;
            }
            tbody.innerHTML = '';

            var mesi_ita = {
                '01': 'Gennaio', '02': 'Febbraio', '03': 'Marzo', '04': 'Aprile',
                '05': 'Maggio', '06': 'Giugno', '07': 'Luglio', '08': 'Agosto',
                '09': 'Settembre', '10': 'Ottobre', '11': 'Novembre', '12': 'Dicembre'
            };

            mesi.forEach(function(mese) {
                var foundOnt = rawData.find(r => r.anno === anno && r.holder === holder && r.mese === mese && r.type === 'Ontologie');
                var foundVoc = rawData.find(r => r.anno === anno && r.holder === holder && r.mese === mese && r.type === 'Vocabolari');
                var foundSch = rawData.find(r => r.anno === anno && r.holder === holder && r.mese === mese && r.type === 'Schemi dati');
                
                var ontologie = foundOnt ? foundOnt.count : 0;
                var vocabolari = foundVoc ? foundVoc.count : 0;
                var schemi = foundSch ? foundSch.count : 0;
                var totale = ontologie + vocabolari + schemi;

                var tr = document.createElement('tr');
                tr.innerHTML = '<th scope="row">' + mesi_ita[mese] + '</th>' +
                    '<td>' + ontologie + '</td>' +
                    '<td>' + vocabolari + '</td>' +
                    '<td>' + schemi + '</td>' +
                    '<td>' + totale + '</td>';
                tbody.appendChild(tr);
            });

            // Announce the update to screen readers
            var announcement = document.createElement('div');
            announcement.setAttribute('aria-live', 'polite');
            announcement.setAttribute('class', 'sr-only');
            announcement.textContent = 'Tabella aggiornata per ' + holder + ' nell\'anno ' + anno;
            document.body.appendChild(announcement);
            setTimeout(() => announcement.remove(), 1000);
        }

        // Sincronizza anno tra grafico e tabella
        document.getElementById('anno-select-custom').addEventListener('change', function() {
            document.getElementById('anno-select-custom').value = this.value;
            updateHolderSelect();
            updateTableHolderSelect();
        });
        document.getElementById('holder-select-custom').addEventListener('change', function() {
            document.getElementById('holder-select-custom').value = this.value;
        });
        // Unifica i bottoni Filtra: aggiornano sia grafico che tabella
        document.getElementById('filter-btn-custom').addEventListener('click', function() {
            updateChart();
            updateTable();
        });

        // Initialize
        updateHolderSelect();
        updateTableHolderSelect();
        updateChart();
        updateTable();
        
        // Handle window resize
        window.addEventListener('resize', function() { 
            myChart.resize(); 
        });
    });
    </script>
    <?php
}