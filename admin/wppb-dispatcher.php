<?php
defined('WP_PLUGIN_URL') or die('Restricted access');

#print_r($_POST); 


if( isset($this) ){
  
  /* New Object POST'd, Save record and redirect to edit */
  if( $_POST[$this->class_name]['action'] == "new" && !empty($_POST[$this->class_name]['submit'])){
    $object = new WordPress_Plugin_Model(null, null, 'create',null,$_POST[$this->class_name]);
  }
  
  
  /* Delete Record from GET id and redirect to index */
  if( $_GET['action'] == "delete"){
    $object = new WordPress_Plugin_Model(null, null, 'delete',$_GET['id']);
  }
  
}




$objects = new WordPress_Plugin_Model(null, null, 'dispatch');
#var_dump($objects);
$objects->load_action();



?>
