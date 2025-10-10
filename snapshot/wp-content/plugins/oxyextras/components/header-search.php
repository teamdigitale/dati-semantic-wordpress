<?php

class ExtraSearchForm extends OxygenExtraElements {
    
    var $js_added = false;
    var $css_added = false;

	function name() {
        return __('Header Search'); 
    }
    
    function icon() {
        return plugin_dir_url(__FILE__) . 'assets/icons/'.basename(__FILE__, '.php').'.svg';
    }
    
    
    function init() {

        // include only for builder
        if (isset( $_GET['oxygen_iframe'] )) {
            add_action( 'wp_footer', array( $this, 'output_js' ) );
            
        }
        
    }
    
    function afterInit() {
        $this->removeApplyParamsButton();
    }
    
    function enablePresets() {
        return true;
    }
    
    function enableFullPresets() {
        return true;
    }
    
    
    function extras_button_place() {
        return "interactive";
    }
    
    
    
    function render($options, $defaults, $content) {
        
        $search_icon = isset( $options['search_icon'] ) ? esc_attr($options['search_icon']) : "FontAwesomeicon-search";
        $close_icon = isset( $options['close_icon'] ) ? esc_attr($options['close_icon']) : "FontAwesomeicon-close";
        
        $search_icon_aria = isset( $options['search_icon_aria'] ) ? esc_attr($options['search_icon_aria']) : "Open search";
        $close_icon_aria = isset( $options['close_icon_aria'] ) ? esc_attr($options['close_icon_aria']) : "Close search";
        
        $button_text = isset( $options['button_text'] ) ? esc_attr($options['button_text']) : "";

        $maybe_require = isset( $options['maybe_require'] ) ? esc_attr($options['maybe_require']) : "";

        $required = isset( $options['maybe_require'] ) && ('true' ===  esc_attr($options['maybe_require'])) ? 'required' : '';
        
        global $oxygen_svg_icons_to_load;
        $oxygen_svg_icons_to_load[] = $search_icon;
        $oxygen_svg_icons_to_load[] = $close_icon;


        // Get Options
        $action = esc_url( home_url( '/' ));
        $label = _x( 'Search for:', 'label' );
        $placeholder = isset( $options['placeholder_text'] ) ? esc_attr($options['placeholder_text']) : "Search ...";
        $icon_close_display = esc_attr($options['icon_close_display']);
        $value = get_search_query();
        $title = esc_attr_x( 'Search for:', 'label' );
        $submit_value = esc_attr_x( 'Search', 'submit button' );
        $search_post_type_filter = esc_attr($options['search_post_type_filter']);
        $post_type_slug = isset( $options['post_type_slug'] ) ? esc_attr($options['post_type_slug']) : "";

        $prevent_scroll = isset( $options['prevent_scroll'] ) ? esc_attr($options['prevent_scroll']) : "";
        

        $output = '<button aria-label="'. $search_icon_aria .'" class="oxy-header-search_toggle oxy-header-search_toggle-open" data-prevent-scroll="'. $prevent_scroll . '"><span class="oxy-header-search_toggle-text">'. $button_text .'</span><svg class="oxy-header-search_open-icon" id="open'. esc_attr($options['selector']) .'-icon"><use xlink:href="#' . $search_icon . '"></use></svg></button>';
        
        $html = '<form role="search" method="get" class="oxy-header-search_form" action="'.$action.'">
                    <div class="oxy-header-container">
                    <label>
                        <span class="screen-reader-text">'.$label.'</span>
                        <input '. $required .' type="search" class="oxy-header-search_search-field" placeholder="'.$placeholder.'" value="'.$value.'" name="s" title="'.$title.'" />
                    </label>';

        $output .= apply_filters( 'get_search_form', $html );
        
       
        
        // Maybe display Close icon
        if ($icon_close_display != 'hide') {
            $output .= '<button aria-label="'. $close_icon_aria .'" type=button class="oxy-header-search_toggle"><svg class="oxy-header-search_close-icon" id="close'. esc_attr($options['selector']) .'-icon"><use xlink:href="#' . $close_icon . '"></use></svg></button>';
        }
        
        if ( ( $search_post_type_filter === 'true') && isset( $options['post_type_slug'] ) ) {
            $output .= '<input type="hidden" name="post_type" value="'. $post_type_slug .'" />';
        }
        
        $output .= '<input type="submit" class="search-submit" value="'.$submit_value.'" /></div></form>';
        
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
        
        /**
         * Type of Search
         */
        $this->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => 'Header Search Type',
                'slug' => 'search_type',
                'default' => 'overlay'
            )
        )->setValue(array( 
            "overlay" => "Header overlay", 
            "slideunder" => "Below header",
            "fullscreen" => "Full screen" ,
            "expand" => "Expand"
        ))->setValueCSS( array(
            "overlay"  => " form.oxy-header-search_form {
                                height: 100%;
                                left: 0;
                                right: 0;
                                max-height: 100%;
                                --slide-height: 100%;
                            }",
            "slideunder"  => " form.oxy-header-search_form {
                                    top: 100%;
                                    bottom: -100%;
                                    left: 0;
                                    right: 0;
                                    --slide-start: 0;
                                }

                                .oxy-header-search_form.visible {
                                    max-height: var(--slide-height);
                                }",
            
             "fullscreen"  => " form.oxy-header-search_form {
                                    position: fixed;
                                    height: 100%;
                                    left: 0;
                                    right: 0;
                                    max-height: 100%;
                                    --slide-height: 100%;
                                }",
            "expand"  => " {
                            position: relative;
                            height: 100%;
                            display: flex;
                            align-items: center;
                        }
                        

                        form.oxy-header-search_form {
                            position: absolute;
                            width: 0;
                            left: auto;
                            transform: translateY(-50%);
                            -webkit-transform: translateY(-50%);
                            top: 50%;
                            height: var(--expand-height);
                        }

                        .oxy-header-search_form.visible {
                            width: var(--expand-width);
                        }

                        .oxy-header-container {
                            padding: 0;
                        }"
        ) );
        
        
        /**
         * Reveal setting
         */ 
        $this->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => 'Reveal',
                'slug' => 'form_reveal',
                'condition' => 'search_type=slideunder'
            )
        )->setValue(array( 
            "fade" => "Fade", 
            "slide_down" => "Slide Down",
        ))->setDefaultValue('fade')
          ->setValueCSS( array(
            "slide_down"  => " .oxy-header-search_form {
                                    height: var(--slide-height);
                                    max-height: var(--slide-start);
                                }
                                
                                .oxy-header-search_form.visible {
                                    max-height: var(--slide-height);
                                }",
              "fade"  => "  .oxy-header-search_form {
                                    
                                    max-height: none;
                                }
                                
                                .oxy-header-search_form.visible {
                                    max-height: none;
                                }"
         
        ) );
        
        
        $form_selector = '.oxy-header-search_form';
        
        $this->addStyleControl( 
            array(
                "name" => 'Height',
                "type" => 'measurebox',
                "selector" => $form_selector,
                "units" => 'px',
                "property" => 'height',
                "control_type" => 'slider-measurebox',
                "condition" => 'search_type=slideunder&&form_reveal!=slide_down'
            )
        )
        ->setRange('0','300','1');
        
        
        $this->addStyleControl( 
            array(
                "name" => 'Height',
                "selector" => $form_selector,
                "units" => 'px',
                "property" => '--slide-height',
                "control_type" => 'slider-measurebox',
                "condition" => 'search_type=slideunder&&form_reveal=slide_down'
            )
        )
        ->setRange('0','300','1')
        ->setUnits('px');
        
        
        $this->addStyleControl( 
            array(
                "name" => 'Expanded width',
                "selector" => $form_selector,
                "property" => '--expand-width',
                "control_type" => 'slider-measurebox',
                "condition" => 'search_type=expand'
            )
        )
        ->setRange('0','400','1')
        ->setUnits('px');
        
        $this->addStyleControl( 
            array(
                "name" => 'Height',
                "selector" => $form_selector,
                "property" => '--expand-height',
                "control_type" => 'slider-measurebox',
                "condition" => 'search_type=expand'
            )
        )
        ->setRange('0','100','1')
        ->setUnits('px');
        
        
         $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Expand direction',
                'slug' => 'expand_direction',
                'condition' => 'search_type=expand'
            )
        )->setValue(array( 
            "left" => "Left", 
            "right" => "Right",
        ))->setDefaultValue('left')
          ->setValueCSS( array(
                "left"  => "",
                "right"  => ".oxy-header-search_form {
                                left: 0;
                                right: auto;
                            }",
        ) ); 
        
        
        
        
        
        
       $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Form Visibility in Builder',
                'slug' => 'search_in_builder'
            )
        )->setValue(array( 
            "display" => "Display", 
            "hidden" => "Hidden"
        ))->setDefaultValue('display')
           ->setValueCSS( array(
                "hidden"  => " .oxy-header-search_form {
                                    --builder-search-form: none;
                                }",
        ) ); 
        
        
        /**
         * Form Input
         */
        $form_input_section = $this->addControlSection("form_input_section", __("Form input"), "assets/icon.png", $this);
        $form_input_selector = '.oxy-header-search_search-field';
        
        // Placeholder Text
        $form_input_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Placeholder Text'),
                "slug" => 'placeholder_text',
                "default" => 'Search...',
                "base64" => true
            )
        )->rebuildElementOnChange();
        
        $form_input_section->addPreset(
            "padding",
            "form_input_padding",
            __("Padding"),
            $form_input_selector
        )->whiteList();
        
        
        $form_input_section->borderSection('Borders', $form_input_selector,$this);
        
        $form_input_colors_section = $form_input_section->addControlSection("form_input_colors_section", __("Colors"), "assets/icon.png", $this);
        
        $form_input_colors_section->addStyleControls(
             array( 
                 array(
                    "name" => 'Background Color',
                    "selector" => $form_input_selector,
                    "property" => 'background-color',
                     "default" => 'rgba(255,255,255,0)',
                ),
                 array(
                    "name" => 'Color',
                    "selector" => $form_input_selector,
                    "property" => 'color',
                ),
                array(
                    "name" => 'Hover Color',
                    "selector" => $form_input_selector.":hover",
                    "property" => 'color',
                ),
                 array(
                    "name" => 'Focus Color',
                    "selector" => $form_input_selector.":focus",
                    "property" => 'color',
                ),
                 array(
                    "name" => 'Placeholder Color',
                    "selector" => $form_input_selector."::placeholder",
                    "property" => 'color',
                     "default" => 'inherit',
                )
                 
            )
        );
        
        
        $form_input_section->boxShadowSection('Shadows', $form_input_selector,$this);
        $form_input_section->typographySection('Typography', $form_input_selector,$this);
        
        
       
         /**
         * Form Spacing
         */
        $form_spacing_section = $this->addControlSection("form_spacing_section", __("Form spacing"), "assets/icon.png", $this);
        
        $form_spacing_section->addPreset(
            "padding",
            "form_spacing_padding",
            __("Padding"),
            $form_selector
        )->whiteList();
        
        
        
        /**
         * Search Settings
         */
        $search_section = $this->addControlSection("search_section", __("Search Results"), "assets/icon.png", $this);
        
        
        // Custom Post Type
        $search_section->addOptionControl(
            array(
                "type" => 'checkbox',
                "name" => __('Search One Post Type Only'),
                "slug" => 'search_post_type_filter',
                "value" => 'false'
            )
        );
        
         // Custom Post Type
        $search_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Post Type Slug'),
                "slug" => 'post_type_slug',
                "condition" => 'search_post_type_filter=true',
                "default" => '',
            )
        );
       
        
        /**
         * Icon Search
         */
        
        $icon_search = $this->addControlSection("icon_search", __("Search Button"), "assets/icon.png", $this);
        $icon_search_selector = '.oxy-header-search_open-icon';
        
        $icon_search->addStyleControls(
            array(
                array(
                    "name" => __('Icon Color'),
                    "selector" => $icon_search_selector,
                    "property" => 'color',
                  
                ),
                array(
                    "name" => __('Hover Icon Color'),
                    "selector" => $icon_search_selector .":hover",
                    "property" => 'color',
                  
                ),
                array(
                    "name" => __('Focus Icon Color'),
                    "selector" => $icon_search_selector .":focus",
                    "property" => 'color',
                  
                ),
                array(
                    "name" => __('Icon Size'),
                    "selector" => $icon_search_selector,
                    "property" => 'font-size',
                   
                ),
            )
        );
        
        
        $icon_search->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Button text'),
                "slug" => 'button_text',
                "default" => '',
                "base64" => true
            )
        )->rebuildElementOnChange();
        
        
        
        
        $icon_search_change = $icon_search->addControlSection("icon_search_change", __("Change Icon"), "assets/icon.png", $this);
       
        $icon_search_change->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Search Icon'),
                "slug" => 'search_icon',
                "default" => 'Lineariconsicon-magnifier'
            )
        )->rebuildElementOnChange();
        
        
        
        $icon_search_spacing = $icon_search->addControlSection("icon_search_spacing", __("Icon Spacing"), "assets/icon.png", $this);
        
        $icon_search_spacing->addPreset(
            "margin",
            "icon_search_margin",
            __("Margin"),
            $icon_search_selector
        )->whiteList();
        
        
        $button_color_section = $icon_search->addControlSection("button_color_section", __("Button Colors"), "assets/icon.png", $this);
        
        $button_selector = '.oxy-header-search_toggle-open';  
        
        
        $button_color_section->addStyleControl(
                 array(
                    "name" => 'Color',
                    "selector" => $button_selector,
                    "property" => 'color',
                )
        )->setParam('hide_wrapper_end', true);
        
        $button_color_section->addStyleControl(
                array(
                    "name" => 'Hover Color',
                    "selector" => $button_selector.":hover",
                    "property" => 'color',
                )
        )->setParam('hide_wrapper_start', true);
        
        $button_color_section->addStyleControl(
                 array(
                    "name" => 'Focus Color',
                    "selector" => $button_selector.":focus",
                    "property" => 'color',
                )
        )->setParam('hide_wrapper_end', true);
        
        
        $button_color_section->addStyleControl(
                 array(
                    "name" => 'Background',
                    "selector" => $button_selector,
                    "property" => 'background-color',
                )
        )->setParam('hide_wrapper_start', true);
        
        $button_color_section->addStyleControl(
                array(
                    "name" => 'Hover Background',
                    "selector" => $button_selector.":hover",
                    "property" => 'background-color',
                )
        )->setParam('hide_wrapper_end', true);
        
        $button_color_section->addStyleControl(
                 array(
                    "name" => 'Focus Background',
                    "selector" => $button_selector.":focus",
                    "property" => 'background-color',
                )
        )->setParam('hide_wrapper_start', true);
        
        
        
        
        
        $button_spacing_section = $icon_search->addControlSection("button_spacing_section", __("Button Spacing"), "assets/icon.png", $this);
                  
            
        $button_spacing_section->addPreset(
            "margin",
            "button_margin",
            __("Margin"),
            $button_selector
        )->whiteList();
        
        $button_spacing_section->addPreset(
            "padding",
            "button_padding",
            __("Padding"),
            $button_selector
        )->whiteList();
        
        
        $button_layout_section = $icon_search->addControlSection("button_layout_section", __("Button Layout"), "assets/icon.png", $this);
        $button_layout_section->flex($button_selector, $this);
        
        $icon_search->typographySection('Button typography', $button_selector,$this);
        
        $icon_search->borderSection('Button borders', $button_selector,$this);
        $icon_search->boxShadowSection('Button shadows', $button_selector,$this);
        
        
        /**
         * Icon Close
         */
        $icon_close = $this->addControlSection("icon_close", __("Close Icon"), "assets/icon.png", $this);
        $icon_close_selector = '.oxy-header-search_close-icon';
        
        $icon_close->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Icon Display',
                'slug' => 'icon_close_display'
            )
        )->setValue(array( 
            "display" => "Display", 
            "hide" => "Hide"
        ))->setDefaultValue('display')->rebuildElementOnChange();
        
        $icon_close->addStyleControls(
            array(
                array(
                    "name" => __('Icon Color'),
                    "selector" => $icon_close_selector,
                    "property" => 'color',
                    "condition" => 'icon_close_display=display'
                  
                ),
                array(
                    "name" => __('Hover Icon Color'),
                    "selector" => $icon_close_selector .":hover",
                    "property" => 'color',
                    "condition" => 'icon_close_display=display'
                  
                ),
                array(
                    "name" => __('Focus Icon Color'),
                    "selector" => $icon_close_selector .":focus",
                    "property" => 'color',
                    "condition" => 'icon_close_display=display'
                  
                ),
                array(
                    "name" => __('Icon Size'),
                    "selector" => $icon_close_selector,
                    "property" => 'font-size',
                    "condition" => 'icon_close_display=display'
                   
                ),
            )
        );
        
        $icon_close_change = $icon_close->addControlSection("icon_close_change", __("Change Icon"), "assets/icon.png", $this);
        
        $icon_close_change->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Close Icon'),
                "slug" => 'close_icon',
                "default" => 'Lineariconsicon-cross',
                "condition" => 'icon_close_display=display'
            )
        )->rebuildElementOnChange();
        
        $icon_close_spacing = $icon_close->addControlSection("icon_close_spacing", __("Spacing"), "assets/icon.png", $this);
        
        $icon_close_spacing->addPreset(
            "margin",
            "icon_close_margin",
            __("Margin"),
            $icon_close_selector
        )->whiteList();
        
        
        
        
        $this->addStyleControls(
             array( 
                array(
                    "name" => 'Form Background Color',
                    "selector" => $form_selector,
                    "property" => 'background-color',
                    "default" => '#f3f3f3',
                )
                 
            )
        );
        
        
        
        $transition = $this->addStyleControl(
            array(
                "name" => __('Transition Duration'),
                "property" => 'transition-duration',
                "selector" => $form_selector,
                "control_type" => 'slider-measurebox',
                "default" => '.3',
            )
        );

        $transition->setUnits('s','s');
        $transition->setRange(.1, 1, .05);
        
        
        $this->addOptionControl(
            array(
                "type" => "dropdown",
                "name" => "Search Container width",
                "slug" => "container_width",
                "default" => 'header',
                "condition" => 'search_type!=expand'
            )
        )->setValue(
           array( 
                "header" => "Header row width", 
                "full" => "Full width",
               //"custom" => "Custom",
               
           )
       )->setValueCSS( array(
            "header"  => " {
                        
                        }",
            "full"  => " .oxy-header-container {
                            max-width: 100%;
                        }"
        ) );



        $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Prevent scroll when search open'),
                'slug' => 'prevent_scroll'
            )
        )->setValue(array( 
            "true" => "Enable", 
            "false" => "Disable"
        ))->setDefaultValue('false');


        $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Require input filled out before submitting'),
                'slug' => 'maybe_require'
            )
        )->setValue(array( 
            "true" => "Enable", 
            "false" => "Disable"
        ))->setDefaultValue('false');
        
        
         /**
         * Icon Close
         */
        $accessibility_section = $this->addControlSection("accessibility_section", __("Accessibility"), "assets/icon.png", $this);
        
        $accessibility_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Icon focus outlines',
                'slug' => 'maybe_outlines',
            )
        )->setValue(array( 
            "enable" => "Enable", 
            "disable" => "Disable",
        ))->setDefaultValue('enable')
          ->setValueCSS( array(
                "disable"  => ".oxy-header-search_toggle:focus {
                    outline: none;
                }",
        ) )->setParam("description", __("If disabled it's recommend to add focus colors so focus state is still visible"));
        
        
        
        $accessibility_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Search icon aria label'),
                "slug" => 'search_icon_aria',
                "default" => 'Open search',
                "base64" => true
            )
        );
        
        $accessibility_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Close icon aria label'),
                "slug" => 'close_icon_aria',
                "default" => 'Close search',
                "base64" => true
            )
        );
        
        
        
        
    }
    
    
    function customCSS($options, $selector) {
        
        $css = "";
        
        if( ! $this->css_added ) {
        
            $css .= ".oxy-header-search svg {
                        width: 1em;
                        height: 1em;
                        fill: currentColor;
                    }
                    
                    .oxy-header-search_toggle-open {
                        display: flex;
                    }

                    .oxy-header-search_search-field:focus {
                        outline: none;
                    }
                    
                    .woocommerce input.oxy-header-search_search-field[type=search]:focus {
                        outline: none;
                        border: none;
                        box-shadow: none;
                    }

                    .oxy-header-search label {
                        width: 100%;
                    }

                    .oxy-header-search .screen-reader-text {
                        border: 0;
                        clip: rect(0, 0, 0, 0);
                        height: 1px;
                        overflow: hidden;
                        position: absolute !important;
                        width: 1px;
                        word-wrap: normal !important;
                    }

                    .oxy-header-search_toggle {
                        cursor: pointer;
                        background: none;
                        border: none;
                        color: inherit;
                    }

                    .oxy-header-search input[type=submit] {
                        display: none;
                    }

                    .oxy-header-search_form {
                        background: #f3f3f3;
                        position: absolute;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        top: 0;
                        opacity: 0;
                        overflow: hidden;
                        visibility: hidden;
                        z-index: 99;
                        transition: all .3s ease;
                    }

                    .oxy-header-search_search-field,
                    .woocommerce input.oxy-header-search_search-field[type=search] {
                        background: rgba(255,255,255,0);
                        font-family: inherit;
                        border: none;
                        width: 100%;
                    }

                    .oxy-header-search_form.visible {
                        opacity: 1;
                        visibility: visible;
                    }

                    .oxy-header-search .oxy-header-container {
                        display: flex;
                        align-items: center;
                    }
                    
                    .oxygen-builder-body .oxy-header-search_form {
                        display: var(--builder-search-form);
                    }

                    html.oxy-header-search_prevent-scroll,
                    body.oxy-header-search_prevent-scroll {
                        overflow: hidden;
                        height: 100%;
                    }

                    .oxy-header-search_search-field {
                        -webkit-appearance: none;
                    }
                    
                    ";
            
            $this->css_added = true;
            
        }

        return $css;
        
    }
    
    /**
     * Output js inline in footer once.
     */
    function output_js() { ?>
            
            <script type="text/javascript">
            jQuery(document).ready(oxygen_init_search);
            function oxygen_init_search($) {

                  
                $('body').on( 'click', '.oxy-header-search_toggle', function(e) {           
                        e.preventDefault();
                        let $toggle = $(this);
                        let $form = $toggle.closest('.oxy-header-search').find('.oxy-header-search_form');
                        
                        
                        if (!$form.hasClass('visible')) {
                            showSearch($toggle);
                            
                        } else {
                            hideSearch($toggle);
                        }
                    }
                );
                
                // Tabbing out will close search
                $('.oxy-header-search_toggle').next('.oxy-header-search_form').find('input[type=search]').on('keydown', function (event) {
                    
                    let togglebutton = $('.oxy-header-search_toggle');

                    if (event.keyCode === 9) {
                      hideSearch(togglebutton);
                    }

                });
                
                // Pressing ESC will close search
                $('.oxy-header-search_toggle').next('.oxy-header-search_form').find('input[type=search]').keyup(function(e){
                    
                    let togglebutton = $('.oxy-header-search_toggle');
                    if(e.keyCode === 27) {
                      hideSearch(togglebutton);
                    } 
                  });
                
                
                // Helper function to show the search form.
                function showSearch(toggle) {
                    
                    toggle.closest('.oxy-header-search').find('.oxy-header-search_form').addClass('visible');

                    if (true === toggle.closest('.oxy-header-search').find('.oxy-header-search_toggle-open').data('prevent-scroll')) {
                        $('html,body').addClass('oxy-header-search_prevent-scroll');
                    }

                    setTimeout(
                    function() {
                        toggle.closest('.oxy-header-search').find('input[type=search]').focus();
                    }, 300);

                }

                // Helper function to hide the search form.
                function hideSearch(toggle) {

                    toggle.closest('.oxy-header-search').find('.oxy-header-search_form').removeClass('visible');
                    $('html,body').removeClass('oxy-header-search_prevent-scroll');

                    setTimeout(
                    function() {
                    toggle.closest('.oxy-header-search').find('.oxy-header-search_toggle-open').focus();
                    }, 0);

                }

             };
            
        </script>

    <?php }

}

new ExtraSearchForm();