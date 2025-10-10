<?php 
function fetch_stats($year = null) {
    // If year is not provided, use the current year
    if (is_null($year)) {
        $year = date('Y');
    }

    $api_url = WP_SCHEMA_API_BASE_URL . 'semantic-assets/stats?year=' . urlencode($year);
    $response = file_get_contents($api_url);
    return json_decode($response, true);
}

function formatValue($value) {
    // Add + sign to values >= 0
    return ($value >= 0 ? '+' : '') . $value;
}

function GetStatsTotal($year = null) {
    $stats = fetch_stats($year);
    return $stats['total']['current'];
}

function GetStatsTotalOverLastYear($year = null) {
    $stats = fetch_stats($year);
    return formatValue($stats['total']['incrementOverLastYear']);
}

function GetStatsTotalPercentageOverLastYear($year = null) {
    $stats = fetch_stats($year);
    return formatValue($stats['total']['incrementPercentageOverLastYear']);
}

function GetStatsCV($year = null) {
    $stats = fetch_stats($year);
    return $stats['controlledVocabulary']['current'];
}

function GetStatsCVOverLastYear($year = null) {
    $stats = fetch_stats($year);
    return formatValue($stats['controlledVocabulary']['incrementOverLastYear']);
}

function GetStatsCVPercentageOverLastYear($year = null) {
    $stats = fetch_stats($year);
    return formatValue($stats['controlledVocabulary']['incrementPercentageOverLastYear']);
}

function GetStatsOntology($year = null) {
    $stats = fetch_stats($year);
    return $stats['ontology']['current'];
}

function GetStatsOntologyOverLastYear($year = null) {
    $stats = fetch_stats($year);
    return formatValue($stats['ontology']['incrementOverLastYear']);
}

function GetStatsOntologyPercentageOverLastYear($year = null) {
    $stats = fetch_stats($year);
    return formatValue($stats['ontology']['incrementPercentageOverLastYear']);
}

function GetStatsSchema($year = null) {
    $stats = fetch_stats($year);
    return $stats['schema']['current'];
}

function GetStatsSchemaOverLastYear($year = null) {
    $stats = fetch_stats($year);
    return formatValue($stats['schema']['incrementOverLastYear']);
}

function GetStatsSchemaPercentageOverLastYear($year = null) {
    $stats = fetch_stats($year);
    return formatValue($stats['schema']['incrementPercentageOverLastYear']);
}

function PrintAllStats($year = null) {
    $stats = fetch_stats($year);
    echo "<pre>";
    print_r($stats);
    echo "</pre>";
}

function GetStatsHolders($year = null) {
    // Get rights holder names
    $rights_holders_url = WP_SCHEMA_API_BASE_URL . 'semantic-assets/rights-holders';
    $rights_holders_response = wp_remote_get($rights_holders_url);
    $holders = [];
    
    if (!is_wp_error($rights_holders_response)) {
        $rights_holders_body = wp_remote_retrieve_body($rights_holders_response);
        $rights_holders = json_decode($rights_holders_body, true);
        if (is_array($rights_holders)) {
            foreach ($rights_holders as $holder) {
                if (isset($holder['identifier']) && isset($holder['name']['it'])) {
                    $holders[] = $holder['name']['it'];
                }
            }
        }
    }
    
    return count($holders);
}

function GetStatsHoldersOverLastYear($year = null) {
    // If year is not provided, use the current year
    if (is_null($year)) {
        $year = date('Y');
    }
    
    // Get current year holders
    $current_holders = GetStatsHolders($year);
    if ($current_holders === false) {
        return '';
    }
    
    // Get previous year holders
    $previous_year = $year - 1;
    $previous_holders = GetStatsHolders($previous_year);
    if ($previous_holders === false) {
        return '';
    }
    
    // Calculate increment
    $increment = $current_holders - $previous_holders;
    return formatValue($increment);
}

function GetStatsHoldersPercentageOverLastYear($year = null) {
    // If year is not provided, use the current year
    if (is_null($year)) {
        $year = date('Y');
    }
    
    // Get current year holders
    $current_holders = GetStatsHolders($year);
    if ($current_holders === false) {
        return '';
    }
    
    // Get previous year holders
    $previous_year = $year - 1;
    $previous_holders = GetStatsHolders($previous_year);
    if ($previous_holders === false || $previous_holders === 0) {
        return '';
    }
    
    // Calculate percentage increment
    $percentage = (($current_holders - $previous_holders) / $previous_holders) * 100;
    return formatValue(round($percentage, 1));
}

// Shortcode: [wp_schema_stats_total]
// Returns the total number of resources for the current year or for the specified year (e.g., [wp_schema_stats_total year="2023"]).
function wp_schema_stats_total_shortcode($atts) {
    $atts = shortcode_atts(['year' => null], $atts);
    return GetStatsTotal($atts['year']);
}
add_shortcode('wp_schema_stats_total', 'wp_schema_stats_total_shortcode');

// Shortcode: [wp_schema_stats_total_over_last_year]
// Returns the absolute increment of resources compared to the previous year.
function wp_schema_stats_total_over_last_year_shortcode($atts) {
    $atts = shortcode_atts(['year' => null], $atts);
    return GetStatsTotalOverLastYear($atts['year']);
}
add_shortcode('wp_schema_stats_total_over_last_year', 'wp_schema_stats_total_over_last_year_shortcode');

// Shortcode: [wp_schema_stats_total_percentage_over_last_year]
// Returns the percentage increment of resources compared to the previous year.
function wp_schema_stats_total_percentage_over_last_year_shortcode($atts) {
    $atts = shortcode_atts(['year' => null], $atts);
    return GetStatsTotalPercentageOverLastYear($atts['year']);
}
add_shortcode('wp_schema_stats_total_percentage_over_last_year', 'wp_schema_stats_total_percentage_over_last_year_shortcode');

// Shortcode: [wp_schema_stats_cv]
// Returns the total number of controlled vocabularies.
function wp_schema_stats_cv_shortcode($atts) {
    $atts = shortcode_atts(['year' => null], $atts);
    return GetStatsCV($atts['year']);
}
add_shortcode('wp_schema_stats_cv', 'wp_schema_stats_cv_shortcode');

// Shortcode: [wp_schema_stats_cv_over_last_year]
// Returns the absolute increment of controlled vocabularies compared to the previous year.
function wp_schema_stats_cv_over_last_year_shortcode($atts) {
    $atts = shortcode_atts(['year' => null], $atts);
    return GetStatsCVOverLastYear($atts['year']);
}
add_shortcode('wp_schema_stats_cv_over_last_year', 'wp_schema_stats_cv_over_last_year_shortcode');

// Shortcode: [wp_schema_stats_cv_percentage_over_last_year]
// Returns the percentage increment of controlled vocabularies compared to the previous year.
function wp_schema_stats_cv_percentage_over_last_year_shortcode($atts) {
    $atts = shortcode_atts(['year' => null], $atts);
    return GetStatsCVPercentageOverLastYear($atts['year']);
}
add_shortcode('wp_schema_stats_cv_percentage_over_last_year', 'wp_schema_stats_cv_percentage_over_last_year_shortcode');

// Shortcode: [wp_schema_stats_ontology]
// Returns the total number of ontologies.
function wp_schema_stats_ontology_shortcode($atts) {
    $atts = shortcode_atts(['year' => null], $atts);
    return GetStatsOntology($atts['year']);
}
add_shortcode('wp_schema_stats_ontology', 'wp_schema_stats_ontology_shortcode');

// Shortcode: [wp_schema_stats_ontology_over_last_year]
// Returns the absolute increment of ontologies compared to the previous year.
function wp_schema_stats_ontology_over_last_year_shortcode($atts) {
    $atts = shortcode_atts(['year' => null], $atts);
    return GetStatsOntologyOverLastYear($atts['year']);
}
add_shortcode('wp_schema_stats_ontology_over_last_year', 'wp_schema_stats_ontology_over_last_year_shortcode');

// Shortcode: [wp_schema_stats_ontology_percentage_over_last_year]
// Returns the percentage increment of ontologies compared to the previous year.
function wp_schema_stats_ontology_percentage_over_last_year_shortcode($atts) {
    $atts = shortcode_atts(['year' => null], $atts);
    return GetStatsOntologyPercentageOverLastYear($atts['year']);
}
add_shortcode('wp_schema_stats_ontology_percentage_over_last_year', 'wp_schema_stats_ontology_percentage_over_last_year_shortcode');

// Shortcode: [wp_schema_stats_schema]
// Returns the total number of schemas.
function wp_schema_stats_schema_shortcode($atts) {
    $atts = shortcode_atts(['year' => null], $atts);
    return GetStatsSchema($atts['year']);
}
add_shortcode('wp_schema_stats_schema', 'wp_schema_stats_schema_shortcode');

// Shortcode: [wp_schema_stats_schema_over_last_year]
// Returns the absolute increment of schemas compared to the previous year.
function wp_schema_stats_schema_over_last_year_shortcode($atts) {
    $atts = shortcode_atts(['year' => null], $atts);
    return GetStatsSchemaOverLastYear($atts['year']);
}
add_shortcode('wp_schema_stats_schema_over_last_year', 'wp_schema_stats_schema_over_last_year_shortcode');

// Shortcode: [wp_schema_stats_schema_percentage_over_last_year]
// Returns the percentage increment of schemas compared to the previous year.
function wp_schema_stats_schema_percentage_over_last_year_shortcode($atts) {
    $atts = shortcode_atts(['year' => null], $atts);
    return GetStatsSchemaPercentageOverLastYear($atts['year']);
}
add_shortcode('wp_schema_stats_schema_percentage_over_last_year', 'wp_schema_stats_schema_percentage_over_last_year_shortcode');

// Shortcode: [wp_schema_print_all_stats]
// Prints all statistics in <pre> format (debug, optional year: [wp_schema_print_all_stats year="2023"]).
function wp_schema_print_all_stats_shortcode($atts) {
    $atts = shortcode_atts(['year' => null], $atts);
    ob_start();
    PrintAllStats($atts['year']);
    return ob_get_clean();
}
add_shortcode('wp_schema_print_all_stats', 'wp_schema_print_all_stats_shortcode');

// Shortcode: [wp_schema_stats_holders]
// Returns the total number of holders
function wp_schema_stats_holders_shortcode($atts) {
    $atts = shortcode_atts(['year' => null], $atts);
    return GetStatsHolders($atts['year']);
}
add_shortcode('wp_schema_stats_holders', 'wp_schema_stats_holders_shortcode');

// Shortcode: [wp_schema_stats_holders_over_last_year]
// Returns the absolute increment of holders compared to the previous year
function wp_schema_stats_holders_over_last_year_shortcode($atts) {
    $atts = shortcode_atts(['year' => null], $atts);
    return GetStatsHoldersOverLastYear($atts['year']);
}
add_shortcode('wp_schema_stats_holders_over_last_year', 'wp_schema_stats_holders_over_last_year_shortcode');

// Shortcode: [wp_schema_stats_holders_percentage_over_last_year]
// Returns the percentage increment of holders compared to the previous year
function wp_schema_stats_holders_percentage_over_last_year_shortcode($atts) {
    $atts = shortcode_atts(['year' => null], $atts);
    return GetStatsHoldersPercentageOverLastYear($atts['year']);
}
add_shortcode('wp_schema_stats_holders_percentage_over_last_year', 'wp_schema_stats_holders_percentage_over_last_year_shortcode');