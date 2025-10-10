<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Oxy_Toolbox_Navigator {

	static $prefix;
	static $mod         = 'navigator_';
	static $title       = 'Navigator';
	static $description = 'Adds Templates and Pages in the WP Toolbar for editing the selected item with Oxygen directly. Also adds one-click access to edit any Page/Post/Product/ACF Field Group/Fluent Forms form.';
	static $link        = 'https://oxyplugins.com/doc/navigator/';
	static $order       = 'ASC';
	static $orderby     = 'post_title';

	public static function init( $prefix ) {
		self::$prefix    = $prefix;
		$oxy_toolbox_mod = new Oxy_Toolbox_Mod( $prefix, self::$mod, self::$title, self::$description, self::$link );
		add_action( self::$prefix . 'register_options', array( __CLASS__, 'mod_register_options' ) );
		// this will block the rest of the mod to load, if it is not checked.
		if ( 0 === intval( get_option( self::$prefix . self::$mod, 0 ) ) || OxyToolboxLicense::is_activated_license() !== true ) {
			return;
		}

		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		add_action( self::$prefix . self::$mod . 'form_options', array( __CLASS__, 'mod_form_options' ) );

		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_style' ) );

		add_action( self::$prefix . 'enqueue_scripts', array( __CLASS__, 'mod_scripts' ), 1 );

		// Add top level menu items.
		add_action( 'admin_bar_menu', array( __CLASS__, 'custom_edit_oxygen_templates_pages' ), 999 );

		// Edit with WordPress for Pages.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'pages', false ) ) {
			add_action( 'admin_bar_menu', array( __CLASS__, 'custom_edit_wp_pages_submenu' ), 999 );
		}

		// Edit with WordPress for Posts.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'posts', false ) ) {
			add_action( 'admin_bar_menu', array( __CLASS__, 'custom_edit_wp_posts_submenu' ), 999 );
		}

		// Edit with WordPress for Products.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'product', false ) ) {
			add_action( 'admin_bar_menu', array( __CLASS__, 'custom_edit_wc_products_submenu' ), 999 );
		}

		// Plugins.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'plugins', false ) ) {
			add_action( 'admin_bar_menu', array( __CLASS__, 'custom_edit_plugins_submenu' ), 1001 );
		}

		// Edit with Oxygen for Templates.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'templates', false ) ) {
			add_action( 'admin_bar_menu', array( __CLASS__, 'custom_edit_oxygen_templates_submenu' ), 999 );
		}

		// Edit with Oxygen for Pages.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'pages_oxygen', false ) ) {
			add_action( 'admin_bar_menu', array( __CLASS__, 'custom_edit_oxygen_pages_submenu' ), 999 );
		}

		// Oxygen.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'oxygen', false ) ) {
			add_action( 'admin_bar_menu', array( __CLASS__, 'custom_edit_oxygen_submenu' ), 1001 );
		}		

		// ACF.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'acf', false ) ) {
			add_action( 'admin_bar_menu', array( __CLASS__, 'custom_edit_acf_submenu' ), 999 );
		}

		// Fluent Forms.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'fluentforms', false ) ) {
			add_action( 'admin_bar_menu', array( __CLASS__, 'custom_edit_fluentforms_submenu' ), 999 );
		}

		// Show in Oxygen Editor.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'adminbar', false ) ) {
			add_action( 'plugins_loaded', array( __CLASS__, 'remove_oxygen_adminbar_action' ) );
			add_action( 'init', array( __CLASS__, 'wpdd_hide_admin_bar' ) );
		}		
		
		// Remove Customize menu item from the admin bar in Oxygen Editor.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'remove_customizer_in_editor', false ) ) {
			add_action( 'admin_bar_menu', array( __CLASS__, 'remove_customizer_in_editor_oxygen_adminbar' ), 999 );
		}		
		
		// Remove My Account menu item from the admin bar in Oxygen Editor.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'remove_my_account', false ) ) {
			add_action( 'admin_bar_menu', array( __CLASS__, 'remove_my_account_oxygen_adminbar' ), 999 );
		}		
		
	}

	/**
	 * Revert admin bar removal from the Oxygen editor.
	 */
	public static function remove_oxygen_adminbar_action() {
		remove_action( 'init', 'ct_hide_admin_bar' );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'wpdd_add_oxygen_editor_inline_css' ) );
	}
	/**
	 * Remove Customize item from admin bar in the Oxygen editor.
	 */
	public static function remove_customizer_in_editor_oxygen_adminbar( $wp_admin_bar ) {
		if ( ! defined( 'SHOW_CT_BUILDER' ) ) {
			return;
		}

		$wp_admin_bar->remove_node( 'customize' );
	}
	/**
	 * Remove My Account item from admin bar in the Oxygen editor.
	 */
	public static function remove_my_account_oxygen_adminbar( $wp_admin_bar ) {
		if ( ! defined( 'SHOW_CT_BUILDER' ) ) {
			return;
		}

		$wp_admin_bar->remove_node( 'my-account' );
	}

	/**
	 * Remove admin bar from the Oxygen editor's iframe.
	 */
	public static function wpdd_hide_admin_bar() {
		if ( defined( 'OXYGEN_IFRAME' ) ) {
			add_filter( 'show_admin_bar', '__return_false' );
		}
	}

	/**
	 * Add custom CSS in the Oxygen editor.
	 */
	public static function wpdd_add_oxygen_editor_inline_css() {
		if ( ! defined( 'SHOW_CT_BUILDER' ) ) {
			return;
		}

		$css = '
			#oxygen-ui .oxygen-add-section-library-flyout-panel,
			#oxygen-ui #oxygen-sidebar {
				top: calc(40px + var(--wp-admin--admin-bar--height));
				height: calc(100vh - 40px - var(--wp-admin--admin-bar--height));
			}

			#oxygen-ui .ct-panel-elements-managers,
			#oxygen-ui .oxygen-global-settings,
			#ct-viewport-container.oxy-fullscreen-toggle {
				top: calc(40px + var(--wp-admin--admin-bar--height));
			}

			.ng-scope .oxygen-sidebar-stylesheet-editor-wrap {
				height: calc(100vh - 264px - var(--wp-admin--admin-bar--height));
			}

			#ct-viewport-container #ct-viewport-ruller-wrap {
				height: calc(100vh - 40px - var(--wp-admin--admin-bar--height));
			}

			body .oxy-fullscreen-codemirror {
				top: 32px;
				height: calc(100vh - 32px);
			}

			body .oxy-fullscreen-codemirror .oxygen-sidebar-code-editor-wrap {
				height: calc(100vh - 92px) !important;
			}

			body #ct-dom-tree-2 {
				height: calc(100vh - 119px - var(--wp-admin--admin-bar--height)) !important;
			}
			';

		wp_add_inline_style( 'oxygen', $css );
	}


	public static function mod_register_options() {
		$option_names = array( 'adminbar', 'remove_customizer_in_editor', 'remove_my_account', 'product', 'fluentforms', 'acf', 'oxygen', 'new', 'plugins', 'pages', 'posts', 'templates', 'pages_oxygen', 'advanced_scripts' );

		foreach ( $option_names as $option_name ) {
			$initial = false;

			if(in_array($option_name, array('templates', 'pages_oxygen', 'oxygen'))) {
				$initial = 'true';
			}

			add_option( self::$prefix . self::$mod . $option_name, $initial );
			register_setting( self::$prefix . 'settings', self::$prefix . self::$mod . $option_name, array( __CLASS__, 'sanitize_module_option' ) );
		}
	}

	public static function sanitize_module_option( $show ) {

		if ( $show === 'true' ) {
			return 'true';
		}

		return '';
	}


	public static function mod_form_options() {
		?>

		<button type="button" class="collapsible"></button>
		<div class="module-settings">
			<div class="module-settings-wrap">

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>new" name="<?php echo self::$prefix . self::$mod; ?>new" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'new' ), 'true' ); ?> />
					<?php _e( '"New" Admin Bar Menu Additions' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>pages" name="<?php echo self::$prefix . self::$mod; ?>pages" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'pages' ), 'true' ); ?> />
					<?php _e( 'Pages (edit with WP)' ); ?>
				</div>
				
				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>posts" name="<?php echo self::$prefix . self::$mod; ?>posts" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'posts' ), 'true' ); ?> />
					<?php _e( 'Posts (edit with WP)' ); ?>
				</div>

				<?php if ( class_exists( 'WooCommerce' ) ) { ?>
					<div>
						<input id="<?php echo self::$prefix . self::$mod; ?>product" name="<?php echo self::$prefix . self::$mod; ?>product" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'product' ), 'true' ); ?> />
						<?php _e( 'Products (edit with WP)' ); ?>
					</div>
				<?php } ?>
				
				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>plugins" name="<?php echo self::$prefix . self::$mod; ?>plugins" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'plugins' ), 'true' ); ?> />
					<?php _e( 'Plugins' ); ?>
				</div>
				
				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>templates" name="<?php echo self::$prefix . self::$mod; ?>templates" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'templates' ), 'true' ); ?> />
					<?php _e( 'Templates (edit with Oxygen)' ); ?>
				</div>
				
				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>pages_oxygen" name="<?php echo self::$prefix . self::$mod; ?>pages_oxygen" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'pages_oxygen' ), 'true' ); ?> />
					<?php _e( 'Pages (edit with Oxygen)' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>oxygen" name="<?php echo self::$prefix . self::$mod; ?>oxygen" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'oxygen' ), 'true' ); ?> />
					<?php _e( 'Oxygen' ); ?>
				</div>

				<?php if ( class_exists( 'ACF' ) ) { ?>
				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>acf" name="<?php echo self::$prefix . self::$mod; ?>acf" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'acf' ), 'true' ); ?> />
					<?php _e( 'ACF Field Groups' ); ?>
				</div>
				<?php } ?>
				
				<?php if ( class_exists( '\ERROPiX\AdvancedScripts\ScriptsManager' ) ) { ?>
				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>advanced_scripts" name="<?php echo self::$prefix . self::$mod; ?>advanced_scripts" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'advanced_scripts' ), 'true' ); ?> />
					<?php _e( 'Advanced Scripts' ); ?>
				</div>
				<?php } ?>
				
				<?php if ( is_plugin_active( 'fluentform/fluentform.php' ) ) { ?>
				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>fluentforms" name="<?php echo self::$prefix . self::$mod; ?>fluentforms" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'fluentforms' ), 'true' ); ?> />
					<?php _e( "Fluent Forms' Forms" ); ?>
				</div>
				<?php } ?>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>adminbar" name="<?php echo self::$prefix . self::$mod; ?>adminbar" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'adminbar' ), 'true' ); ?> />
					<?php _e( 'Admin Bar in Oxygen Editor' ); ?>
				</div>
				<div class="indented">
					<input id="<?php echo self::$prefix . self::$mod; ?>remove_customizer_in_editor" name="<?php echo self::$prefix . self::$mod; ?>remove_customizer_in_editor" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'remove_customizer_in_editor' ), 'true' ); ?> />
					<?php _e( 'Remove Customize From Admin Bar in Oxygen Editor' ); ?>
				</div>
				<div class="indented">
					<input id="<?php echo self::$prefix . self::$mod; ?>remove_my_account" name="<?php echo self::$prefix . self::$mod; ?>remove_my_account" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'remove_my_account' ), 'true' ); ?> />
					<?php _e( 'Remove My Account (User Profile) From Admin Bar in Oxygen Editor' ); ?>
				</div>

			</div>
		</div>
	
		<?php
	}

	public static function admin_style( $version ) {
		wp_enqueue_style( self::$prefix . self::$mod . 'admin_style', plugins_url( 'css/style.css', __FILE__ ), null, $version );
	}

	public static function mod_scripts( $version ) {

		if ( defined( 'SHOW_CT_BUILDER' ) && defined( 'OXYGEN_IFRAME' ) && 'true' === get_option( self::$prefix . self::$mod . 'adminbar' ) ) { // only for builder UI
			wp_enqueue_script( self::$prefix . self::$mod . 'script', plugins_url( 'js/script.js', __FILE__ ), array( 'ct-angular-main' ), $version, true );
		}

		if ( is_admin_bar_showing() ) {
			wp_enqueue_style( self::$prefix . self::$mod . 'style', plugins_url( 'css/style.css', __FILE__ ), null, $version );
		}
	}



	/**
	 * Adds Templates and Pages menu items in the WordPress toolbar.
	 *
	 * @param object $wp_admin_bar WP_Admin_Bar instance, passed by reference.
	 */
	public static function custom_edit_oxygen_templates_pages( $wp_admin_bar ) {

		if ( ! function_exists( 'oxygen_vsb_current_user_can_access' ) || ! oxygen_vsb_current_user_can_access() ) {
			return;
		}

		$iconhtml = sprintf( '<img src="%s" />', plugins_url( 'img/oxygen-icon.png', __FILE__ ) );

		// Edit Page.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'pages', false ) ) {
			$wp_admin_bar->add_node(
				array(
					'id'    => 'oxy-toolbox-wp-pages',
					'title' => '<span class="ab-icon"></span>' . __( 'Pages' ),
					'href'  => admin_url( 'edit.php?post_type=page' ),
					'meta'  => array(
						'class' => 'oxy-toolbox-top-level-item',
						'title' => __( 'View All Pages' ),
					),
				)
			);
		}

		// Edit Post.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'posts', false ) ) {
			$wp_admin_bar->add_node(
				array(
					'id'    => 'oxy-toolbox-wp-posts',
					'title' => '<span class="ab-icon"></span>' . __( 'Posts' ),
					'href'  => admin_url( 'edit.php' ),
					'meta'  => array(
						'class' => 'oxy-toolbox-top-level-item',
						'title' => __( 'View All Posts' ),
					),
				)
			);
		}

		// Edit Product.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'product', false ) ) {
			if ( ! class_exists( 'WooCommerce' ) ) {
				return;
			}

			if ( is_singular( 'product' ) ) {
				$wp_admin_bar->add_node(
					array(
						'id'    => 'oxy-toolbox-wp-products',
						'title' => '<span class="ab-icon"></span>' . __( 'Edit Product' ),
						'href'  => esc_url( get_edit_post_link() ),
						'meta'  => array(
							'class' => 'oxy-toolbox-top-level-item',
							'title' => __( 'Edit Current Product' ),
						),
					)
				);
			} else {
				$wp_admin_bar->add_node(
					array(
						'id'    => 'oxy-toolbox-wp-products',
						'title' => '<span class="ab-icon"></span>' . __( 'Products' ),
						'href'  => admin_url( 'edit.php?post_type=product' ),
						'meta'  => array(
							'class' => 'oxy-toolbox-top-level-item',
							'title' => __( 'View All Products' ),
						),
					)
				);
			}
		}

		// Plugins menu item.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'plugins', false ) && current_user_can( 'manage_options' ) ) {
			$wp_admin_bar->add_node(
				array(
					'id'    => 'oxy-toolbox-plugins',
					'title' => '<span class="ab-icon"></span>' . __( 'Plugins' ),
					'href'  => admin_url( 'plugins.php' ),
					'meta'  => array(
						'class' => 'oxy-toolbox-top-level-item',
						'title' => __( 'Plugins List' ),
					),
				)
			);
		}

		// Templates menu item (Edit with Oxygen).
		if ( 'true' === get_option( self::$prefix . self::$mod . 'templates', false ) && current_user_can( 'manage_options' ) ) {
			$wp_admin_bar->add_node(
				array(
					'id'    => 'oxy-toolbox-oxy-templates',
					'title' => $iconhtml . __( 'Templates' ),
					'href'  => admin_url( 'edit.php?post_type=ct_template' ),
					'meta'  => array(
						'class' => 'oxy-toolbox-top-level-item',
						'title' => __( 'List of Oxygen Templates' ),
					),
				)
			);
		}

		// Pages menu item (Edit with Oxygen).
		if ( 'true' === get_option( self::$prefix . self::$mod . 'pages_oxygen', false ) && current_user_can( 'manage_options' ) ) {
			$wp_admin_bar->add_node(
				array(
					'id'    => 'oxy-toolbox-oxy-pages',
					'title' => $iconhtml . __( 'Pages' ),
					'href'  => admin_url( 'edit.php?post_type=page' ),
					'meta'  => array(
						'class' => 'oxy-toolbox-top-level-item',
						'title' => __( 'List of WordPress Pages' ),
					),
				)
			);
		}

		// Oxygen menu item.
		$users_access_list = get_option( 'oxygen_vsb_options_users_access_list', array() );

		if ( 'true' === get_option( self::$prefix . self::$mod . 'oxygen', false ) && ! ( isset( $users_access_list[get_current_user_id()] ) && 'true' !== $users_access_list[get_current_user_id()][0] ) ) {
			$wp_admin_bar->add_node(
				array(
					'id'    => 'oxy-toolbox-oxygen',
					'title' => $iconhtml . __( 'Oxygen' ),
					'href'  => admin_url( 'admin.php?page=ct_dashboard_page' ),
					'meta'  => array(
						'class' => 'oxy-toolbox-top-level-item',
						'title' => __( 'Oxygen Home' ),
					),
				)
			);
		}

		// ACF.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'acf', false ) ) {
			if ( ! class_exists( 'ACF' ) ) {
				return;
			}

			$wp_admin_bar->add_node(
				array(
					'id'    => 'oxy-toolbox-acf',
					'title' => '<span class="ab-icon"></span>' . __( 'ACF' ),
					'href'  => admin_url( 'edit.php?post_type=acf-field-group' ),
					'meta'  => array(
						'class' => 'oxy-toolbox-top-level-item',
						'title' => __( 'List of ACF Field Groups' ),
					),
				)
			);
		}
		
		// Advanced Scripts.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'advanced_scripts', false ) ) {
			if ( ! class_exists( '\ERROPiX\AdvancedScripts\ScriptsManager' ) ) {
				return;
			}

			$wp_admin_bar->add_node(
				array(
					'id'    => 'oxy-toolbox-advanced-scripts',
					'title' => '<span class="ab-icon"></span>' . __( 'Advanced Scripts' ),
					'href'  => admin_url( 'tools.php?page=advanced-scripts' ),
					'meta'  => array(
						'class' => 'oxy-toolbox-top-level-item',
						'title' => __( 'ERROPiX Advanced Scripts' ),
					),
				)
			);
		}

		// Fluent Forms.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'fluentforms', false ) ) {
			if ( ! is_plugin_active( 'fluentform/fluentform.php' ) ) {
				return;
			}

			$wp_admin_bar->add_node(
				array(
					'id'    => 'oxy-toolbox-fluentforms',
					'title' => '<span class="ab-icon"></span>' . __( 'Fluent Forms' ),
					'href'  => admin_url( 'admin.php?page=fluent_forms' ),
					'meta'  => array(
						'class' => 'oxy-toolbox-top-level-item',
						'title' => __( 'Manage Fluent Forms' ),
					),
				)
			);
		}

		// New.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'new', false ) && current_user_can( 'manage_options' ) ) {
			// ACF Field Group.
			if ( class_exists( 'ACF' ) ) {
				$wp_admin_bar->add_node(
					array(
						'id'     => 'oxy-toolbox-add-acf-field-group',
						'parent' => 'new-content',
						'title'  => __( 'ACF Field Group' ),
						'href'   => admin_url( 'post-new.php?post_type=acf-field-group' ),
						'meta'   => array(
							'class' => 'oxy-toolbox-new-item',
						),
					)
				);
			}

			// Code Snippet.
			if ( is_plugin_active( 'code-snippets/code-snippets.php' ) ) {
				$wp_admin_bar->add_node(
					array(
						'id'     => 'oxy-toolbox-add-code-snippet',
						'parent' => 'new-content',
						'title'  => __( 'Code Snippet' ),
						'href'   => admin_url( 'admin.php?page=add-snippet' ),
						'meta'   => array(
							'class' => 'oxy-toolbox-new-item',
						),
					)
				);
			}

			// Fluent Form.
			if ( is_plugin_active( 'fluentform/fluentform.php' ) ) {
				$wp_admin_bar->add_node(
					array(
						'id'     => 'oxy-toolbox-add-fluent-form',
						'parent' => 'new-content',
						'title'  => __( 'Fluent Form' ),
						'href'   => admin_url( 'admin.php?page=fluent_forms#add=1' ),
						'meta'   => array(
							'class' => 'oxy-toolbox-new-item',
						),
					)
				);
			}

			// Reusable.
			if ( ! ( isset( $users_access_list[get_current_user_id()] ) && 'true' !== $users_access_list[get_current_user_id()][0] ) ) {
				$wp_admin_bar->add_node(
					array(
						'id'     => 'oxy-toolbox-add-reusable',
						'parent' => 'new-content',
						'title'  => __( 'Reusable Part' ),
						'href'   => admin_url( 'post-new.php?post_type=ct_template&is_reusable=true' ),
						'meta'   => array(
							'class' => 'oxy-toolbox-new-item',
						),
					)
				);
			}
		}

	}

	/**
	 * Adds WordPress Pages as submenu items to the Pages menu item in the WordPress toolbar for editing with WordPress editor.
	 *
	 * @param object $wp_admin_bar WP_Admin_Bar instance, passed by reference.
	 */
	public static function custom_edit_wp_pages_submenu( $wp_admin_bar ) {

		if ( ! function_exists( 'oxygen_vsb_current_user_can_access' ) || ! oxygen_vsb_current_user_can_access() ) {
			return;
		}

		require_once 'inc/wordpress-pages.php';

	}

	/**
	 * Adds WordPress Posts as submenu items to the Posts menu item in the WordPress toolbar for editing with WordPress editor.
	 *
	 * @param object $wp_admin_bar WP_Admin_Bar instance, passed by reference.
	 */
	public static function custom_edit_wp_posts_submenu( $wp_admin_bar ) {

		if ( ! function_exists( 'oxygen_vsb_current_user_can_access' ) || ! oxygen_vsb_current_user_can_access() ) {
			return;
		}

		require_once 'inc/wordpress-posts.php';

	}

	/**
	 * Adds WordPress Products as submenu items to the Products menu item in the WordPress toolbar for editing with WordPress editor.
	 *
	 * @param object $wp_admin_bar WP_Admin_Bar instance, passed by reference.
	 */
	public static function custom_edit_wc_products_submenu( $wp_admin_bar ) {

		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		if ( ! function_exists( 'oxygen_vsb_current_user_can_access' ) || ! oxygen_vsb_current_user_can_access() ) {
			return;
		}

		require_once 'inc/products.php';
	}

	/**
	 * Adds Fluent Forms forms as submenu items to the Fluent Forms menu item in the WordPress toolbar.
	 *
	 * @param object $wp_admin_bar WP_Admin_Bar instance, passed by reference.
	 */
	public static function custom_edit_fluentforms_submenu( $wp_admin_bar ) {

		if ( ! is_plugin_active( 'fluentform/fluentform.php' ) ) {
			return;
		}

		if ( ! function_exists( 'oxygen_vsb_current_user_can_access' ) || ! oxygen_vsb_current_user_can_access() ) {
			return;
		}

		require_once 'inc/fluentforms.php';

	}

	/**
	 * Adds WordPress Products as submenu items to the Products menu item in the WordPress toolbar for editing with WordPress editor.
	 *
	 * @param object $wp_admin_bar WP_Admin_Bar instance, passed by reference.
	 */
	public static function custom_edit_acf_submenu( $wp_admin_bar ) {

		if ( ! class_exists( 'ACF' ) ) {
			return;
		}

		if ( ! function_exists( 'oxygen_vsb_current_user_can_access' ) || ! oxygen_vsb_current_user_can_access() ) {
			return;
		}

		require_once 'inc/acf.php';

	}

	/**
	 * Adds Oxygen Templates as submenu items to the Templates menu item in the WordPress toolbar.
	 *
	 * @param object $wp_admin_bar WP_Admin_Bar instance, passed by reference.
	 */
	public static function custom_edit_oxygen_templates_submenu( $wp_admin_bar ) {

		if ( ! function_exists( 'oxygen_vsb_current_user_can_access' ) || ! oxygen_vsb_current_user_can_access() ) {
			return;
		}

		require_once 'inc/oxygen-templates.php';

	}

	/**
	 * Adds WordPress Pages as submenu items to the Pages menu item in the WordPress toolbar.
	 *
	 * @param object $wp_admin_bar WP_Admin_Bar instance, passed by reference
	 */
	public static function custom_edit_oxygen_pages_submenu( $wp_admin_bar ) {

		if ( ! function_exists( 'oxygen_vsb_current_user_can_access' ) || ! oxygen_vsb_current_user_can_access() ) {
			return;
		}

		require_once 'inc/oxygen-pages.php';

	}

	/**
	 * Adds Oxygen's submenu items to the Oxygen menu item in the WordPress toolbar.
	 *
	 * @param object $wp_admin_bar WP_Admin_Bar instance, passed by reference
	 */
	public static function custom_edit_oxygen_submenu( $wp_admin_bar ) {

		if ( ! function_exists( 'oxygen_vsb_current_user_can_access' ) || ! oxygen_vsb_current_user_can_access() ) {
			return;
		}

		$users_access_list = get_option("oxygen_vsb_options_users_access_list", array());

		if ( isset( $users_access_list[get_current_user_id()] ) && $users_access_list[get_current_user_id()][0] !== 'true' ) {
			return;
		}

		require_once 'inc/oxygen.php';

	}
	
	/**
	 * Adds Plugin's submenu items to the Plugins menu item in the WordPress toolbar.
	 *
	 * @param object $wp_admin_bar WP_Admin_Bar instance, passed by reference
	 */
	public static function custom_edit_plugins_submenu( $wp_admin_bar ) {

		if ( ! function_exists( 'oxygen_vsb_current_user_can_access' ) || ! oxygen_vsb_current_user_can_access() ) {
			return;
		}

		require_once 'inc/plugins.php';

	}

}

Oxy_Toolbox_Navigator::init( self::PREFIX );
