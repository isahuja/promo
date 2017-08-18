jQuery(document).ready(function(){
  jQuery('#wp-admin-bar-gawd a').on('click', function(){
    var uri = '#' + jQuery('#wp-admin-bar-gawd a span').attr('data-url');
    /*var uri = jQuery('header h1').text();*/
    gawd_chart_type_post_page_front(uri, 'gawd_post_page_popup');
  })
})
function gawd_chart_type_post_page_front(uri,divID){
  if(jQuery("#gawd_chart_type_post_page").val() == 'pie'){
    gawd_pie_chart_post_page_front(uri,divID);
  }
  else{
    post_page_stats_front(uri,divID);
  }
}
function gawd_pie_chart_post_page_front(uri,divID){
    //jQuery("#chart").empty();
    //jQuery("#metric_compare").hide();
    //jQuery(".vs_image").hide();
    jQuery(".opacity_div_compact").show();
  jQuery(".loading_div_compact").show();
  if(typeof divID == 'undefined'){
    divID = 'gawd_post_page_popup';
  }
  var chartType = 'pie';
  var fillAlphas = 0;

  var metric = typeof jQuery("#gawd_metric_post_page").val() != 'undefined' ? jQuery("#gawd_metric_post_page").val() : (typeof jQuery("#gawd_metric_post_page_popup").val() != 'undefined' ? jQuery("#gawd_metric_post_page_popup").val() : 'sessions');
  var date_30 = gawd_front.date_30;
  var date_7 = gawd_front.date_7;
  var date_yesterday = gawd_front.date_yesterday;
  var date_today = gawd_front.date_today;
  var date_this_month = gawd_front.date_this_month;
  var date_last_month = gawd_front.date_last_month;
  var date_last_week = gawd_front.date_last_week;

  start_date = typeof jQuery("#gawd_post_page_date").val() != 'undefined' ? jQuery("#gawd_post_page_date").val() : (typeof jQuery("#gawd_post_page_popup_date").val() != 'undefined' ? jQuery("#gawd_post_page_popup_date").val() : date_30);
  var dimension = 'date';
  var timezone = -(new Date().getTimezoneOffset()/60);
  if(divID == 'gawd_post_page_popup'){
    var chart_div = '<div id="opacity_div"></div><div id="loading_div" style="display:none; text-align: center; position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 9999;"><img src="'+gawd_front.gawd_plugin_url+'/assets/ajax_loader.gif"  style="margin-top: 200px; width:50px;"></div><div class="page_chart_div"><div style="width:100%; height:100%; position:relative;" class="close_button_cont"><button class="gawd_btn" >X</button>';
    chart_div += '<select name="gawd_post_page_popup_date" id="gawd_post_page_popup_date" class="gawd_draw_analytics_front">';
    chart_div +=  '<option value="'+date_30+'">Last 30 Days</option><option value="'+date_7+'">Last 7 Days</option>';
    chart_div +=  '<option value="'+date_last_month+'">Last month</option><option value="'+date_last_week+'">Last week</option>';
    chart_div +=  '<option value="'+date_this_month+'">This month</option><option value="'+date_yesterday+'">Yesterday</option>';
    chart_div +=  '<option value="'+date_today+'">Today</option>';
    chart_div += '</select>';
    chart_div += '<select name="gawd_metric_post_page_popup" id="gawd_metric_post_page_popup" class="gawd_draw_analytics_front">';
    chart_div += '<option value="sessions" >Sessions</option><option value="users"  >Users</option><option value="bounceRate"  >Bounce Rate</option><option value="pageviews"  >Pageviews</option><option value="percentNewSessions">% New Sessions</option><option value="avgSessionDuration">Avg Session Duration</option>';
    chart_div += '</select>';
    chart_div += '<select name="gawd_chart_type_post_page" id="gawd_chart_type_post_page" class="gawd_draw_analytics_front">';
    chart_div += '<option value="line">Line Chart</option><option value="pie">Pie Cart</option><option value="column">Column Chart</option>';
    chart_div += '</select>';
    chart_div += '<div id="gawd_post_page_popup"></div></div>';
    jQuery(".page_chart_div").remove();
    jQuery('#opacity_div').remove();
    jQuery( "body" ).append(chart_div);
    jQuery("#gawd_metric_post_page_popup").val(metric);
    jQuery("#gawd_chart_type_post_page").val(chartType);
    jQuery("#gawd_post_page_popup_date").val(start_date);
    jQuery( "#loading_div" ).show();
    jQuery( "#opacity_div" ).show();
    jQuery('#gawd_post_page_popup').height('400');
    jQuery('#gawd_metric_post_page_popup, #gawd_post_page_popup_date, #gawd_chart_type_post_page').on('change',function(){
      gawd_chart_type_post_page_front(uri,'gawd_post_page_popup');
    })
  }
    var dates = start_date.split('/-/');

    jQuery("#gawd_post_page_meta").empty();
    jQuery('#gawd_post_page_meta').height('300');
    jQuery.post(gawd_front.ajaxurl, {
        action: 'show_page_post_data',
        metric: metric,
        start_date: dates[0],
        end_date: dates[1],
        dimension: dimension,
        timezone: timezone,
        chart: 'pie',
        security: gawd_front.ajaxnonce,
        filter: uri,
     }).done(function (data) {
        jQuery(".opacity_div_compact").hide();
        jQuery(".loading_div_compact").hide();
        if(divID == 'gawd_post_page_popup'){
          jQuery( "#loading_div" ).remove();
          jQuery( ".gawd_btn" ).show();
          jQuery('#opacity_div, .gawd_btn').on('click', function(){
            jQuery('#opacity_div').remove();
            jQuery( ".gawd_btn" ).remove();
            jQuery( ".page_chart_div" ).remove();
            jQuery( "#loading_div" ).remove();
          })
        }
      var result = JSON.parse(data);
      result = result.chart_data ;
      
      var date1 = new Date(dates[1]).getTime();
      var date2 = new Date(dates[0]).getTime();
      var diff = ((date1 - date2) / 3600 / 24 / 1000)+1;
      if(diff > 8){
        dimension = 'Week';
        metric = metric.replace(/([A-Z])/g, " $1").trim();
        metric = metric.charAt(0).toUpperCase() + metric.slice(1);
        metric = metric.replace(/  +/g, ' ');
      }
      var chart = AmCharts.makeChart( divID, {
        "type": "pie",
        "theme": "light",
        'sequencedAnimation': false,
        "dataProvider": result,
        "valueField": metric,
        "titleField": dimension,
        "depth3D": 15,
        "angle": 30,
        "minRadius": 70,
        "startDuration": 0,
        "percentPrecision": 1,
        "precision": 0,
         "balloon":{
         "fixedPosition":true
        },
        'groupPercent': 1,
        'marginTop': 0,
        "export": {
          "enabled": true
        },
        "legend": {
          "enabled": true,
          "align": "center",
          "markerType": "circle",
        },
      } );
          jQuery("#"+divID).find('a').remove();
          //jQuery("#gbox_griddiv").remove();
          //var grid = '<table id="griddiv"></table><div id="pager"></div>';
          //jQuery('.gawd_chart_conteiner').append(grid);
      //gawd_draw_table(result,metric,metric_compare,dimension);
    })
}
function post_page_stats_front(uri,divID){
  if(typeof divID == 'undefined'){
    divID = 'gawd_post_page_popup';
  }
  var chartType = 'line';
  var fillAlphas = 0;
  var checked_line = "";
  var checked_column = "";
  if(jQuery("#gawd_chart_type_post_page").val() == 'line'){
    chartType = 'line';
    var checked = 'selected="selected"';
    fillAlphas = 0;
  }
  else if(jQuery("#gawd_chart_type_post_page").val() == 'column'){
    chartType = 'column';
    checked_column = 'selected="selected"';
    fillAlphas = 1;
  }
  var metric = typeof jQuery("#gawd_metric_post_page_popup").val() != 'undefined' ? jQuery("#gawd_metric_post_page_popup").val() : 'sessions';
  var date_30 = gawd_front.date_30;
  var date_7 = gawd_front.date_7;
  var date_yesterday = gawd_front.date_yesterday;
  var date_today = gawd_front.date_today;
  var date_this_month = gawd_front.date_this_month;
  var date_last_month = gawd_front.date_last_month;
  var date_last_week = gawd_front.date_last_week;

  start_date = typeof jQuery("#gawd_post_page_date").val() != 'undefined' ? jQuery("#gawd_post_page_date").val() : (typeof jQuery("#gawd_post_page_popup_date").val() != 'undefined' ? jQuery("#gawd_post_page_popup_date").val() : date_30);
  var dimension = 'date';
  var timezone = -(new Date().getTimezoneOffset()/60);
  var chart_div = '<div id="opacity_div"></div><div id="loading_div" style="display:none; text-align: center; position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 9999;"><img src="'+gawd_front.gawd_plugin_url+'/assets/ajax_loader.gif"  style="margin-top: 200px; width:50px;"></div><div class="page_chart_div"><div style="width:100%; height:100%; position:relative;" class="close_button_cont"><button class="gawd_btn" >X</button>';
  chart_div += '<select name="gawd_post_page_popup_date" id="gawd_post_page_popup_date" class="gawd_draw_analytics_front">';
  chart_div +=  '<option value="'+date_30+'">Last 30 Days</option><option value="'+date_7+'">Last 7 Days</option>';
  chart_div +=  '<option value="'+date_last_week+'">Last week</option><option value="'+date_last_month+'">Last month</option>';
  chart_div +=  '<option value="'+date_this_month+'">This month</option><option value="'+date_yesterday+'">Yesterday</option>';
  chart_div +=  '<option value="'+date_today+'">Today</option>';
  chart_div += '</select>';
  chart_div += '<select name="gawd_metric_post_page_popup" id="gawd_metric_post_page_popup" class="gawd_draw_analytics_front">';
  chart_div += '<option value="sessions" >Sessions</option><option value="users"  >Users</option><option value="bounceRate"  >Bounce Rate</option><option value="pageviews"  >Pageviews</option><option value="percentNewSessions">% New Sessions</option><option value="avgSessionDuration">Avg Session Duration</option><option value="pageviewsPerSession"  >Pages/Session</option>';
  chart_div += '</select>';
  chart_div += '<select name="gawd_chart_type_post_page" id="gawd_chart_type_post_page" class="gawd_draw_analytics_front">';
  chart_div += '<option '+checked_line+' value="line">Line Chart</option><option value="pie">Pie Cart</option><option '+checked_column+'  value="column">Column Chart</option>';
  chart_div += '</select>';
  chart_div += '<div id="gawd_post_page_popup"></div></div></div>';
  jQuery(".page_chart_div").remove();
  jQuery('#opacity_div').remove();
  
  jQuery( "body" ).append(chart_div);
  jQuery("#gawd_metric_post_page_popup").val(metric)
  jQuery("#gawd_post_page_popup_date").val(start_date)
  jQuery( "#loading_div" ).show();
  jQuery( "#opacity_div" ).show();
  jQuery('#gawd_post_page_popup').height('400');
  jQuery('#gawd_metric_post_page_popup, #gawd_post_page_popup_date, #gawd_chart_type_post_page').on('change',function(){
    gawd_chart_type_post_page_front(uri,'gawd_post_page_popup');
  })
    jQuery("#gawd_post_page_meta").empty();
    jQuery('#gawd_post_page_meta').height('300');
    var dates = start_date.split('/-/');
    jQuery.post(gawd_front.ajaxurl, {
        action: 'show_page_post_data',
        metric: metric,
        start_date: dates[0],
        end_date: dates[1],
        dimension: dimension,
        timezone: timezone,
        chart: 'line',
        security: gawd_front.ajaxnonce,
        filter: uri,
        
     }).done(function (data) {
        var data = JSON.parse(data);
        data = data.chart_data;
        if(divID == 'gawd_post_page_popup'){
          jQuery( "#loading_div" ).remove();
          jQuery( ".gawd_btn" ).show();
          jQuery('#opacity_div, .gawd_btn').on('click', function(){
            jQuery('#opacity_div').remove();
            jQuery( ".gawd_btn" ).remove();
            jQuery( ".page_chart_div" ).remove();
            jQuery( "#loading_div" ).remove();
          })
  
        }

        var chart = AmCharts.makeChart(divID, {
            "dataProvider": data,
            "type": "serial",
            "theme": "light",
            "percentPrecision": 1,
            "precision": 0,
            "export": {
              "enabled": true
            },
            "dataDateFormat": "YYYY-MM-DD",
            "valueAxes": [{
                "id": "g1",
                "axisAlpha": 0.4,
                "position": "left",
                "title": metric,
                "ignoreAxisWidth":false
                
            }],
            "chartCursor": {
                "pan": true,
                "valueLineEnabled": true,
                "valueLineBalloonEnabled": true,
                "cursorAlpha": 1,
                "cursorColor": "#258cbb",
                "limitToGraph": "g1",
                "valueLineAlpha": 0.2
            },
            "graphs": [{
                "type": chartType,
                "valueAxis": "g1",
                "fillAlphas": fillAlphas,
                "bullet": "round",
                "bulletBorderAlpha": 1,
                "bulletColor": "#FFFFFF",
                "bulletSize": 5,
                //"hideBulletsCount": 50,
                "lineThickness": 2,
                "title": metric,
                "useLineColorForBulletBorder": true,
                "valueField": metric,
            }],
            "categoryField": dimension,
            "categoryAxis": {
                "parseDates": true,
                "equalSpacing": true,
                "dashLength": 2,
                "minorGridEnabled": true,
                "boldLabels": true,
                "labelFrequency": 1,
            }
          } )
            jQuery("#"+divID).find('a').remove();
    })
}
