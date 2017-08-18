<?php

/**
 * Protect direct access
 */
if ( ! defined( 'ABSPATH' ) ) die( WPCS_HACK_MSG );

/**
 * Registers WooCommerce product carousel slider post type.
 */
function wpcs_init() {
    $labels = array(
        'name'               => _x( 'WooCommerce Products Carousel Sliders', 'woocommerce-product-carousel-slider' ),
        'singular_name'      => _x( 'WooCommerce Products Carousel Slider', 'woocommerce-product-carousel-slider' ),
        'menu_name'          => _x( 'Woo Carousel', 'woocommerce-product-carousel-slider' ),
        'name_admin_bar'     => _x( 'Woo Carousel', 'woocommerce-product-carousel-slider' ),
        'add_new'            => _x( 'Add New', 'woocommerce-product-carousel-slider' ),
        'add_new_item'       => __( 'Add New Carousel Slider', 'woocommerce-product-carousel-slider' ),
        'new_item'           => __( 'New Carousel Slider', 'woocommerce-product-carousel-slider' ),
        'edit_item'          => __( 'Edit Carousel Slider', 'woocommerce-product-carousel-slider' ),
        'view_item'          => __( 'View Carousel Slider', 'woocommerce-product-carousel-slider' ),
        'search_items'       => __( 'Search Carousel Slider', 'woocommerce-product-carousel-slider' ),
        'parent_item_colon'  => __( 'Parent Carousel Sliders:', 'woocommerce-product-carousel-slider' ),
        'not_found'          => __( 'No carousel slider found.', 'woocommerce-product-carousel-slider' ),
        'not_found_in_trash' => __( 'No carousel slider found in Trash.', 'woocommerce-product-carousel-slider' )
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'query_var'          => true,
        'rewrite'            => true,
        'capability_type'    => 'post',
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array( 'title' ),
        'menu_icon' => 'dashicons-images-alt2'
    );

    register_post_type( 'woocarousel', $args );
}
add_action( 'init', 'wpcs_init' );

/**
 * Adds a box to the main column on the WooCommerce product carousel slider post type edit screens.
 */
function wpcs_add_meta_box() {
                add_meta_box(
                    'wpcs_metabox',
                    __( 'Settings & Shortcode Generator','woocommerce-product-carousel-slider' ),
                    'meta_box_content_output', 
                    'woocarousel',
                    'normal'
                    );
    }
add_action( 'add_meta_boxes', 'wpcs_add_meta_box' );

/**
 * Prints the box content.
 */
function meta_box_content_output( $post ) {
    
    // Add a nonce field so we can check for it later.
    wp_nonce_field( 'wpcs_save_meta_box_data', 'wpcs_meta_box_nonce' );
    
    $wpcs_display_header = get_post_meta( $post->ID, 'wpcs_display_header', true );
    $wpcs_display_navigation_arrows = get_post_meta( $post->ID, 'wpcs_display_navigation_arrows', true );
    $wpcs_title = get_post_meta( $post->ID, 'wpcs_title', true );
    $wpcs_products_type = get_post_meta( $post->ID, 'wpcs_products_type', true );
    $wpcs_total_products = get_post_meta( $post->ID, 'wpcs_total_products', true );
    $wpcs_img_crop = get_post_meta( $post->ID, 'wpcs_img_crop', true );
    $wpcs_crop_image_width = get_post_meta( $post->ID, 'wpcs_crop_image_width', true );
    $wpcs_crop_image_height = get_post_meta( $post->ID, 'wpcs_crop_image_height', true );

    $wpcs_auto_play = get_post_meta( $post->ID, 'wpcs_auto_play', true );
    $wpcs_stop_on_hover = get_post_meta( $post->ID, 'wpcs_stop_on_hover', true );
    $wpcs_slide_speed = get_post_meta( $post->ID, 'wpcs_slide_speed', true );
    $wpcs_items = get_post_meta( $post->ID, 'wpcs_items', true );
    $wpcs_pagination = get_post_meta( $post->ID, 'wpcs_pagination', true );

    $wpcs_header_title_font_size = get_post_meta( $post->ID, 'wpcs_header_title_font_size', true );
    $wpcs_header_title_font_color = get_post_meta( $post->ID, 'wpcs_header_title_font_color', true );
    $wpcs_nav_arrow_color = get_post_meta( $post->ID, 'wpcs_nav_arrow_color', true );
    $wpcs_nav_arrow_bg_color = get_post_meta( $post->ID, 'wpcs_nav_arrow_bg_color', true );
    $wpcs_nav_arrow_hover_color = get_post_meta( $post->ID, 'wpcs_nav_arrow_hover_color', true );
    $wpcs_nav_arrow_bg_hover_color = get_post_meta( $post->ID, 'wpcs_nav_arrow_bg_hover_color', true );
    $wpcs_display_product_title = get_post_meta( $post->ID, 'wpcs_display_product_title', true );
    $wpcs_title_font_size = get_post_meta( $post->ID, 'wpcs_title_font_size', true );
    $wpcs_title_font_color = get_post_meta( $post->ID, 'wpcs_title_font_color', true );
    $wpcs_title_hover_font_color = get_post_meta( $post->ID, 'wpcs_title_hover_font_color', true );
    $wpcs_display_price = get_post_meta( $post->ID, 'wpcs_display_price', true );
    $wpcs_price_font_size = get_post_meta( $post->ID, 'wpcs_price_font_size', true );
    $wpcs_price_font_color = get_post_meta( $post->ID, 'wpcs_price_font_color', true );
    $wpcs_display_ratings = get_post_meta( $post->ID, 'wpcs_display_ratings', true );
    $wpcs_ratings_color = get_post_meta( $post->ID, 'wpcs_ratings_color', true );
    $wpcs_ratings_size = get_post_meta( $post->ID, 'wpcs_ratings_size', true );
    $wpcs_display_cart = get_post_meta( $post->ID, 'wpcs_display_cart', true );
    $wpcs_cart_font_size = get_post_meta( $post->ID, 'wpcs_cart_font_size', true );
    $wpcs_cart_font_color = get_post_meta( $post->ID, 'wpcs_cart_font_color', true );
    $wpcs_cart_bg_color = get_post_meta( $post->ID, 'wpcs_cart_bg_color', true );
    $wpcs_cart_button_hover_color = get_post_meta( $post->ID, 'wpcs_cart_button_hover_color', true );
    $wpcs_cart_button_hover_font_color  = get_post_meta( $post->ID, 'wpcs_cart_button_hover_font_color ', true );

    ?>
    <div id="tabs-container">

        <ul class="tabs-menu">
            <li class="current"><a href="#tab-1"><?php _e('General Settings', 'woocommerce-product-carousel-slider'); ?></a></li>
            <li><a href="#tab-2"><?php _e('Slider Settings', 'woocommerce-product-carousel-slider'); ?></a></li>
            <li><a href="#tab-3"><?php _e('Style Settings', 'woocommerce-product-carousel-slider'); ?></a></li>
        </ul>

        <div class="tab">

            <div id="tab-1" class="tab-content">
                <div class="cmb2-wrap form-table">
                    <div id="cmb2-metabox" class="cmb2-metabox cmb-field-list">


                        <div class="cmb-row cmb-type-radio">
                            <div class="cmb-th">
                                <label for="wpcs_display_header"><?php _e('Display Header', 'woocommerce-product-carousel-slider'); ?></label>
                            </div>
                            <div class="cmb-td">
                                <ul class="cmb2-radio-list cmb2-list">  
                                    <li><input type="radio" class="cmb2-option" name="wpcs_display_header" id="wpcs_display_header1" value="yes" <?php if($wpcs_display_header=="yes") {echo "checked"; } else { echo "checked"; } ?>> <label for="wpcs_display_header1"><?php _e('Yes', 'woocommerce-product-carousel-slider'); ?></label></li>
                                    <li><input type="radio" class="cmb2-option" name="wpcs_display_header" id="wpcs_display_header2" value="no" <?php if($wpcs_display_header=="no") {echo "checked"; } else { echo ""; } ?>> <label for="wpcs_display_header2"><?php _e('No', 'woocommerce-product-carousel-slider'); ?></label></li>
                                </ul>
                            <p class="cmb2-metabox-description"><?php _e('Display carousel slider header or not', 'woocommerce-product-carousel-slider'); ?></p>
                            </div>
                        </div>


                        <div class="cmb-row cmb-type-radio">
                            <div class="cmb-th">
                                <label for="wpcs_display_header"><?php _e('Display Navigation Arrows', 'woocommerce-product-carousel-slider'); ?></label>
                            </div>
                            <div class="cmb-td">
                                <ul class="cmb2-radio-list cmb2-list">  
                                    <li><input type="radio" class="cmb2-option" name="wpcs_display_navigation_arrows" id="wpcs_display_navigation_arrows" value="yes" <?php if( $wpcs_display_navigation_arrows == "yes" ) {echo "checked"; } else { echo "checked"; } ?>> <label for="wpcs_display_navigation_arrows"><?php _e('Yes', 'woocommerce-product-carousel-slider'); ?></label></li>
                                    <li><input type="radio" class="cmb2-option" name="wpcs_display_navigation_arrows" id="wpcs_display_navigation_arrows2" value="no" <?php if ($wpcs_display_navigation_arrows == "no" ) {echo "checked"; } else { echo ""; } ?>> <label for="wpcs_display_navigation_arrows2"><?php _e('No', 'woocommerce-product-carousel-slider'); ?></label></li>
                                </ul>
                            </div>
                        </div>


                        <div class="cmb-row cmb-type-text-medium">
                            <div class="cmb-th">
                                <label for="wpcs_title"><?php _e('Title', 'woocommerce-product-carousel-slider'); ?></label>
                            </div>
                            <div class="cmb-td">
                                <input type="text" class="cmb2-text-medium" name="wpcs_title" id="wpcs_title" value="<?php if(empty($wpcs_title)) { _e('LATEST PRODUCTS', 'woocommerce-product-carousel-slider'); } else { echo $wpcs_title; } ?>">
                                <p class="cmb2-metabox-description"><?php _e('Carousel slider title', 'woocommerce-product-carousel-slider'); ?></p>
                            </div>
                        </div> 


                        <div class="cmb-row cmb-type-text-medium">
                            <div class="cmb-th">
                                <label for="wpcs_total_products"><?php _e('Total Products', 'woocommerce-product-carousel-slider'); ?></label>
                            </div>
                            <div class="cmb-td">
                                <input type="text" class="cmb2-text-small" name="wpcs_total_products" id="wpcs_total_products" value="<?php if(empty($wpcs_total_products)) { echo 12; } else { echo $wpcs_total_products; } ?>">
                                <p class="cmb2-metabox-description"><?php _e('How many products to display in the carousel slider', 'woocommerce-product-carousel-slider'); ?></p>
                            </div>
                        </div>


                        <div class="cmb-row cmb-type-multicheck">
                            <div class="cmb-th">
                                <label for="wpcs_products_type"><?php _e('Products Type', 'woocommerce-product-carousel-slider'); ?></label>
                            </div>
                            <div class="cmb-td">
                                <ul class="cmb2-radio-list cmb2-list">
                                    <li><input type="radio" class="cmb2-option" name="wpcs_products_type" id="wpcs_products_type" value="latest" <?php if($wpcs_products_type == "latest") {echo "checked"; } else { echo ""; } ?>> <label for="wpcs_products_type"><?php _e('Latest Products', 'woocommerce-product-carousel-slider'); ?></label></li>   
                                    <li><input type="radio" class="cmb2-option" name="wpcs_products_type" id="wpcs_products_type9" value="older" <?php if($wpcs_products_type == "older") {echo "checked"; } else { echo ""; } ?>> <label for="wpcs_products_type9"><?php _e('Older Products', 'woocommerce-product-carousel-slider'); ?></label></li>                
                                    <li><input type="radio" class="cmb2-option" name="wpcs_products_type" id="wpcs_products_type3" value="featured" <?php if($wpcs_products_type == "featured") {echo "checked"; } else { echo ""; } ?>> <label for="wpcs_products_type3"><?php _e('Featured Products', 'woocommerce-product-carousel-slider'); ?></label></li>                                   
                                </ul>
                                <p class="cmb2-metabox-description"><?php _e('What type of products to display in the carousel slider', 'woocommerce-product-carousel-slider'); ?></p>
                                <ul>
                                    <p style="font-size: 14px; margin: 13px 0 5px 0; font-style: italic;">Following options available in <a href="http://adlplugins.com/plugin/woocommerce-product-carousel-slider-pro" target="_blank">Pro Version</a>:</p>
                                    <li><input disabled type="radio" class="cmb2-option" name="wpcsp_products_type" id="wpcsp_ds_products_type" value="onsale"> <label for="wpcsp_ds_products_type"><?php _e('On Sale Products', 'woocommerce-product-carousel-slider'); ?></label></li>
                                    <li><input disabled type="radio" class="cmb2-option" name="wpcsp_products_type" id="wpcsp_ds_products_type" value="bestselling"> <label for="wpcsp_ds_products_type"><?php _e('Best Selling Products', 'woocommerce-product-carousel-slider'); ?></label></li>                               
                                    <li><input disabled type="radio" class="cmb2-option" name="wpcsp_products_type" id="wpcsp_ds_products_type" value="category"> <label for="wpcsp_ds_products_type"><?php _e('Category Products', 'woocommerce-product-carousel-slider'); ?></label></li>
                                    <li class="productsbyidw"><input disabled type="radio" class="cmb2-option" name="wpcsp_products_type" id="wpcsp_ds_products_type" value="productsbyid"> <label for="wpcsp_ds_products_type"><?php _e('Products by ID', 'woocommerce-product-carousel-slider'); ?></label></li>                                        
                                    <li><input disabled type="radio" class="cmb2-option" name="wpcsp_products_type" id="wpcsp_ds_products_type" value="productsbysku"> <label for="wpcsp_ds_products_type"><?php _e('Products by SKU', 'woocommerce-product-carousel-slider'); ?></label></li>
                                    <li><input disabled type="radio" class="cmb2-option" name="wpcsp_products_type" id="wpcsp_ds_products_type" value="productsbytag"> <label for="wpcsp_ds_products_type"><?php _e('Products by Tags', 'woocommerce-product-carousel-slider'); ?></label></li>
                                    <li><input disabled type="radio" class="cmb2-option" name="wpcsp_products_type" id="wpcsp_ds_products_type" value="productsbyyear"> <label for="wpcsp_ds_products_type"><?php _e('Products by Year', 'woocommerce-product-carousel-slider'); ?></label></li>
                                    <li><input disabled type="radio" class="cmb2-option" name="wpcsp_products_type" id="wpcsp_ds_products_type" value="productsbymonth"> <label for="wpcsp_ds_products_type"><?php _e('Products by Month', 'woocommerce-product-carousel-slider'); ?></label></li>
                                </ul>
                            </div>
                        </div>


                        <div class="cmb-row cmb-type-radio">
                            <div class="cmb-th">
                                <label for="wpcs_img_crop"><?php _e('Image Resize & Crop', 'woocommerce-product-carousel-slider'); ?></label>
                            </div>
                            <div class="cmb-td">
                                <ul class="cmb2-radio-list cmb2-list">  
                                    <li><input type="radio" class="cmb2-option" name="wpcs_img_crop" id="wpcs_img_crop1" value="yes" <?php if($wpcs_img_crop=="yes")  { echo "checked"; } else { echo "checked"; } ?>> <label for="wpcs_img_crop1"><?php _e('Yes', 'woocommerce-product-carousel-slider'); ?></label></li>
                                    <li><input type="radio" class="cmb2-option" name="wpcs_img_crop" id="wpcs_img_crop2" value="no" <?php if($wpcs_img_crop=="no") { echo "checked"; } else { echo ""; } ?>> <label for="wpcs_img_crop2"><?php _e('No', 'woocommerce-product-carousel-slider'); ?></label></li>
                                </ul>
                            <p class="cmb2-metabox-description"><?php _e('Images auto resize and crop', 'woocommerce-product-carousel-slider'); ?></p>
                            </div>
                        </div>


                        <div class="cmb-row cmb-type-text-medium">
                            <div class="cmb-th">
                                <label for="wpcs_crop_image_width"><?php _e('Image Width', 'woocommerce-product-carousel-slider'); ?></label>
                            </div>
                            <div class="cmb-td">
                                <input type="text" class="cmb2-text-small" name="wpcs_crop_image_width" id="wpcs_crop_image_width" value="<?php if(empty($wpcs_crop_image_width)) { echo 300; } else { echo $wpcs_crop_image_width; } ?>">
                                <p class="cmb2-metabox-description"><?php _e('Image cropping width', 'woocommerce-product-carousel-slider'); ?></p>
                            </div>
                        </div>


                        <div class="cmb-row cmb-type-text-medium">
                            <div class="cmb-th">
                                <label for="wpcs_crop_image_height"><?php _e('Image Height', 'woocommerce-product-carousel-slider'); ?></label>
                            </div>
                            <div class="cmb-td">
                                <input type="text" class="cmb2-text-small" name="wpcs_crop_image_height" id="wpcs_crop_image_height" value="<?php if(empty($wpcs_crop_image_height)) { echo 300; } else { echo $wpcs_crop_image_height; } ?>">
                                <p class="cmb2-metabox-description"><?php _e('Image cropping height', 'woocommerce-product-carousel-slider'); ?></p>
                            </div>
                        </div>                   

                </div>
            </div>
        </div>


            <div id="tab-2" class="tab-content">

                <div class="cmb2-wrap form-table">
                    <div id="cmb2-metabox" class="cmb2-metabox cmb-field-list">

                        <div class="cmb-row cmb-type-radio">
                            <div class="cmb-th">
                                <label for="wpcs_auto_play"><?php _e('Auto Play', 'woocommerce-product-carousel-slider'); ?></label>
                            </div>
                            <div class="cmb-td">
                                <ul class="cmb2-radio-list cmb2-list">  
                                    <li><input type="radio" class="cmb2-option" name="wpcs_auto_play" id="wpcs_auto_play1" value="true" <?php if($wpcs_auto_play=="true")  { echo "checked"; } else { echo "checked"; } ?>> <label for="wpcs_auto_play1"><?php _e('Yes', 'woocommerce-product-carousel-slider'); ?></label></li>
                                    <li><input type="radio" class="cmb2-option" name="wpcs_auto_play" id="wpcs_auto_play2" value="false" <?php if($wpcs_auto_play=="false") { echo "checked"; } else { echo ""; } ?>> <label for="wpcs_auto_play2"><?php _e('No', 'woocommerce-product-carousel-slider'); ?></label></li>
                                </ul>
                            <p class="cmb2-metabox-description"><?php _e('Slider would automatically play or not', 'woocommerce-product-carousel-slider'); ?></p>
                            </div>
                        </div>



                        <div class="cmb-row cmb-type-radio">
                            <div class="cmb-th">
                                <label for="wpcs_stop_on_hover"><?php _e('Stop on Hover', 'woocommerce-product-carousel-slider'); ?></label>
                            </div>
                            <div class="cmb-td">
                                <ul class="cmb2-radio-list cmb2-list">  
                                    <li><input type="radio" class="cmb2-option" name="wpcs_stop_on_hover" id="wpcs_stop_on_hover1" value="true" <?php if($wpcs_stop_on_hover=="true")  { echo "checked"; } else { echo "checked"; } ?>> <label for="wpcs_stop_on_hover1"><?php _e('Yes', 'woocommerce-product-carousel-slider'); ?></label></li>
                                    <li><input type="radio" class="cmb2-option" name="wpcs_stop_on_hover" id="wpcs_stop_on_hover2" value="false" <?php if($wpcs_stop_on_hover=="false") { echo "checked"; } else { echo ""; } ?>> <label for="wpcs_stop_on_hover2"><?php _e('No', 'woocommerce-product-carousel-slider'); ?></label></li>
                                </ul>
                            <p class="cmb2-metabox-description"><?php _e('Stop autoplay on mouse hover or not', 'woocommerce-product-carousel-slider'); ?></p>
                            </div>
                        </div>
            

                        <div class="cmb-row cmb-type-text-medium">
                            <div class="cmb-th">
                                <label for="wpcs_slide_speed"><?php _e('Slide Speed', 'woocommerce-product-carousel-slider'); ?></label>
                            </div>
                            <div class="cmb-td">
                                <input type="text" class="cmb2-text-small" name="wpcs_slide_speed" id="wpcs_slide_speed" value="<?php if(!empty($wpcs_slide_speed)) { echo $wpcs_slide_speed; } else { echo 900; } ?>">
                            </div>
                        </div> 


                        <div class="cmb-row cmb-type-text-medium">
                            <div class="cmb-th">
                                <label for="wpcs_items"><?php _e('Items', 'woocommerce-product-carousel-slider'); ?></label>
                            </div>
                            <div class="cmb-td">
                                <input type="text" class="cmb2-text-small" name="wpcs_items" id="wpcs_items" value="<?php if(!empty($wpcs_items)) { echo $wpcs_items; } else { echo 4; } ?>">
                                <p class="cmb2-metabox-description"><?php _e('Maximum amount of items to display at a time with the widest browser width.', 'woocommerce-product-carousel-slider'); ?></p>
                            </div>
                        </div>


                        <div class="cmb-row cmb-type-radio">
                            <div class="cmb-th">
                                <label for="wpcs_pagination"><?php _e('Pagination', 'woocommerce-product-carousel-slider'); ?></label>
                            </div>
                            <div class="cmb-td">
                                <ul class="cmb2-radio-list cmb2-list">  
                                    <li><input type="radio" class="cmb2-option" name="wpcs_pagination" id="wpcs_pagination1" value="false" <?php if($wpcs_pagination == "false") { echo "checked"; } else { echo "checked"; } ?>> <label for="wpcs_pagination1"><?php _e('No', 'woocommerce-product-carousel-slider'); ?></label></li>
                                    <li><input type="radio" class="cmb2-option" name="wpcs_pagination" id="wpcs_pagination2" value="true" <?php if($wpcs_pagination == "true") { echo "checked"; } else { echo ""; } ?>> <label for="wpcs_pagination2"><?php _e('Yes', 'woocommerce-product-carousel-slider'); ?></label></li>
                                </ul>
                            <p class="cmb2-metabox-description"><?php _e('Show pagination or not', 'woocommerce-product-carousel-slider'); ?></p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>




            <div id="tab-3" class="tab-content">
                <div class="cmb2-wrap form-table">
                    <div id="cmb2-metabox" class="cmb2-metabox cmb-field-list">

                        <div class="cmb-row cmb-type-text-medium">
                            <div class="cmb-th">
                                <label for="wpcs_header_title_font_size"><?php _e('Title Font Size', 'woocommerce-product-carousel-slider'); ?></label>
                            </div>
                            <div class="cmb-td">
                                <input type="text" class="cmb2-text-small" name="wpcs_header_title_font_size" id="wpcs_header_title_font_size" value="<?php if(!empty($wpcs_header_title_font_size)) { echo $wpcs_header_title_font_size; } ?>" placeholder="e.g. 20px">
                            </div>
                        </div>


                        <div class="cmb-row cmb-type-colorpicker">
                            <div class="cmb-th">
                                <label for="wpcs_header_title_font_color"><?php _e('Title Font Color', 'woocommerce-product-carousel-slider'); ?></label>
                            </div>
                            <div class="cmb-td">
                                <input type="text" class="cmb2-text-small" name="wpcs_header_title_font_color" id="wpcs_header_title_font_color" value="<?php if(!empty($wpcs_header_title_font_color)) { echo $wpcs_header_title_font_color; } else { echo "#303030"; } ?>">
                            </div>
                        </div>


                        <div class="cmb-row cmb-type-colorpicker">
                            <div class="cmb-th">
                                <label for="wpcs_nav_arrow_color"><?php _e('Navigational Arrow Color', 'woocommerce-product-carousel-slider'); ?></label>
                            </div>
                            <div class="cmb-td">
                                <input type="text" class="cmb2-text-small" name="wpcs_nav_arrow_color" id="wpcs_nav_arrow_color" value="<?php if(!empty($wpcs_nav_arrow_color)) { echo $wpcs_nav_arrow_color; } else { echo "#FFFFFF"; } ?>">
                            </div>
                        </div>


                        <div class="cmb-row cmb-type-colorpicker">
                            <div class="cmb-th">
                                <label for="wpcs_nav_arrow_bg_color"><?php _e('Navigational Arrow Background Color', 'woocommerce-product-carousel-slider'); ?></label>
                            </div>
                            <div class="cmb-td">
                                <input type="text" class="cmb2-text-small" name="wpcs_nav_arrow_bg_color" id="wpcs_nav_arrow_bg_color" value="<?php if(!empty($wpcs_nav_arrow_bg_color)) { echo $wpcs_nav_arrow_bg_color; } else { echo "#BBBBBB"; } ?>">
                            </div>
                        </div>


                        <div class="cmb-row cmb-type-colorpicker">
                            <div class="cmb-th">
                                <label for="wpcs_nav_arrow_hover_color"><?php _e('Navigational Arrow Hover Color', 'woocommerce-product-carousel-slider'); ?></label>
                            </div>
                            <div class="cmb-td">
                                <input type="text" class="cmb2-text-small" name="wpcs_nav_arrow_hover_color" id="wpcs_nav_arrow_hover_color" value="<?php if(!empty($wpcs_nav_arrow_hover_color)) { echo $wpcs_nav_arrow_hover_color; } else { echo "#FFFFFF"; } ?>">
                            </div>
                        </div>


                        <div class="cmb-row cmb-type-colorpicker">
                            <div class="cmb-th">
                                <label for="wpcs_nav_arrow_bg_hover_color"><?php _e('Navigational Arrow Background Hover Color', 'woocommerce-product-carousel-slider'); ?></label>
                            </div>
                            <div class="cmb-td">
                                <input type="text" class="cmb2-text-small" name="wpcs_nav_arrow_bg_hover_color" id="wpcs_nav_arrow_bg_hover_color" value="<?php if(!empty($wpcs_nav_arrow_bg_hover_color)) { echo $wpcs_nav_arrow_bg_hover_color; } else { echo "#9A9A9A"; } ?>">
                            </div>
                        </div>


                        <div class="cmb-row cmb-type-radio">
                            <div class="cmb-th">
                                <label for="wpcs_display_product_title"><?php _e('Display Product Title', 'woocommerce-product-carousel-slider'); ?></label>
                            </div>
                            <div class="cmb-td">
                                <ul class="cmb2-radio-list cmb2-list">  
                                    <li><input type="radio" class="cmb2-option" name="wpcs_display_product_title" id="wpcs_display_product_title1" value="yes" <?php if($wpcs_display_product_title == "yes")  { echo "checked"; } else { echo "checked"; } ?>> <label for="wpcs_display_product_title1"><?php _e('Yes', 'woocommerce-product-carousel-slider'); ?></label></li>
                                    <li><input type="radio" class="cmb2-option" name="wpcs_display_product_title" id="wpcs_display_product_title2" value="no" <?php if($wpcs_display_product_title == "no") { echo "checked"; } else { echo ""; } ?>> <label for="wpcs_display_product_title2"><?php _e('No', 'woocommerce-product-carousel-slider'); ?></label></li>
                                </ul>
                            </div>
                        </div>


                        <div class="cmb-row cmb-type-text-medium">
                            <div class="cmb-th">
                                <label for="wpcs_title_font_size"><?php _e('Product Title Font Size', 'woocommerce-product-carousel-slider'); ?></label>
                            </div>
                            <div class="cmb-td">
                                <input type="text" class="cmb2-text-small" name="wpcs_title_font_size" id="wpcs_title_font_size" value="<?php if(!empty($wpcs_title_font_size)) { echo $wpcs_title_font_size; } else { echo "16px"; } ?>">
                            </div>
                        </div>


                        <div class="cmb-row cmb-type-colorpicker">
                            <div class="cmb-th">
                                <label for="wpcs_title_font_color"><?php _e('Product Title Font Color', 'woocommerce-product-carousel-slider'); ?></label>
                            </div>
                            <div class="cmb-td">
                                <input type="text" class="cmb2-text-small" name="wpcs_title_font_color" id="wpcs_title_font_color" value="<?php if(!empty($wpcs_title_font_color)) { echo $wpcs_title_font_color; } else { echo "#444444"; } ?>">
                            </div>
                        </div>


                        <div class="cmb-row cmb-type-colorpicker">
                            <div class="cmb-th">
                                <label for="wpcs_title_hover_font_color"><?php _e('Product Title Hover Font Color', 'woocommerce-product-carousel-slider'); ?></label>
                            </div>
                            <div class="cmb-td">
                                <input type="text" class="cmb2-text-small" name="wpcs_title_hover_font_color" id="wpcs_title_hover_font_color" value="<?php if(!empty($wpcs_title_hover_font_color)) { echo $wpcs_title_hover_font_color; } else { echo "#000"; } ?>">
                            </div>
                        </div> 


                        <div class="cmb-row cmb-type-radio">
                            <div class="cmb-th">
                                <label for="wpcs_display_price"><?php _e('Display Product Price', 'woocommerce-product-carousel-slider'); ?></label>
                            </div>
                            <div class="cmb-td">
                                <ul class="cmb2-radio-list cmb2-list">  
                                    <li><input type="radio" class="cmb2-option" name="wpcs_display_price" id="wpcs_display_price1" value="yes" <?php if($wpcs_display_price == "yes")  { echo "checked"; } else { echo "checked"; } ?>> <label for="wpcs_display_price1"><?php _e('Yes', 'woocommerce-product-carousel-slider'); ?></label></li>
                                    <li><input type="radio" class="cmb2-option" name="wpcs_display_price" id="wpcs_display_price2" value="no" <?php if($wpcs_display_price == "no") { echo "checked"; } else { echo ""; } ?>> <label for="wpcs_display_price2"><?php _e('No', 'woocommerce-product-carousel-slider'); ?></label></li>
                                </ul>
                            </div>
                        </div>


                        <div class="cmb-row cmb-type-text-medium">
                            <div class="cmb-th">
                                <label for="wpcs_price_font_size"><?php _e('Product Price Font Size', 'woocommerce-product-carousel-slider'); ?></label>
                            </div>
                            <div class="cmb-td">
                                <input type="text" class="cmb2-text-small" name="wpcs_price_font_size" id="wpcs_price_font_size" value="<?php if(!empty($wpcs_price_font_size)) { echo $wpcs_price_font_size; } else { echo "18px"; } ?>">
                            </div>
                        </div>


                        <div class="cmb-row cmb-type-colorpicker">
                            <div class="cmb-th">
                                <label for="wpcs_price_font_color"><?php _e('Product Price Font Color', 'woocommerce-product-carousel-slider'); ?></label>
                            </div>
                            <div class="cmb-td">
                                <input type="text" class="cmb2-text-small" name="wpcs_price_font_color" id="wpcs_price_font_color" value="<?php if(!empty($wpcs_price_font_color)) { echo $wpcs_price_font_color; } else { echo "#444444"; } ?>">
                            </div>
                        </div>


                        <div class="cmb-row cmb-type-radio">
                            <div class="cmb-th">
                                <label for="wpcs_display_ratings"><?php _e('Display Product Ratings', 'woocommerce-product-carousel-slider'); ?></label>
                            </div>
                            <div class="cmb-td">
                                <ul class="cmb2-radio-list cmb2-list">  
                                    <li><input type="radio" class="cmb2-option" name="wpcs_display_ratings" id="wpcs_display_ratings1" value="yes" <?php if($wpcs_display_ratings == "yes")  { echo "checked"; } else { echo "checked"; } ?>> <label for="wpcs_display_ratings1"><?php _e('Yes', 'woocommerce-product-carousel-slider'); ?></label></li>
                                    <li><input type="radio" class="cmb2-option" name="wpcs_display_ratings" id="wpcs_display_ratings2" value="no" <?php if($wpcs_display_ratings == "no") { echo "checked"; } else { echo ""; } ?>> <label for="wpcs_display_ratings2"><?php _e('No', 'woocommerce-product-carousel-slider'); ?></label></li>
                                </ul>
                            </div>
                        </div>


                        <div class="cmb-row cmb-type-colorpicker">
                            <div class="cmb-th">
                                <label for="wpcs_ratings_color"><?php _e('Product Ratings Color', 'woocommerce-product-carousel-slider'); ?></label>
                            </div>
                            <div class="cmb-td">
                                <input type="text" class="cmb2-text-small" name="wpcs_ratings_color" id="wpcs_ratings_color" value="<?php if(!empty($wpcs_ratings_color)) { echo $wpcs_ratings_color; } else { echo "#FFC500"; } ?>">
                            </div>
                        </div>


                        <div class="cmb-row cmb-type-text-medium">
                            <div class="cmb-th">
                                <label for="wpcs_ratings_size"><?php _e('Product Ratings Size', 'woocommerce-product-carousel-slider'); ?></label>
                            </div>
                            <div class="cmb-td">
                                <input type="text" class="cmb2-text-small" name="wpcs_ratings_size" id="wpcs_ratings_size" value="<?php if(!empty($wpcs_ratings_size)) { echo $wpcs_ratings_size; } else { echo "1em"; } ?>">
                            </div>
                        </div>


                        <div class="cmb-row cmb-type-radio">
                            <div class="cmb-th">
                                <label for="wpcs_display_cart"><?php _e('Display "Add to Cart" button', 'woocommerce-product-carousel-slider'); ?></label>
                            </div>
                            <div class="cmb-td">
                                <ul class="cmb2-radio-list cmb2-list">  
                                    <li><input type="radio" class="cmb2-option" name="wpcs_display_cart" id="wpcs_display_cart1" value="yes" <?php if($wpcs_display_cart == "yes")  { echo "checked"; } else { echo "checked"; } ?>> <label for="wpcs_display_cart1"><?php _e('Yes', 'woocommerce-product-carousel-slider'); ?></label></li>
                                    <li><input type="radio" class="cmb2-option" name="wpcs_display_cart" id="wpcs_display_cart2" value="no" <?php if($wpcs_display_cart == "no") { echo "checked"; } else { echo ""; } ?>> <label for="wpcs_display_cart2"><?php _e('No', 'woocommerce-product-carousel-slider'); ?></label></li>
                                </ul>
                            </div>
                        </div>


                        <div class="cmb-row cmb-type-text-medium">
                            <div class="cmb-th">
                                <label for="wpcs_cart_font_size"><?php _e('"Add to Cart" Button Font Size', 'woocommerce-product-carousel-slider'); ?></label>
                            </div>
                            <div class="cmb-td">
                                <input type="text" class="cmb2-text-small" name="wpcs_cart_font_size" id="wpcs_cart_font_size" value="<?php if(!empty($wpcs_cart_font_size)) { echo $wpcs_cart_font_size; } else { echo "14px"; } ?>">
                            </div>
                        </div>


                        <div class="cmb-row cmb-type-colorpicker">
                            <div class="cmb-th">
                                <label for="wpcs_cart_font_color"><?php _e('"Add to Cart" Font Color', 'woocommerce-product-carousel-slider'); ?></label>
                            </div>
                            <div class="cmb-td">
                                <input type="text" class="cmb2-text-small" name="wpcs_cart_font_color" id="wpcs_cart_font_color" value="<?php if(!empty($wpcs_cart_font_color)) { echo $wpcs_cart_font_color; } else { echo "#ffffff"; } ?>">
                            </div>
                        </div> 


                        <div class="cmb-row cmb-type-colorpicker">
                            <div class="cmb-th">
                                <label for="wpcs_cart_bg_color"><?php _e('"Add to Cart" Button Background Color', 'woocommerce-product-carousel-slider'); ?></label>
                            </div>
                            <div class="cmb-td">
                                <input type="text" class="cmb2-text-small" name="wpcs_cart_bg_color" id="wpcs_cart_bg_color" value="<?php if(!empty($wpcs_cart_bg_color)) { echo $wpcs_cart_bg_color; } else { echo "#BBBBBB"; } ?>">
                            </div>
                        </div> 


                        <div class="cmb-row cmb-type-colorpicker">
                            <div class="cmb-th">
                                <label for="wpcs_cart_button_hover_color"><?php _e('"Add to Cart" Button Hover Background Color', 'woocommerce-product-carousel-slider'); ?></label>
                            </div>
                            <div class="cmb-td">
                                <input type="text" class="cmb2-text-small" name="wpcs_cart_button_hover_color" id="wpcs_cart_button_hover_color" value="<?php if(!empty($wpcs_cart_button_hover_color)) { echo $wpcs_cart_button_hover_color; } else { echo "#9A9A9A"; } ?>">
                            </div>
                        </div>


                        <div class="cmb-row cmb-type-colorpicker">
                            <div class="cmb-th">
                                <label for="wpcs_cart_button_hover_font_color"><?php _e('"Add to Cart" Hover Font Color', 'woocommerce-product-carousel-slider'); ?></label>
                            </div>
                            <div class="cmb-td">
                                <input type="text" class="cmb2-text-small" name="wpcs_cart_button_hover_font_color" id="wpcs_cart_button_hover_font_color" value="<?php if(!empty($wpcs_cart_button_hover_font_color)) { echo $wpcs_cart_button_hover_font_color; } else { echo "#ffffff"; } ?>">
                            </div>
                        </div>

                    </div>
                </div>
            </div>


        </div> <!-- end tab -->
    </div> <!-- end tabs-container -->

<div class="wpcs_shortcode">
    <h2><?php _e('Shortcode', 'woocommerce-product-carousel-slider'); ?> </h2> 
    <p><?php _e('Use following shortcode to display the Carousel Slider anywhere:', 'woocommerce-product-carousel-slider'); ?></p>
    <textarea cols="25" rows="1" onClick="this.select();" >[wpcs <?php echo 'id="'.$post->ID.'"';?>]</textarea> <br />

    <p><?php _e('If you need to put the shortcode in code/theme file, use this:', 'woocommerce-product-carousel-slider'); ?></p>
    <textarea cols="54" rows="1" onClick="this.select();" ><?php echo '<?php echo do_shortcode("[wpcs id='; echo "'".$post->ID."']"; echo '"); ?>'; ?></textarea> </p>
</div>
<?php }

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function wpcs_save_meta_box_data( $post_id ) {
/*
     * We need to verify this came from our screen and with proper authorization,
     * because the save_post action can be triggered at other times.
     */

    // Check if our nonce is set.
    if ( ! isset( $_POST['wpcs_meta_box_nonce'] ) ) {
        return;
    }

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $_POST['wpcs_meta_box_nonce'], 'wpcs_save_meta_box_data' ) ) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check the user's permissions.
    if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return;
        }

    } else {

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }

    $wpcs_display_header_value = "";
    $wpcs_display_navigation_arrows_value = "";
    $wpcs_title_value = "";
    $wpcs_total_products_value = "";
    $wpcs_products_type_value = ""; 
    $wpcs_img_crop_value = "";
    $wpcs_crop_image_width_value = "";
    $wpcs_crop_image_height_value = "";
    $wpcs_auto_play_value  = "";
    $wpcs_stop_on_hover_value  = "";
    $wpcs_slide_speed_value  = "";
    $wpcs_items_value  = "";
    $wpcs_pagination_value  = "";
    $wpcs_header_title_font_size_value = "";
    $wpcs_header_title_font_color_value = "";
    $wpcs_nav_arrow_color_value = "";
    $wpcs_nav_arrow_bg_color_value = "";
    $wpcs_nav_arrow_hover_color_value = "";
    $wpcs_nav_arrow_bg_hover_color_value = "";
    $wpcs_display_product_title_value = "";
    $wpcs_title_font_size_value = "";
    $wpcs_title_font_color_value = "";
    $wpcs_title_hover_font_color_value = "";
    $wpcs_display_price_value = "";
    $wpcs_price_font_size_value = "";
    $wpcs_price_font_color_value = "";
    $wpcs_display_ratings_value = "";
    $wpcs_ratings_color_value = "";
    $wpcs_ratings_size_value = "";
    $wpcs_display_cart_value = "";
    $wpcs_cart_font_size_value = "";
    $wpcs_cart_font_color_value = "";
    $wpcs_cart_bg_color_value = "";
    $wpcs_cart_button_hover_color_value = "";
    $wpcs_cart_button_hover_font_color_value = "";
    $wpcs_header1_title_bg_color_value = "";
    $themeB_border_color_value = "";
    $themeB_border_hover_color_value = "";
    $themeC_border_hover_color_value = "";


    if(isset($_POST["wpcs_display_header"]))
    {
        $wpcs_display_header_value = sanitize_text_field( $_POST["wpcs_display_header"] );
    }   
    update_post_meta($post_id, "wpcs_display_header", $wpcs_display_header_value);


    if(isset($_POST["wpcs_display_navigation_arrows"]))
    {
        $wpcs_display_navigation_arrows_value = sanitize_text_field( $_POST["wpcs_display_navigation_arrows"] );
    }   
    update_post_meta($post_id, "wpcs_display_navigation_arrows", $wpcs_display_navigation_arrows_value);


    if(isset($_POST["wpcs_title"]))
    {
        $wpcs_title_value = sanitize_text_field( $_POST["wpcs_title"] );
    }   
    update_post_meta($post_id, "wpcs_title", $wpcs_title_value);


    if(isset($_POST["wpcs_total_products"]))
    {
        $wpcs_total_products_value = sanitize_text_field( $_POST["wpcs_total_products"] );
    }   
    update_post_meta($post_id, "wpcs_total_products", $wpcs_total_products_value);


    if(isset($_POST["wpcs_products_type"]))
    {
        $wpcs_products_type_value = sanitize_text_field( $_POST["wpcs_products_type"] );
    }   
    update_post_meta($post_id, "wpcs_products_type", $wpcs_products_type_value);


    if(isset($_POST["wpcs_img_crop"]))
    {
        $wpcs_img_crop_value = sanitize_text_field( $_POST["wpcs_img_crop"] );
    }   
    update_post_meta($post_id, "wpcs_img_crop", $wpcs_img_crop_value);


    if(isset($_POST["wpcs_crop_image_width"]))
    {
        $wpcs_crop_image_width_value = sanitize_text_field( $_POST["wpcs_crop_image_width"] );
    }   
    update_post_meta($post_id, "wpcs_crop_image_width", $wpcs_crop_image_width_value);


    if(isset($_POST["wpcs_crop_image_height"]))
    {
        $wpcs_crop_image_height_value = sanitize_text_field( $_POST["wpcs_crop_image_height"] );
    }   
    update_post_meta($post_id, "wpcs_crop_image_height", $wpcs_crop_image_height_value);


    if(isset($_POST["wpcs_auto_play"]))
    {
        $wpcs_auto_play_value = sanitize_text_field( $_POST["wpcs_auto_play"] );
    }   
    update_post_meta($post_id, "wpcs_auto_play", $wpcs_auto_play_value);


    if(isset($_POST["wpcs_stop_on_hover"]))
    {
        $wpcs_stop_on_hover_value = sanitize_text_field( $_POST["wpcs_stop_on_hover"] );
    }   
    update_post_meta($post_id, "wpcs_stop_on_hover", $wpcs_stop_on_hover_value);


    if(isset($_POST["wpcs_slide_speed"]))
    {
        $wpcs_slide_speed_value = sanitize_text_field( $_POST["wpcs_slide_speed"] );
    }   
    update_post_meta($post_id, "wpcs_slide_speed", $wpcs_slide_speed_value);


    if(isset($_POST["wpcs_items"]))
    {
        $wpcs_items_value = sanitize_text_field( $_POST["wpcs_items"] );
    }   
    update_post_meta($post_id, "wpcs_items", $wpcs_items_value);


    if(isset($_POST["wpcs_pagination"]))
    {
        $wpcs_pagination_value = sanitize_text_field( $_POST["wpcs_pagination"] );
    }   
    update_post_meta($post_id, "wpcs_pagination", $wpcs_pagination_value);


    if(isset($_POST["wpcs_header_title_font_size"]))
    {
        $wpcs_header_title_font_size_value = sanitize_text_field( $_POST["wpcs_header_title_font_size"] );
    }   
    update_post_meta($post_id, "wpcs_header_title_font_size", $wpcs_header_title_font_size_value);


    if(isset($_POST["wpcs_header_title_font_color"]))
    {
        $wpcs_header_title_font_color_value = sanitize_text_field( $_POST["wpcs_header_title_font_color"] );
    }   
    update_post_meta($post_id, "wpcs_header_title_font_color", $wpcs_header_title_font_color_value);


    if(isset($_POST["wpcs_nav_arrow_color"]))
    {
        $wpcs_nav_arrow_color_value = sanitize_text_field( $_POST["wpcs_nav_arrow_color"] );
    }   
    update_post_meta($post_id, "wpcs_nav_arrow_color", $wpcs_nav_arrow_color_value);


    if(isset($_POST["wpcs_nav_arrow_bg_color"]))
    {
        $wpcs_nav_arrow_bg_color_value = sanitize_text_field( $_POST["wpcs_nav_arrow_bg_color"] );
    }   
    update_post_meta($post_id, "wpcs_nav_arrow_bg_color", $wpcs_nav_arrow_bg_color_value);


    if(isset($_POST["wpcs_nav_arrow_hover_color"]))
    {
        $wpcs_nav_arrow_hover_color_value = sanitize_text_field( $_POST["wpcs_nav_arrow_hover_color"] );
    }   
    update_post_meta($post_id, "wpcs_nav_arrow_hover_color", $wpcs_nav_arrow_hover_color_value);


    if(isset($_POST["wpcs_nav_arrow_bg_hover_color"]))
    {
        $wpcs_nav_arrow_bg_hover_color_value = sanitize_text_field( $_POST["wpcs_nav_arrow_bg_hover_color"] );
    }   
    update_post_meta($post_id, "wpcs_nav_arrow_bg_hover_color", $wpcs_nav_arrow_bg_hover_color_value);


    if(isset($_POST["wpcs_display_product_title"]))
    {
        $wpcs_display_product_title_value = sanitize_text_field( $_POST["wpcs_display_product_title"] );
    }   
    update_post_meta($post_id, "wpcs_display_product_title", $wpcs_display_product_title_value);


    if(isset($_POST["wpcs_title_font_size"]))
    {
        $wpcs_title_font_size_value = sanitize_text_field( $_POST["wpcs_title_font_size"] );
    }   
    update_post_meta($post_id, "wpcs_title_font_size", $wpcs_title_font_size_value);


    if(isset($_POST["wpcs_title_font_color"]))
    {
        $wpcs_title_font_color_value = sanitize_text_field( $_POST["wpcs_title_font_color"] );
    }   
    update_post_meta($post_id, "wpcs_title_font_color", $wpcs_title_font_color_value);


    if(isset($_POST["wpcs_title_hover_font_color"]))
    {
        $wpcs_title_hover_font_color_value = sanitize_text_field( $_POST["wpcs_title_hover_font_color"] );
    }   
    update_post_meta($post_id, "wpcs_title_hover_font_color", $wpcs_title_hover_font_color_value);


    if(isset($_POST["wpcs_display_price"]))
    {
        $wpcs_display_price_value = sanitize_text_field( $_POST["wpcs_display_price"] );
    }   
    update_post_meta($post_id, "wpcs_display_price", $wpcs_display_price_value);


    if(isset($_POST["wpcs_price_font_size"]))
    {
        $wpcs_price_font_size_value = sanitize_text_field( $_POST["wpcs_price_font_size"] );
    }   
    update_post_meta($post_id, "wpcs_price_font_size", $wpcs_price_font_size_value);


    if(isset($_POST["wpcs_price_font_color"]))
    {
        $wpcs_price_font_color_value = sanitize_text_field( $_POST["wpcs_price_font_color"] );
    }   
    update_post_meta($post_id, "wpcs_price_font_color", $wpcs_price_font_color_value);


    if(isset($_POST["wpcs_display_ratings"]))
    {
        $wpcs_display_ratings_value = sanitize_text_field( $_POST["wpcs_display_ratings"] );
    }   
    update_post_meta($post_id, "wpcs_display_ratings", $wpcs_display_ratings_value);


    if(isset($_POST["wpcs_ratings_color"]))
    {
        $wpcs_ratings_color_value = sanitize_text_field( $_POST["wpcs_ratings_color"] );
    }   
    update_post_meta($post_id, "wpcs_ratings_color", $wpcs_ratings_color_value);


    if(isset($_POST["wpcs_ratings_size"]))
    {
        $wpcs_ratings_size_value = sanitize_text_field( $_POST["wpcs_ratings_size"] );
    }   
    update_post_meta($post_id, "wpcs_ratings_size", $wpcs_ratings_size_value);


    if(isset($_POST["wpcs_display_cart"]))
    {
        $wpcs_display_cart_value = sanitize_text_field( $_POST["wpcs_display_cart"] );
    }   
    update_post_meta($post_id, "wpcs_display_cart", $wpcs_display_cart_value);


    if(isset($_POST["wpcs_cart_font_size"]))
    {
        $wpcs_cart_font_size_value = sanitize_text_field( $_POST["wpcs_cart_font_size"] );
    }   
    update_post_meta($post_id, "wpcs_cart_font_size", $wpcs_cart_font_size_value);


    if(isset($_POST["wpcs_cart_font_color"]))
    {
        $wpcs_cart_font_color_value = sanitize_text_field( $_POST["wpcs_cart_font_color"] );
    }   
    update_post_meta($post_id, "wpcs_cart_font_color", $wpcs_cart_font_color_value);


    if(isset($_POST["wpcs_cart_bg_color"]))
    {
        $wpcs_cart_bg_color_value = sanitize_text_field( $_POST["wpcs_cart_bg_color"] );
    }   
    update_post_meta($post_id, "wpcs_cart_bg_color", $wpcs_cart_bg_color_value);


    if(isset($_POST["wpcs_cart_button_hover_color"]))
    {
        $wpcs_cart_button_hover_color_value = sanitize_text_field( $_POST["wpcs_cart_button_hover_color"] );
    }   
    update_post_meta($post_id, "wpcs_cart_button_hover_color", $wpcs_cart_button_hover_color_value);


    if(isset($_POST["wpcs_cart_button_hover_font_color"]))
    {
        $wpcs_cart_button_hover_font_color_value = sanitize_text_field( $_POST["wpcs_cart_button_hover_font_color"] );
    }   
    update_post_meta($post_id, "wpcs_cart_button_hover_font_color", $wpcs_cart_button_hover_font_color_value);


    if(isset($_POST["wpcs_header1_title_bg_color"]))
    {
        $wpcs_header1_title_bg_color_value = sanitize_text_field( $_POST["wpcs_header1_title_bg_color"] );
    }   
    update_post_meta($post_id, "wpcs_header1_title_bg_color", $wpcs_header1_title_bg_color_value);


    if(isset($_POST["themeB_border_color"]))
    {
        $themeB_border_color_value = sanitize_text_field( $_POST["themeB_border_color"] );
    }   
    update_post_meta($post_id, "themeB_border_color", $themeB_border_color_value);


    if(isset($_POST["themeB_border_hover_color"]))
    {
        $themeB_border_hover_color_value = sanitize_text_field( $_POST["themeB_border_hover_color"] );
    }   
    update_post_meta($post_id, "themeB_border_hover_color", $themeB_border_hover_color_value);


    if(isset($_POST["themeC_border_hover_color"]))
    {
        $themeC_border_hover_color_value = sanitize_text_field( $_POST["themeC_border_hover_color"] );
    }   
    update_post_meta($post_id, "themeC_border_hover_color", $themeC_border_hover_color_value);

}
add_action( 'save_post', 'wpcs_save_meta_box_data' );