<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class Oxy_Toolbox_BackToTop {

	static $prefix;
	static $mod = 'back_to_top_';
	static $title = 'Back To Top';
	static $description = 'Allows users to smoothly scroll back to the top of the page.';
	static $link = 'https://oxyplugins.com/doc/back-to-top/';
	
	static function init( $prefix ) {
		self::$prefix = $prefix;
		$oxy_toolbox_mod = new Oxy_Toolbox_Mod( $prefix, self::$mod, self::$title, self::$description, self::$link );

		// this will block the rest of the mod to load, if it is not checked.
		if ( 0 === intval( get_option( self::$prefix . self::$mod, 0 ) ) || OxyToolboxLicense::is_activated_license() !== true) {
			return;
		}

		add_action( 'wp_head', array( __CLASS__, 'add_js_to_html' ) );
		add_action( 'wp_footer', array( __CLASS__, 'back_to_top_html' ) );

		add_action( self::$prefix . 'enqueue_scripts', array( __CLASS__, 'mod_scripts' ), 1 );
	}

	static function add_js_to_html() { ?>
		<script>document.getElementsByTagName("html")[0].className += " js";</script>
	<?php }

	static function back_to_top_html() { ?>
		<a href="#" class="cd-top text-replace js-cd-top">Top</a>
	<?php }

	static function mod_scripts( $version ) {

		wp_enqueue_style( self::$prefix . self::$mod . 'style', plugins_url( 'css/style.css', __FILE__ ), [], $version );

		wp_register_script( self::$prefix . self::$mod . 'script', plugins_url( 'js/script.js', __FILE__ ), [], $version, true );
			
		wp_enqueue_script( self::$prefix . self::$mod . 'script' );
	}

}

Oxy_Toolbox_BackToTop::init( self::PREFIX );