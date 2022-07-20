<?php 
/** 
 * @package BlockUser
 */
/*
Plugin Name: Block User
Plugin URI: http://BlockUser.com
Description: This is the custom plugin for BlockUser in  PMP
Version: 1.0.0
Author: Kode Tiger
Author URI: http://www.kodetiger.io
License: GPLv2 or later
Text Domain: BlockUser
*/ 
// Exit if accessed directly
include 'functions.php';


if(!defined('ABSPATH')){
    exit;
  }
  
  define("PLUGIN_DIR_PATH", plugin_dir_path(__FILE__));
  define("PLUGIN_URL", plugins_url());
