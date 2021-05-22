<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Login extends MY_Controller {

    public function __construct() {

        parent::__construct();
        $this->load->library('patient_auth');
        $this->load->model('patient_model','patient');
        $this->load->library('form_validation');
        if($this->patient_auth->is_logged_in()) {
            if($this->uri->segment(2)) {
                $id_arr = explode('_', $this->uri->segment(2));
                if(!empty($id_arr[1]) && $id_arr[1] == 'uas7') {
                    redirect('patient/utilities_list');
                }
            }
            redirect('patient/dashboard');
        }
    }

    public function login_view($unique_id = '') {
        $view_data = array();
        $view_data['breadcrumbs'] = "Login";
        $view_data['page_title'] = "Login";
        $this->form_validation->set_rules('phone_number', 'phone number', 'required|trim');
        $this->form_validation->set_rules('user_password', 'password', 'required|trim');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $view_data['errors'] = $errors;
        } else {
            $user_data = $this->patient->get_patient_by_phone(set_value('phone_number'));
            if(!empty($user_data)) {
                if (password_verify(sha1(set_value('user_password')), $user_data->user_password)) {
                    $this->patient_auth->login([
                        'user_id' => $user_data->user_id, 
                        'patient_name' => $user_data->user_first_name . ' ' . $user_data->user_last_name
                    ]);
                    if($this->input->post('redirect_page'))
                        redirect('patient/'.$this->input->post('redirect_page'));
                    else
                        redirect('patient');
                }
            }
            $view_data['errors'] = "<p>Invalid login</p>";
        }
        if(!empty($unique_id)) {
            $id_arr = explode('_', $unique_id);
            $view_data['redirect_page'] = '';
            if(!empty($id_arr[1]) && $id_arr[1] == 'uas7') {
                $view_data['redirect_page'] = 'utilities_list';
            }
            $row_link = $this->Common_model->get_single_row('me_patient_share_link_log', 'patient_id,doctor_id,share_clinic_id,status', ['unique_code' => $id_arr[0]]);
            if(empty($row_link))
                redirect('patient/login');
            if(!empty($row_link['status']) && $row_link['status'] != 1)
                redirect(DOMAIN_URL);
            if(!empty($row_link['doctor_id']) && is_numeric($row_link['doctor_id'])) {
                $doctor_details = $this->patient->doctor_details($row_link['doctor_id'],$row_link['share_clinic_id']);
                $view_data['doctor_details'] = $doctor_details;
            }
        }
        $this->load->view('patient/login_form', $view_data);
    }

    public function register_view($unique_id = '') {
        $view_data = array();
        $view_data['breadcrumbs'] = "Registration";
        $view_data['page_title'] = "Registration";
        $view_data['doctor_details'] = array();
        $view_data['patient_details'] = array();
        if(!empty($unique_id)) {
            $id_arr = explode('_', $unique_id);
            $row_link = $this->Common_model->get_single_row('me_patient_share_link_log', 'id,patient_id,doctor_id,share_clinic_id,is_set_password,status', ['unique_code' => $id_arr[0]]);
            if(empty($row_link))
                redirect('patient/login');
            if(!empty($row_link['is_set_password']) && $row_link['is_set_password'] == 1){
                redirect('patient/login/' . $unique_id);
            }
            if(!empty($row_link['status']) && $row_link['status'] != 1){
                redirect(DOMAIN_URL);
            }
            if(!empty($row_link['doctor_id']) && is_numeric($row_link['doctor_id'])) {
                $doctor_details = $this->patient->doctor_details($row_link['doctor_id'],$row_link['share_clinic_id']);
                $view_data['doctor_details'] = $doctor_details;
                $view_data['share_link_id'] = $row_link['id'];
            }
            if(!empty($row_link['patient_id']) && is_numeric($row_link['patient_id'])) {
                $patient_details = $this->patient->get_patient_all_details($row_link['patient_id']);
                $view_data['patient_details'] = !empty($patient_details) ? $patient_details : [];
            }
            $view_data['redirect_page'] = '';
            if(!empty($id_arr[1]) && $id_arr[1] == 'uas7') {
                $view_data['redirect_page'] = 'utilities_list';
            }
        }
        $share_id = $this->input->get('id');
        if(!empty($share_id)) {
            $share_id = encrypt_decrypt($share_id,'decrypt');
            $share_row = $this->Common_model->get_single_row('me_registration_share_link', 'registration_share_id,registration_share_social_media_id,registration_share_clinic_id,registration_share_doctor_id,registration_share_expiry_date,', ['registration_share_status' => 1, 'registration_share_id' => $share_id]);
            if(!empty($share_row['registration_share_doctor_id']) && (get_display_date_time('Y-m-d') <= $share_row['registration_share_expiry_date'] || empty($share_row['registration_share_expiry_date']))) {
                $doctor_details = $this->patient->doctor_details($share_row['registration_share_doctor_id'], $share_row['registration_share_clinic_id']);
                $view_data['doctor_details'] = $doctor_details;
            }
            if(get_display_date_time('Y-m-d') <= $share_row['registration_share_expiry_date'] || empty($share_row['registration_share_expiry_date']))
                $view_data['share_row'] = $share_row;
        }
        $this->load->view('patient/register_form', $view_data);
    }
    public function rg($unique_id) {
        redirect('patient/register/' . $unique_id);
    }

    public function patient_register() {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('user_first_name', 'first name', 'required|trim');
        $this->form_validation->set_rules('user_last_name', 'last name', 'required|trim');
        $this->form_validation->set_rules('user_phone_number', 'phone number', 'required|trim|numeric|regex_match[/^[0-9]{10}$/]|callback_phone_exist');
        $this->form_validation->set_rules('user_email', 'email', 'trim|valid_email|callback_email_exist');
        $this->form_validation->set_rules('user_password', 'password', 'trim|required|min_length[6]|max_length[32]');
        $this->form_validation->set_rules('c_user_password', 'confirm password', 'trim|required|min_length[6]|max_length[32]|matches[user_password]');
        $this->form_validation->set_rules('date_of_birth', 'date of birth', 'required|trim');
        $this->form_validation->set_rules('gender', 'gender', 'required|trim');
        $this->form_validation->set_rules('terms_conditions', 'terms_conditions', 'required|trim');
        
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo json_encode(['status' => false,'error' => nl2br($errors)]);
        } else {
            $user_data = array(
                'user_first_name' => set_value('user_first_name'),
                'user_last_name' => set_value('user_last_name'),
                'user_phone_number' => set_value('user_phone_number'),
                'user_phone_verified' => 1,
                'user_email_verified' => 1,
                'user_gender' => set_value('gender'),
                'user_password' => password_hash(sha1(set_value('user_password')), PASSWORD_BCRYPT)    
            );
            
            $email = set_value('user_email');
            if(!empty($email))
                $user_data['user_email'] = $email;

            if(!empty($this->input->post('patient_id'))) {
                $user_data['user_modified_at'] = $this->utc_time_formated;
                $user_id = $this->input->post('patient_id');
                 $this->Common_model->update('me_users', $user_data, ['user_id' => $user_id]);
            } else {
                if($this->input->post('user_source_id')) {
                    $user_data['user_source_id'] = $this->input->post('user_source_id');
                }
                $unique_id = strtoupper($this->Common_model->escape_data(str_rand_access_token(8)));
                $user_data['user_unique_id'] = $unique_id;
                $user_data['user_plan_id'] = DEFAULT_USER_PLAN_ID;
                $user_data['user_created_at'] = $this->utc_time_formated;
                $user_id = $this->Common_model->insert('me_users', $user_data);
                $share_link_data = array(
                    'patient_id' => $user_id,
                    'is_set_password' => 1,
                    'created_at' => $this->utc_time_formated
                );
                $this->Common_model->update('me_users', ['user_created_by' => $user_id], ['user_id' => $user_id]);
                $this->Common_model->insert('me_patient_share_link_log', $share_link_data);
            }
            $address_data = array(
                'address_user_id' => $user_id,
                'address_type' => 1,
            );
            $where = array('address_user_id' => $user_id, 'address_type' => 1);
            $get_address_details = $this->Common_model->get_single_row('me_address', 'address_id', array('address_user_id' => $user_id, 'address_type' => 1));
            if (!empty($get_address_details)) {
                $address_data['address_modified_at'] = $this->utc_time_formated;
                $address_is_update = $this->Common_model->update('me_address', $address_data, array('address_id' => $get_address_details['address_id']));
            } else {
                $address_data['address_created_at'] = $this->utc_time_formated;
                $this->Common_model->insert('me_address', $address_data);
            }
            
            $arr = explode('/', set_value('date_of_birth'));
            $dob = NULL;
            if(count($arr) == 3)
                $dob = $arr[2].'-'.$arr[1].'-'.$arr[0];
            $user_details_data = array(
                'user_details_user_id' => $user_id,
                'user_details_dob' => $dob,
                'user_details_agree_medical_share' => ($user_data['user_gender'] == 'undisclosed') ? 1 : 2,
                'user_details_agree_terms' => set_value('terms_conditions')
            );
            $get_user_details = $this->Common_model->get_single_row('me_user_details', 'user_details_id', array('user_details_user_id' => $user_id));
            if (!empty($get_user_details)) {
                $user_details_data['user_details_modifed_at'] = $this->utc_time_formated;
                $this->Common_model->update('me_user_details', $user_details_data, array('user_details_user_id' => $user_id));
            } else {
                $user_details_data['user_details_created_at'] = $this->utc_time_formated;
                $this->Common_model->insert('me_user_details', $user_details_data);
            }
            if($this->input->post('share_link_id')) {
                $this->Common_model->update('me_patient_share_link_log', ['is_set_password' =>1, 'updated_at' => $this->utc_time_formated], array('id' => $this->input->post('share_link_id')));
            }
            if(empty($this->input->post('patient_id'))) {
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
                $this->Common_model->insert('me_settings', $insert_setting_array);
                $user_auth_insert_array = array(
                    'auth_user_id' => $user_id,
                    'auth_type' => 2,
                    'auth_code' => '',
                    'auth_otp_expiry_time' => '',
                    'auth_created_at' => $this->utc_time_formated
                );
                $this->Common_model->insert('me_auth', $user_auth_insert_array);
                $reset_token = str_rand_access_token(20);
                $user_auth_email_insert_array = array(
                    'auth_user_id' => $user_id,
                    'auth_type' => 1,
                    'auth_code' => $reset_token,
                    'auth_otp_expiry_time' => date('Y-m-d H:i:s', strtotime(OTP_EXPIRE_TOKEN_TIME, strtotime($this->utc_time_formated))),
                    'auth_created_at' => $this->utc_time_formated
                );
                $this->Common_model->insert('me_auth', $user_auth_email_insert_array);
                if(!empty($this->input->post('reg_share_id'))) {
                    $share_row = $this->Common_model->get_single_row('me_registration_share_link', 'registration_share_id,registration_share_social_media_id,registration_share_clinic_id,registration_share_doctor_id,', ['registration_share_status' => 1, 'registration_share_id' => $this->input->post('reg_share_id')]);
                    if(!empty($share_row['registration_share_id'])) {
                        $registration_log_data = [
                            'registration_log_share_id' => $share_row['registration_share_id'], 
                            'registration_log_patient_id' => $user_id,
                            'registration_log_created_at' => $this->utc_time_formated
                        ];
                        if(!empty($share_row['registration_share_doctor_id']))
                            $registration_log_data['registration_log_doctor_id'] = $share_row['registration_share_doctor_id'];
                        if(!empty($share_row['registration_share_social_media_id']))
                            $registration_log_data['registration_log_social_media_id'] = $share_row['registration_share_social_media_id'];
                        if(!empty($share_row['registration_share_clinic_id']))
                            $registration_log_data['registration_log_clinic_id'] = $share_row['registration_share_clinic_id'];
                        $this->Common_model->insert('me_registration_logs', $registration_log_data);
                    }
                }
                if(!empty($email)){
                    $send_notification_data = array(
                        'email' => $email,
                        'first_name' => $user_data['user_first_name'] . ' ' . $user_data['user_last_name'],
                        'password' => '',
                        'unique_id' => $unique_id,
                        'reset_token' => $reset_token
                    );
                    //We had change logic auto verified email
                    /*$cron_job_path = CRON_PATH . " notification/add_patient_email/" . base64_encode(json_encode($send_notification_data));
                    exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");*/
                }
            }
            $response = [];
            $response['status'] = true;
            $this->patient_auth->login([
                'user_id' => $user_id,
                'patient_name' => $user_data['user_first_name'] . ' ' . $user_data['user_last_name']
            ]);
            echo json_encode($response);
        }
    } 

    public function forgot_password() {
        $view_data = array();
        $view_data['breadcrumbs'] = "Forgot Password";
        $view_data['page_title'] = "Forgot Password";
        $this->form_validation->set_rules('phone_number', 'phone number', 'required|trim');
        if ($this->form_validation->run() !== FALSE) {
            $user_data = $this->patient->get_patient_by_phone(set_value('phone_number'));
            if(!empty($user_data)) {
                $reset_token = str_rand_access_token(20);
                $user_auth_insert_array = array(
                    'auth_user_id' => $user_data->user_id,
                    'auth_type' => 3,
                    'auth_code' => $reset_token,
                    'auth_otp_expiry_time' => date('Y-m-d H:i:s', strtotime(OTP_EXPIRE_TOKEN_TIME, strtotime($this->utc_time_formated))),
                    'auth_created_at' => $this->utc_time_formated
                );
                $this->Common_model->insert('me_auth', $user_auth_insert_array);
                $reset_password_url = DOMAIN_URL . "patient/resetpassword/" . $reset_token;
                $patient_name = $user_data->user_first_name . " " . $user_data->user_last_name;
                $send_message['phone_number'] = $user_data->user_phone_number;
                $send_message['message'] = sprintf(lang('forgot_password_sms_link'), $patient_name, $reset_password_url);
                $send_message['whatsapp_sms_body'] = sprintf(lang('wa_template_32_forgot_password_sms_link'), $patient_name, $reset_password_url);
                $send_message['is_not_check_setting_flag'] = true;
                $send_message['is_stop_sms'] = IS_STOP_SMS;
                $send_message['is_stop_whatsapp_sms'] = IS_STOP_WHATSAPP_SMS;
                $send_message['patient_id'] = $user_data->user_id;
                $send_message['user_type'] = 1;
                send_communication($send_message);
                $this->session->set_flashdata('message', 'Password reset instuctions has been sent to your registered mobile number.');
                redirect(site_url('patient/forgot'));
            } else {
                $view_data['errors'] = "<p>User not found</p>";
            }
        }
        $this->load->view('patient/forgot_password_form', $view_data);
    }

    public function reset_password($token = "") {
        $view_data = array();
        if (empty($token)) {
            $view_data['token_error'] = lang("resetpassword_token_empty");
        } else {
            $where = array();
            $where['auth_code'] = $token;
            $user_data = $this->Common_model->get_single_row(TBL_USER_AUTH, "auth_user_id,auth_otp_expiry_time", $where);
            if (empty($user_data)) {
                $view_data['token_error'] = lang("resetpassword_token_invalid");
            } else {
                if (strtotime($user_data['auth_otp_expiry_time']) >= time()) {
                    $this->form_validation->set_rules('userId', 'userId', 'trim|required');
                    $this->form_validation->set_rules('user_password', 'password', 'trim|required|min_length[6]|max_length[32]');
                    $this->form_validation->set_rules('c_user_password', 'confirm password', 'trim|required|min_length[6]|max_length[32]|matches[user_password]');
                    $view_data['userId'] = $user_data['auth_user_id'];
                    if ($this->form_validation->run() !== FALSE) {
                        $password = set_value('user_password');
                        $userId = set_value('userId');
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
                            $this->session->set_flashdata('message', lang("resetpassword_reset_success"));
                            redirect(site_url('patient/reset'));
                        } else {
                            $view_data['errors'] = lang("resetpassword_reset_fail");
                        }
                    }
                } else {
                    $view_data['token_error'] = lang("resetpassword_token_expire");
                }
            }
        }
        $view_data['breadcrumbs'] = "Reset Password";
        $view_data['page_title'] = "Reset Password";
        $this->load->view('patient/reset_password_form', $view_data);
    }

    public function reset_success() {
        $view_data = array();
        $view_data['breadcrumbs'] = "Reset Password Success";
        $view_data['page_title'] = "Reset Password Success";
        $this->load->view('patient/reset_password_success', $view_data);
    }
    public function phone_exist($phone) {
        if($this->patient->phone_exist($phone, $this->input->post('patient_id'))) {
            $this->form_validation->set_message('phone_exist', 'The {field} is already exist');
            return false;
        }
        return true;
    }

    public function email_exist($email) {
        if(!empty($email) && $this->patient->email_exist($email, $this->input->post('patient_id'))) {
            $this->form_validation->set_message('email_exist', 'The {field} is already exist');
            return false;
        }
        return true;
    }

}
