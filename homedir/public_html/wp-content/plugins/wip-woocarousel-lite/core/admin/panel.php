<?php

/**
 * Wp in Progress
 * 
 * @package Wordpress
 * @author WPinProgress
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available at this URL: http://www.gnu.org/licenses/gpl-3.0.txt
 */

$optpanel = array (

	array (	"name" => "Navigation",  
			"type" => "navigation",  
			"item" => array( 
				"Settings"		=> __( "Settings","wip-woocarousel-lite"),
				"Import_Export"	=> __( "Import/Export","wip-woocarousel-lite"),
			),   
			
			"start" => "<ul>", 
			"end" => "</ul>"
	),  

	array(	"tab" => "Settings",
			"element" =>
		   
		array(	"type" => "start-form",
				"name" => "Settings"),

			array(	"type" => "start-open-container",
					"name" => __( "Settings","wip-woocarousel-lite")),

				array(	"name" => __( "Custom Css","wip-woocarousel-lite"),
						"desc" => __( "Insert your custom css code.","wip-woocarousel-lite"),
						"id" => "wip_woocarousel_css_code",
						"type" => "textarea",
						"std" => ""),

				array(	"type" => "save-button",
						"value" => "Save",
						"class" => "Settings"),
			
			array(	"type" => "end-container"),

		array(	"type" => "end-form"),

	),
	
	array(	"tab" => "Import_Export",
			"element" =>
		   
		array(	"type" => "start-form",
				"name" => "Import_Export"),

			array(	"type" => "start-open-container",
					"name" => __( "Import / Export", "wip-woocarousel-lite")),
			
				array(	"name" => __( "Import / Export", "wip-woocarousel-lite"),
						"type" => "import_export"),
				
			array(	"type" => "end-container"),

		array(	"type" => "end-form"),

	),
	
	array(	"type" => "end-tab"),

	array(	"type" => "end-panel"),  

);

new wip_woocarousel_lite_panel ($optpanel);

?>