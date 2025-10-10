<?php
class ExtraInteractiveCursor extends OxygenExtraElements {

    var $js_added = false;
    var $css_added = false;
        
    function name() {
        return 'Interactive Cursor';
    }
   
    function extras_button_place() {
        return "interactive";
    }
    
    function tag() {
        return array('default' => 'div');
    }
    
    function icon() {
        return plugin_dir_url(__FILE__) . 'assets/icons/'.basename(__FILE__, '.php').'.svg';
    }

    function afterInit() {
        $this->removeApplyParamsButton();
    }
    

    function render($options, $defaults, $content) {

        $this->dequeue_scripts_styles();

        $speed = isset( $options['speed'] ) ? esc_attr($options['speed']) : '0.25';
        $hover_selectors = isset( $options['hover_selectors'] ) ? esc_attr($options['hover_selectors']) : '';


        if ( defined('OXY_ELEMENTS_API_AJAX') && OXY_ELEMENTS_API_AJAX ) {

            echo '<div class="oxy-cursor_builder">';
            echo '<div class="oxy-cursor_ball oxy-cursor_inner">';
            echo '<span class="oxy-cursor_text">Hover Text</span></div>';
            echo '<div class="oxy-cursor_trail oxy-cursor_inner"></div>';
            echo '</div>';

        } else {

            echo '<div class="oxy-cursor_ball oxy-cursor_inner" data-hover="' . $hover_selectors . '" data-speed="' . $speed . '">';
            echo '<span class="oxy-cursor_text"></span></div>';
            echo '<div class="oxy-cursor_trail oxy-cursor_inner"></div>';

        }



         // add JavaScript code only once and if shortcode presented
         if ($this->js_added !== true) {
                add_action( 'wp_footer', array( $this, 'output_js' ) );
            $this->js_added = true;
        }
    
    }
    
    function class_names() {
        return array();
    }

    function controls() {
        
        


        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Hover selectors'),
                "slug" => 'hover_selectors',
                "default" => 'a,input,button,.oxy-carousel-builder',
            )
        )->setParam("description", __("Add selectors that trigger the hover state for the cursor"));


        $this->addStyleControl( 
            array(
                "name" => __('Ball color'),
                "default" => "#383838",
                "selector" => '.oxy-cursor_ball::after',
                "property" => 'background-color',
            )
        )->setParam('hide_wrapper_end', true);
        
        $this->addStyleControl( 
            array(
                "name" => __('Trail Color'),
                "property" => 'background-color',
                "default" => "rgba(56,56,56,0.08)",
                "selector" => '.oxy-cursor_trail',
            )
        )->setParam('hide_wrapper_start', true);


        

        $this->addStyleControl(
            array(
                "name" => __('State transition duration'),
                "control_type" => 'slider-measurebox',
                "default" => '500',
                "property" => '--oxy-cursor-transition',
            )
        )
        ->setUnits('ms','ms')
        ->setRange('0','1000','1');


        $this->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Preview state in builder'),
                'slug' => 'preview_state',
                "default" => 'default',
            )
         )->setValue(array( 
            "default" => "Default", 
            "text" => "When showing text", 
            "hover" => "Hovering over selectors", 
            "mousedown" => "Mousedown",
            "none"   => "Hide in Builder"
         ))->setValueCSS( array(
             "text"  => " .oxy-cursor_builder .oxy-cursor_text {
                                 opacity: 1;
                             }
                             .oxy-cursor_builder .oxy-cursor_ball::after {
                                 -webkit-transform: translateZ(0) scale(calc(var(--oxy-cursor-text-scale)/10));
                                         transform: translateZ(0) scale(calc(var(--oxy-cursor-text-scale)/10));
                             }
                             
                             .oxy-cursor_builder .oxy-cursor_trail {
                                 -webkit-transform: translate3d(-50%, -50%,0) scale(0);
                                         transform: translate3d(-50%, -50%,0) scale(0);
                             } 
                             
                             .oxy-cursor_builder {
                                 opacity: var(--oxy-cursor-text-opacity);
                             }",
             
             "hover"  => " .oxy-cursor_builder .oxy-cursor_trail {
                                 -webkit-transform: translate3d(-50%, -50%,0) scale(calc(var(--oxy-cursor-trail-scale)/10));
                                         transform: translate3d(-50%, -50%,0) scale(calc(var(--oxy-cursor-trail-scale)/10));
                             }
                             
                             .oxy-cursor_builder .oxy-cursor_ball::after {
                                 -webkit-transform: translateZ(0) scale(calc(var(--oxy-cursor-text-end)/10));
                                         transform: translateZ(0) scale(calc(var(--oxy-cursor-text-end)/10));
                             }
                             
                             .oxy-cursor_builder {
                                opacity: var(--oxy-cursor-trail-opacity);
                            }",
             "mousedown"  => ".oxy-cursor_builder .oxy-cursor_text {
                                 opacity: 0;
                             }
                             
                             .oxy-cursor_builder .oxy-cursor_ball::after {
                                 -webkit-transform: translateZ(0) scale(calc(var(--oxy-cursor-text-down)/10));
                                         transform: translateZ(0) scale(calc(var(--oxy-cursor-text-down)/10));
                             }
                             
                             .oxy-cursor_builder .oxy-cursor_trail {
                                 -webkit-transform: translate3d(-50%, -50%,0) scale(calc(var(--oxy-cursor-trail-down)/10));
                                         transform: translate3d(-50%, -50%,0) scale(calc(var(--oxy-cursor-trail-down)/10));
                             }",   
             "none"  => " {
                            --oxy-cursor-display: none;
                            }",                                
         ) );


        $this->addStyleControl( 
            array(
                "name" => __('Ball scale'),
                "property" => '--oxy-cursor-text-start',
                "control_type" => 'slider-measurebox',
                "default" => '1',
                "condition" => 'preview_state=default'
            )
        )
        ->setRange('0','5','.01');

        $this->addStyleControl( 
            array(
                "name" => __('Trail scale'),
                "property" => '--oxy-cursor-trail-start',
                "control_type" => 'slider-measurebox',
                "default" => '1',
                "condition" => 'preview_state=default'
            )
        )
        ->setRange('0','5','.01');

        $this->addStyleControl( 
            array(
                "name" => __('Ball scale'),
                "property" => '--oxy-cursor-text-scale',
                "control_type" => 'slider-measurebox',
                "default" => '10',
                "condition" => 'preview_state=text'
            )
        )
        ->setRange('0','20','.01');

        $this->addStyleControl( 
            array(
                "name" => __('Opacity'),
                "property" => '--oxy-cursor-text-opacity',
                "control_type" => 'slider-measurebox',
                "default" => '1',
                "condition" => 'preview_state=text'
            )
        )
        ->setRange('0','1','.01');

        $this->addStyleControl( 
            array(
                "name" => __('Ball scale'),
                "property" => '--oxy-cursor-text-end',
                "control_type" => 'slider-measurebox',
                "default" => '0',
                "condition" => 'preview_state=hover'
            )
        )
        ->setRange('0','5','.01');

        $this->addStyleControl( 
            array(
                "name" => __('Trail scale'),
                "property" => '--oxy-cursor-trail-scale',
                "control_type" => 'slider-measurebox',
                "default" => '3',
                "condition" => 'preview_state=hover'
            )
        )
        ->setRange('0','15','.01');

        $this->addStyleControl( 
            array(
                "name" => __('Opacity'),
                "property" => '--oxy-cursor-trail-opacity',
                "control_type" => 'slider-measurebox',
                "default" => '1',
                "condition" => 'preview_state=hover'
            )
        )
        ->setRange('0','1','.01');

        $this->addStyleControl( 
            array(
                "name" => __('Ball scale'),
                "property" => '--oxy-cursor-text-down',
                "control_type" => 'slider-measurebox',
                "default" => '0.3',
                "condition" => 'preview_state=mousedown'
            )
        )
        ->setRange('0','5','.01');

        $this->addStyleControl( 
            array(
                "name" => __('Trail scale'),
                "property" => '--oxy-cursor-trail-down',
                "control_type" => 'slider-measurebox',
                "default" => '.8',
                "condition" => 'preview_state=mousedown'
            )
        )
        ->setRange('0','5','.01');

        $this->addOptionControl(
            array(
                "type" => 'slider-measurebox',
                "name" => __('Following cursor speed'),
                "slug" => 'speed',
                "default" => '0.25',
            )
        )->setRange('0','0.5','.01')
        ->setParam("description", __("Higher = closer to the cursor. Lower = more delay"));


        $this->typographySection('Typography', '.oxy-cursor_text' ,$this);

    }

    function customCSS($options, $selector) {

        $css = "";

        if( ! $this->css_added ) {

            $css .= ":root {
                        --oxy-cursor-text-scale: 10;
                        --oxy-cursor-trail-scale: 3;
                        --oxy-cursor-transition: 500ms;
                        --oxy-cursor-text-start: 1;
                        --oxy-cursor-trail-start: 1;
                        --oxy-cursor-text-end: 0;
                        --oxy-cursor-trail-down: 0.8;
                        --oxy-cursor-text-down: 0.3;
                        --oxy-cursor-text-opacity: 1;
                        --oxy-cursor-trail-opacity: 1;
                        --oxy-cursor-display: block;
                    }

                    .oxy-interactive-cursor {
                        visibility: hidden;
                    }

                    .oxygen-builder-body .oxy-interactive-cursor {
                        display: var(--oxy-cursor-display)!important;
                        visibility: visible;
                    }

                    .oxy-interactive-cursor.oxy-cursor_ready {
                        visibility: visible;
                    }

                    .extras-inside-lightbox .oxy-interactive-cursor {
                        display: none;
                    }
                    
                    .oxy-cursor_ball {
                        position: fixed;
                        top: 300px;
                        left: 300px;
                        width: 100px;
                        height: 100px;
                        border-radius: 50%;
                        z-index: 100001;
                        pointer-events: none;
                        -webkit-transform: translate3d(-50%, -50%,0);
                                transform: translate3d(-50%, -50%,0);
                        display: -webkit-box;
                        display: -ms-flexbox;
                        display: flex;
                        -webkit-box-align: center;
                            -ms-flex-align: center;
                                align-items: center;
                        -webkit-box-pack: center;
                            -ms-flex-pack: center;
                                justify-content: center;
                        text-align: center;
                    }
                    
                    .oxy-cursor_trail {
                        background-color: rgba(56,56,56,0.08);
                        position: fixed;
                        top: 300px;
                        left: 300px;
                        width: 320px;
                        height: 320px;
                        border-radius: 50%;
                        z-index: 10000;
                        pointer-events: none;
                        -webkit-transform: translate3d(-50%, -50%,0) scale(calc(var(--oxy-cursor-trail-start)/10));
                                transform: translate3d(-50%, -50%,0) scale(calc(var(--oxy-cursor-trail-start)/10));
                        display: -webkit-box;
                        display: -ms-flexbox;
                        display: flex;
                        -webkit-box-align: center;
                            -ms-flex-align: center;
                                align-items: center;
                        -webkit-box-pack: center;
                            -ms-flex-pack: center;
                                justify-content: center;
                        -webkit-transition: opacity var(--oxy-cursor-transition) ease, -webkit-transform var(--oxy-cursor-transition) ease;
                        transition: opacity var(--oxy-cursor-transition) ease, -webkit-transform var(--oxy-cursor-transition) ease;
                        will-change: transform,opacity;
                    }
                    
                    .oxy-cursor_text {
                        opacity: 0;
                        color: #fff;
                        font-size: 14px;
                        -webkit-transition: all var(--oxy-cursor-transition) ease;
                        transition: all var(--oxy-cursor-transition) ease;
                    }
                    
                    .oxy-cursor_text-visible .oxy-cursor_text {
                        opacity: 1;
                    }
                    
                    .oxy-cursor_mousedown .oxy-cursor_text,
                    .oxy-cursor_text-visible.oxy-cursor_mousedown .oxy-cursor_text {
                        opacity: 0;
                    }
                    
                    .oxy-cursor_ball::after {
                        content: '';
                        position: absolute;
                        top: 0;
                        bottom: 0;
                        left: 0;
                        border-radius: 50%;
                        right: 0;
                        background-color: #383838;
                        z-index: -1;
                        -webkit-transform: translateZ(0) scale(calc(var(--oxy-cursor-text-start)/10));
                                transform: translateZ(0) scale(calc(var(--oxy-cursor-text-start)/10));
                        -webkit-transition: opacity var(--oxy-cursor-transition) ease, -webkit-transform var(--oxy-cursor-transition) ease;
                        transition: opacity var(--oxy-cursor-transition) ease, -webkit-transform var(--oxy-cursor-transition) ease;
                        transition: transform var(--oxy-cursor-transition) ease, opacity var(--oxy-cursor-transition) ease;
                        transition: transform var(--oxy-cursor-transition) ease, opacity var(--oxy-cursor-transition) ease, -webkit-transform var(--oxy-cursor-transition) ease;
                        will-change: transform,opacity;
                    }

                    .oxy-cursor_trail-grow {
                        opacity: var(--oxy-cursor-trail-opacity);
                    }

                    .oxy-cursor_grow {
                        opacity: var(--oxy-cursor-text-opacity);
                    }
                    
                    .oxy-cursor_trail-grow .oxy-cursor_trail {
                        -webkit-transform: translate3d(-50%, -50%,0) scale(calc(var(--oxy-cursor-trail-scale)/10));
                                transform: translate3d(-50%, -50%,0) scale(calc(var(--oxy-cursor-trail-scale)/10));
                    }
                    
                    .oxy-cursor_trail-grow .oxy-cursor_ball::after {
                        -webkit-transform: translateZ(0) scale(calc(var(--oxy-cursor-text-end)/10));
                                transform: translateZ(0) scale(calc(var(--oxy-cursor-text-end)/10));
                    }
                    
                    .oxy-cursor_grow .oxy-cursor_ball::after {
                        -webkit-transform: translateZ(0) scale(calc(var(--oxy-cursor-text-scale)/10));
                                transform: translateZ(0) scale(calc(var(--oxy-cursor-text-scale)/10));
                    }
                    
                    .oxy-cursor_grow .oxy-cursor_trail {
                        -webkit-transform: translate3d(-50%, -50%,0) scale(0);
                                transform: translate3d(-50%, -50%,0) scale(0);
                    }
                    
                    .oxy-cursor_mousedown .oxy-cursor_ball::after {
                        -webkit-transform: translateZ(0) scale(calc(var(--oxy-cursor-text-down)/10));
                                transform: translateZ(0) scale(calc(var(--oxy-cursor-text-down)/10));
                    }
                    
                    .oxy-cursor_mousedown .oxy-cursor_trail {
                        -webkit-transform: translate3d(-50%, -50%,0) scale(calc(var(--oxy-cursor-trail-down)/10));
                                transform: translate3d(-50%, -50%,0) scale(calc(var(--oxy-cursor-trail-down)/10));
                    }
                    
                    
                    .oxy-cursor_text {
                        z-index: 10;
                    }
                    
                    .oxy-cursor_offpage .oxy-cursor_ball::after {
                        opacity: 0;
                        -webkit-transform: scale(0);
                                transform: scale(0);
                    }
                    
                    .oxy-cursor_offpage .oxy-cursor_trail {
                        opacity: 0;
                        -webkit-transform: translate3d(-50%, -50%,0) scale(0);
                                transform: translate3d(-50%, -50%,0) scale(0);
                    }
                    
                    .oxygen-builder-body .oxy-interactive-cursor {
                        position: relative;
                        height: 400px;
                        width: 100%;
                    }
                    
                    .oxygen-builder-body .oxy-cursor_ball,
                    .oxygen-builder-body .oxy-cursor_trail {
                        position: absolute;
                        top: 50%;
                        left: 50%;
                    }";

                    $this->css_added = true;

                }

        return $css;

    }

    function output_js() {

        wp_enqueue_script( 'extras-cursor', plugin_dir_url( __FILE__ ) . 'assets/interactive-cursor.js', '', '1.0.0', true );
        
    }

}

new ExtraInteractiveCursor();