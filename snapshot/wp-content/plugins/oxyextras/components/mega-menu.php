<?php

class ExtraMegaMenu extends OxygenExtraElements {
        
     var $js_added = false;
    var $css_added = false;
    
	function name() {
        return 'Mega Menu';
    }
    
     // Element options
    function options(){

        return array(
            "only_child" => "oxy-mega-dropdown"
        );

    }
    
    function enablePresets() {
        return true;
    }
    
    function enableFullPresets() {
        return true;
    }
    
    function tag() {
        return array('default' => 'nav');
    }
    
    function icon() {
        return plugin_dir_url(__FILE__) . 'assets/icons/'.basename(__FILE__, '.php').'.svg';
    }
    
    function extras_button_place() {
        return "interactive";
    }
    
    function init() {

        add_action("ct_toolbar_component_settings", array( $this, "add_dropdown_button"), 1);
        add_action("ct_toolbar_component_settings", array( $this, "add_mega_menu_button"), 1);
        
        $this->enableNesting();
        
    }
    
    function add_mega_menu_button() { ?>

        <div class="oxygen-control-row"
            ng-show="isActiveName('oxy_header')&&!hasOpenTabs('oxy_header')">
            <div class="oxygen-control-wrapper">
                <div id="oxygen-add-another-row" class="oxygen-add-section-element"
                    ng-click="iframeScope.addComponent('oxy-mega-menu')">
                    <img class="extras-ui-icon-padding" src='<?php echo plugin_dir_url(__FILE__) ?>assets/icons/mega-menu.svg' />
                    <img class="extras-ui-icon-padding" src='<?php echo plugin_dir_url(__FILE__) ?>assets/icons/mega-menu.svg' />
                    <?php _e("Add Mega Menu","oxygen"); ?>
                </div>
            </div>
        </div>

    <?php }
    
    
    
    
    function add_dropdown_button() { ?>

        <div class="oxygen-control-row"
            ng-show="isActiveName('oxy-mega-menu')&&!hasOpenTabs('oxy-mega-menu')">
            <div class="oxygen-control-wrapper">
                <div id="oxygen-add-another-row" class="oxygen-add-section-element"
                    ng-click="iframeScope.addComponent('oxy-mega-dropdown')">
                    <img class="extras-ui-icon-padding" src='<?php echo plugin_dir_url(__FILE__) ?>assets/icons/dropdown.svg' />
                    <img class="extras-ui-icon-padding" src='<?php echo plugin_dir_url(__FILE__) ?>assets/icons/dropdown-active.svg' />
                    <?php _e("Add New Dropdown","oxygen"); ?>
                </div>
            </div>
        </div>

    <?php }
    
    
    
    function render($options, $defaults, $content) {

        $this->dequeue_scripts_styles();

        if ( defined('OXY_ELEMENTS_API_AJAX') && OXY_ELEMENTS_API_AJAX ) {

            ?> <div class="oxy-inner-content oxy-mega-menu_inner"></div> <?php

        } else {
        
            $trigger = isset( $options['trigger'] ) ? esc_attr($options['trigger']) : "";
            $hovertabs = isset( $options['maybe_hovertabs'] ) ? esc_attr($options['maybe_hovertabs']) : "";
            $open_delay = isset( $options['open_delay'] ) ? esc_attr($options['open_delay']) : "";
            $close_delay = isset( $options['close_delay'] ) ? esc_attr($options['close_delay']) : "";
            $maybe_hash_close = isset( $options['maybe_hash_close'] ) ? esc_attr($options['maybe_hash_close']) : "";

            $type = isset( $options['type'] ) ? esc_attr($options['type']) : "";
            
            $maybe_mouseover = isset( $options['maybe_mouseover'] ) ? esc_attr($options['maybe_mouseover']) : "";
            
            $slide_duration = isset( $options['slide_duration'] ) ? esc_attr($options['slide_duration']) : "";
            
            $output = '<ul class="oxy-inner-content oxy-mega-menu_inner" ';
            
            $output .= 'data-trigger="'. $trigger .'" ';
            
            $output .= 'data-hovertabs="'. $hovertabs .'" ';
            
            $output .= 'data-odelay="'. $open_delay .'" ';
            
            $output .= 'data-cdelay="'. $close_delay .'" ';
            
            $output .= 'data-duration="'. $slide_duration .'" ';
            
            $output .= 'data-mouseover="'. $maybe_mouseover .'" ';

            $output .= 'data-hash-close="'. $maybe_hash_close .'" ';

            if ('true' === $options['maybe_auto_aria_controls']) {
                $output .= 'data-auto-aria="true" ';
            }


            
            if ( isset( $options['maybe_body_scroll'] ) && 'true' === esc_attr($options['maybe_body_scroll']) ) {
            
                $output .= 'data-prevent-scroll="true" ';
                
            }
                
            $output .= 'data-type="'. $type .'" ';    
            
            $output .= '>';
            
            if ($content) {
                
                if ( function_exists('do_oxygen_elements') ) {
                    $output .=  do_oxygen_elements($content); 
                }
                else {
                    $output .=  do_shortcode($content); 
                }
                
            } 
            
            $output .= '</ul>';
            
            echo $output;
            
            
            if( method_exists('OxygenElement', 'builderInlineJS') ) {
            
                $this->El->builderInlineJS(
                    "jQuery('#%%ELEMENT_ID%%').closest('.oxy-header-container').addClass('oxy-header-container_mega-menu');
                ");
                
            }
            
            // add JavaScript code only once and if shortcode presented
            if ($this->js_added !== true) {

                if (!defined('OXY_ELEMENTS_API_AJAX') || !OXY_ELEMENTS_API_AJAX) {
                    add_action( 'wp_footer', array( $this, 'output_js' ) );
                } 
                    
                $this->js_added = true;
            }

        }
        
    }

    function class_names() {
        return array();
    }
    
   

    function controls() {
        
        
        $this->addOptionControl(
            array(
                "type" => "dropdown",
                "name" => __("Mega menu type"),
                "slug" => "type",
            )
        )->setValue(
           array( 
                "individual" => "Individual dropdowns (standard)", 
                "container" => "Container dropdown (one height)",
                //"custom" => "Custom",
               
           )
       )->setDefaultValue('individual')
        ->setValueCSS( array(
            "individual"  => " {
                        
                        }",
            "container"  => ".oxy-mega-dropdown_inner.oxy-header-container {
                                max-height: 0;
                                overflow: hidden;
                                opacity: 1;
                                visibility: visible;
                            }
                            
                            .oxy-mega-dropdown_inner.oxy-mega-dropdown_inner-open {
                                z-index: 1;
                            }
                            
                            .oxy-mega-dropdown_inner .oxy-mega-dropdown_container {
                                opacity: 1;
                                transform: none;
                                height: 100%;
                            }

                            .oxy-mega-menu_active .oxy-mega-dropdown_inner.oxy-header-container {
                                height: var(--expanded-height);
                                max-height: var(--expanded-height);
                            }"
        ) );
        
        
        $this->addStyleControl( 
            array(
                "name" => 'Expanded container height',
                "default" => "600",
                "property" => '--expanded-height',
                "slug" => "expanded_height",
                "control_type" => 'slider-measurebox',
                "condition" => 'type=container',
                "selector" => '.oxy-mega-menu_inner'
            )
        )
        ->setRange('0','1000','1')
        ->setUnits('px','px');
        
        
        $this->addStyleControl(
            array(
                "selector" => '.oxy-mega-dropdown_inner',
                "name" => __('Expand duration'),
                "property" => 'transition-duration',
                "control_type" => 'slider-measurebox',
                "condition" => 'type=container',
            )
        )
        ->setUnits('ms','ms')
        ->setRange('0','1000','1')
        ->setDefaultValue('500');    
        
        
        $this->addOptionControl(
            array(
                "type" => "dropdown",
                "name" => __("Default dropdown width*"),
                "slug" => "container_width",
                "default" => '',
            )
        )->setValue(
           array( 
                "b_header" => "Header row width (container)", 
                "d_full" => "Full width",
                "c_content" => "Header row width (content)",
                "a_none" => "Custom widths",
           )
       )->setValueCSS( array(
            "b_header"  => " .oxy-mega-dropdown_inner {
                                transform: translateX(-50%);
                                left: 50%;
                                width: 100%;
                            } ",
            "d_full"  => " .oxy-header-container.oxy-mega-dropdown_inner {
                              max-width: 100%;
                               width: 100%;
                               padding-left: 0;
                               padding-right: 0;
                            }
                           .oxy-header-container.oxy-mega-dropdown_content {
                               max-width: 100%;
                               width: var(--global-content-width);
                            }",
            "c_content"  => ".oxy-mega-dropdown_inner.oxy-header-container {
                           max-width: 100%;
                           width: 100%;
                           padding-left: 0;
                           padding-right: 0;
                        }
                        .oxy-mega-dropdown_inner {
                            transform: translateX(-50%);
                            left: 50%;
                        }
                        
                        .oxy-mega-dropdown_content {
                            width: var(--dropdown-content-width)
                        }
                        ",
            "a_none"  => ".oxy-mega-dropdown {
                            position: relative;
                        }
                        .oxy-mega-dropdown_inner.oxy-header-container {
                           max-width: var(--global-dropdown-width);
                           width: var(--global-dropdown-width);
                        }"
                        
        ) )->setParam("description", __("Set this before adding your content"));
        
        
        $this->addStyleControl( 
            array(
                "name" => __('Container width'),
                "property" => '--global-dropdown-width',
                "control_type" => 'slider-measurebox',
                "selector" => '.oxy-mega-dropdown_inner',
                "condition" => 'container_width=a_none'
            )
        )
        ->setRange('0','1000','1')
        ->setUnits('px');
        
        $this->addStyleControl( 
            array(
                "name" => __('Content width'),
                "property" => '--global-content-width',
                "control_type" => 'slider-measurebox',
                "selector" => '.oxy-mega-dropdown_content',
                "condition" => 'container_width=d_full'
            )
        )
        ->setRange('0','1400','1')
        ->setUnits('px')
        ->setParam("description", __("Leave blank for full width"));       
        
        
        $this->addOptionControl(
            array(
                "type" => "buttons-list",
                "name" => __("Dropdown positioning (relative to link)"),
                "slug" => "dropdown_position",
                "default" => 'left',
                "condition" => 'container_width=a_none'
            )
        )->setValue(
           array( 
                "a_left" => "Left", 
                "b_center" => "Center",
                "c_right" => "Right",
               
           )
       )->setValueCSS( array(
            "b_center"  => ".oxy-mega-dropdown_inner {
                                transform: translateX(-50%);
                                left: 50%;
                        }",
            "c_right"  => ".oxy-mega-dropdown_inner {
                                left: auto;
                                right: 0;
                        }",
                        
        ) )->whiteList();
        
        
       
        
        
        $outer_selector = '.oxy-mega-dropdown_inner';
        $open_inner_selector = '.open.oxy-mega-dropdown_inner';
        $dropdown_content_selector = '.oxy-mega-dropdown_content';
        $container_selector = '.oxy-mega-dropdown_container, .oxy-mega-dropdown_flyout .sub-menu';
        
        
        
        /**
         * Colors
         */
        //$colors_section = $this->addControlSection("colors_section", __("Colors"), "assets/icon.png", $this);
        
        
        
        
        
        $this->addCustomControl(
            '<div style="color: #fff; line-height: 1.3; font-size: 13px;">General settings (across all dropdowns)</div><hr>','description');
        
        
        
        /**
         * Links
         */
        $link_section = $this->addControlSection("link_section", __("Dropdown Links"), "assets/icon.png", $this);
        $link_selector = '.oxy-mega-dropdown_link, .oxy-mega-menu_inner > .ct-link-text';
        $link_focus_selector = '.oxy-mega-dropdown_link.oxy-mega-dropdown_inner-focus';
        $link_hover_selector = '.oxy-mega-dropdown_link:hover';
        $link_open_selector = '.oxy-mega-dropdown_link.oxy-mega-dropdown_inner-open';
        $current_link_selector = '.oxy-mega-dropdown_link-current';
        $current_link_ancestor_selector = '.oxy-mega-dropdown_link-current-ancestor';
        
        
        
        $link_padding_left = $link_section->addStyleControl(
                 array(
                    "selector" => $link_selector,
                    "property" => 'padding-left',
                    "default" => '10',
                    "control_type" => 'measurebox', 
                )
        );
        $link_padding_left->setParam('hide_wrapper_end', true);
        $link_padding_left->setUnits('px');    
        
        $link_padding_right = $link_section->addStyleControl(
                array(
                    "selector" => $link_selector,
                    "property" => 'padding-right',
                    "default" => '10',
                    "control_type" => 'measurebox',
                )
        );
        $link_padding_right->setParam('hide_wrapper_start', true);
        $link_padding_right->setUnits('px'); 
        
        $link_padding_top = $link_section->addStyleControl(
                 array(
                    "selector" => $link_selector,
                    "property" => 'padding-top',
                     "default" => '5', 
                    "type" => 'measurebox', 
                )
        );
        $link_padding_top->setParam('hide_wrapper_end', true);
        $link_padding_top->setUnits('px'); 
            
        $link_padding_bottom = $link_section->addStyleControl(
                 array(
                    "selector" => $link_selector,
                    "property" => 'padding-bottom',
                     "default" => '5', 
                    "type" => 'measurebox', 
                )
        );
        $link_padding_bottom->setParam('hide_wrapper_start', true);
        $link_padding_bottom->setUnits('px'); 
        
        
        $link_margin_left = $link_section->addStyleControl(
                 array(
                    "selector" => $link_selector,
                    "property" => 'margin-left',
                    "default" => '10',
                    "control_type" => 'measurebox', 
                )
        );
        $link_margin_left->setParam('hide_wrapper_end', true);
        $link_margin_left->setUnits('px');    
        
        $link_margin_right = $link_section->addStyleControl(
                array(
                    "selector" => $link_selector,
                    "property" => 'margin-right',
                    "default" => '10',
                    "control_type" => 'measurebox',
                )
        );
        $link_margin_right->setParam('hide_wrapper_start', true);
        $link_margin_right->setUnits('px'); 
        
        $link_margin_top = $link_section->addStyleControl(
                 array(
                    "selector" => $link_selector,
                    "property" => 'margin-top',
                     "default" => '5', 
                    "type" => 'measurebox', 
                )
        );
        $link_margin_top->setParam('hide_wrapper_end', true);
        $link_margin_top->setUnits('px'); 
            
        $link_margin_bottom = $link_section->addStyleControl(
                 array(
                    "selector" => $link_selector,
                    "property" => 'margin-bottom',
                     "default" => '5', 
                    "type" => 'measurebox', 
                )
        );
        $link_margin_bottom->setParam('hide_wrapper_start', true);
        $link_margin_bottom->setUnits('px'); 
        
        
        $link_section->addStyleControl(
            array(
                "selector" => $link_selector,
                "name" => __('Hover transition duration'),
                "property" => 'transition-duration',
                "control_type" => 'slider-measurebox',
            )
        )
        ->setUnits('ms','ms')
        ->setRange('0','600','1');
        
        
        
        $link_colors_section = $link_section->addControlSection("link_colors_section", __("Colors"), "assets/icon.png", $this);
        
        $link_colors_section->addStyleControl(
                 array(
                    "name" => 'Text Color',
                    "selector" => $link_selector,
                    "property" => 'color',
                )
        )->setParam('hide_wrapper_end', true);
        
        $link_colors_section->addStyleControl(
                array(
                    "name" => 'Hover Text Color',
                    "selector" => $link_hover_selector,
                    "property" => 'color',
                )
        )->setParam('hide_wrapper_start', true);
        
        $link_colors_section->addStyleControl(
                 array(
                    "name" => 'Focus Text Color',
                    "selector" => $link_focus_selector,
                    "property" => 'color',
                )
        )->setParam('hide_wrapper_end', true);
        
        $link_colors_section->addStyleControl(
                 array(
                    "name" => 'Active Text Color',
                    "selector" => $link_open_selector,
                    "property" => 'color',
                )
        )->setParam('hide_wrapper_start', true);

        $link_colors_section->addStyleControl(
            array(
               "name" => 'Current Text',
               "selector" => $current_link_selector,
               "property" => 'color',
           )
        )->setParam('hide_wrapper_end', true);
        
        $link_colors_section->addStyleControl(
                 array(
                    "name" => 'Current Background',
                    "selector" => $current_link_selector,
                    "property" => 'background-color',
                )
        )->setParam('hide_wrapper_start', true);

        $link_colors_section->addStyleControl(
            array(
               "name" => 'Current Ancestor Text',
               "selector" => $current_link_ancestor_selector,
               "property" => 'color',
           )
        )->setParam('hide_wrapper_end', true);
        
        $link_colors_section->addStyleControl(
                 array(
                    "name" => 'Current Ancestor bg',
                    "selector" => $current_link_ancestor_selector,
                    "property" => 'background-color',
                )
        )->setParam('hide_wrapper_start', true);
        
        
        
        
        
        $link_colors_section->addStyleControl(
                 array(
                    "name" => 'Background',
                    "selector" => $link_selector,
                    "property" => 'background-color',
                )
        )->setParam('hide_wrapper_end', true);
        
        $link_colors_section->addStyleControl(
                array(
                    "name" => 'Hover Background',
                    "selector" => $link_hover_selector,
                    "property" => 'background-color',
                )
        )->setParam('hide_wrapper_start', true);
        
        $link_colors_section->addStyleControl(
                 array(
                    "name" => 'Focus Background',
                    "selector" => $link_focus_selector,
                    "property" => 'background-color',
                )
        )->setParam('hide_wrapper_end', true);
            
        $link_colors_section->addStyleControl(
                 array(
                    "name" => 'Active Background',
                    "selector" => $link_open_selector,
                    "property" => 'background-color',
                )
        )->setParam('hide_wrapper_start', true);
        
        
        $link_section->addStyleControl(
            array(
                "name" => __('Horizonal alignment'),
                "property" => 'justify-content',
                "selector" => '.oxy-mega-menu_inner',
                "control_type" => 'dropdown',
            )
        )->setValue(array( 
            "flex-start" => "Flex start",
            "center" => "Center",
            "flex-end" => "Flex end",
            "space-between" => "Space between",
            "space-around" => "Space around",
            )
        )->setParam("description", __("Set a width on the mega menu before aligning the links inside"));   
        
        $link_section->borderSection('Borders', $link_selector,$this);
        $link_section->borderSection('Hover borders', $link_hover_selector,$this);
        $link_section->borderSection('Focus borders', $link_focus_selector,$this);
        $link_section->borderSection('Active borders', $link_open_selector,$this);
        $link_section->typographySection('Typography', $link_selector,$this);
        $link_section->typographySection('Current link', $current_link_selector,$this);
        $link_section->typographySection('Current link ancestor', $current_link_ancestor_selector, $this);
        
        
        
        
        /**
         * Icons
         */ 
        
        $icon_styles_section = $this->addControlSection("icon_styles_section", __("Icon styles"), "assets/icon.png", $this);
        $icon_selector = '.oxy-mega-dropdown_icon';
        $icon_svg_selector = '.oxy-mega-dropdown_icon svg';
        
        $icon_focus_selector = '.oxy-mega-dropdown_link.oxy-mega-dropdown_inner-focus .oxy-mega-dropdown_icon';
        $icon_hover_selector = '.oxy-mega-dropdown_link:hover .oxy-mega-dropdown_icon';
        $icon_active_selector = '.oxy-mega-dropdown_link.oxy-mega-dropdown_inner-open .oxy-mega-dropdown_icon';
        
        $icon_styles_section->addStyleControls(
            array(
                array(
                    "name" => __('Icon Size'),
                    "selector" => $icon_selector,
                    "property" => 'font-size',
                   
                ),
            )
        );
        
        
        $icon_styles_section->addStyleControl(
            array(
                "name" => __('Rotation'),
                "selector" => $icon_svg_selector,
                "property" => 'transform:rotate',
                "control_type" => 'slider-measurebox',
                "default" => "0"
            )
        )
        ->setUnits('deg','deg')
        ->setRange('-180','180');

        $icon_styles_section->addStyleControl(
            array(
                "name" => __('Rotation When Open'),
                "selector" => ".oxy-mega-dropdown_link.oxy-mega-dropdown_inner-open .oxy-mega-dropdown_icon svg",
                "property" => 'transform:rotate',
                "control_type" => 'slider-measurebox',
                "default" => "0"
                //"condition" => 'show_dropdown_icon=true',
            )
        )
        ->setUnits('deg','deg')
        ->setRange('-180','180');
        
        $icon_styles_section->addStyleControl(
            array(
                "selector" => $icon_svg_selector,
                "name" => __('Rotate Duration'),
                //"default" => "0",
                "property" => 'transition-duration',
                "control_type" => 'slider-measurebox',
            )
        )
        ->setUnits('ms','ms')
        ->setRange('0','1000','1');
        
        
        $icon_color_section = $icon_styles_section->addControlSection("icon_color_section", __("Colors"), "assets/icon.png", $this);
        
        
        $icon_color_section->addStyleControl(
                 array(
                    "name" => 'Color',
                    "selector" => $icon_selector,
                    "property" => 'color',
                )
        )->setParam('hide_wrapper_end', true);
        
        $icon_color_section->addStyleControl(
                array(
                    "name" => 'Hover Text Color',
                    "selector" => $icon_hover_selector,
                    "property" => 'color',
                )
        )->setParam('hide_wrapper_start', true);
        
        $icon_color_section->addStyleControl(
                 array(
                    "name" => 'Focus Text Color',
                    "selector" => $icon_focus_selector,
                    "property" => 'color',
                )
        )->setParam('hide_wrapper_end', true);
        
        $icon_color_section->addStyleControl(
                 array(
                    "name" => 'Active Text Color',
                    "selector" => $icon_active_selector,
                    "property" => 'color',
                )
        )->setParam('hide_wrapper_start', true);
        
        $icon_color_section->addStyleControl(
                 array(
                    "name" => 'Background',
                    "selector" => $icon_selector,
                    "property" => 'background-color',
                )
        )->setParam('hide_wrapper_end', true);
        
        $icon_color_section->addStyleControl(
                array(
                    "name" => 'Hover Background',
                    "selector" => $icon_hover_selector,
                    "property" => 'background-color',
                )
        )->setParam('hide_wrapper_start', true);
        
        $icon_color_section->addStyleControl(
                 array(
                    "name" => 'Focus Background',
                    "selector" => $icon_focus_selector,
                    "property" => 'background-color',
                )
        )->setParam('hide_wrapper_end', true);
            
        $icon_color_section->addStyleControl(
                 array(
                    "name" => 'Active Background',
                    "selector" => $icon_active_selector,
                    "property" => 'background-color',
                )
        )->setParam('hide_wrapper_start', true);
        
        
        
        $icon_spacing_section = $icon_styles_section->addControlSection("icon_spacing_section", __("Spacing"), "assets/icon.png", $this);
        
        $icon_spacing_section->addPreset(
            "padding",
            "icon_padding",
            __("Icon padding"),
            $icon_selector
        )->whiteList();
        
        
        $icon_spacing_section->addPreset(
            "margin",
            "icon_margin",
            __("Icon margin"),
            $icon_selector
        )->whiteList();
        
        
        $icon_styles_section->borderSection('Borders', $icon_selector,$this);
        $icon_styles_section->boxShadowSection('Shadows', $icon_selector,$this);
        
        
        
         /**
         * Flyout Menu
         */
        $flyout_menu_section = $this->addControlSection("flyout_menu_section", __("Flyout menu"), "assets/icon.png", $this);

        $flyout_menu_link_selector = '.oxy-mega-dropdown_flyout .menu-item > a';
        $flyout_sub_menu_icon_selector = '.oxy-mega-dropdown_flyout-icon';

        $flyout_menu_section->addPreset(
            "padding",
            "flying_link_padding",
            __("Link padding"),
            $flyout_menu_link_selector
        )->whiteList();


        $flyout_menu_section->addStyleControls(
            array(
                array(
                    "name" => __('Icon Size'),
                    "selector" => $flyout_sub_menu_icon_selector,
                    "property" => 'font-size',
                   
                ),
            )
        );
        
        
        $flyout_menu_section->addStyleControl(
            array(
                "name" => __('Icon rotation'),
                "selector" => $flyout_sub_menu_icon_selector,
                "property" => 'transform:rotate',
                "control_type" => 'slider-measurebox',
                //"default" => "0"
            )
        )
        ->setUnits('deg','deg')
        ->setRange('-180','180');

        $flyout_menu_section->addStyleControl(
            array(
                "name" => __('Icon rotation when open (mobile menu)'),
                "selector" => ".oxy-mega-menu_mobile .oxy-mega-dropdown_flyout-click-area[aria-expanded=true] .oxy-mega-dropdown_flyout-icon",
                "property" => 'transform:rotate',
                "control_type" => 'slider-measurebox',
                //"default" => "0"
            )
        )
        ->setUnits('deg','deg')
        ->setRange('-180','180');
        
        $flyout_menu_section->addStyleControl(
            array(
                "selector" => $flyout_sub_menu_icon_selector,
                "name" => __('Icon rotate duration'),
                "property" => 'transition-duration',
                "control_type" => 'slider-measurebox',
            )
        )
        ->setUnits('ms','ms')
        ->setRange('0','1000','1');


        $flyout_link_color_section = $flyout_menu_section->addControlSection("flyout_link_color_section", __("Colors"), "assets/icon.png", $this);

        $flyout_link_selector = '.oxy-mega-dropdown_flyout .menu-item > a';
        $flyout_link_hover_selector = '.oxy-mega-dropdown_flyout .menu-item > a:hover';
        $flyout_link_focus_selector = '.oxy-mega-dropdown_flyout .menu-item > a:focus';
        $flyout_link_current_selector = '.oxy-mega-dropdown_flyout .menu-item.current-menu-item > a';

        
        $flyout_link_color_section->addStyleControl(
                 array(
                    "name" => 'Text Color',
                    "selector" => $flyout_link_selector,
                    "property" => 'color',
                )
        )->setParam('hide_wrapper_end', true);
        
        $flyout_link_color_section->addStyleControl(
                array(
                    "name" => 'Hover Text Color',
                    "selector" => $flyout_link_hover_selector,
                    "property" => 'color',
                )
        )->setParam('hide_wrapper_start', true);
        
        $flyout_link_color_section->addStyleControl(
                 array(
                    "name" => 'Focus Text Color',
                    "selector" => $flyout_link_focus_selector,
                    "property" => 'color',
                )
        )->setParam('hide_wrapper_end', true);

        $flyout_link_color_section->addStyleControl(
            array(
               "name" => 'Current Text',
               "selector" => $flyout_link_current_selector,
               "property" => 'color',
           )
        )->setParam('hide_wrapper_start', true);
        
        $flyout_link_color_section->addStyleControl(
                 array(
                    "name" => 'Current Background',
                    "selector" => $flyout_link_current_selector,
                    "property" => 'background-color',
                )
        );

        
        
        $flyout_link_color_section->addStyleControl(
                 array(
                    "name" => 'Background',
                    "selector" => $flyout_link_selector,
                    "property" => 'background-color',
                )
        )->setParam('hide_wrapper_end', true);
        
        $flyout_link_color_section->addStyleControl(
                array(
                    "name" => 'Hover Background',
                    "selector" => $flyout_link_hover_selector,
                    "property" => 'background-color',
                )
        )->setParam('hide_wrapper_start', true);
        
        $flyout_link_color_section->addStyleControl(
                 array(
                    "name" => 'Focus Background',
                    "selector" => $flyout_link_focus_selector,
                    "property" => 'background-color',
                )
        );







        
        
        /**
         * Global
         */
        $global_dropdown_section = $this->addControlSection("global_dropdown_section", __("Dropdowns"), "assets/icon.png", $this);
        
        
        $global_dropdown_section->addStyleControls(
             array( 
                 array(
                    "selector" => $outer_selector,
                    "property" => 'font-size',
                     "default" => '14'
                ),
                 array(
                    "name" => 'Text Color',
                    "selector" => $outer_selector,
                    "property" => 'color',
                    "default" => 'inherit'
                ),
                 array(
                    "default" => '#fff',
                    "selector" => $container_selector,
                    "property" => 'background-color',
                ),
                 
                 
            )
        );
        
        
        
        /**
         * Spacing
         */
        $dropdown_layout_section = $global_dropdown_section->addControlSection("dropdown_layout_section", __("Inner Layout"), "assets/icon.png", $this);
        
        $dropdown_layout_section->flex($outer_selector, $this);

        $global_dropdown_section->typographySection('Current Links', '.oxy-mega-dropdown_menu .current-menu-item > a',$this);

        $global_dropdown_section->typographySection('Current Links Ancester', '.oxy-mega-dropdown_menu .current-menu-ancestor > a',$this);

        
        
         /**
         * Dropdown Spacing
         */
        $dropdown_spacing_section = $global_dropdown_section->addControlSection("dropdown_spacing_section", __("Size & Spacing"), "assets/icon.png", $this);
        
        
        
        
        $dropdown_spacing_section->addPreset(
            "padding",
            "inner_padding",
            __("Dropdown outer padding"),
            '.oxy-mega-dropdown_inner'
        )->whiteList();
        
        $dropdown_spacing_section->addPreset(
            "padding",
            "dropdown_padding",
            __("Content padding"),
            $dropdown_content_selector
        )->whiteList();
        
        $dropdown_spacing_section->addPreset(
            "padding",
            "column_padding",
            __("Columns padding"),
            '.ct-new-columns > .ct-div-block'
        )->whiteList();
        
        
        $global_dropdown_section->borderSection('Borders', $container_selector,$this);
        $global_dropdown_section->boxShadowSection('Shadows', $container_selector,$this);

        
        
        
        
        /**
         * animations
         */
        $animations_section = $this->addControlSection("animations_section", __("Animation"), "assets/icon.png", $this);
        
        
        
        $animations_section->addOptionControl(
            array(
                "type" => "dropdown",
                "name" => __("Container animation"),
                "slug" => "container_animation_reveal",
                "condition" => 'type=individual',
            )
        )->setValue(
           array( 
                "none" => "None",
                "fade" => "Fade",
                "transform" => "Transform",
           )
       )->setValueCSS( array(
            "fade" => "         .oxy-mega-dropdown_container {
                                    opacity: 0;
                                }
                                
                                .oxy-mega-dropdown_inner-open .oxy-mega-dropdown_container {
                                    opacity: 1;
                                }", 
            "transform"  => "   .oxy-mega-dropdown_container {
                                    opacity: 0;
                                    transform: scale(var(--dropdown-container-scale)) translate(var(--dropdown-container-translatex),var(--dropdown-container-translatey));
                                }
                                
                                .oxy-mega-dropdown_inner-open .oxy-mega-dropdown_container {
                                    opacity: 1;
                                    transform: none;
                                }",
        ) );
        
        
        $animations_section->addStyleControl(
            array(
                "name" => 'Transition in',
                "selector" => ".oxy-mega-dropdown_inner-open ". $container_selector,
                "property" => 'transition-duration',
                "control_type" => 'measurebox',
                "condition" => 'container_animation_reveal!=none&&type=individual'
            )
        )
        ->setUnits('ms','ms')->setParam('hide_wrapper_end', true);
        
        $animations_section->addStyleControl(
            array(
                "name" => 'Transition out',
                "selector" => $container_selector,
                "property" => 'transition-duration',
                "control_type" => 'measurebox',
                "condition" => 'container_animation_reveal!=none&&type=individual'
            )
        )
        ->setUnits('ms','ms')->setParam('hide_wrapper_start', true);
        
          $animations_section->addStyleControl( 
            array(
                "name" => __('Translate X'),
                "property" => '--dropdown-container-translatex',
                "control_type" => 'measurebox',
                "selector" => $container_selector,
                "condition" => 'container_animation_reveal=transform&&type=individual',
                "default" => '0',
            )
        )->setUnits('px')->setParam('hide_wrapper_end', true);
        
        $animations_section->addStyleControl( 
            array(
                "name" => __('Translate Y'),
                "property" => '--dropdown-container-translatey',
                "control_type" => 'measurebox',
                "selector" => $container_selector,
                "condition" => 'container_animation_reveal=transform&&type=individual',
                "default" => '0',
            )
        )->setUnits('px')->setParam('hide_wrapper_start', true);
        
        $animations_section->addStyleControl( 
            array(
                "name" => __('Scale'),
                "property" => '--dropdown-container-scale',
                "control_type" => 'slider-measurebox',
                "selector" => $container_selector,
                "condition" => 'container_animation_reveal=transform&&type=individual',
                "default" => '1',
            )
        )
        ->setRange('0.8','1.2','.01');
        
       
        
        
        
        
        $animations_section->addCustomControl(
            '<div style="color: #fff; opacity: .1;"><hr></div>','description');
        
         
        
        
        
        
        $animations_section->addOptionControl(
            array(
                "type" => "dropdown",
                "name" => __("Content animation"),
                "slug" => "content_animation_reveal",
            )
        )->setValue(
           array( 
                "none" => "None",
                "fade" => "Fade",
                "transform" => "Transform" 
           )
       )->setValueCSS( array(
            "translate_x"  => " .oxy-mega-dropdown_content {
                                    opacity: 0;
                                    transform: translateX(var(--dropdown-content-effect));
                                }
                                
                                .oxy-mega-dropdown_inner-open .oxy-mega-dropdown_content {
                                    opacity: 1;
                                    transform: none;
                                }",
            "translate_y"  => " .oxy-mega-dropdown_content {
                                    opacity: 0;
                                    transform: translateY(var(--dropdown-content-effect));
                                }
                                
                                .oxy-mega-dropdown_inner-open .oxy-mega-dropdown_content {
                                    opacity: 1;
                                    transform: none;
                                }",
            "fade" => "         .oxy-mega-dropdown_content {
                                    opacity: 0;
                                }
                                
                                .oxy-mega-dropdown_inner-open .oxy-mega-dropdown_content {
                                    opacity: 1;
                                }",   
            "transform" => "     .oxy-mega-dropdown_content {
                                    opacity: 0;
                                    transform: scale(var(--dropdown-content-scale)) translate(var(--dropdown-content-translatex),var(--dropdown-content-translatey));
                                }
                                
                                .oxy-mega-dropdown_inner-open .oxy-mega-dropdown_content {
                                    opacity: 1;
                                    transform: none;
                                }",   
        ) );
        
        
        $animations_section->addStyleControl(
            array(
                "name" => 'Transition in',
                "selector" => ".oxy-mega-dropdown_inner-open ". $dropdown_content_selector,
                "property" => 'transition-duration',
                "control_type" => 'measurebox',
                "condition" => 'content_animation_reveal!=none'
            )
        )
        ->setUnits('ms','ms')->setParam('hide_wrapper_end', true);
        
        $animations_section->addStyleControl(
            array(
                "name" => 'Transition out',
                "selector" => $dropdown_content_selector,
                "property" => 'transition-duration',
                "control_type" => 'measurebox',
                "condition" => 'content_animation_reveal!=none'
            )
        )
        ->setUnits('ms','ms')->setParam('hide_wrapper_start', true);
            
        
        $animations_section->addStyleControl( 
            array(
                "name" => __('Translate X'),
                "property" => '--dropdown-content-translatex',
                "control_type" => 'measurebox',
                "selector" => $dropdown_content_selector,
                "condition" => 'content_animation_reveal=transform'
            )
        )
        //->setRange('-40','40','1')
        ->setUnits('px')->setParam('hide_wrapper_end', true);
        
        $animations_section->addStyleControl( 
            array(
                "name" => __('Translate Y'),
                "property" => '--dropdown-content-translatey',
                "control_type" => 'measurebox',
                "selector" => $dropdown_content_selector,
                "condition" => 'content_animation_reveal=transform'
            )
        )
        //->setRange('-40','40','1')
        ->setUnits('px')->setParam('hide_wrapper_start', true);
        
        
        $animations_section->addStyleControl( 
            array(
                "name" => __('Scale'),
                "property" => '--dropdown-content-scale',
                "control_type" => 'slider-measurebox',
                "selector" => $dropdown_content_selector,
                "condition" => 'content_animation_reveal=transform',
                "default" => '1',
            )
        )
        ->setRange('0.8','1.2','.01');
        
        
        
        
            
        
         /**
         * Config
         */
        $config_section = $this->addControlSection("config_section", __("User Interaction"), "assets/icon.png", $this);
        
        $config_section->addOptionControl(
            array(
                "type" => 'slider-measurebox',
                "name" => __('Open delay'),
                "slug" => 'open_delay',
                "value" => '0',
            )
        )->setRange('0','500','1')
         ->setUnits('ms','ms');
        
        $config_section->addOptionControl(
            array(
                "type" => 'slider-measurebox',
                "name" => __('Close delay'),
                "slug" => 'close_delay',
                "value" => '50',
            )
        )->setRange('0','500','1')
         ->setUnits('ms','ms');
        
        
        $config_section->addOptionControl(
            array(
                "type" => "buttons-list",
                "name" => __("Allow mouseover to reveal dropdowns"),
                "slug" => "maybe_mouseover",
                "default" => 'true',
            )
        )->setValue(
           array( 
                "true" => "Enable", 
                "false" => "Disable",
               
           )
       );
        
        
        $config_section->addOptionControl(
            array(
                "type" => "buttons-list",
                "name" => __("Allow mouseover to open tabs in dropdowns"),
                "slug" => "maybe_hovertabs",
                "default" => 'true',
            )
        )->setValue(
           array( 
                "true" => "Enable", 
                "false" => "Disable",
               
           )
       );
        
       $config_section->addOptionControl(
            array(
                "type" => "buttons-list",
                "name" => __("Clicking droplink link to hold dropdown open"),
                "slug" => "maybe_hold_menu",
                "default" => 'enable',
            )
        )->setValue(
            array( 
                    "enable" => "Enable", 
                    "disable" => "Disable",
            )
        )->setValueCSS( array(
            "disable"  => " .oxy-mega-dropdown {
                                cursor: pointer;
                            }
                            
                            .oxy-mega-dropdown_link[href^='#']:not(.oxy-mega-menu_mobile .oxy-mega-dropdown_link):not(.oxy-mega-dropdown_just-link) {
                                pointer-events: none;
                            }
                            
                            .oxy-mega-dropdown_inner {
                                cursor: auto;
                            }"
                        
        ) )->setParam("description", __("Disable to close dropdown whenever cursor is moved away."));
        
        $config_section->addOptionControl(
            array(
                "type" => "buttons-list",
                "name" => __("Close mega menu if any hash links are clicked"),
                "slug" => "maybe_hash_close",
                "default" => 'false',
            )
        )->setValue(
            array( 
                    "true" => "Enable", 
                    "false" => "Disable",
            )
        );

        
        
        
        /**
         * Responsive
         */
        $responsive_section = $this->addControlSection("responsive_section", __("Mobile Menu"), "assets/icon.png", $this);
        

        $responsive_section->addOptionControl(
            array(
                "name" => __('Vertical mobile menu below'),
                "slug" => 'mobile_menu_below',
                "type" => 'medialist'
            )
        );
        
        
        $responsive_section->addOptionControl(
            array(
                "type" => "buttons-list",
                "name" => __("Force columns in dropdowns to stack"),
                "slug" => "force_column_collapse",
                "default" => 'false',
            )
        )->setValue(
           array( 
                "true" => "Enable", 
                "false" => "Disable",
               
           )
       )->setValueCSS( array(
            "true"  => "",
        ) );
        
        
        $responsive_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Open / Close trigger'),
                "slug" => 'trigger',
                "default" => '.oxy-burger-trigger',
            )
        );
        
        $responsive_section->addOptionControl(
           array(
                "type" => 'slider-measurebox',
                "name" => __('Slide duration'),
                "slug" => "slide_duration",
                "default" => "300",
            )
        )
        ->setUnits('ms','ms')
        ->setRange(0, 1000, 1);
        
        $responsive_section->addStyleControl( 
            array(
                "control_type" => 'colorpicker',
                "name" => __('Mobile menu background'),
                "property" => '--megamenu-responsive-background',
                "selector" => '',
            )
        );
        
        
        $responsive_section->addOptionControl(
            array(
                "type" => "buttons-list",
                "name" => __("Ensure dropdowns scrollable on mobile sticky header"),
                "slug" => "dropdown_scroll",
                "default" => 'disable',
            )
        )->setValue(
           array( 
                "enable" => "Enable", 
                "disable" => "disable",
           )
       );
        
        
        $responsive_section->addStyleControl( 
            array(
                "name" => __("What is the height of the mobile sticky header?"),
                "property" => '--sticky-header-height',
                "selector" => "",
                "value" => "80",
                "unit" => "px",
                "control_type" => 'measurebox',
                "condition" => 'dropdown_scroll=enable',
            )
        );
        
        
        $responsive_section->addOptionControl(
            array(
                "type" => "buttons-list",
                "name" => __("Site scrolling when dropdowns open"),
                "slug" => "maybe_body_scroll",
                "default" => 'false',
            )
        )->setValue(
           array( 
                "false" => "Enable", 
                "true" => "Disable",
           )
       );
        
        
        $responsive_section->addOptionControl(
            array(
                "type" => "buttons-list",
                "name" => __("Add support for custom headers"),
                "slug" => "maybe_custom_header",
                "default" => 'false',
            )
        )->setValue(
           array( 
                "true" => "Enable", 
                "false" => "Disable",
           )
       )->setParam("description", __("Be sure to add the 'header' tag on your custom header"));

       $responsive_section->addOptionControl(
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


       $responsive_link_layout_section = $responsive_section->addControlSection("responsive_link_layout_section", __("Link layout"), "assets/icon.png", $this);
       $responsive_link_layout_section->flex('.oxy-mega-dropdown .oxy-mega-dropdown_link', $this);

        
    }
    
    
    function customCSS($options, $selector) {
        
        $css = "";
        
        if( ! $this->css_added ) {
    
            $css .= ".oxy-mega-menu {
                        backface-visibility: hidden;
                        -webkit-backface-visibility: hidden;
                     }
            
                    .oxygen-builder-body .oxy-mega-menu {
                        z-index: 999999;
                    }

                    .oxy-mega-dropdown {
                        height: 100%;
                    }
                    
                    .oxy-mega-menu .screen-reader-text {
                        clip: rect(1px,1px,1px,1px);
                        height: 1px;
                        overflow: hidden;
                        position: absolute!important;
                        width: 1px;
                        word-wrap: normal!important;
                    }

                    .oxy-mega-menu_inner {
                        display: flex;
                        list-style: none;
                        margin: 0;
                        padding: 0;
                        z-index: 15;
                        width: 100%;
                    }

                    .oxy-mega-dropdown {
                        list-style: none;
                        padding: 0;
                        margin: 0;
                    }
                    
                    .oxy-mega-dropdown_container {
                        background-color: #fff;
                    }
                    
                    .oxy-mega-dropdown_flyout .sub-menu {
                        background-color: #fff;
                    }

                    .oxy-mega-dropdown_link {
                        display: flex;
                        align-items: center;
                        color: inherit;
                        padding: 5px 10px;
                        position: relative;
                        outline: none;
                        cursor: pointer;
                    }

                    .oxy-mega-dropdown_link-text {
                        white-space: nowrap;
                    }

                    .oxy-mega-dropdown > a.open {
                        z-index: 1;
                    }

                    .oxy-mega-dropdown_inner {
                        display: block;
                        position: absolute;
                        left: 0;
                        visibility: hidden;
                        opacity: 0;
                        transition: all .5s ease;
                        -webkit-transition: all .5s ease;
                        pointer-events: none;
                    }

                    .oxy-header-container.oxy-mega-dropdown_content {
                        padding-left: 0;
                        padding-right: 0;
                        flex-wrap: wrap;
                    }

                    .oxy-mega-dropdown .oxy-header-container {
                        height: auto;
                    }

                    .oxy-mega-dropdown_inner.oxy-mega-dropdown_inner-open {
                        visibility: visible;
                        opacity: 1;
                        pointer-events: auto;
                    }

                    .oxy-mega-dropdown_container {
                        /* overflow: hidden; */
                        --dropdown-container-translatey: 0;
                        --dropdown-container-scale: 1;
                        --dropdown-container-translatex: 0;
                        will-change: opacity, transform;
                    }
                    
                    .oxy-mega-dropdown_content {
                        --dropdown-content-translatey: 0;
                        --dropdown-content-scale: 1;
                        --dropdown-content-translatex: 0;
                        backface-visibility: hidden;
                        -webkit-backface-visibility: hidden;
                    }
                    
                    .oxy-mega-menu_inner .ct-text-link {
                        display: flex;
                        align-items: center;
                        outline: none;
                    }

                    /*
                    .oxy-mega-dropdown_inner ul {
                        display: inline-block;
                        vertical-align: top;
                        margin: 0 1em 0 0;
                        padding: 0;
                    }


                    .oxy-mega-dropdown_inner li {
                        display: block;
                        list-style-type: none;
                        margin: 0;
                        padding: 0;
                    }   
                    */
                    
                    .oxy-mega-dropdown_icon {
                        display: flex;
                    }

                    .oxy-mega-dropdown_icon svg {
                        height: 1em;
                        width: 1em;
                        fill: currentColor;
                    }
                    

                    .oxygen-builder-body .oxy-mega-dropdown.ct-active {
                        z-index: 99;
                    }

                    .oxygen-builder-body .oxy-mega-dropdown.ct-active .oxy-mega-dropdown_inner-open {
                        z-index: 999999;
                    }
                    
                    
                    .oxygen-builder-body .oxy-mega-dropdown_inner-builder-hide .oxy-mega-dropdown_inner.oxy-header-container {
                        max-height: 0!important;
                        height: 0!important;
                    }
                    
                    .oxygen-builder-body .oxy-header-container_mega-menu .oxy-header-left:empty,
                    .oxygen-builder-body .oxy-header-container_mega-menu .oxy-header-right:empty {
                        min-width: 0;
                    }
                    
                    .oxygen-builder-body .oxy-mega-dropdown_content:empty {
                        min-height: 80px;
                        min-width: 300px;
                    }
                    
                    .oxygen-builder-body .oxy-mega-menu_inner:empty {
                        min-width: 200px;
                        min-height: 50px;
                    }
                    
                    .oxy-mega-dropdown_menu,
                    .oxy-mega-dropdown_menu .sub-menu {
                        padding: 0;
                        margin: 0;
                        list-style-type: none;
                        width: 100%;
                    }
                    
                    .oxy-mega-dropdown_menu a {
                        color: inherit;
                        padding: 15px 20px;
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                    }
                    
                    .oxy-mega-dropdown_menu .menu-item-has-children {
                        position: relative;
                    }
                    
                    .oxy-mega-dropdown_menu .sub-menu {
                        left: 100%;
                        top: 0;
                        position: absolute;
                        width: 100%;
                        opacity: 0;
                        visibility: hidden;
                    }
                    
                    .oxy-mega-dropdown_menu .menu-item-has-children:hover > .sub-menu,
                    .oxy-mega-dropdown_menu .menu-item-has-children:focus-within > .sub-menu,
                    .oxy-mega-dropdown_menu .menu-item-has-children > a.oxy-mega-menu_inner-focus + .sub-menu,
                    .oxy-mega-dropdown_menu .menu-item-has-children > a.oxy-mega-menu_inner-hover + .sub-menu {
                        opacity: 1;
                        visibility: visible;
                    }
                    
                    .oxy-header-container.oxy-mega-dropdown_flyout {
                        padding-left: 0;
                        padding-right: 0;
                    }
                    
                    
                    .oxy-mega-dropdown_flyout-click-area {
                        box-shadow: none;
                        border: none;
                        background: none;
                        color: inherit;
                    }
                    
                    .oxy-mega-dropdown_flyout-icon {
                        height: 1em;
                        width: 1em;
                        fill: currentColor;
                        transform: rotate(-90deg);
                    }
                    
                    .oxy-mega-dropdown_link-label-inner {
                        background: #db4848;
                        position: relative;
                        font-size: .6em;
                        font-weight: 700;
                        border-radius: 2px;
                        padding: .25em .5em;
                    }
                    
                    .oxy-mega-dropdown_link-label {
                        position: absolute;
                        top: 7px;
                    }
                    
                    .oxygen-builder-body .oxy-mega-dropdown_link {
                        pointer-events: auto!important;
                    }
                    
                
                ";
            
                $this->css_added = true;
            
            }
        
            
            $css .= ".oxygen-builder-body $selector .oxy-mega-dropdown_inner.oxy-header-container.oxy-mega-dropdown_inner-open {
                        max-height: var(--expanded-height);
                        height: var(--expanded-height);
                    }";
        
        
            if ((isset($options["oxy-mega-menu_mobile_menu_below"]) && $options["oxy-mega-menu_mobile_menu_below"]!="never")) {
                $max_width = oxygen_vsb_get_media_query_size($options["oxy-mega-menu_mobile_menu_below"]);
                $min_width = oxygen_vsb_get_media_query_size($options["oxy-mega-menu_mobile_menu_below"]) + 1;
                $css .= "@media (min-width: {$min_width}px) {
                    
                            $selector {
                                display: flex;
                                backface-visibility: hidden;
                                -webkit-backface-visibility: hidden;
                            }
                            
                            .oxygen-builder-body $selector {
                                display: flex!important;
                            }
                
                        }
                
                        @media (max-width: {$max_width}px) {
                        
                            $selector {
                                background-color: var(--megamenu-responsive-background);
                            }
                
                            $selector .oxy-mega-menu_inner {
                                flex-direction: column;
                                width: 100%;
                            }
                            
                            $selector .oxy-mega-dropdown_link {
                                display: flex;
                                justify-content: space-between;
                                width: 100%;
                            }
                            
                            $selector .oxy-mega-dropdown_inner {
                                position: static;
                                opacity: 1;
                                visibility: visible;
                                transform: none;
                                transition-duration: 0s;
                            }
                            
                            $selector .oxy-mega-dropdown_content {
                                transform: none;
                                opacity: 1;
                            }
                            
                            $selector .oxy-mega-dropdown .oxy-mega-dropdown_link + .oxy-mega-dropdown_inner.oxy-header-container {
                                display: none;
                                width: 100%;
                                max-width: 100%;
                                pointer-events: auto;
                                padding-left: 0;
                                padding-right: 0;
                                max-height: none;
                                height: auto;
                                transform: none;
                                left: 0;
                                right: 0;
                            }
                            
                            $selector .oxy-mega-dropdown .oxy-mega-dropdown_link[data-expanded=enable] + .oxy-mega-dropdown_inner.oxy-header-container {
                                display: block;
                            }
                            
                            .oxygen-builder-body $selector .oxy-mega-dropdown .oxy-mega-dropdown_link + .oxy-mega-dropdown_inner.oxy-header-container.oxy-mega-dropdown_inner-open {
                                display: block;
                            }
                            
                            $selector .oxy-mega-dropdown_menu .sub-menu {
                                display: none;
                                position: static;
                                transition: none;
                                visibility: visible;
                                opacity: 1;
                            }
                            
                            $selector .oxy-mega-dropdown_inner {
                                display: none;
                                width: 100%;
                                max-width: 100%;
                                pointer-events: auto;
                                padding-top: 0;
                            }
                            
                            $selector .oxy-mega-dropdown_container {
                                box-shadow: none;
                                transition: none;
                                transform: none;
                                visibility: visible;
                                opacity: 1;
                                
                            }
                            
                            $selector.oxy-mega-menu {
                                display: none;
                                backface-visibility: unset;
                                -webkit-backface-visibility: unset;
                                position: absolute;
                                width: 100%;
                                left: 0;
                                top: 100%;
                                z-index: 2;
                            }
                            
                            $selector .oxy-mega-dropdown {
                                width: 100%;
                            }
                            
                            
                        }";
                
                
                if ((isset($options["oxy-mega-menu_force_column_collapse"]) && $options["oxy-mega-menu_force_column_collapse"] === "true")) {
                    
                    
                    $css .= "@media (max-width: {$max_width}px) {
                    
                                $selector .ct-new-columns {
                                    flex-direction: column;
                                }
                                
                                $selector .ct-new-columns > .ct-div-block {
                                    width: 100%;
                                }
                    
                            }";
                }
                
                
                if ((isset($options["oxy-mega-menu_dropdown_scroll"]) && $options["oxy-mega-menu_dropdown_scroll"] === "enable")) {
                
                
                        $css .= "@media (max-width: {$max_width}px) {
                    
                                    $selector {
                                         background: none!important; 
                                    }

                                  .oxy-sticky-header-active $selector {
                                      height: calc(100vh - var(--sticky-header-height));
                                      overflow-y: auto;
                                  }

                                  $selector .oxy-mega-dropdown {
                                    background-color: var(--megamenu-responsive-background);
                                  }

                                  .oxy-sticky-header-active $selector .oxy-mega-menu_mobile .oxy-mega-dropdown {
                                      height: auto;
                                  }
                    
                            }";
                    
                }
                
                if ((isset($options["oxy-mega-menu_maybe_body_scroll"]) && $options["oxy-mega-menu_maybe_body_scroll"] === "true")) {
                
                
                        $css .= "@media (max-width: {$max_width}px) {
                    
                                  $selector {
                                      height: calc(100vh - var(--sticky-header-height));
                                      overflow-y: auto;
                                  }
                    
                            }";
                    
                }
                
                if ((isset($options["oxy-mega-menu_maybe_custom_header"]) && $options["oxy-mega-menu_maybe_custom_header"] === "true")) {
                
                
                        $css .= "@media (max-width: {$max_width}px) {
                    
                                  header {
                                      position: relative;
                                  }
                    
                            }";
                    
                }
                
                
                
                
            }
        
        
        return $css;
    }
    
    
    
    function output_js() { 
        
        wp_enqueue_script( 'extras_megamenu', plugin_dir_url(__FILE__) . 'assets/accessible-megamenu.js', '', '1.0.0' );
        wp_enqueue_script( 'extras_megamenu-init', plugin_dir_url(__FILE__) . 'assets/megamenu-init.js', '', '1.0.2' );

    }
}

new ExtraMegaMenu();




// Mega Dropdown

class ExtraMegaDropdown extends OxygenExtraElements {
        
     function name() {
        return 'Mega Dropdown';
    }
    
    function icon() {
        return plugin_dir_url(__FILE__) . 'assets/icons/'.basename(__FILE__, '.php').'.svg';
    }
    
    function tag() {
        return array('default' => 'li');
    }
    
    function extras_button_place() {
        return "interactive";
    }
    
    function init() {
        
        add_filter("oxy_allowed_empty_options_list", array( $this, "allowedEmptyOptions") );
        
        $this->enableNesting();
        
        
    }
    
    // Element options
    function options(){

        return array(
            "only_parent" => "oxy-mega-menu",
        );

    }
    
    
    function render($options, $defaults, $content) {

        $this->dequeue_scripts_styles();
        
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
        
        
        $link_text = html_entity_decode( $dynamic($options['link_text']) );
        
        $dropdown_icon = isset( $options['dropdown_icon'] ) ? esc_attr($options['dropdown_icon']) : "";
        
        //$hashlink = isset( $options['hashlink'] ) ? esc_attr($options['hashlink']) : "";
        $hashlink = isset( $options['hashlink'] ) ? $dynamic($options['hashlink']) : '';
        $flyout_class = ('menu' === esc_attr($options['dropdown_elements'])) ? ' oxy-mega-dropdown_flyout' : '';
        
        $label_text = isset( $options['label_text'] ) ? esc_attr($options['label_text']) : "";
        $link_class = ('none' === esc_attr($options['dropdown_elements'])) ? "oxy-mega-dropdown_link oxy-mega-dropdown_just-link" : "oxy-mega-dropdown_link";

        $link_rel_attr = isset( $options['link_rel_attr'] ) ? esc_attr($options['link_rel_attr']) : '';
        $link_target_attr = isset( $options['link_target_attr'] ) ? esc_attr($options['link_target_attr']) : '';
        
        $maybe_disable_link = isset( $options['maybe_disable_link'] ) ? esc_attr($options['maybe_disable_link']) : "";
        
        $maybe_expanded = isset( $options['maybe_expanded'] ) ? esc_attr($options['maybe_expanded']) : "";
        
        $menu_name  = isset( $options['menu_name'] ) ? esc_attr($options['menu_name']) : '';
        
        $builder_clicks = (defined('OXY_ELEMENTS_API_AJAX') && OXY_ELEMENTS_API_AJAX) ? 'onclick="extrasOpenDropdown.call(this)" ' : '';
        
        $current_page_class = ( $_SERVER['REQUEST_URI'] === $hashlink ) ? 'oxy-mega-dropdown_link-current' : '';
        
        global $oxygen_svg_icons_to_load;
        $oxygen_svg_icons_to_load[] = $dropdown_icon;
        
        $output = '';
        
        $output .= '<a '.$builder_clicks.' ';

        if ('none' === esc_attr($options['dropdown_elements'])) {

            $output .= $link_rel_attr ? 'rel="' . $link_rel_attr . '" ' : '';

            $output .= $link_target_attr ? 'target="' . $link_target_attr . '" ': '';  

        }
        
        $output .= 'href="' . $hashlink . '" ';
        
        $output .= 'class="'. $link_class . ' ' . $current_page_class .'" ';
        
        $output .= 'data-disable-link="'. $maybe_disable_link .'" ';
        
        $output .= 'data-expanded="'. $maybe_expanded .'" ';
        
        $output .= '>';
        
        
        
        $output .= '<span class="oxy-mega-dropdown_link-text">'. $link_text .'</span>';
        
        if ('true' === esc_attr($options['maybe_label'])) {
        
            $output .= '<span class="oxy-mega-dropdown_link-label"><span class="oxy-mega-dropdown_link-label-inner">'. $label_text .'</span></span>';
            
        }
        
        if ('none' !== esc_attr($options['dropdown_elements'])) {
        
            $output .= '<span class="oxy-mega-dropdown_icon"><svg id="icon' . esc_attr($options['selector']) . '"><use xlink:href="#' . $dropdown_icon .'"></use></svg></span>';
            
        }
        
        $output .= '</a>';
        
        
        if ('none' !== esc_attr($options['dropdown_elements'])) {
            
            $output .= '<div class="oxy-mega-dropdown_inner oxy-header-container'. $flyout_class .'" ';
        
            $output .= 'data-icon="'. $dropdown_icon .'"';

            $output .= '>';
        
            $output .= '<div class="oxy-mega-dropdown_container"><div class="oxy-inner-content oxy-mega-dropdown_content oxy-header-container">';


                if ('menu' !== esc_attr($options['dropdown_elements'])) { 

                    if ($content) {

                        if ( function_exists('do_oxygen_elements') ) {
                            $output .=  do_oxygen_elements($content); 
                        }
                        else {
                            $output .=  do_shortcode($content); 
                        }

                    } 

                 } else {

                    ob_start();

                    wp_nav_menu( array(
                        'menu'           => $menu_name,
                        'menu_class'     => 'oxy-mega-dropdown_menu',
                        'container'		 => '',
                    ) );

                    $nav_menu_output = ob_get_clean();

                    $output .= $nav_menu_output;

                }    

            $output .= '</div></div></div>';
            
        }
        
        echo $output;
        
        
        
        
       if( method_exists('OxygenElement', 'builderInlineJS') ) {
        
             $this->El->builderInlineJS(
                "function extrasOpenDropdown() {
                      jQuery(this).toggleClass('oxy-mega-dropdown_inner-open');
                      jQuery(this).next('.oxy-mega-dropdown_inner').toggleClass('oxy-mega-dropdown_inner-open');
                      jQuery(this).closest('.oxy-mega-dropdown').siblings('.oxy-mega-dropdown').find('.oxy-mega-dropdown_inner').removeClass('oxy-mega-dropdown_inner-open');
                      jQuery(this).closest('.oxy-mega-dropdown').siblings('.oxy-mega-dropdown').find('.oxy-mega-dropdown_link').removeClass('oxy-mega-dropdown_inner-open');

                      if ((jQuery(this).next('.oxy-mega-dropdown_inner').hasClass('oxy-mega-dropdown_inner-open')) && !(jQuery(this).hasClass('oxy-mega-dropdown_just-link'))) {
                        jQuery(this).closest('.oxy-mega-menu_inner').removeClass('oxy-mega-dropdown_inner-builder-hide');
                      } else {
                        jQuery(this).closest('.oxy-mega-menu_inner').addClass('oxy-mega-dropdown_inner-builder-hide');
                      }

                      
                }
                
                jQuery('#%%ELEMENT_ID%% .oxy-mega-dropdown_menu .menu-item-has-children > a', 'body').each(function(){
                        jQuery(this).append('<button tabindex=\"-1\" aria-expanded=\"false\" aria-pressed=\"false\" class=\"oxy-mega-dropdown_flyout-click-area\"><svg class=\"oxy-mega-dropdown_flyout-icon\"><use xlink:href=\"#%%dropdown_icon%%\"></use></svg><span class=\"screen-reader-text\">Submenu</span></button>');

                        });
             ");
            
        }
        
        
    }

    function class_names() {
        return array();
    }
    
   

    function controls() {
        
        
        
        $this->addOptionControl(
            array(
                "type" => "dropdown",
                "name" => __("Dropdown contents"),
                "slug" => "dropdown_elements",
                "default" => 'custom',
            )
        )->setValue(
           array( 
                "custom" => "Custom (place elements inside)", 
                "menu" => "WP menu (flyout menu)",
                "none" => "Link (No dropdown)"
               
           )
       )->setValueCSS( array(
            "menu" => " .oxy-mega-dropdown_flyout .oxy-mega-dropdown_container .oxy-mega-dropdown_content {
                            padding: 0;
                        }
                        
                        .oxy-mega-dropdown_content > *:not(.oxy-mega-dropdown_menu) {
                            display: none;
                        }",
            "custom" => " .oxy-mega-dropdown_flyout .oxy-mega-dropdown_container .oxy-mega-dropdown_content {
                            padding: 0;
                        }
                        .oxy-mega-dropdown_menu {
                            display: none;
                        }",
            "none" => " > *:first-child:not(.oxy-mega-dropdown_link) {
                            display: none;
                        }",
            
        ) );
        
        
         /**
         * Menu Dropdown
         */ 
        $menus = get_terms( 'nav_menu', array( 'hide_empty' => true ) ); 

        $menus_list = array(); 
        foreach ( $menus as $key => $menu ) {
            $menus_list[$menu->term_id] = $menu->name;
        } 

        $this->addOptionControl(
            array(
                "type" => "dropdown",
                "name" => "Menu",
                "slug" => "menu_name",
                "condition" => 'dropdown_elements=menu'
            )
        )->setValue($menus_list)->rebuildElementOnChange();
        
        
        
        $this->addOptionControl(
            array(
                "type" => "buttons-list",
                "name" => __("Submenu align"),
                "slug" => "submenu_align",
                "default" => 'right',
                "condition" => 'dropdown_elements=menu'
            )
        )->setValue(
           array( 
                "left" => "Left", 
                "right" => "Right",
               
           )
       )->setValueCSS( array(
            "left" => ".oxy-mega-dropdown_menu .sub-menu {
                            left: -100%;
                        }",
                        
        ) )->whiteList();
        
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Link Text'),
                "slug" => 'link_text',
                "default" => 'Link Text',
                //"base64" => true,
                "css" 	=> false
            )
        )->setParam('dynamicdatacode', '<div optionname="\'oxy-mega-dropdown_link_text\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');
        
       
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('URL'),
                "slug" => 'hashlink',
                "default" => '#dropdown',
                "base64" => true
            )
        )->setParam('dynamicdatacode', '<div optionname="\'oxy-mega-dropdown_hashlink\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesLinkMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');
        
        
        $this->addOptionControl(
            array(
                "type" => "buttons-list",
                "name" => __("Disable link URL on mobile menu"),
                "slug" => "maybe_disable_link",
                "default" => 'disable',
                "condition" => 'dropdown_elements!=none'
            )
        )->setValue(
           array( 
                "enable" => "True", 
                "disable" => "False",
               
           )
       );

       $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Rel'),
                "slug" => 'link_rel_attr',
                "condition" => 'dropdown_elements=none'
            )
        );

        $this->addOptionControl(
            array(
                "type" => "dropdown",
                "name" => __('Target'),
                "slug" => 'link_target_attr',
                "condition" => 'dropdown_elements=none'
            )
        )->setValue(
           array( 
                "_self" => "_self", 
                "_blank" => "_blank",
                "_top" => "_top",
                "_parent" => "_parent",
           )
        );
        
        
        $this->addOptionControl(
            array(
                "type" => "buttons-list",
                "name" => __("Expanded by default on mobile menu"),
                "slug" => "maybe_expanded",
                "default" => 'disable',
                "condition" => 'dropdown_elements!=none'
            )
        )->setValue(
           array( 
                "enable" => "True", 
                "disable" => "False",
               
           )
       );
        
        
        
        $this->addCustomControl(
            '<div style="color: #fff; line-height: 1.3; font-size: 13px;">Settings for this specific dropdown</div><hr style="opacity: .2">','description');
        
        
        
        
       
        
        
        
        
      
        $outer_selector = '.oxy-mega-dropdown_inner';
        $open_inner_selector = '.open.oxy-mega-dropdown_inner';
        $dropdown_content_selector = '.oxy-mega-dropdown_content';
        
        
        
        /**
         * Layout / Spacing
         */
        $dropdown_styles_section = $this->addControlSection("dropdown_styles_section", __("Dropdown Styles"), "assets/icon.png", $this);
        
        
         $dropdown_styles_section->addCustomControl(
            '<div style="color: #fff; line-height: 1.3; font-size: 13px;">These styles will only apply to this individual dropdown. For styles across all dropdowns, set from the main mega menu settings</div>','description');
        
        
        
        
        $dropdown_styles_section->addOptionControl(
            array(
                "type" => "dropdown",
                "name" => __("Dropdown width"),
                "slug" => "dropdown_width",
                "default" => 'inherit',
                //"condition" => 'dropdown_elements=custom'
            )
        )->setValue(
           array( 
                "a_inherit" => 'Inherit',
                "b_none" => "Custom width",
                "c_content" => "Header row width (content)",
                "d_header" => "Header row width (container)",
                "e_full" => "Full width",
           )
       )->setValueCSS( array(
            "d_header"  => " {
                                position: static!important;
                            }
            
                            .oxy-mega-dropdown_link + .oxy-mega-dropdown_inner {
                                max-width: 100%;
                                width: 100%;
                            }
                            ",
            "e_full"  => " {
                                position: static!important;
                            }
                            
                            .oxy-mega-dropdown_link + .oxy-mega-dropdown_inner.oxy-header-container {
                               max-width: 100%;
                               width: 100%;
                               padding-left: 0;
                               padding-right: 0;
                        }
                        .oxy-mega-dropdown_link + .oxy-mega-dropdown_inner {
                               max-width: 100%;
                               width: 100%;
                               padding-left: 0;
                               padding-right: 0;
                        }
                        
                        .oxy-mega-dropdown_inner .oxy-header-container.oxy-mega-dropdown_content {
                            max-width: 100%;
                            width: var(--dropdown-content-width);
                        }",
            
            "c_content"  => 
                        " {
                            position: static!important;
                          }
                        .oxy-mega-dropdown_link + .oxy-mega-dropdown_inner.oxy-header-container {
                           max-width: 100%;
                           width: 100%;
                           padding-left: 0;
                            padding-right: 0;
                        }
                        ",
            "b_none"  => " {
                            position: relative!important;
                        }
                        .oxy-mega-dropdown_link + .oxy-mega-dropdown_inner.oxy-header-container {
                           width: var(--custom-dropdown-width);
                           max-width: var(--custom-dropdown-width);
                        }"
                        
        ) );
        
        
        $dropdown_styles_section->addStyleControl( 
            array(
                "name" => __('Dropdown width'),
                "property" => '--custom-dropdown-width',
                "control_type" => 'slider-measurebox',
                "selector" => '.oxy-mega-dropdown_inner',
                "condition" => 'dropdown_width=b_none||dropdown_elements=menu'
            )
        )
        ->setRange('0','1000','1')
        ->setUnits('px');
        
        
        $dropdown_styles_section->addStyleControl( 
            array(
                "name" => __('Content width'),
                "property" => '--dropdown-content-width',
                "control_type" => 'slider-measurebox',
                "selector" => '.oxy-mega-dropdown_content',
                "condition" => 'dropdown_width=e_full'
            )
        )
        ->setRange('0','1400','1')
        ->setUnits('px')
        ->setParam("description", __("Leave blank to inherit"));    
        
        
        
        $dropdown_styles_section->addOptionControl(
            array(
                "type" => "buttons-list",
                "name" => __("Dropdown position"),
                "slug" => "dropdown_align",
                "default" => 'left',
            )
        )->setValue(
           array( 
                "a_left" => "Left", 
                "b_center" => "Center",
                "c_right" => "Right",
               
           )
       )->setValueCSS( array(
            "a_left" => ".oxy-mega-dropdown_link + .oxy-mega-dropdown_inner.oxy-header-container {
                                left: 0;
                                right: auto;
                                transform: none;
                        }",
            "b_center"  => ".oxy-mega-dropdown_link + .oxy-mega-dropdown_inner.oxy-header-container {
                                transform: translateX(-50%);
                                left: 50%;
                        }",
            "c_right"  => ".oxy-mega-dropdown_link + .oxy-mega-dropdown_inner.oxy-header-container {
                                left: auto;
                                right: 0;
                                transform: none;
                        }",
                        
        ) )->whiteList();
        
        
        $dropdown_styles_section->addOptionControl(
            array(
                "type" => "buttons-list",
                "name" => __("Dropdown position relative to.."),
                "slug" => "dropdown_relative_positioning",
                "default" => 'link',
            )
        )->setValue(
           array( 
                "link" => "Link", 
                "header_row" => "Header",
           )
       )->setValueCSS( array(
            "link" => " {
                         position: relative!important;
                        }",
            "header_row" => " {
                         position: static!important;
                        }",
                        
        ) )->whiteList();
        
        
        
        
        
        
        /**
         * Layout / Spacing
         */
        $spacing_section = $dropdown_styles_section->addControlSection("spacing_section", __("Layout"), "assets/icon.png", $this);
        
        
        
        $spacing_section->flex($dropdown_content_selector, $this);
        
        
        /**
         * Colors
         */
        $dropdown_icon_section = $this->addControlSection("dropdown_icon_section", __("Icons"), "assets/icon.png", $this);
        
        
        $dropdown_icon_section->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Dropdown icon'),
                "slug" => 'dropdown_icon',
                "default" => 'FontAwesomeicon-angle-down'
            )
        );
        
        
        
        
        $icon_selector = '.oxy-mega-dropdown_icon';
        $icon_svg_selector = '.oxy-mega-dropdown_icon svg';
        
        $icon_focus_selector = '.oxy-mega-dropdown_link.oxy-mega-dropdown_inner-focus .oxy-mega-dropdown_icon';
        $icon_hover_selector = '.oxy-mega-dropdown_link:hover .oxy-mega-dropdown_icon';
        $icon_active_selector = '.oxy-mega-dropdown_link.oxy-mega-dropdown_inner-open .oxy-mega-dropdown_icon';
        
        $dropdown_icon_section->addStyleControls(
            array(
                array(
                    "name" => __('Icon Size'),
                    "selector" => $icon_selector,
                    "property" => 'font-size',
                   
                ),
            )
        );
        
        
        $dropdown_icon_section->addStyleControl(
            array(
                "name" => __('Rotation'),
                "selector" => $icon_svg_selector,
                "property" => 'transform:rotate',
                "control_type" => 'slider-measurebox',
                "default" => "0"
            )
        )
        ->setUnits('deg','deg')
        ->setRange('-180','180');

        $dropdown_icon_section->addStyleControl(
            array(
                "name" => __('Rotation When Open'),
                "selector" => ".oxy-mega-dropdown_link.oxy-mega-dropdown_inner-open .oxy-mega-dropdown_icon svg",
                "property" => 'transform:rotate',
                "control_type" => 'slider-measurebox',
                "default" => "0"
                //"condition" => 'show_dropdown_icon=true',
            )
        )
        ->setUnits('deg','deg')
        ->setRange('-180','180');
        
        $dropdown_icon_section->addStyleControl(
            array(
                "selector" => $icon_svg_selector,
                "name" => __('Rotate Duration'),
                //"default" => "0",
                "property" => 'transition-duration',
                "control_type" => 'slider-measurebox',
            )
        )
        ->setUnits('ms','ms')
        ->setRange('0','1000','1');
        
        
        $icon_color_section = $dropdown_icon_section->addControlSection("icon_color_section", __("Colors"), "assets/icon.png", $this);
        
        
        $icon_color_section->addStyleControl(
                 array(
                    "name" => 'Color',
                    "selector" => $icon_selector,
                    "property" => 'color',
                )
        )->setParam('hide_wrapper_end', true);
        
        $icon_color_section->addStyleControl(
                array(
                    "name" => 'Hover Text Color',
                    "selector" => $icon_hover_selector,
                    "property" => 'color',
                )
        )->setParam('hide_wrapper_start', true);
        
        $icon_color_section->addStyleControl(
                 array(
                    "name" => 'Focus Text Color',
                    "selector" => $icon_focus_selector,
                    "property" => 'color',
                )
        )->setParam('hide_wrapper_end', true);
        
        $icon_color_section->addStyleControl(
                 array(
                    "name" => 'Active Text Color',
                    "selector" => $icon_active_selector,
                    "property" => 'color',
                )
        )->setParam('hide_wrapper_start', true);
        
        $icon_color_section->addStyleControl(
                 array(
                    "name" => 'Background',
                    "selector" => $icon_selector,
                    "property" => 'background-color',
                )
        )->setParam('hide_wrapper_end', true);
        
        $icon_color_section->addStyleControl(
                array(
                    "name" => 'Hover Background',
                    "selector" => $icon_hover_selector,
                    "property" => 'background-color',
                )
        )->setParam('hide_wrapper_start', true);
        
        $icon_color_section->addStyleControl(
                 array(
                    "name" => 'Focus Background',
                    "selector" => $icon_focus_selector,
                    "property" => 'background-color',
                )
        )->setParam('hide_wrapper_end', true);
            
        $icon_color_section->addStyleControl(
                 array(
                    "name" => 'Active Background',
                    "selector" => $icon_active_selector,
                    "property" => 'background-color',
                )
        )->setParam('hide_wrapper_start', true);
        
        
        
        $icon_spacing_section = $dropdown_icon_section->addControlSection("icon_spacing_section", __("Spacing"), "assets/icon.png", $this);
        
        $icon_spacing_section->addPreset(
            "padding",
            "icon_padding",
            __("Icon padding"),
            $icon_selector
        )->whiteList();
        
        
        $icon_spacing_section->addPreset(
            "margin",
            "icon_margin",
            __("Icon margin"),
            $icon_selector
        )->whiteList();
        
        
        $dropdown_icon_section->borderSection('Borders', $icon_selector,$this);
        $dropdown_icon_section->boxShadowSection('Shadows', $icon_selector,$this);
        
        
        
        
        
        
        /**
         * Colors
         */
        $colors_section = $dropdown_styles_section->addControlSection("colors_section", __("Colors"), "assets/icon.png", $this);
        
        $dropdown_menu_selector = '.menu-item a';
        
        
        
        $colors_section->addStyleControl(
                 array(
                    "name" => 'Text Color',
                    "selector" => $dropdown_menu_selector,
                    "property" => 'color',
                     "condition" => 'dropdown_elements=menu'
                )
        )->setParam('hide_wrapper_end', true);
        
        $colors_section->addStyleControl(
                array(
                    "name" => 'Hover Text Color',
                    "selector" => $dropdown_menu_selector.":hover",
                    "property" => 'color',
                     "condition" => 'dropdown_elements=menu'
                )
        )->setParam('hide_wrapper_start', true);
        
        $colors_section->addStyleControl(
                 array(
                    "name" => 'Focus Text Color',
                    "selector" => $dropdown_menu_selector.":focus",
                    "property" => 'color',
                      "condition" => 'dropdown_elements=menu'
                )
        )->setParam('hide_wrapper_end', true);
        
        $colors_section->addStyleControl(
                 array(
                    "name" => 'Background',
                    "selector" => $dropdown_menu_selector,
                    "property" => 'background-color',
                      "condition" => 'dropdown_elements=menu'
                )
        )->setParam('hide_wrapper_start', true);
        
        $colors_section->addStyleControl(
                array(
                    "name" => 'Hover Background',
                    "selector" => $dropdown_menu_selector.":hover",
                    "property" => 'background-color',
                     "condition" => 'dropdown_elements=menu'
                )
        )->setParam('hide_wrapper_end', true);
        
        $colors_section->addStyleControl(
                 array(
                    "name" => 'Focus Background',
                    "selector" => $dropdown_menu_selector.":focus",
                    "property" => 'background-color',
                      "condition" => 'dropdown_elements=menu'
                )
        )->setParam('hide_wrapper_start', true);
        
        
        $colors_section->addStyleControls(
             array( 
                 array(
                    "selector" => $outer_selector,
                    "property" => 'font-size',
                     "default" => '14'
                ),
                 array(
                    "name" => 'Text Color',
                    "selector" => $outer_selector,
                    "property" => 'color',
                    "condition" => 'dropdown_elements=custom' 
                ),
                 array(
                    "name" => 'Background Color',
                    "selector" => $outer_selector,
                    "property" => 'background-color',
                    "condition" => 'dropdown_elements=custom' 
                ),
                 
                 
            )
        );
        
        
        
        
        
        
        
        $dropdown_selector = '.oxy-dropdown_inner';
        
        /**
         * Dropdowns
         */
        //$animations_section = $dropdown_styles_section->addControlSection("animations_section", __("Dropdown Animations"), "assets/icon.png", $this);
        
        
        /**
         * Dropdown Spacing
         */
        $dropdown_spacing_section = $dropdown_styles_section->addControlSection("dropdown_spacing_section", __("Spacing"), "assets/icon.png", $this);
        
        $dropdown_spacing_section->addPreset(
            "padding",
            "dropdown_padding",
            __("Padding"),
            '.oxy-mega-dropdown_container .oxy-mega-dropdown_content'
        )->whiteList();
        
        
        $dropdown_spacing_section->addPreset(
            "padding",
            "column_padding",
            __("Columns padding"),
            '.oxy-mega-dropdown_container .oxy-mega-dropdown_content .ct-new-columns > .ct-div-block'
        )->whiteList();
        
        
        $dropdown_menu_link_selector = '.oxy-mega-dropdown_menu a';
        
        $menulink_padding_left = $dropdown_spacing_section->addStyleControl(
                 array(
                    "name" => 'Menu item padding left', 
                    "selector" => $dropdown_menu_link_selector,
                    "property" => 'padding-left',
                    "default" => '15',
                    "control_type" => 'measurebox', 
                    "condition" => 'dropdown_elements=menu' 
                )
        );
        $menulink_padding_left->setParam('hide_wrapper_end', true);
        $menulink_padding_left->setUnits('px');    
        
        $menulink_padding_right = $dropdown_spacing_section->addStyleControl(
                array(
                    "name" => 'Menu item padding right',
                    "selector" => $dropdown_menu_link_selector,
                    "property" => 'padding-right',
                    "default" => '15',
                    "control_type" => 'measurebox',
                    "condition" => 'dropdown_elements=menu'
                )
        );
        $menulink_padding_right->setParam('hide_wrapper_start', true);
        $menulink_padding_right->setUnits('px'); 
        
        $menulink_padding_top = $dropdown_spacing_section->addStyleControl(
                 array(
                     "name" => 'Menu item padding top',
                    "selector" => $dropdown_menu_link_selector,
                    "property" => 'padding-top',
                     "default" => '10', 
                    "type" => 'measurebox', 
                     "condition" => 'dropdown_elements=menu'
                )
        );
        $menulink_padding_top->setParam('hide_wrapper_end', true);
        $menulink_padding_top->setUnits('px'); 
            
        $menulink_padding_bottom = $dropdown_spacing_section->addStyleControl(
                 array(
                     "name" => 'Menu item padding bottom',
                    "selector" => $dropdown_menu_link_selector,
                    "property" => 'padding-bottom',
                     "default" => '10', 
                    "type" => 'measurebox', 
                     "condition" => 'dropdown_elements=menu'
                )
        );
        $menulink_padding_bottom->setParam('hide_wrapper_start', true);
        $menulink_padding_bottom->setUnits('px'); 
        
        
        
        
        
        /**
         * Label
         */
        $label_section = $this->addControlSection("label_section", __("Label"), "assets/icon.png", $this);
        $label_selector = '.oxy-mega-dropdown_link-label';
        $label_focus_selector = '.oxy-mega-dropdown_link.oxy-mega-dropdown_inner-focus .oxy-mega-dropdown_link-label-inner';
        $label_hover_selector = '.oxy-mega-dropdown_link:hover .oxy-mega-dropdown_link-label-inner';
        $label_open_selector = '.oxy-mega-dropdown_link.oxy-mega-dropdown_inner-open .oxy-mega-dropdown_link-label-inner';
        
        $label_section->addOptionControl(
            array(
                "type" => "buttons-list",
                "name" => __("Label"),
                "slug" => "maybe_label",
                "default" => 'false',
                
            )
        )->setValue(
           array( 
                "true" => "Enable", 
                "false" => "Disable",
               
           )
       );
        
        $label_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('label Text'),
                "slug" => 'label_text',
                "default" => 'New',
                "base64" => true,
                "condition" => 'maybe_label=true'
            )
        );
        
        
        $label_section->addOptionControl(
            array(
                "type" => "buttons-list",
                "name" => __("Position"),
                "slug" => "label_position",
                "default" => 'false',
                
            )
        )->setValue(
           array( 
                "absolute" => "Absolute", 
                "static" => "Static",
           )
       )->setValueCSS( array(
            "absolute"  => " .oxy-mega-dropdown_link-label {
                            position: absolute;
                        }",
            "static"  => " .oxy-mega-dropdown_link-label {
                            position: static;
                        }",
        ) )->whiteList();
        
        
        $label_section->addStyleControl(
                 array(
                    "selector" => $label_selector,
                    "property" => 'left',
                    "condition" => 'label_position!=static'
                )
        )->setParam('hide_wrapper_end', true);
        
        $label_section->addStyleControl(
                array(
                    "selector" => $label_selector,
                    "property" => 'right',
                    "condition" => 'label_position!=static'
                )
        )->setParam('hide_wrapper_start', true);
        
        $label_section->addStyleControl(
                 array(
                    "selector" => $label_selector,
                    "property" => 'top',
                    "default" => '5',
                     "units" => 'px',
                     "condition" => 'label_position!=static'
                )
        )->setParam('hide_wrapper_end', true);
            
        $label_section->addStyleControl(
                 array(
                    "selector" => $label_selector,
                    "property" => 'bottom',
                     "condition" => 'label_position!=static'
                )
        )->setParam('hide_wrapper_start', true);
        
        
        $label_section->addStyleControl(
                 array(
                    "selector" => $label_selector,
                    "property" => 'margin-left',
                    "condition" => 'label_position=static'
                )
        )->setParam('hide_wrapper_end', true);
        
        $label_section->addStyleControl(
                array(
                    "selector" => $label_selector,
                    "property" => 'margin-right',
                    "condition" => 'label_position=static'
                )
        )->setParam('hide_wrapper_start', true);
        
        
        
        $label_colors_section = $label_section->addControlSection("label_colors_section", __("Colors"), "assets/icon.png", $this);
        
        
        $label_colors_section->addStyleControl(
                 array(
                    "name" => 'Color',
                    "selector" => '.oxy-mega-dropdown_link-label-inner',
                    "property" => 'color',
                )
        )->setParam('hide_wrapper_end', true);
        
        $label_colors_section->addStyleControl(
                array(
                    "name" => 'Hover Color',
                    "selector" => $label_hover_selector,
                    "property" => 'color',
                )
        )->setParam('hide_wrapper_start', true);
        
        $label_colors_section->addStyleControl(
                 array(
                    "name" => 'Focus Color',
                    "selector" => $label_focus_selector,
                    "property" => 'color',
                )
        )->setParam('hide_wrapper_end', true);
        
        $label_colors_section->addStyleControl(
                 array(
                    "name" => 'Active Color',
                    "selector" => $label_open_selector,
                    "property" => 'color',
                )
        )->setParam('hide_wrapper_start', true);
        
        $label_colors_section->addStyleControl(
                 array(
                    "name" => 'Background',
                    "default" => '#db4848', 
                    "selector" => '.oxy-mega-dropdown_link-label-inner',
                    "property" => 'background-color',
                )
        )->setParam('hide_wrapper_end', true);
        
        $label_colors_section->addStyleControl(
                array(
                    "name" => 'Hover Background',
                    "selector" => $label_hover_selector,
                    "property" => 'background-color',
                )
        )->setParam('hide_wrapper_start', true);
        
        $label_colors_section->addStyleControl(
                 array(
                    "name" => 'Focus Background',
                    "selector" => $label_focus_selector,
                    "property" => 'background-color',
                )
        )->setParam('hide_wrapper_end', true);
            
        $label_colors_section->addStyleControl(
                 array(
                    "name" => 'Active Background',
                    "selector" => $label_open_selector,
                    "property" => 'background-color',
                )
        )->setParam('hide_wrapper_start', true);
        
        $label_spacing_section = $label_section->addControlSection("label_spacing_section", __("Spacing"), "assets/icon.png", $this);
        
        $label_spacing_section->addPreset(
            "padding",
            "label_padding",
            __("Padding"),
            '.oxy-mega-dropdown_link-label-inner'
        )->whiteList();
        
        
        $label_section->typographySection('Typography', '.oxy-mega-dropdown_link',$this);
        
        
        
        /**
         * Text link spacing
         */
        $text_link_spacing_section = $this->addControlSection("text_link_spacing_section", __("Link spacing"), "assets/icon.png", $this);
        $text_link_selector = '.oxy-mega-dropdown_link';
        
        $link_margin_left = $text_link_spacing_section->addStyleControl(
                 array(
                    "selector" => $text_link_selector,
                    "property" => 'margin-left',
                    "control_type" => 'measurebox', 
                )
        );
        $link_margin_left->setParam('hide_wrapper_end', true);
        $link_margin_left->setUnits('px');    
        
        $link_margin_right = $text_link_spacing_section->addStyleControl(
                array(
                    "selector" => $text_link_selector,
                    "property" => 'margin-right',
                    "control_type" => 'measurebox',
                )
        );
        $link_margin_right->setParam('hide_wrapper_start', true);
        $link_margin_right->setUnits('px'); 
        
        $link_margin_top = $text_link_spacing_section->addStyleControl(
                 array(
                    "selector" => $text_link_selector,
                    "property" => 'margin-top',
                    "type" => 'measurebox', 
                )
        );
        $link_margin_top->setParam('hide_wrapper_end', true);
        $link_margin_top->setUnits('px'); 
            
        $link_margin_bottom = $text_link_spacing_section->addStyleControl(
                 array(
                    "selector" => $text_link_selector,
                    "property" => 'margin-bottom',
                    "type" => 'measurebox', 
                )
        );
        $link_margin_bottom->setParam('hide_wrapper_start', true);
        $link_margin_bottom->setUnits('px'); 
        
        
        
        
    }
    
    function allowedEmptyOptions($options) {

        $options_to_add = array(
            "oxy-mega-dropdown_link_text"
        );

        $options = array_merge($options, $options_to_add);

        return $options;
    }
    
}

add_filter("oxy_base64_encode_options", 
    function($items) { 
        $items=array_merge($items, array('oxy-mega-dropdown_link_text','oxy-mega-dropdown_hashlink')); 
        return $items;
    }
);


new ExtraMegaDropdown();