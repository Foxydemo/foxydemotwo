<?php 
function Subscription_checking()
{		echo"subscriptions details";
		global $wpdb;
	$sub_details = $wpdb->get_results("SELECT * FROM $wpdb->pmpro_memberships_users ");


		 foreach($sub_details as $details)
		 {
		 	
		 	$subscription_details[]= $details->user_id.' | '.$details->enddate;		 	 	
		    
		}
		echo'<pre>';
		print_r($subscription_details);
		
		return($subscription_details);

}
?>

