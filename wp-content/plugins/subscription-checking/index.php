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
include 'function.php';


function subscription_check_cron() {
   
  $uriFound = strpos($_SERVER["REQUEST_URI"], "subscription_check.php");
 
   if($uriFound !== false) {

      include strtok(strstr($_SERVER["REQUEST_URI"], "subscription_check.php"), "?");
      exit();  
   }
   
}


if(!defined('ABSPATH')){
    exit;
  }
  
  define("PLUGIN_DIR_PATH", plugin_dir_path(__FILE__));
  define("PLUGIN_URL", plugins_url());

function Subscription_Details(){
  $dates_time = Subscription_checking();
  $check = check_subscriptionDate($dates_time);
  
  $role =  user_role_change($check);
  $check_expiry=send_subscription_mail($check);
  
}

add_action('parse_request', 'subscription_check_cron');
add_shortcode('subscription_check','Subscription_Details');

  