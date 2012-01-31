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
    
    # Set class attributes and determine action and show template
    $this->set_name($name,$action);
    $this->set_action($action);
    $this->class_name = strtolower(str_replace(array(" ","'"),array('_',''),$this->name));
    $this->table_name = $wpdb->prefix.'model_'.$this->class_name;
    $this->capability = "publish_posts";
    $this->attr = $attr; 
    $this->structure = $wpdb->get_results("SHOW COLUMNS FROM $this->table_name");
    $this->id = $id;


    if(is_admin()){
      $this->admin_slug = "wppb-manage-$this->class_name";
      $this->set_routes();
    }



    /* Call controller actions based on $this->action  */
    if( method_exists($this, $this->action) ){
      call_user_func(array($this, $this->action));
    }

    # Set action attributes based on action
    elseif($this->action == "show"){
      $obj = $wpdb->get_results("SELECT * FROM `$this->table_name` WHERE id=$id");
      $this->data = $obj[0];
      $this->edit_url .= $id;
        
    }
    elseif($this->action == "new"){
      $obj = $wpdb->get_results("SELECT * FROM `$this->table_name` WHERE id=$id");
      $this->data = $obj[0];
      $this->edit_url .= $id;
        
    }

    # Register JavaScripts and CSS
    add_action('init',$this->load_assets());

  }


  /* ---------- Controller Actions ------------- */


 /**
  *  Setup Admin menu and DB - Run only once
  */
  private function setup(){
    if(function_exists('is_admin') && is_admin()){
      add_action('admin_menu', array(&$this, 'create_menu'));
      $this->verify_db();
    }
  }


 /**
  *  Create object with array of model objects
  */
  public function index(){
    global $wpdb;
    $ids = $wpdb->get_results("SELECT id FROM $this->table_name");
    $all_objects = array();
    foreach($ids as $id){
      $obj = new WordPress_Plugin_Model($this->name, $this->attr, 'show', $id->id);
      $all_objects[] = $obj;
    }
    $this->saved_objects = $all_objects;
    $this->set_index_headers();
    
  }

 /**
  *  Call save and then load object along with full DB structure
  */
  public function edit(){
    global $wpdb;
    if($this->action == "edit"){
      $this->update();
      $obj = $wpdb->get_results("SELECT * FROM `$this->table_name` WHERE id=$this->id");
      $this->data = $obj[0];
      $this->edit_url .= $this->id;
    }
  }



 /**
  *  Determine name from action or constructor
  */
  private function set_name($name, $action){
    if(!empty($name)) $this->name = ucfirst($name);
    else{
      if(is_admin()){
        $this->name = ucfirst(str_replace('wppb-manage-', '', $_GET['page']));
      }
    } 
  }



 /**
  *  Override $action from constructor params with GET array.
  *  Use $control to limit actions to prevent URL hacking.
  */
  private function set_action($action){
    $this->called_action = $action;
    $control = array('dispatch', 'edit', 'show', 'index');
    if($action != "setup" && !empty($_GET['action'])){
      if(in_array($_GET['action'],$control)) $this->action = $_GET['action'];
    }
    else $this->action = $action;
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
    $this->admin_url = admin_url()."admin.php?page=$this->admin_slug";

    // Dispatcher route: path and url
    $override = WPPB_PATH."/admin/$this->class_name/wppb-dispatcher.php";
    $this->dispatcher_path = file_exists($override) ? $override : WPPB_PATH.'admin/wppb-dispatcher.php';
    $this->dispatcher_url = $this->admin_slug;
    
    // Index route: path and url
    $override = WPPB_PATH."/admin/$this->class_name/wppb-index.php";
    $this->index_path = file_exists($override) ? $override : WPPB_PATH.'admin/wppb-index.php';
    $this->index_url = $this->admin_slug;
    
    // Edit routes
    $override = WPPB_PATH."/admin/$this->class_name/wppb-edit.php";
    $this->edit_path = file_exists($override) ? $override : WPPB_PATH.'admin/wppb-edit.php';
    $this->edit_url = $this->admin_url.'&action=edit&id=';
    
    $override = WPPB_PATH."/admin/$this->class_name/wppb-edit.php";
    $this->new_path = file_exists($override) ? $override : WPPB_PATH.'admin/wppb-edit.php';
    $this->new_url = $this->admin_url.'&action=new';
    
    # Aux routes and URLs
    $this->path = plugin_dir_path(__FILE__);
    $this->url = plugins_url('', __FILE__);
    $this->assets_url = $this->url.'/../assets/';

  }




 /**
  *  Create Admin menu for model on WP::admin_init
  */
  public function create_menu(){
    // add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
    add_menu_page("WP Model ".$this->name, "Manage ".$this->name, $this->capability, $this->admin_slug, array(&$this, 'load_dispatcher'));
  }




 /**
  *  Loads index, edit, or show file for object based on $this->action
  *  Override by adding a file {sanitized_class_name}/wppb_{action}.php
  *  in admin folder.
  */
  public function load_action(){
    switch($this->action){
      case "edit":
      case "new":
        require_once($this->edit_path);
        break;
      case "show":
        require_once($this->show_path);
        break;
      case "index":
      case "dispatch":
        require_once($this->index_path);
        break;
    }
  }
  public function load_dispatcher(){
    require_once($this->dispatcher_path);
  }


 /**
  *  From table structure create array of headers
  *  to display on admin index table. Columns name
  *  'name' and 'title' and 'updated_at' are primary.
  */
  private function set_index_headers(){
    $headers = array();
    $primary = array('id', 'name', 'title', 'updated_at');
    foreach($this->structure as $row){
      if( in_array($row->Field, $primary) ){
        $headers[] = ucwords($row->Field);
      }
    }
    foreach($this->structure as $row){
      if( !in_array($row->Field, $primary) ){
        if( count( $headers ) < 3 ) {
          $headers[] = ucwords($row->Field);
        }
      }
    }
    $this->headers = $headers; 
  }



 /**
  *  Returns value for header in show
  */
  public function get_val($col){
    if(empty($col) || empty($this->data)) return '';
    $c = strtolower($col);
    return $this->data->$c;
  }


 /**
  *  Registers and loads assets CSS and JavaScript
  */
  private function load_assets(){
    if(is_admin()){
      wp_enqueue_style("wppb-admin-css", $this->assets_url.'/css/admin/bootstrap.css');
    }
      
  }



 /**
  *  Call Validations and then update DB
  */
  private function update(){
    if(is_admin() && !empty($_POST[$this->class_name]) ){
      global $wpdb;

      $object = $_POST[$this->class_name];
      if(empty($object) || empty($object['id']) || empty($this->id) || $this->called_action == "dispatch") return "";
      if($object['submit'] == "Save" && $object[id] == $this->id){
        $data = array();
        foreach($this->structure as $field){
          $data[$field->Field] = $object[$field->Field];  
        }
        unset($data['submit']);
        unset($data['updated_at']);

        $wpdb->update($this->table_name, $data, array('id' => "$this->id"));
        
      }
    }
  }


}
?>
