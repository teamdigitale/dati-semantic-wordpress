<?php

class ExtraPostTerms extends OxygenExtraElements {
        

	function name() {
        return 'Post Terms';
    }
    
    function icon() {
        return plugin_dir_url(__FILE__) . 'assets/icons/'.basename(__FILE__, '.php').'.svg';
    }
    
    function extras_button_place() {
        return "single";
    }
    
    function tag() {
        return array('default' => 'span', 'choices' => 'div,p,span' );
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
        
        
        // get options
        $taxonomy  = isset( $options['taxonomy'] ) ? esc_attr($options['taxonomy']) : '';
        $taxonomy_term_before = '<span class="oxy-post-terms_before"> ' . $dynamic($options['taxonomy_term_before']) . '</span>';
        $taxonomy_term_after = '<span class="oxy-post-terms_after"> ' . $dynamic($options['taxonomy_term_after']) . '</span>';
        $taxonomy_term_sep = isset( $options['taxonomy_term_sep'] ) ? esc_attr($options['taxonomy_term_sep']) : '';

        $none_text = $dynamic($options['none_text']);
        $none_output = isset( $options['none_output'] ) ? esc_attr($options['none_output']) : '';

        $taxonomy_term_links = (isset( $options['taxonomy_term_links'] ) && $options["taxonomy_term_links"] === "false" ) ? false : true;
        
        
            if ($taxonomy_term_links) {

                $output = get_the_term_list( get_the_ID(), $taxonomy, $taxonomy_term_before . ' ', $taxonomy_term_sep. ' ', ' ' . $taxonomy_term_after );

            } else {
                    
                $terms = get_the_terms( get_the_ID(), $taxonomy );

                if ( is_wp_error( $terms ) ) {
                    return $terms;
                }

                $term_names = array();

                if (is_array($terms)) {

                    foreach ( $terms as $term ) {
                        
                        $term_names[] = $term->name;
                    }
                    
                    if ( empty( $terms ) ) {
                        $output = '';
                    } else {
                        $output = $taxonomy_term_before . ' ' . join( $taxonomy_term_sep . ' ', $term_names ) . ' ' . $taxonomy_term_after;
                    }

                } else {
                    $output = '';
                }
                

            }

            if (!$output) {
                    if ('text' === $none_output) {
                        echo $none_text;
                    } else if ('both' === $none_output) {
                        echo $taxonomy_term_before . $none_text .$taxonomy_term_after;
                    } else {

                    }
            } else {
                echo $output;
            }

            $this->dequeue_scripts_styles();

        }

    function class_names() {
        return array();
    }

    function controls() {
        
        $taxonomy_term_links_selector = "a";
        
        $all_public_taxonomy_terms = get_taxonomies(array('public' => true),'names');
        
        $taxonomy_terms = array_diff($all_public_taxonomy_terms, array("post_format"));
        
        $dropdown_options = array();
        foreach ($taxonomy_terms as $taxonomy_term)
        {
            $dropdown_options[$taxonomy_term] = $taxonomy_term;
        }

        $this->addOptionControl(
            array(
                "type" => "dropdown",
                "name" => "Taxonomy",
                "slug" => "taxonomy"
            )
        )->setValue($dropdown_options)->rebuildElementOnChange();
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Before Text'),
                "slug" => 'taxonomy_term_before',
                "default" => 'Categories: ',
                "base64" => true
            )
        )->rebuildElementOnChange()
         ->setParam('dynamicdatacode', '<div optionname="\'oxy-post-terms_taxonomy_term_before\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('After Text'),
                "slug" => 'taxonomy_term_after',
                "default" => '',
                "base64" => true
            )
        )->rebuildElementOnChange()
         ->setParam('dynamicdatacode', '<div optionname="\'oxy-post-terms_taxonomy_term_after\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Seperator'),
                "slug" => 'taxonomy_term_sep',
                "default" => ', ',
            )
        )->rebuildElementOnChange();
        
        $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Term Links',
                'slug' => 'taxonomy_term_links')
            
        )->setValue(array( "true" => "Enabled", "false" => "Disabled"))
         ->setDefaultValue('true')->rebuildElementOnChange();

        $this->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('If no terms found'),
                'slug' => 'none_output'
            )
        )->setValue(array( 
            "nothing" => "Display nothing", 
            "text" => "Display text",
            "both" => "Display text with Before/After"
        ))->setDefaultValue('both')->rebuildElementOnChange();

        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Text'),
                "slug" => 'none_text',
                "default" => 'none',
                "condition" => 'none_output=text||none_output=both'
            )
        )->rebuildElementOnChange()->setParam('dynamicdatacode', '<div optionname="\'oxy-post-terms_none_text\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');
        
        
        $this->typographySection('Link Typography', $taxonomy_term_links_selector,$this);

        $this->typographySection('Before text typography', '.oxy-post-terms_before',$this);

        $this->typographySection('After text typography', '.oxy-post-terms_after' ,$this);


    }
    
    function afterInit() {
        $this->removeApplyParamsButton();
    }
    
    function allowedEmptyOptions($options) {

        $options_to_add = array(
            "oxy-post-terms_taxonomy_term_before",
            "oxy-post-terms_taxonomy_term_after",
            "oxy-post-terms_taxonomy_term_sep"
        );

        $options = array_merge($options, $options_to_add);

        return $options;
    }

}


// All the parameters that can contain dynamic data, should be added to this filter
add_filter("oxy_base64_encode_options", 
    function($items) { 
        $items=array_merge($items, array('oxy-post-terms_taxonomy_term_before', 'oxy-post-terms_taxonomy_term_after','oxy-post-terms_none_text')); 
        return $items;
    }
);

new ExtraPostTerms();