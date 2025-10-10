<?php

class ExtraMediaPlayer extends OxygenExtraElements {
        
    var $js_added = false;
    var $css_added = false;
    
	function name() {
        return 'Pro Media Player';
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

        add_filter( 'script_loader_tag', array($this,'add_type_attribute') , 10, 3 );

        add_action("oxygen_default_classes_output", array( $this->El, "generate_defaults_css" ) );

        // include only for builder
        if (isset( $_GET['oxygen_iframe'] )) {
            add_action( 'wp_footer', array( $this, 'output_js' ) );
        }
    
    }

    function add_type_attribute($tag, $handle, $src) {
        
        if ( 'extras-vime' !== $handle ) {
            return $tag;
        }
        
        $tag = '<script type="module" src="' . esc_url( $src ) . '"></script>';
        return $tag;
    }

    function render($options, $defaults, $content) {
        
        $video_source = isset( $options['video_source'] ) ? esc_attr($options['video_source']) : '';
        $theme = isset( $options['theme'] ) ? esc_attr($options['theme']) : 'dark';
        $aspect_ratio = isset( $options['aspect_ratio'] ) ? esc_attr($options['aspect_ratio']) : '16:9';
        $activeDuration = isset( $options['activeDuration'] ) ? esc_attr($options['activeDuration']) : '';
        $hideOnMouseLeave = isset( $options['hideOnMouseLeave'] ) ? esc_attr($options['hideOnMouseLeave']) : '';
        $hideWhenPaused = isset( $options['hideWhenPaused'] ) ? esc_attr($options['hideWhenPaused']) : '';
        $waitForPlaybackStart = isset( $options['waitForPlaybackStart'] ) ? esc_attr($options['waitForPlaybackStart']) : '';
        $fullscreen_click = isset( $options['fullscreen_click'] ) ? esc_attr($options['fullscreen_click']) : '';
        $maybe_poster = isset( $options['maybe_poster'] ) ? esc_attr($options['maybe_poster']) : '';
        $maybe_skeleton = isset( $options['maybe_skeleton'] ) ? esc_attr($options['maybe_skeleton']) : '';
        $maybe_spinner = isset( $options['maybe_spinner'] ) ? esc_attr($options['maybe_spinner']) : '';
        $maybe_click_to_play = isset( $options['maybe_click_to_play'] ) ? esc_attr($options['maybe_click_to_play']) : '';
        
        
        $maybe_autoplay = isset( $options['maybe_autoplay'] ) && ('true' === esc_attr($options['maybe_autoplay'] )) ? 'autoplay' : '';
        $maybe_autopause = isset( $options['maybe_autopause'] ) && ('true' === esc_attr($options['maybe_autopause'] )) ? 'autopause=true' : 'autopause=false';
        $maybe_loop = isset( $options['maybe_loop'] ) && ('true' === esc_attr($options['maybe_loop'] )) ? 'loop' : '';
        $maybe_muted = isset( $options['maybe_muted'] ) && ('true' === esc_attr($options['maybe_muted'] )) ? 'muted' : '';
        $maybe_byline = isset( $options['maybe_byline'] ) ? esc_attr($options['maybe_byline'] ) : 'false';
        $maybe_portrait = isset( $options['maybe_portrait'] ) ? esc_attr($options['maybe_portrait'] ) : 'false';

        $maybe_cdn = isset( $options['maybe_cdn'] ) ? esc_attr($options['maybe_cdn'] ) : '';
        $cross_origin = isset( $options['cross_origin'] ) && ('undefined' !== esc_attr($options['cross_origin'] )) ? 'cross-origin="'. esc_attr($options['cross_origin'] ) . '"' : '';
        
        $ui_type = isset( $options['ui_type'] ) ? esc_attr($options['ui_type']) : '';
        $ui_layout = isset( $options['ui_layout'] ) ? esc_attr($options['ui_layout']) : '';
        
        $maybe_default_controls = ('provider' === $ui_type ) ? 'controls' : '';
        
        $post_image_source = isset( $options['post_image_source'] ) ? esc_attr($options['post_image_source']) : '';
       
        
        $maybe_mobile = ('mobile' === $ui_layout) ? 'is-mobile=true' : '';
        
        $maybe_lazy_poster = isset( $options['maybe_lazy_poster'] ) && ('true' === esc_attr($options['maybe_lazy_poster'] )) ? 'loading=lazy' : '';
        
        
         // Icons
        $play_icon  = isset( $options['play_icon'] ) ? esc_attr($options['play_icon']) : "";
        $pause_icon  = isset( $options['pause_icon'] ) ? esc_attr($options['pause_icon']) : "";
        
        global $oxygen_svg_icons_to_load;
        $oxygen_svg_icons_to_load[] = $play_icon;
        $oxygen_svg_icons_to_load[] = $pause_icon;
        
        
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
        
        
        $get_video_id = function ($url) {
            $parts = parse_url($url);
            if(isset($parts['query'])){
                parse_str($parts['query'], $qs);
                if(isset($qs['v'])){
                    return $qs['v'];
                }else if(isset($qs['vi'])){
                    return $qs['vi'];
                }
            }
            if(isset($parts['path'])){
                $path = explode('/', trim($parts['path'], '/'));
                return $path[count($path)-1];
            }
            return false;
        };
        
        $youtube_video_before_id = $dynamic($options['youtube_video_id']);
		$vimeo_video_before_id = $dynamic($options['vimeo_video_id']);
		
		$youtube_video_id = $get_video_id($youtube_video_before_id);
        $vimeo_video_id = $get_video_id($vimeo_video_before_id);
		
        $video_url = $dynamic($options['video_url']);
        $audio_url = $dynamic($options['audio_url']);

        if ( !isset( $options['poster_image_dynamic'] ) || 'false' === esc_attr($options['poster_image_dynamic']) ) {
            $poster_image_url = isset( $options['poster_image_url'] ) && ( "true" === $maybe_poster ) ? esc_attr($options['poster_image_url']) : '';
            $poster_image_custom_url = isset( $options['poster_image_custom_url'] ) ? esc_attr($options['poster_image_custom_url']) : '';
        } else {
            $poster_image_url = isset( $options['poster_image_urls'] ) ? $dynamic($options['poster_image_urls']) : '';
            $poster_image_custom_url = isset( $options['poster_image_custom_urls'] ) ? $dynamic($options['poster_image_custom_urls']) : '';
        }
         
        ?>
        
        <vime-player <?php echo $maybe_mobile; ?> <?php echo $maybe_muted; ?> <?php echo $maybe_loop; ?> <?php echo $maybe_default_controls; ?> <?php echo $maybe_autoplay; ?> <?php echo $maybe_autopause; ?>  aspect-ratio="<?php echo $aspect_ratio; ?>" theme="<?php echo $theme; ?>" >
            
          <?php if ('youtube' === $video_source) { 
            
            ?> <vime-youtube video-id="<?php echo $youtube_video_id; ?>" /> <?php
            
            } elseif ('vimeo' === $video_source) { 
            
            ?> <vime-vimeo byline="<?php echo $maybe_byline; ?>" portrait="<?php echo $maybe_portrait; ?>" video-id="<?php echo $vimeo_video_id; ?>" /> <?php
        
            } elseif ('audio' === $video_source) {
            
            ?> <vime-audio> <source data-src="<?php echo $audio_url ?>" type="audio/mp3" /> </vime-audio> <?php 
            
            } else { ?>
            
            <vime-video <?php echo $cross_origin; ?> poster="<?php echo $poster_image_url; ?>">
                <source data-src="<?php echo $video_url; ?>" type="video/mp4" />
              </vime-video> 
            
            
        <?php } ?>       
                <vime-default-ui 
                    no-controls=true 
                    no-settings=true             
                     <?php if ("false" === $fullscreen_click || "provider" === $ui_type ) { ?> no-dbl-click-fullscreen=true <?php } ?>
                     <?php if ("false" === $maybe_poster || 'custom' === $post_image_source ) { ?> no-poster=true <?php } ?>
                     <?php if ("false" === $maybe_skeleton || 'audio' === $video_source ) { ?> no-skeleton=true <?php } ?>
                     <?php if ("false" === $maybe_click_to_play || "provider" === $ui_type ) { ?> no-click-to-play=true <?php } ?>  
                     <?php if ("false" === $maybe_spinner || 'audio' === $video_source ) { ?> no-spinner=true <?php } ?>             

                >    <?php if ("true" === $maybe_poster && 'custom' === $post_image_source ) { ?>
                  <vime-custom-poster class="hydrated active"><img class="oxy-pro-media-player_custom-image" <?php echo $maybe_lazy_poster; ?> src="<?php echo $poster_image_custom_url; ?>"></vime-custom-poster>
                      <?php } ?>
                    
                      <?php if ('default' === esc_attr($options['ui_type']) ) { ?>
                  <vime-default-controls 
                  <?php if ("true" === $hideOnMouseLeave ) { ?> hide-on-mouse-leave <?php } ?>
                  <?php if ("true" === $hideWhenPaused ) { ?> hide-when-paused <?php } ?>
                  <?php if ("true" === $waitForPlaybackStart ) { ?> wait-for-playback-start <?php } ?>                       
                  active-duration="<?php echo $activeDuration; ?>"
                >
                    <?php if ('mobile' === $ui_layout ) { ?>  
                      <vime-controls class="hydrated oxy-pro-media-player_play" pin="center" style="top: 50%; left: 50%; transform: translate(-50%, -50%); flex-direction: row; align-items: center; justify-content: start;"><vime-playback-control play-icon="#<?php echo $play_icon; ?>" pause-icon="#<?php echo $pause_icon; ?>"></vime-playback-control></vime-controls>
                    <?php } ?>
                    
                    </vime-default-controls>

                <?php } // closing ui_type condition  ?>  
                </vime-default-ui>
            
             
            
            </vime-player>

        <?php

        $this->dequeue_scripts_styles();
        
        if( method_exists('OxygenElement', 'builderInlineJS') ) {
        
            $this->El->builderInlineJS("
                        jQuery(document).ready(function($) { 
                        jQuery('#%%ELEMENT_ID%%').find('vime-player').addClass('extras-in-builder');
                            
                        const mediaPlayer = $('#%%ELEMENT_ID%%');
                        const player = mediaPlayer.find('vime-player')[0];

                          // Listening to an event.
                          player.addEventListener('vPlaybackStarted', (event) => {
                            const currentTime = event.detail;
                            mediaPlayer.find('vime-custom-poster').removeClass('active');  
                          });    
                            
                        });"
                    );
                 
                        
        }
        
        
         // add JavaScript code only once and if shortcode presented
        if ($this->js_added !== true) {
            if (!defined('OXY_ELEMENTS_API_AJAX') || !OXY_ELEMENTS_API_AJAX) {
                if ('true' === $maybe_cdn) {
                    add_action( 'wp_footer', array( $this, 'output_cdn_js' ) );
                } else {
                    add_action( 'wp_footer', array( $this, 'output_js' ) );
                }
                add_action( 'wp_footer', array( $this, 'output_listeners_js' ) );
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
                'type' => 'dropdown',
                'name' => __('Media source'),
                'slug' => 'video_source',
                'default' => 'vimeo',
            )
        )->setValue(array( 
            "youtube" => "Youtube", 
            "vimeo" => "Vimeo",
            "video" => "Video (url)",
            "audio" => "Audio (url)",
            )
        )->setValueCSS( array(
            "vimeo"  => " vime-player.video {
                            height: 0;
                            overflow: hidden;
                        }",
        ) )->rebuildElementOnChange();


        
        
        /**
         * Youtube ID
         */
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Video ID or URL'),
                "slug" => 'youtube_video_id',
                "default" => 'DyTCOwB0DVw',
                "condition" => 'video_source=youtube'
            )
        )->rebuildElementOnChange()
        ->setParam('dynamicdatacode', '<div optionname="\'oxy-pro-media-player_youtube_video_id\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');
        
        
        /**
         * Vimeo ID
         */
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Video ID or URL'),
                "slug" => 'vimeo_video_id',
                "default" => '411652396',
                "condition" => 'video_source=vimeo'
            )
        )->rebuildElementOnChange()
        ->setParam('dynamicdatacode', '<div optionname="\'oxy-pro-media-player_vimeo_video_id\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');
        
       
        /**
         * Video SRC
         */
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Video url'),
                "slug" => 'video_url',
                "default" => 'https://media.vimejs.com/720p.mp4',
                "condition" => 'video_source=video',
                "base64" => true
            )
        )->rebuildElementOnChange()
        ->setParam('dynamicdatacode', '<div optionname="\'oxy-pro-media-player_video_url\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');
        
        
        /**
         * Audio SRC
         */
        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Audio url'),
                "slug" => 'audio_url',
                "default" => 'https://media.vimejs.com/audio.mp3',
                "condition" => 'video_source=audio',
                "base64" => true
            )
        )->rebuildElementOnChange()
        ->setParam('dynamicdatacode', '<div optionname="\'oxy-pro-media-player_audio_url\'" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode">data</div>');
        
           
        
        $this->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Aspect ratio'),
                'slug' => 'aspect_ratio',
                'default' => '16:9',
                "condition" => 'video_source!=audio&&video_source!=vimeo'
            )
            
        )->setValue(array( 
            "16:9" => "16:9 (default)", 
            "21:9" => "21:9",
            "4:3" => "4:3",
            "9:16" => "9:16",
            "1:1" => "1:1",
            //"section" => 'Section Background'
            )
        )->setValueCSS( array(
            "section"  => " {
                            position: absolute;
                            width: 100%;
                            height: 100%;
                            left: 0;
                            top: 0;
                            z-index: 0;
                        }
                        
                        vime-player {
                            padding: 0!important;
                            width: 100%;
                            height: 100%;
                            --vm-ui-z-index: 0;
                            --vm-poster-z-index: 0;
                        }
                        
                        video {
                            min-width: 100%;
                            min-height: 100%;
                            width: auto;
                            height: auto;
                            top: 50%;
                            left: 50%;
                            position: absolute;
                            transform: translate(-50%, -50%);
                        }
                        
                        vime-player .blocker {
                            background: var(--extras-video-overlay);
                            z-index: 1;
                            pointer-events: none;
                        }",
        ) )->rebuildElementOnChange();
        
        
        
        
        
        /**
         * Controls
         */ 
        $controls_section = $this->addControlSection("controls_section", __("UI Controls"), "assets/icon.png", $this);
        
        // todo //
        
        $controls_section->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('UI Type'),
                'slug' => 'ui_type',
                'default' => 'default',
                'condition' => 'video_source!=audio',
            )
            
        )->setValue(array( 
            "default" => "Stylable UI controls", 
            "provider" => "Standard provider controls",
            "none" => "No controls",
          )
        )->rebuildElementOnChange();
        
        
        $controls_section->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('UI Layout'),
                'slug' => 'ui_layout',
                'condition' => 'video_source!=audio&&ui_type=default',
            )
            
        )->setValue(array( 
            "default" => "Default",
            "mobile" => "Custom Centered Play/Pause"
          )
        )->setValueCSS( array(
            "mobile"  => "
                        
                        vime-tooltip {
                            display: none;
                        }",
        ) )->rebuildElementOnChange();
        
        
        
        
        
        
        $controls_section->addOptionControl(
            array(
                "type" => 'slider-measurebox',
                "name" => __('Fade-out controls after inactivity'),
                "slug" => 'activeDuration',
                "default" => "750",
                "condition" => 'video_source!=audio&&ui_type=default'
            )
        )->setUnits("ms","ms")
         ->setRange('0','5000','10');
        
        
        
        $controls_section->addCustomControl(
            '<hr><div style="color: #fff; line-height: 1.3; font-size: 13px;">Enable/Disable any of the individual controls individually</div>','description');
        
        /**
         * Tooltips
         */ 
        $controls_disable_section = $controls_section->addControlSection("controls_disable_section", __("Disable Controls"), "assets/icon.png", $this);
        
        $controls_disable_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Fullscreen'),
                'slug' => 'maybe_fullscreen',
                "condition" => 'video_source!=audio&&ui_type!=provider&&ui_type!=none'
            )
            
        )->setValue(array( 
            "enable" => "Enable", 
            "disable" => "Disable",
            )
        )->setValueCSS( array(
            "disable"  => "vime-fullscreen-control {
                                display: none;
                            }",
        ) )->whiteList();
        
        $controls_disable_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Time progress'),
                'slug' => 'maybe_timeprogress',
                "condition" => 'video_source!=audio&&ui_type!=provider&&ui_type!=none'
            )
            
        )->setValue(array( 
            "enable" => "Enable", 
            "disable" => "Disable",
            )
        )->setValueCSS( array(
            "disable"  => "vime-time-progress,
                           vime-current-time,
                           vime-end-time {
                                display: none;
                            }",
        ) )->whiteList();
        
        $controls_disable_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Current time'),
                'slug' => 'maybe_current_time',
                "condition" => 'video_source=audio&&ui_type!=provider&&ui_type!=none'
            )
            
        )->setValue(array( 
            "enable" => "Enable", 
            "disable" => "Disable",
            )
        )->setValueCSS( array(
            "disable"  => "vime-current-time {
                                display: none;
                            }",
        ) )->whiteList();
        
        $controls_disable_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('End time'),
                'slug' => 'maybe_end_time',
                "condition" => 'video_source=audio&&ui_type!=provider&&ui_type!=none'
            )
            
        )->setValue(array( 
            "enable" => "Enable", 
            "disable" => "Disable",
            )
        )->setValueCSS( array(
            "disable"  => "vime-end-time {
                                display: none;
                            }",
        ) )->whiteList();
        
         
        
        $controls_disable_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Miniplayer'),
                'slug' => 'maybe_miniplayer',
                "condition" => 'video_source!=audio&&ui_type!=provider&&ui_type!=none'
            )
            
        )->setValue(array( 
            "enable" => "Enable", 
            "disable" => "Disable",
            )
        )->setValueCSS( array(
            "disable"  => "vime-pip-control {
                                display: none;
                            }",
        ) )->whiteList();
        
        $controls_disable_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Slider'),
                'slug' => 'maybe_scrubber',
                "condition" => 'ui_type!=provider&&ui_type!=none'
            )
            
        )->setValue(array( 
            "enable" => "Enable", 
            "disable" => "Disable",
            )
        )->setValueCSS( array(
            "disable"  => "vime-scrubber-control {
                                display: none;
                            }",
        ) )->whiteList();
        
        $controls_disable_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Play'),
                'slug' => 'maybe_playback',
                "condition" => 'ui_type!=provider&&ui_type!=none'
            )
            
        )->setValue(array( 
            "enable" => "Enable", 
            "disable" => "Disable",
            )
        )->setValueCSS( array(
            "disable"  => "vime-playback-control {
                                display: none;
                            }",
        ) )->whiteList();
        
        $controls_disable_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Volume'),
                'slug' => 'maybe_volume',
                "condition" => 'ui_type!=provider&&ui_type!=none'
            )
            
        )->setValue(array( 
            "enable" => "Enable", 
            "disable" => "Disable",
            )
        )->setValueCSS( array(
            "disable"  => "vime-volume-control {
                                display: none;
                            }",
        ) )->whiteList();
        
        
        /**
         * Icons
         */ 
        $play_icon_section = $controls_section->addControlSection("play_icon_section", __("Custom Play/Pause"), "assets/icon.png", $this);
        
        $play_icon_section->addStyleControl(
            array(
                "name" => __('Icon size'),
                "property" => '--extras-play-icon-size',
                "selector" => '.oxy-pro-media-player_play',
                "control_type" => 'slider-measurebox',
                "default" => '60',
            )
        )->setRange(0, 120, 1)
         ->setUnits('px');    
        
        $play_icon_section->addStyleControl(
            array(
                "name" => __('Icon hover scale'),
                "property" => '--vm-play-scale',
                "selector" => '.oxy-pro-media-player_play button:hover',
                "control_type" => 'slider-measurebox',
                "default" => '1',
            )
        )->setRange(0.7, 1.3, .01);
        
        
        $play_icon_section->addStyleControl( 
           array(
                "name" => __('Hover color'),
                "property" => '--vm-control-focus-color',
                "selector" => '.oxy-pro-media-player_play',
                "control_type" => 'colorpicker',
           )
        );
        
        
        $play_icon_section->addStyleControl(
            array(
                "name" => __('Transition Duration'),
                "property" => 'transition-duration',
                "selector" => '.oxy-pro-media-player_play button',
                "control_type" => 'slider-measurebox',
                "default" => '.3',
            )
        )->setUnits('s','s')
         ->setRange(.1, 1, .1);
        
        
        $play_icon_section->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Play icon'),
                "slug" => 'play_icon',
                "value" => 'FontAwesomeicon-play-circle', 
                "condition" => 'video_source!=audio&&ui_type=default&&ui_layout=mobile',
            )
        )->rebuildElementOnChange();
        
        $play_icon_section->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Pause icon'),
                "slug" => 'pause_icon',
                "value" => 'FontAwesomeicon-pause-circle', 
                "condition" => 'video_source!=audio&&ui_type=default&&ui_layout=mobile',
            )
        )->rebuildElementOnChange();
        
        
        
         /**
         * Styling
         */ 
        $styling_section = $controls_section->addControlSection("styling_section", __("Control Styling"), "assets/icon.png", $this);
        /*
        $styling_section->addCustomControl(
            '<div style="color: #fff; line-height: 1.3; font-size: 13px;">These changes will only be seen inside Oxygen in preview mode</div>','description');
        */
        
         /**
         * Styling
         */ 
        $color_section = $this->addControlSection("color_section", __("Colors"), "assets/icon.png", $this);
        
        
         $color_section->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Color theme'),
                'slug' => 'theme',
                'default' => 'dark'
            )
            
        )->setValue(array( 
            "dark" => "Dark", 
            "light" => "Light",
            )
        )->rebuildElementOnChange(); 
        
        
        $vime_player_selector = 'vime-player'; 
        
            
        $color_section->addStyleControl( 
           array(
                "name" => __('Control color'),
                "property" => '--vm-control-color',
                "selector" => $vime_player_selector,
                "control_type" => 'colorpicker',
               // "default" => '#fff',
           )
        );
        
        
        
        $color_section->addStyleControl( 
           array(
                "name" => __('Accent color'),
                "property" => '--vm-player-theme',
                "selector" => $vime_player_selector,
                "control_type" => 'colorpicker',
               // "default" => '#f05b51'
           )
        );
        
        
        $color_section->addStyleControl( 
           array(
                "name" => __('Control area background'),
                "property" => '--vm-controls-bg',
                "selector" => $vime_player_selector,
                "control_type" => 'colorpicker',
           )
        );
        
        
        /*$color_section->addStyleControl( 
           array(
                "name" => __('Settings menu background'),
                "property" => '--vm-menu-bg',
                "selector" => $vime_player_selector,
                "control_type" => 'colorpicker',
                "condition" => 'maybe_settings=enable'
           )
        );
        
        $color_section->addStyleControl( 
           array(
                "name" => __('Settings menu color'),
                "property" => '--vm-menu-color',
                "selector" => $vime_player_selector,
                "control_type" => 'colorpicker',
                "condition" => 'maybe_settings=enable'
           )
        ); */
        
        
        $styling_section->addStyleControl(
            array(
                "name" => __('Control scale'),
                "property" => '--vm-control-scale',
                "selector" => $vime_player_selector,
                "control_type" => 'slider-measurebox',
                "default" => '.9',
            )
        )->setRange(0, 2, .01);
        
        
        $styling_section->addStyleControl(
            array(
                "name" => __('Slider heights'),
                "property" => '--extras-slider-height',
                "selector" => $vime_player_selector,
                "control_type" => 'slider-measurebox',
                "default" => '2',
            )
        )->setRange(0, 15, 1)
         ->setUnits("px"); 
        
        $styling_section->addStyleControl(
            array(
                "name" => __('Control margin top'),
                "property" => '--vm-control-group-spacing',
                "selector" => $vime_player_selector,
                "control_type" => 'slider-measurebox',
                "default" => '12',
                "condition" => 'maybe_scrubber=enable&&video_source!=audio'
            )
        )->setRange(0, 30, 1)
         ->setUnits("px"); 
        
        
        
        
        
        $styling_section->addStyleControl(
            array(
                "name" => __('Control padding'),
                "property" => '--vm-controls-padding',
                "selector" => $vime_player_selector,
                "control_type" => 'slider-measurebox',
                "default" => '15',
            )
        )->setRange(0, 30, 1)
         ->setUnits("px");
        
        
        $styling_section->addStyleControl(
            array(
                "name" => __('Control spacing'),
                "property" => '--vm-controls-spacing',
                "selector" => $vime_player_selector,
                "control_type" => 'slider-measurebox',
                "default" => '15',
            )
        )->setRange(0, 20, 1)
         ->setUnits("px");
        
        $styling_section->addStyleControl(
            array(
                "name" => __('Time font size'),
                "property" => '--vm-time-font-size',
                "selector" => $vime_player_selector,
                "control_type" => 'slider-measurebox',
                "default" => '13',
            )
        )->setRange(1, 48, 1)
         ->setUnits("px");
        
        
        
        $styling_section->addStyleControl(
            array(
                "name" => __('Control border radius'),
                "property" => '--vm-control-border-radius',
                "selector" => $vime_player_selector,
                "control_type" => 'measurebox',
                "default" => '3',
            )
        )->setUnits("px");
        
        
        
       /**
         * Behaviour
         */ 
        $behaviour_section = $this->addControlSection("behaviour_section", __("Behaviour"), "assets/icon.png", $this);
        
        /*$behaviour_section->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Start time (sec)'),
                "slug" => 'start_time',
                "default" => '0',
                "condition" => 'video_source!=audio'
            )
        )->rebuildElementOnChange()
         ->setParam('dynamicdatacode', '<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertShortcodeMediaYoutubeID">data</div>');
        */
        
        
        $behaviour_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Muted by default'),
                'slug' => 'maybe_muted',
                "condition" => 'video_source!=audio'
            )
            
        )->setValue(array( 
            "true" => "True", 
            "false" => "False",
            )
        )->setDefaultValue('false')
         ->rebuildElementOnChange();
        
        
        
        
        $behaviour_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Clicking on element will play/pause'),
                'slug' => 'maybe_click_to_play',
                "condition" => 'video_source!=audio&&ui_type!=provider'
            )
            
        )->setValue(array( 
           "true" => "True", 
            "false" => "False",
            )
        )->setDefaultValue('true')
         ->rebuildElementOnChange();
        
        
        
        $behaviour_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Double click element for fullscreen'),
                'slug' => 'fullscreen_click',
                "condition" => 'video_source!=audio&&ui_type!=provider'
            )
            
        )->setValue(array( 
            "true" => "True", 
            "false" => "False",
            )
        )->setDefaultValue('true')
         ->rebuildElementOnChange();
        
        
        $behaviour_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Hide controls on mouse leave'),
                'slug' => 'hideOnMouseLeave',
                "condition" => 'video_source!=audio&&ui_type=default',
            )
            
        )->setValue(array( 
            "true" => "True", 
            "false" => "False",
            )
        )->setDefaultValue('false')
         ->rebuildElementOnChange();
        
        $behaviour_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Hide controls when paused'),
                'slug' => 'hideWhenPaused',
                "condition" => 'video_source!=audio&&ui_type=default',
            )
            
        )->setValue(array( 
            "true" => "True", 
            "false" => "False",
            )
        )->setDefaultValue('false')
         ->rebuildElementOnChange();
        
        $behaviour_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Wait for playback start before controls shown'),
                'slug' => 'waitForPlaybackStart',
                "condition" => 'video_source!=audio&&ui_type=default',
            )
            
        )->setValue(array( 
            "true" => "True", 
            "false" => "False",
            )
        )->setDefaultValue('false')
         ->rebuildElementOnChange();
        
        $behaviour_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Autoplay'),
                'slug' => 'maybe_autoplay',
                "condition" => 'video_source!=audio&&video_source!=youtube&&video_source!=vimeo'
            )
            
        )->setValue(array( 
            "true" => "True", 
            "false" => "False",
            )
        )->setDefaultValue('false')
         ->rebuildElementOnChange();
        
        
        $behaviour_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Display video owner name in (Vimeo UI)'),
                'slug' => 'maybe_byline',
                "condition" => 'video_source=vimeo'
            )
            
        )->setValue(array( 
            "true" => "True", 
            "false" => "False",
            )
        )->setDefaultValue('false')
         ->rebuildElementOnChange();
        
        $behaviour_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Display video owner portrait (Vimeo UI)'),
                'slug' => 'maybe_portrait',
                "condition" => 'video_source=vimeo'
            )
            
        )->setValue(array( 
            "true" => "True", 
            "false" => "False",
            )
        )->setDefaultValue('false')
         ->rebuildElementOnChange();
        
        
        
        /*$behaviour_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Plays inline'),
                'slug' => 'maybe_playsinline',
                "condition" => 'video_source!=audio'
            )
            
        )->setValue(array( 
            "true" => "True", 
            "false" => "False",
            )
        )->setDefaultValue('false')
         ->rebuildElementOnChange();
        */
        
        
        
        $behaviour_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Autopause'),
                'slug' => 'maybe_autopause',
                "condition" => 'video_source!=audio'
            )
            
        )->setValue(array( 
            "true" => "True", 
            "false" => "False",
            )
        )->setDefaultValue('true');
        
        $behaviour_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Loop'),
                'slug' => 'maybe_loop',
                "condition" => 'video_source!=audio'
            )
            
        )->setValue(array( 
            "true" => "True", 
            "false" => "False",
            )
        )->setDefaultValue('false');


        $behaviour_section->addOptionControl(
            array(
                'type' => 'dropdown',
                'name' => __('Cross Origin'),
                'slug' => 'cross_origin',
                "condition" => 'video_source=video'
            )
            
        )->setValue(array( 
            "anonymous" => "anonymous",
            "use-credentials" => "use-credentials",
            "undefined" => "undefined (default)"
            )
        )->setDefaultValue('undefined')
         ->rebuildElementOnChange();
        
        
        /**
         * Poster Image
         */ 
        $poster_image_section = $this->addControlSection("poster_image_section", __("Poster Image"), "assets/icon.png", $this);
        
        $custom_image_selector = '.oxy-pro-media-player_custom-image';
        
        
        $poster_image_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Poster image'),
                'slug' => 'maybe_poster',
                "condition" => 'video_source!=audio'
            )
            
        )->setValue(array( 
            "true" => "Enable", 
            "false" => "Disable",
            )
        )->setDefaultValue('false')
         ->rebuildElementOnChange();
        
        
        $poster_image_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Poster image source'),
                'slug' => 'post_image_source',
                "condition" => 'maybe_poster=true&&video_source!=audio&&video_source!=video'
            )
            
        )->setValue(array( 
            "provider" => "Video provider", 
            "custom" => "Custom image",
            )
        )->setDefaultValue('provider')
         ->rebuildElementOnChange();

        
        $poster_image_section->addOptionControl(
            array(
                "type" => 'buttons-list',
                "name" => __('Add support for dynamic data','oxygen'),
                "slug" => 'poster_image_dynamic',
                'condition' => 'maybe_poster=true',
                'default' => 'false',
                'description' => __('Enable if using dynamic data to populate the poster image','oxygen'),
            )
        )->setValue(array( 
            "true" => "Enable", 
            "false" => "Disable",
            )
        );
        
        
        $poster_image_section->addOptionControl(
            array(
                'type' => 'mediaurl',
                'name' => 'Image URL',
                'slug' => 'poster_image_url',
                'condition' => 'maybe_poster=true&&video_source=video&&poster_image_dynamic!=true',
                'default' => 'https://media.vimejs.com/poster.png'
            )
        )
        ->rebuildElementOnChange();


        $poster_image_control = $poster_image_section->addCustomControl(
			"<div class=\"oxygen-control  not-available-for-classes not-available-for-media\">			
				<div class=\"oxygen-file-input\">
					<input type=\"text\" spellcheck=\"false\" ng-change=\"iframeScope.setOption(iframeScope.component.active.id,'ct_image','oxy-pro-media-player_poster_image_urls'); iframeScope.parseImageShortcode()\" ng-class=\"{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'oxy-pro-media-player_poster_image_urls')}\" ng-model=\"iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-pro-media-player_poster_image_urls']\" ng-model-options=\"{ debounce: 10 }\" class=\"ng-pristine ng-valid oxygen-option-default ng-touched\">
					<div class=\"oxygen-file-input-browse\" data-mediatitle=\"Select Image\" data-mediabutton=\"Select Image\" data-mediaproperty=\"oxy-pro-media-player_poster_image_urls\" data-mediatype=\"mediaUrl\" data-returnvalue=\"url\">browse</div>
					<div optionname=\"oxy-pro-media-player_poster_image_urls\" class=\"oxygen-dynamic-data-browse ng-isolate-scope\" ctdynamicdata data=\"iframeScope.dynamicShortcodesImageMode\" callback=\"iframeScope.insertPosterShortcodeToImage\">data</div>
				</div>
			</div>",
			'poster_image_urls'
		);
		$poster_image_control->setParam('heading', __('Image URL') );
        $poster_image_control->setCondition("maybe_poster=true&&video_source=video&&poster_image_dynamic=true");
		$poster_image_control->rebuildElementOnChange();
        
        $poster_image_section->addOptionControl(
            array(
                'type' => 'mediaurl',
                'name' => 'Image URL',
                'slug' => 'poster_image_custom_url',
                'condition' => 'maybe_poster=true&&post_image_source=custom&&video_source!=audio&&video_source!=video&&poster_image_dynamic!=true',
                'default' => 'https://media.vimejs.com/poster.png'
            )
        )->rebuildElementOnChange();

        $poster_image_custom_control = $poster_image_section->addCustomControl(
			"<div class=\"oxygen-control  not-available-for-classes not-available-for-media\">			
				<div class=\"oxygen-file-input\">
					<input type=\"text\" spellcheck=\"false\" ng-change=\"iframeScope.setOption(iframeScope.component.active.id,'ct_image','oxy-pro-media-player_poster_image_custom_urls'); iframeScope.parseImageShortcode()\" ng-class=\"{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'oxy-pro-media-player_poster_image_custom_urls')}\" ng-model=\"iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-pro-media-player_poster_image_custom_urls']\" ng-model-options=\"{ debounce: 10 }\" class=\"ng-pristine ng-valid oxygen-option-default ng-touched\">
					<div class=\"oxygen-file-input-browse\" data-mediatitle=\"Select Image\" data-mediabutton=\"Select Image\" data-mediaproperty=\"oxy-pro-media-player_poster_image_custom_urls\" data-mediatype=\"mediaUrl\" data-returnvalue=\"url\">browse</div>
					<div optionname=\"oxy-pro-media-player_poster_image_custom_urls\" class=\"oxygen-dynamic-data-browse ng-isolate-scope\" ctdynamicdata data=\"iframeScope.dynamicShortcodesImageMode\" callback=\"iframeScope.insertPosterShortcodeToCustomImage\">data</div>
				</div>
			</div>",
			'poster_image_custom_urls'
		);
		$poster_image_custom_control->setParam('heading', __('Image URL') );
        $poster_image_custom_control->setCondition("maybe_poster=true&&post_image_source=custom&&video_source!=audio&&video_source!=video&&poster_image_dynamic=true");
		$poster_image_custom_control->rebuildElementOnChange();
        
        $poster_image_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Lazy load image'),
                'slug' => 'maybe_lazy_poster',
                'condition' => 'maybe_poster=true&&post_image_source=custom&&video_source!=audio&&video_source!=video',
            )
            
        )->setValue(array( 
            "true" => "Enable", 
            "false" => "Disable",
            )
        )->setDefaultValue('true');
        
        
       $poster_image_section->addStyleControl(
                array(
                   "selector" => $custom_image_selector,
                    "control_type" => 'measurebox',
                    "value" => '50',
                    "name" => 'left',
                    "property" => '--extras-poster-left',
                    'condition' => 'maybe_poster=true&&post_image_source=custom&&video_source!=audio&&video_source!=video',
                )
        )->setUnits('%')
         ->setParam('hide_wrapper_end', true);
        
        $poster_image_section->addStyleControl(
                array(
                    "selector" => $custom_image_selector,
                    "control_type" => 'measurebox',
                    "value" => '50',
                    "name" => 'top',
                    "property" => '--extras-poster-top',
                    'condition' => 'maybe_poster=true&&post_image_source=custom&&video_source!=audio&&video_source!=video',
                )
        )->setUnits('%', 'px,%,em,auto,vw,vh')
         ->setParam('hide_wrapper_start', true);
        
        $poster_image_section->addStyleControl(
                array(
                    "name" => 'Z index',
                    "selector" => $custom_image_selector,
                    "property" => '--vm-poster-z-index',
                    "condition" => 'maybe_poster=true&&video_source!=audio&&video_source!=video'
                )
        );
        
        /**
         * Loading Styles
         */ 
        $player_styles_section = $this->addControlSection("player_styles_section", __("Loading"), "assets/icon.png", $this);
        
        /*$player_styles_section->addStyleControl( 
           array(
                "name" => __('Scrim background color'),
                "property" => '--vm-scrim-bg',
                "selector" => $vime_player_selector,
                "control_type" => 'colorpicker',
           )
        );*/
        
        //spinner
        $player_styles_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Loading spinner'),
                'slug' => 'maybe_spinner',
                "condition" => 'video_source!=audio'
            )
            
        )->setValue(array( 
            "true" => "Enable", 
            "false" => "Disable",
            )
        )->setDefaultValue('true')
         ->rebuildElementOnChange();
        
        
        $player_styles_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('In-Builder spinner visibility'),
                'slug' => 'builder_spinner',
                "condition" => 'video_source!=audio&&maybe_spinner=true'
            )
            
        )->setValue(array( 
            "visible" => "Visible", 
            "hidden" => "Hidden",
            )
        )->setDefaultValue('hidden')
         ->setValueCSS( array(
            "visible"  => ".extras-in-builder vime-spinner {
                                display: flex;
                                opacity: var(--spinner-opacity);
                                z-index: var(--spinner-zindex);
                            }",
             "hidden"  => ".extras-in-builder vime-spinner {
                                opacity: 0;
                            }",
        ) );
            
        
        $player_styles_section->addStyleControl( 
           array(
                "name" => __('Spinner fill color'),
                "property" => '--vm-spinner-fill-color',
                "selector" => $vime_player_selector,
                "control_type" => 'colorpicker',
                "condition" => 'video_source!=audio&&maybe_spinner=true'
           )
        );
        
        $player_styles_section->addStyleControl( 
           array(
                "name" => __('Spinner track color'),
                "property" => '--vm-spinner-track-color',
                "selector" => $vime_player_selector,
                "control_type" => 'colorpicker',
                "condition" => 'video_source!=audio&&maybe_spinner=true'
           )
        );
        
        
        $player_styles_section->addStyleControl(
            array(
                "name" => __('Spinner size'),
                "property" => '--extras-spinner-size',
                "selector" => $vime_player_selector,
                "control_type" => 'slider-measurebox',
                "default" => '80',
                "condition" => 'video_source!=audio&&maybe_spinner=true'
            )
        )->setRange(1, 150, 1)
         ->setUnits("px");
        
        
        $player_styles_section->addStyleControl(
            array(
                "name" => __('Spinner thickness'),
                "property" => '--vm-spinner-thickness',
                "selector" => $vime_player_selector,
                "control_type" => 'slider-measurebox',
                "default" => '3',
                "condition" => 'video_source!=audio&&maybe_spinner=true'
            )
        )->setRange(1, 10, 1)
         ->setUnits("px");
        
        
        $player_styles_section->addStyleControl(
            array(
                "name" => __('Spin duration'),
                "property" => '--vm-spinner-spin-duration',
                "control_type" => 'slider-measurebox',
                "default" => '1',
                "selector" => $vime_player_selector,
                "condition" => 'video_source!=audio&&maybe_spinner=true'
            )
        )
        ->setUnits('s','s')
        ->setRange('0','3','.05');
        
        
        // Skeleton styles
        $player_styles_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Loading skeleton frame'),
                'slug' => 'maybe_skeleton',
                "condition" => 'video_source!=audio'
            )
            
        )->setValue(array( 
            "true" => "Enable", 
            "false" => "Disable",
            )
        )->setDefaultValue('false')
         ->rebuildElementOnChange();
        
        
        $player_styles_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('In-Builder skeleton visibility'),
                'slug' => 'builder_skeleton',
                "condition" => 'video_source!=audio&&maybe_skeleton=true'
            )
            
        )->setValue(array( 
            "visible" => "Visible", 
            "hidden" => "Hidden",
            )
        )->setDefaultValue('true')
         ->setValueCSS( array(
            "visible"  => ".extras-in-builder vime-skeleton {
                                opacity: 1;
                                visibility: visible;
                            }",
        ) );
        
        
        $player_styles_section->addStyleControl( 
           array(
                "name" => __('Skeleton color'),
                "property" => '--vm-skeleton-color',
                "selector" => $vime_player_selector,
                "control_type" => 'colorpicker',
                "condition" => 'video_source!=audio&&maybe_skeleton=true'
           )
        );
        
        $player_styles_section->addStyleControl( 
           array(
                "name" => __('Skeleton sheen color'),
                "property" => '--vm-skeleton-sheen-color',
                "selector" => $vime_player_selector,
                "control_type" => 'colorpicker',
                "condition" => 'video_source!=audio&&maybe_skeleton=true'
           )
        );

        $player_styles_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Load VimeJS via CDN'),
                'slug' => 'maybe_cdn',
            )
            
        )->setValue(array( 
            "true" => "Enable", 
            "false" => "Disable",
            )
        )->setDefaultValue('false');


        
        /**
         * Tooltips
         */ 
        $styling_tooltips_section = $this->addControlSection("styling_tooltips_section", __("Control Tooltips"), "assets/icon.png", $this);
        
        $styling_tooltips_section->addOptionControl(
            array(
                'type' => 'buttons-list',
                'name' => __('Tooltips'),
                'slug' => 'maybe_tooltip',
                "condition" => 'ui_type!=provider&&ui_type!=none'
            )
            
        )->setValue(array( 
            "enable" => "Enable", 
            "disable" => "Disable",
            )
        )->setValueCSS( array(
            "disable"  => "vime-tooltip {
                                display: none;
                            }",
        ) )->whiteList();
        
        $styling_tooltips_section->addStyleControl(
            array(
                "name" => __('Tooltip spacing'),
                "property" => '--vm-tooltip-spacing',
                "selector" => $vime_player_selector,
                "control_type" => 'slider-measurebox',
                "default" => '3',
                "condition" => 'ui_type!=provider&&ui_type!=none&&maybe_tooltip=enable'
            )
        )->setRange(0, 30, 1)
         ->setUnits("px");
        
        $styling_tooltips_section->addStyleControl( 
           array(
                "name" => __('Tooltip text color'),
                "property" => '--vm-tooltip-color',
                "selector" => $vime_player_selector,
                "control_type" => 'colorpicker',
               "condition" => 'ui_type!=provider&&ui_type!=none&&maybe_tooltip=enable'
           )
        );
        
        $styling_tooltips_section->addStyleControl( 
           array(
                "name" => __('Tooltip background color'),
                "property" => '--vm-tooltip-bg',
                "selector" => $vime_player_selector,
                "control_type" => 'colorpicker',
               "condition" => 'ui_type!=provider&&ui_type!=none&&maybe_tooltip=enable'
           )
        );
        
        $styling_tooltips_section->addStyleControl(
            array(
                "name" => __('Tooltip font size'),
                "property" => '--vm-tooltip-font-size',
                "selector" => $vime_player_selector,
                "control_type" => 'slider-measurebox',
                "condition" => 'ui_type!=provider&&ui_type!=none&&maybe_tooltip=enable'
            )
        )->setRange(0, 30, 1)
         ->setUnits("px");   
        
        
        $styling_tooltips_section->addStyleControl(
            array(
                "name" => __('Tooltip padding'),
                "property" => '--vm-tooltip-padding',
                "selector" => $vime_player_selector,
                "control_type" => 'slider-measurebox',
                "default" => '3',
                "condition" => 'ui_type!=provider&&ui_type!=none&&maybe_tooltip=enable'
            )
        )->setRange(0, 30, 1)
         ->setUnits("px");
        
        /**
         * Advanced
         */ 
        //$advanced_section = $this->addControlSection("advanced_section", __("Advanced"), "assets/icon.png", $this);
        
       
        
                
    }
    
    function customCSS() {
        
        $css = '';
        
        if( ! $this->css_added ) {
        
            $css = file_get_contents( plugin_dir_path(__FILE__) . 'assets/vime/vime.css' );

            $css .= '.oxy-pro-media-player {
                        position: relative;
                        width: 100%;
                    }

                    .oxy-pro-media-player vime-player {
                        --vm-slider-track-height: var(--extras-slider-height);
                        --vm-slider-thumb-height: var(--extras-slider-height);
                        --vm-slider-thumb-width: var(--extras-slider-height);
                        --vm-slider-track-focused-height: var(--extras-slider-height);
                        --vm-time-color: var(--vm-control-color);
                        --vm-player-font-family: inherit;
                        --vm-spinner-height: var(--extras-spinner-size); 
                        --vm-spinner-width: var(--extras-spinner-size); 
                        --vm-blocker-z-index: -1;
                    }

                    .oxy-pro-media-player_custom-image,
                    .woocommerce-page .oxy-pro-media-player_custom-image,
                    .woocommerce .oxy-pro-media-player_custom-image { 
                        object-fit: cover;
                        height: 100%;
                        width: 100%;
                        transiton: all .5s ease;
                        object-position: var(--extras-poster-left) var(--extras-poster-top); 
                    }

                    vime-custom-poster {
                        position: absolute;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background: #000;
                        z-index: var(--vm-poster-z-index);
                        display: inline-block;
                        pointer-events: none;
                        opacity: 0;
                        visibility: hidden;
                        transition: var(--vm-fade-transition);
                        object-fit: cover;
                    }

                    vime-custom-poster.active {
                        opacity: 1;
                        visibility: visible;
                    }


                    .oxy-pro-media-player_custom-image.hidden {
                        opacity: 0;
                        visibility: hidden;
                    }

                    .oxy-pro-media-player_play {
                        --vm-control-focus-bg: transparent;
                        --extras-play-icon-size: 60px;
                    }

                    .oxy-pro-media-player_play svg {
                        height: var(--extras-play-icon-size);
                        width: var(--extras-play-icon-size);
                    }

                    .oxy-pro-media-player vime-control button.notTouch:focus, 
                    .oxy-pro-media-player vime-control button.notTouch:hover, 
                    .oxy-pro-media-player vime-control button.notTouch[aria-expanded=true] {
                        transform: scale(calc(var(--vm-control-scale, 1)));
                    }

                    .oxy-pro-media-player_play ~ vime-controls[pin=center] {
                        display: none;
                    }

                    .oxy-pro-media-player_play button {
                        --vm-play-scale: 1;
                        transform: scale(var(--vm-play-scale));
                    }

                    .oxy-pro-media-player .oxy-pro-media-player_play vime-control button.notTouch:focus, 
                    .oxy-pro-media-player .oxy-pro-media-player_play vime-control button.notTouch:hover, 
                    .oxy-pro-media-player .oxy-pro-media-player_play vime-control button.notTouch[aria-expanded=true] {
                        transform: scale(var(--vm-play-scale));
                    }

                    .oxygen-builder-body .oxy-pro-media-player vime-player {
                        --spinner-opacity: 1;
                        --spinner-zindex: 999;
                        pointer-events: none;
                    }
                    
                    .oxy-pro-media-player vime-player.video.fullscreen {
                        height: 100%!important;
                    }
                    
                    .oxy-pro-media-player vime-click-to-play.hydrated {
                      pointer-events: auto;
                      display: inline-block;
                    }';
            
            $this->css_added = true;
            
        }
        
        return $css;
        
    } 
    
    function output_js() {

      wp_enqueue_script( 'extras-vime', plugin_dir_url(__FILE__) . 'assets/vime/@vime/core/dist/vime/vime.esm.js', '', '4.7.3.1' );

      wp_localize_script(
		'extras-vime',
		'xLightbox',
		[
			'vimeDir' => plugin_dir_url(__FILE__) . 'assets/vime/@vime/core/',
		]
	);

    }

    function output_cdn_js() {

        wp_enqueue_script( 'extras-vime', 'https://cdn.jsdelivr.net/npm/@vime/core@^4/dist/vime/vime.esm.js', '', '4.7.3' );
  
      }
    
    function output_listeners_js() { ?>

        <script type="text/javascript">
        jQuery(document).ready(oxygen_media_player);
        function oxygen_media_player($) {
            
            $('.oxy-pro-media-player').each(function(){

                const mediaPlayer = $(this);
                const player = $(this).find('vime-player')[0];

              // Listening to an event.
              player.addEventListener('vPlaybackStarted', (event) => {
                const currentTime = event.detail;  
                mediaPlayer.find('vime-custom-poster').removeClass('active');  
              });

              if (mediaPlayer.closest('.oxy-carousel-builder').length) {
                player.addEventListener('vReady', (event) => {
                    window.dispatchEvent(new Event('resize')); 
                });
              }
                
            });
            
        }
        </script>    
    <?php } 

}

add_filter("oxy_base64_encode_options", 
    function($items) { 
        $items=array_merge($items, array('oxy-pro-media-player_youtube_video_id','oxy-pro-media-player_vimeo_video_id','oxy-pro-media-player_video_url','oxy-pro-media-player_audio_url','oxy-pro-media-player_poster_image_urls','oxy-pro-media-player_poster_image_custom_urls')); 
        return $items;
    }
);

new ExtraMediaPlayer();