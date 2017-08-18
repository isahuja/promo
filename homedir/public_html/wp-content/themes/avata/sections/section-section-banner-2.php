<?php
  global $allowedposttags;

  $section_title      = avata_option('section_title_banner_2');
  $section_subtitle   = avata_option('section_subtitle_banner_2');
  
  $btn_txt_1          = avata_option('section_btntxt_1_banner_2');
  $btn_link_1         = avata_option('section_btnlink_1_banner_2');
  $btn_target_1       = avata_option('section_btntarget_1_banner_2');
  $btn_txt_2          = avata_option('section_btntxt_2_banner_2');
  $btn_link_2         = avata_option('section_btnlink_2_banner_2');
  $btn_target_2       = avata_option('section_btntarget_2_banner_2');
  
  $arrow             = avata_option('section_display_arrow_banner_2');
  
  ?>
    <div class="avata-box__magnet avata-box__magnet--sm-padding avata-box__magnet--center-center">
        <div class="avata-overlay" style="opacity: 0.6; background-color: rgb(40, 50, 78);"></div>
        <div class="avata-box__container avata-section__container container">
            <div class="avata-box avata-box--stretched"><div class="avata-box__magnet avata-box__magnet--center-center">
                <div class="avata-hero animated fadeInUp">
                    <h1 class="avata-hero__text avata-section_title_banner_2"><?php echo esc_attr($section_title);?></h1>
                    <p class="avata-hero__subtext avata-section_subtitle_banner_2"><strong><?php echo wp_kses($section_subtitle, $allowedposttags);?></strong></p>
                </div>
                <div class="avata-buttons btn-inverse avata-buttons--center">
                <?php if($btn_txt_1 !=''){?>
                <a class="avata-buttons__btn btn btn-lg animated fadeInUp delay btn-primary avata-section_btntxt_1_banner_2" href="<?php echo esc_url($btn_link_1);?>" target="<?php echo esc_attr($btn_target_1);?>"><?php echo esc_attr($btn_txt_1);?></a>
                <?php }?>
                <?php if($btn_txt_2 !=''){?>
                <a class="avata-buttons__btn btn btn-lg btn-outline animated fadeInUp delay avata-section_btntxt_2_banner_2" href="<?php echo esc_url($btn_link_2);?>" target="<?php echo esc_attr($btn_target_2);?>"><?php echo esc_attr($btn_txt_2);?></a>
                <?php }?>
                
                </div>
            </div></div>
        </div>
        
    </div>
    <?php if($arrow =='1' ){?>
    <div class="avata-arrow avata-arrow--floating text-center">
            <div class="avata-section__container container">
                <a class="avata-arrow__link move-section-down" href="#"><i class="glyphicon glyphicon-menu-down"></i></a>
            </div>
        </div>
         <?php }?>
