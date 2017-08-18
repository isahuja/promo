<?php

/**
 * Protect direct access
 */
if ( ! defined( 'ABSPATH' ) ) die( WPCS_HACK_MSG );

/**
 * Registers Shortcode
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )  { 

	function woo_product_carousel_shortcode($atts, $content = null) {

	ob_start();

		$atts = shortcode_atts(
			array(
				'id' => "",

				), $atts);

	wp_enqueue_style( 'wpcs-owl-carousel-style' );
	wp_enqueue_style( 'wpcs-owl-theme-style' );
	wp_enqueue_style( 'wpcs-owl-transitions' );
	wp_enqueue_style( 'wpcs-font-awesome' );
	wp_enqueue_style( 'wpcs-custom-style' );
	wp_enqueue_script( 'wpcs-owl-carousel-js' );
	wp_enqueue_script( 'wpcs-custom-js' );

	$post_id = $atts['id'];

	$random_carousel_wrapper_id = rand();
	$random_next_prev_id = rand();

    $wpcs_display_header = get_post_meta( $post_id, 'wpcs_display_header', true );
    $wpcs_display_navigation_arrows = get_post_meta( $post_id, 'wpcs_display_navigation_arrows', true );
    $wpcs_title = get_post_meta( $post_id, 'wpcs_title', true );
    $wpcs_products_type = get_post_meta( $post_id, 'wpcs_products_type', true );
    $wpcs_total_products = get_post_meta( $post_id, 'wpcs_total_products', true );
    $wpcs_img_crop = get_post_meta( $post_id, 'wpcs_img_crop', true );
    $wpcs_crop_image_width = get_post_meta( $post_id, 'wpcs_crop_image_width', true );
    $wpcs_crop_image_height = get_post_meta( $post_id, 'wpcs_crop_image_height', true );

    $wpcs_auto_play = get_post_meta( $post_id, 'wpcs_auto_play', true );
    $wpcs_stop_on_hover = get_post_meta( $post_id, 'wpcs_stop_on_hover', true );
    $wpcs_slide_speed = get_post_meta( $post_id, 'wpcs_slide_speed', true );
    $wpcs_items = get_post_meta( $post_id, 'wpcs_items', true );
    $wpcs_pagination = get_post_meta( $post_id, 'wpcs_pagination', true );

    $wpcs_header_title_font_size = get_post_meta( $post_id, 'wpcs_header_title_font_size', true );
    $wpcs_header_title_font_color = get_post_meta( $post_id, 'wpcs_header_title_font_color', true );
    $wpcs_nav_arrow_color = get_post_meta( $post_id, 'wpcs_nav_arrow_color', true );
    $wpcs_nav_arrow_bg_color = get_post_meta( $post_id, 'wpcs_nav_arrow_bg_color', true );
    $wpcs_nav_arrow_hover_color = get_post_meta( $post_id, 'wpcs_nav_arrow_hover_color', true );
    $wpcs_nav_arrow_bg_hover_color = get_post_meta( $post_id, 'wpcs_nav_arrow_bg_hover_color', true );
    $wpcs_display_product_title = get_post_meta( $post_id, 'wpcs_display_product_title', true );    
    $wpcs_title_font_size = get_post_meta( $post_id, 'wpcs_title_font_size', true );
    $wpcs_title_font_color = get_post_meta( $post_id, 'wpcs_title_font_color', true );
    $wpcs_display_price = get_post_meta( $post_id, 'wpcs_display_price', true );
    $wpcs_price_font_size = get_post_meta( $post_id, 'wpcs_price_font_size', true );
    $wpcs_display_ratings = get_post_meta( $post_id, 'wpcs_display_ratings', true );
    $wpcs_ratings_color = get_post_meta( $post_id, 'wpcs_ratings_color', true );
    $wpcs_ratings_size = get_post_meta( $post_id, 'wpcs_ratings_size', true );
    $wpcs_display_cart = get_post_meta( $post_id, 'wpcs_display_cart', true );
	$wpcs_title_hover_font_color = get_post_meta( $post_id, 'wpcs_title_hover_font_color', true );
	$wpcs_price_font_color = get_post_meta( $post_id, 'wpcs_price_font_color', true );
	$wpcs_cart_font_size = get_post_meta( $post_id, 'wpcs_cart_font_size', true );
	$wpcs_cart_font_color = get_post_meta( $post_id, 'wpcs_cart_font_color', true );
	$wpcs_cart_bg_color = get_post_meta( $post_id, 'wpcs_cart_bg_color', true );
	$wpcs_cart_button_hover_color = get_post_meta( $post_id, 'wpcs_cart_button_hover_color', true );
	$wpcs_cart_button_hover_font_color  = get_post_meta( $post_id, 'wpcs_cart_button_hover_font_color ', true );

		$common_args = array(
			'post_type'      => 'product', 
			'posts_per_page' => $wpcs_total_products, 
			'post_status'    => 'publish',
			'meta_query'     => array(
									array(
									'key' => '_stock_status',
									'value' => 'outofstock',
									'compare' => 'NOT IN'
									)
								)
		);

		if ($wpcs_products_type == "latest") {
			$args = $common_args;
		}

		elseif ($wpcs_products_type == "older") {
			$older_args = array(
				'orderby'     => 'date',
				'order'       => 'ASC'
				);
			$args = array_merge($common_args, $older_args);
		}

		elseif ($wpcs_products_type == "featured") {
            $meta_query  = WC()->query->get_meta_query();
            $tax_query   = WC()->query->get_tax_query();

            $tax_query[] = array(
                'taxonomy' => 'product_visibility',
                'field'    => 'name',
                'terms'    => 'featured',
                'operator' => 'IN',
            );



            $featured_args = array(
                'meta_query' => $meta_query,
                'tax_query' => $tax_query,
            );
            $args = array_merge($common_args, $featured_args);
        }

		else {
			 $args = $common_args;
		}


	    $loop = new WP_Query( $args );
	    if ( $loop->have_posts() ): ?>
	    <div class="wpcs_product_carousel_slider">

		<style type="text/css">

			.wpcs_product_carousel_slider .another_carousel_header i.prev-<?php echo $random_next_prev_id; ?>, .wpcs_product_carousel_slider .another_carousel_header i.next-<?php echo $random_next_prev_id; ?> {
			    background-color: <?php echo $wpcs_nav_arrow_bg_color; ?>;
			    color: <?php echo $wpcs_nav_arrow_color; ?>;
			}
			.wpcs_product_carousel_slider .another_carousel_header i.fa-angle-left.prev-<?php echo $random_next_prev_id; ?>:hover, .wpcs_product_carousel_slider .another_carousel_header i.fa-angle-right.next-<?php echo $random_next_prev_id; ?>:hover {
			    background-color: <?php echo $wpcs_nav_arrow_bg_hover_color; ?>;
			    color: <?php echo $wpcs_nav_arrow_hover_color; ?>;
			}
			#woo-product-carousel-wrapper-<?php echo $random_carousel_wrapper_id; ?> .owl-item .item h4.product_name { 
				font-size: <?php echo $wpcs_title_font_size; ?>; 
			}
			#woo-product-carousel-wrapper-<?php echo $random_carousel_wrapper_id; ?> .owl-item .item h4.product_name a { 
				color: <?php echo $wpcs_title_font_color; ?>; 
			}
			#woo-product-carousel-wrapper-<?php echo $random_carousel_wrapper_id; ?> .owl-item .item h4.product_name a:hover {
		    	color: <?php echo $wpcs_title_hover_font_color; ?>;
			}
			#woo-product-carousel-wrapper-<?php echo $random_carousel_wrapper_id; ?> .owl-item .item .price {
				font-size: <?php echo $wpcs_price_font_size; ?>;
			}
			#woo-product-carousel-wrapper-<?php echo $random_carousel_wrapper_id; ?> .price {
			    color: <?php echo $wpcs_price_font_color; ?>;
			}
			#woo-product-carousel-wrapper-<?php echo $random_carousel_wrapper_id; ?> .price ins {
			    color: <?php echo $wpcs_price_font_color; ?>;
			}
			#woo-product-carousel-wrapper-<?php echo $random_carousel_wrapper_id; ?> .wpcs_rating .woocommerce-product-rating {
    			color: <?php echo $wpcs_ratings_color; ?>;
			}
			#woo-product-carousel-wrapper-<?php echo $random_carousel_wrapper_id; ?> .woocommerce .star-rating {
				font-size: <?php echo $wpcs_ratings_size; ?>;
			}
			#woo-product-carousel-wrapper-<?php echo $random_carousel_wrapper_id; ?> .owl-item .item .cart .add_to_cart_button, #woo-product-carousel-wrapper-<?php echo $random_carousel_wrapper_id; ?> .owl-item .item .cart a.added_to_cart.wc-forward {
			    color: <?php echo $wpcs_cart_font_color; ?>;
			    background-color: <?php echo $wpcs_cart_bg_color; ?>;
			    border-color: <?php echo $wpcs_cart_bg_color; ?>;
			    font-size: <?php echo $wpcs_cart_font_size; ?>;
			}
			#woo-product-carousel-wrapper-<?php echo $random_carousel_wrapper_id; ?> .owl-item .item .cart .add_to_cart_button:hover, #woo-product-carousel-wrapper-<?php echo $random_carousel_wrapper_id; ?> .owl-item .item .cart a.added_to_cart.wc-forward:hover {
			    background-color: <?php echo $wpcs_cart_button_hover_color; ?>;
			    border-color: <?php echo $wpcs_cart_button_hover_color; ?>;
			    color: <?php echo $wpcs_cart_button_hover_font_color; ?>;
			}

		</style>

	    <?php
	    if($wpcs_display_header == "yes") { ?>
				<div class="another_carousel_header">
					<div class="title" style="font-size: <?php echo $wpcs_header_title_font_size; ?>; color: <?php echo $wpcs_header_title_font_color; ?>;"><?php echo $wpcs_title; ?></div>
					<?php if ($wpcs_display_navigation_arrows == 'yes') { ?>
						<i class="fa fa-angle-left prev-<?php echo $random_next_prev_id; ?>"></i>
						<i class="fa fa-angle-right next-<?php echo $random_next_prev_id; ?>"></i>
					<?php } ?>
				</div>
	        <?php 
	    }

    	else { ?>
			<div class="another_carousel_header">
					<?php if ($wpcs_display_navigation_arrows == 'yes') { ?>
						<i class="fa fa-angle-left prev-<?php echo $random_next_prev_id; ?>"></i>
						<i class="fa fa-angle-right next-<?php echo $random_next_prev_id; ?>"></i>
					<?php } ?>
			</div>
	    <?php } ?>

		    <div id="woo-product-carousel-wrapper-<?php echo $random_carousel_wrapper_id; ?>" class="owl-carousel">
		    <?php while ( $loop->have_posts() ) : $loop->the_post(); global $post, $product; ?>
		        <div class="item">
		        <?php 
				$wpcs_thumb = get_post_thumbnail_id();
				$wpcs_img_url = wp_get_attachment_url( $wpcs_thumb,'full' );
				$wpcs_img = aq_resize( $wpcs_img_url, $wpcs_crop_image_width, $wpcs_crop_image_height, true );		        	
		    	?>
		        	<div class="product_container">
		        		<div class="product_image_container">
				            <a id="id-<?php the_id(); ?>" class="product_thumb_link" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
				            	<?php
					            	if($wpcs_img_crop == 'yes') {
					            	    if (has_post_thumbnail( $loop->post->ID )) { echo '<img src="'.$wpcs_img.'" class="wpcs-thum" alt="'.get_the_title().'" />'; } else { echo '<img src="'.woocommerce_placeholder_img_src().'" alt="Placeholder" />'; } 
									} else {
										if (has_post_thumbnail( $loop->post->ID )) { echo get_the_post_thumbnail($loop->post->ID, 'wpcs-thum'); } else { echo '<img src="'.woocommerce_placeholder_img_src().'" alt="Placeholder" />'; } 
									}
				            	?>
				            </a>
			        	</div>
			            <div class="caption">
			            	<?php 
			            	if ($wpcs_display_product_title == 'yes') { ?>			               
				              <h4 class="product_name"><a id="id-<?php the_id(); ?>" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h4>
				            <?php } 
				            if ($wpcs_display_price == 'yes') { ?>
				              <span class="price"><?php echo $product->get_price_html(); ?></span>
				            <?php }
							if ($wpcs_display_ratings == 'yes') { 
							$rating = (($product->get_average_rating()/5)*100); ?>
				              <div class="wpcs_rating woocommerce"><div class="woocommerce-product-rating"><div class="star-rating" title="<?php echo $rating; ?>%"><span style="width: <?php echo $rating; ?>%;"></span></div></div></div>
				            <?php } 
				            if ($wpcs_display_cart == 'yes') { ?>
				              <div class="cart"><?php echo do_shortcode('[add_to_cart id="'.get_the_ID().'"]') ?></div>
			           		<?php } ?>
			            </div>
		            </div> 

		        </div>
		    <?php endwhile; wp_reset_postdata(); ?>
		    </div> <!-- End woo-product-carousel-wrapper -->
		    <?php else: 
			_e('No products found', 'woocommerce-product-carousel-slider');
		    endif; ?>
	    </div> <!-- End wpcs_product_carousel_slider -->

		<?php echo '<script type="text/javascript"> 
			jQuery(document).ready(function($) {

			 		var owl = $("#woo-product-carousel-wrapper-'.$random_carousel_wrapper_id.'");

					owl.owlCarousel({
					      autoPlay : '.$wpcs_auto_play.',
					      items : '.$wpcs_items.',
					      itemsDesktop : [1199,'.$wpcs_items.'],
					      slideSpeed : '.$wpcs_slide_speed.',
					      stopOnHover: '.$wpcs_stop_on_hover.',					   
	      	              pagination : '.$wpcs_pagination.'
					});

					$(".next-'.$random_next_prev_id.'").click(function(){
					  owl.trigger("owl.next");
					});

					$(".prev-'.$random_next_prev_id.'").click(function(){
					  owl.trigger("owl.prev");
					});

			});
		</script>';

	$carousel_content = ob_get_clean();
	return $carousel_content;

		}

	add_shortcode("wpcs", "woo_product_carousel_shortcode");

}
