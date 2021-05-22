<?php
/**
 * 
 * This controller use for user related activity
 */
class User extends MY_Controller {

    protected $user_columns;

    public function __construct() {
        parent::__construct();
        $this->load->model(array("User_model", 'Doctor_model', 'Auditlog_model'));
    }

    /**
     * 
     * This function is use for register new user in application using only mobile number or email.
     * this function use in both case for register normal user or social user
     * 
     * @author 
     * 
     * Modified Date :- 2018-01-22
     * 
     */
    public function register_post() {
        $this->my_response['status'] = false;
        $this->my_response['message'] = "Please visit: https://medsign.care/";
        $this->send_response();
        $email = !empty($this->post_data['email']) ? trim($this->Common_model->escape_data($this->post_data['email'])) : "";
        $country_code = !empty($this->post_data['country_code']) ? trim($this->Common_model->escape_data($this->post_data['country_code'])) : DEFAULT_COUNTRY_CODE;
        $phone_number = !empty($this->post_data['phone_number']) ? trim($this->Common_model->escape_data($this->post_data['phone_number'])) : "";
        $password = !empty($this->post_data['password']) ? trim($this->Common_model->escape_data($this->post_data['password'])) : "";
        $language = !empty($this->post_data['language']) ? trim($this->Common_model->escape_data($this->post_data['language'])) : "";
        $user_type = !empty($this->post_data['user_type']) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : "";

        $device_type = !empty($this->post_data['device_type']) ? trim($this->Common_model->escape_data($this->post_data['device_type'])) : "";
        $device_token = !empty($this->post_data['device_token']) ? trim($this->Common_model->escape_data($this->post_data['device_token'])) : "";
        $is_term_accept = !empty($this->post_data['is_term_accept']) ? trim($this->Common_model->escape_data($this->post_data['is_term_accept'])) : "";
        try {
            if (
                    empty($email) ||
                    empty($password) ||
                    empty($phone_number) ||
                    empty($country_code) ||
                    empty($device_type) ||
                    empty($language) ||
                    empty($user_type)
            ) {
                $this->bad_request();
                exit;
            }

            $language_array = array('1', '2');
            if (!in_array($language, $language_array)) {
                $this->bad_request();
            }

            if (validate_email($email)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("invalid_email");
                $this->send_response();
            }

            if (validate_phone_number($phone_number)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("invalid_phone_number");
                $this->send_response();
            }

            $user_type_array = array('1', '2');
            if (!in_array($user_type, $user_type_array)) {
                $this->bad_request();
            }

            $check_number = $this->User_model->get_details_by_number($phone_number, $user_type);

            if (!empty($check_number) && count($check_number) > 0) {
                if (
                        (
                        isset($check_number['user_phone_verified']) &&
                        $check_number['user_phone_verified'] == 1
                        ) ||
                        (
                        $check_number['user_email'] != $email
                        )
                ) {
                    $this->my_response['status'] = false;
                    $this->my_response['user_phone_is_verified'] = '1';
                    $this->my_response['message'] = sprintf(lang("user_number_register"), $phone_number);
                    $this->send_response();
                }
            }

            $check_email = $this->User_model->get_details_by_email($email, $user_type);

            if (!empty($check_email) && count($check_email) > 0) {
//                if (isset($check_email['user_email_verified']) && $check_email['user_email_verified'] == 1) {
//                    $this->my_response['status'] = false;
//                    $this->my_response['is_email_verified'] = '1';
//                    $this->my_response['message'] = lang("user_register_email_exist");
//                    $this->send_response();
//                } else if (isset($check_email['user_email_verified']) && $check_email['user_email_verified'] == 2) {
//                    $this->my_response['status'] = true;
//                    $this->my_response['is_email_verified'] = '2';
//                    $this->my_response['message'] = lang("user_register_email_exist_but_not_verified");
//                    $this->send_response();
//                }
                $this->my_response['status'] = false;
                $this->my_response['is_email_verified'] = '1';
                $this->my_response['message'] = lang("user_register_email_exist");
                $this->send_response();
            }

            $otp = getUniqueToken(6, 'numeric');
            $message = sprintf(OTP_MESSAGE, $otp);
            $send_otp = array(
                'phone_number' => $country_code . $phone_number,
                'message' => $message,
            );
            $sening_sms = send_message_by_vibgyortel($send_otp);
			//$sening_sms = TRUE;

            if ($sening_sms) {

                $insert_user = array(
                    'temp_user_email' => $email,
                    'temp_user_password' => password_hash($password, PASSWORD_BCRYPT),
                    'temp_user_phone_number' => $phone_number,
                    'temp_user_language_id' => $language,
                    'temp_user_created_at' => $this->utc_time_formated,
                    'temp_user_gender' => 'male',
                    'temp_user_unique_id' => strtoupper($this->Common_model->escape_data(str_rand_access_token(8))),
                    'temp_auth_code' => $otp,
                    'temp_auth_otp_expiry_time' => date('Y-m-d H:i:s', strtotime(OTP_EXPIRE_TOKEN_TIME, strtotime($this->utc_time_formated))),
                    'temp_is_term_accepted' => $is_term_accept,
                    'temp_is_term_accepted_date' => date('Y-m-d H:i:s'),
                );

                //if user type is doctor then status is inactive
                if ($user_type == 2) {
//                    $insert_user['user_status'] = 2;
                    $insert_user['temp_user_user_type'] = 2;
                }

                $insert_id = $this->User_model->register_temp_user($insert_user);
                if ($insert_id > 0) {
                    $user_data = $this->User_model->get_user_temp_details_by_id($insert_id);
                    $this->my_response['status'] = true;
                    $this->my_response['message'] = lang("otp_sent_to_mobile");
                    $this->my_response['user_data'] = $user_data;
                } else {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("user_register_fail");
                }
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("sms_otp_send_fail");
            }

            $this->send_response();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->store_error();
        }
    }

    /**
     * This function use for check user login
     * @author 
     * @uses Login with email or login with Mobile
     * 
     * Modified Date :- 2018-01-22
     * 
     */
    public function login_post() {
        $email = !empty($this->post_data['email']) ? $this->Common_model->escape_data($this->post_data['email']) : "";
        $password = !empty($this->post_data['password']) ? $this->Common_model->escape_data($this->post_data['password']) : "";
        $login_with = !empty($this->post_data['login_with']) ? $this->Common_model->escape_data($this->post_data['login_with']) : "";
        $country_code = !empty($this->post_data['country_code']) ? $this->Common_model->escape_data($this->post_data['country_code']) : "+91";
        $phone_number = !empty($this->post_data['phone_number']) ? $this->Common_model->escape_data($this->post_data['phone_number']) : "";
        $unique_id = !empty($this->post_data['unique_id']) ? $this->Common_model->escape_data($this->post_data['unique_id']) : "";

        $device_type = !empty($this->post_data['device_type']) ? $this->Common_model->escape_data($this->post_data['device_type']) : "";
        $device_token = !empty($this->post_data['device_token']) ? $this->Common_model->escape_data($this->post_data['device_token']) : "";
        $user_type = !empty($this->post_data['user_type']) ? $this->Common_model->escape_data($this->post_data['user_type']) : "";
        if($phone_number != "5555578688"){
            $this->my_response['status'] = false;
            $this->my_response['message'] = "Please visit: https://medsign.care/";
            $this->send_response();
        }
        try {
            $user_ip_address = $_SERVER['REMOTE_ADDR'];
            $this->my_response['status_code'] = 1000;

            if (empty($user_type)) {
                $this->bad_request();
                exit;
            }

            //check ip is block or not
            $start_date = date("Y-m-d 00:00:00");
            $end_date = date("Y-m-d 23:59:59");
            $check_ip_blocked_sql = "
                SELECT 
                    count(ip_blocked_id) as total_count
                FROM me_ip_blocked
                WHERE 
                    ip_blocked_ip='" . $user_ip_address . "' AND 
                    ip_blocked_date BETWEEN '" . $start_date . "' AND '" . $end_date . "'
                LIMIT 1
            ";
            $check_ip_blocked = $this->Common_model->get_single_row_by_query($check_ip_blocked_sql);
            if (!empty($check_ip_blocked) && $check_ip_blocked['total_count'] >= LOGIN_WRONG_ATTEMPT) {
                $this->my_response['status'] = false;
                $this->my_response['status_code'] = 1001;
                $this->my_response['message'] = lang("login_ip_blocked");
                $this->send_response();
            }

            $access_token = $this->Common_model->escape_data(str_rand_access_token(64));
            $need_to_block_account = FALSE;
            if ($login_with == 1 && !empty($phone_number)) {
                $user_details = $this->User_model->get_details_by_number($phone_number, $user_type);
                if (!empty($user_details) && count($user_details) > 0) {
                    if ($user_details['user_status'] == 1) {

                        $auth_update_where = array(
                            'auth_user_id' => $user_details['user_id'],
                            "auth_type" => 2
                        );
                        $auth_resend_data = $this->User_model->get_single_row(TBL_USER_AUTH, "auth_resend_count,auth_resend_timestamp", $auth_update_where);
                        if (!empty($auth_resend_data)) {
                            if ($auth_resend_data['auth_resend_count'] >= RESEND_OTP_LIMIT) {
                                $this->my_response['status'] = false;
                                $this->my_response['message'] = lang("otp_resend_limit_reach");
                                $this->send_response();
                            }
                        }

                        $enable_2_way_authentication = 2;
                        //check two way authentication enabled or not
                        $where_array = array(
                            'setting_user_id' => $user_details['user_id'],
                            'setting_type' => 2,
                            'setting_status' => 1
                        );
                        $get_setting_data = $this->Common_model->get_single_row(TBL_SETTING, 'setting_data', $where_array);
                        if (!empty($get_setting_data)) {
                            $get_setting_data = json_decode($get_setting_data['setting_data'], true);
                            if (!empty($get_setting_data)) {
                                foreach ($get_setting_data as $data) {
                                    if ($data['id'] == 1 && $data['status'] == 1) {
                                        $enable_2_way_authentication = 1;
                                    }
                                }
                            }
                        }

                        if ($enable_2_way_authentication == 1) {
                            $sening_sms = true;
                        } else {
                            $otp = getUniqueToken(6, 'numeric');
                            $message = sprintf(OTP_MESSAGE, $otp);
                            $send_otp = array(
                                'phone_number' => $country_code . $phone_number,
                                'message' => $message,
                            );
                            $sening_sms = send_message_by_vibgyortel($send_otp);
                            //$sening_sms = true;
                        }

                        if ($sening_sms) {
                            if ($enable_2_way_authentication == 2) {
                                $auth_update_data = array(
                                    'auth_code' => $otp,
                                    'auth_otp_expiry_time' => date('Y-m-d H:i:s', strtotime(OTP_EXPIRE_TOKEN_TIME, strtotime($this->utc_time_formated))),
                                    "auth_type" => 2,
                                    "auth_attempt_count" => 0,
                                    "auth_resend_timestamp" => date("Y-m-d H:i:s")
                                );
                                $auth_update_where = array(
                                    'auth_user_id' => $user_details['user_id'],
                                    "auth_type" => 2
                                );
                                $auth_resend_data = $this->User_model->get_single_row(TBL_USER_AUTH, "auth_resend_count", $auth_update_where);
                                if (!empty($auth_resend_data)) {
                                    $auth_update_data['auth_resend_count'] = $auth_resend_data['auth_resend_count'] + 1;
                                }

                                $this->User_model->update_auth_details($auth_update_data, $auth_update_where);
                            }
                            $this->my_response = array(
                                "status" => true,
                                "authentication_flag" => (string) $enable_2_way_authentication,
                                "user_data" => array("user_phone_number" => $user_details['user_phone_number']),
                                "is_number_verified" => $user_details['user_phone_verified']
                            );
                            if ($enable_2_way_authentication == 1) {
                                $this->my_response['message'] = lang("2_way_enabled");
                            } else {
                                $this->my_response['message'] = lang("otp_sent_to_mobile");
                            }
                        } else {
                            $this->my_response = array(
                                "status" => false,
                                "message" => lang("sms_otp_send_fail")
                            );
                        }
                    } else {
                        $this->my_response = array(
                            "status" => false,
                            "message" => lang("user_login_block")
                        );
                    }
                } else {
                    $need_to_block_account = true;
                    $this->my_response = array(
                        "status" => false,
                        "message" => lang("phone_number_not_found")
                    );
                }
            } else if ((!empty($unique_id) || !empty($email) || !empty($phone_number)) && !empty($password)) {

                if ($login_with == 2) {
                    $result = $this->User_model->get_details_by_email($email, $user_type);
                } else if ($login_with == 3) {
                    $result = $this->User_model->get_user_details_by_uniqueid($unique_id, $user_type);
                } else {
                    $result = $this->User_model->check_user_number_exists($phone_number, $user_type);
                }

                if (!empty($result) && count($result) > 0) {

                    //if ($result['user_status'] == 1 && $result['user_phone_verified'] == 1) {
                    if ($result['user_status'] == 1) {
                        if (password_verify($password, $result['user_password'])) {
                            unset($result['user_password']);
                            $enable_2_way_authentication = 2;
                            //check two way authentication enabled or not
                            $where_array = array(
                                'setting_user_id' => $result['user_id'],
                                'setting_type' => 2,
                                'setting_status' => 1
                            );
                            $get_setting_data = $this->Common_model->get_single_row(TBL_SETTING, 'setting_data', $where_array);
                            if (!empty($get_setting_data)) {
                                $get_setting_data = json_decode($get_setting_data['setting_data'], true);
                                if (!empty($get_setting_data)) {
                                    foreach ($get_setting_data as $data) {
                                        if ($data['id'] == 1 && $data['status'] == 1) {
                                            $enable_2_way_authentication = 1;
                                        }
                                    }
                                }
                            }

                            if ($enable_2_way_authentication == 2) {
                                $token = $this->add_device_token($result['user_id'], $access_token, $device_token, $device_type);
                                if ($token) {
                                    $get_user_details = $this->User_model->get_details_by_id($result['user_id']);
                                    if ($user_type == 1) {
                                        unset($get_user_details['user_unique_id']);
                                        //get user other detail
                                        $get_user_other_detail = $this->Common_model->get_single_row(TBL_USER_DETAILS, 'user_details_agree_terms', array('user_details_user_id' => $result['user_id']));
                                        $get_user_details['user_details_agree_terms'] = 1;
                                        if (!empty($get_user_other_detail)) {
                                            $get_user_details['user_details_agree_terms'] = $get_user_other_detail['user_details_agree_terms'];
                                        }
                                    } else {
                                        $get_user_details['user_type'] = '';
                                        $get_user_details['clinic_doctor_id'] = '';
                                        $doctor_clinic_mapping = $this->get_user_role($result['user_id']);
                                        if ($doctor_clinic_mapping) {
                                            $get_user_details['user_type'] = $doctor_clinic_mapping['doctor_clinic_mapping_role_id'];
                                            $get_user_details['clinic_doctor_id'] = $doctor_clinic_mapping['doctor_clinic_mapping_doctor_id'];
                                        }
                                        $user_detail = $this->Common_model->get_single_row(TBL_DOCTOR_DETAILS, '*', array(
                                            'doctor_detail_doctor_id' => $result['user_id'],
                                        ));
                                        $get_user_details['doctor_detail_is_term_accepted'] = 0;
                                        if ($user_detail) {
                                            $get_user_details['doctor_detail_is_term_accepted'] = $user_detail['doctor_detail_is_term_accepted'];
                                        }
                                    }
                                    //Create audit log
                                    $this->Auditlog_model->create_audit_log($get_user_details['user_id'], $get_user_details['user_type_id'], AUDIT_SLUG_ARR['LOGIN_ACTION']);
                                    $get_user_details['user_photo_filepath'] = get_image_thumb($get_user_details['user_photo_filepath']);
                                    $this->my_response = array(
                                        "status" => true,
                                        "user_data" => $get_user_details,
                                        "access_token" => $access_token,
                                        "authentication_flag" => (string) $enable_2_way_authentication
                                    );


                                    //reset the last send otp
                                    $auth_update_data = array(
                                        'auth_code' => NULL,
                                        'auth_otp_expiry_time' => NULL,
                                        "auth_attempt_count" => 0,
                                        "auth_resend_count" => 0,
                                        "auth_resend_timestamp" => date("Y-m-d")
                                    );
                                    $auth_update_where = array(
                                        'auth_user_id' => $get_user_details['user_id'],
                                        "auth_type" => 2
                                    );
                                    $this->User_model->update_auth_details($auth_update_data, $auth_update_where);


                                    if ($enable_2_way_authentication == 1) {
                                        $this->my_response['message'] = lang('otp_sent_to_mobile');
                                    } else {
                                        $user_update_data = array('last_login_at' => $this->utc_time_formated);
                                        $user_update_where = array('user_id' => $get_user_details['user_id']);
                                        $this->User_model->update_user_data($user_update_data, $user_update_where);
                                        $this->my_response['message'] = lang('login_email_password_login_success');
                                    }
                                } else {
                                    $this->my_response = array(
                                        "status" => false,
                                        "message" => lang("user_login_token_generate_error")
                                    );
                                }
                            } else {
                                $auth_update_where = array(
                                    'auth_user_id' => $result['user_id'],
                                    "auth_type" => 2
                                );
                                $auth_resend_data = $this->User_model->get_single_row(TBL_USER_AUTH, "auth_resend_count,auth_resend_timestamp", $auth_update_where);
                                if (!empty($auth_resend_data)) {
                                    if ($auth_resend_data['auth_resend_count'] >= RESEND_OTP_LIMIT) {
                                        $this->my_response['status'] = false;
                                        $this->my_response['message'] = lang("otp_resend_limit_reach");
                                        $this->send_response();
                                    }
                                }

                                /*[START] ADDITION TO FIX THE MOBILE APP API ISSUE*/
                                $get_user_details = array("user_phone_number" => $result['user_phone_number']);
                                if ($user_type == 1) {
                                    $get_user_details = $this->User_model->get_details_by_id($result['user_id']);
                                    //get user other detail
                                    $get_user_other_detail = $this->Common_model->get_single_row(TBL_USER_DETAILS, 'user_details_agree_terms', array('user_details_user_id' => $result['user_id']));
                                    $get_user_details['user_details_agree_terms'] = 1;
                                    if (!empty($get_user_other_detail)) {
                                        $get_user_details['user_details_agree_terms'] = $get_user_other_detail['user_details_agree_terms'];
                                    }
                                }
                                /*[END] ADDITION TO FIX THE MOBILE APP API ISSUE*/
                                $get_user_details['user_photo_filepath'] = get_image_thumb($get_user_details['user_photo_filepath']);
                                $this->my_response = array(
                                    "status" => true,
                                    "message" => lang('otp_sent_to_mobile'),
                                    "authentication_flag" => (string) $enable_2_way_authentication,
                                    "user_data" => $get_user_details,
                                    //"user_data" => array("user_phone_number" => $result['user_phone_number']),
                                    "is_number_verified" => (string) 1
                                );
                            }
                        } else {

                            if ($login_with == 2) {
                                $this->my_response = array(
                                    "status" => false,
                                    "message" => lang("login_email_password_not_match")
                                );
                            } else if ($login_with == 3) {
                                $this->my_response = array(
                                    "status" => false,
                                    "message" => lang("login_unique_password_not_match")
                                );
                            } else {
                                $this->my_response = array(
                                    "status" => false,
                                    "message" => lang("login_number_password_not_match")
                                );
                            }
                        }
                    } else {
                        if ($result['user_phone_verified'] == 2 && $result['user_status'] == 1) {
                            if (!empty($phone_number)) {
                                $auth_update_where = array(
                                    'auth_user_id' => $result['user_id'],
                                    "auth_type" => 2
                                );
                                $auth_resend_data = $this->User_model->get_single_row(TBL_USER_AUTH, "auth_resend_count,auth_resend_timestamp", $auth_update_where);
                                if (!empty($auth_resend_data)) {
                                    if ($auth_resend_data['auth_resend_count'] >= RESEND_OTP_LIMIT) {
                                        $this->my_response['status'] = false;
                                        $this->my_response['message'] = lang("otp_resend_limit_reach");
                                        $this->send_response();
                                    }
                                }


                                $otp = getUniqueToken(6, 'numeric');
                                $message = sprintf(OTP_MESSAGE, $otp);
                                $send_otp = array(
                                    'phone_number' => $country_code . $phone_number,
                                    'message' => $message,
                                );
                                $sening_sms = send_message_by_vibgyortel($send_otp);
                                //$sening_sms = TRUE;
                                if ($sening_sms) {

                                    $auth_update_data = array(
                                        'auth_code' => $otp,
                                        'auth_otp_expiry_time' => date('Y-m-d H:i:s', strtotime(OTP_EXPIRE_TOKEN_TIME, strtotime($this->utc_time_formated))),
                                        "auth_type" => 2,
                                        "auth_attempt_count" => 0,
                                        "auth_resend_timestamp" => date("Y-m-d H:i:s")
                                    );
                                    $auth_update_where = array(
                                        'auth_user_id' => $result['user_id'],
                                        "auth_type" => 2
                                    );
                                    $auth_resend_data = $this->User_model->get_single_row(TBL_USER_AUTH, "auth_resend_count", $auth_update_where);
                                    if (!empty($auth_resend_data)) {
                                        $auth_update_data['auth_resend_count'] = $auth_resend_data['auth_resend_count'] + 1;
                                    }
                                    $this->User_model->update_auth_details($auth_update_data, $auth_update_where);

                                    $this->my_response = array(
                                        "status" => true,
                                        "message" => lang("otp_sent_to_mobile"),
                                        "authentication_flag" => (string) 1,
                                        "user_data" => array("user_phone_number" => $result['user_phone_number']),
                                        "is_number_verified" => (string) 2
                                    );
                                }
                            } else {
                                $this->my_response = array(
                                    "status" => false,
                                    "message" => lang("number_not_exists_unverified")
                                );
                            }
                        } else {
                            $this->my_response = array(
                                "status" => false,
                                "message" => lang("user_login_block")
                            );
                        }
                    }
                } else {
                    $need_to_block_account = true;
                    if ($login_with == 2) {
                        $this->my_response = array(
                            "status" => false,
                            "message" => lang("login_email_not_found")
                        );
                    } else if ($login_with == 3) {
                        $this->my_response = array(
                            "status" => false,
                            "message" => lang("unique_number_not_found")
                        );
                    } else {
                        $this->my_response = array(
                            "status" => false,
                            "message" => lang("phone_number_not_found")
                        );
                    }
                }
            } else {
                $this->bad_request();
                exit;
            }

            if ($need_to_block_account) {
                $add_block_ip_entry = array(
                    "ip_blocked_ip" => $user_ip_address,
                    "ip_blocked_date" => date("Y-m-d H:i:s")
                );
                $this->Common_model->insert("me_ip_blocked", $add_block_ip_entry);
            }


            $this->send_response();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description : This function verify the clinic number and update the data if verified
     * 
     * @author Manish Ramnani
     * 
     */
    public function verify_otp_for_clinic_post() {

        try {
            $phone_number = !empty($this->post_data['phone_number']) ? $this->Common_model->escape_data($this->post_data['phone_number']) : "";
            $otp = !empty($this->post_data['otp']) ? $this->Common_model->escape_data($this->post_data['otp']) : "";
            $clinic_id = !empty($this->post_data['clinic_id']) ? $this->Common_model->escape_data($this->post_data['clinic_id']) : "";

            if (empty($phone_number) ||
                    empty($otp) ||
                    empty($clinic_id)
            ) {
                $this->bad_request();
            }

            $columns = 'auth_code,
                        auth_user_id,
                        auth_otp_expiry_time, 
                        auth_phone_number';

            $where = array(
                'auth_phone_number' => $phone_number,
                'auth_type' => 2,
                'auth_user_id' => $clinic_id,
                'auth_is_clinic' => 1
            );

            $get_auth_details = $this->Common_model->get_single_row(TBL_USER_AUTH, $columns, $where);


            if (!empty($get_auth_details)) {
                if (strtotime($get_auth_details['auth_otp_expiry_time']) >= $this->utc_time) {
                    if ($get_auth_details['auth_code'] == $otp) {
                        $auth_update_data = array(
                            'auth_code' => NULL,
                            'auth_otp_expiry_time' => NULL,
                            'auth_phone_number' => NULL
                        );
                        $auth_update_where = array(
                            'auth_user_id' => $get_auth_details['auth_user_id'],
                            'auth_type' => 2,
                            'auth_is_clinic' => 1
                        );

                        $is_auth_updated = $this->User_model->update_auth_details($auth_update_data, $auth_update_where);

                        if ($is_auth_updated > 0) {

                            $update_clinic_number = array(
                                'clinic_contact_number' => $phone_number,
                                'clinic_phone_verified' => 1
                            );

                            $update_clinic_where = array(
                                'clinic_id' => $clinic_id
                            );

                            $is_profile_update = $this->Common_model->update(TBL_CLINICS, $update_clinic_number, $update_clinic_where);

                            if ($is_profile_update) {
                                $this->my_response = array(
                                    "status" => true,
                                    "message" => lang("clinic_number_update")
                                );
                            } else {
                                $this->my_response = array(
                                    "status" => false,
                                    "message" => lang("failure")
                                );
                            }
                        } else {
                            $this->my_response = array(
                                "status" => false,
                                "message" => lang("failure")
                            );
                        }
                    } else {
                        $this->my_response = array(
                            "status" => false,
                            "message" => lang("otp_verfication_fail")
                        );
                    }
                } else {
                    $this->my_response = array(
                        "status" => false,
                        "message" => lang("otp_token_expire")
                    );
                }
            } else {
                $this->my_response = array(
                    "status" => false,
                    "message" => lang("phone_number_not_found")
                );
            }

            $this->send_response();
        } catch (ErrorException $e) {
            $this->error = $e->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description : THis function is used if the user change the phone number while updating the profile
     * 
     * @author Manish Ramnani
     * 
     */
    public function verify_update_number_otp_post() {

        $phone_number = !empty($this->post_data['phone_number']) ? $this->post_data['phone_number'] : "";
        $otp = !empty($this->post_data['otp']) ? $this->post_data['otp'] : "";
        $country_code = !empty($this->post_data['country_code']) ? $this->post_data['country_code'] : "";
        $user_type = !empty($this->post_data['user_type']) ? $this->post_data['user_type'] : "";
        $other_user_id = !empty($this->post_data['other_user_id']) ? $this->post_data['other_user_id'] : "";

        try {
            if (
                    empty($phone_number) ||
                    empty($otp) ||
                    empty($country_code)
            ) {
                $this->bad_request();
                exit;
            }
            if (empty($user_type)) {
                $user_type = 1;
            }

            $columns = 'auth_code,
                        auth_attempt_count,
                        auth_user_id,
                        auth_otp_expiry_time, 
                        auth_phone_number,
                        auth_id';

            $where = array(
                'auth_phone_number' => $phone_number,
                'user_status !=' => 9,
                'auth_type' => 2,
                'user_type' => $user_type,
                'auth_user_id' => $other_user_id
            );

            $left_join = array(
                TBL_USERS => "user_id = auth_user_id"
            );
            $get_auth_details = $this->Common_model->get_single_row(TBL_USER_AUTH, $columns, $where, $left_join, 'LEFT');

            if (!empty($get_auth_details)) {
                if (strtotime($get_auth_details['auth_otp_expiry_time']) >= $this->utc_time) {
                    if ($get_auth_details['auth_code'] == $otp) {
                        $auth_update_data = array(
                            'auth_code' => NULL,
                            'auth_otp_expiry_time' => NULL,
                            'auth_phone_number' => NULL
                        );
                        $auth_update_where = array(
                            'auth_user_id' => $get_auth_details['auth_user_id'],
                            'auth_type' => 2
                        );

                        $is_auth_updated = $this->User_model->update_auth_details($auth_update_data, $auth_update_where);

                        if ($is_auth_updated > 0) {

                            $update_user_number = array(
                                'user_phone_number' => $phone_number,
                                'user_phone_verified' => 1
                            );

                            $is_profile_update = $this->User_model->update_profile($get_auth_details['auth_user_id'], $update_user_number);
                            $user_data = $this->User_model->get_user_details_by_id($get_auth_details['auth_user_id'], $user_type);

                            if ($user_type == 1) {
                                //get family medical history data
                                $family_data = $this->User_model->get_family_medical_history($get_auth_details['auth_user_id']);
                                $user_data['family_medical_history_data'] = $family_data;

                                //get the user diseases data
                                $disease_data = $this->User_model->get_diseases_data($get_auth_details['auth_user_id']);
                                $user_data['diseases_data'] = $disease_data;
                            }

                            if ($is_profile_update) {

                                $this->my_response = array(
                                    "status" => true,
                                    "message" => lang("user_profile_update_success"),
                                    "user_data" => $user_data
                                );
                            } else {
                                $this->my_response = array(
                                    "status" => false,
                                    "message" => lang("failure")
                                );
                            }
                        } else {
                            $this->my_response = array(
                                "status" => false,
                                "message" => lang("failure")
                            );
                        }
                    } else {
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = "You have entered invalid otp";
                    }
                } else {
                    $this->my_response = array(
                        "status" => false,
                        "message" => lang("otp_token_expire")
                    );
                }
            } else {
                $this->my_response = array(
                    "status" => false,
                    "message" => lang("phone_number_not_found")
                );
            }

            $this->send_response();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->store_error();
        }
    }

    /**
     * 
     * this function to resend otp
     * 
     * @author 
     * 
     * Modified Date :- 2018-01-22
     * 
     */
    public function register_resend_otp_post() {

        $registered_id = !empty($this->post_data['registered_id']) ? $this->post_data['registered_id'] : "";

        try {
            if (
                    empty($registered_id)
            ) {
                $this->bad_request();
                exit;
            }

            $user_data = $this->User_model->get_user_temp_details_by_id($registered_id);

            if (
                    (!empty($user_data) && count($user_data) > 0)
            ) {

                if (!empty($user_data['temp_auth_resend_timestamp']) && (strtotime($user_data['temp_auth_resend_timestamp']) + RESEND_OTP_NEXT_TIME) >= time()) {
                    $wating_time = (strtotime($user_data['temp_auth_resend_timestamp']) + RESEND_OTP_NEXT_TIME) - time();
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = sprintf(lang("otp_resend_time_limit"), $wating_time);
                    $this->send_response();
                }

                if ($user_data['temp_auth_resend_count'] >= RESEND_OTP_LIMIT) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("otp_resend_limit_reach");
                    $this->send_response();
                }


                $otp = getUniqueToken(6, 'numeric');
                $message = sprintf(OTP_MESSAGE, $otp);
                $send_otp = array(
                    'phone_number' => DEFAULT_COUNTRY_CODE . $user_data['temp_user_phone_number'],
                    'message' => $message,
                );
                $sening_sms = send_message_by_vibgyortel($send_otp);
                //$sening_sms = TRUE;

                if ($sening_sms) {

                    $auth_update_data = array(
                        'temp_auth_code' => $otp,
                        'temp_auth_otp_expiry_time' => date('Y-m-d H:i:s', strtotime(OTP_EXPIRE_TOKEN_TIME, strtotime($this->utc_time_formated))),
                        "temp_auth_attempt_count" => 0,
                        "temp_auth_resend_timestamp" => date("Y-m-d H:i:s"),
                        "temp_auth_resend_count" => $user_data['temp_auth_resend_count'] + 1
                    );

                    $this->Common_model->update(TBL_USER_TEMP, $auth_update_data, array('temp_user_id' => $registered_id));

                    $this->my_response['status'] = true;
                    $this->my_response['message'] = lang("otp_sent_to_mobile");
                    $this->send_response();
                } else {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("sms_otp_send_fail");
                    $this->send_response();
                }
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("user_detail_not_found");
                $this->send_response();
            }
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to register the user after verification of the otp
     * 
     * @author Manish Ramnani
     * 
     * Modified Date :- 2018-10-01
     */
    public function register_verify_otp_post() {

        $otp = !empty($this->post_data['otp']) ? $this->post_data['otp'] : "";
        $registered_id = !empty($this->post_data['registered_id']) ? $this->post_data['registered_id'] : "";

        $device_type = !empty($this->post_data['device_type']) ? trim($this->Common_model->escape_data($this->post_data['device_type'])) : "";
        $device_token = !empty($this->post_data['device_token']) ? trim($this->Common_model->escape_data($this->post_data['device_token'])) : "";

        try {

            if (empty($otp) ||
                    empty($registered_id) ||
                    empty($device_type)
            ) {
                $this->bad_request();
            }

            $user_data = $this->User_model->get_user_temp_details_by_id($registered_id);

            if (!empty($user_data)) {

                if (strtotime($user_data['temp_auth_otp_expiry_time']) >= $this->utc_time) {

                    if ($user_data['temp_auth_attempt_count'] >= VERIFY_OTP_LIMIT) {
                        $this->my_response['status'] = false;
                        $this->my_response['temp_user_phone_verified'] = '2';
                        $this->my_response['message'] = lang("otp_verfication_limit_reach");
                        $this->send_response();
                    }

                    if ($user_data['temp_auth_code'] == $otp) {

                        //register the user now
                        $insert_user = array(
                            'user_email' => $user_data['temp_user_email'],
                            'user_password' => $user_data['temp_user_password'],
                            'user_phone_number' => $user_data['temp_user_phone_number'],
                            'user_language_id' => $user_data['temp_user_language_id'],
                            'user_created_at' => $this->utc_time_formated,
                            'user_gender' => $user_data['temp_user_gender'],
                            'user_unique_id' => $user_data['temp_user_unique_id'],
                            'user_phone_verified' => 1,
                            'user_plan_id' => DEFAULT_USER_PLAN_ID
                        );

                        $user_type = $user_data['temp_user_user_type'];

                        //if user type is doctor then status is inactive
                        if ($user_type == 2) {
                            //$insert_user['user_status'] = 2;
                            $insert_user['user_type'] = 2;
                        }

                        $this->db->trans_start();

                        $insert_id = $this->User_model->register_user($insert_user);

                        if ($insert_id > 0) {
                            $share_link_data = array(
                                'patient_id' => $insert_id,
                                'is_set_password' => 1,
                                'created_at' => $this->utc_time_formated
                            );
                            $this->Common_model->insert('me_patient_share_link_log', $share_link_data);
                            // save user detail 
                            $user_detail = array(
                                'doctor_detail_doctor_id' => $insert_id,
                                'doctor_detail_is_term_accepted' => $user_data['temp_is_term_accepted'],
                                'doctor_detail_term_accepted_date' => $user_data['temp_is_term_accepted_date'],
                                'doctor_detail_color_code' => random_color()
                            );
                            $this->User_model->insert(TBL_DOCTOR_DETAILS, $user_detail);

                            //store the user authentication details
                            $user_auth_insert_array = array(
                                'auth_user_id' => $insert_id,
                                'auth_type' => 2,
                                'auth_code' => NULL,
                                'auth_otp_expiry_time' => NULL,
                                'auth_created_at' => $this->utc_time_formated
                            );
                            $this->User_model->store_auth_detail($user_auth_insert_array);

                            //by default 2 way authentication on for the patient and doctor
                            $setting_array[] = array(
                                'id' => "1",
                                'name' => "data security",
                                'status' => "2"
                            );
                            $insert_setting_array = array(
                                'setting_user_id' => $insert_id,
                                'setting_clinic_id' => '',
                                'setting_data' => json_encode($setting_array),
                                'setting_type' => 2,
                                'setting_data_type' => 1,
                                'setting_created_at' => $this->utc_time_formated
                            );
                            $this->Common_model->insert(TBL_SETTING, $insert_setting_array);

                            $reset_token = str_rand_access_token(20);

                            $user_auth_email_insert_array = array(
                                'auth_user_id' => $insert_id,
                                'auth_type' => 1,
                                'auth_code' => $reset_token,
                                'auth_otp_expiry_time' => date('Y-m-d H:i:s', strtotime(OTP_EXPIRE_TOKEN_TIME, strtotime($this->utc_time_formated))),
                                'auth_created_at' => $this->utc_time_formated
                            );

                            $this->User_model->store_auth_detail($user_auth_email_insert_array);

                            $verify_link = DOMAIN_URL . "verifyaccount/" . $reset_token;
                            $email = $user_data['temp_user_email'];

                            $send_mail = array(
                                'user_name' => 'User',
                                'user_email' => $email,
                                'verify_link' => $verify_link
                            );

                            if ($user_type == 1) {
                                $send_mail['template_id'] = 1;
                            } else {
                                $send_mail['template_id'] = 2;
                            }

                            $cron_job_path = CRON_PATH . " notification/send_mail_background/" . base64_encode(json_encode($send_mail));
                            exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");

                            $access_token = $this->Common_model->escape_data(str_rand_access_token(64));
                            $token = $this->add_device_token($insert_id, $access_token, $device_token, $device_type);

                            if ($token) {
                                $user_data = $this->User_model->get_details_by_id($insert_id);
                                $this->my_response['status'] = true;
                                $this->my_response['message'] = lang("register_successfully");
                                $this->my_response['user_data'] = $user_data;
                                $this->my_response['access_token'] = $access_token;
                            }

                            //remove the entry from the temp detail
                            $update_temp_user = array(
                                'temp_user_status' => 9,
                                'temp_user_updated_at' => $this->utc_time_formated,
                            );
                            $update_where = array(
                                'temp_user_id' => $registered_id
                            );
                            $this->Common_model->update(TBL_USER_TEMP, $update_temp_user, $update_where);

                            if ($this->db->trans_status() !== FALSE) {
                                $this->db->trans_commit();
                            } else {
                                $this->db->trans_rollback();
                                $this->my_response['status'] = false;
                                $this->my_response['message'] = lang('failure');
                            }
                        } else {
                            $this->my_response['status'] = false;
                            $this->my_response['message'] = lang("failure");
                        }
                    } else {
                        $remain_otp_verify_limit = VERIFY_OTP_LIMIT - ($user_data['temp_auth_attempt_count'] + 1);
                        $this->User_model->increase_opt_temp_user_limit(1, $user_data['temp_user_id'], ($user_data['temp_auth_attempt_count'] + 1));
                        $this->my_response['status'] = false;
                        $this->my_response['temp_user_phone_verified'] = '2';
                        $this->my_response['message'] = sprintf(lang("invalid_otp_for_login"), $remain_otp_verify_limit);
                    }
                } else {
                    $this->my_response['status'] = false;
                    $this->my_response['temp_user_phone_verified'] = '2';
                    $this->my_response['message'] = lang("otp_token_expire");
                    $this->my_response['is_expire'] = "1";
                }
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("user_detail_not_found");
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * 
     * This function is use for register new user in application useing only mobile number.
     * this function use in both case for register normal user or social user
     * 
     * @author 
     * 
     * Modified Date :- 2018-01-22
     * 
     */
    public function verify_otp_post() {
        
        $phone_number = !empty($this->post_data['phone_number']) ? $this->post_data['phone_number'] : "";
        $otp = !empty($this->post_data['otp']) ? $this->post_data['otp'] : "";
        $country_code = !empty($this->post_data['country_code']) ? $this->post_data['country_code'] : "";
        $is_login = !empty($this->post_data['is_login']) ? $this->post_data['is_login'] : "";
        $device_type = !empty($this->post_data['device_type']) ? $this->post_data['device_type'] : "";
        $user_type = !empty($this->post_data['user_type']) ? $this->post_data['user_type'] : "";
        $device_token = !empty($this->post_data['device_token']) ? $this->Common_model->escape_data($this->post_data['device_token']) : "";
        
        try {
            if (
                    empty($phone_number) ||
                    empty($otp) ||
                    empty($country_code)
            ) {
                $this->bad_request();
                exit;
            }
            if (empty($user_type)) {
                $user_type = 1;
            }
            $user_details = $this->User_model->get_otp_details_by_phone_number($phone_number, $user_type);

            if (!empty($user_details) && count($user_details) > 0) {
                if (isset($user_details['user_phone_verified']) && $user_details['user_phone_verified'] == 1) {
                    if (strtotime($user_details['auth_otp_expiry_time']) >= $this->utc_time) {
                        if ($user_details['auth_attempt_count'] >= VERIFY_OTP_LIMIT) {
                            $this->my_response['status'] = false;
                            $this->my_response['user_phone_is_verified'] = '1';
                            $this->my_response['message'] = lang("otp_verfication_limit_reach");

                            $this->send_response();
                        }

                        if ($user_details['auth_code'] == $otp) {
                            $this->my_response['status'] = true;
                            if (!empty($is_login) && $is_login == '1') {
                                $user_data = $this->User_model->get_details_by_id($user_details['user_id']);

                                $auth_update_data = array(
                                    'auth_code' => NULL,
                                    'auth_otp_expiry_time' => NULL,
                                    "auth_attempt_count" => 0,
                                    "auth_resend_count" => 0,
                                    "auth_resend_timestamp" => date("Y-m-d")
                                );
                                $auth_update_where = array(
                                    'auth_user_id' => $user_details['user_id'],
                                    'auth_type' => 2
                                );
                                $this->User_model->update_auth_details($auth_update_data, $auth_update_where);
                                $access_token = $this->Common_model->escape_data(str_rand_access_token(64));
                                $token = $this->add_device_token($user_details['user_id'], $access_token, $device_token, $device_type);

                                if ($user_type == 1) {
                                    //get user other detail
                                    $get_user_other_detail = $this->Common_model->get_single_row(TBL_USER_DETAILS, 'user_details_agree_terms', array('user_details_user_id' => $user_details['user_id']));
                                    $user_data['user_details_agree_terms'] = 1;
                                    if (!empty($get_user_other_detail)) {
                                        $user_data['user_details_agree_terms'] = $get_user_other_detail['user_details_agree_terms'];
                                    }
                                } else {
                                    $user_data['user_type'] = '';
                                    $user_data['clinic_doctor_id'] = '';
                                    $doctor_clinic_mapping = $this->get_user_role($user_details['user_id']);
                                    if ($doctor_clinic_mapping) {
                                        $user_data['user_type'] = $doctor_clinic_mapping['doctor_clinic_mapping_role_id'];
                                        $user_data['clinic_doctor_id'] = $doctor_clinic_mapping['doctor_clinic_mapping_doctor_id'];
                                    }
                                    $userDetail = $this->Common_model->get_single_row(TBL_DOCTOR_DETAILS, '*', array(
                                        'doctor_detail_doctor_id' => $user_details['user_id'],
                                    ));
                                    $user_data['doctor_detail_is_term_accepted'] = 0;
                                    if ($userDetail) {
                                        $user_data['doctor_detail_is_term_accepted'] = $userDetail['doctor_detail_is_term_accepted'];
                                    }
                                }
                                //Create audit log
                                $this->Auditlog_model->create_audit_log($user_data['user_id'], $user_data['user_type_id'], AUDIT_SLUG_ARR['LOGIN_ACTION']);
                                $user_data['user_photo_filepath'] = get_image_thumb($user_data['user_photo_filepath']);
                                $this->my_response['user_data'] = $user_data;
                                $this->my_response['access_token'] = $access_token;
                                $user_update_data = array('last_login_at' => $this->utc_time_formated);
                                $user_update_where = array('user_id' => $user_details['user_id']);
                                $this->User_model->update_user_data($user_update_data, $user_update_where);
                                $this->my_response['message'] = lang("login_with_phone_number_success");
                            } else {
                                $this->my_response['message'] = lang("user_register_phone_number_already_verfied");
                            }
                        } else {
                            $remain_otp_verify_limit = VERIFY_OTP_LIMIT - ($user_details['auth_attempt_count'] + 1);
                            $this->User_model->increase_opt_auth_limit(1, $user_details['auth_id'], ($user_details['auth_attempt_count'] + 1));
                            $this->my_response['status'] = false;
                            $this->my_response['user_phone_is_verified'] = $user_details['user_phone_verified'];
                            $this->my_response['message'] = sprintf(lang("invalid_otp_for_login"), $remain_otp_verify_limit);
                        }
                    } else {
                        $this->my_response['status'] = false;
                        $this->my_response['user_phone_is_verified'] = $user_details['user_phone_verified'];
                        $this->my_response['message'] = lang("otp_token_expire");
                        $this->my_response['is_expire'] = "1";
                    }
                } else if (isset($user_details['user_phone_verified']) && $user_details['user_phone_verified'] == 2) {

                    if (strtotime($user_details['auth_otp_expiry_time']) >= $this->utc_time) {

                        if ($user_details['auth_code'] == $otp) {

                            $auth_update_data = array(
                                'auth_code' => NULL,
                                'auth_otp_expiry_time' => NULL,
                                "auth_attempt_count" => 0,
                                "auth_resend_count" => 0,
                                "auth_resend_timestamp" => date("Y-m-d")
                            );
                            $auth_update_where = array(
                                'auth_user_id' => $user_details['user_id'],
                                'auth_type' => 2
                            );
                            $this->User_model->update_auth_details($auth_update_data, $auth_update_where);

                            $user_update_data = array(
                                'user_phone_verified' => 1
                            );
                            $this->User_model->update_profile($user_details['user_id'], $user_update_data);

                            $user_data = $this->User_model->get_details_by_id($user_details['user_id']);
                            $access_token = $this->Common_model->escape_data(str_rand_access_token(64));
                            $token = $this->add_device_token($user_details['user_id'], $access_token, $device_token, $device_type);

                            $this->my_response['status'] = true;
                            $this->my_response['user_data'] = $user_data;
                            $this->my_response['access_token'] = $access_token;
                            $this->my_response['message'] = lang("otp_verfication_success");
                        } else {
                            $remain_otp_verify_limit = VERIFY_OTP_LIMIT - ($user_details['auth_attempt_count'] + 1);
                            $this->User_model->increase_opt_auth_limit(1, $user_details['auth_id'], ($user_details['auth_attempt_count'] + 1));
                            $this->my_response['status'] = false;
                            $this->my_response['user_phone_is_verified'] = '2';
                            $this->my_response['message'] = sprintf(lang("invalid_otp_for_login"), $remain_otp_verify_limit);
                        }
                    } else {
                        $this->my_response['status'] = false;
                        $this->my_response['user_phone_is_verified'] = '2';
                        $this->my_response['message'] = lang("otp_token_expire");
                        $this->my_response['is_expire'] = "1";
                    }
                } else {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("mycontroller_problem_request");
                }
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("user_not_found");
            }
            $this->send_response();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->store_error();
        }
    }

    /**
     * 
     * This function is needed for sendSMS to the given number 
     * 
     * @author 
     * 
     * Modified Date :- 2018-01-22
     * 
     */
    public function send_sms($sms_data) {

        require_once APPPATH . "/third_party/twilio/Services/Twilio.php";

        try {
            $AccountSid = TWILIO_ACC_SID;
            $AuthToken = TWILIO_AUTH_TOKEN;
            $client = new Services_Twilio($AccountSid, $AuthToken);
            $sms = $client->account->messages->sendMessage(
                    TWILIO_REG_NUMBER, "+" . $sms_data['phone_number'], $sms_data['message']
            );
            if ($sms) {
                return 1;
            } else {
                return 0;
            }
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * 
     * this function to resend otp
     * 
     * @author 
     * 
     * Modified Date :- 2018-01-22
     * 
     */
    public function resend_otp_for_clinic_post() {

        $country_code = !empty($this->post_data['country_code']) ? $this->post_data['country_code'] : "";
        $phone_number = !empty($this->post_data['phone_number']) ? $this->post_data['phone_number'] : "";
        $clinic_id = !empty($this->post_data['clinic_id']) ? $this->post_data['clinic_id'] : "";

        try {
            if (
                    empty($country_code) ||
                    empty($phone_number) ||
                    empty($clinic_id)
            ) {
                $this->bad_request();
                exit;
            }

            $get_clinic_details = $this->Common_model->get_single_row(TBL_CLINICS, 'clinic_id', array(
                'clinic_id' => $clinic_id,
                'clinic_contact_number' => $phone_number
            ));

            if (!empty($get_clinic_details) && count($get_clinic_details) > 0) {

                $otp = getUniqueToken(6, 'numeric');
                $message = sprintf(OTP_MESSAGE, $otp);
                $send_otp = array(
                    'phone_number' => $country_code . $phone_number,
                    'message' => $message,
                );
                $sening_sms = send_message_by_vibgyortel($send_otp);

                // $sening_sms = TRUE;

                if ($sening_sms) {

                    //check entry is in auth table or not
                    $get_auth_details = $this->Common_model->get_single_row(TBL_USER_AUTH, 'auth_id', array(
                        'auth_is_clinic' => 1,
                        'auth_type' => 2,
                        'auth_user_id' => $clinic_id
                    ));

                    if (!empty($get_auth_details['auth_id'])) {

                        $auth_update_data = array(
                            'auth_code' => $otp,
                            'auth_otp_expiry_time' => date('Y-m-d H:i:s', strtotime(OTP_EXPIRE_TOKEN_TIME, strtotime($this->utc_time_formated))),
                            'auth_phone_number' => $phone_number
                        );

                        $auth_update_where = array(
                            'auth_user_id' => $clinic_id,
                            'auth_type' => 2,
                            'auth_is_clinic' => 1
                        );
                        $this->User_model->update_auth_details($auth_update_data, $auth_update_where);
                    } else {

                        $insert_clinic_auth = array(
                            'auth_user_id' => $clinic_id,
                            'auth_type' => 2,
                            'auth_code' => $otp,
                            'auth_otp_expiry_time' => date('Y-m-d H:i:s', strtotime(OTP_EXPIRE_TOKEN_TIME, strtotime($this->utc_time_formated))),
                            'auth_created_at' => $this->utc_time_formated,
                            'auth_is_clinic' => 1,
                            'auth_phone_number' => $phone_number
                        );
                        $this->Common_model->insert(TBL_USER_AUTH, $insert_clinic_auth);
                    }
                    $this->my_response['status'] = true;
                    $this->my_response['message'] = lang("otp_sent_to_mobile");
                    $this->send_response();
                } else {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("sms_otp_send_fail");
                    $this->send_response();
                }
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mobile_number_not_register_with_us");
                $this->send_response();
            }
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->store_error();
        }
    }

    /**
     * 
     * this function to resend otp
     * 
     * @author 
     * 
     * Modified Date :- 2018-01-22
     * 
     */
    public function resend_otp_post() {

        $country_code = !empty($this->post_data['country_code']) ? $this->post_data['country_code'] : "";
        $phone_number = !empty($this->post_data['phone_number']) ? $this->post_data['phone_number'] : "";
        $user_type = !empty($this->post_data['user_type']) ? $this->post_data['user_type'] : "";
        $is_number_updated = !empty($this->post_data['is_number_updated']) ? $this->post_data['is_number_updated'] : 2;
        $other_user_id = !empty($this->post_data['other_user_id']) ? $this->post_data['other_user_id'] : "";

        try {
            if (
                    empty($country_code) ||
                    empty($phone_number)
            ) {
                $this->bad_request();
                exit;
            }

            $user_details = $this->User_model->get_otp_details_by_phone_number($phone_number, $user_type);
            if (
                    (!empty($user_details) && count($user_details) > 0) ||
                    $is_number_updated == 1
            ) {

                if ($is_number_updated == 1) {
                    $auth_update_where = array(
                        'auth_user_id' => $other_user_id,
                        "auth_type" => 2
                    );
                } else {
                    $auth_update_where = array(
                        'auth_user_id' => $user_details['user_id'],
                        "auth_type" => 2
                    );
                }

                $auth_resend_data = $this->User_model->get_single_row(TBL_USER_AUTH, "auth_resend_count,auth_resend_timestamp", $auth_update_where);
                if (!empty($auth_resend_data)) {
                    if (!empty($auth_resend_data['auth_resend_timestamp']) && (strtotime($auth_resend_data['auth_resend_timestamp']) + RESEND_OTP_NEXT_TIME) >= time()) {
                        $wating_time = (strtotime($auth_resend_data['auth_resend_timestamp']) + RESEND_OTP_NEXT_TIME) - time();
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = sprintf(lang("otp_resend_time_limit"), $wating_time);
                        $this->send_response();
                    }

                    if ($auth_resend_data['auth_resend_count'] >= RESEND_OTP_LIMIT) {
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang("otp_resend_limit_reach");
                        $this->send_response();
                    }
                }
				
                $otp = getUniqueToken(6, 'numeric');
                $message = sprintf(OTP_MESSAGE, $otp);
                $send_otp = array(
                    'phone_number' => $country_code . $phone_number,
                    'message' => $message,
                );
                $sening_sms = send_message_by_vibgyortel($send_otp);
                //$sening_sms = TRUE;

                if ($sening_sms) {

                    $auth_update_data = array(
                        'auth_code' => $otp,
                        'auth_otp_expiry_time' => date('Y-m-d H:i:s', strtotime(OTP_EXPIRE_TOKEN_TIME, strtotime($this->utc_time_formated))),
                        "auth_type" => 2,
                        "auth_attempt_count" => 0,
                        "auth_resend_timestamp" => date("Y-m-d H:i:s")
                    );
                    if ($is_number_updated == 1) {
                        $auth_update_where = array(
                            'auth_user_id' => $other_user_id,
                            "auth_type" => 2
                        );
                    } else {
                        $auth_update_where = array(
                            'auth_user_id' => $user_details['user_id'],
                            "auth_type" => 2
                        );
                    }

                    if (!empty($auth_resend_data)) {
                        $auth_update_data['auth_resend_count'] = $auth_resend_data['auth_resend_count'] + 1;
                    }
                    $this->User_model->update_auth_details($auth_update_data, $auth_update_where);
                    $this->my_response['status'] = true;
                    $this->my_response['message'] = lang("otp_sent_to_mobile");
                    $this->my_reponse['otp'] = $otp;
                    $this->send_response();
                } else {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("sms_otp_send_fail");
                    $this->send_response();
                }
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mobile_number_not_register_with_us");
                $this->send_response();
            }
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->store_error();
        }
    }

    /**
     * 
     * this function to send the email verify link
     * 
     * @author 
     * 
     * Modified Date :- 2018-01-22
     * 
     */
    public function resend_email_verify_link_post() {

        $email = !empty($this->post_data['email']) ? trim($this->post_data['email']) : "";
        $user_type = !empty($this->post_data['user_type']) ? $this->post_data['user_type'] : "";
        $is_email_updated = !empty($this->post_data['is_email_updated']) ? $this->post_data['is_email_updated'] : 2;
        $other_user_id = !empty($this->post_data['other_user_id']) ? $this->post_data['other_user_id'] : "";

        try {
            if (
                    empty($email)
            ) {
                $this->bad_request();
                exit;
            }

            if (validate_email($email)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("invalid_email");
                $this->send_response();
            }

            $user_details = $this->User_model->get_verification_details_by_email($email, $user_type);

            if (
                    (!empty($user_details) && count($user_details) > 0) ||
                    $is_email_updated == 1
            ) {

                if ($is_email_updated == 2) {
                    if ($user_details['user_email_verified'] == 1) {
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang("email_already_verified");
                        $this->send_response();
                    }
                }


                $reset_token = str_rand_access_token(20);

                $user_auth_email_update_array = array(
                    'auth_code' => $reset_token,
                    'auth_otp_expiry_time' => date('Y-m-d H:i:s', strtotime(OTP_EXPIRE_TOKEN_TIME, strtotime($this->utc_time_formated))),
                    'auth_created_at' => $this->utc_time_formated,
                    'auth_phone_number' => $email
                );

                $user_id = '';

                if ($is_email_updated == 1) {
                    $user_id = $other_user_id;
                } else {
                    $user_id = $user_details['user_id'];
                }

                $auth_where = array(
                    'auth_user_id' => $user_id,
                    'auth_type' => 1
                );
                $is_update = $this->User_model->update_auth_details($user_auth_email_update_array, $auth_where);

                if ($is_update > 0) {

                    $verify_link = DOMAIN_URL . "verifyaccount/" . $reset_token;

                    //this is use for get view and store data in variable
                    //EMAIL TEMPLATE START BY PRAGNESH
                    $this->load->model('Emailsetting_model');
                    $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(12);

                    $parse_arr = array(
                        '{UserName}' => 'User',
                        '{VerificationLink}' => $verify_link,
                        '{WebUrl}' => DOMAIN_URL,
                        '{AppName}' => APP_NAME,
                        '{MailContactNumber}' => $email_template_data['email_static_data']['contact_number'],
                        '{MailEmailAddress}' => $email_template_data['email_static_data']['email_id'],
                        '{MailCompanyName}' => $email_template_data['email_static_data']['company_name'],
                        '{CopyRightsYear}' => date('Y')
                    );
                    $message = replace_values_in_string($parse_arr, $email_template_data['email_template_message']);
                    $subject = $email_template_data['email_template_subject'];
                    //EMAIL TEMPLATE END BY PRAGNESH
                    //this function help you to send mail to single ot multiple users
                    $this->send_email(array($email => $email), $subject, $message);

                    $user_data = $this->User_model->get_details_by_id($user_id);
                    unset($user_data['user_password']);

                    $this->my_response['status'] = true;
                    $this->my_response['message'] = lang("verification_link");
                    $this->my_response['user_data'] = $user_data;
                    $this->send_response();
                } else {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("failure");
                    $this->send_response();
                }
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("user_forgotpassword_fail");
                $this->send_response();
            }
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->store_error();
        }
    }

    /**
     * 
     * this function to send the email verify link for clinic
     * 
     * @author 
     * 
     * Modified Date :- 2018-01-22
     * 
     */
    public function resend_email_link_for_clinic_post() {

        $email = !empty($this->post_data['email']) ? $this->post_data['email'] : "";
        $clinic_id = !empty($this->post_data['clinic_id']) ? $this->post_data['clinic_id'] : "";

        try {
            if (
                    empty($email) ||
                    empty($clinic_id)
            ) {
                $this->bad_request();
                exit;
            }

            $get_clinic_details = $this->Common_model->get_single_row(TBL_CLINICS, 'clinic_id, clinic_name', array(
                'clinic_id' => $clinic_id,
                'clinic_email' => $email
            ));

            if (!empty($get_clinic_details) && count($get_clinic_details) > 0) {

                $reset_token = str_rand_access_token(20);

                //check entry is in auth table or not
                $get_auth_details = $this->Common_model->get_single_row(TBL_USER_AUTH, 'auth_id', array(
                    'auth_is_clinic' => 1,
                    'auth_type' => 1,
                    'auth_user_id' => $clinic_id
                ));

                if (!empty($get_auth_details['auth_id'])) {

                    $auth_update_data = array(
                        'auth_code' => $reset_token,
                        'auth_otp_expiry_time' => date('Y-m-d H:i:s', strtotime(OTP_EXPIRE_TOKEN_TIME, strtotime($this->utc_time_formated))),
                        'auth_phone_number' => $email
                    );

                    $auth_update_where = array(
                        'auth_user_id' => $clinic_id,
                        'auth_type' => 1,
                        'auth_is_clinic' => 1
                    );
                    $is_update = $this->User_model->update_auth_details($auth_update_data, $auth_update_where);
                } else {

                    $insert_clinic_auth = array(
                        'auth_user_id' => $clinic_id,
                        'auth_type' => 1,
                        'auth_code' => $reset_token,
                        'auth_otp_expiry_time' => date('Y-m-d H:i:s', strtotime(OTP_EXPIRE_TOKEN_TIME, strtotime($this->utc_time_formated))),
                        'auth_created_at' => $this->utc_time_formated,
                        'auth_is_clinic' => 1,
                        'auth_phone_number' => $email
                    );
                    $is_update = $this->Common_model->insert(TBL_USER_AUTH, $insert_clinic_auth);
                }

                if ($is_update > 0) {

                    $verify_link = DOMAIN_URL . "verifyaccount/" . $reset_token;

                    if (!empty($get_clinic_details['clinic_name'])) {
                        $user_name = $get_clinic_details['clinic_name'];
                    } else {
                        $user_name = "User";
                    }

                    //this is use for get view and store data in variable
                    //EMAIL TEMPLATE START BY PRAGNESH
                    $this->load->model('Emailsetting_model');
                    $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(12);
                    $parse_arr = array(
                        '{UserName}' => $user_name,
                        '{VerificationLink}' => $verify_link,
                        '{WebUrl}' => DOMAIN_URL,
                        '{AppName}' => APP_NAME,
                        '{MailContactNumber}' => $email_template_data['email_static_data']['contact_number'],
                        '{MailEmailAddress}' => $email_template_data['email_static_data']['email_id'],
                        '{MailCompanyName}' => $email_template_data['email_static_data']['company_name'],
                        '{CopyRightsYear}' => date('Y')
                    );
                    $message = replace_values_in_string($parse_arr, $email_template_data['email_template_message']);
                    $subject = $email_template_data['email_template_subject'];
                    //EMAIL TEMPLATE END BY PRAGNESH
                    //this function help you to send mail to single ot multiple users
                    $this->send_email(array($email => $email), $subject, $message);

                    $this->my_response['status'] = true;
                    $this->my_response['message'] = lang("verification_link");
                    $this->send_response();
                } else {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("failure");
                    $this->send_response();
                }
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("user_forgotpassword_fail");
                $this->send_response();
            }
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->store_error();
        }
    }

    /**
     * 
     * This function use for delete accesstoken from database and lgout user from application
     * 
     * @author 
     * 
     * Modified Date :- 2018-01-22
     * 
     */
    public function logout_post() {
        try {
            $access_token = !empty($this->post_data['access_token']) ? $this->post_data['access_token'] : "";
            //set Null to security token
            $where = array(
                "udt_u_id" => $this->user_id,
                "udt_security_token" => $access_token,
            );
            //$delete = $this->Common_model->delete_data(TBL_USER_DEVICE_TOKENS, $where);

            if ($this->user_id > 0) {
                $get_user_details = $this->User_model->get_details_by_id($this->user_id);
                //Create audit log
                $this->Auditlog_model->create_audit_log($get_user_details['user_id'], $get_user_details['user_type_id'], AUDIT_SLUG_ARR['LOGOUT_ACTION']);
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang("user_logout_success");
            } else {
                $this->my_response['status'] = FALSE;
                $this->my_response['message'] = lang("user_logout_fail");
            }
            $this->send_response();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->store_error();
        }
    }

    /**
     * 
     * This function use for update device token for send push notification
     * because if user not allow permission for push then device token come black
     * at time of login register
     * 
     * @author 
     * 
     * Modified Date :- 2018-01-22
     * 
     */
    public function update_device_token_post() {
        $access_token = !empty($this->post_data['access_token']) ? $this->Common_model->escape_data($this->post_data['access_token']) : "";
        $device_token = !empty($this->post_data['device_token']) ? $this->Common_model->escape_data($this->post_data['device_token']) : "";
        $device_type = !empty($this->post_data['device_type']) ? $this->Common_model->escape_data($this->post_data['device_type']) : "";

        try {
            if (empty($device_token)) {
                $this->bad_request();
            }

            $update_data = array();
            $update_data['udt_device_token'] = $device_token;
            $update_data['udt_device_type'] = $device_type;
            $update_data['udt_created_date'] = time();

            $where = array();
            $where['udt_u_id'] = $this->user_id;
            $where['udt_security_token'] = $access_token;

            $update = $this->Common_model->update(TBL_USER_DEVICE_TOKENS, $update_data, $where);
            if ($update > 0) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang("user_updatedevicetoken_success");
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("user_updatedevicetoken_fail");
            }
            $this->send_response();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->store_error();
        }
    }

    /**
     * 
     * This function use for send reset password mail to user
     * 
     * @author 
     * 
     * Modified Date :- 2018-01-22
     * 
     */
    public function forgot_password_post() {
        $email = !empty($this->post_data['email']) ? $this->Common_model->escape_data($this->post_data['email']) : "";
        $user_type = !empty($this->post_data['user_type']) ? $this->Common_model->escape_data($this->post_data['user_type']) : "";

        try {
            $user_data = $this->Common_model->get_single_row(TBL_USERS, "user_id,user_first_name,user_last_name", array(
                "user_email" => $email,
                "user_status" => "1",
                "user_type" => $user_type
                    )
            );

            if (!empty($user_data)) {
                $reset_token = str_rand_access_token(20);

                $user_auth_insert_array = array(
                    'auth_user_id' => $user_data['user_id'],
                    'auth_type' => 3,
                    'auth_code' => $reset_token,
                    'auth_otp_expiry_time' => date('Y-m-d H:i:s', strtotime(OTP_EXPIRE_TOKEN_TIME, strtotime($this->utc_time_formated))),
                    'auth_created_at' => $this->utc_time_formated
                );
                $this->User_model->store_auth_detail($user_auth_insert_array);

                $reset_password_url = DOMAIN_URL . "resetpassword/" . $reset_token;

                if (!empty($user_data['user_first_name'])) {
                    $first_name = $user_data['user_first_name'] . " " . $user_data['user_last_name'];
                } else {
                    $first_name = "User";
                }

                //this is use for get view and store data in variable
                //EMAIL TEMPLATE START BY PRAGNESH
                $this->load->model('Emailsetting_model');
                $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(4);
                $parse_arr = array(
                    '{UserName}' => $first_name,
                    '{ForgotUrl}' => $reset_password_url,
                    '{WebUrl}' => DOMAIN_URL,
                    '{AppName}' => APP_NAME,
                    '{MailContactNumber}' => $email_template_data['email_static_data']['contact_number'],
                    '{MailEmailAddress}' => $email_template_data['email_static_data']['email_id'],
                    '{MailCompanyName}' => $email_template_data['email_static_data']['company_name'],
                    '{CopyRightsYear}' => date('Y')
                );
                $message = replace_values_in_string($parse_arr, $email_template_data['email_template_message']);
                $subject = $email_template_data['email_template_subject'];
                //EMAIL TEMPLATE END BY PRAGNESH
                //this function help you to send mail to single ot multiple users
                $this->send_email(array($email => $email), $subject, $message);

                $this->my_response['status'] = true;
                $this->my_response['message'] = lang("user_forgotpassword_success");
            } else {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang("user_forgotpassword_success");
            }

            $this->send_response();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to update the caregiver number 
     * of the user and send sms to the caregiver
     * 
     * @author Manish Ramnani
     * 
     */
    public function update_caregiver_post() {

        try {
            $caregiver_number = !empty($this->post_data['caregiver_number']) ? trim($this->Common_model->escape_data($this->post_data['caregiver_number'])) : "";
            $other_user_id = !empty($this->post_data['other_user_id']) ? trim($this->Common_model->escape_data($this->post_data['other_user_id'])) : "";

            if (empty($caregiver_number) ||
                    empty($other_user_id)
            ) {
                $this->bad_request();
            }

            if (
                    (!empty($caregiver_number) && validate_phone_number($caregiver_number))
            ) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("invalid_phone_number");
                $this->send_response();
            }

            $user_details = $this->User_model->get_details_by_id($other_user_id);
            if (!empty($user_details['user_first_name'])) {
                $user_name = ucfirst($user_details['user_first_name']) . ' ' . ucfirst($user_details['user_last_name']);
            } else {
                $user_name = "Somebody";
            }
			$user_caregiver_name = '';
            //getting the caregiver details
            if (!empty($caregiver_number)) {
                $get_user_detail = $this->User_model->check_user_number_exists($caregiver_number, 1);
                if (!empty($get_user_detail) && count($get_user_detail) > 0) {
                    $update_data['user_caregiver_id'] = $get_user_detail['user_id'];
					$user_caregiver_name = ucfirst($get_user_detail['user_first_name']) . ' ' . ucfirst($get_user_detail['user_last_name']);
                }
				$message = sprintf(lang('user_caregiver_message'), $user_caregiver_name, $user_name, $user_name);
				$send_sms_caregiver = array(
					'phone_number' => DEFAULT_COUNTRY_CODE . $caregiver_number,
					'message' => $message,
				);
				send_message_by_vibgyortel($send_sms_caregiver);
            } else {
                $update_data['user_caregiver_id'] = '';
            }

            $update_data['user_modified_at'] = $this->utc_time_formated;

            $user_is_updated = $this->User_model->update_profile($other_user_id, $update_data);
            $user_data = array(
                'user_id' => $other_user_id
            );
            if ($user_is_updated > 0) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang("user_profile_update_success");
                $this->my_response['data'] = $user_data;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("failure");
            }
            $this->send_response();
        } catch (ErrorException $e) {
            $this->error = $e->getMessage();
            $this->store_error();
        }
    }

    /**
     * 
     * This function is for update profile.
     * 
     * @author 
     * 
     * Modified Date :- 2018-01-22
     * 
     */
    public function update_profile_post() {

        $user_id = !empty($this->post_data['user_id']) ? trim($this->Common_model->escape_data($this->post_data['user_id'])) : "";
        $first_name = !empty($this->post_data['first_name']) ? trim($this->Common_model->escape_data($this->post_data['first_name'])) : "";
        $last_name = !empty($this->post_data['last_name']) ? trim($this->Common_model->escape_data($this->post_data['last_name'])) : "";
        $email = !empty($this->post_data['email']) ? trim($this->Common_model->escape_data($this->post_data['email'])) : "";
        $phone_number = !empty($this->post_data['phone_number']) ? trim($this->Common_model->escape_data($this->post_data['phone_number'])) : "";
        $gender = !empty($this->post_data['gender']) ? trim($this->Common_model->escape_data($this->post_data['gender'])) : "";
        $language = !empty($this->post_data['language']) ? trim($this->Common_model->escape_data($this->post_data['language'])) : "";
        $caregiver_number = !empty($this->post_data['caregiver_number']) ? trim($this->Common_model->escape_data($this->post_data['caregiver_number'])) : "";
        $user_type = !empty($this->post_data['user_type']) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : "";

        $dob = !empty($this->post_data['dob']) ? trim(($this->post_data['dob'])) : "";
        $weight = !empty($this->post_data['weight']) ? trim(($this->post_data['weight'])) : "";
        $height = !empty($this->post_data['height']) ? trim(($this->post_data['height'])) : "";
        $blood_group = !empty($this->post_data['blood_group']) ? trim(($this->post_data['blood_group'])) : "";
        $food_allergies = !empty($this->post_data['food_allergies']) ? trim(($this->post_data['food_allergies'])) : "";
        $medicine_allergies = !empty($this->post_data['medicine_allergies']) ? trim(($this->post_data['medicine_allergies'])) : "";
        $other_allergies = !empty($this->post_data['other_allergies']) ? trim(($this->post_data['other_allergies'])) : "";
        $chronic_diseases = !empty($this->post_data['chronic_diseases']) ? trim(($this->post_data['chronic_diseases'])) : "";
        $injuries = !empty($this->post_data['injuries']) ? trim(($this->post_data['injuries'])) : "";
        $surgeries = !empty($this->post_data['surgeries']) ? trim(($this->post_data['surgeries'])) : "";
        $marital_status = !empty($this->post_data['marital_status']) ? trim($this->Common_model->escape_data($this->post_data['marital_status'])) : "";
        $emergency_contact_person = !empty($this->post_data['emergency_contact_person']) ? trim($this->Common_model->escape_data($this->post_data['emergency_contact_person'])) : "";
        $emergency_contact_number = !empty($this->post_data['emergency_contact_number']) ? trim($this->Common_model->escape_data($this->post_data['emergency_contact_number'])) : "";

        $smoking_habbit = !empty($this->post_data['smoking_habbit']) ? trim($this->Common_model->escape_data($this->post_data['smoking_habbit'])) : "";
        $alcohol_habbit = !empty($this->post_data['alcohol_habbit']) ? trim($this->Common_model->escape_data($this->post_data['alcohol_habbit'])) : "";
        $activity_level = !empty($this->post_data['activity_level']) ? trim($this->Common_model->escape_data($this->post_data['activity_level'])) : "";
        $activity_days = !empty($this->post_data['activity_days']) ? trim($this->Common_model->escape_data($this->post_data['activity_days'])) : "";
        $activity_hours = !empty($this->post_data['activity_hours']) ? trim($this->Common_model->escape_data($this->post_data['activity_hours'])) : "";
        $food_preference = !empty($this->post_data['food_preference']) ? trim(($this->post_data['food_preference'])) : "";
        $occupation = !empty($this->post_data['occupation']) ? trim(($this->post_data['occupation'])) : "";

        $address = !empty($this->post_data['address']) ? trim(($this->post_data['address'])) : "";
        $address1 = !empty($this->post_data['address1']) ? trim($this->Common_model->escape_data($this->post_data['address1'])) : "";
        $city_id = !empty($this->post_data['city_id']) ? trim($this->Common_model->escape_data($this->post_data['city_id'])) : "";
        $state_id = !empty($this->post_data['state_id']) ? trim($this->Common_model->escape_data($this->post_data['state_id'])) : "";
        $country_id = !empty($this->post_data['country_id']) ? trim($this->Common_model->escape_data($this->post_data['country_id'])) : "";
        $pincode = !empty($this->post_data['pincode']) ? trim(($this->post_data['pincode'])) : "";
        $latitude = !empty($this->post_data['latitude']) ? trim(($this->post_data['latitude'])) : "";
        $longitude = !empty($this->post_data['longitude']) ? trim(($this->post_data['longitude'])) : "";
        $other_user_id = !empty($this->post_data['other_user_id']) ? trim($this->Common_model->escape_data($this->post_data['other_user_id'])) : "";

        try {
            if (empty($user_id) ||
                    empty($user_type)
            ) {
                $this->bad_request();
                exit;
            }
            if (empty($other_user_id)) {
                $other_user_id = $user_id;
            }

            $check_user_exit = $this->User_model->get_user_exist($other_user_id);

            if (empty($check_user_exit)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("user_not_found");
                $this->send_response();
            }

            if (!empty($email) && validate_email($email)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("invalid_email");
                $this->send_response();
            }

            if (
                    (!empty($emergency_contact_number) && validate_phone_number($emergency_contact_number)) ||
                    (!empty($caregiver_number) && validate_phone_number($caregiver_number))
            ) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("invalid_phone_number");
                $this->send_response();
            }

            if (!empty($dob) && validate_dob($dob)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("invalid_dob");
                $this->send_response();
            }

            if (!empty($pincode) && validate_pincode($pincode)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("invalid_pincode");
                $this->send_response();
            }

            if (
                    (!empty($first_name) && validate_characters($first_name)) ||
                    (!empty($last_name) && validate_characters($last_name))
            ) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
                $this->send_response();
            }

            $user_type_array = array('1', '2');
            if (!in_array($user_type, $user_type_array)) {
                $this->bad_request();
            }

            //check email id exists or not
            if (!empty($email)) {
                $check_email_sql = "SELECT 
                                        user_email, user_email_verified
                                 FROM 
                                    " . TBL_USERS . " 
                                 WHERE 
                                      user_email = '" . $email . "' 
                                 AND         
                                      user_status != 9
                                 AND
                                      user_id != '" . $other_user_id . "'
                                 AND 
                                      user_type = '" . $user_type . "' ";

                $check_email = $this->Common_model->get_single_row_by_query($check_email_sql);

                if (!empty($check_email)) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("user_register_email_exist");
                    $this->send_response();
                }
            }

            //check phone number exists or not
            if (!empty($phone_number)) {
                $check_number_sql = "SELECT 
                                        user_phone_number
                                 FROM 
                                    " . TBL_USERS . " 
                                 WHERE 
                                      user_phone_number = '" . $phone_number . "' 
                                 AND         
                                      user_status != 9
                                 AND
                                      user_id != '" . $other_user_id . "'
                                 AND 
                                      user_type = '" . $user_type . "' ";

                $check_number = $this->Common_model->get_single_row_by_query($check_number_sql);

                if (!empty($check_number)) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("user_register_phone_number_exist");
                    $this->send_response();
                }
            }

            //get old email id and phonenumber
            $getting_old_details = $this->Common_model->get_single_row(TBL_USERS, 'user_email,user_phone_number', array('user_id' => $other_user_id));

            $update_data = array();

            if (!empty($first_name)) {
                $update_data['user_first_name'] = ucwords(strtolower($first_name));
            }

            if (!empty($last_name)) {
                $update_data['user_last_name'] = ucwords(strtolower($last_name));
            }


            if (!empty($language)) {
                $update_data['user_language_id'] = $language;
            }

            if (!empty($gender)) {
                $update_data['user_gender'] = $gender;
            }

            //getting the caregiver details
            if (empty($caregiver_number)) {
                $update_data['user_caregiver_id'] = '';
            }
            if (!empty($email) && empty($getting_old_details['user_email'])) {
                $update_data['user_email'] = $email;
            }
            $need_to_send_sms = 2;
            if (!empty($phone_number) && empty($getting_old_details['user_phone_number'])) {
                $need_to_send_sms = 1;
            }
            $update_data['user_modified_at'] = $this->utc_time_formated;
            $user_is_updated = $this->User_model->update_profile($other_user_id, $update_data);

            //update the addrress
            $update_address_data = array();
            if (!empty($address)) {
                $update_address_data['address_name'] = $address;
            }
//            if (!empty($address1)) {
            $update_address_data['address_name_one'] = $address1;
//            }
            if (!empty($city_id)) {
                $update_address_data['address_city_id'] = $city_id;
            }

            if (!empty($state_id)) {
                $update_address_data['address_state_id'] = $state_id;
            }

            if (!empty($country_id)) {
                $update_address_data['address_country_id'] = $country_id;
            }

            if (!empty($pincode)) {
                $update_address_data['address_pincode'] = $pincode;
            }

            if (!empty($latitude)) {
                $update_address_data['address_latitude'] = $latitude;
            }

            if (!empty($longitude)) {
                $update_address_data['address_longitude'] = $longitude;
            }

            $address_is_update = $this->User_model->update_address($other_user_id, $update_address_data);

            //update the user details
            $update_user_details = array();
            if (!empty($dob)) {
                $update_user_details['user_details_dob'] = $dob;
            }

            if (!empty($height)) {
                $update_user_details['user_details_height'] = $height;
            }

            if (!empty($weight)) {
                $update_user_details['user_details_weight'] = $weight;
            }

            if (!empty($blood_group)) {
                $update_user_details['user_details_blood_group'] = $blood_group;
            }


            $update_user_details['user_details_food_allergies'] = $food_allergies;
            $update_user_details['user_details_medicine_allergies'] = $medicine_allergies;
            $update_user_details['user_details_other_allergies'] = $other_allergies;
            $update_user_details['user_details_chronic_diseases'] = $chronic_diseases;
            $update_user_details['user_details_injuries'] = $injuries;
            $update_user_details['user_details_surgeries'] = $surgeries;


            if (!empty($smoking_habbit)) {
                $update_user_details['user_details_smoking_habbit'] = $smoking_habbit;
            }

            if (!empty($alcohol_habbit)) {
                $update_user_details['user_details_alcohol'] = $alcohol_habbit;
            }
            $update_user_details['user_details_activity_level'] = $activity_level;
            if (!empty($activity_days)) {
                $update_user_details['user_details_activity_days'] = $activity_days;
            }

            if (!empty($activity_days)) {
                $update_user_details['user_details_activity_hours'] = $activity_hours;
            }

            if (!empty($food_preference)) {
                $update_user_details['user_details_food_preference'] = $food_preference;
            }

            if (!empty($occupation)) {
                $update_user_details['user_details_occupation'] = $occupation;
            }

            if (!empty($marital_status)) {
                $update_user_details['user_details_marital_status'] = $marital_status;
            }
            $update_user_details['user_details_emergency_contact_person'] = $emergency_contact_person;
            $update_user_details['user_details_emergency_contact_number'] = $emergency_contact_number;
            $user_details = $this->User_model->get_user_details_by_id($other_user_id, '', 'user_details_emergency_contact_number');
            $user_details_is_update = $this->User_model->update_user_details($other_user_id, $update_user_details);
            $phone_number_updated = 2;
            $email_updated = 2;
            if ($user_is_updated > 0 || $address_is_update > 0 || $user_details_is_update > 0) {

                $result = $this->User_model->get_user_details_by_id($other_user_id);
                if (!empty($emergency_contact_number) && $user_details['user_details_emergency_contact_number'] != $emergency_contact_number) {
                    $user_name = '';
                    if (!empty($result['user_first_name'])) {
                        $user_name = ucfirst($result['user_first_name']) . ' ' . ucfirst($result['user_last_name']);
                    }
					$message = sprintf(lang('emergency_message'), $emergency_contact_person, $user_name);
                    $send_otp = array(
                        'phone_number' => DEFAULT_COUNTRY_CODE . $emergency_contact_number,
                        'message' => $message,
                        'patient_id' => $user_id
                    );
                    $sening_sms = send_message_by_vibgyortel($send_otp);
                }

                unset($result['user_password']);
                //if caregiver id is not empty then send the name of the caregiver name
                if (!empty($result['user_caregiver_id'])) {
                    $care_giver_id = $result['user_caregiver_id'];
                    $get_caregiver_details = $this->User_model->get_details_by_id($care_giver_id);
                    if (!empty($get_caregiver_details)) {
                        $result['user_care_giver_name'] = $get_caregiver_details['user_first_name'] . ' ' . $get_caregiver_details['user_last_name'];
                        $result['user_care_giver_email'] = $get_caregiver_details['user_email'];
                        $result['user_care_giver_number'] = $get_caregiver_details['user_phone_number'];
                    }
                }

                if (
                        (!empty($phone_number) && ($getting_old_details['user_phone_number'] != $phone_number)) ||
                        $need_to_send_sms == 1
                ) {

                    $otp = getUniqueToken(6, 'numeric');
                    //$otp = '123456';

                    $message = sprintf(OTP_MESSAGE, $otp);
                    $send_otp = array(
                        'phone_number' => DEFAULT_COUNTRY_CODE . $phone_number,
                        'message' => $message,
                        'patient_id' => $user_id
                    );
                    $sening_sms = send_message_by_vibgyortel($send_otp);

                    //$sening_sms = TRUE;
                    if ($sening_sms) {

                        $auth_update_data = array(
                            'auth_code' => $otp,
                            'auth_otp_expiry_time' => date('Y-m-d H:i:s', strtotime(OTP_EXPIRE_TOKEN_TIME, strtotime($this->utc_time_formated))),
                            'auth_phone_number' => $phone_number
                        );
                        $auth_update_where = array(
                            'auth_user_id' => $other_user_id,
                            'auth_type' => 2
                        );
                        $this->User_model->update_auth_details($auth_update_data, $auth_update_where);
                        $phone_number_updated = 1;
                    }
                }

                if (!empty($email) && (strtolower($getting_old_details['user_email']) != strtolower($email))) {
                    $reset_token = str_rand_access_token(20);

                    $user_auth_email_update_array = array(
                        'auth_code' => $reset_token,
                        'auth_otp_expiry_time' => date('Y-m-d H:i:s', strtotime(OTP_EXPIRE_TOKEN_TIME, strtotime($this->utc_time_formated))),
                        'auth_created_at' => $this->utc_time_formated,
                        'auth_phone_number' => $email
                    );

                    $auth_where = array(
                        'auth_user_id' => $other_user_id,
                        'auth_type' => 1
                    );
                    $is_update = $this->User_model->update_auth_details($user_auth_email_update_array, $auth_where);

                    if ($is_update > 0) {

                        $email_updated = 1;
                        $verify_link = DOMAIN_URL . "verifyaccount/" . $reset_token;

                        if (!empty($result['user_first_name'])) {
                            $user_name = $result['user_first_name'] . " " . $result['user_last_name'];
                        } else {
                            $user_name = "User";
                        }

                        //this is use for get view and store data in variable
                        //EMAIL TEMPLATE START BY PRAGNESH
                        $this->load->model('Emailsetting_model');
                        $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(12);
                        $parse_arr = array(
                            '{UserName}' => $user_name,
                            '{VerificationLink}' => $verify_link,
                            '{WebUrl}' => DOMAIN_URL,
                            '{AppName}' => APP_NAME,
                            '{MailContactNumber}' => $email_template_data['email_static_data']['contact_number'],
                            '{MailEmailAddress}' => $email_template_data['email_static_data']['email_id'],
                            '{MailCompanyName}' => $email_template_data['email_static_data']['company_name'],
                            '{CopyRightsYear}' => date('Y')
                        );
                        $message = replace_values_in_string($parse_arr, $email_template_data['email_template_message']);
                        $subject = $email_template_data['email_template_subject'];
                        //EMAIL TEMPLATE END BY PRAGNESH
                        //this function help you to send mail to single ot multiple users
                        $this->send_email(array($email => $email), $subject, $message);
                    }
                }

                if ($result['user_email_verified'] == 1) {
                    if (!empty($result['user_first_name'])) {
                        $user_name = $result['user_first_name'] . " " . $result['user_last_name'];
                    } else {
                        $user_name = "User";
                    }
                    $send_update_mail = array(
                        'user_name' => $user_name,
                        'user_email' => $result['user_email'],
                        'template_id' => 24
                    );
                    $cron_job_path = CRON_PATH . " notification/send_mail_background/" . base64_encode(json_encode($send_update_mail));
                    exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");
                }

                //send the flag wheter the phone number is update or not
                $result['phone_number_updated'] = $phone_number_updated;
                $result['email_updated'] = $email_updated;
                if ($email_updated == 2) {
                    $result['auth_phone_number'] = '';
                } else {
                    $result['auth_phone_number'] = $email;
                }

                $this->my_response['status'] = true;
                if ($email_updated == 1 && $phone_number_updated == 1) {
                    $this->my_response['message'] = lang("user_profile_update_success_phone_email");
                } else if ($email_updated == 1) {
                    $this->my_response['message'] = lang("user_profile_update_success_email");
                } else if ($phone_number_updated == 1) {
                    $this->my_response['message'] = lang("user_profile_update_success_phone");
                } else {
                    $this->my_response['message'] = lang("user_profile_update_success");
                }
                $this->my_response['user_data'] = $result;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("user_profile_update_fail");
            }
            $this->send_response();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->store_error();
        }
    }

    /**
     * 
     * This function is for update user profile image.
     * 
     * @author 
     * 
     * Modified Date :- 2018-01-22
     * 
     */
    public function upload_image_post() {
        try {
            $update_data = array();
            $other_user_id = !empty($this->post_data['other_user_id']) ? trim($this->Common_model->escape_data($this->post_data['other_user_id'])) : "";
            $user_id = $this->user_id;
            if (!empty($other_user_id)) {
                $user_id = $other_user_id;
            }

            if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] == 0) {

                $new_profile_img = '';
                $upload_path = UPLOAD_REL_PATH . "/" . USER_FOLDER . "/" . $user_id;
                $upload_folder = USER_FOLDER . "/" . $user_id;
                $profile_image_name = do_upload_multiple($upload_path, array('photo' => $_FILES['photo']), $upload_folder);

                $new_profile_img = $profile_image_name['photo'];

                if (!empty($new_profile_img)) {
                    $update_where = array(
                        'user_id' => $user_id
                    );
                    $update_data['user_photo'] = $new_profile_img;
                    $update_data['user_photo_filepath'] = IMAGE_MANIPULATION_URL . USER_FOLDER . "/" . $user_id . "/" . $new_profile_img;
                    $user_is_updated = $this->Common_model->update(TBL_USERS, $update_data, $update_where);

                    if ($user_is_updated) {
                        $get_user_details = $this->User_model->get_user_details_by_id($user_id);

                        //if caregiver id is not empty then send the name of the caregiver name
                        if (!empty($get_user_details['user_caregiver_id'])) {
                            $care_giver_id = $get_user_details['user_caregiver_id'];
                            $get_caregiver_details = $this->User_model->get_details_by_id($care_giver_id);
                            if (!empty($get_caregiver_details)) {
                                $get_user_details['user_care_giver_name'] = $get_caregiver_details['user_first_name'] . ' ' . $get_caregiver_details['user_last_name'];
                                $get_user_details['user_care_giver_email'] = $get_caregiver_details['user_email'];
                                $get_user_details['user_care_giver_number'] = $get_caregiver_details['user_phone_number'];
                            }
                        }

                        $this->my_response['status'] = true;
                        $this->my_response['message'] = lang("user_photo_upload_success");
                        $this->my_response['user_data'] = $get_user_details;
                    } else {
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang("user_photo_upload_fail");
                    }
                } else {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("user_photo_upload_fail");
                }
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("user_photo_upload_fail");
            }


            $this->send_response();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->store_error();
        }
    }

    /**
     * 
     * This function is for change user password
     * 
     * @author 
     * 
     * Modified Date :- 2018-01-22
     * 
     */
    public function change_password_post() {
        try {
            $current_password = !empty($this->post_data['current_password']) ? $this->Common_model->escape_data($this->post_data['current_password']) : "";
            $new_password = !empty($this->post_data['new_password']) ? $this->Common_model->escape_data($this->post_data['new_password']) : "";
            $confirm_password = !empty($this->post_data['confirm_password']) ? $this->Common_model->escape_data($this->post_data['confirm_password']) : "";

            if (empty($current_password) ||
                    empty($new_password) ||
                    empty($confirm_password)) {
                $this->bad_request();
            }

            if ($new_password != $confirm_password) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("user_confirm_password_notmatch");
                $this->send_response();
            }

            //get login user data
            $user_where = array(
                'user_id' => $this->user_id
            );
            $get_user_array = $this->Common_model->get_single_row(TBL_USERS, 'user_password', $user_where);

            //check current password id ok?
            if (password_verify($current_password, $get_user_array['user_password'])) {
                $processed_new_password = password_hash(trim($new_password), PASSWORD_BCRYPT);
                $update_data = array();
                $update_data['user_password'] = trim($processed_new_password);
                $update_data['user_modified_at'] = $this->utc_time_formated;
                $user_is_updated = $this->Common_model->update(TBL_USERS, $update_data, $user_where);
                if ($user_is_updated) {
                    $this->my_response['status'] = true;
                    $this->my_response['message'] = lang("user_password_update_success");
                } else {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("user_password_update_problem");
                }
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("user_password_enter_current_password");
            }
            $this->send_response();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->store_error();
        }
    }

    public function invite_user_post() {

        try {
            $phone_number = !empty($this->post_data['phone_number']) ? $this->Common_model->escape_data($this->post_data['phone_number']) : "";
            $other_user_id = !empty($this->post_data['other_user_id']) ? $this->Common_model->escape_data($this->post_data['other_user_id']) : "";
            if (empty($phone_number)) {
                $this->bad_request();
            }
			$user_caregiver_name = '';
            $user_details = $this->User_model->get_details_by_id($other_user_id);
            if (!empty($user_details['user_first_name'])) {
                $user_name = ucfirst($user_details['user_first_name']) . ' ' . ucfirst($user_details['user_last_name']);
            } else {
                $user_name = "Somebody";
            }
            $message = sprintf(lang('user_caregiver_message'), $user_caregiver_name, $user_name, $user_name);
            $send_sms_caregiver = array(
                'phone_number' => DEFAULT_COUNTRY_CODE . $phone_number,
                'message' => $message,
            );
            send_message_by_vibgyortel($send_sms_caregiver);
            $this->my_response['status'] = true;
            $this->my_response['message'] = "Invitation send succesfully";
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $e->getMessage();
            $this->store_error();
        }
    }

    /**
     * This function is used to get the detials of the user based on the id or number
     * 
     * @author Manish Ramnani
     * 
     * Modified Date :- 2018-02-28
     * 
     */
    public function get_user_details_post() {

        $user_id = !empty($this->post_data['user_id']) ? $this->Common_model->escape_data($this->post_data['user_id']) : "";
        $phone_number = !empty($this->post_data['phone_number']) ? $this->Common_model->escape_data($this->post_data['phone_number']) : "";
        $other_user_id = !empty($this->post_data['other_user_id']) ? $this->Common_model->escape_data($this->post_data['other_user_id']) : "";
        $user_type = !empty($this->post_data['user_type']) ? $this->Common_model->escape_data($this->post_data['user_type']) : 1;

        try {

            if (!empty($other_user_id)) {
                $user_id = $other_user_id;
            }

            if (!empty($phone_number)) {

                // for caregiver search only from patient side if not found then also send the sms
                $user_data = $this->User_model->check_user_number_exists($phone_number, 1);

                if (!empty($user_data)) {
                    $this->my_response['status'] = true;
                    $this->my_response['message'] = lang('user_detail_found');
                    $this->my_response['user_data'] = $user_data;
                } else {
                    /*
                      $user_details = $this->User_model->get_details_by_id($this->user_id);
                      if (!empty($user_details['user_first_name'])) {
                      $user_name = $user_details['user_first_name'] . ' ' . $user_details['user_last_name'];
                      } else {
                      $user_name = "Somebody";
                      }

                      $message = sprintf(lang('user_caregiver_message'), $user_name);
                      $send_sms_caregiver = array(
                      'phone_number' => DEFAULT_COUNTRY_CODE . $phone_number,
                      'message' => $message,
                      );

                      send_message_by_vibgyortel($send_sms_caregiver);
                     */
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('user_detail_not_found');
                }
            } else {

                $user_data = $this->User_model->get_user_details_by_id($user_id);

                if (!empty($user_data)) {
                    $get_vital_data = $this->Common_model->get_vital_data($user_id);

                    if (!empty($get_vital_data['vital_report_weight']) && strtotime($get_vital_data['vital_report_updated_at']) > strtotime($user_data['user_details_modifed_at'])) {
                        $user_data['user_details_weight'] = $get_vital_data['vital_report_weight'];
                    }

                    //if caregiver id is not empty then send the name of the caregiver name
                    if (!empty($user_data['user_caregiver_id'])) {
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
                    $this->my_response['status'] = true;
                    $this->my_response['message'] = lang('user_detail_found');
                    $this->my_response['user_data'] = $user_data;
                } else {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('user_detail_not_found');
                }
            }

            $this->send_response();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to add the family member
     * 
     * @author Manish Ramnani
     * 
     * Modified Date :- 2018-03-20
     */
    public function add_family_member_post() {

        $phone_number = !empty($this->post_data['phone_number']) ? $this->Common_model->escape_data($this->post_data['phone_number']) : "";
        $email = !empty($this->post_data['email']) ? $this->Common_model->escape_data($this->post_data['email']) : "";
        $first_name = !empty($this->post_data['first_name']) ? $this->Common_model->escape_data($this->post_data['first_name']) : "";
        $last_name = !empty($this->post_data['last_name']) ? $this->Common_model->escape_data($this->post_data['last_name']) : "";
        $relation = !empty($this->post_data['relation']) ? $this->Common_model->escape_data($this->post_data['relation']) : "";
        $caregiver_number = !empty($this->post_data['caregiver_number']) ? $this->Common_model->escape_data($this->post_data['caregiver_number']) : "";
        $user_type = 1;
        try {
            if (empty($first_name) ||
                    empty($last_name) ||
                    empty($relation)) {

                $this->bad_request();
                die;
            }

            if (!empty($email) && validate_email($email)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("invalid_email");
                $this->send_response();
            }

            if (
                    (!empty($phone_number) && validate_phone_number($phone_number)) ||
                    (!empty($caregiver_number) && validate_phone_number($caregiver_number))
            ) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("invalid_phone_number");
                $this->send_response();
            }

            if (
                    (!empty($first_name) && validate_characters($first_name)) ||
                    (!empty($last_name) && validate_characters($last_name))
            ) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
                $this->send_response();
            }

            $check_number = $this->User_model->check_user_number_exists($phone_number, $user_type);
            $check_email = $this->User_model->get_details_by_email($email, $user_type);
            $password = $this->Common_model->generate_random_string(4, 1, 1, 1);
            $unique_id = strtoupper($this->Common_model->escape_data(str_rand_access_token(8)));

            $insert_array = array(
                'user_first_name' => ucfirst($first_name),
                'user_last_name' => ucfirst($last_name),
                'user_email' => $email,
                'user_phone_number' => $phone_number,
                'user_parent_relation' => $relation,
                'user_parent_id' => $this->user_id,
                'user_created_at' => $this->utc_time_formated,
                'user_password' => password_hash($password, PASSWORD_BCRYPT),
                'user_unique_id' => $unique_id,
                'user_plan_id' => DEFAULT_USER_PLAN_ID
            );
            if((!empty($check_number) && count($check_number) > 0) || (!empty($check_email) && count($check_email) > 0)) {
                if(!empty($check_number['user_id'])) {
                    $member_user_data = $check_number;
                } elseif(!empty($check_email['user_id'])) {
                    $member_user_data = $check_email;
                }
                if(!empty($check_number['user_id'])) {
                    if($check_number['user_id'] == $this->user_id) {
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang('your_own_detail');
                        $this->send_response();
                    }
                }
                if(!empty($check_email['user_id'])) {
                    if($check_email['user_id'] == $this->user_id) {
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang('your_own_detail');
                        $this->send_response();
                    }
                }

                $where = array(
                    'parent_patient_id' => $this->user_id,
                    'patient_id' => $member_user_data['user_id'],
                    'mapping_status' => 1
                );
                $rows = $this->User_model->check_member_mapped($where);
                if ($rows > 0) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("already_mapped");
                    $this->send_response();
                } else {
                    $data['user_id'] = $this->user_id;
                    $data['phone_number']  = $phone_number;
                    $data['post_member_data'] = $insert_array;
                    $data['unique_id'] = $unique_id;
                    $data['member_user_data'] = $member_user_data;
                    $user_temp_id = $this->_add_member($data);
                    $user_data = array(
                        'other_user_id' => $user_temp_id,
                        'user_type' => 2
                    );
                    $this->my_response['status'] = true;
                    $this->my_response['is_show_otp_screen'] = 1; // 1=OTP screen, 
                    $this->my_response['message'] = lang('otp_sent_to_member');
                    $this->my_response['user_data'] = $user_data;
                    $this->send_response();
                }
                

            }

            if (!empty($check_number) && count($check_number) > 0) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("user_register_phone_number_exist");
                $this->send_response();
            }


            if (!empty($check_email) && count($check_email) > 0) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("user_register_email_exist");
                $this->send_response();
            }

            //getting the caregiver details
            if (!empty($caregiver_number)) {
                $get_user_detail = $this->User_model->check_user_number_exists($caregiver_number, $user_type);
				$care_giver_name = '';
                if (!empty($get_user_detail) && count($get_user_detail) > 0) {
                    $insert_array['user_caregiver_id'] = $get_user_detail['user_id'];
					$care_giver_name = ucfirst($get_user_detail['user_first_name']).' '.ucfirst($get_user_detail['user_last_name']);
                }
                $user_name = ucfirst($first_name).' '.ucfirst($last_name);
                $message = sprintf(lang('user_caregiver_message'), $care_giver_name, $user_name, $user_name);
                $send_sms_caregiver = array(
                    'phone_number' => DEFAULT_COUNTRY_CODE . $caregiver_number,
                    'message' => $message,
                    'patient_id' => $this->user_id
                );
                send_message_by_vibgyortel($send_sms_caregiver);
            }
            if(empty($phone_number)) {
                unset($insert_array['user_parent_id']);
                $user_id = $this->User_model->register_user($insert_array);
                $data_map = array(
                    'parent_patient_id' => $this->user_id,
                    'patient_id' => $user_id,
                    'mapping_status' => 1,
                    'created_at' => $this->utc_time_formated,
                    'created_by' => $this->user_id
                );
                $this->User_model->create_family_member_map($data_map);
            } else {
                $data['user_id'] = $this->user_id;
                $data['phone_number']  = $phone_number;
                $data['post_member_data'] = $insert_array;
                $data['unique_id'] = $unique_id;
                $data['member_user_data'] = $member_user_data;
                $user_id = $this->_add_member($data);
                $user_data = array(
                    'other_user_id' => $user_id,
                    'user_type' => 2
                );
            }

            if ($user_id > 0) {
                if(!empty($phone_number)) {
                    $this->my_response['is_show_otp_screen'] = 1; // 1=OTP screen,  
                    $this->my_response['message'] = lang('otp_sent_to_member');
                    $this->my_response['user_data'] = $user_data;
                } else {
                    //store the user authentication details
                    $user_auth_insert_array = array(
                        'auth_user_id' => $user_id,
                        'auth_type' => 2,
                        'auth_code' => '',
                        'auth_otp_expiry_time' => '',
                        'auth_created_at' => $this->utc_time_formated
                    );
                    $this->User_model->store_auth_detail($user_auth_insert_array);

                    //by default 2 way authentication on for the patient
                    $setting_array = array();
                    $setting_array[] = array(
                        'id' => "1",
                        'name' => "data security",
                        'status' => "2"
                    );
                    $insert_setting_array = array(
                        'setting_user_id' => $user_id,
                        'setting_clinic_id' => '',
                        'setting_data' => json_encode($setting_array),
                        'setting_type' => 2,
                        'setting_data_type' => 1,
                        'setting_created_at' => $this->utc_time_formated
                    );

                    $this->Common_model->insert(TBL_SETTING, $insert_setting_array);


                    if (!empty($email)) {

                        $reset_token = str_rand_access_token(20);

                        $user_auth_email_insert_array = array(
                            'auth_user_id' => $user_id,
                            'auth_type' => 1,
                            'auth_code' => $reset_token,
                            'auth_otp_expiry_time' => date('Y-m-d H:i:s', strtotime(OTP_EXPIRE_TOKEN_TIME, strtotime($this->utc_time_formated))),
                            'auth_created_at' => $this->utc_time_formated
                        );
                        $this->User_model->store_auth_detail($user_auth_email_insert_array);

                        $verify_link = DOMAIN_URL . "verifyaccount/" . $reset_token;

                        //this is use for get view and store data in variable
                        //EMAIL TEMPLATE START BY PRAGNESH
                        $this->load->model('Emailsetting_model');
                        $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(14);
                        $parse_arr = array(
                            '{Email_id}' => $email,
                            '{PatinetName}' => $insert_array['user_first_name'] . " " . $insert_array['user_last_name'],
                            '{Password}' => $password,
                            '{EmailPasswordImage}' => 1,
                            '{UniqueId}' => $unique_id,
                            '{VerificationLink}' => $verify_link,
                            '{WebUrl}' => DOMAIN_URL,
                            '{AppName}' => APP_NAME,
                            '{MailContactNumber}' => $email_template_data['email_static_data']['contact_number'],
                            '{MailEmailAddress}' => $email_template_data['email_static_data']['email_id'],
                            '{MailCompanyName}' => $email_template_data['email_static_data']['company_name'],
                            '{CopyRightsYear}' => date('Y')
                        );
                        $message = replace_values_in_string($parse_arr, $email_template_data['email_template_message']);
                        $subject = $email_template_data['email_template_subject'];
                        //EMAIL TEMPLATE END BY PRAGNESH
                        //this function help you to send mail to single ot multiple users
                        $this->send_email(array($email => $email), $subject, $message);
                    } else {
                        $user_auth_email_insert_array = array(
                            'auth_user_id' => $user_id,
                            'auth_type' => 1,
                            'auth_code' => NULL,
                            'auth_otp_expiry_time' => NULL,
                            'auth_created_at' => $this->utc_time_formated
                        );
                        $this->User_model->store_auth_detail($user_auth_email_insert_array);
                    }

                    if (!empty($phone_number)) {
    					$user_name = ucfirst($first_name).' '.ucfirst($last_name);
                        //get the logged in user detail
                        $parent_data = $this->User_model->get_details_by_id($this->user_id);
                        $parent_name = 'Someone';
                        if (!empty($parent_data['user_first_name'])) {
                            $parent_name = ucfirst($parent_data['user_first_name']).' '.ucfirst($parent_data['user_last_name']);
                        }
                        $message = sprintf(lang('added_family_member'), $user_name, $parent_name);
                        $send_sms_family_member = array(
                            'phone_number' => DEFAULT_COUNTRY_CODE . $phone_number,
                            'message' => $message,
                            'patient_id' => $this->user_id
                        );
                        send_message_by_vibgyortel($send_sms_family_member);
                    }

                    $user_data = $this->User_model->get_user_details_by_id($user_id);
                    $this->my_response['is_show_otp_screen'] = 0; // 0= Not OTP screen, 
                    $this->my_response['message'] = lang('member_add');
                    $this->my_response['user_data'] = $user_data;
                }
                $this->my_response['status'] = true;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    private function _add_member($data) {
        $post_member_data = $data['post_member_data'];
        $phone_number = $data['phone_number'];
        $unique_id = $data['unique_id'];
        $member_user_data = $data['member_user_data'];

        $otp = getUniqueToken(6, 'numeric');
        $patient_user = $this->User_model->get_user_by_id($data['user_id'], 'user_first_name, user_last_name');
        if(!empty($phone_number)) {
            $message = sprintf(lang('user_family_member_otp'), $post_member_data['user_first_name'] . " " . $post_member_data['user_last_name'], $patient_user['user_first_name'] . " " . $patient_user['user_last_name'], $otp);
            $send_otp = array(
                'phone_number' => DEFAULT_COUNTRY_CODE . $phone_number,
                'message' => $message,
                'patient_id' => $data['user_id']
            );
            $sening_sms = send_message_by_vibgyortel($send_otp);
        }
        $insert_temp_user = array(
            'temp_user_email' => $post_member_data['user_email'],
            'temp_user_password' => $post_member_data['user_password'],
            'temp_user_phone_number' => $post_member_data['user_phone_number'],
            'temp_user_created_at' => $this->utc_time_formated,
            'temp_user_unique_id' => $unique_id,
            'temp_auth_code' => $otp,
            'temp_user_user_type' => 1,
            'temp_auth_otp_expiry_time' => date('Y-m-d H:i:s', strtotime(OTP_EXPIRE_TOKEN_TIME, strtotime($this->utc_time_formated))),
        );
        $user_temp_id = $this->User_model->register_temp_user($insert_temp_user);
        $insert_family_log = array(
            'parent_patient_id' => $data['user_id'],
            'created_at' => $this->utc_time_formated,
            'user_temp_id' => $user_temp_id,
            'user_type' => 2, //2=dependant
            'user_data' => json_encode($post_member_data),
            'status' => 2 //2=Pending
        );
        if(!empty($member_user_data['user_id'])) {
            $insert_family_log['patient_id'] = $member_user_data['user_id'];
        }
        $this->User_model->create_family_log($insert_family_log);
        
        if(!empty($member_user_data['user_email'])) {
            $this->load->model('Emailsetting_model');
            $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(36);
            $parse_arr = array(
                '{PatinetName}' => $member_user_data['user_first_name'] . " " . $member_user_data['user_last_name'],
                '{ParentPatientName}' => $patient_user['user_first_name'] . " " . $patient_user['user_last_name'],
                '{OTP}' => $otp,
                '{WebUrl}' => DOMAIN_URL,
                '{AppName}' => APP_NAME,
                '{MailContactNumber}' => $email_template_data['email_static_data']['contact_number'],
                '{MailEmailAddress}' => $email_template_data['email_static_data']['email_id'],
                '{MailCompanyName}' => $email_template_data['email_static_data']['company_name'],
                '{CopyRightsYear}' => date('Y')
            );
            $message = replace_values_in_string($parse_arr, $email_template_data['email_template_message']);
            $subject = $email_template_data['email_template_subject'];
            //this function help you to send mail to single ot multiple users
            $this->send_email(array($member_user_data['user_email'] => $member_user_data['user_email']), $subject, $message);
        }
        return $user_temp_id;
    }

    /**
     * Description :- This function is used to get the detail of the family members
     * 
     * @author Manish Ramnani
     * 
     * Modified Date :- 2018-03-20
     * 
     */
    public function get_family_members_post() {

        try {

            $get_family_members = $this->User_model->get_family_members($this->user_id);
            //$pending_user = $this->User_model->get_pending_family_members($this->user_id);
            //$all_family_members = array_merge($get_family_members, $pending_user);

            if (!empty($get_family_members)) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('member_found');
                $this->my_response['user_data'] = $get_family_members;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('member_not_found');
            }
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_medical_condition_post() {
        try {
            $medical_condition_data = $this->Common_model->get_medical_condition(array("medical_condition_status" => 1),"medical_condition_id,medical_condition_name");
            $this->my_response['status'] = true;
            $this->my_response['medical_condition_data'] = $medical_condition_data;
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function add_family_medical_history_post() {

        $medical_condition_id = trim($this->Common_model->escape_data($this->post_data['condition_id']));
        $relation_id = $this->post_data['relation_id'];
        $date = $this->post_data['date'];
        $comments = trim($this->Common_model->escape_data($this->post_data['comments']));
        $other_user_id = trim($this->Common_model->escape_data($this->post_data['other_user_id']));

        try {
            if (
                    empty($medical_condition_id) ||
                    empty($relation_id)
            ) {
                $this->bad_request();
                exit;
            }
            $user_id = $this->user_id;
            if (!empty($other_user_id)) {
                $user_id = $other_user_id;
            }
            $created_time = $this->utc_time_formated;
            $insert_array = array(
                "family_medical_history_user_id" => $user_id,
                "family_medical_history_medical_condition_id" => $medical_condition_id,
                "family_medical_history_relation" => $relation_id,
                "family_medical_history_date" => $date,
                "family_medical_history_comment" => $comments,
                "family_medical_history_created_at" => $created_time,
            );
            $is_added = $this->User_model->insert(TBL_FAMILY_MEDICAL_HISTORY, $insert_array);
            if ($is_added > 0) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_added');
                $this->my_response['family_medical_history_data'] = array(
                    "family_medical_history_id" => (string) $is_added,
                    "family_medical_history_medical_condition_id" => $medical_condition_id,
                    "family_medical_history_relation" => $relation_id,
                    "family_medical_history_date" => $date,
                    "family_medical_history_comment" => $comments
                );
            } else {
                $this->my_response['status'] = FALSE;
                $this->my_response['message'] = lang('failure');
            }
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function edit_family_medical_history_post() {

        $medical_history_id = $this->post_data['medical_history_id'];
        $medical_condition_id = trim($this->Common_model->escape_data($this->post_data['condition_id']));
        $relation_id = $this->post_data['relation_id'];
        $date = $this->post_data['date'];
        $comments = trim($this->Common_model->escape_data($this->post_data['comments']));
        $other_user_id = trim($this->Common_model->escape_data($this->post_data['other_user_id']));

        try {
            if (
                    empty($medical_history_id) ||
                    empty($medical_condition_id) ||
                    empty($relation_id)
            ) {
                $this->bad_request();
                exit;
            }
            $user_id = $this->user_id;
            if (!empty($other_user_id)) {
                $user_id = $other_user_id;
            }
            $updated_time = $this->utc_time_formated;
            $update_array = array(
                "family_medical_history_medical_condition_id" => $medical_condition_id,
                "family_medical_history_relation" => $relation_id,
                "family_medical_history_date" => $date,
                "family_medical_history_comment" => $comments,
                "family_medical_history_updated_at" => $updated_time,
            );
            $where = array(
                "family_medical_history_id" => $medical_history_id,
                "family_medical_history_user_id" => $user_id,
                "family_medical_history_status" => 1
            );
            $is_udpated = $this->User_model->update(TBL_FAMILY_MEDICAL_HISTORY, $update_array, $where);
            if ($is_udpated > 0) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_update');
                $this->my_response['family_medical_history_data'] = array(
                    "family_medical_history_id" => (string) $medical_history_id,
                    "family_medical_history_medical_condition_id" => $medical_condition_id,
                    "family_medical_history_relation" => $relation_id,
                    "family_medical_history_date" => $date,
                    "family_medical_history_comment" => $comments
                );
            } else {
                $this->my_response['status'] = FALSE;
                $this->my_response['message'] = lang('failure');
            }
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function delete_family_medical_history_post() {
        $medical_history_id = $this->post_data['medical_history_id'];
        $other_user_id = trim($this->User_model->escape_data($this->post_data['other_user_id']));

        try {

            if (
                    empty($medical_history_id)
            ) {
                $this->bad_request();
                exit;
            }

            $user_id = $this->user_id;
            if (!empty($other_user_id)) {
                $user_id = $other_user_id;
            }

            $update_array = array(
                "family_medical_history_status" => 9,
                "family_medical_history_updated_at" => $this->utc_time_formated,
            );
            $where = array(
                "family_medical_history_id" => $medical_history_id,
                "family_medical_history_user_id" => $user_id,
            );
            $is_udpated = $this->User_model->update(TBL_FAMILY_MEDICAL_HISTORY, $update_array, $where);
            if ($is_udpated > 0) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_deleted');
            } else {
                $this->my_response['status'] = FALSE;
                $this->my_response['message'] = lang('failure');
            }
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to verify the email account
     * 
     * @author Manish Ramnani
     * 
     * 
     * @param type $token
     * @param type $id
     */
    public function email_verify_token($token = NULL) {

        if (!isset($token) || $token == NULL) {
            $this->session->set_flashdata('failure', 'Invalid Token');
            redirect(DOMAIN_URL);
        }

        //check token is valid or not
        $valid_token = $this->User->get_user(array("u_email_verified_token" => $token));
        if (empty($valid_token)) {
            $this->session->set_flashdata('failure', 'Invalid Token');
            redirect(DOMAIN_URL);
        } else {

            $udpate_array = array(
                "u_email_verified" => 1,
                "u_modified_date" => time(),
            );

            $where = array(
                "u_id" => $valid_token['u_id']
            );
            $is_update = $this->User->update_user($udpate_array, $where);

            if ($is_update) {
                $update_token = array(
                    'u_email_verified_token' => ''
                );
                $this->User->update_user($update_token, $where);
                $this->session->set_flashdata("success", "Thankyou, Your email account is verified");
            } else {
                $this->session->set_flashdata('failure', 'Something went wrong');
            }

            redirect(DOMAIN_URL);
        }
    }

    /**
     * Description :- This function is used to get the notification of the user
     * 
     * @author Manish Ramnani
     * 
     */
    public function get_notification_list_post() {

        try {

            $user_type = !empty($this->post_data['user_type']) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : '';

            if (empty($user_type)) {
                $this->bad_request();
            }
            $past_15_days_date = date('Y-m-d h:i:s',strtotime('-15 day'.date('Y-m-d h:i:s')));
            
            $where = array(
                'notification_list_user_id' => $this->user_id,
                'notification_list_user_type' => $user_type,
                'notification_list_type !=' => 3,
                'notification_list_created_at >' => $past_15_days_date
            );
            $column = 'notification_list_message, 
                       notification_list_read_status, 
                       notification_list_created_at,
                       notification_list_id';

            $get_notification_list = $this->Common_model->get_all_rows(TBL_NOTIFICATION, $column, $where, array(), array('notification_list_id' => 'Desc'));

            if (!empty($get_notification_list)) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $get_notification_list;
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

    private function get_user_role($user_id) {
        try {
            $where = array(
                'doctor_clinic_mapping_user_id' => $user_id,
                'doctor_clinic_mapping_status' => 1
            );
            $select_array = array(
                'doctor_clinic_mapping_role_id',
                'doctor_clinic_mapping_doctor_id'
            );
            $get_role = $this->Common_model->get_single_row(TBL_DOCTOR_CLINIC_MAPPING, $select_array, $where);
            if ($get_role) {
                return $get_role;
            }
            return false;
        } catch (ErrorException $ex) {
            throw $ex;
        }
    }

    /**
     * Description :- This function is used to delete the family member 
     * of the user and related data
     * 
     * @author Manish Ramnani
     * 
     */
    public function delete_family_member_post() {
        try {

            $other_user_id = !empty($this->post_data['other_user_id']) ? trim($this->Common_model->escape_data($this->post_data['other_user_id'])) : '';

            if (empty($other_user_id)) {
                $this->bad_request();
            }

            $this->db->trans_start();

            //delete the family member
            $update_data = array(
                'mapping_status' => 9,
                'updated_at' => $this->utc_time_formated,
                'updated_by' => $this->user_id
            );

            $update_where = array(
                'parent_patient_id' => $this->user_id,
                'patient_id' => $other_user_id,
                'mapping_status' => 1
            );
            $is_update = $this->User_model->update_family_member_map($update_data, $update_where);

            if ($is_update > 0) {
                //$user_data = $this->User_model->get_user_by_id($this->user_id, 'user_email,user_phone_number');
                //if(empty($user_data['user_email']) && empty($user_data['user_phone_number'])) {
                    //$return = $this->User_model->delete_all_details_user($other_user_id);
                //}

                $this->db->trans_commit();
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang("family_member_delete");
                
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function reset_user_password_post() {
        try {
            $staff_user_id = !empty($this->post_data['staff_user_id']) ? trim($this->Common_model->escape_data($this->post_data['staff_user_id'])) : '';
            $password = !empty($this->post_data['password']) ? trim($this->Common_model->escape_data($this->post_data['password'])) : '';
            if (empty($staff_user_id) || empty($password)) {
                $this->bad_request();
            }
            $get_user = $this->Common_model->get_single_row(TBL_USERS, 'user_id,user_password', array(
                'user_id' => $staff_user_id,
            ));
            if (empty($get_user)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('user_not_found');
                $this->send_response();
            }

            if ($get_user['user_password'] == $password) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("old_pass_new_pass_same");
                $this->send_response();
            }
            $password = password_hash($password, PASSWORD_BCRYPT);
            $update_data = array(
                'user_password' => $password,
            );

            $update_where = array(
                'user_id' => $staff_user_id,
            );

            $is_update = $this->Common_model->update(TBL_USERS, $update_data, $update_where);
            if ($is_update) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('resetpassword_reset_success');
                $where = array(
                    "udt_u_id" => $staff_user_id
                );

                $this->Common_model->delete_data(TBL_USER_DEVICE_TOKENS, $where);
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("resetpassword_reset_fail");
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function update_terms_condtion_post() {

        try {
            $flag = !empty($this->post_data['flag']) ? trim($this->Common_model->escape_data($this->post_data['flag'])) : '';

            if (empty($flag) || $flag == 2) {
                $this->bad_request();
            }

            $update_array = array(
                'user_details_agree_terms' => 1,
                'user_details_modifed_at' => $this->utc_time_formated
            );

            $where = array(
                'user_details_user_id' => $this->user_id
            );

            $is_update = $this->Common_model->update(TBL_USER_DETAILS, $update_array, $where);

            if ($is_update) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang("common_detail_update");
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("failure");
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to register the user after verification of the otp
     * 
     * 
     */
    public function member_verify_otp_post() {

        $otp = !empty($this->post_data['otp']) ? $this->post_data['otp'] : "";
        $registered_id = !empty($this->post_data['other_user_id']) ? $this->post_data['other_user_id'] : "";

        $device_type = !empty($this->post_data['device_type']) ? trim($this->Common_model->escape_data($this->post_data['device_type'])) : "";
        $device_token = !empty($this->post_data['device_token']) ? trim($this->Common_model->escape_data($this->post_data['device_token'])) : "";

        try {   

            if (empty($otp) ||
                    empty($registered_id) ||
                    empty($device_type)
            ) {
                $this->bad_request();
            }

            $user_data = $this->User_model->get_user_temp_details_by_id($registered_id);

            if (!empty($user_data)) {

                if (strtotime($user_data['temp_auth_otp_expiry_time']) >= $this->utc_time) {

                    if ($user_data['temp_auth_attempt_count'] >= VERIFY_OTP_LIMIT) {
                        $this->my_response['status'] = false;
                        $this->my_response['temp_user_phone_verified'] = '2';
                        $this->my_response['message'] = lang("otp_verfication_limit_reach");
                        $this->send_response();
                    }

                    if ($user_data['temp_auth_code'] == $otp) {

                        //register the user now
                        $family_log = $this->User_model->get_family_log_by_temp_id($registered_id,
                         'parent_patient_id,patient_id,user_temp_id,user_type,user_data,user_data');

                        $family_data = (array) json_decode($family_log['user_data']);
                        $phone_number = $family_data['user_phone_number'];
                        $email = $family_data['user_email'];
                        $first_name = $family_data['user_first_name'];
                        $last_name = $family_data['user_last_name'];
                        $relation = $family_data['user_parent_relation'];
                        $password = $family_data['user_password'];
                        $unique_id = $family_data['user_unique_id'];
                        $user_type = 1;
                        $data_map = array(
                            'parent_patient_id' => $family_log['parent_patient_id'],
                            'mapping_status' => 1,
                            'created_at' => $this->utc_time_formated,
                            'created_by' => $this->user_id
                        );
                        if(!empty($family_log['patient_id'])) {
                            $user_id = $family_log['patient_id'];
                            //Map family member
                            $data_map['patient_id'] = $user_id;
                            $this->User_model->create_family_member_map($data_map);
                            //create family member log
                            $family_log['patient_id'] = $user_id;
                            $family_log['created_at'] = $this->utc_time_formated;
                            $family_log['status'] = 1; // 1=Accept
                            $this->User_model->create_family_log($family_log);
                            $where = array(
                                'parent_patient_id' => $family_log['parent_patient_id'],
                                'user_temp_id' => $registered_id,
                                'status' => 2
                            );
                            $log_data = array('is_deleted' => 9);
                            $this->User_model->update_family_log($log_data, $where);
                            $user_update_data = array('user_parent_relation' => $family_data['user_parent_relation']);
                            $user_update_where = array('user_id' => $user_id);
                            $this->User_model->update_user_data($user_update_data, $user_update_where);
                        } else {
                            $insert_array = array(
                                'user_first_name' => $family_data['user_first_name'],
                                'user_last_name' => $family_data['user_last_name'],
                                'user_email' => $family_data['user_email'],
                                'user_phone_number' => $family_data['user_phone_number'],
                                'user_parent_relation' => $family_data['user_parent_relation'],
                                'user_created_at' => $this->utc_time_formated,
                                'user_password' => $family_data['user_password'],
                                'user_unique_id' => $family_data['user_unique_id'],
                                'user_type' => $user_type,
                                'user_plan_id' => DEFAULT_USER_PLAN_ID
                            );

                            $user_id = $this->User_model->register_user($insert_array);
                            //echo $user_id;die;
                            if ($user_id > 0) {
                                //Map family member
                                $data_map['patient_id'] = $user_id;
                                $this->User_model->create_family_member_map($data_map);
                                //create family member log
                                $family_log['patient_id'] = $user_id;
                                $family_log['created_at'] = $this->utc_time_formated;
                                $family_log['status'] = 1; // 1=Accept
                                $this->User_model->create_family_log($family_log);
                                $where = array(
                                    'parent_patient_id' => $family_log['parent_patient_id'],
                                    'user_temp_id' => $registered_id,
                                    'status' => 2
                                );
                                $log_data = array('is_deleted' => 9);
                                $this->User_model->update_family_log($log_data, $where);
                               //store the user authentication details
                                $user_auth_insert_array = array(
                                    'auth_user_id' => $user_id,
                                    'auth_type' => 2,
                                    'auth_code' => '',
                                    'auth_otp_expiry_time' => '',
                                    'auth_created_at' => $this->utc_time_formated
                                );
                                $this->User_model->store_auth_detail($user_auth_insert_array);

                                //by default 2 way authentication on for the patient
                                $setting_array = array();
                                $setting_array[] = array(
                                    'id' => "1",
                                    'name' => "data security",
                                    'status' => "2"
                                );
                                $insert_setting_array = array(
                                    'setting_user_id' => $user_id,
                                    'setting_clinic_id' => '',
                                    'setting_data' => json_encode($setting_array),
                                    'setting_type' => 2,
                                    'setting_data_type' => 1,
                                    'setting_created_at' => $this->utc_time_formated
                                );

                                $this->Common_model->insert(TBL_SETTING, $insert_setting_array);


                                if (!empty($email)) {

                                    $reset_token = str_rand_access_token(20);

                                    $user_auth_email_insert_array = array(
                                        'auth_user_id' => $user_id,
                                        'auth_type' => 1,
                                        'auth_code' => $reset_token,
                                        'auth_otp_expiry_time' => date('Y-m-d H:i:s', strtotime(OTP_EXPIRE_TOKEN_TIME, strtotime($this->utc_time_formated))),
                                        'auth_created_at' => $this->utc_time_formated
                                    );
                                    $this->User_model->store_auth_detail($user_auth_email_insert_array);

                                    $verify_link = DOMAIN_URL . "verifyaccount/" . $reset_token;

                                    //this is use for get view and store data in variable
                                    //EMAIL TEMPLATE START BY PRAGNESH
                                    $this->load->model('Emailsetting_model');
                                    $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(14);
                                    $parse_arr = array(
                                        '{Email_id}' => $email,
                                        '{PatinetName}' => $insert_array['user_first_name'] . " " . $insert_array['user_last_name'],
                                        '{Password}' => $password,
                                        '{EmailPasswordImage}' => 1,
                                        '{UniqueId}' => $unique_id,
                                        '{VerificationLink}' => $verify_link,
                                        '{WebUrl}' => DOMAIN_URL,
                                        '{AppName}' => APP_NAME,
                                        '{MailContactNumber}' => $email_template_data['email_static_data']['contact_number'],
                                        '{MailEmailAddress}' => $email_template_data['email_static_data']['email_id'],
                                        '{MailCompanyName}' => $email_template_data['email_static_data']['company_name'],
                                        '{CopyRightsYear}' => date('Y')
                                    );
                                    $message = replace_values_in_string($parse_arr, $email_template_data['email_template_message']);
                                    $subject = $email_template_data['email_template_subject'];
                                    //EMAIL TEMPLATE END BY PRAGNESH
                                    //this function help you to send mail to single ot multiple users
                                    $this->send_email(array($email => $email), $subject, $message);
                                } else {
                                    $user_auth_email_insert_array = array(
                                        'auth_user_id' => $user_id,
                                        'auth_type' => 1,
                                        'auth_code' => NULL,
                                        'auth_otp_expiry_time' => NULL,
                                        'auth_created_at' => $this->utc_time_formated
                                    );
                                    $this->User_model->store_auth_detail($user_auth_email_insert_array);
                                }

                                if (!empty($phone_number)) {
                                    $user_name = ucfirst($first_name).' '.ucfirst($last_name);
                                    //get the logged in user detail
                                    $parent_data = $this->User_model->get_details_by_id($this->user_id);
                                    $parent_name = 'Someone';
                                    if (!empty($parent_data['user_first_name'])) {
                                        $parent_name = ucfirst($parent_data['user_first_name']).' '.ucfirst($parent_data['user_last_name']);
                                    }
                                    $message = sprintf(lang('added_family_member'), $user_name, $parent_name);
                                    $send_sms_family_member = array(
                                        'phone_number' => DEFAULT_COUNTRY_CODE . $phone_number,
                                        'message' => $message,
                                        'patient_id' => $this->user_id
                                    );
                                    send_message_by_vibgyortel($send_sms_family_member);
                                }
                            }
                        }
                        $this->Common_model->update(TBL_USER_TEMP, array('temp_user_status' => 9), array('temp_user_id' => $registered_id));
                        $user_data = $this->User_model->get_user_details_by_id($user_id);
                        $this->my_response['status'] = true;
                        $this->my_response['message'] = lang("member_add");
                        $this->my_response['user_data'] = $user_data; 
                    } else {
                        $remain_otp_verify_limit = VERIFY_OTP_LIMIT - ($user_data['temp_auth_attempt_count'] + 1);
                        $this->User_model->increase_opt_temp_user_limit(1, $user_data['temp_user_id'], ($user_data['temp_auth_attempt_count'] + 1));
                        $this->my_response['status'] = false;
                        $this->my_response['temp_user_phone_verified'] = '1';
                        $this->my_response['message'] = sprintf(lang("invalid_otp_for_login"), $remain_otp_verify_limit);
                    }
                } else {
                    $this->my_response['status'] = false;
                    $this->my_response['temp_user_phone_verified'] = '1';
                    $this->my_response['message'] = lang("otp_token_expire");
                    $this->my_response['is_expire'] = "1";
                }
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("user_detail_not_found");
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    } 

    /**
     * Description :- This function is used to delete the family member 
     * of the user and related data
     * 
     */
    public function accept_family_member_request_post() {
        try {

            $parent_patient_id = !empty($this->post_data['parent_patient_id']) ? trim($this->Common_model->escape_data($this->post_data['parent_patient_id'])) : '';
            $request_status = !empty($this->post_data['request_status']) ? trim($this->Common_model->escape_data($this->post_data['request_status'])) : '';

            if (empty($parent_patient_id) || empty($request_status)) {
                $this->bad_request();
            }
            $where = array(
                'parent_patient_id' => $parent_patient_id,
                'patient_id' => $this->user_id,
                'mapping_status' => 1
            );
            $rows = $this->User_model->check_member_mapped($where);
            if ($rows > 0) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("already_mapped");
                $this->send_response();
            }
            //create family member log
            $family_log = array();
            $family_log['parent_patient_id'] = $parent_patient_id;
            $family_log['user_type'] = 1; // 1=independent
            $family_log['patient_id'] = $this->user_id;
            $family_log['created_at'] = $this->utc_time_formated;
            $family_log['status'] = $request_status;
            $id = $this->User_model->create_family_log($family_log);

            if ($id > 0) {
                //Map family member
                $data_map = array(
                    'parent_patient_id' => $parent_patient_id,
                    'patient_id' => $this->user_id,
                    'mapping_status' => 1,
                    'created_at' => $this->utc_time_formated,
                    'created_by' => $this->user_id
                );
                $parent_patient_user = $this->User_model->get_user_by_id($parent_patient_id, 'user_first_name, user_last_name, user_phone_number,user_email,user_email_verified');
                $patient_user = $this->User_model->get_user_by_id($this->user_id, 'user_first_name, user_last_name');
                if($request_status == 1) {
                    $this->User_model->create_family_member_map($data_map);
                    if(!empty($parent_patient_user['user_phone_number'])) {
                        $message = sprintf(lang('request_accept_sms'), $parent_patient_user['user_first_name'] . " " . $parent_patient_user['user_last_name'], $patient_user['user_first_name'] . " " . $patient_user['user_last_name']);
                        $send_sms = array(
                            'phone_number' => DEFAULT_COUNTRY_CODE . $parent_patient_user['user_phone_number'],
                            'message' => $message,
                            'patient_id' => $this->user_id
                        );
                        $sening_sms = send_message_by_vibgyortel($send_sms);
                    }
                    $email_template_id = 35;
                    $this->my_response['message'] = lang("request_accept_success");
                } else {
                    if(!empty($parent_patient_user['user_phone_number'])) {
                        $message = sprintf(lang('request_decline_sms'), $parent_patient_user['user_first_name'] . " " . $parent_patient_user['user_last_name'], $patient_user['user_first_name'] . " " . $patient_user['user_last_name']);
                        $send_sms = array(
                            'phone_number' => DEFAULT_COUNTRY_CODE . $parent_patient_user['user_phone_number'],
                            'message' => $message,
                            'patient_id' => $this->user_id
                        );
                        $sening_sms = send_message_by_vibgyortel($send_sms);
                    }
                    $this->my_response['message'] = lang("request_decline_success");
                    $email_template_id = 34;
                }
                if(!empty($parent_patient_user['user_email']) && $parent_patient_user['user_email_verified'] == 1) {
                    $this->load->model('Emailsetting_model');
                    $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id($email_template_id);
                    $parse_arr = array(
                        '{ParentPatientName}' => $parent_patient_user['user_first_name'] . " " . $parent_patient_user['user_last_name'],
                        '{PatinetName}' => $patient_user['user_first_name'] . " " . $patient_user['user_last_name'],
                        '{WebUrl}' => DOMAIN_URL,
                        '{AppName}' => APP_NAME,
                        '{MailContactNumber}' => $email_template_data['email_static_data']['contact_number'],
                        '{MailEmailAddress}' => $email_template_data['email_static_data']['email_id'],
                        '{MailCompanyName}' => $email_template_data['email_static_data']['company_name'],
                        '{CopyRightsYear}' => date('Y')
                    );
                    $message = replace_values_in_string($parse_arr, $email_template_data['email_template_message']);
                    $subject = $email_template_data['email_template_subject'];
                    //this function help you to send mail to single ot multiple users
                    $this->send_email(array($parent_patient_user['user_email'] => $parent_patient_user['user_email']), $subject, $message);
                }

                $where = array(
                    'parent_patient_id' => $parent_patient_id,
                    'patient_id' => $this->user_id,
                    'status' => 2
                );
                $log_data = array('is_deleted' => 9);
                $this->User_model->update_family_log($log_data, $where);
                $this->my_response['status'] = true;
                
                
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to unmapped the family member 
     * of the user and related data
     * 
     */
    public function remove_family_member_post() {
        try {

            $parent_user_id = !empty($this->post_data['parent_user_id']) ? trim($this->Common_model->escape_data($this->post_data['parent_user_id'])) : '';

            if (empty($parent_user_id)) {
                $this->bad_request();
            }

            $this->db->trans_start();

            //unmapped the family member
            $update_data = array(
                'mapping_status' => 9,
                'updated_at' => $this->utc_time_formated,
                'updated_by' => $this->user_id
            );

            $update_where = array(
                'parent_patient_id' => $parent_user_id,
                'patient_id' => $this->user_id,
                'mapping_status' => 1
            );
            $is_update = $this->User_model->update_family_member_map($update_data, $update_where);

            if ($is_update > 0) {
                $this->db->trans_commit();
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang("member_removed");
                
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to get all linked family member 
     * of the user and related data
     * 
     */
    public function get_linked_family_member_post() {
        try {

            $user_id = !empty($this->post_data['user_id']) ? trim($this->Common_model->escape_data($this->post_data['user_id'])) : '';

            if (empty($user_id)) {
                $this->bad_request();
            }
            
            $user_data = $this->User_model->get_linked_family_members($user_id);
            if (!empty($user_data) && count($user_data) > 0) {
                foreach ($user_data as $key => $value) {
                    $user_data[$key]->details = 'can access your details as family member from ' . date('d-m-Y', strtotime($value->created_at)) . '.';
                    $user_data[$key]->user_photo_filepath = get_image_thumb($value->user_photo_filepath);
                }
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('member_found');
                $this->my_response['user_data'] = $user_data;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('member_not_found');
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to add the family member
     * 
     * @author Manish Ramnani
     * 
     * Modified Date :- 2018-03-20
     */
    public function check_patient_exist_post() {

        $phone_number = !empty($this->post_data['phone_number']) ? $this->Common_model->escape_data($this->post_data['phone_number']) : "";
        $email = !empty($this->post_data['email']) ? $this->Common_model->escape_data($this->post_data['email']) : "";
        $user_type = 1;
        try {
            if (empty($phone_number) &&  empty($email)) {

                $this->bad_request();
                die;
            }

            if (!empty($email) && validate_email($email)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("invalid_email");
                $this->send_response();
            }

            if ((!empty($phone_number) && validate_phone_number($phone_number))) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("invalid_phone_number");
                $this->send_response();
            }

            if(!empty($phone_number)) {
                $check_number = $this->User_model->check_user_number_exists($phone_number, $user_type, 'user_first_name,user_last_name,user_email,user_phone_number');
                if(!empty($check_number) && count($check_number) > 0){
                    $this->my_response['message'] = lang('member_found');
                    $this->my_response['user_data'] = $check_number;
                    $this->my_response['status'] = true;
                    $this->send_response();
                }
            } 
            if(!empty($email)) {
                $check_email = $this->User_model->get_details_by_email($email, $user_type, 'user_first_name,user_last_name,user_email,user_phone_number');
                if(!empty($check_email) && count($check_email) > 0){
                    $this->my_response['message'] = lang('member_found');
                    $this->my_response['user_data'] = $check_email;
                    $this->my_response['status'] = true;
                    $this->send_response();
                }
            }
            $this->my_response['status'] = false;
            $this->my_response['message'] = lang('member_not_found');
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * 
     * this function to resend otp to added member
     * 
     * @author 
     * 
     */
    public function member_resend_otp_post() {

        $registered_id = !empty($this->post_data['other_user_id']) ? $this->post_data['other_user_id'] : "";

        try {
            if (
                    empty($registered_id)
            ) {
                $this->bad_request();
                exit;
            }

            $user_data = $this->User_model->get_user_temp_details_by_id($registered_id);

            if (
                    (!empty($user_data) && count($user_data) > 0)
            ) {

                if (!empty($user_data['temp_auth_resend_timestamp']) && (strtotime($user_data['temp_auth_resend_timestamp']) + RESEND_OTP_NEXT_TIME) >= time()) {
                    $wating_time = (strtotime($user_data['temp_auth_resend_timestamp']) + RESEND_OTP_NEXT_TIME) - time();
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = sprintf(lang("otp_resend_time_limit"), $wating_time);
                    $this->send_response();
                }

                if ($user_data['temp_auth_resend_count'] >= RESEND_OTP_LIMIT) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("otp_resend_limit_reach");
                    $this->send_response();
                }

                $otp = getUniqueToken(6, 'numeric');
                $family_log = $this->User_model->get_family_log_by_temp_id($registered_id,'parent_patient_id,user_data');
                $family_data = (array) json_decode($family_log['user_data']);
                $first_name = $family_data['user_first_name'];
                $last_name = $family_data['user_last_name'];
                $patient_user = $this->User_model->get_user_by_id($family_log['parent_patient_id'], 'user_first_name, user_last_name');
                $sening_sms = false;
                if(!empty($user_data['temp_user_phone_number'])) {
                    $message = sprintf(lang('user_family_member_otp'), $first_name . " " . $last_name, $patient_user['user_first_name'] . " " . $patient_user['user_last_name'], $otp);
                    $send_otp = array(
                        'phone_number' => DEFAULT_COUNTRY_CODE . $user_data['temp_user_phone_number'],
                        'message' => $message,
                        'patient_id' => $this->user_id
                    );
                    $sening_sms = send_message_by_vibgyortel($send_otp);
                }
                $email_sent = false;
                if(!empty($user_data['temp_user_email'])) {
                    $this->load->model('Emailsetting_model');
                    $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(36);
                    $parse_arr = array(
                        '{PatinetName}' => $first_name . " " . $last_name,
                        '{ParentPatientName}' => $patient_user['user_first_name'] . " " . $patient_user['user_last_name'],
                        '{OTP}' => $otp,
                        '{WebUrl}' => DOMAIN_URL,
                        '{AppName}' => APP_NAME,
                        '{MailContactNumber}' => $email_template_data['email_static_data']['contact_number'],
                        '{MailEmailAddress}' => $email_template_data['email_static_data']['email_id'],
                        '{MailCompanyName}' => $email_template_data['email_static_data']['company_name'],
                        '{CopyRightsYear}' => date('Y')
                    );
                    $message = replace_values_in_string($parse_arr, $email_template_data['email_template_message']);
                    $subject = $email_template_data['email_template_subject'];
                    //this function help you to send mail to single ot multiple users
                    $this->send_email(array($user_data['temp_user_email'] => $user_data['temp_user_email']), $subject, $message);
                    $email_sent = true;
                }

                if ($sening_sms || $email_sent) {

                    $auth_update_data = array(
                        'temp_auth_code' => $otp,
                        'temp_auth_otp_expiry_time' => date('Y-m-d H:i:s', strtotime(OTP_EXPIRE_TOKEN_TIME, strtotime($this->utc_time_formated))),
                        "temp_auth_attempt_count" => 0,
                        "temp_auth_resend_timestamp" => date("Y-m-d H:i:s"),
                        "temp_auth_resend_count" => $user_data['temp_auth_resend_count'] + 1
                    );

                    $this->Common_model->update(TBL_USER_TEMP, $auth_update_data, array('temp_user_id' => $registered_id));

                    $this->my_response['status'] = true;
                    $this->my_response['message'] = lang("otp_sent_to_mobile");
                    $this->send_response();
                } else {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("sms_otp_send_fail");
                    $this->send_response();
                }
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("user_detail_not_found");
                $this->send_response();
            }
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->store_error();
        }
    }

}
