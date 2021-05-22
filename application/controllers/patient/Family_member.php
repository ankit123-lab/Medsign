<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Family_member extends MY_Controller {

    public function __construct() {

        parent::__construct();
        $this->load->library('patient_auth');
        $this->load->model('patient_model','patient');
        $this->load->library('form_validation');
        $this->load->library("pagination");
        if (!$this->patient_auth->is_logged_in()) {
            redirect('patient/login');
        }
    }

    public function add_member() {
        $view_data = array();
        $view_data['breadcrumbs'] = "Add Family Member";
        $view_data['page_title'] = "Add Family Member";
        $view_data['family_relation'] = get_relation();
        $user_id = $this->patient_auth->get_user_id();
        $this->form_validation->set_rules('user_id', 'user id', 'trim');
        $this->form_validation->set_rules('first_name', 'first name', 'required|trim');
        $this->form_validation->set_rules('last_name', 'last name', 'required|trim');
        $this->form_validation->set_rules('relation', 'relation', 'required|trim');
        $this->form_validation->set_rules('mobile_no', 'mobile no', 'trim|numeric|regex_match[/^[0-9]{10}$/]');
        if(!$this->input->post('user_id')) {
            $this->form_validation->set_rules('date_of_birth', 'date of birth', 'required|trim');
            $this->form_validation->set_rules('gender', 'gender', 'required|trim');
        }
        
        if ($this->form_validation->run() !== FALSE) {
            $mobile_no = set_value('mobile_no');
            if(!empty($mobile_no)) {
                $user_data = $this->patient->get_patient_by_phone($mobile_no);
            }
            if(!empty($user_data->user_id)) {
                $user_id = $user_data->user_id;
                $mapping = $this->Common_model->get_single_row('me_patient_family_member_mapping', 'patient_id', array('parent_patient_id' => $this->patient_auth->get_logged_user_id(), 'patient_id' => $user_id, 'mapping_status' => 1));
                $pt_name = $user_data->user_first_name . ' ' . $user_data->user_last_name;
            } else {
                $date = set_value('date_of_birth');
                $arr = explode('/', $date);
                if(count($arr) == 3) {
                    $date_of_birth = $arr[2] . "-" . $arr[1] . "-" . $arr[0];
                }
                $unique_id = strtoupper($this->Common_model->escape_data(str_rand_access_token(8)));
                $user_data = array(
                    'user_first_name' => set_value('first_name'),
                    'user_last_name' => set_value('last_name'),
                    'user_unique_id' => $unique_id,
                    'user_plan_id' => DEFAULT_USER_PLAN_ID,
                    'user_gender' => set_value('gender'),
                    'user_created_at' => $this->utc_time_formated,
                    'user_created_by' => $this->patient_auth->get_logged_user_id()
                );
                $pt_name = $user_data['user_first_name'] . ' ' . $user_data['user_last_name'];
                if(!empty($mobile_no)){
                    $user_data['user_phone_number'] = $mobile_no;
                    $user_data['user_phone_verified'] = 1;
                    $phone_number = $mobile_no;
                }
                $user_id = $this->Common_model->insert('me_users', $user_data);
                $user_details_data = array(
                    'user_details_user_id' => $user_id,
                    'user_details_dob' => $date_of_birth,
                    'user_details_agree_medical_share' => 2,
                    'user_details_created_at' => $this->utc_time_formated
                );
                $this->Common_model->insert('me_user_details', $user_details_data);
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
            }
            if(!empty($mapping) || $user_id == $this->patient_auth->get_logged_user_id()) {
                $view_data['errors'] = "This member is already added.";
            } else {
                $insert_caretaker_mapping = array(
                    'parent_patient_id' => $this->patient_auth->get_logged_user_id(),
                    'patient_id' => $user_id,
                    'mapping_status' => 1,
                    'mapping_relation' => set_value('relation'),
                    'created_at' => $this->utc_time_formated,
                    'created_by' => $this->patient_auth->get_logged_user_id()
                );
                $this->Common_model->insert('me_patient_family_member_mapping', $insert_caretaker_mapping);
                $caregiver_user = $this->Common_model->get_single_row('me_users', 'user_first_name, user_last_name,user_phone_number', ['user_id' => $this->patient_auth->get_logged_user_id()]);
                $caregiver_name = $caregiver_user['user_first_name'] . ' ' . $caregiver_user['user_last_name'];
                $caretaker_mobile = $caregiver_user['user_phone_number'];
                $global_setting_row = $this->Common_model->get_single_row('me_global_settings', 'global_setting_value', array('global_setting_name' => 'email_id'));
                $support_email = $global_setting_row['global_setting_value'];
                $message = sprintf(lang('caregiver_notification_sms'), $caregiver_name, $pt_name, trim(DOMAIN_URL, '/'), $support_email);
                $whatsapp_sms_body = sprintf(lang('wa_template_caregiver_has_shared_contacts_37'), $caregiver_name, $pt_name, trim(DOMAIN_URL, '/'), $support_email);
                $send_message = array(
                    'phone_number' => $caretaker_mobile,
                    'message' => $message,
                    'whatsapp_sms_body' => $whatsapp_sms_body,
                    'is_not_check_setting_flag' => true,
                    'is_stop_sms' => IS_STOP_SMS,
                    'is_stop_whatsapp_sms' => IS_STOP_WHATSAPP_SMS,
                    'patient_id' => $this->patient_auth->get_logged_user_id(),
                    'user_type' => 1
                );
                if(!empty($caretaker_mobile))
                    send_communication($send_message);

                $message = sprintf(lang('patient_has_shared_contacts'), $pt_name, $caregiver_name, trim(DOMAIN_URL, '/'), $support_email);
                $whatsapp_sms_body = sprintf(lang('wa_template_patient_has_shared_contacts_38'), $pt_name, $caregiver_name, trim(DOMAIN_URL, '/'), $support_email);
                $send_message = array(
                    'phone_number' => $mobile_no,
                    'message' => $message,
                    'whatsapp_sms_body' => $whatsapp_sms_body,
                    'is_not_check_setting_flag' => true,
                    'is_stop_sms' => IS_STOP_SMS,
                    'is_stop_whatsapp_sms' => IS_STOP_WHATSAPP_SMS,
                    'patient_id' => $user_id,
                    'user_type' => 1
                );
                if(!empty($mobile_no))
                    send_communication($send_message);
                $this->session->set_flashdata('message', 'Family member added successfully');
                redirect(site_url('patient/profile/update'));
            }
        }
        $view_data['user_id'] = set_value('user_id');
        $view_data['first_name'] = set_value('first_name');
        $view_data['last_name'] = set_value('last_name');
        $view_data['relation'] = set_value('relation');
        $view_data['mobile_no'] = set_value('mobile_no');
        $view_data['date_of_birth'] = set_value('date_of_birth');
        $view_data['gender'] = set_value('gender');
        $this->load->view('patient/family_member_add_view', $view_data);
    }

    public function remove_member($id) {
        $patient_id = encrypt_decrypt($id,'decrypt');
        $update_data = array(
            'mapping_status' => 9,
            'updated_by' => $this->patient_auth->get_logged_user_id(),
            'updated_at' => $this->utc_time_formated,
        );
        $update_where = array(
            'parent_patient_id' => $this->patient_auth->get_logged_user_id(),
            'patient_id' => $patient_id,
            'mapping_status' => 1
        );
        $this->Common_model->update('me_patient_family_member_mapping', $update_data, $update_where);
        if($patient_id == $this->patient_auth->get_user_id()) {
            $user = $this->Common_model->get_single_row('me_users', 'user_first_name, user_last_name', ['user_id' => $this->patient_auth->get_logged_user_id()]);
            $this->session->set_userdata(array(
                'patient_id' => $this->patient_auth->get_logged_user_id(),
                'patient_name' => $user['user_first_name'] . ' ' . $user['user_last_name']
            ));
        }
        $this->session->set_flashdata('message', 'Family member removed successfully');
        redirect(site_url('patient/profile/update'));
    }

    public function remove_member_with_save_detail() {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('member_id', 'member id', 'required|trim');
        $this->form_validation->set_rules('member_mobile_no', 'phone number', 'trim|numeric|regex_match[/^[0-9]{10}$/]|callback_phone_exist');
        $this->form_validation->set_rules('member_email', 'email', 'trim|valid_email|callback_email_exist');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo json_encode(['status' => false, 'error' => nl2br($errors)]);
            exit;
        } else {
            $member_id = encrypt_decrypt(set_value('member_id'),'decrypt');
            $member_mobile_no = set_value('member_mobile_no');
            $member_email = set_value('member_email');
            $update_user = [
                'user_phone_number' => $member_mobile_no,
                'user_phone_verified' => 1,
                'user_modified_at' => $this->utc_time_formated
            ];
            if(!empty($member_email)) {
                $update_user['user_email'] = $member_email;
                $update_user['user_email_verified'] = 1;
            }
            $this->Common_model->update('me_users', $update_user, ['user_id' => $member_id]);

            $update_data = array(
                'mapping_status' => 9,
                'updated_by' => $this->patient_auth->get_logged_user_id(),
                'updated_at' => $this->utc_time_formated,
            );
            $update_where = array(
                'parent_patient_id' => $this->patient_auth->get_logged_user_id(),
                'patient_id' => $member_id,
                'mapping_status' => 1
            );
            $this->Common_model->update('me_patient_family_member_mapping', $update_data, $update_where);
            echo json_encode(['status' => true, 'msg' => "Family member removed successfully"]);
            exit;
        }
    }

    public function get_member() {
        $response = [];
        $response['status'] = false;
        $mobile_no = trim($this->input->post('mobile_no'));
        if(!empty($mobile_no) && strlen($mobile_no) == 10) {
            $user = $this->Common_model->get_single_row('me_users', 'user_id,user_first_name, user_last_name', ['user_phone_number' => $mobile_no, 'user_type' => 1, 'user_status' => 1]);
            if(!empty($user)) {
                $response['user'] = $user;
                $response['status'] = true;
            }
        }
        echo json_encode($response);
    }

    public function phone_exist($phone) {
        $user_id = encrypt_decrypt($this->input->post('member_id'),'decrypt');
        if(!empty($phone) && $this->patient->phone_exist($phone, $user_id)) {
            $this->form_validation->set_message('phone_exist', 'The {field} is already exist');
            return false;
        }
        return true;
    }

    public function email_exist($email) {
        $user_id = encrypt_decrypt($this->input->post('member_id'),'decrypt');
        if(!empty($email) && $this->patient->email_exist($email, $user_id)) {
            $this->form_validation->set_message('email_exist', 'The {field} is already exist');
            return false;
        }
        return true;
    }

}