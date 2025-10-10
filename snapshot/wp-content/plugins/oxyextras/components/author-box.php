<?php

class ExtraAuthorBox extends OxygenExtraElements { 
        
    var $css_added = false;
    
	function name() {
        return 'Author Box';
    }
    
    function icon() {
        return plugin_dir_url(__FILE__) . 'assets/icons/'.basename(__FILE__, '.php').'.svg';
    }
    
    function extras_button_place() {
        return "single";
    }
    
    function enablePresets() {
        return true;
    }
    
    function tag() {
        return array('default' => 'section', 'choices' => 'div,section,span' );
    }
    
    function init() {
        
        add_filter("oxy_allowed_empty_options_list", array( $this, "allowedEmptyOptions") );

    }

    function render($options, $defaults, $content){ 
        
        // Get Options
        $image_size  = isset( $options['image_size'] ) ? esc_attr($options['image_size']) : '90';
        $archive_link_text = isset( $options['archive_link_text'] ) ? esc_attr($options['archive_link_text']) : '';
        $website_link_text = isset( $options['website_link_text'] ) ? esc_attr($options['website_link_text']) : '';
        $name_before = isset( $options['name_before'] ) ? esc_attr($options['name_before']) : '';
        $name_tag = esc_attr($options['name_tag']);

        if (null != $name_before) {
            $name_before_out = $name_before . ' ';
        }
            
  
        global $post;
        
        $output = '';

        // Detect if it is a single post with a post author
        if ( isset( $post->post_author ) ) {

            // Get author's display name 
            $display_name = get_the_author_meta( 'display_name', $post->post_author );

            // If display name is not available then use nickname as display name
            if ( empty( $display_name ) )
            $display_name = get_the_author_meta( 'nickname', $post->post_author );

            // Get author's biographical information or description
            $user_description = get_the_author_meta( 'user_description', $post->post_author );

            // Get author's website URL 
            $user_website = esc_url( get_the_author_meta('url', $post->post_author) );

            // Get link to the author archive page
            $user_posts = get_author_posts_url( get_the_author_meta( 'ID' , $post->post_author));

            if ( !isset( $options['image_display'] ) || $options['image_display'] === 'display' ) {

                $output .= '<div class="oxy-author-box_avatar">' . get_avatar( get_the_author_meta('user_email') , $image_size ). '</div>';

            }

            $output .= '<div class="oxy-author-box_info">';   

            
            // Author Name
            if ( !isset( $options['name_display'] ) || $options['name_display'] === 'display' ) {    

                if ( ! empty( $display_name ) ) {

                    $output .= '<'.$name_tag.' class="oxy-author-box_name">'. $name_before_out . $display_name . '</'.$name_tag.'>';  

                }   

            }

            // Author Bio
            if ( !isset( $options['bio_display'] ) || $options['bio_display'] === 'display' ) {        

                if ( ! empty( $user_description ) ) {

                    $output .= '<p class="oxy-author-box_bio">' . nl2br( $user_description ). '</p>';

                }

            }

            // Author Links
            if (!empty( $user_posts ) || !empty( $user_website ) ) {
                
                $output .= '<div class="oxy-author-box_links">';
            
                // Author Archive Link
                if ( ! empty( $user_posts ) ) {
                    if ( !isset( $options['archive_link_display'] ) || $options['archive_link_display'] === 'display' ) { 

                        $output .= '<a href="'. $user_posts .'">' . $archive_link_text . '</a> ';
                    }
                }

                // Author Website Link
                if ( ! empty( $user_website ) ) {

                    if ( !isset( $options['website_link_display'] ) || $options['website_link_display'] === 'display' ) {     

                        $output .= ' <a href="' . $user_website .'" target="_blank" rel="nofollow">' . $website_link_text . '</a>';
                    }

                }
                       
                $output .= '</div>';
                
            }
                
            $output .= '</div>';

        }
        
        echo $output;

        $this->dequeue_scripts_styles();
        

    }

    function class_names() {
        return array();
    }

    function controls() {
        
        
        /**
         * Layout & Spacing
         */
        
        $layout = $this->addControlSection("layout", __("Layout & Spacing"), "assets/icon.png", $this);
        $layout->flex('', $this);
        
        $info_align = $layout->addControl("buttons-list", "info_align", __("Text Align") );
        $info_align->setValue( array("Left","Center", "Right") );
        $info_align->setValueCSS( array(
            "Left" => "
                .oxy-author-box_info {
                    text-align: left;
                    justify-content: flex-start;
                }
            ",
            "Center" => "

                .oxy-author-box_info {
                    text-align: center;
                    justify-content: center;
                }
            ",
            "Right" => "
                .oxy-author-box_info {
                    text-align: right;
                    justify-content: flex-end;
                }
            ",
        ) );
        $info_align->whiteList();
        
        $layout->addStyleControls(
            array(
                array(
                    "name" => 'Padding Left',
                    "property" => 'padding-left',
                    "control_type" => "measurebox",
                    "value" => '0'
                ),
                array(
                    "name" => 'Padding Right',
                    "property" => 'padding-right',
                    "control_type" => "measurebox",
                    "value" => '0'
                ),
                array(
                    "name" => 'Padding Top',
                    "property" => 'padding-top',
                    "control_type" => "measurebox",
                    "value" => '0'
                ),
                array(
                    "name" => 'Padding Bottom',
                    "property" => 'padding-bottom',
                    "control_type" => "measurebox",
                    "value" => '0'
                )
            )
        );
        
       
        
        /**
         * Author Image
         */
        
        $image = $this->addControlSection("image", __("Image"), "assets/icon.png", $this);
        $image_selector = '.avatar';
        
        $image->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Profile Image Dispay',
                'slug' => 'image_display')
            
        )->setValue(array( "display" => "Display", "hide" => "Hide" ))
         ->setDefaultValue('display')->rebuildElementOnChange();
        
        
        
        $image_size_control = $image->addOptionControl(
            array(
                "type" => 'slider-measurebox',
                "name" => __('Image Size','oxygen'),
                "slug" => 'image_size',
                "default" => "90"
            )
        );
        
        $image_size_control->setRange('0','300','1');
        $image_size_control->rebuildElementOnChange();
        
        $image->borderSection('Borders', $image_selector,$this);
        $image->boxShadowSection('Shadows', $image_selector,$this);
        
        $image->addPreset(
            "margin",
            "image_margin",
            __("Margin"),
            $image_selector
        )->whiteList();
        
        
       
        /**
         * Display Name
         */
        
        $name = $this->addControlSection("name", __("Name"), "assets/icon.png", $this);
        $name_selector = '.oxy-author-box_name';
        
        $name->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Name Dispay'),
                'slug' => 'name_display')
            
        )->setValue(array( "display" => "Display", "hide" => "Hide" ))
         ->setDefaultValue('display')->rebuildElementOnChange();
        
        $name->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Before Text (Prefix)'),
                "slug" => 'name_before',
                "condition" => 'name_display=display',
                "default" => 'Written by',
            )
        )->rebuildElementOnChange();
        
        $name->typographySection('Typography', $name_selector,$this);
        
        $name->addOptionControl(
            array(
                "type" => "dropdown",
                "name" => __("Tag"),
                "slug" => "name_tag",
                "default" => 'h4',
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
        
        $name->addPreset(
            "margin",
            "name_margin",
            __("Margin"),
            $name_selector
        )->whiteList();
        
        
        
        /**
         * Author Bio
         */
        
        $bio = $this->addControlSection("bio", __("Bio"), "assets/icon.png", $this);
        $bio_selector = '.oxy-author-box_bio';
        
        $bio->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Author Bio Dispay',
                'slug' => 'bio_display')
            
        )->setValue(array( "display" => "Display", "hide" => "Hide" ))
         ->setDefaultValue('display')->rebuildElementOnChange();
        
        
        
        $bio->typographySection('Typography', $bio_selector,$this);
        
        $bio->addPreset(
            "margin",
            "bio_margin",
            __("Margin"),
            $bio_selector
        )->whiteList();
        
        
        
         /**
         * Author Links
         */
        
        $links = $this->addControlSection("links", __("Links"), "assets/icon.png", $this);
        $links_selector = '.oxy-author-box_links a';
        
        $links->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Archive Link',
                'slug' => 'archive_link_display')
            
        )->setValue(array( "display" => "Display", "hide" => "Hide" ))
         ->setDefaultValue('hide')->rebuildElementOnChange();
        
        $links->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Website Link',
                'slug' => 'website_link_display')
            
        )->setValue(array( "display" => "Display", "hide" => "Hide" ))
         ->setDefaultValue('hide')->rebuildElementOnChange();
        
        $links->typographySection('Typography', $links_selector,$this);
        
        $links_colors = $links->addControlSection("links_styles", __("Colors"), "assets/icon.png", $this);
        
        $links_colors->addStyleControls(
            array(
                array(
                    "name" => __('Background Color'),
                    "selector" => $links_selector,
                    "property" => 'background-color',
                ),
                array(
                    "name" => __('Background Hover Color'),
                    "selector" => $links_selector.":hover",
                    "property" => 'background-color',
                ),
                array(
                    "name" => __('Link Color'),
                    "selector" => $links_selector,
                    "property" => 'color',
                ),
                array(
                    "name" => __('Link Hover Color'),
                    "selector" => $links_selector.":hover",
                    "property" => 'color',
                )
            )
        );
        
        
        
        /**
         * Link Spacing
         */
        
        $links_spacing = $links->addControlSection("links_spacing", __("Link Spacing"), "assets/icon.png", $this);
        
        $links_spacing->addPreset(
            "padding",
            "links_spacing_padding",
            __("Padding"),
            $links_selector
        )->whiteList();
        
        $links_spacing->addPreset(
            "margin",
            "links_spacing_margin",
            __("Margin"),
            $links_selector
        )->whiteList();
        
        
        $link_text_control = $links->addControlSection("links_text", __("Link Text"), "assets/icon.png", $this);
        
        $link_text_control->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Archive Link Text'),
                "slug" => 'archive_link_text',
                "default" => 'All Posts',
            )
        )->rebuildElementOnChange();
        
        $link_text_control->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Website Link Text'),
                "slug" => 'website_link_text',
                "default" => "Website",
            )
        )->rebuildElementOnChange();
        
        
        $this->borderSection('Borders', '',$this);
        $this->boxShadowSection('Shadows', '',$this);
        
        $this->addTagControl();

    }
    
    function customCSS($options, $selector) {
        
        $css = "";
        
        if( ! $this->css_added ) {
        
            $css .= ".oxy-author-box {
                        display: flex;
                        flex-direction: row;
                    }

                    .oxy-author-box_info {
                        display: flex;
                        flex-direction: column;
                    }

                    .oxy-author-box_bio {
                        margin: 0;
                    }

                    .oxy-author-box_links a {
                        display: inline-block;
                    }

                    .oxy-author-box_avatar img {
                        vertical-align: middle;
                    }";
            
            $this->css_added = true;
            
        }

        return $css;
    }

    function allowedEmptyOptions($options) {

        $options_to_add = array(
            "oxy-author-box_name_before",
        );

        $options = array_merge($options, $options_to_add);

        return $options;
    }
    
    function afterInit() {
        $this->removeApplyParamsButton();
    }

}

new ExtraAuthorBox();