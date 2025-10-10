<?php

class ExtraSocial extends OxygenExtraElements {
    
    var $css_added = false;
    var $js_added = false;
    var $print_js_added = false;

	function name() {
        return 'Social Share Buttons';
    }

    function icon() {
        return plugin_dir_url(__FILE__) . 'assets/icons/'.basename(__FILE__, '.php').'.svg';
    }
    
    function extras_button_place() {
        return "other";
    }
    
    function enablePresets() {
        return true;
    }
    
    function enableFullPresets() {
        return true;
    }
    
    function init() {
        
        add_filter("oxy_allowed_empty_options_list", array( $this, "allowedEmptyOptions") );

    }

    function render($options, $defaults, $content) { 
        
        // icons
        $twitter_icon = esc_attr($options['twitter_icon']);
        $facebook_icon  = esc_attr($options['facebook_icon']);
        $email_icon  = esc_attr($options['email_icon']);
        $linkedin_icon  = esc_attr($options['linkedin_icon']);
        $whatsapp_icon = esc_attr($options['whatsapp_icon']);
        $telegram_icon = esc_attr($options['telegram_icon']);
        $pinterest_icon = esc_attr($options['pinterest_icon']);
        $xing_icon = esc_attr($options['xing_icon']);
        $line_icon = esc_attr($options['line_icon']);
        $print_icon = esc_attr($options['print_icon']);

        global $oxygen_svg_icons_to_load;
        $oxygen_svg_icons_to_load[] = $twitter_icon;
        $oxygen_svg_icons_to_load[] = $facebook_icon;
        $oxygen_svg_icons_to_load[] = $email_icon;
        $oxygen_svg_icons_to_load[] = $linkedin_icon;
        $oxygen_svg_icons_to_load[] = $whatsapp_icon;
        $oxygen_svg_icons_to_load[] = $telegram_icon;
        $oxygen_svg_icons_to_load[] = $pinterest_icon;
        $oxygen_svg_icons_to_load[] = $xing_icon;
        $oxygen_svg_icons_to_load[] = $line_icon;
        $oxygen_svg_icons_to_load[] = $print_icon;
        
        // Button Text
        
        //$twitter_texts = htmlentities(esc_attr($options['twitter_text']));
        $twitter_text = esc_attr($options['twitter_text']);
        $facebook_text = esc_attr($options['facebook_text']);
        $email_text = esc_attr($options['email_text']);
        $linkedin_text = esc_attr($options['linkedin_text']);
        $whatsapp_text = esc_attr($options['whatsapp_text']);
        $telegram_text = esc_attr($options['telegram_text']);
        $pinterest_text = esc_attr($options['pinterest_text']);
        $xing_text = esc_attr($options['xing_text']);
        $line_text = esc_attr($options['line_text']);
        $print_text = esc_attr($options['print_text']);
        $print_selector = isset( $options['print_selector'] ) ? esc_attr( $options['print_selector'] ) : 'body';

        $print_color_adjust = isset( $options['print_color_adjust'] ) ? esc_attr( $options['print_color_adjust'] ) : 'exact';
        $remove_share_buttons = isset( $options['remove_share_buttons'] ) ? esc_attr( $options['remove_share_buttons'] ) : 'disable';
        
        
        
        global $post;
        $post_id = $post->ID;
        $site_title = get_bloginfo();
        $home_url = get_home_url();
        $title = get_the_title( $post_id );
        $title_noquotes = str_replace('"', '&quot;', $title);
        $title_decode = html_entity_decode($title,ENT_QUOTES,'UTF-8');
        $title_encode = urlencode($title_decode);
        $link = get_permalink( $post_id );
        $url = urlencode($link);
        $thumbnail = get_the_post_thumbnail_url();
        $email_body = esc_attr($options['email_body']);
        $email_subject = esc_attr($options['email_subject']);
        $twitter_handle  = isset( $options['twitter_handle'] ) ? '&via='. esc_attr($options['twitter_handle']) : '';
        $whatsapp_urltext = '';

        $line_msg_text = isset( $options['line_msg_text'] ) ? esc_attr($options['line_msg_text']) : "";
        
        $custom_share_url = isset( $options['custom_share_url'] ) ? esc_attr($options['custom_share_url']) : "";
        
        $email_post_title = ('true' !== esc_attr($options['maybe_email_post_title'])) ? '' : $title_noquotes; 

        $behaviour = isset( $options['behaviour'] ) ? esc_attr($options['behaviour']) : "";
        
        
        if ('home' === esc_attr($options['social_urls'])) {
            
            $url = urlencode($home_url);
            $whatsapp_urltext = urlencode($title_decode . ' - ' . $home_url);
            
        } else if ('custom' === esc_attr($options['social_urls'])) {
            
            $url = urlencode($custom_share_url);
            $whatsapp_urltext = urlencode($title_decode . ' - ' . $custom_share_url);
            
        } else {
            
            $url = urlencode($link);
            $whatsapp_urltext = urlencode($title_decode . ' - ' . $link);
            
        }
        
        $line_icon_source = isset( $options['line_icon_source'] ) ? esc_attr($options['line_icon_source']) : "";
   
        // Share URLs
        $twitter_url = 'https://twitter.com/share?text=' . $title_encode . '&url=' . $url . $twitter_handle;
        $facebook_url = 'https://www.facebook.com/sharer/sharer.php?u=' . $url;
        $email_url = 'mailto:?body='. $email_body .' '. $url .'&subject='. $email_subject .' '. $email_post_title;
        $linkedin_url = 'https://www.linkedin.com/shareArticle?mini=true&url='. $url .'&title='. $title_noquotes .'&summary='. $email_subject .'&source='. $site_title;
        $whatsapp_url = 'https://api.whatsapp.com/send?text=' . $whatsapp_urltext;
        $telegram_url = 'https://telegram.me/share/url?url='  . $url . '&text=' . $title_encode;
        $pinterest_url = 'https://www.pinterest.com/pin/create/button/?url=' . $url . '&media=' . $thumbnail . '&description=' . $title_encode;
        $xing_url = 'https://www.xing.com/spi/shares/new?url='. $url .'';
        $line_url = 'https://lineit.line.me/share/ui?url='. $url .'&text='. $line_msg_text;

        
        // Twitter
        if ( $options['twitter_display'] !== 'hide' ) {
            
             ?> <a class="oxy-share-button twitter" target="_blank" aria-label="<?php echo $twitter_text; ?>" href="<?php echo $twitter_url; ?>" rel="noopener noreferrer nofollow">
        <?php if ( esc_attr($options['social_display']) != 'text' ) { ?>
                <span class="oxy-share-icon"><svg id="twitter<?php echo esc_attr($options['selector']); ?>-share-icon"><use xlink:href="#<?php echo $twitter_icon; ?>"></use></svg></span> 
        <?php } ?>
        <?php if ( esc_attr($options['social_display']) != 'icon' ) { ?>
                <span class="oxy-share-name"><?php echo $twitter_text; ?></span>
        <?php } ?>
        </a> <?php
            
        } 
        
        
         // Facebook
         if ( $options['facebook_display'] !== 'hide' ) {
            
             ?> <a class="oxy-share-button facebook" target="_blank" aria-label="<?php echo $facebook_text; ?>" href="<?php echo $facebook_url; ?>" rel="noopener noreferrer nofollow">
            <?php if ( esc_attr($options['social_display']) != 'text' ) { ?>
                <span class="oxy-share-icon"><svg id="facebook<?php echo esc_attr($options['selector']); ?>-share-icon"><use xlink:href="#<?php echo $facebook_icon; ?>"></use></svg></span> <?php } ?>
            
            <?php if ( esc_attr($options['social_display']) != 'icon' ) { ?>
                <span class="oxy-share-name"><?php echo $facebook_text; ?></span>
        <?php } ?>
        </a> <?php
             
         }
        
        
         // Email
         if ( $options['email_display'] !== 'hide' ) {
            
             ?> <a class="oxy-share-button email" target="_blank" aria-label="<?php echo $email_text; ?>" href="<?php echo $email_url; ?>" rel="noopener noreferrer nofollow">
            <?php if ( esc_attr($options['social_display']) != 'text' ) { ?>
                <span class="oxy-share-icon"><svg id="email<?php echo esc_attr($options['selector']); ?>-share-icon"><use xlink:href="#<?php echo $email_icon; ?>"></use></svg></span>
             <?php } ?>
            <?php if ( esc_attr($options['social_display']) != 'icon' ) { ?>
                <span class="oxy-share-name"><?php echo $email_text; ?></span>
           <?php } ?>
        </a> <?php
             
        }
        
        
        // LinkedIn
        
        if ( $options['linkedin_display'] !== 'hide' ) {
            
             ?> <a class="oxy-share-button linkedin" target="_blank" aria-label="<?php echo $linkedin_text; ?>" href="<?php echo $linkedin_url; ?>" rel="noopener noreferrer nofollow">
            <?php if ( esc_attr($options['social_display']) != 'text' ) { ?>
                <span class="oxy-share-icon"><svg id="linkedin<?php echo esc_attr($options['selector']); ?>-share-icon"><use xlink:href="#<?php echo $linkedin_icon; ?>"></use></svg></span>
             <?php } ?>
            <?php if ( esc_attr($options['social_display']) != 'icon' ) { ?>
                <span class="oxy-share-name"><?php echo $linkedin_text; ?></span>
           <?php } ?>
        </a> <?php
             
        }
        
        
        // WhatsApp
        
        if ( $options['whatsapp_display'] !== 'hide' ) {
            
             ?> <a class="oxy-share-button whatsapp" target="_blank" aria-label="<?php echo $whatsapp_text; ?>" href="<?php echo $whatsapp_url; ?>" rel="noopener noreferrer nofollow">
            <?php if ( esc_attr($options['social_display']) != 'text' ) { ?>
                <span class="oxy-share-icon"><svg id="whatsapp<?php echo esc_attr($options['selector']); ?>-share-icon"><use xlink:href="#<?php echo $whatsapp_icon; ?>"></use></svg></span>
             <?php } ?>
            <?php if ( esc_attr($options['social_display']) != 'icon' ) { ?>
                <span class="oxy-share-name"><?php echo $whatsapp_text; ?></span>
           <?php } ?>
        </a> <?php
             
        }
        
        
         // Telegram
        
        if ( $options['telegram_display'] !== 'hide' ) {
            
             ?> <a class="oxy-share-button telegram" target="_blank" aria-label="<?php echo $telegram_text; ?>" href="<?php echo $telegram_url; ?>" rel="noopener noreferrer nofollow">
            <?php if ( esc_attr($options['social_display']) != 'text' ) { ?>
                <span class="oxy-share-icon"><svg id="telegram<?php echo esc_attr($options['selector']); ?>-share-icon"><use xlink:href="#<?php echo $telegram_icon; ?>"></use></svg></span>
             <?php } ?>
            <?php if ( esc_attr($options['social_display']) != 'icon' ) { ?>
                <span class="oxy-share-name"><?php echo $telegram_text; ?></span>
           <?php } ?>
        </a> <?php
             
        }
        
        
         // Pinterest
        
        if ( $options['pinterest_display'] !== 'hide' ) {
            
             ?> <a class="oxy-share-button pinterest" target="_blank" aria-label="<?php echo $pinterest_text; ?>" href="<?php echo $pinterest_url; ?>" rel="noopener noreferrer nofollow">
            <?php if ( esc_attr($options['social_display']) != 'text' ) { ?>
                <span class="oxy-share-icon"><svg id="pinterest<?php echo esc_attr($options['selector']); ?>-share-icon"><use xlink:href="#<?php echo $pinterest_icon; ?>"></use></svg></span>
             <?php } ?>
            <?php if ( esc_attr($options['social_display']) != 'icon' ) { ?>
                <span class="oxy-share-name"><?php echo $pinterest_text; ?></span>
           <?php } ?>
        </a> <?php
             
        }
        
        // Xing
        
        if ( $options['xing_display'] !== 'hide' ) {
            
             ?> <a class="oxy-share-button xing" target="_blank" aria-label="<?php echo $xing_text; ?>" href="<?php echo $xing_url; ?>" rel="noopener noreferrer nofollow">
            <?php if ( esc_attr($options['social_display']) != 'text' ) { ?>
                <span class="oxy-share-icon"><svg id="xing<?php echo esc_attr($options['selector']); ?>-share-icon"><use xlink:href="#<?php echo $xing_icon; ?>"></use></svg></span>
             <?php } ?>
            <?php if ( esc_attr($options['social_display']) != 'icon' ) { ?>
                <span class="oxy-share-name"><?php echo $xing_text; ?></span>
           <?php } ?>
        </a> <?php
             
        }

         // Line

        if ( $options['line_display'] !== 'hide' ) {

            ?> <a class="oxy-share-button line" target="_blank" aria-label="<?php echo $line_text; ?>" href="<?php echo $line_url; ?>" rel="noopener noreferrer nofollow">
            <?php if ( esc_attr($options['social_display']) != 'text' ) { ?>
                <span class="oxy-share-icon">
                <?php if ( esc_attr($options['line_icon_source']) == 'custom' ) { ?>
                <svg id="line<?php echo esc_attr($options['selector']); ?>-share-icon"><use xlink:href="#<?php echo $line_icon; ?>"></use></svg>
                <?php } else { ?>
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" preserveAspectRatio="xMidYMid meet" viewBox="0 0 640 640" width="640" height="640"><defs><path d="M359.15 22.4L371.9 23.86L384.49 25.71L396.9 27.95L409.12 30.57L421.14 33.57L432.96 36.93L444.56 40.64L455.93 44.7L467.06 49.11L477.94 53.84L488.56 58.9L498.91 64.27L508.99 69.94L518.77 75.92L528.25 82.19L537.42 88.73L546.27 95.56L554.79 102.64L562.97 109.99L570.8 117.58L578.26 125.41L585.35 133.48L592.06 141.77L598.37 150.28L604.28 158.99L609.78 167.9L614.85 177.01L619.49 186.29L623.69 195.75L627.43 205.38L630.7 215.17L633.5 225.11L635.81 235.18L637.63 245.39L638.94 255.73L639.73 266.18L640 276.75L639.97 280.45L639.87 284.14L639.71 287.81L639.48 291.47L639.19 295.12L638.84 298.76L638.42 302.38L637.94 305.98L637.4 309.57L636.79 313.15L636.13 316.71L635.4 320.25L634.62 323.78L633.77 327.29L632.87 330.78L631.9 334.25L630.88 337.71L629.8 341.15L628.66 344.57L627.47 347.97L626.22 351.35L624.91 354.71L623.54 358.06L622.12 361.38L620.65 364.68L619.12 367.96L617.54 371.22L615.9 374.45L614.21 377.67L612.47 380.86L610.67 384.03L608.82 387.18L606.92 390.3L604.97 393.4L602.97 396.48L600.92 399.53L598.82 402.55L596.67 405.55L594.47 408.53L592.23 411.48L592.11 411.64L592 411.81L591.88 411.98L591.76 412.15L591.64 412.32L591.52 412.49L591.4 412.67L591.27 412.84L591.14 413.01L591.02 413.19L590.88 413.37L590.75 413.54L590.62 413.72L590.48 413.9L590.34 414.08L590.2 414.26L590.06 414.44L589.92 414.63L589.77 414.81L589.63 415L589.48 415.18L589.33 415.37L589.18 415.55L589.02 415.74L588.87 415.93L588.71 416.12L588.55 416.31L588.39 416.51L588.22 416.7L588.06 416.89L587.89 417.09L587.72 417.28L587.55 417.48L587.38 417.68L587.2 417.88L587.03 418.08L586.85 418.28L586.67 418.48L586.49 418.68L586.3 418.88L586.11 419.09L585.34 420.01L584.55 420.94L583.76 421.86L582.97 422.78L582.17 423.69L581.37 424.61L580.56 425.51L579.75 426.42L578.94 427.32L578.11 428.23L577.29 429.12L576.46 430.02L575.62 430.91L574.78 431.8L573.93 432.68L573.08 433.57L572.23 434.45L571.37 435.32L570.51 436.2L569.64 437.07L568.76 437.93L567.89 438.8L567 439.66L566.12 440.52L565.23 441.37L564.33 442.22L563.43 443.07L562.52 443.92L561.61 444.76L560.7 445.6L559.78 446.43L558.86 447.27L557.93 448.1L557 448.92L556.06 449.74L555.12 450.56L554.18 451.38L553.23 452.19L552.27 453L551.31 453.81L544.57 459.97L537.63 466.18L530.52 472.42L523.24 478.68L515.81 484.96L508.25 491.23L500.59 497.49L492.83 503.73L484.99 509.93L477.1 516.08L469.16 522.16L461.19 528.17L453.22 534.1L445.26 539.93L437.33 545.65L429.44 551.25L421.6 556.72L413.85 562.03L406.2 567.2L398.65 572.19L391.24 577L383.97 581.62L376.87 586.03L369.95 590.22L363.23 594.19L356.72 597.91L350.45 601.38L344.42 604.59L338.67 607.51L333.2 610.15L328.03 612.49L323.18 614.51L318.67 616.2L314.51 617.56L310.72 618.57L307.32 619.22L304.32 619.5L301.75 619.38L299.62 618.88L297.94 617.96L296.8 616.88L295.87 615.63L295.14 614.21L294.6 612.62L294.23 610.88L294.01 609L293.93 606.99L293.98 604.86L294.14 602.61L294.39 600.26L294.73 597.82L295.14 595.29L295.6 592.69L296.09 590.03L296.62 587.32L297.14 584.55L297.67 581.76L298.17 578.94L298.64 576.1L299.06 573.26L299.41 570.43L299.69 567.61L299.87 564.81L299.94 562.05L299.89 559.33L299.7 556.66L299.36 554.06L298.86 551.53L298.17 549.08L297.28 546.72L296.19 544.47L294.87 542.33L293.31 540.31L291.49 538.42L289.41 536.67L287.04 535.07L284.37 533.64L281.39 532.37L278.08 531.29L274.43 530.39L274.16 530.36L273.9 530.33L273.63 530.3L273.36 530.27L273.1 530.24L272.83 530.21L272.56 530.17L272.29 530.14L272.03 530.11L271.76 530.08L271.5 530.05L271.23 530.02L270.96 529.98L270.7 529.95L270.43 529.92L270.16 529.89L269.9 529.85L269.63 529.82L269.36 529.79L269.1 529.75L268.83 529.72L268.57 529.69L268.3 529.65L268.04 529.62L267.77 529.58L267.5 529.55L267.24 529.51L266.97 529.48L266.71 529.44L266.44 529.41L266.18 529.37L265.91 529.34L265.65 529.3L265.38 529.27L265.12 529.23L264.85 529.19L264.59 529.16L264.33 529.12L264.06 529.08L263.8 529.05L263.73 529.04L263.73 529.04L252.56 527.28L241.53 525.22L230.64 522.86L219.92 520.2L209.36 517.26L198.97 514.02L188.76 510.51L178.74 506.73L168.91 502.68L159.28 498.37L149.86 493.81L140.66 488.99L131.68 483.93L122.93 478.64L114.41 473.11L106.14 467.36L98.12 461.39L90.36 455.2L82.87 448.81L75.65 442.21L68.72 435.42L62.07 428.43L55.71 421.26L49.66 413.91L43.92 406.39L38.5 398.7L33.4 390.85L28.64 382.85L24.21 374.69L20.13 366.39L16.4 357.95L13.04 349.38L10.04 340.68L7.42 331.86L5.18 322.93L3.34 313.89L1.89 304.74L0.84 295.5L0.21 286.17L0 276.75L0.27 266.18L1.06 255.73L2.37 245.39L4.19 235.18L6.5 225.11L9.3 215.17L12.57 205.38L16.31 195.75L20.51 186.29L25.14 177.01L30.22 167.9L35.72 158.99L41.63 150.28L47.94 141.77L54.65 133.48L61.74 125.41L69.2 117.58L77.03 109.99L85.2 102.64L93.72 95.56L102.57 88.73L111.74 82.19L121.23 75.92L131.01 69.94L141.08 64.27L151.44 58.9L162.06 53.84L172.94 49.11L184.07 44.7L195.44 40.64L207.04 36.93L218.85 33.57L230.88 30.57L243.1 27.95L255.51 25.71L268.09 23.86L280.85 22.4L293.75 21.35L306.81 20.72L320 20.5L333.19 20.72L346.24 21.35L346.24 21.35L359.15 22.4ZM451.66 201.67L450.99 201.74L450.32 201.84L449.67 201.96L449.02 202.1L448.39 202.28L447.76 202.47L447.14 202.69L446.54 202.93L445.95 203.2L445.37 203.49L444.8 203.8L444.25 204.13L443.71 204.48L443.18 204.85L442.67 205.24L442.17 205.65L441.7 206.07L441.23 206.52L440.79 206.98L440.36 207.46L439.95 207.95L439.56 208.47L439.19 208.99L438.84 209.53L438.51 210.08L438.2 210.65L437.91 211.23L437.65 211.83L437.41 212.43L437.19 213.04L436.99 213.67L436.82 214.31L436.67 214.95L436.55 215.61L436.46 216.27L436.39 216.94L436.35 217.62L436.33 218.31L436.33 342.08L436.35 342.77L436.39 343.45L436.46 344.12L436.55 344.78L436.67 345.44L436.82 346.08L436.99 346.72L437.19 347.35L437.41 347.96L437.65 348.57L437.91 349.16L438.2 349.74L438.51 350.31L438.84 350.86L439.19 351.4L439.56 351.93L439.95 352.44L440.36 352.93L440.79 353.41L441.23 353.87L441.7 354.32L442.17 354.75L442.67 355.15L443.18 355.54L443.71 355.92L444.25 356.27L444.8 356.6L445.37 356.91L445.95 357.19L446.54 357.46L447.14 357.7L447.76 357.92L448.39 358.12L449.02 358.29L449.67 358.44L450.32 358.56L450.99 358.65L451.66 358.72L452.34 358.76L453.02 358.78L517.56 358.78L518.25 358.76L518.92 358.72L519.59 358.65L520.26 358.56L520.91 358.44L521.56 358.29L522.19 358.12L522.82 357.92L523.44 357.7L524.04 357.46L524.63 357.19L525.21 356.91L525.78 356.6L526.34 356.27L526.88 355.92L527.4 355.54L527.91 355.15L528.41 354.75L528.89 354.32L529.35 353.87L529.79 353.41L530.22 352.93L530.63 352.44L531.02 351.93L531.39 351.4L531.74 350.86L532.07 350.31L532.38 349.74L532.67 349.16L532.93 348.57L533.18 347.96L533.4 347.35L533.59 346.72L533.76 346.08L533.91 345.44L534.03 344.78L534.13 344.12L534.2 343.45L534.24 342.77L534.25 342.08L534.25 340.69L534.24 340.01L534.2 339.33L534.13 338.66L534.03 337.99L533.91 337.34L533.76 336.69L533.59 336.06L533.4 335.43L533.18 334.82L532.93 334.21L532.67 333.62L532.38 333.04L532.07 332.47L531.74 331.92L531.39 331.38L531.02 330.85L530.63 330.34L530.22 329.85L529.79 329.37L529.35 328.91L528.89 328.46L528.41 328.03L527.91 327.62L527.4 327.23L526.88 326.86L526.34 326.51L525.78 326.18L525.21 325.87L524.63 325.59L524.04 325.32L523.44 325.08L522.82 324.86L522.19 324.66L521.56 324.49L520.91 324.34L520.26 324.22L519.59 324.13L518.92 324.06L518.25 324.02L517.56 324L471.1 324L471.1 297.58L517.56 297.58L518.25 297.56L518.92 297.52L519.59 297.45L520.26 297.36L520.91 297.24L521.56 297.09L522.19 296.92L522.82 296.73L523.44 296.51L524.04 296.26L524.63 296L525.21 295.71L525.78 295.4L526.34 295.07L526.88 294.72L527.4 294.35L527.91 293.96L528.41 293.55L528.89 293.12L529.35 292.68L529.79 292.22L530.22 291.74L530.63 291.24L531.02 290.73L531.39 290.21L531.74 289.67L532.07 289.11L532.38 288.55L532.67 287.97L532.93 287.37L533.18 286.77L533.4 286.15L533.59 285.53L533.76 284.89L533.91 284.25L534.03 283.59L534.13 282.93L534.2 282.26L534.24 281.58L534.25 280.89L534.25 279.5L534.24 278.82L534.2 278.14L534.13 277.47L534.03 276.8L533.91 276.15L533.76 275.5L533.59 274.87L533.4 274.24L533.18 273.63L532.93 273.02L532.67 272.43L532.38 271.85L532.07 271.28L531.74 270.73L531.39 270.19L531.02 269.66L530.63 269.15L530.22 268.66L529.79 268.18L529.35 267.71L528.89 267.27L528.41 266.84L527.91 266.43L527.4 266.04L526.88 265.67L526.34 265.32L525.78 264.99L525.21 264.68L524.63 264.39L524.04 264.13L523.44 263.89L522.82 263.67L522.19 263.47L521.56 263.3L520.91 263.15L520.26 263.03L519.59 262.94L518.92 262.87L518.25 262.83L517.56 262.81L471.1 262.81L471.1 236.39L517.56 236.39L518.25 236.37L518.92 236.33L519.59 236.26L520.26 236.17L520.91 236.05L521.56 235.9L522.19 235.73L522.82 235.53L523.44 235.31L524.04 235.07L524.63 234.8L525.21 234.52L525.78 234.21L526.34 233.88L526.88 233.53L527.4 233.16L527.91 232.77L528.41 232.36L528.89 231.93L529.35 231.49L529.79 231.02L530.22 230.54L530.63 230.05L531.02 229.54L531.39 229.01L531.74 228.47L532.07 227.92L532.38 227.35L532.67 226.77L532.93 226.18L533.18 225.57L533.4 224.96L533.59 224.33L533.76 223.7L533.91 223.05L534.03 222.4L534.13 221.73L534.2 221.06L534.24 220.38L534.25 219.7L534.25 218.31L534.24 217.62L534.2 216.94L534.13 216.27L534.03 215.61L533.91 214.95L533.76 214.31L533.59 213.67L533.4 213.04L533.18 212.43L532.93 211.83L532.67 211.23L532.38 210.65L532.07 210.08L531.74 209.53L531.39 208.99L531.02 208.47L530.63 207.95L530.22 207.46L529.79 206.98L529.35 206.52L528.89 206.07L528.41 205.65L527.91 205.24L527.4 204.85L526.88 204.48L526.34 204.13L525.78 203.8L525.21 203.49L524.63 203.2L524.04 202.93L523.44 202.69L522.82 202.47L522.19 202.28L521.56 202.1L520.91 201.96L520.26 201.84L519.59 201.74L518.92 201.67L518.25 201.63L517.56 201.62L453.02 201.62L452.34 201.63L452.34 201.63L451.66 201.67ZM302.62 201.38L301.95 201.45L301.29 201.54L300.63 201.66L299.99 201.81L299.35 201.98L298.73 202.18L298.11 202.4L297.51 202.64L296.91 202.91L296.33 203.19L295.77 203.5L295.21 203.83L294.67 204.18L294.15 204.55L293.63 204.94L293.14 205.35L292.66 205.78L292.2 206.23L291.75 206.69L291.33 207.17L290.92 207.66L290.53 208.17L290.16 208.7L289.81 209.24L289.47 209.79L289.17 210.36L288.88 210.94L288.61 211.53L288.37 212.14L288.15 212.75L287.95 213.38L287.78 214.01L287.64 214.66L287.51 215.31L287.42 215.98L287.35 216.65L287.31 217.33L287.3 218.01L287.3 341.79L287.31 342.48L287.35 343.16L287.42 343.83L287.51 344.49L287.64 345.14L287.78 345.79L287.95 346.43L288.15 347.05L288.37 347.67L288.61 348.27L288.88 348.87L289.16 349.45L289.47 350.01L289.8 350.57L290.16 351.11L290.53 351.63L290.92 352.14L291.33 352.64L291.75 353.12L292.2 353.58L292.66 354.03L293.14 354.45L293.63 354.86L294.14 355.25L294.67 355.62L295.21 355.97L295.76 356.3L296.33 356.61L296.91 356.9L297.5 357.17L298.11 357.41L298.72 357.63L299.35 357.82L299.99 358L300.63 358.14L301.29 358.26L301.95 358.36L302.62 358.43L303.3 358.47L303.99 358.48L305.38 358.48L306.06 358.47L306.74 358.43L307.41 358.36L308.08 358.26L308.73 358.14L309.38 358L310.01 357.82L310.64 357.63L311.26 357.41L311.86 357.17L312.45 356.9L313.03 356.61L313.6 356.3L314.15 355.97L314.69 355.62L315.22 355.25L315.73 354.86L316.22 354.45L316.7 354.03L317.17 353.58L317.61 353.12L318.04 352.64L318.45 352.14L318.84 351.63L319.21 351.11L319.56 350.57L319.89 350.01L320.2 349.45L320.49 348.87L320.75 348.27L320.99 347.67L321.21 347.05L321.41 346.43L321.58 345.79L321.73 345.14L321.85 344.49L321.94 343.83L322.01 343.16L322.05 342.48L322.07 341.79L322.07 267.77L382.82 350.76L382.85 350.8L382.88 350.84L382.91 350.89L382.94 350.93L382.97 350.97L383 351.02L383.02 351.06L383.05 351.1L383.08 351.15L383.11 351.19L383.14 351.23L383.17 351.27L383.2 351.32L383.23 351.36L383.26 351.4L383.29 351.44L383.32 351.49L383.35 351.53L383.38 351.57L383.41 351.61L383.44 351.65L383.47 351.7L383.5 351.74L383.53 351.78L383.57 351.82L383.6 351.86L383.63 351.9L383.66 351.94L383.69 351.98L383.72 352.03L383.76 352.07L383.79 352.11L383.82 352.15L383.85 352.19L383.88 352.23L383.92 352.27L383.95 352.31L383.98 352.35L384.02 352.39L384.05 352.43L384.05 352.43L384.18 352.6L384.31 352.77L384.44 352.93L384.57 353.1L384.7 353.25L384.84 353.41L384.98 353.56L385.12 353.71L385.27 353.86L385.41 354.01L385.56 354.15L385.71 354.29L385.86 354.43L386.01 354.57L386.17 354.7L386.33 354.83L386.48 354.96L386.65 355.08L386.81 355.2L386.97 355.33L387.14 355.44L387.3 355.56L387.47 355.67L387.64 355.78L387.81 355.89L387.99 356L388.16 356.1L388.34 356.2L388.52 356.3L388.69 356.39L388.87 356.49L389.06 356.58L389.24 356.67L389.42 356.75L389.61 356.84L389.79 356.92L389.98 357L390.17 357.08L390.36 357.15L390.55 357.22L390.7 357.28L390.84 357.34L390.99 357.4L391.14 357.46L391.29 357.51L391.44 357.56L391.59 357.62L391.74 357.67L391.89 357.71L392.05 357.76L392.2 357.81L392.36 357.85L392.51 357.9L392.67 357.94L392.82 357.98L392.98 358.02L393.13 358.05L393.29 358.09L393.45 358.12L393.61 358.16L393.77 358.19L393.93 358.22L394.09 358.24L394.25 358.27L394.41 358.3L394.57 358.32L394.73 358.34L394.89 358.36L395.06 358.38L395.22 358.4L395.39 358.41L395.55 358.43L395.71 358.44L395.88 358.45L396.05 358.46L396.21 358.47L396.38 358.47L396.54 358.48L396.71 358.48L396.88 358.48L398.27 358.48L398.48 358.48L398.68 358.48L398.88 358.47L399.09 358.46L399.29 358.45L399.49 358.44L399.69 358.42L399.9 358.4L400.1 358.38L400.3 358.36L400.5 358.33L400.7 358.3L400.9 358.27L401.1 358.24L401.3 358.2L401.5 358.16L401.7 358.12L401.9 358.08L402.09 358.04L402.29 357.99L402.49 357.94L402.68 357.88L402.88 357.83L403.07 357.77L403.26 357.71L403.46 357.65L403.65 357.58L403.84 357.52L404.03 357.45L404.22 357.38L404.41 357.3L404.6 357.23L404.78 357.15L404.97 357.07L405.16 356.98L405.34 356.9L405.52 356.81L405.71 356.72L405.89 356.63L406.07 356.53L406.19 356.47L406.32 356.41L406.44 356.35L406.56 356.29L406.68 356.22L406.8 356.16L406.92 356.09L407.04 356.03L407.15 355.96L407.27 355.89L407.38 355.83L407.5 355.76L407.61 355.69L407.72 355.62L407.83 355.54L407.94 355.47L408.04 355.4L408.15 355.32L408.25 355.25L408.36 355.17L408.46 355.1L408.56 355.02L408.66 354.94L408.76 354.86L408.85 354.78L408.95 354.7L409.04 354.62L409.14 354.54L409.23 354.46L409.32 354.38L409.4 354.29L409.49 354.21L409.58 354.12L409.66 354.04L409.74 353.95L409.83 353.86L409.9 353.77L409.98 353.68L410.06 353.6L410.13 353.5L410.14 353.5L410.37 353.26L410.59 353.02L410.81 352.78L411.02 352.53L411.23 352.28L411.44 352.03L411.63 351.77L411.83 351.51L412.01 351.24L412.2 350.97L412.37 350.7L412.54 350.43L412.71 350.15L412.87 349.87L413.02 349.58L413.17 349.3L413.31 349.01L413.45 348.71L413.58 348.42L413.71 348.12L413.83 347.82L413.94 347.52L414.05 347.22L414.15 346.91L414.25 346.6L414.34 346.29L414.43 345.98L414.5 345.67L414.58 345.35L414.64 345.03L414.7 344.71L414.76 344.39L414.81 344.07L414.85 343.75L414.88 343.43L414.91 343.1L414.94 342.78L414.95 342.45L414.96 342.12L414.97 341.79L414.97 218.01L414.95 217.33L414.91 216.65L414.84 215.98L414.75 215.31L414.62 214.66L414.48 214.01L414.31 213.38L414.11 212.75L413.89 212.14L413.65 211.53L413.38 210.94L413.1 210.36L412.79 209.79L412.46 209.24L412.11 208.7L411.73 208.17L411.34 207.66L410.94 207.17L410.51 206.69L410.06 206.23L409.6 205.78L409.12 205.35L408.63 204.94L408.12 204.55L407.59 204.18L407.05 203.83L406.5 203.5L405.93 203.19L405.35 202.91L404.76 202.64L404.15 202.4L403.54 202.18L402.91 201.98L402.27 201.81L401.63 201.66L400.97 201.54L400.31 201.45L399.64 201.38L398.96 201.34L398.27 201.32L396.88 201.32L396.2 201.34L395.52 201.38L394.85 201.45L394.18 201.54L393.53 201.66L392.88 201.81L392.25 201.98L391.62 202.18L391.01 202.4L390.4 202.64L389.81 202.91L389.23 203.19L388.66 203.5L388.11 203.83L387.57 204.18L387.04 204.55L386.53 204.94L386.04 205.35L385.56 205.78L385.09 206.23L384.65 206.69L384.22 207.17L383.81 207.66L383.42 208.17L383.05 208.7L382.7 209.24L382.37 209.79L382.06 210.36L381.77 210.94L381.51 211.53L381.27 212.14L381.05 212.75L380.85 213.38L380.68 214.01L380.53 214.66L380.41 215.31L380.32 215.98L380.25 216.65L380.21 217.33L380.19 218.01L380.19 290.67L319.87 209.76L319.65 209.39L319.42 209.02L319.18 208.66L318.93 208.3L318.67 207.96L318.41 207.61L318.13 207.28L317.85 206.95L317.56 206.63L317.26 206.32L316.95 206.02L316.64 205.72L316.32 205.43L315.99 205.15L315.65 204.88L315.3 204.62L314.95 204.36L314.6 204.12L314.23 203.88L313.86 203.66L313.49 203.44L313.1 203.23L312.71 203.03L312.32 202.85L311.92 202.67L311.52 202.5L311.11 202.34L310.69 202.2L310.27 202.06L309.85 201.93L309.42 201.82L308.98 201.72L308.55 201.63L308.1 201.55L307.66 201.48L307.21 201.42L306.76 201.38L306.3 201.35L305.84 201.33L305.38 201.32L303.99 201.32L303.3 201.34L303.3 201.34L302.62 201.38ZM244.36 201.38L243.69 201.45L243.02 201.54L242.37 201.66L241.72 201.81L241.09 201.98L240.46 202.18L239.84 202.4L239.24 202.64L238.65 202.9L238.07 203.19L237.5 203.5L236.95 203.83L236.41 204.18L235.88 204.55L235.37 204.94L234.87 205.35L234.39 205.78L233.93 206.22L233.49 206.69L233.06 207.16L232.65 207.66L232.26 208.17L231.89 208.7L231.54 209.24L231.21 209.79L230.9 210.36L230.61 210.94L230.35 211.53L230.1 212.13L229.88 212.75L229.69 213.38L229.52 214.01L229.37 214.66L229.25 215.31L229.15 215.98L229.09 216.65L229.04 217.32L229.03 218.01L229.03 341.79L229.04 342.47L229.09 343.15L229.15 343.82L229.25 344.49L229.37 345.14L229.52 345.79L229.69 346.42L229.88 347.05L230.1 347.67L230.35 348.27L230.61 348.86L230.9 349.44L231.21 350.01L231.54 350.56L231.89 351.1L232.26 351.63L232.65 352.14L233.06 352.64L233.49 353.12L233.93 353.58L234.39 354.02L234.87 354.45L235.37 354.86L235.88 355.25L236.41 355.62L236.95 355.97L237.5 356.3L238.07 356.61L238.65 356.9L239.24 357.16L239.84 357.41L240.46 357.63L241.09 357.82L241.72 357.99L242.37 358.14L243.02 358.26L243.69 358.36L244.36 358.43L245.04 358.47L245.72 358.48L247.11 358.48L247.8 358.47L248.48 358.43L249.15 358.36L249.81 358.26L250.47 358.14L251.11 357.99L251.75 357.82L252.37 357.63L252.99 357.41L253.59 357.16L254.19 356.9L254.77 356.61L255.33 356.3L255.89 355.97L256.43 355.62L256.95 355.25L257.46 354.86L257.96 354.45L258.44 354.02L258.9 353.58L259.35 353.12L259.77 352.64L260.18 352.14L260.57 351.63L260.94 351.1L261.29 350.56L261.62 350.01L261.93 349.44L262.22 348.86L262.49 348.27L262.73 347.67L262.95 347.05L263.14 346.42L263.31 345.79L263.46 345.14L263.58 344.49L263.68 343.82L263.75 343.15L263.79 342.47L263.8 341.79L263.8 218.01L263.79 217.32L263.75 216.65L263.68 215.98L263.58 215.31L263.46 214.66L263.31 214.01L263.14 213.38L262.95 212.75L262.73 212.13L262.49 211.53L262.22 210.94L261.93 210.36L261.62 209.79L261.29 209.24L260.94 208.7L260.57 208.17L260.18 207.66L259.77 207.16L259.35 206.69L258.9 206.22L258.44 205.78L257.96 205.35L257.46 204.94L256.95 204.55L256.43 204.18L255.89 203.83L255.33 203.5L254.77 203.19L254.19 202.9L253.59 202.64L252.99 202.4L252.37 202.18L251.75 201.98L251.11 201.81L250.47 201.66L249.81 201.54L249.15 201.45L248.48 201.38L247.8 201.34L247.11 201.32L245.72 201.32L245.04 201.34L245.04 201.34L244.36 201.38ZM132.97 201.37L132.3 201.44L131.64 201.54L130.98 201.66L130.33 201.81L129.7 201.98L129.07 202.17L128.46 202.39L127.85 202.64L127.26 202.9L126.68 203.19L126.11 203.5L125.56 203.83L125.02 204.18L124.49 204.55L123.98 204.94L123.49 205.35L123.01 205.78L122.55 206.22L122.1 206.68L121.67 207.16L121.27 207.66L120.88 208.17L120.5 208.69L120.15 209.23L119.82 209.79L119.51 210.35L119.23 210.93L118.96 211.53L118.72 212.13L118.5 212.75L118.3 213.37L118.13 214.01L117.99 214.65L117.86 215.31L117.77 215.97L117.7 216.64L117.66 217.32L117.64 218.01L117.64 341.79L117.66 342.47L117.7 343.15L117.77 343.82L117.86 344.49L117.99 345.14L118.13 345.79L118.3 346.42L118.5 347.05L118.72 347.66L118.96 348.27L119.23 348.86L119.51 349.44L119.82 350.01L120.15 350.56L120.5 351.1L120.88 351.63L121.27 352.14L121.67 352.64L122.1 353.12L122.55 353.58L123.01 354.02L123.49 354.45L123.98 354.86L124.49 355.25L125.02 355.62L125.56 355.97L126.11 356.3L126.68 356.61L127.26 356.9L127.85 357.16L128.46 357.41L129.07 357.63L129.7 357.82L130.33 357.99L130.98 358.14L131.64 358.26L132.3 358.36L132.97 358.43L133.65 358.47L134.33 358.48L198.87 358.48L199.56 358.47L200.24 358.43L200.91 358.36L201.57 358.26L202.23 358.14L202.87 357.99L203.51 357.82L204.13 357.63L204.75 357.41L205.35 357.16L205.95 356.9L206.53 356.61L207.09 356.3L207.65 355.97L208.19 355.62L208.71 355.25L209.22 354.86L209.72 354.45L210.2 354.02L210.66 353.58L211.1 353.12L211.53 352.64L211.94 352.14L212.33 351.63L212.7 351.1L213.05 350.56L213.38 350.01L213.69 349.44L213.98 348.86L214.24 348.27L214.49 347.67L214.71 347.05L214.9 346.42L215.07 345.79L215.22 345.14L215.34 344.49L215.44 343.82L215.51 343.15L215.55 342.47L215.56 341.79L215.56 340.4L215.55 339.71L215.51 339.03L215.44 338.36L215.34 337.7L215.22 337.04L215.07 336.4L214.9 335.76L214.71 335.14L214.49 334.52L214.24 333.92L213.98 333.32L213.69 332.74L213.38 332.18L213.05 331.62L212.7 331.08L212.33 330.56L211.94 330.05L211.53 329.55L211.1 329.07L210.66 328.61L210.2 328.17L209.72 327.74L209.22 327.33L208.71 326.94L208.19 326.57L207.65 326.22L207.09 325.89L206.53 325.58L205.95 325.29L205.35 325.02L204.75 324.78L204.13 324.56L203.51 324.37L202.87 324.2L202.23 324.05L201.57 323.93L200.91 323.83L200.24 323.76L199.56 323.72L198.87 323.71L152.41 323.71L152.41 218.01L152.4 217.32L152.36 216.64L152.29 215.97L152.2 215.31L152.07 214.65L151.93 214.01L151.76 213.37L151.56 212.75L151.34 212.13L151.1 211.53L150.83 210.93L150.54 210.35L150.24 209.79L149.91 209.23L149.55 208.69L149.18 208.17L148.79 207.66L148.38 207.16L147.96 206.68L147.51 206.22L147.05 205.78L146.57 205.35L146.08 204.94L145.57 204.55L145.04 204.18L144.5 203.83L143.95 203.5L143.38 203.19L142.8 202.9L142.21 202.64L141.6 202.39L140.99 202.17L140.36 201.98L139.72 201.81L139.08 201.66L138.42 201.54L137.76 201.44L137.09 201.37L136.41 201.33L135.73 201.32L134.33 201.32L133.65 201.33L133.65 201.33L132.97 201.37Z" id="anFrFjbpI"/></defs><g><g><g><use xlink:href="#anFrFjbpI" opacity="1" fill="#ffffff" fill-opacity="1"/><g><use xlink:href="#anFrFjbpI" opacity="1" fill-opacity="0" stroke="#000000" stroke-width="1" stroke-opacity="0"/></g></g></g></g></svg>
                    <?php } ?>
                </span>
             <?php } ?>
            <?php if ( esc_attr($options['social_display']) != 'icon' ) { ?>
                <span class="oxy-share-name"><?php echo $line_text; ?></span>
           <?php } ?>
        </a> <?php

        }


         // Print
        
         if ( $options['print_display'] !== 'hide' ) {
            
            ?> <button class="oxy-share-button print" target="_blank" aria-label="<?php echo $print_text; ?>" data-print-exact="<?php echo $print_color_adjust; ?>" data-print-selector="<?php echo $print_selector; ?>" >
           <?php if ( esc_attr($options['social_display']) != 'text' ) { ?>
               <span class="oxy-share-icon"><svg id="print<?php echo esc_attr($options['selector']); ?>-share-icon"><use xlink:href="#<?php echo $print_icon; ?>"></use></svg></span>
            <?php } ?>
           <?php if ( esc_attr($options['social_display']) != 'icon' ) { ?>
               <span class="oxy-share-name"><?php echo $print_text; ?></span>
          <?php } ?>
           </button> <?php
            
       }

       echo '<div class="oxy-social-share-buttons_data" data-hide-print="' . $remove_share_buttons . '" data-behaviour="'. $behaviour .'"></div>';

        // Only load each JS file once
        if ($this->js_added !== true && ('popup' === $behaviour)) {
            if (!defined('OXY_ELEMENTS_API_AJAX') || !OXY_ELEMENTS_API_AJAX) {
                add_action( 'wp_footer', array( $this, 'output_js' ) );
            }
        }

        if ($this->print_js_added !== true && ( $options['print_display'] !== 'hide') ) {
            if (!defined('OXY_ELEMENTS_API_AJAX') || !OXY_ELEMENTS_API_AJAX) {
                add_action( 'wp_footer', array( $this, 'output_print_js' ) );
            }
        }


       
        
       
    }

    function class_names() {
        
        $output = '';
        
        return $output;
    }


    function controls() {
        
        
        /**
         * Display
         */
        $this->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Output',
                'slug' => 'social_display'
            )
        )->setValue(array( "text" => "Text", "icon" => "Icon", "both" => "Both" ))
         ->setDefaultValue('both')->rebuildElementOnChange();
        
        
        /**
         * Layout
         */
        $this->addStyleControl(
            array( 
                "property" => 'font-size',
                "default" => '12',
            )
        );
        
        
        $this->addOptionControl(
            array(
                "type" => "dropdown",
                "name" => "Share URL",
                "slug" => "social_urls",
                "default" => 'current',
            )
        )->setValue(
           array( 
                "current" => "Current page", 
                "home" => "Home page",
                "custom" => "Custom URL",
               
           )
       );
        
        
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Custom URL'),
                "slug" => 'custom_share_url',
                "default" => 'https://mycustomurl.com/',
                "condition" => 'social_urls=custom',
                "base64" => true
            )
        );


        $this->addOptionControl(
            array(
                "type" => "dropdown",
                "name" => __("Behaviour"),
                "slug" => "behaviour",
                "default" => 'tab',
            )
        )->setValue(
           array( 
                "tab" => "New tab", 
                "popup" => "New window popup",
           )
       );
        
        
        
       
        
        $display_section = $this->addControlSection("display_section", __("Networks"), "assets/icon.png", $this);
        
            
        /**
          * Twitter
          */
        $twitter_section = $display_section->addControlSection("twitter_section", __("Twitter"), "assets/icon.png", $this);

         $twitter_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Twitter Button',
                'slug' => 'twitter_display')

            )->setValue(array( "display" => "Display", "hide" => "Remove" ))
             ->setDefaultValue('display')->rebuildElementOnChange();

        $twitter_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Button Text'),
                "slug" => 'twitter_text',
                "default" => 'X',
                "condition" => 'twitter_display=display',
                "base64" => true
            )
        )->rebuildElementOnChange();

        $twitter_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Twitter Handle'),
                "slug" => 'twitter_handle',
                "condition" => 'twitter_display=display',
                "base64" => true
            )
        );

        $twitter_section->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Icon'),
                "slug" => 'twitter_icon',
                "value" => 'FontAwesomeicon-x-twitter',
                "condition" => 'twitter_display=display',
            )
        )->rebuildElementOnChange();
        
        $twitter_section->addStyleControl(
            array( 
                "name" => __('Order (flex)'),
                "selector" => '.oxy-share-button.twitter',
                "property" => 'order',
                "control_type" => "textfield",
                "default" => '',
            )
        );
        
        
        
        /**
          * Facebook
          */
        
        $facebook_section = $display_section->addControlSection("facebook_section", __("Facebook"), "assets/icon.png", $this);
        
         $facebook_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'facebook Button',
                'slug' => 'facebook_display')

            )->setValue(array( "display" => "Display", "hide" => "Remove" ))
             ->setDefaultValue('display')->rebuildElementOnChange();

        $facebook_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Button Text'),
                "slug" => 'facebook_text',
                "default" => 'Facebook',
                "condition" => 'facebook_display=display',
                "base64" => true
            )
        )->rebuildElementOnChange();

        $facebook_section->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Icon'),
                "slug" => 'facebook_icon',
                "value" => 'FontAwesomeicon-facebook',
                "condition" => 'facebook_display=display',
            )
        )->rebuildElementOnChange();
        
        $facebook_section->addStyleControl(
            array( 
                "name" => __('Order (flex)'),
                "selector" => '.oxy-share-button.facebook',
                "property" => 'order',
                "control_type" => "textfield",
                "default" => '',
            )
        );
        
        
        /**
          * linkedin
          */
        $linkedin_section = $display_section->addControlSection("linkedin_section", __("Linked In"), "assets/icon.png", $this);
        
         $linkedin_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'linkedin Button',
                'slug' => 'linkedin_display')

            )->setValue(array( "display" => "Display", "hide" => "Remove" ))
             ->setDefaultValue('display')->rebuildElementOnChange();

        $linkedin_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Button Text'),
                "slug" => 'linkedin_text',
                "default" => 'Linkedin',
                "condition" => 'linkedin_display=display',
                "base64" => true
            )
        )->rebuildElementOnChange();

        $linkedin_section->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Icon'),
                "slug" => 'linkedin_icon',
                "value" => 'FontAwesomeicon-linkedin',
                "condition" => 'linkedin_display=display',
            )
        )->rebuildElementOnChange();    
        
        $linkedin_section->addStyleControl(
            array( 
                "name" => __('Order (flex)'),
                "selector" => '.oxy-share-button.linkedin',
                "property" => 'order',
                "control_type" => "textfield",
                "default" => '',
            )
        );
            
        
        
         /**
          * email
          */
        $email_section = $display_section->addControlSection("email_section", __("Email"), "assets/icon.png", $this);
        
         $email_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Email Button',
                'slug' => 'email_display')

            )->setValue(array( "display" => "Display", "hide" => "Remove" ))
             ->setDefaultValue('display')->rebuildElementOnChange();

        $email_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Button Text'),
                "slug" => 'email_text',
                "default" => 'Email',
                "condition" => 'email_display=display',
                "base64" => true
            )
        )->rebuildElementOnChange();
        
        $email_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Include post title in email subject',
                'slug' => 'maybe_email_post_title')

            )->setValue(array( "true" => "True", "false" => "False" ))
             ->setDefaultValue('true')->rebuildElementOnChange();
        
        $email_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Email Subject'),
                "slug" => 'email_subject',
                "default" => 'Check out this post -',
                "condition" => 'email_display=display',
                "base64" => true
            )
        )->rebuildElementOnChange();
        
        $email_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Email Body'),
                "slug" => 'email_body',
                "default" => 'Here is the link -',
                "condition" => 'email_display=display',
                "base64" => true
            )
        )->rebuildElementOnChange();

        $email_section->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Icon'),
                "slug" => 'email_icon',
                "value" => 'FontAwesomeicon-envelope',
                "condition" => 'email_display=display',
            )
        )->rebuildElementOnChange();        
        
        $email_section->addStyleControl(
            array( 
                "name" => __('Order (flex)'),
                "selector" => '.oxy-share-button.email',
                "property" => 'order',
                "control_type" => "textfield",
                "default" => '',
            )
        );

        $email_button = '.oxy-share-button.email';

            $email_section->addStyleControls(
                array(
                    array(
                        "name" => __('Background Color'),
                        "selector" => $email_button,
                        "property" => 'background-color',
                        "default" => '#111',
                        "condition" => 'brand=brand'
                    ),
                    array(
                        "name" => __('Text / Icon Color'),
                        "selector" => $email_button,
                        "property" => 'color',
                        "default" => '#fff',
                        "condition" => 'brand=brand'
                    )
                )
            );
            
            
            $email_section->addStyleControls(
                array(
                    array(
                        "name" => __('Background Color (hover)'),
                        "selector" => $email_button.":hover",
                        "property" => 'background-color',
                        "condition" => 'brand=brand'
                    ),
                    array(
                        "name" => __('Text / Icon Color (hover)'),
                        "selector" => $email_button.":hover",
                        "property" => 'color',
                        "condition" => 'brand=brand'
                    )
                )
            );
        
        
         /**
          * WhatsApp
          */
        
        $whatsapp_section = $display_section->addControlSection("whatsapp_section", __("WhatsApp"), "assets/icon.png", $this);
        
        
        $whatsapp_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'WhatsApp Button',
                'slug' => 'whatsapp_display')

            )->setValue(array( "display" => "Display", "hide" => "Remove" ))
             ->setDefaultValue('hide')->rebuildElementOnChange();

        $whatsapp_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('WhatsApp Text'),
                "slug" => 'whatsapp_text',
                "default" => 'WhatsApp',
                "condition" => 'whatsapp_display=display',
                "base64" => true
            )
        )->rebuildElementOnChange();
        
        
        $whatsapp_section->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Icon'),
                "slug" => 'whatsapp_icon',
                "value" => 'FontAwesomeicon-whatsapp',
                "condition" => 'whatsapp_display=display',
            )
        )->rebuildElementOnChange();  
        
        $whatsapp_section->addStyleControl(
            array( 
                "name" => __('Order (flex)'),
                "selector" => '.oxy-share-button.whatsapp',
                "property" => 'order',
                "control_type" => "textfield",
                "default" => '',
            )
        );
        
        
        /**
          * Telegram
          */
        
        $telegram_section = $display_section->addControlSection("telegram_section", __("Telegram"), "assets/icon.png", $this);
        
        
        $telegram_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Telegram Button',
                'slug' => 'telegram_display')

            )->setValue(array( "display" => "Display", "hide" => "Remove" ))
             ->setDefaultValue('hide')->rebuildElementOnChange();

        $telegram_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Telegram Text'),
                "slug" => 'telegram_text',
                "default" => 'Telegram',
                "condition" => 'telegram_display=display',
                "base64" => true
            )
        )->rebuildElementOnChange();
        
        
        $telegram_section->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Icon'),
                "slug" => 'telegram_icon',
                "value" => 'FontAwesomeicon-paper-plane',
                "condition" => 'telegram_display=display',
            )
        )->rebuildElementOnChange();  
        
        $telegram_section->addStyleControl(
            array( 
                "name" => __('Order (flex)'),
                "selector" => '.oxy-share-button.telegram',
                "property" => 'order',
                "control_type" => "textfield",
                "default" => '',
            )
        );
        
        
        /**
          * Pinterest
          */
        
        $pinterest_section = $display_section->addControlSection("pinterest_section", __("Pinterest"), "assets/icon.png", $this);
        
        
        $pinterest_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Pinterest Button',
                'slug' => 'pinterest_display')

            )->setValue(array( "display" => "Display", "hide" => "Remove" ))
             ->setDefaultValue('hide')->rebuildElementOnChange();

        $pinterest_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Pinterest Text'),
                "slug" => 'pinterest_text',
                "default" => 'Pinterest',
                "condition" => 'pinterest_display=display',
                "base64" => true
            )
        )->rebuildElementOnChange();
        
        
        $pinterest_section->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Icon'),
                "slug" => 'pinterest_icon',
                "value" => 'FontAwesomeicon-pinterest-p',
                "condition" => 'pinterest_display=display',
            )
        )->rebuildElementOnChange();  
        
        $pinterest_section->addStyleControl(
            array( 
                "name" => __('Order (flex)'),
                "selector" => '.oxy-share-button.pinterest',
                "property" => 'order',
                "control_type" => "textfield",
                "default" => '',
            )
        );
        
        
        
        /**
          * Xing
          */
        
        $xing_section = $display_section->addControlSection("xing_section", __("Xing"), "assets/icon.png", $this);
        
        
        $xing_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Xing Button',
                'slug' => 'xing_display')

            )->setValue(array( "display" => "Display", "hide" => "Remove" ))
             ->setDefaultValue('hide')->rebuildElementOnChange();

        $xing_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Xing Text'),
                "slug" => 'xing_text',
                "default" => 'Xing',
                "condition" => 'xing_display=display',
                "base64" => true
            )
        )->rebuildElementOnChange();
        
        
        $xing_section->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Icon'),
                "slug" => 'xing_icon',
                "value" => 'FontAwesomeicon-xing',
                "condition" => 'xing_display=display',
            )
        )->rebuildElementOnChange();  
        
        $xing_section->addStyleControl(
            array( 
                "name" => __('Order (flex)'),
                "selector" => '.oxy-share-button.xing',
                "property" => 'order',
                "control_type" => "textfield",
                "default" => '',
            )
        );


         /**
          * Line
          */
        
          $line_section = $display_section->addControlSection("line_section", __("Line"), "assets/icon.png", $this);
        
        
          $line_section->addOptionControl(
              array(
                  'type' => 'buttons-list',
                  'name' => 'Line Button',
                  'slug' => 'line_display')
  
              )->setValue(array( "display" => "Display", "hide" => "Remove" ))
               ->setDefaultValue('hide')->rebuildElementOnChange();
  
          $line_section->addOptionControl(
              array(
                  "type" => 'textfield',
                  "name" => __('Button text'),
                  "slug" => 'line_text',
                  "default" => 'Line',
                  "condition" => 'line_display=display',
                  "base64" => true
              )
          )->rebuildElementOnChange();

          $line_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Message text'),
                "slug" => 'line_msg_text',
                "default" => '',
                "condition" => 'line_display=display',
                "base64" => true
            )
          );
          
          $line_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Icon source'),
                'slug' => 'line_icon_source')

            )->setValue(array( "custom" => "Custom", "logo" => "Line logo" ))
             ->setDefaultValue('logo')->rebuildElementOnChange();

          
          $line_section->addOptionControl(
              array(
                  "type" => 'icon_finder',
                  "name" => __('Icon'),
                  "slug" => 'line_icon',
                  "value" => 'FontAwesomeicon-commenting',
                  "condition" => 'line_display=display&&line_icon_source=custom',
              )
          )->rebuildElementOnChange();  
          
          $line_section->addStyleControl(
              array( 
                  "name" => __('Order (flex)'),
                  "selector" => '.oxy-share-button.xing',
                  "property" => 'order',
                  "control_type" => "textfield",
                  "default" => '',
              )
          );


          /**
          * Print
          */
        
          $print_section = $display_section->addControlSection("print_section", __("Print"), "assets/icon.png", $this);
        
        
          $print_section->addOptionControl(
              array(
                  'type' => 'buttons-list',
                  'name' => 'Print Button',
                  'slug' => 'print_display')
  
              )->setValue(array( "display" => "Display", "hide" => "Remove" ))
               ->setDefaultValue('hide')->rebuildElementOnChange();

            $print_section->addOptionControl(
                array(
                    "type" => 'textfield',
                    "name" => __('Button text'),
                    "slug" => 'print_text',
                    "default" => 'Print',
                    "condition" => 'print_display=display',
                    "base64" => true
                )
            )->rebuildElementOnChange();

            $print_section->addOptionControl(
                array(
                    "type" => 'icon_finder',
                    "name" => __('Icon'),
                    "slug" => 'print_icon',
                    "value" => 'FontAwesomeicon-print',
                    "condition" => 'print_display=display',
                )
            )->rebuildElementOnChange();

            $print_section->addOptionControl(
                array(
                    "type" => 'textfield',
                    "name" => __('Selector of content area to print'),
                    "slug" => 'print_selector',
                    "default" => 'body',
                    "condition" => 'print_display=display',
                    "base64" => true
                )
            );

            $print_section->addOptionControl(
                array(
                    "type" => 'buttons-list',
                    "name" => __('Print color adjust'),
                    "slug" => 'print_color_adjust',
                    "default" => 'Exact',
                    "condition" => 'print_display=display',
                )
            )->setValue(array( 
                "exact" => "Exact", 
                "economy" => "Economy" 
            ));

            $print_section->addOptionControl(
                array(
                    "type" => 'buttons-list',
                    "name" => __('Remove share buttons from print'),
                    "slug" => 'remove_share_buttons',
                    "default" => 'Disable',
                    "condition" => 'print_display=display',
                )
            )->setValue(array( 
                "enable" => "Enable", 
                "disable" => "Disable" 
            ));


            $print_section->addStyleControl(
                array( 
                    "name" => __('Order (flex)'),
                    "selector" => '.oxy-share-button.print',
                    "property" => 'order',
                    "control_type" => "textfield",
                    "default" => '',
                )
            );

            $print_button = '.oxy-share-button.print';

            $print_section->addStyleControls(
                array(
                    array(
                        "name" => __('Background Color'),
                        "selector" => $print_button,
                        "property" => 'background-color',
                        "default" => '#111',
                        "condition" => 'brand=brand'
                    ),
                    array(
                        "name" => __('Text / Icon Color'),
                        "selector" => $print_button,
                        "property" => 'color',
                        "default" => '#fff',
                        "condition" => 'brand=brand'
                    )
                )
            );
            
            
            $print_section->addStyleControls(
                array(
                    array(
                        "name" => __('Background Color (hover)'),
                        "selector" => $print_button.":hover",
                        "property" => 'background-color',
                        "condition" => 'brand=brand'
                    ),
                    array(
                        "name" => __('Text / Icon Color (hover)'),
                        "selector" => $print_button.":hover",
                        "property" => 'color',
                        "condition" => 'brand=brand'
                    )
                )
            );


        
         /**
         * Layout
         */
        $layout_section = $this->addControlSection("layout_section", __("Layout / Position"), "assets/icon.png", $this);
        $layout_section->flex('', $this);
        
        $position = $layout_section->addControl("buttons-list", "position", __("Position") );
        $position->setValue( array("Fixed","Sticky","Manual") );
        $position->setDefaultValue('Manual');
        $position->setValueCSS( array(
            "Fixed"  => "
                { 
                    position: fixed;
               }
               ",
            "Sticky"  => "
               {
                    position: -webkit-sticky;  
                    position: sticky;
               }

            ",
            "Manual"  => "",
        ) );
        $position->whiteList();
        
        
        
        $layout_section->addStyleControl(
            array( 
                "property" => 'top',
                "control_type" => "measurebox",
                "condition" => "position!=Manual",
                "unit" => "px"
            )
        );
        
        $layout_section->addStyleControl(
            array( 
                "property" => 'bottom',
                "control_type" => "measurebox",
                "condition" => "position!=Manual",
                "unit" => "px"
            )
        );
        
        $layout_section->addStyleControl(
            array( 
                "property" => 'left',
                "control_type" => "measurebox",
                "condition" => "position=Fixed",
                "unit" => "px"
            )
        );
        
        $layout_section->addStyleControl(
            array( 
                "property" => 'right',
                "control_type" => "measurebox",
                "condition" => "position=Fixed",
                "unit" => "px"
            )
        );
        
        $layout_section->addStyleControl(
            array( 
                "property" => 'z-index',
                "control_type" => "measurebox",
                "condition" => "position=Fixed",
            )
        );

        
        /**
         * Button Styles
         */
        
        $button_section = $this->addControlSection("button_section", __("Button Styles"), "assets/icon.png", $this);
        $button_selector = '.oxy-share-button';
        
        $button_spacing = $button_section->addControlSection("styles_spacing", __("Layout / Spacing"), "assets/icon.png", $this);
        
        $button_spacing->flex($button_selector, $this);
            
        
        
       $button_spacing->addStyleControl(
            array( 
                //"name" => __('Top'),
                "selector" => $button_selector,
                "property" => 'margin-top',
                "control_type" => "measurebox",
                "unit" => "em",
                "default" => '.2',
            )
        );
        
        $button_spacing->addStyleControl(
            array( 
                //"name" => __('Top'),
                "selector" => $button_selector,
                "property" => 'margin-left',
                "control_type" => "measurebox",
                "unit" => "em",
                "default" => '.2',
            )
        );
        
        $button_spacing->addStyleControl(
            array( 
                //"name" => __('Top'),
                "selector" => $button_selector,
                "property" => 'margin-right',
                "control_type" => "measurebox",
                "unit" => "em",
                "default" => '.2',
            )
        );
        
        $button_spacing->addStyleControl(
            array( 
                //"name" => __('Top'),
                "selector" => $button_selector,
                "property" => 'margin-bottom',
                "control_type" => "measurebox",
                "unit" => "em",
                "default" => '.2',
            )
        );
        
        $button_section->borderSection('Borders', $button_selector,$this);
        
        $button_section->boxShadowSection('Shadows', $button_selector,$this);
        
        $button_brand = $button_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => 'Button Colours',
                'slug' => 'brand')

            );
        $button_brand->setValue(array( "brand" => "Brand", "custom" => "Custom" ));
        $button_brand->setDefaultValue('custom');
        $button_brand->setValueCSS( array(
            "brand"  => "
                .oxy-share-button.twitter { 
                    background-color: #00b6f1;
               }
               
               .oxy-share-button.twitter:hover { 
                    background-color: #20d6ff;
               }
               
               .oxy-share-button.facebook { 
                    background-color: #3b5998;
               }
               
               .oxy-share-button.facebook:hover { 
                    background-color: #5b79b8;
               }
               
               .oxy-share-button.linkedin { 
                    background-color: #007bb6;
               }
               
               .oxy-share-button.linkedin:hover { 
                    background-color: #209bd6;
               }
               
               .oxy-share-button.whatsapp { 
                    background-color: #23D366;
               }
               
               .oxy-share-button.whatsapp:hover { 
                    background-color: #3DED7F;
               }
               
               .oxy-share-button.telegram {
                    background-color: #28A8EA;
               }
               
               .oxy-share-button.telegram:hover {
                    background-color: #5CDAFF;
               }
               
               .oxy-share-button.pinterest {
                    background-color: #E60023;
               }
               
               .oxy-share-button.pinterest:hover {
                    background-color: #FF1A3D;
               }
               
               .oxy-share-button.xing {
                    background-color: #1A7576;
               }
               
               .oxy-share-button.xing:hover {
                    background-color: #1e8687;
               }

               .oxy-share-button.line {
                    background-color: #0BBF5B;
                }
                
                .oxy-share-button.line:hover {
                        background-color: #25D975;
                }

               
               ",
            "custom"  => "",
        ) );
        $button_brand->whiteList();
        
        
        $button_section->addStyleControls(
            array(
                array(
                    "name" => __('Background Color'),
                    "selector" => $button_selector,
                    "property" => 'background-color',
                    "default" => '#111',
                    "condition" => 'brand=custom'
                ),
                array(
                    "name" => __('Text / Icon Color'),
                    "selector" => $button_selector,
                    "property" => 'color',
                    "default" => '#fff',
                    "condition" => 'brand=custom'
                )
            )
        );
        
        
        /**
         * Button Hover Styles
         */
        
        $button_hover_section = $this->addControlSection("button_hover_section", __("Button Hover Styles"), "assets/icon.png", $this);
        
        $button_hover_section->addStyleControls(
            array(
                
                array(
                    "name" => __('Background Color'),
                    "selector" => $button_selector.":hover",
                    "property" => 'background-color',
                    "condition" => 'brand=custom'
                ),
                array(
                    "name" => __('Text / Icon Color'),
                    "selector" => $button_selector.":hover",
                    "property" => 'color',
                    "condition" => 'brand=custom'
                )
            )
        );
        
        $button_hover_section->borderSection('Borders', $button_selector.":hover",$this);
        $button_hover_section->boxShadowSection('Shadows', $button_selector.":hover",$this);
        
        $button_hover_section->addStyleControl(
            array(
                "name" => __('Hover Transition Duration'),
                "selector" => $button_selector,
                "property" => 'transition-duration',
                "control_type" => 'slider-measurebox',
                "default" => '0.3',
            )
        )
        ->setUnits('ms','ms')
        ->setRange('0','1000','1');
        
        
        /**
          * Icons
          */
        
        $icon_section = $this->addControlSection("icon_section", __("Icons"), "assets/icon.png", $this);
        $icon_selector = '.oxy-share-icon';
        $icon_hover_selector = '.oxy-share-button:hover .oxy-share-icon';
        
        $icon_section->addStyleControls(
            array(
                
                array(
                    "name" => __('Icon Size'),
                    "selector" => $icon_selector,
                    "property" => 'font-size',
                )
            )
        );
        
        $icon_section->addStyleControls(
            array(
                array(
                    "name" => __('Icon Area Background'),
                    "selector" => $icon_selector,
                    "property" => 'background-color',
                    "default" => 'rgba(255,255,255,0.15)',
                ),
                array(
                    "name" => __('Icon Area Hover Background'),
                    "selector" => $icon_hover_selector,
                    "property" => 'background-color',
                    "default" => 'rgba(255,255,255,0.25)',
                )
            )
        );
        
        $icon_section->addStyleControl(
            array(
                "name" => __('Hover Transition Duration'),
                "selector" => $icon_selector,
                "property" => 'transition-duration',
                "control_type" => 'slider-measurebox',
                "default" => '0.3',
            )
        )
        ->setUnits('ms','ms')
        ->setRange('0','1000','1');
        
        $hide_icon_control = $icon_section->addOptionControl(
            array(
                "name" => __('Hide Icon Below', 'oxygen'),
                "slug" => 'hide_icon_below',
                "type" => 'medialist',
                "default" => 'never'
            )
        );
        $hide_icon_control->rebuildElementOnChange();
        
        
        $icon_spacing_section = $icon_section->addControlSection("icon_spacing_section", __("Spacing"), "assets/icon.png", $this);
        
        
        $icon_spacing_section->addStyleControl(
            array( 
                //"name" => __('Top'),
                "selector" => $icon_selector,
                "property" => 'padding-top',
                "control_type" => "measurebox",
                "unit" => "em",
                "default" => '1',
            )
        )->setParam('hide_wrapper_end', true);
        
       $icon_spacing_section->addStyleControl(
            array( 
                //"name" => __('Top'),
                "selector" => $icon_selector,
                "property" => 'padding-left',
                "control_type" => "measurebox",
                "unit" => "em",
                "default" => '1.5',
            )
        )->setParam('hide_wrapper_start', true);
        
        $icon_spacing_section->addStyleControl(
            array( 
                //"name" => __('Top'),
                "selector" => $icon_selector,
                "property" => 'padding-right',
                "control_type" => "measurebox",
                "unit" => "em",
                "default" => '1.5',
            )
        )->setParam('hide_wrapper_end', true);
        
        $icon_spacing_section->addStyleControl(
            array( 
                //"name" => __('Top'),
                "selector" => $icon_selector,
                "property" => 'padding-bottom',
                "control_type" => "measurebox",
                "unit" => "em",
                "default" => '1',
            )
        )->setParam('hide_wrapper_start', true);
        
        $icon_section->borderSection('Borders', $icon_selector,$this);
        
        $icon_section->boxShadowSection('Shadows', $icon_selector,$this);
        
        
        /**
          * Button Text
          */
        
        $text_section = $this->addControlSection("text_section", __("Text"), "assets/icon.png", $this);
        $text_selector = '.oxy-share-name';
        
        $hide_text_control = $text_section->addOptionControl(
            array(
                "name" => __('Hide Text Below', 'oxygen'),
                "slug" => 'hide_text_below',
                "type" => 'medialist',
                "default" => 'never'
            )
        );
        $hide_text_control->rebuildElementOnChange();
        
        $text_section->typographySection('Typography', $text_selector,$this);
        
        $text_section->addStyleControl(
            array( 
                "selector" => $text_selector,
                "property" => 'padding-top',
                "control_type" => "measurebox",
                "unit" => "em",
                "default" => '1',
            )
        )->setParam('hide_wrapper_end', true);
        
       $text_section->addStyleControl(
            array( 
                "selector" => $text_selector,
                "property" => 'padding-left',
                "control_type" => "measurebox",
                "unit" => "em",
                "default" => '1.5',
            )
        )->setParam('hide_wrapper_start', true);
        
        $text_section->addStyleControl(
            array( 
                "selector" => $text_selector,
                "property" => 'padding-right',
                "control_type" => "measurebox",
                "unit" => "em",
                "default" => '1.5',
            )
        )->setParam('hide_wrapper_end', true);
        
        $text_section->addStyleControl(
            array( 
                "selector" => $text_selector,
                "property" => 'padding-bottom',
                "control_type" => "measurebox",
                "unit" => "em",
                "default" => '1',
            )
        )->setParam('hide_wrapper_start', true);


        $text_section->addStyleControl(
            array( 
                "selector" => $text_selector,
                "property" => 'width',
            )
        );
        
    }
    
    
    function allowedEmptyOptions($options) {

        $options_to_add = array(
            "oxy-social-share-buttons_email_subject",
            "oxy-social-share-buttons_email_body"
        );

        $options = array_merge($options, $options_to_add);

        return $options;
    }


    function output_js() { ?>

        <script type="text/javascript">
            jQuery(document).ready(oxygen_social_share);
            function oxygen_social_share($) {

                let extrasSocialShare = function ( container ) {

                    $('.oxy-social-share-buttons').each(function( i, OxySocialShare ) {

                        if ( $(OxySocialShare).has('.oxy-social-share-buttons_data[data-behaviour="popup"]') ) {

                            let socialWidth = 600;
                            let socialHeight = 600;
                            
                            let leftPosition = (window.screen.width / 2) - ((socialWidth / 2) + 10);
                            let topPosition = (window.screen.height / 2) - ((socialHeight / 2) + 50);
                            let windowFeatures = "width="+ socialWidth +",height="+ socialHeight +",scrollbars=yes,left=" + leftPosition + ",top=" + topPosition + ",screenX=" + leftPosition + ",screenY=" + topPosition + ",toolbar=no,menubar=no,location=no,directories=no";

                            $(OxySocialShare).has('.oxy-social-share-buttons_data[data-behaviour="popup"]').find('a.oxy-share-button:not(.email)').on('click', function (e) {

                                e.preventDefault();
                                window.open(
                                    jQuery(this).attr("href"),
                                    "popupWindow",
                                    windowFeatures
                                );

                            });

                        }

                    }); 

                }

                extrasSocialShare('body');
                
                // Expose function
                window.doExtrasSocialShare = extrasSocialShare;

           }  </script>  
           
        <?php }
        

    function output_print_js() { 
        wp_enqueue_script( 'print-js', plugin_dir_url(__FILE__) . 'assets/print.js', '', '1.0.0' );
    }
    
    
    function customCSS($options, $selector) {
        
        $css = '';
        
        if( ! $this->css_added ) {
        
            $css .= ".oxy-social-share-buttons {
                        display: inline-flex;
                        flex-wrap: wrap;
                        font-size: 12px;
                    }

                    .oxy-share-button {
                        display: flex;
                        align-items: stretch;
                        margin: .2em;
                        overflow: hidden;
                        cursor: pointer;
                        border: none;
                        padding: 0;
                    }

                    .oxy-social-share-buttons .oxy-share-button {
                        background: #111;
                        color: #fff;
                        display: flex;
                        transition-duration: .35s;
                        line-height: 1;
                        transition-timing-function: ease;
                        transition-property: background-color,color,border-color;
                    }

                    .oxy-share-icon svg {
                        fill: currentColor;
                        width: 1em;
                        height: 1em;
                    }

                    .oxy-share-icon {
                        background-color: rgba(255,255,255,0.15);
                        display: flex;
                        align-items: center;
                        padding: 1em 1.5em;
                        transition-duration: 0.3s;
                        transition-property: background-color;
                    }

                    .oxy-share-button:hover .oxy-share-icon {
                        background-color: rgba(255,255,255,0.25);
                    }

                    .oxy-share-name {
                        padding: 1em 1.5em;
                    }
                    
                    @media print {
    
                        .x-print-no-print {
                            display: none !important;
                        }
                    
                        .x-print-preserve-ancestor {
                            display: block !important;
                            margin: 0 !important;
                            padding: 0 !important;
                            border: none !important;
                            box-shadow: none !important;
                        }
                    
                        .oxy-social-share-buttons[data-x-hide-print] {
                            display: none !important;
                        }
                    }
                    
                    ";
            
            $this->css_added = true;
            
            }
        
        
            if ((isset($options["oxy-social-share-buttons_hide_text_below"]) && $options["oxy-social-share-buttons_hide_text_below"]!="never")) {    
                $max_width = oxygen_vsb_get_media_query_size($options["oxy-social-share-buttons_hide_text_below"]);
                $css .= "@media (max-width: {$max_width}px) {
                
                            $selector .oxy-share-name {
                                display: none;
                            }

                        }";
                }
        
            if ((isset($options["oxy-social-share-buttons_hide_icon_below"]) && $options["oxy-social-share-buttons_hide_icon_below"]!="never")) {
                $max_width = oxygen_vsb_get_media_query_size($options["oxy-social-share-buttons_hide_icon_below"]);
                $css .= "@media (max-width: {$max_width}px) {
                
                            $selector .oxy-share-icon {
                                display: none;
                            }
                            
                        }";
            }
        

            return $css;
    }
    
    
    function afterInit() {
        $this->removeApplyParamsButton();
    }

}

new ExtraSocial();