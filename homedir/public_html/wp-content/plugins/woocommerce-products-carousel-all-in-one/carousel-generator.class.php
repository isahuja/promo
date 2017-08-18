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

class WooCommerceProductsCarouselAllInOneGenerator {
    private $allowed_templates_names = array('image', 'title', 'categories', 'description', 'price', 'tags', 'buttons');

    private $template = array(
        'image' => 'image',
        'details' => array(
            'title', 'categories', 'description', 'price', 'tags', 'buttons',
        ),
    );

    private $id = '';
    private $title = '';
    private $featured_image = '';
    private $description = '';
    private $tags = '';
    private $category = '';
    private $price = '';
    private $buttons = '';

    private function getAllowedTemplatesNames() {
        return $this->allowed_templates_names;
    }

    private function getTemplate() {
        return $this->template;
    }

    private function generateId() {
        return mt_rand();
    }


    // public function __construct() {

    // }

    public function getDefaults() {
        return array(
            'id'                      => $this->generateId(),
            'template'                => 'default.css',
            'ordering'                => 'asc',
            'products'                => '',
            'out_of_stock'            => 'false',
            'exclude'                 => '',
            'categories'              => '',
            'relation'                => 'or',
            'tags'                    => '',
            'all_items'               => 10,
            'show_only'               => 'id',

            'show_title'              => 'true',
            'show_price'              => 'true',
            'show_description'        => 'excerpt',
            'allow_shortcodes'        => 'false',
            'show_category'           => 'true',
            'show_tags'               => 'false',
            'show_add_to_cart_button' => 'true',
            'show_more_button'        => 'true',
            'show_more_items_button'  => 'true',
            'show_featured_image'     => 'true',
            'image_source'            => 'thumbnail',
            'image_width'             => 100,
            'image_height'            => 100,

            'items_to_show_mobiles'   => 1,
            'items_to_show_tablets'   => 2,
            'items_to_show'           => 4,
            'loop'                    => 'true',
            'auto_play'               => 'true',
            'stop_on_hover'           => 'true',
            'auto_play_timeout'       => 1200,
            'auto_play_speed'         => 800,
            'nav'                     => 'true',
            'nav_speed'               => 800,
            'dots'                    => 'true',
            'dots_speed'              => 800,
            'margin'                  => 5,
            'stage_padding'           => 0,
            'lazy_load'               => 'false',
            'mouse_drag'              => 'true',
            'mouse_wheel'             => 'true',
            'touch_drag'              => 'true',
            'slide_by'                => 1,
            'easing'                  => 'linear',
            'auto_width'              => 'false',
            'auto_height'             => 'true',

            'custom_breakpoints'      => '',
            );
    }

    public function generate($atts) {
        global $post;

        /*
         * default parameters
         */
        $this->params = $this->prepareSettings($atts);

        /*
         * fix to previous versions
         */
        if (array_key_exists('show_description', $this->params) && in_array( $this->params['show_description'], array('true', 'false') )) {
            $this->params['show_description'] = $this->params['show_description'] == 'true' ? 'excerpt' : 'false';
        }

        /*
         * theme
         */
        $theme =  $this->params['template'];
        $theme_name = str_replace('.css', '', $theme);

        /*
         * check if template css file exists
         */
        $plugin_theme_url = plugins_url( dirname(plugin_basename(__FILE__)) ) . '/templates/' . $theme;
        $plugin_theme_file = plugin_dir_path( __FILE__ ) . '/templates/' . $theme;

        $site_theme_url = get_stylesheet_directory_uri() . '/css/woocommerce-products-carousel-all-in-one/' . $theme;
        $site_theme_file = get_stylesheet_directory() . '/css/woocommerce-products-carousel-all-in-one/' . $theme;

        if ( @file_exists($site_theme_file) ) {
            wp_enqueue_style('woocommerce_products_carousel_all_in_one-carousel-style-' . $theme_name, $site_theme_url, true);
        } else if ( @file_exists($plugin_theme_file) ) {
            wp_enqueue_style('woocommerce_products_carousel_all_in_one-carousel-style-' . $theme_name, $plugin_theme_url, true);
        } else {
            return '<div class="error"><p>' . printf( __('Theme - %.css stylesheet is missing. ', 'woocommerce-products-carousel-all-in-one'), $theme_name ) . '</p></div>';
        }

        /*
         * prepare html and loop
         */
        $out = '<div id="woocommerce-products-carousel-all-in-one-' . $this->params['id'] . '" class="' . $theme_name . '-theme woocommerce-products-carousel-all-in-one owl-carousel">';

        /*
         * prepare sql query
         */
        $query_args = array(
            'post_type'      =>  'product',
            'post_status'    =>  'publish',
            'posts_per_page' =>  $this->params['all_items'],
            'no_found_rows'  =>  1,
            'post__not_in'   =>  array_merge( array($post->ID), explode(',', $this->params['exclude']) ) //exclude current post and the others
        );

        $sql_i = 0;

        if ( $this->params['products'] != "" ) {
            $query_args['post__in'] = explode(',', $this->params['products']);
        }

        if ( $this->params['categories'] != "" || $this->params['tags'] != "" ) {
            $query_args['tax_query'] = array('relation' => strtoupper( $this->params['relation'] ), array());
        }

        if ( $this->params['categories'] != "" ) {
            $query_args['tax_query'] = array(array(
                'taxonomy'  =>  'product_cat',
                'field'     =>  'id',
                'terms'     =>  explode(',', $this->params['categories']),
                'operator'  => 'IN'
            ));
        }

        if ( $this->params['tags'] != "" ) {
            $query_args['tax_query'][$sql_i++] = array(
                'taxonomy'  =>  'product_tag',
                'field'     =>  'name',
                'terms'     =>  explode(',', $this->params['tags']),
                'operator'  => 'IN'
            );
        }


        if ( $this->params['products'] != "" ) {
            $query_args['orderby'] = 'ID';
        } else {
            $index = 0;
            switch( $this->params['show_only'] ) {
                case "id":
                    $query_args['orderby'] = 'ID';
                break;
                case "title":
                default:
                    $query_args['orderby'] = 'post_title';
                break;
                case "newest":
                    $query_args['orderby'] = 'post_date';
                break;
                case "popular":
                    $query_args['meta_key'] = 'total_sales';
                    $query_args['orderby']  = 'meta_value_num';
                break;
                case "featured":
                    $query_args['meta_key']   = '_featured';
                    $query_args['orderby']    = 'date';
                    $query_args['meta_value'] = 'yes';
                break;
                case "sale":
                    $query_args['meta_query'] = array(
                        'relation' => 'OR',
                        $index++ => array(
                            'relation' => 'OR',
                            array(
                                'key'     => '_sale_price',
                                'value'   => 0,
                                'compare' => '>',
                                'type'    => 'numeric'
                            ),
                            array(
                                'key'     => '_min_variation_sale_price',
                                'value'   => 0,
                                'compare' => '>',
                                'type'    => 'numeric'
                            ),
                        )

                    );
                    $query_args['orderby']    = '_sale_price';
                break;
            }
        }


        /*
        * include/exclude out of stock
        */
        if ( !array_key_exists('meta_query', $query_args) ) {
            $query_args['meta_query'] = array(
                'relation' => 'OR'
            );
        }

        $query_args['meta_query'][$index++] = array(
            'key'     => '_stock_status',
            'value'   => 'instock',
            'compare' => '=',
        );

        if ( $this->params['out_of_stock'] === 'true' ) {
            $query_args['meta_query'][$index++] = array(
                'key'     => '_stock_status',
                'value'   => 'outofstock',
                'compare' => '=',
            );
        }

        if ( in_array($this->params['ordering'], array('asc', 'desc')) ) {
            $query_args['order'] = $this->params['ordering'];
        } else {
            $query_args['order'] = 'desc';
        }
        /*
         * end sql query
         */

        $loop = new WP_Query(apply_filters('woocommerce_products_carousel_all_in_one_query', $query_args));

        /*
         * if random, we shuffle array
         */
        if ( $this->params['ordering'] === "random" ) {
            shuffle($loop->posts);
        }


        /*
         * check if there are more then one item
         */
        $this->params['post_count'] = $loop->post_count;
        if ( !$this->params['post_count'] > 1 ) {
            return false;
        }

        /*
         * products loop
         */
        while ( $loop->have_posts() ) {
            $loop->the_post();

            /*
             * create product object
             */
            $this->title = '';
            $this->featured_image = '';
            $this->description = '';
            $this->tags = '';
            $this->category = '';
            $this->price = '';
            $this->buttons = '';
            $this->product = get_product( $post->ID );

            $this->product_url = esc_url( get_permalink(apply_filters('woocommerce_in_cart_product', $this->product->id)) );
            $this->shop_url = esc_url( get_permalink(woocommerce_get_page_id('shop')));


            $this->image = wp_get_attachment_image_src( get_post_thumbnail_id($this->product->id), $this->params['image_source'] );

            /*
             * if no featured image for product
             */
            if ( $this->image[0] == '' || $this->image[0] == '/' ) {
                $this->image[0] = apply_filters('woocommerce_products_carousel_all_in_one_item_featured_image_placeholder', plugin_dir_url(__FILE__) . 'images/placeholder.png');
            }

            /*
             * show featured image
             */
            if ( $this->params['show_featured_image'] === 'true' ) {

                $data_src = 'src="' . $this->image[0] . '"';
                $image_class = null;

                if ( $this->params['lazy_load'] === 'true' ) {
                    $data_src = 'data-src="' . $this->image[0] . '" data-src-retina="' . $this->image[0] . '"';
                    $image_class = 'class="owl-lazy"';
                }

                $featured_image = '<div class="woocommerce-products-carousel-all-in-one-image">';
                    $featured_image .= '<a href="' . $this->product_url . '" title="' . __('Show item', 'woocommerce-products-carousel-all-in-one') . ' ' . $this->product->post->post_title . '">';
                        $featured_image .= '<img alt="' . $this->product->post->post_title . '" style="max-width:' . $this->params['image_width'] . '%;max-height:' . $this->params['image_height'] . '%" ' . $data_src . $image_class . '>';
                    $featured_image .= '</a>';
                $featured_image .= '</div>';

                $this->featured_image = $featured_image;
            }

            /*
             * show title
             */
            if ( $this->params['show_title'] === 'true' ) {
                $title = '<h3 class="woocommerce-products-carousel-all-in-one-title">';
                    $title .= '<a href="' . $this->product_url . '" title="' . $this->product->post->post_title . '">' . $this->product->post->post_title . '</a>';
                $title .= '</h3>';

                $this->title = $title;
            }

            /*
             * show categories
             */
            $this->categories_list = array();
            if ( $this->params['show_category'] === 'true' ) {
                $this->categories_list = get_the_terms( get_the_ID(), 'product_cat' );
                if ($this->categories_list) {
                    $category = '<p class="woocommerce-products-carousel-all-in-one-categories">';
                    foreach ($this->categories_list as $cat) {
                        $category .= '<a href="' .  get_term_link( $cat ) . '" title="' . esc_attr( sprintf( __( "View all items in %s" ), $cat->name ) ) . '">' . $cat->name . '</a> ';
                    }
                    $category .= '</p>';

                    $this->category = $category;
                }
            }

            /*
             * show tags
             */
            $this->tags_list = array();
            if ( $this->params['show_tags'] == 'true' ) {
                $this->tags_list = get_the_term_list( get_the_ID(), 'product_tag', '', ' ', '' );
                $tags = '<p class="woocommerce-products-carousel-all-in-one-tags">';
                    $tags .= get_the_term_list( get_the_ID(), 'product_tag', '', ' ', '' );
                $tags .= '</p>';

                $this->tags = $tags;
            }

            /*
             * show description
             */
            if ( $this->params['show_description'] === 'excerpt' ) {
                $this->description = '<div class="woocommerce-products-carousel-all-in-one-desc">' . get_the_excerpt() . '</div>';
            } else if ( $this->params['show_description'] === 'content' ) {
                $this->description = '<div class="woocommerce-products-carousel-all-in-one-desc">' . ( $this->params['allow_shortcodes'] === 'true' ? do_shortcode( get_the_content( '', true) ) : get_the_content() ) . '</div>';
            }

            /*
             * show price
             */
            if ( $this->product->is_on_sale() ) {
                $this->price .= '<span class="woocommerce-products-carousel-all-in-one-sale">' . __('On sale', 'woocommerce-products-carousel-all-in-one') . '</span>';
            }

            if ( $this->params['show_price'] == 'true' ) {
                if (get_option('woocommerce_display_cart_prices_excluding_tax') === 'yes') {
                    $this->price_value = apply_filters( 'woocommerce_cart_item_price_html', woocommerce_price( $this->product->get_price_excluding_tax() ) );
                } else {
                    $this->price_value = apply_filters( 'woocommerce_cart_item_price_html', woocommerce_price( $this->product->get_price() ) );
                }

                /*
                 * if product variables
                 */
                if ( $this->product->product_type === 'variable' ) {
                    $this->price .= '<span class="woocommerce-products-carousel-all-in-one-price">' . __('From', 'woocommerce-products-carousel-all-in-one') . ' ' . $this->price_value . '</span>';
                } else {
                    $this->price .= '<span class="woocommerce-products-carousel-all-in-one-price">' . $this->price_value . '</span>';
                }
            }

            /*
             * show buttons
             */
            if ( $this->params['show_add_to_cart_button'] === 'true' || $this->params['show_more_button'] === 'true' || $this->params['show_more_items_button'] === 'true' ) {
                $buttons = '<p class="woocommerce-products-carousel-all-in-one-buttons">';


                if ( $this->params['show_add_to_cart_button'] === 'true' ) {
                    if ( $this->product->is_in_stock() ) {
                        $buttons .= sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="woocommerce-products-carousel-all-in-one-add-to-cart-button button %s product_type_%s" title="%s">%s</a>',
                            esc_url( $this->product->add_to_cart_url() ),
                            esc_attr( get_the_ID() ),
                            esc_attr( $this->product->get_sku() ),
                            esc_attr( 1 ),
                            $this->product->is_purchasable() && $this->product->is_in_stock() ? 'add_to_cart_button' : '',
                            esc_attr( $this->product->product_type ),
                            esc_html( __('Add to cart', 'woocommerce-products-carousel-all-in-one') ),
                            esc_html( __('add to cart', 'woocommerce-products-carousel-all-in-one') )
                            );
                    } else {
                        $buttons .= '<a href="' . $this->product_url . '" class="woocommerce-products-carousel-all-in-one-stock-button button" title="' . __('Out of stock', 'woocommerce-products-carousel-all-in-one') . '">' . __('Out of stock', 'woocommerce-products-carousel-all-in-one') . '</a>';
                    }
                }

                if ( $this->params['show_more_button'] === 'true' ) {
                    if( $this->product->is_downloadable() ) {
                        $buttons .= '<a href="' . $this->product_url . '" class="woocommerce-products-carousel-all-in-one-more-button button" title="' . __('Download', 'woocommerce-products-carousel-all-in-one') . ' ' . $this->product->post->post_title . '">' . __('<i class="icon-download-alt icon-"></i> download', 'woocommerce-products-carousel-all-in-one') . '</a>';
                    } else {
                        $buttons .= '<a href="' . $this->product_url . '" class="woocommerce-products-carousel-all-in-one-more-button button" title="' . __('Show item', 'woocommerce-products-carousel-all-in-one') . ' ' . $this->product->post->post_title . '">' . __('show item', 'woocommerce-products-carousel-all-in-one') . '</a>';
                    }
                }

                if ( $this->params['show_more_items_button'] === 'true' ) {
                    $buttons .= '<a href="' . $this->shop_url . '" class="woocommerce-products-carousel-all-in-one-more-items-button button" title="' . __('Show more items', 'woocommerce-products-carousel-all-in-one') . '">' . __('show more items', 'woocommerce-products-carousel-all-in-one') . '</a>';
                }

                $buttons .= '</p>';

                $this->buttons = $buttons;
            }


            /*
             * list products
             */
            $out .= '<div class="woocommerce-products-carousel-all-in-one-slide slides-' . $this->params['items_to_show'] . '">';
                $out .= '<div class="woocommerce-products-carousel-all-in-one-container">';
                    do_action( 'woocommerce_products_carousel_all_in_one_before_item_content', $this->params );


                    /*
                    * template ordering
                    */
                    $template = apply_filters( 'woocommerce_products_carousel_all_in_one_template', $this->getTemplate(), array(
                        'params'      => $this->params,
                    ) );

                    foreach ( $template as $template_key => $template_name ) {
                        if ( $template_key !== 'details') {
                            $uc_name = ucfirst($template_name);

                            if ( in_array( $template_name, $this->getAllowedTemplatesNames() ) ) {
                                $fnc = 'generate' . $uc_name;
                                $out .= $this->{$fnc}();
                            }
                        } else {
                            $out .= '<div class="woocommerce-products-carousel-all-in-one-details">';

                            foreach ( $template_name as $name ) {
                                $uc_name = ucfirst($name);

                                if ( in_array( $name, $this->getAllowedTemplatesNames() ) ) {
                                    $fnc = 'generate' . $uc_name;
                                    $out .= $this->{$fnc}();
                                }
                            }

                            $out .= '</div>';
                        }
                    }

                    do_action( 'woocommerce_products_carousel_all_in_one_after_item_content', $this->params );
                $out .= '</div>';
            $out .= '</div>';
        }
        /*
         * reset wordpress query
         */
        wp_reset_postdata();

        $out .= '</div>';

        /*
         * generate jQuery script for OwlCarousel
         */
        $out .= $this->carousel($this->params);
        return $out;
    }

    private function carousel($params = array()) {
        if ( empty($params) ) {
            return false;
        }
        $mouse_wheel = null;

        if ( $params['mouse_wheel'] == 'true' ) {
            $mouse_wheel = 'wooCommerceCarousel' . $params['id'] . ' .on("mousewheel", ".owl-stage", function(e) {
                if (e.deltaY > 0) {
                    wooCommerceCarousel' . $params['id'] . ' .trigger("next.owl");
                } else {
                    wooCommerceCarousel' . $params['id'] . ' .trigger("prev.owl");
                }
                e.preventDefault();
            });';
        }

        $out = '<script type="text/javascript">
                    jQuery(window).load(function(e) {
                        var wooCommerceCarousel' . $params['id'] . ' = jQuery("#woocommerce-products-carousel-all-in-one-' . $params['id'] . '");
                        wooCommerceCarousel' . $params['id'] . ' .owlCarousel({
                            loop: ' . ( $params['post_count'] > 1  ? $params['loop'] : 'false' ) .',
                            nav: ' . $params['nav'] . ',
                            navSpeed: ' . $params['nav_speed'] . ',
                            dots: ' . $params['dots'] . ',
                            dotsSpeed: ' . $params['dots_speed'] . ',
                            lazyLoad: ' . $params['lazy_load'] . ',
                            autoplay: ' . $params['auto_play'] . ',
                            autoplayHoverPause: ' . $params['stop_on_hover'] . ',
                            autoplayTimeout: ' . $params['auto_play_timeout'] . ',
                            autoplaySpeed:  ' . $params['auto_play_speed'] . ',
                            margin: ' . $params['margin'] . ',
                            stagePadding: ' . $params['stage_padding'] . ',
                            freeDrag: false,
                            mouseDrag: ' . $params['mouse_drag'] . ',
                            touchDrag: ' . $params['touch_drag'] . ',
                            slideBy: ' . $params['slide_by'] . ',
                            fallbackEasing: "' . $params['easing'] . '",
                            responsiveClass: true,
                            navText: [ "' . __('previous product', 'woocommerce-products-carousel-all-in-one') . '", "' . __('next product', 'woocommerce-products-carousel-all-in-one') . '" ],
                            responsive:{
                                0:{
                                    items: ' . ( $params['items_to_show_mobiles'] != '' ? intval( $params['items_to_show_mobiles']) : 1 ) . ',
                                },
                                600:{
                                    items: ' . ( $params['items_to_show_tablets'] != '' ? intval( $params['items_to_show_tablets'] ) : (ceil($params['items_to_show']/2)) ) . ',

                                },
                                1000:{
                                    items: ' . intval( $params['items_to_show'] ) . '
                                }
                            },
                            autoWidth: ' . $params['auto_width'] . ',
                            autoHeight: ' . $params['auto_height'] . '
                        });
                        ' . $mouse_wheel . '
                    });
                </script>';

        return $out;
    }


    public function prepareSettings($settings) {
        $checkboxes = array(
            'out_of_stock'            => 'false',
            'show_title'              => 'true',
            'allow_shortcodes'        => 'false',
            'show_category'           => 'true',
            'show_tags'               => 'false',
            'show_price'              => 'true',
            'show_add_to_cart_button' => 'true',
            'show_more_button'        => 'true',
            'show_more_items_button'  => 'true',
            'show_featured_image'     => 'true',

            'loop'                    => 'true',
            'auto_play'               => 'true',
            'stop_on_hover'           => 'true',
            'nav'                     => 'true',
            'dots'                    => 'true',
            'lazy_load'               => 'false',
            'mouse_drag'              => 'true',
            'mouse_wheel'             => 'true',
            'touch_drag'              => 'true',
            'auto_width'              => 'false',
            'auto_height'             => 'true',
            );

        foreach( $checkboxes as $k => $v ) {
            if ( !array_key_exists($k, $settings) ) {
                $settings[$k] = 'false';
            } else {
                $settings[$k] = ($settings[$k] == 1 || $settings[$k] == 'true') ? 'true' : 'false';
            }
        }

        $settings['id'] = $this->generateId();

        /*
         * if there are no all settings
         */
        $defaults = $this->getDefaults();
        foreach( $defaults as $k => $v ) {
            if ( !array_key_exists($k, $settings) ) {
                $settings[$k] = $defaults[$k];
            }
        }
        return $settings;
    }


    private function generateImage() {
        return apply_filters( 'woocommerce_products_carousel_all_in_one_item_featured_image', $this->featured_image, array(
            'product_url' => $this->product_url,
            'product'     => $this->product,
            'image'       => $this->image[0],
            'params'      => $this->params,
        ) );
    }

    private function generateTitle() {
        return apply_filters( 'woocommerce_products_carousel_all_in_one_item_title', $this->title, array(
            'product_url' => $this->product_url,
            'product'     => $this->product,
            'params'      => $this->params,
        ) );
    }

    private function generateCategories() {
        return apply_filters( 'woocommerce_products_carousel_all_in_one_item_categories', $this->category, array(
            'categories_list' => $this->categories_list,
            'product'         => $this->product,
            'params'          => $this->params,
        ) );
    }

    private function generateDescription() {
        return apply_filters( 'woocommerce_products_carousel_all_in_one_item_description', $this->description, array(
            'product' => $this->product,
            'params'  => $this->params,
        ) );
    }

    private function generatePrice() {
        return apply_filters( 'woocommerce_products_carousel_all_in_one_item_price', $this->price, array(
            'product' => $this->product,
            'params'  => $this->params,
        ) );
    }

    private function generateTags() {
        return apply_filters( 'woocommerce_products_carousel_all_in_one_item_tags', $this->tags, array(
            'tags_list' => $this->tags_list,
            'product'   => $this->product,
            'params'    => $this->params,
        ) );
    }

    private function generateButtons() {
        return apply_filters( 'woocommerce_products_carousel_all_in_one_item_buttons', $this->buttons, array(
            'product'     => $this->product,
            'product_url' => $this->product_url,
            'shop_url'    => $this->shop_url,
            'params'      => $this->params,
        ) );
    }
}