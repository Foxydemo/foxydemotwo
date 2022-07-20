<?php 
$folder="/foxydemotwo";
require_once($_SERVER['DOCUMENT_ROOT'] . $folder . '/wp-config.php');
require_once($_SERVER['DOCUMENT_ROOT'] . $folder . '/wp-load.php');
global $wpdb;

if($_POST['action']=='cancel'){
    $name = $_POST["name"];
    $id = $_POST["id"];
    $uid = $_POST["uid"];
    date_default_timezone_set('Asia/Kolkata');
	$current_date = date('Y-m-d H:i:s', time());
    $update = $wpdb->update($wpdb->pmpro_memberships_users, array('enddate'=>$current_date, 'status' => 'expired'), array('user_id' => $uid, 'membership_id' => $id));
    if($update){
            $get_users= $wpdb->get_results("SELECT * FROM $wpdb->pmpro_memberships_users WHERE user_id= $uid");
             foreach($get_users as $get_user){
                    $id=$get_user->user_id;
                    $email=$get_user->user_email;
                    $ufname=$get_user->user_firstname;
                    $ulname=$get_user->user_lastname;
                    $fullname= $ufname.' '.$ulname.' ';
                    $subject="Your Subscription has been cancelled.";
                    $message="Dear ".$fullname.", Your Subscription has been cancelled by the administrator. Please contact for enquiry.";
                    $header = "From:admin@solari.com \r\n";
                    $header .= "MIME-Version: 1.0\r\n";
                    $header .= "Content-type: text/html\r\n";
                        try{
                        mail($email,$subject,$message,$header);
                            // echo "Mail sent";
                        }
                        catch(Exception $e){
                            echo "Mail not sent".$e->getMessage();
                        }
                }
            echo "Subscription has been cancelled and notified to $fullname";
    }
    else{
        echo "Subscription has not been cancelled!";
    }
}
