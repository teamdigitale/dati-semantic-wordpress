<?php

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

class ExtraWooMiniCart extends OxygenExtraElements {

    var $css_added = false;
    
    function name() {
        return __('Mini Cart'); 
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
    
    
    function render($options, $defaults, $content) {
        
        global $pagenow;
		if ( ( $pagenow != 'post.php' ) ) {
            ?> 
            
            <div class="widget_shopping_cart_content">
            <?php woocommerce_mini_cart(); ?>
            </div>
            
            <?php
		}
        
         
    }

    function class_names() {
        return array('woocommerce');
    }

    function controls() {
        
        
        /**
         * Cart Items
         */
        $cart_item_section = $this->addControlSection("cart_item_section", __("Cart Items"), "assets/icon.png", $this);
        $cart_item_selector = '.woocommerce-mini-cart-item';
        $cart_item_title_selector = '.woocommerce-mini-cart-item a + a';
        
        
        $cart_item_spacing_section = $cart_item_section->addControlSection("cart_item_spacing_section", __("Item Spacing"), "assets/icon.png", $this);
        
        $cart_item_spacing_section->addPreset(
            "padding",
            "cart_item_padding",
            __("Padding"),
            '.woocommerce-mini-cart-item'
        )->whiteList();
        
        $cart_item_spacing_section->addPreset(
            "margin",
            "cart_item_margin",
            __("Margin"),
            '.woocommerce-mini-cart-item'
        )->whiteList();
        
       
        
        $cart_item_section->addStyleControl( 
            array(
                "name" => 'Max-Height of Item List (Scrollable)',
                "property" => 'max-height',
                "control_type" => 'slider-measurebox',
                "selector" => '.cart_list',
            )
        )
        ->setUnits('px','px')
        ->setRange('0','1000','1');
        
        $cart_item_section->addStyleControls(
             array( 
                 
               array(
                    "selector" => $cart_item_selector,
                    "property" => 'background-color',
                ),
                  
            )
        );
        
        
        
        
        $cart_typography = $cart_item_section->addControlSection("cart_typography", __("Typography"), "assets/icon.png", $this);
        
        $cart_typography->addStyleControls(
             array( 
                array(
                    "name" => 'Product Title Size',
                    "selector" => '.woocommerce-mini-cart-item a + a',
                    "property" => 'font-size',
                ),
                array(
                    "name" => 'Product Title Weight',
                    "selector" => '.woocommerce-mini-cart-item a + a',
                    "property" => 'font-weight',
                ),
                 array(
                    "name" => 'Quantity Size',
                    "selector" => '.woocommerce-mini-cart-item .quantity',
                    "property" => 'font-size',
                     "default" => '14',
                ),
                 array(
                    "name" => 'Price',
                    "selector" => '.woocommerce-mini-cart-item .woocommerce-Price-amount',
                    "property" => 'font-size',
                ),
                 array(
                    "name" => 'Link Color',
                    "selector" => 'ul.product_list_widget li > a:not(.remove_from_cart_button)',
                    "property" => 'color',
                     "default" => 'inherit',
                ),
                 array(
                    "name" => 'Link Hover Color',
                    "selector" => 'ul.product_list_widget li > a:not(.remove_from_cart_button):hover',
                    "property" => 'color',
                ),
                 array(
                    "name" => 'Link Hover Text Decoration',
                    "selector" => 'ul.product_list_widget li > a:not(.remove_from_cart_button):hover',
                    "property" => 'text-decoration',
                     "default" => 'underline',
                ),
            )
        );
        
        
        $cart_item_section->borderSection('Borders', $cart_item_selector,$this);
        $cart_item_section->boxShadowSection('Box Shadow', $cart_item_selector,$this);
        
      
       
       
        /**
         * Product Image
         */
        
        $product_image = $this->addControlSection("product_image", __("Product Image"), "assets/icon.png", $this);
        $product_image_selector = 'ul.product_list_widget li img';
        
        $product_image->addPreset(
            "margin",
            "product_image_margin",
            __("Margin"),
             $product_image_selector
        )->whiteList();
        
        $product_image->addStyleControl( 
            array(
                
                "default" => "60",
                "units" => 'px',
                "property" => 'width',
                "control_type" => 'slider-measurebox',
                "selector" => $product_image_selector,
            )
        )
        ->setRange('0','500','1');
        
        
        
        
        $product_image->borderSection('Borders', $product_image_selector,$this);
        $product_image->boxShadowSection('Box Shadow', $product_image_selector,$this);
        
        
        /**
         * Remove Item button
         */
        $cart_item_remove_section = $this->addControlSection("cart_item_remove_section", __("Remove Icon"), "assets/icon.png", $this);
        $cart_item_remove_selector = '.woocommerce-mini-cart-item a.remove_from_cart_button';
        
        $cart_item_remove_section->addStyleControls(
             array( 
                 array(
                    "name" => 'Icon Size',
                    "selector" => $cart_item_remove_selector,
                    "property" => 'font-size',
                     "default" => '20',
                     
                ),
                 array(
                    "name" => 'Background Color',
                    "selector" => $cart_item_remove_selector,
                    "property" => 'background-color',
                    "default" => '#f5f5f5',
                ),
                 array(
                    "name" => 'Hover Background Color',
                    "selector" => $cart_item_remove_selector.":hover",
                    "property" => 'background-color',
                    "default" => '#e5e5e5',
                ),
            )
        );
      
        
        $cart_item_remove_section->borderSection('Borders', $cart_item_remove_selector,$this);
        $cart_item_remove_section->boxShadowSection('Shadows', $cart_item_remove_selector,$this);
        
        $cart_item_remove_spacing = $cart_item_remove_section->addControlSection("cart_item_remove_spacing", __("Spacing"), "assets/icon.png", $this);
        
        $cart_item_remove_spacing->addPreset(
            "margin",
            "cart_item_remove_margin",
            __("Margin"),
             $cart_item_remove_selector
        )->whiteList();
        
        $cart_item_remove_spacing->addStyleControls(
             array( 
                 array(
                    "selector" => $cart_item_remove_selector,
                    "property" => 'top',  
                ),
                  array(
                    "selector" => $cart_item_remove_selector,
                    "property" => 'bottom',  
                ),
                  array(
                    "selector" => $cart_item_remove_selector,
                    "property" => 'left',  
                ),
                  array(
                    "selector" => $cart_item_remove_selector,
                    "property" => 'right',  
                    "default" => '0',
                ),
            )
        );
        
        
        
        /**
         * Buttons
         */
        
        $cart_buttons = $this->addControlSection("cart_buttons", __("Buttons"), "assets/icon.png", $this);
        
        $cart_button_selector = '.woocommerce-mini-cart__buttons a.button';
        
        $cart_buttons_spacing = $cart_buttons->addControlSection("cart_buttons_spacing", __("Spacing"), "assets/icon.png", $this);
        
        $cart_buttons_spacing->addPreset(
            "padding",
            "cart_button_padding",
            __("Padding"),
            $cart_button_selector
        )->whiteList();
        
        $cart_buttons_spacing->addPreset(
            "margin",
            "cart_button_margin",
            __("Margin"),
             $cart_button_selector
        )->whiteList();
        
        
        
        $cart_button_align = $cart_buttons->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Flex Direction',
                'slug' => 'button_align'
            )
            
        );
        $cart_button_align->whiteList();
        $cart_button_align->setValue(array( "row" => "Row", "column" => "Column" ));
        $cart_button_align->setDefaultValue('row');
        $cart_button_align->setValueCSS( array(
            "column"  => " .woocommerce-mini-cart__buttons {
                        flex-direction: column;
                    }"
        ) );
        
         $cart_buttons->addStyleControls(
             array( 
                 array(
                    "name" => 'Border Radius',
                    "selector" => $cart_button_selector,
                    "property" => 'border-radius',
                    "default" => '4',
                ),
                array(
                    "name" => 'Button Width',
                    "selector" => $cart_button_selector,
                    "property" => 'width',
                ),
            )
        );


        $cart_buttons->typographySection('Button Typography', $cart_button_selector, $this);
        
        
        $cart_buttons_view_cart_section = $this->addControlSection("cart_buttons_view_cart_section", __("View Cart Button"), "assets/icon.png", $this);
        $cart_buttons_view_cart_selector = '.woocommerce-mini-cart__buttons .button:not(.checkout)';

        $maybe_cart_button = $cart_buttons_view_cart_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Display'),
                'slug' => 'cart_button_display'
            )
        )->setValue(array( "enable" => "Enable", "disable" => "Disable" ));
        $maybe_cart_button->setDefaultValue('enable');
        $maybe_cart_button->setValueCSS( array(
            "disable"  => " .woocommerce-mini-cart__buttons .button:not(.checkout) {
                        display: none;
                    }
                    
               ",
        ) );
            
        $cart_buttons_view_cart_section->addStyleControls(
             array( 
                array(
                    "name" => 'Background Color',
                    "selector" => $cart_buttons_view_cart_selector,
                    "property" => 'background-color',
                ),
                 array(
                    "name" => 'Text Color',
                    "selector" => $cart_buttons_view_cart_selector,
                    "property" => 'color',
                ),
                 array(
                    "name" => 'Hover Background Color',
                    "selector" => $cart_buttons_view_cart_selector.':hover',
                    "property" => 'background-color',
                ),
                 array(
                    "name" => 'Hover Text Color',
                    "selector" => $cart_buttons_view_cart_selector.':hover',
                    "property" => 'color',
                )
                 
            )
        );
        
        $cart_buttons_view_cart_section->borderSection('Borders', $cart_buttons_view_cart_selector,$this);
        $cart_buttons_view_cart_section->borderSection('Borders Hover', $cart_buttons_view_cart_selector.":hover",$this);
        $cart_buttons_view_cart_section->boxShadowSection('Box Shadow', $cart_buttons_view_cart_selector,$this);
        $cart_buttons_view_cart_section->boxShadowSection('Box Shadow Hover', $cart_buttons_view_cart_selector.":hover",$this);
        
        $cart_buttons_checkout_section = $this->addControlSection("cart_buttons_checkout_section", __("Checkout Button"), "assets/icon.png", $this);
        $cart_buttons_checkout_selector = '.woocommerce-mini-cart__buttons .button.checkout';


        $maybe_checkout_button = $cart_buttons_checkout_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Display'),
                'slug' => 'cart_button_display'
            )
        )->setValue(array( "enable" => "Enable", "disable" => "Disable" ));
        $maybe_checkout_button->setDefaultValue('enable');
        $maybe_checkout_button->setValueCSS( array(
            "disable"  => " .woocommerce-mini-cart__buttons .button.checkout {
                        display: none;
                    }
                    
               ",
        ) );
        
        $cart_buttons_checkout_section->addStyleControls(
             array( 
                array(
                    "name" => 'Background Color',
                    "selector" => $cart_buttons_checkout_selector,
                    "property" => 'background-color',
                ),
                 array(
                    "name" => 'Text Color',
                    "selector" => $cart_buttons_checkout_selector,
                    "property" => 'color',
                ),
                 array(
                    "name" => 'Hover Background',
                    "selector" => $cart_buttons_checkout_selector.':hover',
                    "property" => 'background-color',
                ),
                 array(
                    "name" => 'Hover Text Color',
                    "selector" => $cart_buttons_checkout_selector.':hover',
                    "property" => 'color',
                )
                 
            )
        );
        
        
        $cart_buttons_checkout_section->borderSection('Borders', $cart_buttons_checkout_selector,$this);
        $cart_buttons_checkout_section->borderSection('Borders Hover', $cart_buttons_checkout_selector.":hover",$this);
        $cart_buttons_checkout_section->boxShadowSection('Box Shadow', $cart_buttons_checkout_selector,$this);
        $cart_buttons_checkout_section->boxShadowSection('Box Shadow Hover', $cart_buttons_checkout_selector.":hover",$this);
        
        
        
        
            
         /**
         * Cart Total
         */
        
        $cart_total_section = $this->addControlSection("cart_total_section", __("Cart Total"), "assets/icon.png", $this);
        $cart_total_selector = '.woocommerce-mini-cart__total';
        
        
        $cart_total_spacing = $cart_total_section->addControlSection("cart_total_spacing", __("Spacing"), "assets/icon.png", $this);
        
        $cart_total_spacing->addPreset(
            "margin",
            "cart_total_margin",
            __("Margin"),
             $cart_total_selector
        )->whiteList();
        
        $cart_total_spacing->addPreset(
            "padding",
            "cart_total_padding",
            __("padding"),
             $cart_total_selector
        )->whiteList();
        
        $cart_total_section->borderSection('Borders', $cart_total_selector,$this);
        
        $cart_total_section->typographySection('Amount', '.woocommerce-mini-cart__total .woocommerce-Price-amount',$this);
        
        $cart_total_section->typographySection('Title', '.woocommerce-mini-cart__total strong',$this);
        
        
         /**
         * Inner Layout
         */
        $cart_layout = $this->addControlSection("cart_layout", __("Inner Layout"), "assets/icon.png", $this);
        $cart_layout->flex('.widget_shopping_cart_content', $this);
        
        
    }
    
    
    function customCSS($options, $selector) {
        
        $css = '';
        
        if( ! $this->css_added ) {
        
            $css .= ".oxy-mini-cart {
                        max-width: 100%;
                    }

                    .oxy-mini-cart .widget_shopping_cart_content {
                            display: flex;
                            flex-direction: column;
                            height: 100%;
                    }

                    .oxy-mini-cart .woocommerce-mini-cart__buttons {
                        display: flex;
                        width: 100%;
                        flex-direction: row;
                        justify-content: space-between;
                    }

                    .oxy-mini-cart .woocommerce-mini-cart__total {
                        display: flex;
                        flex-direction: row;
                        justify-content: space-between;
                    }

                    .oxy-mini-cart.woocommerce a.remove.remove_from_cart_button {
                        color: inherit!important;
                        position: absolute;
                        text-decoration: none;
                        right: 0;
                        font-size: 20px;
                    } 

                    .oxy-mini-cart .woocommerce-mini-cart-item {
                        position: relative;
                        text-align: left;
                    }

                    .oxy-mini-cart .woocommerce a.button {
                        margin-bottom: 10px;
                        padding: 12px 18px;
                        font-size: 10px;
                    }

                    .oxy-mini-cart .cart_list {
                        overflow-y: auto;
                    }

                    .oxy-mini-cart .woocommerce-mini-cart-item .quantity {
                        font-size: 14px;
                    }

                    .oxy-mini-cart a.remove_from_cart_button {
                        color: #333;
                    }

                    .oxy-mini-cart a.remove_from_cart_button:hover {
                        color: #f5f5f5;
                    }

                    .oxy-mini-cart a.remove_from_cart_button {
                        background-color: #f5f5f5;
                    }

                    .oxy-mini-cart .woocommerce-mini-cart__buttons a {
                        width: 48%;
                        text-align: center;
                    }

                    .oxy-mini-cart a.remove_from_cart_button:hover {
                        background-color: #e5e5e5;
                    }

                    .oxy-mini-cart ul.product_list_widget li img {
                        width: 60px;
                    }

                    .oxy-mini-cart .woocommerce-mini-cart__empty-message {
                        margin: 0;
                    }";
            
            $this->css_added = true;
            
        }
        
        return $css;
    }
    

}

new ExtraWooMiniCart();