<?php

/**
 * Wp in Progress
 * 
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available at this URL: http://www.gnu.org/licenses/gpl-3.0.txt
 */

/*-----------------------------------------------------------------------------------*/
/* Woocommerce is active */
/*-----------------------------------------------------------------------------------*/ 

if ( ! function_exists( 'bazaarlite_is_woocommerce_active' ) ) {
	
	function bazaarlite_is_woocommerce_active( $type = "" ) {
	
        global $woocommerce;

        if ( isset( $woocommerce ) ) {
			
			if ( !$type || call_user_func($type) ) {
            
				return true;
			
			}
			
		}
	
	}

}

/*-----------------------------------------------------------------------------------*/
/* THEME SETTINGS */
/*-----------------------------------------------------------------------------------*/ 

if (!function_exists('bazaarlite_setting')) {

	function bazaarlite_setting($id, $default = "" ) {

		$bazaarlite_setting = get_theme_mod($id);
			
		if( $bazaarlite_setting ):
		
			return $bazaarlite_setting;
		
		else:
		
			return $default;
		
		endif;
	
	}

}

/*-----------------------------------------------------------------------------------*/
/* POST META */
/*-----------------------------------------------------------------------------------*/ 

if (!function_exists('bazaarlite_postmeta')) {

	function bazaarlite_postmeta($id) {
	
		global $post, $wp_query;

		$content_ID = $post->ID;
	
		if( bazaarlite_is_woocommerce_active('is_shop') ) {
	
			$content_ID = get_option('woocommerce_shop_page_id');
	
		}

		$val = get_post_meta( $content_ID , $id, TRUE);
		
		if( isset($val) ) {
			
			return $val;
			
		} else {
				
			return '';
			
		}
		
	}

}

/*-----------------------------------------------------------------------------------*/
/* THUMBNAILS */
/*-----------------------------------------------------------------------------------*/         

if (!function_exists('bazaarlite_get_width')) {

	function bazaarlite_get_width() {
		
		if ( bazaarlite_setting('wip_screen3') ):
			return bazaarlite_setting('wip_screen3');
		else:
			return "940";
		endif;
	
	}

}

if (!function_exists('bazaarlite_get_height')) {

	function bazaarlite_get_height() {
		
		if ( bazaarlite_setting('wip_thumbnails') ):
			return bazaarlite_setting('wip_thumbnails');
		else:
			return "600";
		endif;
	
	}

}

/*-----------------------------------------------------------------------------------*/
/*RESPONSIVE EMBED */
/*-----------------------------------------------------------------------------------*/ 

if (!function_exists('bazaarlite_embed_html')) {
	
	function bazaarlite_embed_html( $html ) {
		return '<div class="embed-container">' . $html . '</div>';
	}
	 
	add_filter( 'embed_oembed_html', 'bazaarlite_embed_html', 10, 3 );
	add_filter( 'video_embed_html', 'bazaarlite_embed_html' );
	
}

/*-----------------------------------------------------------------------------------*/
/* POST CLASS */
/*-----------------------------------------------------------------------------------*/   

if (!function_exists('bazaarlite_post_class')) {

	function bazaarlite_post_class($classes) {	

		$masonry  = 'post-container masonry-element col-md-4';
		$standard = 'post-container col-md-12';

		if ( bazaarlite_is_woocommerce_active('is_cart') ) :
		
			$classes[] = 'woocommerce_cart_page';
				
		endif;

		if ( !bazaarlite_is_single() && is_home() ) {
			
			if ( !bazaarlite_setting('wip_home') || bazaarlite_setting('wip_home') == "masonry" ) {

				$classes[] = $masonry;

			} else {

				$classes[] = $standard;

			}
			
		} else if ( !bazaarlite_is_single() && bazaarlite_get_archive_title() ) {
			
			if ( !bazaarlite_setting('wip_category_layout') || bazaarlite_setting('wip_category_layout') == "masonry" ) {

				$classes[] = $masonry;

			} else {

				$classes[] = $standard;

			}
			
		} else if ( !bazaarlite_is_single() && is_search() ) {
			
			if ( !bazaarlite_setting('wip_search_layout') || bazaarlite_setting('wip_search_layout') == "masonry" ) {

				$classes[] = $masonry;

			} else {

				$classes[] = $standard;

			}
			
		} else if ( bazaarlite_is_single() && !bazaarlite_is_woocommerce_active('is_product') ) {

			$classes[] = 'post-container col-md-12';

		}
	
		return $classes;
		
	}
	
	add_filter('post_class', 'bazaarlite_post_class');

}

/*-----------------------------------------------------------------------------------*/
/* GET ARCHIVE TITLE */
/*-----------------------------------------------------------------------------------*/ 

if (!function_exists('bazaarlite_get_archive_title')) {

	function bazaarlite_get_archive_title() {
		
		if ( get_the_archive_title()  && ( get_the_archive_title() <> __( 'Archives', 'bazaar-lite' )) && (!bazaarlite_is_woocommerce_active('is_woocommerce')) ) :
		
			return get_the_archive_title();
		
		endif;
	
	}

}

/*-----------------------------------------------------------------------------------*/
/* IS SINGLE */
/*-----------------------------------------------------------------------------------*/ 

if (!function_exists('bazaarlite_is_single')) {

	function bazaarlite_is_single() {
		
		if ( is_single() || is_page() || is_singular('portfolio') ) :
		
			return true;
		
		endif;
	
	}

}

/*-----------------------------------------------------------------------------------*/
/* TAG TITLE */
/*-----------------------------------------------------------------------------------*/  

if (!function_exists('bazaarlite_addtitle')) {

	function bazaarlite_addtitle() {
		
?>

	<title><?php wp_title( '|', true, 'right' ); ?></title>

<?php

	}

	add_action( 'wp_head', 'bazaarlite_addtitle' );

}

/*-----------------------------------------------------------------------------------*/
/* BODY CLASSES */
/*-----------------------------------------------------------------------------------*/   

if (!function_exists('bazaarlite_body_classes_function')) {

	function bazaarlite_body_classes_function( $classes ) {

		global $wp_customize;

		if ( bazaarlite_setting('wip_infinitescroll_system') == "on" ) :
		
			$classes[] = 'infinitescroll';
				
		endif;

		if ( isset( $wp_customize ) ) :

			$classes[] = 'customizer_active';
				
		endif;

		if ( bazaarlite_setting('wip_minimal_layout') == "on" ) :
		
			$classes[] = 'minimal-layout';
				
		endif;

		return $classes;

	}
	
	add_filter( 'body_class', 'bazaarlite_body_classes_function' );

}

/*-----------------------------------------------------------------------------------*/
/* SIDEBAR LIST */
/*-----------------------------------------------------------------------------------*/ 

if (!function_exists('bazaarlite_sidebar_list')) {

	function bazaarlite_sidebar_list($sidebar_type) {
	
		if ( $sidebar_type == "side" ) :

			$default = array( $sidebar_type."_sidebar_area" => "Default" );

		else:

			$default = array("none" => "None", $sidebar_type."_sidebar_area" => "Default");

		endif;
		
		return $default;
		
	}

}

/*-----------------------------------------------------------------------------------*/
/* GET PAGED */
/*-----------------------------------------------------------------------------------*/ 

if (!function_exists('bazaarlite_paged')) {

	function bazaarlite_paged() {
		
		if ( get_query_var('paged') ) {
			$paged = get_query_var('paged');
		} elseif ( get_query_var('page') ) {
			$paged = get_query_var('page');
		} else {
			$paged = 1;
		}
		
		return $paged;
		
	}

}

/*-----------------------------------------------------------------------------------*/
/* BREADCRUMB */
/*-----------------------------------------------------------------------------------*/   

if (!function_exists('bazaarlite_breadcrumb')) {

	function bazaarlite_breadcrumb() {
		
		global $s;
	
		echo '<ul id="breadcrumb">';
		
		if ( !bazaarlite_is_woocommerce_active('is_woocommerce') ) {
			
			echo '<li><a href="' . esc_url(home_url()) . '">' . esc_html__("Home","bazaar-lite") . "</a></li> / ";
			
			if ( is_category() ) {
				
				echo '<li>' . bazaarlite_get_archive_title(). '</li>';

			} elseif (is_single() && !is_attachment()) {
				
				echo "<li>" . the_category(' </li> / <li> ') . '</li> / <li> ' . get_the_title() . '</li>';
	
			} elseif (is_page()) {
				
				echo "<li>" . get_the_title() . '</li>';
	
			} else if ( bazaarlite_get_archive_title()) {
			
				echo "<li>" . bazaarlite_get_archive_title() . "</li>";
			
			} else if ( is_search() ) {

				echo "<li>" . esc_html__( '<span>Search </span> results for ', 'bazaar-lite' ) . $s . "</li>";
			
			} else if ( is_404() ) {

				echo "<li>" . esc_html__( 'Page 404', 'bazaar-lite' ) . $s . "</li>";
			
			} else if ( is_attachment() ) {

				echo "<li>" . esc_html__( 'Attachment: ', 'bazaar-lite' ) . get_the_title() . "</li>";
			
			} 
	
		} else if ( bazaarlite_is_woocommerce_active('is_woocommerce') ) {
	
			woocommerce_breadcrumb(
				array(
					'wrap_before' => '',
					'wrap_after'  => '',
					'before'      => '<li>',
					'after'       => '</li>',
				)
			);
	
		}
		
		echo '</ul>';
		
	}

}

/*-----------------------------------------------------------------------------------*/
/* LOGO */
/*-----------------------------------------------------------------------------------*/   

if (!function_exists('bazaarlite_get_logo')) {

	function bazaarlite_get_logo() {
		
		if ( function_exists( 'the_custom_logo' ) && get_custom_logo() ) :
		
			the_custom_logo();
						
		else :
		
			echo '<a href="' . esc_url(home_url()) . '" title="' .  esc_attr(get_bloginfo('name')) . '">';
			
				if ( bazaarlite_setting('wip_custom_logo') ):
                                                
					echo "<img src='" . bazaarlite_setting('wip_custom_logo') . "' alt='logo'>"; 
                                           
				else: 
                                                
					bloginfo('name');
            
				endif; 

			echo '</a>';

		endif;
		
	}

}

/*-----------------------------------------------------------------------------------*/
/* READ MORE */
/*-----------------------------------------------------------------------------------*/   

if (!function_exists('bazaarlite_remove_readmore')) {

	function bazaarlite_remove_readmore($more) {
		
		return '';
		
	}
	
	add_filter('excerpt_more', 'bazaarlite_remove_readmore' );

}


if (!function_exists('bazaarlite_manual_excerpt_more')) {

	function bazaarlite_manual_excerpt_more( $excerpt ) {
	
		global $post;
		
		$class = 'button';
		$button = esc_html__('Read more','bazaar-lite');

		if ( bazaarlite_setting('wip_readmore_button') == "off" ): 
	
			$class = 'more';
			$button = ' [&hellip;] ';
		
		endif;

		return $excerpt . '<a class="'.$class.'" href="'.esc_url( get_permalink( $post->ID ) ).'" title="'.esc_attr__( 'More', 'bazaar-lite' ).'">'.$button.'</a>';
	
	}
	
	add_filter( 'get_the_excerpt', 'bazaarlite_manual_excerpt_more' );

}

/*-----------------------------------------------------------------------------------*/
/* POST ICON */
/*-----------------------------------------------------------------------------------*/ 

if (!function_exists('bazaarlite_posticon')) {

	function bazaarlite_posticon( $view = "off" ) {
	
		$icons = array (
				
			"video" => "genericon-video" , 
			"gallery" => "genericon-image" , 
			"audio" => "genericon-audio" , 
			"chat" => "genericon-chat", 
			"status" => "genericon-status", 
			"image" => "genericon-picture", 
			"quote" => "genericon-quote" , 
			"link" => "genericon-external", 
			"aside" => "genericon-aside"
			
		);
		
		if (get_post_format()) : 
			
			$icon = '<span class="genericon '.$icons[get_post_format()].'"></span>'; 
			
		else:
			
			$icon = '<span class="genericon genericon-standard"></span>'; 
			
		endif;

		if ( $view == "on" ):
		
			return $icon;
		
		endif;
	
	}

}

/*-----------------------------------------------------------------------------------*/
/* Woocommerce template */
/*-----------------------------------------------------------------------------------*/ 

if (!function_exists('bazaarlite_template')) {

	function bazaarlite_template($id) {
	
		$template = array ( 
		
			"full" => "col-md-12" , 
			"left-sidebar" => "col-md-8" , 
			"right-sidebar" => "col-md-8",
			"masonry" => "col-md-4"
		
		);
	
		$span = $template["right-sidebar"];
		$sidebar =  "right-sidebar";

		if ( bazaarlite_is_woocommerce_active('is_woocommerce') && ( bazaarlite_is_woocommerce_active('is_product_category') || bazaarlite_is_woocommerce_active('is_product_tag') ) && bazaarlite_setting('wip_woocommerce_category_layout') ) {
		
			$span = $template[bazaarlite_setting('wip_woocommerce_category_layout')];
			$sidebar =  bazaarlite_setting('wip_woocommerce_category_layout');

		} else if ( bazaarlite_is_woocommerce_active('is_woocommerce') && is_search() && bazaarlite_postmeta('wip_template') ) {
					
			$span = $template[bazaarlite_postmeta('wip_template')];
			$sidebar =  bazaarlite_postmeta('wip_template');
	
		} else if ( ( is_page() || is_single() || bazaarlite_is_woocommerce_active('is_shop') ) && bazaarlite_postmeta('wip_template') ) {
					
			$span = $template[bazaarlite_postmeta('wip_template')];
			$sidebar =  bazaarlite_postmeta('wip_template');

		} else if ( !bazaarlite_is_woocommerce_active('is_woocommerce') && ( is_category() || is_tag() || is_tax() || is_month() ) && bazaarlite_setting('wip_category_layout') ) {

			$span = $template[bazaarlite_setting('wip_category_layout')];
			$sidebar =  bazaarlite_setting('wip_category_layout');
						
		} else if ( is_home() && bazaarlite_setting('wip_home') ) {
					
			$span = $template[bazaarlite_setting('wip_home')];
			$sidebar =  bazaarlite_setting('wip_home');

		} else if ( ! bazaarlite_is_woocommerce_active('is_woocommerce') && is_search() && bazaarlite_setting('wip_search_layout') ) {

			$span = $template[bazaarlite_setting('wip_search_layout')];
			$sidebar =  bazaarlite_setting('wip_search_layout');
						
		}

		return ${$id};
		
	}

}

/*-----------------------------------------------------------------------------------*/
/* PRETTYPHOTO */
/*-----------------------------------------------------------------------------------*/   

if (!function_exists('bazaarlite_prettyPhoto')) {

	function bazaarlite_prettyPhoto( $html, $id, $size, $permalink, $icon, $text ) {
		
		if ( ! $permalink )
			return str_replace( '<a', '<a rel="prettyPhoto" ', $html );
		else
			return $html;
	
	}
	
	add_filter( 'wp_get_attachment_link', 'bazaarlite_prettyPhoto', 10, 6);
	
}

/*-----------------------------------------------------------------------------------*/
/* REMOVE CSS GALLERY */
/*-----------------------------------------------------------------------------------*/ 

if (!function_exists('bazaarlite_my_gallery_style')) {

	function bazaarlite_my_gallery_style() {
		return "<div class='gallery'>";
	}
	
	add_filter( 'gallery_style', 'bazaarlite_my_gallery_style', 99 );
	
}

/*-----------------------------------------------------------------------------------*/
/* THEME SETUP */
/*-----------------------------------------------------------------------------------*/   

if (!function_exists('bazaarlite_setup')) {

	function bazaarlite_setup() {

		global $content_width;

		if ( ! isset( $content_width ) )
			$content_width = bazaarlite_get_width();
	
		add_theme_support( 'post-formats', array( 'aside','gallery','quote','video','audio','link','status','chat','image' ) );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'woocommerce' );
		add_theme_support( 'title-tag' );

		add_theme_support( 'custom-logo');

		add_image_size( 'bazaar-lite-thumbnail', bazaarlite_get_width(), bazaarlite_get_height(), TRUE ); 
		add_image_size( 'bazaar-lite-product', 500,500, TRUE ); 
	
		register_nav_menu( 'main-menu', 'Main menu' );

		load_theme_textdomain('bazaar-lite', get_template_directory() . '/languages');
		
		add_theme_support( 'custom-background', array(
			'default-color' => 'fafafa',
		) );
		
		require_once( trailingslashit( get_template_directory() ) . '/core/includes/class-plugin-activation.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/includes/class-customize.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/includes/class-metaboxes.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/includes/class-notice.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/admin/customize/customize.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/admin/customize/general.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/after-content.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/before-content.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/bottom_sidebar.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/breadcrumb.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/footer_sidebar.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/header_sidebar.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/header-slideshow.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/masonry.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/media.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/pagination.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/post-formats.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/side_sidebar.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/social_buttons.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/title.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/scripts/infinitescroll.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/scripts/infinitescroll_masonry.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/scripts/masonry.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/functions/functions_required_plugins.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/functions/functions_style.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/functions/functions_widgets.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/functions/functions_woocommerce.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/metaboxes/pages.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/metaboxes/posts.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/metaboxes/product.php' );
		
	}

	add_action( 'after_setup_theme', 'bazaarlite_setup' );

}

/*-----------------------------------------------------------------------------------*/
/* STYLES AND SCRIPTS */
/*-----------------------------------------------------------------------------------*/ 

if (!function_exists('bazaarlite_scripts_styles')) {

	function bazaarlite_scripts_styles() {

		wp_enqueue_style( 'bazaar-style', get_stylesheet_uri(), array() );

		wp_enqueue_style ( 'bootstrap', get_template_directory_uri() . '/assets/css/bootstrap.css' );
		wp_enqueue_style ( 'font-awesome', get_template_directory_uri() . '/assets/css/font-awesome.css' );
		wp_enqueue_style ( 'genericons', get_template_directory_uri() . '/assets/css/genericons.css' );
		wp_enqueue_style ( 'bazaar-lite-minimal_layout', get_template_directory_uri() . '/assets/css/minimal_layout.css' );
		wp_enqueue_style ( 'prettyPhoto', get_template_directory_uri() . '/assets/css/prettyPhoto.css' );
		wp_enqueue_style ( 'slick', get_template_directory_uri() . '/assets/css/slick.css' );
		wp_enqueue_style ( 'swipebox', get_template_directory_uri() . '/assets/css/swipebox.css' );
		wp_enqueue_style ( 'bazaar-lite-template', get_template_directory_uri() . '/assets/css/template.css' );
		wp_enqueue_style ( 'bazaar-lite-woocommerce', get_template_directory_uri() . '/assets/css/woocommerce.css' );
	
		if ( ( get_theme_mod('wip_skin') ) && ( get_theme_mod('wip_skin') <> "black_turquoise" ) ):
	
			wp_enqueue_style( 'bazaar-lite-' . get_theme_mod('wip_skin') , get_template_directory_uri() . '/assets/skins/' . get_theme_mod('wip_skin') . '.css' ); 
	
		endif;
		
		wp_enqueue_style( 'bazaar-lite-google-fonts', '//fonts.googleapis.com/css?family=Montserrat:300,400,600,700|Yesteryear' );
		
		if ( is_singular() ) wp_enqueue_script( 'comment-reply' );
	
		wp_enqueue_script( "jquery-ui-core");
		wp_enqueue_script( "jquery-ui-tabs");
		wp_enqueue_script( "masonry");


		wp_enqueue_script( 'jquery-easing', get_template_directory_uri() . '/assets/js/jquery.easing.js' , array('jquery'), FALSE, TRUE ); 
		wp_enqueue_script( 'jquery-imagesloaded', get_template_directory_uri() . '/assets/js/jquery.imagesloaded.js' , array('jquery'), FALSE, TRUE ); 
		wp_enqueue_script( 'jquery-infinitescroll', get_template_directory_uri() . '/assets/js/jquery.infinitescroll.js' , array('jquery'), FALSE, TRUE ); 
		wp_enqueue_script( 'jquery-modernizr', get_template_directory_uri() . '/assets/js/jquery.modernizr.js' , array('jquery'), FALSE, TRUE ); 
		wp_enqueue_script( 'jquery-prettyPhoto', get_template_directory_uri() . '/assets/js/jquery.prettyPhoto.js' , array('jquery'), FALSE, TRUE ); 
		wp_enqueue_script( 'jquery-scrollTo', get_template_directory_uri() . '/assets/js/jquery.scrollTo.js' , array('jquery'), FALSE, TRUE ); 
		wp_enqueue_script( 'jquery-slick', get_template_directory_uri() . '/assets/js/jquery.slick.js' , array('jquery'), FALSE, TRUE ); 
		wp_enqueue_script( 'jquery-swipebox', get_template_directory_uri() . '/assets/js/jquery.swipebox.js' , array('jquery'), FALSE, TRUE ); 
		wp_enqueue_script( 'jquery-tinynav', get_template_directory_uri() . '/assets/js/jquery.tinynav.js' , array('jquery'), FALSE, TRUE ); 
		wp_enqueue_script( 'bazaar-lite-jquery.wip', get_template_directory_uri() . '/assets/js/jquery.wip.js' , array('jquery'), FALSE, TRUE ); 

		$wip_vars = array(
			'slick_autoplay' => bazaarlite_setting('wip_slick_autoplay', 'false'),
			'slick_dots' => bazaarlite_setting('wip_slick_dots', 'true'),
		);

		wp_localize_script( 'bazaar-lite-jquery.wip', 'wip_vars', $wip_vars );

		wp_enqueue_script ( 'bazaar-lite-html5',get_template_directory_uri().'/assets/scripts/html5.js');
		wp_script_add_data ( 'bazaar-lite-html5', 'conditional', 'IE 8' );
		
		wp_enqueue_script ( 'bazaar-lite-selectivizr',get_template_directory_uri().'/assets/scripts/selectivizr-min.js');
		wp_script_add_data ( 'bazaar-lite-selectivizr', 'conditional', 'IE 8' );

	}
	
	add_action( 'wp_enqueue_scripts', 'bazaarlite_scripts_styles', 11 );

}

?>