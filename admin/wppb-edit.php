<?php
defined('WP_PLUGIN_URL') or die('Restricted access');
if (!current_user_can('publish_posts')) wp_die( __('You do not have sufficient permissions to access this page.') );

$object = new WordPress_Plugin_Model(null, null, 'edit', $_GET['id']);

var_dump($object);

?>
<div class="wrap wprmm">
  <div id="icon-tools" class="icon32"></div><h2 class="left">Edit <?php echo $object->name;?></h2>
  <div class="clear"></div>
  <hr />

  <?php #wprmm_get_help(array('main'=>true));?>
  <?php
    global $menu, $admin_page_hooks, $_registered_pages, $_parent_pages;
    var_dump($_registered_pages);
  
  ?>


</div>      
