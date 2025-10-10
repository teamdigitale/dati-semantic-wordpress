<?php
namespace NinjaFileManager;

defined('ABSPATH') || exit;
/**
 * Plugin activate/deactivate logic
 */
class Plugin {
  protected static $instance = null;

  public static function getInstance() {
    if (null == self::$instance) {
      self::$instance = new self;
    }

    return self::$instance;
  }

  private function __construct() {
  }

  /** Plugin activated hook */
  public static function activate() {
    $first_time_active = get_option('njt_fs_first_time_active');
    $njt_fs_review = get_option('njt_fs_review');

    if ($first_time_active === false) {
      update_option('njt_fs_first_time_active', 1);
      if ($njt_fs_review !== false) return;
        update_option('njt_fs_review', time() + 3*60*60*24); //After 3 days show
    }

    $current_version = get_option('njt_fs_version');
    if ( version_compare(NJT_FS_BN_VERSION, $current_version, '>') ) {
      $filebirdCross = \FileBirdCross::get_instance('filebird', 'filebird+ninjateam', NJT_FS_BN_PLUGIN_URL, array('filebird/filebird.php', 'filebird-pro/filebird.php'));
      $filebirdCross->need_update_option();
      update_option('njt_fs_version', NJT_FS_BN_VERSION);
      if ($njt_fs_review !== false) return;
        update_option('njt_fs_review', time() + 3*60*60*24); //After 3 days show
    }
  }

  /** Plugin deactivate hook */
  public static function deactivate() {
  }
}
