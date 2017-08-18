<?php

require_once(GAWD_DIR . '/admin/gawd_google_class.php');

class GAWD
{
    /**
     * @var GAWD The reference to Singleton instance of this class
     */
    private static $instance;
    private $project_client_id = null;
    private $project_client_secret = null;
    public $redirect_uri = "urn:ietf:wg:oauth:2.0:oob";

    /**
     * Protected constructor to prevent creating a new instance of the
     * Singleton via the `new` operator from outside of this class.
     */
    protected function __construct()
    {
        if (isset($_POST["reset_data"]) && $_POST["reset_data"] != '') {
            $this->reset_user_data();
        }
        if(!extension_loaded('openssl')){
            add_action( 'admin_notices', array( $this, 'nossl_message' ), 1 );
            return;
        }


        add_action('init', array($this, 'gawd_check_id'), 1);


        $gawd_settings = get_option('gawd_settings');
        $gawd_user_data = get_option('gawd_user_data');
        $gawd_post_page_roles = isset($gawd_settings['gawd_post_page_roles']) ? $gawd_settings['gawd_post_page_roles'] : array();
        $roles = $this->get_current_user_role();
        if ((isset($gawd_settings['gawd_tracking_enable']) && $gawd_settings['gawd_tracking_enable'] == 'on') && (isset($gawd_settings['post_page_chart']) && $gawd_settings['post_page_chart'] != '') && (in_array($roles, $gawd_post_page_roles) || current_user_can('manage_options')) && ( isset( $gawd_user_data['refresh_token'] ) && ( $gawd_user_data['refresh_token'] != '' ) ) && isset($gawd_user_data['default_webPropertyId'])) {
            add_filter('manage_posts_columns', array($this, 'gawd_add_columns'));
            // Populate custom column in Posts List
            add_action('manage_posts_custom_column', array($this, 'gawd_add_icons'), 10, 2);
            // Add custom column in Pages List
            add_filter('manage_pages_columns', array($this, 'gawd_add_columns'));
            // Populate custom column in Pages List
            add_action('manage_pages_custom_column', array($this, 'gawd_add_icons'), 10, 2);
            add_action('load-post.php', array($this, 'gawd_add_custom_box'));
        }
        $gawd_frontend_roles = isset($gawd_settings['gawd_frontend_roles']) ? $gawd_settings['gawd_frontend_roles'] : array();
        if ((isset($gawd_settings['gawd_tracking_enable']) && $gawd_settings['gawd_tracking_enable'] == 'on') && (in_array($roles, $gawd_frontend_roles) || current_user_can('manage_options')) && ( isset( $gawd_user_data['refresh_token'] ) && ( $gawd_user_data['refresh_token'] != '' ) ) && isset($gawd_user_data['default_webPropertyId'])) {
            add_action('wp_enqueue_scripts', array($this, 'gawd_front_scripts'));
            add_action('admin_bar_menu', array($this, 'report_adminbar'), 999);
        }

        $this->update_credentials();
        $credentials = get_option('gawd_credentials');
        if (is_array($credentials)) {
            $this->set_project_client_id($credentials['project_id']);
            $this->set_project_client_secret($credentials['project_secret']);
        } else {
            //send error
            return;
        }
    }

    function get_current_user_role()
    {
        global $wp_roles;
        if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            $roles = $current_user->roles;
            $role = array_shift($roles);
            return $role;
        } else {
            return "";
        }
    }

    function report_adminbar($wp_admin_bar)
    {
        /* @formatter:off */
        $gawd_settings = get_option('gawd_settings');
        $gawd_frontend_roles = isset($gawd_settings['gawd_frontend_roles']) ? $gawd_settings['gawd_frontend_roles'] : array();
        $roles = $this->get_current_user_role();
        $id = intval(get_the_ID());
        if (((in_array($roles, $gawd_frontend_roles) || current_user_can('manage_options')) && !is_admin()) && $gawd_settings['post_page_chart'] != '' && $id > 0) {
            $uri_parts = get_post($id);
            $uri    = '/' . $uri_parts->post_name;
                $filter = rawurlencode(rawurldecode($uri));
                $args = array(
                    'id' => 'gawd',
                    'title' => '<span data-url="' . $filter . '" class="ab-icon"></span><span class="">' . __("Analytics WD", 'gawd') . '</span>',
                    'href' => '#1',
                );
                /* @formatter:on */
                $wp_admin_bar->add_node($args);
        }
    }

    public function update_credentials()
    {
        //check_admin_referer('gawd_save_form', 'gawd_save_form_fild');
        if ($_POST) {
            $gawd_own_project = isset($_POST['gawd_own_project']) ? $_POST['gawd_own_project'] : '';
            $gawd_own_client_id = isset($_POST['gawd_own_client_id']) ? $_POST['gawd_own_client_id'] : '';
            $gawd_own_client_secret = isset($_POST['gawd_own_client_secret']) ? $_POST['gawd_own_client_secret'] : '';
            $gawd_credentials['project_id'] = $gawd_own_client_id;
            $gawd_credentials['project_secret'] = $gawd_own_client_secret;
            if ($gawd_own_project && $gawd_own_client_id != '' && $gawd_own_client_secret != '') {
                update_option('gawd_credentials', $gawd_credentials);
                delete_option('gawd_user_data');
                add_option('gawd_own_project', 1);
            }
        }
    }

    public function set_project_client_id($id)
    {
        $this->project_client_id = $id;
    }

    public function get_project_client_id()
    {
        return $this->project_client_id;
    }

    public function set_project_client_secret($secret)
    {
        $this->project_client_secret = $secret;
    }

    public function get_project_client_secret()
    {
        return $this->project_client_secret;
    }

    function gawd_check_id()
    {
        global $gawd_redirect_to_settings;
        $current_page = isset($_GET['page']) ? $_GET['page'] : "";
        if (strpos($current_page, 'gawd') !== false) {
          $gawd_user_data = get_option('gawd_user_data');
          require_once(GAWD_DIR . '/admin/gawd_google_class.php');
          $gawd_client        = GAWD_google_client::get_instance();

            if(GAWD_google_client::has_error()){
                add_action( 'admin_notices', array( 'GAWD_google_client', 'get_error_notice' ), 1 );
                return;
            }


          $accounts = $gawd_client->get_management_accounts();
          if (isset($gawd_user_data['refresh_token']) == false || ($gawd_user_data['refresh_token'] == '')) {
            update_option('gawd_redirect_to_settings', 'yes');
          } else {
            if (!empty($accounts)) {
              update_option('gawd_redirect_to_settings', 'no');
            } else {
              //delete_option("gawd_user_data");
              update_option('gawd_redirect_to_settings', 'yes');
            }
          }
        }
        $gawd_redirect_to_settings = get_option('gawd_redirect_to_settings');

        /*add_action('admin_notices', array($this, 'upgrade_pro'))*/;
        add_action('admin_menu', array($this, 'gawd_add_menu'), 9);
        add_action('admin_enqueue_scripts', array($this, 'gawd_enqueue_scripts'));
        add_action('wp_ajax_gawd_auth', array($this, 'gawd_auth'));
        add_action('wp_ajax_create_pdf_file', array($this, 'create_pdf_file'));
        add_action('wp_ajax_create_csv_file', array($this, 'create_csv_file'));
        add_action('wp_ajax_show_data', array($this, 'show_data'));
        add_action('wp_ajax_remove_zoom_message', array($this, 'remove_zoom_message'));
        add_action('wp_ajax_show_page_post_data', array($this, 'show_page_post_data'));
        add_action('wp_ajax_show_data_compact', array($this, 'show_data_compact'));
        add_action('wp_ajax_get_realtime', array($this, 'get_realtime'));
        add_action('wp_dashboard_setup', array($this, 'google_analytics_wd_dashboard_widget'));
        add_action('admin_menu', array($this, 'overview_date_meta'));
        add_filter('cron_schedules', array($this, 'gawd_my_schedule'));
        add_action('admin_init', array($this, 'gawd_export'));
        add_action('gawd_pushover_daily', array($this, 'gawd_pushover_daily'));
        add_action('gawd_pushover_gawd_weekly', array($this, 'gawd_pushover_weekly'));
        add_action('gawd_pushover_gawd_monthly', array($this, 'gawd_pushover_monthly'));
        add_action('gawd_alert_daily', array($this, 'gawd_alert_daily'));
        add_action('gawd_alert_gawd_monthly', array($this, 'gawd_alert_monthly'));
        add_action('gawd_alert_gawd_weekly', array($this, 'gawd_alert_weekly'));
        add_action('gawd_email_daily', array($this, 'gawd_daily_email'), 0);
        add_action('gawd_email_gawd_weekly', array($this, 'gawd_weekly_email'));
        add_action('gawd_email_gawd_monthly', array($this, 'gawd_monthly_email'));
        //add_action('init', array($this, 'gawd_daily_email'));
        add_action('wp_head', array($this, 'gawd_tracking_code'), 99);
        add_action('admin_notices', array($this, 'check_property_delete'), 9999);
    }

    function gawd_add_custom_box()
    {
        $screens = array('post', 'page');
        foreach ($screens as $screen) {
            add_meta_box('gawd_page_post_meta', 'Sessions in month', array(
                $this,
                'gawd_add_custom_box_callback'
            ), $screen, 'normal');
        }
    }

    function gawd_add_custom_box_callback()
    {
        require_once('admin/post_page_view.php');
    }

    public function gawd_add_icons($column, $id)
    {
        if ($column != 'gawd_stats') {
            return;
        }
        $uri_parts = get_post($id);
        $uri = '/' . $uri_parts->post_name;
        $filter = rawurlencode(rawurldecode($uri));
        echo '<a id="gawd-' . $id . '" class="gawd_page_post_stats" title="' . get_the_title($id) . '" href="#' . $filter . '"><img  src="' . GAWD_URL . '/assets/back_logo.png"</a>';
    }

    public function gawd_add_columns($columns)
    {
        return array_merge($columns, array('gawd_stats' => __('Analytics WD', 'gawd')));
    }

    public static function gawd_roles($access_level, $tracking = false)
    {
        if (is_user_logged_in() && isset($access_level)) {
            $current_user = wp_get_current_user();
            $roles = (array)$current_user->roles;
            if ((current_user_can('manage_options')) && !$tracking) {
                return true;
            }
            if (count(array_intersect($roles, $access_level)) > 0) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    public function gawd_tracking_code()
    {
        $gawd_user_data = get_option('gawd_user_data');
        if (isset($gawd_user_data['default_webPropertyId']) && ($gawd_user_data['default_webPropertyId'])) {
            global $gawd_client;
            $gawd_client = GAWD_google_client::get_instance();
            require_once(GAWD_DIR . '/admin/tracking.php');
        }
    }

    public function create_pdf_file($ajax = true, $data = null, $dimension = null, $start_date = null, $end_date = null, $metric_compare_recc = null, $metric_recc = null)
    {
        $first_data = isset($_REQUEST["first_data"]) ? sanitize_text_field($_REQUEST["first_data"]) : '';
        $_data_compare = isset($_REQUEST["_data_compare"]) ? ($_REQUEST["_data_compare"]) : '';
        if ($ajax == true) {
            $export_type = isset($_REQUEST["export_type"]) ? sanitize_text_field($_REQUEST["export_type"]) : '';
            if ($export_type != 'pdf') {
                return;
            }
            $report_type = isset($_REQUEST["report_type"]) ? sanitize_text_field($_REQUEST["report_type"]) : '';
            if ($report_type !== 'alert') {
                return;
            }
        }
        include_once GAWD_DIR . '/include/gawd_pdf_file.php';
        $file = new GAWD_PDF_FILE();
        /*

                require_once(GAWD_DIR . '/admin/gawd_google_class.php');

                $this->gawd_google_client = GAWD_google_client::get_instance();

        */
        $file->get_request_data($this, $ajax, $data, $dimension, $start_date, $end_date, $metric_compare_recc, $metric_recc);
        $file->sort_data();
        if ($first_data != '') {
            $file->create_file('pages');
        } elseif (($_data_compare) != '') {
            $file->create_file('compare');
        } else {
            $file->create_file(true);
        }
        if ($ajax == true) {
            die();
        } else {
            return $file->file_dir;
        }
    }

    public function create_csv_file($ajax = true, $data = null, $dimension = null, $start_date = null, $end_date = null, $metric_compare_recc = null, $metric_recc = null)
    {
        if ($ajax == true) {
            $export_type = isset($_REQUEST["export_type"]) ? sanitize_text_field($_REQUEST["export_type"]) : '';
            if ($export_type != 'csv') {
                return;
            }
            $report_type = isset($_REQUEST["report_type"]) ? sanitize_text_field($_REQUEST["report_type"]) : '';
            if ($report_type !== 'alert') {
                return;
            }
        }
        require_once(GAWD_DIR . '/admin/gawd_google_class.php');
        $this->gawd_google_client = GAWD_google_client::get_instance();
        $first_data = isset($_REQUEST["first_data"]) ? sanitize_text_field($_REQUEST["first_data"]) : '';
        include_once GAWD_DIR . '/include/gawd_csv_file.php';
        $file = new GAWD_CSV_FILE();
        $file->get_request_data($this, $ajax, $data, $dimension, $start_date, $end_date, $metric_compare_recc, $metric_recc);
        $file->sort_data();
        //$file->get_request_data($this);
        $file->sort_data();
        if ($first_data != '') {
            $file->create_file(false);
        } else {
            $file->create_file();
        }
        if ($ajax == true) {
            die();
        } else {
            return $file->file_dir;
        }
    }

    public static function get_domain($domain)
    {
        $root = explode('/', $domain);
        $ret_domain = str_ireplace('www', '', isset($root[2]) ? $root[2] : $domain);
        return $ret_domain;
    }

    public static function error_message($type, $message)
    {
        echo '<div style="width:99%"><div class="' . $type . '"><p><strong>' . $message . '</strong></p></div></div>';
    }

    public function gawd_export()
    {
        if (!isset($_REQUEST['action']) || (isset($_REQUEST['action']) && $_REQUEST['action'] !== 'gawd_export')) {
            return;
        }
        $export_type = isset($_REQUEST["export_type"]) ? sanitize_text_field($_REQUEST["export_type"]) : '';
        if ($export_type != 'pdf' && $export_type != 'csv') {
            return;
        }
        $report_type = isset($_REQUEST["report_type"]) ? sanitize_text_field($_REQUEST["report_type"]) : '';
        require_once(GAWD_DIR . '/admin/gawd_google_class.php');
        $this->gawd_google_client = GAWD_google_client::get_instance();
        if ($export_type == 'pdf') {
            include_once GAWD_DIR . '/include/gawd_pdf_file.php';
            $file = new GAWD_PDF_FILE();
        } else {
            include_once GAWD_DIR . '/include/gawd_csv_file.php';
            $file = new GAWD_CSV_FILE();
        }
        if ($report_type == 'alert') {
            if ($export_type == 'pdf') {
                $file->export_file();
            } else {
                $file->export_file();
            }
        } else {
            $metric = isset($_REQUEST["gawd_metric"]) ? sanitize_text_field($_REQUEST["gawd_metric"]) : '';
            $_data_compare = isset($_REQUEST["_data_compare"]) ? ($_REQUEST["_data_compare"]) : '';
            $first_data = isset($_REQUEST["first_data"]) ? ($_REQUEST["first_data"]) : '';
            $view_id = isset($_REQUEST["view_id"]) ? sanitize_text_field($_REQUEST["view_id"]) : '';
            $metric_compare = isset($_REQUEST["gawd_metric_compare"]) ? sanitize_text_field($_REQUEST["gawd_metric_compare"]) : '';
            $dimension = isset($_REQUEST["gawd_dimension"]) ? sanitize_text_field($_REQUEST["gawd_dimension"]) : '';
            $tab_name = isset($_REQUEST["tab_name"]) ? sanitize_text_field($_REQUEST["tab_name"]) : '';
            $img = isset($_REQUEST["img"]) ? sanitize_text_field($_REQUEST["img"]) : '';
            $gawd_email_subject = isset($_REQUEST["gawd_email_subject"]) ? sanitize_text_field($_REQUEST["gawd_email_subject"]) : '';
            $gawd_email_body = isset($_REQUEST["gawd_email_body"]) && $_REQUEST["gawd_email_body"] != '' ? sanitize_text_field($_REQUEST["gawd_email_body"]) : ' ';
            $email_from = isset($_REQUEST["gawd_email_from"]) ? sanitize_email($_REQUEST["gawd_email_from"]) : '';
            $email_to = isset($_REQUEST["gawd_email_to"]) ? sanitize_email($_REQUEST["gawd_email_to"]) : '';
            $email_period = isset($_REQUEST["gawd_email_period"]) ? sanitize_text_field($_REQUEST["gawd_email_period"]) : '';
            $week_day = isset($_REQUEST["gawd_email_week_day"]) ? sanitize_text_field($_REQUEST["gawd_email_week_day"]) : '';
            $month_day = isset($_REQUEST["gawd_email_month_day"]) ? sanitize_text_field($_REQUEST["gawd_email_month_day"]) : '';
            $email_time = isset($_REQUEST["email_time"]) ? sanitize_text_field($_REQUEST["email_time"]) : '';
            $emails = array();
            $invalid_email = false;
            $email_to = explode(',', $email_to);
            foreach ($email_to as $email) {
                if (is_email($email) == false) {
                    $emails = $email;
                }
            }
            if (count($emails) > 0) {
                $invalid_email = true;
            }
            if (($invalid_email != true) && is_email($email_from) && $gawd_email_subject != '') {
                if ($email_period == "once") {
                    $file->get_request_data($this);
                    $file->sort_data();
                    if ($export_type == 'csv') {
                        if ($first_data != '') {
                            $file->create_file(false);
                        } else {
                            $file->create_file();
                        }
                    } else {
                        if ($first_data != '') {
                            $file->create_file('pages');
                        } elseif (($_data_compare) != '') {
                            $file->create_file('compare');
                        } else {
                            $file->create_file(false);
                        }
                    }
                    $attachment = $file->file_dir;
                    if ($report_type == 'email') {
                        $headers = 'From: <' . $email_from . '>';
                        wp_mail($email_to, $gawd_email_subject, $gawd_email_body, $headers, $attachment);
                    }
                    echo json_encode(array('status' => 'success', 'msg' => 'Email successfuly sent'));
                } else {
                    if ($email_period == 'gawd_weekly') {
                        $period_day = $week_day;
                        $timestamp = strtotime('this ' . $period_day . ' ' . $email_time);
                    } elseif ($email_period == 'gawd_monthly') {
                        $period_day = $month_day;
                        $timestamp = strtotime(date('Y-m-' . $period_day . ' ' . $email_time));
                    } else {
                        $period_day = '';
                        $timestamp = strtotime(date('Y-m-d ' . $email_time));
                    }
                    $saved_email = get_option('gawd_email');
                    if ($saved_email) {
                        $gawd_email_options = array(
                            'name' => $gawd_email_subject,
                            'period' => $email_period,
                            'metric' => $metric,
                            'metric_compare' => $metric_compare,
                            'dimension' => $dimension,
                            'creation_date' => date('Y-m-d') . ' ' . $email_time,
                            'emails' => $email_to,
                            'email_from' => $email_from,
                            'email_subject' => $gawd_email_subject,
                            'email_body' => $gawd_email_body,
                            'period_day' => $period_day,
                            'period_time' => $email_time,
                            'img' => $img,
                            'tab_name' => $tab_name,
                            'view_id' => $view_id,
                            'export_type' => $export_type
                        );
                        $saved_email[] = $gawd_email_options;
                        update_option('gawd_email', $saved_email);
                    } else {
                        $gawd_email_options = array(
                            0 => array(
                                'name' => $gawd_email_subject,
                                'period' => $email_period,
                                'metric' => $metric,
                                'metric_compare' => $metric_compare,
                                'dimension' => $dimension,
                                'creation_date' => date('Y-m-d') . ' ' . $email_time,
                                'emails' => $email_to,
                                'email_from' => $email_from,
                                'email_subject' => $gawd_email_subject,
                                'email_body' => $gawd_email_body,
                                'period_day' => $period_day,
                                'period_time' => $email_time,
                                'img' => $img,
                                'tab_name' => $tab_name,
                                'view_id' => $view_id,
                                'export_type' => $export_type
                            )
                        );
                        update_option('gawd_email', $gawd_email_options);
                    }
                    $saved_email = get_option('gawd_email');
                    if ($saved_email) {
                        foreach ($saved_email as $email) {
                            if (!wp_next_scheduled('gawd_email_' . $email['period'])) {
                                wp_schedule_event($timestamp, $email['period'], 'gawd_email_' . $email['period']);
                            }
                        }
                    }
                    $success_message = 'Email successfuly Scheduled </br> Go to <a href="' . admin_url() . 'admin.php?page=gawd_settings#gawd_emails_tab">Settings page</a> to delete scheduled e-mails.';
                    echo json_encode(array('status' => 'success', 'msg' => $success_message));
                }
                die;
            } else {
                if ($invalid_email == true) {
                    echo json_encode('Invalid email');
                    die;
                } else if ($gawd_email_subject == '') {
                    echo json_encode("Can't send email with empty subject");
                    die;
                }
            }
        }
    }

    public function overview_date_meta($screen = null, $context = 'advanced')
    {
        //righ side wide meta..
        $orintation = wp_is_mobile() ? 'side' : 'normal';
        add_meta_box('gawd-real-time', __('Real Time', 'gawd'), array(
            $this,
            'gawd_real_time'
        ), 'gawd_analytics', 'side', 'high');
        add_meta_box('gawd-date-meta', __('Audience', 'gawd'), array(
            $this,
            'gawd_date_box'
        ), 'gawd_analytics', $orintation, null);
        add_meta_box('gawd-country-box', __('Location', 'gawd'), array(
            $this,
            'gawd_country_box'
        ), 'gawd_analytics', $orintation, null);
        //left side thin meta.
        add_meta_box('gawd-visitors-meta', __('Visitors', 'gawd'), array(
            $this,
            'gawd_visitors'
        ), 'gawd_analytics', 'side', null);
        add_meta_box('gawd-browser-meta', __('Browsers', 'gawd'), array(
            $this,
            'gawd_browser'
        ), 'gawd_analytics', 'side', null);
    }

    public function gawd_date_box()
    {
        require_once('admin/pages/date.php');
    }

    public function gawd_country_box()
    {
        require_once('admin/pages/location.php');
    }

    public function gawd_real_time()
    {
        require_once('admin/pages/real_time.php');
    }

    public function gawd_visitors()
    {
        require_once('admin/pages/visitors.php');
    }

    public function gawd_browser()
    {
        require_once('admin/pages/browser.php');
    }

    /**
     * Activation function needed for the activation hook.
     */
		public static function global_activate($networkwide)
    {
			if (function_exists('is_multisite') && is_multisite()) {
				// Check if it is a network activation - if so, run the activation function for each blog id.
				if ($networkwide) {
					global $wpdb;
					// Get all blog ids.
					$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
					foreach ($blogids as $blog_id) {
						switch_to_blog($blog_id);
						self::activate();
						restore_current_blog();
					}
					return;
				}
			}
			self::activate();
    }
		 
    public static function activate()
    {
        $credentials['project_id'] = '115052745574-5vbr7tci4hjkr9clkflmnpto5jisgstg.apps.googleusercontent.com';
        $credentials['project_secret'] = 'wtNiu3c_bA_g7res6chV0Trt';
        if (!get_option('gawd_credentials')) {
            update_option('gawd_credentials', $credentials);
        }
        $gawd_settings = get_option('gawd_settings');
        if ($gawd_settings === false) {
            self::gawd_settings_defaults();
        } elseif ($gawd_settings && !isset($gawd_settings['show_report_page'])) {
            $gawd_settings['show_report_page'] = 'on';
            update_option('gawd_settings', $gawd_settings);
        }
        self::add_dashboard_menu();
    }

    /**
     * Deactivation function needed for the deactivation hook.
     */
    public static function deactivate()
    {
    }

    /**
     * Enqueues the required styles and scripts, localizes some js variables.
     */
    public function gawd_front_scripts()
    {
        if (is_user_logged_in()) {
            wp_enqueue_style('admin_css', GAWD_URL . '/inc/css/gawd_admin.css', false, GAWD_VERSION);
            wp_enqueue_script('gawd_amcharts', GAWD_URL . '/inc/js/amcharts.js', false, GAWD_VERSION);
            wp_enqueue_script('gawd_pie', GAWD_URL . '/inc/js/pie.js', false, GAWD_VERSION);
            wp_enqueue_script('gawd_serial', GAWD_URL . '/inc/js/serial.js', false, GAWD_VERSION);
            wp_enqueue_script('gawd_light_theme', GAWD_URL . '/inc/js/light.js', array('jquery'), GAWD_VERSION);
            wp_enqueue_script('gawd_dataloader', GAWD_URL . '/inc/js/dataloader.min.js', array('jquery'), GAWD_VERSION);
            wp_enqueue_script('gawd_front_js', GAWD_URL . '/inc/js/gawd_front.js', array('jquery'), GAWD_VERSION);
            wp_localize_script('gawd_front_js', 'gawd_front', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'ajaxnonce' => wp_create_nonce('gawd_admin_page_nonce'),
                'gawd_plugin_url' => GAWD_URL,
                'date_30' => date('Y-m-d', strtotime('-31 day')) . '/-/' . date('Y-m-d', strtotime('-1 day')),
                'date_7' => date('Y-m-d', strtotime('-8 day')) . '/-/' . date('Y-m-d', strtotime('-1 day')),
                'date_last_week' => date('Y-m-d', strtotime('last week -1day')) . '/-/' . date('Y-m-d', strtotime('last week +5day')),
                'date_last_month' => date('Y-m-01', strtotime('last month')) . '/-/' . date('Y-m-t', strtotime('last month')),
                'date_this_month' => date('Y-m-01') . '/-/' . date('Y-m-d'),
                'date_today' => date('Y-m-d') . '/-/' . date('Y-m-d'),
                'date_yesterday' => date('Y-m-d', strtotime('-1 day')) . '/-/' . date('Y-m-d', strtotime('-1 day')),
                'wp_admin_url' => admin_url(),
                'exportUrl' => add_query_arg(array('action' => 'gawd_export'), admin_url('admin-ajax.php'))
            ));
        }
    }

    public function gawd_enqueue_scripts()
    {
        $options = get_option('gawd_settings');
        $show_report_page = (isset($options['show_report_page']) && $options['show_report_page'] != '') ? $options['show_report_page'] : 'on';
        $default_date = (isset($options['default_date']) && $options['default_date'] != '') ? $options['default_date'] : 'last_30days';
        $default_date_format = (isset($options['default_date_format']) && $options['default_date_format'] != '') ? $options['default_date_format'] : 'ymd_with_week';
        $enable_hover_tooltip = (isset($options['enable_hover_tooltip']) && $options['enable_hover_tooltip'] != '') ? $options['enable_hover_tooltip'] : '';
        $screen = get_current_screen();
        if (strpos($screen->base, 'gawd') !== false || strpos($screen->post_type, 'page') !== false || strpos($screen->post_type, 'post') !== false || strpos($screen->base, 'dashboard') !== false || strpos($screen->base, 'edit') !== false) {
            wp_enqueue_script('common');
            /* wp_enqueue_script('wp-lists'); */
            wp_enqueue_script('postbox');
            wp_enqueue_script('jquery-ui-tooltip');
            wp_enqueue_script('gawd_paging', GAWD_URL . '/inc/js/paging.js', false, GAWD_VERSION);
            wp_enqueue_script('jquery.cookie', GAWD_URL . '/inc/js/jquery.cookie.js', false, GAWD_VERSION);
            wp_enqueue_style('timepicker_css', GAWD_URL . '/inc/css/jquery.timepicker.css', false, GAWD_VERSION);
            wp_enqueue_style('admin_css', GAWD_URL . '/inc/css/gawd_admin.css', false, GAWD_VERSION);
            wp_enqueue_style('gawd_licensing', GAWD_URL . '/inc/css/gawd_licensing.css', false, GAWD_VERSION);
            wp_enqueue_style('font-awesome', GAWD_URL . '/inc/css/font_awesome.css', false, GAWD_VERSION);
            wp_enqueue_style('jquery-ui.css', GAWD_URL . '/inc/css/jquery-ui.css', false, GAWD_VERSION);
           	if(strpos( $screen->post_type, 'page' ) === false && strpos( $screen->post_type, 'post' ) === false && strpos( $screen->base, 'edit' ) === false ){
							wp_enqueue_style( 'gawd_bootstrap', GAWD_URL . '/inc/css/bootstrap.css', false, GAWD_VERSION );
							wp_enqueue_style( 'gawd_bootstrap-chosen', GAWD_URL . '/inc/css/bootstrap-chosen.css', false, GAWD_VERSION );
							wp_enqueue_style( 'gawd_bootstrap-select', GAWD_URL . '/inc/css/bootstrap-select.css', false, GAWD_VERSION );
						}
            wp_enqueue_style('gawd_datepicker', GAWD_URL . '/inc/css/daterangepicker.css', false, GAWD_VERSION);
            wp_enqueue_style('ui.jqgrid.css', GAWD_URL . '/inc/css/ui.jqgrid.css', false, GAWD_VERSION);
            wp_enqueue_script('gawd_moment', GAWD_URL . '/inc/js/moment.min.js', false, GAWD_VERSION);
            wp_enqueue_script('gawd_daterangepicker', GAWD_URL . '/inc/js/daterangepicker.js', false, GAWD_VERSION);
            wp_enqueue_script('gawd_amcharts', GAWD_URL . '/inc/js/amcharts.js', false, GAWD_VERSION);
            wp_enqueue_script('gawd_pie', GAWD_URL . '/inc/js/pie.js', false, GAWD_VERSION);
            wp_enqueue_script('gawd_serial', GAWD_URL . '/inc/js/serial.js', false, GAWD_VERSION);
            /*Map*/
            wp_enqueue_script('gawd_ammap', GAWD_URL . '/inc/js/ammap.js', false, GAWD_VERSION);
            wp_enqueue_script('gawd_worldLow', GAWD_URL . '/inc/js/worldLow.js', false, GAWD_VERSION);
            wp_enqueue_script('gawd_map_chart', GAWD_URL . '/inc/js/gawd_map_chart.js', false, GAWD_VERSION);
            /*End Map*/
            wp_enqueue_script('gawd_light_theme', GAWD_URL . '/inc/js/light.js', array('jquery'), GAWD_VERSION);
            wp_enqueue_script('gawd_dataloader', GAWD_URL . '/inc/js/dataloader.min.js', array('jquery'), GAWD_VERSION);
            wp_enqueue_script('rgbcolor.js', GAWD_URL . '/inc/js/rgbcolor.js', array('jquery'), GAWD_VERSION);
            wp_enqueue_script('StackBlur.js', GAWD_URL . '/inc/js/StackBlur.js', array('jquery'), GAWD_VERSION);
            wp_enqueue_script('canvg.js', GAWD_URL . '/inc/js/canvg.js', array('jquery'), GAWD_VERSION);
            wp_enqueue_script('gawd_tables', GAWD_URL . '/inc/js/loader.js', array('jquery'), GAWD_VERSION);
            wp_enqueue_script('gawd_grid', GAWD_URL . '/inc/js/jquery.jqGrid.min.js', array('jquery'), GAWD_VERSION);
            wp_enqueue_script('gawd_grid_locale', GAWD_URL . '/inc/js/grid.locale-en.js', array('jquery'), GAWD_VERSION);
            wp_enqueue_script('timepicker_js', GAWD_URL . '/inc/js/jquery.timepicker.min.js', array('jquery'), GAWD_VERSION);
            wp_enqueue_script('admin_js', GAWD_URL . '/inc/js/gawd_admin.js', array('jquery'), GAWD_VERSION);
            wp_enqueue_script('chosen.jquery.js', GAWD_URL . '/inc/js/chosen.jquery.js', array('jquery'), GAWD_VERSION);
						if(strpos( $screen->post_type, 'page' ) === false && strpos( $screen->post_type, 'post' ) === false && strpos( $screen->base, 'edit' ) === false ){
							wp_enqueue_script('bootstrap_js', GAWD_URL . '/inc/js/bootstrap_js.js', array('jquery'), GAWD_VERSION);
							wp_enqueue_script('bootstrap-select', GAWD_URL . '/inc/js/bootstrap-select.js', array('jquery'), GAWD_VERSION);
						}
            wp_enqueue_script('highlight_js', GAWD_URL . '/inc/js/js_highlight.js', array('jquery'), GAWD_VERSION);
            wp_enqueue_script('settings_js', GAWD_URL . '/inc/js/gawd_settings.js', array('jquery'), GAWD_VERSION);
            wp_enqueue_script('overview', GAWD_URL . '/inc/js/gawd_overview.js', array('jquery'), GAWD_VERSION);
            wp_localize_script('overview', 'gawd_overview', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'ajaxnonce' => wp_create_nonce('gawd_admin_page_nonce'),
                'gawd_plugin_url' => GAWD_URL,
                'default_date' => $default_date,
                'enableHoverTooltip' => $enable_hover_tooltip,
                'wp_admin_url' => admin_url()
            ));
            wp_localize_script('admin_js', 'gawd_admin', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'ajaxnonce' => wp_create_nonce('gawd_admin_page_nonce'),
                'gawd_plugin_url' => GAWD_URL,
                'wp_admin_url' => admin_url(),
                'enableHoverTooltip' => $enable_hover_tooltip,
                'default_date' => $default_date,
                'default_date_format' => $default_date_format,
                'date_30' => date('Y-m-d', strtotime('-31 day')) . '/-/' . date('Y-m-d', strtotime('-1 day')),
                'date_7' => date('Y-m-d', strtotime('-8 day')) . '/-/' . date('Y-m-d', strtotime('-1 day')),
                'date_last_week' => date('Y-m-d', strtotime('last week -1day')) . '/-/' . date('Y-m-d', strtotime('last week +5day')),
                'date_last_month' => date('Y-m-01', strtotime('last month')) . '/-/' . date('Y-m-t', strtotime('last month')),
                'date_this_month' => date('Y-m-01') . '/-/' . date('Y-m-d'),
                'date_today' => date('Y-m-d') . '/-/' . date('Y-m-d'),
                'date_yesterday' => date('Y-m-d', strtotime('-1 day')) . '/-/' . date('Y-m-d', strtotime('-1 day')),
                'exportUrl' => add_query_arg(array('action' => 'gawd_export'), admin_url('admin-ajax.php')),
                'show_report_page' => $show_report_page
            ));
        }
        if(strpos($screen->base, 'gawd_uninstall') !== false) {
            wp_enqueue_style('gawd_deactivate-css',  GAWD_URL . '/wd/assets/css/deactivate_popup.css', array(), GAWD_VERSION);
           wp_enqueue_script('gawd-deactivate-popup', GAWD_URL.'/wd/assets/js/deactivate_popup.js', array(), GAWD_VERSION, true );
           $admin_data = wp_get_current_user();
		   
            wp_localize_script( 'gawd-deactivate-popup', 'gawdWDDeactivateVars', array(
                    "prefix" => "gawd" ,
                    "deactivate_class" =>  'gawd_deactivate_link',
                    "email" => $admin_data->data->user_email,
                    "plugin_wd_url" => "https://web-dorado.com/products/wordpress-google-maps-plugin.html",
            ));            
        }	
    }

    /**
     * Adds the menu page with its submenus.
     */
    public function gawd_add_menu()
    {
        $gawd_settings = get_option('gawd_settings');
        $gawd_permissions = isset($gawd_settings['gawd_permissions']) ? $gawd_settings['gawd_permissions'] : array();
        if (empty($gawd_permissions)) {
            $permission = 'manage_options';
        } else {
            if (in_array('manage_options', $gawd_permissions)) {
                $permission = 'manage_options';
            }
            if (in_array('moderate_comments', $gawd_permissions)) {
                $permission = 'moderate_comments';
            }
            if (in_array('publish_posts', $gawd_permissions)) {
                $permission = 'publish_posts';
            }
            if (in_array('edit_posts', $gawd_permissions)) {
                $permission = 'edit_posts';
            }
            if (in_array('read', $gawd_permissions)) {
                $permission = 'read';
            }
        }
      
        $parent_slug = null;
        if( get_option( "gawd_subscribe_done" ) == 1 ){
          $parent_slug = "gawd_analytics";
          add_menu_page( 
            "Analytics",
            "Analytics",
            'manage_options',
            $this->gawd_set_slug('gawd_analytics'), //$menu_slug
            array($this, $this->gawd_set_display('gawd_display_overview_page')), //$function = '',
            GAWD_URL . '/assets/main_icon.png', '25,13' ); 
        }
        add_submenu_page(
            $parent_slug, //$parent_slug
            __('Analytics Dashboard', 'gawd'), //$page_title
            __('Analytics Dashboard', 'gawd'), //$menu_title
            $permission, //$capability
            $this->gawd_set_slug('gawd_analytics'), //$menu_slug
            array($this, $this->gawd_set_display('gawd_display_overview_page')) //$function = '',
        );
        add_submenu_page(
            $parent_slug, //$parent_slug
            __('Reports', 'gawd'), //$page_title
            __('Reports', 'gawd'), //$menu_title
            $permission, //$capability
            $this->gawd_set_slug('gawd_reports'), //$menu_slug
            array($this, $this->gawd_set_display('gawd_display_reports_page')) //$function = '',
        );
        add_submenu_page(
            $parent_slug, //$parent_slug
            __('Settings', 'gawd'), //$page_title
            __('Settings', 'gawd'), //$menu_title
            $permission, //$capability
            'gawd_settings', //$menu_slug
            array($this, 'gawd_display_settings_page')   //$function = '',
        );
        add_submenu_page(
            $parent_slug, //$parent_slug
            __('Tracking', 'gawd'), //$page_title
            __('Tracking', 'gawd'), //$menu_title
            $permission, //$capability
            $this->gawd_set_slug('gawd_tracking'), //$menu_slug
            array($this, $this->gawd_set_display('gawd_display_tracking_page')) //$function = '',
        );
        add_submenu_page(
            $parent_slug, //$parent_slug
            __('Goal Management', 'gawd'), //$page_title
            __('Goal Management', 'gawd'), //$menu_title
            $permission, //$capability
            $this->gawd_set_slug('gawd_goals'), //$menu_slug
            array($this, $this->gawd_set_display('gawd_display_goals_page')) //$function = '',
        );
        add_submenu_page(
            $parent_slug, //$parent_slug
            __('Custom Reports', 'gawd'), //$page_title
            __('Custom Reports', 'gawd'), //$menu_title
            $permission, //$capability
            $this->gawd_set_slug('gawd_custom_reports'), //$menu_slug
            array($this, $this->gawd_set_display('gawd_display_custom_reports_page')) //$function = '',
        );
        add_submenu_page(
            $parent_slug, //$parent_slug
            __('Get Pro', 'gawd'), //$page_title
            __('Get Pro', 'gawd'), //$menu_title
            $permission, //$capability
            'gawd_licensing', //$menu_slug
            array($this, 'gawd_display_licensing_page') //$function = '',
        );
        add_submenu_page(
            $parent_slug, //$parent_slug
            __('Uninstall', 'gawd'), //$page_title
            __('Uninstall', 'gawd'), //$menu_title
            $permission, //$capability
            'gawd_uninstall', //$menu_slug
            array($this, 'gawd_display_uninstall_page') //$function = '',
        );
    }

    public function gawd_set_slug($slug)
    {
        global $gawd_redirect_to_settings;
        if ($gawd_redirect_to_settings == 'yes') {
            return 'gawd_settings';
        } else {
            return $slug;
        }
    }

    public function gawd_set_display($slug)
    {
        global $gawd_redirect_to_settings;
        if ($gawd_redirect_to_settings == 'yes') {
            return 'gawd_display_settings_page';
        } else {
            return $slug;
        }
    }

    public function gawd_display_licensing_page()
    {
        require_once(GAWD_DIR . '/admin/licensing.php');
    }

    /* function upgrade_pro()
    {
        $screen = get_current_screen();
        if (strpos($screen->base, 'gawd') !== false) {
            ?>

            <div class="gawd_upgrade wd-clear">

               

            </div>

            <?php
        }
    } */

      
    public function gawd_auth()
    {

        check_ajax_referer('gawd_admin_page_nonce', 'security');
        $code = $_POST['token'];
        if(isset($code) && $code != ''){
          $status = GAWD_google_client::authenticate($code);
          if($status==true){
            update_option("gawd_user_status", "1");
          }

          $gawd_client = GAWD_google_client::get_instance(true);
          $accounts = $gawd_client->get_management_accounts();
          $account_status = true;
          if(empty($accounts)){
            $account_status = false;
          }
          if ($status === true && !empty($accounts)) {
            delete_option("gawd_account_status");
              $res = array(
                  'message' => 'successfully saved',
                  'status' => $status,
                  'account_status' => $account_status,
              );
          } else {
            update_option("gawd_account_status","no_account");
              $res = array(
                  'message' => 'there is an error',
                  'status' => $status,
                  'account_status' => $account_status
              );
          }
          header('content-type: application/json');
          echo json_encode($res);
          wp_die();
        }
    }

    /**
     * Displays the Dashboard page.
     */
    public function gawd_display_uninstall_page()
    {
        global  $gawd_options;
        if(!class_exists("DoradoWebConfig")){
          include_once (GMWD_DIR . "/wd/config.php"); 
        }
        $config = new DoradoWebConfig();

        $config->set_options( $gawd_options );

        $deactivate_reasons = new DoradoWebDeactivate($config);
        //$deactivate_reasons->add_deactivation_feedback_dialog_box();	
        $deactivate_reasons->submit_and_deactivate(); 
                
        require_once('admin/pages/uninstall.php'); 
        $gawd_uninstall = new GAWDUninstall();
        $deactivate_url = wp_nonce_url('plugins.php?action=deactivate&plugin=' . GWD_NAME . '/google-analytics-wd.php', 'deactivate-plugin_' . GWD_NAME . '/google-analytics-wd.php');
        $deactivate_url = str_replace('&amp;', '&', $deactivate_url);
        if (isset($_POST['gawd_submit_and_deactivate'])) {
            check_admin_referer('gawd_save_form', 'gawd_save_form_fild');
            delete_option('gawd_custom_reports');
            delete_option('gawd_menu_for_user');
            delete_option('gawd_all_metrics');
            delete_option('gawd_all_dimensions');
            delete_option('gawd_custom_dimensions');
            delete_option('gawd_settings');
            delete_option('gawd_user_data');
            delete_option('gawd_credentials');
            delete_option('gawd_menu_items');
            delete_option('gawd_export_chart_data');
            delete_option('gawd_email');
            delete_option('gawd_custom_reports');
            delete_option('gawd_alerts');
            delete_option('gawd_pushovers');
            delete_option('gawd_menu_for_users');
            delete_option('gawd_own_project');
            delete_option('gawd_zoom_message');
            delete_option('gawd_subscribe_done');
            delete_option('gawd_redirect_to_settings');
            delete_option('gawd_admin_notice');
            delete_option('gawd_user_status');
            delete_option('gawd_account_status');
            delete_transient('gawd_user_profiles');


           // echo '<script>window.location.href="' . $deactivate_url . '";</script>';
        }
        if (get_option('gawd_credentials')) {
            $gawd_uninstall->uninstall();
        }
    }

    public function gawd_display_goals_page()
    {
        global $gawd_client;
        if ($this->manage_ua_code_selection() != 'done') {
            return;
        }
        $gawd_client = GAWD_google_client::get_instance();
        if (!empty($_POST)) {
            check_admin_referer('gawd_save_form', 'gawd_save_form_fild');
        }
        $gawd_goal_profile = isset($_POST['gawd_goal_profile']) ? sanitize_text_field($_POST['gawd_goal_profile']) : '';
        $gawd_goal_name = isset($_POST['gawd_goal_name']) ? sanitize_text_field($_POST['gawd_goal_name']) : '';
        $gawd_goal_type = isset($_POST['gawd_goal_type']) ? sanitize_text_field($_POST['gawd_goal_type']) : '';
        $gawd_visit_hour = isset($_POST['gawd_visit_hour']) ? sanitize_text_field($_POST['gawd_visit_hour']) : '';
        $gawd_visit_minute = isset($_POST['gawd_visit_minute']) ? sanitize_text_field($_POST['gawd_visit_minute']) : '';
        $gawd_visit_second = isset($_POST['gawd_visit_second']) ? sanitize_text_field($_POST['gawd_visit_second']) : '';
        $gawd_goal_duration_comparison = isset($_POST['gawd_goal_duration_comparison']) ? sanitize_text_field($_POST['gawd_goal_duration_comparison']) : '';
        $gawd_goal_page_comparison = isset($_POST['gawd_goal_page_comparison']) ? sanitize_text_field($_POST['gawd_goal_page_comparison']) : '';
        $gawd_page_sessions = isset($_POST['gawd_page_sessions']) ? sanitize_text_field($_POST['gawd_page_sessions']) : '';
        $goal_max_id = isset($_POST['goal_max_id']) ? $_POST['goal_max_id'] + 1 : 1;
        $gawd_goal_page_destination_match = isset($_POST['gawd_goal_page_destination_match']) ? sanitize_text_field($_POST['gawd_goal_page_destination_match']) : '';
        $gawd_page_url = isset($_POST['gawd_page_url']) ? sanitize_text_field($_POST['gawd_page_url']) : '';
        $url_case_sensitve = isset($_POST['url_case_sensitve']) ? $_POST['url_case_sensitve'] : '';
        if ($gawd_goal_type == 'VISIT_TIME_ON_SITE') {
            if ($gawd_visit_hour != '' || $gawd_visit_minute != '' || $gawd_visit_second != '') {
                $value = 0;
                if ($gawd_visit_hour != '') {
                    $value += $gawd_visit_hour * 60 * 60;
                }
                if ($gawd_visit_minute != '') {
                    $value += $gawd_visit_minute * 60;
                }
                if ($gawd_visit_second != '') {
                    $value += $gawd_visit_second;
                }
            }
            $gawd_client->add_goal($gawd_goal_profile, $goal_max_id, $gawd_goal_type, $gawd_goal_name, $gawd_goal_duration_comparison, $value);
            add_option("gawd_save_goal", 1);
        } elseif ($gawd_goal_type == 'VISIT_NUM_PAGES') {
            if ($gawd_page_sessions != '') {
                $gawd_client->add_goal($gawd_goal_profile, $goal_max_id, $gawd_goal_type, $gawd_goal_name, $gawd_goal_page_comparison, $gawd_page_sessions);
            }
            add_option("gawd_save_goal", 1);
        } elseif ($gawd_goal_type == 'URL_DESTINATION') {
            if ($gawd_page_url != '') {
                $gawd_client->add_goal($gawd_goal_profile, $goal_max_id, $gawd_goal_type, $gawd_goal_name, $gawd_goal_page_destination_match, $gawd_page_url, $url_case_sensitve);
            }
            add_option("gawd_save_goal", 1);
        } elseif ($gawd_goal_type == 'EVENT') {
            if ($gawd_page_url != '') {
                $gawd_client->add_goal($gawd_goal_profile, $goal_max_id, $gawd_goal_type, $gawd_goal_name, $gawd_goal_page_comparison, $gawd_page_url, $url_case_sensitve);
            }
            add_option("gawd_save_goal", 1);
        }
        if (get_option('gawd_save_goal') == 1) {
            $this->gawd_admin_notice('Goal successfully has been created.', 'success is-dismissible');
        }
        delete_option('gawd_save_goal');
        require_once('admin/pages/goals.php');
    }

    public function gawd_display_custom_reports_page()
    {
        global $gawd_client;
        if (!empty($_POST)) {
            check_admin_referer('gawd_save_form', 'gawd_save_form_fild');
        }
        $gawd_client = GAWD_google_client::get_instance();
        $gawd_remove_custom_report = isset($_POST['gawd_remove_custom_report']) ? sanitize_text_field($_POST['gawd_remove_custom_report']) : '';
        if ($gawd_remove_custom_report) {
            $all_reports = get_option("gawd_custom_reports");
            if ($all_reports) {
                unset($all_reports[$gawd_remove_custom_report]);
                update_option('gawd_custom_reports', $all_reports);
                self::add_dashboard_menu();
            }
        }
        if (isset($_POST['gawd_add_custom_report'])) {
            $gawd_custom_report_name = isset($_POST['gawd_custom_report_name']) ? sanitize_text_field($_POST['gawd_custom_report_name']) : '';
            $gawd_custom_report_metric = isset($_POST['gawd_custom_report_metric']) ? sanitize_text_field($_POST['gawd_custom_report_metric']) : '';
            $gawd_custom_report_dimension = isset($_POST['gawd_custom_report_dimension']) ? sanitize_text_field($_POST['gawd_custom_report_dimension']) : '';
            if ($gawd_custom_report_name != '' && $gawd_custom_report_metric != '' && $gawd_custom_report_dimension != '') {
                $saved_custom_reports = get_option("gawd_custom_reports");
                if (!isset($saved_custom_reports[$gawd_custom_report_name])) {
                    if ($saved_custom_reports) {
                        $custom_reports = array(
                            'metric' => $gawd_custom_report_metric,
                            'dimension' => $gawd_custom_report_dimension,
                            'id' => count($saved_custom_reports) + 1
                        );
                        $saved_custom_reports[$gawd_custom_report_name] = $custom_reports;
                        update_option('gawd_custom_reports', $saved_custom_reports);
                    } else {
                        $custom_reports = array(
                            $gawd_custom_report_name => array(
                                'metric' => $gawd_custom_report_metric,
                                'dimension' => $gawd_custom_report_dimension,
                                'id' => 1
                            )
                        );
                        update_option('gawd_custom_reports', $custom_reports);
                    }
                }
            }
            self::add_dashboard_menu();
        }
        require_once('admin/pages/custom_reports.php');
    }

    public function gawd_display_overview_page()
    {
        global $gawd_client, $gawd_user_data;
        $gawd_client = GAWD_google_client::get_instance();
        $profiles = $gawd_client->get_profiles();
        $gawd_user_data = get_option('gawd_user_data');
        if (isset($_POST['gawd_id'])) {
            $gawd_user_data['gawd_id'] = isset($_POST['gawd_id']) ? $_POST['gawd_id'] : '';
            foreach ($gawd_user_data['gawd_profiles'] as $web_property_name => $web_property) {
                foreach ($web_property as $profile) {
                    if ($profile['id'] == $gawd_user_data['gawd_id']) {
                        $gawd_user_data['web_property_name'] = $web_property_name;
                        $gawd_user_data['webPropertyId'] = $profile['webPropertyId'];
                        $gawd_user_data['accountId'] = $profile['accountId'];
                    }
                }
            }
            $gawd_user_data['web_property_name'] = isset($_POST['web_property_name']) ? $_POST['web_property_name'] : '';
            update_option('gawd_user_data', $gawd_user_data);
        }
        require_once('admin/pages/overview.php');
    }

    public function gawd_display_reports_page()
    {
        global $gawd_client, $gawd_user_data;
        $gawd_client = GAWD_google_client::get_instance();
        $profiles = $gawd_client->get_profiles();
        $gawd_user_data = get_option('gawd_user_data');
        $gawd_settings = get_option('gawd_settings');
        if (isset($_POST['gawd_id'])) {
            $gawd_user_data['gawd_id'] = isset($_POST['gawd_id']) ? $_POST['gawd_id'] : '';
            foreach ($gawd_user_data['gawd_profiles'] as $web_property_name => $web_property) {
                foreach ($web_property as $profile) {
                    if ($profile['id'] == $gawd_user_data['gawd_id']) {
                        $gawd_user_data['web_property_name'] = $web_property_name;
                        $gawd_user_data['webPropertyId'] = $profile['webPropertyId'];
                        $gawd_user_data['accountId'] = $profile['accountId'];
                    }
                }
            }
            $gawd_user_data['web_property_name'] = isset($_POST['web_property_name']) ? $_POST['web_property_name'] : '';
            update_option('gawd_user_data', $gawd_user_data);
        }
        require_once('admin/pages/dashboard.php');
    }

    public function gawd_daily_email()
    {
        require_once(GAWD_DIR . '/admin/gawd_google_class.php');
        $gawd_client = GAWD_google_client::get_instance();
        $emails = get_option('gawd_email');
        $gawd_user_data = get_option('gawd_user_data');
        $data = '';
        foreach ($emails as $email) {
            if (isset($email['period']) && $email['period'] == 'daily') {
                //pls send email if ....
                $date = date('Y-m-d', strtotime('yesterday'));
                $email_subject = preg_match('/\(([0-9]{4}-[0-1][0-9]-[0-3][0-9] \- [0-9]{4}-[0-1][0-9]-[0-3][0-9])\)/', $email['email_subject']) ? preg_replace('/\(([0-9]{4}-[0-1][0-9]-[0-3][0-9] \- [0-9]{4}-[0-1][0-9]-[0-3][0-9])\)/', '(' . $date . ' - ' . $date . ')', $email['email_subject']) : $email['email_subject'] . ' (' . $date . ' - ' . $date . ')';
                $data = $this->show_data(array(
                    'metric' => 'ga:' . $email['metric'],
                    'dimension' => $email['dimension'],
                    'start_date' => $date,
                    'end_date' => $date
                ));
                if ($email['export_type'] == 'pdf') {
                    $filedir = $this->create_pdf_file(false, $data, $email['dimension'], $date, $date, $email['metric_compare'], $email['metric']);
                } else {
                    $filedir = $this->create_csv_file(false, $data, $email['dimension'], $date, $date, $email['metric_compare'], $email['metric']);
                }
                //$attachment = gawd_export_data($data, $export_type, 'email', $email['dimension'], $email['metric'], $email['metric_compare'], $email['img'], $email['tab_name'], $start_date, $end_date, $gawd_user_data['web_property_name'],$filter_type);
                $attachment = $filedir;
                $headers = 'From: <' . $email['email_from'] . '>';
                wp_mail($email['emails'], $email_subject, $email['email_body'], $headers, $attachment);
            }
        }
    }

    public function gawd_weekly_email()
    {
        require_once(GAWD_DIR . '/admin/gawd_google_class.php');
        $gawd_client = GAWD_google_client::get_instance();
        $emails = get_option('gawd_email');
        $gawd_user_data = get_option('gawd_user_data');
        $data = '';
        foreach ($emails as $email) {
            if (isset($email['period']) && $email['period'] == 'gawd_weekly') {
                //pls send email if ....
                /*$start_date = date('Y-m-d', strtotime('last' . $email['period_day']));

                $end_date = date('Y-m-d', strtotime('this' . $email['period_day']));*/
                $start_date = date('Y-m-d', strtotime('last week -1 day'));
                $end_date = date('l') != 'Sunday' ? date('Y-m-d', strtotime('last sunday -1 day')) : date('Y-m-d', strtotime('-1 day'));
                $email_subject = preg_match('/\(([0-9]{4}-[0-1][0-9]-[0-3][0-9] \- [0-9]{4}-[0-1][0-9]-[0-3][0-9])\)/', $email['email_subject']) ? preg_replace('/\(([0-9]{4}-[0-1][0-9]-[0-3][0-9] \- [0-9]{4}-[0-1][0-9]-[0-3][0-9])\)/', '(' . $start_date . ' - ' . $end_date . ')', $email['email_subject']) : $email['email_subject'] . ' (' . $start_date . ' - ' . $end_date . ')';
                $data = $this->show_data(array(
                    'metric' => 'ga:' . $email['metric'],
                    'dimension' => $email['dimension'],
                    'start_date' => $start_date,
                    'end_date' => $end_date
                ));
                if ($email['export_type'] == 'pdf') {
                    $filedir = $this->create_pdf_file(false, $data, $email['dimension'], $start_date, $end_date, $email['metric_compare'], $email['metric']);
                } else {
                    $filedir = $this->create_csv_file(false, $data, $email['dimension'], $start_date, $end_date, $email['metric_compare'], $email['metric']);
                }
                //$attachment = gawd_export_data($data, $export_type, 'email', $email['dimension'], $email['metric'], $email['metric_compare'], $email['img'], $email['tab_name'], $start_date, $end_date, $gawd_user_data['web_property_name'],$filter_type);
                $attachment = $filedir;
                $headers = 'From: <' . $email['email_from'] . '>';
                wp_mail($email['emails'], $email_subject, $email['email_body'], $headers, $attachment);
            }
        }
    }

    public function gawd_monthly_email()
    {
        require_once(GAWD_DIR . '/admin/gawd_google_class.php');
        $gawd_client = GAWD_google_client::get_instance();
        $emails = get_option('gawd_email');
        $gawd_user_data = get_option('gawd_user_data');
        $data = '';
        foreach ($emails as $email) {
            if (isset($email['period']) && $email['period'] == 'gawd_monthly') {
                //pls send email if ....
                $end_date = date('Y-m-d', strtotime(date('Y-' . date('m') . '-1') . '-1 day'));
                $start_date = date('Y-m-d', strtotime($end_date . '- 1 month'));
                $data = $this->show_data(array(
                    'metric' => 'ga:' . $email['metric'],
                    'dimension' => $email['dimension'],
                    'start_date' => $start_date,
                    'end_date' => $end_date
                ));
                $email_subject = preg_match('/\(([0-9]{4}-[0-1][0-9]-[0-3][0-9] \- [0-9]{4}-[0-1][0-9]-[0-3][0-9])\)/', $email['email_subject']) ? preg_replace('/\(([0-9]{4}-[0-1][0-9]-[0-3][0-9] \- [0-9]{4}-[0-1][0-9]-[0-3][0-9])\)/', '(' . $start_date . ' - ' . $end_date . ')', $email['email_subject']) : $email['email_subject'] . ' (' . $start_date . ' - ' . $end_date . ')';
                if ($email['export_type'] == 'pdf') {
                    $filedir = $this->create_pdf_file(false, $data, $email['dimension'], $start_date, $end_date, $email['metric_compare'], $email['metric']);
                } else {
                    $filedir = $this->create_csv_file(false, $data, $email['dimension'], $start_date, $end_date, $email['metric_compare'], $email['metric']);
                }
                //$attachment = gawd_export_data($data, $export_type, 'email', $email['dimension'], $email['metric'], $email['metric_compare'], $email['img'], $email['tab_name'], $start_date, $end_date, $gawd_user_data['web_property_name'],$filter_type);
                $attachment = $filedir;
                $headers = 'From: <' . $email['email_from'] . '>';
                wp_mail($email['emails'], $email_subject, $email['email_body'], $headers, $attachment);
            }
        }
    }

    /**
     * Prepares the settings to be displayed then displays the settings page.
     */
    public static function gawd_settings_defaults()
    {
        $settings = get_option('gawd_settings');
        $settings['gawd_tracking_enable'] = 'on';
        $settings['gawd_custom_dimension_Logged_in'] = 'on';
        $settings['gawd_custom_dimension_Post_type'] = 'on';
        $settings['gawd_custom_dimension_Author'] = 'on';
        $settings['gawd_custom_dimension_Category'] = 'on';
        $settings['gawd_custom_dimension_Published_Month'] = 'on';
        $settings['gawd_custom_dimension_Published_Year'] = 'on';
        $settings['gawd_custom_dimension_Tags'] = 'on';
        $settings['enable_hover_tooltip'] = 'on';
        $settings['gawd_show_in_dashboard'] = 'on';
        $settings['post_page_chart'] = 'on';
        $settings['show_report_page'] = 'off';
        update_option('gawd_settings', $settings);
    }

    public function manage_ua_code_selection()
    {
        global $gawd_user_data, $gawd_client;
        $gawd_user_data = get_option('gawd_user_data');
        if (isset($gawd_user_data['default_webPropertyId']) && $gawd_user_data['default_webPropertyId']) {
            return 'done';
        } else {
            $gawd_client = GAWD_google_client::get_instance();
            $property = $gawd_client->property_exists();
            if ($property == 'no_matches') {
                //$this->gawd_admin_notice("<p class='gawd_notice'>You don't have any web-properties with current site url, go with <a href='" . admin_url('admin.php?page=gawd_tracking') . "'>this</a> link to add.</p>", 'error');
                // show notice that you don't have property with current site url
                // add account or property to an existing account
            } elseif (count($property) == 1) {
                $property = $property[0];
                $gawd_user_data['webPropertyId'] = $property['id'];
                $gawd_user_data['default_webPropertyId'] = $property['id'];
                $gawd_user_data['accountId'] = $property['accountId'];
                $gawd_user_data['default_accountId'] = $property['accountId'];
                $gawd_user_data['gawd_id'] = $property['defaultProfileId'];
                update_option('gawd_user_data', $gawd_user_data);
                $this->gawd_admin_notice("In order to enable tracking for your website, you have to go with

                    <a href='" . admin_url('admin.php?page=gawd_tracking') . "'>this</a> link and turn the option on.", 'warning is-dismissible');
                // show notice that you have to enable tracking code, link to tracking submenu
            } else {
                $this->gawd_admin_notice("You have two or more web-properties configured with current site url. Please go with

                    <a href='" . admin_url('admin.php?page=gawd_tracking') . "'>this</a> link to select the proper one.", 'error');
                // show notice that you have >=2 properties with current site url
                // select property from same url properties
            }
        }
    }

    public function manage_ua_code_selection_tracking()
    {
        global $gawd_user_data;
        if (isset($gawd_user_data['default_webPropertyId']) && $gawd_user_data['default_webPropertyId']) {
            return 'done';
        } else {
            $gawd_client = GAWD_google_client::get_instance();
            $property = $gawd_client->property_exists();
            if ($property == 'no_matches') {
                $accounts = $gawd_client->get_management_accounts();
                if (!empty($accounts)) {
                    echo "<h3 style='margin-top:10px' class='gawd_page_titles'>Tracking</h3>

                    <p class='gawd_notice notice'>Create <b>web property</b> on your Google Analytics account to enable tracking of this website. After creating a <b>web property</b> Google Analytics tracking code automatically will be added to your website.</p></br>

                    <form method='post' id='gawd_property_add'>

                    <div class='gawd_settings_wrapper'>

                        <div class='gawd_goal_row'>

                            <span class='gawd_goal_label'>Account</span>

                            <span class='gawd_goal_input'>

                                <select name='gawd_account_select' class='gawd_account_select' style='padding: 2px;width: 96%;line-height: 30px;height: 30px !important;'>";
                    foreach ($accounts as $account) {
                        echo "<option value='" . $account['id'] . "'>" . $account['name'] . "</option>";
                    }
                    echo "</select>

                            </span>

                            <div class='gawd_info' title='Choose the Google Analytics account to connect this property to.'></div>

                            <div class='clear'></div>

                        </div>

                        <div class='gawd_goal_row'>

                            <span class='gawd_goal_label'>Name</span>

                            <span class='gawd_goal_input'>

                                <input id='gawd_property_name' name='gawd_property_name' type='text'>

                            </span>

                            <div class='gawd_info' title='Provide a name for the property.'></div>

                            <div class='clear'></div>

                        </div>  

                    </div>

                    <div class='gawd_add_prop gawd_submit'>

                        <a href='" . admin_url() . "admin.php?page=gawd_analytics' class='gawd_later button_gawd'>Later</a>

                        <input type='button' id='gawd_add_property' class='button_gawd' value='Add'/>

                        <input type='hidden' id='add_property' name='add_property'/>

                    </div>

                    </form>";
                    // account select to add web property and web property parameters
                    // and add link to google analytics for manually creating an account
                    // wp_die();
                }/* else {
                    //$this->gawd_admin_notice("You do not have any google analytics accounts set. Please go with <a href='https://analytics.google.com/' target='_blank'>this</a> link to add one.", "error");
                    // link to google analytics to add account
                    // wp_die();
                }*/
            } elseif (count($property) == 1) {
                $property = $property[0];
                $gawd_user_data['webPropertyId'] = $property['id'];
                $gawd_user_data['default_webPropertyId'] = $property['id'];
                $gawd_user_data['accountId'] = $property['accountId'];
                $gawd_user_data['default_accountId'] = $property['accountId'];
                $gawd_user_data['gawd_id'] = $property['defaultProfileId'];
                update_option('gawd_user_data', $gawd_user_data);
            } else {
                echo "<p class='notice'>You have multiple web-properties set with current site url. Please select the one which you want to use for tracking from the list below.</p>

                <br/>

                <form method='post' id='gawd_property_select'>

                <div class='gawd_settings_wrapper'>

                    <div class='gawd_goal_row'>

                        <span class='gawd_goal_label'>Web-property</span>

                        <span class='gawd_goal_input'>

                            <select name='gawd_property_select' class='gawd_property_select' style='padding: 2px;width: 96%;line-height: 30px;height: 30px !important;'>";
                foreach ($property as $select_property) {
                    echo "<option value='" . $select_property['id'] . "'>" . $select_property['name'] . " (" . $select_property['id'] . ")</option>";
                }
                echo "</select>

                        </span>

                        <div class='gawd_info' title=''></div>

                        <div class='clear'></div>

                    </div>

                </div>

                <div class='gawd_submit'><input type='submit' name='lock_property' class='button_gawd' value='SAVE'/></div>

                </form>";
                // web property select to select from properties with same site url
                // wp_die();
            }
        }
    }

    public function gawd_admin_notice($message, $type)
    {
        $class = 'notice notice-' . $type;
        echo '<div class="' . $class . '"><p>' . $message . '</p></div>';
    }

    public function gawd_display_settings_page()
    {
        global $gawd_user_data;
        $gawd_client = GAWD_google_client::get_instance();
        $accounts = $gawd_client->get_management_accounts();
        $gawd_user_data = get_option('gawd_user_data');
        if (isset($_GET['defaultExist']) && $_GET['defaultExist'] == 1) {
            $redirect_url = admin_url() . 'admin.php?page=gawd_tracking';
            echo '<script>window.location.href="' . $redirect_url . '";</script>';
        }
        if (isset($_POST['gawd_settings_logout']) && $_POST['gawd_settings_logout'] == 1) {
            delete_option('gawd_user_data');
            $redirect_url = admin_url() . 'admin.php?page=gawd_settings';
            echo '<script>window.location.href="' . $redirect_url . '";</script>';
        }
        if (isset($_POST['web_property_name']) && $_POST['web_property_name'] != '') {
            $gawd_user_data['gawd_id'] = isset($_POST['gawd_id']) ? $_POST['gawd_id'] : '';
            foreach ($gawd_user_data['gawd_profiles'] as $web_property_name => $web_property) {
                foreach ($web_property as $profile) {
                    if ($profile['id'] == $gawd_user_data['gawd_id']) {
                        $gawd_user_data['web_property_name'] = $web_property_name;
                        $gawd_user_data['webPropertyId'] = $profile['webPropertyId'];
                        $gawd_user_data['accountId'] = $profile['accountId'];
                    }
                }
            }
            $gawd_user_data['web_property_name'] = isset($_POST['web_property_name']) ? $_POST['web_property_name'] : '';
            update_option('gawd_user_data', $gawd_user_data);
            $redirect_url = admin_url() . 'admin.php?page=gawd_settings';
            //echo '<script>window.location.href="'.$redirect_url.'";</script>';
        }
        /*         if(isset($_POST['account_name']) && $_POST['account_name'] != ''){

            $gawd_user_data['accountId'] = isset($_POST['gawd_id']) ? $_POST['gawd_id'] : '';

            foreach ($gawd_user_data['gawd_profiles'] as $web_property_name => $web_property) {

                foreach ($web_property as $profile) {

                    if ($profile['accountId'] == $gawd_user_data['accountId']) {

                        $gawd_user_data['web_property_name'] = $web_property_name;

                        $gawd_user_data['webPropertyId'] = $profile['webPropertyId'];

                        $gawd_user_data['accountId'] = $profile['accountId'];

                    }

                }

            }

            $gawd_user_data['web_property_name'] = isset($_POST['web_property_name']) ? $_POST['web_property_name'] : '';

            update_option('gawd_user_data', $gawd_user_data);

            $redirect_url = admin_url() . 'admin.php?page=gawd_settings';

            //echo '<script>window.location.href="'.$redirect_url.'";</script>';

        } */
        if (isset($_GET['errorMsg'])) {
            self::error_message('error', 'User does not have sufficient permissions for this account to add filter');
        }
        $gawd_user_status = get_option("gawd_user_status");
        if (!isset($gawd_user_data['refresh_token']) || ($gawd_user_data['refresh_token'] == '') || empty($accounts)) {
            $account_status = get_option("gawd_account_status");
            if($account_status === "no_account"){
                echo '<div class="no_account_content clearfix"><p>You don\'t have Google Analytics account. Please create it first then authenticate again.</p><a class="gawd_account_button gwd_refresh_page" href="">Refresh</a>
<div class="gawd_account_button gwd_another_account" onclick="show_authenticate_form()">Authenticate with another account</div>
<a class="gawd_auth_button gwd_create_account" href="https://analytics.google.com/analytics/web/provision/?authuser=0#provision/SignUp/" target="_blank" onclick="reload_account()">CREATE</a>
</div>';
            }
            $hide_auth_wrap = "hide_auth_wrap";
            if($gawd_user_status!="1"){
              $hide_auth_wrap = "";
            }

          echo '<div class="gawd_auth_wrap gawd_auth_authenticate '.$hide_auth_wrap.'"><p class="auth_description">Click <b>Authenticate</b> button and login to your Google account. A window asking for relevant permissions will appear. Click <b>Allow</b> and copy the authentication code from the text input.</p><div id="gawd_auth_url" onclick="gawd_auth_popup(' . GAWD_google_client::create_authentication_url() . ',800,400)" style="cursor: pointer;"><div class="gawd_auth_button">AUTHENTICATE</div><div class="clear"></div></div>';
          echo '<div id="gawd_auth_code"><form id="gawd_auth_code_paste" action="" method="post" onSubmit="return false;">

      <p style="margin:0;color: #444;">Paste the authentication code from the popup to this input.</p>

      <input id="gawd_token" type="text">';
            wp_nonce_field("gawd_save_form", "gawd_save_form_fild");
            echo '</form>

          <div id="gawd_auth_code_submit">SUBMIT</div></div>';
            $gawd_own_project = get_option('gawd_own_project');
            if (isset($gawd_own_project) && $gawd_own_project && intval($gawd_own_project) == 1) {
                echo '<form method="post">

                        <div class="gawd_reset_button">

                          <input type="hidden" name="reset_data" id="reset_data"/>

                          <input type="button"  class="button_gawd" id="gawd_reset_button" value="RESET"/>

                        </div>

                  </form>';
            }
            echo '</div><div id="opacity_div" style="display: none; background-color: rgba(0, 0, 0, 0.2); position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 99998;"></div>

          <div id="loading_div" style="display:none; text-align: center; position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 99999;">

            <img src="' . GAWD_URL . '/assets/ajax_loader.gif"  style="margin-top: 200px; width:50px;">

          </div>';
        } else {
            if ($this->manage_ua_code_selection() != 'done') {
                // return;
            }
            try {
                $gawd_client = GAWD_google_client::get_instance();
                $gawd_client->get_profiles();
            } catch (Google_Service_Exception $e) {
                $errors = $e->getErrors();
                return $errors[0]["message"];
            } catch (Exception $e) {
				$myFile = GAWD_UPLOAD_DIR."/logfile.txt"; ;
				$fh = fopen($myFile, 'a');
				fwrite($fh, $e->getMessage(). "----gawd_display_settings_page function".PHP_EOL);
				fclose($fh);
                return $e->getMessage();
            }
            $gawd_alert_remove = isset($_POST['gawd_alert_remove']) ? intval($_POST['gawd_alert_remove']) : false;
            $gawd_menu_remove = isset($_POST['gawd_menu_remove']) ? intval($_POST['gawd_menu_remove']) : false;
            $gawd_pushover_remove = isset($_POST['gawd_pushover_remove']) ? intval($_POST['gawd_pushover_remove']) : false;
            $gawd_email_remove = isset($_POST['gawd_email_remove']) ? intval($_POST['gawd_email_remove']) : false;
            $gawd_filter_remove = isset($_POST['gawd_filter_remove']) ? intval($_POST['gawd_filter_remove']) : false;
            if ($gawd_alert_remove) {
                $all_alerts = get_option('gawd_alerts');
                if ($all_alerts) {
                    foreach ($all_alerts as $alert) {
                        wp_unschedule_event(wp_next_scheduled('gawd_alert_' . $alert['period']), 'gawd_alert_' . $alert['period']);
                    }
                    unset($all_alerts[$gawd_alert_remove - 1]);
                    update_option('gawd_alerts', $all_alerts);
                }
            }
            if ($gawd_menu_remove) {
                $all_menues = get_option('gawd_menu_for_user');
                if ($all_menues) {
                    unset($all_menues[$gawd_menu_remove]);
                    update_option('gawd_menu_for_user', $all_menues);
                }
            }
            if ($gawd_email_remove) {
                $all_emails = get_option('gawd_email');
                if ($all_emails) {
                    foreach ($all_emails as $email) {
                        wp_unschedule_event(wp_next_scheduled('gawd_email_' . $email['period']), 'gawd_email_' . $email['period']);
                    }
                    unset($all_emails[$gawd_email_remove - 1]);
                    update_option('gawd_email', $all_emails);
                }
            }
            if ($gawd_filter_remove) {
                $analytics = $gawd_client->analytics_member;
                $accountId = $gawd_client->get_profile_accountId();
                try {
                    $analytics->management_filters->delete($accountId, $gawd_filter_remove);
                } catch (apiServiceException $e) {
                    print 'There was an Analytics API service error '
                        . $e->getCode() . ':' . $e->getMessage();
                } catch (apiException $e) {
                    print 'There was a general API error '
                        . $e->getCode() . ':' . $e->getMessage();
                } catch (Exception $e) {
					$myFile = GAWD_UPLOAD_DIR."/logfile.txt"; ;
					$fh = fopen($myFile, 'a');
					fwrite($fh, $e->getMessage(). "----check_property_delete function  get_profile_accountId".PHP_EOL);
					fclose($fh);
                    echo '<script>window.location.href="' . admin_url() . 'admin.php?page=gawd_settings&errorMsg=1#gawd_filters_tab";</script>';
                }
            }
            $gawd_pushover_remove = isset($_POST['gawd_pushover_remove']) ? $_POST['gawd_pushover_remove'] : false;
            if ($gawd_pushover_remove) {
                $all_pushovers = get_option('gawd_pushovers');
                if ($all_pushovers) {
                    foreach ($all_pushovers as $pushover) {
                        wp_unschedule_event(wp_next_scheduled('gawd_pushover_' . $pushover['period']), 'gawd_pushover_' . $pushover['period']);
                    }
                    unset($all_pushovers[$gawd_pushover_remove - 1]);
                    update_option('gawd_pushovers', $all_pushovers);
                }
            }
            if (isset($_POST['settings_submit'])) {
                check_admin_referer('gawd_save_form', 'gawd_save_form_fild');
                $gawd_user_data = get_option('gawd_user_data');
                $gawd_alert_name = isset($_POST['gawd_alert_name']) ? sanitize_text_field($_POST['gawd_alert_name']) : '';
                $gawd_alert_period = isset($_POST['gawd_alert_name']) ? sanitize_text_field($_POST['gawd_alert_period']) : '';
                $gawd_alert_metric = isset($_POST['gawd_alert_metric']) ? sanitize_text_field($_POST['gawd_alert_metric']) : '';
                $gawd_alert_condition = isset($_POST['gawd_alert_condition']) ? sanitize_text_field($_POST['gawd_alert_condition']) : '';
                $gawd_alert_value = isset($_POST['gawd_alert_value']) ? sanitize_text_field($_POST['gawd_alert_value']) : '';
                $gawd_alert_emails = isset($_POST['gawd_alert_emails']) ? sanitize_email($_POST['gawd_alert_emails']) : '';
                $gawd_alert_view = isset($_POST['gawd_alert_view']) ? sanitize_text_field($_POST['gawd_alert_view']) : '';
                $alert_view_name = isset($_POST['alert_view_name']) ? sanitize_text_field($_POST['alert_view_name']) : '';
                if ($gawd_alert_name != '' && $gawd_alert_period != '' && $gawd_alert_metric != '' && $gawd_alert_condition != '' && $gawd_alert_value != '' && $gawd_alert_emails != '') {
                    $saved_alerts = get_option('gawd_alerts');
                    if ($saved_alerts) {
                        $gawd_alert_options = array(
                            'name' => $gawd_alert_name,
                            'period' => $gawd_alert_period,
                            'metric' => $gawd_alert_metric,
                            'condition' => $gawd_alert_condition,
                            'value' => $gawd_alert_value,
                            'creation_date' => date('Y-m-d'),
                            'emails' => $gawd_alert_emails,
                            'alert_view' => $gawd_alert_view,
                            'alert_view_name' => $alert_view_name
                        );
                        $saved_alerts[] = $gawd_alert_options;
                        update_option('gawd_alerts', $saved_alerts);
                    } else {
                        $gawd_alert_options = array(
                            0 => array(
                                'name' => $gawd_alert_name,
                                'period' => $gawd_alert_period,
                                'metric' => $gawd_alert_metric,
                                'condition' => $gawd_alert_condition,
                                'value' => $gawd_alert_value,
                                'creation_date' => date('Y-m-d'),
                                'emails' => $gawd_alert_emails,
                                'alert_view' => $gawd_alert_view,
                                'alert_view_name' => $alert_view_name
                            )
                        );
                        update_option('gawd_alerts', $gawd_alert_options);
                    }
                    $saved_alerts = get_option('gawd_alerts');
                    if ($saved_alerts) {
                        foreach ($saved_alerts as $alert) {
                            if (!wp_next_scheduled('gawd_alert_' . $alert['period'])) {
                                wp_schedule_event(time(), $alert['period'], 'gawd_alert_' . $alert['period']);
                            }
                        }
                    }
                }
                $gawd_pushover_name = isset($_POST['gawd_pushover_name']) ? sanitize_text_field($_POST['gawd_pushover_name']) : '';
                $gawd_pushover_period = isset($_POST['gawd_pushover_period']) ? sanitize_text_field($_POST['gawd_pushover_period']) : '';
                $gawd_pushover_metric = isset($_POST['gawd_pushover_metric']) ? sanitize_text_field($_POST['gawd_pushover_metric']) : '';
                $gawd_pushover_condition = isset($_POST['gawd_pushover_condition']) ? sanitize_text_field($_POST['gawd_pushover_condition']) : '';
                $gawd_pushover_value = isset($_POST['gawd_pushover_value']) ? intval($_POST['gawd_pushover_value']) : '';
                $gawd_pushover_user_keys = isset($_POST['gawd_pushover_user_keys']) ? sanitize_text_field($_POST['gawd_pushover_user_keys']) : '';
                $gawd_pushover_view = isset($_POST['gawd_pushover_view']) ? sanitize_text_field($_POST['gawd_pushover_view']) : '';
                $pushover_view_name = isset($_POST['pushover_view_name']) ? sanitize_text_field($_POST['pushover_view_name']) : '';
                if ($gawd_pushover_name != '' && $gawd_pushover_period != '' && $gawd_pushover_metric != '' && $gawd_pushover_condition != '' && $gawd_pushover_value !== '' && $gawd_pushover_user_keys != '') {
                    $saved_pushovers = get_option('gawd_pushovers');
                    if ($saved_pushovers) {
                        $gawd_pushover_options = array(
                            'name' => $gawd_pushover_name,
                            'period' => $gawd_pushover_period,
                            'metric' => $gawd_pushover_metric,
                            'condition' => $gawd_pushover_condition,
                            'value' => $gawd_pushover_value,
                            'creation_date' => date('Y-m-d'),
                            'user_key' => $gawd_pushover_user_keys,
                            'pushover_view' => $gawd_pushover_view,
                            'pushover_view_name' => $pushover_view_name
                        );
                        $saved_pushovers[] = $gawd_pushover_options;
                        update_option('gawd_pushovers', $saved_pushovers);
                    } else {
                        $gawd_pushover_options = array(
                            0 => array(
                                'name' => $gawd_pushover_name,
                                'period' => $gawd_pushover_period,
                                'metric' => $gawd_pushover_metric,
                                'condition' => $gawd_pushover_condition,
                                'value' => $gawd_pushover_value,
                                'creation_date' => date('Y-m-d'),
                                'user_key' => $gawd_pushover_user_keys,
                                'pushover_view' => $gawd_pushover_view,
                                'pushover_view_name' => $pushover_view_name
                            )
                        );
                        update_option('gawd_pushovers', $gawd_pushover_options);
                    }
                    $saved_pushovers = get_option('gawd_pushovers');
                    if ($saved_pushovers) {
                        foreach ($saved_pushovers as $pushover) {
                            $this->gawd_pushover_api($pushover['user_key'], $pushover['metric'], $pushover['condition'], $pushover['value']);
                            if (!wp_next_scheduled('gawd_pushover_' . $pushover['period'])) {
                                wp_schedule_event(time(), $pushover['period'], 'gawd_pushover_' . $pushover['period']);
                            }
                        }
                    }
                }
                $gawd_show_in_dashboard = isset($_POST['gawd_show_in_dashboard']) ? sanitize_text_field($_POST['gawd_show_in_dashboard']) : '';
                $gawd_permissions = isset($_POST['gawd_permissions']) ? explode(',', $_POST['gawd_permissions']) : array('manage_options');
                $gawd_own_project = isset($_POST['gawd_own_project']) ? sanitize_text_field($_POST['gawd_own_project']) : '';
                $site_speed_rate = isset($_POST['site_speed_rate']) ? intval($_POST['site_speed_rate']) : '1';
                $post_page_chart = isset($_POST['post_page_chart']) ? sanitize_text_field($_POST['post_page_chart']) : '';
                $enable_cross_domain = isset($_POST['enable_cross_domain']) ? sanitize_text_field($_POST['enable_cross_domain']) : '';
                $cross_domains = isset($_POST['cross_domains']) ? sanitize_text_field($_POST['cross_domains']) : '';
                $default_date = isset($_POST['default_date']) ? $_POST['default_date'] : 'last_7_days';
                $default_date_format = isset($_POST['default_date_format']) ? $_POST['default_date_format'] : 'ymd_with_week';
                $enable_hover_tooltip = isset($_POST['enable_hover_tooltip']) ? $_POST['enable_hover_tooltip'] : '';
                $gawd_backend_roles = isset($_POST['gawd_backend_roles']) ? explode(',', $_POST['gawd_backend_roles']) : array('administrator');
                $gawd_frontend_roles = isset($_POST['gawd_frontend_roles']) ? explode(',', $_POST['gawd_frontend_roles']) : array('administrator');
                $gawd_post_page_roles = isset($_POST['gawd_post_page_roles']) ? explode(',', $_POST['gawd_post_page_roles']) : array('administrator');
                $exclude_events = isset($_POST['exclude_events']) ? sanitize_text_field($_POST['exclude_events']) : array();
                $gawd_settings_exist = get_option('gawd_settings');
                $gawd_settings_exist['gawd_show_in_dashboard'] = $gawd_show_in_dashboard;
                $gawd_settings_exist['site_speed_rate'] = $site_speed_rate;
                $gawd_settings_exist['post_page_chart'] = $post_page_chart;
                $gawd_settings_exist['enable_cross_domain'] = $enable_cross_domain;
                $gawd_settings_exist['cross_domains'] = $cross_domains;
                $gawd_settings_exist['gawd_backend_roles'] = $gawd_backend_roles;
                $gawd_settings_exist['gawd_frontend_roles'] = $gawd_frontend_roles;
                $gawd_settings_exist['gawd_post_page_roles'] = $gawd_post_page_roles;
                $gawd_settings_exist['default_date'] = $default_date;
                $gawd_settings_exist['default_date_format'] = $default_date_format;
                $gawd_settings_exist['enable_hover_tooltip'] = $enable_hover_tooltip;
                $gawd_settings_exist['exclude_events'] = $exclude_events;
                $gawd_settings_exist['gawd_permissions'] = $gawd_permissions;
                update_option('gawd_settings', $gawd_settings_exist);
                $gawd_filter_name = isset($_POST['gawd_filter_name']) ? sanitize_text_field($_POST['gawd_filter_name']) : '';
                $gawd_filter_type = isset($_POST['gawd_filter_type']) ? sanitize_text_field($_POST['gawd_filter_type']) : '';
                $gawd_filter_value = isset($_POST['gawd_filter_value']) ? $gawd_filter_type == 'GEO_IP_ADDRESS' ? ($_POST['gawd_filter_value']) : sanitize_text_field($_POST['gawd_filter_value']) : '';
                if ($gawd_filter_name != '' && $gawd_filter_type != '' && $gawd_filter_value != '') {
                    $gawd_client->add_filter($gawd_filter_name, $gawd_filter_type, $gawd_filter_value);
                }
                add_option("gawd_save_settings", 1);
            }
            if (get_option('gawd_save_settings') == 1) {
                $this->gawd_admin_notice('Your changes have been saved successfully.', 'success is-dismissible');
            }
            delete_option('gawd_save_settings');
            require_once('admin/pages/settings.php');
        }
    }

    public function reset_user_data()
    {
        delete_option("gawd_credentials");
        $credentials['project_id'] = '115052745574-5vbr7tci4hjkr9clkflmnpto5jisgstg.apps.googleusercontent.com';
        $credentials['project_secret'] = 'wtNiu3c_bA_g7res6chV0Trt';
        update_option('gawd_credentials', $credentials);
        delete_option('gawd_own_project');
        delete_option('gawd_user_data');
    }

    public function gawd_display_tracking_page()
    {
        global $gawd_client, $gawd_user_data;
        $gawd_client = GAWD_google_client::get_instance();
        $gawd_user_data = get_option('gawd_user_data');
        $add_dimension_value = isset($_POST['add_dimension_value']) ? $_POST['add_dimension_value'] : '';
        if (isset($_GET['errorMsg'])) {
            self::error_message('error', 'User does not have sufficient permissions for this account');
        }
        if (isset($_POST['add_property'])) {
            $gawd_account_select = isset($_POST['gawd_account_select']) ? $_POST['gawd_account_select'] : '';
            $gawd_property_name = isset($_POST['gawd_property_name']) ? $_POST['gawd_property_name'] : '';
            if ($gawd_account_select && $gawd_property_name) {
                $err_msg = $gawd_client->add_webproperty($gawd_account_select, $gawd_property_name);
                $redirect_url = admin_url() . 'admin.php?page=gawd_tracking&enableTracking=1';
                if ($err_msg) {
                    $redirect_url .= '&errorMsg=1';
                }
                echo '<script>window.location.href="' . $redirect_url . '";</script>';
            }
        }
        if (isset($_POST['lock_property'])) {
            $property = $gawd_client->property_exists();
            $gawd_property_select = $_POST['gawd_property_select'];
            foreach ($property as $property_select) {
                if ($property_select['id'] == $gawd_property_select) {
                    $property = $property_select;
                    break;
                }
            }
            $gawd_user_data['webPropertyId'] = $property['id'];
            $gawd_user_data['default_webPropertyId'] = $property['id'];
            $gawd_user_data['accountId'] = $property['accountId'];
            $gawd_user_data['default_accountId'] = $property['accountId'];
            $gawd_user_data['gawd_id'] = $property['defaultProfileId'];
            update_option('gawd_user_data', $gawd_user_data);
        }
        if ($this->manage_ua_code_selection_tracking() != 'done') {
            $redirect_url = admin_url() . 'admin.php?page=gawd_tracking';
            //echo '<script>window.location.href="'.$redirect_url.'";</script>';
            return;
        }
        $gawd_settings = get_option('gawd_settings');
        if ($add_dimension_value == 'add_dimension_Logged_in') {
            $id = isset($_POST['gawd_custom_dimension_id']) ? ($_POST['gawd_custom_dimension_id'] + 1) : 1;
            $gawd_client->add_custom_dimension('Logged in', $id);
            $settings = get_option('gawd_settings');
            $optname = 'gawd_custom_dimension_Logged_in';
            $settings[$optname] = isset($_POST['gawd_tracking_enable']) ? $_POST['gawd_tracking_enable'] : '';
            update_option('gawd_settings', $settings);
        }
        if ($add_dimension_value == 'add_dimension_Post_type') {
            $id = isset($_POST['gawd_custom_dimension_id']) ? ($_POST['gawd_custom_dimension_id'] + 1) : 1;
            $gawd_client->add_custom_dimension('Post type', $id);
            $settings = get_option('gawd_settings');
            $optname = 'gawd_custom_dimension_Post_type';
            $settings[$optname] = isset($_POST['gawd_tracking_enable']) ? $_POST['gawd_tracking_enable'] : '';
            update_option('gawd_settings', $settings);
        }
        if ($add_dimension_value == 'add_dimension_Author') {
            $id = isset($_POST['gawd_custom_dimension_id']) ? ($_POST['gawd_custom_dimension_id'] + 1) : 1;
            $gawd_client->add_custom_dimension('Author', $id);
            $settings = get_option('gawd_settings');
            $optname = 'gawd_custom_dimension_Author';
            $settings[$optname] = isset($_POST['gawd_tracking_enable']) ? $_POST['gawd_tracking_enable'] : '';
            update_option('gawd_settings', $settings);
        }
        if ($add_dimension_value == 'add_dimension_Category') {
            $id = isset($_POST['gawd_custom_dimension_id']) ? ($_POST['gawd_custom_dimension_id'] + 1) : 1;
            $gawd_client->add_custom_dimension('Category', $id);
            $settings = get_option('gawd_settings');
            $optname = 'gawd_custom_dimension_Category';
            $settings[$optname] = isset($_POST['gawd_tracking_enable']) ? $_POST['gawd_tracking_enable'] : '';
            update_option('gawd_settings', $settings);
        }
        if ($add_dimension_value == 'add_dimension_Published_Month') {
            $id = isset($_POST['gawd_custom_dimension_id']) ? ($_POST['gawd_custom_dimension_id'] + 1) : 1;
            $gawd_client->add_custom_dimension('Published Month', $id);
            $settings = get_option('gawd_settings');
            $optname = 'gawd_custom_dimension_Published_Month';
            $settings[$optname] = isset($_POST['gawd_tracking_enable']) ? $_POST['gawd_tracking_enable'] : '';
            update_option('gawd_settings', $settings);
        }
        if ($add_dimension_value == 'add_dimension_Published_Year') {
            $id = isset($_POST['gawd_custom_dimension_id']) ? ($_POST['gawd_custom_dimension_id'] + 1) : 1;
            $gawd_client->add_custom_dimension('Published Year', $id);
            $settings = get_option('gawd_settings');
            $optname = 'gawd_custom_dimension_Published_Year';
            $settings[$optname] = isset($_POST['gawd_tracking_enable']) ? $_POST['gawd_tracking_enable'] : '';
            update_option('gawd_settings', $settings);
        }
        if ($add_dimension_value == 'add_dimension_Tags') {
            $id = isset($_POST['gawd_custom_dimension_id']) ? ($_POST['gawd_custom_dimension_id'] + 1) : 1;
            $gawd_client->add_custom_dimension('Tags', $id);
            $settings = get_option('gawd_settings');
            $optname = 'gawd_custom_dimension_Tags';
            $settings[$optname] = isset($_POST['gawd_tracking_enable']) ? $_POST['gawd_tracking_enable'] : '';
            update_option('gawd_settings', $settings);
        }
        if (isset($_POST['settings_submit'])) {
            check_admin_referer('gawd_save_form', 'gawd_save_form_fild');
            $gawd_user_data = get_option('gawd_user_data');
            $gawd_file_formats = isset($_POST['gawd_file_formats']) ? sanitize_text_field($_POST['gawd_file_formats']) : '';
            $gawd_anonymize = isset($_POST['gawd_anonymize']) ? sanitize_text_field($_POST['gawd_anonymize']) : '';
            $gawd_tracking_enable = isset($_POST['gawd_tracking_enable']) ? sanitize_text_field($_POST['gawd_tracking_enable']) : '';
            $gawd_outbound = isset($_POST['gawd_outbound']) ? sanitize_text_field($_POST['gawd_outbound']) : '';
            $gawd_enhanced = isset($_POST['gawd_enhanced']) ? sanitize_text_field($_POST['gawd_enhanced']) : '';
            $enable_custom_code = isset($_POST['enable_custom_code']) ? $_POST['enable_custom_code'] : '';
            $custom_code = isset($_POST['gawd_custom_code']) ? stripslashes($_POST['gawd_custom_code']) : '';
            if ($add_dimension_value == '') {
                $gawd_cd_Logged_in = isset($_POST['gawd_custom_dimension_Logged_in']) ? sanitize_text_field($_POST['gawd_custom_dimension_Logged_in']) : '';
                $gawd_cd_Post_type = isset($_POST['gawd_custom_dimension_Post_type']) ? sanitize_text_field($_POST['gawd_custom_dimension_Post_type']) : '';
                $gawd_cd_Author = isset($_POST['gawd_custom_dimension_Author']) ? sanitize_text_field($_POST['gawd_custom_dimension_Author']) : '';
                $gawd_cd_Category = isset($_POST['gawd_custom_dimension_Category']) ? sanitize_text_field($_POST['gawd_custom_dimension_Category']) : '';
                $gawd_cd_Published_Month = isset($_POST['gawd_custom_dimension_Published_Month']) ? sanitize_text_field($_POST['gawd_custom_dimension_Published_Month']) : '';
                $gawd_cd_Published_Year = isset($_POST['gawd_custom_dimension_Published_Year']) ? sanitize_text_field($_POST['gawd_custom_dimension_Published_Year']) : '';
                $gawd_cd_Tags = isset($_POST['gawd_custom_dimension_Tags']) ? sanitize_text_field($_POST['gawd_custom_dimension_Tags']) : '';
                $gawd_settings['gawd_custom_dimension_Logged_in'] = $gawd_cd_Logged_in;
                $gawd_settings['gawd_custom_dimension_Post_type'] = $gawd_cd_Post_type;
                $gawd_settings['gawd_custom_dimension_Author'] = $gawd_cd_Author;
                $gawd_settings['gawd_custom_dimension_Category'] = $gawd_cd_Category;
                $gawd_settings['gawd_custom_dimension_Published_Month'] = $gawd_cd_Published_Month;
                $gawd_settings['gawd_custom_dimension_Published_Year'] = $gawd_cd_Published_Year;
                $gawd_settings['gawd_custom_dimension_Tags'] = $gawd_cd_Tags;
            }
            $gawd_excluded_roles = isset($_POST['gawd_excluded_roles']) ? $_POST['gawd_excluded_roles'] : array();
            $gawd_excluded_users = isset($_POST['gawd_excluded_users']) ? $_POST['gawd_excluded_users'] : array();
            $gawd_settings['gawd_file_formats'] = $gawd_file_formats;
            $gawd_settings['gawd_anonymize'] = $gawd_anonymize;
            $gawd_settings['gawd_file_formats'] = $gawd_file_formats;
            $gawd_settings['gawd_tracking_enable'] = $gawd_tracking_enable;
            $gawd_settings['gawd_outbound'] = $gawd_outbound;
            $gawd_settings['gawd_enhanced'] = $gawd_enhanced;
            $gawd_settings['gawd_excluded_roles'] = $gawd_excluded_roles;
            $gawd_settings['gawd_excluded_users'] = $gawd_excluded_users;
            $gawd_settings['enable_custom_code'] = $enable_custom_code;
            $gawd_settings['gawd_custom_code'] = $custom_code;
            update_option('gawd_settings', $gawd_settings);
            add_option("gawd_save_tracking", 1);
        }
        if (get_option('gawd_save_tracking') == 1) {
            $this->gawd_admin_notice('Your changes have been saved successfully.', 'success is-dismissible');
        }
        delete_option('gawd_save_tracking');
        if ($add_dimension_value != '') {
            $redirect_url = admin_url() . 'admin.php?page=gawd_tracking';
            echo '<script>window.location.href="' . $redirect_url . '";</script>';
        }
        require_once('admin/pages/tracking.php');
    }

    public function gawd_my_schedule($schedules)
    {
        $schedules['gawd_weekly'] = array(
            'interval' => 604800,
            'display' => __('Every week')
        );
        $schedules['gawd_monthly'] = array(
            'interval' => 18748800,
            'display' => __('Every month')
        );
        return $schedules;
    }

    public function gawd_pushover_api($user_key, $metric, $condition, $value)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.pushover.net/1/messages.json");
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            "token" => "aJBDhTfhR87EaTzs7wpx1MMKwboBjB",
            "user" => $user_key,
            "message" => 'The ' . $metric . ' less ' . $value
        ));
        // curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
        curl_exec($ch);
        curl_close($ch);
    }

    public function gawd_pushover_daily()
    {
        require_once(GAWD_DIR . '/admin/gawd_google_class.php');
        $gawd_client = GAWD_google_client::get_instance();
        $pushovers = get_option('gawd_pushovers');
        $data = '';
        $condition = '';
        foreach ($pushovers as $pushover) {
            if (isset($pushover['period']) && $pushover['period'] == 'daily') {
                //pls send email if ....
                $date = date('Y-m-d', strtotime('yesterday'));
                $data = $gawd_client->get_data_alert('ga:' . $pushover['metric'], 'date', $date, $date, $pushover['pushover_view']);
                $pushover_condition = $pushover['condition'] == 'greater' ? '>' : '<';
                if (!eval($data . $pushover_condition . $pushover['value'] . ';')) {
                    $cond = ' ' . $pushover['condition'] . ' than';
                    $this->gawd_pushover_api($pushover['user_key'], $pushover['metric'], $pushover['condition'], $pushover['value']);
                }
            }
        }
    }

    public function gawd_pushover_weekly()
    {
        require_once(GAWD_DIR . '/admin/gawd_google_class.php');
        $gawd_client = GAWD_google_client::get_instance();
        $pushovers = get_option('gawd_pushovers');
        $data = '';
        $condition = '';
        foreach ($pushovers as $pushover) {
            if (isset($pushover['period']) && $pushover['period'] == 'gawd_weekly') {
                //pls send email if ....
                $start_date = date('Y-m-d', strtotime('last week -1 day'));
                $end_date = date('l') != 'Sunday' ? date('Y-m-d', strtotime('last sunday -1 day')) : date('Y-m-d', strtotime('-1 day'));
                $data = $gawd_client->get_data_alert('ga:' . $pushover['metric'], 'date', $start_date, $end_date, $pushover['pushover_view']);
                $pushover_condition = $pushover['condition'] == 'greater' ? '>' : '<';
                if (!eval($data . $pushover_condition . $pushover['value'] . ';')) {
                    $cond = ' ' . $pushover['condition'] . ' than';
                    $this->gawd_pushover_api($pushover['user_key'], $pushover['metric'], $pushover['condition'], $pushover['value']);
                }
            }
        }
    }

    public function gawd_pushover_monthly()
    {
        require_once(GAWD_DIR . '/admin/gawd_google_class.php');
        $gawd_client = GAWD_google_client::get_instance();
        $pushovers = get_option('gawd_pushovers');
        $data = '';
        $condition = '';
        foreach ($pushovers as $pushover) {
            if (isset($pushover['period']) && $pushover['period'] == 'gawd_monthly') {
                //pls send email if ....
                $end_date = date('Y-m-t', strtotime('last month'));
                $start_date = date('Y-m-01', strtotime('last month'));
                $data = $gawd_client->get_data_alert('ga:' . $pushover['metric'], 'date', $start_date, $end_date, $pushover['pushover_view']);
                $pushover_condition = $pushover['condition'] == 'greater' ? '>' : '<';
                if (!eval($data . $pushover_condition . $pushover['value'] . ';')) {
                    $cond = ' ' . $pushover['condition'] . ' than';
                    $this->gawd_pushover_api($pushover['user_key'], $pushover['metric'], $pushover['condition'], $pushover['value']);
                }
            }
        }
    }

    public function gawd_alert_daily()
    {
        require_once(GAWD_DIR . '/admin/gawd_google_class.php');
        $gawd_client = GAWD_google_client::get_instance();
        $alerts = get_option('gawd_alerts');
        $data = '';
        $condition = '';
        $email_from = get_option('admin_email');
        foreach ($alerts as $alert) {
            if (isset($alert['period']) && $alert['period'] == 'daily') {
                //pls send email if ....
                $date = date('Y-m-d', strtotime('yesterday'));
                $data = $gawd_client->get_data_alert('ga:' . $alert['metric'], 'date', $date, $date, $alert['alert_view']);
                $alert_condition = $alert['condition'] == 'greater' ? '>' : '<';
                $color_condition = $alert['condition'] == 'greater' ? 'rgb(157, 207, 172)' : 'rgb(251, 133, 131)';
                if (!eval($data . $alert_condition . $alert['value'] . ';')) {
                    $cond = ' ' . $alert['condition'] . ' than';
                    $headers = array();
                    $headers[] = 'From: <' . $email_from . '>';
                    $headers[] = 'Content-Type: text/html';
                    $content = '<div style="font-family: sans-serif;width:100%;height:50px;background-color:#FB8583;font-size:20px;color:#fff;margin-bottom:20px;text-align:center;line-height:50px">Google Analytics WD Alert!</div><p style="color:#808080;text-align: center;font-size: 26px;font-family: sans-serif;">' . preg_replace('!\s+!', ' ', trim(ucfirst(preg_replace('/([A-Z])/', ' $1', $alert['metric'])))) . ' in <a style="text-decoration:none;color:rgba(124,181,216,1);font-family: sans-serif;" href="' . $alert["alert_view_name"] . '" target="_blank">' . $alert["alert_view_name"] . '</a> are <span style="color:' . $color_condition . '">' . $cond . '</span></p><p style="color:rgba(124,181,216,1);font-size: 26px;font-family: sans-serif; text-align: center;">' . $alert['value'] . '</p>';
                    wp_mail($alert['emails'], 'Analytics Alert', $content, $headers);
                }
            }
        }
    }

    public function gawd_alert_weekly()
    {
        require_once(GAWD_DIR . '/admin/gawd_google_class.php');
        $gawd_client = GAWD_google_client::get_instance();
        $alerts = get_option('gawd_alerts');
        $data = '';
        $condition = '';
        $email_from = get_option('admin_email');
        foreach ($alerts as $alert) {
            if (isset($alert['period']) && $alert['period'] == 'gawd_weekly') {
                //pls send email if ....
                $start_date = date('Y-m-d', strtotime('last week -1 day'));
                $end_date = date('l') != 'Sunday' ? date('Y-m-d', strtotime('last sunday -1 day')) : date('Y-m-d', strtotime('-1 day'));
                $data = $gawd_client->get_data_alert('ga:' . $alert['metric'], 'date', $start_date, $end_date, $alert['alert_view']);
                $alert_condition = $alert['condition'] == 'greater' ? '>' : '<';
                if (!eval($data . $alert_condition . $alert['value'] . ';')) {
                    $cond = ' ' . $alert['condition'] . ' than';
                    $headers = array();
                    $headers[] = 'From: <' . $email_from . '>';
                    $headers[] = 'Content-Type: text/html';
                    $content = '<div style="font-family: sans-serif;width:100%;height:50px;background-color:#FB8583;font-size:20px;color:#fff;margin-bottom:20px;text-align:center;line-height:50px">Google Analytics WD Alert!</div><p style="color:#808080;text-align: center;font-size: 26px;font-family: sans-serif;">' . preg_replace('!\s+!', ' ', trim(ucfirst(preg_replace('/([A-Z])/', ' $1', $alert['metric'])))) . ' in <a style="text-decoration:none;color:rgba(124,181,216,1);font-family: sans-serif;" href="' . $alert["alert_view_name"] . '" target="_blank">' . $alert["alert_view_name"] . '</a> are <span style="color:' . $color_condition . '">' . $cond . '</span></p><p style="color:rgba(124,181,216,1);font-size: 26px;font-family: sans-serif; text-align: center;">' . $alert['value'] . '</p>';
                    wp_mail($alert['emails'], 'Analytics Alert', $content, $headers);
                }
            }
        }
    }

    public function gawd_alert_monthly()
    {
        require_once(GAWD_DIR . '/admin/gawd_google_class.php');
        $gawd_client = GAWD_google_client::get_instance();
        $alerts = get_option('gawd_alerts');
        $data = '';
        $email_from = get_option('admin_email');
        foreach ($alerts as $alert) {
            if (isset($alert['period']) && $alert['period'] == 'gawd_monthly') {
                //pls send email if ....
                $end_date = date('Y-m-t', strtotime('last month'));
                $start_date = date('Y-m-01', strtotime('last month'));
                $data = $gawd_client->get_data_alert('ga:' . $alert['metric'], 'date', $start_date, $end_date, $alert['alert_view']);
                $alert_condition = $alert['condition'] == 'greater' ? '>' : '<';
                if (!eval($data . $alert_condition . $alert['value'] . ';')) {
                    $cond = ' ' . $alert['condition'] . ' than';
                    $headers = array();
                    $headers[] = 'From: <' . $email_from . '>';
                    $headers[] = 'Content-Type: text/html';
                    $content = '<div style="font-family: sans-serif;width:100%;height:50px;background-color:#FB8583;font-size:20px;color:#fff;margin-bottom:20px;text-align:center;line-height:50px">Google Analytics WD Alert!</div><p style="color:#808080;text-align: center;font-size: 26px;font-family: sans-serif;">' . preg_replace('!\s+!', ' ', trim(ucfirst(preg_replace('/([A-Z])/', ' $1', $alert['metric'])))) . ' in <a style="text-decoration:none;color:rgba(124,181,216,1);font-family: sans-serif;" href="' . $alert["alert_view_name"] . '" target="_blank">' . $alert["alert_view_name"] . '</a> are <span style="color:' . $color_condition . '">' . $cond . '</span></p><p style="color:rgba(124,181,216,1);font-size: 26px;font-family: sans-serif; text-align: center;">' . $alert['value'] . '</p>';
                    wp_mail($alert['emails'], 'Analytics Alert', $content, $headers);
                }
            }
        }
    }

    public function wd_dashboard_widget()
    {
        global $gawd_client, $gawd_user_data;
        $gawd_client = GAWD_google_client::get_instance();
        $profiles = $gawd_client->get_profiles();
        $gawd_user_data = get_option('gawd_user_data');
        if (isset($_POST['gawd_id'])) {
            $gawd_user_data['gawd_id'] = isset($_POST['gawd_id']) ? $_POST['gawd_id'] : '';
            foreach ($gawd_user_data['gawd_profiles'] as $web_property_name => $web_property) {
                foreach ($web_property as $profile) {
                    if ($profile['id'] == $gawd_user_data['gawd_id']) {
                        $gawd_user_data['web_property_name'] = $web_property_name;
                        $gawd_user_data['webPropertyId'] = $profile['webPropertyId'];
                        $gawd_user_data['accountId'] = $profile['accountId'];
                    }
                }
            }
            $gawd_user_data['web_property_name'] = isset($_POST['web_property_name']) ? $_POST['web_property_name'] : '';
            update_option('gawd_user_data', $gawd_user_data);
        }
        require_once('admin/pages/dashboard_widget.php');
    }

    public function google_analytics_wd_dashboard_widget()
    {
        $gawd_settings = get_option('gawd_settings');
        $gawd_backend_roles = isset($gawd_settings['gawd_backend_roles']) ? $gawd_settings['gawd_backend_roles'] : array();
        $roles = $this->get_current_user_role();
        if (isset($gawd_settings['gawd_show_in_dashboard']) && $gawd_settings['gawd_show_in_dashboard'] == 'on') {
            if (in_array($roles, $gawd_backend_roles) || current_user_can('manage_options')) {
                wp_add_dashboard_widget('wd_dashboard_widget', 'WD Google Analytics', array(
                    $this,
                    'wd_dashboard_widget'
                ));
            }
        }
    }

    public function show_data($params = array())
    {
        /*         if (isset($_REQUEST['security'])) {

                    check_ajax_referer('gawd_admin_page_nonce', 'security');

                } else {

                    check_admin_referer('gawd_save_form', 'gawd_save_form_fild');

                } */
        require_once(GAWD_DIR . '/admin/gawd_google_class.php');
        $return = true;
        if ($params == '') {
            $params = $_POST;
            $return = false;
        }
        $gawd_client = GAWD_google_client::get_instance();
        $start_date = isset($params["start_date"]) && $params["start_date"] != '' ? $params["start_date"] : date('Y-m-d', strtotime('-7 days'));
        $end_date = isset($params["end_date"]) && $params["end_date"] != '' ? $params["end_date"] : date('Y-m-d');
        $metric = isset($params["metric"]) ? $params["metric"] : 'ga:sessions';
        $metric = is_array($metric) ? count($metric) > 1 ? implode(",", $metric) : $metric[0] : $metric;
        $dimension = isset($params["dimension"]) ? $params["dimension"] : 'date';
        $country_filter = isset($params["country_filter"]) ? $params["country_filter"] : '';
        $geo_type = isset($params["geo_type"]) ? $params["geo_type"] : '';
        $filter_type = isset($params["filter_type"]) && $params["filter_type"] != '' ? $params["filter_type"] : '';
        $custom = isset($params["custom"]) && $params["custom"] != '' ? $params["custom"] : '';
        $same_dimension = $dimension;
        $dimension = $filter_type != '' && $dimension == 'date' ? $filter_type : $dimension;
        if ($dimension == 'week' || $dimension == 'month') {
            $same_dimension = $dimension;
        }
        $timezone = isset($params["timezone"]) && $params["timezone"] != '' ? $params["timezone"] : 0;
        if ($dimension == 'pagePath' || $dimension == 'PagePath' || $dimension == 'landingPagePath' || $dimension == 'LandingPagePath') {
            if (get_transient('gawd-' . $gawd_client->get_profile_id() . '-' . $dimension . '-' . $start_date . '-' . $end_date)) {
                $grid_data = get_transient('gawd-' . $gawd_client->get_profile_id() . '-' . $dimension . '-' . $start_date . '-' . $end_date);
            } else {
                $grid_data = $gawd_client->get_page_data($dimension, $start_date, $end_date, $timezone);
            }
            if ($return) {
                return $grid_data;
            }
            echo $grid_data;
            die();
        } elseif ($dimension == 'goals') {
            if (get_transient('gawd-' . $gawd_client->get_profile_id() . '-' . $dimension . '-' . $start_date . '-' . $end_date)) {
                $goal_data = get_transient('gawd-' . $gawd_client->get_profile_id() . '-' . $dimension . '-' . $start_date . '-' . $end_date);
            } else {
                $goal_data = $gawd_client->get_goal_data('date', $start_date, $end_date, $timezone, $same_dimension);
            }
            if ($return) {
                return $goal_data;
            }
            echo $goal_data;
            die();
        } elseif ( $custom == '' && (( $dimension == 'region' || $dimension == 'city' ) || ( $dimension == 'Region' || $dimension == 'City' )) ) {
            if (get_transient('gawd-' . $gawd_client->get_profile_id() . '-' . $country_filter . '-' . $start_date . '-' . $end_date)) {
                $chart_data = get_transient('gawd-' . $gawd_client->get_profile_id() . '-' . $country_filter . '-' . $start_date . '-' . $end_date);
            } else {
                $chart_data = $gawd_client->get_country_data($metric, $dimension, $start_date, $end_date, $country_filter, $geo_type, $timezone);
            }
            if ($return) {
                return $chart_data;
            }
            echo $chart_data;
            die();
        } else {
            if ($custom != '') {
                $chart_data = $gawd_client->get_data($metric, $dimension, $start_date, $end_date, $filter_type, $timezone, $same_dimension);
if ( $return ) {
						return $chart_data;
			}
            } else {
                if ($dimension == 'siteSpeed') {
                    if (get_transient('gawd-' . $gawd_client->get_profile_id() . '-' . $same_dimension . '_' . $filter_type . '-' . $start_date . '-' . $end_date)) {
                        $chart_data = get_transient('gawd-' . $gawd_client->get_profile_id() . '-' . $dimension . '-' . $start_date . '-' . $end_date);
                    } else {
                        $chart_data = $gawd_client->get_data($metric, $dimension, $start_date, $end_date, $filter_type, $timezone, $same_dimension);
                    }
                    if ($return) {
                        return $chart_data;
                    }
                } else {
                    /*  if (get_transient('gawd-' . $gawd_client->get_profile_id() . '-' . $dimension . '-' . $start_date . '-' . $end_date)) {

                         $chart_data = get_transient('gawd-' . $gawd_client->get_profile_id() . '-' . $dimension . '-' . $start_date . '-' . $end_date);

                     }  */
                    //else {
                    $chart_data = $gawd_client->get_data($metric, $dimension, $start_date, $end_date, $filter_type, $timezone, $same_dimension);
                    //}
                    if ($return) {
                        return $chart_data;
                    }
                }
            }
            echo $chart_data;
            die();
        }
    }

    public function show_data_compact()
    {
        check_ajax_referer('gawd_admin_page_nonce', 'security');
        require_once(GAWD_DIR . '/admin/gawd_google_class.php');
        $gawd_client = GAWD_google_client::get_instance();
        $start_date = isset($_POST["start_date"]) && $_POST["start_date"] != '' ? $_POST["start_date"] : date('Y-m-d', strtotime('-30 days'));
        $end_date = isset($_POST["end_date"]) && $_POST["end_date"] != '' ? $_POST["end_date"] : date('Y-m-d');
        $metric = isset($_POST["metric"]) ? $_POST["metric"] : 'sessions';
        $metric = is_array($metric) ? count($metric) > 1 ? implode(",", $metric) : $metric[0] : 'ga:' . $metric;
        $dimension = isset($_POST["dimension"]) ? $_POST["dimension"] : 'date';
        $timezone = isset($_POST["timezone"]) ? $_POST["timezone"] : 0;
        if (get_transient('gawd-compact-' . $gawd_client->get_profile_id() . '-' . $dimension . '-' . $start_date . '-' . $end_date)) {
            $chart_data = get_transient('gawd-compact-' . $gawd_client->get_profile_id() . '-' . $dimension . '-' . $start_date . '-' . $end_date);
        } else {
            $chart_data = $gawd_client->get_data_compact($metric, $dimension, $start_date, $end_date, $timezone);
        }
        echo $chart_data;
        die();
    }

    public function show_page_post_data()
    {
        check_ajax_referer('gawd_admin_page_nonce', 'security');
        require_once(GAWD_DIR . '/admin/gawd_google_class.php');
        $gawd_client = GAWD_google_client::get_instance();
        $start_date = isset($_POST["start_date"]) && $_POST["start_date"] != '' ? $_POST["start_date"] : date('Y-m-d', strtotime('-30 days'));
        $end_date = isset($_POST["end_date"]) && $_POST["end_date"] != '' ? $_POST["end_date"] : date('Y-m-d');
        $metric = isset($_POST["metric"]) ? $_POST["metric"] : 'ga:sessions';
        $metric = is_array($metric) ? count($metric) > 1 ? implode(",", $metric) : $metric[0] : $metric;
        $dimension = isset($_POST["dimension"]) ? $_POST["dimension"] : 'date';
        $timezone = isset($_POST["timezone"]) ? $_POST["timezone"] : 0;
        if(isset($_POST["filter"])){
           if(ctype_digit($_POST["filter"])){
              $uri_parts = explode( '/', get_permalink( $_POST["filter"] ), 4 );
              if ( isset( $uri_parts[3] ) ) {
                $uri = '/' . $uri_parts[3];
              }
              $uri = explode( '/',$uri);
              end($uri);      
              $key = key($uri);
              $uri = '/' . $uri[$key-1];
              $filter = rawurlencode( rawurldecode( $uri ) );
           }
           else {
             $filter = substr($_POST["filter"], 1);
           }
        }
        else {
          $filter = '';
        }
        $chart = isset($_POST["chart"]) ? $_POST["chart"] : '';
        $chart_data = get_transient('gawd-page-post-' . $gawd_client->get_profile_id() . '-' . $filter . '-' . '-' . $dimension . '-' . $start_date . '-' . $end_date . '-' . $chart);
        if (!$chart_data) {
            $chart_data = $gawd_client->get_post_page_data($metric, $dimension, $start_date, $end_date, $filter, $timezone, $chart);
        }
        echo $chart_data;
        die();
    }

    public function get_realtime()
    {
        check_ajax_referer('gawd_admin_page_nonce', 'security');
        require_once(GAWD_DIR . '/admin/gawd_google_class.php');
        $gawd_client = GAWD_google_client::get_instance();
        $chart_data = get_transient('gawd-real' . $gawd_client->get_profile_id());
        if (!$chart_data) {
            $chart_data = $gawd_client->gawd_realtime_data();
        }
        return $chart_data;
    }

    public static function add_dashboard_menu()
    {
        $get_custom_reports = get_option('gawd_custom_reports');
        if (!$get_custom_reports) {
            $custom_report = array();
        } else {
            foreach ($get_custom_reports as $name => $report) {
                $custom_report['custom_report_' . $name] = __($name, "gawd");
            }
        }
        $tabs = array(
            "general" => array(
                "title" => __("Audience", "gawd"),
                "childs" => array(),
                "desc" => "Report of your website audience. Provides details about new and returning users of your website, sessions, bounces, pageviews and session durations."
            ),
            "realtime" => array(
                "title" => __("Real Time", "gawd"),
                "childs" => array(),
                "desc" => "Real Time statistics show the number of active users currently visiting your website pages."
            ),
            "Pro" => array(
                "title" => __("Available in pro", "gawd"),
                "childs" => array(),
                "desc" => ""
            ),
            "demographics" => array(
                "title" => __("Demographics", "gawd"),
                "childs" => array(
                    "userGender" => __("User Gender", "gawd"),
                    "userAge" => __("User Age", "gawd")
                ),
                "desc" => "Demographics display tracking statistics of your website users based on their age and gender. "
            ),
            "interests" => array(
                "title" => __("Interests", "gawd"),
                "childs" => array(
                    "inMarket" => __("In-Market Segment", "gawd"),
                    "affinityCategory" => __("Affinity Category", "gawd"),
                    "otherCategory" => __("Other Category", "gawd")
                ),
                "desc" => "Provides tracking information about site users depending on Affinity Categories (e.g. Music Lovers or Mobile Enthusiasts), In-Market Segments (based on online product purchase interests) and Other Categories (most specific identification, for example, tennis lovers among Sports Fans)."
            ),
            "geo" => array(
                "title" => __("GEO", "gawd"),
                "childs" => array(
                    "location" => __("Location", "gawd"),
                    "language" => __("Language", "gawd")
                ),
                "desc" => "Geo-identifier report is built from interactions of location (countries, cities) and language of your website users."
            ),
            "behavior" => array(
                "title" => __("Behavior", "gawd"),
                "childs" => array(
                    "behaviour" => __("New vs Returning", "gawd"),
                    "engagement" => __("Engagement", "gawd")
                ),
                "desc" => "Compares number of New visitors and Returning users of your website in percents. You can check the duration of sessions with Engagement report."
            ),
            "technology" => array(
                "title" => __("Technology", "gawd"),
                "childs" => array(
                    "os" => __("OS", "gawd"),
                    "browser" => __("Browser", "gawd")
                ),
                "desc" => "Identifies tracking of the site based on operating systems and browsers visitors use."
            ),
            "mobile" => array(
                "title" => __("Mobile", "gawd"),
                "childs" => array(
                    "device_overview" => __("Overview", "gawd"),
                    "devices" => __("Devices", "gawd")
                ),
                "desc" => "Shows statistics of mobile and desktop devices visitors have used while interacting with your website."
            ),
            "custom" => array(
                "title" => __("Custom Dimensions", "gawd"),
                "childs" => array(),
                "desc" => "Set up Custom Dimensions based on Users, Post type, Author, Category, Publication date and Tags in Custom Dimensions page, and view their report in this tab."
            ),
            "trafficSource" => array(
                "title" => __("Traffic Source", "gawd"),
                "childs" => array(),
                "desc" => "Displays overall graph of traffic sources directing to your website."
            ),
            "adWords" => array(
                "title" => __("AdWords", "gawd"),
                "childs" => array(),
                "desc" => "If your website is registered on Google AdWords, you can link its Google Analytics to AdWords, and gather relevant tracking information with this report."
            ),
            /*             "pagePath" => array(

                            "title" => __("Pages", "gawd"),

                            "childs" => array(),

                            "desc" => "Pages report table will provide you information about Bounces, Entrances, Pageviews, Unique Pageviews, time spent on pages, Exits and Average page loading time."

                        ), */
            "siteContent" => array(
                "title" => __("Site Content", "gawd"),
                "childs" => array(
                    "pagePath" => __("All Pages", "gawd"),
                    "landingPagePath" => __("Landing Pages", "gawd"),
                ),
                "desc" => "Pages report table will provide you information about Bounces, Entrances, Pageviews, Unique Pageviews, time spent on pages, Exits and Average page loading time."
            ),
            "siteSpeed" => array(
                "title" => __("Site Speed", "gawd"),
                "childs" => array(),
                "desc" => "Shows the average load time of your website users experienced during specified date range."
            ),
            "events" => array(
                "title" => __("Events", "gawd"),
                "childs" => array(
                    "eventsLabel" => __("Events by Label", "gawd"),
                    "eventsAction" => __("Events by Action", "gawd"),
                    "eventsCategory" => __("Events by Category", "gawd")
                ),
                "desc" => "Displays the report based on Events you set up on Google Analytics of your website. Graphs are built based on Event Labels, Categories and Actions."
            ),
            "goals" => array(
                "title" => __("Goals", "gawd"),
                "childs" => array(),
                "desc" => "Set Goals from Goal Management and review their Google Analytics reports under this tab."
            ),
            "ecommerce" => array(
                "title" => __("Ecommerce", "gawd"),
                "childs" => array(
                    "daysToTransaction" => __("TIme to Purchase", "gawd"),
                    "transactionId" => __("Transaction ID", "gawd"),
                    "sales_performance" => __("Sales Performance", "gawd"),
                    "productSku" => __("Product Sku", "gawd"),
                    "productCategory" => __("Product Category ", "gawd"),
                    "productName" => __("Product Name", "gawd"),
                ),
                "desc" => "Check sales statistics of your website identified by revenues, transactions, products and performance."
            ),
            "adsense" => array(
                "title" => __("AdSense", "gawd"),
                "childs" => array(),
                "desc" => "Link your Google Analytics and AdSense accounts from Google Analytics Admin setting and keep track of AdSense tracking under this report."
            ),
            "customReport" => array(
                "title" => __("Custom Report", "gawd"),
                "childs" => $custom_report,
                "desc" => "Add Custom Reports from any metric and dimension in Custom Reports page, and view relevant Google Analytics tracking information in this tab."
            ),
        );
        update_option('gawd_menu_items', $tabs);
    }

    public function remove_zoom_message()
    {
        check_ajax_referer('gawd_admin_page_nonce', 'security');
        $got_it = isset($_REQUEST["got_it"]) ? sanitize_text_field($_REQUEST["got_it"]) : '';
        if ($got_it != '') {
            add_option('gawd_zoom_message', $got_it);
        }
    }

    public function nossl_message(){
        ?>
        <div class="notice notice-error">
            <p><?php _e( 'ERROR: Google Analytics WD cannot make insecure requests to Google API. Please enable PHP OpenSSL extension', 'gawd' ); ?></p>
        </div>
        <?php
    }


    public function check_property_delete(){
        global $gawd_client;
        $gawd_client = GAWD_google_client::get_instance();
        $accountId = $gawd_client->get_default_accountId();
        $webPropertyId = $gawd_client->get_default_webPropertyId();
        $gawd_user_data = get_option('gawd_user_data');
        $screen = get_current_screen();
        $accounts = $gawd_client->get_management_accounts();
         if ($webPropertyId == null && strpos($screen->base, 'gawd') !== false && !empty($accounts)) {
                    echo "<div class='notice notice-error'><p>Google Analytics WD: You haven't created a web-property with current site URL, or it has been deleted. Please <a href='" . admin_url() . "admin.php?page=gawd_tracking'>create </a> one.</p></div>";
                }
        if (strpos($screen->base, 'gawd') !== false && $accountId != null && $webPropertyId != null && $gawd_client->analytics_member->management_webproperties != null ) {
            try {
                $deleted = $gawd_client->analytics_member->management_webproperties->get($accountId, $webPropertyId);
            } 
            catch (Exception $e) {
              $gawd_user_data['webPropertyId'] = null;
              $gawd_user_data['default_webPropertyId'] = null;
              $gawd_user_data['gawd_id'] = null;
              update_option('gawd_user_data', $gawd_user_data);
              $myFile = GAWD_UPLOAD_DIR."/logfile.txt";
              $fh = fopen($myFile, 'a');
              fwrite($fh, $e->getMessage(). "----check_property_delete function".PHP_EOL);
              fclose($fh);
                if (strpos($e->getMessage(), 'not found.') !== false) {
                    echo "<div class='notice notice-error'><p>Google Analytics WD: You haven't created a web-property with current site URL, or it has been deleted. Please <a href='" . admin_url() . "admin.php?page=gawd_settings'>authenticate</a>.</p></div>";
                }
            }
        }
    }

    /**
     * Checks if the protocol is secure.
     *
     * @return boolean
     */
    public static function is_ssl()
    {
        if (isset($_SERVER['HTTPS'])) {
            if ('on' == strtolower($_SERVER['HTTPS'])) {
                return true;
            }
            if ('1' == $_SERVER['HTTPS']) {
                return true;
            }
        } elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])) {
            return true;
        }
        return false;
    }

    /**
     * Returns the Singleton instance of this class.
     *
     * @return GAWD The Singleton instance.
     */
    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Private clone method to prevent cloning of the instance of the
     * Singleton instance.
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Private unserialize method to prevent unserializing of the Singleton
     * instance.
     *
     * @return void
     */
    private function __wakeup()
    {
    }
}
