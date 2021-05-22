<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * This controller use for common apis
 */
class Common extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    /**
     * This function use for show test page view for testing apis
     * Modified Date :- 2016-09-15
     */
    public function index_get() {
       
        $user_name = $this->input->get("unm");
        $password = $this->input->get("pwd");
        if (
                (!empty($user_name) && $user_name == 'medeasy') &&
                (!empty($password) && $password == 'api@medeasy')
        ) {
            $this->load->view('test');
        } else {
            redirect(DOMAIN_URL);
        }
    }

    /**
     * 
     * This function use for get last query log form api_log table base on device type
     * 
     * @author Dipesh Shihora
     * 
     * Modified Date :- 2016-09-15
     * 
     */
    public function get_last_api_log_post() {
        try {
            $no_of_record = !empty($this->post_data['no_of_record']) ? $this->post_data['no_of_record'] : 10;
            $device_type = !empty($this->post_data['device_type']) ? $this->post_data['device_type'] : "";

            if (empty($device_type)) {
                $this->bad_request();
            }

            $where = array();
            $where['al_device_type'] = $device_type;

            $api_log_data = $this->Common_model->get_all_rows(TBL_API_LOGS, "*", $where, array(), array("al_created_date" => "DESC"), $no_of_record);

            $this->my_response = array(
                "status" => true,
                "message" => lang("common_api_log_success"),
                "data" => $api_log_data
            );
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * 
     * This function use for testing push notification fot both device
     * 
     * @author Dipesh Shihora
     * 
     * Modified Date :- 2016-09-15
     * 
     */
    public function push_notification_test_post() {
        try {
            $device_token = !empty($this->post_data['device_token']) ? $this->post_data['device_token'] : "";
            $notification_message = !empty($this->post_data['message']) ? $this->post_data['message'] : "";
            $notification_type = !empty($this->post_data['notification_type']) ? $this->post_data['notification_type'] : "";
            $notification_type_id = !empty($this->post_data['notification_type_id']) ? $this->post_data['notification_type_id'] : "";
            $device_type = !empty($this->post_data['device_type']) ? $this->post_data['device_type'] : "";

            if (empty($device_type) || empty($device_token) || empty($notification_message)) {
                $this->bad_request();
            }

            if (strtolower($device_type) == "ios") {
                $message = array();
                $message['aps']['icon'] = 'appicon';
                $message['aps']['vibrate'] = 'true';
                $message['aps']['badge'] = 1;
                $message['aps']['sound'] = "default";
                $message['aps']['alert'] = (string) $notification_message;
                $message['aps']['userId'] = (string) 1;
                $message['aps']['notificationType'] = (string) $notification_type;
                $message['aps']['notificationTypeId'] = (string) $notification_type_id;
                $result = send_notification_ios($device_token, $message, IOS_PEM_PATH, APP_IS_LIVE);
                if ($result) {
                    $this->my_response = array(
                        "status" => true,
                        "message" => lang("common_push_send_success"),
                    );
                } else {
                    $this->my_response = array(
                        "status" => false,
                        "message" => lang("common_push_send_fail"),
                    );
                }
            } else {
                $message = array();
                $message['android']['icon'] = 'appicon';
                $message['android']['vibrate'] = 'true';
                $message['android']['badge'] = 1;
                $message['android']['sound'] = "default";
                $message['android']['message'] = (string) $notification_message;
                $message['android']['userId'] = (string) 1;
                $message['android']['notificationType'] = (string) $notification_type;
                $message['android']['notificationTypeId'] = (string) $notification_type_id;
                $result = send_notification_android($device_token, $message, ANDROID_GCM_KEY);
                if ($result) {
                    $this->my_response = array(
                        "status" => true,
                        "message" => lang("common_push_send_success"),
                    );
                } else {
                    $this->my_response = array(
                        "status" => false,
                        "message" => lang("common_push_send_fail"),
                    );
                }
            }
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function send_pushwoosh_notification_post() { 
        $device_token = !empty($this->post_data['device_token']) ? $this->post_data['device_token'] : "";
        $notification_message = !empty($this->post_data['message']) ? $this->post_data['message'] : "";

        $message = array();
        $message['send_date'] = "now";
        // $message['link'] = $GLOBALS['ENV_VARS']['APP_NOTIFICATION_URL'] . '/staging';
        $message['content'] = $notification_message;
        $message["platforms"] = [10,11,12,13]; // optional. 1 — iOS; 2 — BB; 3 — Android; 5 — Windows Phone; 7 — OS X; 8 — Windows 8; 9 — Amazon; 10 — Safari; 11 — Chrome; 12 — Firefox; 13 - IE11; ignored if "devices" < 10
        $message["chrome_title"] = "MedSign"; // optional. You can specify the header of the message in this parameter.
        $message["firefox_title"] = "MedSign"; // optional. You can specify the header of the message in this parameter.
        $message["chrome_icon"] = $GLOBALS['ENV_VARS']['APP_NOTIFICATION_URL'] . '/staging/logo.png'; // full path URL to the icon or extension resources file path
        $message["firefox_icon"] = $GLOBALS['ENV_VARS']['APP_NOTIFICATION_URL'] . '/staging/logo.png'; // full path URL to the icon or extension resources file path
        $message["chrome_gcm_ttl"] = 3600; // optional. Time to live parameter – maximum message lifespan in seconds.
        $message["chrome_duration"] = 0; // optional. Changes chrome push display time. Set to 0 to display push until user interacts with it.
        $message["chrome_image"] = $GLOBALS['ENV_VARS']['APP_NOTIFICATION_URL'] . '/staging/logo.png'; // optional. URL to large image. 

        $message['data']['notification_list_type'] = 4;
        $message['data']['_flag'] = 1;
        $message['data']['_verified'] = 1;
        $message['devices'] = array($device_token);

        send_pushwoosh_notification($message);
        echo 'send';
        exit;
    }

    /**
     * 
     * This function use for testing push notification fot both device
     * 
     * @author Dipesh Shihora
     * 
     * Modified Date :- 2017-02-17
     * 
     */
    public function not_found_response() {
        $this->my_response = array(
            "status" => false,
            "message" => "Url Not Found",
        );
        $this->send_response(404);
    }

    /**
     * This function to get country code of telephone
     * 
     * 
     * 
     * 
     */
    public function get_country_code_post() {
        $field_name = array("country_id", "country_phonecode as country_phonecode", "country_name as country_name", "country_id as country_id");
        $result = $this->Common_model->get_country_code($field_name);
        if ($result) {
            $this->my_response = array(
                "status" => true,
                "message" => lang("get_country_code_success"),
                "data" => $result,
            );
        } else {
            $this->my_response = array(
                "status" => false,
                "message" => lang("get_country_code_fail"),
                "data" => array(),
            );
        }
        $this->send_response();
    }

    /**
     * This function is used to get the name of the states
     * 
     
     * 
     * 
     */
    public function get_states_post() {
        if (!empty($this->post_data['country_id'])) {
            $state_country_id = $this->post_data['country_id'];
        }else{
			$state_country_id = '101'; //default INDIA
		}
        $columns = 'state_id, state_name';
        $get_state_names = $this->Common_model->get_states($state_country_id, $columns);
        if ($get_state_names) {
            $this->my_response = array(
                "status" => true,
                "message" => lang("get_state_code_success"),
                "data" => $get_state_names,
            );
        } else {
            $this->my_response = array(
                "status" => false,
                "message" => lang('common_detail_not_found'),
                "data" => array(),
            );
        }
        $this->send_response();
    }

    /**
     * This function is used to get the name of the cities based on the id
     * 
     
     * 
     * 
     */
    public function get_cities_post() {
        $state_id = !empty($this->post_data['state_id']) ? trim($this->Common_model->escape_data($this->post_data['state_id'])) : "";
        if (empty($state_id)) {
            $this->bad_request();
            exit;
        }
        $columns = 'city_id, city_name';
        $get_state_names = $this->Common_model->get_cities($state_id, $columns);
        if ($get_state_names) {
            $this->my_response = array(
                "status" => true,
                "message" => lang("get_state_code_success"),
                "data" => $get_state_names,
            );
        } else {
            $this->my_response = array(
                "status" => false,
                "message" => lang('common_detail_not_found'),
                "data" => array(),
            );
        }
        $this->send_response();
    }

    /**
     * Description :- This function is used to get the name of the laboratories
     * 
     
     * 
     * 
     */
    public function get_laboratories_post() {

        $columns = 'laboratary_id, 
                    laboratary_name';

        $where = array(
            'laboratary_status' => 1
        );
        $get_laboratories = $this->Common_model->get_all_rows(TBL_LABORATORIES, $columns, $where);

        if (!empty($get_laboratories)) {
            $this->my_response = array(
                "status" => true,
                "message" => lang("common_detail_found"),
                "data" => $get_laboratories,
            );
        } else {
            $this->my_response = array(
                "status" => true,
                "message" => lang("common_detail_not_found"),
            );
        }
        $this->send_response();
    }

    /**
     * Description :- This function is used to get the name of the reports
     * 
     
     * 
     * 
     */
    public function get_reports_types_post() {

        $parent_id = !empty($this->post_data['parent_id']) ? trim($this->Common_model->escape_data($this->post_data['parent_id'])) : "";

        $columns = 'report_type_id, 
                    report_type_name, 
                    report_type_parent_id';

        $get_report_types = $this->Common_model->get_reports_types($parent_id,$columns);
        if (!empty($get_report_types)) {
            $this->my_response = array(
                "status" => true,
                "message" => lang("common_detail_found"),
                "data" => $get_report_types,
            );
        } else {
            $this->my_response = array(
                "status" => true,
                "message" => lang("common_detail_not_found"),
            );
        }
        $this->send_response();
    }

    /**
     * Description :- This function is used to get the name of the reports
     * 
     
     * 
     * 
     */
    public function get_health_anlaytics_test_post() {
        $parent_id = !empty($this->post_data['parent_id']) ? trim($this->Common_model->escape_data($this->post_data['parent_id'])) : "";
        $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : "";
        $search    = !empty($this->post_data['search']) ? trim($this->Common_model->escape_data($this->post_data['search'])) : "";
        $user_type = !empty($this->post_data['user_type']) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : "";
        $medsign_speciality_id = !empty($this->post_data['medsign_speciality_id']) ? trim($this->Common_model->escape_data($this->post_data['medsign_speciality_id'])) : "";
		$health_analytics_test_type = !empty($this->post_data['health_analytics_test_type']) ? trim($this->Common_model->escape_data($this->post_data['health_analytics_test_type'])) : "";
		
        $columns = 'health_analytics_test_id, 
                    health_analytics_test_name,
                    health_analytics_test_name_precise,
                    health_analytics_test_validation,
                    health_analytics_test_parent_id';
		
		$where = array('health_analytics_test_status' => 1);
		
		// if patient then send the child data only
        // if user then send send the data based on the parent id or only parent data
        $where['doctor_id'] = $doctor_id;
        if (!empty($parent_id)) {
            $where['health_analytics_test_parent_id'] = $parent_id;
        } else {
            $where['health_analytics_test_parent_id'] = 0;
        }
        if (!empty($search)) {
            $where['health_analytics_test_name'] = $search;
        }
        if (!empty($medsign_speciality_id)) {
            $where['medsign_speciality_id'] = $medsign_speciality_id;
        }
		if (!empty($health_analytics_test_type) && $health_analytics_test_type == 1) {
			$where['health_analytics_test_type'] = 1;
		}
        $get_health_analytics = $this->Common_model->get_health_anlaytics_test($columns, $where);
		if(!empty($parent_id)){
            $where_data = [
                'doctor_id' => $doctor_id,
                'parent_id' => $parent_id,
            ];
            if(!empty($get_health_analytics)) {
                $where_data['health_analytics_ids'] = array_column($get_health_analytics,'health_analytics_test_id');
            }
            $investigation_instructions = $this->Common_model->get_investigation_instruction($where_data);
            $instructions_data = [];
            foreach ($investigation_instructions as $key => $value) {
                $instructions_data[$value->health_analytics_test_id][] = $value;
            }
        }
        if (!empty($get_health_analytics)) {
            $this->my_response = array(
                "status" => true,
                "message" => lang("common_detail_found"),
                "data" => $get_health_analytics,
                "instructions_data" => $instructions_data,
            );
        } else {
            $this->my_response = array(
                "status" => true,
                "instructions_data" => $instructions_data,
                "message" => lang("common_detail_not_found"),
            );
        }
        $this->send_response();
    }
	
	public function search_investigation_instructions_post() {
        $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : "";
        $search = !empty($this->post_data['search']) ? trim($this->Common_model->escape_data($this->post_data['search'])) : "";
        $health_analytics_test_id = !empty($this->post_data['health_analytics_test_id']) ? trim($this->Common_model->escape_data($this->post_data['health_analytics_test_id'])) : "";
        $health_analytics_test_name = !empty($this->post_data['health_analytics_test_name']) ? trim($this->Common_model->escape_data($this->post_data['health_analytics_test_name'])) : "";
        if ((empty($health_analytics_test_id) && empty($health_analytics_test_name)) || empty($doctor_id) || empty($search)) {
            $this->bad_request();
            exit;
        }
        $where_data = [
            'doctor_id' => $doctor_id,
            'parent_id' => $health_analytics_test_id,
            'health_analytics_test_name' => $health_analytics_test_name,
            'search' => $search,
        ];
        $investigation_instructions = $this->Common_model->get_investigation_instruction($where_data);
        $this->my_response = array(
            "status" => true,
            "message" => lang("common_detail_found"),
            "data" => $investigation_instructions,
        );
        $this->send_response();
    }
    public function get_all_health_anlaytics_test_post() {
		$parent_id = !empty($this->post_data['parent_id']) ? trim($this->Common_model->escape_data($this->post_data['parent_id'])) : "";
        $search    = !empty($this->post_data['search']) ? trim($this->Common_model->escape_data($this->post_data['search'])) : "";
        $user_type = !empty($this->post_data['user_type']) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : "";
		$resourceType = !empty($this->post_data['resource_type']) ? trim($this->Common_model->escape_data($this->post_data['resource_type'])) : "";
		
        $columns = 'hat1.health_analytics_test_id, 
                    hat1.health_analytics_test_name,
                    hat1.health_analytics_test_name_precise,
                    hat1.health_analytics_test_validation,
                    hat1.health_analytics_test_parent_id,
					hat2.health_analytics_test_name AS health_analytics_parent_test_name';
		$where = array('hat1.health_analytics_test_status' => 1);
		if (!empty($parent_id)) {
			$where['hat1.health_analytics_test_parent_id'] = $parent_id;
		} else if(!empty($resourceType) && $resourceType == "all"){
			$where['hat1.health_analytics_test_parent_id!='] = 0;
		} else {
			$where['hat1.health_analytics_test_parent_id'] = 0;
		}
        if (!empty($search)) {
            $where['hat1.health_analytics_test_name LIKE'] = '%' . $search . '%';
        }
		$get_health_analytics = $this->Common_model->get_all_rows(TBL_HEALTH_ANALYTICS.' AS hat1', $columns, $where,[TBL_HEALTH_ANALYTICS.' AS hat2'=>'hat1.health_analytics_test_parent_id = hat2.health_analytics_test_id'],'','','left');
		
		if (!empty($get_health_analytics)) {
            $this->my_response = array(
                "status" => true,
                "message" => lang("common_detail_found"),
                "data" => $get_health_analytics,
            );
        } else {
            $this->my_response = array(
                "status" => true,
                "message" => lang("common_detail_not_found"),
            );
        }
        $this->send_response();
    }

    public function get_health_anlaytics_post() {

        $parent_id = !empty($this->post_data['parent_id']) ? trim($this->Common_model->escape_data($this->post_data['parent_id'])) : "";
        $search = !empty($this->post_data['search']) ? trim($this->Common_model->escape_data($this->post_data['search'])) : "";
        $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : "";
        $patient_id = !empty($this->post_data['patient_id']) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : "";
        $medsign_speciality_id = !empty($this->post_data['medsign_speciality_id']) ? trim($this->Common_model->escape_data($this->post_data['medsign_speciality_id'])) : "";

        $columns = 'health_analytics_test_id, 
                    health_analytics_test_name,
                    health_analytics_test_name_precise,
                    health_analytics_test_validation,
                    patient_analytics_doctor_id,
                    health_analytics_test_parent_id';

        $medsign_speciality_id_where = "";
        if(!empty($medsign_speciality_id)) {
            $find_where = '';
            foreach (explode(',', $medsign_speciality_id) as $speciality_id) {
                $find_where .= "FIND_IN_SET(".$speciality_id.", health_analytics_medsign_speciality_id) OR ";
            }
            $medsign_speciality_id_where = " AND (".trim($find_where, ' OR ').")";
        }
        if (!empty($parent_id)) {
            $get_analytics_query = "SELECT 
                                        " . $columns . "
                                    FROM 
                                        " . TBL_HEALTH_ANALYTICS . " A
                                    LEFT JOIN 
                                        " . TBL_PATIENT_ANALYTICS . "
                                    ON 
                                        patient_analytics_analytics_id = health_analytics_test_id 
                                        AND patient_analytics_status=1 
                                        AND patient_analytics_user_id='" . $patient_id . "'
                                    WHERE 
                                        health_analytics_test_status = 1
                                    AND 
                                        health_analytics_test_parent_id= '" . $parent_id . "' 
                                    ".$medsign_speciality_id_where." 
                                    GROUP BY 
                                        health_analytics_test_id";
        } else {
            $get_analytics_query = "SELECT 
                                        " . $columns . "
                                    FROM 
                                        " . TBL_HEALTH_ANALYTICS . " A
                                    LEFT JOIN 
                                        " . TBL_PATIENT_ANALYTICS . "
                                    ON 
                                        patient_analytics_status=1 AND
                                        patient_analytics_user_id='" . $patient_id . "' AND
                                        patient_analytics_analytics_id IN 
                                    (
                                    SELECT 
                                        health_analytics_test_id 
                                    FROM 
                                        " . TBL_HEALTH_ANALYTICS . "
                                    WHERE 
                                        health_analytics_test_parent_id=A.health_analytics_test_id
                                    )
                                    WHERE
                                        health_analytics_test_status = 1
                                    AND 
                                        health_analytics_test_parent_id=0 
									AND 
										health_analytics_test_type = 1 ";
            if (!empty($search)) {
                $get_analytics_query .= " AND health_analytics_test_name LIKE '%" . $search . "%' ";
            }
            if (!empty($medsign_speciality_id_where)) {
                $get_analytics_query .= $medsign_speciality_id_where;
            }
            $get_analytics_query .= " GROUP BY health_analytics_test_id ";
            if(!empty($medsign_speciality_id)) {
                $medsign_speciality_id_arr = explode(",", $medsign_speciality_id);
                $sort_data = INVESTIGATION_SORT_ARR[$medsign_speciality_id_arr[0]];
                $get_analytics_query .= " ORDER BY FIELD(health_analytics_medsign_speciality_id, ".$sort_data."),health_analytics_test_rank";
            }
        }

        $get_health_analytics = $this->Common_model->get_all_rows_by_query($get_analytics_query);

        if (!empty($get_health_analytics)) {

            foreach ($get_health_analytics as &$health) {
                $is_editable = 2;
                $is_checked = 2;
                if ($health['patient_analytics_doctor_id'] == $doctor_id) {
                    $is_editable = 1;
                }
                if (!empty($health['patient_analytics_doctor_id'])) {
                    $is_checked = 1;
                }
                $health['is_editable'] = (string) $is_editable;
                $health['is_checked'] = (string) $is_checked;
            }

            $this->my_response = array(
                "status" => true,
                "message" => lang("common_detail_found"),
                "data" => $get_health_analytics,
            );
        } else {
            $this->my_response = array(
                "status" => true,
                "message" => lang("common_detail_not_found"),
            );
        }
        $this->send_response();
    }

    /**
     * This function send colleges list and search also college.
     * 
     * 
     * 
     * 
     */
    public function get_colleges_post() {

        $search_college = !empty($this->post_data['search_college']) ? trim($this->Common_model->escape_data($this->post_data['search_college'])) : "";
        $columns = 'college_id, college_name';
        $get_all_colleges = $this->Common_model->get_colleges($search_college, $columns);

        if (!empty($get_all_colleges)) {
            $this->my_response = array(
                "status" => true,
                "message" => lang("common_college_found"),
                "data" => $get_all_colleges,
            );
        } else {
            $this->my_response = array(
                "status" => true,
                "message" => lang("common_college_not_found"),
            );
        }
        $this->send_response();
    }

    /**
     * This function send qualificationslist lists and search also qualifications.
     * 
     * 
     * 
     * 
     */
    public function get_qualifications_post() {

        $search_qualification = !empty($this->post_data['search_qualification']) ? trim($this->Common_model->escape_data($this->post_data['search_qualification'])) : "";

        $query = "SELECT
                    qualification_id, 
                    qualification_name 
                 FROM
                    " . TBL_QUALIFICATION . "
                 WHERE
                    qualification_status = 1";

        if (!empty($search_qualification)) {
            $query .= " AND qualification_name LIKE '%" . strtolower($search_qualification) . "%'";
        }

        $query .= " ORDER BY qualification_id ASC";
        if(IS_FILE_CACHING_ACTIVE == 1){
            create_cache_sub_dir('me_qualification');
            $hashObj = sha1($query);
            $this->load->driver('cache');
            if(!$resultCached = $this->cache->file->get($hashObj)){
                $get_all_qualifications = $this->Common_model->get_all_rows_by_query($query);
                $this->cache->file->save($hashObj, $get_all_qualifications, CACHE_TTL);
            }else{
                $get_all_qualifications = $resultCached;
            }
        }else{
            $get_all_qualifications = $this->Common_model->get_all_rows_by_query($query);
        }

        if (!empty($get_all_qualifications)) {
            $this->my_response = array(
                "status" => true,
                "message" => lang("common_qualification_found"),
                "data" => $get_all_qualifications,
            );
        } else {
            $this->my_response = array(
                "status" => true,
                "message" => lang("common_qualification_not_found"),
            );
        }
        $this->send_response();
    }

    /**
     * This function send councils lists and search also councils.
     * 
     * 
     * 
     * 
     */
    public function get_councils_post() {

        $search_council = !empty($this->post_data['search_council']) ? trim($this->Common_model->escape_data($this->post_data['search_council'])) : "";

        $query = "SELECT
                    council_id, 
                    council_name 
                 FROM
                    " . TBL_COUNCILS . "
                 WHERE
                    council_status = 1";

        if (!empty($search_council)) {
            $query .= " AND council_name LIKE '%" . strtolower($search_council) . "%'";
        }

        $query .= " ORDER BY council_id ASC";
        if(IS_FILE_CACHING_ACTIVE == 1){
            create_cache_sub_dir('me_councils');
            $hashObj = sha1($query);
            $this->load->driver('cache');
            if(!$resultCached = $this->cache->file->get($hashObj)){
                $get_all_councils = $this->Common_model->get_all_rows_by_query($query);
                $this->cache->file->save($hashObj, $get_all_councils, CACHE_TTL);
            }else{
                $get_all_councils = $resultCached;
            }
        }else{
            $get_all_councils = $this->Common_model->get_all_rows_by_query($query);
        }

        if (!empty($get_all_councils)) {
            $this->my_response = array(
                "status" => true,
                "message" => lang("common_councils_found"),
                "data" => $get_all_councils,
            );
        } else {
            $this->my_response = array(
                "status" => true,
                "message" => lang("common_councils_not_found"),
            );
        }
        $this->send_response();
    }

    /**
     * This function GET LANGUAGE lists .
     * 
     * 
     * 
     * 
     */
    public function get_language_post() {

        $query = "SELECT
                    language_id, 
                    language_code, 
                    language_name 
                 FROM
                    " . TBL_LANGUAGES . "
                 WHERE
                    language_status = 1";

        if(IS_FILE_CACHING_ACTIVE == 1){
            create_cache_sub_dir('me_languages');
            $hashObj = sha1($query);
            $this->load->driver('cache');
            if(!$resultCached = $this->cache->file->get($hashObj)){
                $get_lang_data = $this->Common_model->get_all_rows_by_query($query);
                $this->cache->file->save($hashObj, $get_lang_data, CACHE_TTL);
            }else{
                $get_lang_data = $resultCached;
            }
        }else{
            $get_lang_data = $this->Common_model->get_all_rows_by_query($query);
        }

        if (!empty($get_lang_data)) {
            $this->my_response = array(
                "status" => true,
                "message" => lang("common_language_found"),
                "data" => $get_lang_data,
            );
        } else {
            $this->my_response = array(
                "status" => true,
                "message" => lang("common_language_not_found"),
            );
        }
        $this->send_response();
    }

    /**
     * This function GET specialization lists and search also  search specialization.
     * 
     * 
     * 
     * 
     */
    public function get_specialization_post() {

        $search_specialization = !empty($this->post_data['search_specialization']) ? trim($this->Common_model->escape_data($this->post_data['search_specialization'])) : "";
        $parent_id = !empty($this->post_data['parent_id']) ? trim($this->Common_model->escape_data($this->post_data['parent_id'])) : 0;
        $flag = !empty($this->post_data['flag']) ? trim($this->Common_model->escape_data($this->post_data['flag'])) : 2;

        $query = "SELECT
                    specialization_id, 
                    specialization_title,
                    specialization_parent_id
                 FROM
                    " . TBL_SPECIALIZATION . "
                 WHERE
                    specialization_status = 1";

        if (!empty($search_specialization)) {
            $query .= " AND specialization_title LIKE '%" . strtolower($search_specialization) . "%'";
        }

        if (!empty($parent_id)) {
            $query .= " AND specialization_parent_id = '" . $parent_id . "' ";
        } else if ($flag == 2 && empty($parent_id)) {
            $query .= " AND specialization_parent_id = 0 ";
        } else if ($flag == 1) {
            $query .= " AND specialization_parent_id != 0 ";
        }
        $query .= " ORDER BY specialization_id ASC";
		if(IS_FILE_CACHING_ACTIVE == 1){
            create_cache_sub_dir('me_specialization');
            $hashObj = sha1($query);
            $this->load->driver('cache');
            if(!$resultCached = $this->cache->file->get($hashObj)){
                $get_specialization = $this->Common_model->get_all_rows_by_query($query);
                $this->cache->file->save($hashObj, $get_specialization, CACHE_TTL);
            }else{
                $get_specialization = $resultCached;
            }
        }else{
            $get_specialization = $this->Common_model->get_all_rows_by_query($query);
        }
		if (!empty($get_specialization)) {
            $this->my_response = array(
                "status" => true,
                "message" => lang("common_specialization_found"),
                "data" => $get_specialization,
            );
        } else {
            $this->my_response = array(
                "status" => true,
                "message" => lang("common_specialization_not_found"),
            );
        }
        $this->send_response();
    }

    /**
     * Description :- This function is used to get the contact information 
      one is global and one is based on the country id
     * 
     
     * 
     */
    public function get_support_contact_post() {
        try {

            $country = !empty($this->post_data['country']) ? trim($this->Common_model->escape_data($this->post_data['country'])) : 101;
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $flag = !empty($this->post_data['flag']) ? trim($this->Common_model->escape_data($this->post_data['flag'])) : '';
            
            $requested_data = array(
                'country_id' => $country,
                'flag' => $flag,
                'user_type' => !empty($this->post_data['user_type']) && ($this->post_data['user_type'] == '2') ? 2 : 1
            );

            $get_contact_us_data = $this->Common_model->get_contact_us_info($requested_data);
            $sub_plan_name = "";
            if(!empty($doctor_id)) {
                $this->load->model("subscription_model");
                $columns = 'spm.sub_plan_name';
                $sub_details = $this->subscription_model->get_doctor_subscription($doctor_id, $columns);
                if(!empty($sub_details->sub_plan_name))
                    $sub_plan_name = $sub_details->sub_plan_name;
            }
            if (!empty($get_contact_us_data)) {
                $this->my_response['status'] = true;
                foreach ($get_contact_us_data as $data) {

                    if (!empty($data['contact_us_country_id'])) {
                        $this->my_response['local'] = array(
                            "name" => $data['contact_us_name'],
                            "call" => $data['contact_number'],
                            "email" => $data['contact_us_email']
                        );
                    }

                    if (empty($data['contact_us_country_id'])) {
                        $this->my_response['global'] = array(
                            "name" => $data['contact_us_name'],
                            "call" => $data['contact_us_phone_number'],
                            "email" => $data['contact_us_email']
                        );
                    }
                }
            } else {
                $this->my_response = array(
                    "status" => false,
                    "local" => array(
                        "name" => "",
                        "call" => "",
                        "email" => "",
                    ),
                    "global" => array(
                        "name" => "",
                        "call" => "",
                        "email" => "",
                    ),
                );
            }
            $this->my_response['other_detail'] = array('sub_plan_name' => $sub_plan_name);
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to mail the admin if any user found any issues
     * 
     
     * 
     */
    public function add_issue_post() {
        try {
            $issue = !empty($this->post_data['issue']) ? trim($this->Common_model->escape_data($this->post_data['issue'])) : "";
            $issue_email = !empty($this->post_data['issue_email']) ? trim($this->Common_model->escape_data($this->post_data['issue_email'])) : "";
            $user_id = !empty($this->post_data['user_id']) ? trim($this->Common_model->escape_data($this->post_data['user_id'])) : "";

            if (empty($issue) || empty($issue_email) || empty($user_id)) {
                $this->bad_request();
            }

            $this->load->model("User_model");
            $get_user_details = $this->User_model->get_details_by_id($user_id);
            $user_name = '';
            if (!empty($get_user_details)) {
                $user_name = $get_user_details['user_first_name'] . " " . $get_user_details['user_last_name'];
            }

            $insert_issue = array(
                'user_issue_user_id' => $user_id,
                'user_issue_email' => $issue_email,
                'user_issue_message' => $issue,
                'user_issue_created_at' => $this->utc_time_formated,
                 'user_type' => 2,
            );
            $inserted_id = $this->Common_model->insert(TBL_USER_ISSUE, $insert_issue);

            $attachment_link = '';
            if ($inserted_id > 0) {
                if (isset($_FILES['attachment'])) {
                    $attachment_array = array();
                    $upload_path = UPLOAD_REL_PATH . "/" . ISSUE_FOLDER . "/" . $inserted_id;
                    $upload_folder = ISSUE_FOLDER . "/" . $inserted_id;
                    $attachment_name = do_upload_multiple3($upload_path, array('attachment' => $_FILES['attachment']), $upload_folder);
                    if (!empty($attachment_name)) {
                        foreach ($attachment_name as $attachment) {
                            $attachment_filepath = get_file_json_detail(ISSUE_FOLDER . '/' . $inserted_id . "/" . $attachment);
                            $attachment_array[] = array(
                                'user_issue_attachment_issue_id' => $inserted_id,
                                'user_issue_attachment_name' => $attachment,
                                'user_issue_attachment_filepath' => $attachment_filepath,
                                'user_issue_attachment_created_at' => $this->utc_time_formated
                            );
                            $attachment_link .= get_file_full_path($attachment_filepath);
                            $attachment_link .= "<br>";
                        }
                        $this->Common_model->insert_multiple(TBL_USER_ISSUE_ATTACHMENT, $attachment_array);
                    }
                }
            }

            $send_issue_mail = array(
                'user_name' => $user_name,
                'user_email' => ADMIN_EMAIL,
                'issue' => $issue,
                'template_id' => 16,
                'issue_email' => $issue_email,
                'attachment_link' => $attachment_link
            );
            $cron_job_path = CRON_PATH . " notification/send_mail_background/" . base64_encode(json_encode($send_issue_mail));
            exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");

            $revert_mail = array(
                'user_name' => $user_name,
                'template_id' => 25,
                'user_email' => $issue_email
            );
            $cron_job_path = CRON_PATH . " notification/send_mail_background/" . base64_encode(json_encode($revert_mail));
            exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");

            $this->my_response['status'] = true;
            $this->my_response['message'] = lang('issue_send');
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to get list of the  news  added by admin
     * 
     
     * 
     */
    public function get_whats_new_data_post() {
        try {
            $get_news = $this->Common_model->get_news();
            if (!empty($get_news)) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = true;
                $this->my_response['data'] = $get_news;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('news_not_found');
            }

            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to get list of the  diesease  added by admin
     * 
     
     * 
     */
    public function get_diseases_post() {

        try {
            $columns = 'disease_id, disease_name';
            $get_diseases = $this->Common_model->get_diseases($columns);
            if (!empty($get_diseases)) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = true;
                $this->my_response['data'] = $get_diseases;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('common_detail_not_found');
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to get the lab test.
     * 
     
     * 
     */
    public function get_lab_test_post() {
        try {

            $search = !empty($this->Common_model->escape_data($this->post_data['search'])) ? trim($this->Common_model->escape_data($this->post_data['search'])) : '';

            $where = array(
                'lab_test_status' => 1
            );

            if (!empty($search)) {
                $where['lab_test_name LIKE'] = "%" . $search . "%";
            }

            $columns = 'lab_test_id, lab_test_name';

            $get_lab_test = $this->Common_model->get_all_rows(TBL_LAB_TEST, $columns, $where);

            if (!empty($get_lab_test)) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $get_lab_test;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('common_detail_not_found');
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to get the prescription print setting of the user
     * 
     * 
     */
    public function get_prescription_print_setting_post() {
        try {
            $clinic_id = $this->post_data['clinic_id'];
            $doctor_id = $this->post_data['doctor_id'];
            $data = !empty($this->post_data['data']) ? $this->post_data['data'] : '';
            if (empty($clinic_id) || empty($doctor_id)) {
                $this->bad_request();
                exit;
            }
            $setting_where = array(
                'setting_user_id' => $doctor_id,
                'setting_clinic_id' => $clinic_id,
                'setting_type' => PRESCRIPTION_PRINT_SETTING_TYPE
            );
            $get_setting_data = $this->Common_model->get_single_row(TBL_SETTING, 'setting_data', $setting_where);
            if (!empty($get_setting_data) && count($get_setting_data) > 0) {
                $this->my_response = array(
                    "status" => true,
                    "message" => "success",
                    "prescription_print_setting_data" => json_decode($get_setting_data['setting_data'])
                );
            } else {
                $this->my_response = array(
                    "status" => false,
                    "prescription_print_setting_data" => array(),
                    "message" => lang('common_detail_not_found')
                );
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to set the prescription print setting of the user
     * 
     * 
     */
    public function set_prescription_print_setting_post() {
        try {
            $clinic_id = $this->post_data['clinic_id'];
            $doctor_id = $this->post_data['doctor_id'];
            $data = !empty($this->post_data['data']) ? $this->post_data['data'] : '';
            $share_setting_data = !empty($this->post_data['share_setting_data']) ? $this->post_data['share_setting_data'] : '';
            $sign_photo_base64 = !empty($this->post_data['signature_img']) ? $this->post_data['signature_img'] : '';
            if (empty($clinic_id) || empty($doctor_id)) {
                $this->bad_request();
                exit;
            }
            if(!empty($data)) {
                if (!empty($_FILES['logo_img']['name']) && $_FILES['logo_img']['error'] == 0) {
                    $upload_path = UPLOAD_REL_PATH . "/" . RX_PRINT_LOGO . "/" . $doctor_id;
                    $upload_folder = RX_PRINT_LOGO . "/" . $doctor_id;
                    $logo_image = do_upload_multiple($upload_path, array('logo_img' => $_FILES['logo_img']), $upload_folder,300,300);
                    if(!empty($logo_image['logo_img'])) {
                        $logo_img_path = get_file_json_detail(RX_PRINT_LOGO . "/" . $doctor_id . "/" . $logo_image['logo_img']);
                    }
                }
                if (!empty($_FILES['watermark_img']['name']) && $_FILES['watermark_img']['error'] == 0) {
                    $upload_path = UPLOAD_REL_PATH . "/" . RX_PRINT_WATERMARK . "/" . $doctor_id;
                    $upload_folder = RX_PRINT_WATERMARK . "/" . $doctor_id;
                    $watermark_image = do_upload_multiple($upload_path, array('watermark_img' => $_FILES['watermark_img']), $upload_folder,2480,3508,"20%");
                    if(!empty($watermark_image['watermark_img'])) {
                        $watermark_img_path = get_file_json_detail(RX_PRINT_WATERMARK . "/" . $doctor_id . "/" . $watermark_image['watermark_img']);
                    }
                }
                if(!is_array($data))
                    $data = json_decode($data, true);
                if(!empty($logo_img_path))
                    $data['logo_img_path'] = $logo_img_path;
                if(!empty($watermark_img_path))
                    $data['watermark_img_path'] = $watermark_img_path;
                $setting_where = array(
                    'setting_user_id'   => $doctor_id,
                    'setting_clinic_id' => $clinic_id,
                    'setting_type' 		=> PRESCRIPTION_PRINT_SETTING_TYPE
                );
                $get_setting_data = $this->Common_model->get_single_row(TBL_SETTING, 'setting_id, setting_data', $setting_where);
                $settingDataArr = [];
                if(!empty($get_setting_data) && count($get_setting_data)) {
                    $setting_data = json_decode($get_setting_data['setting_data'],true);
                    if(!empty($setting_data[0])) {
                        $key = array_search($data['appointment_type'], array_column($setting_data, 'appointment_type'));
                        if(is_numeric($key) && isset($key) && $key >= 0) {
                            $setting_data[$key] = $data;
                        } else {
                            $setting_data[] = $data;
                        }
                        $settingDataArr = $setting_data;
                    } else {
                        if(!empty($setting_data) && $data['appointment_type'] == "5") {
                            $setting_data['template_id'] = "1";
                            $setting_data['appointment_type'] = "1";
                            $settingDataArr[] = $setting_data;
                            $settingDataArr[] = $data;
                        } else {
                            $settingDataArr[] = $data;
                        }
                    }
                    $update_setting_data = array(
                        'setting_data' => json_encode($settingDataArr),
                        'setting_updated_at' => $this->utc_time_formated
                    );
                    $update_setting_where = array(
                        'setting_id' => $get_setting_data['setting_id']
                    );
                    $this->Common_model->update(TBL_SETTING, $update_setting_data, $update_setting_where);
                } else {
                    $settingDataArr[] = $data;
                    $insert_setting_array = array(
                        'setting_user_id' => $doctor_id,
                        'setting_clinic_id' => $clinic_id,
                        'setting_data' => json_encode($settingDataArr),
                        'setting_type' => PRESCRIPTION_PRINT_SETTING_TYPE,
                        'setting_data_type' => 1,
                        'setting_created_at' => $this->utc_time_formated
                    );
                    $insert_setting_array['setting_created_at'] = $this->utc_time_formated;
                    $inserted_id = $this->Common_model->insert(TBL_SETTING, $insert_setting_array);
                }
                /*signature upload*/
                if (!empty($_FILES['upload_signature_img']['name']) && $_FILES['upload_signature_img']['error'] == 0) {
                    $upload_path = UPLOAD_REL_PATH . "/" . USER_SIGN_FOLDER . "/" . $doctor_id;
                    $upload_folder = USER_SIGN_FOLDER . "/" . $doctor_id;
                    $signature_img = do_upload_multiple($upload_path, array('upload_signature_img' => $_FILES['upload_signature_img']), $upload_folder,150);
                    if(!empty($signature_img['upload_signature_img'])) {
                        $upload_signature_img_path = get_file_json_detail(USER_SIGN_FOLDER . "/" . $doctor_id . "/" . $signature_img['upload_signature_img']);
                        $update_data = [];
                        $update_data['user_sign_filepath'] = $upload_signature_img_path;
                        $update_where = array(
                            'user_id' => $doctor_id
                        );
                        $this->Common_model->update(TBL_USERS, $update_data, $update_where);
                        $update_data['user_sign_filepath_thumb'] = get_image_thumb($update_data['user_sign_filepath']);
                        $this->my_response['data'] = $update_data;
                    }
                } elseif(!empty($sign_photo_base64)) {
                    $upload_path = UPLOAD_REL_PATH . "/" . USER_SIGN_FOLDER . "/" . $doctor_id;
                    $upload_folder = USER_SIGN_FOLDER . "/" . $doctor_id;
                    if (!file_exists($upload_path)) {
                        mkdir($upload_path, 0777, TRUE);
                        chmod($upload_path, 0777);
                    }
                    $file_name = uniqid() . '.png';
                    $file = $upload_path. '/' . $file_name;
                    $image_parts = explode(";base64,", $sign_photo_base64);
                    $image_type_aux = explode("image/", $image_parts[0]);
                    $image_type = $image_type_aux[1];
                    $image_base64 = base64_decode($image_parts[1]);
                    file_put_contents($file, $image_base64);
                    $is_upload = upload_import_report($file,$upload_folder . '/' . $file_name, 150);
                    if($is_upload) {
                        $update_data = [];
                        $update_data['user_sign_filepath'] = get_file_json_detail($upload_folder . "/" . $file_name);
                        $update_where = array(
                            'user_id' => $doctor_id
                        );
                        if(!IS_SERVER_UPLOAD){
                            unlink($file);
                            unlink(get_thumb_filename($file));
                        }
                        $this->Common_model->update(TBL_USERS, $update_data, $update_where);
                        $update_data['user_sign_filepath_thumb'] = get_image_thumb($update_data['user_sign_filepath']);
                        $this->my_response['data'] = $update_data;
                    }
                }
                if(!empty($share_setting_data)) {
                    $get_setting = $this->Common_model->get_single_row(TBL_SETTING, 'setting_id', ['setting_user_id' => $doctor_id,'setting_clinic_id' => $clinic_id,'setting_type' => 1]);
                    if(!empty($get_setting) && count($get_setting) > 0) {
                        $update_setting_data = array(
                            'setting_data' => $share_setting_data,
                            'setting_updated_at' => $this->utc_time_formated
                        );
                        $update_setting_where = array(
                            'setting_id' => $get_setting['setting_id']
                        );
                        $this->Common_model->update(TBL_SETTING, $update_setting_data, $update_setting_where);
                    } else {
                        $insert_setting_array = array(
                            'setting_user_id' => $doctor_id,
                            'setting_clinic_id' => $clinic_id,
                            'setting_data' => $rx_setting,
                            'setting_type' => 1,
                            'setting_data_type' => 1,
                            'setting_created_at' => $this->utc_time_formated
                        );
                        $inserted_id = $this->Common_model->insert(TBL_SETTING, $insert_setting_array);
                    }
                }
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('setting_set');
            } else {
                $this->my_response = array(
                    "status" => false,
                    "message" => lang("mycontroller_bad_request")
                );
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }
    /**
     * Description :- This function is used to set the setting of the user
     * 
     * 
     */
    public function set_setting_post() {
        try {
            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $setting_type = !empty($this->Common_model->escape_data($this->post_data['setting_type'])) ? trim($this->Common_model->escape_data($this->post_data['setting_type'])) : '';
            $setting_data_type = !empty($this->Common_model->escape_data($this->post_data['setting_data_type'])) ? trim($this->Common_model->escape_data($this->post_data['setting_data_type'])) : '';
            $user_type = !empty($this->Common_model->escape_data($this->post_data['user_type'])) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : '';
            $data = !empty($this->post_data['data']) ? $this->post_data['data'] : '';
            $patient_data = !empty($this->post_data['patient_data']) ? $this->post_data['patient_data'] : '';
            $rx_setting = !empty($this->post_data['rx_setting']) ? $this->post_data['rx_setting'] : '';
            if (empty($setting_type) ||
                    empty($setting_data_type)
            ) {
                $this->bad_request();
            }

            $add_permission = 1;
            $edit_permission = 1;

            if ($user_type == 2) {
                $get_role_details = $this->Common_model->get_the_role($this->user_id);
                if (!empty($get_role_details['user_role_data'])) {
                    $permission_data = array(
                        'role_data' => $get_role_details['user_role_data']
                    );
                    // 3 = if setting call for notification settings
                    // 2 = if setting call for the data security                    
                    if ($setting_type == 3) {
                        $permission_data['module'] = 14;
                    } else if ($setting_type == 2) {
                        $permission_data['module'] = 17;
                    } else if ($setting_type == 1) {
                        $permission_data['module'] = 21;
                    }
                    $permission_data['key'] = 1;
                    $check_add_permission = $this->check_module_permission($permission_data);
                    if ($check_add_permission == 2) {
                        $add_permission = 1;
                    }
                    $permission_data['key'] = 2;
                    $check_edit_permission = $this->check_module_permission($permission_data);
                    if ($check_edit_permission == 2) {
                        $edit_permission = 1;
                    }
                }
            }

            if (!in_array($setting_type, $this->setting_type)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('mycontroller_invalid_request');
                $this->send_response();
            }

            if (!in_array($setting_data_type, $this->setting_data_type)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('mycontroller_invalid_request');
                $this->send_response();
            }
            if(!empty($rx_setting)) {
                $get_rx_setting = $this->Common_model->get_single_row(TBL_SETTING, 'setting_id', ['setting_user_id' => $this->user_id,'setting_type' => 6]);
                if(!empty($get_rx_setting) && count($get_rx_setting) > 0) {
                    $update_rx_setting_data = array(
                        'setting_data' => $rx_setting,
                        'setting_type' => 6,
                        'setting_data_type' => 1,
                        'setting_updated_at' => $this->utc_time_formated
                    );
                    $update_rx_setting_where = array(
                        'setting_id' => $get_rx_setting['setting_id']
                    );
                    $this->Common_model->update(TBL_SETTING, $update_rx_setting_data, $update_rx_setting_where);
                } else {
                    $insert_rx_setting_array = array(
                        'setting_user_id' => $this->user_id,
                        'setting_clinic_id' => 0,
                        'setting_data' => $rx_setting,
                        'setting_type' => 6,
                        'setting_data_type' => 1,
                        'setting_created_at' => $this->utc_time_formated
                    );
                    $inserted_id = $this->Common_model->insert(TBL_SETTING, $insert_rx_setting_array);
                }
            }
            $setting_where = array(
                'setting_user_id' => $this->user_id,
                'setting_clinic_id' => $clinic_id,
                'setting_type' => $setting_type,
            );

            $get_setting_data = $this->Common_model->get_single_row(TBL_SETTING, 'setting_id', $setting_where);
            if(!empty($patient_data) && $edit_permission == 1) {
                $update_patient_data = array(
                    'setting_data' => $patient_data,
                    'setting_updated_at' => $this->utc_time_formated
                );
                $update_patient_where = array(
                    'setting_user_id' => $this->user_id,
                    'setting_type' => 12,
                );
                $this->Common_model->update(TBL_SETTING, $update_patient_data, $update_patient_where);
            }
            if (!empty($get_setting_data['setting_id']) &&
                    $edit_permission == 1
            ) {

                $update_setting_data = array(
                    'setting_data' => $data,
                    'setting_type' => $setting_type,
                    'setting_data_type' => $setting_data_type,
                    'setting_updated_at' => $this->utc_time_formated
                );

                $update_setting_where = array(
                    'setting_id' => $get_setting_data['setting_id']
                );

                $is_update = $this->Common_model->update(TBL_SETTING, $update_setting_data, $update_setting_where);

                if ($is_update > 0) {
                    $this->my_response['status'] = true;
                    $this->my_response['message'] = lang('setting_set');
                } else {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('failure');
                }
            } else if ($add_permission == 1) {

                $insert_setting_array = array(
                    'setting_user_id' => $this->user_id,
                    'setting_clinic_id' => $clinic_id,
                    'setting_data' => $data,
                    'setting_type' => $setting_type,
                    'setting_data_type' => $setting_data_type,
                    'setting_created_at' => $this->utc_time_formated
                );

                $inserted_id = $this->Common_model->insert(TBL_SETTING, $insert_setting_array);

                if ($inserted_id > 0) {
                    $this->my_response['status'] = true;
                    $this->my_response['message'] = lang('setting_set');
                } else {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('failure');
                }
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('permission_error');
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function set_staff_setting_post() {

        try {

            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $staff_id = !empty($this->Common_model->escape_data($this->post_data['staff_id'])) ? trim($this->Common_model->escape_data($this->post_data['staff_id'])) : '';
            $setting_type = !empty($this->Common_model->escape_data($this->post_data['setting_type'])) ? trim($this->Common_model->escape_data($this->post_data['setting_type'])) : '';
            $setting_data_type = !empty($this->Common_model->escape_data($this->post_data['setting_data_type'])) ? trim($this->Common_model->escape_data($this->post_data['setting_data_type'])) : '';
            $user_type = !empty($this->Common_model->escape_data($this->post_data['user_type'])) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : '';
            $data = !empty($this->post_data['data']) ? $this->post_data['data'] : '';

            if (empty($setting_type) ||
                    empty($setting_data_type)
            ) {
                $this->bad_request();
            }

            $add_permission = 1;
            $edit_permission = 1;

            if ($user_type == 2) {
                $get_role_details = $this->Common_model->get_the_role($this->user_id);
                if (!empty($get_role_details['user_role_data'])) {

                    $permission_data = array(
                        'role_data' => $get_role_details['user_role_data']
                    );

                    // 3 = if setting call for notification settings
                    // 2 = if setting call for the data security                    
                    if ($setting_type == 3) {
                        $permission_data['module'] = 14;
                    } else if ($setting_type == 2) {
                        $permission_data['module'] = 17;
                    } else if ($setting_type == 1) {
                        $permission_data['module'] = 21;
                    }

                    $permission_data['key'] = 1;
                    $check_add_permission = $this->check_module_permission($permission_data);
                    if ($check_add_permission == 2) {
                        $add_permission = 1;
                    }
                    $permission_data['key'] = 2;
                    $check_edit_permission = $this->check_module_permission($permission_data);
                    if ($check_edit_permission == 2) {
                        $edit_permission = 1;
                    }
                }
            }

            if (!in_array($setting_type, $this->setting_type)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('mycontroller_invalid_request');
                $this->send_response();
            }

            if (!in_array($setting_data_type, $this->setting_data_type)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('mycontroller_invalid_request');
                $this->send_response();
            }

            $setting_where = array(
                'setting_user_id' => $staff_id,
                'setting_type' => $setting_type
            );

//            if (!empty($clinic_id)) {
//                $setting_where['setting_clinic_id'] = $clinic_id;
//            }

            $get_setting_data = $this->Common_model->get_single_row(TBL_SETTING, 'setting_id', $setting_where);

            if (!empty($get_setting_data['setting_id']) &&
                    $edit_permission == 1
            ) {

                $update_setting_data = array(
                    'setting_data' => $data,
                    'setting_type' => $setting_type,
                    'setting_data_type' => $setting_data_type,
                    'setting_updated_at' => $this->utc_time_formated
                );

                $update_setting_where = array(
                    'setting_id' => $get_setting_data['setting_id']
                );

                $is_update = $this->Common_model->update(TBL_SETTING, $update_setting_data, $update_setting_where);

                if ($is_update > 0) {
                    $this->my_response['status'] = true;
                    $this->my_response['message'] = lang('setting_set');
                } else {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('failure');
                }
            } else if ($add_permission == 1) {

                $insert_setting_array = array(
                    'setting_user_id' => $staff_id,
                    'setting_clinic_id' => $clinic_id,
                    'setting_data' => $data,
                    'setting_type' => $setting_type,
                    'setting_data_type' => $setting_data_type,
                    'setting_created_at' => $this->utc_time_formated
                );

                $inserted_id = $this->Common_model->insert(TBL_SETTING, $insert_setting_array);

                if ($inserted_id > 0) {
                    $this->my_response['status'] = true;
                    $this->my_response['message'] = lang('setting_set');
                } else {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('failure');
                }
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('permission_error');
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to get the setting of the user
     * 
     
     * 
     */
    public function get_setting_post() {
        try {
            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $setting_type = !empty($this->post_data['setting_type']) ? $this->post_data['setting_type'] : '';
            $user_type = !empty($this->Common_model->escape_data($this->post_data['user_type'])) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : '';

            if (empty($this->user_id) || empty($setting_type)){
                $this->bad_request();
            }

            if ($user_type == 2) {
                $get_role_details = $this->Common_model->get_the_role($this->user_id);
                if (!empty($get_role_details['user_role_data'])) {
                    $permission_data = array(
                        'role_data' => $get_role_details['user_role_data'],
                        'key' => 3
                    );

                    // 3 = if setting call for notification settings
                    // 2 = if setting call for the data security         
                    // 1 = Share record
                    if ($setting_type == 3) {
                        $permission_data['module'] = 14;
                    } else if (is_array($setting_type) && in_array(2, $setting_type)) {
                        $permission_data['module'] = 17;
                    } else if ($setting_type == 1) {
                        $permission_data['module'] = 21;
                    }

                    $check_module_permission = $this->check_module_permission($permission_data);
                    if ($check_module_permission == 2) {
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang('permission_error');
                        $this->send_response();
                    }
                }
            }

            $setting_where = array(
                'setting_user_id' => $this->user_id,
                'setting_type' => $setting_type
            );

			if (!empty($clinic_id)) {
			   $setting_where['setting_clinic_id'] = $clinic_id;
			}
            if(is_array($setting_type) && count($setting_type) > 0)
                $get_setting_data = $this->Common_model->get_user_setting($setting_where);
            else
                $get_setting_data = $this->Common_model->get_single_row(TBL_SETTING, '', $setting_where);
            if($setting_type == 1) {
                $setting_data_arr = json_decode($get_setting_data['setting_data']);
                $arr = array_column($setting_data_arr, 'name','id');
                if(empty($arr[8])){
                    $res = array_slice($setting_data_arr, 0, 2, true) +
                            array(8 => ['id'=>8,'name'=>'Only Diagnosis','status'=>'2']) +
                            array_slice($setting_data_arr, 2, count($setting_data_arr) - 1, true) ;
                    $get_setting_data['setting_data'] = json_encode(array_values($res));
                }
                $setting_data_arr = json_decode($get_setting_data['setting_data']);
                $arr = array_column($setting_data_arr, 'name','id');
                if(empty($arr[9])){
                    $res = array_slice($setting_data_arr, 0, 4, true) +
                            array(9 => ['id'=>9,'name'=>'Generic','status'=>'2']) +
                            array_slice($setting_data_arr, 4, count($setting_data_arr) - 1, true) ;
                    $get_setting_data['setting_data'] = json_encode(array_values($res));
                }
            }
            if($setting_type == 3) {
                $setting_data_arr = json_decode($get_setting_data['setting_data']);
                $arr = array_column($setting_data_arr, 'name','id');
                if(empty($arr[21])) {
                    $whasapp_setting = [];
                    $whasapp_setting[] = (object)[
                        'id' => 21,
                        'name' => 'patient_app_whatsapp_status',
                        'status' => "1",
                    ];
                    $whasapp_setting[] = (object)[
                        'id' => 22,
                        'name' => 'patient_can_whatsapp_status',
                        'status' => "1",
                    ];
                    $whasapp_setting[] = (object)[
                        'id' => 23,
                        'name' => 'patient_reschedule_whatsapp_status',
                        'status' => "1",
                    ];
                    $setting_data_arr = array_merge($setting_data_arr, $whasapp_setting);
                    $get_setting_data['setting_data'] = json_encode($setting_data_arr);
                }
                if(empty($arr[24])) {
                    $google_sync_setting = [];
                    $google_sync_setting[] = (object)[
                        'id' => 24,
                        'name' => 'google_sync_status',
                        'status' => "1",
                    ];
                    $setting_data_arr = array_merge($setting_data_arr, $google_sync_setting);
                    $get_setting_data['setting_data'] = json_encode($setting_data_arr);
                }
                if(empty($arr[25])) {
                    $whasapp_setting = [];
                    $whasapp_setting[] = (object)[
                        'id' => 25,
                        'name' => 'patient_book_notification_status',
                        'status' => "1",
                    ];
                    $whasapp_setting[] = (object)[
                        'id' => 26,
                        'name' => 'patient_cancel_notification_status',
                        'status' => "1",
                    ];
                    $whasapp_setting[] = (object)[
                        'id' => 27,
                        'name' => 'patient_reschedule_notification_status',
                        'status' => "1",
                    ];
                    $setting_data_arr = array_merge($setting_data_arr, $whasapp_setting);
                    $get_setting_data['setting_data'] = json_encode($setting_data_arr);
                }
                $setting_where = array(
                    'setting_user_id' => $this->user_id,
                    'setting_type' => 12 //Patient register welcome email sms setting
                );
                $get_patient_setting_data = $this->Common_model->get_single_row(TBL_SETTING, '', $setting_where);
                if(empty($get_patient_setting_data)){
                    $patient_setting_data = [
                        ["id"=>1, "name"=>"patient_register_sms_status", "status"=>"2"],
                        ["id"=>2, "name"=>"patient_register_email_status", "status"=>"2"],
                        ["id"=>3, "name"=>"patient_register_whatsapp_status", "status"=>"2"],
                    ];
                    $insert_setting_array = array(
                        'setting_user_id' => $this->user_id,
                        'setting_data' => json_encode($patient_setting_data),
                        'setting_type' => 12,
                        'setting_data_type' => 1,
                        'setting_created_at' => $this->utc_time_formated
                    );
                    $this->Common_model->insert(TBL_SETTING, $insert_setting_array);
                    $get_patient_setting_data = $this->Common_model->get_single_row(TBL_SETTING, '', $setting_where);
                }
            }
            if(!empty($get_patient_setting_data))
                $this->my_response['patient_setting_data'] = $get_patient_setting_data;
            if (!empty($get_setting_data)) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $get_setting_data;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('common_detail_not_found');
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This is the test function for sending the sms
     * 
     
     * 
     */
    public function send_sms_post() {

        $phone_number = !empty($this->Common_model->escape_data($this->post_data['phone_number'])) ? trim($this->Common_model->escape_data($this->post_data['phone_number'])) : '';
        $message = !empty($this->Common_model->escape_data($this->post_data['message'])) ? trim($this->Common_model->escape_data($this->post_data['message'])) : '';
        $country_code = '+91';

        if (empty($phone_number) || empty($message)) {
            $this->bad_request();
        }

        $send_message = array(
            'phone_number' => $country_code . $phone_number,
            'message' => $message,
        );
        $sening_sms = send_message_by_vibgyortel($send_message);

        if ($sening_sms) {
            $this->my_response['status'] = true;
            $this->my_response['message'] = "Message send successfully";
        } else {
            $this->my_response['status'] = false;
            $this->my_response['message'] = "Not able to send the sms";
        }

        $this->send_response();
    }

    /**
     * Description :- This is the test function for sending the email
     * 
     
     * 
     */
    public function send_email_post() {
        $email = !empty($this->Common_model->escape_data($this->post_data['email_id'])) ? trim($this->Common_model->escape_data($this->post_data['email_id'])) : '';

        if (empty($email)) {
            $this->bad_request();
        }

        $reset_token = str_rand_access_token(20);
        $verify_link = DOMAIN_URL . "verifyaccount/" . $reset_token;
        //this is use for get view and store data in variable
        //EMAIL TEMPLATE START BY PRAGNESH
        $this->load->model('Emailsetting_model');
        $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(1);
        $parse_arr = array(
            '{Email}' => 'test@test.com',
            '{Password}' => '123456',
            '{EmailPasswordImage}' => 1,
            '{UniqueId}' => '111111',
            '{VerificationLink}' => '<a href="' . $verify_link . '">' . $verify_link . '</a>',
            '{WebUrl}' => '<a href="' . DOMAIN_URL . '">' . DOMAIN_URL . '</a>',
            '{AppName}' => APP_NAME,
        );
        $message = replace_values_in_string($parse_arr, $email_template_data['email_template_message']);
        $subject = $email_template_data['email_template_subject'];
        //EMAIL TEMPLATE END BY PRAGNESH
        //this function help you to send mail to single ot multiple users
        //$this->send_email(array($email => $email), $subject, $message);

        $this->my_response['status'] = true;
        $this->my_response['message'] = "Mail send successfully.";
        $this->send_response();
    }

    /**
     * Description :- This function is used to get the video of each page
     * 
     
     * 
     */
    public function get_video_post() {

        try {
            $page_id = !empty($this->Common_model->escape_data($this->post_data['page_id'])) ? trim($this->Common_model->escape_data($this->post_data['page_id'])) : '';

            if (empty($page_id)) {
                $this->bad_request();
            }

            $where = array(
                'me_video_page' => $page_id,
                'me_video_status' => 1
            );

            $columns = 'me_video_id, me_video_url, me_video_page, me_video_title';
            $this->db->select($columns)->from(TBL_VIDEO);
            $this->db->where('me_video_status', 1);
            $this->db->where('FIND_IN_SET('.$page_id.',me_video_page) !=', 0);
            $query = $this->db->get();
            $get_video_data = $query->result_array();
            // $get_video_data = $this->Common_model->get_all_rows(TBL_VIDEO, $columns, $where);
            $update_key = array();
            foreach ($get_video_data as $key => $value) {
                $get_video_data[$key]['key'] = encrypt_decrypt($value['me_video_id'], 'encrypt');
            }

            if (!empty($get_video_data)) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $get_video_data;
            } else {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_not_found');
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to get the procedure listing
     * 
     
     * 
     */
    public function get_procedure_post() {

        try {
            $search = !empty($this->Common_model->escape_data($this->post_data['search'])) ? trim($this->Common_model->escape_data($this->post_data['search'])) : '';
            $columns = 'procedure_id, procedure_title';
            $get_procedure_data = $this->Common_model->get_procedure($search, $columns);

            if (!empty($get_procedure_data)) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $get_procedure_data;
            } else {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_not_found');
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to get the content of the static page
     * 
     
     * 
     */
    public function static_page_post() {
        try {
            $flag = !empty($this->Common_model->escape_data($this->post_data['flag'])) ? trim($this->Common_model->escape_data($this->post_data['flag'])) : '';

            $where = array(
                'static_page_id' => $flag
            );

            $columns = 'static_page_content, static_page_title';

            $get_static_data = $this->Common_model->get_single_row(TBL_STATIC_PAGE, $columns, $where);

            if (!empty($get_static_data)) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $get_static_data;
            } else {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_not_found');
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to get the sidebar menu for doctor based on the role
     * 
     
     * 
     */
    public function get_sidebar_menu_post() {
        try {
            //get the role of the user
            $where = array(
                'doctor_clinic_mapping_user_id' => $this->user_id,
                'doctor_clinic_mapping_status' => 1
            );
            $get_role = $this->Common_model->get_single_row(TBL_DOCTOR_CLINIC_MAPPING, 'doctor_clinic_mapping_role_id', $where);

            if (!empty($get_role)) {

                $menu_where = array(
                    'menu_status' => 1,
                    'menu_role_id' => $get_role['doctor_clinic_mapping_role_id']
                );

                $get_menu_detail = $this->Common_model->get_single_row(TBL_MENU, 'menu_data', $menu_where);

                if (!empty($get_menu_detail)) {

                    $get_role_detail = $this->Common_model->get_the_role($this->user_id);

                    if (!empty($get_role_detail)) {
                        $this->my_response['status'] = true;
                        $this->my_response['message'] = lang('common_detail_found');
                        $this->my_response['data'] = array(
                            'sidebar_menu' => $get_menu_detail['menu_data'],
                            'role' => $get_role_detail['user_role_data']
                        );
                        $this->my_response['role_type'] = $get_role['doctor_clinic_mapping_role_id'];
                    } else {
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang('common_detail_not_found');
                    }
                } else {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('common_detail_not_found');
                }
            } else {

                $menu_where = array(
                    'menu_status' => 1,
                    'menu_role_id' => 1
                );

                $get_menu_detail = $this->Common_model->get_single_row(TBL_MENU, 'menu_data', $menu_where);

                $role_where = array(
                    'user_role_id' => 1,
                    'user_role_status' => 1
                );
                $get_role_detail = $this->Common_model->get_single_row(TBL_USER_ROLE, 'user_role_data', $role_where);

                if (!empty($get_role_detail)) {
                    $this->my_response['status'] = true;
                    $this->my_response['message'] = lang('common_detail_found');
                    $this->my_response['data'] = array(
                        'sidebar_menu' => $get_menu_detail['menu_data'],
                        'role' => $get_role_detail['user_role_data']
                    );
                    $this->my_response['role_type'] = "1";
                } else {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('common_detail_not_found');
                }
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to get the static content.
     * 
     * 
     */
    public function get_static_data_post() {

        try {

            $get_activity_levels = $this->Common_model->get_all_rows(TBL_ACTIVITY_LEVES, 'activity_level_id, activity_level_name_en, activity_level_name_hi', array('activity_level_status' => 1));
            $get_alcohol = $this->Common_model->get_all_rows(TBL_ALCOHOL, 'alcohol_id, alcohol_name_en, alcohol_name_hi', array('alcohol_status' => 1));
            $get_appointment_type = $this->Common_model->get_all_rows(TBL_APPOINTMENT_TYPE, 'appointment_type_id, appointment_type_name_en, appointment_type_name_hi', array('appointment_type_status' => 1));
            $get_food_preference = $this->Common_model->get_all_rows(TBL_FOOD_PREFERENCE, 'food_preference_id, food_preference_name_en, food_preference_name_hi', array('food_preference_status' => 1));
            $get_smoking_habbit = $this->Common_model->get_all_rows(TBL_SMOKING_HABBIT, 'smoking_habbit_id, smoking_habbit_name_en, smoking_habbit_name_hi', array('smoking_habbit_status' => 1));
            $get_occupation = $this->Common_model->get_all_rows(TBL_OCCUPATIONS, 'occupation_id, occupation_name_en, occupation_name_hi', array('occupation_status' => 1));

            $send_array = array(
                'activity_levels' => $get_activity_levels,
                'alcohol' => $get_alcohol,
                'appointment_type' => $get_appointment_type,
                'food_preference' => $get_food_preference,
                'smoking_habbit' => $get_smoking_habbit,
                'occupation' => $get_occupation
            );

            $this->my_response = array(
                'status' => true,
                'message' => lang('common_detail_found'),
                'data' => $send_array
            );

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_global_settings_post() {

        try {
            $get_global_settings = array();
            $get_global_settings = $this->Common_model->get_global_settings();
            
            $user_type = !empty($this->Common_model->escape_data($this->post_data['user_type'])) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : 2;
            
            if (!empty($get_global_settings)) {

                $this->my_response = array(
                    'status' => true,
                    'message' => lang('common_detail_found'),
                    'data' => $get_global_settings
                );
            } else {
                $this->my_response = array(
                    'status' => false,
                    'message' => lang('common_detail_not_found'),
                );
            }
            
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to update the duration of the doctor for that clinic
     * 
     
     * 
     */
    public function update_hour_format_post() {
        $doctor_id = !empty($this->post_data['doctor_id']) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
        $hour_format = !empty($this->post_data['hour_format']) ? $this->Common_model->escape_data($this->post_data['hour_format']) : '';

        try {
            if (empty($doctor_id) ||
                    empty($hour_format)
            ) {
                $this->bad_request();
            }

            if (!is_numeric($hour_format)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('mycontroller_invalid_request');
                $this->send_response();
            }

            $setting_where = array(
                'setting_user_id' => $doctor_id,
                'setting_type' => 5
            );

            $get_setting_data = $this->Common_model->get_single_row(TBL_SETTING, 'setting_id', $setting_where);
            if (!empty($get_setting_data['setting_id'])) {
                $update_setting_data = array(
                    'setting_data' => $hour_format,
                    'setting_type' => 5,
                    'setting_data_type' => 2,
                    'setting_updated_at' => $this->utc_time_formated
                );
                $update_setting_where = array(
                    'setting_id' => $get_setting_data['setting_id']
                );
                $this->Common_model->update(TBL_SETTING, $update_setting_data, $update_setting_where);
            } else {
                $insert_setting_array = array(
                    'setting_user_id' => $doctor_id,
                    'setting_data' => $hour_format,
                    'setting_type' => 5,
                    'setting_data_type' => 2,
                    'setting_created_at' => $this->utc_time_formated
                );
                $this->Common_model->insert(TBL_SETTING, $insert_setting_array);
            }
            $this->my_response['status'] = true;
            $this->my_response['message'] = lang('setting_set');
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_doctor_setting_post() {
        try {
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $setting_type = !empty($this->post_data['setting_type']) ? $this->post_data['setting_type'] : '';
            $user_type = !empty($this->Common_model->escape_data($this->post_data['user_type'])) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : '';
            if (empty($this->user_id) || empty($setting_type)){
                $this->bad_request();
            }
            $setting_where = array(
                'setting_user_id' => $doctor_id,
                'setting_type' => $setting_type
            );
            if(!is_array($setting_type)) {
                $get_setting_data = $this->Common_model->get_single_row(TBL_SETTING, '', $setting_where);
            } elseif(is_array($setting_type) && count($setting_type) > 0) {
                $get_setting_data = $this->Common_model->get_user_setting($setting_where);
                if(in_array("7", $setting_type) && in_array("8", $setting_type)) {
                    if(empty($get_setting_data)) {
                        $get_setting_data = check_sms_whatsapp_credit($doctor_id);
                    }
                    foreach ($get_setting_data as $key => $value) {
                        if($value['setting_type'] == "7") {
                            $get_setting_data[$key]['is_check_sms_credit'] = false;
                            if(!empty($GLOBALS['ENV_VARS']['CHECK_SMS_CREDIT']['PRESCRIPTION_SHARE']))
                                $get_setting_data[$key]['is_check_sms_credit'] = true;
                        }
                    }
                }
            }
            if (!empty($get_setting_data)) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $get_setting_data;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('common_detail_not_found');
            }
            $this->my_response['video_api_key'] = $GLOBALS['ENV_VARS']['VIDEO_CONF_KEY'];
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function search_instruction_post() {
        $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : "";
        $search = !empty($this->post_data['search']) ? trim($this->Common_model->escape_data($this->post_data['search'])) : "";
        $type = !empty($this->post_data['type']) ? trim($this->Common_model->escape_data($this->post_data['type'])) : "";
        if(empty($doctor_id) || empty($search) || empty($type)) {
            $this->bad_request();
        }
        $where = ['doctor_id' => $doctor_id, 'search' => $search, 'type' => $type];
        $diet_instruction = $this->Common_model->search_instruction($where);
        $this->my_response = array(
            "status" => true,
            "message" => lang("common_detail_found"),
            "data" => $diet_instruction,
        );
        $this->send_response();
    }

    public function get_prescription_template_post() {
        $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : "";
        $clinic_id = !empty($this->post_data['clinic_id']) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : "";
        $template_type = !empty($this->post_data['template_type']) ? trim($this->Common_model->escape_data($this->post_data['template_type'])) : "";
        if(empty($doctor_id) || empty($clinic_id) || empty($template_type)) {
            $this->bad_request();
        }
        $where = ['doctor_id' => $doctor_id, 'template_type' => $template_type];
        $result = $this->Common_model->get_prescription_template($where);
        $doctorAlldetails = $this->Common_model->getDoctorAlldetails($doctor_id, $clinic_id);
        if(!empty($doctorAlldetails->doctor_name))
            $doctorAlldetails->doctor_name = DOCTOR.' '.$doctorAlldetails->doctor_name;
        $doctorAlldetails->user_sign_thumb_filepath = '';
        if(!empty($doctorAlldetails->user_sign_filepath))
            $doctorAlldetails->user_sign_thumb_filepath = get_image_thumb($doctorAlldetails->user_sign_filepath);
        if(!empty($doctorAlldetails->doctor_qualification_degree))
            $doctorAlldetails->doctor_qualification_degree =  str_replace(',', ', ', $doctorAlldetails->doctor_qualification_degree);
        if(!empty($doctorAlldetails->doctor_detail_speciality))
            $doctorAlldetails->doctor_detail_speciality =  str_replace(',', ', ', $doctorAlldetails->doctor_detail_speciality);
        if(!empty($doctorAlldetails)){
            $address_data = [
                'address_name' => $doctorAlldetails->address_name,
                'address_name_one' => $doctorAlldetails->address_name_one,
                'address_locality' => $doctorAlldetails->address_locality,
                'city_name' => $doctorAlldetails->city_name,
                'state_name' => $doctorAlldetails->state_name,
                'address_pincode' => $doctorAlldetails->address_pincode
            ];
            $doctorAlldetails->clinic_address = clinic_address($address_data);
        }
        $doctorAlldetails->logo_img_thumb_path = '';
        $doctorAlldetails->watermark_img_thumb_path = '';
        if(!empty($doctorAlldetails->setting_data)) {
            $settingArr = json_decode($doctorAlldetails->setting_data,true);
            if(!empty($settingArr[0])) {
                $key = array_search($template_type, array_column($settingArr, 'appointment_type'));
                if(is_numeric($key) && isset($key) && $key >= 0) {
                    $settingDataArr = $settingArr[$key];
                }
            } else {
                $settingDataArr = json_decode($doctorAlldetails->setting_data,true);
            }
            if(!empty($settingDataArr['logo_img_path']))
                $doctorAlldetails->logo_img_thumb_path = get_image_thumb($settingDataArr['logo_img_path']);
            if(!empty($settingDataArr['watermark_img_path']))
                $doctorAlldetails->watermark_img_thumb_path = get_image_thumb($settingDataArr['watermark_img_path']);
        }
        if(!empty($settingDataArr)){
            $doctorAlldetails->setting_data = json_encode($settingDataArr);
        } else {
            $doctorAlldetails->setting_data = null;
        }
        $setting_where = [
            'setting_user_id' => $doctor_id,
            'setting_clinic_id' => $clinic_id,
            'setting_type' => 1
        ];
        $get_setting_data = $this->Common_model->get_single_row(TBL_SETTING, 'setting_data', $setting_where);
        $setting_data_arr = json_decode($get_setting_data['setting_data']);
        $arr = array_column($setting_data_arr, 'name','id');
        if(empty($arr[8])){
            $res = array_slice($setting_data_arr, 0, 2, true) +
                    array(8 => ['id'=>8,'name'=>'Only Diagnosis','status'=>'2']) +
                    array_slice($setting_data_arr, 2, count($setting_data_arr) - 1, true) ;
            $get_setting_data['setting_data'] = json_encode(array_values($res));
        }
        $setting_data_arr = json_decode($get_setting_data['setting_data']);
        $arr = array_column($setting_data_arr, 'name','id');
        if(empty($arr[9])){
            $res = array_slice($setting_data_arr, 0, 4, true) +
                    array(9 => ['id'=>9,'name'=>'Generic','status'=>'2']) +
                    array_slice($setting_data_arr, 4, count($setting_data_arr) - 1, true) ;
            $get_setting_data['setting_data'] = json_encode(array_values($res));
        }
        $this->my_response = array(
            "status" => true,
            "message" => lang("common_detail_found"),
            "data" => $result,
            "doctorAlldetails" => $doctorAlldetails,
            "shareSettingData" => $get_setting_data
        );
        $this->send_response();
    }

}
