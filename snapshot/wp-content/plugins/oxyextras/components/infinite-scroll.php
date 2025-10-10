<?php

class ExtraInfiniteScroll extends OxygenExtraElements {
        
    var $js_added = false;
    var $css_added = false;

	function name() {
        return 'Infinite Scroller';
    }
    
    function icon() {
        return plugin_dir_url(__FILE__) . 'assets/icons/'.basename(__FILE__, '.php').'.svg';
    }
    
    function extras_button_place() {
        return "interactive";
    }
    
    /*function tag() {
        return array('default' => 'li', 'choices' => 'li,div,span' );
    }*/
    
    function enablePresets() {
        return true;
    }
    
    function enableFullPresets() {
        return true;
    }
    
    
    
    function init() {
        
        $this->enableNesting();
        
        //add_filter("oxy_allowed_empty_options_list", array( $this, "allowedEmptyOptions") );
    
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
        
        
        // options
        $loop_type = isset( $options['loop_type'] ) ? esc_attr($options['loop_type']) : 'easy_posts';
        $infinite_trigger = isset( $options['infinite_trigger'] ) ? esc_attr($options['infinite_trigger']) : 'scroll';
        $scroll_threshold = isset( $options['scroll_threshold'] ) ? esc_attr($options['scroll_threshold']) : '0';
        $button_selector = isset( $options['button_selector'] ) ? esc_attr($options['button_selector']) : '.load-more';
        $maybe_animation = isset( $options['maybe_animation'] ) ? esc_attr($options['maybe_animation']) : '';
        
        $browser_history = isset( $options['browser_history'] ) ? esc_attr($options['browser_history']) : '';
        $library_support = isset( $options['library_support'] ) ? esc_attr($options['library_support']) : '';
        $scroll_element = isset( $options['scroll_element'] ) ? esc_attr($options['scroll_element']) : '';
        $scroll_container_selector = isset( $options['scroll_container_selector'] ) ? esc_attr($options['scroll_container_selector']) : '';
        
        $content_selector = isset( $options['content_selector'] ) ? esc_attr($options['content_selector']) : '';
        $container_selector = isset( $options['container_selector'] ) ? esc_attr($options['container_selector']) : '';
        $next_page_link = isset( $options['next_page_link'] ) ? esc_attr($options['next_page_link']) : '';
        $link_selector = isset( $options['link_selector'] ) ? esc_attr($options['link_selector']) : '';

        $page_break_button_selector = isset( $options['page_break_button_selector'] ) ? esc_attr($options['page_break_button_selector']) : '';
        
        $retrigger_aos = isset( $options['retrigger_aos'] ) ? esc_attr($options['retrigger_aos']) : '';

        $easy_post_selector = isset( $options['easy_post_selector'] ) ? esc_attr($options['easy_post_selector']) : '.oxy-post';
        
        if ('custom' === $scroll_element) {
            
            $scroll_el = $scroll_container_selector;
            
        } else if ('this' === $scroll_element) {
            
            $scroll_el = '#'. esc_attr($options['selector']);
            
        } else {
            
            $scroll_el = 'false';
            
        }
        
        
        $end_text = $dynamic( $options['end_text'] );
        
        $output = '<div class="oxy-infinite-scroller_inner oxy-inner-content" ';
        
        $output .= 'data-type="'. $loop_type .'" ';
        
        $output .= 'data-trigger="'. $infinite_trigger .'" ';
        
        $output .= 'data-scroll-threshold="'. $scroll_threshold .'" ';
        
        $output .= ('page_break' !== $loop_type) ? 'data-btn-selector="'. $button_selector .'" ' : 'data-btn-selector="'. $page_break_button_selector .'" ';
        
        $output .= 'data-history="'. $browser_history .'" ';
        
        $output .= 'data-scroll-el="'. $scroll_el .'" ';
        
        $output .= 'data-support="'. $library_support .'" ';
        
        $output .= 'data-retrigger-aos="'. $retrigger_aos .'" ';
        
        if ('custom' === $loop_type) {
        
            $output .= 'data-content-selector="'. $content_selector .'" ';

            $output .= 'data-container-selector="'. $container_selector .'" ';
            
            if ('prev' === $next_page_link) {
                $output .= 'data-link-selector=".oxy-infinite-scroller_prev" ';
            } else {
                $output .= 'data-link-selector="'. $link_selector .'" ';
            }
            
        }

        if ('page_break' === $loop_type) {

            $output .= 'data-content-selector=".ct-inner-content" ';
        }

        if ('easy_posts' === $loop_type) {

            $output .= 'data-post-selector="'. $easy_post_selector .'" ';
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
        
        $output .= '</div>';
        
        if ( ('prev' === $next_page_link ) && ('custom' === $loop_type) ) {
            $prev_post  = get_previous_post();
            $output .= (! empty( $prev_post )) ? '<a class="oxy-infinite-scroller_prev" href="'. get_permalink( $prev_post->ID ) .'"></a>': '';
        }

        if ('page_break' === $loop_type) {
            $output .= wp_link_pages('next_or_number=number');
        }
        
        $output .= '<div class="page-load-status">';
        
        if ('true' === $maybe_animation) {
            
            $animation_css = $options['animation_css'];
            
            switch ($animation_css) {
                    
            case 'Plane':
                $animation_css = '<div class="sk-plane"></div>';
            break;
            case 'Chase':
                $animation_css = '<div class="sk-chase">
                                  <div class="sk-chase-dot"></div>
                                  <div class="sk-chase-dot"></div>
                                  <div class="sk-chase-dot"></div>
                                  <div class="sk-chase-dot"></div>
                                  <div class="sk-chase-dot"></div>
                                  <div class="sk-chase-dot"></div>
                                </div>';
            break;
            case 'Bounce':
                $animation_css = '<div class="sk-bounce">
                                  <div class="sk-bounce-dot"></div>
                                  <div class="sk-bounce-dot"></div>
                                </div>';
            break;
            case 'Plane':
                $animation_css = '<div class="sk-plane"></div>';
            break;
            case 'Wave':
                $animation_css = '<div class="sk-wave">
                                  <div class="sk-wave-rect"></div>
                                  <div class="sk-wave-rect"></div>
                                  <div class="sk-wave-rect"></div>
                                  <div class="sk-wave-rect"></div>
                                  <div class="sk-wave-rect"></div>
                                </div>';
            break;
            case 'Pulse':
                $animation_css = '<div class="sk-pulse"></div>';
            break;  
            case 'Flow':
                $animation_css = '<div class="sk-flow">
                                  <div class="sk-flow-dot"></div>
                                  <div class="sk-flow-dot"></div>
                                  <div class="sk-flow-dot"></div>
                                </div>';
            break;  
            case 'Swing':
                $animation_css = '<div class="sk-swing">
                                  <div class="sk-swing-dot"></div>
                                  <div class="sk-swing-dot"></div>
                                </div>';
            break;  
            case 'Circle':
                $animation_css = '<div class="sk-circle">
                                  <div class="sk-circle-dot"></div>
                                  <div class="sk-circle-dot"></div>
                                  <div class="sk-circle-dot"></div>
                                  <div class="sk-circle-dot"></div>
                                  <div class="sk-circle-dot"></div>
                                  <div class="sk-circle-dot"></div>
                                  <div class="sk-circle-dot"></div>
                                  <div class="sk-circle-dot"></div>
                                  <div class="sk-circle-dot"></div>
                                  <div class="sk-circle-dot"></div>
                                  <div class="sk-circle-dot"></div>
                                  <div class="sk-circle-dot"></div>
                                </div>';
            break;  
            case 'Circle Fade':
                $animation_css = '<div class="sk-circle-fade">
                                  <div class="sk-circle-fade-dot"></div>
                                  <div class="sk-circle-fade-dot"></div>
                                  <div class="sk-circle-fade-dot"></div>
                                  <div class="sk-circle-fade-dot"></div>
                                  <div class="sk-circle-fade-dot"></div>
                                  <div class="sk-circle-fade-dot"></div>
                                  <div class="sk-circle-fade-dot"></div>
                                  <div class="sk-circle-fade-dot"></div>
                                  <div class="sk-circle-fade-dot"></div>
                                  <div class="sk-circle-fade-dot"></div>
                                  <div class="sk-circle-fade-dot"></div>
                                  <div class="sk-circle-fade-dot"></div>
                                </div>';
            break;  
            case 'Grid':
                $animation_css = '<div class="sk-grid">
                                  <div class="sk-grid-cube"></div>
                                  <div class="sk-grid-cube"></div>
                                  <div class="sk-grid-cube"></div>
                                  <div class="sk-grid-cube"></div>
                                  <div class="sk-grid-cube"></div>
                                  <div class="sk-grid-cube"></div>
                                  <div class="sk-grid-cube"></div>
                                  <div class="sk-grid-cube"></div>
                                  <div class="sk-grid-cube"></div>
                                </div>';
            break;  
            case 'Fold':
                $animation_css = '<div class="sk-fold">
                                  <div class="sk-fold-cube"></div>
                                  <div class="sk-fold-cube"></div>
                                  <div class="sk-fold-cube"></div>
                                  <div class="sk-fold-cube"></div>
                                </div>';
            break;  
            case 'Wander':
                $animation_css = '<div class="sk-wander">
                                  <div class="sk-wander-cube"></div>
                                  <div class="sk-wander-cube"></div>
                                  <div class="sk-wander-cube"></div>
                                </div>';
            break;          
            }
            
            
            $output .= '<div class="infinite-scroll-request"><div class="infinite-scroll-request_inner">';
            $output .= $animation_css;
            $output .= '</div></div>';
            
        }
        
        if (isset( $options['maybe_end'] ) && 'true' === $options['maybe_end']) {
            
            $output .= '<div class="infinite-scroll-last">';
            $output .= $end_text;
            $output .= '</div>';
            
        }
        
        $output .= '</div>';
        
        echo $output;

        $this->dequeue_scripts_styles();
        
        if ($this->js_added !== true) {
            if (!defined('OXY_ELEMENTS_API_AJAX') || !OXY_ELEMENTS_API_AJAX) {
                add_action( 'wp_footer', array( $this, 'output_js' ) );
                add_action( 'wp_footer', array( $this, 'output_init_js' ), 995 );
            }
            $this->js_added = true;
        }  
        
    
    }
    

    function class_names() {
        return array();
    }

    function controls() {
        
        $this->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Content type'),
                'slug' => 'loop_type',
            )
            
        )->setValue(array( 
            "repeater" => "Repeater", 
            "easy_posts" => "Easy posts",
            "woo" => 'Products List',
            "page_break" => 'Page content (using page breaks)',
            "custom" => 'Custom'
            )
        )->setValueCSS( array(
            ""  => "",
        ) );

        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Post selector'),
                "slug" => 'easy_post_selector',
                "default" => '.oxy-post',
                "condition" => 'loop_type=easy_posts',
                "base64" => true,
            )
        );
        
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Content selector'),
                "slug" => 'content_selector',
                "default" => '.post-content',
                "condition" => 'loop_type=custom',
                "base64" => true,
            )
        )->setParam("description", __("What content to pull in from the next page?"));

        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Container selector'),
                "slug" => 'container_selector',
                "default" => '.oxy-infinite-scroller_inner',
                "condition" => 'loop_type=custom',
                "base64" => true,
            )
        )->setParam("description", __("Which container content to append the content?"));
        
        $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Which page to get content from?'),
                'slug' => 'next_page_link',
                "condition" => 'loop_type=custom',
            )
            
        )->setValue(array( 
            "prev" => "Previous Post",   
            "custom" => "Follow link on page"
        ))
         ->setDefaultValue('prev');
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Link selector'),
                "slug" => 'link_selector',
                "default" => '.prev-post',
                "condition" => 'loop_type=custom&&next_page_link=custom',
                "base64" => true,
            )
        );

        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Click selector'),
                "slug" => 'page_break_button_selector',
                "default" => '.view-more',
                "condition" => 'loop_type=page_break',
                "base64" => true,
            )
        )->setParam("description", __("Make sure the post content is inside the infinite scroller, but the view more link is outside"));
        
        
        $this->addCustomControl(
            '<div style="color: #eee; line-height: 1.3; font-size: 13px; opacity: .3;"><hr></div>','description');
        
        
        $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Trigger to load more'),
                'slug' => 'infinite_trigger',
                "condition" => 'loop_type!=page_break'
            )
            
        )->setValue(array( 
            "button" => "Click element", 
            "scroll" => "Scroll down" 
        ))
         ->setDefaultValue('scroll');
        
        $this->addOptionControl(
            array(
                "type" => 'slider-measurebox',
                "name" => __('Scroll threshold'),
                "slug" => 'scroll_threshold',
                "default" => '0',
                "condition" => 'infinite_trigger=scroll&&loop_type!=page_break'
            )
        )->setUnits('px', 'px')
         ->setRange('0','1000','1');
        
        $this->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Scroll element'),
                'slug' => 'scroll_element',
                "condition" => 'infinite_trigger=scroll&&loop_type!=page_break'
            )
            
        )->setValue(array( 
            "page" => "Page (default)", 
            "this" => "This element",
            "custom" => "A container element"
        ))
         ->setDefaultValue('page')
         ->setValueCSS( array(
            "this"  => "{
                        overflow-y: scroll;
                    }",
             ));    
        
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Container selector'),
                "slug" => 'scroll_container_selector',
                "default" => '.container',
                "condition" => 'infinite_trigger=scroll&&scroll_element=custom',
                "base64" => true,
            )
        )->setParam("description", __("(Ensure this container element has overflow set to scroll and has a set height)"));
        
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Click selector'),
                "slug" => 'button_selector',
                "default" => '.load-more',
                "condition" => 'infinite_trigger=button&&loop_type!=page_break',
                "base64" => true,
            )
        );
        
        
        $this->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Change URL and browser history'),
                'slug' => 'browser_history',
                'condition' => 'loop_type!=page_break'
            )
            
        )->setValue(array( 
            "push" => "Push", 
            "false" => "Disable",
             "replace" => "Replace",
            
        ))->setDefaultValue('false');
        
        
        $this->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Retrigger scroll animations after adding new items'),
                'slug' => 'retrigger_aos',
                'condition' => 'loop_type!=page_break'
            )
            
        )->setValue(array( 
            "true" => "Enable",
            "false" => "Disable",
            
        ))->setDefaultValue('false');
        
        
        $this->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Add support for another library'),
                'slug' => 'library_support',
                'condition' => 'loop_type!=page_break'
            )
            
        )->setValue(array( 
            "iso" => "Isotope",
            "msnry" => "Masonry",
            "pckry" => "Packery",
            "false" => "False",
            
        ))->setDefaultValue('false');
        
        
        
        
        
        /**
         * End Content Text
         */ 
        $end_section = $this->addControlSection("end_section", __("No content text"), "assets/icon.png", $this);
        
        $end_selector = '.infinite-scroll-last';
        
        $end_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Display text when no more content'),
                'slug' => 'maybe_end'
            )
            
        )->setValue(array( 
            "true" => "True", 
            "false" => "False",
            
        ))->setDefaultValue('false')
          ->setValueCSS( array(
            "false"  => " $end_selector {
                display: none;
            }",
        ) );    
        
         $end_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Preview in builder'),
                'slug' => 'endtext_builder',
                "condition" => 'maybe_end=true'
            )
            
        )->setValue(array( "true" => "Visible", "false" => "Hidden" ))
         ->setValueCSS( array(
            "false"  => " $end_selector {
                            visibility: hidden;
                        }",
        ) )
         ->setDefaultValue('true');
        
        $end_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('End of content text'),
                "slug" => 'end_text',
                "default" => "No more content to load.",
                "condition" => 'maybe_end=true',
                "base64" => true,
            )
        )->setParam('dynamicdatacode', '<div optionname="\'oxy-infinite-scroller_end_text\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');
        
        
        
        
        $end_positioning_section = $end_section->addControlSection("end_positioning_section", __("Positioning"), "assets/icon.png", $this);
        
        
        $end_positioning_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Alignment'),
                'slug' => 'endtext_alignment',
                "condition" => 'maybe_end=true'
            )
            
        )->setValue(array( 
            "left" => "Left", 
            "center" => "Center",
            "right" => "Right" 
        ))
         ->setValueCSS( array(
            "left"  => " $end_selector {
                            text-align: left;
                        }",
             "center"  => " $end_selector {
                            text-align: center;
                        }",
             "right"  => " $end_selector {
                            text-align: right;
                        }",
        ) )
         ->setDefaultValue('center');
        
        
       
        
        
        $end_positioning_section->addStyleControl(
                array(
                    "selector" => $end_selector,
                    "control_type" => 'measurebox',
                    "value" => '',
                    "property" => 'margin-top',
                    "condition" => 'maybe_animation=true'
                )
        )->setUnits('px', 'px,%,em,auto,vw,vh')
         ->setParam('hide_wrapper_end', true);
        
        $end_positioning_section->addStyleControl(
                array(
                    "selector" => $end_selector,
                    "control_type" => 'measurebox',
                    "value" => '-50',
                    "property" => 'margin-bottom',
                    "condition" => 'maybe_animation=true'
                )
        )->setUnits('px', 'px,%,em,auto,vw,vh')
         ->setParam('hide_wrapper_start', true);
        
        $end_positioning_section->addStyleControl(
                array(
                    "selector" => $end_selector,
                    "control_type" => 'measurebox',
                    "value" => '',
                    "property" => 'margin-left',
                    "condition" => 'maybe_animation=true'
                )
        )->setUnits('px', 'px,%,em,auto,vw,vh')
         ->setParam('hide_wrapper_end', true);
        
        $end_positioning_section->addStyleControl(
                array(
                    "selector" => $end_selector,
                    "control_type" => 'measurebox',
                    "value" => '',
                    "property" => 'margin-right',
                    "condition" => 'maybe_animation=true'
                )
        )->setUnits('px', 'px,%,em,auto,vw,vh')
         ->setParam('hide_wrapper_start', true);
        
        
        
        /**
         * Loading animations
         */ 
        $animation_section = $this->addControlSection("animation_section", __("Loading animation"), "assets/icon.png", $this);
        
        $animation_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Loading animation'),
                'slug' => 'maybe_animation'
            )
            
        )->setValue(array( "true" => "Enable", "false" => "Disable" ))
          ->setValueCSS( array(
            "false"  => ".infinite-scroll-request {
                display: none;
            }",
        ) )->setDefaultValue('true')
            ->setParam("description", __("Click 'Apply Params' button to apply animation settings"));
        
        $animation_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Animation preview in builder'),
                'slug' => 'animation_builder',
                "condition" => 'maybe_animation=true'
            )
            
        )->setValue(array( "true" => "Visible", "false" => "Hidden" ))
         ->setValueCSS( array(
            "false"  => " .infinite-scroll-request {
                visibility: hidden;
            }",
        ) )->setDefaultValue('true');
        
        
        
        
        $animation_control = $animation_section->addOptionControl(
            array(
                "type" => "dropdown",
                "name" => "Animation",
                "slug" => "animation_css",
                "default" => 'Pulse',
                "condition" => 'maybe_animation=true'
            )
        )->setValue(
           array( 
               "Plane",
               "Chase",
               "Bounce",
               "Wave",
               "Pulse",
               //"Flow",
               "Swing",
               "Circle",
               "Circle Fade",
               "Grid",
               "Fold",
               //"Wander",
           )
       );
        
        
        
        /**
         * Animation Styles
         */
        
        $animation_styling_section = $animation_section->addControlSection("animation_styling_section", __("Styling"), "assets/icon.png", $this);
        
        $anim_selector = '.infinite-scroll-request';
        
        $animation_styling_section->addStyleControl(
            array(
                "property" => 'color',
                "default" => '#ffffff',
                "selector" => $anim_selector,
                "condition" => 'maybe_animation=true'
            )
        );
        
        
        $animation_styling_section->addStyleControl( 
            array(
                "selector" => $anim_selector,
                "name" => 'Size',
                "default" => "40",
                "property" => '--sk-size',
                "control_type" => 'slider-measurebox',
                "condition" => 'maybe_animation=true'
            )
        )
        ->setUnits('px','px')
        ->setRange('0','300','1');
        
        $animation_styling_section->addStyleControl(
            array(
                "property" => 'opacity',
                "default" => '',
                "selector" => $anim_selector,
                "condition" => 'maybe_animation=true'
            )
        );
        
        $animation_styling_section->addStyleControl( 
            array(
                "selector" => '.infinite-scroll-request_inner > div',
                "default" => "1.2",
                "property" => 'animation-duration',
                "control_type" => 'slider-measurebox',
                "condition" => 'maybe_animation=true'
            )
        )
        ->setUnits('s','s')
        ->setRange('0','3','.1');
        
        
        $animation_styling_section->addStyleControl( 
            array(
                "selector" => '.infinite-scroll-request_inner > div > div, .infinite-scroll-request_inner > div > div::before',
                "name" => 'Inner Animation Duration',
                "default" => "1.2",
                "property" => 'animation-duration',
                "control_type" => 'slider-measurebox',
                "condition" => 'maybe_animation=true'
            )
        )
        ->setUnits('s','s')
        ->setRange('0','3','.1');
        
        
        $animation_positioning_section = $animation_section->addControlSection("animation_positioning_section", __("Positioning"), "assets/icon.png", $this);
        
        
        $animation_positioning_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Alignment'),
                'slug' => 'animation_alignment',
                "condition" => 'maybe_animation=true'
            )
            
        )->setValue(array( 
            "left" => "Left", 
            "center" => "Center",
            "right" => "Right" 
        ))
         ->setValueCSS( array(
            "left"  => ".infinite-scroll-request_inner {
                            justify-content: flex-start;
                        }",
             "right"  => ".infinite-scroll-request_inner {
                            justify-content: flex-end;
                        }",
        ) )
         ->setDefaultValue('center');
        
        
        $anim_div_selector = '.infinite-scroll-request_inner';
        
        $animation_positioning_section->addStyleControl(
                array(
                    "selector" => $anim_div_selector,
                    "control_type" => 'measurebox',
                    "value" => '',
                    "property" => 'margin-top',
                    "condition" => 'maybe_animation=true'
                )
        )->setUnits('px', 'px,%,em,auto,vw,vh')
         ->setParam('hide_wrapper_end', true);
        
        $animation_positioning_section->addStyleControl(
                array(
                    "selector" => $anim_div_selector,
                    "control_type" => 'measurebox',
                    "value" => '-50',
                    "property" => 'margin-bottom',
                    "condition" => 'maybe_animation=true'
                )
        )->setUnits('px', 'px,%,em,auto,vw,vh')
         ->setParam('hide_wrapper_start', true);
        
        $animation_positioning_section->addStyleControl(
                array(
                    "selector" => $anim_div_selector,
                    "control_type" => 'measurebox',
                    "value" => '',
                    "property" => 'margin-left',
                    "condition" => 'maybe_animation=true'
                )
        )->setUnits('px', 'px,%,em,auto,vw,vh')
         ->setParam('hide_wrapper_end', true);
        
        $animation_positioning_section->addStyleControl(
                array(
                    "selector" => $anim_div_selector,
                    "control_type" => 'measurebox',
                    "value" => '',
                    "property" => 'margin-right',
                    "condition" => 'maybe_animation=true'
                )
        )->setUnits('px', 'px,%,em,auto,vw,vh')
         ->setParam('hide_wrapper_start', true);
        
        

    }
    
  
    
    function customCSS($options, $selector) {
        
        $css = '';
        
        if( ! $this->css_added ) {
        
            $css .= file_get_contents(__DIR__.'/preloader.css');

            $css .= ".oxy-infinite-scroller {
                        --sk-color: currentColor;
                        --sk-size : 50px;
                        width: 100%;
                        position: relative;
                    }

                    .oxy-infinite-scroller .infinite-scroll-request_inner {
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            margin-bottom: -50px;
                    }

                    .oxy-infinite-scroller .infinite-scroll-request {
                            position: absolute;
                            bottom: 0;
                            width: 100%;
                    }

                    .oxy-infinite-scroller_inner:empty {
                        min-height: 80px;
                    }

                    .oxy-infinite-scroller .page-load-status {
                       position: relative;
                       left: 0;
                       right: 0;
                       pointer-events: none;
                    }

                    .oxy-infinite-scroller .oxy-easy-posts-pages,
                    .oxy-infinite-scroller .oxy-repeater-pages-wrap,
                    .oxy-infinite-scroller .woocommerce-pagination,
                    .oxy-infinite-scroller .post-nav-links,
                    .oxy-infinite-scroller_next,
                    .oxy-infinite-scroller_prev {
                        display: none;
                    }";
            
                $this->css_added = true;
            
            
            }
                
                
             $css .= "body:not(.oxygen-builder-body) $selector .infinite-scroll-request,
                      body:not(.oxygen-builder-body) $selector .infinite-scroll-last {
                        display: none;
                        visibility: visible;
                    }
                ";
        
        return $css;
        
    }
    
    
    function output_js() {
         wp_enqueue_script( 'infinite-scroll-js', plugin_dir_url( __FILE__ ) . 'assets/infinite-scroll-v4.min.js', '', '4.0.1', true );
    }
    
    function output_init_js() {
        
        ?><script type="text/javascript">
            jQuery(document).ready(oxygen_init_infinitescroll);
            function oxygen_init_infinitescroll($) {
                $('.oxy-infinite-scroller').each(function(i, oxyInfiniteScroll){
                    
                    var infiniteID = '#' + $(oxyInfiniteScroll).attr('ID'),
                        scroller = $(oxyInfiniteScroll).children('.oxy-infinite-scroller_inner'),
                        btnSelector = scroller.data('btn-selector'),
                        libSupport = '',
                        container = '',
                        nextURL,
                        linkSelector = scroller.data('link-selector'),
                        postSelector = scroller.data('post-selector');
                    
                    if ('easy_posts' === scroller.data('type')) {
                        var appendSelector = infiniteID + ' ' + postSelector,
                            pageNav = '.oxy-easy-posts-pages',
                            container = '.oxy-posts',
                            nextLink = infiniteID + ' .next';
                    } else if ('woo' === scroller.data('type')) {
                        var appendSelector = infiniteID + ' .product',
                            pageNav = '.woocommerce-pagination',
                            container = '.products',
                            nextLink = infiniteID + ' .next';
                    } else if ('repeater' === scroller.data('type')){
                        var appendSelector = infiniteID + ' .oxy-dynamic-list > .ct-div-block',
                            pageNav = '.oxy-repeater-pages-wrap',
                            container = '.oxy-dynamic-list',
                            nextLink = infiniteID + ' .next';
                    } else if ('page_break' === scroller.data('type')){
                        var appendSelector = scroller.data('content-selector'),
                            pageNav = '.post-nav-links',
                            container = '.oxy-infinite-scroller_inner',
                            nextLink = infiniteID + ' .post-page-numbers.current + .post-page-numbers';
                    } else {
                        var appendSelector = scroller.data('content-selector'),
                            container = scroller.data('container-selector'),
                            //pageNav = '',
                            nextLink = linkSelector;
                    }
                    
                    if ('iso' === scroller.data('support')) {
                        libSupport = $(infiniteID + ' ' + container).data('isotope');
                    } else if ('msnry' === scroller.data('support')) {
                        libSupport = $(infiniteID + ' ' + container).data('masonry');
                    } else if ('pckry' === scroller.data('support')) {
                        libSupport = $(infiniteID + ' ' + container).data('packery');
                    } else {
                        libSupport = '';
                    }
                    
                    if ('custom' === scroller.data('type')) {
                    
                        function updateNextURL( doc ) {
                          nextURL = $( doc ).find(linkSelector).attr('href');
                        }
                        // get initial nextURL
                        updateNextURL( document );
                    
                    }
                    
                    if ($(nextLink).length) {
                
                        $(oxyInfiniteScroll).find(container).infiniteScroll({
                            path: ('custom' === scroller.data('type')) ? function() {
                                return nextURL;
                            } : nextLink,
                            history: scroller.data('history'),
                            status: infiniteID + ' .page-load-status',
                            elementScroll: scroller.data('scroll-el'),
                            hideNav: pageNav,
                            button: btnSelector,
                            append: appendSelector,
                            scrollThreshold: scroller.data('scroll-threshold'),
                            loadOnScroll: ('scroll' === scroller.data('trigger')),
                            outlayer: libSupport,
                        });
                        
                        $(btnSelector).css("overflow-anchor", "none");
                        
                    } else {
                        $(btnSelector).hide();
                    }
                    
                    if (true === scroller.data('retrigger-aos')) {
                        $(appendSelector).parent().on( 'append.infiniteScroll', function( event, body ) {   
                                AOS.init();
                        });
                    }
                    if ('custom' === scroller.data('type')) {
                        $(appendSelector).parent().on( 'load.infiniteScroll', function( event, body ) {
                            updateNextURL( body ); 
                        });
                    }

                    $(appendSelector).parent().on( 'append.infiniteScroll', function( event, body, path, items, response ) {
        
                        /* Lightbox */
                        if (typeof doExtrasLightbox == 'function' && $(oxyInfiniteScroll).has('.oxy-lightbox')) {
                           doExtrasLightbox(jQuery(items));
                        }
                    
                        /* Read More / Less */
                        if (typeof doExtrasReadmore == 'function' && $(oxyInfiniteScroll).has('.oxy-read-more-less')) {
                           doExtrasReadmore(jQuery(items));
                        }
                        
                        /* Tabs */
                        if (typeof doExtrasTabs == 'function' && $(oxyInfiniteScroll).has('.oxy-dynamic-tabs')) {
                           doExtrasTabs(jQuery(items));
                        }
                        
                        /* Accordion */
                        if (typeof doExtrasAccordion == 'function' && $(oxyInfiniteScroll).has('.oxy-pro-accordion')) {
                           doExtrasAccordion(jQuery(items));
                        }
                        
                        /* Carousel */
                        if (typeof doExtrasCarousel == 'function' && $(oxyInfiniteScroll).has('.oxy-carousel-builder')) {
                           doExtrasCarousel(jQuery(items));
                        }

                        /* Popover */
                        if (typeof doExtrasPopover == 'function' && $(oxyInfiniteScroll).has('.oxy-popover')) {
                            doExtrasPopover(jQuery(items));
                        }

                    });

            });
                
        } </script> <?php
        
    }

}

add_filter("oxy_base64_encode_options", 
    function($items) { 
        $items=array_merge($items, array('oxy-infinite-scroller_end_text')); 
        return $items;
    }
);


new ExtraInfiniteScroll();