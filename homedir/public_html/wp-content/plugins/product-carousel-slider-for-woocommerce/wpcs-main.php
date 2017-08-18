<?php
/*
Plugin Name: WooCommerce Product Carousel Slider
Plugin URI:  http://adlplugins.com/plugin/woocommerce-product-carousel-slider
Description: This plugin allows you to easily create WooCommerce product carousel slider. It is fully responsive and mobile friendly carousel slider which comes with lots of features.
Version:     2.1
Author:      ADL Plugins
Author URI:  http://adlplugins.com
License:     GPL2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages/
Text Domain: woocommerce-product-carousel-slider
*/

/**
 * Protect direct access
 */
if( ! defined( 'WPCS_HACK_MSG' ) ) define( 'WPCS_HACK_MSG', __( 'Sorry! This is not your place!', 'woocommerce-product-carousel-slider' ) );
if ( ! defined( 'ABSPATH' ) ) die( WPCS_HACK_MSG );

/**
 * Defining constants
 */
if( ! defined( 'WPCS_PLUGIN_DIR' ) ) define( 'WPCS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
if( ! defined( 'WPCS_PLUGIN_URI' ) ) define( 'WPCS_PLUGIN_URI', plugins_url( '', __FILE__ ) );

require_once WPCS_PLUGIN_DIR . 'wpcs-metabox.php';
require_once WPCS_PLUGIN_DIR . 'wpcs-img-resizer.php';
require_once WPCS_PLUGIN_DIR . 'wpcs-shortcodes.php';

/**
 * Registers scripts and stylesheets
 */
function wpcs_frontend_scripts_and_styles() {
	wp_register_style( 'wpcs-owl-carousel-style', WPCS_PLUGIN_URI . '/css/owl.carousel.css' );
	wp_register_style( 'wpcs-owl-theme-style', WPCS_PLUGIN_URI . '/css/owl.theme.css' );
	wp_register_style( 'wpcs-owl-transitions', WPCS_PLUGIN_URI . '/css/owl.transitions.css' );
	wp_register_style( 'wpcs-font-awesome', WPCS_PLUGIN_URI . '/css/font-awesome.min.css' );
	wp_register_style( 'wpcs-custom-style', WPCS_PLUGIN_URI . '/css/wpcs-styles.css' );
	wp_register_script( 'wpcs-owl-carousel-js', WPCS_PLUGIN_URI . '/js/owl.carousel.min.js', array('jquery'),'1.3.3', true );
	wp_register_script( 'wpcs-custom-js', WPCS_PLUGIN_URI . '/js/custom.js', array('jquery'),'1.3.3', true );
}
add_action( 'wp_enqueue_scripts', 'wpcs_frontend_scripts_and_styles' );

function wpcs_admin_scripts_and_styles() {
	global $typenow;	
	if ( ($typenow == 'woocarousel') ) {
		wp_enqueue_style( 'wpcs_custom_wp_admin_css', WPCS_PLUGIN_URI . '/css/wpcs-admin-styles.css' );
		wp_enqueue_style( 'wpcs_meta_fields_css', WPCS_PLUGIN_URI . '/css/cmb2.min.css' );
		wp_enqueue_script( 'wpcs_custom_wp_admin_js', WPCS_PLUGIN_URI . '/js/wpcs-admin-script.js', array('jquery'), '1.3.3', true  );
		wp_enqueue_style( 'wp-color-picker' );
	    wp_enqueue_script( 'wpcs-wp-color-picker', WPCS_PLUGIN_URI . '/js/wpcs-color-picker.js', array( 'wp-color-picker' ), false, true );  
	}	
}
add_action( 'admin_enqueue_scripts', 'wpcs_admin_scripts_and_styles' );

/**
 * Enables shortcode for Widget
 */
add_filter('widget_text', 'do_shortcode');

/**
 * Check if WooCommerce is not active
 */
if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
   add_action( 'admin_notices', 'wpcs_admin_notice' );
}

function wpcs_admin_notice() { ?> 
	<div class="error"><p><?php _e('WooCommerce plugin is not activated. Please install and activate it to use <strong>WooCommerce Product Carousel Slider</strong> plugin.', 'woocommerce-product-carousel-slider'); ?></p></div>
<?php }

/**
 * Load plugin textdomain
 */
function wpcs_load_textdomain() {
	load_plugin_textdomain( 'woocommerce-product-carousel-slider', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
}
add_action('plugins_loaded', 'wpcs_load_textdomain');

/**
 * Pro Version link
 */
function wpcs_pro_version_link( $links ) {
   $links[] = '<a href="http://adlplugins.com/plugin/woocommerce-product-carousel-slider-pro" target="_blank">Pro Version</a>';
   return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'wpcs_pro_version_link' );

/**
 * Upgrade & Support submenu pages
 */
function upgrade_support_submenu_pages() {
	add_submenu_page( 'edit.php?post_type=woocarousel', __('Upgrade', 'woocommerce-product-carousel-slider'), __('Upgrade', 'woocommerce-product-carousel-slider'), 'manage_options', 'upgrade', 'wpcs_upgrade_callback' );
	add_submenu_page( 'edit.php?post_type=woocarousel', __('Support', 'woocommerce-product-carousel-slider'), __('Support', 'woocommerce-product-carousel-slider'), 'manage_options', 'support', 'wpcs_support_callback' );
}
add_action('admin_menu', 'upgrade_support_submenu_pages');

function wpcs_upgrade_callback() {
	include('wpcs-upgrade.php');
}

function wpcs_support_callback() { 
	include('wpcs-support.php');
}


