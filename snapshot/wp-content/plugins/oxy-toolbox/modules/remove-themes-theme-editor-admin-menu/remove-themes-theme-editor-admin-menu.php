<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Oxy_Toolbox_Remove_Themes_Theme_Editor_Admin_Menu {

	static $prefix;
	static $mod = 'remove_themes_theme_editor_admin_menu_';
	static $title = 'Remove Themes and Theme Editor From Admin Menu';
	static $description = 'Removes Appearance > Themes and Appearance > Theme Editor admin menu items from WordPress admin.';
	static $link = 'https://oxyplugins.com/doc/remove-themes-and-theme-editor-from-admin-menu/';
	
	static function init( $prefix ) {
		self::$prefix = $prefix;
		$oxy_toolbox_mod = new Oxy_Toolbox_Mod( $prefix, self::$mod, self::$title, self::$description, self::$link );

		// this will block the rest of the mod to load, if it is not checked.
		if ( 0 === intval( get_option( self::$prefix . self::$mod, 0 ) ) || OxyToolboxLicense::is_activated_license() !== true) {
			return;
		}

		add_action( 'admin_menu', array( __CLASS__, 'mod_admin_scripts' ), 999 );
	}

	static function mod_admin_scripts() {

		remove_submenu_page( 'themes.php', 'themes.php' );
		remove_submenu_page( 'themes.php', 'theme-editor.php' );
	}
	
}

Oxy_Toolbox_Remove_Themes_Theme_Editor_Admin_Menu::init( self::PREFIX );