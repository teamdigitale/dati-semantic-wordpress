<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Oxy_Toolbox_Repeatermod {

	static $prefix;
	static $mod = 'repeater_mod_';
	static $title = 'Repeater Mod';
	static $description = 'Adding some more options';
	static $link = 'https://oxyplugins.com/doc/repeater-mod/';
	
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
		remove_shortcode($oxygen_vsb_components['repeater']->options['tag']);
		add_shortcode($oxygen_vsb_components['repeater']->options['tag'], array( __CLASS__, 'add_shortcode' ));
		
	}

	static function add_shortcode( $atts, $content, $name ) {
		global $oxygen_vsb_components;
		$output = $oxygen_vsb_components['repeater']->add_shortcode($atts, $content, $name);

		$options = json_decode($atts['ct_options'], true);
		$replace_tag = !empty($options['original']['tag']) && $options['original']['tag'] !== 'div';
		$supress_ids = !empty($options['original']['tb-ids']) && $options['original']['tb-ids'] === 'true';
	    
       	
		if( $replace_tag || $supress_ids ) {
			// format the output and return it
			$doc = new DOMDocument();
			
			ob_start();
			$doc->loadHtml($output);
			ob_end_clean();

			if($replace_tag) {
				$node = $doc->lastChild->lastChild->firstChild;
				$ul = $doc->createElement($options['original']['tag']);
				// append each of the $node's children to the $ul
				while ($node->firstChild) {
					$ul->appendChild($node->firstChild);
				}
				// copy the attributes from the $node to the $ul
				foreach ($node->attributes as $attrName => $attrNode) {
					$ul->setAttribute($attrName, $attrNode->value);
				}
				// replace the $node with the $ul
				$node->parentNode->replaceChild($ul, $node);
			}

			if($supress_ids) {
				$node = $doc->lastChild->lastChild->firstChild;
				// foreach each of the $node's children except the first one
				$count = 0;
				foreach ($node->childNodes as $child) {
					$count++;
					if($count === 1) {
						continue;
					}

					// if the child is an element
					if ($child->nodeType == 1) {
						// remove the id attribute
						self::remove_ids($child);
					}
					
				}
			}

			// remove doctype.
			if ( $doc->doctype ) {
				$doc->removeChild( $doc->doctype ); 
			}
			// remove html, body tags.
        	$doc->replaceChild($doc->firstChild->firstChild->firstChild, $doc->firstChild);
			
			return $doc->saveHTML();
		}
       	return $output;
	}

	function remove_ids(&$node) {

        if(method_exists($node, 'removeAttribute')) {
            $node->removeAttribute('id');
        }

        foreach($node->childNodes as $child) {
			if ($child->nodeType == 1) {
           		self::remove_ids($child); 
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

Oxy_Toolbox_Repeatermod::init( self::PREFIX );