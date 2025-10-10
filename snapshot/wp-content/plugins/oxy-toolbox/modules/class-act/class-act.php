<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Oxy_Toolbox_Class_Act {
	
	static $prefix;
	static $mod = 'class_act_';
	static $title = 'Class Act';
	static $description = 'Lets you copy/move the styles for the ID/class to a new class and copy/move styles from class to ID and to reset the styles for the current ID/class.';
	static $link = 'https://oxyplugins.com/doc/class-act/';
	
	static function init( $prefix ) {
		
		self::$prefix = $prefix;

		$oxy_toolbox_mod = new Oxy_Toolbox_Mod( $prefix, self::$mod, self::$title, self::$description, self::$link );
		add_action( self::$prefix . 'register_options', array( __CLASS__, 'mod_register_options' ) );
		// this will block the rest of the mod to load, if it is not checked.
		if ( 0 === intval( get_option( self::$prefix . self::$mod, 0 ) ) || OxyToolboxLicense::is_activated_license() !== true) {
			return;
		}
		
		add_action( self::$prefix.self::$mod . 'form_options', array( __CLASS__, 'mod_form_options' ) );

		add_action( self::$prefix . 'enqueue_scripts', array( __CLASS__, 'mod_scripts' ), 1 );

		
	}

	static function mod_scripts( $version ) {

		if ( ! defined( 'SHOW_CT_BUILDER' ) ) {
			return;
		}

		if ( defined( 'OXYGEN_IFRAME' ) ) {
			wp_register_script( self::$prefix . self::$mod . 'script', plugins_url( 'js/script.js', __FILE__ ), array( 'ct-angular-main' ), $version );
			wp_localize_script( self::$prefix . self::$mod . 'script', self::$prefix . self::$mod . 'options', array(
				'disable_warnings' => get_option( self::$prefix . self::$mod . 'hide_warnings' ) === "true" ? true :false
			));
			wp_enqueue_script( self::$prefix . self::$mod . 'script' );
		} else {
			wp_enqueue_style( self::$prefix . self::$mod . 'style', plugins_url( 'css/style.css', __FILE__ ), array(), $version );
		}
	}

	static function mod_register_options() {
		add_option( self::$prefix . self::$mod . 'hide_warnings', false );
		register_setting( self::$prefix . 'settings', self::$prefix . self::$mod . 'hide_warnings', array( __CLASS__, 'sanitize_hide_warnings' ) );
	}

	static function sanitize_hide_warnings( $hide_warnings ) {

		if ( $hide_warnings === "true" ) {
			return "true";
		}

		return "";
	}


	static function mod_form_options() {
		?>

		<button type="button" class="collapsible"></button>
		<div class="module-settings">
			<div class="module-settings-wrap">
				<div>
					<input id="<?php echo self::$prefix . self::$mod;?>hide_warnings" name="<?php echo self::$prefix . self::$mod;?>hide_warnings" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'hide_warnings' ), "true" ); ?> />
					<?php _e( 'Disable Warnings' ); ?>
				</div>
			</div>
		</div>
			

		<?php
	}
}

Oxy_Toolbox_Class_Act::init( self::PREFIX );