<?php

require_once(GAWD_DIR . '/google/vendor/autoload.php');

class GAWD_google_client {

    private static $instance;
    private $google_client;
    public $analytics_member;
    private $gawd_user_data;
    private static $error_message ='';

    protected function __construct() {
        $this->gawd_user_data = get_option('gawd_user_data');
        try{

            $this->google_client = new Google_Client();
            $this->set_google_client();
            $this->analytics_member = new Google_Service_Analytics($this->google_client);

        }catch(Google_Service_Exception $e){
			$errors = $e->getErrors();
            self::$error_message = $errors[0]["message"];
        }catch(Exception $e){
            $myFile = GAWD_UPLOAD_DIR."/logfile.txt"; ;
            $fh = fopen($myFile, 'a');
            fwrite($fh, $e->getMessage(). "----constructor function".PHP_EOL);
            fclose($fh);
            self::$error_message = $e->getMessage();
        }
    }

    /**
     * Sets the google class member.
     */
    private function set_google_client() {

        $access_token = isset($this->gawd_user_data['access_token']) ? $this->gawd_user_data['access_token'] : '' ;
        if($access_token != ''){
          $this->google_client->setAccessToken($access_token);

          if ($this->google_client->isAccessTokenExpired()) {
              $refresh_token = $this->gawd_user_data['refresh_token'];

              $this->google_client->setClientId(GAWD::get_instance()->get_project_client_id());
              $this->google_client->setClientSecret(GAWD::get_instance()->get_project_client_secret());
              $this->google_client->setRedirectUri(GAWD::get_instance()->redirect_uri);
              // $this->google_client->setAuthConfigFile(GAWD_DIR . '/client_secrets.json');
              $this->google_client->refreshToken($refresh_token);
          }
        }
    }

    public static function create_authentication_url() {
        $client = new Google_Client();
        // $client->setAuthConfigFile(GAWD_DIR . '/client_secrets.json');
        $client->setClientId(GAWD::get_instance()->get_project_client_id());
        $client->setClientSecret(GAWD::get_instance()->get_project_client_secret());
        $client->setRedirectUri(GAWD::get_instance()->redirect_uri);
        $client->addScope(array(Google_Service_Analytics::ANALYTICS_EDIT, Google_Service_Analytics::ANALYTICS_READONLY));
        $client->setApprovalPrompt('force');
        $client->setAccessType('offline');
        return "'" . $client->createAuthUrl() . "'";
    }

    public static function authenticate($code) {

        $client = new Google_Client();
        // $client->setAuthConfigFile(GAWD_DIR . '/client_secrets.json');
        $client->setClientId(GAWD::get_instance()->get_project_client_id());
        $client->setClientSecret(GAWD::get_instance()->get_project_client_secret());
        $client->setRedirectUri(GAWD::get_instance()->redirect_uri);
        if(isset($code) && $code != ''){
          try {
            $client->authenticate($code);

            if ($client->isAccessTokenExpired()) {
                return false;
            }
            else {
                $access_token = $client->getAccessToken();
                $refresh_token = $client->getRefreshToken();
                update_option('gawd_user_data', array(
                        'access_token' => $access_token,
                        'refresh_token' => $refresh_token
                    )
                );

                $gawd_client = self::get_instance();
                delete_transient('gawd_user_profiles');
                $profiles = $gawd_client->get_profiles();

           /*     if ($profiles instanceof Google_Service_Exception) {

                    delete_option('gawd_user_data');
                    $errors = $profiles->getErrors();
                    return $errors[0]["message"];
                }*/


                update_option('gawd_user_data', array(
                        'access_token' => $access_token,
                        'refresh_token' => $refresh_token,
                        'gawd_profiles' => $profiles,
                    )
                );
                return true;
            }
          } catch (Google_Service_Exception $e) {
              delete_option('gawd_user_data');
            $errors = $e->getErrors();
            return $errors[0]["message"];
          } catch (Exception $e) {
              $myFile = GAWD_UPLOAD_DIR."/logfile.txt"; ;
              $fh = fopen($myFile, 'a');
              fwrite($fh, $e->getMessage(). "----authenticate function".PHP_EOL);
              fclose($fh);
              return $e->getMessage();
          }
        }
       
    }

    public function get_management_accounts() {
        $accounts_light = array();
        try{
            $accounts = $this->analytics_member->management_accounts->listManagementAccounts()->getItems();

            foreach ($accounts as $account) {
                $edit_flag = FALSE;
                $permissions = $account['permissions']['effective'];
                foreach ($permissions as $permission) {
                    if ($permission == 'EDIT') {
                        $edit_flag = TRUE;
                    }
                }
                $accounts_light[] = array(
                    'name' => $account['name'],
                    'id' => $account['id'],
                    'edit_permissions' => $edit_flag
                );
                /*if ($edit_flag == TRUE) {
                    $accounts_light[] = array(
                        'name' => $account['name'],
                        'id' => $account['id']
                    );
                }*/
            }
        }catch (Google_Service_Exception $e) {
            //return $e->getErrors()[0]["message"];
        } catch (Exception $e) {
            $myFile = GAWD_UPLOAD_DIR."/logfile.txt"; ;
            $fh = fopen($myFile, 'a');
            fwrite($fh, $e->getMessage(). "----get_management_accounts function".PHP_EOL);
            fclose($fh);
            //return $e->getMessage();
        }
        return $accounts_light;
    }

    public function property_exists() {
        try{
            $web_properties = $this->analytics_member->management_webproperties->listManagementWebproperties('~all')->getItems();
        }catch(Google_Service_Exception $e){
            return 'no_matches';

        }catch(Exception $e){
            $myFile = GAWD_UPLOAD_DIR."/logfile.txt"; ;
            $fh = fopen($myFile, 'a');
            fwrite($fh, $e->getMessage(). "----property_exists function".PHP_EOL);
            fclose($fh);
            return 'no_matches';
        }
        $exact_properties = array();
        $site_url = get_site_url() . '/';

        foreach ($web_properties as $web_property) {
            $current_url = $web_property['websiteUrl'];
            if (($current_url == $site_url) || (($current_url . '/') == $site_url)) {
                $exact_properties[] = $web_property;
            }
        }
        if (!empty($exact_properties)) {
            return $exact_properties;
        } else {
            return 'no_matches';
        }
    }

    public function get_default_profiles() {
        $gawd_user_data = get_option('gawd_user_data');
        $accountId = $this->get_default_accountId();
        $webPropertyId = $this->get_default_webPropertyId();
        $webProperty = $this->analytics_member->management_webproperties->get($accountId, $webPropertyId);
        $webPropertyName = $webProperty['name'];
        $profiles = $this->analytics_member->management_profiles->listManagementProfiles($accountId, $webPropertyId)->getItems();
        $profiles_light = array();
        foreach ($profiles as $profile) {
            $profiles_light[] = array(
                'id'              => $profile['id'],
                'name'            => $profile['name'],
                'webPropertyName' => $webPropertyName
            );
        }
        return $profiles_light;
    }

    public function add_webproperty($accountId, $name) {

        $analytics = $this->analytics_member;
        $websiteUrl = get_site_url();
        try {
            $property = new Google_Service_Analytics_Webproperty();
            $property->setName($name);
            $property->setWebsiteUrl($websiteUrl);
            $analytics->management_webproperties->insert($accountId, $property);
        } catch (apiServiceException $e) {
            print 'There was an Analytics API service error '
                . $e->getCode() . ':' . $e->getMessage();
        } catch (apiException $e) {
            print 'There was a general API error '
                . $e->getCode() . ':' . $e->getMessage();
        }
        catch (Google_Service_Exception $e) {
            return 'There was a general API error '
                . $e->getCode() . ':' . $e->getMessage();
        }catch (Exception $e) {
            $myFile = GAWD_UPLOAD_DIR."/logfile.txt"; ;
            $fh = fopen($myFile, 'a');
            fwrite($fh, $e->getMessage(). "----add_webproperty function".PHP_EOL);
            fclose($fh);
            echo $e->getCode() . ':' . $e->getMessage();
        }

        $web_properties = $this->analytics_member->management_webproperties->listManagementWebproperties($accountId)->getItems();
        foreach ($web_properties as $web_property) {
            if ($web_property['name'] == $name) {
                $profile = new Google_Service_Analytics_Profile();
                $profile->setName('All Web Site Data');
                
                try {
                    $analytics->management_profiles->insert($accountId, $web_property['id'], $profile);
                } catch (apiServiceException $e) {
                    print 'There was an Analytics API service error '
                        . $e->getCode() . ':' . $e->getMessage();
                } catch (apiException $e) {
                    print 'There was a general API error '
                        . $e->getCode() . ':' . $e->getMessage();
                }
                 $current_profiles = $this->analytics_member->management_profiles->listManagementProfiles($accountId,$web_property['id'])->getItems();
                try {
                  $property = new Google_Service_Analytics_Webproperty();
                  $property->setName($name);
                  $property->setWebsiteUrl($websiteUrl);
                  $property->setDefaultProfileId($current_profiles[0]['id']);
                  $analytics->management_webproperties->update($accountId, $web_property['id'], $property);
                } catch (apiServiceException $e) {
                    print 'There was an Analytics API service error '
                        . $e->getCode() . ':' . $e->getMessage();
                } catch (apiException $e) {
                    print 'There was a general API error '
                        . $e->getCode() . ':' . $e->getMessage();
                }
                catch (Google_Service_Exception $e) {
                    return 'There was a general API error '
                        . $e->getCode() . ':' . $e->getMessage();
                }catch (Exception $e) {
                    echo $e->getCode() . ':' . $e->getMessage();
                }
            }
        }
       
    }

    /**
     * Get all the management profiles of the authenticated user.
     *
     * @return array
     */
    public function get_profiles() {
        $profiles_light = get_transient('gawd_user_profiles') ? get_transient('gawd_user_profiles') : '';
        if ($profiles_light && $profiles_light != ''){
            return $profiles_light;
        }
        $profiles_light = array();
        $gawd_user_data = get_option('gawd_user_data');
        try{

	        if($this->analytics_member && $this->analytics_member->management_webproperties) {
		        $web_properties       = $this->analytics_member->management_webproperties->listManagementWebproperties( '~all' )->getItems();
		        $profiles             = $this->analytics_member->management_profiles->listManagementProfiles( '~all', '~all' )->getItems();
		        $profiles_count       = count( $profiles );
		        $web_properties_count = count( $web_properties );
		        for ( $i = 0; $i < $web_properties_count; $i ++ ) {
			        for ( $j = 0; $j < $profiles_count; $j ++ ) {
				        if ( $web_properties[ $i ]['id'] == $profiles[ $j ]['webPropertyId'] ) {
					        $profiles_light[ $web_properties[ $i ]['name'] ][] = array(
						        'id'            => $profiles[ $j ]['id'],
						        'name'          => $profiles[ $j ]['name'],
						        'webPropertyId' => $profiles[ $j ]['webPropertyId'],
						        'websiteUrl'    => $profiles[ $j ]['websiteUrl'],
						        'accountId'     => $profiles[ $j ]['accountId']
					        );
				        }
			        }
		        }
	        }else{
		        return 'Something went wrong';
	        }
            if (!isset($gawd_user_data['gawd_id']) || $gawd_user_data['gawd_id'] == '' || $gawd_user_data['gawd_id'] == NULL) {
                if (!empty($profiles_light)) {
	                $first_profiles = reset($profiles_light);
                    $first_profile = $first_profiles[0];
                    $gawd_user_data['gawd_id'] = $first_profile['id'];
                    $gawd_user_data['webPropertyId'] = $first_profile['webPropertyId'];
                    $gawd_user_data['accountId'] = $first_profile['accountId'];
                    $gawd_user_data['web_property_name'] = $web_properties[0]['name'];
                }
            }

            $gawd_user_data['gawd_profiles'] = $profiles_light;
            update_option('gawd_user_data', $gawd_user_data);
            set_transient('gawd_user_profiles',$profiles_light, 60);
            return $profiles_light;
        }catch(Google_Service_Exception $e){
            return $e;
        }catch(Exception $e){
            $myFile = GAWD_UPLOAD_DIR."/logfile.txt"; ;
            $fh = fopen($myFile, 'a');
            fwrite($fh, $e->getMessage(). "----get_profiles function".PHP_EOL);
            fclose($fh);
            return $e;
        }
    }

    public function get_custom_dimensions($default = '') {
        $this->gawd_user_data = get_option('gawd_user_data');
        if ($default == 'default') {
            $webPropertyId = $this->get_default_webPropertyId();
            $accountId = $this->get_default_accountId();
        }
        else {
            $webPropertyId = $this->get_profile_webPropertyId();
            $accountId = $this->get_profile_accountId();
            $transient = get_transient('gawd-custom-dimensions-' . $webPropertyId);
            if ($transient) {
                if ($transient != 'no_custom_dimensions_exist') {
                    return json_decode($transient, true);
                } else {
                    return $transient;
                }
            }
        }
        try{
            $all_dimensions = $this->analytics_member->management_customDimensions->listManagementCustomDimensions($accountId, $webPropertyId)->getItems();

        }
        catch (Exception $e) {
            $myFile = GAWD_UPLOAD_DIR."/logfile.txt"; ;
            $fh = fopen($myFile, 'a');
            fwrite($fh, $e->getMessage(). "----get_custom_dimensions function".PHP_EOL);
            fclose($fh);
        }
        if (0 == sizeof($all_dimensions)) {
            if ($default == 'default') {
                update_option('gawd_custom_dimensions', "no_custom_dimensions_exist");
            }
            set_transient('gawd-custom-dimensions-' . $webPropertyId, "no_custom_dimensions_exist", 12 * HOUR_IN_SECONDS);
            return "no_custom_dimensions_exist";
        }
        else {
            foreach ($all_dimensions as $dimension) {
                $dimensions_light[] = array(
                    'name' => $dimension['name'],
                    'id' => $dimension['id']
                );
            }
            $supported_dimensions = array("Logged in", "Post type", "Author", "Category", "Tags", "Published Month", "Published Year");
            $dimensions = array();
            foreach ($dimensions_light as $dimension) {
                foreach ($supported_dimensions as $supported_dimension) {
                    if (trim(strtolower($dimension['name'])) == strtolower($supported_dimension)) {
                        $dimension['name'] = $supported_dimension;
                        $dimension['id'] = substr($dimension['id'], -1);
                        $dimensions[] = $dimension;
                    }
                }
            }
            if ($default == 'default') {
                update_option('gawd_custom_dimensions', $dimensions);
            }
            set_transient('gawd-custom-dimensions-' . $webPropertyId, json_encode($dimensions_light), 12 * HOUR_IN_SECONDS);
            return $dimensions_light;
        }
    }

    public function get_custom_dimensions_tracking() {
        $all_dimensions = get_option('gawd_custom_dimensions');
        if ($all_dimensions) {
            return $all_dimensions;
        }
        $all_dimensions = $this->get_custom_dimensions('default');
        if ($all_dimensions == 'no_custom_dimensions_exist') {
            return 'no_custom_dimensions_exist';
        }
        $supported_dimensions = array("Logged in", "Post type", "Author", "Category", "Tags", "Published Month", "Published Year");
        $dimensions = array();
        foreach ($all_dimensions as $dimension) {
            foreach ($supported_dimensions as $supported_dimension) {
                if (trim(strtolower($dimension['name'])) == strtolower($supported_dimension)) {
                    $dimension['id'] = substr($dimension['id'], -1);
                    $dimension['name'] = $supported_dimension;
                    $dimensions[] = $dimension;
                }
            }
        }
        update_option('gawd_custom_dimensions', $dimensions);
        if ($dimensions) {
            return $dimensions;
        } else {
            return "no_custom_dimensions_exist";
        }
    }

    public static function gawd_cd_logged_in() {
        $value = var_export(is_user_logged_in(), true);
        $value = $value == 'true' ? 'yes' : 'no';
        return $value;
    }

    public static function gawd_cd_post_type() {
        if (is_singular()) {
            $post_type = get_post_type(get_the_ID());

            if ($post_type) {
                return $post_type;
            }
        }
    }

    public static function gawd_cd_author() {
        if (is_singular()) {
            if (have_posts()) {
                while (have_posts()) {
                    the_post();
                }
            }
            $name = get_the_author_meta('user_nicename');
            $value = trim($name);
            return $value;
        }
    }

    public static function gawd_cd_category() {
        if (is_single()) {
            $categories = get_the_category(get_the_ID());

            if ($categories) {
                foreach ($categories as $category) {
                    $category_names[] = $category->slug;
                }

                return implode(',', $category_names);
            }
        }
    }

    public static function gawd_cd_tags() {
        if (is_single()) {
            $tag_names = 'untagged';

            $tags = get_the_tags(get_the_ID());

            if ($tags) {
                $tag_names = implode(',', wp_list_pluck($tags, 'name'));
            }

            return $tag_names;
        }
    }


    public static function gawd_cd_published_month() {
        if (is_singular()) {
            return get_the_date('M-Y');
        }
    }
    public static function gawd_cd_published_year() {
        if (is_singular()) {
            return get_the_date('Y');
        }
    }

    public function get_management_filters() {
      $analytics = $this->analytics_member;
      $this->gawd_user_data = get_option('gawd_user_data');
      $accountId = $this->get_profile_accountId();
      $profileId = $this->get_profile_id();
      $webPropertyId = $this->get_profile_webPropertyId();
      if( (isset($accountId) && $accountId != '') && (isset($webPropertyId) && $webPropertyId != '') && (isset($profileId) && $profileId != '')){
        try {
          $view_filters = $analytics->management_profileFilterLinks->listManagementProfileFilterLinks($accountId, $webPropertyId, $profileId);
          $filters = $view_filters->getItems();
          foreach ($filters as $filter) {
              $filter_info = $analytics->management_filters->get($accountId,$filter['modelData']['filterRef']['id']);
              $all_filters[] = array(
                  'name' => $filter['modelData']['filterRef']['name'],
                  'id' => $filter['modelData']['filterRef']['id'],
                  'type' => $filter_info['excludeDetails']['field'],
                  'value' => $filter_info['excludeDetails']['expressionValue'],
                  'view' => $filter['modelData']['profileRef']['name']
              );
          }
          if (isset($all_filters)) {
              return $all_filters;
          }
        } 
        catch (Exception $e) {

            $myFile = GAWD_UPLOAD_DIR."/logfile.txt"; ;
            $fh = fopen($myFile, 'a');
            fwrite($fh, $e->getMessage(). "----get_management_filters function".PHP_EOL);
            fclose($fh);

            $error = array('error_message' => 'Error');
            if (strpos($e->getMessage(), 'User does not have sufficient permissions for this ')) {
                $error['error_message'] = 'User does not have sufficient permissions for this profile';
            }
            return json_encode($error);
        }
      }
    }

    public function get_management_goals() {
      $this->gawd_user_data = get_option('gawd_user_data');
      $profileId = $this->get_profile_id();
      $accountId = $this->get_profile_accountId();
      $webPropertyId = $this->get_profile_webPropertyId();
      $goals = array();
      if(isset($profileId) && $profileId != '' && isset($webPropertyId) && $webPropertyId != ''){
        try {
            $goals = $this->analytics_member->management_goals->listManagementGoals($accountId, $webPropertyId, $profileId)->getItems();
        } 
        catch (Exception $e) {

            $myFile = GAWD_UPLOAD_DIR."/logfile.txt"; ;
            $fh = fopen($myFile, 'a');
            fwrite($fh, $e->getMessage(). "----get_management_goals function".PHP_EOL);
            fclose($fh);

        }
        if (0 == sizeof($goals)) {
            return "no_goals_exist";
        } else {
            foreach ($goals as $goal) {
                $goals_light[] = array(
                    'name' => $goal['name'],
                    'id' => $goal['id']
                );
            }
            return $goals_light;
        }
      }
    }

    public function get_default_goals() {
        $this->gawd_user_data = get_option('gawd_user_data');
        $accountId = $this->get_default_accountId();
        $webPropertyId = $this->get_default_webPropertyId();
        $goals = array();
        try {
            $goals = $this->analytics_member->management_goals->listManagementGoals($accountId, $webPropertyId, '~all')->getItems();
        } catch (Exception $e) {

            $myFile = GAWD_UPLOAD_DIR."/logfile.txt"; ;
            $fh = fopen($myFile, 'a');
            fwrite($fh, $e->getMessage(). "----get_default_goals function".PHP_EOL);
            fclose($fh);

        }
        if (0 == sizeof($goals)) {
            return "no_goals_exist";
        } else {
            $profiles = array();
            foreach ($goals as $goal) {
                $flag = false;
                foreach ($profiles as $profile) {
                    if ($profile == $goal['profileId']) {
                        $flag = true;
                    }
                }
                if ($flag == false) {
                    $profiles[] = $goal['profileId'];
                }
            }
            $goals_light = array();
            $caseSensitive = '';
            foreach ($profiles as $profile) {
                foreach ($goals as $goal) {
                    if ($goal['profileId'] == $profile) {
                        if($goal['type'] == 'URL_DESTINATION'){
                            $type = 'Destination';
                            if($goal["modelData"]['urlDestinationDetails']['matchType'] == 'EXACT'){
                                $match_type = 'Equals';
                            }
                            elseif($goal["modelData"]['urlDestinationDetails']['matchType'] == 'HEAD'){
                                $match_type = 'Begin with';
                            }
                            else{
                                $match_type = 'Regular expresion';
                            }
                            $value = $goal["modelData"]['urlDestinationDetails']['url'];
                            $caseSensitive = $goal["modelData"]['urlDestinationDetails']['caseSensitive'];
                        }
                        elseif($goal['type'] == 'VISIT_TIME_ON_SITE'){
                            $type = 'Duration';
                            if($goal["modelData"]['visitTimeOnSiteDetails']['comparisonType'] == 'GREATER_THAN'){
                                $match_type = 'Greater than';
                            }
                            $value = $goal["modelData"]['visitTimeOnSiteDetails']['comparisonValue'];
                            $hours = strlen(floor($value / 3600)) < 2 ?  '0' . floor($value / 3600) : floor($value / 3600);
                            $mins = strlen(floor($value / 60 % 60)) < 2 ? '0' . floor($value / 60 % 60) : floor($value / 60 % 60);
                            $secs = strlen(floor($value % 60)) < 2 ? '0' . floor($value % 60) : floor($value % 60);
                            $value = $hours.':'.$mins.':'.$secs;
                        }
                        else{
                            $type = 'Pages/Screens per session';
                            if($goal["modelData"]['visitNumPagesDetails']['comparisonType'] == 'GREATER_THAN'){
                                $match_type = 'Greater than';
                            }
                            $value = $goal["modelData"]['visitNumPagesDetails']['comparisonValue'];
                        }

                        $goals_light[$profile][] = array(
                            'name' => $goal['name'],
                            'id' => $goal['id'],
                            'type' => $type,
                            'match_type' => $match_type,
                            'profileID' => $goal['profileId'],
                            'caseSensitive' => $caseSensitive,
                            'value' => $value,
                        );
                    }
                }
            }
            return $goals_light;
        }
    }

    public function add_custom_dimension($name, $id) {

        $custom_dimension = new Google_Service_Analytics_CustomDimension();
        $custom_dimension->setId($id);
        $custom_dimension->setActive(TRUE);
        $custom_dimension->setScope('Hit');
        $custom_dimension->setName($name);

        $accountId = $this->get_default_accountId();
        $webPropertyId = $this->get_default_webPropertyId();
        $analytics = $this->analytics_member;
        delete_transient('gawd-custom-dimensions-' . $webPropertyId);
        try {
            $analytics->management_customDimensions->insert($accountId, $webPropertyId, $custom_dimension);
        } catch (apiServiceException $e) {
            echo 'There was an Analytics API service error '
                . $e->getCode() . ':' . $e->getMessage();
        } catch (apiException $e) {
            echo 'There was a general API error '
                . $e->getCode() . ':' . $e->getMessage();
        }

        catch (Exception $e){

            $myFile = GAWD_UPLOAD_DIR."/logfile.txt"; ;
            $fh = fopen($myFile, 'a');
            fwrite($fh, $e->getMessage(). "----add_custom_dimension function".PHP_EOL);
            fclose($fh);

            if(strpos($e->getMessage(), 'not found.') !==false){
            echo '<div class="notice notice-error"><p>Something went wrong.</p></div>';
            return 'error';
          }
        }
    }

    public function add_goal($gawd_goal_profile, $goal_max_id, $gawd_goal_type, $gawd_goal_name, $gawd_goal_comparison = "GREATER_THAN", $gawd_goal_value, $url_case_sensitve = 'false') {
        $this->gawd_user_data = get_option('gawd_user_data');
        /* This request creates a new Goal. */
        // Construct the body of the request.
        $goal = new Google_Service_Analytics_Goal();
        $goal->setId($goal_max_id); //ID
        $goal->setActive(True); //ACTIVE/INACTIVE
        $goal->setType($gawd_goal_type); //URL_DESTINATION, VISIT_TIME_ON_SITE, VISIT_NUM_PAGES, AND EVENT
        $goal->setName($gawd_goal_name); //NAME
        // Construct the time on site details.
        if ($gawd_goal_type == 'VISIT_TIME_ON_SITE') {
            $details = new Google_Service_Analytics_GoalVisitTimeOnSiteDetails();
            $details->setComparisonType($gawd_goal_comparison); //VISIT_TIME_ON_SITE -------- LESS_THAN/ GREATER_THAN------
            $details->setComparisonValue($gawd_goal_value);
            $goal->setVisitTimeOnSiteDetails($details);
        } elseif ($gawd_goal_type == 'URL_DESTINATION') {
            if($url_case_sensitve != ''){
                $url_case_sensitve = true;
            }
            $details = new Google_Service_Analytics_GoalUrlDestinationDetails();
            $details->setCaseSensitive($url_case_sensitve);
            $details->setFirstStepRequired('false');
            $details->setMatchType($gawd_goal_comparison);
            $details->setUrl($gawd_goal_value);
            $goal->setUrlDestinationDetails($details);
        } elseif ($gawd_goal_type == 'VISIT_NUM_PAGES') {
            $details = new Google_Service_Analytics_GoalVisitNumPagesDetails();
            $details->setComparisonType($gawd_goal_comparison); //VISIT_TIME_ON_SITE -------- LESS_THAN/ GREATER_THAN------
            $details->setComparisonValue($gawd_goal_value);
            $goal->setVisitNumPagesDetails($details);
        } elseif ($gawd_goal_type == 'EVENT') {
            /*     $details = new Google_Service_Analytics_GoalEventDetails();
              $details = new Google_Service_Analytics_GoalEventDetailsEventConditions();
              $detailssetComparisonType
              //$details->setEventConditions($gawd_goal_comparison);//VISIT_TIME_ON_SITE -------- LESS_THAN/ GREATER_THAN------
              //$details->setUseEventValue($gawd_goal_value); */
           // $goal->setEventDetails($details);
        }

        //Set the time on site details.
        $this->analytics_member;
        $this->gawd_user_data = get_option('gawd_user_data');
        $accountId = $this->get_default_accountId();
        $webPropertyId = $this->get_default_webPropertyId();
        $profileId = $gawd_goal_profile;
        $analytics = $this->analytics_member;
        try {
            $analytics->management_goals->insert($accountId, $webPropertyId, $profileId, $goal);
        } catch (apiServiceException $e) {
            echo 'There was an Analytics API service error '
                . $e->getCode() . ':' . $e->getMessage();
        } catch (apiException $e) {
            echo 'There was a general API error '
                . $e->getCode() . ':' . $e->getMessage();
        }
        catch (Exception $e) {
            $myFile = GAWD_UPLOAD_DIR."/logfile.txt"; ;
            $fh = fopen($myFile, 'a');
            fwrite($fh, $e->getMessage(). "----add goal function".PHP_EOL);
            fclose($fh);
            $error = array('error_message' => $e->getMessage());
            if (strpos($e->getMessage(), 'User does not have permission to perform this operation')) {
                $error['error_message'] = 'User does not have permission to perform this operation';
            }
            return json_encode($error);
        }
    }

    public function add_filter($name, $type, $value) {
        $this->gawd_user_data = get_option('gawd_user_data');
        $accountId = $this->get_profile_accountId();
        $profileId = $this->get_profile_id();
        $webPropertyId = $this->get_profile_webPropertyId();
        $analytics = $this->analytics_member;
        $condition = $type == 'GEO_IP_ADDRESS' ? 'EQUAL' : 'MATCHES';
        /**
         * Note: This code assumes you have an authorized Analytics service object.
         * See the Filters Developer Guide for details.
         */
        /**
         * This request creates a new filter.
         */
        try {
            // Construct the filter expression object.
            $details = new Google_Service_Analytics_FilterExpression();
            $details->setField($type);
            $details->setMatchType($type);
            $details->setExpressionValue($value);
            $details->setCaseSensitive(false);
            // Construct the filter and set the details.
            $filter = new Google_Service_Analytics_Filter();
            $filter->setName($name);
            $filter->setType("EXCLUDE");
            $filter->setExcludeDetails($details);

            $insertedFilter = $analytics->management_filters->insert($accountId, $filter);
            $analyticsFilterRef = new Google_Service_Analytics_FilterRef();
            $analyticsFilterRef->setId($insertedFilter->id);
            $filterData = new Google_Service_Analytics_ProfileFilterLink();
            $filterData->setFilterRef($analyticsFilterRef );
            // Add view to inserted filter
            $res = $analytics->management_profileFilterLinks->insert($accountId, $webPropertyId, $profileId,  $filterData);

        } catch (apiServiceException $e) {
            echo 'There was an Analytics API service error '
                . $e->getCode() . ':' . $e->getMessage();
        } catch (apiException $e) {
            echo 'There was a general API error '
                . $e->getCode() . ':' . $e->getMessage();
        } catch (Exception $e) {

            $myFile = GAWD_UPLOAD_DIR."/logfile.txt"; ;
            $fh = fopen($myFile, 'a');
            fwrite($fh, $e->getMessage(). "----add_filter function".PHP_EOL);
            fclose($fh);

            echo '<script>window.location.href="' . admin_url() . 'admin.php?page=gawd_settings&errorMsg=1#gawd_filters_tab";</script>';
        }
    }

    public function get_country_data($metric, $dimension, $start_date, $end_date, $country_filter, $geo_type, $timezone) {
        $profileId = $this->get_profile_id();
        $analytics = $this->analytics_member;
        $metric = 'ga:users,ga:sessions,ga:percentNewSessions,ga:bounceRate,ga:pageviews,ga:avgSessionDuration';

        try {
            $results = $analytics->data_ga->get(
                'ga:' . $profileId, $start_date, $end_date, $metric, array(
                    'dimensions' => 'ga:' . $dimension,
                    'sort' => 'ga:' . $dimension,
                    'filters' => 'ga:' . $geo_type . '==' . $country_filter
                )
            );
        } catch (Exception $e) {

            $myFile = GAWD_UPLOAD_DIR."/logfile.txt"; ;
            $fh = fopen($myFile, 'a');
            fwrite($fh, $e->getMessage(). "----get_country_data function".PHP_EOL);
            fclose($fh);

            $error = array('error_message' => 'Error');
            if (strpos($e->getMessage(), 'User does not have sufficient permissions for this profile')) {
                $error['error_message'] = 'User does not have sufficient permissions for this profile';
            }
            return json_encode($error);
        }
        $rows = $results->getRows();
        $metric = explode(',', $metric);
        if ($rows) {
            $data_sum = array();
            foreach($results->getTotalsForAllResults() as $key => $value){
                $data_sum[trim(ucfirst(preg_replace('/([A-Z])/', ' $1', substr($key,3))))] = $value;
            }
            $j = 0;
            foreach ($rows as $row) {
                $data[$j] = array(
                    ucfirst($dimension) => $row[0]
                );
                $data[$j]['No'] = floatval($j + 1);
                for ($i = 0; $i < count($metric); $i++) {
                    $data[$j][trim(ucfirst(preg_replace('/([A-Z])/', ' $1', substr($metric[$i], 3))))] = floatval($row[$i + 1]);
                }
                $j++;
            }
        } else {
            $empty[0] = array(
                trim(ucfirst(preg_replace('/([A-Z])/', ' $1', $dimension))) => 0
            );
            $empty[0]['No'] = 1;
            for ($i = 0; $i < count($metric); $i++) {
                $empty[0][trim(ucfirst(preg_replace('/([A-Z])/', ' $1', substr($metric[$i], 3))))] = 0;
            }

            return json_encode($empty);
        }
        $expiration = strtotime(date("Y-m-d 23:59:59")) - strtotime(gmdate("Y-m-d H:i:s") . '+' . $timezone . ' hours');
        $result = $data;
        if ($data_sum != '') {
            $result = array('data_sum' => $data_sum, 'chart_data' => $data);
        }
        set_transient( 'gawd-country-'.$profileId.'-'.$country_filter.'-'.$start_date.'-'.$end_date, json_encode($result), $expiration  );
        return json_encode($result);
    }

    public function get_post_page_data($metric, $dimension, $start_date, $end_date, $filter, $timezone, $chart) {
        $profileId = $this->get_profile_id();
        $analytics = $this->analytics_member;
        $metric = 'ga:users,ga:sessions,ga:percentNewSessions,ga:bounceRate,ga:pageviews,ga:avgSessionDuration,ga:pageviewsPerSession';
        if($chart == 'pie'){
            $diff = date_diff(date_create($start_date),date_create($end_date));
            if(intval($diff->format("%a")) > 7){
                $dimension = 'week';
            }
            if(intval($diff->format("%a")) > 60){
                $dimension = 'month';
            }
        }
        // Get the results from the Core Reporting API and print the results.
        // Calls the Core Reporting API and queries for the number of sessions
        // for the last seven days.

        $filter_type = 'pagePath';

	    try {
		    $results = $analytics->data_ga->get(
			    'ga:' . $profileId, $start_date, $end_date, $metric, array(
				    'dimensions' => 'ga:' . $dimension,
				    'sort' => 'ga:' . $dimension,
				    'filters' => 'ga:' . $filter_type . '=~' . $filter
			    )
		    );
	    } catch (Exception $e) {

            $myFile = GAWD_UPLOAD_DIR."/logfile.txt"; ;
            $fh = fopen($myFile, 'a');
            fwrite($fh, $e->getMessage(). "----get_post_page_data function".PHP_EOL);
            fclose($fh);

            $error = array('error_message' => 'Error');
		    if (strpos($e->getMessage(), 'User does not have sufficient permissions for this profile')) {
			    $error['error_message'] = 'User does not have sufficient permissions for this profile';
		    }
		    return json_encode($error);
	    }

        $rows = $results->getRows();
        $metric = explode(',', $metric);
        if ($rows) {
            $j = 0;
            $data_sum = array();
            /*if ($dimension == 'week') {
                $date = $start_date;
                    $_end_date = date("M d,Y", strtotime('next Saturday ' . $date));
                if (strtotime($end_date) > strtotime(date('Y-m-d'))) {
                    $end_date = date("M d,Y");
                }
                foreach ($rows as $row) {
                        if (strtotime($_end_date) <= strtotime(date('Y-m-d'))) {

                            $dimension_value = date("M d,Y", strtotime($date)) . '-' . $_end_date;
                            // echo $date;
                        }
                        else {

                            if (strtotime($date) != strtotime($end_date)) {
                                $dimension_value = date("M d,Y", strtotime($date)) . '-' . $_end_date;
                            } else {
                                break;
                            }
                        }
                    $data[] = array(trim(ucfirst(preg_replace('/([A-Z])/', ' $1', $dimension))) => $dimension_value);
                    $data[$j]['No'] = floatval($j + 1);
                    for ($i = 0; $i < count($metric); $i++) {
                        $val = $i + 1;
                        $metric_val = floatval($row[$val]);
                        if(substr($metric[$i], 3) == 'bounceRate'){
                          $metric_val = $metric_val;
                        }
                        $data[$j][trim(ucfirst(preg_replace('/([A-Z])/', ' $1', substr($metric[$i], 3))))] = $metric_val;
                    }
                    $j++;
                    $date = date("M d,Y", strtotime('next Sunday ' . $_end_date));
                    $_end_date = date("M d,Y", strtotime('next Saturday ' . $date));
                    if (isset($_end_date) && (strtotime($_end_date) > strtotime($end_date))) {
                      $_end_date = $end_date;
                    }
                }
            }*/
            if ($dimension == 'week' || $dimension == 'month') {
                $date = $start_date;
                if ($dimension == 'week') {
                    $_end_date =  date("l", strtotime($date)) == 'Saturday' ? date("M d,Y", strtotime($date)) : date("M d,Y", strtotime('next Saturday ' . $date));
                }
                elseif ($dimension == 'month') {
                    $_end_date = date("M t,Y", strtotime($date));
                    if(strtotime($_end_date) > strtotime(date('Y-m-d'))){
                        $_end_date = date("M d,Y",strtotime('-1 day ' . date('Y-m-d')));
                    }
                }
                if (strtotime($end_date) > strtotime(date('Y-m-d'))) {
                    $end_date = date("M d,Y");
                }
                foreach ($rows as $row) {
                    if ($dimension == 'hour') {
                        $dimension_value = date("M d,Y", strtotime($row[0])) . ' ' . $row[1] . ':00';
                    }
                    else {
                        if (strtotime($_end_date) <= strtotime(date('Y-m-d'))) {

                            $dimension_value = date("M d,Y", strtotime($date)) . '-' . $_end_date;
                            // echo $date;
                        } else {
                            if ($dimension == 'month') {
                                //continue;
                            }
                            if (strtotime($date) != strtotime($end_date) ) {
                                $dimension_value = date("M d,Y", strtotime($date)) . '-' . $_end_date;
                            } else {
                                break;
                            }
                        }
                    }
                    $data[] = array(trim(ucfirst(preg_replace('/([A-Z])/', ' $1', $dimension))) => $dimension_value);
                    $data[$j]['No'] = floatval($j + 1);
                    for ($i = 0; $i < count($metric); $i++) {
                        $val = $i + 1;
                        if ($dimension == 'hour') {
                            $val = $i + 2;
                        }
                        $metric_val = floatval($row[$val]);
                        if(substr($metric[$i], 3) == 'bounceRate'){
                            $metric_val = $metric_val;
                        }
                        $data[$j][trim(ucfirst(preg_replace('/([A-Z])/', ' $1', substr($metric[$i], 3))))] = $metric_val;
                    }

                    $j++;

                    if(isset($break) && $break){
                        break;
                    }

                    if ($dimension == 'week') {
                        $date = date("M d,Y", strtotime('next Sunday ' . $_end_date));
                        $_end_date = date("M d,Y", strtotime('next Saturday ' . $date));
                    } elseif ($dimension == 'month') {
                        $date = date("M d,Y", strtotime('+1 day ' . $_end_date));
                        $_end_date = date("M t,Y", strtotime($date));
                    }
                    if (isset($_end_date) && (strtotime($_end_date) > strtotime($end_date))) {
                        $_end_date = date("M d,Y", strtotime($end_date));
                        $break = true;
                    }
                }
            }
            else{
                foreach ($rows as $row) {

                    if ($dimension == 'date') {
                        $row[0] = date('Y-m-d', strtotime($row[0]));
                    }
                    $data[$j] = array(
                        $dimension => $row[0]
                    );
                    for ($i = 0; $i < count($metric); $i++) {
                        $data[$j][substr($metric[$i], 3)] = floatval($row[$i + 1]);
                        if (isset($data_sum[substr($metric[$i], 3)])) {
                            $data_sum[substr($metric[$i], 3)] += floatval($row[$i + 1]);
                        } else {
                            if (substr($metric[$i], 3) != 'percentNewSessions' && substr($metric[$i], 3) != 'bounceRate') {
                                $data_sum[substr($metric[$i], 3)] = floatval($row[$i + 1]);
                            }
                        }
                    }
                    $j++;
                }
            }
            $expiration = strtotime(date("Y-m-d 23:59:59")) - strtotime(gmdate("Y-m-d H:i:s") . '+' . $timezone . ' hours');
            if (isset($same_dimension) && $same_dimension != null) {
                $dimension = $same_dimension;
            }
            $result = array('data_sum' => $data_sum, 'chart_data' => $data);
            set_transient('gawd-page-post-' . $profileId . '-' . $filter . '-' . '-' . $dimension . '-' . $start_date . '-' . $end_date . '-' . $chart, json_encode($result), $expiration);
            return json_encode($result);
        } else {
            $empty[] = array(
                $dimension => 0,
                substr($metric[0], 3) => 0);
            return json_encode($empty);
        }
    }

    public function get_data($metric, $dimension, $start_date, $end_date, $filter_type, $timezone, $same_dimension = null) {
      if(function_exists('lcfirst') === false) {
          function lcfirst($str) {
              $str[0] = strtolower($str[0]);
              return $str;
          }
      }
        $dimension = lcfirst($dimension);

        $metric = lcfirst($metric);
        $profileId = $this->get_profile_id();
        
        $analytics = $this->analytics_member;
        $selected_metric = $metric;
        if (strpos($selected_metric, 'ga:') > -1) {
            $selected_metric = substr($selected_metric,3);
        }
        if (strpos($metric, 'ga:') === false) {
            $metric = 'ga:' . $metric;
        }
        if ($dimension == 'interestInMarketCategory' || $dimension == 'interestAffinityCategory' || $dimension == 'interestOtherCategory' || $dimension == 'country' || $dimension == 'language' || $dimension == 'userType' || $dimension == 'sessionDurationBucket' || $dimension == 'userAgeBracket' || $dimension == 'userGender' || $dimension == 'mobileDeviceInfo' || $dimension == 'deviceCategory' || $dimension == 'operatingSystem' || $dimension == 'browser' || $dimension == 'date' || $dimension == "source") {
            $metrics = 'ga:users,ga:sessions,ga:percentNewSessions,ga:bounceRate,ga:pageviews,ga:avgSessionDuration,ga:pageviewsPerSession';

            if (strpos($metrics, $metric) !== false) {
                $metric = $metrics;
            }
        }
        elseif ($dimension == 'siteSpeed') {
            $dimension = 'date';
            $metrics = 'ga:avgPageLoadTime,ga:avgRedirectionTime,ga:avgServerResponseTime,ga:avgPageDownloadTime';
            if (strpos($metrics, $metric) !== false) {
                $metric = $metrics;
            }
        }
        elseif ($dimension == 'eventLabel' || $dimension == 'eventAction' || $dimension == 'eventCategory') {
            $metrics = 'ga:eventsPerSessionWithEvent,ga:sessionsWithEvent,ga:avgEventValue,ga:eventValue,ga:uniqueEvents,ga:totalEvents';
            if (strpos($metrics, $metric) !== false) {
                $metric = $metrics;
            }
        } 

        $dimension = $dimension == 'date' ? $filter_type != '' ? $filter_type : 'date' : $dimension;
        if ($same_dimension == 'sales_performance' && ($dimension == 'week' || $dimension == 'month' || $dimension == 'hour')) {
            $metrics = 'ga:transactionRevenue, ga:transactionsPerSession';
            if (strpos($metrics, $metric) !== false) {
                $metric = $metrics;
            }
        } elseif ($same_dimension == 'adsense' && ($dimension == 'week' || $dimension == 'month' || $dimension == 'hour')) {
            $metrics = 'ga:adsenseRevenue,ga:adsenseAdsClicks';
            if (strpos($metrics, $metric) !== false) {
                $metric = $metrics;
            }
        } elseif ($same_dimension == 'siteSpeed' && ($dimension == 'week' || $dimension == 'month' || $dimension == 'hour')) {
            $metrics = 'ga:avgPageLoadTime,ga:avgRedirectionTime,ga:avgServerResponseTime,ga:avgPageDownloadTime';
            if (strpos($metrics, $metric) !== false) {
                $metric = $metrics;
            }
        }

        if ($same_dimension == 'week' || $same_dimension == 'month' || $same_dimension == 'hour') {
            $metrics = 'ga:users,ga:sessions,ga:percentNewSessions,ga:bounceRate,ga:pageviews,ga:avgSessionDuration';
            if (strpos($metrics, $metric) !== false) {
                $metric = $metrics;
            }
        }

        /*        if(!is_array($metric)){
          if (strpos($metric, 'ga') === false) {
          $metric = 'ga:' . $metric;
          }
          } */
        // Get the results from the Core Reporting API and print the results.
        // Calls the Core Reporting API and queries for the number of sessions
        // for the last seven days.
        if ($dimension == 'hour') {

            $gawd_dimension = array(
                'dimensions' => 'ga:date, ga:hour',
                'sort' => 'ga:date',
            );
        }
        else {
          if($dimension != 'sessionDurationBucket'){
            $gawd_dimension = array(
                'dimensions' => 'ga:' . $dimension,
                'sort' => '-ga:' . $selected_metric,
            );
          }
          else{
            $gawd_dimension = array(
                'dimensions' => 'ga:' . $dimension,
                'sort' => 'ga:' . $dimension,
            );
          }
        }
      if(isset($profileId) && $profileId != ''){
        try {
            $results = $analytics->data_ga->get(
                'ga:' . $profileId, $start_date, $end_date, $metric, $gawd_dimension
            );

        } 
        catch (Exception $e) {
            $myFile = GAWD_UPLOAD_DIR."/logfile.txt"; ;
            $fh = fopen($myFile, 'a');
            fwrite($fh, $e->getMessage(). "----get_data function".PHP_EOL);
            fclose($fh);
            $error = array('error_message' => 'Error');
            if (strpos($e->getMessage(), 'Selected dimensions and metrics cannot be queried together')) {
                $error['error_message'] = 'Selected dimensions and metrics cannot be queried together';
            } else if (strpos($e->getMessage(), 'User does not have sufficient permissions for this profile')) {
                $error['error_message'] = 'User does not have sufficient permissions for this profile';
            }
            return json_encode($error);
        }

        $metric = explode(',', $metric);
        $rows = $results->getRows();
        if ($rows) {

            $j = 0;
            $data_sum = array();
            foreach($results->getTotalsForAllResults() as $key => $value){
                $data_sum[trim(ucfirst(preg_replace('/([A-Z])/', ' $1', substr($key,3))))] = $value;
            }

            if ($dimension == 'week' || $dimension == 'month' || $dimension == 'hour') {

                $date = $start_date;
                if ($dimension == 'week') {
                    $_end_date =  date("l", strtotime($date)) == 'Saturday' ? date("M d,Y", strtotime($date)) : date("M d,Y", strtotime('next Saturday ' . $date));
                }
                elseif ($dimension == 'month') {
                    $_end_date = date("M t,Y", strtotime($date));
                    if(strtotime($_end_date) > strtotime(date('Y-m-d'))){
                        $_end_date = date("M d,Y",strtotime('-1 day ' . date('Y-m-d')));
                    }
                }
                if (strtotime($end_date) > strtotime(date('Y-m-d'))) {
                    $end_date = date("M d,Y");
                }
                foreach ($rows as $row) {
                    if ($dimension == 'hour') {
                        $dimension_value = date("M d,Y", strtotime($row[0])) . ' ' . $row[1] . ':00';
                    }
                    else {
                        if (strtotime($_end_date) <= strtotime(date('Y-m-d'))) {

                            $dimension_value = date("M d,Y", strtotime($date)) . '-' . $_end_date;
                            // echo $date;
                        } else {
                            if ($dimension == 'month') {
                                //continue;
                            }
                            if (strtotime($date) != strtotime($end_date) ) {
                                $dimension_value = date("M d,Y", strtotime($date)) . '-' . $_end_date;
                            } else {
                                break;
                            }
                        }
                    }
                    $data[] = array(trim(ucfirst(preg_replace('/([A-Z])/', ' $1', $dimension))) => $dimension_value);
                    $data[$j]['No'] = floatval($j + 1);
                    for ($i = 0; $i < count($metric); $i++) {
                        $val = $i + 1;
                        if ($dimension == 'hour') {
                            $val = $i + 2;
                        }
                        $metric_val = floatval($row[$val]);
                        if(substr($metric[$i], 3) == 'bounceRate'){
                            $metric_val = $metric_val;
                        }
                        $data[$j][trim(ucfirst(preg_replace('/([A-Z])/', ' $1', substr($metric[$i], 3))))] = $metric_val;
                    }

                    $j++;

                    if(isset($break) && $break){
                        break;
                    }

                    if ($dimension == 'week') {
                        $date = date("M d,Y", strtotime('next Sunday ' . $_end_date));
                        $_end_date = date("M d,Y", strtotime('next Saturday ' . $date));
                    } elseif ($dimension == 'month') {
                        $date = date("M d,Y", strtotime('+1 day ' . $_end_date));
                        $_end_date = date("M t,Y", strtotime($date));
                    }
                    if (isset($_end_date) && (strtotime($_end_date) > strtotime($end_date))) {
                        $_end_date = date("M d,Y", strtotime($end_date));
                        $break = true;
                    }
                }
            }
            else {
                foreach ($rows as $row) {
                    if (strtolower($dimension) == 'date') {
                        $row[0] = date('Y-m-d', strtotime($row[0]));
                    }
                    elseif(strtolower($dimension) == 'sessiondurationbucket'){
                        if($row[0] >= 0 && $row[0] <= 10){
                            $row[0] = '0-10';
                        }
                        elseif($row[0] >= 11 && $row[0] <= 30){
                            $row[0] = '11-30';
                        }
                        elseif($row[0] >= 31 && $row[0] <= 40){
                            $row[0] = '31-40';
                        }
                        elseif($row[0] >= 41 && $row[0] <= 60){
                            $row[0] = '41-60';
                        }
                        elseif($row[0] >= 61 && $row[0] <= 180){
                            $row[0] = '61-180';
                        }
                        elseif($row[0] >= 181 && $row[0] <= 600){
                            $row[0] = '181-600';
                        }
                        elseif($row[0] >= 601 && $row[0] <= 1800){
                            $row[0] = '601-1800';
                        }
                        elseif($row[0] >= 1801){
                            $row[0] = '1801';
                        }
                    }
                    elseif(strpos($dimension,'dimension') >-1){
                        $dimension_data = $this->get_custom_dimensions();
                        foreach($dimension_data as $key => $value){
                            if($dimension == substr($value['id'],3)){
                                $dimension = $value['name'];
                            }
                        }
                    }
                    $data[$j]['No'] = floatval($j + 1);
                    $dimension_data = ctype_digit($row[0]) ? intval($row[0]) : $row[0];
                    $dimension_data = strpos($dimension_data,'T') ? substr($dimension_data ,0,strpos($dimension_data,'T')) : $dimension_data;
                    $data[$j][trim(ucfirst(preg_replace('/([A-Z])/', ' $1', $dimension)))] = $dimension_data;

                    for ($i = 0; $i < count($metric); $i++) {
                        $metric_val = floatval($row[$i + 1]);
                        if(substr($metric[$i], 3) == 'avgSessionDuration'){
                            $metric_val = ceil($row[$i + 1]);
                        }
                        $data[$j][trim(ucfirst(preg_replace('/([A-Z])/', ' $1', substr($metric[$i], 3))))] = $metric_val;
                    }
                    $j++;
                }
            }
            $expiration = strtotime(date("Y-m-d 23:59:59")) - strtotime(gmdate("Y-m-d H:i:s") . '+' . $timezone . ' hours');
            if (isset($same_dimension) && $same_dimension != null) {
                $dimension = $filter_type == 'date' || $filter_type == '' || $filter_type == 'Date' ? $same_dimension : $same_dimension . '_' . $filter_type;
            }
            if($dimension == "daysToTransaction"){
                foreach ($data as $key => $row) {
                    $daysToTransaction[$key]  = $row['Days To Transaction'];
                }
                array_multisort($daysToTransaction, SORT_ASC, $data);
                foreach($data as $j=>$val){
                    $val["No"] = ($j+1);
                    $data[$j] = $val;
                }
            }
            elseif($dimension == "sessionDurationBucket"){
                $_data = array();
                //$j = 1;
                foreach($data as $val){
                    if(isset($_data[$val["Session Duration Bucket"]])){
                        $_data[$val["Session Duration Bucket"]]["Users"] += floatval($val["Users"]);
                        $_data[$val["Session Duration Bucket"]]["Sessions"] += floatval($val["Sessions"]);
                        $_data[$val["Session Duration Bucket"]]["Percent New Sessions"] += floatval($val["Percent New Sessions"]);
                        $_data[$val["Session Duration Bucket"]]["Bounce Rate"] += floatval($val["Bounce Rate"]);
                        $_data[$val["Session Duration Bucket"]]["Pageviews"] += floatval($val["Pageviews"]);
                        $_data[$val["Session Duration Bucket"]]["Avg Session Duration"] += $val["Avg Session Duration"];
                    }
                    else{
                        // $val["No"] = $j;
                        // $j++;
                        $_data[$val["Session Duration Bucket"]] = $val;
                        $_data[$val["Session Duration Bucket"]]["order"] = intval($val["Session Duration Bucket"]);
                    }


                }
                $data = array_values($_data);
                foreach ($data as $key => $row) {
                    $yyy[$key]  = $row['order'];
                }
                array_multisort($yyy, SORT_ASC, $data);
                foreach($data as $j=>$val){
                    $val["No"] = ($j+1);
                    $data[$j] = $val;
                }
            }
            else{
              if(strpos($dimension,'dimension') === false){
                $dimension = $dimension == 'siteSpeed' || $dimension == 'sales_performance' ? 'Date' : $dimension;
                foreach ($data as $key => $row) {
                  $new_data[$key]  = $row[trim(ucfirst(preg_replace('/([A-Z])/', ' $1', $dimension)))];
                }
                array_multisort($new_data, SORT_ASC, $data);
                foreach($data as $j=>$val){
                    $val["No"] = ($j+1);
                    $data[$j] = $val;
                }
              }
            }
            $result = $data;
            if ($data_sum != '') {
                $result = array('data_sum' => $data_sum, 'chart_data' => $data);
            }
            set_transient( 'gawd-'.$profileId.'-'.$dimension.'-'.$start_date.'-'.$end_date, json_encode($result), $expiration  );
            return json_encode($result);
        }
        else {
            if(strpos($dimension,'dimension') >-1){
                $dimension_data = $this->get_custom_dimensions();
                foreach($dimension_data as $key => $value){
                    if($dimension == substr($value['id'],3)){
                        $dimension = $value['name'];
                    }
                }
            }
            $empty[0] = array(
                trim(ucfirst(preg_replace('/([A-Z])/', ' $1', $dimension))) => 0
            );
            $empty[0]['No'] = 1;
            for ($i = 0; $i < count($metric); $i++) {
                $empty[0][trim(ucfirst(preg_replace('/([A-Z])/', ' $1', substr($metric[$i], 3))))] = 0;
            }

            return json_encode(array('chart_data' => $empty));
        }
      }
    }

    public function get_data_compact($metric, $dimension, $start_date, $end_date, $timezone) {
        $profileId = $this->get_profile_id();
        $metric_sort = $metric;
        $analytics = $this->analytics_member;
        // Get the results from the Core Reporting API and print the results.
        // Calls the Core Reporting API and queries for the number of sessions
        // for the last seven days.
        if ($dimension == 'date') {
            $metric = 'ga:users,ga:sessions,ga:percentNewSessions,ga:bounceRate,ga:pageviews,ga:avgSessionDuration,ga:pageviewsPerSession';
        }
        try{
            $results = $analytics->data_ga->get(
                'ga:' . $profileId, $start_date, $end_date, $metric, array(
                    'dimensions' => 'ga:' . $dimension,
                    'sort' => 'ga:' . $dimension,
                )
            );
        }
        catch (Exception $e) {
            $myFile = GAWD_UPLOAD_DIR."/logfile.txt"; ;
            $fh = fopen($myFile, 'a');
            fwrite($fh, $e->getMessage(). "----get_data_compact function".PHP_EOL);
            fclose($fh);
        }
        $rows = $results->getRows();
        $metric = explode(',', $metric);
        if ($rows) {
            $j = 0;
            $data_sum = array();
            foreach($results->getTotalsForAllResults() as $key => $value){
                $data_sum[trim(ucfirst(preg_replace('/([A-Z])/', ' $1', substr($key,3))))] = $value;
            }

            foreach ($rows as $row) {
                if ($dimension == 'date') {
                    $row[0] = date('Y-m-d', strtotime($row[0]));
                }
                $data[$j] = array(
                    trim(ucfirst(preg_replace('/([A-Z])/', ' $1', $dimension))) => $row[0]
                );
                for ($i = 0; $i < count($metric); $i++) {
                    $metric_val = floatval($row[$i + 1]);

                    $data[$j][trim(ucfirst(preg_replace('/([A-Z])/', ' $1', substr($metric[$i], 3))))] = $metric_val;
                }
                $j++;
            }
            if($dimension == "country"){
                foreach ($data as $key => $row) {
                    $country[$key]  = $row[trim(ucfirst(preg_replace('/([A-Z])/', ' $1', substr($metric_sort, 3))))];
                }
                array_multisort($country, SORT_DESC, $data);
                foreach($data as $j=>$val){
                    $val["No"] = ($j+1);
                    $data[$j] = $val;
                }
            }
        }
        else {
            $data_sum = array();
            $empty[0] = array(
                trim(ucfirst(preg_replace('/([A-Z])/', ' $1', $dimension))) => 0
            );
            $data_sum[trim(ucfirst(preg_replace('/([A-Z])/', ' $1', $dimension)))] = 0;
            $empty[0]['No'] = 1;
            for ($i = 0; $i < count($metric); $i++) {
                $empty[0][trim(ucfirst(preg_replace('/([A-Z])/', ' $1', substr($metric[$i], 3))))] = 0;
                $data_sum[trim(ucfirst(preg_replace('/([A-Z])/', ' $1', substr($metric[$i], 3))))] = 0;
            }
            $result = array('data_sum' => $data_sum, 'chart_data' => $empty);
            return json_encode($result);
        }
        if ($data_sum != '') {
            $result = array('data_sum' => $data_sum, 'chart_data' => $data);
        }
        $expiration = strtotime(date("Y-m-d 23:59:59")) - strtotime(gmdate("Y-m-d H:i:s") . '+' . $timezone . ' hours');
        set_transient('gawd-compact-' . $profileId . '-' . $dimension . '-' . $start_date . '-' . $end_date, json_encode($result), $expiration);
        return json_encode($result);
    }

    public function get_data_alert($metric, $dimension, $start_date, $end_date, $gawd_alert_view) {
        $profileId = $gawd_alert_view == '' ? $this->get_profile_id() : $gawd_alert_view;
        $analytics = $this->analytics_member;
        // Get the results from the Core Reporting API and print the results.
        // Calls the Core Reporting API and queries for the number of sessions
        // for the last seven days.
        $results = $analytics->data_ga->get(
            'ga:' . $profileId, $start_date, $end_date, $metric, array(
                'dimensions' => 'ga:' . $dimension,
                'sort' => 'ga:' . $dimension,
            )
        );
        $rows = $results->getRows();

        $data = '';
        foreach ($rows as $row) {
            $data += floatval($row[1]);
        }
        return ($data);
    }

    public function get_profile_id() {
        $this->gawd_user_data = get_option('gawd_user_data');
        $profiles_light = get_transient('gawd_user_profiles') ? get_transient('gawd_user_profiles') : $this->gawd_user_data['gawd_profiles'];
        if (!isset($this->gawd_user_data['gawd_id']) || $this->gawd_user_data['gawd_id'] == '') {
            if (!empty($profiles_light)) {
	            $first_profiles = reset($profiles_light);
	            $first_profile = $first_profiles[0];
                $this->gawd_user_data['gawd_id'] = $first_profile['id'];
            }
        }
	    return (isset($this->gawd_user_data['gawd_id'])?$this->gawd_user_data['gawd_id']:null);
    }

    public function get_profile_webPropertyId() {
        $this->gawd_user_data = get_option('gawd_user_data');
	    return (isset($this->gawd_user_data['webPropertyId'])?$this->gawd_user_data['webPropertyId']:null);
    }

    public function get_profile_accountId() {
        $this->gawd_user_data = get_option('gawd_user_data');
        return isset($this->gawd_user_data['accountId']) ? $this->gawd_user_data['accountId'] : '';
    }

    public function get_default_webPropertyId() {
        $this->gawd_user_data = get_option('gawd_user_data');
	    return (isset($this->gawd_user_data['default_webPropertyId'])?$this->gawd_user_data['default_webPropertyId']:null);
    }

    public function get_default_accountId() {
        $this->gawd_user_data = get_option('gawd_user_data');
	    return (isset($this->gawd_user_data['default_accountId'])?$this->gawd_user_data['default_accountId']:null);
    }

    public function get_page_data($dimension, $start_date, $end_date, $timezone) {
        $analytics = $this->analytics_member;
        $profileId = $this->get_profile_id();
        $metric = $dimension == 'pagePath' ||  $dimension == 'PagePath' ? 'ga:pageviews,ga:uniquePageviews,ga:avgTimeOnPage,ga:entrances,ga:bounceRate,ga:exitRate,ga:pageValue,ga:avgPageLoadTime' : 'ga:sessions,ga:percentNewSessions,ga:newUsers,ga:bounceRate,ga:pageviewsPerSession,ga:avgSessionDuration,ga:transactions,ga:transactionRevenue,ga:transactionsPerSession';
        $sorts = explode(',', $metric);
        $sort = '-'. $sorts[0];

        try {
            $results = $analytics->data_ga->get(
                'ga:' . $profileId, $start_date, $end_date, $metric, array(
                    'dimensions' => 'ga:'.$dimension,
                    'sort' => $sort,
                )
            );
        } catch (Exception $e) {

            $myFile = GAWD_UPLOAD_DIR."/logfile.txt"; ;
            $fh = fopen($myFile, 'a');
            fwrite($fh, $e->getMessage(). "----get_page_data function".PHP_EOL);
            fclose($fh);

            $error = array('error_message' => 'Error');
            if (strpos($e->getMessage(), 'User does not have sufficient permissions for this profile')) {
                $error['error_message'] = 'User does not have sufficient permissions for this profile';
            }
            return json_encode($error);
        }
        $rows = $results->getRows();
        $metric = explode(',', $metric);
        if ($rows) {
            $data_sum = array();
            foreach($results->getTotalsForAllResults() as $key => $value){
                $data_sum[trim(ucfirst(preg_replace('/([A-Z])/', ' $1', substr($key,3))))] = $value;
            }
            foreach ($rows as $key => $row) {
                $hours = strlen(floor($row[3] / 3600)) < 2 ?  '0' . floor($row[3] / 3600) : floor($row[3] / 3600);
                $mins = strlen(floor($row[3] / 60 % 60)) < 2 ? '0' . floor($row[3] / 60 % 60) : floor($row[3] / 60 % 60);
                $secs = strlen(floor($row[3] % 60)) < 2 ? '0' . floor($row[3] % 60) : floor($row[3] % 60);
                $time_on_page = $hours.':'.$mins.':'.$secs;
                if($dimension == 'pagePath' || $dimension == 'PagePath'){
                    $data[] = array(
                        'No' => floatval($key + 1),
                        'Page Path' => $row[0],
                        'Pageviews' => intval($row[1]),
                        'Unique Pageviews' => intval($row[2]),
                        'Avg Time On Page' => $time_on_page,
                        'Entrances' => intval($row[4]),
                        'Bounce Rate' => floatval($row[5]),
                        'Exit Rate' => ($row[6]),
                        'Page Value' => intval($row[7]),
                        'Avg Page Load Time' => intval($row[8])
                    );
                }
                else{
                    $data[] = array(
                        'No' => floatval($key + 1),
                        'Landing Page' => $row[0],
                        'Sessions' => intval($row[1]),
                        'Percent New Sessions' => ($row[2]),
                        'New Users' => intval($row[3]),
                        'Bounce Rate' => ($row[4]),
                        'Pageviews Per Session' => floatval($row[5]),
                        'Avg Session Duration' => intval($row[6]),
                        'Transactions' => intval($row[7]),
                        'Transaction Revenue' => intval($row[8]),
                        'Transactions Per Session' => intval($row[9])
                    );
                }
            }

        }
        else {
            $empty[0] = array(
                trim(ucfirst(preg_replace('/([A-Z])/', ' $1', $dimension))) => 0
            );
            $empty[0]['No'] = 1;
            for ($i = 0; $i < count($metric); $i++) {
                $empty[0][trim(ucfirst(preg_replace('/([A-Z])/', ' $1', substr($metric[$i], 3))))] = 0;
                $data_sum[trim(ucfirst(preg_replace('/([A-Z])/', ' $1', substr($metric[$i],3))))] = 0;
            }

            return json_encode(array('data_sum' => $data_sum, 'chart_data' => $empty));
        }
        if ($data_sum != '') {
            $result = array('data_sum' => $data_sum, 'chart_data' => $data);
        }
        $expiration = strtotime(date("Y-m-d 23:59:59")) - strtotime(gmdate("Y-m-d H:i:s") . '+' . $timezone . ' hours');
        set_transient('gawd-' . $profileId . '-' . $dimension . '-' . $start_date . '-' . $end_date, json_encode($result), $expiration);
        return json_encode($result);
    }

    public function get_goal_data($dimension, $start_date, $end_date, $timezone, $same_dimension) {
        $goals = $this->get_management_goals();
        if ('no_goals_exist' != $goals) {
            $analytics = $this->analytics_member;
            $profileId = $this->get_profile_id();
            $metric = array();
            $all_metric = '';
            $counter = 1;
            foreach ($goals as $goal) {
                $all_metric .= 'ga:goal' . $goal['id'] . 'Completions,';
                if($counter <= 10){
                    $metrics[0][] = 'ga:goal' . $goal['id'] . 'Completions';
                }
                else{
                    $metrics[1][] = 'ga:goal' . $goal['id'] . 'Completions';
                }
                $counter++;
            }
            $rows = array();
            foreach($metrics as $metric){
                $metric = implode(',',$metric);
                $results = $analytics->data_ga->get(
                    'ga:' . $profileId, $start_date, $end_date, $metric, array(
                        'dimensions' => 'ga:' . $dimension,
                        'sort' => 'ga:' . $dimension,
                    )
                );

                $temp_rows = $results->getRows();
                if(empty($temp_rows)){
                    continue;
                }

                foreach($temp_rows as $key=>$value){
                    if(!isset($rows[$key])){
                        $rows[$key] = $value;
                    }
                    else{
                        unset($value[0]);
                        $rows[$key] = array_merge($rows[$key],$value);
                    }
                }

            }
            $all_metric = explode(',', $all_metric);
            if ($rows) {
                $j = 0;
                $data_sum = array();
                foreach ($rows as $row) {
                    if ($dimension == 'date') {
                        $row[0] = date('Y-m-d', strtotime($row[0]));
                    }
                    $data[$j] = array(
                        preg_replace('!\s+!',' ',trim(ucfirst(preg_replace('/([A-Z])/', ' $1', $dimension))))=> $row[0]
                    );
                    $data[$j]['No'] = floatval($j + 1);
                    for ($i = 0; $i < count($goals); $i++) {
                        $data[$j][preg_replace('!\s+!',' ',trim(ucfirst(preg_replace('/([A-Z])/', ' $1', $goals[$i]['name']))))] = floatval($row[$i + 1]);
                        if (isset($data_sum[preg_replace('!\s+!',' ',trim(ucfirst(preg_replace('/([A-Z])/', ' $1', $goals[$i]['name']))))])) {
                            $data_sum[preg_replace('!\s+!',' ',trim(ucfirst(preg_replace('/([A-Z])/', ' $1', $goals[$i]['name']))))] += floatval($row[$i + 1]);
                        } else {
                            if (substr($all_metric[$i], 3) != 'percentNewSessions' && substr($all_metric[$i], 3) != 'bounceRate') {
                                $data_sum[preg_replace('!\s+!',' ',trim(ucfirst(preg_replace('/([A-Z])/', ' $1', $goals[$i]['name']))))] = floatval($row[$i + 1]);
                            }
                        }
                    }
                    $j++;
                }
                $expiration = strtotime(date("Y-m-d 23:59:59")) - strtotime(gmdate("Y-m-d H:i:s") . '+' . $timezone . ' hours');
                if (isset($same_dimension) && $same_dimension != null) {
                    $dimension = $same_dimension;
                }
                $result = $data;
                if ($data_sum != '') {
                    $result = array('data_sum' => $data_sum, 'chart_data' => $data);
                }
                set_transient('gawd-' . $profileId . '-' . $dimension . '-' . $start_date . '-' . $end_date, json_encode($result), $expiration);
                return json_encode($result);
            } else {
                return $goals;
            }
        } else {
            return json_encode(array('error_message' => 'No goals exist'));
        }
    }

    public function gawd_realtime_data() {
        $analytics = $this->analytics_member;
        $profileId = $this->get_profile_id();
        $metrics = 'rt:activeUsers';
        $dimensions = 'rt:pagePath,rt:source,rt:keyword,rt:trafficType,rt:country,rt:pageTitle,rt:deviceCategory';
        $managequota = 'u' . get_current_user_id() . 's' . get_current_blog_id();

        try {
            $data = $analytics->data_realtime->get('ga:' . $profileId, $metrics, array('dimensions' => $dimensions, 'quotaUser' => $managequota . 'p' . $profileId));
        } catch (Exception $e) {

            $myFile = GAWD_UPLOAD_DIR."/logfile.txt"; ;
            $fh = fopen($myFile, 'a');
            fwrite($fh, $e->getMessage(). "----gawd_realtime_data function".PHP_EOL);
            fclose($fh);

            $error = array('error_message' => 'Error');
            if (strpos($e->getMessage(), 'User does not have sufficient permissions for this profile')) {
                $error['error_message'] = 'User does not have sufficient permissions for this profile';
            }
            return json_encode($error);
        }
        $expiration = 5 ;
        if ($data->getRows() != '') {
            $i = 0;
            $gawd_data = $data;
            foreach ($data->getRows() as $row) {
                $gawd_data[$i] = $row;
                $i++;
            }
            set_transient('gawd-real-' . $profileId, json_encode($gawd_data), $expiration);
            echo json_encode($gawd_data);
            wp_die();
        }
        else {
            return 0;
        }
    }

    public static function get_instance($force_new_instance = false) {

        if (null === self::$instance && !$force_new_instance) {
            self::$instance = new self();
        }
        else if($force_new_instance){
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function has_error(){

        return self::$error_message == '' ? false : true;
    }
    public static function get_error_notice(){
        ?>
        <div class="notice notice-error">
            <p>Google Analytics WD: <?php echo self::$error_message; ?></p>
        </div>
        <?php
    }

}
