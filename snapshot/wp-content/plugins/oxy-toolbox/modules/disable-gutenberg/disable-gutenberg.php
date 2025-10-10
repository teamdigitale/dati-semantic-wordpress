<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class Oxy_Toolbox_DisableGutenberg {

	static $prefix;
	static $mod = 'disable_gutenberg_';
	static $title = 'Disable Gutenberg';
	static $description = 'Disables Gutenberg WordPress editor and unloads Gutenberg-related stylesheets.';
	static $link = 'https://oxyplugins.com/doc/disable-gutenberg/';
	
	static function init( $prefix ) {
		self::$prefix = $prefix;
		$oxy_toolbox_mod = new Oxy_Toolbox_Mod( $prefix, self::$mod, self::$title, self::$description, self::$link );

		// this will block the rest of the mod to load, if it is not checked.
		if ( 0 === intval( get_option( self::$prefix . self::$mod, 0 ) ) || OxyToolboxLicense::is_activated_license() !== true ) {
			return;
		}

		// Fully disable Gutenberg editor for all post types.
		add_filter( 'use_block_editor_for_post_type', '__return_false', 10 );

		add_action( self::$prefix . 'enqueue_scripts', array( __CLASS__, 'remove_block_css' ), 100 );
	}

	static function remove_block_css() {
		wp_dequeue_style( 'wp-block-library' ); // WordPress core
		wp_dequeue_style( 'wp-block-library-theme' ); // WordPress core
		wp_dequeue_style( 'wc-block-style' ); // WooCommerce
	}

}

Oxy_Toolbox_DisableGutenberg::init( self::PREFIX );