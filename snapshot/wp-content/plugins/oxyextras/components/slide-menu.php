<?php

class ExtraslideMenu extends OxygenExtraElements {
    
    var $js_added = false;
    var $css_added = false;

	function name() {
        return __('Slide Menu'); 
    }
    
    function icon() {
        return plugin_dir_url(__FILE__) . 'assets/icons/'.basename(__FILE__, '.php').'.svg';
    }
    
    function enablePresets() {
        return true;
    }
    
    function enableFullPresets() {
        return true;
    }

    
    function init() {
        
        $this->El->useAJAXControls();

        // include only for builder
        if (isset( $_GET['oxygen_iframe'] )) {
            add_action( 'wp_footer', array( $this, 'output_js' ) );
            
        }
        
    }
    
    
    function extras_button_place() {
        return "interactive";
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

        if (isset( $options['schema_markup'] ) && esc_attr($options['schema_markup']) === 'true') {
        
            add_filter('nav_menu_link_attributes', array( $this, 'oxy_slide_menu_schema_link_attributes' ), 3, 10);
        
        }
        
        $icon = isset( $options['icon'] ) ? esc_attr($options['icon']) : "";
        
        global $oxygen_svg_icons_to_load;
        $oxygen_svg_icons_to_load[] = $icon;
        
        $trigger = isset( $options['trigger'] ) ? esc_attr($options['trigger']) : "";
        $start = esc_attr($options['start']);
        $duration = isset( $options['duration'] ) ? esc_attr($options['duration']) : "";
        $schema_markup = isset( $options['schema_markup'] ) && esc_attr($options['schema_markup']) === 'true' ? 'itemscope itemtype="https://schema.org/SiteNavigationElement"' : '';
        $link_before = isset( $options['schema_markup'] ) && esc_attr($options['schema_markup']) === 'true' ? '<span itemprop="name">' : '';
        $link_after = isset( $options['schema_markup'] ) && esc_attr($options['schema_markup']) === 'true' ? '</span>' : '';
        
        $nav_tag = (!isset( $options['menu_type'] ) || esc_attr($options['menu_type']) === 'dropdown') ? 'nav' : 'div';
        
        $maybe_current_menu_open = isset( $options['maybe_current_menu_open'] ) ? esc_attr($options['maybe_current_menu_open']) : "";
        
        $menu_title_tag = isset( $options['menu_title_tag'] ) ? esc_attr($options['menu_title_tag']) : "";

        $menu_source = isset( $options['menu_source'] ) ? esc_attr($options['menu_source']) : "";

        $auto_collapse = isset( $options['auto_collapse'] ) ? esc_attr($options['auto_collapse']) : "";

        if ('custom' === $menu_source) {

            $menu_name  = isset( $options['extras_menu_custom'] ) ? $dynamic( $options['extras_menu_custom'] ) : '';

        } else {
        
            $menu_name  = isset( $options['extras_menu_name'] ) ? esc_attr($options['extras_menu_name']) : '';

        }

        $this->dequeue_scripts_styles();

        if ( !is_nav_menu( $menu_name ) ) {
            return;
        } 
        
        ob_start();
        
        if( is_object(wp_get_nav_menu_object($menu_name) ) ) {    

            $menu_title = isset( $options['maybe_menu_title'] ) && ('enable' === esc_attr($options['maybe_menu_title'])) ? '<'.$menu_title_tag.' class="oxy-slide-menu_title">' . wp_get_nav_menu_object($menu_name)->name . '</'.$menu_title_tag.'>': "";

            echo $menu_title;
        }
        
        ?><<?php echo $nav_tag; ?> class="oxy-slide-menu_inner" <?php echo $schema_markup; ?> data-currentopen="<?php echo $maybe_current_menu_open; ?>" data-duration="<?php echo $duration; ?>" data-collapse="<?php echo $auto_collapse; ?>" data-start="<?php echo $start; ?>" data-icon="<?php echo $icon; ?>" data-trigger-selector="<?php echo $trigger; ?>">  <?php
		
		wp_nav_menu( array(
			'menu'           => $menu_name,
			'menu_class'     => 'oxy-slide-menu_list',
			'container'		 => '',
			'link_before'	 => $link_before,
			'link_after'	 => $link_after,
		) );

        $nav_menu_output = ob_get_clean();

        echo $nav_menu_output;
        
        echo '</'. $nav_tag .'>';
        
        if (isset( $options['schema_markup'] ) && esc_attr($options['schema_markup']) === 'true') {
        
            remove_filter('nav_menu_link_attributes', array( $this, 'oxy_slide_menu_schema_link_attributes' ), 3, 10);
            
        }
        
        // Don't run JS if we're inside a mega menu
        if ('megamenu' !== esc_attr($options['menu_type'])) {
            
            
            /**
             * Add SVG icon
             */ 
            if( !method_exists('OxygenElement', 'builderInlineJS') ) {
                $this->El->inlineJS(
                        "jQuery('#%%ELEMENT_ID%% .oxy-slide-menu_list .menu-item-has-children > a', 'body').each(function(){
                            jQuery(this).append('<button aria-expanded=\"false\" aria-pressed=\"false\" class=\"oxy-slide-menu_dropdown-icon-click-area\"><svg class=\"oxy-slide-menu_dropdown-icon\"><use xlink:href=\"#%%icon%%\"></use></svg><span class=\"screen-reader-text\">Submenu</span></button>');
                        });
                        "
                );
            } else {
                 $this->El->builderInlineJS(
                        "jQuery('#%%ELEMENT_ID%% .oxy-slide-menu_list .menu-item-has-children > a', 'body').each(function(){
                            jQuery(this).append('<button aria-expanded=\"false\" aria-pressed=\"false\" class=\"oxy-slide-menu_dropdown-icon-click-area\"><svg class=\"oxy-slide-menu_dropdown-icon\"><use xlink:href=\"#%%icon%%\"></use></svg><span class=\"screen-reader-text\">Submenu</span></button>');
                        });
                        "
                );
            }
        
            // add JavaScript code only once and if shortcode presented
            if ($this->js_added !== true) {
                    add_action( 'wp_footer', array( $this, 'output_js' ) );
                $this->js_added = true;
            }
            
        }
        
    }

    function class_names() {
        return array();
    }
    
    function oxy_slide_menu_schema_link_attributes( $atts, $item, $args ) {
        $atts['itemprop'] = 'url';
        return $atts;
    }

    function controls() {
        
        
        /**
         * Menu Type
         */ 
        $this->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Component Type'),
                'slug' => 'menu_type'
            )
            
        )->setValue(array( 
            "dropdown" => __("Slide menu"), 
            "megamenu" => __("Mega menu list") 
        ))->setDefaultValue('dropdown')
          ->setValueCSS( array(
            "dropdown" => " .oxy-slide-menu_list {
                                column-count: 1;
                            }
            ",  
            "megamenu" => " .oxy-slide-menu_list,
                            .oxy-slide-menu_list .menu-item {
                                -webkit-column-break-inside: avoid;
                                -moz-column-break-inside: avoid;
                                -o-column-break-inside: avoid;
                                -ms-column-break-inside: avoid;
                                column-break-inside: avoid;
                            }
                            
                            .oxy-slide-menu_list .menu-item {
                                display: list-item;
                            }
                            
                            .oxy-slide-menu_list .sub-menu {
                                display: block;
                            }
                            
                            .oxy-slide-menu_dropdown-icon-click-area {
                                display: none;
                            }
            ",
        ) )->rebuildElementOnChange();
        
        
        /**
         * Menu source
         */ 
        $this->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' =>  __('Menu Source'),
                'slug' => 'menu_source'
            )
            
        )->setValue(array( 
            "dropdown" => __("Select menu from list"), 
            "custom" => __("Dynamic") 
        ))->setDefaultValue('dropdown');
        
        
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
                "name" => __("WP Menu"),
                "slug" => "extras_menu_name",
                "condition" => 'menu_source!=custom'
            )
        )->setValue($menus_list)->rebuildElementOnChange();


        $extras_menu_custom_control = $this->addOptionControl(
            array(
                "type" => "textfield",
                "name" => __("WP Menu"),
                "slug" => "extras_menu_custom",
                "condition" => 'menu_source=custom',
                "base64" => true
            )
        );
        $extras_menu_custom_control->setParam("description", __("Menu name, menu slug or menu ID"));
        $extras_menu_custom_control->setParam('dynamicdatacode', '<div optionname="\'oxy-slide-menu_extras_menu_custom\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');
        
        
        /**
         * Menu Dropdown
         */ 
        $start_control = $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'State on page load',
                'slug' => 'start',
                'condition' => 'menu_type!=megamenu'
            )
            
        );
        
        $start_control->setValue(array( "hidden" => "Hidden", "open" => "Open" ));
        $start_control->setDefaultValue('open');
        $start_control->setValueCSS( array(
            "hidden" => "
                {
                 display: none;
                }
            ",
        ) );
        //$start_control->whiteList();
        
        
        /*$this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Menu items',
                'slug' => 'maybe_expanded',
                'condition' => 'menu_type=megamenu'
            )
            
        )->setValue(array( 
            "expanded" => "Expanded", 
            "collapsed" => "Collapsed" 
        ))->setDefaultValue('collapsed'); */
        
        
        $this->addStyleControl(
            array(
                "selector" => '.oxy-slide-menu_list',
                "name" => __('Column count'),
                "property" => 'column-count',
                "control_type" => 'slider-measurebox',
                'condition' => 'menu_type=megamenu'
            )
        )
        ->setRange('1','6','1');
        
        
        $this->addStyleControl(
            array(
                "selector" => '.oxy-slide-menu_list',
                "name" => __('Column gap'),
                "property" => 'column-gap',
                "default" => '0',
                "control_type" => 'slider-measurebox',
                'condition' => 'menu_type=megamenu'
            )
        )
        ->setUnits('px')    
        ->setRange('0','60','1');
        
        
        
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Click trigger to reveal menu'),
                "slug" => 'trigger',
                "condition" => 'start=hidden',
                "default" => '.oxy-burger-trigger',
                "base64" => true,
            )
        );

      
        $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' =>  __('Auto collapse when hash link clicked'),
                'slug' => 'auto_collapse',
                "condition" => 'start=hidden',
            )
        )->setValue(array( 
            "true" => __("Enable"), 
            "false" => __("Disable") 
        ))->setDefaultValue('disable');
        
        $item_selector = ".oxy-slide-menu_list .menu-item a";
        
        
        
        
        
         /**
         * Icons
         */
        
        $icon_section = $this->addControlSection("icon_section", __("Icons"), "assets/icon.png", $this);
        
        $icon_choose = $icon_section->addControlSection("icon_choose", __("Change Icons"), "assets/icon.png", $this);
        
        $icon_selector = ".oxy-slide-menu_dropdown-icon-click-area > svg";
        
        $icon_click_area = '.oxy-slide-menu_dropdown-icon-click-area';
        
        $icon_choose->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Icon'),
                "slug" => 'icon',
                "default" => 'Lineariconsicon-chevron-down'
            )
        )->rebuildElementOnChange();
        
        
        
       
        
        $icon_section->addStyleControls(
            array(
                array(
                    "name" => __('Icon Size'),
                    "selector" => $icon_selector,
                    "property" => 'font-size',
                   
                ),
            )
        );
        
        
        /**
         * Icon Colors
         */ 
        $icon_colors = $icon_section->addControlSection("icon_colors", __("Colors"), "assets/icon.png", $this);
        
        $icon_colors->addStyleControls(
            array(
                array(
                    "name" => __('Color'),
                    "selector" => $icon_click_area,
                    "property" => 'color',
                  
                ),
                 array(
                    "name" => __('Hover Color'),
                    "selector" => $icon_click_area.":hover",
                    "property" => 'color',
                  
                ),
                 array(
                    "name" => __('Focus Color'),
                    "selector" => $icon_click_area.":focus",
                    "property" => 'color',
                  
                ),
                array(
                    "name" => __('Background Color'),
                    "selector" => $icon_click_area,
                    "property" => 'background-color',
                  
                ),
                array(
                    "name" => __('Hover Background Color'),
                    "selector" => $icon_click_area.":hover",
                    "property" => 'background-color',
                  
                ),
                array(
                    "name" => __('Focus Background Color'),
                    "selector" => $icon_click_area.":focus",
                    "property" => 'background-color',
                  
                ),
            )
        );
        
        $icon_colors->addOptionControl(
            array(
                "type" => 'checkbox',
                "name" => __('Focus Outline'),
                "slug" => 'icon_focus',
                "value" => 'true'
            )
        )->setValueCSS( array(
            "false" => " .oxy-slide-menu_dropdown-icon-click-area:focus {
                outline: none;
            }",
        ) );
        
        
        /**
         * Icon Spacing
         */ 
        $icon_spacing = $icon_section->addControlSection("icon_spacing", __("Icon Spacing"), "assets/icon.png", $this);
        
        $icon_section->borderSection('Borders', $icon_click_area,$this);

        $icon_spacing->addPreset( 
            "margin",
            "dropdown_icon_item_margin",
            __("Icon Margin"),
            $icon_click_area
        )->whiteList();
        
        $icon_spacing->addPreset(
            "padding",
            "dropdown_icon_item_padding",
            __("Icon Padding"),
            $icon_click_area
        )->whiteList();
        
        $icon_section->addStyleControl(
            array(
                "name" => __('Rotation'),
                "selector" => $icon_selector,
                "property" => 'transform:rotate',
                "control_type" => 'slider-measurebox',
            )
        )
        ->setUnits('deg','deg')
        ->setRange('-180','180');

        $icon_section->addStyleControl(
            array(
                "name" => __('Rotation When Open'),
                "selector" => ".oxy-slide-menu_dropdown-icon-click-area.oxy-slide-menu_open > svg",
                "property" => 'transform:rotate',
                "control_type" => 'slider-measurebox',
                "default" => "180"
                //"condition" => 'show_dropdown_icon=true',
            )
        )
        ->setUnits('deg','deg')
        ->setRange('-180','180');
        
        $icon_section->addStyleControl(
            array(
                "type" => 'measurebox',
                "selector" => $icon_selector,
                "name" => __('Rotate Duration'),
                //"default" => "0",
                "property" => 'transition-duration',
                "control_type" => 'slider-measurebox',
            )
        )
        ->setUnits('s','s')
        ->setRange('0','1','0.1');
        
        
        /**
         * Menu Title
         */
        
        $menu_title_section = $this->addControlSection("menu_title_section", __("Menu Title"), "assets/icon.png", $this);
        $menu_title_selector = '.oxy-slide-menu_title';
        
        $menu_title_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Menu title display'),
                'slug' => 'maybe_menu_title'
            )
            
        )->setValue(array( 
            "disable" => "Disable", 
            "enable" => "Enable" 
        ))
         ->setDefaultValue('disable')
         ->rebuildElementOnChange();  
        
        
        $menu_title_section->addOptionControl(
            array(
                "type" => "dropdown",
                "name" => "Tag",
                "slug" => "menu_title_tag",
                "default" => 'h4',
                "condition" => 'maybe_menu_title=enable'
            )
        )->setValue(
           array( 
                "h1" => "h1", 
                "h2" => "h2",
               "h3" => "h3",
               "h4" => "h4",
               "h5" => "h5",
               "h6" => "h6",
               "p" => "p",
               "div" => "div",
               "span" => "span",
           )
       )->rebuildElementOnChange();
        
        
        $menu_title_section->addStyleControls(
            array(
                array(
                    "name" => __('Background Color'),
                    "property" => 'background-color',
                    "selector" => $menu_title_selector,
                ),
            )
        );
        
        
        $menu_title_spacing = $menu_title_section->addControlSection("menu_title_spacing", __("Spacing"), "assets/icon.png", $this);
        
        $menu_title_spacing->addPreset(
            "padding",
            "menu_title_padding",
            __("Padding"),
            $menu_title_selector
        )->whiteList();
        
        $menu_title_spacing->addPreset( 
            "margin",
            "menu_title_margin",
            __("Margin"),
            $menu_title_selector
        )->whiteList();
        
        
        
        
        $menu_title_section->borderSection('Borders', $menu_title_selector,$this);
        $menu_title_section->typographySection('Typography', $menu_title_selector,$this);
        
        
        
        /**
         * Menu Items
         */
        
        $styles_section = $this->addControlSection("extra_slide_menu_styles_section", __("Menu Items"), "assets/icon.png", $this);
        
        
        
        
        $styles_section->addStyleControls(
            array(
                array(
                    "name" => __('Color'),
                    "property" => 'color',
                    "selector" => $item_selector,
                    "default" => 'inherit'
                ),
                array(
                    "name" => __('Hover Color'),
                    "property" => 'color',
                    "selector" => $item_selector.":hover",
                ),
                array(
                    "name" => __('Background Color'),
                    "property" => 'background-color',
                    "selector" => $item_selector,
                    "default" => 'inherit'
                ),
                array(
                    "name" => __('Background Hover Color'),
                    "property" => 'background-color',
                    "selector" => $item_selector.":hover",
                )
            )
        );
        
        
        $align_control = $styles_section->addOptionControl(
            array(
                'type' => 'dropdown',
                "name" => __('Menu align'),
                "slug" => "menu_align",
                'condition' => 'menu_type!=megamenu'
            )
        );
        
        $align_control->setValue(array( 
            "text left" => "Left",
            "center" => "Center all",
            "center_text"  => "Center text",
            "right" => "Right",
        ));
        $align_control->setDefaultValue('left');
        $align_control->setValueCSS( array(
            "center_text" => " .menu-item a {
                            justify-content: center;
                            position: relative;
                        }

                        .oxy-slide-menu_dropdown-icon-click-area {
                            position: absolute;
                            right: 0;
                        }
            ",
            
             "center" => " .menu-item a {
                            justify-content: center;
                        }
            ",
            "right" => " .menu-item a {
                            justify-content: flex-end;
                        }
            ",
        ) );
        
        
         $styles_section->addStyleControl(
           array(
                    "property" => 'font-size',
                    //"selector" => $item_selector,
                )
        );
        
        $styles_section->addStyleControl(
            array(
                "type" => 'measurebox',
                "selector" => $item_selector,
                "name" => __('Hover Transition Duration'),
                "property" => 'transition-duration',
                "control_type" => 'slider-measurebox',
            )
        )
        ->setUnits('s','s')
        ->setRange('0','1','0.1');
        
        
        
        $styles_section->borderSection('Borders', $item_selector,$this);
        $styles_section->typographySection('Typography', '.menu-item a',$this);
        
        
        
        /**
         * Menu Item Spacing
         */
        
        $item_spacing = $styles_section->addControlSection("extra_slide_menu_item_spacing", __("Spacing"), "assets/icon.png", $this);
        
        $item_spacing->addPreset(
            "padding",
            "extra_menu_link_padding",
            __("Padding"),
            $item_selector
        )->whiteList();
        
        $item_spacing->addPreset(
            "margin",
            "extra_menu_link_margin",
            __("Margin"),
            $item_selector
        )->whiteList();
        
        
       /**
         * SubMenus 
         */
        
        $submenu_section = $this->addControlSection("extra_slide_menu_submenu_section", __("Sub Menus"), "assets/icon.png", $this);
        
        $submenu_selector = ".sub-menu";
        
        $submenu_section->addStyleControls(
            array(
                array(
                    "name" => __('Background Color'),
                    "property" => 'background-color',
                    "selector" => $submenu_selector
                )
            )
        );
        
        
        $submenu_section->typographySection('Typography', '.oxy-slide-menu_list .sub-menu .menu-item a',$this);
        $submenu_section->borderSection('Borders', $submenu_selector ,$this);
        
        $submenu_spacing = $submenu_section->addControlSection("extra_slide_menu_submenu_spacing", __("Spacing"), "assets/icon.png", $this);
        
        $submenu_spacing->addPreset(
            "padding",
            "extra_slide_menu_submenu_padding",
            __("Container padding"),
            $submenu_selector
        )->whiteList();
        
        $submenu_spacing->addPreset(
            "margin",
            "extra_slide_menu_submenu_margin",
            __("Container margin"),
            $submenu_selector
        )->whiteList();

        $submenu_spacing->addPreset(
            "padding",
            "extra_slide_menu_submenu_padding",
            __("Link padding"),
            '.oxy-slide-menu_list .sub-menu .menu-item a'
        )->whiteList();
        
        $submenu_spacing->addPreset(
            "margin",
            "extra_slide_menu_submenu_margin",
            __("Link margin"),
            '.oxy-slide-menu_list .sub-menu .menu-item a'
        )->whiteList();
        
        
        $submenu_section->addOptionControl(
           array(
                "type" => 'slider-measurebox',
                "name" => __('Sub menu slide duration'),
                "slug" 	    => "duration",
                "default" => "300",
            )
        )
        ->setUnits('ms','ms')
        ->setRange(0, 1000, 1)
        ->rebuildElementOnChange()
        ->whiteList();
        
        
        /**
         * Current Menu Item
         */
        
        $current_section = $this->addControlSection("current_section", __("Current Menu Item"), "assets/icon.png", $this);
        
        $current_item_selector = ".oxy-slide-menu_list .current-menu-item > a";
        
        $current_section->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Expand menu items to show current menu item on page load'),
                'slug' => 'maybe_current_menu_open'
            )
            
        )->setValue(array( 
            "disable" => "Disable", 
            "enable" => "Enable" 
        ))
         ->setDefaultValue('disable')
         ->setValueCSS( array(
            "enable" => "",
        ) );
        
        
        
        $current_section->addStyleControls(
            array(
                array(
                    "name" => __('Color'),
                    "property" => 'color',
                    "selector" => $current_item_selector,
                ),
                array(
                    "name" => __('Hover Color'),
                    "property" => 'color',
                    "selector" => $current_item_selector.":hover",
                ),
                array(
                    "name" => __('Background Color'),
                    "property" => 'background-color',
                    "selector" => $current_item_selector,
                ),
                array(
                    "name" => __('Background Hover Color'),
                    "property" => 'background-color',
                    "selector" => $current_item_selector.":hover",
                )
            )
        );
        
        
        
        $current_section->borderSection('Borders', $current_item_selector,$this);


        $this->addStyleControl( 
            array(
                "name" => __('Width'),
                "property" => 'width',
                "control_type" => 'slider-measurebox',
            )
        )
        ->setRange('0','400','1')
        ->setUnits('px');
        
        
        /**
         * Schema Markup 
         */
        $this->addOptionControl(
            array(
                "type" => 'checkbox',
                "name" => __('Add Schema markup to menu','oxygen'),
                "slug" => 'schema_markup',
                'condition' => 'menu_type!=megamenu'
            )
        );
        
        
        
        
    }
    
    
    function customCSS($options, $selector) {
        
        $css = '';
        
        if( ! $this->css_added ) {
        
            $css .= "
                    .oxy-slide-menu .menu-item a {
                        color: inherit;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                    }

                    .oxy-slide-menu .menu-item {
                        list-style-type: none;
                        display: flex;
                        flex-direction: column;
                        width: 100%;
                    }

                    .oxy-slide-menu_dropdown-icon-click-area {
                        background: none;
                        cursor: pointer;
                        color: inherit;
                        border: none;
                        padding: 0;
                    }

                    .oxy-slide-menu_dropdown-icon-click-area.oxy-slide-menu_open > svg {
                        transform: rotate(180deg);
                    }

                    .oxy-slide-menu_list {
                        padding: 0;
                    }

                    .oxy-slide-menu .sub-menu {
                        display: none;
                        flex-direction: column;
                        padding: 0;
                    }

                    .oxy-slide-menu_dropdown-icon {
                        height: 1em;
                        fill: currentColor;
                        width: 1em;
                    }

                    .oxy-slide-menu_dropdown-icon-click-area:first-of-type:nth-last-of-type(2) {
                        display: none;
                    }
                    
                    .oxy-slide-menu a[href='#'] span[itemprop=name] {
                        pointer-events: none;
                    }

                    .oxy-slide-menu .screen-reader-text {
                        clip: rect(1px,1px,1px,1px);
                        height: 1px;
                        overflow: hidden;
                        position: absolute!important;
                        width: 1px;
                        word-wrap: normal!important;
                    }";
            
            $this->css_added = true;
            
        }

            return $css;
        
    }
    
    /**
     * Output js inline in footer once.
     */
    function output_js() { ?>
            <script type="text/javascript">   
                
            jQuery(document).ready(oxygen_init_slide_menu);
            function oxygen_init_slide_menu($) {
                
                // check if supports touch, otherwise it's click:
                let touchEvent = 'ontouchstart' in window ? 'click' : 'click';  
                  
                    $('.oxy-slide-menu').each(function(){
                        
                          let slide_menu = $(this);
                          let slide_start = slide_menu.children( '.oxy-slide-menu_inner' ).data( 'start' );
                          let slide_duration = slide_menu.children( '.oxy-slide-menu_inner' ).data( 'duration' );
                          let slideClickArea = '.menu-item-has-children > a > .oxy-slide-menu_dropdown-icon-click-area';
                          let dropdownIcon = slide_menu.children( '.oxy-slide-menu_inner' ).data( 'icon' );
                        
                        
                          slide_menu.find('.menu-item-has-children > a').append('<button aria-expanded=\"false\" aria-pressed=\"false\" class=\"oxy-slide-menu_dropdown-icon-click-area\"><svg class=\"oxy-slide-menu_dropdown-icon\"><use xlink:href=\"#'+ dropdownIcon +'\"></use></svg><span class=\"screen-reader-text\">Submenu</span></button>');
                         
                         // If being hidden as starting position, for use as mobile menu
                          if ( slide_start == 'hidden' ) {

                              let slide_trigger_selector = $( slide_menu.children( '.oxy-slide-menu_inner' ).data( 'trigger-selector' ) );

                              slide_trigger_selector.on( touchEvent, function(e) {      
                                 slide_menu.slideToggle(slide_duration);
                              } );

                             if (true == slide_menu.children( '.oxy-slide-menu_inner' ).data( 'collapse' ) ) {
                                slide_menu.find(".menu-item a[href^='#']:not([href='#'])").on('click', function(e) {
                                    slide_trigger_selector.click()
                                })
                            }

                          }
                        
                          if ('enable' === slide_menu.children( '.oxy-slide-menu_inner' ).data( 'currentopen' )) {
                              
                              let currentAncestorButton = slide_menu.find('.current-menu-ancestor').children('a').children('.oxy-slide-menu_dropdown-icon-click-area');
                              
                              currentAncestorButton.attr('aria-expanded', 'true');
                              currentAncestorButton.attr('aria-pressed', 'true');
                              currentAncestorButton.addClass('oxy-slide-menu_open');
                              currentAncestorButton.closest('.current-menu-ancestor').children('.sub-menu').slideDown(0);
                          }

                          
                        
                    });

                 // Sub menu icon being clicked
                 $('.oxy-slide-menu, .oxygen-builder-body').on( touchEvent, '.menu-item-has-children > a > .oxy-slide-menu_dropdown-icon-click-area',  function(e) {  
                        e.stopPropagation();
                        e.preventDefault();
                            oxy_slide_menu_toggle(this);
                        }

                    );
                

                    function oxy_slide_menu_toggle(trigger) {
                                    
                            var durationData = $(trigger).closest('.oxy-slide-menu_inner').data( 'duration' );
                            var othermenus = $(trigger).closest( '.menu-item-has-children' ).siblings('.menu-item-has-children');
                                             othermenus.find( '.sub-menu' ).slideUp( durationData );
                                             othermenus.find( '.oxy-slide-menu_open' ).removeClass( 'oxy-slide-menu_open' );
                                             othermenus.find( '.oxy-slide-menu_open' ).attr('aria-expanded', function (i, attr) {
                                                    return attr == 'true' ? 'false' : 'true'
                                                });
                                            othermenus.find( '.oxy-slide-menu_open' ).attr('aria-pressed', function (i, attr) {
                                                return attr == 'true' ? 'false' : 'true'
                                            });

                            $(trigger).closest('.menu-item-has-children').children('.sub-menu').slideToggle( durationData );

                            $(trigger).attr('aria-expanded', function (i, attr) {
                                return attr == 'true' ? 'false' : 'true'
                            });

                            $(trigger).attr('aria-pressed', function (i, attr) {
                                return attr == 'true' ? 'false' : 'true'
                            });

                            $(trigger).toggleClass('oxy-slide-menu_open');

                        }        
                        
                
                    let selector = '.oxy-slide-menu .menu-item a[href*="#"]';
                    $(selector).on('click', function(event){
                        
                        if ($(event.target).closest('.oxy-slide-menu_dropdown-icon-click-area').length > 0) {
                            // toggle icon clicked, no need to trigger it 
                            return;
                        }
                        else if ($(event.target).attr("href") === "#" && $(this).parent().hasClass('menu-item-has-children')) {
                            // prevent browser folllowing link
                            event.preventDefault();
                            // empty href don't lead anywhere, use it as toggle icon click area
                            var hasklinkIcon = $(this).find('.oxy-slide-menu_dropdown-icon-click-area');
                            oxy_slide_menu_toggle(hasklinkIcon);
                            
                        }
                      });

             };
            
        </script>

    <?php }
    
    function afterInit() {
        //$this->removeApplyParamsButton();
    }

}

add_filter("oxy_base64_encode_options", 
    function($items) { 
        $items=array_merge($items, array('oxy-slide-menu_extras_menu_custom')); 
        return $items;
    }
);

new ExtraslideMenu();