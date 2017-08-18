<?php

namespace Includes\Admin;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('Abstracts\QuoteupList')) {
    require_once QUOTEUP_PLUGIN_DIR.'/includes/admin/abstracts/class-abstract-quoteup-list.php';
}

class QuoteupEnquiriesList extends Abstracts\QuoteupList
{
    public $countFilter;

    public function columnsTitles(){

        $columns = array(
                'product_details' => __('Items', QUOTEUP_TEXT_DOMAIN),
                'name' => __('Customer Name', QUOTEUP_TEXT_DOMAIN),
                'email' => __('Customer Email', QUOTEUP_TEXT_DOMAIN),
                'enquiry_date' => __('Enquiry Date', QUOTEUP_TEXT_DOMAIN),
                'message' => __('Message', QUOTEUP_TEXT_DOMAIN),
            );

            return apply_filters('quoteup_enquiries_get_columns', $columns);
    }

    public function rowActions($enquiryId, $adminPath, $currentPage){

            return array(
                'edit' => sprintf('<a href="%sadmin.php?page=%s&id=%s">%s</a>', $adminPath, 'quoteup-details-edit', $enquiryId, __('Edit', QUOTEUP_TEXT_DOMAIN))
            );
    }

    /*
     * text to be displayed when there are no records
     */
    public function noItems()
    {
        _e('No enquiries avaliable.', QUOTEUP_TEXT_DOMAIN);
    }

    public function sortableColumns()
    {
        $sortable_columns = array(
            'enquiry_id' => array('enquiry_id', false),
            'name' => array('name', true),
            'email' => array('email', true),
            'enquiry_date' => array('enquiry_date', true),
        );

        return apply_filters('quoteup_enquiries_get_sortable_columns', $sortable_columns);
    }

    public function displayMessage($item){
        return $item['message'];
    }
}
