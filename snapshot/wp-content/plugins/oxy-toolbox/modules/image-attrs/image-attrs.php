<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Oxy_Toolbox_Image_Attrs {
	
	static $prefix;
	static $mod = 'image_attrs_';
	static $title = 'Image Width and Height Size Attributes';
	static $description = 'Adds width and height attributes with values for images.';
	static $link = 'https://oxyplugins.com/doc/image-size-attributes/';
	
	static function init( $prefix ) {
		
		self::$prefix = $prefix;

		$oxy_toolbox_mod = new Oxy_Toolbox_Mod( $prefix, self::$mod, self::$title, self::$description, self::$link );

		// this will block the rest of the mod to load, if it is not checked.
		if ( 0 === intval( get_option( self::$prefix . self::$mod, 0 ) ) || OxyToolboxLicense::is_activated_license() !== true) {
			return;
		}
		
		add_action( 'init', array(__CLASS__, 'image_override' ) );
		
	}

	static function image_override() {
		global $oxygen_vsb_components;
		
		if ( isset( $oxygen_vsb_components['image'] ) && is_object( $oxygen_vsb_components['image'] ) && isset( $oxygen_vsb_components['image']->options ) ) {
			remove_shortcode( $oxygen_vsb_components['image']->options['tag'] );
			add_shortcode( $oxygen_vsb_components['image']->options['tag'], array( __CLASS__, 'image_attrs' ) );
		}
	}

	static function image_attrs($atts, $content, $name) {
		
		global $oxygen_vsb_components;

		$imgcode = $oxygen_vsb_components['image']->add_shortcode($atts, $content, $name);
		
		$options = $oxygen_vsb_components['image']->set_options( $atts );
		
		$width = false;
		$height = false;

		if(intval($options['image_type']) === 2) {

			$width = (isset($options['attachment_width']) && !empty($options['attachment_width'])) ? $options['attachment_width'] : false;
			
			$height = (isset($options['attachment_height']) && !empty($options['attachment_height'])) ? $options['attachment_height'] : false;
		}
		
		if($width === false && $height === false) {
			
			$width = (isset($options['width']) && !empty($options['width']) && $options['width_unit'] === 'px') ? $options['width'] : false;

			$height = (isset($options['height']) && !empty($options['height']) && $options['height_unit'] === 'px') ? $options['height'] : false;
		}


		if($width !== false) {
			$imgcode = str_replace('<img', '<img width="'.esc_attr($width).'"', $imgcode);
		}

		if($height !== false) {
			$imgcode = str_replace('<img', '<img height="'.esc_attr($height).'"', $imgcode);
		}

		

		return $imgcode;
	}
}

Oxy_Toolbox_Image_Attrs::init( self::PREFIX );