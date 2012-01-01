<?php
class WordPress_Plugin_Model{

  // Create object, menu, and varify database structure
  public function __construct($name, $attr) {
    global $wpdb;
    $this->name = ucfirst($name);
    $this->class_name = strtolower(str_replace(array(" ","'"),array('_',''),$name));
    $this->table_name = $wpdb->prefix.'model_'.$name;
    $this->attr = $attr;
    $this->capability = "publish_posts";
    $this->admin_url = "wp-manage-model";

    $this->verify_db();
    add_action('admin_menu', array(&$this, 'create_menu'));
    

  }



  // Create db table for object and rebuild structure if necessary
  private function verify_db(){
    global $wpdb;


    // setup database structure for Answers
    if($wpdb->get_var("show tables like '$this->table_name'") != $this->table_name){
        $sql =  "CREATE TABLE '$this->table_name' (".
                   "id int NOT NULL AUTO_INCREMENT, ";
                   foreach($this->attr as $name => $type){
                     $sql .= "$name $type, ";
                   }    
                   $sql .= "UNIQUE KEY id (id) )";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        echo $sql;
#        dbDelta($sql);
    }
  }

  
  // Create Admin menu for model
  public function create_menu(){

    // add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
    add_menu_page("WP Model ".$this->name, "Manage ".$this->name, $this->capability, $this->admin_url, "wppb_model_index");   
  }
  
}
?>
