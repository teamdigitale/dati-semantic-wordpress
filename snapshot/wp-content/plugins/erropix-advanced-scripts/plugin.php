<?php

/**
 * Plugin Name: Advanced Scripts
 * Plugin URI: https://www.cleanplugins.com/products/advanced-scripts/
 * Description: A plugin that allow you to create custom scripts and styles
 * Version: 2.6.1
 * Update URI: https://api.freemius.com
 * Requires PHP: 7.0
 * Requires at least: 5.0
 * Author: Clean Plugins
 * Author URI: https://www.cleanplugins.com/
 **/

// don't load directly
if (!defined('ABSPATH')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

// Plugin constants
define('EPXADVSC_VER', '2.6.1');
define('EPXADVSC_PHP_VERSION', '7.0');
define('EPXADVSC_BASE', plugin_basename(__FILE__));
define('EPXADVSC_URL', plugin_dir_url(__FILE__));
define('EPXADVSC_DIR', plugin_dir_path(__FILE__));

// Require the minimum PHP version
if (version_compare(PHP_VERSION, EPXADVSC_PHP_VERSION, '<')) {
    add_action('admin_notices', function () {
        echo '<div class="error notice"><p>Advanced Scripts require PHP version ' . EPXADVSC_PHP_VERSION . ' or newer</p></div>';
    });
} else {
    // Load the plugin class
    require EPXADVSC_DIR . 'vendor/autoload.php';

    cpas_scripts_manager();
}
