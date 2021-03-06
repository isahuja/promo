<?php

/**
 * Wp in Progress
 * 
 * @author WPinProgress
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available at this URL: http://www.gnu.org/licenses/gpl-3.0.txt
 */

$bazaarlite_new_metaboxes = new bazaarlite_metaboxes ('post', array (

array( "name" => "Navigation",  
       "type" => "navigation",  
	   
       "item" => array( 
	   		
			"setting" => esc_html__( "Setting","bazaar-lite") , 
			"sidebars" => esc_html__( "Sidebars","bazaar-lite") , 
		),
		   
       "start" => "<ul>", 
       "end" => "</ul>"),  

array( "type" => "begintab",
	   "tab" => "setting",
	   "element" =>

		array( "name" => esc_html__( "Setting","bazaar-lite"),
			   "type" => "title",
			  ),
		
		array( "name" => esc_html__( "Template","bazaar-lite"),
			   "desc" => esc_html__( "Choose a template for this post","bazaar-lite"),
			   "id" => "wip_template",
			   "type" => "select",
			   "options" => array(
				   "full" => esc_html__( "Full Width","bazaar-lite"),
				   "left-sidebar" =>  esc_html__( "Left Sidebar","bazaar-lite"),
				   "right-sidebar" => esc_html__( "Right Sidebar","bazaar-lite"),
			  ),
			  
			   "std" => "right-sidebar",
			   
		),
		
),

array( "type" => "endtab"),

array( "type" => "begintab",
	   "tab" => "sidebars",
	   "element" =>

		array( "name" => esc_html__( "Sidebars","bazaar-lite"),
			   "type" => "title",
			  ),

		array( "name" => esc_html__( "Header Sidebar","bazaar-lite"),
			   "desc" => esc_html__( "Choose a header sidebar","bazaar-lite"),
			   "id" => "wip_header_sidebar",
			   "type" => "select",
			   "std" => "none",
			   "options" => bazaarlite_sidebar_list('header'),
			),

		array( "name" => esc_html__( "Sidebar","bazaar-lite"),
			   "desc" => esc_html__( "Choose a side sidebar","bazaar-lite"),
			   "id" => "wip_sidebar",
			   "type" => "select",
			   "std" => "Default",
			   "options" => bazaarlite_sidebar_list('side'),
			),

		array( "name" => esc_html__( "Bottom Sidebar","bazaar-lite"),
			   "desc" => esc_html__( "Choose a bottom sidebar","bazaar-lite"),
			   "id" => "wip_bottom_sidebar",
			   "type" => "select",
			   "std" => "none",
			   "options" => bazaarlite_sidebar_list('bottom'),
			),

		array( "name" => esc_html__( "Footer Sidebar","bazaar-lite"),
			   "desc" => esc_html__( "Choose a footer sidebar","bazaar-lite"),
			   "id" => "wip_footer_sidebar",
			   "type" => "select",
			   "std" => "none",
			   "options" => bazaarlite_sidebar_list('footer'),
			),

),

array( "type" => "endtab"),

array( "type" => "endtab")
)

);


?>