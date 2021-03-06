<?php
<?php 
function Subscription_checking()
{		
		global $wpdb;
	$sub_details = $wpdb->get_results("SELECT * FROM $wpdb->pmpro_memberships_users ");


		 foreach($sub_details as $details)
		 {
		 	
		 	$subscription_details[]= $details->user_id.' | '.$details->enddate;		 	 	
		    
		}
		return($subscription_details);
}

// To check subscription enddate and display remaining days for active users and displays 0 for expired members.
function check_subscriptionDate($check){

	for($i=0;$i<count($check);$i++){
	
		$split= (explode("|",$check[$i]));
		$user_id=$split[0];
		$last_date=$split[1];
		
		date_default_timezone_set('Asia/Kolkata');
		$current_date = date('Y-m-d H:i:s', time());

		$curr_date= strtotime($current_date);
		$end_date= strtotime($last_date);  

		$dueDate = ($end_date - $curr_date)/60/60/24;
		$decimal=$dueDate-floor($dueDate);
		$hours=$decimal*24;
		$decihours=$hours-floor($hours);
		$mins=$decihours*60;
		$decimins=$mins-floor($mins);
		$seconds=round($decimins*60);

		if($dueDate>0){ //for active subscription, displays remaining days and time.
			$checking[]=($user_id.' | '.(int)$dueDate.' | '.(int)$hours.':'.(int)$mins. ':'.$seconds);	
		}
		elseif($dueDate<=0){ //for expired subscription, displays 0, 0 means expired subscription.
			$checking[] =($user_id.' | '. 0);
		}
   	}
	   return $checking;
}

// Changing user role to none for expired subscription members.
function user_role_change($check){
	global $wpdb;
	for($i=0;$i<count($check);$i++){
		$split= (explode("|",$check[$i]));
		$id = $split[0];
		$expired = $split[1];
		if($expired==0){
			if($id!=1){ //to avoid the change in user role to admin.
			$user_id = $id;
			$role = 'none';
			$user_id = wp_update_user( [ 
				'ID'   => $user_id, 
				'role' => $role
			] );
			// echo"<pre>";
			// // echo $user_id."<br>";
			
			}
		}
	}
}