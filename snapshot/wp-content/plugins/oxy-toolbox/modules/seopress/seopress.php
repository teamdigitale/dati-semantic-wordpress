<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Oxy_Toolbox_SEOPress {

	static $prefix;
	static $mod = 'seopress_';
	static $title = 'SEOPress';
	static $description = 'Generates automatic meta description from Oxygen Builder content.';
	static $link = 'https://oxyplugins.com/doc/seopress/';
	
	static function init( $prefix ) {
		self::$prefix = $prefix;
		$oxy_toolbox_mod = new Oxy_Toolbox_Mod( $prefix, self::$mod, self::$title, self::$description, self::$link );

		// this will block the rest of the mod to load, if it is not checked.
		if ( 0 === intval( get_option( self::$prefix . self::$mod, 0 ) ) || OxyToolboxLicense::is_activated_license() !== true) {
			return;
		}

		// check if Rank Math is active.
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if ( ! is_plugin_active( 'wp-seopress/seopress.php' ) ) {
			return;
		}

		add_filter( 'seopress_titles_template_variables_array', array( __CLASS__, 'sp_titles_template_variables_array') );
		
		add_filter( 'seopress_titles_template_replace_array', array( __CLASS__, 'sp_titles_template_replace_array') );

	}

	static function sp_titles_template_variables_array( $array ) {
		$array[] = '%%oxygen%%';

		return $array;
	}
	
	static function sp_titles_template_replace_array( $array ) {
		if ( ! isset( $_GET['ct_builder'] ) ) {
			add_filter( 'wp_doing_ajax', '__return_true' );
			$content = do_shortcode( get_post_meta( get_the_id(), 'ct_builder_shortcodes', true) );
			remove_filter( 'wp_doing_ajax', '__return_true' );

			$array[] = preg_replace( '/\r|\n/',  '',  substr( strip_tags( wp_filter_nohtml_kses( $content ) ), 0, 160 ) );
		}

		return $array;
	}
	
}

Oxy_Toolbox_SEOPress::init( self::PREFIX );