<?php
/**
 * Plugin Name: WP Bootstrap Italia
 * Plugin URI: https://tuosito.it/wp-bootstrap-italia
 * Description: Integra Bootstrap Italia nel tuo sito WordPress.
 * Version: 1.0.0
 * Author: I
 * Author URI: I
 * License: GPL2
 * Text Domain: wp-bootstrap-italia
 */

// Evita l'accesso diretto
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

define( 'WP_BOOTSTRAP_ITALIA_VERSION', '1.0.0' );
define( 'WP_BOOTSTRAP_ITALIA_DIR', plugin_dir_path( __FILE__ ) );
define( 'WP_BOOTSTRAP_ITALIA_URL', plugin_dir_url( __FILE__ ) );

// Funzione per caricare CSS e JS
function wp_bootstrap_italia_enqueue_assets() {
    // Bootstrap Italia CSS
    wp_enqueue_style(
        'bootstrap-italia',
        WP_BOOTSTRAP_ITALIA_URL . 'css/bootstrap-italia.min.css',
        array(),
        WP_BOOTSTRAP_ITALIA_VERSION
    );

    // Font di Bootstrap Italia
//    wp_enqueue_style(
//        'bootstrap-italia-fonts',
//        WP_BOOTSTRAP_ITALIA_URL . 'fonts/bootstrap-italia-fonts.css',
//        array('bootstrap-italia'),
//        WP_BOOTSTRAP_ITALIA_VERSION
//    );

    // Popper.js
    wp_enqueue_script(
        'popper-js',
        WP_BOOTSTRAP_ITALIA_URL . 'js/popper.min.js',
        array('jquery'), // Dipendenze, se necessarie
        '2.11.6', // Versione di Popper.js
        true
    );

    // Bootstrap Italia JS
    wp_enqueue_script(
        'bootstrap-italia',
        WP_BOOTSTRAP_ITALIA_URL . 'js/bootstrap-italia.min.js',
        array('jquery'), // Dipendenze, se necessarie
        WP_BOOTSTRAP_ITALIA_VERSION,
        true
    );

    // RIMOSSO: bootstrap-italia.ems.js e version.js perché moduli ES6 non compatibili senza type="module"

}
add_action( 'wp_enqueue_scripts', 'wp_bootstrap_italia_enqueue_assets' );

// Includi le funzioni del menu Bootstrap Italia
require_once WP_BOOTSTRAP_ITALIA_DIR . 'wp-plugin-functions.php';

?>