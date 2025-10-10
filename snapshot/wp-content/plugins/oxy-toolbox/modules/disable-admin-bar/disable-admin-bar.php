<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class Oxy_Toolbox_DisableAdminBar {

	static $prefix;
	static $mod = 'disable_admin_bar_';
	static $title = 'Disable Admin Bar';
	static $description = 'Lets you disable WordPress Admin Toolbar.';
	static $link = 'https://oxyplugins.com/doc/disable-admin-bar/';
	
	static function init( $prefix ) {
		self::$prefix = $prefix;
		$oxy_toolbox_mod = new Oxy_Toolbox_Mod( $prefix, self::$mod, self::$title, self::$description, self::$link );
		add_action( self::$prefix . 'register_options', array( __CLASS__, 'mod_register_options' ) );
		// this will block the rest of the mod to load, if it is not checked.
		if ( 0 === intval( get_option( self::$prefix . self::$mod, 0 ) ) || OxyToolboxLicense::is_activated_license() !== true ) {
			return;
		}


		add_action( self::$prefix.self::$mod . 'form_options', array( __CLASS__, 'mod_form_options' ) );

		// All users.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'all_users', false ) ) {
			add_filter( 'show_admin_bar', '__return_false', 999 );
		}
		
		// non-Admins only.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'all_users', false ) ) {
			// if ( ! current_user_can( 'manage_options' ) ) {
			// 	add_filter( 'show_admin_bar', '__return_false' );
			// }
		}

	}

	static function mod_register_options() {
		add_option( self::$prefix . self::$mod . 'all_users', false );
		register_setting( self::$prefix . 'settings', self::$prefix . self::$mod . 'all_users', array( __CLASS__, 'sanitize_all_users' ) );
		
		add_option( self::$prefix . self::$mod . 'non_admins', false );
		register_setting( self::$prefix . 'settings', self::$prefix . self::$mod . 'non_admins', array( __CLASS__, 'sanitize_non_admins' ) );
	}

	static function sanitize_all_users( $all_users ) {

		if ( $all_users === "true" ) {
			return "true";
		}

		return "";
	}
	static function sanitize_non_admins( $non_admins ) {

		if ( $non_admins === "true" ) {
			return "true";
		}

		return "";
	}


	static function mod_form_options() {
		?>

		<div class="module-settings">
			<div>
				<input id="<?php echo self::$prefix . self::$mod;?>all_users" name="<?php echo self::$prefix . self::$mod;?>all_users" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'all_users' ), "true" ); ?> />
				<?php _e( 'For all users' ); ?>
			</div>

			<div>
				<input id="<?php echo self::$prefix . self::$mod;?>non_admins" name="<?php echo self::$prefix . self::$mod;?>non_admins" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'non_admins' ), "true" ); ?> />
				<?php _e( 'For non-Admins only' ); ?>
			</div>	
		</div>

		<?php
	}

}

Oxy_Toolbox_DisableAdminBar::init( self::PREFIX );