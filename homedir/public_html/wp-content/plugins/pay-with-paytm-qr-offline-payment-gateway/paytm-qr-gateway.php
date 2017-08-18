<?php

// Make sure WooCommerce is active
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))))
    return;

/*
  Plugin Name: Paytm QR Gateway
  Description: Provides an Offline Payment Gateway using Paytm QR code.Display your paytm QR code on your website and get payment direcly into your paytm wallet.
  Version: 1.0.0
  Author: Smit Raval
  License: GPL2
 */

add_action('plugins_loaded', 'wc_offline_gateway_init', 11);

function wc_offline_gateway_init() {

    class WC_Gateway_Paytm_QR extends WC_Payment_Gateway {

        /**
         * Init and hook in the integration.
         */
        function __construct() {
            global $woocommerce;
            $this->id = "paytm-qr";
            $this->has_fields = false;
            $this->method_title = __("Paytm QR", 'woocommerce-paytm-qr');
            $this->method_description = "Provides an Offline Payment Gateway using Paytm QR code.Display your paytm QR code on your website and get payment direcly into your paytm wallet.";

            //Initialize form methods
            $this->init_form_fields();
            $this->init_settings();

            // Define user set variables.
            $this->title = $this->settings['title'];
            $this->description = $this->settings['description'];
            $this->instructions = $this->settings['instructions'];
            $this->paytm_qr_url = $this->settings['paytm_qr_url'];

            if (version_compare(WOOCOMMERCE_VERSION, '2.0.0', '>=')) {
                add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
                add_action('woocommerce_thankyou_' . $this->id, array($this, 'thankyou_page'));
            } else {
                add_action('woocommerce_update_options_payment_gateways', array(&$this, 'process_admin_options'));
                add_action('woocommerce_thankyou', array(&$this, 'thankyou_page'));
            }
            // Customer Emails
            add_action('woocommerce_email_before_order_table', array($this, 'email_instructions'), 10, 3);
        }

        // Build the administration fields for this specific Gateway
        public function init_form_fields() {
            $this->form_fields = array(
                'enabled' => array(
                    'title' => __('Enable/Disable', 'woocommerce-paytm-qr'),
                    'type' => 'checkbox',
                    'label' => __('Enable Paytm QR', 'woocommerce-paytm-qr'),
                    'default' => 'no'
                ),
                'title' => array(
                    'title' => __('Title', 'woocommerce-paytm-qr'),
                    'type' => 'text',
                    'description' => __('This controls the title for the payment method the customer sees during checkout.', 'woocommerce-paytm-qr'),
                    'default' => __('Pay with Paytm QR', 'woocommerce-paytm-qr'),
                    'desc_tip' => true,
                ),
                'description' => array(
                    'title' => __('Description', 'woocommerce-paytm-qr'),
                    'type' => 'textarea',
                    'description' => __('Payment method description that the customer will see on your checkout.', 'woocommerce-paytm-qr'),
                    'default' => __('Make your payment directly using paytm QR code.Please use your Order ID as a payment reference.', 'woocommerce-paytm-qr'),
                    'desc_tip' => true,
                ),
                'instructions' => array(
                    'title' => __('Instructions', 'woocommerce-paytm-qr'),
                    'type' => 'textarea',
                    'description' => __('Instructions that will be added to the thank you page and emails.', 'woocommerce-paytm-qr'),
                    'default' => "Make your payment directly into our paytm wallet by scanning the QR code. Please use your Order ID as the payment reference. Your order won't be shipped until the funds have cleared in our paytm.",
                    'desc_tip' => true,
                ),
                'paytm_qr_url' => array(
                    'title' => __('Paytm QR Image URL*', 'woocommerce-paytm-qr'),
                    'type' => 'textarea',
                    'description' => __('Upload paytm QR to your media library and Provide the URL here.', 'woocommerce-paytm-qr'),
                    'default' => "Paytm QR code image Url, to be displayed on Thank you page!!",
                    'desc_tip' => true,
                ),
            );
        }

        public function validate_paytm_qr_url_field($key, $value) {
            if (isset($value)) {
                if (filter_var($value, FILTER_VALIDATE_URL) === FALSE) {
                    WC_Admin_Settings::add_error(esc_html__('Please enter a valid Paytm Qr URL.This image will be displayed on Thank you page to recieve payments.', 'woocommerce-paytm-qr'));
                }
            }

            return $value;
        }

        public function process_payment($order_id) {

            $order = wc_get_order($order_id);

            // Mark as on-hold (we're awaiting the payment)
            $order->update_status('on-hold', __('Awaiting offline payment', 'woocommerce-paytm-qr'));

            // Reduce stock levels
            $order->reduce_order_stock();

            // Remove cart
            WC()->cart->empty_cart();

            // Return thankyou redirect
            return array(
                'result' => 'success',
                'redirect' => $this->get_return_url($order)
            );
        }

        /**
         * Output for the order received page.
         */
        public function thankyou_page() {
            if ($this->instructions) {
                echo wpautop(wptexturize($this->instructions)). PHP_EOL;
            }
            if ($this->paytm_qr_url) {
                echo "<br/>Please scan this QR code using paytm app in your mobile and make payment.<b> Please add order number in 'What is it for?' tab in paytm app while making payment.</b>";
                echo "<div class='qr_image_class'><img src='" . $this->paytm_qr_url . "' alt='paytm_qr_code' /></div>";
                echo "<style>.qr_image_class{width:100%;display:block;padding:10px;} .qr_image_class > img{display:block;margin:0 auto;}</style>";
            }
        }

        /**
         * Add content to the WC emails.
         *
         * @access public
         * @param WC_Order $order
         * @param bool $sent_to_admin
         * @param bool $plain_text
         */
        public function email_instructions($order, $sent_to_admin, $plain_text = false) {
            if ($this->instructions && !$sent_to_admin && 'paytm-qr' === $order->payment_method && $order->has_status('on-hold')) {
                if ($this->instructions) {
                echo wpautop(wptexturize($this->instructions));
            }
            if ($this->paytm_qr_url) {
                echo "<br/>Please scan this QR code using paytm app in your mobile and make payment.<b> Please add order number in 'What is it for?' tab in paytm app while making payment.</b>". PHP_EOL;
                echo "<div class='qr_image_class'><img src='" . $this->paytm_qr_url . "' alt='paytm_qr_code' /></div>". PHP_EOL;
                echo "<style>.qr_image_class{width:100%;display:block;padding:10px;} .qr_image_class > img{display:block;margin:0 auto;}</style>";
            }
            }
        }

    }

    // Now that we have successfully included our class,
    // Lets add it too WooCommerce
    add_filter('woocommerce_payment_gateways', 'add_paytm_qr');

    function add_paytm_qr($methods) {
        $methods[] = 'WC_Gateway_Paytm_QR';
        return $methods;
    }

}
