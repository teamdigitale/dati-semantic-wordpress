<?php
namespace NinjaFileManager\File_manager;

defined('ABSPATH') || exit;

/**
 * Settings Page
 */

class FileManager
{
    protected static $instance = null;
    
    /**
     *
     * @var object $options The object of the options class
     *
     * */
    public $options;
    public $fmCapability = '';
    public $userRole = '';
    private $hook_suffix = array();
    
    public static function getInstance()
    {
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    private function __construct()
    {
        //get user role
        $user = wp_get_current_user();
        $this->userRole = $user && $user->roles && isset($user->roles[0]) ? $user->roles[0] : '';

        if ( empty($this->userRole) && isset($user->roles)) {
			$role = '';
			foreach( $user->roles as $key => $value) {
				$role = $value;
			}
	
			$this->userRole = $role;
		}
        
        // Loading Options
        // Options
		$this->options = get_option('njt_fs_settings');
        if(empty($this->options)) {
            $this->options = array( // Setting up default values
                'njt_fs_file_manager_settings' => array(
                    'root_folder_path' =>  ABSPATH,
                    'root_folder_url' => site_url(),
                    'enable_sensitive_protection' => '1'
                ),
            );
        }
        register_shutdown_function(array($this, 'saveOptions'));

        add_action('init', array($this, 'isAlowUserAccess'));
        if ($this->isAlowUserAccess()) {
            add_action('admin_enqueue_scripts', array($this, 'enqueueAdminScripts'));
            add_action('admin_menu', array($this, 'FileManager'));
            add_action('wp_ajax_fs_connector', array($this, 'fsConnector'));
            add_action('wp_ajax_selector_themes', array($this, 'selectorThemes'));
            add_action('wp_ajax_get_role_restrictions', array($this, 'getArrRoleRestrictions'));
            add_action('wp_ajax_njt_fs_save_setting', array($this, 'njt_fs_saveSetting'));
            add_action('wp_ajax_njt_fs_save_setting_restrictions', array($this, 'njt_fs_saveSettingRestrictions'));
            
            $optionReview = get_option('njt_fs_review');
            if (time() >= (int)$optionReview && $optionReview !== '0'){
                add_action('admin_notices', array($this, 'njt_fs_give_review'));
            }
            
            add_action('wp_ajax_njt_fs_save_review', array($this, 'njt_fs_save_review'));
       }
    }

    public function njt_fs_give_review()
    {
        if (function_exists('get_current_screen')) {
            if (get_current_screen()->id == 'file-manager_page_filester-settings' || get_current_screen()->id == 'toplevel_page_njt-fs-filemanager' || get_current_screen()->id == 'plugins') {
                $this->enqueue_scripts();
                ?>
                <div class="notice notice-success is-dismissible" id="njt-fs-review">
                    <h3><?php _e('Give Filester a review', 'filester')?></h3>
                    <p>
                        <?php _e('Thank you for choosing Filester. We hope you love it. Could you take a couple of seconds posting a nice review to share your happy experience?', 'filester')?>
                    </p>
                    <p>
                        <?php _e('We will be forever grateful. Thank you in advance ;)', 'filester')?>
                    </p>
                    <p>
                        <a href="javascript:;" data="rateNow" class="button button-primary" style="margin-right: 5px"><?php _e('Rate now', 'filester')?></a>
                        <a href="javascript:;" data="later" class="button" style="margin-right: 5px"><?php _e('Later', 'filester')?></a>
                        <a href="javascript:;" data="alreadyDid" class="button"><?php _e('Already did', 'filester')?></a>
                    </p>
                </div>
                <?php
            }
        }
    }

    public function njt_fs_save_review()
    {
        if ( isset( $_POST ) ) {
            $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : null;
            $field = isset( $_POST['field'] ) ? sanitize_text_field( $_POST['field'] ) : null;

            if ( ! wp_verify_nonce( $nonce, 'njt-fs-review' ) ) {
				wp_send_json_error( array( 'status' => 'Wrong nonce validate!' ) );
				exit();
            }
            
            if ($field == 'later'){
                update_option('njt_fs_review', time() + 3*60*60*24); //After 3 days show
            } else if ($field == 'alreadyDid'){
                update_option('njt_fs_review', 0);
            }
            wp_send_json_success();
        }
        wp_send_json_error( array( 'message' => 'Update fail!' ) );
    }

    public function enqueue_scripts(){
        wp_enqueue_script('njt-fs-review', NJT_FS_BN_PLUGIN_URL . 'assets/js/review.js', array('jquery'), NJT_FS_BN_VERSION, false);
        wp_localize_script('njt-fs-review', 'wpDataFs', array(
            'admin_ajax' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce("njt-fs-review"),
        ));
    }

    public function isAlowUserAccess()
    {
        if($this->userRole) {
            $allowed_roles = !empty($this->options['njt_fs_file_manager_settings']['list_user_alow_access']) ? $this->options['njt_fs_file_manager_settings']['list_user_alow_access'] : array();
            if( in_array($this->userRole,$allowed_roles)) {
                if (is_multisite() && $this->userRole == 'administrator') {
                    $this->fmCapability = 'activate_plugins';
                }
                $this->fmCapability = $this->userRole;
                return true;
            }
        }
        if (is_multisite() && is_super_admin()) {
            $this->fmCapability = 'create_sites';
            return true;
        } 

        if (!is_multisite() && is_super_admin()) {
            $this->fmCapability = 'administrator';
            return true;
        } 
        $this->fmCapability = 'read';
        return false;
    }

    public function FileManager()
    {
       if( class_exists( 'NestedPages' ) ) {
            $this->fmCapability = 'read';
       }
        $icon = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz48IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4iICJodHRwOi8vd3d3LnczLm9yZy9HcmFwaGljcy9TVkcvMS4xL0RURC9zdmcxMS5kdGQiPjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCI+PHBhdGggZD0iTTEwLDRINEMyLjg5LDQgMiw0Ljg5IDIsNlYxOEEyLDIgMCAwLDAgNCwyMEgyMEEyLDIgMCAwLDAgMjIsMThWOEMyMiw2Ljg5IDIxLjEsNiAyMCw2SDEyTDEwLDRaIiBmaWxsPSIjYTdhYWFkIi8+PC9zdmc+';
        $display_suffix = add_menu_page(
            __('Filester', 'textdomain'),
            'File Manager',
            $this->fmCapability,
            'njt-fs-filemanager',
            array($this, 'fsViewFileCallback'),
            $icon,
            9
        );
        if (is_multisite()) {
            $settings_suffix = add_submenu_page (
                'njt-fs-filemanager',
                'Settings',
                'Settings', 
                'create_sites', 
                'filester-settings',
                array($this, 'fsSettingsPage') );
        }
        
        if (!is_multisite()) {
            $settings_suffix = add_submenu_page (
                'njt-fs-filemanager',
                'Settings',
                'Settings', 
                'manage_options', 
                'filester-settings',
                array($this, 'fsSettingsPage') );
        }
       
        $this->hook_suffix = array($display_suffix, $settings_suffix);
    }

    public function fsViewFileCallback()
    {
        $viewPath = NJT_FS_BN_PLUGIN_PATH . 'views/pages/html-filemanager.php';
        include_once $viewPath;
    }

    public function fsSettingsPage()
    {
        $viewPath = NJT_FS_BN_PLUGIN_PATH . 'views/pages/html-filemanager-settings.php';
        include_once $viewPath;
    }

    public function enqueueAdminScripts($suffix)
    {
        if (in_array($suffix, $this->hook_suffix)) {
            $selectorThemes = get_option('njt_fs_selector_themes');
            if (empty($selectorThemes[$this->userRole])) {
                $selectorThemes[$this->userRole]['themesValue'] = 'Default';
                update_option('njt_fs_selector_themes', $selectorThemes);
            }
        
            $selectedTheme = $selectorThemes[$this->userRole]['themesValue'];

            //elfinder css
            wp_enqueue_style('elfinder.jq.css', plugins_url('/lib/jquery/jquery-ui.min.css', __FILE__));
            wp_enqueue_style('elfinder.full.css', plugins_url('/lib/css/elfinder.min.css', __FILE__));
            wp_enqueue_style('themes', plugins_url('/lib/css/theme.css', __FILE__));
            wp_enqueue_style('themes-selector', plugins_url('/lib/themes/' . $selectedTheme . '/css/theme.css', __FILE__));
        
            //elfinder core
            if(version_compare(get_bloginfo('version'),'5.6', '>=') ){
                wp_enqueue_script('jquery_min', plugins_url('/lib/jquery/jquery-ui.min.js', __FILE__));
            } else {
                wp_enqueue_script('jquery_min', plugins_url('/lib/jquery/jquery-ui-old.min.js', __FILE__));
            }          
            
            //elfinder js, toastr JS, css custom
            wp_register_style('njt_fs_toastr_css',NJT_FS_BN_PLUGIN_URL . 'assets/js/toastr/toastr.min.css');
            wp_enqueue_style('njt_fs_toastr_css');
            wp_enqueue_script('njt_fs_toastr_js', NJT_FS_BN_PLUGIN_URL . 'assets/js/toastr/toastr.min.js', array('jquery'), NJT_FS_BN_VERSION);

            wp_register_style('file_manager_admin_css',NJT_FS_BN_PLUGIN_URL . 'assets/css/file_manager_admin.css');
            wp_enqueue_style('file_manager_admin_css');
            wp_enqueue_script('file_manager_admin', NJT_FS_BN_PLUGIN_URL . 'assets/js/file_manager_admin.js', array('jquery'), NJT_FS_BN_VERSION, true);

            //js load elFinder
            wp_enqueue_script('njt_fs_elFinder', plugins_url('/lib/js/elfinder.min.js', __FILE__));

            wp_enqueue_script('njt_fs_elfinder_editor', plugins_url('/lib/js/extras/editors.default.js', __FILE__));
            //js load fm_locale
            if(isset($this->options['njt_fs_file_manager_settings']['fm_locale'])) {
                $locale = $this->options['njt_fs_file_manager_settings']['fm_locale'];
                if( !empty($locale) &&  $locale != 'en' && in_array($locale, njt_fs_locales(), true)) {
                    $locale = sanitize_file_name($locale);
                    wp_enqueue_script( 'njt_fs_fma_lang', plugins_url('lib/js/i18n/elfinder.'.$locale.'.js', __FILE__));
                }
            }

            wp_localize_script('njt_fs_elFinder', 'wpData', array(
                'admin_ajax' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce("njt-fs-file-manager-admin"),
                'PLUGIN_URL' => NJT_FS_BN_PLUGIN_URL .'includes/File_manager/lib/',
                'PLUGIN_PATH' => NJT_FS_BN_PLUGIN_PATH.'includes/File_manager/lib/',
                'PLUGIN_DIR'=> NJT_FS_BN_PLUGIN_DIR,
                'ABSPATH'=> str_replace("\\", "/", ABSPATH),
                'is_multisite' => is_multisite(),
                'lang' => !empty( $this->options['njt_fs_file_manager_settings']['fm_locale']) ? sanitize_file_name($this->options['njt_fs_file_manager_settings']['fm_locale']) : '',
                'nonce_connector' => wp_create_nonce('file-manager-security-token'),
            ));
        }
    }

    //File manager connector function

    public function fsConnector()
    {
        check_ajax_referer( 'file-manager-security-token', 'nonce' );
        $uploadMaxSize = isset($this->options['njt_fs_file_manager_settings']['upload_max_size']) && !empty($this->options['njt_fs_file_manager_settings']['upload_max_size']) ? $this->options['njt_fs_file_manager_settings']['upload_max_size'] : 0;

        $opts = array(
            'bind' => array(
                'put.pre' => array(new \FileManagerHelper, 'madeStripcslashesFile'), // Check endcode when save file.
            ),
            'debug' => false, 
            'roots' => array(
                array(
                    'driver' => 'LocalFileSystem',
                    'path' => isset($this->options['njt_fs_file_manager_settings']['root_folder_path']) && !empty($this->options['njt_fs_file_manager_settings']['root_folder_path']) ? $this->options['njt_fs_file_manager_settings']['root_folder_path'] : ABSPATH,
                    'URL' => isset($this->options['njt_fs_file_manager_settings']['root_folder_url']) && !empty($this->options['njt_fs_file_manager_settings']['root_folder_url']) ? $this->options['njt_fs_file_manager_settings']['root_folder_url'] : site_url(),
                    'trashHash'     => '', // default is empty, when not enable trash
                    'uploadMaxSize' =>  $uploadMaxSize .'M',
                    'winHashFix'    => DIRECTORY_SEPARATOR !== '/', 
                    'uploadOrder'   => array('deny', 'allow'),
                    'uploadDeny'    => array('htaccess'),
                    //'acceptedName' => 'validName',
                    'attributes' => array() // default is empty
                ),
            ),
        );

    

        //Enable Trash
        if(isset($this->options['njt_fs_file_manager_settings']['enable_trash']) && ($this->options['njt_fs_file_manager_settings']['enable_trash'] == '1')) {
            $trash = array(
                'id'            => '1',
                'driver'        => 'Trash',
                'path'          => NJT_FS_BN_PLUGIN_PATH.'includes/File_manager/lib/files/.trash/',
                'tmbURL'        => site_url() . '/includes/File_manager/lib/files/.trash/.tmb',
                'winHashFix'    => DIRECTORY_SEPARATOR !== '/', 
                'uploadDeny'    => array('htaccess'), 
                'uploadAllow'   => array('all'),
                'uploadOrder'   => array('deny', 'allow'),
               // 'acceptedName' => 'validName',
                'attributes' => array(
                    array(
                        'pattern' => '/.tmb/',
                        'read' => false,
                        'write' => false,
                        'hidden' => true,
                        'locked' => false
                    ),
                    array(
                        'pattern' => '/.gitkeep/',
                        'read' => false,
                        'write' => false,
                        'hidden' => true,
                        'locked' => false
                    )
                )
            );
            $opts['roots'][0]['trashHash'] = 't1_Lw';
            $opts['roots'][1] = $trash;
        }

        //Start --setting User Role Restrictions
        $user = wp_get_current_user();
        $userRoles =  $user && $user->roles && $user->roles[0] ? $user->roles[0] : '';
        
        //Disable Operations
        if(!empty($this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$this->userRole]['list_user_restrictions_alow_access'])){
            $opts['roots'][0]['disabled'] = $this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$this->userRole]['list_user_restrictions_alow_access'];
        }
        //Creat root path for user
        if(!empty($this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$this->userRole]['private_folder_access'])){
            $opts['roots'][0]['path'] = $this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$this->userRole]['private_folder_access'] .'/';
        }

         //Creat url root path for user
         if(!empty($this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$this->userRole]['private_url_folder_access'])){
            $opts['roots'][0]['URL'] = $this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$this->userRole]['private_url_folder_access'] .'/';
        }

        //Folder or File Paths That You want to Hide
        if(!empty($this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$this->userRole]['hide_paths'])){
            foreach ($this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$this->userRole]['hide_paths'] as $key => $value){
                $arrItemHidePath =  array( 
                     'pattern' => '~/'.$value.'~',
                     'read' => false,
                     'write' => false,
                     'hidden' => true,
                     'locked' => false
                   );
                   array_push($opts['roots'][0]['attributes'], $arrItemHidePath);
               };
        }

        //File extensions which you want to Lock
        if(!empty($this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$this->userRole]['lock_files'])){
            foreach ($this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$this->userRole]['lock_files'] as $key => $value){
                $arrItemLockFile =  array( 
                     'pattern' => '/'.$value.'/',
                     'read' => false,
                     'write' => false,
                     'hidden' => false,
                     'locked' => true
                   );
                   array_push($opts['roots'][0]['attributes'], $arrItemLockFile);
               };
        }

        //Enter file extensions which can be uploaded
        $flag = false;

       
       if (is_multisite()) {
        if( !current_user_can('create_sites') && empty($this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$this->userRole]['can_upload_mime'])) {
            $opts['roots'][0]['uploadDeny'] = array('all');
            $opts['roots'][0]['uploadAllow'] = array('');
        } else if ( !current_user_can('create_sites') && !empty($this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$this->userRole]['can_upload_mime'])) {
            $opts['roots'][0]['uploadDeny'] = array('all');
            $opts['roots'][0]['uploadAllow'] = array();
            $arrCanUploadMime = $this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$this->userRole]['can_upload_mime'];
            $mimeTypes = new \FileManagerHelper();
            $arrMimeTypes = $mimeTypes->getArrMimeTypes();
            foreach ($arrMimeTypes as $key => $value){
                if(in_array($key,$arrCanUploadMime)) {
                    $explodeValue = explode(',',$value);
                    foreach($explodeValue as $item) {
                        array_push($opts['roots'][0]['uploadAllow'], $item );
                    }
                }
          
            };
            foreach ($arrCanUploadMime as $value){
                if(strpos($value,"x-conference") !== false
                   || strpos($value,"video") !== false
                   || strpos($value,"text") !== false
                   || strpos($value,"model") !== false
                   || strpos($value,"message") !== false
                   || strpos($value,"image") !== false
                   || strpos($value,"font") !== false
                   || strpos($value,"chemical") !== false
                   || strpos($value,"audio") !== false
                   || strpos($value,"application") !== false
                   ) {
                    array_push($opts['roots'][0]['uploadAllow'], $value );
                }
            }

        } else {
            $opts['roots'][0]['uploadDeny'] = array();
            $opts['roots'][0]['uploadAllow'] = array('all');
        }
       }
       
       if (!is_multisite()) {
        if($this->userRole !== 'administrator' && empty($this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$this->userRole]['can_upload_mime'])) {
            $opts['roots'][0]['uploadDeny'] = array('all');
            $opts['roots'][0]['uploadAllow'] = array('');
        } else if ( $this->userRole !== 'administrator' && !empty($this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$this->userRole]['can_upload_mime'])) {
            $opts['roots'][0]['uploadDeny'] = array('all');
            $opts['roots'][0]['uploadAllow'] = array();
            $arrCanUploadMime = $this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$this->userRole]['can_upload_mime'];
            $mimeTypes = new \FileManagerHelper();
            $arrMimeTypes = $mimeTypes->getArrMimeTypes();
            foreach ($arrMimeTypes as $key => $value){
                if(in_array($key,$arrCanUploadMime)) {
                    $explodeValue = explode(',',$value);
                    foreach($explodeValue as $item) {
                        $listFileCanNotUpload = $mimeTypes->listFileCanNotUpload();
                        if(!in_array($item, $listFileCanNotUpload)) {
                            array_push($opts['roots'][0]['uploadAllow'], $item );
                        }
                    }
                }
          
            };
            foreach ($arrCanUploadMime as $value){
                if(strpos($value,"x-conference") !== false
                   || strpos($value,"video") !== false
                   || strpos($value,"text") !== false
                   || strpos($value,"model") !== false
                   || strpos($value,"message") !== false
                   || strpos($value,"image") !== false
                   || strpos($value,"font") !== false
                   || strpos($value,"chemical") !== false
                   || strpos($value,"audio") !== false
                   || strpos($value,"application") !== false
                   ) {
                    array_push($opts['roots'][0]['uploadAllow'], $value );
                }
            }

        } else {
            $opts['roots'][0]['uploadDeny'] = array();
            $opts['roots'][0]['uploadAllow'] = array('all');
        }
       }

        // Sensitive files protection
        if(isset($this->options['njt_fs_file_manager_settings']['enable_sensitive_protection']) && ($this->options['njt_fs_file_manager_settings']['enable_sensitive_protection'] == '1')) {
            $sensitive_files = apply_filters('njt_fs_sensitive_files', array(
                '.htaccess',
                'wp-config.php', 
                '.env',
                'wp-config-sample.php',
                'readme.html',
                'license.txt',
                'xmlrpc.php'
            ));

            foreach ($sensitive_files as $file) {
                $attributes = array(
                    'pattern' => '/' . preg_quote($file, '/') . '/',
                    'read' => $this->canAccessSensitiveFiles(),
                    'write' => $this->canEditSensitiveFiles(),
                    'hidden' => !$this->canAccessSensitiveFiles(),
                    'locked' => !$this->canEditSensitiveFiles()
                );
                array_push($opts['roots'][0]['attributes'], $attributes);
            }
        }

        // .htaccess
        if(isset($this->options['njt_fs_file_manager_settings']['enable_htaccess']) && ($this->options['njt_fs_file_manager_settings']['enable_htaccess'] == '1')) {
            $attributes = array(
                'pattern' => '/.htaccess/',
                'read' => true,
                'write' => false,
                'hidden' => true,
                'locked' => true
            );
            array_push($opts['roots'][0]['attributes'], $attributes);
        }

        //End --setting User Role Restrictions

        $connector = new \elFinderConnector(new \elFinder($opts));
        $connector->run();
        wp_die();
    }
    
    public function selectorThemes()
    {
        if( ! wp_verify_nonce( $_POST['nonce'] ,'njt-fs-file-manager-admin')) wp_die();
        check_ajax_referer('njt-fs-file-manager-admin', 'nonce', true);
        
        $themesValue = sanitize_text_field ($_POST['themesValue']);
        $selectorThemes = get_option('njt_fs_selector_themes');
        if (empty($selectorThemes[$this->userRole])) {
            $selectorThemes[$this->userRole]['themesValue'] = 'Default';
            update_option('njt_fs_selector_themes', $selectorThemes);
        }
       
        if ($selectorThemes[$this->userRole]['themesValue'] != $themesValue) {
            $selectorThemes[$this->userRole]['themesValue'] = $themesValue;
            update_option('njt_fs_selector_themes', $selectorThemes);
        }
        $selected_themes = get_option('njt_fs_selector_themes');
        $linkThemes = plugins_url('/lib/themes/' . $selected_themes[$this->userRole]['themesValue'] . '/css/theme.css', __FILE__);
        wp_send_json_success($linkThemes);
        wp_die();
    }

    public function saveOptions()
    {
        //if(isset($_POST['njt-settings-form-submit'])) {
           update_option('njt_fs_settings', $this->options);
            // if($u) {
            //     $this->f('?page=njt-fs-filemanager-settings&status=1');
            // } else {
            //     $this->f('?page=njt-fs-filemanager-settings&status=2');
            // }
       // }
    }

    public function f($u) {
		echo '<script>';
		echo 'window.location.href="'.$u.'"';
		echo '</script>';
	}
    
    public function getArrRoleRestrictions()
    {
        if(!wp_verify_nonce( $_POST['nonce'] ,'njt-fs-file-manager-admin')) wp_die();
        check_ajax_referer('njt-fs-file-manager-admin', 'nonce', true);
        $valueUserRole = filter_var($_POST['valueUserRole']) ? sanitize_text_field ($_POST['valueUserRole']) : '';
        $arrRestrictions = !empty($this->options['njt_fs_file_manager_settings']['list_user_role_restrictions']) ? $this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'] : array();
        $dataArrRoleRestrictions = array (
            'disable_operations' => implode(",", !empty($arrRestrictions[$valueUserRole]['list_user_restrictions_alow_access']) ? $arrRestrictions[$valueUserRole]['list_user_restrictions_alow_access'] : array()),
            'private_folder_access' => !empty($arrRestrictions[$valueUserRole]['private_folder_access']) ? str_replace("\\\\", "/", trim($arrRestrictions[$valueUserRole]['private_folder_access'])) : '',
            'private_url_folder_access' => !empty($arrRestrictions[$valueUserRole]['private_url_folder_access']) ? str_replace("\\\\", "/", trim($arrRestrictions[$valueUserRole]['private_url_folder_access'])) : '',
            'hide_paths' => implode(',', !empty($arrRestrictions[$valueUserRole]['hide_paths']) ? $arrRestrictions[$valueUserRole]['hide_paths'] : array()),
            'lock_files' => implode(',', !empty($arrRestrictions[$valueUserRole]['lock_files']) ? $arrRestrictions[$valueUserRole]['lock_files'] : array()),
            'can_upload_mime' => implode(',', !empty($arrRestrictions[$valueUserRole]['can_upload_mime']) ? $arrRestrictions[$valueUserRole]['can_upload_mime'] : array())
        );
        wp_send_json_success($dataArrRoleRestrictions);
        wp_die();
    }

    public function njt_fs_saveSetting()
    {
        if( ! wp_verify_nonce( $_POST['nonce'] ,'njt-fs-file-manager-admin')) wp_die();
        check_ajax_referer('njt-fs-file-manager-admin', 'nonce', true);

        if (!current_user_can('manage_options')) {
            wp_die();
        }

        $root_folder_path = !empty($_POST['root_folder_path']) ? str_replace("\\\\", "/", trim(sanitize_text_field($_POST['root_folder_path']))) : '';
        $root_folder_url = !empty($_POST['root_folder_url']) ? str_replace("\\\\", "/", trim(sanitize_url($_POST['root_folder_url']))) : site_url();
        $list_user_alow_access = !empty($_POST['list_user_alow_access']) ? explode(',', sanitize_text_field($_POST['list_user_alow_access'])) : array();
        $upload_max_size = !empty($_POST['upload_max_size']) ? sanitize_text_field(trim($_POST['upload_max_size'])) : 0;
        $fm_locale = !empty($_POST['fm_locale']) ? sanitize_text_field($_POST['fm_locale']) : 'en';
        $enable_htaccess =  isset($_POST['enable_htaccess']) && $_POST['enable_htaccess'] == 'true' ? 1 : 0;
        $enable_trash = isset($_POST['enable_trash']) && $_POST['enable_trash'] == 'true' ? 1 : 0;
        $enable_sensitive_protection = isset($_POST['enable_sensitive_protection']) && $_POST['enable_sensitive_protection'] == 'true' ? 1 : 0;
        //save options
        $this->options['njt_fs_file_manager_settings']['root_folder_path'] = $root_folder_path;
        $this->options['njt_fs_file_manager_settings']['root_folder_url'] = $root_folder_url;
        $this->options['njt_fs_file_manager_settings']['list_user_alow_access'] = $list_user_alow_access;
        $this->options['njt_fs_file_manager_settings']['upload_max_size'] = $upload_max_size;
        $this->options['njt_fs_file_manager_settings']['fm_locale'] = $fm_locale;
        $this->options['njt_fs_file_manager_settings']['enable_htaccess'] = $enable_htaccess;
        $this->options['njt_fs_file_manager_settings']['enable_trash'] = $enable_trash;
        $this->options['njt_fs_file_manager_settings']['enable_sensitive_protection'] = $enable_sensitive_protection;
        //update options
        update_option('njt_fs_settings', $this->options);
        wp_send_json_success(get_option('njt_fs_settings'));
        wp_die();
    }

    public function njt_fs_saveSettingRestrictions() {
        if( ! wp_verify_nonce( $_POST['nonce'] ,'njt-fs-file-manager-admin')) wp_die();
        check_ajax_referer('njt-fs-file-manager-admin', 'nonce', true);

        if (!current_user_can('manage_options')) {
            wp_die();
        }

        if(! $_POST['njt_fs_list_user_restrictions']) wp_die();

        $njt_fs_list_user_restrictions = sanitize_text_field($_POST['njt_fs_list_user_restrictions']);
        $list_user_restrictions_alow_access = !empty($_POST['list_user_restrictions_alow_access']) ? explode(',', sanitize_text_field($_POST['list_user_restrictions_alow_access'])) : array();
        $private_folder_access = !empty($_POST['private_folder_access']) ? str_replace("\\\\", "/", trim(sanitize_text_field($_POST['private_folder_access']))) : '';
        $private_url_folder_access = !empty($_POST['private_url_folder_access']) ? str_replace("\\\\", "/", trim(sanitize_text_field($_POST['private_url_folder_access']))) : '';
        $hide_paths = !empty($_POST['hide_paths']) ? explode('|', preg_replace('/\s+/', '', sanitize_text_field($_POST['hide_paths']))) : array();
        $lock_files = !empty($_POST['lock_files']) ? explode('|', preg_replace('/\s+/', '', sanitize_text_field($_POST['lock_files']))) : array();

        $can_upload_mime = !empty($_POST['can_upload_mime']) ? explode(',', preg_replace('/\s+/', '', sanitize_text_field($_POST['can_upload_mime']))) : array();

        $can_upload_mime = array_filter($can_upload_mime, function($item) {
            $helper = new \FileManagerHelper();
            $listFileCanNotUpload = $helper->listFileCanNotUpload();
            return !in_array($item, $listFileCanNotUpload);
        });

        //save options
        $this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$njt_fs_list_user_restrictions]['list_user_restrictions_alow_access'] = $list_user_restrictions_alow_access;
        $this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$njt_fs_list_user_restrictions]['private_folder_access'] = $private_folder_access;
        $this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$njt_fs_list_user_restrictions]['private_url_folder_access'] = $private_url_folder_access;
        $this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$njt_fs_list_user_restrictions]['hide_paths'] = $hide_paths;
        $this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$njt_fs_list_user_restrictions]['lock_files'] = $lock_files;
        $this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$njt_fs_list_user_restrictions]['can_upload_mime'] = $can_upload_mime;
        //update options
        update_option('njt_fs_settings', $this->options);
        wp_send_json_success(get_option('njt_fs_settings'));
        wp_die();
    }

    public function canAccessSensitiveFiles() {
        // Filter hook for developers
        if (apply_filters('njt_fs_allow_sensitive_access', false)) {
            return true;
        }
        
        return false;
    }

    public function canEditSensitiveFiles() {
        // Filter hook for developers
        if (apply_filters('njt_fs_allow_sensitive_edit', false)) {
            return true;
        }
        
        return false;
    }

}