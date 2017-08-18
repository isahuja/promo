<?php

namespace Templates\Frontend;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/*
 * Handle the view of approval and Rejection of Quote
 */

class QuoteupHandleQuoteApprovalRejectionView
{
    public $isProductDeleted = false;

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

    /**
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
     */
    protected function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'));
        add_action('quoteup_approval_rejection_content', array($this, 'approvalRejectionView'));
    }

    /**
     * This function is used to enqueue scripts
     */
    public function enqueueScripts()
    {
        wp_register_script('quoteup-frontend-script', QUOTEUP_PLUGIN_URL.'/js/public/reject-quote-button-animation.js', array(
            'jquery', ));
        wp_register_style('quoteup-quote-table', QUOTEUP_PLUGIN_URL.'/css/public/quote-table-on-approval-rejection-page.css');
        wp_enqueue_script('quoteup-cart-responsive', QUOTEUP_PLUGIN_URL.'/js/public/responsive-table.js', array('jquery', 'jquery-ui-draggable'), time(), true);
        wp_enqueue_style('quoteup_responsive', QUOTEUP_PLUGIN_URL.'/css/public/responsive-style.css', false);

        $form_data = quoteupSettings();

        if (isset($form_data[ 'user_custom_css' ])) {
            wp_add_inline_style('quoteup_responsive', $form_data[ 'user_custom_css' ]);
        }
    }

    /**
     * This function is used to get custom css
     * @param  Array $form_data Settings stored in database
     * @return String            Css stored in database
     */
    public function getCustomCSS($form_data)
    {
        $customCSS = '';
        if (isset($form_data[ 'button_CSS' ]) && $form_data[ 'button_CSS' ] == 'manual_css') {
            $customCSS = getManualCSS($form_data);
        }

        return $customCSS;
    }

    /**
     * Display view of Approval/Rejection functionality.
     */
    public function approvalRejectionView()
    {
        global $quoteup, $quoteup_enough_stock, $quoteup_enough_stock_product_id, $quoteup_enough_stock_variation_details;
        wp_enqueue_script('quoteup-frontend-script');
        wp_enqueue_style('quoteup-quote-table');
        $result = \Includes\QuoteupOrderQuoteMapping::getOrderIdOfQuote(0);
        if ($result != null || $result === 0) {
            echo '<div style="color:red;" class="quoteup-error">';
            echo apply_filters('quoteup_quote_expired_text', __('The quotation has expired.', QUOTEUP_TEXT_DOMAIN));
            echo '</div>';

            return;
        }
        if (!$quoteup_enough_stock) {
            echo '<div style="color:red;" class="quoteup-error">';
            echo sprintf(__("'%s%s' is not in stock. Contact site admin to make this order", QUOTEUP_TEXT_DOMAIN), get_the_title($quoteup_enough_stock_product_id), $quoteup_enough_stock_variation_details);
            echo '</div>';
            $quoteup->wcCartSession->unsetSession();
            if (WC()->cart->get_cart_contents_count() !== 0) {
                $replace_order = new \WC_Cart();
                $replace_order->empty_cart(true);
            }

            return;
        }

        $quotation_check = $quoteup->wcCartSession->get('quotationProducts');
        if ($quotation_check) {
            echo '<div style="color:red;" class="quoteup-error">';
            _e('An earlier session seems to be in progress ', QUOTEUP_TEXT_DOMAIN);
            echo '</div>';

            return;
        }


        if (isset($_POST[ '_quoteupApprovalRejectionNonce' ]) &&
        !empty($_POST[ '_quoteupApprovalRejectionNonce' ])) {
            if (isset($quoteup->quoteApprovalRejection->isQuoteRejected) && $quoteup->quoteApprovalRejection->isQuoteRejected) {
                $enquiry_id = explode('_', $_GET[ 'quoteupHash' ]);
                $enquiry_id = $enquiry_id[ 0 ];
                $order = 0;
                $form_data = quoteupSettings();

                \Includes\QuoteupOrderQuoteMapping::updateOrderIDOfQuote($enquiry_id, $order);
                echo empty($form_data[ 'reject_message' ]) ? _e('Quote Rejected', QUOTEUP_TEXT_DOMAIN) : $form_data[ 'reject_message' ];
                $customCSS = $this->getCustomCSS($form_data);
                ?>
                <p class="quoteup-return-to-shop">
                    <a class="wc-backward" href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop')));
                ?>">
                    <button <?php echo $customCSS;
                ?>><?php _e('Return To Shop', QUOTEUP_TEXT_DOMAIN) ?></button>
                    </a>
               </p>
                <?php
                return;
            }
        }
        //Show form which shows Approve and Reject button.
        $this->displayApprovalRejectionForm();
    }

    /**
     * Displays either a form asking for email id associated with enquiry or  buttons to approve/reject quote. Approve/Reject actions are handled using POST. Email id form's handling is done using GET.
     */
    public function displayApprovalRejectionForm()
    {
        global $quoteup;
        $emailAddressFailed = false;
        if (isset($_GET[ 'quoteupHash' ])) {
            if (isset($_GET[ 'enquiryEmail' ])) {
                $email = $_GET[ 'enquiryEmail' ];
                //validate email address entered by user
                if (!filter_var($email, FILTER_VALIDATE_EMAIL) !== false) {
                    $emailAddressFailed = $email;
                } else {
                    //If email address entered by user is correct, show him/her buttons to accept or reject a quote
                    if ($quoteup->quoteApprovalRejection->checkIfEmailIsValid($_GET[ 'enquiryEmail' ], $_GET[ 'quoteupHash' ]) !== 0) {
                        $enquiry_id = getEnquiryIdFromHash($_GET[ 'quoteupHash' ]);
                        //Check if Quote is Expired
                        if ($quoteup->manageExpiration->isQuoteExpired($enquiry_id)) {
                            echo '<div style="color: red;">';
                            echo apply_filters('quoteup_quote_expired_text', __('The quotation has expired.', QUOTEUP_TEXT_DOMAIN));
                            echo '</div>';

                            return;
                        }
                        //Show Quotation sent to user
                        $this->displayQuotation();
                        if ($this->isProductDeleted) {
                            return;
                        }

                        $form_data = quoteupSettings();
                        //Show Approve button Label
                        if (isset($form_data['approve_custom_label']) && !empty($form_data['approve_custom_label'])) {
                            $approveButtonLabel = $form_data['approve_custom_label'];
                        } else {
                            $approveButtonLabel = __('Approve Quote', QUOTEUP_TEXT_DOMAIN);
                        }
                        //Show Reject button Label
                        if (isset($form_data['reject_custom_label']) && !empty($form_data['reject_custom_label'])) {
                            $rejectButtonLabel = $form_data['reject_custom_label'];
                        } else {
                            $rejectButtonLabel = __('Reject Quote', QUOTEUP_TEXT_DOMAIN);
                        }
                        $customCSS = $this->getCustomCSS($form_data);

                        ?>
                        <form  action="" method="POST">
                            <input type="hidden" name="_quoteupApprovalRejectionNonce" value="<?php echo wp_create_nonce('approveRejectionNonce');
                        ?>">
                            <input type="hidden" name="quoteupHash" value="<?php echo $_GET[ 'quoteupHash' ] ?>">
                            <input type="hidden" name="enquiryEmail" value="<?php echo $_GET[ 'enquiryEmail' ] ?>">

                            <input <?php echo $customCSS; ?> type="submit" class="button approve-quote-button" name="approvalQuote" value=" <?php echo apply_filters('quoteup_approve_quote_text', $approveButtonLabel) ?>">
                            <a href="#" class="reject-quote-button"> <?php echo $rejectButtonLabel ?></a>
                            <div class="quote-rejection-reason-div">
                                <textarea class="quote-rejection-reason-textbox" name="quoteRejectionReason" placeholder="<?php echo apply_filters('quoteup_add_quote_rejection_reason_placeholder', __('Please add your reason behind rejecting the quote', QUOTEUP_TEXT_DOMAIN));
                        ?>"></textarea>
                                <input <?php echo $customCSS;
                        ?> type="submit" class="button reject-quote-button-with-message" name="rejectQuote" value=" <?php echo apply_filters('quoteup_reject_quote_text', __('Reject This Quote', QUOTEUP_TEXT_DOMAIN));
                        ?>">
                            </div>
                        </form>
                        <?php
                    } else {
                        //email id entered by user does not match with the email id associated with enquiry
                        $emailAddressFailed = $_GET[ 'enquiryEmail' ];
                    }
                }
            } else {
                $emailAddressFailed = true;
            }
            //Either email address is blank, invalid or does not match with the one associated with enquiry. Hence show email address form again.
            if ($emailAddressFailed !== false) {
                if ($emailAddressFailed !== true) {
                    echo '<div style="color: red;">';
                    echo sprintf(__('%s is not a valid email address or Quotation has been updated. Please contact site admin for new Quotation mail %s', QUOTEUP_TEXT_DOMAIN), "<strong> <code>$emailAddressFailed</code>", '</strong>');
                    echo '</div>';
                }
                $this->displayEmailAddressField();
            }
        }
    }

    /**
     * Displays email address form.
     */
    public function displayEmailAddressField()
    {
        ?>
        <div class="enquiry-email-address">
            <p><strong><?php _e('Please provide your email address associated with the quote:', QUOTEUP_TEXT_DOMAIN) ?> </strong></p>
            <form action="" method="GET">
        <?php
        $currentPermalinkStructure = get_option('permalink_structure', '');
        if (empty($currentPermalinkStructure)) {
            ?>
                    <input type="hidden" name="page_id" value="<?php echo $_GET[ 'page_id' ] ?>" />
                    <?php
        }
        if (quoteupIsWpmlActive() && isset($_GET['lang'])) {
            ?>
                <input type="hidden" name="lang" value="<?php echo $_GET[ 'lang' ] ?>" />
        <?php
        }
        ?>
                <input type="email" class="enquiry-email-address-textfield" name="enquiryEmail" placeholder="test_email@address.com" value=""/>
                <input type="hidden" name="quoteupHash" value="<?php echo $_GET[ 'quoteupHash' ] ?>" />
                <input type="submit" class="button enquiry-email-address-button" name="emailAddressSubmit" value="<?php _e('Submit', QUOTEUP_TEXT_DOMAIN) ?>">    
            </form>
        </div>
        <?php
    }

    /**
     * This function is used to display expiration date.
     * @param  Int $enquiry_id Enquiry ID
     */
    public function includeExpirationDate($enquiry_id)
    {
        global $quoteup;
        $expiration_date = $quoteup->manageExpiration->getExpirationDate($enquiry_id);
        if (!empty($expiration_date)) {
            ?>
        <div class="expiration-info">
            <div class="expiration-title">
                <?php _e('Expiration Date', QUOTEUP_TEXT_DOMAIN);
            ?>
            </div>
            <div class="expiration-data">
                <?php
                echo $expiration_date.'<br>';
            ?>
            </div>
        </div>
        <div class="clear"></div>
        <?php
        }
    }

    /**
     * This function is used to create table head
     * @param  String $show_price yes if price needs to be displayed
     */
    public function getHead($show_price)
    {
        ?>
        <thead>
            <tr>
        <?php

        if ($show_price == 'yes') {
            ?>
                    <th class="product-price"><?php _e('Product', QUOTEUP_TEXT_DOMAIN);
            ?></th>
                    <th class="sku-price"><?php _e('Sku', QUOTEUP_TEXT_DOMAIN);
            ?></th>
                    <th><?php _e('Old', QUOTEUP_TEXT_DOMAIN);
            ?></th>
                        <?php
        } else {
            ?>
                    <th class="product"><?php _e('Product', QUOTEUP_TEXT_DOMAIN);
            ?></th>
                    <th class="sku"><?php _e('Sku', QUOTEUP_TEXT_DOMAIN);
            ?></th>
            <?php
        }
        ?>

                    <th><?php _e('New', QUOTEUP_TEXT_DOMAIN);
        ?></th>
                    <th><?php _e('Qty', QUOTEUP_TEXT_DOMAIN);
        ?></th>
                    <th><?php _e('Amount', QUOTEUP_TEXT_DOMAIN);
        ?></th>
                </tr>
        </thead>
        <?php
    }

    /**
     * This function is used to check if product is available
     * @param  Array $quoteProduct Quote Product details
     * @return boolean              true if product is available
     */
    public function getProductStatus($quoteProduct)
    {
        if ($quoteProduct['variation_id'] != 0) {
            $productAvailable = isProductAvailable($quoteProduct[ 'variation_id' ]);
        } else {
            $productAvailable = isProductAvailable($quoteProduct[ 'product_id' ]);
        }

        return $productAvailable;
    }

    /**
     * Displays quotation Which was sent to that user.
     *
     * @return [type] [description]
     */
    public function displayQuotation()
    {
        global $wpdb;
        $enquiry_tbl = getEnquiryDetailsTable();
        $quotation_tbl = getQuotationProductsTable();
        $enquiry_id = explode('_', $_GET[ 'quoteupHash' ]);
        $enquiry_id = $enquiry_id[ 0 ];
        $enquiry_details = $wpdb->get_row($wpdb->prepare("SELECT product_details, name, email FROM $enquiry_tbl WHERE enquiry_id = %d", $enquiry_id));
        $quotation = $wpdb->get_results($wpdb->prepare("SELECT * FROM $quotation_tbl WHERE enquiry_id = %d", $enquiry_id), ARRAY_A);
        $products = unserialize($enquiry_details->product_details);
        $quoteupSettings = quoteupSettings();
        $company_name = $quoteupSettings['company_name'];
        $company_address = $quoteupSettings['company_address'];
        $admin_mail = get_option('admin_email');

        $name = $enquiry_details->name;

        $mail = $enquiry_details->email;
        $show_price = $quotation[ 0 ]['show_price'];
        ob_start();
        ?>
        <div id='header'>
            <div class="PDFLogo">
                <h2> <?php _e('Quote', QUOTEUP_TEXT_DOMAIN) ?> </h2>
            </div>
            <div class="content">
                <!-- <h2> Quote </h2> -->
                <div class="from-info">
                    <div class="from-title">
        <?php _e('From', QUOTEUP_TEXT_DOMAIN) ?>
                    </div>
                    <div class="from-data">
        <?php
        echo $company_name.'<br>';
        echo $company_address.'<br>';
        echo $admin_mail.'<br>';
        ?>

                    </div>
                </div>
                <div class="clear"></div>
                <div class="to-info">
                    <div class="to-title">
        <?php _e('Quote For', QUOTEUP_TEXT_DOMAIN) ?>
                    </div>
                    <div class="to-data">
                        <?php
                        echo $name.'<br>';
                        echo $mail.'<br>';
        ?>
                    </div>

                </div>
                <div class="clear"></div>
                <?php
                $this->includeExpirationDate($enquiry_id);

        ?>
            </div>
        </div>


        <div id="head"><h3> <?php _e('Quote Request', QUOTEUP_TEXT_DOMAIN) ?> #<?php echo "$enquiry_id";
        ?> </h3></div>
        <div id="Enquiry">
            <table align="center" class="quoteup-quote-table generated_for_desktop">
            <?php
            $this->getHead($show_price);
            $products = unserialize($enquiry_details->product_details);
            $total_price = 0;

            foreach ($quotation as $quoteProduct) {
                $productAvailable = $this->getProductStatus($quoteProduct);
                if ($productAvailable) {
                    $_product = wc_get_product($quoteProduct[ 'product_id' ]);
                    $sku = $this->getSku($_product, $quoteProduct['variation_id']);
                } else {
                    $this->isProductDeleted = true;
                    break;
                }
                    $price = $quoteProduct['oldprice'];
                    ?>
                                <tr>
                                <?php
                                if ($show_price == 'yes') {
                                    ?>
                            <td class="product-price" align="left"><?php
                                echo get_the_title($quoteProduct[ 'product_id' ]);
                            if ($_product->is_type('variable')) {
                                $variationArray = unserialize($quoteProduct['variation']);
                                quoteupRemoveClassFilter('woocommerce_attribute_label', 'WCML_WC_Strings', 'translated_attribute_label');
                                global $sitepress;
                                remove_filter('get_term', array($sitepress, 'get_term_adjust_id'), 1);
                                echo printVariations($quoteProduct);
                            }
                                    ?>
                                    </td>
                                    <?php
                                } else {
                                    ?>
                                    <td class="product" align="left"><?php
                                    echo get_the_title($quoteProduct[ 'product_id' ]);
                                    if ($_product->is_type('variable')) {
                                        $variationArray = unserialize($quoteProduct['variation']);
                                        quoteupRemoveClassFilter('woocommerce_attribute_label', 'WCML_WC_Strings', 'translated_attribute_label');
                                        global $sitepress;
                                        remove_filter('get_term', array($sitepress, 'get_term_adjust_id'), 1);
                                        echo printVariations($quoteProduct);
                                    }
                                    ?>
                                    </td>
                                    <?php
                                }
                                if ($show_price == 'yes') {
                                        ?>
                                                            <td class="sku-price"><?php echo $sku;
                                                        ?></td>
                                                            <?php
                                } else {
                                        ?>
                                                            <td class="sku"><?php echo $sku;
                                                        ?></td>
                                    
                                                            <?php
                                }
                                if ($show_price == 'yes') {
                                        ?>
                                        <td align="left"><?php echo wc_price($price);
                                        ?></td>
                                        <?php
                                }
                    ?>
                                    <td align="left"><?php echo wc_price($quoteProduct['newprice']);
                    ?></td>
                                    <td align="left"><?php echo $quoteProduct['quantity'];
                    ?></td>
                                    <td align="right"><?php
                                    echo wc_price($quoteProduct['newprice'] * $quoteProduct['quantity']);
                                    $total_price = $total_price + $quoteProduct['newprice'] * $quoteProduct['quantity'];
                    ?></td>
                                </tr>
                                        <?php
            }
        ?>
                <tr border="1">
                <?php
                if ($show_price == 'yes') {
                    ?>
                        <td colspan="5" class="final-total"><?php _e('TOTAL', QUOTEUP_TEXT_DOMAIN);
                    ?></td>
                        <?php
                } else {
                    ?>
                    <td colspan="4" class="final-total"><?php _e('TOTAL', QUOTEUP_TEXT_DOMAIN);
                    ?></td>
                    <?php
                }
        ?>
                    <td align="center" ><?php echo wc_price($total_price);
        ?></td>
                </tr>
            </table>
        </div>
        <div class='wdm_notes'>
        <?php _e('TAX', QUOTEUP_TEXT_DOMAIN);
        ?>:               <?php _e('Quote does not include default store tax', QUOTEUP_TEXT_DOMAIN);
        ?>.<br>
        <?php _e('SHIPPING', QUOTEUP_TEXT_DOMAIN);
        ?>:          <?php _e('Quote does not include shipping', QUOTEUP_TEXT_DOMAIN);
        ?>.<br>
        </div>        <!-- </pre> -->


            <?php
            $html = ob_get_clean();
            if ($this->isProductDeleted) {
                echo '<div style="color: red;">';
                _e('One or more products from the quotation have been deleted from the store and are currently unavailable. Please contact site admin for more information.', QUOTEUP_TEXT_DOMAIN);
                echo '</div>';
            } else {
                echo $html;
            }
    }

    /**
     * This function is used to get SKU of product
     * @param  object  $product      Object of product
     * @param  integer $variation_id Variation ID
     * @return String                SKU of the product
     */
    public function getSku($product, $variation_id = 0)
    {
        $sku = '';
        if ($product->is_type('variable')) {
            $_variableProduct = wc_get_product($variation_id);
            $sku = $_variableProduct->get_sku();
            if (empty($sku)) {
                $sku = $product->get_sku();
            }
        } else {
            $sku = $product->get_sku();
        }

        return $sku;
    }
}

    $quoteupViewQuotationApprovalRejection = QuoteupHandleQuoteApprovalRejectionView::getInstance();
