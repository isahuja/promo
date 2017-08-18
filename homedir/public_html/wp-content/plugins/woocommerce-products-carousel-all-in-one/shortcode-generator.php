<?php
/*
Author: Marcin Gierada
Author URI: http://www.teastudio.pl/
Author Email: m.gierada@teastudio.pl
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>

<script type="text/javascript">
function insert_shortcode() {
    if ( jQuery('#woocommerce-products-carousel-all-in-one-form .field-invalid').length > 0) {
        alert("<?php _e('Error detected.\nPlease correct your form and try again.', 'woocommerce-products-carousel-all-in-one') ?>");
    } else {
        var shortcode = '[woocommerce_products_carousel_all_in_one';
        var custom_breakpoints = '';
        jQuery('#woocommerce-products-carousel-all-in-one-form .woocommerce-products-carousel-all-in-one-left-sidebar').find('.woocommerce-products-carousel-all-in-one-field').filter(function() {
            var val = null;

            if ( this.type != "button" ) {
                if( this.type == "checkbox" ) {
                    val = this.checked ? "true" : "false";
                } else if ( this.type == "select-multiple") {
                    val = jQuery("option:selected", this).length > 1 ? jQuery(this).val().join(",") : this.value;
                } else {
                    if ( jQuery(this).hasClass('woocommerce-products-carousel-all-in-one-custom-breakpoint') ) {
                        custom_breakpoints += jQuery(this).attr('id').split('_')[2] + ':' + jQuery(this).val() + ','
                    } else {
                        val = this.value;
                    }
                }
                if ( !jQuery(this).hasClass('woocommerce-products-carousel-all-in-one-custom-breakpoint') ) {
                    var name = this.name.replace(/\[|\]/g, '');
                    shortcode += ' '+jQuery.trim( name )+'="'+jQuery.trim( val )+'"';
                }
            }
        });

        if (custom_breakpoints != null) {
            shortcode += ' custom_breakpoints="'+ custom_breakpoints.slice(0,-1) +'"';
        }
        shortcode +=']';
        tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
        tb_remove();
    }
}
</script>

<div class="widget metabox-holder has-right-sidebar woocommerce-products-carousel-all-in-one-form" id="woocommerce-products-carousel-all-in-one-form">
    <div  class="woocommerce-products-carousel-all-in-one-sidebar woocommerce-products-carousel-all-in-one-right-sidebar">
        <br />
        <?php include( 'includes/plugin-info.php' ); ?>
    </div>

    <div class="woocommerce-products-carousel-all-in-one-left-sidebar">
        <br />
        <table cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th colspan="2">
                        <h2><?php _e('Basic options', 'woocommerce-products-carousel-all-in-one') ?></h2>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th><?php _e('Products limit', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                    <td>
                        <input class="woocommerce-products-carousel-all-in-one-field field-validate" type="number" name="all_items" id="all_items" value="10" size="5" required min="1" pattern="^\d+$" />
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <fieldset>
                            <legend><?php _e('Product Selection Criteria', 'woocommerce-products-carousel-all-in-one') ?></legend>

                            <table cellspacing="0" cellpadding="0">
                                <tr>
                                    <th><?php _e('Show Products', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                                    <td>
                                        <select class="select woocommerce-products-carousel-all-in-one-field" name="show_only" id="show_only">
                                        <?php
                                            $show_list = WooCommerce_Products_Carousel_All_In_One_Utils::getShows();
                                            foreach($show_list as $key => $list) {
                                                echo "<option value=\"". $key ."\">". $list ."</option>";
                                            }
                                        ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php _e('Include out of stock', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                                    <td>
                                        <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" value="1" name="out_of_stock" id="out_of_stock" />
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="woocommerce-products-carousel-all-in-one-inclusion"><?php _e('but', 'woocommerce-products-carousel-all-in-one') ?></td>
                                </tr>
                                <tr>
                                    <th><?php _e('Exclude Products with IDs', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                                    <td>
                                        <textarea class="widefat woocommerce-products-carousel-all-in-one-field field-validate" name="exclude" id="exclude" pattern="^[1-9](0*)(,?[1-9](0*))*$"></textarea>
                                        <br />
                                        <?php echo WooCommerce_Products_Carousel_All_In_One_Utils::getTooltip( __('Please enter Product IDs with comma seperated to exlude from display.', 'woocommerce-products-carousel-all-in-one') );?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="woocommerce-products-carousel-all-in-one-separator woocommerce-products-carousel-all-in-one-inclusion">
                                        <?php _e('or', 'woocommerce-products-carousel-all-in-one') ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php _e('Show Products with IDs', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                                    <td>
                                        <textarea class="widefat woocommerce-products-carousel-all-in-one-field field-validate" name="products" id="products" pattern="^[1-9](0*)(,?[1-9](0*))*$"></textarea>
                                        <br />
                                        <?php echo WooCommerce_Products_Carousel_All_In_One_Utils::getTooltip( __('Please enter Product IDs with comma seperated.', 'woocommerce-products-carousel-all-in-one') );?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="woocommerce-products-carousel-all-in-one-separator woocommerce-products-carousel-all-in-one-inclusion">
                                        <?php _e('also include', 'woocommerce-products-carousel-all-in-one') ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php _e('Products with Category IDs', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                                    <td>
                                        <textarea class="widefat woocommerce-products-carousel-all-in-one-field field-validate" name="categories" id="categories" pattern="^[1-9](0*)(,?[1-9](0*))*$"></textarea>
                                        <br />
                                        <?php echo WooCommerce_Products_Carousel_All_In_One_Utils::getTooltip( __('Please enter Category IDs with comma seperated.', 'woocommerce-products-carousel-all-in-one') ); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="woocommerce-products-carousel-all-in-one-separator woocommerce-products-carousel-all-in-one-inclusion">
                                        <?php _e('And/Or', 'woocommerce-products-carousel-all-in-one'); ?>
                                    </th>
                                    <td>
                                        <select class="select woocommerce-products-carousel-all-in-one-field" name="relation" id="relation" class="select">
                                        <?php
                                            $relation_list = WooCommerce_Products_Carousel_All_In_One_Utils::getRelations();
                                            foreach($relation_list as $key => $list) {
                                                echo '<option value="' . $key . '">' . $list . '</option>';
                                            }
                                        ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php _e('Products with Tag names', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                                    <td>
                                        <textarea class="widefat woocommerce-products-carousel-all-in-one-field field-validate" name="tags" id="tags" pattern="^[a-zA-Z](,?[a-zA-Z])*$"></textarea>
                                        <br />
                                        <?php echo WooCommerce_Products_Carousel_All_In_One_Utils::getTooltip( __('Please enter Tag names with comma seperated.', 'woocommerce-products-carousel-all-in-one') ); ?>
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Ordering', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                    <td>
                        <select class="select woocommerce-products-carousel-all-in-one-field" name="ordering" id="ordering">
                        <?php
                            $ordering_list = WooCommerce_Products_Carousel_All_In_One_Utils::getOrderings();
                            foreach($ordering_list as $key => $list) {
                                echo '<option value="'. $key .'">'. $list .'</option>';
                            }
                        ?>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>

        <br />
        <table cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th colspan="2">
                        <h2><?php _e('Display options', 'woocommerce-products-carousel-all-in-one') ?></h2>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th><?php _e('Template', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                    <td>
                        <select class="select woocommerce-products-carousel-all-in-one-field" name="template" id="template">
                        <?php
                            $files_list = WooCommerce_Products_Carousel_All_In_One_Utils::getTemplates();
                            foreach($files_list as $filename) {
                                echo '<option value="'. $filename .'">'. $filename .'</option>';
                            }
                        ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Show title', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                    <td>
                        <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" value="1" name="show_title" id="show_title" checked="checked" />
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Show description', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                    <td>
                        <select class="select woocommerce-products-carousel-all-in-one-field" name="show_description" id="show_description">
                        <?php
                            $description_list = WooCommerce_Products_Carousel_All_In_One_Utils::getDescriptions();
                            foreach($description_list as $key => $list) {
                                echo '<option value="'. $key .'">'. $list .'</option>';
                            }
                        ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Allow shortcodes in full content', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                    <td>
                        <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" value="1" name="allow_shortcodes" id="allow_shortcodes" />
                    </td>
                </tr>
               <tr>
                    <th><?php _e('Show price', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                    <td>
                        <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" value="1" name="show_price" id="show_price" checked="checked" />
                    </td>
                </tr>
               <tr>
                    <th><?php _e('Show category', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                    <td>
                        <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" value="1" name="show_category" id="show_category" checked="checked" />
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Show tags', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                    <td>
                        <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" value="1" name="show_tags" id="show_tags" />
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Show add to cart button', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                    <td>
                        <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" value="1" name="show_add_to_cart_button" id="show_add_to_cart_button" checked="checked" />
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Show more button', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                    <td>
                        <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" value="1" name="show_more_button" id="show_more_button" checked="checked" />
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Show more items button', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                    <td>
                        <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" value="1" name="show_more_items_button" id="show_more_items_button" checked="checked" />
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Show featured image', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                    <td>
                        <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" value="1" name="show_featured_image" id="show_featured_image" checked="checked" />
                    </td>
                </tr>
                <tr>
                    <th><?php echo _e('Image source', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                    <td>
                        <select class="select woocommerce-products-carousel-all-in-one-field" name="image_source" id="image_source">
                        <?php
                            $source_list = WooCommerce_Products_Carousel_All_In_One_Utils::getSources();
                            foreach($source_list as $key => $list) {
                                echo '<option value="'. $key .'">'. $list .'</option>';
                            }
                        ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Image height', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                    <td>
                        <input class="woocommerce-products-carousel-all-in-one-field field-validate" type="text" name="image_height" id="image_height" value="100" size="5" required pattern="^[0-9](\.?[0-9])*$" />%
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Image width', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                    <td>
                        <input class="woocommerce-products-carousel-all-in-one-field field-validate" type="text" name="image_width" id="image_width" value="100" size="5" required pattern="^[0-9](\.?[0-9])*$" />%
                    </td>
                </tr>
            </tbody>
        </table>

        <br />
        <table cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th colspan="2">
                        <h2><?php _e('Carousel options', 'woocommerce-products-carousel-all-in-one') ?></h2>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2">
                        <fieldset>
                            <legend>
                                <?php _e('Items to show', 'woocommerce-products-carousel-all-in-one') ?>
                                <?php echo WooCommerce_Products_Carousel_All_In_One_Utils::getTooltip( __("If you need to create some other breakpoints suits to your website, you can define custombreakpoints on plugin's settings page.", 'woocommerce-products-carousel-all-in-one') ); ?>
                            </legend>

                            <table cellspacing="5" cellpadding="5">
                                <tr>
                                    <th><?php _e('on mobiles', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                                    <td>
                                        <input class="woocommerce-products-carousel-all-in-one-field field-validate" type="number" name="items_to_show_mobiles" id="items_to_show_mobiles" value="1" size="5" min="1" required pattern="^\d+$" />
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php _e('on tablets', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                                    <td>
                                        <input class="woocommerce-products-carousel-all-in-one-field field-validate" type="number" name="items_to_show_tablets" id="items_to_show_tablets" value="2" size="5" min="1" required pattern="^\d+$" />
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php _e('on laptops and desktops', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                                    <td>
                                        <input class="woocommerce-products-carousel-all-in-one-field field-validate" type="number" name="items_to_show" id="items_to_show" value="4" size="5" min="1" required pattern="^\d+$" />
                                    </td>
                                </tr>

                                <?php $plugin_options = get_option( 'woocommerce-products-carousel-all-in-one_options' ); ?>
                                <?php if ( $plugin_options && array_key_exists('custom_breakpoints', $plugin_options) ): ?>
                                    <?php $breakpoints = explode(',', $plugin_options['custom_breakpoints']); ?>
                                    <?php if( count($breakpoints) > 0 ): ?>
                                    <tr>
                                        <th colspan="2">
                                            <hr />
                                            <?php _e('Custom breakpoints for RWD', 'woocommerce-products-carousel-all-in-one'); ?>:
                                            <?php echo WooCommerce_Products_Carousel_All_In_One_Utils::getTooltip( __("Custom breakpoint are defined on plugin's settings page.", 'woocommerce-products-carousel-all-in-one') ); ?>
                                        </th>
                                    </tr>
                                        <?php sort($breakpoints); ?>
                                        <?php foreach ($breakpoints as $width): ?>
                                            <tr>
                                                <th><?php echo $width . 'px' ?>:</th>
                                                <td>
                                                    <input class="woocommerce-products-carousel-all-in-one-field woocommerce-products-carousel-all-in-one-custom-breakpoint field-validate" size="5" id="custom_breakpoints_<?php echo $width ?>" name="custom_breakpoints[<?php echo $width ?>]" type="number" value="" min="1" required pattern="^\d+$" />
                                                </td>
                                            </tr>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                <?php endif ?>
                            </table>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Slide by', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                    <td>
                        <input class="woocommerce-products-carousel-all-in-one-field field-validate" type="number" name="slide_by" id="slide_by" value="1" size="5" min="1" required pattern="^\d+$" />
                        <?php echo WooCommerce_Products_Carousel_All_In_One_Utils::getTooltip( __('Number of elements to slide.', 'woocommerce-products-carousel-all-in-one') ); ?>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Stage padding', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                    <td>
                        <input class="woocommerce-products-carousel-all-in-one-field field-validate" type="number" name="stage_padding" id="stage_padding" value="0" size="5" min="0" required pattern="^\d+$" />[px]
                        <?php echo WooCommerce_Products_Carousel_All_In_One_Utils::getTooltip( __('Padding left and right on stage (can see prev and next products).', 'woocommerce-products-carousel-all-in-one') ); ?>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Margin', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                    <td>
                        <input class="woocommerce-products-carousel-all-in-one-field field-validate" type="number" name="margin" id="margin" value="5" size="5" min="0" required pattern="^\d+$" />[px]
                        <?php echo WooCommerce_Products_Carousel_All_In_One_Utils::getTooltip( __('Margin between items.', 'woocommerce-products-carousel-all-in-one') ); ?>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Inifnity loop', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                    <td>
                        <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" value="1" name="loop" id="loop" checked="checked" />
                        <?php echo WooCommerce_Products_Carousel_All_In_One_Utils::getTooltip( __('Duplicate last and first items to get loop illusion.', 'woocommerce-products-carousel-all-in-one') ); ?>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Pause on mouse hover', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                    <td>
                        <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" value="1" name="stop_on_hover" id="stop_on_hover" checked="checked" />
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Auto play', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                    <td>
                        <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" value="1" name="auto_play" id="auto_play" checked="checked" />
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Autoplay interval timeout', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                    <td>
                        <input class="woocommerce-products-carousel-all-in-one-field field-validate" type="number" name="auto_play_timeout" id="auto_play_timeout" value="1200" size="5" min="1" required pattern="^\d+$" />[ms]
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Autoplay speed', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                    <td>
                        <input class="woocommerce-products-carousel-all-in-one-field field-validate" type="number" name="auto_play_speed" id="auto_play_speed" value="800" size="5" min="1" required pattern="^\d+$" />[ms]
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Show "next" and "prev" buttons', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                    <td>
                        <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" value="1" name="nav" id="nav" checked="checked" />
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Navigation speed', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                    <td>
                        <input class="woocommerce-products-carousel-all-in-one-field field-validate" type="number" name="nav_speed" id="nav_speed" value="800" size="5" min="1" required pattern="^\d+$" />[ms]
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Show dots navigation', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                    <td>
                        <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" value="1" name="dots" id="dots" checked="checked" />
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Dots speed', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                    <td>
                        <input class="woocommerce-products-carousel-all-in-one-field field-validate" type="number" name="dots_speed" id="dots_speed" value="800" size="5" min="1" required pattern="^\d+$" />[ms]
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Delays loading of images', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                    <td>
                        <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" value="1" name="lazy_load" id="lazy_load" />
                        <?php echo WooCommerce_Products_Carousel_All_In_One_Utils::getTooltip( __("Images outside of viewport won't be loaded before user scrolls to them. Great for mobile devices to speed up page loadings.", 'woocommerce-products-carousel-all-in-one') ); ?>
                        <?php echo WooCommerce_Products_Carousel_All_In_One_Utils::getTooltip( __('With some themes or servers it might cause problems. If you have problem with your carousel (e.g: one of the images did not show, etc.), please uncheck this option.', 'woocommerce-products-carousel-all-in-one'), 'warning' ); ?>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Mouse events', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                    <td>
                        <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" value="1" name="mouse_drag" id="mouse_drag" checked="checked" />
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Mousewheel scrolling', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                    <td>
                        <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" value="1" name="mouse_wheel" id="mouse_wheel" checked="checked" />
                        <?php echo WooCommerce_Products_Carousel_All_In_One_Utils::getTooltip( __("With some themes or servers it might cause problems. If you have problem with your carousel or site (e.g: you can't scroll the page, etc.), please uncheck this option.", 'woocommerce-products-carousel-all-in-one'), 'warning' ); ?>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Touch events', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                    <td>
                        <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" value="1" name="touch_drag" id="touch_drag" checked="checked" />
                    </td>
                </tr>
                <tr>
                    <th><?php echo _e('Animation', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                    <td>
                        <select class="select woocommerce-products-carousel-all-in-one-field" name="easing" id="easing">
                        <?php
                            $animation_list = WooCommerce_Products_Carousel_All_In_One_Utils::getAnimations();
                            foreach($animation_list as $key => $list) {
                                  echo '<option value="'. $key .'">'. $list .'</option>';
                            }
                        ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Auto width', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                    <td>
                        <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" value="1" name="auto_width" id="auto_width" />
                        <?php echo WooCommerce_Products_Carousel_All_In_One_Utils::getTooltip( __("It's recommended to set width on divs, but sometimes you may want this option :)", 'woocommerce-products-carousel-all-in-one') ); ?>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Auto height', 'woocommerce-products-carousel-all-in-one'); ?>:</th>
                    <td>
                        <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" value="1" name="auto_height" id="auto_height" checked="checked" />
                        <?php echo WooCommerce_Products_Carousel_All_In_One_Utils::getTooltip( __('Height adjusted dynamically to highest displayed item.', 'woocommerce-products-carousel-all-in-one') ); ?>
                        <?php echo WooCommerce_Products_Carousel_All_In_One_Utils::getTooltip( __('With some themes or servers it might cause problems. If you have problem with your carousel (e.g: one of the images did not show, etc.), please uncheck this option.', 'woocommerce-products-carousel-all-in-one'), 'warning' ); ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="woocommerce-products-carousel-all-in-one-buttons">
        <input type="button" class="button button-primary button-large" value="<?php _e('Insert Shortcode', 'woocommerce-products-carousel-all-in-one') ?>" onClick="insert_shortcode();">
    </div>
</div>
