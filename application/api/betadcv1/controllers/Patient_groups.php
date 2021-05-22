<?php

Class Patient_groups extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("patient_group_model", "patient_group");
        $this->load->model("User_model");
    }

    public function get_disease_by_patient_post() {
        try {
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            
            if (empty($doctor_id)) {
                $this->bad_request();
            }
            $rece = $this->User_model->get_doctor_receptionist($doctor_id, 'user_id');
            $user_source_id_arr = array_column($rece, 'user_id');
            array_push($user_source_id_arr, $doctor_id);
            $where = [
                'doctor_id' => $doctor_id,
                'user_source_id_arr' => $user_source_id_arr
            ];
            $diseases = $this->patient_group->get_disease_by_patient($where);
            if (!empty($diseases)) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $diseases;
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
    public function search_patient_group_post() {
        try {
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            $patient_gender = !empty($this->Common_model->escape_data($this->post_data['patient_gender'])) ? $this->Common_model->escape_data($this->post_data['patient_gender']) : '';
            $patient_age_group = !empty($this->Common_model->escape_data($this->post_data['patient_age_group'])) ? $this->Common_model->escape_data($this->post_data['patient_age_group']) : '';
            $patient_disease_ids = !empty($this->Common_model->escape_data($this->post_data['patient_disease_ids'])) ? $this->Common_model->escape_data($this->post_data['patient_disease_ids']) : '';
            $page = !empty($this->Common_model->escape_data($this->post_data['page'])) ? trim($this->Common_model->escape_data($this->post_data['page'])) : '1';
            $per_page = !empty($this->Common_model->escape_data($this->post_data['per_page'])) ? trim($this->Common_model->escape_data($this->post_data['per_page'])) : '20';

            if (empty($doctor_id) || (empty($patient_gender) && empty($patient_age_group) && empty($patient_disease_ids))) {
                $this->bad_request();
            }
            $rece = $this->User_model->get_doctor_receptionist($doctor_id, 'user_id');
            $user_source_id_arr = array_column($rece, 'user_id');
            array_push($user_source_id_arr, $doctor_id);
            $where = [
                'doctor_id' => $doctor_id,
                'user_source_id_arr' => $user_source_id_arr,
                'patient_gender' => $patient_gender,
                'patient_disease_ids' => $patient_disease_ids,
                'patient_age_group' => $patient_age_group
            ];
            $where['page'] = $page;
            $where['per_page'] = $per_page;
            $patient_groups = $this->patient_group->search_patient_group($where);
            $count = $this->patient_group->search_patient_group($where, true);
            if (!empty($patient_groups)) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $patient_groups;
                $this->my_response['count'] = $count;
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

    public function get_patient_groups_post() {
        try {
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            
            if (empty($doctor_id)) {
                $this->bad_request();
            }
            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 46,
                    'key' => 3
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }
            $rece = $this->User_model->get_doctor_receptionist($doctor_id, 'user_id');
            $user_source_id_arr = array_column($rece, 'user_id');
            array_push($user_source_id_arr, $doctor_id);
            $where = [
                'doctor_id' => $doctor_id,
                'user_source_id_arr' => $user_source_id_arr
            ];
            $patient_groups = $this->patient_group->get_patient_groups($where);
            if (!empty($patient_groups)) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $patient_groups;
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

    public function get_patient_group_members_post() {
        try {
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            $patient_group_id = !empty($this->Common_model->escape_data($this->post_data['patient_group_id'])) ? $this->Common_model->escape_data($this->post_data['patient_group_id']) : '';
            $page = !empty($this->Common_model->escape_data($this->post_data['page'])) ? trim($this->Common_model->escape_data($this->post_data['page'])) : '1';
            $per_page = !empty($this->Common_model->escape_data($this->post_data['per_page'])) ? trim($this->Common_model->escape_data($this->post_data['per_page'])) : '20';

            if (empty($doctor_id) || empty($patient_group_id)) {
                $this->bad_request();
            }
            $where = ['patient_group_id' => $patient_group_id];
            $where['page'] = $page;
            $where['per_page'] = $per_page;
            $patient_group_members = $this->patient_group->get_patient_group_members($where);
            $count = $this->patient_group->get_patient_group_members($where, true);
            if (!empty($patient_group_members)) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $patient_group_members;
                $this->my_response['count'] = $count;
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

    public function add_patient_group_post() {
        try {
            $patient_group_id = !empty($this->Common_model->escape_data($this->post_data['patient_group_id'])) ? $this->Common_model->escape_data($this->post_data['patient_group_id']) : '';
            $patient_group_title = !empty($this->Common_model->escape_data($this->post_data['patient_group_title'])) ? $this->Common_model->escape_data($this->post_data['patient_group_title']) : '';
            $patient_group_gender = !empty($this->Common_model->escape_data($this->post_data['patient_group_gender'])) ? $this->Common_model->escape_data($this->post_data['patient_group_gender']) : '';
            $patient_group_age = !empty($this->Common_model->escape_data($this->post_data['patient_age_group'])) ? $this->Common_model->escape_data($this->post_data['patient_age_group']) : '';
            $patient_disease_ids = !empty($this->Common_model->escape_data($this->post_data['patient_disease_ids'])) ? $this->Common_model->escape_data($this->post_data['patient_disease_ids']) : '';
            $select_all_patients = !empty($this->Common_model->escape_data($this->post_data['select_all_patients'])) ? $this->Common_model->escape_data($this->post_data['select_all_patients']) : '';
            $auto_added_patients = !empty($this->Common_model->escape_data($this->post_data['auto_added_patients'])) ? $this->Common_model->escape_data($this->post_data['auto_added_patients']) : '';
            $member_ids = !empty($this->Common_model->escape_data($this->post_data['member_ids'])) ? $this->Common_model->escape_data($this->post_data['member_ids']) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            if (empty($patient_group_title) ||
                    (empty($patient_disease_ids) && 
                    empty($patient_group_gender) && 
                    empty($patient_group_age)) || 
                    empty($doctor_id)
            ) {
                $this->bad_request();
            }
            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 46,
                    'key' => !empty($patient_group_id) ? 2 : 1
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }
            //check tax belongs to doctor id or not
            if(empty($patient_disease_ids))
                $patient_disease_ids = [];
            if(!empty($patient_group_id)) {
                $requested_data = array(
                    'patient_group_id' => $patient_group_id,
                    'doctor_id' => $doctor_id
                );
                $this->check_group_belongs($requested_data);
                $update_data = array(
                    'patient_group_disease_id' => (count($patient_disease_ids) > 0) ? implode(',', $patient_disease_ids) : NULL,
                    'patient_group_title' => $patient_group_title,
                    'patient_group_gender' => $patient_group_gender,
                    'patient_group_age' => $patient_group_age,
                    'patient_group_updated_at' => $this->utc_time_formated,
                    'patient_group_all_added' => !empty($select_all_patients) ? 1 : 0,
                    'patient_group_auto_added' => !empty($auto_added_patients) ? 1 : 0
                );
                $update_where = array(
                    'patient_group_id' => $patient_group_id
                );
                $is_update = $this->Common_model->update('me_patient_groups', $update_data, $update_where);
                $this->my_response['message'] = lang('patient_group_updated');
            } else {
                $insert_data = array(
                    'patient_group_doctor_id' => $doctor_id,
                    'patient_group_disease_id' => (count($patient_disease_ids) > 0) ? implode(',', $patient_disease_ids) : NULL,
                    'patient_group_title' => $patient_group_title,
                    'patient_group_gender' => $patient_group_gender,
                    'patient_group_age' => $patient_group_age,
                    'patient_group_created_at' => $this->utc_time_formated,
                    'patient_group_all_added' => !empty($select_all_patients) ? 1 : 0,
                    'patient_group_auto_added' => !empty($auto_added_patients) ? 1 : 0,
                );
                $patient_group_id = $this->Common_model->insert('me_patient_groups', $insert_data);
                $this->my_response['message'] = lang('patient_group_added');
            }
            if(empty($auto_added_patients)) {
                if(!empty($select_all_patients)) {
                    $rece = $this->User_model->get_doctor_receptionist($doctor_id, 'user_id');
                    $user_source_id_arr = array_column($rece, 'user_id');
                    array_push($user_source_id_arr, $doctor_id);
                    $where = [
                        'doctor_id' => $doctor_id,
                        'user_source_id_arr' => $user_source_id_arr,
                        'patient_gender' => $patient_group_gender,
                        'patient_disease_ids' => $patient_disease_ids,
                        'patient_age_group' => $patient_group_age
                    ];
                    $member_data = $this->patient_group->search_patient_group($where, true, true);
                    $member_ids = array_column($member_data, 'user_id');
                }
                if(!empty($member_ids) && !empty($patient_group_id)) {
                    $update_where = array(
                        'patient_group_member_patient_group_id' => $patient_group_id
                    );
                    $this->Common_model->delete_data('me_patient_group_members', $update_where);
                    $insert_member_data = [];
                    foreach ($member_ids as $key => $value) {
                        $insert_member_data[] = array(
                            'patient_group_member_patient_group_id' => $patient_group_id,
                            'patient_group_member_user_id' => $value,
                            'patient_group_member_created_at' => $this->utc_time_formated
                        );
                    }
                    $this->Common_model->insert_multiple('me_patient_group_members', $insert_member_data);
                }
            }
            $this->my_response['status'] = true;
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function delete_patient_group_post() {
        try {
            $patient_group_id = !empty($this->Common_model->escape_data($this->post_data['patient_group_id'])) ? $this->Common_model->escape_data($this->post_data['patient_group_id']) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            if (empty($patient_group_id) ||
                    empty($doctor_id)
            ) {
                $this->bad_request();
            }
            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 46,
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
                'patient_group_id' => $patient_group_id,
                'doctor_id' => $doctor_id
            );
            $this->check_group_belongs($requested_data);
            $update_data = array(
                'patient_group_status' => 9,
                'patient_group_updated_at' => $this->utc_time_formated
            );
            $update_where = array(
                'patient_group_id' => $patient_group_id
            );
            $is_update = $this->Common_model->update('me_patient_groups', $update_data, $update_where);
            if ($is_update > 0) {
                $update_where = array(
                    'patient_group_member_patient_group_id' => $patient_group_id
                );
                $this->Common_model->delete_data('me_patient_group_members', $update_where);
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('patient_group_deleted');
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
    public function check_group_belongs($requested_data) {
        $where_array = array(
            'patient_group_status' => 1,
            'patient_group_id' => $requested_data['patient_group_id'],
            'patient_group_doctor_id' => $requested_data['doctor_id']
        );
        $check_group_belongs_doctor = $this->Common_model->get_single_row('me_patient_groups', 'patient_group_id', $where_array);
        if (empty($check_group_belongs_doctor)) {
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