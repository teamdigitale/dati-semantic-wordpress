<?php

class ExtraBacktoTop extends OxygenExtraElements {
        
    var $js_added = false;

	function name() {
        return 'Back to Top';
    }
    
    function icon() {
        return plugin_dir_url(__FILE__) . 'assets/icons/'.basename(__FILE__, '.php').'.svg';
    }
    
    function extras_button_place() {
        return "interactive";
    }
    
    function tag() {
        return array('default' => 'div');
    }
    
    function enablePresets() {
        return true;
    }
    
    function enableFullPresets() {
        return true;
    }
    
    function init() {
        
        $this->enableNesting();
        
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
        
        
         // Get Options
        $icon  = isset( $options['icon'] ) ? esc_attr($options['icon']) : "";
        $image_url = isset( $options['image_url'] ) ? esc_url($options['image_url']) : "";
        $text = $dynamic($options['text']);
        $title_text = $dynamic($options['title_text']);

        $maybe_link = isset( $options['maybe_link'] ) ? esc_attr($options['maybe_link']) : "";

        global $oxygen_svg_icons_to_load;
        $oxygen_svg_icons_to_load[] = $icon;
                
       $output = ('enable' === $maybe_link ) ? '<a href="#0" class="oxy-back-to-top_inner oxy-inner-content" ' : '<div class="oxy-back-to-top_inner oxy-inner-content" ';

            if(isset($options['scrolled'])) {

                $output .= 'data-scroll="' . esc_attr( $options['scrolled'] ) . '" ';
            }

            if(isset($options['scroll_transition'])) {

                $output .= 'data-scroll-duration="' . esc_attr( $options['scroll_transition'] ) . '" ';
            }

            if(isset($options['easing'])) {

                $output .= 'data-scroll-easing="' . esc_attr( $options['easing'] ) . '" ';
            }
        
            if($options['scrolling_back_up'] === 'true') {

                $output .= 'data-up="' . esc_attr( $options['scrolling_back_up'] ) . '" ';
            }


        if ( $title_text ) {
            $output .= 'title="'. $title_text .'"';
        }

       $output .= '>';
        
       if (!$content) {

           if ($options["type"] != "image" ) {

                $output .= '<span class="oxy-back-to-top_icon"><svg id="' . esc_attr($options['selector']) . '-icon"><use xlink:href="#' . $icon .'"></use></svg></span>';

                if ($options["type"] === "text" ) {
                    $output .= '<span class="oxy-back-to-top_text">'. $text .'</span>';
                }

           } else {

            $output .= '<img src="'. $image_url .'" class="oxy-back-to-top_image">';

            }
           
       } else {
           
           if ( function_exists('do_oxygen_elements') ) {
                $output .= do_oxygen_elements($content); 
            }
            else {
                $output .= do_shortcode($content); 
            } 
       }

        $output .= ('enable' === $maybe_link ) ? '</a>' : '</div>';
        
        echo $output;

        $this->dequeue_scripts_styles();
        
        // add JavaScript code only once and if shortcode presented
        if ($this->js_added !== true) {
            if (!defined('OXY_ELEMENTS_API_AJAX') || !OXY_ELEMENTS_API_AJAX) {
                add_action( 'wp_footer', array( $this, 'output_js' ) );
            }
            $this->js_added = true;
        }
        
    }

    function class_names() {
        return '';
    }

    function controls() {
        
        $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Button Type',
                'slug' => 'type'
            )
            
        )->setValue(array( "text" => "Icon / Text", "icon" => "Icon", "image" => "Image" ))
         ->setDefaultValue('text')->rebuildElementOnChange();


         


        
        /**
         * Image
         */
        
        $image_selector = '.oxy-back-to-top_image';
        
        $this->addOptionControl(
            array(
                'type' => 'mediaurl',
                'name' => 'Image URL',
                'slug' => 'image_url',
                'condition' => 'type=image',
                'default' => plugins_url( 'assets/circled-up-2.png', __FILE__ )
            )
        )->rebuildElementOnChange();
        
        $this->addStyleControls(
            array(
                array(
                    "name" => 'Image Width',
                    "property" => 'width',
                    "selector" => $image_selector,
                    "control_type" => "measurebox",
                    "value" => '90',
                    'condition' => 'type=image'
                )
            )
        );
        
        $position_control = $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Button Position',
                'slug' => 'position'
            )
            
        );
        $position_control->whiteList();
        $position_control->setValue(array( "bottomleft" => "Bottom Left", "bottomright" => "Bottom Right" ));
        $position_control->setDefaultValue('bottomright');
        $position_control->setValueCSS( array(
            "bottomleft"  => " {
                        left: 0;
                        right: auto;
                    }
                    
               ",
            "bottomright"  => " {
                        right: 0;
                        left: auto;
                    }"
        ) );
        
        $this->addOptionControl(
            array(
                "type" => 'checkbox',
                "name" => __('Only visible when scrolling up','oxygen'),
                "slug" => 'scrolling_back_up',
                "value" => 'false'
            )
        );
        
        $this->addOptionControl(
           array(
                "type" => 'measurebox',
                "name" => __('Visible after scrolling..'),
                "slug" 	    => "scrolled",
                "default" => "0",
                "control_type" => 'slider-measurebox',
            )
        )
        ->setUnits('px','px');
        
        $transition = $this->addStyleControl(
            array(
                "name" => __('Fade Transition'), 
                "property" => 'transition-duration',
                "default" => "300",
                "control_type" => 'slider-measurebox',
            )
        );

        $transition->setUnits('ms','ms');
        $transition->setRange(0, 1000, 10);
        
        
        $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Focusable by keyboard'),
                'slug' => 'maybe_link'
            )
        )->setValue(array(
            "enable" => "True",
            "disable" => "False"
         ))
         ->setDefaultValue('disable')->rebuildElementOnChange();


        
        /**
         * Scrolling Up
         */
        $scrolling_section = $this->addControlSection("scrolling_section", __("Scrolling Up"), "assets/icon.png", $this);
        
        
        $scrolling_section->addOptionControl(
           array(
                "type" => 'measurebox',
                "name" => __('Scroll Up Duration'),
                "slug" 	    => "scroll_transition",
                "default" => "300",
                "control_type" => 'slider-measurebox',
            )
        )
        ->setUnits('ms','ms');
        
        $scrolling_section->addOptionControl(
            array(
                "type" => "dropdown",
                "name" => "Easing",
                "slug" => "easing",
                "default" => 'linear',
            )
        )->setValue(
           array( 
                "swing" => "Swing", 
                "linear" => "Linear",
               
           )
       );
       
        
        /**
         * Button Styles
         */
        $button_section = $this->addControlSection("button_section", __("Button Styles"), "assets/icon.png", $this);
        
        $button_section->addStyleControls(
            array(
                array(
                    "name" => __('Background Color'),
                    "property" => 'background-color',
                ),
                array(
                    "name" => __('Background Hover Color'),
                    "selector" => ":hover",
                    "property" => 'background-color',
                ),
                array(
                    "name" => __('Text Color'),
                    "property" => 'color',
                ),
                array(
                    "name" => __('Text Hover Color'),
                    "selector" => ":hover",
                    "property" => 'color',
                ),
                array(
                    "property" => 'height',
                    "control_type" => "measurebox",
                ),
                array(
                    "property" => 'width',
                    "control_type" => "measurebox",
                )
            )
        );
        
        
        
        $button_section->borderSection('Border', '',$this);
        $button_section->borderSection('Border Hover', ''.":hover",$this);
        $button_section->boxShadowSection('Box Shadow', '',$this);
        $button_section->boxShadowSection('Box Shadow Hover', ''.":hover",$this);
        
        $button_padding_section = $button_section->addControlSection("button_padding_section", __("Spacing"), "assets/icon.png", $this);
        
        
        
        $button_padding_section->addStyleControls(
            array(
                array(
                    "property" => 'padding-left',
                    "control_type" => "measurebox",
                ),
                array(
                    "property" => 'padding-right',
                    "control_type" => "measurebox",
                ),
                array(
                    "property" => 'padding-bottom',
                    "control_type" => "measurebox",
                    "value" => '30'
                ),
                array(
                    "property" => 'padding-top',
                    "control_type" => "measurebox",
                    "value" => '30'
                ),
                array(
                    "property" => 'margin-left',
                    "control_type" => "measurebox",
                    "value" => '30'
                ),
                array(
                    "property" => 'margin-right',
                    "control_type" => "measurebox",
                    "value" => '30'
                ),
                array(
                    "property" => 'margin-top',
                    "control_type" => "measurebox",
                    "unit" => "px",
                    "value" => '30'
                ),
                array(
                    "property" => 'margin-bottom',
                    "control_type" => "measurebox",
                    "unit" => "px",
                    "value" => '30'
                )
            )
        );
        
        
        
        
         /**
         * Icon Styles
         */
        $icon_section = $this->addControlSection("icon_section", __("Icon"), "assets/icon.png", $this);
        $icon_svg_selector = '.oxy-back-to-top_icon svg'; 
        $icon_selector = '.oxy-back-to-top_icon';
        
        $icon_size = $icon_section->addStyleControl(
                array(
                    "name" => __('Icon Size'),
                    "slug" => "icon_size",
                    "selector" => $icon_svg_selector,
                    "control_type" => 'slider-measurebox',
                    "value" => '24',
                    "property" => 'font-size',
                    "condition" => 'type!=image',
                )
        );
        $icon_size->setRange(4, 72, 1);
        
        $icon_finder_section = $icon_section->addControlSection("icon_finder_section", __("Change Icon"), "assets/icon.png", $this);
        
        $icon_finder_section->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Icon'),
                "slug" => 'icon',
                "value" => 'FontAwesomeicon-angle-double-up', 
                "condition" => 'type!=image',
            )
        )->rebuildElementOnChange();
        
        $icon_color_section = $icon_section->addControlSection("icon_color_section", __("Colors"), "assets/icon.png", $this);
        $icon_section->borderSection('Border', $icon_selector,$this);
        $icon_section->borderSection('Border Hover', $icon_selector.":hover",$this);
        $icon_section->boxShadowSection('Box Shadow', $icon_selector,$this);
        $icon_section->boxShadowSection('Box Shadow Hover', $icon_selector.":hover",$this);
        
        $icon_section->addStyleControls(
            array(
                array(
                    "name" => 'Icon Height',
                    "property" => 'height',
                    "control_type" => "measurebox",
                    "selector" => $icon_selector,
                ),
                array(
                    "name" => 'Icon Width',
                    "property" => 'width',
                    "control_type" => "measurebox",
                    "selector" => $icon_selector,
                ),
                array(
                    "property" => 'z-index',
                    "control_type" => "measurebox",
                    "selector" => $icon_selector,
                    "value" => '99'
                )
            )
        );
        
        $icon_color_section->addStyleControls(
            array(
                array(
                    "name" => __('Background Color'),
                    "property" => 'background-color',
                    "selector" => $icon_selector,
                ),
                array(
                    "name" => __('Background Hover Color'),
                    "property" => 'background-color',
                    "selector" => $icon_selector.":hover",
                ),
                array(
                    "name" => __('Icon Color'),
                    "property" => 'color',
                    "selector" => $icon_selector,
                ),
                array(
                    "name" => __('Icon Hover Color'),
                    "property" => 'color',
                    "selector" => $icon_selector.":hover",
                )
            )
        );
        
        $icon_transition = $icon_section->addStyleControl(
            array(
                "name" => __('Hover Transition'), 
                "property" => 'transition-duration',
                "default" => "0",
                "control_type" => 'slider-measurebox',
                "selector" => $icon_selector
            )
        );
        
        $icon_transition->setUnits('ms','ms');
        $icon_transition->setRange(0, 1000, 10);
        
        
        $text_selector = '.oxy-back-to-top_text';
        $text_section = $this->typographySection('Text', $text_selector,$this);
        
        $text_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Text'),
                "default" => 'Back to Top',
                "slug" => 'text',
                "condition" => 'type=text',
                "base64" => true
            )
        )->rebuildElementOnChange()
         ->setParam('dynamicdatacode', '<div optionname="\'oxy-back-to-top_text\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');    
        
         $text_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Title text'),
                "default" => 'Back to Top',
                "slug" => 'title_text',
                "base64" => true
            )
        )->rebuildElementOnChange()
         ->setParam('dynamicdatacode', '<div optionname="\'oxy-back-to-top_title_text\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');    
        

    }
    
    
    function customCSS($options, $selector) {
    
        $css = "";
        
        $css .= ".oxy-back-to-top {
                    cursor: pointer;
                    position: fixed;
                    bottom: 0;
                    right: 0;
                    z-index: 99;
                    margin: 30px;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    opacity: 0;
                    visibility: hidden;
                    transition-property: opacity,visibility;
                    transition-duration: 0.3s;
                }
                
                .oxygen-builder-body .oxy-back-to-top {
                    opacity: 1;
                    visibility: visible;
                }
                
                .oxy-back-to-top.btt-visible {
                    opacity: 1;
                    visibility: visible;
                }
                
                .oxy-back-to-top_inner {
                    display: inline-flex;
                    flex-direction: column;
                    align-items: center;
                    color: inherit;
                    text-decoration: none;
                }
                
                .oxy-back-to-top_image {
                    height: auto;
                    max-width: 100%;
                    width: 90px;
                }
                
                .oxy-back-to-top_icon {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                
                .oxy-back-to-top_icon svg {
                    fill: currentColor;
                    width: 1em;
                    height: 1em;
                    pointer-events: none;
                }";

        return $css;
    }
    
    
    
    function allowedEmptyOptions($options) {

        $options_to_add = array(
            "oxy-back-to-top_text",
        );

        $options = array_merge($options, $options_to_add);

        return $options;
    } 
    
    
    
    function afterInit() {
        $this->removeApplyParamsButton();
    }
    
    
    function output_js() { ?>
            
            <script type="text/javascript">
            jQuery(document).ready(oxygen_init_btt);
            function oxygen_init_btt($) {

           //Get the button:
            let mybutton = $('.oxy-back-to-top'),
                scrollDuration = $('.oxy-back-to-top_inner').data( 'scroll-duration' ),
                scrollAmount = $('.oxy-back-to-top_inner').data( 'scroll' ),
                scrollEasing = $('.oxy-back-to-top_inner').data( 'scroll-easing' ),
                scrollDataUp = $('.oxy-back-to-top_inner').attr('data-up'),
                scrollUp;

                if (typeof scrollDataUp !== typeof undefined && scrollDataUp !== false) {
                    scrollUp = 'true';
                } else {
                    scrollUp = 'false';
                }
            
                mybutton.on('click', function(e) {
                  e.preventDefault();
                  $('html, body').animate({
                      scrollTop:0
                  }, {
                        duration: scrollDuration,
                        easing: scrollEasing,
                        }
                    );
                });
                
                let previousScroll = 0;
             
                $(window).scroll(function() {
                    var scroll = $(this).scrollTop();
                    if (scroll >= scrollAmount) {                
                        if (((scrollUp == 'true') && (scroll < previousScroll)) || (scrollUp != 'true')) {
                            mybutton.addClass('btt-visible');
                        } 
                        else {
                            mybutton.removeClass("btt-visible");
                        }
                    }
                    else {
                        mybutton.removeClass("btt-visible");
                    }
                    previousScroll = scroll;
                });
                
            };    
            
        </script>

    <?php }

}

add_filter("oxy_base64_encode_options", 
    function($items) { 
        $items=array_merge($items, array('oxy-back-to-top_text','oxy-back-to-top_title_text')); 
        return $items;
    }
);

new ExtraBacktoTop();