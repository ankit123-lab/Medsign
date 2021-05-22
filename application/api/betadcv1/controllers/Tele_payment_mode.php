<?php

Class Tele_payment_mode extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function get_tele_payment_mode_post() {
        try {
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? $this->Common_model->escape_data($this->post_data['clinic_id']) : '';
            
            if (empty($doctor_id) || empty($clinic_id)) {
                $this->bad_request();
            }
            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 45,
                    'key' => 3
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }
            $where = ['doctor_id' => $doctor_id, 'clinic_id' => $clinic_id];
            $doctor_payment_mode = $this->Common_model->doctor_payment_mode_link($where);
            if (!empty($doctor_payment_mode)) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $doctor_payment_mode;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('common_detail_not_found');
                $this->my_response['data'] = array();
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function add_tele_payment_mode_post() {
        try {
            $doctor_payment_mode_id = !empty($this->Common_model->escape_data($this->post_data['doctor_payment_mode_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_payment_mode_id']) : '';
            $master_id = !empty($this->Common_model->escape_data($this->post_data['master_id'])) ? $this->Common_model->escape_data($this->post_data['master_id']) : '';
            $upi_link = !empty($this->Common_model->escape_data($this->post_data['upi_link'])) ? $this->Common_model->escape_data($this->post_data['upi_link']) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? $this->Common_model->escape_data($this->post_data['clinic_id']) : '';
            $bank_name = !empty($this->Common_model->escape_data($this->post_data['bank_name'])) ? $this->Common_model->escape_data($this->post_data['bank_name']) : '';
            $bank_holder_name = !empty($this->Common_model->escape_data($this->post_data['bank_holder_name'])) ? $this->Common_model->escape_data($this->post_data['bank_holder_name']) : '';
            $ifsc_code = !empty($this->Common_model->escape_data($this->post_data['ifsc_code'])) ? $this->Common_model->escape_data($this->post_data['ifsc_code']) : '';
            $account_no = !empty($this->Common_model->escape_data($this->post_data['account_no'])) ? $this->Common_model->escape_data($this->post_data['account_no']) : '';
            if (empty($master_id) ||
                    (empty($upi_link) && $master_id != 5) || 
                    empty($doctor_id) || empty($clinic_id)
            ) {
                $this->bad_request();
            }
            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 45,
                    'key' => !empty($doctor_payment_mode_id) ? 2 : 1
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }
            if($master_id == 5) {
                if(empty($bank_name) || empty($bank_holder_name) || empty($ifsc_code) || empty($account_no)) {
                    $this->bad_request();
                }
                $bank_details = array(
                    'bank_name' => $bank_name,
                    'bank_holder_name' => $bank_holder_name,
                    'ifsc_code' => $ifsc_code,
                    'account_no' => $account_no,
                );
                $upi_link = json_encode($bank_details);
            }
            //check tax belongs to doctor id or not
            if(!empty($doctor_payment_mode_id)) {
                $requested_data = array(
                    'doctor_payment_mode_id' => $doctor_payment_mode_id,
                    'doctor_id' => $doctor_id
                );
                $this->check_tax_belongs($requested_data);
                $update_data = array(
                    'doctor_payment_mode_master_id' => $master_id,
                    'doctor_payment_mode_upi_link' => $upi_link,
                    'doctor_payment_mode_updated_at' => $this->utc_time_formated
                );
                $update_where = array(
                    'doctor_payment_mode_id' => $doctor_payment_mode_id
                );
                $is_update = $this->Common_model->update('me_doctor_payment_mode_link', $update_data, $update_where);
                $this->my_response['message'] = lang('payment_mode_updated');
            } else {
                $insert_data = array(
                    'doctor_payment_mode_doctor_id' => $doctor_id,
                    'doctor_payment_mode_clinic_id' => $clinic_id,
                    'doctor_payment_mode_master_id' => $master_id,
                    'doctor_payment_mode_upi_link' => $upi_link,
                    'doctor_payment_mode_created_at' => $this->utc_time_formated
                );
                $this->Common_model->insert('me_doctor_payment_mode_link', $insert_data);
                $this->my_response['message'] = lang('payment_mode_added');
            }
            $this->my_response['status'] = true;
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function delete_tele_payment_mode_post() {
        try {
            $doctor_payment_mode_id = !empty($this->Common_model->escape_data($this->post_data['doctor_payment_mode_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_payment_mode_id']) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            if (empty($doctor_payment_mode_id) ||
                    empty($doctor_id)
            ) {
                $this->bad_request();
            }
            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 45,
                    'key' => 4
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }
            //check tax belongs to doctor id or not
            $requested_data = array(
                'doctor_payment_mode_id' => $doctor_payment_mode_id,
                'doctor_id' => $doctor_id
            );
            $this->check_tax_belongs($requested_data);
            $update_data = array(
                'doctor_payment_mode_status' => 9,
                'doctor_payment_mode_updated_at' => $this->utc_time_formated
            );
            $update_where = array(
                'doctor_payment_mode_id' => $doctor_payment_mode_id
            );
            $is_update = $this->Common_model->update('me_doctor_payment_mode_link', $update_data, $update_where);
            if ($is_update > 0) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('payment_mode_deleted');
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }
    public function check_tax_belongs($requested_data) {
        $where_array = array(
            'doctor_payment_mode_status' => 1,
            'doctor_payment_mode_id' => $requested_data['doctor_payment_mode_id'],
            'doctor_payment_mode_doctor_id' => $requested_data['doctor_id']
        );
        $check_tax_belongs_doctor = $this->Common_model->get_single_row('me_doctor_payment_mode_link', 'doctor_payment_mode_id', $where_array);
        if (empty($check_tax_belongs_doctor)) {
            $this->my_response['status'] = false;
            $this->my_response['message'] = lang('mycontroller_invalid_request');
            $this->send_response();
        }
    }

    public function get_payment_mode_master_post() {
        try {
            $where = ['payment_mode_status' => 1];
            $columns = 'payment_mode_id,payment_mode_name';
            $payment_mode = $this->Common_model->get_all_rows('me_payment_mode_master', $columns, $where);
            $this->my_response['status'] = true;
            $this->my_response['message'] = lang('common_detail_found');
            $this->my_response['data'] = $payment_mode;
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }
}