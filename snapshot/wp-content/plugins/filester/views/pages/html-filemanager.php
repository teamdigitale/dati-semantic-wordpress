<?php
defined('ABSPATH') || exit;
$lang = !empty( $this->options['njt_fs_file_manager_settings']['fm_locale']) ? $this->options['njt_fs_file_manager_settings']['fm_locale'] : ''; 
?>
<div class="njt-fs-file-manager">
  <div class="njt-fs-select-theme">
    <div class="njt-fs-wrap njt-fs-mr0">
      <h1 class="wp-heading-inline njt-fs-pd0"><?php _e("Filester - WordPress File Manager Pro", 'filester'); ?></h1>
    </div>
    <div class="select-theme-content">
      <?php    
      $selectedTheme = get_option('njt_fs_selector_themes') && get_option('njt_fs_selector_themes')[$this->userRole]['themesValue'] ? get_option('njt_fs_selector_themes')[$this->userRole]['themesValue'] : null;
    ?>
      <div class="njt-fs-wrap njt-fs-mr0">
        <h3 class="wp-heading-inline select-theme-title"><?php _e("Select theme:", 'filester'); ?></h3>
      </div>
      <select name="selector-themes" id="selector-themes">
        <option value="Default"><?php _e("Default Elfinder", 'filester'); ?></option>
        <option value="dark-slim"><?php _e("Dark Slim", 'filester'); ?></option>
        <option value="Material"><?php _e("Material", 'filester'); ?></option>
        <option value="Material-Gray"><?php _e("Material Gray", 'filester'); ?></option>
        <option value="Material-Light"><?php _e("Material Light", 'filester'); ?></option>
        <option value="windows-10"><?php _e("Windows 10", 'filester'); ?></option>
      </select>
      <input type="hidden" name="selected-theme" value="<?php echo esc_attr($selectedTheme) ?>">

    </div>
  </div>

  <div class="clear"></div>
  <div id="njt-fs-file-manager">
  </div>
</div>
