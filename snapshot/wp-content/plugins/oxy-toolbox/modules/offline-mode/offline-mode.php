<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class Oxy_Toolbox_OfflineMode {

	static $prefix;
	static $mod = 'offline_mode_';
	static $title = 'Offline Mode';
	static $description = 'Enables you to use the Oxygen editor locally without an internet connection.';
	static $link = 'https://oxyplugins.com/doc/offline-mode/';
	
	static function init( $prefix ) {
		self::$prefix = $prefix;
		$oxy_toolbox_mod = new Oxy_Toolbox_Mod( $prefix, self::$mod, self::$title, self::$description, self::$link );

		// this will block the rest of the mod to load, if it is not checked.
		if ( 0 === intval( get_option( self::$prefix . self::$mod, 0 ) ) || OxyToolboxLicense::is_activated_license() !== true ) {
			return;
		}

		// Module's scripts.
		add_action( self::$prefix . 'enqueue_scripts', array( __CLASS__, 'mod_scripts' ), 1 );

		// Custom WP Admin styles.
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'load_custom_wp_admin_style' ), 11 );
	}

	static function mod_scripts( $version ) {
		if ( ! defined( 'SHOW_CT_BUILDER' ) ) {
			return;
		}

		add_action( 'wp_print_styles', array( __CLASS__, 'dequeue_unnecessary_styles' ), 100 );
		add_action( 'wp_print_scripts', array( __CLASS__, 'dequeue_unnecessary_scripts' ), 100 );

		wp_enqueue_style( self::$prefix . self::$mod . 'fixfont', plugins_url( 'css/fixfont.css', __FILE__ ), [], $version );
	}

	static function load_custom_wp_admin_style( $version ) {
		$css = "
			@font-face {
				font-family: 'Source Sans Pro', serif;
				src: url('fonts/sourcesanspro-regular-webfont.woff2') format('woff2'),
					url('fonts/sourcesanspro-regular-webfont.woff') format('woff');
				font-weight: normal;
				font-style: normal;
			}

			#ct_views_cpt, #ct_connection_metabox {
				font-family: 'Source Sans Pro', 'system-ui', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif;
			}
		";
	
		wp_add_inline_style( 'ct-admin-style', $css );
	}

	static function dequeue_unnecessary_styles() {
		wp_deregister_style( 'font-awesome' );
		wp_deregister_style( 'jquery-ui-css' );

		wp_enqueue_style( 'font-awesome', plugins_url( 'css/font-awesome.min.css', __FILE__ ), [], '4.3.0' );
		wp_enqueue_style( 'jquery-ui-css', plugins_url( 'css/jquery-ui.css', __FILE__ ), [], '5.4.1' );
	}

	static function dequeue_unnecessary_scripts() {
		wp_deregister_script( 'font-loader' );
		wp_deregister_script( 'angular' );
		wp_deregister_script( 'angular-animate' );
	
		wp_enqueue_script( 'font-loader', plugins_url( 'js/webfont.js', __FILE__ ), [], false, false );
		wp_enqueue_script( 'angular', plugins_url( 'js/angular.js', __FILE__ ), [], '1.4.2', false );
		wp_enqueue_script( 'angular-animate', plugins_url( 'js/angular-animate.js', __FILE__ ), [], '1.4.2', false );
	}
}

Oxy_Toolbox_OfflineMode::init( self::PREFIX );