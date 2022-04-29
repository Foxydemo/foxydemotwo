<?php
/*
Plugin Name: Email Update
Description: Email Update for customer account in Solari
Version: 1.0.0
Author: Kode Tiger
*/

if(!defined('ABSPATH')){
    exit;
}
  
define("PLUGIN_DIR_PATH", plugin_dir_path(__FILE__));
define("PLUGIN_URL", plugins_url());

//enqueued javascript needed for the various menu functionality
function custom_plugin_assets() {

    // JS
    // wp_register_script('prefix_bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js');
    // wp_enqueue_script('prefix_bootstrap');

    // // CSS
    // wp_register_style('prefix_bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css');
    // wp_enqueue_style('prefix_bootstrap');

    // Jquery
    wp_enqueue_script('jquery');
    wp_enqueue_script('jqueryvalidate','https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js'); 

    wp_enqueue_script(
        "main_script",
        PLUGIN_URL."/emailupdate/admin/assets/js/main.js",
        '',
        '1.0',
        true
    );

    $object_array = array (
        "ajaxurl" => admin_url("admin-ajax.php")
    );

    wp_localize_script("main_script", "main_script", $object_array);

}

add_action("init", "custom_plugin_assets");

// change font function ajax callback
add_action("wp_ajax_simple_form", "simple_form");

function simple_form() {
    global $wpdb;

    if ($_REQUEST['emailinput']) {

        $exists = email_exists($_REQUEST['emailinput']);

        if ($exists) {
            echo "This E-mail Address is already registered";
        } else {
            $check1 = filter_var($_REQUEST['emailinput'], FILTER_VALIDATE_EMAIL);
            $check2 = filter_var($_REQUEST['emailconfirm'], FILTER_VALIDATE_EMAIL);

            if($check1 == false && $check2 == false) {
                echo "Invalid Email ID";

            } else {
                $user_ID = get_current_user_id(); 
    
                $args = array(
                    'ID'         => $user_ID,
                    'user_email' => esc_attr($_POST['emailinput']),
                );
                
                wp_update_user($args);

                $current_user = wp_get_current_user();
                update_user_meta( $user_ID, '_previous_user_id', $current_user->user_login);
    
                $wpdb->update(
                    $wpdb->users, 
                    ['user_login' => esc_attr($_POST['emailinput'])],
                    ['ID' => $user_ID]
                );

                do_action( 'delete_user', $user_ID );

                // $to = 'to@to.com';
                // $subject = 'The subject';
                // $body = 'The email body content';
                // $headers = array('Content-Type: text/html; charset=UTF-8');
                
                // wp_mail($to, $subject, $body, $headers);
                echo "Email Address updated successfully";
            }
        }
        
    } else {
        echo "Email Address field can't be empty!";
    }

    wp_die();
}

function form($atts) {
    ob_start();
    include_once PLUGIN_DIR_PATH."admin/assets/views/simple_form.php";
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
}

add_shortcode('email-update', 'form');