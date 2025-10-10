<?php

class ExtraContentSwitcherToggle extends OxygenExtraElements {
        
    var $js_added = false;
    var $css_added = false;
    
	function name() {
        return 'Toggle Switch';
    }
    
    function icon() {
        return plugin_dir_url(__FILE__) . 'assets/icons/'.basename(__FILE__, '.php').'.svg';
    }
    
    function extras_button_place() {
        return "interactive";
    }
    
    function enablePresets() {
        return true;
    }
    
    function enableFullPresets() {
        return true;
    }
    
    function init() {
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
        
        //get options
        $label_1  = $dynamic( $options['label_1'] );
        $label_2  = $dynamic( $options['label_2'] );
        
        $screen_reader_text = isset( $options['screen_reader_text'] ) ? esc_attr($options['screen_reader_text']) : 'Switch pricing';
        //$label_position = isset( $options['label_position'] ) ? esc_attr($options['label_position']) : '';
        $default_state = esc_attr($options['default_state']) === 'checked' ? 'checked' : '';
        
        $switch_function = isset( $options['switch_function'] ) ? esc_attr($options['switch_function']) : '';
        
        $class_toggle_selector = isset( $options['class_toggle_selector'] ) ? esc_attr($options['class_toggle_selector']) : 'body';
        $class_toggle_class = isset( $options['class_toggle_class'] ) ? esc_attr($options['class_toggle_class']) : '';
        
        $content_switcher = isset( $options['content_switcher'] ) ? esc_attr($options['content_switcher']) : '';
        $content_switcher_selector = isset( $options['content_switcher_selector'] ) ? esc_attr($options['content_switcher_selector']) : '';
        
        
        
        $output = '<div class="oxy-toggle-switch_inner" ';

        
        if (( 'content_switcher' === $switch_function ) && ( 'selector' === $content_switcher ) ) {

            $output .= 'data-selector="'. $content_switcher_selector .'" ';

        }
        
        if (( 'content_switcher' === $switch_function ) && ( 'section' === $content_switcher ) ) {

            $output .= 'data-section ';

        }



        // The class to toggle

        if ( 'class_toggle' === $switch_function ) {

            $output .= 'data-class="'. $class_toggle_class .'" ';
            $output .= 'data-selector="'. $class_toggle_selector .'" ';

        }
        
         if ( 'content_switcher' === $switch_function ) {
             
             $output .= 'data-class="oxy-content-switcher_toggled" ';
             
         }

        


        $output .= '>';

    //if ( 'side' === $label_position ) {

        $output .= '<span class="oxy-toggle-switch_label">'. $label_1 .'</span>';
        $output .= '<label class="oxy-toggle-switch_switch"><div class="screen-reader-text">'. $screen_reader_text .'</div><input autocomplete="off" type="checkbox" '. $default_state .'><span class="oxy-toggle-switch_slider"><span class="oxy-toggle-switch_control"></span></span></label>';
        $output .= '<span class="oxy-toggle-switch_label">'. $label_2 .'</span>';

    //} else {

        /*

        $output .= '<label class="oxy-toggle-switch_switch"><input type="checkbox" '.$default_state.'><span class="oxy-toggle-switch_slider">'.$label_1.'<span class="oxy-toggle-switch_control"></span>'.$label_2.'</span></label>';
        $output .= $label_2;

        /* } */
        
        $output .= '</div>'; // close inner div
        
        echo $output;

        $this->dequeue_scripts_styles();
        
        // add JavaScript code only once and if shortcode presented
        if ($this->js_added !== true) {
                add_action( 'wp_footer', array( $this, 'output_js' ) );
            $this->js_added = true;
        }

    }

    function class_names() {
        return array();
    }

    function controls() {
        
        $slider_selector = '.oxy-toggle-switch_slider';
        $slider_checked_selector = 'input:checked + .oxy-toggle-switch_slider';
        $control_selector = '.oxy-toggle-switch_control';
        $control_checked_selector = 'input:checked + .oxy-toggle-switch_slider .oxy-toggle-switch_control';
        $switch_selector = '.oxy-toggle-switch_switch';
        
        $transition_selector = '.oxy-toggle-switch_control, .oxy-toggle-switch_slider';
        
        
        $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Default State',
                'slug' => 'default_state'
            )
            
        )->setValue(
            array( 
                "unchecked" => "Unchecked" ,
                "checked" => "Checked", 
            )
        )
         ->setDefaultValue('unchecked')->rebuildElementOnChange();
        
        
        /**
         * Switch Function
         */
        $this->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Switch function'),
                'slug' => 'switch_function'
            )
            
        )->setValue(
            array( 
                "class_toggle" => "Toggle Class", 
                "content_switcher" => "Toggle Content Switcher",
                "manual" => "None"
            )
        )
         ->setDefaultValue('content_switcher');
        
        
        /**
         * Content Switcher
         */
        $this->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Which content switcher?'),
                'slug' => 'content_switcher',
                "condition" => 'switch_function=content_switcher',
            )
            
        )->setValue(
            array( 
                //"next" => "Next element", 
                "section" => "All switchers inside this section", 
                "selector" => "Choose selector"
            )
        )
         ->setDefaultValue('section');
        
        
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Content switcher selector'),
                "slug" => 'content_switcher_selector',
                "default" => '.oxy-content-switcher',
                "condition" => 'switch_function=content_switcher&&content_switcher=selector',
                "base64" => true,
            )
        );
        
        
        /**
         * Selector to toggle class
         */
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Choose a selector..'),
                "slug" => 'class_toggle_selector',
                "default" => 'body',
                "condition" => 'switch_function=class_toggle',
                "base64" => true,
            )
        );
        
        
        /**
         * Class Switch
         */
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Class to toggle'),
                "slug" => 'class_toggle_class',
                "default" => 'dark-mode',
                "condition" => 'switch_function=class_toggle',
            )
        );
        
        
        
        /**
         * Switch
         */
        $switch_section = $this->addControlSection("switch_section", __("Switch"), "assets/icon.png", $this);
        
        $switch_section->addStyleControls(
             array( 
                array(
                    "name" => 'Slider border radius',
                    "selector" => $slider_selector,
                    "property" => 'border-radius',
                ),
                 
            )
        );
        
        $switch_section->boxShadowSection('Control Shadow', $control_selector,$this);
        
        $switch_section->addStyleControl(
            array(
                "name" => 'Slider height',
                "property" => 'height',
                "control_type" => 'slider-measurebox',
                "selector" => $slider_selector,
            )
        )
        ->setUnits('px','px')
        ->setRange('0','60','1');
        
        $switch_section->addStyleControl(
            array(
                "name" => 'Slider width',
                "property" => '--slider-width',
                "control_type" => 'slider-measurebox',
                "default" => '60',
                "selector" => $switch_selector,
            )
        )
        ->setUnits('px','px')
        ->setRange('0','200','1');
        
        
        $switch_section->addStyleControl(
            array(
                 "name" => 'Control border radius',
                    "selector" => $control_selector,
                    "property" => 'border-radius',
            )
        );
        
        
        
        $switch_section->addStyleControl(
            array(
                "name" => 'Control border width',
                "selector" => $control_selector,
                "property" => 'border-width',
                "control_type" => 'slider-measurebox',
                "default" => '0',
            )
        )
        ->setUnits('px','px')
        ->setRange('0','20','1');
        
        $switch_section->addStyleControl(
            array(
                "name" => 'Control border width (checked)',
                "selector" => $control_checked_selector,
                "property" => 'border-width',
                "control_type" => 'slider-measurebox',
                "default" => '2',
            )
        )
        ->setUnits('px','px')
        ->setRange('0','20','1');
        
        
        $switch_section->addStyleControl(
            array(
                "name" => 'Control side margin',
                "property" => '--slider-padding',
                "control_type" => 'slider-measurebox',
                "default" => '4',
                "selector" => $slider_selector,
            )
        )
        ->setUnits('px','px')
        ->setRange('-20','20','1');
        
       
        
        $switch_section->addStyleControl(
            array(
                "name" => 'Control size',
                "property" => '--control-size',
                "control_type" => 'slider-measurebox',
                "default" => '38',
                "selector" => $control_selector,
            )
        )
        ->setUnits('px','px')
        ->setRange('0','100','1');
        
        
        
        $this->addStyleControl(
            array(
                "name" => 'Transition duration',
                "property" => 'transition-duration',
                "control_type" => 'slider-measurebox',
                "default" => '.4',
                "selector" => $transition_selector,
                "condition" => 'switch_function=content_switcher',
            )
        )
        ->setUnits('s','s')
        ->setRange('0','1','.01');
        
        
        
        
        /**
         * Switch / Spacing
         */
        $spacing_section = $switch_section->addControlSection("spacing_section", __("Spacing"), "assets/icon.png", $this);
        
        
        
        $spacing_section->addPreset(
            "margin",
            "margin_padding",
            __("Margin"),
            '.oxy-toggle-switch_switch'
        )->whiteList();
        
        
        
        /**
         * Colors
         */
        $colors_section = $this->addControlSection("colors_section", __("Colors"), "assets/icon.png", $this);
        
        
        $colors_section->addStyleControls(
             array( 
                array(
                    "name" => 'Slider Background',
                    "selector" => $slider_selector,
                    "property" => 'background-color',
                ),
                array(
                    "name" => 'Checked Slider Background',
                    "selector" => $slider_checked_selector,
                    "property" => 'background-color',
                ),
                 array(
                    "name" => 'Control Background',
                    "selector" => $control_selector,
                    "property" => 'background-color',
                ),
                 array(
                    "name" => 'Checked Control Background',
                    "selector" => $control_checked_selector,
                    "property" => 'background-color',
                ),
                 array(
                    "name" => 'Control Border Color',
                    "selector" => $control_selector,
                    "property" => 'border-color',
                )
                 
            )
        );
        
        
         /**
         * Labels
         */
        $label_section = $this->addControlSection("label_section", __("Labels"), "assets/icon.png", $this);
        $label_selector = '.oxy-toggle-switch_label';
        
        
        
        $label_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('First label'),
                "slug" => 'label_1',
                "default" => 'Monthly',
                "base64" => true
            )
        )->rebuildElementOnChange()
         ->setParam('dynamicdatacode', '<div optionname="\'oxy-toggle-switch_label_1\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');    
        
        $label_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Second label'),
                "slug" => 'label_2',
                "default" => 'Yearly',
                "base64" => true
            )
        )->rebuildElementOnChange()
         ->setParam('dynamicdatacode', '<div optionname="\'oxy-toggle-switch_label_2\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');
        
        $label_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Screen reader text (not visible)'),
                "slug" => 'screen_reader_text',
                "default" => __('Switch pricing'),
                "base64" => true
            )
        );
        
        $label_section->addPreset(
            "padding",
            "label_padding",
            __("Padding"),
            $label_selector
        )->whiteList();
        
        
        
        $label_section->typographySection('Typography', $label_selector,$this);   
        
        

    }
    
    
    function customCSS($options, $selector) {
        
        $css = '';
        
        if( ! $this->css_added ) {
        
            $css .= ".oxy-toggle-switch {
                        display: flex;
                        align-items: center;
                    }

                    .oxy-toggle-switch_inner {
                        display: flex;
                        align-items: center;
                    }

                    .oxy-toggle-switch_switch {
                      position: relative;
                      display: inline-block;
                      width: var(--slider-width);
                      height: 34px;
                      --slider-width: 60px;
                    }

                    .oxy-toggle-switch_switch input { 
                      opacity: 0;
                      width: 0;
                      height: 0;
                    }

                    .oxy-toggle-switch_slider {
                      position: absolute;
                      cursor: pointer;
                      left: 0;
                      right: 0;
                      background-color: #ccc;
                      -webkit-transition: .4s;
                      transition: .4s;
                      top: 50%;
                      transform: translateY(-50%);
                      -webkit-transform: translateY(-50%);
                      height: 30px;
                      --slider-padding: 4px;
                      padding-left: var(--slider-padding);
                      padding-right: var(--slider-padding);
                      will-change: background-color;
                    }

                    .oxy-toggle-switch_control {
                      border-style: solid;
                      border-width: 0;
                      position: absolute;
                      content: '';
                      height: var(--control-size);
                      width: var(--control-size);
                      left: var(--slider-padding);
                      bottom: 4px;
                      background-color: white;
                      -webkit-transition-duration: .4s;
                      transition-duration: .4s;
                       -webkit-transition-property: all;
                      transition-property: all;
                      top: 50%;
                      transform: translateY(-50%);
                      -webkit-transform: translateY(-50%);
                      box-shadow: 0px 4px 20px rgba(0,0,0,0.33);
                      --control-size: 26px;
                      --transform-size: calc(var(--slider-width) - var(--control-size) - var(--slider-padding) - var(--slider-padding) );
                      will-change: transform;

                    }

                    .oxy-toggle-switch input:checked + .slider {
                      background-color: #2196F3;
                    }

                    .oxy-toggle-switch input:focus + .oxy-toggle-switch_slider {
                      box-shadow: 0 0 1px #2196F3;
                    }

                    .oxy-toggle-switch input:checked + .oxy-toggle-switch_slider .oxy-toggle-switch_control {
                      -webkit-transform: translate(var(--transform-size), -50%);
                      -ms-transform: translate(var(--transform-size), -50%);
                      transform: translate(var(--transform-size), -50%);
                    }

                    /* Text meant only for screen readers. */
                    .screen-reader-text {
                      border: 0;
                      clip: rect(1px, 1px, 1px, 1px);
                      clip-path: inset(50%);
                      height: 1px;
                      margin: -1px;
                      overflow: hidden;
                      padding: 0;
                      position: absolute;
                      width: 1px;
                      word-wrap: normal !important;
                    }";
            
            $this->css_added = true;
            
        }
        
        return $css;
        
    }
    
    
    
    function afterInit() {
        $this->removeApplyParamsButton();
    }
    
    
    function output_js() { ?>
            <script type="text/javascript">
            jQuery(document).ready(oxygen_init_switch);
            function oxygen_init_switch($) {
                
                 $('.oxy-toggle-switch').each(function(){
               
                    var toggleSwitch = $(this),
                        toggleCheckbox = toggleSwitch.find('checkbox'),
                        className = $(this).children('.oxy-toggle-switch_inner').data('class'),
                        maybeSection = $(this).children('.oxy-toggle-switch_inner').data('section');
                        if (typeof maybeSection !== typeof undefined && maybeSection !== false) {
                            var classSelector = $(this).closest('.ct-section').find('.oxy-content-switcher');    
                        } else {
                            var classSelector = $($(this).children('.oxy-toggle-switch_inner').data('selector'));
                        }
                        
                     // Trigger Clicked
                      toggleSwitch.change(function() {
                         $(this).toggleClass('oxy-toggle-switch_toggled')   
                         classSelector.toggleClass(className);
                        } );
                     
                });
            }
        </script>
    <?php }
    
    
    function allowedEmptyOptions($options) {

        $options_to_add = array(
             "oxy-toggle-switch_label_1",
             "oxy-toggle-switch_label_2",
             "oxy-toggle-switch_screen_reader_text",
        );

        $options = array_merge($options, $options_to_add);

        return $options;
    }
    

}

add_filter("oxy_base64_encode_options", 
    function($items) { 
        $items=array_merge($items, array('oxy-toggle-switch_label_1', 'oxy-toggle-switch_label_2')); 
        return $items;
    }
);

new ExtraContentSwitcherToggle();