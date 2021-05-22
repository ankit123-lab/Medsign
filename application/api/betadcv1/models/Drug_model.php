<?php

class Drug_model extends MY_Model {

    protected $drug_table;
    protected $drug_column;

    public function __construct() {
        parent::__construct();
        $this->drug_table = TBL_DRUGS;
        $this->drug_column = array(
            'drug_id',
            'drug_name'
        );
    }

    /**
     * Description :- This function is used to get the list of the drug
     * 
     * @author 
     * 
     * @param type $drug_where_array
     * @return type
     */
    public function get_drug_list($request_data = array()) {
        $get_drug_list = array();
        if (!empty($request_data)) {


            $get_drugs_by_sql = " SELECT drug_id, drug_name FROM " . TBL_DRUGS . " WHERE drug_status = 1  ";

            if (!empty($request_data['drug_user_id'])) {
                $get_drugs_by_sql .= " AND drug_user_id = '" . $request_data['drug_user_id'] . "' ";
            }
            if (!empty($request_data['search'])) {
                $get_drugs_by_sql .= " AND drug_name like '" . $request_data['search'] . "%' ";
            }
            if (empty($request_data['page'])) {
                return $this->get_count_by_query($get_drugs_by_sql);
            }

            $get_drugs_by_sql .= " LIMIT " . (($request_data['page'] - 1) * $request_data['per_page']) . "," . $request_data['per_page'] . " ";

            $get_drug_list = $this->get_all_rows_by_query($get_drugs_by_sql);
        }
        return $get_drug_list;
    }

    /**
     * Description :- This function is used to add the drug
     * 
     * @author 
     * 
     * @param type $drug_array
     * @return int
     */
    public function add_drug($drug_array = array()) {
        if (!empty($drug_array) && is_array($drug_array)) {
            $inserted_id = $this->insert($this->drug_table, $drug_array);
            return $inserted_id;
        }
        return 0;
    }

    /**
     * Description :- This function is used to get the drug detaail based on the condition
     * 
     * @author 
     * 
     * @param type $drug_where_array
     * @return type
     */
    public function get_drug($drug_where_array = array()) {
        $get_drug = array();

        if (!empty($drug_where_array)) {
            $get_drug = $this->get_single_row($this->drug_table, $this->drug_column, $drug_where_array);
        }
        return $get_drug;
    }

    /**
     * Description :- This function is used to get the drug detaail based on the condition
     * 
     * @param type $brand_name
     * @param type $doctor_id
     * @return result array
     */
    public function search_drug($brand_name, $doctor_id = '', $drug_generic_id = '') {
        //$this->db->cache_on('drug_db');
        $this->db->select('drug_id, drug_name_with_unit AS drug_name, drug_strength')->from($this->drug_table);
        $this->db->where('drug_status', 1);
        if (!empty($doctor_id)) {
            $this->db->where("(drug_user_id IS NULL OR drug_user_id = ".$doctor_id.")");    
        } else {
            $this->db->where("drug_user_id IS NULL"); 
        }
        if(!empty($brand_name))
            $this->db->like('drug_name', $brand_name, 'after');
        if(!empty($drug_generic_id))
            $this->db->where('drug_drug_generic_id', $drug_generic_id);
        $this->db->order_by('drug_name');
        $selectQueStr = $this->db->get_compiled_select();
        // echo $selectQueStr;die;
        if(IS_FILE_CACHING_ACTIVE == 1){
            create_cache_sub_dir("me_drugs", $doctor_id);
            $hashObj = sha1($selectQueStr);
            $this->load->driver('cache');
            if(!$resultCached = $this->cache->file->get($hashObj)){
                $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
                $this->cache->file->save($hashObj, $result, CACHE_TTL);
            }else{
                $result = $resultCached;
            }
        }else{
            $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
        }
        //$this->db->cache_off('drug_db');
        return $result;
    }

public function get_generic($columns) {
        $this->db->select($columns)->from("me_drug_generic");
        $this->db->where('drug_generic_status', 1);
        $selectQueStr = $this->db->get_compiled_select();
        if(IS_FILE_CACHING_ACTIVE == 1){
            create_cache_sub_dir('me_drug_generic');
            $hashObj = sha1($selectQueStr);
            $this->load->driver('cache');
            if(!$resultCached = $this->cache->file->get($hashObj)){
                $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
                $this->cache->file->save($hashObj, $result, CACHE_TTL);
            }else{
                $result = $resultCached;
            }
        }else{
            $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
        }
        return $result;
    }
    public function search_generic($generic_name, $doctor_id = '') {
        $this->db->select("drug_drug_generic_id")->from($this->drug_table);
        $this->db->join("me_drug_generic", "find_in_set(drug_generic_id,drug_drug_generic_id) AND  drug_generic_status = 1");
        $this->db->where('drug_status', 1);
        if (!empty($doctor_id)) {
            $this->db->where("(drug_user_id IS NULL OR drug_user_id = ".$doctor_id.")");    
        } else {
            $this->db->where("drug_user_id IS NULL"); 
        }
        $this->db->like('LOWER(drug_generic_title)', strtolower($generic_name), 'after');
        $this->db->group_by("drug_drug_generic_id");
        $this->db->order_by("drug_generic_title");
        $selectQueStr = $this->db->get_compiled_select();
        if(IS_FILE_CACHING_ACTIVE == 1){
            create_cache_sub_dir('me_drug_generic');
            $hashObj = sha1($selectQueStr);
            $this->load->driver('cache');
            if(!$resultCached = $this->cache->file->get($hashObj)){
                $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
                $this->cache->file->save($hashObj, $result, CACHE_TTL);
            }else{
                $result = $resultCached;
            }
        }else{
            $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
        }
        return $result;
    }

    /**
     * Description :- This function is used to check the same color 
     * of the drug exists or not
     * 
     * @author 
     * 
     * @param type $color
     * @return type
     */
    public function check_color($color) {
        $is_exist = $this->get_single_row(TBL_DRUGS, "drug_id", array("drug_color_code" => $color));
        return $is_exist;
    }

    /**
     * Description :- This function is used to get the appointments of the patient
     * 
     * @author 
     * 
     * @param type $request
     * @return type
     */
    public function get_my_prescription($request_data) {
        $prescription_query = "     SELECT
                                        user_id AS doctor_user_id,
                                        user_first_name AS doctor_first_name,
                                        user_last_name AS doctor_last_name,
                                        user_photo_filepath AS doctor_photo,
                                        doctor_detail_year_of_experience AS doctor_experience,
                                        GROUP_CONCAT(DISTINCT(specialization_title)) AS doctor_specialisation,
                                        GROUP_CONCAT(DISTINCT(doctor_qualification_degree)) AS doctor_qualification,
                                        appointment_id AS appointment_id,
                                        appointment_from_time AS appointment_from_time,    
                                        appointment_date AS appointment_date,
                                        appointment_type AS appointment_type,
                                        doctor_clinic_mapping_fees AS doctor_fees,
                                        clinic_id AS clinic_id,
                                        clinic_name AS clinic_name,
                                        clinic_email AS clinic_email,
                                        clinic_contact_number AS clinic_contact_number,
                                        address_name AS clinic_address,
                                        address_latitude AS clinic_latitude,
                                        address_longitude AS clinic_longitude,
                                        user_phone_number AS doctor_phone_number,
                                        '' AS patient_prescription_doctor_name,
                                        '' AS patient_prescription_clinic_name,
                                        '' As created_by
                                    FROM
                                        " . TBL_USERS . " 
                                    LEFT JOIN
                                        " . TBL_APPOINTMENTS . " 
                                    ON 
                                        appointment_doctor_user_id=user_id AND appointment_status=1
                                    JOIN 
                                        " . TBL_CLINICS . "  
                                    ON  
                                        clinic_id=appointment_clinic_id AND clinic_status=1
                                    JOIN 
                                        " . TBL_DOCTOR_CLINIC_MAPPING . "  
                                    ON  
                                        doctor_clinic_mapping_clinic_id=clinic_id AND doctor_clinic_mapping_user_id=user_id AND doctor_clinic_mapping_status=1
                                    LEFT JOIN 
                                        " . TBL_ADDRESS . "  
                                    ON  
                                        address_user_id=clinic_id AND address_status=1 AND address_type=2
                                    LEFT JOIN 
                                        " . TBL_DOCTOR_DETAILS . " 
                                    ON  
                                        doctor_detail_doctor_id=user_id AND doctor_detail_status=1         
                                    LEFT JOIN  
                                        " . TBL_DOCTOR_SPECIALIZATIONS . "  
                                    ON  
                                        doctor_specialization_doctor_id=user_id AND doctor_specialization_status=1
                                    LEFT JOIN  
                                        " . TBL_DOCTOR_EDUCATIONS . "  
                                    ON  
                                        doctor_qualification_user_id=user_id AND doctor_qualification_status=1
                                    LEFT JOIN 
                                        " . TBL_SPECIALISATIONS . "  
                                    ON  
                                        doctor_specialization_specialization_id=specialization_id AND specialization_status=1
                                    LEFT JOIN
                                        " . TBL_LAB_REPORTS . " ON appointment_id = lab_report_appointment_id AND lab_report_status = 1
                                    LEFT JOIN
                                        " . TBL_PROCEDURE_REPORTS . " ON appointment_id = procedure_report_appointment_id AND procedure_report_status = 1
                                    LEFT JOIN
                                        " . TBL_CLINICAL_REPORTS . " ON appointment_id  = clinical_notes_reports_appointment_id AND clinical_notes_reports_status = 1
                                    LEFT JOIN
                                        " . TBL_FILE_REPORTS . " ON appointment_id = file_report_appointment_id AND file_report_status = 1
                                    LEFT JOIN
                                        " . TBL_PRESCRIPTION_REPORTS . " ON appointment_id = prescription_appointment_id AND prescription_status = 1
                                    LEFT JOIN
                                        " . TBL_VITAL_REPORTS . " ON appointment_id = vital_report_appointment_id AND vital_report_status = 1
                                    WHERE
                                        appointment_user_id = '" . $request_data['patient_id'] . "' AND
                                        ( 
                                            lab_report_appointment_id IS NOT NULL OR
                                            procedure_report_appointment_id IS NOT NULL OR
                                            clinical_notes_reports_appointment_id IS NOT NULL OR
                                            file_report_appointment_id IS NOT NULL OR
                                            prescription_appointment_id IS NOT NULL OR
                                            vital_report_appointment_id IS NOT NULL 
                                        )   
                                    GROUP BY 
                                        appointment_id
                                UNION
                                    SELECT
                                        patient_prescription_user_id AS doctor_user_id,
                                        user_first_name AS doctor_first_name,
                                        user_last_name AS doctor_last_name,
                                        '' AS doctor_photo,
                                        '' AS doctor_experience,
                                        '' AS doctor_specialisation,
                                        '' AS doctor_qualification,
                                        patient_prescription_id AS appointment_id,
                                        '' AS appointment_from_time,
                                        patient_prescription_date AS appointment_date,
                                        '' AS appointment_type,
                                        '' AS doctor_fees,
                                        '' AS clinic_id,
                                        '' AS clinic_name,
                                        '' AS clinic_email,
                                        '' AS clinic_contact_number,
                                        '' AS clinic_address,
                                        '' AS clinic_latitude,
                                        '' AS clinic_longitude,
                                        '' AS doctor_phone_number,
                                        patient_prescription_doctor_name AS patient_prescription_doctor_name,
                                        patient_prescription_clinic_name AS patient_prescription_clinic_name,
                                        patient_prescription_created_by As created_by
                                    FROM
                                        " . TBL_PATIENT_PRESCRIPTION . "
                                    LEFT JOIN
                                        " . TBL_USERS . " ON patient_prescription_created_by = user_id
                                    WHERE
                                        patient_prescription_user_id = '" . $request_data['patient_id'] . "'   
                                    AND
                                        patient_prescription_status != 9
                                ";
        $prescription_query.="  ORDER BY appointment_date DESC, appointment_id DESC ";
        $get_prescription_data = $this->Common_model->get_all_rows_by_query($prescription_query);
        if (empty($request_data['page'])) {
            return $this->get_count_by_query($prescription_query);
        }
        $limit = " LIMIT " . (($request_data['page'] - 1) * $request_data['per_page']) . "," . $request_data['per_page'] . " ";
        $prescription_query.=$limit;
        $get_prescription_data = $this->get_all_rows_by_query($prescription_query);
        return $get_prescription_data;
    }

    public function get_doctor_drug($where, $count = false) {
        $colums = 'drug_id, 
                   drug_name,
                   drug_instruction,
                   drug_strength,
                   drug_unit_name,
                   drug_unit_medicine_type';
        $this->db->select($colums);
        $this->db->from(TBL_DRUGS);
        $this->db->join(TBL_DRUG_UNIT, 'drug_drug_unit_id = drug_unit_id', "LEFT");
        $this->db->where('drug_user_id', $where['doctor_id']);
        $this->db->where('drug_status', 1);
        if(!empty($where['search']))
            $this->db->like('drug_name', $where['search']);
        $this->db->order_by('drug_id', 'DESC');
        if(!$count)
            $this->db->limit($where['per_page'], (($where['page'] - 1) * $where['per_page']));
        $query = $this->db->get();
        if($count)
            return $query->num_rows();
        else
            return $query->result();
    }
}