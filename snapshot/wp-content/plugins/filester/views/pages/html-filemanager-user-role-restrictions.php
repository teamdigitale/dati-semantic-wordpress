<?php
defined('ABSPATH') || exit;
$viewListOperations = NJT_FS_BN_PLUGIN_PATH . 'views/pages/html-filemanager-list-operations.php';
$listUserApproved = !empty($this->options['njt_fs_file_manager_settings']['list_user_alow_access']) ? $this->options['njt_fs_file_manager_settings']['list_user_alow_access'] : array();

if (isset($_POST) && !empty($_POST) && !empty($_POST['njt-form-user-role-restrictionst'])) {
  if (!wp_verify_nonce($_POST['njt-fs-user-restrictions-security-token'], 'njt-fs-user-restrictions-security-token')) {
      wp_die();
  }
  if(!empty($_POST['njt-fs-list-user-restrictions'])) {

    $userRoleRestrictedSubmited = !empty($_POST['njt-fs-list-user-restrictions']) ? sanitize_text_field($_POST['njt-fs-list-user-restrictions']) : '';
    
    if (empty($this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'])) {
      $this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'] = array();
    }

    //Save data list User Restrictions alow access
    $this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$_POST['njt-fs-list-user-restrictions']]['list_user_restrictions_alow_access'] = 
      !empty($_POST['list_user_restrictions_alow_access']) ?
      explode(',', sanitize_text_field($_POST['list_user_restrictions_alow_access'])) : array();
    //Seperate or private folder access
    $this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$_POST['njt-fs-list-user-restrictions']]['private_folder_access'] =
      !empty($_POST['private_folder_access']) ?
      str_replace("\\\\", "/", trim(sanitize_text_field($_POST['private_folder_access']))) : '';
    //Save data Enter Folder or File Paths That You want to Hide
    $this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$_POST['njt-fs-list-user-restrictions']]['hide_paths'] = 
      !empty($_POST['hide_paths']) ?
      explode('|', preg_replace('/\s+/', '', sanitize_text_field($_POST['hide_paths']))) : array();
    //Save data Enter file extensions which you want to Lock
    $this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$_POST['njt-fs-list-user-restrictions']]['lock_files'] =
      !empty($_POST['lock_files']) ?
      explode('|', preg_replace('/\s+/', '', sanitize_text_field($_POST['lock_files']))) : array();
    //Enter file extensions which can be uploaded
    $this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$_POST['njt-fs-list-user-restrictions']]['can_upload_mime'] =
      !empty($_POST['can_upload_mime']) ?
      explode(',', preg_replace('/\s+/', '', sanitize_text_field($_POST['can_upload_mime']))) : array();
  }
}

$arrRestrictions = !empty($this->options['njt_fs_file_manager_settings']['list_user_role_restrictions']) ? $this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'] : array();
if (count($arrRestrictions) > 0) {
  $firstKeyRestrictions = !empty($userRoleRestrictedSubmited) ? $userRoleRestrictedSubmited : $listUserApproved[0];
} else {
  $firstKeyRestrictions = '';
}
?>

<form action="" class="njt-plugin-setting form-user-role-restrictions" method="POST">
  <!-- creat token -->
  <input type='hidden' name='njt-fs-user-restrictions-security-token'
    value='<?php echo wp_create_nonce('njt-fs-user-restrictions-security-token'); ?>'>
  <table class="form-table">
    <tr>
      <th><?php _e("If User Role is", 'filester'); ?></th>
      <td>
        <div>
          <select class="njt-fs-list-user-restrictions njt-settting-width-select" name="njt-fs-list-user-restrictions">
            <?php
              if ($listUserApproved && count($listUserApproved) != 1) {
              foreach ( $wp_roles->roles as $key=>$value ):
                if (is_multisite()) {
                  if (in_array($key,$listUserApproved) ) {?>
                    <option value="<?php echo $key; ?>"
                      <?php echo(!empty($firstKeyRestrictions) && $firstKeyRestrictions == $key ) ? 'selected="selected"' : '';?>>
                      <?php echo esc_html($value['name']); ?>
                    </option>
                    <?php 
                        }
                }
                if (!is_multisite()) {
                  if ($key !== 'administrator' && in_array($key,$listUserApproved) ) {?>
                    <option value="<?php echo $key; ?>"
                      <?php echo(!empty($firstKeyRestrictions) && $firstKeyRestrictions == $key ) ? 'selected="selected"' : '';?>>
                      <?php echo esc_html($value['name']); ?>
                    </option>
                    <?php 
                        }
                }
                
              endforeach;}
              else {
             ?>
            <option selected disabled hidden><?php _e("Nothing to choose", 'filester'); ?></option>
            <?php }?>
          </select>
          <?php 
            if(empty($listUserApproved) || $listUserApproved && count($listUserApproved) == 1 && $listUserApproved[0] == 'administrator') {
              ?>
          <p class="description njt-text-error njt-settting-width">
            <?php _e("Please select a User Role at Setings tab to use this option.", 'filester'); ?>
          </p>
          <?php
            } else {
          ?>
          <p class="description njt-text-error njt-settting-width" style="display:none">
            <?php _e("Please select a User Role at Setings tab to use this option.", 'filester'); ?>
          </p>
          <?php
            } 
          ?>
        </div>
      </td>
    </tr>
    <tr>
      <th><?php _e("Disable command", 'filester'); ?></th>
      <td>
        <div style="line-height: 2" class="njt-settting-width njt-fs-list-col4">
          <?php include_once $viewListOperations; ?>
          <!-- Value to submit data -->
          <input type="hidden" name="list_user_restrictions_alow_access" id="list_user_restrictions_alow_access">
          <!-- Data saved after submit -->
          <input type="hidden" name="list_restrictions_has_approved" id="list_restrictions_has_approved"
            value="<?php echo esc_attr(implode(",", !empty($arrRestrictions[$firstKeyRestrictions]['list_user_restrictions_alow_access']) ? $arrRestrictions[$firstKeyRestrictions]['list_user_restrictions_alow_access'] : array()));?>">
        </div>
      </td>
    </tr>
    <tr>
      <th><?php _e("Root Path for this User Role", 'filester'); ?></th>
      <td>
        <div>
          <div class="njt-settting-width">
            <button type="button"
              class="njt-fs-button js-creat-root-path"><?php _e("Insert Root Path", 'filester'); ?></button>
          </div>
          <textarea name="private_folder_access" id="private_folder_access" placeholder="ex: <?php echo (str_replace("\\", "/", ABSPATH)."wp-content");?>"
            class="njt-settting-width"><?php echo esc_textarea(!empty($arrRestrictions[$firstKeyRestrictions]['private_folder_access']) ? $arrRestrictions[$firstKeyRestrictions]['private_folder_access'] : '');?></textarea>
          <div>
            <p class="description njt-settting-width">
              <?php _e("Default path is: "."<code>". str_replace("\\", "/", ABSPATH)."</code>", 'filester'); ?>
            </p>
            <p class="description njt-settting-width">
            <?php _e("Eg: If you want to set root path access is ". "<strong>wp-content</strong>". " folder. Just enter ", 'filester'); ?>
              <?php echo (str_replace("\\", "/", ABSPATH));?>wp-content
            </p>
          </div>
        </div>
      </td>
    </tr>
    <tr>
      <th><?php _e("Files URL for this User Role", 'filester'); ?></th>
      <td>
        <div>
          <textarea name="private_url_folder_access" id="private_url_folder_access" placeholder="ex: <?php echo (site_url());?>"
            class="njt-settting-width"><?php echo esc_textarea(!empty($arrRestrictions[$firstKeyRestrictions]['private_url_folder_access']) ? $arrRestrictions[$firstKeyRestrictions]['private_url_folder_access'] : '');?></textarea>
          <div>
            <p class="description njt-settting-width">
              <?php _e("Default path is: "."<code>". str_replace("\\", "/", site_url())."</code>", 'filester'); ?>
            </p>
            <p class="description njt-settting-width">
            <?php _e("Eg: If you want to set files url path access is ". "<strong>wp-content</strong>". " folder. Just enter ", 'filester'); ?>
              <?php echo (str_replace("\\", "/", site_url()));?>/wp-content
            </p>
          </div>
        </div>
      </td>
    </tr>
    <tr>
      <th> <?php _e("Enter folder or file paths that you want to Hide", 'filester'); ?></th>
      <td>
        <div>
          <textarea name="hide_paths" id="hide_paths"
            class="njt-settting-width"><?php echo esc_textarea(implode(" | ", !empty($arrRestrictions[$firstKeyRestrictions]['hide_paths']) ? $arrRestrictions[$firstKeyRestrictions]['hide_paths'] : array()));?></textarea>
          <p class="description njt-settting-width">
            <?php _e("Multiple separated by vertical bar (|). Eg: themes/twentytwenty | themes/avada", 'filester'); ?>
          </p>
        </div>
      </td>
    </tr>
    <tr>
      <th><?php _e("Enter file extensions which you want to Lock", 'filester'); ?></th>
      <td>
        <div>
          <textarea name="lock_files" id="lock_files"
            class="njt-settting-width"><?php echo esc_textarea(implode(" | ", !empty($arrRestrictions[$firstKeyRestrictions]['lock_files']) ? $arrRestrictions[$firstKeyRestrictions]['lock_files'] : array()));?></textarea>
          <p class="description njt-settting-width">
            <?php _e("Multiple separated by vertical bar (|). Eg: .php | .png | .css", 'filester'); ?>
          </p>
        </div>
      </td>
    </tr>
    <tr>
      <th><?php _e("Enter file extensions which user can be Uploaded", 'filester'); ?></th>
      <td>
        <div>
          <textarea name="can_upload_mime" id="can_upload_mime"
            class="njt-settting-width"><?php echo esc_textarea(implode(",", !empty($arrRestrictions[$firstKeyRestrictions]['can_upload_mime']) ? $arrRestrictions[$firstKeyRestrictions]['can_upload_mime'] : array()));?></textarea>
          <p class="description njt-settting-width">
            <?php _e("Multiple separated by comma. If left empty, this means user can't upload any files. Eg: .jpg, .png, .csv", 'filester'); ?>
            <br>
            <?php _e("Note: For security reasons, non-admin users cannot upload files with the following extensions: .php, .htaccess, or mime types: text/x-php, text/php, text/plain", 'filester'); ?>
          </p>
        </div>
      </td>
    </tr>

    <!-- button submit -->
    <tr>
      <td></td>
      <td>
        <p class="submit">
          <button type="button" name="njt-form-user-role-restrictionst" id="njt-form-user-role-restrictionst"
            class="button button-primary"><?php _e("Save Changes", 'filester'); ?></button>
        </p>
      </td>
    </tr>
  </table>
</form>