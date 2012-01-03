<?php
class WordPress_Plugin_Model{

 /** 
  *  Admin Constructor for creating object
  *  Sets menu, and varifies database structure
  *
  *  @param string $name : unique class name 
  *  @param mixed $attr
  *          array  : fields and field types for database storage 
  *          string : "index"
  *          int    : database id
  *                      
  */
  public function __construct($name, $attr, $action = "setup", $id = NULL) {
    global $wpdb;

    $this->set_name($name,$action);

    $this->class_name = strtolower(str_replace(array(" ","'"),array('_',''),$this->name));
    $this->table_name = $wpdb->prefix.'model_'.$this->name;
    $this->capability = "publish_posts";
    $this->attr = $attr;
    if(is_admin()){
      $this->admin_url = "wppb-manage-$this->class_name";
      $this->set_routes();
    }

    # Set attributes based on action
    if($action == "setup"){
      if(function_exists('is_admin') && is_admin()){
        $this->verify_db();
        add_action('admin_menu', array(&$this, 'create_menu'));
      }
    }
    elseif($action == "index"){
      $ids = $wpdb->get_results("SELECT id FROM $this->table_name");
      $all_objects = array();
      foreach($ids as $id){
        #$obj = $wpdb->get_results("SELECT * FROM $this->table_name WHERE id=`$id`");
        $obj = new WordPress_Plugin_Model($this->name, $this->attr, 'show', $id);
        $all_objects[] = $obj;
      }
      $this->saved_objects = $all_objects;
    }
    elseif($action == "show" || $action == "edit"){
      $obj = $wpdb->get_results("SELECT * FROM $this->table_name WHERE id=`$id`", A_ARRAY);
      $attr = array();
      foreach($obj as $field => $value){
        $attr[$field] = $value;
      }
      $this->values = $attr;


    }

  }



 /**
  *  Determine name from action or constructor
  */
  private function set_name($name, $action){
    if(!empty($name)) $this->name = ucfirst($name);
    elseif($action="index"){
      if($attr=="index"){
        if(is_admin()){
          $this->name = str_replace('wppb-manage-', '', $_POST['page']);
        }
      }
    }
    
  }



 /**
  *  Create DB table for object and rebuild structure if necessary
  *  Relies on dbDelta
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
