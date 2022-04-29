<?php 
/** 
 * @package CustomPlugin
 */
/*
Plugin Name: Foxy webhook listener solari
Plugin URI: http://webhook-plugin.com
Description: This is the custom plugin
Version: 1.0.0
Author: Kode Tiger
Author URI: http://webhook-plugin.com
License: GPLv2 or later
Text Domain: webhook-plugin
*/ 
include 'functions.php';
//include 'webhook_listener.php';
include dirname(__FILE__).'../../../wp-load.php';
//include dirname(__FILE__).'webhook_listener.php';
// Exit if accessed directly
if(!defined('ABSPATH')){
    exit;
  }
  
  define("PLUGIN_DIR_PATH", plugin_dir_path(__FILE__));
  define("PLUGIN_URL", plugins_url());
  
  function my_custom_url_handler() {
   
    $uriFound = strpos($_SERVER["REQUEST_URI"], "webhook_listener.php");
   
     if($uriFound !== false) {
  
        include strtok(strstr($_SERVER["REQUEST_URI"], "webhook_listener.php"), "?");
        exit();  
     }
     
  }
  
    function custom_route() {
   
    $uriFounds = strpos($_SERVER["REQUEST_URI"], "auto_reconciliation.php");
   
     if($uriFounds !== false) {
  
        include strtok(strstr($_SERVER["REQUEST_URI"], "auto_reconciliation.php"), "?");
        exit();  
     }
     
  }
  
  
function api_calling(){

   $current_access_token = access_token();
   $current_store_URL = getting_storeUrl($current_access_token);
   $current_transactionUrl = getting_transactionUrl($current_store_URL,$current_access_token);
   $current_transaction_email = getting_transaction_details($current_transactionUrl,$current_access_token);
  
   $parse_json = json_decode($current_transaction_email);
   foreach($parse_json as $key){
    $transaction_email [] = $key;
   }
   $transactionEmail = $transaction_email;
   $current_wordpress_user = getting_user();
   $parseJson = json_decode($current_wordpress_user);
   foreach($parseJson as $key){
    $email [] = $key;
   }
   $wordpress_email = $email;
   //echo insert_data();
   
   //echo match_email($transactionEmail,$wordpress_email,$current_store_URL,$current_access_token); 
  $matchEmail = match_email($transactionEmail,$wordpress_email,$current_store_URL,$current_access_token);
  if ($transactionEmail ==null){
       echo "<h2 style ='font-family:sans-serif'> There are no missing users. </h2>";
    }
    else 
    {
  foreach($matchEmail as $key) {
      $id = $key[0];
      $fname = $key[1];
      $lname = $key[2];
      $emails = $key[3];
      $user_missingEmail[] = $key[3];
      $date_create = $key[4];
      $date_modified = $key[5];
      echo foxy_missing_users($id,$fname,$lname,$emails,$date_create,$date_modified);
    } 
    $missing_Email = user_creation($user_missingEmail);
  }
   
}


//   wp_schedule_single_event( time() + 86400, 'api_calling' );
   add_action('parse_request', 'my_custom_url_handler');
   add_action('parse_request', 'custom_route');
   add_shortcode("testing","api_calling");
  
  
