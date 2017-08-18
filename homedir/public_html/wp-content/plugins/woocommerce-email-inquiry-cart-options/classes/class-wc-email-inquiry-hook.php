<?php
/**
 * WC Email Inquiry Hook Filter
 *
 * Table Of Contents
 *
 * shop_before_hide_add_to_cart_button()
 * shop_after_hide_add_to_cart_button()
 * details_before_hide_add_to_cart_button()
 * details_after_hide_add_to_cart_button()
 * add_email_inquiry_button()
 * shop_add_email_inquiry_button_above()
 * shop_add_email_inquiry_button_below()
 * details_add_email_inquiry_button_above()
 * details_add_email_inquiry_button_below()
 * wc_email_inquiry_popup()
 * wc_email_inquiry_action()
 * add_style_header()
 * footer_print_scripts()
 * script_contact_popup()
 * a3_wp_admin()
 * admin_sidebar_menu_css()
 * plugin_extra_links()
 */
class WC_Email_Inquiry_Hook_Filter
{
		
	public static function shop_before_hide_add_to_cart_button($template_name, $template_path, $located, $args ) {
		global $post;
		global $product;
		if ($template_name == 'loop/add-to-cart.php') {
			$product_id = $product->get_id();
			
			if (WC_Email_Inquiry_Functions::check_hide_add_cart_button($product_id))
				ob_start();
		}
	}
	
	public static function shop_after_hide_add_to_cart_button($template_name, $template_path, $located, $args ) {
		global $post;
		global $product;
		if ($template_name == 'loop/add-to-cart.php') {
			$product_id = $product->get_id();
			
			if (WC_Email_Inquiry_Functions::check_hide_add_cart_button($product_id))
				ob_end_clean();
		}
	}
	
	public static function details_before_hide_add_to_cart_button() {
		global $post, $product;
		$product_id = $product->get_id();
		
		if (WC_Email_Inquiry_Functions::check_hide_add_cart_button($product_id) ) {
			ob_start();
		}
	}
	
	public static function details_after_hide_add_to_cart_button() {
		global $post, $product;
		$product_id = $product->get_id();
		
		if (WC_Email_Inquiry_Functions::check_hide_add_cart_button($product_id)){
			ob_end_clean();
			
			if ($product->is_type('variable')) {
				?>
					<div class="single_variation_wrap" style="display:none;">
						<div class="woocommerce-variation single_variation"></div>
						<div class="woocommerce-variation-add-to-cart variations_button"><input type="hidden" name="variation_id" value="" /></div>
					</div>
					<div><input type="hidden" name="product_id" value="<?php echo $post->ID; ?>" /></div>
				<?php
			}
		}
	}
	
	public static function grouped_product_hide_add_to_cart_style() {
		global $product;
		$product_id = $product->get_id();
		
		if ( $product->is_type('grouped') && WC_Email_Inquiry_Functions::check_hide_add_cart_button( $product_id ) ){
			echo '<style>body table.group_table a.button, body table.group_table a.single_add_to_cart_button, body table.group_table .quantity, table.group_table a.button, table.group_table a.single_add_to_cart_button, table.group_table .quantity { display:none !important; } </style>';
		}
	}
	
	public static function grouped_product_hide_add_to_cart( $add_to_cart='', $product_type ) {
		global $product;
		$product_id = $product->get_id();
		
		if ( WC_Email_Inquiry_Functions::check_hide_add_cart_button( $product_id ) ){
			$add_to_cart = '';
		}
		
		return $add_to_cart;
	}
	
	public static function before_grouped_product_hide_quatity_control( $template_name, $template_path, $located, $args ) {
		global $product;
		if ( $template_name == 'single-product/add-to-cart/quantity.php' ) {
			$product_id = $product->get_id();
			
			if ( WC_Email_Inquiry_Functions::check_hide_add_cart_button( $product_id ) ) {
				ob_start();
			}
		}
	}
	
	public static function after_grouped_product_hide_quatity_control( $template_name, $template_path, $located, $args ) {
		global $product;
		if ( $template_name == 'single-product/add-to-cart/quantity.php' ) {
			$product_id = $product->get_id();
			
			if ( WC_Email_Inquiry_Functions::check_hide_add_cart_button( $product_id ) ) {
				ob_end_clean();
			}
		}
	}
	
	public static function add_email_inquiry_button($product_id) {
		global $post;
		global $wc_email_inquiry_contact_form_settings;
		global $wc_email_inquiry_customize_email_button;
		
		$expand_text = '';
		$inner_form = '';
		$email_inquiry_button_class = 'wc_email_inquiry_popup_button wc_email_inquiry_button_closed';
				
		$wc_email_inquiry_button_type = $wc_email_inquiry_customize_email_button['inquiry_button_type'];
		
		$wc_email_inquiry_text_before = $wc_email_inquiry_customize_email_button['inquiry_text_before'];
		
		$wc_email_inquiry_hyperlink_text = $wc_email_inquiry_customize_email_button['inquiry_hyperlink_text'];
		
		if (trim($wc_email_inquiry_hyperlink_text) == '') $wc_email_inquiry_hyperlink_text = __('Click Here', 'woocommerce-email-inquiry-cart-options' );
		
		$wc_email_inquiry_trailing_text = $wc_email_inquiry_customize_email_button['inquiry_trailing_text'];
		
		$wc_email_inquiry_button_title = $wc_email_inquiry_customize_email_button['inquiry_button_title'];
		
		if (trim($wc_email_inquiry_button_title) == '') $wc_email_inquiry_button_title = __('Product Enquiry', 'woocommerce-email-inquiry-cart-options' );
		
		$wc_email_inquiry_button_position = $wc_email_inquiry_customize_email_button['inquiry_button_position'];
		
		$wc_email_inquiry_button_class = '';
		
		$button_link = '';
		if (trim($wc_email_inquiry_text_before) != '') $button_link .= '<span class="wc_email_inquiry_text_before wc_email_inquiry_text_before_'.$product_id.'">'.trim($wc_email_inquiry_text_before).'</span> ';
		$button_link .= '<a class="wc_email_inquiry_hyperlink_text wc_email_inquiry_hyperlink_text_'.$product_id.' '.$email_inquiry_button_class.'" id="wc_email_inquiry_button_'.$product_id.'" product_name="'.addslashes( strip_tags( $post->post_title ) ).'" product_id="'.$product_id.'" form_action="hide">'.$wc_email_inquiry_hyperlink_text.$expand_text.'</a>';
		if (trim($wc_email_inquiry_trailing_text) != '') $button_link .= ' <span class="wc_email_inquiry_trailing_text wc_email_inquiry_trailing_text_'.$product_id.'">'.trim($wc_email_inquiry_trailing_text).'</span>';
		
		$button_button = '<a class="wc_email_inquiry_email_button wc_email_inquiry_button_'.$product_id.' '.$email_inquiry_button_class.' '.$wc_email_inquiry_button_class.'" id="wc_email_inquiry_button_'.$product_id.'" product_name="'.addslashes( strip_tags( get_the_title($product_id) ) ).'" product_id="'.$product_id.'" form_action="hide">'.$wc_email_inquiry_button_title.$expand_text.'</a>';

			add_action('wp_footer', array('WC_Email_Inquiry_Hook_Filter', 'footer_print_scripts') );
			$button_ouput = '<span class="wc_email_inquiry_button_container">';
			if ($wc_email_inquiry_button_type == 'link') $button_ouput .= $button_link;
			else $button_ouput .= $button_button;
			
			$button_ouput .= '</span>';
			
		return $button_ouput . $inner_form;
	}
	
	public static function shop_add_email_inquiry_button_above($template_name, $template_path, $located, $args) {
		global $post;
		global $product;
		if ($template_name == 'loop/add-to-cart.php') {
			$product_id = $product->get_id();
			
			if ( ($post->post_type == 'product' || $post->post_type == 'product_variation') && WC_Email_Inquiry_Functions::check_add_email_inquiry_button_on_shoppage($product_id) ) {
				echo WC_Email_Inquiry_Hook_Filter::add_email_inquiry_button($product_id);
			}
		}
	}
	
	public static function shop_add_email_inquiry_button_below() {
		global $post;
		global $product;
		global $wc_email_inquiry_customize_email_button_settings;
		$product_id = $product->get_id();
		
		if ( $wc_email_inquiry_customize_email_button_settings['inquiry_button_position'] == 'above' ) return;
		 
		if ( ($post->post_type == 'product' || $post->post_type == 'product_variation') && WC_Email_Inquiry_Functions::check_add_email_inquiry_button_on_shoppage($product_id) ) {
			echo WC_Email_Inquiry_Hook_Filter::add_email_inquiry_button($product_id);
		}
	}

	public static function details_add_email_inquiry_button_above($template_name, $template_path, $located, $args) {
		global $post;
		global $product;

		$addtocart_templates = apply_filters( 'wc_ei_addtocart_templates', array(
			'simple'                => 'single-product/add-to-cart/simple.php',
			'grouped'               => 'single-product/add-to-cart/grouped.php',
			'external'              => 'single-product/add-to-cart/external.php',
			'variable'              => 'single-product/add-to-cart/variable.php',
			'subscription'          => 'single-product/add-to-cart/subscription.php',
			'variable-subscription' => 'single-product/add-to-cart/variable-subscription.php',
		) );

		if ( in_array($template_name, $addtocart_templates ) ) {
			$product_id = $product->get_id();

			if (($post->post_type == 'product' || $post->post_type == 'product_variation') && WC_Email_Inquiry_Functions::check_add_email_inquiry_button($product_id) ) {
				echo WC_Email_Inquiry_Hook_Filter::add_email_inquiry_button($product_id);
			}
		}
	}

	public static function details_add_email_inquiry_button_below($template_name, $template_path, $located, $args){
		global $post;
		global $product;

		$addtocart_templates = apply_filters( 'wc_ei_addtocart_templates', array(
			'simple'                => 'single-product/add-to-cart/simple.php',
			'grouped'               => 'single-product/add-to-cart/grouped.php',
			'external'              => 'single-product/add-to-cart/external.php',
			'variable'              => 'single-product/add-to-cart/variable.php',
			'subscription'          => 'single-product/add-to-cart/subscription.php',
			'variable-subscription' => 'single-product/add-to-cart/variable-subscription.php',
		) );

		if ( in_array($template_name, $addtocart_templates ) ) {
			$product_id = $product->get_id();

			if (($post->post_type == 'product' || $post->post_type == 'product_variation') && WC_Email_Inquiry_Functions::check_add_email_inquiry_button($product_id) ) {
				echo WC_Email_Inquiry_Hook_Filter::add_email_inquiry_button($product_id);
			}
		}
	}
	
	public static function wc_email_inquiry_popup() {

		global $wc_email_inquiry_contact_form_settings;
		global $wc_email_inquiry_customize_email_button;
		global $wc_email_inquiry_global_settings;
		
		$wc_email_inquiry_action = wp_create_nonce("wc_email_inquiry_action");
		$product_id = $_REQUEST['product_id'];
		$product_name = strip_tags( get_the_title($product_id) );
				
		$wc_email_inquiry_button_title = $wc_email_inquiry_customize_email_button['inquiry_button_title'];
		if (trim($wc_email_inquiry_button_title) == '') $wc_email_inquiry_button_title = __('Product Enquiry', 'woocommerce-email-inquiry-cart-options' );
		
		$wc_email_inquiry_text_before = $wc_email_inquiry_customize_email_button['inquiry_text_before'];
		
		$wc_email_inquiry_hyperlink_text = $wc_email_inquiry_customize_email_button['inquiry_hyperlink_text'];
		if (trim($wc_email_inquiry_hyperlink_text) == '') $wc_email_inquiry_hyperlink_text = __('Click Here', 'woocommerce-email-inquiry-cart-options' );
		
		$wc_email_inquiry_trailing_text = $wc_email_inquiry_customize_email_button['inquiry_trailing_text'];
		
		if ( trim( $wc_email_inquiry_global_settings['inquiry_contact_heading'] ) != '') {
			$wc_email_inquiry_contact_heading = $wc_email_inquiry_global_settings['inquiry_contact_heading'];
		} else {
			$wc_email_inquiry_contact_heading = __('Product Inquiry', 'woocommerce-email-inquiry-cart-options' );
		}
		
		if ( trim( $wc_email_inquiry_global_settings['inquiry_contact_text_button'] ) != '') $wc_email_inquiry_contact_text_button = $wc_email_inquiry_global_settings['inquiry_contact_text_button'];
		else $wc_email_inquiry_contact_text_button = __('SEND', 'woocommerce-email-inquiry-cart-options' );
				
		$wc_email_inquiry_contact_button_class = '';
		$wc_email_inquiry_contact_form_class = '';

		$wc_email_inquiry_send_copy = false;
		
		
	?>	
<div class="wc_email_inquiry_form <?php echo $wc_email_inquiry_contact_form_class; ?>">
<div style="padding:10px;">
	<h1 class="wc_email_inquiry_result_heading"><?php echo $wc_email_inquiry_contact_heading; ?></h1>
	<div class="wc_email_inquiry_content" id="wc_email_inquiry_content_<?php echo $product_id; ?>">
		<div class="wc_email_inquiry_field">
        	<label class="wc_email_inquiry_label" for="your_name_<?php echo $product_id; ?>"><?php _e('Name','woocommerce-email-inquiry-cart-options' ); ?> <span class="wc_email_inquiry_required">*</span></label> 
			<input type="text" class="your_name" name="your_name" id="your_name_<?php echo $product_id; ?>" value="" /></div>
		<div class="wc_email_inquiry_field">
        	<label class="wc_email_inquiry_label" for="your_email_<?php echo $product_id; ?>"><?php _e('Email','woocommerce-email-inquiry-cart-options' ); ?> <span class="wc_email_inquiry_required">*</span></label>
			<input type="text" class="your_email" name="your_email" id="your_email_<?php echo $product_id; ?>" value="" /></div>
		<div class="wc_email_inquiry_field">
        	<label class="wc_email_inquiry_label" for="your_phone_<?php echo $product_id; ?>"><?php _e('Phone','woocommerce-email-inquiry-cart-options' ); ?> <span class="wc_email_inquiry_required">*</span></label> 
			<input type="text" class="your_phone" name="your_phone" id="your_phone_<?php echo $product_id; ?>" value="" /></div>
		<div class="wc_email_inquiry_field">
        	<label class="wc_email_inquiry_label"><?php _e('Subject','woocommerce-email-inquiry-cart-options' ); ?> </label> 
			<span class="wc_email_inquiry_subject"><?php echo $product_name; ?></span></div>
		<div class="wc_email_inquiry_field">
        	<label class="wc_email_inquiry_label" for="your_message_<?php echo $product_id; ?>"><?php _e('Message','woocommerce-email-inquiry-cart-options' ); ?></label> 
			<textarea class="your_message" name="your_message" id="your_message_<?php echo $product_id; ?>"></textarea></div>
        <div class="wc_email_inquiry_field">
            <a class="wc_email_inquiry_form_button wc_email_inquiry_bt_<?php echo $product_id; ?> <?php echo $wc_email_inquiry_contact_button_class; ?>" id="wc_email_inquiry_bt_<?php echo $product_id; ?>" product_id="<?php echo $product_id; ?>"><?php echo $wc_email_inquiry_contact_text_button; ?></a> <span class="wc_email_inquiry_loading" id="wc_email_inquiry_loading_<?php echo $product_id; ?>"><img src="<?php echo WC_EMAIL_INQUIRY_IMAGES_URL; ?>/ajax-loader.gif" /></span>
        </div>
        <div style="clear:both"></div>
	</div>
    <div style="clear:both"></div>
</div>
</div>
	<?php		
		die();
	}
	
	public static function wc_email_inquiry_action() {
		$product_id 	= esc_attr( stripslashes( $_REQUEST['product_id'] ) );
		$your_name 		= esc_attr( stripslashes( $_REQUEST['your_name'] ) );
		$your_email 	= esc_attr( stripslashes( $_REQUEST['your_email'] ) );
		$your_phone 	= esc_attr( stripslashes( $_REQUEST['your_phone'] ) );
		$your_message 	= esc_attr( stripslashes( strip_tags( $_REQUEST['your_message'] ) ) );
		$send_copy_yourself	= esc_attr( stripslashes( $_REQUEST['send_copy'] ) );
		
		$email_result = WC_Email_Inquiry_Functions::email_inquiry($product_id, $your_name, $your_email, $your_phone, $your_message, $send_copy_yourself);
		echo json_encode($email_result );
		die();
	}
		
	public static function add_style_header() {
		wp_enqueue_style('a3_wc_email_inquiry_style', WC_EMAIL_INQUIRY_CSS_URL . '/wc_email_inquiry_style.css');
	}
	
	public static function footer_print_scripts() {
		global $woocommerce;
		global $wc_email_inquiry_global_settings;
		global $wc_email_inquiry_contact_form_settings;
		$woocommerce_db_version = get_option( 'woocommerce_db_version', null );
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
		
		wp_enqueue_script('jquery');
		$wc_email_inquiry_popup_type = $wc_email_inquiry_global_settings['inquiry_popup_type'];
		if ($wc_email_inquiry_popup_type == 'colorbox') {
			wp_enqueue_style( 'a3_colorbox_style', WC_EMAIL_INQUIRY_JS_URL . '/colorbox/colorbox.css' );
			wp_enqueue_script( 'colorbox_script', WC_EMAIL_INQUIRY_JS_URL . '/colorbox/jquery.colorbox'.$suffix.'.js', array( 'jquery' ), false, true );
		} else {
			wp_enqueue_style( 'woocommerce_fancybox_styles', WC_EMAIL_INQUIRY_JS_URL . '/fancybox/fancybox.css' );
			wp_enqueue_script( 'fancybox', WC_EMAIL_INQUIRY_JS_URL . '/fancybox/fancybox'.$suffix.'.js', array( 'jquery' ), false, true );
		}
	}
	
	public static function script_contact_popup() {
		global $wc_email_inquiry_global_settings;
		global $wc_email_inquiry_contact_form_settings;
		
		
		$woocommerce_db_version = get_option( 'woocommerce_db_version', null );
		$wc_email_inquiry_popup = wp_create_nonce("wc_email_inquiry_popup");
		$wc_email_inquiry_action = wp_create_nonce("wc_email_inquiry_action");

		wp_enqueue_script('jquery');
	?>
<script type="text/javascript">
(function($){
	$(function(){
		var ajax_url = "<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>";
		$(document).on("click", ".wc_email_inquiry_popup_button", function(){
			var product_id = $(this).attr("product_id");
			var product_name = $(this).attr("product_name");
		<?php
			$wc_email_inquiry_popup_type = $wc_email_inquiry_global_settings['inquiry_popup_type'];
			if ($wc_email_inquiry_popup_type == 'colorbox') {
		?>
			var popup_wide = 520;
			var center_on_scroll = true;
			if ( ei_getWidth()  <= 568 ) { 
				popup_wide = '100%';
				center_on_scroll = false;
			}
			$.colorbox({
				href		: ajax_url+"?action=wc_email_inquiry_popup&product_id="+product_id+"&security=<?php echo $wc_email_inquiry_popup; ?>",
				className	: 'email_inquiry_cb',
				opacity		: 0.85,
				scrolling	: true,
				initialWidth: 100,
				initialHeight: 100,
				innerWidth	: popup_wide,
				//innerHeight	: 500,
				maxWidth  	: '100%',
				maxHeight  	: '90%',
				returnFocus : true,
				transition  : 'none',
				speed		: 300,
				fixed		: center_on_scroll
			});
		<?php } else { ?> 
			var popup_wide = 520;
			var center_on_scroll = true;
			if ( ei_getWidth()  <= 568 ) { 
				popup_wide = '95%';
				center_on_scroll = false;
			}
			$.fancybox({
				href: ajax_url+"?action=wc_email_inquiry_popup&product_id="+product_id+"&security=<?php echo $wc_email_inquiry_popup; ?>",
				centerOnScroll : center_on_scroll,
				transitionIn : 'none', 
				transitionOut: 'none',
				easingIn: 'swing',
				easingOut: 'swing',
				speedIn : 300,
				speedOut : 0,
				width: popup_wide,
				autoScale: true,
				autoDimensions: true,
				height: 460,
				margin: 0,
				maxWidth: "95%",
				maxHeight: "80%",
				padding: 10,
				overlayColor: '#666666',
				showCloseButton : true,
				openEffect	: "none",
				closeEffect	: "none"
			});
		<?php } ?>
		});
		
		$(document).on("click", ".wc_email_inquiry_form_button", function(){
			if ( $(this).hasClass('wc_email_inquiry_sending') ) {
				return false;
			}
			$(this).addClass('wc_email_inquiry_sending');
			
			var product_id = $(this).attr("product_id");
			var your_name = $("#your_name_"+product_id).val();
			var your_email = $("#your_email_"+product_id).val();
			var your_phone = $("#your_phone_"+product_id).val();
			var your_message = $("#your_message_"+product_id).val();
			var send_copy = 0;
			
			var wc_email_inquiry_error = "";
			var wc_email_inquiry_have_error = false;
			var filter = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
			
			if (your_name.replace(/^\s+|\s+$/g, '') == "") {
				wc_email_inquiry_error += "<?php _e('Please enter your Name', 'woocommerce-email-inquiry-cart-options' ); ?>\n";
				wc_email_inquiry_have_error = true;
			}
			if (your_email == "" || !filter.test(your_email)) {
				wc_email_inquiry_error += "<?php _e('Please enter valid Email address', 'woocommerce-email-inquiry-cart-options' ); ?>\n";
				wc_email_inquiry_have_error = true;
			}
			if (your_phone.replace(/^\s+|\s+$/g, '') == "") {
				wc_email_inquiry_error += "<?php _e('Please enter your Phone', 'woocommerce-email-inquiry-cart-options' ); ?>\n";
				wc_email_inquiry_have_error = true;
			}
			if (wc_email_inquiry_have_error) {
				$(this).removeClass('wc_email_inquiry_sending');
				alert(wc_email_inquiry_error);
				return false;
			}
			$("#wc_email_inquiry_loading_"+product_id).show();
			
			var data = {
				action: 		"wc_email_inquiry_action",
				product_id: 	product_id,
				your_name: 		your_name,
				your_email: 	your_email,
				your_phone: 	your_phone,
				your_message: 	your_message,
				send_copy:		send_copy,
				security: 		"<?php echo $wc_email_inquiry_action; ?>"
			};
			$.post( ajax_url, data, function(response) {
				wc_email_inquiry_response = $.parseJSON( response );
				$("#wc_email_inquiry_loading_"+product_id).hide();
				$("#wc_email_inquiry_content_"+product_id).html(wc_email_inquiry_response);
				<?php if ( $wc_email_inquiry_popup_type == 'colorbox' ) { ?>
				var height_cb = false;
				if ( ei_getWidth()  <= 568 ) { 
					height_cb = '90%';
				}
				$.colorbox.resize({
					height:		height_cb
				});
				<?php } ?>
			});
		});
	});
})(jQuery);
</script>
    <?php
	?>
<script>
function ei_getWidth() {
    xWidth = null;
    if(window.screen != null)
      xWidth = window.screen.availWidth;

    if(window.innerWidth != null)
      xWidth = window.innerWidth;

    if(document.body != null)
      xWidth = document.body.clientWidth;

    return xWidth;
}
</script>
	<?php
	}
	
	public static function add_google_fonts() {
		global $wc_ei_fonts_face;
		global $wc_email_inquiry_global_settings;
		global $wc_email_inquiry_customize_email_button;
		$google_fonts = array( 
							$wc_email_inquiry_global_settings['inquiry_contact_popup_text']['face'], 
						);
		
		$google_fonts = apply_filters( 'wc_ei_google_fonts', $google_fonts );
		
		$wc_ei_fonts_face->generate_google_webfonts( $google_fonts );
	}
	
	public static function change_order_item_display_meta_value( $meta_value = '' ) {
		if ( stristr( $meta_value, 'http://' ) !== false || stristr( $meta_value, 'https://' ) !== false ) {
			$meta_value = strip_tags( $meta_value );
			$meta_file_name = basename( $meta_value );
			$meta_value = '<a href="'.$meta_value.'">'.$meta_file_name.'</a>';
		}
		
		return $meta_value;
	}
	
	public static function a3_wp_admin() {
		wp_enqueue_style( 'a3rev-wp-admin-style', WC_EMAIL_INQUIRY_CSS_URL . '/a3_wp_admin.css' );
	}
	
	public static function admin_sidebar_menu_css() {
		wp_enqueue_style( 'a3rev-wc-ei-admin-sidebar-menu-style', WC_EMAIL_INQUIRY_CSS_URL . '/admin_sidebar_menu.css' );
	}

	public static function plugin_extension_box( $boxes = array() ) {
		global $wc_ei_admin_init;

		$support_box = '<a href="'.$wc_ei_admin_init->support_url.'" target="_blank" alt="'.__('Go to Support Forum', 'woocommerce-email-inquiry-cart-options' ).'"><img src="'.WC_EMAIL_INQUIRY_IMAGES_URL.'/go-to-support-forum.png" /></a>';
		$boxes[] = array(
			'content' => $support_box,
			'css' => 'border: none; padding: 0; background: none;'
		);

		/*$docs_box = '<a href="http://docs.a3rev.com/user-guides/plugins-extensions/woocommerce/email-inquiry-ultimate/" target="_blank" alt="'.__('View Plugin Docs', 'woocommerce-email-inquiry-cart-options' ).'"><img src="'.WC_EMAIL_INQUIRY_IMAGES_URL.'/view-plugin-docs.png" /></a>';

		$boxes[] = array(
			'content' => $docs_box,
			'css' => 'border: none; padding: 0; background: none;'
		);*/

		$premium_box = '<a href="http://a3rev.com/shop/woocommerce-email-inquiry-and-cart-options/" target="_blank" alt="'.__('Go to Support Forum', 'woocommerce-email-inquiry-cart-options' ).'"><img src="'.WC_EMAIL_INQUIRY_IMAGES_URL.'/woocommerce-email-inquiry-premium.jpg" /></a>';
		$boxes[] = array(
			'content' => $premium_box,
			'css' => 'border: none; padding: 0; background: none;'
		);

		$ultimate_box = '<a href="http://a3rev.com/shop/woocommerce-email-inquiry-ultimate/" target="_blank" alt="'.__('Go to Support Forum', 'woocommerce-email-inquiry-cart-options' ).'"><img src="'.WC_EMAIL_INQUIRY_IMAGES_URL.'/woocommerce-email-inquiry-ultimate.jpg" /></a>';
		$boxes[] = array(
			'content' => $ultimate_box,
			'css' => 'border: none; padding: 0; background: none;'
		);

		$quote_order_box = '<a href="http://a3rev.com/shop/woocommerce-quotes-and-orders/" target="_blank" alt="'.__('Go to Support Forum', 'woocommerce-email-inquiry-cart-options' ).'"><img src="'.WC_EMAIL_INQUIRY_IMAGES_URL.'/woocommerce-quotes-orders.jpg" /></a>';
		$boxes[] = array(
			'content' => $quote_order_box,
			'css' => 'border: none; padding: 0; background: none;'
		);

		$connect_box = '<div style="margin-bottom: 5px;">' . __('Connect with us via','woocommerce-email-inquiry-cart-options' ) . '</div>';
		$connect_box .= '<a href="https://www.facebook.com/a3rev" target="_blank" alt="'.__('a3rev Facebook', 'woocommerce-email-inquiry-cart-options' ).'" style="margin-right: 5px;"><img src="'.WC_EMAIL_INQUIRY_IMAGES_URL.'/follow-facebook.png" /></a> ';
		$connect_box .= '<a href="https://twitter.com/a3rev" target="_blank" alt="'.__('a3rev Twitter', 'woocommerce-email-inquiry-cart-options' ).'"><img src="'.WC_EMAIL_INQUIRY_IMAGES_URL.'/follow-twitter.png" /></a>';

		$boxes[] = array(
			'content' => $connect_box,
			'css' => 'border-color: #3a5795;'
		);

		$woocommerce_box = '<a href="http://a3rev.com/product-category/woocommerce/?display=products" target="_blank" alt="'.__('WooCommerce Plugins', 'woocommerce-email-inquiry-cart-options' ).'"><img src="'.WC_EMAIL_INQUIRY_IMAGES_URL.'/woocommerce-plugins.png" /></a>';

		$boxes[] = array(
			'content' => $woocommerce_box,
			'css' => 'border: none; padding: 0; background: none;'
		);

		return $boxes;
	}

	public static function plugin_extra_links($links, $plugin_name) {
		if ( $plugin_name != WC_EMAIL_INQUIRY_NAME) {
			return $links;
		}

		global $wc_ei_admin_init;

		$links[] = '<a href="http://docs.a3rev.com/user-guides/woocommerce/woo-email-inquiry-cart-options/" target="_blank">'.__('Documentation', 'woocommerce-email-inquiry-cart-options' ).'</a>';
		$links[] = '<a href="'.$wc_ei_admin_init->support_url.'" target="_blank">'.__('Support', 'woocommerce-email-inquiry-cart-options' ).'</a>';
		return $links;
	}

	public static function settings_plugin_links($actions) {
		$actions = array_merge( array( 'settings' => '<a href="admin.php?page=email-cart-options">' . __( 'Settings', 'woocommerce-email-inquiry-cart-options' ) . '</a>' ), $actions );

		return $actions;
	}
}
?>
