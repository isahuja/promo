<?php

namespace Includes\Admin\Abstracts;

abstract class QuoteupEdit
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'addScriptsAndStyles'));
        add_action('wp_before_admin_bar_render', 'quoteupWpmlRemoveAdminBarMenu');
    }

    protected function registerScripts($scriptHandles = []){
        if(!empty($scriptHandles)){
            foreach($scriptHandles as $scriptHandle => $scriptUrl){
                if (wp_script_is( $scriptHandle, 'registered' )) {
                    continue;
                }
                wp_register_script($scriptHandle, $scriptUrl, [], QUOTEUP_VERSION);
            }
        }
        }

    protected function enqueueScripts($scriptHandles = []){
        if(!empty($scriptHandles)){
            foreach($scriptHandles as $scriptHandle){
                if (shouldScriptBeEnqueued($scriptHandle)) {
                    wp_enqueue_script($scriptHandle);
                }  
            }
        }
    }

    protected function registerStyles($styleHandles = []){
        if(!empty($styleHandles)){
            foreach($styleHandles as $styleHandle => $styleUrl){
                if (wp_style_is( $styleHandle, 'registered' )) {
                    continue;
        }
                wp_register_style($styleHandle, $styleUrl, [], QUOTEUP_VERSION);
            }
        }
    }

    protected function enqueueStyles($styleHandles = [])
    {
        if (!empty($styleHandles)) {
            foreach ($styleHandles as $styleHandle) {
                if (shouldStyleBeEnqueued($styleHandle)) {
                    wp_enqueue_style($styleHandle);
                }
            }
        }
    }

    protected function localizeScripts($scriptHandles = [])
    {
        if (!empty($scriptHandles)) {
            foreach ($scriptHandles as $scriptHandle) {
                switch ($scriptHandle) {

                    case 'quoteup-edit-quote':
                        $aryArgs = getDateLocalizationArray();
                        $aryArgs['unreadEnquiryFlag'] = getEnquiryMeta($_GET['id'], '_unread_enquiry');
                        wp_localize_script($scriptHandle, 'dateData', $aryArgs);
                        break;
                
                    case 'products-selection-js':
                        wp_localize_script($scriptHandle, 'productsSelectionData', array(
                                'ajax_url' => admin_url('admin-ajax.php'),
                        ));
                        break;

                    case 'wc-enhanced-select-extended':
                        $enquiryLanguage = "";

                        if (quoteupIsWpmlActive()) {
                            $enquiryLanguage = getEnquiryMeta($_GET['id'], 'enquiry_lang_code');
                        }
                    
                         wp_localize_script($scriptHandle, 'wc_enhanced_select_params', array(
                        'i18n_matches_1' => _x('One result is available, press enter to select it.', 'enhanced select', 'woocommerce'),
                        'i18n_matches_n' => _x('%qty% results are available, use up and down arrow keys to navigate.', 'enhanced select', 'woocommerce'),
                        'i18n_no_matches' => _x('No matches found', 'enhanced select', 'woocommerce'),
                        'i18n_ajax_error' => _x('Loading failed', 'enhanced select', 'woocommerce'),
                        'i18n_input_too_short_1' => _x('Please enter 1 or more characters', 'enhanced select', 'woocommerce'),
                        'i18n_input_too_short_n' => _x('Please enter %qty% or more characters', 'enhanced select', 'woocommerce'),
                        'i18n_input_too_long_1' => _x('Please delete 1 character', 'enhanced select', 'woocommerce'),
                        'i18n_input_too_long_n' => _x('Please delete %qty% characters', 'enhanced select', 'woocommerce'),
                        'i18n_selection_too_long_1' => _x('You can only select 1 item', 'enhanced select', 'woocommerce'),
                        'i18n_selection_too_long_n' => _x('You can only select %qty% items', 'enhanced select', 'woocommerce'),
                        'i18n_load_more' => _x('Loading more results&hellip;', 'enhanced select', 'woocommerce'),
                        'i18n_searching' => _x('Searching&hellip;', 'enhanced select', 'woocommerce'),
                        'ajax_url' => admin_url('admin-ajax.php'),
                        'search_products_nonce' => wp_create_nonce('search-products'),
                        'enquiryLanguage' => $enquiryLanguage,
                    ));
                        break; 
                }
            }
        }
    }

    public function addScriptsAndStyles($hook)
    {
        if ('admin_page_quoteup-details-edit' != $hook) {
            return;
            }

        if (!is_callable('WC')) {
            return;
            }

        $stylesToBeRegistered = array(
            'wdm_data_css'              =>  QUOTEUP_PLUGIN_URL.'/css/admin/edit-quote.css',
            'wdm-mini-cart-css2'        =>  QUOTEUP_PLUGIN_URL.'/css/common.css',
            'quoteup-select2-css'       =>  QUOTEUP_PLUGIN_URL.'/css/admin/quoteup-select2.css',
            'woocommerce-admin-css'     =>  QUOTEUP_PLUGIN_URL.'/css/admin/woocommerce-admin.css',
            'woocommerce_admin_styles'  =>  WC()->plugin_url().'/assets/css/admin.css',
            'products-selection-css'    =>  QUOTEUP_PLUGIN_URL.'/css/admin/products-selection.css'
        );

        $this->registerStyles($stylesToBeRegistered);

        $this->enqueueStyles(array_keys($stylesToBeRegistered));

        if (version_compare(WC_VERSION, '2.6', '>')) {
            $cssString = "th.item-head-img, td.item-content-img {display: none;}";
            wp_add_inline_style('wdm-mini-cart-css2', $cssString);
        }

        $scriptsToBeRegistered = array(
            'quoteup-edit-quote'            =>  QUOTEUP_PLUGIN_URL.'/js/admin/edit-quote.js',
            'quoteup-encode'                =>  QUOTEUP_PLUGIN_URL.'/js/admin/encode-md5.js',
            'quoteup-functions'             =>  QUOTEUP_PLUGIN_URL.'/js/admin/functions.js',
            'quoteup-select2'               =>  QUOTEUP_PLUGIN_URL.'/js/admin/quoteup-select2.js',
            'products-selection-js'         =>  QUOTEUP_PLUGIN_URL.'/js/admin/products-selection.js',
            'wc-enhanced-select-extended'   =>  QUOTEUP_PLUGIN_URL.'/js/admin/enhanced-select-extended.js'    
        );
        $this->registerScripts($scriptsToBeRegistered);

        //Enqueue Essential Scripts
        $this->enqueueScripts(
            array_merge(
                array(
                    'jquery',
                    'jquery-ui-core',
                    'jquery-ui-datepicker',
                    'select2',
                    'postbox',
                ),
            array_keys($scriptsToBeRegistered)
            )
        );


        $this->localizeScripts(array_keys($scriptsToBeRegistered));

        quoteupGetAdminTemplatePart('quote-edit', '', []);
    }

    /*
     * This function is used to display data on enquiry or quote edit page
     */
    public function editQuoteDetails()
    {
        global $quoteup_admin_menu;
        $form_data = quoteupSettings();
        $quoteModal = 1;
        if (isset($form_data[ 'enable_disable_quote' ]) && $form_data[ 'enable_disable_quote' ] == 1) {
            $quoteModal = 0;
        }
        $enquiry_id = filter_var($_GET[ 'id' ], FILTER_SANITIZE_NUMBER_INT);
        $this->resetNewEnquiryStatus($enquiry_id);
        $this->enquiry_details = getEnquiryData($enquiry_id);

        if ($this->enquiry_details == null) {
            echo '<br /><br /><p><strong>'.__('No Enquiry Found.', QUOTEUP_TEXT_DOMAIN).'</strong></p>';

            return;
        }

        ?>
        <div class="wrap">
            <?php screen_icon();?>
            <h1>
                <?php do_action('quoteup-edit-heading', $this->enquiry_details); ?>
            </h1>
            <form name="editQuoteDetailForm" method="post">
                <input type="hidden" name="action" value="editQuoteDetail" />
        <?php
        wp_nonce_field('editQuoteDetail-nonce');
        /* Used to save closed meta boxes and their order */
        wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false);
        wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false);
        ?>
                <div id="poststuff">
                    <div id="post-body" class="metabox-holder">

                        <div id="post-body-content">
                            <p>Admin Page for Editing Product Enquiry Detail.</p>
                        </div>
                        <div id="postbox-container-2" class="postbox-container">
        <?php
        add_meta_box('editCustomerData', __('Customer Data', QUOTEUP_TEXT_DOMAIN), array($this,'customerDataSection'), $quoteup_admin_menu, 'normal');
        do_action('quoteup_after_customer', $this->enquiry_details);

        add_meta_box('editPEDetailMsg', __('Enquiry Remarks and Messages', QUOTEUP_TEXT_DOMAIN), array($this,'editPEDetailMsgFn'), $quoteup_admin_menu, 'normal');
        do_action('PEDetailEdit', $this->enquiry_details);
        do_meta_boxes($quoteup_admin_menu, 'normal', '');
        ?>
                        </div>
                    </div>
                </div>
            </form>
        </div>
                <?php

    }


    public function resetNewEnquiryStatus($enquiry_id)
    {
        global $wpdb;
        $metaTbl = getEnquiryMetaTable();
        $sql = "SELECT meta_value FROM $metaTbl WHERE meta_key = '_unread_enquiry' AND enquiry_id= $enquiry_id";
        $metaValue = $wpdb->get_var($sql);
        if ($metaValue == 'yes')
        {
            $wpdb->update(
            $metaTbl,
            array(
                'meta_value' => 'no',
                ),
            array(
                    'enquiry_id' => $enquiry_id,
                    'meta_key' => '_unread_enquiry',
                    ),
            array(
                '%s',
                ),
            array('%d', '%s')
            );
        }
    }

    /**
     * This function renders the customer data section on enquiry or quote edit page.
     *
     * @return [type] [description]
     */
    public function customerDataSection()
    {
        $form_data = quoteupSettings();
        ?>
        
        <div class='cust_section'>
        <input type='hidden' class='wdm-enq-id' value="<?php echo $_GET[ 'id' ] ?>">
            <input type='hidden' class='admin-url' value='<?php echo admin_url('admin-ajax.php');
        ?>'>
            <article class='wdm-tbl-gen clearfix'>
                <section class='wdm-tbl-gen-sec clearfix wdm-tbl-gen-sec-1'>
                    <div class='wdm-tbl-gen-detail'>
                <div class='wdm-user'>
                    <input id="input-name" type='text' value='<?php echo $this->enquiry_details['name'];
        ?>' class='wdm-input input-field input-name' disabled name='cust_name' required>
                            <label placeholder="<?php _e('Client\'s Full Name', QUOTEUP_TEXT_DOMAIN) ?>" alt="<?php _e('Full Name', QUOTEUP_TEXT_DOMAIN) ?>"></label>
                        </div>
                        <div class='wdm-user-email'>
                            <input id="input-email" type='email' value='<?php echo $this->enquiry_details['email'];
        ?>' class='wdm-input input-field input-email' disabled name='cust_email' required>
                            <label placeholder="<?php _e('Client\'s Email Address', QUOTEUP_TEXT_DOMAIN) ?>" alt="<?php _e('Email', QUOTEUP_TEXT_DOMAIN) ?>"></label>
                        </div>
        
                <div class='wdm-user-ip'>
                    <input type='text' value='<?php echo $this->enquiry_details['enquiry_ip'];
        ?>' class='wdm-input-ip wdm-input' disabled name='cust_ip' required>
                    <label placeholder="<?php _e('Client\'s IP Address', QUOTEUP_TEXT_DOMAIN) ?>" alt="<?php _e('IP Address', QUOTEUP_TEXT_DOMAIN) ?>"></label>
                </div>
                <div class='wdm-user-enquiry-date'>
                    <input type='text' value='<?php echo date('M d, Y', strtotime($this->enquiry_details['enquiry_date']));
        ?>' class='wdm-input-enquiry-date wdm-input' disabled name='enquiry_date'>
                    <label placeholder="<?php _e('Enquiry Date', QUOTEUP_TEXT_DOMAIN) ?>" alt="<?php _e('Enquiry Date', QUOTEUP_TEXT_DOMAIN) ?>"></label>
                </div>

        <?php
        $this->getPhoneNumberField($form_data);
        $this->getDateField($form_data);
        do_action('mep_custom_fields', $this->enquiry_details['enquiry_id']);
        ?>
                    </div>
                </section>
            </article>
            </div>
            <?php
    }

    public function getPhoneNumberField($form_data)
    {
        $enable_ph = 0;
        if (isset($form_data[ 'enable_telephone_no_txtbox' ])) {
            $enable_ph = $form_data[ 'enable_telephone_no_txtbox' ];
        } else {
            $enable_ph = 0;
        }

        if ($enable_ph == 1) {
            do_action('quoteup_before_customer_telephone_column');
            do_action('pep_before_customer_telephone_column');
            $phNumber = $this->enquiry_details['phone_number'];
            if (empty($phNumber)) {
                $phNumber = '-';
            }
            ?>
                        <div class='wdm-user-telephone'>
                            <input type='text' value='<?php echo $phNumber;
            ?>' class='wdm-input-telephone wdm-input' disabled name='cust_telephone' required>
                            <label placeholder="<?php _e('Telephone', QUOTEUP_TEXT_DOMAIN) ?>" alt="<?php _e('Telephone', QUOTEUP_TEXT_DOMAIN) ?>"></label>
                        </div>
            <?php

        }
        do_action('quoteup_after_customer_telephone_column');
        do_action('pep_after_customer_telephone_column');
    }

    public function getDateField($form_data)
    {
        $enable_dt = 0;
        if (isset($form_data[ 'enable_date_field' ])) {
            $enable_dt = $form_data[ 'enable_date_field' ];
        } else {
            $enable_dt = 0;
        }

        if ($enable_dt == 1) {
            do_action('quoteup_before_customer_date_field');
            do_action('pep_before_customer_date_field');
            $dateField = '';
            $dateLabel = 'Date';

            if (isset($form_data[ 'date_field_label' ])) {
                $dateLabel = $form_data[ 'date_field_label' ];
            }

            if (!empty($this->enquiry_details['date_field']) && $this->enquiry_details['date_field'] != '0000-00-00 00:00:00' && $this->enquiry_details['date_field'] != '1970-01-01 00:00:00') {
                $dateField = date('M d, Y', strtotime($this->enquiry_details['date_field']));
            }

            if (empty($dateField)) {
                $dateField = '-';
            }
            ?>

                        <div class='wdm-user-date-field'>
                            <input type='text' value='<?php echo $dateField;
            ?>' class='wdm-input-telephone wdm-input' disabled name='cust_date_field' required>
                            <label placeholder="<?php _e($dateLabel, QUOTEUP_TEXT_DOMAIN) ?>" alt="<?php _e($dateLabel, QUOTEUP_TEXT_DOMAIN) ?>"></label>
                        </div>


                            <?php
                            do_action('quoteup_after_customer_date_field');
                            do_action('pep_after_customer_date_field');
        }
    }

    public function editPEDetailMsgFn()
    {
        global $pep_admin_menu;

        $enquiryProducts = getEnquiryProducts($_GET['id']);

        ?>
        <div id="postbox-container-1" class=""> 
            <table class="remarks-table" cellspacing="0">
                <thead>
                    <tr>
                        <th class="product-name-head"> <?php _e('Product Name', QUOTEUP_TEXT_DOMAIN); ?></th>
                        <th class="remarks-head"><?php _e('Remarks', QUOTEUP_TEXT_DOMAIN); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach ($enquiryProducts as $singleProduct) {
                    $productId = $singleProduct['product_id'];
                    $url = admin_url("/post.php?post={$productId}&action=edit");
                    $productTitle = '<a href='.$url." target='_blank'>".get_the_title($productId).'</a>';
                    if (!$productTitle) {
                        $productTitle = $singleProduct['product_title'];
                    }
                    $remarks = $singleProduct['remark'];

                    ?>
                    <tr>
                        <td class='product-name-content' data-title="Product Name">
                          <p>
                            <?php 
                            echo $productTitle;
                            if (isset($singleProduct[ 'variation_id' ]) && $singleProduct[ 'variation_id' ] != '' && $singleProduct[ 'variation_id' ] != 0) {
                                    $variationString = printVariations($singleProduct);
                                    $variationString = preg_replace(']<br>]', '<br>&#8627 ', $variationString); // Used to add arrow symbol
                                    $variationString = preg_replace(']<br>]', '', $variationString, 1); // Used to remove first br tag
                                    echo "<div style='margin-left:10px'>";
                                    echo $variationString;
                                    echo '</div>';
                            }
                            ?>                            
                            </p>                           
                        </td>
                        <td class='remarks-content' data-title="Remarks">
                          <p><?php echo $remarks; ?></p>                           
                        </td>                        
                    </tr>
                    <?php
                }
                ?>
                    
                </tbody>
            </table>
        <?php
        $this->editPEDetailEnquiryNotesFn();
        do_meta_boxes($pep_admin_menu, 'side', '');
        ?>
        </div>
                        <?php

    }

    public function editPEDetailEnquiryNotesFn()
    {
        global $enquiry_details, $wpdb;
        $enquiryID = filter_var($_GET[ 'id' ], FILTER_SANITIZE_NUMBER_INT);
        $enquiry_tbl = getEnquiryDetailsTable();
        $sql = $wpdb->prepare("SELECT * FROM $enquiry_tbl WHERE enquiry_id = '%d'", $enquiryID);
        $enquiry_details = $wpdb->get_row($sql);
        $enq_tbl = getEnquiryThreadTable();
        $url = admin_url('admin-ajax.php');
        $sql = $wpdb->prepare("SELECT * FROM $enq_tbl WHERE enquiry_id=%d", $enquiryID);
        $reply = $wpdb->get_results($sql);
        echo "<input type='hidden' class='wdm-enquiry-usr' value='{$enquiry_details->email}'/>";
        echo "<input type='hidden' class='admin-url' value='{$url}'/>";
        echo "<div class='msg-wrapper'><div class='wdm-input-ip wdm-enquirymsg'><em>$enquiry_details->subject</em></div>";
        echo "<div class='wdm-input-ip enquiry-message'>$enquiry_details->message</div>";
        echo " <hr class='msg-border'/>";
        $thr_id = $enquiryID;
        foreach ($reply as $msg) {
            $thr_id = $msg->id;
            $sub = $msg->subject;
            $message = $msg->message;
            echo "<div class='msg-wrapper'><div class='wdm-input-ip hide wdm-enquirymsg'><em>{$sub}</em></div>";
            echo "<div wdm-input-ip>{$message}</div>";
            echo " <hr class='msg-border'/>";
            echo '</div>';
        }
        echo "<a href='#' class='rply-link'><button class = 'button'>".__('Reply', QUOTEUP_TEXT_DOMAIN).' &crarr; </button></a>';
        $this->replyThreadSection($thr_id);
        echo '</div>';
    }

    public function replyThreadSection($thr_id)
    {
        global $enquiry_details;
        $sub = $enquiry_details->subject;
        if ($sub == '') {
            $sub = 'Reply for Enquiry';
        }
        ?>
        <div class='reply-div' data-thred-id = '<?php echo $thr_id ?>'>
            <input type='hidden' class='parent-id' value='<?php echo $thr_id ?>'>

            <div class="reply-field-wrap hide" >

                <input type='text' placeholder='Subject' value="<?php echo $sub;
        ?>" name='wdm_reply_subject' class='wdm_reply_subject_<?php echo $thr_id ?> wdm-field reply-field'/>
            </div>

            <div class="reply-field-wrap">
                <textarea class='wdm-field wdm_reply_msg_<?php echo $thr_id ?> reply-field' name='wdm_reply_msg' placeholder="<?php _e('Message', QUOTEUP_TEXT_DOMAIN) ?>"></textarea>
            </div>
            <?php do_action('quoteup_before_reply_customer_enquiry_btn');
        ?>
            <div class="reply-field-wrap reply-field-submitwrap">
                <input type='submit' value='<?php echo __('Send', QUOTEUP_TEXT_DOMAIN);
        ?>' name='btn_submit' class='button button-rply-user button-primary' data_thread_id='<?php echo $thr_id ?>'/>
                <span class='load-ajax'></span>
            </div>
        </div>

        <div class='msg-sent'>

            <div>
                <span class="wdm-pepicon wdm-pepicon-done"></span> <?php echo __('Reply sent successfully', QUOTEUP_TEXT_DOMAIN);
        ?>
            </div>
        </div>
        <!--       <hr class="msg-border"/>
              </div> -->
        <?php

    }

    /**
     * This function is used to get image url.
     *
     * @return [type] [description]
     */
    public function getImageURL($prod)
    {
        $img_url = '';
        if (isset($prod[ 'variation_id' ]) && $prod[ 'variation_id' ] != '') {
            $img_url = wp_get_attachment_url(get_post_thumbnail_id($prod[ 'variation_id' ]));
        }
        if (!$img_url || $img_url == '') {
            $img_url = wp_get_attachment_url(get_post_thumbnail_id($prod[ 'product_id' ]));
        }
        if (!$img_url || $img_url == '') {
            $img_url = WC()->plugin_url().'/assets/images/placeholder.png';
        }

        return $img_url;
    }

    /**
     * This function is used to send sku value.
     * If sku is blank then '-' is sent.
     *
     * @param [string] $sku [sku value]
     *
     * @return [string] [updated sku value]
     */
    public function getSkuValue($sku)
    {
        return empty($sku) ? '-' : $sku;
    }

    public function displayAttachments()
    {
        $upload_dir = wp_upload_dir();
        $attachmentDirectory = $upload_dir[ 'basedir' ].'/QuoteUp_Files/'.$_GET['id'].'/';
        $attachmentDirURL = $upload_dir[ 'baseurl' ].'/QuoteUp_Files/'.$_GET['id'].'/';
        if(file_exists($attachmentDirectory) && count(glob("$attachmentDirectory/*")) !== 0) {
            ?>
        <div class="display-attachment-main">
        <?php
        if ($handle = opendir($attachmentDirectory)) {
            $thelist = '';
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    $thelist .= '<div class="attachment-div"><img class="wdm-attachment-img" src="'.QUOTEUP_PLUGIN_URL.'/images/attachment.png"/> <a href="'.$attachmentDirURL.$file.'" download="'.$file.'">'.$file.'</a></div>';
                }
            }
                closedir($handle);
        }
        echo "<h3>".__('Attachments', QUOTEUP_TEXT_DOMAIN).":</h3>";
        echo $thelist;
        ?>
        </div>
        <?php
        }
    }
}
