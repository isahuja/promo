<?php
try{
	$profiles = $gawd_client->get_default_profiles();
}catch (Exception $e){
	$profiles = array();
}
try{
	$goals = $gawd_client->get_default_goals();
}catch (Exception $e){
	$goals = array();
}


if (!is_array($goals)) {
    $goals = array();
}
?>
  <div class="goal_wrap">
  
    <div class="wd-upgrade-pro-main-wrap">
      <div class="wd-text">This section allows you to add Google Analytics goals for current domain.
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
    <h3 class="gawd_page_titles">Goal Management</h3>

    <p>You can set and manage goals for your website tracking. Select the View that you’re going to track and configure these options based on the type of goal you would like to set.</p>
      <form action='' method="post" id="gawd_goal_form">
        <div class="gawd_goal_row">
          <span class="gawd_goal_label">View</span>
          <span class="gawd_goal_input">
            <select name="gawd_goal_profile" class="gawd_goal_profile">
              <?php foreach ($profiles as $profile) {
                echo '<option value="' . $profile['id'] . '">' . $profile['webPropertyName'] . ' - ' . $profile['name'] . '</option>';
              } ?>
            </select>
          </span>
          <div class="gawd_info" title="Choose the website, to which you would like to set Google Analytics Goals. "></div>
          <div class='clear'></div>
        </div>
        <div class="gawd_goal_row">
          <span class="gawd_goal_label">Name</span>
          <span class="gawd_goal_input">
            <input id="gawd_goal_name" name="gawd_goal_name" class="" type="text" value="">
          </span>
          <div class="gawd_info" title="Provide a name for this goal"></div>
          <div class='clear'></div>
        </div>      
        <div class="gawd_goal_row">
          <span class="gawd_goal_label">Type</span>
          <span class="gawd_goal_input">
            <select name="gawd_goal_type" class="gawd_goal_type">
              <option value="URL_DESTINATION">Destination</option>
              <option value="VISIT_TIME_ON_SITE">Duration</option>
              <option value="VISIT_NUM_PAGES">Pages/Screens per session</option>
              <!-- <option value="EVENT">Event</option> -->
            </select>
          </span>
          <div class="gawd_info" title="Select its type (Destination, Duration, Pages/Screens per session or Event)."></div>
          <div class='clear'></div>
        </div>
        <div class="gawd_goal_duration_wrap" id="gawd_goal_duration_wrap">
          <div class="gawd_duration_label">Duration</div>
          <div class="gawd_comparison_input">
            <select name="gawd_goal_duration_comparison" class="gawd_goal_duration_comparison">
              <option value="GREATER_THAN">Greater than</option>
            </select>
          </div>
          <div class="gawd_duration">
            <div class="time_wrap">
              <!--<div class="time_label">Hour</div> -->
              <div class="time_input"><input placeholder="hour" type="number" min='0' name="gawd_visit_hour"/></div>
            </div>          
            <div class="time_wrap">
              <!--<div class="time_label">Minute</div> -->
              <div class="time_input"><input placeholder="min." type="number" min='0' name="gawd_visit_minute"/></div>
            </div>          
            <div class="time_wrap" id="time_wrap">
              <!--<div class="time_label">Second</div> -->
              <div class="time_input"><input placeholder="sec." type="number" min='0' name="gawd_visit_second"/></div>
            </div>
            <div class='clear'></div>
          </div>
          <div class="gawd_info" style="margin-left: 15px" title="Set a duration for this goal. For example, if you select 20 minutes, each time users spend 20 minutes or more on your site, it will be counted as goal completion."></div>
          <div class='clear'></div>
        </div>
        <div class="gawd_page_sessions" id="gawd_page_sessions">
          <div class="gawd_duration_label">Pages per session</div>
          <div class="gawd_comparison_input">
            <select name="gawd_goal_page_comparison" class="gawd_goal_duration_comparison">
              <option value="GREATER_THAN">Greater than</option>
            </select>
          </div>
          <div class="gawd_duration">
            <div class="time_wrap">
              <!--<div class="time_label">Hour</div> -->
             <input type="number" min='0' name="gawd_page_sessions"/>
            </div>          
            <div class='clear'></div>
          </div>
          <div class="gawd_info" style="margin-left: 15px" title="Choose the number of pages/screens users should view to complete this goal."></div>
          <div class='clear'></div>
        </div>
        <div class="gawd_page_destination" id="gawd_page_destination">
          <div class="gawd_duration_label">Destination</div>
          <div class="gawd_url_comparison_input" >
            <select name="gawd_goal_page_destination_match" class="gawd_goal_duration_comparison">
              <option value="EXACT">Equals to</option>
              <option value="HEAD">Begins with</option>
              <option value="REGEX">Regular expression</option>
            </select>
          </div>
          <div class="gawd_info" style="margin-left: 8px;" title="Set the destination of your goal. It can be equal to the specified value, as well as begin with it. You can also add a Regular Expression as destination value."></div>
          <div class="gawd_destination_url">
            <label for="gawd_case_sensitive" class="case_sensitive gawd_duration_label">URL</label>
            <div class="time_wrap">
              <div class="time_input"><input type="text" name="gawd_page_url"/></div>
            </div>          
            <div class="gawd_info" title="Set destination URL."></div>
            <div class='clear'></div>
          </div>
          <div class="gawd_destination_url">
            <label for="gawd_case_sensitive" class="case_sensitive gawd_duration_label">URL Case sensitive</label>
            <div class="time_wrap">
            <div class="onoffswitch" style="margin: 3px 0 0 6px;">
              <input type="checkbox" name="url_case_sensitve" class="onoffswitch-checkbox" id="gawd_case_sensitive">
              <label class="onoffswitch-label" for="gawd_case_sensitive">
                <span class="onoffswitch-inner"></span>
                <span class="onoffswitch-switch"></span>
              </label>
            </div>
            </div>          
            <div class="gawd_info" title="Enable this option to set destination URL case sensitive."></div>
          </div>

          <div class='clear'></div>
        </div>
        <!-- <div class="gawd_page_event" id="gawd_page_event">
          <div class="event_type_row">
            <div class="gawd_duration_label">Category</div>
            <div class="gawd_event_comparison_input" >
              <select name="gawd_goal_event_match" class="gawd_goal_duration_comparison">
                <option value="EXACT">Equals to</option>
                <option value="HEAD">Begins with</option>
                <option value="REGEX">Regular expresion</option>
              </select>
            </div>
            <div class="gawd_event_name">
                <input type="text"  name="gawd_category_name"/>
              <div class='clear'></div>
            </div>
            <div class='clear'></div>
          </div>
          <div class="event_type_row">
            <div class="gawd_duration_label">Action</div>
            <div class="gawd_event_comparison_input" >
              <select name="gawd_goal_event_match" class="gawd_goal_duration_comparison">
                <option value="EXACT">Equals to</option>
                <option value="HEAD">Begins with</option>
                <option value="REGEX">Regular expresion</option>
              </select>
            </div>
            <div class="gawd_event_name">
                <input type="text"  name="gawd_action_name"/>
              <div class='clear'></div>
            </div>
            <div class='clear'></div>
          </div>
          <div class="event_type_row">
            <div class="gawd_duration_label">Label</div>
            <div class="gawd_event_comparison_input" >
              <select name="gawd_goal_event_match" class="gawd_goal_duration_comparison">
                <option value="EXACT">Equals to</option>
                <option value="HEAD">Begins with</option>
                <option value="REGEX">Regular expresion</option>
              </select>
            </div>
            <div class="gawd_event_name">
                <input type="text"  name="gawd_label_name"/>
              <div class='clear'></div>
            </div>
            <div class='clear'></div>
          </div>
          <div class="event_type_row">
            <div class="gawd_duration_label">Value</div>
            <div class="gawd_event_comparison_input" >
              <select name="gawd_goal_event_match" class="gawd_goal_duration_comparison">
                <option value="EXACT">Equals to</option>
                <option value="GREATER_THAN">Greater than</option>
                <option value="LESS_THAN">Less than</option>
              </select>
            </div>
            <div class="gawd_event_name">
                <input type="text"  name="gawd_value_name"/>
              <div class='clear'></div>
            </div>
            <div class='clear'></div>
          </div> 

        </div>-->
        <div class="gawd_buttons" id="goal_submit">
          <input class="button_gawd" type="button" name="add_goal" value="Save"/>
        </div>
        <?php wp_nonce_field('gawd_save_form', 'gawd_save_form_fild'); ?>
        <?php if (!empty($goals)) {?>
          <input class="goal_max_id" id="goal_max_id" name="goal_max_id" type="hidden" value="<?php echo count($goals[$profile_id]);?>"/>
        <?php } ?>

      </form>
      <?php if (!empty($goals)) {
        $counter = 0;
        foreach ($goals as $profile_id => $profile_goals) { ?>
          <table border="1" class="gawd_table" id="<?php echo $profile_id; ?>" style="<?php echo (($counter != 0) ? 'display:none;' : ''); ?>">
              <tr>
                  <th>Id</th>
                  <th>Name</th>
                  <th>Type</th>
                  <th>Match Type</th>
                  <th>Value</th>
              </tr>
              <?php
              foreach ($profile_goals as $goal) {
                $case_sensitive = $goal['caseSensitive'] ? ' - case sensitive' : '';
                  ?>
                  <tr class="gawd_rows">
                      <td><?php echo $goal['id']; ?></td>
                      <td><?php echo $goal['name']; ?></td>
                      <td><?php echo $goal['type']; ?></td>
                      <td><?php echo $goal['match_type']; ?></td>
                      <td><?php echo $goal['value'].$case_sensitive; ?></td>
                  </tr>
                  <?php
              }
              ?>
          </table>
        <?php $counter++; }
      } ?>

  </div>
<script>
    jQuery('.gawd_goal_type').on('change', function () {
        if (jQuery('.gawd_goal_type :selected').val() == 'VISIT_TIME_ON_SITE') {
            jQuery('.gawd_goal_duration_wrap').show();
            jQuery('.gawd_page_sessions').hide();
            jQuery('.gawd_page_destination').hide();
            jQuery('.gawd_page_event').hide();
        } else if (jQuery('.gawd_goal_type :selected').val() == 'VISIT_NUM_PAGES') {
            jQuery('.gawd_goal_duration_wrap').hide();
            jQuery('.gawd_page_destination').hide();
            jQuery('.gawd_page_event').hide();
            jQuery('.gawd_page_sessions').show();
        } else if (jQuery('.gawd_goal_type :selected').val() == 'EVENT') {
            jQuery('.gawd_goal_duration_wrap').hide();
            jQuery('.gawd_page_sessions').hide();
            jQuery('.gawd_page_destination').hide();
            jQuery('.gawd_page_event').show();
        } else {
            jQuery('.gawd_goal_duration_wrap').hide();
            jQuery('.gawd_page_sessions').hide();
            jQuery('.gawd_page_event').hide();
            jQuery('.gawd_page_destination').show();
        }
    });
    jQuery('.button_gawd').on('click',function(){
      var submit_form = true;
        var gawd_goal_name = jQuery("#gawd_goal_name");
        var gawd_goal_name = jQuery("#gawd_goal_name");
        if(gawd_goal_name.val()=== ""){
          gawd_goal_name.addClass('gawd_invalid');
          submit_form = false;
        }
        else if(
        (jQuery('input[name="gawd_page_sessions"]').val() === '' && jQuery('.gawd_goal_type :selected').val() == 'VISIT_NUM_PAGES') || 
        (jQuery('input[name="gawd_page_url"]').val() === '' && jQuery('.gawd_goal_type :selected').val() == 'URL_DESTINATION') || 
        ((jQuery('input[name="gawd_visit_hour"]').val() === '' || jQuery('input[name="gawd_visit_minute"]').val() === '' || jQuery('input[name="gawd_visit_second"]').val() === '') && jQuery('.gawd_goal_type :selected').val() == 'VISIT_TIME_ON_SITE')){
          jQuery('input[name="gawd_page_url"]').addClass('gawd_invalid');
          jQuery('input[name="gawd_page_sessions"]').addClass('gawd_invalid');
          jQuery('input[name="gawd_visit_hour"]').addClass('gawd_invalid');
          jQuery('input[name="gawd_visit_minute"]').addClass('gawd_invalid');
          jQuery('input[name="gawd_visit_second"]').addClass('gawd_invalid');
          submit_form = false;
        }
        else{
          gawd_goal_name.removeClass('gawd_invalid');
          jQuery('input[name="gawd_page_url"]').removeClass('gawd_invalid');
          jQuery('input[name="gawd_page_sessions"]').removeClass('gawd_invalid');
          jQuery('input[name="gawd_visit_hour"]').removeClass('gawd_invalid');
          jQuery('input[name="gawd_visit_minute"]').removeClass('gawd_invalid');
          jQuery('input[name="gawd_visit_second"]').removeClass('gawd_invalid');
        }
        
      if(jQuery('.gawd_table tr').length -1 >= 20){
        alert('You have reached the maximum number of goals.')
        return;
      }
      jQuery('#goal_max_id').val(jQuery('.gawd_rows').length);
      if(submit_form){
        jQuery('#gawd_goal_form').submit();
        return false;
      }
    });
    jQuery('.gawd_goal_profile').on('change', function(){
      jQuery('.gawd_table').each(function(){
        jQuery(this).hide();
      });
      var id = jQuery(this).val();
      jQuery('#' + id).show();
    });
  </script>
