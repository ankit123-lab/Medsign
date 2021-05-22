<?php

class Teleconsultation_model extends MY_Model {

    function __construct() {
        parent::__construct();
    }

    public function get_teleconsultation_date($where, $count=false) {
        if(empty($where['doctor_id']))
            return array();

        $this->db->select("
                DATE_FORMAT(CONVERT_TZ(tch.created_at,'+00:00','+05:30'), '%Y-%m-%d') as created_at,
                DATE_FORMAT(CONVERT_TZ(tch.created_at,'+00:00','+05:30'), '%d/%m/%Y') as title_date
            ");
        $this->db->from('me_teleconsultant_call_history tch');
        $this->db->join('me_appointments a', 'a.appointment_id=tch.appointment_id');
        $this->db->where("tch.doctor_id", $where['doctor_id']);
        $this->db->where("a.appointment_clinic_id", $where['clinic_id']);
        $this->db->group_by('DATE(tch.created_at)');
        $this->db->order_by('DATE(tch.created_at)' , 'DESC');
        if(!$count)
            $this->db->limit($where['per_page'], (($where['page'] - 1) * $where['per_page']));
        $query = $this->db->get();
        // echo $this->db->last_query();die;
        if($count)
            return $query->num_rows();
        else
            return $query->result();
    }

    public function get_teleconsultation_list($where, $count=false) {
        if(empty($where['doctor_id']))
            return array();

        $this->db->select("
                ch.call_duration_time,
                CONCAT(p.user_first_name, ' ', p.user_last_name) as patient_name,
                p.user_phone_number,
                DATE_FORMAT(CONCAT(a.appointment_date, ' ', a.appointment_from_time), '%d/%m/%Y %h:%i %p') as appointment_date_time,
                DATE_FORMAT(CONVERT_TZ(ch.created_at,'+00:00','+05:30'), '%d/%m/%Y %h:%i %p') as created_at
                
            ");
        $this->db->from('me_teleconsultant_call_history ch');
        $this->db->join("me_appointments a", "a.appointment_id = ch.appointment_id", "LEFT");
        $this->db->join("me_users p", "p.user_id = ch.patient_id", "LEFT");
        $this->db->where("ch.doctor_id", $where['doctor_id']);
        $this->db->where("a.appointment_clinic_id", $where['clinic_id']);
        $this->db->where("DATE(CONVERT_TZ(ch.created_at,'+00:00','+05:30'))", $where['date']);
        $this->db->group_by('a.appointment_id');
        $this->db->order_by('ch.created_at' , 'DESC');
        if(!$count)
            $this->db->limit($where['per_page'], (($where['page'] - 1) * $where['per_page']));
        $query = $this->db->get();
        // echo $this->db->last_query();die;
        if($count)
            return $query->num_rows();
        else
            return $query->result();
    }

    public function get_global_setting_by_key_arr($key_arr) {
        $this->db->select('global_setting_key,global_setting_value');
        $this->db->from('me_global_settings');
        $this->db->where('global_setting_status', 1);
        $this->db->where_in('global_setting_key', $key_arr);
        $query = $this->db->get();
        return $query->result_array();
    }

}