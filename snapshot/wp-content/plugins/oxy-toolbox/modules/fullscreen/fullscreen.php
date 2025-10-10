<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Oxy_Toolbox_Fullscreen {

	static $prefix;
	static $mod         = 'fullscreen_';
	static $title       = 'Fullscreen';
	static $description = 'Fullscreen options.';
	static $link        = 'https://oxyplugins.com/doc/fullscreen/';

	public static function init( $prefix ) {

		self::$prefix = $prefix;

		$oxy_toolbox_mod = new Oxy_Toolbox_Mod( $prefix, self::$mod, self::$title, self::$description, self::$link );
		add_action( self::$prefix . 'register_options', array( __CLASS__, 'mod_register_options' ) );
		// this will block the rest of the mod to load, if it is not checked.
		if ( 0 === intval( get_option( self::$prefix . self::$mod, 0 ) ) || OxyToolboxLicense::is_activated_license() !== true ) {
			return;
		}

		add_action( self::$prefix . self::$mod . 'form_options', array( __CLASS__, 'mod_form_options' ) );

		add_action( self::$prefix . 'enqueue_scripts', array( __CLASS__, 'mod_scripts' ), 1 );

	}

	public static function mod_scripts( $version ) {

		if ( ! defined( 'SHOW_CT_BUILDER' ) ) {
			return;
		}

		$mode   = get_option( self::$prefix . self::$mod . 'mode', 1 );
		$hotkey = get_option( self::$prefix . self::$mod . 'hotkey', 17 );

		if ( defined( 'OXYGEN_IFRAME' ) ) {
			wp_register_script( self::$prefix . self::$mod . 'script', plugins_url( 'js/script.js', __FILE__ ), array( 'ct-angular-main' ), $version, true );
			wp_localize_script(
				self::$prefix . self::$mod . 'script',
				self::$prefix . self::$mod . 'options',
				array(
					'mode'   => $mode,
					'hotkey' => $hotkey,

				)
			);
			wp_enqueue_script( self::$prefix . self::$mod . 'script' );
		} else {
			if ( intval( $mode ) === 2 ) {
				wp_enqueue_script( self::$prefix . self::$mod . 'parent', plugins_url( 'js/parent.js', __FILE__ ), array( 'ct-angular-ui' ), $version, true );
			}

			wp_enqueue_style( self::$prefix . self::$mod . 'style', plugins_url( 'css/style.css', __FILE__ ), array(), $version );
		}
	}

	public static function mod_register_options() {

		add_option( self::$prefix . self::$mod . 'mode', 1 );
		register_setting( self::$prefix . 'settings', self::$prefix . self::$mod . 'mode', array( __CLASS__, 'sanitize_mode' ) );

		add_option( self::$prefix . self::$mod . 'hotkey', 1 );
		register_setting( self::$prefix . 'settings', self::$prefix . self::$mod . 'hotkey', array( __CLASS__, 'sanitize_hotkey' ) );

	}

	public static function sanitize_hotkey( $key ) {
		if ( ! is_numeric( $key ) ) {
			return 17;
		}

		return intval( $key );
	}




	public static function sanitize_mode( $mode ) {

		if ( is_numeric( $mode ) && intval( $mode ) === 2 ) {
			return 2;
		}

		return 1;// default
	}


	public static function mod_form_options() {
		?>
		<div style="margin-top: 10px;">
			<select id="<?php echo self::$prefix . self::$mod; ?>hotkey" name="<?php echo self::$prefix . self::$mod; ?>hotkey" type="text" />
				<?php
				foreach ( array(
					'16' => 'Shift',
					'17' => 'Ctrl',
					'18' => 'Alt',
					'91' => 'Cmd',
					'9'  => 'Tab'
				) as $modifier => $label ) {
					?>
					<option value="<?php echo $modifier; ?>" <?php selected( intval( $modifier ), get_option( self::$prefix . self::$mod . 'hotkey' ), true ); ?>><?php echo $label; ?></option>
					<?php
				}
				?>
			</select>
			<span><?php _e( 'toggles all the panels in the Oxygen editor and makes code editor take up full width.' ); ?></span>
		</div>

		<button type="button" class="collapsible"></button>
		<div class="module-settings" style="margin-top: 20px;">
			<div class="module-settings-wrap">
				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>mode" name="<?php echo self::$prefix . self::$mod; ?>mode" type="checkbox" value="2" <?php checked( get_option( self::$prefix . self::$mod . 'mode' ), 2 ); ?> />
					<?php _e( 'Detached layout' ); ?>
					<p>Enabling detached layout will separate the inner and outer frames so they can be placed in separate tabs/windows/monitor screens.</p>
				</div>
			</div>
		</div>


		<?php
	}
}

Oxy_Toolbox_Fullscreen::init( self::PREFIX );
