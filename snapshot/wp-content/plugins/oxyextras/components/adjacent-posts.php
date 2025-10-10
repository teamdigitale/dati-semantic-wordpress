<?php

class ExtraAdjPosts extends OxygenExtraElements {
    
    var $css_added = false;

	function name() {
        return 'Adjacent Posts';
    }
    
    function enablePresets() {
        return true;
    }
    
    function enableFullPresets() {
        return true;
    }
    
    function tag() {
        return array('default' => 'nav');
    }

    function icon() {
        return plugin_dir_url(__FILE__) . 'assets/icons/'.basename(__FILE__, '.php').'.svg';
    }
    
    function extras_button_place() {
       return "single";
    }
    

    function render($options, $defaults, $content) {

        // Get Options
        $adjacent_icon_prev_attr  = isset( $options['prev_icon'] ) ? esc_attr($options['prev_icon']) : "";
        $adjacent_icon_next_attr  = isset( $options['next_icon'] ) ? esc_attr($options['next_icon']) : "";
        $titles_tag = isset( $options['titles_tag'] ) ? esc_attr($options['titles_tag']) : "h4";
        $same_taxonony = esc_attr($options['same_taxonony']) === 'true' ? true : false;
        $exclude_terms = isset( $options['exclude_terms'] ) ? esc_attr($options['exclude_terms']) : "";
        $taxonony = isset( $options['taxonony'] ) ? esc_attr($options['taxonony']) : "";
        $posts_to_display = esc_attr($options['posts_to_display']);
        
        
        // Load Icons
        global $oxygen_svg_icons_to_load;
        $oxygen_svg_icons_to_load[] = $adjacent_icon_prev_attr;
        $oxygen_svg_icons_to_load[] = $adjacent_icon_next_attr;
        
        
        if (isset( $options['display_image'] ) && $options["display_image"] === "show" ) {
            $image_size = isset( $options['image_size'] ) ? esc_attr($options['image_size']) : "";
        }
        
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
        
        $extras_previous_label = $dynamic($options['extras_previous_label']);
        $extras_next_label = $dynamic($options['extras_next_label']);
        
        // Labels
        $prev_label = (isset( $options['label_display'] ) && $options["label_display"] === "show" ) ? '<span class=adj-post_label>' . $extras_previous_label . '</span>' : '';
        $next_label = (isset( $options['label_display'] ) && $options["label_display"] === "show" ) ? '<span class=adj-post_label>' . $extras_next_label . '</span>' : '';
        
        // Get Adjacent Posts
        $prev_post  = get_previous_post($same_taxonony,$exclude_terms,$taxonony);
        $next_post =  get_next_post($same_taxonony,$exclude_terms,$taxonony);
        
        if ( empty( $prev_post ) && empty( $next_post )) {
            return 'No previous or next posts found';
        }
        
        
        //* Output links only if each post exists (and if user wants it to be displayed)
        if ( (! empty( $prev_post )) && ('next' !== $posts_to_display) ) { ?>
            <a rel="prev" class="adj-post prev-post" href="<?php echo get_permalink( $prev_post->ID ); ?>">
            <?php if (isset( $options['display_icons'] ) && $options["display_icons"] === "show" ) { ?>
                    <span class="adj-post_icon">
                <svg id="prev<?php echo esc_attr($options['selector']); ?>-icon"><use xlink:href="#<?php echo $adjacent_icon_prev_attr; ?>"></use></svg>
                <?php } ?> </span>
                <?php if (isset( $options['display_image'] ) && $options["display_image"] === "show" ) {
                        echo get_the_post_thumbnail($prev_post->ID, $image_size, array('alt' => $prev_post->post_title ));
                } ?>
                <div class="adj-post_content">
                <?php  echo $prev_label; 
                    if (isset( $options['title_display'] ) && $options["title_display"] === "show" ) {                  
                        echo '<' . $titles_tag .' class=adj-post_title>';                 
                        echo apply_filters( 'the_title', $prev_post->post_title, $prev_post->ID );
                        echo '</' . $titles_tag .'>';  
                    } ?>
                </div>    
            </a>
        <?php }

        if ( (! empty( $next_post )) && ('previous' !== $posts_to_display) ) { ?>
            <a rel="next" class="adj-post next-post" href="<?php echo get_permalink( $next_post->ID ); ?>">
                 <div class="adj-post_content">
                <?php  echo $next_label; 
                    if (isset( $options['title_display'] ) && $options["title_display"] === "show" ) { 
                      echo '<' . $titles_tag .' class=adj-post_title>';
                      echo apply_filters( 'the_title', $next_post->post_title, $next_post->ID );
                      echo '</' . $titles_tag .'>';
                    } ?>
                </div>     
                <?php if (isset( $options['display_image'] ) && $options["display_image"] === "show" ) {
                        echo get_the_post_thumbnail($next_post->ID, $image_size, array('alt' => $next_post->post_title ));
                       } ?>
                <?php if (isset( $options['display_icons'] ) && $options["display_icons"] === "show" ) { ?>
                <span class="adj-post_icon">
                    <svg id="next<?php echo esc_attr($options['selector']); ?>-icon"><use xlink:href="#<?php echo $adjacent_icon_next_attr; ?>"></use></svg></span>
                <?php } ?>
            </a>
        <?php }

        $this->dequeue_scripts_styles();
        
    }


    function controls() {
        
        
        /**
         * Post Settings
         */
        //$post_settings = $this->addControlSection("post_settings", __("Post Settings"), "assets/icon.png", $this);
        
        
        $adjacent_post_selector = '.adj-post';
        $adjacent_post_content_selector = '.adj-post_content';
        
      
        
         // Split posts
        $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Posts to display',
                'slug' => 'posts_to_display'
            )
        )->setValue(array( 
            "previous" => "Previous",
            "next" => "Next",
            "both" => "Both"
            )
        )->setDefaultValue('both')->rebuildElementOnChange();
        
        
        
        
        
        
          // Same Taxomony?
        $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Only display posts in same taxonomy?',
                'slug' => 'same_taxonony')
            
        )->setValue(array( "true" => "True", "false" => "False" ))
         ->setDefaultValue('false')->rebuildElementOnChange();
        
        
        $this->addStyleControl(
            array(
                "name" => 'Post Width',
                "property" => 'width',
                "selector" => $adjacent_post_selector,
                "default" => '50'
            )
        )->setUnits('%','%');
        
        $this->addStyleControl(
            array(
                "name" => 'Post Width (when only one post)',
                "property" => 'width',
                "selector" => $adjacent_post_selector.":only-child",
                "default" => '50'
            )
        )->setUnits('%','%');
        
        
        /**
         * Taxonomies
         */
        $all_taxonomy_terms = get_taxonomies('','names');
        
        $taxonomy_terms = array_diff($all_taxonomy_terms, array("link_category", "nav_menu", "post_format"));
        
        $dropdown_options = array();
        foreach ($taxonomy_terms as $taxonomy_term)
        {
            $dropdown_options[$taxonomy_term] = $taxonomy_term;
        }

        $this->addOptionControl(
            array(
                "type" => "dropdown",
                "name" => "Taxonomy",
                "slug" => "taxonony",
                "condition" => "same_taxonony=true"
            )
        )->setValue($dropdown_options)->rebuildElementOnChange();
    
        
         /**
         * Exclude Terms
         */
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('IDs of Terms to Exclude'),
                "slug" => 'exclude_terms',
            )
        )->rebuildElementOnChange();
        
        
        
        
        
        /**
         * Inner Post styles
         */

        $post_styles = $this->addControlSection("post_styles", __("Post Styles"), "assets/icon.png", $this);
        
        
        
        $post_styles->addStyleControl(
            array(
                "property" => 'background-color',
                "selector" => $adjacent_post_selector
            )
        )->whiteList();
        
        
        $post_styles->addStyleControl(
            array(
                "name" => 'Hover Background Color',
                "property" => 'background-color',
                "selector" => $adjacent_post_selector.":hover",
            )
        )->whiteList();
        
        
        /**
         * Post Spacing
         */
        $post_spacing = $post_styles->addControlSection("post_spacing", __("Post Spacing"), "assets/icon.png", $this);
        
        $post_spacing->addPreset(
            "padding",
            "post_padding",
            __("Padding"),
            $adjacent_post_selector
        )->whiteList();
        
        $post_spacing->addPreset(
            "margin",
            "post_margin",
            __("Margin"),
            $adjacent_post_selector
        )->whiteList();
        
        
        $post_styles->borderSection('Borders', $adjacent_post_selector,$this);
        $post_styles->boxShadowSection('Shadows', $adjacent_post_selector,$this);
        $post_styles->borderSection('Hover Borders', $adjacent_post_selector.":hover",$this);
        $post_styles->boxShadowSection('Hover Shadows', $adjacent_post_selector.":hover",$this);
        
        
        
        
        /**
         * Post Content
         */
        $post_content = $post_styles->addControlSection("post_content", __("Text Spacing / Layout"), "assets/icon.png", $this);
        
        $post_content->flex($adjacent_post_content_selector, $this);
        
        $post_content->addPreset(
            "padding",
            "post_content_padding",
            __("Padding"),
            $adjacent_post_content_selector
        )->whiteList();
        
        
        
        $post_fullwidth_below = $post_styles->addOptionControl(
            array(
                "name" => 'Stack Posts Below',
                "slug" => 'adjacent_post_fullwidth_below',
                "type" => 'medialist',
            )
        );
        
        $post_fullwidth_below->rebuildElementOnChange();

        
        
        /**
         * Post Titles
         */
        
        $titles_section = $this->addControlSection("titles_section", __("Titles"), "assets/icon.png", $this);
        $adj_post_title_selector = '.adj-post_title';
        
        $titles_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Post Title Display',
                'slug' => 'title_display')
            
        )->setValue(array( "show" => "Show", "hide" => "Hide" ))
         ->setDefaultValue('show')->rebuildElementOnChange();
        
        $titles_section->typographySection('Typography', $adj_post_title_selector,$this);
        
        $titles_spacing = $titles_section->addControlSection("titles_spacing", __("Spacing"), "assets/icon.png", $this);
        
        $titles_section->addOptionControl(
            array(
                "type" => "dropdown",
                "name" => "Tag",
                "slug" => "titles_tag",
                "default" => 'h4',
                "condition" => 'title_display=show',
            )
        )->setValue(
           array( 
                "h1" => "h1", 
                "h2" => "h2",
               "h3" => "h3",
               "h4" => "h4",
               "h5" => "h5",
               "h6" => "h6",
               "p" => "p",
               "div" => "div",
               "span" => "span",
           )
       )->rebuildElementOnChange();
        
        $titles_spacing->addPreset(
            "margin",
            "titles_margin",
            __("Margin"),
            $adj_post_title_selector
        )->whiteList();
        
        $titles_spacing->addPreset(
            "padding",
            "titles_padding",
            __("Padding"),
            $adj_post_title_selector
        )->whiteList();
        
        
        
        /**
         * Labels
         */
        
        $adj_post_label_selector = '.adj-post_label';
        
        $adj_post_labels = $this->addControlSection("labels", __("Labels"), "assets/icon.png", $this);
        
        $adj_post_labels->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Label Display',
                'slug' => 'label_display')
            
        )->setValue(array( "show" => "Show", "hide" => "Hide" ))
         ->setDefaultValue('show')->rebuildElementOnChange();
        
        $adj_post_labels->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Previous Label'),
                "slug" => 'extras_previous_label',
                "default" => 'Previous Post',
                "condition" => "label_display=show",
                "base64" => true,
            )
        )->rebuildElementOnChange()
         ->setParam('dynamicdatacode', '<div optionname="\'oxy-adjacent-posts_extras_previous_label\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');  
        
        $adj_post_labels->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Next Label'),
                "slug" => 'extras_next_label',
                "default" => 'Next Post',
                "condition" => "label_display=show",
                "base64" => true,
            )
        )->rebuildElementOnChange()
         ->setParam('dynamicdatacode', '<div optionname="\'oxy-adjacent-posts_extras_next_label\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');    
        
        $adj_post_labels->typographySection('Label Typography', $adj_post_label_selector,$this);
        
        $adj_post_labels->addPreset(
            "margin",
            "labels_margin",
            __("Margin"),
            $adj_post_label_selector
        )->whiteList();
        
        
        /**
         * Icon
         */

        $icon = $this->addControlSection("icon", __("Icons"), "assets/icon.png", $this);
        
        $icon_area = $icon->addControlSection("icon_area", __("Icon Area"), "assets/icon.png", $this);
        
        $icons = $icon->addControlSection("icon_settings", __("Previous Icon"), "assets/icon.png", $this);
        
        
        $next_icons = $icon->addControlSection("next_icons_settings", __("Next Icon"), "assets/icon.png", $this);
        
        $adj_icon_area_selector = '.adj-post_icon';
        
        $adj_icon_selector = '.adj-post svg';
        
        
        $icon->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Display Icons',
                'slug' => 'display_icons')
            
        )->setValue(array( "show" => "Show", "hide" => "Hide" ))
         ->setDefaultValue('show')->rebuildElementOnChange();
        
        $icon_size = $icon->addStyleControl(
                array(
                    "name" => __('Icon Size'),
                    "slug" => "icon_size",
                    "selector" => $adj_icon_selector,
                    "control_type" => 'slider-measurebox',
                    "value" => '24',
                    "property" => 'font-size',
                    "condition" => 'display_icons=show',
                )
        );
        $icon_size->setRange(4, 72, 1);
        
        $icons->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Prev Icon'),
                "slug" => 'prev_icon',
                "value" => 'FontAwesomeicon-long-arrow-left',
                "condition" => 'display_icons=show',
            )
        )->rebuildElementOnChange();
        
        $next_icons->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Next Icon'),
                "slug" => 'next_icon',
                "value" => 'FontAwesomeicon-long-arrow-right',
                "condition" => 'display_icons=show',
            )
        )->rebuildElementOnChange();
        
        $icon_area->addStyleControls(
            
            array(
                array(
                    "property" => 'background-color',
                    "selector" => $adj_icon_area_selector
                ),
                array(
                    "name" => 'Icon Color',
                    "property" => 'color',
                    "selector" => $adj_icon_selector
                )
            )
        );
        
        $icon->boxShadowSection('Icon Shadows', $adj_icon_area_selector,$this);
        $icon->borderSection('Icon Borders', $adj_icon_area_selector,$this);
        
        $icon_area->flex($adj_icon_area_selector, $this);
        
        $icon_area->addPreset(
            "padding",
            "icon_area_padding",
            __("Padding"),
            $adj_icon_area_selector
        )->whiteList();
        
        
        
        
        /**
         * Images
         */

        $image = $this->addControlSection("image", __("Images"), "assets/icon.png", $this);
        
        $image->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Display Image',
                'slug' => 'display_image')
            
        )->setValue(array( "show" => "Show", "hide" => "Hide" ))
         ->setDefaultValue('hide')->rebuildElementOnChange();
        
        $image_sizes = get_intermediate_image_sizes();
        
        $dropdown_options = array();
        foreach ($image_sizes as $image_size)
        {
            $dropdown_options[$image_size] = $image_size;
        }

        $image->addOptionControl(
            array(
                "type" => "dropdown",
                "name" => "Image Size",
                "slug" => "image_size",
                "condition" => "display_image=show"
            )
        )->setValue($dropdown_options)->setDefaultValue('thumbnail')->rebuildElementOnChange();
        
        $adjacent_image_selector = '.wp-post-image';
        
        $image->addStyleControl(
            array(
                "property" => 'width',
                "selector" => $adjacent_image_selector
            )
        );
        
        $image->boxShadowSection('Box Shadows', $adjacent_image_selector,$this);
        $image->borderSection('Borders', $adjacent_image_selector,$this);
        
        $image->addPreset(
            "margin",
            "image_margin",
            __("Margin"),
            $adjacent_image_selector
        )->whiteList();
        

    }
    
    function afterInit() {
        $this->removeApplyParamsButton();
    }
    
    
    function customCSS($options, $selector) {
        
        $css = '';
        
        if( ! $this->css_added ) {
        
            $css .= ".oxy-adjacent-posts {
                        display: flex;
                        width: 100%;
                        flex-direction: row;
                        justify-content: space-between;
                    }

                    .adj-post {
                        color: inherit;
                        width: 50%;
                        display: flex;
                        align-items: stretch;
                    }

                    .adj-post:only-child {
                        width: 50%;
                    }

                    .adj-post img {
                        align-self: center;
                        height: auto;
                        max-width: 100%;
                    }

                    .prev-post .adj-post_content {
                        align-items: flex-start;
                    }

                    .next-post .adj-post_content {
                        align-items: flex-end;
                    }

                    .adj-post_icon {
                        display: flex;
                    }

                    .prev-post {
                        text-align: left;
                    }

                    .next-post {
                        text-align: right;
                        justify-content: flex-end;
                    }

                    .adj-post_content {
                        display: flex;
                        flex-direction: column;
                    }

                    .adj-post svg {
                        fill: currentColor;
                        width: 1em;
                        height: 1em;
                    }";
            
            $this->css_added = true;
            
            
        }
        
        if (!isset($options["oxy-adjacent-posts_adjacent_post_fullwidth_below"]) || $options["oxy-adjacent-posts_adjacent_post_fullwidth_below"] === 'never') {
            return $css;
        } else {

            $max_width = oxygen_vsb_get_media_query_size($options["oxy-adjacent-posts_adjacent_post_fullwidth_below"]);
            $css .= "@media (max-width: {$max_width}px) {";
        
            $css .= "$selector {
                        flex-direction: column;
                     }
                     
                    $selector .adj-post {
                        width: 100%;
                    }";
        
             $css .= "}";
            
        }

        return $css;
    }

}


// All the parameters that can contain dynamic data, should be added to this filter
add_filter("oxy_base64_encode_options", 
    function($items) { 
        $items=array_merge($items, array('oxy-adjacent-posts_extras_previous_label', 'oxy-adjacent-posts_extras_next_label')); 
        return $items;
    }
);

new ExtraAdjPosts();