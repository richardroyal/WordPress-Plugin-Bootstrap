<?php
defined('WP_PLUGIN_URL') or die('Restricted access');

$objects = new WordPress_Plugin_Model(null, null, 'dispatch');
$objects->load_action();
?>
