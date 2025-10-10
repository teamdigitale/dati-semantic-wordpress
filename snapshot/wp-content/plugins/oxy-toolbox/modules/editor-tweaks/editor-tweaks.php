<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Oxy_Toolbox_EditorTweaks {

	static $prefix;
	static $mod         = 'editor_tweaks_';
	static $title       = 'Editor Tweaks';
	static $description = "Adds options to tweak Oxygen editor's UI.";
	static $link        = 'https://oxyplugins.com/doc/editor-tweaks/';

	static function init( $prefix ) {
		self::$prefix    = $prefix;
		$oxy_toolbox_mod = new Oxy_Toolbox_Mod( $prefix, self::$mod, self::$title, self::$description, self::$link );
		add_action( self::$prefix . 'register_options', array( __CLASS__, 'mod_register_options' ) );
		// this will block the rest of the mod to load, if it is not checked.
		if ( 0 === intval( get_option( self::$prefix . self::$mod, 0 ) ) || OxyToolboxLicense::is_activated_license() !== true ) {
			return;
		}

		add_action( self::$prefix . self::$mod . 'form_options', array( __CLASS__, 'mod_form_options' ) );

		// Back To WP Additions.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'back_to_wp_additions', false ) ) {
			add_action( self::$prefix . 'enqueue_scripts', array( __CLASS__, 'back_to_wp_additions_scripts' ) );
		}

		// if both Back To WP Additions and Expanded Oxygen Toolbar Menus are checked.
		// if ( 'true' === get_option( self::$prefix . self::$mod . 'back_to_wp_additions', false ) && 'true' === get_option( self::$prefix . self::$mod . 'expanded_oxygen_toolbar_menus', false ) ) {
		// 	add_action( 'wp_enqueue_scripts', array( __CLASS__, 'add_oxygen_editor_inline_css_back_to_wp_additions' ), 12 );
		// }

		// Components Panel.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'components_panel', false ) ) {
			add_action( 'ct_before_builder', array( __CLASS__, 'add_controls' ) );
			add_action( self::$prefix . 'enqueue_scripts', array( __CLASS__, 'components_panel_scripts' ) );
		}

		// Currently Editing.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'currently_editing', false ) ) {
			add_action( self::$prefix . 'enqueue_scripts', array( __CLASS__, 'currently_editing_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'add_oxygen_editor_inline_css_currently_editing' ), 11 );
		}
		
		// Easy Panels.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'easy_panels', false ) ) {
			add_action( 'wp_footer', array( __CLASS__, 'footer_scripts' ) );
		}

		// Open Back To WP Menu Links In New Tabs.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'open_back_to_wp_menu_links_in_new_tabs', false ) ) {
			add_action( self::$prefix . 'enqueue_scripts', array( __CLASS__, 'open_back_to_wp_menu_links_in_new_tabs_scripts' ) );
		}

		// Remove min-height for empty Divs.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'remove_min_height_empty_divs', false ) ) {
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'add_oxygen_editor_inline_css_remove_min_height_empty_divs' ), 11 );
		}

		// CSS Tweaks.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'css_tweaks', false ) ) {
			add_action( self::$prefix . 'enqueue_scripts', array( __CLASS__, 'css_tweaks_scripts' ) );
		}

		// Expanded Measure Box Units.
		// if ( 'true' === get_option( self::$prefix . self::$mod . 'expanded_measure_box_units', false ) ) {
		// 	add_action( 'wp_enqueue_scripts', array( __CLASS__, 'add_oxygen_editor_inline_css_expanded_measure_box_units' ), 11 );
		// }

		// Wider Library Flyout Panel.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'wider_library_flyout_panel', false ) ) {
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'add_oxygen_editor_inline_css_wider_library_flyout_panel' ), 11 );
		}

		// autosave 
		if( 0 !== intval(get_option( self::$prefix . self::$mod . 'autosave', 0 ))) {
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'auto_save_scripts' ), 11 );
		}

		// Highlight Active Options.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'highlight_active_options', false ) ) {
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'add_oxygen_editor_inline_css_highlight_active_options' ), 11 );
		}
		
		// Disable Oxygen Composite Elements.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'disable_oxygen_composite_elements', false ) ) {
			add_action( 'plugins_loaded', array( __CLASS__, 'disable_oxygen_composite_elements' ) );
		}

		// Copy Selector Button.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'copy_selector', false ) && 'true' !== get_option( self::$prefix . self::$mod . 'copy_selector_with_prefix', false ) ) {
			add_action( self::$prefix . 'enqueue_scripts', array( __CLASS__, 'copy_selector_scripts' ) );
		}
		// Copy Selector Button With Prefix.
		if ( 'true' === get_option( self::$prefix . self::$mod . 'copy_selector_with_prefix', false ) && 'true' === get_option( self::$prefix . self::$mod . 'copy_selector', false ) ) {
			add_action( self::$prefix . 'enqueue_scripts', array( __CLASS__, 'copy_selector_with_prefix_scripts' ) );
		}
	}

	static function disable_oxygen_composite_elements() {
		remove_action( 'init', 'run_oxygen_composite_elements' );
	}

	static function auto_save_scripts( ) {

		if ( ! defined( 'SHOW_CT_BUILDER' ) ) {
			return;
		}

		if ( defined( 'OXYGEN_IFRAME' ) ) {
			wp_register_script( self::$prefix . self::$mod . 'autosave', plugins_url( 'js/auto-save/script.js', __FILE__ ), array( 'ct-angular-main' ) );
			wp_localize_script( self::$prefix . self::$mod . 'autosave', self::$prefix . self::$mod . 'options', array(
				'autosave' => get_option( self::$prefix . self::$mod . 'autosave', 0 ),
				'smartautosave' => get_option( self::$prefix . self::$mod . 'smartautosave', 0 )
			));
			wp_enqueue_script( self::$prefix . self::$mod . 'autosave' );
		}
	}

	public static function back_to_wp_additions_scripts( $version ) {
		if ( ! defined( 'SHOW_CT_BUILDER' ) ) {
			return;
		}

		wp_register_script( self::$prefix . self::$mod . 'back_to_wp_additions_script', plugins_url( 'js/back-to-wp-additions/script.js', __FILE__ ), array( 'jquery' ), $version, true );

		wp_enqueue_script( self::$prefix . self::$mod . 'back_to_wp_additions_script' );

		wp_localize_script(
			self::$prefix . self::$mod . 'back_to_wp_additions_script',
			self::$prefix . self::$mod . 'admin_urls',
			array(
				'pages'     => admin_url( 'edit.php?post_type=page' ),
				'templates' => admin_url( 'edit.php?post_type=ct_template' ),
			)
		);
	}

	/**
	 * Add custom CSS in the Oxygen editor.
	 */
	public static function add_oxygen_editor_inline_css_currently_editing() {
		if ( ! defined( 'SHOW_CT_BUILDER' ) ) {
			return;
		}

		$css = '
		.oxygen-editor-currently-editing {
			font-size: 13px;
			text-transform: uppercase;
			letter-spacing: 1px;
			align-items: center;
			display: flex;
			padding: 0 10px;
		}';

		wp_add_inline_style( 'oxygen', $css );
	}

	public static function currently_editing_scripts( $version ) {
		if ( ! defined( 'SHOW_CT_BUILDER' ) ) {
			return;
		}

		wp_register_script( self::$prefix . self::$mod . 'currently_editing_script', plugins_url( 'js/currently-editing/script.js', __FILE__ ), array( 'jquery' ), $version, true );

		wp_enqueue_script( self::$prefix . self::$mod . 'currently_editing_script' );

		wp_localize_script( self::$prefix . self::$mod . 'currently_editing_script', self::$prefix . self::$mod . 'current_entry', array( 'name' => get_the_title() ) );
	}

	public static function open_back_to_wp_menu_links_in_new_tabs_scripts( $version ) {
		if ( ! defined( 'SHOW_CT_BUILDER' ) ) {
			return;
		}

		wp_register_script( self::$prefix . self::$mod . 'open_back_to_wp_menu_links_in_new_tabs_script', plugins_url( 'js/open-back-to-wp-menu-links-in-new-tabs/script.js', __FILE__ ), array( 'jquery' ), $version, true );

		wp_enqueue_script( self::$prefix . self::$mod . 'open_back_to_wp_menu_links_in_new_tabs_script' );
	}

	public static function footer_scripts() {
		if ( ! defined( 'SHOW_CT_BUILDER' ) ) {
			return;
		}

		if ( defined( 'OXYGEN_IFRAME' ) ) {
			
			?>
			<script type="text/javascript">
				(($) => {
					$(document).ready(() => {
					    let navbar = window.parent.document.createElement('div');
					    let showtabs = <?php echo 'true' === get_option( self::$prefix . self::$mod . 'easy_panels_show_tabs', false ) ? 'true' : 'false';?>;
					    
					    navbar.classList.add('toolbox-easypanel');
					    navbar.innerHTML = '<div class="toolbox-easypanel-button" ng-click="styleTabAdvance=false" ng-class="{\'active\': !styleTabAdvance}">P<span>Primary</span></div>';
					    
					    let faux = {
					    	switchTab: (param1, param2) => {
					    		return param2;
					    	}
					    }
					    window.parent.document.querySelectorAll(`.oxygen-sidebar-advanced-home div[ng-click^="switchTab('advanced',"]`).forEach(item => {
					        
					        if(item.getAttribute('ng-click').indexOf('code') > -1) {
					            return;
					        }

					        let images = item.getElementsByTagName('img');
					        let text = item.innerText;
					        
					        if(text) {
					        	text = text.trim();
					        }
					        
					        let icon = images[0];

					        if(icon) {
					            let container = document.createElement('div');
					            container.appendChild( icon.cloneNode() );
					            let ngclick = item.getAttribute('ng-click');
					            let panelname =  eval('faux.'+ngclick);

					            container.classList.add('toolbox-easypanel-button');
					            container.setAttribute('ng-click', 'styleTabAdvance=true; ' + ngclick );
					            container.setAttribute('ng-class', "{'active': styleTabAdvance && "+item.getAttribute('ng-click').replace('switchTab', 'isShowTab')+", 'oxy-styles-present': iframeScope.isTabHasOptions('"+ panelname +"')}");
					            
					            container.style.cursor = 'pointer';

					            let tooltip = document.createElement('span');
					            tooltip.innerText =  text;
					            container.appendChild(tooltip);
					            navbar.appendChild( container );
					        }
					        
					    });
					    
					    $(document, parent.document).injector().invoke(function($compile) {
					        $('.oxygen-sidebar-tabs', parent.document).parent().prepend($compile(navbar.outerHTML)(iframeScope.parentScope));
					    });

					    if( !showtabs ) {
					    	$('.oxygen-sidebar-tabs', parent.document).hide();
					    }
					});
				    
				})(jQuery)
			</script>

			<?php
		} else {
			?>
			<style type="text/css">
				.toolbox-easypanel {
				  display: flex;
				  width: 100%;
				  justify-content: start;
				  margin-bottom: 10px;
				  padding-left: 8px;
				}


				.toolbox-easypanel-button {
					display: flex;
					width: 22px;
					height: 22px;
					align-items: center;
					justify-content: center;
					margin-right: 7px;
					cursor: pointer;
					position: relative;
				  	margin-top: 10px;
				  	padding: 4px;
				}

				.toolbox-easypanel-button.oxy-styles-present:after {
					content: "";
				    width: 5px;
				    height: 2px;
				    border-radius: 1px;
				    background-color: rgba(218,231,255,1);
				    box-shadow: 0px 1px 5px rgb(70 136 200), 0px -1px 4px rgb(70 136 200), 2px 0px 5px rgb(70 136 200), -2px 0px 5px rgb(70 136 200);
				    position: absolute;
				    top: -6px;
				    left: calc(50% - 3px);
				}

				.toolbox-easypanel-button img {
					width 100%;
					height: 100%;
					object-fit: contain;
				}
				.toolbox-easypanel-button span {
				  opacity: 0;
				  font-size: 12px;
				  position: absolute;
				  width: auto;
				  display: flex;
				  justify-content: center;
				  top: -60%;
				  white-space: nowrap;
				}

				.toolbox-easypanel-button:hover span {
				  opacity: 1;
				  top: -100%;
				  transition: all 0.3s;
				}

				.toolbox-easypanel-button:last-child {
  					margin-right: 0;
				}
				.toolbox-easypanel-button.active {
				  box-shadow: inset 1px 1px 2px #000;
				}
			</style>
			<?php
		}
	}

	/**
	 * Add custom CSS in the Oxygen editor.
	 */
	public static function add_oxygen_editor_inline_css_back_to_wp_additions() {
		if ( ! defined( 'SHOW_CT_BUILDER' ) ) {
			return;
		}

		$css = '
			#oxygen-topbar .oxygen-toolbar-menus:not(.oxygen-undo-redo-menus) {
				width: 280px;
		  	}';

		wp_add_inline_style( 'oxygen', $css );
	}

	/**
	 * Add custom CSS in the Oxygen editor.
	 */
	public static function add_oxygen_editor_inline_css_highlight_active_options() {
		if ( ! defined( 'SHOW_CT_BUILDER' ) ) {
			return;
		}

		// if this is the Oxygen's frame, abort.
		if ( defined( 'OXYGEN_IFRAME' ) ) {
			return;
		}

		$css = '
			body .oxygen-button-list .oxygen-button-list-button-default {
				outline: 2px dotted rgba(70,136,200);
			}'
		;

		wp_add_inline_style( 'oxygen', $css );
	}

	/**
	 * Adds controls in the Components Panel.
	 */
	public static function add_controls() {
		if ( ! defined( 'SHOW_CT_BUILDER' ) ) {
			return;
		}
		?>
		<div class="otb-components-panel" style="display: none;">
			<div id="otb-containers" class="otb-control-group">
				<h2 class="otb-control-group-heading"><?php _e( 'Containers', 'oxygen' ); ?></h2>
				<div class="otb-controls"><?php do_action( 'oxygen_basics_components_containers' ); ?></div>
			</div>
			<div id="otb-text" class="otb-control-group">
				<h2 class="otb-control-group-heading"><?php _e( 'Text', 'oxygen' ); ?></h2>
				<div class="otb-controls"><?php do_action( 'oxygen_basics_components_text' ); ?></div>
			</div>
			<div id="otb-links" class="otb-control-group">
				<h2 class="otb-control-group-heading"><?php _e( 'Links', 'oxygen' ); ?></h2>
				<div class="otb-controls"><?php do_action( 'oxygen_basics_components_links' ); ?></div>
			</div>
			<div id="otb-visual" class="otb-control-group">
				<h2 class="otb-control-group-heading"><?php _e( 'Visual', 'oxygen' ); ?></h2>
				<div class="otb-controls"><?php do_action( 'oxygen_basics_components_visual' ); ?></div>
			</div>
			<div id="otb-other" class="otb-control-group">
				<h2 class="otb-control-group-heading"><?php _e( 'Other', 'oxygen' ); ?></h2>
				<div class="otb-controls"><?php do_action( 'ct_toolbar_fundamentals_list' ); ?></div>
			</div>

			<div id="otb-composite" class="otb-control-group">
				<h2 class="otb-control-group-heading"><?php _e( 'Composite', 'oxygen' ); ?></h2>
				<div class="otb-controls"><?php do_action( 'oxygen_helpers_components_composite' ); ?></div>
			</div>
			<div id="otb-dynamic" class="otb-control-group">
				<h2 class="otb-control-group-heading"><?php _e( 'Dynamic', 'oxygen' ); ?></h2>
				<div class="otb-controls"><?php do_action( 'oxygen_helpers_components_dynamic' ); ?></div>
			</div>
			<div id="otb-interactive" class="otb-control-group">
				<h2 class="otb-control-group-heading"><?php _e( 'Interactive', 'oxygen' ); ?></h2>
				<div class="otb-controls"><?php do_action( 'oxygen_helpers_components_interactive' ); ?></div>
			</div>
			<!-- <div id="otb-external" class="otb-control-group"> -->
				<!-- <h2 class="otb-control-group-heading"><?php // _e( 'External', 'oxygen' ); ?></h2> -->
				<!-- <div class="otb-controls"><?php // do_action( 'oxygen_helpers_components_external' ); ?></div> -->
			<!-- </div> -->
			<div id="otb-wp-components" class="otb-control-group">
				<h2 class="otb-control-group-heading"><?php _e( 'WordPress', 'oxygen' ); ?></h2>
				<div class="otb-controls"><?php do_action( 'oxy_folder_wordpress_components' ); ?></div>
			</div>
			<div id="otb-wp-dynamic" class="otb-control-group">
				<h2 class="otb-control-group-heading"><?php _e( 'Dynamic Data', 'oxygen' ); ?></h2>
				<div class="otb-controls"><?php do_action( 'ct_toolbar_data_folder' ); ?></div>
			</div>
			<!-- <div id="otb-reusables" class="otb-control-group"> -->
				<?php // do_action( 'ct_toolbar_reusable_parts' ); ?>
			<!-- </div> -->
			<div id="otb-wc" class="otb-control-group">
				<h2 class="otb-control-group-heading"><?php _e( 'WooCommerce', 'oxygen' ); ?></h2>
				<div class="otb-controls"><?php do_action( 'oxygen_add_plus_searchable_list' ); ?></div>
			</div>
		</div>
		<?php
	}

	/**
	 * Components panel.
	 */
	public static function components_panel_scripts( $version ) {
		if ( ! defined( 'SHOW_CT_BUILDER' ) ) {
			return;
		}

		wp_enqueue_style( self::$prefix . self::$mod . 'components_panel_style', plugins_url( 'css/components-panel/style.css', __FILE__ ), array(), $version );

		wp_register_script( self::$prefix . self::$mod . 'components_panel_script', plugins_url( 'js/components-panel/script.js', __FILE__ ), array( 'jquery' ), $version, true );
		
		if ( 'true' === get_option( self::$prefix . self::$mod . 'components_panel_click', false ) ) {
			wp_localize_script( self::$prefix . self::$mod . 'components_panel_script', self::$prefix . self::$mod . 'cpoptions', array(
				'onclick' => '1'
			));
		}

		wp_enqueue_script( self::$prefix . self::$mod . 'components_panel_script' );

	}

	/**
	 * Remove min-height for empty Divs.
	 */
	static function add_oxygen_editor_inline_css_remove_min_height_empty_divs() {
		if ( ! defined( 'SHOW_CT_BUILDER' ) || ! defined( 'OXYGEN_IFRAME' ) ) {
			return;
		}

		$css = '
			body .ct-div-block:empty {
				min-width: 0;
				min-height: 0;
		  	}';

		wp_add_inline_style( 'oxygen', $css );
	}

	/**
	 * CSS Tweaks
	 */
	static function css_tweaks_scripts( $version ) {
		if ( ! defined( 'SHOW_CT_BUILDER' ) ) {
			return;
		}

		wp_register_style( self::$prefix . self::$mod . 'css_tweaks', plugins_url( 'css/style.css', __FILE__ ), array(), $version, false );

		wp_enqueue_style( self::$prefix . self::$mod . 'css_tweaks' );
	}

	/**
	 * Media Query Buttons.
	 */
	static function media_query_buttons_scripts( $version ) {
		if ( ! defined( 'SHOW_CT_BUILDER' ) ) {
			return;
		}

		wp_register_style( self::$prefix . self::$mod . 'media_query_buttons', plugins_url( 'css/balloon/balloon.min.css', __FILE__ ), array(), $version, false );
		wp_enqueue_style( self::$prefix . self::$mod . 'media_query_buttons' );

		if ( ! defined( 'OXYGEN_IFRAME' ) ) { // load it in the iframe
			return;
		}
		wp_register_script( self::$prefix . self::$mod . 'media_query_buttons_script', plugins_url( 'js/media-query-buttons/script.js', __FILE__ ), array( 'jquery' ), $version, true );
		wp_enqueue_script( self::$prefix . self::$mod . 'media_query_buttons_script' );
	}

	/**
	 * Copy Selector Button.
	 */
	static function copy_selector_scripts( $version ) {
		if ( ! defined( 'OXYGEN_IFRAME' ) ) { // load it in the iframe
			return;
		}
		wp_enqueue_script( self::$prefix . self::$mod . 'copy_selector_scripts', plugins_url( 'js/copy-selector/script.js', __FILE__ ), array( 'jquery' ), $version, true );
	}
	/**
	 * Copy Selector Button With Prefix.
	 */
	static function copy_selector_with_prefix_scripts( $version ) {
		if ( ! defined( 'OXYGEN_IFRAME' ) ) { // load it in the iframe
			return;
		}
		wp_enqueue_script( self::$prefix . self::$mod . 'copy_selector_with_prefix', plugins_url( 'js/copy-selector-with-prefix/script.js', __FILE__ ), array( 'jquery' ), $version, true );
	}

	/**
	 * Expanded Select Box Options.
	 */
	// static function add_oxygen_editor_inline_css_expanded_measure_box_units($version = null) {
	// 	if ( ! defined( 'SHOW_CT_BUILDER' ) ) {
	// 		return;
	// 	}

	// 	wp_register_style( self::$prefix . self::$mod . 'expanded_select_box_options', plugins_url( 'css/expanded-select-box-options.css', __FILE__ ), array(), $version, false );

	// 	wp_enqueue_style( self::$prefix . self::$mod . 'expanded_select_box_options' );
	// }
	
	/**
	 * Media Query Buttons.
	 */
	static function add_oxygen_editor_inline_css_media_query_buttons() {
		if ( ! defined( 'SHOW_CT_BUILDER' ) ) {
			return;
		}

		$css = '
			body .oxygen-media-query-box-wrapper {
				position: static;
			}
		
			body .oxygen-sidebar-currently-editing:not(.ng-hide) {
				position: relative;
				padding-bottom: 58px;
			}
			body .oxygen-sidebar-currently-editing.oxygen-sidebar-currently-editing-top.ng-scope {
				position: static;
				padding-bottom: 0;
			}
			
			.oxygen-media-query-box-wrapper .oxygen-media-query-box {
				display: none;
			}
			
			.oxygen-media-query-box-wrapper .oxygen-media-query-dropdown {
				display: flex;
				/* flex-direction: row-reverse; */
				width: 268px;
				left: 12px;
				bottom: 18px;
				top: unset;
				background: transparent;
				box-shadow: none;
				z-index: 2;
				overflow: visible;
			}
			
			.oxygen-media-query-dropdown span {
				position: absolute;
				top: 26px;
				width: 18px;
				height: 3px;
				border-radius: 2px;
				text-shadow: none !important;
			}
			
			.oxygen-media-query-dropdown span.oxygen-current-media-query {
				background: #26a0f5;
			}
			
			body .oxygen-media-query-dropdown li {
				position: relative;
				margin: 0px 10px;
			}

			body .oxygen-media-query-dropdown li > img:first-child {
				padding-right: 0;
			}

			.oxygen-media-query-dropdown li span + img {
				position: absolute;
				height: 16px;
				right: -12px;
			}
			
			body .oxygen-active-select .oxygen-media-query-dropdown {
				display: flex;
			}

			/* body .oxygen-media-query-dropdown li:last-child:after {
				--balloon-font-size: 10px;
			} */

			.oxygen-media-query-box-wrapper:hover .oxygen-media-query-dropdown {
				display: flex;
				top: unset;
			}
			';

		wp_add_inline_style( 'oxygen', $css );
	}

	/**
	 * Wider Library Flyout Panel.
	 */
	static function add_oxygen_editor_inline_css_wider_library_flyout_panel() {
		if ( ! defined( 'SHOW_CT_BUILDER' ) ) {
			return;
		}

		$css = '
				body .oxygen-add-section-library-flyout-panel {
					width: 600px;
					left: -300px;
				}

				body .oxygen-add-section-library-flyout-panel-open {
					left: 300px;
				}
			';

		wp_add_inline_style( 'oxygen', $css );
	}

	static function mod_register_options() {
		// $option_names = array( 'back_to_wp_additions', 'compact_view_for_element_buttons', 'components_panel', 'components_panel_click', 'currently_editing', 'open_back_to_wp_menu_links_in_new_tabs', 'easy_panels', 'easy_panels_show_tabs', 'remove_min_height_empty_divs', 'css_tweaks', 'media_query_buttons', 'expanded_measure_box_units', 'wider_library_flyout_panel', 'small_to_large_media_queries', 'highlight_active_options', 'disable_oxygen_composite_elements', 'copy_selector', 'copy_selector_with_prefix' );
		$option_names = array( 'back_to_wp_additions', 'components_panel', 'components_panel_click', 'currently_editing', 'open_back_to_wp_menu_links_in_new_tabs', 'easy_panels', 'easy_panels_show_tabs', 'remove_min_height_empty_divs', 'css_tweaks', 'media_query_buttons', 'wider_library_flyout_panel', 'highlight_active_options', 'disable_oxygen_composite_elements', 'copy_selector', 'copy_selector_with_prefix' );

		foreach ( $option_names as $option_name ) {
			add_option( self::$prefix . self::$mod . $option_name, false );
			register_setting( self::$prefix . 'settings', self::$prefix . self::$mod . $option_name, array( __CLASS__, 'sanitize_module_option' ) );
		}

		add_option( self::$prefix . self::$mod . 'autosave', 0 );
		add_option( self::$prefix . self::$mod . 'smartautosave', 1 );

		register_setting( self::$prefix . 'settings', self::$prefix . self::$mod . 'autosave', array(__CLASS__, 'sanitize_autosave') );
		register_setting( self::$prefix . 'settings', self::$prefix . self::$mod . 'smartautosave', array(__CLASS__, 'sanitize_smartautosave') );
	}

	static function sanitize_smartautosave( $val ) {
		if ( is_numeric( $val ) ) {
			return intval( $val );
		}

		return 0;
	}

	static function sanitize_autosave( $minutes ) {
		if ( is_numeric( $minutes ) ) {
			return intval( $minutes );
		}

		return 0;
	}

	static function sanitize_module_option( $show ) {

		if ( $show === 'true' ) {
			return 'true';
		}

		return '';
	}


	static function mod_form_options() { ?>

		<button type="button" class="collapsible"></button>
		<div class="module-settings">
			<div class="module-settings-wrap">
				<div>
					<div style="display: flex; align-items: center;">
						<svg width="16" height="16" style="margin-right: 8px;" aria-hidden="true" focusable="false" data-prefix="fal" data-icon="save" class="svg-inline--fa fa-save fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M433.941 129.941l-83.882-83.882A48 48 0 0 0 316.118 32H48C21.49 32 0 53.49 0 80v352c0 26.51 21.49 48 48 48h352c26.51 0 48-21.49 48-48V163.882a48 48 0 0 0-14.059-33.941zM288 64v96H96V64h192zm128 368c0 8.822-7.178 16-16 16H48c-8.822 0-16-7.178-16-16V80c0-8.822 7.178-16 16-16h16v104c0 13.255 10.745 24 24 24h208c13.255 0 24-10.745 24-24V64.491a15.888 15.888 0 0 1 7.432 4.195l83.882 83.882A15.895 15.895 0 0 1 416 163.882V432zM224 232c-48.523 0-88 39.477-88 88s39.477 88 88 88 88-39.477 88-88-39.477-88-88-88zm0 144c-30.879 0-56-25.121-56-56s25.121-56 56-56 56 25.121 56 56-25.121 56-56 56z"></path></svg>
						<label for="<?php echo self::$prefix . self::$mod;?>autosave"><?php _e( 'Auto save after how many minutes?' ); ?></label>
						<input id="<?php echo self::$prefix . self::$mod;?>autosave" name="<?php echo self::$prefix . self::$mod;?>autosave" type="number" value="<?php echo get_option(self::$prefix . self::$mod . 'autosave' ); ?>" style="width: 60px; margin-left: 4px;" />
						<small style="margin-left: 4px;"><?php _e( 'Set this value to 0 (zero) to disable autosave' ); ?></small>
					</div>
					<div style="display: flex; align-items: center; margin: 6px 24px;"><input id="<?php echo self::$prefix . self::$mod;?>smartautosave" name="<?php echo self::$prefix . self::$mod;?>smartautosave" type="checkbox" value="1" <?php checked( get_option( self::$prefix . self::$mod . 'smartautosave' ), "1" ); ?> />
					<?php _e( 'Smart autosave' ); ?>
					<small style="margin-left: 4px;"><?php _e( '(if checked, autosave will wait until any keyboard and mouse activity ceases)' ); ?></small></div>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>back_to_wp_additions" name="<?php echo self::$prefix . self::$mod; ?>back_to_wp_additions" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'back_to_wp_additions' ), 'true' ); ?> />
					<?php _e( 'Back to WP Additions' ); ?>
					<?php _e( '<p>Adds Templates and Pages menu items under "Back to WP" menu in the Oxygen editor.</p>' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>components_panel" name="<?php echo self::$prefix . self::$mod; ?>components_panel" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'components_panel' ), 'true' ); ?> />
					<?php _e( 'Components Panel' ); ?>
					<?php _e( '<p>Adds a panel of Oxygen components that appears when hovered along the top edge of Oxygen editor.</p>' ); ?>
					<input id="<?php echo self::$prefix . self::$mod; ?>components_panel_click" style="margin-left: 24px" name="<?php echo self::$prefix . self::$mod; ?>components_panel_click" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'components_panel_click' ), 'true' ); ?> />
					<?php _e( 'Display Components Panel on click instead of hover' ); ?>
				</div>

				<div class="no-bottom-margin">
					<input id="<?php echo self::$prefix . self::$mod; ?>copy_selector" name="<?php echo self::$prefix . self::$mod; ?>copy_selector" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'copy_selector' ), 'true' ); ?> />
					<?php _e( 'Copy Selector' ); ?>
					<?php _e( '<p>Adds a button to copy current selector name to clipboard.</p>' ); ?>
				</div>
				<div class="indented">
					<input id="<?php echo self::$prefix . self::$mod; ?>copy_selector_with_prefix" name="<?php echo self::$prefix . self::$mod; ?>copy_selector_with_prefix" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'copy_selector_with_prefix' ), 'true' ); ?> />
					<?php _e( 'with Prefix (# for ID and . for class)' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>css_tweaks" name="<?php echo self::$prefix . self::$mod; ?>css_tweaks" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'css_tweaks' ), 'true' ); ?> />
					<?php _e( 'CSS Tweaks' ); ?>
					<?php _e( '<p>Adds general CSS fixes for the conditions diaglog on smaller screens, removes the unneeded scrollbars and light gray background for range slider inputs, vertically centers the color picker circle in Firefox etc.</p>' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>currently_editing" name="<?php echo self::$prefix . self::$mod; ?>currently_editing" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'currently_editing' ), 'true' ); ?> />
					<?php _e( 'Currently Editing' ); ?>
					<?php _e( '<p>Adds the name of current entry (Template/Page etc.) that is currently being edited to the left of Structure button in the Oxygen editor.</p>' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>disable_oxygen_composite_elements" name="<?php echo self::$prefix . self::$mod; ?>disable_oxygen_composite_elements" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'disable_oxygen_composite_elements' ), 'true' ); ?> />
					<?php _e( 'Disable Oxygen Composite Elements' ); ?>
					<?php _e( '<p>Prevents Oxygen composite elements from loading in the Oxygen editor.</p>' ); ?>
				</div>
				
				<div class="no-bottom-margin">
					<input id="<?php echo self::$prefix . self::$mod; ?>easy_panels" name="<?php echo self::$prefix . self::$mod; ?>easy_panels" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'easy_panels' ), 'true' ); ?> />
					<?php _e( 'Easy Panels' ); ?>
					<?php _e( '<p>Makes all the panels (Primary and Advanced) a single click away.</p>' ); ?>
				</div>
				<div class="indented">
					<input id="<?php echo self::$prefix . self::$mod; ?>easy_panels_show_tabs" name="<?php echo self::$prefix . self::$mod; ?>easy_panels_show_tabs" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'easy_panels_show_tabs' ), 'true' ); ?> />
					<?php _e( 'Show Oxygen\'s Primary/Advanced tabs as well' ); ?>
				</div>
	
				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>highlight_active_options" name="<?php echo self::$prefix . self::$mod; ?>highlight_active_options" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'highlight_active_options' ), 'true' ); ?> />
					<?php _e( 'Highlight Active Options' ); ?>
					<?php _e( '<p>Improves the contrast of dotted border around active/selected interface elements in the Oxygen editor.</p>' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>open_back_to_wp_menu_links_in_new_tabs" name="<?php echo self::$prefix . self::$mod; ?>open_back_to_wp_menu_links_in_new_tabs" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'open_back_to_wp_menu_links_in_new_tabs' ), 'true' ); ?> />
					<?php _e( 'Open Back to WP Menu Links in New Tabs' ); ?>
					<?php _e( '<p>Makes Admin, Frontend etc. buttons to open in a new tab.</p>' ); ?>
				</div>

				<div>
					<input id="<?php echo self::$prefix . self::$mod; ?>remove_min_height_empty_divs" name="<?php echo self::$prefix . self::$mod; ?>remove_min_height_empty_divs" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'remove_min_height_empty_divs' ), 'true' ); ?> />
					<?php _e( 'Remove min-width and min-height for Empty Divs' ); ?>
					<?php _e( '<p>Removes min-width and min-height of 80px for empty Divs.</p>' ); ?>
				</div>

				<div class="no-bottom-margin">
					<input id="<?php echo self::$prefix . self::$mod; ?>wider_library_flyout_panel" name="<?php echo self::$prefix . self::$mod; ?>wider_library_flyout_panel" type="checkbox" value="true" <?php checked( get_option( self::$prefix . self::$mod . 'wider_library_flyout_panel' ), 'true' ); ?> />
					<?php _e( 'Wider Library Flyout Panel' ); ?>
					<?php _e( '<p>Increases the width of library flyout panel from the default 300px to 600px.</p>' ); ?>
				</div>
			</div>
		</div>

	<?php }

}

Oxy_Toolbox_EditorTweaks::init( self::PREFIX );
