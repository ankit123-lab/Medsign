<?php

class Drug extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("Drug_model", "drug");
    }

    /**
     * Description :- This function is used to get the list of the drugs for the patient only
     * 
     * 
     * 
     * 
     */
    public function get_drug_list_post() {

        $added_by = !empty($this->post_data['added_by']) ? trim($this->Common_model->escape_data($this->post_data['added_by'])) : "";
        $search = !empty($this->post_data['search']) ? trim($this->Common_model->escape_data($this->post_data['search'])) : "";
        $page = !empty($this->post_data['page']) ? trim($this->Common_model->escape_data($this->post_data['page'])) : "1";
        $per_page = !empty($this->post_data['per_page']) ? trim($this->Common_model->escape_data($this->post_data['per_page'])) : "10";
        
        $total_data = array(
            'page' => ''
        );
        
        $total_count = $this->drug->get_drug_list($total_data);
        
        $request_data = array(
            'page' => $page,
            'per_page' => $per_page,
            'drug_user_id' => $added_by,
            'search' => $search
        );
        
        $get_drug_list = $this->drug->get_drug_list($request_data);

        if (!empty($get_drug_list)) {
            $this->my_response = array(
                "status" => true,
                "message" => lang("common_detail_found"),
                "data" => $get_drug_list,
                'total_count' => $total_count,
                'per_page' => $per_page,
                'current_page' => $page
            );
        } else {
            $this->my_response = array(
                "status" => true,
                "message" => lang("common_detail_not_found"),
            );
        }
        $this->send_response();
    }

    /**
     * Description :- This function is used to add the drug for patient only for the purpose of the reminder
     * 
     * 
     * 
     */
    public function add_drug_post() {

        $drug_name = !empty($this->post_data['drug_name']) ? trim($this->Common_model->escape_data($this->post_data['drug_name'])) : "";

        try {
            if (empty($drug_name)) {
                $this->bad_request();
                exit;
            }

            //check same drug is added by user not

            $where_drug = array(
                'LOWER(drug_name)' => strtolower($drug_name),
                'drug_user_id' => $this->user_id,
                'drug_status' => 1
            );
            $get_drug_data = $this->drug->get_drug($where_drug);

            if (empty($get_drug_data)) {
                /* get random color until find unique */
                $is_found = true;
                $loop_count = 0;
                while ($is_found && $loop_count <= 100) {
                    $color = random_color();
                    //check into db
                    $is_exist = $this->drug->check_color($color);
                    if (empty($is_exist)) {
                        $is_found = false;
                    }
                    $loop_count++;
                }

                $insert_drug_data = array(
                    'drug_name' => strtolower($drug_name),
                    'drug_color_code' => $color,
                    'drug_user_id' => $this->user_id,
                    'drug_created_at' => $this->utc_time_formated
                );

                $add_drug_id = $this->drug->add_drug($insert_drug_data);
                if ($add_drug_id > 0) {
                    $this->my_response['status'] = true;
                    $this->my_response['message'] = lang('drug_add');
                    $this->my_response['drug_id'] = (string) $add_drug_id;
                } else {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('drug_add_failure');
                }
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('drug_already_added');
            }
            $this->send_response();
        } catch (ErrorException $e) {
            $this->error = $e->getMessage();
            $this->store_error();
        }
    }

    public function get_drugs_post() {

        try {

            $brand_name = !empty($this->post_data['brand_name']) ? trim($this->Common_model->escape_data($this->post_data['brand_name'])) : "";

            $columns = "drug_id,
                        drug_name_with_unit as drug_name,
                        drug_strength ";

            $get_drug_list_query = "SELECT 
                        " . $columns . " 
                        FROM 
                        " . TBL_DRUGS . " 
                        WHERE 
                        drug_status = 1 
                        AND
                        drug_user_id IS NULL 
                        ";

            if (!empty($brand_name)) {
                $get_drug_list_query .= " AND drug_name LIKE '" . $brand_name . "%' ";
                $get_drug_list_query .= " ORDER BY drug_name_with_unit asc";
            }else{
                $get_drug_list_query .= " ORDER BY drug_name_with_unit asc LIMIT 0,50";
            }
            
            $get_drug_list = $this->Common_model->get_all_rows_by_query($get_drug_list_query);

            if (!empty($get_drug_list)) {
                $this->my_response['status'] = true;
                $this->my_response['messgae'] = lang('common_detail_found');
                $this->my_response['data'] = $get_drug_list;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['messgae'] = lang('common_detail_not_found');
                $this->my_response['data'] = array();
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to get the related list of the similiar brand name
     * 
     * @author Reena Gatecha
     * 
     */
    public function get_related_drugs_post() {

        try {

            $drug_id = !empty($this->post_data['drug_id']) ? trim($this->Common_model->escape_data($this->post_data['drug_id'])) : "";

            $columns = "drug_id, 
                        drug_name,
                        drug_strength ";

            //get the related drugs data
            $get_related_data_sql = " SELECT 
                                        group_concat(drug_strength) as drug_strength, 
                                        group_concat(drug_drug_generic_id) as drug_generic_id, 
                                        group_concat(drug_drug_unit_id) as drug_unit_id
                                        FROM
                                        " . TBL_DRUGS . " 
                                        WHERE 
                                        drug_id =" . $drug_id . "";

            $get_related_data = $this->Common_model->get_single_row_by_query($get_related_data_sql);

            //get related drugs
            $get_related_drugs_sql = " SELECT 
                                        " . $columns . " 
                                        FROM 
                                        " . TBL_DRUGS . "
                                        WHERE 1=1 ";

            if (!empty($get_related_data['drug_strength'])) {
                $get_related_drugs_sql .= " OR drug_strength IN ('" . $get_related_data['drug_strength'] . "') ";
            }
            if (!empty($get_related_data['drug_generic_id'])) {
                $get_related_drugs_sql .= " OR drug_drug_generic_id IN (" . $get_related_data['drug_generic_id'] . ") ";
            }
            if (!empty($get_related_data['drug_unit_id'])) {
                $get_related_drugs_sql .= " OR drug_drug_unit_id IN (" . $get_related_data['drug_unit_id'] . ") ";
            }
            $get_related_drugs_sql .= " LIMIT 0,5 ";

            $get_related_drugs = $this->Common_model->get_all_rows_by_query($get_related_drugs_sql);

            if (!empty($get_related_drugs)) {
                $this->my_response['status'] = true;
                $this->my_response['messgae'] = lang('common_detail_found');
                $this->my_response['data'] = $get_related_drugs;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['messgae'] = lang('common_detail_not_found');
                $this->my_response['data'] = array();
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to get the drug detail
     * 
     * 
     * 
     */
    public function get_drug_detail_post() {

        try {

            $drug_id = !empty($this->post_data['drug_id']) ? trim($this->Common_model->escape_data($this->post_data['drug_id'])) : "";

            $columns = "drug_id, 
                        drug_name, 
                        drug_strength, 
                        drug_duration, 
                        drug_duration_value,
                        drug_intake,
                        drug_instruction,
                        
                        drug_drug_generic_id, 
                        GROUP_CONCAT(drug_generic_title) as drug_generic_title, 
                        
                        drug_unit_id, 
                        drug_unit_name, 
                        drug_unit_medicine_type,
                        drug_unit_is_qty_calculate,
                        drug_drug_unit_value, 
                        
                        drug_frequency_id,
                        drug_frequency_name ";

            $get_drug_detail_query = " SELECT 
                                            " . $columns . " 
                                        FROM 
                                            " . TBL_DRUGS . " 
                                        LEFT JOIN
                                            " . TBL_DRUG_GENERIC . " 
                                        ON 
                                            FIND_IN_SET(drug_generic_id, drug_drug_generic_id)  AND  drug_generic_status = 1
                                        LEFT JOIN
                                             " . TBL_DRUG_UNIT . " 
                                        ON 
                                            drug_drug_unit_id = drug_unit_id AND drug_unit_status = 1
                                        LEFT JOIN
                                             " . TBL_DRUG_FREQUENCY . "   
                                        ON
                                            drug_drug_frequency_id = drug_frequency_id AND drug_frequency_status = 1
                                        WHERE 
                                            drug_status = 1
                                        AND
                                            drug_id = '" . $drug_id . "' ";

            $get_drug_detail_data = $this->Common_model->get_all_rows_by_query($get_drug_detail_query);

            if (!empty($get_drug_detail_data)) {
                $this->my_response['status'] = true;
                $this->my_response['messgae'] = lang('common_detail_found');
                $this->my_response['data'] = $get_drug_detail_data;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['messgae'] = lang('common_detail_not_found');
                $this->my_response['data'] = array();
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to get the drugs list of the similiar brand name
     * 
     * @author Manish Ramnanni
     * 
     */
    public function get_drug_brand_type_post() {

        try {

            $columns = "drug_unit_id, 
                        drug_unit_name,
                        drug_unit_medicine_type";

            $get_brand_type_list_query = "SELECT 
                                        " . $columns . " 
                                    FROM 
                                        " . TBL_DRUG_UNIT . "     
                                    WHERE 
                                        drug_unit_status = 1";

            $get_brand_type_list = $this->Common_model->get_all_rows_by_query($get_brand_type_list_query);

            if (!empty($get_brand_type_list)) {
                $this->my_response['status'] = true;
                $this->my_response['messgae'] = lang('common_detail_found');
                $this->my_response['data'] = $get_brand_type_list;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['messgae'] = lang('common_detail_not_found');
                $this->my_response['data'] = array();
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * This function GET DRUG_GENERIC lists and search also  DRUG GENERIC.
     * 
     * @author Prashant Suthar
     * 
     * 
     */
    public function get_drug_generic_post() {

        try {
            $search_drug_generic = !empty($this->post_data['search_drug_generic']) ? trim($this->Common_model->escape_data($this->post_data['search_drug_generic'])) : "";

            $query = "SELECT
                    drug_generic_id, 
                    drug_generic_title, 
                    drug_generic_reference 
                 FROM
                    " . TBL_DRUG_GENERIC . "
                 WHERE
                    drug_generic_status = 1";

            if (!empty($search_drug_generic)) {
                $query .= " AND drug_generic_title LIKE '%" . strtolower($search_drug_generic) . "%'";
            }

            $query .= " ORDER BY drug_generic_id ASC";

            $get_drug_generic = $this->Common_model->get_all_rows_by_query($query);


            if (!empty($get_drug_generic)) {
                $this->my_response = array(
                    "status" => true,
                    "message" => lang("common_drug_generic_found"),
                    "data" => $get_drug_generic,
                );
            } else {
                $this->my_response = array(
                    "status" => true,
                    "message" => lang("common_drug_generic_not_found"),
                );
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to get the detail of the generic based on id
     * 
     * 
     * 
     */
    public function get_drug_generic_detail_post() {

        try {
            $generic_id = !empty($this->post_data['generic_id']) ? trim($this->Common_model->escape_data($this->post_data['generic_id'])) : "";

            $query = "SELECT
                        drug_generic_id, 
                        drug_generic_title, 
                        drug_generic_reference,
                        drug_generic_indications,
                        drug_generic_administration,
                        drug_generic_contraindications,
                        drug_generic_special_precautions,
                        drug_generic_pc,
                        drug_generic_adr,
                        drug_generic_interactions,
                        drug_generic_dosage1,
                        drug_generic_dosage2,
                        drug_generic_dosage_indication_dosage
                    FROM
                        " . TBL_DRUG_GENERIC . "
                    WHERE
                        drug_generic_status = 1 
                    AND 
                        drug_generic_id = '" . $generic_id . "' ";

            $get_drug_generic = $this->Common_model->get_single_row_by_query($query);

            if (!empty($get_drug_generic)) {
                $this->my_response = array(
                    "status" => true,
                    "message" => lang("common_drug_generic_found"),
                    "data" => $get_drug_generic,
                );
            } else {
                $this->my_response = array(
                    "status" => true,
                    "message" => lang("common_drug_generic_not_found"),
                );
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * This function GET drug frequency lists and search also Drug frequency.
     * 
     * @author Prashant Suthar
     * 
     * 
     */
    public function get_drug_frequency_post() {

        try {
            $search_drug_frequency = !empty($this->post_data['search_drug_frequency']) ? trim($this->Common_model->escape_data($this->post_data['search_drug_frequency'])) : "";

            $query = "SELECT
                    drug_frequency_id, 
                    drug_frequency_name 
                 FROM
                    " . TBL_DRUG_FREQUENCY . "
                 WHERE
                    drug_frequency_status = 1";

            if (!empty($search_drug_frequency)) {
                $query .= " AND me_drug_frequency_name LIKE '%" . strtolower($search_drug_frequency) . "%'";
            }

            $query .= " ORDER BY drug_frequency_id ASC";

            $get_drug_frequency = $this->Common_model->get_all_rows_by_query($query);


            if (!empty($get_drug_frequency)) {
                $this->my_response = array(
                    "status" => true,
                    "message" => lang("common_drug_frequency_found"),
                    "data" => $get_drug_frequency,
                );
            } else {
                $this->my_response = array(
                    "status" => true,
                    "message" => lang("common_drug_frequency_not_found"),
                );
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function add_drug_by_doctor_post() {

        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : "";
            $drug_data = !empty($this->post_data['drug_data']) ? $this->post_data['drug_data'] : "";
            $drug_data = json_decode($drug_data, true);
            $insert_drug = array();
            $temp_array = array();

            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 19,
                    'key' => 1
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }

            //_px($drug_data);
            if (is_array($drug_data) && count($drug_data) > 0) {

                foreach ($drug_data as $data) {

                    if (!in_array($data['drug_intake'], array(1, 2, 3, 4))) {
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang('mycontroller_invalid_request');
                        $this->send_response();
                    }

                    if (!in_array($data['drug_duration'], array(1, 2, 3))) {
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang('mycontroller_invalid_request');
                        $this->send_response();
                    }

                    if (!isset($temp_array[$data['drug_name']])) {
                        $insert_drug[] = array(
                            'drug_name' => $data['drug_name'],
                            'drug_user_id' => $doctor_id,
                            'drug_color_code' => random_color(),
                            'drug_strength' => $data['drug_strength'],
                            'drug_drug_generic_id' => $data['drug_generic_id'],
                            'drug_drug_frequency_id' => $data['drug_frequency_id'],
                            'drug_drug_unit_id' => $data['drug_unit_id'],
                            'drug_drug_unit_value' => $data['drug_unit_value'],
                            'drug_instruction' => $data['drug_instruction'],
                            'drug_intake' => $data['drug_intake'],
                            'drug_duration' => $data['drug_duration'],
                            'drug_duration_value' => $data['drug_duration_value'],
                            'drug_created_at' => $this->utc_time_formated
                        );
                        $temp_array[$data['drug_name']] = true;
                    }
                }

                $this->Common_model->insert_multiple(TBL_DRUGS, $insert_drug);

                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('drug_add');
                $this->send_response();
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('mycontroller_invalid_request');
                $this->send_response();
            }
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to get the list of the medicine added by the doctor
     * 
     * 
     * 
     */
    public function get_doctor_drug_post() {
        try {

            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : "";
            $search = !empty($this->post_data['search']) ? trim($this->Common_model->escape_data($this->post_data['search'])) : "";

            if (empty($doctor_id)) {
                $this->bad_request();
            }

            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 19,
                    'key' => 3
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }

            $where_array = array(
                'drug_user_id' => $doctor_id,
                'drug_status' => 1
            );

            if (!empty($search)) {
                $where_array['drug_name LIKE'] = '%' . $search . '%';
            }

            $colums = 'drug_id, 
                       drug_name,
                       drug_instruction,
                       drug_strength,
                       drug_unit_name,
                       drug_unit_medicine_type';

            $left_join = array(
                TBL_DRUG_UNIT => 'drug_drug_unit_id = drug_unit_id'
            );

            $get_drug_list = $this->Common_model->get_all_rows(TBL_DRUGS, $colums, $where_array, $left_join, array(), '', 'LEFT');

            if (!empty($get_drug_list)) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $get_drug_list;
            } else {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_not_found');
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to delete the medicine created by the user
     * 
     * 
     * 
     */
    public function delete_drug_post() {

        try {

            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : "";
            $drug_id = !empty($this->post_data['drug_id']) ? trim($this->Common_model->escape_data($this->post_data['drug_id'])) : "";

            if (empty($doctor_id) ||
                    empty($drug_id)
            ) {
                $this->bad_request();
            }

            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 19,
                    'key' => 4
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }

            //check drug id belongs to the doctor
            $get_drug_where = array(
                'drug_user_id' => $doctor_id,
                'drug_id' => $drug_id,
                'drug_status' => 1
            );

            $get_drug_detail = $this->Common_model->get_single_row(TBL_DRUGS, 'drug_id', $get_drug_where);

            if (empty($get_drug_detail)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('mycontroller_invalid_request');
                $this->send_response();
            }

            $update_drug_data = array(
                'drug_status' => 9,
                'drug_modified_at' => $this->utc_time_formated
            );

            $update_drug_where = array(
                'drug_id' => $drug_id
            );

            $is_update = $this->Common_model->update(TBL_DRUGS, $update_drug_data, $update_drug_where);

            if ($is_update > 0) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('drug_delete');
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

    /**
     * Description :- This function is used to add the prescription by the patient
     * 
     * 
     * 
     */
    public function add_prescription_by_patient_post() {
        try {
            $date = !empty($this->Common_model->escape_data($this->post_data['date'])) ? trim($this->Common_model->escape_data($this->post_data['date'])) : '';
            $doctor_name = !empty($this->Common_model->escape_data($this->post_data['doctor_name'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_name'])) : '';
            $clinic_name = !empty($this->Common_model->escape_data($this->post_data['clinic_name'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_name'])) : '';
            $other_user_id = !empty($this->Common_model->escape_data($this->post_data['other_user_id'])) ? trim($this->Common_model->escape_data($this->post_data['other_user_id'])) : '';

            if (empty($date) ||
                    empty($doctor_name) ||
                    empty($clinic_name)
            ) {
                $this->bad_request();
            }

            $this->db->trans_start();

            $insert_prescription = array(
                'patient_prescription_user_id' => $other_user_id,
                'patient_prescription_created_by' => $this->user_id,
                'patient_prescription_doctor_name' => ucwords(str_replace(array('dr. ', 'dr '), array('',''), strtolower($doctor_name))),
                'patient_prescription_clinic_name' => $clinic_name,
                'patient_prescription_date' => $date,
                'patient_prescription_created_at' => $this->utc_time_formated
            );

            $inserted_id = $this->Common_model->insert(TBL_PATIENT_PRESCRIPTION, $insert_prescription);

            if ($inserted_id > 0) {

                if (!empty($_FILES['images']['name']) && $_FILES['images']['error'] == 0) {

                    $upload_path = UPLOAD_REL_PATH . "/" . PRESCRIPTION_FOLDER . "/" . $inserted_id;
                    $upload_folder = PRESCRIPTION_FOLDER . "/" . $inserted_id;
                    $new_file = do_upload($upload_path, $_FILES, $upload_folder);

                    if (!empty($new_file[0])) {
                        $send_data = array(
                            'file_name' => $new_file[0],
                            'upload_path' => $upload_path,
                            'upload_folder' => $upload_folder,
                            'id' => $inserted_id
                        );
                        $image_name = upload_zip_data($send_data);
                    }

                    $insert_image_array = array();
                    if (!empty($image_name) && count($image_name) > 0) {
                        foreach ($image_name as $image) {
                            if (!empty($image)) {
                                $insert_image_array[] = array(
                                    'patient_prescription_photo_prescription_id' => $inserted_id,
                                    'patient_prescription_photo_image' => $image,
                                    'patient_prescription_photo_filepath' => IMAGE_MANIPULATION_URL . PRESCRIPTION_FOLDER . "/" . $inserted_id . "/" . $image,
                                    'patient_prescription_file_size' => get_file_size(IMAGE_MANIPULATION_URL . PRESCRIPTION_FOLDER . "/" . $inserted_id . "/" . $image),
                                    'patient_prescription_photo_created_at' => $this->utc_time_formated
                                );
                            }
                        }
                        $this->Common_model->insert_multiple(TBL_PATIENT_PRESCRIPTION_IMAGES, $insert_image_array);
                    }
                }

                if ($this->db->trans_status() !== FALSE) {
                    $this->db->trans_commit();
                    $this->my_response['status'] = true;
                    $this->my_response['message'] = lang('prescription_added');
                } else {
                    $this->db->trans_rollback();
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('failure');
                }
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
    
    
    public function add_invoice_by_patient_post() {
        try {

            $date = !empty($this->Common_model->escape_data($this->post_data['date'])) ? trim($this->Common_model->escape_data($this->post_data['date'])) : '';
            $doctor_name = !empty($this->Common_model->escape_data($this->post_data['doctor_name'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_name'])) : '';
            $clinic_name = !empty($this->Common_model->escape_data($this->post_data['clinic_name'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_name'])) : '';
            $other_user_id = !empty($this->Common_model->escape_data($this->post_data['other_user_id'])) ? trim($this->Common_model->escape_data($this->post_data['other_user_id'])) : '';

            if (empty($date) ||
                    empty($doctor_name) ||
                    empty($clinic_name)
            ) {
                $this->bad_request();
            }

            $this->db->trans_start();
            $insert_invoice = array(
                'patient_invoice_user_id' => $other_user_id,
                'patient_invoice_created_by' => $this->user_id,
                'patient_invoice_doctor_name' => ucwords(str_replace(array('dr. ', 'dr '), array('',''), strtolower($doctor_name))),
                'patient_invoice_clinic_name' => $clinic_name,
                'patient_invoice_date' => $date,
                'patient_invoice_created_at' => $this->utc_time_formated
            );
            //echo '<pre>';print_r($insert_invoice); exit;
            $inserted_id = $this->Common_model->insert(TBL_PATIENT_INVOICE, $insert_invoice);

            if ($inserted_id > 0) {

                if (!empty($_FILES['images']['name']) && $_FILES['images']['error'] == 0) {
					
                    /* $upload_path = UPLOAD_REL_PATH . "/" . INVOICE_FOLDER . "/" . $inserted_id;
                    $upload_folder = INVOICE_FOLDER . "/" . $inserted_id;
                    $new_file = do_upload($upload_path, $_FILES, $upload_folder);

                    if (!empty($new_file[0])) {
                        $send_data = array(
                            'file_name' => $new_file[0],
                            'upload_path' => $upload_path,
                            'upload_folder' => $upload_folder,
                            'id' => $inserted_id
                        );
                        $image_name = upload_zip_data($send_data);
                    } */
					
					$upload_path = UPLOAD_REL_PATH . "/" . INVOICE_FOLDER . "/" . $inserted_id;
                    $upload_folder = INVOICE_FOLDER . "/" . $inserted_id;
                    $new_file = do_upload($upload_path, $_FILES, $upload_folder);

                    if (!empty($new_file[0])) {
                        $send_data = array(
                            'file_name' => $new_file[0],
                            'upload_path' => $upload_path,
                            'upload_folder' => $upload_folder,
                            'id' => $inserted_id
                        );
                        $image_name = upload_zip_data($send_data);
                    }

                    $insert_image_array = array();
                    if (!empty($image_name) && count($image_name) > 0) {
                        foreach ($image_name as $image) {
                            if (!empty($image)) {
                                $insert_image_array[] = array(
                                    'patient_invoice_photo_invoice_id' => $inserted_id,
                                    'patient_invoice_photo_image' => $image,
                                    'patient_invoice_photo_filepath' => IMAGE_MANIPULATION_URL . INVOICE_FOLDER . "/" . $inserted_id . "/" . $image,
                                    'patient_invoice_file_size' => get_file_size(IMAGE_MANIPULATION_URL . INVOICE_FOLDER . "/" . $inserted_id . "/" . $image),
                                    'patient_invoice_photo_created_at' => $this->utc_time_formated
                                );
                            }
                        }
                        $this->Common_model->insert_multiple(TBL_PATIENT_INVOICE_IMAGES, $insert_image_array);
                    }
                }

                if ($this->db->trans_status() !== FALSE) {
                    $this->db->trans_commit();
                    $this->my_response['status'] = true;
                    $this->my_response['message'] = lang('invoice_added');
                } else {
                    $this->db->trans_rollback();
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('failure');
                }
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

    /**
     * Description :- This function is used to get the prescription added  
     * by the doctor as well as by patient here listing of the appointment and 
     * prescription added by the doctor is same i.e using the appointment details for the same
     * 
     * 
     * 
     */
    public function get_my_prescription_post() {

        try {
            $patient_id = !empty($this->post_data['patient_id']) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : "";
            $page = !empty($this->post_data['page']) ? trim($this->Common_model->escape_data($this->post_data['page'])) : "1";
            $per_page = !empty($this->post_data['per_page']) ? trim($this->Common_model->escape_data($this->post_data['per_page'])) : "10";

            if (empty($patient_id)) {
                $this->bad_request();
            }
            //get appointment data from db
            $request_data = array(
                "patient_id" => $patient_id,
            );
            $total_count = $this->drug->get_my_prescription($request_data);
            $request_data = array(
                "patient_id" => $patient_id,
                "page" => $page,
                "per_page" => $per_page,
            );
            $prescription_data = $this->drug->get_my_prescription($request_data);
            foreach($prescription_data as $key => $value){
				$user_where = array('user_id' => $value['doctor_user_id']);
				$column = 'user_type';
				$user_data = $this->Common_model->get_single_row(TBL_USERS, $column, $user_where);
				if(isset($user_data['user_type']) && $user_data['user_type'] == 2){
					$dr_prefix = DOCTOR;
				}else{
					$dr_prefix = '';
					$prescription_data[$key]['patient_prescription_doctor_name'] =  DOCTOR .' '. $value['patient_prescription_doctor_name'];
				}
				$prescription_data[$key]['doctor_first_name'] =  $dr_prefix .' '. $value['doctor_first_name'];
            }
            
             //pr($prescription_data); exit;
            if (!empty($prescription_data)) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $prescription_data;
                $this->my_response['total_count'] = $total_count;
                $this->my_response['per_page'] = $per_page;
                $this->my_response['current_page'] = $page;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('common_detail_not_found');
            }
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_my_prescription_detail_post() {

        try {
            $patient_id = !empty($this->post_data['patient_id']) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : "";
            $prescription_id = !empty($this->post_data['prescription_id']) ? trim($this->Common_model->escape_data($this->post_data['prescription_id'])) : "";

            if (empty($patient_id) ||
                    empty($prescription_id)
            ) {
                $this->bad_request();
            }

            $prescription_where = array(
                'patient_prescription_user_id' => $patient_id,
                'patient_prescription_id' => $prescription_id,
                'patient_prescription_status' => 1
            );
            $column = 'patient_prescription_doctor_name, 
                       patient_prescription_clinic_name,
                       patient_prescription_date';

            $prescription_data = $this->Common_model->get_single_row(TBL_PATIENT_PRESCRIPTION, $column, $prescription_where);

            if (!empty($prescription_data)) {

                $image_where = array(
                    'patient_prescription_photo_prescription_id' => $prescription_id,
                    'patient_prescription_photo_status' => 1
                );
                $get_prescription_images = $this->Common_model->get_all_rows(TBL_PATIENT_PRESCRIPTION_IMAGES, 'patient_prescription_photo_filepath', $image_where);
                $prescription_data["images"] = array_column($get_prescription_images, 'patient_prescription_photo_filepath');
                $prescription_data['patient_prescription_doctor_name'] = DOCTOR . ' ' . $prescription_data['patient_prescription_doctor_name'];
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $prescription_data;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('common_detail_not_found');
            }
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

}
