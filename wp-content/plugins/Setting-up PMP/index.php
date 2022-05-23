<?php 
/** 
 * @package SettingPmp
 */
/*
Plugin Name: Setting-up PMP
Plugin URI: http://setting-pmp.com
Description: This is the custom plugin for setting up PMP
Version: 1.0.0
Author: Kode Tiger
Author URI: http://www.kodetiger.io
License: GPLv2 or later
Text Domain: settingup-pmp
*/ 
// Exit if accessed directly
include 'functions.php';


if(!defined('ABSPATH')){
    exit;
  }
  
  define("PLUGIN_DIR_PATH", plugin_dir_path(__FILE__));
  define("PLUGIN_URL", plugins_url());
  
  function my_handler() {
   
    $uriFound = strpos($_SERVER["REQUEST_URI"], "user_creation_webhook.php");
   
     if($uriFound !== false) {
  
        include strtok(strstr($_SERVER["REQUEST_URI"], "user_creation_webhook.php"), "?");
        exit();  
     }
     
  }
    function my_handler_transfer_products() {
   
    $uriFound = strpos($_SERVER["REQUEST_URI"], "transfer_products.php");
   
     if($uriFound !== false) {
  
        include strtok(strstr($_SERVER["REQUEST_URI"], "transfer_products.php"), "?");
        exit();  
     }
     
  }
      function my_handler_transfer_user_details() {
   
    $uriFound = strpos($_SERVER["REQUEST_URI"], "transfer_users.php");
   
     if($uriFound !== false) {
  
        include strtok(strstr($_SERVER["REQUEST_URI"], "transfer_users.php"), "?");
        exit();  
     }
     
  }
  $current_store_URL = getting_store_Url();
  function migrating_users_products(){
      global $current_store_URL;
         
         $subscription_URL = getting_subscription_Url($current_store_URL); 
         $transaction_URL = getting_transaction_Url($subscription_URL);
         $item_URL = getting_items($transaction_URL);
         $products=getting_products();
         $store_products = storing_products($products);
         $store_ID = getting_storeId();
         
  }
  function migrating_all_details(){
      global $current_store_URL;
  
        $fetching_Subscription = fetching_subscription_details($current_store_URL); //fetching all subscriptions details
        $fetching_transaction = fetching_transaction_customer_details($fetching_Subscription); //fetching all transactions details
        $fetching_items = getting_items_details($fetching_transaction); //fetching all items details
        $fetching_payment = getting_payments_details($fetching_items); //fetching all paymentzs details
        
        $Subscriber_details= subscriber_user($fetching_payment); //adding subscriber details to user table.
        $storing_SubscriptionPayment = storing_SubscriptionPayment_details($fetching_payment); // storing all subscription and payment details
        
if($storing_SubscriptionPayment){
           echo "Payment Status Stored";
        }
        if($Subscriber_details){
         echo "Subscription Details Stored";
      }

  }
  
  add_action('parse_request', 'my_handler');
   add_action('parse_request', 'my_handler_transfer_products');
   add_action('parse_request', 'my_handler_transfer_user_details');
  add_shortcode("get_detail","migrating_users_products");
  add_shortcode("get_details","migrating_all_details");