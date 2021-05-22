<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * This controller use for handle resetpassword request
 * 
 * 
 * Modified Date :- 2016-09-15
 * 
 */
class Resetpassword extends CI_Controller {

    public $utc_time;

    public function __construct() {
        parent::__construct();

        $this->utc_time = date(DATE_FORMAT, time());
    }

    /**
     * 
     * This function use for handle reset password request and display view
     * 
     
     * 
     * @param string $token
     */
    public function index($token = "") {
        
        $data = array();
        if (empty($token)) {

            $data['success'] = false;
            $data['message'] = lang("resetpassword_token_empty");
        } else {

            $where = array();
            $where['auth_code'] = $token;

            $user_data = $this->Common_model->get_single_row(TBL_USER_AUTH, "auth_user_id,auth_otp_expiry_time", $where);

            if (empty($user_data)) {
                $data['success'] = false;
                $data['message'] = lang("resetpassword_token_invalid");
            } else {
                if (strtotime($user_data['auth_otp_expiry_time']) >= time()) {
                    $data['userId'] = $user_data['auth_user_id'];
                } else {
                    $data['success'] = false;
                    $data['message'] = lang("resetpassword_token_expire");
                }
            }
        }
        $data['breadcrumbs'] = 'Reset Password';
        $data['page_title'] = 'Reset Password';
        $data['is_load_reset_password_js'] = true;
        $this->load->view("web/reset", $data);
    }

    /**
     * 
     * This function use for handle verify the email account
     * 
     
     * 
     * @param string $token
     */
    public function verify_account($token = "") {
        $data = array();
        if (empty($token)) {
            $data['success'] = false;
            $data['message'] = lang("resetpassword_token_empty");
        } else {
            $where = array();
            $where['auth_code'] = $token;

            $columns = "auth_user_id,
                        auth_otp_expiry_time,
                        auth_phone_number,
                        auth_is_clinic";

            $get_data_query = " SELECT " . $columns . " FROM  " . TBL_USER_AUTH . " WHERE auth_code =  '" . $token . "' AND auth_type = 1 ";
            $user_data = $this->Common_model->get_single_row_by_query($get_data_query);
            

            $this->db->trans_start();
            if (empty($user_data)) {
                $data['success'] = false;
                $data['message'] = lang("resetpassword_token_invalid");
            } else {
                if (strtotime($user_data['auth_otp_expiry_time']) >= time()) {

                    if ($user_data['auth_is_clinic'] == 1) {

                        $update_clinic_data = array(
                            'clinic_email' => $user_data['auth_phone_number'],
                            'clinic_email_verified' => 1
                        );

                        $update_clinic_where = array(
                            'clinic_id' => $user_data['auth_user_id']
                        );

                        $update = $this->Common_model->update(TBL_CLINICS, $update_clinic_data, $update_clinic_where);

                        //update the auth table
                        $update_auth_data = array(
                            'auth_code' => NULL,
                            'auth_otp_expiry_time' => NULL,
                            'auth_phone_number' => NULL
                        );

                        $where_auth_data = array(
                            'auth_type' => 1,
                            'auth_is_clinic' => 1,
                            'auth_user_id' => $user_data['auth_user_id']
                        );
                        $is_auth_update = $this->Common_model->update(TBL_USER_AUTH, $update_auth_data, $where_auth_data);
                    } else {
                        //auth phone number i.e email id
                        $update_data = array();
                        $update_data['user_email_verified'] = 1;
                        //$update_data['user_email'] = $user_data['auth_phone_number'];
                        
                        $where = array();
                        $where['user_id'] = $user_data['auth_user_id'];
                        
                        $update = $this->Common_model->update(TBL_USERS, $update_data, $where);

                        //update the auth table
                        $update_auth_data = array(
                            'auth_code' => NULL,
                            'auth_otp_expiry_time' => NULL,
                            'auth_phone_number' => NULL
                        );

                        $where_auth_data = array(
                            'auth_type' => 1,
                            'auth_user_id' => $user_data['auth_user_id']
                        );
                        $is_auth_update = $this->Common_model->update(TBL_USER_AUTH, $update_auth_data, $where_auth_data);

                        $user_id = $user_data['auth_user_id'];
                        //check if the all details of the doctor approved by the admin
                        //then status the status of the doctor active
                        $requested_data = array(
                            'user_id' => $user_id
                        );
                        $get_user_detail = $this->Common_model->get_user_detail($requested_data);

                        if (!empty($get_user_detail) && $get_user_detail['user_type'] == 2) {

                            //check the education details is apporved or not
                            $education_where = array(
                                'doctor_qualification_user_id' => $user_id,
                                'doctor_qualification_status IN (2,3)' => NULL
                            );
                            $get_edu_count = $this->Common_model->get_count(TBL_DOCTOR_EDUCATIONS, 'doctor_qualification_id', $education_where);

                            //check the registration details
                            $registration_where = array(
                                'doctor_registration_user_id' => $user_id,
                                'doctor_registration_status IN (2,3) ' => NULL
                            );
                            $get_registration_count = $this->Common_model->get_count(TBL_DOCTOR_REGISTRATIONS, 'doctor_registration_id', $registration_where);

                            if ($get_edu_count <= 0 && $get_registration_count <= 0) {
                                //change the doctor status from pending to the active
                                $user_where = array('user_id' => $user_id);
                                $update_user = array('user_modified_at' => $this->utc_time);
								//'user_status' => 1
                                $this->Common_model->update(TBL_USERS, $update_user, $user_where);
                            }
                        }
                    }

                    if ($this->db->trans_status() !== FALSE) {
                        $this->db->trans_commit();
                        /*$where_data = array(
                            'udt_status' => 1,
                            'udt_device_token !=' => '',
                            'user_email' => $user_data['auth_phone_number'],
                            'user_status' => 1
                        );

                        $fields = 'udt_u_id,
                                udt_device_token,
                                udt_device_type
                                ';

                        $join_array = array(
                            TBL_USERS => 'user_id = udt_u_id'
                        );
                        $device_tokens = $this->Common_model->get_single_row(TBL_USER_DEVICE_TOKENS, $fields, $where_data, $join_array);

                        if (!empty($device_tokens)) {
                            if ($device_tokens['udt_device_type'] == 'android' ||
                                    $device_tokens['udt_device_type'] == 'web'
                            ) {

                                $message = array();
                                $message['send_date'] = "now";
                                $message['link'] = "https://www.medsign.in";
                                $message['content'] = "Your email verified successfully";
                                $message['data']['notification_list_type'] = 4;

                                if ($user_data['auth_is_clinic'] == 1) {
                                    $message['data']['email_flag'] = 1;
                                } else {
                                    $message['data']['email_flag'] = 2;
                                }
                                $message['data']['email_verified'] = 1;
                                $message['devices'] = array(
                                    $device_tokens['udt_device_token']
                                );

                                $result = send_pushwoosh_notification($message);
                                $log = LOG_FILE_PATH . "" . date('d-m-Y') . ".txt";
                                file_put_contents($log, "\n  ================ START =====================    \n\n", FILE_APPEND | LOCK_EX);
                                if ($result) {
                                    file_put_contents($log, "Notification send to " . $device_tokens['udt_u_id'] . " ", FILE_APPEND | LOCK_EX);
                                    file_put_contents($log, "Notification send to " . json_encode($device_tokens) . " ", FILE_APPEND | LOCK_EX);
                                } else {
                                    file_put_contents($log, "Fail to send notification " . $device_tokens['udt_u_id'] . " ", FILE_APPEND | LOCK_EX);
                                }
                                file_put_contents($log, "\n  ================ END =====================    \n\n", FILE_APPEND | LOCK_EX);
                            }
                        }*/

                        $data['success'] = true;
                        $data['message'] = lang("email_verify");
                    } else {
                        $this->db->trans_rollback();
                        $data['success'] = false;
                        $data['message'] = lang("failure");
                    }
                } else {
                    $data['success'] = false;
                    $data['message'] = lang("resetpassword_token_expire");
                }
            }
        }
        $data['breadcrumbs'] = 'Email Verification';
        $data['page_title'] = 'Email Verification';
        $this->load->view("web/verifyaccount", $data);
    }

    /**
     * 
     * This function use for handle resetpassword form request
     * 
     
     * 
     * Modified Date :- 2016-09-15
     * 
     */
    public function reset() {

        if (!isset($_POST) || empty($_POST) || count($_POST) <= 0) {
            $data['success'] = false;
            $data['message'] = lang("resetpassword_reset_empty");
        } else {
            $userId = $this->input->post("userId");
            $password = $this->input->post("password");

            if (empty($userId) || empty($password)) {
                $data['success'] = false;
                $data['message'] = lang("resetpassword_reset_empty");
            } else {
                $update_data = array();
                $update_data['user_password'] = password_hash(sha1($password), PASSWORD_BCRYPT);
                $update_data['user_modified_at'] = date(DATE_FORMAT, time());

                $where = array();
                $where['user_id'] = $userId;
                $update = $this->Common_model->update(TBL_USERS, $update_data, $where);

                //update the auth table
                $update_auth_data = array(
                    'auth_code' => NULL,
                    'auth_otp_expiry_time' => NULL
                );

                $where_auth_data = array(
                    'auth_type' => 3,
                    'auth_user_id' => $userId
                );
                $is_auth_update = $this->Common_model->update(TBL_USER_AUTH, $update_auth_data, $where_auth_data);

                if ($update > 0 && $is_auth_update > 0) {
                    $data['success'] = true;
                    $data['message'] = lang("resetpassword_reset_success");
                } else {
                    $data['success'] = false;
                    $data['message'] = lang("resetpassword_reset_fail");
                }
            }
        }
        $this->load->view("web/reset", $data);
    }
}
?>