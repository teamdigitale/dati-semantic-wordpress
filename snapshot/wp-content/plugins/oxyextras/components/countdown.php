<?php
class ExtraCountdown extends OxygenExtraElements {

    var $js_added = false;
        
    function name() {
        return 'Countdown Timer';
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
    
    function enablePresets() {
        return true;
    }
    
    function enableFullPresets() {
        return true;
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
        
        $finish = $dynamic($options['finish']);
        $weeks = intval($dynamic($options['weeks']));
        $days = intval($dynamic($options['days']));
        $hours = intval($dynamic($options['hours']));
        $minutes = intval($dynamic($options['minutes']));
        $seconds = intval($dynamic($options['seconds']));
        $redirect_url = $dynamic($options['redirect_url']);
        $action_text = $dynamic($options['action_text']);
        $action_selector = $dynamic($options['action_selector']);
        $recurring_start = $dynamic($options['recurring_start']);
        $recurring_days = $dynamic($options['recurring_days']);
        $interval_text = $dynamic($options['interval_text']);

        $mode = isset( $options['mode'] ) ? esc_attr($options['mode']) : "";

        $maybe_remove = isset( $options['maybe_remove'] ) ? esc_attr($options['maybe_remove']) : "";
        $maybe_reset = isset( $options['maybe_reset'] ) ? esc_attr($options['maybe_reset']) : "";

        $action = isset( $options['action'] ) ? esc_attr($options['action']) : "";

        $maybe_labels = isset( $options['maybe_labels'] ) ? esc_attr($options['maybe_labels']) : "";
        $maybe_seperator = isset( $options['maybe_seperator'] ) ? esc_attr($options['maybe_seperator']) : "";

        $week_text = isset( $options['week_text'] ) ? esc_attr($options['week_text']) : "";
        $weeks_text = isset( $options['weeks_text'] ) ? esc_attr($options['weeks_text']) : "";

        $day_text = isset( $options['day_text'] ) ? esc_attr($options['day_text']) : "";
        $days_text = isset( $options['days_text'] ) ? esc_attr($options['days_text']) : "";

        $hour_text = isset( $options['hour_text'] ) ? esc_attr($options['hour_text']) : "";
        $hours_text = isset( $options['hours_text'] ) ? esc_attr($options['hours_text']) : "";

        $minute_text = isset( $options['minute_text'] ) ? esc_attr($options['minute_text']) : "";
        $minutes_text = isset( $options['minutes_text'] ) ? esc_attr($options['minutes_text']) : "";

        $second_text = isset( $options['second_text'] ) ? esc_attr($options['second_text']) : "";
        $seconds_text = isset( $options['seconds_text'] ) ? esc_attr($options['seconds_text']) : "";

        $seperator = isset( $options['seperator'] ) ? esc_attr($options['seperator']) : ""; 

        $wp_timezone = get_option('timezone_string');

        $timezone = isset( $options['timezone'] ) ? esc_attr($options['timezone']) : get_option('timezone_string');

        $prevent_redirect = isset( $options['prevent_redirect'] ) ? esc_attr($options['prevent_redirect']) : "";

        $output = '';

        $output .= '<div class="oxy-countdown-timer_inner" ';

        $output .= 'data-mode="'. $mode .'" ';

        $output .= 'data-weeks="'. $weeks .'" ';
        $output .= 'data-days="'. $days .'" ';
        $output .= 'data-hours="'. $hours .'" ';
        $output .= 'data-minutes="'. $minutes .'" ';
        $output .= 'data-seconds="'. $seconds .'" ';
        $output .= 'data-start="'. $recurring_start .'" ';
        $output .= 'data-recurring-days="'. $recurring_days .'" ';
        $output .= 'data-interval-text="'. $interval_text .'" ';
        
        $output .= 'data-countdown="'. $finish .'" ';
        $output .= 'data-timezone="'. $timezone .'" ';
        
        
        $output .= ('true' === $options['maybe_weeks']) ? 'data-show-weeks="true" ': '';
        $output .= ('true' === $options['maybe_days']) ? 'data-show-days="true" ': '';
        $output .= ('true' === $options['maybe_hours']) ? 'data-show-hours="true" ': '';
        $output .= ('true' === $options['maybe_minutes']) ? 'data-show-minutes="true" ': '';
        $output .= ('true' === $options['maybe_seconds']) ? 'data-show-seconds="true" ': '';
       
        $output .= 'data-remove="'. $maybe_remove .'" ';
        $output .= 'data-reset="'. $maybe_reset .'" ';
        $output .= 'data-action="'. $action .'" ';
        $output .= 'data-labels="'. $maybe_labels .'" '; 

        if ('true' === $maybe_seperator) {
            $output .= 'data-seperator="'. $seperator .'" ';
        }

        if ('true' !== $prevent_redirect || !is_user_logged_in() ) {
            $output .= 'redirect' === $action ? 'data-redirect="'. $redirect_url .'" ': '';
        }

        $output .= 'text' === $action ? 'data-text="'. $action_text .'" ': '';

        $output .= ('alert' === $action || 'offcanvas' === $action || 'lightbox' === $action ) ? 'data-selector="'. $action_selector .'" ': '';

        $output .= 'data-week="'. $week_text .'" ';
        $output .= 'data-week-plural="'. $weeks_text .'" ';
        $output .= 'data-day="'. $day_text .'" ';
        $output .= 'data-day-plural="'. $days_text .'" ';
        $output .= 'data-hour="'. $hour_text .'" ';
        $output .= 'data-hour-plural="'. $hours_text .'" ';
        $output .= 'data-minute="'. $minute_text .'" ';
        $output .= 'data-minute-plural="'. $minutes_text .'" ';
        $output .= 'data-second="'. $second_text .'" ';
        $output .= 'data-second-plural="'. $seconds_text .'" ';

        $output .= '>';


        /* in builder content */

        $seperator_output = 'true' === $maybe_seperator ? '<div class="oxy-countdown-timer_seperator">'. $seperator .'</div>' : '';

        if ( 'true' === $options['maybe_weeks'] || 'true' === $options['maybe_remove'] ) {

            $output .=  '<div class="oxy-countdown-timer_item oxy-countdown-timer_weeks">
                        <div class="oxy-countdown-timer_digits">00</div>';
            $output .=  'true' === $maybe_labels ? '<div class="oxy-countdown-timer_label"> ' . $weeks_text . ' </div>' : '';
            $output .=  '</div> '. $seperator_output;

        }

        if ( 'true' === $options['maybe_days'] || 'true' === $options['maybe_remove'] ) {

            $output .= '<div class="oxy-countdown-timer_item oxy-countdown-timer_days">
                        <div class="oxy-countdown-timer_digits">00</div>';
            $output .=  'true' === $maybe_labels ? '<div class="oxy-countdown-timer_label"> ' . $days_text . ' </div>' : '';
            $output .=  '</div> '. $seperator_output;

        }

        if ( 'true' === $options['maybe_hours'] || 'true' === $options['maybe_remove'] ) {

            $output .= '<div class="oxy-countdown-timer_item oxy-countdown-timer_hours">
                        <div class="oxy-countdown-timer_digits">00</div>';
            $output .=  'true' === $maybe_labels ? '<div class="oxy-countdown-timer_label"> ' . $hours_text . ' </div>' : '';
            $output .=  '</div> '. $seperator_output;

        }


        if ( 'true' === $options['maybe_minutes'] || 'true' === $options['maybe_remove'] ) {

            $output .= '<div class="oxy-countdown-timer_item oxy-countdown-timer_minutes">
                        <div class="oxy-countdown-timer_digits">00</div>';
            $output .= 'true' === $maybe_labels ? '<div class="oxy-countdown-timer_label"> ' . $minutes_text . ' </div>' : '';
            $output .= '</div>'. $seperator_output;

        }

                
        if ( 'true' === $options['maybe_seconds'] || 'true' === $options['maybe_remove'] ) {
        
            $output .= '<div class="oxy-countdown-timer_item oxy-countdown-timer_seconds">
                        <div class="oxy-countdown-timer_digits">00</div>';
            $output .= 'true' === $maybe_labels ? '<div class="oxy-countdown-timer_label"> ' . $seconds_text . ' </div>' : '';
            $output .= '</div>';

        }

        
        
        
        $output .= '</div>';

        echo $output;


        // add JavaScript code only once and if shortcode presented
        if ($this->js_added !== true) {
            if (!defined('OXY_ELEMENTS_API_AJAX') || !OXY_ELEMENTS_API_AJAX) {
                add_action( 'wp_footer', array( $this, 'output_js' ) );
                add_action( 'wp_footer', array( $this, 'output_init_js' ) );
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
                'name' => __('Mode'),
                'slug' => 'mode'
            )
        )->setValue(array( 
            "once" => "Fixed time", 
            "evergreen" => "Evergreen",
            "recurring" => "Recurring"
        ))
         ->setDefaultValue('once')->rebuildElementOnChange();

          /*  
         $this->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Data source'),
                'slug' => 'source'
            )
        )->setValue(array( 
            "manual" => "Manual", 
            "dynamic" => "Dynamic",
        ))
         ->setDefaultValue('manual')->rebuildElementOnChange();
         */

        
        $now = current_time( 'mysql' ); 
        $time_default = date( 'Y-m-d H:i:s', strtotime( $now ) + 7200 );

         $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('End date / time'),
                "default" => $time_default,
                "slug" => 'finish',
                "base64" => true,
                "condition" => 'mode=once'
            )
        )->setParam('dynamicdatacode', '<div optionname="\'oxy-countdown-timer_finish\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');

        


        $weeks_control = $this->addOptionControl(
			array(
				'type' 		=> 'textfield',
				'name' 		=> __('Weeks'),
				"slug" 		=> 'weeks',
				"default" 	=> '0',
                "condition" => 'mode=evergreen||mode=recurring'
			)
        );
        $weeks_control->setParam('dynamicdatacode', '<div optionname="\'oxy-countdown-timer_weeks\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode"></div>');
        $weeks_control->setParam('hide_wrapper_end', true);

        $days_control = $this->addOptionControl(
			array(
				'type' 		=> 'textfield',
				'name' 		=> __('Days'),
				"slug" 		=> 'days',
				"default" 	=> '0',
                "condition" => 'mode=evergreen||mode=recurring'
			)
		);
        $days_control->setParam('dynamicdatacode', '<div optionname="\'oxy-countdown-timer_days\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode"></div>');
        $days_control->setParam('hide_wrapper_start', true);

		$hours_control = $this->addOptionControl(
			array(
				'type' 		=> 'textfield',
				'name' 		=> __('Hours'),
				"slug" 		=> 'hours',
				"default" 	=> '0',
                "condition" => 'mode=evergreen||mode=recurring'
			)
		);
        $hours_control->setParam('dynamicdatacode', '<div optionname="\'oxy-countdown-timer_hours\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode"></div>');
        $hours_control->setParam('hide_wrapper_end', true);

		$minutes_control = $this->addOptionControl(
			array(
				'type' 		=> 'textfield',
				'name' 		=> __('Minutes'),
				"slug" 		=> 'minutes',
				"default" 	=> '0',
                "condition" => 'mode=evergreen||mode=recurring'
			)
		);
        $minutes_control->setParam('dynamicdatacode', '<div optionname="\'oxy-countdown-timer_minutes\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode"></div>');
        $minutes_control->setParam('hide_wrapper_start', true);

        $seconds_control = $this->addOptionControl(
			array(
				'type' 		=> 'textfield',
				'name' 		=> __('Seconds'),
				"slug" 		=> 'seconds',
				"default" 	=> '0',
                "condition" => 'mode=evergreen||mode=recurring'
			)
		);
        $seconds_control->setParam('dynamicdatacode', '<div optionname="\'oxy-countdown-timer_seconds\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode"></div>');


        $this->addOptionControl(
            array(
                'type' => 'textfield',
                'name' => __('Start date / time'),
                'slug' => 'recurring_start',
                'default' => $time_default,
                "condition" => 'mode=recurring'
            )
        )->setParam('dynamicdatacode', '<div optionname="\'oxy-countdown-timer_recurring_start\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode"></div>');

        $time = DateTimeZone::listIdentifiers();
        
        $default_timezone = get_option('timezone_string');  
        
        $time = array_merge($time, [
            "UTC-12",
            "UTC-11:30",
            "UTC-11",
            "UTC-10:30",
            "UTC-10",
            "UTC-9:30",
            "UTC-9",
            "UTC-8:30",
            "UTC-8",
            "UTC-7:30",
            "UTC-7",
            "UTC-6:30",
            "UTC-6",
            "UTC-5:30",
            "UTC-5",
            "UTC-4:30",
            "UTC-4",
            "UTC-3:30",
            "UTC-3",
            "UTC-2:30",
            "UTC-2",
            "UTC-1:30",
            "UTC-1",
            "UTC-0:30",
            "UTC+0:30",
            "UTC+1",
            "UTC+1:30",
            "UTC+2",
            "UTC+2:30",
            "UTC+3",
            "UTC+3:30",
            "UTC+4",
            "UTC+4:30",
            "UTC+5",
            "UTC+5:30",
            "UTC+5:35",
            "UTC+6",
            "UTC+6:30",
            "UTC+7",
            "UTC+7:30",
            "UTC+8",
            "UTC+8:30",
            "UTC+8:45",
            "UTC+9",
            "UTC+9:30",
            "UTC+10",
            "UTC+10:30",
            "UTC+11",
            "UTC+11:30",
            "UTC+12",
            "UTC+12:45",
            "UTC+13",
            "UTC+13:45",
            "UTC+14",
        ]);

        $this->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Timezone'),
                'slug' => 'timezone',
                "condition" => 'mode=once||mode=recurring'
            )
        )->setValue($time)
         ->setDefaultValue($default_timezone);

        $this->addOptionControl(
			array(
				'type' 		=> 'textfield',
				'name' 		=> __('Recur every (days)'),
				"slug" 		=> 'recurring_days',
				"default" 	=> '0',
                "condition" => 'mode=recurring'
			)
		)->setParam('dynamicdatacode', '<div optionname="\'oxy-countdown-timer_recurring_days\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode"></div>');


        $interval_text_control = $this->addOptionControl(
            array(
                'type' => 'textfield',
                'name' => __('Text to display in between countdown intervals'),
                'slug' => 'interval_text',
                'default' => '',
                "condition" => 'mode=recurring'
            )
        );
        $interval_text_control->setParam('dynamicdatacode', '<div optionname="\'oxy-countdown-timer_interval_text\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode"></div>');


        $this->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Reset on page load'),
                'slug' => 'maybe_reset',
                "condition" => 'mode=evergreen'
            )
        )->setValue(array( 
            "true" => "True (Start again each time the user visits)", 
            "false" => "False (Start from the first visit)" 
        ))
         ->setDefaultValue('false')
         ->rebuildElementOnChange();



        /**
         * Format
         */
        $format_section = $this->addControlSection("format_section", __("Time format"), "assets/icon.png", $this);

        $format_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Auto'),
                'slug' => 'maybe_remove'
            )
        )->setValue(array( 
            "true" => "True", 
            "false" => "False" 
        ))
         ->setDefaultValue('false')
         ->rebuildElementOnChange()
         ->setParam("description", __("Only show when there is a non-zero value"));

        $format_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Weeks'),
                'slug' => 'maybe_weeks',
                'condition' => 'maybe_remove=false'
            )
        )->setValue(array( 
            "true" => "True", 
            "false" => "False" 
        ))
         ->setDefaultValue('true')
         ->rebuildElementOnChange();

         $format_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Days'),
                'slug' => 'maybe_days',
                'condition' => 'maybe_remove=false'
            )
        )->setValue(array( 
            "true" => "True", 
            "false" => "False" 
        ))
         ->setDefaultValue('true')
         ->rebuildElementOnChange();

         $format_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Hours'),
                'slug' => 'maybe_hours',
                'condition' => 'maybe_remove=false'
            )
        )->setValue(array( 
            "true" => "True", 
            "false" => "False" 
        ))
         ->setDefaultValue('true')
         ->rebuildElementOnChange();

         $format_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Minutes'),
                'slug' => 'maybe_minutes',
                'condition' => 'maybe_remove=false'
            )
        )->setValue(array( 
            "true" => "True", 
            "false" => "False" 
        ))
         ->setDefaultValue('true')
         ->rebuildElementOnChange();

         $format_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Seconds'),
                'slug' => 'maybe_seconds',
                'condition' => 'maybe_remove=false'
            )
        )->setValue(array( 
            "true" => "True", 
            "false" => "False" 
        ))
         ->setDefaultValue('true')
         ->rebuildElementOnChange();


         /**
         * Layout
         
        $layout_section = $this->addControlSection("layout_section", __("Layout"), "assets/icon.png", $this);
        $layout_section->flex('.oxy-countdown-timer_inner', $this);
        */

         /**
         * Action
         */
        $action_section = $this->addControlSection("action_section", __("Expire action"), "assets/icon.png", $this);

        $action_section->addOptionControl(
			array(
				'type' 		=> 'dropdown',
				'name' 		=> __("Action after time expires"),
				"slug" 		=> 'action',
				"default"		=> 'none',
			)
		)->setValue(array(
            //'none' 		=> __('None'),
            'hide' 		=> __('Hide timer'),
            'text' 		=> __('Show text'),
            'redirect' 	=> __('Redirect'),
            'alert'     => __('Open alert box'),
            'offcanvas' => __('Open offcanvas'),
            'count' => __('Count back up'),
            //'lightbox' => __('Open lightbox'),
        )); 


        $action_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Element selector'),
                "slug" => 'action_selector',
                "default" => '',
                "condition" => 'action=alert||action=offcanvas||action=lightbox'
            )
        )->setParam('dynamicdatacode', '<div optionname="\'oxy-countdown-timer_action_selector\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');


        $action_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Redirect URL'),
                "slug" => 'redirect_url',
                "default" => '',
                "base64" => true,
                "condition" => 'action=redirect'
            )
        )->setParam('dynamicdatacode', '<div optionname="\'oxy-countdown-timer_redirect_url\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');

        $action_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Prevent redirect if logged in'),
                'slug' => 'prevent_redirect',
                "condition" => 'action=redirect'
            )
        )->setValue(array( 
            "true" => "True", 
            "false" => "False" 
        ))
         ->setDefaultValue('true')
         ->rebuildElementOnChange();


        $action_section->addOptionControl(
            array(
                "type" => 'textarea',
                "name" => __('Text'),
                "slug" => 'action_text',
                "default" => '',
                "condition" => 'action=text'
            )
        )->setParam('dynamicdatacode', '<div optionname="\'oxy-countdown-timer_action_text\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');



        /**
         * Items
         */
        $items_section = $this->addControlSection("items_section", __("Items"), "assets/icon.png", $this);
        $item_selector = '.oxy-countdown-timer_item';


        $items_spacing_section = $items_section->addControlSection("items_spacing_section", __("Spacing"), "assets/icon.png", $this);

        $items_spacing_section->addPreset(
			"padding",
			"item_padding",
			__("Padding"),
			$item_selector
		)->whiteList();

		$items_spacing_section->addPreset(
			"margin",
			"item_margin",
			__("Margin"),
			$item_selector
		)->whiteList();

        $items_section->addStyleControl( 
            array(
                "property" => 'width',
                "control_type" => 'slider-measurebox',
                "selector" => $item_selector
            )
        )
        ->setRange('0','600','1')
        ->setUnits('px');
        
        $items_section->addStyleControl( 
            array(
                "property" => 'height',
                "control_type" => 'slider-measurebox',
                "selector" => $item_selector
            )
        )
        ->setRange('0','600','1')
        ->setUnits('px');

        $items_section->addStyleControl(
            array(
                "name" => 'Color',
                "selector" => $item_selector,
                "property" => 'color',
            )
    )->setParam('hide_wrapper_end', true);

        $items_section->addStyleControl(
            array(
               "name" => 'Background',
               "selector" => $item_selector,
               "property" => 'background-color',
           )
        )->setParam('hide_wrapper_start', true);
        
        


        $items_section->borderSection(__('Borders'), $item_selector, $this );
		$items_section->boxShadowSection(__('Box Shadow'), $item_selector, $this );

        

        /**
         * Labels
         */
        $labels_section = $this->addControlSection("labels_section", __("Labels"), "assets/icon.png", $this);

        $label_selector = '.oxy-countdown-timer_label';

        $labels_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Show labels'),
                'slug' => 'maybe_labels',
            )
        )->setValue(array( 
            "true" => "True", 
            "false" => "False" 
        ))
         ->setDefaultValue('true')
         ->rebuildElementOnChange();


        $labels_section->addStyleControl(
            array(
                "name" => __('Labels position'),
                "property" => 'flex-direction',
                "control_type" => 'radio',
                "default" => 'column',
                "selector" => '.oxy-countdown-timer_item',
                "condition" => 'maybe_labels=true'
            )
        )->setValue(array( 
            "column" => "Bottom",
            "column-reverse" => "Top",
            "row-reverse" => "Left",
            "row" => "Right",
            )
        );


        //$label_text_section = $labels_section->addControlSection("label_text_section", __("Label text"), "assets/icon.png", $this);

        $labels_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Week'),
                "default" => 'week',
                "slug" => 'week_text',
                "base64" => true,
                "condition" => 'maybe_labels=true'
            )
        )->setParam('hide_wrapper_end', true);
        $labels_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Weeks'),
                "default" => 'weeks',
                "slug" => 'weeks_text',
                "base64" => true,
                "condition" => 'maybe_labels=true'
            )
        )->setParam('hide_wrapper_start', true);
        
        $labels_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Day'),
                "default" => 'day',
                "slug" => 'day_text',
                "base64" => true,
                "condition" => 'maybe_labels=true'
            )
        )->setParam('hide_wrapper_end', true);
        $labels_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Days'),
                "default" => 'days',
                "slug" => 'days_text',
                "base64" => true,
                "condition" => 'maybe_labels=true'
            )
        )->setParam('hide_wrapper_start', true);

        $labels_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Hour'),
                "default" => 'hour',
                "slug" => 'hour_text',
                "base64" => true,
                "condition" => 'maybe_labels=true'
            )
        )->setParam('hide_wrapper_end', true);
        $labels_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Hours'),
                "default" => 'hours',
                "slug" => 'hours_text',
                "base64" => true,
                "condition" => 'maybe_labels=true'
            )
        )->setParam('hide_wrapper_start', true);

        $labels_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Minute'),
                "default" => 'minute',
                "slug" => 'minute_text',
                "base64" => true,
                "condition" => 'maybe_labels=true'
            )
        )->setParam('hide_wrapper_end', true);
        $labels_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Minutes'),
                "default" => 'minutes',
                "slug" => 'minutes_text',
                "base64" => true,
                "condition" => 'maybe_labels=true'
            )
        )->setParam('hide_wrapper_start', true);

        $labels_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Second'),
                "default" => 'second',
                "slug" => 'second_text',
                "base64" => true,
                "condition" => 'maybe_labels=true'
            )
        )->setParam('hide_wrapper_end', true);
        $labels_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Seconds'),
                "default" => 'seconds',
                "slug" => 'seconds_text',
                "base64" => true,
                "condition" => 'maybe_labels=true'
            )
        )->setParam('hide_wrapper_start', true);



        $labels_section->typographySection('Typography', $label_selector,$this);

        $labels_spacing_section = $labels_section->addControlSection("labels_spacing_section", __("Spacing"), "assets/icon.png", $this);

        $labels_spacing_section->addPreset(
			"padding",
			"label_padding",
			__("Padding"),
			$label_selector
		)->whiteList();

		$labels_spacing_section->addPreset(
			"margin",
			"label_margin",
			__("Margin"),
			$label_selector
		)->whiteList();



        /**
         * Digits
         */
        //$digits_section = $this->addControlSection("digits_section", __("Digits"), "assets/icon.png", $this);

        $digit_selector = '.oxy-countdown-timer_digits';

        $this->typographySection('Digits', $digit_selector,$this);




         /**
         * Seperator
         */
        $seperator_section = $this->addControlSection("seperator_section", __("Seperator"), "assets/icon.png", $this);

        $seperator_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Show seperators'),
                'slug' => 'maybe_seperator',
            )
        )->setValue(array( 
            "true" => "True", 
            "false" => "False" 
        ))
         ->setDefaultValue('true')
         ->rebuildElementOnChange();

        $seperator_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Seperator'),
                "default" => ':',
                "slug" => 'seperator',
                "base64" => true,
                "condition" => 'maybe_seperator=true'
            )
        )->rebuildElementOnChange();

        $seperator_section->typographySection('Typography', '.oxy-countdown-timer_seperator',$this);

    }

    function customCSS() {
        $css = "
                .oxy-countdown-timer_inner {
                    display: flex;
                    opacity: 0;
                    align-items: center;
                }

                .oxy-countdown-timer_inner-visible {
                    opacity: 1;
                }

                .oxy-countdown-timer_item {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    flex: 1;
                    padding: 1em;
                }
                
                .oxygen-builder-body .oxy-countdown-timer_inner {
                    opacity: 1;
                }

                .oxy-countdown-timer_seperator:last-child {
                    display: none;
                }

                ";



        return $css;
    }


    function output_js() {
        wp_enqueue_script( 'extras-countdown', plugin_dir_url(__FILE__) . 'assets/jquery.countdown.min.js', '', '2.1.0' );
        wp_enqueue_script( 'extras-luxonjs', plugin_dir_url(__FILE__) . 'assets/luxon.min.js', '', '1.0.0' );
      
     }
     
     function output_init_js() {
         wp_enqueue_script( 'extras-countdown-init', plugin_dir_url(__FILE__) . 'assets/countdown-init.js', '', '1.0.1' );
     }

}


add_filter("oxy_base64_encode_options", 
    function($items) { 
        $items=array_merge($items, array(
            'oxy-countdown-timer_finish',
            'oxy-countdown-timer_redirect_url',
            'oxy-countdown-timer_action_text',
            'oxy-countdown-timer_action_selector',
            'oxy-countdown-timer_weeks',
            'oxy-countdown-timer_days',
            'oxy-countdown-timer_hours',
            'oxy-countdown-timer_minutes',
            'oxy-countdown-timer_seconds',
            'oxy-countdown-timer_recurring_start',
            'oxy-countdown-timer_recurring_days',
            'oxy-countdown-timer_interval_text'
        )); 
        return $items;
    }
);

new ExtraCountdown();