<?php
$gawd_settings = get_option('gawd_settings');
$gawd_own_project = get_option('gawd_own_project');
$gawd_client = GAWD_google_client::get_instance();
$profiles = $gawd_client->get_profiles();
$accounts = $gawd_client->get_management_accounts();
$tabs = get_option('gawd_menu_items');
$current_user = get_current_user_id();
$saved_user_menues = get_option('gawd_menu_for_user');

if(isset($gawd_own_project) && $gawd_own_project && intval($gawd_own_project)==1){
  $own_credentials = get_option("gawd_credentials");
  $own_cliend_secret = $own_credentials['project_secret'];
  $own_cliend_id = $own_credentials['project_id'];
  echo'<form method="post" style="width: 92.9%;">
        <div class="">
          <input type="hidden" name="reset_data" id="reset_data"/>
          <input type="button" class="button_gawd" id="gawd_reset_button" value="RESET"/>
          <div class="clear"></div>
        </div>
      </form>';
}
$filters =  $gawd_client->get_management_filters();
if (!is_array($filters)) {
    $filters = array();
}

$gawd_permissions = isset($gawd_settings['gawd_permissions']) ? implode(',',$gawd_settings['gawd_permissions']) : 'manage_options';
$gawd_excluded_users = isset($gawd_settings['gawd_excluded_users']) ? $gawd_settings['gawd_excluded_users'] : array();
$gawd_excluded_roles = isset($gawd_settings['gawd_excluded_roles']) ? $gawd_settings['gawd_excluded_roles'] : array();
$gawd_backend_roles = isset($gawd_settings['gawd_backend_roles']) ? implode(',',$gawd_settings['gawd_backend_roles']) : 'administrator';
$gawd_frontend_roles = isset($gawd_settings['gawd_frontend_roles']) ? implode(',',$gawd_settings['gawd_frontend_roles']) : 'administrator';
$gawd_post_page_roles = isset($gawd_settings['gawd_post_page_roles']) ? implode(',',$gawd_settings['gawd_post_page_roles']) : 'administrator';
$gawd_file_formats = isset($gawd_settings['gawd_file_formats']) ? $gawd_settings['gawd_file_formats'] : '';
$enable_hover_tooltip = isset($gawd_settings['enable_hover_tooltip']) ? $gawd_settings['enable_hover_tooltip'] : 'on';
$exclude_events = isset($gawd_settings['exclude_events']) ? $gawd_settings['exclude_events'] : '';
$enable_cross_domain = isset($gawd_settings['enable_cross_domain']) ? $gawd_settings['enable_cross_domain'] : '';
$default_date = isset($gawd_settings['default_date']) ? $gawd_settings['default_date'] : '';
$default_date_format = isset($gawd_settings['default_date_format']) ? $gawd_settings['default_date_format'] : '';
$post_page_chart = isset($gawd_settings['post_page_chart']) ? $gawd_settings['post_page_chart'] : '';
$roles = new WP_Roles();
$roles_changed = array();
$_roles_changed = array();
foreach($roles->role_names as $key => $name){
  $_roles_changed['role_names'][$name] = $key;
}
foreach($roles->role_names as $key => $name){
  if($name == 'Administrator'){
    $key = 'manage_options';
  }
  elseif($name == 'Editor'){
    $key = 'moderate_comments';
  }
  elseif($name == 'Author'){
    $key = 'publish_posts';
  }
  elseif($name == 'Contributor'){
    $key = 'edit_posts';
  }
  else{
    $key = 'read';
  }
  $roles_changed['role_names'][$name] = $key;
}
?>

<div id="gawd_body">
   <div class="wd-upgrade-pro-main-wrap">
    <div class="wd-text">Authenticate your Google account and customize Google Analytics settings in this section.
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
  <div class="resp_menu"><div class="menu_img"></div><div class="button_label">SETTINGS</div><div class="clear"></div></div>
  <div class="gawd_menu_coteiner gawd_settings_menu_coteiner">  
    <ul class="gawd_menu_ul">
      <li class="gawd_menu_li" id="gawd_authenicate">
        Authenticate
      </li>
      <li class="gawd_menu_li gawd_pro_menu" id="gawd_alerts">
        Alerts
        <span class="gawd_pro_flag">Paid</span>
      </li>
      <li class="gawd_menu_li gawd_pro_menu" id="gawd_pushover">
        Pushover
        <span class="gawd_pro_flag">Paid</span>
      </li>
      <li class="gawd_menu_li" id="gawd_filters">
        Filters
      </li>
      <li class="gawd_menu_li" id="gawd_emails">
        Emails
      </li>      
      <li class=" gawd_menu_li" id="gawd_advanced">
        Advanced
      </li>
    </ul>
  </div>
  <div id="gawd_right_conteiner">
    <h3 class="gawd_page_titles">Settings</h3>

    <form method="post" id="gawd_form">
      <div class="gawd_authenicate">
        <div id="opacity_div" style="display: none; background-color: rgba(0, 0, 0, 0.2); position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 99998;"></div>
        <div id="loading_div" style="display:none; text-align: center; position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 99999;">
          <img src="<?php echo GAWD_URL . '/assets/ajax_loader.gif'; ?>"  style="margin-top: 200px; width:50px;">
        </div>
        <div id="gawd_auth_url"  style="cursor: pointer; margin: -10px 0 20px 0;">
          <div style="float:left; color:#444;padding:5px 5px 5px 0">Press <b>Reauthenticate</b> button to change your Google account.</div>
          <div class="gawd_auth_button" onclick="gawd_auth_popup(<?php echo GAWD_google_client::create_authentication_url();?>, 800,400)">REAUTHENTICATE</div>
        <div class="clear"></div>
        </div>
        <div id="gawd_auth_code">
          <p style="margin:0;color: #444;">Paste the authentication code from the popup to this input.</p>
          <input id="gawd_token" type="text">
          <div id="gawd_auth_code_submit">SUBMIT</div>
        </div> 
        
        <div class="gawd_own_wrap">
          <label for="gawd_own_project">
            <input type="checkbox" <?php echo $gawd_own_project != '' ? 'checked disabled' : '';?> name="gawd_own_project" id="gawd_own_project"/>
            <span>Use your own project</span>          
            <div class="gawd_info" title="Mark as checked to use your project, which you created on console.developers.google.com"></div>
          </label>
          <div class="own_inputs" <?php echo $gawd_own_project != '' ? 'checked' : 'style="display:none"';?>>
            <div class="gawd_goal_row">
              <div class="gawd_goal_label">Client ID</div>
              <div class="gawd_goal_input">
                <input type="text" value="<?php echo isset($own_cliend_id) ? $own_cliend_id : '';?>" name="gawd_own_client_id"/>
              </div>
              <div class="gawd_info" title="Paste Client ID key. For more information about getting project keys please check the plugin User Guide."></div>
              <div class='clear'></div>
            </div>            
            <div class="gawd_goal_row">
              <div class="gawd_goal_label">Client Secret</div>
              <div class="gawd_goal_input">
                <input type="text" value="<?php echo isset($own_cliend_secret) ? $own_cliend_secret : '';?>" name="gawd_own_client_secret"/>
              </div>
              <div class="gawd_info" title="Paste Client Secret key. For more information about getting project keys please check the plugin User Guide."></div>
              <div class='clear'></div>
            </div>
          </div>
        </div>
      </div> 
      <div class="gawd_alerts">
        <img class="gawd_pro_img" data-attr="alerts" style="margin-top:10px" src="<?php echo GAWD_URL . '/assets/freePages/alerts.png';?>"/>
      </div>       
      <div class="gawd_pushover">
        <img class="gawd_pro_img" data-attr="pushovers" style="margin-top:10px" src="<?php echo GAWD_URL . '/assets/freePages/pushovers.png';?>"/>
      </div> 
      <div class="gawd_filters">
        <div class="gawd_goal_row">
          <span class="gawd_goal_label">View</span>
          
          <span class="gawd_goal_input">
             
            </select>
            <select title="Click to pick the website, filters of which you’d like to display." name="gawd_id" onchange="change_filter_account(this)">
              <?php 
              foreach ($profiles as $property_name => $property): ?>
              <optgroup label="<?php echo $property_name; ?>">
                <?php foreach ($property as $profile):
                  $webPropertyId = $profile['webPropertyId'];
                  $id = $profile['id']; 
                  $name = $profile['name'];
                  $selected = '';
                  if($id == $gawd_user_data['gawd_id']){
                    $selected = 'selected="selected"';
                    $filter_account_name = $property_name;
                  }
                ?>
                  <option value="<?php echo $id; ?>" <?php echo $selected; ?>><?php echo $property_name.' - '.$name ; ?></option>
                <?php endforeach ?>
              </optgroup>
              <?php endforeach ?>
            </select>
            <input type="hidden" name='account_name' id='account_name'/>
            <input type="hidden" name='web_property_name' id='web_property_name'/>
          </span>
          <div class="gawd_info" title="Select the website, to which you would like to configure this filter."></div>
          <div class='clear'></div>
        </div>
        <div class="gawd_goal_row">
          <span class="gawd_goal_label">Name</span>
          <span class="gawd_goal_input">
            <input id="gawd_goal_name " class="gawd_filter_name_fild" name="gawd_filter_name" type="text">
          </span>
          <div class="gawd_info" title="Write a title for the filter."></div>
          <div class='clear'></div>
        </div>      
        <div class="gawd_goal_row">
          <span class="gawd_goal_label">Type</span>
          <span class="gawd_goal_input">
            <select name="gawd_filter_type" id="gawd_filter_type">
              <option data-name="IP" value="GEO_IP_ADDRESS">Exclude Traffic From IP Address</option>
              <option data-name="Country" value="GEO_COUNTRY">Exclude Traffic From Country</option>
              <option data-name="Region" value="GEO_REGION">Exclude Traffic From Region</option>
              <option data-name="City" value="GEO_CITY">Exclude Traffic From City</option>
            </select>
          </span>
          <div class="gawd_info" title="Choose a type for tracking exclusions: IP address, Country, Region or City."></div>
          <div class='clear'></div>
        </div>
        <div class="gawd_goal_row" id="gawd_filter_value_cont">
          <span class="gawd_goal_label" id="gawd_filter_name">IP</span>
          <span class="gawd_goal_input">
            <div class="time_input"><input id="gawd_filter_value" type="text" name="gawd_filter_value"/></div>
          </span>
          <div class="gawd_info" title="Define IP address to filter from Google Analytics tracking."></div>
          <div class='clear'></div>
        </div>  
     <?php 
      if (!empty($filters)) {
        ?>
        <table border="1" class="gawd_table">
            <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Value</th>
                <th>View</th>
                <th>Action</th>

            </tr>
            <?php
            foreach ($filters as $filter) {
              $filter_type = 'Not Supported';
              $filter_value = $filter['value'] != "" ? $filter['value'] : 'Not Suported';
              if($filter['type'] == "GEO_COUNTRY"){
                $filter_type = 'Exclude Traffic From Country';
              }
              elseif($filter['type'] == "GEO_REGION"){
                $filter_type = 'Exclude Traffic From Region';
              }
              elseif($filter['type'] == "GEO_CITY"){
                $filter_type = 'Exclude Traffic From City';
              }
              elseif($filter['type'] == "GEO_IP_ADDRESS"){
                $filter_type = 'Exclude Traffic From IP Address';
              }
                ?>
                <tr data-key="<?php echo $filter['id']; ?>" >
                    <td><?php echo $filter['name']; ?></td>
                    <td><?php echo $filter_type; ?></td>
                    <td><?php echo $filter_value; ?></td>
                    <td><?php echo $filter['view']; ?></td>
                    <td><a href="" class="gawd_filter_remove" onclick="if (confirm('<?php echo addslashes(__("Do you want to delete selected item?", 'gawd')); ?>')) {gawd_remove_item('<?php echo $filter['id']; ?>','gawd_filter_remove');return false;} else {return false;}">remove</a></td>
                </tr>
                <?php
            }
            ?>
        </table>
        <?php
      }
      else{
        echo 'There is no data for this view.';
      }
      ?>
      </div> 
      <div class="gawd_emails">
        <?php
        $gawd_emails = get_option('gawd_email');

        if($gawd_emails){
          ?>
          <table border="1" class="gawd_table">
            <tr>
              <th>Subject</th>
              <th>Frequency</th>
              <th>Start Date</th>
              <th>Recipients</th>
              <th>View</th>
              <th>Action</th>
            </tr>
            <?php
          foreach($gawd_emails as $key => $email){
            if($email['period'] == 'gawd_weekly'){
              $email['period'] = 'Weekly';
            }
            elseif($email['period'] == 'gawd_monthly'){
              $email['period'] = 'Monthly';
            }
            else{
              $email['period'] = 'Daily';
            }
            ?>
            <tr data-key="<?php echo $key +1; ?>" >
              <td><?php echo $email['name']; ?></td>
              <td><?php echo $email['period']; ?></td>
              <td><?php echo $email['creation_date']; ?></td>
              <td><span class="gawd_break"><?php echo implode(', ',$email['emails']); ?></span></td>
              <td><span class="gawd_break"><?php echo $email['view_id']; ?></span></td>
              <td><a href="" class="gawd_remove_emails" onclick="if (confirm('<?php echo addslashes(__("Do you want to delete selected item?", 'gawd')); ?>')) {gawd_remove_item('<?php echo $key +1; ?>','gawd_email_remove');return false;} else {return false;}">remove</a></td>
            </tr>
            <?php
          }
          ?>
            </table>
            <?php
        }
        else{
          echo '<a href="' . admin_url() . 'admin.php?page=gawd_reports">You can setup sending e-mail to recipients for any report.</a>';
        }
        ?>          
      </div>   
      <div class="gawd_advanced">
        <div class="settings_row">
          <div class="onoffswitch">
            <input type="checkbox" name="gawd_show_in_dashboard" class="onoffswitch-checkbox" id="gawd_show_in_dashboard" <?php echo isset($gawd_settings['gawd_show_in_dashboard']) && 'on' == $gawd_settings['gawd_show_in_dashboard'] ? 'checked' : '';?>>
            <label class="onoffswitch-label" for="gawd_show_in_dashboard">
              <span class="onoffswitch-inner"></span>
              <span class="onoffswitch-switch"></span>
            </label>
          </div>
          <div class="gawd_info" title="Enable this option to display Google Analytics overview report on WordPress Dashboard."></div>
          <div class="onoffswitch_text">
            Analytics on WordPress Dashboard
          </div>
          <div class="clear"></div>
        </div>
        <div class="settings_row">
          <div class="onoffswitch">
            <input type="checkbox" name="enable_hover_tooltip" class="onoffswitch-checkbox" id="enable_hover_tooltip" <?php echo $enable_hover_tooltip != '' ? 'checked' : '';?>>
            <label class="onoffswitch-label" for="enable_hover_tooltip">
              <span class="onoffswitch-inner"></span>
              <span class="onoffswitch-switch"></span>
            </label>
          </div>
          <div class="gawd_info" title="Click to enable/disable help text for WD Google Analytics reports."></div>
          <div class="onoffswitch_text">
            Enable reports tooltips
          </div>
          <div class="clear"></div>
        </div>
        <div class="settings_row">
          <div class="onoffswitch onoffswitch_disabled">
            <input disabled type="checkbox" name="adsense_acc_linking" class="onoffswitch-checkbox" id="adsense_acc_linking" >
            <label class="onoffswitch-label" for="adsense_acc_linking">
              <span class="onoffswitch-inner"></span>
              <span class="onoffswitch-switch"></span>
            </label>
          </div>
          <div class="gawd_info" title="Turn this option on to get AdSense tracking reports. Make sure to link your Google AdSense to Google Analytics first (find out more in plugin User Guide)"></div>
          <div class="onoffswitch_text">
            Enable AdSense link tracking  <a target="_blank" href="https://web-dorado.com/products/wordpress-google-analytics-plugin.html" class="gawd_pro"> ( This feature is available in Google Analytics WD Pro. )</a>
          </div>
          <div class="clear"></div>
        </div>
        <div class="settings_row">
          <div class="onoffswitch">
            <input type="checkbox" name="post_page_chart" class="onoffswitch-checkbox" id="post_page_chart" <?php echo $post_page_chart != '' ? 'checked' : '';?>>
            <label class="onoffswitch-label" for="post_page_chart">
              <span class="onoffswitch-inner"></span>
              <span class="onoffswitch-switch"></span>
            </label>
          </div>
          <div class="gawd_info" title="Enable this option to display individual page and post reports on frontend and backend."></div>
          <div class="onoffswitch_text">
            Enable reports on posts/pages (frontend and backend)
          </div>
          <div class="clear"></div>
        </div>
        <div class="settings_row">
          <div class="onoffswitch">
            <input type="checkbox" name="exclude_events" class="onoffswitch-checkbox" id="exclude_events" <?php echo $exclude_events != '' ? 'checked' : '';?>>
            <label class="onoffswitch-label" for="exclude_events">
              <span class="onoffswitch-inner"></span>
              <span class="onoffswitch-switch"></span>
            </label>
          </div>
          <div class="gawd_info" title="For example, watching a video is a non-interactive event, whereas submitting a form is interactive. Enable this option to filter non-interactive events while calculating bounce-rate."></div>
          <div class="onoffswitch_text">
            Exclude non-interactive events from bounce-rate calculation
          </div>
          <div class="clear"></div>
        </div>
        <div class="settings_row">
          <div class="onoffswitch">
            <input type="checkbox" name="enable_cross_domain" class="onoffswitch-checkbox" id="enable_cross_domain" <?php echo $enable_cross_domain != '' ? 'checked' : '';?>>
            <label class="onoffswitch-label" for="enable_cross_domain">
              <span class="onoffswitch-inner"></span>
              <span class="onoffswitch-switch"></span>
            </label>
          </div>
          <div class="gawd_info" title="Enable Cross domain tracking to let Google Analytics see similar activities on two related websites as single session."></div>
          <div class="onoffswitch_text">
            Enable Cross Domain Tracking
          </div>
          <div class="clear"></div>
        </div>
        <?php 
          $cross_dom_show = $enable_cross_domain == '' ? 'style="display:none"' : '';
        ?> 
        <div id="cross_domains" class="gawd_goal_row" <?php echo $cross_dom_show;?>>
          <span class="gawd_goal_label">Cross Domains</span>
          <span class="gawd_goal_input">
            <div class="time_input">
              <?php $gawd_settings_cross_domains = get_option("gawd_settings");
              if(isset($gawd_settings_cross_domains) && isset($gawd_settings_cross_domains["cross_domains"])){
                $gawd_settings_cross_domains = $gawd_settings_cross_domains["cross_domains"];
              }else{
                $gawd_settings_cross_domains = "";
              }
              ?>
              <input type="text" value="<?php echo $gawd_settings_cross_domains;?>" name="cross_domains">
            </div>
          </span>
          <div class="gawd_info" title="Provide cross domain links separated by commas. The links should have the following format: http://example.com"></div>
          <div class="clear"></div>
        </div>
          
        <div class="gawd_goal_row">
          <?php $gawd_settings_site_speed_rate = get_option("gawd_settings");
          if(isset($gawd_settings_site_speed_rate) && isset($gawd_settings_site_speed_rate["site_speed_rate"])){
            $gawd_settings_site_speed_rate = intval($gawd_settings_site_speed_rate["site_speed_rate"]);
          }else{
            $gawd_settings_site_speed_rate = 1;
          }
          ?>
          <span class="gawd_goal_label">Site Speed SR (%)</span>
          <span class="gawd_goal_input">
            <div class="time_input"><input value="<?php echo $gawd_settings_site_speed_rate;?>" type="number" min="1" name="site_speed_rate"></div>
          </span>
          <div class="gawd_info" title="Define the percentage of users, which activity should be evaluated for Site Speed report."></div>
          <div class="clear"></div>
        </div>
        <div class="gawd_goal_row">
          <div class="gawd_goal_label">Back end plugin permissions</div>
          <div class="checkbox_wrap">
              <div class="time_wrap gawd_permissions"><span data-attribute="manage_options" class="gawd_permission">Administrator</span></div>
             <?php 
                if($gawd_permissions != ''){
                  foreach(array_intersect($roles_changed['role_names'], explode(',',$gawd_permissions)) as $key => $_roles){
                    if($key == 'Administrator'){
                      continue;
                    }
                    ?>
                    <div class="time_wrap gawd_permissions"><span data-attribute="<?php echo $_roles;?>" class="gawd_permission"><?php echo $key;?></span><span class="remove_gawd_permission gawd_remove">X</span></div>
                 <?php
                  }
                }
                 ?>
             <input type="button" id="open_gawd_permissions" class="gawd_chose_btn button_gawd" value="Choose"/>
            <div class='clear'></div>
          </div>
          <div class="gawd_info" title="Select user roles to permit access to WD Google Analytics plugin. Only Administrator users can view it by default."></div>
        <div class='clear'></div>
        </div> 
        <div class="gawd_goal_row">
          <div class="gawd_goal_label">Dashboard overview permissions</div>
          <div class="checkbox_wrap">
          <div class="time_wrap dashboard_report_permissions"><span data-attribute="administrator" class="dashboard_report_permission">Administrator</span></div>
             <?php 
             if($gawd_backend_roles != ''){
                  foreach(array_intersect($_roles_changed['role_names'],explode(',',$gawd_backend_roles)) as $key => $roles){
                    if($roles == 'administrator'){
                      continue;
                    }
                    ?>
                    <div class="time_wrap dashboard_report_permissions"><span data-attribute="<?php echo $roles;?>" class="dashboard_report_permission"><?php echo $key;?></span><span class="remove_dashboard_report_permission gawd_remove">X</span></div>
                 <?php
                 }
             }
                 ?>
            <input type="button" id="open_dashboard_report_permissions" class="gawd_chose_btn button_gawd" value="Choose"/>
            <div class='clear'></div>
          </div>
          <div class="gawd_info" title="Select user roles, which will have access to view WordPress Dashboard report."></div>
          <div class='clear'></div>
        </div>
        <div class="gawd_goal_row">
          <div class="gawd_goal_label">Post/Page report permissions</div>
          <div class="checkbox_wrap">
            <div class="time_wrap gawd_post_page_roles"><span data-attribute="administrator" class="gawd_post_page_role">Administrator</span></div>
               <?php 
             if($gawd_post_page_roles != ''){
                  foreach(array_intersect($_roles_changed['role_names'],explode(',',$gawd_post_page_roles)) as $key => $roles){
                    if($roles == 'administrator'){
                      continue;
                    }
                    ?>
                    <div class="time_wrap gawd_post_page_roles"><span data-attribute="<?php echo $roles;?>" class="gawd_post_page_role"><?php echo $key;?></span><span class="remove_post_page_role gawd_remove">X</span></div>
                 <?php
                 }
             }
                 ?>
            <input type="button" id="post_page_report_permissions" class="gawd_chose_btn button_gawd" value="Choose"/>
            <div class='clear'></div>
          </div>
          <div class="gawd_info" title="Select user roles, which will have access to view reports from frontend of your website."></div>
          <div class='clear'></div>
        </div> 
        <div class="gawd_goal_row">
          <div class="gawd_goal_label">Frontend report permissions</div>
          <div class="checkbox_wrap">
            <div class="time_wrap frontend_report_roles"><span data-attribute="administrator" class="frontend_report_role">Administrator</span></div>
               <?php 
             if($gawd_frontend_roles != ''){
                  foreach(array_intersect($_roles_changed['role_names'],explode(',',$gawd_frontend_roles)) as $key => $roles){
                    if($roles == 'administrator'){
                      continue;
                    }
                    ?>
                    <div class="time_wrap frontend_report_roles"><span data-attribute="<?php echo $roles;?>" class="frontend_report_role"><?php echo $key;?></span><span class="remove_frontend_report_role gawd_remove">X</span></div>
                 <?php
                 }
             }
                 ?>
              <input type="button" id="open_frontend_report_permissions" class="gawd_chose_btn button_gawd" value="Choose"/>
            <div class='clear'></div>
          </div>
          <div class="gawd_info" title="Select user roles, which will have access to view reports from frontend of your website."></div>
          <div class='clear'></div>
        </div> 
        <div class="gawd_goal_row">
          <span class="gawd_goal_label">Date format</span>
          <span class="gawd_goal_input">
             <select name="default_date_format" id="default_date_format">
              <option <?php selected($default_date_format,'ymd_with_week');?> value="ymd_with_week">l, Y-m-d</option>
              <option <?php selected($default_date_format,'ymd_without_week');?> value="ymd_without_week">Y-m-d</option>
              <option <?php selected($default_date_format,'month_name_with_week');?> value="month_name_with_week">l, F d, Y</option>
              <option <?php selected($default_date_format,'month_name_without_week');?> value="month_name_without_week">F d, Y</option>
            </select>
          </span>
          <div class="gawd_info" title="Choose the date format"></div>
          <div class='clear'></div>
        </div>
        <div class="gawd_goal_row">
          <span class="gawd_goal_label">Default Date</span>
          <span class="gawd_goal_input">
            <select name="default_date" id="default_date">
              <option id='gawd_last_30days' <?php selected($default_date,'last_30days');?> value="last_30days">Last 30 Days</option>
              <option id='gawd_last_7days' <?php selected($default_date,'last_7days');?> value="last_7days">Last 7 Days</option>
              <option id='gawd_last_week' <?php selected($default_date,'last_week');?> value="last_week">Last Week</option>
              <option id='gawd_this_month' <?php selected($default_date,'this_month');?> value="this_month">This Month</option>
              <option id='gawd_last_month' <?php selected($default_date,'last_month');?> value="last_month">Last Month</option>
              <option id='gawd_today' <?php selected($default_date,'today');?> value="today">Today</option>
              <option id='gawd_yesterday' <?php selected($default_date,'yesterday');?> value="yesterday">Yesterday</option>
            </select>
          </span>
          <div class="gawd_info" title="Choose the initial time period, which will be applied to all reports as their date range."></div>
          <div class='clear'></div>
        </div>
      </div>    
      <div class="gawd_submit">
        <input type="button"  class="button_gawd" id="gawd_settings_button"value="Save"/>
        <input type="button" style="display:none;"  class="button_gawd" id="gawd_settings_logout"value="Logout"/>
      </div>
      <input type='hidden' name="gawd_alert_remove" id="gawd_alert_remove"/>
      <input type='hidden' name="gawd_menu_remove" id="gawd_menu_remove"/>
      <input type='hidden' name="gawd_pushover_remove" id="gawd_pushover_remove"/>
      <input type='hidden' name="gawd_email_remove" id="gawd_email_remove"/>
      <input type='hidden' name="gawd_filter_remove" id="gawd_filter_remove"/>
      <input type='hidden' name="gawd_settings_tab" id="gawd_settings_tab"/>
      <input type='hidden' name="settings_submit" id="gawd_settings_submit"/>
      <input type='hidden' name="gawd_settings_logout" id="gawd_settings_logout_val"/>
      <?php wp_nonce_field('gawd_save_form', 'gawd_save_form_fild'); ?>
      
      <div class="gawd_permissions_popup_overlay"></div>
      <div class="gawd_permissions_popup">
        <div class="close_btn_cont">
          <div class="gawd_permission_popup_btn">X</div>
        </div>
        <div class="gawd_permissions_popup_content">
        </div>        
         <div class="add_btn_cont">
          <input type="button" class="button_gawd" id="add_roles" value="Add"/>
        </div>
      </div>
      <input type='hidden' name="gawd_backend_roles" id="gawd_backend_roles" value="<?php echo $gawd_backend_roles;?>"/>
      <input type='hidden' name="gawd_frontend_roles" id="gawd_frontend_roles" value="<?php echo $gawd_frontend_roles;?>"/>
      <input type='hidden' name="gawd_post_page_roles" id="gawd_post_page_roles" value="<?php echo $gawd_post_page_roles;?>"/>
      <input type='hidden' name="gawd_permissions" id="gawd_permissions" value="<?php echo $gawd_permissions;?>"/>
    </form>
  </div>

  <div class="clear"></div>
</div>

<script>
jQuery('.gawd_chose_btn').on('click', function(){
  permissions_popup_content(jQuery(this).attr('id'))
});

function permissions_popup_content(id){
  if(id != "open_gawd_permissions"){
    var roles = <?php  echo json_encode(new WP_Roles());?>;
  }
  else{
    var roles = <?php  echo json_encode($roles_changed);?>;
  }
  var inp_id = 0;
  var html = '';
  var popup_overlay = 'gawd_permissions_popup_overlay';
  var popup_body = 'gawd_permissions_popup';
  var popup_btn = 'gawd_permission_popup_btn';
  var add_role = 'add_roles';
  var permissions_array = '';
  jQuery("." + popup_body).fadeIn('fast');
  jQuery("." + popup_overlay).fadeIn('fast');

  if(id == "open_dashboard_report_permissions"){
    permissions_array = 'gawd_backend_roles[]';
    var permissions_for = 'gawd_backend_roles';
    var open_popup_btn = 'open_dashboard_report_permissions';
    var removed_roles = 'dashboard_report_permissions';
    var remove_role = 'remove_dashboard_report_permission';
    var role_value = 'dashboard_report_permission';
  }
  else if(id == "open_frontend_report_permissions"){
    permissions_array = 'gawd_frontend_roles[]';
    var permissions_for = 'frontend_report_permissions';
    var open_popup_btn = 'open_frontend_report_permissions';
    var removed_roles = 'frontend_report_roles';
    var remove_role = 'remove_frontend_report_role';
    var role_value = 'frontend_report_role';
  }
  else if(id == "post_page_report_permissions"){
    permissions_array = 'gawd_post_page_roles[]';
    var permissions_for = 'gawd_post_page_roles';
    var open_popup_btn = 'post_page_report_permissions';
    var removed_roles = 'gawd_post_page_roles';
    var remove_role = 'remove_post_page_role';
    var role_value = 'gawd_post_page_role';
  }
  else if(id == "open_gawd_permissions"){
    permissions_array = 'gawd_permissions[]';
    var permissions_for = 'gawd_post_page_roles';
    var open_popup_btn = 'open_gawd_permissions';
    var removed_roles = 'gawd_permissions';
    var remove_role = 'remove_gawd_permission';
    var role_value = 'gawd_permission';
  }
  var check = [];
  if(id == "open_gawd_permissions"){
    jQuery("." + role_value).each(function(){
      check.push(jQuery(this).html());
    })
  }
  else{
    jQuery("." + role_value).each(function(){
      check.push(jQuery(this).data('attribute'));
    });
  }
  html += '<table border="1" class="gawd_table" id="'+ id +'_table">';
  html += '<tr><th>Name</th><th>Action</th></tr>';
  for(key in roles.role_names){
    inp_id++;
    var disabled = key == "administrator" || key == 'Administrator' ? "disabled" : "";
    var checked = check.indexOf(key) > -1 || key == 'administrator'   ? 'checked' : '';
    var value = id == 'open_gawd_permissions' ? roles.role_names[key] : key
    var name =  id != 'open_gawd_permissions' ? roles.role_names[key] : key
    html += '<tr><td><label for="' + permissions_for + inp_id +'">' + name + '</label></td>';
    html += '<td><input id="' + permissions_for+inp_id + '"' + disabled +' class="gawd_perm" type="checkbox"' + checked + ' value="' + value + '"/></td></tr>';
  }
  html += '</table>';  
  jQuery('.gawd_permissions_popup_content').html(html);
  jQuery('.gawd_perm').on('click',function(){
    if(jQuery(this).attr('checked') == 'checked'){
      jQuery(this).closest('tr').prevAll().find(".gawd_perm").attr('checked',true);
    }
  })
  popup_overlay = "." + popup_overlay;
  popup_btn = "." + popup_btn;
  popup_body = "." + popup_body;
  open_popup_btn = "#" + open_popup_btn;
  add_role = "#" + add_role;
    jQuery(popup_overlay + ', ' + popup_btn).on('click', function () {
        jQuery(popup_body).fadeOut('fast');
        jQuery(popup_overlay).fadeOut('fast');
    });
    jQuery(add_role).unbind( "click" );
    jQuery(add_role).on('click', function (event) {
      event.preventDefault();
      jQuery(popup_body).fadeOut('fast');
      jQuery(popup_overlay).fadeOut('fast');
      var span =''; 
      var display_name = "";
      var value = [];
      jQuery( "#" + id + "_table input[type='checkbox']:checked").each(function () {
        value.push(jQuery(this).val());
          display_name = jQuery(this).closest('tr').find('label').html();
          jQuery("." + removed_roles).remove();
          span += '<div class="time_wrap ' + removed_roles + '"><span data-attribute="' + jQuery(this).val() + '" class="' + role_value + '">' + display_name + '</span>';
          if(display_name.toLowerCase() != 'administrator'){
            span += '<span class="' + remove_role + '">X</span>'
          }
         span += '</div>';
      });
      
      jQuery('#' + permissions_array.replace('[]','')).val(value.join());
      jQuery(open_popup_btn).before(span);
        jQuery("." + remove_role).on('click', function () {
            var find = jQuery(this).closest('.time_wrap').find("." + role_value).html();
            jQuery(this).closest('div').remove();

          jQuery(popup_body + " .gawd_table input[type='checkbox']:checked").each(function () {
              if(jQuery(this).val() == find){
                jQuery(this).removeAttr('checked');
              }
          });
          var value = [];
          jQuery("." + role_value).each(function () {
            value.push(jQuery(this).attr('data-attribute'));
          });
          jQuery('#' + permissions_array.replace('[]','')).val(value.join());
        })
    });

    jQuery("." + removed_roles).on('click', function () {
        var find = jQuery(this).closest('.time_wrap').find(role_value).html();
        jQuery(this).closest('div').remove();

      jQuery(popup_body + " .gawd_table input[type='checkbox']:checked").each(function () {
          if(jQuery(this).val() == find){
            jQuery(this).removeAttr('checked');
          }
      });
      var value = [];
      jQuery("." + role_value).each(function () {
        value.push(jQuery(this).attr('data-attribute'));
      });
      jQuery('#' + permissions_array.replace('[]','')).val(value.join());
    })
}
    jQuery(".gawd_remove").on('click', function () {
      var id = jQuery(this).closest('.time_wrap').attr('class').split(' ')[1];
      jQuery(this).closest('div').remove();
      var value = [];
      jQuery('.' + id.substring(0, id.length-1)).each(function () {
        value.push(jQuery(this).attr('data-attribute'));
      });
      if(id == 'dashboard_report_permissions'){
        id = 'gawd_backend_roles';
      }
      jQuery('#' + id).val(value.join()); 
    })
</script>
