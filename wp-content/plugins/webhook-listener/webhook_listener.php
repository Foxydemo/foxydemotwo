<?php
    
    include dirname(__FILE__).'functions.php';
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
    $getting_transaction_id = $webhook_data->_embedded->$fx_customer->id;
    $getting_transaction_firstName = $webhook_data->_embedded->$fx_customer->first_name;
    $getting_transaction_lastName = $webhook_data->_embedded->$fx_customer->last_name;
    $getting_transaction_email = $webhook_data->_embedded->$fx_customer->email;
    $getting_transaction_password = $webhook_data->_embedded->$fx_customer->password_hash;
    $getting_transaction_date_modified = $webhook_data->_embedded->$fx_customer->date_modified;
    
    // here calling the function
    echo insert_foxy_data($getting_transaction_id,$getting_transaction_firstName,$getting_transaction_lastName,$getting_transaction_email,$getting_transaction_password,$getting_transaction_date_modified);
    
      
    //$parse_email = json_encode($getting_transaction_details);
    
    $myfile = fopen("detail.php", 'a') or die("Unable to open file!");
    $txt = $data;
    fwrite($myfile, $txt);

    // $txt1 = $getting_transaction_email;
    // fwrite($myfile, $txt1);
    
    
    fclose($myfile);
?>