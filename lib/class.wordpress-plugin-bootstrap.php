<?php
class WordPress_Plugin_Bootstrap{

  // Create object, menu, and varify database structure
  public function __construct($name, $attr) {
    self.class_name = $name;
    self.attr = $attr;

    self.verify_db();

  }



  // create database for object and rebuild structure if necessary
  private function verify_db(){
    global $wpdb;


    // setup database structure for Answers
    if($wpdb->get_var("show tables like '".WPSS_ANSWERS_DB."'") != WPSS_ANSWERS_DB){
        $sql =  "CREATE TABLE ".WPSS_ANSWERS_DB." (".
                   "id int NOT NULL AUTO_INCREMENT, ".
                   foreach(self.attr as $name => $type){
                     "$name $type, ".
                   }    
                   "UNIQUE KEY id (id) ".
               ")";
        dbDelta($sql);
    }











    $sql = "CREATE TABLE " . $table_name . " (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                name tinytext NOT NULL,
                text text NOT NULL,
                url VARCHAR(55) DEFAULT '' NOT NULL,

                UNIQUE KEY id (id)
            );";



  }
  
  
}
?>
