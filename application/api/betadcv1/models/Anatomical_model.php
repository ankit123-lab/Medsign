<?php

class Anatomical_model extends MY_Model {

    function __construct() {
        parent::__construct();
    }

    public function get_anatomical_diagrams($where_data, $start, $limit, $count = false) {
        $doctor_id = $where_data['doctor_id'];
        $doctor_specialisation_ids = $where_data['doctor_specialisation_ids'];
        $doctor_qua_ids = $where_data['doctor_qua_ids'];
        $this->db->select("
            d.anatomical_diagrams_id,
            d.anatomical_diagrams_title,
            d.anatomical_diagrams_keyword,
            d.anatomical_diagrams_desc,
			d.anatomical_diagrams_image_name,
            d.anatomical_diagrams_image_path,
            d.anatomical_diagrams_video_url,
            d.anatomical_diagrams_file_path,
            (SELECT GROUP_CONCAT(CONCAT(anatomical_category_name,'##', anatomical_category_id)) FROM me_anatomical_category WHERE FIND_IN_SET(anatomical_category_id,d.anatomical_diagrams_cat_id)) AS categories,
            (SELECT GROUP_CONCAT(anatomical_category_name) FROM me_anatomical_category WHERE FIND_IN_SET(anatomical_category_id,d.anatomical_diagrams_sub_cat_id)) AS sub_categories
            ")->from('me_anatomical_diagrams d');
        $this->db->join('me_assignment a', 'a.assignment_id=d.anatomical_diagrams_id', "LEFT");
        $this->db->where('d.anatomical_diagrams_status', 1);
        $this->db->where('a.assignment_type', 1);
        $doctor_specialisation_sql = "";
        if(!empty($doctor_specialisation_ids)) {
            $doctor_specialisation_sql = " OR (
                                CONCAT(',', `assignment_value`, ',') REGEXP ',(".$doctor_specialisation_ids."),' AND  
                                assignment_visible = 3)";
        }
        $doctor_qua_sql = "";
        if(!empty($doctor_qua_ids)) {
            $doctor_qua_sql = " OR (
                                CONCAT(',', `assignment_value`, ',') REGEXP ',(" . $doctor_qua_ids . "),' AND  
                                assignment_visible = 4)";
        }
        $this->db->where("((assignment_visible = 1) OR
                        (FIND_IN_SET($doctor_id, assignment_value) AND  
                        assignment_visible = 2) 
                        {$doctor_specialisation_sql} 
                        {$doctor_qua_sql})");
        if(!empty($where_data['medsign_speciality_id'])) {
             $find_where = '';
            foreach (explode(',', $where_data['medsign_speciality_id']) as $speciality_id) {
                $find_where .= "FIND_IN_SET(".$speciality_id.", d.anatomical_diagrams_medsign_speciality_id) OR ";
            }
            $this->db->where("(".trim($find_where, ' OR ').")");
        }
        if(!empty($where_data['search'])) {
            $this->db->group_start();
            $this->db->like("d.anatomical_diagrams_title",$where_data['search']);
            $this->db->or_like("d.anatomical_diagrams_keyword",$where_data['search']);
            $this->db->group_end();
        }
        if(!empty($where_data['category_id']) && count($where_data['category_id']) > 0) {
            $find_where = '';
            foreach ($where_data['category_id'] as $category_id) {
                $find_where .= "FIND_IN_SET(".$category_id.", d.anatomical_diagrams_cat_id) OR ";
            }
            $this->db->where("(".trim($find_where, ' OR ').")");
        }
        if(!empty($where_data['sub_category_id']) && count($where_data['sub_category_id']) > 0) {
            $find_where = '';
            foreach ($where_data['sub_category_id'] as $sub_category_id) {
                $find_where .= "FIND_IN_SET(".$sub_category_id.", d.anatomical_diagrams_sub_cat_id) OR ";
            }
            $this->db->where("(".trim($find_where, ' OR ').")");
        }
        $this->db->group_by(array('d.anatomical_diagrams_id'));
        if(!empty($where_data['medsign_speciality_id'])) {
            $this->db->order_by("FIELD(d.anatomical_diagrams_medsign_speciality_id, ".$where_data['medsign_speciality_id'].")");
        }
        $this->db->order_by('d.anatomical_diagrams_id', 'DESC');
        if(!$count)
            $this->db->limit($limit,$start);
        $query = $this->db->get();
        if($count) {
            return $query->num_rows();
        } else {
            return $query->result();
        }
    }

    public function get_category($where_data) {
        $doctor_id = $where_data['doctor_id'];
        $doctor_specialisation_ids = $where_data['doctor_specialisation_ids'];
        $doctor_qua_ids = $where_data['doctor_qua_ids'];
        $this->db->select("
            c.anatomical_category_id,
            c.anatomical_category_name
            ")->from('me_anatomical_category c');
        $this->db->join('me_anatomical_diagrams d', 'FIND_IN_SET(c.anatomical_category_id,d.anatomical_diagrams_cat_id) > 0');
        $this->db->join('me_assignment a', 'a.assignment_id=d.anatomical_diagrams_id', "LEFT");
        $this->db->where('d.anatomical_diagrams_status', 1);
        $this->db->where('a.assignment_type', 1);
        $doctor_specialisation_sql = "";
        if(!empty($doctor_specialisation_ids)) {
            $doctor_specialisation_sql = " OR (
                                CONCAT(',', `assignment_value`, ',') REGEXP ',(".$doctor_specialisation_ids."),' AND  
                                assignment_visible = 3)";
        }
        $doctor_qua_sql = "";
        if(!empty($doctor_qua_ids)) {
            $doctor_qua_sql = " OR (
                                CONCAT(',', `assignment_value`, ',') REGEXP ',(" . $doctor_qua_ids . "),' AND  
                                assignment_visible = 4)";
        }
        $this->db->where("((assignment_visible = 1) OR
                        (FIND_IN_SET($doctor_id, assignment_value) AND  
                        assignment_visible = 2) 
                        {$doctor_specialisation_sql} 
                        {$doctor_qua_sql})");
        if(!empty($where_data['medsign_speciality_id'])) {
             $find_where = '';
            foreach (explode(',', $where_data['medsign_speciality_id']) as $speciality_id) {
                $find_where .= "FIND_IN_SET(".$speciality_id.", d.anatomical_diagrams_medsign_speciality_id) OR ";
            }
            $this->db->where("(".trim($find_where, ' OR ').")");
        }
        $this->db->group_by(array('c.anatomical_category_id'));
        $this->db->order_by('c.anatomical_category_name');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_sub_category($where_data) {
        $this->db->select("anatomical_category_id,anatomical_category_name")->from('me_anatomical_category');
        $this->db->join('me_anatomical_diagrams', 'FIND_IN_SET(anatomical_category_id,anatomical_diagrams_sub_cat_id) > 0');

        $this->db->where('anatomical_category_status', 1);
        $this->db->where_in('anatomical_category_parent_id', $where_data['category_id']);
        if(!empty($where_data['medsign_speciality_id'])) {
             $find_where = '';
            foreach (explode(',', $where_data['medsign_speciality_id']) as $speciality_id) {
                $find_where .= "FIND_IN_SET(".$speciality_id.", anatomical_diagrams_medsign_speciality_id) OR ";
            }
            $this->db->where("(".trim($find_where, ' OR ').")");
        }
        $this->db->where('anatomical_diagrams_status', 1);
        $this->db->group_by(array('anatomical_category_id'));
        $this->db->order_by('anatomical_category_name');
        $query = $this->db->get();
        return $query->result();
    }

    
}