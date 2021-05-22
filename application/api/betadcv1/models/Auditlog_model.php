<?php

class Auditlog_model extends MY_Model {

    protected $auditlog_table;
    
    function __construct() {
        parent::__construct();
        $this->auditlog_table = TBL_AUDIT_LOG;
    }

    /**
     * Description :- This function is used to create audit log
     * 
     * 
     * @param $user_id $user_type $action_slug_name $old_data $new_data $table_name $table_primary_key_name $table_primary_key_value
     * @return boolean
     */

    public function create_audit_log($user_id, $user_type, $action_slug_name, $old_data = array(), $new_data = array(), $table_name = NULL, $table_primary_key_name = NULL, $table_primary_key_value = NULL, $other_data = array()) {
        $this->load->library('user_agent');
        if ($this->agent->is_browser())
        {
            $agent = $this->agent->browser().' '.$this->agent->version();
        }
        elseif ($this->agent->is_robot())
        {
            $agent = $this->agent->robot();
        }
        elseif ($this->agent->is_mobile())
        {
            $agent = $this->agent->mobile();
        }
        else
        {
            $agent = 'Unidentified User Agent';
        }
        $user_agent = "Platform: ".$this->agent->platform() . ", User Agent: " . $agent;
        $audit_data = array(
            'action_slug_name' => $action_slug_name,
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $user_agent,
            'user_id' => $user_id,
            'user_type' => $user_type,
            'audit_created_at' => $this->utc_time_formated,
        );
        if(is_array($other_data) && count($other_data) > 0) {
            $audit_data['other_data'] = json_encode($other_data);
        }
        if(count($old_data) > 0 && count($new_data) > 0) {
            $arr_key = $this->array_first_key($old_data);
            $arr_key2 = $this->array_first_key($new_data);
            $is_insert = false;
            if(is_array($old_data[$arr_key]) || is_array($old_data[$arr_key2])) { // check array is two dimensional
                $diff_data = $this->array_diff_assoc_recursive($old_data,$new_data);

                // print_r($old_data);
                // print_r($new_data);
                // print_r($diff_data);
                // die;
                $audit_data['table_old_value'] = json_encode($old_data);
                $audit_data['table_new_value'] = json_encode($new_data);
                if(count($old_data) != count($new_data))
                    $is_insert = true;
                
            } else {
                $diff_data = $this->check_data_new($old_data, $new_data);
                $audit_data['table_old_value'] = json_encode($diff_data['old_data']);
                $audit_data['table_new_value'] = json_encode($diff_data['new_data']);
            }
            if((is_array($diff_data) && count($diff_data) > 0) || $is_insert) {
                $audit_data['table_name'] = $table_name;
                $audit_data['table_primary_key_name'] = $table_primary_key_name;
                $audit_data['table_primary_key_value'] = $table_primary_key_value;
                
                $insert_id = $this->insert($this->auditlog_table, $audit_data);
                //print_r($audit_data);die;
            }
            return TRUE;
        } else {
            if(count($new_data) > 0) {
                $audit_data['table_new_value'] = json_encode($new_data);
            }
            $audit_data['table_name'] = $table_name;
            $audit_data['table_primary_key_name'] = $table_primary_key_name;
            $audit_data['table_primary_key_value'] = $table_primary_key_value;
        }
        $insert_id = $this->insert($this->auditlog_table, $audit_data);
        return TRUE;
    }


    function array_first_key(array $arr) {
        foreach($arr as $key => $unused) {
            return $key;
        }
        return NULL;
    }

    public function check_data_new($old_data, $new_data) {
        $return_data = array();
        foreach ($new_data as $key => $value) {
            if($old_data[$key] != $value) {
                $return_data['new_data'][$key] = $value;
                $return_data['old_data'][$key] = $old_data[$key];
            }
        }
        return $return_data;
    }

    public function array_diff_assoc_recursive($array1, $array2) {
        foreach($array1 as $key => $value)
        {
            if(is_array($value))
            {
                if(!isset($array2[$key]))
                {
                    $difference[$key] = $value;
                }
                elseif(!is_array($array2[$key]))
                {
                    $difference[$key] = $value;
                }
                else
                {
                    $new_diff = $this->array_diff_assoc_recursive($value, $array2[$key]);
                    if($new_diff != FALSE)
                    {
                        $difference[$key] = $new_diff;
                    }
                }
            }
            elseif(!isset($array2[$key]) || $array2[$key] != $value)
            {
                $difference[$key] = $value;
            }
        }
        return !isset($difference) ? 0 : $difference;
    }

    public function check_data($old_data, $new_data) {
        $return_data = array();
        foreach ($new_data as $key => $value) {
            if($old_data[$key] != $value) {
                $return_data['new_data'][$key] = $value;
                $return_data['old_data'][$key] = $old_data[$key];
            }
        }
        return $return_data;
    }

    public function get_audit_log($where, $columns = '*', $other_data = '') {
        if(is_array($where) && count($where) > 0) {
            $this->db->select($columns)->from($this->auditlog_table);
            foreach ($where as $key => $value) {
                $this->db->where($key, $value);
            }
            if(!empty($other_data)) {
                $this->db->like('other_data', $other_data);
            }
            $this->db->order_by('audit_created_at', 'DESC');
            $query = $this->db->get();
            return $query->result();
        } else {
            return array();
        }

    }

    public function get_last_audit_log($where) {
        if(is_array($where) && count($where) > 0) {
            $this->db->select('audit_created_at')->from($this->auditlog_table);
            foreach ($where as $key => $value) {
                $this->db->where($key, $value);
            }
            $this->db->order_by('audit_created_at', 'DESC');
            $this->db->limit(1);
            $query = $this->db->get();
            return $query->row();
        } else {
            return array();
        }
    }

    
}