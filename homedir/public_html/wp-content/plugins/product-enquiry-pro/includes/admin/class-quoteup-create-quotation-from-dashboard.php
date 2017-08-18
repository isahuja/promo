<?php

namespace Includes\Admin;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * This class is used to add extra features in PEP
 * - Adds Create Quotation from Dashboard feature.
 * - shows button for save quotation and send quotation.
 * - Handles everything about the create quotation from dashboard.
 */
class QuoteupCreateDashboardQuotation
{
    protected static $instance = null;
    public $enquiry_details = null;
    private static $currentLanguage = '';
    protected static $quoteupVatiationData = array();

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

    /**
     * constructor is used to add actions and filter.
     */
    private function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'), 1, 1);
        add_action('wp_ajax_save_dashboard-quote', array($this, 'saveQuote'));
        add_action('wp_ajax_action_send_dashboard_quote', array('Includes\Admin\SendQuoteMail', 'sendMailAjaxCallback'));
        do_action('quoteup_create_dashboard_custom_field');
    }

    public static function getVariations($variation_data, $variation_id)
    {
        if (isset($variation_data) && $variation_data != '') {
            $variation_data = maybe_unserialize($variation_data);
            $variableProduct = wc_get_product($variation_id);
            $product_attributes = $variableProduct->get_attributes();
            return getVariationString($variation_data, $variableProduct, $product_attributes);
        }

        return '';
    }

    /**
     * This function is used to enqueue scripts.
     */
    public function enqueueScripts()
    {
        if (!isset($_GET['page']) || $_GET['page'] != "quoteup-create-quote") {
            return;
        }

        $form_data = quoteupSettings();
        wp_enqueue_style('common-css', QUOTEUP_PLUGIN_URL.'/css/common.css');
        wp_enqueue_script('quoteup-functions', QUOTEUP_PLUGIN_URL.'/js/admin/functions.js');
        wp_enqueue_script('quoteup-encode', QUOTEUP_PLUGIN_URL.'/js/admin/encode-md5.js');
        wp_enqueue_script('postbox');

        $this->addInlineCss();

        wp_localize_script(
            'quoteup-functions',
            'quote_data',
            array(
                    'decimal_separator' => wc_get_price_decimal_separator(),
                    'thousand_separator' => wc_get_price_thousand_separator(),
                    'decimals' => wc_get_price_decimals(),
                    'price_format' => get_woocommerce_price_format(),
                    'currency_symbol' => get_woocommerce_currency_symbol(),
                    'path' => WP_CONTENT_URL.'/uploads/QuoteUp_PDF/',
                    )
        );

        wp_enqueue_script('bootstrap-modal', QUOTEUP_PLUGIN_URL.'/js/admin/bootstrap-modal.js', array(
                'jquery', ), false, true);
        wp_enqueue_style('modal_css1', QUOTEUP_PLUGIN_URL.'/css/wdm-bootstrap.css', false, false);
        wp_enqueue_style('dashboard-quote-creation-css', QUOTEUP_PLUGIN_URL.'/css/admin/dashboard-quote-creation.css');
        wp_enqueue_style('table-responsive-creation-css', QUOTEUP_PLUGIN_URL.'/css/admin/table-responsive.css');

        wp_enqueue_script('dashboard-quote-creation-js', QUOTEUP_PLUGIN_URL.'/js/admin/dashboard-quote-creation.js', array('jquery', 'wp-util'), QUOTEUP_VERSION, true);
        $pdfDisplay = isset($form_data['enable_disable_quote_pdf'])?$form_data['enable_disable_quote_pdf']:1;

        $data = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'fields' => apply_filters('quoteup_get_custom_field', 'fields'),
            'quoteup_no_products' => sprintf(__('%s No Products Selected %s', QUOTEUP_TEXT_DOMAIN), "<tr class='quoteup-no-product'><td colspan='6' style='text-align: center;'>", '</td></tr>'),
            'quantity_not_valid' => __('Enter valid Quantity', QUOTEUP_TEXT_DOMAIN),
            'price_not_valid' => __('Enter valid Price', QUOTEUP_TEXT_DOMAIN),
            'no_products_in_quotation' => __('No Products in Quotation', QUOTEUP_TEXT_DOMAIN),
            'update_quotation_text' => __('Update Quotation', QUOTEUP_TEXT_DOMAIN),
            'same_variation' => __('Same variation of a product cannot be added twice.', QUOTEUP_TEXT_DOMAIN),
            'invalid_variation' => __('Please select a valid variation for', QUOTEUP_TEXT_DOMAIN),
            'PDF' => $pdfDisplay,
            );

        wp_localize_script('dashboard-quote-creation-js', 'wdm_data', $data);
        
        wp_enqueue_script('quoteup-select2', QUOTEUP_PLUGIN_URL.'/js/admin/quoteup-select2.js', array('jquery'), QUOTEUP_VERSION);
        wp_enqueue_style('quoteup-select2-css', QUOTEUP_PLUGIN_URL.'/css/admin/quoteup-select2.css', array(), QUOTEUP_VERSION);
        wp_enqueue_style('woocommerce-admin-css', QUOTEUP_PLUGIN_URL.'/css/admin/woocommerce-admin.css', array(), QUOTEUP_VERSION);

        if (is_callable('WC')) {
            if (shouldScriptBeEnqueued('products-selection-js')) {
                wp_enqueue_script('products-selection-js', QUOTEUP_PLUGIN_URL.'/js/admin/products-selection.js', array('jquery'), QUOTEUP_VERSION);
                $productsSelectionData = array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                );

                wp_localize_script('products-selection-js', 'productsSelectionData', $productsSelectionData);
            }

            if (shouldScriptBeEnqueued('wc-enhanced-select-extended')) {
                wp_enqueue_script('wc-enhanced-select-extended', QUOTEUP_PLUGIN_URL.'/js/admin/enhanced-select-extended.js', array('jquery', 'quoteup-select2'), QUOTEUP_VERSION);

                wp_localize_script('wc-enhanced-select-extended', 'wc_enhanced_select_params', array(
                        'i18n_matches_1' => _x('One result is available, press enter to select it.', 'enhanced select', 'woocommerce'),
                        'i18n_matches_n' => _x('%qty% results are available, use up and down arrow keys to navigate.', 'enhanced select', 'woocommerce'),
                        'i18n_no_matches' => _x('No matches found', 'enhanced select', 'woocommerce'),
                        'i18n_ajax_error' => _x('Loading failed', 'enhanced select', 'woocommerce'),
                        'i18n_input_too_short_1' => _x('Please enter 1 or more characters', 'enhanced select', 'woocommerce'),
                        'i18n_input_too_short_n' => _x('Please enter %qty% or more characters', 'enhanced select', 'woocommerce'),
                        'i18n_input_too_long_1' => _x('Please delete 1 character', 'enhanced select', 'woocommerce'),
                        'i18n_input_too_long_n' => _x('Please delete %qty% characters', 'enhanced select', 'woocommerce'),
                        'i18n_selection_too_long_1' => _x('You can only select 1 item', 'enhanced select', 'woocommerce'),
                        'i18n_selection_too_long_n' => _x('You can only select %qty% items', 'enhanced select', 'woocommerce'),
                        'i18n_load_more' => _x('Loading more results&hellip;', 'enhanced select', 'woocommerce'),
                        'i18n_searching' => _x('Searching&hellip;', 'enhanced select', 'woocommerce'),
                        'ajax_url' => admin_url('admin-ajax.php'),
                        'search_products_nonce' => wp_create_nonce('search-products'),
                    ));
            }

            if (shouldStyleBeEnqueued('woocommerce_admin_styles')) {
                wp_enqueue_style('woocommerce_admin_styles', WC()->plugin_url().'/assets/css/admin.css', array(), QUOTEUP_VERSION);
            }

            if (shouldStyleBeEnqueued('products-selection-css')) {
                wp_enqueue_style('products-selection-css', QUOTEUP_PLUGIN_URL.'/css/admin/products-selection.css', array(), QUOTEUP_VERSION);
            }

            global $quoteup;

            $quoteup->quoteDetailsEdit->includeWooCommerceScripts();
        }

        enqueueDatePickerFiles();
        $this->enqueueWpmlCss();

        $args = array();
        quoteupGetAdminTemplatePart('quote-creation', '', $args);
    }

    private function addInlineCss()
    {
        $cssString = "th.quote-product-image, td.item-content-img {display: none;}";
        if (version_compare(WC_VERSION, '2.6', '>')) {
            wp_add_inline_style('common-css', $cssString);
        }
    }

    private function enqueueWpmlCss()
    {
        if (quoteupIsWpmlActive()) {
            wp_enqueue_style('dashboard-quote-creation-wpml-css', QUOTEUP_PLUGIN_URL.'/css/admin/dashboard-quote-creation-wpml.css');
        }
    }

    /**
     * Displays Language Selector to select the language.
     */
    private function displayLanguageSelector()
    {
        // Check if WPML is active or not
        if (!defined('ICL_SITEPRESS_VERSION') || ICL_PLUGIN_INACTIVE) {
            return;
        }
        ?>
        <label class="quote-language left-label"> <?php _e('Quote Language :', QUOTEUP_TEXT_DOMAIN) ?> </label>
        <?php
        echo '<select class="quoteup-pdf-language wc-language-selector">';
        echo "<option value='null'>Select Language</option>";
        $activeLanguages = icl_get_languages('skip_missing=0&orderby=code');
        foreach ($activeLanguages as $languageCode => $languageInfo) {
            $languageName = $languageInfo['native_name'].'('.$languageInfo['translated_name'].')';
            echo "<option value='{$languageCode}'> {$languageName} </option>'";
        }
        echo '</select>';
    }

    public static function jsonSearchProductsAndVariations()
    {
        //We won't write at the end of this because jsonSearchProducts will automatically do that for us.
        self::jsonSearchProducts('', array('product', 'product_variation'));
    }

    /**
     * Search for products and echo json.
     * It is very similar to WooCommerce's WC_AJAX::jsonSearchProducts. But it supports WPML too.
     *
     * @param string $term       (default: '')
     * @param string $post_types (default: array('product'))
     */
    private static function jsonSearchProducts($term = '', $post_types = array('product'))
    {
        global $wpdb;

        ob_start();

        check_ajax_referer('search-products', 'security');

        self::$currentLanguage = self::getProductsLanguage();

        $term = self::getTerm($term);

        $like_term = '%'.$wpdb->esc_like($term).'%';

        $query = $wpdb->prepare("
            SELECT ID FROM {$wpdb->posts} posts LEFT JOIN {$wpdb->postmeta} postmeta ON posts.ID = postmeta.post_id
            WHERE posts.post_status = 'publish'
            AND (
                posts.post_title LIKE %s
                or posts.post_content LIKE %s
                OR (
                    postmeta.meta_key = '_sku' AND postmeta.meta_value LIKE %s
                )
            )
        ", $like_term, $like_term, $like_term);

        $query .= " AND posts.post_type IN ('".implode("','", array_map('esc_sql', $post_types))."')";

        $query .= self::getQueryPart();

        $posts = array_unique($wpdb->get_col($query));

        $excludedProducts = self::getExcludedProducts();

        // Get Products of selected language only.
        $posts = self::getProductsOfSelectedLanguage($posts);

        $found_products = array();

        if (!empty($posts)) {
            foreach ($posts as $post) {
                $product = wc_get_product($post);

                if (!current_user_can('read_product', $post)) {
                    continue;
                }
                if (empty($product)) {
                    continue;
                }

                $product_type = $product->get_type();

                switch ($product_type) {
                    case 'simple':
                        $found_products = self::getSimpleProduct($post, $product, $found_products, $excludedProducts);
                        break;
                    case 'variable':
                        $found_products = self::addAllVariationsInSearchResults($product, $found_products, $excludedProducts);
                        break;
                    case 'variation':
                        if (!$product || ($product->is_type('variation'))) {
                            continue;
                        }
                        $found_products = self::getVariationProduct($post, $product, $found_products, $excludedProducts);
                        break;
                    default:
                        return;
                }
            }
        }
        $found_products = apply_filters('quoteup_json_search_found_products', $found_products);
        wp_send_json($found_products);
    }

    private static function getSimpleProduct($post, $product, $found_products, $excludedProducts)
    {
        $img_url = '';
        if (!$img_url || $img_url == '') {
            $img_url = wp_get_attachment_url(get_post_thumbnail_id($post));
        }
        if (!$img_url || $img_url == '') {
            $img_url = WC()->plugin_url().'/assets/images/placeholder.png';
        }
        if (!in_array(md5($post), $excludedProducts)) {
            $found_products[ md5($post) ] = array(
                'product_id' => $post,
                'product_type' => 'simple',
                'formatted_name' => rawurldecode($product->get_formatted_name()),
                'price' => $product->get_price(),
                'product_title' => $product->get_title(),
                'url' => admin_url("/post.php?post={$post}&action=edit"),
                'product_image' => $img_url,
                'sku' => $product->get_sku(),
                );
        }
        return $found_products;
    }

    private static function getVariationProduct($post, $product, $found_products, $excludedProducts)
    {
        $variation_data = $product->get_variation_attributes();

        $skipVariation = self::getSkipVariationStatus($variation_data);

        if (!empty($skipVariation)) {
            $product->variation_data = $variation_data;
            $found_products = self::addVariationInSearchResults($product, $post, $variation_data, $found_products, $excludedProducts);
        }
        return $found_products;
    }

    private static function getExcludedProducts()
    {
        $excludedProducts = array();

        if (!empty($_GET['exclude'])) {
            $excludedProducts = array_filter(array_unique(explode(',', $_GET['exclude'])));
        }
        return $excludedProducts;
    }

    private static function getProductsOfSelectedLanguage($posts)
    {
        global $wpdb;
        if (!empty(self::$currentLanguage) &&  self::$currentLanguage != 'all') {
            if (!empty($posts)) {
                $elements = implode(', ', $posts);
                $query = "SELECT element_id FROM {$wpdb->prefix}icl_translations WHERE element_id IN ({$elements}) AND language_code = %s AND element_type IN ('post_product_variation', 'post_product')";
                $posts = $wpdb->get_col($wpdb->prepare($query, self::$currentLanguage));
            }
        }
        return $posts;
    }

    private static function getSkipVariationStatus($variation_data)
    {
        $skipVariation = false;
        foreach ($variation_data as $variation) {
            if (empty($variation)) {
                $skipVariation = true;
                break;
            }
        }

        return $skipVariation;
    }

    private static function getProductsLanguage()
    {
        if (quoteupIsWpmlActive()) {
            return isset($_GET['language']) ? $_GET['language'] : 'all';
        }
    }

    private static function getTerm($term)
    {
        if (empty($term)) {
            $term = wc_clean(stripslashes($_GET['term']));
        } else {
            $term = wc_clean($term);
        }

        if (empty($term)) {
            die();
        }

        return $term;
    }

    private static function getQueryPart()
    {
        $query = '';
        if (!empty($_GET['exclude'])) {
            $query .= ' AND posts.ID NOT IN ('.implode(',', array_map('intval', explode(',', $_GET['exclude']))).')';
        }

        if (!empty($_GET['limit'])) {
            $query .= ' LIMIT '.intval($_GET['limit']);
        }

        return $query;
    }

    private static function addAllVariationsInSearchResults($product, $found_products, $excluded_products)
    {
        /*
         * Below we will be calling get_variation_attributes function which makes a call to get_attributes.
         * get_variation_attributes uses name of attribute as a key in the returned array. We need to change the
         * behavior and we need to have a 'slug' as a key in the array returned by get_variation_attributes. 
         * Therefore, we'll change the array returned by get_attributes. We'll keep name as slug in 
         * get_attributes array
         */
        do_action('quoteup_change_lang', self::$currentLanguage);
        
        if (version_compare(WC_VERSION, '3.0.0', '<')) {
            add_filter('woocommerce_get_product_attributes', array(__CLASS__, 'modifyProductAttributeNames'), 99, 1);
            $attributesArray = $product->get_variation_attributes();
            remove_filter('woocommerce_get_product_attributes', array(__CLASS__, 'modifyProductAttributeNames'), 99, 1);
        } else {
            add_filter('woocommerce_product_get_attributes', array(__CLASS__, 'modifyProductAttributeNames'), 99, 1);
            $attributesArray = $product->get_attributes();
            remove_filter('woocommerce_product_get_attributes', array(__CLASS__, 'modifyProductAttributeNames'), 99, 1);
        }
                
        if (empty($attributesArray)) {
            return $found_products;
        }
        $setAttributeName = function ($value) {
            return 'attribute_'.$value;
        };

        //Sets attribute_ prefix to all keys of an array
        $attributesArray = array_combine(
            array_map($setAttributeName, array_keys($attributesArray)),
            $attributesArray
        );

        $variationCombinations = self::getArrayCombinations($attributesArray);
        if ($variationCombinations) {
            foreach ($variationCombinations as $singleVariationCombination) {
                if (version_compare(WC_VERSION, '3.0.0', '<')) {
                    $variation_id = $product->get_matching_variation($singleVariationCombination);
                } else {
                    $data_store   = \WC_Data_Store::load('product-variable');
                    $variation_id =  $data_store->find_matching_product_variation($product, $singleVariationCombination);
                }
                if ($variation_id) {
                    $variation_product = wc_get_product($variation_id);
                    if (version_compare(WC_VERSION, '3.0.0', '<')) {
                        $variation_product->variation_data = $singleVariationCombination;
                    } else {
                        //Setting Variation Attributes in WC 3.0 and greater
                        $variationData = array();
                        foreach ($singleVariationCombination as $key => $value) {
                            // Remove attribute prefix which meta gets stored with.
                            if (0 === strpos($key, 'attribute_')) {
                                $key = substr($key, 10);
                            }
                            $variationData[ $key ] = $value;
                        }
                        $variation_product->set_props(array('attributes' => $variationData));
                    }

                    $found_products = self::addVariationInSearchResults($variation_product, $variation_id, $singleVariationCombination, $found_products, $excluded_products);
                }
            }
        }
        do_action('wdm_after_create_pdf');
        do_action('quoteup_reset_lang');
        return $found_products;
    }

    public static function modifyProductAttributeNames($attributes)
    {
        
        if (version_compare(WC_VERSION, '3.0.0', '<')) {
            if (is_array($attributes)) {
                foreach ($attributes as $attributeSlug => $attributeData) {
                    $attributes[$attributeSlug]['name'] = $attributeSlug;
                    unset($attributeData);
                }
            }
        } else {
            //For 3.0 and greater
            if (is_array($attributes)) {
                foreach ($attributes as $attributeSlug => $attributeObject) {
                    $attributeData = $attributeObject->get_data();
                    if ($attributeData['is_variation']) {
                        $attributes[$attributeSlug] = $attributeObject->get_slugs();
                    }
                }
            }
        }

        return $attributes;
    }

    private static function addVariationInSearchResults($variation_product, $variation_id, $variation_data, $found_products, $excluded_products)
    {
        if (!is_array($variation_data)) {
            return $found_products;
        }

        // Return Products if value of  any variation attribute is empty
        foreach ($variation_data as $variation) {
            if (empty($variation)) {
                return $found_products;
            }
        }
        //Creating md5 hash to check whether current combinations of attributes already exists in the array or not.
        $variation_hash = md5($variation_id.'_'.implode('_', $variation_data));

        if (in_array($variation_hash, $excluded_products)) {
            return $found_products;
        }

        //Check if this variation already exists in the array
        if (!isset($found_products[$variation_hash])) {
            $img_url = self::getImgUrl($variation_id, $variation_product);
            $productID = self::getParentID($variation_product);
            $found_products[$variation_hash] = array(
                'product_id' => $productID,
                'variation_id' => $variation_id,
                'product_type' => 'variation',
                'price' => $variation_product->get_price(),
                'formatted_name' => self::getVariationName($variation_product),
                'variation_attributes' => $variation_data,
                'variation_string' => self::getVariations($variation_data, $variation_id),
                'product_title' => $variation_product->get_title(),
                'url' => admin_url("/post.php?post={$productID}&action=edit"),
                'product_image' => $img_url,
                'sku' => $variation_product->get_sku(),
                );
        }

        return $found_products;
    }

    private static function getVariationName($variationProduct)
    {

        if (version_compare(WC_VERSION, '3.0.0', '>=')) {
            return rawurldecode(self::generateVariationName($variationProduct));
        }
            
            return rawurldecode($variationProduct->get_formatted_name());
    }

    /**
     * Generates Variation Title for WC greater than 3.0
     *
     * This is the copy of WooCommerce's WC_Product_Variation_Data_Store_CPT::generate_product_title(). Because
     * direct call to this method is not possible as it is a protected method, we are creating a copy of it in plugin
     *
     * @param  object $product variation product object
     * @return string          title of variation
     */
    private static function generateVariationName($product)
    {
        $include_attribute_names = false;
        $attributes = (array) $product->get_attributes();

        // Determine whether to include attribute names through counting the number of one-word attribute values.
        $one_word_attributes = 0;
        foreach ($attributes as $name => $value) {
            if (false === strpos($value, '-')) {
                ++$one_word_attributes;
            }
            if ($one_word_attributes > 1) {
                $include_attribute_names = true;
                break;
            }
            unset($name);
        }

        $include_attribute_names = apply_filters('woocommerce_product_variation_title_include_attribute_names', $include_attribute_names, $product);
        $title_base_text         = get_post_field('post_title', $product->get_parent_id());
        $title_attributes_text   = wc_get_formatted_variation($product, true, $include_attribute_names);
        $separator               = ! empty($title_attributes_text) ? ' &ndash; ' : '';

        return apply_filters(
            'woocommerce_product_variation_title',
            $title_base_text . $separator . $title_attributes_text,
            $product,
            $title_base_text,
            $title_attributes_text
        );
    }

    private static function getParentID($variation_product)
    {
        if (version_compare(WC_VERSION, '3.0.0', '<')) {
            $productID = $variation_product->parent->id;
        } else {
            $productID = $variation_product->get_parent_id();
        }
        return $productID;
    }

    private static function getImgUrl($variation_id, $variation_product)
    {
        if (isset($variation_id) && $variation_id != '') {
            $img_url = wp_get_attachment_url(get_post_thumbnail_id($variation_id));
        }
        if (!$img_url || $img_url == '') {
            $img_url = wp_get_attachment_url(get_post_thumbnail_id(self::getParentID($variation_product)));
        }
        if (!$img_url || $img_url == '') {
            $img_url = WC()->plugin_url().'/assets/images/placeholder.png';
        }
    }

    private static function getArrayCombinations($arrays)
    {
        $result = array(array());
        foreach ($arrays as $property => $property_values) {
            $tmp = array();
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, array($property => $property_value));
                }
            }
            $result = $tmp;
        }

        return $result;
    }

    /*
     * This function is used to display data on enquiry or quote edit page
     */
    public function createDashboardQuotation()
    {
        global $quoteup_admin_menu;
        ?>
        <div class="wrap">
        <?php screen_icon();
        ?>
            <h1>
        <?php
            echo esc_html_e('Create Quotation', QUOTEUP_TEXT_DOMAIN);
        ?>

            </h1>
            <!-- <form name="editQuoteDetailForm" method="post"> -->
                <input type="hidden" name="action" value="editQuoteDetail" />
                        <?php
        ?>
                <div id="poststuff">
                    <div id="post-body" class="metabox-holder">

                        <div id="post-body-content">
                            <!-- <p>Admin Page for Editing Product Enquiry Detail.</p> -->
                            <?php
                            $this->displayLanguageSelector();
        ?>
                        </div>
                        <div id="postbox-container-2" class="postbox-container">
        <?php
        add_meta_box('quoteProducts', __('Product Details', QUOTEUP_TEXT_DOMAIN), array($this, 'productDetailsSection'), $quoteup_admin_menu, 'normal');

        add_meta_box('saveSendQuote', __('Send Quote To :', QUOTEUP_TEXT_DOMAIN), array($this,
                                'sendQuote', ), $quoteup_admin_menu, 'normal');
        do_meta_boxes($quoteup_admin_menu, 'normal', '');
        ?>
                        </div>
                    </div>
                </div>
            <!-- </form> -->
        </div>
                <?php
    }

    public function productDetailsSection()
    {
        $img = QUOTEUP_PLUGIN_URL.'/images/table_header.png';
        ?>
        <div class="productDetailsSection">
            <table class='admin-quote-table generated_for_desktop' id="Quotation" cellspacing='0'>
                <thead class="wdm-quote-head">
                    <tr>
                        <th class="quote-product-remove"></th>
                        <th class="quote-product-image">
                            <img src= '<?php echo $img;
        ?>' class='wdm-prod-img wdm-prod-head-img'/>
                        </th>
                        <th class="quote-product-title"> <?php _e('Product', QUOTEUP_TEXT_DOMAIN) ?> </th>
                        <th class="quote-product-variation"> <?php _e('Variations', QUOTEUP_TEXT_DOMAIN) ?> </th>
                        <th class="quote-product-sku"> <?php _e('SKU', QUOTEUP_TEXT_DOMAIN);
        ?> </th>
                        <th class="quote-product-sale-price"> <?php _e('Price', QUOTEUP_TEXT_DOMAIN);
        ?></th>
                        <th class="quote-product-new-price"> <?php _e('New Price', QUOTEUP_TEXT_DOMAIN);
        ?></th>
                        <th class="quote-product-qty"> <?php _e('Quantity', QUOTEUP_TEXT_DOMAIN);
        ?></th>
                        <th class="quote-product-total"> <?php _e('Total', QUOTEUP_TEXT_DOMAIN);
        ?></th>
                    </tr>                                
                </thead>
                <tbody class="wdmpe-detailtbl-content">
                    <tr class="quoteup-no-product">
                        <td colspan="9" style="text-align: center;"><?php _e('No Products Selected', QUOTEUP_TEXT_DOMAIN);
        ?></td>
                    </tr>                   
                 </tbody>
                    <tfoot>
                        <?php
                        if (version_compare(WC_VERSION, '2.6', '>')) { ?>
                            <td colspan="6"></td>
                        <?php
                        } else { ?>
                            <td colspan="7"></td>
                        <?php
                        }
            ?>
                        <td>Total</td>
                        <td class="quote-final-total"><?php echo wc_price('0');
        ?></td>
                    </tfoot>
            </table>
            <input type="hidden" name="database-amount" id="database-amount" value="0">
            <div>
            <?php
                getProductsSelection()
            ?>
                <div class="quote-expiration-date">
                        <?php
                        if (!isset($form_data[ 'enable_disable_quote' ]) || $form_data[ 'enable_disable_quote' ] != 1) { ?>
                            <div class='wdm-user-expiration-date'>
                                <input type='hidden' name='expiration_date' class="expiration_date_hidden" value=''>
                                <?php
                            ?>
                            <label class="expiration-date left-label"><?php _e('Expiration Date', QUOTEUP_TEXT_DOMAIN) ?> : </label>
                                    <input type='text' value='' class='wdm-input-expiration-date'  readonly  required>
                                    
                                </div>
                    <?php
                        }
        ?>
                    </div>
            </div>    

            <div class="quote-related-options">
                <div class ="show-price-option">
                    <input id="show_price" class="wdm-checkbox" type="checkbox" name="show_price" value= "1" />
                    <label for="show_price"><?php _e('Show Old Price in Quotation', QUOTEUP_TEXT_DOMAIN) ?>   </label>
                </div>
            </div>
        </div>
            <?php
    }

    /**
     * This function is used to display quote form.
     * All the fields on MPE will be displayed in form.
     * We have one extra attribute in array now to display custom field on quote form.
     * include_in_quote_form
     * if 'include_in_quote_form' is not set then field will be displayed
     * if 'include_in_quote_form' is set to 'no' then field will not be displayed
     * if 'include_in_quote_form' is set to 'yes' then field will be displayed.
     */
    public function sendQuote()
    {
        $ajax_nonce = wp_create_nonce('quoteup');
        $url = admin_url();
        ?>
        <div class='wdm-enquiry-form'>
            <form method='post' id='frm_dashboaard_quote' name='frm_dashboaard_quote' class='quoteup-quote-form' >
                <input type='hidden' name='quote_ajax_nonce' id='quote_ajax_nonce' value='<?php echo $ajax_nonce;
        ?>'>
                <input type='hidden' name='submit_value' id='submit_value'>
                <input type='hidden' name='site_url' id='site_url' value='<?php echo $url;
        ?>'>
                <input type='hidden' name='tried' id='tried' value='yes' />
                <div id='wdm_nonce_error'>
                    <div  class='wdmquoteup-err-display'>
                        <span class='wdm-quoteupicon wdm-quoteupicon-exclamation-circle'></span><?php _e('Unauthorized enquiry', QUOTEUP_TEXT_DOMAIN) ?>
                    </div>
                </div>
        <?php
        do_action('quote_add_custom_field_in_form');
        ?>
                <div id="text"></div>
                <div class='form-errors-wrap mpe_form_input' id='wdm-quoteupform-error'>
                    <div class='mpe-left' style='height: 1px;'></div>
                    <div class='mpe-right form-errors'>
                        <ul class='error-list'>
                        </ul>
                    </div>
                </div>
            </form>
        </div>
        <div class='form_input btn_div clearfix'>
                    <!-- <div class='mpe-left' style='height: 1px;'></div>
                    <div class='mpe-right'> -->

                        <input type='button' value='<?php _e('Create Quotation', QUOTEUP_TEXT_DOMAIN) ?>
                            ' name='btnSave'  id='btnQuoteSave' class='quote-save button button-primary'>
                            <span class='load-send-quote-ajax'></span>
                        <input type='button' value='<?php _e('Send Quotation', QUOTEUP_TEXT_DOMAIN) ?>
                            ' name='btnSend'  id='btnQuoteSend' class='quote-send button button-primary'>
                    <!-- </div>
                </div> -->
        <?php
        global $quoteup;
        $quoteup->quoteDetailsEdit->pdfPreviewModal();
        $quoteup->quoteDetailsEdit->addMessageQuoteModal();
    }

    public function updateEnquiryDetailsTable($name, $email, $phone, $subject, $address, $product_details, $msg, $date, $dateField)
    {
        global $wpdb;
        $tbl = getEnquiryDetailsTable();
        if (isset($_POST['globalEnquiryID']) && $_POST['globalEnquiryID'] != 0) {
            $wpdb->update(
                $tbl,
                array(
                'name' => $name,
                'email' => $email,
                'phone_number' => $phone,
                'subject' => $subject,
                'enquiry_ip' => $address,
                'product_details' => serialize($product_details),
                'message' => $msg,
                'enquiry_date' => $date,
                'date_field' => $dateField,
                ),
                array('enquiry_id' => $_POST['globalEnquiryID']),
                array(
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                ),
                array('%d')
            );
            $enquiry_id = $_POST['globalEnquiryID'];
            $wpdb->delete(getEnquiryProductsTable(), array('enquiry_id' => $enquiry_id), array('%d'));
        } else {
            $wpdb->insert(
                $tbl,
                array(
                'name' => $name,
                'email' => $email,
                'phone_number' => $phone,
                'subject' => $subject,
                'enquiry_ip' => $address,
                'product_details' => serialize($product_details),
                'message' => $msg,
                'enquiry_date' => $date,
                'date_field' => $dateField,
                ),
                array(
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                )
            );
            $enquiry_id = $wpdb->insert_id;
        }

        //Update product details in enquiry products table after version 5.0.0
        //After some versions only this table will be used
        updateProductDetails($enquiry_id, $product_details);

        return $enquiry_id;
    }

    public function getVariationDetailsKeyValue($variation_id, $rawVariationDetails)
    {
        $variationDetails = array();
        if (!empty($variation_id) && !empty($rawVariationDetails)) {
            foreach ($rawVariationDetails as $value) {
                $detectVariationDetails = array_map('trim', explode(':', $value));
                if (!empty($detectVariationDetails)) {
                    //$detectVariationDetails[0] has attribute name
                    //$detectVariationDetails[1] has attribute value
                    $variationDetails[$detectVariationDetails[0]] = $detectVariationDetails[1];
                }
            }
        }

        return $variationDetails;
    }

    public function saveQuote()
    {
        $data = $_POST;
        global $quoteup;
        $name = wp_kses($_POST[ 'custname' ], array());
        $email = filter_var($_POST[ 'txtemail' ], FILTER_SANITIZE_EMAIL);
        $phone = quoteupPhoneNumber();
        $dateField = quoteupDateField();
        $subject = '';
        $msg = '';
        $quoteProducts = isset($_POST['quoteProductsData']) ? $_POST['quoteProductsData'] : '';
        $product_details = array();
        $arrayids = array();
        $newprice = array();
        $quantity = array();
        $oldPrice = array();
        $variationIDArray = array();
        $variationsArray = array();
        $variationIndexInEnquiry = array();

        $counter = 0;
        $quoteProducts = json_decode(stripcslashes($quoteProducts));
        $quoteProducts = (array)$quoteProducts;
        $validMedia = validateAttachField($quoteup);

        foreach ($quoteProducts as $key => $value) {
            $value = (array)$value;
            $prod = array();
            $product_id = $value['productID'];
            $variation_id = isset($value['variationID']) ? $value['variationID'] : '';
            $title = get_the_title($product_id);
            $rawVariationDetails = $value['variationDetails'];
            $variationDetails = $this->getVariationDetailsKeyValue($variation_id, $rawVariationDetails);
            $address = getEnquiryIP();
            $variationDetailsForArray = array();
            $type = 'Y-m-d H:i:s';
            $date = current_time($type);
            if ($variation_id != '') {
                $product = wc_get_product($variation_id);
                $sku = $product->get_sku();
                $img = wp_get_attachment_url(get_post_thumbnail_id($variation_id));
                $img_url = getImgUrl($img, $product_id);
                $variationDetailsForArray = getVariationDetailsForArray($variationDetails);
                // end of For Save quotation data in enquiry_quotation table
            } else {
                $product = wc_get_product($product_id);
                $sku = $product->get_sku();
                $img_url = wp_get_attachment_url(get_post_thumbnail_id($product_id));
            }
            array_push($arrayids, $product_id);
            array_push($newprice, $value['productPrice']);
            array_push($oldPrice, $value['salePrice']);
            array_push($quantity, $value['productQty']);
            array_push($variationIDArray, $variation_id);
            array_push($variationsArray, $variationDetailsForArray);
            array_push($variationIndexInEnquiry, $counter);
            $prod = array(
            'id' => $product_id,
            'title' => $title,
            'price' => $value['salePrice'],
            'quant' => $value['productQty'],
            'img' => $img_url,
            'remark' => '',
            'sku' => $sku,
            'variation_id' => $variation_id,
            'variation' => $variationDetails,
            'author_email' => '',
            );
            $prod = apply_filters('wdm_filter_quote_product_data', $prod, $data);
            $product_details[$counter] = $prod;
            ++$counter;
            unset($key);
        }
        $this->checkStock($arrayids, $quantity, $variationIDArray, $variationsArray);
        $enquiry_id = $this->updateEnquiryDetailsTable($name, $email, $phone, $subject, $address, $product_details, $msg, $date, $dateField);
        do_action('create_quote_form_entry_added_in_db', $enquiry_id);
        do_action('quoteup_add_dashboard_custom_field_in_db', $enquiry_id);
        $this->addQuoteFlagTOMeta($enquiry_id);
        $language = $this->getLanguage();
        //add Locale in enquiry meta if WPML is activated
        if (quoteupIsWpmlActive()) {
            addLanguageToEnquiryMeta($enquiry_id, 'enquiry_lang_code', $language);
            addLanguageToEnquiryMeta($enquiry_id, 'quotation_lang_code', $language);
        }
        //End of locale insertion
        
        deleteDirectoryIfExists($enquiry_id);
        uploadAttachedFile($quoteup, $validMedia, $enquiry_id);

        $show_price = $this->getShowPrice();
        $show_pricePDF = $this->getShowPricePDF();
        $quotationData = array(
            'enquiry_id' => $enquiry_id,
            'cname' => $name,
            'email' => $email,
            'id' => $arrayids,
            'newprice' => $newprice,
            'quantity' => $quantity,
            'old-price' => $oldPrice,
            'variations_id' => $variationIDArray,
            'variations' => $variationsArray,
            'show-price' => $show_price,
            'variation_index_in_enquiry' => $variationIndexInEnquiry,
            'security' => $_POST['security'],
            'language' => $language,
            'expiration-date' => $_POST['expiration-date'],
            'previous_enquiry_id' => $_POST['globalEnquiryID'],
            'source' => 'dashboard',
            );

        $PDFData = array(
            'enquiry_id' => $enquiry_id,
            'show-price' => $show_pricePDF,
            'language' => $language,
            );

        QuoteupQuotesEdit::saveQuotation($quotationData);
        $form_data = quoteupSettings();
        $pdfDisplay = isset($form_data['enable_disable_quote_pdf'])?$form_data['enable_disable_quote_pdf']:1;
        if ($pdfDisplay == 1) {
            QuoteupGeneratePdf::generatePdf($PDFData);
        }
        echo json_encode(
            array(
                'enquiry_id'     => $enquiry_id,
                'saveString'    => 'Saved Successfully.',
            )
        );
        die();
    }

    public function getLanguage()
    {
        return isset($_POST['language']) ? $_POST['language'] : '';
    }

    public function getShowPrice()
    {
        return $_POST[ 'show-price' ] == 'yes' ? 'yes' : 'no';
    }

    public function getShowPricePDF()
    {
        return $_POST[ 'show-price' ] == 'yes' ? '1' : '0';
    }

    public function checkStock($arrayids, $quantity, $variationIDArray, $variationsArray)
    {
        if (!QuoteupQuotesEdit::quoteStockManagementCheck($arrayids, $quantity, $variationIDArray, $variationsArray)) {
            throw new Exception('Product Cannot be added in quotation', 1);
        }
    }

    public function addQuoteFlagTOMeta($enquiry_id)
    {
        global $wpdb;
        $metaTbl = getEnquiryMetaTable();
        if (isset($_POST['globalEnquiryID']) && $_POST['globalEnquiryID'] != 0) {
            $wpdb->update(
                $metaTbl,
                array(
                'meta_value' => '1',
                ),
                array(
                    'enquiry_id' => $_POST['globalEnquiryID'],
                    'meta_key' => '_admin_quote_created',
                    ),
                array(
                '%s',
                ),
                array('%d', '%s')
            );
        } else {
            $wpdb->insert(
                $metaTbl,
                array(
                'enquiry_id' => $enquiry_id,
                'meta_key' => '_admin_quote_created',
                'meta_value' => '1',
                ),
                array(
                '%d',
                '%s',
                '%d',
                )
            );
        }
    }
}

$this->quoteCreateQuotation = QuoteupCreateDashboardQuotation::getInstance();
