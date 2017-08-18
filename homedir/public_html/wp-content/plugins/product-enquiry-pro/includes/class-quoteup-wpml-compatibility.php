<?php

namespace Includes;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class QuoteUpWPMLCompatibility
{
    protected static $instance = null;

    public $current_language;

    public $locale;

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

    public function __construct()
    {
        add_action('quoteup_change_lang', array($this, 'changeLang'));
        //If devs want to reset lang, they can call do_action in their code
        add_action('quoteup_reset_lang', array($this, 'resetLang'));
        add_action('wdm_before_send_admin_email', array($this, 'wdmBeforeSendAdminEmail'));
        add_action('wdm_before_create_pdf', array($this, 'wdmBeforeQuotePDF'));
        add_action('wdm_before_send_mail', array($this, 'wdmBeforeQuotePDF'));
        add_action('wdm_after_send_admin_email', array($this, 'resetLang'));
        add_action('wdm_after_create_pdf', array($this, 'resetLang'));
        add_action('wdm_after_send_mail', array($this, 'resetLang'));
        add_filter('icl_lang_sel_copy_parameters', array($this, 'preserveGetParameters'), 10, 1);
        add_filter('woocommerce_order_get_items', array($this, 'setEnquiryProductsInOrder'), 1, 2);
        add_filter('translate_object_id', array($this, 'setEnquiryObjectId'), 100, 2);
    }

    public function changeLang($lang)
    {
        if (!defined('ICL_SITEPRESS_VERSION') || ICL_PLUGIN_INACTIVE) {
            return;
        }

        global $sitepress;

        if (!$this->current_language) {
            $this->current_language = $sitepress->get_current_language();
        }

        if ($lang) {
            $this->wdmSwitchLocale($lang);
        } else {
            global $sitepress_settings;
            $this->wdmSwitchLocale($sitepress_settings[ 'admin_default_language' ]);
        }
    }
    /**
     * This function is used to switch language before creating PDF
     * @param  [string] $lang [Language to be selected]
     */
    public function wdmBeforeQuotePDF($lang)
    {
        do_action('quoteup_change_lang', $lang);
    }

    /**
     * This function is used to switch language before Sending admin mail
     * @param  [string] $lang [Language to be selected]
     */
    public function wdmBeforeSendAdminEmail($email)
    {
        if (!defined('ICL_SITEPRESS_VERSION') || ICL_PLUGIN_INACTIVE) {
            return;
        }
        global $sitepress, $sitepress_settings;
        $user = get_user_by('email', $email);
        if ($user) {
            $lang = $sitepress->get_user_admin_language($user->ID);
        } else {
            $lang = $sitepress_settings[ 'admin_default_language' ];
        }
        do_action('quoteup_change_lang', $lang);
    }

    /**
     * This function is used to switch language to orignal once mail is sent
     */
    public function resetLang()
    {
        if (!defined('ICL_SITEPRESS_VERSION') || ICL_PLUGIN_INACTIVE) {
            return;
        }

        if ($this->current_language) {
            $this->wdmSwitchLocale($this->current_language);
        }
    }

    /**
     * This function is used to switch language
     * @param  [type] $lang [description]
     * @return [type]       [description]
     */
    public function wdmSwitchLocale($lang)
    {
        global $sitepress, $wp_locale;
        $sitepress->switch_lang($lang, true);
        $this->locale = $sitepress->get_locale($lang);
        unload_textdomain('quoteup');
        unload_textdomain('default');
        $wp_locale = new \WP_Locale();
        $this->switchLang();
        load_default_textdomain();
    }

    public function switchLang()
    {
        global $quoteup;
        add_filter('plugin_locale', array($this, 'setLocale'), 10);
        $quoteup->loadTextDomain();
        remove_filter('plugin_locale', array($this, 'setLocale'), 10);
    }

    public function setLocale()
    {
        return $this->locale;
    }

    public function preserveGetParameters($parameters)
    {
        $parameters[] = 'quoteupHash';
        $parameters[] = 'emailAddressSubmit';
        $parameters[] = 'enquiryEmail';

        return $parameters;
    }

    public function setEnquiryProductsInOrder($items, $orderObject)
    {
        if (!quoteupIsWpmlActive()) {
            return $items;
        }

        if (version_compare(WC_VERSION, '3.0.0', '<')) {
            $orderId = $orderObject->id;
        } else {
            $orderId = $orderObject->get_id();
        }

        $enquiryId = get_post_meta($orderId, 'quoteup_enquiry_id', true);
        
        if ($enquiryId == null || empty($enquiryId)) {
            return $items;
        }

        $this->quoteProductIds = getProductIdsInQuote($enquiryId);

        return $items;
    }

    public function setEnquiryObjectId($productId, $item_type)
    {
        if (!isset($this->quoteProductIds) || empty($this->quoteProductIds) || is_null($this->quoteProductIds)) {
            return $productId;
        }

        if ($item_type == 'product') {
            global $sitepress;
            $trid = $sitepress->get_element_trid($productId, 'post_' . $item_type);
            $translations = $sitepress->get_element_translations($trid, 'post_' . $item_type);
            if ($translations) {
                foreach ($translations as $singleTranslation) {
                    if (in_array($singleTranslation->element_id, $this->quoteProductIds)) {
                        return $singleTranslation->element_id;
                    }
                }
            }
        }

        return $productId;
    }
}

QuoteUpWPMLCompatibility::getInstance();
