<?php
  global $allowedposttags;
  $section_title     = avata_option('section_title_gallery');
  $section_subtitle  = avata_option('section_subtitle_gallery');
  $gallery           = avata_option('section_items_gallery');
  $fullwidth         = avata_option('section_fullwidth_gallery');
  $container         = 'container';
  if ($fullwidth=='1')
 	 $container         = 'container-fluid';

  
  ?>
<div class="section-content-wrap">
  <div class="<?php echo $container;?>">
  <?php if ( $section_title !='' || $section_subtitle !='' ){?>
    <div class="section-title-area">
      <h2 class="section-title text-center avata-section_title_gallery"><?php echo esc_attr($section_title);?></h2>
      <p class="section-subtitle text-center avata-section_subtitle_gallery"><?php echo wp_kses($section_subtitle, $allowedposttags);?></p>
    </div>
    <?php }?>
    <div class="section-content avata-section_items_gallery">
		<ul class="row no-gutter" id="lightgallery">
    <?php
	$i = 1;
	if (is_array($gallery) && !empty($gallery) ):
		foreach($gallery as $item ):
			if(is_numeric($item['image']))
				$item['image'] = wp_get_attachment_image_url($item['image'],'full');
	?>
    <li class="col-lg-2 col-md-4 col-sm-4 work" data-src="<?php echo esc_url($item['image']);?>" data-sub-html="<?php echo wp_kses($item['title'], $allowedposttags);?>"><a href="#" class="work-box"> <img src="<?php echo esc_url($item['image']);?>" class="img-responsive" alt="" />
        <div class="overlay">
          <div class="overlay-caption">
            <p><i class="fa fa-search" aria-hidden="true"></i></p>
          </div>
        </div>
        </a> 
        </li>
     <?php
	 $i++;
	 endforeach;
	 endif; 
	 ?>
		</ul>
    </div>
  </div>
  </div>
