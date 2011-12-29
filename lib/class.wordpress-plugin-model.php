<?php
#class WordPress_Plugin_Bootstrap{
class WordPress_Plugin_Model{

  // Create object, menu, and varify database structure
  public function __construct($name, $attr) {
    $this->class_name = $name;
    $this->attr = $attr;

    $this->verify_db();
    $this->create_menu();
    

  }



  // Create db table for object and rebuild structure if necessary
  private function verify_db(){
    global $wpdb;


    // setup database structure for Answers
    if($wpdb->get_var("show tables like '".WPSS_ANSWERS_DB."'") != WPSS_ANSWERS_DB){
        $sql =  "CREATE TABLE ".WPSS_ANSWERS_DB." (".
                   "id int NOT NULL AUTO_INCREMENT, ";
                   foreach($this->attr as $name => $type){
                     $sql .= "$name $type, ";
                   }    
                   $sql .= "UNIQUE KEY id (id) ";
               ")";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
#        dbDelta($sql);
    }
  }

  
  // Create Admin menus for model
  private function create_menu(){
    
    
  }
  
}
?>
