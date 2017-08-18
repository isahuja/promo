<?php

namespace Includes\Admin;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('Abstracts\QuoteupList')) {
    require_once QUOTEUP_PLUGIN_DIR.'/includes/admin/abstracts/class-abstract-quoteup-list.php';
}

class QuoteupQuotesList extends Abstracts\QuoteupList
{
    public $countFilter;

    protected function statusSpecificEnquiries(){

        $enquiries = array();

        if(!isset($_GET['status'])){
            return $enquiries;
        }

        $filter = filter_var($_GET['status'], FILTER_SANITIZE_STRING);

        global $wpdb;
        $tableName = getEnquiryHistoryTable();

        if (isset($filter) && $filter == 'admin-created') {

            $metaTableName = getEnquiryMetaTable();
            $sql = "SELECT enquiry_id FROM $metaTableName WHERE meta_key = '_admin_quote_created'";

        } elseif($filter == "saved") {

            $sql = "SELECT s1.enquiry_id
                FROM $tableName s1
                LEFT JOIN $tableName s2 ON s1.enquiry_id = s2.enquiry_id
                AND s1.id < s2.id
                WHERE s2.enquiry_id IS NULL AND (s1.status ='".$filter."' OR s1.status ='Quote Created') AND s1.enquiry_id > 0 AND s1.ID > 0";

        } else {
            $sql = "SELECT s1.enquiry_id
                FROM $tableName s1
                LEFT JOIN $tableName s2 ON s1.enquiry_id = s2.enquiry_id
                AND s1.id < s2.id
                WHERE s2.enquiry_id IS NULL AND s1.status ='".$filter."'AND s1.enquiry_id > 0 AND s1.ID > 0";
        }

        return $wpdb->get_col($sql);

    }




    public function getStatusImage($status)
    {
        $spanTag = "<span class = 'status-span'>";
        $closeSpanImageTag = "</span><img class='status-image' title = '";
        $title = '';
        $imgSrc2 = '';
        $imgSrc = "' src = ".QUOTEUP_PLUGIN_URL.'/images/';
        //Create image name for status image
        $imgSrc2 = strtolower($status).'.png>'; //convert name to lower
        $imgSrc2 = str_replace(" ", "-", $imgSrc2); // replace space with -
        //End of create image name for status image
        
        // Using array to make title translation ready
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
        $title= $statusArray[$status];
        
        return $spanTag.$title.$closeSpanImageTag.$title.$imgSrc.$imgSrc2;
    }

    public function get_views()
    {
        $countAll = $countRequested = $countSaved = $countSent = $countApproved = $countRejected = $countPlaced = $countExpired = $countAdminQuote = 0;

        global $wpdb;
        $tableName = getEnquiryHistoryTable();
        $metaTableName = getEnquiryMetaTable();

        $sql = "SELECT s1.status,COUNT(s1.enquiry_id) AS EnquiryCount FROM $tableName s1 LEFT JOIN $tableName s2 ON s1.enquiry_id = s2.enquiry_id AND s1.id < s2.id WHERE s2.enquiry_id IS NULL AND s1.status IN ('requested','saved','sent','approved','rejected','Order Placed','expired','Quote Created') AND s1.enquiry_id > 0 AND s1.ID > 0 GROUP BY s1.status";
        $res = $wpdb->get_results($sql, ARRAY_A);
        $sql1 = "SELECT COUNT(enquiry_id) AS EnquiryCount FROM $metaTableName WHERE meta_key = '_admin_quote_created'";
        $res1 = $wpdb->get_var($sql1);
        $adminCreatedQuoteArray = array(
            'status' => 'admin-created',
            'EnquiryCount' => $res1,
            );
        array_push($res, $adminCreatedQuoteArray);
        $this->countFilter = $res;

        self::getCount($res, $countAll, $countRequested, $countSaved, $countSent, $countApproved, $countRejected, $countPlaced, $countExpired, $countAdminQuote);

        $requestedURL = get_admin_url('', 'admin.php?page=quoteup-details-new');
        $currentAll = $currentRequested = $currentSaved = $currentSent = $currentApproved = $currentRejected = $currentPlaced = $currentExpired = $currentAdminCreated = '';
        if (isset($_GET['status'])) {
            switch ($_GET['status']) {
                case 'requested':
                    $currentRequested = 'current';
                    break;

                case 'saved':
                    $currentSaved = 'current';
                    break;

                case 'sent':
                    $currentSent = 'current';
                    break;

                case 'approved':
                    $currentApproved = 'current';
                    break;

                case 'rejected':
                    $currentRejected = 'current';
                    break;

                case 'Order Placed':
                    $currentPlaced = 'current';
                    break;

                case 'expired':
                    $currentExpired = 'current';
                    break;
                case 'admin-created':
                    $currentAdminCreated = 'current';
                    break;
                default:
                    break;
            }
        } else {
            $currentAll = 'current';
        }
        $status_links = array(
            'all' => "<a class=$currentAll id='all' href='".$requestedURL."'>".__('All', QUOTEUP_TEXT_DOMAIN)." <span class='count'>(".$countAll.')</span></a>',
        );
        if ($countRequested>0){
            $status_links['requested'] = "<a class='".$currentRequested."'  id='requested' href='".$requestedURL."&status=requested'>".__('Requested', QUOTEUP_TEXT_DOMAIN)."<span class='count'>(".$countRequested.')</span></a>';
        }

        if ($countSaved>0){
            $status_links['saved'] = "<a class='".$currentSaved."'  id='saved' href='".$requestedURL."&status=saved'>".__('Saved', QUOTEUP_TEXT_DOMAIN)." <span class='count'>(".$countSaved.')</span></a>';
        }

        if ($countSent>0){
            $status_links['sent'] = "<a class='".$currentSent."'  id='sent' href='".$requestedURL."&status=sent'>".__('Sent', QUOTEUP_TEXT_DOMAIN)." <span class='count'>(".$countSent.')</span></a>';
        }

        if ($countApproved>0){
            $status_links['approved'] = "<a class='".$currentApproved."'  id='approved' href='".$requestedURL."&status=approved'>".__('Approved', QUOTEUP_TEXT_DOMAIN)." <span class='count'>(".$countApproved.')</span></a>';
        }

        if ($countRejected>0){
            $status_links['rejected'] = "<a class='".$currentRejected."'  id='rejected' href='".$requestedURL."&status=rejected'>".__('Rejected', QUOTEUP_TEXT_DOMAIN)." <span class='count'>(".$countRejected.')</span></a>';
        }

        if ($countPlaced>0){
            $status_links['Order Placed'] = "<a class='".$currentPlaced."'  id='completed' href='".$requestedURL."&status=Order Placed'>".__('Order Placed', QUOTEUP_TEXT_DOMAIN)." <span class='count'>(".$countPlaced.')</span></a>';
        }

        if ($countExpired>0){
            $status_links['expired'] = "<a class='".$currentExpired."' id='expired' href='".$requestedURL."&status=expired'>".__('Expired', QUOTEUP_TEXT_DOMAIN)." <span class='count'>(".$countExpired.')</span></a>';
        }

        if ($countAdminQuote>0){
            $status_links['admin'] = "<a class='".$currentAdminCreated."' id='adminCreated' href='".$requestedURL."&status=admin-created'>".__('Admin Created Quotes', QUOTEUP_TEXT_DOMAIN)." <span class='count'>(".$countAdminQuote.')</span></a>';
        }

        return $status_links;
    }

    public static function getCount($res, &$countAll, &$countRequested, &$countSaved, &$countSent, &$countApproved, &$countRejected, &$countPlaced, &$countExpired, &$countAdminQuote)
    {
        foreach ($res as $key) {
            switch ($key['status']) {
                case 'Requested':
                    $countRequested = $key['EnquiryCount'];
                    $countAll = $countAll + $countRequested;
                    break;

                case 'Saved':
                    $countSaved = $countSaved + $key['EnquiryCount'];
                    $countAll = $countAll + $countSaved;
                    break;

                case 'Sent':
                    $countSent = $key['EnquiryCount'];
                    $countAll = $countAll + $countSent;
                    break;

                case 'Approved':
                    $countApproved = $key['EnquiryCount'];
                    $countAll = $countAll + $countApproved;
                    break;

                case 'Rejected':
                    $countRejected = $key['EnquiryCount'];
                    $countAll = $countAll + $countRejected;
                    break;

                case 'Order Placed':
                    $countPlaced = $key['EnquiryCount'];
                    $countAll = $countAll + $countPlaced;
                    break;

                case 'Expired':
                    $countExpired = $key['EnquiryCount'];
                    $countAll = $countAll + $countExpired;
                    break;

                case 'Quote Created':
                    $countSaved = $countSaved + $key['EnquiryCount'];

                case 'admin-created':
                    $countAdminQuote = $key['EnquiryCount'];
                    break;
                default:
                    break;
            }
        }
    }

    /*
     * text to be displayed when there are no records
     */
    public function noItems()
    {
        _e('No enquiry & quote details available.', QUOTEUP_TEXT_DOMAIN);
    }

    public function rowActions($enquiryId, $adminPath, $currentPage){

        return array(
            'edit' => sprintf('<a href="%sadmin.php?page=%s&id=%s">%s</a>', $adminPath, 'quoteup-details-edit', $enquiryId, __('Edit', QUOTEUP_TEXT_DOMAIN))
        );
    }


    public function columnsTitles()
    {
        $columns = array(
            'status' => __('Status', QUOTEUP_TEXT_DOMAIN),
            'product_details' => __('Items', QUOTEUP_TEXT_DOMAIN),
            'name' => __('Customer Name', QUOTEUP_TEXT_DOMAIN),
            'email' => __('Customer Email', QUOTEUP_TEXT_DOMAIN),
            'enquiry_date' => __('Enquiry Date', QUOTEUP_TEXT_DOMAIN),
            'total' => __('Total', QUOTEUP_TEXT_DOMAIN),
            'order_number' => __('Order #', QUOTEUP_TEXT_DOMAIN),
        );

        return apply_filters('quoteup_enquiries_get_columns', $columns);
    }

    public function sortableColumns()
    {
        $sortableColumns = array(
            'enquiry_id' => array('enquiry_id', false),
            'name' => array('name', true),
            'email' => array('email', true),
            'enquiry_date' => array('enquiry_date', true),
        );

        return apply_filters('quoteup_enquiries_get_sortable_columns', $sortableColumns);
    }

    public function displayStatus($item){
        global $quoteupManageHistory;
        $status = $quoteupManageHistory->getLastAddedHistory($item['enquiry_id']);
        return $this->getStatusImage($status['status']);
    }

    public function displayTotal($item){
            return ($item['total'] == null || empty($item['total']))  ? '-' : wc_price($item['total']);
    }

    public function displayOrderNumber($item){
            $orderid = \Includes\QuoteupOrderQuoteMapping::getOrderIdOfQuote($item['enquiry_id']);
            if ($orderid == '0' || $orderid == null) {
                $orderid = '-';
            } else {
                $orderid = '<a href="'.admin_url('post.php?post='.absint($orderid).'&action=edit').'" >'.$orderid.'</a>';
            }
            return $orderid;
    }

}
