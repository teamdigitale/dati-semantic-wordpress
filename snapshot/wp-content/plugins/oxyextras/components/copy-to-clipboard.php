<?php
class ExtraCopyToClipboard extends OxygenExtraElements {

    var $js_added = false;
    var $js_tooltip_added = false;
    var $css_added = false;
        
    function name() {
        return 'Copy to Clipboard';
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
    
    
    
    function init() {
        
        $this->enableNesting();
    
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
        
        $label_text = $dynamic($options['label_text']);
        $label_text_copied = $dynamic($options['label_text_copied']);

        $maybe_button_text = isset( $options['maybe_button_text'] ) ? 'enable' === esc_attr($options['maybe_button_text']) : true;
            
        $icon = isset( $options['icon'] ) ? esc_attr($options['icon']) : "";
        $copied_icon = isset( $options['copied_icon'] ) ? esc_attr($options['copied_icon']) : "";

        $copy_completed = isset( $options['copy_completed'] ) ? esc_attr($options['copy_completed']) : "animatedCheck";
        
        $inbuilder = (defined('OXY_ELEMENTS_API_AJAX') && OXY_ELEMENTS_API_AJAX) ? ' extras-in-builder oxy-inner-content' : '';
        
        $popover_placement = isset( $options['popover_placement'] ) ? esc_attr($options['popover_placement']) : "";
        $maybe_tooltip = isset( $options['maybe_tooltip'] ) ? 'enable' === esc_attr($options['maybe_tooltip']) : false;
        $tooltip_show = isset( $options['tooltip_show'] ) ? esc_attr($options['tooltip_show']) : 'hocus';

        $maybe_showoncreate = isset( $options['maybe_showoncreate'] ) ? esc_attr($options['maybe_showoncreate']) : "";
        
        $offset_x = isset( $options['offset_x'] ) ? esc_attr($options['offset_x']) : "0";
        $offset_y = isset( $options['offset_y'] ) ? esc_attr($options['offset_y']) : "10";
        
        $element_selector = isset( $options['element_selector'] ) ? esc_attr($options['element_selector']) : "";
        
        $prevent_dom_change = isset( $options['prevent_dom_change'] ) ? esc_attr($options['prevent_dom_change']) : "";
        
        $popover_content_source = isset( $options['popover_content_source'] ) ? esc_attr($options['popover_content_source']) : "";
        
        $interaction = isset( $options['interaction'] ) ? esc_attr($options['interaction']) : "";

        $move_transition = isset( $options['move_transition'] ) ? esc_attr($options['move_transition']) : "";

        $aria_label = isset( $options['aria_label'] ) ? esc_attr($options['aria_label']) : "";

        $text_to_copy = isset( $options['text_to_copy'] ) ? esc_attr($options['text_to_copy']) : "selector";
        $copy_selector = isset( $options['copy_selector'] ) ? $dynamic($options['copy_selector']) : "";
        $copy_text = isset( $options['copy_text'] ) ? $dynamic($options['copy_text']) : "";
        $icon_animation = isset( $options['icon_animation'] ) ? esc_attr( $options['icon_animation'] ) : 'fade';

        $tooltip_text = isset( $options['tooltip_text'] ) ? $dynamic( $options['tooltip_text'] ) : "Copy";
        $tooltip_text_copied = isset( $options['tooltip_text_copied'] ) ? $dynamic( $options['tooltip_text_copied'] ) : "Copied";
        $maybe_hide = isset( $options['maybe_hide'] ) ? 'true' === esc_attr($options['maybe_hide']) : false;
        $state_delay = isset( $options['state_delay'] ) ? intval( $options['state_delay'] ) : 3000;
        $select_text = isset( $options['select_text'] ) ? 'true' === esc_attr($options['select_text']) : false;
        
        global $oxygen_svg_icons_to_load;
        $oxygen_svg_icons_to_load[] = $icon;
        $oxygen_svg_icons_to_load[] = $copied_icon;
        
        $output = '';
        
        $output .= '<button aria-label="'. $aria_label .'" class="oxy-copy-to-clipboard_marker'. $inbuilder .'" ';
        $output .= 'data-x-animation="' . $icon_animation . '" ';
        $output .= 'data-x-delay="' . $state_delay . '" ';

        if ( $maybe_hide ) {
            $output .= 'data-x-hide ';
        }
        if ( $select_text ) {
            $output .= 'data-x-select ';
        }

        if ('selector' === $text_to_copy) {
            $output .= 'data-x-copy-selector="'. $copy_selector .'" ';
        } else {
            $output .= 'data-x-copy-text="'. $copy_text .'" ';
        }

        if ( $maybe_tooltip ) {
            $output .= 'data-x-tooltip="true" ';
            $output .= 'data-x-tooltip-show="' . $tooltip_show . '" ';
        }

        $output .= 'data-x-copy-button ';

        $output .= '>';

        if ( 'true' === esc_attr($options['maybe_icon'] ) && 'before' === esc_attr($options['icons_position'] ) ) {

            $output .= '<span class="oxy-copy-to-clipboard_icons">';
            $output .= '<span class="oxy-copy-to-clipboard_icon"><svg id="icon' . esc_attr($options['selector']) . '"><use xlink:href="#' . $icon .'"></use></svg></span>';

            if ('animatedCheck' === $copy_completed) {
                $output .= '<span class="oxy-copy-to-clipboard_copied-icon"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="x-copy-to-clipboard_animated-icon"><path d="M13.25 4.75L6 12L2.75 8.75"></path></svg></span>';
            } else {
                $output .= '<span class="oxy-copy-to-clipboard_copied-icon"><svg><use xlink:href="#' . $copied_icon .'"></use></svg></span>';
            }

            $output .= '</span>';

        }

        if ($content) {
            if ( function_exists('do_oxygen_elements') ) {
                $output .= do_oxygen_elements($content); 
            } else {
                $output .= do_shortcode($content); 
            }  
        } else {
            if ( $maybe_button_text && $label_text ) {
                $output .= '<span class="oxy-copy-to-clipboard_label" ';
                $output .= $label_text_copied ? 'data-x-copied="' . $label_text_copied .  '" ' : '';
                $output .= '>'. $label_text .'</span>';
            }
        }
        
        if ( 'true' === esc_attr($options['maybe_icon']) && 'after' === esc_attr($options['icons_position'] ) ) {
        
            $output .= '<span class="oxy-copy-to-clipboard_icons">';
            $output .= '<span class="oxy-copy-to-clipboard_icon"><svg id="icon' . esc_attr($options['selector']) . '"><use xlink:href="#' . $icon .'"></use></svg></span>';

            if ('animatedCheck' === $copy_completed) {
                $output .= '<span class="oxy-copy-to-clipboard_copied-icon"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="x-copy-to-clipboard_animated-icon"><path d="M13.25 4.75L6 12L2.75 8.75"></path></svg></span>';
            } else {
                $output .= '<span class="oxy-copy-to-clipboard_copied-icon"><svg><use xlink:href="#' . $copied_icon .'"></use></svg></span>';
            }

            $output .= '</span>';

        }
        
        
        $output .= '</button>';

        if ( $maybe_tooltip ) {
        
            $output .= '<div class="oxy-copy-to-clipboard_popup" id="oxy-copy-to-clipboard_popup' . esc_attr($options['selector']) . '"' ;
            
            $output .= 'data-placement="'. $popover_placement .'" ';
            $output .= 'data-offsetx="'. $offset_x .'" ';
            $output .= 'data-offsety="'. $offset_y .'" ';
            $output .= 'data-show="'. $maybe_showoncreate .'" ';
            $output .= 'data-move-transition="'. $move_transition .'" ';
            $output .= 'data-interaction="'. $interaction .'" ';
            $output .= 'data-copied-text="'. $tooltip_text_copied .'" ';
            $output .= 'data-copy-text="'. $tooltip_text .'" ';
            
            $output .= '><div class="oxy-copy-to-clipboard_popup-inner"><div class="oxy-copy-to-clipboard_popup-content">';

            $output .= $tooltip_text;
            
            $output .= '</div></div></div>';

        }
        
        echo $output;

        $this->dequeue_scripts_styles();

        // Just for the builder
        if (defined('OXY_ELEMENTS_API_AJAX') && OXY_ELEMENTS_API_AJAX) {

            echo '<script type="text/javascript" src="'. plugin_dir_url(__FILE__) . 'assets/popper.min.js' .'"></script>';
            echo '<script type="text/javascript" src="'. plugin_dir_url(__FILE__) . 'assets/tippy.min.js' .'"></script>';
        }

        if ( isset( $options['maybe_tooltip'] ) && esc_attr($options['maybe_tooltip'])  === 'enable' ) {
            
            if ($this->js_tooltip_added !== true) {
                if (!defined('OXY_ELEMENTS_API_AJAX') || !OXY_ELEMENTS_API_AJAX) {
                    add_action( 'wp_footer', array( $this, 'output_js' ) );
                }
                $this->js_tooltip_added = true;
            }

        }
        
         // add JavaScript code only once and if shortcode presented
        if ($this->js_added !== true) {
            if (!defined('OXY_ELEMENTS_API_AJAX') || !OXY_ELEMENTS_API_AJAX) {
                add_action( 'wp_footer', array( $this, 'output_init_js' ) );
            }
            $this->js_added = true;
        }
        
        $inBuilderJS = "jQuery(document).ready(function($){

                            $('#%%ELEMENT_ID%%').on('click', () => {

                                $('#%%ELEMENT_ID%%').find('.oxy-copy-to-clipboard_marker').addClass('oxy-copy-to-clipboard_copied')
                                $('#%%ELEMENT_ID%%').find('.oxy-copy-to-clipboard_marker').attr('aria-pressed', 'true')

                                setTimeout(() => {

                                    $('#%%ELEMENT_ID%%').find('.oxy-copy-to-clipboard_marker').removeClass('oxy-copy-to-clipboard_copied')
                                    $('#%%ELEMENT_ID%%').find('.oxy-copy-to-clipboard_marker').attr('aria-pressed', 'false')

                                }, 3000 );

                            })
                            
                            setTimeout(function(){

                                if ( $('#%%ELEMENT_ID%% .oxy-copy-to-clipboard_popup-inner').length ) {

                                    tippy('#%%ELEMENT_ID%% .oxy-copy-to-clipboard_marker', {
                                        content: $('#%%ELEMENT_ID%% .oxy-copy-to-clipboard_popup').attr('data-copy-text'),
                                        allowHTML: true,     
                                        interactive: false, 
                                        arrow: true,
                                        appendTo: $('#%%ELEMENT_ID%% .oxy-copy-to-clipboard_popup')[0],
                                        placement: '%%popover_placement%%',
                                        maxWidth: 'none',    
                                        inertia: true,
                                        theme: 'copy-clipboard',     
                                        trigger: 'click',
                                        hideOnClick: 'toggle',
                                        moveTransition: 'transform %%move_transition%%ms ease-in',
                                        offset: [parseInt( %%offset_x%% ), parseInt( %%offset_y%% )],          
                                        popperOptions: {
                                                modifiers: [{
                                                    name: 'flip',
                                                    options: {
                                                        fallbackPlacements: ['%%popover_placement%%'],
                                                    },
                                                    },
                                                ],
                                            },
                                        });

                                    }
                                
                            
                            }, 100 );
                    });
                    
                ";


        
        if( method_exists('OxygenElement', 'builderInlineJS') ) {
             $this->El->builderInlineJS($inBuilderJS);
        }
        
    
    }
    
    
    function class_names() {
        return array();
    }

    function controls() {
        
        $popover_marker_selector = '.oxy-copy-to-clipboard_marker';
        $popover_outer_selector = '.oxy-copy-to-clipboard_popup';
        $popover_popup_selector = '.oxy-copy-to-clipboard_popup-inner';


        /* general */
    
        $this->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Source to copy'),
                'slug' => 'text_to_copy',
            )
        )->setValue(array( 
            "selector" => "From selector", 
            "dynamic_data" => "From dynamic Data",
        ))->setDefaultValue('selector');


        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Copy selector'),
                "default" => '',
                "slug" => 'copy_selector',
                "base64" => true,
                "condition" => 'text_to_copy=selector',
                'description' => 'some info'
            )
        )->setParam('dynamicdatacode', '<div optionname="\'oxy-copy-to-clipboard_copy_selector\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Copy text'),
                "default" => '',
                "slug" => 'copy_text',
                "base64" => true,
                "condition" => 'text_to_copy!=selector'
            )
        )->setParam('dynamicdatacode', '<div optionname="\'oxy-copy-to-clipboard_copy_text\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');


        

        /**
         * icon
         */ 
        $copy_icon_section = $this->addControlSection("copy_icon_section", __("Icons"), "assets/icon.png", $this);

        $icon_selector = '.oxy-copy-to-clipboard_icons';
        
       
        $copy_icon_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Include Icons'),
                'slug' => 'maybe_icon',
            )
        )->setValue(array( 
            "true" => "Enable", 
            "false" => "Disable",
        ))->setDefaultValue('true')
          ->setValueCSS( array(
            "false"  => ".oxy-copy-to-clipboard_icons {
                                display: none;
                            }",
        ) );

        $copy_icon_section->addStyleControl(
            array(
                "name" => __('Icon Size'),
                "selector" => $icon_selector,
                "property" => 'font-size',
                "condition" => 'maybe_icon=true'
            )
        );

        $copy_icon_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Icons position'),
                'slug' => 'icons_position',
            )
        )->setValue(array( 
            "after" => "After content", 
            "before" => "Before content",
        ))
        ->setDefaultValue('after')
        ->rebuildElementOnChange();

        $copy_icon_section->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Copy Icon'),
                "slug" => 'icon',
                "value" => 'FontAwesomeicon-copy',
                "condition" => 'maybe_icon=true'
            )
        )->rebuildElementOnChange();
        
        $copy_icon_finder_section = $copy_icon_section->addControlSection("copy_icon_finder_section", __("Copied icon"), "assets/icon.png", $this);
        
        $copy_icon_finder_section->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Copied Icon'),
                "slug" => 'copied_icon',
                "value" => 'FontAwesomeicon-copy',
                "condition" => 'copy_completed=customIcon&&maybe_icon=true'
            )
        )->rebuildElementOnChange();


        $copy_icon_section->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('On copy completed..'),
                'slug' => 'copy_completed',
            )
        )->setValue(array( 
            "animatedCheck" => "Animated check", 
            "customIcon" => "Show custom icon",
        ))->setDefaultValue('animatedCheck')->rebuildElementOnChange();


        $copy_icon_section->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Icon animation'),
                'slug' => 'icon_animation',
            )
        )->setValue(array( 
            'fade' => __( 'Fade', 'bricks' ),
            'slideUp' => __( 'Slide Up', 'bricks' ),
            'slideDown' => __( 'Slide Down', 'bricks' ),
            'slideLeft' => __( 'Slide Left', 'bricks' ),
            'slideRight' => __( 'Slide Right', 'bricks' ),
            'flipX' => __( 'Flip X', 'bricks' ),
            'flipY' => __( 'Flip Y', 'bricks' ),
        ))->setDefaultValue('fade')->rebuildElementOnChange();
        
        
       




        
        /**
         * Marker
         */ 
        $marker_section = $this->addControlSection("marker_section", __("Button"), "assets/icon.png", $this);

        $marker_section->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Button Text'),
                'slug' => 'maybe_button_text',
                'default' => 'enable',
            )
        )->setValue(array( 
                'enable' => __('Enable'), 
				'disable' => __('Disable'), 
            )
        )->rebuildElementOnChange();
        
        $marker_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Button Text'),
                "default" => 'Copy',
                "slug" => 'label_text',
                "base64" => true,
                "condition" => 'maybe_button_text=enable',
            )
        )->rebuildElementOnChange()->setParam('dynamicdatacode', '<div optionname="\'oxy-copy-to-clipboard_label_text\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode"></div>');
        
        $marker_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Button Text (copied)'),
                "default" => '',
                "slug" => 'label_text_copied',
                "base64" => true,
                "condition" => 'maybe_button_text=enable',
            )
        )->rebuildElementOnChange()->setParam('dynamicdatacode', '<div optionname="\'oxy-copy-to-clipboard_label_text_copied\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode"></div>');
        

        $marker_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Aria label'),
                "default" => '',
                "slug" => 'aria_label',
                "base64" => true,
            )
        )->setParam('dynamicdatacode', '<div optionname="\'oxy-copy-to-clipboard_aria_label\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode"></div>');
                
        
        
        
        /**
         * spacing
         */ 
        $marker_spacing_section = $marker_section->addControlSection("marker_spacing_section", __("Spacing"), "assets/icon.png", $this);
        
        $marker_selector = '.oxy-copy-to-clipboard_marker';
        
        $marker_spacing_section->addPreset(
            "padding",
            "marker_padding",
            __("Marker padding"),
            $marker_selector
        )->whiteList();
        
        
        $marker_layout_section = $marker_section->addControlSection("marker_layout_section", __("Layout"), "assets/icon.png", $this);
        
        $marker_layout_section->flex($marker_selector, $this);
        
        $marker_section->borderSection('Borders', '.oxy-copy-to-clipboard_marker',$this);
        $marker_section->boxShadowSection('Shadows', $marker_selector,$this);
        $marker_section->typographySection('Typography', $marker_selector,$this);
        
        
        
        $marker_section->addStyleControl(
            array(
                "name" => __('Color'),
                "property" => 'color',
                "control_type" => 'colorpicker',
                "selector" => $marker_selector
            )
        )->setParam('hide_wrapper_end', true);
        
        $marker_section->addStyleControl(
            array(
                "name" => __('Background'),
                "property" => 'background-color',
                "control_type" => 'colorpicker',
                "selector" => $marker_selector
            )
        )->setParam('hide_wrapper_start', true);
        
        $marker_section->addStyleControl( 
            array(
                "name" => __('Width'),
                "property" => 'width',
                "control_type" => "measurebox",
                "unit" => "px",
                "value" => '',
                "selector" => $marker_selector
            )
            );
        
            $marker_section->addStyleControl( 
                array(
                    "name" => __('Transition duration'),
                    "property" => 'transiton-duration',
                    "control_type" => 'slider-measurebox',
                    "selector" => $popover_marker_selector,
                    "default" => '300'
                )
            )
            ->setRange('0','1000','1')
            ->setUnits('ms','ms');
        
        /**
         * Marker
         */ 
        $marker_hover_section = $this->addControlSection("marker_hover_section", __("Button Hover/Active"), "assets/icon.png", $this);
        
        $marker_hover_section->addStyleControl(
            array(
                "name" => __('Hover Color'),
                "property" => 'color',
                "control_type" => 'colorpicker',
                "selector" => $marker_selector . ":hover"
            )
        )->setParam('hide_wrapper_end', true);
        
        $marker_hover_section->addStyleControl(
            array(
                "name" => __('Focus Color'),
                "property" => 'color',
                "control_type" => 'colorpicker',
                "selector" => $marker_selector . ":focus"
            )
        )->setParam('hide_wrapper_start', true);
        
        $marker_hover_section->addStyleControl(
            array(
                "name" => __('Active Color'),
                "property" => 'color',
                "control_type" => 'colorpicker',
                "selector" => $marker_selector . ".oxy-copy-to-clipboard_copied"
            )
        )->setParam('hide_wrapper_end', true);
        
        $marker_hover_section->addStyleControl(
            array(
                "name" => __('Hover background'),
                "property" => 'background-color',
                "control_type" => 'colorpicker',
                "selector" => $marker_selector . ":hover"
            )
        )->setParam('hide_wrapper_start', true);
        
        $marker_hover_section->addStyleControl(
            array(
                "name" => __('Focus background'),
                "property" => 'background-color',
                "control_type" => 'colorpicker',
                "selector" => $marker_selector . ":focus"
            )
        )->setParam('hide_wrapper_end', true);
        
        $marker_hover_section->addStyleControl(
            array(
                "name" => __('Active background'),
                "property" => 'background-color',
                "control_type" => 'colorpicker',
                "selector" => $marker_selector . ".oxy-copy-to-clipboard_copied"
            )
        )->setParam('hide_wrapper_start', true);
        
        
        $marker_hover_section->borderSection('Hover Borders', $marker_selector.":hover",$this);
        $marker_hover_section->boxShadowSection('Hover Shadows', $marker_selector.":hover",$this);
        $marker_hover_section->borderSection('Focus Borders', $marker_selector.":focus",$this);
        $marker_hover_section->boxShadowSection('Focus Shadows', $marker_selector.":focus",$this);
        $marker_hover_section->borderSection('Active Borders', $marker_selector.".oxy-copy-to-clipboard_copied",$this);
        $marker_hover_section->boxShadowSection('Active Shadows', $marker_selector.".oxy-copy-to-clipboard_copied",$this);
        
        
        
        /**
         * Tooltip
         */ 
        $popover_section = $this->addControlSection("popover_section", __("Tooltip"), "assets/icon.png", $this);
        
        $popover_section->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Add Tooltip'),
                'slug' => 'maybe_tooltip',
                'default' => 'disable',
            )
        )->setValue(array( 
                'enable' => __('Enable'), 
				'disable' => __('Disable'), 
            )
        )->rebuildElementOnChange();

        $popover_section->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Show tooltip on..'),
                'slug' => 'tooltip_show',
                'default' => 'hocus',
                "condition" => 'maybe_tooltip=enable',
            )
        )->setValue(array( 
                'hocus' => __('Hover / Focus'), 
				'copy' => __('Copy completed'), 
            )
        )->rebuildElementOnChange();

        

        $popover_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Tooltip text'),
                "slug" => 'tooltip_text',
                'default' => 'Copy',
                "condition" => 'maybe_tooltip=enable',
            )
        )->rebuildElementOnChange()->setParam('hide_wrapper_end', true);
        
        $popover_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Tooltip text (copied)'),
                "slug" => 'tooltip_text_copied',
                'default' => 'Copied',
                "condition" => 'maybe_tooltip=enable',
            )
        )->rebuildElementOnChange()->setParam('hide_wrapper_start', true);


        $popover_section->addStyleControl( 
            array(
                "name" => __('Tooltip width'), 
                "property" => '--extras-copy-width',
                "control_type" => 'slider-measurebox',
                
            )
        )
        ->setRange('0','1000','1')
        ->setUnits('px');
            
        $popover_section->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Tooltip placement'),
                'slug' => 'popover_placement',
                'default' => 'top',
                "condition" => 'maybe_tooltip=enable',
            )
        )->setValue(array( 
                'top' => __('Top'), 
				'right' => __('Right'), 
				'bottom' => __('Bottom'), 
				'left' => __('Left'), 
				'auto' 	=> __( 'Auto (Side with the most space)' ), 
				'auto-start' => __( 'Auto Start' ), 
				'auto-end' => __( 'Auto End' ),
				'top-start' => __( 'Top Start' ), 
				'top-end' => __( 'Top End' ),
				'right-start' => __( 'Right Start' ), 
				'right-end' => __( 'Right End' ),
				'bottom-start' => __( 'Bottom Start' ), 
				'bottom-end' => __( 'Bottom End' ),
				'left-start' => __( 'Left Start' ), 
				'left-end' => __( 'Left End' ),
            )
        )->rebuildElementOnChange();
        
        $popover_section->addOptionControl(
            array(
                "type" => 'measurebox',
                "name" => __('Offset X'),
                "slug" => 'offset_x',
                "default" => '0',
                "condition" => 'maybe_tooltip=enable',
            )
        )->setUnits('px','px')
        ->rebuildElementOnChange()
         ->setParam('hide_wrapper_end', true);
         
        
        $popover_section->addOptionControl(
            array(
                "type" => 'measurebox',
                "name" => __('Offset Y'),
                "slug" => 'offset_y',
                "default" => '10',
                "condition" => 'maybe_tooltip=enable',
            )
        )->setUnits('px','px')
        ->rebuildElementOnChange()
         ->setParam('hide_wrapper_start', true);
         
        
        /**
         * Inner Layout
         */
        //$layout_section = $this->addControlSection("layout_section", __("Popover Layout"), "assets/icon.png", $this);
        
        
        
        
        /**
         * Inner Layout
         */
        $popover_styles_section = $this->addControlSection("popover_spacing_section", __("Tooltip Styles"), "assets/icon.png", $this);
        
        $popover_content_selector = '.oxy-copy-to-clipboard_popup-content';
        
        $popover_styles_section->addPreset(
            "padding",
            "popover_padding",
            __("Padding"),
            '.tippy-box[data-animation=fade][data-theme~="copy-clipboard"], .extras-in-builder .oxy-copy-to-clipboard_popup-content'
        )->whiteList();
        
        
        $popover_styles_section->addStyleControl(
                 array(
                    "name" => 'Color',
                    "control_type" => 'colorpicker',
                    "property" => '--extras-copy-color',
                )
        )->setParam('hide_wrapper_end', true);
        
        $popover_styles_section->addStyleControl(
                array(
                    "name" => 'Background',
                    "property" => '--extras-copy-bg',
                    "control_type" => 'colorpicker',
                )
        )->setParam('hide_wrapper_start', true);
        
        
        $popover_styles_section->boxShadowSection('Shadows', '.tippy-box[data-theme="copy-clipboard"], .extras-in-builder .oxy-copy-to-clipboard_popup-inner',$this);
        $popover_styles_section->typographySection('Typography', '.oxy-copy-to-clipboard_popup .tippy-content',$this);
        $popover_styles_section->borderSection('Borders', '.tippy-box[data-animation=fade][data-theme="copy-clipboard"], .extras-in-builder .oxy-copy-to-clipboard_popup-content',$this);
        
        
        
        /**
         * animations
         */
        $animations_section = $popover_section->addControlSection("animations_section", __("Tooltip Animation"), "assets/icon.png", $this);
        
        $container_selector = '.oxy-copy-to-clipboard_inner';
        
        
        
        $animations_section->addStyleControl(
            array(
                "name" => 'Transition in',
                "property" => '--extras-copy-transitionin',
                "control_type" => 'measurebox',
            )
        )
        ->setUnits('ms','ms')->setParam('hide_wrapper_end', true);
        
        $animations_section->addStyleControl(
            array(
                "name" => 'Transition out',
                "property" => '--extras-copy-transitionout',
                "control_type" => 'measurebox',
            )
        )
        ->setUnits('ms','ms')->setParam('hide_wrapper_start', true);
        
          $animations_section->addStyleControl( 
            array(
                "name" => __('Translate X'),
                "property" => '--extras-copy-translatex',
                "control_type" => 'measurebox',
            )
        )->setUnits('px')->setParam('hide_wrapper_end', true);
        
        $animations_section->addStyleControl( 
            array(
                "name" => __('Translate Y'),
                "property" => '--extras-copy-translatey',
                "control_type" => 'measurebox',
                "value" => '-10'
            )
        )->setUnits('px')->setParam('hide_wrapper_start', true);
        
        $animations_section->addStyleControl( 
            array(
                "name" => __('Scale'),
                "property" => '--extras-copy-scale',
                "control_type" => 'slider-measurebox',
                "value" => '.9'
            )
        )
        ->setRange('0.8','1.2','.01');
        
        
        $animations_section->addCustomControl(
            '<div style="color: #fff; line-height: 1.3; font-size: 13px;">Rotate<hr style="opacity: .25;"></div>','description');
        
        
        $animations_section->addStyleControl( 
            array(
                "name" => __('Rotate X'),
                "property" => '--extras-copy-rotatex',
                "control_type" => 'slider-measurebox',
            )
        )
        ->setRange('-100','100','1');
        
        $animations_section->addStyleControl( 
            array(
                "name" => __('Rotate Y'),
                "property" => '--extras-copy-rotatey',
                "control_type" => 'slider-measurebox',
            )
        )
        ->setRange('-100','100','1');
        
        $animations_section->addStyleControl( 
            array(
                "name" => __('Rotate Z'),
                "property" => '--extras-copy-rotatez',
                "control_type" => 'slider-measurebox',
            )
        )
        ->setRange('-100','100','1');
        
        $animations_section->addStyleControl( 
            array(
                "name" => __('Rotate angle'),
                "property" => '--extras-copy-rotatedeg',
                "control_type" => 'slider-measurebox',
            )
        )
        ->setRange('-360','360','1')
        ->setUnits('deg');


        /**
         * behaviour
         */ 
        $behaviour_section = $this->addControlSection("behaviour_section", __("Behaviour"), "assets/icon.png", $this);

        $behaviour_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Hide if no text found to copy'),
                'slug' => 'maybe_hide',
            )
        )->setValue(array( 
            "true" => "Enable", 
            "false" => "Disable",
        ))->setDefaultValue('false');

        $behaviour_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Button state delay (ms)'),
                "default" => '3000',
                "slug" => 'state_delay',
                )
        );

        
        $behaviour_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Select text after copying'),
                'slug' => 'select_text',
                "condition" => 'text_to_copy=selector',
            )
        )->setValue(array( 
            "true" => "Enable", 
            "false" => "Disable",
        ))->setDefaultValue('false');
        
        

    }
    
    
    
    function customCSS($options, $selector) {
        
        $css = '';
        
        if (! $this->css_added ) {
        
            $css .= "
            
                    :root {
                        --extras-copy-rotatex: 0;
                        --extras-copy-rotatey: 0;
                        --extras-copy-rotatez: 0;
                        --extras-copy-rotatedeg: 0deg;
                        --extras-copy-transitionout: 300ms;
                        --extras-copy-transitionin: 300ms;
                        --extras-copy-bg: #fff;
                        --extras-copy-color: #111;
                        --extras-copy-marker-scale: 1;
                        --extras-copy-marker-bg: #f3f3f3;
                        --extras-copy-translatex: 0;
                        --extras-copy-translatey: -10px;
                        --extras-copy-scale: 1;
                        --extras-copy-width: auto;
                        --oxy-copytoclipboard-duration: 300ms;
                    }

                    .oxy-copy-to-clipboard_icon {
                        display: flex;
                        pointer-events: none;
                    }

                    .oxy-copy-to-clipboard_icon svg,
                    .oxy-copy-to-clipboard_copied-icon svg {
                        height: 1em;
                        width: 1em;
                        fill: currentColor;
                    }

                    .oxy-copy-to-clipboard_copied-icon .x-copy-to-clipboard_animated-icon {
                        fill: none;
                    }

                    .oxy-copy-to-clipboard_marker {
                        color: inherit;
                        position: relative;
                        cursor: pointer;
                        box-shadow: none;
                        border: none;
                        will-change: transform;
                        transition: all 300ms ease;
                        padding: 0;
                        font-size: 14px;
                    }

                    .oxy-copy-to-clipboard_marker[data-x-hide] {
                        display: none;
                    }
                    
                    .oxy-hotspots .oxy-copy-to-clipboard_marker {
                        transform: translate(-50%,-50%);
                        -webkit-transform: translate(-50%,-50%);
                    }
                    
                    .oxygen-builder-body .oxy-copy-to-clipboard_marker,
                    .oxygen-builder-body .oxy-copy-to-clipboard_marker[data-x-hide] {
                        display: flex;
                        transform: none;
                        -webkit-transform: none;
                    }
                    
                    .oxy-copy-to-clipboard_marker {
                        background: var(--extras-copy-marker-bg);
                        color: var(--extras-copy-marker-color);
                        padding: 10px;
                        display: flex;
                        flex-direction: row;
                        align-items: center;
                        position: relative;
                        z-index: 1;
                        border-radius: inherit;
                        transition: all 300ms ease;
                        gap: .5em;
                    }
                    
                    .oxy-copy-to-clipboard_marker:hover {
                        transform: scale(var(--extras-copy-marker-scale));
                        -webkit-transform: scale(var(--extras-copy-marker-scale));
                    }
                    
                    .oxygen-builder-body .oxy-copy-to-clipboard_marker:hover {
                        transform: scale(var(--extras-copy-marker-scale));
                        -webkit-transform: scale(var(--extras-copy-marker-scale));
                    }

                    .oxy-copy-to-clipboard_icons {
                        position: relative;
                    }

                    .oxy-copy-to-clipboard_icons > span {
                        width: 1em;
                        height: 1em;
                        font-size: 1em;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        transition: transform var(--oxy-copytoclipboard-duration) ease, opacity var(--oxy-copytoclipboard-duration) ease, stroke-dashoffset var(--oxy-copytoclipboard-duration) ease;
                    }
        
                    .oxy-copy-to-clipboard_copied .oxy-copy-to-clipboard_icon {
                        opacity: 0;
                    }

                    .oxy-copy-to-clipboard_copied-icon {
                        color: inherit;
                        stroke-dasharray: 50;
                        stroke-dashoffset: -50;
                        opacity: 0;
                        position: absolute;
                        top: 0;
                        left: 0;
                    }

                    .oxy-copy-to-clipboard_copied-icon svg {
                        height: 100%;
                        width: 100%;
                    }
        
                    .oxy-copy-to-clipboard_copied .oxy-copy-to-clipboard_copied-icon {
                        stroke-dashoffset: 0;
                        opacity: 1;
                        transform: none!important;
                    }
                    
                    
                   

                    .oxy-copy-to-clipboard_popup {
                        display: flex;
                        visibility: hidden;
                        position: absolute;
                    }
                    
                    .oxy-copy-to-clipboard_popup [data-tippy-root] {
                         width: var(--extras-copy-width);
                    }
                    
                    .oxy-copy-to-clipboard_popup-inner img {
                        max-width: 100%;
                        height: auto;
                    }
                    
                    .oxy-popover .tippy-arrow {
                        color: var(--extras-copy-bg);
                    }

                    .oxy-copy-to-clipboard_popup .tippy-content {
                        background-color: var(--extras-copy-bg);
                        color: var(--extras-copy-color);
                        font-size: 16px;
                    }

                    .oxy-copy-to-clipboard_popup-content {
                        border-radius: 3px;
                        positon: relative;
                        display: flex;
                        flex-direction: column;
                    }

                    .oxygen-builder-body .oxy-copy-to-clipboard_popup-content:empty {
                        min-height: 100px;
                        min-width: 100px;
                    }

                    .oxy-copy-to-clipboard_popup.oxy-copy-to-clipboard_popup-reveal .oxy-copy-to-clipboard_popup-inner {
                        opacity: 1;
                        visibility: visible;
                        transform: none;
                    }
                    
                    .oxy-copy-to-clipboard_marker[aria-describedby] + .oxy-copy-to-clipboard_popup .tippy-box[data-theme='copy-clipboard'] {
                        visibility: visible;
                        transition-duration: var(--extras-copy-transitionin);
                    }
                    
                    .oxy-copy-to-clipboard_marker:not([aria-describedby]) + .oxy-copy-to-clipboard_popup .tippy-box[data-theme='copy-clipboard'],
                    .oxy-copy-to-clipboard_popup[data-elem-selector] .tippy-box[data-theme='copy-clipboard'][data-state='hidden']{
                          opacity: 0;
                          transform: translate(var(--extras-copy-translatex),var(--extras-copy-translatey)) scale(var(--extras-copy-scale)) rotate3d(var(--extras-copy-rotatex),var(--extras-copy-rotatey),var(--extras-copy-rotatez),var(--extras-copy-rotatedeg));
                          transition-duration: var(--extras-copy-transitionout);
                    }
                    
                    .oxygen-builder-body .oxy-copy-to-clipboard_popup {
                        position: absolute;
                    }
                

                    .oxy-copy-to-clipboard_marker[aria-describedby] + .oxy-copy-to-clipboard_popup .oxy-copy-to-clipboard_popup-inner,
                    .oxy-copy-to-clipboard_marker[aria-describedby] + .oxy-copy-to-clipboard_popup .oxy-copy-to-clipboard_popup-inner {
                        transform: none;
                         opacity: 1;
                         visibility: visible;
                    }
                    
                    .oxy-popover .tippy-content {
                        padding: 0;
                    }
                    
                    .tippy-box[data-animation=fade][data-theme='copy-clipboard'] {
                        position: relative;
                        font-size: inherit;
                        outline: 0;
                        opacity: 1;
                        background: none;
                        box-shadow: 0 5px 50px rgba(0,0,0,0.1);
                        background-color: var(--extras-copy-bg);
                        color: var(--extras-copy-color);
                        padding: .25em;
                        will-change: opacity, transform;
                    }

                    .tippy-box[data-animation=fade][data-theme='copy-clipboard'] .tippy-arrow {
                        color: var(--extras-copy-bg);
                    }
                    
                    .tippy-box[data-animation=fade][data-theme='copy-clipboard'] .oxy-copy-to-clipboard_popup-content {
                        padding: 0;
                    }
                    
                    .extras-in-builder .oxy-copy-to-clipboard_popup-inner {
                        box-shadow: 0 5px 50px rgba(0,0,0,0.1);
                    }

                    .oxygen-builder-body .oxy-copy-to-clipboard_popup-inner {
                        display: none;
                    }

                    .oxygen-builder-body .oxy-copy-to-clipboard_popup [data-tippy-root]:not(:last-child) {
                        display: none;
                    }
                    
                   .tippy-box[data-theme='copy-clipboard'] .tippy-backdrop {
                        background-color: #fff;
                    }
                    
                    .oxy-copy-to-clipboard_marker[aria-pressed=true] .oxy-copy-to-clipboard_icon {
                        opacity: 0;
                    }
                    
                    /* slideup */
                    .oxy-copy-to-clipboard_marker[aria-pressed=true][data-x-animation*=slideUp] .oxy-copy-to-clipboard_icon {
                        transform: translateY(-100%);
                    }
                    
                    .oxy-copy-to-clipboard_marker[data-x-animation*=slideUp] .oxy-copy-to-clipboard_copied-icon {
                        transform: translateY(100%);
                    }
                    
                    /* slidedown */
                    .oxy-copy-to-clipboard_marker[aria-pressed=true][data-x-animation*=slideDown] .oxy-copy-to-clipboard_icon {
                        transform: translateY(100%);
                    }
                    
                    .oxy-copy-to-clipboard_marker[data-x-animation*=slideDown] .oxy-copy-to-clipboard_copied-icon {
                        transform: translateY(-100%);
                    }
                    
                    /* slideleft */
                    .oxy-copy-to-clipboard_marker[aria-pressed=true][data-x-animation*=slideLeft] .oxy-copy-to-clipboard_icon {
                        transform: translateX(-100%);
                    }
                    
                    .oxy-copy-to-clipboard_marker[data-x-animation*=slideLeft] .oxy-copy-to-clipboard_copied-icon {
                        transform: translateX(100%);
                    }
                    
                    /* slideright */
                    .oxy-copy-to-clipboard_marker[aria-pressed=true][data-x-animation*=slideRight] .oxy-copy-to-clipboard_icon {
                        transform: translateX(100%);
                    }
                    
                    .oxy-copy-to-clipboard_marker[data-x-animation*=slideRight] .oxy-copy-to-clipboard_copied-icon {
                        transform: translateX(-100%);
                    }
                    
                    /* flipY */
                    .oxy-copy-to-clipboard_marker[aria-pressed=true][data-x-animation*=flipY] .oxy-copy-to-clipboard_icon {
                        transform: rotateY(180deg);
                    }
                    
                    .oxy-copy-to-clipboard_marker[data-x-animation*=flipY] .oxy-copy-to-clipboard_copied-icon {
                        transform: rotateY(-180deg);
                    }
                    
                    /* flipX */
                    .oxy-copy-to-clipboard_marker[aria-pressed=true][data-x-animation*=flipX] .oxy-copy-to-clipboard_icon {
                        transform: rotateX(180deg);
                    }
                    
                    .oxy-copy-to-clipboard_marker[data-x-animation*=flipX] .oxy-copy-to-clipboard_copied-icon {
                        transform: rotateX(-180deg);
                    }
                    
                    ";
            
            $css_added = 'true';
            
        }
        
        return $css;
        
    } 
    
    
    function output_js() {

         wp_enqueue_script( 'popper-js', plugin_dir_url(__FILE__) . 'assets/popper.min.js', '', '1.0.0' );
         wp_enqueue_script( 'tippy-js', plugin_dir_url(__FILE__) . 'assets/tippy.min.js', '', '6.3.1' );
               
        
    }
    
    function output_init_js() { 
        wp_enqueue_script( 'extras-copy-to-clipboard', plugin_dir_url(__FILE__) . 'assets/copytoclipboard.js', '', '1.0.0' );      
     }
    
    
    function allowedEmptyOptions($options) {

        $options_to_add = array(
            "oxy-copy-to-clipboard_label_text",
        );

        $options = array_merge($options, $options_to_add);

        return $options;
    } 
    

}

add_filter("oxy_base64_encode_options", 
    function($items) { 
        $items=array_merge($items, array('oxy-copy-to-clipboard_label_text','oxy-copy-to-clipboard_aria_label')); 
        return $items;
    }
);

new ExtraCopyToClipboard();