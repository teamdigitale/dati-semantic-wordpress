<?php

class ExtraProLogin extends OxygenExtraElements {

	function name() {
        return __('Extras Login Form'); 
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
        
        add_filter("oxy_allowed_empty_options_list", array( $this, "allowedEmptyOptions") );
    }
    
    function extras_button_place() {
        return "wordpress";
    }
    
    
    function render($options, $defaults, $content) {
        
        // get options 
        $label_username  = isset( $options['label_username'] ) ? esc_attr($options['label_username']) : '';
        $label_password = isset( $options['label_password'] ) ? esc_attr($options['label_password']) : '';
        $label_remember = isset( $options['label_remember'] ) ? esc_attr($options['label_remember']) : '';
        $label_login = esc_attr($options['label_login']);
        $redirect_url = isset( $options['redirect_url'] ) ? esc_url($options['redirect_url']) : get_home_url();
        $redirect = esc_attr($options['redirect']) === 'false' ? ( is_ssl() ? 'https://' : 'http://' ) . sanitize_text_field( $_SERVER['HTTP_HOST'] ) . sanitize_text_field( $_SERVER['REQUEST_URI'] ) : $redirect_url;
        
        
        $args = array(
            'echo'           => true,
            'redirect'       => $redirect,
            'form_id'        => 'loginform',
            'label_username' => __( $label_username ),
            'label_password' => __( $label_password ),
            'label_remember' => __( $label_remember ),
            'label_log_in'   => __( $label_login ),
        );
        
        wp_login_form($args);
        
    }

    function class_names() {
        return array();
    }

    function controls() {
        
        // Login redirect?
        $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Redirect After Login',
                'slug' => 'redirect')
            
        )->setValue(array( "false" => "No Redirect", "true" => "Custom URL" ))
         ->setDefaultValue('false');
        
        $default_home = get_home_url();
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Redirect URL'),
                "slug" => 'redirect_url',
                "default" => $default_home,
                "condition" => 'redirect=true'
            )
        );
        
        /**
         * Labels
         */

        $label_section = $this->addControlSection("label_section", __("Labels"), "assets/icon.png", $this);
        $label_selector = 'label';
        
        //$label_text = $label_section->addControlSection("label_text", __("Label Text"), "assets/icon.png", $this);
        
        $label_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Username'),
                "slug" => 'label_username',
                "default" => 'Username or Address',
                "base64" => true
            )
        )->rebuildElementOnChange();
        
        $label_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Password'),
                "slug" => 'label_password',
                "default" => 'Password',
                "base64" => true
            )
        )->rebuildElementOnChange();
        
        $label_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Remember'),
                "slug" => 'label_remember',
                "default" => 'Remember Me',
                "base64" => true
            )
        )->rebuildElementOnChange();
        
        $label_section->typographySection('Typography', $label_selector,$this);
        
        
        /**
         * Labels
         */
        
        $spacing = $this->addControlSection("Spacing", __("Layout / Spacing"), "assets/icon.png", $this);
        $label_selector = 'p';
        $spacing->flex($label_selector, $this);
        
        
        
        /**
         * Inputs 
         */
        $input_section = $this->addControlSection("Inputs", __("Inputs"), "assets/icon.png", $this);
        $input_selector = 'input';
        
        $input_section->borderSection('Input Borders', $input_selector,$this);
        
        $input_section->boxShadowSection('Input Shadows', $input_selector,$this);
        
        $input_section->addStyleControls(
            
            array(
                array(
                    "property" => 'background-color',
                    "selector" => $input_selector
                ),
                array(
                    "name" => 'Background Hover Color',
                    "property" => 'background-color',
                    "selector" => $input_selector.":hover"
                ),
                array(
                    "name" => 'Text Color',
                    "property" => 'color',
                    "selector" => $input_selector
                ),
                array(
                    "name" => 'Text Hover Color',
                    "property" => 'color',
                    "selector" => $input_selector.":hover"
                ),
                array(
                    "name" => 'Width',
                    "property" => 'width',
                    "selector" => $input_selector
                )
            )
        );
        
        $input_spacing = $input_section->addControlSection("Input Spacing", __("Input Spacing"), "assets/icon.png", $this);
        
        $input_spacing->addPreset(
            "padding",
            "input_padding",
            __("Padding"),
            $input_selector
        )->whiteList();

        $input_spacing->addPreset(
            "margin",
            "input_margin",
            __("Margin"),
            $input_selector
        )->whiteList();
        
        
        /**
         * Submit Button
         */
        $submit_section = $this->addControlSection("Submit Button", __("Submit Button"), "assets/icon.png", $this);
        $submit_selector = 'input[type="submit"]';
        
        $submit_section->addPreset(
            "padding",
            "submit_padding",
            __("Padding"),
            $submit_selector
        )->whiteList();

        $submit_section->addPreset(
            "margin",
            "submit_margin",
            __("Margin"),
            $submit_selector
        )->whiteList();
        
        $submit_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Button Text'),
                "slug" => 'label_login',
                "default" => 'Log In',
                "base64" => true
            )
        )->rebuildElementOnChange();
        
        $submit_section->addStyleControls(
            array(
                array(
                    "property" => 'width',
                    "selector" => $submit_selector,
                )
            )
        );
        
        $submit_colors_section = $submit_section->addControlSection("submit_colors_section", __("Colors"), "assets/icon.png", $this);
        $submit_colors_section->addStyleControls(
            array(
                array(
                    "property" => 'background-color',
                    "selector" => $submit_selector,
                    "default" => '#111'
                ),
                array(
                    "name" => 'Background Hover Color',
                    "property" => 'background-color',
                    "selector" => $submit_selector.":hover",
                    "default" => '#222'
                ),
                array(
                    "name" => 'Text Color',
                    "property" => 'color',
                    "selector" => $submit_selector,
                    "default" => '#fff'
                ),
                array(
                    "name" => 'Text Hover Color',
                    "property" => 'color',
                    "selector" => $submit_selector.":hover",
                    "default" => '#fff'
                ),
                array(
                    "property" => 'width',
                    "selector" => $submit_selector,
                )
            )
        );
        
        $submit_section->borderSection('Borders', $submit_selector,$this);
        $submit_section->borderSection('Hover Borders', $submit_selector.":hover",$this);
        $submit_section->boxShadowSection('Shadows', $submit_selector,$this);
        $submit_section->boxShadowSection('Hover Shadows', $submit_selector.":hover",$this);

        $submit_section->typographySection('Typography', $submit_selector,$this);
        
        
        
    }
    
    function afterInit() {
        $this->removeApplyParamsButton();
    }
    
  
    
    function customCSS($options, $selector) {
        
        $css = ".oxy-extras-login-form {
                    width: 100%;
                }
        
                .oxy-extras-login-form p {
                    display: flex;
                    flex-direction: column;
                }
                
                .oxy-extras-login-form input[type='submit'] {
                    cursor: pointer;
                }
                
                .oxy-extras-login-form input[type='submit'] {
                    background-color: #111;
                    color: #fff;
                    max-width: 100%;
                }

                .oxy-extras-login-form input {
                    max-width: 100%;
                }
                
                .oxy-extras-login-form input[type='submit']:hover {
                    background-color: #222;
                }";
        
        return $css;
    }
    
    function allowedEmptyOptions($options) {

        $options_to_add = array(
            "oxy-extras-login-form_label_login",
             "oxy-extras-login-form_label_username",
             "oxy-extras-login-form_label_password",
             "oxy-extras-login-form_label_remember",
        );

        $options = array_merge($options, $options_to_add);

        return $options;
    }


}

new ExtraProLogin();