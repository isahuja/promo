function validateEmail(email) {
  var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(email);
}
function ValidateIPaddress(ipaddress){
  if (/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(ipaddress))
  {
    return (true);
  }
  return (false);
}
jQuery(function() {
        jQuery('.chosen-select').chosen();
        jQuery('.chosen-select-deselect').chosen({ allow_single_deselect: true });
      });
function change_filter_account(that) {
    jQuery('#web_property_name').val(jQuery(that).find(':selected').closest('optgroup').attr('label'));
    jQuery('#gawd_form').submit();
}
jQuery(document).ready(function(){
  jQuery('#enable_cross_domain').on('change',function(){
    jQuery('#cross_domains').toggle();
  });
  jQuery('#enable_custom_code').on('change',function(){
    jQuery('#gawd_custom_code').toggle();
  });


jQuery('#gawd_own_project').on('change',function(){
  jQuery('.own_inputs').toggle();
  });

jQuery('#gawd_settings_logout').on('click',function(){
        jQuery('#gawd_settings_logout_val').val('1');
        jQuery('#gawd_form').submit();
});
jQuery('#gawd_settings_button').on('click', function(){
    jQuery('#alert_view_name').val(jQuery("#gawd_alert_view option:selected").closest('optgroup').attr('label'));
    jQuery('#pushover_view_name').val(jQuery("#gawd_pushover_view option:selected").closest('optgroup').attr('label'));

  jQuery('#gawd_settings_submit').val(1);
  if(window.location.hash==='#gawd_filters_tab'){
    var submit_form1 = true;
    var gawd_filter_name_fild = jQuery(".gawd_filter_name_fild");
    var gawd_filter_value = jQuery("#gawd_filter_value");

    if(gawd_filter_name_fild.val()===""){
      gawd_filter_name_fild.addClass('gawd_invalid');
      submit_form1 = false;
    }
    else{
      gawd_filter_name_fild.removeClass('gawd_invalid');
    }
    if(jQuery('#gawd_filter_type').val() == 'GEO_IP_ADDRESS'){
      if(gawd_filter_value.val()==="" || !ValidateIPaddress(gawd_filter_value.val())){
        gawd_filter_value.addClass('gawd_invalid');
        submit_form1 = false;
      }
      else{
        gawd_filter_value.removeClass('gawd_invalid');
      }
    }
    if(submit_form1){
      jQuery('#gawd_form').submit();
    }

  }

  else{
    jQuery('#gawd_form').submit();
  }
});
  jQuery('.gawd_perm').on('click',function(){
    if(jQuery(this).attr('checked') == 'checked'){
      jQuery(this).parent().prevAll().find(".gawd_perm").attr('checked',true);
    }
  })
  var gawd_emails = false;
  jQuery("#gawd_right_conteiner").show();
  if(window.location.hash===''){
    var url = window.location.toString();
    if(url.indexOf('gawd_settings') > -1){
      jQuery('.gawd_submit').width('100%');
    }
    jQuery('.gawd_authenicate').show();
    jQuery('#gawd_settings_logout').show();
    jQuery('#gawd_authenicate').addClass('gawd_active_li');
    jQuery('#gawd_tracking').addClass('gawd_active_li');
    if(jQuery(window).width() < 720){
      jQuery('#gawd_authenicate').addClass('gawd_resp_active_li');
     
    }
  }
  else if(window.location.hash==='#gawd_alerts_tab'){
    jQuery('.gawd_submit').width('100%');
    jQuery('#gawd_alerts').addClass('gawd_active_li');
    jQuery('#gawd_settings_button').hide();
    jQuery('.gawd_alerts').show();
    if(jQuery(window).width() < 720){
      jQuery('#gawd_alerts').addClass('gawd_resp_active_li');
    }
  }
  else if(window.location.hash==='#gawd_emails_tab'){
    jQuery('.gawd_submit').width('82.5%');
    jQuery('#gawd_emails').addClass('gawd_active_li');
    if(jQuery(window).width() < 720){
      jQuery('#gawd_emails').addClass('gawd_resp_active_li');
    }
    if(jQuery(".gawd_emails table").length<=0){
      gawd_emails = true;
      jQuery('#gawd_settings_button').hide();
    }
    
    jQuery('.gawd_emails').show();
  }
  else if(window.location.hash==='#gawd_advanced_tab'){
    jQuery('.gawd_submit').width('92.9%');
    if(jQuery(window).width() < 720){
      jQuery('#gawd_advanced').addClass('gawd_resp_active_li');
    }
    jQuery('#gawd_advanced').addClass('gawd_active_li');
    jQuery('.gawd_advanced').show();
  }
  else if(window.location.hash==='#gawd_pushover_tab'){
    jQuery('.gawd_submit').width('100%');
    if(jQuery(window).width() < 720){
      jQuery('#gawd_pushover').addClass('gawd_resp_active_li');
    }
    jQuery('#gawd_pushover').addClass('gawd_active_li');
    jQuery('#gawd_settings_button').hide();
    jQuery('.gawd_pushover').show();
  }
  else if(window.location.hash==='#gawd_filters_tab'){
    jQuery('.gawd_submit').width('100%');
    if(jQuery(window).width() < 720){
      jQuery('#gawd_filters').addClass('gawd_resp_active_li');
    }
    jQuery('#gawd_filters').addClass('gawd_active_li');
    jQuery('.gawd_filters').show();
  }
  else if(window.location.hash==='#gawd_authenicate_tab'){
    jQuery('.gawd_submit').width('100%');
    jQuery('#gawd_settings_logout').show();
    if(jQuery(window).width() < 720){
      jQuery('#gawd_authenicate').addClass('gawd_resp_active_li');
    }
    jQuery('#gawd_authenicate').addClass('gawd_active_li');
    jQuery('.gawd_authenicate').show();
  }  
  else if(window.location.hash==='#gawd_advanced'){
    if(jQuery(window).width() < 720){
      jQuery('#gawd_advanced').addClass('gawd_resp_active_li');
    }
    jQuery('#gawd_advanced').addClass('gawd_active_li');
    jQuery('.gawd_advanced').show();
  }
  else{
    jQuery('.gawd_authenicate').show();
    jQuery('#gawd_authenicate').addClass('gawd_active_li');
    if(jQuery(window).width() < 720){
      jQuery('#gawd_authenicate').addClass('gawd_resp_active_li');
    }
  }
  

  jQuery('#gawd_own_project').on('change', function(){
    if(jQuery(this).is(":checked")){
      jQuery('.own_inputs').show();
    }
    else{
      jQuery('.own_inputs').hide();
    }
  })
  if(jQuery(window).width() < 720){
    jQuery('.gawd_menu_li').addClass('gawd_resp_li');
    jQuery('.gawd_resp_li').show();
    var elId = window.location.hash ? window.location.hash.substring(0,window.location.hash.length-4) : "#gawd_authenicate";
    show_hide(jQuery(elId));
    /* jQuery('.gawd_resp_li').hide();   
    jQuery(elId).show(); */
  }


  jQuery('.gawd_resp_li').on('click', function(){
      show_hide(jQuery(this));
  })
  jQuery('.gawd_settings_menu_coteiner').show();
  jQuery('#gawd_filter_type').on('change',function(){
    jQuery('#gawd_filter_name').html(jQuery('#gawd_filter_type :selected').attr('data-name'));
    if(jQuery('#gawd_filter_type :selected').attr('data-name') == 'Country'){
      var tooltip = 'Define country to filter from Google Analytics tracking.';
    }
    else if(jQuery('#gawd_filter_type :selected').attr('data-name') == 'Region'){
      var tooltip = 'Define region to filter from Google Analytics tracking.';
    }    
    else if(jQuery('#gawd_filter_type :selected').attr('data-name') == 'City'){
      var tooltip = 'Define city to filter from Google Analytics tracking.';
    }
    else{
      var tooltip = 'Define IP address to filter from Google Analytics tracking.';
    }
    jQuery('#gawd_filter_name').closest(jQuery('#gawd_filter_value_cont')).find(jQuery('.gawd_info')).attr('title',tooltip);
  })
  
  if(jQuery('#gawd_settings_tab').val() == ''){
    //jQuery('#gawd_authenicate').addClass('gawd_active_li');
    //  if(jQuery(window).width() < 720){
    //    jQuery('#gawd_authenicate').addClass('gawd_resp_active_li');
    //  }
  };
  jQuery('.gawd_menu_li').on('click',function(){
    var current_hash = window.location.hash;
    var tab = jQuery(this).attr('id');
    jQuery('.gawd_menu_li').removeClass('gawd_active_li');
    jQuery('.gawd_menu_li').removeClass('gawd_resp_active_li');
    //jQuery(this).addClass('gawd_active_li');
    if(jQuery(window).width() < 720){
      jQuery(this).addClass('gawd_resp_active_li');
    }

   jQuery('.gawd_settings_menu_coteiner .gawd_menu_ul li').each(function(){
     jQuery(this).removeClass('gawd_active_li');
   });
    jQuery('#gawd_settings_tab').val(tab);
    if(tab == 'gawd_alerts'){
      jQuery('.gawd_submit').width('100%');
      window.location.hash = "gawd_alerts_tab";
      jQuery(this).addClass('gawd_active_li');
      jQuery('.gawd_authenicate').hide();
      jQuery('.gawd_pushover').hide();
      jQuery('.gawd_filters').hide();
      jQuery('.gawd_advanced').hide();
      jQuery('.gawd_emails').hide();
      jQuery('.gawd_alerts').show();
      if(gawd_emails){
        gawd_emails = true;
        jQuery('#gawd_right_conteiner').show();
      }
      jQuery('#gawd_settings_button').hide();
      jQuery('#gawd_settings_logout').hide();
    }
    else if(tab == 'gawd_emails'){
      jQuery('.gawd_submit').width('82.5%');
      window.location.hash = "gawd_emails_tab";
      jQuery(this).addClass('gawd_active_li');
      jQuery('.gawd_alerts').hide();
      jQuery('.gawd_authenicate').hide();
      jQuery('.gawd_pushover').hide();
      jQuery('.gawd_filters').hide();
      jQuery('.gawd_advanced').hide();
      jQuery('.gawd_emails').show();
      if(jQuery(".gawd_emails table").length<=0){
        gawd_emails = true;
        jQuery('#gawd_settings_button').hide();
      }
      jQuery('#gawd_settings_logout').hide();
    }
    else if(tab == 'gawd_pushover'){
      jQuery('.gawd_submit').width('100%');
      window.location.hash = "gawd_pushover_tab";
      jQuery(this).addClass('gawd_active_li');
      jQuery('.gawd_alerts').hide();
      jQuery('.gawd_pushover').show();
      jQuery('.gawd_authenicate').hide();
      jQuery('.gawd_emails').hide();
      jQuery('.gawd_advanced').hide();
      jQuery('.gawd_filters').hide();
      if(gawd_emails){
        gawd_emails = true;
        jQuery('#gawd_right_conteiner').show();
      }
      jQuery('#gawd_settings_button').hide();
      jQuery('#gawd_settings_logout').hide();
    }
    else if(tab == 'gawd_filters'){
      jQuery('.gawd_submit').width('100%');
      window.location.hash = "gawd_filters_tab";
      jQuery(this).addClass('gawd_active_li');
      jQuery('.gawd_alerts').hide();
      jQuery('.gawd_pushover').hide();
      jQuery('.gawd_authenicate').hide();
      jQuery('.gawd_emails').hide();
      jQuery('.gawd_advanced').hide();
      jQuery('.gawd_filters').show();
      if(gawd_emails){
        gawd_emails = true;
        jQuery('#gawd_right_conteiner').show();
      }
      jQuery('#gawd_settings_button').show();
      jQuery('#gawd_settings_logout').hide();
    }
    else if(tab == 'gawd_advanced'){
      jQuery('.gawd_submit').width('92.9%');
      window.location.hash = "gawd_advanced_tab";
      jQuery(this).addClass('gawd_active_li');
      jQuery('.gawd_alerts').hide();
      jQuery('.gawd_pushover').hide();
      jQuery('.gawd_authenicate').hide();
      jQuery('.gawd_emails').hide();
      jQuery('.gawd_filters').hide();
      jQuery('.gawd_advanced').show();
      if(gawd_emails){
        gawd_emails = true;
        jQuery('#gawd_right_conteiner').show();
      }
      jQuery('#gawd_settings_button').show();
      jQuery('#gawd_settings_logout').hide();
    }
    else if(tab == 'gawd_authenicate'){
      jQuery('.gawd_submit').width('100%');
      window.location.hash = "gawd_authenicate_tab";
      jQuery(this).addClass('gawd_active_li');
      jQuery('.gawd_alerts').hide();
      jQuery('.gawd_pushover').hide();
      jQuery('.gawd_advanced').hide();
      jQuery('.gawd_filters').hide();
      jQuery('.gawd_emails').hide();
      jQuery('.gawd_authenicate').show();
      if(gawd_emails){
        gawd_emails = true;
        jQuery('#gawd_right_conteiner').show();
      }
      jQuery('#gawd_settings_button').show();
      jQuery('#gawd_settings_logout').show();
    }
  })
});
