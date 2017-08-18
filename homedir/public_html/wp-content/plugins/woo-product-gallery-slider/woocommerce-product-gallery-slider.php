<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://iamniloy.com
 * @since             1.0.0
 * @package           Woo_Product_Gallery_Slide
 *
 * @wordpress-plugin
 * Plugin Name:       Twist - Woocommerce Product Gallery Slider Free
 * Plugin URI:        https://wordpress.org/plugins/woo-product-gallery-slider/
 * Description:       Too Many Product Images in your Product Gallery ? This plugin will add a carousel in your Product Gallery. <a href="http://codecanyon.net/item/twist-product-gallery-slidercarousel-plugin-for-woocommerce/14849108?ref=NiloySarker">Get the Pro Version Now </a>
 * Version:           1.1.8.2
 * Author:            Niloy Sarker
 * Author URI:        http://iamniloy.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wpgs
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'add_action_links' );

function add_action_links ( $links ) {
 $mylinks = array(
 '<a style="font-weight:bold;color:green" target="_blank" href="http://codecanyon.net/item/twist-product-gallery-slidercarousel-plugin-for-woocommerce/14849108?ref=NiloySarker">Get the Pro Version!</a>',
 );
return array_merge( $links, $mylinks );
}


 add_action('plugins_loaded','after_woo_hooks');

function after_woo_hooks() {
	
remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );

add_action( 'woocommerce_product_thumbnails', 'twist_show_product_thumbnails', 20 );
add_action( 'woocommerce_before_single_product_summary', 'twist_show_product_image', 10 ); 


add_image_size( 'thumbnails_twist', 120, 120, true);
}



// Remove srcset from Image
function meks_disable_srcset( $sources ) {
    return false;
}
 
add_filter( 'wp_calculate_image_srcset', 'meks_disable_srcset' );



// Product Thumbnails 
function twist_show_product_thumbnails() {

		// Woocmmerce 3.0+ Gallery Fix 

		
			require_once 'includes/product-thumbnails.php';
		
		
	}

// Single Product Image
function twist_show_product_image() {

	// Woocmmerce 3.0+ Gallery Fix 

		require_once 'includes/product-image.php';
	

}

/**
	 * Register the JS & CSS for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	function wpgs_enqueue_files() {
		wp_enqueue_style( 'owl-carousel', plugin_dir_url( __FILE__ ) . 'public/css/owl.carousel.css', array(),  'all' );
		wp_enqueue_style( 'wpgs-fa', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css', false ); 
		
		wp_enqueue_style( 'wpgs-css', plugin_dir_url( __FILE__ ) . 'public/css/woocommerce-product-gallery-slider-public.css', array(),  'all' );
		
		wp_enqueue_script( 'owl-carousel', plugin_dir_url( __FILE__ ) . 'public/js/owl.carousel.min.js', array( 'jquery' ),  true );
		wp_enqueue_script( 'wpgs-js', plugin_dir_url( __FILE__ ) . 'public/js/woocommerce-product-gallery-slider-public.js', array( 'jquery' ),  true );


				// woocommerce version 3.0 Fix
		if(wpgs_option("twist_wc3") =='true'){
		wp_enqueue_style( 'prettyphoto-css', plugin_dir_url( __FILE__ ) . 'assets/css/prettyPhoto.css', array(),  'all' );	
		wp_enqueue_script( 'prettyphoto-twist', plugin_dir_url( __FILE__ ) . 'assets/js/jquery.prettyPhoto.js', array( 'jquery' ),'1', false );
		
		}
		
	}
	
add_action( 'wp_enqueue_scripts','wpgs_enqueue_files' );


	

function wpgs_option( $name )
{
    return  vp_option( "wpgs_option." . $name );
}
/**
 * Include Vafpress Framework
 */
require_once  'functions.php';

function wpgs_option_edits() {
  ?>
  <style type="text/css">
  .woocommerce .thumbnails .owl-nav .owl-prev ,.woocommerce .thumbnails .owl-nav .owl-next {

    background: <?php echo wpgs_option("wpgs_nav_cl"); ?>!important;
    color: <?php echo wpgs_option("wpgs_nav_ico_cl"); ?>!important; 
padding: 2px 10px;
    top: 40%;
    position: absolute;
    margin: 0px !important;
    font-size: 20px;	
}
.woocommerce .thumbnails .owl-dot.active{
	background:<?php echo wpgs_option("wpgs_nav_cl"); ?> !important;
}

  </style>
  <script type="text/javascript">
  (function( $ ) {
	'use strict';
	 $( document ).ready(function() {

	 
	   
	 
		 	 $( '.woocommerce .thumbnails' ).owlCarousel({
				loop:<?php echo wpgs_option("wpgs_nav_infinite"); ?>,
				slideBy:2,
				dots: false,
				nav: <?php echo wpgs_option("wpgs_nav"); ?>,
				items:<?php echo wpgs_option("wpgs_thumbanils"); ?>,
				margin:<?php echo wpgs_option("wpgs_thum_margin"); ?>,
				stagePadding: <?php echo wpgs_option("wpgs_stagepadding"); ?>,
				mouseDrag: false,
				navText:["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"],				
				autoplayTimeout: 5000,
				autoplayHoverPause:false,
				autoplay: <?php echo wpgs_option("wpgs_autoplay"); ?>, // type false | if you don't want auto play
			});

	 		jQuery("a[rel^='prettyPhoto']").prettyPhoto({
		    	social_tools: false,
		    	theme: 'pp_default', /* light_rounded / dark_rounded / light_square / dark_square / facebook */
		    });

	});
})( jQuery );

  </script>
  <?php
}
add_action( 'wp_head', 'wpgs_option_edits' );




