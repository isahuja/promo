<?php
  global $allowedposttags;

  $section_content   = avata_option('section_content_slogan');
  $btn_txt           = avata_option('section_btn_txt_slogan');
  $btn_link          = avata_option('section_btn_link_slogan');
  $btn_target        = avata_option('section_btn_target_slogan');
  $fullwidth         =  avata_option('section_fullwidth_slogan');
  $container         = 'container';
  if ($fullwidth=='1')
 	 $container         = 'container-fullwidth';
  ?>
<div class="section-content-wrap">
  <div class="<?php echo $container;?>">
    <div class="section-content">
        <div class="col-md-8 col-md-offset-2 text-center">
      <h3 class="avata-section_content_slogan"><?php echo wp_kses($section_content, $allowedposttags);?></h3>
      <a href="<?php echo esc_url($btn_link);?>" target="<?php echo esc_attr($btn_target);?>" class="btn btn-lg btn-primary avata-section_btn_txt_slogan"><?php echo esc_attr($btn_txt);?></a> </div>
</div>

    </div>
  </div>
