<?php

class Doctor_import extends MY_Model {

    protected $doctor_import_table;

    function __construct() {
        parent::__construct();
        $this->doctor_import_table = TBL_DOCTOR_IMPORT_TABLE;
    }

    public function get_doctor_import_file_data_by_id($import_file_id, $columns = '*') {
        $this->db->select($columns)->from($this->doctor_import_table);
        $this->db->where('import_file_id', $import_file_id);
        $query = $this->db->get();
        return $query->row();
    }

    public function get_doctor_import_file_data_by_where($where, $columns = '*') {
        if(!empty($where) && is_array($where) && count($where) > 0) {
            $this->db->select($columns)->from($this->doctor_import_table);
            foreach ($where as $key => $value) {
                $this->db->where($key, $value);
            }
            $query = $this->db->get();
            return $query->result();
        } else {
            return array();
        }
    }

    public function get_doctor_import_file_data($where, $columns = 'd.*') {
        if(!empty($where) && is_array($where) && count($where) > 0) {
            $this->db->select($columns)->from($this->doctor_import_table . ' d');
            $this->db->join('me_import_file_type_master ift', 'ift.import_file_type_id=d.import_file_type_id');
            foreach ($where as $key => $value) {
                $this->db->where($key, $value);
            }
            $query = $this->db->get();
            return $query->result();
        } else {
            return array();
        }
    }

    public function create_doctor_import($data) {
        if (!empty($data)) {
            $insert_id = $this->insert($this->doctor_import_table, $data);
            return $insert_id;
        }
        return false;
    }

    public function update_doctor_import($import_file_id, $data) {
        if (!empty($data) && !empty($import_file_id)) {
            $this->db->where('import_file_id', $import_file_id);
            $this->db->update($this->doctor_import_table, $data);
            return true;
        }
        return false;
    }

    public function bulk_update($table_name, $data, $on_update_column) {
        $this->db->update_batch($table_name, $data, $on_update_column);
    }

    public function get_import_file_type($where, $columns = '*') {
        if(!empty($where) && is_array($where) && count($where) > 0) {
            $this->db->select($columns)->from('me_import_file_type_master');
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