<?php

class ExtraLottie extends OxygenExtraElements {
        
    var $js_added = false;
    var $css_added = false;
    
	function name() {
        return 'Lottie Animation';
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
    
    function init() {
        
        // include only for builder
        if (isset( $_GET['oxygen_iframe'] )) {
    
            add_action( 'wp_footer', array( $this, 'output_js' ) );
            add_action( 'wp_footer', array( $this, 'output_interactivity_js' ) );
            
        }
        
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
        $trigger = isset( $options['trigger'] ) ? $options["trigger"] : '';
        $speed = isset( $options['speed'] ) ? $options['speed'] : '';
        $play_hover = isset( $options['trigger'] ) && $options["trigger"] === "hover" ? 'hover' : '';
        $play_scroll = isset( $options['trigger'] ) && $options["trigger"] === "scroll" ? 'scrolling' : '';
        $play_cursor = isset( $options['trigger'] ) && $options["trigger"] === "cursor" ? 'cursor' : '';
        $controls = isset( $options['controls'] ) && $options["controls"] === "true"  ? 'controls' : '';
        $maybe_lazy = isset( $options['maybe_lazy'] ) && $options["maybe_lazy"] === "enable"  ? 'data-src' : 'src';
        $autoplay = isset( $options['trigger'] ) && $options["trigger"] === "always"  ? 'autoplay' : '';
        $play_mode = isset( $options['play_mode'] ) && $options["play_mode"] === "bounce"  ? 'mode="bounce"' : '';
        $id = esc_attr($options['selector']);
        $loop = (isset( $options['loop'] )) && ($options["loop"] === "true") && ($options["trigger"] != "click")  ? 'loop' : '';
        $start_frame = isset( $options['start_frame'] ) ? $options['start_frame'] : '0';
        $end_frame = isset( $options['end_frame'] ) ? $options['end_frame'] : '300';
        $scroll_container_selector = isset( $options['scroll_container_selector'] ) ? esc_attr($options['scroll_container_selector']) : esc_attr($options['selector']);
        $cursor_container_selector = isset( $options['cursor_container_selector'] ) ? esc_attr($options['cursor_container_selector']) : esc_attr($options['selector']);
        $mouseover_container_selector = esc_attr($options['maybe_mouseover_container']) === 'container' ? esc_attr($options['mouseover_container_selector']) : '#' .esc_attr($options['selector']);
        
        $renderer = isset( $options['renderer'] ) ? $options["renderer"] : 'svg';
        
        $maybe_offset = ($options['maybe_offset'] === 'yes') ? true : false;
        $offset_top = esc_attr($options['offset_top']);
        $offset_bottom = esc_attr($options['offset_bottom']);
        $click_trigger_selector = isset( $options['click_trigger_selector'] ) ? esc_attr($options['click_trigger_selector']) : '';
        
        $json_acf = isset( $options['json_acf'] ) ? esc_attr($options['json_acf']) : '';
        
        if ( !isset( $options['json_source'] ) || $options['json_source'] !== 'acf') {
            
            //$json_url = isset( $options['json_url'] ) ? esc_url($options['json_url']) : '';
            $json_url = $dynamic($options['json_url']);
            
        } else {
            
            $json_url = $json_acf && class_exists( 'acf' ) ? esc_url(get_field($json_acf)) : '';
            
        }
        
        
        $output = '<lottie-player renderer="'. $renderer .'" id="'. $id .'-lottie" '. $maybe_lazy .'="'.$json_url.'" '.$play_mode;  
        
        
        if (($options['maybe_scroll_container'] === 'yes') && ($options['trigger'] === 'scroll')) {
            $output .= ' data-container="'. $scroll_container_selector .'" ';
        }
        
        if (($options['maybe_cursor_container'] === 'container') && ($options['trigger'] === 'cursor')) {
            $output .= ' data-cursor-container="'. $cursor_container_selector .'" ';
        }
        
        if ($options['trigger'] === 'mouseover') {
            $output .= ' data-mouseover-container="'. $mouseover_container_selector .'" ';
        }
        
        $output .= ' data-offset="'. $maybe_offset .'" ';
        
        if ($options['maybe_offset'] === 'yes') {
            $output .= ' data-offset-top="'. $offset_top .'" ';
            $output .= ' data-offset-bottom="'. $offset_bottom .'" ';
        }
        
        if ($options['click_trigger'] === 'click_selector') {
            $output .= ' data-click-trigger="'. $click_trigger_selector .'" ';
        } else {
            $output .= ' data-click-trigger="self" ';
        }
        
        if ($options['second_click'] === 'reverse') {
         
            $output .= ' data-reverse="true" ';
            
        }
        
        
        $output .=  'data-trigger="'. $trigger .'" data-start="'. $start_frame .'"  data-end="'. $end_frame .'" speed="'.$speed.'" '.$play_hover.' '. $autoplay .' '.$loop.' '.$controls.' '.$play_cursor.' '.$play_scroll. ' ></lottie-player>';
        
        echo $output;
        
        $this->dequeue_scripts_styles();
        
        if( !method_exists('OxygenElement', 'builderInlineJS') ) {  // For users on before Oxygen v3.4 
        
         if (isset( $options['trigger'] ) && ($options["trigger"] === "scroll")) {
                     
             $this->El->inlineJS("
                        jQuery(document).ready(function($) { 
                               let  frameStart = $('#%%ELEMENT_ID%%').children('lottie-player').data('start'),
                                    frameEnd = $('#%%ELEMENT_ID%%').children('lottie-player').data('end');
                                    containerSelector = $('#%%ELEMENT_ID%%').children('lottie-player').data('container');

                                    if ($('#%%ELEMENT_ID%%').children('lottie-player').data('offset') == '1') {
                                        offsetBottom = ($('#%%ELEMENT_ID%%').children('lottie-player').data('offset-bottom')/100);
                                        offsetTop = (1 - ($('#%%ELEMENT_ID%%').children('lottie-player').data('offset-top')/100));
                                    } else {
                                        offsetBottom = '0';
                                        offsetTop = '1';
                                    }

                                    LottieInteractivity.create({
                                        mode:'scroll',
                                        player:'#%%ELEMENT_ID%%-lottie',
                                        container: containerSelector,
                                        actions: [
                                            {
                                              visibility:[0, offsetBottom],
                                              type: 'stop',
                                              frames: [frameStart]
                                            },
                                            {
                                              visibility: [offsetBottom, offsetTop],
                                              type: 'seek',
                                              frames: [frameStart, frameEnd]
                                            },
                                            {
                                              visibility: [offsetTop, 1],
                                              type: 'stop',
                                              frames: [frameEnd]
                                            },
                                        ]

                                });
                        });"
                    );
              }

             if (isset( $options['trigger'] ) && ($options["trigger"] == "cursor")) {
                 $this->El->inlineJS(
                        "jQuery(document).ready(function($) { 
                            let  frameStart = $('#%%ELEMENT_ID%%').children('lottie-player').data('start'),
                                    frameEnd = $('#%%ELEMENT_ID%%').children('lottie-player').data('end'),
                                    cursorSelector = $('#%%ELEMENT_ID%%').children('lottie-player').data('cursor-container');

                                LottieInteractivity.create({
                                        mode: 'cursor',
                                        player:'#%%ELEMENT_ID%%-lottie',
                                        container: cursorSelector,
                                          actions: [
                                            {
                                              position: { x: [0, 1], y: [0, 1] },
                                              type: 'seek',
                                              frames: [frameStart, frameEnd],
                                            },
                                        ],
                                }); 
                        }); 
                        "
                    );
             }
        
        
            if (isset( $options['trigger'] ) && ($options["trigger"] == "mouseover")) {
                 $this->El->inlineJS(
                        "jQuery(document).ready(function($) { 
                            let  frameStart = $('#%%ELEMENT_ID%%').children('lottie-player').data('start'),
                                    frameEnd = $('#%%ELEMENT_ID%%').children('lottie-player').data('end'),
                                    mouseoverSelector = $('#%%ELEMENT_ID%%').children('lottie-player').data('mouseover-container');    
                                
                                let lottiePlayer = $('#%%ELEMENT_ID%%').children('lottie-player')[0];
                                let anim = lottiePlayer.getLottie();
                                
                                anim.goToAndStop(frameStart, true);
                                
                                $(mouseoverSelector).hover(function(){
                                    anim.playSegments([[frameStart, frameEnd]], true);

                                }, function(){
                                    anim.setDirection(-1);
                                    anim.play();
                            }); 

                        }); 
                        "
                    );
             }
        
        if (isset( $options['trigger'] ) && ($options["trigger"] == "click")) {
            
            if (isset( $options['second_click'] ) && ($options["second_click"] == "reverse")) {
                     
                 $this->El->inlineJS("
                            jQuery(document).ready(function($) { 
                               
                            
                               let clickAnim = $('#%%ELEMENT_ID%%').children('lottie-player')[0];
                               let clickAnimLottie = clickAnim.getLottie();
                               let  frameStart = $('#%%ELEMENT_ID%%').children('lottie-player').data('start'),
                                    frameEnd = $('#%%ELEMENT_ID%%').children('lottie-player').data('end'),
                                    triggerSelector = $('#%%ELEMENT_ID%%');
                                    
                                    if ( $('#%%ELEMENT_ID%%').children('lottie-player').data('click-trigger') == 'self' ) {
                                         triggerSelector = $('#%%ELEMENT_ID%%');
                                    } else {
                                         triggerSelector = $($('#%%ELEMENT_ID%%').children('lottie-player').data('click-trigger'));
                                    }
                               
                                  clickAnim.seek(frameStart);
                                  
                                  triggerSelector.click( function( e ) {    
                                   clickAnim = !clickAnim; 
                                      if(clickAnim) { 
                                        clickAnimLottie.playSegments([[frameEnd, frameStart]], true);
                                        
                                      } else { 
                                        clickAnimLottie.playSegments([[frameStart, frameEnd]], true);
                                      } 
                                } );
                        });"
                    );
                
                } else {
                
                    $this->El->inlineJS("
                            jQuery(document).ready(function($) { 
                               var clickAnim = $('#%%ELEMENT_ID%%').children('lottie-player')[0];
                               var clickAnimLottie = clickAnim.getLottie();
                               let  frameStart = $('#%%ELEMENT_ID%%').children('lottie-player').data('start'),
                                    frameEnd = $('#%%ELEMENT_ID%%').children('lottie-player').data('end'),
                                    triggerSelector = $('#%%ELEMENT_ID%%');
                                    
                                if ( $('#%%ELEMENT_ID%%').children('lottie-player').data('click-trigger') == 'self' ) {
                                         triggerSelector = $('#%%ELEMENT_ID%%');
                                    } else {
                                         triggerSelector = $($('#%%ELEMENT_ID%%').children('lottie-player').data('click-trigger'));
                                    }    
                               
                                  clickAnim.seek(frameStart);
                                  
                                  triggerSelector.click( function( e ) {
                                        clickAnimLottie.playSegments([[frameStart, frameEnd]], true);
                                })
                        });"
                    );
                
                }
            
            }
            
        } else { // inline JS inside builder only for users on Oxygen 3.4+
            
            if (isset( $options['trigger'] ) && ($options["trigger"] === "scroll")) {
                     
             $this->El->builderInlineJS("
                        jQuery(document).ready(function($) { 
                               let  frameStart = $('#%%ELEMENT_ID%%').children('lottie-player').data('start'),
                                    frameEnd = $('#%%ELEMENT_ID%%').children('lottie-player').data('end');
                                    containerSelector = $('#%%ELEMENT_ID%%').children('lottie-player').data('container');
                                    
                                    let player = $('#%%ELEMENT_ID%%').children('lottie-player')[0];
                                    let JSON = $('#%%ELEMENT_ID%%').children('lottie-player').data('src');
                                    if (JSON !== undefined) {
                                     player.load(JSON);
                                    }  

                                    if ($('#%%ELEMENT_ID%%').children('lottie-player').data('offset') == '1') {
                                        offsetBottom = ($('#%%ELEMENT_ID%%').children('lottie-player').data('offset-bottom')/100);
                                        offsetTop = (1 - ($('#%%ELEMENT_ID%%').children('lottie-player').data('offset-top')/100));
                                    } else {
                                        offsetBottom = '0';
                                        offsetTop = '1';
                                    }

                                    LottieInteractivity.create({
                                        mode:'scroll',
                                        player:'#%%ELEMENT_ID%%-lottie',
                                        container: containerSelector,
                                        actions: [
                                            {
                                              visibility:[0, offsetBottom],
                                              type: 'stop',
                                              frames: [frameStart]
                                            },
                                            {
                                              visibility: [offsetBottom, offsetTop],
                                              type: 'seek',
                                              frames: [frameStart, frameEnd]
                                            },
                                            {
                                              visibility: [offsetTop, 1],
                                              type: 'stop',
                                              frames: [frameEnd]
                                            },
                                        ]

                                });
                        });"
                    );
              }

             else if (isset( $options['trigger'] ) && ($options["trigger"] == "cursor")) {
                 $this->El->builderInlineJS(
                        "jQuery(document).ready(function($) { 
                            let  frameStart = $('#%%ELEMENT_ID%%').children('lottie-player').data('start'),
                                    frameEnd = $('#%%ELEMENT_ID%%').children('lottie-player').data('end'),
                                    cursorSelector = $('#%%ELEMENT_ID%%').children('lottie-player').data('cursor-container');
                                    
                                    let player = $('#%%ELEMENT_ID%%').children('lottie-player')[0];
                                    let JSON = $('#%%ELEMENT_ID%%').children('lottie-player').data('src');
                                    if (JSON !== undefined) {
                                     player.load(JSON);
                                    }

                                LottieInteractivity.create({
                                        mode: 'cursor',
                                        player:'#%%ELEMENT_ID%%-lottie',
                                        container: cursorSelector,
                                          actions: [
                                            {
                                              position: { x: [0, 1], y: [0, 1] },
                                              type: 'seek',
                                              frames: [frameStart, frameEnd],
                                            },
                                        ],
                                }); 
                        }); 
                        "
                    );
             }
        
        
            else if (isset( $options['trigger'] ) && ($options["trigger"] == "mouseover")) {
                 $this->El->builderInlineJS(
                        "jQuery(document).ready(function($) { 
                            let  frameStart = $('#%%ELEMENT_ID%%').children('lottie-player').data('start'),
                                    frameEnd = $('#%%ELEMENT_ID%%').children('lottie-player').data('end'),
                                    mouseoverSelector = $('#%%ELEMENT_ID%%').children('lottie-player').data('mouseover-container');  
                                    
                                    
                                    let player = $('#%%ELEMENT_ID%%').children('lottie-player')[0];
                                    let JSON = $('#%%ELEMENT_ID%%').children('lottie-player').data('src');
                                    if (JSON !== undefined) {
                                     player.load(JSON);
                                    }
                                
                                let lottiePlayer = $('#%%ELEMENT_ID%%').children('lottie-player')[0];
                                
                                let anim = lottiePlayer.getLottie();
                                
                                anim.goToAndStop(frameStart, true);
                                
                                $(mouseoverSelector).hover(function(){
                                    anim.playSegments([[frameStart, frameEnd]], true);

                                }, function(){
                                    anim.setDirection(-1);
                                    anim.play();
                            }); 

                        }); 
                        "
                    );
             }
        
        else if (isset( $options['trigger'] ) && ($options["trigger"] == "click")) {
            
            if (isset( $options['second_click'] ) && ($options["second_click"] == "reverse")) {
                     
                 $this->El->builderInlineJS("
                            jQuery(document).ready(function($) { 
                            
                                let clickAnim = $('#%%ELEMENT_ID%%').children('lottie-player')[0];
                               let  frameStart = $('#%%ELEMENT_ID%%').children('lottie-player').data('start'),
                                    frameEnd = $('#%%ELEMENT_ID%%').children('lottie-player').data('end'),
                                    triggerSelector = $('#%%ELEMENT_ID%%');
                                    
                                    let player = $('#%%ELEMENT_ID%%').children('lottie-player')[0];
                                    let JSON = $('#%%ELEMENT_ID%%').children('lottie-player').data('src');
                                    
                                    if (JSON !== undefined) {
                                     player.load(JSON);
                                    }
                                    
                                    let clickAnimLottie = clickAnim.getLottie();
                                    
                                    if ( $('#%%ELEMENT_ID%%').children('lottie-player').data('click-trigger') == 'self' ) {
                                         triggerSelector = $('#%%ELEMENT_ID%%');
                                    } else {
                                         triggerSelector = $($('#%%ELEMENT_ID%%').children('lottie-player').data('click-trigger'));
                                    }
                               
                                  clickAnim.seek(frameStart);
                                  
                                  triggerSelector.click( function( e ) {    
                                   clickAnim = !clickAnim; 
                                      if(clickAnim) { 
                                        clickAnimLottie.playSegments([[frameEnd, frameStart]], true);
                                        
                                      } else { 
                                        clickAnimLottie.playSegments([[frameStart, frameEnd]], true);
                                      } 
                                } );
                        });"
                    );
                
                } else {
                
                    $this->El->builderInlineJS("
                            jQuery(document).ready(function($) { 
                            
                                
                               var clickAnim = $('#%%ELEMENT_ID%%').children('lottie-player')[0];
                               
                               let  frameStart = $('#%%ELEMENT_ID%%').children('lottie-player').data('start'),
                                    frameEnd = $('#%%ELEMENT_ID%%').children('lottie-player').data('end'),
                                    triggerSelector = $('#%%ELEMENT_ID%%');
                                    
                                    let player = $('#%%ELEMENT_ID%%').children('lottie-player')[0];
                                    let JSON = $('#%%ELEMENT_ID%%').children('lottie-player').data('src');
                                    if (JSON !== undefined) {
                                     player.load(JSON);
                                    }
                                    
                                var clickAnimLottie = clickAnim.getLottie();    
                                    
                                if ( $('#%%ELEMENT_ID%%').children('lottie-player').data('click-trigger') == 'self' ) {
                                         triggerSelector = $('#%%ELEMENT_ID%%');
                                    } else {
                                         triggerSelector = $($('#%%ELEMENT_ID%%').children('lottie-player').data('click-trigger'));
                                    }    
                               
                                  clickAnim.seek(frameStart);
                                  
                                  triggerSelector.click( function( e ) {
                                        clickAnimLottie.playSegments([[frameStart, frameEnd]], true);
                                })
                        });"
                    );
                
                }
            
            }
            
            else {
                
                $this->El->builderInlineJS("
                            jQuery(document).ready(function($) { 
                            
                              let player = $('#%%ELEMENT_ID%%').children('lottie-player')[0];
                              let JSON = $('#%%ELEMENT_ID%%').children('lottie-player').data('src');
                                if (JSON !== undefined) {
                                 player.load(JSON);
                                }
                        });"
                    );
                
            }
            
            // Minified version for the front end to appear once in footer (only users on Oxygen v3.4+)
            /*$this->El->footerJS(
            'function oxygen_init_lottie(t){t(".oxy-lottie-animation").each(function(){let e=t(this).children("lottie-player"),o=e.attr("id"),i=e[0],a=i.getLottie(),s=e.data("trigger"),r=e.data("start"),n=e.data("end"),c=e.data("reverse");if("scroll"===s){let t=e.data("container");"1"===e.data("offset")?(offsetBottom=e.data("offset-bottom")/100,offsetTop=1-e.data("offset-top")/100):(offsetBottom="0",offsetTop="1"),LottieInteractivity.create({mode:"scroll",player:"#"+o,container:t,actions:[{visibility:[0,offsetBottom],type:"stop",frames:[r]},{visibility:[offsetBottom,offsetTop],type:"seek",frames:[r,n]},{visibility:[offsetTop,1],type:"stop",frames:[n]}]})}if("cursor"===s){let e=t(this).children("lottie-player").data("cursor-container");LottieInteractivity.create({mode:"cursor",player:"#"+o,container:e,actions:[{position:{x:[0,1],y:[0,1]},type:"seek",frames:[r,n]}]})}if("mouseover"===s){let e=t(this).children("lottie-player").data("mouseover-container");a.goToAndStop(r,!0),t(e).hover(function(){a.playSegments([[r,n]],!0)},function(){a.setDirection(-1),a.play()})}if("click"===s){let o=t(this);o="self"===e.data("click-trigger")?t(this):t(e.data("click-trigger")),i.seek(r),!0===c?o.click(function(t){(i=!i)?a.playSegments([[n,r]],!0):a.playSegments([[r,n]],!0)}):o.click(function(t){a.playSegments([[r,n]],!0)})}})}jQuery(document).ready(oxygen_init_lottie);'
            ); */
            
        }
        
        
         // add JavaScript code only once and if shortcode presented
        if ($this->js_added !== true) {
                add_action( 'wp_footer', array( $this, 'output_js' ) );
                add_action( 'wp_footer', array( $this, 'output_interactivity_js' ) );
                add_action( 'wp_footer', array( $this, 'output_init_js' ) );
            $this->js_added = true;
        }
        
    }

    function class_names() {
        return array();
    }
    
   

    function controls() {
        
        
        /**
         * JSON Source
         */
        $json_source_control = $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'JSON Source',
                'slug' => 'json_source'
            )
        )->setValue(array( "manual" => "URL", "acf" => "ACF Field" ));
        $json_source_control->setDefaultValue('manual');
        
        

        
        /**
         * JSON URL
         */
        $lottieselector = "lottie-player";

        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('URL'),
                "slug" => 'json_url',
                "default" => 'https://assets2.lottiefiles.com/private_files/lf30_kd6vmszk.json',
                "condition" => 'json_source!=acf'
            )
        )->rebuildElementOnChange()
         ->setParam('dynamicdatacode', '<div optionname="\'oxy-lottie-animation_json_url\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');

        
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('ACF Field'),
                "slug" => 'json_acf',
                "default" => '',
                "condition" => 'json_source=acf'
            )
        );
       
      
        
        
         /**
         * Interactivity
         */
        
        $interactivity_section = $this->addControlSection("interactivity_section", __("Interactivity"), "assets/icon.png", $this);
        
        $interactivity_section->addOptionControl(
            array(
                "type" => "dropdown",
                "name" => "Animation Control",
                "slug" => "trigger",
                "default" => 'always',
            )
        )->setValue(
           array( 
                "click" => "Click",
                "always" => "Autoplay", 
                "hover" => "Hover", 
                "scroll" => "Scroll", 
                "cursor" => "Cursor Position",
                "mouseover" => "Mouse Over"
           )
       )->setValueCSS( array(
            "click"  => " {
                        cursor: pointer;
                    }
                    
               ",
        ) )->rebuildElementOnChange();
        
        
        $interactivity_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Mouse over which element...',
                'slug' => 'maybe_mouseover_container',
                'condition' => 'trigger=mouseover'
            )
        )->setValue(array( "container" => "Container", "element" => "Element Itself" ))
        ->setDefaultValue('element')
        ->rebuildElementOnChange();
        
        $interactivity_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Container selector'),
                "slug" => 'mouseover_container_selector',
                "default" => '',
                "condition" => 'maybe_mouseover_container=container&&trigger=mouseover'
            )
        );
        
        
        $interactivity_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Cursor position inside of..',
                'slug' => 'maybe_cursor_container',
                'condition' => 'trigger=cursor'
            )
        )->setValue(array( "container" => "Container", "element" => "Element Itself" ))
        ->setDefaultValue('element')
        ->rebuildElementOnChange();
        
        $interactivity_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Container selector'),
                "slug" => 'cursor_container_selector',
                "default" => '',
                "condition" => 'maybe_cursor_container=container&&trigger=cursor'
            )
        );
        
        
        $interactivity_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Play Mode',
                'slug' => 'play_mode',
                'condition' => 'trigger!=scroll&&trigger!=cursor&&trigger!=click&&trigger!=mouseover'
            )
        )->setValue(array( "normal" => "Normal", "bounce" => "Bounce" ))
            ->setDefaultValue('normal')
            ->rebuildElementOnChange();
        
        
        $interactivity_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Second click will..',
                'slug' => 'second_click',
                'condition' => 'trigger=click'
            )
        )->setValue(array( "normal" => "Play Again", "reverse" => "Play in Reverse" ))
        ->setDefaultValue('normal')
        ->rebuildElementOnChange();
        
        $interactivity_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('When user clicks..'),
                'slug' => 'click_trigger',
                'condition' => 'trigger=click'
            )
        )->setValue(array( 
            "click_self" => "This Element", 
            "click_selector" => "Another Element" 
            )
        )
        ->setDefaultValue('click_self')
        ->rebuildElementOnChange();
        
        
        $interactivity_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Click Selector'),
                "slug" => 'click_trigger_selector',
                "default" => '',
                "condition" => 'click_trigger=click_selector&&trigger=click'
            )
        );
        
        
        $interactivity_section->addOptionControl(
            array(
                "type" => 'slider-measurebox',
                "name" => __('Start Frame'),
                "slug" => 'start_frame',
                "value" => '0',
                'condition' => 'trigger=scroll||trigger=cursor||trigger=click||trigger=mouseover'
            )
        )->setRange('0','500','1')->rebuildElementOnChange();
        
        $interactivity_section->addOptionControl(
            array(
                "type" => 'slider-measurebox',
                "name" => __('End Frame'),
                "slug" => 'end_frame',
                "value" => '60',
                'condition' => 'trigger=scroll||trigger=cursor||trigger=click||trigger=mouseover'
            )
        )->setRange('0','500','1')->rebuildElementOnChange(); 
        
        $interactivity_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Sync scroll to visibility of..',
                'slug' => 'maybe_scroll_container',
                'condition' => 'trigger=scroll'
            )
        )->setValue(array( "yes" => "Container", "no" => "Element Itself" ))
        ->setDefaultValue('no')
        ->rebuildElementOnChange();
        
        $interactivity_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Container Selector'),
                "slug" => 'scroll_container_selector',
                "default" => '.oxy-lottie-animation',
                "condition" => 'maybe_scroll_container=yes&&trigger=scroll'
            )
        );
        
        
        $interactivity_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Scroll Animation Offset',
                'slug' => 'maybe_offset',
                'condition' => 'trigger=scroll'
            )
        )->setValue(array( "yes" => "Add Offset", "no" => "No Offset" ))
        ->setDefaultValue('no')
        ->rebuildElementOnChange();
    
        
        $interactivity_section->addOptionControl(
            array(
                "type" => 'slider-measurebox',
                "name" => __('Offset Top'),
                "slug" => 'offset_top',
                "default" => '0',
                'condition' => 'maybe_offset=yes&&trigger=scroll'
            )
        )
        ->setUnits('%','%')
        ->setRange('0','100','1')
        ->rebuildElementOnChange();
        
        
        $interactivity_section->addOptionControl(
            array(
                "type" => 'slider-measurebox',
                "name" => __('Offset Bottom'),
                "slug" => 'offset_bottom',
                "default" => '0',
                'condition' => 'maybe_offset=yes&&trigger=scroll'
            )
        )
        ->setUnits('%','%')
        ->setRange('0','100','1')
        ->rebuildElementOnChange();
        
        
        $interactivity_section->addOptionControl(
            array(
                "type" => 'slider-measurebox',
                "name" => __('Animation Speed'),
                "slug" => 'speed',
                "value" => '1',
                'condition' => 'trigger!=scroll&&trigger!=cursor'
            )
        )->setRange('0.1','5','.01')->rebuildElementOnChange();
        
        
        $interactivity_section->addOptionControl(
            array(
                "type" => 'checkbox',
                "name" => __('Loop','oxygen'),
                "slug" => 'loop',
                "value" => 'true',
                'condition' => 'trigger!=scroll&&trigger!=cursor&&trigger!=click&&trigger!=mouseover'
            )
        )->rebuildElementOnChange();
        
        
        $interactivity_section->addOptionControl(
            array(
                "type" => 'checkbox',
                "name" => __('Show Controls','oxygen'),
                "slug" => 'controls',
                "value" => 'false'
            )
        );
        
        
        
        
        $lottie_selectors = ' , lottie-player';
        
        
        $this->addStyleControl( 
            array(
                "type" => 'measurebox',
                "default" => "300",
                "units" => 'px',
                "property" => 'width',
                "control_type" => 'slider-measurebox',
                "selector" => $lottie_selectors,
            )
        )
        ->setRange('0','1000','1');
        
        $this->addStyleControl( 
            array(
                "type" => 'measurebox',
                //"default" => "300",
                "units" => 'px',
                "property" => 'height',
                "control_type" => 'slider-measurebox',
                "selector" => $lottie_selectors,
            )
        )
        ->setRange('0','1000','1');
        
        
        
        
        
        /**
         * config
         */
        
        $config_section = $this->addControlSection("config_section", __("Config"), "assets/icon.png", $this);
        
        
        if( method_exists('OxygenElement', 'builderInlineJS') ) { // Must be on Oxygen v3.4+
            $config_section->addOptionControl(
                array(
                    "type" => "buttons-list",
                    "name" => __('Lazy load'),
                    "slug" => "maybe_lazy",
                )
            )->setValue(array( "enable" => "Enable", "disable" => "Disable" ))
            ->setDefaultValue('enable')->rebuildElementOnChange();
        }
        
        $config_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Renderer',
                'slug' => 'renderer'
            )
        )->setValue(array( "svg" => "SVG", "canvas" => "Canvas" ))
         ->setDefaultValue('svg')->rebuildElementOnChange()->setParam("description", __("SVG is recommended for most animations. Only switch to canvas if flickering"));
        

    }
    
    
    function customCSS($options, $selector) {
    
        $css = "";
        
        if( ! $this->css_added ) {
        
            $css .= ".oxy-lottie-animation {
                        width: 300px;
                        max-width: 100%;
                    }

                    lottie-player {
                        height: 100%;
                        width: 100%;
                    }

                    lottie-player .main {
                        width: 100%;
                    }";
            
            $this->css_added = true;
            
        }
        
        return $css;
    }
    
    
    function output_js() {

       wp_enqueue_script( 'lottie-js', plugin_dir_url(__FILE__) . 'assets/lottie-player.min.js', '', '1.0.1' );
        
    }
    
    function output_interactivity_js() {
        
        wp_enqueue_script( 'lottie-interactivity-js', plugin_dir_url(__FILE__) . 'assets/lottie-interactivity.js', '', '1.0.0' );
        
    }
    
    function output_init_js() {
        wp_enqueue_script( 'intersection-js', plugin_dir_url( __FILE__ ) . 'assets/intersectionobserver.js', '', '1.0.0', true );
        wp_enqueue_script( 'lottie-init-js', plugin_dir_url(__FILE__) . 'assets/lottie-init.js', '', '1.0.0' );
        
    }
    
}

add_filter("oxy_base64_encode_options", 
    function($items) { 
        $items=array_merge($items, array(
            'oxy-lottie-animation_json_url'
        )); 
        return $items;
    }
);

new ExtraLottie();