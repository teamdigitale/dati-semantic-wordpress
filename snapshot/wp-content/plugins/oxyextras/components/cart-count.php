<?php

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

class ExtraWooCartCounter extends OxygenExtraElements {
    
    var $js_added = false;
    var $css_added = false;

	function name() {
        return __('Cart Counter'); 
    }
    
    function icon() {
        return plugin_dir_url(__FILE__) . 'assets/icons/'.basename(__FILE__, '.php').'.svg';
    }
    
    function extras_button_place() {
        return "woo"; 
    }
    
    function enablePresets() {
        return true;
    }
    
    function enableFullPresets() {
        return true;
    }
    
    function init() {
        
        add_filter( 'woocommerce_add_to_cart_fragments', array($this,'cart_woo_ajax_callback') );
        
        $this->enableNesting();
        
         // include only for builder
        if (isset( $_GET['oxygen_iframe'] )) {
            add_action( 'wp_footer', array( $this, 'output_js' ) );
        }
    }
    
    
    function render($options, $defaults, $content) {
        
        // Get Options
        $cart_icon = isset( $options['cart_icon'] ) ? esc_attr($options['cart_icon']) : "";
        $maybe_link_to_cart_class = esc_attr($options['cart_icon']) === 'reveal' ? 'oxy-cart-counter_icon_reveal' : ''; 
        $count_visibility = isset( $options['count_visibility'] ) ? esc_attr($options['count_visibility']) : "";
        $cart_link_title = isset( $options['cart_link_title'] ) ? esc_attr($options['cart_link_title']) : "Go to Cart";

        $button_aria_label = isset( $options['button_aria_label'] ) ? esc_attr($options['button_aria_label']) : "Cart";

        $maybe_dropdown_hover = isset( $options['maybe_dropdown_hover'] ) ? esc_attr($options['maybe_dropdown_hover']) : "disable";
        
        
        global $oxygen_svg_icons_to_load;
        $oxygen_svg_icons_to_load[] = $cart_icon;
        
        $output = '';
        
        if ( function_exists("WC") && isset( WC()->cart) ) {
            
            if ( $options['maybe_link_to_cart'] === 'link') {
                $output .= '<a class="oxy-cart-counter_link" href="'. wc_get_cart_url() .'" title="'.$cart_link_title.'"><div';
            } else if ( $options['maybe_link_to_cart'] === 'none' ) {
                $output .= '<div';
            } else {
                $output .= '<button aria-expanded="false" aria-controls=dropdown' . esc_attr($options['selector']) . '" aria-label="' . $button_aria_label . '"';
            }
            
            $output .= ' tabindex=0 class="oxy-cart-counter_icon_count" ';
            $output .= 'data-hover="' . $maybe_dropdown_hover . '" ';
            if ( $options['maybe_link_to_cart'] === 'reveal') {
                $output .= 'data-reveal ';
            }
            $output .= '><span class="oxy-cart-counter_icon"><svg class="oxy-cart-counter_icon_svg" id="cart'. esc_attr($options['selector']) .'-icon"><use xlink:href="#' . $cart_icon . '"></use></svg></span>';
            $output .= '<span class="oxy-cart-counter_count ';
            $output .= 'hidden' === $count_visibility ? 'oxy-cart-counter_hidden ' : '';
            $output .= '">';
                $output .= '<span class="oxy-cart-counter_number">';
                $output .= WC()->cart->get_cart_contents_count() != '0' ? WC()->cart->get_cart_contents_count() : '';
                $output .= '</span>';
            
            $output .= '</span>';
            
            if ( $options['maybe_link_to_cart'] === 'link') {
                $output .= '</div></a>';
            } else if ( $options['maybe_link_to_cart'] === 'none' ) {
                $output .= '</div>';
            } else {
                $output .= '</button>';
            }
            
        }
        
        if ($content && ($options['maybe_link_to_cart'] === 'reveal')) {
            
            $output .= '<div class="oxy-cart-counter_inner_content oxy-inner-content" id="dropdown' . esc_attr($options['selector']) . '">';
            if ( function_exists('do_oxygen_elements') ) {
                $output .=  do_oxygen_elements($content); 
            }
            else {
                $output .=  do_shortcode($content); 
            }
            $output .= '</div>'; 
            
        } 
        
        echo $output;
        
        $this->dequeue_scripts_styles();
        
        // add JavaScript code only once and if shortcode presented
        if ($this->js_added !== true && $options['maybe_link_to_cart'] === 'reveal') {
                add_action( 'wp_footer', array( $this, 'output_js' ) );
            $this->js_added = true;
        }
         
    }

    function class_names() {
        return array();
    }

    function controls() {
        
        /**
         * Cart Function
         */
        $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Function',
                'slug' => 'maybe_link_to_cart'
            )
            
        )->setValue(
            array( 
                "link" => "Link to Cart", 
                "reveal" => "Drop Down",
                "none" => "None"
            )
        )
         ->setDefaultValue('reveal')
        ->setValueCSS( array(
            "link"  => ".oxy-cart-counter_inner_content {
                        display: none;
                    } ",
            "none"  => ".oxy-cart-counter_inner_content {
                        display: none;
                    } ",
        ) );

        /**
         * Cart Function
         */
        $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Open on hover',
                'slug' => 'maybe_dropdown_hover',
                "condition" => 'maybe_link_to_cart=reveal'
            )
            
        )->setValue(
            array( 
                "enable" => "Enable", 
                "disable" => "Disable",
            )
        )
         ->setDefaultValue('disable');
        
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Link Title'),
                "slug" => 'cart_link_title',
                "default" => 'Go to Cart',
                "condition" => 'maybe_link_to_cart=link'
            )
        );

        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Button aria label'),
                "slug" => 'button_aria_label',
                "default" => 'Cart',
                "condition" => 'maybe_link_to_cart=reveal'
            )
        );
        
        
        /**
         * Cart Icon
         */ 
        
        $cart_icon_section = $this->addControlSection("cart_icon_section", __("Cart Icon"), "assets/icon.png", $this);
        
        
        $icon_size = $cart_icon_section->addStyleControl(
                array(
                    "name" => __('Icon Size'),
                    "slug" => "icon_size",
                    "selector" => '.oxy-cart-counter_icon_svg',
                    "control_type" => 'slider-measurebox',
                    "value" => '',
                    "property" => 'font-size',
                )
        );
        $icon_size->setRange(4, 72, 1);
        
        
        $cart_icon_section->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Icon'),
                "slug" => 'cart_icon',
                "default" => 'FontAwesomeicon-shopping-bag'
            )
        );
        
        
        $cart_icon_section->addStyleControls(
             array( 
                 array(
                    "name" => 'Color',
                    "selector" => '.oxy-cart-counter_icon',
                    "property" => 'color',
                ),
                 array(
                    "name" => 'Hover Color',
                    "selector" => '.oxy-cart-counter_icon_count:hover .oxy-cart-counter_icon',
                    "property" => 'color',
                ),
                 array(
                    "name" => 'Focus Color',
                    "selector" => '.oxy-cart-counter_icon_count:focus .oxy-cart-counter_icon',
                    "property" => 'color',
                ),
                 
                 
            )
        );
        
        $cart_icon_section->addStyleControl(
            array(
                "name" => 'Hover Transition',
                "property" => 'transition-duration',
                "control_type" => 'slider-measurebox',
                "default" => '.3',
                "selector" => '.oxy-cart-counter_icon',
            )
        )
        ->setUnits('s','s')
        ->setRange('0','1','.1');
        
        
        /**
         * Cart Count
         */ 
        
        $cart_count_section = $this->addControlSection("cart_count_section", __("Cart Count"), "assets/icon.png", $this);
        $cart_count_section->typographySection('Typography', '.oxy-cart-counter_count',$this);
        
        $cart_count_number_selector = '.oxy-cart-counter_number';
        
        $cart_count_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Count Visible When Zero',
                'slug' => 'count_visibility'
            )
            
        )->setValue(
            array( 
                "hidden" => "Hidden", 
                "visible" => "Visible"
            )
        )
         ->setDefaultValue('visible');


         $cart_count_section->addStyleControls(
            array( 
                array(
                   "selector" => $cart_count_number_selector,
                   "property" => 'font-size',
                    "default" => '14'
               ),
                array(
                   "selector" => $cart_count_number_selector,
                   "property" => 'border-radius',
                    "default" => '100'
               ),
                
           )
       );
        
        
        $cart_count_section->addStyleControl( 
            array(
                "name" => 'Counter Size',
                "default" => "20",
                "property" => 'height|width',
                "control_type" => 'slider-measurebox',
                "selector" => $cart_count_number_selector,
            )
        )
        ->setUnits('px','px')
        ->setRange('10','100','1');

        $cart_count_color_section = $cart_count_section->addControlSection("cart_count_color_section", __("Colors"), "assets/icon.png", $this);

        $cart_count_color_section->addStyleControl(
            array(
                "name" => 'Color',
                "selector" => $cart_count_number_selector,
                "property" => 'color',
                "default" => '#fff'
                )
        )->setParam('hide_wrapper_end', true);

        $cart_count_color_section->addStyleControl(
            array(
                "name" => 'Hover Color',
                "selector" => '.oxy-cart-counter_icon_count:hover .oxy-cart-counter_number',
                "property" => 'color',
                )
        )->setParam('hide_wrapper_start', true);

        $cart_count_color_section->addStyleControl(
            array(
                "name" => 'Focus Color',
                "selector" => '.oxy-cart-counter_icon_count:focus .oxy-cart-counter_number',
                "property" => 'color',
                )
        )->setParam('hide_wrapper_end', true);

        $cart_count_color_section->addStyleControl(
            array(
                "name" => 'Background',
                "selector" => $cart_count_number_selector,
                "property" => 'background-color',
                "default" => '#333'
                )
        )->setParam('hide_wrapper_start', true);

        $cart_count_color_section->addStyleControl(
            array(
                "name" => 'Hover Background',
                "selector" => '.oxy-cart-counter_icon_count:hover .oxy-cart-counter_number',
                "property" => 'background-color',
                )
        )->setParam('hide_wrapper_end', true);

        $cart_count_color_section->addStyleControl(
            array(
                "name" => 'Focus Background',
                "selector" => '.oxy-cart-counter_icon_count:focus .oxy-cart-counter_number',
                "property" => 'background-color',
                )
        )->setParam('hide_wrapper_start', true);
        
        
        
        
            
        $cart_count_section->addStyleControl(
            array(
                "name" => 'Hover Transition',
                "property" => 'transition-duration',
                "control_type" => 'slider-measurebox',
                "default" => '.3',
                "selector" => '.oxy-cart-counter_number',
            )
        )
        ->setUnits('s','s')
        ->setRange('0','1','.1');
        
        
        $cart_count_layout = $cart_count_section->addControlSection("cart_count_layout", __("Layout"), "assets/icon.png", $this);
        $cart_count_layout->flex('.oxy-cart-counter_count', $this);
        
        $cart_count_spacing = $cart_count_section->addControlSection("cart_count_spacing", __("Spacing"), "assets/icon.png", $this);
        
        $cart_count_spacing->addPreset(
            "padding",
            "cart_counter_padding",
            __("Padding"),
            '.oxy-cart-counter_count'
        )->whiteList();
        
        $cart_count_spacing->addPreset(
            "margin",
            "cart_counter_margin",
            __("Margin"),
            '.oxy-cart-counter_count'
        )->whiteList();


        $cart_count_positioning = $cart_count_section->addControlSection("cart_count_positioning", __("Positioning"), "assets/icon.png", $this);

        $cart_count_positioning->addStyleControl(
            array(
                "name" => __('Position'),
                "property" => 'position',
                "control_type" => 'buttons-list',
                "selector" => '.oxy-cart-counter_count',
            )
        )->setValue(array( 
            "static" => "Static",
            "absolute" => "Absolute",
            )
        )->setDefaultValue('relative');

        $cart_count_left = $cart_count_positioning->addStyleControl(
                array(
                    "selector" => '.oxy-cart-counter_count',
                    "property" => 'left',
                    "control_type" => 'measurebox', 
                )
        );
        $cart_count_left->setParam('hide_wrapper_end', true);
        $cart_count_left->setUnits('px');    
        
        $cart_count_right = $cart_count_positioning->addStyleControl(
                array(
                    "selector" => '.oxy-cart-counter_count',
                    "property" => 'right',
                    "control_type" => 'measurebox',
                    "default" => '-10'
                )
        );
        $cart_count_right->setParam('hide_wrapper_start', true);
        $cart_count_right->setUnits('px'); 
        
        $cart_count_top = $cart_count_positioning->addStyleControl(
                array(
                    "selector" => '.oxy-cart-counter_count',
                    "property" => 'top',
                    "type" => 'measurebox', 
                )
        );
        $cart_count_top->setParam('hide_wrapper_end', true);
        $cart_count_top->setUnits('px'); 
            
        $cart_count_bottom = $cart_count_positioning->addStyleControl(
                array(
                    "selector" => '.oxy-cart-counter_count',
                    "property" => 'bottom',
                    "type" => 'measurebox', 
                )
        );
        $cart_count_bottom->setParam('hide_wrapper_start', true);
        $cart_count_bottom->setUnits('px'); 
        
        
        $cart_count_section->borderSection('Borders', $cart_count_number_selector,$this);
        $cart_count_section->boxShadowSection('Shadows', $cart_count_number_selector,$this);
        
        
        /**
         * DropDown Content
         */ 
        
        $cart_inner_section = $this->addControlSection("cart_inner_section", __("Dropdown Content"), "assets/icon.png", $this);
        $cart_inner_selector = '.oxy-cart-counter_inner_content';
        
        $cart_inner_section->addCustomControl(
            '<div style="color: #eee; line-height: 1.3; font-size: 13px;">Nest elements inside of the cart counter to have them reveal as a dropdown when opened</div>','description');
            
        
        $cart_inner_section->addStyleControl(
            array(
                "name" => 'Fade Duration',
                "property" => 'transition-duration',
                "control_type" => 'slider-measurebox',
                "default" => '.3',
                "selector" => $cart_inner_selector,
            )
        )
        ->setUnits('s','s')
        ->setRange('0','1','.1');
        
        
        $cart_inner_colors = $cart_inner_section->addControlSection("cart_inner_colors", __("Colors"), "assets/icon.png", $this);
        
        $cart_inner_colors->addStyleControls(
             array( 
                 array(
                    "name" => 'Text Color',
                    "selector" => $cart_inner_selector,
                    "property" => 'color',
                     
                ),
                 array(
                    "name" => 'Background Color',
                    "selector" => $cart_inner_selector,
                    "property" => 'background-color',
                     "default" => '#fff',
                ),
                 
                 
            )
        );
        
        $cart_inner_spacing = $cart_inner_section->addControlSection("cart_inner_spacing", __("Spacing"), "assets/icon.png", $this);
        
        $cart_inner_spacing->addPreset(
            "padding",
            "cart_inner_padding",
            __("Padding"),
            $cart_inner_selector
        )->whiteList();
        
         $cart_inner_spacing->addStyleControls(
             array( 
                 array(
                    "selector" => $cart_inner_selector,
                    "property" => 'padding-top',
                     "default" => '20',
                ),
                 array(
                    "selector" => $cart_inner_selector,
                    "property" => 'padding-bottom',
                     "default" => '20',
                ),
                 array(
                    "selector" => $cart_inner_selector,
                    "property" => 'padding-left',
                     "default" => '20',
                ),
                 array(
                    "selector" => $cart_inner_selector,
                    "property" => 'padding-right',
                     "default" => '20',
                ),
                 
            )
        );
        
        $cart_inner_position = $cart_inner_section->addControlSection("cart_inner_position", __("Position"), "assets/icon.png", $this);
        
        
        $cart_inner_position->addStyleControls(
             array( 
                 array(
                    "selector" => $cart_inner_selector,
                    "property" => 'top',
                ),
                 array(
                    "selector" => $cart_inner_selector,
                    "property" => 'left',
                     
                ),
                 array(
                    "selector" => $cart_inner_selector,
                    "property" => 'right',
                     "default" => '0',
                     
                ),
                 array(
                    "selector" => $cart_inner_selector,
                    "property" => 'bottom',
                     
                ),
                 
                 
            )
        );
        
        $cart_inner_section->addStyleControl( 
            array(
                "type" => 'measurebox',
                "default" => "300",
                "units" => 'px',
                "property" => 'width',
                "control_type" => 'slider-measurebox',
                "selector" => $cart_inner_selector,
            )
        )
        ->setRange('0','1000','1');
        
        $cart_inner_section->addStyleControl( 
            array(
                "type" => 'measurebox',
                "default" => "90",
                "property" => 'max-width',
                "control_type" => 'slider-measurebox',
                "selector" => $cart_inner_selector,
            )
        )
        ->setRange('0','100','1')
        ->setUnits('vw','vw');
        
        
        $cart_inner_section->borderSection('Borders', $cart_inner_selector,$this);
        $cart_inner_section->boxShadowSection('Shadows', $cart_inner_selector,$this);    
        
        
    }
    
    
    /**
     * Show cart count / Ajax
     */
    function cart_woo_ajax_callback( $fragments ) {
        
        ob_start();

        ?><span class="oxy-cart-counter_number"><?php 
        
        echo WC()->cart->get_cart_contents_count() != '0' ? WC()->cart->get_cart_contents_count() : '';
        
        
         ?></span> <?php

        $fragments['span.oxy-cart-counter_number'] = ob_get_clean();
        return $fragments;
        
    }
    
    
    function customCSS($options, $selector) {
        
        $css = "";
        
        if( ! $this->css_added ) {
        
            $css = ".oxy-cart-counter {
                        position: relative;
                    }

                    .oxy-cart-counter_link {
                        color: inherit;
                    }

                    .oxygen-builder-body .oxy-cart-counter > div:not(.oxy-cart-counter_inner_content):not(.oxy-cart-counter_icon_count) {
                        display: none;
                    }

                    .oxy-cart-counter_inner_content {
                        background-color: #fff;
                        position: absolute;
                        visibility: hidden;
                        opacity: 0;
                        transition-property: opacity, visibility;
                        transition-timing-function: ease;
                        transition-duration: 0.3s;
                        z-index: 99;
                        width: 300px;
                        max-width: 90vw;
                        right: 0;
                        padding: 20px;
                    }

                    .oxy-cart-counter_visible + .oxy-cart-counter_inner_content {
                        visibility: visible;
                        opacity: 1;
                    }

                    .oxy-cart-counter_icon_count {
                        background: none;
                        border: none;
                        position: relative;
                    }

                    .oxy-cart-counter_icon_count[data-reveal] {
                        cursor: pointer;
                    }

                    .oxy-cart-counter_icon_count:focus,
                    .oxy-cart-counter_link:focus {
                        outline: none;
                    }

                    .oxy-cart-counter_icon_svg {
                        height: 1em;
                        width: 1em;
                        fill: currentColor;
                    }

                    .oxy-cart-counter_icon_count,
                    .oxy-cart-counter_count {
                        display: flex;
                        align-items: center;
                    }

                    .oxy-cart-counter_count {
                        right: -10px;
                    }


                    .oxy-cart-counter_number {
                        display: flex;
                        align-items: center;
                        justify-content: center; 
                        border-radius: 100px;
                        background-color: #333;
                        color: #fff;
                        font-size: 14px;
                        height: 20px;
                        width: 20px;
                        transition-property: color, background-color;
                        transition-timing-function: ease;
                        transition-duration: 0.3s;
                    }

                    .oxy-cart-counter_icon {
                        transition-property: color, background-color;
                        transition-timing-function: ease;
                        transition-duration: 0.3s;
                    }
                    
                    .oxy-cart-counter_number:empty::before {
                        content: '0';
                    }
                    
                    .oxy-cart-counter_hidden .oxy-cart-counter_number:empty {
                        display: none;
                    }
                    
                    .oxygen-builder-body .oxy-cart-counter_hidden .oxy-cart-counter_number:empty {
                        display: flex;
                    }";
            
            $this->css_added = true;
            
        }
        
        return $css;
    }
    
    
    function output_js() { ?>
            
            <script type="text/javascript">
            jQuery(document).ready(extras_init_cart_counter);
            function extras_init_cart_counter($) {
                                          
                $(document).on( 'touchstart click', 'body:not(.oxygen-builder-body)', function(e) {
                    
                    let cart_count = $('.oxy-cart-counter'),
                        cart_counter = $('.oxy-cart-counter_icon_count');

                    // Always remove visibility if click outside the counter
                    if (!cart_counter.is(e.target) && cart_count.has(e.target).length === 0 ) {
                        cart_counter.removeClass('oxy-cart-counter_visible');
                    }

                }); 

                
                function ariaExpandToggle(el) {

                    if ( 'false' === $(el).attr("aria-expanded" ) ) {
                        $(el).attr("aria-expanded", "true" );
                    } else {
                        $(el).attr("aria-expanded", "false" );
                    }

                };
                
                $('body').on( 'touchstart click', '.oxy-cart-counter_icon_count', function(e) {
                    
                    e.stopPropagation();
                    
                        // workaround to stop click event from triggering to prevet double toggle
                        if (window.extrasIconTouched === true) {
                            window.extrasIconTouched = false;
                            return;
                        }
                        if (e.type==='touchstart') {
                            window.extrasIconTouched = true;
                        }
                    
                    // Toggle visibility when counter clickeds
                    e.stopPropagation();
                    $(this).toggleClass('oxy-cart-counter_visible');
                    ariaExpandToggle(this);
                    
                });  

                $('.oxy-cart-counter').each(function( i, OxyCartCounter ) {    
                    let clickable = $(OxyCartCounter).find('.oxy-cart-counter_icon_count[data-hover="enable"]');

                    $(OxyCartCounter).on('mouseover', function() {
                        $(clickable).addClass('oxy-cart-counter_visible');
                        ariaExpandToggle(clickable[0]);
                    });
                    $(OxyCartCounter).on('mouseout', function() {
                        $(clickable).removeClass('oxy-cart-counter_visible');
                        ariaExpandToggle(clickable[0]);
                    });
                    
                }); 
                
            }
        </script>
    <?php }
    

}

new ExtraWooCartCounter();