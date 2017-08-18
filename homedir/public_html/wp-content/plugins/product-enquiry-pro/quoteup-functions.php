<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Checks if provided product id is simple or not.
 *
 * @param int $productId Product Id of the product
 *
 * @return bool returns true if it is a simple produc, otherwise returns false.
 */
function isSimpleProduct($productId)
{
    $product = get_product($productId);
    if ($product->get_type() == null || $product->is_type('simple')) {
        return true;
    }

    return false;
}

/**
 * Returns the Manual CSS Settings saved in the options table.
 *
 * @param type $form_data
 */
function getManualCSS($form_data = array())
{
    if (empty($form_data)) {
        $form_data = quoteupSettings();
    }

    $btn_text_color = $form_data[ 'button_text_color' ];
    $btn_border = $form_data[ 'button_border_color' ];

    $end = $form_data[ 'end_color' ];
    $start = $form_data[ 'start_color' ];
    $style_attr = "style = '";
    $style_array = array();
    if (!empty($btn_text_color)) {
        $style_array[] = "color:{$btn_text_color} !important";
    }
    if (!empty($btn_border)) {
        $style_array[] = "border-color:{$btn_border}";
    }
    if (!empty($start)) {
        $style_array[] = "background: {$start}";
    }
    if (!empty($btn_border)) {
        $style_array[] = "border-color:{$btn_border}";
    }
    if (!empty($start) && !empty($end)) {
        $style_array[] = "background: -webkit-linear-gradient(bottom,{$start}, {$end})";
        $style_array[] = "background: -o-linear-gradient(bottom,{$start}, {$end})";
        $style_array[] = "background: -moz-linear-gradient(bottom,{$start}, {$end})";
        $style_array[] = "filter:progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr={$start}, endColorstr={$end})";
        $style_array[] = "-ms-filter: progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr={$start}, endColorstr={$end})";
        $style_array[] = "background: linear-gradient({$start}, {$end})";
    }
    $style_attr .= implode(';', $style_array)."'";

    return htmlspecialchars($style_attr);
}

/**
 * This function is used to get localization data.
 *
 * @param [type] $redirect_url [description]
 * @param [type] $country      [description]
 *
 * @return [type] [description]
 */
function getLocalizationDataForJs($redirect_url)
{
    $product_id = '';
    $quoteCart = '';
    $quoteCartLink = '';
    $mpe = 'no';
    if (is_product()) {
        global $product;
        $product_id = $product->get_id();
    }
    $form_data = get_option('wdm_form_data');
    if (isset($form_data[ 'enable_disable_mpe' ]) && $form_data[ 'enable_disable_mpe' ] == 1) {
        $mpe = 'yes';
    }
    if (isset($form_data[ 'mpe_cart_page' ])) {
        $quoteCart = $form_data[ 'mpe_cart_page' ];
        $quoteCartLink = get_permalink($quoteCart);
    }

    if (isset($form_data[ 'cart_custom_label' ]) && !empty($form_data[ 'cart_custom_label' ])) {
        $QuoteCartLinkWithText = "<a href='$quoteCartLink'>".$form_data[ 'cart_custom_label' ].'</a>';
    } elseif (isset($form_data[ 'enable_disable_quote' ]) && $form_data[ 'enable_disable_quote' ] == 0) {
        $QuoteCartLinkWithText = "<a href='$quoteCartLink'>".__('View Enquiry & Quote Cart', QUOTEUP_TEXT_DOMAIN).'</a>';
    } else {
        $QuoteCartLinkWithText = "<a href='$quoteCartLink'>".__('View Enquiry Cart', QUOTEUP_TEXT_DOMAIN).'</a>';
    }

    $buttonText = empty($form_data[ 'custom_label' ]) ? __('Make an Enquiry', QUOTEUP_TEXT_DOMAIN) : $form_data[ 'custom_label' ];

    return array(
        'ajax_admin_url' => admin_url('admin-ajax.php'),
        'name_req' => __('Please Enter Name', QUOTEUP_TEXT_DOMAIN),
        'valid_name' => __('Please Enter Valid Name', QUOTEUP_TEXT_DOMAIN),
        'e_req' => __('Please Enter Email Address', QUOTEUP_TEXT_DOMAIN),
        'email_err' => __('Please Enter Valid Email Address', QUOTEUP_TEXT_DOMAIN),
        'tel_err' => __('Please Enter Valid Telephone No', QUOTEUP_TEXT_DOMAIN),
        'msg_req' => __('Please Enter Message', QUOTEUP_TEXT_DOMAIN),
        'msg_err' => __('Message length must be between 15 to 500 characters', QUOTEUP_TEXT_DOMAIN),
        'nm_place' => __('Name*', QUOTEUP_TEXT_DOMAIN),
        'email_place' => __('Email*', QUOTEUP_TEXT_DOMAIN),
        'please_enter' => __('Please Enter', QUOTEUP_TEXT_DOMAIN),
        'please_select' => __('Please Select', QUOTEUP_TEXT_DOMAIN),
        'fields' => apply_filters('quoteup_get_custom_field', 'fields'),
        'redirect' => $redirect_url,
        'product_id' => $product_id,
        'MPE' => $mpe,
        'view_quote_cart_link_with_text' => $QuoteCartLinkWithText,
        'view_quote_cart_link_with_sold_individual_text' => __('Products that are sold individually can be added only once', QUOTEUP_TEXT_DOMAIN),
        'view_quote_cart_link' => $quoteCartLink,
        'products_added_in_quote_cart' => __('products added in Quote Cart', QUOTEUP_TEXT_DOMAIN),
        'select_variation' => __('Please select variation before sending enquiry', QUOTEUP_TEXT_DOMAIN),
        'product_added_in_quote_cart' => __('product added in Quote Cart', QUOTEUP_TEXT_DOMAIN),
        'cart_not_updated' => __('Enter valid Quantity', QUOTEUP_TEXT_DOMAIN),
        'spinner_img_url' => admin_url('images/spinner.gif'),
        'empty_cart_remove' => __('Your cart is currently empty', QUOTEUP_TEXT_DOMAIN),
        'buttonText' => $buttonText,
    );
}

/**
 * Returns the  Base Url of the plugin without trailing slash.
 *
 * @return type
 */
function quoteupPluginUrl()
{
    return untrailingslashit(plugins_url('/', __FILE__));
}

/**
 * Returns the  Base dir of the plugin without trailing slash.
 *
 * @return type
 */
function quoteupPluginDir()
{
    return untrailingslashit(plugin_dir_path(__FILE__));
}

/**
 * Returns the Base dir of the WooCommerce plugin without trailing slash.
 */
function quoteupWcPluginDir()
{
    return untrailingslashit(plugin_dir_path(dirname(__FILE__)).'woocommerce');
}

/**
 * Generates a hash to be used for Enquiry.
 *
 * @param int $enquiryId
 *
 * @return string enquiry hash
 */
function quoteupEnquiryHashGenerator($enquiryId)
{
    $hash = sha1(uniqid(rand(), true));
    list($usec, $sec) = explode(' ', microtime());
    $hash .= dechex($usec).dechex($sec);

    return $enquiryId.'_'.$hash;
}

/**
 * Generates a link to be used to reach Approval/Rejection page.
 *
 * @param string $enquiryHash
 *
 * @return mixed reutrns false or returns a generated link
 */
function quoteLinkGenerator($enquiryHash)
{
    $enquiryHash = trim($enquiryHash);
    if (empty($enquiryHash)) {
        return false;
    }
    $optionData = quoteupSettings();
    if (!isset($optionData[ 'approval_rejection_page' ]) || !intval($optionData[ 'approval_rejection_page' ])) {
        return false;
    }
    $pageId = $optionData[ 'approval_rejection_page' ];
    if (quoteupIsWpmlActive()) {
        $pageId = icl_object_id($optionData[ 'approval_rejection_page' ], 'page', true);
    }

    return add_query_arg('quoteupHash', $enquiryHash, get_page_link($pageId));
}

/**
 * Set hash to the enquiry in database.
 */
function updateHash($enquiry_id, $hash)
{
    global $wpdb;
    $table_name = getEnquiryDetailsTable();
    $wpdb->update(
        $table_name,
        array(
        'enquiry_hash' => $hash,
        ),
        array(
        'enquiry_id' => $enquiry_id,
        )
    );
}

/**
 * Check if a product is sold individually (no quantities).
 *
 * @return bool
 */
function isSoldIndividually($productId)
{
    $product = wc_get_product($productId);

    return $product->is_sold_individually();
}

/**
 * [This function downloads the file from the specified url]
 * it is the copy of wordpress download URL function.
 * We have replaced wp_remote_safe_get to wp_remote_get.
 *
 * @param [string] $url     [URL from which we have to download file]
 * @param int      $timeout [description]
 *
 * @return [type] [description]
 */
function quoteup_download_url($url, $timeout = 300)
{
    //WARNING: The file is not automatically deleted, The script must unlink() the file.
    if (!$url) {
        return new WP_Error('http_no_url', __('Invalid URL Provided.', QUOTEUP_TEXT_DOMAIN));
    }

    $tmpfname = wp_tempnam($url);
    if (!$tmpfname) {
        return new WP_Error('http_no_file', __('Could not create Temporary file.', QUOTEUP_TEXT_DOMAIN));
    }

    $response = wp_remote_get($url, array('timeout' => $timeout, 'stream' => true,
        'filename' => $tmpfname, ));

    if (is_wp_error($response)) {
        unlink($tmpfname);

        return $response;
    }

    if (200 != wp_remote_retrieve_response_code($response)) {
        unlink($tmpfname);

        return new WP_Error('http_404', trim(wp_remote_retrieve_response_message($response)));
    }

    $content_md5 = wp_remote_retrieve_header($response, 'content-md5');
    if ($content_md5) {
        $md5_check = verify_file_md5($tmpfname, $content_md5);
        if (is_wp_error($md5_check)) {
            unlink($tmpfname);

            return $md5_check;
        }
    }

    return $tmpfname;
}

/**
 * This function is used to get enquiry id from hash.
 *
 * @param [type] $quoteupHash [description]
 *
 * @return [type] [description]
 */
function getEnquiryIdFromHash($quoteupHash)
{
    $enquiry_id = explode('_', $quoteupHash);

    return $enquiry_id[ 0 ];
}

/**
 * This function is used to check if product is available.
 * This also checks the status of product is not trash.
 *
 * @param [type] $productID [description]
 *
 * @return bool [description]
 */
function isProductAvailable($productID)
{
    $parentAvailable = '';
    $productType = get_post_type($productID);
    if ($productType == 'product_variation') {
        $parentID = wp_get_post_parent_id($productID);
        $parentAvailable = get_post_status($parentID);
    }
    $productAvailable = get_post_status($productID);

    if ($productAvailable) {
        return !($productAvailable == 'trash' || $parentAvailable == 'trash');
    }

    return false;
}

/**
 * This function is used to display helptip.
 *
 * @param [String] $helptip  [Help tip to be displayed]
 * @param bool     $settings [description]
 * @param string   $image    [description]
 * @param string   $title    [description]
 *
 * @return [type] [description]
 */
function quoteupHelpTip($helptip, $settings = false, $image = '', $title = '')
{
    if ($settings === true) {
        $wooVersion = WC_VERSION;
        $wooVersion = floatval($wooVersion);
        if ($wooVersion < 2.5) {
            return '<img class="help_tip" data-tip="'.esc_attr($helptip).'" src="'.WC()->plugin_url().'/assets/images/help.png" height="16" width="16" />';
        } else {
            return \wc_help_tip($helptip);
        }
    }
    if (!empty($image)) {
        return '<img class="help_tip tips" alt="'.esc_attr($title).'" data-tip="'.esc_attr($helptip).'" src="'.$image.'" height="25" width="25" />';
    }

    return '<span class="help_tip tips" data-tip="'.esc_attr($helptip).'">'.esc_attr($title).'</span>';
}

/**
 * Checks whether provided product is in stock or not.
 *
 * @param $product it can be a product id or a Product Object
 *
 * @return bool
 */
function quoteupIsProductInStock($product)
{
    if (!is_object($product)) {
        $product = wc_get_product($product);
    }
    if ($product->is_in_stock()) {
        return true;
    }

    return false;
}

/*
 * This function is used to replace 'enquiry', 'quote' and 'quotation' words if set by user in settings.
 *
 * @param [string] $translatedText [Translated text of the orignal string]
 * @param [string] $text           [Orignal string]
 * @param [string] $domain         [text domain of the string]
 *
 * @return [string] [Returns the final string]
 */
add_filter('gettext', 'replaceText', 10, 3);

function replaceText($translatedText, $text, $domain)
{
    $form_data = quoteupSettings();
    // Check if string belongs to our plugin
    if ($domain == 'quoteup') {
        switch ($translatedText) {
            case ' Alternate word for Enquiry ':
            case ' Alternate word for Quote ':
            case 'Quotation  ':
            case 'Product Enquiry Pro %s %s %s':
            case 'Enquiry Details':
            case 'Enquiry & Quote Details':
            case 'Product Enquiry Pro for WooCommerce (A.K.A QuoteUp)':
            case 'QuoteUp Settings':
            case 'Create New Quote':
            case 'Alternate word for Enquiry.':
            case 'Alternate word for Quote and Quotation.':
                return $translatedText;            
            default:
                //Check if replace text for enquiry is set by the admin
                if (isset($form_data['replace_enquiry']) && !empty($form_data['replace_enquiry'])) {
                    //Replace the text
                    $translatedText = str_ireplace('enquiry', $form_data['replace_enquiry'], $translatedText);
                }
                //Check if replace text for quote and quotation is set by the admin
                if (isset($form_data['replace_quote']) && !empty($form_data['replace_quote'])) {
                    //Replace the text
                    $translatedText = str_ireplace('quote', $form_data['replace_quote'], $translatedText);
                    $translatedText = str_ireplace('quotation', $form_data['replace_quote'], $translatedText);
                }
                break;
        }
    }
    unset($text);

    return $translatedText;
}

/**
 * This function is used to get settings.
 *
 * @return [type] [description]
 */
function quoteupSettings()
{
    static $settings;
    if (quoteupIsWpmlActive()) {
        global $sitepress;
        if ($sitepress !== null) {
            $currentLanguage = $sitepress->get_current_language();
            if (isset($settings[$currentLanguage])) {
                return $settings[$currentLanguage];
            } else {
                $settings[$currentLanguage] = get_option('wdm_form_data');

                return $settings[$currentLanguage];
            }
        }
    }
    if (!isset($settings) || empty($settings)) {
        $settings = get_option('wdm_form_data');
    }

    return $settings;
}

function quoteupGetAdminTemplatePart($slug, $name = '', $args = array())
{
    quoteupGetTemplatePart('admin', $args, $slug, $name);
}

function quoteupGetPublicTemplatePart($slug, $name = '', $args = array())
{
    quoteupGetTemplatePart('public', $args, $slug, $name);
}

function quoteupGetTemplatePart($templateType = 'public', $args, $slug, $name = '')
{
    $template = '';
    extract($args);
    // Look in yourtheme/quoteup/slug-name.php
    if ($name) {
        $template = locate_template("quoteup/{$templateType}/{$slug}-{$name}.php");
    }

    // Get default slug-name.php
    if (!$template && $name && file_exists(QUOTEUP_PLUGIN_DIR."/templates/{$templateType}/{$slug}-{$name}.php")) {
        $template = QUOTEUP_PLUGIN_DIR."/templates/{$templateType}/{$slug}-{$name}.php";
    }

    // If template file doesn't exist, yourtheme/quoteup/slug.php
    if (!$template) {
        $template = locate_template("quoteup/{$templateType}/{$slug}.php");
    }

    // Get default slug.php
    if (!$template && file_exists(QUOTEUP_PLUGIN_DIR."/templates/{$templateType}/{$slug}.php")) {
        $template = QUOTEUP_PLUGIN_DIR."/templates/{$templateType}/{$slug}.php";
    }

    // Allow 3rd party plugin filter template file from their plugin
    $template = apply_filters("quoteup_get_{$templateType}_template_part", $template, $slug, $name, $args);

    if ($template) {
        include $template;
    }
}

/**
 * This function returns the total quantity of any product in given array of products.
 *
 * @param [type] $product_ids   [description]
 * @param [type] $quantities    [description]
 * @param [type] $variation_ids [description]
 *
 * @return [type] [description]
 */
function getAllCartItemsTotalQuantity($product_ids, $quantities, $variation_ids)
{
    $size = sizeof($product_ids);
    for ($i = 0; $i < $size; ++$i) {
        $product_id = absint($product_ids[$i]);
        $variation_id = absint($variation_ids[$i]);
        $quantity = $quantities[$i];

        if (empty($product_id) || empty($variation_id) || empty($quantity)) {
            continue;
        }

        // Get the product
        $_product = wc_get_product($variation_id ? $variation_id : $product_id);

        if ($_product->is_type('variation') && true === $_product->managing_stock()) {
            // Variation has stock levels defined so its handled individually
            $quantities[ $variation_id ] = isset($quantities[ $variation_id ]) ? $quantities[ $variation_id ] + $quantity : $quantity;
        } else {
            $quantities[ $product_id ] = isset($quantities[ $product_id ]) ? $quantities[ $product_id ] + $quantity : $quantity;
        }
    }

    return $quantities;
}

function setEnoughStockFalse($product_id, $variation_id, $variationDetail)
{
    global $quoteup_enough_stock, $quoteup_enough_stock_product_id;
    $quoteup_enough_stock = false;
    $quoteup_enough_stock_product_id = $product_id;
    if ('-' != $variation_id && 0 != $variation_id) {
        return getCartVariationString($variationDetail);
    }
}

/**
 * Returns the Product Details of Enquiry.
 *
 * @param  [int]    Enquiry Id
 *
 * @return [mix] Returns the array of Product details if found. Else returns Null
 */
function getProductDetailsOfEnquiry($enquiryId)
{
    global $wpdb;
    $productDetails = $wpdb->get_var($wpdb->prepare("SELECT product_details FROM {$wpdb->prefix}enquiry_detail_new WHERE enquiry_id = %d", $enquiryId));
    if (!is_null($productDetails)) {
        $productDetails = maybe_unserialize($productDetails);
    }

    return $productDetails;
}

/**
 * Returns list of all Product ids in the Quote. For variable products, it returns variation ids.
 *
 * @param  [int]    Enquiry Id
 *
 * @return [mix] Returns array of product ids in the Quote if found. Else returns null.
 */
function getProductIdsInQuote($enquiryId)
{
    global $wpdb;
    return $wpdb->get_col($wpdb->prepare("SELECT DISTINCT product_id FROM {$wpdb->prefix}enquiry_quotation WHERE enquiry_id = %d", $enquiryId));
}

/**
 * THis function is used to get variation string for error in quote.
 *
 * @param [type] $variationDetails [description]
 *
 * @return [type] [description]
 */
function getQuoteVariationString($variationDetails)
{
    if ($variationDetails != '' && !empty($variationDetails)) {
        $newVariation = array();
        foreach ($variationDetails as $individualVariation) {
            $keyValue = explode(':', $individualVariation);
            $newVariation[ trim($keyValue[ 0 ]) ] = trim($keyValue[ 1 ]);
        }

        $variation_detail = $newVariation;
        $variationString = '';
        foreach ($variation_detail as $attributeName => $attributeValue) {
            if (!empty($variationString)) {
                $variationString .= ',';
            }
            $variationString .= '<b> '.wc_attribute_label(str_replace('attribute_', '', $attributeName)).'</b> : '.$attributeValue;
        }

        return '('.$variationString.')';
    }

    return '';
}

 /* THis function is used to get variation string for error in quote
 * @param  [type] $variationDetails [description]
 * @return [type]                   [description]
 */
function getCartVariationString($variationDetail)
{
    if ($variationDetail != '') {
        $variationString = '';
        $variation_detail = maybe_unserialize($variationDetail);
        foreach ($variation_detail as $attributeName => $attributeValue) {
            if (!empty($variationString)) {
                $variationString .= ',';
            }
            $variationString .= '<b> '.wc_attribute_label(str_replace('attribute_', '', $attributeName)).'</b> : '.$attributeValue;
        }

        return '('.$variationString.')';
    }

    return '';
}

/**
 * This function is used to remove admin bar language switcher.
 *
 * @param [type] $hook [description]
 *
 * @return [type] [description]
 */
function quoteupWpmlRemoveAdminBarMenu()
{
    if (quoteupIsWpmlActive()) {
        if (isset($_GET['page'])) {
            if ($_GET['page'] == 'quoteup-details-edit' || $_GET['page'] == 'quoteup-for-woocommerce') {
                global $sitepress;
                $sitepress->switch_lang('all');
                global $wp_admin_bar;
                $wp_admin_bar->remove_menu('WPML_ALS');
            }
        }
    }
}

/**
 * Returns true if WPML is active. Else returns false.
 */
function quoteupIsWpmlActive()
{
    if (!defined('ICL_SITEPRESS_VERSION') || ICL_PLUGIN_INACTIVE) {
        return false;
    }
    
    return true;
}

/**
 * This function is used to add short code on given page.
 *
 * @param [int]    $pageId    [page id]
 * @param [string] $shortcode [SHortcode to be added]
 *
 * @return [type] [description]
 */
function quoteupAddShortcodeOnPage($pageId, $shortcode)
{
    //get content of the page
    $selectedPage = get_post($pageId);

    if ($selectedPage !== null) {
        $pages = getRelatedPages($selectedPage);
        foreach ($pages as $singlePage) {
            //Check if shortcode is present already
            if (quoteupDoesContentHaveShortcode($singlePage->post_content, $shortcode) === false) {
                // Update Selected Page
                $page_data = array(
                      'ID' => $singlePage->ID,
                      'post_content' => $singlePage->post_content."<br /> [$shortcode]",
                );

                // Update the page into the database
                wp_update_post($page_data);
            }
        }
    }
}

function getRelatedPages($selectedPage)
{
    $pages = array($selectedPage);
    if (quoteupIsWpmlActive()) {
        global $sitepress;
        $trid = $sitepress->get_element_trid($selectedPage->ID, 'post_'.$selectedPage->post_type);
        $translations = $sitepress->get_element_translations($trid, 'post_'.$selectedPage->post_type);

        if ($translations) {
            foreach ($translations as $singleTranslation) {
                $page = get_post($singleTranslation->element_id);
                if ($page !== null) {
                    $pages[] = $page;
                }
            }
        }
    }
    return $pages;
}

/**
 * This function is used to remove short code on given page.
 *
 * @param [int]    $pageId    [page id]
 * @param [string] $shortcode [SHortcode to be added]
 *
 * @return [type] [description]
 */
function quoteupRemoveShortcodeFromPage($pageId, $shortcode)
{
    //get content of the page
    $selectedPage = get_post($pageId);

    if ($selectedPage !== null) {
        $pages = getRelatedPages($selectedPage);
        foreach ($pages as $singlePage) {
            // Update Selected Page
                $page_data = array(
                      'ID' => $singlePage->ID,
                      'post_content' => str_replace("[$shortcode]", '', $selectedPage->post_content),
                );

                // Update the page into the database
                wp_update_post($page_data);
        }
    }
}

/**
 * Returns Attribute Name for variations which are not Taxonomies.
 */
function quoteupVariationAttributeLabel($variableProduct, $variationAttribute, $allAttributes){

        if (version_compare(WC_VERSION, '3.0.0', '<')) {

            if(isset($allAttributes[ str_replace('attribute_', '', $variationAttribute) ])) {
                $label = wc_attribute_label($allAttributes[ str_replace('attribute_', '', $variationAttribute) ]['name']);
            } else {
                $label = $variationAttribute;
            }
            
        } else {
            $label = wc_attribute_label(str_replace('attribute_', '', $variationAttribute), $variableProduct);
        }
        return $label;
}

/**
 * Checks if content has provided shortcode.
 *
 * @param string $content   Content in which shortcode is to be searched
 * @param string $shortcode Shortcode to search
 *
 * @return bool returns true if found, else returns false
 */
function quoteupDoesContentHaveShortcode($content, $shortcode)
{
    if (false === strstr($content, "[$shortcode]")) {
        return false;
    }

    return true;
}

/**
 * Remove Class Filter Without Access to Class Object.
 *
 * In order to use the core WordPress remove_filter() on a filter added with the callback
 * to a class, you either have to have access to that class object, or it has to be a call
 * to a static method.  This method allows you to remove filters with a callback to a class
 * you don't have access to.
 *
 * Works with WordPress 1.2+ (4.7+ support added 9-19-2016)
 *
 * @param string $tag         Filter to remove
 * @param string $class_name  Class name for the filter's callback
 * @param string $method_name Method name for the filter's callback
 * @param int    $priority    Priority of the filter (default 10)
 *
 * @return bool Whether the function is removed.
 */
function quoteupRemoveClassFilter($tag, $class_name = '', $method_name = '', $priority = 10)
{
    global $wp_filter;

    // Check that filter actually exists first
    if (!isset($wp_filter[ $tag ])) {
        return false;
    }

    /**
     * If filter config is an object, means we're using WordPress 4.7+ and the config is no longer
     * a simple array, rather it is an object that implements the ArrayAccess interface.
     *
     * To be backwards compatible, we set $callbacks equal to the correct array as a reference (so $wp_filter is updated)
     *
     * @see https://make.wordpress.org/core/2016/09/08/wp_hook-next-generation-actions-and-filters/
     */
    if (is_object($wp_filter[ $tag ]) && isset($wp_filter[ $tag ]->callbacks)) {
        $callbacks = &$wp_filter[ $tag ]->callbacks;
    } else {
        $callbacks = &$wp_filter[ $tag ];
    }

    // Exit if there aren't any callbacks for specified priority
    if (!isset($callbacks[ $priority ]) || empty($callbacks[ $priority ])) {
        return false;
    }

    // Loop through each filter for the specified priority, looking for our class & method
    foreach ((array) $callbacks[ $priority ] as $filter_id => $filter) {
        // Filter should always be an array - array( $this, 'method' ), if not goto next
        if (!isset($filter[ 'function' ]) || !is_array($filter[ 'function' ])) {
            continue;
        }

        // If first value in array is not an object, it can't be a class
        if (!is_object($filter[ 'function' ][ 0 ])) {
            continue;
        }

        // Method doesn't match the one we're looking for, goto next
        if ($filter[ 'function' ][ 1 ] !== $method_name) {
            continue;
        }

        // Method matched, now let's check the Class
        if (get_class($filter[ 'function' ][ 0 ]) === $class_name) {
            // Now let's remove it from the array
            unset($callbacks[ $priority ][ $filter_id ]);

            // and if it was the only filter in that priority, unset that priority
            if (empty($callbacks[ $priority ])) {
                unset($callbacks[ $priority ]);
            }

            // and if the only filter for that tag, set the tag to an empty array
            if (empty($callbacks)) {
                $callbacks = array();
            }

            // If using WordPress older than 4.7
            if (!is_object($wp_filter[ $tag ])) {
                // Remove this filter from merged_filters, which specifies if filters have been sorted
                unset($GLOBALS[ 'merged_filters' ][ $tag ]);
            }

            return true;
        }
    }

    return false;
}

/**
 * Remove Class Action Without Access to Class Object.
 *
 * In order to use the core WordPress remove_action() on an action added with the callback
 * to a class, you either have to have access to that class object, or it has to be a call
 * to a static method.  This method allows you to remove actions with a callback to a class
 * you don't have access to.
 *
 * Works with WordPress 1.2+ (4.7+ support added 9-19-2016)
 *
 * @param string $tag         Action to remove
 * @param string $class_name  Class name for the action's callback
 * @param string $method_name Method name for the action's callback
 * @param int    $priority    Priority of the action (default 10)
 *
 * @return bool Whether the function is removed.
 */
function quoteupRemoveClassAction($tag, $class_name = '', $method_name = '', $priority = 10)
{
    quoteupRemoveClassFilter($tag, $class_name, $method_name, $priority);
}

function quoteupLogHookedFunctions($hookName)
{
    global $wp_filter;

    if (isset($wp_filter[$hookName])) {
        error_log("PRINTING ALL FUNCTIONS HOOKED ON $hookName ".print_r($wp_filter[$hookName], true));
    }
}

/**
 * This function is used to print vatiations from the given product details.
 *
 * @param [array] $product [Product Details]
 *
 * @return [string] [Variation string]
 */
function printVariations($product)
{
    $variationToBeSent = '';
    if (isset($product['variation']) && $product['variation'] != '') {
        $product['variation'] = maybe_unserialize($product['variation']);
        if (isset($product['variation_id'])) {
            $isAvailable = isProductAvailable($product['variation_id']);
        } else {
            $isAvailable = isProductAvailable($product['variationID']);
        }
        if (!$isAvailable) {
            foreach ($product['variation'] as $singleVariationAttribute => $singleVariationValue) {
                if (!empty($variationToBeSent)) {
                    $variationToBeSent .= '<br>';
                }

                $variationToBeSent .= '<b>'.wc_attribute_label($singleVariationAttribute).': </b>'.stripcslashes($singleVariationValue);
            }

            return $variationToBeSent;
        }
        if (isset($product['variation_id'])) {
            $variableProduct = wc_get_product($product['variation_id']);
        } else {
            $variableProduct = wc_get_product($product['variationID']);
        }
        $product_attributes = $variableProduct->get_attributes();
        $variationToBeSent =  getVariationString($product['variation'], $variableProduct, $product_attributes);
        return '<br>'.$variationToBeSent;
    }

    return '';
}

/**
 * Format array for the datepicker.
 *
 * WordPress stores the locale information in an array with a alphanumeric index, and
 * the datepicker wants a numerical index. This function replaces the index with a number
 */
function stripArrayIndices($ArrayToStrip)
{
    foreach ($ArrayToStrip as $objArrayItem) {
        $NewArray[] = $objArrayItem;
    }

    return  $NewArray;
}

/**
 * Convert a date format to a jQuery UI DatePicker format.
 *
 * @param string $dateFormat a date format
 *
 * @return string
 */
function dateFormatTojQueryUIDatePickerFormat($dateFormat)
{
    $chars = array(
        // Day
        'd' => 'dd', 'j' => 'd', 'l' => 'DD', 'D' => 'D',
        // Month
        'm' => 'mm', 'n' => 'm', 'F' => 'MM', 'M' => 'M',
        // Year
        'Y' => 'yy', 'y' => 'y',
    );

    return strtr((string) $dateFormat, $chars);
}

function shouldScriptBeEnqueued($handle)
{
    if (wp_script_is($handle, 'enqueued') || wp_script_is($handle, 'done')) {
        return false;
    }

    return true;
}

function shouldStyleBeEnqueued($handle)
{
    if (wp_style_is($handle, 'enqueued') || wp_style_is($handle, 'done')) {
        return false;
    }

    return true;
}

function quoteupDebugDataLog($prefixText, $data){
    error_log(strtoupper($prefixText) . ': ' . print_r($data, true));
}

function quoteupDebugBulkData($data){
    if(is_array($data)){
        foreach($data as $key => $value){
            $prefixText = $key;
            if(is_numeric($key)){
                $prefixText = 'DATA ' . $key;
            }
            quoteupDebugDataLog($prefixText, $value);
        }
    }
}

function getProductsSelection($excludedProducts = "")
{
    ?>
    <div class="quote-products-selection">
        <div class="wrap">
            <input type="hidden" id="nonce" value="<?php echo wp_create_nonce('create-dashboard-quotation'); ?>">
            <input  type="hidden" 
                name="wc_products_selections" 
                class="wc-product-search" 
                data-multiple="true" 
                style="width: 75%;" 
                data-placeholder="<?php esc_attr_e('Search for a product&hellip;', 'woocommerce'); ?>"
                data-action="woocommerce_wpml_json_search_products_and_variations" 
                data-selected=""
                data-exclude="<?php echo $excludedProducts ?>"
                value="" />
        </div>
        <button class="button quoteup-add-products-button button-primary"><?php _e('Add Product(s)', QUOTEUP_TEXT_DOMAIN); ?></button>
        <span id="productLoad" class="productLoad"></span>
    </div>
    <?php
}


function quoteupVariationDropdown($count, $variationID, $productImage, $id, $product, $variationData)
{
        // Enqueue variation scripts
        wp_enqueue_script('wc-add-to-cart-variation');

        // Get Available variations?
        $get_variations = sizeof($product->get_children()) <= apply_filters('woocommerce_ajax_variation_threshold', 30, $product);

        // Load the template
        $args = array(
            'available_variations' => $get_variations ? $product->get_available_variations() : array(),
            'attributes'           => $product->get_variation_attributes(),
            'selected_attributes'  => method_exists($product, 'get_default_attributes') ? $product->get_default_attributes() : $product->get_variation_default_attributes(),
            'count'                => $count,
            'variationID'          => $variationID,
            'productImage'         => $productImage,
            'id'                   => $id,
            'product'              => $product,
            'variationData'        => $variationData,
        );

        quoteupGetAdminTemplatePart('variable', "", $args);
}



function getVariationAttributes($product) {
    global $wpdb;

    $variation_attributes = array();
    $attributes           = getAttributes($product);
    $child_ids            = $product->get_children( true );

    if ( ! empty( $child_ids ) ) {
        foreach ( $attributes as $attribute ) {
            if ( empty( $attribute['is_variation'] ) ) {
                continue;
            }

            // Get possible values for this attribute, for only visible variations.
            $values = array_unique( $wpdb->get_col( $wpdb->prepare(
                "SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s AND post_id IN (" . implode( ',', array_map( 'esc_sql', $child_ids ) ) . ")",
                wc_variation_attribute_name( $attribute['name'] )
            ) ) );

            // empty value indicates that all options for given attribute are available
            if ( in_array( '', $values ) || empty( $values ) ) {
                $values = $attribute['is_taxonomy'] ? wp_get_post_terms( $product->id, $attribute['name'], array( 'fields' => 'slugs' ) ) : wc_get_text_attributes( $attribute['value'] );

            // Get custom attributes (non taxonomy) as defined
            } elseif ( ! $attribute['is_taxonomy'] ) {
                $text_attributes          = wc_get_text_attributes( $attribute['value'] );
                $assigned_text_attributes = $values;
                $values                   = array();

                // Pre 2.4 handling where 'slugs' were saved instead of the full text attribute
                if ( version_compare( get_post_meta( $product->id, '_product_version', true ), '2.4.0', '<' ) ) {
                    $assigned_text_attributes = array_map( 'sanitize_title', $assigned_text_attributes );

                    foreach ( $text_attributes as $text_attribute ) {
                        if ( in_array( sanitize_title( $text_attribute ), $assigned_text_attributes ) ) {
                            $values[] = $text_attribute;
                        }
                    }
                } else {
                    foreach ( $text_attributes as $text_attribute ) {
                        if ( in_array( $text_attribute, $assigned_text_attributes ) ) {
                            $values[] = $text_attribute;
                        }
                    }
                }
            }

            $variation_attributes[ $attribute['name'] ] = array_unique( $values );
        }
    }

    return $variation_attributes;
}

function getAttributes($product) {
    $attributes = array_filter( (array) maybe_unserialize( $product->product_attributes() ) );
    $taxonomies = wp_list_pluck( wc_get_attribute_taxonomies(), 'attribute_name' );

    // Check for any attributes which have been removed globally
    foreach ( $attributes as $key => $attribute ) {
        if ( $attribute['is_taxonomy'] ) {
            if ( ! in_array( substr( $attribute['name'], 3 ), $taxonomies ) ) {
                unset( $attributes[ $key ] );
            }
        }
    }

    return apply_filters( 'woocommerce_get_product_attributes', $attributes );
}

function getCurrentLocale()
{
    $currentLocale = get_locale();
    $arr = explode("_", $currentLocale, 2);
    $currentLocale = $arr[0];
    if (quoteupIsWpmlActive()) {
        global $sitepress;
        $currentLocale = $sitepress->get_current_language();
    }
    return $currentLocale;
}

function quoteupGetPriceToDisplay($product)
{
    if (version_compare(WC_VERSION, '3.0.0', '<')) {
        return $product->get_display_price();
    } else {
        return wc_get_price_to_display($product);
    }
}

function getDateLocalizationArray()
{
    global $wp_locale;
 
    return array(
        'closeText' => __('Done', QUOTEUP_TEXT_DOMAIN),
        'currentText' => __('Today', QUOTEUP_TEXT_DOMAIN),
        'monthNames' => stripArrayIndices($wp_locale->month),
        'monthNamesShort' => stripArrayIndices($wp_locale->month_abbrev),
        'monthStatus' => __('Show a different month', QUOTEUP_TEXT_DOMAIN),
        'dayNames' => stripArrayIndices($wp_locale->weekday),
        'dayNamesShort' => stripArrayIndices($wp_locale->weekday_abbrev),
        'dayNamesMin' => stripArrayIndices($wp_locale->weekday_initial),
        // set the date format to match the WP general date settings
        'dateFormat' => dateFormatTojQueryUIDatePickerFormat(get_option('date_format')),
        // get the start of week from WP general setting
        'firstDay' => get_option('start_of_week'),
        // is Right to left language? default is false
        'isRTL' => is_rtl(),
    );
}

function getVariationString($variation_data, $variableProduct, $product_attributes)
{
    $variationToBeSent = '';
    foreach ($variation_data as $name => $value) {
        $taxonomy = wc_attribute_taxonomy_name(str_replace('attribute_pa_', '', urldecode($name)));

            // If this is a term slug, get the term's nice name
        if (taxonomy_exists($taxonomy)) {
            $term = get_term_by('slug', $value, $taxonomy);
            if (!is_wp_error($term) && $term && $term->name) {
                $value = $term->name;
            }
            $label = wc_attribute_label($taxonomy);
            // If this is a custom option slug, get the options name
        } else {
            $label = quoteupVariationAttributeLabel($variableProduct, $name, $product_attributes);
        }

        if (!empty($variationToBeSent)) {
            $variationToBeSent .= '<br>';
        }

        $variationToBeSent .= '<b>'.$label.': </b>'.stripcslashes($value);
    }

    return $variationToBeSent;
}

function enqueueDatePickerFiles()
{
    // Datepicker files
    global $wp_scripts;
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-core');
            // JS and css required for datepciker
            wp_enqueue_script('datepicker', quoteupPluginUrl().'/js/public/datepicker.js', array('jquery', 'jquery-ui-core', 'jquery-effects-highlight', 'jquery-ui-datepicker'), true);

            // get registered script object for jquery-ui
            $uiObject = $wp_scripts->query('jquery-ui-core');
            // tell WordPress to load the Smoothness theme from Google CDN
            $protocol = is_ssl() ? 'https' : 'http';
    $url = "$protocol://ajax.googleapis.com/ajax/libs/jqueryui/{$uiObject->ver}/themes/smoothness/jquery-ui.min.css";
    wp_enqueue_style('jquery-ui-smoothness', $url, false, null);
    wp_enqueue_style('jquery-ui-datepicker', QUOTEUP_PLUGIN_URL.'/css/admin/datepicker.css');
    $aryArgs = getDateLocalizationArray();
    wp_localize_script('datepicker', 'dateData', $aryArgs);
    // End of datepicker files
}

function getVariationDetailsForArray($variationDetails)
{
    $variationDetailsForArray = array();
    foreach ($variationDetails as $attriname => $attriValue) {
        $variationString = '';
        $variationString = $attriname.' : '.$attriValue;
        array_push($variationDetailsForArray, $variationString);
    }

    return $variationDetailsForArray;
}