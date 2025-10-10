<?php

class ExtraLightbox extends OxygenExtraElements {
        
    var $js_added = false;
    var $css_added = false;
    
	function name() {
        return 'Lightbox';
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
        return "interactive";
    }
    
    function tag() {
        return array('default' => 'div');
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
        
        $url = $dynamic($options['url']);
        
        $ajax_selector = isset( $options['ajax_selector'] ) ? esc_attr($options['ajax_selector']) : '';
        $force_ajax_selector = isset( $options['force_ajax_selector'] ) ? esc_attr($options['force_ajax_selector']) : '';
        $link_selector = '#' . esc_attr($options['selector']) . ' > a';
        $click_selector = isset( $options['click_selector'] ) ? esc_attr($options['click_selector']) : '';
        $inner_element_selector = isset( $options['click_selector'] ) ? esc_attr($options['click_selector']) : '';
        
        $element_selector = isset( $options['element_selector'] ) ? esc_attr($options['element_selector']) : '';
        
        $custom_click_selector = isset( $options['custom_click_selector'] ) ? esc_attr($options['custom_click_selector']) : '';
        
        $maybe_multiple = isset( $options['maybe_multiple'] ) ? esc_attr($options['maybe_multiple']) : ''; 

        $maybe_loop = isset( $options['maybe_loop'] ) ? esc_attr($options['maybe_loop']) : ''; 
        
        $custom_html = isset( $options['custom_html'] ) ? esc_attr($options['custom_html']) : '';
        
        $content_type = isset( $options['content_type'] ) ? esc_attr($options['content_type']) : '';
        
        $inline_content = isset( $options['inline_content'] ) ? esc_attr($options['inline_content']) : '';

        $maybe_swipe = isset( $options['maybe_swipe'] ) ? esc_attr($options['maybe_swipe']) : false;
        
        $inline_selector = ('inner' === $inline_content) ? 'true' : $element_selector;
        
        $iframe_selector = isset( $options['iframe_selector'] ) ? esc_attr($options['iframe_selector']) : '';
        $maybe_iframe_selector = isset( $options['maybe_iframe_selector'] ) ? esc_attr($options['maybe_iframe_selector']) : '';
        $force_iframe_selector = isset( $options['force_iframe_selector'] ) ? esc_attr($options['force_iframe_selector']) : '';
        $force_maybe_iframe_selector = isset( $options['force_maybe_iframe_selector'] ) ? esc_attr($options['force_maybe_iframe_selector']) : '';
        
        $small_close_button = isset( $options['small_close_button'] ) ? esc_attr($options['small_close_button']) : '';
        $iframe_preload = isset( $options['iframe_preload'] ) ? esc_attr($options['iframe_preload']) : '';
        $toolbar_display = isset( $options['toolbar_display'] ) ? esc_attr($options['toolbar_display']) : '';
        
        $maybe_thumbs = isset( $options['maybe_thumbs'] ) ? esc_attr($options['maybe_thumbs']) : '';
        $maybe_fullscreen = isset( $options['maybe_fullscreen'] ) ? esc_attr($options['maybe_fullscreen']) : '';
        $maybe_autofocus = isset( $options['maybe_autofocus'] ) ? esc_attr($options['maybe_autofocus']) : '';
        $maybe_backfocus = isset( $options['maybe_backfocus'] ) ? esc_attr($options['maybe_backfocus']) : '';
        $maybe_trapfocus = isset( $options['maybe_trapfocus'] ) ? esc_attr($options['maybe_trapfocus']) : '';
        $animation_duration = isset( $options['animation_duration'] ) ? esc_attr($options['animation_duration']) : '';

        $footer_prepend = isset( $options['footer_prepend'] ) ? esc_attr($options['footer_prepend']) : '';
        
        
        $nav_icon  = isset( $options['nav_icon'] ) ? esc_attr($options['nav_icon']) : "";
        $close_icon  = isset( $options['close_icon'] ) ? esc_attr($options['close_icon']) : "";
        $small_close_icon  = isset( $options['small_close_icon'] ) ? esc_attr($options['small_close_icon']) : "";
        $zoom_icon  = isset( $options['zoom_icon'] ) ? esc_attr($options['zoom_icon']) : "";
        $share_icon  = isset( $options['share_icon'] ) ? esc_attr($options['share_icon']) : "";
        //$slideshow_icon  = isset( $options['slideshow_icon'] ) ? esc_attr($options['slideshow_icon']) : "";
        //$fullscreen_icon  = isset( $options['fullscreen_icon'] ) ? esc_attr($options['fullscreen_icon']) : "";
        $download_icon  = isset( $options['download_icon'] ) ? esc_attr($options['download_icon']) : "";
        
        
        $force_type = isset( $options['force_type'] ) ? esc_attr($options['force_type']) : '';
        
        $stylesheet_name = isset( $options['stylesheet_name'] ) ? esc_attr($options['stylesheet_name']) : '';
        
        global $oxygen_svg_icons_to_load;
        $oxygen_svg_icons_to_load[] = $nav_icon;
        $oxygen_svg_icons_to_load[] = $close_icon;
        $oxygen_svg_icons_to_load[] = $zoom_icon;
        $oxygen_svg_icons_to_load[] = $download_icon;
        $oxygen_svg_icons_to_load[] = $small_close_icon;
        
        
        
        
        
        $output = '<div id="link'. esc_attr($options['selector']) .'" class="oxy-lightbox_link ';
        
        if ($content && ('element' == $inline_content) && 'inline' === $content_type ) {
        
            $output .= 'oxy-inner-content" ';
            
        } else {
            
            $output .= '" ';
            
        }
        
        if ('iframe' === $content_type) {
        
            $output .= 'data-type="iframe" data-src="'. $url .'" ';
            
        }
        
        if ('ajax' === $content_type) {
        
            //$output .= 'data-fancybox data-type="ajax" data-src="'. $url .'" ';
            //$output .= 'data-fancybox data-type="ajax" href="'. $url .'" data-src="'. $url .'" data-filter="'. $ajax_selector .'" ';
            $output .= 'data-fancybox data-type="ajax" data-src="'. $url .'" data-filter="'. $ajax_selector .'" ';
        } 
        
        
        
        $output .= '>';
        
        
            if ($content && (('inline' !==  $content_type) || ('inner' !==  $inline_content ))) {

                if ( function_exists('do_oxygen_elements') ) {
                    $output .=  do_oxygen_elements($content); 
                }
                else {
                    $output .=  do_shortcode($content); 
                } 

            }
        
        $output .= '</div>'; 
        
        if ($content && ('inner' === $inline_content)) {
        
            $output .= '<div class="oxy-lightbox_inner" ';
            
        } else {
            
            $output .= '<div class="oxy-lightbox_inner oxy-inner-content" ';
            
        }
        
        if ('iframe' === $content_type || 'ajax' === $content_type || 'video' === $content_type || 'image' === $content_type) {
        
            $output .= 'data-src="'. $url .'" ';
            
        }
        
        if ('html' === $content_type) {
        
            $output .= 'data-src="'. $custom_html .'" ';
            
        }
        
        if ('ajax' === $content_type ) {
        
            $output .= 'data-filter="'. $ajax_selector .'" ';
            
        } 
        
        if ( 'custom' === $content_type && 'ajax' === $force_type) {
            
            $output .= 'data-filter="'. $force_ajax_selector .'" ';
            
        }
        
        if ('iframe' === $content_type ) {
            
            $output .= ('true' === $maybe_iframe_selector) ? 'data-iframe-selector="'. $iframe_selector .'" ' : '';
            
        }
        
        if ( 'custom' === $content_type && 'iframe' === $force_type) {
            
            $output .= ('true' === $force_maybe_iframe_selector) ? 'data-iframe-selector="'. $force_iframe_selector .'" ' : '';
            
        }
        
        
        if ('inline' === $content_type ) {
            
            $output .= 'data-src="'. $inline_selector .'" ';
            
            $output .= ('inner' === $inline_content) ? 'data-inner-content="true" ' : 'data-inline-selector="'. $element_selector .'" ';
            
            $output .= ('inner' === $inline_content) ? 'data-click-selector="'. $click_selector .'" ' : '';
            
        }
        
        
        $output .= 'data-multiple="' . $maybe_multiple . '" ';
        $output .= 'data-loop="' . $maybe_loop . '" ';
        $output .= 'data-type="'. $content_type .'" ';
        
        $output .= 'data-small-btn="'. $small_close_button .'" ';
        $output .= 'data-iframe-preload="'. $iframe_preload .'" ';
        $output .= 'data-toolbar="'. $toolbar_display .'" ';
        $output .= 'data-thumbs="'. $maybe_thumbs .'" ';
        $output .= 'data-duration="'. $animation_duration .'" '; 
        $output .= 'data-fullscreen="'. $maybe_fullscreen .'" ';
        $output .= 'data-autofocus="'. $maybe_autofocus .'" ';
        $output .= 'data-backfocus="'. $maybe_backfocus .'" ';
        $output .= 'data-trapfocus="'. $maybe_trapfocus .'" ';
        //$output .= 'data-effect="'. $transition_effect .'" ';
        
        $output .= 'data-nav-icon="'. $nav_icon .'" ';
        $output .= 'data-close-icon="'. $close_icon .'" ';
        $output .= 'data-small-close-icon="'. $small_close_icon .'" ';
        $output .= 'data-zoom-icon="'. $zoom_icon .'" ';
        $output .= 'data-share-icon="'. $share_icon .'" ';
        //$output .= 'data-slideshow-icon="'. $slideshow_icon .'" ';
        //$output .= 'data-fullscreen-icon="'. $fullscreen_icon .'" ';
        $output .= 'data-download-icon="'. $download_icon .'" ';
        $output .= 'data-prepend="'. $footer_prepend .'" ';

        $output .= 'data-swipe="'. $maybe_swipe .'" ';
        
        
        if ('custom' === $content_type) {
            
            $output .= 'data-force-type="'. $force_type .'" ';
            
        }
        
        
        
        if ( isset( $options['maybe_css'] ) && 'true' === esc_attr($options['maybe_css']) ) {
            
            $output .= 'data-lightbox-css="'. $stylesheet_name .'" ';
            
        }
        
        
        
        if (('custom' === $content_type)) {
            
            $output .= 'data-click-selector="'. $custom_click_selector .'" ';
            
        }
        
        $output .= '>';
        
        //if (defined('OXY_ELEMENTS_API_AJAX') && !OXY_ELEMENTS_API_AJAX) {
        
            if ($content && ('inline' ===  $content_type) && ('inner' ===  $inline_content)) {
                
                if ( function_exists('do_oxygen_elements') ) {
                    $output .=  do_oxygen_elements($content); 
                }
                else {
                    $output .=  do_shortcode($content); 
                }

            }
            
       // }
        
        $output .= '</div>';
        
        if (defined('OXY_ELEMENTS_API_AJAX') && OXY_ELEMENTS_API_AJAX) {
        
            $output .= '<div class="fancybox-container fancybox-is-open fancybox-can-swipe" role="dialog" tabindex="-1"><div class="fancybox-bg"></div><div class="fancybox-inner"><div class="fancybox-infobar"><span data-fancybox-index="">1</span>&nbsp;/&nbsp;<span data-fancybox-count="">1</span></div><div class="fancybox-toolbar">
            
            <button data-fancybox-zoom="" class="fancybox-button fancybox-button--zoom" title="Zoom"><svg viewBox="0 0 40 40"><use xlink:href="#' . $zoom_icon .'"></use></svg></button>
            
            
            
            <a download="" data-fancybox-download="" class="fancybox-button fancybox-button--download" title="Download" href="javascript:;"><svg viewBox="0 0 40 40"><use xlink:href="#' . $download_icon .'"></use></svg></a>
            
            <button data-fancybox-close="" class="fancybox-button fancybox-button--close" title="Close"><svg viewBox="0 0 40 40"><use xlink:href="#' . $close_icon .'"></use></svg></button></div><div class="fancybox-navigation">
            
            <button data-fancybox-prev="" class="fancybox-button fancybox-button--arrow_left" title="Previous" disabled=""><svg viewBox="0 0 40 40"><use xlink:href="#' . $nav_icon .'"></use></svg></button>
            
            <button data-fancybox-next="" class="fancybox-button fancybox-button--arrow_right" title="Next"><svg viewBox="0 0 40 40"><use xlink:href="#' . $nav_icon .'"></use></svg></button></div><div class="fancybox-stage fancybox-stage-in-builder"><div class="fancybox-slide fancybox-slide--html fancybox-slide--current fancybox-slide--complete" style="">';
            
            
            
            if ($content && ('inner' === $inline_content) && 'inline' ===  $content_type) {
                
                $output .= '<div class="fancybox-content oxy-lightbox_inner oxy-inner-content">';
                //$output .= '<div class="oxy-inner-content oxy-lightbox_inner-flex">';
                if ( function_exists('do_oxygen_elements') ) {
                    $output .=  do_oxygen_elements($content); 
                }
                else {
                    $output .=  do_shortcode($content); 
                }
                //$output .= '</div>';

                
            } else {
            
                $output .= '<div class="fancybox-content fancybox-fake-content">';
                
            }
            
            
            $output .= '<button type="button" data-fancybox-close="" class="fancybox-button fancybox-close-small" title="Close"><svg viewBox="0 0 40 40"><use xlink:href="#' . $small_close_icon .'"></use></svg></button></div></div></div><div class="fancybox-caption"><div class=""fancybox-caption__body">Caption would appear here</div></div></div>
            
            <div class="fancybox-thumbs fancybox-thumbs-x"><div class="fancybox-thumbs__list" style="width: 426px;"><a href="javascript:;" tabindex="0" data-index="0" class="fancybox-thumbs-active"></a><a href="javascript:;" tabindex="0" data-index="1" class=""></a><a href="javascript:;" tabindex="0" data-index="2" class=""></a><a href="javascript:;" tabindex="0" data-index="3" class=""></a></div></div>
            </div>';
            
        }
        
        $this->dequeue_scripts_styles();
        
        echo $output;
        
         // add JavaScript code only once and if shortcode presented
        if ($this->js_added !== true) {
            if (!defined('OXY_ELEMENTS_API_AJAX') || !OXY_ELEMENTS_API_AJAX) {
                add_action( 'wp_footer', array( $this, 'output_js' ) );
            }
            $this->js_added = true;
        }
        
        
    
    }
    
    
    

    function class_names() {
        return array('woocommerce');
    }

    function controls() {
        
        
        $this->addStyleControl(
            array(
                "name" => __('In-builder visibility'),
                "property" => '--extras-lightbox-builder-visibility',
                "control_type" => 'buttons-list',
            )
        )->setValue(array( 
            "none" => "Hidden",
            "block" => "Visible",
            )
        )->setDefaultValue('none');
        
        
        $this->addOptionControl(
            array(
                "type" => 'dropdown',
                "name" => __('Lightbox content'),
                "slug" => 'content_type',
                "default" => 'ajax',
            )
        )->setValue(array( 
            "inline" => "Inline (elements on current page)",
            "ajax" => "AJAX (HTML from another page)",   
            "iframe" => "iFrame",
            "html" => "HTML",
            "video" => "Video",
            "image" => "Image",
            "custom" => "Manual (using links)"
            )
        )->setDefaultValue('ajax');
        
        
        
        
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Link Selector'),
                "slug" => 'custom_click_selector',
                "default" => '.lightbox',
                "condition" => 'content_type=custom',
                "base64" => true,
                "css" 	 => false
            )
        )->setParam("description", __("Provide selector for links containing the href"));
        
        
        $this->addOptionControl(
            array(
                "type" => 'dropdown',
                "name" => __('Force content type'),
                "slug" => 'force_type',
                "condition" => 'content_type=custom',
                "css" 	 => false
            )
        )->setValue(array( 
            "auto" => "Disabled",
            "ajax" => "Ajax",
            "iframe" => "Iframe",
            )
        )->setDefaultValue('auto')
         ->setParam("description", __("Only needed for AJAX / iFrame"));
        
        
        $this->addOptionControl(
            array(
                "type" => 'dropdown',
                "name" => __('Inline content'),
                "slug" => 'inline_content',
                "condition" => 'content_type=inline'
            )
        )->setValue(array( 
            "inner" => "Display elements inside lightbox",
            "elementid" => "Another element on page",
            )
        )->setDefaultValue('elementid')
          ->setValueCSS( array(
                    "inner"  => " {
                         
                    }",
        ) )->setParam("description", __("Click 'Apply Params' button to apply"));   
        
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Element selector'),
                "slug" => 'element_selector',
                "condition" => 'content_type=inline&&inline_content=elementid'
            )
        );
        
        
        
        
        $this->addOptionControl(
            array(
                "type" => 'textarea',
                "name" => __('Custom HTML'),
                "slug" => 'custom_html',
                "default" => 'Some text here..',
                "condition" => 'content_type=html',
                "base64" => true,
            )
        );
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Click selector'),
                "slug" => 'click_selector',
                "default" => '.open-lightbox',
                "condition" => 'content_type=inline&&inline_content!=elementid'
            )
        );
        
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Source'),
                "slug" => 'url',
                "default" => '/my-page/',
                "condition" => 'content_type=video||content_type=ajax||content_type=iframe||content_type=image'
            )
        )->setParam('dynamicdatacode', '<div optionname="\'oxy-lightbox_url\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesLinkMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');
        
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Ajax content selector'),
                "slug" => 'ajax_selector',
                "default" => '.content',
                "condition" => 'content_type=ajax'
            )
        );
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Ajax content selector'),
                "slug" => 'force_ajax_selector',
                "default" => '.content',
                "condition" => 'content_type=custom&&force_type=ajax'
            )
        );
        
        
        $this->addOptionControl(
            array(
                "type" => 'buttons-list',
                "name" => __('Only display part of page'),
                "slug" => 'maybe_iframe_selector',
                "css" 	 => false,
                "default" => 'false',
                "condition" => 'content_type=iframe'
                
            )
        )->setValue(array( 
            "true" => "True",
            "false" => "False",
            )
        )->setParam("description", __("iFrame needs to be on the same domain as the page"));
        
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Iframe content selector'),
                "slug" => 'iframe_selector',
                "default" => '.content',
                "condition" => 'content_type=iframe&&maybe_iframe_selector=true' 
            )
        );
        
        
        $this->addOptionControl(
            array(
                "type" => 'buttons-list',
                "name" => __('Only display part of page (iframe)'),
                "slug" => 'force_maybe_iframe_selector',
                "css" 	 => false,
                "default" => 'false',
                "condition" => 'content_type=custom&&force_type=iframe'
                
            )
        )->setValue(array( 
            "true" => "True",
            "false" => "False",
            )
        )->setParam("description", __("iFrame needs to be on the same domain as the page"));
        
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Iframe content selector'),
                "slug" => 'force_iframe_selector',
                "default" => '.content',
                "condition" => 'content_type=custom&&force_type=iframe&&force_maybe_iframe_selector=true' 
            )
        );
        
        $this->addOptionControl(
            array(
                "type" => 'buttons-list',
                "name" => __('Group into slides'),
                "slug" => 'maybe_multiple',
                "css" 	 => false
                
            )
        )->setValue(array( 
            "true" => "Group",
            "false" => "Single",
            )
        )->setDefaultValue('false')
         ->setValueCSS( array(
                    "false"  => "  {
                                --extras-lightbox-nav-display: hidden;
                                    }",
                    "true"  => "  {
                                    --extras-lightbox-nav-display: visible;
                                }",
        ) )->whiteList();

        $this->addOptionControl(
            array(
                "type" => 'buttons-list',
                "name" => __('Loop slides'),
                "slug" => 'maybe_loop',
                "condition" => 'maybe_multiple=true' 
            )
        )->setValue(array( 
            "true" => "Enable",
            "false" => "Disable",
            )
        )->setDefaultValue('false');


        $this->addOptionControl(
            array(
                "type" => 'buttons-list',
                "name" => __('Allow slides to be swiped'),
                "slug" => 'maybe_swipe',
                "condition" => 'maybe_multiple=true' 
            )
        )->setValue(array( 
            "true" => "Enable",
            "false" => "Disable",
            )
        )->setDefaultValue('false');
       
        
        
        /**
         * config
         */ 
        $config_section = $this->addControlSection("config_section", __("Config"), "assets/icon.png", $this);
        
        
        
        
        $config_section->addOptionControl(
            array(
                "type" => 'buttons-list',
                "name" => __('iFrame preload'),
                "slug" => 'iframe_preload',
                "css" 	 => false
            )
        )->setValue(array( 
            "true" => "Enable",
            "false" => "Disable",
            )
        )->setDefaultValue('true')
         ->setParam("description", __("Recommended to leave enabled so autoheight and/or iframe content selector can be used"));
        
        
        $config_section->addOptionControl(
            array(
                "type" => 'buttons-list',
                "name" => __('iFrame auto height'),
                "slug" => 'iframe_autoheight',
                "css" 	 => false
            )
        )->setValue(array( 
            "true" => "Enable",
            "false" => "Disable",
            )
        )->setDefaultValue('true')
         ->setValueCSS( array(
                    "false"  => " .fancybox-slide.fancybox-slide--iframe .fancybox-content {
                                        height: var(--extras-lightbox-height)!important;
                                    }",
                            ) );
        
        
        
        
        $config_section->addOptionControl(
            array(
                "type" => 'buttons-list',
                "name" => __('Add thumbnails for images'),
                "slug" => 'maybe_thumbs',
                "condition" => 'content_type=custom' 
            )
        )->setValue(array( 
            "true" => "Enable",
            "false" => "Disable",
            )
        )->setDefaultValue('false')
        ->setValueCSS( array(
            "true"  => " .fancybox-thumbs {
                                top: auto;
                                width: auto;
                                bottom: 0;
                                left: 0;
                                right : 0;
                                height: var(--extras-lightbox-thumbnail-height);
                                padding: 10px 10px 5px 10px;
                                box-sizing: border-box;
                                display: block;
                            }
                            
                            .fancybox-show-thumbs .fancybox-inner {
                                right: 0;
                                bottom: var(--extras-lightbox-thumbnail-height);
                            }",
        ) )->setParam("description", __("For when grouping images"));

        $config_section->addStyleControl(
            array(
                "name" => __('Thumbnail area background'),
                "property" => '--extras-lightbox-thumbnail-bg',
                "control_type" => 'colorpicker',
                "condition" => 'content_type=custom&&maybe_thumbs=true' 
            )
        );

        $config_section->addStyleControl( 
            array(
                "name" => __('Thumbnail area height'),
                "property" => '--extras-lightbox-thumbnail-height',
                "control_type" => 'measurebox',
                "condition" => 'content_type=custom&&maybe_thumbs=true'
            )
        )->setUnits('px');

        $config_section->addStyleControl(
            array(
                "name" => __('Selected border color'),
                "property" => '--extras-lightbox-thumbnail-bordercolor',
                "control_type" => 'colorpicker',
                "condition" => 'content_type=custom&&maybe_thumbs=true'
            )
        );

        $config_section->addStyleControl( 
            array(
                "name" => __('Selected border width'),
                "property" => '--extras-lightbox-thumbnail-borderwidth',
                "control_type" => 'measurebox',
                "condition" => 'content_type=custom&&maybe_thumbs=true'
            )
        )->setUnits('px');

        #-lightbox-5082-5 .fancybox-thumbs
        
        /*

        $config_section->addOptionControl(
            array(
                "type" => 'buttons-list',
                "name" => __('Info Bar'),
                "slug" => 'maybe_infobar',
                
            )
        )->setValue(array( 
            "true" => "Enable",
            "false" => "Disable",
            )
        )->setDefaultValue('false');
        
        */
        
        
        $config_section->addStyleControl(
            array(
                "name" => __('Info Bar'),
                "property" => '--extras-lightbox-infobar',
                "control_type" => 'buttons-list',
            )
        )->setValue(array( 
            "none" => "Disable",
            "block" => "Enable",
            )
        )->setDefaultValue('none');
        
        
        $config_section->addOptionControl(
            array(
                "type" => 'buttons-list',
                "name" => __('Prepend in footer'),
                "slug" => 'footer_prepend',
                "css" 	 => false
            )
        )->setValue(array( 
            "true" => "Enable",
            "false" => "Disable",
            )
        )->setDefaultValue('false')
         ->setParam("description", __("Useful if lightbox is inside container with a transform (eg inside a carousel)"));
        
        
       

        
        $config_focus_section = $config_section->addControlSection("config_focus_section", __("Focus"), "assets/icon.png", $this);
        
        $config_focus_section->addOptionControl(
            array(
                "type" => 'buttons-list',
                "name" => __('Focus on the first focusable element after opening'),
                "slug" => 'maybe_autofocus',
                
            )
        )->setValue(array( 
            "true" => "Enable",
            "false" => "Disable",
            )
        )->setDefaultValue('true');
        
        $config_focus_section->addOptionControl(
            array(
                "type" => 'buttons-list',
                "name" => __('Put focus back to active element after closing'),
                "slug" => 'maybe_backfocus',
                
            )
        )->setValue(array( 
            "true" => "Enable",
            "false" => "Disable",
            )
        )->setDefaultValue('true');
        
        $config_focus_section->addOptionControl(
            array(
                "type" => 'buttons-list',
                "name" => __('Trap focus on element outside modal content'),
                "slug" => 'maybe_trapfocus',
                
            )
        )->setValue(array( 
            "true" => "Enable",
            "false" => "Disable",
            )
        )->setDefaultValue('true');
        
        
        $config_section->addOptionControl(
            array(
                "type" => 'buttons-list',
                "name" => __('Include page/template CSS (AJAX)'),
                "slug" => 'maybe_css',
                "css" 	 => false,
            )
        )->setValue(array( 
            "true" => "Enable",
            "false" => "Disable",
            )
        )->setDefaultValue('false');
        
        
        $config_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Load another Oxygen stylesheet when lightbox opens'),
                "slug" => 'stylesheet_name',
                "default" => 'single-post.css',
                "condition" => 'maybe_css=true',
                "css" 	 => false
            )
        )->setParam("description", __("/oxygen/css/....."));
        
        
        
        $config_section->addStyleControl(
            array(
                "name" => __('Video aspect ratio'),
                "property" => '--extras-lightbox-aspect-ratio',
                "control_type" => 'dropdown',
            )
        )->setValue(array( 
            "56.25%" => "16:9 (default)", 
            "75%" => "4:3",
            "100%" => "1:1",
            "50%" => "2:1",
            )
        );
        
        
         /**
         * Lightbox
         */ 
        $lightbox_section = $this->addControlSection("lightbox_section", __("Lightbox styles"), "assets/icon.png", $this);
        
        $lightbox_content_selector = '.fancybox-content';
        
        
        $lightbox_section->addStyleControl( 
            array(
                "name" => __('Width (leave blank for auto)'),
                "default" => "600",
                "property" => '--extras-lightbox-width',
                "control_type" => 'slider-measurebox',
            )
        )
        ->setRange('0','1000','1')
        ->setUnits('px');
        
        $lightbox_section->addStyleControl( 
            array(
                "name" => __('Height (leave blank for auto)'),
                "default" => "600",
                "property" => '--extras-lightbox-height',
                "control_type" => 'slider-measurebox',
                
            )
        )
        ->setRange('0','1000','1')
        ->setUnits('px');
        
        
        
        $lightbox_section->addStyleControl(
            array(
                "name" => __('Backdrop color'),
                "property" => '--extras-lightbox-bg',
                "control_type" => 'colorpicker',
                "default" => 'rgba(0,0,0,0.5)',
            )
        );
        
        $lightbox_section->addStyleControl(
            array(
                "name" => __('Lightbox content background'),
                "property" => '--extras-lightbox-content-bg',
                "control_type" => 'colorpicker',
                "default" => '#fff',
            )
        );
        
       
        
        
        
        $lightbox_section->addStyleControl(
            array(
                "name" => __('Content overflow'),
                "property" => '--extras-lightbox-overflow',
                "control_type" => 'buttons-list',
                "default" => 'auto',
            )
        )->setValue(array( 
            "auto" => "Auto",
            "visible" => "Visible",
            "hidden" => "Hidden",
            )
        );
        
        
        
        $lightbox_section->borderSection('Border', $lightbox_content_selector,$this);
        $lightbox_section->boxShadowSection('Box Shadow', $lightbox_content_selector,$this);
        
        
        /**
         * Lightbox
         */ 
        $positioning_section = $this->addControlSection("positioning_section", __("Positioning"), "assets/icon.png", $this);
        
        
        $positioning_section->addStyleControl(
            array(
                "name" => __('Vertical align'),
                "property" => '--extras-lightbox-vertical-align',
                "control_type" => 'buttons-list',
            )
        )->setValue(array( 
            "top" => "Top",
            "middle" => "Middle",
            "bottom" => "Bottom",
            )
        )->setDefaultValue('middle');
        
        
        $positioning_section->addStyleControl(
            array(
                "name" => __('Horizontal align'),
                "property" => '--extras-lightbox-horizontal-align',
                "control_type" => 'buttons-list',
                "default" => 'center',
            )
        )->setValue(array( 
            "left" => "Left",
            "center" => "Center",
            "right" => "Right",
            )
        );
        
        
        $positioning_section->addStyleControl( 
            array(
                "name" => 'Margin top',
                "property" => '--extras-lightbox-margin-top',
                "control_type" => 'measurebox',
            )
        )->setUnits('px')->setParam('hide_wrapper_end', true);
        
        $positioning_section->addStyleControl( 
            array(
                "name" => 'Margin bottom',
                "property" => '--extras-lightbox-margin-bottom',
                "control_type" => 'measurebox',
            )
        )->setUnits('px')->setParam('hide_wrapper_start', true);
        
        $positioning_section->addStyleControl(
            array(
                "name" => 'Margin left',
                "property" => '--extras-lightbox-margin-left',
                "control_type" => 'measurebox',
            )
        )
        ->setUnits('px')->setParam('hide_wrapper_end', true);
        
          $positioning_section->addStyleControl( 
            array(
                "name" => 'Margin right',
                "property" => '--extras-lightbox-margin-right',
                "control_type" => 'measurebox',
            )
        )->setUnits('px')->setParam('hide_wrapper_start', true);
        
        
        /**
         * Size & Spacing
         */ 
        $size_spacing_section = $lightbox_section->addControlSection("size_spacing_section", __("Content Spacing"), "assets/icon.png", $this);
        
        $size_spacing_section->addCustomControl(
            '<div style="color: #fff; line-height: 1.3; font-size: 13px;">Lightbox content<hr></div>','description');
        
        
        
        
        $size_spacing_section->addStyleControl( 
            array(
                "name" => 'Padding top',
                "property" => '--extras-lightbox-padding-top',
                "control_type" => 'measurebox',
                "condition" => 'content_type!=video',
                "default" => '30'
            )
        )->setUnits('px')->setParam('hide_wrapper_end', true);
        
        $size_spacing_section->addStyleControl( 
            array(
                "name" => 'Padding bottom',
                "property" => '--extras-lightbox-padding-bottom',
                "control_type" => 'measurebox',
                "condition" => 'content_type!=video',
                "default" => '30'
            )
        )->setUnits('px')->setParam('hide_wrapper_start', true);
        
        $size_spacing_section->addStyleControl(
            array(
                "name" => 'Padding left',
                "property" => '--extras-lightbox-padding-left',
                "control_type" => 'measurebox',
                "condition" => 'content_type!=video',
                "default" => '30'
            )
        )
        ->setUnits('px')->setParam('hide_wrapper_end', true);
        
          $size_spacing_section->addStyleControl( 
            array(
                "name" => 'Padding right',
                "property" => '--extras-lightbox-padding-right',
                "control_type" => 'measurebox',
                "condition" => 'content_type!=video',
                "default" => '30'
            )
        )->setUnits('px')->setParam('hide_wrapper_start', true);
        
        
        $slide_spacing_section = $lightbox_section->addControlSection("slide_spacing_section", __("Slide Spacing"), "assets/icon.png", $this);
        
        $slide_spacing_section->addCustomControl(
            '<div style="color: #fff; line-height: 1.3; font-size: 13px;">To prevent the lightbox content from touching the edges of the viewport..</div>','description');
        
        $slide_spacing_section->addStyleControl(
            array(
                "name" => 'Padding left',
                "property" => '--extras-slide-padding-left',
                "control_type" => 'measurebox',
                "default" => "20",
            )
        )
        ->setUnits('px')->setParam('hide_wrapper_end', true);
        
        $slide_spacing_section->addStyleControl( 
            array(
                "name" => 'Padding right',
                "property" => '--extras-slide-padding-right',
                "control_type" => 'measurebox',
                "default" => "20",
            )
        )->setUnits('px')->setParam('hide_wrapper_start', true);
        
        $slide_spacing_section->addStyleControl( 
            array(
                "name" => 'Padding top',
                "property" => '--extras-slide-padding-top',
                "control_type" => 'measurebox',
                "default" => "20",
            )
        )->setUnits('px')->setParam('hide_wrapper_end', true);
        
        $slide_spacing_section->addStyleControl( 
            array(
                "name" => 'Padding bottom',
                "property" => '--extras-slide-padding-bottom',
                "control_type" => 'measurebox',
                "default" => "20",
            )
        )->setUnits('px')->setParam('hide_wrapper_start', true);
        
        

        

        
        
        /**
         * Navigation
         */ 
        $nav_section = $this->addControlSection("nav_section", __("Navigation arrows"), "assets/icon.png", $this);
        
        $nav_section->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Navigation Icon'),
                "slug" => 'nav_icon',
                "value" => 'FontAwesomeicon-chevron-left', 
            )
        )->setParam("description", __("Only need to choose the left icon"));
        
        
        $nav_section->addStyleControl( 
            array(
                "name" => __('Icon Size'),
                "default" => "14",
                "property" => '--extras-lightbox-isize',
                "control_type" => 'slider-measurebox',
            )
        )
        ->setRange('0','100','1')
        ->setUnits('px');
        
        
        $nav_section->addStyleControl( 
            array(
                "name" => __('Color'),
                "default" => "#eee",
                "property" => '--extras-lightbox-icolor',
                "control_type" => 'colorpicker',
            )
        )->setParam('hide_wrapper_end', true);
        
        $nav_section->addStyleControl( 
            array(
                "name" => __('Hover Color'),
                "property" => '--extras-lightbox-icolor-hover',
                "control_type" => 'colorpicker',
            )
        )->setParam('hide_wrapper_start', true);
        
        
        $nav_section->addStyleControl( 
            array(
                "name" => __('Background'),
                "default" => 'rgba(20,20,20,0.15)',
                "property" => '--extras-lightbox-ibg',
                "control_type" => 'colorpicker',
            )
        )->setParam('hide_wrapper_end', true);
        
        $nav_section->addStyleControl( 
            array(
                "name" => __('Hover Background'),
                "property" => '--extras-lightbox-ibg-hover',
                "control_type" => 'colorpicker',
            )
        )->setParam('hide_wrapper_start', true);
        
       
        
        $nav_section->addStyleControl(
            array(
                "name" => 'Border radius',
                "property" => '--extras-lightbox-iradius',
                "control_type" => 'slider-measurebox',
            )
        )->setRange('0','60','1')
        ->setUnits('px');
        
         $nav_section->addStyleControl(
            array(
                "name" => 'Margin',
                "property" => '--extras-lightbox-imargin',
                "default" => '0',
                "control_type" => 'slider-measurebox',
            )
        )->setRange('0','100','1')
        ->setUnits('px');
        
        
        $nav_section->addStyleControl( 
            array(
                "name" => 'Padding',
                "property" => '--extras-lightbox-ipadding',
                "default" => '10',
                "control_type" => 'slider-measurebox',
            )
        )->setRange('0','100','1')
        ->setUnits('px');
        
        
         /**
         * Toolbar
         */ 
        $toolbar_section = $this->addControlSection("toolbar_section", __("Toolbar"), "assets/icon.png", $this);
        
        
        $toolbar_section->addOptionControl(
            array(
                "type" => 'buttons-list',
                "name" => __('Toolbar display'),
                "slug" => 'toolbar_display',
                
            )
        )->setValue(array( 
            "true" => "Enable",
            "false" => "Disable",
            )
        )->setDefaultValue('true')
         ->setValueCSS( array(
                    "false"  => " .fancybox-toolbar {
                                         display: none;
                                    }",
        ) )->setParam("description", __("Note - Icons will change depending on the type of lightbox content"));
        
        
        /**
         * Icon styles
         */ 
        //$toolbar_icon_section = $toolbar_section->addControlSection("toolbar_icon_section", __("Toolbar icon styles"), "assets/icon.png", $this);
        
        
        $toolbar_section->addStyleControl( 
            array(
                "name" => __('Icon Size'),
                "default" => "14",
                "property" => '--extras-lightbox-csize',
                "control_type" => 'slider-measurebox',
                "condition" => 'toolbar_display=true'
            )
        )
        ->setRange('0','100','1')
        ->setUnits('px');
        
        
        $toolbar_section->addStyleControl( 
            array(
                "name" => __('Color'),
                "default" => "#eee",
                "property" => '--extras-lightbox-ccolor',
                "control_type" => 'colorpicker',
                "condition" => 'toolbar_display=true'
            )
        )->setParam('hide_wrapper_end', true);
        
        $toolbar_section->addStyleControl( 
            array(
                "name" => __('Hover Color'),
                "property" => '--extras-lightbox-ccolor-hover',
                "control_type" => 'colorpicker',
                "condition" => 'toolbar_display=true'
            )
        )->setParam('hide_wrapper_start', true);
        
        
        $toolbar_section->addStyleControl( 
            array(
                "name" => __('Background'),
                "default" => 'rgba(20,20,20,0.15)',
                "property" => '--extras-lightbox-cbg',
                "control_type" => 'colorpicker',
                "condition" => 'toolbar_display=true'
            )
        )->setParam('hide_wrapper_end', true);
        
        $toolbar_section->addStyleControl( 
            array(
                "name" => __('Hover Background'),
                "property" => '--extras-lightbox-cbg-hover',
                "control_type" => 'colorpicker',
                "condition" => 'toolbar_display=true'
            )
        )->setParam('hide_wrapper_start', true);
        
        $toolbar_section->addStyleControl(
            array(
                "name" => 'Border radius',
                "property" => '--extras-lightbox-cradius',
                "control_type" => 'slider-measurebox',
                "condition" => 'toolbar_display=true'
            )
        )->setRange('0','60','1')
        ->setUnits('px');
        
         $toolbar_section->addStyleControl(
            array(
                "name" => 'Toolbar margin',
                "property" => '--extras-lightbox-tmargin',
                "default" => '10',
                "control_type" => 'slider-measurebox',
                "condition" => 'toolbar_display=true'
            )
        )->setRange('0','100','1')
        ->setUnits('px');
        
        $toolbar_section->addStyleControl(
            array(
                "name" => 'Icon margin',
                "property" => '--extras-lightbox-cmargin',
                "default" => '10',
                "control_type" => 'slider-measurebox',
                "condition" => 'toolbar_display=true'
            )
        )->setRange('0','100','1')
        ->setUnits('px');
        
        
        $toolbar_section->addStyleControl( 
            array(
                "name" => 'Icon padding',
                "property" => '--extras-lightbox-cpadding',
                "default" => '10',
                "control_type" => 'slider-measurebox',
                "condition" => 'toolbar_display=true'
            )
        )->setRange('0','100','1')
        ->setUnits('px');
        
        
        
        /**
         * Zoom
         */ 
        $zoom_section = $toolbar_section->addControlSection("zoom_section", __("Zoom icon"), "assets/icon.png", $this);
        
        
        $zoom_section->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Zoom Icon'),
                "slug" => 'zoom_icon',
                "value" => 'FontAwesomeicon-search', 
            )
        );
        
        
        /**
         * Download
         */ 
        $download_section = $toolbar_section->addControlSection("download_section", __("Download icon"), "assets/icon.png", $this);
        
        
        $download_section->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Download Icon'),
                "slug" => 'download_icon',
                "value" => 'FontAwesomeicon-download', 
            )
        );
        
        
        
        /**
         * Closing
         */ 
        $closing_section = $toolbar_section->addControlSection("closing_section", __("Close icon"), "assets/icon.png", $this);
        
        
        $closing_section->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Close Icon'),
                "slug" => 'close_icon',
                "value" => 'FontAwesomeicon-close', 
            )
        );
        
        
        /**
         * Small close
         */ 
        $small_button_section = $this->addControlSection("small_button_section", __("Small close button"), "assets/icon.png", $this);
        $small_button_selector = '.fancybox-close-small';        
        
        $small_button_section->addCustomControl(
            '<div style="color: #fff; line-height: 1.3; font-size: 13px;"></div>','description');

        $small_button_icon_section = $small_button_section->addControlSection("small_button_icon_section", __("Change icon"), "assets/icon.png", $this);

        $small_button_section->addOptionControl(
            array(
                "type" => 'buttons-list',
                "name" => __('Small close button'),
                "slug" => 'small_close_button',
                
            )
        )->setValue(array( 
            "true" => "Enable",
            "false" => "Disable",
            )
        )->setDefaultValue('true')
            ->setValueCSS( array(
                    "false"  => " .fancybox-close-small {
                                            display: none;
                                    }",
        ) );    

        $small_button_icon_section->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Small close Icon'),
                "slug" => 'small_close_icon',
                "value" => 'Lineariconsicon-cross', 
            )
        );

        $small_button_section->addStyleControl( 
            array(
                "name" => __('Icon size'),
                "default" => "",
                "property" => '--extras-lightbox-smallsize',
                "control_type" => 'slider-measurebox',
            )
        )
        ->setRange('0','40','1')
        ->setUnits('px');

        $small_button_section->addStyleControl( 
            array(
                "name" => __('Transition duration'),
                "default" => "",
                "selector" => $small_button_selector,
                "property" => 'transition-duration',
                "control_type" => 'slider-measurebox',
            )
        )
        ->setRange('0','600','1')
        ->setUnits('ms');

        $small_button_section->addStyleControl( 
            array(
                "name" => __('Color'),
                "default" => "",
                "property" => '--extras-lightbox-smallcolor',
                "control_type" => 'colorpicker',
            )
        )->setParam('hide_wrapper_end', true);
        
        $small_button_section->addStyleControl( 
            array(
                "name" => __('Hover Color'),
                "property" => '--extras-lightbox-smallcolor-hover',
                "control_type" => 'colorpicker',
            )
        )->setParam('hide_wrapper_start', true);
        
        
        $small_button_section->addStyleControl( 
            array(
                "name" => __('Background'),
                "default" => '',
                "property" => '--extras-lightbox-smallbg',
                "control_type" => 'colorpicker',
            )
        )->setParam('hide_wrapper_end', true);
        
        $small_button_section->addStyleControl( 
            array(
                "name" => __('Hover Background'),
                "property" => '--extras-lightbox-smallbg-hover',
                "control_type" => 'colorpicker',
            )
        )->setParam('hide_wrapper_start', true);


        $small_button_positioning_section = $small_button_section->addControlSection("small_button_positioning_section", __("Spacing"), "assets/icon.png", $this);

        $small_button_positioning_section->addPreset(
            "padding",
            "small_button_padding",
            __("Padding"),
            $small_button_selector
        )->whiteList();
        
        
        $small_button_positioning_section->addPreset(
            "margin",
            "small_button_margin",
            __("Margin"),
            $small_button_selector
        )->whiteList();


        $small_button_section->borderSection('Border', '.fancybox-close-small',$this);
        $small_button_section->boxShadowSection('Box Shadow', '.fancybox-close-small',$this);




        /**
         * Animations
         */ 
        $animations_section = $this->addControlSection("animations_section", __("Animation"), "assets/icon.png", $this);
        
        $current_selector = '.fancybox-fx-extras.fancybox-slide--current';
        $adjacent_selector = '.fancybox-fx-extras.fancybox-slide--previous, .fancybox-fx-extras.fancybox-slide--next';
        
        
        $animations_section->addOptionControl(
            array(
                "type" => 'buttons-list',
                "name" => __('Preview starting position'),
                "slug" => 'animation_preview',
            )
        )->setValue(array( 
            "true" => "Preview",
            "false" => "Disable",
            )
        )->setDefaultValue('false')
         ->setValueCSS( array(
                    "true"  => " .fancybox-stage-in-builder .fancybox-slide {
                                        opacity: 0.3;
                                        transform: translate(var(--extras-lightbox-translatex),var(--extras-lightbox-translatey)) scale(var(--extras-lightbox-scale)) rotate3d(var(--extras-lightbox-rotatex),var(--extras-lightbox-rotatey),var(--extras-lightbox-rotatez),var(--extras-lightbox-rotatedeg));
                                    }
                    ",
                    "false"  => " .fancybox-inner .fancybox-stage-in-builder .fancybox-slide {
                                        opacity: 1;
                                        transform: none;
                                    }
                    ",
        ) )->setParam('ng_show', "!iframeScope.isEditing('media')");
        
        /*
        $animations_section->addOptionControl(
            array(
                "type" => 'buttons-list',
                "name" => __('Apply animation to..'),
                "slug" => 'animate_element',
            )
        )->setValue(array( 
            "content" => "Content",
            "slide" => "Full slide",
            )
        )->setDefaultValue('slide')
         ->setValueCSS( array(
                    "content"  => " .fancybox-stage-in-builder .fancybox-slide {
                                        opacity: 1;
                                        transform: none;
                                    }
                                    
                                    .fancybox-stage-in-builder .fancybox-content {
                                        opacity: 0.3;
                                        transform: translate(var(--extras-lightbox-translatex),var(--extras-lightbox-translatey)) scale(var(--extras-lightbox-scale));
                                    }
                    ",
                     "slide"  => " .fancybox-stage-in-builder .fancybox-slide {
                                            opacity: 0.3;
                                            transform: translate(var(--extras-lightbox-translatex),var(--extras-lightbox-translatey)) scale(var(--extras-lightbox-scale));
                                        }

                                       .fancybox-stage-in-builder .fancybox-content {
                                            opacity: 1;
                                            transform: none;
                                        }
                            ",
        ) ); */
        
        
        
            
        
          $animations_section->addStyleControl( 
            array(
                "name" => __('Translate X'),
                "property" => '--extras-lightbox-translatex',
                "control_type" => 'measurebox',
                "default" => '0',
            )
        )->setUnits('px')->setParam('hide_wrapper_end', true);
        
        $animations_section->addStyleControl( 
            array(
                "name" => __('Translate Y'),
                "property" => '--extras-lightbox-translatey',
                "control_type" => 'measurebox',
                "default" => '10',
            )
        )->setUnits('px')->setParam('hide_wrapper_start', true);
        
        $animations_section->addStyleControl( 
            array(
                "name" => __('Scale'),
                "property" => '--extras-lightbox-scale',
                "control_type" => 'slider-measurebox',
                "default" => '.95',
            )
        )
        ->setRange('0.8','1.2','.01');
        
        
        $animations_section->addCustomControl(
            '<div style="color: #fff; line-height: 1.3; font-size: 13px;">Rotate<hr style="opacity: .25;"></div>','description');
        
        
        $animations_section->addStyleControl( 
            array(
                "name" => __('Rotate X'),
                "property" => '--extras-lightbox-rotatex',
                "control_type" => 'slider-measurebox',
                "default" => '1',
            )
        )
        ->setRange('-100','100','1');
        
        $animations_section->addStyleControl( 
            array(
                "name" => __('Rotate Y'),
                "property" => '--extras-lightbox-rotatey',
                "control_type" => 'slider-measurebox',
                "default" => '1',
            )
        )
        ->setRange('-100','100','1');
        
        $animations_section->addStyleControl( 
            array(
                "name" => __('Rotate Z'),
                "property" => '--extras-lightbox-rotatez',
                "control_type" => 'slider-measurebox',
                "default" => '1',
            )
        )
        ->setRange('-100','100','1');
        
        $animations_section->addStyleControl( 
            array(
                "name" => __('Rotate angle'),
                "property" => '--extras-lightbox-rotatedeg',
                "control_type" => 'slider-measurebox',
                "default" => '0',
            )
        )
        ->setRange('-360','360','1')
        ->setUnits('deg');
        
        
        $animations_section->addCustomControl(
            '<div style="color: #fff; line-height: 1.3; font-size: 13px;"><hr style="opacity: .25;"></div>','description');
        
        $animations_section->addStyleControl( 
            array(
                "name" => __('Transform origin left'),
                "property" => '--extras-lightbox-originx',
                "control_type" => 'measurebox',
                "default" => '50',
            )
        )->setUnits('%')->setParam('hide_wrapper_end', true);
        
        $animations_section->addStyleControl( 
            array(
                "name" => __('Transform origin top'),
                "property" => '--extras-lightbox-originy',
                "control_type" => 'measurebox',
                "default" => '50',
            )
        )->setUnits('%')->setParam('hide_wrapper_start', true);
        
        
        $animations_section->addOptionControl(
            array(
                "type" => 'slider-measurebox',
                "name" => __('Duration'),
                "slug" => 'animation_duration',
                "default" => '300',
            )
        )->setRange('0','1400','1')
         ->setUnits('ms');
        
        
        

    }
    
    
    function allowedEmptyOptions($options) {

        $options_to_add = array(
            "oxy-lightbox_ajax_selector",
            "oxy-lightbox_iframe_selector",
            "oxy-lightbox_force_ajax_selector",
            "oxy-lightbox_force_iframe_selector",
            
        );

        $options = array_merge($options, $options_to_add);

        return $options;
    }
    
    
    function customCSS($options, $selector) {
        
        $css = '';
        
        if( ! $this->css_added ) {
        
        $css .= 'body.compensate-for-scrollbar{overflow:hidden}.fancybox-active{height:auto}.fancybox-is-hidden{left:-9999px;margin:0;position:absolute!important;top:-9999px;visibility:hidden}.fancybox-container{-webkit-backface-visibility:hidden;height:100%;left:0;outline:none;position:fixed;-webkit-tap-highlight-color:transparent;top:0;-ms-touch-action:manipulation;touch-action:manipulation;transform:translateZ(0);width:100%;z-index:99992}.fancybox-container *{box-sizing:border-box}.fancybox-bg,.fancybox-inner,.fancybox-outer,.fancybox-stage{bottom:0;left:0;position:absolute;right:0;top:0}.fancybox-outer{-webkit-overflow-scrolling:touch;overflow-y:auto}.fancybox-bg{background:#1e1e1e;opacity:0;transition-duration:inherit;transition-property:opacity;transition-timing-function:cubic-bezier(.47,0,.74,.71)}.fancybox-is-open .fancybox-bg{opacity:.9;transition-timing-function:cubic-bezier(.22,.61,.36,1)}.fancybox-caption,.fancybox-infobar,.fancybox-navigation .fancybox-button,.fancybox-toolbar{direction:ltr;opacity:0;position:absolute;transition:opacity .25s ease,visibility 0s ease .25s;visibility:hidden;z-index:99997}.fancybox-show-caption .fancybox-caption,.fancybox-show-infobar .fancybox-infobar,.fancybox-show-nav .fancybox-navigation .fancybox-button,.fancybox-show-toolbar .fancybox-toolbar{opacity:1;transition:opacity .25s ease 0s,visibility 0s ease 0s;visibility:visible}.fancybox-infobar{color:#ccc;font-size:13px;-webkit-font-smoothing:subpixel-antialiased;height:44px;left:0;line-height:44px;min-width:44px;mix-blend-mode:difference;padding:0 10px;pointer-events:none;top:0;-webkit-touch-callout:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none}.fancybox-toolbar{right:0;top:0}.fancybox-stage{direction:ltr;overflow:visible;transform:translateZ(0);z-index:99994}.fancybox-is-open .fancybox-stage{overflow:hidden}.fancybox-slide{-webkit-backface-visibility:hidden;display:none;height:100%;left:0;outline:none;overflow:auto;-webkit-overflow-scrolling:touch;padding:44px;position:absolute;text-align:center;top:0;transition-property:transform,opacity;white-space:normal;width:100%;z-index:99994}.fancybox-slide:before{content:"";display:inline-block;font-size:0;height:100%;vertical-align:middle;width:0}.fancybox-is-sliding .fancybox-slide,.fancybox-slide--current,.fancybox-slide--next,.fancybox-slide--previous{display:block}.fancybox-slide--image{overflow:hidden;padding:44px 0}.fancybox-slide--image:before{display:none}.fancybox-slide--html{padding:6px}.fancybox-content{background:#fff;display:inline-block;margin:0;max-width:100%;overflow:auto;-webkit-overflow-scrolling:touch;padding:44px;position:relative;text-align:left;vertical-align:middle}.fancybox-slide--image .fancybox-content{animation-timing-function:cubic-bezier(.5,0,.14,1);-webkit-backface-visibility:hidden;background:transparent;background-repeat:no-repeat;background-size:100% 100%;left:0;max-width:none;overflow:visible;padding:0;position:absolute;top:0;transform-origin:top left;transition-property:transform,opacity;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;z-index:99995}.fancybox-can-zoomOut .fancybox-content{cursor:zoom-out}.fancybox-can-zoomIn .fancybox-content{cursor:zoom-in}.fancybox-can-pan .fancybox-content,.fancybox-can-swipe .fancybox-content{cursor:grab}.fancybox-is-grabbing .fancybox-content{cursor:grabbing}.fancybox-container [data-selectable=true]{cursor:text}.fancybox-image,.fancybox-spaceball{background:transparent;border:0;height:100%;left:0;margin:0;max-height:none;max-width:none;padding:0;position:absolute;top:0;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;width:100%}.fancybox-spaceball{z-index:1}.fancybox-slide--iframe .fancybox-content,.fancybox-slide--map .fancybox-content,.fancybox-slide--pdf .fancybox-content,.fancybox-slide--video .fancybox-content{height:100%;overflow:visible;padding:0;width:100%}.fancybox-slide--video .fancybox-content{background:#000}.fancybox-slide--map .fancybox-content{background:#e5e3df}.fancybox-slide--iframe .fancybox-content{background:#fff}.fancybox-iframe,.fancybox-video{background:transparent;border:0;display:block;height:100%;margin:0;overflow:hidden;padding:0;width:100%}.fancybox-iframe{left:0;position:absolute;top:0}.fancybox-error{background:#fff;cursor:default;max-width:400px;padding:40px;width:100%}.fancybox-error p{color:#444;font-size:16px;line-height:20px;margin:0;padding:0}.fancybox-button{background:rgba(30,30,30,.6);border:0;border-radius:0;box-shadow:none;cursor:pointer;display:inline-block;height:44px;margin:0;padding:10px;position:relative;transition:color .2s;vertical-align:top;visibility:inherit;width:44px}.fancybox-button,.fancybox-button:link,.fancybox-button:visited{color:#ccc}.fancybox-button:hover{color:#fff}.fancybox-button:focus{outline:none}.fancybox-button.fancybox-focus{outline:1px dotted}.fancybox-button[disabled],.fancybox-button[disabled]:hover{color:#888;cursor:default;outline:none}.fancybox-button div{height:100%}.fancybox-button svg{display:block;height:100%;overflow:visible;position:relative;width:100%}.fancybox-button svg path{fill:currentColor;stroke-width:0}.fancybox-button--fsenter svg:nth-child(2),.fancybox-button--fsexit svg:first-child,.fancybox-button--pause svg:first-child,.fancybox-button--play svg:nth-child(2){display:none}.fancybox-progress{background:#ff5268;height:2px;left:0;position:absolute;right:0;top:0;transform:scaleX(0);transform-origin:0;transition-property:transform;transition-timing-function:linear;z-index:99998}.fancybox-close-small{background:transparent;border:0;border-radius:0;color:#ccc;cursor:pointer;opacity:.8;padding:8px;position:absolute;right:-12px;top:-44px;z-index:401}.fancybox-close-small:hover{color:#fff;opacity:1}.fancybox-slide--html .fancybox-close-small{color:currentColor;padding:10px;right:0;top:0}.fancybox-slide--image.fancybox-is-scaling .fancybox-content{overflow:hidden}.fancybox-is-scaling .fancybox-close-small,.fancybox-is-zoomable.fancybox-can-pan .fancybox-close-small{display:none}.fancybox-navigation .fancybox-button{background-clip:content-box;height:100px;opacity:0;position:absolute;top:calc(50% - 50px);width:70px}.fancybox-navigation .fancybox-button div{padding:7px}.fancybox-navigation .fancybox-button--arrow_left{left:0;left:env(safe-area-inset-left);padding:31px 26px 31px 6px}.fancybox-navigation .fancybox-button--arrow_right{padding:31px 6px 31px 26px;right:0;right:env(safe-area-inset-right)}.fancybox-caption{background:linear-gradient(0deg,rgba(0,0,0,.85) 0,rgba(0,0,0,.3) 50%,rgba(0,0,0,.15) 65%,rgba(0,0,0,.075) 75.5%,rgba(0,0,0,.037) 82.85%,rgba(0,0,0,.019) 88%,transparent);bottom:0;color:#eee;font-size:14px;font-weight:400;left:0;line-height:1.5;padding:75px 44px 25px;pointer-events:none;right:0;text-align:center;z-index:99996}@supports (padding:max(0px)){.fancybox-caption{padding:75px max(44px,env(safe-area-inset-right)) max(25px,env(safe-area-inset-bottom)) max(44px,env(safe-area-inset-left))}}.fancybox-caption--separate{margin-top:-50px}.fancybox-caption__body{max-height:50vh;overflow:auto;pointer-events:all}.fancybox-caption a,.fancybox-caption a:link,.fancybox-caption a:visited{color:#ccc;text-decoration:none}.fancybox-caption a:hover{color:#fff;text-decoration:underline}.fancybox-loading{animation:a 1s linear infinite;background:transparent;border:4px solid #888;border-bottom-color:#fff;border-radius:50%;height:50px;left:50%;margin:-25px 0 0 -25px;opacity:.7;padding:0;position:absolute;top:50%;width:50px;z-index:99999}@keyframes a{to{transform:rotate(1turn)}}.fancybox-animated{transition-timing-function:cubic-bezier(0,0,.25,1)}.fancybox-fx-slide.fancybox-slide--previous{opacity:0;transform:translate3d(-100%,0,0)}.fancybox-fx-slide.fancybox-slide--next{opacity:0;transform:translate3d(100%,0,0)}.fancybox-fx-slide.fancybox-slide--current{opacity:1;transform:translateZ(0)}.fancybox-fx-fade.fancybox-slide--next,.fancybox-fx-fade.fancybox-slide--previous{opacity:0;transition-timing-function:cubic-bezier(.19,1,.22,1)}.fancybox-fx-fade.fancybox-slide--current{opacity:1}.fancybox-fx-zoom-in-out.fancybox-slide--previous{opacity:0;transform:scale3d(1.5,1.5,1.5)}.fancybox-fx-zoom-in-out.fancybox-slide--next{opacity:0;transform:scale3d(.5,.5,.5)}.fancybox-fx-zoom-in-out.fancybox-slide--current{opacity:1;transform:scaleX(1)}.fancybox-fx-rotate.fancybox-slide--previous{opacity:0;transform:rotate(-1turn)}.fancybox-fx-rotate.fancybox-slide--next{opacity:0;transform:rotate(1turn)}.fancybox-fx-rotate.fancybox-slide--current{opacity:1;transform:rotate(0deg)}.fancybox-fx-circular.fancybox-slide--previous{opacity:0;transform:scale3d(0,0,0) translate3d(-100%,0,0)}.fancybox-fx-circular.fancybox-slide--next{opacity:0;transform:scale3d(0,0,0) translate3d(100%,0,0)}.fancybox-fx-circular.fancybox-slide--current{opacity:1;transform:scaleX(1) translateZ(0)}.fancybox-fx-tube.fancybox-slide--previous{transform:translate3d(-100%,0,0) scale(.1) skew(-10deg)}.fancybox-fx-tube.fancybox-slide--next{transform:translate3d(100%,0,0) scale(.1) skew(10deg)}.fancybox-fx-tube.fancybox-slide--current{transform:translateZ(0) scale(1)}@media (max-height:576px){.fancybox-slide{padding-left:6px;padding-right:6px}.fancybox-slide--image{padding:6px 0}.fancybox-close-small{right:-6px}.fancybox-slide--image .fancybox-close-small{background:#4e4e4e;color:#f2f4f6;height:36px;opacity:1;padding:6px;right:0;top:0;width:36px}.fancybox-caption{padding-left:12px;padding-right:12px}@supports (padding:max(0px)){.fancybox-caption{padding-left:max(12px,env(safe-area-inset-left));padding-right:max(12px,env(safe-area-inset-right))}}}.fancybox-share{background:#f4f4f4;border-radius:3px;max-width:90%;padding:30px;text-align:center}.fancybox-share h1{color:#222;font-size:35px;font-weight:700;margin:0 0 20px}.fancybox-share p{margin:0;padding:0}.fancybox-share__button{border:0;border-radius:3px;display:inline-block;font-size:14px;font-weight:700;line-height:40px;margin:0 5px 10px;min-width:130px;padding:0 15px;text-decoration:none;transition:all .2s;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;white-space:nowrap}.fancybox-share__button:link,.fancybox-share__button:visited{color:#fff}.fancybox-share__button:hover{text-decoration:none}.fancybox-share__button--fb{background:#3b5998}.fancybox-share__button--fb:hover{background:#344e86}.fancybox-share__button--pt{background:#bd081d}.fancybox-share__button--pt:hover{background:#aa0719}.fancybox-share__button--tw{background:#1da1f2}.fancybox-share__button--tw:hover{background:#0d95e8}.fancybox-share__button svg{height:25px;margin-right:7px;position:relative;top:-1px;vertical-align:middle;width:25px}.fancybox-share__button svg path{fill:#fff}.fancybox-share__input{background:transparent;border:0;border-bottom:1px solid #d7d7d7;border-radius:0;color:#5d5b5b;font-size:14px;margin:10px 0 0;outline:none;padding:10px 15px;width:100%}.fancybox-thumbs{background:#ddd;bottom:0;display:none;margin:0;-webkit-overflow-scrolling:touch;-ms-overflow-style:-ms-autohiding-scrollbar;padding:2px 2px 4px;position:absolute;right:0;-webkit-tap-highlight-color:rgba(0,0,0,0);top:0;width:212px;z-index:99995}.fancybox-thumbs-x{overflow-x:auto;overflow-y:hidden}.fancybox-show-thumbs .fancybox-thumbs{display:block}.fancybox-show-thumbs .fancybox-inner{right:212px}.fancybox-thumbs__list{font-size:0;height:100%;list-style:none;margin:0;overflow-x:hidden;overflow-y:auto;padding:0;position:absolute;position:relative;white-space:nowrap;width:100%}.fancybox-thumbs-x .fancybox-thumbs__list{overflow:hidden}.fancybox-thumbs-y .fancybox-thumbs__list::-webkit-scrollbar{width:7px}.fancybox-thumbs-y .fancybox-thumbs__list::-webkit-scrollbar-track{background:#fff;border-radius:10px;box-shadow:inset 0 0 6px rgba(0,0,0,.3)}.fancybox-thumbs-y .fancybox-thumbs__list::-webkit-scrollbar-thumb{background:#2a2a2a;border-radius:10px}.fancybox-thumbs__list a{-webkit-backface-visibility:hidden;backface-visibility:hidden;background-color:rgba(0,0,0,.1);background-position:50%;background-repeat:no-repeat;background-size:cover;cursor:pointer;float:left;height:75px;margin:2px;max-height:calc(100% - 8px);max-width:calc(50% - 4px);outline:none;overflow:hidden;padding:0;position:relative;-webkit-tap-highlight-color:transparent;width:100px}.fancybox-thumbs__list a:before{border:6px solid #ff5268;bottom:0;content:"";left:0;opacity:0;position:absolute;right:0;top:0;transition:all .2s cubic-bezier(.25,.46,.45,.94);z-index:99991}.fancybox-thumbs__list a:focus:before{opacity:.5}.fancybox-thumbs__list a.fancybox-thumbs-active:before{opacity:1}@media (max-width:576px){.fancybox-thumbs{width:110px}.fancybox-show-thumbs .fancybox-inner{right:110px}.fancybox-thumbs__list a{max-width:calc(100% - 10px)}}';
        
        $css .= "
                .oxy-lightbox_link {
                    cursor: pointer;
                }   
                
                body:not(.oxygen-builder-body) .oxy-lightbox_inner[data-inner-content=true] {
                    display: none;
                } 

                body.fancybox-active,
                body.oxygen-aos-enabled.fancybox-active {
                    overflow: hidden;
                }
        
                .oxy-lightbox {
                    --extras-lightbox-builder-visibility: none;
                    --extras-lightbox-smallcolor-hover: var(--extras-lightbox-smallcolor);
                    --extras-lightbox-smallbg-hover: var(--extras-lightbox-smallbg);
                }
                
                .oxygen-builder-body .fancybox-toolbar {
                    visibility: visible;
                    opacity: 1;
                }
                
                .extras-inside-lightbox body {
                    min-height: unset;
                }
                
                .oxygen-builder-body .oxy-lightbox .fancybox-button--thumbs {
                   display: none;
                }
                
                html {
                    --extras-lightbox-bg: rgba(0,0,0,0.5);
                    --extras-lightbox-translatex: 0;
                    --extras-lightbox-translatey: 10px;
                    --extras-lightbox-scale: .95;
                    --extras-lightbox-rotatex: 0;
                    --extras-lightbox-rotatey: 0;
                    --extras-lightbox-rotatez: 0;
                    --extras-lightbox-rotatedeg: 0deg;
                    --extras-lightbox-opacity: 0;
                    --extras-lightbox-horizontal-align: center;
                    --extras-lightbox-vertical-align: middle;
                    --extras-lightbox-width: 600px;
                    --extras-lightbox-aspect-ratio: 56.25%;
                    --extras-lightbox-nav-display: hidden;
                    --extras-lightbox-content-bg: #fff;
                    --extras-lightbox-isize: 14px;
                    --extras-lightbox-icolor: #eee;
                    --extras-lightbox-iradius: 2px;
                    --extras-lightbox-ibg: rgba(20,20,20,0.15);
                    --extras-lightbox-ipadding: 10px;
                    --extras-lightbox-imargin: 0px;
                    --extras-lightbox-csize: 14px;
                    --extras-lightbox-ccolor: #eee;
                    --extras-lightbox-cradius: 0;
                    --extras-lightbox-cbg: rgba(20,20,20,0.15);
                    --extras-lightbox-cpadding: 10px;
                    --extras-lightbox-cmargin: 0;
                    --extras-lightbox-infobar: none;
                    --extras-lightbox-caption: none;
                    --extras-slide-padding-top: 20px;
                    --extras-slide-padding-left: 20px;
                    --extras-slide-padding-right: 20px;
                    --extras-slide-padding-bottom: 20px;
                    --extras-lightbox-padding-left: 30px;
                    --extras-lightbox-padding-right: 30px;
                    --extras-lightbox-padding-top: 30px;
                    --extras-lightbox-padding-bottom: 30px;
                    --extras-lightbox-overflow: auto;
                    --extras-lightbox-originx: 50%;
                    --extras-lightbox-originy: 50%;
                    --extras-lightbox-smallsize: inherit;
                    --extras-lightbox-smallcolor: inherit;
                    --extras-lightbox-smallbg: none;
                    --extras-lightbox-thumbnail-bg: transparent;
                    --extras-lightbox-thumbnail-bordercolor: #111;
                    --extras-lightbox-thumbnail-borderwidth: 3px;
                    --extras-lightbox-thumbnail-height: 100px;
                } 

                .oxy-lightbox .fancybox-thumbs {
                    background-color: var(--extras-lightbox-thumbnail-bg);
                }

                .oxy-lightbox .fancybox-thumbs__list a {
                    background-color: #eee;
                }

                .oxy-lightbox .fancybox-thumbs__list a:before {
                    border-width: var(--extras-lightbox-thumbnail-borderwidth);
                    border-color: var(--extras-lightbox-thumbnail-bordercolor);
                }

                .oxy-lightbox .fancybox-close-small {
                    font-size: var(--extras-lightbox-smallsize);
                    color: var(--extras-lightbox-smallcolor);
                    background-color: var(--extras-lightbox-smallbg);
                    opacity: 1;
                }

                .oxy-lightbox .fancybox-close-small:hover {
                    color: var(--extras-lightbox-smallcolor-hover);
                    background-color: var(--extras-lightbox-smallbg-hover);
                }
                
                .oxy-lightbox .fancybox-navigation .fancybox-button {
                    background-clip: initial;
                    font-size: var(--extras-lightbox-isize);
                    color: var(--extras-lightbox-icolor);
                    width: auto;
                    height: auto;
                    background-color: var(--extras-lightbox-ibg);
                    padding: var(--extras-lightbox-ipadding);
                    margin: 0 var(--extras-lightbox-imargin);
                    border-radius: var(--extras-lightbox-iradius);
                }
                
                .oxy-lightbox .fancybox-navigation .fancybox-button:hover {
                    color: var(--extras-lightbox-icolor-hover);
                    background-color: var(--extras-lightbox-ibg-hover);
                }
                
                .oxy-lightbox .fancybox-infobar {
                    font-size: inherit;
                    display: var(--extras-lightbox-infobar);
                    visibility: visible;
                    opacity: 1;
                } 
                
                .oxy-lightbox .fancybox-caption {
                    display: var(--extras-lightbox-caption);
                    visibility: hidden;
                    opacity: 0;
                }
                
                .oxy-lightbox .fancybox-navigation .fancybox-button[disabled] {
                    visibility: hidden;
                }

                .oxy-lightbox .fancybox-navigation .fancybox-button svg,
                .oxy-lightbox .fancybox-button svg {
                    height: 1em;
                    width: 1em;
                    fill: currentColor;
                    pointer-events: none;
                }
                
                .oxy-lightbox .fancybox-toolbar {
                    margin: var(--extras-lightbox-tmargin);
                    display: flex;
                }
                
                .oxy-lightbox .fancybox-toolbar .fancybox-button {
                    background-clip: initial;
                    font-size: var(--extras-lightbox-csize);
                    color: var(--extras-lightbox-ccolor);
                    width: auto;
                    height: auto;
                    background-color: var(--extras-lightbox-cbg);
                    padding: var(--extras-lightbox-cpadding);
                    margin-left: var(--extras-lightbox-cmargin);
                    border-radius: var(--extras-lightbox-cradius);
                }
                
                .oxy-lightbox .fancybox-toolbar .fancybox-button:hover {
                    background-color: var(--extras-lightbox-cbg-hover);
                    color: var(--extras-lightbox-ccolor-hover);
                }
                
                .oxy-lightbox .fancybox-button--arrow_right svg {
                    transform: rotateY(180deg);
                    -webkit-transform: rotateY(180deg);
                } 

                .oxy-lightbox .fancybox-thumbs-x .fancybox-thumbs__list {
                    margin: auto;
                    display: flex;
                    align-items: center;
                    flex-direction: row;
                }
                
                /* Custom animation */
                .fancybox-fx-extras.fancybox-slide--previous {
                  opacity: var(--extras-lightbox-opacity);
                  transform: translate(var(--extras-lightbox-translatex),var(--extras-lightbox-translatey)) scale(var(--extras-lightbox-scale)) rotate3d(var(--extras-lightbox-rotatex),var(--extras-lightbox-rotatey),var(--extras-lightbox-rotatez),var(--extras-lightbox-rotatedeg));
                }

                .fancybox-fx-extras.fancybox-slide--next {
                  opacity: var(--extras-lightbox-opacity);
                  transform: translate(var(--extras-lightbox-translatex),var(--extras-lightbox-translatey)) scale(var(--extras-lightbox-scale)) rotate3d(var(--extras-lightbox-rotatex),var(--extras-lightbox-rotatey),var(--extras-lightbox-rotatez),var(--extras-lightbox-rotatedeg));
                }

                .fancybox-fx-extras.fancybox-slide--current {
                  transform: none;
                  opacity: 1;
                }
                
                .oxygen-builder-body .fancybox-container {
                    display: var(--extras-lightbox-builder-visibility);
                }
                
                .oxygen-builder-body .fancybox-content {
                    align-items: flex-start;
                }
                
                
                .admin-bar .oxy-lightbox .fancybox-container {
                    top: 32px;
                    height: calc(100% - 32px);
                }
                
                .admin-bar .oxy-lightbox .fancybox-caption {
                    bottom: 32px;
                }
                
                .oxygen-builder-body .oxy-dynamic-list > div:not(.oxy_repeater_original) .oxy-lightbox > *:not(.oxy-lightbox_link):not(.oxy-lightbox_inner) {
                    display: none;
                }

                .oxy-lightbox .fancybox-slide:not(.fancybox-slide--image) {
                    text-align: var(--extras-lightbox-horizontal-align);
                }
                
                .oxy-lightbox .fancybox-slide {
                    -webkit-transition-timing-function: cubic-bezier(0.77, 0, 0.175, 1);
                    -o-transition-timing-function: cubic-bezier(0.77, 0, 0.175, 1);
                    transition-timing-function-timing: cubic-bezier(0.77, 0, 0.175, 1);
                    transform-origin: var(--extras-lightbox-originx) var(--extras-lightbox-originy);
                    -webkit-transform-origin: var(--extras-lightbox-originx) var(--extras-lightbox-originy);
                }
                
                .oxy-lightbox .fancybox-navigation .fancybox-button {
                    visibility: var(--extras-lightbox-nav-display);
                    opacity: 1;
                    top: calc(50% - (var(--extras-lightbox-isize)/2) - var(--extras-lightbox-ipadding));
                }
                
                .oxy-lightbox .fancybox-slide [data-fancybox-close] {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                
                .oxy-lightbox_inner-flex {
                    display: flex;
                }
                
                .fancybox-content_wrapper {
                    width: 100%;
                    padding-top: var(--extras-lightbox-aspect-ratio);
                }
                
                .fancybox-content_wrapper .fancybox-content_wrapper {
                    padding-top: 0%;
                }
                
                .oxy-lightbox .fancybox-content {
                    margin-top: var(--extras-lightbox-margin-top);
                    margin-left: var(--extras-lightbox-margin-left);
                    margin-right: var(--extras-lightbox-margin-right);
                    margin-bottom: var(--extras-lightbox-margin-bottom);
                    padding-top: var(--extras-lightbox-padding-top);
                    padding-left: var(--extras-lightbox-padding-left);
                    padding-right: var(--extras-lightbox-padding-right);
                    padding-bottom: var(--extras-lightbox-padding-bottom);
                    vertical-align: var(--extras-lightbox-vertical-align);
                    overflow: var(--extras-lightbox-overflow);
                }
                
                .oxy-lightbox .fancybox-slide:not(.fancybox-slide--image):not(.fancybox-slide--video) .fancybox-content {
                    width: var(--extras-lightbox-width)!important;
                }
                
                .oxy-lightbox .fancybox-slide:not(.fancybox-slide--image):not(.fancybox-slide--iframe):not(.fancybox-slide--video) .fancybox-content {
                    height: var(--extras-lightbox-height)!important;
                }
                
                .oxy-lightbox .fancybox-slide {
                    padding-top: var(--extras-slide-padding-top);
                    padding-left: var(--extras-slide-padding-left);
                    padding-right: var(--extras-slide-padding-right);
                    padding-bottom: var(--extras-slide-padding-bottom);
                }
                
                .oxy-lightbox .fancybox-slide.fancybox-slide--video .fancybox-content {
                    height: unset!important;
                    width: var(--extras-lightbox-width)!important;
                    padding: 0;
                }
                
                .oxy-lightbox .fancybox-bg {
                    background: var(--extras-lightbox-bg);
                    will-change: opacity;
                }
                
                .oxy-lightbox .fancybox-fake-content {
                    height: 100%;
                    width: 100%;
                    background: rgba(0,0,0,0.04);
                    padding: 40px;
                }
                
                .oxygen-builder-body .oxy-lightbox .fancybox-container {
                    z-index: 2147483630;
                }
                
                .oxy-lightbox .fancybox-content,
                .oxy-lightbox .fancybox-content:not(.ct-section):not(.oxy-easy-posts) {
                    display: inline-flex;
                    flex-direction: column;
                    background: var(--extras-lightbox-content-bg);
                }
                
                .fancybox-stage-in-builder .fancybox-slide {
                    transition: all .5s ease;
                }";
            
            
            $this->css_added = true;
            
        }
        
        $css .= "$selector .fancybox-content,
                 $selector .fancybox-content:not(.ct-section):not(.oxy-easy-posts),
                 $selector .fancybox-content {
                    display: inline-flex;
                    flex-direction: column;
                }"; 
        
        return $css;
        
    } 
    
    
    function output_js() {
         wp_enqueue_script( 'fancybox-js', plugin_dir_url(__FILE__) . 'assets/fancybox.min.js', '', '3.5.7' );

         if ( ! function_exists('do_oxygen_elements') ) {
             wp_enqueue_script( 'fancybox-init', plugin_dir_url(__FILE__) . 'assets/fancybox-init.js', '', '1.0.1' );
         } else {
            wp_enqueue_script( 'fancybox-init', plugin_dir_url(__FILE__) . 'assets/fancybox-init-4.js', '', '1.0.2' );
         }
        
         function localize_vars() {
            return array(
                'oxygen_directory' => content_url() . '/uploads/oxygen/css/' 
            );
        }

        wp_localize_script( 'fancybox-init', 'localize_extras_plugin', localize_vars() );
        
        
    }
    

}

add_filter("oxy_base64_encode_options", 
    function($items) { 
        $items=array_merge($items, array('oxy-lightbox_url','oxy-lightbox_caption_text')); 
        return $items;
    }
);

new ExtraLightbox();