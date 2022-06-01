<?php
    
    // include dirname(__FILE__).'../../../wp-load.php';
    //include dirname(__FILE__).'functions.php';
    define('FOXY_WEBHOOK_ENCRYPTION_KEY', 'J7G9WVSCPF3BBTC4FZGTNC8Z798QFBP2');

    $data = file_get_contents('php://input');
    
    $event = $_SERVER['HTTP_FOXY_WEBHOOK_EVENT'];
     
    // Verify the webhook payload
    $signature = hash_hmac('sha256', $data, FOXY_WEBHOOK_ENCRYPTION_KEY);
    if (!hash_equals($signature, $_SERVER['HTTP_FOXY_WEBHOOK_SIGNATURE'])) {
        echo "Signature verification failed - data corrupted";
        http_response_code(500);
        return;
    }
    //global $wpdb;
    //$datainstring = implode("|", $parsedData);
    $webhook_data = json_decode($data);
    
    $fx_customer = "fx:customer";
    $fx_items = "fx:items";
    $fx_billing = "fx:billing_addresses";
    $fx_shipments = "fx:shipments";
    $fx_payment = "fx:payments";
    
   
    $getting_items_code = $webhook_data->_embedded->$fx_items[0]->code;
    $getting_items_price = $webhook_data->_embedded->$fx_items[0]->price;
    $getting_subscription_start_date = $webhook_data->_embedded->$fx_items[0]->subscription_start_date;
    $getting_items_base_price = $webhook_data->_embedded->$fx_items[0]->base_price;
    $getting_subscription_next_transaction_date = $webhook_data->_embedded->$fx_items[0]->subscription_next_transaction_date;
    $getting_items_image = $webhook_data->_embedded->$fx_items[0]->image;
    $getting_subscription_end_date = $webhook_data->_embedded->$fx_items[0]->subscription_end_date;
    $getting_subscription_date_modified = $webhook_data->_embedded->$fx_items[0]->date_modified;
    $getting_subscription_frequency = $webhook_data->_embedded->$fx_items[0]->subscription_frequency;
    
    
    $getting_customer_id = $webhook_data->_embedded->$fx_customer->id;
    $getting_transaction_firstName = $webhook_data->_embedded->$fx_customer->first_name;
    $getting_transaction_lastName = $webhook_data->_embedded->$fx_customer->last_name;
    $getting_transaction_email = $webhook_data->_embedded->$fx_customer->email;
    $getting_transaction_password = $webhook_data->_embedded->$fx_customer->password_hash;
    $getting_transaction_date_modified = $webhook_data->_embedded->$fx_customer->date_modified;
    
    
    $fullname = $getting_transaction_firstName .' '. $getting_transaction_lastName;
    
    
    $billing_address1 = $webhook_data->_embedded->$fx_billing[0]->address1;
    $billing_address2 = $webhook_data->_embedded->$fx_billing[0]->address2;
    $billing_city = $webhook_data->_embedded->$fx_billing[0]->city;
    $billing_zip = $webhook_data->_embedded->$fx_billing[0]->customer_postal_code;
    $billing_country = $webhook_data->ip_country;
    $billing_phone = $webhook_data->_embedded->$fx_shipments[0]->phone;
    $tax = $webhook_data->_embedded->$fx_shipments[0]->total_tax;
    $payment_type = $webhook_data->_embedded->$fx_payment[0]->type;
    $payment_card_type = $webhook_data->_embedded->$fx_payment[0]->cc_type;
    $payment_card_number = $webhook_data->_embedded->$fx_payment[0]->cc_number_masked;
    $payment_cc_exp_month = $webhook_data->_embedded->$fx_payment[0]->cc_exp_month;
    $payment_cc_exp_year = $webhook_data->_embedded->$fx_payment[0]->cc_exp_year;
    $payment_status = $webhook_data->status;
    $source = $webhook_data->source;
    $transcation_id = $webhook_data->id;
    $payment_gateway_type = $webhook_data->_embedded->$fx_payment[0]->gateway_type;
    $payment_transaction_id = $webhook_data->display_id;
    
    $address = $billing_address1 .' '. $billing_address2;
    
    $bytes = random_bytes(5);
    $random = bin2hex($bytes);
    
    // getting the user Id from wordpress user
        global $wpdb;
        $result = $wpdb->get_results ( "SELECT ID FROM wp_fdemo_users WHERE (user_email ='". $getting_transaction_email ."')" );
        
         $membership_id = $wpdb->get_results("SELECT id FROM wp_fdemo_pmpro_membership_levels WHERE code ='$getting_items_code'");
        $new_membership_id = $membership_id[0]->id;
        date_default_timezone_set('Asia/Kolkata');
		$current_date = date('Y-m-d H:i:s', time());
     //for insert the all data in PMP order table name wp_fdemo_pmpro_membership_orders
      global $wpdb;
        $wpdb->insert('wp_fdemo_pmpro_membership_orders', array(
        'code' => $random,
        'session_id' => $random,
        'user_id' => $result[0]->ID,
        'membership_id' => $new_membership_id,
        'billing_name' => $fullname, 
        'billing_street' => $address,
        'billing_city' => $billing_city,
        'billing_zip' => $billing_zip,
        'billing_country' => $billing_country,
        'billing_phone' => $billing_phone,
        'subtotal' => $getting_items_price,
        'tax' => $tax,
        'checkout_id' => $transcation_id,
        'certificate_id' => $transcation_id,
        'total' => $getting_items_price,
        'payment_type' => $payment_type,
        'cardtype' => $payment_card_type,
        'accountnumber' => $payment_card_number,
        'expirationmonth' => $payment_cc_exp_month,
        'expirationyear' => $payment_cc_exp_year,
        'status' => $payment_status,
        'gateway' => $payment_gateway_type,
        'gateway_environment' => $payment_gateway_type,
        'payment_transaction_id' => $payment_transaction_id,
        'subscription_transaction_id' => $payment_transaction_id,
        'timestamp' => $current_date,
         ),array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s','%s','%s' ));
         
        // $errormsg = $wpdb->last_error; 
    
    
    
    $split = str_split($getting_subscription_frequency);
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
    
    // for user create in wordpress
     wp_insert_user( array(
                    'user_login' => $getting_transaction_email,
                    'first_name' => $getting_transaction_firstName,
                    'last_name' => $getting_transaction_lastName,
                    'user_pass' => $getting_transaction_password,
                    'user_email' => $getting_transaction_email,
                    'display_name' => $getting_transaction_firstName. ' ' .$getting_transaction_lastName,
                    'role' => apply_filters('foxyshop_default_user_role', 'subscriber'),
                )); 
        
         $start_date = strtotime($getting_subscription_start_date);
        $end_date = strtotime($getting_subscription_end_date);
          
        $dueDate = ($end_date - $start_date)/60/60/24;
        if($dueDate == 0){
        $active = "inactive";
        }
        else{
            $active = "active";
        }
        
        // Adding Column in wp_fdemo_pmpro_memberships_users table
        $wpdb->query("ALTER TABLE wp_fdemo_pmpro_memberships_users ADD user_firstname varchar(200) NOT NULL  AFTER source,ADD user_lastname VARCHAR(200) NOT NULL AFTER user_firstname,
ADD user_email VARCHAR(200) NOT NULL AFTER user_lastname; ");
       
        // for insert the user_Id in PMP wp_fdemo_pmpro_memberships_users
        $wpdb->insert('wp_fdemo_pmpro_memberships_users', array(
        'user_id' => $result[0]->ID,
        'membership_id' => $new_membership_id,
        'code_id'=>$getting_items_code,
        'initial_payment' => $getting_items_base_price,
        'billing_amount' => $getting_items_price,
        'cycle_number' => $cn,
        'cycle_period' => $cp,
        'status' => $active,
        'startdate' => $getting_subscription_start_date,
        'enddate' => $getting_subscription_next_transaction_date,
        'modified' => $getting_subscription_date_modified,
        'source' => $source,
        'user_firstname' => $getting_transaction_firstName,
        'user_lastname' => $getting_transaction_lastName,
        'user_email' => $getting_transaction_email,
         ));
         
         
      
    //$parse_email = json_encode($getting_transaction_details);
    
    // $myfile = fopen("data.txt", 'a') or die("Unable to open file!");
    // // $txt = $billing_country;
    // $txt = $data;
    // fwrite($myfile, $txt);

    // $txt1 = $billing_phone;
    // fwrite($myfile, $txt1);
    
    
    // fclose($myfile);
?>