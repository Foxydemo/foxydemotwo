<?php 
/** 
 * @package Subscriptionchecking
 */
/*
Plugin Name: Subscription-checking
Plugin URI: http://Subscription-checking.com
Description: This is the custom plugin for subscription checking user in  PMP
Version: 1.0.0
Author: Kode Tiger
Author URI: http://www.kodetiger.io
License: GPLv2 or later
Text Domain: Subscription-checking
*/ 
// Exit if accessed directly
include 'functions.php';


if(!defined('ABSPATH')){
    exit;
  }
  
  define("PLUGIN_DIR_PATH", plugin_dir_path(__FILE__));
  define("PLUGIN_URL", plugins_url());

add_shortcode('Subscription_check','Subscription_checking');

  