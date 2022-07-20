
<?php
/**
 * Template: Checkout
 *
 * See documentation for how to override the PMPro templates.
 * @link https://www.paidmembershipspro.com/documentation/templates/
 *
 * @version 2.0
 *
 * @author Paid Memberships Pro
 */

global $gateway, $pmpro_review, $skip_account_fields, $pmpro_paypal_token, $wpdb, $current_user, $pmpro_msg, $pmpro_msgt, $pmpro_requirebilling, $pmpro_level, $pmpro_levels, $tospage, $pmpro_show_discount_code, $pmpro_error_fields;
global $discount_code, $username, $password, $password2, $bfirstname, $blastname, $baddress1, $baddress2, $bcity, $bstate, $bzipcode, $bcountry, $bphone, $bemail, $bconfirmemail, $CardType, $AccountNumber, $ExpirationMonth,$ExpirationYear;

/**
 * Filter to set if PMPro uses email or text as the type for email field inputs.
 *
 * @since 1.8.4.5
 *
 * @param bool $use_email_type, true to use email type, false to use text type
 */
$pmpro_email_field_type = apply_filters('pmpro_email_field_type', true);

// Set the wrapping class for the checkout div based on the default gateway;
$default_gateway = pmpro_getOption( 'gateway' );
if ( empty( $default_gateway ) ) {
	$pmpro_checkout_gateway_class = 'pmpro_checkout_gateway-none';
} else {
	$pmpro_checkout_gateway_class = 'pmpro_checkout_gateway-' . $default_gateway;
}
?>

<?php do_action('pmpro_checkout_before_form'); ?>



<div id="pmpro_level-<?php echo $pmpro_level->id; ?>" class="<?php echo pmpro_get_element_class( $pmpro_checkout_gateway_class, 'pmpro_level-' . $pmpro_level->id ); ?>">
<form id="pmpro_form" class="<?php echo pmpro_get_element_class( 'pmpro_form' ); ?>" action="<?php if(!empty($_REQUEST['review'])) echo pmpro_url("checkout", "?level=" . $pmpro_level->id); ?>" method="post">

	<input type="hidden" id="level" name="level" value="<?php echo esc_attr($pmpro_level->id) ?>" />
	<input type="hidden" id="checkjavascript" name="checkjavascript" value="1" />
	<?php if ($discount_code && $pmpro_review) { ?>
		<input class="<?php echo pmpro_get_element_class( 'input pmpro_alter_price', 'discount_code' ); ?>" id="discount_code" name="discount_code" type="hidden" size="20" value="<?php echo esc_attr($discount_code) ?>" />
	<?php } ?>

	

	<?php
		$include_pricing_fields = apply_filters( 'pmpro_include_pricing_fields', true );
		if ( $include_pricing_fields ) {
		?>
		<div id="pmpro_pricing_fields" class="<?php echo pmpro_get_element_class( 'pmpro_checkout', 'pmpro_pricing_fields' ); ?>">
			<h3>
				<span class="<?php echo pmpro_get_element_class( 'pmpro_checkout-h3-name' ); ?>"><?php esc_html_e('Membership Level', 'paid-memberships-pro' );?></span>
				<?php if(count($pmpro_levels) > 1) { ?><span class="<?php echo pmpro_get_element_class( 'pmpro_checkout-h3-msg' ); ?>"><a href="<?php echo esc_url( pmpro_url( "levels" ) ); ?>"><?php esc_html_e('change', 'paid-memberships-pro' );?></a></span><?php } ?>
			</h3>
			<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-fields' ); ?>">
				<p>
					<?php printf(__('You have selected the <strong>%s</strong> membership level.', 'paid-memberships-pro' ), $pmpro_level->name);?>
				</p>

				<?php
					/**
					 * All devs to filter the level description at checkout.
					 * We also have a function in includes/filters.php that applies the the_content filters to this description.
					 * @param string $description The level description.
					 * @param object $pmpro_level The PMPro Level object.
					 */
					$level_description = apply_filters('pmpro_level_description', $pmpro_level->description, $pmpro_level);
					if(!empty($level_description))
						echo $level_description;
				?>

				<div id="pmpro_level_cost">
					<?php if($discount_code && pmpro_checkDiscountCode($discount_code)) { ?>
						<?php printf(__('<p class="' . pmpro_get_element_class( 'pmpro_level_discount_applied' ) . '">The <strong>%s</strong> code has been applied to your order.</p>', 'paid-memberships-pro' ), $discount_code);?>
					<?php } ?>
					<?php echo wpautop(pmpro_getLevelCost($pmpro_level)); ?>
					<?php echo wpautop(pmpro_getLevelExpiration($pmpro_level)); ?>
				</div>

				<?php do_action("pmpro_checkout_after_level_cost"); ?>

				<?php if($pmpro_show_discount_code) { ?>
					<?php if($discount_code && !$pmpro_review) { ?>
						<p id="other_discount_code_p" class="<?php echo pmpro_get_element_class( 'pmpro_small', 'other_discount_code_p' ); ?>"><a id="other_discount_code_a" href="#discount_code"><?php esc_html_e('Click here to change your discount code.', 'paid-memberships-pro' );?></a></p>
					<?php } elseif(!$pmpro_review) { ?>
						<p id="other_discount_code_p" class="<?php echo pmpro_get_element_class( 'pmpro_small', 'other_discount_code_p' ); ?>"><?php esc_html_e('Do you have a discount code?', 'paid-memberships-pro' );?> <a id="other_discount_code_a" href="#discount_code"><?php esc_html_e('Click here to enter your discount code', 'paid-memberships-pro' );?></a>.</p>
					<?php } elseif($pmpro_review && $discount_code) { ?>
						<p><strong><?php esc_html_e('Discount Code', 'paid-memberships-pro' );?>:</strong> <?php echo $discount_code?></p>
					<?php } ?>
				<?php } ?>

				<?php if($pmpro_show_discount_code) { ?>
				<div id="other_discount_code_tr" style="display: none;">
					<label for="other_discount_code"><?php esc_html_e('Discount Code', 'paid-memberships-pro' );?></label>
					<input id="other_discount_code" name="other_discount_code" type="text" class="<?php echo pmpro_get_element_class( 'input pmpro_alter_price', 'other_discount_code' ); ?>" size="20" value="<?php echo esc_attr($discount_code); ?>" />
					<input type="button" name="other_discount_code_button" id="other_discount_code_button" value="<?php esc_attr_e('Apply', 'paid-memberships-pro' );?>" />
				</div>
				<?php } ?>
			</div> <!-- end pmpro_checkout-fields -->
		</div> <!-- end pmpro_pricing_fields -->
		<?php
		} // if ( $include_pricing_fields )
	?>

	<?php
		do_action('pmpro_checkout_after_pricing_fields');
	?>

	<?php if(!$skip_account_fields && !$pmpro_review) { ?>

	<?php 
		// Get discount code from URL parameter, so if the user logs in it will keep it applied.
		$discount_code_link = !empty( $discount_code) ? '&discount_code=' . $discount_code : ''; 
	?>
	<div id="pmpro_user_fields" class="<?php echo pmpro_get_element_class( 'pmpro_checkout', 'pmpro_user_fields' ); ?>">
		<!-- <hr /> -->
		<!-- <h3>
			<span class="<?php //echo pmpro_get_element_class( 'pmpro_checkout-h3-name' ); ?>"><?php //esc_html_e('Account Information', 'paid-memberships-pro' );?></span>
			<span class="<?php //echo pmpro_get_element_class( 'pmpro_checkout-h3-msg' ); ?>"><?php //esc_html_e('Already have an account?', 'paid-memberships-pro' );?> <a href="<?php //echo wp_login_url( apply_filters( 'pmpro_checkout_login_redirect', pmpro_url("checkout", "?level=" . $pmpro_level->id . $discount_code_link) ) ); ?>"><?php //esc_html_e('Log in here', 'paid-memberships-pro' );?></a></span>
		</h3> -->
		<!-- <div class="<?php //echo pmpro_get_element_class( 'pmpro_checkout-fields' ); ?>"> -->
			<!-- <div class="<?php //echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-username', 'pmpro_checkout-field-username' ); ?>">
				<label for="username"><?php //esc_html_e('Username', 'paid-memberships-pro' );?></label>
				<input id="username" name="username" type="text" class="<?php //echo pmpro_get_element_class( 'input', 'username' ); ?>" size="30" value="<?php //echo esc_attr($username); ?>" />
			</div> end pmpro_checkout-field-username -->

			<?php
				//do_action('pmpro_checkout_after_username');
			?>

			<!-- <div class="<?php //echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-password', 'pmpro_checkout-field-password' ); ?>">
				<label for="password"><?php //esc_html_e('Password', 'paid-memberships-pro' );?></label>
				<input id="password" name="password" type="password" class="<?php //echo pmpro_get_element_class( 'input', 'password' ); ?>" size="30" value="<?php //echo esc_attr($password); ?>" /> -->
			<!-- </div> end pmpro_checkout-field-password -->

			<?php
				// $pmpro_checkout_confirm_password = apply_filters("pmpro_checkout_confirm_password", true);
				// if($pmpro_checkout_confirm_password) { ?>
					<!-- <div class="<?php //echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-password2', 'pmpro_checkout-field-password2' ); ?>">
						<label for="password2"><?php //esc_html_e('Confirm Password', 'paid-memberships-pro' );?></label>
						<input id="password2" name="password2" type="password" class="<?php //echo pmpro_get_element_class( 'input', 'password2' ); ?>" size="30" value="<?php// echo esc_attr($password2); ?>" />
					</div> end pmpro_checkout-field-password2 -->
				<?php //} else { ?>
					<!-- <input type="hidden" name="password2_copy" value="1" /> -->
				<?php //}
			?>

			<?php
				//do_action('pmpro_checkout_after_password');
			?>

			<!-- <div class="<?php //echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-bemail', 'pmpro_checkout-field-bemail' ); ?>">
				<label for="bemail"><?php //esc_html_e('Email Address', 'paid-memberships-pro' );?></label>
				<input id="bemail" name="bemail" type="<?php //echo ($pmpro_email_field_type ? 'email' : 'text'); ?>" class="<?php //echo pmpro_get_element_class( 'input', 'bemail' ); ?>" size="30" value="<?php //echo esc_attr($bemail); ?>" />
			</div> end pmpro_checkout-field-bemail -->

			<?php
				// $pmpro_checkout_confirm_email = apply_filters("pmpro_checkout_confirm_email", true);
				// if($pmpro_checkout_confirm_email) { ?>
					<!-- <div class="<?php //echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-bconfirmemail', 'pmpro_checkout-field-bconfirmemail' ); ?>">
						<label for="bconfirmemail"><?php //esc_html_e('Confirm Email Address', 'paid-memberships-pro' );?></label>
						<input id="bconfirmemail" name="bconfirmemail" type="<?php //echo ($pmpro_email_field_type ? 'email' : 'text'); ?>" class="<?php //echo pmpro_get_element_class( 'input', 'bconfirmemail' ); ?>" size="30" value="<?php //echo esc_attr($bconfirmemail); ?>" />
					</div> end pmpro_checkout-field-bconfirmemail -->
				<?php //} else { ?>
					<!-- <input type="hidden" name="bconfirmemail_copy" value="1" /> -->
				<?php// }
			?>

			<?php
				//do_action('pmpro_checkout_after_email');
			?>

			<div class="<?php echo pmpro_get_element_class( 'pmpro_hidden' ); ?>">
				<label for="fullname"><?php esc_html_e('Full Name', 'paid-memberships-pro' );?></label>
				<input id="fullname" name="fullname" type="text" class="<?php echo pmpro_get_element_class( 'input', 'fullname' ); ?>" size="30" value="" autocomplete="off"/> <strong><?php esc_html_e('LEAVE THIS BLANK', 'paid-memberships-pro' );?></strong>
			</div> <!-- end pmpro_hidden -->

		</div>  <!-- end pmpro_checkout-fields -->
	</div> <!-- end pmpro_user_fields -->
	<?php } elseif($current_user->ID && !$pmpro_review) { ?>
		<div id="pmpro_account_loggedin" class="<?php echo pmpro_get_element_class( 'pmpro_message pmpro_alert', 'pmpro_account_loggedin' ); ?>">
			<?php printf(__('You are logged in as <strong>%s</strong>. If you would like to use a different account for this membership, <a href="%s">log out now</a>.', 'paid-memberships-pro' ), $current_user->user_login, wp_logout_url($_SERVER['REQUEST_URI'])); ?>
		</div> <!-- end pmpro_account_loggedin -->
		
	<?php } ?>
<?php 
global $wpdb;
		
$level=''; 
if(isset($_GET['level'])){
	$level = $_GET['level']; 
} 
$level_details = $wpdb->get_results("SELECT * FROM wp_fdemo_pmpro_membership_levels WHERE id = '$level'");
if(!empty($level_details)){
$levels = $level_details[0];
$id= $level_details[0]->id;
$name= $level_details[0]->name;
$description= $level_details[0]->description;
$confirmation= $level_details[0]->confirmation;
$initial_payment= $level_details[0]->initial_payment;
$billing_amount= $level_details[0]->billing_amount;
$cycle_number= $level_details[0]->cycle_number;
$cycle_period= $level_details[0]->cycle_period;
$billing_limit= $level_details[0]->billing_limit;
$trial_amount= $level_details[0]->trial_amount;
$trial_limit= $level_details[0]->trial_limit;
$allow_signups= $level_details[0]->allow_signups;
$expiration_number= $level_details[0]->expiration_number;
$expiration_period= $level_details[0]->expiration_period;
$code= $level_details[0]->code;
$frequency= $level_details[0]->frequency;
$start_date= $level_details[0]->start_date;
$end_date= $level_details[0]->end_date;
}

?>

<head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <style>

  </style>
</head>
<div class="container">
  
  <!-- Trigger the modal with a button -->
  <form action=" " method='POST'>
	<button id="Inbutton">+</button>

  <input type='text'  value='1' name='PQuntity' id='PQuantity' style=" text-align: center;width:100px">
  <button id="DeButton">-</button>
  <input type="hidden"id="PAmount" name="price" value="<?php echo $billing_amount; ?>"/>
      
      
        <input type="hidden" name="image" value="https://foxyshopdemotwo.kodetiger.in/wp-content/uploads/2022/03/51PpSVije-L-150x150.jpg" id="foxyshop_cart_product_image_<?php echo $code; ?>">
        <input type="hidden" name="url" value="https://foxyshopdemotwo.kodetiger.in/products/laddu/" id="fs_url_<?php echo $code; ?>">
 
       
       
        <input type="hidden" name="sub_frequency" id='PFrequency'  value="<?php echo $frequency; ?>" />
        <input type="hidden"  id="PName" name="name" id="fs_name" value="<?php echo $name; ?>" />
        <input type="hidden" name="code" id="PCode" value="<?php echo $code; ?>"/>
        <input type="hidden" name="weight" id="fs_weight_<?php echo $code; ?>" value="1.0">
       <input type="hidden" name="sub_startdate"  id='PStartDate' value="<?php echo $start_date; ?>" />
        <input type="hidden" name="sub_enddate" id='PEndDate' value="<?php echo $end_date; ?>"/>
 

  <button type="button" class="btn btn-info btn-lg"  id="AddCart" data-toggle="modal" data-target="#myModal">Add to Cart</button>
  </form>
  <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h2 class="modal-title text-center">Your Cart</h2>
		 

        </div>
        <div class="modal-body">

    
<div class='row' id='ShowCart'>


</div>

			<h3 class="text-center" >Order Summary</h3>
		
			<table>
  <tr>
  
    <th id='ordertotal'></th>
    <th id=amounttotal></th>
  </tr>
  
</table>


</div>





	
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Proceed to checkout</button>  <!-- add link to this button for redirecting -->
        </div>
      </div>
    </div>
  </div>
</div>
<script>
$('#AddCart').click((e)=>{
	e.preventDefault();
	var PQuantity=$('#PQuantity').val();
	var PName=$('#PName').val();
	var PAmount=$('#PAmount').val();
	var PCode=$('#PCode').val();

	var PFrequency=$('#PFrequency').val();
	var PStartDate=$('#PStartDate').val();
	var PEndDate=$('#PEndDate').val();
	

	$.ajax({
		url: "<?php echo plugin_dir_url( __FILE__ ) ?>insertcarts.php",
        type: "POST",
        data: {PQuantity:PQuantity,PName:PName,PAmount:PAmount,PCode:PCode,PFrequency:PFrequency,PStartDate:PStartDate,PEndDate},
        success: function (response) {
			$('#ShowCart').html(response)
			
			$('#ordertotal').text( 'Total Order : ' + $('#quantity').val());
			$('#amounttotal').text( '$  ' + $('#totalPrice').val())
		
			



	




        }
   
    });
})
function delFunction(e) {
	let  PCode=$('.pdel').val();
	$.ajax({
		url: "<?php echo plugin_dir_url( __FILE__ ) ?>insertcarts.php",
        type: "POST",
        data: {PCode:PCode,PDel:'PDel'},
        success: function (response) {
			console.log(response)
		
			



	




        }
   
    });
}
$('#Inbutton').click((e)=>{
	e.preventDefault();
	
	let incre=$('#PQuantity').val();
	incre++;
	$('#PQuantity').val(incre);
})
$('#DeButton').click((e)=>{
	e.preventDefault();
	let dece=$('#PQuantity').val();
	if(dece > 1){
	
	dece--;
	$('#PQuantity').val(dece);
	}
})
</script>


	<?php
		do_action('pmpro_checkout_after_user_fields');
	?>

	<?php
		do_action('pmpro_checkout_boxes');
	?>

	<?php if(pmpro_getGateway() == "paypal" && empty($pmpro_review) && true == apply_filters('pmpro_include_payment_option_for_paypal', true ) ) { ?>
	<div id="pmpro_payment_method" class="<?php echo pmpro_get_element_class( 'pmpro_checkout', 'pmpro_payment_method' ); ?>" <?php if(!$pmpro_requirebilling) { ?>style="display: none;"<?php } ?>>
		<hr />
		<h3>
			<span class="<?php echo pmpro_get_element_class( 'pmpro_checkout-h3-name' ); ?>"><?php esc_html_e('Choose your Payment Method', 'paid-memberships-pro' ); ?></span>
		</h3>
		<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-fields' ); ?>">
			<span class="<?php echo pmpro_get_element_class( 'gateway_paypal' ); ?>">
				<input type="radio" name="gateway" value="paypal" <?php if(!$gateway || $gateway == "paypal") { ?>checked="checked"<?php } ?> />
				<a href="javascript:void(0);" class="<?php echo pmpro_get_element_class( 'pmpro_radio' ); ?>"><?php esc_html_e('Check Out with a Credit Card Here', 'paid-memberships-pro' );?></a>
			</span>
			<span class="<?php echo pmpro_get_element_class( 'gateway_paypalexpress' ); ?>">
				<input type="radio" name="gateway" value="paypalexpress" <?php if($gateway == "paypalexpress") { ?>checked="checked"<?php } ?> />
				<a href="javascript:void(0);" class="<?php echo pmpro_get_element_class( 'pmpro_radio' ); ?>"><?php esc_html_e('Check Out with PayPal', 'paid-memberships-pro' );?></a>
			</span>
		</div> <!-- end pmpro_checkout-fields -->
	</div> <!-- end pmpro_payment_method -->
	<?php } ?>

	
	<?php do_action("pmpro_checkout_after_tos_fields"); ?>

	<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_captcha', 'pmpro_captcha' ); ?>">
	<?php
		global $recaptcha, $recaptcha_publickey;
		if ( $recaptcha == 2 || $recaptcha == 1 ) {
			echo pmpro_recaptcha_get_html($recaptcha_publickey, NULL, true);
		}
	?>
	</div> <!-- end pmpro_captcha -->

	<?php
		do_action('pmpro_checkout_after_captcha');
	?>

	<?php do_action("pmpro_checkout_before_submit_button"); ?>

	<div class="<?php echo pmpro_get_element_class( 'pmpro_submit' ); ?>">
		<hr />
		

		<?php if($pmpro_review) { ?>

			<!-- <span id="pmpro_submit_span">
				<input type="hidden" name="confirm" value="1" />
				<input type="hidden" name="token" value="<?php echo esc_attr($pmpro_paypal_token); ?>" />
				<input type="hidden" name="gateway" value="<?php echo esc_attr($gateway); ?>" />
				<input type="submit" id="pmpro_btn-submit" class="<?php echo pmpro_get_element_class( 'pmpro_btn pmpro_btn-submit-checkout', 'pmpro_btn-submit-checkout' ); ?>" value="<?php esc_attr_e('Complete Payment', 'paid-memberships-pro' );?> &raquo;" />
			</span> -->

		<?php } else { ?>

			<?php
				$pmpro_checkout_default_submit_button = apply_filters('pmpro_checkout_default_submit_button', true);
				if($pmpro_checkout_default_submit_button)
				{
				?>
				<!-- <span id="pmpro_submit_span">
					<input type="hidden" name="submit-checkout" value="1" />
					<input type="submit"  id="pmpro_btn-submit" class="<?php echo pmpro_get_element_class(  'pmpro_btn pmpro_btn-submit-checkout', 'pmpro_btn-submit-checkout' ); ?>" value="<?php if($pmpro_requirebilling) { _e('Submit and Check Out', 'paid-memberships-pro' ); } else { _e('Submit and Confirm', 'paid-memberships-pro' );}?> &raquo;" />
				</span> -->
				<?php
				}
			?>

		<?php } ?>
		<span id="pmpro_processing_message" style="visibility: hidden;">
			<?php
				$processing_message = apply_filters("pmpro_processing_message", __("Processing...", 'paid-memberships-pro' ));
				echo $processing_message;
			?>
		</span>
	</div>
</form>
<!-- Custom Code starts from here -->
		<?php
// 		$folder="/foxyshopdemotwo.kodetiger.in";
// 		require_once($_SERVER['DOCUMENT_ROOT'] . $folder . '/wp-config.php');
// 		require_once($_SERVER['DOCUMENT_ROOT'] . $folder . '/wp-load.php');
        //define( 'ROOT_DIR', dirname(__FILE__) );
// 		require_once( ROOT_DIR.'../../../../wp-config.php' );
        require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-config.php');
		global $wpdb;
		
		$level=''; 
		if(isset($_GET['level'])){
			$level = $_GET['level']; 
		} 
		$level_details = $wpdb->get_results("SELECT * FROM wp_fdemo_pmpro_membership_levels WHERE id = '$level'");
		if(!empty($level_details)){
		$levels = $level_details[0];
		$id= $level_details[0]->id;
		$name= $level_details[0]->name;
		$description= $level_details[0]->description;
		$confirmation= $level_details[0]->confirmation;
		$initial_payment= $level_details[0]->initial_payment;
		$billing_amount= $level_details[0]->billing_amount;
		$cycle_number= $level_details[0]->cycle_number;
		$cycle_period= $level_details[0]->cycle_period;
		$billing_limit= $level_details[0]->billing_limit;
		$trial_amount= $level_details[0]->trial_amount;
		$trial_limit= $level_details[0]->trial_limit;
		$allow_signups= $level_details[0]->allow_signups;
		$expiration_number= $level_details[0]->expiration_number;
		$expiration_period= $level_details[0]->expiration_period;
		$code= $level_details[0]->code;
		$frequency= $level_details[0]->frequency;
		$start_date= $level_details[0]->start_date;
		$end_date= $level_details[0]->end_date;
		}
	
		?>
 
 

		<form action="" method="post" accept-charset="utf-8" class="foxyshop_product" id="foxyshop_product_form_<?php echo $code; ?>" rel="<?php echo $code; ?>">
        <input type="hidden" name="fcsid" value="brenm3nsefjk4hfd75ujcplab1">
        <input type="hidden" name="price" id="fs_price_<?php echo $code; ?>" value="<?php echo $billing_amount; ?>"/>
        <input type="hidden" name="x:originalprice" id="originalprice_<?php echo $code; ?>" value="<?php echo $billing_amount; ?>" />
        <input type="hidden" name="x:l18n" value="$|.|,|1|0" id="foxyshop_l18n_<?php echo $code; ?>"> 
        <input type="hidden" name="image" value="https://foxyshopdemotwo.kodetiger.in/wp-content/uploads/2022/03/51PpSVije-L-150x150.jpg" id="foxyshop_cart_product_image_<?php echo $code; ?>">
        <input type="hidden" name="url" value="https://foxyshopdemotwo.kodetiger.in/products/laddu/" id="fs_url_<?php echo $code; ?>">
        <input type="hidden" name="quantity_min" value="0" id="fs_quantity_min_<?php echo $code; ?>">
        <input type="hidden" name="quantity_max" value="0" id="fs_quantity_max_<?php echo $code; ?>">
        <input type="hidden" name="x:quantity_max" value="0" id="original_quantity_max_<?php echo $code; ?>">
        <input type="hidden" name="sub_frequency" id="fs_sub_frequency_<?php echo $code; ?>" value="<?php echo $frequency; ?>" />
        <input type="hidden" name="name" id="fs_name_<?php echo $code; ?>" value="<?php echo $name; ?>" />
        <input type="hidden" name="code" id="fs_code_<?php echo $code; ?>" value="<?php echo $code; ?>"/>
        <input type="hidden" name="weight" id="fs_weight_<?php echo $code; ?>" value="1.0">
       <input type="hidden" name="sub_startdate" id="fs_sub_startdate_<?php echo $code; ?>" value="<?php echo $start_date; ?>" />
        <input type="hidden" name="sub_enddate" id="fs_sub_enddate_<?php echo $code; ?>" value="<?php echo $end_date; ?>" />
     
    <input type="submit" value="Add to cart!" name='cart-submit' />
</form>

	<!-- Custom Code ends here -->
<?php do_action('pmpro_checkout_after_form'); ?>


</div> <!-- end pmpro_level-ID -->
