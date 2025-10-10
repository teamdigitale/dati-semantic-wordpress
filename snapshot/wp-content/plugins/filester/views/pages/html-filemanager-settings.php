<?php
defined('ABSPATH') || exit;
$viewPathLanguage = NJT_FS_BN_PLUGIN_PATH . 'views/pages/html-filemanager-language.php';
$viewUserRoleRestrictions = NJT_FS_BN_PLUGIN_PATH . 'views/pages/html-filemanager-user-role-restrictions.php';
global $wp_roles;
if( isset( $_POST ) && !empty( $_POST ) && !empty($_POST['njt-settings-form-submit'])){
  if( ! wp_verify_nonce( $_POST['njt-fs-settings-security-token'] ,'njt-fs-settings-security-token')) wp_die();

  $this->options['njt_fs_file_manager_settings']['root_folder_path']  = !empty($_POST['root_folder_path']) ? str_replace("\\\\", "/", trim(sanitize_text_field($_POST['root_folder_path']))) : '';
  $this->options['njt_fs_file_manager_settings']['enable_htaccess'] =  isset($_POST['enable_htaccess']) ? sanitize_text_field($_POST['enable_htaccess']) : 0;
  $this->options['njt_fs_file_manager_settings']['enable_trash'] =  isset($_POST['enable_trash']) ? sanitize_text_field($_POST['enable_trash']) : 0;
  $this->options['njt_fs_file_manager_settings']['upload_max_size'] =  !empty($_POST['upload_max_size']) ? sanitize_text_field(trim($_POST['upload_max_size'])) : 0;
  $this->options['njt_fs_file_manager_settings']['fm_locale'] = !empty($_POST['fm_locale']) ? sanitize_text_field($_POST['fm_locale']) : 'en';
  $this->options['njt_fs_file_manager_settings']['list_user_alow_access'] = !empty($_POST['list_user_alow_access']) ? explode(',', sanitize_text_field($_POST['list_user_alow_access'])) : array();
}

?>
<div class="njt-fs-settings njt-fs-file-manager">
  <h1 id="njt-plugin-tabs" class="nav-tab-wrapper hide-if-no-js">
    <a href="javascript:void(0)" class="nav-tab nav-tab-active" data-tab="njt_fs_setting"><?php _e("Settings", 'filester'); ?></a>
    <a href="javascript:void(0)" class="nav-tab" data-tab="njt_fs_roles"><?php _e("User Role Restrictions", 'filester'); ?></a>
  </h1>
  <div class="njt-fs-settings-content">
    <form action="" class="njt-plugin-setting settings-form" method="POST">
      <!-- creat token -->
      <input type='hidden' name='njt-fs-settings-security-token'
        value='<?php echo wp_create_nonce('njt-fs-settings-security-token'); ?>'>

      <table class="form-table">
        <tr>
          <th><?php _e("Select User Roles to access", 'filester'); ?></th>
          <td>
            <div class="njt-fs-list-user njt-settting-width njt-fs-list-col4" style="line-height:2">
              <?php foreach ( $wp_roles->roles as $key=>$value ): ?>
                <?php if (is_multisite() && is_super_admin()) {?>
              <span class="list-col4-item">
                <input type="checkbox" class="fm-list-user-item" id="<?php echo $key; ?>" name="<?php echo $key; ?>"
                  data-name="<?php echo esc_attr($value['name']);?>" value="<?php echo $key; ?>">
                <label for="<?php echo $key; ?>"> <?php echo esc_html($value['name']); ?></label>
              </span>
              <?php } else {
                if ($key != 'administrator') {
              ?>
                
              <span class="list-col4-item">
                <input type="checkbox" class="fm-list-user-item" id="<?php echo $key; ?>" name="<?php echo $key; ?>"
                  data-name="<?php echo esc_attr($value['name']);?>" value="<?php echo $key; ?>">
                <label for="<?php echo $key; ?>"> <?php echo esc_html($value['name']); ?></label>
              </span>
              <?php  }}?>
              <?php endforeach; ?>
              <!-- Value to submit data -->
              <input type="hidden" name="list_user_alow_access" id="list_user_alow_access">
              <!-- Data saved after submit -->
              <input type="hidden" name="list_user_has_approved" id="list_user_has_approved"
                value="<?php echo esc_attr(implode(",", !empty($this->options['njt_fs_file_manager_settings']['list_user_alow_access']) ? $this->options['njt_fs_file_manager_settings']['list_user_alow_access'] : array()));?>">
            </div>
          </td>
        </tr>
        <!-- root Path -->
        <tr>
          <th><?php _e("Root Path", 'filester'); ?></th>
          <td>
            <textarea type='text' name='root_folder_path' id='root_folder_path' class="njt-settting-width"
              placeholder="ex: <?php echo (str_replace("\\", "/", ABSPATH)."wp-content");?>"> <?php  if( isset( $this->options['njt_fs_file_manager_settings']['root_folder_path'] ) && !empty( $this->options['njt_fs_file_manager_settings']['root_folder_path'] ) ) echo str_replace("\\", "/",esc_attr($this->options['njt_fs_file_manager_settings']['root_folder_path'])); ?></textarea>
            <div>
            <p class="description njt-settting-width">
              <?php _e("Default path is: "."<code>". str_replace("\\", "/", ABSPATH)."</code>", 'filester'); ?>
            </p>
            <p class="description njt-settting-width">
            <?php _e("Eg: If you want to set root path access is ". "<strong>wp-content</strong>". " folder. Just enter ", 'filester'); ?>
              <?php echo (str_replace("\\", "/", ABSPATH));?>wp-content
            </p>
          </div>
          </td>
        </tr>
        <!-- url Path -->
        <tr>
          <th><?php _e("Files URL", 'filester'); ?></th>
          <td>
            <textarea type='text' name='root_folder_url' id='root_folder_url' class="njt-settting-width"
              placeholder="ex: <?php echo (site_url());?>"> <?php  if( isset( $this->options['njt_fs_file_manager_settings']['root_folder_url'] ) && !empty( $this->options['njt_fs_file_manager_settings']['root_folder_url'] ) ) echo str_replace("\\", "/",esc_attr($this->options['njt_fs_file_manager_settings']['root_folder_url'])); ?></textarea>
            <div>
            <p class="description njt-settting-width">
              <?php _e("Default path is: "."<code>". str_replace("\\", "/", site_url())."</code>", 'filester'); ?>
            </p>
          </div>
          </td>
        </tr>
        <!-- Maximum Upload Size -->
        <tr>
          <th><?php _e("Maximum Upload Size", 'filester'); ?></th>
          <td>
            <input type="number" name="upload_max_size" id="upload_max_size" class="njt-fs-settting-width-half"
              value="<?php  if( isset( $this->options['njt_fs_file_manager_settings']['upload_max_size'] ) && !empty( $this->options['njt_fs_file_manager_settings']['upload_max_size'] )) echo esc_attr($this->options['njt_fs_file_manager_settings']['upload_max_size']); ?>">
            <strong><?php _e("MB", 'filester'); ?></strong>
            <div class="des-path njt-settting-width">
              <small>
                <?php _e("Default:", 'filester'); ?>
                <b><?php _e("0 means unlimited upload.", 'filester'); ?></b>
              </small>
            </div>
          </td>
        </tr>
        <!-- Select language -->
        <tr>
          <th><?php _e("Select language", 'filester'); ?></th>
          <td>
            <?php include_once $viewPathLanguage; ?>
          </td>
        </tr>
        <!-- .htaccess -->
        <tr>
          <th><?php _e("Hide .htaccess?", 'filester'); ?></th>
          <td>
            <label class="shortcode-switch" for="enable_htaccess">
              <input name="enable_htaccess" type="checkbox" id="enable_htaccess" value="1"
                <?php echo isset($this->options['njt_fs_file_manager_settings']['enable_htaccess']) && ($this->options['njt_fs_file_manager_settings']['enable_htaccess'] == '1') ? 'checked="checked"' : '';?>>

              <div class="slider round"></div>
            </label>


            <p class="description njt-settting-width">
              <?php _e("Will Hide .htaccess file (if exists) in file manager.", 'filester'); ?>
            </p>
          </td>
        </tr>
        <!-- Enable Trash? -->
        <tr>
          <th><?php _e("Enable Trash?", 'filester'); ?></th>
          <td>
            <label class="shortcode-switch" for="enable_trash">
              <input name="enable_trash" type="checkbox" id="enable_trash" value="1"
                <?php echo isset($this->options['njt_fs_file_manager_settings']['enable_trash']) && ($this->options['njt_fs_file_manager_settings']['enable_trash'] == '1') ? 'checked="checked"' : '';?>>
              <div class="slider round"></div>
            </label>

            <p class="description njt-settting-width">
              <?php _e("After enable trash, after delete your files will go to trash folder.", 'filester'); ?></p>
          </td>
        </tr>
        <!-- Sensitive Files Protection -->
        <tr>
          <th><?php _e("Sensitive Files Protection", 'filester'); ?></th>
          <td>
            <label class="shortcode-switch" for="enable_sensitive_protection">
              <input name="enable_sensitive_protection" type="checkbox" id="enable_sensitive_protection" value="1"
                <?php echo isset($this->options['njt_fs_file_manager_settings']['enable_sensitive_protection']) && ($this->options['njt_fs_file_manager_settings']['enable_sensitive_protection'] == '1') ? 'checked="checked"' : '';?>>
              <div class="slider round"></div>
            </label>
            <p class="description njt-settting-width">
              <?php _e("Protect sensitive files like wp-config.php, .env, etc. Only administrators can access.", 'filester'); ?>
            </p>
          </td>
        </tr>
        <!-- button submit -->
        <tr>
          <td></td>
          <td>
            <p class="submit">
              <button type="button" name="njt-settings-form-submit" id="submit"
                class="button button-primary njt-settings-form-submit"><?php _e("Save Changes", 'filester'); ?></button>
            </p>
          </td>
        </tr>
      </table>
    </form>
    <!-- include html User Role Restrictions -->
    <?php include_once $viewUserRoleRestrictions; ?>
  </div>
</div>