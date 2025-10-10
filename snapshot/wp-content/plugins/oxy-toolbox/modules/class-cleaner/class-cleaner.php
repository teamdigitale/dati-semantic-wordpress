<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Oxy_Toolbox_Class_Cleaner {
	
	static $prefix;
	static $mod = 'class_cleaner_';
	static $title = 'Classes Cleaner';
	static $description = 'Lets you clean unused classes and rename classes after doing a sitewide search for existing references.';
	static $link = 'https://oxyplugins.com/doc/classes-cleaner/';
	
	static function init( $prefix ) {
		
		self::$prefix = $prefix;

		$oxy_toolbox_mod = new Oxy_Toolbox_Mod( $prefix, self::$mod, self::$title, self::$description, self::$link );

		// this will block the rest of the mod to load, if it is not checked.
		if ( 0 === intval( get_option( self::$prefix . self::$mod, 0 ) ) || OxyToolboxLicense::is_activated_license() !== true) {
			return;
		}
		
		add_action( self::$prefix . 'enqueue_scripts', array( __CLASS__, 'mod_scripts' ), 1 );

		add_action( 'wp_ajax_oxy_cleaner_backend', array( __CLASS__, 'oxy_cleaner_backend' ) );
		
	}

	static function mod_scripts( $version ) {

		if ( ! defined( 'SHOW_CT_BUILDER' ) ) {
			return;
		}

		if ( defined( 'OXYGEN_IFRAME' ) ) {
			wp_register_script( self::$prefix . self::$mod . 'script', plugins_url( 'js/script.js', __FILE__ ), array( 'ct-angular-main' ), $version );
			wp_localize_script( self::$prefix . self::$mod . 'script', self::$prefix . self::$mod . 'options', array(
				'plugin_dir_url' => plugin_dir_url( __FILE__ )
			));
			wp_enqueue_script( self::$prefix . self::$mod . 'script' );
		} else {
			wp_enqueue_style( self::$prefix . self::$mod . 'style', plugins_url( 'css/style.css', __FILE__ ), array(), $version );
		}
	}

	static function oxy_cleaner_backend() {

		global $ct_ignore_post_types;
		$postTypes = get_post_types();
		
		$ignore_post_types = $ct_ignore_post_types;

		$ct_template_key = array_search( 'ct_template', $ignore_post_types );

		if ( $ct_template_key !== false ) {
			unset( $ignore_post_types[$ct_template_key] );
		}

		if ( is_array( $ignore_post_types ) && is_array( $postTypes ) ) {
			$postTypes = array_diff( $postTypes, $ignore_post_types );
		}

		$postTypeKeys = array_keys( $postTypes );
		
		$step = isset( $_REQUEST['step'] ) ? intval( $_REQUEST['step'] ) : 1;
		
		$response = array();

		if ( $step <= sizeof( $postTypeKeys ) ) {

			$postType = $postTypeKeys[$step-1];

			$response = self::oxy_cleaner_process( $postType );
			
			if ( isset( $response['index'] ) ) {
				$response['step'] = $step; // if we are still looping through the same post type
			}
			else {
				$response['step'] = $step + 1; // move to next post type
			}

			if ( $response['step'] > sizeof( $postTypeKeys ) ) { // thats the end of it
				$response['step'] = 9999; // just a signal for the client side to stop requesting
			}

		}
		else {
			$response['step'] = 9999; // just a signal for the client side to stop requesting
		}
		
		header( 'Content-Type: application/json' );
		
		echo json_encode( $response );

		die();

	}

	static function oxy_cleaner_process( $type ) {

		$pages = get_posts( array( 'post_type' => array( $type ), 'numberposts' => -1 ) );
		$rename = isset( $_REQUEST['rename'] ) ? true : false;

		if ( $rename ) {
			$response = array( 'classes' => 0 );	
		} else {
			$response = array( 'classes' => array() );
		}

		if ( sizeof( $pages ) > 0 ) {

			// get index from request, if not available set it to zero.
			$index = isset( $_REQUEST['index'] ) ? intval( $_REQUEST['index'] ) : 0;
			$except = isset( $_REQUEST['except']) ? intval( $_REQUEST['except'] ) : false;

			// get $pages[$index], get the shortcodes, do the process.
			$page = $pages[$index];
			
			if ( $page->ID !== $except ) { // skip self page and determine the classes in the frontend
				// get the shortcodes of the page.
				// $json = get_post_meta( $page->ID, "ct_builder_json", true );
				// $shortcodes = get_post_meta( $page->ID, 'ct_builder_shortcodes', true );

				$is_new_format = true;
				// Try new meta key format first, then fall back to old format
				$json = get_post_meta( $page->ID, '_ct_builder_json', true );
				if ( empty( $json ) ) {
					$json = get_post_meta( $page->ID, 'ct_builder_json', true );
					if(! empty($json)) {
						$is_new_format = false;
					}
				}

				$shortcodes = get_post_meta( $page->ID, '_ct_builder_shortcodes', true );
				if ( empty( $shortcodes ) ) {
					$shortcodes = get_post_meta( $page->ID, 'ct_builder_shortcodes', true );
					if(! empty($shortcodes)) {
						$is_new_format = false;
					}
				}

				if ( $shortcodes ) {
					
					$shortcodes = parse_shortcodes( $shortcodes );

					if ( $shortcodes['content'] ) {
						if ( $rename ) {
							$response['classes'] = self::extract_classes( $shortcodes['content'] );
						} else {
							$response['classes'] = array_keys( self::extract_classes( $shortcodes['content'] ) );
						}

					}

					if ( $rename ) {
						// update the shortcodes.
						$shortcodes = parse_components_tree( $shortcodes['content'] );
						if($is_new_format) {
							update_post_meta( $page->ID, "_ct_builder_shortcodes", $shortcodes );
						} else {
							update_post_meta( $page->ID, "ct_builder_shortcodes", $shortcodes );
						}
					}
				}

				// update the json
				$json = json_decode($json, true);
				$wrapper = array($json);

				if($json) {
					if($rename) {
						$response['classes'] = self::extract_classes($wrapper);
					} else {
						$response['classes'] = array_keys(self::extract_classes($wrapper));
					}

					if($rename) {
						// update the json
						$json = json_encode($wrapper[0]);
						if($is_new_format) {
							update_post_meta($page->ID, "_ct_builder_json", $json);
						} else {
							update_post_meta($page->ID, "ct_builder_json", $json);
						}
					}
				}
			}

			if ( $index < sizeof( $pages ) - 1 ) {
				$index++;
				$response['index'] = $index;
			}
		}
		$response['type'] = $type;
		return $response;
	}

	static function extract_classes( &$children ) {

		$rename = isset( $_REQUEST['rename'] ) ? array_map( 'sanitize_text_field', $_REQUEST['rename'] ): false;
		if ( $rename !== false ) {
			$classes = 0;
		}
		else {
			$classes = array();
		}

		foreach( $children as $key => $child ) {

			if ( isset( $child['options']['classes'] ) ) {
				if ( $rename ) {
					$index = array_search( $rename['nameFrom'], $child['options']['classes'] );
					if ( $index !== false ) {
						$children[$key]['options']['classes'][$index] = $rename['nameTo'];
						$classes++;
					}

					if ( $child['options']['activeselector'] === $rename['nameFrom'] ) {
						$children[$key]['options']['activeselector'] = $rename['nameTo'];
					}
				}
				else {
					foreach( $child['options']['classes'] as $item ) {
						if ( is_string( $item ) ) {
							$classes[$item] = false;
						}
					}
				}
			}

			if ( isset( $child['children'] ) ) {
				if ( $rename ) {
					$classes += self::extract_classes( $children[$key]['children'] );
				} else {
					$classes = array_merge( $classes, self::extract_classes( $child['children'] ) );
				}
			}
		}

		return $classes;
	}

}

Oxy_Toolbox_Class_Cleaner::init( self::PREFIX );