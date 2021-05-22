<?php

class Import_clinical_notes extends MY_Model {

    protected $import_clinical_notes_table;
    protected $doctor_import_table;

    function __construct() {
        parent::__construct();
        $this->import_clinical_notes_table = 'me_import_clinical_notes_reports';
        $this->doctor_import_table = 'me_doctor_import';
    }

    public function get_clinical_notes($where, $columns = '*') {
        if(!empty($where) && count($where) > 0) {
            $this->db->select($columns)->from($this->import_clinical_notes_table . ' cn');
            $this->db->join($this->doctor_import_table . ' d', 'd.import_file_id=cn.import_file_id');
            foreach ($where as $key => $value) {
                $this->db->where($key, $value);
            }
            $query = $this->db->get();
            return $query->result();
        } else {
            return array();
        }
    }

}