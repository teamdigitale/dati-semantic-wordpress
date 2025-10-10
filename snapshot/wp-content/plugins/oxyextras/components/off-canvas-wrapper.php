<?php

class ExtraOffCanvasWrapper extends OxygenExtraElements {
    
    var $js_added = false;
    var $js_inert_added = false;
    var $css_added = false;

	function name() {
        return __('Off Canvas'); 
    }
    
    function init() {
        $this->enableNesting();
    }

    function icon() {
        return plugin_dir_url(__FILE__) . 'assets/icons/'.basename(__FILE__, '.php').'.svg';
    }
    
    function extras_button_place() {
        return 'interactive';
    }
    
    
    function render($options, $defaults, $content) {

        $this->dequeue_scripts_styles();

        if ( defined('OXY_ELEMENTS_API_AJAX') && OXY_ELEMENTS_API_AJAX ) {

            ?>

            <div class="oxy-offcanvas_backdrop"></div>
            <div class="offcanvas-inner oxy-inner-content"></div>

            <?php

        } else {
        
            $trigger = isset( $options['trigger'] ) ? esc_attr( $options['trigger'] ) : 'ct-inner-wrap';
            $click_outside = $options['click_outside'] === 'true' ? 'true' : 'false';
            $start = $options['start'] === 'open' ? 'true' : 'false';
            $pressing_esc = $options['pressing_esc'] === 'true' ? 'true' : 'false';
            $focus_selector = $options['maybe_focus'] === 'selector' ? esc_attr( $options['focus_selector'] ) : '.offcanvas-inner';
            $maybe_focus = $options['maybe_focus'];
            $stagger_animation_delay = isset( $options['stagger_animation_delay'] ) ? esc_attr($options['stagger_animation_delay']) : '50';
            $stagger_first_delay = isset( $options['stagger_first_delay'] ) ? esc_attr($options['stagger_first_delay']) : '200';
            $animation_reset = isset( $options['animation_reset'] ) ? esc_attr($options['animation_reset']) : '400';
            
            $offcanvas_push = isset( $options['offcanvas_push'] ) ? esc_attr($options['offcanvas_push']) : '';
            $container_selector = isset( $options['container_selector'] ) ? esc_attr($options['container_selector']) : '';
            $offcanvas_push_duration = isset( $options['offcanvas_push_duration'] ) ? esc_attr($options['offcanvas_push_duration']) : '';
            
            $second_selector = isset( $options['second_selector'] ) ? esc_attr($options['second_selector']) : '';
            
            $maybe_hash_close = isset( $options['maybe_hash_close'] ) ? esc_attr($options['maybe_hash_close']) : '';
            
            $maybe_burger_sync = isset( $options['maybe_burger_sync'] ) ? esc_attr($options['maybe_burger_sync']) : '';

            $maybe_overflow = isset( $options['overflow'] ) && 'false' === esc_attr($options['overflow']) ? 'true' : 'false';

            $slide_menu_animations = isset( $options['slide_menu_animations'] ) ? esc_attr($options['slide_menu_animations']) : '';
            $slide_menu_animation_type = isset( $options['slide_menu_animation_type'] ) ? esc_attr($options['slide_menu_animation_type']) : '';

            $aria_label = isset( $options['aria_label'] ) ? esc_attr($options['aria_label']) : '';
            $inner_tag = isset( $options['inner_tag'] ) ? esc_attr($options['inner_tag']) : 'div';

            $output = '<div class="oxy-offcanvas_backdrop"></div>';
            
            $output .= '<' . $inner_tag . ' id="' . esc_attr($options['selector']) . '-inner" class="offcanvas-inner oxy-inner-content" role="dialog" aria-label="' . $aria_label . '" tabindex="0" data-start="'. $start .'" data-click-outside="'. $click_outside .'" data-trigger-selector="'. $trigger .'" data-esc="'. $pressing_esc .'" ';
            
            if ( 'selector' === $maybe_focus ) {
                $output .= 'data-focus-selector="'. $focus_selector .'" ';
            } elseif ( 'inner' === $maybe_focus ) {
                $output .= 'data-focus-selector=".offcanvas-inner" ';
            }
            
            $output .= 'data-reset="'. $animation_reset .'" ';
            
            $output .= 'data-hashclose="'. $maybe_hash_close .'" ';
            
            $output .= 'data-burger-sync="'. $maybe_burger_sync .'" ';

            $output .= 'data-overflow="'. $maybe_overflow .'" ';

            if ('false' === $options['maybe_inert'] ) {
                $output .= 'data-inert="false" ';
            }
            
            if ('true' === $options['maybe_auto_aria_controls']) {
                $output .= 'data-auto-aria="true" ';
            }

            if ('true' === $options['maybe_focus_trap']) {
                $output .= 'data-focus-trap="true" ';
            }

            
            
            if ( 'true' === $options['stagger_animations'] ) {
                $output .= 'data-stagger="'. $stagger_animation_delay .'" ';
                $output .= 'data-first-delay="'. $stagger_first_delay .'" ';
                $output .= 'true' === $slide_menu_animations ? 'data-stagger-menu="'. $slide_menu_animation_type .'" ' : 'data-stagger-menu="false" ';
            }
            
            if ( 'true' === $options['maybe_second_offcanvas'] ) {
                $output .= 'data-second-offcanvas="'. $second_selector .'" ';
            }
            
            if ( isset( $options['offcanvas_type'] ) && ('push' === esc_attr($options['offcanvas_type']) ) ) {
                $output .= 'data-content-push="'. $offcanvas_push .'" ';
                $output .= 'data-content-selector="'. $container_selector .'" ';
                $output .= 'data-content-duration="'. $offcanvas_push_duration .'" ';
            }
            
            $output .= '>';

                if ($content) {
                
                    if ( function_exists('do_oxygen_elements') ) {
                        $output .= do_oxygen_elements($content); 
                    }
                    else {
                        $output .= do_shortcode($content); 
                    } 
                
                } 
            
            $output .= '</'. $inner_tag .'>';
                
            echo $output;   

             
            // add JavaScript code only once and if shortcode presented
            if ($this->js_inert_added !== true) {
                if ('false' !== $options['maybe_inert'] ) {
                        add_action( 'wp_footer', array( $this, 'output_inert_js' ) );
                    $this->js_inert_added = true;
                }
            }
            if ($this->js_added !== true) {
                    add_action( 'wp_footer', array( $this, 'output_js' ) );
                $this->js_added = true;
            }

        }

        
        
    }

    function class_names() {
        return array();
    }
    
    
    
    

    function controls() {
        
        
        
         /**
         * Visibility in Builder
         */
        $visibility_oxygen = $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('In-builder visibility'),
                'slug' => 'builder_visibility'
            )
        )->setValue(array( "hidden" => "Hidden", "visible" => "Visible" ));
        $visibility_oxygen->setDefaultValue('visible');
        $visibility_oxygen->setValueCSS( array(
            "hidden"  => " {
                        display: none;
                    }
                    
               ",
        ) )->setParam('ng_show', "!iframeScope.isEditing('state')&&!iframeScope.isEditing('media')");
        
        $offcanvas_inner_selector = '.offcanvas-inner';
        
        $this->addStyleControls(
            array(
            
                array(
                    //"name" => 'Canvas Backgro'
                    "property" => 'background-color',
                    "default" => '#fff',
                    "selector" => $offcanvas_inner_selector,
                )
            )
        );

        /**
         * Choose trigger selector
         */
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Click selector'),
                "slug" => 'trigger',
                "default" => '.oxy-burger-trigger',
            )
        )->setParam("description", __("Change to custom class if not using burger trigger."));
        
        /**
         * Slide Options
         */
         $side_options = $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Slide in from',
                'slug' => 'side'
            )
            
        )->setValue(array( "left" => "Left", "right" => "Right", "top" => "Top", "bottom" => "Bottom" ));
         $side_options->setDefaultValue('left');
        $side_options->setValueCSS( array(
            "left"  => " .offcanvas-inner {
                            height: 100vh;
                            min-height: -webkit-fill-available;
                        }
                        ",
            "right"  => " .offcanvas-inner {
                            left: auto;
                            right: 0;
                            height: 100vh;
                            min-height: -webkit-fill-available;
                        }",
            "top"  => " .offcanvas-inner {
                            left: 0;
                            right: 0;
                            top: 0;
                            bottom: auto;
                            width: 100%;
                        }",
            "bottom"  => " .offcanvas-inner {
                            left: 0;
                            right: 0;
                            top: auto;
                            bottom: 0;
                            width: 100%;
                    }"
        ) );
        
        
        $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Offcanvas Type'),
                'slug' => 'offcanvas_type'
            )
            
        )->setValue(array( 
             "push" => "Push", 
             "slide" => "Slide",  
         ))->setDefaultValue('slide');
        
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Container selector to push'),
                "slug" => 'container_selector',
                "default" => '.site-container',
                "condition" => 'offcanvas_type=push'
            )
        );
        
        
        /**
         * Styles controls
         */
        $this->addOptionControl( 
            array(
                "name" => __('Push content by'),
                "default" => "280",
                "type" => 'slider-measurebox',
                "slug" => 'offcanvas_push',
                "condition" => 'offcanvas_type=push'
            )
        )
        ->setRange('0','1000','1')
        ->setUnits('px','px');    
        
        
        
        $this->addOptionControl(
            array(
                "name" => __('Content transition duration'),
                "type" => 'slider-measurebox',
                "default" => '.5',
                "slug" => 'offcanvas_push_duration',
                "condition" => 'offcanvas_type=push'
            )
        )
        ->setUnits('s','s')
        ->setRange('0','1','.01');
        
        
        /**
         * Styles controls
         */
        $this->addStyleControl( 
            array(
                "name" => __('Offcanvas width'),
                "type" => 'measurebox',
                "default" => "280",
                "units" => 'px',
                "property" => 'width',
                "control_type" => 'slider-measurebox',
                "selector" => $offcanvas_inner_selector,
                "condition" => 'side!=top&&side!=bottom'
                
            )
        )
        ->setRange('0','1000','1');
        
        $this->addStyleControl( 
            array(
                "name" => __('Offcanvas height'),
                "type" => 'measurebox',
                "default" => "300",
                "units" => 'px',
                "property" => 'height',
                "control_type" => 'slider-measurebox',
                "selector" => $offcanvas_inner_selector,
                "condition" => 'side!=left&&side!=right'
            )
        )
        ->setRange('0','1000','1');
        
        $this->addStyleControl(
            array(
                "name" => __('Offcanvas transition duration'),
                "property" => 'transition-duration',
                "control_type" => 'slider-measurebox',
                "default" => '.5',
                "selector" => $offcanvas_inner_selector,
            )
        )
        ->setUnits('s','s')
        ->setRange('0','1','.01');
        
        
        $this->addOptionControl(
            array(
                "type" => "dropdown",
                "name" => "Tag",
                "slug" => "inner_tag",
                "default" => 'div',
            )
        )->setValue(
           array( 
                "div" => "div", 
                "aside" => "aside",
           )
       );
      
       
        
        /**
         * Config
         */
        $offcanvas_section = $this->addControlSection("offcanvas_section", __("Config"), "assets/icon.png", $this);
        
        
        $offcanvas_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('State on page load'),
                'slug' => 'start'
            )
            
        )->setValue(array( "closed" => "Closed", "open" => "Open" ))
         ->setDefaultValue('closed');
        
        $offcanvas_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Site scrolling when open'),
                'slug' => 'overflow',
                'condition' => 'start=closed'
            )
            
        )->setValue(array( "true" => "Enabled", "false" => "Disabled" ))
         ->setDefaultValue('true');
        
        $offcanvas_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Clicking outside offcanvas closes it',
                'slug' => 'click_outside',
                'condition' => 'start=closed&&backdrop_display=display'
            )
            
        )->setValue(array( "true" => "Enabled", "false" => "Disabled" ))
         ->setDefaultValue('true');
        
        
        
        
        $offcanvas_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Browser performance (will-change)'),
                'slug' => 'maybe_will_change',
            )
            
        )->setValue(array( "true" => "Enabled", "false" => "Disabled" ))
         ->setDefaultValue('false')
         ->setValueCSS( array(
            "true"  => " .oxy-offcanvas_backdrop {
                            will-change: opacity, visibility;
                        }
                        .offcanvas-inner {
                            will-change: transform;
                        }
                    "
        ) );
        
        
        $offcanvas_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Force second offcanvas to close'),
                'slug' => 'maybe_second_offcanvas',
            )
            
        )->setValue(array( "true" => "Enabled", "false" => "Disabled" ))
         ->setDefaultValue('false');
        
        
        
       
        
        
        $offcanvas_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Second offcanvas selector'),
                "slug" => 'second_selector',
                "default" => '.other-offcanvas',
                "condition" => 'maybe_second_offcanvas=true',
                "base64" => true,
            )
        );
        
        
         $offcanvas_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Clicking hashlinks will close offcanvas'),
                'slug' => 'maybe_hash_close',
            )
            
        )->setValue(array( "true" => "Enabled", "false" => "Disabled" ))
         ->setDefaultValue('true');
        
        
        $offcanvas_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Sync burger triggers'),
                'slug' => 'maybe_burger_sync',
            )
            
        )->setValue(array( "true" => "Enabled", "false" => "Disabled" ))
         ->setDefaultValue('false')
         ->setParam("description", __("If using two burger triggers, will ensure they are both toggled"));
        
        
        
        
        /**
         * Inner Layout / Spacing
         */
        $offcanvas_inner_section = $this->addControlSection("Spacing", __("Layout / Spacing"), "assets/icon.png", $this);
        $offcanvas_inner_section->flex($offcanvas_inner_selector, $this);
        
        $this->boxShadowSection('Box Shadow', $offcanvas_inner_selector,$this);
    

        $offcanvas_inner_section->addStyleControl( 
            array(
                "name" => __('Padding top'),
                "property" => 'padding-top',
                "control_type" => "measurebox",
                "unit" => "px",
                "value" => '30',
                "selector" => $offcanvas_inner_selector
            )
        )->setParam('hide_wrapper_end', true);

        $offcanvas_inner_section->addStyleControl( 
            array(
                "name" => __('Padding right'),
                "property" => 'padding-right',
                "control_type" => "measurebox",
                "unit" => "px",
                "value" => '30',
                "selector" => $offcanvas_inner_selector
            )
        )->setParam('hide_wrapper_start', true);

        $offcanvas_inner_section->addStyleControl( 
            array(
                "name" => __('Padding bottom'),
                "property" => 'padding-bottom',
                "control_type" => "measurebox",
                "unit" => "px",
                "value" => '30',
                "selector" => $offcanvas_inner_selector
            )
        )->setParam('hide_wrapper_end', true);

        $offcanvas_inner_section->addStyleControl( 
            array(
                "name" => __('Padding left'),
                "property" => 'padding-left',
                "control_type" => "measurebox",
                "unit" => "px",
                "value" => '30',
                "selector" => $offcanvas_inner_selector
            )
        )->setParam('hide_wrapper_start', true);
        
        
        /**
         * Backdrop
         */
        $offcanvas_backdrop_section = $this->addControlSection("offcanvas_backdrop_section", __("Backdrop"), "assets/icon.png", $this);
        $offcanvas_backdrop_selector = '.oxy-offcanvas_backdrop';
        
        
        $backdrop_display = $offcanvas_backdrop_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Backdrop Display',
                'slug' => 'backdrop_display'
            )
        )->setValue(array( "hide" => "Disable", "display" => "Enable" ));
        $backdrop_display->setDefaultValue('display');
        $backdrop_display->setValueCSS( array(
            "hide"  => " .oxy-offcanvas_backdrop {
                            opacity: 0;
                            visibility: hidden;
                        }
                    
               "
        ) );
        
        $offcanvas_backdrop_section->addStyleControls(
            array(
                array(
                    "property" => 'background-color',
                    "selector" => $offcanvas_backdrop_selector,
                    "condition" => 'backdrop_display=display',
                    "default" => 'rgba(0,0,0,0.5)'
                ),
                array(
                    "property" => 'z-index',
                    "selector" => $offcanvas_backdrop_selector,
                    "condition" => 'backdrop_display=display',
                    "default" => '10'
                )
            )
        );
        
        $offcanvas_backdrop_section->addStyleControl(
            array(
                "name" => 'Fade Duration',
                "property" => 'transition-duration',
                "control_type" => 'slider-measurebox',
                "default" => '.5',
                "selector" => $offcanvas_backdrop_selector,
                 "condition" => 'backdrop_display=display'
            )
        )
        ->setUnits('s','s')
        ->setRange('0','1','.1');
        
        
        /**
         * Config
         */
        $inner_animations_section = $this->addControlSection("inner_animations_section", __("Inner Animations"), "assets/icon.png", $this);
        
        /**
         * Slide Options
         */
         $stagger_control = $inner_animations_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Stagger scroll animations'),
                'slug' => 'stagger_animations'
            )
            
        )->setValue(array( 
             "true" => "Enable", 
             "false" => "Disable"
         ));
         $stagger_control->setDefaultValue('false');
         $stagger_control->setParam("description", __("Will apply to any elements inside using scroll animations"));
        
        
        $inner_animations_section->addOptionControl(
            array(
                "type" => 'slider-measurebox',
                "name" => __('First element delay'),
                "slug" => 'stagger_first_delay',
                "value" => '200',
                "condition" => 'stagger_animations=true'
            )
        )->setUnits('ms','ms')
         ->setRange('0','1000','50');
        
        $inner_animations_section->addOptionControl(
            array(
                "type" => 'slider-measurebox',
                "name" => __('Delay each animation by..'),
                "slug" => 'stagger_animation_delay',
                "value" => '50',
                "condition" => 'stagger_animations=true'
            )
        )->setUnits('ms','ms')
         ->setRange('50','400','50');
        
        $inner_animations_section->addOptionControl(
            array(
                "type" => 'slider-measurebox',
                "name" => __('Animation reset timing'),
                "slug" => 'animation_reset',
                "value" => '400',
                "condition" => 'stagger_animations=true'
            )
        )->setUnits('ms','ms')
         ->setRange('100','1000','5')
         ->setParam("description", __("How long before animations reset after closing offcanvas"));        
        
        
         $inner_animations_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Include top-level slide menu items?'),
                'slug' => 'slide_menu_animations'
            )
        )->setValue(array( 
             "true" => "Enable", 
             "false" => "Disable"
         ))->setDefaultValue('false');

         $inner_animations_section->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Animation Type'),
                'slug' => 'slide_menu_animation_type',
                'condition' => 'slide_menu_animations=true'
            )
        )->setValue(array( 
            "fade" => __("Fade"),
			"fade-up" => __("Fade Up"),
			"fade-down" => __("Fade Down"),
			"fade-left" => __("Fade Left"),
			"fade-right" => __("Fade Right"),
			"fade-up-right" => __("Fade Up Right"),
			"fade-up-left" => __("Fade Up Left"),
			"fade-down-right" => __("Fade Down Right"),
			"fade-down-left" => __("Fade Down Left"),
			"flip-up" => __("Flip Up"),
			"flip-down" => __("Flip Down"),
			"flip-left" => __("Flip Left"),
			"flip-right" => __("Flip Right"),
			"slide-up" => __("Slide Up"),
			"slide-down" => __("Slide Down"),
			"slide-left" => __("Slide Left"),
			"slide-right" => __("Slide Right"),
			"zoom-in" => __("Zoom In"),
			"zoom-in-up" => __("Zoom In Up"),
			"zoom-in-down" => __("Zoom In Down"),
			"zoom-in-left" => __("Zoom In Left"),
			"zoom-in-right" => __("Zoom In Right"),
			"zoom-out" => __("Zoom Out"),
			"zoom-out-up" => __("Zoom Out Up"),
			"zoom-out-down" => __("Zoom Out Down"),
			"zoom-out-left" => __("Zoom Out Left"),
			"zoom-out-right" => __("Zoom Out Right"),
         ));


               
        /**
         * Accessibility
         */
        $accessibility_section = $this->addControlSection("accessibility_section", __("Accessibility"), "assets/icon.png", $this);
        
        
        $accessibility_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Set inert when offcanvas hidden'),
                'slug' => 'maybe_inert'
            )
            
        )->setValue(array( 
            "true" => "Enabled", 
            "false" => "Disabled" 
        ))
         ->setDefaultValue('true')
         ->setParam("description", __("Ensures user can't focus inside offcanvas when hidden"));

         $accessibility_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Auto add aria-controls to click trigger'),
                'slug' => 'maybe_auto_aria_controls'
            )
            
        )->setValue(array( 
            "true" => "Enabled", 
            "false" => "Disabled" 
        ))
         ->setDefaultValue('true');

         $accessibility_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Trap focus'),
                'slug' => 'maybe_focus_trap'
            )
            
        )->setValue(array( 
            "true" => "Enabled", 
            "false" => "Disabled" 
        ))
         ->setDefaultValue('true');

         $accessibility_section->addOptionControl(
            array(
                'type' => 'textfield',
                'name' => __('Aria label'),
                'slug' => 'aria_label',
                'default' => 'offcanvas content'
            )
        );


         $accessibility_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('ESC key when focus in offcanvas closes it'),
                'slug' => 'pressing_esc',
                'condition' => 'start=closed'
            )
            
        )->setValue(array( "true" => "Enabled", "false" => "Disabled" ))
         ->setDefaultValue('true');
        
        
        $accessibility_section->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Move focus when opened'),
                'slug' => 'maybe_focus',
            )
            
        )->setValue(
            array(  
                "disable" => "disable",
                "selector" => "selector inside offcanvas",
                "inner" => "offcanvas inner"
            )
        )
         ->setDefaultValue('inner');
        
        
        $accessibility_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Selector to receive focus (first found in offcanvas)'),
                "slug" => 'focus_selector',
                "default" => '.ff-el-input--content input',
                "condition" => 'maybe_focus=selector',
                "base64" => true,
            )
        );
        
    }
    
    
    function customCSS($options, $selector) {
        
        $css = '';
        
        $css .= "body:not(.oxygen-builder-body) $selector {
                    display: block;
                }
                
                body:not(.oxygen-builder-body) .editor-styles-wrapper $selector {
                    visibility: hidden;
                }
                
                 body.oxygen-builder-body $selector .offcanvas-inner {
                    -webkit-transform: none;
                        -ms-transform: none;
                            transform: none;
                    z-index: 2147483640;
                }";
        
               
        
        if( ! $this->css_added ) {
        
            $css .= ".oxy-off-canvas {
                        visibility: visible;
                        pointer-events: none;
                    }

                    .offcanvas-inner {
                        background: #fff;
                        display: -webkit-box;
                        display: -ms-flexbox;
                        display: flex;
                        -webkit-box-orient: vertical;
                        -webkit-box-direction: normal;
                            -ms-flex-direction: column;
                                flex-direction: column;
                        position: fixed;
                        height: 100vh;
                        max-width: 100%;
                        width: 280px;
                        overflow-x: hidden;
                        top: 0;
                        left: 0;
                        padding: 30px;
                        z-index: 1000;
                        -webkit-transition: -webkit-transform .5s cubic-bezier(0.77, 0, 0.175, 1), box-shadow .5s cubic-bezier(0.77, 0, 0.175, 1);
                        transition: transform .5s cubic-bezier(0.77, 0, 0.175, 1), box-shadow .5s cubic-bezier(0.77, 0, 0.175, 1);
                        -o-transition: -o-transform .5s cubic-bezier(0.77, 0, 0.175, 1), box-shadow .5s cubic-bezier(0.77, 0, 0.175, 1);
                        pointer-events: auto;
                    }

                    .offcanvas-inner:focus {
                        outline: none;
                    }

                    .oxy-offcanvas_backdrop {
                        background: rgba(0,0,0,.5);
                        position: fixed;
                        top: 0;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        opacity: 0;
                        visibility: hidden;
                        -webkit-transition: all .5s cubic-bezier(0.77, 0, 0.175, 1);
                        -o-transition: all .5s cubic-bezier(0.77, 0, 0.175, 1);
                        transition: all .5s cubic-bezier(0.77, 0, 0.175, 1);
                        pointer-events: auto;
                        z-index: 10;
                    }

                    .oxy-off-canvas-toggled .oxy-offcanvas_backdrop {
                        opacity: 1;
                         visibility: visible;
                    }
                
                    body.oxygen-builder-body .oxy-slide-menu-dropdown-icon-click-area {
                        position: relative;
                        z-index: 2147483641;
                    }

                    body.oxygen-builder-body .oxy-offcanvas_backdrop {
                        opacity: 1;
                        visibility: visible;
                    }

                    .oxy-off-canvas .aos-animate-disabled[data-aos^='fade'][data-aos^='fade'] {
                        opacity: 0;
                    }

                    .oxy-off-canvas .aos-animate-disabled[data-aos='fade-up'],
                    .oxy-off-canvas .aos-animate.aos-animate-disabled[data-aos='fade-up']{
                        transform: translate3d(0, 100px, 0);
                    }
                    .oxy-off-canvas .aos-animate-disabled[data-aos='fade-down'],
                    .oxy-off-canvas .aos-animate.aos-animate-disabled[data-aos='fade-down']{
                        transform: translate3d(0, -100px, 0);
                    }
                    .oxy-off-canvas .aos-animate-disabled[data-aos='fade-right'],
                    .oxy-off-canvas .aos-animate.aos-animate-disabled[data-aos='fade-right']{
                        transform: translate3d(-100px, 0, 0);
                    }
                    .oxy-off-canvas .aos-animate-disabled[data-aos='fade-left'],
                    .oxy-off-canvas .aos-animate.aos-animate-disabled[data-aos='fade-left']{
                        transform: translate3d(100px, 0, 0);
                    }
                    .oxy-off-canvas .aos-animate-disabled[data-aos='fade-up-right'],
                    .oxy-off-canvas .aos-animate.aos-animate-disabled[data-aos='fade-up-right'] {
                        transform: translate3d(-100px, 100px, 0);
                    }
                    .oxy-off-canvas .aos-animate-disabled[data-aos='fade-up-left'],
                    .oxy-off-canvas .aos-animate.aos-animate-disabled[data-aos='fade-up-left']{
                        transform: translate3d(100px, 100px, 0);
                    }
                    .oxy-off-canvas .aos-animate-disabled[data-aos='fade-down-right'],
                    .oxy-off-canvas .aos-animate.aos-animate-disabled[data-aos='fade-down-right']{
                        transform: translate3d(-100px, -100px, 0);
                    }
                    .oxy-off-canvas .aos-animate-disabled[data-aos='fade-down-left'],
                    .oxy-off-canvas .aos-animate.aos-animate-disabled[data-aos='fade-down-left']{
                        transform: translate3d(100px, -100px, 0);
                    }
                    .oxy-off-canvas .aos-animate-disabled[data-aos^='zoom'][data-aos^='zoom'],
                    .oxy-off-canvas .aos-animate.aos-animate-disabled[data-aos^='zoom'][data-aos^='zoom']{
                        opacity: 0;
                        transition-property: opacity, transform;
                    }
                    .oxy-off-canvas .aos-animate-disabled[data-aos^='zoom'][data-aos^='zoom'].aos-animate,
                    .oxy-off-canvas .aos-animate.aos-animate-disabled[data-aos^='zoom'][data-aos^='zoom'].aos-animate{
                        opacity: 1;
                        transform: translateZ(0) scale(1);
                    }
                    .oxy-off-canvas .aos-animate-disabled[data-aos='zoom-in'],
                    .oxy-off-canvas .aos-animate.aos-animate-disabled[data-aos='zoom-in']{
                        transform: scale(0.6);
                    }
                    .oxy-off-canvas .aos-animate-disabled[data-aos='zoom-in-up'],
                    .oxy-off-canvas .aos-animate.aos-animate-disabled[data-aos='zoom-in-up']{
                        transform: translate3d(0, 100px, 0) scale(0.6);
                    }
                    .oxy-off-canvas .aos-animate-disabled[data-aos='zoom-in-down'],
                    .oxy-off-canvas .aos-animate.aos-animate-disabled[data-aos='zoom-in-down']{
                        transform: translate3d(0, -100px, 0) scale(0.6);
                    }
                    .oxy-off-canvas .aos-animate-disabled[data-aos='zoom-in-right'],
                    .oxy-off-canvas .aos-animate.aos-animate-disabled[data-aos='zoom-in-right']{
                        transform: translate3d(-100px, 0, 0) scale(0.6);
                    }
                    .oxy-off-canvas .aos-animate-disabled[data-aos='zoom-in-left'],
                    .oxy-off-canvas .aos-animate.aos-animate-disabled[data-aos='zoom-in-left']{
                        transform: translate3d(100px, 0, 0) scale(0.6);
                    }
                    .oxy-off-canvas .aos-animate-disabled[data-aos='zoom-out'],
                    .oxy-off-canvas .aos-animate.aos-animate-disabled[data-aos='zoom-out'] {
                        transform: scale(1.2);
                    }
                    .oxy-off-canvas .aos-animate-disabled[data-aos='zoom-out-up'],
                    .oxy-off-canvas .aos-animate.aos-animate-disabled[data-aos='zoom-out-up'] {
                        transform: translate3d(0, 100px, 0) scale(1.2);
                    }
                    .oxy-off-canvas .aos-animate-disabled[data-aos='zoom-out-down'],
                    .oxy-off-canvas .aos-animate.aos-animate-disabled[data-aos='zoom-out-down'] {
                        transform: translate3d(0, -100px, 0) scale(1.2);
                    }
                    .oxy-off-canvas .aos-animate-disabled[data-aos='zoom-out-right'],
                    .oxy-off-canvas .aos-animate.aos-animate-disabled[data-aos='zoom-out-right'] {
                        transform: translate3d(-100px, 0, 0) scale(1.2);
                    }
                    .oxy-off-canvas .aos-animate-disabled[data-aos='zoom-out-left'],
                    .oxy-off-canvas .aos-animate-disabled[data-aos='zoom-out-left'] {
                        transform: translate3d(100px, 0, 0) scale(1.2);
                    }
                    .oxy-off-canvas .aos-animate-disabled[data-aos^='slide'][data-aos^='slide'],
                    .oxy-off-canvas .aos-animate.aos-animate-disabled[data-aos^='slide'][data-aos^='slide'] {
                        transition-property: transform;
                    }
                    .oxy-off-canvas .aos-animate-disabled[data-aos='slide-up'],
                    .oxy-off-canvas .aos-animate.aos-animate-disabled[data-aos='slide-up'] {
                        transform: translate3d(0, 100%, 0);
                    }
                    .oxy-off-canvas .aos-animate-disabled[data-aos='slide-down'],
                    .oxy-off-canvas .aos-animate.aos-animate-disabled[data-aos='slide-down'] {
                        transform: translate3d(0, -100%, 0);
                    }
                    .oxy-off-canvas .aos-animate-disabled[data-aos='slide-right'],
                    .oxy-off-canvas .aos-animate.aos-animate-disabled[data-aos='slide-right'] {
                        transform: translate3d(-100%, 0, 0);
                    }
                    .oxy-off-canvas .aos-animate-disabled[data-aos='slide-left'],
                    .oxy-off-canvas .aos-animate.aos-animate-disabled[data-aos='slide-left'] {
                        transform: translate3d(100%, 0, 0);
                    }
                    .oxy-off-canvas .aos-animate-disabled[data-aos^='flip'][data-aos^='flip'],
                    .oxy-off-canvas .aos-animate.aos-animate-disabled[data-aos^='flip'][data-aos^='flip'] {
                        backface-visibility: hidden;
                        transition-property: transform;
                    }
                    .oxy-off-canvas .aos-animate-disabled[data-aos='flip-left'],
                    .oxy-off-canvas .aos-animate.aos-animate-disabled[data-aos='flip-left'] {
                        transform: perspective(2500px) rotateY(-100deg);
                    }
                    .oxy-off-canvas .aos-animate-disabled[data-aos='flip-right'],
                    .oxy-off-canvas .aos-animate-disabled[data-aos='flip-right'] {
                        transform: perspective(2500px) rotateY(100deg);
                    }
                    .oxy-off-canvas .aos-animate-disabled[data-aos='flip-up'],
                    .oxy-off-canvas .aos-animate.aos-animate-disabled[data-aos='flip-up'] {
                        transform: perspective(2500px) rotateX(-100deg);
                    }
                    .oxy-off-canvas .aos-animate-disabled[data-aos='flip-up'].aos-animate {
                        transform: perspective(2500px) rotateX(0);
                    }
                    .oxy-off-canvas .aos-animate-disabled[data-aos='flip-down'],
                    .oxy-off-canvas .aos-animate.aos-animate-disabled[data-aos='flip-down']{
                        transform: perspective(2500px) rotateX(100deg);
                    }
                    .oxy-off-canvas .aos-animate-disabled[data-aos='flip-down'].aos-animate {
                        transform: perspective(2500px) rotateX(0);
                    }
                    
                    .oxy-off-canvas .screen-reader-text {
                        clip: rect(1px,1px,1px,1px);
                        height: 1px;
                        overflow: hidden;
                        position: absolute!important;
                        width: 1px;
                        word-wrap: normal!important;
                    }
                    
                    .admin-bar .oxy-off-canvas .offcanvas-inner {
                        margin-top: 32px;
                    }
                    
                    @media screen and (max-width: 782px) {
                        .admin-bar .oxy-off-canvas .offcanvas-inner {
                            margin-top: 46px;
                        }
                    }
                    
                    body.oxygen-builder-body.admin-bar  .oxy-off-canvas .offcanvas-inner {
                        margin-top: 0;
                     }";
            
            $this->css_added = true;
            
            
        }
        
        if ( (isset($options["oxy-off-canvas_overflow"]) && $options["oxy-off-canvas_overflow"] === "false") && ( !isset($options["oxy-off-canvas_start"] ) || ( null !== $options["oxy-off-canvas_start"] && $options["oxy-off-canvas_start"] === "closed" ) ) ) {

            $toggled = str_replace('#','toggled', $selector);        

            $css .= "html.$toggled,
                    body.$toggled {
                        overflow: hidden;
                    }";         
            
        }

        
         /**
         * Offcanvas Left (default)
         */
        if ((!isset($options["oxy-off-canvas_start"]) || $options["oxy-off-canvas_start"] === "closed")) {
            
            $css .= ".oxy-off-canvas .offcanvas-inner {
                        -webkit-transform: translate(-100%,0);
                            -ms-transform: translate(-100%,0);
                                transform: translate(-100%,0);
                    }

                    $selector.oxy-off-canvas-toggled.oxy-off-canvas .offcanvas-inner {
                        -webkit-transform: none;
                        -ms-transform: none;
                            transform: none;
                    }
                    
                    [data-offcanvas-push='$selector'].oxy-off-canvas-toggled {
                        -webkit-transform: translate(var(--offcanvas-push),0);
                            -ms-transform: translate(var(--offcanvas-push),0);
                                transform: translate(var(--offcanvas-push),0);
                    }
                    
                    body:not(.oxygen-builder-body) $selector:not(.oxy-off-canvas-toggled) .offcanvas-inner {
                        box-shadow: none;
                    }
                    ";
            
        } else {
            
            $css .= "body:not(.oxygen-builder-body) $selector.oxy-off-canvas-toggled .offcanvas-inner {
                        box-shadow: none;
                    }
                    ";
        }
        
        
        /**
         * Offcanvas Right
         */
        if ((isset($options["oxy-off-canvas_side"]) && $options["oxy-off-canvas_side"] === "right")) {
            
            if ((!isset($options["oxy-off-canvas_start"]) || $options["oxy-off-canvas_start"] === "closed")) {
                
                $css .= "$selector .offcanvas-inner {
                        -webkit-transform: translate(100%,0);
                            -ms-transform: translate(100%,0);
                                transform: translate(100%,0);
                    }
                
                    $selector.oxy-off-canvas-toggled .offcanvas-inner {
                        -webkit-transform: none;
                        -ms-transform: none;
                            transform: none;
                    }
                    
                    [data-offcanvas-push='$selector'].oxy-off-canvas-toggled {
                        -webkit-transform: translate(calc(0px - (var(--offcanvas-push))),0);
                            -ms-transform: translate(calc(0px - (var(--offcanvas-push))),0);
                                transform: translate(calc(0px - (var(--offcanvas-push))),0);
                    }";
                
            } else {
                
                 $css .= "$selector.oxy-off-canvas-toggled .offcanvas-inner  {
                        -webkit-transform: translate(100%,0);
                            -ms-transform: translate(100%,0);
                                transform: translate(100%,0);
                    }
            
                    body.oxygen-builder-body $selector .offcanvas-inner {
                        -webkit-transform: none;
                        -ms-transform: none;
                            transform: none;
                            left: auto;
                    }";
                
            }
            
            
        }
        
        
        if ((isset($options["oxy-off-canvas_start"]) && $options["oxy-off-canvas_start"] === "open")) {

            $css .= "$selector.oxy-offcanvas_backdrop {
                            visibility: visible;
                                opacity: 1;
                            }
                            
                            $selector.oxy-off-canvas-toggled.oxy-off-canvas .oxy-offcanvas_backdrop {
                                opacity: 0;
                                visibility: hidden;
                            }";

            if ( !isset( $options["oxy-off-canvas_side"] )  || ( $options["oxy-off-canvas_side"] !== "right" && $options["oxy-off-canvas_side"] !== "top" && $options["oxy-off-canvas_side"] !== "bottom")) {
            
                $css .= "$selector.oxy-off-canvas-toggled.oxy-off-canvas .offcanvas-inner {
                            -webkit-transform: translate(-100%,0);
                                -ms-transform: translate(-100%,0);
                                    transform: translate(-100%,0);
                        }";

                }
            
        }
        
        
        /**
         * Offcanvas Top
         */
        if ((isset($options["oxy-off-canvas_side"]) && $options["oxy-off-canvas_side"] === "top")) {
            
            if ((!isset($options["oxy-off-canvas_start"]) || $options["oxy-off-canvas_start"] === "closed")) {
                
                $css .= "$selector .offcanvas-inner {
                        -webkit-transform: translate(0,-100%);
                            -ms-transform: translate(0,-100%);
                                transform: translate(0,-100%);
                    }
                
                    $selector.oxy-off-canvas-toggled .offcanvas-inner {
                        -webkit-transform: none;
                        -ms-transform: none;
                            transform: none;
                    }
                    
                    [data-offcanvas-push='$selector'].oxy-off-canvas-toggled {
                        -webkit-transform: translate(0,var(--offcanvas-push));
                            -ms-transform: translate(0,var(--offcanvas-push));
                                transform: translate(0,var(--offcanvas-push));
                    }";
                
            } else {
                
                 $css .= "$selector.oxy-off-canvas-toggled .offcanvas-inner  {
                        -webkit-transform: translate(0,-100%);
                            -ms-transform: translate(0,-100%);
                                transform: translate(0,-100%);
                    }
            
                    body.oxygen-builder-body $selector .offcanvas-inner {
                        -webkit-transform: none;
                        -ms-transform: none;
                            transform: none;
                        left: auto;
                    }";
                
            }
            
           
            
        }
        
        
        
        /**
         * Offcanvas Bottom
         */
        if ((isset($options["oxy-off-canvas_side"]) && $options["oxy-off-canvas_side"] === "bottom")) {
            
            if ((!isset($options["oxy-off-canvas_start"]) || $options["oxy-off-canvas_start"] === "closed")) {
                
                $css .= "$selector .offcanvas-inner {
                        -webkit-transform: translate(0,100%);
                            -ms-transform: translate(0,100%);
                                transform: translate(0,100%);
                    }
                
                    $selector.oxy-off-canvas-toggled .offcanvas-inner {
                        -webkit-transform: none;
                        -ms-transform: none;
                            transform: none;
                    }
                    
                    [data-offcanvas-push='$selector'].oxy-off-canvas-toggled {
                        -webkit-transform: translate(0,calc(0px - (var(--offcanvas-push))));
                            -ms-transform: translate(0,calc(0px - (var(--offcanvas-push))));
                                transform: translate(0,calc(0px - (var(--offcanvas-push))));
                    }";
                
            } else {
                
                 $css .= "$selector.oxy-off-canvas-toggled .offcanvas-inner  {
                        -webkit-transform: translate(0,100%);
                            -ms-transform: translate(0,100%);
                                transform: translate(0,100%);
                    }
            
                    body.oxygen-builder-body $selector .offcanvas-inner {
                        -webkit-transform: none;
                        -ms-transform: none;
                            transform: none;
                        left: auto;
                    }";
                
            }
            
            
        }
        
        if ((isset($options["oxy-off-canvas_offcanvas_type"]) && $options["oxy-off-canvas_offcanvas_type"] === "push")) {
        
            $css .= "[data-offcanvas-push='$selector'] {
                          -webkit-transition: -webkit-transform var(--offcanvas-push-duration) cubic-bezier(0.77, 0, 0.175, 1);
                            transition: -webkit-transform var(--offcanvas-push-duration) cubic-bezier(0.77, 0, 0.175, 1);
                            -o-transition: transform var(--offcanvas-push-duration) cubic-bezier(0.77, 0, 0.175, 1);
                            transition: transform var(--offcanvas-push-duration) cubic-bezier(0.77, 0, 0.175, 1);
                            transition: transform var(--offcanvas-push-duration) cubic-bezier(0.77, 0, 0.175, 1), 
                            -webkit-transform var(--offcanvas-push-duration) cubic-bezier(0.77, 0, 0.175, 1);   
                    }
                    
            ";
            
            if ((isset($options["oxy-off-canvas_maybe_will_change"]) && $options["oxy-off-canvas_maybe_will_change"] === "true")) {
             
                $css .= "[data-offcanvas-push='$selector'] {
                            will-change: transform;
                        }";
                
            }
            
            if ((isset($options["oxy-off-canvas_overflow"]) && $options["oxy-off-canvas_overflow"] !== "false")  && ($options["oxy-off-canvas_start"] != "true") ) {

                $toggled = str_replace('#','toggled', $selector);        

                $css .= "html.$toggled,
                        body.$toggled {
                            overflow-x: hidden;
                        }";  
                
            }
                
            
        }
        
        
        return $css;
    }
    
    function afterInit() {
        $this->removeApplyParamsButton();
    }

    function output_inert_js() {
        wp_enqueue_script( 'extras-inert', plugin_dir_url(__FILE__) . 'assets/inert.js', '', '1.0.0' );
    }
    
    function output_js() { 
        wp_enqueue_script( 'extras-offcanvas', plugin_dir_url(__FILE__) . 'assets/offcanvas-init.js', '', '1.0.4' );
    }
    
}

new ExtraOffCanvasWrapper();