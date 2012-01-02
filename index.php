<?php
/*
Plugin Name: WordPress Plugin Bootstrap
Plugin URI: http://saidigital.co
Description: Copy folder into WordPress plugins folder with a unique name and then change the prefix for functions and constants.
Version: 0.0.1
Author: Richard Royal
Author URI: http://saidigital.co
*/

defined('WP_PLUGIN_URL') or die('Restricted access');


if(!class_exists("WordPress_Plugin_Model")) require_once('lib/class.wordpress-plugin-model.php');

global $wpdb;
define('WPPB_PATH', plugin_dir_path(__FILE__));
define('WPPB_URL', plugins_url('', __FILE__));

$wppb = new WordPress_Plugin_Model('widget', array('name'=>'string', 'description'=>'text', 'active'=>'boolean')); 



/*
define('WPPB_MENU_DB',$wpdb->prefix.'wppb_menus');
define('WPPB_CATEGORY_DB',$wpdb->prefix.'wppb_categories');
define('WPPB_ITEM_DB',$wpdb->prefix.'wppb_items');
define('WPPB_ADMIN_URL',"wppb-menu-setup");
define('WPPB_ADMIN_PARSE',"wppb-page");
define('WPPB_CAPABILITY','publish_posts');
define('WPPB_ROUTE',get_bloginfo('url').'/?wppb-routing=');
define('WPPB_CRUD',get_bloginfo('url').'/?wppb-routing=crud');
define('WPPB_EXTENDED_DB_VERSION','1.0');
define('WPPB_HELP','help');
require_once(ABSPATH.'wp-admin/includes/upgrade.php');
require_once("lib/db_setup.php");
require_once("lib/functions.php");
require_once("admin/functions.php");
*/


/* Classes */
/*
require_once("lib/class.menu.php");
require_once("lib/class.category.php");
require_once("lib/class.item.php");
require_once("views/view.items.php");
require_once("views/view.menu.php");
*/

#$wppb->create_model('widget', array('name'=>'string', 'description'=>'text', 'active'=>'boolean'));





/* run setup scripts on activation */
#register_activation_hook(__FILE__,'wppb_install_plugin');



/**
 *   Admin page Routes and Callbacks
 */
#function wp_restaurant_admin(){require_once("admin/manage-menus.php");}
#function wp_restaurant_admin_category(){require_once("admin/manage-categories.php");}
#function wp_restaurant_admin_item(){require_once("admin/manage-items.php");}
#function wppb_admin_menu() {
#  if (current_user_can('manage_options')) {
#    $title = "WP Restaurant Menu Manager - "; 
    // add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
#    add_menu_page($title . "Setup Menus", "Manage Menus", WPPB_CAPABILITY, WPPB_ADMIN_URL, "wp_restaurant_admin");
    // add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
//    add_submenu_page("wppb-menu-setup", $title.'Manage Categories', "Categories", WPPB_CAPABILITY, "wppb-category-setup", "wp_restaurant_admin_category" );
//    add_submenu_page("wppb-menu-setup", $title.'Manager Items', "Items", WPPB_CAPABILITY, "wppb-item-setup", "wp_restaurant_admin_item" );
#  }
#}
#add_action('admin_menu', 'wppb_admin_menu');






/**
 *  Register Frontend CSSs
 */
 /*
function wppb_stylesheets() {
  if(!is_admin()){
    wp_enqueue_style('wppb-style', WPPB_URL.'css/style.css');  
  } 
}add_action('wp_print_styles', 'wppb_stylesheets');
*/



/**
 *  Register CSS for Admin Pages
 */
 /*
function wppb_admin_register_css(){
  wp_enqueue_style('wppb-admin-style', WPPB_URL.'css/admin.css');
  wp_enqueue_style('thickbox');
}add_action('admin_init', 'wppb_admin_register_css');
*/


/*** Register admin JS */
/*
function wppb_admin_scripts() {
  if(isset($_GET['page']) && $_GET['page'] == WPPB_ADMIN_URL) {
    wp_enqueue_script('media-upload');
    wp_enqueue_script('thickbox');
    wp_register_script('wppb-admin', WPPB_URL.'/js/admin.js', array('jquery','media-upload','thickbox'));
    wp_enqueue_script('wppb-admin');
  }
}add_action('admin_print_scripts', 'wppb_admin_scripts');
*/






/*** Use Shortcode API to output menus on frontend */
/*
function wppb_shortcode_handler($atts, $content=null, $code=""){
 return wppb_get_menu($atts);
}add_shortcode('WP_Restaurant_Menu', 'wppb_shortcode_handler');
*/





/**
 *  Setup custom URL for Export Route and Create New
 */

function wppb_parse_export($wp) {
    // only process requests POST'ed to "/?wppb-routing=export"
    if (array_key_exists('wppb-routing', $wp->query_vars) && $wp->query_vars['wppb-routing'] == 'export') {
      include('export/export-menu.php');
      die();exit();
    }
}#add_action('parse_request', 'wppb_parse_export');

function wppb_parse_crud($wp) {
    // only process requests POST'ed to "/?wppb-routing=crud"
    if (array_key_exists('wppb-routing', $wp->query_vars) && $wp->query_vars['wppb-routing'] == 'crud') {
      include('admin/crud-routing.php');
      die();exit();
    }
}#add_action('parse_request', 'wppb_parse_crud');

function wppb_parse_query_vars($vars) {
    $vars[] = 'wppb-routing';
    return $vars;
}#add_filter('query_vars', 'wppb_parse_query_vars');




?>
