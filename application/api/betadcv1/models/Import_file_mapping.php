<?php

class Import_file_mapping extends MY_Model {

    protected $import_file_mapping_table;
    protected $import_file_type_master_table;

    function __construct() {
        parent::__construct();
        $this->import_file_mapping_table = TBL_IMPORT_FILE_MAPPING_TABLE;
        $this->import_file_type_master_table = TBL_IMPORT_FILE_TYPE_MASTER;
    }

    public function get_import_file_mapping_by_type_id($import_file_type_id, $columns = '*') {
        $this->db->select($columns)->from($this->import_file_mapping_table . ' ifm');
        $this->db->join($this->import_file_type_master_table . ' itm', "itm.import_file_type_id=ifm.import_file_type_id");
        $this->db->where('ifm.import_file_type_id', $import_file_type_id);
        $query = $this->db->get();
        return $query->result();
    }
    
}