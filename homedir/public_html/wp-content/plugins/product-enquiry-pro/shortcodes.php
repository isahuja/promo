<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

add_shortcode('APPROVAL_REJECTION_CHOICE', array('Includes\Frontend\QuoteupHandleQuoteApprovalRejection', 'approvalRejectionShortcodeCallback'));

add_shortcode('ENQUIRY_CART', array('Includes\Frontend\QuoteupHandleEnquiryCart', 'quoteupEnquiryCartShortcodeCallback'));

add_shortcode('ENQUIRY_BUTTON', array('Includes\Frontend\QuoteupEnquiryButtonShortcode', 'quoteupEnquiryButtonShortcodeCallback'));
