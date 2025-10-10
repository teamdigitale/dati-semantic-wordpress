<?php
/**
 * Plugin Name: WP Schema
 * Description: Plugin for managing JSON-LD schemas in WordPress
 * Version: 1.0
 * Author: LuZen
 */

// Page ID for the search page
$IdSearchPage = 107;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
require_once(ABSPATH . 'wp-load.php');

// Core configuration and dependencies
require_once plugin_dir_path(__FILE__) . 'config.php';
require_once plugin_dir_path(__FILE__) . 'functions/CacheManager.php';
require_once plugin_dir_path(__FILE__) . 'functions/ThemesDataHelper.php';
require_once plugin_dir_path(__FILE__) . 'functions/HelperFunctions.php';
require_once plugin_dir_path(__FILE__) . 'functions/AjaxHandlers.php';
require_once plugin_dir_path(__FILE__) . 'templates/search.php';
require_once plugin_dir_path(__FILE__) . 'templates/details.php';
require_once plugin_dir_path(__FILE__) . 'functions/GetResourceDetails.php';
require_once plugin_dir_path(__FILE__) . 'functions/GetAPIDetails.php';
require_once plugin_dir_path(__FILE__) . 'functions/SwaggerUI.php';
require_once plugin_dir_path(__FILE__) . 'functions/Validator.php';
require_once plugin_dir_path(__FILE__) . 'functions/SchemaEditor.php';
require_once plugin_dir_path(__FILE__) . 'functions/Dashboard/ResourceEvolution.php';
require_once plugin_dir_path(__FILE__) . 'functions/Dashboard/ResourceHolders.php';
require_once plugin_dir_path(__FILE__) . 'functions/Dashboard/ResourceHoldersTimeDistribution.php';
require_once plugin_dir_path(__FILE__) . 'functions/Dashboard/ResourceHoldersCustomTimeDistribution.php';
require_once plugin_dir_path(__FILE__) . 'functions/Dashboard/WeeklyHolderDistribution.php';
require_once plugin_dir_path(__FILE__) . 'functions/Dashboard/YearlyHolderDistribution.php';
require_once plugin_dir_path(__FILE__) . 'functions/Dashboard/HarvesterExecutionTime.php';
require_once plugin_dir_path(__FILE__) . 'functions/Dashboard/MatomoStats.php';
require_once plugin_dir_path(__FILE__) . 'functions/Dashboard/MatomoVisitsPerYear.php';
require_once plugin_dir_path(__FILE__) . 'functions/Dashboard/GeneralStats.php';
require_once plugin_dir_path(__FILE__) . 'functions/Dashboard/CookieCutterStats.php';

// Plugin activation hook
register_activation_hook(__FILE__, 'wp_schema_activate');

function wp_schema_activate() {
    // Create the cache table on plugin activation
    ensure_resource_transients_table();
}

function wp_schema_enqueue_assets() {
    // Fonts CSS (must be loaded first)
    wp_enqueue_style('wp-schema-fonts', plugin_dir_url(__FILE__) . 'assets/css/fonts.css');
    
    // Main CSS
    wp_enqueue_style('wp-schema-main', plugin_dir_url(__FILE__) . 'assets/css/main.css');
    
    // Search JavaScript (avoid duplicate enqueue)
    if (!wp_script_is('wp-schema-search', 'enqueued')) {
        wp_enqueue_script('wp-schema-search', plugin_dir_url(__FILE__) . 'assets/js/search.js', array('jquery'), '1.0', true);
    }

    // Pass AJAX URL to JS
    wp_localize_script('wp-schema-search', 'ajax_object', ['ajax_url' => admin_url('admin-ajax.php')]);

    // Details JavaScript
    if (!wp_script_is('wp-schema-details', 'enqueued')) {
        wp_enqueue_script('wp-schema-details', plugin_dir_url(__FILE__) . 'assets/js/details.js', array('jquery'), '1.0', true);
    }

    // ECharts JavaScript
    if (!wp_script_is('wp-schema-echarts', 'enqueued')) {
        wp_enqueue_script('wp-schema-echarts', plugin_dir_url(__FILE__) . 'assets/js/echarts.js', array('jquery'), '1.0', true);
    }
}
add_action('wp_enqueue_scripts', 'wp_schema_enqueue_assets');