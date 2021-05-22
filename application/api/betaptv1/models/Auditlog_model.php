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
    public function create_audit_log($user_id, $user_type, $action_slug_name, $old_data = array(), $new_data = array(), $table_name = NULL, $table_primary_key_name = NULL, $table_primary_key_value = NULL) {
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
            $agent = false;
        }
        if($agent) {
            $user_agent = "Platform: ".$this->agent->platform() . ", User Agent: " . $agent;
        } else {
            $user_agent = "Platform: ".$_REQUEST['device_type'] . ", User Agent: " . $this->agent->agent;
        }
        $audit_data = array(
            'action_slug_name' => $action_slug_name,
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $user_agent,
            'user_id' => $user_id,
            'user_type' => $user_type,
            'audit_created_at' => $this->utc_time_formated
        );

        if(count($old_data) > 0 && count($new_data)) {
            $diff_data = $this->check_data($old_data, $new_data);
            if(count($diff_data) > 0) {
                $audit_data['table_name'] = $table_name;
                $audit_data['table_primary_key_name'] = $table_primary_key_name;
                $audit_data['table_primary_key_value'] = $table_primary_key_value;
                $audit_data['table_old_value'] = json_encode($diff_data['old_data']);
                $audit_data['table_new_value'] = json_encode($diff_data['new_data']);
                $insert_id = $this->insert($this->auditlog_table, $audit_data);
            }
            return TRUE;
        }
        $insert_id = $this->insert($this->auditlog_table, $audit_data);
        return TRUE;
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

    
}