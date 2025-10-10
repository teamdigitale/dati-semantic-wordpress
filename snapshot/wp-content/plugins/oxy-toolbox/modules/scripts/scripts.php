<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Oxy_Toolbox_Scripts {

	static $prefix;
	static $mod = 'scripts_';
	static $title = 'Scripts';
	static $description = 'Registers popular scripts like Flickity and Isotope so you can load them anywhere in Oxygen.';
	static $link = 'https://oxyplugins.com/doc/scripts/';

	public static function init( $prefix ) {
		self::$prefix = $prefix;
		$oxy_toolbox_mod = new Oxy_Toolbox_Mod( $prefix, self::$mod, self::$title, self::$description, self::$link );

		// this will block the rest of the mod to load, if it is not checked.
		if ( 0 === intval( get_option( self::$prefix . self::$mod, 0 ) ) || OxyToolboxLicense::is_activated_license() !== true) {
			return;
		}

		add_action( self::$prefix . 'enqueue_scripts', array( __CLASS__, 'mod_scripts' ) );
	}

	public static function mod_scripts() {

		// Flickity.
		wp_register_style( self::$prefix . self::$mod . 'flickity', plugins_url( 'css/flickity/flickity.min.css', __FILE__ ), [], '3.0.0' );
		wp_register_script( self::$prefix . self::$mod . 'flickity', plugins_url( 'js/flickity/flickity.pkgd.min.js', __FILE__ ), [], '3.0.0', true );

		// Glightbox.
		wp_register_style( self::$prefix . self::$mod . 'glightbox', plugins_url( 'css/glightbox/glightbox.min.css', __FILE__ ), [], '3.2.0' );
		wp_register_script( self::$prefix . self::$mod . 'glightbox', plugins_url( 'js/glightbox/glightbox.min.js', __FILE__ ), [], '3.2.0', true );

		// Isotope.
		wp_register_script( self::$prefix . self::$mod . 'isotope', plugins_url( 'js/isotope/isotope.pkgd.min.js', __FILE__ ), [ 'jquery' ], '3.0.6', true );

		// imagesLoaded.
		wp_register_script( self::$prefix . self::$mod . 'imagesloaded', plugins_url( 'js/imagesloaded/imagesloaded.pkgd.min.js', __FILE__ ), [], '5.0.0', true );

		// Infinite Scroll.
		wp_register_script( self::$prefix . self::$mod . 'infinite-scroll', plugins_url( 'js/infinite-scroll/infinite-scroll.pkgd.min.js', __FILE__ ), [], '4.0.1', true );

		// Splide.
		wp_register_style( self::$prefix . self::$mod . 'splide', plugins_url( 'css/splide/splide.min.css', __FILE__ ), [], '4.0.2' );
		wp_register_script( self::$prefix . self::$mod . 'splide', plugins_url( 'js/splide/splide.min.js', __FILE__ ), [], '4.0.2', true );

		// Headroom.
		wp_register_script( self::$prefix . self::$mod . 'headroom', plugins_url( 'js/headroom/headroom.min.js', __FILE__ ), [], '0.12.0', true );
	}

}

Oxy_Toolbox_Scripts::init( self::PREFIX );