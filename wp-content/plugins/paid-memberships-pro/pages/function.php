<?php
$folder="/Kodtiger/foxydemotwo";
require_once($_SERVER['DOCUMENT_ROOT'] . $folder . '/wp-config.php');
require_once($_SERVER['DOCUMENT_ROOT'] . $folder . '/wp-load.php');
global $wpdb;

if($_POST['action']=='checkout'){
    $level = $_POST["lev"];
    $level_details = $wpdb->get_results("SELECT * FROM wp_fdemo_pmpro_membership_levels WHERE id = '$level'");
    if(!empty($level_details)){
    $levels = $level_details[0];
    $output['id']= $level_details[0]->id;
    $output['name']= $level_details[0]->name;
    $output['description']= $level_details[0]->description;
    $output['confirmation']= $level_details[0]->confirmation;
    $output['initial_payment']= $level_details[0]->initial_payment;
    $output['billing_amount']= $level_details[0]->billing_amount;
    $output['cycle_number']= $level_details[0]->cycle_number;
    $output['cycle_period']= $level_details[0]->cycle_period;
    $output['billing_limit']= $level_details[0]->billing_limit;
    $output['trial_amount']= $level_details[0]->trial_amount;
    $output['trial_limit']= $level_details[0]->trial_limit;
    $output['allow_signups']= $level_details[0]->allow_signups;
    $output['expiration_number']= $level_details[0]->expiration_number;
    $output['expiration_period']= $level_details[0]->expiration_period;
    $output['code']= $level_details[0]->code;
    $output['frequency']= $level_details[0]->frequency;
    $output['start_date']= $level_details[0]->start_date;
    $output['end_date']= $level_details[0]->end_date;
    }
    echo json_encode($output);
}

?>