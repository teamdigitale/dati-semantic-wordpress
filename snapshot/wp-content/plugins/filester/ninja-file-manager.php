<?php
/**
 * Plugin Name: Filester - File Manager Pro
 * Plugin URI: https://ninjateam.org/filester
 * Description: Made to help you focus on WordPress file management and avoid being distracted.
 * Version: 2.0
 * Author: Ninja Team
 * Author URI: https://ninjateam.org
 * Text Domain: filester
 * Domain Path: /i18n/languages/
 *
 * @package BigPlugin
 */

namespace NinjaFileManager;

if (file_exists(dirname(__FILE__) . '/includes/File_manager/lib/php/autoload.php')) {
  require_once dirname(__FILE__) . '/includes/File_manager/lib/php/autoload.php';
}

if (file_exists(dirname(__FILE__) . '/includes/File_manager/FileManagerHelper.php')) {
  require_once dirname(__FILE__) . '/includes/File_manager/FileManagerHelper.php';
}

if (file_exists(dirname(__FILE__) . '/includes/Recommended/Recommended.php')) {
  require_once dirname(__FILE__) . '/includes/Recommended/Recommended.php';
}

if (file_exists(dirname(__FILE__) . '/includes/functions.php')) {
  require_once dirname(__FILE__) . '/includes/functions.php';
}



defined('ABSPATH') || exit;

define('NJT_FS_BN_PREFIX', 'njt-fs');
define('NJT_FS_BN_VERSION', '2.0');
define('NJT_FS_BN_DOMAIN', 'filester');

define('NJT_FS_BN_PLUGIN_DIR', basename(__DIR__));
define('NJT_FS_BN_PLUGIN_URL', plugin_dir_url(__FILE__));
define('NJT_FS_BN_PLUGIN_PATH', plugin_dir_path(__FILE__));

spl_autoload_register(function ($class) {
  $prefix = __NAMESPACE__; // project-specific namespace prefix
  $base_dir = __DIR__ . '/includes'; // base directory for the namespace prefix

  $len = strlen($prefix);
  if (strncmp($prefix, $class, $len) !== 0) { // does the class use the namespace prefix?
    return; // no, move to the next registered autoloader
  }

  $relative_class_name = substr($class, $len);

  // replace the namespace prefix with the base directory, replace namespace
  // separators with directory separators in the relative class name, append
  // with .php
  $file = $base_dir . str_replace('\\', '/', $relative_class_name) . '.php';

  if (file_exists($file)) {
    require $file;
  }
});

// Add crossale for filebird
if (file_exists(dirname(__FILE__) . '/includes/cross.php')) {
  require_once dirname(__FILE__) . '/includes/cross.php';
}

function init() {
  Plugin::activate();
  Plugin::getInstance();
  I18n::getInstance();
  File_manager\FileManager::getInstance();
}
add_action('plugins_loaded', 'NinjaFileManager\\init');

register_activation_hook(__FILE__, array('NinjaFileManager\\Plugin', 'activate'));
register_deactivation_hook(__FILE__, array('NinjaFileManager\\Plugin', 'deactivate'));

