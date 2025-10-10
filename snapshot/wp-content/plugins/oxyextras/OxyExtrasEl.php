<?php

if (class_exists('OxygenExtraElements')){
	return;
}

class OxygenExtraElements extends OxyEl {
    
    function init() {
		$this->El->useAJAXControls();
	}
    
	function class_names() {
		return '';
	}

    function dequeue_scripts_styles() {

		if ( ! defined( 'OXY_ELEMENTS_API_AJAX' ) ) {
			return;
		}

		global $wp_scripts, $wp_styles;

		if ( isset( $wp_styles->queue ) ) {
			$wp_styles->queue  = [];
		}

		if ( isset( $wp_scripts->queue ) ) {
			$wp_scripts->queue  = [];
		}

	}
    
    function button_place() {
        
            $extras_button_place = $this->extras_button_place();

            if ($extras_button_place) {
                return "extras::".$extras_button_place;
            }
        
    }
    
    function button_priority() {
        return '';
    }
    
}