<?php

class ExtraModifiedDate extends OxygenExtraElements {
        

	function name() {
        return 'Post Modified Date';
    }
    
    function icon() {
        return plugin_dir_url(__FILE__) . 'assets/icons/'.basename(__FILE__, '.php').'.svg';
    }
    
    function extras_button_place() {
        return "single";
    }
    
    function tag() {
        return array('default' => 'span');
    }
    
    function init() {
        add_filter("oxy_allowed_empty_options_list", array( $this, "allowedEmptyOptions") );
    }

    function render($options, $defaults, $content) { 
        
        
        $date_before = esc_attr($options['date_before']);
        $date_display  = isset( $options['date_display'] ) ? esc_attr($options['date_display']) : '';
        
        if (isset( $options['date_format'] ) && $options['date_format'] === 'one') {
            $date_format = 'l F j, Y';
        }
        
        else if (isset( $options['date_format'] ) && $options['date_format'] === 'two') {
            $date_format = 'Y-m-d';
        }
        
        else if (isset( $options['date_format'] ) && $options['date_format'] === 'three') {
            $date_format = 'm/d/Y';
        }
        
        else if (isset( $options['date_format'] ) && $options['date_format'] === 'four') {
            $date_format = 'd/m/Y';
        }
        
        else if (isset( $options['date_format'] ) && $options['date_format'] === 'five') {
            $date_format = 'jS F, Y';
        }
        
        else {
            $date_format = $options['date_custom_format'];
        }
        
        if ($date_display === 'absolute') {
            echo $date_before . ' ';
            the_modified_date($date_format);
        }
        
        else {
            $last_modified_time = get_the_modified_time('U');
            $current_time = current_time('U');

            echo $date_before . ' <time class=modified-time>' . human_time_diff($last_modified_time,$current_time) . "</time> ago";  
        }

        $this->dequeue_scripts_styles();
        
    }

    function class_names() {
        return array();
    }

    function controls() {
        
        $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Display as',
                'slug' => 'date_display')
        )->setValue(array("absolute" => "Absolute", "relative" => "Relative" ))
            ->setDefaultValue('absolute')
            ->rebuildElementOnChange(); 
        
        $formats = array(
            "custom" => 'custom',
			"one" => 'Thursday March 20, 2020',
			"two" => '2020-03-20',
            "three" => '03/20/2020',
            "four" => '20/03/2020',
            "five" => '20th March, 2020'
		);
		
        
        $this->addOptionControl(
            array(
                "type" => "dropdown",
                "name" => "Date Format",
                "slug" => "date_format",
                "default" => 'slider',
                "condition" => 'date_display=absolute'
            )
        )->setValue(
           $formats
       )->rebuildElementOnChange();
        
        // Get date format options
        $date_format = get_option( 'date_format' );
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Custom Format'),
                "slug" => 'date_custom_format',
                "default" => $date_format,
                "condition" => 'date_format=custom'
            )
        )->rebuildElementOnChange();
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Text Before'),
                "slug" => 'date_before',
                "default" => 'Updated:  ',
            )
        )->rebuildElementOnChange();
        
        $this->typographySection('Typography', '',$this);

    }
    
    function allowedEmptyOptions($options) {

        $options_to_add = array(
            "oxy-post-modified-date_date_before",
        );

        $options = array_merge($options, $options_to_add);

        return $options;
    } 
    
    function afterInit() {
        $this->removeApplyParamsButton();
    }

}

new ExtraModifiedDate();