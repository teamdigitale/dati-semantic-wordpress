<?php
class ExtraCounter extends OxygenExtraElements {
        
    var $js_added = false;
    
	function name() {
        return 'Counter';
    }
    
    function icon() {
        return plugin_dir_url(__FILE__) . 'assets/icons/'.basename(__FILE__, '.php').'.svg';
    }
    
    function enablePresets() {
        return true;
    }
    
    function extras_button_place() {
        return "interactive";
    }
    
    function tag() {
        return array('default' => 'div', 'choices' => 'div,p,span' );
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
          

        // Get Options
        $unit = ($options['unit_display'] === 'display') ? '<span class="oxy-counter_suffix">' . esc_attr($options['unit']) . '</span>' : '';
        $unit_space = ($options['unit_spacing'] === 'space') ? ' ' . $unit : '' . $unit;
            
            
        $start = $dynamic($options['start']);
        $end = $dynamic($options['end']);
        
        
        $before = ($options['before_unit_display'] === 'display') ? '<span class="oxy-counter_prefix">' . esc_attr($options['before']) . '</span>' : '';
        $duration = isset( $options['duration'] ) ? esc_attr($options['duration']) : '100';
        $easing = ($options['easing'] === 'false') ? false : true;
        $amount = isset( $options['amount'] ) ? esc_attr($options['amount']) : '333';
        $threshold = isset( $options['threshold'] ) ? esc_attr($options['threshold']) : '999';
        $decimal = isset( $options['decimal'] ) ? esc_attr($options['decimal']) : '.';
        $separator = isset( $options['separator'] ) ? esc_attr($options['separator']) : ','; 
        $grouping = ($options['grouping'] === 'false') ? false : true;
        $decimals = isset( $options['decimals'] ) ? esc_attr($options['decimals']) : '0';
        
        $output = $before . '<span id="' . esc_attr($options['selector']) .'-digit" class="oxy-counter_digit" data-easing="' . $easing . '" data-threshold="' . $threshold . '" data-amount="' . $amount . '" data-duration="'. $duration .'" data-end="'. $end .'" data-grouping="'. $grouping .'" data-start="'. $start .'"  data-decimal="'. $decimal .'" data-decimals="'. $decimals .'" data-separator="'. $separator .'">' . $start . '</span>' . $unit_space;
        
        echo $output;
        
        $this->dequeue_scripts_styles();
        
        if( method_exists('OxygenElement', 'builderInlineJS') ) {
            
            // show end number inside builder instead of start number.
            $this->El->builderInlineJS("
            var end = jQuery('#%%ELEMENT_ID%%').find('.oxy-counter_digit').data('end');
            jQuery('#%%ELEMENT_ID%%').find('.oxy-counter_digit').text(end);");
        
        }
        
        // add JavaScript code only once and if shortcode presented
        if ($this->js_added !== true) {
            if (!defined('OXY_ELEMENTS_API_AJAX') || !OXY_ELEMENTS_API_AJAX) {
                add_action( 'wp_footer', array( $this, 'output_js' ) );
                add_action( 'wp_footer', array( $this, 'output_init_js' ), 25 );
            }
            $this->js_added = true;
        } 
        
    }

    function class_names() {
        return array();
    }

    function controls() {

        /**
         * Primary Controls
         */
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Start Digit'),
                "slug" => 'start',
                "default" => '0',
                'shortcode'	=> true,
            )
        )->rebuildElementOnChange()
        ->setParam('dynamicdatacode', '<div optionname="\'oxy-counter_start\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');
        
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('End Digit'),
                "slug" => 'end',
                "default" => '100'
            )
        )->rebuildElementOnChange()
        ->setParam('dynamicdatacode', '<div optionname="\'oxy-counter_end\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');
        
        
        
        $duration_control = $this->addOptionControl(
            array(
                "type" => 'slider-measurebox',
                "name" => __('Count Up Duration','oxygen'),
                "slug" => 'duration',
                "default" => "1"
            )
        );
        $duration_control->setUnits("s","s");
        $duration_control->setRange('0','10','0.1');
        
        
        
        /**
         * Content
         */
        
        $content_section = $this->addControlSection("content_section", __("Content"), "assets/icon.png", $this);
        
        $content_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Suffix Display',
                'slug' => 'unit_display'
            )
            
        )->setValue(array( "display" => "Display", "hide" => "Hide" ))
         ->setDefaultValue('hide')->rebuildElementOnChange();
        
        $content_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Suffix Text'),
                "slug" => 'unit',
                "default" => '%',
                "condition" => 'unit_display=display',
            )
        )->rebuildElementOnChange();
        
        
        $content_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Suffix Spacing',
                'slug' => 'unit_spacing',
                "condition" => 'unit_display=display',
            )
            
        )->setValue(array( "space" => "Add Space", "nospace" => "No Space" ))
         ->setDefaultValue('nospace')->rebuildElementOnChange();
        
        
        $content_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Prefix Display',
                'slug' => 'before_unit_display'
            )
            
        )->setValue(array( "display" => "Display", "hide" => "Hide" ))
         ->setDefaultValue('hide')->rebuildElementOnChange();
        
        
        $content_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Prefix Text'),
                "slug" => 'before',
                "default" => '',
                "condition" => 'before_unit_display=display',
            )
        )->rebuildElementOnChange();
        
        $content_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Use Grouping',
                'slug' => 'grouping'
            )
            
        )->setValue(array( "true" => "True", "false" => "False" ))
         ->setDefaultValue('true')->rebuildElementOnChange();
        
        $content_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Decimal Places'),
                "slug" => 'decimals',
                "default" => '0',
            )
        ); 
        
        $content_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Separator'),
                "slug" => 'separator',
                "default" => ',',
                "condition" => 'grouping=true'
            )
        ); 
        
        $content_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Decimal'),
                "slug" => 'decimal',
                "default" => '.',
                "condition" => 'grouping=true'
            )
        );
        
        
        /**
         * Content
         */
        
        $easing_section = $this->addControlSection("easing_section", __("Easing"), "assets/icon.png", $this);
        
        
        $easing_section->addOptionControl(
            array(
                "type" => "dropdown",
                "name" => "Easing",
                "slug" => "easing",
                "default" => 'true',
            )
        )->setValue(
           array( 
                "true" => "Easing On", 
                "false" => "Linear",
               
           )
       );
        
        $easing_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Smart Easing Threshold'),
                "slug" => 'threshold',
                "default" => '999',
                "condition" => 'easing=true'
            )
        );
        
        $easing_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Smart Easing Amount'),
                "slug" => 'amount',
                "default" => '333',
                "condition" => 'easing=true'
            )
        );
        
        
        /**
         * Content
         */
        $this->typographySection('Digit Typography', '.oxy-counter_digit',$this);    
        
        
        
    }
    
    
    function customCSS($options, $selector) {
        
        $css = ".oxy-counter {
                    font-size: 48px;
                    line-height: 1.2;
                }";
        
        return $css;
        
    }
    
    
    function afterInit() {
        $this->removeApplyParamsButton();
    }
    
    function allowedEmptyOptions($options) {

        $options_to_add = array(
            "oxy-counter_unit",
            "oxy-counter_end",
            "oxy-counter_before",
        );

        $options = array_merge($options, $options_to_add);

        return $options;
    }
    
    function output_js() {
        
         wp_enqueue_script( 'intersection-js', plugin_dir_url( __FILE__ ) . 'assets/intersectionobserver.js', '', '1.0.0', true );
         wp_enqueue_script( 'countUp-js', plugin_dir_url( __FILE__ ) . 'assets/countUp.js', '', '2.0.5', true );
        
    }
    
    function output_init_js() {
        
        ?>

        <script type="text/javascript">

            jQuery(document).ready(oxygen_init_counter);
            function oxygen_init_counter($) {
                
               var config = {
                    root: null,
                    rootMargin: '0px',
                    threshold: 1
                };
                
                function callback(entries, observer){
                  entries.forEach(entry => {
                    if (entry.isIntersecting) {

                        let $this = $(entry.target),
                            $end = $this.attr('data-end');
                        
                        const options = {
                                  startVal: $this.attr('data-start'),
                                  decimalPlaces: $this.attr('data-decimals'),
                                  duration: $this.attr('data-duration'),
                                  useEasing: $this.attr('data-easing'),
                                  useGrouping: $this.attr('data-grouping'),
                                  separator: $this.attr('data-separator'),
                                  decimal: $this.attr('data-decimal'),
                                  smartEasingThreshold: $this.attr('data-threshold'),
                                  smartEasingAmount: $this.attr('data-amount'),
                                };
                            
                        const numAnim = new countUp.CountUp(entry.target, $end, options);
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
                var counters = document.querySelectorAll('.oxy-counter_digit'); 
                counters.forEach(counter => {
                    observer.observe(counter);
                });                  
        
            }
                
        </script>

    <?php
        
    }

}

add_filter("oxy_base64_encode_options", 
    function($items) { 
        $items=array_merge($items, array('oxy-counter_start', 'oxy-counter_end')); 
        return $items;
    }
);

new ExtraCounter();