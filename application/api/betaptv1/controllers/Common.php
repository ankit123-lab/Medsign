<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * This controller use for common apis
 * 
 
 * 
 * 
 */
class Common extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    /**
     * This function use for show test page view for testing apis
     * 
     
     * 
     * Modified Date :- 2016-09-15
     * 
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

    /**
     * 
     * This function use for testing push notification fot both device
     * 
     
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
     * @author Prashant Suthar
     * 
     * 
     */
    public function get_country_code_post() {

        $where = array(
            'country_status' => '1'
        );

        $field_name = array("country_id", "country_phonecode as country_phonecode", "country_name as country_name", "country_id as country_id");
        $result = $this->Common_model->get_all_rows(TBL_COUNTRIES, $field_name, $where);
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

        $where_array = array(
            'state_status' => 1
        );
        if (!empty($this->post_data['country_id'])) {
            $where_array['state_country_id'] = $this->post_data['country_id'];
        }
        $columns = 'state_id, state_name';

        $get_state_names = $this->Common_model->get_all_rows(TBL_STATES, $columns, $where_array);

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

        $where_array = array(
            'city_state_id' => $state_id,
            'city_status' => 1
        );
        $columns = 'city_id, city_name';

        $get_state_names = $this->Common_model->get_all_rows(TBL_CITIES, $columns, $where_array);

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

        $where = array(
            'report_type_status' => 1
        );

        if (!empty($parent_id)) {
            $where['report_type_parent_id'] = $parent_id;
        }

        $get_report_types = $this->Common_model->get_all_rows(TBL_REPORT_TYPES, $columns, $where);

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
        $search    = !empty($this->post_data['search']) ? trim($this->Common_model->escape_data($this->post_data['search'])) : "";
        $user_type = !empty($this->post_data['user_type']) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : "";
		
        $columns = 'health_analytics_test_id, 
                    health_analytics_test_name,
                    health_analytics_test_name_precise,
                    health_analytics_test_validation,
                    health_analytics_test_parent_id';
					
		$get_sql = "SELECT $columns FROM " . TBL_HEALTH_ANALYTICS . "  WHERE health_analytics_test_status = 1 ";
		// if patient then send the child data only
        // if user then send send the data based on the parent id or only parent data
        if ($user_type == 1) {
            $get_sql.= " AND ((health_analytics_test_parent_id != 0 AND health_analytics_test_type = 1) OR (health_analytics_test_type = 2)) ";
        } else {
            if (!empty($parent_id)) {
                $get_sql.= " AND health_analytics_test_parent_id = $parent_id ";
            } else {
				$get_sql.= " AND health_analytics_test_parent_id = 0 ";
            }
        }
        if (!empty($search)) {
			$get_sql.= " AND health_analytics_test_name LIKE '%$search%' ";
        }
		
		$get_health_analytics = $this->Common_model->get_all_rows_by_query($get_sql);
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

        $columns = 'health_analytics_test_id, 
                    health_analytics_test_name, 
                    health_analytics_test_name_precise,
                    patient_analytics_doctor_id,
                    health_analytics_test_parent_id';


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
                                        health_analytics_test_parent_id=0 ";
            if (!empty($search)) {
                $get_analytics_query .= " AND health_analytics_test_name LIKE '%" . $search . "%' ";
            }
            $get_analytics_query .= " GROUP BY health_analytics_test_id ";
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
     * @author Prashant Suthar
     * 
     * 
     */
    public function get_colleges_post() {

        $search_college = !empty($this->post_data['search_college']) ? trim($this->Common_model->escape_data($this->post_data['search_college'])) : "";

        $query = "SELECT
                    college_id, 
                    college_name 
                 FROM
                    " . TBL_COLLEGE . "
                 WHERE
                    college_status = 1";

        if (!empty($search_college)) {
            $query .= " AND college_name LIKE '%" . strtolower($search_college) . "%'";
        }

        $query .= " ORDER BY college_id ASC";

        $get_all_colleges = $this->Common_model->get_all_rows_by_query($query);

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
     * @author Prashant Suthar
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

        $get_all_qualifications = $this->Common_model->get_all_rows_by_query($query);

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
     * @author Prashant Suthar
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

        $get_all_councils = $this->Common_model->get_all_rows_by_query($query);

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
     * @author Prashant Suthar
     * 
     * 
     */
    public function get_language_post() {
        $get_lang_data = $this->Common_model->get_language(array('language_status' => 1), 'language_id,language_code,language_name');
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
     * @author Prashant Suthar
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

        $get_specialization = $this->Common_model->get_all_rows_by_query($query);


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
     */
    public function get_support_contact_post() {
        try {

            $country = !empty($this->post_data['country']) ? trim($this->Common_model->escape_data($this->post_data['country'])) : 101;
            $flag = !empty($this->post_data['flag']) ? trim($this->Common_model->escape_data($this->post_data['flag'])) : '';

            $requested_data = array(
                'country_id' => $country,
                'flag' => $flag
            );

            $get_contact_us_data = $this->Common_model->get_contact_us_info($requested_data);

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
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

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
                            $attachment_array[] = array(
                                'user_issue_attachment_issue_id' => $inserted_id,
                                'user_issue_attachment_name' => $attachment,
                                'user_issue_attachment_filepath' => IMAGE_MANIPULATION_URL . ISSUE_FOLDER . '/' . $inserted_id . "/" . $attachment,
                                'user_issue_attachment_created_at' => $this->utc_time_formated
                            );
                            $attachment_link .= IMAGE_MANIPULATION_URL . ISSUE_FOLDER . '/' . $inserted_id . "/" . $attachment;
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
    
    public function patient_help_and_support_post() {
        try {   
            $user_issue_message = !empty($this->post_data['user_issue_message']) ? trim($this->Common_model->escape_data($this->post_data['user_issue_message'])) : "";
            $user_issue_email = !empty($this->post_data['user_issue_email']) ? trim($this->Common_model->escape_data($this->post_data['user_issue_email'])) : "";
            $user_id = !empty($this->post_data['user_id']) ? trim($this->Common_model->escape_data($this->post_data['user_id'])) : "";

            if (empty($user_issue_message) || empty($user_issue_email) || empty($user_id)) {
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
                'user_issue_email' => $user_issue_email,
                'user_issue_message' => $user_issue_message,
                'user_issue_created_at' => $this->utc_time_formated,
                'user_type' => 1,
            );
            $inserted_id = $this->Common_model->insert(TBL_USER_ISSUE, $insert_issue);

            $send_issue_mail = array(
                'user_name' => $user_name,
                'user_email' => ADMIN_EMAIL,
                'issue' => $user_issue_message,
                'template_id' => 16,
                'issue_email' => $user_issue_email
            );
            $cron_job_path = CRON_PATH . " notification/send_mail_background/" . base64_encode(json_encode($send_issue_mail));
            exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");

            $revert_mail = array(
                'user_name' => $user_name,
                'template_id' => 32,
                'user_email' => $user_issue_email
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

            $get_diseases_where = array(
                'disease_status' => 1
            );
            $columns = 'disease_id, disease_name';

            $get_diseases = $this->Common_model->get_all_rows(TBL_DISEASES, $columns, $get_diseases_where);

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
                'setting_user_id' => $this->user_id,
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
            $setting_type = !empty($this->Common_model->escape_data($this->post_data['setting_type'])) ? trim($this->Common_model->escape_data($this->post_data['setting_type'])) : '';
            $user_type = !empty($this->Common_model->escape_data($this->post_data['user_type'])) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : '';

            if (empty($this->user_id) ||
                    empty($setting_type)
            ) {
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
                    } else if ($setting_type == 2) {
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

//            if (!empty($clinic_id)) {
//                $setting_where['setting_clinic_id'] = $clinic_id;
//            }

            $get_setting_data = $this->Common_model->get_single_row(TBL_SETTING, '', $setting_where);

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
        echo 'test';exit;
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
        $this->send_email(array($email => $email), $subject, $message);

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

            $columns = 'me_video_id, me_video_url, me_video_page';

            $get_video_data = $this->Common_model->get_single_row(TBL_VIDEO, $columns, $where);

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

            $where = array(
                'procedure_status' => 1
            );

            if (!empty($search)) {
                $where['procedure_title LIKE'] = '%' . $search . '%';
            }

            $columns = 'procedure_id, procedure_title';

            $get_procedure_data = $this->Common_model->get_all_rows(TBL_PROCEDURE, $columns, $where);

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

            $get_activity_levels = $this->Common_model->get_activity_levels(array('activity_level_status' => 1), 'activity_level_id, activity_level_name_en, activity_level_name_hi');
            $get_alcohol = $this->Common_model->get_alcohol(array('alcohol_status' => 1), 'alcohol_id, alcohol_name_en, alcohol_name_hi');
            $get_appointment_type = $this->Common_model->get_appointment_type(array('appointment_type_status' => 1), 'appointment_type_id, appointment_type_name_en, appointment_type_name_hi');
            $get_food_preference = $this->Common_model->get_food_preference(array('food_preference_status' => 1), 'food_preference_id, food_preference_name_en, food_preference_name_hi');
            $get_smoking_habbit = $this->Common_model->get_smoking_habbit(array('smoking_habbit_status' => 1), 'smoking_habbit_id, smoking_habbit_name_en, smoking_habbit_name_hi');
            $get_occupation = $this->Common_model->get_occupation(array('occupation_status' => 1), 'occupation_id, occupation_name_en, occupation_name_hi');

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

    public function get_all_master_data_post() {

        try {
            $country = !empty($this->post_data['country']) ? trim($this->Common_model->escape_data($this->post_data['country'])) : 101;
            $flag = !empty($this->post_data['flag']) ? trim($this->Common_model->escape_data($this->post_data['flag'])) : '';

            $responseData = array();
            $responseData['global_settings'] = array();
            $get_global_settings = $this->Common_model->get_global_settings();
            if (!empty($get_global_settings)) {
                $responseData['global_settings'] = $get_global_settings;
            }
            $responseData['activity_levels'] = $this->Common_model->get_activity_levels(array('activity_level_status' => 1), 'activity_level_id, activity_level_name_en, activity_level_name_hi');
            $responseData['alcohol'] = $this->Common_model->get_alcohol(array('alcohol_status' => 1), 'alcohol_id, alcohol_name_en, alcohol_name_hi');
            $responseData['appointment_type'] = $this->Common_model->get_appointment_type(array('appointment_type_status' => 1), 'appointment_type_id, appointment_type_name_en, appointment_type_name_hi');
            $responseData['food_preference'] = $this->Common_model->get_food_preference(array('food_preference_status' => 1), 'food_preference_id, food_preference_name_en, food_preference_name_hi');
            $responseData['smoking_habbit'] = $this->Common_model->get_smoking_habbit(array('smoking_habbit_status' => 1), 'smoking_habbit_id, smoking_habbit_name_en, smoking_habbit_name_hi');
            $responseData['occupation'] = $this->Common_model->get_occupation(array('occupation_status' => 1), 'occupation_id, occupation_name_en, occupation_name_hi');
            $responseData['lang_data'] = $this->Common_model->get_language(array('language_status' => 1), 'language_id,language_code,language_name');
            $responseData['medical_condition_data'] = $this->Common_model->get_medical_condition(array("medical_condition_status" => 1),"medical_condition_id,medical_condition_name");

            $requested_data = array(
                'country_id' => $country,
                'flag' => $flag
            );
            $get_contact_us_data = $this->Common_model->get_contact_us_info($requested_data);

            if (!empty($get_contact_us_data)) {
                foreach ($get_contact_us_data as $data) {
                    if (!empty($data['contact_us_country_id'])) {
                        $responseData['support_contact_local'] = array(
                            "name" => $data['contact_us_name'],
                            "call" => $data['contact_number'],
                            "email" => $data['contact_us_email']
                        );
                    }

                    if (empty($data['contact_us_country_id'])) {
                        $responseData['support_contact_global'] = array(
                            "name" => $data['contact_us_name'],
                            "call" => $data['contact_us_phone_number'],
                            "email" => $data['contact_us_email']
                        );
                    }
                }
            } else {
                $responseData["support_contact_local"] = array(
                        "name" => "",
                        "call" => "",
                        "email" => "",
                    );
                $responseData["support_contact_global"] = array(
                        "name" => "",
                        "call" => "",
                        "email" => "",
                    );
            }
            $setting_where = array(
                'setting_user_id' => $this->user_id,
                'setting_type' => array(2,3)
            );

            $get_setting_data = $this->Common_model->get_user_setting($setting_where);
            $user_setting_data = array();
            $user_setting_data['data_security'] = array();
            $user_setting_data['notify_status'] = array();
            foreach ($get_setting_data as $key => $value) {
                if($value['setting_type'] == 2)
                    $user_setting_data['data_security'] = $value;
                if($value['setting_type'] == 3)
                    $user_setting_data['notify_status'] = $value;
            }
            $responseData['user_setting_data'] = $user_setting_data;
            $this->my_response = array(
                'status' => true,
                'message' => lang('common_detail_found'),
                'data' => $responseData
            );
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_user_all_data_post() {
        $user_id = !empty($this->post_data['user_id']) ? $this->Common_model->escape_data($this->post_data['user_id']) : "";
        $user_type = !empty($this->post_data['user_type']) ? $this->Common_model->escape_data($this->post_data['user_type']) : 1;
        $date = !empty($this->post_data['date']) ? $this->Common_model->escape_data($this->post_data['date']) : date('Y-m-d');
        if (empty(trim($user_id))) {
            $this->bad_request();
            exit;
        }
        try {
            $this->load->model(array("User_model"));
            $this->load->model("Reminder_model", 'Reminder');
            //get_user_details
            $user_data = $this->User_model->get_user_details_by_id($user_id);
            $get_vital_data = $this->Common_model->get_vital_data($user_id);
            if (!empty($get_vital_data['vital_report_weight']) && strtotime($get_vital_data['vital_report_updated_at']) > strtotime($user_data['user_details_modifed_at'])) {
                $user_data['user_details_weight'] = $get_vital_data['vital_report_weight'];
            }
            //if caregiver id is not empty then send the name of the caregiver name
            if (!empty($user_data['user_caregiver_id']) && $user_data['user_caregiver_id'] > 0) {
                $care_giver_id = $user_data['user_caregiver_id'];
                $get_caregiver_details = $this->User_model->get_details_by_id($care_giver_id);
                if (!empty($get_caregiver_details)) {
                    $user_data['user_care_giver_name'] = $get_caregiver_details['user_first_name'] . ' ' . $get_caregiver_details['user_last_name'];
                    $user_data['user_care_giver_email'] = $get_caregiver_details['user_email'];
                    $user_data['user_care_giver_number'] = $get_caregiver_details['user_phone_number'];
                }
            }
            //get family medical history data
            $family_data = $this->User_model->get_family_medical_history($user_id);
            $user_data['family_medical_history_data'] = $family_data;

            //get the user diseases data
            $disease_data = $this->User_model->get_diseases_data($user_id);
            $user_data['diseases_data'] = $disease_data;
            $user_data['user_photo_filepath'] = get_image_thumb($user_data['user_photo_filepath']);
            $other_user_id_arr = array();
            $other_user_id_arr[] = $user_id;
            //END get_user_details

            //get_family_members
            $get_family_members = $this->User_model->get_family_members($user_id);
            if(count($get_family_members) > 0) {
                $family_member_user_id = array_column($get_family_members, 'user_id');
                $other_user_id_arr = array_merge($other_user_id_arr, $family_member_user_id);
            }
            //END get_family_members

            //sync_reminder
            $sync_date = '';
            $reminder_type = '';
            $other_user_id = implode(',', $other_user_id_arr);
            $reminder_data = $this->Reminder->get_sync_reminders($other_user_id, $sync_date, $reminder_type);
            $inserted = array();
            $updated = array();
            $deleted = array();
            if (!empty($reminder_data)) {
                foreach ($reminder_data as $reminder) {
                    if (empty($sync_date) && $reminder['reminder_status'] == 1) {
                        $inserted[] = $reminder;
                    } else if ($reminder['reminder_status'] == 9) {
                        $deleted[] = $reminder;
                    } else {
                        $updated[] = $reminder;
                    }
                }
            }
            //END sync_reminder

            //get_all_reminder_chart
            $chart_data = $this->Reminder->get_chart_data($other_user_id, $date);
            //END get_all_reminder_chart

            $this->my_response = array(
                'status' => true,
                'message' => lang('user_detail_found'),
                'user_details' => $user_data,
                'family_members' => $get_family_members,
            );
            $this->my_response['sync_reminder']['inserted'] = $inserted;
            $this->my_response['sync_reminder']['updated'] = $updated;
            $this->my_response['sync_reminder']['deleted'] = $deleted;
            $this->my_response['sync_date'] = $this->utc_time_formated;
            $this->my_response['chart_data'] = $chart_data;
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

}
