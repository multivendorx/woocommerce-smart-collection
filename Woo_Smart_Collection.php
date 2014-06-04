<?php
/*
Plugin Name: WooCommerce Smart Collection
Plugin URI: http://dualcube.com
Description: A cool new Wordpress Woocommerce plugin that helps you to make smart collection of products.
Author: Dualcube
Version: 1.0.5
Author URI: http://dualcube.com
*/

if ( ! class_exists( 'WC_Samrt_Collection_Dependencies' ) )
	require_once 'includes/class-dc-dependencies.php';
require_once 'includes/dc-Woo-Smart-Collection-core-functions.php';
require_once 'config.php';
if(!defined('ABSPATH')) exit; // Exit if accessed directly
if(!defined('DC_WOO_SMART_COLLECTION_PLUGIN_TOKEN')) exit;
if(!defined('DC_WOO_SMART_COLLECTION_TEXT_DOMAIN')) exit;

if(!WC_Samrt_Collection_Dependencies::woocommerce_active_check()) {
  add_action( 'admin_notices', 'woocommerce_inactive_notice' );
}

if(!class_exists('DC_Woo_Smart_Collection')) {
	require_once( 'classes/class-dc-Woo-Smart-Collection.php' );
	global $DC_Woo_Smart_Collection;
	$DC_Woo_Smart_Collection = new DC_Woo_Smart_Collection( __FILE__ );
	$GLOBALS['DC_Woo_Smart_Collection'] = $DC_Woo_Smart_Collection;
	
	// Activation Hooks
	register_activation_hook( __FILE__, array('DC_Woo_Smart_Collection', 'activate_dc_Woo_Smart_Collection') );
	register_activation_hook( __FILE__, 'flush_rewrite_rules' );
	
	// Deactivation Hooks
	register_deactivation_hook( __FILE__, array('DC_Woo_Smart_Collection', 'deactivate_dc_Woo_Smart_Collection') );
}
?>
