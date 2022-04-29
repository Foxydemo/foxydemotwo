<?php
//require_once( ABSPATH . "wp-includes/pluggable.php" );
//require_once( ABSPATH . "wp-includes/wp-load.php" );
include dirname(__FILE__).'../../../wp-load.php';
include dirname(__FILE__).'webhook_listener.php';
//include 'webhook_listener.php';
// $pagePath = explode('/wp-content/', dirname(__FILE__));
//     include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));
//     echo $pagePath;
//C:\xampp\htdocs\foxydemo\wp-content\plugins\FoxyWebhookListenerSolari\functions.php

$grant_type = 'refresh_token';
$refresh_token = 'vvKk3uc3lhjkyX6U1vWLqK4BJECnhwJIbUavpnKS';
$client_id = 'client_luHCLP8quvd2byd1HstU';
$client_secret = 'svw6fAbP7W765whGqiEui1BZmdpWDY7Uvz7mqMNQ';

// For Getting Access Token from FoxyAPI
function access_token(){
        global $grant_type,$refresh_token,$client_id,$client_secret;
        $ch = curl_init();
        $curlConfig = array(
            CURLOPT_URL            => "https://api.foxycart.com/token",
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS     => array(
                'grant_type' => $grant_type,
                'refresh_token' => $refresh_token,
                'client_id' => $client_id,
                'client_secret' => $client_secret,
            )
        );
        curl_setopt_array($ch, $curlConfig);
        $result = curl_exec($ch);
        curl_close($ch);
        $parseResult = json_decode($result);
        $access_Token = $parseResult->access_token;
        return $access_Token;
         
}

// For getting store url from mother FoxyAPI
function getting_storeUrl($accessToken){
        
        $url = "https://api.foxycart.com";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $headers = array(
        "FOXY-API-VERSION: 1",
        "Authorization: Bearer ".$accessToken,
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
        $resp = curl_exec($curl);
        curl_close($curl);
        $parseResp = json_decode($resp);
        $storeDetails = $parseResp->_links;
        $store = "fx:store";
        $store_fx = $storeDetails->$store;
        $store_link = $store_fx->href;
        return $store_link;
      
}
// for getting transactions url from store FoxyApi
function getting_transactionUrl($comming_storeUrl,$comming_accessToken){
        $url_store = $comming_storeUrl;
        $curl_store = curl_init($url_store);
        curl_setopt($curl_store, CURLOPT_URL, $url_store);
        curl_setopt($curl_store, CURLOPT_RETURNTRANSFER, true);
        $headers = array(
            "FOXY-API-VERSION: 1",
            "Authorization: Bearer ".$comming_accessToken,
            );
            
        curl_setopt($curl_store, CURLOPT_HTTPHEADER, $headers);
        //for debug only!
        curl_setopt($curl_store, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl_store, CURLOPT_SSL_VERIFYPEER, false);
        
        $resp_store = curl_exec($curl_store);
        curl_close($curl_store);

        $result_store = json_decode($resp_store);
        $transcation_name = "fx:transactions";
        $transcationUrlList = $result_store->_links;
        $transcation_link = $transcationUrlList->$transcation_name;
        $transcation_url = $transcation_link->href;
        return $transcation_url;
         
}

// for getting transaction details of particular email from transaction foxyAPI
function getting_transaction_details($comming_transactionUrl,$comming_accessToken){
        $trans_url = $comming_transactionUrl;
        $NewDate=Date('Y-m-d', strtotime('-2 days'));
        $currentDate = date('Y-m-d',strtotime('+1 days'));
        $transcationDate = $NewDate . '..' . $currentDate;
        $urlA = $trans_url;
        $dataArray = ['transaction_date' => $transcationDate];
        
        $data = http_build_query($dataArray);
        $limit = "limit=300";
  
        $getUrl = $urlA."?".$data."&".$limit;


        $curl_trans = curl_init($getUrl);
  
        curl_setopt($curl_trans, CURLOPT_URL, $getUrl);
        curl_setopt($curl_trans, CURLOPT_RETURNTRANSFER, true);
        $headers = array(
            "FOXY-API-VERSION: 1",
            "Authorization: Bearer ".$comming_accessToken,
            );
        curl_setopt($curl_trans, CURLOPT_HTTPHEADER, $headers);
        //for debug only!
        curl_setopt($curl_trans, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl_trans, CURLOPT_SSL_VERIFYPEER, false);
        
        
        $resp_cust = curl_exec($curl_trans);
        curl_close($curl_trans);

        $parseCust = json_decode($resp_cust);
        
        $cust = "fx:transactions";
        $allCustDetails = $parseCust->_embedded->$cust;
        foreach($allCustDetails as $key){
         $customerEmail [] = $key->customer_email;
        }  
        return json_encode($customerEmail);


       
}


// for getting wordpress user
function getting_user(){
        $all_users = get_users();
      
        foreach ($all_users as $user) {
    
            $email [] = $user -> user_email ;
    
        }
        return json_encode($email);
       
}

// for matching the wordpress user email or transactions email
function match_email($customerEmail,$email,$store_link,$comming_accessToken){
    global $wpdb;
    $missing_mail=array_diff($customerEmail, $email);

            //Report of Mismatch Users
            $result=array_values($missing_mail);
            echo "<h1 style = 'text-align:center'>No. of Missing Users in the last 24 hours transaction: ".count($result)."</h1>";
            for($i=0;$i<=(count($result)-1);$i++) {

                //for getting customer details from FoxyAPI
                $url_customer = $store_link;
                $curl_customer = curl_init($url_customer);
                curl_setopt($curl_customer, CURLOPT_URL, $url_customer);
                curl_setopt($curl_customer, CURLOPT_RETURNTRANSFER, true);
                $headers = array(
                  "FOXY-API-VERSION: 1",
                  "Authorization: Bearer ".$comming_accessToken,
                  );
                curl_setopt($curl_customer, CURLOPT_HTTPHEADER, $headers);
                //for debug only!
                curl_setopt($curl_customer, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl_customer, CURLOPT_SSL_VERIFYPEER, false);
                
                $resp_customer = curl_exec($curl_customer);
                curl_close($curl_customer);
                $result_customer = json_decode($resp_customer);
                $customer_name = "fx:customers";
                $customerUrl = $result_customer->_links->$customer_name->href;
                $paramurl = $customerUrl;
                //for does't match email
                $paramEmail = ['email' => $result[$i]];
      
                $dataEmail = http_build_query($paramEmail);
          
                $getParamUrl = $paramurl."?".$dataEmail;
                
                $curl_customerDetails = curl_init($getParamUrl);
                curl_setopt($curl_customerDetails, CURLOPT_URL, $getParamUrl);
                curl_setopt($curl_customerDetails, CURLOPT_RETURNTRANSFER, true);
              
                curl_setopt($curl_customerDetails, CURLOPT_HTTPHEADER, $headers);
                //for debug only!
                curl_setopt($curl_customerDetails, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl_customerDetails, CURLOPT_SSL_VERIFYPEER, false);
                
                $response_customer = curl_exec($curl_customerDetails);
                curl_close($curl_customerDetails);
                $result_customerDetail = json_decode($response_customer);
                $custName = "fx:customers";
                $allCustomerDetails = $result_customerDetail->_embedded->$custName;
            
                foreach($allCustomerDetails as $key){
                    $user_id = $key->id;
                    $user_firstName  = $key->first_name;
                    $user_lastName  = $key->last_name;
                    $user_email  = $key->email;
                    $user_password  =(string)$key->password_hash;
                    $date_create = $key->date_created;
                    $date_modify = $key->date_modified;
                    //$chang_pass = md5($user_password);
                }
                $results_data[$i] = array($user_id,$user_firstName,$user_lastName,$user_email,$date_create,$date_modify);
        }   
      return $results_data;  
}


function create_posttype() {
register_post_type( 'Foxy_data',
// CPT Options

array(
  'labels' => array(
   'name' => __( 'Foxy_data' ),
   'singular_name' => __( 'Foxy_data' )
  ),
  'public' => true,
  'has_archive' => false,
  'rewrite' => array('slug' => 'Foxy_data'),
 )
);
}
add_action( 'init', 'create_posttype' );
 
function insert_foxy_data($getting_transaction_id,$getting_transaction_firstName,$getting_transaction_lastName,$getting_transaction_email,$getting_transaction_password,$getting_transaction_date_modified){

    $my_post = array(
        'post_title'    => ($getting_transaction_firstName.'&nbsp;&nbsp;'.$getting_transaction_lastName),
        'post_type'     => 'foxy_data',
        'post_content'  => 'This is my post.',
        'post_status'   => 'publish',
                    
                    );
        //'post_author'   => 1
  $post_id = wp_insert_post( $my_post); 
update_post_meta($post_id,'customer_id',$getting_transaction_id,true);
update_post_meta($post_id,'first_name',$getting_transaction_firstName,true);
update_post_meta($post_id,'last_name',$getting_transaction_lastName,true);
update_post_meta($post_id,'email',$getting_transaction_email,true);
update_post_meta($post_id,'password',$getting_transaction_password,true);
update_post_meta($post_id,'date_modified',$getting_transaction_date_modified,true);

// for check user exit or not in wordpress email.
$user = get_user_by( 'email', $getting_transaction_email );
    if ( $user ) {
        $user_id = $user->ID;
    } else {
        $user_id = false;
    }
}


    //function to create custom post type for foxy_report_data
    function create_post() {
    register_post_type( 'Foxy_report_data',
    // CPT Options
    
    array(
      'labels' => array(
       'name' => __( 'Foxy_report_data' ),
       'singular_name' => __( 'Foxy Reports' )
      ),
      'public' => true,
      'has_archive' => false,
      'rewrite' => array('slug' => 'foxy_report_data'),
     )
    );
    }
    add_action( 'init', 'create_post' );
    // function to create custom post type for all the missing users for report.  
    function foxy_missing_users($id,$fname,$lname,$emails,$date_create,$date_modified){

        $my_post = array(
            'post_title'    => ($fname.'&nbsp;&nbsp;'.$lname),
            'post_type'     => 'Foxy_report_data',
            'post_content'  => 'All missing users are here.',
            'post_status'   => 'publish',
                        
                        );
            //'post_author' => 1
      $posts_id = wp_insert_post( $my_post);
    update_post_meta($posts_id,'customer_id',$id,true);
    update_post_meta($posts_id,'first_name',$fname,true);
    update_post_meta($posts_id,'last_name',$lname,true);
    update_post_meta($posts_id,'email',$emails,true);
    update_post_meta($posts_id,'date_created',$date_create,true);
    update_post_meta($posts_id,'date_modified',$date_modified,true);

}
    // function used to create users if not present in wordpress database.
    function user_creation($missingEmail) {
        global $wpdb, $foxyshop_new_password_hash;
            $args = array(
                'post_type' => 'foxy_data',
                    );
            $obituary_query = new WP_Query($args);
            while ($obituary_query->have_posts()) : $obituary_query->the_post(); // for matching all emails in foxy_data custom post
                $email[] = get_post_custom_values('email');
            endwhile;
            $missing=array_diff($missingEmail, $email);
            wp_reset_postdata();
        for($i=0; $i<count($missing); $i++){
               $args = array(
              'post_type'   => 'foxy_data',
              'meta_query'  => array(
                array(
                  'value' => $missing[$i]
                )
              )
            );
            $my_query = new WP_Query( $args );
                $my_query->the_post();
                $id= get_the_ID();
                $cust_id = get_field('customer_id', $id);
                $fname = get_field('first_name', $id);
                $lname = get_field('last_name', $id);
                $email = get_field('email', $id);
                $password = get_field('password', $id);
                $foxyshop_new_password_hash=$password;
                $date = get_field('date_modified', $id);
                $usercreated[$i] = $fname. ' ' .$lname;
                $role = 'subscriber';
                 $new_user_id = wp_insert_user( array(
                    'user_login' => $email,
                    'user_pass' => wp_generate_password(),
                    'user_email' => $email,
                    'first_name' => $fname,
                    'last_name' => $lname,
                    'display_name' => $fname. ' ' .$lname,
                    'role' => apply_filters('foxyshop_default_user_role', 'subscriber'),
                ));    
                  add_user_meta($new_user_id, 'foxycart_customer_id', $cust_id, true);
                //Set Password In WordPress Database 
                $errorMsg = $new_user_id -> errors;
                $msg = $errorMsg[existing_user_login][0];
                if (!isset($msg)) {
                    $updatepwd = $wpdb->query("UPDATE $wpdb->users SET user_pass = '" . esc_sql($password) . "' WHERE ID = $new_user_id");
                } 
            wp_reset_postdata();
        } 
                if ($updatepwd == 1){
                    echo "<h1 style ='font-family:Monospace'> No of Users Created: ".count($missing)."</h1>";
                    for($j=0;$j<count($usercreated);$j++){
                        echo "<h2 style = 'font-family:Monospace'> User: " .$usercreated[$j]. "</br></h2>";
                    }
                } 
                if ($updatepwd == 0){
                    echo "<h2  style ='font-family:sans-serif'> There are no users created! </h2>"; 
                }
}
        
        



