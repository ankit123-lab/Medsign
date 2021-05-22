<?php

Class Template extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Descriptoin :- This function is used to add the template name
     * 
     * 
     * 
     */
    public function add_template_post() {

        try {

            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $diagnosis_name = !empty($this->Common_model->escape_data($this->post_data['diagnosis_name'])) ? trim($this->Common_model->escape_data($this->post_data['diagnosis_name'])) : '';
            $template_name = !empty($this->Common_model->escape_data($this->post_data['template_name'])) ? trim($this->Common_model->escape_data($this->post_data['template_name'])) : '';
            $clinical_notes = !empty($this->post_data['clinical_notes']) ? $this->post_data['clinical_notes'] : '';
            $investigation_name = !empty($this->post_data['investigation_name']) ? $this->post_data['investigation_name'] : '';
            $investigation_instruction = !empty($this->Common_model->escape_data($this->post_data['investigation_instruction'])) ? trim($this->Common_model->escape_data($this->post_data['investigation_instruction'])) : '';
            $drug_data = !empty($this->post_data['drug_data']) ? $this->post_data['drug_data'] : '';

            $insert_template_binding_array = array();

            if (empty($diagnosis_name) ||
                    empty($template_name) ||
                    empty($doctor_id)
            ) {
                $this->bad_request();
            }
            
            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 20,
                    'key' => 1
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
                'template_name' => $template_name
            );
            $this->check_template_name_exists($requested_data, 2);

            $clinical_notes = json_decode($clinical_notes, true);

            $clinical_notes_id = array();
            if (!empty($clinical_notes) && count($clinical_notes) > 0 && is_array($clinical_notes)) {
                foreach ($clinical_notes as $notes) {
                    $id = '';

                    //check already clinical notes exists or not
                    $requested_data = array(
                        'doctor_id' => $doctor_id,
                        'clincal_notes_title' => $notes['search']
                    );

                    //check already clinical notes exists or not
                    $requested_data = array(
                        'doctor_id' => $doctor_id,
                        'clincal_notes_title' => $notes['search'],
                        'clinical_notes_type' => $notes['clinical_notes_type']
                    );
                    $check_exists = $this->check_clinical_name_exists($requested_data, 2);

                    if (!empty($check_exists)) {
                        $inserted_id = $check_exists;
                    } else {
                        $insert_array = array(
                            'clinical_notes_catalog_type' => $notes['clinical_notes_type'],
                            'clinical_notes_catalog_title' => $notes['search'],
                            'clinical_notes_catalog_doctor_id' => $doctor_id,
                            'clinical_notes_catalog_created_at' => $this->utc_time_formated
                        );
                        $inserted_id = $this->Common_model->insert(TBL_CLINICAL_NOTES, $insert_array);
                    }

                    $clinical_notes_id[] = $inserted_id;
                }
            }

            $this->db->trans_start();

            $insert_template_array = array(
                'template_diagnosis_name' => $diagnosis_name,
                'template_template_name' => $template_name,
                'template_doctor_id' => $doctor_id,
                'template_clinical_notes_id' => implode(',', $clinical_notes_id),
                'template_investigation_name' => $investigation_name,
                'template_investigation_instruction' => $investigation_instruction,
                'template_created_at' => $this->utc_time_formated
            );

            $insert_template = $this->Common_model->insert(TBL_TEMPLATE, $insert_template_array);

            if ($insert_template > 0) {
                if (!empty($drug_data)) {
                    $drug_data = json_decode($drug_data, true);
                    if (is_array($drug_data) && count($drug_data) > 0) {
                        foreach ($drug_data as $drug) {
                            $insert_template_binding_array[] = array(
                                'template_drug_binding_template_id' => $insert_template,
                                'template_drug_binding_drug_id' => $drug['drug_id'],
                                'template_drug_binding_intake_instruction' => !empty($drug['drug_intake_instruction']) ? trim($drug['drug_intake_instruction']) : '',
                                'template_drug_binding_dosage' => !empty($drug['drug_dosage']) ? trim($drug['drug_dosage']) : '',
                                'template_drug_binding_frequency_instruction' => !empty($drug['drug_frequency_instruction']) ? trim($drug['drug_frequency_instruction']) : '',
                                'template_drug_binding_custom_frequency' => !empty($drug['drug_custom_frequency']) ? trim($drug['drug_custom_frequency']) : '',
                                'template_drug_binding_duration_value' => !empty($drug['drug_duration_value']) ? trim($drug['drug_duration_value']) : '',
                                'template_drug_binding_duration' => !empty($drug['drug_duration']) ? trim($drug['drug_duration']) : '',
                                'template_drug_binding_intake' => !empty($drug['drug_intake']) ? trim($drug['drug_intake']) : '',
                                'template_drug_binding_frequency' => !empty($drug['drug_frequency']) ? trim($drug['drug_frequency']) : '',
                                'template_drug_binding_diet_instruction' => !empty($drug['drug_diet_instruction']) ? trim($drug['drug_diet_instruction']) : '',
                                'template_drug_binding_created_at' => $this->utc_time_formated
                            );
                        }
                        $this->Common_model->insert_multiple(TBL_TEMPLATE_DRUG, $insert_template_binding_array);
                    }
                }
            }
            if ($this->db->trans_status() !== FALSE) {
                $this->db->trans_commit();
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('template_added');
            } else {
                $this->db->trans_rollback();
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
     * Description :- This function is used to get the template of the doctor
     * 
     * 
     * 
     */
    public function get_template_post() {

        try {
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $search = !empty($this->Common_model->escape_data($this->post_data['search'])) ? trim($this->Common_model->escape_data($this->post_data['search'])) : '';
            $page = !empty($this->Common_model->escape_data($this->post_data['page'])) ? trim($this->Common_model->escape_data($this->post_data['page'])) : '1';
            $per_page = !empty($this->Common_model->escape_data($this->post_data['per_page'])) ? trim($this->Common_model->escape_data($this->post_data['per_page'])) : '10';
            if (empty($doctor_id)) {
                $this->bad_request();
            }
            
            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 20,
                    'key' => 3
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }
            
            $where = [
                'doctor_id' => $doctor_id,
                'search' => $search,
                'page' => $page,
                'per_page' => $per_page
            ];
            $this->load->model("Doctor_model", "doctor");
            $get_template_data = $this->doctor->get_ds_template($where);
            $count = $this->doctor->get_ds_template($where, true);
            if (!empty($get_template_data)) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $get_template_data;
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

    /**
     * Description :- This function is used to delete the template of the doctor
     * 
     * 
     * 
     */
    public function delete_template_post() {

        try {
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $template_id = !empty($this->Common_model->escape_data($this->post_data['template_id'])) ? trim($this->Common_model->escape_data($this->post_data['template_id'])) : '';

            if (empty($doctor_id) ||
                    empty($template_id)
            ) {
                $this->bad_request();
            }
            
            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 20,
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
                'template_id' => $template_id
            );
            $this->check_template_belongs($requested_data);

            $update_template_data = array(
                'template_updated_at' => $this->utc_time_formated,
                'template_status' => 9
            );

            $update_template_where = array(
                'template_id' => $template_id
            );

            $is_update = $this->Common_model->update(TBL_TEMPLATE, $update_template_data, $update_template_where);

            if ($is_update > 0) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('template_deleted');
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
     * Description :- This function is used to edit the template
     * 
     * 
     * 
     */
    public function edit_template_post() {

        try {

            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $template_id = !empty($this->Common_model->escape_data($this->post_data['template_id'])) ? trim($this->Common_model->escape_data($this->post_data['template_id'])) : '';
            $diagnosis_name = !empty($this->Common_model->escape_data($this->post_data['diagnosis_name'])) ? trim($this->Common_model->escape_data($this->post_data['diagnosis_name'])) : '';
            $template_name = !empty($this->Common_model->escape_data($this->post_data['template_name'])) ? trim($this->Common_model->escape_data($this->post_data['template_name'])) : '';
            $clinical_notes = !empty($this->post_data['clinical_notes']) ? $this->post_data['clinical_notes'] : '';
            $investigation_name = !empty($this->post_data['investigation_name']) ? $this->post_data['investigation_name'] : '';
            $investigation_instruction = !empty($this->Common_model->escape_data($this->post_data['investigation_instruction'])) ? trim($this->Common_model->escape_data($this->post_data['investigation_instruction'])) : '';
            $drug_data = !empty($this->post_data['drug_data']) ? $this->post_data['drug_data'] : '';

            $insert_template_binding_array = array();

            if (empty($diagnosis_name) ||
                    empty($template_name) ||
                    empty($doctor_id) ||
                    empty($template_id)
            ) {
                $this->bad_request();
            }
            
            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 20,
                    'key' => 2
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }
            
            $template_belongs = array(
                'doctor_id' => $doctor_id,
                'template_id' => $template_id
            );
            $this->check_template_belongs($template_belongs);

            $requested_data = array(
                'doctor_id' => $doctor_id,
                'template_name' => $template_name,
                'template_id' => $template_id
            );
            $this->check_template_name_exists($requested_data, 1);

            $clinical_notes = json_decode($clinical_notes, true);

            $clinical_notes_id = array();
            if (!empty($clinical_notes) && count($clinical_notes) > 0 && is_array($clinical_notes)) {
                foreach ($clinical_notes as $notes) {
                    $id = '';

                    //check already clinical notes exists or not
                    $requested_data = array(
                        'doctor_id' => $doctor_id,
                        'clincal_notes_title' => $notes['search'],
                        'clinical_notes_type' => $notes['clinical_notes_type']
                    );
                    $check_exists = $this->check_clinical_name_exists($requested_data, 2);

                    if (!empty($check_exists)) {
                        $inserted_id = $check_exists;
                    } else {
                        $insert_array = array(
                            'clinical_notes_catalog_type' => $notes['clinical_notes_type'],
                            'clinical_notes_catalog_title' => $notes['search'],
                            'clinical_notes_catalog_doctor_id' => $doctor_id,
                            'clinical_notes_catalog_created_at' => $this->utc_time_formated
                        );
                        $inserted_id = $this->Common_model->insert(TBL_CLINICAL_NOTES, $insert_array);
                    }

                    $clinical_notes_id[] = $inserted_id;
                }
            }


            $this->db->trans_start();

            $update_template_data = array(
                'template_diagnosis_name' => $diagnosis_name,
                'template_template_name' => $template_name,
                'template_doctor_id' => $doctor_id,
                'template_clinical_notes_id' => implode(',', $clinical_notes_id),
                'template_investigation_name' => $investigation_name,
                'template_investigation_instruction' => $investigation_instruction,
                'template_created_at' => $this->utc_time_formated
            );

            $update_template_where = array(
                'template_id' => $template_id
            );

            $update_template = $this->Common_model->update(TBL_TEMPLATE, $update_template_data, $update_template_where);

            if ($update_template > 0) {
                if (!empty($drug_data)) {
                    $drug_data = json_decode($drug_data, true);
                    if (is_array($drug_data) && count($drug_data) > 0) {

                        foreach ($drug_data as $drug) {

                            $insert_template_binding_array[] = array(
                                'template_drug_binding_template_id' => $template_id,
                                'template_drug_binding_drug_id' => $drug['drug_id'],
                                'template_drug_binding_intake_instruction' => !empty($drug['drug_intake_instruction']) ? trim($drug['drug_intake_instruction']) : '',
                                'template_drug_binding_dosage' => !empty($drug['drug_dosage']) ? trim($drug['drug_dosage']) : '',
                                'template_drug_binding_frequency_instruction' => !empty($drug['drug_frequency_instruction']) ? trim($drug['drug_frequency_instruction']) : '',
                                'template_drug_binding_custom_frequency' => !empty($drug['drug_custom_frequency']) ? trim($drug['drug_custom_frequency']) : '',
                                'template_drug_binding_duration_value' => !empty($drug['drug_duration_value']) ? trim($drug['drug_duration_value']) : '',
                                'template_drug_binding_duration' => !empty($drug['drug_duration']) ? trim($drug['drug_duration']) : '',
                                'template_drug_binding_intake' => !empty($drug['drug_intake']) ? trim($drug['drug_intake']) : '',
                                'template_drug_binding_frequency' => !empty($drug['drug_frequency']) ? trim($drug['drug_frequency']) : '',
                                'template_drug_binding_diet_instruction' => !empty($drug['drug_diet_instruction']) ? trim($drug['drug_diet_instruction']) : '',
                                'template_drug_binding_created_at' => $this->utc_time_formated
                            );
                        }

                        $update_template_drug = array(
                            'template_drug_binding_status' => 9,
                            'template_drug_binding_updated_at' => $this->utc_time_formated
                        );

                        $update_template_where = array(
                            'template_drug_binding_template_id' => $template_id
                        );

                        $this->Common_model->update(TBL_TEMPLATE_DRUG, $update_template_drug, $update_template_where);
                        $this->Common_model->insert_multiple(TBL_TEMPLATE_DRUG, $insert_template_binding_array);
                    }
                }
            }

            if ($this->db->trans_status() !== FALSE) {
                $this->db->trans_commit();
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('template_updated');
            } else {
                $this->db->trans_rollback();
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
     * Description :- This function is used to get the detail of the template
     * 
     * 
     * 
     */
    public function get_template_detail_post() {


        try {
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $template_id = !empty($this->Common_model->escape_data($this->post_data['template_id'])) ? trim($this->Common_model->escape_data($this->post_data['template_id'])) : '';

            if (empty($doctor_id) ||
                    empty($template_id)
            ) {
                $this->bad_request();
            }

            $requested_data = array(
                'doctor_id' => $doctor_id,
                'template_id' => $template_id
            );
            $this->check_template_belongs($requested_data);

            $template_columns = 'template_diagnosis_name, 
                                 template_template_name,
                                 template_investigation_name,
                                 template_investigation_instruction';

            $template_where = array(
                'template_id' => $template_id
            );

            $get_template_detail = $this->Common_model->get_single_row(TBL_TEMPLATE, $template_columns, $template_where);


            //get clinical notes array
            $get_clinical_notes_query = " SELECT 
                                                clinical_notes_catalog_id,
                                                clinical_notes_catalog_type,
                                                clinical_notes_catalog_title
                                          FROM 
                                                " . TBL_TEMPLATE . " 
                                          LEFT JOIN
                                                " . TBL_CLINICAL_NOTES . " ON FIND_IN_SET(clinical_notes_catalog_id, template_clinical_notes_id)
                                          WHERE  
                                                template_id = '" . $template_id . "' 
                                          AND 
                                                clinical_notes_catalog_status=1
                                          AND 
                                                template_clinical_notes_id != '' ";

            $get_clinical_notes = $this->Common_model->get_all_rows_by_query($get_clinical_notes_query);

            //get the rx data
            $rx_column = 'template_drug_binding_drug_id as drug_binding_id,
                          template_drug_binding_duration_value as duration_value,
                          template_drug_binding_duration as duration,
                          template_drug_binding_intake as intake,
                          template_drug_binding_intake_instruction as intake_instruction,
                          template_drug_binding_dosage as dosage,
                          template_drug_binding_frequency as frequency,
                          template_drug_binding_custom_frequency as custom_frequency,
                          template_drug_binding_frequency_instruction as frequency_instruction,
                          template_drug_binding_diet_instruction as diet_instruction,
                          drug_id,
                          drug_name,
						  drug_name_with_unit,
                          drug_drug_generic_id as generic_id,
                          drug_unit_id,
                          drug_unit_name,
                          GROUP_CONCAT(DISTINCT(drug_generic_title)) as generic_title';

            $get_drug_data_query = " SELECT 
                                        " . $rx_column . " 
                                   FROM 
                                        " . TBL_TEMPLATE_DRUG . "
                                   LEFT JOIN
                                        " . TBL_DRUGS . " ON template_drug_binding_drug_id = drug_id
                                    LEFT JOIN
                                    " . TBL_DRUG_UNIT . " 
                                        ON 
                                            drug_drug_unit_id = drug_unit_id AND drug_unit_status = 1
                                   LEFT JOIN
                                        " . TBL_DRUG_GENERIC . " ON FIND_IN_SET(drug_generic_id, drug_drug_generic_id)
                                   WHERE
                                        template_drug_binding_status = 1  AND 
                                        template_drug_binding_template_id = " . $template_id . "
                                   GROUP BY 
                                        template_drug_binding_id";


            $get_drug_data = $this->Common_model->get_all_rows_by_query($get_drug_data_query);

            if (!empty($get_template_detail)) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $get_template_detail;
                $this->my_response['data']['clinical_notes'] = $get_clinical_notes;
                $this->my_response['data']['drug_data'] = $get_drug_data;
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

    /**
     * Description :- This function is used to template belongs to the particular doctor or not
     * 
     * 
     * 
     * @param type $requested_data
     */
    public function check_template_belongs($requested_data) {

        $where_array = array(
            'template_id' => $requested_data['template_id'],
            'template_doctor_id' => $requested_data['doctor_id'],
            'template_status' => 1
        );

        $get_template_data = $this->Common_model->get_single_row(TBL_TEMPLATE, 'template_id', $where_array);

        if (empty($get_template_data)) {
            $this->my_response['status'] = false;
            $this->my_response['message'] = lang('mycontroller_invalid_request');
            $this->send_response();
        }
    }

    /**
     * Description :- This function is used to check the template name exists for the same doctor or not
     * 
     * 
     * 
     * @param type $requested_data
     * @param type $is_edit
     */
    public function check_template_name_exists($requested_data, $is_edit = 2) {

        $where_array = array(
            'template_template_name' => $requested_data['template_name'],
            'template_doctor_id' => $requested_data['doctor_id'],
            'template_status !=' => 9
        );

        if ($is_edit == 1) {
            $where_array['template_id !='] = $requested_data['template_id'];
        }

        $get_template_data = $this->Common_model->get_single_row(TBL_TEMPLATE, 'template_id', $where_array);

        if (!empty($get_template_data)) {
            $this->my_response['status'] = false;
            $this->my_response['message'] = lang('template_name_exists');
            $this->send_response();
        }
    }

    /**
     * Description :- This function is used to check the pricing name exists or not for same doctor
     * 
     * 
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
            return $get_clinical_notes_data['clinical_notes_catalog_id'];
        }

        return 0;
    }

    /**
     * Description :- This function is used to save the template based on the appointment id 
     * if data is added such as clinical notes, rx and investigation
     * 
     * 
     * 
     */
    public function save_template_based_appointment_post() {

        try {

            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $template_name = !empty($this->Common_model->escape_data($this->post_data['template_name'])) ? trim($this->Common_model->escape_data($this->post_data['template_name'])) : '';
            $appointment_id = !empty($this->Common_model->escape_data($this->post_data['appointment_id'])) ? trim($this->Common_model->escape_data($this->post_data['appointment_id'])) : '';

            if (empty($doctor_id) ||
                    empty($template_name) ||
                    empty($appointment_id)
            ) {
                $this->bad_request();
            }

            //check appointment id belongs to the doctor or not
            $where = array(
                'appointment_id' => $appointment_id,
                'appointment_doctor_user_id' => $doctor_id
            );
            $validate_data = $this->Common_model->validate_data(TBL_APPOINTMENTS, 'appointment_id', $where);

            if ($validate_data == 2) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('mycontroller_invalid_request');
                $this->send_response();
            }

            $requested_data = array(
                'doctor_id' => $doctor_id,
                'template_name' => $template_name
            );
            $this->check_template_name_exists($requested_data, 2);

            $this->db->trans_start();

            $insert_template_array = array(
                'template_diagnosis_name' => $template_name,
                'template_template_name' => $template_name,
                'template_doctor_id' => $doctor_id,
                'template_created_at' => $this->utc_time_formated
            );

            //get clinical notes reports
            $clinical_notes_where = array(
                'clinical_notes_reports_appointment_id' => $appointment_id,
                'clinical_notes_reports_status' => 1
            );

            $columns = 'clinical_notes_reports_complaints, 
                    clinical_notes_reports_observation, 
                    clinical_notes_reports_diagnoses,
                    clinical_notes_reports_add_notes';

            $get_clinical_notes_data = $this->Common_model->get_single_row(TBL_CLINICAL_NOTES_REPORT, $columns, $clinical_notes_where);

            $clinical_notes_id = array();

            if (!empty($get_clinical_notes_data)) {
                if (!empty($get_clinical_notes_data['clinical_notes_reports_complaints'])) {
                    $decode_complaints = json_decode($get_clinical_notes_data['clinical_notes_reports_complaints'], true);

                    if (!empty($decode_complaints) && is_array($decode_complaints)) {

                        foreach ($decode_complaints as $complaints) {
                            //check already clinical notes exists or not
                            $requested_data = array(
                                'doctor_id' => $doctor_id,
                                'clincal_notes_title' => $complaints,
                                'clinical_notes_type' => 1
                            );
                            $check_exists = $this->check_clinical_name_exists($requested_data, 2);

                            if (!empty($check_exists)) {
                                $complaint_id = $check_exists;
                            } else {
                                $complaint_insert_array = array(
                                    'clinical_notes_catalog_type' => 1,
                                    'clinical_notes_catalog_title' => $complaints,
                                    'clinical_notes_catalog_doctor_id' => $doctor_id,
                                    'clinical_notes_catalog_created_at' => $this->utc_time_formated
                                );
                                $complaint_id = $this->Common_model->insert(TBL_CLINICAL_NOTES, $complaint_insert_array);
                            }

                            $clinical_notes_id[] = $complaint_id;
                        }
                    }
                }

                if (!empty($get_clinical_notes_data['clinical_notes_reports_observation'])) {
                    $decode_observation = json_decode($get_clinical_notes_data['clinical_notes_reports_observation'], true);
                    if (!empty($decode_observation) && is_array($decode_observation)) {

                        foreach ($decode_observation as $observation) {
                            //check already clinical notes exists or not
                            $requested_data = array(
                                'doctor_id' => $doctor_id,
                                'clincal_notes_title' => $observation,
                                'clinical_notes_type' => 2
                            );
                            $check_exists = $this->check_clinical_name_exists($requested_data, 2);

                            if (!empty($check_exists)) {
                                $observation_id = $check_exists;
                            } else {
                                $observation_insert_array = array(
                                    'clinical_notes_catalog_type' => 2,
                                    'clinical_notes_catalog_title' => $observation,
                                    'clinical_notes_catalog_doctor_id' => $doctor_id,
                                    'clinical_notes_catalog_created_at' => $this->utc_time_formated
                                );
                                $observation_id = $this->Common_model->insert(TBL_CLINICAL_NOTES, $observation_insert_array);
                            }

                            $clinical_notes_id[] = $observation_id;
                        }
                    }
                }

                if (!empty($get_clinical_notes_data['clinical_notes_reports_diagnoses'])) {
                    $decode_diagnoses = json_decode($get_clinical_notes_data['clinical_notes_reports_diagnoses'], true);
                    if (!empty($decode_diagnoses) && is_array($decode_diagnoses)) {

                        foreach ($decode_diagnoses as $diagnoses) {
                            //check already clinical notes exists or not
                            $requested_data = array(
                                'doctor_id' => $doctor_id,
                                'clincal_notes_title' => $diagnoses,
                                'clinical_notes_type' => 3
                            );
                            $check_exists = $this->check_clinical_name_exists($requested_data, 2);

                            if (!empty($check_exists)) {
                                $diagnoses_id = $check_exists;
                            } else {
                                $diagnoses_insert_array = array(
                                    'clinical_notes_catalog_type' => 3,
                                    'clinical_notes_catalog_title' => $diagnoses,
                                    'clinical_notes_catalog_doctor_id' => $doctor_id,
                                    'clinical_notes_catalog_created_at' => $this->utc_time_formated
                                );
                                $diagnoses_id = $this->Common_model->insert(TBL_CLINICAL_NOTES, $diagnoses_insert_array);
                            }

                            $clinical_notes_id[] = $diagnoses_id;
                        }
                    }
                }

                if (!empty($get_clinical_notes_data['clinical_notes_reports_add_notes'])) {
                    $decode_notes = json_decode($get_clinical_notes_data['clinical_notes_reports_add_notes'], true);
                    if (!empty($decode_notes) && is_array($decode_notes)) {

                        foreach ($decode_notes as $notes) {
                            //check already clinical notes exists or not
                            $requested_data = array(
                                'doctor_id' => $doctor_id,
                                'clincal_notes_title' => $notes,
                                'clinical_notes_type' => 4
                            );
                            $check_exists = $this->check_clinical_name_exists($requested_data, 2);

                            if (!empty($check_exists)) {
                                $notes_id = $check_exists;
                            } else {
                                $notes_insert_array = array(
                                    'clinical_notes_catalog_type' => 4,
                                    'clinical_notes_catalog_title' => $notes,
                                    'clinical_notes_catalog_doctor_id' => $doctor_id,
                                    'clinical_notes_catalog_created_at' => $this->utc_time_formated
                                );
                                $notes_id = $this->Common_model->insert(TBL_CLINICAL_NOTES, $notes_insert_array);
                            }

                            $clinical_notes_id[] = $notes_id;
                        }
                    }
                }
            }

            if (!empty($clinical_notes_id)) {
                $insert_template_array['template_clinical_notes_id'] = implode(',', $clinical_notes_id);
            }


            //get the lab report data
            $lab_report_where = array(
                'lab_report_appointment_id' => $appointment_id,
                'lab_report_status' => 1
            );

            $investigation_columns = "lab_report_test_name, lab_report_instruction";

            $get_investigation_data = $this->Common_model->get_single_row(TBL_LAB_REPORTS, $investigation_columns, $lab_report_where);

            if (!empty($get_investigation_data)) {
                $insert_template_array['template_investigation_name'] = $get_investigation_data['lab_report_test_name'];
                $insert_template_array['template_investigation_instruction'] = $get_investigation_data['lab_report_instruction'];
            }


            $inserted_template_id = $this->Common_model->insert(TBL_TEMPLATE, $insert_template_array);

            if ($inserted_template_id > 0) {

                //get the prescription data
                $prescription_where = array(
                    'prescription_appointment_id' => $appointment_id,
                    'prescription_status' => 1
                );

                $prescription_columns = 'prescription_drug_id, 
                                     prescription_duration, 
                                     prescription_duration_value, 
                                     prescription_intake,
                                     prescription_intake_instruction,
                                     prescription_dosage,
                                     prescription_frequency_id,
                                     prescription_frequency_value,
                                     prescription_frequency_instruction,
                                     prescription_diet_instruction';

                $get_prescription_data = $this->Common_model->get_all_rows(TBL_PRESCRIPTION_REPORTS, $prescription_columns, $prescription_where);

                if (!empty($get_prescription_data)) {

                    if (is_array($get_prescription_data) && count($get_prescription_data) > 0) {
                        foreach ($get_prescription_data as $drug) {
                            $insert_template_binding_array[] = array(
                                'template_drug_binding_template_id' => $inserted_template_id,
                                'template_drug_binding_drug_id' => $drug['prescription_drug_id'],
                                'template_drug_binding_intake_instruction' => !empty($drug['prescription_intake_instruction']) ? trim($drug['prescription_intake_instruction']) : '',
                                'template_drug_binding_dosage' => !empty($drug['prescription_dosage']) ? trim($drug['prescription_dosage']) : '',
                                'template_drug_binding_frequency_instruction' => !empty($drug['prescription_frequency_instruction']) ? trim($drug['prescription_frequency_instruction']) : '',
                                'template_drug_binding_custom_frequency' => !empty($drug['prescription_frequency_value']) ? trim($drug['prescription_frequency_value']) : '',
                                'template_drug_binding_duration_value' => !empty($drug['prescription_duration_value']) ? trim($drug['prescription_duration_value']) : '',
                                'template_drug_binding_duration' => !empty($drug['prescription_duration']) ? trim($drug['prescription_duration']) : '',
                                'template_drug_binding_intake' => !empty($drug['prescription_intake']) ? trim($drug['prescription_intake']) : '',
                                'template_drug_binding_frequency' => !empty($drug['prescription_frequency_id']) ? trim($drug['prescription_frequency_id']) : '',
                                'template_drug_binding_diet_instruction' => !empty($drug['prescription_diet_instruction']) ? trim($drug['prescription_diet_instruction']) : '',
                                'template_drug_binding_created_at' => $this->utc_time_formated
                            );
                        }
                        $this->Common_model->insert_multiple(TBL_TEMPLATE_DRUG, $insert_template_binding_array);
                    }
                }
            }

            if ($this->db->trans_status() !== FALSE) {
                $this->db->trans_commit();
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('template_added');
            } else {
                $this->db->trans_rollback();
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }

            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

}
