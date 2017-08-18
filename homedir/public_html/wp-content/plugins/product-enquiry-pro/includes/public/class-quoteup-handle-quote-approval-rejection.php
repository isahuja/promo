<?php

namespace Includes\Frontend;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
/**
 * Handles Approval and Rejection on the Frontend Side.
 */
class QuoteupHandleQuoteApprovalRejection
{
    /**
     * @var Singleton The reference to *Singleton* instance of this class
     */
    private static $instance;

    public $isQuoteRejected = false;

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
        add_action('wp_loaded', array($this, 'handleApprovalRejectionResponse'));
    }

    /**
     * Checks if email id passed to function is associated with the enquiry.
     *
     * @global object $wpdb Database object
     *
     * @param string $emailAddress Email Address of the user
     * @param string $enquiryHash  Hash of the enquiry
     *
     * @return int If enquiry is found, returns enquiry id, else returns 0
     */
    public function checkIfEmailIsValid($emailAddress, $enquiryHash)
    {
        global $wpdb;
        $quotationTable = getEnquiryDetailsTable();
        $checkEnquiryExists = $wpdb->get_var($wpdb->prepare("SELECT enquiry_id FROM $quotationTable WHERE email = %s AND enquiry_hash = %s", $emailAddress, $enquiryHash));
        if ($checkEnquiryExists === null) {
            return 0;
        }

        return $checkEnquiryExists;
    }

    /**
     * Triggers the display for Shortcode.
     */
    public static function approvalRejectionShortcodeCallback()
    {
        ob_start();
        do_action('quoteup_approval_rejection_content');
        $getContent = ob_get_contents();
        ob_end_clean();

        return $getContent;
    }

    public function getFlag($hash, $enquiryEmail)
    {
        if ((isset($_POST['approvalQuote']) &&
        isset($_POST['quoteupHash']) &&
        isset($_POST['enquiryEmail']) &&
        !empty($hash) &&
        !empty($enquiryEmail)) || (isset($_GET['quoteupHash']) && isset($_GET['enquiryEmail']) && isset($_GET['source']) && $_GET['source'] == 'emailApprove')) {
            return true;
        }
        return false;
    }

    /**
     * Handles the action taken by user on the frontend. It triggers all the actions
     * need to be taken on clicking 'Approve' or 'Reject' button on the frontend.
     *
     * @global object $quoteupManageHistory Object of the class which manages history
     */
    public function handleApprovalRejectionResponse()
    {
        if (isset($_GET['source']) && $_GET['source'] == 'emailApprove') {
            $data = $_GET;
        } else {
            $data = $_POST;
        }

        if (!isset($data['_quoteupApprovalRejectionNonce']) ||
            empty($data['_quoteupApprovalRejectionNonce'])) {
            return;
        }

        global $quoteup, $quoteupManageHistory, $quoteup_enough_stock;
        $hash = trim($data['quoteupHash']);
        $enquiryEmail = trim($data['enquiryEmail']);
        //User has approved the quote. Handles that action in below if.
        $checkFlag = $this->getFlag($hash, $enquiryEmail);
        if ($checkFlag) {
            //Check if hash is correct
            if (($enquiryId = $this->checkIfEmailIsValid($data['enquiryEmail'], $data['quoteupHash'])) !== 0) {
                $enquiry_id = explode('_', $_GET['quoteupHash']);
                $enquiry_id = $enquiry_id[0];
                $quoteupManageHistory->addQuoteHistory($enquiry_id, __('Approved but order not yet placed'), 'Approved');

                //Add Product in cart and redirect to cart page
                $quoteup->wcCart->addProductsToCart($enquiryId);

                if ($quoteup_enough_stock) {
                    $quoteup->wcCart->redirectToCheckoutPage('ManualRedirect');
                }
            }
        } else {
            //User has rejected the quote. Handles that action in this else.
            if (isset($data['quoteupHash']) &&
            isset($data['enquiryEmail']) &&
            !empty($data['quoteupHash']) &&
            !empty($data['enquiryEmail'])) {
                //Check if hash is correct
                if (($enquiryId = $this->checkIfEmailIsValid($data['enquiryEmail'], $data['quoteupHash'])) !== 0) {
                    $enquiry_id = explode('_', $_GET['quoteupHash']);

                    $reason = trim($data['quoteRejectionReason']);
                    $message = esc_textarea($data['quoteRejectionReason']);
                    if (empty($reason)) {
                        $message = __('No message from customer', QUOTEUP_TEXT_DOMAIN);
                    }

                    $message = stripcslashes($message);

                    $quoteupManageHistory->addQuoteHistory($enquiryId, $message, 'Rejected');
                    $this->isQuoteRejected = true;
                }
            }
        }
    }
}

$this->quoteApprovalRejection = QuoteupHandleQuoteApprovalRejection::getInstance();
