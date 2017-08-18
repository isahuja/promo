<?php

namespace Includes\Settings;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/*
 * This function is used to display form option fields
 */
function formOptionsSection($form_data)
{
    ?>
    <fieldset>

        <?php
        echo '<legend>'.__('Form Options', QUOTEUP_TEXT_DOMAIN).'</legend>';

        enquiryButtonLabel($form_data);
        replaceEnquiry($form_data);
        enquiryButtonLocation($form_data);
        enquiryAsLink($form_data);
        displayWisdmlabs($form_data);
        sendMeCopy($form_data);
        dateField($form_data);
        dateFieldMandatory($form_data);
        dateFieldLabel($form_data);
        telephoneNumber($form_data);
        telephoneNumberMandatory($form_data);
        attachField($form_data);
        attachFieldMandatory($form_data);
        attachFieldLabel($form_data);
        enableGoogleCaptcha($form_data);
        googleSiteKey($form_data);
        googleSecretKey($form_data);
    ?>
    </fieldset>
    <?php
}

/**
 * This is used to show Enquiry button label on settings page.
 *
 * @param [array] $form_data [Settings stored previously in database]
 *
 * @return [type] [description]
 */
function enquiryButtonLabel($form_data)
{
    ?>

    <div class="fd">
        <div class='left_div'>
            <label for="custom_label">
                <?php _e(' Enquiry Button Label ', QUOTEUP_TEXT_DOMAIN) ?>
            </label>
        </div>
        <div class='right_div'>
            <?php
            $helptip = __('Add custom label for Enquiry or Quote button.', QUOTEUP_TEXT_DOMAIN);
            echo \quoteupHelpTip($helptip, true);
    ?>
            <input type="text" class="wdm_wpi_input wdm_wpi_text" name="wdm_form_data[custom_label]"
                   value="<?php echo empty($form_data[ 'custom_label' ]) ? _e('Make an Enquiry', QUOTEUP_TEXT_DOMAIN) : $form_data[ 'custom_label' ];
    ?>" id="custom_label"  />
        </div>
    </div>


    <?php
}


/**
 * This is used to replace 'enquiry' words on settings page.
 *
 * @param [array] $form_data [Settings stored previously in database]
 *
 * @return [type] [description]
 */
function replaceEnquiry($form_data)
{
    ?>

    <div class="fd">
        <div class='left_div'>
            <label for="replace_enquiry">
                <?php _e(' Alternate word for Enquiry ', QUOTEUP_TEXT_DOMAIN) ?>
            </label>
        </div>
        <div class='right_div'>
            <?php
            $helptip = __('Alternate word for Enquiry.', QUOTEUP_TEXT_DOMAIN);
            echo \quoteupHelpTip($helptip, true);
    ?>
            <input type="text" class="wdm_wpi_input wdm_wpi_text" name="wdm_form_data[replace_enquiry]"
                   value="<?php echo empty($form_data[ 'replace_enquiry' ]) ? 'Enquiry' : $form_data[ 'replace_enquiry' ];
    ?>" id="replace_enquiry"  />
        </div>
        <div class="clear"></div>
    </div>


    <?php
}

/**
 * This is used to show Enquiry button location on settings page.
 *
 * @param [array] $form_data [Settings stored previously in database]
 *
 * @return [type] [description]
 */
function enquiryButtonLocation($form_data)
{
    ?>

    <div class="fd">
        <div class='left_div'>
            <label>
                <?php _e(' Button Location', QUOTEUP_TEXT_DOMAIN) ?>

            </label>

        </div>
        <div class='right_div'>
            <?php
            if (isset($form_data[ 'pos_radio' ])) {
                $pos = $form_data[ 'pos_radio' ];
            } else {
                $pos = 'show_after_summary';
            }
    ?>

            <input type="radio" class="wdm_wpi_input wdm_wpi_checkbox input-without-tip" name="wdm_form_data[pos_radio]"
                   value="show_after_summary" 
                    <?php
                    if ($pos == 'show_after_summary') {
                    ?> 
                    checked 
                    <?php
                    } ?> id="show_after_summary" />
                    <?php echo '<em>'.__(' After single product summary ', QUOTEUP_TEXT_DOMAIN).'</em>';
    ?>

            <br />


            <input type="radio" class="wdm_wpi_input wdm_wpi_checkbox input-without-tip" name="wdm_form_data[pos_radio]" value="show_at_page_end" 
            <?php
            if ($pos == 'show_at_page_end') {
                ?>
                checked
                <?php
            } ?> id="show_at_page_end" />

            <?php echo '<em>'.__(' At the end of single product page ', QUOTEUP_TEXT_DOMAIN).'</em>';
    ?>
        </div>
        <div class="clear"></div>
    </div>

    <?php
}

/**
 * This is used to show checkbox for show enquiry button as a link on settings page.
 *
 * @param [array] $form_data [Settings stored previously in database]
 *
 * @return [type] [description]
 */
function enquiryAsLink($form_data)
{
    $showButtonAsLink = isset($form_data[ 'show_button_as_link' ]) ? $form_data[ 'show_button_as_link' ] : 0;
    ?>

    <div class="fd">
        <div class='left_div'>
            <label for="link">
                <?php _e(' Display Enquiry Button As A Link ', QUOTEUP_TEXT_DOMAIN) ?>
            </label>
        </div>
        <div class='right_div'>
            <input type="checkbox" class="wdm_wpi_input wdm_wpi_checkbox input-without-tip" value="1" <?php checked(1, $showButtonAsLink);
    ?> id="show_button_as_link" />
            <input type="hidden" name="wdm_form_data[show_button_as_link]" value="<?php echo isset($form_data[ 'show_button_as_link' ]) && $form_data[ 'show_button_as_link' ] == 1 ? $form_data[ 'show_button_as_link' ] : 0 ?>" />

        </div>
        <div class="clear"></div>
    </div>

    <?php
}

/**
 * This is used to show checkbox for show footer on form.
 *
 * @param [array] $form_data [Settings stored previously in database]
 *
 * @return [type] [description]
 */
function displayWisdmlabs($form_data)
{
    $displayWisdmlabs = isset($form_data[ 'show_powered_by_link' ]) ? $form_data[ 'show_powered_by_link' ] : 0;
    //Don't show option to Display Powered by WisdmLabs if not checked till now
    if ($displayWisdmlabs != 1) {
        return;
    }
    ?>

    <div class="fd">
        <div class='left_div'>
            <label for="link">
                <?php _e(" Display 'Powered by WisdmLabs' ", QUOTEUP_TEXT_DOMAIN) ?>
            </label>
        </div>
        <div class='right_div'>
            <input type="checkbox" class="wdm_wpi_input wdm_wpi_checkbox input-without-tip" value="1" <?php checked(1, $displayWisdmlabs);
    ?> id="show_powered_by_link" />
            <input type="hidden" name="wdm_form_data[show_powered_by_link]" value="<?php echo isset($form_data[ 'show_powered_by_link' ]) && $form_data[ 'show_powered_by_link' ] == 1 ? $form_data[ 'show_powered_by_link' ] : 0 ?>" />

        </div>
        <div class="clear"></div>
    </div>

    <?php
}

/**
 * This is used to show checkbox for send me a copy on settings page.
 *
 * @param [array] $form_data [Settings stored previously in database]
 *
 * @return [type] [description]
 */
function sendMeCopy($form_data)
{
    ?>

    <div class="fd">
        <div class='left_div'>
            <label for="enable_send_mail_copy">
                <?php _e(" Display 'Send me a copy' ", QUOTEUP_TEXT_DOMAIN) ?>
            </label>

        </div>
        <div class='right_div'>
            <?php
            $helptip = __('This will display \'Send me a copy\' checkbox on Enquiry or Quote request form.', QUOTEUP_TEXT_DOMAIN);
            echo \quoteupHelpTip($helptip, true);
    ?>
            <input type="checkbox" class="wdm_wpi_input wdm_wpi_checkbox" value="1" <?php checked(1, isset($form_data[ 'enable_send_mail_copy' ]) ? $form_data[ 'enable_send_mail_copy' ] : 0);
    ?> id="enable_send_mail_copy" />
            <input type="hidden" name="wdm_form_data[enable_send_mail_copy]" value="<?php echo isset($form_data[ 'enable_send_mail_copy' ]) && $form_data[ 'enable_send_mail_copy' ] == 1 ? $form_data[ 'enable_send_mail_copy' ] : 0 ?>" />

        </div>
        <div class="clear"></div>
    </div>

    <?php
}

/**
 * This is used to show checkbox for 'Date Field' on settings page.
 *
 * @param [array] $form_data [Settings stored previously in database]
 *
 * @return [type] [description]
 */
function dateField($form_data)
{
    ?>

    <div class="fd">
        <div class='left_div'>
            <label for="enable_date_field">
                <?php _e(" Display 'Date Field' ", QUOTEUP_TEXT_DOMAIN) ?>
            </label>

        </div>
        <div class='right_div'>
            <?php
            $helptip = __('This will display \'Date Field\' on Enquiry or Quote request form.', QUOTEUP_TEXT_DOMAIN);
            echo \quoteupHelpTip($helptip, true);
    ?>
            <input type="checkbox" class="wdm_wpi_input wdm_wpi_checkbox" value="1" <?php checked(1, isset($form_data[ 'enable_date_field' ]) ? $form_data[ 'enable_date_field' ] : 0);
    ?> id="enable_date_field" />
            <input type="hidden" name="wdm_form_data[enable_date_field]" value="<?php echo isset($form_data[ 'enable_date_field' ]) && $form_data[ 'enable_date_field' ] == 1 ? $form_data[ 'enable_date_field' ] : 0 ?>" />

        </div>
        <div class="clear"></div>
    </div>

    <?php
}

/**
 * This is used to show Date Field label on settings page.
 *
 * @param [array] $form_data [Settings stored previously in database]
 *
 * @return [type] [description]
 */
function dateFieldLabel($form_data)
{
    ?>

    <div class="fd toggle-date">
        <div class='left_div'>
            <label for="date_field_label">
                <?php _e(' Date Field Label ', QUOTEUP_TEXT_DOMAIN) ?>
            </label>
        </div>
        <div class='right_div'>
            <?php
            $helptip = __('Add custom label for Date Field.', QUOTEUP_TEXT_DOMAIN);
            echo \quoteupHelpTip($helptip, true);
    ?>
            <input type="text" class="wdm_wpi_input wdm_wpi_text" name="wdm_form_data[date_field_label]"
                   value="<?php echo empty($form_data[ 'date_field_label' ]) ? _e('Date', QUOTEUP_TEXT_DOMAIN) : $form_data[ 'date_field_label' ];
    ?>" id="date_field_label"  />
        </div>
        <div class="clear"></div>
    </div>


    <?php
}

/**
 * This is used to show checkbox for date field mandatory on settings page.
 *
 * @param [array] $form_data [Settings stored previously in database]
 *
 * @return [type] [description]
 */
function dateFieldMandatory($form_data)
{
    $display = '';
    ?>
    <div class="fd toggle-date" <?php echo $display;
    ?>>
        <div class='left_div'>
            <label for="make_date_mandatory">
                <?php _e(' Make Date Field Mandatory', QUOTEUP_TEXT_DOMAIN) ?>
            </label>
        </div>
        <div class='right_div'>
            <input type="checkbox" class="wdm_wpi_input wdm_wpi_checkbox input-without-tip" value="1" <?php checked(1, isset($form_data[ 'make_date_mandatory' ]) ? $form_data[ 'make_date_mandatory' ] : 0);
    ?> id="make_date_mandatory" />
            <input type="hidden" name="wdm_form_data[make_date_mandatory]" value="<?php echo isset($form_data[ 'make_date_mandatory' ]) && $form_data[ 'make_date_mandatory' ] == 1 ? $form_data[ 'make_date_mandatory' ] : 0 ?>" />
        </div>
        <div class="clear"></div>
    </div>

    <?php
}

/**
 * This is used to show checkbox for Telephone number on settings page.
 *
 * @param [array] $form_data [Settings stored previously in database]
 *
 * @return [type] [description]
 */
function telephoneNumber($form_data)
{
    ?>

    <div class="fd">
        <div class='left_div'>
            <label for="enable_telephone_no_txtbox">
                <?php _e(' Display Telephone Number Field', QUOTEUP_TEXT_DOMAIN) ?>
            </label>
        </div>
        <div class='right_div'>
            <?php
            $helptip = __('Display Telephone number field on Enquiry and Quote Request form.', QUOTEUP_TEXT_DOMAIN);
            echo \quoteupHelpTip($helptip, true);
    ?>
            <input type="checkbox" class="wdm_wpi_input wdm_wpi_checkbox" value="1" <?php checked(1, isset($form_data[ 'enable_telephone_no_txtbox' ]) ? $form_data[ 'enable_telephone_no_txtbox' ] : 0);
    ?> id="enable_telephone_no_txtbox" />
            <input type="hidden" name="wdm_form_data[enable_telephone_no_txtbox]" value="<?php echo isset($form_data[ 'enable_telephone_no_txtbox' ]) && $form_data[ 'enable_telephone_no_txtbox' ] == 1 ? $form_data[ 'enable_telephone_no_txtbox' ] : 0 ?>" />
        </div>
        <div class="clear"></div>
    </div>

    <?php
}

/**
 * This is used to show checkbox for telephone number mandatory on settings page.
 *
 * @param [array] $form_data [Settings stored previously in database]
 *
 * @return [type] [description]
 */
function telephoneNumberMandatory($form_data)
{
    $display = '';
    if (!isset($form_data[ 'enable_telephone_no_txtbox' ]) || (isset($form_data[ 'enable_telephone_no_txtbox' ]) && $form_data[ 'enable_telephone_no_txtbox' ] == 0)) {
        $display = "style='display:none'";
    }
    ?>
    <div class="fd toggle" <?php echo $display;
    ?>>
        <div class='left_div'>
            <label for="make_phone_mandatory">
                <?php _e(' Make Telephone Number Field Mandatory', QUOTEUP_TEXT_DOMAIN) ?>
            </label>
        </div>
        <div class='right_div'>
            <input type="checkbox" class="wdm_wpi_input wdm_wpi_checkbox input-without-tip" value="1" <?php checked(1, isset($form_data[ 'make_phone_mandatory' ]) ? $form_data[ 'make_phone_mandatory' ] : 0);
    ?> id="make_phone_mandatory" />
            <input type="hidden" name="wdm_form_data[make_phone_mandatory]" value="<?php echo isset($form_data[ 'make_phone_mandatory' ]) && $form_data[ 'make_phone_mandatory' ] == 1 ? $form_data[ 'make_phone_mandatory' ] : 0 ?>" />
        </div>
        <div class="clear"></div>
    </div>

    <?php
}

function attachField($form_data)
{
    ?>
    <div class="fd">
        <div class='left_div'>
            <label for="enable_attach_field">
                <?php _e(' Display Attach Field', QUOTEUP_TEXT_DOMAIN) ?>
            </label>
        </div>
        <div class='right_div'>
            <?php
            $helptip = __('Display Attach field on Enquiry and Quote Request form.', QUOTEUP_TEXT_DOMAIN);
            echo \quoteupHelpTip($helptip, true);
    ?>
            <input type="checkbox" class="wdm_wpi_input wdm_wpi_checkbox" value="1" <?php checked(1, isset($form_data[ 'enable_attach_field' ]) ? $form_data[ 'enable_attach_field' ] : 0);
    ?> id="enable_attach_field" />
            <input type="hidden" name="wdm_form_data[enable_attach_field]" value="<?php echo isset($form_data[ 'enable_attach_field' ]) && $form_data[ 'enable_attach_field' ] == 1 ? $form_data[ 'enable_attach_field' ] : 0 ?>" />
        </div>
        <div class="clear"></div>
    </div>
    <?php
}

function attachFieldMandatory($form_data)
{
    $display = '';
    ?>
    <div class="fd toggle-attach" <?php echo $display;
    ?>>
        <div class='left_div'>
            <label for="make_attach_mandatory">
                <?php _e(' Make Attach Field Mandatory', QUOTEUP_TEXT_DOMAIN) ?>
            </label>
        </div>
        <div class='right_div'>
            <input type="checkbox" class="wdm_wpi_input wdm_wpi_checkbox input-without-tip" value="1" <?php checked(1, isset($form_data[ 'make_attach_mandatory' ]) ? $form_data[ 'make_attach_mandatory' ] : 0);
    ?> id="make_attach_mandatory" />
            <input type="hidden" name="wdm_form_data[make_attach_mandatory]" value="<?php echo isset($form_data[ 'make_attach_mandatory' ]) && $form_data[ 'make_attach_mandatory' ] == 1 ? $form_data[ 'make_attach_mandatory' ] : 0 ?>" />
        </div>
        <div class="clear"></div>
    </div>

    <?php
}

function attachFieldLabel($form_data)
{
    ?>
    <div class="fd toggle-attach">
        <div class='left_div'>
            <label for="attach_field_label">
                <?php _e(' Attach Field Label ', QUOTEUP_TEXT_DOMAIN) ?>
            </label>
        </div>
        <div class='right_div'>
            <?php
            $helptip = __('Add custom label for Attach Field.', QUOTEUP_TEXT_DOMAIN);
            echo \quoteupHelpTip($helptip, true);
    ?>
            <input type="text" class="wdm_wpi_input wdm_wpi_text" name="wdm_form_data[attach_field_label]"
                   value="<?php echo empty($form_data[ 'attach_field_label' ]) ? _e('Attach File', QUOTEUP_TEXT_DOMAIN) : $form_data[ 'attach_field_label' ];
    ?>" id="attach_field_label"  />
        </div>
        <div class="clear"></div>
    </div>
    <?php
}

function enableGoogleCaptcha($form_data)
{
    ?>
    <div class="fd">
        <div class='left_div'>
            <label for="enable_google_captcha">
                <?php _e(' Enable google captcha', QUOTEUP_TEXT_DOMAIN) ?>
            </label>
        </div>
        <div class='right_div'>
            <?php
            $helptip = __('Enable google captcha on Enquiry and Quote Request form.', QUOTEUP_TEXT_DOMAIN);
            echo \quoteupHelpTip($helptip, true);
    ?>
            <input type="checkbox" class="wdm_wpi_input wdm_wpi_checkbox" value="1" <?php checked(1, isset($form_data[ 'enable_google_captcha' ]) ? $form_data[ 'enable_google_captcha' ] : 0);
    ?> id="enable_google_captcha" />
            <input type="hidden" name="wdm_form_data[enable_google_captcha]" value="<?php echo isset($form_data[ 'enable_google_captcha' ]) && $form_data[ 'enable_google_captcha' ] == 1 ? $form_data[ 'enable_google_captcha' ] : 0 ?>" />
        </div>
        <div class="clear"></div>
    </div>
    <?php
}

function googleSiteKey($form_data)
{
    ?>

    <div class="fd toggle-captcha">
        <div class='left_div'>
            <label for="google_site_key">
                <?php _e(' Google Site Key ', QUOTEUP_TEXT_DOMAIN) ?>
            </label>
        </div>
        <div class='right_div'>
            <?php
            $helptip = __('Site key after ragistration for google captcha.', QUOTEUP_TEXT_DOMAIN);
            echo \quoteupHelpTip($helptip, true);
    ?>
            <input type="text" class="wdm_wpi_input wdm_wpi_text" name="wdm_form_data[google_site_key]"
                   value="<?php echo empty($form_data[ 'google_site_key' ]) ? '' : $form_data[ 'google_site_key' ];
    ?>" id="google_site_key"  />

            <a class="new_page_link" target="_blank" href="https://www.google.com/recaptcha/"> <?php _e('Create Recaptcha Key', QUOTEUP_TEXT_DOMAIN); ?> </a>
        </div>
        <!-- <div class="clear"></div> -->
    </div>
    <?php
}

function googleSecretKey($form_data)
{
    ?>
    <div class="fd toggle-captcha">
        <div class='left_div'>
            <label for="google_secret_key">
                <?php _e(' Google Secret Key ', QUOTEUP_TEXT_DOMAIN) ?>
            </label>
        </div>
        <div class='right_div'>
            <?php
            $helptip = __('Secret key after ragistration for google captcha.', QUOTEUP_TEXT_DOMAIN);
            echo \quoteupHelpTip($helptip, true);
    ?>
            <input type="text" class="wdm_wpi_input wdm_wpi_text" name="wdm_form_data[google_secret_key]"
                   value="<?php echo empty($form_data[ 'google_secret_key' ]) ? '' : $form_data[ 'google_secret_key' ]; ?>" id="google_secret_key"  />
        </div>
        <!-- <div class="clear"></div> -->
    </div>


    <?php
}

formOptionsSection($form_data);
