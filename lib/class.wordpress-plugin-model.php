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
    $this->admin_url = "wp-manage-".$this->class_name;

    $this->verify_db();
    $this->set_routes();
    add_action('admin_menu', array(&$this, 'create_menu'));
    

  }



 /**
  *  Create DB table for object and rebuild structure if necessary
  */
  private function verify_db(){
    global $wpdb;

    if($wpdb->get_var("show tables like '$this->table_name'") != $this->table_name){
        $sql =  "CREATE TABLE '$this->table_name' (".
                   "id int NOT NULL AUTO_INCREMENT, ";
                   foreach($this->attr as $name => $type){
                     $sql .= "$name $type, ";
                   }    
                   $sql .= "UNIQUE KEY id (id) )";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        # echo $sql;
        # dbDelta($sql);
    }
  }


 /**
  *  Create class attributes related to routes
  */
  private function set_routes(){
    $override = WPPB_PATH."/admin/$this->class_name/wppb-index.php";
    $this->index_route = file_exists($override) ? $override : WPPB_PATH.'/admin/wppb-index.php';
    
  }

  
 /**
  *  Create Admin menu for model on WP::admin_init
  */
  public function create_menu(){
    // add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
    add_menu_page("WP Model ".$this->name, "Manage ".$this->name, $this->capability, $this->admin_url, array(&$this, 'model_index'));
  }


 /**
  *  Loads index file for object wppb_index.php
  *  Override by adding a file {sanitized_class_name}/wppb_index.php
  *  in admin folder.
  */
  public function model_index(){
    #echo $this->index_route;
    require_once($this->index_route);
  }




}
?>
