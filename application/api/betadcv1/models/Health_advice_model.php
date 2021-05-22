<?php

class Health_advice_model extends MY_Model {

    function __construct() {
        parent::__construct();
    }

    public function get_health_advice_groups() {
    	$this->db->select("hag.health_advice_group_id,hag.health_advice_group_name,ha.health_advice_id")->from('me_health_advice_groups hag');
        $this->db->join("me_health_advice ha", "hag.health_advice_group_id=ha.health_advice_group_id", "LEFT");
    	$this->db->where('hag.health_advice_group_status', 1);
        $this->db->where('ha.health_advice_id IS NOT NULL');
        $this->db->group_by("hag.health_advice_group_id");
    	$query = $this->db->get();
    	return $query->result();
    }

    public function get_health_advice($health_advice_group_id) {
    	$this->db->select("COUNT(ha.health_advice_id) AS total_advice, hag.health_advice_group_name")->from('me_health_advice ha');
    	$this->db->join("me_health_advice_groups hag", "hag.health_advice_group_id=ha.health_advice_group_id");
    	$this->db->where('ha.health_advice_status', 1);
    	$this->db->where('ha.health_advice_group_id', $health_advice_group_id);
    	$query = $this->db->get();
    	return $query->row();
    }

	public function get_health_advice_assigned($patient_id) {
    	$this->db->select("pha.patient_health_advice_group_id, hag.health_advice_group_name")->from('me_patients_health_advice pha');
    	$this->db->join("me_health_advice_groups hag", "hag.health_advice_group_id=pha.patient_health_advice_group_id");
    	$this->db->where('pha.patient_health_advice_status', 1);
    	$this->db->where('pha.patient_health_advice_end_date IS NULL');
    	$this->db->where('pha.patient_health_advice_patient_id', $patient_id);
    	$query = $this->db->get();
    	return $query->result();
    }

    public function is_health_advice_assigned($patient_id, $group_id) {
        $this->db->select("patient_health_advice_group_id")->from('me_patients_health_advice');
        $this->db->where('patient_health_advice_status', 1);
        $this->db->where('patient_health_advice_end_date IS NULL');
        $this->db->where('patient_health_advice_patient_id', $patient_id);
        $this->db->where('patient_health_advice_group_id', $group_id);
        $query = $this->db->get();
        if($query->num_rows() > 0)
            return true;
        else
            return false;
    }

}