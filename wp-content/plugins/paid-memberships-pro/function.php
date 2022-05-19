<?php
$folder="/Kodtiger/foxydemotwo";
require_once($_SERVER['DOCUMENT_ROOT'] . $folder . '/wp-config.php');
require_once($_SERVER['DOCUMENT_ROOT'] . $folder . '/wp-load.php');
global $wpdb;


if($_POST['action']=='checkout'){
    $id= $_POST["id"];
    $sub_details = $wpdb->get_results("SELECT * FROM wp_fdemo_pmpro_membership_orders WHERE id = '$id'");
    if(!empty($sub_details)){
    $id = $sub_details[0];
    $output['id']= $sub_details[0]->id;
    $output['code']=$sub_details[0]->code;
    $output['session_id']=$sub_details[0]->session_id;
    $output['user_id']= $sub_details[0]->user_id;
    $output['membership_id']= $sub_details[0]->membership_id;
    $output['paypal_token']= $sub_details[0]->paypal_token;
    $output['billing_name']= $sub_details[0]->billing_name;
    $output['billing_state']= $sub_details[0]->billing_state;
    $output['billing_city']= $sub_details[0]->billing_city;
    $output['billing_zip']= $sub_details[0]->billing_zip;
    $output['billing_country']= $sub_details[0]->billing_country;
    $output['billing_phone']= $sub_details[0]->billing_phone;
    $output['subtotal']= $sub_details[0]->subtotal;
    $output['tax']= $sub_details[0]->tax;
    $output['couponamount']= $sub_details[0]->couponamount;
    $output['checkout_id']= $sub_details[0]->checkout_id;
    $output['certificate_id']= $sub_details[0]->certificate_id;
    $output['certificateamount']= $sub_details[0]->certificateamount;
    $output['expirationmonth']= $sub_details[0]->expirationmonth;
    $output['expirationyear']= $sub_details[0]->expirationyear;
    $output['gateway']= $sub_details[0]->gateway;
    $output['gateway_environment']= $sub_details[0]->gateway_environment;
    $output['payment_transaction_id']= $sub_details[0]->payment_transaction_id;
    $output['subscription_transaction_id']= $sub_details[0]->subscription_transaction_id;
    $output['timestamp']= $sub_details[0]->timestamp;
    $output['affiliate_id']= $sub_details[0]->affiliate_id;
    $output['affiliate_subid']= $sub_details[0]->affiliate_subid;
    $output['notes']= $sub_details[0]->notes;
    }
    echo json_encode($output);
}

?>