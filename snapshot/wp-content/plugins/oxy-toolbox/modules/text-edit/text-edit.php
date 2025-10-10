<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Oxy_Toolbox_Textedit {

	static $prefix;
	static $mod = 'text_edit_';
	static $title = 'Text Edit';
	static $description = 'Adds a text area for viewing and editing text of Text, Rich Text and Heading components even if they are hidden.';
	static $link = 'https://oxyplugins.com/doc/text-edit/';
	
	static function init( $prefix ) {
		self::$prefix = $prefix;
		$oxy_toolbox_mod = new Oxy_Toolbox_Mod( $prefix, self::$mod, self::$title, self::$description, self::$link );

		// this will block the rest of the mod to load, if it is not checked.
		if ( 0 === intval( get_option( self::$prefix . self::$mod, 0 ) ) || OxyToolboxLicense::is_activated_license() !== true) {
			return;
		}

		add_action( self::$prefix . 'enqueue_scripts', array( __CLASS__, 'mod_scripts' ), 1 );
		
		add_action( 'init', array( __CLASS__, 'initialize') );
	}

	static function initialize() {
		global $oxygen_vsb_components;

		foreach( $oxygen_vsb_components ?? [] as $ckey => $component ) {
			if( ! empty( $component->options['params'] ) && is_array( $component->options['params'] ) ) {
				foreach( $component->options['params'] as $key => $param ) {
					
					if( ! empty( $param['param_name'] ) && 'ct_content' === $param['param_name'] ) {
						$oxygen_vsb_components[ $ckey ]->options['params'][$key]['hidden'] = true;
						break;
					}
				}
			}
		}
		
	}

	static function mod_scripts( $version ) {

		if ( ! defined( 'SHOW_CT_BUILDER' ) ) {
			return;
		}

		if ( defined( 'OXYGEN_IFRAME' ) ) {
			wp_register_script( self::$prefix . self::$mod . 'script', plugins_url( 'js/script.js', __FILE__ ), array( 'ct-angular-main' ), $version, true );
			
			wp_enqueue_script( self::$prefix . self::$mod . 'script' );
		}
	}

}

Oxy_Toolbox_Textedit::init( self::PREFIX );