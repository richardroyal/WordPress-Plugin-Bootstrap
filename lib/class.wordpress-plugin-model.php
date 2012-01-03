<?php
class WordPress_Plugin_Model{

 /** 
  *  Create object, menu, and varify database structure
  *
  *  @param string $name : unique class name 
  *  @param array $attr : fields and field types for database storage
  */
  public function __construct($name, $attr) {
    global $wpdb;
    $this->name = ucfirst($name);
    $this->class_name = strtolower(str_replace(array(" ","'"),array('_',''),$name));
    $this->table_name = $wpdb->prefix.'model_'.$name;
    $this->attr = $attr;
    $this->capability = "publish_posts";
  
    if(is_admin()){
      $this->admin_url = "wppb-manage-$this->class_name";
      $this->verify_db();
      $this->set_routes();
      add_action('admin_menu', array(&$this, 'create_menu'));
    }
    

  }



 /**
  *  Create DB table for object and rebuild structure if necessary
  */
  private function verify_db(){
    global $wpdb;
    $sql =  "CREATE TABLE $this->table_name (" . "\r\n";
    $sql .=   "`id` mediumint(9) NOT NULL AUTO_INCREMENT," . "\r\n";
    foreach($this->attr as $field => $field_type){
      $sql .= "`$field` ".str_replace(array('string', 'boolean'), array('VARCHAR(255)', 'TINYINT(1)'), $field_type)." NOT NULL," . "\r\n";
    }
    $sql .=   "`updated_at` timestamp default now() on update now()," . "\r\n";
    $sql .=   "UNIQUE KEY id (id)" . "\r\n";
    $sql .= ");";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }


 /**
  *  Create class attributes related to routes
  */
  private function set_routes(){
    // Index route: path and url
    $override = WPPB_PATH."/admin/$this->class_name/wppb-index.php";
    $this->index_path = file_exists($override) ? $override : WPPB_PATH.'/admin/wppb-index.php';
    $this->index_url = $this->admin_url.'&action=index';
    
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
    require_once($this->index_path);
  }




}
?>
