<?php

class ExtraAlert extends OxygenExtraElements {
    
    var $js_added = false;
    var $css_added = false;

	function name() {
        return __('Alert Box'); 
    }
    
    function icon() {
        return plugin_dir_url(__FILE__) . 'assets/icons/'.basename(__FILE__, '.php').'.svg';
    }
    
    
    function tag() {
        return array('default' => 'div' );
    }
    
    function enablePresets() {
        return true;
    }
    
    function enableFullPresets() {
        return true;
    }
    
    function init() {
        $this->enableNesting();
    }
    
    
    function extras_button_place() {
        return "interactive";
    }
    
    
    function render($options, $defaults, $content) {
        
        $alert_closing_duration = isset( $options['alert_closing_duration'] ) ? esc_attr($options['alert_closing_duration']) : "";
        $alert_reveal = isset( $options['alert_reveal'] ) ? esc_attr($options['alert_reveal']) : "";
        $alert_type = isset( $options['alert_type'] ) ? esc_attr($options['alert_type']) : "";
        $alert_trigger = isset( $options['alert_trigger'] ) ? esc_attr($options['alert_trigger']) : "";
        $click_selector = isset( $options['click_selector'] ) ? esc_attr($options['click_selector']) : "";
        
        
        // icons
        $icon  = isset( $options['icon'] ) ? esc_attr($options['icon']) : "";

        global $oxygen_svg_icons_to_load;
        $oxygen_svg_icons_to_load[] = $icon;
        
        
          if ($content) {

            if ( function_exists('do_oxygen_elements') ) {
                echo do_oxygen_elements($content); 
            }
            else {
                echo do_shortcode($content); 
            } 

          } 

          echo '<span class="alert-box_icon" ';    

                if(isset($options['show_again'])) {
                    echo 'data-open-again="' . esc_attr( $options['show_again'] ) . '"';
                }

                if(isset($options['show_days'])) {
                    echo 'data-open-again-after-days="' . esc_attr( $options['show_days'] ) . '"';
                }
        
                if (isset( $options['alert_closing'] ) && $options["alert_closing"] === "slide" ) {
                    echo 'data-close="' . esc_attr( $options['alert_closing'] ) . '"';  
                }
        
                if('click' === $alert_trigger) {
                    echo 'data-clickselector="' . $click_selector . '"';
                }
        
        
        echo 'data-duration="' . $alert_closing_duration . '"';  
        
        echo 'data-reveal="' . $alert_reveal . '"';
        
        echo 'data-trigger="' . $alert_trigger . '"';

        echo '>';

          if (isset( $options['display_icon'] ) && $options["display_icon"] === "show" ) { ?>  
            <svg class="oxy-close-alert" id="<?php echo esc_attr($options['selector']); ?>-open-icon"><use xlink:href="#<?php echo $icon; ?>"></use></svg>
          <?php } 

      echo '</span>';
        
      $this->dequeue_scripts_styles();
        
      // add JavaScript code only once and if shortcode presented
        if ($this->js_added !== true) {
            if (!defined('OXY_ELEMENTS_API_AJAX') || !OXY_ELEMENTS_API_AJAX) {
                add_action( 'wp_footer', array( $this, 'output_js' ) );
            }
            $this->js_added = true;
        }    
        
        
        
        if( method_exists('OxygenElement', 'builderInlineJS') ) {
            // This is inline so when user adds alert box in header it will be full width for them without having to reload.
            $this->El->builderInlineJS("jQuery('#%%ELEMENT_ID%%').closest('.oxy-header-container').addClass('oxy-alert-box_inside');"); 
            
        } else {
            // Users on pre Oxygen v3.4 will have this on front end also.
            $this->El->inlineJS("jQuery('#%%ELEMENT_ID%%').parent('.oxy-header-center').parent('.oxy-header-container').addClass('oxy-alert-box_inside');");     
        }
        
    }

    function class_names() {
        return array();
    }
    
    function description() {
        ob_start(); ?>
        
            <div class=oxygen-control-label><?php echo __( "If the default close icon is removed, any element inside the alert box with 'oxy-close-alert' class can act as a close button." ) ?></div>

        <?php 

        return ob_get_clean();
    }

    function controls() {
        
        /**
         * Alert Type
         */
        $this->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => 'Alert Type',
                'slug' => 'alert_type'
            )
            
        )->setValue(
            array( 
            "default" => "Default", 
            //"welcome" => "Welcome mat (before all elements)",
            "header" => "Header notice (inside header row)",
            //"fixed" => "Fixed",
            )
        )
         ->setDefaultValue('default')
         ->setValueCSS( array(
            "welcome"  => " {
                height: 100vh;
                width: 100%;
                will-change: height, display;
                z-index: 1000;
            }",
             "header"  => " {
                width: 100%;
                will-change: height, display;
            }",
        ) );
        
        
         $this->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Alert trigger'),
                'slug' => 'alert_trigger',
                'condition' => 'alert_type=default'
            )
            
        )->setValue(
            array( 
                "pageload" => "On page load",
                "click" => 'On click',
                "custom" => 'Custom'
            )
        )->setDefaultValue('pageload');
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Click selector'),
                "slug" => 'click_selector',
                "default" => '.alert-trigger',
                "condition" => 'alert_type=default&&alert_trigger=click',
                "base64" => true,
            )
        );
        
        
        
        /**
         * Show Again
         */
        $this->addOptionControl(
            array(
                "type" => "dropdown",
                "name" => "Once the user has closed the alert",
                "slug" => "show_again",
                "default" => 'always_show',
            )
        )->setValue(
           array( 
                "always_show" => "Show again each time",
                "never_show_again" => "Never show again",
                "show_again_after" => "Show again only after:",
           )
       );
        
       $this->addOptionControl(
           array(
                "type" => 'measurebox',
                "name" => __('Show Again After:'),
                "slug" 	    => "show_days",
                "default" => "3",
                "control_type" => 'slider-measurebox',
                "condition"		=> "show_again=show_again_after",
            )
        )
        ->setUnits('days','days');
        
        
        
        
        
        /**
         * Alert Style controls
         */
        $this->addStyleControl(
            array(
                "property" => 'background-color',
            )
        );
        
        $this->addStyleControl(
            array(
                "property" => 'height',
                'condition' => 'alert_type=welcome'
            )
        );
        
        $this->addStyleControl(
            array(
                "property" => 'width',
            )
        );
        
        
        $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Visibility in Builder',
                'slug' => 'builder_display')

        )->setValue(array( "hidden" => "Hidden", "visible" => "Visible" ))
         ->setDefaultValue('visible')
         ->setValueCSS( array(
            "hidden"  => " {
                --builder-visibility: none;
            }",
        ) );
        
        /**
         * Gutenberg support
         */
        if( class_exists( 'Oxygen_Gutenberg' ) ) {
        
            $this->addOptionControl(
                array(
                    'type' => 'buttons-list',
                    'name' => 'Visibility in Gutenberg',
                    'slug' => 'gutenberg_display')

            )->setValue(array( "hidden" => "Hidden", "visible" => "Visible" ))
             ->setDefaultValue('hidden');
            
        }
        
        
        
        
        
        /**
         * Icon
         */
        
        $icon = $this->addControlSection("icon", __("Close Icon"), "assets/icon.png", $this); 
        
         $icon_solid_selector = '.alert-box_icon';
        
        
        $icon_size = $icon->addStyleControl(
                array(
                    "name" => __('Icon Size'),
                    "slug" => "icon_size",
                    "selector" => $icon_solid_selector,
                    "control_type" => 'slider-measurebox',
                    "value" => '24',
                    "property" => 'font-size',
                    "condition" => 'display_icon=show',
                )
        );
        $icon_size->setRange(4, 72, 1);
        
        
        $icon->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Icon Display (Need to Apply Params)',
                'slug' => 'display_icon'
            )
        )->setValue(array( "show" => "Show Icon", "hide" => "Use Custom" ))
         ->setDefaultValue('show');
        
        $html = $this->description('desc',__("Description","oxygen"));
        $icon->addCustomControl($html, 'desc');
        
        
        $icon_choose = $icon->addControlSection("icon_choose", __("Change Icon"), "assets/icon.png", $this);
        
        $icon_choose->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Icon'),
                "slug" => 'icon',
                "value" => 'Lineariconsicon-cross',
                "condition" => 'display_icon=show',
            )
        );
        
        $icon_color_section = $icon->addControlSection("icon_color_section", __("Colors"), "assets/icon.png", $this);
        
        $icon_color_section->addStyleControl(
            array(
                "property" => 'background-color',
                //"default" => '',
                "selector" => $icon_solid_selector,
                "condition" => 'display_icon=show',
            )
        );
        
        $icon_color_section->addStyleControl(
            array(
                "property" => 'color',
                "default" => '',
                "selector" => $icon_solid_selector,
                "condition" => 'display_icon=show',
            )
        );
        
        $icon_spacing_section = $icon->addControlSection("icon_spacing_section", __("Layout / Spacing"), "assets/icon.png", $this);
        
        $icon_spacing_section->addPreset(
            "padding",
            "icon_padding",
            __("Padding"),
            $icon_solid_selector
        )->whiteList();
        
        $icon_spacing_section->addPreset(
            "padding",
            "icon_margin",
            __("Margin"),
            $icon_solid_selector
        )->whiteList();
        
        $icon_spacing_section->addStyleControl(
            array(
                "property" => 'top',
                "default" => '10',
                "selector" => $icon_solid_selector,
            )
        );
        
        $icon_spacing_section->addStyleControl(
            array(
                "property" => 'left',
                "selector" => $icon_solid_selector,
            )
        );
        
        $icon_spacing_section->addStyleControl(
            array(
                "property" => 'right',
                "default" => '10',
                "selector" => $icon_solid_selector,
            )
        );
        
        $icon_spacing_section->addStyleControl(
            array(
                "property" => 'bottom',
                "selector" => $icon_solid_selector,
            )
        );
        
        $icon->borderSection('Borders', $icon_solid_selector,$this);
        $icon->boxShadowSection('Shadows', $icon_solid_selector,$this); 
       
        
        
        /**
         * Inner
         */
        
        $inner_selector = '';

        $spacing_section = $this->addControlSection("spacing_section", __("Layout / Spacing"), "assets/icon.png", $this);
        
        $spacing_section->flex('', $this);
        
        $spacing_section->addStyleControls(
            array(
                array(
                    "name" => 'Padding Left',
                    "property" => 'padding-left',
                    "control_type" => "measurebox",
                    "unit" => "px",
                    "value" => '30'
                ),
                array(
                    "name" => 'Padding Right',
                    "property" => 'padding-right',
                    "control_type" => "measurebox",
                    "unit" => "px",
                    "value" => '30'
                ),
                array(
                    "name" => 'Padding Top',
                    "property" => 'padding-top',
                    "control_type" => "measurebox",
                    "unit" => "px",
                    "value" => '30'
                ),
                array(
                    "name" => 'Padding Bottom',
                    "property" => 'padding-bottom',
                    "control_type" => "measurebox",
                    "unit" => "px",
                    "value" => '30'
                )
            )
        );
        
        /**
         * Closing / Reveal
         */
        $closing_section = $this->addControlSection("closing_section", __("Closing / Reveal"), "assets/icon.png", $this);
        
        $closing_section->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => 'Alert closing',
                'slug' => 'alert_closing'
            )
            
        )->setValue(
            array( 
            "fade" => "Fade Out", 
            "slide" => "Slide Up",    
            )
        )
         ->setDefaultValue('fade');
        
        $closing_section->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => 'Alert reveal',
                'slug' => 'alert_reveal',
            )
            
        )->setValue(
            array( 
            "none" => "None",
            "fade" => "Fade In", 
            "slide" => "Slide Down",    
            )
        )
         ->setDefaultValue('none');
        
        
        $closing_section->addOptionControl(
           array(
                "type" => 'slider-measurebox',
                "name" => __('Duration'),
                "slug" 	    => "alert_closing_duration",
                "default" => "300",
            )
        )
        ->setUnits('ms','ms')
        ->setRange(0, 1000, 1);
        
        
        
        
        /**
         * Advanced
         */
        //$trigger_section = $this->addControlSection("trigger_section", __("Triggers"), "assets/icon.png", $this);
        
       
        
        
        
    }
    
    
    function customCSS($options, $selector) {
        
        $css = '';
        
        if( ! $this->css_added ) {
        
            $css = ".oxy-alert-box {
                        display: none;
                        position: relative;
                        max-width: 100%;
                        margin: 0;
                        padding: 30px;
                    }

                    .show-alert {
                        display: inline-flex;
                    }

                    .oxy-alert-box_inside .oxy-alert-box {
                        width: 100%;
                    }

                    .oxygen-builder-body .oxy-alert-box {
                        --builder-visibility: inline-flex;
                        display: var(--builder-visibility);
                    }

                    .alert-box-inner {
                        display: flex;
                    }

                    .alert-box_icon {
                       display: inline-flex;
                        position: absolute;
                        top: 10px;
                        right: 10px;
                    }

                    .alert-box_icon svg {
                        fill: currentColor;
                        width: 1em;
                        height: 1em;
                        cursor: pointer;
                    }

                    .oxy-alert-box_inside.oxy-header-container {
                        padding-left: 0;
                        padding-right: 0;
                    }

                    .oxygen-builder-body .oxy-alert-box_inside.oxy-header-container > div:empty {
                        min-width: 0;
                    }
                    
                    ";
            
             $this->css_added = true;
            
          }
        
            // Maybe visible in gutenberg
            if ((isset($options["oxy-alert-box_gutenberg_display"]) && $options["oxy-alert-box_gutenberg_display"] === "visible")) {
             
                $css .= ".oxy-alert-box.oxygenberg-element {
                            display: inline-flex;
                        }"; 
            }
        
        
           // Maybe change header row
            if ((isset($options["oxy-alert-box_alert_type"]) && $options["oxy-alert-box_alert_type"] === "header")) {
             
                $css .= ".oxy-alert-box_inside .oxy-header-container {
                            padding: 0;
                            width: 100%;
                        }
                        
                        .oxy-alert-box_inside > div {
                            flex-grow: 1;
                        }

                        .oxy-alert-box_inside > div:empty {
                            flex-grow: 0;
                        }"; 
            }
                
        
        return $css;
    }
    
    function output_js() {

        wp_enqueue_script( 'intersection-js', plugin_dir_url( __FILE__ ) . 'assets/intersectionobserver.js', '', '1.0.0', true );
    
    ?>
            
            <script type="text/javascript">
            jQuery(document).ready(oxygen_init_alert);
            function oxygen_init_alert($) {
                
                // Show function
                var showAlert = function ( alert ) {
                 
                    var $alert = jQuery( alert );
                    var alertData = $alert.children('.alert-box_icon');
                    var duration = parseInt(alertData.data('duration'));
                    
                    if ('slide' === alertData.data( 'reveal' )) {
                            //$alert.css("display", "inline-flex");
                            $alert.slideDown({
                              duration: duration,    
                              start: function () {
                                $(this).css({
                                  display: "inline-flex",
                                })
                              }
                            });
                        } else if ('fade' === alertData.data( 'reveal' )) {
                            $alert.css("display", "inline-flex");
                            $alert.hide();
                            $alert.fadeIn( duration )
                        } else {
                            $alert.addClass('show-alert');
                        }
                    
                }
                
                // Close function
                var closeAlert = function ( alert ) {
                    
                    var $alert = jQuery( alert );
                    var duration = parseInt($alert.children('.alert-box_icon').data('duration'));
                    
                
                    if ( $alert.find('.alert-box_icon').data( 'close' ) === 'slide') {
                        $alert.slideUp(duration);
                     } else {
                         $alert.fadeOut(duration);
                     }
                    
                }
                
                // Expose functions for external use
                window.extrasShowAlert = showAlert;
                window.extrasCloseAlert = closeAlert;
                
                
                // Helper function to maybe reshow alert
                function maybeshowAlert( alert ) {
                    var $alert = jQuery( alert );
                    var alertData = $alert.children('.alert-box_icon');
                    var alertId = $alert[0].id;

                    // Current and last time in milliseconds
                    var currentTime = new Date().getTime();
                    var lastShownTime = localStorage && localStorage['oxy-' + alertId + '-last-shown-time'] ? JSON.parse( localStorage['oxy-' + alertId + '-last-shown-time'] ) : false;
                   
                        switch( alertData.data( 'open-again' ) ) {
                            case 'never_show_again':
                                // if it was shown at least once, don't show it again
                                if( lastShownTime !== false ) return;
                                break;
                            case 'show_again_after':
                                var settingDays = parseInt( alertData.data( 'open-again-after-days' ) );
                                var actualDays = ( currentTime - lastShownTime ) / ( 60*60*24*1000 );
                                if( actualDays < settingDays ) return;
                                break;
                            default:
                                //always show
                                break;
                        }
                   
                    // save current time as last shown time
                    if( localStorage ) localStorage['oxy-' + alertId + '-last-shown-time'] = JSON.stringify( currentTime );
                    
                    // Display alert
                    showAlert( alert );
                   
                }
                
                // Loop through all found alerts on page
                $( ".oxy-alert-box" ).each(function( index ) {
                    
                    var alert = this;
                    
                    $(alert).closest('.oxy-header-container').addClass('oxy-alert-box_inside');
                    
                    if (('custom' !== $(alert).children('.alert-box_icon').data('trigger')) && ('click' !== $(alert).children('.alert-box_icon').data('trigger'))) {
                    
                         (function( alert ){
                                var $alert = $( alert );

                                // Maybe show each alert
                                maybeshowAlert( alert );

                            })( alert );
                        
                    }
                    
                    if ('click' === $(alert).children('.alert-box_icon').data('trigger')) {
                     
                        var clickSelector = $(alert).children('.alert-box_icon').data('clickselector');
                        
                        $(clickSelector).on( 'click', function(e) {    
                            e.stopPropagation();
                            e.preventDefault();
                            
                            maybeshowAlert( alert );
                            
                        } );
                        
                    }
                    

                });
                
                
                // Close button function
                $( '.oxy-close-alert, .alert-box_icon' ).click( function(event) {
                     event.preventDefault();
                    
                     closeAlert($(this).closest('.oxy-alert-box')[0]);
                } );
                
            }
                
        </script>

    <?php }

}

new ExtraAlert();