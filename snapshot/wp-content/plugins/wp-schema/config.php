<?php

// API Base URL for the main application
define('WP_SCHEMA_API_BASE_URL', 'https://ndc-dev.apps.cloudpub.testedev.istat.it/api/');

// Matomo Analytics API Configuration
define('MATOMO_API_URL', 'https://analytics.istat.it/?module=API'); // Base URL for Matomo API
define('MATOMO_TOKEN', 'ef124ba7d106b8f55d64670f506b8ac6'); // Authentication token for Matomo API
define('MATOMO_SITE_ID', 33); // ID of the site in Matomo
define('MATOMO_USER', 'dtd'); // Username for Matomo API authentication
define('MATOMO_PASS', 'NDCstats2024'); // Password for Matomo API authentication

// GitHub API Configuration for Cookie Cutter Statistics
// Used in: GeneralFunctions.php -> fetch_github_repo_data() function
// Purpose: Fetch repository statistics for dati-semantic-cookiecutter
define('GITHUB_API_BASE_URL', 'https://api.github.com'); // Base URL for GitHub API
define('GITHUB_REPO_OWNER', 'teamdigitale'); // Repository owner/organization
define('GITHUB_REPO_NAME', 'dati-semantic-cookiecutter'); // Repository name
define('GITHUB_REPO_URL', GITHUB_API_BASE_URL . '/repos/' . GITHUB_REPO_OWNER . '/' . GITHUB_REPO_NAME); // Full repository API URL
define('GITHUB_CONTRIBUTORS_URL', GITHUB_REPO_URL . '/contributors'); // Contributors endpoint URL
define('GITHUB_LANGUAGES_URL', GITHUB_REPO_URL . '/languages'); // Languages endpoint URL
define('GITHUB_COMMITS_URL', GITHUB_REPO_URL . '/commits'); // Commits endpoint URL

// Status mapping for resource chips display
function get_status_chip($status) {
    $status = strtolower($status);
    switch ($status) {
        case 'catalogued':
        case 'published':
            return '<span class="chip-status chip-green">Stabile</span>';
        case 'archived':
            return '<span class="chip-status chip-grey">Archiviato</span>';
        case 'closed access':
            return '<span class="chip-status chip-grey">Accesso ristretto</span>';
        case 'initial draft':
        case 'draft':
        case 'final draft':
        case 'intermediate draft':
        case 'submitted':
            return '<span class="chip-status chip-grey">Bozza</span>';
        default:
            return '';
    }
}