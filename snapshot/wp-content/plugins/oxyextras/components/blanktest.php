<?php
class ExtraBlank extends OxygenExtraElements {
        
    function name() {
        return 'Blank nestable component';
    }
   
    function extras_button_place() {
        return "interactive";
    }
    
    function tag() {
        return array('default' => 'div');
    }
    
    
    
    function init() {
        
        $this->enableNesting();
    
    }

    function render($options, $defaults, $content) {
        
        
        if ($content) {
            echo do_shortcode( $content );
        }
        
    
    }
    
    
    function class_names() {
        return array();
    }

    function controls() {

    }

}

new ExtraBlank();