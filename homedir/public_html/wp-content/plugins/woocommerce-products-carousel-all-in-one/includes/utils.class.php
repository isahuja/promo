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

class WooCommerce_Products_Carousel_All_In_One_Utils {

    public static function getTemplates() {
        $plugin_theme_file = scandir( plugin_dir_path(__FILE__) . '../templates/' );

        if ( count($plugin_theme_file) > 0 && array_key_exists(0, $plugin_theme_file) && array_key_exists(1, $plugin_theme_file)) {
            unset($plugin_theme_file[0]);
            unset($plugin_theme_file[1]);
        }

        $site_theme = get_stylesheet_directory() . '/css/woocommerce-products-carousel-all-in-one/';
        if ( is_dir($site_theme) ) {
            $site_theme_file = scandir( $site_theme );

            if ( count($site_theme_file) > 0 && array_key_exists(0, $site_theme_file) && array_key_exists(1, $site_theme_file) ) {
                unset($site_theme_file[0]);
                unset($site_theme_file[1]);
            }
        } else {
            $site_theme_file = array();
        }

        $templates = array_merge( $plugin_theme_file, $site_theme_file );

        return $templates;
    }

    public static function getShows() {
        return array(
            'id'       => __('By id', 'woocommerce-products-carousel-all-in-one'),
            'title'    => __('By title', 'woocommerce-products-carousel-all-in-one'),
            'newest'   => __('Newest', 'woocommerce-products-carousel-all-in-one'),
            'popular'  => __('Popular', 'woocommerce-products-carousel-all-in-one'),
            'featured' => __('Featured', 'woocommerce-products-carousel-all-in-one'),
            'sale'   => __('On sale', 'woocommerce-products-carousel-all-in-one'),
        );
    }

    public static function getOrderings() {
        return array(
            'asc'    => __('Ascending', 'woocommerce-products-carousel-all-in-one'),
            'desc'   => __('Descending', 'woocommerce-products-carousel-all-in-one'),
            'random' => __('Random', 'woocommerce-products-carousel-all-in-one')
        );
    }

    public static function getDescriptions() {
        return array(
            'false'   => __('No', 'woocommerce-products-carousel-all-in-one'),
            'excerpt' => __('Excerpt', 'woocommerce-products-carousel-all-in-one'),
            'content' => __('Full content', 'woocommerce-products-carousel-all-in-one')
        );
    }

    public static function getSources() {
        return array(
            'thumbnail' => __('Thumbnail', 'woocommerce-products-carousel-all-in-one'),
            'medium'    => __('Medium', 'woocommerce-products-carousel-all-in-one'),
            'large'     => __('Large', 'woocommerce-products-carousel-all-in-one'),
            'full'      => __('Full', 'woocommerce-products-carousel-all-in-one')
        );
    }

    public static function getAnimations() {
        return array(
            'linear'             => 'linear',
            'swing'              => 'swing',
            'easeInQuad'         => 'easeInQuad',
            'easeOutQuad'        => 'easeOutQuad',
            'easeInOutQuad'      => 'easeInOutQuad',
            'easeInCubic'        => 'easeInCubic',
            'easeOutCubic'       => 'easeOutCubic',
            'easeInOutCubic'     => 'easeInOutCubic',
            'easeInQuart'        => 'easeInQuart',
            'easeOutQuart'       => 'easeOutQuart',
            'easeInOutQuart'     => 'easeInOutQuart',
            'easeInQuint'        => 'easeInQuint',
            'easeOutQuint'       => 'easeOutQuint',
            'easeInOutQuint'     => 'easeInOutQuint',
            'easeInExpo'         => 'easeInExpo',
            'easeOutExpo'        => 'easeOutExpo',
            'easeInOutExpo'      => 'easeInOutExpo',
            'easeInSine'         => 'easeInSine',
            'easeOutSine'        => 'easeOutSine',
            'easeInOutSine'      => 'easeInOutSine',
            'easeInCirc'         => 'easeInCirc',
            'easeOutCirc'        => 'easeOutCirc',
            'easeInOutCirc'      => 'easeInOutCirc',
            'easeInElastic'      => 'easeInElastic',
            'easeOutElastic'     => 'easeOutElastic',
            'easeInOutElastic'   => 'easeInOutElastic',
            'easeInBack'         => 'easeInBack',
            'easeOutBack'        => 'easeOutBack',
            'easeInOutBack'      => 'easeInOutBack',
            'easeInBounce'       => 'easeInBounce',
            'easeOutBounce'      => 'easeOutBounce',
            'easeInOutBounce'    => 'easeInOutBounce'
        );
    }

    public static function getRelations() {
        return array(
            'and' => __('And', 'wp-posts-carousel'),
            'or'  => __('Or', 'wp-posts-carousel'),
        );
    }

    public static function getTooltip( $text = null, $type = 'help' ) {
        if( $text == null ) {
            return null;
        }

        if ( in_array( $type, array('help', 'warning') ) ) {
            switch ($type) {
                case 'warning':
                    $type = 'warning';
                    break;
                case 'help':
                default:
                    $type = 'editor-help';
                    break;
            }
        }
        return '<a href="" title="' . esc_attr($text) . '" class="woocommerce-products-carousel-all-in-one-tooltip tooltip-' . $type . '"><span class="dashicons dashicons-' . $type . '" title="' . __('Hint', 'woocommerce-products-carousel-all-in-one') . '"></span></a>';
    }

    public static function parseBreakpoints( $params ) {
        if ( $params == '') {
            return null;
        }
        $out = '';
        $plugin_options = get_option( 'woocommerce-products-carousel-all-in-one-carousel_options' );
        $breakpoints_array = array();

        if ( array_key_exists('custom_breakpoints', $plugin_options) ) {
            $plugin_breakpoints = explode(',', $plugin_options['custom_breakpoints']);
            $data = @unserialize( $params ) ;
            /*
            * is serialized from widget
            * else from shortcode
             */
            if ( $data !== false || $data === 'b:0;' ) {
                $breakpoints = unserialize( $params );
            } else {
                $data = explode(',', $params);
                if ( !empty($data) ) {
                    foreach ( $data as $points) {

                        $point = explode(':', $points);

                        if ( array_key_exists(0, $point) && array_key_exists(1, $point) ) {
                            $breakpoints_array[$point[0]] = $point[1];
                        }
                    }
                }
            }

            if ( count($breakpoints_array) > 0 ) {
                foreach ( $breakpoints_array as $breakpoint => $items ) {
                    if ( intval($breakpoint) > 0 && intval($items) > 0 && !in_array($breakpoint, array(0,600,1000))) {
                        $out .= ',' . intval($breakpoint) . ':{items: ' . intval($items) . '}';
                    }
                }
            }
        }
        return $out;
    }
}
