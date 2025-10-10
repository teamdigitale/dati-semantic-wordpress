<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Oxy_Toolbox_AllTemplatesButton {

	static $prefix;
	static $mod = 'all_templates_button_';
	static $title = 'All Templates Button';
	static $description = 'Adds a link to go back to Templates list on Template edit pages in the WP Admin.';
	static $link = 'https://oxyplugins.com/doc/all-templates-button/';

	static function init( $prefix ) {
		self::$prefix = $prefix;
		$oxy_toolbox_mod = new Oxy_Toolbox_Mod( $prefix, self::$mod, self::$title, self::$description, self::$link );

		// this will block the rest of the mod to load, if it is not checked.
		if ( 0 === intval( get_option( self::$prefix . self::$mod, 0 ) ) || OxyToolboxLicense::is_activated_license() !== true) {
			return;
		}

		add_action( 'edit_form_after_title', array( __CLASS__, 'add_all_templates_btn' ), 7 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'style_all_templates_btn' ), 11 );
	}

	/**
	 * Adds "< All Templates" button on the Oxygen template edit screens.
	 *
	 * @param WP_Post $post The post object.
	 */
	static function add_all_templates_btn( $post ) {

		if ( 'ct_template' === $post->post_type ) {
			printf( '<a href="%s" class="all-templates-btn">< All Templates</a>',
				admin_url( 'edit.php?post_type=ct_template' )
			);
		}
	}
	
	/**
	 * Styles "< All Templates" button on single template pages in the backend.
	 *
	 * @param string $hook the $hook_suffix for the current admin page.
	 */
	static function style_all_templates_btn( $hook ) {

		if ( 'post.php' !== $hook ) {
			return;
		}
	
		$css = '
			.all-templates-btn {
				padding: 4px 8px;
				border: 1px solid #ccc;
				border-radius: 2px;
				font-weight: 600;
				font-size: 13px;
				color: #0073aa;
				background: #f7f7f7;
				margin-bottom: 20px;
				text-decoration: none;
				display: inline-block;
			}
	
			.all-templates-btn:hover {
				border-color: #008ec2;
				background: #00a0d2;
				color: #fff;
			}';
	
		wp_add_inline_style( 'ct-admin-style', $css );
	}
}

Oxy_Toolbox_AllTemplatesButton::init( self::PREFIX );