<?php

Class Notes extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Description :- This function is used to add the clinical notes doctor wise
     * 
     * @author Manish Ramnani 
     */
    public function add_clinical_notes_post() {

        try {

            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            $clinical_notes_title = !empty($this->Common_model->escape_data($this->post_data['clinical_notes_title'])) ? $this->Common_model->escape_data($this->post_data['clinical_notes_title']) : '';
            $clinical_notes_type = !empty($this->Common_model->escape_data($this->post_data['clinical_notes_type'])) ? $this->Common_model->escape_data($this->post_data['clinical_notes_type']) : '';

            if (empty($doctor_id) ||
                    empty($clinical_notes_title) ||
                    empty($clinical_notes_type)
            ) {
                $this->bad_request();
            }

            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 18,
                    'key' => 1
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }
            
            $clinical_notes_type_array = array(1, 2, 3, 4);

            if (!in_array($clinical_notes_type, $clinical_notes_type_array)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('mycontroller_invalid_request');
                $this->send_response();
            }

            $requested_data = array(
                'doctor_id' => $doctor_id,
                'clincal_notes_title' => $clinical_notes_title,
                'clinical_notes_type' => $clinical_notes_type
            );
            $this->check_clinical_name_exists($requested_data, 2);

            $insert_clinical_notes_array = array(
                'clinical_notes_catalog_type' => $clinical_notes_type,
                'clinical_notes_catalog_title' => $clinical_notes_title,
                'clinical_notes_catalog_doctor_id' => $doctor_id,
                'clinical_notes_catalog_created_at' => $this->utc_time_formated
            );

            $inserted_id = $this->Common_model->insert(TBL_CLINICAL_NOTES, $insert_clinical_notes_array);

            if ($inserted_id > 0) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('clinical_notes_added');
                $this->my_response['inserted_id'] = $inserted_id;
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

    /**
     * Description :- This function is used to edit the clinical notes doctor wise
     * 
     * @author Manish Ramnani 
     */
    public function edit_clinical_notes_post() {

        try {

            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            $clinical_notes_id = !empty($this->Common_model->escape_data($this->post_data['clinical_notes_id'])) ? $this->Common_model->escape_data($this->post_data['clinical_notes_id']) : '';
            $clinical_notes_title = !empty($this->Common_model->escape_data($this->post_data['clinical_notes_title'])) ? $this->Common_model->escape_data($this->post_data['clinical_notes_title']) : '';
            $clinical_notes_type = !empty($this->Common_model->escape_data($this->post_data['clinical_notes_type'])) ? $this->Common_model->escape_data($this->post_data['clinical_notes_type']) : '';

            if (empty($doctor_id) ||
                    empty($clinical_notes_title) ||
                    empty($clinical_notes_type) ||
                    empty($clinical_notes_id)
            ) {
                $this->bad_request();
            }
            
            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 18,
                    'key' => 2
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }
            
            $clinical_notes_type_array = array(1, 2, 3, 4);

            if (!in_array($clinical_notes_type, $clinical_notes_type_array)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('mycontroller_invalid_request');
                $this->send_response();
            }

            $requested_data = array(
                'doctor_id' => $doctor_id,
                'clincal_notes_title' => $clinical_notes_title,
                'clinical_notes_id' => $clinical_notes_id,
                'clinical_notes_type' => $clinical_notes_type
            );
            $this->check_clinical_name_exists($requested_data, 1);

            $clinical_data = array(
                'doctor_id' => $doctor_id,
                'clinical_notes_id' => $clinical_notes_id
            );
            $this->check_clinical_notes_belongs($clinical_data);

            $update_clinical_notes_array = array(
                'clinical_notes_catalog_type' => $clinical_notes_type,
                'clinical_notes_catalog_title' => $clinical_notes_title,
                'clinical_notes_catalog_updated_at' => $this->utc_time_formated
            );

            $update_clinical_note_where = array(
                'clinical_notes_catalog_id' => $clinical_notes_id
            );

            $is_update = $this->Common_model->update(TBL_CLINICAL_NOTES, $update_clinical_notes_array, $update_clinical_note_where);

            if ($is_update > 0) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('clinical_notes_updated');
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

    /**
     * Description :- This function is used to get the clinical notes based on the doctor id
     * 
     * @author Manish Ramnani
     * 
     */
    public function get_clinical_notes_post() {
        try {
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            $search = !empty($this->Common_model->escape_data($this->post_data['search'])) ? $this->Common_model->escape_data($this->post_data['search']) : '';
            $clinical_notes_type = !empty($this->Common_model->escape_data($this->post_data['clinical_notes_type'])) ? $this->Common_model->escape_data($this->post_data['clinical_notes_type']) : '';
            $flag = !empty($this->Common_model->escape_data($this->post_data['flag'])) ? $this->Common_model->escape_data($this->post_data['flag']) : '';

            if (empty($doctor_id)) {
                $this->bad_request();
            }

            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 18,
                    'key' => 3
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }
            
            $columns = 'clinical_notes_catalog_id, 
                        clinical_notes_catalog_type,
                        clinical_notes_catalog_doctor_id,
                        clinical_notes_catalog_title';

            $get_clinical_note_data_sql = " SELECT 
                                                " . $columns . " 
                                            FROM 
                                                " . TBL_CLINICAL_NOTES . " 
                                            WHERE
                                                (
                                                    clinical_notes_catalog_doctor_id = '" . $doctor_id . "'
                                                ";
            if (!empty($flag) && $flag == 1) {
                $get_clinical_note_data_sql .= " OR clinical_notes_catalog_doctor_id = 0 ";
            }
            $get_clinical_note_data_sql .= "    ) AND clinical_notes_catalog_status = 1 ";

            if (!empty($search)) {
                $get_clinical_note_data_sql .= " AND clinical_notes_catalog_title LIKE '%" . $search . "%' ";
            }

            if (!empty($clinical_notes_type)) {
                $get_clinical_note_data_sql .= " AND clinical_notes_catalog_type = '" . $clinical_notes_type . "' ";
            }

            if (!empty($flag) && $flag == 1) {
                $get_clinical_note_data_sql .= " ORDER BY clinical_notes_catalog_doctor_id DESC LIMIT 0, " . RECORDS_LIMIT . " ";
            }
            
            
            $get_clinical_note_data = $this->Common_model->get_all_rows_by_query($get_clinical_note_data_sql);

            if (!empty($get_clinical_note_data)) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $get_clinical_note_data;
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

    /**
     * Description :- This function is used to delete the clinical notes of the doctor
     * 
     * @author Manish Ramnani
     * 
     */
    public function delete_clinical_notes_post() {

        try {
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            $clinical_notes_id = !empty($this->Common_model->escape_data($this->post_data['clinical_notes_id'])) ? $this->Common_model->escape_data($this->post_data['clinical_notes_id']) : '';

            if (empty($clinical_notes_id) ||
                    empty($doctor_id)
            ) {
                $this->bad_request();
            }
            
            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 18,
                    'key' => 4
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }
            
            $requested_data = array(
                'doctor_id' => $doctor_id,
                'clinical_notes_id' => $clinical_notes_id
            );
            $this->check_clinical_notes_belongs($requested_data);

            $update_clinical_notes_data = array(
                'clinical_notes_catalog_status' => 9,
                'clinical_notes_catalog_updated_at' => $this->utc_time_formated
            );

            $update_clinical_notes_where = array(
                'clinical_notes_catalog_id' => $clinical_notes_id
            );

            $is_update = $this->Common_model->update(TBL_CLINICAL_NOTES, $update_clinical_notes_data, $update_clinical_notes_where);

            if ($is_update > 0) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('clinical_notes_deleted');
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

    /**
     * Description :- This function is used to check the pricing name exists or not for same doctor
     * 
     * @author Manish Ramnani
     * 
     * @param type $requested_data
     * @param type $is_edit
     */
    public function check_clinical_name_exists($requested_data, $is_edit = 2) {

        $where_array = array(
            'clinical_notes_catalog_doctor_id' => $requested_data['doctor_id'],
            'clinical_notes_catalog_title' => $requested_data['clincal_notes_title'],
            'clinical_notes_catalog_status' => 1,
            'clinical_notes_catalog_type' => $requested_data['clinical_notes_type']
        );

        if ($is_edit == 1) {
            $where_array['clinical_notes_catalog_id !='] = $requested_data['clinical_notes_id'];
        }

        $get_clinical_notes_data = $this->Common_model->get_single_row(TBL_CLINICAL_NOTES, 'clinical_notes_catalog_id', $where_array);

        if (!empty($get_clinical_notes_data)) {
            $this->my_response['status'] = false;
            $this->my_response['message'] = lang('clinical_notes_already_exists');
            $this->send_response();
        }
    }

    /**
     * Description :- This function is used to pricing belongs to the particular doctor or not
     * 
     * @author Manish Ramnani
     * 
     * @param type $requested_data
     */
    public function check_clinical_notes_belongs($requested_data) {

        $where_array = array(
            'clinical_notes_catalog_id' => $requested_data['clinical_notes_id'],
            'clinical_notes_catalog_doctor_id' => $requested_data['doctor_id'],
            'clinical_notes_catalog_status' => 1
        );

        $get_clinical_notes_data = $this->Common_model->get_single_row(TBL_CLINICAL_NOTES, 'clinical_notes_catalog_id', $where_array);

        if (empty($get_clinical_notes_data)) {
            $this->my_response['status'] = false;
            $this->my_response['message'] = lang('mycontroller_invalid_request');
            $this->send_response();
        }
    }

}
