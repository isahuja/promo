<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if( !class_exists( 'wip_woocarousel_lite_panel' ) ) {

	class wip_woocarousel_lite_panel {
	
		/**
		 * Constructor
		 */
		 
		public function __construct( $fields = array() ) {

			$this->panel_fields = $fields;
			$this->plugin_optionname = "wip_woocarousel_settings";

			add_action('admin_menu', array(&$this, 'admin_menu'),11);
			add_action('admin_init', array(&$this, 'add_script'),11);
			add_action('admin_init', array(&$this, 'save_option'),11);

		}

		/**
		 * Create option panel menu
		 */
		 
		public function admin_menu() {

			global $admin_page_hooks;
			
            if ( !isset( $admin_page_hooks['tip_plugins_panel'] ) ) :

				add_menu_page(
					__('TIP Plugins', 'wip-woocarousel-lite'),
					__('TIP Plugins', 'wip-woocarousel-lite'),
					'manage_options',
					'tip_plugins_panel',
					NULL,
					plugins_url('/assets/images/tip-icon.png', dirname(__FILE__)),
					64
				);

			endif;

			add_submenu_page(
				'tip_plugins_panel',
				__('WooCarousel Lite', 'wip-woocarousel-lite'),
				__('WooCarousel Lite', 'wip-woocarousel-lite'),
				'manage_options',
				'wip_woocarousel_lite_panel',
				array(&$this, 'wip_woocarousel_lite_panel')
			);

			if ( isset( $admin_page_hooks['tip_plugins_panel'] ) )
				remove_submenu_page( 'tip_plugins_panel', 'tip_plugins_panel' );

		}

		/**
		 * Loads the plugin scripts and styles
		 */
		 
		public function add_script() {
			
			 global $wp_version, $pagenow;
			 
			 $file_dir = plugins_url('/assets/', dirname(__FILE__));

			 wp_enqueue_style ( 'WIP_plugin_notice', $file_dir.'css/notice.css' ); 

			 if ( $pagenow == 'admin.php' || $pagenow == 'widgets.php' ) {

				wp_enqueue_style ( 'WIP_WooCarousel_lite_panel_googlefonts', '//fonts.googleapis.com/css?family=Roboto');
				wp_enqueue_style ( 'WIP_WooCarousel_lite_panel_on_off', $file_dir.'css/on_off.css' );
				wp_enqueue_style ( 'WIP_WooCarousel_lite_panel', $file_dir.'css/panel.css' ); 

				wp_enqueue_script( 'jquery');
				wp_enqueue_script( "jquery-ui-core", array('jquery'));
				wp_enqueue_script( "jquery-ui-tabs", array('jquery'));
				wp_enqueue_script( 'WIP_WooCarousel_lite_panel_on_off', $file_dir.'js/on_off.js','3.5', '', TRUE); 
				wp_enqueue_script( 'WIP_WooCarousel_lite_panel', $file_dir.'js/panel.js',array('jquery','thickbox'),'1.0',TRUE ); 
			 
			 }
			 
		}

		/**
		 * Message after the options saving
		 */
		 
		public function save_message () {
			
			global $message_action;
			
			if (isset($message_action)) :
				
				echo '<div id="message" class="updated fade message_save WIP_plugin_panel_message"><p><strong>'.$message_action.'</strong></p></div>';
			
			endif;
			
		}

		/**
		 * Save options function
		 */
		 
		public function save_option () {
			
			global $message_action;
			
			$wip_woocarousel_setting = get_option( $this->plugin_optionname );
			
			if ( $wip_woocarousel_setting != false ) :
					
				$wip_woocarousel_setting = maybe_unserialize( $wip_woocarousel_setting );
								
			else :
				
				$wip_woocarousel_setting = array();
			
			endif;      

			if (isset($_GET['action']) && ($_GET['action'] == 'wip_woocarousel_lite_backup_download')) {

				header("Cache-Control: public, must-revalidate");
				header("Pragma: hack");
				header("Content-Type: text/plain");
				header('Content-Disposition: attachment; filename="wip_woocarousel_lite_backup.dat"');
				echo serialize($this->get_options());
				exit;

			}
			
			if (isset($_GET['action']) && ($_GET['action'] == 'wip_woocarousel_lite_backup_reset')) {
				
				update_option( $this->plugin_optionname,'');
				wp_redirect(admin_url('admin.php?page=wip_woocarousel_lite_panel&tab=Import_Export'));
				exit;

			}
			
			if (isset($_POST['wip_woocarousel_lite_upload_backup']) && check_admin_referer('wip_woocarousel_lite_restore_options', 'wip_woocarousel_lite_restore_options')) {

				if ($_FILES["wip_woocarousel_lite_upload_file"]["error"] <= 0) {
					
					$options = unserialize(file_get_contents($_FILES["wip_woocarousel_lite_upload_file"]["tmp_name"]));
				
					if ($options) {
				
						foreach ($options as $option) {
							update_option( $this->plugin_optionname, unserialize($option->option_value));
				
						}
				
					}
				
				}

				wp_redirect(admin_url('admin.php?page=wip_woocarousel_lite_panel&tab=Import_Export'));
				exit;
		
			}

			if ( $this->wip_woocarousel_lite_request('wip_woocarousel_lite_action') == "Save" ) {
						
				foreach ( $this->panel_fields as $element ) {
					
					if ( isset($element['tab']) && $element['tab'] == $_GET['tab'] ) {
							
						foreach ($element as $value ) {
	
							if ( isset( $value['id']) ) {	
								
								if ( isset($_POST[$value["id"]]) ) :

									$current[$value["id"]] = $_POST[$value["id"]]; 

								else :

									$current[$value["id"]] = ""; 

								endif;
								
								update_option( $this->plugin_optionname, array_merge( $wip_woocarousel_setting  ,$current) );
	
							} 
	
							$message_action = __('Options saved successfully.', 'wip-woocarousel-lite' );
	
						}
		
					}
	
				}
	
			}
	
		}
		
		/**
		 * Get options
		 */
		 
		public function get_options() {
		
			global $wpdb;
			return $wpdb->get_results("SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name = '".$this->plugin_optionname."'");
		
		}
		
		/**
		 * Request function
		 */
		 
		public function wip_woocarousel_lite_request($id) {
			
			if ( isset ( $_REQUEST[$id]) ) :
				return $_REQUEST[$id];	
			endif;
			
		}
		
		/**
		 * Option panel
		 */
		 
		public function wip_woocarousel_lite_panel() {

			global $message_action;

			if ( !isset($_GET['tab']) )  { 
			
				$_GET['tab'] = "Settings"; 
			
			}
			
			foreach ( $this->panel_fields as $element) {
	
				if (isset($element['type'])) : 
	
					switch ( $element['type'] ) { 
	
						case 'navigation': ?>
                        
							<div id="WIP_WooCarousel_lite_banner">
					
								<h1> <?php _e( 'WIP WooCarousel Lite.', 'wip-woocarousel-lite'); ?> </h1>
								<p>  <?php _e( 'Upgrade to the premium version of WIP WooCarousel, to enable 5 different layouts, 600+ Google Fonts, unlimited colors and much more.', 'wip-woocarousel-lite'); ?></p>
								
								<div class="big-button"> 
									<a href="<?php echo esc_url( 'https://www.themeinprogress.com/wip-woocarousel-woocommerce-slider-carousel/?ref=2&campaign=wip-woocarousel-panel'); ?>" target="_blank"><?php _e( 'Upgrade to the premium version.', 'wip-woocarousel-lite'); ?></a>
								</div>
										
							</div>

							<div id="WIP_plugin_panel_tabs">
		
								<div id="WIP_plugin_panel_header">
									
									<div class="left"> <a href="<?php echo esc_url( 'https://www.themeinprogress.com'); ?>" target="_blank"><img src="<?php echo plugins_url('/assets/images/tip-logo.png', dirname(__FILE__) ); ?>" ></a> </div>
									<div class="plugin_description"> <h2 class="maintitle"> <?php echo __( 'WIP WooCarousel Lite','wip-woocarousel-lite'); ?> </h2> </div>
										
									<div class="clear"></div>
								
								</div>
					
								<?php $this->save_message(); ?>
		
								<ul>
					
									<?php 
									
										foreach ($element['item'] as $option => $name ) {
										
											if (str_replace(" ", "", $option) == $_GET['tab'] ) { 
											
												$class = "class='ui-state-active'";
											
											} else { 
											
												$class = "";
											
											}
											
											echo "<li ".$class."><a href='".esc_url( 'admin.php?page=wip_woocarousel_lite_panel&tab=' . str_replace(" ", "", $option))."'>".$name."</a></li>";
										
										}
									
									?>
	
									<li class="clear"></li>
								
								</ul>
							   
							<?php	
							
						break;
						
						case 'end-tab':  ?>
	
								<div class="clear"></div>
		
							</div>
								
						<?php break;
						
						case 'end-panel':  ?>
	
						<?php break;
						
					}
				
				endif;
			
			if (isset($element['tab'])) : 
			
				switch ( $element['tab'] ) { 
			
					case $_GET['tab']:  
			
						foreach ($element as $value) {
						
							if (isset($value['type'])) :
							
								switch ( $value['type'] ) { 
							
								case 'start-form':  ?>
									
									<div id="<?php echo str_replace(" ", "", $value['name']); ?>">
									
                                    	<form method="post" enctype="multipart/form-data" action="?page=wip_woocarousel_lite_panel&tab=<?php echo $_GET['tab']; ?>">
								
								<?php break;
								
								case 'end-form':  ?>
									
									
                                    	</form>
                                        
									</div>
								
								<?php break;
									
								case 'start-container':
					
									if ( ('Save' == $this->wip_woocarousel_lite_request('wip_woocarousel_lite_action'))  && ( $value['val'] == $this->wip_woocarousel_lite_request('element-opened')) ) { 
										$class=" inactive"; $style='style="display:block;"'; } else { $class="";  $style=''; 
									}  
						
									?>
			
									<div class="WIP_plugin_panel_container">
					
                                        <h5 class="element<?php echo $class; ?>" id="<?php echo $value['val']; ?>"><?php echo $value['name']; ?></h5>
                               
                                        <div class="WIP_plugin_panel_mainbox"> 
					
								<?php break;
						
								case 'start-open-container':  ?>
						
									<div class="WIP_plugin_panel_container">
					
                                        <h5 class="element-open"><?php echo $value['name']; ?></h5>
                               
                                        <div class="WIP_plugin_panel_mainbox wip_openbox"> 
					
								<?php break;
						
								case 'end-container':  ?>
						
										</div>            
					
									</div>
						
								<?php break;
						
								case 'navigation':
									
									echo $value['start'];
									foreach ($value['item'] as $option) { echo "<li><a href='#".str_replace(" ", "", $option)."'>".$option."</a></li>"; }
									echo $value['end']; 
									
								break;
					 
								case 'textarea':  ?>
						
									<div class="WIP_plugin_panel_box">
					
                                        <label for="bl_custom_style"> <?php echo $value['name']; ?> </label>
                                        <textarea name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" cols="" rows=""><?php if ( wip_woocarousel_lite_setting($value['id']) != "") { echo stripslashes(wip_woocarousel_lite_setting($value['id'])); } else { echo $value['std']; } ?></textarea>
                                        <p><?php echo $value['desc']; ?></p>
                        
									</div> 
						
								<?php break;
					
								case "save-button": ?>
					
									<div class="WIP_plugin_panel_box WIP_plugin_save_box">
                                        <input name="wip_woocarousel_lite_action" id="element-open" type="submit" value="<?php echo $value['value']; ?>" class="button"/>
									</div>
					
								<?php break;
					 
								case "color": ?>
									
									<div class="WIP_plugin_panel_box">
					
										<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
										<input type="text" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="<?php if ( wip_woocarousel_lite_setting($value['id']) != "") { echo wip_woocarousel_lite_setting($value['id']) ; } else { echo $value['std']; } ?>" data-default-color="<?php echo $value['std']; ?>" class="WIP_plugin_panel_color"  />
										<p><?php echo $value['desc']; ?></p>
					
									</div> 
					
								<?php break;
									
								case 'import_export': ?>
					
									<div class="WIP_plugin_panel_box">
						
									    <label for="<?php echo $value['id']; ?>"><?php _e( "Current plugin settings","wip-woocarousel-lite"); ?></label>
										<p><textarea class="widefat code" rows="20" cols="100" onclick="this.select()"><?php echo serialize($this->get_options()); ?></textarea></p>
										<a href="<?php echo esc_url( '?page=wip_woocarousel_lite_panel&tab=Import_Export&action=wip_woocarousel_lite_backup_download'); ?>" class="button button-secondary"><?php _e( "Download current plugin settings","wip-woocarousel-lite"); ?></a>
                                        <div class="clear"></div>
									   
									</div>
		
									<div class="WIP_plugin_panel_box">
						
									    <label for="<?php echo $value['id']; ?>"><?php _e( "Reset plugin settings","wip-woocarousel-lite"); ?></label>
									    <a href="<?php echo esc_url( '?page=wip_woocarousel_lite_panel&tab=Import_Export&action=wip_woocarousel_lite_backup_reset'); ?>" class="button-secondary"><?php _e( "Reset plugin settings","wip-woocarousel-lite"); ?></a>
									    <p><?php _e( "If you click the button above, the plugin options return to its default values","wip-woocarousel-lite"); ?></p>
									    <div class="clear"></div>
									   
									</div>
		
									<div class="WIP_plugin_panel_box">
						
									    <label for="<?php echo $value['id']; ?>"><?php _e( "Import plugin settings","wip-woocarousel-lite"); ?></label>
									    <input type="file" name="wip_woocarousel_lite_upload_file" /> 
									    <input type="submit" name="wip_woocarousel_lite_upload_backup" id="wip_woocarousel_lite_upload_backup" class="button-primary" value="<?php _e( "Import plugin settings","wip-woocarousel-lite"); ?>" />	
									    <?php if (function_exists('wp_nonce_field')) wp_nonce_field('wip_woocarousel_lite_restore_options', 'wip_woocarousel_lite_restore_options'); ?>

									</div>
		
								<?php break;

								}
							
							endif;
						
						}
					
					}	
				
				endif;	
		
			}

		}
	
	}

}

?>