<?php
    
    // header('Content-Type: application/json');
    // define('FOXY_WEBHOOK_ENCRYPTION_KEY', 'KSQ4LN5L7C3NFD8NR66D897CKH9KSVMC');
    define('FOXY_WEBHOOK_ENCRYPTION_KEY', 'J7G9WVSCPF3BBTC4FZGTNC8Z798QFBP2');
 
    $data = file_get_contents('php://input');
    
    // $parts = explode(':', $data);
    // $mac = $parts[0];
    // $iv = $parts[1];
    // $data = $parts[2];
     
    // $calc_mac = hash('sha256', "$iv:$data");
     
    // if (hash_equals($calc_mac, $mac)) {
    //     $iv = hex2bin($iv);
    //     $key = hex2bin(hash('sha256', FOXY_WEBHOOK_ENCRYPTION_KEY));
     
    //     if ($data = openssl_decrypt($data, 'aes-256-cbc', $key, 0, $iv)) {
    //         $parsedData = json_decode($data, true);
    //     } else {
    //         while ($msg = openssl_error_string()) {
    //             echo("Openssl error: " . $msg);
    //         }
    //         http_response_code(500);
    //         return;
    //     }
    // } else {
    //     // Encrypted data corrupted
    //     echo("Encrypted data corrupted");
        
    //     $myfile = fopen("datafromjsonwebhook.txt", 'a') or die("Unable to open file!");
    //     $txt = "failed, Encrypted data corrupted";
    //     fwrite($myfile, $txt);
    //     fclose($myfile);
        
    //     http_response_code(500);
    //     return;
    // }
    
    //$parsedData = json_decode($data, true);
    $event = $_SERVER['HTTP_FOXY_WEBHOOK_EVENT'];
     
    // Verify the webhook payload
    $signature = hash_hmac('sha256', $data, FOXY_WEBHOOK_ENCRYPTION_KEY);
    if (!hash_equals($signature, $_SERVER['HTTP_FOXY_WEBHOOK_SIGNATURE'])) {
        echo "Signature verification failed - data corrupted";
        http_response_code(500);
        return;
    }
    
    $datainstring = implode("|", $parsedData);
    
    $myfile = fopen("data_from_webhook.txt", 'a') or die("Unable to open file!");
    $txt = $data;
    fwrite($myfile, $txt);
    fclose($myfile);
     
    // if (is_array($parsedData)) {
    //     // Handle the payload
     
    //     if ($event == "transaction/created") {
    //         // The following is an example of working with the transaction/created payload
            
    //         $datainstring = implode("|", $parsedData);
            
    //         $myfile = fopen("data_from_json_webhook.txt", 'a') or die("Unable to open file!");
    //         $txt = $datainstring;
    //         fwrite($myfile, $txt);
    //         $txt = "Jane Doe\n";
    //         fwrite($myfile, $txt);
    //         fclose($myfile);
    //     }
     
    // } else {
    //     // JSON data not found
    //     echo("No data");
    //     http_response_code(500);
    //     return;
    // }

?>