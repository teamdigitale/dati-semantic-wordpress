<?php
/*
	Plugin Name: Oxy Toolbox
	Author: Gagan S Goraya, Sridhar Katakam
	Author URI: https://oxyplugins.com
	Description: Adds several useful and time-saving features for the Oxygen builder.
	Version: 1.6.2
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'OXY_Toolbox_Plugin_Updater' ) ) {
	// load our custom updater.
	include dirname( __FILE__ ) . '/special/OXY_Toolbox_Plugin_Updater.php';
}

require_once 'special/oxy-toolbox-license.php';



class OxyToolbox {
	const PREFIX    = 'oxy_toolbox_';
	const TITLE     = 'Oxy Toolbox';
	const VERSION   = '1.6.2';
	const STORE_URL = 'https://oxyplugins.com';
	const ITEM_ID   = 2757;

	static function init() {

		include 'modules/oxy_toolbox_mod.php';

		include 'modules/all-templates-button/all-templates-button.php';
		include 'modules/back-to-top/back-to-top.php';
		// include 'modules/background-image-lazy-loading/background-image-lazy-loading.php';
		include 'modules/class-act/class-act.php';
		include 'modules/class-cleaner/class-cleaner.php';
		include 'modules/emmet/emmet.php';
		include 'modules/conditions/conditions.php';
		// include( 'modules/disable-admin-bar/disable-admin-bar.php' );
		// include 'modules/disable-gutenberg/disable-gutenberg.php';
		include 'modules/editor-tweaks/editor-tweaks.php';
		include 'modules/essentials/essentials.php';
		include 'modules/fullscreen/fullscreen.php';
		include 'modules/gutenberg/gutenberg.php';
		include 'modules/move-oxygen-admin-menu-up/move-oxygen-admin-menu-up.php';
		include 'modules/navigator/navigator.php';
		include 'modules/offline-mode/offline-mode.php';
		include 'modules/open-external-links-new-tab/open-external-links-new-tab.php';
		// include 'modules/rank-math-integration/rank-math-integration.php';
		include 'modules/reading-progress-bar/reading-progress-bar.php';
		include 'modules/remove-themes-theme-editor-admin-menu/remove-themes-theme-editor-admin-menu.php';
		include 'modules/seopress/seopress.php';
		include 'modules/scripts/scripts.php';
		include 'modules/text-edit/text-edit.php';
		include 'modules/toc/toc.php';
		// include 'modules/repeatermod/repeatermod.php';
		include 'modules/revisions/revisions.php';
		include 'modules/image-attrs/image-attrs.php';
		include 'modules/wordpress/wordpress.php';


		OxyToolboxLicense::init( self::PREFIX, self::TITLE, self::STORE_URL, self::ITEM_ID );

		add_action( 'admin_init', array( __CLASS__, 'register_option' ) );

		add_filter( 'plugin_action_links_' . basename( __DIR__ ) . '/' . basename( __FILE__ ), array( __CLASS__, 'settings_link' ) );

		add_action( 'activate_' . plugin_basename( __FILE__ ), array( __CLASS__, 'activate' ), 10, 2 );

		if ( true === OxyToolboxLicense::is_activated_license() ) {
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'scripts' ), 11 );
		}

		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ), 11 );

		include 'modules/sseditor/sseditor.php';
		
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_style' ) );
		add_action( 'admin_init', array( __CLASS__, 'plugin_updater' ), 0 );

		add_action( 'admin_notices', array( __CLASS__, 'general_admin_notice' ), 12 );

		add_action( 'init', array( __CLASS__, 'if_options_saved' ) );

		add_action( 'body_class', array( __CLASS__, 'body_class' ) );

	}

	static function body_class( $classes ) {
		if ( defined( 'SHOW_CT_BUILDER' ) ) {
			$classes[] = 'oxy-toolbox-is-active';
		}

		return $classes;
	}

	static function if_options_saved() {
		global $pagenow;

		if ( isset( $_POST['action'] ) && $_POST['action'] == 'update' && $pagenow === 'options.php' ) {
			set_transient( self::PREFIX . 'options_saved', true );
		}
	}


	static function general_admin_notice() {

		if ( isset( $_GET['page'] ) && $_GET['page'] === self::PREFIX . 'menu' ) {
			$saved = get_transient( self::PREFIX . 'options_saved' );
			if ( $saved ) {
				delete_transient( self::PREFIX . 'options_saved' );
				echo '<div class="notice notice-success is-dismissible">
					 <p>' . self::TITLE . ' settings have been saved.</p>
				</div>';
			}
		}
	}



	static function settings_link( $links ) {
		$url = esc_url(
			add_query_arg(
				'page',
				self::PREFIX . 'menu',
				get_admin_url() . 'admin.php'
			)
		);

		// Create the link.
		$settings_link = "<a href='$url'>" . __( 'Settings' ) . '</a>';

		// Adds the link to the beginning of the array.
		array_unshift(
			$links,
			$settings_link
		);

		return $links;
	}

	static function admin_menu() {

		$users_access_list = get_option("oxygen_vsb_options_users_access_list", array());

		if(isset($users_access_list[get_current_user_id()]) && $users_access_list[get_current_user_id()][0] !== 'true') {
			return;
		}

		global $menu;
		$menu_exists = false;

		foreach ( $menu as $item ) {
			if ( array_search( 'ct_dashboard_page', $item ) !== false ) {
				$menu_exists = true;
				break;
			}
		}

		if ( $menu_exists !== false ) {
			add_submenu_page( 'ct_dashboard_page', self::TITLE, self::TITLE, 'manage_options', self::PREFIX . 'menu', array( __CLASS__, 'menu_item' ) );
		}
	}

	static function admin_style( $hook ) {

		if ( 'oxygen_page_' . self::PREFIX . 'menu' !== $hook ) {
			return;
		}
		wp_enqueue_script( self::PREFIX . 'admin_script', plugins_url( 'js/admin.js', __FILE__ ), null, self::VERSION, true );
		wp_enqueue_style( self::PREFIX . 'admin_style', plugins_url( 'css/admin.css', __FILE__ ), null, self::VERSION );
	}


	static function menu_item() {
		$tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : false;
		?>
		<div class="wrap">
			<h2 class="nav-tab-wrapper">
			<a href="?page=<?php echo self::PREFIX . 'menu'; ?>&amp;tab=settings" class="nav-tab<?php echo ( $tab === false || $tab == 'editor' ) ? ' nav-tab-active' : ''; ?>">Settings</a>
			<a href="?page=<?php echo self::PREFIX . 'menu'; ?>&amp;tab=license" class="nav-tab<?php echo $tab == 'license' ? ' nav-tab-active' : ''; ?>">License</a>
			<a href="?page=<?php echo self::PREFIX . 'menu'; ?>&amp;tab=export_import" class="nav-tab<?php echo $tab == 'export_import' ? ' nav-tab-active' : ''; ?>">Export / Import</a>
			</h2>

		<?php
		if ( $tab === 'license' ) {
			OxyToolboxLicense::license_page();
		} elseif( 'export_import' === $tab ) {
			self::export_import_page();
		} else {
			self::settings_page();
		}
		?>
		</div>
		<?php
	}

	static function export_import_page() { ?>
		<div class="settings-export-import">
			<h4>Export/Import Settings</h4>
			
			<?php
				$registeredSettings = get_registered_settings();
				$toolboxSettings    = array();

			foreach ( $registeredSettings as $key => $item ) {
				if ( $item['group'] === self::PREFIX . 'settings' ) {
					$toolboxSettings[ $key ] = get_option( $key );
				}
			}
			?>
			
			<form method="post" action="options.php" id="oxy-toolbox-json-settings-form">
				<?php settings_fields( self::PREFIX . 'settings' ); ?>
				<textarea id="oxy-toolbox-settings" name="oxy-toolbox-settings"><?php echo json_encode( $toolboxSettings ); ?></textarea>
				<input type="submit" name="submit" id="submit" class="button button-primary" value="Apply Settings">
			</form>
			
			<script>
				let jsonform = document.querySelector('#oxy-toolbox-json-settings-form');
				let jsondata = document.querySelector('#oxy-toolbox-settings');
				jsonform.addEventListener('submit', (e) => {
					let data = false
					try {
						data = JSON.parse(jsondata.value.trim());
					} catch(e) {}

					if(data !== false) {
						for(let i in data) {
							let item = document.createElement('input');
							item.setAttribute('type', 'hidden');
							item.setAttribute('name', i);
							item.setAttribute('value', data[i]);

							jsonform.append(item);
						}
					}

					return true;
				});
			</script>

			<p>To import settings from another site, copy all the code from the above box on the other site and overwrite the code in this site. Then click Apply Settings.</p>
		</div>
	<?php }

	static function register_option() {
		add_option( self::PREFIX . '_disable_posttypes', 0 );
		register_setting( self::PREFIX . 'settings', self::PREFIX . '_disable_posttypes', array( __CLASS__, 'disable_post_types' ) );
		do_action( self::PREFIX . 'register_options' );

	}

	static function disable_post_types($val) {
		if($val === 'yes') {
			// logic to disable metaboxes on post type
			global $ct_ignore_post_types;
			$postTypes = get_post_types();
			
			if(is_array($ct_ignore_post_types) && is_array($postTypes)) {
				$postTypes = array_diff($postTypes, $ct_ignore_post_types);
			}

			foreach($postTypes as $key => $val) {
				if($key === 'page') {
					continue;
				}
				update_option('oxygen_vsb_ignore_post_type_'.$key, "true");
			}
			
		}

		return 0;
	}


	static function settings_page() {
		?>
		<h2><?php echo self::TITLE . ' ' . __( 'General Settings' ); ?></h2>
		<div class="form-plugin-links">
			<form method="post" action="options.php">

				<?php settings_fields( self::PREFIX . 'settings' ); ?>
				<table class="wp-list-table widefat plugins">
					<thead>
					<tr>
						<td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Activate All</label><input id="cb-select-all-1" type="checkbox"></td><th scope="col" id="name" class="manage-column column-name column-primary">Select All</th><td></td></tr>
					</thead>
					<tbody>
						<?php do_action( self::PREFIX . 'form_options' ); ?>
					</tbody>
					<thead>
					<tr>
						<td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Activate All</label><input id="cb-select-all-1" type="checkbox"></td><th scope="col" id="name" class="manage-column column-name column-primary">Select All</th><td></td></tr>
					</thead>
				</table>
				<p>
					<label>
					<input type="checkbox" value="yes" name="<?php echo self::PREFIX;?>_disable_posttypes" />
					Check this box to disable Oxygen metabox on post types other than `page`.
					</label>
				</p>
			<?php submit_button(); ?>
			</form>
			<div class="plugin-links">
				<ul>
					<li>Oxy Toolbox v<?php echo self::VERSION; ?></li>
					<li><a target="_blank" href="https://oxyplugins.com/downloads/oxy-toolbox/#changelog"><svg width="14" height="14" aria-hidden="true" focusable="false" data-prefix="fal" data-icon="book-open" class="svg-inline--fa fa-book-open fa-w-18" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M514.91 32h-.16c-24.08.12-144.75 8.83-219.56 48.09-4.05 2.12-10.33 2.12-14.38 0C205.99 40.83 85.32 32.12 61.25 32h-.16C27.4 32 0 58.47 0 91.01v296.7c0 31.41 25.41 57.28 57.85 58.9 34.77 1.76 122.03 8.26 181.89 30.37 5.27 1.95 10.64 3.02 16.25 3.02h64c5.62 0 10.99-1.08 16.26-3.02 59.87-22.11 147.12-28.61 181.92-30.37 32.41-1.62 57.82-27.48 57.82-58.89V91.01C576 58.47 548.6 32 514.91 32zM272 433c0 8.61-7.14 15.13-15.26 15.13-1.77 0-3.59-.31-5.39-.98-62.45-23.21-148.99-30.33-191.91-32.51-15.39-.77-27.44-12.6-27.44-26.93V91.01c0-14.89 13.06-27 29.09-27 19.28.1 122.46 7.38 192.12 38.29 11.26 5 18.64 15.75 18.66 27.84l.13 100.32V433zm272-45.29c0 14.33-12.05 26.16-27.45 26.93-42.92 2.18-129.46 9.3-191.91 32.51-1.8.67-3.62.98-5.39.98-8.11 0-15.26-6.52-15.26-15.13V230.46l.13-100.32c.01-12.09 7.4-22.84 18.66-27.84 69.66-30.91 172.84-38.19 192.12-38.29 16.03 0 29.09 12.11 29.09 27v296.7z"></path></svg> Changelog</a></li>
					<li><a target="_blank" href="https://www.facebook.com/groups/oxyplugins/"><svg width="14" height="14" aria-hidden="true" focusable="false" data-prefix="fab" data-icon="facebook-f" class="svg-inline--fa fa-facebook-f fa-w-10" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"></path></svg> Facebook Group</a></li>
					<li><a target="_blank" href="https://oxyplugins.com/support/"><svg width="14" height="14" aria-hidden="true" focusable="false" data-prefix="fal" data-icon="life-ring" class="svg-inline--fa fa-life-ring fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M256 8C119.033 8 8 119.033 8 256s111.033 248 248 248 248-111.033 248-248S392.967 8 256 8zm168.766 113.176l-62.885 62.885a128.711 128.711 0 0 0-33.941-33.941l62.885-62.885a217.323 217.323 0 0 1 33.941 33.941zM256 352c-52.935 0-96-43.065-96-96s43.065-96 96-96 96 43.065 96 96-43.065 96-96 96zM363.952 68.853l-66.14 66.14c-26.99-9.325-56.618-9.33-83.624 0l-66.139-66.14c66.716-38.524 149.23-38.499 215.903 0zM121.176 87.234l62.885 62.885a128.711 128.711 0 0 0-33.941 33.941l-62.885-62.885a217.323 217.323 0 0 1 33.941-33.941zm-52.323 60.814l66.139 66.14c-9.325 26.99-9.33 56.618 0 83.624l-66.139 66.14c-38.523-66.715-38.5-149.229 0-215.904zm18.381 242.776l62.885-62.885a128.711 128.711 0 0 0 33.941 33.941l-62.885 62.885a217.366 217.366 0 0 1-33.941-33.941zm60.814 52.323l66.139-66.14c26.99 9.325 56.618 9.33 83.624 0l66.14 66.14c-66.716 38.524-149.23 38.499-215.903 0zm242.776-18.381l-62.885-62.885a128.711 128.711 0 0 0 33.941-33.941l62.885 62.885a217.323 217.323 0 0 1-33.941 33.941zm52.323-60.814l-66.14-66.14c9.325-26.99 9.33-56.618 0-83.624l66.14-66.14c38.523 66.715 38.5 149.229 0 215.904z"></path></svg> Support</a></li>
				</ul>
			</div>
		</div>
		<?php
	}



	static function scripts() {

		do_action( self::PREFIX . 'enqueue_scripts', self::VERSION );
	}

	static function activate( $plugin ) {
		if ( ! defined( 'CT_FW_PATH' ) ) {
			die( '<p>\'Oxygen builder\' must be installed and activated, in order to activate \'' . self::TITLE . '\'</p>' );
		}
	}

	static function plugin_updater() {
		// retrieve our license key from the DB.
		$license_key = trim( get_option( self::PREFIX . 'license_key' ) );

		// setup the updater.
		$edd_updater = new OXY_Toolbox_Plugin_Updater(
			self::STORE_URL,
			__FILE__,
			array(
				'version'   => self::VERSION, // current version number
				'license'   => $license_key, // license key (used get_option above to retrieve from DB)
				'item_id'   => self::ITEM_ID, // ID of the product
				'item_name' => self::TITLE,
				'author'    => 'Oxy Plugins', // author of this plugin
				'url'       => home_url(),
				'beta'      => false,
			)
		);
	}

}

OxyToolbox::init();
