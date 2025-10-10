<?php
// Shortcode: [MATOMO-VISITS-TREND]
add_shortcode('MATOMO-VISITS-TREND', 'matomo_visits_trend_shortcode');

function matomo_visits_trend_shortcode() {
    // Check if matomo_fetch is available
    if (!function_exists('matomo_fetch')) {
        echo '<div style="color:red;font-weight:bold;">Matomo API function not available. Please ensure MatomoStats.php is included.</div>';
        return;
    }
    $matomo_data = matomo_visits_trend_get_data();
    if (empty($matomo_data['by_year']) || !is_array($matomo_data['by_year'])) {
        echo '<div style="color:red;font-weight:bold;">No visit data available from Matomo.</div>';
        return;
    }
    $years = array_keys($matomo_data['by_year']);
    $default_year = $years[0] ?? date('Y');
    $months = isset($matomo_data['by_year'][$default_year]['months']) && is_array($matomo_data['by_year'][$default_year]['months'])
        ? array_keys($matomo_data['by_year'][$default_year]['months']) : [];
    ?>
    <div class="matomo-trend-container">
        <div class="matomo-trend-header">Andamento delle visite per Schema.gov</div>
        <div class="matomo-trend-filters">
            <div>
                <label for="matomo-trend-year">Anno</label>
                <select id="matomo-trend-year">
                    <?php foreach ($years as $year): ?>
                        <option value="<?php echo esc_attr($year); ?>"><?php echo esc_html($year); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="matomo-trend-month">Mese</label>
                <select id="matomo-trend-month">
                    <option value="">--</option>
                    <?php foreach ($months as $month): ?>
                        <option value="<?php echo esc_attr($month); ?>"><?php echo esc_html(matomo_visits_trend_month_label($month)); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div id="matomo-trend-chart" style="width:100%;height:340px;background:#fff;border-radius:8px;"></div>
        <div class="matomo-trend-legend">
            <span class="legend-dot legend-dot-blue"></span> Ontologie
            <span class="legend-dot legend-dot-green"></span> Vocabolari
            <span class="legend-dot legend-dot-yellow"></span> Schemi dati
        </div>
    </div>
    <style>
    .matomo-trend-container { font-family: Arial, sans-serif; max-width: 700px; margin: 0 auto 32px auto; background: #f9fbfd; border-radius: 10px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); padding: 24px 32px; }
    .matomo-trend-header { font-size: 22px; font-weight: 600; margin-bottom: 18px; }
    .matomo-trend-filters { display: flex; gap: 32px; margin-bottom: 18px; }
    .matomo-trend-filters label { font-weight: 500; margin-right: 6px; }
    .matomo-trend-filters select { padding: 6px 12px; font-size: 15px; border-radius: 6px; border: 1px solid #bfc7d1; background: #fff; }
    .matomo-trend-legend { margin-top: 18px; font-size: 15px; font-weight: 600; color: #2a4b6a; display: flex; gap: 18px; align-items: center; }
    .legend-dot { display: inline-block; width: 14px; height: 14px; border-radius: 50%; margin-right: 6px; }
    .legend-dot-blue { background: #0043E3; }
    .legend-dot-green { background: #077F7B; }
    .legend-dot-yellow { background: #EBA704; }
    </style>
    <script type="text/javascript">
    const matomoTrendData = <?php echo json_encode($matomo_data['by_year']); ?>;
    const monthLabels = ["G", "F", "M", "A", "M", "G", "L", "A", "S", "O", "N", "D"];
    function getMonthLabel(num) { return monthLabels[parseInt(num,10)-1] || num; }
    function updateMonthSelect(year) {
        if (!matomoTrendData[year] || !matomoTrendData[year].months) return;
        const months = Object.keys(matomoTrendData[year].months);
        const monthSelect = document.getElementById('matomo-trend-month');
        monthSelect.innerHTML = '<option value="">--</option>';
        months.forEach(m => {
            const label = getMonthLabel(m);
            monthSelect.innerHTML += `<option value="${m}">${label}</option>`;
        });
    }
    function renderChart(year, month) {
        const chartDom = document.getElementById('matomo-trend-chart');
        if (!matomoTrendData[year] || !matomoTrendData[year].months) {
            chartDom.innerHTML = '<div style="color:red;font-weight:bold;padding:30px;">No data available for the selected year.</div>';
            return;
        }
        const myChart = echarts.init(chartDom);
        let x = [], ont = [], voc = [], sch = [];
        if (month && matomoTrendData[year].months[month] && matomoTrendData[year].months[month].days) {
            const days = matomoTrendData[year].months[month].days;
            x = Object.keys(days);
            ont = x.map(d => days[d]['Ontologie']||0);
            voc = x.map(d => days[d]['Vocabolari']||0);
            sch = x.map(d => days[d]['Schemi dati']||0);
        } else {
            x = Object.keys(matomoTrendData[year].months);
            ont = x.map(m => matomoTrendData[year].months[m]['Ontologie']||0);
            voc = x.map(m => matomoTrendData[year].months[m]['Vocabolari']||0);
            sch = x.map(m => matomoTrendData[year].months[m]['Schemi dati']||0);
            x = x.map(getMonthLabel);
        }
        if (x.length === 0) {
            chartDom.innerHTML = '<div style="color:red;font-weight:bold;padding:30px;">No data available for the selected period.</div>';
            return;
        }
        const option = {
            color: ['#0043E3', '#077F7B', '#EBA704'],
            tooltip: { trigger: 'axis' },
            legend: { data: ['Ontologie', 'Vocabolari', 'Schemi dati'], bottom: 0, left: 0, itemWidth: 10, itemHeight: 10, icon: 'circle', textStyle: { fontWeight: 600, fontSize: 14 } },
            grid: { left: 50, right: 30, top: 40, bottom: 60 },
            xAxis: { type: 'category', data: x, name: month ? 'Giorni' : 'Mesi dell\'anno', nameLocation: 'middle', nameGap: 30, axisLabel: { fontSize: 15 }, axisLine: { lineStyle: { color: '#bbb' } } },
            yAxis: { type: 'value', name: 'Numero visite', nameLocation: 'middle', nameGap: 50, min: 0, axisLabel: { fontSize: 15 }, axisLine: { lineStyle: { color: '#bbb' } } },
            series: [
                { name: 'Ontologie', type: 'line', data: ont, symbol: 'circle', lineStyle: { width: 3 } },
                { name: 'Vocabolari', type: 'line', data: voc, symbol: 'circle', lineStyle: { width: 3 } },
                { name: 'Schemi dati', type: 'line', data: sch, symbol: 'circle', lineStyle: { width: 3 } }
            ]
        };
        myChart.setOption(option);
        window.addEventListener('resize', function() { myChart.resize(); });
    }
    document.addEventListener('DOMContentLoaded', function() {
        const yearSel = document.getElementById('matomo-trend-year');
        const monthSel = document.getElementById('matomo-trend-month');
        function updateAll() {
            updateMonthSelect(yearSel.value);
            renderChart(yearSel.value, monthSel.value);
        }
        yearSel.addEventListener('change', function() {
            updateMonthSelect(this.value);
            monthSel.value = '';
            renderChart(this.value, '');
        });
        monthSel.addEventListener('change', function() {
            renderChart(yearSel.value, this.value);
        });
        updateAll();
    });
    </script>
    <?php
}

// Helper: Italian month label
function matomo_visits_trend_month_label($m) {
    $labels = ['01'=>'G','02'=>'F','03'=>'M','04'=>'A','05'=>'M','06'=>'G','07'=>'L','08'=>'A','09'=>'S','10'=>'O','11'=>'N','12'=>'D'];
    return $labels[$m] ?? $m;
}

// Fetch and aggregate Matomo data for the chart
function matomo_visits_trend_get_data() {
    $result = [ 'by_year' => [] ];
    if (!function_exists('matomo_fetch')) {
        echo '<div style="color:red;">Function matomo_fetch not found</div>';
        return $result;
    }

    // Debug: Print ALL URLs as in MatomoStats.php
    echo '<div style="background:#f5f5f5;padding:20px;margin:20px;border-radius:5px;">';
    echo '<h3>DEBUG: Lista TUTTE le URL (url e label) come in MatomoStats.php</h3>';
    echo '<pre style="max-height:400px;overflow:auto;">';

    $params = [
        'idSite' => MATOMO_SITE_ID,
        'period' => 'range',
        'date' => 'last1000',
        'format' => 'json',
        'token_auth' => MATOMO_TOKEN,
        'module' => 'API',
        'method' => 'Actions.getPageUrls'
    ];
    $pages = matomo_fetch('Actions.getPageUrls', $params);

    echo '<pre>';
    print_r($pages);
    echo '</pre>';

    error_log('Matomo API result: ' . print_r($filtered_pages, true));
    if (is_wp_error($filtered_pages) || !is_array($filtered_pages)) return $result;
    
    foreach ($filtered_pages as $page) {
        if ((!isset($page['label'])) || !isset($page['nb_visits']) || !isset($page['date'])) continue;
        $url = $page['label'];
        
        // Enhanced debug logging
        error_log('Processing URL: ' . $url);
        error_log('URL length: ' . strlen($url));
        
        // Estrai la parte dopo ?uri=
        $type = null;
        $uri_part = '';
        
        if (preg_match('/[?&]uri=([^&]+)/', $url, $matches)) {
            $uri_part = urldecode($matches[1]);
            error_log('Decoded URI part: ' . $uri_part);
            
            // Categorizza basandosi sulla parte decodificata dopo ?uri=
            if (strpos($uri_part, 'onto') !== false) {
                $type = 'Ontologie';
            } elseif (strpos($uri_part, 'schemas') !== false) {
                $type = 'Schemi dati';
            } elseif (strpos($uri_part, 'controlled-vocabulary') !== false) {
                $type = 'Vocabolari';
            }
            
            error_log('URL type: ' . ($type ?? 'none'));
        }
        
        if (!$type) {
            error_log('URL did not match any category pattern');
            continue;
        }
        
        $date = $page['date']; // format: YYYY-MM-DD
        $parts = explode('-', $date);
        if (count($parts) !== 3) continue;
        
        list($year, $month, $day) = $parts;
        
        // Aggregate by year/month/day/type
        if (!isset($result['by_year'][$year])) $result['by_year'][$year] = [ 'months' => [] ];
        if (!isset($result['by_year'][$year]['months'][$month])) $result['by_year'][$year]['months'][$month] = [ 'Ontologie'=>0, 'Vocabolari'=>0, 'Schemi dati'=>0, 'days'=>[] ];
        $result['by_year'][$year]['months'][$month][$type] += intval($page['nb_visits']);
        if (!isset($result['by_year'][$year]['months'][$month]['days'][$day])) $result['by_year'][$year]['months'][$month]['days'][$day] = [ 'Ontologie'=>0, 'Vocabolari'=>0, 'Schemi dati'=>0 ];
        $result['by_year'][$year]['months'][$month]['days'][$day][$type] += intval($page['nb_visits']);
    }
    return $result;
}

function matomo_flatten_pages($pages, &$flat = []) {
    foreach ($pages as $page) {
        if (isset($page['url']) || isset($page['label'])) {
            $flat[] = $page;
        }
        if (isset($page['subtable']) && is_array($page['subtable'])) {
            matomo_flatten_pages($page['subtable'], $flat);
        }
    }
    return $flat;
}