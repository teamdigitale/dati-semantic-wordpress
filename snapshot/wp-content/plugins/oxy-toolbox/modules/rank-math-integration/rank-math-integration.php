<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Oxy_Toolbox_RankMathIntegration {

	static $prefix;
	static $mod = 'rank_math_integration_';
	static $title = 'Rank Math Integration';
	static $description = 'Enables Rank Math SEO plugin to parse text inside the Oxygen editor for Pages and Posts.';
	static $link = 'https://oxyplugins.com/doc/rank-math-integration/';
	
	static function init( $prefix ) {
		self::$prefix = $prefix;
		$oxy_toolbox_mod = new Oxy_Toolbox_Mod( $prefix, self::$mod, self::$title, self::$description, self::$link );

		// this will block the rest of the mod to load, if it is not checked.
		if ( 0 === intval( get_option( self::$prefix . self::$mod, 0 ) ) || OxyToolboxLicense::is_activated_license() !== true) {
			return;
		}

		// check if Rank Math is active.
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if ( ! is_plugin_active( 'seo-by-rank-math/rank-math.php' ) ) {
			return;
		}

		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'mod_admin_scripts' ) );

		add_filter( 'rank_math/sitemap/urlimages', array( __CLASS__, 'add_rm_images'), 10, 2 );

		add_filter( 'template_include', array( __CLASS__, 'stripped_content'), 9 );
	}

	static function extract_images($items, $postId) {
		$images = array();
		if(is_array($items)) {
			foreach($items as $index => $item) {
				if($index === 'src' || $index === 'attachment_url') {
					// do the thing
					if(strpos($item, '[oxygen ') === 0) {
						
						$query = new WP_Query(array('p' => $postId, 'post_type'=> 'any'));

						if($query->have_posts()) {
			                $query->the_post();
			                global $oxy_vsb_use_query;
			                $oxy_vsb_use_query = $query;
			                $item = do_shortcode($item);
			            }

			            $query->reset_postdata();
					}
					$image = array('src' => $item);
					
					if(isset($items['alt'])) {
						$image['alt'] = $items['alt'];

						// do the thing
						if(strpos($image['alt'], '[oxygen ') === 0) {
							
							$query = new WP_Query(array('p' => $postId, 'post_type'=> 'any'));

							if($query->have_posts()) {
				                $query->the_post();
				                global $oxy_vsb_use_query;
				                $oxy_vsb_use_query = $query;
								
				                $image['alt'] = do_shortcode($image['alt']);
				            }

				            $query->reset_postdata();
						}
					}
					$images[] = $image;
				} elseif(is_array($item)) {
					$images = array_merge($images, self::extract_images($item, $postId));
				}
			}
		}
		return $images;
	}

	static function add_rm_images($images, $postId) {
		
		$shortcodes = self::get_template_shortcodes($postId);

		// collect the images
		$shortcodes = parse_shortcodes( $shortcodes, false);

		$images = array_merge($images, self::extract_images($shortcodes['content'], $postId));

		// inner content
		$shortcodes = get_post_meta( $postId, 'ct_builder_shortcodes', true );

		$shortcodes = parse_shortcodes( $shortcodes, false);

		$images = array_merge($images, self::extract_images($shortcodes['content'], $postId));

		return $images;

	}

	static function get_template_shortcodes($postId) {
		$shortcodes = false;

		$generic_view = null;
		$ct_other_template = get_post_meta( $postId, 'ct_other_template', true );

		// generic view
		if ( get_option( 'page_for_posts' ) == $postId || get_option( 'page_on_front' ) == $postId ) {
			$generic_view = ct_get_archives_template( $postId ); // true, for exclude templates of type inner_content

			if(!$generic_view) {  // if not template is set to apply to front page or blog posts page, then use the generic page template, as these are pages
				$generic_view = ct_get_posts_template( $postId );
			}
		}
		else {
			$generic_view = ct_get_posts_template( $postId ); // true, exclude templates of type inner_content
		}

		$template = $ct_other_template === "-1" ? false : ($ct_other_template > 0 ? $ct_other_template : ($generic_view->ID ? $generic_view->ID : false));
		
		if($template !== false) {
			$tree = array();
	
			// update global template var
			global $ct_template_id;
			$ct_template_id = $template;

			// the following also takes care of the shortcode signature validation
			$combinedCodes = oxygen_get_combined_shortcodes($template);

			$tree['children'] = $combinedCodes['content'];
		
			$shortcodes_json = json_encode($tree);
		
			$shortcodes = components_json_to_shortcodes($shortcodes_json);
		}

		return $shortcodes;
	}

	static function stripped_content() {
		if(!isset($_GET['rmstrippedcontent'])) {
			return;
		}

		$template_path = __DIR__.'/includes/stripped-content.php';

		if(file_exists($template_path)){
	        include($template_path);
	        exit;
	    }
	}

	static function mod_admin_scripts() {

		global $pagenow;
		global $post;
		
		// save global $post to restore later.
		$saved_post = $post;
	
		// exclude templates.
		if ( is_object( $post ) && 'ct_template' === $post->post_type ) {
			return;
		}
		if ( 'post.php' === $pagenow && ! is_null( $post ) ) {

			wp_register_script( self::$prefix . self::$mod . 'script', plugins_url( 'js/script.js', __FILE__ ), [ 'wp-hooks', 'rank-math-analyzer' ], '', true );

			wp_localize_script( self::$prefix . self::$mod . 'script', self::$prefix . self::$mod . 'rm_data', [
				'permalink' => add_query_arg('rmstrippedcontent', '1', get_permalink($post->ID))
			]);

			//ct_template_output
			wp_enqueue_script( self::$prefix . self::$mod . 'script' );
		}
	
		// restore original global post.
		$post = $saved_post;
	}
	
}

Oxy_Toolbox_RankMathIntegration::init( self::PREFIX );