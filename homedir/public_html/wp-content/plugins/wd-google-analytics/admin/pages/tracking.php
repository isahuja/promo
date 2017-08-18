<?php
$gawd_settings = get_option('gawd_settings');
if (isset($gawd_user_data['default_webPropertyId'])) {
	$tracking_dimensions = $gawd_client->get_custom_dimensions_tracking();
} else {
	$tracking_dimensions = 'no_custom_dimensions_exist';
}
try{
	$existing_custom_dimensions = $gawd_client->get_custom_dimensions('default');
}catch (Exception $e){
	$existing_custom_dimensions = array();
}
if (!is_array($existing_custom_dimensions)) {
	$existing_custom_dimensions = array();
}
$supported_dimensions = array("Logged in","Post type","Author","Category","Tags","Published Month","Published Year");
$ua_code = isset($gawd_user_data['default_webPropertyId']) ? $gawd_user_data['default_webPropertyId'] : '';
$gawd_permissions = isset($gawd_settings['gawd_permissions']) ? $gawd_settings['gawd_permissions'] : array();
$gawd_anonymize = isset($gawd_settings['gawd_anonymize']) ? $gawd_settings['gawd_anonymize'] : '';
$gawd_tracking_enable = isset($gawd_settings['gawd_tracking_enable']) ? $gawd_settings['gawd_tracking_enable'] : 'on';
$gawd_outbound = isset($gawd_settings['gawd_outbound']) ? $gawd_settings['gawd_outbound'] : '';

$gawd_enhanced = isset($gawd_settings['gawd_enhanced']) ? $gawd_settings['gawd_enhanced'] : '';

$enable_custom_code = isset($gawd_settings['enable_custom_code']) ? $gawd_settings['enable_custom_code'] : '';
$gawd_custom_code = isset($gawd_settings['gawd_custom_code']) ? $gawd_settings['gawd_custom_code'] : '';

$gawd_file_formats = isset($gawd_settings['gawd_file_formats']) ? $gawd_settings['gawd_file_formats'] : '';
$gawd_tracking_enable = isset($_GET['enableTracking']) ? 'on' : $gawd_tracking_enable;
$domain = GAWD::get_domain(esc_html(get_option('siteurl')));
?>

<div id="gawd_body">
  <div class="wd-upgrade-pro-main-wrap">
    <div class="wd-text">Create a web property and manage tracking settings in this section.
      <a style="color: #00A0D2; text-decoration: none;" target="_blank" href="https://web-dorado.com/wordpress-google-analytics/quick-start/overview.html">Read more in User Guide.</a>
    </div>
    <div class="wd-ugrade-pro-wrap">
      <a href="https://web-dorado.com/products/wordpress-google-analytics-plugin.html" target="_blank">
          <div class="wd-ugrade-pro-image-wrap">
              <div class="wd-ugrade-pro-image">
                  <img src="<?php echo GAWD_URL; ?>/assets/web-dorado.png">
              </div>
               <div class="wd-ugrade-pro-text">
                  <?php _e("Upgrade to paid version", "gawd"); ?>
              </div>
          </div>
      </a>
    </div>
  </div>
  <div class="resp_menu"><div class="menu_img"></div><div class="button_label">TRACKING</div><div class="clear"></div></div>
  <div class="gawd_menu_coteiner gawd_settings_menu_coteiner">  
    <ul class="gawd_menu_ul">     
      <li class=" gawd_menu_li_tracking" id="gawd_tracking">
        Tracking
      </li>       
      <li class=" gawd_menu_li_tracking gawd_pro_menu" id="gawd_exclude_tracking">
        Exclude
        <span class="gawd_pro_flag">Paid</span>
      </li>          
    </ul>
  </div>
  <div id="gawd_right_conteiner">
    <h3 class="gawd_page_titles">Tracking</h3>

    <form method="post" id="gawd_form">
      <div class="gawd_tracking">
        <div class="gawd_settings_wrapper">
          <div class="settings_row">
            <div class="onoffswitch">
              <input type="checkbox" name="gawd_tracking_enable" class="onoffswitch-checkbox" id="gawd_tracking_enable" <?php echo $gawd_tracking_enable != '' ? 'checked' : '';?>>
              <label class="onoffswitch-label" for="gawd_tracking_enable">
                <span class="onoffswitch-inner"></span>
                <span class="onoffswitch-switch"></span>
              </label>
            </div>
            <div class="gawd_info" title="Enable to add Google Analytics tracking code to <head> tag of your website HTML."></div>
            <div class="onoffswitch_text">
              Enable Tracking
            </div>
            <div class="clear"></div>
          </div>
          <div class="settings_row independent_setting">
            <div class="onoffswitch <?php echo (($gawd_tracking_enable == '') ? 'onoffswitch_disabled' : ''); ?> independent_switch">
              <input type="checkbox" name="gawd_anonymize" class="onoffswitch-checkbox independent_input" id="gawd_anonymize" <?php echo $gawd_anonymize != '' ? 'checked' : '';?> <?php echo (($gawd_tracking_enable == '') ? 'disabled' : ''); ?>>
              <label class="onoffswitch-label" for="gawd_anonymize">
                <span class="onoffswitch-inner"></span>
                <span class="onoffswitch-switch"></span>
              </label>
            </div>
            <div class="gawd_info" title="Turn this option on, in case you’d like to hide the last block of users’ IP addresses."></div>
            <div class="onoffswitch_text">
              Anonymize IP address
            </div>
            <div class="clear"></div>
          </div> 
          <div class="settings_row independent_setting">
            <div class="onoffswitch <?php echo (($gawd_tracking_enable == '') ? 'onoffswitch_disabled' : ''); ?> independent_switch">
              <input type="checkbox" name="gawd_enhanced" class="onoffswitch-checkbox independent_input" id="gawd_enhanced" <?php echo $gawd_enhanced != '' ? 'checked' : '';?> <?php echo (($gawd_tracking_enable == '') ? 'disabled' : ''); ?>>
              <label class="onoffswitch-label" for="gawd_enhanced">
                <span class="onoffswitch-inner"></span>
                <span class="onoffswitch-switch"></span>
              </label>
            </div>
            <div class="gawd_info" title="Enable this option to track multiple links with the same destination. Get information for buttons, menus, as well as elements with multiple destinations, e.g. search boxes. You can find out more about Enhanced Link Attribution in the plugin User Guide."></div>
            <div class="onoffswitch_text">
              Enhanced Link Attribution
            </div>
            <div class="clear"></div>
          </div>       
          <div class="settings_row independent_setting">
            <div class="onoffswitch <?php echo (($gawd_tracking_enable == '') ? 'onoffswitch_disabled' : ''); ?> independent_switch">
              <input type="checkbox" name="gawd_outbound" class="onoffswitch-checkbox independent_input" id="gawd_outbound" <?php echo $gawd_outbound != '' ? 'checked' : '';?> <?php echo (($gawd_tracking_enable == '') ? 'disabled' : ''); ?>>
              <label class="onoffswitch-label" for="gawd_outbound">
                <span class="onoffswitch-inner"></span>
                <span class="onoffswitch-switch"></span>
              </label>
            </div>
            <div class="gawd_info" title="Turn outbound clicks tracking on to track the links users click to leave your website."></div>
            <div class="onoffswitch_text">
              Outbound clicks tracking
            </div>
            <div class="clear"></div>
          </div>
          <div class="settings_row independent_setting">
            <div class="onoffswitch <?php echo (($gawd_tracking_enable == '') ? 'onoffswitch_disabled' : ''); ?> independent_switch">
              <input type="checkbox" name="gawd_file_formats" value="zip|mp3*|mpe*g|pdf|docx*|pptx*|xlsx*|rar*" class="onoffswitch-checkbox independent_input" id="gawd_file_formats" <?php echo $gawd_file_formats != '' ? 'checked' : '';?> <?php echo (($gawd_tracking_enable == '') ? 'disabled' : ''); ?>>
              <label class="onoffswitch-label" for="gawd_file_formats">
                <span class="onoffswitch-inner"></span>
                <span class="onoffswitch-switch"></span>
              </label>
            </div>
            <div class="gawd_info" title="Enable to track file downloads and mailing links."></div>
            <div class="onoffswitch_text track_label">
              Mailto, Download tracking (ex.: .doc, .pdf, .jpg, etc.)
            </div>
            <div class="clear"></div>
          </div>
          <div class="settings_row">
            <div class="onoffswitch">
              <input type="checkbox" name="enable_custom_code" class="onoffswitch-checkbox" id="enable_custom_code" <?php echo $enable_custom_code != '' ? 'checked' : '';?>>
              <label class="onoffswitch-label" for="enable_custom_code">
                <span class="onoffswitch-inner"></span>
                <span class="onoffswitch-switch"></span>
              </label>
            </div>
            <div class="gawd_info" title="Enable adding custom code to tracking code."></div>
            <div class="onoffswitch_text">
              Enable custom code. For advanced users. <span style="color:#dd0000;"> Enter only code from trustworthy sources.<span>
            </div>
            <div class="clear"></div>
          </div>
          <?php
          $custom_code_show = $enable_custom_code == '' ? 'style="display:none"' : '';
          ?>
          <div id="gawd_custom_code" class="gawd_goal_row" <?php echo $custom_code_show;?>>
            <span class="gawd_goal_label">Custom Code</span>
          <span class="gawd_goal_input">
            <div class="time_input">
              <textarea class="gawd_custom_code" name="gawd_custom_code"><?php echo $gawd_custom_code;?></textarea>
            </div>
          </span>
            <div class="gawd_info" title="Provide custom code that you want to add in tracking code"></div>
            <div class="clear"></div>
          </div>
          <div style="margin-top:25px">
            <img class="gawd_pro_img" data-attr="custom dimensions" src="<?php echo GAWD_URL.'/assets/freePages/custom_dimensions.png';?>"/>
          </div>
        </div>
        <input name="gawd_custom_dimension_id" type="hidden" value="<?php echo count($existing_custom_dimensions);?>"/>
        <div class="gawd_tracking_display">
          <p>CODE ADDED TO SITE:</p>
          <div id="gawd_tracking_enable_code" <?php if('on' != $gawd_tracking_enable): ?>style="display: none;"<?php endif; ?>>
            <code class="html">&#60;script&#62;</code>
            <code class="javascript">
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
           
              <br /><br />
              ga('create', '<?php echo $ua_code ?>', 'auto');
            </code>
            <code id="enable_custom_code_code" class="javascript" <?php if('on' != $enable_custom_code){; ?> style="display: none;"<?php }; ?>>
              </br>
              <?php  echo "/*CUSTOM CODE START*/ </br>" . $gawd_custom_code . "</br>/*CUSTOM CODE END*/ </br>";?>
              
            </code>
            <code id="gawd_anonymize_code" class="javascript" <?php if('on' != $gawd_anonymize): ?>style="display: none;"<?php endif; ?>>
            ga('set', 'anonymizeIp', true);
            </code>
            <code id="gawd_enhanced_code" class="javascript" <?php if('on' != $gawd_enhanced): ?>style="display: none;"<?php endif; ?>>
            ga('require', 'linkid', 'linkid.js');
            </code>
            <code id="gawd_outbound_code" class="javascript" <?php echo $gawd_outbound != '' && isset($domain) && $domain != '' ? '' :   'style="display: none;"';?>>
              var links_out = document.querySelectorAll('a[href^="http"]');
              links_out.forEach(function (link, key, listObj){
              if (!link.href.match(/.*\.(zip|mp3*|mpe*g|pdf|docx*|pptx*|xlsx*|rar*)(\?.*)?$/) {
              if (link.href.indexOf('mysite.example') == -1) {
              link.addEventListener('click', function (e){
              ga('send', 'event', 'outbound', 'click', e.target.href, {'nonInteraction': 1});
              });
              });
              }
              }});
            </code>
            <code id="gawd_file_formats_code" class="javascript" <?php echo isset($gawd_file_formats) && $gawd_file_formats != '' ? '' : 'style="display: none"';?>>
              var links_download = document.querySelectorAll( 'a' );
              links_download.forEach(function(link, key, listObj){
              if (link.href.match(/.*\.(zip|mp3*|mpe*g|pdf|docx*|pptx*|xlsx*|rar*)(\?.*)?$/)) {
              link.addEventListener('click', function (e)
              {
              ga('send', 'event', 'download', 'click', e.target.href,{'nonInteraction': 1});
              });
              }
              });
              var links_mailto = document.querySelectorAll('a[href^="mailto"]');
              links_mailto.forEach(function(link, key, listObj) {
              link.addEventListener('click', function (e)
              {
              ga('send', 'event', 'email', 'send', e.target.href, {'nonInteraction': 1}";
              });
              });
              });
            </code>
            <code class="javascript">
            </br>
              ga('send', 'pageview');
            </code>
            <code class="html">&#60;/script&#62;</code>
          </div>
        </div>
        <div class="clear"></div>
      </div> 
      <div class="gawd_exclude_tracking">
        <img class="gawd_pro_img" data-attr="exclude options" src="<?php echo GAWD_URL.'/assets/freePages/exclude_tracking.png';?>"/>
      </div>
      <input type='hidden' name="settings_submit" id="settings_submit"/>
      <div class="gawd_submit"><input type="submit" name="settings_submit" class="button_gawd" value="SAVE"/></div>
      <input type='hidden' name="gawd_settings_tab" id="gawd_settings_tab"/>
      <input type='hidden' name="add_dimension_value" id="add_dimension_value"/>
      <?php wp_nonce_field('gawd_save_form', 'gawd_save_form_fild'); ?>
    </form>
  </div>

  <div class="clear"></div>
</div>
<script>
  jQuery(function() {
    jQuery('.chosen-select').chosen();
    jQuery('.chosen-select-deselect').chosen({ allow_single_deselect: true });
  });
jQuery(document).ready(function(){
  jQuery('.button_gawd_add').on('click',function(){
    jQuery('#add_dimension_value').val(jQuery(this).data('name'));
    jQuery('#settings_submit').val('1');
    jQuery('#gawd_form').submit();
  })
  jQuery("#gawd_right_conteiner").show();
  if(window.location.hash===''){
    jQuery('.gawd_submit').width('92.9%');
    jQuery('.gawd_tracking').show();
    jQuery('#gawd_tracking').addClass('gawd_active_li');
    if(jQuery(window).width() < 720){
      jQuery('#gawd_tracking').addClass('gawd_resp_active_li');
    }
  }

  else if(window.location.hash==='#gawd_exclude_tracking_tab'){
    jQuery('.gawd_submit').width('92.9%');
    jQuery('#gawd_exclude_tracking').addClass('gawd_active_li');
    jQuery('.gawd_submit').hide();
    jQuery('.gawd_exclude_tracking').show();
    if(jQuery(window).width() < 720){
      jQuery('#gawd_exclude_tracking').addClass('gawd_resp_active_li');
    }
  }
  else if(window.location.hash==='#gawd_tracking_tab'){
    jQuery('.gawd_submit').width('92.9%');
    jQuery('#gawd_tracking').addClass('gawd_active_li');
    jQuery('.gawd_submit').show();
    jQuery('.gawd_tracking').show();
    if(jQuery(window).width() < 720){
      jQuery('#gawd_tracking').addClass('gawd_resp_active_li');
    }
  }
  
  else{
    jQuery('.gawd_submit').hide();
    jQuery('.gawd_tracking').show();
    jQuery('#gawd_tracking').addClass('gawd_active_li');
    if(jQuery(window).width() < 720){
      jQuery('#gawd_tracking').addClass('gawd_resp_active_li');
    }
  }
})
    jQuery('.gawd_menu_li_tracking').on('click',function(){
      var tab = jQuery(this).attr('id');
      jQuery('.gawd_menu_li_tracking').removeClass('gawd_active_li');
      jQuery('.gawd_menu_li_tracking').removeClass('gawd_resp_active_li');
      jQuery(this).addClass('gawd_active_li');
      if(jQuery(window).width() < 720){
        jQuery(this).addClass('gawd_resp_active_li');
      }

      jQuery('#gawd_settings_tab').val(tab);
      if(tab == 'gawd_tracking'){
        window.location.hash = "gawd_tracking_tab";
        jQuery(this).addClass('gawd_active_li');
        jQuery('.gawd_exclude_tracking').hide();
        jQuery('.gawd_submit').show();
        jQuery('.gawd_tracking').show();
      }  
      else if(tab == 'gawd_exclude_tracking'){
        window.location.hash = "gawd_exclude_tracking_tab";
        jQuery(this).addClass('gawd_active_li');
        jQuery('.gawd_tracking').hide();
        jQuery('.gawd_submit').hide();
        jQuery('.gawd_exclude_tracking').show();
      }    
    })
</script>

