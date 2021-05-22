<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Patient_auth {

    private $error = array();

    function __construct() {
        $this->ci = & get_instance();
        $this->ci->load->library('session');
    }

    function login($data) {
        $access = $this->ci->Common_model->get_all_rows('me_doctors_global_setting', 'setting_name,setting_value', ['doctor_id' => $data['user_id'], 'setting_status' => 1]);
        $access_data = [];
        if(!empty($access) && count($access) > 0){
            $access_data = array_column($access, 'setting_value', 'setting_name');
        } else {
            $sub_setting_data = $this->ci->Common_model->get_all_rows('me_sub_setting_data', 'sub_setting_data_name,sub_setting_data_value', ['sub_setting_data_plan_id' => DEFAULT_USER_PLAN_ID]);
            $user_setting_data = [];
            foreach ($sub_setting_data as $key => $value) {
                $user_setting_data[] = [
                    'doctor_id' => $data['user_id'],
                    'sub_plan_id' => DEFAULT_USER_PLAN_ID,
                    'setting_name' => $value['sub_setting_data_name'],
                    'setting_value' => $value['sub_setting_data_value'],
                    'setting_status' => 1,
                    'created_at' => date("Y-m-d H:i:s"),
                    'created_by' => $data['user_id']
                ];
            }
            $this->ci->Common_model->insert_multiple('me_doctors_global_setting', $user_setting_data);
            $access_data = array_column($user_setting_data, 'setting_value', 'setting_name');
        }
        if(!empty($data['expiry_date']))
            $expiry_date = $data['expiry_date'];
        else
            $expiry_date = '';
    	$this->ci->session->set_userdata(array(
            'patient_id' => $data['user_id'],
            'patient_name' => $data['patient_name'],
            'logged_user_id' => $data['user_id'],
            'expiry_date' => $expiry_date,
            'access' => $access_data
        ));
        $this->ci->Common_model->update('me_users',['last_login_at' => date('Y-m-d H:i:s')],array("user_id" => $data['user_id']));
        $this->ci->load->model('Auditlog_model');
        $this->ci->Auditlog_model->create_audit_log($data['user_id'], 1, AUDIT_SLUG_ARR['LOGIN_ACTION']);
        return true;
    }

    function access_update($data, $expiry_date) {
        $access_data = array_column($data, 'setting_value', 'setting_name');
        $this->ci->session->set_userdata(array(
            'access' => $access_data,
            'expiry_date' => $expiry_date
        ));
    }

    function get_expiry_date() {
        return $this->ci->session->userdata('expiry_date');
    }

    function is_logged_in() {
        return !empty($this->ci->session->userdata('logged_user_id')) ? true : false;
    }

    function get_user_id() {
        return $this->ci->session->userdata('patient_id');
    }

    function get_logged_user_id() {
        return $this->ci->session->userdata('logged_user_id');
    }

    function get_patient_name() {
        return $this->ci->session->userdata('patient_name');
    }

    function get_access($key) {
        $access = $this->ci->session->userdata('access');
        $date = $this->get_expiry_date();
        if(!empty($access[$key]) || (!empty($date) && $date >= date("Y-m-d")))
            return true;
        else
            return false;
    }

    function logout() {
        $this->ci->load->model('Auditlog_model');
        $this->ci->Auditlog_model->create_audit_log($this->get_logged_user_id(), 1, AUDIT_SLUG_ARR['LOGOUT_ACTION']);
        $this->ci->session->sess_destroy();
    }
}