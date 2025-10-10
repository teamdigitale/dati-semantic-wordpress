<?php
/**
 * WordPress module.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The PHP class for this module.
 */
class Oxy_Toolbox_WordPress {

	public static $prefix;
	public static $mod         = 'wordpress_';
	public static $title       = 'WordPress';
	public static $description = 'WordPress related tweaks.';
	public static $link        = 'https://oxyplugins.com/doc/wordpress/';

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

		// If "Disable auto-updates UI elements" option is enabled.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'disable_auto_updates_ui', false ) ) {
			// Disable plugins auto-update UI elements.
			add_filter( 'plugins_auto_update_enabled', '__return_false' );

			// Disable themes auto-update UI elements.
			add_filter( 'themes_auto_update_enabled', '__return_false' );
		}

		// If "Disable auto-update emails" option is enabled.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'disable_auto_update_emails', false ) ) {
			// Disable auto-update email notifications for plugins.
			add_filter( 'auto_plugin_update_send_email', '__return_false' );

			// Disable auto-update email notifications for themes.
			add_filter( 'auto_theme_update_send_email', '__return_false' );
		}

		// If "Disable WP sitemap generation for Oxygen Templates" option is enabled.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'disable_xml_sitemap_oxygen_templates', false ) ) {
			add_filter(
				'wp_sitemaps_post_types',
				function( $post_types ) {
					unset( $post_types['ct_template'] );
					return $post_types;
				}
			);
		}

		// If "Disable core sitemaps" option is enabled.
		// https://make.wordpress.org/core/2020/06/10/merge-announcement-extensible-core-sitemaps/
		// https://dev.to/gretathemes/how-to-disable-sitemaps-in-wordpress-5-5-1k2h
		if ( 'true' === get_option( self::$prefix . self::$mod . 'disable_core_sitemaps', false ) ) {
			// add_filter( 'wp_sitemaps_enabled', '__return_false' );
			add_action(
				'init',
				function() {
					remove_action( 'init', 'wp_sitemaps_get_server' );
				},
				5
			);
		}

		// If "Disable Admin Email Check" option is enabled.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'disable_admin_email_check', false ) ) {
			add_filter( 'admin_email_check_interval', '__return_false' );
		}

		// If "Disable XML-RPC" option is enabled.
		// https://wordpress.org/plugins/disable-xml-rpc/
		// https://developer.wordpress.org/reference/hooks/xmlrpc_enabled/
		if ( 'true' === get_option( self::$prefix . self::$mod . 'disable_xml_rpc', false ) ) {
			add_filter( 'xmlrpc_enabled', '__return_false' );
		}

		// If "Remove Screen Options" option is enabled.
		// https://www.isitwp.com/remove-the-screen-options-tab-with-screen_options_show_screen-hook/
		if ( 'true' === get_option( self::$prefix . self::$mod . 'remove_screen_options', false ) ) {
			add_filter(
				'screen_options_show_screen',
				function() {
					return current_user_can( 'manage_options' );
				}
			);
		}

		// If "Remove Help" option is enabled.
		// https://wpartisan.me/tutorials/how-to-the-remove-screen-options-and-help-tabs-from-the-wp-admin
		// https://core.trac.wordpress.org/ticket/23861#comment:2
		// https://wordpress.stackexchange.com/a/324768
		if ( 'true' === get_option( self::$prefix . self::$mod . 'remove_help', false ) ) {
			add_action( 'plugins_loaded', array( __CLASS__, 'remove_help' ) );
		}

		// If "Disable Year Month Folders for Uploads" option is enabled.
		// https://wordpress.stackexchange.com/a/104312
		if ( 'true' === get_option( self::$prefix . self::$mod . 'disable_uploads_use_yearmonth_folders', false ) ) {
			add_filter( 'option_uploads_use_yearmonth_folders', '__return_false', 100 );
		}

	}

	/**
	 * Remove Help functionality.
	 */
	public static function remove_help() {
		/**
		* Removes the Help tab in the WP Admin
		*
		* @param array $old_help
		* @param int $screen_id
		* @param obj $screen
		* @return array
		*/
		if ( ! current_user_can( 'manage_options' ) ) {
			add_filter(
				'contextual_help',
				function( $old_help, $screen_id, $screen ) {
					$screen->remove_help_tabs();
					return $old_help;
				},
				999,
				3
			);
		}
	}

	/**
	 * Function to register options for this module.
	 */
	public static function mod_register_options() {

		$option_names = array( 'disable_auto_updates_ui', 'disable_auto_update_emails', 'disable_xml_sitemap_oxygen_templates', 'disable_core_sitemaps', 'disable_admin_email_check', 'disable_xml_rpc', 'remove_screen_options', 'remove_help', 'disable_uploads_use_yearmonth_folders' );

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
					<input id="<?php echo self::$prefix . self::$mod; ?>disable_admin_email_check" name="<?php echo self::$prefix . self::$mod; ?>disable_admin_email_check" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'disable_admin_email_check' ), 'true' ); ?> />
					<?php esc_html_e( 'Disable Admin Email Check' ); ?>
					<?php _e( '<p>Disables periodic checking by WordPress asking the admins if their email is still valid by preventing redirection to the admin email confirmation screen.</p>' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>disable_auto_updates_ui" name="<?php echo self::$prefix . self::$mod; ?>disable_auto_updates_ui" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'disable_auto_updates_ui' ), 'true' ); ?> />
					<?php esc_html_e( 'Disable Auto-update UI Elements' ); ?>
					<?php _e( '<p>Disables auto-update user interface elements for plugins and themes. This does not enable or disable auto-updates. It only hides the user interface elements.</p>' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>disable_auto_update_emails" name="<?php echo self::$prefix . self::$mod; ?>disable_auto_update_emails" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'disable_auto_update_emails' ), 'true' ); ?> />
					<?php esc_html_e( 'Disable Auto-update Emails' ); ?>
					<?php _e( '<p>Disables auto-update email notifications for plugins and themes.</p>' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>disable_core_sitemaps" name="<?php echo self::$prefix . self::$mod; ?>disable_core_sitemaps" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'disable_core_sitemaps' ), 'true' ); ?> />
					<?php esc_html_e( 'Disable Core Sitemaps' ); ?>
					<?php _e( "<p>Disables WordPress' sitemaps functionality completely.</p>" ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>disable_xml_sitemap_oxygen_templates" name="<?php echo self::$prefix . self::$mod; ?>disable_xml_sitemap_oxygen_templates" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'disable_xml_sitemap_oxygen_templates' ), 'true' ); ?> />
					<?php esc_html_e( 'Disable Sitemap Generation for Oxygen Templates' ); ?>
					<?php _e( '<p>Disables XML sitemap generation by WordPress for Oxygen Templates.</p>' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>disable_xml_rpc" name="<?php echo self::$prefix . self::$mod; ?>disable_xml_rpc" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'disable_xml_rpc' ), 'true' ); ?> />
					<?php esc_html_e( 'Disable XML-RPC' ); ?>
					<?php _e( '<p>Disables XML-RPC methods that require authentication.</p>' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>disable_uploads_use_yearmonth_folders" name="<?php echo self::$prefix . self::$mod; ?>disable_uploads_use_yearmonth_folders" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'disable_uploads_use_yearmonth_folders' ), 'true' ); ?> />
					<?php esc_html_e( 'Disable Year Month Folders for Uploads' ); ?>
					<?php _e( '<p>Disables organization of uploads into month- and year-based folders.</p>' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>remove_help" name="<?php echo self::$prefix . self::$mod; ?>remove_help" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'remove_help' ), 'true' ); ?> />
					<?php esc_html_e( 'Remove Help' ); ?>
					<?php _e( '<p>Removes Help tab for everyone but the admin or users with the `manage_options` capability.</p>' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>remove_screen_options" name="<?php echo self::$prefix . self::$mod; ?>remove_screen_options" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'remove_screen_options' ), 'true' ); ?> />
					<?php esc_html_e( 'Remove Screen Options' ); ?>
					<?php _e( '<p>Removes Screen Options tab for everyone but the admin or users with the `manage_options` capability.</p>' ); ?>
				</div>
			</div>
		</div>

		<?php
	}

}

Oxy_Toolbox_WordPress::init( self::PREFIX );
