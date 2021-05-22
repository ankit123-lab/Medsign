<?php

Class Patientgroup extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function get_patient_group_post() {
        try {

            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';

            $smart_patient_group = array();

            $get_patient_details_query = "SELECT
                                                COUNT(user_id) as all_patient,
                                                COUNT(CASE WHEN user_gender = 'male' THEN 1 END) male,
                                                COUNT(CASE WHEN user_gender = 'female' THEN 1 END) female, 
                                                COUNT(CASE WHEN user_gender = 'other' THEN 1 END) other, 
                                                COUNT(CASE WHEN user_gender = 'undisclosed' THEN 1 END) undisclosed  
                                          FROM
                                             " . TBL_USERS . "
                                          LEFT JOIN
                                            " . TBL_USER_DETAILS . " ON user_id = user_details_user_id
                                          LEFT JOIN
                                             " . TBL_APPOINTMENTS . " ON user_id = appointment_user_id
                                          WHERE 
                                                user_status = 1 
                                          AND
                                                appointment_doctor_user_id = '" . $doctor_id . "'
                                          AND
                                                appointment_status != 9 ";

            $get_patient_details_data = $this->Common_model->get_single_row_by_query($get_patient_details_query);


            //get the doctor group
            $patient_group_columns = "patient_group_title, 
                                      patient_group_gender, 
                                      patient_group_age";

            $get_doctor_group_query = "SELECT  
                                            " . $patient_group_columns . " 
                                       FROM 
                                            " . TBL_PATIENT_GROUP . " 
                                       WHERE
                                            patient_group_doctor_id  = '" . $doctor_id . "' 
                                       AND 
                                            patient_group_status = 1  ";
            



            $smart_group = array(
                'male' => 'All male patients',
                'female' => 'All female patients',
                'other' => 'All other patients',
                'undisclosed' => 'All undisclosed',
                'all_patient' => 'All patient'
            );

            if (!empty($get_patient_details_data)) {
                foreach ($smart_group as $key => $group) {
                    $smart_patient_group[] = array(
                        "text" => $group,
                        "value" => $get_patient_details_data[$key]
                    );
                }

                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = array();
                $this->my_response['data']['smart_group'] = $smart_patient_group;
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

    public function add_patient_group_post() {
        try {

            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $group_name = !empty($this->Common_model->escape_data($this->post_data['group_name'])) ? trim($this->Common_model->escape_data($this->post_data['group_name'])) : '';
            $disease_id = !empty($this->Common_model->escape_data($this->post_data['disease_id'])) ? trim($this->Common_model->escape_data($this->post_data['disease_id'])) : '';
            $gender = !empty($this->Common_model->escape_data($this->post_data['gender'])) ? trim($this->Common_model->escape_data($this->post_data['gender'])) : '';
            $age = !empty($this->Common_model->escape_data($this->post_data['age'])) ? trim($this->Common_model->escape_data($this->post_data['age'])) : '';

            if (!in_array($gender, $this->gender_array)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('mycontroller_invalid_request');
                $this->send_response();
            }

            if (!is_numeric($disease_id) || !is_numeric($doctor_id)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('mycontroller_invalid_request');
                $this->send_response();
            }

            if (!in_array($age, range(1, 6))) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('mycontroller_invalid_request');
                $this->send_response();
            }

            //check same name exists
            $name_array = array(
                'doctor_id' => $doctor_id,
                'group_name' => $group_name
            );
            $this->check_same_name_exists($name_array, 2);

            $add_patient_group = array(
                'patient_group_doctor_id' => $doctor_id,
                'patient_group_disease_id' => $disease_id,
                'patient_group_title' => $group_name,
                'patient_group_gender' => $gender,
                'patient_group_age' => $age,
                'patient_group_created_at' => $this->utc_time_formated
            );

            $insert_group = $this->Common_model->insert(TBL_PATIENT_GROUP, $add_patient_group);

            if ($insert_group > 0) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('patient_group_added');
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

    public function edit_patient_group_post() {
        try {

            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $group_name = !empty($this->Common_model->escape_data($this->post_data['group_name'])) ? trim($this->Common_model->escape_data($this->post_data['group_name'])) : '';
            $disease_id = !empty($this->Common_model->escape_data($this->post_data['disease_id'])) ? trim($this->Common_model->escape_data($this->post_data['disease_id'])) : '';
            $gender = !empty($this->Common_model->escape_data($this->post_data['gender'])) ? trim($this->Common_model->escape_data($this->post_data['gender'])) : '';
            $age = !empty($this->Common_model->escape_data($this->post_data['age'])) ? trim($this->Common_model->escape_data($this->post_data['age'])) : '';
            $patient_group_id = !empty($this->Common_model->escape_data($this->post_data['patient_group_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_group_id'])) : '';

            if (!in_array($gender, $this->gender_array)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('mycontroller_invalid_request');
                $this->send_response();
            }

            if (!is_numeric($disease_id) || !is_numeric($doctor_id)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('mycontroller_invalid_request');
                $this->send_response();
            }

            if (!in_array($age, range(1, 6))) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('mycontroller_invalid_request');
                $this->send_response();
            }


            //check same name exists
            $name_array = array(
                'doctor_id' => $doctor_id,
                'group_name' => $group_name,
                'patient_group_id' => $patient_group_id
            );
            $this->check_same_name_exists($name_array, 1);

            //check group belongs to the doctor or not
            $requested_data = array(
                'doctor_id' => $doctor_id,
                'patient_group_id' => $patient_group_id
            );
            $this->check_patient_group_belongs($requested_data);

            $update_group_data = array(
                'patient_group_disease_id' => $disease_id,
                'patient_group_title' => $group_name,
                'patient_group_gender' => $gender,
                'patient_group_age' => $age,
                'patient_group_updated_at' => $this->utc_time_formated
            );

            $update_group_where = array(
                'patient_group_id' => $patient_group_id
            );

            $is_update = $this->Common_model->update(TBL_PATIENT_GROUP, $update_group_data, $update_group_where);

            if ($is_update > 0) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('patient_group_updated');
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

    public function delete_patient_group_post() {

        try {

            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $patient_group_id = !empty($this->Common_model->escape_data($this->post_data['patient_group_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_group_id'])) : '';

            if (!is_numeric($patient_group_id) || !is_numeric($doctor_id)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('mycontroller_invalid_request');
                $this->send_response();
            }

            //check group belongs to the doctor or not
            $requested_data = array(
                'doctor_id' => $doctor_id,
                'patient_group_id' => $patient_group_id
            );

            $this->check_patient_group_belongs($requested_data);

            $update_group_data = array(
                'patient_group_updated_at' => $this->utc_time_formated,
                'patient_group_status' => 9
            );

            $update_group_where = array(
                'patient_group_id' => $patient_group_id
            );

            $is_update = $this->Common_model->update(TBL_PATIENT_GROUP, $update_group_data, $update_group_where);

            if ($is_update > 0) {
                $this->my_response['status'] = false;
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

    public function check_patient_group_belongs($requested_data) {

        $where_array = array(
            'patient_group_id' => $requested_data['patient_group_id'],
            'patient_group_doctor_id' => $requested_data['doctor_id']
        );

        $get_patient_group = $this->Common_model->get_single_row(TBL_PATIENT_GROUP, 'patient_group_id', $where_array);

        if (empty($get_patient_group)) {
            $this->my_response['status'] = false;
            $this->my_response['message'] = lang('mycontroller_invalid_request');
            $this->send_response();
        }
    }

    public function check_same_name_exists($requested_data, $is_edit = 2) {

        $where_array = array(
            'patient_group_title' => $requested_data['group_name'],
            'patient_group_doctor_id' => $requested_data['doctor_id']
        );

        if ($is_edit == 1) {
            $where_array['patient_group_id !='] = $requested_data['patient_group_id'];
        }


        $get_patient_group = $this->Common_model->get_single_row(TBL_PATIENT_GROUP, 'patient_group_id', $where_array);

        if (!empty($get_patient_group)) {
            $this->my_response['status'] = false;
            $this->my_response['message'] = lang('patient_group_exists');
            $this->send_response();
        }
    }

}
