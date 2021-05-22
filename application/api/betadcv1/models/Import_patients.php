<?php

class Import_patients extends MY_Model {

    protected $import_patients_table;
    protected $doctor_import_table;
    protected $users_table;
    protected $city_table;

    function __construct() {
        parent::__construct();
        $this->import_patients_table = 'me_import_patients';
        $this->doctor_import_table = 'me_doctor_import';
        $this->users_table = 'me_users';
        $this->city_table = 'me_city';
    }

    public function get_import_patients($where, $columns = '*') {
        if(!empty($where) && count($where) > 0) {
            $this->db->select($columns)->from($this->import_patients_table . ' p');
            $this->db->join($this->doctor_import_table . ' d', 'd.import_file_id=p.import_file_id');
            foreach ($where as $key => $value) {
                $this->db->where($key, $value);
            }
            $this->db->where("mobile_number != ''");
            $this->db->group_by("mobile_number");
            $this->db->having("total", 1);
            $query = $this->db->get();
            // echo $this->db->last_query();die;
            return $query->result();
        } else {
            return array();
        }
    }

    public function get_existing_users($mobile_number_arr, $email_arr, $columns = '*') {

        $sql_where = '';
        if(count($mobile_number_arr) > 0 && count($email_arr) > 0) {
            $email_ids = "'".implode("','", $email_arr)."'";
            $sql_where = '(user_phone_number IN ('.implode(",", $mobile_number_arr).') OR user_email IN ('.$email_ids.'))';
        } elseif(count($mobile_number_arr) > 0) {
            $sql_where = '(user_phone_number IN ('.implode(",", $mobile_number_arr).'))';
        } elseif(count($email_arr) > 0) {
            $email_ids = "'".implode("','", $email_arr)."'";
            $sql_where = '(user_email IN ('.$email_ids.'))';
        }
        if(empty($sql_where))
            return array();

        $this->db->select($columns)->from($this->users_table);
        $this->db->where($sql_where);

        $this->db->where('user_status !=', 9);
        $this->db->where('user_type', 1);

        $query = $this->db->get();
        return $query->result_array();
    }

    function get_next_auto_id($table_name) {
        $next = $this->db->query("SHOW TABLE STATUS LIKE '" . $table_name . "'");
        $next = $next->row(0);
        $next->Auto_increment;
        return $next->Auto_increment;
    }
    
    public function get_city() {
        $this->db->select('city_id,city_state_id,city_name')->from($this->city_table);
        $this->db->where('city_status', 1);
        // $this->db->limit(10);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_languages() {
        $this->db->select('language_id,language_name')->from('me_languages');
        $this->db->where('language_status', 1);
        $query = $this->db->get();
        return $query->result_array();
    }
}