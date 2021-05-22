<?php

class Import_prescription_reports extends MY_Model {

    protected $import_prescription_reports_table;
    protected $doctor_import_table;

    function __construct() {
        parent::__construct();
        $this->import_prescription_reports_table = 'me_import_prescription_reports';
        $this->doctor_import_table = 'me_doctor_import';
    }

    public function get_prescription_reports($where, $columns = '*') {
        if(!empty($where) && count($where) > 0) {
            $this->db->select($columns)->from($this->import_prescription_reports_table . ' pr');
            $this->db->join($this->doctor_import_table . ' d', 'd.import_file_id=pr.import_file_id');
            $this->db->join('me_import_patients p', 'p.patient_unique_id=pr.patient_unique_id AND p.import_file_id=pr.import_file_id');
            foreach ($where as $key => $value) {
                $this->db->where($key, $value);
            }
            $query = $this->db->get();
            return $query->result();
        } else {
            return array();
        }
    }

    public function search_drug($drug_name,$drug_unit) {
        $columns = "d.*,u.drug_unit_name";
        $this->db->select($columns)->from('me_drugs d');
        $this->db->join('me_drug_units u', 'u.drug_unit_id=drug_drug_unit_id', 'LEFT');
        $this->db->like('LOWER(d.drug_name)', strtolower($drug_name));
        $this->db->like('LOWER(d.drug_name_with_unit)', strtolower($drug_unit));
        $query = $this->db->get();
        return $query->result();
    }

    public function create_drug($data) {
        $insert_drug = array(
            'drug_name' => strtoupper($data->drug_name),
            'drug_name_with_unit' => strtoupper($data->drug_name.' ('.$data->drug_type.')'),
            'drug_user_id' => $data->import_file_doctor_id,
            'drug_color_code' => random_color(),
            'drug_drug_generic_id' => $this->search_generic($data->generic_name),
            'drug_drug_unit_id' => $this->search_drug_unit_id($data->drug_type),
            'drug_created_at' => $this->utc_time_formated
        );
        $this->db->insert('me_drugs', $insert_drug);
        $drug_id = $this->db->insert_id();
        if(!empty($drug_id)) {
            $columns = "d.*,u.drug_unit_name";
            $this->db->select($columns)->from('me_drugs d');
            $this->db->join('me_drug_units u', 'u.drug_unit_id=drug_drug_unit_id', 'LEFT');
            $this->db->where('drug_id', $drug_id);
            $query = $this->db->get();
            return $query->result();
        } else {
            return array();
        }
    }

    public function search_generic($generic_name) {
        if(!empty($generic_name)) {
            $generic_name_arr = array();
            foreach (explode(',', $generic_name) as $key => $value) {
                $generic_name_arr[] = strtolower(trim($value));
            }
            if(count($generic_name_arr) > 0) {
                $this->db->select('drug_generic_id')->from('me_drug_generic');
                $this->db->where_in('LOWER(drug_generic_title)', $generic_name_arr);
                $query = $this->db->get();

                $rs = $query->result_array();
                if(count($rs) > 0) {
                    return implode(',',  array_column($rs, 'drug_generic_id'));
                }
            }
        }
        return '';
    }

    public function search_drug_unit_id($drug_unit) {
        if(!empty($drug_unit)) {
            
            $this->db->select('drug_unit_id')->from('me_drug_units');
            $this->db->where_in('LOWER(drug_unit_medicine_type)', strtolower($drug_unit));
            $query = $this->db->get();

            $rs = $query->row();
            if(count($rs) > 0) {
                return $rs->drug_unit_id;
            }
            
        }
        return '';
    }

    public function get_analytics_values($where, $columns = '*') {
        $this->db->select($columns)->from('me_import_analytics_values a');
        $this->db->join($this->doctor_import_table . ' d', 'd.import_file_id=a.import_file_id');
        $this->db->join('me_import_patients p', 'p.patient_unique_id=a.patient_unique_id AND p.import_file_id=a.import_file_id');
        foreach ($where as $key => $value) {
            $this->db->where($key, $value);
        }
        $query = $this->db->get();
        return $query->result();
        
    }

    public function get_health_analytics_test($lab_test) {
        $this->db->select('*')->from('me_health_analytics_test');
        $this->db->where('LOWER(health_analytics_test_name_precise)', strtolower($lab_test));
        $this->db->where('health_analytics_test_parent_id >', 0);
        $query = $this->db->get();
        return $query->row();
    }

    public function get_reports($where, $columns = '*') {
        $this->db->select($columns)->from('me_import_reports r');
        $this->db->join($this->doctor_import_table . ' d', 'd.import_file_id=r.import_file_id');
        $this->db->join('me_import_patients p', 'p.patient_unique_id=r.patient_unique_id AND p.import_file_id=r.import_file_id');
        foreach ($where as $key => $value) {
            $this->db->where($key, $value);
        }
        $query = $this->db->get();
        return $query->result();
        
    }

}