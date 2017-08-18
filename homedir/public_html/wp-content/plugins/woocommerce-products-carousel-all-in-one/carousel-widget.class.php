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

/*
 * widget
 */
class WooCommerceProductsCarouselAllInOneWidget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'woocommerce_products_carousel_all_in_one',
            __('WooCommerce Products Carousel all in one', 'woocommerce-products-carousel-all-in-one'),
            array(
                'classname'  => 'widget_woocommerce_products_carousel_all_in_one',
                'description' =>  __('Show new, featured or popular products in Owl Carousel', 'woocommerce-products-carousel-all-in-one')
            )
        );
    }

    public function widget( $args, $instance ) {
        $title = apply_filters('widget_title', $instance['title']);

        echo $args['before_widget'];

        if ($title) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        $generator = new WooCommerceProductsCarouselAllInOneGenerator();
        echo $generator->generate($instance);

        echo $args['after_widget'];
    }

    public function update ( $new_instance, $old_instance ) {
        $instance = $new_instance;
        $instance['custom_breakpoints'] = '';

        if ( array_key_exists('custom_breakpoints', $new_instance) ) {
            if ( !empty($new_instance['custom_breakpoints']) ) {
                $instance['custom_breakpoints'] = serialize($new_instance['custom_breakpoints']);
            }
        }
        return $new_instance;
    }

/**
 * the configuration form.
 */
public function form($instance) {
    /*
     * load defaults if new
     */
    if(empty($instance)) {
        $generator = new WooCommerceProductsCarouselAllInOneGenerator();
        $instance = $generator->getDefaults();
    }

?>
    <div class="woocommerce-products-carousel-all-in-one-form">
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title'); ?>:</label>
            <input class="widefat woocommerce-products-carousel-all-in-one-field" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr(array_key_exists('title', $instance) ? $instance['title'] : ''); ?>" />
        </p>

        <p>
            <h2><?php _e('Basic options', 'woocommerce-products-carousel-all-in-one') ?></h2>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('all_items'); ?>"><?php _e('Products limit', 'woocommerce-products-carousel-all-in-one'); ?>:</label>
            <br />
            <input class="woocommerce-products-carousel-all-in-one-field field-validate" size="5" id="<?php echo $this->get_field_id('all_items'); ?>" name="<?php echo $this->get_field_name('all_items'); ?>" type="number" value="<?php echo esc_attr($instance['all_items']); ?>" required min="1" pattern="^\d+$" />
        </p>

        <div>
            <fieldset>
                <legend><strong><?php _e('Product Selection Criteria', 'woocommerce-products-carousel-all-in-one') ?></strong></legend>
                <p>
                    <label for="<?php echo $this->get_field_id('show_only'); ?>"><?php _e('Show Products', 'woocommerce-products-carousel-all-in-one'); ?>:</label>
                    <br />
                    <select class="select woocommerce-products-carousel-all-in-one-field" name="<?php echo $this->get_field_name('show_only'); ?>" id="<?php echo $this->get_field_id('show_only'); ?>">
                    <?php
                        $show_list = WooCommerce_Products_Carousel_All_In_One_Utils::getShows();
                        foreach($show_list as $key => $list) {
                            echo '<option value="'. esc_attr($key) .'" '. (esc_attr($instance['show_only']) == esc_attr($key) ? 'selected="selected"' : null) .'>'. esc_attr($list) .'</option>';
                        }
                    ?>
                    </select>
                </p>
                <p>
                    <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" id="<?php echo $this->get_field_id('out_of_stock'); ?>" name="<?php echo $this->get_field_name('out_of_stock'); ?>" <?php array_key_exists('out_of_stock', $instance) ? checked( (bool) $instance['out_of_stock'], false ): null; ?> value="1" />
                    <label for="<?php echo $this->get_field_id('out_of_stock'); ?>"><?php _e('Include out of stock', 'woocommerce-products-carousel-all-in-one'); ?></label>
                </p>
                <p>
                    <?php _e('but', 'woocommerce-products-carousel-all-in-one') ?>
                </p>
                <p>
                    <label for="<?php echo $this->get_field_id('exclude'); ?>"><?php _e('Exclude Products with IDs', 'woocommerce-products-carousel-all-in-one'); ?>:</label>
                    <?php echo WooCommerce_Products_Carousel_All_In_One_Utils::getTooltip( __('Please enter Product IDs with comma seperated.', 'woocommerce-products-carousel-all-in-one') );?>
                    <textarea class="widefat woocommerce-products-carousel-all-in-one-field field-validate" id="<?php echo $this->get_field_id('exclude'); ?>" name="<?php echo $this->get_field_name('exclude'); ?>" pattern="^[1-9](0*)(,?[1-9](0*))*$"><?php echo ( array_key_exists('exclude', $instance) ? esc_attr($instance['exclude']) : null ); ?></textarea>
                </p>
                <p class="woocommerce-products-carousel-all-in-one-separator woocommerce-products-carousel-all-in-one-inclusion">
                    <?php _e('or', 'woocommerce-products-carousel-all-in-one') ?>
                </p>
                <p>
                    <label for="<?php echo $this->get_field_id('products'); ?>"><?php _e('Show Products with IDs', 'woocommerce-products-carousel-all-in-one'); ?>:</label>
                    <?php echo WooCommerce_Products_Carousel_All_In_One_Utils::getTooltip( __('Please enter Product IDs with comma seperated.', 'woocommerce-products-carousel-all-in-one') );?>
                    <textarea class="widefat woocommerce-products-carousel-all-in-one-field field-validate" id="<?php echo $this->get_field_id('products'); ?>" name="<?php echo $this->get_field_name('products'); ?>" pattern="^[1-9](0*)(,?[1-9](0*))*$"><?php echo ( array_key_exists('products', $instance) ? esc_attr($instance['products']) : null ); ?></textarea>
                </p>
                <p class="woocommerce-products-carousel-all-in-one-separator woocommerce-products-carousel-all-in-one-inclusion">
                    <?php _e('also include', 'woocommerce-products-carousel-all-in-one') ?>
                </p>
                <p>
                    <label for="<?php echo $this->get_field_id('categories'); ?>"><?php _e('Products with Category IDs', 'woocommerce-products-carousel-all-in-one'); ?>:</label>
                    <?php echo WooCommerce_Products_Carousel_All_In_One_Utils::getTooltip( __('Please enter Category IDs with comma seperated.', 'woocommerce-products-carousel-all-in-one') );?>
                    <textarea class="widefat woocommerce-products-carousel-all-in-one-field field-validate" id="<?php echo $this->get_field_id('categories'); ?>" name="<?php echo $this->get_field_name('categories'); ?>" pattern="^[1-9](0*)(,?[1-9](0*))*$"><?php echo ( array_key_exists('categories', $instance) ? esc_attr($instance['categories']) : null ); ?></textarea>
                </p>
                <p >
                    <label for="<?php echo $this->get_field_id('relation'); ?>"><?php _e('And/Or', 'woocommerce-products-carousel-all-in-one'); ?></label>
                    <select class="select woocommerce-products-carousel-all-in-one-field" name="<?php echo $this->get_field_name('relation'); ?>" id="<?php echo $this->get_field_id('relation'); ?>">
                    <?php
                        $relation_list = WooCommerce_Products_Carousel_All_In_One_Utils::getRelations();
                        foreach($relation_list as $key => $list) {
                            echo '<option value="'. esc_attr($key) .'" '. (esc_attr($instance['relation']) == esc_attr($key) ? 'selected="selected"' : null) .'>'. esc_attr($list) .'</option>';
                        }
                    ?>
                    </select>
                </p>
                <p>
                    <label for="<?php echo $this->get_field_id('tags'); ?>"><?php _e('Products with Tag names', 'woocommerce-products-carousel-all-in-one'); ?>:</label>
                    <?php echo WooCommerce_Products_Carousel_All_In_One_Utils::getTooltip( __('Please enter Tag names with comma seperated.', 'woocommerce-products-carousel-all-in-one') ); ?>
                    <textarea class="widefat woocommerce-products-carousel-all-in-one-field field-validate" id="<?php echo $this->get_field_id('tags'); ?>" name="<?php echo $this->get_field_name('tags'); ?>" pattern="^[a-zA-z](,?[a-zA-Z])*$"><?php echo ( array_key_exists('tags', $instance) ? esc_attr($instance['tags']) : null ); ?></textarea>
                </p>
            </fieldset>
        </div>

        <p>
            <label for="<?php echo $this->get_field_id('ordering'); ?>"><?php _e('Ordering', 'woocommerce-products-carousel-all-in-one'); ?>:</label>
            <br />
            <select class="select woocommerce-products-carousel-all-in-one-field" name="<?php echo $this->get_field_name('ordering'); ?>" id="<?php echo $this->get_field_id("ordering"); ?>">
            <?php
                $ordering_list = WooCommerce_Products_Carousel_All_In_One_Utils::getOrderings();
                foreach($ordering_list as $key => $list) {
                    echo '<option value="'. esc_attr($key) .'" '. (esc_attr($instance['ordering']) == esc_attr($key) ? 'selected="selected"' : null) .'>'. esc_attr($list) .'</option>';
                }
            ?>
            </select>
        </p>



        <p>
            <h2><?php _e('Display options', 'woocommerce-products-carousel-all-in-one') ?></h2>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('template'); ?>"><?php _e('Template', 'woocommerce-products-carousel-all-in-one'); ?>:</label>
            <br />
            <select class="select woocommerce-products-carousel-all-in-one-field" name="<?php echo $this->get_field_name('template'); ?>" id="<?php echo $this->get_field_id('template'); ?>">
            <?php
            $files_list = WooCommerce_Products_Carousel_All_In_One_Utils::getTemplates();
                foreach($files_list as $list) {
                    echo '<option value="'. esc_attr($list) .'" '. (esc_attr($instance['template']) == esc_attr($list) ? 'selected="selected"' : null) .'>'. esc_attr($list) .'</option>';
                }
            ?>
            </select>
        </p>
        <p>
            <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" id="<?php echo $this->get_field_id('show_title'); ?>" name="<?php echo $this->get_field_name('show_title'); ?>" <?php array_key_exists('show_title', $instance) ? checked( (bool) $instance['show_title'], true ): null; ?> value="1" />
            <label for="<?php echo $this->get_field_id('show_title'); ?>"><?php _e('Show title', 'woocommerce-products-carousel-all-in-one'); ?></label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('show_description'); ?>"><?php _e('Show description', 'woocommerce-products-carousel-all-in-one'); ?>:</label>
            <br />
            <select class="select woocommerce-products-carousel-all-in-one-field" name="<?php echo $this->get_field_name('show_description'); ?>" id="<?php echo $this->get_field_id('show_description'); ?>">
            <?php
                $description_list = WooCommerce_Products_Carousel_All_In_One_Utils::getDescriptions();
                foreach($description_list as $key => $list) {
                    echo '<option value="'. esc_attr($key) .'" '. (esc_attr($instance['show_description']) == esc_attr($key) ? 'selected="selected"' : null) .'>'. $list .'</option>';
                }
            ?>
            </select>
        </p>
        <p>
            <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" id="<?php echo $this->get_field_id('allow_shortcodes'); ?>" name="<?php echo $this->get_field_name('allow_shortcodes'); ?>" <?php array_key_exists('allow_shortcodes', $instance) ? checked( (bool) $instance['allow_shortcodes'], false ): null; ?> value="1" />
            <label for="<?php echo $this->get_field_id("allow_shortcodes"); ?>"><?php _e("Allow shortcodes in full content", "woocommerce-products-carousel-all-in-one"); ?></label>
        </p>
        <p>
            <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" id="<?php echo $this->get_field_id('show_price'); ?>" name="<?php echo $this->get_field_name('show_price'); ?>" <?php array_key_exists('show_price', $instance) ? checked( (bool) $instance['show_price'], true ): null; ?> value="1" />
            <label for="<?php echo $this->get_field_id('show_price'); ?>"><?php _e('Show price', 'woocommerce-products-carousel-all-in-one'); ?></label>
        </p>
        <p>
            <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" id="<?php echo $this->get_field_id('show_category'); ?>" name="<?php echo $this->get_field_name('show_category'); ?>" <?php array_key_exists('show_category', $instance) ? checked( (bool) $instance['show_category'], true ): null; ?> value="1" />
            <label for="<?php echo $this->get_field_id('show_category'); ?>"><?php _e('Show category', 'woocommerce-products-carousel-all-in-one'); ?></label>
        </p>
        <p>
            <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" id="<?php echo $this->get_field_id('show_tags'); ?>" name="<?php echo $this->get_field_name('show_tags'); ?>" <?php array_key_exists('show_tags', $instance) ? checked( (bool) $instance['show_tags'], false ): null; ?> value="1" />
            <label for="<?php echo $this->get_field_id('show_tags'); ?>"><?php _e('Show tags', 'woocommerce-products-carousel-all-in-one'); ?></label>
        </p>
        <p>
            <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" id="<?php echo $this->get_field_id('show_add_to_cart_button'); ?>" name="<?php echo $this->get_field_name('show_add_to_cart_button'); ?>" <?php array_key_exists('show_add_to_cart_button', $instance) ? checked( (bool) $instance['show_add_to_cart_button'], true ): null; ?> value="1" />
            <label for="<?php echo $this->get_field_id('show_add_to_cart_button'); ?>"><?php _e('Show add to cart button', 'woocommerce-products-carousel-all-in-one'); ?></label>
        </p>
        <p>
            <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" id="<?php echo $this->get_field_id('show_more_button'); ?>" name="<?php echo $this->get_field_name('show_more_button'); ?>" <?php array_key_exists('show_more_button', $instance) ? checked( (bool) $instance['show_more_button'], true ): null; ?> value="1" />
            <label for="<?php echo $this->get_field_id('show_more_button'); ?>"><?php _e('Show more button', 'woocommerce-products-carousel-all-in-one'); ?></label>
        </p>
        <p>
            <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" id="<?php echo $this->get_field_id('show_more_items_button'); ?>" name="<?php echo $this->get_field_name('show_more_items_button'); ?>" <?php array_key_exists('show_more_items_button', $instance) ? checked( (bool) $instance['show_more_items_button'], true ): null; ?> value="1" />
            <label for="<?php echo $this->get_field_id('show_more_items_button'); ?>"><?php _e('Show more items button', 'woocommerce-products-carousel-all-in-one'); ?></label>
        </p>
        <p>
            <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" id="<?php echo $this->get_field_id('show_featured_image'); ?>" name="<?php echo $this->get_field_name('show_featured_image'); ?>" <?php array_key_exists('show_featured_image', $instance) ? checked( (bool) $instance['show_featured_image'], true ): null; ?> value="1" />
            <label for="<?php echo $this->get_field_id('show_featured_image'); ?>"><?php _e('Show featured image', 'woocommerce-products-carousel-all-in-one'); ?></label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('image_source'); ?>"><?php echo _e('Image source', 'woocommerce-products-carousel-all-in-one'); ?>:</label>
            <br />
            <select class="select woocommerce-products-carousel-all-in-one-field" name="<?php echo $this->get_field_name('image_source'); ?>" id="<?php echo $this->get_field_id('image_source'); ?>">
            <?php
                $source_list = WooCommerce_Products_Carousel_All_In_One_Utils::getSources();
                foreach($source_list as $key => $list) {
                    echo '<option value="'. esc_attr($key) .'" '. (esc_attr($instance['image_source']) == esc_attr($key) ? 'selected="selected"' : null) .'>'. esc_attr($list) .'</option>';
                }
            ?>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('image_height'); ?>"><?php _e('Image height', 'woocommerce-products-carousel-all-in-one'); ?>:</label>
            <br />
            <input class="woocommerce-products-carousel-all-in-one-field field-validate" size="10" id="<?php echo $this->get_field_id('image_height'); ?>" name="<?php echo $this->get_field_name('image_height'); ?>" type="text" value="<?php echo esc_attr($instance['image_height']); ?>" required pattern="^[0-9](\.?[0-9])*$" />%
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('image_width'); ?>"><?php _e('Image width', 'woocommerce-products-carousel-all-in-one'); ?>:</label>
            <br />
            <input class="woocommerce-products-carousel-all-in-one-field field-validate" size="10" id="<?php echo $this->get_field_id('image_width'); ?>" name="<?php echo $this->get_field_name('image_width'); ?>" type="text" value="<?php echo esc_attr($instance['image_width']); ?>" required pattern="^[0-9](\.?[0-9])*$" />%
        </p>


        <p>
            <h2><?php _e('Carousel options', 'woocommerce-products-carousel-all-in-one') ?></h2>
        </p>
        <div>
            <fieldset>
                <legend>
                    <strong><?php _e('Items to show', 'woocommerce-products-carousel-all-in-one') ?>:</strong>
                    <?php echo WooCommerce_Products_Carousel_All_In_One_Utils::getTooltip( __("If you need to create some other breakpoints suits to your website, you can define custombreakpoints on plugin's settings page.", 'woocommerce-products-carousel-all-in-one') ); ?>
                </legend>

                <p>
                    <label for="<?php echo $this->get_field_id('items_to_show_mobiles'); ?>"><?php _e('on mobiles', 'woocommerce-products-carousel-all-in-one'); ?>:</label>
                    <br />
                    <input class="woocommerce-products-carousel-all-in-one-field field-validate" size="5" id="<?php echo $this->get_field_id('items_to_show_mobiles'); ?>" name="<?php echo $this->get_field_name('items_to_show_mobiles'); ?>" type="number" value="<?php echo esc_attr($instance['items_to_show_mobiles']); ?>" min="1" required pattern="^\d+$" />
                </p>
                <p>
                    <label for="<?php echo $this->get_field_id('items_to_show_tablets'); ?>"><?php _e('on tablets', 'woocommerce-products-carousel-all-in-one'); ?>:</label>
                    <br />
                    <input class="woocommerce-products-carousel-all-in-one-field field-validate" size="5" id="<?php echo $this->get_field_id('items_to_show_tablets'); ?>" name="<?php echo $this->get_field_name('items_to_show_tablets'); ?>" type="number" value="<?php echo esc_attr($instance['items_to_show_tablets']); ?>" min="1" required pattern="^\d+$" />
                </p>
                <p>
                    <label for="<?php echo $this->get_field_id('items_to_show'); ?>"><?php _e('on laptops and desktops', 'woocommerce-products-carousel-all-in-one'); ?>:</label>
                    <br />
                    <input class="woocommerce-products-carousel-all-in-one-field field-validate" size="5" id="<?php echo $this->get_field_id('items_to_show'); ?>" name="<?php echo $this->get_field_name('items_to_show'); ?>" type="number" value="<?php echo esc_attr($instance['items_to_show']); ?>" min="1" required pattern="^\d+$" />
                </p>
            </fieldset>
        </div>
        <p>
            <label for="<?php echo $this->get_field_id('slide_by'); ?>"><?php _e('Slide by', 'woocommerce-products-carousel-all-in-one'); ?>:</label>
            <?php echo WooCommerce_Products_Carousel_All_In_One_Utils::getTooltip( __('Number of elements to slide.', 'woocommerce-products-carousel-all-in-one') ); ?>
            <br />
            <input class="woocommerce-products-carousel-all-in-one-field field-validate" size="5" id="<?php echo $this->get_field_id('slide_by'); ?>" name="<?php echo $this->get_field_name('slide_by'); ?>" type="number" value="<?php echo esc_attr($instance['slide_by']); ?>" min="1" required pattern="^\d+$" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('stage_padding'); ?>"><?php _e('Stage padding', 'woocommerce-products-carousel-all-in-one'); ?>:</label>
            <?php echo WooCommerce_Products_Carousel_All_In_One_Utils::getTooltip( __('Padding left and right on stage (can see prev and next products).', 'woocommerce-products-carousel-all-in-one') ); ?>
            <br />
            <input class="woocommerce-products-carousel-all-in-one-field field-validate" size="5" id="<?php echo $this->get_field_id('stage_padding'); ?>" name="<?php echo $this->get_field_name('stage_padding'); ?>" type="number" value="<?php echo esc_attr($instance['stage_padding']); ?>" min="0" required pattern="^\d+$" />[px]
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('margin'); ?>"><?php _e('Margin', 'woocommerce-products-carousel-all-in-one'); ?>:</label>
            <?php echo WooCommerce_Products_Carousel_All_In_One_Utils::getTooltip( __('Margin between items.', 'woocommerce-products-carousel-all-in-one') ); ?>
            <br />
            <input class="woocommerce-products-carousel-all-in-one-field field-validate" size="5" id="<?php echo $this->get_field_id('margin'); ?>" name="<?php echo $this->get_field_name('margin'); ?>" type="number" value="<?php echo esc_attr($instance['margin']); ?>" min="0" required pattern="^\d+$" />[px]
        </p>
        <p>
            <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" id="<?php echo $this->get_field_id('loop'); ?>" name="<?php echo $this->get_field_name('loop'); ?>" <?php array_key_exists('loop', $instance) ? checked( (bool) $instance['loop'], true ): null; ?> value="1" />
            <label for="<?php echo $this->get_field_id('loop'); ?>"><?php _e('Inifnity loop', 'woocommerce-products-carousel-all-in-one'); ?></label>
            <?php echo WooCommerce_Products_Carousel_All_In_One_Utils::getTooltip( __('Duplicate last and first items to get loop illusion.', 'woocommerce-products-carousel-all-in-one') ); ?>
        </p>
        <p>
            <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" id="<?php echo $this->get_field_id('stop_on_hover'); ?>" name="<?php echo $this->get_field_name('stop_on_hover'); ?>" <?php array_key_exists('stop_on_hover', $instance) ? checked( (bool) $instance['stop_on_hover'], true ): null; ?> value="1" />
            <label for="<?php echo $this->get_field_id('stop_on_hover'); ?>"><?php _e('Pause on mouse hover', 'woocommerce-products-carousel-all-in-one'); ?></label>
        </p>
        <p>
            <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" id="<?php echo $this->get_field_id('auto_play'); ?>" name="<?php echo $this->get_field_name('auto_play'); ?>" <?php array_key_exists('auto_play', $instance) ? checked( (bool) $instance['auto_play'], true ): null; ?> value="1" />
            <label for="<?php echo $this->get_field_id("auto_play"); ?>"><?php _e('Auto play', 'woocommerce-products-carousel-all-in-one'); ?></label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('auto_play_timeout'); ?>"><?php _e('Autoplay interval timeout', 'woocommerce-products-carousel-all-in-one'); ?>:</label>
            <br />
            <input class="woocommerce-products-carousel-all-in-one-field field-validate" size="5" id="<?php echo $this->get_field_id('auto_play_timeout'); ?>" name="<?php echo $this->get_field_name('auto_play_timeout'); ?>" type="number" value="<?php echo esc_attr($instance['auto_play_timeout']); ?>" min="1" required pattern="^\d+$" />[ms]
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('auto_play_speed'); ?>"><?php _e('Autoplay speed', 'woocommerce-products-carousel-all-in-one'); ?>:</label>
            <br />
            <input class="woocommerce-products-carousel-all-in-one-field field-validate" size="5" id="<?php echo $this->get_field_id('auto_play_speed'); ?>" name="<?php echo $this->get_field_name('auto_play_speed'); ?>" type="number" value="<?php echo esc_attr($instance['auto_play_speed']); ?>" min="1" required pattern="^\d+$" />[ms]
        </p>
        <p>
            <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" id="<?php echo $this->get_field_id('nav'); ?>" name="<?php echo $this->get_field_name('nav'); ?>" <?php array_key_exists('nav', $instance) ? checked( (bool) $instance['nav'], true ): null; ?> value="1"/>
            <label for="<?php echo $this->get_field_id('nav'); ?>"><?php _e('show "next" and "prev" buttons', 'woocommerce-products-carousel-all-in-one'); ?></label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('nav_speed'); ?>"><?php _e('Navigation speed', 'woocommerce-products-carousel-all-in-one'); ?>:</label>
            <br />
            <input class="woocommerce-products-carousel-all-in-one-field field-validate" size="5" id="<?php echo $this->get_field_id('nav_speed'); ?>" name="<?php echo $this->get_field_name('nav_speed'); ?>" type="number" value="<?php echo esc_attr($instance['nav_speed']); ?>" min="1" required pattern="^\d+$" />[ms]
        </p>
        <p>
            <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" id="<?php echo $this->get_field_id('dots'); ?>" name="<?php echo $this->get_field_name('dots'); ?>" <?php array_key_exists('dots', $instance) ? checked( (bool) $instance['dots'], true ): null; ?> value="1" />
            <label for="<?php echo $this->get_field_id('dots'); ?>"><?php _e('Show dots navigation', 'woocommerce-products-carousel-all-in-one'); ?></label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('dots_speed'); ?>"><?php _e('Dots speed', 'woocommerce-products-carousel-all-in-one'); ?>:</label>
            <br />
            <input class="woocommerce-products-carousel-all-in-one-field field-validate" size="5" id="<?php echo $this->get_field_id('dots_speed'); ?>" name="<?php echo $this->get_field_name('dots_speed'); ?>" type="number" value="<?php echo esc_attr($instance['dots_speed']); ?>" min="1" required pattern="^\d+$" />[ms]
        </p>
        <p>
            <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" id="<?php echo $this->get_field_id('lazy_load'); ?>" name="<?php echo $this->get_field_name('lazy_load'); ?>" <?php array_key_exists('lazy_load', $instance) ? checked( (bool) $instance['lazy_load'], false ): null; ?> value="1" />
            <label for="<?php echo $this->get_field_id('lazy_load'); ?>"><?php _e('Delays loading of images', 'woocommerce-products-carousel-all-in-one'); ?></label>
            <?php echo WooCommerce_Products_Carousel_All_In_One_Utils::getTooltip( __('Images outside of viewport won\'t be loaded before user scrolls to them. Great for mobile devices to speed up page loadings.', 'woocommerce-products-carousel-all-in-one') ); ?>
            <?php echo WooCommerce_Products_Carousel_All_In_One_Utils::getTooltip( __('With some themes or servers it might cause problems. If you have problem with your carousel (e.g: one of the images did not show, etc.), please uncheck this option.', 'woocommerce-products-carousel-all-in-one'), 'warning' ); ?>
        </p>
        <p>
            <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" id="<?php echo $this->get_field_id('mouse_drag'); ?>" name="<?php echo $this->get_field_name('mouse_drag'); ?>" <?php array_key_exists('mouse_drag', $instance) ? checked( (bool) $instance['mouse_drag'], true ): null; ?> value="1" />
            <label for="<?php echo $this->get_field_id('mouse_drag'); ?>"><?php _e('Mouse events', 'woocommerce-products-carousel-all-in-one'); ?></label>
        </p>
        <p>
            <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" id="<?php echo $this->get_field_id('mouse_wheel'); ?>" name="<?php echo $this->get_field_name('mouse_wheel'); ?>" <?php array_key_exists('mouse_wheel', $instance) ? checked( (bool) $instance['mouse_wheel'], true ): null; ?> value="1" />
            <label for="<?php echo $this->get_field_id('mouse_wheel'); ?>"><?php _e('Mousewheel scrolling', 'woocommerce-products-carousel-all-in-one'); ?></label>
            <?php echo WooCommerce_Products_Carousel_All_In_One_Utils::getTooltip( __('With some themes or servers it might cause problems. If you have problem with your carousel or site (e.g: you can\'t scroll the page, etc.), please uncheck this option.', 'woocommerce-products-carousel-all-in-one'), 'warning' ); ?>
        </p>
        <p>
            <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" id="<?php echo $this->get_field_id('touch_drag'); ?>" name="<?php echo $this->get_field_name('touch_drag'); ?>" <?php array_key_exists('touch_drag', $instance) ? checked( (bool) $instance['touch_drag'], true ): null; ?> value="1" />
            <label for="<?php echo $this->get_field_id('touch_drag'); ?>"><?php _e('Touch events', 'woocommerce-products-carousel-all-in-one'); ?></label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('easing'); ?>"><?php echo _e('Animation', 'woocommerce-products-carousel-all-in-one'); ?>:</label>
            <br />
            <select class="select woocommerce-products-carousel-all-in-one-field field-validate" name="<?php echo $this->get_field_name('easing'); ?>" id="<?php echo $this->get_field_id('easing'); ?>">
            <?php
                $animation_list = WooCommerce_Products_Carousel_All_In_One_Utils::getAnimations();
                foreach($animation_list as $key => $list) {
                    echo '<option value="'. esc_attr($key) .'" '. (esc_attr($instance["easing"]) == esc_attr($key) ? 'selected="selected"' : null) .'>'. esc_attr($list) .'</option>';
                }
            ?>
            </select>
        </p>
        <p>
            <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('auto_width'); ?>" name="<?php echo $this->get_field_name('auto_width'); ?>" <?php array_key_exists('auto_width', $instance) ? checked( (bool) $instance['auto_width'], false ): null; ?> value="1"/>
            <label for="<?php echo $this->get_field_id('auto_width'); ?>"><?php _e('Auto width', 'woocommerce-products-carousel-all-in-one'); ?></label>
            <?php echo WooCommerce_Products_Carousel_All_In_One_Utils::getTooltip( __("It's recommended to set width on divs, but sometimes you may want this option :)", 'woocommerce-products-carousel-all-in-one') ); ?>
        </p>
        <p>
            <input class="checkbox woocommerce-products-carousel-all-in-one-field" type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('auto_height'); ?>" name="<?php echo $this->get_field_name('auto_height'); ?>" <?php array_key_exists('auto_height', $instance) ? checked( (bool) $instance['auto_height'], true ): null; ?> value="1"/>
            <label for="<?php echo $this->get_field_id('auto_height'); ?>"><?php _e('Auto height', 'woocommerce-products-carousel-all-in-one'); ?></label>
            <?php echo WooCommerce_Products_Carousel_All_In_One_Utils::getTooltip( __('Height adjusted dynamically to highest displayed item.', 'woocommerce-products-carousel-all-in-one') ); ?>
            <?php echo WooCommerce_Products_Carousel_All_In_One_Utils::getTooltip( __('With some themes or servers it might cause problems. If you have problem with your carousel (e.g: one of the images did not show, etc.), please uncheck this option.', 'woocommerce-products-carousel-all-in-one'), 'warning' ); ?>
        </p>
    </div>
<?php
    }
}
add_action('widgets_init', create_function('', 'return register_widget("WooCommerceProductsCarouselAllInOneWidget");'));
?>