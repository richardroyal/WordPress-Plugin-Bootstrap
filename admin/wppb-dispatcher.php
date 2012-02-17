<?php
defined('WP_PLUGIN_URL') or die('Restricted access');

print_r($_POST); 


if( isset($this) ){
  
  /* New Object POST'd, Save record and redirect to edit */
  if( $_POST[$this->class_name]['action'] == "new" && !empty($_POST[$this->class_name]['submit'])){
    die('Saving Object');  
  }
  
}




$objects = new WordPress_Plugin_Model(null, null, 'dispatch');
#var_dump($objects);
$objects->load_action();



?>
