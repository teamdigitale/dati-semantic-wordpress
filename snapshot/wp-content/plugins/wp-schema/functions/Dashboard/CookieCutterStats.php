<?php 
// CookieCutter Statistics Functions
// Purpose: Display GitHub repository statistics for dati-semantic-cookiecutter
// Used in: Shortcode [cookiecutter_stats]

// Shortcode for GitHub statistics of the cookiecutter repository
add_shortcode('cookiecutter_stats', 'render_cookiecutter_stats');

function render_cookiecutter_stats($atts) {
    // Debug: Log shortcode execution
    error_log('Cookiecutter Stats Shortcode: Executed');
    
    $atts = shortcode_atts(array(
        'show_forks' => 'true',
        'show_stars' => 'true',
        'show_watchers' => 'true',
        'show_languages' => 'true',
        'show_commits' => 'true'
    ), $atts);
    
    // Debug: Log attributes
    error_log('Cookiecutter Stats Shortcode: Attributes = ' . print_r($atts, true));
    
    // Fetch GitHub API data
    $github_data = fetch_github_repo_data();
    
    // Debug: Log GitHub data fetch
    error_log('Cookiecutter Stats Shortcode: GitHub data = ' . ($github_data ? 'Success' : 'Failed'));
    
    if (!$github_data) {
        // Try a simple connectivity test using WordPress HTTP API
        $test_url = 'https://api.github.com';
        $test_response = wp_remote_get($test_url, array(
            'timeout' => 10,
            'headers' => array('User-Agent' => 'Mozilla/5.0 (compatible; WordPress-Plugin/1.0)')
        ));
        
        $connectivity_status = 'GitHub API unreachable';
        if (!is_wp_error($test_response)) {
            $test_code = wp_remote_retrieve_response_code($test_response);
            $connectivity_status = $test_code === 200 ? 'GitHub API reachable' : 'GitHub API returned code: ' . $test_code;
        } else {
            $connectivity_status = 'GitHub API unreachable - ' . $test_response->get_error_message();
        }
        error_log('GitHub Connectivity Test: ' . $connectivity_status);
        
        // Show fallback with static data if GitHub is blocked
        return render_cookiecutter_stats_fallback($atts, $connectivity_status);
    }
    
    ob_start();
    ?>
    <div class="cookiecutter-stats-container">
        <h3>📊 Statistiche Repository dati-semantic-cookiecutter</h3>
        <?php if (isset($github_data['cache_source'])): ?>
        <div class="cache-info">
            <small>
                <?php if ($github_data['cache_source'] === 'github_api'): ?>
                    ✅ Dati aggiornati da GitHub API
                <?php else: ?>
                    📁 Dati da cache locale
                <?php endif; ?>
                <?php if (isset($github_data['cached_at'])): ?>
                    (<?php echo esc_html($github_data['cached_at']); ?>)
                <?php endif; ?>
            </small>
        </div>
        <?php endif; ?>
        <div class="stats-grid">
            
            <?php if ($atts['show_stars'] === 'true'): ?>
            <div class="stat-card">
                <div class="stat-icon">⭐</div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo esc_html($github_data['stargazers_count']); ?></div>
                    <div class="stat-label">Stars</div>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($atts['show_forks'] === 'true'): ?>
            <div class="stat-card">
                <div class="stat-icon">🍴</div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo esc_html($github_data['forks_count']); ?></div>
                    <div class="stat-label">Forks</div>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($atts['show_watchers'] === 'true'): ?>
            <div class="stat-card">
                <div class="stat-icon">👀</div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo esc_html($github_data['watchers_count']); ?></div>
                    <div class="stat-label">Watchers</div>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($atts['show_commits'] === 'true'): ?>
            <div class="stat-card">
                <div class="stat-icon">📝</div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo esc_html($github_data['commits_count']); ?></div>
                    <div class="stat-label">Commits</div>
                </div>
            </div>
            <?php endif; ?>
            
        </div>
        
        
        <?php if ($atts['show_languages'] === 'true' && !empty($github_data['languages'])): ?>
        <div class="languages-section">
            <h4>💻 Linguaggi</h4>
            <div class="languages-chart">
                <?php 
                $total_bytes = array_sum($github_data['languages']);
                foreach ($github_data['languages'] as $language => $bytes): 
                    $percentage = ($bytes / $total_bytes) * 100;
                ?>
                <div class="language-item">
                    <div class="language-name"><?php echo esc_html($language); ?></div>
                    <div class="language-bar">
                        <div class="language-fill" style="width: <?php echo esc_attr($percentage); ?>%"></div>
                    </div>
                    <div class="language-percentage"><?php echo esc_html(round($percentage, 1)); ?>%</div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="repo-info">
            <p><strong>Repository:</strong> 
                <a href="<?php echo esc_url($github_data['html_url']); ?>" target="_blank">
                    <?php echo esc_html($github_data['full_name']); ?>
                </a>
            </p>
            <p><strong>Ultimo aggiornamento:</strong> 
                <?php echo esc_html(date('d/m/Y H:i', strtotime($github_data['updated_at']))); ?>
            </p>
            <p><strong>Creato:</strong> 
                <?php echo esc_html(date('d/m/Y', strtotime($github_data['created_at']))); ?>
            </p>
        </div>
    </div>
    
    <style>
    .cookiecutter-stats-container {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin: 20px 0;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }
    
    .cookiecutter-stats-container h3 {
        margin-bottom: 20px;
        color: #333;
        border-bottom: 2px solid #007cba;
        padding-bottom: 10px;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        border: 1px solid #e9ecef;
        transition: transform 0.2s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .stat-icon {
        font-size: 24px;
        margin-bottom: 8px;
    }
    
    .stat-number {
        font-size: 24px;
        font-weight: bold;
        color: #007cba;
        margin-bottom: 5px;
    }
    
    .stat-label {
        font-size: 14px;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .languages-section {
        margin-bottom: 25px;
    }
    
    .languages-section h4 {
        margin-bottom: 15px;
        color: #333;
        font-size: 18px;
    }
    
    .languages-chart {
        background: #f8f9fa;
        border-radius: 6px;
        padding: 15px;
        border: 1px solid #e9ecef;
    }
    
    .language-item {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }
    
    .language-name {
        width: 100px;
        font-weight: bold;
        color: #333;
    }
    
    .language-bar {
        flex: 1;
        height: 20px;
        background: #e9ecef;
        border-radius: 10px;
        margin: 0 10px;
        overflow: hidden;
    }
    
    .language-fill {
        height: 100%;
        background: linear-gradient(90deg, #007cba, #0056b3);
        transition: width 0.3s ease;
    }
    
    .language-percentage {
        width: 50px;
        text-align: right;
        font-size: 14px;
        color: #666;
    }
    
    .repo-info {
        background: #f8f9fa;
        border-radius: 6px;
        padding: 15px;
        border: 1px solid #e9ecef;
        font-size: 14px;
    }
    
    .repo-info p {
        margin: 5px 0;
        color: #333;
    }
    
    .repo-info a {
        color: #007cba;
        text-decoration: none;
    }
    
    .repo-info a:hover {
        text-decoration: underline;
    }
    
    .cookiecutter-stats-error {
        background: #f8d7da;
        color: #721c24;
        padding: 15px;
        border-radius: 6px;
        border: 1px solid #f5c6cb;
        margin: 20px 0;
    }
    </style>
    <?php
    return ob_get_clean();
}

// Function to fetch GitHub repository data for cookiecutter statistics with file caching
function fetch_github_repo_data() {
    // Cache file path
    $cache_file = plugin_dir_path(__FILE__) . '../../cache/cookiecutter_stats.json';
    $cache_dir = dirname($cache_file);
    
    // Ensure cache directory exists
    if (!file_exists($cache_dir)) {
        wp_mkdir_p($cache_dir);
    }
    
    // Check if cache file exists and is less than 48 hours old
    if (file_exists($cache_file)) {
        $cache_time = filemtime($cache_file);
        $cache_age = time() - $cache_time;
        $cache_expiry = 48 * 60 * 60; // 48 hours in seconds
        
        if ($cache_age < $cache_expiry) {
            error_log('GitHub API Debug: Using cached data (age: ' . round($cache_age / 3600, 1) . ' hours)');
            $cached_data = json_decode(file_get_contents($cache_file), true);
            if ($cached_data) {
                return $cached_data;
            }
        } else {
            error_log('GitHub API Debug: Cache expired (age: ' . round($cache_age / 3600, 1) . ' hours)');
        }
    }
    
    error_log('GitHub API Debug: Cache expired or empty, fetching fresh data from GitHub API');
    error_log('GitHub API Debug: Calling URL = ' . GITHUB_REPO_URL);
    
    // Use WordPress HTTP API with proper GitHub headers
    $args = array(
        'timeout' => 30,
        'headers' => array(
            'User-Agent' => 'Mozilla/5.0 (compatible; WordPress-Plugin/1.0; +https://wordpress.org/)',
            'Accept' => 'application/vnd.github.v3+json',
            'Accept-Language' => 'en-US,en;q=0.9',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive'
        ),
        'sslverify' => true,
        'redirection' => 5
    );
    
    // Fetch main repository data
    $response = wp_remote_get(GITHUB_REPO_URL, $args);
    
    // Debug: Log response details
    if (is_wp_error($response)) {
        error_log('GitHub API Debug: wp_remote_get failed - ' . $response->get_error_message());
        return get_fallback_github_data();
    }
    
    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);
    
    error_log('GitHub API Debug: Response code = ' . $response_code);
    error_log('GitHub API Debug: Response body length = ' . strlen($response_body));
    
    if ($response_code !== 200) {
        error_log('GitHub API Debug: Non-200 response code: ' . $response_code);
        return get_fallback_github_data();
    }
    
    $repo_data = json_decode($response_body, true);
    
    if (!$repo_data) {
        error_log('GitHub API Debug: Failed to decode JSON response');
        return get_fallback_github_data();
    }
    
    // Contributors data not fetched for privacy reasons
    $contributors = [];
    
    // Fetch programming languages data
    $languages_response = wp_remote_get(GITHUB_LANGUAGES_URL, $args);
    $languages = [];
    if (!is_wp_error($languages_response) && wp_remote_retrieve_response_code($languages_response) === 200) {
        $languages = json_decode(wp_remote_retrieve_body($languages_response), true) ?: [];
    }
    
    // Fetch commits data for counting
    $commits_response = wp_remote_get(GITHUB_COMMITS_URL, $args);
    $commits = [];
    if (!is_wp_error($commits_response) && wp_remote_retrieve_response_code($commits_response) === 200) {
        $commits = json_decode(wp_remote_retrieve_body($commits_response), true) ?: [];
    }
    
    $github_data = [
        'full_name' => $repo_data['full_name'],
        'html_url' => $repo_data['html_url'],
        'stargazers_count' => $repo_data['stargazers_count'],
        'forks_count' => $repo_data['forks_count'],
        'watchers_count' => $repo_data['watchers_count'],
        'created_at' => $repo_data['created_at'],
        'updated_at' => $repo_data['updated_at'],
        'contributors' => $contributors,
        'languages' => $languages,
        'commits_count' => count($commits),
        'cached_at' => date('Y-m-d H:i:s'),
        'cache_source' => 'github_api'
    ];
    
    // Save to cache file
    $cache_success = file_put_contents($cache_file, json_encode($github_data, JSON_PRETTY_PRINT));
    if ($cache_success) {
        error_log('GitHub API Debug: Data cached successfully to ' . $cache_file);
    } else {
        error_log('GitHub API Debug: Failed to cache data to ' . $cache_file);
    }
    
    return $github_data;
}

// Function to get fallback data when GitHub API fails
function get_fallback_github_data() {
    // Try to get expired cache first
    $cache_file = plugin_dir_path(__FILE__) . '../../cache/cookiecutter_stats.json';
    if (file_exists($cache_file)) {
        $cached_data = json_decode(file_get_contents($cache_file), true);
        if ($cached_data) {
            error_log('GitHub API Debug: Using expired cache data');
            return $cached_data;
        }
    }
    
    error_log('GitHub API Debug: Using static fallback data');
    
    return [
        'full_name' => 'teamdigitale/dati-semantic-cookiecutter',
        'html_url' => 'https://github.com/teamdigitale/dati-semantic-cookiecutter',
        'stargazers_count' => 15,
        'forks_count' => 8,
        'watchers_count' => 12,
        'created_at' => '2023-01-15T10:30:00Z',
        'updated_at' => '2024-01-10T14:20:00Z',
        'contributors' => [],
        'languages' => [
            'Python' => 45000,
            'Jupyter Notebook' => 12000,
            'Shell' => 8000,
            'Dockerfile' => 2000
        ],
        'commits_count' => 67,
        'cached_at' => current_time('mysql'),
        'cache_source' => 'fallback_static'
    ];
}

// Fallback function with static data when GitHub API is blocked
function render_cookiecutter_stats_fallback($atts, $connectivity_status) {
    // Static data as fallback (last known values)
    $fallback_data = [
        'full_name' => 'teamdigitale/dati-semantic-cookiecutter',
        'html_url' => 'https://github.com/teamdigitale/dati-semantic-cookiecutter',
        'stargazers_count' => 15,
        'forks_count' => 8,
        'watchers_count' => 12,
        'created_at' => '2023-01-15T10:30:00Z',
        'updated_at' => '2024-01-10T14:20:00Z',
        'contributors' => [],
        'languages' => [
            'Python' => 45000,
            'Jupyter Notebook' => 12000,
            'Shell' => 8000,
            'Dockerfile' => 2000
        ],
        'commits_count' => 67
    ];
    
    ob_start();
    ?>
    <div class="cookiecutter-stats-container">
        <h3>📊 Statistiche Repository dati-semantic-cookiecutter</h3>
        <div class="stats-notice">
            <p><strong>⚠️ Dati statici (GitHub API bloccata)</strong></p>
            <p>Status: <?php echo esc_html($connectivity_status); ?></p>
        </div>
        <div class="stats-grid">
            
            <?php if ($atts['show_stars'] === 'true'): ?>
            <div class="stat-card">
                <div class="stat-icon">⭐</div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo esc_html($fallback_data['stargazers_count']); ?></div>
                    <div class="stat-label">Stars</div>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($atts['show_forks'] === 'true'): ?>
            <div class="stat-card">
                <div class="stat-icon">🍴</div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo esc_html($fallback_data['forks_count']); ?></div>
                    <div class="stat-label">Forks</div>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($atts['show_watchers'] === 'true'): ?>
            <div class="stat-card">
                <div class="stat-icon">👀</div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo esc_html($fallback_data['watchers_count']); ?></div>
                    <div class="stat-label">Watchers</div>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($atts['show_commits'] === 'true'): ?>
            <div class="stat-card">
                <div class="stat-icon">📝</div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo esc_html($fallback_data['commits_count']); ?></div>
                    <div class="stat-label">Commits</div>
                </div>
            </div>
            <?php endif; ?>
            
        </div>
        
        <?php if ($atts['show_languages'] === 'true' && !empty($fallback_data['languages'])): ?>
        <div class="languages-section">
            <h4>💻 Linguaggi</h4>
            <div class="languages-chart">
                <?php 
                $total_bytes = array_sum($fallback_data['languages']);
                foreach ($fallback_data['languages'] as $language => $bytes): 
                    $percentage = ($bytes / $total_bytes) * 100;
                ?>
                <div class="language-item">
                    <div class="language-name"><?php echo esc_html($language); ?></div>
                    <div class="language-bar">
                        <div class="language-fill" style="width: <?php echo esc_attr($percentage); ?>%"></div>
                    </div>
                    <div class="language-percentage"><?php echo esc_html(round($percentage, 1)); ?>%</div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="repo-info">
            <p><strong>Repository:</strong> 
                <a href="<?php echo esc_url($fallback_data['html_url']); ?>" target="_blank">
                    <?php echo esc_html($fallback_data['full_name']); ?>
                </a>
            </p>
            <p><strong>Ultimo aggiornamento:</strong> 
                <?php echo esc_html(date('d/m/Y H:i', strtotime($fallback_data['updated_at']))); ?>
            </p>
            <p><strong>Creato:</strong> 
                <?php echo esc_html(date('d/m/Y', strtotime($fallback_data['created_at']))); ?>
            </p>
        </div>
    </div>
    
    <style>
    .cookiecutter-stats-container {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin: 20px 0;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }
    
    .stats-notice {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 6px;
        padding: 10px;
        margin-bottom: 20px;
        color: #856404;
    }
    
    .cookiecutter-stats-container h3 {
        margin-bottom: 20px;
        color: #333;
        border-bottom: 2px solid #007cba;
        padding-bottom: 10px;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        border: 1px solid #e9ecef;
        transition: transform 0.2s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .stat-icon {
        font-size: 24px;
        margin-bottom: 8px;
    }
    
    .stat-number {
        font-size: 24px;
        font-weight: bold;
        color: #007cba;
        margin-bottom: 5px;
    }
    
    .stat-label {
        font-size: 14px;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .languages-section {
        margin-bottom: 25px;
    }
    
    .languages-section h4 {
        margin-bottom: 15px;
        color: #333;
        font-size: 18px;
    }
    
    .languages-chart {
        background: #f8f9fa;
        border-radius: 6px;
        padding: 15px;
        border: 1px solid #e9ecef;
    }
    
    .language-item {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }
    
    .language-name {
        width: 100px;
        font-weight: bold;
        color: #333;
    }
    
    .language-bar {
        flex: 1;
        height: 20px;
        background: #e9ecef;
        border-radius: 10px;
        margin: 0 10px;
        overflow: hidden;
    }
    
    .language-fill {
        height: 100%;
        background: linear-gradient(90deg, #007cba, #0056b3);
        transition: width 0.3s ease;
    }
    
    .language-percentage {
        width: 50px;
        text-align: right;
        font-size: 14px;
        color: #666;
    }
    
    .repo-info {
        background: #f8f9fa;
        border-radius: 6px;
        padding: 15px;
        border: 1px solid #e9ecef;
        font-size: 14px;
    }
    
    .repo-info p {
        margin: 5px 0;
        color: #333;
    }
    
    .repo-info a {
        color: #007cba;
        text-decoration: none;
    }
    
    .repo-info a:hover {
        text-decoration: underline;
    }
    </style>
    <?php
    return ob_get_clean();
}