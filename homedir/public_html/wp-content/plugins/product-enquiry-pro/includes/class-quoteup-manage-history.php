<?php

namespace Includes;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Manage Quote history.
 */
class QuoteupManageHistory
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

    /**
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
     */
    protected function __construct()
    {
        add_action('quoteup_add_custom_field_in_db', array($this, 'updateRequest'), 15);
        add_action('create_quote_form_entry_added_in_db', array($this, 'updateQuoteCreated'), 15);
    }

    public function updateRequest($insert)
    {
        $this->addQuoteHistory($insert, '-', 'Requested');
    }

    public function updateQuoteCreated($insert)
    {
        if (!isset($_POST['globalEnquiryID']) || $_POST['globalEnquiryID'] == 0) {
            $this->addQuoteHistory($insert, '-', 'Quote Created');
        }
    }

    /**
     * Add message to Enquiry.
     *
     * @param int    $enquiryId Enquiry id associated with enquiry
     * @param string $message   message to be added in the enquiry
     * @param string $status    action performed in the enquiry
     */
    public function addQuoteHistory($enquiryId, $message, $status)
    {
        global $wpdb;
        $date = current_time('mysql');
        $table_name = getEnquiryHistoryTable();
        $performedBy = null;
        if (is_user_logged_in()) {
            $performedBy = get_current_user_id();
        }
        do_action('quoteup_before_adding_history', $enquiryId, $message, $status);
        $insert_id = $wpdb->insert(
            $table_name,
            array(
            'enquiry_id' => $enquiryId,
            'date' => $date,
            'message' => $message,
            'status' => $status,
            'performed_by' => $performedBy,
            )
        );
        do_action('quoteup_after_adding_history', $insert_id, $enquiryId, $message, $status);
    }

    /**
     * Returns the last entry added in the history table.
     *
     * @return Returns NULL if record is not found in the database. Otherwise returns the data
     */
    public function getLastAddedHistory($enquiry_id)
    {
        global $wpdb;
        $table_name = getEnquiryHistoryTable();

        return $wpdb->get_row($wpdb->prepare("SELECT ID, date, message, status, performed_by FROM $table_name WHERE enquiry_id = %d ORDER BY ID DESC LIMIT 1", $enquiry_id), ARRAY_A);
    }
}

/*
 * Creating $quoteupManageHistory as a Global variable because it is going to be needed in Cron Job too.
 * Creating it as a separate variable eliminates bootstraping of $quoteup variable and hence can be used
 * directly whenever needed 
 */
$GLOBALS['quoteupManageHistory'] = QuoteupManageHistory::getInstance();
