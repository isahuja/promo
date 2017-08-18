<?php

if (!function_exists('bazaarlite_customize_panel_function')) {

	function bazaarlite_customize_panel_function() {
		
		$theme_panel = array ( 

			/* FULL IMAGE BACKGROUND */ 

			array(
				
				"label" => esc_html__( "Full Image Background",'bazaar-lite'),
				"description" => esc_html__( "Do you want to set a full background image? (After the upload, check 'Fixed', from the Background Attachment section)",'bazaar-lite'),
				"id" => "wip_full_image_background",
				"type" => "select",
				"section" => "background_image",
				"options" => array (
				   "off" => esc_html__( "No",'bazaar-lite'),
				   "on" => esc_html__( "Yes",'bazaar-lite'),
				),
				
				"std" => "off",
			
			),

			/* START SUPPORT SECTION */ 

			array(
			
				"title" => esc_html__( "Get support","bazaar-lite"),
				"id" => "bazaarlite-customize-info",
				"type" => "bazaarlite-customize-info",
				"section" => "bazaarlite-customize-info",
				"priority" => "08",

			),

			/* START GENERAL SECTION */ 

			array( 
				
				"title" => esc_html__( "Slideshow",'bazaar-lite'),
				"description" => esc_html__( "Slideshow",'bazaar-lite'),
				"type" => "panel",
				"id" => "slideshow_panel",
				"priority" => "09",
				
			),

			/* SLIDESHOW */ 

			array( 

				"title" => esc_html__( "Slideshow Settings",'bazaar-lite'),
				"type" => "section",
				"panel" => "slideshow_panel",
				"priority" => "10",
				"id" => "slideshow_settings",

			),

			array(
				
				"label" => esc_html__( "Slideshow",'bazaar-lite'),
				"description" => esc_html__( "Do you want to enable the slideshow?",'bazaar-lite'),
				"id" => "wip_enable_slideshow",
				"type" => "select",
				"section" => "slideshow_settings",
				"options" => array (
				   "off" => esc_html__( "No",'bazaar-lite'),
				   "on" => esc_html__( "Yes",'bazaar-lite'),
				),
				
				"std" => "on",
			
			),

			array(
				
				"label" => esc_html__( "Slideshow Autoplay",'bazaar-lite'),
				"description" => esc_html__( "Do you want to enable the slideshow autoplay?",'bazaar-lite'),
				"id" => "wip_slick_autoplay",
				"type" => "select",
				"section" => "slideshow_settings",
				"options" => array (
				   "false" => esc_html__( "No",'bazaar-lite'),
				   "true" => esc_html__( "Yes",'bazaar-lite'),
				),
				
				"std" => "false",
			
			),

			array(
				
				"label" => esc_html__( "Slideshow Dots",'bazaar-lite'),
				"description" => esc_html__( "Do you want to view a pagination for the slideshow?",'bazaar-lite'),
				"id" => "wip_slick_dots",
				"type" => "select",
				"section" => "slideshow_settings",
				"options" => array (
				   "false" => esc_html__( "No",'bazaar-lite'),
				   "true" => esc_html__( "Yes",'bazaar-lite'),
				),
				
				"std" => "true",
			
			),

			array(
				
				"label" => esc_html__( "Slideshow fixed images",'bazaar-lite'),
				"description" => esc_html__( "Do you want to set the 'background-attachment' css rule as fixed, for the slideshow images?",'bazaar-lite'),
				"id" => "wip_fixed_images",
				"type" => "select",
				"section" => "slideshow_settings",
				"options" => array (
				   "off" => esc_html__( "No",'bazaar-lite'),
				   "on" => esc_html__( "Yes",'bazaar-lite'),
				),
				
				"std" => "on",
			
			),

			array( 

				"label" => esc_html__( "Slideshow height",'bazaar-lite'),
				"description" => esc_html__( "Set the height of slideshow.",'bazaar-lite'),
				"id" => "wip_slideshow_height",
				"type" => "text",
				"section" => "slideshow_settings",
				"std" => "700px",

			),

			array( 

				"label" => esc_html__( "Slideshow mobile height",'bazaar-lite'),
				"description" => esc_html__( "Set the height of slideshow, for mobile devices.",'bazaar-lite'),
				"id" => "wip_slideshow_mobile_height",
				"type" => "text",
				"section" => "slideshow_settings",
				"std" => "500px",

			),

			array(
				
				"label" => esc_html__( "Image position",'bazaar-lite'),
				"description" => esc_html__( "Image position",'bazaar-lite'),
				"id" => "wip_slideshow_image_position",
				"type" => "select",
				"section" => "slideshow_settings",
				"options" => array(
					"" => "None",
					"top left" => "top left",
					"top center" => "top center",
					"top right" => "top right",
					"center" => "center",
					"bottom left" => "bottom left",
					"bottom center" => "bottom center",
					"bottom right" => "bottom right",
				),
				
				"std" => "center",
			
			),

			/* #1 SLIDE */ 

			array( 

				"title" => esc_html__( "Slide #1",'bazaar-lite'),
				"type" => "section",
				"panel" => "slideshow_panel",
				"priority" => "10",
				"id" => "slideshow_1",

			),

			array(
				
				"label" => esc_html__( "Default image",'bazaar-lite'),
				"description" => esc_html__( "Do you want to hide this slideshow item?",'bazaar-lite'),
				"id" => "wip_slideshow_1_default_image",
				"type" => "select",
				"section" => "slideshow_1",
				"options" => array (
				   "off" => esc_html__( "No",'bazaar-lite'),
				   "on" => esc_html__( "Yes",'bazaar-lite'),
				),
				
				"std" => "off",
			
			),

			array( 

				"label" => esc_html__( "Image",'bazaar-lite'),
				"description" => esc_html__( "Upload the image",'bazaar-lite'),
				"id" => "wip_slideshow_1_image",
				"type" => "upload",
				"section" => "slideshow_1",
				"std" => get_template_directory_uri().'/assets/images/slideshow/img01.jpg',

			),

			array( 

				"label" => esc_html__( "Title",'bazaar-lite'),
				"description" => esc_html__( "Insert the title of this slide",'bazaar-lite'),
				"id" => "wip_slideshow_1_title",
				"type" => "text",
				"section" => "slideshow_1",
				"std" => "Welcome on Bazaar Lite",

			),

			array( 

				"label" => esc_html__( "Call to action",'bazaar-lite'),
				"description" => esc_html__( "Insert the call to action of this slide",'bazaar-lite'),
				"id" => "wip_slideshow_1_cta",
				"type" => "text",
				"section" => "slideshow_1",
				"std" => "Get Now",

			),

			array( 

				"label" => esc_html__( "Url",'bazaar-lite'),
				"description" => esc_html__( "Insert the url of this slide",'bazaar-lite'),
				"id" => "wip_slideshow_1_url",
				"type" => "url",
				"section" => "slideshow_1",
				"std" => "#",

			),

			/* #2 SLIDE */ 

			array( 

				"title" => esc_html__( "Slide #2",'bazaar-lite'),
				"type" => "section",
				"panel" => "slideshow_panel",
				"priority" => "10",
				"id" => "slideshow_2",

			),

			array(
				
				"label" => esc_html__( "Default image",'bazaar-lite'),
				"description" => esc_html__( "Do you want to hide this slideshow item?",'bazaar-lite'),
				"id" => "wip_slideshow_2_default_image",
				"type" => "select",
				"section" => "slideshow_2",
				"options" => array (
				   "off" => esc_html__( "No",'bazaar-lite'),
				   "on" => esc_html__( "Yes",'bazaar-lite'),
				),
				
				"std" => "off",
			
			),

			array( 

				"label" => esc_html__( "Image",'bazaar-lite'),
				"description" => esc_html__( "Upload the image",'bazaar-lite'),
				"id" => "wip_slideshow_2_image",
				"type" => "upload",
				"section" => "slideshow_2",
				"std" => get_template_directory_uri().'/assets/images/slideshow/img02.jpg',

			),

			array( 

				"label" => esc_html__( "Title",'bazaar-lite'),
				"description" => esc_html__( "Insert the title of this slide",'bazaar-lite'),
				"id" => "wip_slideshow_2_title",
				"type" => "text",
				"section" => "slideshow_2",
				"std" => "Welcome on Bazaar Lite",

			),

			array( 

				"label" => esc_html__( "Call to action",'bazaar-lite'),
				"description" => esc_html__( "Insert the call to action of this slide",'bazaar-lite'),
				"id" => "wip_slideshow_2_cta",
				"type" => "text",
				"section" => "slideshow_2",
				"std" => "Get Now",

			),

			array( 

				"label" => esc_html__( "Url",'bazaar-lite'),
				"description" => esc_html__( "Insert the url of this slide",'bazaar-lite'),
				"id" => "wip_slideshow_2_url",
				"type" => "url",
				"section" => "slideshow_2",
				"std" => "#",

			),

			/* #3 SLIDE */ 

			array( 

				"title" => esc_html__( "Slide #3",'bazaar-lite'),
				"type" => "section",
				"panel" => "slideshow_panel",
				"priority" => "10",
				"id" => "slideshow_3",

			),
			
			array(
				
				"label" => esc_html__( "Default image",'bazaar-lite'),
				"description" => esc_html__( "Do you want to hide this slideshow item?",'bazaar-lite'),
				"id" => "wip_slideshow_3_default_image",
				"type" => "select",
				"section" => "slideshow_3",
				"options" => array (
				   "off" => esc_html__( "No",'bazaar-lite'),
				   "on" => esc_html__( "Yes",'bazaar-lite'),
				),
				
				"std" => "off",
			
			),

			array( 

				"label" => esc_html__( "Image",'bazaar-lite'),
				"description" => esc_html__( "Upload the image",'bazaar-lite'),
				"id" => "wip_slideshow_3_image",
				"type" => "upload",
				"section" => "slideshow_3",
				"std" => get_template_directory_uri().'/assets/images/slideshow/img03.jpg',

			),

			array( 

				"label" => esc_html__( "Title",'bazaar-lite'),
				"description" => esc_html__( "Insert the title of this slide",'bazaar-lite'),
				"id" => "wip_slideshow_3_title",
				"type" => "text",
				"section" => "slideshow_3",
				"std" => "Welcome on Bazaar Lite",

			),

			array( 

				"label" => esc_html__( "Call to action",'bazaar-lite'),
				"description" => esc_html__( "Insert the call to action of this slide",'bazaar-lite'),
				"id" => "wip_slideshow_3_cta",
				"type" => "text",
				"section" => "slideshow_3",
				"std" => "Get Now",

			),

			array( 

				"label" => esc_html__( "Url",'bazaar-lite'),
				"description" => esc_html__( "Insert the url of this slide",'bazaar-lite'),
				"id" => "wip_slideshow_3_url",
				"type" => "url",
				"section" => "slideshow_3",
				"std" => "#",

			),

			/* #4 SLIDE */ 

			array( 

				"title" => esc_html__( "Slide #4",'bazaar-lite'),
				"type" => "section",
				"panel" => "slideshow_panel",
				"priority" => "10",
				"id" => "slideshow_4",

			),

			array(
				
				"label" => esc_html__( "Default image",'bazaar-lite'),
				"description" => esc_html__( "Do you want to hide this slideshow item?",'bazaar-lite'),
				"id" => "wip_slideshow_4_default_image",
				"type" => "select",
				"section" => "slideshow_4",
				"options" => array (
				   "off" => esc_html__( "No",'bazaar-lite'),
				   "on" => esc_html__( "Yes",'bazaar-lite'),
				),
				
				"std" => "off",
			
			),

			array( 

				"label" => esc_html__( "Image",'bazaar-lite'),
				"description" => esc_html__( "Upload the image",'bazaar-lite'),
				"id" => "wip_slideshow_4_image",
				"type" => "upload",
				"section" => "slideshow_4",
				"std" => get_template_directory_uri().'/assets/images/slideshow/img04.jpg',

			),

			array( 

				"label" => esc_html__( "Title",'bazaar-lite'),
				"description" => esc_html__( "Insert the title of this slide",'bazaar-lite'),
				"id" => "wip_slideshow_4_title",
				"type" => "text",
				"section" => "slideshow_4",
				"std" => "Welcome on Bazaar Lite",

			),

			array( 

				"label" => esc_html__( "Call to action",'bazaar-lite'),
				"description" => esc_html__( "Insert the call to action of this slide",'bazaar-lite'),
				"id" => "wip_slideshow_4_cta",
				"type" => "text",
				"section" => "slideshow_4",
				"std" => "Get Now",

			),

			array( 

				"label" => esc_html__( "Url",'bazaar-lite'),
				"description" => esc_html__( "Insert the url of this slide",'bazaar-lite'),
				"id" => "wip_slideshow_4_url",
				"type" => "url",
				"section" => "slideshow_4",
				"std" => "#",

			),

			/* #5 SLIDE */ 

			array( 

				"title" => esc_html__( "Slide #5",'bazaar-lite'),
				"type" => "section",
				"panel" => "slideshow_panel",
				"priority" => "10",
				"id" => "slideshow_5",

			),

			array(
				
				"label" => esc_html__( "Default image",'bazaar-lite'),
				"description" => esc_html__( "Do you want to hide this slideshow item?",'bazaar-lite'),
				"id" => "wip_slideshow_5_default_image",
				"type" => "select",
				"section" => "slideshow_5",
				"options" => array (
				   "off" => esc_html__( "No",'bazaar-lite'),
				   "on" => esc_html__( "Yes",'bazaar-lite'),
				),
				
				"std" => "off",
			
			),

			array( 

				"label" => esc_html__( "Image",'bazaar-lite'),
				"description" => esc_html__( "Upload the image",'bazaar-lite'),
				"id" => "wip_slideshow_5_image",
				"type" => "upload",
				"section" => "slideshow_5",
				"std" => get_template_directory_uri().'/assets/images/slideshow/img05.jpg',

			),

			array( 

				"label" => esc_html__( "Title",'bazaar-lite'),
				"description" => esc_html__( "Insert the title of this slide",'bazaar-lite'),
				"id" => "wip_slideshow_5_title",
				"type" => "text",
				"section" => "slideshow_5",
				"std" => "Welcome on Bazaar Lite",

			),

			array( 

				"label" => esc_html__( "Call to action",'bazaar-lite'),
				"description" => esc_html__( "Insert the call to action of this slide",'bazaar-lite'),
				"id" => "wip_slideshow_5_cta",
				"type" => "text",
				"section" => "slideshow_5",
				"std" => "Get Now",

			),

			array( 

				"label" => esc_html__( "Url",'bazaar-lite'),
				"description" => esc_html__( "Insert the url of this slide",'bazaar-lite'),
				"id" => "wip_slideshow_5_url",
				"type" => "url",
				"section" => "slideshow_5",
				"std" => "#",

			),

			/* #6 SLIDE */ 

			array( 

				"title" => esc_html__( "Slide #6",'bazaar-lite'),
				"type" => "section",
				"panel" => "slideshow_panel",
				"priority" => "10",
				"id" => "slideshow_6",

			),

			array(
				
				"label" => esc_html__( "Default image",'bazaar-lite'),
				"description" => esc_html__( "Do you want to hide this slideshow item?",'bazaar-lite'),
				"id" => "wip_slideshow_6_default_image",
				"type" => "select",
				"section" => "slideshow_6",
				"options" => array (
				   "off" => esc_html__( "No",'bazaar-lite'),
				   "on" => esc_html__( "Yes",'bazaar-lite'),
				),
				
				"std" => "off",
			
			),

			array( 

				"label" => esc_html__( "Image",'bazaar-lite'),
				"description" => esc_html__( "Upload the image",'bazaar-lite'),
				"id" => "wip_slideshow_6_image",
				"type" => "upload",
				"section" => "slideshow_6",
				"std" => get_template_directory_uri().'/assets/images/slideshow/img06.jpg',

			),

			array( 

				"label" => esc_html__( "Title",'bazaar-lite'),
				"description" => esc_html__( "Insert the title of this slide",'bazaar-lite'),
				"id" => "wip_slideshow_6_title",
				"type" => "text",
				"section" => "slideshow_6",
				"std" => "Welcome on Bazaar Lite",

			),

			array( 

				"label" => esc_html__( "Call to action",'bazaar-lite'),
				"description" => esc_html__( "Insert the call to action of this slide",'bazaar-lite'),
				"id" => "wip_slideshow_6_cta",
				"type" => "text",
				"section" => "slideshow_6",
				"std" => "Get Now",

			),

			array( 

				"label" => esc_html__( "Url",'bazaar-lite'),
				"description" => esc_html__( "Insert the url of this slide",'bazaar-lite'),
				"id" => "wip_slideshow_6_url",
				"type" => "url",
				"section" => "slideshow_6",
				"std" => "#",

			),

			/* START GENERAL SECTION */ 

			array( 
				
				"title" => esc_html__( "General",'bazaar-lite'),
				"description" => esc_html__( "General",'bazaar-lite'),
				"type" => "panel",
				"id" => "general_panel",
				"priority" => "10",
				
			),

			/* SKINS */ 

			array( 

				"title" => esc_html__( "Color Scheme",'bazaar-lite'),
				"type" => "section",
				"panel" => "general_panel",
				"priority" => "11",
				"id" => "colorscheme_section",

			),

			array(
				
				"label" => esc_html__( "Predefined Color Schemes",'bazaar-lite'),
				"description" => esc_html__( "Choose your Color Scheme",'bazaar-lite'),
				"id" => "wip_skin",
				"type" => "select",
				"section" => "colorscheme_section",
				"options" => array (
				   "black_turquoise" => esc_html__( "Black & Turquoise",'bazaar-lite'),
				   "black_orange" => esc_html__( "Black & Orange",'bazaar-lite'),
				   "black_blue" => esc_html__( "Black & Blue",'bazaar-lite'),
				   "black_red" => esc_html__( "Black & Red",'bazaar-lite'),
				   "black_pink" => esc_html__( "Black & Pink",'bazaar-lite'),
				   "black_purple" => esc_html__( "Black & Purple",'bazaar-lite'),
				   "black_yellow" => esc_html__( "Black & Yellow",'bazaar-lite'),
				   "black_green" => esc_html__( "Black & Green",'bazaar-lite'),
				   "turquoise" => esc_html__( "Turquoise",'bazaar-lite'),
				   "orange" => esc_html__( "Orange",'bazaar-lite'),
				   "blue" => esc_html__( "Blue",'bazaar-lite'),
				   "red" => esc_html__( "Red",'bazaar-lite'),
				   "pink" => esc_html__( "Pink",'bazaar-lite'),
				   "purple" => esc_html__( "Purple",'bazaar-lite'),
				   "yellow" => esc_html__( "Yellow",'bazaar-lite'),
				   "green" => esc_html__( "Green",'bazaar-lite'),
				   "white_turquoise" => esc_html__( "White & Turquoise",'bazaar-lite'),
				   "white_orange" => esc_html__( "White & Orange",'bazaar-lite'),
				   "white_blue" => esc_html__( "White & Blue",'bazaar-lite'),
				   "white_red" => esc_html__( "White & Red",'bazaar-lite'),
				   "white_pink" => esc_html__( "White & Pink",'bazaar-lite'),
				   "white_purple" => esc_html__( "White & Purple",'bazaar-lite'),
				   "white_yellow" => esc_html__( "White & Yellow",'bazaar-lite'),
				   "white_green" => esc_html__( "White & Green",'bazaar-lite'),
				),
				
				"std" => "black_turquoise",
			
			),

			/* PAGE WIDTH */ 

			array( 

				"title" => esc_html__( "Page width",'bazaar-lite'),
				"type" => "section",
				"id" => "pagewidth_section",
				"panel" => "general_panel",
				"priority" => "12",

			),

			array( 

				"label" => esc_html__( "Screen greater than 768px",'bazaar-lite'),
				"description" => esc_html__( "Set a width, for a screen greater than 768 pixel (for example 750 and not 750px ) ",'bazaar-lite'),
				"id" => "wip_screen1",
				"type" => "text",
				"section" => "pagewidth_section",
				"std" => "750",

			),

			array( 

				"label" => esc_html__( "Screen greater than 992px",'bazaar-lite'),
				"description" => esc_html__( "Set a width, for a screen greater than 992 pixel (for example 940 and not 940px ) ",'bazaar-lite'),
				"id" => "wip_screen2",
				"type" => "text",
				"section" => "pagewidth_section",
				"std" => "940",

			),

			array( 

				"label" => esc_html__( "Screen greater than 1200px",'bazaar-lite'),
				"description" => esc_html__( "Set a width, in px, for a screen greater than 1200 pixel (for example 1170 and not 1170px ) ",'bazaar-lite'),
				"id" => "wip_screen3",
				"type" => "text",
				"section" => "pagewidth_section",
				"std" => "940",

			),

			array( 

				"label" => esc_html__( "Screen greater than 1400px",'bazaar-lite'),
				"description" => esc_html__( "Set a width, in px, for a screen greater than 1400px pixel (for example 1370 and not 1370px ) ",'bazaar-lite'),
				"id" => "wip_screen4",
				"type" => "text",
				"section" => "pagewidth_section",
				"std" => "940",

			),

			/* SETTINGS SECTION */ 

			array( 

				"title" => esc_html__( "Settings",'bazaar-lite'),
				"type" => "section",
				"id" => "settings_section",
				"panel" => "general_panel",
				"priority" => "13",

			),

			array(
				
				"label" => esc_html__( "Header cart",'bazaar-lite'),
				"description" => esc_html__( "Do you want to show the header cart?",'bazaar-lite'),
				"id" => "wip_woocommerce_header_cart",
				"type" => "select",
				"section" => "settings_section",
				"options" => array (
				   "off" => esc_html__( "No",'bazaar-lite'),
				   "on" => esc_html__( "Yes",'bazaar-lite'),
				),
				
				"std" => "off",
			
			),

			array(
				
				"label" => esc_html__( "Minimal layout",'bazaar-lite'),
				"description" => esc_html__( "Do you want to use a minimal layout, with a white background color?",'bazaar-lite'),
				"id" => "wip_minimal_layout",
				"type" => "select",
				"section" => "settings_section",
				"options" => array (
				   "off" => esc_html__( "No",'bazaar-lite'),
				   "on" => esc_html__( "Yes",'bazaar-lite'),
				),
				
				"std" => "off",
			
			),

			array(
				
				"label" => esc_html__( "Infinite scroll",'bazaar-lite'),
				"description" => esc_html__( "Do you want to use the infinite scroll, for the articles?",'bazaar-lite'),
				"id" => "wip_infinitescroll_system",
				"type" => "select",
				"section" => "settings_section",
				"options" => array (
				   "off" => esc_html__( "No",'bazaar-lite'),
				   "on" => esc_html__( "Yes",'bazaar-lite'),
				),
				
				"std" => "off",
			
			),

			array(
				
				"label" => esc_html__( "Read more button",'bazaar-lite'),
				"description" => esc_html__( "Do you want to use a button, for open the posts? (Instead of [&hellip;])",'bazaar-lite'),
				"id" => "wip_readmore_button",
				"type" => "select",
				"section" => "settings_section",
				"options" => array (
				   "off" => esc_html__( "No",'bazaar-lite'),
				   "on" => esc_html__( "Yes",'bazaar-lite'),
				),
				
				"std" => "off",
			
			),

			array( 

				"title" => esc_html__( "Styles",'bazaar-lite'),
				"type" => "section",
				"id" => "styles_section",
				"panel" => "general_panel",
				"priority" => "14",

			),

			array( 

				"label" => esc_html__( "Custom css",'bazaar-lite'),
				"description" => esc_html__( "Insert your custom css code.",'bazaar-lite'),
				"id" => "wip_custom_css_code",
				"type" => "custom_css",
				"section" => "styles_section",
				"std" => "",

			),

			/* LAYOUTS SECTION */ 

			array( 

				"title" => esc_html__( "Layouts",'bazaar-lite'),
				"type" => "section",
				"id" => "layouts_section",
				"panel" => "general_panel",
				"priority" => "15",

			),

			array(
				
				"label" => esc_html__("Home Blog Layout",'bazaar-lite'),
				"description" => esc_html__("If you've set the latest articles, for the homepage, choose a layout.",'bazaar-lite'),
				"id" => "wip_home",
				"type" => "select",
				"section" => "layouts_section",
				"options" => array (
				   "full" => esc_html__( "Full Width",'bazaar-lite'),
				   "left-sidebar" => esc_html__( "Left Sidebar",'bazaar-lite'),
				   "right-sidebar" => esc_html__( "Right Sidebar",'bazaar-lite'),
				   "masonry" => esc_html__( "Masonry","bazaar-lite"),
				),
				
				"std" => "masonry",
			
			),
	

			array(
				
				"label" => esc_html__("Category Layout",'bazaar-lite'),
				"description" => esc_html__("Select a layout for category pages.",'bazaar-lite'),
				"id" => "wip_category_layout",
				"type" => "select",
				"section" => "layouts_section",
				"options" => array (
				   "full" => esc_html__( "Full Width",'bazaar-lite'),
				   "left-sidebar" => esc_html__( "Left Sidebar",'bazaar-lite'),
				   "right-sidebar" => esc_html__( "Right Sidebar",'bazaar-lite'),
				   "masonry" => esc_html__( "Masonry","bazaar-lite"),
				),
				
				"std" => "masonry",
			
			),
	
			array(
				
				"label" => esc_html__("WooCommerce Category Layout",'bazaar-lite'),
				"description" => esc_html__("Select a layout for the WooCommerce categories.",'bazaar-lite'),
				"id" => "wip_woocommerce_category_layout",
				"type" => "select",
				"section" => "layouts_section",
				"options" => array (
				   "full" => esc_html__( "Full Width",'bazaar-lite'),
				   "left-sidebar" => esc_html__( "Left Sidebar",'bazaar-lite'),
				   "right-sidebar" => esc_html__( "Right Sidebar",'bazaar-lite'),
				),
				
				"std" => "right-sidebar",
			
			),
	
			array(
				
				"label" => esc_html__("Search Layout",'bazaar-lite'),
				"description" => esc_html__("Select a layout for the search page.",'bazaar-lite'),
				"id" => "wip_search_layout",
				"type" => "select",
				"section" => "layouts_section",
				"options" => array (
				   "full" => esc_html__( "Full Width",'bazaar-lite'),
				   "left-sidebar" => esc_html__( "Left Sidebar",'bazaar-lite'),
				   "right-sidebar" => esc_html__( "Right Sidebar",'bazaar-lite'),
				   "masonry" => esc_html__( "Masonry","bazaar-lite"),
				),
				
				"std" => "masonry",
			
			),

			/* THUMBNAILS SECTION */ 

			array( 

				"title" => esc_html__( "Thumbnails",'bazaar-lite'),
				"type" => "section",
				"id" => "thumbnails_section",
				"panel" => "general_panel",
				"priority" => "16",

			),

			array( 

				"label" => esc_html__( "Blog Thumbnail",'bazaar-lite'),
				"description" => esc_html__( "Insert the height for blog thumbnail.",'bazaar-lite'),
				"id" => "wip_blog_thumbinal",
				"type" => "text",
				"section" => "thumbnails_section",
				"std" => "550",

			),

			/* HEADER AREA SECTION */ 

			array( 

				"title" => esc_html__( "Header",'bazaar-lite'),
				"type" => "section",
				"id" => "header_section",
				"panel" => "general_panel",
				"priority" => "18",

			),

			array( 

				"label" => esc_html__( "Custom Logo",'bazaar-lite'),
				"description" => esc_html__( "Upload your custom logo",'bazaar-lite'),
				"id" => "wip_custom_logo",
				"type" => "upload",
				"section" => "header_section",
				"std" => "",

			),

			/* FOOTER AREA SECTION */ 

			array( 

				"title" => esc_html__( "Footer",'bazaar-lite'),
				"type" => "section",
				"id" => "footer_section",
				"panel" => "general_panel",
				"priority" => "19",

			),

			array( 

				"label" => esc_html__( "Copyright Text",'bazaar-lite'),
				"description" => esc_html__( "Insert your copyright text.",'bazaar-lite'),
				"id" => "wip_copyright_text",
				"type" => "textarea",
				"section" => "footer_section",
				"std" => "",

			),

			array( 

				"label" => esc_html__( "Facebook Url",'bazaar-lite'),
				"description" => esc_html__( "Insert Facebook Url (empty if you want to hide the button)",'bazaar-lite'),
				"id" => "wip_footer_facebook_button",
				"type" => "url",
				"section" => "footer_section",
				"std" => "",

			),

			array( 

				"label" => esc_html__( "Twitter Url",'bazaar-lite'),
				"description" => esc_html__( "Insert Twitter Url (empty if you want to hide the button)",'bazaar-lite'),
				"id" => "wip_footer_twitter_button",
				"type" => "url",
				"section" => "footer_section",
				"std" => "",

			),

			array( 

				"label" => esc_html__( "Flickr Url",'bazaar-lite'),
				"description" => esc_html__( "Insert Flickr Url (empty if you want to hide the button)",'bazaar-lite'),
				"id" => "wip_footer_flickr_button",
				"type" => "url",
				"section" => "footer_section",
				"std" => "",

			),

			array( 

				"label" => esc_html__( "Google Url",'bazaar-lite'),
				"description" => esc_html__( "Insert Google Url (empty if you want to hide the button)",'bazaar-lite'),
				"id" => "wip_footer_google_button",
				"type" => "url",
				"section" => "footer_section",
				"std" => "",

			),

			array( 

				"label" => esc_html__( "Linkedin Url",'bazaar-lite'),
				"description" => esc_html__( "Insert Linkedin Url (empty if you want to hide the button)",'bazaar-lite'),
				"id" => "wip_footer_linkedin_button",
				"type" => "url",
				"section" => "footer_section",
				"std" => "",

			),

			array( 

				"label" => esc_html__( "Pinterest Url",'bazaar-lite'),
				"description" => esc_html__( "Insert Pinterest Url (empty if you want to hide the button)",'bazaar-lite'),
				"id" => "wip_footer_pinterest_button",
				"type" => "url",
				"section" => "footer_section",
				"std" => "",

			),

			array( 

				"label" => esc_html__( "Tumblr Url",'bazaar-lite'),
				"description" => esc_html__( "Insert Tumblr Url (empty if you want to hide the button)",'bazaar-lite'),
				"id" => "wip_footer_tumblr_button",
				"type" => "url",
				"section" => "footer_section",
				"std" => "",

			),

			array( 

				"label" => esc_html__( "Youtube Url",'bazaar-lite'),
				"description" => esc_html__( "Insert Youtube Url (empty if you want to hide the button)",'bazaar-lite'),
				"id" => "wip_footer_youtube_button",
				"type" => "url",
				"section" => "footer_section",
				"std" => "",

			),

			array( 

				"label" => esc_html__( "Skype Url",'bazaar-lite'),
				"description" => esc_html__( "Insert Skype ID (empty if you want to hide the button)",'bazaar-lite'),
				"id" => "wip_footer_skype_button",
				"type" => "button",
				"section" => "footer_section",
				"std" => "",

			),

			array( 

				"label" => esc_html__( "Instagram Url",'bazaar-lite'),
				"description" => esc_html__( "Insert Instagram Url (empty if you want to hide the button)",'bazaar-lite'),
				"id" => "wip_footer_instagram_button",
				"type" => "url",
				"section" => "footer_section",
				"std" => "",

			),

			array( 

				"label" => esc_html__( "Github Url",'bazaar-lite'),
				"description" => esc_html__( "Insert Github Url (empty if you want to hide the button)",'bazaar-lite'),
				"id" => "wip_footer_github_button",
				"type" => "url",
				"section" => "footer_section",
				"std" => "",

			),

			array( 

				"label" => esc_html__( "Xing Url",'bazaar-lite'),
				"description" => esc_html__( "Insert Xing Url (empty if you want to hide the button)",'bazaar-lite'),
				"id" => "wip_footer_xing_button",
				"type" => "url",
				"section" => "footer_section",
				"std" => "",

			),

			array( 

				"label" => esc_html__( "WhatsApp number",'bazaar-lite'),
				"description" => esc_html__( "Insert WhatsApp number (empty if you want to hide the button)",'bazaar-lite'),
				"id" => "wip_footer_whatsapp_button",
				"type" => "button",
				"section" => "footer_section",
				"std" => "",

			),

			array( 

				"label" => esc_html__( "Email Address",'bazaar-lite'),
				"description" => esc_html__( "Insert Email Address (empty if you want to hide the button)",'bazaar-lite'),
				"id" => "wip_footer_email_button",
				"type" => "button",
				"section" => "footer_section",
				"std" => "",

			),

			array(
				
				"label" => esc_html__( "Feed Rss Button",'bazaar-lite'),
				"description" => esc_html__( "Do you want to display the Feed Rss button?",'bazaar-lite'),
				"id" => "wip_footer_rss_button",
				"type" => "select",
				"section" => "footer_section",
				"options" => array (
				   "off" => esc_html__( "No",'bazaar-lite'),
				   "on" => esc_html__( "Yes",'bazaar-lite'),
				),
				
				"std" => "off",
			
			),

			/* TYPOGRAPHY SECTION */ 

			array( 
				
				"title" => esc_html__( "Typography",'bazaar-lite'),
				"description" => esc_html__( "Typography",'bazaar-lite'),
				"type" => "panel",
				"id" => "typography_panel",
				"priority" => "11",
				
			),

			/* LOGO */ 

			array( 

				"title" => esc_html__( "Logo",'bazaar-lite'),
				"type" => "section",
				"id" => "logo_section",
				"panel" => "typography_panel",
				"priority" => "10",

			),

			array( 

				"label" => esc_html__( "Font size",'bazaar-lite'),
				"description" => esc_html__( "Insert a size, for logo font (For example, 30px) ",'bazaar-lite'),
				"id" => "wip_logo_font_size",
				"type" => "text",
				"section" => "logo_section",
				"std" => "30px",

			),

			/* MENU */ 

			array( 

				"title" => esc_html__( "Menu",'bazaar-lite'),
				"type" => "section",
				"id" => "menu_section",
				"panel" => "typography_panel",
				"priority" => "11",

			),

			array( 

				"label" => esc_html__( "Font size",'bazaar-lite'),
				"description" => esc_html__( "Insert a size, for menu font (For example, 14px) ",'bazaar-lite'),
				"id" => "wip_menu_font_size",
				"type" => "text",
				"section" => "menu_section",
				"std" => "14px",

			),

			/* CONTENT */ 

			array( 

				"title" => esc_html__( "Content",'bazaar-lite'),
				"type" => "section",
				"id" => "content_section",
				"panel" => "typography_panel",
				"priority" => "12",

			),

			array( 

				"label" => esc_html__( "Font size",'bazaar-lite'),
				"description" => esc_html__( "Insert a size, for content font (For example, 14px) ",'bazaar-lite'),
				"id" => "wip_content_font_size",
				"type" => "text",
				"section" => "content_section",
				"std" => "14px",

			),


			/* HEADLINES */ 

			array( 

				"title" => esc_html__( "Headlines",'bazaar-lite'),
				"type" => "section",
				"id" => "headlines_section",
				"panel" => "typography_panel",
				"priority" => "13",

			),

			array( 

				"label" => esc_html__( "H1 headline",'bazaar-lite'),
				"description" => esc_html__( "Insert a size, for for H1 elements (For example, 24px) ",'bazaar-lite'),
				"id" => "wip_h1_font_size",
				"type" => "text",
				"section" => "headlines_section",
				"std" => "24px",

			),

			array( 

				"label" => esc_html__( "H2 headline",'bazaar-lite'),
				"description" => esc_html__( "Insert a size, for for H2 elements (For example, 22px) ",'bazaar-lite'),
				"id" => "wip_h2_font_size",
				"type" => "text",
				"section" => "headlines_section",
				"std" => "22px",

			),

			array( 

				"label" => esc_html__( "H3 headline",'bazaar-lite'),
				"description" => esc_html__( "Insert a size, for for H3 elements (For example, 20px) ",'bazaar-lite'),
				"id" => "wip_h3_font_size",
				"type" => "text",
				"section" => "headlines_section",
				"std" => "20px",

			),

			array( 

				"label" => esc_html__( "H4 headline",'bazaar-lite'),
				"description" => esc_html__( "Insert a size, for for H4 elements (For example, 18px) ",'bazaar-lite'),
				"id" => "wip_h4_font_size",
				"type" => "text",
				"section" => "headlines_section",
				"std" => "18px",

			),

			array( 

				"label" => esc_html__( "H5 headline",'bazaar-lite'),
				"description" => esc_html__( "Insert a size, for for H5 elements (For example, 16px) ",'bazaar-lite'),
				"id" => "wip_h5_font_size",
				"type" => "text",
				"section" => "headlines_section",
				"std" => "16px",

			),

			array( 

				"label" => esc_html__( "H6 headline",'bazaar-lite'),
				"description" => esc_html__( "Insert a size, for for H6 elements (For example, 14px) ",'bazaar-lite'),
				"id" => "wip_h6_font_size",
				"type" => "text",
				"section" => "headlines_section",
				"std" => "14px",

			),
		);
		
		new bazaarlite_customize($theme_panel);
		
	} 
	
	add_action( 'bazaarlite_customize_panel', 'bazaarlite_customize_panel_function', 10, 2 );

}

do_action('bazaarlite_customize_panel');

?>