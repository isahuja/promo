<?php

/**
 * Wp in Progress
 * 
 * @package Wordpress
 * @theme Sueva
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available at this URL: http://www.gnu.org/licenses/gpl-3.0.txt
 */

/*-----------------------------------------------------------------------------------*/
/* IS WOOCOMMERCE ACTIVE */
/*-----------------------------------------------------------------------------------*/ 

if ( ! function_exists( 'is_woocommerce_active' ) ) {
	
	function is_woocommerce_active( $type = "" ) {
	
        global $woocommerce;

        if ( isset( $woocommerce ) ) {
			
			if ( !$type || call_user_func($type) ) {
            
				return true;
			
			}
			
		}
	
	}

}

/*-----------------------------------------------------------------------------------*/
/* SETTINGS */
/*-----------------------------------------------------------------------------------*/ 

if (!function_exists('wip_woocarousel_lite_setting')) {

	function wip_woocarousel_lite_setting($id, $default = "" ) {
	
		$wip_woocarousel_setting = get_option("wip_woocarousel_settings");
		
		if(isset($wip_woocarousel_setting[$id])):
		
			return $wip_woocarousel_setting[$id];
		
		else:
		
			return $default;
		
		endif;
	
	}

}

/*-----------------------------------------------------------------------------------*/
/* POSTMETA */
/*-----------------------------------------------------------------------------------*/ 

if (!function_exists('wip_woocarousel_lite_postmeta')) {

	function wip_woocarousel_lite_postmeta($id, $default = "" ) {
	
		global $post, $wp_query;
		
		$content_ID = $post->ID;
	
		if( is_woocommerce_active('is_shop') ) {
	
			$content_ID = get_option('woocommerce_shop_page_id');
	
		}

		$val = get_post_meta( $content_ID , $id, TRUE);
		
		if(isset($val)) {
			
			return $val;
			
		} else {
				
			return '';
			
		}
	
	}

}

?>