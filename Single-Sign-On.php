<?php
/*
 
	FoxyCart Shared Authentication for FoxyCart v051
 
Two options:
 
Option 1: If you have an existing session-based authentication
	include file, then include it at the top of this file and
	use this file as your shared authentication end point in the
	FoxyCart admin.	Ideally, your authentication script should
	ideally know which FoxyCart customer_id is logged in.
 
Option 2: Include this file in your existing authentication check
	to create a new, publicly accessible shared authentication
	endpoint which you'll configure in the FoxyCart admin. Be
	sure no output has taken place yet, since this file	will do
	a redirect every time.
 
*/
/*************** EDIT THESE VALUES *******************/
// Put in a boolean value here which represents if the
// current user session is authenticated. 
$user_is_authenticated = true;
 
// Put in your FoxyCart api / datafeed access key here.
// This value should match the value setup in your
// FoxyCart admin under "advanced".
$foxycart_api_key = 'spfx042ef8b9c0ad08c5450144dcb0a6b916fddcc8eb9b9266926e83b1a2ea5cc90e';
 
// Put in your full foxycart store domain here.
$foxycart_domain = 'foxydemo';
 
// If the user is not logged in, do you still want to
// allow them to proceed to checkout?
$allow_non_auth_checkout = true;
// if not, you must specify a redirect page (such as your login page?)
$redirect_url = 'https://foxyshopdemotwo.kodetiger.in/login.php';
 
// Put in your database query or session variable here
// for the current logged in user's FoxyCart customer_id	
$foxycart_customer_id = 32676902;
/**************************************************/
 
 
$return_hash = '';
$customer_id = 0;
$timestamp = 0;
$fcsid = '';
 
if ($user_is_authenticated) {
	$customer_id = $foxycart_customer_id;
}
if (isset($_GET['timestamp']) && isset($_GET['fcsid'])) {
	$fcsid = $_GET['fcsid']; 
	$timestamp = $_GET['timestamp'] + (60 * 30); // valid for 30 minutes; 
}
 
/*
// Uncomment this block of code to fetch the current cart contents for this user
// Send the session ID
$foxyData            = array();
$foxyData["fcsid"]   = $fcsid;
$foxyData["output"]  = 'json';
 
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,"https://" . $foxycart_domain . "/cart");
curl_setopt($ch,CURLOPT_POSTFIELDS, $foxyData);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch,CURLOPT_TIMEOUT, 15);
// If you get SSL errors, you can uncomment the following, or ask your host to add the appropriate CA bundle
// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = trim(curl_exec($ch));
 
if ($response == false) {
	//FAIL
} else {
	//SUCCESS
	$response = json_decode($response,true);
}
 
curl_close($ch);
*/
 
 
if (!$allow_non_auth_checkout) {
	if (!$user_is_authenticated) {
		header('Location: ' . $redirect_url);
		exit();
	}
}
 
$redirect_url = 'https://' . $foxycart_domain . '/checkout?fc_auth_token=';
$return_hash = sha1($customer_id . '|' . $timestamp . '|' . $foxycart_api_key);
$full_redirect = $redirect_url . $return_hash . '&fc_customer_id=' . $customer_id . '&timestamp=' . $timestamp . '&fcsid=' . $fcsid; 
header('Location: ' . $full_redirect);
?>