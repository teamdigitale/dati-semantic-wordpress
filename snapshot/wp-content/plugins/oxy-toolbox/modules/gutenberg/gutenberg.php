<?php
/**
 * Gutenberg module.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The PHP class for this module.
 */
class Oxy_Toolbox_Gutenberg {

	public static $prefix;
	public static $mod         = 'gutenberg_';
	public static $title       = 'Gutenberg';
	public static $description = 'Gutenberg related tweaks.';
	public static $link        = 'https://oxyplugins.com/doc/gutenberg/';

	/**
	 * Init function.
	 *
	 * @param string $prefix The prefix for this module.
	 */
	public static function init( $prefix ) {

		self::$prefix = $prefix;

		// Create a new module.
		$oxy_toolbox_mod = new Oxy_Toolbox_Mod( $prefix, self::$mod, self::$title, self::$description, self::$link );

		// Register the options for this module.
		add_action( self::$prefix . 'register_options', array( __CLASS__, 'mod_register_options' ) );

		// This will block the rest of the mod to load, if it is not checked.
		if ( 0 === intval( get_option( self::$prefix . self::$mod, 0 ) ) || OxyToolboxLicense::is_activated_license() !== true ) {
			return;
		}

		// Define the options for this module.
		add_action( self::$prefix . self::$mod . 'form_options', array( __CLASS__, 'mod_form_options' ) );

		// If "Disable Gutenberg" option is enabled.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'disable_gutenberg', false ) ) {
			// Fully disable Gutenberg editor for all post types.
			add_filter( 'use_block_editor_for_post_type', '__return_false', 10 );

			add_action( self::$prefix . 'enqueue_scripts', array( __CLASS__, 'disable_gutenberg' ), 100 );
		}

		// If "Full Width Editor" option is enabled.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'full_width_editor', false ) ) {
			add_action( 'admin_head', array( __CLASS__, 'full_width_editor' ) );
		}

		// If "Disable Editor Fullscreen by Default" option is enabled.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'disable_editor_fullscreen_by_default', false ) ) {
			add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'disable_editor_fullscreen_by_default' ) );
		}

		// If "Disable Welcome Guide" option is enabled.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'disable_welcome_guide', false ) ) {
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'disable_welcome_guide_scripts' ), 11 );
		}

		// If "Disable NUX" option is enabled.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'disable_nux', false ) ) {
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'disable_nux_scripts' ), 11 );
		}

	}

	/**
	 * Disable Gutenberg functionality.
	 */
	public static function disable_gutenberg() {

		wp_dequeue_style( 'wp-block-library' ); // WordPress core.
		wp_dequeue_style( 'wp-block-library-theme' ); // WordPress core.
		wp_dequeue_style( 'wc-block-style' ); // WooCommerce.

	}

	/**
	 * Full Width Editor functionality.
	 */
	public static function full_width_editor() {

		echo '
		<style type="text/css">
		body.gutenberg-editor-page .editor-post-title__block, body.gutenberg-editor-page .editor-default-block-appender, body.gutenberg-editor-page .editor-block-list__block {
			max-width: none !important;
		}
		.block-editor__container .wp-block {
			max-width: none !important;
		}
		/* code editor */
		.edit-post-text-editor__body {
			max-width: none !important;	
			margin-left: 2%;
			margin-right: 2%;
		}
		</style>
		';

	}

	/**
	 * Disable Editor Fullscreen by Default functionality.
	 *
	 * @link https://jeanbaptisteaudras.com/en/2020/03/disable-block-editor-default-fullscreen-mode-in-wordpress-5-4/
	 */
	public static function disable_editor_fullscreen_by_default() {
		$script = "window.onload = function() { const isFullscreenMode = wp.data.select( 'core/edit-post' ).isFeatureActive( 'fullscreenMode' ); if ( isFullscreenMode ) { wp.data.dispatch( 'core/edit-post' ).toggleFeature( 'fullscreenMode' ); } }";

		wp_add_inline_script( 'wp-blocks', $script );
	}

	/**
	 * Check if the current page is the Gutenberg block editor.
	 *
	 * @link https://github.com/Freemius/wordpress-sdk/commit/8a87d389c647b4588bfe96fc7d420d62a48cbac5
	 * @return boolean
	 */
	public static function is_gutenberg_page() {
		if ( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() ) {
			// The Gutenberg plugin is on.
			return true;
		}

		$current_screen = get_current_screen();

		if ( method_exists( $current_screen, 'is_block_editor' ) &&
				$current_screen->is_block_editor()
		) {
			// Gutenberg page on 5+.
			return true;
		}
		return false;
	}

	/**
	 * Disable Welcome Guide functionality.
	 *
	 * @link https://github.com/WordPress/gutenberg/issues/19512#issuecomment-572304859
	 * @param string $hook Current admin screen.
	 */
	public static function disable_welcome_guide_scripts( $hook ) {
		if ( ! self::is_gutenberg_page() ) {
			return;
		}

		wp_add_inline_script( 'wp-edit-post', 'wp.data.select( "core/edit-post" ).isFeatureActive( "welcomeGuide" ) && wp.data.dispatch( "core/edit-post" ).toggleFeature( "welcomeGuide" )' );
	}

	/**
	 * Disable NUX functionality.
	 *
	 * @link https://github.com/aduth/wp-disable-nux
	 * @param string $hook Current admin screen.
	 */
	public static function disable_nux_scripts( $hook ) {
		if ( ! self::is_gutenberg_page() ) {
			return;
		}

		wp_add_inline_script( 'wp-nux', 'wp.data.dispatch( "core/nux" ).disableTips();' );
	}



	/**
	 * Function to register options for this module.
	 */
	public static function mod_register_options() {

		$option_names = array( 'disable_gutenberg', 'full_width_editor', 'disable_editor_fullscreen_by_default', 'disable_welcome_guide', 'disable_nux' );

		foreach ( $option_names as $option_name ) {
			add_option( self::$prefix . self::$mod . $option_name, false );
			register_setting( self::$prefix . 'settings', self::$prefix . self::$mod . $option_name, array( __CLASS__, 'sanitize_module_option' ) );
		}

	}

	/**
	 * Callback function that sanitizes the option's value.
	 *
	 * @param string $show Value of this option submitted from the form on the settings page.
	 */
	public static function sanitize_module_option( $show ) {

		if ( 'true' === $show ) {
			return 'true';
		}

		return '';

	}

	/**
	 * Function to define the options for this module.
	 */
	public static function mod_form_options() {
		?>

		<button type="button" class="collapsible"></button>
		<div class="module-settings">
			<div class="module-settings-wrap">
				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>disable_editor_fullscreen_by_default" name="<?php echo self::$prefix . self::$mod; ?>disable_editor_fullscreen_by_default" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'disable_editor_fullscreen_by_default' ), 'true' ); ?> />
					<?php esc_html_e( 'Disable Editor Fullscreen by Default' ); ?>
					<?php _e( '<p>Disables the default behavior of Gutenberg editor being fullscreen.</p>' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>disable_gutenberg" name="<?php echo self::$prefix . self::$mod; ?>disable_gutenberg" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'disable_gutenberg' ), 'true' ); ?> />
					<?php esc_html_e( 'Disable Gutenberg' ); ?>
					<?php _e( '<p>Disables Gutenberg WordPress editor and unloads Gutenberg-related stylesheets.</p>' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>disable_nux" name="<?php echo self::$prefix . self::$mod; ?>disable_nux" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'disable_nux' ), 'true' ); ?> />
					<?php esc_html_e( 'Disable NUX' ); ?>
					<?php _e( '<p>Disables "New User Experience" tooltips.</p>' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>disable_welcome_guide" name="<?php echo self::$prefix . self::$mod; ?>disable_welcome_guide" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'disable_welcome_guide' ), 'true' ); ?> />
					<?php esc_html_e( 'Disable Welcome Guide' ); ?>
					<?php _e( '<p>Disables "Welcome to the block editor" popup modal.</p>' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>full_width_editor" name="<?php echo self::$prefix . self::$mod; ?>full_width_editor" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'full_width_editor' ), 'true' ); ?> />
					<?php esc_html_e( 'Full Width Editor' ); ?>
					<?php _e( '<p>Changes the default width of the Gutenberg editor to fullsize.</p>' ); ?>
				</div>
			</div>
		</div>

		<?php
	}

}

Oxy_Toolbox_Gutenberg::init( self::PREFIX );
