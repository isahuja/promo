<?php
  $id = isset($_GET['post']) ? $_GET['post'] : '';
  $uri_parts = explode( '/', get_permalink( $id ), 4 );
  if ( isset( $uri_parts[3] ) ) {
    $uri = '/' . $uri_parts[3];
  }
  $uri = explode( '/',$uri);
  end($uri);      
  $key = key($uri);
  $uri = '/' . $uri[$key-1];
  $filter = rawurlencode( rawurldecode( $uri ) );
  /*$filter = rawurlencode( rawurldecode(get_the_title()));*/
?>
  <a href="#" class="gawd_btn">X</a>
<!--<select name="gawd_post_page_date" id="gawd_post_page_date" >
  <option value="<?php echo date('Y-m-d', strtotime('-30 days')); ?>"><?php  _e('Last 30 Days', 'gawd'); ?></option>
  <option value="<?php echo date('Y-m-d', strtotime('-7 days')); ?>"><?php _e('Last 7 Days', 'gawd'); ?></option>
</select>    -->
<div class="gawd_content" id="gawd_content_range" >
  <div id="reportrange" class="pull-right" style="float:none !important">
    <span></span> <b class="caret"></b>
  </div>
  <input type="hidden" id="gawd_start_end_date"/>
</div>
<select name="gawd_metric_post_page" id="gawd_metric_post_page" >
  <option value="sessions"><?php _e('Sessions', 'gawd'); ?></option>
  <option value="users"  ><?php _e('Users', 'gawd'); ?></option>
  <option value="bounceRate"  ><?php _e('Bounce Rate', 'gawd'); ?></option>
  <option value="pageviews"  ><?php _e('Pageviews', 'gawd'); ?></option>
  <option value="percentNewSessions"><?php _e('% New Sessions', 'gawd'); ?></option>
  <option value="avgSessionDuration"><?php _e('Avg Session Duration', 'gawd'); ?></option>
  <option value="pageviewsPerSession"  ><?php echo __('Pages/Session', 'gawd'); ?></option>
</select>
<select name="gawd_chart_type_post_page" id="gawd_chart_type_post_page" class="gawd_draw_analytics">
  <option value="line"  ><?php  _e('Line Chart', 'gawd'); ?></option>
  <option value="pie"  ><?php  _e('Pie Chart', 'gawd'); ?></option>
  <option value="column"  ><?php  _e('Columns', 'gawd'); ?></option>
</select>
<div class="clear"></div>

<div class="">
  <div class="opacity_div_compact">
    <div class="loading_div_compact">
      <img src="<?php echo GAWD_URL . '/assets/ajax_loader.gif'; ?>"  style="margin-top: 200px; width:50px;">
    </div>
  </div>
<div class="gawd_post_page_meta" id="gawd_post_page_meta"></div>
</div> 
<script>
jQuery(document).ready(function(){
  datepicker_js('right','gawd_chart_type_post_page_callback');
  jQuery('#gawd_metric_post_page, #gawd_post_page_date, #gawd_chart_type_post_page').on('change',function(){
    gawd_chart_type_post_page('#<?php echo $filter;?>','gawd_post_page_meta');
  })
})
function gawd_chart_type_post_page_callback(start, end){
    jQuery('#reportrange span').html(start.format('Y-MM-DD') + ' - ' + end.format('Y-MM-DD'));
    jQuery('#gawd_start_end_date').val(start.format('Y-MM-DD') + '/-/' + end.format('Y-MM-DD'));
    gawd_chart_type_post_page('#<?php echo $filter;?>','gawd_post_page_meta');
}

</script>