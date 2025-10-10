<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class Oxy_Toolbox_ReadingProgressBar {

	static $prefix;
	static $mod = 'reading_progress_bar_';
	static $title = 'Reading Progress Bar (frontend)';
	static $description = 'Adds a reading progress indicator at the top of webpages on the frontend.';
	static $link = 'https://oxyplugins.com/doc/reading-progress-bar/';
	
	static function init( $prefix ) {
		self::$prefix = $prefix;
		$oxy_toolbox_mod = new Oxy_Toolbox_Mod( $prefix, self::$mod, self::$title, self::$description, self::$link );

		// this will block the rest of the mod to load, if it is not checked.
		if ( 0 === intval( get_option( self::$prefix . self::$mod, 0 ) ) || OxyToolboxLicense::is_activated_license() !== true ) {
			return;
		}

		add_action( 'ct_before_builder', array( __CLASS__, 'reading_progress_bar_frontend' ) );

		add_action( self::$prefix . 'enqueue_scripts', array( __CLASS__, 'mod_scripts' ), 1 );
	}

	static function reading_progress_bar_frontend() {
		if ( defined( 'SHOW_CT_BUILDER' ) ) {
			return;
		} ?>
		
		<progress value="0" class="reading-progress">
			<div class="progress-container">
				<span class="progress-bar"></span>
			</div>
		</progress>
	<?php }

	static function mod_scripts( $version ) {
		if ( defined( 'SHOW_CT_BUILDER' ) ) {
			return;
		}

		wp_enqueue_style( self::$prefix . self::$mod . 'style', plugins_url( 'css/style.css', __FILE__ ), [], $version );

		wp_register_script( self::$prefix . self::$mod . 'script', plugins_url( 'js/script.js', __FILE__ ), [ 'jquery' ], $version, true );
			
		wp_enqueue_script( self::$prefix . self::$mod . 'script' );
	}

}

Oxy_Toolbox_ReadingProgressBar::init( self::PREFIX );