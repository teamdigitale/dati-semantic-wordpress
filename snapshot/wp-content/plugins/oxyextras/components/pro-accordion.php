<?php

class ExtraAccordion extends OxygenExtraElements {
        
    var $js_added = false;
    
	function name() {
        return 'Pro Accordion';
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
        
        $title_text = html_entity_decode( $dynamic($options['title_text']) );
        $subtitle_text = html_entity_decode( $dynamic($options['subtitle_text']) );
        $metabox_post_id = $dynamic($options['metabox_post_id']);
        $metabox_user_id = $dynamic($options['metabox_user_id']);
        
        $accordion_type = isset( $options['accordion_type'] ) ? esc_attr($options['accordion_type']) : "";
        $initial_state = isset( $options['initial_state'] ) ? esc_attr($options['initial_state']) : "";
        $initial_class = isset( $options['initial_state'] ) && ('open' === esc_attr($options['initial_state']) ) ? 'active' : '';
        $acf_inital = isset( $options['acf_inital'] ) ? esc_attr($options['acf_inital']) : "";
        $repeater_field = isset( $options['repeater_field'] ) ? esc_attr($options['repeater_field']) : "";
        $group_field = isset( $options['group_field'] ) ? esc_attr($options['group_field']) : "";
        $title_field = isset( $options['title_field'] ) ? esc_attr($options['title_field']) : "";
        $subtitle_field = isset( $options['subtitle_field'] ) ? esc_attr($options['subtitle_field']) : "";
        $content_field = isset( $options['content_field'] ) ? esc_attr($options['content_field']) : "";
        $acf_post_id = isset( $options['acf_post_id'] ) ? esc_attr($options['acf_post_id']) : "";
        
        $maybe_repeater = isset( $options['maybe_repeater'] ) ? esc_attr($options['maybe_repeater']) : "";
        $first_open_repeater = 'false';
        $maybe_wpautop = isset( $options['maybe_wpautop'] ) ? esc_attr($options['maybe_wpautop']) : "";

        if ( isset( $options['first_open_repeater'] ) ) {
           if ( 'open' === esc_attr($options['first_open_repeater']) && 'true' === $maybe_repeater ) {
            $first_open_repeater = 'true';
           }
        }
        
        if ('options' === esc_attr($options['acf_post_id'])) {
            $post_id = "options";
        } elseif ('custom' === esc_attr($options['acf_post_id'])) {
            $post_id = $dynamic($options['custom_post_id']);
        } else {
            $post_id = false;
        }
        
        $toggle_icon = isset( $options['toggle_icon'] ) ? esc_attr($options['toggle_icon']) : "";
        $toggle_close_icon = isset( $options['toggle_close_icon'] ) ? esc_attr($options['toggle_close_icon']) : "";
        $context_icon = isset( $options['context_icon'] ) ? esc_attr($options['context_icon']) : "";
        $expand_speed = isset( $options['expand_speed'] ) ? esc_attr($options['expand_speed']) : "";
        
        $toggle_expanded = isset( $options['toggle_expanded'] ) ? esc_attr($options['toggle_expanded']) : "";
        $icon_animate_class = isset( $options['toggle_expanded'] ) && ('animate' === esc_attr($options['toggle_expanded']) ) ? ' oxy-pro-accordion_icon-animate' : "";
        
        $question_schema = isset( $options['maybe_faq_schema'] ) && ('enable' === esc_attr($options['maybe_faq_schema']) ) ? ' itemscope itemprop="mainEntity" itemtype="https://schema.org/Question"' : '';
        $answer_schema = isset( $options['maybe_faq_schema'] ) && ('enable' === esc_attr($options['maybe_faq_schema']) ) ? ' itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer"' : '';
        $name_schema = isset( $options['maybe_faq_schema'] ) && ('enable' === esc_attr($options['maybe_faq_schema']) ) ? ' itemprop="name"' : '';
        $text_schema = isset( $options['maybe_faq_schema'] ) && ('enable' === esc_attr($options['maybe_faq_schema']) ) ? ' itemprop="text"' : '';
        
        $metabox_data_source = isset( $options['metabox_data_source'] ) ? esc_attr($options['metabox_data_source']) : "";
        $metabox_option_name = isset( $options['metabox_option_name'] ) ? esc_attr($options['metabox_option_name']) : "";
        
        if ( isset( $options['item_tags'] ) && ( 'list' === esc_attr($options['item_tags']) ) ) {
            
            $list_tag = 'ul';
            
        } elseif ( isset( $options['item_tags'] ) && ( 'number' === esc_attr($options['item_tags']) ) ) {
            
            $list_tag = 'ol';
            
        } else {
            $list_tag = 'div';    
        }
        
        $disable_sibling_togggle = ( isset( $options['maybe_close_others'] ) && ( 'disable' === esc_attr($options['maybe_close_others']) ) ) ? ' data-disablesibling="true"' : '';

        $container_selector = isset( $options['container_selector'] ) ? esc_attr($options['container_selector']) : "";

        if ( isset( $options['maybe_close_others'] ) && ( 'disable' === esc_attr($options['maybe_close_others']) ) ) {
            $disable_sibling_togggle = ' data-disablesibling="true"';
        }

        elseif ( isset( $options['maybe_close_others'] ) && ( 'container' === esc_attr($options['maybe_close_others']) ) ) {
            $disable_sibling_togggle = ' data-disablesibling="'. $container_selector .'"';
        }

        else {
            $disable_sibling_togggle = ' data-disablesibling="false"';
        }
        
        $item_tag = isset( $options['item_tags'] ) && ( 'div' === esc_attr($options['item_tags']) ) ? 'div' : 'li';
        $title_tag = isset( $options['title_tag'] ) ? esc_attr($options['title_tag']) : "h3";
        $subtitle_tag = isset( $options['subtitle_tag'] ) ? esc_attr($options['subtitle_tag']) : "div";
            
        global $oxygen_svg_icons_to_load;
        $oxygen_svg_icons_to_load[] = $toggle_icon;
        $oxygen_svg_icons_to_load[] = $context_icon;
        $oxygen_svg_icons_to_load[] = $toggle_close_icon;
        
        $builder_clicks = (defined('OXY_ELEMENTS_API_AJAX') && OXY_ELEMENTS_API_AJAX) ? 'onclick="extrasOpenAccordion.call(this)" ' : '';
        
        $output = '<'.$list_tag.' class="oxy-pro-accordion_inner" data-icon="'.$toggle_expanded.'" data-expand="'.$expand_speed.'" data-repeater="'. $maybe_repeater .'" data-repeater-first="' . $first_open_repeater . '" data-acf="'.$acf_inital.'" data-type="'.$accordion_type.'"'.$disable_sibling_togggle.'>';
        
        $showbuilderAccordion = false;

        if ('manual' === $accordion_type ) {
        
            $output .= '
              <div class="oxy-pro-accordion_item '.$initial_class.'" data-init="'.$initial_state.'"'.$question_schema.'><button '.$builder_clicks.' id="header' . esc_attr($options['selector']) . '" class="oxy-pro-accordion_header" aria-expanded="false" aria-controls="body' . esc_attr($options['selector']) . '">';
            
            if ('disable' !== esc_attr($options['context_icon_display'])) {
                $output .= '<span class="oxy-pro-accordion_context-icon">';
                if ('icon' === esc_attr($options['context_type'])) {
                      $output .= '<svg id="title-' . esc_attr($options['selector']) . '"><use xlink:href="#' . $context_icon .'"></use></svg>';
                }
                $output .= '</span>';
            }
            
            $output .= '<span class="oxy-pro-accordion_title-area"'.$name_schema.'><'.$title_tag.' class="oxy-pro-accordion_title">'.$title_text.'</'.$title_tag.'><'.$subtitle_tag.' class="oxy-pro-accordion_subtitle">'.$subtitle_text.'</'.$subtitle_tag.'></span>';

            if ('disable' !== esc_attr($options['icon_display'])) {
                $output .= '<span class="oxy-pro-accordion_icon'.$icon_animate_class.'"><svg id="toggle' . esc_attr($options['selector']) . '" class="oxy-pro-accordion_toggle-icon"><use xlink:href="#' . $toggle_icon .'"></use></svg>';
                if ('switch' === esc_attr($options['toggle_expanded'])) {
                    $output .= '<svg id="toggle-close' . esc_attr($options['selector']) . '" class="oxy-pro-accordion_close-icon"><use xlink:href="#' . $toggle_close_icon .'"></use></svg>';
                }
                $output .= '</span>';
            }

            $output .= '</button><div id="body' . esc_attr($options['selector']) . '" class="oxy-pro-accordion_body" aria-labelledby="header' . esc_attr($options['selector']) . '" role="region"'.$answer_schema.'><div class="oxy-pro-accordion_content oxy-inner-content"'.$text_schema.'>';

            if ($content) {
                if ( function_exists('do_oxygen_elements') ) {
                    $output .= do_oxygen_elements($content); 
                }
                else {
                    $output .= do_shortcode($content); 
                } 
            } else {
                $output .= 'Add elements inside. Lorem ipsum dolor sit amet consectetur adipisicing elit. Dignissimos sequi placeat distinctio dolor, amet magnam voluptatibus eos ex vero, sunt veritatis esse. Nostrum voluptatum et repudiandae vel sed, explicabo in?';
            }

            $output .= '</div></div></div>';
            
        } else if ('metabox' === $accordion_type ) {

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
                    foreach ( $group_values as $group_key => $group_value ) {

                        // Load sub field value.
                        $title_out = isset( $group_value[$title_field] ) ? wp_kses_post( $group_value[$title_field] ) : '';
                        $subtitle_out = isset( $group_value[$subtitle_field] ) ? wp_kses_post( $group_value[$subtitle_field] ) : '';
                        $content_out = isset( $group_value[$content_field] ) ? wp_kses_post( $group_value[$content_field] ) : '';

                        if ('true' === $maybe_wpautop) {
                            $content_out = do_shortcode( wpautop( $content_out ) );
                        }

                        $output .= '<'.$item_tag.' class="oxy-pro-accordion_item"'.$question_schema.'><button '.$builder_clicks.' id="header' . esc_attr($options['selector']) .'-'. $group_key .'" class="oxy-pro-accordion_header" aria-controls="body' . esc_attr($options['selector']) .'-'. $group_key .'" aria-expanded=false>';

                        if ('disable' !== esc_attr($options['context_icon_display'])) {
                            $output .= '<span class="oxy-pro-accordion_context-icon">';
                            if ('icon' === esc_attr($options['context_type'])) {
                                  $output .= '<svg id="title-' . esc_attr($options['selector']) . '"><use xlink:href="#' . $context_icon .'"></use></svg>';
                            }
                            $output .= '</span>';
                        }

                        $output .= '<span class="oxy-pro-accordion_title-area"'.$name_schema.'><'.$title_tag.' class="oxy-pro-accordion_title">'.$title_out.'</'.$title_tag.'><'.$subtitle_tag.' class="oxy-pro-accordion_subtitle">'.$subtitle_out.'</'.$subtitle_tag.'></span>';

                        if ('disable' !== esc_attr($options['icon_display'])) {
                            $output .= '<span class="oxy-pro-accordion_icon'.$icon_animate_class.'"><svg id="toggle-' . esc_attr($options['selector']) . '" class="oxy-pro-accordion_toggle-icon"><use xlink:href="#' . $toggle_icon .'"></use></svg>';
                            if ('switch' === esc_attr($options['toggle_expanded'])) {
                                $output .= '<svg id="toggle-close' . esc_attr($options['selector']) . '" class="oxy-pro-accordion_close-icon"><use xlink:href="#' . $toggle_close_icon .'"></use></svg>';
                            }
                            $output .= '</span>';
                        }

                        $output .= '</button><div id="body' . esc_attr($options['selector']) .'-'. $group_key . '" class="oxy-pro-accordion_body" aria-labelledby="header' . esc_attr($options['selector']) .'-'. $group_key . '" role="region"'.$answer_schema.'><div class="oxy-pro-accordion_content"'.$text_schema.'>'.$content_out.'</div></div></'.$item_tag.'>';


                    }
                } else {
                    // If metabox group field not found
                    $showbuilderAccordion = true;
                }
                
            } else {
                // If metabox plugin not active
                $showbuilderAccordion = true;
            }

        } else {
            
            if (!function_exists('have_rows')) { // ACF Pro not active
                // If acf pro not active
                $showbuilderAccordion = true;
            } 
            
            else { // ACF Pro active
            
                // Check rows exists.
                if( have_rows($repeater_field, $post_id) ):

                    // Loop through rows.
                    while( have_rows($repeater_field, $post_id) ) : the_row();

                        // Load sub field value.
                        $title_out = wp_kses_post( get_sub_field($title_field) );
                        $subtitle_out = wp_kses_post( get_sub_field($subtitle_field) );
                        $content_out = wp_kses_post( get_sub_field($content_field) );

                        $output .= '<'.$item_tag.' class="oxy-pro-accordion_item"'.$question_schema.'><button '.$builder_clicks.' id="header' . esc_attr($options['selector']) .'-'. get_row_index() .'" class="oxy-pro-accordion_header" aria-controls="body' . esc_attr($options['selector']) .'-'. get_row_index() .'" aria-expanded=false>';

                        if ('disable' !== esc_attr($options['context_icon_display'])) {
                            $output .= '<span class="oxy-pro-accordion_context-icon">';
                            if ('icon' === esc_attr($options['context_type'])) {
                                  $output .= '<svg id="title-' . esc_attr($options['selector']) . '"><use xlink:href="#' . $context_icon .'"></use></svg>';
                            }
                            $output .= '</span>';
                        }

                        $output .= '<span class="oxy-pro-accordion_title-area"'.$name_schema.'><'.$title_tag.' class="oxy-pro-accordion_title">'.$title_out.'</'.$title_tag.'><'.$subtitle_tag.' class="oxy-pro-accordion_subtitle">'.$subtitle_out.'</'.$subtitle_tag.'></span>';

                        if ('disable' !== esc_attr($options['icon_display'])) {
                            $output .= '<span class="oxy-pro-accordion_icon'.$icon_animate_class.'"><svg id="toggle-' . esc_attr($options['selector']) . '" class="oxy-pro-accordion_toggle-icon"><use xlink:href="#' . $toggle_icon .'"></use></svg>';
                            if ('switch' === esc_attr($options['toggle_expanded'])) {
                                $output .= '<svg id="toggle-close' . esc_attr($options['selector']) . '" class="oxy-pro-accordion_close-icon"><use xlink:href="#' . $toggle_close_icon .'"></use></svg>';
                            }
                            $output .= '</span>';
                        }

                        $output .= '</button><div id="body' . esc_attr($options['selector']) .'-'. get_row_index() . '" class="oxy-pro-accordion_body" aria-labelledby="header' . esc_attr($options['selector']) .'-'. get_row_index() . '" role="region"'.$answer_schema.'><div class="oxy-pro-accordion_content"'.$text_schema.'>'.$content_out.'</div></div></'.$item_tag.'>';



                    // End loop.
                    endwhile;

                // No value.
                else :
            
                    // If no repeater fields found
                    $showbuilderAccordion = true;
                
            endif;
            
         } // end of ACF pro active
            
        } // end of acf pro setting

        // Builder accordion for styling purposes only when no fields found (will not show on front end)
        if (true == $showbuilderAccordion) {

            if( isset($_GET['oxygen_iframe']) || defined('OXY_ELEMENTS_API_AJAX') ) {

                // Load sub field value.
                $title_out = $options['title_field'] ? 'Header text would be here' : '';
                $subtitle_out = $options['subtitle_field'] ? 'Subtext would be here' : '';
                $content_out = $options['content_field'] ? 'Fake content just for styling as no field data was found, this is only visible inside the builder. Lorem ipsum dolor sit amet consectetur adipisicing elit. Dignissimos sequi placeat distinctio dolor, amet magnam voluptatibus eos ex vero, sunt veritatis esse. Nostrum voluptatum et repudiandae vel sed, explicabo in?' : '';

                $builder_output = '<'.$item_tag.' class="oxy-pro-accordion_item"'.$question_schema.'><button '.$builder_clicks.' id="header' . esc_attr($options['selector']) .'" class="oxy-pro-accordion_header" aria-controls="body' . esc_attr($options['selector']) .'" aria-expanded=false>';

                    if ('disable' !== esc_attr($options['context_icon_display'])) {
                        $builder_output .= '<span class="oxy-pro-accordion_context-icon">';
                        if ('icon' === esc_attr($options['context_type'])) {
                              $builder_output .= '<svg id="title-' . esc_attr($options['selector']) . '"><use xlink:href="#' . $context_icon .'"></use></svg>';
                        }
                        $builder_output .= '</span>';
                    }

                    $builder_output .= '<span class="oxy-pro-accordion_title-area"'.$name_schema.'><'.$title_tag.' class="oxy-pro-accordion_title">'.$title_out.'</'.$title_tag.'><'.$subtitle_tag.' class="oxy-pro-accordion_subtitle">'.$subtitle_out.'</'.$subtitle_tag.'></span>';

                    if ('disable' !== esc_attr($options['icon_display'])) {
                        $builder_output .= '<span class="oxy-pro-accordion_icon'.$icon_animate_class.'"><svg id="toggle-' . esc_attr($options['selector']) . '" class="oxy-pro-accordion_toggle-icon"><use xlink:href="#' . $toggle_icon .'"></use></svg>';
                        if ('switch' === esc_attr($options['toggle_expanded'])) {
                            $output .= '<svg id="toggle-close' . esc_attr($options['selector']) . '" class="oxy-pro-accordion_close-icon"><use xlink:href="#' . $toggle_close_icon .'"></use></svg>';
                        }
                        $builder_output .= '</span>';
                    }

                    $builder_output .= '</button><div id="body' . esc_attr($options['selector']) .'" class="oxy-pro-accordion_body" aria-labelledby="header' . esc_attr($options['selector']) .'" role="region"'.$answer_schema.'><div class="oxy-pro-accordion_content"'.$text_schema.'>'.$content_out.'</div></div></'.$item_tag.'>';


                $output .= str_repeat($builder_output, 4);

            } // inside builder

        }
        
        $output .= '</'.$list_tag.'>'; //close inner
        
        echo $output; 

        $this->dequeue_scripts_styles();
        
        
        if( method_exists('OxygenElement', 'builderInlineJS') ) {
        
             $this->El->builderInlineJS(
                "   
                function extrasOpenAccordion() {
                  jQuery(this).closest('.oxy-pro-accordion_item').toggleClass('active');
                }
             ");
            
        }
        
        
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
        
        
         $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('In-Editor Mode'),
                'slug' => 'editor_state',
            )
            
        )->setValue(array( 
            "open" => "Expanded",
            "closed" => "Collapsed",
            )
        )->setDefaultValue('open')
         ->setValueCSS( array(
            "closed"  => " .oxy-pro-accordion_body {
                                --extras-hide-accordion: none;
                            }",
             "open"  => " .oxy-pro-accordion_body {
                                --extras-hide-accordion: block;
                            }",
                
        ) )->setParam('ng_show', "!iframeScope.isEditing('state')&&!iframeScope.isEditing('media')");
        
        
        $this->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Accordion type'),
                'slug' => 'accordion_type',
            )
            
        )->setValue(array( 
            "acf" => "Accordion - Dynamic Items (ACF)",
            "manual" => "Accordion - Individual Item",
            "metabox" => "Accordion - Dynamic Items (Meta Box)",
            )
        )->setDefaultValue('manual')
         ->setValueCSS( array(
            "acf"  => " > div:not(.oxy-pro-accordion_inner) {
                            display: none;
                        }",
            "metabox"  => " > div:not(.oxy-pro-accordion_inner) {
                            display: none;
                        }",
                
        ) );
        
        
        $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Initial state'),
                'slug' => 'initial_state',
                'condition' => 'accordion_type=manual'
            )
            
        )->setValue(array( 
            "open" => "Expanded",
            "closed" => "Collapsed",
            )
        )->setDefaultValue('closed');
        
        
        

        
        $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('First item open'),
                'slug' => 'acf_inital',
                'condition' => 'accordion_type=acf||accordion_type=metabox'
            )
            
        )->setValue(array( 
            "open" => "True",
            "closed" => "False",
            )
        )->setDefaultValue('closed')
         ->setValueCSS( array(
            "open"  => " .oxy-pro-accordion_item:first-child .oxy-pro-accordion_body {
                                --extras-hide-accordion: flex;
                            }",
                
        ) ); 
        
        
        $this->addStyleControl(
            array(
                "name" => __('Expanding duration'),
                "property" => '--extras-accordion-duration',
                "control_type" => 'slider-measurebox',
                "default" => '300',
                "selector" => '.oxy-pro-accordion_item',
                "slug" => 'expand_speed',
            )
        )->setUnits("ms","ms")
         ->setRange('0','1000','10');
        

        /**
         * Dynamic Data
         */ 
        $dynamic_section = $this->addControlSection("dynamic_section", __("Dynamic Data"), "assets/icon.png", $this);


        /**
         * Header
         */ 
        $header_section = $this->addControlSection("header_section", __("Accordion Header"), "assets/icon.png", $this);
        $header_selector = '.oxy-pro-accordion_header';
        
        
        $dynamic_section->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('ACF field source'),
                'slug' => 'acf_post_id',
                'condition' => 'accordion_type=acf'
            )
            
        )->setDefaultValue('page')->setValue(array( 
            "options" => "Options page",
            "page" => "Current page",
            "custom" => "Custom (post ID)"
            )
        );
        
        $dynamic_section->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Data source'),
                'slug' => 'metabox_data_source',
                'condition' => 'accordion_type=metabox'
            )
            
        )->setDefaultValue('page')->setValue(array( 
            "settings" => "Settings page",
            "page" => "Post (current)",
            "user" => "User ID",
            "term" => "Term",
            "current_user" => "User (current)",
            "custom" => "Post ID"
            )
        );

        $dynamic_section->addOptionControl(
            array(
                'type' => 'textfield',
                'name' => __('Option name'),
                'slug' => 'metabox_option_name',
                'condition' => 'accordion_type=metabox&&metabox_data_source=settings'
            )
        );

        $dynamic_section->addOptionControl(
            array(
                'type' => 'textfield',
                'name' => __('Post ID'),
                'slug' => 'metabox_post_id',
                'default' => '1',
                'condition' => 'accordion_type=metabox&&metabox_data_source=custom'
            )
        )->setParam('dynamicdatacode', '<div optionname="\'oxy-pro-accordion_metabox_post_id\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');
        
        $dynamic_section->addOptionControl(
            array(
                'type' => 'textfield',
                'name' => __('User ID'),
                'slug' => 'metabox_user_id',
                'default' => '1',
                'condition' => 'accordion_type=metabox&&metabox_data_source=user'
            )
        )->setParam('dynamicdatacode', '<div optionname="\'oxy-pro-accordion_metabox_user_id\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');
        

        
        $dynamic_section->addOptionControl(
            array(
                'type' => 'textfield',
                'name' => __('Header text'),
                'slug' => 'title_text',
                'condition' => 'accordion_type=manual',
                'default' => 'Header text',
                "base64" => true
            )
        )->setParam('dynamicdatacode', '<div optionname="\'oxy-pro-accordion_title_text\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');
        
        
        $dynamic_section->addOptionControl(
            array(
                'type' => 'textfield',
                'name' => __('Subtext (Leave blank to remove)'),
                'slug' => 'subtitle_text',
                'default' => '',
                'condition' => 'accordion_type=manual',
                "base64" => true
            )
        )->setParam('dynamicdatacode', '<div optionname="\'oxy-pro-accordion_subtitle_text\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');
        
        
        // ACF Repeater
        $dynamic_section->addOptionControl(
            array(
                'type' => 'textfield',
                'name' => __('Post ID'),
                'slug' => 'custom_post_id',
                'condition' => 'accordion_type=acf&&acf_post_id=custom'
            )
        )->setParam('dynamicdatacode', '<div optionname="\'oxy-pro-accordion_custom_post_id\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');

        $dynamic_section->addOptionControl(
            array(
                'type' => 'textfield',
                'name' => __('Cloneable group field'),
                'slug' => 'group_field',
                'condition' => 'accordion_type=metabox'
            )
        );
        
        $dynamic_section->addOptionControl(
            array(
                'type' => 'textfield',
                'name' => __('ACF repeater field'),
                'slug' => 'repeater_field',
                'condition' => 'accordion_type=acf'
            )
        );
        
        $dynamic_section->addOptionControl(
            array(
                'type' => 'textfield',
                'name' => __('Header text field'),
                'slug' => 'title_field',
                'condition' => 'accordion_type=acf||accordion_type=metabox'
            )
        );
        
        $dynamic_section->addOptionControl(
            array(
                'type' => 'textfield',
                'name' => __('Subtext field'),
                'slug' => 'subtitle_field',
                'condition' => 'accordion_type=acf||accordion_type=metabox'
            )
        );
        
        $dynamic_section->addOptionControl(
            array(
                'type' => 'textfield',
                'name' => __('Content field'),
                'slug' => 'content_field',
                'condition' => 'accordion_type=acf||accordion_type=metabox'
            )
        );

        $dynamic_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Format & run shortcodes'),
                'slug' => 'maybe_wpautop',
                'condition' => 'accordion_type=metabox'
            )
            
        )->setValue(array( 
            "true" => "True",
            "false" => "False",
            )
        )->setDefaultValue('false')
         ->setParam("description", __("Applies wpautop (for WYSIWYG content fields)"));




        
        
        
        
        
        $header_section->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Header text tag'),
                'slug' => 'title_tag',
            )
            
        )->setValue(array( 
            "h1",
            "h2",
            "h3",
            "h4",
            "h5",
            "h6",
            "div",
            "span",
            "p"
            )
        )->setDefaultValue('h4');
        
        $header_section->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Subtext tag'),
                'slug' => 'subtitle_tag',
            )
            
        )->setValue(array( 
            "h1",
            "h2",
            "h3",
            "h4",
            "h5",
            "h6",
            "div",
            "span",
            )
        )->setDefaultValue('span');
        
        $header_color_section = $header_section->addControlSection("header_color_section", __("Header Colors"), "assets/icon.png", $this);
        
        $header_color_section->addStyleControls(
            array(
                array(
                   "property" => 'background-color',
                    "selector" => $header_selector,
                ),
                array(
                    "name" => 'Hover Background Color',
                    "property" => 'background-color',
                    "selector" => $header_selector.":hover",
                ),
                array(
                    "name" => 'Focus Background Color',
                    "property" => 'background-color',
                    "selector" => $header_selector.":focus",
                ),
                array(
                    "name" => 'Active Background Color',
                    "property" => 'background-color',
                    "selector" => '.oxy-pro-accordion_item.active .oxy-pro-accordion_header',
                ),
                array(
                    "property" => 'color',
                    "selector" => $header_selector,
                ),
                array(
                    "name" => 'Hover Color',
                    "property" => 'color',
                    "selector" => $header_selector.":hover",
                ),
                array(
                    "name" => 'Focus Color',
                    "property" => 'color',
                    "selector" => $header_selector.":focus",
                ),
                array(
                    "name" => 'Active Color',
                    "property" => 'color',
                    "selector" => '.oxy-pro-accordion_item.active .oxy-pro-accordion_header',
                ),
            )
        );
        
        
        $header_spacing_section = $header_section->addControlSection("header_spacing_section", __("Header Spacing"), "assets/icon.png", $this);
        
        $header_spacing_section->addPreset(
            "padding",
            "header_padding",
            __("Padding"),
            $header_selector
        )->whiteList();
        
        $header_spacing_section->addPreset(
            "margin",
            "header_margin",
            __("Margin"),
            $header_selector
        )->whiteList();
        
        
        $header_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Flex Direction'),
                'slug' => 'header_flex_direction',
            )
            
        )->setValue(array( 
            "normal" => "Normal",
            "reversed" => "Reversed",
            )
        )->setDefaultValue('normal')
         ->setValueCSS( array(
            "reversed"  => " .oxy-pro-accordion_header {
                                flex-direction: row-reverse;
                            }",
                
        ) )->whiteList();
        
       
        
    
        
        
        
        $title_spacing_section = $header_section->addControlSection("title_spacing_section", __("Title Area Spacing"), "assets/icon.png", $this);
        $title_area_selector = '.oxy-pro-accordion_title-area';
        
        
        $title_spacing_section->addStyleControl(
                array(
                    "selector" => $title_area_selector,
                    "control_type" => 'measurebox',
                    "value" => '10',
                    "property" => 'padding-top',
                )
        )->setUnits('px')
         ->setParam('hide_wrapper_end', true);
        
        $title_spacing_section->addStyleControl(
                array(
                    "selector" => $title_area_selector,
                    "control_type" => 'measurebox',
                    "value" => '10',
                    "property" => 'padding-bottom',
                )
        )->setUnits('px')
         ->setParam('hide_wrapper_start', true);
        
        $title_spacing_section->addStyleControl(
                array(
                   "selector" => $title_area_selector,
                    "control_type" => 'measurebox',
                    "value" => '20',
                    "property" => 'padding-left',
                )
        )->setUnits('px')
         ->setParam('hide_wrapper_end', true);
        
        $title_spacing_section->addStyleControl(
                array(
                    "selector" => $title_area_selector,
                    "control_type" => 'measurebox',
                    "value" => '20',
                    "property" => 'padding-right',
                )
        )->setUnits('px')
         ->setParam('hide_wrapper_start', true);
        
        
        
        $header_section->typographySection('Header text Typography', '.oxy-pro-accordion_title',$this);
        $header_section->typographySection('Subtext Typography', '.oxy-pro-accordion_subtitle',$this);
        $header_section->borderSection('Borders', $header_selector,$this);
        
        
        $header_section->boxShadowSection('Box Shadow', $header_selector,$this);
        
        
        
        /**
         * Item
         */ 
        $item_section = $this->addControlSection("item_section", __("Accordion Item"), "assets/icon.png", $this);
        $item_selector = '.oxy-pro-accordion_item';
        
        
        $item_section->addStyleControl(
                array(
                    "selector" => $item_selector,
                    "control_type" => 'measurebox',
                    "value" => '0',
                    "property" => 'margin-top',
                )
        )->setUnits('px')->setParam('hide_wrapper_end', true);
        
        $item_section->addStyleControl(
                array(
                    "selector" => $item_selector,
                    "control_type" => 'measurebox',
                    "value" => '0',
                    "property" => 'margin-bottom',
                )
        )->setUnits('px')
         ->setParam('hide_wrapper_start', true);
        
        $item_section->addStyleControl(
                array(
                   "selector" => $item_selector,
                    "control_type" => 'measurebox',
                    "value" => '0',
                    "property" => 'margin-left',
                )
        )->setUnits('px')
         ->setParam('hide_wrapper_end', true);
        
        $item_section->addStyleControl(
                array(
                    "selector" => $item_selector,
                    "control_type" => 'measurebox',
                    "value" => '0',
                    "property" => 'margin-right',
                )
        )->setUnits('px')
         ->setParam('hide_wrapper_start', true);
        
        
        $item_color_section = $item_section->addControlSection("item_color_section", __("Colors"), "assets/icon.png", $this);
        
        $item_color_section->addStyleControls(
            array(
                array(
                   "property" => 'background-color',
                    "selector" => $item_selector,
                ),
                array(
                    "name" => 'Hover Background Color',
                    "property" => 'background-color',
                    "selector" => $item_selector.":hover",
                ),
                array(
                    "name" => 'Active Background Color',
                    "property" => 'background-color',
                    "selector" => $item_selector.".active",
                ),
                array(
                    "property" => 'color',
                    "selector" => $item_selector,
                ),
                array(
                    "name" => 'Hover Color',
                    "property" => 'color',
                    "selector" => $item_selector.":hover",
                ),
                array(
                    "name" => 'Active Color',
                    "property" => 'color',
                    "selector" => $item_selector.".active",
                ),
            )
        );
        
        
        $item_section->borderSection('Borders', $item_selector,$this);
        
        $item_section->boxShadowSection('Box Shadow', $item_selector,$this);
        
        $item_section->boxShadowSection('Active Box Shadow', $item_selector.".active",$this);
        
        
        
        
        
        /**
         * Content
         */ 
        $content_section = $this->addControlSection("content_section", __("Accordion Content"), "assets/icon.png", $this);
        
        $content_selector = '.oxy-pro-accordion_content';
        
        
        
        
        
        $content_section->addStyleControls(
            array(
                array(
                   "property" => 'background-color',
                    "selector" => '.oxy-pro-accordion_body',
                ),
                array(
                    "name" => 'Text color',
                    "property" => 'color',
                    "selector" => '.oxy-pro-accordion_body',
                ),
            )
        );
        
        $content_section->addStyleControl(
                array(
                    "selector" => $content_selector,
                    "control_type" => 'measurebox',
                    "value" => '30',
                    "property" => 'padding-top',
                    "condition" => 'icon_display=enable'
                )
        )->setUnits('px')->setParam('hide_wrapper_end', true);
        
        $content_section->addStyleControl(
                array(
                    "selector" => $content_selector,
                    "control_type" => 'measurebox',
                    "value" => '30',
                    "property" => 'padding-bottom',
                    "condition" => 'icon_display=enable'
                )
        )->setUnits('px')
         ->setParam('hide_wrapper_start', true);
        
        $content_section->addStyleControl(
                array(
                   "selector" => $content_selector,
                    "control_type" => 'measurebox',
                    "value" => '30',
                    "property" => 'padding-left',
                    "condition" => 'icon_display=enable'
                )
        )->setUnits('px')
         ->setParam('hide_wrapper_end', true);
        
        $content_section->addStyleControl(
                array(
                    "selector" => $content_selector,
                    "control_type" => 'measurebox',
                    "value" => '30',
                    "property" => 'padding-right',
                    "condition" => 'icon_display=enable'
                )
        )->setUnits('px')
         ->setParam('hide_wrapper_start', true);
    
        
        $content_section->addStyleControl(
            array(
                "name" => __('Closed content opacity'),
                "property" => 'opacity',
                "selector" => '.oxy-pro-accordion_content',
            )
        );
        
        
        
        
        /**
         * Icons
         */ 
        $icon_selector = '.oxy-pro-accordion_icon';
        
        
        $icons_toggle_section = $this->addControlSection("icons_toggle_section", __("Toggle Icon"), "assets/icon.png", $this);
        
        $icons_toggle_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Toggle icon display'),
                'slug' => 'icon_display',
            )
            
        )->setValue(array( 
            "enable" => "Enable",
            "disable" => "Disable",
            )
        )->setDefaultValue('enable')
         ->setValueCSS( array(
            "disable"  => " .oxy-pro-accordion_icon {
                                display: none;
                            }",
                
        ) );  
        
        
         
        
        $icons_toggle_section->addStyleControls(
            array(
                array(
                    "name" => __('Icon Size'),
                    "selector" => $icon_selector,
                    "property" => 'font-size',
                    "condition" => 'icon_display=enable',
                    "default" => '18'
                ),
            )
        );
        
        
        
        $icons_toggle_section->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Rotate'),
                'slug' => 'icon_toggle_rotate',
                'condition' => 'icon_display=enable'
            )
            
        )->setValue(array( 
            "rotatex" => "Vertical flip",
            "rotatey" => "Horizontal flip",
            "rotate" => "Rotate",
            "none" => "None",
            )
        )->setDefaultValue('rotatex')
         ->setValueCSS( array(
            "rotatex"  => " .oxy-pro-accordion_item.active .oxy-pro-accordion_icon svg {
                                transform: rotateX(180deg);
                                -webkit-transform: rotateX(180deg);
                            }",
             "rotatey"  => " .oxy-pro-accordion_item.active .oxy-pro-accordion_icon svg {
                                transform: rotateY(180deg);
                                -webkit-transform: rotateY(180deg);
                            }",
             "rotate"  => " .oxy-pro-accordion_item.active .oxy-pro-accordion_icon svg {
                                transform: rotate(var(--extras-icon-rotate));
                                -webkit-transform: rotate(var(--extras-icon-rotate));
                            }",
             "none"  => " .oxy-pro-accordion_item.active .oxy-pro-accordion_icon svg {
                                transform: none;
                                -webkit-transform: none;
                            }",               
                
        ) );  
        
        
        $icons_toggle_section->addStyleControl(
            array(
                "name" => __('Rotation amount'),
                "selector" => '.oxy-pro-accordion_icon',
                "property" => '--extras-icon-rotate',
                "control_type" => 'slider-measurebox',
                'condition' => 'toggle_expanded=animate&&icon_toggle_rotate=rotate'
            )
        )
        ->setUnits('deg','deg')
        ->setRange('-180','180');
        
        
        
        
        
        $icons_toggle_icon_section = $icons_toggle_section->addControlSection("icons_toggle_icon_section", __("Change Icon(s)"), "assets/icon.png", $this);
        
        $icons_toggle_icon_section->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Toggle icon when expanded'),
                'slug' => 'toggle_expanded',
                "condition" => 'icon_display=enable'
            )
            
        )->setValue(array( 
            "switch" => "Switch icons",
            "animate" => "Same icon",
            )
        )->setDefaultValue('animate');
        
            
        $icons_toggle_icon_section->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Toggle Icon'),
                "slug" => 'toggle_icon',
                "default" => 'FontAwesomeicon-angle-down',
                "condition" => 'icon_display=enable'
            )
        );
        
        $icons_toggle_icon_section->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Toggle Close Icon'),
                "slug" => 'toggle_close_icon',
                "default" => 'FontAwesomeicon-angle-up',
                "condition" => 'icon_display=enable&&toggle_expanded=switch'
            )
        );
       
        
        
        
        $icons_toggle_color_section = $icons_toggle_section->addControlSection("icons_toggle_color_section", __("Colors"), "assets/icon.png", $this);
        
        $icons_toggle_color_section->addStyleControls(
            array(
                array(
                   "property" => 'background-color',
                    "selector" => $icon_selector,
                ),
                array(
                    "name" => 'Hover Background Color',
                    "property" => 'background-color',
                    "selector" => ".oxy-pro-accordion_header:hover " .$icon_selector,
                ),
                array(
                    "name" => 'Active Background Color',
                    "property" => 'background-color',
                    "selector" => ".active " .$icon_selector,
                ),
                array(
                    "property" => 'color',
                    "selector" => $icon_selector,
                ),
                array(
                    "name" => 'Hover Color',
                    "property" => 'color',
                    "selector" => ".oxy-pro-accordion_header:hover " .$icon_selector,
                ),
                array(
                    "name" => 'Active Color',
                    "property" => 'color',
                    "selector" => ".active " .$icon_selector,
                ),
            )
        );
        
        
        $icons_toggle_section->borderSection('Borders', $icon_selector,$this);
        $icons_toggle_section->boxShadowSection('Box Shadow', $icon_selector,$this);
        
        
        $icons_toggle_spacing_section = $icons_toggle_section->addControlSection("icons_toggle_spacing_section", __("Spacing"), "assets/icon.png", $this);
        
        $icons_toggle_spacing_section->addStyleControl(
                array(
                    "selector" => $icon_selector,
                    "control_type" => 'measurebox',
                    "value" => '20',
                    "property" => 'padding-top',
                    "condition" => 'icon_display=enable'
                )
        )->setUnits('px')->setParam('hide_wrapper_end', true);
        
        $icons_toggle_spacing_section->addStyleControl(
                array(
                    "selector" => $icon_selector,
                    "control_type" => 'measurebox',
                    "value" => '20',
                    "property" => 'padding-bottom',
                    "condition" => 'icon_display=enable'
                )
        )->setUnits('px')
         ->setParam('hide_wrapper_start', true);
        
        $icons_toggle_spacing_section->addStyleControl(
                array(
                   "selector" => $icon_selector,
                    "control_type" => 'measurebox',
                    "value" => '20',
                    "property" => 'padding-left',
                    "condition" => 'icon_display=enable'
                )
        )->setUnits('px')
         ->setParam('hide_wrapper_end', true);
        
        $icons_toggle_spacing_section->addStyleControl(
                array(
                    "selector" => $icon_selector,
                    "control_type" => 'measurebox',
                    "value" => '20',
                    "property" => 'padding-right',
                    "condition" => 'icon_display=enable'
                )
        )->setUnits('px')
         ->setParam('hide_wrapper_start', true);
        
        //$icons_toggle_animation_section = $icons_toggle_section->addControlSection("icons_toggle_animation_section", __("Animation"), "assets/icon.png", $this);
        
        
        
        
        
       
        
        
        
        $icons_context_section = $this->addControlSection("icons_context_section", __("Context Icon / Counter"), "assets/icon.png", $this);
        
        $context_icon_selector = '.oxy-pro-accordion_context-icon'; 
        
        $icons_context_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Context display'),
                'slug' => 'context_icon_display',
            )
            
        )->setValue(array( 
            "enable" => "Enable",
            "disable" => "disable",
            )
        )->setDefaultValue('enable')
         ->setValueCSS( array(
            "disable"  => " .oxy-pro-accordion_context-icon {
                                display: none;
                            }",
                
        ) );  
        
        
        $icons_context_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Context type'),
                'slug' => 'context_type',
            )
            
        )->setValue(array( 
            "icon" => "Icon",
            "numbers" => "Counter",
            )
        )->setValueCSS( array(
            "numbers"  => " .oxy-pro-accordion_context-icon svg {
                                display: none;
                            }
                            
                            .oxy-pro-accordion_context-icon::before {
                                content: counter(extras_accordion_items, var(--extras-counter-type));
                            }",
                
        ) ); 


        $icons_context_section->typographySection('Counter typography', '.oxy-pro-accordion_header .oxy-pro-accordion_context-icon',$this);
        
                
        $icons_context_section->addStyleControls(
            array(
                array(
                    "name" => __('Size'),
                    "selector" => $context_icon_selector,
                    "property" => 'font-size',
                    "condition" => 'context_type=fake_input',
                    "default" => '18'
                ),
            )
        );
        
        $icons_context_section->addStyleControls(
            array(
                array(
                    "selector" => $context_icon_selector,
                    "property" => 'font-weight',
                    "condition" => 'context_type=fake_input',
                ),
                array(
                    "selector" => $context_icon_selector,
                    "property" => 'font-family',
                    "condition" => 'context_type=fake_input',
                ),
            )
        );
        
        
        /**
         * List Style Type
         */
        $icons_context_section->addOptionControl(
            array(
                "type" => "dropdown",
                "name" => "List Style Type",
                "slug" => "list_style_type",
                "default" => 'decimal',
                "condition" => 'context_icon_display=enable&&context_type=numbers'
            )
        )->setValue(
           array( 
                "decimal" => "decimal (1, 2, 3)",
                "decimal-leading-zero" => "decimal-leading-zero (01, 02, 03)",
                "lower-roman" => "lower-roman (i ii iii)",
                "upper-roman" => "upper-roman (I II III)",
                "lower-alpha" => "lower-alpha (a b c)",
                "upper-alpha" => "upper-alpha (A B C)",
                "none",
           )
        )->setValueCSS( array(
             "decimal"  => " .oxy-pro-accordion_item {
                            --extras-counter-type: decimal;
                          }",
             "decimal-leading-zero"  => " .oxy-pro-accordion_item {
                            --extras-counter-type: decimal-leading-zero;
                          }",
             "lower-roman"  => " .oxy-pro-accordion_item {
                            --extras-counter-type: lower-roman;
                          } ",
             "upper-roman"  => " .oxy-pro-accordion_item {
                            --extras-counter-type: upper-roman;
                          }",
             "lower-greek"  => " .oxy-pro-accordion_item {
                            --extras-counter-type: lower-greek;
                          }",
             "armenian"  => " .oxy-pro-accordion_item {
                            --extras-counter-type: armenian;
                          }",
             "georgian"  => " .oxy-pro-accordion_item {
                            --extras-counter-type: georgian;
                          }",
             "lower-alpha"  => " .oxy-pro-accordion_item {
                            --extras-counter-type: lower-alpha;
                          }",
             "armenian"  => " .oxy-pro-accordion_item {
                            --extras-counter-type: armenian;
                          }",
             "upper-alpha"  => " .oxy-pro-accordion_item {
                            --extras-counter-type: upper-alpha;
                          }",
            "none"  => " .oxy-pro-accordion_item {
                            --extras-counter-type: none;
                          }",
            
        ) );   
        
            
        $icons_context_section->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Toggle arrow icon'),
                "slug" => 'context_icon',
                "default" => 'FontAwesomeicon-question-circle-o',
                "condition" => 'context_icon_display=enable&&context_type=icon'
            )
        );
       
        
        $icons_context_spacing_section = $icons_context_section->addControlSection("icons_context_spacing_section", __("Size & Spacing"), "assets/icon.png", $this);
        
        $icons_context_spacing_section->addStyleControl(
                array(
                    "selector" => $context_icon_selector,
                    "control_type" => 'measurebox',
                    "value" => '20',
                    "property" => 'padding-top',
                    "condition" => 'context_icon_display=enable',
                )
        )->setUnits('px')->setParam('hide_wrapper_end', true);
        
        $icons_context_spacing_section->addStyleControl(
                array(
                    "selector" => $context_icon_selector,
                    "control_type" => 'measurebox',
                    "value" => '20',
                    "property" => 'padding-bottom',
                    "condition" => 'context_icon_display=enable',
                )
        )->setUnits('px')
         ->setParam('hide_wrapper_start', true);
        
        $icons_context_spacing_section->addStyleControl(
                array(
                   "selector" => $context_icon_selector,
                    "control_type" => 'measurebox',
                    "value" => '20',
                    "property" => 'padding-left',
                    "condition" => 'context_icon_display=enable',
                )
        )->setUnits('px')
         ->setParam('hide_wrapper_end', true);
        
        $icons_context_spacing_section->addStyleControl(
                array(
                    "selector" => $context_icon_selector,
                    "control_type" => 'measurebox',
                    "value" => '20',
                    "property" => 'padding-right',
                    "condition" => 'context_icon_display=enable',
                )
        )->setUnits('px')
         ->setParam('hide_wrapper_start', true);
        
        
        
        
        $icons_context_spacing_section->addStyleControl(
                array(
                    "selector" => $context_icon_selector,
                    "control_type" => 'measurebox',
                    "value" => '0',
                    "property" => 'margin-top',
                    "condition" => 'context_icon_display=enable',
                )
        )->setUnits('px')->setParam('hide_wrapper_end', true);
        
        $icons_context_spacing_section->addStyleControl(
                array(
                    "selector" => $context_icon_selector,
                    "control_type" => 'measurebox',
                    "value" => '0',
                    "property" => 'margin-bottom',
                    "condition" => 'context_icon_display=enable',
                )
        )->setUnits('px')
         ->setParam('hide_wrapper_start', true);
        
        $icons_context_spacing_section->addStyleControl(
                array(
                   "selector" => $context_icon_selector,
                    "control_type" => 'measurebox',
                    "value" => '0',
                    "property" => 'margin-left',
                    "condition" => 'context_icon_display=enable',
                )
        )->setUnits('px')
         ->setParam('hide_wrapper_end', true);
        
        $icons_context_spacing_section->addStyleControl(
                array(
                    "selector" => $context_icon_selector,
                    "control_type" => 'measurebox',
                    "value" => '0',
                    "property" => 'margin-right',
                    "condition" => 'context_icon_display=enable',
                )
        )->setUnits('px')
         ->setParam('hide_wrapper_start', true);
        
        
        $icons_context_spacing_section->addStyleControl(
                array(
                    "selector" => $context_icon_selector,
                    "control_type" => 'measurebox',
                    "property" => 'width',
                    "condition" => 'context_icon_display=enable',
                )
        )->setUnits('px');
        
        $icons_context_spacing_section->addStyleControl(
                array(
                    "selector" => $context_icon_selector,
                    "control_type" => 'measurebox',
                    "property" => 'height',
                    "condition" => 'context_icon_display=enable',
                )
        )->setUnits('px');
        
        
        
        
        $icons_context_color_section = $icons_context_section->addControlSection("icons_context_color_section", __("Colors"), "assets/icon.png", $this);
        
        
        $icons_context_color_section->addStyleControls(
            array(
                array(
                   "property" => 'background-color',
                    "selector" => $context_icon_selector,
                    "default" => 'rgba(255,255,255,0.35)'
                ),
                array(
                    "name" => 'Hover Background Color',
                    "property" => 'background-color',
                    "selector" => ".oxy-pro-accordion_header:hover " .$context_icon_selector,
                ),
                array(
                    "name" => 'Active Background Color',
                    "property" => 'background-color',
                    "selector" => ".active " .$context_icon_selector,
                ),
                array(
                    "property" => 'color',
                    "selector" => $context_icon_selector,
                ),
                array(
                    "name" => 'Hover Color',
                    "property" => 'color',
                    "selector" => ".oxy-pro-accordion_header:hover " .$context_icon_selector,
                ),
                array(
                    "name" => 'Active Color',
                    "property" => 'color',
                    "selector" => ".active " .$context_icon_selector,
                ),
            )
        );
        
        
        $icons_context_section->borderSection('Borders', $context_icon_selector,$this);
        $icons_context_section->boxShadowSection('Box Shadow', $context_icon_selector,$this);
        
        
        
        
        /**
         * Adv
         */ 
        $advanced_section = $this->addControlSection("advanced_section", __("Advanced"), "assets/icon.png", $this);
        
        $advanced_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('FAQ schema markup'),
                'slug' => 'maybe_faq_schema',
            )
            
        )->setValue(array( 
            "enable" => "Enable",
            "disable" => "Disable",
            )
        )->setDefaultValue('disable');
        
        $advanced_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Overflow anchoring'),
                'slug' => 'overflow_anchor',
            )
            
        )->setValue(array( 
            "enable" => "Enable",
            "disable" => "disable",
            )
        )->setDefaultValue('disable')
         ->setValueCSS( array(
            "enable"  => " {
                                overflow-anchor: auto;
                            }",
                
        ) )->setParam("description", __("Enable only if large elements inside accordion where page needs to scroll when opened"));
        
        $advanced_section->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Close other accordion items'),
                'slug' => 'maybe_close_others',
            )
            
        )->setValue(array( 
            "enable" => __("Close sibling items"),
            "disable" => __("Disable"),
            "container" => __("Close all items in container"),
            )
        )->setDefaultValue('enable');


        $advanced_section->addOptionControl(
            array(
                'type' => 'textfield',
                'name' => __('Container selector'),
                'slug' => 'container_selector',
                'default' => '.container-class',
                'condition' => 'maybe_close_others=container'
            )
        );
        
        
        $advanced_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Using individual items inside repeater'),
                'slug' => 'maybe_repeater',
            )
            
        )->setValue(array( 
            "true" => "Enable",
            "false" => "Disable",
            )
        )->setDefaultValue('disable')
         ->setParam("description", __("Enable if using items inside of a repeater to create one large accordion"));


         $advanced_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('First item open'),
                'slug' => 'first_open_repeater',
                'condition' => 'maybe_repeater=true'
            )
            
        )->setValue(array( 
            "open" => "True",
            "closed" => "False",
            )
        )->setDefaultValue('closed'); 
        
        
        $advanced_section->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Accordion item tags'),
                'slug' => 'item_tags',
                'condition' => 'accordion_type!=manual'
            )
            
        )->setValue(array( 
            "div" => "div > div",
            "list" => "ul > li",
            "number" => "ol > li",
            )
        )->setDefaultValue('div')
         ->rebuildElementOnChange();
        
        
    }
    
    function defaultCSS() {
        
        $css = file_get_contents( plugin_dir_path(__FILE__) . 'assets/accordion.css' );
        
        return $css;
        
    }
    
    function customCSS($options, $selector) {
        
        $css = "$selector .active .oxy-pro-accordion_content {
                    opacity: 1;
                }
                
                .oxygen-builder-body .oxy-pro-accordion_body,
                .oxygen-builder-body .oxy-pro-accordion_item[data-init=open] .oxy-pro-accordion_body,
                .oxy-pro-accordion.oxygenberg-element .oxy-pro-accordion_body,
                .oxy-pro-accordion.oxygenberg-element .oxy-pro-accordion_item[data-init=open] .oxy-pro-accordion_body {
                    --extras-hide-accordion: block;
                    display: var(--extras-hide-accordion)!important;
                }
                ";
        
        return $css;
        
    } 
    
    
    function output_js() { ?>
        <script type="text/javascript">
            jQuery(document).ready(oxygen_init_accordion);
            function oxygen_init_accordion($) {
                
                let touchEvent = 'click';  

                let extrasAccordion = function ( container ) {
                    
                $(container).find('.oxy-pro-accordion').each(function(){
                    
                    var $accordion = $(this);
                    var disable_sibling = $accordion.find('.oxy-pro-accordion_inner').data('disablesibling');

                    if ( 'manual' === $(this).find('.oxy-pro-accordion_inner').data('type') ) {

                        
                        var $accordion_header = $accordion.find('.oxy-pro-accordion_header');
                        var $accordion_item = $accordion.find('.oxy-pro-accordion_item');
                        var $accordion_body = $accordion.find('.oxy-pro-accordion_body');
                        var $speed = $accordion.find('.oxy-pro-accordion_inner').data('expand');
                        var mediaPlayer = $accordion.parent().children('.oxy-pro-accordion').find('.oxy-pro-media-player vime-player');
                        var accordionID = '#' + $accordion.attr('id');

                        var repeaterFirst = $accordion.find('.oxy-pro-accordion_inner').data('repeater-first')

                        
                        
                        if (true === $accordion.find('.oxy-pro-accordion_inner').data('repeater')) {
                            $accordion.closest('.oxy-dynamic-list').children('.ct-div-block').attr('data-counter', 'true');
                            $accordion.attr('data-counter', 'false');

                            if ( repeaterFirst ) {

                                $accordion.closest('.oxy-dynamic-list > .ct-div-block:first-child').find('.oxy-pro-accordion_item').addClass('active')
                                $accordion.closest('.oxy-dynamic-list > .ct-div-block:first-child').find('.oxy-pro-accordion_item').attr('data-init', 'open')
                                $accordion.closest('.oxy-dynamic-list > .ct-div-block:first-child').find('.oxy-pro-accordion_header').attr('aria-expanded', 'true')
                            }
                        }
                        
                        $accordion_header.on(touchEvent, function() {

                            $accordion_item.toggleClass('active');
                            $accordion_body.slideToggle($speed);
                            $accordion.trigger('extras_pro_accordion:toggle');
                            $accordion_header.attr('aria-expanded', function (i, attr) {
                                                        return attr == 'true' ? 'false' : 'true'
                                                    });
                                                    
                            if (true !== disable_sibling) {

                                /* Sibling */
                                if (false === disable_sibling) {

                                    if (!$accordion.siblings('.oxy-pro-accordion').length && ($accordion.closest('.oxy-dynamic-list').length) ) {
                                        $accordion_item_active_sibling = $accordion.closest('.oxy-dynamic-list > .ct-div-block').siblings('.ct-div-block').find('.oxy-pro-accordion').children('.oxy-pro-accordion_inner[data-type=manual]').children('.oxy-pro-accordion_item.active')
                                    } else {
                                        $accordion_item_active_sibling = $accordion.siblings('.oxy-pro-accordion').children('.oxy-pro-accordion_inner[data-type=manual]').children('.oxy-pro-accordion_item.active');
                                    }

                                } else {  /* Container */
                                    $accordion_item_active_sibling = $(disable_sibling).find('.oxy-pro-accordion').not(accordionID).children('.oxy-pro-accordion_inner[data-type=manual]').children('.oxy-pro-accordion_item.active');
                                }    
                                    
                                $accordion_item_active_sibling.find('.oxy-pro-accordion_body').slideUp($speed);
                                $accordion_item_active_sibling.find('.oxy-pro-accordion_header').attr('aria-expanded', function (i, attr) {
                                                            return attr == 'true' ? 'false' : 'true'
                                                        });

                                $accordion_item_active_sibling.removeClass('active');

                            }

                            $accordion.trigger('extras_pro_accordion:toggle');
                            
                            mediaPlayer.each(function() {
                                $(this)[0].pause();
                            });
                            
                        });
                        
                    } else {
                        
                        var $accordion_item = $accordion.find('.oxy-pro-accordion_item');
                        var $accordion_item_first = $accordion_item.first();
                        var $accordion_first_open = $accordion.children('.oxy-pro-accordion_inner').data('acf');
                        var $speed = $accordion.find('.oxy-pro-accordion_inner').data('expand');
                        
                        
                        if ( 'closed' !== $accordion_first_open ) {
                            
                            $accordion_item_first.addClass('active');
                            $accordion_item_first.children('.oxy-pro-accordion_body').show();
                            $accordion_item_first.children('.oxy-pro-accordion_header').attr('aria-expanded', 'true');
                        }
                        
                        $accordion_item.each(function(){
                            
                            var $item = $(this);
                            var $accordion_header = $item.find('.oxy-pro-accordion_header');
                            var $accordion_body = $item.find('.oxy-pro-accordion_body');
                            
                            $accordion_header.on(touchEvent, function() {
                            
                                $item.toggleClass('active');
                                $accordion_body.slideToggle($speed);
                                $accordion_header.attr('aria-expanded', function (i, attr) {
                                                        return attr == 'true' ? 'false' : 'true'
                                                    });

                                if (true !== disable_sibling) {
                                    $item.siblings('.oxy-pro-accordion_item.active').find('.oxy-pro-accordion_body').slideUp($speed);
                                    $item.siblings('.oxy-pro-accordion_item.active').removeClass('active');
                                    $item.siblings('.oxy-pro-accordion_item').find('.oxy-pro-accordion_header').attr('aria-expanded', 'false');
                                }

                            });
                            
                        });
                        
                        
                    }


                    var $accordionHeaders = $accordion.find('.oxy-pro-accordion_header');
                    var $accordionItems = $accordion.find('.oxy-pro-accordion_item');

                    $accordionHeaders.each(function( index, accordionHeader ) {

                        $accordion_header = $(accordionHeader);

                        $accordion_header.on( "keydown", (e) => {

                            if ('ArrowDown' === e.code ) {
                                e.preventDefault()

                                if ( $accordionItems[(index + 1)] ) {
                                    $($accordionItems[(index + 1)]).find('.oxy-pro-accordion_header').focus()
                                } else {
                                    $($accordionItems[0]).find('.oxy-pro-accordion_header').focus()
                                }

                            } else if ( 'ArrowUp' === e.code ) {
                                e.preventDefault()
                                if ( $accordionItems[(index - 1)] ) {
                                    $($accordionItems[(index - 1)]).find('.oxy-pro-accordion_header').focus()
                                } else {
                                    $($accordionItems[$accordionItems.length - 1]).find('.oxy-pro-accordion_header').focus()
                                }
                            } else if ( 'Home' === e.code ) {
                                e.preventDefault()
                                $($accordionItems[0]).find('.oxy-pro-accordion_header').focus()

                            } else if ( 'End' === e.code ) {
                                e.preventDefault()
                                $($accordionItems[$accordionItems.length - 1]).find('.oxy-pro-accordion_header').focus()
                            }
                        } );
                    })
                    
                });

                

                }
                
                extrasAccordion('body');
                
                // Expose function
                window.doExtrasAccordion = extrasAccordion;
                    
            };

        </script>    

    <?php }
    
    
    function allowedEmptyOptions($options) {

        $options_to_add = array(
            "oxy-pro-accordion_title_field",
            "oxy-pro-accordion_subtitle_field",
            "oxy-pro-accordion_content_field",
            "oxy-pro-accordion_title_text",
            "oxy-pro-accordion_subtitle_text",
        );

        $options = array_merge($options, $options_to_add);

        return $options;
    } 
    

}

add_filter("oxy_base64_encode_options", 
    function($items) { 
        $items=array_merge($items, array('oxy-pro-accordion_title_text','oxy-pro-accordion_subtitle_text','oxy-pro-accordion_custom_post_id','oxy-pro-accordion_metabox_post_id','oxy-pro-accordion_metabox_user_id')); 
        return $items;
    }
);

new ExtraAccordion();