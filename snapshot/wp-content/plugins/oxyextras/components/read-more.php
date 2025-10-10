<?php

class ExtraReadMore extends OxygenExtraElements {
    
    var $js_added = false;
    var $css_added = false;

	function name() {
        return __('Read More / Less'); 
    }

    function slug() {
        return "read-more-less";
    }
    
    function icon() {
        return plugin_dir_url(__FILE__) . 'assets/icons/'.basename(__FILE__, '.php').'.svg';
    }
    
    function extras_button_place() {
        return "interactive"; 
    }
    
    function init() {
        
        $this->enableNesting();
        
        add_filter("oxy_allowed_empty_options_list", array( $this, "allowedEmptyOptions") );
        
    }
    
    
    function render($options, $defaults, $content) {
        
        $open_text  = isset( $options['open_text'] ) ? esc_attr($options['open_text']) : 'Read More';
        $close_text = isset( $options['close_text'] ) ? esc_attr($options['close_text']) : 'Close';
        $speed = isset( $options['speed'] ) ? esc_attr($options['speed']) : '700';
        $height_margin = isset( $options['height_margin'] ) ? esc_attr($options['height_margin']) : '16';
        
        $open_icon = isset( $options['open_icon'] ) ? esc_attr($options['open_icon']) : "";
        
        $maybe_open_icon = isset( $options['maybe_open_icon'] ) ? esc_attr($options['maybe_open_icon']) : "";
        
        $icon_output = ('enable' === $maybe_open_icon) ? '<span class="oxy-read-more-link_icon"><svg class="oxy-read-more-link_icon-svg"><use xlink:href="#' . $open_icon . '"></use></svg></span>' : '';
        
        global $oxygen_svg_icons_to_load;
        $oxygen_svg_icons_to_load[] = $open_icon;
        
        $inbuilder_class = (defined('OXY_ELEMENTS_API_AJAX') && OXY_ELEMENTS_API_AJAX) ? ' extras_inbuilder': '';
        
        $output = '';
        
        $output .= '<div id="'. esc_attr($options['selector']) .'-inner" class="oxy-read-more-inner oxy-inner-content'. $inbuilder_class .'" data-margin="' . $height_margin . '" data-speed="' . $speed . '" data-open="' . $open_text . '" data-close="' . $close_text . '"  data-iconopen="' . $open_icon . '" data-icon="' . $maybe_open_icon . '">';
        
        if ($content) {
            
            if ( function_exists('do_oxygen_elements') ) {
                $output .=  do_oxygen_elements($content); 
            }
            else {
                $output .=  do_shortcode($content); 
            } 
            
        } 
        
        $output .= '</div>';
        
        //For Styling in builder only, is removed on frontend
        $output .= '<a class="oxy-read-more-link"><span class="oxy-read-more-link_text">'.$open_text.'</span>'.$icon_output.'</a>';
        
        echo $output;

        $this->dequeue_scripts_styles();
        
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

        $this->addCustomControl(
            '<div style="color: #fff; line-height: 1.3; font-size: 13px;">Place the elements inside that you wish to hide by collapsing</div>','description');
        
        
        
        $this->addStyleControl( 
            array(
                 "name" => __('Collapsed Height'),
                "type" => 'measurebox',
                "selector" => '.oxy-read-more-inner',
                "property" => 'max-height',
                "default" => '200',
                "control_type" => 'slider-measurebox',
            )
        )
        ->setRange('0','1000','1')->setUnits('px')->whiteList();
        
        
        
        
        
        $this->addOptionControl(
            array(
                
                "name" => __('Animation Speed'),
                "slug" => 'speed',
                "default" => '700',
                "type" => 'slider-measurebox',
            )
        )->setRange('0','2000','1')->setUnits('ms','ms');
        
        
        $this->addOptionControl(
            array(
                
                "name" => __('Height Margin'),
                "slug" => 'height_margin',
                "default" => '16',
                "type" => 'measurebox',
            )
        )->setUnits('px','px');
        
        
        $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Visibility in Builder'),
                'slug' => 'builder_viibility'
            )
            
        )->setDefaultValue('collapse')
        ->setValue(array( 
             "collapse" => "Collapse", 
             "expand" => "Expand"
            )
        )->setValueCSS( array(
            "expand"  => ".oxy-read-more-inner.extras_inbuilder {
                            max-height: none;
                        }
                        
                        .oxy-read-more-inner.extras_inbuilder::after {
                          visibility: hidden;
                          opacity: 0;
                        }",
                
        ) );
        
        
        
        
        
        
        
        
        $icon_section = $this->addControlSection("icon_section", __("Read more icon"), "assets/icon.png", $this);
        
        $icon_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Read more icon'),
                'slug' => 'maybe_open_icon'
            )
            
        )->setDefaultValue('disable')
        ->setValue(array( 
             "disable" => "Disable", 
             "enable" => "Enable"
            )
        )->setValueCSS( array(
            "disable"  => ".oxy-read-more-link_icon {
                                display: none;
                            }",
            "enable"  => ".oxy-read-more-link_icon {
                                display: flex;
                            }",
                
        ) );
        
        $icon_section->addStyleControls(
            array(
                array(
                    "name" => __('Icon Size'),
                    "selector" => '.oxy-read-more-link_icon svg',
                    "property" => 'font-size',
                    "condition" => 'maybe_open_icon=enable'
                ),
            )
        );
        
        $icon_section->addStyleControl(
            array(
                "name" => __('Icon rotation on expanding'),
                "selector" => '.oxy-read-more-less_expanded + .oxy-read-more-link .oxy-read-more-link_icon svg',
                "property" => 'transform:rotate',
                "default" => '180',
                "control_type" => 'slider-measurebox',
                "condition" => 'maybe_open_icon=enable'
            )
        )
        ->setUnits('deg','deg')
        ->setRange('-180','180');
        
        $icon_section->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Open icon'),
                "slug" => 'open_icon',
                "default" => 'FontAwesomeicon-angle-down',
                "condition" => 'maybe_open_icon=enable'
            )
        );
        
        
        $icon_spacing_section = $icon_section->addControlSection("icon_spacing_section", __("Icon spacing"), "assets/icon.png", $this);
        $icon_selector = '.oxy-read-more-link_icon';
        
        
        $icon_spacing_section->addPreset(
            "margin",
            "icon_margin",
            __("Margin"),
            $icon_selector
        )->whiteList();
        
        $icon_spacing_section->addPreset(
            "padding",
            "icon_padding",
            __("padding"),
            $icon_selector
        )->whiteList();
        
        
        $icon_section->borderSection('Icon borders', $icon_selector,$this);
        $icon_section->boxShadowSection('Icon shadows', $icon_selector,$this);
        
        
        
        $readmore_text_section = $this->addControlSection("readmore_text_section", __("Read more text"), "assets/icon.png", $this);
        
        /**
         * Link
         */
        
        $link_selector = '.oxy-read-more-link';
        
        $this->addStyleControl(
            array(
                "name" => __('Read more link fade duration'),
                "property" => 'animation-duration',
                "control_type" => 'slider-measurebox',
                "default" => '0',
                "selector" => $link_selector,
            )
        )
        ->setRange('0','1000','1')->setUnits('ms','ms');
        
        
        $link_text_section = $readmore_text_section->typographySection('Typography', $link_selector,$this);
        
        
        
        $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Read more link align'),
                'slug' => 'link_text_align'
            )
            
        )->setDefaultValue('disable')
        ->setValue(array( 
             "auto" => "auto", 
             "center" => "center",
             "flexend" => "flexend",
             "flexstart" => "flexstart"
            )
        )->setDefaultValue('auto')
         ->setValueCSS( array(
            "auto"  => ".oxy-read-more-link {
                                align-self: auto;
                            }",
            "center"  => ".oxy-read-more-link {
                                align-self: center;
                            }",
            "flexend"  => ".oxy-read-more-link {
                                align-self: flex-end;
                            }",
            "flexstart"  => ".oxy-read-more-link {
                                align-self: flex-start;
                            }",
                
        ) );    
    
        
        $readmore_text_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Read More Text (Leave blank to remove)'),
                "slug" => 'open_text',
                "default" => 'Read More',
            )
        );
            
        $readmore_text_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Close Text (Leave blank to remove)'),
                "slug" => 'close_text',
                "default" => 'Close',
            )
        );
        
        
        
        
        $link_color_section = $this->addControlSection("link_color_section", __("Colors"), "assets/icon.png", $this);
        
        $link_color_section->addStyleControl(
            array(
                "name"     => 'Link Color',
                "property" => 'color',
                "selector" => $link_selector
            )
        );
        
        $link_color_section->addStyleControl(
            array(
                "name" => 'Hover Link Color',
                "property" => 'color',
                "selector" => $link_selector .":hover",
            )
        );
        
        $link_color_section->addStyleControl(
            array(
                "name"     => 'Link Background',
                "property" => 'background-color',
                "selector" => $link_selector
            )
        );
        
        $link_color_section->addStyleControl(
            array(
                "name"     => 'Hover Link Background',
                "property" => 'background-color',
                "selector" => $link_selector .":hover",
            )
        );
        
        
        
        $link_spacing_section = $this->addControlSection("link_spacing_section", __("Spacing"), "assets/icon.png", $this);
        
        $link_spacing_section->addPreset(
            "padding",
            "link_padding",
            __("Outer Padding"),
            $link_selector
        )->whiteList();
        
        $link_spacing_section->addPreset(
            "margin",
            "link_margin",
            __("Outer Margin"),
            $link_selector
        )->whiteList();
        
        
        $fade_section = $this->addControlSection("fade_section", __("Fade gradient"), "assets/icon.png", $this);
        
        
        $fade_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Gradient'),
                'slug' => 'maybe_fade'
            )
            
        )->setDefaultValue('false')
        ->setValue(array( 
             "true" => "Enable", 
            "false" => "Disable"
            )
        )->setValueCSS( array(
            "true"  => ".oxy-read-more-inner {
                            position: relative;
                        }
                        
                        .oxy-read-more-inner::after {
                          background: linear-gradient(to top, var(--fade-color) var(--fadecolor-percent), var(--transparent-color) var(--fade-percent));
                          content: '';
                          visibility: visible;
                          opacity: 1;
                        }

                        .oxy-read-more-less_expanded::after {
                          visibility: hidden;
                          opacity: 0;
                        }
                        
                        .oxy-read-more-less_not-collapsable::after {
                          content: none;
                          visibility: hidden;
                          opacity: 0;
                        }",
            
            "false"  => ".oxy-read-more-inner::after {
                          content: none;
                        }",
                
        ) );
        
        $fade_section->addStyleControl( 
           array(
                "name" => __('Color (match the background color)'),
                "property" => '--fade-color',
                "selector" => '.oxy-read-more-inner',
                "default" => 'rgba(255,255,255,0)',
                "control_type" => 'colorpicker',
                "condition" => 'maybe_fade=true'
           )
        );

        $fade_section->addStyleControl( 
            array(
                 "name" => __('Transparent color '),
                 "property" => '--transparent-color',
                 "description" => 'Transparent version of color (if white, rgba(255,255,255,0)',
                 "selector" => '.oxy-read-more-inner',
                 "control_type" => 'colorpicker',
                 "condition" => 'maybe_fade=true'
            )
         );
        
        $fade_section->addStyleControl(
            array(
                "name" => 'Color percent',
                "property" => '--fadecolor-percent',
                "control_type" => 'slider-measurebox',
                "default" => '0',
                "selector" => '.oxy-read-more-inner',
                 "condition" => 'maybe_fade=true'
            )
        )
        ->setUnits('%','%')
        ->setRange('0','100','1');
        
        $fade_section->addStyleControl(
            array(
                "name" => 'Transparent percent',
                "property" => '--fade-percent',
                "control_type" => 'slider-measurebox',
                "default" => '100',
                "selector" => '.oxy-read-more-inner',
                 "condition" => 'maybe_fade=true'
            )
        )
        ->setUnits('%','%')
        ->setRange('0','100','1');
        
        
        $fade_section->addStyleControl(
            array(
                "name" => 'Gradient fade transition',
                "property" => 'transition-duration',
                "control_type" => 'slider-measurebox',
                "default" => '700',
                "selector" => '.oxy-read-more-inner::after',
                 "condition" => 'maybe_fade=true'
            )
        )
        
        ->setUnits('ms','ms')
        ->setRange('0','1000','1');
        
        
        
        
    }
    
    
    function allowedEmptyOptions($options) {

        $options_to_add = array(
            "oxy-read-more-less_open_text",
            "oxy-read-more-less_close_text"
        );

        $options = array_merge($options, $options_to_add);

        return $options;
    }
    
    
    function customCSS($options, $selector) {
        
        $css = ''; 
        
        if( ! $this->css_added ) {
        
            $css .= ".oxy-read-more-less {
                        display: flex;
                        flex-direction: column; 
                        width: 100%;
                    }

                    .oxy-read-more-inner {
                       display: block;
                       max-height: 200px;
                       overflow: hidden;
                       width: 100%;
                       --fade-color: #fff;
                       --transparent-color: rgba(255,255,255,0);
                       --fade-percent: 100%;
                       --fadecolor-percent: 0%;
                    }
                    
                    .oxy-read-more-inner::after {
                          pointer-events: none;
                          position: absolute;
                          top: 0;
                          bottom: 0;
                          left: 0;
                          right: 0;
                          visibility: hidden;
                          opacity: 0;
                          transition-property: all;
                          transition-timing-function: ease;
                          transition-duration: 700ms;
                    }

                    .oxy-read-more-inner:empty {
                        min-height: 80px;
                    }

                    .oxy-read-more-link {
                        cursor: pointer;
                        position: relative;
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                    }
                    
                    .oxy-read-more-link_icon svg {
                        height: 1em;
                        width: 1em;
                        fill: currentColor;
                    }
                    
                    .oxy-read-more-less_expanded + .oxy-read-more-link .oxy-read-more-link_icon svg {
                        transform: rotate(180deg);
                    }
                    
                    .oxy-read-more-link {
                        -webkit-animation: oxy-read-more-link_fade 0ms;
                        animation: oxy-read-more-link_fade 0ms;
                        visibility: hidden;
                    }

                    [data-readmore] + .oxy-read-more-link {
                        visibility: visible;
                    }

                    .oxygen-builder-body .oxy-read-more-link {
                        visibility: visible;
                    }
                    
                    @keyframes oxy-read-more-link_fade {
                        from {
                            opacity: 0;
                        }
                        to {
                            opacity: 1;
                        }
                        }
                    @-webkit-keyframes oxy-read-more-link_fade {
                        from {
                            opacity: 0;
                        }
                        to {
                            opacity: 1;
                        }
                    }
                    ";
            
            $this->css_added = true;
            
        }
        
        return $css;
    }
    
    function output_js() { 
       wp_enqueue_script( 'readmore-js', plugin_dir_url( __FILE__ ) . 'assets/readmore.min.js', '', '3.0.1' );
    }
    
    function output_init_js() { 
        
        if ( !function_exists('do_oxygen_elements') ) {
            wp_enqueue_script( 'readmore-init', plugin_dir_url(__FILE__) . 'assets/readmore-init.js', '', '1.0.1' );
        } else {

        ?>
            
            <script type="text/javascript">
            jQuery(document).ready(oxygen_init_readmore);
            function oxygen_init_readmore($) {
                
               let extrasReadmore = function ( container ) {
                    
                    $(container).find('.oxy-read-more-inner').each(function(i, oxyReadMore){

                        let readMore = $(oxyReadMore),
                            readMoreID = readMore.attr('ID'),
                            openText = readMore.data( 'open' ),
                            closeText = readMore.data( 'close' ),
                            speed = readMore.data( 'speed' ),
                            heightMargin = readMore.data( 'margin' ), 
                            icon = ('enable' === readMore.data( 'icon' )) ? '<span class="oxy-read-more-link_icon"><svg class="oxy-read-more-link_icon-svg"><use xlink:href="#' + readMore.data( 'iconopen' ) + '"></use></svg></span>' : '',

                            moreText = '<a href=# class=oxy-read-more-link><span class="oxy-read-more-link_text">' + openText + '</span>' + icon +'</a>',
                            lessText = '<a href=# class=oxy-read-more-link><span class="oxy-read-more-link_text">' + closeText + '</span>' + icon +'</a>';

                            readMore.attr('id', readMoreID + '_' + i);
                        
                            if ($(oxyReadMore).closest('.oxy-dynamic-list').length) {
                                readMore.attr('id', readMoreID + '_' + $(oxyReadMore).closest('.oxy-dynamic-list > .ct-div-block').index() + 1);
                            }
                    
                        function doReadMore() {

                            new Readmore(readMore, {
                                  speed: speed,
                                  moreLink: moreText,
                                  lessLink: lessText,
                                  embedCSS: false,
                                  collapsedHeight: parseInt(readMore.css('max-height')),
                                  heightMargin: heightMargin,
                                  beforeToggle: function(trigger, element, expanded) {
                                    if(!expanded) { // The "Close" link was clicked
                                      $(element).addClass('oxy-read-more-less_expanded');
                                      readMore.parent('.oxy-read-more-less').trigger('extras_readmore:expand');
                                    } else {  
                                      $(element).removeClass('oxy-read-more-less_expanded');
                                      readMore.parent('.oxy-read-more-less').trigger('extras_readmore:collapse');
                                    }
                                  },
                                  afterToggle: function(trigger, element, expanded) {
                                    if(expanded) {
                                        readMore.parent('.oxy-read-more-less').trigger('extras_readmore:expanded');
                                    } else {
                                        readMore.parent('.oxy-read-more-less').trigger('extras_readmore:collapsed');
                                    }
                                  },
                                  blockProcessed: function(element, collapsable) {
                                    if(! collapsable) {
                                     readMore.addClass('oxy-read-more-less_not-collapsable');
                                     readMore.parent('.oxy-read-more-less').find('.oxy-read-more-link').remove();
                                    }
                                  }
                                });

                                $('.oxy-read-more-link + .oxy-read-more-link').remove();

                            }

                        doReadMore();

                        if (readMore.closest('.oxy-tabs-contents').length) {
                            $('.oxy-tab').on('click', function() {
                                readMore.css('max-height', '')
                                setTimeout(function() {
                                    doReadMore();
                                    readMore.siblings('.oxy-read-more-link + .oxy-read-more-link').remove();
                                    window.dispatchEvent(new Event('resize'));
                                }, 10);
                            });
                        }

                        if (readMore.closest('.oxy-pro-accordion').length) {
                            readMore.closest('.oxy-pro-accordion').on('extras_pro_accordion:toggle', function() {
                                doReadMore();
                                readMore.siblings('.oxy-read-more-link + .oxy-read-more-link').remove();
                            });
                        }

                    }); 
                    
                    window.dispatchEvent(new Event('resize'));

                    /* Force resize again after everything loaded (for Safari fix) */
                    jQuery(window).on('load', function(){
                        setTimeout(function(){
                            window.dispatchEvent(new Event('resize'));
                        }, 1000);
                    });
                    
                }
                
                extrasReadmore('body');
                
                // Expose function
                window.doExtrasReadmore = extrasReadmore;
                
                
            }</script>
        
    <?php }

    }

}

new ExtraReadMore();