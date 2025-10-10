<?php

class ExtraPreLoad extends OxygenExtraElements {
    
    var $js_added = false;
    var $css_added = false;

	function name() {
        return __('Preloader'); 
    }
    
    function icon() {
        return plugin_dir_url(__FILE__) . 'assets/icons/'.basename(__FILE__, '.php').'.svg';
    }
    
    function init() {
        $this->enableNesting();
    }
    
    
    function extras_button_place() {
        return "other";
    }
    
    
    function render($options, $defaults, $content) {
        
        
        echo '<div class="oxy-preloader_fadein oxy-inner-content" ';
        
        
        /**
         * Preload Data
         */
        if(isset($options['preload_show_again'])) {
                echo 'data-again="' . esc_attr( $options['preload_show_again'] ) . '" ';
            }

            if(isset($options['preload_wait'])) {
                echo 'data-wait="' . esc_attr( $options['preload_wait'] ) . '" ';
            }

            if(isset($options['preload_wait_seconds'])) {
                echo 'data-wait-sec="' . esc_attr( $options['preload_wait_seconds'] ) . '" ';
            }
        
        echo '>';
        
        /**
         * Custom (nestable content)
         */
        if (isset( $options['preload_type'] ) && $options['preload_type'] === 'custom') {
            
            if ($content) {

                if ( function_exists('do_oxygen_elements') ) {
                    echo do_oxygen_elements($content); 
                }
                else {
                    echo do_shortcode($content); 
                } 
                
            } 
            
        } 
        
        /**
         * Image
         */
        
        elseif (isset( $options['preload_type'] ) && $options['preload_type'] === 'image') {
            
            $preload_image_url = isset( $options['preload_image_url'] ) ? esc_url($options['preload_image_url']) : "";
            
            echo '<img src="'. $preload_image_url .'" class="oxy-preloader_image">';
            
        } 
        
        
        /**
         * SpinKit Presets
         */
        else {
            
            echo '<div class="oxy-preloader_loader">';
            
            $preload_css = $options['preload_css'];
            
            switch ($preload_css) {
                    
            case 'Plane':
                $preload_css = '<div class="sk-plane"></div>';
            break;
            case 'Chase':
                $preload_css = '<div class="sk-chase">
                                  <div class="sk-chase-dot"></div>
                                  <div class="sk-chase-dot"></div>
                                  <div class="sk-chase-dot"></div>
                                  <div class="sk-chase-dot"></div>
                                  <div class="sk-chase-dot"></div>
                                  <div class="sk-chase-dot"></div>
                                </div>';
            break;
            case 'Bounce':
                $preload_css = '<div class="sk-bounce">
                                  <div class="sk-bounce-dot"></div>
                                  <div class="sk-bounce-dot"></div>
                                </div>';
            break;
            case 'Plane':
                $preload_css = '<div class="sk-plane"></div>';
            break;
            case 'Wave':
                $preload_css = '<div class="sk-wave">
                                  <div class="sk-wave-rect"></div>
                                  <div class="sk-wave-rect"></div>
                                  <div class="sk-wave-rect"></div>
                                  <div class="sk-wave-rect"></div>
                                  <div class="sk-wave-rect"></div>
                                </div>';
            break;
            case 'Pulse':
                $preload_css = '<div class="sk-pulse"></div>';
            break;  
            case 'Flow':
                $preload_css = '<div class="sk-flow">
                                  <div class="sk-flow-dot"></div>
                                  <div class="sk-flow-dot"></div>
                                  <div class="sk-flow-dot"></div>
                                </div>';
            break;  
            case 'Swing':
                $preload_css = '<div class="sk-swing">
                                  <div class="sk-swing-dot"></div>
                                  <div class="sk-swing-dot"></div>
                                </div>';
            break;  
            case 'Circle':
                $preload_css = '<div class="sk-circle">
                                  <div class="sk-circle-dot"></div>
                                  <div class="sk-circle-dot"></div>
                                  <div class="sk-circle-dot"></div>
                                  <div class="sk-circle-dot"></div>
                                  <div class="sk-circle-dot"></div>
                                  <div class="sk-circle-dot"></div>
                                  <div class="sk-circle-dot"></div>
                                  <div class="sk-circle-dot"></div>
                                  <div class="sk-circle-dot"></div>
                                  <div class="sk-circle-dot"></div>
                                  <div class="sk-circle-dot"></div>
                                  <div class="sk-circle-dot"></div>
                                </div>';
            break;  
            case 'Circle Fade':
                $preload_css = '<div class="sk-circle-fade">
                                  <div class="sk-circle-fade-dot"></div>
                                  <div class="sk-circle-fade-dot"></div>
                                  <div class="sk-circle-fade-dot"></div>
                                  <div class="sk-circle-fade-dot"></div>
                                  <div class="sk-circle-fade-dot"></div>
                                  <div class="sk-circle-fade-dot"></div>
                                  <div class="sk-circle-fade-dot"></div>
                                  <div class="sk-circle-fade-dot"></div>
                                  <div class="sk-circle-fade-dot"></div>
                                  <div class="sk-circle-fade-dot"></div>
                                  <div class="sk-circle-fade-dot"></div>
                                  <div class="sk-circle-fade-dot"></div>
                                </div>';
            break;  
            case 'Grid':
                $preload_css = '<div class="sk-grid">
                                  <div class="sk-grid-cube"></div>
                                  <div class="sk-grid-cube"></div>
                                  <div class="sk-grid-cube"></div>
                                  <div class="sk-grid-cube"></div>
                                  <div class="sk-grid-cube"></div>
                                  <div class="sk-grid-cube"></div>
                                  <div class="sk-grid-cube"></div>
                                  <div class="sk-grid-cube"></div>
                                  <div class="sk-grid-cube"></div>
                                </div>';
            break;  
            case 'Fold':
                $preload_css = '<div class="sk-fold">
                                  <div class="sk-fold-cube"></div>
                                  <div class="sk-fold-cube"></div>
                                  <div class="sk-fold-cube"></div>
                                  <div class="sk-fold-cube"></div>
                                </div>';
            break;  
            case 'Wander':
                $preload_css = '<div class="sk-wander">
                                  <div class="sk-wander-cube"></div>
                                  <div class="sk-wander-cube"></div>
                                  <div class="sk-wander-cube"></div>
                                </div>';
            break;          
            }
            
            
            echo $preload_css;
            
            echo '</div>';
        }
        
        echo '</div>';

        $this->dequeue_scripts_styles();
        
        if (!defined('OXY_ELEMENTS_API_AJAX') || !OXY_ELEMENTS_API_AJAX) {
        
            $this->El->inlineJS('!function(e){if("CTFrontendBuilder"!=e("html").attr("ng-app")){var a=e("#%%ELEMENT_ID%%").find(".oxy-preloader_fadein"),o=1e3*a.data("wait-sec");switch(a.data("wait")){case"load":e(window).on("load",function(){t()});break;case "click": e("#%%ELEMENT_ID%%").on("click", function () { t(); }); case"webfont":break;case"manual":setTimeout(function(){t()},o)}!function(a){e(a);!function(a){var e=jQuery(a),o=e.find(".oxy-preloader_fadein"),t=e[0].id,n=(new Date).getTime(),i=!(!localStorage||!localStorage["oxy-"+t+"-last-shown-time"])&&JSON.parse(localStorage["oxy-"+t+"-last-shown-time"]);switch(o.data("again")){case"never_show_again":if(!1!==i)return}localStorage&&(localStorage["oxy-"+t+"-last-shown-time"]=JSON.stringify(n));e.css({display:"flex"})}(a)}("#%%ELEMENT_ID%%")}function t(){e("#%%ELEMENT_ID%%").addClass("oxy-preloader_hidden")}}(jQuery);');

        }
        
    }

    function class_names() {
        return array();
    }
    

    function controls() {

        $preload_builder_visibility = $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('In-builder Visibility'),
                'slug' => 'preload_visibility_builder')
            
        );
        $preload_builder_visibility->setValue(array( "visible" => "Visible", "hidden" => "Hidden"));
        $preload_builder_visibility->setDefaultValue('visible')->setValueCSS( array(
            "hidden"  => " {
                        display: none;
                    }
                    
               ",
        ) );
        
        $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Type of Pre-loader',
                'slug' => 'preload_type')
            
        )->setValue(array( "css" => "Presets", "image" => "Image", "custom" => "Custom" ))
         ->setDefaultValue('css')->rebuildElementOnChange();
        
        
        $preloader_css_control = $this->addOptionControl(
            array(
                "type" => "dropdown",
                "name" => "CSS Loader presets",
                "slug" => "preload_css",
                "default" => 'Plane',
                "condition" => 'preload_type=css',
            )
        )->setValue(
           array( 
               "Plane",
               "Chase",
               "Bounce",
               "Wave",
               "Pulse",
               //"Flow",
               "Swing",
               "Circle",
               "Circle Fade",
               "Grid",
               "Fold",
               //"Wander",
           )
       )->rebuildElementOnChange();
        
        /**
         * Styles
         */
        
        $this->addStyleControl(
            array(
                "property" => 'color',
                "default" => '#f15b51',
                "selector" => '.oxy-preloader_loader',
                "condition" => "preload_type=css"
            )
        );
        
        $this->addStyleControl(
            array(
                "property" => 'background-color',
                "default" => '#fff',
                "selector" => '.oxy-preloader_fadein',
                "condition" => "preload_type=css"
            )
        );
        
        
        
        $this->addStyleControl( 
            array(
                "selector" => '.oxy-preloader_loader',
                "name" => 'Size',
                "default" => "40",
                "property" => 'height|width',
                "control_type" => 'slider-measurebox',
                "condition" => 'preload_type=css',
            )
        )
        ->setUnits('px','px')
        ->setRange('0','300','1');
        
        $this->addStyleControl( 
            array(
                "selector" => '.oxy-preloader_loader > div',
                //"name" => 'Spin Duration',
                "default" => "1.2",
                "property" => 'animation-duration',
                "control_type" => 'slider-measurebox',
                "condition" => 'preload_type=css',
            )
        )
        ->setUnits('s','s')
        ->setRange('0','3','.1');
        
        
        $this->addStyleControl( 
            array(
                "selector" => '.oxy-preloader_loader > div > div, .oxy-preloader_loader > div > div::before',
                "name" => 'Inner Animation Duration',
                "default" => "1.2",
                "property" => 'animation-duration',
                "control_type" => 'slider-measurebox',
                "condition" => 'preload_type=css',
            )
        )
        ->setUnits('s','s')
        ->setRange('0','3','.1');
        
        
        
         /**
         * Image
         */
        
        $preload_image_selector = '.oxy-preloader_image';
        
        $this->addOptionControl(
            array(
                'type' => 'mediaurl',
                'name' => 'Image URL',
                'slug' => 'preload_image_url',
                'default' => 'https://i.gifer.com/origin/34/34338d26023e5515f6cc8969aa027bca_w200.gif',
                "condition" => 'preload_type=image',
            )
        )->rebuildElementOnChange();
        
        
        $this->addStyleControl( 
            array(
                "selector" => $preload_image_selector,
                "name" => 'Image Width',
                "default" => "50",
                "property" => 'width',
                "control_type" => 'slider-measurebox',
                "condition" => 'preload_type=image',
            )
        )
        ->setUnits('px','px')
        ->setRange('0','300','1');
        
        
        
        /**
         * Config
         */
        $config_section = $this->addControlSection("config_section", __("Config"), "assets/icon.png", $this);
        
        $config_section->addOptionControl(
            array(
                "type" => "dropdown",
                "name" => "Remove preloader only after...",
                "slug" => "preload_wait",
                "default" => 'load',
            )
        )->setValue(
           array( 
                "load" => "All page content loaded", 
               "webfont" => "Webfonts are active (if using webfont.js)", 
               "manual" => "After x seconds",
               "click" => "Click"
           )
       )->setValueCSS( array(
        "click"  => " {
                    cursor: pointer;
                }
           ",
        ) );
        
        $config_section->addOptionControl(
           array(
                "type" => 'measurebox',
                "name" => __('Remove preloader after...'),
                "slug" 	    => "preload_wait_seconds",
                "default" => "2",
                "control_type" => 'slider-measurebox',
                "condition"		=> "preload_wait=manual",
            )
        )
        ->setUnits('sec','sec');
        
        /**
         * Show Again
         */
        $config_section->addOptionControl(
            array(
                "type" => "dropdown",
                "name" => "Show Again..",
                "slug" => "preload_show_again",
                "default" => 'always_show',
            )
        )->setValue(
           array( 
                "always_show" => "On every page load",
                "never_show_again" => "Only on first page visit",
           )
       );
        
        
        
        
    }
    
    function preload_break() {
        ob_start(); ?>
            <hr>
        <?php 

        return ob_get_clean();
    }
    
    function customCSS($options, $selector) {
        
        $css = '';
        
        if( ! $this->css_added ) {
        
            $css .= ".oxy-preloader {
                        color: #f15b51;
                        position: fixed;
                        top: 0;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        z-index: 999999;
                        justify-content: center;
                        align-items: center;
                        transition-duration: 1s;
                        transition-timing-function: ease;
                        transition-property: opacity, visibility;
                        --sk-color: currentColor;
                        --sk-size : 100%;
                    }

                    .oxy-preloader_hidden {
                        visibility: hidden;
                        opacity: 0;
                        pointer-events: none;
                    }

                    .oxy-preloader_fadein {
                        background-color: #fff;
                        height: 100%;
                        width: 100%;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        z-index: 999999999;
                    }

                    .oxy-preloader_loader {
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 40px;
                        width: 40px;
                    }

                    .oxy-preloader_image {
                        width: 50px;
                    }

                    .admin-bar .oxy-preloader {
                        z-index: 99998;
                    }

                    .oxy-preloader.oxygenberg-element {
                        visibility: hidden;
                    }

                    .oxygen-builder-body .oxy-preloader,
                    .wf-active .oxygen-builder-body .oxy-preloader,
                    .wf-inactive .oxygen-builder-body .oxy-preloader {
                        display: flex;
                        visibility: visible;
                        opacity: 1;
                    }

                    ";

            $css .= file_get_contents(__DIR__.'/'.basename(__FILE__, '.php').'.css');
            
            $this->css_added = true;
            
        }
            
        $css .= "body:not(.oxygen-builder-body) $selector {
                    display: flex;
                 }";    
        
        
        if (isset($options['oxy-preloader_preload_wait']) && ($options['oxy-preloader_preload_wait'] === 'webfont')) {
            
            // Remove visibility of preloader when the webfont adds classes to html element
            $css .= ".wf-active .oxy-preloader,
                     .wf-inactive .oxy-preloader {
                        visibility: hidden;
                        opacity: 0;
                     }";
            
        }
        
        if (isset($options['oxy-preloader_preload_show_again']) && ($options['oxy-preloader_preload_show_again'] !== 'always_show')) {
            
            $css .= "body:not(.oxygen-builder-body) $selector {
                        display: none;
                     }";
            
        }
        
        return $css;
        
    }
    

}

new ExtraPreLoad();