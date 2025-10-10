<?php

class ExtraCircularProgress extends OxygenExtraElements {
        
    var $js_added = false;
    var $css_added = false;
    
	function name() {
        return 'Circular Progress';
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
        
        $percentage = $dynamic($options['percentage']);
        $maybe_text = isset( $options['maybe_text'] ) ? esc_attr($options['maybe_text']) : "";
        $count_duration = isset( $options['count_duration'] ) ? esc_attr($options['count_duration']) : "";
        
        $output = '';
        
        $output .= '<svg viewBox="0 0 36 36">
                      <circle class="oxy-circular-progress_inner-circle" cx="18" cy="18" r="14"></circle>
                      <path class="oxy-circular-progress_bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                      <path class="oxy-circular-progress_circle oxy-circular-progress_start" stroke-dasharray="'.$percentage.', 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" style="stroke: url(#'.esc_attr($options['selector']).'-gradient);" />
                      <linearGradient id="'.esc_attr($options['selector']).'-gradient" x1="0%" y1="100%" x2="100%" y2="0%" >
                        <stop class="oxy-circular-progress_gradient-one" offset="0%" />
                        <stop class="oxy-circular-progress_gradient-two" offset="100%" />
                        </linearGradient>
                    </svg>';
        
        $output .= '<div class="oxy-inner-content" data-duration="'.$count_duration.'" data-start="0" data-end="'.$percentage.'">';
        
        if ($content) {
            
            if ( function_exists('do_oxygen_elements') ) {
                $output .= do_oxygen_elements($content); 
            }
            else {
                $output .= do_shortcode($content); 
            }      
            
        } else {
            
            if ('true' === $maybe_text) {
            
                $output .= '<div class="oxy-circular-progress_text"><div id="number' . esc_attr($options['selector']) .'" class="oxy-circular-progress_number">0</div><div class="oxy-circular-progress_symbol">%</div></div>';
                
            }
            
        }
        
        $output .= '</div>';
        
        echo $output; 
        
        $this->dequeue_scripts_styles();

        if( method_exists('OxygenElement', 'builderInlineJS') ) {
            
            // Force animation to play when user rebuilds element (inside Oxygen only for better UX)
            $this->El->builderInlineJS("jQuery('#%%ELEMENT_ID%%').find('.oxy-circular-progress_start').removeClass('oxy-circular-progress_start');
                                        var end = jQuery('#%%ELEMENT_ID%%').find('.oxy-inner-content').data('end');
                                                  jQuery('#%%ELEMENT_ID%%').find('.oxy-circular-progress_number').text(end);");
            
        }
        
        
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
        
        $progress_selector = '.oxy-circular-progress_circle';
        $bg_selector = '.oxy-circular-progress_bg';
        $text_selector = '.oxy-circular-progress_text';
        $inner_selector = '.oxy-circular-progress_text, .oxy-inner-content';
        $gradient_one_selector = '.oxy-circular-progress_gradient-one';
        $gradient_two_selector = '.oxy-circular-progress_gradient-two';
        
        
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Progress percentage'),
                "slug" => 'percentage',
                "default" => '78',
            )
        )->rebuildElementOnChange()
        ->setParam('dynamicdatacode', '<div optionname="\'oxy-circular-progress_percentage\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');
        
        
        $this->addStyleControl( 
           array(
                "name" => __('Circle Size'),
                "type" => 'measurebox',
                "property" => 'width',
                "control_type" => 'slider-measurebox',
                "selector" => '',
               "default" => '200'
            
           )
        )
        //->setUnits('px')
        ->setRange('1','1000','1');
        
        
        $this->addStyleControl( 
           array(
                "property" => 'animation-duration',
                "default" => '1000',
                "slug" => 'count_duration',
                "control_type" => 'slider-measurebox',
                "selector" => $progress_selector,
            
           )
        )
        ->setUnits('ms')
        ->setRange('0','2000','10');
        
        
        
        
         /**
         * Progress Circle
         */ 
        $progress_circle_section = $this->addControlSection("progress_circle_section", __("Progress Circle"), "assets/icon.png", $this);
        
        
        $progress_circle_section->addStyleControl( 
           array(
                "name" => 'Thickness',
                "type" => 'measurebox',
                "default" => '0.8',
                "property" => 'stroke-width',
                "control_type" => 'slider-measurebox',
                "selector" => $progress_selector,
            
           )
        )
        //->setUnits('px')
        ->setRange('0','4','.1');
        
        
        $progress_circle_section->addStyleControls(
            array(
                array(
                    "name" => __('Opacity'),
                    "property" => 'opacity',
                    "selector" => $progress_selector,
                )
            )
        );
        
        
        $progress_circle_section->addStyleControl( 
           array(
                "name" => __('Color one'),
                "property" => 'stop-color',
                "selector" => $gradient_one_selector,
                "control_type" => 'colorpicker',
                "default" => '#61f4ba'
            
           )
        );
        
        $progress_circle_section->addStyleControl( 
           array(
                "name" => __('Color two'),
                "property" => 'stop-color',
                "selector" => $gradient_two_selector,
                "control_type" => 'colorpicker',
                "default" => '#f46161'
           )
        );
        
        
        $progress_circle_section->addOptionControl(
            array(
                "type" => "dropdown",
                "name" => __('Line ending style'),
                "slug" => "extras_menu_name",
                "selector" => $progress_selector,
            )
        )->setValue(array( 
            "round" => "Round",
            "square" => "Square",
            "butt" => "Butt (pie chart)"
            )
        )->setValueCSS( array(
            "butt"  => "$progress_selector {
                stroke-linecap: butt;
                transform: scale(var(--progress-circle-scale)) rotate(var(--progress-circle-rotate));
            }",
            "square" => "$progress_selector {
                stroke-linecap: square;
            }
            ",
                
        ) );
        
        
        $progress_circle_section->addStyleControl(
            array(
                "name" => __('Rotate'),
                "selector" => $progress_selector,
                "property" => 'transform:rotate',
                "control_type" => 'slider-measurebox',
                "condition" => 'extras_menu_name!=butt'
            )
        )
        ->setUnits('deg','deg')
        ->setRange('-180','180');
        
        
        $progress_circle_section->addStyleControl( 
           array(
                "name" => 'Rotate',
                "default" => '0',
                "property" => '--progress-circle-rotate',
                "control_type" => 'slider-measurebox',
                "selector" => $progress_selector,
               "condition" => 'extras_menu_name=butt'
            
           )
        )
        ->setUnits('deg','deg')
        ->setRange('-180','180');
        
        
        $progress_circle_section->addStyleControl( 
           array(
                "name" => 'Scale',
                "default" => '1',
                "property" => '--progress-circle-scale',
                "control_type" => 'slider-measurebox',
                "selector" => $progress_selector,
                "condition" => 'extras_menu_name=butt'
           )
        )
        ->setRange('0','2','.01');
        
        
        /**
         * Inner Circle
         */ 
        $inner_circle_section = $this->addControlSection("inner_circle_section", __("Inner Circle"), "assets/icon.png", $this);
        
        $inner_circle_selector = '.oxy-circular-progress_inner-circle';
        
        $inner_circle_section->addStyleControl( 
           array(
                "name" => __('Background Color'),
                "property" => 'fill',
                "selector" => $inner_circle_selector,
                "control_type" => 'colorpicker',
                "default" => ''
            
           )
        );
        
        
        $inner_circle_section->addStyleControl( 
           array(
                "name" => 'Scale',
                "default" => '1',
                "property" => '--inner-circle-scale',
                "control_type" => 'slider-measurebox',
                "selector" => $inner_circle_selector,
            
           )
        )
        ->setRange('0','2','.01');
        
        
        $inner_circle_section->addStyleControls(
            array(
                array(
                    "property" => 'opacity',
                    "selector" => $inner_circle_selector,
                ),
            )
        );
        
        /**
         * Animation
         */ 
        //$animation_section = $progress_circle_section->addControlSection("animation_section", __("Animation"), "assets/icon.png", $this);
        
        
        
        
        
        
        
        /*
        $animation_section->addStyleControl( 
           array(
                "property" => 'animation-timing-function',
                "selector" => $progress_selector,
                "default" => 'ease-out',
           )
        );
        
        
        */
        
        
        
         /**
         * Background Circle
         */ 
        $bg_circle_section = $this->addControlSection("bg_circle_section", __("Background Circle"), "assets/icon.png", $this);
        
        
        $bg_circle_section->addStyleControl( 
           array(
                "name" => 'Thickness',
                "type" => 'measurebox',
                "default" => '0.8',
                "property" => 'stroke-width',
                "control_type" => 'slider-measurebox',
                "selector" => $bg_selector,
            
           )
        )
        //->setUnits('px')
        ->setRange('0','4','.1');
        
        
        $bg_circle_section->addStyleControls(
            array(
                array(
                    "name" => __('Opacity'),
                    "default" => '0.05',
                    "property" => 'opacity',
                    "selector" => $bg_selector,
                ),
                array(
                    "name" => __('Color'),
                    "property" => 'color',
                    "selector" => $bg_selector,
                ),
            )
        );
        
        
        $bg_circle_section->addStyleControl( 
           array(
                "name" => 'Scale',
                "default" => '1',
                "property" => '--background-circle-scale',
                "control_type" => 'slider-measurebox',
                "selector" => $bg_selector,
            
           )
        )
        ->setRange('0','2','.01');
        
        
        /**
         * Text
         */ 
        $text_section = $this->addControlSection("text_section", __("Text"), "assets/icon.png", $this);
        
        $text_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Percentage text'),
                'slug' => 'maybe_text'
            )
            
        )->setDefaultValue('true')
        ->setValue(array( 
             "true" => "Enable", 
            "false" => "Disable"
            )
        )->rebuildElementOnChange()
         ->setParam("description", __("Any element can be placed inside the circle, if default text disabled"));
        
        
        $text_section->typographySection('Typography', $text_selector,$this);
        
        
        /*$text_duration_control = $text_section->addOptionControl(
            array(
                "type" => 'slider-measurebox',
                "name" => __('Count Up Duration','oxygen'),
                
                "default" => "1000",
                "condition" => 'maybe_text=true'
            )
        );
        $text_duration_control->setUnits("s","s");
        $text_duration_control->setRange('0','2000','10'); */
        
        
        /**
         * Shadow
         */ 
        $drop_shadow_section = $this->addControlSection("drop_shadow_section", __("Drop Shadow"), "assets/icon.png", $this);
        
        
        $drop_shadow_section->addStyleControl( 
           array(
                "name" => __('Shadow Color'),
                "property" => '--circular-shadow-color',
                "selector" => 'svg',
                "control_type" => 'colorpicker',
            
           )
        );
        
        $drop_shadow_section->addStyleControl( 
           array(
                "name" => __('Shadow horizontal offset'),
                "property" => '--circular-shadow-horizonal-offset',
                "control_type" => 'slider-measurebox',
                "selector" => 'svg',
            
           )
        )
        ->setUnits('px')
        ->setRange('0','100','1');
        
        $drop_shadow_section->addStyleControl( 
           array(
                "name" => __('Shadow vertical offset'),
                "property" => '--circular-shadow-vertical-offset',
                "control_type" => 'slider-measurebox',
                "selector" => 'svg',
            
           )
        )
        ->setUnits('px')
        ->setRange('0','100','1');
        
        $drop_shadow_section->addStyleControl( 
           array(
                "name" => __('Shadow blur'),
                "property" => '--circular-shadow-blur',
                "control_type" => 'slider-measurebox',
                "selector" => 'svg',
            
           )
        )
        ->setUnits('px')
        ->setRange('0','100','1');
        
        
        
        $inner_section = $this->addControlSection("inner_section", __("Inner Layout"), "assets/icon.png", $this);
        $inner_section->flex($inner_selector, $this);
        
        
       

    }
   
    
    function customCSS($options, $selector) {
        
        $css = '';
        
        if( ! $this->css_added ) {
        
            $css .= ".oxy-circular-progress {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        width: 200px;
                    }

                    .oxy-circular-progress svg {
                        height: 100%;
                        width: 100%;
                        filter: drop-shadow(var(--circular-shadow-horizonal-offset) var(--circular-shadow-vertical-offset) var(--circular-shadow-blur) var(--circular-shadow-color));
                    }

                    .oxy-circular-progress_text {
                        display: flex;
                        flex-direction: row;
                    }

                    .oxy-circular-progress .oxy-inner-content {
                        position: absolute;
                        display: flex;
                    }

                    .oxy-circular-progress_bg {
                      opacity: .05;
                      fill: none;
                      stroke: currentColor;
                      stroke-width: 3.8;
                      transform-origin: center;
                      -webkit-transform-origin: center;
                      -webkit-transform: scale(var(--background-circle-scale));
                      transform: scale(var(--background-circle-scale));
                    }

                    .oxy-circular-progress_start.oxy-circular-progress_circle {
                        stroke-dasharray: 0 100;
                        animation: none;
                    }

                    .oxy-circular-progress_circle {
                      fill: none;
                      stroke-width: 2.8;
                      stroke-linecap: round;
                      transform-origin: center;
                      -webkit-transform-origin: center;
                      animation: oxy-circular-progress_animate 1s cubic-bezier(0,0,0.58,1) forwards;
                      --progress-circle-scale: 1;
                      --progress-circle-rotate: 0;
                    }

                    .oxy-circular-progress_gradient-one {
                        stop-color: #db4646;
                        stop-opacity: 1;
                    }

                    .oxy-circular-progress_gradient-two { 
                        stop-color: #61f4ba;
                        stop-opacity: 1;
                    }

                    .oxy-circular-progress_inner-circle {
                        transform: scale(var(--inner-circle-scale));
                        -webkit-transform: scale(var(--inner-circle-scale));
                        transform-origin: center;
                        -webkit-transform-origin: center;
                        fill: none;
                    }

                    @keyframes oxy-circular-progress_animate {
                      0% {
                        stroke-dasharray: 0 100;
                      }
                    }";
            
            $this->css_added = true;
            
        }
        
        
        return $css;
        
    } 
    
    
    function output_js() {

        wp_enqueue_script( 'intersection-js', plugin_dir_url( __FILE__ ) . 'assets/intersectionobserver.js', '', '1.0.0', true );
        wp_enqueue_script( 'countUp-js', plugin_dir_url( __FILE__ ) . 'assets/countUp.js', '', '2.0.5', true );
        
    }
    
    function output_init_js() { ?>
         <script type="text/javascript">
            jQuery(document).ready(oxygen_init_circle_progress);
            function oxygen_init_circle_progress($) {

                var config = {
                    root: null,
                    rootMargin: '0px',
                    threshold: 1
                };
                
                function callback(entries, observer){
                  entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        
                        let $number = $(entry.target).find('.oxy-circular-progress_number'),
                            $inner = $(entry.target).find('.oxy-inner-content'),
                            $end = $inner.attr('data-end'),
                            $duration = $inner.data('duration');
                        
                        
                        $(entry.target).find('.oxy-circular-progress_circle').css("animation-duration", $duration + 'ms');
                        
                        $(entry.target).find('.oxy-circular-progress_start').removeClass('oxy-circular-progress_start');
                        
                        const options = {
                                  startVal: $inner.attr('data-start'),
                                  useEasing: false,
                                  duration: $duration/1000,
                                };
                            
                        const numAnim = new countUp.CountUp($number[0], $end, options);
                        if (!countUp.error) {
                          numAnim.start()
                        } else {
                          console.error(countUp.error);
                        }
                        observer.unobserve(entry.target);
                    }
                  }
                )};
                                  
                var observer = new IntersectionObserver(callback,config);
                var counters = document.querySelectorAll('.oxy-circular-progress'); 
                counters.forEach(counter => {

                    let progressCircle = $(counter).find('.oxy-circular-progress_circle');
                    let progressGradientURL = 'url(#' + $(counter).find('linearGradient').attr('id') + ')';

                    progressCircle.css("stroke", progressGradientURL);

                    observer.observe(counter);
                });                  
            }
        </script>
    <?php }
    
    
}

add_filter("oxy_base64_encode_options", 
    function($items) { 
        $items=array_merge($items, array('oxy-circular-progress_percentage')); 
        return $items;
    }
);

new ExtraCircularProgress();