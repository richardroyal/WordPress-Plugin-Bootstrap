<?php
defined('WP_PLUGIN_URL') or die('Restricted access');
if (!current_user_can('publish_posts')) wp_die( __('You do not have sufficient permissions to access this page.') );

$objects = new WordPress_Plugin_Model(null, null, 'index');
var_dump($objects->headers);

?>
<div class="wrap wprmm">
  <div id="icon-tools" class="icon32"></div><h2 class="left">Manage <?php echo $objects->name;?></h2>
  <div class="clear"></div>
  <hr />

  <?php #wprmm_get_help(array('main'=>true));?>

  <table class="widefat">
  <thead>
    <tr>
      <th></th>
      <th><?php echo $objects->name;?></th>
      <th>Show</th>
      <th>Edit</th>
      <th>Delete</th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
    </tr>
  </tfoot>
  <tbody>
    <?php foreach($objects->saved_objects as $obj): ?>
      <th><?php $obj->id?></th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
    <?php endforeach; ?>
  </tbody>
  </table>

  <div class="wprmm-admin-nav">
    <p>
      <a class="button-primary" href="<?php # echo wprmm_admin_url('menu','new-menu','new');?>">+ Create New Menu</a>&nbsp;
      <span><a class="button" href="<?php # echo wprmm_help_link(); ?>">help?</a></span>&nbsp;
    </p>
  </div>

</div>      
