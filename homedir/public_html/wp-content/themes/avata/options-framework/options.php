<?php
global $avata_sections, $avata_sidebars;
$option_name = avata_get_option_name();

$args['repeat'] = $repeat = array(
	'no-repeat'   => esc_attr__( 'No Repeat', 'avata' ),
	'repeat-x' => esc_attr__( 'Repeat Horizontally', 'avata' ),
	'repeat-y'   => esc_attr__( 'Repeat Vertically', 'avata' ),
	'repeat' => esc_attr__( 'Repeat All', 'avata' ),
	);
	
$args['position'] = $position = array(
	'top left'   => esc_attr__( 'Top Left', 'avata' ),
	'top center' => esc_attr__( 'Top Center', 'avata' ),
	'top right'   => esc_attr__( 'Top Right', 'avata' ),
	'center left' => esc_attr__( 'Middle Left', 'avata' ),
	'center center' => esc_attr__( 'Middle Center', 'avata' ),
	'center right' => esc_attr__( 'Middle Right', 'avata' ),
	'bottom left' => esc_attr__( 'Bottom Left', 'avata' ),
	'bottom center' => esc_attr__( 'Bottom Center', 'avata' ),
	'bottom right' => esc_attr__( 'Bottom Right', 'avata' ),
	);
	
$args['attachment'] = $attachment = array(
	'scroll'   => esc_attr__( 'Scroll Normally', 'avata' ),
	'fixed' => esc_attr__( 'Fixed in Place', 'avata' ),
	);

$args['fonts'] = $fonts = array(
	'Arial, sans-serif'   => esc_attr__( 'Arial', 'avata' ),
	'"Avant Garde", sans-serif' => esc_attr__( 'Avant Garde', 'avata' ),
	'Cambria, Georgia, serif' => esc_attr__( 'Cambria', 'avata' ),
	'Copse, sans-serif' => esc_attr__( 'Copse', 'avata' ),
	'Garamond, "Hoefler Text", Times New Roman, Times, serif' => esc_attr__( 'Garamond', 'avata' ),
	'Georgia, serif' => esc_attr__( 'Georgia', 'avata' ),
	'"Helvetica Neue", Helvetica, sans-serif' => esc_attr__( 'Helvetica Neue', 'avata' ),
	'Tahoma, Geneva, sans-serif' => esc_attr__( 'Tahoma', 'avata' ),
	);
	
// Pull all the categories into an array
	$options_categories = array();
	$options_categories_obj = get_categories();
	$options_categories[''] = __( 'All', 'avata' );
	foreach ($options_categories_obj as $category) {
		$options_categories[$category->cat_ID] = $category->cat_name;
	}

$avata_sidebars['0'] = __( '--Disable--', 'avata' );
for( $i=1;$i<=8;$i++ ):
	$avata_sidebars['avata-sidebar-'.$i] = sprintf( __( 'Sidebar %d', 'avata' ), $i);
endfor;

$font_size = array();
for($i=9;$i<=71;$i++){
	$font_size[$i.'px'] = $i.'px';
}

$args['font_size'] = $font_size;
$args['choices']   = $choices   = array('yes'=>esc_attr__( 'Yes', 'avata' ),'no'=>esc_attr__( 'No', 'avata' ));
$args['target']    = $target    = array('_self'=>esc_attr__( 'Self', 'avata' ),'_blank'=>esc_attr__( 'Blank', 'avata' ));
$args['imagepath'] = $imagepath =  get_template_directory_uri() . '/assets/images/';

$options         = get_option($option_name);
$default_options = array();
$nav_icon_style  = 'css3';
$avata_lite_sections = array();
$is_old_version  = false;
if($options)
	$is_old_version  = true;


function avata_public_section_options($id,$default,$custom = false,$args ){
	
	extract($args);
	
	$default_options = array_merge(array(
						  'hide' => $hide,
						  'section_title' => '',
						  'section_subtitle' => '',
						  'fullwidth' => '',
						  'autoheight' => '',
						  'menu_title'  => '',
						  'menu_slug' => 'section-'.$id,
						  'font_size' => '14px',
						  'font' => 'Open Sans, sans-serif',
						  'font_color' => '#666666',
						  'background_color' => '',
						  'background_opacity' => '1',
						  'background_image' => '',
						  'background_repeat' => 'repeat',
						  'background_position' => 'top left',
						  'background_attachment' => 'scroll',
						  'background_color_tablet' => '',
						  'background_image_tablet' => '',
						  'background_repeat_tablet' => 'repeat',
						  'background_position_tablet' => 'top left',
						  'background_attachment_tablet' => 'scroll',
						  'background_color_mobile' => '',
						  'background_image_mobile' => '',
						  'background_repeat_mobile' => 'repeat',
						  'background_position_mobile' => 'top left',
						  'background_attachment_mobile' => 'scroll',
						  'full_background_image' => 'yes',
						  'css_class' => '',
						  'id' => '',
						  'content_background_image' => '',
						  'content_background_image_link' => '',
						  'content_background_image_target' => '_blank',
						  'content_background_color' => '',
						  'content_background_opacity' => '1',
						  'content_box_border_radius' => '',
						  'content' => '',
						  'padding_top' => '100px',
						  'padding_bottom' => '100px',
				),$default);

	
	$return = array(
												
			  'section_hide_'.$id => array(
					'type'        => 'checkbox',
					'label'       => esc_attr__('Hide Section', 'avata' ),
					'description' =>  '',
					'default'     => $default_options['hide'],
					  ),

			  'section_title_'.$id => array(
					'type'        => 'text',
					'label'       => esc_attr__('Section Title', 'avata' ),
					'description' =>  '',
					'default'     => $default_options['section_title'],
					  ),

			  'section_subtitle_'.$id => array(
					'type'        => 'text',
					'label'       => esc_attr__('Section Subitle', 'avata' ),
					'description' =>  '',
					'default'     => $default_options['section_subtitle'],
					  ),
			  'section_content_'.$id => array(
					'type'        => 'textarea',
					'label'       => esc_attr__('Section Content', 'avata' ),
					'description' =>  '',
					'default'     => $default_options['content'],
					  ),
					  
			  'section_fullwidth_'.$id => array(
					'type'        => 'checkbox',
					'label'       => esc_attr__('Full Width', 'avata' ),
					'description' =>  '',
					'default'     => $default_options['fullwidth'],
					  ),
			   'section_autoheight_'.$id => array(
					'type'        => 'checkbox',
					'label'       => esc_attr__('Auto Height', 'avata' ),
					'description' =>  __('It will take the height defined by your section/slide content.', 'avata' ),
					'default'     => $default_options['autoheight'],
					  ),
			  'font_size_'.$id => array(
					'type'        => 'select',
					'label'       => esc_attr__( 'Font Size', 'avata' ),
					'description' =>  esc_attr__( 'Section content font size.', 'avata' ),
					'default'     => $default_options['font_size'],
					'choices'     => $font_size
					  ),
			  'font_'.$id => array(
					'type'        => 'select',
					'label'       => esc_attr__( 'Content Font', 'avata' ),
					'description' =>  esc_attr__( 'Section content font.', 'avata' ),
					'default'     => $default_options['font'],
					'choices'     => $fonts
			  ),
			  'font_color_'.$id => array(
					'type'        => 'color',
					'label'       => esc_attr__( 'Font Color', 'avata' ),
					'description' =>  esc_attr__( 'Section content font color.', 'avata' ),
					'default'     => $default_options['font_color'],
			  ),
			  
			  'background_color_'.$id => array(
					'type'        => 'color',
					'label'       => esc_attr__( 'Background Color', 'avata' ),
					'description' =>  esc_attr__( 'Section background color.', 'avata' ),
					'default'     => $default_options['background_color'],
			  ),
			  'background_opacity_'.$id => array(
					'type'        => 'select',
					'label'       => esc_attr__( 'Background Opacity', 'avata' ),
					'description' =>  esc_attr__( 'Section background color opacity.', 'avata' ),
					'default'     => $default_options['background_opacity'],
					'choices'     => array_combine(range(0.1,1,0.1), range(0.1,1,0.1))
			  ),
			  
			  'background_image_'.$id => array(
					'type'        => 'image',
					'label'       => esc_attr__( 'Background Image', 'avata' ),
					'description' =>  esc_attr__( 'Section background image.', 'avata' ),
					'default'     => $default_options['background_image'],
			  ),
			  'background_repeat_'.$id => array(
					'type'        => 'select',
					'label'       => esc_attr__( 'Background Repeat', 'avata' ),
					'description' =>  '',
					'default'     => $default_options['background_repeat'],
					'choices'     => $repeat
			  ),
			  'background_position_'.$id => array(
					'type'        => 'select',
					'label'       => esc_attr__( 'Background Position', 'avata' ),
					'description' =>  '',
					'default'     => $default_options['background_position'],
					'choices'     => $position
			  ),
			  'background_attachment_'.$id => array(
					'type'        => 'select',
					'label'       => esc_attr__( 'Background Attachment', 'avata' ),
					'description' =>  '',
					'default'     => $default_options['background_attachment'],
					'choices'     => $attachment
			  ),
			  
			  'full_background_image_'.$id => array(
					'type'        => 'select',
					'label'       => esc_attr__( '100% Width Background Image', 'avata' ),
					'description' =>  '',
					'default'     => $default_options['full_background_image'],
					'choices'     => $choices
			  ),
			  
			  'section_css_class_'.$id => array(
					'type'        => 'text',
					'label'       => esc_attr__( 'Css Class', 'avata' ),
					'description' =>  '',
					'default'     => $default_options['css_class'],
			  ),
			  'section_id_'.$id => array(
					'type'        => 'text',
					'label'       => esc_attr__( 'Section ID', 'avata' ),
					'description' =>  '',
					'default'     => $default_options['menu_slug'],
			  ),
			  'content_background_image_'.$id => array(
					'type'        => 'image',
					'label'       => esc_attr__( 'Content Background Image', 'avata' ),
					'description' =>  '',
					'default'     => $default_options['content_background_image'],
			  ),
			  'content_background_image_link_'.$id => array(
					'type'        => 'text',
					'label'       => esc_attr__( 'Content Background Image Link', 'avata' ),
					'description' =>  '',
					'default'     => $default_options['content_background_image_link'],
			  ),
			  'section_image_link_target_'.$id => array(
					'type'        => 'select',
					'label'       => esc_attr__( 'Content Background Image Link Target', 'avata' ),
					'description' =>  '',
					'default'     => $default_options['content_background_image_target'],
					'choices'     => $target
			  ),
			  
			  'content_background_color_'.$id => array(
					'type'        => 'color',
					'label'       => esc_attr__( 'Content Box Background Color', 'avata' ),
					'description' =>  '',
					'default'     => $default_options['content_background_color'],
			  ),
			  'content_background_opacity_'.$id => array(
					'type'        => 'select',
					'label'       => esc_attr__( 'Opacity', 'avata' ),
					'description' =>  '',
					'default'     => $default_options['content_background_opacity'],
					'choices'     => array_combine(range(0.1,1,0.1), range(0.1,1,0.1))
			  ),
			  
			  'content_box_border_radius_'.$id => array(
					'type'        => 'text',
					'label'       => esc_attr__( 'Content Box Border Radius', 'avata' ),
					'description' =>  '',
					'default'     => $default_options['content_box_border_radius'],
			  ),
			  
			  'padding_top_'.$id => array(
					'type'        => 'text',
					'label'       => esc_attr__( 'Padding Top ( Valid under Auto Height )', 'avata' ),
					'description' => '',
					'default'     => $default_options['padding_top'],
			  ),
			  'padding_bottom_'.$id => array(
					'type'        => 'text',
					'label'       => esc_attr__( 'Padding Bottom ( Valid under Auto Height )', 'avata' ),
					'description' => '',
					'default'     => $default_options['padding_bottom'],
			  ),
			  'hide_side_menu_'.$id => array(
					'type'        => 'checkbox',
					'label'       => esc_attr__( 'Hide Side Menu Dot', 'avata' ),
					'description' => '',
					'default'     => '',
			  ),
			  
	  );
	  
	if (!$custom){
		unset($return['section_content_'.$id]);
	}
	
	if ( isset($excludes) && !empty($excludes) ){
		foreach($excludes as $exclude){
			if(isset($return[$exclude.'_'.$id]))
				unset($return[$exclude.'_'.$id]);
			}
		}	
	return $return;
	
	}

// front page sections
$args['hide'] = '';
if ($is_old_version){
	$hide = '1';
	}else{
	$hide = '';
}

$defaults = array();

// section banner 1

$banner_defaults = array(
			'menu_slug' => 'banner',
			);

$banner_args = $args;
$banner_args['excludes'] = array('section_title','section_subtitle','section_fullwidth','font_color','font_size');
$banner_1_options = avata_public_section_options('banner_1',$banner_defaults,false,$banner_args);

array_splice($banner_1_options,1,0,array('section_slider_banner_1' => array(
													'type'        => 'repeater',
													'label'       => esc_attr__( 'Slider', 'avata' ),
													'section'     => 'section_banner_1',
													'priority'    => 10,
													'row_label' => array(
														'type' => 'text',
														'value' => esc_attr__('Slide', 'avata' ),
														'field' => 'icon',
													),
													'settings'    => 'section_slider_banner_1',
													'default'     => array(
														array(
															'image'  => $imagepath.'slide-1.jpg',
															'title'  => __('Online Support', 'avata' ),
															'description'  => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut <br/>labore et dolore magna aliqua. ',
															'font_color' => '#666',
															'left_btn_text' => __('Get Started', 'avata' ),
															'left_btn_link' => '#',
															'left_btn_target' => '_blank',
															'right_btn_text' => __('View Plans', 'avata' ),
															'right_btn_link' => '#',
															'right_btn_target' => '_blank',
															
														),
														array(
															'image'  => $imagepath.'slide-2.jpg',
															'title'  => __('Web Development', 'avata' ),
															'description'  => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut <br/>labore et dolore magna aliqua. ',
															'font_color' => '#fff',
															'left_btn_text' => __('Get Started', 'avata' ),
															'left_btn_link' => '#',
															'left_btn_target' => '_blank',
															'right_btn_text' => __('View Plans', 'avata' ),
															'right_btn_link' => '#',
															'right_btn_target' => '_blank',
														),
														array(
															'image'  => $imagepath.'slide-3.jpg',
															'title'  => __('Simple, Beautiful and Amazing', 'avata' ),
															'description'  => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut <br/>labore et dolore magna aliqua. ',
															'font_color' => '#fff',
															'left_btn_text' => __('Get Started', 'avata' ),
															'left_btn_link' => '#',
															'left_btn_target' => '_blank',
															'right_btn_text' => __('View Plans', 'avata' ),
															'right_btn_link' => '#',
															'right_btn_target' => '_blank',
														),
														
													),
													'fields' => array( 
														
														'image' => array(
															'type'        => 'image',
															'label'       => esc_attr__( 'Image', 'avata' ),
															'description' => '',
															'default'     => '',
														),
														'title' => array(
															'type'        => 'text',
															'label'       => esc_attr__( 'Title', 'avata' ),
															'description' => '',
															'default'     => '',
														),
														'description' => array(
															'type'        => 'textarea',
															'label'       => esc_attr__( 'Description', 'avata' ),
															'description' => '',
															'default'     => '',
														),
														'font_color' => array(
															'type'        => 'color',
															'label'       => esc_attr__( 'Font Color', 'avata' ),
															'description' => '',
															'default'     => '',
														),
														
														'left_btn_text' => array(
															'type'        => 'text',
															'label'       => esc_attr__( 'Left Button Text', 'avata' ),
															'description' => '',
															'default'     => '',
														),
														'left_btn_link' => array(
															'type'        => 'text',
															'label'       => esc_attr__( 'Left Button Link', 'avata' ),
															'description' => '',
															'default'     => '',
														),
														'left_btn_target' => array(
															'type'        => 'select',
															'label'       => esc_attr__( 'Left Button Target', 'avata' ),
															'description' => '',
															'default'     => '_blank',
															'choices'     => $target
														),
														
														'right_btn_text' => array(
															'type'        => 'text',
															'label'       => esc_attr__( 'Right Button Text', 'avata' ),
															'description' => '',
															'default'     => '',
														),
														'right_btn_link' => array(
															'type'        => 'text',
															'label'       => esc_attr__( 'Right Button Link', 'avata' ),
															'description' => '',
															'default'     => '',
														),
														'right_btn_target' => array(
															'type'        => 'select',
															'label'       => esc_attr__( 'Right Button Target', 'avata' ),
															'description' => '',
															'default'     => '_blank',
															'choices'     => $target
														),
														
														
													),
													),
													'section_autoplay_banner_1' => array(
													  'type'        => 'checkbox',
													  'settings'    => 'section_autoplay_banner_1',
													  'label'       => esc_attr__( 'Auto Paly', 'avata' ),
													  'description' => '',
													  'default'     => '1',
												  ),
												  'section_timeout_banner_1' => array(
													  'type'        => 'text',
													  'settings'    => 'section_timeout_banner_1',
													  'label'       => esc_attr__( 'Auto Paly Timeout', 'avata' ),
													  'description' => '',
													  'default'     => '3000',
												  ),
													
													));

$avata_lite_sections['section-banner-1'] = array(
										'name'=> __('Section Slider Banner', 'avata'),
										'fields'=> $banner_1_options 
										);


// section banner 2

$banner_2_defaults = array(
			'section_title'=> __('FULL-SCREEN VIDEO BACKGROUND', 'avata'),
			'section_subtitle'=> 'Lorem ipsum dolor sit amet, consectetur adipiscing elit sed do eiusmod.',
			'menu_slug' => 'banner-video',
			'hide' => 1,
			'background_image' => $imagepath.'maxresdefault.jpg'
			);

$banner_args = $args;
$banner_args['excludes'] = array('section_fullwidth','section_autoheight','font_color','font_size');
$banner_2_options = avata_public_section_options('banner_2',$banner_2_defaults,false,$banner_args);

array_splice($banner_2_options,3,0,array(
										'section_videourl_banner_2' => array(
												  'type'        => 'text',
												  'settings'    => 'section_videourl_banner_2',
												  'label'       => esc_attr__( 'YouTube Video URL(Pro Version)', 'avata' ),
												  'description' => '',
												  'default'     => 'https://www.youtube.com/embed/V-FgQ2NAGFc?rel=0&amp;autoplay=0&amp;loop=0',
										),
										
										'section_btntxt_1_banner_2' => array(
												  'type'        => 'text',
												  'settings'    => 'section_btntxt_1_banner_2',
												  'label'       => esc_attr__( 'Left Button Text', 'avata' ),
												  'description' => '',
												  'default'     => esc_attr__( 'DOWNLOAD FOR WINDOWS', 'avata' ),
										),
										'section_btnlink_1_banner_2' => array(
												  'type'        => 'text',
												  'settings'    => 'section_btnlink_1_banner_2',
												  'label'       => esc_attr__( 'Button Link', 'avata' ),
												  'description' => '',
												  'default'     => 'http://',
										),
										'section_btntarget_1_banner_2' => array(
												  'type'        => 'select',
												  'settings'    => 'section_btntarget_1_banner_2',
												  'label'       => esc_attr__( 'Link Target', 'avata' ),
												  'description' => '',
												  'default'     => '_blank',
												  'choices'     => $target
										),
										
										'section_btntxt_2_banner_2' => array(
												  'type'        => 'text',
												  'settings'    => 'section_btntxt_2_banner_2',
												  'label'       => esc_attr__( 'Right Button Text', 'avata' ),
												  'description' => '',
												  'default'     => esc_attr__( 'DOWNLOAD FOR MAC', 'avata' ),
										),
										'section_btnlink_2_banner_2' => array(
												  'type'        => 'text',
												  'settings'    => 'section_btnlink_2_banner_2',
												  'label'       => esc_attr__( 'Button Link', 'avata' ),
												  'description' => '',
												  'default'     => 'http://',
										),
										'section_btntarget_2_banner_2' => array(
												  'type'        => 'select',
												  'settings'    => 'section_btntarget_2_banner_2',
												  'label'       => esc_attr__( 'Link Target', 'avata' ),
												  'description' => '',
												  'default'     => '_blank',
												  'choices'     => $target
										),
										'section_display_arrow_banner_2' => array(
												  'type'        => 'checkbox',
												  'settings'    => 'section_display_arrow_banner_2',
												  'label'       => esc_attr__( 'Display Arrow Button', 'avata' ),
												  'description' => '',
												  'default'     => '1',
										),
													
	));

$avata_lite_sections['section-banner-2'] = array(
										'name'=> __('Section Video Background Banner(Pro)', 'avata'),
										'fields'=> $banner_2_options 
										);

// section service 1

$service_1_defaults = array(
			'section_title'=> __('Why Choose Us', 'avata'),
			'section_subtitle'=> 'Nullam porttitor, turpis lacinia euismod efficitur,<br/> nisl arcu imperdiet ligula',
			'menu_slug' => 'service'
			);

$service_1_options = avata_public_section_options('service_1',$service_1_defaults,false,$args);

array_splice($service_1_options,3,0,array('section_items_service_1' => array(
													'type'        => 'repeater',
													'label'       => esc_attr__( 'Service Items', 'avata' ),
													'section'     => 'section_service_1',
													'priority'    => 10,
													'row_label' => array(
														'type' => 'text',
														'value' => esc_attr__('Service', 'avata' ),
														'field' => 'icon',
													),
													'settings'    => 'section_items_service_1',
													'default'     => array(
														array(
															'icon' =>  'weixin',
															'image'  => '',
															'title'  => __('Online Support', 'avata' ),
															'description'  => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. ',
															
														),
														array(
															'icon' => 'bar-chart',
															'image'  => '',
															'title'  => __('Web Development', 'avata' ),
															'description'  => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. ',
														),
														array(
															'icon' => 'windows',
															'image'  => '',
															'title'  => __('Responsive Design', 'avata' ),
															'description'  => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. ',
														),
													),
													'fields' => array(
														'icon' => array(
															'type'        => 'text',
															'label'       => esc_attr__( 'Font Awesome Icon', 'avata' ),
															'description' => '',
															'default'     => '',
														),
														'image' => array(
															'type'        => 'image',
															'label'       => esc_attr__( 'Image Icon', 'avata' ),
															'description' => '',
															'default'     => '',
															'choices' => array('save_as'=>'url')
															
														),
														'title' => array(
															'type'        => 'text',
															'label'       => esc_attr__( 'Title', 'avata' ),
															'description' => '',
															'default'     => '',
														),
														'description' => array(
															'type'        => 'textarea',
															'label'       => esc_attr__( 'Description', 'avata' ),
															'description' => '',
															'default'     => '',
														),
													),
													)));

$avata_lite_sections['section-service-1'] = array(
										'name'=> __('Section Features Style 1', 'avata'),
										'fields'=> $service_1_options
										);
										
										
// section video 1

$video_1_defaults = array(
			'section_title'=> __('Watch the best Technology in <span>Action</span>', 'avata'),
			'section_subtitle'=> 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc eget nunc vitae tellus luctus ullamcorper. Nam porttitor ullamcorper felis at convallis. Aenean ornare vestibulum nisi fringilla lacinia. Nullam pulvinar sollicitudin velit id laoreet. Quisque non rhoncus sem.',
			'menu_slug' => 'video-1',
			'background_image' => $imagepath.'video.jpg',
			'background_color' => '#539ebc',
			'background_opacity' => '0.6',
			'font_color' => '#ffffff',
			'padding_top' => '150px',
			'padding_bottom' => '120px',
			'autoheight' => '1',
			);

$video_1_options = avata_public_section_options('video_1',$video_1_defaults,false,$args);

array_splice($video_1_options,3,0,array('section_url_video_1' => array(
												  'type'        => 'text',
												  'settings'    => 'section_url_video_1',
												  'label'       => esc_attr__( 'Youtube or Vimeo Video URL', 'avata' ),
												  'description' => '',
												  'default'     => 'https://www.youtube.com/watch?v=meBbDqAXago',
														
													)
));

$avata_lite_sections['section-video-1'] = array(
										'name'=> __('Section Video', 'avata'),
										'fields'=> $video_1_options
										);

// section intro 1

$intro_1_defaults = array(
			'section_title'=> __('We create Awesome Branding', 'avata'),
			'section_subtitle'=> 'Nullam porttitor, turpis lacinia euismod efficitur',
			'fullwidth' => '1',
			'autoheight' => '1',
			'padding_top' => '0',
			'padding_bottom' => '0',
			'menu_slug' => 'about-us',
			'background_color' => '#f9f9f9',
			);

$intro_1_options = avata_public_section_options('intro_1',$intro_1_defaults,false,$args);

array_splice($intro_1_options,3,0,
		array(
		'section_layout_intro_1' => array(
				  'type'        => 'select',
				  'settings'    => 'section_layout_intro_1',
				  'label'       => esc_attr__( 'Layout', 'avata' ),
				  'description' => '',
				  'default'     => '0',
				  'choices'     => array('0'=>__( 'Left Image Right Text', 'avata' ),'1'=>__( 'Left Text Right Image', 'avata' ))
		),
		'section_image_intro_1' => array(
				  'type'        => 'image',
				  'settings'    => 'section_image_intro_1',
				  'label'       => esc_attr__( 'Image', 'avata' ),
				  'description' => '',
				  'default'     => $imagepath.'intro-1.jpg',
		),
		'section_content_intro_1' => array(
				  'type'        => 'editor',
				  'settings'    => 'section_content_intro_1',
				  'label'       => esc_attr__( 'Content', 'avata' ),
				  'description' => '',
				  'default'     => '<p>Susan Sims, Interaction Designer at XYZCras mattis consectetur purus sit amet fermentum. Donec sed odio dui. Aenean lacinia bibendum nulla sed consectetur.Cras mattis consectetur purus sit amet fermentum.Interaction Designer at XYZCras mattis consectetur purus sit amet fermentum.</p><p></p><ul class="list-nav">
								<li><i class="fa fa-check"></i> Far far away, behind the word</li>
								<li><i class="fa fa-check"></i>There live the blind texts</li>
								<li><i class="fa fa-check"></i>Separated they live in bookmarksgrove</li>
								<li><i class="fa fa-check"></i>Semantics a large language ocean</li>
								<li><i class="fa fa-check"></i>A small river named Duden</li>
							</ul>',
		)

));

$avata_lite_sections['section-intro-1'] = array(
										'name'=> __('Section Intro', 'avata'),
										'fields'=> $intro_1_options 
										);
										
										
										
// section gallery

$gallery_defaults = array(
			'section_title'=> __('Gallery', 'avata'),
			'section_subtitle'=> 'Vivamus tincidunt cursus mi pretium tristique',
			'fullwidth' => '1',
			'menu_slug' => 'gallery'
			);

$gallery_options = avata_public_section_options('gallery',$gallery_defaults,false,$args);

array_splice($gallery_options,3,0,array('section_items_gallery' => array(
													'type'        => 'repeater',
													'label'       => esc_attr__( 'Gallery Items', 'avata' ),
													'section'     => 'section_gallery',
													'priority'    => 10,
													'row_label' => array(
														'type' => 'text',
														'value' => esc_attr__('Image', 'avata' ),
														'field' => 'icon',
													),
													'settings'    => 'section_items_gallery',
													'default'     => array(
														array('image'  => $imagepath.'work-1.jpg', 'title'=> sprintf(__( 'Gallery Items %d', 'avata' ),1 )),
														array('image'  => $imagepath.'work-2.jpg', 'title'=> sprintf(__( 'Gallery Items %d', 'avata' ),2 )),
														array('image'  => $imagepath.'work-3.jpg', 'title'=> sprintf(__( 'Gallery Items %d', 'avata' ),3 )),
														array('image'  => $imagepath.'work-4.jpg', 'title'=> sprintf(__( 'Gallery Items %d', 'avata' ),4 )),
														array('image'  => $imagepath.'work-5.jpg', 'title'=> sprintf(__( 'Gallery Items %d', 'avata' ),5 )),
														array('image'  => $imagepath.'work-6.jpg', 'title'=> sprintf(__( 'Gallery Items %d', 'avata' ),6 )),
														array('image'  => $imagepath.'work-7.jpg', 'title'=> sprintf(__( 'Gallery Items %d', 'avata' ),7 )),
														array('image'  => $imagepath.'work-8.jpg', 'title'=> sprintf(__( 'Gallery Items %d', 'avata' ),8 )),
														array('image'  => $imagepath.'work-9.jpg', 'title'=> sprintf(__( 'Gallery Items %d', 'avata' ),9 )),
														array('image'  => $imagepath.'work-10.jpg', 'title'=> sprintf(__( 'Gallery Items %d', 'avata' ),10 )),
														array('image'  => $imagepath.'work-11.jpg', 'title'=> sprintf(__( 'Gallery Items %d', 'avata' ),11)),
														array('image'  => $imagepath.'work-12.jpg', 'title'=> sprintf(__( 'Gallery Items %d', 'avata' ),12 )),
													),
													'fields' => array(
				
														'image' => array(
															'type'        => 'image',
															'label'       => esc_attr__( 'Image', 'avata' ),
															'description' => '',
															'default'     => '',
														),
														'title' => array(
															'type'        => 'textarea',
															'label'       => esc_attr__( 'Description', 'avata' ),
															'description' => '',
															'default'     => '',
														),
											
													),
													)));

$avata_lite_sections['section-gallery'] = array(
										'name'=> __('Section Gallery', 'avata'),
										'fields'=> $gallery_options 
										);
										

// section team

$team_defaults = array(
			'section_title'=> __('Our Team', 'avata'),
			'section_subtitle'=> 'Vivamus tincidunt cursus mi pretium tristique',
			'menu_slug' => 'team'
			);

$team_options = avata_public_section_options('team',$team_defaults,false,$args);

		  
array_splice($team_options,3,0,array('section_items_team' => array(
													'type'        => 'repeater',
													'label'       => esc_attr__( 'Members', 'avata' ),
													'section'     => 'section_team',
													'priority'    => 10,
													'row_label' => array(
														'type' => 'text',
														'value' => esc_attr__('Member', 'avata' ),
														'field' => 'icon',
													),
													'settings'    => 'section_items_team',
													'default'     => array(
														array(
															'avatar'  => $imagepath.'img-380x380.png',
															'name'  => 'Johnathan Doe',
															'role'  => 'The Mastermind',
															'description'  => 'Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.',
															'social_icon_1' => 'facebook',
															'social_link_1' => '#',
															'social_icon_2' => 'twitter',
															'social_link_2' => '#',
															'social_icon_3' => 'linkedin',
															'social_link_3' => '#',
															'social_icon_4' => 'google-plus',
															'social_link_4' => '#',
															'social_icon_5' => 'dribbble',
															'social_link_5' => '#',
														),
														array(
															'avatar'  => $imagepath.'img-380x380.png',
															'name'  => 'Lisa Brown',
															'role'  => 'Creative head',
															'description'  => 'Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.',
															'social_icon_1' => 'facebook',
															'social_link_1' => '#',
															'social_icon_2' => 'twitter',
															'social_link_2' => '#',
															'social_icon_3' => 'linkedin',
															'social_link_3' => '#',
															'social_icon_4' => 'google-plus',
															'social_link_4' => '#',
															'social_icon_5' => 'dribbble',
															'social_link_5' => '#',
														),
														array(
															'avatar'  => $imagepath.'img-380x380.png',
															'name'  => 'Mike Collins',
															'role'  => 'Technical lead',
															'description'  => 'Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.',
															'social_icon_1' => 'facebook',
															'social_link_1' => '#',
															'social_icon_2' => 'twitter',
															'social_link_2' => '#',
															'social_icon_3' => 'linkedin',
															'social_link_3' => '#',
															'social_icon_4' => 'google-plus',
															'social_link_4' => '#',
															'social_icon_5' => 'dribbble',
															'social_link_5' => '#',
														),
								
													),
													'fields' => array(
				
														'avatar' => array(
															'type'        => 'image',
															'label'       => esc_attr__( 'Avatar', 'avata' ),
															'description' => '',
															'default'     => '',
														),
														'name' => array(
															'type'        => 'text',
															'label'       => esc_attr__( 'Name', 'avata' ),
															'description' => '',
															'default'     => '',
														),
														'role' => array(
															'type'        => 'text',
															'label'       => esc_attr__( 'Role', 'avata' ),
															'description' => '',
															'default'     => '',
														),
														'description' => array(
															'type'        => 'text',
															'label'       => esc_attr__( 'Description', 'avata' ),
															'description' => '',
															'default'     => '',
														),
														'social_icon_1' => array(
															'type'        => 'text',
															'label'       => esc_attr__( 'Social Icon 1', 'avata' ),
															'description' => esc_attr__( 'Get social icon string from http://fontawesome.io/icons/, e.g. facebook.', 'avata' ),
															'default'     => '',
														),
														'social_link_1' => array(
															'type'        => 'text',
															'label'       => esc_attr__( 'Social Link 1', 'avata' ),
															'description' => '',
															'default'     => 'http://',
														),
														'social_icon_2' => array(
															'type'        => 'text',
															'label'       => esc_attr__( 'Social Icon 2', 'avata' ),
															'description' => esc_attr__( 'Get social icon string from http://fontawesome.io/icons/, e.g. facebook.', 'avata' ),
															'default'     => '',
														),
														'social_link_2' => array(
															'type'        => 'text',
															'label'       => esc_attr__( 'Social Link 2', 'avata' ),
															'description' => '',
															'default'     => 'http://',
														),
														'social_icon_3' => array(
															'type'        => 'text',
															'label'       => esc_attr__( 'Social Icon 3', 'avata' ),
															'description' => esc_attr__( 'Get social icon string from http://fontawesome.io/icons/, e.g. facebook.', 'avata' ),
															'default'     => '',
														),
														'social_link_3' => array(
															'type'        => 'text',
															'label'       => esc_attr__( 'Social Link 3', 'avata' ),
															'description' => '',
															'default'     => 'http://',
														),
														'social_icon_4' => array(
															'type'        => 'text',
															'label'       => esc_attr__( 'Social Icon 4', 'avata' ),
															'description' => esc_attr__( 'Get social icon string from http://fontawesome.io/icons/, e.g. facebook.', 'avata' ),
															'default'     => '',
														),
														'social_link_4' => array(
															'type'        => 'text',
															'label'       => esc_attr__( 'Social Link 4', 'avata' ),
															'description' => '',
															'default'     => 'http://',
														),
														'social_icon_5' => array(
															'type'        => 'text',
															'label'       => esc_attr__( 'Social Icon 5', 'avata' ),
															'description' => esc_attr__( 'Get social icon string from http://fontawesome.io/icons/, e.g. facebook.', 'avata' ),
															'default'     => '',
														),
														'social_link_5' => array(
															'type'        => 'text',
															'label'       => esc_attr__( 'Social Link 5', 'avata' ),
															'description' => '',
															'default'     => 'http://',
														),
																									
													),
													)));

$avata_lite_sections['section-team'] = array(
										'name'=> __('Section Team', 'avata'),
										'fields'=> $team_options 
										);		
																	
										

// section testimonial

$testimonial_defaults = array(
			'section_title'=> '',
			'section_subtitle'=> '',
			'background_color'=>'#f1faff',
			'autoheight' => '1',
			'menu_slug' => 'testimonial'
			);

$testimonial_options = avata_public_section_options('testimonial',$testimonial_defaults,false,$args);

		  
array_splice($testimonial_options,3,0,array('section_items_testimonial' => array(
													'type'        => 'repeater',
													'label'       => esc_attr__( 'Testimonial', 'avata' ),
													'section'     => 'section_testimonial',
													'settings'     => 'section_items_testimonial',
													'priority'    => 10,
													'row_label' => array(
														'type' => 'text',
														'value' => esc_attr__('Testimonial', 'avata' ),
														'field' => 'icon',
													),
													'settings'    => 'section_items_testimonial',
													'default'     => array(
														array(
															'avatar'  => $imagepath.'person-1.jpg',
															'name'  => 'Johnathan Doe',
															'role'  => 'The Mastermind',
															'description'  => 'Lorem ipsum dolor sit amet consectetur adipiscing elit sed tempor incididunt ut laboret tempor incididunt dolore magna consequat siad minim aliqua.',
															
														),
														array(
															'avatar'  => $imagepath.'person-2.jpg',
															'name'  => 'Lisa Brown',
															'role'  => 'Creative head',
															'description'  => 'Lorem ipsum dolor sit amet consectetur adipiscing elit sed tempor incididunt ut laboret tempor incididunt dolore magna consequat siad minim aliqua.',
															
														),
														array(
															'avatar'  => $imagepath.'person-3.jpg',
															'name'  => 'Mike Collins',
															'role'  => 'Technical lead',
															'description'  => 'Lorem ipsum dolor sit amet consectetur adipiscing elit sed tempor incididunt ut laboret tempor incididunt dolore magna consequat siad minim aliqua.',
															
														),
								
													),
													'fields' => array(
				
														'avatar' => array(
															'type'        => 'image',
															'label'       => esc_attr__( 'Avatar', 'avata' ),
															'description' => '',
															'default'     => '',
														),
														'name' => array(
															'type'        => 'text',
															'label'       => esc_attr__( 'Name', 'avata' ),
															'description' => '',
															'default'     => '',
														),
														'role' => array(
															'type'        => 'text',
															'label'       => esc_attr__( 'Role', 'avata' ),
															'description' => '',
															'default'     => '',
														),
														'description' => array(
															'type'        => 'text',
															'label'       => esc_attr__( 'Description', 'avata' ),
															'description' => '',
															'default'     => '',
														),
														
													),
													)));

$avata_lite_sections['section-testimonial'] = array(
										'name'=> __('Section Testimonial', 'avata'),
										'fields'=> $testimonial_options 
										);	
	

// section blog

$blog_defaults = array('menu_slug' => 'blog','section_title'=>'Recent From Blog','section_subtitle'=>'Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.','background_color'=>'#eeeeee','hide'=>$hide);
$blog_args = $args;
$blog_args['excludes'] = array();

$blog_options = avata_public_section_options('blog',$blog_defaults,false,$blog_args);

array_splice($blog_options,3,0,
		array(
		'section_post_num_blog' => array(
				  'type'        => 'select',
				  'settings'    => 'section_post_num_blog',
				  'label'       => esc_attr__( 'Display Posts Num', 'avata' ),
				  'description' => '',
				  'default'     => '3',
				  'choices'     => array('2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9')
		),
		'section_columns_blog' => array(
				  'type'        => 'select',
				  'settings'    => 'section_columns_blog',
				  'label'       => esc_attr__( 'Columns', 'avata' ),
				  'description' => '',
				  'default'     => '3',
				  'choices'     => array('2'=>'2','3'=>'3','4'=>'4')
		),
		'section_category_blog' => array(
				  'type'        => 'select',
				  'settings'    => 'section_category_blog',
				  'label'       => esc_attr__( 'Category', 'avata' ),
				  'description' => '',
				  'default'     => '',
				  'multiple'    => 10,
				  'choices'     => $options_categories
		),
		'section_excerpt_length' => array(
				  'type'        => 'text',
				  'settings'    => 'section_excerpt_length',
				  'label'       => esc_attr__( 'Excerpt Length', 'avata' ),
				  'description' => '',
				  'default'     => 20,
		),
		
		'section_btn_txt' => array(
				  'type'        => 'text',
				  'settings'    => 'section_btn_txt_blog',
				  'label'       => esc_attr__( 'Button Text', 'avata' ),
				  'description' => '',
				  'default'     => esc_attr__( 'View All Post', 'avata' ),
		),
		'section_btn_link_blog' => array(
				  'type'        => 'text',
				  'settings'    => 'section_btn_link_blog',
				  'label'       => esc_attr__( 'Button Link', 'avata' ),
				  'description' => '',
				  'default'     => '#',
		),
		'section_btn_target_blog' => array(
				  'type'        => 'select',
				  'settings'    => 'section_btn_target_blog',
				  'label'       => esc_attr__( 'Link Target', 'avata' ),
				  'description' => '',
				  'default'     => '_blank',
				  'choices'     => $target
		),

));

$avata_lite_sections['section-blog'] = array(
										'name'=> __('Section Blog', 'avata'),
										'fields'=> $blog_options 
										);

									
// section slogan

$slogan_defaults = array('autoheight' => '1','menu_slug' => 'slogan');
$slogan_args = $args;

$slogan_args['excludes'] = array('section_title','section_subtitle');
$slogan_options = avata_public_section_options('slogan',$slogan_defaults,false,$slogan_args);

array_splice($slogan_options,3,0,
		array(
		'section_content_slogan' => array(
				  'type'        => 'textarea',
				  'settings'    => 'section_content_slogan',
				  'label'       => esc_attr__( 'Content', 'avata' ),
				  'description' => '',
				  'default'     => 'Looking for a new brand? Let\'s work together!',
		),
		'section_btn_txt_slogan' => array(
				  'type'        => 'text',
				  'settings'    => 'section_btn_txt_slogan',
				  'label'       => esc_attr__( 'Button Text', 'avata' ),
				  'description' => '',
				  'default'     => esc_attr__( 'Get Quotation', 'avata' ),
		),
		'section_btn_link_slogan' => array(
				  'type'        => 'text',
				  'settings'    => 'section_btn_link_slogan',
				  'label'       => esc_attr__( 'Button Link', 'avata' ),
				  'description' => '',
				  'default'     => 'http://',
		),
		'section_btn_target_slogan' => array(
				  'type'        => 'select',
				  'settings'    => 'section_btn_target_slogan',
				  'label'       => esc_attr__( 'Link Target', 'avata' ),
				  'description' => '',
				  'default'     => '_blank',
				  'choices'     => $target
		),

));

$avata_lite_sections['section-slogan'] = array(
										'name'=> __('Section Slogan', 'avata'),
										'fields'=> $slogan_options 
										);




// custom frontpage sections
$args['hide']    = '1';

for( $i=1;$i<=2;$i++){
	$j = $i-1;
	if(!isset($default_options[$j]))
		$default_options[$j] = array();
	$options = avata_public_section_options($j,$default_options[$j],true,$args);
	$avata_lite_sections['section-'.$j] = array(
										'name'=>sprintf(__('Custom Section %d', 'avata'),$i),
										'fields'=> $options
										);
	
	}
											

Hoo::add_config( 'avata', array(
	'capability'    => 'edit_theme_options',
	'option_type'   => 'option',
	'option_name'   => $option_name
) );


$sortsections_saved  = get_option('avata_sortsections',true);
$avata_sections = array();
if( is_array($sortsections_saved) && !empty($sortsections_saved) ){
	foreach( $sortsections_saved as $sortsection ){
		if(isset($avata_lite_sections[$sortsection])){
			$avata_sections[$sortsection] = $avata_lite_sections[$sortsection];
		}
	}

	$avata_sections = array_merge($avata_sections,$avata_lite_sections);

}else{
	$avata_sections = 	$avata_lite_sections;
}
	
foreach( $avata_sections as $k => $v ){
	
	
	$section_id = 'avata_'.str_replace('-','_',$k);
	
	Hoo::add_section( $section_id , array(
    'title'          => $v['name'],
    'description'    => '',
    'panel'          => '', 
    'priority'       => 10,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
	) );
	

	foreach( $v['fields'] as $field_id=>$field ){
		if(!isset($field['settings']))
			$field['settings'] = $field_id;
			 
		$field['section'] = $section_id;
		$field['priority'] = '-10';
		Hoo::add_field( 'avata',$field);
			
	}
		
}

// Front page

Hoo::add_panel( 'avata_homepage_options', array(
    'priority'    => 10,
    'title'       => __( 'Front Page Options', 'avata' ),
    'description' => '',
) );

// Front Page General Options
Hoo::add_section( 'avata_frontpage_general', array(
    'title'          => __( 'General Options', 'avata'  ),
    'description'    => '',
    'panel'          => 'avata_homepage_options', 
    'priority'       => 9,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
) );

Hoo::add_field( 'avata', array(
	'type'     => 'checkbox',
	'settings' => 'autoscrolling',
	'label'    => __('Section Auto Scrolling', 'avata'),
	'description' => __('Defines whether to use the "automatic" scrolling or the "normal" one. It also has affects the way the sections fit in the browser/device window in tablets and mobile phones.', 'avata'),
	'section'  => 'avata_frontpage_general',
	'default'  => '',
	'priority' => 10,
	) );

Hoo::add_field( 'avata', array(
	'type'     => 'color',
	'settings' => 'menu_color_frontpage',
	'label'    => __('Menu Font Color', 'avata'),
	'section'  => 'avata_frontpage_general',
	'default'  => '#ffffff',
	'priority' => 10,
	) );

	
// Front Page Header
Hoo::add_section( 'avata_frontpage_header', array(
    'title'          => __( 'Front Page Header', 'avata'  ),
    'description'    => '',
    'panel'          => 'avata_homepage_options', 
    'priority'       => 10,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
) );

Hoo::add_field( 'avata', array(
	'type'     => 'checkbox',
	'settings' => 'sticky_header_frontpage',
	'label'    => __('Sticky Header', 'avata'),
	'section'  => 'avata_frontpage_header',
	'default'  => '',
	'priority' => 10,
	) );

Hoo::add_field( 'avata', array(
	'type'     => 'select',
	'settings' => 'sticky_header_opacity_frontpage',
	'label'    => __('Sticky Header Opacity', 'avata'),
	'section'  => 'avata_frontpage_header',
	'default'  => '0.4',
	'priority' => 10,
	'choices'  => array_combine(range(0.1,1,0.1), range(0.1,1,0.1))
	) );

	
// Side Navigation Styling

Hoo::add_section( 'avata_nav_styling', array(
    'title'          => __( 'Side Navigation Icon Styling', 'avata'  ),
    'description'    => '',
    'panel'          => 'avata_homepage_options', 
    'priority'       => 11,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
) );

Hoo::add_field( 'avata', array(
	'type'     => 'checkbox',
	'settings' => 'hide_side_nav',
	'label'    => __('Hide Side Navigation', 'avata'),
	'section'  => 'avata_nav_styling',
	'default'  => '',
	'priority' => 10,
	) );

Hoo::add_field( 'avata', array(
	'type'        => 'select',
	'settings'    => 'side_nav_align',
	'label'       => __( 'Side Navigation Align', 'avata' ),
	'section'     => 'avata_nav_styling',
	'default'     => 'right',
	'priority'    => 10,
	'choices'     => array(
		'left' => __( 'Left', 'avata' ),
		'right' => __( 'Right', 'avata' ),
		
	),
) );


Hoo::add_field( 'avata', array(
	'type'        => 'select',
	'settings'    => 'nav_styling_css3_styles',
	'label'       => __( 'Icon Styles', 'avata' ),
	'section'     => 'avata_nav_styling',
	'default'     => 'fillup',
	'priority'    => 10,
	'choices'     => array(
		'fillup' => __( 'Fill up', 'avata' ),
		'scaleup' => __( 'Scale up', 'avata' ),
		'stroke' => __( 'Stroke', 'avata' ),
		'fillin' => __( 'Fill in', 'avata' ),
		'circlegrow' => __( 'Circle grow', 'avata' ),
		'dotstroke' => __( 'Dot stroke', 'avata' ),
		'drawcircle' => __( 'Draw circle', 'avata' ),
		'smalldotstroke' => __( 'Small dot stroke', 'avata' ),
		'puff' => __( 'Puff', 'avata' ),
		'hop' => __( 'Hop', 'avata' ),
	),
) );

Hoo::add_field( 'avata', array(
	'settings' => 'nav_css3_color',
	'label'    => __( 'Icon Color', 'avata' ),
	'section'  => 'avata_nav_styling',
	'type'     => 'color',
	'priority' => 10,
	'default'  => '#f9ae40',
) );

Hoo::add_field( 'avata', array(
	'settings' => 'nav_css3_border_color',
	'label'    => __( 'Icon Border Color', 'avata' ),
	'section'  => 'avata_nav_styling',
	'type'     => 'color',
	'priority' => 10,
	'default'  => '#f9ae40',
) );

Hoo::add_field( 'avata', array(
	'settings' => 'side_nav_padding',
	'label'    => __( 'Side Padding', 'avata' ),
	'section'  => 'avata_nav_styling',
	'type'     => 'text',
	'priority' => 10,
	'default'  => '37px',
) );

// Basic settings

Hoo::add_section( 'avata_panel_basic_settings', array(
    'title'          => __( 'Basic Settings', 'avata' ),
    'description'    => '',
    'panel'          => '', 
    'priority'       => 11,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
) );

Hoo::add_field( 'avata', array(
	'settings' => 'primary_color',
	'label'    => __( 'Primary Color', 'avata' ),
	'section'  => 'avata_panel_basic_settings',
	'type'     => 'color',
	'priority' => 10,
	'default'  => '#f9ae40',
) );

Hoo::add_field( 'avata', array(
	'settings' => 'header_code',
	'label'    => __( 'Header Tracking Code', 'avata' ),
	'section'  => 'avata_panel_basic_settings',
	'type'     => 'code',
	'priority' => 10,
	'default'  => '',
) );

Hoo::add_field( 'avata', array(
	'settings' => 'footer_code',
	'label'    => __( 'Footer Tracking Code', 'avata' ),
	'section'  => 'avata_panel_basic_settings',
	'type'     => 'code',
	'priority' => 10,
	'default'  => '',
) );

Hoo::add_field( 'avata', array(
	'settings' => 'page_404_content',
	'label'    => __( '404 Page Content', 'avata' ),
	'section'  => 'avata_panel_basic_settings',
	'type'     => 'dropdown-pages',
	'priority' => 10,
	'default'  => '0',
) );

// Sidebar settings


Hoo::add_section( 'avata_panel_sidebar_settings', array(
    'title'          => __( 'Sidebar Settings', 'avata' ),
    'description'    => '',
    'panel'          => '', 
    'priority'       => 12,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
) );

Hoo::add_field( 'avata', array(
	'settings' => 'left_sidebar_pages',
	'label'    => __( 'Page Left Sidebar', 'avata' ),
	'section'  => 'avata_panel_sidebar_settings',
	'type'     => 'select',
	'priority' => 10,
	'default'  => '0',
	'choices'  => $avata_sidebars
) );

Hoo::add_field( 'avata', array(
	'settings' => 'right_sidebar_pages',
	'label'    => __( 'Page Right Sidebar', 'avata' ),
	'section'  => 'avata_panel_sidebar_settings',
	'type'     => 'select',
	'priority' => 10,
	'default'  => '0',
	'choices'  => $avata_sidebars
) );

Hoo::add_field( 'avata', array(
	'settings' => 'left_sidebar_posts',
	'label'    => __( 'Post Left Sidebar', 'avata' ),
	'section'  => 'avata_panel_sidebar_settings',
	'type'     => 'select',
	'priority' => 10,
	'default'  => '0',
	'choices'  => $avata_sidebars
) );

Hoo::add_field( 'avata', array(
	'settings' => 'right_sidebar_posts',
	'label'    => __( 'Post Right Sidebar', 'avata' ),
	'section'  => 'avata_panel_sidebar_settings',
	'type'     => 'select',
	'priority' => 10,
	'default'  => '0',
	'choices'  => $avata_sidebars
) );

Hoo::add_field( 'avata', array(
	'settings' => 'left_sidebar_archive',
	'label'    => __( 'Archive Left Sidebar', 'avata' ),
	'section'  => 'avata_panel_sidebar_settings',
	'type'     => 'select',
	'priority' => 10,
	'default'  => '0',
	'choices'  => $avata_sidebars
) );

Hoo::add_field( 'avata', array(
	'settings' => 'right_sidebar_archive',
	'label'    => __( 'Archive Right Sidebar', 'avata' ),
	'section'  => 'avata_panel_sidebar_settings',
	'type'     => 'select',
	'priority' => 10,
	'default'  => '0',
	'choices'  => $avata_sidebars
) );



// Footer

Hoo::add_section( 'avata_footer', array(
    'title'          => __( 'Footer', 'avata' ),
    'description'    => esc_attr__( 'Get social icon string from http://fontawesome.io/icons/, e.g. facebook.', 'avata' ),
    'panel'          => '', 
    'priority'       => 13,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
) );

Hoo::add_field( 'avata', array(
	'type'     => 'checkbox',
	'settings' => 'enable_footer_widgets',
	'label'    => sprintf(__('Display Footer Widgets', 'avata' ),'#'.($i+1)),
	'section'  => 'avata_footer',
	'default'  => '1',
	'priority' => 10,
	) );
for($i=0;$i<9;$i++){
	
	Hoo::add_field( 'avata', array(
	'type'     => 'text',
	'settings' => 'social_icon_'.$i.'',
	'label'    => sprintf(__('Social Icon %s', 'avata' ),'#'.($i+1)),
	'section'  => 'avata_footer',
	'default'  => '',
	'priority' => 10+$i,
	) );
	
	Hoo::add_field( 'avata', array(
	'type'     => 'text',
	'settings' => 'social_title_'.$i.'',
	'label'    => sprintf(__('Social Title %s', 'avata' ),'#'.($i+1)),
	'section'  => 'avata_footer',
	'default'  => '',
	'priority' => 10+$i,
	) );
	
	Hoo::add_field( 'avata', array(
	'type'     => 'text',
	'settings' => 'social_link_'.$i.'',
	'label'    => sprintf(__('Social Link %s', 'avata' ),'#'.($i+1)),
	'section'  => 'avata_footer',
	'default'  => '',
	'priority' => 10+$i,
	) );
}

Hoo::add_field( 'avata', array(
	'type'     => 'textarea',
	'settings' => 'copyright',
	'label'    => __('Copyright', 'avata'),
	'section'  => 'avata_footer',
	'default'  => '&copy;Copyright '.date('Y').' - All rights reserved.',
	'priority' => 11+$i,
	) );
	
Hoo::add_field( 'avata', array(
	'type'     => 'color',
	'settings' => 'copyright_color',
	'label'    => __('Copyright Font Color', 'avata' ),
	'section'  => 'avata_footer',
	'default'  => '#ffffff',
	'priority' => 12+$i,
	) );
Hoo::add_field( 'avata', array(
	'type'     => 'color',
	'settings' => 'copyright_bg_color',
	'label'    => __('Copyright Background Color', 'avata' ),
	'section'  => 'avata_footer',
	'default'  => '#333',
	'priority' => 13+$i,
	) );
	


// Typography

Hoo::add_section( 'avata_typography', array(
    'title'          => __( 'Typography', 'avata' ),
    'description'    => '',
    'panel'          => '', 
    'priority'       => 14,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
) );

Hoo::add_field( 'avata', array(
	'type'     => 'text',
	'settings' => 'google_fonts',
	'label'    => __('Load Google Fonts', 'avata' ),
	'section'  => 'avata_typography',
	'default'  => 'Source+Sans+Pro:400,900,700,300,300italic|Lato:300,400,700,900|Poppins:300,400,500,600,700',
	'priority' => 10,
	) );


// Blog
Hoo::add_section( 'avata_blog', array(
    'title'          => __( 'Blog', 'avata'  ),
    'description'    => '',
    'panel'          => '', 
    'priority'       => 15,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
) );

Hoo::add_field( 'avata', array(
	'type'     => 'checkbox',
	'settings' => 'hide_post_meta',
	'label'    => __('Hide Post Meta', 'avata'),
	'section'  => 'avata_blog',
	'default'  => '',
	'priority' => 10,
	) );
Hoo::add_field( 'avata', array(
	'type'     => 'checkbox',
	'settings' => 'hide_meta_date',
	'label'    => __('Hide Meta: Date', 'avata'),
	'section'  => 'avata_blog',
	'default'  => '',
	'priority' => 10,
	) );
Hoo::add_field( 'avata', array(
	'type'     => 'checkbox',
	'settings' => 'hide_meta_author',
	'label'    => __('Hide Meta: Author', 'avata'),
	'section'  => 'avata_blog',
	'default'  => '',
	'priority' => 10,
	) );
Hoo::add_field( 'avata', array(
	'type'     => 'checkbox',
	'settings' => 'hide_meta_comments',
	'label'    => __('Hide Meta: Comments', 'avata'),
	'section'  => 'avata_blog',
	'default'  => '',
	'priority' => 10,
	) );
Hoo::add_field( 'avata', array(
	'type'     => 'checkbox',
	'settings' => 'hide_meta_categories',
	'label'    => __('Hide Categories on Post List Page', 'avata'),
	'section'  => 'avata_blog',
	'default'  => '',
	'priority' => 10,
	) );
Hoo::add_field( 'avata', array(
	'type'     => 'checkbox',
	'settings' => 'hide_meta_readmore',
	'label'    => __('Hide "Read More" on Post List Page', 'avata'),
	'section'  => 'avata_blog',
	'default'  => '',
	'priority' => 10,
	) );