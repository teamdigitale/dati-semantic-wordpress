<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class Oxy_Toolbox_BackgroundImageLazyLoading {

	static $prefix;
	static $mod = 'background_image_lazy_loading_';
	static $title = 'Background Image Lazy Loading';
	static $description = "Enables you to lazy-load background images. Add `lazy-background` class to the element. Set the background image via CSS for the `&lt;element's ID&gt;.visible` selector.";
	static $link = 'https://oxyplugins.com/doc/background-image-lazy-loading/';
	
	static function init( $prefix ) {
		self::$prefix = $prefix;
		$oxy_toolbox_mod = new Oxy_Toolbox_Mod( $prefix, self::$mod, self::$title, self::$description, self::$link );

		// this will block the rest of the mod to load, if it is not checked.
		if ( 0 === intval( get_option( self::$prefix . self::$mod, 0 ) ) || OxyToolboxLicense::is_activated_license() !== true) {
			return;
		}

		add_action( self::$prefix . 'enqueue_scripts', array( __CLASS__, 'mod_scripts' ) );
	}

	static function mod_scripts( $version ) {

        wp_enqueue_script( self::$prefix . self::$mod . 'script', plugins_url( 'js/script.js', __FILE__ ), array(), $version, true );
	}
	
}

Oxy_Toolbox_BackgroundImageLazyLoading::init( self::PREFIX );