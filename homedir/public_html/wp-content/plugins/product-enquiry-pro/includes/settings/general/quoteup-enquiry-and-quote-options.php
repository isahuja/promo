<?php

namespace Includes\Settings;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/*
 * This function is used to display Enable disable quote field
 */
function enableDisableQuoteUpSection($form_data)
{
    ?>
    <fieldset>
        <?php
        echo '<legend>'.__('Enquiry & Quote Options', QUOTEUP_TEXT_DOMAIN).'</legend>';
        quotemoduleEnableDisable($form_data);
        enquiryOutofStock($form_data);
        enquiryButtonShopPage($form_data);
    ?>
    </fieldset>
    <?php
}
/*
 * This function is used to display Quote Module enable disable checkbox
 */
function quotemoduleEnableDisable($form_data)
{
    ?>
    <div class="fd">
        <div class='left_div'>
            <label for="quote-enable-disable"> <?php _e('Disable Quotation System', QUOTEUP_TEXT_DOMAIN) ?> </label>
        </div>
        <div class='right_div'>
        <?php
        $helptip = __('Disable quote management system', QUOTEUP_TEXT_DOMAIN);
        echo \quoteupHelpTip($helptip, true);
    ?>
            <input type="checkbox" class="wdm_wpi_input wdm_wpi_checkbox" value="1" <?php checked(1, isset($form_data[ 'enable_disable_quote' ]) ? $form_data[ 'enable_disable_quote' ] : 0);
    ?> id="quote-enable-disable" /> 
            <input type="hidden" name="wdm_form_data[enable_disable_quote]" value="<?php echo isset($form_data[ 'enable_disable_quote' ]) && $form_data[ 'enable_disable_quote' ] == 1 ? $form_data[ 'enable_disable_quote' ] : 0?>" /> 
    
        </div>
        <div class='clear'></div>
    </div >
    <?php
}

/*
 * This function is used to display Out Of Stock checkbox
 */
function enquiryOutofStock($form_data)
{
    ?>

    <!--enambe only when out of stock -->
    <div class="fd">
        <div class='left_div'>
            <label for="only_if_out_of_stock">
    <?php _e('Display Enquiry or Quote button only when \'Out of Stock\'', QUOTEUP_TEXT_DOMAIN) ?>
            </label>
        </div>
        <div class='right_div'>
        <?php
        $helptip = __('Display enquiry and quote button only after the product runs out of stock', QUOTEUP_TEXT_DOMAIN);
        echo \quoteupHelpTip($helptip, true);
    ?>
            <input type="checkbox" class="wdm_wpi_input wdm_wpi_checkbox" value="1" <?php checked(1, isset($form_data[ 'only_if_out_of_stock' ]) ? $form_data[ 'only_if_out_of_stock' ] : 0);
    ?> id="only_if_out_of_stock" /> 
            <input type="hidden" name="wdm_form_data[only_if_out_of_stock]" value="<?php echo isset($form_data[ 'only_if_out_of_stock' ]) && $form_data[ 'only_if_out_of_stock' ] == 1 ? $form_data[ 'only_if_out_of_stock' ] : 0?>" />
        </div>
        <div class="clear"></div>
    </div>
    <!--end-->

    <?php
}

/*
 * This function is used to display Enquiry Button on Shop/Archive Page checkbox
 */
function enquiryButtonShopPage($form_data)
{
    ?>
    <div class="fd">
        <div class='left_div'>
            <label for="show_enquiry_on_shop">
    <?php _e('Display Enquiry or Quote button on Archive Page ', QUOTEUP_TEXT_DOMAIN) ?>
            </label>
        </div>
        <div class='right_div'>
        <?php
        $helptip = __('Display enquiry and quote button on Archive (Shop, Categories) Page', QUOTEUP_TEXT_DOMAIN);
        echo \quoteupHelpTip($helptip, true);
    ?>
            <input type="checkbox" class="wdm_wpi_input wdm_wpi_checkbox" value="1" <?php checked(1, isset($form_data[ 'show_enquiry_on_shop' ]) ? $form_data[ 'show_enquiry_on_shop' ] : 0);
    ?> id="show_enquiry_on_shop" />
            <input type="hidden" name="wdm_form_data[show_enquiry_on_shop]" value="<?php echo isset($form_data[ 'show_enquiry_on_shop' ]) && $form_data[ 'show_enquiry_on_shop' ] == 1 ? $form_data[ 'show_enquiry_on_shop' ] : 0;
    ?>" />

        </div>
        <div class="clear"></div>
    </div>

    <?php
}

enableDisableQuoteUpSection($form_data);
