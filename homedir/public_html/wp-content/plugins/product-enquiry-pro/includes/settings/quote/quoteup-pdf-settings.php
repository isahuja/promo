<?php

namespace Includes\Settings;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * It displays PDF settings on general settings page. It shows following fields
 * - Company Name
 * - Company Email
 * - Company Address
 * - Logo for PDF.
 *
 * @param [array] $form_data [Settings stored previously in database]
 *
 * @return [type] [description]
 */
function pdfSettingsSection($form_data)
{
    if (!isset($form_data[ 'company_name' ])) {
        $form_data[ 'company_name' ] = get_bloginfo();
    }

    if (!isset($form_data[ 'company_logo' ])) {
        $form_data[ 'company_logo' ] = '';
    }
    if (!isset($form_data[ 'company_address' ])) {
        $form_data[ 'company_address' ] = '';
    }

    if (!isset($form_data[ 'company_email' ])) {
        $form_data[ 'company_email' ] = get_option('admin_email');
    }
    ?>
    <fieldset>
        <?php
         $helptip = __('These settings will add details to the generated quote when the Quotation System is enabled.', QUOTEUP_TEXT_DOMAIN);
        $tip = \quoteupHelpTip($helptip, true);
        echo '<legend>'.__('PDF Settings ', QUOTEUP_TEXT_DOMAIN).$tip.'</legend>';

    ?>

        <div class="fd">
            <div class='left_div'>
                <label for="enable_disable_quote_pdf"> <?php _e('Enable PDF', QUOTEUP_TEXT_DOMAIN) ?> </label>
            </div>
            <div class='right_div'>
                <?php
                $helptip = __('You can enable/disable PDF.', QUOTEUP_TEXT_DOMAIN);
                echo \quoteupHelpTip($helptip, true);
        ?>          
                <input type="checkbox" class="wdm_wpi_input wdm_wpi_checkbox" value="1" <?php checked(1, isset($form_data[ 'enable_disable_quote_pdf' ]) ? $form_data[ 'enable_disable_quote_pdf' ] : 0);
        ?> id="enable-disable-pdf" /> 
                <input type="hidden" name="wdm_form_data[enable_disable_quote_pdf]" value="<?php echo isset($form_data[ 'enable_disable_quote_pdf' ]) && $form_data[ 'enable_disable_quote_pdf' ] == 1 ? $form_data[ 'enable_disable_quote_pdf' ] : 0 ?>" />
            </div>
            <div class='clear'></div>
        </div>

        <div class="fd toggle-pdf">
            <div class='left_div'>
                <label for="wdm_company_name"> <?php _e('Company Name', QUOTEUP_TEXT_DOMAIN) ?> </label>
            </div>
            <div class='right_div'>
                <input type="text" class="wdm_wpi_text input-without-tip" name="wdm_form_data[company_name]" value="<?php echo $form_data[ 'company_name' ] ?>">
            </div>
            <div class='clear'></div>
        </div >

        <div class="fd toggle-pdf">
            <div class='left_div'>
                <label for="wdm_company_email"> <?php _e('Company Email', QUOTEUP_TEXT_DOMAIN) ?> </label>
            </div>
            <div class='right_div'>
                <input class="wdm_wpi_text input-without-tip" type="text" name="wdm_form_data[company_email]" value="<?php echo $form_data[ 'company_email' ] ?>">
            </div>
            <div class='clear'></div>
        </div >

        <div class="fd toggle-pdf">
            <div class='left_div'>
                <label for="wdm_company_add"> <?php _e('Company Address', QUOTEUP_TEXT_DOMAIN) ?> </label>
            </div>
            <div class='right_div'>
                <textarea class="wdm_wpi_text input-without-tip"  name="wdm_form_data[company_address]" rows="5"><?php echo $form_data[ 'company_address' ] ?></textarea>
            </div>
            <div class='clear'></div>
        </div >

        <div class="fd toggle-pdf">
            <div class='left_div'>
            <br>
                <label for="wdm_company_logo"> <?php _e('Logo for PDF', QUOTEUP_TEXT_DOMAIN) ?> </label>
            </div>
            <div class='right_div'>
                <div id="tgm-new-media-settings">
                    <p><input class="wdm_wpi_text input-without-tip" type="text" id="tgm-new-media-image" size="70" value="<?php echo $form_data[ 'company_logo' ] ?>" name="wdm_form_data[company_logo]" /><a href="#" class="tgm-open-media button button-primary wdm-media-upload-button" title="Upload Logo"><?php _e('Upload Image', QUOTEUP_TEXT_DOMAIN) ?> </a> 
                </div>
            </div>
            <div class='clear'></div>
        </div >
    </fieldset>
    <?php
}

pdfSettingsSection($form_data);
