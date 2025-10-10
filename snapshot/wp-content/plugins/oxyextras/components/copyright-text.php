<?php

class ExtraCopyrightText extends OxygenExtraElements {
        

	function name() {
        return 'Copyright Year';
    }
    
    function icon() {
        return plugin_dir_url(__FILE__) . 'assets/icons/'.basename(__FILE__, '.php').'.svg';
    }
    
    function extras_button_place() {
        return "dynamic";
    }
    
    function tag() {
        return array('default' => 'span');
    }
    
    function init() {
        
        // Allow textfields to be empty and not be replaced by defaults
        add_filter("oxy_allowed_empty_options_list", array( $this, "allowedEmptyOptions") );
    
    }

    function render($options, $defaults, $content) {
        
         $dynamic = function ($textfield) {
                $field = isset( $textfield ) ? $textfield : '';
                if( strstr( $field, '[oxygen') ) {                
                    $field = ct_sign_oxy_dynamic_shortcode(array($field));
                    $field_out =  esc_attr(do_shortcode($field));
                } else {
                    $field_out = esc_attr($textfield);
                }
                return $field_out;
            };
        
        
        // get options 
        $copyright  = $dynamic($options['copyright']);
        $text_before = $dynamic($options['text_before']);
        $text_after = $dynamic($options['text_after']);
        $text_first = $dynamic($options['text_first']);
        
        
        $output = $text_before .' '. $copyright . ' ';

        if ( '' !== $text_first && date( 'Y' ) !== $text_first ) {
            $output .= $text_first . ' &#x02013; ';
        }

        $output .= date( 'Y' ) .' '. $text_after;
        
        echo $output;
        
        $this->dequeue_scripts_styles();
        
    }

    function class_names() {
        return array();
    }

    function controls() {
        
       $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('First Year (YYYY)'),
                "slug" => 'text_first',
                "default" => '',
            )
        )->rebuildElementOnChange()->setParam('dynamicdatacode', '<div optionname="\'oxy-copyright-year_text_first\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Text Before'),
                "slug" => 'text_before',
                "default" => 'Copyright',
                "base64" => true
            )
        )->rebuildElementOnChange()->setParam('dynamicdatacode', '<div optionname="\'oxy-copyright-year_text_before\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Text After'),
                "slug" => 'text_after',
                "default" => 'All Rights Reserved',
                "base64" => true
            )
        )->rebuildElementOnChange()->setParam('dynamicdatacode', '<div optionname="\'oxy-copyright-year_text_after\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Copyright Symbol'),
                "slug" => 'copyright',
                "default" => '©',
                "base64" => true
            )
        )->rebuildElementOnChange()->setParam('dynamicdatacode', '<div optionname="\'oxy-copyright-year_copyright\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');
        
        $this->typographySection('Typography', '',$this);

    }
    
    function allowedEmptyOptions($options) {

        $options_to_add = array(
            "oxy-copyright-year_text_before",
            "oxy-copyright-year_text_after",
            "oxy-copyright-year_copyright",
        );

        $options = array_merge($options, $options_to_add);

        return $options;
    } 
    
    function afterInit() {
        $this->removeApplyParamsButton();
    }

}

add_filter("oxy_base64_encode_options", 
    function($items) { 
        $items=array_merge($items, array('oxy-copyright-year_text_after', 'oxy-copyright-year_text_before','oxy-copyright-year_copyright','oxy-copyright-year_text_first')); 
        return $items;
    }
);

new ExtraCopyrightText();