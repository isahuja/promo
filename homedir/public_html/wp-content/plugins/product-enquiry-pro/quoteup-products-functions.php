<?php

/**
 * This function generates hash products
 * @param  int $productID     Product ID of product
 * @param  int $variationID   Variation ID of product(if variable product)
 * @param  array $variationData Variation data of product (if variable product)
 * @return [type]                [description]
 */
function generateProductHash($productID, $variationID = 0, $variationData = array())
{
    if (!$variationID) {
        return md5($productID);
    } else {
        return md5($variationID.'_'.implode('_', $variationData));
    }
}

/**
 * This function is used to add attribute_ prefix to the label of variations.
 * @param  [type] $variation_id [description]
 * @param  [type] $variation    [description]
 * @return [type]               [description]
 */
function sanitizeVariationLabel($variation_id, $variation)
{
    $variationData = array();
    if ($variation_id) {
        $setAttributeName = function ($value) {
            return 'attribute_'.$value;
        };
        //Sets attribute_ prefix to all keys of an array
        $variationData = array_combine(
            array_map($setAttributeName, array_keys($variation)),
            $variation
        );
    }

    return $variationData;
}


/**
 * This function returns enquiry data of particular enquiry or array of enquiry id
 */
function getEnquiryData($enquiryId = 0)
{
    global $wpdb;
    $enquiryTable = getEnquiryDetailsTable();
    if (is_array($enquiryId)) {
        $enquiryIDs = implode(',', $enquiryId);
        $sql = "SELECT * FROM $enquiryTable WHERE enquiry_id IN ($enquiryIDs)";
        $results = $wpdb->get_results($sql, ARRAY_A);
    } elseif ($enquiryId == 0) {
        $sql = "SELECT * FROM $enquiryTable";
        $results = $wpdb->get_results($sql, ARRAY_A);
    } else {
        $sql = "SELECT * FROM $enquiryTable WHERE enquiry_id = $enquiryId";
        $results = $wpdb->get_row($sql, ARRAY_A);
    }
    return $results;
}


/**
 * This function returns enquiry products of particular enquiry or array of enquiry id
 */
function getEnquiryProducts($enquiryId)
{
    $enquiryProductsTable = getEnquiryProductsTable();
    return quoteupGetProducts($enquiryId, $enquiryProductsTable);
}

/**
 * This function returns enquiry products of particular enquiry or array of enquiry id
 */
function getQuoteProducts($enquiryId)
{
    $quoteProductsTable = getQuotationProductsTable();
    return quoteupGetProducts($enquiryId, $quoteProductsTable);
}

/**
 * This function returns the products data
 */
function quoteupGetProducts($enquiryId, $tableName)
{
    global $wpdb;
    if (is_array($enquiryId)) {
        $enquiryIDs = implode(',', $enquiryId);
        $sql = "SELECT * FROM $tableName WHERE enquiry_id IN ($enquiryIDs)";
        $results = $wpdb->get_results($sql, ARRAY_A);
    } else {
        $sql = "SELECT * FROM $tableName WHERE enquiry_id = $enquiryId";
        $results = $wpdb->get_results($sql, ARRAY_A);
    }
    return $results;
}

function getEnquiryMeta($enquiryId, $metaKey)
{
    global $wpdb;
    $metaTbl = $wpdb->prefix.'enquiry_meta';
    $sql = $wpdb->prepare("SELECT meta_value FROM $metaTbl WHERE meta_key=%s AND enquiry_id=%d", $metaKey, $enquiryId);
    return $wpdb->get_var($sql);
}

function updateProductDetails($enquiryID, $product_details)
{
    global $wpdb;
    $enquiryProductsTable = getEnquiryProductsTable();
    foreach ($product_details as $singleProduct) {
        $productID = $singleProduct['id'];
        $title = $singleProduct['title'];
        $price = $singleProduct['price'];
        $quantity = $singleProduct['quant'];
        $remark = $singleProduct['remark'];
        $variation_id = $singleProduct['variation_id'];
        $variation = $singleProduct['variation'];
    // function to sanitize variations with prefix attribute_ for generating hash
        $variationData = sanitizeVariationLabel($variation_id, $variation);
        $product_hash = GenerateProductHash($productID, $variation_id, $variationData);

        $wpdb->insert(
            $enquiryProductsTable,
            array(
                'enquiry_id' => $enquiryID,
                'product_id' => $productID,
                'product_title' => $title,
                'price' => $price,
                'quantity' => $quantity,
                'remark' => $remark,
                'variation_id' => $variation_id,
                'variation' => serialize($variation),
                'product_hash' => $product_hash,
            ),
            array(
                '%d',
                '%d',
                '%s',
                '%f',
                '%d',
                '%s',
                '%d',
                '%s',
                '%s',
            )
        );
    }
}

function getEnquiryDetailsTable()
{
    global $wpdb;
    return $wpdb->prefix.'enquiry_detail_new';
}

function getEnquiryHistoryTable()
{
    global $wpdb;
    return $wpdb->prefix.'enquiry_history';
}

function getEnquiryMetaTable()
{
    global $wpdb;
    return $wpdb->prefix.'enquiry_meta';
}

function getEnquiryProductsTable()
{
    global $wpdb;
    return $wpdb->prefix.'enquiry_products';
}

function getQuotationProductsTable()
{
    global $wpdb;
    return $wpdb->prefix.'enquiry_quotation';
}

function getVersionTable()
{
    global $wpdb;
    return $wpdb->prefix.'enquiry_quotation_version';
}

function getEnquiryThreadTable()
{
    global $wpdb;
    return $wpdb->prefix.'enquiry_thread';
}
