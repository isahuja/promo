<?php 

/*

	Plugin Name: WIP WooCarousel Lite
	Plugin URI: https://www.themeinprogress.com
	Description: WIP WooCarousel Lite allows you to create a product carousel for your WooCommerce website.
	Version: 1.0.7
	Text Domain: wip-woocarousel-lite
	Author: Theme in Progress
	Author URI: https://www.themeinprogress.com
	License: GPL2
	Domain Path: /languages/

	Copyright 2016  ThemeinProgress  (email : info@wpinprogress.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

if( !class_exists( 'wip_woocarousel_lite_init' ) ) {

	class wip_woocarousel_lite_init {
	
		/**
		* Constructor
		*/
			 
		public function __construct(){
	
			add_action('plugins_loaded', array(&$this,'plugin_setup'));
			add_action('wp_enqueue_scripts', array(&$this,'load_scripts') );
			add_filter('plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'plugin_action_links' ), 10, 2 );
			add_action('wp_head', array(&$this,'custom_css'));

		}

		/**
		* Plugin settings link
		*/
			 
		public function plugin_action_links( $links ) {
			
			$links[] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=wip_woocarousel_lite_panel') ) .'">' . __('Settings','wip-woocarousel-lite') . '</a>';
			return $links;
						
		}		

		/**
		* Plugin setup
		*/
			 
		public function plugin_setup() {
			
			load_plugin_textdomain( 'wip-woocarousel-lite', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
			
			require_once dirname(__FILE__) . '/core/includes/class-panel.php';
			require_once dirname(__FILE__) . '/core/includes/class-notice.php';
			require_once dirname(__FILE__) . '/shortcode/products_carousel.php';
			
			if ( is_admin() == 1 )
				require_once dirname(__FILE__) . '/core/admin/panel.php';
		
		}
		
		/**
		* Load scripts
		*/
			 
		public function load_scripts() {
	
			wp_enqueue_script( 'jquery' );
			
			wp_enqueue_script( 'wip_woocarousel_lite_jquery.slick.min', plugins_url('/assets/js/jquery.slick.min.js', __FILE__ ), array('jquery'), FALSE, TRUE );
			wp_enqueue_script( 'wip_woocarousel_lite_woocarousel', plugins_url('/assets/js/woocarousel.js', __FILE__ ), array('jquery'), FALSE, TRUE );
			
			wp_enqueue_style( 'wip_woocarousel_lite_slick.css', plugins_url('/assets/css/slick.css', __FILE__ ), array(), null );
			wp_enqueue_style( 'wip_woocarousel_lite_style.css', plugins_url('/assets/css/woocarousel.css', __FILE__ ), array(), null );
		
		}

		/**
		* Custom css
		*/
			 
		public function custom_css() {
			
			if ( wip_woocarousel_lite_setting('wip_woocarousel_css_code') ) :
				
				$css = '<style type="text/css">' . esc_html(wip_woocarousel_lite_setting('wip_woocarousel_css_code')) . '</style>'; 
				echo str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);
			
			endif;
			
		}
		
	}

	new wip_woocarousel_lite_init();

}

require_once dirname(__FILE__) . '/core/functions.wip-woocarousel-lite.php';

?>