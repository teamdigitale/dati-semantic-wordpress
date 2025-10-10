<?php
$listOperations = array(
  'mkdir'=>'mkdir',
  'mkfile'=>'mkfile',
  'rename'=>'rename',
  'duplicate'=>'duplicate',
  'paste'=>'paste',
  'archive'=>'archive',
  'extract'=>'extract',
  'copy'=>'copy',
  'cut'=>'cut',
  'edit'=>'edit',
  'rm'=>'rm',
  'download'=>'download',
  'upload'=>'upload',
  'search'=>'search'
)
?>

<?php foreach($listOperations as $key => $listOperation) { ?>
<span class="list-col4-item">
  <input type="checkbox" class="fm-list-user-restrictions-item" id="<?php echo esc_attr($key); ?>" name="<?php echo esc_attr($key); ?>"
    value="<?php echo esc_attr($key); ?>">
  <label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($listOperation); ?></label>
</span>
<?php } ?>