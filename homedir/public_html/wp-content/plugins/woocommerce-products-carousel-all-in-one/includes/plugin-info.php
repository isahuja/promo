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
<div class="woocommerce-products-carousel-all-in-one-box">
    <div class="woocommerce-products-carousel-all-in-one-box-content woocommerce-products-carousel-all-in-one-box-separator">
        <p><?php _e('Version', 'woocommerce-products-carousel-all-in-one') ?>: <?php echo self::VERSION ?></p>
    </div>

    <h3 class="woocommerce-products-carousel-all-in-one-box-separator">
        <?php _e('Donate, if you like this plugin', 'woocommerce-products-carousel-all-in-one') ?>
    </h3>
    <div class="woocommerce-products-carousel-all-in-one-box-content woocommerce-products-carousel-all-in-one-box-separator">
        <p><?php _e('Thanks for all donations, no matter the size.', 'woocommerce-products-carousel-all-in-one') ?></p>

        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
            <input type="hidden" name="cmd" value="_s-xclick">
            <input type="hidden" name="hosted_button_id" value="2SQA4FL25Y73W">
            <input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online." style="margin: 0 auto;display: block;">
            <img alt="" border="0" src="https://www.paypalobjects.com/pl_PL/i/scr/pixel.gif" width="1" height="1">
        </form>
    </div>

    <div class="woocommerce-products-carousel-all-in-one-box-rating">
        <p>
            <a href="https://wordpress.org/plugins/woocommerce-products-carousel-all-in-one/" target="_blank" title="<?php _e('Leave your review at wordpress.org', 'woocommerce-products-carousel-all-in-one') ?>">
                <?php _e('You can also leave a good review', 'woocommerce-products-carousel-all-in-one') ?><br />
                <span class="dashicons dashicons-star-filled"></span>
                <span class="dashicons dashicons-star-filled"></span>
                <span class="dashicons dashicons-star-filled"></span>
                <span class="dashicons dashicons-star-filled"></span>
                <span class="dashicons dashicons-star-filled"></span>
            </a>
        </p>
    </div>
</div>


<div class="woocommerce-products-carousel-all-in-one-box orange-box">
    <h3 class="woocommerce-products-carousel-all-in-one-box-separator">
        <?php _e('Need support?', 'woocommerce-products-carousel-all-in-one') ?>
    </h3>
    <div class="woocommerce-products-carousel-all-in-one-box-content">
        <p>
            <?php _e('If you are having problems with this plugin, please contact by', $this->plugin_slug) ?> <a href="mailto:info@teastudio.pl" target="_blank" title="info@teastudio.pl">info@teastudio.pl</a>
        </p>
        <p>
            <?php _e('For more information about this plugin, please visit', 'woocommerce-products-carousel-all-in-one') ?> <a href="http://www.teastudio.pl/en/product/woocommerce-products-carousel-all-in-one/" target="_blank" title="http://www.teastudio.pl/en/product/woocommerce-products-carousel-all-in-one/"><?php _e('plugin page', 'woocommerce-products-carousel-all-in-one') ?></a><br />
        </p>
    </div>
</div>

<div class="woocommerce-products-carousel-all-in-one-box green-box">
    <h3 class="woocommerce-products-carousel-all-in-one-box-separator">
        <?php _e('Need custom modification, plugin or theme?', 'woocommerce-products-carousel-all-in-one') ?>
    </h3>
    <div class="woocommerce-products-carousel-all-in-one-box-content">
        <p>
            <?php _e('If you like this plugin, but need something a bit more custom or completely new, you can hire me to work for you!', 'woocommerce-products-carousel-all-in-one') ?>
        </p>
        <p>
            <?php _e('Email me at <a href="mailto:m.gierada@teastudio.pl" title="Hire me">m.gierada@teastudio.pl</a> for more information!', 'woocommerce-products-carousel-all-in-one') ?>
        </p>
    </div>
    <div class="woocommerce-products-carousel-all-in-one-box-content">
        <p>
            <a href="http://www.teastudio.pl" target="_blank" title="Design and web development - www.teastudio.pl"><img src="<?php echo plugins_url('/../images/teastudio-logo.png' , __FILE__ ) ?>" alt="www.teastudio.pl" /></a>
        </p>
    </div>
</div>