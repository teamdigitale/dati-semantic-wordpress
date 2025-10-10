<?php

class ExtrahorizontalslideMenu extends OxygenExtraElements {
    
    var $js_added = false;
    var $css_added = false;

	function name() {
        return __('Horizontal Slide Menu'); 
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

        $this->dequeue_scripts_styles();

        $menu_source = isset( $options['menu_source'] ) ? esc_attr($options['menu_source']) : "";

        $open_submenu = isset( $options['open_submenu'] ) ? esc_attr( $options['open_submenu'] ) : '';
        $close_submenu = isset( $options['close_submenu'] ) ? esc_attr( $options['close_submenu'] ) : '';
        $navbar_custom_title = isset( $options['navbar_custom_title'] ) ? esc_attr( $options['navbar_custom_title'] ) : '';
        $navbar_link_type = isset( $options['navbar_link_type'] ) ? esc_attr( $options['navbar_link_type'] ) : '';

        $icon = isset( $options['icon'] ) ? esc_attr($options['icon']) : "";
        
        global $oxygen_svg_icons_to_load;
        $oxygen_svg_icons_to_load[] = $icon;


        if ('custom' === $menu_source) {
            $menu_name  = isset( $options['extras_menu_custom'] ) ? $dynamic( $options['extras_menu_custom'] ) : '';
        } else {
            $menu_name  = isset( $options['extras_menu_name'] ) ? esc_attr($options['extras_menu_name']) : '';
        }

        if ( !is_nav_menu( $menu_name ) ) {
            return;
        } 

        echo '<nav class="oxy-horizontal-slide-menu_inner" ';

        echo 'data-open="' . $open_submenu . '" ';
        echo 'data-close="' . $close_submenu . '" ';
        echo 'data-icon="' . $icon . '" ';
        echo 'data-navbar-link="' . $navbar_link_type . '" ';

        if ('custom' ===  $options['navbar_title'] ) {
            echo 'data-navbar-title="' . $navbar_custom_title . '" ';
        } else {
            echo 'data-navbar-title="' . wp_get_nav_menu_object($menu_name)->name . '" '; 
        }
        
        
        echo '>';
        
        ob_start();

        //wp_get_nav_menu_object($menu_name)->name

        
        wp_nav_menu( array(
			'menu'           => $menu_name,
			'menu_class'     => 'oxy-horizonal-slide-menu_list',
			'container'		 => '',
		) );

        $nav_menu_output = ob_get_clean();

        echo $nav_menu_output;

        echo '</nav>';

        $inline = file_get_contents( plugin_dir_path(__FILE__) . 'assets/mmenu.js' );
        
        $slideMenuInit = "

                document.querySelectorAll('#%%ELEMENT_ID%% .oxy-horizontal-slide-menu_inner .menu-item-has-children > a[href*=\"#\"]').forEach( menuItemWithChild => {
                    jQuery(menuItemWithChild).contents().unwrap().wrap('<span></span>')
                });

                new Mmenu( document.querySelector('#%%ELEMENT_ID%% .oxy-horizontal-slide-menu_inner'), {
                            offCanvas: {
                                    use: false,
                            },
                            navbar: {
                                add: true,
                                title: document.querySelector('#%%ELEMENT_ID%% .oxy-horizontal-slide-menu_inner').getAttribute('data-navbar-title'), 
                                titleLink: document.querySelector('#%%ELEMENT_ID%% .oxy-horizontal-slide-menu_inner').getAttribute('data-navbar-link')
                            },    
                            slidingSubmenus: true,
                            
                        });

                        document.querySelectorAll('#%%ELEMENT_ID%% .menu-item-has-children > .mm-listitem__btn').forEach((subMenuLink) => {
                            subMenuLink.innerHTML += '<svg class=\"oxy-horizontal-slide-menu_icon\"><use xlink:href=\"#%%icon%%\"></use></svg>';
                          });

                          document.querySelectorAll('#%%ELEMENT_ID%% .mm-btn--prev').forEach((backLink) => {
                            backLink.innerHTML += '<svg class=\"oxy-horizontal-slide-menu_icon-prev\"><use xlink:href=\"#%%icon%%\"></use></svg>';
                          });  

                        ";


        $inline .= $slideMenuInit;          


        $this->El->builderInlineJS($inline);
    
        // add JavaScript code only once and if shortcode presented
        if ($this->js_added !== true) {
                add_action( 'wp_footer', array( $this, 'output_js' ) );
                add_action( 'wp_footer', array( $this, 'output_init_js' ), 25 );
            $this->js_added = true;
        }

        
    }

    function class_names() {
        return array();
    }
    

    function controls() {
        
        
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
        $extras_menu_custom_control->setParam('dynamicdatacode', '<div optionname="\'oxy-horizontal-slide-menu_extras_menu_custom\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');
        
        $inner_selector = '.oxy-horizontal-slide-menu_inner';
        
        $this->addStyleControl( 
            array(
                "name" => __('Menu height'),
                "default" => "400",
                "units" => 'px',
                "property" => 'height',
                "control_type" => 'slider-measurebox',
                "selector" => '',
                
            )
        )
        ->setRange('0','1000','1');
        

        //$color_section = $this->addControlSection("color_section", __("Colors"), "assets/icon.png", $this);

        $this->addStyleControl( 
            array(
                "name" => __('Background color'),
                "default" => "#fff",
                "selector" => $inner_selector,
                "property" => '--mm-color-background',
                "control_type" => 'colorpicker',
            )
        )->setParam('hide_wrapper_end', true);
        
        $this->addStyleControl( 
            array(
                "name" => __('Border Color'),
                "property" => '--mm-color-border',
                "control_type" => 'colorpicker',
                "default" => '#ddd',
                "selector" => $inner_selector,
            )
        )->setParam('hide_wrapper_start', true);


        
        $navbar_section = $this->addControlSection("navbar_section", __("Navbar"), "assets/icon.png", $this);


       

        $navbar_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Navbar title'),
                'slug' => 'navbar_title',
            )
        )
        ->setValue(array( "custom" => "Custom", "menu" => "Menu title" ))
        ->setDefaultValue('menu')
        ->rebuildElementOnChange();


        $navbar_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Title'),
                "slug" => 'navbar_custom_title',
                "default" => 'Menu',
                "condition" => 'navbar_title=custom'
            )
        )->rebuildElementOnChange();

        $navbar_section->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' =>  __('Navbar link type'),
                'slug' => 'navbar_link_type'
            )
            
        )->setValue(array( 
            "parent" => __("Back to parent"), 
            "anchor" => __("Follow link"),
            "none" => __("none")  
        ))
        ->setDefaultValue('parent')
        ->rebuildElementOnChange();

        $navbar_section->addStyleControl( 
            array(
                "name" => __('Navbar height'),
                "control_type" => "measurebox",
                "default" => "50",
                "unit" => 'px',
                "property" => '--mm-navbar-size',
                "selector" => $inner_selector,
            )
        );

        $navbar_section->typographySection('Typography', '.mm-navbar',$this);


       


        $menu_items_section = $this->addControlSection("menu_items_section", __("Menu items"), "assets/icon.png", $this);


        $menu_items_section->addStyleControl( 
            array(
                "name" => __('Color'),
                "property" => 'color',
                "control_type" => 'colorpicker',
                "selector" => '.menu-item a',
            )
        )->setParam('hide_wrapper_end', true);


        $menu_items_section->addStyleControl( 
            array(
                "name" => __('Color (hover)'),
                "property" => 'color',
                "control_type" => 'colorpicker',
                "selector" => '.menu-item a:hover',
            )
        )->setParam('hide_wrapper_start', true);

        $menu_items_section->addStyleControl( 
            array(
                "name" => __('Color (focus)'),
                "property" => 'color',
                "control_type" => 'colorpicker',
                "selector" => '.menu-item a:focus-visible',
            )
        );

        $menu_items_section->addStyleControl( 
            array(
                "name" => __('Background'),
                "default" => "",
                "selector" => '.menu-item a',
                "property" => 'background-color',
                "control_type" => 'colorpicker',
            )
        )->setParam('hide_wrapper_end', true);

        $menu_items_section->addStyleControl( 
            array(
                "name" => __('Background (hover)'),
                "default" => "rgba(0,0,0,0.02)",
                "selector" => '.menu-item a:hover',
                "property" => 'background-color',
                "control_type" => 'colorpicker',
            )
        )->setParam('hide_wrapper_start', true);

        $menu_items_section->addStyleControl( 
            array(
                "name" => __('Background (focus)'),
                "property" => 'background-color',
                "control_type" => 'colorpicker',
                "selector" => '.menu-item a:focus-visible',
            )
        );

        
        $current_menu_item_selector = '.current-menu-item > a';

        $menu_items_section->addStyleControl( 
            array(
                "name" => __('Current menu color'),
                "property" => 'color',
                "control_type" => 'colorpicker',
                "selector" => $current_menu_item_selector,
            )
        );

        $menu_items_section->typographySection('Typography', '.mm-listitem__text', $this);

        
        $menu_items_section->addStyleControl( 
            array(
                "name" => __('Focus outline color'),
                "property" => '--mm-color-focusring',
                "default" => "#aaa",
                "control_type" => 'colorpicker',
                "selector" => $inner_selector,
            )
        );


        $menu_items_section->addPreset(
            "padding",
            "link_padding",
            __("Padding"),
            '.mm-listitem__text'
        )->whiteList();

          /**
         * Icons
         */
        
        $icon_section = $this->addControlSection("icon_section", __("Icons"), "assets/icon.png", $this);
        
        
        $icon_section->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Icon'),
                "slug" => 'icon',
                "default" => 'FontAwesomeicon-angle-right'
            )
        )->rebuildElementOnChange();

        $icon_section->addStyleControls(
            array(
                array(
                    "name" => __('Icon Size'),
                    "selector" => '.mm-btn svg',
                    "property" => 'font-size',
                   
                ),
            )
        );

        /*

        $icon_section->addStyleControl( 
            array(
                "name" => __('Icon color'),
                "selector" => '.menu-item .mm-listitem__btn',
                "property" => 'color',
                "control_type" => 'colorpicker',
            )
        )->setParam('hide_wrapper_end', true);

        $icon_section->addStyleControl( 
            array(
                "name" => __('Icon hover'),
                "selector" => '.menu-item .mm-listitem__btn:hover',
                "property" => 'color',
                "control_type" => 'colorpicker',
            )
        )->setParam('hide_wrapper_start', true);

        $icon_section->addStyleControl( 
            array(
                "name" => __('Background'),
                "selector" => '.menu-item .mm-listitem__btn',
                "property" => 'background-color',
                "control_type" => 'colorpicker',
            )
        )->setParam('hide_wrapper_end', true);

        $icon_section->addStyleControl( 
            array(
                "name" => __('Hover Background'),
                "selector" => '.menu-item .mm-listitem__btn:hover',
                "property" => 'background-color',
                "control_type" => 'colorpicker',
            )
        )->setParam('hide_wrapper_start', true);

        */


        $icon_section->addStyleControl( 
            array(
                "name" => __('Padding left'),
                "property" => 'padding-left',
                "control_type" => "measurebox",
                "unit" => "px",
                "value" => '10',
                "selector" => '.menu-item .mm-listitem__btn:not(.mm-listitem__text)',
            )
        )->setParam('hide_wrapper_end', true);

        $icon_section->addStyleControl( 
            array(
                "name" => __('Padding right'),
                "property" => 'padding-right',
                "control_type" => "measurebox",
                "unit" => "px",
                "value" => '10',
                "selector" => '.menu-item .mm-listitem__btn:not(.mm-listitem__text)',
            )
        )->setParam('hide_wrapper_start', true);

        


        $icon_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Open submenu link title'),
                "slug" => 'open_submenu',
                "default" => 'Open submenu'
            )
        );

        $icon_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Close submenu link title'),
                "slug" => 'close_submenu',
                "default" => 'Close submenu'
            )
        );
        
        
    }
    
    
    function customCSS($options, $selector) {
        
        $css = '';
        
        if( ! $this->css_added ) {

            $css .= file_get_contents( plugin_dir_path(__FILE__) . 'assets/mmenu.css' );
        
            $css .= ".oxy-horizontal-slide-menu {
                        height: 400px;
                        width: 100%;
                    }

                    .oxy-horizontal-slide-menu_inner {
                        visibility: hidden;
                    }

                    .oxy-horizontal-slide-menu_inner.mm-menu {
                        visibility: visible;
                    }

                    .oxy-horizontal-slide-menu .menu-item:not(.menu-item-has-children):before {
                        display: none;
                    }

                    .oxy-horizontal-slide-menu .menu-item {
                        display: flex;
                        flex-direction: row;
                    }

                    .oxy-horizontal-slide-menu .menu-item a[href='#'] {
                        pointer-events: none;
                    }

                    .oxy-horizontal-slide-menu .mm-panel {
                        will-change: transform;
                    }

                    .oxy-horizontal-slide-menu_icon {
                        height: 1em;
                        width: 1em;
                        font-size: 1em;
                        fill: currentColor;
                    }

                    .oxy-horizontal-slide-menu_icon-prev {
                        height: 1em;
                        width: 1em;
                        font-size: 1em;
                        transform: rotateY(180deg);
                        -webkit-transform: rotateY(180deg);
                        fill: currentColor;
                    }

                    .oxy-horizontal-slide-menu .menu-item a:hover {
                        background-color: rgba(0,0,0,0.02);
                    }

                    .oxy-horizontal-slide-menu .mm-listitem__btn {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        padding: 10px;
                    }

                    .oxy-horizontal-slide-menu .mm-listitem__text {
                        justify-content: space-between;
                        padding: 10px;
                    }
                    ";
            
            $this->css_added = true;
            
        }

            return $css;
        
    }

    function output_js() {

        wp_enqueue_script( 'extras-horizontal-slide-menu', plugin_dir_url(__FILE__) . 'assets/mmenu.js', array( 'jquery' ), '1.0.0' );
        
   }
    
    
    function output_init_js() { ?>
            <script type="text/javascript">  

                   document.addEventListener(
                        "DOMContentLoaded", () => {

                                document.querySelectorAll('.oxy-horizontal-slide-menu_inner .menu-item-has-children > a[href*="#"]').forEach( menuItemWithChild => {
                                    jQuery(menuItemWithChild).contents().unwrap().wrap("<span></span>")
                                });

                                document.addEventListener(
                                    "DOMContentLoaded", () => {
                                        const menu = new MmenuLight(
                                            document.querySelector("#mmenu")
                                        );

                                        const navigator = menu.navigation({
                                            slidingSubmenus: true,
                                            theme: 'dark',
                                            title: 'Menu'
                                        });
                                    }
                                );

                            document.querySelectorAll('.oxy-horizontal-slide-menu_inner').forEach( slideMenu => {

                                new Mmenu( slideMenu, {
                                        offCanvas: {
                                                use: false,
                                        },
                                        navbar: {
                                            add: true,
                                            title: slideMenu.getAttribute('data-navbar-title'),
                                            titleLink: slideMenu.getAttribute('data-navbar-link'),
                                        }, 
                                        slidingSubmenus: true, 
                                        panelNodetype: ["div", "ul", "ol"]
                                        },
                                        {
                                        screenReader: {
                                            closeSubmenu: slideMenu.getAttribute('data-close'),
                                            openSubmenu: slideMenu.getAttribute('data-open'),
                                        }
                                    }

                                )

                                slideMenu.querySelectorAll('.menu-item-has-children > .mm-listitem__btn').forEach( subMenuLink => {
                                    subMenuLink.innerHTML += '<svg class=\"oxy-horizontal-slide-menu_icon\"><use xlink:href=\"#'+ slideMenu.getAttribute('data-icon') +'\"></use></svg>';
                                }); 

                                slideMenu.querySelectorAll('.mm-btn--prev').forEach( backLink => {
                                    backLink.innerHTML += '<svg class=\"oxy-horizontal-slide-menu_icon-prev\"><use xlink:href=\"#'+ slideMenu.getAttribute('data-icon') +'\"></use></svg>';
                                }); 
                                
                            });  

                        }
                    );
            
        </script>

    <?php }
    
    function afterInit() {
        //$this->removeApplyParamsButton();
    }

}

add_filter("oxy_base64_encode_options", 
    function($items) { 
        $items=array_merge($items, array('oxy-horizontal-slide-menu_extras_menu_custom')); 
        return $items;
    }
);

new ExtrahorizontalslideMenu();