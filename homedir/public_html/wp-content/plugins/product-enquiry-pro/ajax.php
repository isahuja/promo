<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!defined('DOING_AJAX')) {
    return; // Exit if accessed directly
}
//Ajax for CSV Generation
add_action('wp_ajax_wdm_return_rows', 'quoteupReturnRows');
//Ajax to add products in enquiry cart
add_action('wp_ajax_wdm_add_product_in_enq_cart', 'quoteupAddProductInEnqCart');
add_action('wp_ajax_nopriv_wdm_add_product_in_enq_cart', 'quoteupAddProductInEnqCart');

//Ajax to update cart
add_action('wp_ajax_wdm_update_enq_cart_session', 'quoteupUpdateEnqCartSession');
add_action('wp_ajax_nopriv_wdm_update_enq_cart_session', 'quoteupUpdateEnqCartSession');

//Ajax to Migrate Scripts
add_action('wp_ajax_migrateScript', 'migrateScript');

//Ajax for Nounce Validation
add_action('wp_ajax_quoteupValidateNonce', 'quoteupValidateNonce');
add_action('wp_ajax_nopriv_quoteupValidateNonce', 'quoteupValidateNonce');

//Ajax for submitting enquiry form
add_action('wp_ajax_quoteupSubmitWooEnquiryForm', 'quoteupSubmitWooEnquiryForm');
add_action('wp_ajax_nopriv_quoteupSubmitWooEnquiryForm', 'quoteupSubmitWooEnquiryForm');

//Ajax to send reply to customer
add_action('wp_ajax_wdmSendReply', 'quoteupSendReply');
add_action('wp_ajax_nopriv_wdmSendReply', 'quoteupSendReply');

//Ajax to update customer data
add_action('wp_ajax_modify_user_data', 'quoteupModifyUserQuoteData');

/*
 * Ajax To set the global setting option of add_to_cart to individual product.
 * Ajax To set the global setting option of quoteup_enquiry to individual product.
 */
add_action('wp_ajax_wdm_set_add_to_cart_value', 'quoteupSetAddToCartValue');

/*
 * Ajax to add product in Enquiry/Quote Cart
 */
add_action('wp_ajax_wdm_trigger_add_to_enq_cart', 'wdmTriggerAddToEnqCart');
add_action('wp_ajax_nopriv_wdm_trigger_add_to_enq_cart', 'wdmTriggerAddToEnqCart');
/*
 * Ajax call to fetch variation details and display those.
 */
add_action('wp_ajax_get_variations', 'getVariationsDropdown');

add_action('wp_ajax_woocommerce_wpml_json_search_products_and_variations', array('Includes\Admin\QuoteupCreateDashboardQuotation', 'jsonSearchProductsAndVariations'));

/**
 * Ajax callback for adding products in cart.
 *
 * @return [type] [description]
 */
function wdmTriggerAddToEnqCart()
{
    quoteupAddProductInEnqCart();
}

function getVariationsDropdown()
{
    $variationData = $_POST['perProductDetail'];
    $language = isset($variationData['language']) ? $variationData['language'] : '';
    do_action('quoteup_change_lang', $language);
    ob_start();
    $productID = $variationData['productID'];
    $count = $variationData['count'];
    $variationID = $variationData['variationID'];
    $productImage = $variationData['product_image'];

    if (isset($GLOBALS['product'])) {
        $GLOBALS['oldProduct'] = $GLOBALS['product'];
    }

    //Defining a global variable here because quoteupVariationDropdown() needs a global variable $product
    $GLOBALS[ 'product' ] = wc_get_product($productID);
    /*
     * Below WC 2.6, we also need global $post variable because it is used in variable.php
     */
    if (version_compare(WC_VERSION, '2.6', '<')) {
        $GLOBALS[ 'post' ] = get_post($productID);
    }
    $product = $GLOBALS[ 'product' ];
    // Get Available variations?
    $get_variations = sizeof($product->get_children()) <= apply_filters('woocommerce_ajax_variation_threshold', 30, $product);
    $available_variations = $get_variations ? $product->get_available_variations() : array();

    /*
     * we are using quoteupVariationDropdown() instead of woocommerce_variable_add_to_cart(). quoteupVariationDropdown() is just a copy of woocommerce_variable_add_to_cart() loading our template instead of woocommerce variable.php
     *
     * woocommerce_variable_add_to_cart() includes woocommerce/templates/single-product/add-to-cart/variable.php. This file has a form tag and dropdowns are shown in a form tag. Since we are already inside a table, form tag can not be used here and therefore, we are creating a div tag which is very similar to form tag created in variable.php
     */
    ?>
    <div class="product">
        <div id="variation-<?php echo $count ?>" class="variations_form cart" data-product_id="<?php echo absint($productID);
    ?>" data-product_variations="<?php echo htmlspecialchars(json_encode($available_variations)) ?>">
<?php
            $variation = $variationData[ 'rawVariationAttributes' ];
    quoteupVariationDropdown($count, $variationID, $productImage, $productID, $product, $variation);
    ?>
        </div>
    </div>
    <?php
    $html = ob_get_clean();
    echo json_encode($html);

    //Reset product data
    if (isset($GLOBALS['oldProduct'])) {
        $GLOBALS['product'] = $GLOBALS['oldProduct'];
    }

    do_action('quoteup_reset_lang');
    die;
}

/**
 * Verify nonce for CSV generation.
 *
 * @return [type] [description]
 */
function checkSecurity()
{
    if (!wp_verify_nonce($_POST[ 'security' ], 'quoteup-nonce')) {
        die('SECURITY_ISSUE');
    }

    if (!current_user_can('manage_options')) {
        die('SECURITY_ISSUE');
    }
}

/**
 * This function returns Meta columns to be included in CSV
 * @param  string $metaTable Meta table name
 * @return array            meta columns
 */
function getMetaColumns($metaTable, $form)
{
    global $wpdb;
    $sql = "SELECT DISTINCT meta_key FROM $metaTable";
    $metaKeys = $wpdb->get_col($sql);
    $metaHeadings = array();

    foreach ($metaKeys as $metaKey) {
        switch ($metaKey) {
            case 'enquiry_lang_code':
            case 'quotation_lang_code':
            case '_unread_enquiry':
                break;

            default:
                array_push($metaHeadings, $metaKey);
                break;
        }
    }

    // If quotation module is deactivated
    $del_val = '_admin_quote_created';
    if ($form['enable_disable_quote'] && array_search($del_val, $metaHeadings) !== false) {
        $key = array_search($del_val, $metaHeadings);
        unset($metaHeadings[$key]);
    }
    return $metaHeadings;
}

/**
 * This function sets products to be included in CSV and string to get price from array
 * @param  string &$csvProducts      blank string which will be set as products array
 * @param  string &$price            blank string which will be set as string to get price
 * @param  array $form              settings of product
 * @param  array $individualEnquiry enquiry data
 */
function getCsvProducts(&$csvProducts, &$price, $form, $individualEnquiry)
{
    // If quotation module is activated
    if ($form['enable_disable_quote'] == 0) {
        $csvProducts = getQuoteProducts($individualEnquiry['enquiry_id']);
        $price = 'newprice';
    }

    if (empty($csvProducts)) {
        $csvProducts = getEnquiryProducts($individualEnquiry['enquiry_id']);
        $price = 'price';
    }
}

/**
 * This function returns title of product
 * @param  array $data product details
 * @return string       product title
 */
function getProductTitle($data)
{
    $title = get_the_title($data['product_id']);
    if (empty($title)) {
        $title = get_the_title($data['product_title']);
    }

    if (isset($data[ 'variation_id' ]) && $data[ 'variation_id' ] != '' && $data[ 'variation_id' ] != 0) {
        $variationString = printVariations($data);
        $variationString = preg_replace(']<br>]', ',', $variationString); // Used to replace br tag with ','
        $variationString = preg_replace(']<b>]', '', $variationString); // Used to replace b tag with ''
        $variationString = preg_replace(']</b>]', '', $variationString); // Used to replace </b> tag with ''
        $variationString = preg_replace('],]', '', $variationString, 1); // Used to remove first ,
        $title = $title.' - '. $variationString;
    }

    return $title;
}

/**
 * This function returns meta columns values for each enquiry
 * @param  array $csvData      values of other csv columns
 * @param  array $metaHeadings Meta columns
 * @param  int $enquriyID    enquiry ID
 * @return array               values of CSV columns with meta columns values
 */
function getMetaColumnsValues($csvData, $metaHeadings, $enquriyID, $metaTable)
{
    global $wpdb;

    if (!empty($metaHeadings)) {
        foreach ($metaHeadings as $value) {
            $sql = "SELECT meta_value FROM $metaTable WHERE enquiry_id=$enquriyID AND meta_key = '$value'";
            $result = $wpdb->get_var($sql);
            if ($value == '_admin_quote_created') {
                $value = 'Admin Created Quote';
                if ($result) {
                    $result = 'Yes';
                } else {
                    $result = 'No';
                }
            }
            $csvData[$value] = $result;
        }
    }

    return $csvData;
}

function getTelephoneAndDateFieldStatus($form, &$tel, &$dateFiels)
{
    if (isset($form[ 'enable_telephone_no_txtbox' ])) {
        $tel = $form[ 'enable_telephone_no_txtbox' ];
    }

    if (isset($form[ 'enable_date_field' ])) {
        $dateFiels = $form[ 'enable_date_field' ];
    }
}

/*
 * Callback for CSV generation ajax
 */
if (!function_exists('quoteupReturnRows')) {
    function quoteupReturnRows()
    {
        checkSecurity();
        global $wpdb;
        $ids = array();

        $enquiryTable = getEnquiryDetailsTable();
        $metaTable = getEnquiryMetaTable();
        $form = quoteupSettings();
        $arr = getarr($ids);
        $sql = "SELECT * FROM $enquiryTable ";

        $qry = appendConditionInQUery($arr);

        $sql = $sql.$qry;
        $enquiryData = $wpdb->get_results($sql, ARRAY_A);

        $metaHeadings = getMetaColumns($metaTable, $form);

        $data = array();
        foreach ($enquiryData as $individualEnquiry) {
            $csvProducts = '';
            $price = '';

            getCsvProducts($csvProducts, $price, $form, $individualEnquiry);

            if (empty($csvProducts)) {
                continue;
            }

            $title = getProductTitle($csvProducts[0]);

            $tel = '';
            $dateFiels = '';

            $form = quoteupSettings();

            getTelephoneAndDateFieldStatus($form, $tel, $dateFiels);

            $csvData = array(
                'enquiry ID' => $individualEnquiry['enquiry_id'],
                'Enquiry Date' => $individualEnquiry['enquiry_date'],
                'Enquiry IP' => $individualEnquiry['enquiry_ip'],
                'Customer Name' => $individualEnquiry['name'],
                'Email' => $individualEnquiry['email'],
                'Product Name' => $title,
                'Quantity' => $csvProducts[0]['quantity'],
                'Price' => $csvProducts[0][$price],
                );

            if ($tel) {
                $csvData['Phone Number'] = $individualEnquiry['phone_number'];
            }

            if ($dateFiels) {
                $csvData['Date'] = $individualEnquiry['date_field'];
            }
            $csvData['Subject'] = $individualEnquiry['subject'];
            $csvData['Message'] = $individualEnquiry['message'];

            $enquriyID = $individualEnquiry['enquiry_id'];
            $csvData = getMetaColumnsValues($csvData, $metaHeadings, $enquriyID, $metaTable);

            // If quotation module is activated
            if ($form['enable_disable_quote'] == 0) {
                global $quoteupManageHistory;
                $quoteStatus = $quoteupManageHistory->getLastAddedHistory($individualEnquiry['enquiry_id']);
                if ($quoteStatus != null && is_array($quoteStatus)) {
                    $quoteStatus = $quoteStatus[ 'status' ];
                }
                $csvData['Expiration Date'] = $individualEnquiry['expiration_date'];
                $csvData['Order ID'] = $individualEnquiry['order_id'];
                $csvData['Total'] = $individualEnquiry['total'];
                $csvData['Quote Status'] = $quoteStatus;
            }

            array_push($data, $csvData);

            $firstProduct = true;
            foreach ($csvProducts as $individualProduct) {
                if ($firstProduct) {
                    $firstProduct = false;
                    continue;
                }
                $title = getProductTitle($individualProduct);

                $productsData = array(
                'enquiry ID' => '',
                'Enquiry Date' => '',
                'Enquiry IP' => '',
                'Customer Name' => '',
                'Email' => '',
                'Product Name' => $title,
                'Quantity' => $individualProduct['quantity'],
                'Price' => $individualProduct[$price],
                );

                array_push($data, $productsData);
            }
            
            $blankArray = array();

            array_push($data, $blankArray);
        }
        echo json_encode($data);
        die();
    }
}

/**
 * This function is used to apped the where clause in query depending upon condition.
 *
 * @param [type] $qry [description]
 *
 * @return [type] [description]
 */
function appendConditionInQUery($arr)
{
    $qry = '';
    if (isset($_POST[ 'status' ])) {
        $status = filter_var($_POST[ 'status' ], FILTER_SANITIZE_STRING);
    }
    if (!empty($arr) && $arr != '') {
        $qry = "WHERE enquiry_id in ($arr)";
    } elseif (isset($status) && 'all' != $status) {
        $resultSet = getSqlStatus($status);
        if (isset($resultSet)) {
            $qry = "WHERE enquiry_id in ($resultSet)";
        }
    }

    return $qry;
}

/**
 * This function gives sql query as per status.
 *
 * @return [type] [description]
 */
function getSqlStatus($filter)
{
    global $wpdb;
    $tableName = getEnquiryHistoryTable();

    $sql = "SELECT s1.enquiry_id
                FROM $tableName s1
                LEFT JOIN $tableName s2 ON s1.enquiry_id = s2.enquiry_id
                AND s1.id < s2.id
                WHERE s2.enquiry_id IS NULL AND s1.status ='".$filter."'AND s1.enquiry_id > 0 AND s1.ID > 0";
    $res = $wpdb->get_col($sql);
    if (isset($res)) {
        $resultSet = implode(',', $res);
    }

    return $resultSet;
}

/**
 * get all ids in array for CSV generation.
 *
 * @param [type] $ids [description]
 *
 * @return [type] [description]
 */
function getarr($ids)
{
    if (isset($_POST[ 'ids' ])) {
        $ids = $_POST[ 'ids' ];
    }
    if ($ids == '') {
        $arr = '';
    } else {
        $arr = implode(',', $ids);
    }

    return $arr;
}

/**
 * Trim column names for CSV generation.
 *
 * @param [type] $ids [description]
 *
 * @return [type] [description]
 */
function quoteupTrimNamesOfAllColumns(&$array_item)
{
    $array_item = trim($array_item);
}

/**
 * This function is used to trace through each enquriy id for CSV generation.
 *
 * @param [type] $result  [description]
 * @param [type] $columns [description]
 *
 * @return [type] [description]
 */
function forEachEnquiry($result, $columns)
{
    foreach ($result as &$single_result) {
        $single_result->name = apply_filters('pep_export_csv_customer_name_data', $single_result->name);
        $single_result->name = apply_filters('quoteup_export_csv_customer_name_data', $single_result->name);

        $single_result->email = apply_filters('pep_export_csv_customer_email_data', $single_result->email);
        $single_result->email = apply_filters('quoteup_export_csv_customer_email_data', $single_result->email);

        $single_result->subject = apply_filters('pep_export_csv_subject_data', $single_result->subject);
        $single_result->subject = apply_filters('quoteup_export_csv_subject_data', $single_result->subject);

        $single_result->message = apply_filters('pep_export_csv_message_data', $single_result->message);
        $single_result->message = apply_filters('quoteup_export_csv_message_data', $single_result->message);

        $single_result->product_details = apply_filters('pep_export_csv_product_details_data', $single_result->product_details);
        $single_result->product_details = apply_filters('quoteup_export_csv_product_details_data', $single_result->product_details);

        foreach ($columns as $single_custom_column) {
            $single_result->{$single_custom_column} = apply_filters('pep_export_csv_'.$single_custom_column.'_data', $single_result->{$single_custom_column});
            $single_result->{$single_custom_column} = apply_filters('quoteup_export_csv_'.$single_custom_column.'_data', $single_result->{$single_custom_column});
        }
    }
}


/**
 * This function is used to convert variation name and value in required format
 * array[variation name] = variation value;.
 *
 * @param [type] $variation_detail [description]
 *
 * @return [type] [description]
 */
function getNewVariation($variation_detail)
{
    foreach ($variation_detail as $individualVariation) {
        $keyValue = explode(':', $individualVariation);
        $keyValue[0] = stripslashes($keyValue[0]);
        $keyValue[1] = stripcslashes($keyValue[1]);
        $newVariation[trim($keyValue[0])] = trim($keyValue[1]);
    }

    return $newVariation;
}

function getAuthorMail()
{
    return isset($_POST['author_email']) ? filter_var($_POST['author_email'], FILTER_SANITIZE_EMAIL) : "";
}

/**
 * Callback for Add products to enquiry cart ajax.
 */
function quoteupAddProductInEnqCart()
{
    @session_start();
    $data = $_POST;
    $prod = isset($_SESSION['wdm_product_info']) ? $_SESSION['wdm_product_info'] : '';

    $product_id = filter_var($_POST[ 'product_id' ], FILTER_SANITIZE_NUMBER_INT);
    $prod_quant = filter_var($_POST[ 'product_quant' ], FILTER_SANITIZE_NUMBER_INT);
    $title = get_the_title($product_id);
    $remark = isset($_POST[ 'remark' ]) ? $_POST[ 'remark' ] : '';
    $id_flag = 0;
    $counter = 0;
    $authorEmail = getAuthorMail();
    $variation_id = $_POST['variation'];
    $variation_detail = '';

    //Variable Product
    if ($variation_id != '') {
        $product = wc_get_product($variation_id);
        $sku = $product->get_sku();
        $variation_detail = $_POST['variation_detail'];
        $variation_detail = getNewVariation($variation_detail);
        $price = quoteupGetPriceToDisplay($product);
        $img = wp_get_attachment_url(get_post_thumbnail_id($variation_id));
        if ($img != '') {
            $img_url = $img;
        } else {
            $img_url = wp_get_attachment_url(get_post_thumbnail_id($product_id));
        }
    } else {
        $product = wc_get_product($product_id);
        $price = quoteupGetPriceToDisplay($product);

        $sku = $product->get_sku();
        $img_url = wp_get_attachment_url(get_post_thumbnail_id($product_id));
    }
    //End of Variable Product

    $flag_counter = setFlag($product_id, $id_flag, $counter, $variation_detail, $variation_id);
    $id_flag = $flag_counter[ 'id_flag' ];
    $counter = $flag_counter[ 'counter' ];

    if ($id_flag == 0) {
        if (isset($_SESSION[ 'wdm_product_count' ])) {
            if ($_SESSION[ 'wdm_product_count' ] != '') {
                $counter = $_SESSION[ 'wdm_product_count' ];
            }
        }
        $prod[ $counter ] = array('id' => $product_id,
            'title' => $title,
            'price' => $price,
            'quant' => $prod_quant,
            // 'img' => $img_url,
            'remark' => $remark,
            // 'sku' => $sku,
            'variation_id' => $variation_id,
            'variation' => $variation_detail,
            'author_email' => $authorEmail,
            );
        $prod[ $counter ] = apply_filters('wdm_filter_product_data', $prod[ $counter ], $data);
        $_SESSION[ 'wdm_product_info' ] = $prod;
        getWpmlLanguage();
        setProductCount();
    } else {
        setProductInfo($counter, $remark, $prod_quant, $price);
    }
    echo $_SESSION[ 'wdm_product_count' ];
    die;
}

/**
 * This function is used to set cart language if WPML is active.
 *
 * @return [type] [description]
 */
function getWpmlLanguage()
{
    $_SESSION[ 'wdm_cart_language' ] = $_POST['language'];
}

/**
 * This function is used to update total count of products in cart.
 *
 * @param [int] $productCount [total number of products in cart]
 */
function setProductCount()
{
    if (isset($_SESSION[ 'wdm_product_count' ]) && !empty($_SESSION[ 'wdm_product_count' ]) && is_int($_SESSION[ 'wdm_product_count' ])) {
        $_SESSION[ 'wdm_product_count' ] = $_SESSION[ 'wdm_product_count' ] + 1;
    } else {
        $_SESSION[ 'wdm_product_count' ] = 1;
    }
}

/**
 * This function is used to update remart, product quantity and price in cart.
 *
 * @param [type] $counter    [description]
 * @param [type] $remark     [description]
 * @param [type] $prod_quant [description]
 * @param [type] $price      [description]
 */
function setProductInfo($counter, $remark, $prod_quant, $price)
{
    if ($remark != '') {
        $_SESSION[ 'wdm_product_info' ][ $counter ][ 0 ][ 'remark' ] = $remark;
    }
    $_SESSION[ 'wdm_product_info' ][ $counter ][ 0 ][ 'quant' ] += $prod_quant;
    $_SESSION[ 'wdm_product_info' ][ $counter ][ 0 ][ 'price' ] = $price;
}

/**
 * Checks whether product has already been added to Enquiry/Quote cart.
 *
 * If product is already there in the Enquiry cart, returns id_flag as 1, else returns id_flag as 0
 */
function setFlag($product_id, $id_flag, $counter, $variation_detail, $variation_id)
{
    if (isset($_SESSION[ 'wdm_product_info' ]) && !empty($_SESSION[ 'wdm_product_info' ])) {
        for ($search = 0; $search < count($_SESSION[ 'wdm_product_info' ]); ++$search) {
            if ($product_id == $_SESSION[ 'wdm_product_info' ][ $search ][ 'id' ]) {
                if ($variation_detail != '' && $variation_id != '') {
                    if ($_SESSION['wdm_product_info'][$search]['variation'] == $variation_detail && $_SESSION['wdm_product_info'][$search]['variation_id'] == $variation_id) {
                        $id_flag = 1;
                        $counter = $search;
                    }
                } else {
                    $id_flag = 1;
                    $counter = $search;
                }
            }
        }
    }

    return array(
        'id_flag' => $id_flag,
        'counter' => $counter,
    );
}

/**
 * THis function returns the quantity to update in session.
 *
 * @param [int] $pid [Product ID]
 *
 * @return [type] [description]
 */
function getQuantity()
{
    if (isset($_POST[ 'clickcheck' ]) && $_POST[ 'clickcheck' ] == 'remove') {
        $quant = 0;
    } else {
        $quant = filter_var($_POST[ 'quantity' ], FILTER_SANITIZE_NUMBER_INT);
    }

    return $quant;
}

/**
 * Callback for Update cart ajax.
 */
function quoteupUpdateEnqCartSession()
{
    @session_start();
    $pid = filter_var($_POST[ 'product_id' ], FILTER_SANITIZE_NUMBER_INT);
    $vid = filter_var($_POST['product_var_id'], FILTER_SANITIZE_NUMBER_INT);
    $variation_detail = $_POST['variation'];
    if (!empty($vid)) {
        foreach ($variation_detail as $key => $value) {
            $variation_detail[$key] = stripcslashes($value);
        }
    }
    $quant = getQuantity();
    if (isset($_POST[ 'remark' ])) {
        $remark = stripcslashes($_POST[ 'remark' ]);
    }
    $product = wc_get_product($pid);
    $pri = quoteupGetPriceToDisplay($product);
    $price = $product->get_price_html();
    $priceStatus = get_post_meta($pid, '_enable_price', true);
    for ($search = 0; $search < count($_SESSION[ 'wdm_product_info' ]); ++$search) {
        if ($pid == $_SESSION[ 'wdm_product_info' ][ $search ][ 'id' ]) {
            if ($vid != '') {
                if ($_SESSION['wdm_product_info'][$search]['variation_id'] == $vid && $_SESSION['wdm_product_info'][$search]['variation'] == $variation_detail) {
                    if ($quant == 0) {
                        array_splice($_SESSION['wdm_product_info'], $search, 1);
                        $_SESSION['wdm_product_count'] = $_SESSION['wdm_product_count'] - 1;
                    } else {
                        $product = wc_get_product($vid);
                        $pri = quoteupGetPriceToDisplay($product);
                        $price = $product->get_price_html();
                        $price = wc_price($pri * $quant);
                        if ($priceStatus == 'yes') {
                            echo json_encode(array('product_id' => $pid, 'variation_id' => $vid,  'variation_detail' => $variation_detail,  'price' => $price));
                        } else {
                            echo json_encode(array('product_id' => $pid, 'variation_id' => $vid, 'variation_detail' => $variation_detail, 'price' => '-'));
                        }
                        $_SESSION['wdm_product_info'][$search]['quant'] = $quant;
                        $_SESSION[ 'wdm_product_info' ][ $search ][ 'price' ] = $pri;
                        $_SESSION['wdm_product_info'][$search]['remark'] = $remark;
                    }
                }
            } else {
                if ($quant == 0) {
                    array_splice($_SESSION['wdm_product_info'], $search, 1);
                    $_SESSION['wdm_product_count'] = $_SESSION['wdm_product_count'] - 1;
                } else {
                    $price = wc_price($pri * $quant);
                    if ($priceStatus == 'yes') {
                        echo json_encode(array('product_id' => $pid, 'price' => $price));
                    } else {
                        echo json_encode(array('product_id' => $pid, 'price' => '-'));
                    }
                    $_SESSION['wdm_product_info'][$search]['quant'] = $quant;
                    $_SESSION[ 'wdm_product_info' ][ $search ][ 'price' ] = $pri;
                    $_SESSION['wdm_product_info'][$search]['remark'] = $remark;
                }
            }
        }
    }
    if ($_SESSION['wdm_product_count'] == 0) {
        unset($_SESSION['wdm_cart_language']);
    }
    die();
}

/*
 * Callback for script migration ajax
 */
if (!function_exists('migrateScript')) {
    function migrateScript()
    {
        if (!wp_verify_nonce($_POST[ 'security' ], 'migratenonce')) {
            die('SECURITY_ISSUE');
        }

        if (!current_user_can('manage_options')) {
            die('SECURITY_ISSUE');
        }

        $migrated = get_option('wdm_enquiries_migrated');
        if ($migrated != 1) {
            global $wpdb;
            $enquiry_tbl = $wpdb->prefix.'enquiry_details';
            $enquiry_tbl_new = getEnquiryDetailsTable();
            $enquiry_meta_tbl = getEnquiryMetaTable();
            $enquiries = $wpdb->get_results("SELECT * FROM {$enquiry_tbl}");
            foreach ($enquiries as $enquiry) {
                $pid = $enquiry->product_id;
                $pname = $enquiry->product_name;
                $psku = $enquiry->product_sku;
                $price = get_post_meta($pid, '_regular_price', true);
                $id = $enquiry->enquiry_id;
                $sql = $wpdb->prepare("select meta_key,meta_value FROM {$enquiry_meta_tbl} WHERE enquiry_id=%d", $id);
                $meta = $wpdb->get_results($sql);

                $cust_name = $enquiry->name;
                $cust_email = $enquiry->email;
                $ip = $enquiry->enquiry_ip;
                $dt = $enquiry->enquiry_date;
                $sub = $enquiry->subject;
                $number = $enquiry->phone_number;
                $msg = $enquiry->message;
                $img_url = wp_get_attachment_url(get_post_thumbnail_id($pid));

                $products_arr = array();
                $products_arr[][ 0 ] = array('id' => $pid, 'title' => $pname, 'quant' => 1, 'sku' => $psku, 'img' => $img_url, 'price' => $price, 'remark' => '');
                $record = serialize($products_arr);
                $wpdb->insert(
                    $enquiry_tbl_new,
                    array('name' => $cust_name,
                    'email' => $cust_email,
                    'message' => $msg,
                    'phone_number' => $number,
                    'subject' => $sub,
                    'enquiry_ip' => $ip,
                    'product_details' => $record,
                    'enquiry_date' => $dt,
                    ),
                    array('%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    )
                );
                echo $insert_id = $wpdb->insert_id;

                foreach ($meta as $pair) {
                    $key = $pair->meta_key;
                    $value = $pair->meta_value;
                    $wpdb->insert(
                        $enquiry_meta_tbl,
                        array(
                        'enquiry_id' => $insert_id,
                        'meta_key' => $key,
                        'meta_value' => $value,
                        ),
                        array('%d',
                        '%s',
                        '%s',
                        )
                    );
                }
            }
            update_option('wdm_enquiries_migrated', 1);

            $table_name = getEnquiryHistoryTable();
            $enquiryDetailTable = getEnquiryDetailsTable();
            $sql = "SELECT enquiry_id,enquiry_date FROM  $enquiryDetailTable WHERE  enquiry_id NOT IN (SELECT enquiry_id FROM $table_name)";
            $oldEnquiryIDs = $wpdb->get_results($sql, ARRAY_A);
            foreach ($oldEnquiryIDs as $enquiryID) {
                $enquiry = $enquiryID[ 'enquiry_id' ];
                $date = $enquiryID[ 'enquiry_date' ];
                $table_name = getEnquiryHistoryTable();
                $performedBy = null;
                $wpdb->insert(
                    $table_name,
                    array(
                    'enquiry_id' => $enquiry,
                    'date' => $date,
                    'message' => '-',
                        'status' => 'Requested',
                    'performed_by' => $performedBy,
                    )
                );
            }
        }

        die();
    }
}

/**
 * Callback for nonce ajax.
 */
function quoteupValidateNonce()
{
    echo check_ajax_referer('nonce_for_enquiry', 'security', false);
    die();
}

function addLanguageToEnquiryMeta($enquiryID, $metaKey, $currentLocale)
{
    global $wpdb;
    $metaTbl = getEnquiryMetaTable();

    if (isset($_POST['globalEnquiryID']) && $_POST['globalEnquiryID'] != 0) {
        $wpdb->update(
            $metaTbl,
            array(
                'meta_value' => $currentLocale,
                ),
            array(
                    'enquiry_id' => $_POST['globalEnquiryID'],
                    'meta_key' => $metaKey,
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
            'enquiry_id' => $enquiryID,
            'meta_key' => $metaKey,
            'meta_value' => $currentLocale,
            ),
            array(
            '%d',
            '%s',
            '%s',
            )
        );
    }
}


function addEnquiryMeta($enquiryID, $metaKey, $metaValue)
{
    global $wpdb;
    $metaTbl = getEnquiryMetaTable();

    if (isset($_POST['globalEnquiryID']) && $_POST['globalEnquiryID'] != 0) {
        $wpdb->update(
            $metaTbl,
            array(
                'meta_value' => $metaValue,
                ),
            array(
                    'enquiry_id' => $_POST['globalEnquiryID'],
                    'meta_key' => $metaKey,
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
            'enquiry_id' => $enquiryID,
            'meta_key' => $metaKey,
            'meta_value' => $metaValue,
            ),
            array(
            '%d',
            '%s',
            '%s',
            )
        );
    }
}

/**
 * Callback for submitting enquiry form ajax.
 */
function quoteupSubmitWooEnquiryForm()
{
    @session_start();
    if (isset($_POST[ 'security' ]) && wp_verify_nonce($_POST[ 'security' ], 'nonce_for_enquiry')) {
        $form_data = quoteupSettings();
        if (isset($form_data['enable_google_captcha']) && $form_data['enable_google_captcha'] == 1) {
            $secretKey = $form_data[ 'google_secret_key' ];
            $response= isset($_POST["captcha"])?$_POST["captcha"] : '';
            $verify=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$response}");
            $captcha_success=json_decode($verify);
            if (!$captcha_success->success) {
                //This user was not verified by recaptcha.
                echo json_encode(
                    array(
                    'status'     => 'failed',
                    'message'    => __('could not be verified by captcha', QUOTEUP_TEXT_DOMAIN),
                    )
                );
                die();
            }
        }
        global $wpdb, $quoteup;
        $name = wp_kses($_POST[ 'custname' ], array());
        $email = filter_var($_POST[ 'txtemail' ], FILTER_SANITIZE_EMAIL);
        $phone = quoteupPhoneNumber();
        $dateField = quoteupDateField();
        $subject = '';
        $authorEmail = '';
        if (isset($_POST[ 'txtsubject' ])) {
            $subject = wp_kses($_POST[ 'txtsubject' ], array());
            $subject = stripcslashes($subject);
            $subject = (strlen($subject) > 50) ? substr($subject, 0, 47).'...' : $subject;
        }

        $validMedia = validateAttachField($quoteup);

        $msg = wp_kses($_POST[ 'txtmsg' ], array());
        $msg = stripcslashes($msg);
        $product_table_and_details = getEmailAndDbDataOfProducts($form_data);
        $product_details = setProductDetails($product_table_and_details);
        $authorEmail = setAuthorEmail($product_table_and_details);
        $address = getEnquiryIP();
        $type = 'Y-m-d H:i:s';
        $date = current_time($type);
        $tbl = getEnquiryDetailsTable();

        if ($wpdb->insert(
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
        )
        ) {
            $enquiryID = $wpdb->insert_id;
            updateProductDetails($enquiryID, $product_details);
            do_action('mpe_form_entry_added_in_db', $enquiryID);
            do_action('quoteup_form_entry_added_in_db', $enquiryID);
            do_action('pep_form_entry_added_in_db', $enquiryID);
            do_action('quoteup_create_custom_field');
            do_action('pep_create_custom_field');
            do_action('quoteup_add_custom_field_in_db', $enquiryID);
            do_action('pep_add_custom_field_in_db', $enquiryID);

            deleteDirectoryIfExists($enquiryID);
            uploadAttachedFile($quoteup, $validMedia, $enquiryID);


            //add Locale in enquiry meta
            addLanguageToEnquiryMeta($enquiryID, 'enquiry_lang_code', $_POST['wdmLocale']);
            addLanguageToEnquiryMeta($enquiryID, 'quotation_lang_code', $_POST['wdmLocale']);
            //End of locale insertion
            
            addEnquiryMeta($enquiryID, '_unread_enquiry', 'yes');

            $emailObject = Includes\Frontend\SendEnquiryMail::getInstance($enquiryID, $authorEmail, $subject);
            do_action('quoteup_send_enquiry_email', $emailObject);
            $_SESSION[ 'wdm_product_info' ] = '';
            $_SESSION[ 'wdm_product_count' ] = 0;
            unset($_SESSION['wdm_cart_language']);
            unset($_SESSION[ 'wdm_product_info' ]);
            do_action('quoteup_after_enquiry_email', $enquiryID);
        }
    }
    echo json_encode(
        array(
        'status'     => 'COMPLETED',
        'message'    => 'COMPLETED',
        )
    );
    die();
}

/**
 * This function is used to validate files if attach field is activated.
 * @param  [type] $quoteup [description]
 * @return [type]          [description]
 */
function validateAttachField($quoteup)
{
    $validMedia = false;
    if (isset($_FILES) && !empty($_FILES)) {
        $validMedia = $quoteup->QuoteupFileUpload->validateFileUpload();
    }
    return $validMedia;
}

/**
 * This function is used to delete existing folder of files if exists
 * @param  int $enquiryID enquiry id of current enquiry
 */
function deleteDirectoryIfExists($enquiryID)
{
    $upload_dir = wp_upload_dir();
    $path = $upload_dir[ 'basedir' ].'/QuoteUp_Files/';
    $files = glob($path.$enquiryID.'/*'); // get all file names
    foreach ($files as $file) { // iterate files
        if (is_file($file)) {
            unlink($file); // delete file
        }
    }
}

/**
 * This function is used to upload files if attach field is activated
 * @param  obejct $quoteup    Global object for classes
 * @param  boolean $validMedia true if media is valid
 * @param  int $enquiryID  enquiry id of current enquiry
 * @return [type]             [description]
 */
function uploadAttachedFile($quoteup, $validMedia, $enquiryID)
{
    $success = true;
    if (isset($_FILES) && !empty($_FILES) && $validMedia) {
        $success =  $quoteup->QuoteupFileUpload->quoteupUploadFiles($enquiryID);
        if (!$success) {
            echo json_encode(
                array(
                'status'     => 'failed',
                'message'    => __('Some issue with file upload', QUOTEUP_TEXT_DOMAIN),
                )
            );
            die();
        }
    }
}




/**
 * This function is used to set author mail.
 *
 * @param [type] $product_table_and_details [description]
 */
function setAuthorEmail($product_table_and_details)
{
    if (isset($product_table_and_details[ 'authorEmail' ])) {
        $authorEmail = $product_table_and_details[ 'authorEmail' ];
    } else {
        $authorEmail = '';
    }

    return $authorEmail;
}

/**
 * set phone number
 * sets phone number if entered by customer or keeps it blank.
 *
 * @return [type] [description]
 */
function quoteupPhoneNumber()
{
    if (isset($_POST[ 'txtphone' ])) {
        $phone = filter_var($_POST[ 'txtphone' ], FILTER_SANITIZE_NUMBER_INT);
    } else {
        $phone = '';
    }

    return $phone;
}

/**
 * set Date Field Value
 * sets Date field value if entered by customer or keeps it blank.
 *
 * @return [type] [description]
 */
function quoteupDateField()
{
    if (isset($_POST[ 'txtdate' ])) {
        $dateField = $_POST[ 'txtdate' ];
        if (!empty($dateField)) {
            $dateField = date('Y-m-d', strtotime($dateField));
        }
    } else {
        $dateField = null;
    }

    return $dateField;
}

/**
 * This function is used to get img url of product.
 *
 * @param [type] $img [description]
 *
 * @return [type] [description]
 */
function getImgUrl($img, $product_id)
{
    if ($img != '') {
        $img_url = $img;
    } else {
        $img_url = wp_get_attachment_url(get_post_thumbnail_id($product_id));
    }

    return $img_url;
}

/**
 * This function is used to get variation details of the product.
 *
 * @param [type] $variation_detail [description]
 *
 * @return [type] [description]
 */
function getVariationDetails($variation_detail)
{
    $variation_detail = explode(",", $variation_detail);
    foreach ($variation_detail as $individualVariation) {
        $keyValue = explode(':', $individualVariation);
        $newVariation[trim($keyValue[0])] = trim($keyValue[1]);
    }

    return $newVariation;
}

/**
 * Returns email content to be sent to customer and admin. It also returns content
 * to be saved in the database. Checks whether multi product enquiry mode is enabled
 * or not and returns data accordingly.
 *
 * @param [array] $form_data     [settings stored by admin]
 * @param [type]  $product_table [description]
 */
function getEmailAndDbDataOfProducts($form_data)
{
    @session_start();
    $product_details = '';
    $authorEmail = array();
    if (isset($form_data[ 'enable_disable_mpe' ]) && $form_data[ 'enable_disable_mpe' ] == 1) {
        $product_details = getEnquirySessionProductsDetails();
        foreach ($_SESSION[ 'wdm_product_info' ] as $arr) {
            array_push($authorEmail, $arr['author_email']);
        }
    } else {
        $product_id = $_POST[ 'product_id' ];
        $prod_quant = filter_var($_POST[ 'product_quant' ], FILTER_SANITIZE_NUMBER_INT);
        $title = get_the_title($product_id);
        $variation_id = filter_var($_POST['variation_id'], FILTER_SANITIZE_NUMBER_INT);
        $variation_detail = '';
        $authorEmail = array();
        array_push($authorEmail, isset($_POST[ 'uemail' ]) ? $_POST[ 'uemail' ] : '');

    //Variable Product
        if ($variation_id != '') {
            $product = wc_get_product($variation_id);
            $sku = $product->get_sku();
            $variation_detail = $_POST['variation_detail'];
            $price = $product->get_price();
            $img = wp_get_attachment_url(get_post_thumbnail_id($variation_id));
            $img_url = getImgUrl($img, $product_id);
            $variation_detail = getVariationDetails($variation_detail);
        } else {
            $product = wc_get_product($product_id);
            $enable_price = get_post_meta($product_id, '_enable_price', true);
            $price = $product->get_price();
            $sku = $product->get_sku();
            $img_url = wp_get_attachment_url(get_post_thumbnail_id($product_id));
        }
    //End of Variable Product
        $enable_price = get_post_meta($product_id, '_enable_price', true);
        $prod[] = array('id' => $product_id,
            'title' => $title,
            'price' => $price,
            'quant' => $prod_quant,
            'img' => $img_url,
            'remark' => '',
            'sku' => $sku,
            'variation_id' => $variation_id,
            'variation' => $variation_detail, );

        $product_details = $prod;
    }

    return array(
        'product_details' => $product_details,
        'authorEmail' => $authorEmail,
    );
}


function getEnquirySessionProductsDetails()
{
    @session_start();
    $data = array();
    foreach ($_SESSION[ 'wdm_product_info' ] as $key => $value) {
        if ($value['variation_id'] != '') {
            $product = wc_get_product($value['variation_id']);
            $price = $product->get_price();
        } else {
            $product = wc_get_product($value['id']);
            $price = $product->get_price();
        }
        $value['price'] = $price;
        array_push($data, $value);
        unset($key);
    }
    return $data;
}

/**
 * Set product details from an array.
 *
 * @param [type] $product_table_and_details [description]
 */
function setProductDetails($product_table_and_details)
{
    if (isset($product_table_and_details[ 'product_details' ])) {
        $product_details = $product_table_and_details[ 'product_details' ];
    } else {
        $product_details = '';
    }

    return $product_details;
}

/**
 * Get IP of client.
 *
 * @return [type] [description]
 */
function getEnquiryIP()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        //check ip from share internet
        $address = $_SERVER[ 'HTTP_CLIENT_IP' ];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        //to check ip is pass from proxy
        $address = $_SERVER[ 'HTTP_X_FORWARDED_FOR' ];
        $address = explode(',', $address);
        $address = $address[0];
    } else {
        $address = $_SERVER[ 'REMOTE_ADDR' ];
    }

    return $address;
}

/*
 * Function to check input currency and return only sale price
 * @param  [string] $original_price Original string containing price.
 * @return [int]                    Sale price
 */
if (!function_exists('getSalePrice')) {
    function getSalePrice($original_price)
    {
        // Trim spaces
        $original_price = trim($original_price);
        // Extract Sale Price
        $price = extractSalePrice($original_price);
        $sanitized_price = filter_var($price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        if (!$sanitized_price) {
            return $original_price;
        }

        return $sanitized_price;
    }
}

if (!function_exists('extractSalePrice')) {
    function extractSalePrice($price)
    {
        //Check if more than 1 value is present
        $prices = explode(' ', $price);
        if (count($prices) > 1) {
            return $prices[ 1 ];   // If yes return sale price.
        }

        return $prices[ 0 ]; //  Else return same string.
    }
}

/*
 * To set the global setting option of add_to_cart to individual product. 
 * To set the global setting option of quoteup_enquiry to individual product.
 * To set the global setting option of show price to individual product.
 */
function quoteupSetAddToCartValue()
{
    if (!current_user_can('manage_options')) {
        die('SECURITY_ISSUE');
    }
    $add_to_cart_option = $_POST[ 'option_add_to_cart' ];
    $global_quoteup_enquiry_option = $_POST[ 'option_quoteup_enquiry' ];
    $quoteup_price = $_POST[ 'option_quoteup_price' ];
    if ($global_quoteup_enquiry_option == 'yes') {
        $individual_quoteup_enquiry_option = 'yes';
    }
    if ($global_quoteup_enquiry_option == 'no') {
        $individual_quoteup_enquiry_option = '';
    }

    global $post;

    $args = array(
        'post_type' => 'product',
        'posts_per_page' => '-1',
    );
    $wp_query = new WP_Query($args);

    if ($wp_query->have_posts()) :
        while ($wp_query->have_posts()) :
            $wp_query->the_post();

            $product = get_product($post->ID);
            update_post_meta($post->ID, '_enable_add_to_cart', $add_to_cart_option);
            update_post_meta($post->ID, '_enable_pep', $individual_quoteup_enquiry_option);
            update_post_meta($post->ID, '_enable_price', $quoteup_price);
        endwhile;
    endif;

    die();
    unset($product);
}

/*
 * Ajax callback to modify user name and email on enquiry/quote edit page
 */
if (!function_exists('quoteupModifyUserQuoteData')) {
    function quoteupModifyUserQuoteData()
    {
        if (!current_user_can('manage_options')) {
            die('SECURITY_ISSUE');
        }

        global $wpdb;
        $enq_tbl = getEnquiryDetailsTable();
        $name = filter_var($_POST[ 'cname' ], FILTER_SANITIZE_STRING);
        $email = filter_var($_POST[ 'email' ], FILTER_SANITIZE_EMAIL);
        $enquiry_id = filter_var($_POST[ 'enquiry_id' ], FILTER_SANITIZE_NUMBER_INT);
        $wpdb->update(
            $enq_tbl,
            array(
            'name' => $name, // string
            'email' => $email, // integer (number)
            ),
            array('enquiry_id' => $enquiry_id),
            array(
            '%s',
            '%s',
            ),
            array('%d')
        );
        echo 'Saved Successfully.';
        die;
    }
}

/*
 * Ajax callback to send reply from enquiry edit page
 */
if (!function_exists('quoteupSendReply')) {
    function quoteupSendReply()
    {
        global $wpdb, $quoteupEmail;

        $wdm_reply_message = getEnquiryThreadTable();
        $uemail = filter_var($_POST[ 'email' ], FILTER_SANITIZE_EMAIL);
        $subject = wp_kses($_POST[ 'subject' ], array());
        $subject = stripcslashes($subject);
        $message = wp_kses($_POST[ 'msg' ], array());
        $message = stripcslashes($message);
        $enquiryID = filter_var($_POST[ 'eid' ], FILTER_SANITIZE_NUMBER_INT);
        $type = 'Y-m-d H:i:s';
        $date = current_time($type);
        $parent = filter_var($_POST[ 'parent_id' ], FILTER_SANITIZE_NUMBER_INT);
        $email_data = quoteupSettings();
        $admin_emails = array();
        if ($email_data[ 'user_email' ] != '') {
            $admin_emails = explode(',', $email_data[ 'user_email' ]);
        }
        $admin_emails = array_map('trim', $admin_emails);
        $admin = get_option('admin_email');
        if (!in_array($admin, $admin_emails)) {
            $admin_emails[] = $admin;
        }
        if (class_exists('Postman')) {
            $emails = implode(';', $admin_emails);
        } else {
            $emails = implode(',', $admin_emails);
        }
        $client_headers[] = 'Content-Type: text/html; charset=UTF-8';
        $client_headers[] = 'MIME-Version: 1.0';
        $client_headers[] = "Reply-to: {$emails}";

        $uemail = apply_filters('quoteup_send_reply_email', $uemail);
        $subject = apply_filters('quoteup_subject', $subject);
        $message = apply_filters('quoteup_msg', $message);
        $client_headers = apply_filters('quoteup_client_headers', $client_headers);

        $insertId = $wpdb->insert(
            $wdm_reply_message,
            array(
            'enquiry_id' => $enquiryID,
            'subject' => $subject,
            'message' => $message,
            'parent_thread' => $parent,
            'date' => $date,
            ),
            array(
            '%d',
            '%s',
            '%s',
            '%d',
            '%s',
            )
        );
        do_action('quoteup_after_send_reply', $insertId);
        echo $enquiryID;

        $quoteupEmail->send($uemail, $subject, $message, $client_headers);
        die();
    }
}
