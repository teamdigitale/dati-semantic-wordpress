<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class Oxy_Toolbox_Essentials {

	static $prefix;
	static $mod = 'essentials_';
	static $title = 'Essentials';
	static $description = "Enables HTML5 support for elements like the search form and adds (what we consider to be) some essential CSS – for example, to make all the images responsive.";
	static $link = 'https://oxyplugins.com/doc/essentials/';
	
	static function init( $prefix ) {
		self::$prefix = $prefix;
		$oxy_toolbox_mod = new Oxy_Toolbox_Mod( $prefix, self::$mod, self::$title, self::$description, self::$link );

		// this will block the rest of the mod to load, if it is not checked.
		if ( 0 === intval( get_option( self::$prefix . self::$mod, 0 ) ) || OxyToolboxLicense::is_activated_license() !== true) {
			return;
		}

		// Enable HTML5 Support.
		add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'style', 'script' ) );

		add_action( self::$prefix . 'enqueue_scripts', array( __CLASS__, 'mod_scripts' ) );
	}

	static function mod_scripts( $version ) {

		wp_enqueue_style( self::$prefix . self::$mod . 'style', plugins_url( 'css/style.css', __FILE__ ), array(), $version );
	}
	
}

Oxy_Toolbox_Essentials::init( self::PREFIX );