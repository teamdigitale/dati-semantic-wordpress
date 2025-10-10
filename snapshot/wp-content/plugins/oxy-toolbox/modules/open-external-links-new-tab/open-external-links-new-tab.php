<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class Oxy_Toolbox_ExternalLinksNewTab {

	static $prefix;
	static $mod = 'open_external_links_new_tab_';
	static $title = 'Open External Links In A New Tab (frontend)';
	static $description = 'Opens all external links in a new tab.';
	static $link = 'https://oxyplugins.com/doc/open-external-links-in-a-new-tab/';
	
	static function init( $prefix ) {
		self::$prefix = $prefix;
		$oxy_toolbox_mod = new Oxy_Toolbox_Mod( $prefix, self::$mod, self::$title, self::$description, self::$link );

		// this will block the rest of the mod to load, if it is not checked.
		if ( 0 === intval( get_option( self::$prefix . self::$mod, 0 ) ) || OxyToolboxLicense::is_activated_license() !== true ) {
			return;
		}

		add_action( self::$prefix . 'enqueue_scripts', array( __CLASS__, 'mod_scripts' ), 1 );
	}

	static function mod_scripts( $version ) {

		// Do not run inside the editor.
		if ( defined( 'SHOW_CT_BUILDER' ) ) {
			return;
		}

		wp_register_script( self::$prefix . self::$mod . 'script', plugins_url( 'js/script.js', __FILE__ ), [ 'jquery' ], $version, true );
			
		wp_enqueue_script( self::$prefix . self::$mod . 'script' );
	}

}

Oxy_Toolbox_ExternalLinksNewTab::init( self::PREFIX );