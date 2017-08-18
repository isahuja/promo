<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if( !class_exists( 'wip_woocarousel_lite_admin_notice' ) ) {

	class wip_woocarousel_lite_admin_notice {
	
		/**
		 * Constructor
		 */
		 
		public function __construct( $fields = array() ) {

			if ( !get_user_meta( get_current_user_id(), 'wip_woocarousel-lite_notice_userid_' . get_current_user_id() , TRUE ) ) {

				add_action( 'admin_notices', array(&$this, 'admin_notice') );
				add_action( 'admin_head', array( $this, 'dismiss' ) );
			
			}

		}

		/**
		 * Dismiss notice.
		 */
		
		public function dismiss() {
		
			if ( isset( $_GET['wip_woocarousel-lite-dismiss'] ) ) {
		
				update_user_meta( get_current_user_id(), 'wip_woocarousel-lite_notice_userid_' . get_current_user_id() , $_GET['wip_woocarousel-lite-dismiss'] );
				remove_action( 'admin_notices', array(&$this, 'admin_notice') );
				
			} 
		
		}

		/**
		 * Admin notice.
		 */
		 
		public function admin_notice() {
			
		?>
			
            <div class="update-nag notice wip-woocarousel-lite-notice">
            
            	<div class="wip-woocarousel-lite-noticedescription">
					<strong><?php _e( 'Upgrade to the premium version of WIP WooCarousel, to enable 5 different layouts, 600+ Google Fonts, unlimited colors and much more.', 'wip-woocarousel-lite' ); ?></strong><br/>
					<?php printf( '<a href="%1$s" class="dismiss-notice">'. __( 'Dismiss this notice', 'wip-woocarousel-lite' ) .'</a>', esc_url( '?wip_woocarousel-lite-dismiss=1' ) ); ?>
                </div>
                
                <a target="_blank" href="<?php echo esc_url( 'https://www.themeinprogress.com/wip-woocarousel-woocommerce-slider-carousel/?ref=2&campaign=wip-woocarousel-notice' ); ?>" class="button"><?php _e( 'Upgrade to the premium version.', 'wip-woocarousel-lite' ); ?></a>
                <div class="clear"></div>

            </div>
		
		<?php
		
		}

	}

}

new wip_woocarousel_lite_admin_notice();

?>