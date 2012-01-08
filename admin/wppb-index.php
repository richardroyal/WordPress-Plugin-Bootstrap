<?php
defined('WP_PLUGIN_URL') or die('Restricted access');
if (!current_user_can('publish_posts')) wp_die( __('You do not have sufficient permissions to access this page.') );

$objects = new WordPress_Plugin_Model(null, null, 'index');
var_dump($objects->saved_objects);

?>
<div class="wrap wprmm">
  <div id="icon-tools" class="icon32"></div><h2 class="left">Manage <?php echo $objects->name;?></h2>
  <div class="clear"></div>
  <hr />

  <?php #wprmm_get_help(array('main'=>true));?>

  <table class="widefat">
  <thead>
    <tr>
      <th><?php echo $objects->name;?></th>

      <?php foreach( $objects->headers as $h ): ?>
        <?php if($h != "Id"): ?>
          <th><?php echo $h;?></th>
        <?php endif;?>
      <?php endforeach;?>

      <th>Actions</th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <?php for($i=1; $i<( count($objects->headers)+2 ); $i++): ?>
        <th></th>
      <?php endfor;?>
    </tr>
  </tfoot>
  <tbody>
    <?php foreach($objects->saved_objects as $obj): ?>
      <th><?php $obj->id?></th>
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
