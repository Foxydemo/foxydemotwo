<?php 
/** 
 * @package FaraazPlugin
 */
/*
Plugin Name: Faraaz Plugin
Plugin URI: http://www.faraaz-plugin.com
Description: This is the custom plugin created by faraaz
Version: 1.0.0
Author: Kode Tiger
Author URI: http://www.kodetiger.io
License: GPLv2 or later
Text Domain: faraaz-plugin
*/ 
// Exit if accessed directly
include 'function.php';


if(!defined('ABSPATH')){
    exit;
  }
  
  define("PLUGIN_DIR_PATH", plugin_dir_path(__FILE__));
  define("PLUGIN_URL", plugins_url());


function check_date(){
    echo check_subscriptionDate();
}
add_shortcode("details","check_date");