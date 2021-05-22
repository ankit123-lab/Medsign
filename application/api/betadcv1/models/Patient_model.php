<?php

class Patient_model extends MY_Model {
    
    function __construct() {
        parent::__construct();
    }
    
    public function get_patient_past_rx($where_data, $columns) {
    	$this->db->select($columns)->from('me_prescription_reports pr');
    	$this->db->join('me_appointments a','a.appointment_id=pr.prescription_appointment_id');
    	$this->db->join('me_users u','u.user_id=pr.prescription_doctor_user_id');
    	$this->db->where('pr.prescription_user_id', $where_data['patient_id']);
    	$this->db->where('pr.prescription_doctor_user_id !=', $where_data['doctor_id']);
    	$this->db->where('pr.prescription_status', 1);
    	$this->db->group_by('pr.prescription_appointment_id');
    	$this->db->order_by('pr.prescription_id', 'DESC');
    	$this->db->limit(5);
    	$query = $this->db->get();
    	return $query->result();
    }

    public function is_other_rx_available($where_data) {
        $this->db->select('prescription_appointment_id')->from('me_prescription_reports');
        $this->db->where('prescription_user_id', $where_data['patient_id']);
        $this->db->where('prescription_doctor_user_id !=', $where_data['doctor_id']);
        $this->db->where('prescription_status', 1);
        $query = $this->db->get();
        if($query->num_rows() > 0)
            return true;
        else
            return false;
    }
    
    public function get_uploaded_rx($where_data, $columns) {
        $this->db->select($columns)->from(TBL_RX_UPLOAD_REPORTS);
        $this->db->join(TBL_RX_UPLOAD_REPORTS_IMAGES,'rx_upload_report_id=rx_upload_id');
        $this->db->where('rx_upload_user_id', $where_data['patient_id']);
        $this->db->where('rx_upload_doctor_user_id', $where_data['doctor_id']);
        $this->db->where('rx_upload_appointment_id', $where_data['appointment_id']);
        $this->db->where('rx_upload_clinic_id', $where_data['clinic_id']);
        $this->db->where('rx_upload_status', 1);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_uas7_param_by_date($date_arr, $patient_id) {
        $this->db->select("patient_diary_id,patient_diary_label,patient_diary_date");
        $this->db->from('me_patient_diary');
        $this->db->where('patient_diary_patient_id', $patient_id);
        $this->db->where('patient_diary_status', 1);
        $this->db->where('patient_diary_type', 1);
        $this->db->where_in('patient_diary_date', $date_arr);
        $this->db->order_by('patient_diary_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    
    public function uas7_bulk_update($data) {
        $this->db->update_batch('me_patient_diary',$data, 'patient_diary_id'); 
    }

    public function get_uas7_para_data($where) {
        $this->db->select("
            patient_diary_patient_id,
            patient_diary_added_by,
            patient_diary_date,
            patient_diary_created_at,
            GROUP_CONCAT(pd.patient_diary_id) AS patient_diary_id,
            GROUP_CONCAT(pd.patient_diary_label) AS patient_diary_label,
            GROUP_CONCAT(pd.patient_diary_value) AS patient_diary_value
            ");
        $this->db->from('me_patient_diary pd');
        $this->db->where('pd.patient_diary_patient_id', $where['patient_id']);
        $this->db->where('pd.patient_diary_type', 1);
        $this->db->where('pd.patient_diary_status', 1);
        $this->db->group_by('pd.patient_diary_date');
        $this->db->order_by('pd.patient_diary_date', "DESC");
        $query = $this->db->get();
        return $query->result();
        
    }
    public function search_caretaker($where) {
        $this->db->select("user_id,user_first_name,user_last_name,user_phone_number,CONCAT(user_first_name,' ',user_last_name) AS user_name")->from('me_users');
        $this->db->where('user_type', 1);
        $this->db->where('user_status', 1);
        $this->db->like('user_phone_number', $where['search']);
        $this->db->order_by("CONCAT(user_first_name,' ',user_last_name)");
        $this->db->limit(50);
        $query = $this->db->get();
        return $query->result();
    }

    public function check_patient_reminder_access($patient_id) {
        $this->db->select("
            u.user_id,
            u.user_plan_expiry_date,
            us.setting_value,
            pfm.parent_patient_id,
            cru.user_plan_expiry_date AS caregiver_expiry_date,
            crus.setting_value AS caregiver_setting_value,
        ")->from('me_users u');
        $this->db->join("me_doctors_global_setting us","us.doctor_id=u.user_id AND us.setting_name='reminder' AND us.setting_status=1","LEFT");
        $this->db->join("me_patient_family_member_mapping pfm","pfm.patient_id=u.user_id AND pfm.mapping_status=1","LEFT");
        $this->db->join("me_users cru","pfm.parent_patient_id=cru.user_id AND cru.user_type=1 AND cru.user_status=1","LEFT");
        $this->db->join("me_doctors_global_setting crus","crus.doctor_id=cru.user_id AND crus.setting_name='reminder' AND crus.setting_status=1","LEFT");
        $this->db->where('u.user_id', $patient_id);
        $this->db->where('u.user_type', 1);
        $this->db->where('u.user_status', 1);
        $query = $this->db->get();
        return $query->result();
    }
}
