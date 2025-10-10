<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Oxy_Toolbox_Emmet {
	
	static $prefix;
	static $mod = 'emmet_';
	static $title = 'Code Completion';
	static $description = 'Write HTML and CSS faster using Emmet.';
	static $link = 'https://oxyplugins.com/doc/code-completion/';
	
	static function init( $prefix ) {
		
		self::$prefix = $prefix;

		$oxy_toolbox_mod = new Oxy_Toolbox_Mod( $prefix, self::$mod, self::$title, self::$description, self::$link );
		
		// this will block the rest of the mod to load, if it is not checked.
		if ( 0 === intval( get_option( self::$prefix . self::$mod, 0 ) ) || OxyToolboxLicense::is_activated_license() !== true) {
			return;
		}
		

		add_action( self::$prefix . 'enqueue_scripts', array( __CLASS__, 'mod_scripts' ), 1 );

		
	}

	static function mod_scripts( $version ) {

		if ( ! defined( 'SHOW_CT_BUILDER' ) ) {
			return;
		}

		if ( defined( 'OXYGEN_IFRAME' ) ) {
			wp_enqueue_script( self::$prefix . self::$mod . 'script', plugins_url( 'js/script.js', __FILE__ ), array( 'ct-angular-main' ), $version );
		} else {
			wp_enqueue_script( self::$prefix . self::$mod . 'browser', plugins_url( 'js/browser.js', __FILE__ ), array(), $version );
		}
	}

	
}

Oxy_Toolbox_Emmet::init( self::PREFIX );