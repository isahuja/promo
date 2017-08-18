<?php

namespace Includes\Frontend;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

WC()->mailer();
/**
 * This class is used to send mail to customer.
 * Mail includes pdf and the unique link by which customer can approve or reject quote.
 */
class SendEnquiryMail extends \WC_Email
{

    private static $instance;
    private $enquiryID;
    private $source;

    public static function getInstance($enquiryID,$authorEmail,$subject)
    {
        if (null === static::$instance) {
            static::$instance = new static($enquiryID,$authorEmail,$subject);
        }

        return static::$instance;
    }

    public function __construct($enquiryID,$authorEmail,$subject)
    {
        add_filter('woocommerce_email_styles', array($this,'addCSS') , 10, 1);
        $this->enquiryID = $enquiryID;
        $admin_emails = array();

        $email_data = quoteupSettings();
        if ($email_data[ 'user_email' ] != '') {
            $admin_emails = explode(',', $email_data[ 'user_email' ]);
        }
        $admin_emails = array_map('trim', $admin_emails);

        //Send email to admin only if 'Send mail to Admin' settings is checked
        $admin_emails = $this->addAdminMail($email_data, $admin_emails);

        //Send email to author only if 'Send mail to Author' settings is checked
        $admin_emails = $this->addAuthorMail($email_data, $authorEmail, $admin_emails);

        $admin_emails = array_unique($admin_emails);

        if (empty($admin_emails)) {
            return;
        }

        $wdm_sitename = '['.trim(wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES)).'] ';
        $subject = $this->getMailSubject($subject, $email_data, $wdm_sitename);

        $this->email_type = 'text/html';

        $this->heading = __('Enquiry/Quote', QUOTEUP_TEXT_DOMAIN);
        $this->subject = $subject;

        // Triggers for this email
        add_action('quoteup_send_enquiry_email', array( $this, 'trigger' ), 15, 1);

        parent::__construct();

        $this->recipient = $admin_emails;
    }

    public function addCSS($css)
    {
        $stylesheet = file_get_contents(QUOTEUP_PLUGIN_DIR.'/css/public/enquiry-mail.css');
        return $css.$stylesheet;
    }

    public function trigger()
    {
        $this->source = 'admin';
        $subject = $this->get_subject();
        $message = $this->get_content();
        $headers = $this->get_admin_headers();
        $attachments = $this->get_attachments();
        $this->send($this->recipient, $subject, $message, $headers, $attachments);
        $cc = apply_filters('quoteup_change_send_copy', $_POST['cc']);
        if ($cc == 'checked') {
            $this->source = 'cc';
            $message = $this->get_content();
            $headers = "Reply-to: " . get_option('admin_email') . "\r\n";
            $this->recipient = filter_var($_POST[ 'txtemail' ], FILTER_SANITIZE_EMAIL);
            $this->send($this->recipient, $subject, $message, $headers, $attachments);
        }
    }

    public function get_content_html()
    {
        $optionData = quoteupSettings();
        $customerName = $_POST['custname'];
        $data_obtained_from_form = $_POST;
        $form_data_for_mail = json_encode($data_obtained_from_form);
        $enable_price_flag = false;
        ob_start();
        do_action('quoteup_change_lang', $_POST['wdmLocale']);
        if ($optionData['enable_disable_quote'] == 1) {
            $heading = "<b>".__('Enquiry from', QUOTEUP_TEXT_DOMAIN)."  $customerName </b>";
        } else {
            $heading = "<b>".__('Quote Request from', QUOTEUP_TEXT_DOMAIN)."  $customerName </b>";
        }
        do_action('woocommerce_email_header', $heading);
        $enquiry_email = "<div style=background-color:#ddd>
            
           
           <table style='width: 100%;
                    background: #F7F7F7;
                    border-bottom: 1px solid #ddd;
                    margin-bottom: 0px;
                    'cellspacing='10px'>";
        $productTable = $this->getAdminProductTable($optionData, $enable_price_flag);
        if ($this->source == 'admin') {
            $enquiry_email = apply_filters('pep_add_custom_field_admin_email', $enquiry_email);
            $enquiry_email = apply_filters('quoteup_add_custom_field_admin_email', $enquiry_email);
        } else {
            $enquiry_email = apply_filters('pep_add_custom_field_customer_email', $enquiry_email);
            $enquiry_email = apply_filters('quoteup_add_custom_field_customer_email', $enquiry_email);
        }
        $enquiry_email = apply_filters('pep_before_product_name_in_admin_email', $enquiry_email, $form_data_for_mail);
        $enquiry_email = apply_filters('quoteup_before_product_name_in_admin_email', $enquiry_email, $form_data_for_mail);
        $enquiry_email = apply_filters('pep_before_price_in_admin_email', $enquiry_email, $form_data_for_mail);
        $enquiry_email = apply_filters('quoteup_before_price_in_admin_email', $enquiry_email, $form_data_for_mail);
        $enquiry_email .= '</table>';
        $enquiry_email .= $productTable;
        $enquiry_email .= '</div>';
        if ($enable_price_flag) {
            $enquiry_email .= "<label class='price-message'>". __('Products prices that are hidden on the frontend are displayed only in the enquiry emails received by the admin', QUOTEUP_TEXT_DOMAIN)."</label>";
        }
        $enquiry_email = apply_filters('pep_after_price_in_admin_email', $enquiry_email, $form_data_for_mail);
        $enquiry_email = apply_filters('quoteup_after_price_in_admin_email', $enquiry_email, $form_data_for_mail);
        echo $enquiry_email;
        do_action('woocommerce_email_footer');
        do_action('quoteup_reset_lang');
        $message = ob_get_clean();
        return wp_specialchars_decode($message, ENT_QUOTES);
    }

    /**
     * get_headers function.
     *
     * @access public
     * @return string
     */
    public function get_admin_headers() {
        $email = filter_var($_POST[ 'txtemail' ], FILTER_SANITIZE_EMAIL);
        return "Reply-to: " . $email . "\r\n";
    }

    public function get_attachments()
    {
        $upload_dir = wp_upload_dir();
        $path = $upload_dir[ 'basedir' ].'/QuoteUp_Files/';
        $attachments = array();
        if (file_exists($path.$this->enquiryID)) {
            $folder_path = $path.$this->enquiryID.'/';
            $files = scandir($path.$this->enquiryID);
            foreach ($files as $file) {
                if ($file == '.' || $file == "..") {
                    continue;
                }
                array_push($attachments, $folder_path.$file);
            }
        }
        return $attachments;
    }

    /**
     * This function is used to add author mail in admin_emails.
     *
     * @param [array] $email_data   [Settings stored in database]
     * @param [array] $admin_emails [Total mail ids on which mail needs to be sent]
     */
    private function addAdminMail($email_data, $admin_emails)
    {
        if (isset($email_data[ 'send_mail_to_admin' ]) && $email_data[ 'send_mail_to_admin' ] == 1) {
            $admin = get_option('admin_email');
            if (!in_array($admin, $admin_emails)) {
                $admin_emails[] = $admin;
            }
        }

        return $admin_emails;
    }

    /**
     * This function is used to add author mail in admin_emails.
     *
     * @param [array]  $email_data   [Settings stored in database]
     * @param [string] $authorEmail  [Author email id]
     * @param [array]  $admin_emails [Total mail ids on which mail needs to be sent]
     */
    private function addAuthorMail($email_data, $authorEmail, $admin_emails)
    {
        if (isset($email_data[ 'send_mail_to_author' ]) && $email_data[ 'send_mail_to_author' ] == 1 && !empty($authorEmail)) {
                $admin_emails = array_merge($admin_emails, $authorEmail);
        }

        return $admin_emails;
    }

    /**
     * This function is used to set default subject for mail if subject is blank
     * or set the subject entered by customer.
     *
     * @param [type] $subject      [description]
     * @param [type] $email_data   [description]
     * @param [type] $wdm_sitename [description]
     *
     * @return [type] [description]
     */
    private function getMailSubject($subject, $email_data, $wdm_sitename)
    {
        if ($subject == '') {
            $admin_subject = $wdm_sitename.$email_data[ 'default_sub' ];
            if ($email_data[ 'default_sub' ] == '') {
                $admin_subject = $wdm_sitename.__('Enquiry or Quote Request for Products from  ', QUOTEUP_TEXT_DOMAIN).get_bloginfo('name');
            }
        } else {
            $admin_subject = $wdm_sitename.$subject;
        }

        return $admin_subject;
    }

    /**
     * This function returns admin Product table.
     *
     * @return [type] [description]
     */
    private function getAdminProductTable($form_data, &$enable_price_flag)
    {
        @session_start();
        $productTable = '';
        $authorEmail = array();
        if (isset($form_data[ 'enable_disable_mpe' ]) && $form_data[ 'enable_disable_mpe' ] == 1) {
            $productTable .= "<table style='width:100%;table-layout: fixed;' cellspacing='0' cellpadding='0'>
                    <tr>
                        <th class='product-name-head'>". __('Product Name', QUOTEUP_TEXT_DOMAIN) ."</th>
                        <th class='sku-head'>". __('SKU', QUOTEUP_TEXT_DOMAIN) ."</th>
                        <th class='qty-head'>". __('Quantity', QUOTEUP_TEXT_DOMAIN) ." </th>
                        <th class='price-head'>". __('Price', QUOTEUP_TEXT_DOMAIN) ." </th>
                        <th class='remarks-head'>". __('Remarks', QUOTEUP_TEXT_DOMAIN) ." </th>
                    </tr>";
                $productTable = $this->getAdminMailProductInfo($_SESSION[ 'wdm_product_info' ], $productTable, $enable_price_flag);
            $productTable .= "</table>";
        } else {
            $product_id = $_POST[ 'product_id' ];
            $enable_price = get_post_meta($product_id, '_enable_price', true);
            $productTable .= "<table style='width: 100%;' cellspacing='0' cellpadding='0'>
                    <tr>
                        <th class='product-name-head'>". __('Product Name', QUOTEUP_TEXT_DOMAIN) ."</th>
                        <th class='sku-head'>". __('SKU', QUOTEUP_TEXT_DOMAIN) ."</th>
                        <th class='qty-head'>". __('Quantity', QUOTEUP_TEXT_DOMAIN) ." </th>";
            if ($enable_price == 'yes' || $this->source == 'admin') {
                $productTable .= "<th class='price-head'>". __('Price', QUOTEUP_TEXT_DOMAIN) ." </th>";
            }
            $productTable .= "</tr>";
            $title = get_the_title($product_id);
            $prod_permalink = $_POST[ 'product_url' ];
            $productQuantity = $_POST[ 'product_quant' ];
            $variation_id = filter_var($_POST['variation_id'], FILTER_SANITIZE_NUMBER_INT);
            $variation_detail = '';
            $variationStringToPrint = array();
            $authorEmail = array();
            array_push($authorEmail, isset($_POST[ 'uemail' ]) ? $_POST[ 'uemail' ] : '');

        //Variable Product
            if ($variation_id != '') {
                $product = wc_get_product($variation_id);
                $sku = $product->get_sku();
                $variation_detail = $_POST['variation_detail'];
                $price = quoteupGetPriceToDisplay($product);
                $img = wp_get_attachment_url(get_post_thumbnail_id($variation_id));
                $img_url = getImgUrl($img, $product_id);

                $variation_detail = getVariationDetails($variation_detail);
                $variationString = $this->getVatiationString($variation_detail, $variation_id);
                $variationStringToPrint = explode(",", $variationString);
            } else {
                $product = wc_get_product($product_id);
                $price = quoteupGetPriceToDisplay($product);

                $sku = $product->get_sku();
                $img_url = wp_get_attachment_url(get_post_thumbnail_id($product_id));
            }
        //End of Variable Product
            if ($price == '') {
                $price = 0;
            }

            //Get product name column
            $productTable .= "<tr>
                <td class='product-name'><a href='{$prod_permalink}'>{$title}</a>";
            // Get variation string if variable product
            if (!empty($variationStringToPrint)) {
                $productTable .= "<div style='margin-left:10px'>";
                foreach ($variationStringToPrint as $value) {
                    $productTable .= "&#8627".$value."<br>";
                }
                $productTable .= "</div>";
            }

            $productTable .= "</td>
                <td class='sku'>$sku</td>
                <td class='qty'>$productQuantity</td>";
            if ($enable_price == 'yes'  || $this->source == 'admin') {
                $productTable .= "<td class='price'>$price</td>";
            }
            $productTable .= "</tr> </table>";
            if ($enable_price == ''  && $this->source == 'admin') {
                $enable_price_flag = true;
            }
        }
        return $productTable;
    }

    /**
     * This function creates a product table HTML for admin mail.
     *
     * @param [array]  $arr           [array of products]
     * @param [string] $product_table [product table string]
     *
     * @return [type] [description]
     */
    private function getAdminMailProductInfo($arr, $productTable, &$enable_price_flag)
    {
        foreach ($arr as $element) {
            $id = $element[ 'id' ];
            $url = get_permalink($id);
            $product = wc_get_product($id);
            $enable_price = get_post_meta($id, '_enable_price', true);
            $sku = $product->get_sku();
            if ($element['variation_id'] != '') {
                $product = wc_get_product($element['variation_id']);
                $variation_sku = $product->get_sku();
                if (!empty($variation_sku)) {
                    $sku = $variation_sku;
                }
                $variationString = printVariations($element);
            }

            $title = get_the_title($id);
            
            $productTable .= "<tr>
                <td class='product-name'><a href='{$url}'>{$title}</a>";



            if (!empty($variationString)) {
                $variationString = preg_replace(']<br>]', '<br>&#8627 ', $variationString); // Used to add arrow symbol
                $variationString = preg_replace(']<br>]', '', $variationString, 1); // Used to remove first br tag
                $productTable .= "<div style='margin-left:10px'>" . $variationString . "</div>";
            }

            $productTable .= "</td>
                <td class='sku'>$sku</td>
                <td class='qty'>{$element[ 'quant' ]}</td>";

            if ($enable_price == 'yes'  || $this->source == 'admin') {
                $productTable .= "<td class='price'>".wc_price($element['price'])."</td>";
            } else {
                $productTable .= "<td class='price'>-</td>";
            }
            $productTable .= "<td class='remarks'>{$element[ 'remark' ]}</td>
            </tr>";
        }
        if ($enable_price == ''  && $this->source == 'admin') {
                $enable_price_flag = true;
        }

        return $productTable;
    }



    /**
     * THis function is used to get variation string.
     *
     * @param [array] $variation_detail [Variation details]
     *
     * @return [type] [description]
     */
    private function getVatiationString($variation_detail, $variation_id)
    {
        $variationString = '';
        $variableProduct = wc_get_product($variation_id);
        $product_attributes = $variableProduct->get_attributes();
        foreach ($variation_detail as $attributeName => $attributeValue) {
            if (!empty($variationString)) {
                $variationString .= ',';
            }

            $taxonomy = wc_attribute_taxonomy_name(str_replace('pa_', '', urldecode($attributeName)));

            // If this is a term slug, get the term's nice name
            if (taxonomy_exists($taxonomy)) {
                $term = get_term_by('slug', $attributeValue, $taxonomy);
                if (!is_wp_error($term) && $term && $term->name) {
                    $attributeValue = $term->name;
                }
                $label = wc_attribute_label($taxonomy);

            // If this is a custom option slug, get the options name
            } else {
                $label = quoteupVariationAttributeLabel($variableProduct, $attributeName, $product_attributes);
            }

            $variationString .= '<b> '.$label.'</b> : '.$attributeValue;
        }

        return $variationString;
    }
}
