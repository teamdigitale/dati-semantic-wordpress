<?php
 $locales = njt_fs_locales();
?>

<select name="fm_locale" id="fm_locale" class="njt-fs-settting-width-half">
  <?php foreach($locales as $key => $locale) { ?>
  <option value="<?php echo $locale;?>"
    <?php echo (isset($this->options['njt_fs_file_manager_settings']['fm_locale']) && $this->options['njt_fs_file_manager_settings']['fm_locale'] == $locale) ? 'selected="selected"' : '';?>>
    <?php echo esc_html($key);?></option>
  <?php } ?>
</select>