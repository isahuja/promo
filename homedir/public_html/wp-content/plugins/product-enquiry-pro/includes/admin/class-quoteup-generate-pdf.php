<?php

namespace Includes\Admin;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * This class is used to genrate pdf file of quotation.
 */
class QuoteupGeneratePdf
{
    /**
     * Function is used to genrate pdf.
     */
    public static function generatePdfAjaxCallback()
    {
        $PDFData  = $_POST;
        self::generatePdf($PDFData);
        die;
    }

    public static function generatePdf($PDFData)
    {
        do_action('wdm_before_create_pdf', isset($PDFData['language']) ? $PDFData['language'] : 'all');
        global $wpdb, $quoteup;
        $enquiry_tbl = getEnquiryDetailsTable();
        $quotation_tbl = getQuotationProductsTable();
        $enquiry_id = $PDFData[ 'enquiry_id' ];
        $show_price = isset($PDFData[ 'show-price' ]) ? $PDFData[ 'show-price' ] : '0';
        //Get data of enquiry details
        $enquiry_details = $wpdb->get_row($wpdb->prepare("SELECT name, email, product_details FROM $enquiry_tbl WHERE enquiry_id = %d", $enquiry_id));
        //Get data of Quotation(Updated price and Quantity)
        $quotation = $wpdb->get_results($wpdb->prepare("SELECT * FROM $quotation_tbl WHERE enquiry_id = %d", $enquiry_id), ARRAY_A);

        //Get Plugin setttings in selected Language
        add_filter('icl_current_string_language', array(__CLASS__, 'setQuoteLanguage'));
        $pdfSetting = get_option('wdm_form_data');
        remove_filter('icl_current_string_language', array(__CLASS__, 'setQuoteLanguage'));

        $name = $enquiry_details->name;
        $mail = $enquiry_details->email;
        $expiration_date = $quoteup->manageExpiration->getExpirationDate($PDFData[ 'enquiry_id' ]);

        //Genrate hash for this enquiry id
        $hash = quoteupEnquiryHashGenerator($enquiry_id);

        //update Hash in database(enquiry_detail_new) Table
        \updateHash($enquiry_id, $hash);
        //Genrate Unique URL For Approve or reject
        $uniqueURL = quoteLinkGenerator($hash);
        if (empty($uniqueURL)) {
            echo 'ERROR';
            die();
        }

        ob_start();
        $args = array(
            'pdfSetting' => $pdfSetting,
            'enquiry_id' => $enquiry_id,
            'name' => $name,
            'mail' => $mail,
            'show_price' => $show_price,
            'enquiry_details' => $enquiry_details,
            'uniqueURL' => $uniqueURL,
            'quotation' => $quotation,
            'expiration_date' => $expiration_date,
            'source' => isset($PDFData[ 'source' ])?$PDFData[ 'source' ]:"",
            );

        $args = apply_filters('quoteup_pdf_args', $args, $enquiry_id);

        //This loads the template for PDF
        quoteupGetAdminTemplatePart('pdf-quote/quote-pdf', '', $args);
        wc_cart_totals_shipping_html();

        do_action('wdm_after_create_pdf');

        $html = ob_get_clean();

        self::createFontsDirectory();

        require_once 'mpdf/mpdf.php';
        $stylesheet = file_get_contents(QUOTEUP_PLUGIN_DIR.'/css/admin/pdf-generation.css');
        $upload_dir = wp_upload_dir();
        $path = $upload_dir[ 'basedir' ].'/QuoteUp_PDF/';
        $mpdf = new \mPDF();
        $mpdf->useAdobeCJK = true;
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->WriteHtml($html, 2);

        $mpdf->Output($path.$enquiry_id.'.pdf', 'F');

        if (isset($PDFData[ 'source' ]) && $PDFData[ 'source' ] == 'email') {
            do_action('wdm_before_create_pdf', isset($PDFData['language']) ? $PDFData['language'] : 'all');
            ob_start();
            //This loads the template for PDF
            quoteupGetAdminTemplatePart('pdf-quote/quote-email', '', $args);
            $customCSS = '';
            $form_data = quoteupSettings();
            $too = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $table_name = getEnquiryDetailsTable();
            $sql = $wpdb->prepare("SELECT name,enquiry_hash FROM $table_name WHERE enquiry_id=%d", $enquiry_id);
            $hash = $wpdb->get_row($sql, ARRAY_A);
            $uniqueURL = quoteLinkGenerator($hash['enquiry_hash']);
            //Show Approve button Label
            $approveButtonLabel = self::getApproveButtonLabel($form_data);
            //Show Reject button Label
            $rejectButtonLabel = self::getRejectButtonLabel($form_data);
            
            if (isset($form_data[ 'button_CSS' ]) && $form_data[ 'button_CSS' ] == 'manual_css') {
                $customCSS = getManualCSS($form_data);
            }
            $nonce = wp_create_nonce('approveRejectionNonce');
            $enquiryHash = $hash['enquiry_hash'];
            $rejectURL = $uniqueURL.'&enquiryEmail='.$too.'&source=emailReject';
            $approveURL = $uniqueURL."&_quoteupApprovalRejectionNonce=$nonce&quoteupHash=$enquiryHash&enquiryEmail=$too&source=emailApprove";
            ?>
            <div>
                <a href="<?php echo $approveURL ?>" class="reject-quote-button"> <?php echo $approveButtonLabel ?></a> |

                <a href="<?php echo $rejectURL ?>" class="reject-quote-button"> <?php echo $rejectButtonLabel ?></a>
            </div>
            <?php
            do_action('woocommerce_email_footer');
            $mailData = ob_get_clean();
            do_action('wdm_after_create_pdf');
            return $mailData;
        }
    }

    public static function getApproveButtonLabel($form_data)
    {
        if (isset($form_data['approve_custom_label']) && !empty($form_data['approve_custom_label'])) {
            $approveButtonLabel = $form_data['approve_custom_label'];
        } else {
            $approveButtonLabel = __('Approve Quote', QUOTEUP_TEXT_DOMAIN);
        }
        return $approveButtonLabel;
    }

    public static function getRejectButtonLabel($form_data)
    {
        if (isset($form_data['reject_custom_label']) && !empty($form_data['reject_custom_label'])) {
            $rejectButtonLabel = $form_data['reject_custom_label'];
        } else {
            $rejectButtonLabel = __('Reject Quote', QUOTEUP_TEXT_DOMAIN);
        }
        return $rejectButtonLabel;
    }

    /*
     * This function is used to set quote language if WPML is active
     */
    public static function setQuoteLanguage()
    {
        return isset($PDFData['language']) ? $PDFData['language'] : 'all';
    }
    /**
     * This function is used to Create a font file if it does not exist in fonts.
     */
    public static function createFontsDirectory()
    {
        if (!defined('QUOTEUP_MPDF_CUSTOM_TTFONTPATH')) {
            $upload_dir = wp_upload_dir();
            $quoteup_custom_font_dir = $upload_dir[ 'basedir' ].'/custom_fonts/';
            define('QUOTEUP_MPDF_CUSTOM_TTFONTPATH', $quoteup_custom_font_dir);
            if (!file_exists(QUOTEUP_MPDF_CUSTOM_TTFONTPATH)) {
                wp_mkdir_p(QUOTEUP_MPDF_CUSTOM_TTFONTPATH);
            }
        }
    }
}
