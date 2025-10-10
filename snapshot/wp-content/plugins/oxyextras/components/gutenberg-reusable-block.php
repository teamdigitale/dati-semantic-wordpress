<?php

class ExtraGutenbergBlock extends OxygenExtraElements {

	function name() {
        return 'Reusable Block';
    }
    
    function icon() {
        return plugin_dir_url(__FILE__) . 'assets/icons/'.basename(__FILE__, '.php').'.svg';
    }
    
    function extras_button_place() {
        return "wordpress";
    }

    function render($options, $defaults, $content) {
        
        $output = '';
        
        if ( isset( $options['reusable_block'] ) ) {

            $reusable_block_query = new WP_Query(
                array(
                    'post_type'              => 'wp_block',
                    'title'                  => esc_attr( $options['reusable_block'] ),
                    'post_status'            => 'all',
                    'posts_per_page'         => 1,
                    'no_found_rows'          => true,
                    'ignore_sticky_posts'    => true,
                    'update_post_term_cache' => false,
                    'update_post_meta_cache' => false,
                    'orderby'                => 'post_date ID',
                    'order'                  => 'ASC',
                )
            );
             
            if ( ! empty( $reusable_block_query->post ) ) {
                $reusable_block = $reusable_block_query->post;
            } else {
                $reusable_block = null;
            }
            
            if (null !== $reusable_block ) {

                $reusable_block_id = $reusable_block->ID;
                $output .= do_blocks( get_the_content('', '', get_post( $reusable_block_id ) ) );

            }
            
        } 
        
        echo $output;
         
    }

    function class_names() {
        return '';
    }
    
   
    function controls() {
        
        $args = array(
          'posts_per_page'  => -1,
          'post_type' => 'wp_block',
        );

        $reusable_blocks = get_posts( $args );
       
        $dropdown_options = array();
        foreach ($reusable_blocks as $reusable_block)
        {
            array_push($dropdown_options, $reusable_block->post_title);
        }

        $this->addOptionControl(
            array(
                "type" => "dropdown",
                "name" => "Resuable Gutenberg Block (Pattern)",
                "slug" => "reusable_block"
            )
        )->setValue($dropdown_options)->rebuildElementOnChange();


    }
    
    function afterInit() {
        $this->removeApplyParamsButton();
    }

}

new ExtraGutenbergBlock();