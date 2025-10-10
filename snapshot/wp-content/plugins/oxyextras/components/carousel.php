<?php

class ExtraCarousel extends OxygenExtraElements {
    
    var $js_added = false;
    var $js_fullscreen_added = false;
    var $js_fade_added = false;
    var $bg_lazy_added = false;
    var $sync_added = false;
    var $hash_added = false;
    var $css_added = false;

	function name() {
        return __('Carousel Builder'); 
    }
    
    function icon() {
        return plugin_dir_url(__FILE__) . 'assets/icons/'.basename(__FILE__, '.php').'.svg';
    }
    
    function enablePresets() {
        return true;
    }
    
    function enableFullPresets() {
        return true;
    }
    
    function init() {
        
        $this->El->useAJAXControls();
        $this->enableNesting();
        
    }
    
    
    function extras_button_place() {
        return "interactive";
    }
    
    
    function render($options, $defaults, $content) { 

        
        // get options
        $nav_type = isset( $options['nav_type'] ) ? esc_attr($options['nav_type']) : "";
        $prev_icon  = isset( $options['prev_icon'] ) ? esc_attr($options['prev_icon']) : "";
        $next_icon  = isset( $options['next_icon'] ) ? esc_attr($options['next_icon']) : "";
        
        $contain = isset( $options['contain'] ) ? esc_attr($options['contain']) : "";
        $percentage_position = isset( $options['percentage_position'] ) ? esc_attr($options['percentage_position']) : "";
        $free_scroll = isset( $options['free_scroll'] ) ? esc_attr($options['free_scroll']) : "";
        $draggable = isset( $options['draggable'] ) ? esc_attr($options['draggable']) : "";
        $wrap_around = isset( $options['wrap_around'] ) ? esc_attr($options['wrap_around']) : "";
        $maybe_group = isset( $options['maybe_group'] ) ? esc_attr($options['maybe_group']) : "";
        $group_cells = isset( $options['group_cells'] ) ? esc_attr($options['group_cells']) : "1";
        $group_percent = isset( $options['group_percent'] ) ? esc_attr($options['group_percent']) : "1";
        $maybe_pause_autoplay = isset( $options['maybe_pause_autoplay'] ) ? esc_attr($options['maybe_pause_autoplay']) : "";
        $autoplay = isset( $options['autoplay'] ) ? esc_attr($options['autoplay']) : "";
        $initial_index = isset( $options['initial_index'] ) ? esc_attr($options['initial_index']) : "";
        $maybe_accessibility = isset( $options['maybe_accessibility'] ) ? esc_attr($options['maybe_accessibility']) : "";
        $cell_align = isset( $options['cell_align'] ) ? esc_attr($options['cell_align']) : "";
        $right_to_left = isset( $options['right_to_left'] ) ? esc_attr($options['right_to_left']) : "";
        $images_loaded = isset( $options['images_loaded'] ) ? esc_attr($options['images_loaded']) : "";
        $page_dots = isset( $options['page_dots'] ) ? esc_attr($options['page_dots']) : "";
        $lazy_cells = isset( $options['lazy_cells'] ) ? esc_attr($options['lazy_cells']) : "";
        $maybe_lazy = isset( $options['maybe_lazy'] ) ? esc_attr($options['maybe_lazy']) : "";
        $as_nav_for = isset( $options['as_nav_for'] ) ? esc_attr($options['as_nav_for']) : "";
        $click_to_select = isset( $options['click_to_select'] ) ? esc_attr($options['click_to_select']) : "false";
        
        $parallax_bg = isset( $options['parallax_bg'] ) ? esc_attr($options['parallax_bg']) : "false";
        $parallax_bg_control = isset( $options['parallax_bg_control'] ) ? esc_attr($options['parallax_bg_control']) : "5";
        $maybe_force_heights = isset( $options['maybe_force_heights'] ) ? esc_attr($options['maybe_force_heights']) : "";
        
        $drag_threshold = isset( $options['drag_threshold'] ) ? esc_attr($options['drag_threshold']) : "";
        $selected_attraction = isset( $options['selected_attraction'] ) ? esc_attr($options['selected_attraction']) : "";
        $friction = isset( $options['friction'] ) ? esc_attr($options['friction']) : "";
        $free_scroll_friction = isset( $options['free_scroll_friction'] ) ? esc_attr($options['free_scroll_friction']) : "";
        
        $maybe_ticker = isset( $options['maybe_ticker'] ) ? esc_attr($options['maybe_ticker']) : "";
        $ticker = isset( $options['ticker'] ) ? esc_attr($options['ticker']) : "";
        
        $acf_field_name = isset( $options['acf_field_name'] ) ? esc_attr($options['acf_field_name']) : "";
        $gallery_image_size = isset( $options['gallery_image_size'] ) ? esc_attr($options['gallery_image_size']) : "large";
        $acf_return_format = isset( $options['acf_return_format'] ) ? esc_attr($options['acf_return_format']) : "";

        $metabox_image_field = isset( $options['metabox_image_field'] ) ? esc_attr($options['metabox_image_field']) : "";
        $metabox_group_field = isset( $options['metabox_group_field'] ) ? esc_attr($options['metabox_group_field']) : "";

        $metabox_data_source = isset( $options['metabox_data_source'] ) ? esc_attr($options['metabox_data_source']) : "";
        $metabox_option_name = isset( $options['metabox_option_name'] ) ? esc_attr($options['metabox_option_name']) : "";
        $metabox_post_id = isset( $options['metabox_post_id'] ) ? esc_attr($options['metabox_post_id']) : "";

        $shuffle = isset( $options['random_order'] ) ? esc_attr($options['random_order']) : "false";
        
        $acf_post_id = isset( $options['acf_post_id'] ) ? esc_attr($options['acf_post_id']) : "";
        
        if ('options' === esc_attr($options['acf_post_id'])) {
            $post_id = "options";
        } elseif ('custom' === esc_attr($options['acf_post_id'])) {
            $post_id = esc_attr($options['custom_post_id']);
        } else {
            $post_id = false;
        }

        $maybe_main_product = isset( $options['maybe_main_product'] ) ? esc_attr($options['maybe_main_product']) : "";
        
        $maybe_captions = isset( $options['maybe_captions'] ) ? esc_attr($options['maybe_captions']) : "";
        
        $maybe_fade = isset( $options['maybe_fade'] ) ? esc_attr($options['maybe_fade']) : "";
        
        $ticker_hover_pause = isset( $options['ticker_hover_pause'] ) ? esc_attr($options['ticker_hover_pause']) : "";
        
        $maybe_links = isset( $options['maybe_links'] ) ? esc_attr($options['maybe_links']) : "";
        
        $trigger_animations = isset( $options['trigger_animations'] ) ? esc_attr($options['trigger_animations']) : "";
        
        $trigger_animations_delay = isset( $options['trigger_animations_delay'] ) ? esc_attr($options['trigger_animations_delay']) : "";
        
        $maybe_sync = isset( $options['maybe_sync'] ) ? esc_attr($options['maybe_sync']) : "";
        
        $sync_carousel = isset( $options['sync_carousel'] ) ? esc_attr($options['sync_carousel']) : "";
        
        $resume_autoplay = isset( $options['resume_autoplay'] ) ? esc_attr($options['resume_autoplay']) : "";
        
        $maybe_fouc = isset( $options['maybe_fouc'] ) ? esc_attr($options['maybe_fouc']) : "";
        $maybe_srcset = isset( $options['maybe_srcset'] ) ? esc_attr($options['maybe_srcset']) : "";

        $maybe_hash = isset( $options['maybe_hash'] ) ? esc_attr($options['maybe_hash']) : "";

        $id = esc_attr($options['selector']);

        $maybe_cell_selector = isset( $options['maybe_cell_selector'] ) ? esc_attr($options['maybe_cell_selector']) : "";

        $no_image = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNgYAAAAAMAASsJTYQAAAAASUVORK5CYII=';
        
        
        if ( isset( $options['carousel_type'] ) && esc_attr($options['carousel_type'])  === 'repeater' ) {
            $carousel_selector = '.oxy-dynamic-list';
        } 
        else if ( isset( $options['carousel_type'] ) && esc_attr($options['carousel_type'])  === 'woo' ) {
            $carousel_selector = 'ul.products';
        }
        else if ( isset( $options['carousel_type'] ) && esc_attr($options['carousel_type'])  === 'easy_posts' ) {
            $carousel_selector = '.oxy-posts';
        }
        else if ( isset( $options['carousel_type'] ) && (esc_attr($options['carousel_type'])  === 'acf_gallery' || esc_attr($options['carousel_type'])  === 'medialibrary' || esc_attr($options['carousel_type'])  === 'woo_gallery' || esc_attr($options['carousel_type'])  === 'metabox_image')) {
            $carousel_selector = '.oxy-carousel-builder_gallery-images';
        }
        else {                
            $carousel_selector = '.oxy-inner-content';
        }
        
        
        if ( isset( $options['carousel_type'] ) && esc_attr($options['carousel_type'])  === 'woo' ) {
            $cell_selector = '.product';
        } 
        else if ( isset( $options['carousel_type'] ) && esc_attr($options['carousel_type'])  === 'easy_posts' ) {
            $cell_selector = '.oxy-post';
        }
        else if ( isset( $options['carousel_type'] ) && esc_attr($options['carousel_type'])  === 'custom' ) {
            $cell_selector = '.cell';
        }
        else if ( isset( $options['carousel_type'] ) && (esc_attr($options['carousel_type'])  === 'acf_gallery' || esc_attr($options['carousel_type'])  === 'medialibrary' || esc_attr($options['carousel_type'])  === 'woo_gallery' || esc_attr($options['carousel_type'])  === 'metabox_image')) {
            $cell_selector = '.oxy-carousel-builder_gallery-image';
        }
        else {
             $cell_selector = isset( $options['cell_selector'] ) ? esc_attr($options['cell_selector']) : "";
        }
        
        
        if ( isset( $options['nav_type'] ) && esc_attr($options['nav_type'])  === 'custom' ) {
            $previous_selector = isset( $options['previous_selector'] ) ? esc_attr($options['previous_selector']) : "";
            $next_selector = isset( $options['next_selector'] ) ? esc_attr($options['next_selector']) : "";
        } else {
            if ( ! function_exists('do_oxygen_elements') ) {
                $previous_selector = '#'. esc_attr($options['selector']) . ' .oxy-carousel-builder_prev';
                $next_selector = '#'. esc_attr($options['selector']) . ' .oxy-carousel-builder_next';  
            } else {
                $previous_selector = '.oxy-carousel-builder_prev';
                $next_selector = '.oxy-carousel-builder_next';  
            }
        }
        
        
        global $oxygen_svg_icons_to_load;
        $oxygen_svg_icons_to_load[] = $next_icon;
        $oxygen_svg_icons_to_load[] = $prev_icon;
        
        $output = '';
        $this->dequeue_scripts_styles();
                
        $output .= '<div class="oxy-carousel-builder_inner oxy-inner-content';
        
        $output .= ('true' !== $maybe_fouc) || (defined('OXY_ELEMENTS_API_AJAX') && OXY_ELEMENTS_API_AJAX) ? '': ' oxy-carousel-builder_hidden oxy-carousel-builder_fadein ';
        
        $output .= (defined('OXY_ELEMENTS_API_AJAX') && OXY_ELEMENTS_API_AJAX) ? ' extras-in-builder ' : '';
        
        $output .= '" ';
        
        $output .= 'data-prev="' . $previous_selector . '" ';
        $output .= 'data-next="' . $next_selector . '" ';
        $output .= 'data-contain="' . $contain . '" ';
        $output .= 'data-percent="' . $percentage_position . '" ';
        $output .= 'data-freescroll="' . $free_scroll . '" ';
        $output .= 'data-draggable="' . $draggable . '" ';
        $output .= 'data-wraparound="' . $wrap_around . '" ';
        $output .= 'data-carousel="' . $carousel_selector . '" ';
        $output .= 'data-cell="' . $cell_selector . '" ';

        if ( ( 'true' === $maybe_cell_selector ) && ( 'repeater' === esc_attr($options['carousel_type']) ) ) {
            $output .= 'data-repeater-cell="true" ';
        }
        
        $output .= 'data-dragthreshold="' . $drag_threshold . '" ';
        $output .= 'data-selectedattraction="' . $selected_attraction . '" ';
        $output .= 'data-friction="' . $friction . '" ';
        $output .= 'data-freescrollfriction="' . $free_scroll_friction . '" ';
        $output .= 'data-forceheight="' . $maybe_force_heights . '" ';
        $output .= 'data-fade="' . $maybe_fade . '" '; 
        $output .= 'data-tickerpause="' . $ticker_hover_pause . '" ';
     
        if (isset( $options['parallax_bg'] ) && esc_attr($options['parallax_bg']) === 'false') {    
            // No grouping if we have parallax on
            
            if ('true' === $maybe_group) {
                $output .= 'data-groupcells="true" ';
            }
            
            else if ('percent' === $maybe_group) {
                $output .= 'data-groupcells="' . $group_percent . '%" ';
            }
            
            else if ('false' === $maybe_group) {
                $output .= 'data-groupcells="false" ';
            }
            
            else  {
                $output .= 'data-groupcells="' . $group_cells . '" ';
            }
            
            
        }
        
        if (isset( $options['maybe_lazy'] ) && esc_attr($options['maybe_lazy']) === 'true') {    
            
            $output .= 'data-lazy="' . $lazy_cells . '" ';
            
        }
        
        if (isset( $options['maybe_bg_lazy'] ) && esc_attr($options['maybe_bg_lazy']) === 'true') {    
            
            $output .= 'data-bg-lazy="' . $lazy_cells . '" ';
            
        }
        
        if (isset( $options['maybe_resume_autoplay'] ) && esc_attr($options['maybe_resume_autoplay']) === 'true') {    
            
            $output .= 'data-resume-autoplay="' . $resume_autoplay . '" ';
            
        }
        
        
        $output .= 'data-autoplay="' . $autoplay . '" ';
        $output .= 'data-pauseautoplay="' . $maybe_pause_autoplay . '" '; 
        $output .= 'data-hash="' . $maybe_hash . '" '; 
        $output .= 'data-initial="' . $initial_index . '" ';
        $output .= 'data-accessibility="' . $maybe_accessibility . '" ';
        $output .= 'data-cellalign="' . $cell_align . '" ';
        $output .= 'data-righttoleft="' . $right_to_left . '" ';
        $output .= 'data-images-loaded="' . $images_loaded . '" ';
        $output .= 'data-pagedots="' . $page_dots . '" ';
        $output .= 'data-trigger-aos="' . $trigger_animations . '" ';
        
        $output .= ('true' === $trigger_animations) ? 'data-trigger-aos-delay="' . $trigger_animations_delay . '" ' : '';
        
        $output .= 'data-clickselect="' . $click_to_select . '" ';   
        
        
        if (isset( $options['wrap_around'] ) && esc_attr($options['wrap_around']) === 'false') {
            // No parallax if wrap around is on.
            $output .= 'data-parallaxbg="' . $parallax_bg . '" ';
            $output .= 'data-bgspeed="' . $parallax_bg_control . '" ';
        }

        if (isset( $options['editor_mode'] ) && esc_attr($options['editor_mode']) === 'preview') {
            
            $output .= 'data-preview="true" ';
            
        }
        
        if ( isset( $options['maybe_asnavfor'] ) && esc_attr($options['maybe_asnavfor'])  === 'true' ) {
            
            $output .= 'data-asnavfor="' . $as_nav_for . '" ';
            
        }
        
        if ( isset( $options['maybe_sync'] ) && esc_attr($options['maybe_sync'])  === 'true' ) {
            
            $output .= 'data-sync="' . $sync_carousel . '" ';
            
        }
        
        
        if ( isset( $options['adaptive_height'] ) && esc_attr($options['adaptive_height'])  === 'true' ) {
            
            $output .= 'data-adaptheight="true" ';
            
        }
        
        if ( isset( $options['maybe_fullscreen'] ) && esc_attr($options['maybe_fullscreen'])  === 'true' ) {
            
            $output .= 'data-fullscreen="true" ';
            
        }
        
        
        $output .= 'data-tick="' . $maybe_ticker . '" ';
        
        
        if ( isset( $options['maybe_ticker'] ) && esc_attr($options['maybe_ticker'])  === 'true' ) {
            
            $output .= 'data-ticker="' . $ticker . '" ';
        }
        
        
        $output .= '>';
        
        $gallery_src_attr = ( (defined('OXY_ELEMENTS_API_AJAX') && OXY_ELEMENTS_API_AJAX) || ( 'true' !== $maybe_lazy ) ) ? 'src' : 'data-flickity-lazyload';
        $gallery_srcset_attr = ( (defined('OXY_ELEMENTS_API_AJAX') && OXY_ELEMENTS_API_AJAX) || ( 'true' !== $maybe_lazy ) ) ? 'srcset' : 'data-flickity-lazyload-srcset';
        
        
        
        if ( ( esc_attr($options['carousel_type']) !== 'acf_gallery' ) && ( esc_attr($options['carousel_type']) !== 'medialibrary' ) && ( esc_attr($options['carousel_type']) !== 'woo_gallery' ) && ( esc_attr($options['carousel_type']) !== 'metabox_image' ) ) {
            
            if ( $content ) {
            
                if ( function_exists('do_oxygen_elements') ) {
                    $output .=  do_oxygen_elements($content); 
                }
                else {
                    $output .=  do_shortcode($content); 
                }
                
            }
            

        } else if ( esc_attr($options['carousel_type']) === 'metabox_image' ) {

            if ( !function_exists( 'rwmb_meta' ) ) {
                return;
            }

            if ('settings' === $metabox_data_source) {
                $metabox_args = ['object_type' => 'setting', 'size' => $gallery_image_size];
                $post_id = $metabox_option_name; 
            } 
            else if ('custom' === $metabox_data_source) {
                $metabox_args = ['size' => $gallery_image_size];
                $post_id = $metabox_post_id;
            }
            else {
                $metabox_args = ['size' => $gallery_image_size];
                $post_id = null; 
            }

            if ( empty( $metabox_group_field ) ) {
                $images = rwmb_meta( $metabox_image_field, $metabox_args, $post_id );
            }

            else  {
                $image_group = rwmb_meta( $metabox_group_field, $metabox_args, $post_id );
                $images = isset( $image_group[$metabox_image_field] ) ? $image_group[$metabox_image_field] : '';
            }

            if( $images ) {

                if ('true' === $shuffle) {
                    shuffle($images);
                }

                $output .= '<div class=oxy-carousel-builder_gallery-images>';
                    foreach ( $images as $key => $image ) {

                        if ( !empty( $metabox_group_field ) ) {
                            $image = RWMB_Image_Field::file_info( $image, array( 'size' => $gallery_image_size ) );
                        }

                        if ('true' === $maybe_links) {
                            $output .= '<a href="'. $image['full_url']. '" class="oxy-carousel-builder_gallery-image" id=' . $id . '-' . $key . '>';
                        } else {
                            $output .= '<div class="oxy-carousel-builder_gallery-image" id=' . $id . '-' . $key . '>';
                        }
                        $output .= $image['caption'] && ('true' === $maybe_captions) ? '<span class="oxy-carousel-builder_gallery-image-wrapper">' : '';
                        if ('false' !== $maybe_srcset) {
                            $output .= ( 'true' !== $maybe_lazy ) ? wp_get_attachment_image( $image['ID'], $gallery_image_size, array( "alt" => $image['alt'] ), array( 'loading' => false ) ) : '<img src="'. $no_image .'" '. $gallery_srcset_attr .'="'. wp_get_attachment_image_srcset( $image['ID'], $gallery_image_size ) .'" alt="' . $image['alt'] . '">';
                        } else {
                            if ( 'true' !== $maybe_lazy ) {
                                $output .= wp_get_attachment_image( $image['ID'], $gallery_image_size, false, array( 'loading' => false, 'srcset' => wp_get_attachment_image_url( $image['ID'], $gallery_image_size ) ) );
                            } else {
                                $output .= '<img '. $gallery_src_attr .'="'. wp_get_attachment_image_url( $image['ID'], $gallery_image_size ) .'" alt="' . $image['alt'] . '">';
                            }

                        }
                        $output .= $image['caption'] && ('true' === $maybe_captions) ? '<span class="oxy-carousel-builder_caption">'. $image['caption'] .'</span></span>' : '';

                        $output .= ('true' === $maybe_links) ? '</a>' : '</div>'; 
                    }
                $output .= '</div>';
            }

            
        } else if ( esc_attr($options['carousel_type']) === 'acf_gallery' ) {

            if ( !function_exists( 'get_field' ) ) {
                return;
            }
            
            $images = get_field($acf_field_name, $post_id) ? get_field($acf_field_name, $post_id) : get_sub_field($acf_field_name);
            $size = $gallery_image_size;
            
            if( $images ) {

                if ('true' === $shuffle) {
                    shuffle($images);
                }
                
                 $output .= '<div class=oxy-carousel-builder_gallery-images>';
                    foreach( $images as $key => $image_id ):

                        if ('true' === $maybe_links) {
                            $output .= '<a href="';
                            $output .=  'array' === $acf_return_format ? $image_id['url'] : wp_get_attachment_url( $image_id );
                            $output .= '" class="oxy-carousel-builder_gallery-image" id=' . $id . '-' . $key . '>';
                         } else {
                            $output .= '<div class="oxy-carousel-builder_gallery-image" id=' . $id . '-' . $key . '>';
                         }
                             
                        if ('array' === $acf_return_format) {

                            //$output .= 'gallery size: ' . $gallery_image_size;
                            //$output .= '<br> image src: ' . esc_url($image_id['sizes'][$size]);
                            
                            $image_source = ('full' === $gallery_image_size) ? esc_url($image_id['url']) : esc_url($image_id['sizes'][$gallery_image_size]);

                            $output .= $image_id['caption'] && ('true' === $maybe_captions) ? '<span class="oxy-carousel-builder_gallery-image-wrapper">' : '';
                            $output .= '<img '. $gallery_src_attr .'="'. $image_source .'" alt="' . $image_id['alt'] . '" />';
                            $output .= $image_id['caption'] && ('true' === $maybe_captions) ? '<span class="oxy-carousel-builder_caption">'. $image_id['caption'] .'</span></span>' : '';
                        } else {
                            $image_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true);
                            $output .=  ( 'true' !== $maybe_lazy ) ? wp_get_attachment_image( $image_id, $size, '', array( "alt" => $image_alt ), array( 'loading' => false ) ) : '<img '. $gallery_src_attr .'="'. wp_get_attachment_image_url( $image_id, $size ) .'" '. $gallery_srcset_attr .'="'. wp_get_attachment_image_srcset( $image_id, $size ) .'" alt="'. $image_alt .'">';
                        }
                             
                        $output .= ('true' === $maybe_links) ? '</a>' : '</div>';     
                        
                
                    endforeach;
                $output .= '</div>';

            }
            
        } else if ( esc_attr($options['carousel_type']) === 'woo_gallery' ) {
            
            if (!function_exists('wc_get_product')){
                return;
            }

            if (!wc_get_product()) {
                return;
            }

            $images = wc_get_product()->get_gallery_image_ids();

            if ('true' === $maybe_main_product) {
                $product_image = wc_get_product()->get_image_id();
                array_unshift($images,$product_image);
            }

            if( $images ) {

                if ('true' === $shuffle) {
                    shuffle($images);
                }
                
                $output .= '<div class=oxy-carousel-builder_gallery-images>';

                foreach( $images as $key => $image_id ) {

                    if ('true' === $maybe_links) {
                        $output .= '<a href="';
                        $output .=  wp_get_attachment_url( $image_id );
                        $output .= '" class="oxy-carousel-builder_gallery-image" id=' . $id . '-' . $key . '>';
                     } else {
                        $output .= '<div class="oxy-carousel-builder_gallery-image" id=' . $id . '-' . $key . '>';
                     }

                    $output .=  wp_get_attachment_image( $image_id, $gallery_image_size );

                    $output .= ('true' === $maybe_links) ? '</a>' : '</div>';  

                }

                $output .= '</div>';

            }
            
        } else {
            
            $images = explode( ",", $options['media_images'] );
            $size = $gallery_image_size;

            if( $images ) {

                if ('true' === $shuffle) {
                    shuffle($images);
                }
            
                $output .= '<div class=oxy-carousel-builder_gallery-images>';
                
                        foreach( $images as $key => $image_id ):
                
                            if ('true' === $maybe_links) {
                                $output .= '<a href="'. wp_get_attachment_url( $image_id ) . '" class="oxy-carousel-builder_gallery-image" id=' . $id . '-' . $key . '>';
                            } else {
                                $output .= '<div class="oxy-carousel-builder_gallery-image" id=' . $id . '-' . $key . '>';
                            }
                
                            $output .= wp_get_attachment_caption($image_id) && ('true' === $maybe_captions) ? '<span class="oxy-carousel-builder_gallery-image-wrapper">' : '';

                            if ('false' !== $maybe_srcset) {
                                $output .= ( 'true' !== $maybe_lazy ) ? wp_get_attachment_image( $image_id, $size, false, array( 'loading' => false ) ) : '<img '. $gallery_srcset_attr .'="'. wp_get_attachment_image_srcset( $image_id, $size ) .'" alt="' . get_post_meta($image_id, '_wp_attachment_image_alt', TRUE) . '">';
                            } else {
                                $output .= ( 'true' !== $maybe_lazy ) ? wp_get_attachment_image( $image_id, $size, false, array( 'loading' => false, 'srcset' => wp_get_attachment_image_url( $image_id, $size ) ) ) : '<img '. $gallery_src_attr .'="'. wp_get_attachment_image_url( $image_id, $size ) .'" alt="' . get_post_meta($image_id, '_wp_attachment_image_alt', TRUE) . '">';
                            }
                            $output .= wp_get_attachment_caption($image_id) && ('true' === $maybe_captions) ? '<span class="oxy-carousel-builder_caption">'. wp_get_attachment_caption($image_id) .'</span></span>' : '';
                            
                            $output .= ('true' === $maybe_links) ? '</a>' : '</div>';  
                            
                        endforeach;
                $output .= '</div>';

            }
            
            
        }
           
        $output .= '</div>';
        
        if ('icon' === $nav_type) {
            
            $output .= '<div class="oxy-carousel-builder_icon oxy-carousel-builder_prev"><svg id="prev' . esc_attr($options['selector']) . '"><use xlink:href="#' . $prev_icon .'"></use></svg></span></div>';
            $output .= '<div class="oxy-carousel-builder_icon oxy-carousel-builder_next"><svg id="next' . esc_attr($options['selector']) . '"><use xlink:href="#' . $next_icon .'"></use></svg></span></div>';
            
        }
        
        echo $output;

        $inline = "jQuery('#%%ELEMENT_ID%%').find('.oxy-inner-content').after('<ol class=flickity-page-dots><li class=dot></li><li class=dot></li><li class=dot></li></ol>');";
        
        if  (isset( $options['editor_mode'] ) && esc_attr($options['editor_mode']) === 'preview') {
        
            if (defined('OXY_ELEMENTS_API_AJAX') && OXY_ELEMENTS_API_AJAX) {
                echo '<script type="text/javascript" src="'. plugin_dir_url( __FILE__ ) . 'assets/flickity/flickity.pkgd.min.js?ver=2.2.1'.'" id="flickity-js"></script>';
                echo '<script type="text/javascript" src="'. plugin_dir_url( __FILE__ ) . 'assets/flickity/flickity-fade.js?ver=2.2.1'.'" id="flickity-fade-js"></script>';
            }
            
            $inline .= file_get_contents( plugin_dir_path(__FILE__) . 'assets/flickity/flickity.js' );

        }

        if( method_exists('OxygenElement', 'builderInlineJS') ) {   
            $this->El->builderInlineJS($inline); 
        }
        
        // Only load each JS file once
        if ($this->js_added !== true) {
            if (!defined('OXY_ELEMENTS_API_AJAX') || !OXY_ELEMENTS_API_AJAX) {
                add_action( 'wp_footer', array( $this, 'output_js' ) );
            }
        }
        
        if ( isset( $options['maybe_fullscreen'] ) && esc_attr($options['maybe_fullscreen'])  === 'true' ) {
            
            if ($this->js_fullscreen_added !== true) {
                if (!defined('OXY_ELEMENTS_API_AJAX') || !OXY_ELEMENTS_API_AJAX) {
                    add_action( 'wp_footer', array( $this, 'output_fullscreen_js' ), 12 );
                }
                $this->js_fullscreen_added = true;
            }

        }
        
        if ( isset( $options['maybe_fade'] ) && esc_attr($options['maybe_fade'])  === 'true' ) {
            
            if ($this->js_fade_added !== true) {
                if (!defined('OXY_ELEMENTS_API_AJAX') || !OXY_ELEMENTS_API_AJAX) {
                    add_action( 'wp_footer', array( $this, 'output_fade_js' ), 12 );
                }
                $this->js_fade_added = true;

            }

        }
        
        if ( isset( $options['maybe_bg_lazy'] ) && esc_attr($options['maybe_bg_lazy'])  === 'true' ) {
            
            if ($this->bg_lazy_added !== true) {
                if (!defined('OXY_ELEMENTS_API_AJAX') || !OXY_ELEMENTS_API_AJAX) {
                    add_action( 'wp_footer', array( $this, 'output_bg_lazy_js' ), 12 );
                }
                $this->bg_lazy_added = true;

            }

        }  

        if ( isset( $options['maybe_hash'] ) && esc_attr($options['maybe_hash'])  === 'true' ) {
            
            if ($this->bg_lazy_added !== true) {
                if (!defined('OXY_ELEMENTS_API_AJAX') || !OXY_ELEMENTS_API_AJAX) {
                    add_action( 'wp_footer', array( $this, 'output_hash_js' ), 12 );
                }
                $this->hash_added = true;

            }

        }  
        
        if ( isset( $options['maybe_sync'] ) && esc_attr($options['maybe_sync'])  === 'true' ) {
            
            if ($this->sync_added !== true) {
                if (!defined('OXY_ELEMENTS_API_AJAX') || !OXY_ELEMENTS_API_AJAX) {
                    add_action( 'wp_footer', array( $this, 'output_sync_js' ), 13 );
                }
                $this->sync_added = true;
            }

        }  
        
        if ($this->js_added !== true) {
            if (!defined('OXY_ELEMENTS_API_AJAX') || !OXY_ELEMENTS_API_AJAX) {
                add_action( 'wp_footer', array( $this, 'output_init_js' ), 15 );
            }
            $this->js_added = true;
        }  
        
        
        
    }
    

    function class_names() {
        return array('');
    }

    function controls() {
        
        /**
         * Selectors
         */ 
        $gallery_cell_selector = '.oxy-carousel-builder_gallery-image'; 
        $repeater_div = '.oxy-dynamic-list > .ct-div-block, .oxy-dynamic-list .flickity-slider > .ct-div-block';
        $product_list_div = 'ul.products .product, ul.products .flickity-slider > .product';
        $easy_posts_div = '.oxy-posts .oxy-post';
        $cell_div = '.cell';
        $dots_selector = '.flickity-page-dots';
        $dot_selector = '.flickity-page-dots .dot';
        $dot_selected_selector = '.flickity-page-dots .dot.is-selected';
        $cell_selected = '.is-selected';
        $cell_previous = '.is-previous';
        $cell_next = '.is-next';
        
        
      
        
        $editor_mode_control = $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('In-Editor Mode'),
                'slug' => 'editor_mode',
            )
            
        )->setValue(array( 
            "edit" => "Edit",
            "preview" => "Preview",
            )
        )->setDefaultValue('edit');
        $editor_mode_control->setParam("description", __("Always click 'Apply Params' button to apply"));
        $editor_mode_control->setParam('ng_show', "!iframeScope.isEditing('state')&&!iframeScope.isEditing('media')");
        
        
        $this->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => 'Carousel content',
                'slug' => 'carousel_type',
            )
        )->setValue(array( 
            "repeater" => "Repeater", 
            "easy_posts" => "Easy posts",
            "woo" => "Woo components",
            "woo_gallery" => "Woo product gallery",
            "acf_gallery" => "ACF Gallery",
            "metabox_image" => "Meta Box gallery",
            "custom" => "Custom elements (.cell)",
            "medialibrary" => "Media library gallery",
            )
        )->setValueCSS( array(
            "woo"  => "",
            "custom" => "
                        .oxy-inner-content {
                            display: flex;
                            flex-direction: row;
                            flex-wrap: nowrap;
                        }
                        .cell {
                            flex-shrink: 0;
                        }
                        
                        .oxy-carousel-builder_hidden {
                            display: none;
                        }
            ",
            "acf_gallery" => "
                        .oxy-inner-content > *:not('.oxy-carousel-builder_gallery-images'){
                            display: none;
                        }
            ",
            "metabox_image" => "
                        .oxy-inner-content > *:not('.oxy-carousel-builder_gallery-images'){
                            display: none;
                        }
            ",
            "medialibrary" => "
                        .oxy-inner-content > *:not('.oxy-carousel-builder_gallery-images'){
                            display: none;
                        }
            ",
            "woo_gallery" => "
                        .oxy-inner-content > *:not('.oxy-carousel-builder_gallery-images'){
                            display: none;
                        }
            ",

                
        ) )->setParam('ng_show', "!iframeScope.isEditing('state')");
        
        
        $mediaLibraryControl = $this->addCustomControl("
			<div class='oxygen-control'>
				<div class='oxygen-file-input'
				ng-class=\"{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'media_images')}\">
					<input type=\"text\" spellcheck=\"false\" ng-model=\"iframeScope.component.options[iframeScope.component.active.id]['model']['media_images']\" ng-model-options=\"{ debounce: 10 }\"
						ng-change=\"iframeScope.setOption(iframeScope.component.active.id,'oxy-carousel-builder','media_images');\">
					<div class=\"oxygen-file-input-browse\"
						data-mediaTitle=\"Select Images\" 
						data-mediaButton=\"Select Images\"
						data-mediaMultiple=\"true\"
						data-mediaProperty=\"media_images\"
						data-mediaType=\"gallery\">". __("Browse","oxygen") . "
					</div>
				</div>
			</div>",
			'media_images'
    	);
        $mediaLibraryControl->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-carousel-builder_carousel_type']=='medialibrary'");
        $mediaLibraryControl->setParam('heading', __('Image IDs', 'oxygen'));
        
        
        $this->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('ACF field source'),
                'slug' => 'acf_post_id',
                'condition' => 'carousel_type=acf_gallery'
            )
            
        )->setDefaultValue('page')->setValue(array( 
            "options" => "Options page",
            "page" => "Current page",
            "custom" => "Custom post ID"
            )
        );
        
        $this->addOptionControl(
            array(
                'type' => 'textfield',
                'name' => __('Post ID'),
                'slug' => 'custom_post_id',
                'condition' => 'carousel_type=acf_gallery&&acf_post_id=custom'
            )
        );
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('ACF gallery field name'),
                "slug" => 'acf_field_name',
                "default" => '',
                "condition" => 'carousel_type=acf_gallery'
            )
        );
        
        $this->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Data source'),
                'slug' => 'metabox_data_source',
                'condition' => 'carousel_type=metabox_image'
            )
            
        )->setDefaultValue('page')->setValue(array( 
            "settings" => "Settings page",
            "page" => "Current page",
            "custom" => "Post ID"
            )
        );

        $this->addOptionControl(
            array(
                'type' => 'textfield',
                'name' => __('Option name'),
                'slug' => 'metabox_option_name',
                'condition' => 'carousel_type=metabox_image&&metabox_data_source=settings'
            )
        );

        $this->addOptionControl(
            array(
                'type' => 'textfield',
                'name' => __('Post ID'),
                'slug' => 'metabox_post_id',
                'default' => '1',
                'condition' => 'carousel_type=metabox_image&&metabox_data_source=custom'
            )
        )->setParam('dynamicdatacode', '<div optionname="\'oxy-pro-accordion_metabox_post_id\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');
        

        
        $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __("Image return format"),
                'slug' => 'acf_return_format',
                "condition" => 'carousel_type=acf_gallery'
            )
            
        )->setValue(array( 
            "array" => "Image Array", 
            "id" => "Image ID",
            )           
        )->setDefaultValue('id');

        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Metabox image field'),
                "slug" => 'metabox_image_field',
                "default" => '',
                "condition" => 'carousel_type=metabox_image'
            )
        ); 
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Group field (leave blank if not in group)'),
                "slug" => 'metabox_group_field',
                "default" => '',
                "condition" => 'carousel_type=metabox_image'
            )
        ); 
        
        
        $image_sizes = get_intermediate_image_sizes();
        $image_sizes[] = "full";

        $this->addOptionControl(
            array(
                "type" => "dropdown",
                "name" => __("Image Size"),
                "slug" => "gallery_image_size",
                //"default" => 'full',
                "condition" => 'carousel_type=acf_gallery||carousel_type=medialibrary||carousel_type=metabox_image||carousel_type=woo_gallery'
            )
        )->setValue($image_sizes)
         ->rebuildElementOnChange();
        

         $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __("Include main product image"),
                'slug' => 'maybe_main_product',
                "condition" => 'carousel_type=woo_gallery'
            )
            
        )->setValue(array( 
            "true" => "True", 
            "false" => "False",
            )           
        )->setDefaultValue('false')
        ->rebuildElementOnChange();
        
        
        $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __("Add links to images"),
                'slug' => 'maybe_links',
                "condition" => 'carousel_type=acf_gallery||carousel_type=medialibrary||carousel_type=metabox_image||carousel_type=woo_gallery'
            )
            
        )->setValue(array( 
            "true" => "True", 
            "false" => "False",
            )           
        )->setDefaultValue('false')
        ->setParam("description", __("For lightbox compatibility use the link selector '.oxy-carousel-builder_gallery-image' in the lightbox settings"));
        
        $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __("Random order"),
                'slug' => 'random_order',
                "condition" => 'carousel_type=acf_gallery||carousel_type=medialibrary||carousel_type=metabox_image||carousel_type=woo_gallery'
            )
            
        )->setValue(array( 
            "true" => "True", 
            "false" => "False",
            )           
        )->setDefaultValue('false')
        ->rebuildElementOnChange();
        
        
        
        
        /**
         * Cells
         */ 
        $cells_section = $this->addControlSection("cells_section", __("Cells"), "assets/icon.png", $this);
        
        
        $cells_section->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Prioritize property'),
                'slug' => 'prioritise_width',
                "condition" => 'editor_mode!=preview&&carousel_type!=easy_posts&&carousel_type!=custom&&carousel_type!=woo&&carousel_type!=repeater'
            )
            
        )->setValue(array( 
            "enable" => "Width (setting the width only)", 
            "disable" => "Height (setting the height only)",
            "none" => "None (setting both)",
            )           
        )->setDefaultValue('none')
         ->setValueCSS( array(
            "enable"  => " .oxy-carousel-builder_gallery-image img {
                              height: auto;
                              width: 100%;
                            }
                            
                            .oxy-carousel-builder_gallery-images {
                                height: auto;
                            }",
             "none"  => " .oxy-carousel-builder_gallery-image {
                              display: flex;
                            }",
                
        ) )->setParam("description", __("To preserve the image aspect ratio, choose which property you are setting. <a style='color: lightskyblue;' target='_blank' href=https://oxyextras.com/understanding-cell-dimensions-in-the-carousel-builder/ >more info</a>"));
        
        
        $cells_section->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Width setting'),
                'slug' => 'maybe_autofit',
                "condition" => 'editor_mode!=preview'
            )
            
        )->setValue(array( 
            "width" => "Set width value", 
            "auto" => "Set no. of visible cells",
            )           
        )->setDefaultValue('width')
         ->setValueCSS( array(
            "auto"  => "  .oxy-posts .oxy-post {
                            margin-right: var(--carousel-space-between);
                            width: var(--carousel-cell-width);
                        }
                        
                        .cell {
                            margin-right: var(--carousel-space-between);
                            width: var(--carousel-cell-width);
                        }
                        
                        .oxy-carousel-builder_gallery-image {
                            margin-right: var(--carousel-space-between);
                            width: var(--carousel-cell-width);
                        }
                        
                        .oxy-dynamic-list > .ct-div-block, .oxy-dynamic-list .flickity-slider > .ct-div-block {
                            margin-right: var(--carousel-space-between);
                            width: var(--carousel-cell-width);
                        }
                        
                        ul.products .product, ul.products .flickity-slider > .product {
                            margin-right: var(--carousel-space-between);
                            width: var(--carousel-cell-width);
                        }
                    ",
             )
        );
        
        
        $cells_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('No. of visible cells in carousel viewport'),
                'slug' => 'autofit',
                "condition" => 'editor_mode!=preview&&maybe_autofit=auto'
            )
            
        )->setValue(array( 
            "1" => "1", 
            "2" => "2",
            "3" => "3",
            "4" => "4",
            "6" => "6",
            "8" => "8",
            )           
        )->setValueCSS( array(
            "1"  => " {
                        --carousel-cell-width: 100%;
                        }
                        ",
            "2"  => " {
                        --carousel-cell-width: calc((100% - var(--carousel-space-between)) / 2);
                        }
                        ",
            "3"  => " {
                        --carousel-cell-width: calc((100% - (2 * var(--carousel-space-between))) / 3);
                        }
                        ",
            "4"  => " {
                        --carousel-cell-width: calc((100% - (3 * var(--carousel-space-between))) / 4);
                        }
                        ",
            "6"  => " {
                        --carousel-cell-width: calc((100% - (5 * var(--carousel-space-between))) / 6);
                        }
                        ",
            "8"  => " {
                        --carousel-cell-width: calc((100% - (7 * var(--carousel-space-between))) / 8);
                        }
                        ",
        ))->whiteList();
        
        
        $cells_section->addStyleControl( 
            array(
                "name" => __('Cell width'),
                "type" => 'measurebox',
                "property" => 'width',
                "control_type" => 'slider-measurebox',
                "selector" => $repeater_div,
                "condition" => 'editor_mode!=preview&&carousel_type=repeater&&maybe_autofit!=auto'
                
            )
        )
        ->setUnits('%')
        ->setRange('0','100','.1');
        
        $cells_section->addStyleControl( 
            array(
                "name" => __('Cell width'),
                "type" => 'measurebox',
                "property" => 'width',
                "control_type" => 'slider-measurebox',
                "selector" => $product_list_div,
                "condition" => 'editor_mode!=preview&&carousel_type=woo&&maybe_autofit!=auto'
                
            )
        )
        ->setUnits('%')
        ->setRange('0','100','.1');
        
        $cells_section->addStyleControl( 
            array(
                "name" => __('Cell width'),
                "type" => 'measurebox',
                "property" => 'width',
                "control_type" => 'slider-measurebox',
                "selector" => $cell_div,
                "condition" => 'editor_mode!=preview&&carousel_type=custom&&maybe_autofit!=auto'
                
            )
        )
        ->setUnits('%')
        ->setRange('0','100','.1');
        
        
        
            
        $cells_section->addStyleControl( 
            array(
                "name" => __('Cell width'),
                "type" => 'measurebox',
                "property" => 'width',
                "control_type" => 'slider-measurebox',
                "selector" => $easy_posts_div,
                "condition" => 'editor_mode!=preview&&carousel_type=easy_posts&&maybe_autofit!=auto'
                
            )
        )
        ->setUnits('%')
        ->setRange('0','100','.1'); 
        
        
        $cells_section->addStyleControl( 
            array(
                "name" => __('Cell width'),
                "type" => 'measurebox',
                "property" => 'width',
                "control_type" => 'slider-measurebox',
                "selector" => $gallery_cell_selector,
                "condition" => 'editor_mode!=preview&&carousel_type!=easy_posts&&carousel_type!=custom&&carousel_type!=woo&&carousel_type!=repeater&&maybe_autofit!=auto'
                
            )
        )
        ->setUnits('%')
        ->setRange('0','100','.1'); 
        
        
        $cells_section->addStyleControl( 
            array(
                "name" => __('Cell height'),
                "type" => 'measurebox',
                "property" => 'height',
                "control_type" => 'slider-measurebox',
                "selector" => '.oxy-dynamic-list > .ct-div-block, .oxy-dynamic-list .flickity-slider > .ct-div-block, .oxy-inner-content .oxy-dynamic-list',
                "condition" => 'editor_mode!=preview&&carousel_type=repeater'
            )
        )
        ->setUnits('px')    
        ->setRange('0','1000','1');
        
        
        $cells_section->addStyleControl( 
            array(
                "name" => __('Cell height'),
                "type" => 'measurebox',
                "property" => 'height',
                "control_type" => 'slider-measurebox',
                "selector" => '.oxy-carousel-builder_gallery-image img, .oxy-carousel-builder_gallery-images',
                "condition" => 'editor_mode!=preview&&carousel_type!=easy_posts&&carousel_type!=custom&&carousel_type!=woo&&carousel_type!=repeater'
            )
        )
        ->setUnits('px')    
        ->setRange('0','1000','1');
        
        
        
        $cells_section->addStyleControl( 
            array(
                "name" => __('Cell height'),
                "type" => 'measurebox',
                "property" => 'height',
                "control_type" => 'slider-measurebox',
                "selector" => '.products > .product, .products .flickity-slider > .product, .oxy-inner-content .products',
                "condition" => 'editor_mode!=preview&&carousel_type=woo'
            )
        )
        ->setUnits('px')    
        ->setRange('0','1000','1');
        
        $cells_section->addStyleControl( 
            array(
                "name" => __('Cell height'),
                "type" => 'measurebox',
                "property" => 'height',
                "control_type" => 'slider-measurebox',
                "selector" => '.oxy-posts > .oxy-post, .oxy-posts .flickity-slider > .oxy-post, .oxy-inner-content .oxy-posts',
                "condition" => 'editor_mode!=preview&&carousel_type=easy_posts'
            )
        )
        ->setUnits('px')    
        ->setRange('0','1000','1');
        
        $cells_section->addStyleControl( 
            array(
                "name" => __('Cell height'),
                "type" => 'measurebox',
                "property" => 'height',
                "control_type" => 'slider-measurebox',
                "selector" => '.cell, .oxy-inner-content .flickity-slider > .cell, .oxy-inner-content',
                "condition" => 'editor_mode!=preview&&carousel_type=custom'
            )
        )
        ->setUnits('px')    
        ->setRange('0','1000','1');
        
        $cells_section->addStyleControl( 
            array(
                "name" => __('Space between cells'),
                "type" => 'measurebox',
                "default" => "0",
                "units" => 'px',
                "property" => '--carousel-space-between',
                "control_type" => 'slider-measurebox',
                "condition" => 'editor_mode!=preview&&maybe_autofit=auto'
            )
        )
        ->setUnits('px')
        ->setRange('0','100','1');
        
        $cells_section->addStyleControl( 
            array(
                "name" => __('Space between cells'),
                "type" => 'measurebox',
                "default" => "0",
                "units" => 'px',
                "property" => 'margin-right',
                "control_type" => 'slider-measurebox',
                "selector" => $repeater_div,
                "condition" => 'editor_mode!=preview&&carousel_type=repeater&&maybe_autofit!=auto'
            )
        )
        ->setUnits('px')
        ->setRange('0','100','1');
        
        $cells_section->addStyleControl( 
            array(
                "name" => __('Space between cells'),
                "type" => 'measurebox',
                "default" => "0",
                "units" => 'px',
                "property" => 'margin-right',
                "control_type" => 'slider-measurebox',
                "selector" => $product_list_div,
                "condition" => 'editor_mode!=preview&&carousel_type=woo&&maybe_autofit!=auto'
            )
        )
        ->setUnits('px')
        ->setRange('0','100','1');
        
        $cells_section->addStyleControl( 
            array(
                "name" => __('Space between cells'),
                "type" => 'measurebox',
                "default" => "0",
                "units" => 'px',
                "property" => 'margin-right',
                "control_type" => 'slider-measurebox',
                "selector" => $easy_posts_div,
                "condition" => 'editor_mode!=preview&&carousel_type=easy_posts&&maybe_autofit!=auto'
            )
        )
        ->setUnits('px')
        ->setRange('0','100','1');
        
        $cells_section->addStyleControl( 
            array(
                "name" => __('Space between cells'),
                "type" => 'measurebox',
                "default" => "0",
                "units" => 'px',
                "property" => 'margin-right',
                "control_type" => 'slider-measurebox',
                "selector" => $gallery_cell_selector,
                "condition" => 'editor_mode!=preview&&carousel_type!=easy_posts&&carousel_type!=custom&&carousel_type!=woo&&carousel_type!=repeater&&maybe_autofit!=auto'
            )
        )
        ->setUnits('px')
        ->setRange('0','100','1');
        
        
        
        $cells_section->addStyleControl( 
            array(
                "name" => __('Space between cells'),
                "type" => 'measurebox',
                "default" => "0",
                "units" => 'px',
                "property" => 'margin-right',
                "control_type" => 'slider-measurebox',
                "selector" => $cell_div,
                "condition" => 'editor_mode!=preview&&carousel_type=custom&&maybe_autofit!=auto'
            )
        )
        ->setUnits('px')
        ->setRange('0','100','1');
        
    
        $cells_section->addStyleControl(
            array(
                "name" => __('Cell Transition'),
                "property" => 'transition-duration',
                "selector" => $repeater_div,
                "control_type" => 'slider-measurebox',
                "default" => '400',
                "condition" => 'editor_mode!=preview&&carousel_type=repeater'
            )
        )->setUnits('ms','ms')
         ->setRange(10, 800, 5);
        
        
        $cells_section->addStyleControl(
            array(
                "name" => __('Cell Transition'),
                "property" => 'transition-duration',
                "selector" => $gallery_cell_selector,
                "control_type" => 'slider-measurebox',
                "default" => '400',
                "condition" => 'editor_mode!=preview&&carousel_type!=easy_posts&&carousel_type!=custom&&carousel_type!=woo&&carousel_type!=repeater'
            )
        )->setUnits('ms','ms')
         ->setRange(10, 800, 5);
        
        $cells_section->addStyleControl(
            array(
                "name" => __('Cell Transition'),
                "property" => 'transition-duration',
                "selector" => $product_list_div,
                "control_type" => 'slider-measurebox',
                "default" => '400',
                "condition" => 'editor_mode!=preview&&carousel_type=woo'
            )
        )->setUnits('ms','ms')
         ->setRange(10, 800, 5);
        
        $cells_section->addStyleControl(
            array(
                "name" => __('Cell Transition'),
                "property" => 'transition-duration',
                "selector" => $easy_posts_div,
                "control_type" => 'slider-measurebox',
                "default" => '400',
                "condition" => 'editor_mode!=preview&&carousel_type=easy_posts'
            )
        )->setUnits('ms','ms')
         ->setRange(10, 800, 5);
        
        $cells_section->addStyleControl(
            array(
                "name" => __('Cell Transition'),
                "property" => 'transition-duration',
                "selector" => $cell_div,
                "control_type" => 'slider-measurebox',
                "default" => '400',
                "condition" => 'editor_mode!=preview&&carousel_type=custom'
            )
        )->setUnits('ms','ms')
         ->setRange(10, 800, 5);
        
        
        $cells_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Gallery image horizontal align'),
                'slug' => 'cell_inner_layout',
                "condition" => 'editor_mode!=preview&&carousel_type!=easy_posts&&carousel_type!=custom&&carousel_type!=woo&&carousel_type!=repeater&&prioritise_width!=enable'
            )
            
        )->setValue(array( 
            "a_flexstart" => "Left",
            "b_center" => "Center",
            "c_flexend" => "Right",
            )           
        )->setDefaultValue('center')
         ->setValueCSS( array(
            "a_flexstart"  => ".oxy-carousel-builder_gallery-image {
                                display: flex;
                                align-items: flex-start;
                            }",
            "c_flexend"  => ".oxy-carousel-builder_gallery-image {
                                display: flex;
                                align-items: flex-end;
                            }",
            "b_center"  => ".oxy-carousel-builder_gallery-image {
                                display: flex;
                                align-items: center;
                            }",
            ))->whitelist();
        
        
        $cells_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Gallery image vertical align'),
                'slug' => 'cell_inner_alignment',
                "condition" => 'editor_mode!=preview&&carousel_type!=easy_posts&&carousel_type!=custom&&carousel_type!=woo&&carousel_type!=repeater'
            )
            
        )->setValue(array( 
            "a_flexstart" => "Top",
            "c_flexend" => "Bottom",
            "b_center" => "Center",
            )           
        )->setDefaultValue('center')
         ->setValueCSS( array(
            "a_flexstart"  => ".oxy-carousel-builder_gallery-images,
                            .oxy-carousel-builder_gallery-images .flickity-slider {
                                display: flex;
                                align-items: flex-start;
                            }",
            "c_flexend"  => " .oxy-carousel-builder_gallery-images,
                            .oxy-carousel-builder_gallery-images .flickity-slider {
                                display: flex;
                                align-items: flex-end;
                            }",
            "b_center"  => " .oxy-carousel-builder_gallery-images,
                            .oxy-carousel-builder_gallery-images .flickity-slider {
                                display: flex;
                                align-items: center;
                            }",
        ))->whitelist();
        
        
        $cells_section->addStyleControl(
            array(
                "name" => __('Object fit'),
                "property" => 'object-fit',
                "selector" => '.oxy-carousel-builder_gallery-image img',
                "control_type" => 'dropdown',
                "condition" => 'editor_mode!=preview&&carousel_type!=easy_posts&&carousel_type!=custom&&carousel_type!=woo&&carousel_type!=repeater&&prioritise_width=none'
            )
        )->setValue(array( 
            "fill" => "Fill",
            "contain" => "Contain",
            "none" => "None",
            "cover" => "Cover",
            "scale-down" => "Scale-down",
            )
        )->setDefaultValue('none')
         ->setParam("description", __("Changes how the image fits inside of the cell"));   


         $cells_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Force image to take up 100% width of cell'),
                'slug' => 'force_full_width',
                "condition" => 'editor_mode!=preview&&carousel_type!=easy_posts&&carousel_type!=custom&&carousel_type!=woo&&carousel_type!=repeater'
            )
            
        )->setValue(array( 
            "true" => "True",
            "false" => "False"
            )           
        )->setDefaultValue('false')
         ->setValueCSS( array(
            "true"  => ".oxy-carousel-builder_gallery-images .oxy-carousel-builder_gallery-image img {
                            width: 100%;
                        }",
        ))->whitelist();

       
        
        /**
         * Selected Styles
         */ 
        $cell_selected_section = $cells_section->addControlSection("cell_selected_section", __("Selected Cells"), "assets/icon.png", $this);
        
        $cell_selected_section->addStyleControls(
            array(
                array(
                    "name" => __('Selected Opacity'),
                    "property" => 'opacity',
                    "selector" => $cell_selected,
                ),
                array(
                    "name" => __('Selected Background Color'),
                    "property" => 'background-color',
                    "selector" => $cell_selected,
                ),
                array(
                    "name" => __('Selected Text Color'),
                    "property" => 'color',
                    "selector" => $cell_selected,
                ),
            )
        );
        
        
         $cell_selected_section->addStyleControl(
            array(
                "name" => __('Selected Scale'),
                "selector" => $cell_selected,
                "property" => '--cell-selected-scale',
                "control_type" => 'slider-measurebox',
                "default" => '1',
                )
            )
            ->setRange('0','2', '.02');
        
        
        $cell_selected_section->addStyleControl(
            array(
                "name" => __('Selected Rotate'),
                "selector" => $cell_selected,
                "property" => '--cell-selected-rotate',
                "control_type" => 'slider-measurebox',
                "default" => '0',
                )
            )
            ->setUnits('deg','deg')
            ->setRange('-180','180');
        
        /**
         * Selected Styles
         */ 
        $cell_prev_next_section = $cells_section->addControlSection("cell_prev_next_section", __("Prev/Next Cells"), "assets/icon.png", $this);
        
        $cell_prev_next_section->addCustomControl(
            '<div style="color: #fff; line-height: 1.3; font-size: 13px;">These changes will only be seen inside Oxygen in preview mode</div>','description');
        
        
        $cell_prev_next_section->addStyleControl(
            array(
                "name" => __('Previous Scale'),
                "selector" => $cell_previous,
                "property" => '--cell-prev-scale',
                "control_type" => 'slider-measurebox',
                "default" => '1',
                )
            )
            ->setRange('0','2', '.02');
        
        $cell_prev_next_section->addStyleControl(
            array(
                "name" => __('Previous Rotate'),
                "selector" => $cell_previous,
                "property" => '--cell-prev-rotate',
                "control_type" => 'slider-measurebox',
                "default" => '0',
                )
            )
            ->setUnits('deg','deg')
            ->setRange('-180','180');
        
        $cell_prev_next_section->addStyleControls(
            array(
                array(
                    "name" => __('Previous Opacity'),
                    "property" => 'opacity',
                    "selector" => $cell_previous,
                ),
            )
        );
        
        $cell_prev_next_section->addStyleControl(
            array(
                "name" => __('Next Scale'),
                "selector" => $cell_next,
                "property" => '--cell-next-scale',
                "control_type" => 'slider-measurebox',
                "default" => '1',
                )
            )
            ->setRange('0','2', '.02');
        
        
        
        $cell_prev_next_section->addStyleControl(
            array(
                "name" => __('Next Rotate'),
                "selector" => $cell_next,
                "property" => '--cell-next-rotate',
                "control_type" => 'slider-measurebox',
                "default" => '0',
                )
            )
            ->setUnits('deg','deg')
            ->setRange('-180','180');
        
        
        
        $cell_prev_next_section->addStyleControls(
            array(
                array(
                    "name" => __('Next Opacity'),
                    "property" => 'opacity',
                    "selector" => $cell_next,
                ),
            )
        );
        
        
        
        
        
        
            
            
        
        /**
         * Config
         */ 
        $config_section = $this->addControlSection("config_section", __("Carousel"), "assets/icon.png", $this);
        
        
        $config_section->addCustomControl(
            '<div style="color: #eee; line-height: 1.3; font-size: 13px;">Grouping & Alignments<hr></div>','description');
        
        $config_section->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Group cells by..'),
                'slug' => 'maybe_group'
            )
            
        )->setValue(array( 
            "true" => __("Auto (Number of cells visible)"), 
            "number" => __("Choose a number"),
            "percent" => __("% of carousel viewport"), 
            "false" => __("No grouping"),
            )           
        )->setDefaultValue('number')
         ->setParam("description", __("Choose how to groups the cells together in slides"));    
        
        $config_section->addOptionControl(
            array(
                "type" => 'slider-measurebox',
                "name" => __('Number of cells grouped together'),
                "slug" => 'group_cells',
                "default" => '1',
                "condition" => 'maybe_group=number'
            )
        )->setRange('1','10','1');
        
        $config_section->addOptionControl(
            array(
                "type" => 'slider-measurebox',
                "name" => __('Width of carousel viewport to group'),
                "slug" => 'group_percent',
                "default" => '100',
                "condition" => 'maybe_group=percent'
            )
        )->setRange('1','100','1')
         ->setUnits('%','%');
        
        
        
        $config_section->addOptionControl(
            array(
                "type" => 'slider-measurebox',
                "name" => __('Initial cell selected'),
                "slug" => 'initial_index',
                "default" => '1',
                "condition" => 'maybe_hash=false'
            )
        )->setRange('1','10','1');
        
        $config_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Cell alignment',
                'slug' => 'cell_align'
            )
            
        )->setValue(array( 
            "left" => "Left", 
            "center" => "Center",
            "right" => "Right" 
            )           
        )->setDefaultValue('center')
         ->setParam("description", __("(Horizontal, relative to the carousel viewport)"));    
        
        
        $config_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Contain'),
                'slug' => 'contain',
                'condition' => 'wrap_around=false&&maybe_fade!=true'
            )
            
        )->setValue(array( 
            "true" => "Enable", 
            "false" => "Disable" 
            )
        )->setParam("description", __("Prevents excess scroll at beginning or end"));
        
        
        
        
        
        $config_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Adaptive height'),
                'slug' => 'adaptive_height'
            )
            
        )->setDefaultValue('false')
        ->setValue(array( 
             "true" => "Enable", 
            "false" => "Disable"
            )
        )->setParam("description", __("Dynamically change carousel height to match each selected cell"));
        
        
        $config_section->addStyleControl(
            array(
                "name" => __('Height transition duration'),
                "property" => 'transition-duration',
                "selector" => '.flickity-viewport',
                "control_type" => 'slider-measurebox',
                "condition" => 'adaptive_height=true',
                "default" => '0',
            )
        )->setUnits('ms','ms')
         ->setRange(10, 800, 5);
        
        
        
        
        
        
        
        /**
         * Config / Behaviour
         */ 
        $config_behaviour_section = $this->addControlSection("config_behaviour_section", __("Behaviour / Interaction"), "assets/icon.png", $this);
        
        
        $config_behaviour_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Transition type'),
                'slug' => 'maybe_fade'
            )
            
        )->setDefaultValue('false')
        ->setValue(array( 
             "false" => "Slide", 
             "true" => "Fade"
            )
        ); 
        
        
        $config_behaviour_section->addOptionControl(
            array(
                "type" => 'slider-measurebox',
                "name" => __('Autoplay'),
                "slug" => 'autoplay',
                "value" => '0',
            )
        )->setRange('0','4000','10')
        ->setUnits('ms','ms')
        ->setParam("description", __("Advance cells every {x} milliseconds (0=disabled)"));
        
        $config_behaviour_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Resume Autoplay after user interaction'),
                'slug' => 'maybe_resume_autoplay'
            )
            
        )->setDefaultValue('false')
        ->setValue(array( 
             "true" => "Enable", 
            "false" => "Disable"
            )
        );  
        
        
        $config_behaviour_section->addOptionControl(
            array(
                "type" => 'slider-measurebox',
                "name" => __('Resume autoplay after..'),
                "slug" => 'resume_autoplay',
                "value" => '300',
                "condition" => 'maybe_resume_autoplay=true'
            )
        )->setRange('0','4000','1')
        ->setUnits('ms','ms');

        
        $config_behaviour_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Wrap around (infinite cells)'),
                'slug' => 'wrap_around'
            )
            
        )->setDefaultValue('false')
        ->setValue(array( 
             "true" => "Enable", 
            "false" => "Disable"
            )
        );  
        
        $config_behaviour_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Free scrolling'),
                'slug' => 'free_scroll',
                'condition' => 'maybe_fade=false'
            )
            
        )
        ->setDefaultValue('false')    
        ->setValue(array( 
            "true" => "Enable", 
            "false" => "Disable" 
            )
        )->setParam("description", __("Prevent the cells from snapping into place"));
        
        
        $config_behaviour_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Retrigger AOS animations on selected cell'),
                'slug' => 'trigger_animations'
            )
            
        )->setValue(array( 
            "true" => "Enable", 
            "false" => "Disable" 
            )           
        )->setDefaultValue('false')
         ->setParam("description", __("Scroll animations on inner elements will retrigger when the cell is selected"));
        
        
        $config_behaviour_section->addOptionControl(
            array(
                "type" => 'slider-measurebox',
                "name" => __('Delay before triggering'),
                "slug" => 'trigger_animations_delay',
                "default" => '700',
                "condition" => 'trigger_animations=true'
            )
        )->setRange('0','1400','1')
         ->setUnits('ms');
        
        
        
        $config_behaviour_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Ticker (continuous play)'),
                'slug' => 'maybe_ticker',
                'condition' => 'wrap_around=true&&maybe_fade=false',
            )
            
        )->setDefaultValue('false')
        ->setValue(array( 
             "true" => "Enable", 
            "false" => "Disable"
            )
        );  
        
        $config_behaviour_section->addOptionControl(
            array(
                "type" => 'slider-measurebox',
                "name" => __('Ticker Speed (negative for right-to-left)'),
                "slug" => 'ticker',
                "value" => '0',
                "condition" => 'maybe_ticker=true&&wrap_around=true&&maybe_fade=false'
            )
        )->setRange('-10','10','0.1');
        
        
        $config_behaviour_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Ticker paused on hover'),
                'slug' => 'ticker_hover_pause',
                'condition' => 'maybe_ticker=true&&wrap_around=true&&maybe_fade=false'
            )
            
        )->setDefaultValue('true')
         ->setValue(array( 
            "true" => "Enable", 
            "false" => "Disable" 
            )
        );
        
        
        $config_behaviour_section->addCustomControl(
            '<div style="color: #eee; line-height: 1.3; font-size: 13px;">User interactions<hr style="opacity: 0.5;"></div>','description');
        
        
        $config_behaviour_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Carousel can be dragged'),
                'slug' => 'draggable',
            )
            
        )->setDefaultValue('true')
            ->setValue(array( 
             "true" => "Enable", 
             "false" => "Disable"
            )
        );   
        
        
        $config_behaviour_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Clicking a cell will select it'),
                'slug' => 'click_to_select',
                'condition' => 'maybe_fade=false'
            )
            
        )->setDefaultValue('false')
        ->setValue(array( 
             "true" => "Enable", 
            "false" => "Disable"
            )
        )->setValueCSS( array(
            "true"  => ".flickity-viewport .oxy-carousel-builder_gallery-image,
                        .flickity-viewport .oxy-dynamic-list > .ct-div-block,
                        .flickity-viewport .product,
                        .flickity-viewport .oxy-post {
                            cursor: pointer;
                        }
                        ",
        ));  
        
        
        $config_behaviour_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Pause autoplay on hover'),
                'slug' => 'maybe_pause_autoplay',
                
            )
        )->setDefaultValue('true')
            ->setValue(array( 
             "true" => "Enable", 
             "false" => "Disable"
            )
        );   

        $config_behaviour_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Hash link to select cells'),
                'slug' => 'maybe_hash',
                
            )
        )->setDefaultValue('false')
            ->setValue(array( 
             "true" => "Enable", 
             "false" => "Disable"
            )
        );   
        
            
        /**
         * Friction / Drag
         */ 
        $friction_section = $config_behaviour_section->addControlSection("friction_section", __("Friction / Drag"), "assets/icon.png", $this);
        
       
        $friction_section->addOptionControl(
            array(
                "type" => 'slider-measurebox',
                "name" => __('Drag Threshold'),
                "slug" => 'drag_threshold',
                "value" => '3',
            )
        )->setRange('0','40','1')
         ->setParam("description", __("Number of px moved until dragging starts"));    
        
        $friction_section->addOptionControl(
            array(
                "type" => 'slider-measurebox',
                "name" => __('Selected Attraction'),
                "slug" => 'selected_attraction',
                "value" => '0.025',
            )
        )->setRange('0','0.03','.001')
         ->setParam("description", __("Higher attraction makes the slider move faster"));    
        
        
        
        $friction_section->addOptionControl(
            array(
                "type" => 'slider-measurebox',
                "name" => __('Friction'),
                "slug" => 'friction',
                "value" => '0.28',
            )
        )->setRange('0','1','.02') 
         ->setParam("description", __("Higher friction makes the slider feel stickier and less bouncy")); 
        
        
        $friction_section->addOptionControl(
            array(
                "type" => 'slider-measurebox',
                "name" => __('Free Scroll Friction'),
                "slug" => 'free_scroll_friction',
                "value" => '0.075',
                "condition" => 'free_scroll=true'
            )
        )->setRange('0','1','.05') 
         ->setParam("description", __("Higher friction makes the slider feel stickier")); 
        
        
        
        
        
        
        $config_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Enable carousel at this break point (& below)',
                'slug' => 'watch_css'
            )
            
        )->setValue(array( 
            "true" => "Enable", 
            "false" => "Disable"
            )
        )
        ->setDefaultValue('true')
        ->setValueCSS( array(
            "false"  => " .oxy-dynamic-list::after {
                                content: none;
                            }
                            
                            ul.products::after {
                                content: none;
                            }
                            
                            .oxy-posts::after {
                                content: none;
                            }
                            
                            .oxy-posts {
                                flex-wrap: wrap;
                            }
                            
                            .oxy-inner-content::after {
                                content: none;
                            }

                            .oxy-carousel-builder_gallery-images::after {
                                content: none;
                            }
                            
                            ul.products {
                                flex-wrap: wrap;
                            }
                            
                            .flickity-page-dots,
                            .oxy-carousel-builder_icon {
                                display: none;
                            }",
            
            "true"  => " .oxy-dynamic-list::after {
                                content: 'flickity';
                            }
                            
                            ul.products::after {
                                content: 'flickity';
                            }
                            
                            .oxy-posts::after {
                                content: 'flickity';
                            }
                            
                            .oxy-posts {
                                flex-wrap: nowrap;
                            }
                            
                            .oxy-inner-content::after {
                                content: 'flickity';
                            }

                            .oxy-carousel-builder_gallery-images::after {
                                content: 'flickity';
                            }
                            
                            ul.products {
                                flex-wrap: nowrap;
                            }
                            
                            .flickity-page-dots,
                            .oxy-carousel-builder_icon{
                                display: inline-flex;
                            }",
        ) )->whiteList();
        
        
        $config_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Fullscreen option'),
                'slug' => 'maybe_fullscreen'
            )
            
        )
        ->setDefaultValue('false')    
        ->setValue(array( 
            "true" => "Enable", 
            "false" => "Disable" 
            )
        );
        
        $config_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Keep image aspect ratio in fullscreen mode'),
                'slug' => 'maybe_gallery_fullscreen',
                'condition' => 'maybe_fullscreen!=false&&carousel_type!=repeater&&carousel_type!=easy_posts&&carousel_type!=repeater&&carousel_type!=custom'
            )
            
        )
        ->setDefaultValue('false')    
        ->setValue(array( 
            "true" => "Enable", 
            "false" => "Disable" 
            )
        )->setValueCSS( array(
            "true"  => " .flickity-enabled.is-fullscreen .oxy-carousel-builder_gallery-image {
                              height: 100%;
                              width: 100%;
                              display: flex;
                              justify-content: center;
                              align-items: center;
                          }
                            
                         .flickity-enabled.is-fullscreen .oxy-carousel-builder_gallery-image img {
                              object-fit: contain;
                              width: 100%
                            }",
        ) );
        
        
        $config_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Force equal cell heights'),
                'slug' => 'maybe_force_heights'
            )
            
        )
        ->setDefaultValue('false')    
        ->setValue(array( 
            "true" => "Enable", 
            "false" => "Disable" 
            )
        )->setValueCSS( array(
            "true"  => " .flickity-resize .flickity-slider > .ct-div-block {
                                min-height: 100%;
                            }
                            
                            .flickity-resize .flickity-slider .product {
                                min-height: 100%;
                            }
                            
                            .flickity-resize .flickity-slider .cell {
                                min-height: 100%;
                            }
                            
                            .flickity-resize .flickity-slider .oxy-post {
                                min-height: 100%;
                            }
                            
                            .flickity-resize .flickity-slider .oxy-inner-content {
                                min-height: 100%;
                            }
                            ",
        ) )->whiteList()
           ->setParam("description", __("Will make each cell the same height as the tallest cell"));
        
        
        
        
       
      
        
        
        /**
         * Navigation
         */ 
        $navigation_section = $this->addControlSection("navigation_section", __("Navigation Arrows"), "assets/icon.png", $this);
        
        $navigation_section->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Navigation Arrows'),
                'slug' => 'nav_type'
            )
            
        )->setValue(array( 
            "icon" => "Built-in icons", 
            "custom" => "Using custom elements" ,
            "none" => 'None',
         )
        )
         ->setDefaultValue('icon')
         ->setValueCSS( array(
            "custom"  => " .oxy-carousel-builder_icon {
                            display: none;
                        }
                        ",
             "none"  => " .oxy-carousel-builder_icon {
                            display: none;
                        }
                        "
        ) );
        
        
        /* $navigation_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Navigation Visibility'),
                'slug' => 'nav_visibility'
            )
            
        )->setValue(array( "hover" => "On Hover", "always" => "Always Visible" ))
         ->setDefaultValue('always')
         ->setValueCSS( array(
            "hover"  => 
                " .oxy-carousel-builder_icon {
                    opacity: 0;
                    visibility: hidden;
                    }

                    .oxy-inner-content:hover .oxy-carousel-builder_icon {
                        opacity: 1;
                        visibility: visible;
                    }
                "
        ) ); */
        
        
        
        $navigation_section->addCustomControl(
            '<div style="color: #fff; font-size: 13px;">All style controls below are for the built-in icons<hr style="opacity: 0.5;"></div>','description');
        
        /**
         * Icons
         */ 
        $prev_icon_section = $navigation_section->addControlSection("prev_icon_section", __("Previous Icon"), "assets/icon.png", $this);
        
        $navigation_icon_selector = '.oxy-carousel-builder_icon';
        
        $navigation_section->addStyleControl(
                array(
                    "name" => __('Icon Size'),
                    "slug" => "icon_size",
                    "selector" => $navigation_icon_selector,
                    "control_type" => 'slider-measurebox',
                    "value" => '14',
                    "property" => 'font-size',
                    "condition" => 'nav_type=icon',
                )
        )->setRange(1, 72, 1);
        
        $prev_icon_section->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Prev Icon'),
                "slug" => 'prev_icon',
                "value" => 'FontAwesomeicon-chevron-left', 
                "condition" => 'nav_type=icon',
            )
        );
        
        
        $next_icon_section = $navigation_section->addControlSection("next_icon_section", __("Next Icon"), "assets/icon.png", $this);
        
        $next_icon_section->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Next Icon'),
                "slug" => 'next_icon',
                "value" => 'FontAwesomeicon-chevron-right', 
                "condition" => 'nav_type=icon',
            )
        );
        
        
        $navigation_spacing_section = $navigation_section->addControlSection("navigation_spacing_section", __("Position / Spacing"), "assets/icon.png", $this);
        
        $navigation_spacing_section->addPreset(
            "padding",
            "nav_icon_padding",
            __("Padding"),
            $navigation_icon_selector
        )->whiteList();
        
        $prev_selector = '.oxy-carousel-builder_prev';
        $next_selector = '.oxy-carousel-builder_next';
        
        
        $navigation_spacing_section->addCustomControl(
            '<div style="color: #fff; font-size: 13px;">Previous Navigation</div>','description');
        $navigation_spacing_section->addStyleControl(
                array(
                    "selector" => $prev_selector,
                    "control_type" => 'measurebox',
                    "default" => '50',
                    "property" => 'top',
                )
        )->setUnits('%', 'px,%,em,auto,vw,vh')
         ->setParam('hide_wrapper_end', true);
        
        $navigation_spacing_section->addStyleControl(
                array(
                    "selector" => $prev_selector,
                    "control_type" => 'measurebox',
                    "default" => '0',
                    "property" => 'bottom',
                )
        )->setParam('hide_wrapper_start', true);
        
        $navigation_spacing_section->addStyleControl(
                array(
                   "selector" => $prev_selector,
                    "control_type" => 'measurebox',
                    "default" => '0',
                    "property" => 'left',
                )
        )->setParam('hide_wrapper_end', true);
        
        $navigation_spacing_section->addStyleControl(
                array(
                    "selector" => $prev_selector,
                    "control_type" => 'measurebox',
                    "default" => '0',
                    "property" => 'right',
                )
        )->setParam('hide_wrapper_start', true);
        
        
        $navigation_spacing_section->addCustomControl(
            '<div style="color: #fff; font-size: 13px;">Next Navigation</div>','description');
        
        $navigation_spacing_section->addStyleControl(
                array(
                    "selector" => $next_selector,
                    "control_type" => 'measurebox',
                    "default" => '50',
                    "property" => 'top',
                )
        )->setUnits('%', 'px,%,em,auto,vw,vh')
         ->setParam('hide_wrapper_end', true);
        
        $navigation_spacing_section->addStyleControl(
                array(
                    "selector" => $next_selector,
                    "control_type" => 'measurebox',
                    "default" => '0',
                    "property" => 'bottom',
                )
        )->setParam('hide_wrapper_start', true);
        
        $navigation_spacing_section->addStyleControl(
                array(
                    "selector" => $next_selector,
                    "control_type" => 'measurebox',
                    "default" => '0',
                    "property" => 'left',
                )
        )->setParam('hide_wrapper_end', true);
        
        $navigation_spacing_section->addStyleControl(
                array(
                    "selector" => $next_selector,
                    "control_type" => 'measurebox',
                    "default" => '0',
                    "property" => 'right',
                )
        )->setParam('hide_wrapper_start', true);
        
        
       
         /**
         * Disabled
         */ 
        $nav_disabled_section = $navigation_section->addControlSection("nav_disabled_section", __("Disabled"), "assets/icon.png", $this);
        
        $navigation_icon_disabled_selector = '.oxy-carousel-builder_icon_disabled';
        
        $nav_disabled_section->addStyleControls(
            array(
                array(
                    "name" => __('Disabled Background Color'),
                    "property" => 'background-color',
                    "selector" => $navigation_icon_disabled_selector,
                    "condition" => 'wrap_around=false'
                ),
                array(
                    "name" => __('Disabled Color'),
                    "property" => 'color',
                    "selector" => $navigation_icon_disabled_selector,
                    "condition" => 'wrap_around=false'
                ),
                array(
                    "name" => __('Disabled Border Color'),
                    "property" => 'border-color',
                    "selector" => $navigation_icon_disabled_selector,
                    "condition" => 'wrap_around=false'
                ),
                array(
                    "name" => __('Disabled Opacity'),
                    "property" => 'opacity',
                    "selector" => $navigation_icon_disabled_selector,
                    "condition" => 'wrap_around=false'
                )
            )
        );
        
        
        $navigation_colors_section = $navigation_section->addControlSection("navigation_colors_section", __("Colors"), "assets/icon.png", $this);
        
        
        $navigation_colors_section->addStyleControls(
            array(
                array(
                    "name" => __('Background Color'),
                    "property" => 'background-color',
                    "default" => '#222',
                    "selector" => $navigation_icon_selector,
                ),
                array(
                    "name" => __('Hover Background Color'),
                    "property" => 'background-color',
                    "selector" => $navigation_icon_selector.":hover",
                ),
                array(
                    "name" => __('Icon Color'),
                    "property" => 'color',
                    "default" => '#fff',
                    "selector" => $navigation_icon_selector,
                ),
                array(
                    "name" => __('Hover Icon Color'),
                    "property" => 'color',
                    "selector" => $navigation_icon_selector.":hover",
                )
            )
        );
        
        $navigation_colors_section->addStyleControl(
            array(
                "name" => __('Hover Transition Duration'),
                "property" => 'transition-duration',
                "selector" => $navigation_icon_selector,
                "control_type" => 'slider-measurebox',
                "default" => '400',
            )
        )->setUnits('ms','ms')
         ->setRange(10, 800, 5);
        
        $navigation_section->borderSection('Borders', $navigation_icon_selector,$this);
        $navigation_section->boxShadowSection('Shadows', $navigation_icon_selector,$this);
        

        
        $navigation_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Previous Selector'),
                "slug" => 'previous_selector',
                "default" => '.prev-btn',
                "condition" => 'nav_type=custom'
            )
        );
        
        $navigation_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Next Selector'),
                "slug" => 'next_selector',
                "default" => '.next-btn',
                 "condition" => 'nav_type=custom'
            )
        );
        
        
        
        
        /**
         * Page Dots
         */ 
        $dots_section = $this->addControlSection("dots_section", __("Page Dots"), "assets/icon.png", $this);
        
        
        $dots_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Dots Display',
                'slug' => 'page_dots',
                'default' => 'true'
            )
            
        )->setValue(array( 
            "true" => "Enable", 
            "false" => "Disable"
            )
        )->setValueCSS( array(
            "false"  => " .flickity-page-dots {
                                display: none;
                            }",
        ) );
        
        
        $dots_section->addOptionControl(
            array(
                "name" => __('Hide Dots Below'),
                "slug" => 'hide_dots_below',
                "type" => 'medialist',
                "default" => 'never',
                "condition" => 'page_dots=true'
            )
        );
        
        /**
         * Dot Styles
         */ 
        $dots_styles_section = $dots_section->addControlSection("dots_styles_sectionf", __("Dot Styles"), "assets/icon.png", $this);
        
        $dots_styles_section->addStyleControls(
             array( 
                 array( 
                    "selector" => $dot_selector,
                    "property" => 'background-color',
                      "condition" => 'page_dots=true'
                ),
                array(
                    "selector" => $dot_selector,
                    "property" => 'opacity',
                    "default" => '0.25',
                     "condition" => 'page_dots=true'
                ),
                array(
                    "default" => "20",
                    "selector" => $dot_selector,
                    "property" => 'border-radius',
                      "condition" => 'page_dots=true'
                ), 
            )
        );
        
        $dots_styles_section->addStyleControl( 
            array(
                "default" => "10",
                "property" => 'height',
                "control_type" => 'slider-measurebox',
                "selector" => $dot_selector,
                "condition" => 'page_dots=true'
            )
        )
        ->setUnits('px','px')
        ->setRange('1','20','1');
        
        $dots_styles_section->addStyleControl( 
            array(
                "default" => "10",
                "property" => 'width',
                "control_type" => 'slider-measurebox',
                "selector" => $dot_selector,
                "condition" => 'page_dots=true'
            )
        )
        ->setUnits('px','px')
        ->setRange('1','30','1');
        
       
        
        $dots_styles_section->addStyleControl( 
            array(
                "name" => 'Margin Between',
                "default" => "8",
                "property" => 'margin-left|margin-right',
                "control_type" => 'slider-measurebox',
                "selector" => $dot_selector,
                "condition" => 'page_dots=true'
            )
        )
        ->setUnits('px','px')
        ->setRange('0','10','1');
        
        
        
        
        /**
         * Selected Page Dots
         */ 
        $dots_selected_section = $dots_section->addControlSection("dots_selected_section", __("Selected Dot"), "assets/icon.png", $this);
        
        
        $dots_selected_section->addStyleControls(
             array( 
                  array(
                    "name" => 'Selected background color',  
                    "selector" => $dot_selected_selector,
                    "property" => 'background-color',
                      "condition" => 'page_dots=true'
                ),
                array(
                    "name" => 'Selected opacity',
                    "selector" => $dot_selected_selector,
                    "property" => 'opacity',
                    "default" => '1',
                     "condition" => 'page_dots=true'
                ),
                 
            )
        );
        
        
        $dots_selected_section->addStyleControl(
            array(
                "name" => __('Scale'),
                "selector" => $dot_selected_selector,
                "property" => '--selected-dot-scale',
                "control_type" => 'slider-measurebox',
                "default" => '1',
                )
            )
            ->setRange('0','2', '.02');
        
        $dots_selected_section->addStyleControl(
            array(
                "name" => __('Transition duration'),
                "property" => 'transition-duration',
                "selector" => $dot_selector,
                "control_type" => 'slider-measurebox',
                "default" => '0',
            )
        )->setUnits('ms','ms')
         ->setRange(10, 800, 5);
        
        
        
        $dots_position_section = $dots_section->addControlSection("dot_position_section", __("Positioning"), "assets/icon.png", $this);
        
        $dots_position_section->addStyleControl(
            array(
                "name" => __('Position'),
                "selector" => '.oxy-carousel-builder_inner .flickity-page-dots',
                "property" => 'position',
                "control_type" => 'buttons-list',
            )
        )->setValue(array( 
            "relative" => "Relative",
            "absolute" => "Absolute",
            )
        )->setDefaultValue('relative');
        
        
        
        $dots_position_section->addStyleControl(
                array(
                    "selector" => $dots_selector,
                    "control_type" => 'measurebox',
                    "value" => '0',
                    "property" => 'top',
                    "condition" => 'page_dots=true'
                )
        )->setParam('hide_wrapper_end', true);
        
        $dots_position_section->addStyleControl(
                array(
                    "selector" => $dots_selector,
                    "control_type" => 'measurebox',
                    "value" => '-25',
                    "property" => 'bottom',
                    "condition" => 'page_dots=true'
                )
        )->setUnits('px', 'px,%,em,auto,vw,vh')
         ->setParam('hide_wrapper_start', true);
        
        $dots_position_section->addStyleControl(
                array(
                   "selector" => $dots_selector,
                    "control_type" => 'measurebox',
                    "value" => '0',
                    "property" => 'left',
                    "condition" => 'page_dots=true'
                )
        )->setParam('hide_wrapper_end', true);
        
        $dots_position_section->addStyleControl(
                array(
                    "selector" => $dots_selector,
                    "control_type" => 'measurebox',
                    "value" => '0',
                    "property" => 'right',
                    "condition" => 'page_dots=true'
                )
        )->setParam('hide_wrapper_start', true);
        
        
        
         
        
        
        /**
         * Captions
         */ 
        $caption_section = $this->addControlSection("caption_section", __("Gallery Captions"), "assets/icon.png", $this);
        $caption_selector = '.oxy-carousel-builder_caption';
        
        $caption_section->addCustomControl(
            '<div style="color: #eee; line-height: 1.3; font-size: 13px;">Captions are available for galleries (if ACF use "Image Array" as return value)</div>','description');
        
        
        $caption_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __("Image captions"),
                'slug' => 'maybe_captions',
                "condition" => 'carousel_type=acf_gallery||carousel_type=medialibrary||carousel_type=metabox_image'
            )
            
        )->setValue(array( 
            "true" => "Enable", 
            "false" => "Disable",
            )           
        )->setDefaultValue('false')
         ->setValueCSS( array(
            "true"  => " .oxy-carousel-builder_caption {
                              display: flex;
                         }",
             "false"  => " .oxy-carousel-builder_caption {
                              display: none;
                         }",
                
        ) );
        
        
        $caption_section->addPreset(
            "padding",
            "caption_padding",
            __("Padding"),
            $caption_selector
        )->whiteList();
        
        
        $caption_section->addStyleControl(
            array(
                "name" => __('Align'),
                "selector" => $caption_selector,
                "property" => 'justify-content',
                "control_type" => 'buttons-list',
            )
        )->setValue(array( 
            "flex-start" => "Left",
            "center" => "Center",
            "flex-end" => "Right",
            )
        )->setDefaultValue('center');
        
        $caption_section->addStyleControl( 
            array(
                "name" => __('Color'),
                "default" => '#fff',
                "property" => 'color',
                "selector" => $caption_selector,
            )
        )->setParam('hide_wrapper_end', true);
        
        $caption_section->addStyleControl( 
            array(
                "name" => __('Hover Color'),
                "property" => 'color',
                "selector" => '.oxy-carousel-builder_gallery-image:hover ' .$caption_selector,
            )
        )->setParam('hide_wrapper_start', true);
        
        
        $caption_section->addStyleControl( 
            array(
                "name" => __('Background'),
                "default" => 'rgba(50,50,50,0.2)',
                "property" => 'background-color',
                "selector" => $caption_selector,
            )
        )->setParam('hide_wrapper_end', true);
        
        $caption_section->addStyleControl( 
            array(
                "name" => __('Hover background'),
                "property" => 'background-color',
                "selector" => '.oxy-carousel-builder_gallery-image:hover ' .$caption_selector,
            )
        )->setParam('hide_wrapper_start', true);
        
        $caption_section->typographySection('Typography', $caption_selector, $this);
        
       
         /**
         * Loading
         */ 
        $loading_section = $this->addControlSection("loading_section", __("Loading / Performance"), "assets/icon.png", $this);
        
        
        $loading_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Support lazy loading'),
                'slug' => 'maybe_lazy',
                "condition" => 'carousel_type=repeater||carousel_type=easy_posts||carousel_type=custom||carousel_type=acf_gallery||carousel_type=medialibrary||carousel_type=metabox_image'
            )
            
        )->setValue(array( "true" => "Enabled", "false" => "Disabled" ))
         ->setDefaultValue('false')
         ->setValueCSS( array(
            "true"  => " [data-flickity-lazyload] {
                            opacity: 0;
                        }

                        .flickity-lazyloaded,
                        .flickity-lazyerror {
                          opacity: 1;
                        }
                    "
        ) )->setParam("description", __("Add the 'data-flickity-lazyload' attribute with the image URL on the images"));  
        
        
        $loading_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Support background lazy loading'),
                'slug' => 'maybe_bg_lazy',
                "condition" => 'carousel_type=repeater||carousel_type=easy_posts||carousel_type=custom'
            )
            
        )->setValue(array( "true" => "Enabled", "false" => "Disabled" ))
         ->setDefaultValue('false')
         ->setParam("description", __("Add the 'data-flickity-bg-lazyload' attribute with the image URL on elmenents with the background images"));
        
        
        $loading_section->addOptionControl(
            array(
                "type" => 'slider-measurebox',
                "name" => __('Lazy loading after how many cells?'),
                "slug" => 'lazy_cells',
                "default" => '2',
                "condition" => 'maybe_lazy=true||maybe_bg_lazy=true'
            )
        )->setRange('1','10','1');


        $loading_section->addStyleControl(
            array(
                "name" => __('Fade duration for lazy images'),
                "property" => 'transition-duration',
                "selector" => '[data-flickity-lazyload]',
                "control_type" => 'slider-measurebox',
                "default" => '400',
                "condition" => 'maybe_lazy=true'
            )
        )->setUnits('ms','ms')
         ->setRange(10, 800, 5);
        
        
       $loading_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Browser performance (will-change)'),
                'slug' => 'maybe_will_change',
            )
            
        )->setValue(array( "true" => "Enabled", "false" => "Disabled" ))
         ->setDefaultValue('false')
         ->setValueCSS( array(
            "true"  => " .oxy-inner-content [data-speed] {
                            transition: transform 0s;
                            -webkit-transition: transform 0s;
                            will-change: transform;
                        }

                        .flickity-slider {
                            will-change: transform;
                        }
                    "
        ) )
        ->setParam("description", __("Enabling will generally result in a smoother transition"));


        $loading_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('SRCSET images?'),
                'slug' => 'maybe_srcset',
                "condition" => 'carousel_type=medialibrary||carousel_type=metabox_image'
            )
        )->setValue(array( "true" => "Enabled", "false" => "Disabled" ))
         ->setDefaultValue('true');
        
        
        $loading_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Fade in carousel only after initialised'),
                'slug' => 'maybe_fouc',
            )
            
        )->setValue(array( "true" => "Enabled", "false" => "Disabled" ))
         ->setDefaultValue('false')
         ->setValueCSS( array(
            "true"  => " .oxy-carousel-builder_hidden .oxy-carousel-builder_gallery-images {
                            display: none;
                        }

                        .oxy-carousel-builder_hidden .oxy-dynamic-list {
                            display: none;
                        }
                        
                        .oxy-carousel-builder_hidden .oxy-posts {
                            display: none;
                        }
                        .oxy-carousel-builder_hidden ul.products {
                            display: none;
                        }
                        ",
        ) )    
         ->setParam("description", __("If using carousel above the fold, will prevent FOUC"));
        
        
        
        $loading_section->addStyleControl(
            array(
                "name" => __('Fade duration'),
                "property" => '--fade-duration',
                "selector" => '.oxy-carousel-builder_fadein',
                "control_type" => 'slider-measurebox',
                "condition" => 'maybe_fouc=true'
            )
        )->setUnits('ms','ms')
         ->setRange(10, 1000, 1);

         $loading_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Overflow'),
                'slug' => 'viewport_overflow'
            )
            
        )->setValue(array( 
            "visible" => "Visible", 
            "hidden" => "Hidden" 
            )
        )
         ->setDefaultValue('hidden')
         ->setValueCSS( array(
            "visible"  => " .flickity-viewport {
                                overflow: unset;
                            }",
             "hidden"  => " .oxy_dynamic_list:not(.flickity-enabled) {
                                overflow-x: hidden;
                            }
                            
                            .oxy_dynamic_list.flickity-enabled .flickity-viewport {
                                overflow-x: hidden;
                            }
                            
                            ul.products:not(.flickity-enabled) {
                                overflow-x: hidden;
                            }
                            
                            ul.products.flickity-enabled .flickity-viewport {
                                overflow-x: hidden;
                            }
                            
                            ul.products:not(.flickity-enabled) {
                                overflow-x: hidden;
                            }
                            
                            .oxy-carousel-builder_gallery-images:not(.flickity-enabled) {
                                overflow-x: hidden;
                            }
                            
                            ul.products.flickity-enabled .flickity-viewport {
                                overflow-x: hidden;
                            }
                            
                            .oxy-posts:not(.flickity-enabled) {
                                overflow-x: hidden;
                            }
                            
                            .oxy-posts.flickity-enabled .flickity-viewport {
                                overflow-x: hidden;
                            }",
                
        ) )->setParam("description", __("Set to hidden to avoid overflow outside of the carousel. (Note if using arrows outside of the carousel, add overflow hidden to parent section instead"));


        /*
         $loading_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Optimise for low CLS'),
                'slug' => 'maybe_cls'
            )
            
        )->setValue(array( 
            "enable" => "Enable", 
            "disable" => "Disable" 
            )
        )
         ->setDefaultValue('disable')
         ->setValueCSS( array(
            "disable"  => " ",
             "enable"  => " .oxy-dynamic-list:not(.flickity-enabled) {
                                overflow-x: hidden;
                            }
                            
                            .oxy-dynamic-list.flickity-enabled .flickity-viewport {
                                overflow-x: hidden;
                            }

                            .oxy-carousel-builder_gallery-images {
                                display: block;
                            }

                            .oxy-carousel-builder_gallery-images:not(.flickity-enabled) {
                                display: flex;
                                overflow-x: hidden;
                            }

                            .oxy-carousel-builder_hidden .oxy-carousel-builder_gallery-images:not(.flickity-enabled) {
                                display: flex;
                            }
    
                            .oxy-carousel-builder_hidden .oxy-dynamic-list:not(.flickity-enabled) {
                                display: flex;
                            }
                            
                            .oxy-carousel-builder_hidden .oxy-posts:not(.flickity-enabled) {
                                display: flex;
                            }
                            .oxy-carousel-builder_hidden ul.products:not(.flickity-enabled) {
                                display: flex;
                            }
                            
                            .oxy-carousel-builder_inner[data-carousel='.oxy-inner-content']:not(.flickity-enabled) {
                                overflow-x: hidden;
                            }",
                
        ) )->setParam("description", __("Experimental: this could help reduce Content Layout Shifting"));
        */
        
         /**
         * Syncing
         */ 
        $syncing_section = $this->addControlSection("syncing_section", __("Syncing"), "assets/icon.png", $this);
        
        
        $syncing_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Use as navigation for another carousel'),
                'slug' => 'maybe_asnavfor',
            )
        )
        ->setDefaultValue('false')    
        ->setValue(array( 
            "true" => "Enable", 
            "false" => "Disable" 
            )
        )->setValueCSS( array(
            "true"  => ".flickity-viewport .oxy-carousel-builder_gallery-image,
                        .flickity-viewport .oxy-dynamic-list > .ct-div-block,
                        .flickity-viewport .product,
                        .flickity-viewport .oxy-post {
                            cursor: pointer;
                        }
                        ",            
                        
        ) )
        ->setParam("description", __("Clicking the cells will navigate another carousel (one-way sync)"));
        
        
        $syncing_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Other carousel builder selector'),
                "slug" => 'as_nav_for',
                "default" => '#main-carousel-builder',
                "condition" => 'maybe_asnavfor=true'
            )
        );
        
        
        $syncing_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Sync carousels'),
                'slug' => 'maybe_sync',
            )
            
        )->setValue(array( 
            "true" => "Enable", 
            "false" => "Disable" 
            )           
        )->setDefaultValue('false')
         ->setParam("description", __("Selecting a cell, another carousel will select its matching cell (two-way sync)"));
        
        
        $syncing_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Other carousel builder selector'),
                "slug" => 'sync_carousel',
                "default" => '#my-other-carousel',
                "condition" => 'maybe_sync=true'
            )
        );


        
         /**
         * Advanced
         */ 
        $config_other_section = $this->addControlSection("config_other_section", __("Advanced"), "assets/icon.png", $this);
        
        
        $config_other_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Percentage Position',
                'slug' => 'percentage_position'
            )
            
        )->setValue(array( 
            "true" => "Enable", 
            "false" => "Disable" 
            )           
        )->setDefaultValue('true')->setParam("description", __("Disable only if not using % for cell widths"));
        
        
        
        $config_other_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Keyboard navigation (accessibility)',
                'slug' => 'maybe_accessibility'
            )
            
        )
        ->setDefaultValue('true')    
        ->setValue(array( 
            "true" => "Enable", 
            "false" => "Disable" 
            )
        );  
        
        
        $config_other_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Right to left'),
                'slug' => 'right_to_left'
            )
            
        )->setDefaultValue('false')
        ->setValue(array( 
             "true" => "Enable", 
            "false" => "Disable"
            )
        );  
        
        
        $config_other_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Woo results count'),
                'slug' => 'woo_results_count',
                'condition' => 'carousel_type=woo'
            )
            
        )->setDefaultValue('disable')
        ->setValue(array( 
             "enable" => "Show", 
            "disable" => "Hide"
            )
        )->setValueCSS( array(
            "enable"  => " .woocommerce-result-count {
                            display: block;
                        }
                        ",
        ) );
        
        
        $config_other_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Woo sorting dropdown'),
                'slug' => 'woo_sorting',
                'condition' => 'carousel_type=woo'
            )
            
        )->setDefaultValue('disable')
        ->setValue(array( 
             "enable" => "Show", 
            "disable" => "Hide"
            )
        )->setValueCSS( array(
            "enable"  => " .woocommerce-ordering {
                            display: block;
                        }
                        ",
        ) );  
        
        
        $config_other_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Woo cart buttons'),
                'slug' => 'woo_cart_buttons',
                'condition' => 'carousel_type=woo'
            )
            
        )->setDefaultValue('display')
        ->setValue(array(  
            "display" => "Show",
            "hide" => "Hide"
            )
        )->setValueCSS( array(
            "hide"  => ".product .add_to_cart_button {
                            display: none;
                        }
                        
                        .product .added_to_cart {
                            display: none;
                        }
                        
                        .product .button {
                            display: none;
                        }",
        ) );  
        
        $config_other_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Woo prices'),
                'slug' => 'woo_price',
                'condition' => 'carousel_type=woo'
            )
            
        )->setDefaultValue('display')
        ->setValue(array( 
            "display" => "Show",
            "hide" => "Hide"
            )
        )->setValueCSS( array(
            "hide"  => ".product .price {
                            display: none;
                        }
                        ",
        ) );  
        
        
        $config_other_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Woo product title'),
                'slug' => 'woo_title',
                'condition' => 'carousel_type=woo'
            )
            
        )->setDefaultValue('display')
        ->setValue(array( 
            "display" => "Show",
            "hide" => "Hide"
            )
        )->setValueCSS( array(
            "hide"  => ".product .woocommerce-loop-product__title {
                            display: none;
                        }
                        ",
        ) );  
        
        
         $config_other_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Parallax Elements',
                'slug' => 'parallax_bg',
                'condition' => 'wrap_around=false&&carousel_type!=woo&&carousel_type!=custom&&maybe_fade!=true&&carousel_type!=medialibrary&&carousel_type!=acf_gallery'
            )
            
        )->setDefaultValue('false')
        ->setValue(array( 
             "true" => "Enable", 
            "false" => "Disable"
            )
        )->setParam("description", __("Need to add the 'data-speed' attribute on inner elements with a value from 1 - 20"));

        $config_other_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Images loaded'),
                'slug' => 'images_loaded'
            )
            
        )->setDefaultValue('true')
        ->setValue(array( 
             "true" => "Enable", 
            "false" => "Disable"
            )
        )->setParam("description", __("Re-positions cells once their images have loaded."));


        $config_other_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Enable cell selector for repeater',
                'slug' => 'maybe_cell_selector',
                'condition' => 'carousel_type=repeater'
            )
            
        )->setDefaultValue('false')
        ->setValue(array( 
             "true" => "Enable", 
            "false" => "Disable"
            )
        )->setParam("description", __("To remove dependancy of the repeater IDs, you can give the repeater divs a class"));
        
        $config_other_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Cell selector'),
                "slug" => 'cell_selector',
                "default" => '',
                "condition" => 'carousel_type=repeater&&maybe_cell_selector=true'
            )
        )->setParam("description", __("Add the selector for the class you've added to the repeater div for the cells"));
        
       
       
    }
    
    
    
    function defaultCSS() {
        
        $css = ".oxygenberg-element.oxy-dynamic-list:empty:after {
                    display: block;
                    content: attr(gutenberg-placeholder);
                }";
        
        return $css;
        
    }
   
    function customCSS($options, $selector) {
        
        $css = '';
        
        if( ! $this->css_added ) {
        
            $css .= file_get_contents( plugin_dir_path(__FILE__) . 'assets/flickity/flickity.css' );

            $css .= ".oxy-carousel-builder {
                        display: flex;
                        flex-direction: column;
                        position: relative;
                        width: 100%;
                        --carousel-space-between: 0px;
                        --fade-duration: 400ms;
                    }

                    .oxy-carousel-builder .oxy-dynamic-list.flick:not(.ct-section) {
                        display: block;
                    }

                    .oxygen-builder-body .oxy-carousel-builder .flickity-prev-next-button,
                    .oxygen-builder-body .oxy-carousel-builder .flickity-page-dots {
                        z-index: 2147483643;
                    }

                    .oxy-carousel-builder .oxy-dynamic-list > div.flickity-viewport:not(.oxy_repeater_original):first-child {
                        display: block;
                    }

                    .oxy-carousel-builder .oxy-dynamic-list {
                        display: flex;
                        flex-direction: row;
                        flex-wrap: nowrap;
                        justify-content: flex-start;
                    }

                    .oxygen-builder-body .oxy-carousel-builder_gallery-images {
                        display: flex;
                        flex-direction: row;
                        flex-wrap: nowrap;
                    }

                    .oxy-carousel-builder .oxy-woo-element ul.products {
                        display: flex;
                        flex-direction: row;
                        flex-wrap: nowrap;
                        margin: 0;
                    }

                    .oxy-carousel-builder .oxy-carousel-builder_icon {
                        -webkit-tap-highlight-color: transparent;
                      -webkit-user-select: none;
                         -moz-user-select: none;
                          -ms-user-select: none;
                              user-select: none;
                    }

                    .oxy-carousel-builder ul.products::before {
                        content: none;
                    }

                    .oxy-carousel-builder .oxy-woo-element ul.products .product {
                        float: none;
                        padding: 0;
                        flex-shrink: 0;
                    }           

                    .oxy-carousel-builder .oxy-post {
                        float: none;
                        flex-shrink: 0;
                    }

                    .oxy-carousel-builder .cell {
                        float: none;
                        flex-shrink: 0;
                        overflow: hidden; 
                    }

                    .oxy-carousel-builder .flickity-viewport {
                        transition-property: height;
                    }

                    .oxy-carousel-builder .flickity-page-dots {
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        position: relative;
                    }

                    .oxy-carousel-builder .dot.is-selected:only-child {
                        display: none;
                    }

                    .oxy-carousel-builder .oxy-dynamic-list::after {
                        content: 'flickity';
                        display: none;
                    }

                    .oxy-carousel-builder ul.products::after {
                        content: 'flickity';
                        display: none;
                    }

                    .oxy-carousel-builder .oxy-posts::after {
                        content: 'flickity';
                        display: none;
                    }

                    .oxy-carousel-builder_gallery-images::after {
                        content: 'flickity';
                        display: none;
                    }

                    .oxy-carousel-builder .oxy-inner-content::after {
                        content: 'flickity';
                        display: none;
                    }

                    .oxy-carousel-builder .woocommerce-result-count,
                    .oxy-carousel-builder .woocommerce-ordering {
                        display: none;
                    }

                    .oxy-carousel-builder .oxy-dynamic-list > .ct-div-block,
                    .oxy-carousel-builder .oxy-dynamic-list .flickity-slider > .ct-div-block {
                        transition: transform 0.4s ease, background-color 0.4s ease, color 0.4s ease, opacity 0.4s ease;
                        -webkit-transition: -webkit-transform 0.4s ease, background-color 0.4s ease, color 0.4s ease, opacity 0.4s ease;
                    }

                    .oxy-carousel-builder_gallery-image {
                        flex-direction: column;
                        transition: transform 0.4s ease, background-color 0.4s ease, color 0.4s ease, opacity 0.4s ease;
                        -webkit-transition: -webkit-transform 0.4s ease, background-color 0.4s ease, color 0.4s ease, opacity 0.4s ease;
                    }

                    .oxy-carousel-builder ul.products .product,
                    .oxy-carousel-builder ul.products .flickity-slider > .product {
                        transition: transform 0.4s ease, background-color 0.4s ease, color 0.4s ease, opacity 0.4s ease;
                        -webkit-transition: -webkit-transform 0.4s ease, background-color 0.4s ease, color 0.4s ease, opacity 0.4s ease;
                    }

                    .oxy-carousel-builder .cell,
                    .oxy-carousel-builder .flickity-slider > .cell {
                        transition: transform 0.4s ease, background-color 0.4s ease, color 0.4s ease, opacity 0.4s ease;
                        -webkit-transition: -webkit-transform 0.4s ease, background-color 0.4s ease, color 0.4s ease, opacity 0.4s ease;
                    }

                    .oxy-carousel-builder .oxy-dynamic-list > .ct-div-block {
                        flex-shrink: 0;
                        overflow: hidden;
                    }

                    .oxy-carousel-builder .oxy-dynamic-list .flickity-slider > .ct-div-block {
                        flex-shrink: 0;
                        overflow: hidden;
                    }

                    .oxy-carousel-builder_gallery-image {
                        flex-shrink: 0;
                        overflow: hidden;
                    }

                    .oxy-carousel-builder_gallery-image img {
                        width: auto;
                        max-width: none;
                        vertical-align: middle;
                    }

                    .oxy-carousel-builder_icon {
                        background-color: #222;
                        color: #fff;
                        display: inline-flex;
                        font-size: 14px;
                        padding: .75em;
                        cursor: pointer;
                        transition-duration: 400ms;
                        transition-property: color, background-color;
                    }


                    .oxy-carousel-builder_icon {
                        top: 50%;
                        position: absolute;
                        transform: translateY(-50%);
                        -webkit-transform: translateY(-50%);
                    }

                    .oxy-carousel-builder_icon.oxy-carousel-builder_icon-fullscreen {
                        position: fixed;
                        z-index: 12;
                    }

                    .oxy-carousel-builder_prev {
                        left: 0;
                    }

                    .oxy-carousel-builder_next {
                        right: 0;
                    }

                    .oxy-carousel-builder_icon svg {
                        height: 1em;
                        width: 1em;
                        fill: currentColor;
                    }

                    .oxy-carousel-builder .flickity-page-dots .dot {
                        --selected-dot-scale: 1;
                        flex-shrink: 0;
                    }

                    .oxy-carousel-builder .oxy-repeater-pages-wrap {
                        display: none;
                    }

                    .oxy-carousel-builder .oxy-easy-posts-pages {
                        display: none;
                    }

                    .oxy-carousel-builder .is-next {
                        --cell-next-scale: 1;
                        --cell-next-rotate: 0deg;
                    }

                    .oxy-carousel-builder .is-selected {
                        --cell-selected-scale: 1;
                        --cell-selected-rotate: 0deg;
                    }

                    .oxy-carousel-builder .is-previous {
                        --cell-prev-scale: 1;
                        --cell-prev-rotate: 0deg;
                    }

                   .oxy-carousel-builder .oxy-inner-content [data-speed] {
                        transition: transform 0s;
                        -webkit-transition: transform 0s;
                    }


                    // In builder styles
                    .oxygen-builder-body .oxy-carousel-builder .oxy-dynamic-list .flickity-slider > .ct-div-block:not(:first-child) {
                        opacity: .4;
                        pointer-events: none;
                    }

                    .oxy-carousel-builder .oxy-inner-content:empty {
                        min-height: 80px;
                    }

                    .admin-bar .flickity-enabled.is-fullscreen .flickity-fullscreen-button {
                        top: 42px;
                    }

                    .flickity-fullscreen-button {
                        z-index: 10;
                    }

                    .oxy-carousel-builder .oxy-inner-content:empty + .flickity-page-dots .dot:not(:first-child) {
                        display: none;
                    }

                    .oxygen-builder-body .oxy-carousel-builder .oxy-dynamic-list.flickity-enabled {
                        pointer-events: none;
                    }
                    
                    .oxygen-builder-body .oxy-carousel-builder .oxy-dynamic-list.flickity-enabled vime-dbl-click-fullscreen.enabled,
                    .oxygen-builder-body .oxy-carousel-builder .oxy-dynamic-list.flickity-enabled vime-click-to-play.enabled,
                    .oxygen-builder-body .oxy-carousel-builder .oxy-dynamic-list.flickity-enabled vime-controls,
                    .oxygen-builder-body .oxy-carousel-builder .oxy-dynamic-list.flickity-enabled vime-volume-control {
                        pointer-events: none;
                    }
                    
                    .oxygen-builder-body .oxy-carousel-builder.ct-active .oxy-dynamic-list:not(.flickity-enabled) > div:not(.oxy_repeater_original) {
                        opacity: 0.5;
                    }
                    
                    
                    .oxygen-builder-body .oxy-carousel-builder .oxy-dynamic-list.flickity-enabled .oxy_repeater_original {
                         /* display: none!important; */
                    }

                    .oxygen-builder-body .oxy-flickity-buttons {
                        position: absolute;
                        display: block;
                        align-items: center;
                        color: #fff;
                        background-color: rgb(100, 0, 255);
                        z-index: 2147483641;
                        cursor: default;
                    }

                    .oxygen-builder-body .oxy-flickity-buttons .hide {
                        display: none;
                    }
                    
                    .oxygen-builder-body .oxy-carousel-builder .oxy-inner-content[data-carousel='.oxy-inner-content'],
                    .oxygen-builder-body .oxy-carousel-builder .oxy-inner-content[data-carousel='.oxy-carousel-builder_gallery-images'] .oxy-carousel-builder_gallery-images,
                    .oxygen-builder-body .oxy-carousel-builder .oxy-inner-content[data-carousel='.oxy-posts'] .oxy-posts,
                    .oxygen-builder-body .oxy-carousel-builder .oxy-inner-content[data-carousel='.oxy-dynamic-list'] .oxy-dynamic-list,
                    .oxygen-builder-body .oxy-carousel-builder .oxy-inner-content[data-carousel='ul.products'] ul.products {
                      overflow-x: scroll;
                    }
                    
                    [data-flickity-lazyload] {
                        transition: opacity .4s ease;
                    }
                    
                    .oxy-carousel-builder_gallery-image-wrapper {
                            display: flex;
                            flex-direction: column;
                            position: relative;
                    }
                    
                    .oxy-carousel-builder_caption {
                        display: none;
                        position: absolute;
                        bottom: 0;
                        width: 100%;
                        left: 0;
                        justify-content: center;
                        color: #fff;
                        background-color: rgba(66,60,60,0.2);
                    }
                    
                    a.oxy-carousel-builder_gallery-image {
                        text-decoration: none;
                    }
                    
                    .oxy-carousel-builder_fadein .oxy-carousel-builder_gallery-images,
                    .oxy-carousel-builder_fadein .oxy-dynamic-list,
                    .oxy-carousel-builder_fadein .oxy-posts,
                    .oxy-carousel-builder_fadein ul.products {
                          opacity: 0;
                          -webkit-transition: opacity;
                          transition-property: opacity;
                          -webkit-transition-delay: .1s;
                          transition-delay: .1s;
                          transition-duration: var(--fade-duration);
                          -webkit-transition-duration: var(--fade-duration);
                    }

                    .oxy-carousel-builder_fadein .oxy-carousel-builder_gallery-images.flickity-enabled,
                    .oxy-carousel-builder_fadein .oxy-dynamic-list.flickity-enabled,
                    .oxy-carousel-builder_fadein .oxy-posts.flickity-enabled,
                    .oxy-carousel-builder_fadein ul.products.flickity-enabled {
                      opacity: 1;
                    }
                    
                    .oxygenberg-element.oxy-carousel-builder {
                        overflow-x: scroll;
                    }

                    body:not(.oxygen-builder-body) .oxy-carousel-builder_inner[data-carousel='.oxy-carousel-builder_gallery-images']:empty,
                    body:not(.oxygen-builder-body) .oxy-carousel-builder_inner[data-carousel='.oxy-carousel-builder_gallery-images']:empty + .oxy-carousel-builder_prev,
                    body:not(.oxygen-builder-body) .oxy-carousel-builder_inner[data-carousel='.oxy-carousel-builder_gallery-images']:empty + .oxy-carousel-builder_prev + .oxy-carousel-builder_next {
                        display: none;
                    }
                    
                    ";
            
            $this->css_added = true;
            
        }
        
        $css .= "$selector .oxy-posts {
                    display: flex;
                    flex-direction: row;
                    flex-wrap: nowrap;
                }
                
                $selector .is-next {
                  
                    transform: scale(var(--cell-next-scale)) rotate(var(--cell-next-rotate));
                    -webkit-transform: scale(var(--cell-next-scale)) rotate(var(--cell-next-rotate));
               }
               
               $selector .is-selected:not(.dot) {
                  
                    transform: scale(var(--cell-selected-scale)) rotate(var(--cell-selected-rotate));
                    -webkit-transform: scale(var(--cell-selected-scale)) rotate(var(--cell-selected-rotate));
               }
               
               $selector .is-previous {
                  
                    transform: scale(var(--cell-prev-scale)) rotate(var(--cell-prev-rotate));
                    -webkit-transform: scale(var(--cell-prev-scale)) rotate(var(--cell-prev-rotate));
               }
               
               $selector .dot.is-selected {
                    transform: scale(var(--selected-dot-scale));
                    -webkit-transform: scale(var(--selected-dot-scale));
               }
               
                $selector .flickity-enabled.is-fullscreen img {
                    object-fit: cover;
                }
                
                $selector .flickity-enabled.is-fullscreen {
                    height: 100%!important;
                    z-index: 5;
                }
                
                $selector .flickity-enabled.is-fullscreen .oxy-inner-content {
                    height: 100%!important;
                }
                
                $selector .flickity-enabled.is-fullscreen .cell {
                    height: 100%!important;
                }
                
                $selector .flickity-enabled.is-fullscreen .product {
                    height: 100%!important;
                }
                
                $selector .flickity-enabled.is-fullscreen .oxy-dynamic-list > .ct-div-block {
                    height: 100%!important;
                }
                
                $selector .flickity-enabled.is-fullscreen .oxy-post {
                    height: 100%!important;
                }
                
                $selector .flickity-enabled.is-fullscreen > .ct-div-block, 
                $selector .flickity-enabled.is-fullscreen .flickity-slider > .ct-div-block, 
                $selector .oxy-inner-content .flickity-enabled.is-fullscreen {
                    height: 100%!important;
                }
                
                $selector .flickity-enabled.is-fullscreen .oxy-carousel-builder_gallery-image {
                    height: 100%;
                }
                
                $selector .flickity-enabled.is-fullscreen .oxy-carousel-builder_gallery-image img {
                    height: 100%;
                }
                
                $selector .flickity-enabled {
                    display: block;
                }
                
                $selector .oxy-dynamic-list.flickity-enabled {
                    display: block;
                }
                
                .oxygen-builder-body $selector [data-flickity-lazyload] {
                    opacity: 1;
                }   
                
                ";
        
        if (!isset($options["oxy-carousel-builder_editor_mode"]) || $options["oxy-carousel-builder_editor_mode"] === "preview") {
            
            $css .= "
                
                .oxygen-builder-body $selector .oxy-inner-content {
                    cursor: pointer;
                }    
                
                .oxygen-builder-body $selector .oxy-inner-content + .flickity-page-dots {
                    display: none;
                }
            
            ";
            
        }
        
        if (!isset($options["oxy-carousel-builder_editor_mode"]) || $options["oxy-carousel-builder_editor_mode"] === "edit") {
            
            $css .= "
            
                .oxygen-builder-body $selector .oxy-dynamic-list:after {
                        content: '';
                    }
                      
                
                .oxygen-builder-body $selector .flickity-viewport + .flickity-page-dots {
                    display: none;
                }
            
            ";
            
        }
        
        if ((isset($options["oxy-carousel-builder_hide_dots_below"]) && $options["oxy-carousel-builder_hide_dots_below"]!="never")) {
                $max_width = oxygen_vsb_get_media_query_size($options["oxy-carousel-builder_hide_dots_below"]);
                $css .= "@media (max-width: {$max_width}px) {
                
                            $selector .flickity-page-dots {
                                display: none;
                            }
                            
                        }";
            }
        
        
        
        return $css;
    }
    
    function output_js() { 
        wp_enqueue_script( 'flickity', plugin_dir_url( __FILE__ ) . 'assets/flickity/flickity.pkgd.min.js', '', '2.2.1' );
    }
    
    function output_fade_js() {
        wp_enqueue_script( 'flickity-fade', plugin_dir_url( __FILE__ ) . 'assets/flickity/flickity-fade.js', '', '2.2.1' );
    } 
    
    function output_bg_lazy_js() {
        wp_enqueue_script( 'flickity-bg-lazy', plugin_dir_url( __FILE__ ) . 'assets/flickity/bg-lazyload.js', '', '1.0.1' );
    }

    function output_hash_js() {
        wp_enqueue_script( 'flickity-hash', plugin_dir_url( __FILE__ ) . 'assets/flickity/hash.js', '', '1.0.3' );
    }
    
    function output_fullscreen_js() {
        wp_enqueue_script( 'flickity-fullscreen', plugin_dir_url( __FILE__ ) . 'assets/flickity/flickity-fullscreen.js', '', '2.2.1' );
    }
    
    function output_sync_js() {
        wp_enqueue_script( 'flickity-sync', plugin_dir_url( __FILE__ ) . 'assets/flickity/flickity-sync.js', '', '2.2.1' );
    }
    
    function output_init_js() {   
        if ( ! function_exists('do_oxygen_elements') ) {
            wp_enqueue_script( 'flickity-init', plugin_dir_url( __FILE__ ) . 'assets/flickity/flickity-init.js', '', '2.3.1' );
        } else {
            wp_enqueue_script( 'flickity-init-js', plugin_dir_url( __FILE__ ) . 'assets/flickity/flickity-init-4.js', '', '2.3.3' );
        }
    }


}

new ExtraCarousel();