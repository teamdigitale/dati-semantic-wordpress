<?php

class ExtraTabs extends OxygenExtraElements {
        
    var $js_added = false;
    
	function name() {
        return 'Dynamic Tabs';
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

        $dynamic_data_source = isset( $options['dynamic_data_source'] ) ? esc_attr($options['dynamic_data_source']) : "";
        
        $repeater_field = isset( $options['repeater_field'] ) ? esc_attr($options['repeater_field']) : "";
        $group_field = isset( $options['group_field'] ) ? esc_attr($options['group_field']) : "";
        $tab_field = isset( $options['tab_field'] ) ? esc_attr($options['tab_field']) : "";
        $tab_content_field = isset( $options['tab_content_field'] ) ? esc_attr($options['tab_content_field']) : "";

        $acf_post_id = isset( $options['acf_post_id'] ) ? esc_attr($options['acf_post_id']) : "";
        
        if ('options' === esc_attr($options['acf_post_id'])) {
            $post_id = "options";
        } elseif ('custom' === esc_attr($options['acf_post_id'])) {
            $post_id = $dynamic($options['custom_post_id']);
        } else {
            $post_id = false;
        }

        $metabox_post_id = $dynamic($options['metabox_post_id']);
        $metabox_user_id = $dynamic($options['metabox_user_id']);

        $transition_effect = isset( $options['transition_effect'] ) ? esc_attr($options['transition_effect']) : "";
        
        $panel_height = isset( $options['panel_height'] ) ? esc_attr($options['panel_height']) : "";
        $history = isset( $options['history'] ) ? esc_attr($options['history']) : "";
        $transition_duration = isset( $options['transitionduration'] ) ? esc_attr($options['transitionduration']) : "";
        $keyboard = isset( $options['keyboard'] ) ? esc_attr($options['keyboard']) : "";
        
        $breakpoint_layout = isset( $options['breakpoint_layout'] ) ? esc_attr($options['breakpoint_layout']) : "";
        
        $mobile_breakpoint = isset( $options['mobile_breakpoint'] ) ? oxygen_vsb_get_media_query_size($options["mobile_breakpoint"]) : "992";
        
        $maybe_autoplay = isset( $options['maybe_autoplay'] ) ? esc_attr($options['maybe_autoplay']) : "";
        $autoplay_interval = isset( $options['autoplay_interval'] ) ? esc_attr($options['autoplay_interval']) : "";
        $maybe_pauseonhover = isset( $options['maybe_pauseonhover'] ) ? esc_attr($options['maybe_pauseonhover']) : "";
        $maybe_pauseonfocus = isset( $options['maybe_pauseonhover'] ) ? esc_attr($options['maybe_pauseonhover']) : "";

        $maybe_wpautop = isset( $options['maybe_wpautop'] ) ? esc_attr($options['maybe_wpautop']) : "";
        
        $icon = isset( $options['icon'] ) ? esc_attr($options['icon']) : "";
        
        $hash_suffix = isset( $options['hash_suffix'] ) ? esc_attr($options['hash_suffix']) : "";
        
        $unique_hash = esc_attr($options['selector']);

        $metabox_data_source = isset( $options['metabox_data_source'] ) ? esc_attr($options['metabox_data_source']) : "";
        $metabox_option_name = isset( $options['metabox_option_name'] ) ? esc_attr($options['metabox_option_name']) : "";
        
        global $oxygen_svg_icons_to_load;
        $oxygen_svg_icons_to_load[] = $icon;
        
        
        $icon_output = isset( $options['icon_display'] ) && 'true' === esc_attr($options['icon_display']) ? '<span class="oxy-dynamic-tabs_tab-icon"><svg><use xlink:href="#' . $icon .'"></use></svg></span>' : '';
        
        $output = '';
        
        $output .= '<div class="oxy-dynamic-tabs_inner ';
        
        $output .= $transition_effect .' ';
        
        $output .= '" ';
        
        $output .= 'data-panel-height="'. $panel_height .'" ';
        $output .= 'data-history="'. $history .'" ';
        $output .= 'data-duration="'. $transition_duration .'" ';
        $output .= 'data-mobile-layout="'. $breakpoint_layout .'" ';
        $output .= 'data-breakpoint="'. $mobile_breakpoint .'" ';
        $output .= 'data-keyboard="'. $keyboard .'" ';
        $output .= 'data-autoplay="'. $maybe_autoplay .'" ';
        $output .= 'data-autoplay-int="'. $autoplay_interval .'" ';
        $output .= 'data-pauseonhover="'. $maybe_pauseonhover .'" ';
        $output .= 'data-pauseonfocus="'. $maybe_pauseonfocus .'" ';
        
        
        $output .= '>';
        
        $builder_tab_output = '<li class="oxy-dynamic-tabs_tab-item"><button class="oxy-dynamic-tabs_tab">';
        $builder_tab_output .= '<span class="oxy-dynamic-tabs_tab-text">Example Tab</span>';
        $builder_tab_output .= $icon_output;
        $builder_tab_output .= '</button></li>';

        $builder_tab_content_output = '<div class="oxy-dynamic-tabs_panel"><div class="oxy-dynamic-tabs_panel-inner">';
        $builder_tab_content_output .= 'Fake content just for styling as no content was found, this is only visible inside the builder. Lorem ipsum dolor sit amet consectetur adipisicing elit. Dignissimos sequi placeat distinctio dolor, amet magnam voluptatibus eos ex vero, sunt veritatis esse. Nostrum voluptatum et repudiandae vel sed, explicabo in?';
        $builder_tab_content_output .= '</div></div>';
        $builder_tab_content_output .= '<div class="oxy-dynamic-tabs_panel"><div class="oxy-dynamic-tabs_panel-inner">';
        $builder_tab_content_output .= 'XOXO enamel pin drinking vinegar pinterest tofu succulents bushwick la croix. Poke you probably post-ironic subway tile irony listicle yuccie retro waistcoat ethical. Vaporware pinterest edison bulb glossier cardigan beard neutra. Heirloom typewriter hoodie, chia wayfarers cronut banjo seitan stumptown vinyl tilde tattooed austin hell of af. Pour-over af pitchfork pickled taxidermy tumeric scenester kale chips. Yuccie skateboard shoreditch, woke hell of tacos gentrify williamsburg edison bulb salvia dreamcatcher locavore palo santo authentic.';
        $builder_tab_content_output .= '</div></div>';
        $builder_tab_content_output .= '<div class="oxy-dynamic-tabs_panel"><div class="oxy-dynamic-tabs_panel-inner">';
        $builder_tab_content_output .= 'Put a bird on it blue bottle before they sold out pickled. Snackwave try-hard glossier VHS umami fashion axe vice lumbersexual woke flannel. Copper mug tousled you heard of them master cleanse VHS sriracha yuccie salvia vice selvage pinterest locavore church-key mumblecore.';
        $builder_tab_content_output .= '</div></div>';
        $builder_tab_content_output .= '<div class="oxy-dynamic-tabs_panel"><div class="oxy-dynamic-tabs_panel-inner">';
        $builder_tab_content_output .= 'Vegan keffiyeh iceland typewriter, twee locavore cold-pressed squid edison bulb. Plaid lyft ethical kitsch pour-over iPhone hella williamsburg DIY man braid ramps. Tofu chartreuse lumbersexual polaroid etsy try-hard taiyaki. Tumblr brunch biodiesel thundercats activated charcoal vegan +1 keffiyeh DIY hoodie artisan hell of meh deep v umami.';
        $builder_tab_content_output .= '</div></div>';

        $showbuilderTabs = false;
        
        if ('metabox' === $dynamic_data_source) {

            if (function_exists('rwmb_meta')) {

                if ('settings' === $metabox_data_source) {
                    $metabox_args = ['object_type' => 'setting'];
                    $post_id = $metabox_option_name; 
                } 
                else if ('custom' === $metabox_data_source) {
                    $metabox_args = '';
                    $post_id = $metabox_post_id;
                }
                else if ('user' === $metabox_data_source) {
                    $metabox_args = ['object_type' => 'user'];
                    $post_id = $metabox_user_id;
                }
                else if ('term' === $metabox_data_source) {
                    $metabox_args = ['object_type' => 'term'];
                    $post_id = get_queried_object_id();
                }
                else if ('current_user' === $metabox_data_source) {
                    $metabox_args = ['object_type' => 'user'];
                    $post_id = get_current_user_id();
                }
                else {
                    $metabox_args = '';
                    $post_id = null; 
                }
               
                // Check group exists.
                $group_values = rwmb_meta( $group_field, $metabox_args, $post_id );

                if ( ! empty( $group_values ) ) {

                    $output .= '<ul class="oxy-dynamic-tabs_tab-group">';
                    foreach ( $group_values as $group_key => $group_value ) {

                         // Load sub field value.
                         $tab_out = isset( $group_value[$tab_field] ) ? wp_kses_post( $group_value[$tab_field] ) : '';
            
                         $output .= '<li class="oxy-dynamic-tabs_tab-item" id="'. $unique_hash . $group_key .'-item"><button class="oxy-dynamic-tabs_tab">';
                         $output .= '<span class="oxy-dynamic-tabs_tab-text">' . $tab_out . '</span>';
                         $output .= $icon_output;
                         $output .= '</button></li>';

                    }
                    $output .= '</ul><div class="oxy-dynamic-tabs_panel-group">';
                    foreach ( $group_values as $group_key => $group_value ) {

                        // Load sub field value.
                        $tab_content_out = isset( $group_value[$tab_content_field] ) ? wp_kses_post( $group_value[$tab_content_field] ) : '';

                        if ('true' === $maybe_wpautop) {
                            $tab_content_out = do_shortcode( wpautop( $tab_content_out ) );
                        }

                        $output .= '<div class="oxy-dynamic-tabs_panel" id="'. $unique_hash . '-' . $group_key . $hash_suffix .'"><div class="oxy-dynamic-tabs_panel-inner">';
                        $output .= $tab_content_out;
                        $output .= '</div></div>';

                    }

                    $output .= '</div>';

                    
                } else {
                    // No group data found
                    $showbuilderTabs = true;
                }



            } else {
                // Metabox group not active
                $showbuilderTabs = true;
            }

        } else {
        
            if (function_exists('have_rows') ) {
            
                if( have_rows($repeater_field, $post_id) ):
            
                    $output .= '<ul class="oxy-dynamic-tabs_tab-group">';

                        // Loop through rows.
                        while( have_rows($repeater_field, $post_id) ) : the_row();

                            // Load sub field value.
                            $tab_out = wp_kses_post( get_sub_field($tab_field) );
            
                            $output .= '<li class="oxy-dynamic-tabs_tab-item" id="'. $unique_hash . get_row_index() .'-item"><button class="oxy-dynamic-tabs_tab">';
                            $output .= '<span class="oxy-dynamic-tabs_tab-text">' . $tab_out . '</span>';
                            $output .= $icon_output;
                            $output .= '</button></li>';


                        // End loop.
                        endwhile;
            
                    $output .= '</ul><div class="oxy-dynamic-tabs_panel-group">';
                
                        
                        while( have_rows($repeater_field, $post_id) ) : the_row();

                            // Load sub field value.
                            $tab_content_out = wp_kses_post( get_sub_field($tab_content_field) );

                            $output .= '<div class="oxy-dynamic-tabs_panel" id="'. $unique_hash . '-' . get_row_index() . $hash_suffix .'"><div class="oxy-dynamic-tabs_panel-inner">';
                            $output .= $tab_content_out;
                            $output .= '</div></div>';

                        // End loop.
                        endwhile;
            
                    $output .= '</div>';
            

                // No fields found.
                else :
                    $showbuilderTabs = true;  
                endif;
                
            } else {
                // ACF not found.
                $showbuilderTabs = true;  
            }

        }

        if (true == $showbuilderTabs) {

            if (defined('OXY_ELEMENTS_API_AJAX') && OXY_ELEMENTS_API_AJAX) {
                    
                $output .= '<ul class="oxy-dynamic-tabs_tab-group">';
                $output .= str_repeat($builder_tab_output, 4);
                $output .= '</ul><div class="oxy-dynamic-tabs_panel-group">';
                $output .= $builder_tab_content_output;
                $output .= '</div>';
                
             }   

        }
        
        $output .= '</div>';
        
        echo $output;
        
        $inline = file_get_contents( plugin_dir_path(__FILE__) . 'assets/skeletabs/skeletabs.js' );
        $inline .= "jQuery(document).ready(function($) { 
        
               if ($('#%%ELEMENT_ID%%').find('.oxy-dynamic-tabs_inner').length) {
                            $('#%%ELEMENT_ID%%').find('.oxy-dynamic-tabs_inner').skeletabs({
                              autoplay: %%maybe_autoplay%%,
                              autoplayInterval: %%autoplay_interval%%,
                              breakpoint: (('%%mobile_breakpoint%%' != 'page-width') && ('%%mobile_breakpoint%%' != 'never')) ? '". $mobile_breakpoint ."' : '0',
                              breakpointLayout: '%%breakpoint_layout%%',
                              transitionDuration: %%transitionduration%%,    
                              disabledIndex: null,
                              history: '%%history%%',
                              keyboard: '%%keyboard%%',
                              keyboardAccordion: 'vertical',
                              keyboardTabs: 'horizontal',
                              panelHeight: '%%panel_height%%',
                              pauseOnFocus: %%maybe_pauseonfocus%%,
                              pauseOnHover: %%maybe_pauseonhover%%,
                              selectEvent: 'click',
                              slidingAccordion: true,
                              resizeTimeout: 400,    
                              startIndex: 0,
                            },
                            {
                              tabGroup: 'oxy-dynamic-tabs_tab-group',
                              tabItem: 'oxy-dynamic-tabs_tab-item',
                              tab: 'oxy-dynamic-tabs_tab',
                              panelGroup: 'oxy-dynamic-tabs_panel-group',
                              panel: 'oxy-dynamic-tabs_panel',
                              panelHeading: 'oxy-dynamic-tabs_panel-heading',
                              init: 'oxy-dynamic-tabs_init',
                              tabsMode: 'oxy-dynamic-tabs_mode-tabs',
                              accordionMode: 'oxy-dynamic-tabs_mode-accordion',
                              active: 'oxy-dynamic-tabs_active',
                              disabled: 'oxy-dynamic-tabs_disabled',
                              enter: 'oxy-dynamic-tabs_enter',
                              enterActive: 'oxy-dynamic-tabs_enter-active',
                              enterDone: 'oxy-dynamic-tabs_enter-done',
                              leave: 'oxy-dynamic-tabs_leave',
                              leaveActive: 'oxy-dynamic-tabs_leave-active',
                              leaveDone: 'oxy-dynamic-tabs_leave-done',
                        });

                    }
            });
            
        ";

        $this->dequeue_scripts_styles();
        
        if( method_exists('OxygenElement', 'builderInlineJS') ) {
            
            $this->El->builderInlineJS($inline);
            
        }
        
         // add JavaScript code only once and if shortcode presented
        if ($this->js_added !== true) {
            if (!defined('OXY_ELEMENTS_API_AJAX') || !OXY_ELEMENTS_API_AJAX) {
                add_action( 'wp_footer', array( $this, 'output_js' ) );
                add_action( 'wp_footer', array( $this, 'output_init_js' ), 20 );
            }
            $this->js_added = true;
        }
        
    
    }

    function class_names() {
        return array();
    }

    function controls() {


        $this->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Dynamic data'),
                'slug' => 'dynamic_data_source',
            )
            
        )->setValue(array( 
            "acf" => "ACF Repeater",
            "metabox" => "Meta Box Group (cloneable)",
            )
        )->setDefaultValue('acf');

        $this->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Data source'),
                'slug' => 'acf_post_id',
                'condition' => 'dynamic_data_source=acf'
            )
            
        )->setDefaultValue('page')->setValue(array( 
            "options" => "Options page",
            "page" => "Current page",
            "custom" => "Custom (post ID)"
            )
        );


        $this->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Data source'),
                'slug' => 'metabox_data_source',
                'condition' => 'dynamic_data_source=metabox'
            )
            
        )->setDefaultValue('page')->setValue(array( 
            "settings" => "Settings page",
            "page" => "Current page",
            "user" => "User ID",
            "term" => "Term",
            "current_user" => "Current User",
            "custom" => "Post ID"
            )
        );

        $this->addOptionControl(
            array(
                'type' => 'textfield',
                'name' => __('Option name'),
                'slug' => 'metabox_option_name',
                'condition' => 'dynamic_data_source=metabox&&metabox_data_source=settings'
            )
        );

        $this->addOptionControl(
            array(
                'type' => 'textfield',
                'name' => __('Post ID'),
                'slug' => 'metabox_post_id',
                'default' => '1',
                'condition' => 'dynamic_data_source=metabox&&metabox_data_source=custom'
            )
        )->setParam('dynamicdatacode', '<div optionname="\'oxy-dynamic-tabs_metabox_post_id\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');
        
        $this->addOptionControl(
            array(
                'type' => 'textfield',
                'name' => __('User ID'),
                'slug' => 'metabox_user_id',
                'default' => '1',
                'condition' => 'dynamic_data_source=metabox&&metabox_data_source=user'
            )
        )->setParam('dynamicdatacode', '<div optionname="\'oxy-dynamic-tabs_metabox_user_id\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');
        

        $this->addOptionControl(
            array(
                'type' => 'textfield',
                'name' => __('Post ID'),
                'slug' => 'custom_post_id',
                'condition' => 'dynamic_data_source=acf&&acf_post_id=custom'
            )
        )->setParam('dynamicdatacode', '<div optionname="\'oxy-dynamic-tabs_custom_post_id\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');
        
        
        /**
         * ACF Repeater
         */
        $this->addOptionControl(
            array(
                'type' => 'textfield',
                'name' => __('ACF repeater field'),
                'slug' => 'repeater_field',
                'condition' => 'dynamic_data_source=acf'
            )
        );

        /**
         * Metabox Group
         */
        $this->addOptionControl(
            array(
                'type' => 'textfield',
                'name' => __('Group field'),
                'slug' => 'group_field',
                'condition' => 'dynamic_data_source=metabox'
            )
        );
        
        $this->addOptionControl(
            array(
                'type' => 'textfield',
                'name' => __('Tab subfield'),
                'slug' => 'tab_field',
            )
        );
        
        $this->addOptionControl(
            array(
                'type' => 'textfield',
                'name' => __('Tab content subfield'),
                'slug' => 'tab_content_field',
            )
        );
        
        
        $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Format & run shortcodes'),
                'slug' => 'maybe_wpautop',
                'condition' => 'dynamic_data_source=metabox'
            )
        )->setValue(array( 
            "true" => "True",
            "false" => "False",
            )
        )->setDefaultValue('false')
         ->setParam("description", __("Applies wpautop (for WYSIWYG tab content field)"));
        
        
        /**
         * Config
         */
        $config_section = $this->addControlSection("config_section", __("Config"), "assets/icon.png", $this);

        $config_section->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Adjust height of panels'),
                'slug' => 'panel_height'
            )
            
        )->setDefaultValue('adaptive')
        ->setValue(array( 
             "auto" => "Auto", 
             "equal" => "Equalise",
             "adaptive" => "Adaptive"
            )
        )->rebuildElementOnChange();

        $config_section->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Transition effect'),
                'slug' => 'transition_effect'
            )
            
        )->setDefaultValue('use-fade')
        ->setValue(array( 
             "use-fade" => "Fade", 
             //"use-fade-scale" => "fade-scale",
             //"use-drop" => "drop",
             "use-rotate" => "Rotate",
             "none" => "None"
            )
        )->rebuildElementOnChange();
        
        $config_section->addOptionControl(
           array(
                "type" => 'slider-measurebox',
                "name" => __('Transition duration'),
                "slug" 	    => "transitionduration",
                "default" => "400",
            )
        )
        ->setUnits('ms','ms')
        ->setRange(0, 1000, 1);

        $config_section->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Browser history'),
                'slug' => 'history'
            )
            
        )->setDefaultValue('replace')
        ->setValue(array( 
             "replace" => "Replace (Update hash)", 
             "push" => "Push (Update hash & allow going back in browser)",
             "false" => "False (Do not update the hash)"
            )
        );  
        
        $config_section->addOptionControl(
            array(
                'type' => 'textfield',
                'name' => __('Hash suffix (leave blank for none)'),
                'slug' => 'hash_suffix',
                'default' => ''
            )
        )->setParam("description", __("Hashlink format #ID-1-suffix, #ID-2-suffix"));
        
        $config_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Autoplay'),
                'slug' => 'maybe_autoplay'
            )
            
        )->setDefaultValue('false')
        ->setValue(array( 
             "true" => "Enable", 
             "false" => "Disable"
            )
        );  
        
        $config_section->addOptionControl(
            array(
                'type' => 'measurebox',
                'name' => __('Autoplay interval'),
                'slug' => 'autoplay_interval',
                'default' => '3000',
                'units' => 'ms',
                'condition' => 'maybe_autoplay=true'
            )
        );
        
        
        $config_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Pause on Hover'),
                'slug' => 'maybe_pauseonhover',
                'condition' => 'maybe_autoplay=true'
            )
            
        )->setDefaultValue('false')
        ->setValue(array( 
             "true" => "Enable", 
             "false" => "Disable"
            )
        );  


        
        
        $config_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Pause on Focus'),
                'slug' => 'maybe_pauseonfocus',
                'condition' => 'maybe_autoplay=true'
            )
            
        )->setDefaultValue('true')
        ->setValue(array( 
             "true" => "Enable", 
             "false" => "Disable"
            )
        );  
        
        
        
        $config_section->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Keyboard'),
                'slug' => 'keyboard'
            )
            
        )->setDefaultValue('select')
        ->setValue(array( 
             "select" => "Move to the focused tab", 
             "focus" => "Move focus to the target tab",
             "false" => "Do not respond to keyboard inputs"
            )
        );  

        
        
        /**
         * Layout
         */
        //$layout_section = $this->addControlSection("layout_section", __("Layout"), "assets/icon.png", $this);
        
        /**
         * Style tabs
         */
        $tabs_section = $this->addControlSection("tabs_section", __("Style tabs"), "assets/icon.png", $this);
        
        $tab_group_selector = '.oxy-dynamic-tabs_tab-group';
        $tab_item_selector = '.oxy-dynamic-tabs_tab-item';
        $tab_item_active_selector = '.oxy-dynamic-tabs_tab.oxy-dynamic-tabs_active';
        $tab_button_selector = '.oxy-dynamic-tabs_tab';
        
        
        $tabs_section->addStyleControl(
            array(
                "name" => __('Horizontal alignment'),
                "property" => 'justify-content',
                "selector" => $tab_group_selector,
                "control_type" => 'dropdown',
            )
        )->setValue(array( 
            "flex-start" => "Flex start",
            "center" => "Center",
            "flex-end" => "Flex end",
            "space-between" => "Space between",
            "space-around" => "Space around",
            )
        );
        
        
        $tabs_section->addStyleControl( 
            array(
                "name" => __('Tab width'),
                "property" => 'width',
                "selector" => $tab_item_selector,
            )
        )->setParam("description", __("Set to 100% to take up all available space"));
        
        
        $tabs_colors_section = $tabs_section->addControlSection("tabs_colors_section", __("Colors"), "assets/icon.png", $this);
        
        $tabs_colors_section->addStyleControl(
                 array(
                    "name" => 'Text Color',
                    "selector" => $tab_button_selector,
                    "property" => 'color',
                )
        )->setParam('hide_wrapper_end', true);
        
        $tabs_colors_section->addStyleControl(
                array(
                    "name" => 'Hover Text Color',
                    "selector" => $tab_button_selector.":hover",
                    "property" => 'color',
                )
        )->setParam('hide_wrapper_start', true);
        
        $tabs_colors_section->addStyleControl(
                 array(
                    "name" => 'Focus Text Color',
                    "selector" => $tab_button_selector.":focus",
                    "property" => 'color',
                )
        )->setParam('hide_wrapper_end', true);
        
        $tabs_colors_section->addStyleControl(
                 array(
                    "name" => 'Active Text Color',
                    "selector" => $tab_item_active_selector,
                    "property" => 'color',
                )
        )->setParam('hide_wrapper_start', true);
        
        $tabs_colors_section->addStyleControl(
                 array(
                    "name" => 'Background',
                    "selector" => $tab_button_selector,
                    "property" => 'background-color',
                )
        )->setParam('hide_wrapper_end', true);
        
        $tabs_colors_section->addStyleControl(
                array(
                    "name" => 'Hover Background',
                    "selector" => $tab_button_selector.":hover",
                    "property" => 'background-color',
                )
        )->setParam('hide_wrapper_start', true);
        
        $tabs_colors_section->addStyleControl(
                 array(
                    "name" => 'Focus Background',
                    "selector" => $tab_button_selector.":focus",
                    "property" => 'background-color',
                )
        )->setParam('hide_wrapper_end', true);
            
        $tabs_colors_section->addStyleControl(
                 array(
                    "name" => 'Active Background',
                    "selector" => $tab_item_active_selector,
                    "property" => 'background-color',
                    "default" => '#eee' 
                )
        )->setParam('hide_wrapper_start', true);
        
        
        $tabs_section->typographySection('Typography', '.oxy-dynamic-tabs_tab',$this);
        
        $tabs_spacing_section = $tabs_section->addControlSection("tabs_spacing_section", __("Spacing"), "assets/icon.png", $this);
        
        
        $tabs_spacing_section->addStyleControl(
            array(
                "property" => 'margin-left',
                "selector" => $tab_item_selector,
            )
        )
        ->setUnits('px')->setParam('hide_wrapper_end', true);
        
        $tabs_spacing_section->addStyleControl( 
            array(
                "property" => 'margin-right',
                "selector" => $tab_item_selector,
            )
        )->setUnits('px')->setParam('hide_wrapper_start', true);
        
        $tabs_spacing_section->addStyleControl( 
            array(
                "property" => 'margin-top',
                "selector" => $tab_item_selector,
            )
        )->setUnits('px')->setParam('hide_wrapper_end', true);
        
        $tabs_spacing_section->addStyleControl( 
            array(
                "property" => 'margin-bottom',
                "selector" => $tab_item_selector,
            )
        )->setUnits('px')->setParam('hide_wrapper_start', true);
        
        
        $tabs_spacing_section->addStyleControl(
            array(
                "property" => 'padding-left',
                "selector" => $tab_button_selector,
            )
        )
        ->setUnits('px')->setParam('hide_wrapper_end', true);
        
        $tabs_spacing_section->addStyleControl( 
            array(
                "property" => 'padding-right',
                "selector" => $tab_button_selector,
            )
        )->setUnits('px')->setParam('hide_wrapper_start', true);
        
        $tabs_spacing_section->addStyleControl( 
            array(
                "property" => 'padding-top',
                "selector" => $tab_button_selector,
            )
        )->setUnits('px')->setParam('hide_wrapper_end', true);
        
        $tabs_spacing_section->addStyleControl( 
            array(
                "property" => 'padding-bottom',
                "selector" => $tab_button_selector,
            )
        )->setUnits('px')->setParam('hide_wrapper_start', true);
        
        
        //$tabs_layout_section = $tabs_section->addControlSection("tabs_layout_section", __("Layout"), "assets/icon.png", $this);
        
        
        //$tabs_layout_section->flex($tab_group_selector, $this);
        
        $tabs_section->borderSection('Borders', $tab_item_selector,$this);
        $tabs_section->boxShadowSection('Shadows', $tab_item_selector,$this);
        
        
        
        /**
         * Style tab content
         */
        $tabs_content_section = $this->addControlSection("tabs_content_section", __("Style tab content"), "assets/icon.png", $this);
        
        $tabs_content_selector = '.oxy-dynamic-tabs_panel-group';
        $tabs_content_active_selector = '.oxy-dynamic-tabs_panel.oxy-dynamic-tabs_active';
        
        $tabs_content_section->addStyleControl(
                 array(
                    "name" => 'Text Color',
                    "selector" => '.oxy-dynamic-tabs_panel-inner',
                    "property" => 'color',
                )
        )->setParam('hide_wrapper_end', true);
        
        $tabs_content_section->addStyleControl(
                 array(
                    "name" => 'Background',
                    "selector" => $tabs_content_selector,
                    "property" => 'background-color',
                )
        )->setParam('hide_wrapper_start', true);
        
        $tabs_content_section->borderSection('Borders', $tabs_content_selector,$this);
        $tabs_content_section->boxShadowSection('Shadows', $tabs_content_selector,$this);
        
        //$tabs_content_spacing_section = $tabs_content_section->addControlSection("tabs_content_spacing_section", __("Spacing"), "assets/icon.png", $this);
        
        $tabs_content_inner_selector = '.oxy-dynamic-tabs_panel-inner';
        
        $tabs_content_section->addStyleControl(
            array(
                "property" => 'padding-left',
                "selector" => $tabs_content_inner_selector,
                "value" => '30'
            )
        )
        ->setUnits('px')->setParam('hide_wrapper_end', true);
        
        $tabs_content_section->addStyleControl( 
            array(
                "property" => 'padding-right',
                "selector" => $tabs_content_inner_selector,
                "value" => '30'
            )
        )->setUnits('px')->setParam('hide_wrapper_start', true);
        
        $tabs_content_section->addStyleControl( 
            array(
                "property" => 'padding-top',
                "selector" => $tabs_content_inner_selector,
                "value" => '30'
            )
        )->setUnits('px')->setParam('hide_wrapper_end', true);
        
        $tabs_content_section->addStyleControl( 
            array(
                "property" => 'padding-bottom',
                "selector" => $tabs_content_inner_selector,
                "value" => '30'
            )
        )->setUnits('px')->setParam('hide_wrapper_start', true);
        
        
        $tabs_content_section->typographySection('Typography', $tabs_content_selector,$this);
         
        
        
        
        /**
         * Responsive
         */
        $responsive_section = $this->addControlSection("responsive_section", __("Mobile"), "assets/icon.png", $this);
        
        $responsive_section->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Mobile behaviour'),
                'slug' => 'breakpoint_layout'
            )
            
        )->setDefaultValue('accordion')
        ->setValue(array( 
             "accordion" => "Accordion", 
             //"dropdown" => "Dropdown",
             "tabs" => "Tabs"
            )
        )->rebuildElementOnChange();
        
        
        $responsive_section->addOptionControl(
            array(
                "name" => __('Mobile breakpoint'),
                "slug" => 'mobile_breakpoint',
                "type" => 'medialist',
                'condition' => 'breakpoint_layout=accordion'
            )
        )->rebuildElementOnChange();
         
        
        
        $icon_section = $responsive_section->addControlSection("icon_section", __("Accordion icons"), "assets/icon.png", $this);
        $icon_selector = '.oxy-dynamic-tabs_tab-icon';
        
        $icon_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Icon display'),
                'slug' => 'icon_display',
            )
            
        )->setDefaultValue('false')
        ->setValue(array( 
             "true" => "Enable", 
             "false" => "Disable"
            )
        )->rebuildElementOnChange();
        
            
        $icon_section->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Icon'),
                "slug" => 'icon',
                "default" => 'FontAwesomeicon-angle-down',
                "condition" => 'icon_display=true'
            )
        )->rebuildElementOnChange();
        
        
        $icon_section->addStyleControls(
            array(
                array(
                    "name" => __('Icon Size'),
                    "selector" => $icon_selector,
                    "property" => 'font-size',
                    "condition" => 'icon_display=true'
                ),
            )
        );
        
        
        $icon_section->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Rotate'),
                'slug' => 'icon_toggle_rotate',
                "condition" => 'icon_display=true'
            )
            
        )->setValue(array( 
            "rotateX" => "Vertical flip",
            "rotateY" => "Horizontal flip",
            "rotate" => "Rotate",
            //"fade" => "Fade",
            "none" => "None",
            )
        )->setDefaultValue('rotateX')
         ->setValueCSS( array(
            "rotateX"  => " .oxy-dynamic-tabs_active .oxy-dynamic-tabs_tab-icon svg {
                                transform: rotateX(180deg);
                                -webkit-transform: rotateX(180deg);
                            }",
             "rotateY"  => " .oxy-dynamic-tabs_active .oxy-dynamic-tabs_tab-icon svg {
                                transform: rotateY(180deg);
                                -webkit-transform: rotateY(180deg);
                            }",
             "rotate"  => " .oxy-dynamic-tabs_active .oxy-dynamic-tabs_tab-icon svg {
                                transform: rotate(var(--extras-icon-rotate));
                                -webkit-transform: rotate(var(--extras-icon-rotate));
                            }",
                
        ) ); 
        
        
        $icon_section->addStyleControl(
            array(
                "name" => __('Rotation amount'),
                "selector" => '.oxy-dynamic-tabs_active .oxy-dynamic-tabs_tab-icon',
                "property" => '--extras-icon-rotate',
                "control_type" => 'slider-measurebox',
                'condition' => 'icon_display=true&&icon_toggle_rotate=rotate'
            )
        )
        ->setUnits('deg','deg')
        ->setRange('-180','180');
        
        
        $icon_section->addStyleControl(
            array(
                "property" => 'transition-duration',
                "control_type" => 'slider-measurebox',
                "default" => '.3',
                "selector" => $icon_selector." svg",
                "condition" => 'icon_display=true'
            )
        )
        ->setUnits('s','s')
        ->setRange('0','1','.1');
        

    }
    
    function allowedEmptyOptions($options) {

        $options_to_add = array(
            "oxy-dynamic-tabs_hash_suffix"
        );

        $options = array_merge($options, $options_to_add);

        return $options;
    }
    
    
    function customCSS($options, $selector) {
        
        $css = "";
        
        $css .= file_get_contents( plugin_dir_path(__FILE__) . 'assets/skeletabs/skeletabs.css' );
        
        $css .= ".oxy-dynamic-tabs {
                    width: 100%;
                }
                
                .oxy-dynamic-tabs_tab-group {
                   display: flex;
                }
        
                .oxy-dynamic-tabs_panel {
                    will-change: opacity, transform, height;
                    width: 100%;
                }
                
                .oxy-dynamic-tabs_tab-icon svg {
                    height: 1em;
                    width: 1em;
                    fill: currentColor;
                }
                
                .oxy-dynamic-tabs_panel-inner {
                    padding: 30px;
                }
                
                .oxy-dynamic-tabs_tab-icon {
                    display: none;
                }

                .oxy-dynamic-tabs_tab-item {
                    display: flex;
                }

                .oxy-dynamic-tabs_mode-accordion .oxy-dynamic-tabs_tab-icon {
                    display: flex;
                }
        
                ";
        
            
       return $css;
        
    } 
    
    
    function output_js() {

         wp_enqueue_script( 'extras-tabs', plugin_dir_url(__FILE__) . 'assets/skeletabs/skeletabs.js', array( 'jquery' ), '2.0.0' );
        
        
    }
    
    function output_init_js() { ?>
            
        <script type="text/javascript">
        jQuery(document).ready(oxygen_dynamic_tabs);
        function oxygen_dynamic_tabs($) {

            let extrasTabs = function ( container ) {
            
                $(container).find('.oxy-dynamic-tabs_inner').each(function(i, dynamicTabs){
                    
                    $(dynamicTabs).skeletabs({
                        autoplay: $(dynamicTabs).data('autoplay'),
                        autoplayInterval: $(dynamicTabs).data('autoplay-int'),
                        breakpoint: $(dynamicTabs).data('breakpoint'),
                        breakpointLayout: $(dynamicTabs).data('mobile-layout'),
                        transitionDuration: $(dynamicTabs).data('duration'),    
                        history: $(dynamicTabs).data('history'),
                        keyboard: $(dynamicTabs).data('keyboard'),
                        panelHeight: $(dynamicTabs).data('panel-height'),
                        pauseOnFocus: $(dynamicTabs).data('pauseonfocus'),
                        pauseOnHover: $(dynamicTabs).data('pauseonhover'), 
                        keyboardAccordion: 'vertical',
                        keyboardTabs: 'horizontal',
                        disabledIndex: null,
                        selectEvent: 'click',
                        slidingAccordion: true,
                        resizeTimeout: 100,    
                        startIndex: 0,
                        },
                        {
                        tabGroup: 'oxy-dynamic-tabs_tab-group',
                        tabItem: 'oxy-dynamic-tabs_tab-item',
                        tab: 'oxy-dynamic-tabs_tab',
                        panelGroup: 'oxy-dynamic-tabs_panel-group',
                        panel: 'oxy-dynamic-tabs_panel',
                        panelHeading: 'oxy-dynamic-tabs_panel-heading',
                        init: 'oxy-dynamic-tabs_init',
                        tabsMode: 'oxy-dynamic-tabs_mode-tabs',
                        accordionMode: 'oxy-dynamic-tabs_mode-accordion',
                        active: 'oxy-dynamic-tabs_active',
                        disabled: 'oxy-dynamic-tabs_disabled',
                        enter: 'oxy-dynamic-tabs_enter',
                        enterActive: 'oxy-dynamic-tabs_enter-active',
                        enterDone: 'oxy-dynamic-tabs_enter-done',
                        leave: 'oxy-dynamic-tabs_leave',
                        leaveActive: 'oxy-dynamic-tabs_leave-active',
                        leaveDone: 'oxy-dynamic-tabs_leave-done',
                    });

                });

            }
                
            extrasTabs('body');
            
            // Expose function
            window.doExtrasTabs = extrasTabs;

        }</script>

    <?php }

}

add_filter("oxy_base64_encode_options", 
    function($items) { 
        $items=array_merge($items, array('oxy-dynamic-tabs_custom_post_id','oxy-dynamic-tabs_metabox_post_id', 'oxy-dynamic-tabs_metabox_user_id')); 
        return $items;
    }
);

new ExtraTabs();