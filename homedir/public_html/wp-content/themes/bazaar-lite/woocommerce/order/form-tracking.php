<?php
/**
 * Order tracking form
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;

?>

<form action="<?php echo esc_url( get_permalink( $post->ID ) ); ?>" method="post" class="track_order">

	<p><?php esc_html_e( 'To track your order please enter your Order ID in the box below and press the "Track" button. This was given to you on your receipt and in the confirmation email you should have received.', 'bazaar-lite' ); ?></p>

	<p class="form-row form-row-first"><label for="orderid"><?php esc_html_e( 'Order ID', 'bazaar-lite' ); ?></label> <input class="input-text" type="text" name="orderid" id="orderid" placeholder="<?php esc_attr_e( 'Found in your order confirmation email.', 'bazaar-lite' ); ?>" /></p>
	<p class="form-row form-row-last"><label for="order_email"><?php esc_html_e( 'Billing Email', 'bazaar-lite' ); ?></label> <input class="input-text" type="text" name="order_email" id="order_email" placeholder="<?php esc_attr_e( 'Email you used during checkout.', 'bazaar-lite' ); ?>" /></p>
	<div class="clear"></div>

	<p class="form-row"><input type="submit" class="button" name="track" value="<?php esc_attr_e( 'Track', 'bazaar-lite' ); ?>" /></p>
	<?php wp_nonce_field( 'woocommerce-order_tracking' ); ?>

</form>
