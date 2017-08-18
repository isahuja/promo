<?php

namespace Includes\Admin;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('Abstracts\QuoteupEdit')) {
    require_once QUOTEUP_PLUGIN_DIR.'/includes/admin/abstracts/class-abstract-quoteup-edit.php';
}

class QuoteupQuotesEdit extends Abstracts\QuoteupEdit
{

    protected static $instance = null;
    public $enquiry_details = null;

    /**
     * Function to create a singleton instance of class and return the same.
     *
     * @return object -Object of the class
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct($displayNames = array())
    {
        $settings = quoteupSettings();
        if (!isset($settings['enable_disable_quote']) || $settings['enable_disable_quote'] == 0) {
            parent::__construct();
            add_action('admin_enqueue_scripts', array($this, 'addQuotesScript'));
            add_action('wp_ajax_save_quotation', array($this, 'saveQuotationAjaxCallback'));
            add_action('wp_ajax_action_pdf', array('Includes\Admin\QuoteupGeneratePdf', 'generatePdfAjaxCallback'));
            add_action('wp_ajax_action_send', array('Includes\Admin\SendQuoteMail', 'sendMailAjaxCallback'));
            add_action('wp_ajax_get_last_history_data', array($this, 'getLastUpdatedHistoryRow'));
            add_action('wp_ajax_get_last_version_data', array($this, 'getLastUpdatedVersionRow'));
            add_action('quoteup-edit-heading', array($this, 'displayQuoteHeading'));
            add_action('quoteup_after_customer', array($this, 'displayQuoteProductDetails'));
            add_action('PEDetailEdit', array($this, 'addMetaBoxAfterMessage'));
        }
    }

    public function addMetaBoxAfterMessage($enquiry_details)
    {
        do_action('quoteup_edit_details', $enquiry_details);
    }

    public function addQuotesScript($hook)
    {
        if ('admin_page_quoteup-details-edit' != $hook) {
            return;
        }
        global $wp_scripts;

        $this->includeWooCommerceScripts();
        wp_enqueue_script('quoteup-ajax', QUOTEUP_PLUGIN_URL.'/js/admin/ajax.js', array(
                'jquery', 'jquery-ui-core', 'jquery-effects-highlight', ));

         // get registered script object for jquery-ui
        $uiVersion = $wp_scripts->query('jquery-ui-core');
        // tell WordPress to load the Smoothness theme from Google CDN
        $protocol = is_ssl() ? 'https' : 'http';
        $url = "$protocol://ajax.googleapis.com/ajax/libs/jqueryui/{$uiVersion->ver}/themes/smoothness/jquery-ui.min.css";
        wp_enqueue_style('jquery-ui-smoothness', $url, false, null);
        wp_enqueue_style('jquery-ui-datepicker', QUOTEUP_PLUGIN_URL.'/css/admin/datepicker.css');

        wp_enqueue_style('wdm_quote_responsive_css', QUOTEUP_PLUGIN_URL.'/css/admin/quote-edit-responsive.css');

        $lastGeneratedPDFExists = false;
        if (isset($_GET[ 'id' ]) && intval($_GET[ 'id' ])) {
            $upload_dir = wp_upload_dir();
            if (file_exists($upload_dir[ 'basedir' ].'/QuoteUp_PDF/'.$_GET[ 'id' ].'.pdf')) {
                $lastGeneratedPDFExists = true;
            }
        }

        $dateTime = date_create_from_format('Y-m-d H:i:s', current_time('Y-m-d H:i:s'));
        $form_data = quoteupSettings();
        $pdfDisplay = isset($form_data['enable_disable_quote_pdf'])?$form_data['enable_disable_quote_pdf']:1;

        wp_localize_script(
            'quoteup-ajax',
            'quote_data',
            array(
                    'decimal_separator' => wc_get_price_decimal_separator(),
                    'thousand_separator' => wc_get_price_thousand_separator(),
                    'decimals' => wc_get_price_decimals(),
                    'price_format' => get_woocommerce_price_format(),
                    'currency_symbol' => get_woocommerce_currency_symbol(),
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'path' => WP_CONTENT_URL.'/uploads/QuoteUp_PDF/',
                    'save' => __('Saving Data', QUOTEUP_TEXT_DOMAIN),
                    'generatePDF' => __('Generating PDF', QUOTEUP_TEXT_DOMAIN),
                    'errorPDF' => sprintf(__(
                            'Please select the Approval/Rejection page %s here %s to create quote', QUOTEUP_TEXT_DOMAIN),"<a href='admin.php?page=quoteup-for-woocommerce#wdm_quote'>",
                        '</a>'
                    ),
                    'generatedPDF' => __('PDF Generated', QUOTEUP_TEXT_DOMAIN),
                    'otherProductType' => __('Product types other than simple and variable cannot be added to quote', QUOTEUP_TEXT_DOMAIN),
                    'deletedProduct' => __('Deleted Products cannot be added to quote', QUOTEUP_TEXT_DOMAIN),
                    'sendmail' => __('Sending Mail', QUOTEUP_TEXT_DOMAIN),
                    'quantity_less_than_0' => __('Total Quantity can not be less than or equal to 0', QUOTEUP_TEXT_DOMAIN),
                    'pdf_generation_aborted' => __('PDF generation process aborted due to security issue.', QUOTEUP_TEXT_DOMAIN),
                    'data_update_aborted' => __('Data update aborted due to security issue.', QUOTEUP_TEXT_DOMAIN),
                    'saved_successfully' => __('Saved Successfully', QUOTEUP_TEXT_DOMAIN),
                    'data_updated' => __('Customer Data updated.', QUOTEUP_TEXT_DOMAIN),
                    'quantity_invalid' => __('Quantity can not be in decimal.', QUOTEUP_TEXT_DOMAIN),
                    'data_not_updated_name' => __('Enter valid name. Customer Data not updated.', QUOTEUP_TEXT_DOMAIN),
                    'data_not_updated_email' => __('Enter valid email address. Customer Data not updated.', QUOTEUP_TEXT_DOMAIN),
                    'invalid_variation' => __('Please select a valid variation for', QUOTEUP_TEXT_DOMAIN),
                    'same_variation' => __('Same variation of a product cannot be added twice.', QUOTEUP_TEXT_DOMAIN),
                    'lastGeneratedPDFExists' => $lastGeneratedPDFExists,
                    'todays_date' => apply_filters(
                        'quoteup_human_readable_expiration_date',
                        date_format(
                            $dateTime,
                            'M d, Y'
                        ),
                        $dateTime
                    ),
                    'quote_expired' => __('Quote can not be saved because it is already expired. Kindly, change the date to future date.', QUOTEUP_TEXT_DOMAIN),
                    'save_and_preview_quotation' => __('Generate Quotation', QUOTEUP_TEXT_DOMAIN),
                    'preview_quotation' => __('Preview Quotation', QUOTEUP_TEXT_DOMAIN),
                    'save_and_send_quotation' => __('Send Quotation', QUOTEUP_TEXT_DOMAIN),
                    'send_quotation' => __('Send Quotation', QUOTEUP_TEXT_DOMAIN),
                    'PDF' => $pdfDisplay,
                    )
        );

        wp_enqueue_script('bootstrap-modal', QUOTEUP_PLUGIN_URL.'/js/admin/bootstrap-modal.js', array(
                'jquery', ), false, true);
        wp_enqueue_style('modal_css1', QUOTEUP_PLUGIN_URL.'/css/wdm-bootstrap.css', false, false);
    }

     /*
     * Include WooCommerce's add-to-cart-variation.js which is used by WooCommerce on Frontend
     * (On variable product) to get appropriate variations from database and filter values
     * in variations dropdown
     * 
     * Mimic the way WooCommerce handles variation dropdown. Therefore enqueing the script
     * it requires and localizing scripts with object names which are used in add-to-cart-variation.js
     * 
     * This was figured out after studying woocommerce/includes/class-wc-frontend-scripts.php
     */
    public function includeWooCommerceScripts()
    {
        $assets_path = str_replace(array('http:', 'https:'), '', WC()->plugin_url()).'/assets/';
        $frontend_script_path = $assets_path.'js/frontend/';
        wp_enqueue_script('wc-add-to-cart-variation', $frontend_script_path.'add-to-cart-variation.js', array(
                'jquery', 'wp-util', ));

        quoteupGetAdminTemplatePart('variation');

        $wc_ajax_url = '';

        /*
         * For WooCommerce below 2.6, there is no method like get_endpoint in WC_AJAX class
         */
        if (method_exists('\WC_AJAX', 'get_endpoint')) {
            $wc_ajax_url = \WC_AJAX::get_endpoint('%%endpoint%%');
        }

        wp_localize_script('wc-add-to-cart-variation', 'wc_cart_fragments_params', array(
                'ajax_url' => WC()->ajax_url(),
                'wc_ajax_url' => $wc_ajax_url,
                'fragment_name' => apply_filters('woocommerce_cart_fragment_name', 'wc_fragments'),
                ));

        wp_localize_script('wc-add-to-cart-variation', 'wc_add_to_cart_variation_params', array(
                'i18n_no_matching_variations_text' => esc_attr__('Sorry, no products matched your selection. Please choose a different combination.', 'woocommerce'),
                'i18n_make_a_selection_text' => esc_attr__('Please select some product options before adding this product to your cart.', 'woocommerce'),
                'i18n_unavailable_text' => esc_attr__('Sorry, this product is unavailable. Please choose a different combination.', 'woocommerce'),
                ));
    }

    public function displayQuoteHeading($enquiry_details)
    {
        global $quoteupManageHistory;
        $enquiryId = $enquiry_details['enquiry_id'];
        $quoteStatus = $quoteupManageHistory->getLastAddedHistory($enquiryId);
        if ($quoteStatus != null && is_array($quoteStatus)) {
            $quoteStatus = $quoteStatus[ 'status' ];
        }
        $statusArray = array(
            'Quote Created' => __('Quote Created', QUOTEUP_TEXT_DOMAIN),
            'Requested' => __('Requested', QUOTEUP_TEXT_DOMAIN),
            'Saved' => __('Saved', QUOTEUP_TEXT_DOMAIN),
            'Sent' => __('Sent', QUOTEUP_TEXT_DOMAIN),
            'Approved' => __('Approved', QUOTEUP_TEXT_DOMAIN),
            'Rejected' => __('Rejected', QUOTEUP_TEXT_DOMAIN),
            'Order Placed' => __('Order Placed', QUOTEUP_TEXT_DOMAIN),
            'Expired' => __('Expired', QUOTEUP_TEXT_DOMAIN),
            );

        echo __('Quotation Details', QUOTEUP_TEXT_DOMAIN).' #'.$enquiryId;
            ?> <span class="quote-status-span"><?php echo empty($quoteStatus) ? 'New' : $statusArray[$quoteStatus];
            ?></span>
            <?php
    }

    /**
     * Create Meta box with heading "Product Details".
     *
     * @return [type] [description]
     */
    public function displayQuoteProductDetails($enquiryDetailsRecieved)
    {
        global $quoteup_admin_menu;
        $this->enquiry_details = $enquiryDetailsRecieved;
        add_meta_box('editProductDetailsData', __('Product Details', QUOTEUP_TEXT_DOMAIN), array($this, 'productDetailsSection'), $quoteup_admin_menu, 'normal');
        do_action('quoteup_after_product_details', $this->enquiry_details);
    }

    public function productDetailsSection()
    {
        ?>
        <div class="wdmpe-detailtbl-wrap">
        <?php echo $this->quotationTable();
        ?>
        </div>
        <?php        
    }

    public function quotationTable()
    {
        $enquiryId = filter_var($_GET[ 'id' ], FILTER_SANITIZE_NUMBER_INT);
        $quoteProducts = getQuoteProducts($enquiryId);
        $quotationDownload = $this->getQuotationStatus($quoteProducts);
        if (empty($quoteProducts)) {
            $quoteProducts = getEnquiryProducts($enquiryId);
        }
        $deletedProducts = array();
        $variableProducts = array();
        $result = \Includes\QuoteupOrderQuoteMapping::getOrderIdOfQuote($_GET[ 'id' ]);
        $quotationDisabled = '';
        $quotationbTN = '';
        $inputOff = '';
        if ($result != null && $result != 0) {
            $quotationDisabled = 'disabled';
            $quotationbTN = "style='display:none'";
            $inputOff = "style='border: none; color: #505560;'";
        }
        ob_start();
        ?>
        <table class='wdm-tbl-prod wdmpe-detailtbl wdmpe-quotation-table admin-quote-table' id="Quotation">
            <?php $this->getQuoteTableHead();
        ?>
            <tbody class="wdmpe-detailtbl-content">
        <?php
        $email = $this->enquiry_details['email'];
        $count = 0;
        $total_price = 0;
        $excludedProducts = "";
        foreach ($quoteProducts as $singleProduct) {
            $varProduct = '';
            $disableInputboxes = '';
            $disableCheckBox = false;
            $strike = '';
            $productDisabled = '';
            $deletedClass = '';
            $productId = $singleProduct[ 'product_id' ];
            $img_url = $this->getImageURL($singleProduct);
            $url = admin_url("/post.php?post={$productId}&action=edit");

                // If it is variable product then check if variaion is available
            if (isset($singleProduct['variation_id']) && $singleProduct['variation_id'] != 0) {
                $productAvailable = isProductAvailable($singleProduct['variation_id']);
                $product = wc_get_product($singleProduct['variation_id']);
                $tempVariation = unserialize($singleProduct['variation']);
                $tempVariation = sanitizeVariationLabel($singleProduct['variation_id'], $tempVariation);
                $encryptedID = generateProductHash($productId, $singleProduct['variation_id'], $tempVariation);
            } else {
                //Check avaiblity for simple product
                $productAvailable = isProductAvailable($productId);
                $product = wc_get_product($productId);
                $encryptedID = md5($productId);
            }
            if ($excludedProducts != "") {
                $excludedProducts = $excludedProducts . ',' . $encryptedID;
            } else {
                $excludedProducts = $encryptedID;
            }
            ++$count;
                //If product is available get latest data from database
            if ($productAvailable) {
                $sku = $product->get_sku();
                $ProductTitle = "<a href='".$url."' target='_blank' id='product-title-".$count."'>".get_the_title($productId).'</a>';
            } else {
                //If product is not available show old data and disabled
                $strike = '';
                $productDisabled = 'disabled';
                $sku = "";
                $ProductTitle = get_the_title($productId);
                $deletedClass = 'deleted-product';
                ob_start();
            }

            $sku = $this->getSkuValue($sku);
            $price = isset($singleProduct[ 'oldprice' ]) ? $singleProduct[ 'oldprice' ] : $singleProduct[ 'price' ];
            $product_object = wc_get_product($productId);
            $productType = $product_object->get_type();
            if ($productType != 'simple' && $productType != 'variable') {
                $disableInputboxes = 'disabled';
                $disableCheckBox = 'quote-disableCheckBox';
                $singleProduct[ 'checked' ] = '';
            }
            ?>
                            <tr class="wdmpe-detailtbl-content-row <?php echo $deletedClass .' '. $disableCheckBox; ?>" data-row-num="<?php echo $count; ?>">

                                <td class="quote-product-remove">
                                    <a href="#" class="remove" data-row-num="<?php echo $count; ?>" data-id="<?php echo $encryptedID; ?>" data-product_id="<?php echo $productId; ?>" data-variation_id="<?php echo $singleProduct['variation_id']; ?>" data-variation="">×</a>
                                </td>

                            <td class="wdmpe-detailtbl-content-item item-content-img">
                                <img src= '<?php echo $img_url;
            ?>' class='wdm-prod-img'/>
                            </td>
                            <td class="wdmpe-detailtbl-content-item item-content-link">
                                <?php echo $ProductTitle;
            ?>
                            </td>
                            <?php
                            $singleProduct['variation'] = unserialize($singleProduct['variation']);
                             $this->getQuoteVariationsColumn($count, $productId, $quotationbTN, $singleProduct, $productAvailable, $singleProduct);
            ?>
                            <td class="wdmpe-detailtbl-content-item item-content-sku">
                                <?php echo $sku;
            ?>
                            </td>
                            <td class="wdmpe-detailtbl-content-item item-content-old-cost" data-old_price="<?php echo $this->oldPriceData($singleProduct, $count - 1) ?>">
                                    <?php echo wc_price($price);
            ?>
                                <input type="hidden" id="old-price-<?php echo $count ?>" value="<?php echo $price;
            ?>" <?php echo $productDisabled;
            ?> >
                            </td>
                            <td class="wdmpe-detailtbl-content-item item-content-newcost">
                            <?php
                            if ($disableCheckBox) {
                                echo '-';
                            } else {
                                ?>
                        <input id="content-new-<?php echo $count ?>" data-row-num="<?php echo $count;
                                ?>" class="newprice <?php echo $varProduct;
                                ?>" type="number" name="newprice" value="<?php echo isset($singleProduct['newprice']) ? $singleProduct['newprice'] : $singleProduct['price'];
                                ?>" min="0" <?php
                                echo $quotationDisabled.' '.$inputOff;
                                echo ' '.$productDisabled.' '.$disableInputboxes;
                                ?> step="any" >
                        <?php

                            }

            ?>
                            </td>
                            <td class="wdmpe-detailtbl-content-item item-content-qty" >
                                <input data-row-num="<?php echo $count; ?>" id="content-qty-<?php echo $count ?>" class="newqty <?php echo ' '.$varProduct;?>" type="number" name="newqty" value="<?php echo $singleProduct['quantity']; ?>" min="0" <?php echo $quotationDisabled.' '.$productDisabled.' '.$disableInputboxes.' '.$inputOff; ?> >
                            </td>
                            <td class="wdmpe-detailtbl-content-item item-content-cost" id="content-cost-<?php echo $count ?>">
                                        <?php
                                        $this->printCost($disableInputboxes, $singleProduct, $productDisabled, $total_price);
            ?>
                            </td>
                    <input <?php echo $strike ?> data-row-num="<?php echo $count; ?>" id="content-amount-<?php echo $count ?>" class="amount_database" type="hidden" name="price" value="<?php $this->getDatabaseAmount($productDisabled, $disableInputboxes, $singleProduct); ?>">
                    <input data-row-num="<?php echo $count; ?>" id="content-ID-<?php echo $count ?>" class="id_database" type="hidden" name="id" value="<?php echo $productId; ?>">
                    </tr>
                                <?php
                                if (!$productAvailable) {
                                    $deletedRow = ob_get_contents();
                                    ob_end_clean();
                                    array_push($deletedProducts, $deletedRow);
                                }
        }
        $deletedProducts = implode("\n", $deletedProducts);
        $variableProducts = implode("\n", $variableProducts);
        echo $variableProducts;
        echo $deletedProducts;
        ?>
        </tbody>
        <tfoot>
            <tr class="total_amount_row">
            <?php
            if (version_compare(WC_VERSION, '2.6', '>')) {
                ?>
                    <td class="total_span" colspan="6"></td>
                <?php
            } else {
                ?>
                    <td class="total_span" colspan="7"></td>
                <?php
            }
            ?>
                <td  class='wdmpe-detailtbl-head-item amount-total-label' id="amount_total_label"> <?php _e('Total', QUOTEUP_TEXT_DOMAIN);
        ?>  </td>
                <td class="wdmpe-detailtbl-content-item item-content-cost quote-final-total" id="amount_total"> <?php echo wc_price($total_price);
        ?>
                </td>
                    <input type="hidden" name="database-amount" id="database-amount" value=<?php echo $total_price;
        ?>>
            </tr>
        </tfoot>
        </table>
        <?php
        $this->decideButtonOrOrderID($quotationbTN, $quoteProducts, $quotationDownload, $result, $email, $excludedProducts);
        $this->displayAttachments();
        $currentdata = ob_get_contents();
        ob_end_clean();
        return $currentdata;
    }

    /*
     * This function is as a flag for quotation status
     */
    public function getQuotationStatus($quoteProducts)
    {
        $quotationDownload = '';
        if ($quoteProducts == null) {
            $quotationDownload = "style='display:none'";
        }

        return $quotationDownload;
    }

    /**
     * Function to show table head on enquiry details edit page.
     *
     * @return [type] [description]
     */
    public function getQuoteTableHead()
    {
        $img_url = QUOTEUP_PLUGIN_URL.'/images/table_header.png';
        ?>
        <thead class="wdmpe-detailtbl-head">
            <tr class="wdmpe-detailtbl-head-row">
            <!-- <th class="wdmpe-detailtbl-head-item item-head-count">#</th> -->
                <th class="wdmpe-detailtbl-head-item item-head-add-to-quote"></th>
                <th class="wdmpe-detailtbl-head-item item-head-img"><img src= '<?php echo $img_url;
        ?>' class='wdm-prod-img wdm-prod-head-img'/></th>
                <th class="wdmpe-detailtbl-head-item item-head-detail"><?php echo __('Item', QUOTEUP_TEXT_DOMAIN);
        ?> </th>
                <th class="wdmpe-detailtbl-head-item item-head-Variations"><?php echo __('Variations', QUOTEUP_TEXT_DOMAIN);
        ?> </th>
                <th class="wdmpe-detailtbl-head-item item-head-sku"><?php echo __('SKU', QUOTEUP_TEXT_DOMAIN);
        ?></th>
                <th class="wdmpe-detailtbl-head-item item-head-old-cost"><?php echo __('Price', QUOTEUP_TEXT_DOMAIN);
        ?></th>
                <th class="wdmpe-detailtbl-head-item item-head-newcost"><?php echo __('New Price', QUOTEUP_TEXT_DOMAIN);
        ?></th>
                <th class="wdmpe-detailtbl-head-item item-head-qty"><?php echo __('Quantity', QUOTEUP_TEXT_DOMAIN);
        ?></th>
                <th class="wdmpe-detailtbl-head-item item-head-cost"><?php
                echo sprintf(__('Amount( %s )', QUOTEUP_TEXT_DOMAIN), get_woocommerce_currency_symbol());
        ?></th>
            </tr>
        </thead>
        <?php

    }

    /**
     * This function is used to display Variation column on quote edit page.
     *
     * @param [int]     $count                [Row number]
     * @param [int]     $id                   [Product ID]
     * @param [string]  $quotationbTN         [Used as flag]
     * @param [array]   $productData          [description]
     * @param [boolean] $productAvailable     [Used as flag]
     * @param [type]    $prod                 [description]
     *
     * @return [type] [description]
     */
    public function getQuoteVariationsColumn($count, $id, $quotationbTN, $productData, $productAvailable, $prod)
    {
        ?>
        <td class="wdmpe-detailtbl-content-item item-content-variations" data-row-num="<?php echo $count;
        ?>" id="variations-<?php echo $count;
        ?>" data-product-link="<?php echo get_permalink($id);
        ?>">

                        <?php
                        if (isset($productData['variation_id']) && $productData['variation_id'] == 0 && $productData['variation_id'] == '') {
                            echo "-";
                            echo "</td>";
                            return;
                        }
                        //Defining a global variable here because quoteupVariationDropdown() needs a global variable $product
                        $GLOBALS['product'] = wc_get_product($id);
                        /*
                         * Enquiries which are stored till version 4.1.0 do not have
                         * variation data. Therefore, we will first check if variaion_id
                         * exists or not. And if it does not exist, that means it is an
                         * old enquiry
                         */
                        if (!isset($prod[0][ 'variation_id' ]) && $productAvailable) {
                            $GLOBALS[ 'product' ] = wc_get_product($id);
                            /*
                                     * Below WC 2.6, we also need global $post variable because it is used in variable.php
                                     */
                            $wooVersion = WC_VERSION;
                            $wooVersion = floatval($wooVersion);
                            if ($wooVersion < 2.6) {
                                $GLOBALS[ 'post' ] = get_post($id);
                            }
                            //Checking type of Product
                            if ($GLOBALS[ 'product' ]->is_type('variable') && function_exists('quoteupVariationDropdown')) {
                                /*
                                 * Print Dropdowns for Variable Product
                                 */
                                    //Defining a global variable here because quoteupVariationDropdown() needs a global variable $product
                                    $GLOBALS[ 'product' ] = wc_get_product($id);
                                $product = $GLOBALS[ 'product' ];
                                    // Get Available variations?
                                    $get_variations = sizeof($product->get_children()) <= apply_filters('woocommerce_ajax_variation_threshold', 30, $product);
                                $available_variations = $get_variations ? $product->get_available_variations() : array();
                                $attributes = $product->get_variation_attributes();
                            /*
                             *we are using quoteupVariationDropdown() instead of woocommerce_variable_add_to_cart(). quoteupVariationDropdown() is just a copy of woocommerce_variable_add_to_cart() loading our template instead of woocommerce variable.php
                             *
                                     * woocommerce_variable_add_to_cart() includes woocommerce/templates/single-product/add-to-cart/variable.php. This file has a form tag and dropdowns are shown in a form tag. Since we are already inside a table, form tag can not be used here and therefore, we are creating a div tag which is very similar to form tag created in variable.php
                                     */
                                    ?>
                                <div class="product" <?php echo $quotationbTN; ?>>
                                        <div id="variation-<?php echo $count ?>" class="variations_form cart" data-product_id="<?php echo absint($id);
                                ?>" data-product_variations="<?php echo htmlspecialchars(json_encode($available_variations)) ?>">
                                    <?php         
                                    add_filter('woocommerce_locate_template', array($this, 'changeVariableTemplatePath'), 10, 2);
                                    quoteupVariationDropdown($count, '', '', $id, $product, $productData[ 'variation' ]);
                                    remove_filter('woocommerce_locate_template', array($this, 'changeVariableTemplatePath'), 10);
                                ?>
                                        </div>
                                    </div>
                                    <?php
                                    //This Block is displayed when order is completed for that enquiry.
                                    if ($quotationbTN != '') {
                                        ?>
                                        <div>
                                        <?php
                                        echo printVariations($productData);
                                        ?>                                                    
                                       </div>
                                        <?php

                                    }
                            } else {
                                echo '-';
                            }
                        } /*
                                 * Starting with version 4.1.0, QuoteUp stores variation_id and variation
                                 * details for all products. For simple products variation_id is blank or
                                 * null. For variable products, proper data is available
                                 */ elseif (isset($prod[0][ 'variation_id' ]) && $prod[0][ 'variation_id' ] !== '' && $prod[0][ 'variation_id' ] !== '0' && function_exists('quoteupVariationDropdown')) {
                                    /*
                                     * Print Dropdowns for Variable Product
                                     */
                            if ($productAvailable) {
                                //Defining a global variable here because quoteupVariationDropdown() needs a global variable $product
                                $GLOBALS[ 'product' ] = wc_get_product($id);
                                /*
                                         * Below WC 2.6, we also need global $post variable because it is used in variable.php
                                         */
                                $wooVersion = WC_VERSION;
                                $wooVersion = floatval($wooVersion);
                                if ($wooVersion < 2.6) {
                                    $GLOBALS[ 'post' ] = get_post($id);
                                }
                                $product = $GLOBALS[ 'product' ];
                                // Get Available variations?
                                $get_variations = sizeof($product->get_children()) <= apply_filters('woocommerce_ajax_variation_threshold', 30, $product);
                                $available_variations = $get_variations ? $product->get_available_variations() : array();
                                $attributes = $product->get_variation_attributes();
                                /*
                                 * we are using quoteupVariationDropdown() instead of woocommerce_variable_add_to_cart(). quoteupVariationDropdown() is just a copy of woocommerce_variable_add_to_cart() loading our template instead of woocommerce variable.php
                                 *
                                         * woocommerce_variable_add_to_cart() includes woocommerce/templates/single-product/add-to-cart/variable.php. This file has a form tag and dropdowns are shown in a form tag. Since we are already inside a table, form tag can not be used here and therefore, we are creating a div tag which is very similar to form tag created in variable.php
                                         */
                                ?>
                                <div class="product" <?php echo $quotationbTN ?>>
                                    <div id="variation-<?php echo $count ?>" class="variations_form cart" data-product_id="<?php echo absint($id);
                                ?>" data-product_variations="<?php echo htmlspecialchars(json_encode($available_variations)) ?>">
                                <?php
                                        quoteupVariationDropdown($count, '', '', $id, $product, $productData[ 'variation' ]);
                                ?>
                                    </div>
                                        </div>
                                        <?php
                            } else {
                                ?>
                                            <div>
                                                <?php
                                                echo printVariations($productData);
                                            ?>
                                            </div>
                                            <?php
                            }
     ?>
                                        <?php
                                        if ($quotationbTN != '' && $productAvailable) {
                                            ?>
                                        
                                            <div>
                                                <?php
                                                echo printVariations($productData);
                                            ?>
                                            </div>
                                            <?php

                                        }
 } /*
                                 * For Simple products, variation data is not available and hence print
                                 * blank string
                                 */ else {
     echo '-';
 }
        ?>
                            </td>
        <?php
    }

    /*
     * This function is used to display Total column in products detials
     */
    public function printCost($disableInputboxes, $singleProduct, $productDisabled, &$total_price)
    {
        $price = isset($singleProduct['newprice']) ? $singleProduct['newprice'] : $singleProduct['price'];
        if ($disableInputboxes == '') {
            echo wc_price($price * $singleProduct[ 'quantity' ]);
        } else {
            echo '-';
        }
        if ($productDisabled == '' && $disableInputboxes == '') {
            $total_price = $total_price + ($price * $singleProduct[ 'quantity' ]);
        }
    }

    /*
     * This function is used to get amount to be stored in database
     */
    public function getDatabaseAmount($productDisabled, $disableInputboxes, $singleProduct)
    {
        $price = isset($singleProduct['newprice']) ? $singleProduct['newprice'] : $singleProduct['price'];
        if ($productDisabled == '' && $disableInputboxes == '') {
            echo $price * $singleProduct[ 'quantity' ];
        } else {
            echo 0;
        }
    }

    public function changeVariableTemplatePath($template, $template_name)
    {
        if ('single-product/add-to-cart/variable.php' == $template_name) {
            $default_path = WC()->plugin_path().'/templates/';

            return $default_path.$template_name;
        }

        return $template;
    }

    /**
     * This function is used to decide wheather to show buttons or to show order id and disable quotation edit.
     *
     * @param [string] $quotationbTN      [used as a flag]
     * @param [array]  $res               [details stored in enquiry quotation table]
     * @param [string] $quotationDownload [used as a flag]
     * @param [int]    $result            [link status of quotation]
     * @param [string] $email             [customers email id]
     *
     * @return [type] [description]
     */
    public function decideButtonOrOrderID($quotationbTN, $res, $quotationDownload, $result, $email, $excludedProducts)
    {
        global $quoteup, $wpdb;
        $form_data = quoteupSettings();
        $table_name = getEnquiryDetailsTable();
        $pdfStatus = $wpdb->get_var("SELECT pdf_deleted FROM {$table_name} WHERE enquiry_id = {$_GET[ 'id' ]}");
        $addToQuoteBtnStatus = '';
        if ($quotationbTN == '') {
            $checked = '';
            $res[0][ 'show_price' ] = isset($res[0][ 'show_price' ]) ? $res[0][ 'show_price' ] : 'yes';
            if ($res[0][ 'show_price' ] == 'yes' || $res[0][ 'show_price' ] == '' || $res[0][ 'show_price' ] == null) {
                $checked = 'checked';
            }
            ?>
            <div>
            <?php
                getProductsSelection($excludedProducts)
            ?>
                <div class="quote-expiration-date">
                    <?php
                    if (!isset($form_data[ 'enable_disable_quote' ]) || $form_data[ 'enable_disable_quote' ] != 1) {
                        ?>
                        <div class='wdm-user-expiration-date'>
                            <input type='hidden' name='expiration_date' class="expiration_date_hidden" value='<?php echo $this->enquiry_details['expiration_date'];
                        ?>'>
                            <?php
                                $expirationDisabled = '';
                        $result = \Includes\QuoteupOrderQuoteMapping::getOrderIdOfQuote($_GET[ 'id' ]);
                        if ($result != null && $result != 0) {
                            $expirationDisabled = 'disabled';
                        }
                        ?>
                        <label class="expiration-date left-label"><?php _e('Expiration Date', QUOTEUP_TEXT_DOMAIN) ?> : </label>
                                <input type='text' value='<?php echo $quoteup->manageExpiration->getHumanReadableDate($this->enquiry_details['expiration_date']);
                        ?>' class='wdm-input-expiration-date' <?php echo $expirationDisabled ?> readonly  required>
                            </div>
                <?php

                    }
            ?>
                </div>
            </div>
            <div class="quote-options">
                <div class ="show-price-option">
                    <input id="show_price" class="wdm-checkbox" type="checkbox" name="show_price" value= "1" <?php echo $checked;
            ?>  />
                    <label for="show_price"><?php _e('Show Old Price in Quotation', QUOTEUP_TEXT_DOMAIN) ?></label> <br />
                    <!-- <p class="save-quote-note"><em> Note: Saving a Quote after making modifications makes earlier created quote unusable. </em>
                    </p> -->
                                    <?php
                                    $sendQuotationStatus = '';
            if (!empty($this->enquiry_details['expiration_date']) && $this->enquiry_details['expiration_date'] != '0000-00-00 00:00:00') {
                $currentTime = strtotime(current_time('Y-m-d'));
                $expirationTime = strtotime($this->enquiry_details['expiration_date']);
                if ($currentTime > $expirationTime) {
                    $sendQuotationStatus = 'disabled';
                    ?>
                                                <p class="save-quote-note send-quotation-button-disabled-note"><em><strong>To resend the quote please set new expiration date.</strong></em>
                                                </p>
                                            <?php

                }
            }
            ?>

                </div>

                <?php

                if (quoteupIsWpmlActive()) {
                    global $wpdb;
                    $tbl = getEnquiryMetaTable();
                    $sql = "SELECT meta_value FROM {$tbl} WHERE enquiry_id='{$_GET[ 'id' ]}' AND meta_key = 'quotation_lang_code'";
                    $results = $wpdb->get_var($sql);
                    if (isset($form_data['enable_disable_quote_pdf']) && $form_data['enable_disable_quote_pdf']) {
                        $dropdownLabel = __('Quote PDF Language :', QUOTEUP_TEXT_DOMAIN);
                    } else {
                        $dropdownLabel = __('Quote Email Language :', QUOTEUP_TEXT_DOMAIN);
                    }
                    ?>
                    <div class="wpml-language-options">
                        <label class="quote-language left-label"><?php echo $dropdownLabel;  ?> </label>
                        <select class="quoteup-pdf-language wc-language-selector">
                            <?php
                                $activeLanguages = icl_get_languages('skip_missing=0&orderby=code');

                            foreach ($activeLanguages as $code => $value) {
                                $selectedLanguage = '';
                                if ($code == $results) {
                                    $selectedLanguage = 'selected';
                                }
                                $languageName = $value['native_name'].'('.$value['translated_name'].')';
                                echo "<option value='$code' $selectedLanguage>$languageName</option>";
                            }
                    ?>
                        </select>
                    </div>
                    <?php

                }
            ?>
            </div>
            
            <div align="left" class="quotation-related-buttons">
            <?php
            $this->pdfPreviewModal();
            $this->addMessageQuoteModal();
            $upload_dir = wp_upload_dir();
            $path = $upload_dir[ 'baseurl' ].'/QuoteUp_PDF/'.$_GET[ 'id' ].'.pdf';
            $filepath = $upload_dir[ 'basedir' ].'/QuoteUp_PDF/'.$_GET[ 'id' ].'.pdf';
            $preview_button_text = '';
            $send_button_text = '';

            $this->getButtonText($filepath, $preview_button_text, $send_button_text, $pdfStatus);
            ?>
                <?php
                    $this->getQuotationRelatedButtons($filepath, $sendQuotationStatus, $send_button_text, $preview_button_text, $addToQuoteBtnStatus, $path, $quotationDownload);
            ?>
                <input type="hidden" id="enquiry_id" value="<?php echo $_GET['id'];
            ?>">
                <input type="hidden" id="email" value="<?php echo $email;
            ?>">
                <input type="hidden" id="quoteNonce" value="<?php echo wp_create_nonce('quoteup');
            ?>">
            <div class="show-hide-details">
                <button type="button" id="showEnquiry" class="button"><?php _e('Show Orignal Enquiry', QUOTEUP_TEXT_DOMAIN); ?></button>                
            </div>
                <div class="wdm-status-box">
                    <div id="text"></div>
                    <img src="<?php echo admin_url('images/spinner.gif');
            ?>" id="PdfLoad">
                </div>
            </div>
            
                <?php

        } else {
            $link = '<center><h3><label>';
            $link .= __('Order associated with the Quote  : ', QUOTEUP_TEXT_DOMAIN);
            $link .= '</label><label>';
            $link .= '<a href="'.admin_url('post.php?post='.absint($result).'&action=edit').'" >';
            $link .= $result;
            $link .= '</a></label></h3></center>';
            echo $link;
        }
    }

    /*
     * Used to create a modal to display PDF.
     */
    public function pdfPreviewModal()
    {
        ?>
        <div class="wdm-modal wdm-fade wdm-pdf-preview-modal" id="wdm-pdf-preview" tabindex="-1" role="dialog" style="display: none;">
        <div class="wdm-modal-dialog wdm-pdf-modal-dialog">
            <div class="wdm-modal-content wdm-pdf-modal-content" style="background-color:#ffffff">
                <div class="wdm-modal-header">
                    <button type="button" class="close" data-dismiss="wdm-modal" aria-hidden="true">&times;</button>
                    <h4 class="wdm-modal-title" style="color: #333;">
                        <span><?php _e('Quote PDF Preview', QUOTEUP_TEXT_DOMAIN);
        ?></span>
                    </h4>
                </div>
                <div class="wdm-modal-body wdm-pdf-modal-body">
                    <div class="wdm-pdf-body" style="text-align: center;">
                        <iframe class="wdm-pdf-iframe" frameborder="0" vspace="0" hspace="0" webkitallowfullscreen="" mozallowfullscreen="" allowfullscreen="" scrolling="auto"></iframe>
                    </div>
                </div>
                <!--/modal body-->
            </div>
            <!--/modal-content-->
            </div>
            <!--/modal-dialog-->
        </div>

        <?php

    }

    /*
     * This function is used to create a modal to send quote PDF
     */
    public function addMessageQuoteModal()
    {
        $site_name = get_bloginfo();
        $enquiryID = isset($_GET[ 'id' ]) ? $_GET[ 'id' ] : '';
        $form_data = quoteupSettings();
        $defaultText = "";
        $defaultLabel = "";
        if(isset($form_data['enable_disable_quote_pdf']) && $form_data['enable_disable_quote_pdf'] == 1)
        {
            $defaultText = __('This email has the quotation attached for your enquiry', QUOTEUP_TEXT_DOMAIN);
            $defaultLabel = __('The quotation PDF will be attached to this email', QUOTEUP_TEXT_DOMAIN);
        }
        ?>
        <div class="wdm-modal wdm-fade wdm-quote-modal" id="MessageQuote" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display:none">
            <div class="wdm-modal-dialog">
                <div class="wdm-modal-content" style="background-color:#ffffff">
                    <div class="wdm-modal-header">
                        <button type="button" class="close" data-dismiss="wdm-modal" aria-hidden="true">&times;</button>
                        <h4 class="wdm-modal-title" id="myModalLabel" style="color: #333;">
                            <span class="wdm-quote-heading"><?php
                            _e('Quotation Details #', QUOTEUP_TEXT_DOMAIN);
        echo $enquiryID;
        ?></span>
                        </h4>
                    </div>
                    <div class="wdm-modal-body">
                        <form class="send-quotes-to-customers wdm-quoteup-form form-horizontal">
                            <div class="wdm-quoteup-form-inner">
                                <div class="form_input">
                                    <div class="form-wrap">
                                        <label for="subject"><?php _e('Subject', QUOTEUP_TEXT_DOMAIN); ?>:</label>
                                        <div class="form-wrap-inner">
                                            <input type="text" name="mailsubject" id="subject" size="50" value="<?php echo sprintf(__('Quote Request sent from %s', QUOTEUP_TEXT_DOMAIN), $site_name);
        ?>" required="" placeholder="Subject">
                                        </div>
                                    </div>
                                </div>
                                <div class="row"></div>
                                <div class="form_input">
                                    <div class="form-wrap">
                                        <label for="message"><?php _e('Message', QUOTEUP_TEXT_DOMAIN); ?>:</label>
                                        <div class="form-wrap-inner">
                                            <textarea rows="4" cols="50" id="wdm_message" required=""><?php echo $defaultText; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row wdm-note-row">
                                    <em> <?php echo $defaultLabel; ?> </em>
                                </div>
                                <div class="form_input">
                                    <div class="form-wrap">
                                        <button type="button" class="button button-primary" id="btnSendQuote"><?php _e('Send Quote', QUOTEUP_TEXT_DOMAIN);
        ?></button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="row send-row">
                            <div id="txt" style="visibility: hidden;"></div>
                            <img src="<?php echo admin_url('images/spinner.gif');
        ?>" id="Load">
                        </div>
                    </div>
                    <!--/modal body-->
                </div>
                <!--/modal-content-->
            </div>
            <!--/modal-dialog-->
        </div>
        <?php

    }

    /**
     * This function is used to get button text.
     *
     * @param [string] $filepath            [Path of pdf file]
     * @param [string] $preview_button_text [text for preview button]
     * @param [string] $send_button_text    [text for send button]
     *
     * @return [type] [description]
     */
    public function getButtonText($filepath, &$preview_button_text, &$send_button_text, $pdfStatus)
    {
        if (file_exists($filepath)) {
            $preview_button_text = __('Preview Quotation', QUOTEUP_TEXT_DOMAIN);
            $send_button_text = __('Send Quotation', QUOTEUP_TEXT_DOMAIN);
        } elseif ($pdfStatus == 1) {
            $preview_button_text = __('Regenerate PDF', QUOTEUP_TEXT_DOMAIN);
            $send_button_text = __('Send Quotation', QUOTEUP_TEXT_DOMAIN);
        } else {
            $preview_button_text = __('Generate Quotation', QUOTEUP_TEXT_DOMAIN);
            $send_button_text = __('Send Quotation', QUOTEUP_TEXT_DOMAIN);
        }

        $form_data = quoteupSettings();
        $pdfDisplay = isset($form_data['enable_disable_quote_pdf'])?$form_data['enable_disable_quote_pdf']:1;

        if (!$pdfDisplay) {
            $preview_button_text = __('Generate Quotation', QUOTEUP_TEXT_DOMAIN);
            $send_button_text = __('Send Quotation', QUOTEUP_TEXT_DOMAIN);
        }


    }

    /**
     * This function is used to display quote related buttons.
     * It displays send button and download button.
     */
    public function getQuotationRelatedButtons($filepath, $sendQuotationStatus, $send_button_text, $preview_button_text, $addToQuoteBtnStatus, $path, $quotationDownload)
    {
        $displayNone = '';
        $QuoteCreatedDisplayNone = '';
        $QuoteNotCreatedDisplayNone = '';
        $form_data = quoteupSettings();
        $pdfDisplay = isset($form_data['enable_disable_quote_pdf'])?$form_data['enable_disable_quote_pdf']:1;
        $isQuoteCreated = $this->getQuoteCreationStatus();
        if (!$pdfDisplay && !empty($isQuoteCreated)) {
            $QuoteCreatedDisplayNone = "style='display:none'";
        }

        if (!$pdfDisplay && empty($isQuoteCreated)) {
            $QuoteNotCreatedDisplayNone = "style='display:none'";
        }

        if (!file_exists($filepath) &&  $pdfDisplay) {
            $displayNone = "style='display:none'";
        }
        ?>
            <input <?php echo $QuoteCreatedDisplayNone; ?> type="button" id="btnPQuote" class="button" value="<?php echo $preview_button_text ?>" <?php echo $addToQuoteBtnStatus; ?> >
            <input <?php echo $displayNone.$QuoteNotCreatedDisplayNone; ?> id="send" type="button" <?php echo $sendQuotationStatus ?> class="button" value="<?php echo $send_button_text ?>" <?php echo $addToQuoteBtnStatus;
        ?> >
            <a href="<?php echo $path;
        ?>" <?php echo $quotationDownload ?> id="DownloadPDF" download <?php echo $displayNone." ". $QuoteCreatedDisplayNone; ?> ><input id="downloadPDF" type="button" class="button" value="<?php _e('Download PDF', QUOTEUP_TEXT_DOMAIN);
        ?>" ></a>
        <?php

    }

    public function getQuoteCreationStatus()
    {
        global $wpdb;
        $enquiryID = $_GET['id'];
        $tableName = getQuotationProductsTable();
        $sql = "SELECT * FROM $tableName WHERE enquiry_id=$enquiryID";
        return $wpdb->get_results($sql, ARRAY_A);
    }

    /*
     * Set price and variation attribute in old price.
     */
    public function oldPriceData($enquiryData, $rowNumber)
    {
        return htmlspecialchars(json_encode(array(
            'price' => isset($enquiryData['newprice']) ? $enquiryData['newprice'] : $enquiryData['price'],
            'variation' => isset($enquiryData['variation']) ? $enquiryData['variation'] : '',
        )));
    }

    /**
     * Function for inserting data in enquiry_quotation table.
     */
    public static function saveQuotationAjaxCallback()
    {
        $quotationData = $_POST;
        if (!wp_verify_nonce($quotationData[ 'security' ], 'quoteup')) {
            die('SECURITY_ISSUE');
        }
        if (!current_user_can('manage_options')) {
            die('SECURITY_ISSUE');
        }
        self::saveQuotation($quotationData);
        die();
    }

    public static function saveQuotation($quotationData)
    {
        global $wpdb,$quoteup;
        $table_name = getQuotationProductsTable();
        $enquiryTableName = getEnquiryDetailsTable();

        $enquiry_id = filter_var($quotationData[ 'enquiry_id' ], FILTER_SANITIZE_NUMBER_INT);
        $product_id = $quotationData[ 'id' ];
        $newprice = $quotationData[ 'newprice' ];
        $quantity = $quotationData[ 'quantity' ];
        $oldprice = $quotationData[ 'old-price' ];
        $variation_id = $quotationData[ 'variations_id' ];
        $variation_details = $quotationData[ 'variations' ];
        $show_price = $quotationData[ 'show-price' ];
        $variation_index_in_enquiry = $quotationData[ 'variation_index_in_enquiry' ];
        $total_price = 0;
        $size = sizeof($product_id);
        $language = isset($quotationData['language']) ? $quotationData['language'] : '';
        $versionTableName = getVersionTable();
        $sql = $wpdb->prepare("SELECT MAX(version) FROM $versionTableName WHERE enquiry_id=%d", $enquiry_id);
        $currentVersion = $wpdb->get_var($sql);

        //This function is used to check if total quantity is greater than 0
        self::checkValidQuantity($size, $quantity);
        try {
            if (!self::quoteStockManagementCheck($product_id, $quantity, $variation_id, $variation_details)) {
                throw new Exception('Product Cannot be added in quotation', 1);
            }
            //Delete old quotation
            $wpdb->delete(
                $table_name,
                array(
                'enquiry_id' => $quotationData[ 'enquiry_id' ],
                )
            );

            for ($i = 0; $i < $size; ++$i) {
                $productAvailable = wc_get_product($product_id[ $i ]);
                if ($productAvailable == '') {
                    continue;
                }
                $variation_details[ $i ] = self::getQuoteVariationDetails($variation_details[$i]);
                $title = get_the_title($product_id[ $i ]);
                $wpdb->insert(
                    $table_name,
                    array(
                    'enquiry_id' => $enquiry_id,
                    'product_id' => $product_id[ $i ],
                    'product_title' => $title,
                    'newprice' => $newprice[ $i ],
                    'quantity' => $quantity[ $i ],
                    'oldprice' => $oldprice[ $i ],
                    'variation_id' => $variation_id[ $i ],
                    'variation' => serialize($variation_details[ $i ]),
                    'show_price' => $show_price,
                    'variation_index_in_enquiry' => $variation_index_in_enquiry[$i],
                    )
                );
                $total_price += $newprice[ $i ] * $quantity[ $i ];
                $quoteup->displayVersions->addVersion($enquiry_id, $product_id[ $i ], $newprice[ $i ], $quantity[ $i ], $oldprice[ $i ],$variation_id[ $i ], $variation_details[ $i ], $show_price,$variation_index_in_enquiry[$i], $currentVersion);
            }

             //add Locale in enquiry meta if WPML is activated
            if (quoteupIsWpmlActive()) {
                $metaTbl = getEnquiryMetaTable();
                $wpdb->update(
                    $metaTbl,
                    array(
                        'meta_value' => $language,
                    ),
                    array(
                        'enquiry_id' => $enquiry_id,
                        'meta_key' => 'quotation_lang_code',
                    ),
                    array(
                    '%s',
                    ),
                    array(
                        '%d',
                        '%s',
                    )
                );
            }
            //End of locale insertion

            //Save Expiration Date
            self::setExpiry($enquiry_id, $quotationData);

            //Save total price of quotation in enquiry table
            $wpdb->update(
                $enquiryTableName,
                array(
                'total' => $total_price,
                'pdf_deleted' => 0,
                ),
                array(
                'enquiry_id' => $enquiry_id,
                )
            );

            //Set Order Id to NULL, so that it opens up a communication channel after rejecting the Quote
            self::setOrderID($enquiry_id);
            //Don't translate this string here. It's translation is handled in js
            if (!isset($quotationData['source']) || $quotationData['source'] != 'dashboard') {
                echo 'Saved Successfully.';
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        //Delete PDF if already exists
        $uploads_dir = wp_upload_dir();
        $base_uploads = $uploads_dir[ 'basedir' ].'/QuoteUp_PDF/';
        if ($enquiry_id != 0 && file_exists($base_uploads.$enquiry_id.'.pdf')) {
            unlink($base_uploads.$enquiry_id.'.pdf');
        }
        //update History Table
        global $quoteupManageHistory;
        if (!isset($quotationData[ 'previous_enquiry_id' ]) || $quotationData[ 'previous_enquiry_id' ] != 0) {
            $quoteupManageHistory->addQuoteHistory($enquiry_id, '-', 'Saved');
        }
    }

    /**
     * This function is used to check if quantity is greater than 0.
     *
     * @param [type] $size     [description]
     * @param [type] $quantity [description]
     *
     * @return [type] [description]
     */
    public static function checkValidQuantity($size, $quantity)
    {
        $finalQuant = 0;
        for ($i = 0; $i < $size; ++$i) {
            $finalQuant += $quantity[ $i ];
        }
        if ($finalQuant == 0) {
            _e('Total quantity is 0. Quotation is same as orignal quantity and price', QUOTEUP_TEXT_DOMAIN);
            die();
        }
    }

     /*
     * This function is used to check stock of products to be added in quote
     */
    public static function quoteStockManagementCheck($product_ids, $quantities, $variation_ids, $variationDetails)
    {
        $size = sizeof($product_ids);
        for ($i = 0; $i < $size; ++$i) {
            $product_id = absint($product_ids[$i]);
            $variation_id = absint($variation_ids[$i]);
            $quantity = $quantities[$i];
            $variationString = getQuoteVariationString($variationDetails[ $i ]);

            if (empty($product_id) || empty($quantity)) {
                continue;
            }

        // Get the product
            $product_data = wc_get_product($variation_id ? $variation_id : $product_id);

        // Sanity check
            if (version_compare(WC_VERSION, '2.7', '<')) {
                $postStatus = $product_data->post->post_status;
            } else {
                $postStatus = $product_data->get_status();
            }
            if ($quantity <= 0 || !$product_data || 'trash' === $postStatus) {
                throw new Exception();
            }

        // Stock check - only check if we're managing stock and backorders are not allowed
            if (!$product_data->is_in_stock()) {
                echo sprintf(__('You cannot add &quot;%s%s&quot; to the quotation because the product is out of stock.', QUOTEUP_TEXT_DOMAIN), $product_data->get_title(), $variationString);
                die;
            }

            if (!$product_data->has_enough_stock($quantity)) {
                echo sprintf(__('You cannot add that quantity of &quot;%s%s&quot; to the quotation because there is not enough stock (%s remaining).', QUOTEUP_TEXT_DOMAIN), $product_data->get_title(), $variationString, $product_data->get_stock_quantity());
                die;
            }

        // Stock check - this time accounting for whats already in-cart
            if ($managing_stock = $product_data->managing_stock()) {
                $products_qty_in_cart = getAllCartItemsTotalQuantity($product_ids, $quantities, $variation_ids);

                if ($product_data->is_type('variation') && true === $managing_stock) {
                    $check_qty = isset($products_qty_in_cart[ $variation_id ]) ? $products_qty_in_cart[ $variation_id ] : 0;
                } else {
                    $check_qty = isset($products_qty_in_cart[ $product_id ]) ? $products_qty_in_cart[ $product_id ] : 0;
                }

                    /*
                     * Check stock based on all items in the cart.
                     */
                if (!$product_data->has_enough_stock($check_qty)) {
                    echo sprintf(__('You cannot add that quantity of &quot;%s&quot; to the quotation &mdash; we have %s in stock and you have %s in your quotation.', QUOTEUP_TEXT_DOMAIN), $product_data->get_title(), $product_data->get_stock_quantity(), $check_qty);
                    die;
                }
            }
        }

        return true;
    }

    /**
     * This function is used to format variation details in required format.
     *
     * @param [array] $variation_details [description]
     *
     * @return [array] [description]
     */
    public static function getQuoteVariationDetails($variation_details)
    {
        if ($variation_details != '') {
            $newVariation = array();
            foreach ($variation_details as $individualVariation) {
                $keyValue = explode(':', $individualVariation);
                $newVariation[ trim($keyValue[ 0 ]) ] = trim($keyValue[ 1 ]);
            }

            return $newVariation;
        }

        return $variation_details;
    }

    /**
     * This function is used to set expiration date in database.
     *
     * @param [int] $enquiry_id [Enquiry id]
     */
    public static function setExpiry($enquiry_id, $quotationData)
    {
        global $quoteup;
        $expiration_date = isset($quotationData[ 'expiration-date' ]) ? $quotationData[ 'expiration-date' ] : '0000-00-00 00:00:00';
        if (!empty($expiration_date)) {
            $quoteup->manageExpiration->setExpirationDate($expiration_date, $enquiry_id);
        }
    }

    /**
     * This function is used to update order id.
     *
     * @param [int] $enquiry_id [Enquiry ID]
     */
    public static function setOrderID($enquiry_id)
    {
        global $wpdb;
        $getOrderId = $wpdb->get_var($wpdb->prepare("SELECT order_id FROM {$wpdb->prefix}enquiry_detail_new WHERE enquiry_id = %d", $enquiry_id));
        if ($getOrderId != null || $getOrderId === 0) {
            $wpdb->update(
                getEnquiryDetailsTable(),
                array(
                'order_id' => null,
                ),
                array(
                'enquiry_id' => $enquiry_id,
                )
            );
        }
    }

    /*
     * Ajax callback for "get_last_history_data"
     */
    public function getLastUpdatedHistoryRow()
    {
        global $quoteupManageHistory, $wpdb, $quoteup;
        $status = '';
        $_POST[ 'enquiry_id' ] = filter_var($_POST[ 'enquiry_id' ], FILTER_SANITIZE_NUMBER_INT);
        $history = $quoteupManageHistory->getLastAddedHistory($_POST[ 'enquiry_id' ]);
        if ($history == null) {
            $status = 'NO_NEW_HISTORY';
            echo json_encode(array('status' => $status));
            die();
        }
        $enquiry_tbl = getEnquiryDetailsTable();
        $enquiryDetails = $wpdb->get_row($wpdb->prepare("SELECT enquiry_id, name, message FROM $enquiry_tbl WHERE enquiry_id = %d", $_POST[ 'enquiry_id' ]));
        ob_start();
        $quoteup->displayHistory->printSingleRow($history, $enquiryDetails);
        $getContent = ob_get_contents();
        ob_end_clean();
        echo json_encode(array(
            'status' => $history[ 'status' ],
            'table_row' => $getContent,
        ));
        die();
    }

    /*
     * Ajax callback for "get_last_version_data"
     */
    public function getLastUpdatedVersionRow()
    {
        global $wpdb, $quoteup;
        $status = '';
        $_POST[ 'enquiry_id' ] = filter_var($_POST[ 'enquiry_id' ], FILTER_SANITIZE_NUMBER_INT);
        $version = $_POST[ 'lastversion' ] + 1;
        $versionTbl = getVersionTable();
        $versionDetails = $wpdb->get_results($wpdb->prepare("SELECT * FROM $versionTbl WHERE enquiry_id = %d AND version = %d ORDER BY version", $_POST[ 'enquiry_id' ], $version), ARRAY_A);
        if ($versionDetails == null) {
            $status = 'NO_NEW_VERSION';
            echo json_encode(array('status' => $status));
            die();
        }
        ob_start();
        $quoteup->displayVersions->printVersionRow($version, $versionDetails);
        $getContent = ob_get_contents();
        ob_end_clean();
        echo json_encode(array(
            'status' => $status,
            'table_row' => $getContent,
        ));
        die();
    }
}
