 <?php

function gawd_write_menu($tabs, $title = true){
  $gawd_settings = get_option('gawd_settings');
  $sub_arrow = '<span class="gawd_menu_li_sub_arrow"></span>';
  foreach ($tabs as $tab_key => $tab_data){
    if(!$title){
      $tab_data["title"] = "";
      $sub_arrow = '';
    }
    if($tab_data["childs"] == array()){
      $active_tab = $_GET['tab'] == $tab_key ? 'gawd_active_li' : '';

      if(isset($gawd_settings['show_report_page']) && $gawd_settings['show_report_page'] == 'off'){
        if($tab_key == 'general' ||  $tab_key == 'realtime'){
          echo ' <li class=" gawd_menu_li  '.$active_tab.'" id="gawd_'.$tab_key.'" >
                <a class="gawd_menu_item " href="'.admin_url().'admin.php?page=gawd_reports&tab='.$tab_key.'">'.$tab_data["title"].'</a>
                <span class="gawd_description"  data-hint="'.$tab_data["desc"].'"></span>
             </li>';
        }
        elseif($tab_key == 'Pro'){
          echo ' <li class="gawd_inactive_pro gawd_menu_li  '.$active_tab.' " id="gawd_'.$tab_key.'">
              <span  class="gawd_menu_item gawd_pro_menu" >'.$tab_data["title"].'</span>
           </li>';
        }
        else{
          echo ' <li class="gawd_inactive gawd_menu_li  '.$active_tab.' " id="gawd_'.$tab_key.'">
              <span class=" gawd_menu_item gawd_pro_menu" >'.$tab_data["title"].'
              </span><span class="gawd_description"  data-hint="'.$tab_data["desc"].'"></span>
           </li>';
        }
      }
      else{
        if($tab_key == 'customReport' || $tab_key == 'custom' || $tab_key == 'adsense' || $tab_key == 'adWords'){
          echo ' <li class="gawd_menu_li  '.$active_tab.'" id="gawd_'.$tab_key.'">
                <span class="gawd_menu_item gawd_pro_menu" >'.$tab_data["title"].'
                  <span class="gawd_pro_flag">Paid</span>
                </span>
                <span class="gawd_description"  data-hint="'.$tab_data["desc"].'"></span>
             </li>';
        }
        elseif($tab_key == 'Pro'){
          continue;
        }
        else{
          echo ' <li class="gawd_menu_li  '.$active_tab.'" id="gawd_'.$tab_key.'" >
                <a class="gawd_menu_item " href="'.admin_url().'admin.php?page=gawd_reports&tab='.$tab_key.'">'.$tab_data["title"].'</a>
                <span class="gawd_description"  data-hint="'.$tab_data["desc"].'"></span>
             </li>';
        }
      }
    }
    else{
      if(isset($gawd_settings['show_report_page']) && $gawd_settings['show_report_page'] == 'off'){
        echo ' <li class="gawd_inactive gawd_menu_li " id="gawd_'.$tab_key.'_li">
                  <span id="gawd_'.$tab_key.'s" class="gawd_menu_li_sub">'.$tab_data["title"].$sub_arrow.'
                  </span>
                  <span class="gawd_description"  data-hint="'.$tab_data["desc"].'"></span>
                  <ul id="gawd_'.$tab_key.'_ul">';
      }
      else{
        if($tab_key == 'customReport' || $tab_key == 'ecommerce'){
          echo ' <li class="gawd_menu_li " id="gawd_'.$tab_key.'_li">
                  <span id="gawd_'.$tab_key.'s" class="gawd_pro_menu gawd_menu_li_sub">'.$tab_data["title"].$sub_arrow.'
                    <span class="gawd_pro_flag">Paid</span>
                  </span>
                  <span class="gawd_description"  data-hint="'.$tab_data["desc"].'"></span>
                  <ul id="gawd_'.$tab_key.'_ul">';
        }
        else{
          echo ' <li class="gawd_menu_li " id="gawd_'.$tab_key.'_li" >
                <span id="gawd_'.$tab_key.'" class="gawd_menu_li_sub">'.$tab_data["title"].$sub_arrow.'
            </span>
            <span class="gawd_description"  data-hint="'.$tab_data["desc"].'"></span>
            <ul id="gawd_'.$tab_key.'_ul">';
        }
      }
      
      foreach($tab_data["childs"] as $child_key => $child_title) {
        if(!$title){
          $child_title = "";
        }
        $active_tab = $_GET['tab'] == $child_key ? 'gawd_active_li' : '';
        if(isset($gawd_settings['show_report_page']) && $gawd_settings['show_report_page'] == 'off'){
          echo '  <li class=" gawd_menu_ul_li '.$active_tab.'">
                     <span class="gawd_menu_item " >'.$child_title.'</span>
                 </li> ';
        }
        else{
          if($child_key == 'productCategory' || $child_key == 'productName' || $child_key == 'productSku' || $child_key == 'transactionId' || $child_key == 'sales_performance' || $child_key == 'daysToTransaction'){
            echo '  <li class="gawd_menu_ul_li '.$active_tab.'">
                     <span class="gawd_menu_item " >'.$child_title.'</span>
                 </li> ';
          }
          else{
            echo '  <li class="gawd_menu_ul_li '.$active_tab.'">
                     <a class="gawd_menu_item " href="'.admin_url().'admin.php?page=gawd_reports&tab='.$child_key.'">'.$child_title.'</a>
                 </li> ';
          }
        }
      }      
       echo   '</ul>
                </li>';
    }
  }
}
function gawd_write_menu_collapse($tabs, $title = true){
    $gawd_settings = get_option('gawd_settings');

  $sub_arrow = '<span class="gawd_menu_li_sub_arrow"></span>';
  foreach ($tabs as $tab_key => $tab_data){
    if(!$title){
      $tab_data["title"] = "";
      $sub_arrow = '';
    }
    if($tab_data["childs"] == array()){
      $active_tab = $_GET['tab'] == $tab_key ? 'gawd_active_li' : '';
      if(isset($gawd_settings['show_report_page']) && $gawd_settings['show_report_page'] == 'off'){
        if($tab_key == 'general' ||  $tab_key == 'realtime'){
          echo '<a id="gawd_'.$tab_key.'" class="'.$active_tab.' gawd_menu_item " href="'.admin_url().'admin.php?page=gawd_reports&tab='.$tab_key.'">'.$tab_data["title"].'</a>';
        }
        else{
          echo '<span id="gawd_'.$tab_key.'" class="'.$active_tab.' gawd_menu_item " >'.$tab_data["title"].'</span>';
        }
      }
      else{
        if($tab_key == 'customReport' || $tab_key == 'custom' || $tab_key == 'adsense' || $tab_key == 'adWords'){
          echo '<span id="gawd_'.$tab_key.'" class="'.$active_tab.' gawd_menu_item " >'.$tab_data["title"].'</span>';
        }
        else{
          echo '<a id="gawd_'.$tab_key.'" class="'.$active_tab.' gawd_menu_item " href="'.admin_url().'admin.php?page=gawd_reports&tab='.$tab_key.'">'.$tab_data["title"].'</a>';
        }
      }
      
    }
    else{
      if($tab_key == 'customReport' || $tab_key == 'ecommerce'){
        echo '<span id="gawd_'.$tab_key.'_li" id="gawd_'.$tab_key.'s" class="gawd_menu_li_sub">'.$tab_data["title"].$sub_arrow.'
             <div class="collapse_ul" id="gawd_'.$tab_key.'_ul">';
      }
      else{
        echo '<span id="gawd_'.$tab_key.'_li" id="gawd_'.$tab_key.'" class="gawd_menu_li_sub">'.$tab_data["title"].$sub_arrow.'
          <div class="collapse_ul" id="gawd_'.$tab_key.'_ul">';
      }
      foreach($tab_data["childs"] as $child_key => $child_title) {
        $active_tab = $_GET['tab'] == $child_key ? 'gawd_active_li_text' : '';
        if(isset($gawd_settings['show_report_page']) && $gawd_settings['show_report_page'] == 'off'){
          if($child_key == 'productCategory' || $child_key == 'productName' || $child_key == 'productSku' || $child_key == 'transactionId' || $child_key == 'sales_performance' || $child_key == 'daysToTransaction'){
            echo '<span class="'.$active_tab.' gawd_menu_item " >'.$child_title.'</span>';
          }
          else{
            echo '<a class="'.$active_tab.' gawd_menu_item " href="'.admin_url().'admin.php?page=gawd_reports&tab='.$child_key.'">'.$child_title.'</a>';
          }
        }
        else{
          echo '<span class="'.$active_tab.' gawd_menu_item " >'.$child_title.'</span>';
        }
        
      }      
      echo   '</div></span>';
    }
  }
  
}
 ?>
 <div class="resp_menu"><div class="menu_img"></div><div class="button_label">REPORTS</div><div class="clear"></div></div>

  <div class="gawd_menu_coteiner_collapse">  
    <div class="gawd_menu_ul">
    <?php 
      gawd_write_menu_collapse($tabs,false);
    ?>
      <span class='gawd_collapsed'></span>
    </div>
  </div>
    <div class="gawd_menu_coteiner">  
   <input style="width: 100%; margin-bottom: 5px;" onkeyup="gawd_search()" type="text" class='gawd_search_input'/>
    <ul class="gawd_menu_ul" >
    <?php 
      
      gawd_write_menu($tabs);
    ?>
      <li class='gawd_collapse'>Collapse menu</li>
    </ul>
  </div>