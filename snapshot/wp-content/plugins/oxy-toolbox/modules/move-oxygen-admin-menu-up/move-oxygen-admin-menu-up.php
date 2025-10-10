<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Oxy_Toolbox_Move_Oxygen_Menu_Up {

	static $prefix;
	static $mod = 'move_oxygen_admin_menu_up_';
	static $title = 'Move Oxygen Admin Menu Up';
	static $description = 'Moves Oxygen admin menu up in the WordPress admin below the Dashboard item.';
	static $link = 'https://oxyplugins.com/doc/move-oxygen-admin-menu-up/';

	public static function init( $prefix ) {
		self::$prefix = $prefix;
		$oxy_toolbox_mod = new Oxy_Toolbox_Mod( $prefix, self::$mod, self::$title, self::$description, self::$link );

		// this will block the rest of the mod to load, if it is not checked.
		if ( 0 === intval( get_option( self::$prefix . self::$mod, 0 ) ) || OxyToolboxLicense::is_activated_license() !== true) {
			return;
		}

		add_action( 'admin_menu', array( __CLASS__, 'mod_admin_scripts' ), 999 );
	}

	public static function custom_menu_order() {
		return array( 'index.php', 'separator1', 'ct_dashboard_page' );
	}

	public static function mod_admin_scripts() {

		add_filter( 'custom_menu_order', '__return_true' );

		add_filter( 'menu_order', array( __CLASS__, 'custom_menu_order' ) );
	}

}

Oxy_Toolbox_Move_Oxygen_Menu_Up::init( self::PREFIX );