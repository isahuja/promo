<?php
/**
 * WC Catalog Enquiry Admin
 *
 * @author 	WC Marketplace
 * @version   3.0.2
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $WC_Woocommerce_Catalog_Enquiry;

echo $email_heading . "\n\n";

echo sprintf( __( "Dear Admin", 'woocommerce-catalog-enquiry' ) ) . "\n\n";
echo sprintf( __( "Please find the product enquiry, details are given below", 'woocommerce-catalog-enquiry' ) ) . "\n\n";

echo "\n****************************************************\n\n";

$product_obj = wc_get_product( $product_id );

echo "\n Product Name : ".$product_obj->get_name();

echo "\n\n Product link : ".$product_obj->get_permalink();
if(!empty($product_obj->get_sku()))
	echo "\n\n Product SKU : ".$product_obj->get_sku();

echo "\n\n\n****************************************************\n\n";

echo "\n Customer Details : ";

echo "\n\n\n Name : ".$enquiry_data['cust_name'];

echo "\n\n Email : ".$enquiry_data['cust_email'];
if(!empty($enquiry_data['phone']))
	echo "\n\n User Phone : ".$enquiry_data['phone'];
if(!empty($enquiry_data['address']))
	echo "\n\n User Address : ".$enquiry_data['address'];
if(!empty($enquiry_data['subject']))
	echo "\n\n User Subject : ".$enquiry_data['subject'];
if(!empty($enquiry_data['comment']))
	echo "\n\n User Comments : ".$enquiry_data['comment'];

echo "\n\n\n****************************************************\n\n";

echo apply_filters('wc_catalog_enquiry_email_footer_text', sprintf( __( '%s - Powered by WC Catalog Enquiey', 'woocommerce-catalog-enquiry' ), get_bloginfo( 'name', 'display' ) ) );
