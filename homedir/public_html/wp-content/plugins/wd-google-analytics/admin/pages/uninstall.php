	<?php

class GAWDUninstall{

	public function uninstall(){
    ?>
		<form method="post" action="" id="adminForm">
			<div class="gawd">
				<h2>
        <img src="<?php echo GAWD_URL . '/assets/uninstall-icon.png';?>" width="30" style="vertical-align:middle;">
                    <span><?php _e("Uninstall WD Google Analytics","gawd"); ?></span>
                </h2>
        <div class="goodbye-text">
          <?php
          $support_team = '<a href="https://web-dorado.com/support/contact-us.html?source=google-analytics-wd" target="_blank">' . __('support team', 'gawd') . '</a>';
          $contact_us = '<a href="https://web-dorado.com/support/contact-us.html?source=google-analytics-wd" target="_blank">' . __('Contact us', 'gawd') . '</a>';
          echo sprintf(__("Before uninstalling the plugin, please Contact our %s. We'll do our best to help you out with your issue. We value each and every user and value what's right for our users in everything we do.<br />
          However, if anyway you have made a decision to uninstall the plugin, please take a minute to %s and tell what you didn't like for our plugins further improvement and development. Thank you !!!", "gawd"), $support_team, $contact_us); ?>
        </div>
				<p style="color: red;">
				  <strong><?php _e("WARNING:","gawd"); ?></strong>
				  <?php _e("Once uninstalled, this can't be undone. You should use a Database Backup plugin of WordPress to back up all the data first.","gawd"); ?>
				</p>
				<p style="color: red">
					<strong><?php _e("The following Database options will be deleted:","gawd"); ?></strong>
				</p>
				<table class="widefat">
					<thead>
						<tr>
							<th><?php _e("Database options","gawd"); ?></th>
						</tr>
					</thead>
					<tr>
						<td valign="top">
							<ol>
							  <li>gawd_custom_reports</li>
							  <li>gawd_menu_for_user</li>
							  <li>gawd_all_metrics</li>
							  <li>gawd_all_dimensions</li>
							  <li>gawd_custom_dimensions</li>
							  <li>gawd_settings</li>
							  <li>gawd_user_data</li>
							  <li>gawd_credentials</li>
							  <li>gawd_menu_items</li>
							  <li>gawd_export_chart_data</li>
							  <li>gawd_email</li>
							  <li>gawd_custom_reports</li>
							  <li>gawd_alerts</li>
							  <li>gawd_pushovers</li>
							  <li>gawd_menu_for_users</li>
								<li>gawd_own_project</li>
								<li>gawd_zoom_message</li>
							</ol>
						</td>
					</tr>
	
				</table>
				<p style="text-align: center;">	<?php _e("Do you really want to uninstall WD Google Analytics?","gawd"); ?></p>
				<p style="text-align: center;">
				<input type="button" id="gawd_uninstall" value="<?php _e("UNINSTALL","gawd"); ?>" class="gawd_deactivate_link wd-btn wd-btn-primary" data-uninstall="1" />
				</p>				
			</div>	
      <?php wp_nonce_field('gawd_save_form', 'gawd_save_form_fild'); ?>
		</form>
<?php
  }
    
}