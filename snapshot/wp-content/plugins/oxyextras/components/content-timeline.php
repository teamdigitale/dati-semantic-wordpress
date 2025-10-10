<?php

class ExtraContentTimeline extends OxygenExtraElements {

    var $js_added = false;
    var $css_added = false;
        
    function name() {
        return 'Content Timeline';
    }
   
    function extras_button_place() {
        return "interactive";
    }
    
    function tag() {
        return array('default' => 'div');
    }
    
    function icon() {
        return plugin_dir_url(__FILE__) . 'assets/icons/'.basename(__FILE__, '.php').'.svg';
    }
    
    function init() {
        
        $this->enableNesting();
    
    }

    function render($options, $defaults, $content) {

        $dynamic = function ($textfield) {
            $field = isset( $textfield ) ? $textfield : '';
            if( strstr( $field, '[oxygen') ) {                
                $field = ct_sign_oxy_dynamic_shortcode(array($field));
                $field_out =  esc_html(do_shortcode($field));
            } else {
                $field_out = esc_html($textfield);
            }
            return $field_out;
        };
        
        $meta_content = $options['meta_content'] ? $dynamic($options['meta_content']) : '';
        $marker_text = $options['marker_text'] ? $dynamic($options['marker_text']) : '';

        $scroll_position = isset( $options['scroll_position'] ) ? esc_attr($options['scroll_position']) : "50%";

         // Get Options
         $icon  = isset( $options['icon'] ) ? esc_attr($options['icon']) : "";
         

         global $oxygen_svg_icons_to_load;
         $oxygen_svg_icons_to_load[] = $icon;
        
        echo '<div class="oxy-content-timeline_inner" ';
        
        if ( isset( $options['maybe_scroll_animations'] ) && esc_attr($options['maybe_scroll_animations'])  === 'enable' ) {
            echo 'data-scroll="true" data-scroll-position="'. $scroll_position .'" ';
        }
        
        echo '>';
        echo '<div class="oxy-content-timeline_content">';
        echo '<div class="oxy-content-timeline_content-inner oxy-inner-content">';
        
        if ($content) {
            if ( function_exists('do_oxygen_elements') ) {
                echo do_oxygen_elements($content); 
            }
            else {
                echo do_shortcode($content); 
            } 
        }

        echo '</div>';
        echo '</div>';

        echo '<div class="oxy-content-timeline_marker"><div class="oxy-content-timeline_marker-inner">';

        if ('icon' === $options['marker_content']) {
            echo '<svg class="oxy-content-timeline_icon" id="' . esc_attr($options['selector']) . '-icon"><use xlink:href="#' . $icon .'"></use></svg>';
        } else if ('text' === $options['marker_content']) {
            echo $marker_text;
        } else {
            echo '<span class="oxy-content-timeline_counter"></span>';
        }

        echo '</div></div>';

        echo '<div class="oxy-content-timeline_meta"><div class="oxy-content-timeline_meta-inner">' . html_entity_decode($meta_content);

        echo '</div></div>';

        echo '</div>';


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
        return array();
    }

    function controls() {

        $content_selector = '.oxy-content-timeline_content-inner';
        $marker_selector = '.oxy-content-timeline_marker-inner';
        $meta_selector = '.oxy-content-timeline_meta';
        $icon_svg_selector = '.oxy-content-timeline_icon'; 

        $active_content_selector = '.oxy-content-timeline_active .oxy-content-timeline_content-inner';
        $active_marker_selector = '.oxy-content-timeline_active .oxy-content-timeline_marker-inner';
        $active_meta_selector = '.oxy-content-timeline_active .oxy-content-timeline_meta';
        $active_icon_svg_selector = '.oxy-content-timeline_active .oxy-content-timeline_icon'; 


       

        /**
         * Content
         */
        $this->addStyleControl( 
            array(
                "name" => __('Content width (flex-basis)'), 
                "default" => '50',
                "property" => '--timeline-content-width',
                "control_type" => 'slider-measurebox',
                "selector" => ' ',
            )
        )
        ->setRange('0','100','1')
        ->setUnits('%');


         /**
         * Meta Content
         */
        $this->addStyleControl( 
            array(
                "name" => __('Meta content width (flex-basis)'), 
                "property" => '--timeline-metacontent-width',
                "default" => '50',
                "control_type" => 'slider-measurebox',
                
            )
        )
        ->setRange('0','100','1')
        ->setUnits('%');

        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Meta content'),
                "slug" => 'meta_content',
                "default" => ''
            )
        )->setParam('dynamicdatacode', '<div optionname="\'oxy-content-timeline_meta_content\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');
        

        
        $this->addStyleControl(
            array(
                "control_type" => 'buttons-list',
                'name' => __('Flex direction'),
                "property" => '--timeline-flex-layout',
                "default" => ''
            )
        )->setValue(array( 
            "row" => "Row", 
            "row-reverse" => "Row reverse"
         ))->whiteList();


         $this->addStyleControl(
            array(
                "control_type" => 'buttons-list',
                'name' => __('Meta content text align'),
                "property" => '--timeline-meta-align',
                "default" => ''
            )
        )->setValue(array( 
            "left" => "Left", 
            "right" => "Right"
         ))->whiteList();

        



        /**
         * Content
         */
        $content_section = $this->addControlSection("content_section", __("Content"), "assets/icon.png", $this);

        $content_section->addPreset(
            "padding",
            "content_padding",
            __("Padding"),
            $content_selector
        )->whiteList();
        
        $content_section->addPreset(
            "margin",
            "content_margin",
            __("Margin"),
            $content_selector
        )->whiteList();

        $content_section->addStyleControl(
            array(
                "name" => __('Background'),
                "property" => '--timeline-content-background',
                "control_type" => 'colorpicker',
            )
        )->setParam('hide_wrapper_end', true);
        $content_section->addStyleControl(    
                array(
                    "name" => __('Active Background'),
                    "property" => '--timeline-content-backgrounda',
                    "control_type" => 'colorpicker',
                )
        )->setParam('hide_wrapper_start', true);

        $content_section->addStyleControl(
            array(
                "name" => __('Color'),
                "property" => '--timeline-content-color',
                "control_type" => 'colorpicker',
            )
        )->setParam('hide_wrapper_end', true);
        $content_section->addStyleControl(    
                array(
                    "name" => __('Active Color'),
                    "property" => '--timeline-content-colora',
                "control_type" => 'colorpicker',
                )
        )->setParam('hide_wrapper_start', true);


        $content_section->borderSection('Borders', $content_selector,$this);
        $content_section->boxShadowSection('Shadows', $content_selector,$this);
        $content_section->borderSection('Borders (active)', $active_content_selector,$this);
        $content_section->boxShadowSection('Shadows (active)', $active_content_selector,$this);
        $content_section->typographySection('Typography', $content_selector,$this);


         /**
         * Marker Content
         */
        $marker_content_section = $this->addControlSection("marker_content_section", __("Marker"), "assets/icon.png", $this);


        $marker_content_section->addStyleControl(
            array(
                "name" => __('Background'),
                "property" => '--timeline-marker-background',
                "control_type" => 'colorpicker',
            )
        )->setParam('hide_wrapper_end', true);
        $marker_content_section->addStyleControl(    
                array(
                    "name" => __('Active Background'),
                    "property" => '--timeline-marker-backgrounda',
                    "control_type" => 'colorpicker',
                )
        )->setParam('hide_wrapper_start', true);

        $marker_content_section->addStyleControl(
            array(
                "name" => __('Color'),
                "property" => '--timeline-marker-color',
                "control_type" => 'colorpicker',
            )
        )->setParam('hide_wrapper_end', true);
        $marker_content_section->addStyleControl(    
                array(
                    "name" => __('Active Color'),
                    "property" => '--timeline-marker-colora',
                "control_type" => 'colorpicker',
                )
        )->setParam('hide_wrapper_start', true);


        $marker_content_section->addStyleControl( 
            array(
                "name" => __('Scale'),
                "property" => '--timeline-marker-scale',
                "control_type" => 'slider-measurebox',
                "default" => '1',
            )
        )
        ->setRange('0.6','1.4','.01');

        $marker_content_section->addStyleControl( 
            array(
                "name" => __('Active scale'),
                "property" => '--timeline-marker-scalea',
                "control_type" => 'slider-measurebox',
                "default" => '1',
            )
        )
        ->setRange('0.6','1.4','.01');


        $marker_spacing = $marker_content_section->addControlSection("marker_spacing", __("Size / Spacing"), "assets/icon.png", $this);
        $marker_spacing->addPreset(
            "padding",
            "marker_padding",
            __("Padding"),
            $marker_selector
        )->whiteList();
        
        $marker_spacing->addPreset(
            "margin",
            "marker_margin",
            __("Margin"),
            $marker_selector
        )->whiteList();

        $marker_spacing->addStyleControl(
            array(
                "property" => 'height',
                "selector" => $marker_selector,
                "default" => '40'
            )
        );

        $marker_spacing->addStyleControl(
            array(
                "property" => 'width',
                "selector" => $marker_selector,
                "default" => '40'
            )
        );

        $marker_content_section->borderSection('Borders', $marker_selector,$this);
        $marker_content_section->borderSection('Borders (active)', $active_marker_selector,$this);
        $marker_content_section->boxShadowSection('Shadows', $marker_selector,$this);
        $marker_content_section->boxShadowSection('Shadows (active)', $active_marker_selector,$this);
        $marker_content_section->typographySection('Typography', $marker_selector,$this);
        $marker_content_section->typographySection('Typography', $marker_selector,$this);


        

        $marker_content_section->addStyleControl(
            array(
                "control_type" => 'buttons-list',
                'name' => __('Marker vertical align'),
                "property" => '--timeline-marker-align',
                "default" => ''
            )
        )->setValue(array( 
            "flex-start" => "Flex start", 
            "center" => "Center",
            "flex-end" => "Flex end", 
         ))->whiteList();
        
        $marker_content_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Marker content'),
                'slug' => 'marker_content'
            )
        )->setValue(array( 
            "icon" => "Icon", 
            "text" => "Text" ,
            "counter" => "Counter" 
        ));

        $marker_content_section->addStyleControl(
                array(
                    "name" => __('Icon Size'),
                    "control_type" => 'slider-measurebox',
                    "default" => '24',
                    "property" => '--timeline-icon-size',
                    "condition" => 'marker_content=icon',
                    "selector" => ''
                )
        )->setRange('4', '72', '1')
         ->setUnits('px');
        
        $marker_content_section->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Icon'),
                "slug" => 'icon',
                "value" => 'FontAwesomeicon-calendar-o', 
                "condition" => 'marker_content=icon',
            )
        );

        $marker_content_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Marker content'),
                "slug" => 'marker_text',
                "default" => '',
                "condition" => 'marker_content=text',
            )
        )->setParam('dynamicdatacode', '<div optionname="\'oxy-content-timeline_marker_text\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');
        

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

        $marker_content_section->addStyleControl(
            array(
                "name" => __('List Style Type'),
                "property" => '--timeline-list-type',
                "control_type" => 'dropdown',
                "condition" => 'marker_content=counter',
                "default" => 'decimal'
            )
        )->setValue( $list_style_type_options );


        /**
         * Meta Content
         */
        $meta_content_section = $this->addControlSection("meta_content_section", __("Meta Content"), "assets/icon.png", $this);

        $meta_content_section->addPreset(
            "padding",
            "meta_padding",
            __("Padding"),
            $meta_selector
        )->whiteList();
        
        $meta_content_section->addPreset(
            "margin",
            "meta_margin",
            __("Margin"),
            $meta_selector
        )->whiteList();

        $meta_content_section->addStyleControl(
            array(
                "control_type" => 'buttons-list',
                'name' => __('Meta content display'),
                "property" => 'display',
                "selector" => $meta_selector,
                "default" => 'block'
            )
        )->setValue(array( 
            "block" => "Show", 
            "none" => "Hide"
        ));

        $meta_content_section->borderSection('Borders', $meta_selector,$this);
        $meta_content_section->boxShadowSection('Shadows', $meta_selector,$this);
        $meta_content_section->typographySection('Typography', $meta_selector,$this);


        /**
         * Meta inner
         */
        $meta_inner_section = $this->addControlSection("meta_inner_section", __("Meta Inner"), "assets/icon.png", $this);

        $meta_inner_selector = '.oxy-content-timeline_meta-inner';

        $meta_inner_section->addStyleControl( 
            array(
                "name"     => __('Width'),
                "property" => '--timeline-meta-width',
                "control_type" => 'slider-measurebox',
            )
        )
        ->setRange('0','500','1')
        ->setUnits('px');

        $meta_inner_section->addPreset(
            "padding",
            "meta_inner_padding",
            __("Padding"),
            $meta_inner_selector
        )->whiteList();
        
        $meta_inner_section->addPreset(
            "margin",
            "meta_inner_margin",
            __("Margin"),
            $meta_inner_selector
        )->whiteList();

        $meta_inner_section->addStyleControl(
            array(
                "name" => __('Background'),
                "property" => '--timeline-meta-background',
                "control_type" => 'colorpicker',
            )
        )->setParam('hide_wrapper_end', true);
        $meta_inner_section->addStyleControl(    
                array(
                    "name" => __('Active Background'),
                    "property" => '--timeline-meta-backgrounda',
                    "control_type" => 'colorpicker',
                )
        )->setParam('hide_wrapper_start', true);

        $meta_inner_section->addStyleControl(
            array(
                "name" => __('Color'),
                "property" => '--timeline-meta-color',
                "control_type" => 'colorpicker',
            )
        )->setParam('hide_wrapper_end', true);
        $meta_inner_section->addStyleControl(    
                array(
                    "name" => __('Active Color'),
                    "property" => '--timeline-meta-colora',
                "control_type" => 'colorpicker',
                )
        )->setParam('hide_wrapper_start', true);


        $meta_inner_section->borderSection('Borders', $meta_inner_selector,$this);
        $meta_inner_section->boxShadowSection('Shadows', $meta_inner_selector,$this);
        $meta_inner_section->typographySection('Typography', $meta_inner_selector,$this);

        


        /**
         * Line
         */
        $line_section = $this->addControlSection("line_section", __("Line"), "assets/icon.png", $this);
        $line_section->addStyleControl( 
            array(
                "name" => __('line width'),
                "property" => '--timeline-line-width',
                "default" => '3',
                "control_type" => 'slider-measurebox',
                
            )
        )
        ->setRange('0','10','1')
        ->setUnits('px');

        $line_section->addStyleControl(
            array(
                "name" => __('Color'),
                "property" => '--timeline-line-color',
                "control_type" => 'colorpicker',
            )
        )->setParam('hide_wrapper_end', true);
        $line_section->addStyleControl(    
                array(
                    "name" => __('Active Color'),
                    "property" => '--timeline-line-colora',
                    "control_type" => 'colorpicker',
                )
        )->setParam('hide_wrapper_start', true);


         /*
        Scroll animation
        */
        $visibility_oxygen = $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Add active classes on scroll'),
                'slug' => 'maybe_scroll_animations'
            )
        )->setValue(array( 
            "enable" => "Enable", 
            "disable" => "Disable" 
        ));
        $visibility_oxygen->setDefaultValue('disable');


        $this->addOptionControl( 
            array(
                "type" => 'slider-measurebox',
                "name" => __('Position from top of viewport'),
                "slug" => 'scroll_position',
                "default" => 50,
                "condition" => 'maybe_scroll_animations=enable',
            )
        )
        ->setRange('0','100','1')
        ->setUnits('%');
       

    }


    function customCSS($options, $selector) {
        
        $css = "";
        
        if( ! $this->css_added ) {

            $css .= "

            :root {
                --timeline-content-width: 50%;
                --timeline-metacontent-width: 50%;
                --timeline-line-width: 2px;
                --timeline-line-color: #eee;
                --timeline-marker-scale: 1;
                --timeline-flex-layout: row;
                --timeline-meta-align: left;
                --timeline-marker-align: center;
                --timeline-marker-background: #ddd;
                --timeline-marker-color: #fff;
                --timeline-icon-size: 16px;
                --timeline-content-background: inherit;
                --timeline-content-color: inherit;
                --timeline-list-type: decimal;
            }
            
            .oxy-content-timeline {    
                width: 100%;
                --timeline-content-backgrounda: var(--timeline-content-background);
                --timeline-content-colora: var(--timeline-content-color);
                --timeline-meta-backgrounda: var(--timeline-meta-background);
                --timeline-meta-colora: var(--timeline-meta-color);
                --timeline-marker-backgrounda: var(--timeline-marker-background);
                --timeline-marker-colora: var(--timeline-marker-color);
                --timeline-line-colora: var(-timeline-line-color);
                --timeline-marker-scalea: var(--timeline-marker-scale);
            }

            .oxy-content-timeline_line {
                position: absolute;
                width: var(--timeline-line-width);
                top: 0;
                bottom: 0;
                display: none;
                opacity: 0;
                background-color: var(--timeline-line-color);
                left: 50%;
                transform: translate3d(-50%,0,0);
                -webkit-transform: translateX(-50%,0,0);
            }
            
            [data-content-timeline='active'] .oxy-content-timeline_line {
                display: block;
            }

            [data-content-timeline='active'] > .ct-div-block:last-of-type {
                margin-bottom: 0!important;
            }

            [data-content-timeline='active'] .oxy-content-timeline_marker::before {
                content: none;
            }
            
            .oxy-content-timeline_line-active {
                position: absolute;
                width: 100%;
                top: 0;
                left: 0;
                height: 100%;
                background-color: var(--timeline-line-colora);
                will-change: transform;
                transform-origin: top;
                transform: scaleY(0);
            }
            
            .oxy-content-timeline_icon {
                height: 1em;
                width: 1em;
                fill: currentColor;
            }
            
            .oxy-content-timeline_inner {
                display: flex;
                align-items: var(--timeline-marker-align);
                position: relative;
                flex-direction: var(--timeline-flex-layout);
            }
            
            
            .oxy-content-timeline:last-of-type {
                margin-bottom: 0;
            }
            
            .oxy-content-timeline_marker {
                flex-shrink: 0;
                flex-grow: 0;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .oxy-content-timeline_icon {
                font-size: var(--timeline-icon-size);
            }
            
            .oxy-content-timeline_marker-inner {
                background: var(--timeline-marker-background);
                color: var(--timeline-marker-color);
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 100%;
                width: 40px;
                height: 40px;
                margin: 20px;
                position: relative;
                z-index: 100;
                border: 0px solid;
                transform: translateZ(0) scale(var(--timeline-marker-scale));
                transition-property: transform, background, color;
                transition-duration: .2s;
                transition-timing-function: ease;
            }

            .oxy-content-timeline_active .oxy-content-timeline_marker-inner {
                transform: translateZ(0) scale(var(--timeline-marker-scalea));
                background: var(--timeline-marker-backgrounda);
                color: var(--timeline-marker-colora);
            }
            
            .oxy-content-timeline_marker::before {
                position: absolute;
                top: 0;
                bottom: -100px;
                width: var(--timeline-line-width);
                content: '';
                background-color: var(--timeline-line-color)
            }

            [data-content-timeline='active'] {
                position: relative;
            }
            
            .oxy-dynamic-list > .ct-div-block:first-of-type .oxy-content-timeline_marker::before,
            .oxy-content-timeline:not(.oxy-dynamic-list .oxy-content-timeline):first-of-type .oxy-content-timeline_marker::before {
                top: 50%;
            }
            
            .oxy-dynamic-list > .ct-div-block:last-of-type .oxy-content-timeline_marker::before,
            .oxy-content-timeline:not(.oxy-dynamic-list .oxy-content-timeline):last-of-type .oxy-content-timeline_marker::before {
                bottom: 50%;
            }
            
            .oxy-content-timeline_content {
                flex-basis: var(--timeline-content-width);
                min-width: 25%;
            }
            
            .oxy-content-timeline_content-inner {
                border: 1px solid #eee;
                padding: 30px;
                background-color: var(--timeline-content-background);
                color: var(--timeline-content-color);
                transition-properties: transform, background, color;
                transition-duration: .2s;
                transition-timing-function: ease;
            }

            .oxy-content-timeline_active .oxy-content-timeline_content-inner {
                background-color: var(--timeline-content-backgrounda);
                color: var(--timeline-content-colora);
            }
            
            .oxy-content-timeline_meta {
                flex-basis: var(--timeline-metacontent-width);
                text-align: var(--timeline-meta-align);
                justify-content: var(--timeline-meta-align);
                display: flex;
            }

            .oxy-content-timeline_meta-inner {
                width: var(--timeline-meta-width);
                background-color: var(--timeline-meta-background);
                color: var(--timeline-meta-color);
                transition-properties: transform, background, color;
                transition-duration: .2s;
                transition-timing-function: ease;
            }

            .oxy-content-timeline_active .oxy-content-timeline_meta-inner {
                background-color: var(--timeline-meta-backgrounda);
                color: var(--timeline-meta-colora);
            }

            .oxy-content-timeline:first-of-type {
                counter-reset: extras_timeline_items;
            }
             
            .oxy-content-timeline {
                 counter-increment: extras_timeline_items;
             }

            .oxy-content-timeline_counter::before {
                content: counter(extras_timeline_items, var(--timeline-list-type));
            }
            
            .oxy-dynamic-list[data-content-timeline='active'] {
                counter-reset: extras_timeline_items;
            }

            .oxy_dynamic_list {
                counter-reset: extras_timeline_items;
            }

            .oxy-dynamic-list[data-content-timeline='active'] .oxy-content-timeline:first-of-type {
                counter-reset: none;
            }

            .oxy_dynamic_list .oxy-content-timeline:first-of-type {
                counter-reset: none;
            }

            :where(.oxy-dynamic-list[data-alternating='even'] > .ct-div-block:nth-of-type(even) ) .oxy-content-timeline {
                --timeline-flex-layout: row-reverse;
            }

            :where(.oxy-dynamic-list[data-alternating='even'] > .ct-div-block:nth-of-type(even) ) .oxy-content-timeline {
                --timeline-meta-align: right;
            }

            :where(.oxy-dynamic-list[data-alternating='odd'] > .ct-div-block:nth-of-type(odd) ) .oxy-content-timeline {
                --timeline-flex-layout: row-reverse;
            }

            :where(.oxy-dynamic-list[data-alternating='odd'] > .ct-div-block:nth-of-type(odd) ) .oxy-content-timeline {
                --timeline-meta-align: right;
            }

            :where(.oxygen-builder-body .oxy-dynamic-list[data-alternating='odd'] > .ct-div-block:nth-of-type(2n) ) .oxy-content-timeline {
                --timeline-flex-layout: row-reverse;
            }
            
            :where(.oxygen-builder-body .oxy-dynamic-list[data-alternating='odd'] > .ct-div-block:nth-of-type(2n) ) .oxy-content-timeline {
                --timeline-meta-align: right;
            }
            
            :where(.oxygen-builder-body .oxy-dynamic-list[data-alternating='odd'] > .ct-div-block:nth-of-type(2n+1):not(:nth-child(1)) ) .oxy-content-timeline {
                --timeline-flex-layout: row;
            }
            
            :where(.oxygen-builder-body .oxy-dynamic-list[data-alternating='odd'] > .ct-div-block:nth-of-type(2n+1):not(:nth-child(1)) ) .oxy-content-timeline {
                --timeline-meta-align: left;
            }
            
            
            :where(.oxygen-builder-body .oxy-dynamic-list[data-alternating='even'] > .ct-div-block:nth-of-type(2n+1):not(:nth-child(1)) ) .oxy-content-timeline {
                --timeline-flex-layout: row-reverse;
            }
            
            :where(.oxygen-builder-body .oxy-dynamic-list[data-alternating='even'] > .ct-div-block:nth-of-type(2n+1):not(:nth-child(1)) ) .oxy-content-timeline {
                --timeline-meta-align: right;
            }
            
            :where(.oxygen-builder-body .oxy-dynamic-list[data-alternating='even'] > .ct-div-block:nth-of-type(even) ) .oxy-content-timeline {
                --timeline-flex-layout: row;
            }
            
            :where(.oxygen-builder-body .oxy-dynamic-list[data-alternating='even'] > .ct-div-block:nth-of-type(even) ) .oxy-content-timeline {
                --timeline-meta-align: left;
            }
            
            ";

            $this->css_added = true;
            
        }
        
        return $css;
    }


    function output_js() {
        wp_enqueue_script( 'timeline-js', plugin_dir_url(__FILE__) . 'assets/timeline-init.js', '', '1.0.3' );
    }


}

// All the parameters that can contain dynamic data, should be added to this filter
add_filter("oxy_base64_encode_options", 
    function($items) { 
        $items=array_merge($items, array('oxy-content-timeline_meta_content','oxy-content-timeline_marker_text')); 
        return $items;
    }
);

new ExtraContentTimeline();