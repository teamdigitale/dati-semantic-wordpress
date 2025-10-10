<?php
defined('ABSPATH') || exit;

if ( !class_exists('NjtCross') ) {
    class NjtCross
    {
        public $pluginPrefix = '';
        public $pluginInstallSearching = '';
        public $pluginDirURL = '';
        public $pluginFolderSlug = '';
        
        public $showPopup = false;

        protected static $instance = null;

        public function __construct($pluginPrefix, $pluginInstallSearching, $pluginDirURL, $pluginFolderSlug)
        {
            $this->pluginPrefix = $pluginPrefix;
            $this->pluginInstallSearching = $pluginInstallSearching;
            $this->pluginDirURL = $pluginDirURL;
            $this->pluginFolderSlug = $pluginFolderSlug;
        }

        public static function get_instance($pluginPrefix, $pluginInstallSearching, $pluginDirURL, $pluginFolderSlug)
        {
            if (null == self::$instance) {
                self::$instance = new static($pluginPrefix, $pluginInstallSearching, $pluginDirURL, $pluginFolderSlug);
                self::$instance->doHooks();
            }
            return self::$instance;
        }
    
        public function is_plugin_exist() { 
            if ( ! function_exists( 'get_plugins' ) ) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }
            $all_plugins = get_plugins();

            if (is_array($this->pluginFolderSlug)) {
                foreach ($this->pluginFolderSlug as $slug) {
                    if (array_key_exists($slug, $all_plugins)) return true;
                }
            } else {
                if (array_key_exists($this->pluginFolderSlug, $all_plugins)) return true;
            }


            return false; 
        }
    
        public function doHooks() {
            add_action('init', function(){
                if (!$this->is_plugin_exist()) {
                    $notificationOption = get_option("njt_notification_{$this->pluginPrefix}_cross"); //Save the next time notification will appear
                    $popupOption        = get_option("njt_popup_{$this->pluginPrefix}_cross"); //Save the next time notification will appear
         
                    if ($notificationOption === false || time() >= $notificationOption) {
                        add_action('admin_notices', array($this, 'add_notification'));
                        add_action("wp_ajax_njt_{$this->pluginPrefix}_cross_notification", array($this, 'ajax_set_notification'));
                    }

                    if ($popupOption === false || time() >= $popupOption) {
                        $this->showPopup = true;
                    }

                    add_action('wp_dashboard_setup', array($this, 'add_dashboard'));
                    add_action('admin_footer', array($this, 'add_global_script_styles'));
                    add_action("wp_ajax_njt_{$this->pluginPrefix}_cross_install", array($this, 'ajax_install_plugin'));
                    add_action("wp_ajax_njt_{$this->pluginPrefix}_cross_hide", array($this, 'ajax_hide_cross'));
                }
            });
        }

        public function need_update_option(){ 
            $time = time() +  (1 * 60 * 60 * 24); //1 day
            update_option("njt_popup_{$this->pluginPrefix}_cross", $time);
            update_option("njt_notification_{$this->pluginPrefix}_cross", $time);
        }

        public function add_global_script_styles()
        {
            if ( function_exists('current_user_can') && current_user_can('install_plugins') ) {
                $nonce = wp_create_nonce('install-plugin_' . $this->pluginPrefix);
                $url   = self_admin_url('update.php?action=install-plugin&plugin=' . $this->pluginPrefix . '&_wpnonce=' . $nonce);
            } else {
                $url = admin_url("plugin-install.php?s={$this->pluginInstallSearching}&tab=search&type=term");
            }

            if ( function_exists('get_current_screen') ) {
                $screen = get_current_screen();
                if ( !in_array($screen->id , array('plugins', 'dashboard', 'upload')) ) return;
            } else return;

            wp_register_script("njt-popup-{$this->pluginPrefix}-cross", $this->pluginDirURL . '/assets/js/cross.js', ['jquery'], '1.0', true);
            wp_localize_script("njt-popup-{$this->pluginPrefix}-cross", 'njtCross', array(
                'nonce' => wp_create_nonce("njt_{$this->pluginPrefix}_cross_nonce"),
                'media_url' => admin_url('upload.php'),
                'filebird_install_url' => $url,
                'show_popup' => $this->showPopup
            ));
            wp_enqueue_script("njt-popup-{$this->pluginPrefix}-cross");
            ?>
            <style>
                @-webkit-keyframes rotate360{to{-webkit-transform:rotate(1turn);transform:rotate(1turn)}}@keyframes rotate360{to{-webkit-transform:rotate(1turn);transform:rotate(1turn)}}@-webkit-keyframes dotLoad{0%{opacity:1}to{opacity:.1}}@keyframes dotLoad{0%{opacity:1}to{opacity:.1}}.fbv-icon{background-color:transparent;background-position:50%;background-repeat:no-repeat;background-size:contain;display:inline-block;height:1em;width:1em}.fbv-i-folder{background-image:url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath fill='%23fff' d='M10 4H4c-1.11 0-2 .89-2 2v12a2 2 0 002 2h16a2 2 0 002-2V8a2 2 0 00-2-2h-8l-2-2z'/%3E%3C/svg%3E")}.fbv-cross-wrap{bottom:45px;position:fixed;right:30px;-webkit-transition:all .4s ease;-o-transition:all .4s ease;transition:all .4s ease;-webkit-transition-delay:.5s;-o-transition-delay:.5s;transition-delay:.5s;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;z-index:100000}.fbv-cross-wrap.fbv_permanent_hide{opacity:0;pointer-events:none}.fbv-cross-link{color:#a1a1a1;font-size:12px;text-decoration:none}.fbv-cross-link:active,.fbv-cross-link:focus,.fbv-cross-link:hover{-webkit-box-shadow:none;box-shadow:none;color:#a1a1a1;opacity:.8;outline:none}.fbv-cross-popup{cursor:pointer;position:relative;z-index:100}.fbv-cross-icon-wrap{background-color:#0085ba;-webkit-box-shadow:0 6px 10px 2px rgba(0,0,0,.1);box-shadow:0 6px 10px 2px rgba(0,0,0,.1);line-height:1;position:relative;height:56px;width:56px;border-radius:56px}.fbv-cross-icon-wrap i{color:#fff;font-size:32px;left:50%;margin-left:-16px;margin-top:-16px;position:absolute;top:50%;-webkit-transition:all .4s ease;-o-transition:all .4s ease;transition:all .4s ease}.fbv-cross-popup-open .fbv-cross-icon-wrap i.fbv-icon{opacity:0;-webkit-transform:rotate(1turn);-ms-transform:rotate(1turn);transform:rotate(1turn)}.fbv-cross-icon-wrap i.dashicons{opacity:0;-webkit-transform:rotate(0);-ms-transform:rotate(0);transform:rotate(0);height:auto;width:auto}.fbv-cross-popup-open .fbv-cross-icon-wrap i.dashicons{opacity:1;-webkit-transform:rotate(1turn);-ms-transform:rotate(1turn);transform:rotate(1turn)}.fbv-cross-sub{background-color:#fff;border-radius:3px;-webkit-box-shadow:0 2px 10px 0 rgba(0,0,0,.1);box-shadow:0 2px 10px 0 rgba(0,0,0,.1);color:#0085ba;font-size:14px;font-weight:500;margin:-13px 10px 0 0;padding:4px 12px;position:absolute;right:100%;top:50%;-webkit-transition:all .4s ease;-o-transition:all .4s ease;transition:all .4s ease;white-space:nowrap}.fbv-cross-popup-open .fbv-cross-sub{opacity:0;pointer-events:none;-webkit-transform:translateY(15px);-ms-transform:translateY(15px);transform:translateY(15px);visibility:hidden}.fbv-cross-window{background-color:#fff;border-radius:3px;bottom:100%;-webkit-box-shadow:0 10px 10px 4px rgba(0,0,0,.04);box-shadow:0 10px 10px 4px rgba(0,0,0,.04);margin-bottom:15px;opacity:0;pointer-events:none;position:absolute;right:-5px;-webkit-transform:translateY(50px);-ms-transform:translateY(50px);transform:translateY(50px);-webkit-transition:all .4s ease;-o-transition:all .4s ease;transition:all .4s ease;visibility:hidden;width:360px;z-index:99}.fbv-cross-window-mess{background-color:#0085ba;border-radius:3px 3px 0 0;color:#fff;padding:15px 20px}.fbv-cross-window-mess h3{color:#fff;font-size:14px;margin:0 0 10px}.fbv-cross-window-mess span{font-size:14px;line-height:1.5;opacity:.9}.fbv-cross-window-img-wrap{padding:20px}.fbv-cross-window-img-wrap img{max-width:100%}.fbv-cross-window-btn{padding:5px 20px 25px;text-align:center}.fbv-cross-window-btn .button-primary{-webkit-box-align:center;-ms-flex-align:center;align-items:center;display:-webkit-inline-box;display:-ms-inline-flexbox;display:inline-flex;font-weight:500;height:42px;-webkit-box-pack:center;-ms-flex-pack:center;justify-content:center;margin-bottom:10px;max-width:100%;min-width:162px;padding:0 20px}.fbv-cross-window-btn .button-primary,.fbv-cross-window-btn .button-primary:active,.fbv-cross-window-btn .button-primary:focus,.fbv-cross-window-btn .button-primary:hover{-webkit-box-shadow:none;box-shadow:none;outline:none}.fbv-cross-window-btn .button-primary i{margin-right:8px}.fbv-cross-window-btn .button-primary .dashicons-saved{background-color:#fff;color:#0085ba;font-size:18px;height:18px;width:18px;border-radius:18px}.fbv-cross-window-btn .button-primary.fbv_installing,.fbv-cross-window-btn .button-primary.fbv_installing:active,.fbv-cross-window-btn .button-primary.fbv_installing:focus,.fbv-cross-window-btn .button-primary.fbv_installing:hover{background-color:#e4f7ff;border-color:#e4f7ff;color:#0085ba;cursor:not-allowed}.fbv-cross-window-btn .button-primary.fbv_installing i{-webkit-animation:rotate360 1s linear infinite both;animation:rotate360 1s linear infinite both}.fbv-cross-popup-open .fbv-cross-window{opacity:1;pointer-events:all;-webkit-transform:translateY(0);-ms-transform:translateY(0);transform:translateY(0);visibility:visible}.fbv-noti-install-failed{margin-bottom:10px;margin-top:5px}.fbv-noti-install-failed a{font-weight:600}.fbv-label-error{color:#e90808;margin-bottom:2px}.text-dots:after,.text-dots:before{content:"."}.text-dots:after,.text-dots:before,.text-dots span{-webkit-animation:dotLoad 1s linear 1s infinite alternate;animation:dotLoad 1s linear 1s infinite alternate;opacity:.1}.text-dots:before{-webkit-animation-delay:.5s;animation-delay:.5s}.text-dots:after{-webkit-animation-delay:1.5s;animation-delay:1.5s}
            </style>
            <?php
        }

        public function add_notification() {
            if ( function_exists('get_current_screen') ) {
                $screen = get_current_screen();
                if ( !in_array ($screen->id, array('plugins')) ) return;
            } else return;
        
            if ( function_exists('current_user_can') && current_user_can('install_plugins') ) {
                $nonce = wp_create_nonce('install-plugin_' . $this->pluginPrefix);
                $url   = self_admin_url('update.php?action=install-plugin&plugin=' . $this->pluginPrefix . '&_wpnonce=' . $nonce);
            } else {
                $url = admin_url("plugin-install.php?s={$this->pluginInstallSearching}&tab=search&type=term");
            }
            ?>
            <div class="notice notice-info is-dismissible" id="njt-ads-wrapper">
                <div class="njt-d-row njt-justify-between">
                    <div class="njt-ads-info">
                    <h4 class="njt-ads-title"><?php _e('Recommend', 'ninjateam-whatsapp') ?></h4>
                    <p>To easily manage your files in WordPress media library with folders, please try FileBird plugin.</p>
                    <div class="njt-btn-row">
                        <a class="button button-primary" target="_blank" rel="noopener noreferrer" href="<?php echo esc_url($url) ?>">
                            <strong>I'm feeling lucky</strong>
                        </a>
                        <a class="button button-secondary" target="_blank" rel="noopener noreferrer" href="https://1.envato.market/FileBird-Premium-WP">
                            <strong>Go FileBird Pro</strong>
                        </a>
                        <a class="fbv-cross-link fbv-cross-hide-notification" href="javascript:;">
                            No, thanks
                        </a>
                    </div>
                    </div>
                    <img class="njt-ads-img" src="<?php echo NJT_FS_BN_PLUGIN_URL . '/assets/img/filebird.png' ?>" alt="filebird">
                </div>
            </div>
            <style>
                .njt-d-row{-webkit-box-align:center;-ms-flex-align:center;align-items:center;display:-webkit-box;display:-ms-flexbox;display:flex}.njt-justify-between{-webkit-box-pack:justify;-ms-flex-pack:justify;justify-content:space-between}.njt-ads-info{padding:15px 0 30px}@media screen and (max-width:782px){.njt-ads-info{padding:0}}.njt-ads-info p{margin-bottom:25px}.njt-ads-title{font-size:16px;margin:0 0 15px}.njt-ads-img{max-width:252px}.njt-btn-row{line-height:30px}.njt-btn-row>.button{margin-right:10px}
            </style>
            <?php
        }

        public function add_dashboard(){
            wp_add_dashboard_widget( 'dashboard_widget', 'Recommended', array($this, 'add_dashboard_widget') );
        }

        public function add_dashboard_widget(){
            ?>
            <style>
                #dashboard-widgets .njt-postbox-title-wrap {
                    margin: 15px 0;
                }
                #dashboard-widgets .njt-postbox-title-wrap>h3 {
                    font-size: 14px;
                    font-weight: 600;
                    padding: 0;
                    margin: 0 0 10px;
                    border: 0;
                }
                #dashboard-widgets .njt-postbox-title-wrap>span {
                        font-size: 14px;
                opacity: .9;
                    }
                #dashboard-widgets .fbv-cross-window-img-wrap {
                padding: 10px 0 20px;
                }
                .fbv-cross-go-pro{
                    font-weight: bold;
                    color: #2c7cb9;
                }
            </style>
            <div class="njt-wrap-postbox">
              <div class="njt-postbox-title-wrap">
                <h3>Your WordPress media library is messy?</h3>
                <span>Start using FileBird to organize your files into folders by drag and drop.</span>
              </div>
              <div class="fbv-cross-window-img-wrap">
                <img src="https://ps.w.org/filebird/assets/screenshot-2.gif" alt="screenshot_demo">
              </div>
              <div class="fbv-cross-window-btn">
                <div><a class="button button-primary fbv-cross-install" href="javascript:;"><i class="dashicons dashicons-wordpress-alt"></i>Install for free</a></div>
                <div><a class="fbv-cross-go-pro" href="https://1.envato.market/FileBird-Pro-WP" target="_blank" rel="noopener noreferrer">Go Pro</a></div>
              </div>
            </div>
            <?php
        }

        public function ajax_install_plugin(){
            check_ajax_referer("njt_filebird_cross_nonce", 'nonce', true);

            if (!current_user_can('install_plugins') || !current_user_can('activate_plugins')) {
                wp_send_json_error(array('message' => _('You do not have permission to install plugins.')));
                wp_die();
            }
            
            $installed = $this->pluginInstaller('filebird');
            if ($installed === false) {
                wp_send_json_error(array('message' => $installed));
            }
            try {
                $result = activate_plugin('filebird/filebird.php');

                if (is_wp_error($result)) {
                    throw new \Exception($result->get_error_message());
                }
                wp_send_json_success();
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
        }

        public function pluginInstaller ($slug) {
            require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
            require_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
            require_once( ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php' );
            require_once( ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php' );

            $api = plugins_api( 'plugin_information',
                array(
                    'slug' => $slug,
                    'fields' => array(
                        'short_description' => false,
                        'sections' => false,
                        'requires' => false,
                        'rating' => false,
                        'ratings' => false,
                        'downloaded' => false,
                        'last_updated' => false,
                        'added' => false,
                        'tags' => false,
                        'compatibility' => false,
                        'homepage' => false,
                        'donate_link' => false,
                    ),
                )
            );

            if (is_wp_error($api)) {
                throw new \Exception(_('Could not fetch plugin information.'));
            }
            
            $skin     = new \WP_Ajax_Upgrader_Skin();
            $upgrader = new \Plugin_Upgrader( $skin );
            try {
                $result = $upgrader->install( $api->download_link );

                if (is_wp_error($result)) {
                    throw new \Exception($result->get_error_message());
                }

                return true;
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }

            return false;
        }

        public function ajax_set_notification(){
            check_ajax_referer("njt_{$this->pluginPrefix}_cross_nonce", 'nonce', true);
            //Save after 30 days
            update_option("njt_notification_{$this->pluginPrefix}_cross", time() + (30 * 60 * 60 * 24));
            wp_send_json_success();
        }

        public function ajax_hide_cross(){
            check_ajax_referer("njt_{$this->pluginPrefix}_cross_nonce", 'nonce', true);
            
            $type = sanitize_text_field($_POST['type']);
            $time = time() + (30 * 60 * 60 * 24); // hide 30 days
            
            update_option("njt_{$type}_{$this->pluginPrefix}_cross", $time); 
            wp_send_json_success();
        }
    }
}

if ( !class_exists('FileBirdCross') ) {
    class FileBirdCross extends NjtCross {
        public function is_plugin_exist()
        {
            return ( 
                defined('NJT_FILEBIRD_VERSION') || 
                defined('NJFB_VERSION') || 
                parent::is_plugin_exist() 
            );
        }
    }

    FileBirdCross::get_instance('filebird', 'filebird+ninjateam', NJT_FS_BN_PLUGIN_URL, array('filebird/filebird.php', 'filebird-pro/filebird.php'));
}



