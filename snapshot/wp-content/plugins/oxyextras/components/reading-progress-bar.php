<?php

class ExtraReadingProgress extends OxygenExtraElements {
    
    var $js_added = false;

	function name() {
        return __('Reading Progress Bar'); 
    }
    
    function icon() {
        return plugin_dir_url(__FILE__) . 'assets/icons/'.basename(__FILE__, '.php').'.svg';
    }
    
    function extras_button_place() {
        return "interactive";
    }
    
    
    function render($options, $defaults, $content) {
        
        // Get options
        $element_selector = isset( $options['element_selector'] ) ? esc_attr( $options['element_selector'] ) : 'body';
        $viewport_start = isset( $options['viewport_start'] ) ? esc_attr( $options['viewport_start'] ) : 'top';
        $viewport_end = isset( $options['viewport_end'] ) ? esc_attr( $options['viewport_end'] ) : 'middle';
        
        ?>
        <div class="reading-progress-inner" data-selector="<?php echo $element_selector; ?>" data-progress-start="<?php echo $viewport_start; ?>" data-progress-end="<?php echo $viewport_end; ?>"></div>
                
         <?php // }
             
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
       
        
        $position = $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Progess Bar Position',
                'slug' => 'position'
            )
            
        );
        
        $position->setValue(
            array( 
                "top" => "Top", 
                "bottom" => "Bottom", 
                "manual" => "Manual" 
            )
        );
        $position->setDefaultValue('top');
        $position->setValueCSS( array(
            "top"  => " {
                        bottom: auto;
                    }
                    
               ",
            "bottom"  => " {
                        top: auto;
                        bottom: 0;
                    }",
            "manual"  => " {
                        position: static;
                    }",
        ) );
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Element selector'),
                "slug" => 'element_selector',
                "default" => 'body',
            )
        );
        
        $this->addOptionControl(
            array(
                "type" => "dropdown",
                "name" => "Start when top of element reaches..",
                "slug" => "viewport_start",
                "default" => 'top',
            )
        )->setValue(
           array( 
                "top" => "Top of Viewport", 
                "middle" => "Middle of Viewport",
               "bottom" => "Bottom of Viewport",
               
           )
       );
        
        $this->addOptionControl(
            array(
                "type" => "dropdown",
                "name" => "End when bottom of element reaches..",
                "slug" => "viewport_end",
                "default" => 'bottom',
            )
        )->setValue(
           array( 
                "top" => "Top of Viewport", 
                "middle" => "Middle of Viewport",
               "bottom" => "Bottom of Viewport",
               
           )
       );
        
        $inner_selector = ".reading-progress-inner";
        
        $this->addStyleControls(
            array(
                array(
                    "name" => 'Fill Color',
                    "property" => 'background-color',
                    "default" => '#b54726',
                    "selector" => $inner_selector,
                ),
                array(
                    "property" => 'background-color',
                )
            )
        );
    
        
        $transition = $this->addStyleControl(
            array(
                "name" => __('Transition Duration'),
                "property" => 'transition-duration',
                "control_type" => 'slider-measurebox',
                "selector" => $inner_selector,
            )
        );

        $transition->setUnits('ms','ms');
        $transition->setRange(0, 500, 1);
        
        $this->addStyleControl( 
            array(
                "type" => 'measurebox',
                "name" => __('Height'),
                "default" => "4",
                "property" => 'height',
                "control_type" => 'slider-measurebox',
            )
        )
        ->setUnits('px','px')
        ->setRange('1','20','1');
        
        $this->addStyleControl(
            array(
                "property" => 'z-index',
                "default" => '9999',
            )
        );
        
        
    }
    
    function afterInit() {
        $this->removeApplyParamsButton();
    }
    
    function customCSS($options, $selector) {
        
        $css = ".oxy-reading-progress-bar {
                    position: fixed;
                    left: 0;
                    right: 0;
                    top: 0;
                    width: 100%;
                    height: 4px;
                    z-index: 9999;
                    overflow: hidden;
                }
                
                .admin-bar .oxy-reading-progress-bar {
                    top: 32px;
                }
                
                body:not(.oxygen-builder-body) .oxy-reading-progress-bar {
                    pointer-events: none;
                }
                
                body:not(.oxygen-builder-body) .reading-progress-inner {
                    transform: scaleX(0);
                }
                
                .oxygen-builder-body .oxy-reading-progress-bar.ct-active {
                    z-index: 2147483640;
                }
                
                .reading-progress-inner {
                    background-color: #b54726;
                    transform-origin: left;
                    height: 100%;
                    width: 100%;
                    will-change: transform;
                }
                ";

        return $css;
    }
    
    function output_js() { ?>
            
            <script type="text/javascript">
            jQuery(document).ready(oxygen_init_reading_progress);
            function oxygen_init_reading_progress($) {
                     
                    var progressBar = $('.oxy-reading-progress-bar');
                
                        $(window).scroll(function(){
                            
                            progressBar.each(function(){
                                    
                                  let reading_progress_selector = $( $(this).children( '.reading-progress-inner' ).data( 'selector' )),
                                        reading_progress_start = $(this).children( '.reading-progress-inner' ).data( 'progress-start' ),
                                        reading_progress_end = $(this).children( '.reading-progress-inner' ).data( 'progress-end' );
                                
                                    if (reading_progress_selector.length) {  
                                        
                                        let postTopOffset = reading_progress_selector.offset().top,                 // Position of top of element
                                            postBottomOffset = postTopOffset + reading_progress_selector.height(),  // Position of bottom of element
                                            scrolltop = window.pageYOffset,                                         // Current Scroll Position
                                            windowHeight = $(window).height(),
                                            scrollPercentTop,
                                            scrollPercentBottom;

                                        if ( reading_progress_start === "top" ) {
                                           scrollPercentTop = (scrolltop - postTopOffset);
                                        } else if ( reading_progress_start == 'middle' ) {
                                            scrollPercentTop = (scrolltop + (windowHeight/2)) - postTopOffset;
                                        } else if ( reading_progress_start == 'bottom' ) {
                                            scrollPercentTop = (scrolltop + (windowHeight)) - postTopOffset;
                                        }
                                        
                                        if ( reading_progress_end === "top" ) {
                                            scrollPercentBottom = scrolltop - postBottomOffset;
                                        } else if ( reading_progress_end == 'middle' ) {
                                            scrollPercentBottom = (scrolltop + (windowHeight/2)) - postBottomOffset;
                                        } else if ( reading_progress_end == 'bottom' ) {
                                            scrollPercentBottom = (scrolltop + (windowHeight)) - postBottomOffset;
                                        }                                        

                                        if (scrollPercentTop >= 0) {

                                            let scale = 1 - (scrollPercentBottom / (scrollPercentBottom - scrollPercentTop) );
 
                                            $(this).children( '.reading-progress-inner' ).css({ transform: "scaleX("+ (scale) + ")"});

                                            if (scrollPercentBottom >= 0) {
                                                $(this).children( '.reading-progress-inner' ).css({ transform: "scaleX(1)"});
                                            }

                                        } else {
                                            $(this).children( '.reading-progress-inner' ).css({ transform: "scaleX(0)"});
                                        }

                                    }
                                
                            }); 
                            
                        }); 
                
            };
        </script>
    <?php }

}

new ExtraReadingProgress();