<?php 

global $wpdb;
// $grant_type = 'refresh_token';
// $refresh_token = 'n36bquPRUeRwzJ7zEfcrZD1WbrMly7sDDH6SaFaK';
// $client_id = 'client_3cevCd8ZeuSxuXPwyd3h';
// $client_secret = 't9ImLiSVItWJaxMntpgziCPprY44uMdEBqhlQR5n';
$grant_type = 'refresh_token';
$refresh_token = 'vvKk3uc3lhjkyX6U1vWLqK4BJECnhwJIbUavpnKS';
$client_id = 'client_luHCLP8quvd2byd1HstU';
$client_secret = 'svw6fAbP7W765whGqiEui1BZmdpWDY7Uvz7mqMNQ';
$prefix= $wpdb;


// For Getting Access Token from FoxyAPI
function get_access_token(){
    try{
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
    }catch (Exception $e)
    {
        echo"access token is not generated";
    }
        return $access_Token;
    }
         

// For getting store url from mother FoxyAPI
function getting_store_Url(){
    try{
        $accessToken=get_access_token();
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
    }catch(Exception $e)
    {
            echo"store url is not generated";
    }
        return $store_link;
    }
    
//for getting subscription url
function getting_subscription_Url($current_store_URL){
    try{
        global $wpdb,$prefix;
        $accessToken=get_access_token();
            
        $subscription_url = $current_store_URL;

        $curl = curl_init($subscription_url);
        curl_setopt($curl, CURLOPT_URL, $subscription_url);
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
        $parse_Resp = json_decode($resp);
        $SubscriptionDetails = $parse_Resp->_links;
        $Subscription = "fx:subscriptions";
        $subscription_url = $SubscriptionDetails->$Subscription->href;
        
        $fox_ids = cidfunction();
        foreach($fox_ids as $id) {
       
            $paramurl = $subscription_url;
            $paramCust_id = ['customer_id' => $id ];
            $dataCust_id = http_build_query($paramCust_id);
            $getParamUrl = $paramurl."?".$dataCust_id;
            
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
            $customer_details=$result_customerDetail->_embedded;
            $Subscriptions = "fx:subscriptions";
            $Subscription_link = $customer_details->$Subscriptions;
            
            $link="fx:transactions";
            $sublink = $Subscription_link[0]->_links;
            $transaction_url = $sublink->$link->href;
            
            $Transaction_CustomerDetails[] = getting_transaction_Data($transaction_url);
            $Transaction_URL = getting_transaction_Url($transaction_url);
            $Items_URL[] = getting_items ($Transaction_URL);
            //STORE THE SUBSCRIPTION CUSTOMER DETAILS INTO THE PMP DATABASE  
        }
    }
    catch(Exception $e){
        //echo $e->errorMessage();
        echo "Store URL not generated from Foxy API response.";
    }
}

// for getting transaction URl along with customer details.
function getting_transaction_Url($transaction_url){
    try{
        $accessToken=get_access_token();
        $curl = curl_init($transaction_url);
        curl_setopt($curl, CURLOPT_URL, $transaction_url);
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
        $parse_Resp = json_decode($resp);
        $tran_url = "fx:transactions";
        $gettingDetails =$parse_Resp->_embedded->$tran_url[0];
        $item="fx:items";
        $item_url = $gettingDetails->_links->$item->href;
        return $item_url;
    }
    catch(Exception $e){
        //echo $e->errorMessage();
        echo "Transaction URL not generated from Foxy API response.";
    }         
}
//to store the details of the customer per transaction
function getting_transaction_Data($transaction_url){
    try{
        $accessToken=get_access_token();
        $curl = curl_init($transaction_url);
        curl_setopt($curl, CURLOPT_URL, $transaction_url);
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
        $parse_Resp = json_decode($resp);
        $tran_url = "fx:transactions";
        $gettingDetails =$parse_Resp->_embedded->$tran_url[0];
        $start_date = strtotime($getting_items_start_date);
        $end_date = strtotime($getting_subscription_end_date);
        
        $dueDate = ($end_date - $start_date)/60/60/24;
        if($dueDate >= 0){
            $status = "active";
        }
        else{
            $status = "inactive";
        }
        $CustomerDetails = array(
            'id' => $gettingDetails->id,
            'display_id'=> $gettingDetails->display_id,
            'display_id'=> $gettingDetails->display_id,
            'is_test'=> $gettingDetails->is_test,
            'hide_transaction'=> $gettingDetails->hide_transaction,
            'data_is_fed'=> $gettingDetails->data_is_fed,
            'type'=> $gettingDetails->type,
            'source'=> $gettingDetails->source,
            'transaction_date'=> $gettingDetails->transaction_date,
            'locale_code'=> $gettingDetails->locale_code,
            'customer_first_name'=> $gettingDetails->customer_first_name,
            'customer_last_name'=> $gettingDetails->customer_last_name,
            'customer_tax_id'=> $gettingDetails->customer_tax_id,
            'customer_email'=> $gettingDetails->customer_email,
            'customer_ip'=> $gettingDetails->customer_ip,
            'ip_country'=> $gettingDetails->ip_country,
            'user_agent'=> $gettingDetails->user_agent,
            'total_item_price'=> $gettingDetails->total_item_price,
            'total_tax'=> $gettingDetails->total_tax,
            'total_shipping'=> $gettingDetails->total_shipping,
            'total_future_shipping'=> $gettingDetails->total_future_shipping,
            'total_order'=> $gettingDetails->total_order,
            'status'=> $status,
            'date_created'=> $gettingDetails->date_created,
            'date_modified'=> $gettingDetails->date_modified,
            'currency_code'=> $gettingDetails->currency_code,
            'currency_symbol'=> $gettingDetails->currency_symbol
        );
        $item="fx:items";
        $item_url = $gettingDetails->_links->$item->href;
        return $CustomerDetails;
    }
    catch(Exception $e){
        //echo $e->errorMessage();
        echo "Transaction URL not generated from Foxy API response.";
    }
        
}


// for getting items url and also to display items as to what customer have purchased.
function getting_items($item_url){
    try{
        $accessToken=get_access_token();
        $curl = curl_init($item_url);
        curl_setopt($curl, CURLOPT_URL, $item_url);
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
        $parse_Resp = json_decode($resp);
        
        $links = "fx:items";
        $itemUrl = $parse_Resp->_embedded->$links[0];
        
        $ItemsDetails = array(
            'item_category_uri' => $itemUrl->item_category_uri,
            'name'=> $itemUrl->name,
            'price'=> $itemUrl->price,
            'quantity'=> $itemUrl->quantity,
            'quantity_min'=> $itemUrl->quantity_min,
            'quantity_max'=> $itemUrl->quantity_max,
            'weight'=> $itemUrl->weight,
            'code'=> $itemUrl->code,
            'parent_code'=> $itemUrl->parent_code,
            'discount_name'=> $itemUrl->discount_name,
            'discount_type'=> $itemUrl->discount_type,
            'discount_details'=> $itemUrl->discount_details,
            'subscription_frequency'=> $itemUrl->subscription_frequency,
            'subscription_start_date'=> $itemUrl->subscription_start_date,
            'subscription_next_transaction_date'=> $itemUrl->subscription_next_transaction_date,
            'subscription_end_date'=> $itemUrl->subscription_end_date,
            'is_future_line_item'=> $itemUrl->is_future_line_item,
            'shipto'=> $itemUrl->shipto,
            'url'=> $itemUrl->url,
            'image'=> $itemUrl->image,
            'length'=> $itemUrl->length,
            'width'=> $itemUrl->width,
            'height'=> $itemUrl->height,
            'expires'=> $itemUrl->expires,
            'date_created'=> $itemUrl->date_created,
            'date_modified'=> $itemUrl->date_modified,
            'currency_symbol'=> $itemUrl->currency_symbol
        );
        return $ItemsDetails;
    }
    catch(Exception $e){
        //echo $e->errorMessage();
        echo "Item URL not generated from Foxy API response.";
    }
}

// getting store Id from storeUrl
function getting_storeId(){
    
    $storeUrl = 'https://api.foxycart.com/stores/98773';
    $storeID = preg_replace('/[^0-9]/', '', $storeUrl);
    
    return $storeID;
    
}

// for fetching products data from database
function getting_products(){
    try{
        global $wpdb,$prefix;
        $result = $wpdb->get_results ( "SELECT * FROM $wpdb->posts WHERE post_type = 'foxyshop_product'" );
        $id[] = '';
        $i=0;
        
        foreach ( $result as $print ) {
 
            $id = $print->ID;
            $value = get_post_meta($print->ID);
            
            $products[$i] = array(
                'product_id'=> $id,
                'name'=> $print->post_name,
                'desc'=> $print->post_content,
                'edit_last' => $value[edit_last][0],
                '_edit_lock' => $value[_edit_lock][0],
                '_weight' => $value[_weight][0],
                '_price' => $value[_price][0],
                '_code' => $value[_code][0],
                '_salestartdate' => $value[_salestartdate][0],
                '_saleenddate' => $value[_saleenddate][0],
                '_quantity_min' => $value[_quantity_min][0],
                '_wp_old_slug' => $value[_wp_old_slug][0],
                '_sub_frequency' => $value[_sub_frequency][0],
                '_sub_startdate' => $value[_sub_startdate][0],
                '_sub_enddate' => $value[_sub_enddate][0],
                '_saleprice' => $value[_saleprice][0],
                '_foxyshop_menu_order_3' => $value[_foxyshop_menu_order_3][0],
                );
            $i++; 
            }
        return $products; 
    }
    catch(Exception $e){
        //echo $e->errorMessage();
        echo "Products are not generated from database.";
    }
}

//to store all the foxy products in PMP database.
function storing_products($products){
    try{
    global $wpdb,$prefix;
  
    foreach($products as $key){
       
        $name=$key[name];
        $check = $wpdb->get_var ( "SELECT count(*) FROM $wpdb->pmpro_membership_levels WHERE name = '$name'" );
        if($check==0){
            
            $freq=$key[_sub_frequency];
            if($freq==null){
                $freq=0;
                $cp=0;
                $cn=0;
            }
            else{
            $split = str_split($freq);
            $n=count($split);
            $last=$split[$n-1];
            if($last=='d'){
                $cp = "day";
            }
            if($last=='w'){
                $cp= "week";
            }
            if($last=='m'){
                $cp= "month";
            }
            if($last=='y'){
                $cp= "year";
            }
            $cn=0;
            for($i=0;$i<$n-1;$i++){
                $cn.=$split[$i];
            }
            }
        if($key[_code]==null){
            $code=$key[product_id]; 
          }
         else{
            $code=$key[_code];
         }
        // Adding Column in wp_fdemo_pmpro_membership_levels table
        $wpdb->query("ALTER TABLE $wpdb->pmpro_membership_levels ADD code varchar(200) NOT NULL  AFTER expiration_period,ADD frequency VARCHAR(200) NOT NULL AFTER code,
        ADD start_date VARCHAR(200) NOT NULL AFTER frequency,ADD end_date VARCHAR(200) NOT NULL AFTER start_date; ");

       $wpdb->insert("$wpdb->pmpro_membership_levels", array(
        'name' => $name,
        'description' => $key[desc],
        'confirmation' => 0,
        'initial_payment' => $key[_price],
        'billing_amount' => $key[_saleprice],
        'cycle_number' => $cn,
        'cycle_period' => $cp,
        'billing_limit' => 0,
        'trial_amount' => 0,
        'trial_limit' => 0,
        'allow_signups' => 1,
        'expiration_number' => 0,
        'expiration_period' => 0,
        'code' => $code,
        'frequency' => $freq,
        'start_date' => $key[_sub_startdate],
        'end_date' => $key[_sub_enddate],
         )); 
        
        }
        
    }
    }
    catch(Exception $e){
        //echo $e->errorMessage();
        echo "Products are not generated or product insert query failed.";
    }
}


function hypermedia(){
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

//FETCHING ALL FOXY CUSTOMER_IDS
 function cidfunction(){
    $users = get_users( array('fields'=> array('ID')));
    $usser_arr = array();
        foreach ($users as $user) {

          //echo'<pre>';
            $usser_arr=get_user_meta($user->ID,$key = 'foxycart_customer_id');
            //print_r($usser_arr);
              $user_a[]=$usser_arr[0];
            
        }
        return $user_a;
 }


//Fetching all details from Subscription API
function fetching_subscription_details($current_store_URL){
    try{
    
    $accessToken=get_access_token();
    $Store_URL = $current_store_URL;

    $curl = curl_init($Store_URL);
    curl_setopt($curl, CURLOPT_URL, $Store_URL);
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
    $parse_Resp = json_decode($resp);
    $SubscriptionDetails = $parse_Resp->_links;
    $Subscription = "fx:subscriptions";
    $subscription_URL = $SubscriptionDetails->$Subscription->href;
    
    $limit = "limit=300";
    $getUrl = $subscription_URL."?".$limit;
   
    $curl_customerDetails = curl_init($getUrl);
    curl_setopt($curl_customerDetails, CURLOPT_URL, $getUrl);
    curl_setopt($curl_customerDetails, CURLOPT_RETURNTRANSFER, true);
              
    curl_setopt($curl_customerDetails, CURLOPT_HTTPHEADER, $headers);
    //for debug only!
    curl_setopt($curl_customerDetails, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl_customerDetails, CURLOPT_SSL_VERIFYPEER, false);
                    
    $response_customer = curl_exec($curl_customerDetails);
    curl_close($curl_customerDetails);
    $result_customerDetail = json_decode($response_customer);
    $customer_details=$result_customerDetail->_embedded;
    $Subscriptions = "fx:subscriptions";
    $Subscription_Link = $customer_details->$Subscriptions;
    $transaction_fetch="fx:last_transaction";

    $j=0;
    foreach($Subscription_Link as $key){
        
        $Transaction_URL = $key->_links->$transaction_fetch->href;
     
        $Subscription_Details[$j] = array(
            'transaction_url' =>$Transaction_URL,
            'start_date' => $key->start_date,
            'next_transaction_date'=> $key->next_transaction_date,
            'end_date'=> $key->end_date,
            'frequency'=> $key->frequency,
            'error_message'=> $key->error_message,
            'past_due_amount'=> $key->past_due_amount,
            'first_failed_transaction_date'=> $key->first_failed_transaction_date,
            'is_active'=> $key->is_active,
            'third_party_id'=> $key->third_party_id,
            'cancellation_source'=> $key->cancellation_source,
            'date_created'=> $key->date_created,
            'date_modified'=> $key->date_modified,
            'payment_type'=> $key->payment_type,
        );
        $j++;
    }
 
    
    $total_items= $result_customerDetail->total_items;
    $Next_URL='';
    $Next_URL = $result_customerDetail->_links->next->href;
    if($total_items>300){
        $total_limit=ceil(($total_items/300));
        for($i=0;$i<$total_limit-1;$i++){
            $limit = "limit=300";
            $getUrl = $Next_URL."?".$limit;
            $curl_customerDetails = curl_init($getUrl);
            curl_setopt($curl_customerDetails, CURLOPT_URL, $getUrl);
            curl_setopt($curl_customerDetails, CURLOPT_RETURNTRANSFER, true);

            curl_setopt($curl_customerDetails, CURLOPT_HTTPHEADER, $headers);
            //for debug only!
            curl_setopt($curl_customerDetails, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl_customerDetails, CURLOPT_SSL_VERIFYPEER, false);
                    
            $response_customer = curl_exec($curl_customerDetails);
            curl_close($curl_customerDetails);
            $result_customerDetail = json_decode($response_customer);
            $customer_details=$result_customerDetail->_embedded;
            $Subscriptions = "fx:subscriptions";
            $Subscription_Link = $customer_details->$Subscriptions;
            $transaction_fetch="fx:last_transaction";
            
            foreach($Subscription_Link as $key){  
             
                $Transaction_URL = $key->_links->$transaction_fetch->href;
                 
                $Subscription_Details[$j] = array(
                    'transaction_url' =>$Transaction_URL,
                    'start_date' => $key->start_date,
                    'next_transaction_date'=> $key->next_transaction_date,
                    'end_date'=> $key->end_date,
                    'frequency'=> $key->frequency,
                    'error_message'=> $key->error_message,
                    'past_due_amount'=> $key->past_due_amount,
                    'first_failed_transaction_date'=> $key->first_failed_transaction_date,
                    'is_active'=> $key->is_active,
                    'third_party_id'=> $key->third_party_id,
                    'cancellation_source'=> $key->cancellation_source,
                    'subscription_date_created'=> $key->date_created,
                    'subscription_date_modified'=> $key->date_modified,
                    'payment_type'=> $key->payment_type,
                );
                $j++; 
                    }
        $Next_URL = $result_customerDetail->_links->next->href;
        }
    }

    return $Subscription_Details;
}
catch(Exception $e){
    //echo $e->errorMessage();
    echo "Store URL not generated from Foxy API response.";
}
}

//fetching transaction_customer_details from API
function fetching_transaction_customer_details($Subscription_Details){
    try{

    $accessToken=get_access_token();
    $i=0;
    foreach($Subscription_Details as $key){

        $curl = curl_init($key[transaction_url]);
        curl_setopt($curl, CURLOPT_URL, $key[transaction_url]);
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
        $parse_Resp = json_decode($resp);
        $item_fetch="fx:items";
        $item_URL=$parse_Resp->_links->$item_fetch->href;
        $payment_fetch="fx:payments";
        $payment_URL=$parse_Resp->_links->$payment_fetch->href;

        $Transaction_Details[$i]=array(
            'item_url' => $item_URL,
            'transaction_id' => $parse_Resp->id,
            'display_id'=> $parse_Resp->display_id,
            'is_test'=> $parse_Resp->is_test,
            'hide_transaction'=> $parse_Resp->hide_transaction,
            'data_is_fed'=> $parse_Resp->data_is_fed,
            'type'=> $parse_Resp->type,
            'source'=> $parse_Resp->source,
            'transaction_date'=> $parse_Resp->transaction_date,
            'locale_code'=> $parse_Resp->locale_code,
            'customer_first_name'=> $parse_Resp->customer_first_name,
            'customer_last_name'=> $parse_Resp->customer_last_name,
            'customer_tax_id'=> $parse_Resp->customer_tax_id,
            'customer_email'=> $parse_Resp->customer_email,
            'customer_ip'=> $parse_Resp->customer_ip,
            'ip_country'=> $parse_Resp->ip_country,
            'user_agent'=> $parse_Resp->user_agent,
            'total_item_price'=> $parse_Resp->total_item_price,
            'total_tax'=> $parse_Resp->total_tax,
            'total_shipping'=> $parse_Resp->total_shipping,
            'total_future_shipping'=> $parse_Resp->total_future_shipping,
            'total_order'=> $parse_Resp->total_order,
            'status'=> $parse_Resp->status,
            'transaction_date_created'=> $parse_Resp->date_created,
            'transaction_date_modified'=> $parse_Resp->date_modified,
            'currency_code'=> $parse_Resp->currency_code,
            'currency_symbol'=> $parse_Resp->currency_symbol,
            'payment_url' => $payment_URL
            );
            $i++; 
        }
    
            $Subscription_Transaction_Results = array();
            foreach($Subscription_Details as $key=>$val){ // Loop though one array
                    $val2 = $Transaction_Details[$key]; // Get the values from the other array
                    $Subscription_Transaction_Results[$key] = $val + $val2; // combine 'em
                    }
        return $Subscription_Transaction_Results;
    }
    catch(Exception $e){
        //echo $e->errorMessage();
        echo "Subscription Details are not generated from Foxy API response.";
    }
}

//fetching itenms for particular transaction from API above.
function getting_items_details($Subscription_Transaction_Results){
    try{
    
    $accessToken=get_access_token();
    $i=0;
    foreach($Subscription_Transaction_Results as $key){
        
        $curl = curl_init($key[item_url]);
        curl_setopt($curl, CURLOPT_URL, $key[item_url]);
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
        $parse_Resp = json_decode($resp);
        $item_fetch = "fx:items";
        $item_details = $parse_Resp->_embedded->$item_fetch[0];
        
        $Item_Details[$i]=array(
            'item_category_uri' => $item_details->item_category_uri,
            'name'=> $item_details->name,
            'price'=> $item_details->price,
            'quantity'=> $item_details->quantity,
            'quantity_min'=> $item_details->quantity_min,
            'quantity_max'=> $item_details->quantity_max,
            'weight'=> $item_details->weight,
            'code'=> $item_details->code,
            'parent_code'=> $item_details->parent_code,
            'discount_name'=> $item_details->discount_name,
            'discount_type'=> $item_details->discount_type,
            'discount_details'=> $item_details->discount_details,
            'subscription_frequency'=> $item_details->subscription_frequency,
            'subscription_start_date'=> $item_details->subscription_start_date,
            'subscription_next_transaction_date'=> $item_details->subscription_next_transaction_date,
            'subscription_end_date'=> $item_details->subscription_end_date,
            'is_future_line_item'=> $item_details->is_future_line_item,
            'shipto'=> $item_details->shipto,
            'url'=> $item_details->url,
            'image'=> $item_details->image,
            'length'=> $item_details->length,
            'width'=> $item_details->width,
            'height'=> $item_details->height,
            'expires'=> $item_details->expires,
            'item_date_created'=> $item_details->date_created,
            'item_date_modified'=> $item_details->date_modified,
            'item_currency_symbol'=> $item_details->currency_symbol
            );
     $i++;     
    }

            $Subscription_Transaction_Item_Results = array();
            foreach($Subscription_Transaction_Results as $key=>$val){ // Loop though one array
                    $val2 = $Item_Details[$key]; // Get the values from the other array
                    $Subscription_Transaction_Item_Results[$key] = $val + $val2; // combine 'em
                    }

        return $Subscription_Transaction_Item_Results;
    }
    catch(Exception $e){
        //echo $e->errorMessage();
        echo "Transaction Details not generated from Foxy API response.";
    }
}

//fetching payment details from the above API
function getting_payments_details($Subscription_Transaction_Item_Results){
    try{
    
    $accessToken=get_access_token();
    $i=0;
    foreach($Subscription_Transaction_Item_Results as $key){
        
        $curl = curl_init($key[payment_url]);
        curl_setopt($curl, CURLOPT_URL, $key[payment_url]);
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
        $parse_Resp = json_decode($resp);
        $payment_fetch="fx:payments";
        $payment_details=$parse_Resp->_embedded->$payment_fetch[0];
        
         $Payment_Details[$i]=array(
            'ctype' => $payment_details->type,
            'gateway_type'=> $payment_details->gateway_type,
            'processor_response'=> $payment_details->processor_response,
            'processor_response_details'=> $payment_details->processor_response_details,
            'purchase_order'=> $payment_details->purchase_order,
            'cc_number_masked'=> $payment_details->cc_number_masked,
            'cc_type'=> $payment_details->cc_type,
            'cc_exp_month'=> $payment_details->cc_exp_month,
            'cc_exp_year'=> $payment_details->cc_exp_year,
            'fraud_protection_score'=> $payment_details->fraud_protection_score,
            'paypal_payer_id'=> $payment_details->paypal_payer_id,
            'third__party__id'=> $payment_details->third_party_id,
            'amount'=> $payment_details->amount,
            'payment_date_created'=> $payment_details->date_created,
            'payment_date_modified'=> $payment_details->date_modified,
            );
    $i++;    
    }
    
            $Subscription_Transaction_Item_Payment_Results = array();
            foreach($Subscription_Transaction_Item_Results as $key=>$val){ // Loop though one array
                    $val2 = $Payment_Details[$key]; // Get the values from the other array
                    $Subscription_Transaction_Item_Payment_Results[$key] = $val + $val2; // combine 'em
                    }
        return $Subscription_Transaction_Item_Payment_Results;    
    }
    catch(Exception $e){
        //echo $e->errorMessage();
        echo "Item Details are not generated from Foxy API response.";
    }
}

//Fetching the details of subscribe users data
function subscriber_user($subscriber)
{
     //echo'<pre>';
     //print_r($subscriber);
    
    global $wpdb,$prefix;
    
     foreach ($subscriber as $row)
        {
                // echo'<pre>';
                // print_r($row);
                
             $email = $row[customer_email];
             if($email == null){$email=0;}
             $post_id = $wpdb->get_results("SELECT ID FROM $wpdb->users WHERE user_email ='$email'");
             $Customer_ID = $post_id[0]->ID; 
             
            if($Customer_ID!=NULL){

                    $check_user = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->pmpro_memberships_users WHERE user_id ='$Customer_ID'" );
                    //print_r($check_user);
                    if($check_user==0){
                        
                              
                                  $code=$row[code];
                                  if($code==null){ $code=0; }
                                    $start_date =$row[start_date];
                                    if($start_date == null){$start_date=0;}
                                    
                                    $end_date =$row[end_date];
                                    if($end_date == null){$end_date=0;}
                                    
                                    $date_modified = $row[date_modified];
                                    if($date_modified == null){$date_modified=0;}
                                    
                                    $user_first_name=$row[customer_first_name];
                                    if( $user_first_name==null){$user_first_name=0;}
                                    
                                    $user_last_name=$row[customer_last_name];
                                    if( $user_last_name==null){$user_last_name=0;}
                                    
                                    $source = $row[source];
                                    if($source == null){$source=0;}
                                    
                                    $price = $row[price];
                                    if($price == null){$price = 0;}
                                    
                                      $freq=$row[frequency];
                                   if($freq==null){
                                                    $freq=0;
                                                    $cp=0;
                                                    $cn=0;
                                                }
                                        else{
                                        $split = str_split($freq);
                                        $n=count($split);
                                        $last=$split[$n-1];
                                        if($last=='d'){
                                            $cp = "day";
                                        }
                                        if($last=='w'){
                                            $cp= "week";
                                        }
                                        if($last=='m'){
                                            $cp= "month";
                                        }
                                        if($last=='y'){
                                            $cp= "year";
                                        }
                                        $cn=0;
                                        for($i=0;$i<$n-1;$i++){
                                            $cn.=$split[$i];
                                        }
                                        }
                                        $strdate = $row[start_date];
                                        $endate = $row[end_date];
                                        $start_date1 = strtotime($strdate);
                                        $end_date1 = strtotime($endate);
              
                                         $dueDate = ($end_date1 - $start_date1)/60/60/24;
                                        if($dueDate == 0){
                                        $inactive = "inactive";
                                        }
                                        else{
                                            $active = "active";
                                        }
                                        // $check=array($start_date,$end_date,$date_modified,$email,$user_first_name,$user_last_name,$source,$price,$code);
                                        // echo'<pre>';
                                        // print_r($check);
                                $code_id = $wpdb->get_results("SELECT id FROM $wpdb->pmpro_membership_levels WHERE code ='$code'");
                                // echo'<pre>';
                                // print_r($code_id[0]->id);
                                $newcode = $code_id[0]->id;
                               if($newcode==null){$newcode=0;}
                                // Adding Column in wp_fdemo_pmpro_memberships_users table
        $wpdb->query("ALTER TABLE $wpdb->pmpro_memberships_users ADD user_firstname varchar(200) NOT NULL  AFTER source,ADD user_lastname VARCHAR(200) NOT NULL AFTER user_firstname,
ADD user_email VARCHAR(200) NOT NULL AFTER user_lastname; ");
                                
                                 $wpdb->insert("$wpdb->pmpro_memberships_users",array(
                                    
                             'user_id'=>$Customer_ID,
                             'membership_id'=>$newcode,
                             'code_id'=>$code,
                             'initial_payment'=>$price,
                             'billing_amount'=>$price,
                             'cycle_number'=>$cn,
                             'cycle_period'=>$cp,
                             'billing_limit'=>0,
                             'trial_amount'=>0,
                             'trial_limit'=>0,
                             'status'=>$active,
                             'startdate'=>$start_date,
                             'enddate'=>$end_date,
                             'modified'=>$date_modified,
                             'user_firstname'=>$user_first_name,
                             'user_lastname'=>$user_last_name,
                             'user_email'=> $email,
                             'source'=>$source,
                             ));
                               
                        
                    }
                   
            }
        }

}

//function to store subscriptions details from foxy to PMP database
function storing_SubscriptionPayment_details($Subscription_Details){
    try{
    global $wpdb,$prefix;
    foreach($Subscription_Details as $key){
        
        $email = $key[customer_email];
        $post_id = $wpdb->get_results("SELECT ID FROM $wpdb->users WHERE user_email ='$email' ");
        $user_id= $post_id[0]->ID;
        if($user_id==null){$user_id=0;}
        $memb_name = $key[name];
        $post_name = $wpdb->get_results("SELECT ID FROM $wpdb->pmpro_membership_levels WHERE name ='$memb_name' ");
        $membership_id=$post_name[0]->ID;
        $bytes = random_bytes(5);
        $random = bin2hex($bytes);
        

        $check_user = $wpdb->get_var( "SELECT count(*) FROM  $wpdb->pmpro_membership_orders WHERE user_id = '$user_id' AND membership_id = '$membership_id'"  );

        if($check_user==0){
                    
            $paypal_payer_id=$key[paypal_payer_id];
            if($paypal_payer_id==null) { $paypal_payer_id=0; }
            $ip_country=$key[ip_country];
            if($ip_country==null) { $ip_country=0; }
            $price=$key[price];
            if($price==null) { $price=0; }
            $total_tax=$key[total_tax];
            if($total_tax==null) { $total_tax=0; }
            $transaction_id=$key[transaction_id];
            if($transaction_id==null) { $transaction_id=0; }
            $payment_type=$key[payment_type];
            if($payment_type==null) { $payment_type=0; }
            $cc_type=$key[cc_type];
            if($cc_type==null) { $cc_type=0; }
            $cc_number_masked=$key[cc_number_masked];
            if($cc_number_masked==null) { $cc_number_masked=0; }
            $cc_exp_month=$key[cc_exp_month];
            if($cc_exp_month==null) { $cc_exp_month=0; }
            $cc_exp_year=$key[cc_exp_year];
            if($cc_exp_year==null) { $cc_exp_year=0; }
            $processor_response=$key[processor_response];
            if($processor_response==null) { $processor_response=0; }
            $status=$key[status];
            if($status==null) { $status=0; }
            $gateway_type=$key[gateway_type];
            if($gateway_type==null) { $gateway_type=0; }
            $payment_date_created=$key[payment_date_created];
            if($payment_date_created==null) { $payment_date_created=0; }
            $type=$key[type];
            if($type==null) { $type=0; }
            $billing_name=$key[customer_first_name].' '.$key[customer_last_name];
            
             $wpdb->insert("$wpdb->pmpro_membership_orders", array(
                'code' => $random,
                'session_id' => $random,
                'user_id' => $user_id,
                'membership_id' => $membership_id,
                'paypal_token' => $paypal_payer_id,
                'billing_name' => $billing_name, 
                'billing_street' => $ip_country,
                'billing_city' => $ip_country,
                'billing_state' => $ip_country,
                'billing_zip' => 0,
                'billing_country' => $ip_country,
                'billing_phone' => 0,
                'subtotal' => $price,
                'tax' => $total_tax,
                'couponamount' => 0,
                'checkout_id' => $transaction_id,
                'certificate_id' => $transaction_id,
                'certificateamount' => 0,
                'total' => $price,
                'payment_type' => $payment_type,
                'cardtype' => $cc_type,
                'accountnumber' => $cc_number_masked,
                'expirationmonth' => $cc_exp_month,
                'expirationyear' => $cc_exp_year,
                'status' => $status,
                'gateway' => $gateway_type,
                'gateway_environment' => $gateway_type,
                'payment_transaction_id' => $transaction_id,
                'subscription_transaction_id' => $transaction_id,
                'timestamp' => $payment_date_created,
                'affiliate_id' => $transaction_id,
                'affiliate_subid' => 0,
                'notes' => $type,
                 ));
        }
        
    }
    }
    catch(Exception $e){
        //echo $e->errorMessage();
        echo "Subscription Details are not generated from Foxy API response.";
    }
}
