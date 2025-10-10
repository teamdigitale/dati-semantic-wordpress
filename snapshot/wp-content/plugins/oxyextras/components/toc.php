<?php

class ExtraTOC extends OxygenExtraElements {
        
    var $js_added = false;
    var $css_added = false;
    
	function name() {
        return 'Table of Contents';
    }
    
    function enablePresets() {
        return true;
    }
    
    function enableFullPresets() {
        return true;
    }
    
    function icon() {
        return plugin_dir_url(__FILE__) . 'assets/icons/'.basename(__FILE__, '.php').'.svg';
    }
    
    function extras_button_place() {
        return "single";
    }
    
    function tag() {
        return array('default' => 'div', 'choices' => 'nav,div' );
    }
    

    function render($options, $defaults, $content) {
        
        // icons
        $title_icon = isset( $options['title_icon'] ) ? esc_attr($options['title_icon']) : "";
        $dropdown_icon = isset( $options['dropdown_icon'] ) ? esc_attr($options['dropdown_icon']) : "";
        $heading_link_icon = isset( $options['heading_link_icon'] ) ? esc_attr($options['heading_link_icon']) : "";
        $context_icon = isset( $options['context_icon'] ) ? esc_attr($options['context_icon']) : "";
        
        //$text_before_one = isset( $options['counter_text_before'] ) ? esc_attr($options['counter_text_before']) : "";
        //$text_after_one = isset( $options['counter_text_after'] ) ? esc_attr($options['counter_text_after']) : "";
        
        $counter_text_before_two = isset( $options['counter_text_before_two'] ) ? esc_attr($options['counter_text_before_two']) : "";
        $counter_text_after_two = isset( $options['counter_text_after_two'] ) ? esc_attr($options['counter_text_after_two']) : "";
        
        

        global $oxygen_svg_icons_to_load;
        $oxygen_svg_icons_to_load[] = $title_icon;
        $oxygen_svg_icons_to_load[] = $dropdown_icon;
        $oxygen_svg_icons_to_load[] = $heading_link_icon;
        $oxygen_svg_icons_to_load[] = $context_icon;
        
        
        // get options
        $content_selector = isset( $options['content_selector'] ) ? esc_attr($options['content_selector']) : '';
        $heading_selectors = isset( $options['heading_selectors'] ) ? esc_attr($options['heading_selectors']) : 'h2,h3';
        
        $title_text = isset( $options['title_text'] ) ? esc_attr($options['title_text']) : 'Contents';
        $ignore_selector = isset( $options['ignore_selector'] ) ? esc_attr($options['ignore_selector']) : '';
        $initial_state = isset( $options['initial_state'] ) ? esc_attr($options['initial_state']) : '';

        $smooth_scroll = isset( $options['smooth_scroll'] ) ? esc_attr($options['smooth_scroll']) : '';
        $smooth_scroll_duration = isset( $options['smooth_scroll_duration'] ) ? esc_attr($options['smooth_scroll_duration']) : '';
        $smooth_scroll_offset = isset( $options['smooth_scroll_offset'] ) ? esc_attr($options['smooth_scroll_offset']) : '';

        $collapse_depth  = isset( $options['collapse_depth'] ) ? esc_attr($options['collapse_depth']) : '';
        $collapsed_below = isset( $options['collapsed_below'] ) ? esc_attr($options['collapsed_below']) : '';
        $max_characters = isset( $options['max_characters'] ) ? esc_attr($options['max_characters']) : '';
        $title_tag = isset( $options['title_tag'] ) ? esc_attr($options['title_tag']) : 'h3';
        $toc_positioning = isset( $options['toc_positioning'] ) ? esc_attr($options['toc_positioning']) : '';
        
        $animation_duration = isset( $options['animation_duration'] ) ? esc_attr($options['animation_duration']) : '';
        
        $context_type = isset( $options['context_type'] ) ? esc_attr($options['context_type']) : '';

        $suffix = isset( $options['suffix'] ) ? esc_attr($options['suffix']) : '';
        $seperator = isset( $options['seperator'] ) ? esc_attr($options['seperator']) : '';

        $heading_ids = isset( $options['heading_ids'] ) ? esc_attr($options['heading_ids']) : 'prefix';

        $builder_clicks = (defined('OXY_ELEMENTS_API_AJAX') && OXY_ELEMENTS_API_AJAX) ? 'onclick="extrasToggleToc.call(this)" ' : '';

        $context_type = isset( $options['context_type'] ) ? esc_attr($options['context_type']) : '';
        
        
        $output = '';
        
        if (!isset( $options['maybe_title'] ) || ('enable' === $options['maybe_title'])) {
            
            $output .= '<'.$title_tag.' class="oxy-table-of-contents_title"' . $builder_clicks .'>'. $title_text;
            
                if (!isset( $options['maybe_icon'] ) || ('enable' === $options['maybe_icon'])) {
                    $output .= '<svg class="oxy-table-of-contents_title-icon" id="icon'. esc_attr($options['selector']).'"><use xlink:href="#'. $title_icon .'"></use></svg>';
                }
            
            $output .= '</'.$title_tag.'>';
        }
        
        
        $output .= '<div class="oxy-table-of-contents_inner" ';
        
        $output .= 'data-content="'. $content_selector .'" ';
        
        $output .= 'data-headings="'. $heading_selectors .'" ';
        
        $output .= 'data-ignore="'. $ignore_selector .'" ';

        $output .= 'data-scroll="'. $smooth_scroll .'" ';
        $output .= 'data-scroll-duration="'. $smooth_scroll_duration .'" ';
        $output .= 'data-scroll-offset="'. $smooth_scroll_offset .'" ';
        
        $output .= 'data-collapse="'. $collapse_depth .'" ';
        
        if (isset( $options['maybe_autoid'] ) && ('true' === $options['maybe_autoid'])) {
            
            $output .= 'data-autoid="true" '; 
            $output .= 'data-autoid-type="'. $heading_ids .'" ';
            $output .= 'data-prefix="'. esc_attr($options['id_prefix']) .'" ';
            
        }
        
        if (isset( $options['maybe_auto_link'] ) && ('true' === $options['maybe_auto_link'])) {
            
            $output .= 'data-autolink="true" data-linkicon="'. $heading_link_icon .'" ';
            
        }

        
        
        $output .= 'data-context="'. $context_type .'" ';
        
        $output .= 'data-positioning="'. $toc_positioning .'" ';

        $output .= 'data-animation-duration="'. $animation_duration .'" ';
        
        
        if (isset( $options['maybe_dropdown_icon'] ) && ('enable' === $options['maybe_dropdown_icon'])) {
            
            $output .= 'data-dropdown="'. $dropdown_icon .'" ';
            
            
        }
        
        $output .= 'data-context-icon="'. $context_icon .'" ';
        
        $inlinestyles = "--extras-toc-suffix: '". $suffix ."'; --extras-toc-seperator: '". $seperator ."'; ";
        
        $output .= 'style="'. $inlinestyles .'" ';
        
        
        $output .= '></div>';
        
        
        echo $output; 

        $this->dequeue_scripts_styles();
        
       if ( method_exists('OxygenElement', 'builderInlineJS') ) { 
            
            $inline = file_get_contents( plugin_dir_path(__FILE__) . 'assets/tocbot.min.js' );
            $inline .= file_get_contents( plugin_dir_path(__FILE__) . 'assets/tocbot-init.js' );
            $inline .= "function extrasToggleToc() {

                        var inner = jQuery(this).children('.oxy-table-of-contents_inner');
                         jQuery(this).toggleClass('oxy-table-of-contents_toggled'); 
                         inner.slideToggle(%%animation_duration%%);
                         
                    }";
                                                        
            if( method_exists('OxygenElement', 'builderInlineJS') ) {
                $this->El->builderInlineJS($inline); 
            }

        }
        
         // add JavaScript code only once and if shortcode presented
        if ($this->js_added !== true) {
            if (!defined('OXY_ELEMENTS_API_AJAX') || !OXY_ELEMENTS_API_AJAX) {
                add_action( 'wp_footer', array( $this, 'output_js' ) );
                add_action( 'wp_footer', array( $this, 'output_init_js' ) );
            }
            $this->js_added = true;
        }
    
    }
    

    function class_names() {
        return array();
    }

    function controls() {


        /**
         * Title Section
         */ 
        $title_section = $this->addControlSection("title_section", __("Header / Title"), "assets/icon.png", $this);
        $title_selector = '.oxy-table-of-contents_title';
        
        $title_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Title',
                'slug' => 'maybe_title'
            )
            
        )->setValue(array( "enable" => "Enable", "disable" => "Disable" ))
         ->setDefaultValue('enable')->rebuildElementOnChange();
        
        $title_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Title text'),
                "slug" => 'title_text',
                "default" => 'Contents',
                "base64" => true,
                "condition" => 'maybe_title=enable'
            )
        )->rebuildElementOnChange();
        
        
        
        
        
        
         /**
         * Icon
         */
        
        $icon_section = $title_section->addControlSection("icon", __("Icon"), "assets/icon.png", $this); 
        
        $icon_selector = '.oxy-table-of-contents_title-icon';
        
        $icon_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Icon display'),
                'slug' => 'maybe_icon'
            )
            
        )->setValue(array( "enable" => "Enable", "disable" => "Disable" ))
         ->setDefaultValue('enable')->rebuildElementOnChange();
        
        $icon_section->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Icon set'),
                "slug" => 'title_icon',
                "value" => 'FontAwesomeicon-angle-up',
                "condition" => 'maybe_title=enable&&maybe_icon=enable'
            )
        )->rebuildElementOnChange();
        
        
        $icon_size = $icon_section->addStyleControl(
                array(
                    "name" => __('Icon Size'),
                    "selector" => $icon_selector,
                    "control_type" => 'slider-measurebox',
                    "value" => '24',
                    "property" => 'font-size',
                   "condition" => 'maybe_title=enable&&maybe_icon=enable'
                )
        );
        $icon_size->setRange(4, 72, 1);
        
        
        $title_section->addOptionControl(
            array(
                "type" => "dropdown",
                "name" => "Title tag",
                "slug" => "title_tag",
                "default" => 'h5',
                "condition" => 'maybe_title=enable'
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
        
        
        $title_color_section = $title_section->addControlSection("title_color_section", __("Colors"), "assets/icon.png", $this); 
        
        $title_color_section->addStyleControls(
             array( 
                array(
                    "name" => 'Color',
                    "selector" => $title_selector,
                    "property" => 'color',
                ),
                array(
                    "name" => 'Hover Color',
                    "selector" => $title_selector.":hover",
                    "property" => 'color',
                ),
                array(
                    "name" => 'Background Color',
                    "selector" => $title_selector,
                    "property" => 'background-color',
                    "value" => '#f8f8f8'
                ),
                array(
                    "name" => 'Hover Background  Color',
                    "selector" => $title_selector.":hover",
                    "property" => 'background-color',
                ),
                 
                 
            )
        );
        
        
        $title_layout_section = $title_section->addControlSection("title_layout_section", __("Layout"), "assets/icon.png", $this); 
        $title_layout_section->flex($title_selector, $this);
        
        
        $title_spacing_section = $title_section->addControlSection("title_spacing_section", __("Size & Spacing"), "assets/icon.png", $this); 
        
        
        $title_spacing_section->addPreset( 
            "margin",
            "main_title_margin",
            __("Margin"),
            $title_selector
        )->whiteList();
        
        $title_spacing_section->addPreset(
            "padding",
            "main_title_padding",
            __("Padding"),
            $title_selector
        )->whiteList();
        
        
        $title_section->typographySection('Typography', $title_selector,$this);
        $title_section->borderSection('Borders', $title_selector,$this);
        
       
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Content selector*'),
                "slug" => 'content_selector',
                "default" => '.ct-inner-content',
            )
        )->setParam("description", __("Add your container for the content and press 'apply params' to build table of contents"));
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Heading selectors'),
                "slug" => 'heading_selectors',
                "default" => 'h2,h3,h4,h5',
            )
        );
        
        
         
        



        
        $fixed_positioning_selector = '.oxy-table-of-contents_fixed';
        
        
       
        /**
         * List Items
         */
        $list_item_section = $this->addControlSection("list_item_section", __("List Items"), "assets/icon.png", $this);
        $list_item_selector = '.oxy-table-of-contents_list-item';
        $list_item_link_selector = '.oxy-table-of-contents_link';
        
        
        
        $this->addOptionControl(
            array(
                "type" => 'slider-measurebox',
                "name" => __('Initial Collapse depth'),
                "slug" => 'collapse_depth',
                "value" => '6',
            )
        )->setRange('0','6','1')
        ->rebuildElementOnChange()
         ->setParam("description", __("0 = No items collapsed, all visible"));

        
       $list_item_section->addStyleControl(
            array(
                "name" => __('Sub items indent'),
                "selector" => '.oxy-table-of-contents_list .oxy-table-of-contents_list',
                "control_type" => 'slider-measurebox',
                "unit" => 'px',
                "value" => '10',
                "property" => 'padding-left',
            )
        )->setRange(0, 30, 1);
        
        $list_item_section->addStyleControl(
                array(
                    "name" => __('Transition duration'),
                    "selector" => '.oxy-table-of-contents_inner > .oxy-table-of-contents_list',
                    "control_type" => 'slider-measurebox',
                    "value" => '300',
                    "property" => '--extras-toc-duration',
                )
        )->setRange(0, 600, 1)->setUnits('ms');

        
        $list_item_section->addStyleControl(
            array(
                "name" => __("List item display"),
                "selector" => '.oxy-table-of-contents_link',
                "property" => 'display',
                "control_type" => 'dropdown',
                "default" => 'inline-block'
            )
        )->setValue( array( 
            "block",
            "inline-flex",
            "flex", 
            "inline-block", 
            "inline",
       ) );


        $list_item_section->addStyleControl(
            array(
                "name" => __('Column count'),
                "selector" => '.oxy-table-of-contents_inner > .oxy-table-of-contents_list',
                "control_type" => 'slider-measurebox',
                "property" => 'column-count',
                "value" => '1'
            )
        )->setRange(0, 5, 1)
        ->setParam("description", __("Useful if adding your table of contents inline"));

        $list_item_section->addStyleControl(
            array(
                "name" => __('Column gap'),
                "selector" => '.oxy-table-of-contents_inner > .oxy-table-of-contents_list',
                "control_type" => 'slider-measurebox',
                "property" => 'column-gap',
            )
        )->setRange(0, 50, 1)
        ->setUnits('px');
                
                    
        
        
        
        $list_item_colors_section = $list_item_section->addControlSection("list_item_colors_section", __("Colors"), "assets/icon.png", $this);
        
        $list_item_colors_section->addStyleControls(
             array( 
                array(
                    "name" => 'Color',
                    "selector" => $list_item_link_selector,
                    "property" => 'color',
                ),
                array(
                    "name" => 'Hover Color',
                    "selector" => $list_item_link_selector.":hover",
                    "property" => 'color',
                ),
                array(
                    "name" => 'Active Color',
                    "selector" => ".oxy-table-of-contents_link.is-active-link",
                    "property" => 'color',
                ),
                array(
                    "name" => 'Background',
                    "selector" => $list_item_link_selector,
                    "property" => 'background-color',
                ),
                array(
                    "name" => 'Hover Background ',
                    "selector" => $list_item_link_selector.":hover",
                    "property" => 'background-color',
                ),
                array(
                    "name" => 'Active Background',
                    "selector" => ".oxy-table-of-contents_link.is-active-link",
                    "property" => 'background-color',
                )
                 
            )
        );

        
        
        
        $list_item_section->typographySection('Typography', $list_item_link_selector,$this);
        $list_item_section->typographySection('Hover Typography', $list_item_link_selector.":hover",$this);



        $list_item_section->borderSection('Borders', $list_item_link_selector,$this);
        
        
        
         /**
         * List Items Spacing
         */ 
        $list_item_spacing_section = $list_item_section->addControlSection("list_item_spacing_section", __("Spacing"), "assets/icon.png", $this);

        $list_item_spacing_section->addPreset( 
            "margin",
            "list_item_margin",
            __("Margin"),
            $list_item_link_selector
        )->whiteList();
        
        $list_item_spacing_section->addPreset(
            "padding",
            "list_item_padding",
            __("Padding"),
            $list_item_link_selector
        )->whiteList();
        
        
        /**
         * context
         */ 
        $icons_context_section = $this->addControlSection("icons_context_section", __("Icon / Counter / Border"), "assets/icon.png", $this);
        $context_icon_selector = ".oxy-table-of-contents_context-icon";
        
        $icons_context_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('List type'),
                'slug' => 'context_type',
                "default" => 'numbers',
            )
        )->setValue(array( 
            "icon" => "Icon",
            "numbers" => "Counter",
            "border" => "Border",
            "none" => "Text"
            )
        )->setValueCSS( array(
            "icon" => " .oxy-table-of-contents_link:before {
                            display: none;
                        }
                        .oxy-table-of-contents_context-icon {
                            display: inline;
                        }",
            "numbers"  => " .oxy-table-of-contents_context-icon {
                                display: none;
                            }
                            
                            .oxy-table-of-contents_link:before {
                                display: inline;
                            }",
            "none"  => ".oxy-table-of-contents_context-icon {
                            display: none;
                        }
                        .oxy-table-of-contents_link:before {
                            display: none;
                        }",
             "border"  => ".oxy-table-of-contents_link:before {
                                background-color: #eee;
                                content: ' '!important;
                                display: inline-block;
                                height: 100%;
                                left: 0;
                                margin-top: -1px;
                                position: absolute;
                                width: 2px;
                            }
                            
                            .oxy-table-of-contents_link.is-active-link:before {
                                background-color: currentColor;
                            }
                            .oxy-table-of-contents_context-icon {
                                display: none;
                            }"                
        ) )->rebuildElementOnChange();


        $icons_context_section->addStyleControls(
            array( 
               array(
                   "name" => __('Active color'),
                   "selector" => '[data-context="border"] .oxy-table-of-contents_link.is-active-link:before',
                   "property" => 'background-color',
                   "default" => 'currentColor',
                   "condition" => 'context_type=border'
               ),
               array(
                   "name" => __('Background color'),
                   "selector" => '[data-context="border"] .oxy-table-of-contents_link:before',
                   "property" => 'background-color',
                   "condition" => 'context_type=border'
               )
                
           )
       );

       $icons_context_section->addStyleControl( 
            array(
                "name" => __('Border width'),
                "default" => "2",
                "units" => 'px',
                "property" => 'width',
                "control_type" => 'slider-measurebox',
                "selector" => '[data-context="border"] .oxy-table-of-contents_link:before',
                "condition" => 'context_type=border'
                
            )
        )
        ->setRange('0','10','1');

        $icons_context_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Nest counters'),
                'slug' => 'inherit_list_type',
                "condition" => 'context_type=numbers'
            )
        )->setValue(array( 
            "false" => "False",
            "true" => "True",
            )
        )->setValueCSS( array(
            "false" => " .oxy-table-of-contents_link:before {
                            content: counters(extras_toc_items,'', var(--extras-toc-type)) '' var(--extras-toc-suffix) ' ';
                        }
                        
                        .oxy-table-of-contents_list .oxy-table-of-contents_list .oxy-table-of-contents_link:before {
                            content: counter(extras_toc_itemstwo, var(--extras-toc-typetwo)) '' var(--extras-toc-suffix) ' ';
                        }
        
                        .oxy-table-of-contents_list .oxy-table-of-contents_list .oxy-table-of-contents_list .oxy-table-of-contents_link:before {
                            content: counter(extras_toc_itemsthree, var(--extras-toc-typethree)) '' var(--extras-toc-suffix) ' ';
                        }
        
                        .oxy-table-of-contents_list .oxy-table-of-contents_list .oxy-table-of-contents_list .oxy-table-of-contents_list .oxy-table-of-contents_link:before {
                            content: counter(extras_toc_items, var(--extras-toc-typefour)) '' var(--extras-toc-suffix) ' ';
                        }
                        
                        "           
                
        ) )->setDefaultValue('true')->rebuildElementOnChange();

        
        $icons_context_section->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Context icon'),
                "slug" => 'context_icon',
                "default" => 'FontAwesomeicon-angle-right',
                "condition" => 'context_type=icon'
            )
        )->rebuildElementOnChange();

        $context_icon_size_control = $icons_context_section->addStyleControl(
            array(
                "name" => __('Icon Size'),
                "selector" => '.oxy-table-of-contents_context-icon',
                "control_type" => 'slider-measurebox',
                "value" => '14',
                "property" => 'font-size',
                "condition" => 'context_type=icon'
            )
        );
        $context_icon_size_control->setRange(4, 72, 1);


        /*$icons_context_section->addStyleControl(
            array(
                "name" => __('Rotation amount'),
                "selector" => '.oxy-table-of-contents_context-icon',
                "property" => '--context-icon-rotate',
                "control_type" => 'slider-measurebox',
                "condition" => 'context_type=icon'
            )
        )
        ->setUnits('deg','deg')
        ->setRange('-180','180');
        */
        
        
        $suffix_control = $icons_context_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Suffix'),
                "slug" => 'suffix',
                "default" => '.',
                "base64" => true,
                "condition" => 'context_type=numbers',
            )
        );
        $suffix_control->setParam('hide_wrapper_end', true);
        $suffix_control->rebuildElementOnChange();

        $seperator_control = $icons_context_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Seperator'),
                "slug" => 'seperator',
                "default" => '.',
                "base64" => true,
                "condition" => 'context_type=numbers',
            )
        );
        $seperator_control->setParam('hide_wrapper_start', true);
        $seperator_control->rebuildElementOnChange();

        
        $list_style_type_options = array( 
            "decimal" => "decimal (1, 2, 3)",
            "decimal-leading-zero" => "decimal-leading-zero (01, 02, 03)",
            "lower-roman" => "lower-roman (i ii iii)",
            "upper-roman" => "upper-roman (I II III)",
            "lower-alpha" => "lower-alpha (a b c)",
            "upper-alpha" => "upper-alpha (A B C)",
            "disc" => "disc",
            "circle" => "circle",
            "none",
        );

        
        
        $icons_context_section->addStyleControl(
            array(
                "name" => __("Level 1: List Style Type"),
                "selector" => '.oxy-table-of-contents_list',
                "property" => '--extras-toc-type',
                "control_type" => 'dropdown',
                "condition" => 'context_type=numbers',
                "default" => 'decimal'
            )
        )->setValue( $list_style_type_options );

        $icons_context_section->addStyleControl(
            array(
                "name" => __("Level 2: List Style Type"),
                "selector" => '.oxy-table-of-contents_list .oxy-table-of-contents_list',
                "property" => '--extras-toc-typetwo',
                "control_type" => 'dropdown',
                "condition" => 'context_type=numbers',
                "default" => 'decimal'
            )
        )->setValue( $list_style_type_options );

        $icons_context_section->addStyleControl(
            array(
                "name" => __("Level 3: List Style Type"),
                "selector" => '.oxy-table-of-contents_list .oxy-table-of-contents_list .oxy-table-of-contents_list',
                "property" => '--extras-toc-typethree',
                "control_type" => 'dropdown',
                "condition" => 'context_type=numbers',
                "default" => 'decimal'
            )
        )->setValue( $list_style_type_options );

        $icons_context_section->addStyleControl(
            array(
                "name" => __("Level 4: List Style Type"),
                "selector" => '.oxy-table-of-contents_list .oxy-table-of-contents_list .oxy-table-of-contents_list .oxy-table-of-contents_list',
                "property" => '--extras-toc-typefour',
                "control_type" => 'dropdown',
                "condition" => 'context_type=numbers',
                "default" => 'decimal-leading-zero'
            )
        )->setValue( $list_style_type_options );

        $counter_selector = '.oxy-table-of-contents_link:before';

        $icons_context_section->typographySection('Counter typography', $counter_selector, $this);

        $icons_context_spacing_section = $icons_context_section->addControlSection("icons_context_spacing_section", __("Spacing"), "assets/icon.png", $this);

        $icons_context_spacing_section->addPreset(
            "margin",
            "counter-margin",
            __("Margin"),
            '.oxy-table-of-contents_link:before, .oxy-table-of-contents_context-icon'
        )->whiteList();

        $icons_context_spacing_section->addPreset(
            "padding",
            "counter-padding",
            __("Padding"),
            '.oxy-table-of-contents_link:before, .oxy-table-of-contents_context-icon'
        )->whiteList();

         
        
        
        /**
         * Positioning Section
         */ 
        $positioning_section = $this->addControlSection("positioning_section", __("Spacing / Positioning"), "assets/icon.png", $this);

        $positioning_section->addCustomControl(
            '<div style="color: #fff; line-height: 1.3; font-size: 13px;">Positioning<hr></div>','description');
        
        $positioning_section->addStyleControl(
            array(
                'control_type' => 'dropdown',
                'name' => __('Position'),
                'property' => 'position',
            )
        )->setValue(array( 
            "sticky" => "Sticky", 
            //"scroll" => "Fixed after scrolling",
            "fixed" => "Fixed",
            "static" => "Static"
        ))->setDefaultValue('static');


        $title_section->addOptionControl(
            array(
                "type" => "dropdown",
                "name" => __('Position'),
                "slug" => "toc_positioning",
                "default" => 'static',
            )
        )->setValue(
           array( 
            "sticky" => "Sticky", 
            //"scroll" => "Fixed after scrolling",
            "fixed" => "Fixed",
            "static" => "Static"
           )
       )->setValueCSS( array(
        "sticky" => "  {
                            position: sticky;
                            -webkit-position: sticky;
                        }",
        "fixed" => "  {
                            position: fixed;
                        }",
                        "static" => "  {
                            position: static;
                        }",
        ) )->rebuildElementOnChange();
        
        $positioning_section->addStyleControl(
                array(
                    "selector" => '',
                    "control_type" => 'measurebox',
                    "property" => 'top',
                )
        )->setParam('hide_wrapper_end', true);
        
        $positioning_section->addStyleControl(
                array(
                    "selector" => '',
                    "control_type" => 'measurebox',
                    "property" => 'bottom',
                )
        )->setParam('hide_wrapper_start', true);
        
        $positioning_section->addStyleControl(
                array(
                    "selector" => '',
                    "control_type" => 'measurebox',
                    "property" => 'left',
                )
        )->setParam('hide_wrapper_end', true);
        
        $positioning_section->addStyleControl(
                array(
                    "selector" => '',
                    "control_type" => 'measurebox',
                    "property" => 'right',
                )
        )->setParam('hide_wrapper_start', true);
        
       


        $positioning_section->addCustomControl(
            '<div style="color: #fff; line-height: 1.3; font-size: 13px;">Inner spacing<hr></div>','description');


        $inner_content_selector = '.oxy-table-of-contents_inner > .oxy-table-of-contents_list';

        $positioning_section->addStyleControl(
            array(
                "selector" => $inner_content_selector,
                "control_type" => 'measurebox',
                "value" => '30',
                "property" => 'padding-top',
            )
        )->setUnits('px')->setParam('hide_wrapper_end', true);
        
        $positioning_section->addStyleControl(
                array(
                    "selector" => $inner_content_selector,
                    "control_type" => 'measurebox',
                    "value" => '30',
                    "property" => 'padding-left',
                )
        )->setUnits('px')->setParam('hide_wrapper_start', true);
        
        $positioning_section->addStyleControl(
                array(
                    "selector" => $inner_content_selector,
                    "control_type" => 'measurebox',
                    "value" => '30',
                    "property" => 'padding-right',
                )
        )->setUnits('px')->setParam('hide_wrapper_end', true);
        
        $positioning_section->addStyleControl(
                array(
                    "selector" => $inner_content_selector,
                    "control_type" => 'measurebox',
                    "value" => '30',
                    "property" => 'padding-bottom',
                )
        )->setUnits('px')->setParam('hide_wrapper_start', true);



         $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Ignore selector'),
                "slug" => 'ignore_selector',
                "default" => '.ignore',
            )
        )->setParam("description", __("Ignore headings matching this selector"));


        
        
        
        /**
         * Animation
         */ 
        $animation_section = $this->addControlSection("animation_section", __("Collapse"), "assets/icon.png", $this);

        $collapsed_below_control = $animation_section->addOptionControl(
            array(
                "name" => __('Start collapsed below'),
                "slug" => 'collapsed_below',
                "type" => 'medialist',
            )
        );
        $collapsed_below_control->setParam("always_option", true);
        $collapsed_below_control->rebuildElementOnChange();


        $animation_section->addOptionControl(
            array(
                 "type" => 'slider-measurebox',
                 "name" => __('Animation duration'),
                 "slug" => "animation_duration",
                 "default" => "300",
             )
         )
         ->setUnits('ms','ms')
         ->setRange(0, 1000, 1)
         ->rebuildElementOnChange();    
        


         /**
         * Individual Styles
         */ 
        $individual_link_section = $this->addControlSection("individual_link_section", __("H2-H6 Typography"), "assets/icon.png", $this);


        $h2_selector = '.oxy-table-of-contents_link.node-name--H2';
        $individual_link_section->typographySection('H2 items', $h2_selector, $this);

        $h3_selector = '.oxy-table-of-contents_link.node-name--H3';
        $individual_link_section->typographySection('H3 items', $h3_selector, $this);

        $h4_selector = '.oxy-table-of-contents_link.node-name--H4';
        $individual_link_section->typographySection('H4 items', $h4_selector, $this);

        $h5_selector = '.oxy-table-of-contents_link.node-name--H5';
        $individual_link_section->typographySection('H5 items', $h5_selector, $this);

        $h6_selector = '.oxy-table-of-contents_link.node-name--H6';
        $individual_link_section->typographySection('H6 items', $h6_selector, $this);


        /**
         * Scroll
         */ 
        $scroll_section = $this->addControlSection("scroll_section", __("Behaviour"), "assets/icon.png", $this);

        $scroll_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Smooth scroll',
                'slug' => 'smooth_scroll'
            )
            
        )->setValue(array( "true" => "Enable", "false" => "Disable" ))
         ->setDefaultValue('true');
        
        $scroll_section->addOptionControl(
            array(
                "type" => 'slider-measurebox',
                "name" => __('Smooth scroll duration'),
                "slug" => 'smooth_scroll_duration',
                "value" => '420',
                "condition" => 'smooth_scroll=true'
            )
        )->setUnits('ms','ms')
         ->setRange('10','1000','5');


         $scroll_section->addOptionControl(
            array(
                "type" => 'measurebox',
                "name" => __('Smooth scroll offset'),
                "slug" => 'smooth_scroll_offset',
                "value" => '0',
                "condition" => 'smooth_scroll=true'
            )
        )->setParam("description", __("Distance from the top of the page"));


        $scroll_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Add unique IDs to headings inside content'),
                'slug' => 'maybe_autoid'
            )
            
        )->setValue(array( "true" => "Enable", "false" => "Disable" ))
         ->setDefaultValue('false');

         $scroll_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Heading IDs'),
                'slug' => 'heading_ids',
                "condition" => 'maybe_autoid=true'
            )
        )->setValue(array(
             "prefix" => "ID with Prefix", 
             "text" => "Use heading text" 
             )
        )
         ->setDefaultValue('prefix');

         $scroll_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('ID prefix'),
                "slug" => 'id_prefix',
                "default" => 'toc-',
                "condition" => 'maybe_autoid=true&&heading_ids!=text'
            )
        );

        

         $scroll_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Add "copy link" icon to headings in content'),
                'slug' => 'maybe_auto_link'
            )
            
        )->setValue(array( "true" => "Enable", "false" => "Disable" ))
         ->setDefaultValue('false')
         ->setParam("description", __("Added dynamically on front end"));
        
        
        $scroll_section->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Link icon'),
                "slug" => 'heading_link_icon',
                "value" => 'FontAwesomeicon-chain',
                "condition" => 'maybe_auto_link=true'
            )
        );

        $this->addTagControl();


    }
    
    function customCSS($options, $selector) {

        $css = "";

        if( ! $this->css_added ) {
        
        $css .= ".oxy-table-of-contents {
                    width: 100%;
                }

                .oxy-table-of-contents_inner {
                    --extras-toc-type: decimal;
                    --extras-toc-typetwo: decimal;
                    --extras-toc-typethree: decimal;
                    --extras-toc-typefour: decimal;
                    --extras-toc-typefive: decimal;
                    --extras-toc-duration: 300ms;
                    font-size: 16px;
                    position: relative;
                    overflow: hidden;
                }

                .oxy-table-of-contents_inner > .oxy-table-of-contents_list {
                    padding: 20px;
                }

                .oxy-table-of-contents_list .oxy-table-of-contents_list {
                    padding-left: 10px;
                }

                .oxy-table-of-contents_link:before {
                    display: inline;
                }

                .oxy-table-of-contents_link:before {
                    content: counters(extras_toc_items,'', var(--extras-toc-type)) '' var(--extras-toc-suffix) ' ';
                }
                
                .oxy-table-of-contents_list .oxy-table-of-contents_list .oxy-table-of-contents_link:before {
                    content: counter(extras_toc_items, var(--extras-toc-type)) '' var(--extras-toc-seperator) '' counter(extras_toc_itemstwo, var(--extras-toc-typetwo)) '' var(--extras-toc-suffix) ' ';
                }

                .oxy-table-of-contents_list .oxy-table-of-contents_list .oxy-table-of-contents_list .oxy-table-of-contents_link:before {
                    content: counter(extras_toc_items, var(--extras-toc-type)) '' var(--extras-toc-seperator) '' counter(extras_toc_itemstwo, var(--extras-toc-typetwo)) '' var(--extras-toc-seperator) '' counter(extras_toc_itemsthree, var(--extras-toc-typethree)) '' var(--extras-toc-suffix) ' ';
                }

                .oxy-table-of-contents_list .oxy-table-of-contents_list .oxy-table-of-contents_list .oxy-table-of-contents_list .oxy-table-of-contents_link:before {
                    content: counter(extras_toc_items, var(--extras-toc-type)) '' var(--extras-toc-seperator) '' counter(extras_toc_itemstwo, var(--extras-toc-typetwo)) '' var(--extras-toc-seperator) '' counter(extras_toc_itemsthree, var(--extras-toc-typethree)) '' var(--extras-toc-seperator) '' counter(extras_toc_items, var(--extras-toc-typefour)) '' var(--extras-toc-suffix) ' ';
                }
                
                .oxy-table-of-contents_title {
                    cursor: pointer;
                }
                
                .oxy-table-of-contents_list {
                    margin: 0;
                    padding: 0;
                    list-style-type: none;
                    list-style-position: inside;
                    counter-reset: extras_toc_items; 
                }

                .oxy-table-of-contents_list .oxy-table-of-contents_list {
                    counter-reset: extras_toc_itemstwo;
                }

                .oxy-table-of-contents_list .oxy-table-of-contents_list .oxy-table-of-contents_list {
                    counter-reset: extras_toc_itemsthree;
                }

                .oxy-table-of-contents_list .oxy-table-of-contents_list .oxy-table-of-contents_list .oxy-table-of-contents_list {
                    counter-reset: extras_toc_itemsfour;
                }

                .oxy-table-of-contents_list-item {
                    counter-increment: extras_toc_items;
                }

                .oxy-table-of-contents_list .oxy-table-of-contents_list .oxy-table-of-contents_list-item {
                    counter-increment: extras_toc_itemstwo;
                }

                .oxy-table-of-contents_list .oxy-table-of-contents_list .oxy-table-of-contents_list .oxy-table-of-contents_list-item {
                    counter-increment: extras_toc_itemsthree;
                }

                .oxy-table-of-contents_list .oxy-table-of-contents_list .oxy-table-of-contents_list .oxy-table-of-contents_list .oxy-table-of-contents_list-item {
                    counter-increment: extras_toc_itemsfour;
                }
                
                .oxy-table-of-contents .is-collapsible {
                    max-height: 1000px;
                    overflow: hidden;
                    transition: all 1000ms ease-in-out;
                }
                
                .oxy-table-of-contents .is-collapsed {
                    max-height:0;
                }
                
                
                [data-toc-container] h1:focus,
                [data-toc-container] h2:focus,
                [data-toc-container] h3:focus,
                [data-toc-container] h4:focus,
                [data-toc-container] h5:focus,
                [data-toc-container] h6:focus {
                    outline: none;
                }

                
                .oxy-table-of-contents_context-icon svg {
                    fill: currentColor;
                    width: 1em;
                    height: 1em;
                }

                .oxy-table-of-contents_context-icon {
                    --context-icon-rotate: 0;
                    display: none;
                    vertical-align: middle;
                    line-height: 1;
                }

                .oxy-table-of-contents .is-active-link > .oxy-table-of-contents_context-icon {
                    transform: rotate(var(--context-icon-rotate));
                }
                
                .oxy-table-of-contents_dropdown-icon {
                    margin-left: auto;
                }
                
                .oxy-table-of-contents_dropdown-icon svg {
                    fill: currentColor;
                    width: 1em;
                    height: 1em;
                }
                
                .oxy-table-of-contents_title {
                    background-color: #f8f8f8;
                    display: flex;
                    align-items: center;
                    flex-direction: row;
                    justify-content: space-between; 
                    padding: 15px;
                }
                
                .oxy-table-of-contents_title::before {
                    content: none;
                }
        
                .oxy-table-of-contents_link {
                    color: inherit;
                    transition-duration: var(--extras-toc-duration);
                    display: inline-block;
                }

                .oxy-table-of-contents_link.is-active-link{
                    color: red;
                }
                
                .oxy-table-of-contents_placeholder {
                    display: none;
                }
                
                .oxy-table-of-contents_fixed + .oxy-table-of-contents_placeholder {
                    display: flex;
                }
                
                .oxy-table-of-contents_fixed .oxy-table-of-contents_inner {
                    display: flex!important;
                }
                
                .oxy-table-of-contents_list-item {
                    -webkit-column-break-inside: avoid;
                      -moz-column-break-inside: avoid;
                      break-inside: avoid; 
                }
                
                .oxy-table-of-contents_title-icon {
                    fill: currentColor;
                    width: 1em;
                    height: 1em;
                }
                
                .oxy-table-of-contents_toggled .oxy-table-of-contents_title-icon {
                            transform: rotate(180deg);
                    -webkit-transform: rotate(180deg);
                }
                 
                 .oxy-table-of-contents_heading-icon {
                    fill: currentColor;
                    width: 1em;
                    height: 1em;
                }
                 
                 .oxy-tbc-copy-id {
                    opacity: 0;
                    transition: opacity .5s ease;
                    background: none;
                    border: none;
                    box-shadow: none;
                    cursor: pointer;
                    display: inline-flex;
                    margin-left: 10px;
                    font-size: .6em;
                 }
                 
                 .oxy-tbc-copy-link {
                    outline: none;
                 }
                 
                 .oxy-tbc-copy-link:hover .oxy-tbc-copy-id {
                    opacity: 0.35;
                 }
                 
                 .oxy-tbc-copy-link:hover .oxy-tbc-copy-id:hover {
                    opacity: 1;
                 }";

                 $this->css_added = true;
            
            }


                $css .= "$selector.oxy-table-of-contents_fixed {
                            position: fixed;
                        }
                        
                        $selector.oxy-table-of-contents_fixed .oxy-table-of-contents_inner > .oxy-table-of-contents_list {
                            column-count: 1;
                        }";

                if ((isset($options["oxy-table-of-contents_collapsed_below"]) && $options["oxy-table-of-contents_collapsed_below"]=="always")) {    
                 
                    $css .= " $selector .oxy-table-of-contents_inner {
                            display: none;
                        }";

                }

                if ((isset($options["oxy-table-of-contents_collapsed_below"]) && $options["oxy-table-of-contents_collapsed_below"]!="never")) {
                    $max_width = oxygen_vsb_get_media_query_size($options["oxy-table-of-contents_collapsed_below"]);
                     
                    $css .= "@media (max-width: {$max_width}px) {

                                $selector .oxy-table-of-contents_inner {
                                    display: none;
                                }

                            }";
                }
        
        
        return $css;
        
    } 
    
    
    function output_js() {
        wp_enqueue_script( 'tocbot-js', plugin_dir_url(__FILE__) . 'assets/tocbot.min.js', '', '4.11.1' );
    }
    
    function output_init_js() { 
        wp_enqueue_script( 'tocbot-init-js', plugin_dir_url(__FILE__) . 'assets/tocbot-init.js', '', '1.0.2' );    
    }
    

}

new ExtraTOC();