<?php

class ExtraHotspot extends OxygenExtraElements {
        
    var $js_added = false;
    var $css_added = false;
    
	function name() {
        return 'Hotspots';
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
        
        add_action("ct_toolbar_component_settings", array( $this, "add_popover_button"), 1);
    
    }
    
    
    function add_popover_button() { ?>

        <div class="oxygen-control-row"
            ng-show="isActiveName('oxy-hotspots')&&!hasOpenTabs('oxy-hotspots')">
                <div class="oxygen-add-section-element"
                    ng-click="iframeScope.addComponent('ct_image')">
                    <img class="extras-ui-icon-padding" src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/image.svg' />
                    <img class="extras-ui-icon-padding" src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/image-active.svg' />
                    <?php _e("Add Image","oxygen"); ?>
                </div>
                <div class="oxygen-add-section-element"
                    ng-click="iframeScope.addComponent('oxy-popover')">
                    <img class="extras-ui-icon-padding" src='<?php echo plugin_dir_url(__FILE__) ?>assets/icons/other/hotspots.svg' />
                    <img class="extras-ui-icon-padding" src='<?php echo plugin_dir_url(__FILE__) ?>assets/icons/other/hotspots-active.svg' />
                    <?php _e("Popover Marker","oxygen"); ?>
                </div>
        </div>

    <?php }

    function render($options, $defaults, $content) {
        
        $maybe_hotspot_nav = isset( $options['maybe_hotspot_nav'] ) ? esc_attr($options['maybe_hotspot_nav']) : "";
        
        $close_selector = isset( $options['close_selector'] ) ? esc_attr($options['close_selector']) : "";
        $next_selector = isset( $options['next_selector'] ) ? esc_attr($options['next_selector']) : "";
        $prev_selector = isset( $options['prev_selector'] ) ? esc_attr($options['prev_selector']) : "";
        
        
        $first_hotspot_open = isset( $options['first_hotspot_open'] ) ? esc_attr($options['first_hotspot_open']) : "";
        //$maybe_cycle = isset( $options['maybe_cycle'] ) ? esc_attr($options['maybe_cycle']) : "";
        //$cycle_delay = isset( $options['cycle_delay'] ) ? esc_attr($options['cycle_delay']) : "";
        
        
        
        $output = '';
        
        if ($content) {
            
            if ( function_exists('do_oxygen_elements') ) {
                $output .= do_oxygen_elements($content); 
            }
            else {
                $output .= do_shortcode($content); 
            } 
            
        }
        
        $output .= '<div class="oxy-hotspots_data" ';
        
        $output .= 'data-first-open="'. $first_hotspot_open .'" ';
        
        $output .= 'data-nav="'. $maybe_hotspot_nav .'" ';
        
        if ('true' === $maybe_hotspot_nav) {
            
            $output .= 'data-close="'. $close_selector .'" ';
            $output .= 'data-next="'. $next_selector .'" ';
            $output .= 'data-prev="'. $prev_selector .'" ';
            
        }
        
        $output .= '></div>';
        
        echo $output;

        $this->dequeue_scripts_styles();
        
         // add JavaScript code only once and if shortcode presented
        if ($this->js_added !== true) {
                add_action( 'wp_footer', array( $this, 'output_init_js' ), 25 );
            $this->js_added = true;
        }
        
        
    
    }
    
    
    

    function class_names() {
        return array();
    }

    function controls() {
        
        $popover_marker_selector = '.oxy-popover_marker';
        $popover_popup_selector = '.oxy-popover_popup-inner';
        
        /*$this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('In-Builder Visibility'),
                'slug' => 'builder_visibility'
            )
            
        )->setValue(array( 
            "visible" => "Visible", 
            "hidden" => "Hidden" 
        ))->setDefaultValue('visible');*/
        
        
        /*
        
        $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Hotspot Type'),
                'slug' => 'hotspot_type'
            )
            
        )->setValue(array( 
            "true" => "Enable", 
            "false" => "Disable" 
        ))->setDefaultValue('enable');
        
        */
        
        /**
         * Tour
         */ 
        //$tour_section = $this->addControlSection("tour_section", __("Hotspot Tours"), "assets/icon.png", $this);
        
        $this->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Hotspots open on page load'),
                'slug' => 'first_hotspot_open'
            )
            
        )->setValue(array( 
            "first" => "First", 
            "all" => "All",
            "none" => "None"
        ))->setDefaultValue('none');
        
        
        
        
        $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Allow user to navigate between hotspots'),
                'slug' => 'maybe_hotspot_nav'
            )
            
        )->setValue(array( 
            "true" => "Enable", 
            "false" => "Disable" 
        ))->setDefaultValue('false');
        
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Close all hotspots selector'),
                "slug" => 'close_selector',
                "default" => '.close-hotspots',
                "condition" => 'maybe_hotspot_nav=true'
            )
        );
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Next hotspot selector'),
                "slug" => 'next_selector',
                "default" => '.next-hotspot',
                "condition" => 'maybe_hotspot_nav=true'
            )
        );
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Previous hotspot selector'),
                "slug" => 'prev_selector',
                "default" => '.previous-hotspot',
                "condition" => 'maybe_hotspot_nav=true'
            )
        );
        
        
        
        $this->addStyleControl( 
            array(
                "name" => __('Disabled navigation opacity'),
                "property" => 'opacity',
                "selector" => '[data-disabled="true"]',
                "condition" => 'maybe_hotspot_nav=true'
            )
        );
        
        /*
        
        $tour_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Auto cycle through hotspots'),
                'slug' => 'maybe_cycle'
            )
            
        )->setValue(array( 
            "true" => "Enable", 
            "false" => "Disable" 
        ))->setDefaultValue('false');
        
        
        $tour_section->addOptionControl(
            array(
                "type" => 'slider-measurebox',
                "name" => __('Go to next hotspot every x seconds..'),
                "slug" => 'cycle_delay',
                "condition" => 'maybe_cycle=true'
            )
        )->setRange('0','10','.1')
         ->setUnits('s','s');
        
       */

    }
    
    
    
    function customCSS($options, $selector) {
        
        $css = "";
        
        $css .= "$selector.oxy-hotspots {
                    position: relative;
                }";
        
        if (! $this->css_added ) {
        
            $css .= ".oxy-hotspots {
                        position: relative;
                        width: 100%;
                    }

                    .oxy-hotspots > .ct-image {
                        vertical-align: middle;
                        width: 100%;
                    }

                    .oxygen-builder-body .oxy-hotspots .oxy-popover {
                        position: absolute;
                        left: var(--extras-popover-x);
                        top: var(--extras-popover-y);
                    }

                    .oxy-hotspots .oxy-popover_marker {
                        position: absolute;
                        left: var(--extras-popover-x);
                        top: var(--extras-popover-y);
                    }

                    .oxygen-builder-body .oxy-hotspots .oxy-popover_marker {
                        position: static;
                        left: 0;
                        top: 0;
                    }

                    .oxy-hotspots > .oxy-dynamic-list:first-child {
                        position: absolute;
                        height: 100%;
                        width: 100%;
                    }

                    .oxygen-builder-body .oxy-hotspots > .oxy-dynamic-list:first-child > .ct-div-block:not(.oxy_repeater_original) {
                        display: none!important;
                    }

                    .oxygen-builder-body .oxy-hotspots > .oxy-dynamic-list-edit:first-child .oxy_repeater_original {
                        position: static;
                    }

                    .oxygen-builder-body .oxy-hotspots > .oxy-dynamic-list:first-child {
                        pointer-events: none;
                    }

                    .oxygen-builder-body .oxy-hotspots > .oxy-dynamic-list:first-child > * {
                        pointer-events: auto;
                    }

                    .oxy-hotspots [data-disabled='true'] {
                        pointer-events: none;
                    }

                    ";
            
            $this->css_added = true;
            
        }
        
        return $css;
        
    } 
    
    
    function output_init_js() { ?>
            
        <script type="text/javascript">
        jQuery(document).ready(oxygen_hotspots);
        function oxygen_hotspots($) {
            
                $('.oxy-hotspots').each(function(i, oxyHotSpots){

                        let hotData = $(oxyHotSpots).find('.oxy-hotspots_data');
                        let totalPopovers = $(oxyHotSpots).find('.oxy-popover').length;
                        let firstOpen = hotData.data('first-open');
                    
                        setTimeout(function(){

                            $(oxyHotSpots).find('.oxy-popover').each(function(i, oxyHotSpotsPopovers){
                                
                                let instance = $(oxyHotSpotsPopovers).find('.oxy-popover_marker')[0]._tippy;
                                
                                 if (true === hotData.data('nav')) {
                            
                                    let prevPopover, nextPopover;

                                        if ($(oxyHotSpotsPopovers).closest('.oxy-dynamic-list').length) {
                                             prevPopover = $(oxyHotSpotsPopovers).parent('.ct-div-block').prev('.ct-div-block').find('.oxy-popover').find('.oxy-popover_marker');
                                             nextPopover = $(oxyHotSpotsPopovers).parent('.ct-div-block').next('.ct-div-block').find('.oxy-popover').find('.oxy-popover_marker');
                                        } else {
                                            prevPopover = $(oxyHotSpotsPopovers).prev('.oxy-popover').find('.oxy-popover_marker');
                                            nextPopover = $(oxyHotSpotsPopovers).next('.oxy-popover').find('.oxy-popover_marker');
                                        }

                                    let previnstance = prevPopover.length ? prevPopover[0]._tippy : null;
                                    let nextinstance = nextPopover.length ? nextPopover[0]._tippy : null;

                                     instance.setProps({ 
                                        onShow(instance) {
                                            
                                            if (instance.popper.querySelector(hotData.data('next')) != null) {
                                                    $(instance.popper).find(hotData.data('next')).on('click', function(event) {
                                                    event.preventDefault();
                                                    instance.hide();
                                                    if (null != nextinstance) {
                                                        nextinstance.show();
                                                    }
                                                })
                                            }
                                            if (instance.popper.querySelector(hotData.data('prev')) != null) {     
                                                $(instance.popper).find(hotData.data('prev')).on('click', function(event) {    
                                                     event.preventDefault();
                                                    instance.hide();
                                                    if (null != previnstance) {
                                                        previnstance.show();
                                                    }
                                                })
                                            }
                                            if (instance.popper.querySelector(hotData.data('close')) != null) {
                                                $(instance.popper).find(hotData.data('close')).on('click', function(event) {
                                                     event.preventDefault();
                                                    instance.hide();
                                                })  
                                            }
                                            
                                            // first hotspot
                                            if ('0' == i) {
                                                $(instance.popper).find(hotData.data('prev')).attr('data-disabled', 'true');
                                            }
                                            // last hotspot
                                            if( (totalPopovers - 1) == i) {
                                                $(instance.popper).find(hotData.data('next')).attr('data-disabled', 'true');
                                            }  
                                            
                                            },
                                        onAfterUpdate(instance) {
                                             
                                             // first hotspot show
                                            if (('0' == i && 'first' == firstOpen) || ('all' == firstOpen) ) {
                                                instance.show();
                                            }
                                                
                                          },
                                    });
                                }
                                
                            });
                            
                        }, 150 );

                    });    

            }</script>
        <?php }

}

new ExtraHotspot();






class Extrapopover extends OxygenExtraElements {
        
    var $js_added = false;
    var $css_added = false;
    
	function name() {
        return 'Popover';
    }
    
    
    function enablePresets() {
        return true;
    }
    
    function enableFullPresets() {
        return true;
    }
    
    function icon() {
        return plugin_dir_url(__FILE__) . 'assets/icons/other/'.basename(__FILE__, '.php').'.svg';
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
        
        $dynamic_position = $dynamic($options['dynamic_position']);
        $dynamic_popover_placement = $dynamic($options['dynamic_popover_placement']);
        $label_text = $dynamic($options['label_text']);
            
        $icon = isset( $options['icon'] ) ? esc_attr($options['icon']) : "";
        
        $inbuilder = (defined('OXY_ELEMENTS_API_AJAX') && OXY_ELEMENTS_API_AJAX) ? ' extras-in-builder' : '';
        
        $manual_popover_placement = isset( $options['popover_placement'] ) ? esc_attr($options['popover_placement']) : "";
        
        $maybe_placement_dynamic = isset( $options['maybe_placement_dynamic'] ) ? esc_attr($options['maybe_placement_dynamic']) : "";
        
        $popover_placement = 'true' === $maybe_placement_dynamic ? $dynamic_popover_placement : $manual_popover_placement;

        $maybe_showoncreate = isset( $options['maybe_showoncreate'] ) ? esc_attr($options['maybe_showoncreate']) : "";
        
        if ('true' === esc_attr($options['maybe_dynamic']) ) {
        
            $popover_positions = explode(",", $dynamic_position);
            $popover_position_x = $popover_positions[0];
            $popover_position_y = $popover_positions[1];
            
        }
        
        $offset_x = isset( $options['offset_x'] ) ? esc_attr($options['offset_x']) : "";
        $offset_y = isset( $options['offset_y'] ) ? esc_attr($options['offset_y']) : "";
        $maybe_flip = isset( $options['maybe_flip'] ) ? esc_attr($options['maybe_flip']) : "";
        
        $fallbacks = isset( $options['fallbacks'] ) ? esc_attr($options['fallbacks']) : "";
        
        $element_selector = isset( $options['element_selector'] ) ? esc_attr($options['element_selector']) : "";
        
        $prevent_dom_change = isset( $options['prevent_dom_change'] ) ? esc_attr($options['prevent_dom_change']) : "";
        
        $popover_content_source = isset( $options['popover_content_source'] ) ? esc_attr($options['popover_content_source']) : "";
        
        $interaction = isset( $options['interaction'] ) ? esc_attr($options['interaction']) : "";

        $move_transition = isset( $options['move_transition'] ) ? esc_attr($options['move_transition']) : "";

        $aria_label = isset( $options['aria_label'] ) ? esc_attr($options['aria_label']) : "";
        
        global $oxygen_svg_icons_to_load;
        $oxygen_svg_icons_to_load[] = $icon;
        
        $output = '';
        
        $output .= '<div class="oxy-popover_inner'. $inbuilder .'"' ;
        
        $output .= '>';
        
        $output .= '<button aria-label="'. $aria_label .'" class="oxy-popover_marker" ';
        
        if ('true' === esc_attr($options['maybe_dynamic']) ) {
        
            $output .= 'style="left: '. $popover_position_x .'; top: '. $popover_position_y .';" ';
            
        }
        
        
        $output .= '><span class="oxy-popover_marker-inner">';
        
        if ('true' === esc_attr($options['maybe_icon'])) {
        
            $output .= '<span class="oxy-popover_icon"><svg id="icon' . esc_attr($options['selector']) . '"><use xlink:href="#' . $icon .'"></use></svg></span>';
            
        }
        
        $output .= $label_text ? '<span class="oxy-popover_label">'. $label_text .'</span>' : '';
        
        $output .= '</span></button>';
        
        $output .= '<div class="oxy-popover_popup" id="oxy-popover_popup' . esc_attr($options['selector']) . '"' ;
        
        $output .= 'data-placement="'. $popover_placement .'" ';
        
        $output .= 'data-offsetx="'. $offset_x .'" ';
        
        $output .= 'data-offsety="'. $offset_y .'" ';
        
        $output .= 'data-flip="'. $maybe_flip .'" ';

        $output .= 'data-show="'. $maybe_showoncreate .'" ';

        $output .= 'data-move-transition="'. $move_transition .'" ';
        
        $output .= 'true' === $maybe_flip ? 'data-fallbacks="'. $fallbacks .'" ': ' ';
        
        if ('true' === esc_attr($options['maybe_dynamic']) ) {
            
            $output .= 'data-dynamic-position="'. $dynamic_position .'" ';
            
        }
        
        if ('true' === $prevent_dom_change ) {
            
            $output .= 'data-dom="'. $prevent_dom_change .'" ';
            
        }
        
        if (('selector' === esc_attr($options['trigger'])) && ('popover' === esc_attr($options['type']))) {
            
            $output .= 'data-elem-selector="'. $element_selector .'" ';
        
        }
        
        $output .= 'data-content-source="'. $popover_content_source .'" ';
        
        $output .= 'data-interaction="'. $interaction .'" ';
        
        
        
        
        $output .= '><div class="oxy-popover_popup-inner"><div class="oxy-popover_popup-content oxy-inner-content">';
        
        if ($content) {
            
            if ( function_exists('do_oxygen_elements') ) {
                $output .= do_oxygen_elements($content); 
            }
            else {
                $output .= do_shortcode($content); 
            }  
            
        }
        
        $output .= '</div></div></div>';
        
        $output .= '</div>';
        
        
        $output .= '';
        
        echo $output;

        $this->dequeue_scripts_styles();

        // Just for the builder
        if (defined('OXY_ELEMENTS_API_AJAX') && OXY_ELEMENTS_API_AJAX) {

            echo '<script type="text/javascript" src="'. plugin_dir_url(__FILE__) . 'assets/popper.min.js' .'"></script>';
            echo '<script type="text/javascript" src="'. plugin_dir_url(__FILE__) . 'assets/tippy.min.js' .'"></script>';
        }
        
         // add JavaScript code only once and if shortcode presented
        if ($this->js_added !== true) {
            if (!defined('OXY_ELEMENTS_API_AJAX') || !OXY_ELEMENTS_API_AJAX) {
                add_action( 'wp_footer', array( $this, 'output_js' ) );
                add_action( 'wp_footer', array( $this, 'output_init_js' ), 25 );
            }
            $this->js_added = true;
        }
        
        $inBuilderJS = "jQuery(document).ready(function($){
                            setTimeout(function(){
                            
                                let popPosition = $('#%%ELEMENT_ID%%').find('.oxy-popover_popup').data('dynamic-position');
                    
                                if (popPosition) {
                                    let splitCoords = popPosition.split(',');
                                    $('#%%ELEMENT_ID%%').css({'left': splitCoords[0], 'top': splitCoords[1]});
                                }
                                
                                tippy('#%%ELEMENT_ID%% .oxy-popover_marker', {
                                    render(instance) {
                                        const popper = $('#%%ELEMENT_ID%% .oxy-popover_popup')[0];
                                        return {
                                        popper,
                                        };
                                    },
                                    allowHTML: true,     
                                    interactive: true, 
                                    arrow: true,
                                    appendTo: $(this).next('.oxy-popover_popup')[0],
                                    placement: '%%popover_placement%%',
                                    maxWidth: 'none',    
                                    inertia: true,
                                    theme: 'extras',     
                                    trigger: 'click',
                                    hideOnClick: 'toggle',
                                    showOnCreate: %%maybe_showoncreate%%,
                                    moveTransition: 'transform %%move_transition%%ms ease-in',
                                    offset: [parseInt( %%offset_x%% ), parseInt( %%offset_y%% )],          
                                    popperOptions: {
                                            modifiers: [{
                                                name: 'flip',
                                                options: {
                                                    fallbackPlacements: ['%%popover_placement%%'],
                                                },
                                                },
                                            ],
                                        },
                                    });
                                
                            
                            }, 100 );
                    });
                    
                ";


        
        if( method_exists('OxygenElement', 'builderInlineJS') ) {
             $this->El->builderInlineJS($inBuilderJS);
        }
        
    
    }
    
    
    

    function class_names() {
        return array();
    }

    function controls() {
        
        
        $popover_marker_selector = '.oxy-popover_marker';
        $popover_outer_selector = '.oxy-popover_popup';
        $popover_popup_selector = '.oxy-popover_popup-inner';
        
        
        $this->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Type'),
                'slug' => 'type'
            )
        )->setValue(array( 
            //"tooltip" => "Tooltip", 
            "popover" => "Popover",
            "hotspot_marker" => "Hotspot marker"
        ))->setDefaultValue('hotspot_marker');
        
        
        $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Marker position'),
                'slug' => 'maybe_dynamic',
                "condition" => 'type!=popover'
            )
        )->setValue(array( 
            "false" => "Manual", 
            "true" => "Dynamic",
        ))->setDefaultValue('false');
        
        
        
        
        
        $this->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Element to trigger popover'),
                'slug' => 'trigger',
                "condition" => 'type=popover'
            )
        )->setValue(array( 
            "marker" => "Marker", 
            "selector" => "Another element on page",
        ))->setDefaultValue('marker')
          ->setValueCSS( array(
            "selector"  => ".oxy-popover_inner:not(.extras-in-builder) .oxy-popover_marker {
                                display: none;
                            }
                            
                            .oxy-popover_inner.extras-in-builder {
                                --inbuilder-visibility: none;
                            }"
        ) );
        
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Selector'),
                "default" => '.element',
                "slug" => 'element_selector',
                "base64" => true,
                "condition" => 'type=popover&&trigger=selector'
            )
        );
        
        
        $this->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Popover content'),
                'slug' => 'popover_content_source',
                "condition" => 'type=popover&&trigger=selector'
            )
        )->setValue(array( 
            "popover_content" => "Elements inside popover", 
            "custom_content" => "Manual (data-tippy-content attr)",
        ))->setDefaultValue('popover_content');
        
        
        $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('In-editor visibility'),
                'slug' => 'ineditor_visibility',
                "condition" => 'type=popover&&trigger=selector'
            )
        )->setValue(array( 
            "visible" => "Visible", 
            "hidden" => "Hidden",
        ))->setDefaultValue('marker')
          ->setValueCSS( array(
            "hidden"  => ".oxy-popover_inner.extras-in-builder {
                                display: var(--inbuilder-visibility);
                            }",
        ) )->setParam("description", __("The marker is visible in the builder to edit the popover. On the frontend, the popover will be attached to the new element(s)."));
        
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Horizontal %, Vertical %'),
                "default" => '50%,35%',
                "slug" => 'dynamic_position',
                "base64" => true,
                "condition" => 'type=hotspot_marker&&maybe_dynamic=true'
            )
        )->setParam('dynamicdatacode', '<div optionname="\'oxy-popover_dynamic_position\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');
        
        
        $this->addStyleControl( 
            array(
                "name" => __('Horizontal'), 
                "property" => '--extras-popover-x',
                "control_type" => 'slider-measurebox',
                 "condition" => 'type=hotspot_marker&&maybe_dynamic=false'
            )
        )
        ->setRange('-10','100','.0001')
        ->setUnits('%');
        
        
        $this->addStyleControl( 
            array(
                "name" => __('Vertical'), 
                "property" => '--extras-popover-y',
                "control_type" => 'slider-measurebox',
                "condition" => 'type=hotspot_marker&&maybe_dynamic=false'
            )
        )
        ->setRange('-10','100','.0001')
        ->setUnits('%');
        
        
        $this->addStyleControl( 
            array(
                "name" => __('Popover width'), 
                "property" => '--extras-popover-width',
                "control_type" => 'slider-measurebox',
                
            )
        )
        ->setRange('0','500','1')
        ->setUnits('px')
        ->setParam("description", __("Click the marker to view new placement"));
        
        
        /**
         * Marker
         */ 
        $marker_section = $this->addControlSection("marker_section", __("Marker"), "assets/icon.png", $this);


        
        
        $icon_selector = '.oxy-popover_icon';
        
        $marker_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Label Text'),
                "default" => '',
                "slug" => 'label_text',
                "base64" => true,
            )
        )->setParam('dynamicdatacode', '<div optionname="\'oxy-popover_label_text\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');
        
        $marker_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Aria label'),
                "default" => 'Open popover',
                "slug" => 'aria_label',
                "base64" => true,
            )
        )->setParam('dynamicdatacode', '<div optionname="\'oxy-popover_aria_label\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');
                
        $marker_icon_section = $marker_section->addControlSection("marker_icon_section", __("Icon"), "assets/icon.png", $this);

        $marker_icon_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Marker icon'),
                'slug' => 'maybe_icon',
            )
        )->setValue(array( 
            "true" => "Enable", 
            "false" => "Disable",
        ))->setDefaultValue('true')
          ->setValueCSS( array(
            "false"  => ".oxy-popover_icon {
                                display: none;
                            }",
        ) );
        
        
        $marker_icon_section->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Icon'),
                "slug" => 'icon',
                "value" => 'FontAwesomeicon-dot-circle-o',
                "condition" => 'maybe_icon=true'
            )
        );
        
        $marker_icon_section->addStyleControl(
            array(
                "name" => __('Icon Size'),
                "selector" => $icon_selector,
                "property" => 'font-size',
                "condition" => 'maybe_icon=true'
            )
        );
        
        $marker_icon_section->addStyleControl(
            array(
                "name" => __('Margin right'),
                "selector" => $icon_selector.":not(:only-child)",
                "property" => 'margin-right',
                "condition" => 'maybe_icon=true',
                "value" => '10'
            )
        );
        
        
        /**
         * spacing
         */ 
        $marker_spacing_section = $marker_section->addControlSection("marker_spacing_section", __("Spacing"), "assets/icon.png", $this);
        
        $marker_selector = '.oxy-popover_marker-inner';
        
        $marker_spacing_section->addPreset(
            "padding",
            "marker_padding",
            __("Marker padding"),
            $marker_selector
        )->whiteList();
        
        
        $marker_layout_section = $marker_section->addControlSection("marker_layout_section", __("Layout"), "assets/icon.png", $this);
        
        $marker_layout_section->flex($marker_selector, $this);
        
        $marker_section->borderSection('Borders', '.oxy-popover_marker',$this);
        $marker_section->boxShadowSection('Shadows', $marker_selector,$this);
        $marker_section->typographySection('Typography', $marker_selector,$this);
        
        
        
        $marker_section->addStyleControl(
            array(
                "name" => __('Color'),
                "property" => '--extras-marker-color',
                "control_type" => 'colorpicker',
            )
        )->setParam('hide_wrapper_end', true);
        
        $marker_section->addStyleControl(
            array(
                "name" => __('Background'),
                "property" => '--extras-marker-bg',
                "control_type" => 'colorpicker',
            )
        )->setParam('hide_wrapper_start', true);
        
        
        
        
        
        /**
         * Marker
         */ 
        $marker_hover_section = $this->addControlSection("marker_hover_section", __("Marker Hover/Focus"), "assets/icon.png", $this);
        
        $marker_hover_section->addStyleControl(
            array(
                "name" => __('Hover Color'),
                "property" => '--extras-marker-hover-color',
                "control_type" => 'colorpicker',
            )
        )->setParam('hide_wrapper_end', true);
        
        $marker_hover_section->addStyleControl(
            array(
                "name" => __('Focus Color'),
                "property" => '--extras-marker-focus-color',
                "control_type" => 'colorpicker',
            )
        )->setParam('hide_wrapper_start', true);
        
        $marker_hover_section->addStyleControl(
            array(
                "name" => __('Active Color'),
                "property" => '--extras-marker-active-color',
                "control_type" => 'colorpicker',
            )
        )->setParam('hide_wrapper_end', true);
        
        $marker_hover_section->addStyleControl(
            array(
                "name" => __('Hover background'),
                "property" => '--extras-marker-hover-bg',
                "control_type" => 'colorpicker',
            )
        )->setParam('hide_wrapper_start', true);
        
        $marker_hover_section->addStyleControl(
            array(
                "name" => __('Focus background'),
                "property" => '--extras-marker-focus-bg',
                "control_type" => 'colorpicker',
            )
        )->setParam('hide_wrapper_end', true);
        
        $marker_hover_section->addStyleControl(
            array(
                "name" => __('Active background'),
                "property" => '--extras-marker-active-bg',
                "control_type" => 'colorpicker',
            )
        )->setParam('hide_wrapper_start', true);
        
        
        $marker_hover_section->addStyleControl( 
            array(
                "name" => __('Scale'),
                "property" => '--extras-marker-scale',
                "control_type" => 'slider-measurebox',
            )
        )
        ->setRange('0.8','2','.01');
        
        
        $marker_hover_section->addStyleControl( 
            array(
                "name" => __('Transition duration'),
                "property" => 'transiton-duration',
                "control_type" => 'slider-measurebox',
                "selector" => $popover_marker_selector,
                "default" => '300'
            )
        )
        ->setRange('0','1000','1')
        ->setUnits('ms','ms');
        
        
        $marker_hover_section->borderSection('Hover Borders', $marker_selector.":hover",$this);
        $marker_hover_section->boxShadowSection('Hover Shadows', $marker_selector.":hover",$this);
        $marker_hover_section->borderSection('Focus Borders', $marker_selector.":focus",$this);
        $marker_hover_section->boxShadowSection('Focus Shadows', $marker_selector.":focus",$this);
        $marker_hover_section->borderSection('Active Borders', $marker_selector."[aria-expanded='true']",$this);
        $marker_hover_section->boxShadowSection('Active Shadows', $marker_selector."[aria-expanded='true']",$this);
        
        
        
        /**
         * popover
         */ 
        $popover_section = $this->addControlSection("popover_section", __("Popover Placement"), "assets/icon.png", $this);
        
        $popover_section->addCustomControl(
            '<div style="color: #fff; line-height: 1.3; font-size: 13px;">Set the direction of the popup placement in relation to the marker<hr style="opacity: .3;"></div>');
            
        
        $popover_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Marker placement'),
                'slug' => 'maybe_placement_dynamic',
            )
        )->setValue(array( 
            "false" => "Manual", 
            "true" => "Dynamic",
        ))->setDefaultValue('false');
            
        $popover_section->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Popover placement'),
                'slug' => 'popover_placement',
                'default' => 'auto',
                'condition' => 'maybe_placement_dynamic=false'
            )
        )->setValue(array( 
                'top' => __('Top'), 
				'right' => __('Right'), 
				'bottom' => __('Bottom'), 
				'left' => __('Left'), 
				'auto' 	=> __( 'Auto (Side with the most space)' ), 
				'auto-start' => __( 'Auto Start' ), 
				'auto-end' => __( 'Auto End' ),
				'top-start' => __( 'Top Start' ), 
				'top-end' => __( 'Top End' ),
				'right-start' => __( 'Right Start' ), 
				'right-end' => __( 'Right End' ),
				'bottom-start' => __( 'Bottom Start' ), 
				'bottom-end' => __( 'Bottom End' ),
				'left-start' => __( 'Left Start' ), 
				'left-end' => __( 'Left End' ),
            )
        );
        
        
        $popover_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Popover placement'),
                "default" => 'auto',
                "slug" => 'dynamic_popover_placement',
                "base64" => true,
                'condition' => 'maybe_placement_dynamic=true'
            )
        )->setParam('dynamicdatacode', '<div optionname="\'oxy-popover_dynamic_popover_placement\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');
        
        
        
        $popover_section->addOptionControl(
            array(
                "type" => 'measurebox',
                "name" => __('Offset X'),
                "slug" => 'offset_x',
                "default" => '0',
            )
        )->setUnits('px','px')
         ->setParam('hide_wrapper_end', true);
        
        $popover_section->addOptionControl(
            array(
                "type" => 'measurebox',
                "name" => __('Offset Y'),
                "slug" => 'offset_y',
                "default" => '10',
            )
        )->setUnits('px','px')
         ->setParam('hide_wrapper_start', true);
        
        
        $popover_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Allow to flip placement if out of viewport'),
                'slug' => 'maybe_flip',
            )
        )->setValue(array( 
            "true" => "Enable", 
            "false" => "Disable",
        ))->setDefaultValue('true')
          ->setParam("description", __("Recommend to leave enabled"));
        
        
        $popover_section->addOptionControl(
            array(
                'type' => 'textfield',
                'name' => __('Preferred fallback placements'),
                'slug' => 'fallbacks',
                'default' => 'auto',
                'condition' => 'maybe_flip=true'
            )
        )->setParam("description", __("(bottom, bottom-start, bottom-end, top, etc. Leave on auto for any direction)"));


        $popover_section->addOptionControl(
            array(
                'type' => 'measurebox',
                "name" => __('Placement movement transition'),
                'slug' => 'move_transition',
                'condition' => 'maybe_flip=true'
            )
        )->setUnits('ms','ms')
         ->setDefaultValue('0');
        
        
        /**
         * Inner Layout
         */
        //$layout_section = $this->addControlSection("layout_section", __("Popover Layout"), "assets/icon.png", $this);
        
        
        
        
        /**
         * Inner Layout
         */
        $popover_styles_section = $this->addControlSection("popover_spacing_section", __("Popover Styles"), "assets/icon.png", $this);
        
        $popover_content_selector = '.oxy-popover_popup-content';
        
        $popover_styles_section->addPreset(
            "padding",
            "popover_padding",
            __("Padding"),
            '.tippy-box[data-animation=fade][data-theme~="extras"], .extras-in-builder .oxy-popover_popup-content'
        )->whiteList();
        
        
        $popover_styles_section->addStyleControl(
                 array(
                    "name" => 'Color',
                    "control_type" => 'colorpicker',
                    "property" => '--extras-popover-color',
                )
        )->setParam('hide_wrapper_end', true);
        
        $popover_styles_section->addStyleControl(
                array(
                    "name" => 'Background',
                    "property" => '--extras-popover-bg',
                    "control_type" => 'colorpicker',
                )
        )->setParam('hide_wrapper_start', true);
        
        
        $popover_layout_section = $popover_styles_section->addControlSection("popover_layout_section", __("Inner layout"), "assets/icon.png", $this);
        $popover_layout_section->flex($popover_content_selector, $this);
        
        $popover_styles_section->boxShadowSection('Shadows', '.tippy-box[data-theme="extras"], .extras-in-builder .oxy-popover_popup-inner',$this);
        $popover_styles_section->typographySection('Typography', $popover_content_selector,$this);
        $popover_styles_section->borderSection('Borders', '.tippy-box[data-animation=fade][data-theme~="extras"], .extras-in-builder .oxy-popover_popup-content',$this);
        
        
        
        /**
         * animations
         */
        $animations_section = $this->addControlSection("animations_section", __("Popover Animation"), "assets/icon.png", $this);
        
        $container_selector = '.oxy-popover_inner';
        
        
        
        $animations_section->addStyleControl(
            array(
                "name" => 'Transition in',
                "property" => '--extras-popover-transitionin',
                "control_type" => 'measurebox',
            )
        )
        ->setUnits('ms','ms')->setParam('hide_wrapper_end', true);
        
        $animations_section->addStyleControl(
            array(
                "name" => 'Transition out',
                "property" => '--extras-popover-transitionout',
                "control_type" => 'measurebox',
            )
        )
        ->setUnits('ms','ms')->setParam('hide_wrapper_start', true);
        
          $animations_section->addStyleControl( 
            array(
                "name" => __('Translate X'),
                "property" => '--extras-popover-translatex',
                "control_type" => 'measurebox',
            )
        )->setUnits('px')->setParam('hide_wrapper_end', true);
        
        $animations_section->addStyleControl( 
            array(
                "name" => __('Translate Y'),
                "property" => '--extras-popover-translatey',
                "control_type" => 'measurebox',
                "value" => '20'
            )
        )->setUnits('px')->setParam('hide_wrapper_start', true);
        
        $animations_section->addStyleControl( 
            array(
                "name" => __('Scale'),
                "property" => '--extras-popover-scale',
                "control_type" => 'slider-measurebox',
                "value" => '.9'
            )
        )
        ->setRange('0.8','1.2','.01');
        
        
        $animations_section->addCustomControl(
            '<div style="color: #fff; line-height: 1.3; font-size: 13px;">Rotate<hr style="opacity: .25;"></div>','description');
        
        
        $animations_section->addStyleControl( 
            array(
                "name" => __('Rotate X'),
                "property" => '--extras-popover-rotatex',
                "control_type" => 'slider-measurebox',
            )
        )
        ->setRange('-100','100','1');
        
        $animations_section->addStyleControl( 
            array(
                "name" => __('Rotate Y'),
                "property" => '--extras-popover-rotatey',
                "control_type" => 'slider-measurebox',
            )
        )
        ->setRange('-100','100','1');
        
        $animations_section->addStyleControl( 
            array(
                "name" => __('Rotate Z'),
                "property" => '--extras-popover-rotatez',
                "control_type" => 'slider-measurebox',
            )
        )
        ->setRange('-100','100','1');
        
        $animations_section->addStyleControl( 
            array(
                "name" => __('Rotate angle'),
                "property" => '--extras-popover-rotatedeg',
                "control_type" => 'slider-measurebox',
            )
        )
        ->setRange('-360','360','1')
        ->setUnits('deg');
        

        
        /**
         * Pulse animation
         */ 
        $pulse_animation_section = $this->addControlSection("pulse_animation_section", __("Pulse Animation"), "assets/icon.png", $this);
        
        $pulse_animation_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Pulses'),
                'slug' => 'number_pulses',
            )
        )->setValue(array( 
            "single" => "Single", 
            "double" => "Double",
            "none" => "None",
        ))->setDefaultValue('double')
          ->setValueCSS( array(
            "single"  => ".oxy-popover_marker::before {
                                content: none;
                            }",
            "none"  => ".oxy-popover_marker::before,
                        .oxy-popover_marker::after {
                                content: none;
                            }",
        ) )->whiteList();
        
        
        $pulse_animation_section->addStyleControl(
            array(
                'control_type' => 'buttons-list',
                'name' => __('Remove on hover'),
                "property" => '--extras-pulse-pause',
            )
        )->setValue(array( 
            "none" => "True", 
            "block" => "False",
        ))->setDefaultValue('block');
        
        
        $pulse_animation_section->addStyleControl( 
            array(
                "name" => __('Pulse size'),
                "property" => '--extras-pulse-size',
                "control_type" => 'slider-measurebox',
            )
        )
        ->setRange('1','3','.01');
        
        
        $pulse_animation_section->addStyleControl( 
            array(
                "control_type" => 'colorpicker',
                "name" => __('Color'),
                "property" => '--extras-pulse-color',
            )
        );
        
        $pulse_animation_section->addStyleControl(
            array(
                "name" => __('Pulse duration'),
                "default" => "2",
                "property" => '--extras-pulse-duration',
                "control_type" => 'slider-measurebox',
            )
        )
        ->setUnits('s','s')
        ->setRange('0','4','.01');
        
        
        /**
         * advanced animation
         */ 
        $advanced_section = $this->addControlSection("advanced_section", __("Config"), "assets/icon.png", $this);
        
        $advanced_section->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('User interaction to open popover'),
                'slug' => 'interaction'
            )
        )->setValue(array( 
            "mouseenter focus" => "mouseenter focus", 
            "click" => "click (default)",
            "focusin" => "focusin",
            "mouseenter click" => "mouseenter click",
        ))->setDefaultValue('click');

        $advanced_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Open on page load'),
                'slug' => 'maybe_showoncreate'
            )
        )->setValue(array( 
            "true" => "Enable", 
            "false" => "Disable",
        ))->setDefaultValue('false');

        $advanced_section->addCustomControl(
            '<div style="color: #fff; line-height: 1.3; font-size: 13px;">Advanced<hr style="opacity: .25;"></div>','description');
        
        $advanced_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Prevent DOM Unmount'),
                'slug' => 'prevent_dom_change'
            )
        )->setValue(array( 
            "true" => "Enable", 
            "false" => "Disable",
        ))->setDefaultValue('false')
         ->setValueCSS( array(
            "true"  => " ",
        ) )
        ->setParam("description", __("Useful if you're using dynamic elements inside popover that break if DOM changes (note - arrows not supported)"));
        
        

    }
    
    
    
    function customCSS($options, $selector) {
        
        $css = '';
        
        if (! $this->css_added ) {
        
            $css .= ".oxygen-builder-body .oxy-hotspots .oxy-popover {
                        transform: translate(-50%,-50%);
                        -webkit-transform: translate(-50%,-50%);
                    }
                    
                    .oxy-hotspots .oxy-popover_inner {
                        position: absolute;
                        top: 0;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        pointer-events: none;
                    }
                    
                    .oxy-hotspots .oxy-popover_inner > * {
                        pointer-events: auto;
                    }
            
                    .oxy-popover_icon {
                        display: flex;
                        pointer-events: none;
                    }
                    
                    .oxy-popover_icon:not(:only-child) {
                        margin-right: 10px;
                    }

                    .oxy-popover_icon svg {
                        height: 1em;
                        width: 1em;
                        fill: currentColor;
                    }

                    .oxy-popover_marker {
                        background: none;
                        color: inherit;
                        position: relative;
                        cursor: pointer;
                        box-shadow: none;
                        border: none;
                        will-change: transform;
                        transition: all 300ms ease;
                        padding: 0;
                        font-size: 14px;
                    }
                    
                    .oxy-hotspots .oxy-popover_marker {
                        transform: translate(-50%,-50%);
                        -webkit-transform: translate(-50%,-50%);
                    }
                    
                    .oxygen-builder-body .oxy-popover_marker {
                        transform: none;
                        -webkit-transform: none;
                    }
                    
                    .oxy-popover_marker-inner {
                        background: var(--extras-marker-bg);
                        color: var(--extras-marker-color);
                        padding: 10px;
                        display: flex;
                        flex-direction: row;
                        align-items: center;
                        position: relative;
                        z-index: 1;
                        border-radius: inherit;
                        transition: all 300ms ease;
                    }
                    
                    .oxy-popover_marker:hover {
                        transform: scale(var(--extras-marker-scale));
                        -webkit-transform: scale(var(--extras-marker-scale));
                    }
                    
                    .oxy-hotspots .oxy-popover_marker:hover {
                        transform: translate(-50%,-50%) scale(var(--extras-marker-scale));
                        -webkit-transform: translate(-50%,-50%) scale(var(--extras-marker-scale));
                    }
                    
                    .oxygen-builder-body .oxy-popover_marker:hover {
                        transform: scale(var(--extras-marker-scale));
                        -webkit-transform: scale(var(--extras-marker-scale));
                    }
                    
                    
                    
                    .oxy-popover_marker:hover .oxy-popover_marker-inner {
                        background: var(--extras-marker-hover-bg);
                        color: var(--extras-marker-hover-color);
                    }
                    
                    .oxy-popover_marker:focus .oxy-popover_marker-inner {
                        background: var(--extras-marker-hover-bg);
                        color: var(--extras-marker-hover-color);
                    }
                    
                    .oxy-popover_marker[aria-expanded='true'] .oxy-popover_marker-inner {
                        background: var(--extras-marker-active-bg);
                        color: var(--extras-marker-active-color);
                    }
                    
                    
                   :root {
                        --extras-popover-rotatex: 0;
                        --extras-popover-rotatey: 0;
                        --extras-popover-rotatez: 0;
                        --extras-popover-rotatedeg: 0deg;
                        --extras-pulse-duration: 2s;
                        --extras-pulse-color: rgba(0,0,0,0.4);
                        --extras-pulse-size: 1.4;
                        --extras-popover-transitionout: 300ms;
                        --extras-popover-transitionin: 300ms;
                        --extras-popover-bg: #fff;
                        --extras-popover-color: #111;
                        --extras-pulse-pause: none;
                        --extras-marker-scale: 1;
                        --extras-popover-width: 200px;
                        --extras-marker-bg: #fff;
                        --extras-popover-translatex: 0;
                        --extras-popover-translatey: 20px;
                        --extras-popover-scale: .9;
                        --extras-marker-active-bg: #fff;
                        --extras-marker-hover-bg: #fff;
                    }

                    .oxygen-builder-body .oxy-popover_inner {
                        position: relative;
                    }
                    
                    .oxygen-builder-body .oxy-popover_inner {
                        will-change: opacity, transform;
                    }

                    .oxy-popover_popup {
                        display: flex;
                        visibility: hidden;
                        position: absolute;
                    }
                    
                    .oxy-popover_popup-inner {
                         width: var(--extras-popover-width);
                    }
                    
                    .oxy-popover_popup-inner img {
                        max-width: 100%;
                        height: auto;
                    }
                    
                    .oxy-popover .tippy-arrow {
                        color: var(--extras-popover-bg);
                    }

                    .oxy-popover_popup-content {
                        background-color: var(--extras-popover-bg);
                        color: var(--extras-popover-color);
                        border-radius: 3px;
                        padding: 20px;
                        font-size: 16px;
                        positon: relative;
                        display: flex;
                        flex-direction: column;
                    }

                    .oxygen-builder-body .oxy-popover_popup-content:empty {
                        min-height: 100px;
                        min-width: 100px;
                    }

                    .oxy-popover_popup.oxy-popover_popup-reveal .oxy-popover_popup-inner {
                        opacity: 1;
                        visibility: visible;
                        transform: none;
                    }
                    
                    .oxy-popover_marker[aria-expanded='true'] + .oxy-popover_popup .tippy-box[data-theme~='extras'],
                    .oxygen-builder-body .oxy-popover_marker[aria-expanded='true'] + .oxy-popover_popup .oxy-popover_popup-inner {
                        visibility: visible;
                        transition-duration: var(--extras-popover-transitionin);
                    }
                    
                    .oxy-popover_marker[aria-expanded='false'] + .oxy-popover_popup .tippy-box[data-theme~='extras'],
                    .oxygen-builder-body .oxy-popover_marker[aria-expanded='false'] + .oxy-popover_popup .oxy-popover_popup-inner,
                    .oxy-popover_popup[data-elem-selector] .tippy-box[data-theme~='extras'][data-state='hidden']{
                          opacity: 0;
                          transform: translate(var(--extras-popover-translatex),var(--extras-popover-translatey)) scale(var(--extras-popover-scale)) rotate3d(var(--extras-popover-rotatex),var(--extras-popover-rotatey),var(--extras-popover-rotatez),var(--extras-popover-rotatedeg));
                          transition-duration: var(--extras-popover-transitionout);
                    }
                    
                    .oxygen-builder-body .oxy-popover_popup-inner {
                        transition: all var(--extras-popover-transitionin) ease;
                        will-change: opacity, transform;
                    }
                    
                    
                    .oxygen-builder-body .oxy-popover_popup {
                        position: absolute;
                    }
                    
                    /* Pulsing Effect */

                    .oxy-popover_marker::after,
                    .oxy-popover_marker::before {
                            content: '';
                            display: block;
                            position: absolute;
                            pointer-events: none;
                            -webkit-animation: oxy-popover_pulse var(--extras-pulse-duration) infinite;
                            animation: oxy-popover_pulse var(--extras-pulse-duration) infinite;
                            left: 0;
                            top: 0;
                            width: 100%;
                            height: 100%;
                            border-radius: inherit;
                            -webkit-backface-visibility: hidden;
                            will-change: opacity, transform;
                            opacity: 0;
                            background: var(--extras-pulse-color);
                    }

                    .oxy-popover_marker:hover::after,
                    .oxy-popover_marker:hover::before,
                    .oxy-popover_marker[aria-expanded='true']::after, 
                    .oxy-popover_marker[aria-expanded='true']::before {
                        display: var(--extras-pulse-pause);
                    }
                    
                    .oxy-popover_marker::before {
                        animation-delay: calc(var(--extras-pulse-duration)/4);
                        -webkit-animation-delay: calc(var(--extras-pulse-duration)/4);
                    }


                    @keyframes oxy-popover_pulse {
                          0% {
                            opacity: .8;
                            transform: scale(1);
                          }
                          100% {
                            opacity: 0;
                            transform: scale(var(--extras-pulse-size));
                          }
                        }

                        @-webkit-keyframes oxy-popover_pulse {
                          0% {
                            opacity: .8;
                            -webkit-transform: scale(1);
                          }
                          100% {
                            opacity: 0;
                            -webkit-transform: scale(var(--extras-pulse-size));;
                          }
                        } 

                    .oxy-popover_marker[aria-describedby] + .oxy-popover_popup .oxy-popover_popup-inner,
                    .oxy-popover_marker[aria-expanded='true'] + .oxy-popover_popup .oxy-popover_popup-inner {
                        transform: none;
                         opacity: 1;
                         visibility: visible;
                    }
                    
                    .oxy-popover .tippy-content {
                        padding: 0;
                    }
                    
                    .tippy-box[data-animation=fade][data-theme~='extras'] {
                        position: relative;
                        font-size: inherit;
                        outline: 0;
                        opacity: 1;
                        background: none;
                        box-shadow: 0 5px 50px rgba(0,0,0,0.1);
                        background-color: var(--extras-popover-bg);
                        color: var(--extras-popover-color);
                        padding: 20px;
                        will-change: opacity, transform;
                    }
                    
                    .tippy-box[data-animation=fade][data-theme~='extras'] .oxy-popover_popup-content {
                        padding: 0;
                    }
                    
                    .extras-in-builder .oxy-popover_popup-inner {
                        box-shadow: 0 5px 50px rgba(0,0,0,0.1);
                    }
                    
                   .tippy-box[data-theme~='extras'] .tippy-backdrop {
                        background-color: #fff;
                    }";
            
            $css_added = 'true';
            
        }
        
        return $css;
        
    } 
    
    
    function output_js() {

         wp_enqueue_script( 'popper-js', plugin_dir_url(__FILE__) . 'assets/popper.min.js', '', '1.0.0' );
         wp_enqueue_script( 'tippy-js', plugin_dir_url(__FILE__) . 'assets/tippy.min.js', '', '6.3.1' );
        
    }
    
    function output_init_js() { ?>
            
        <script type="text/javascript">
        jQuery(document).ready(oxygen_popover);
        function oxygen_popover($) {

            let extrasPopover = function ( container ) {
            
                $(container).find('.oxy-popover').each(function( i, oxypopover ){
                
                    $(oxypopover).find('.oxy-popover_marker').attr('data-button', 'oxy-popover_marker_' + i);
                    
                    let popData = $(oxypopover).find('.oxy-popover_popup');
                    let popoverPlacement = popData.data('placement');
                    let flipFallback;
                    
                    let contentSrc = popData.data('content-source');
                    
                    if (true != popData.data('flip')) {
                        flipFallback = [popoverPlacement];
                    } else {
                        flipFallback = popData.data('fallbacks').split(',');
                    }
                    
                    let elem;
                    
                    if (popData.data('elem-selector')) {
                        
                        if ($(oxypopover).closest('.oxy-dynamic-list').length) {
                            elem = $(oxypopover).closest('.oxy-dynamic-list > .ct-div-block').find(popData.data('elem-selector'))[0];
                        } else {
                            elem = popData.data('elem-selector') ;
                        }
                    } else {
                        elem = $(oxypopover).find('.oxy-popover_marker[data-button="oxy-popover_marker_' + i + '"]')[0];
                    }
                    
                    let instance = tippy(elem, {
                        content: $(oxypopover).find('.oxy-popover_popup-inner')[0], 
                        allowHTML: true,     
                        interactive: true, 
                        arrow: true,
                        trigger: popData.data('interaction'),    
                        appendTo: $(oxypopover).find('.oxy-popover_popup')[0],
                        placement: popoverPlacement,
                        maxWidth: 'none',    
                        inertia: true,
                        theme: 'extras',     
                        touch: true,
                        showOnCreate: popData.data('show'),  
                        moveTransition: 'transform ' + popData.data('move-transition') + 'ms ease-out', 
                        offset: [parseInt( popData.data('offsetx') ), parseInt( popData.data('offsety') )], 
                        popperOptions: {
                                modifiers: [{
                                    name: 'flip',
                                    options: {
                                        fallbackPlacements: flipFallback,
                                    },
                                    },
                                ],
                        },
                        onShown() {
                            document.addEventListener('keydown', function(e) {
                                if((e.key === "Escape" || e.key === "Esc")){
                                    tippy.hideAll()
                                }
                            });
                        }
                    });
                    
                    
                    if (true === popData.data('dom')) {
                        
                        instance.setProps({
                            render(instance) { const popper = popData[0]; return { popper } }
                        });
                        
                    }
                    

                });

            }

            extrasPopover('body');
                
            // Expose function
            window.doExtrasPopover = extrasPopover;

        }</script>

        <?php }
    
    
    function allowedEmptyOptions($options) {

        $options_to_add = array(
            "oxy-popover_label_text",
        );

        $options = array_merge($options, $options_to_add);

        return $options;
    } 
    

}

add_filter("oxy_base64_encode_options", 
    function($items) { 
        $items=array_merge($items, array('oxy-popover_dynamic_position','oxy-popover_dynamic_popover_placement','oxy-popover_label_text','oxy-popover_aria_label')); 
        return $items;
    }
);

new Extrapopover();