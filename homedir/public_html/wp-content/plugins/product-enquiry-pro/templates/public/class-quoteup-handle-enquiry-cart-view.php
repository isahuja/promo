<?php

namespace Templates\Frontend;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/*
 * Handle the view of approval and Rejection of Quote
 */

class QuoteupHandleEnquiryCartView
{
    /**
     * @var Singleton The reference to *Singleton* instance of this class
     */
    private static $instance;

    /**
     * Returns the *Singleton* instance of this class.
     *
     * @return Singleton The *Singleton* instance.
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    protected function __construct()
    {
        add_action('quoteup_enquiry_cart_content', array($this, 'enquiryCartView'));
    }

    /**
     * This function is used to enqueue scripts
     */
    public function enqueueScripts()
    {
        global $quoteup;
        $form_data = quoteupSettings();

        if (isset($form_data[ 'enable_disable_mpe' ]) && $form_data[ 'enable_disable_mpe' ] == 1) {
            $url = get_permalink($form_data[ 'mpe_cart_page' ]);

            wp_enqueue_script('quoteup-quote-cart', QUOTEUP_PLUGIN_URL.'/js/public/quote-cart.js', array('jquery', 'jquery-ui-draggable'), time(), true);
            wp_enqueue_script('quoteup-cart-responsive', QUOTEUP_PLUGIN_URL.'/js/public/responsive-table.js', array('jquery', 'jquery-ui-draggable'), time(), true);

            $redirect_url = $quoteup->displayQuoteButton->getRedirectUrl($form_data);

            $data = getLocalizationDataForJs($redirect_url);

            wp_localize_script('quoteup-quote-cart', 'wdm_data', $data);

            unset($url);
        }
        wp_enqueue_script('phone_validate', QUOTEUP_PLUGIN_URL.'/js/public/phone-format.js', array('jquery'), false, true);
        wp_enqueue_style('quoteup_responsive', QUOTEUP_PLUGIN_URL.'/css/public/responsive-style.css', false);
        if (isset($form_data['enable_google_captcha']) && $form_data['enable_google_captcha'] == 1) {
            wp_register_script('google-captcha', 'https://www.google.com/recaptcha/api.js', array(), QUOTEUP_VERSION);
            wp_enqueue_script('google-captcha');
        }
    }

    /**
     * This function prints cart head.
     *
     * @return [type] [description]
     */
    public function getCartHead($form_data)
    {
        $str = "<thead>
                                  <tr class='cart_item cart_header'>
                                     <th class='product-remove cart-remove'>&nbsp;</th>
                                     <th class='product-thumbnail cart-thumbnail'>&nbsp;</th>
                                     <th class='product-name cart-name'>".__('Product', QUOTEUP_TEXT_DOMAIN)."</th>
                                     <th class='product-price quote-cart-price'>".__('Price', QUOTEUP_TEXT_DOMAIN)."</th>
                                     <th class='product-quantity cart-quantity'>".__('Quantity', QUOTEUP_TEXT_DOMAIN).'</th>';
        if (isset($form_data[ 'enable_disable_quote' ]) && $form_data[ 'enable_disable_quote' ] == 0) {
            $str .= "<th class='product-subtotal cart-subtotal'>".__('Expected Price', QUOTEUP_TEXT_DOMAIN);
        } else {
            $str .= "<th class='product-subtotal cart-subtotal'>".__('Remarks', QUOTEUP_TEXT_DOMAIN);
        }
        $str = apply_filters('quoteup_enquiry_cart_table_head', $str);
        echo $str .= '</th></tr></thead>';
    }

    /**
     * This function is used to get css settings
     * @param  [array] $form_data [settings stored in database]
     * @return [int]            [1 if manual css is selected]
     */
    public function getCssSettings($form_data)
    {
        $manual_css = '';
        if (isset($form_data[ 'button_CSS' ]) && $form_data[ 'button_CSS' ] == 'manual_css') {
            $manual_css = 1;
        }

        return $manual_css;
    }

    /**
     * This function is used to get image URL
     * @param  Array $product product details in cart session
     * @return String          Image URL
     */
    public function getImageURL($product)
    {
        $img_content = '';
        if (isset($product[ 'variation_id' ]) && $product[ 'variation_id' ] != '') {
            $img_content = wp_get_attachment_url(get_post_thumbnail_id($product[ 'variation_id' ]));
        }
        if (!$img_content || $img_content == '') {
            $img_content = wp_get_attachment_url(get_post_thumbnail_id($product[ 'id' ]));
        }
        if (!$img_content || $img_content == '') {
            $img_content = WC()->plugin_url().'/assets/images/placeholder.png';
        }

        return $img_content;
    }

    /**
     * This function is used to get total price to be displayed
     * Returns '-' if enable price is disabled
     * @param  String $current_status 'yes' if price is enabled
     * @param  Array $product        Product details in cart page
     * @return String                 price to be displayed
     */
    public function getTotalPrice($current_status, $product)
    {
        if ($current_status == 'yes') {
            $totalPrice = $product['price'];
            $totalPrice = wc_price($totalPrice * $product[ 'quant' ]);
        } else {
            $totalPrice = '-';
        }

        return $totalPrice;
    }

    /**
     * This function is used to get placehoder for remarks textarea
     * @param  Array $form_data Settings stored in database
     * @return String            Placeholder to be displayed
     */
    public function getPlaceholder($form_data)
    {
        if (isset($form_data[ 'enable_disable_quote' ]) && $form_data[ 'enable_disable_quote' ] == 0) {
            $placeholder = __('Expected price and remarks', QUOTEUP_TEXT_DOMAIN);
        } else {
            $placeholder = __('Remarks', QUOTEUP_TEXT_DOMAIN);
        }

        return $placeholder;
    }

    /**
     * This function is used to display cart
     */
    public function enquiryCartView()
    {
        @session_start();
        $this->enqueueScripts();
        if ($_SESSION[ 'wdm_product_count' ] > 0) {
            $manual_css = '';
            $default_vals = array('show_after_summary' => 1,
                'button_CSS' => 0,
                'pos_radio' => 0,
                'show_powered_by_link' => 0,
                'enable_send_mail_copy' => 0,
                'enable_telephone_no_txtbox' => 0,
                'only_if_out_of_stock' => 0,
                'dialog_product_color' => '#3079ED',
                'dialog_text_color' => '#000000',
                'dialog_color' => '#F7F7F7',
            );
            $form_data = get_option('wdm_form_data', $default_vals);
            $manual_css = $this->getCssSettings($form_data);
            echo "
                <div class='quoteup-quote-cart'>
                    <div class='woocommerce wdm-quoteup-woo'>
                        <div class='error-quote-cart' id='error-quote-cart'></div>
                            <table class='shop_table cart generated_for_desktop wdm_shop_tbl wdm-quote-cart-table' cellspacing='0'>";
            $this->getCartHead($form_data);
            echo '<tbody>';
            foreach ($_SESSION[ 'wdm_product_info' ] as $product) {
                    $current_status = get_post_meta($product[ 'id' ], '_enable_price', true);
                    $img_content = $this->getImageURL($product);
                    $totalPrice = $this->getTotalPrice($current_status, $product);
                    $url = get_permalink($product[ 'id' ]);
                    $abc = htmlspecialchars(json_encode($product['variation']), ENT_QUOTES, 'UTF-8');
                    echo $str = '<tr class=\'cart_item cart_product\'>
            <td class="product-remove"><a href="#" class="remove" data-product_id="'.$product[ 'id' ].'" data-variation_id="'.$product['variation_id'].'" data-variation = \''.$abc.'\'>&times;</a>
            </td>
            <td class="product-thumbnail">
            <img width="180" height="180" src="'.$img_content.'" class="attachment-shop_thumbnail size-shop_thumbnail wp-post-image"  sizes="(max-width: 180px) 100vw, 180px" />

            </td>
            <td class="product-name"><a href="'.$url.'">'.get_the_title($product[ 'id' ]).'</a>';
                    echo printVariations($product);
                    echo $str = "</td>
            <td class='product-price'>".$totalPrice."</td>
            <td class='product-quantity'><div class='quantity wdm-quantity'><input type='number' min='1' step='1' data-product_id='{$product[ 'id' ]}' data-variation_id='{$product['variation_id']}' data-variation = '".$abc."'
                name='wdm_product_quantity' value='{$product[ 'quant' ]}' class='wdm-prod-quant input-text qty '></div>
            </td>";

                    $placeholder = $this->getPlaceholder($form_data);
                    echo $str = "<td class='product-subtotal'><textarea placeholder='$placeholder' rows='2' cols='5' class='wdm-remark' data-product_id='{$product[ 'id' ]}'>{$product[ 'remark' ]}</textarea>
            </td>
        </tr>";
            }

            $ajax_nonce = wp_create_nonce('nonce_for_enquiry');
            $url = admin_url();
            $optionData = quoteupSettings();
            $currentLocale = getCurrentLocale();
            if (isset($optionData['enable_disable_quote']) && $optionData['enable_disable_quote'] == 1) {
                echo $str = "<tr>
                <td colspan='6' class='td-btn-update'><input type='button' class='update wdm-update' value='".__('Update Enquiry Cart', QUOTEUP_TEXT_DOMAIN)."'><span class='load-ajax'></span></td>
                </tr>";
            } else {
                echo $str = "<tr>
                <td colspan='6' class='td-btn-update'><input type='button' class='update wdm-update' value='".__('Update Enquiry & Quote Cart', QUOTEUP_TEXT_DOMAIN)."'><span class='load-ajax'></span></td>
                </tr>";
            }
            do_action('quoteup_enquiry_cart_table_body');
            echo $str = "</tbody>

                </table>
                </div>
                <div class='wdm-enquiry-form'>
                <h4 class='wdm-enquiry-form-title'>
                ".__('Requestor Info', QUOTEUP_TEXT_DOMAIN)."
                </h4>
                   <form method='post' id='frm_mpe_enquiry' name='frm_enquiry' class='wdm-mpe-form' >

                        <input type='hidden' name='mpe_ajax_nonce' id='mpe_ajax_nonce' value='{$ajax_nonce}'>
                        <input type='hidden' name='wdmLocale' id='wdmLocale' value='{$currentLocale}'>
                        <input type='hidden' name='submit_value' id='submit_value'>

                        <input type='hidden' name='site_url' id='site_url' value='{$url}'>
                        <input type='hidden' name='tried' id='tried' value='yes' />
                        <!--<div class='ck_msg wdm-enquiry-form-indication'><sup class='req'>*</sup> ".__('Indicates required fields', QUOTEUP_TEXT_DOMAIN)."</div>-->
                        <div id='error' class='error' >
                        </div>
                        <div id='wdm_nonce_error'>
                            <div  class='wdmquoteup-err-display'>
                                <span class='wdm-quoteupicon wdm-quoteupicon-exclamation-circle'></span><?php _e('Unauthorized enquiry', QUOTEUP_TEXT_DOMAIN) ?>
                            </div>
                        </div>";
            do_action('mpe_add_custom_field_in_form');

            $enable_mc = '';
            if (isset($form_data[ 'enable_send_mail_copy' ])) {
                $enable_mc = $form_data[ 'enable_send_mail_copy' ];
            }
            if ($enable_mc == 1) {
                echo "<div class='ck mpe_form_input'><div class='mpe-left' style='height: 1px;'></div>";
                echo "<label class='mpe-right contact-cc-wrap'";
                if (isset($dialogue_text_color)) {
                    echo " style=' color: ".$dialogue_text_color.";'";
                }
                echo "><input type='checkbox' id='contact-cc'  name='cc' value='yes' /><span class='contact-cc-txt'>".__('Send me a copy', QUOTEUP_TEXT_DOMAIN).'</span></label>';
                echo '</div>';
            }
            $this->getCapchaField($form_data);
            echo "
    <div class='form_input btn_div wdm-enquiryform-btn-wrap clearfix'>
                            <div class='mpe-left' style='height: 1px;'></div>
                                <div class='mpe-right'>

                            <input type='submit' value='".__('Send', QUOTEUP_TEXT_DOMAIN)."
                                ' name='btnSend'  id='btnMPESend' class='button_example'";

            if ($manual_css == 1) {
                echo getManualCSS($form_data);
            }
            $url = get_permalink(get_option('woocommerce_shop_page_id'));
            echo ">
    <span class='load-send-quote-ajax'></span>
          </div>
          </div>
          <div class='form-errors-wrap mpe_form_input' id='wdm-quoteupform-error'>
            <div class='mpe-left' style='height: 1px;'></div>
            <div class='mpe-right form-errors'>
                <ul class='error-list'>
                </ul>
            </div>
          </div>
         </form>
        </div>
        </div>
        <div class='success' id='success'><div class='wdm-enquiry-success'><span class='wdm-quoteupicon wdm-quoteupicon-done wdm-enquiry-success-icon'></span>".__('Enquiry sent successfully!', QUOTEUP_TEXT_DOMAIN)."</div><div class='woocommerce'><p class='return-to-shop'><a class='button wc-backward' href='{$url}'>".__('Return To Shop', QUOTEUP_TEXT_DOMAIN).'</a></p></div></div>';
        } else {
            $url = get_permalink(get_option('woocommerce_shop_page_id'));
            $optionData = quoteupSettings();
            if (isset($optionData['enable_disable_quote']) && $optionData['enable_disable_quote'] == 1) {
                echo "<div class='woocommerce'>
    <p class='cart-empty'>".__('Your enquiry cart is currently empty.', QUOTEUP_TEXT_DOMAIN)."</p>
    <p class='return-to-shop'><a class='button wc-backward' href='{$url}'>".__('Return To Shop', QUOTEUP_TEXT_DOMAIN).'</a></p>
    </div>';
            } else {
                echo "<div class='woocommerce'>
    <p class='cart-empty'>".__('Your enquiry and quotation cart is currently empty.', QUOTEUP_TEXT_DOMAIN)."</p>
    <p class='return-to-shop'><a class='button wc-backward' href='{$url}'>".__('Return To Shop', QUOTEUP_TEXT_DOMAIN).'</a></p>
    </div>';
            }
        }
    }

    public function getCapchaField($form_data)
    {
        if (isset($form_data['enable_google_captcha']) && $form_data['enable_google_captcha'] == 1) {
                $siteKey = $form_data[ 'google_site_key' ];

        ?>
        <div class="captcha mpe_form_input">
            <div class='mpe-left' style='height: 1px;'>
            </div>
            <div class='mpe-right'>
                <div class="g-recaptcha" data-sitekey="<?php echo $siteKey; ?>">
                </div>
            </div>
        </div>
        <?php
        }
    }
}

$quoteupHandleEnquiryCartView = QuoteupHandleEnquiryCartView::getInstance();
