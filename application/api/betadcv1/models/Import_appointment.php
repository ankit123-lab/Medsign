<?php

class Import_appointment extends MY_Model {

    protected $import_appointment_table;
    protected $import_investigations;
    protected $import_patients_table;
    protected $doctor_import_table;
    protected $doctor_clinic_mapping_table;

    function __construct() {
        parent::__construct();
        $this->import_appointment_table = 'me_import_appointment';
        $this->import_investigations = 'me_import_investigations';
        $this->import_patients_table = 'me_import_patients';
        $this->doctor_import_table = 'me_doctor_import';
        $this->doctor_clinic_mapping_table = 'me_doctor_clinic_mapping';
    }

    public function get_import_appointment($where, $columns = '*') {
        if(!empty($where) && count($where) > 0) {
            $this->db->select($columns)->from($this->import_appointment_table . ' a');
            $this->db->join($this->import_patients_table . ' p', 'p.patient_unique_id=a.patient_unique_id AND p.import_file_id=a.import_file_id');
            $this->db->join($this->doctor_import_table . ' d', 'd.import_file_id=a.import_file_id');
            foreach ($where as $key => $value) {
                $this->db->where($key, $value);
            }
            $query = $this->db->get();
            return $query->result();
        } else {
            return array();
        }
    }

    public function get_import_investigations($where, $columns = '*') {
        if(!empty($where) && count($where) > 0) {
            $this->db->select($columns)->from($this->import_investigations . ' i');
            $this->db->join($this->import_patients_table . ' p', 'p.patient_unique_id=i.patient_unique_id AND p.import_file_id=i.import_file_id');
            $this->db->join($this->doctor_import_table . ' d', 'd.import_file_id=i.import_file_id');
            foreach ($where as $key => $value) {
                $this->db->where($key, $value);
            }
            $query = $this->db->get();
            return $query->result();
        } else {
            return array();
        }
    }

    public function get_doctor_clinic_mapping($where, $columns = '*') {
        $this->db->select($columns)->from($this->doctor_clinic_mapping_table);
        foreach ($where as $key => $value) {
            $this->db->where($key, $value);
        }
        $this->db->where('doctor_clinic_mapping_role_id', 1);
        $this->db->where('doctor_clinic_mapping_status', 1);
        $query = $this->db->get();
        return $query->row();
    }

}