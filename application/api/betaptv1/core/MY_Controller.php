<?php

/**
 * @author Dipesh Shihora
 * Modified Date : 2016-09-15
 */
/**
 * Description of My_contoller
 * 
 */
require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class MY_Controller extends REST_Controller {

    /**
     *
     * @var array
     */
    protected $post_data;

    /**
     *
     * @var array 
     */
    protected $file_data;

    /**
     *
     * @var bigint 
     */
    protected $request_id;

    /**
     *
     * @var array 
     */
    protected $response;

    /**
     *
     * @var string 
     */
    protected $error;

    /**
     *
     * @var integer 
     */
    protected $start_time;

    /**
     *
     * @var integer 
     */
    protected $end_time;

    /**
     *
     * @var bigint
     */
    public $utc_time;
    public $current_lang;
    public $log_file;
    public $gender_array;
    public $setting_type;
    public $setting_data_type;
    public $blood_pressure_type;
    public $temperature_type;
    public $temperature_taken;
    public $user_type;
    public $drug_intake;
    public $duration;
    public $appointment_state;

    /**
     *
     * @var integer 
     */
    public $user_id;

    public function __construct() {
        parent::__construct();
        $this->utc_time = time();
        $this->start_time = time();
        $this->utc_time_formated = date('Y-m-d H:i:s', time());
        $this->user_id = null;

        try {

            $http_request_method = strtoupper($this->request->method);
            switch ($http_request_method) {
                case "GET":
                    $this->post_data = $this->get();
                    break;
                case "POST":
                    $this->post_data = $this->post();
                    break;
                case "PUT":
                    $this->post_data = $this->input->input_stream();
                    break;
                case "DELETE":
                    $this->post_data = $this->input->input_stream();
                    break;
                default :
                    $this->my_response['success'] = false;
                    $this->my_response['message'] = "Invalid request";
                    $this->send_response(405);
                    break;
            }
        } catch (Exception $e) {
            $this->my_response['success'] = false;
            $this->my_response['message'] = "Invalid request";
            $this->my_response['error'] = $e->getMessage();
            $this->send_response();
        }


        $this->log_file = DOCROOT . 'logs/log_' . date('d-m-Y') . '.txt';

        $this->current_lang = DEFAULT_LANG;

        $this->gender_array = array('male', 'female', 'other', 'undisclosed');
        $this->setting_type = array('1', '2', '3');
        $this->setting_data_type = array('1', '2');
        $this->blood_pressure_type = array('1', '2', '3');
        $this->temperature_taken = array('1', '2', '3', '4', '5', '6');
        $this->temperature_type = array('1', '2');
        $this->user_type = array('1', '2');
        $this->drug_intake = array('1', '2', '3', '4');
        $this->duration = array('1', '2', '3');
        $this->appointment_state = array('1', '2');

        if (function_exists('post_data')) {
            $this->current_lang = !empty($this->post_data('language')) ? $this->post_data('language') : DEFAULT_LANG;
        }
        $this->config->set_item('language', $this->current_lang);
        $this->lang->load($this->current_lang);
        $this->file_data = &$_FILES;

        $api_name = $this->router->fetch_method();

        //check under_maintenance is schedule ?
        $under_maintenance_static_apis = array(
            "login",
            "register",
            "forgot_password",
            "resend_otp"
        );
        $this->my_response['under_maintenance'] = false;
        if (!empty($this->post_data['user_id']) || in_array($api_name, $under_maintenance_static_apis)) {

            $user_type = !empty($this->post_data['user_type']) ? $this->post_data['user_type'] : "";
            if (!empty($this->post_data['user_id'])) {
                $check_user_type_where = array(
                    "user_id" => $this->post_data['user_id']
                );
                $check_user_type = $this->Common_model->get_single_row(TBL_USERS, "user_type", $check_user_type_where);
                $user_type = !empty($check_user_type['user_type']) ? $check_user_type['user_type'] : "";
            }

            $check_have_under_maintenance_sql = "
                SELECT 
                    maintenance_message
                FROM " . TBL_UNDER_MAINTENANCE . "
                WHERE 
                    UNIX_TIMESTAMP(maintenance_start_date) <= '" . time() . "' AND 
                    UNIX_TIMESTAMP(maintenance_end_date) >= '" . time() . "' AND 
                    (
                        maintenance_for=3 OR 
                        maintenance_for='" . $user_type . "'
                    ) AND 
                    maintenance_status=1
                LIMIT 1
            ";

            $check_have_under_maintenance = $this->Common_model->get_single_row_by_query($check_have_under_maintenance_sql);

            if (!empty($check_have_under_maintenance)) {
                $this->my_response['success'] = true;
                $this->my_response['message'] = $check_have_under_maintenance['maintenance_message'];
                $this->my_response['under_maintenance'] = true;
                $this->my_response['url'] = $api_name;
                $this->send_response();
            }
        }

        //check api whether required security token
        $apis_without_security_token = array(
            'index',
            'get_last_api_log',
            'push_notification_test',
            "login",
            "register",
            "forgot_password",
            "get_country_code",
            "resend_otp",
            "verify_otp",
            "get_states",
            "get_cities",
            "get_laboratories",
            "get_colleges",
            "get_qualifications",
            "get_councils",
            "get_drug_frequency",
            "get_drug_generic",
            "get_language",
            "get_specialization",
            "resend_email_verify_link",
            "send_sms",
            "send_email",
            "get_video",
            "static_page",
            "register_verify_otp",
            "register_resend_otp"
        );

        if (!in_array($api_name, $apis_without_security_token)) {
            if (!$this->check_security_token()) {
                $this->my_response['success'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_token");
                $this->my_response['url'] = $api_name;
                $this->my_response['at'] = $this->post_data['access_token'];
                $this->my_response['user_id'] = $this->post_data['user_id'];
                $this->send_response(REST_Controller::HTTP_UNAUTHORIZED, false);
            }
        }
    }

    /**
     * 
     * Method to store device token of the user in database
     * @param integer $user_id
     * @param string $device_token
     * @param integer $device_type
     * @return boolean
     */
    function add_device_token($user_id, $access_token, $device_token = '', $device_type = '') {
        $where = array(
            "udt_u_id" => $user_id
        );

        $this->Common_model->delete_data(TBL_USER_DEVICE_TOKENS, $where);

        $where_token = array(
            "udt_device_token" => $device_token
        );
        $this->Common_model->delete_data(TBL_USER_DEVICE_TOKENS, $where_token);

        $insert_data = array();
        $insert_data['udt_u_id'] = $user_id;
        $insert_data['udt_security_token'] = trim($access_token);
        $insert_data['udt_device_token'] = trim($device_token);
        $insert_data['udt_device_type'] = trim($device_type);
        $insert_data['udt_created_date'] = time();

        $insertId = $this->Common_model->insert(TBL_USER_DEVICE_TOKENS, $insert_data);
        if ($insertId > 0) {
            return $insertId;
        } else {
            return FALSE;
        }
    }

    /**
     * Function to validate user with security token and unique token
     * @return boolean
     */
    public function check_security_token() {
        $user_id = !empty($this->post_data['user_id']) ? $this->post_data['user_id'] : '';
        $token = !empty($this->post_data['access_token']) ? $this->post_data['access_token'] : '';

        if ($token == 'medeasy') {
            $this->user_id = $user_id;
            return TRUE;
        }

        $where = array(
            "udt_u_id" => $user_id,
            "udt_security_token" => $token
        );

        $device_token_data = $this->Common_model->get_single_row(TBL_USER_DEVICE_TOKENS, "udt_u_id", $where);
        if (is_array($device_token_data) && count($device_token_data) > 0 && !empty($device_token_data['udt_u_id'])) {
            $this->user_id = $user_id;
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Removing extra slashes from the response string recursively
     * @param type $variable
     * @return type
     */
    function strip_slashes_recursive($variable) {
        if (is_null($variable))
            $variable = "";
        if (is_string($variable))
            return stripslashes($variable);
        if (is_array($variable))
            foreach ($variable as $i => $value)
                $variable[$i] = $this->strip_slashes_recursive($value);

        return $variable;
    }

    /**
     * Function to return api response
     * @param boolean $authentication Pass false if authentication fails.
     * @return null
     */
    public function send_response($http_code = 200, $authentication = true) {

        $api_name = $this->router->fetch_method();
        //check application version
        $check_application_version_static_apis = array(
            "login",
            "register"
        );
        $this->my_response['app_update_flag'] = 1;
        if (in_array($api_name, $check_application_version_static_apis)) {
            $user_type = !empty($this->post_data['user_type']) ? $this->post_data['user_type'] : "";
            $application_version = !empty($this->post_data['application_version']) ? $this->post_data['application_version'] : "";

            if (!empty($user_type) && $user_type == 1 && !empty($application_version)) {
                $check_application_version_sql = "
                    SELECT 
                        version_update_flag
                    FROM " . TBL_APPLICATION_VERSION . "
                    WHERE 
                        version_number='" . $application_version . "'
                    LIMIT 1
                ";
                $check_have_under_maintenance = $this->Common_model->get_single_row_by_query($check_application_version_sql);
                if (!empty($check_have_under_maintenance)) {
                    $this->my_response['app_update_flag'] = (integer) $check_have_under_maintenance['version_update_flag'];
                }
            }
        }


        //store response to api log table
        $this->my_response['authentication'] = $authentication;
        if (empty($this->my_response['under_maintenance'])) {
            $this->my_response['under_maintenance'] = false;
        }
        $this->my_response['csrf_token'] = $this->security->get_csrf_hash();
        $response = $this->strip_slashes_recursive($this->my_response);
//        $response = $this->my_response;
        $this->response($response, $http_code);
    }

    /**
     * Function to store runtime exceptions in error table
     * @return null
     */
    public function store_error() {

        //store error log into db
        $error = array(
            "er_uri" => $this->router->fetch_method(),
            "er_message" => $this->error,
            "er_request_data" => json_encode($this->post_data),
            "er_created_date" => current_time(),
        );
        log_message("error", $this->error);

        $this->Common_model->insert(TBL_ERRORS, $error);
        $this->my_response = array(
            "status" => false,
            "message" => lang("mycontroller_problem_request"),
        );
        $this->send_response();
    }

    /**
     * Function to return bad request message in response
     * @return null
     */
    public function bad_request() {

        $this->my_response = array(
            "status" => false,
            "message" => lang("mycontroller_bad_request"),
        );
        $this->send_response(400);
    }

    /**
     * Function to store notification data and send to devices
     * @param array $notification_data
     * @return boolean
     */
    public function push_notification_test() {
        $device_token = !empty($this->post_data['device_token']) ? $this->post_data['device_token'] : "";
        $notification_message = !empty($this->post_data['message']) ? $this->post_data['message'] : "";
        $notification_type = !empty($this->post_data['notification_type']) ? $this->post_data['notification_type'] : "";
        $notification_type_id = !empty($this->post_data['notification_type_id']) ? $this->post_data['notification_type_id'] : "";
        $device_type = !empty($this->post_data['device_type']) ? $this->post_data['device_type'] : "";

        if (empty($device_type) || empty($device_token) || empty($notification_message)) {
            $this->bad_request();
        }

        try {
            $notification_name = APP_NAME;

            //Inserting notification in database
            $insert_array = array(
                'n_nt_id' => $notification_type_id,
                'n_reciever_id' => 0,
                'n_sender_id' => 1,
                'n_reciever_type' => 1,
                'n_sender_type' => 1,
                'n_params' => serialize(array()),
                'n_created_date' => time(),
                'n_status' => 1
            );
            $insert_id = $this->Common_model->insert(TBL_NOTIFICATIONS, $insert_array);

            $mode = 'test';
            if ($mode == 'test') {
                // EXECUTE THIS CODE FOR TESTING PURPOSE ONLY
                if (strtolower($device_type) == "ios") {
                    $message = array();
                    $message['aps']['icon'] = 'appicon';
                    $message['aps']['vibrate'] = 'true';
                    $message['aps']['badge'] = 0;
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
            } else {
                /**
                 * USE THIS CODE WHEN YOU WANT TO SEND NOTIFICATION TO USER IN REAL-TIME APPLICATION
                 * 
                 * Get device token for send notification 
                 * 
                 * SEND BROADCAST NOTIFICATION
                 * 
                 */
                $columns = array('udt_u_id', 'udt_device_token', 'udt_device_type');
                $where = array('udt_status' => 1);
                $token_data = $this->Common_model->get_all_rows(TBL_USER_DEVICE_TOKENS, $columns, $where);

                if (!empty($token_data) && is_array($token_data) && count($token_data) > 0) {
                    //get notification message from notification_type table
                    $notification_type = $this->Common_model->get_single_row(TBL_NOTIFICATION_TYPES, '*', array('nt_status' => 1, 'nt_id' => $notification_type_id));

                    if (!empty($notification_type) && is_array($notification_type) && count($notification_type > 0)) {
                        $notification_message = vsprintf($notification_type['nt_message'], $insert_array['params']);
                        $notification_name = $notification_type['nt_name'];
                    }
                    $device_token = array();
                    foreach ($token_data as $user) {
                        if (strtolower($user['udt_device_type']) == "ios") {
                            // send notification to the ios
                            $message = array();
                            $message['aps']['icon'] = 'appicon';
                            $message['aps']['vibrate'] = 'true';
                            $message['aps']['badge'] = 0;
                            $message['aps']['sound'] = "default";
                            $message['aps']['alert'] = (string) $notification_message;
                            $message['aps']['userId'] = (string) 1;
                            $message['aps']['notificationType'] = (string) $notification_type;
                            $message['aps']['notificationTypeId'] = (string) $notification_type_id;
                            $result = send_notification_ios($user['udt_device_token'], $message, IOS_PEM_PATH, APP_IS_LIVE);
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
                        } else if (strtolower($user['udt_device_type']) == "android") {
                            $device_token[] = $user['udt_device_token'];
                        }
                    }

                    if (!empty($device_token) && is_array($device_token) && count($device_token) > 0) {
                        // send notification to the android
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
                }
            }
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function send_notification($notification_data) {
        if (!empty($notification_data) && is_array($notification_data) && count($notification_data) > 0) {

            //Inserting data in notification table
            $insert_array = array(
                'n_nt_id' => $notification_data['type_id'],
                'n_reciever_id' => $notification_data['user_id'],
                'n_sender_id' => $this->user_id,
                'n_reciever_type' => $notification_data['user_type'],
                'n_sender_type' => 1,
                'n_params' => serialize(array($notification_data['params'])),
                'n_created_date' => time(),
                'n_status' => 1
            );
            $notif_insert_id = $this->Common_model->insert(TBL_NOTIFICATIONS, $insert_array);
            if ($notif_insert_id > 0) {

                /**
                 * USE THIS CODE WHEN YOU WANT TO SEND NOTIFICATION TO USER.
                 * 
                 * Get device token for send notification
                 * 
                 */
                $notification_message = 'alert';
                $notification_name = APP_NAME;
                $where = array(
                    'udt_u_id' => $notification_data['user_id'],
                    'udt_device_token IS NOT' => 'NULL',
                );
                $columns = array('udt_u_id', 'udt_device_token', 'udt_device_type');
                $token_data = $this->Common_model->get_all_rows(TBL_USER_DEVICE_TOKENS, $columns, $where);

                if (!empty($token_data) && is_array($token_data) && count($token_data) > 0) {
                    //get notification message from notification_type table
                    $where_type = array('nt_status' => 1, 'nt_id' => $notification_data['type_id']);
                    $notification_type = $this->Common_model->get_single_row(TBL_NOTIFICATION_TYPES, '*', $where_type);

                    if (!empty($notification_type) && is_array($notification_type) && count($notification_type > 0)) {
                        $notification_message = vsprintf($notification_type['nt_message'], $insert_array['params']);
                        $notification_name = $notification_type['nt_name'];
                    }

                    $device_token = array();
                    foreach ($token_data as $user) {
                        if (strtolower($user['udt_device_type']) == "ios") {
                            // send notification to the ios
                            $message = array();
                            $message['aps']['icon'] = 'appicon';
                            $message['aps']['vibrate'] = 'true';
                            $message['aps']['badge'] = 0;
                            $message['aps']['sound'] = "default";
                            $message['aps']['alert'] = (string) $notification_message;
                            $message['aps']['userId'] = (string) 1;
                            $message['aps']['notificationType'] = (string) $notification_data['type_id'];
                            $message['aps']['notificationTypeId'] = (string) 1;
                            $result = send_notification_ios($user['udt_device_token'], $message, IOS_PEM_PATH, APP_IS_LIVE);
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
                        } else if (strtolower($user['udt_device_type']) == "android") {
                            $device_token[] = $user['udt_device_token'];
                        }
                    }

                    if (!empty($device_token) && is_array($device_token) && count($device_token) > 0) {
                        // send notification to the android
                        $message = array();
                        $message['android']['icon'] = 'appicon';
                        $message['android']['vibrate'] = 'true';
                        $message['android']['badge'] = 1;
                        $message['android']['sound'] = "default";
                        $message['android']['message'] = (string) $notification_message;
                        $message['android']['userId'] = (string) 1;
                        $message['android']['notificationType'] = (string) $notification_data['type_id'];
                        $message['android']['notificationTypeId'] = (string) 1;
                        $result = send_notification_android($device_token, $message, ANDROID_GCM_KEY);
                    }
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
        return true;
    }

    /**
     * This function is use for send email
     * 
     * @param type $to_email_address
     * @param type $subject
     * @param type $message
     */
    public function send_email($to_email_address, $subject, $message) {
        try {
            require_once SWIFT_MAILER_PATH;
            $transport = Swift_SmtpTransport::newInstance(EMAIL_HOST, EMAIL_PORT, EMAIL_CERTIFICATE)
                    ->setUsername(EMAIL_USER)
                    ->setPassword(EMAIL_PASS);
            $mailer = Swift_Mailer::newInstance($transport);
            $sendMessage = Swift_Message::newInstance($subject)
                    ->setFrom(array(EMAIL_FROM => APP_NAME))
                    ->setTo($to_email_address);
            $doc = new DOMDocument();
            @$doc->loadHTML($message);
            $tags = $doc->getElementsByTagName('img');
            foreach ($tags as $tag) {
                if (filter_var($tag->getAttribute('src'), FILTER_VALIDATE_URL)) {
                    
                } else {
                    //$attachment = Swift_Image::newInstance($tag->getAttribute('src'),"image.png",'image/png')->setDisposition('inline');
                    $imag_src = str_replace("data:image/png;base64,", "", $tag->getAttribute('src'));
                    $imag_src = str_replace(" ", "+", $imag_src);
                    $attachment = new Swift_Image(base64_decode($imag_src), 'image.png', 'image/png');
                    //echo $attachment;exit;
                    $cid = $sendMessage->embed($attachment);
                    $tag->setAttribute('src', $cid);
                }
            }
            $sendMessage->setBody($doc->saveHTML(), "text/html");


            if (!$mailer->send($sendMessage)) {
                $this->send_normal_email($to_email_address, $subject, $message);
            }
        } catch (\Swift_TransportException $e) {
            $response = $e->getMessage();
            $this->send_normal_email($to_email_address, $subject, $message);
            file_put_contents($this->log_file, "\n\n ======== swift send_email ex: " . json_encode($response) . "===== \n\n", FILE_APPEND | LOCK_EX);
        } catch (Exception $e) {
            $response = $e->getMessage();
            $this->send_normal_email($to_email_address, $subject, $message);
            file_put_contents($this->log_file, "\n\n ======== send_email ex: " . json_encode($response) . "===== \n\n", FILE_APPEND | LOCK_EX);
        }
    }

    /**
     * send mail if failed by swift mailer
     * @param type $to_email_address
     * @param type $subject
     * @param type $message
     */
    public function send_normal_email($to_email_address, $subject, $message) {
        try {
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=utf-8\r\n";
            $headers .= "X-Priority: 3\r\n";
            $headers .= "X-Mailer: PHP" . phpversion() . "\r\n";
            $headers .= "From:" . APP_NAME . "<" . EMAIL_USER . ">" . "\r\n";
            foreach ($to_email_address as $email => $name) {
                mail($email, $subject, $message, $headers);
            }
        } catch (Exception $e) {
            $response = $e->getMessage();
            file_put_contents($this->log_file, "\n\n ======== send_normal_email ex: " . json_encode($response) . "===== \n\n", FILE_APPEND | LOCK_EX);
        }
    }

    /**
     * 
     * @param type $to_email_address
     * @param type $subject
     * @param type $message
     * @param type $attachment1
     * @param type $attachment2
     */
    public function send_attachment_email($to_email_address, $subject, $message, $attachment1 = array(), $attachment2 = array()) {

        try {
            require_once SWIFT_MAILER_PATH;
            $transport = Swift_SmtpTransport::newInstance(EMAIL_HOST, EMAIL_PORT, EMAIL_CERTIFICATE)
                    ->setUsername(EMAIL_USER)
                    ->setPassword(EMAIL_PASS);
            $mailer = Swift_Mailer::newInstance($transport);
            $sendMessage = Swift_Message::newInstance()
                    ->setSubject($subject)
                    ->setTo($to_email_address)
                    ->setFrom(array(EMAIL_FROM => APP_NAME))
                    ->setBody($message, 'text/html');

            if (!empty($attachment1)) {
                $sendMessage->attach(Swift_Attachment::fromPath($attachment1[0])->setFilename($attachment1[1]));
            }

            if (!empty($attachment2)) {
                $sendMessage->attach(Swift_Attachment::fromPath($attachment2[0])->setFilename($attachment2[1]));
            }

            if (!$mailer->send($sendMessage)) {
                file_put_contents($this->log_file, "\n\n ======== send by php mail ===== \n\n", FILE_APPEND | LOCK_EX);
                $this->send_simple_attach_email($to_email_address, $subject, $message, $attachment1, $attachment2);
            }
        } catch (\Swift_TransportException $e) {
            $response = $e->getMessage();
            file_put_contents($this->log_file, "\n\n ======== response" . json_encode($response) . "===== \n\n", FILE_APPEND | LOCK_EX);
            //$this->send_simple_attach_email($to_email_address, $subject, $message, $attachment1, $attachment2);
        } catch (Exception $e) {
            $response = $e->getMessage();
            file_put_contents($this->log_file, "\n\n ======== response" . json_encode($response) . "===== \n\n", FILE_APPEND | LOCK_EX);
            //$this->send_simple_attach_email($to_email_address, $subject, $message, $attachment1, $attachment2);
        }
    }

    /**
     * 
     * @param type $to_email_address
     * @param type $subject
     * @param type $message
     * @param type $attachment1
     * @param type $attachment2
     * @return boolean
     */
    public function send_simple_attach_email($to_email_address, $subject, $message, $attachment1 = array(), $attachment2 = array()) {
        try {
            $from = APP_NAME . "<" . EMAIL_USER . ">";
            $mime_boundary = "==Multipart_Boundary_x" . md5(mt_rand()) . "x";
            $headers = "From: $from\r\n" .
                    "MIME-Version: 1.0\r\n" .
                    "Content-Type: multipart/mixed;\r\n" .
                    " boundary=\"{$mime_boundary}\"";

            $message = "This is a multi-part message in MIME format.\n\n" .
                    "--{$mime_boundary}\n" .
                    "Content-Type: text/html; charset=\"utf-8\"\n" .
                    "Content-Transfer-Encoding: 7bit\n\n" .
                    $message . "\n\n";

            $filename_list = array($attachment1[0], $attachment2[0]);

            foreach ($filename_list as $tmp_name) {
                $file = fopen($tmp_name, 'rb');
                $data = fread($file, filesize($tmp_name));
                fclose($file);
                $data = chunk_split(base64_encode($data));
                $name = basename($tmp_name);
                $type = filetype($tmp_name);

                $message .= "--{$mime_boundary}\n" .
                        "Content-Type: {$type};\n" .
                        " name=\"{$name}\"\n" .
                        "Content-Disposition: attachment;\n" .
                        " filename=\"{$name}\"\n" .
                        "Content-Transfer-Encoding: base64\n\n" .
                        $data . "\n\n";
            }

            $message.="--{$mime_boundary}--\n";

            foreach ($to_email_address as $email => $name) {
                @mail($email, $subject, $message, $headers);
            }
            return true;
//            if (@mail($to_email_address, $subject, $message, $headers)) {
//                file_put_contents($this->log_file, "\n\n ======== mail sent to: " . $to_email_address . "===== \n\n", FILE_APPEND | LOCK_EX);
//                return true;
//            } else {
//                file_put_contents($this->log_file, "\n\n ======== fail mail sent to: " . $to_email_address . "===== \n\n", FILE_APPEND | LOCK_EX);
//                return FALSE;
//            }
        } catch (Exception $e) {
            $response = $e->getMessage();
            file_put_contents($this->log_file, "\n\n ======== response" . json_encode($response) . "===== \n\n", FILE_APPEND | LOCK_EX);
            return FALSE;
        }
    }

    public function check_module_permission($recieve_data) {
        if (!empty($recieve_data)) {
            $permission_detail_array = json_decode($recieve_data['role_data'], true);
            foreach ($permission_detail_array as $key => $permission) {
                if ($key == $recieve_data['module']) {
                    if ($permission[$recieve_data['key']] == 'on') {
                        return 1;
                    }
                }
            }
        }
        return 2;
    }

}
