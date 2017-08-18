<?php

return array(
	'title' => __('Woocommerce Product Gallery Slider Option Panel', 'wpgs'),
	'logo' => 'logo-256x256.png',
	'menus' => array(
		array(
			'title' => __('WPGS Controls', 'wpgs'),
			'name' => 'wpgs_menu',
			'icon' => 'font-awesome:fa-magic',
			'controls' => array(
				
				array(
					'type' => 'select',
					'name' => 'twist_wc3',
					'label' => __('Woocommerce 3.0+ Fix', 'twist'),
					'description' => __('Only "Use" it If you Are using Woocommerce 3.0 or later version ', 'vp_textdomain'),
					'items' => array(
							array(
								'value' => 'true',
								'label' => __('Yes', 'twist'),
							),
							array(
								'value' => 'false',
								'label' => __('No', 'twist'),
							),
							
						),
						'default' => array(
							'false',
						),
					
				),
				array(
					'type' => 'section',
					'title' => __('', 'wpgs'),
					'fields' => array(
						
						
						
						array(
							'type' => 'radiobutton',
							'name' => 'wpgs_nav',
							'label' => __('Navigation ', 'wpgs'),
							'description' => __('Defalut : Yes', 'wpgs'),
							
							'items' => array(								
								array(
									'value' => 'true',
									'label' => __('Yes', 'wpgs'),
								),
								array(
									'value' => 'false',
									'label' => __('No', 'wpgs'),
								),
							),
							'default' => array(
								'true',
							),
						),
						
						array(
							'type' => 'radiobutton',
							'name' => 'wpgs_nav_infinite',
							'label' => __('Infinite Loop ', 'wpgs'),
							'description' => __('Defalut : No', 'wpgs'),
							
							'items' => array(								
								array(
									'value' => 'true',
									'label' => __('Yes', 'wpgs'),
								),
								array(
									'value' => 'false',
									'label' => __('No', 'wpgs'),
								),
							),
							'default' => array(
								'false',
							),
						),
						array(
							'type' => 'color',
							'name' => 'wpgs_nav_cl',
							'label' => __('Navigation Button Background Color', 'wpgs'),
							'description' => __(' ', 'wpgs'),
							'default' => '#ff6b00',
						),
						array(
							'type' => 'color',
							'name' => 'wpgs_nav_ico_cl',
							'label' => __('Navigation Icon Color', 'wpgs'),
							'description' => __(' ', 'wpgs'),
							'default' => '#fff',
						),
						array(
							'type' => 'radiobutton',
							'name' => 'wpgs_autoplay',
							'label' => __('AutoPlay ', 'wpgs'),
							'description' => __('Defalut : No', 'wpgs'),
							
							'items' => array(								
								array(
									'value' => 'true',
									'label' => __('Yes', 'wpgs'),
								),
								array(
									'value' => 'false',
									'label' => __('No', 'wpgs'),
								),
							),
							'default' => array(
								'false',
							),
						),
						
						array(
							'type' => 'textbox',
							'name' => 'wpgs_thumbanils',
							'label' => __('Thumbnails To Show', 'wpgs'),
							'description' => __('', 'wpgs'),
							'default' => '4',
							'validation' => 'numeric',
						),
						
						array(
							'type' => 'textbox',
							'name' => 'wpgs_thum_margin',
							'label' => __('Space Beetween Thumbnails', 'wpgs'),
							'description' => __('', 'wpgs'),
							'default' => '10',
							'validation' => 'numeric',
						),
						
						array(
							'type' => 'slider',
							'name' => 'wpgs_stagepadding',
							'label' => __('Thubnails Half Effect for First and Last Item', 'wpgs'),
							'description' => __('Add the % too Display', 'wpgs'),
							'default' => '10',
							'min' => '10',
							'max' => '90'
 							
						),
						
						
					),
				),
				array(
					'type' => 'notebox',
					'name' => 'wpgs_nb_1',
					'label' => __(' ', 'wpgs'),
					'description' => __('<p style="font-size:17px">The premium version of this plugin is available on Codecayon <a target="_blank" href="http://codecanyon.net/item/twist-product-gallery-slidercarousel-plugin-for-woocommerce/14849108?ref=NiloySarker">Buy Now</a></p>', 'wpgs'),
					'status' => 'normal',
				),
				array(
					'type' => 'notebox',
					'name' => 'wpgs_nb_2',
					'label' => __(' ', 'wpgs'),
					'description' => __('<p style="font-size:15px">For Plugin Support  <a target="_blank" href="https://wordpress.org/support/plugin/woo-product-gallery-slider">Click Here</a></p>', 'wpgs'),
					'status' => 'info',
				),
				array(
					'type' => 'notebox',
					'name' => 'wpgs_nb_1',
					'label' => __(' ', 'wpgs'),
					'description' => __('<p style="font-size:15px">Please write a review on <a target="_blank" href="https://wordpress.org/support/plugin/woo-product-gallery-slider/reviews/#new-post">wordpress.org</a>  if you found this plugin helpful,Thanks.</p>', 'wpgs'),
					'status' => 'success',
				),
			),
		)

	)
);

/**
 *EOF
 */