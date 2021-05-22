<?php

class Report_model extends MY_Model {

    function __construct() {
        parent::__construct();
    }

    public function get_member_summary($where, $count=false) {
        $this->db->select("
                IF(u.user_patient_id IS NULL,u.user_unique_id,u.user_patient_id) AS user_unique_id,
                u.user_status,
                CONCAT(u.user_first_name, ' ', u.user_last_name) as user_name,
                c.city_name,
                u.user_phone_number,
                u.user_email,
                DATE_FORMAT(CONVERT_TZ(u.user_created_at,'+00:00','+05:30'), '%d/%m/%Y') as user_created_at,
                GROUP_CONCAT(DISTINCT l.language_name) AS language_name,
                FORMAT((usv.total_reports_size + usv.total_prescription_size + usv.total_invoices_size) / 1000000, 3) AS total_size,
                (SELECT COUNT(*) FROM me_patient_family_member_mapping WHERE parent_patient_id = u.user_id AND mapping_status=1) as total_members,
                u.user_id,
                u.user_photo_filepath,
                ud.user_details_emergency_contact_person,
                DATE_FORMAT(ud.user_details_dob, '%d/%m/%Y') as user_details_dob,
                ud.user_details_marital_status,
                a.address_name,
                ud.user_details_weight,
                ud.user_details_height,
                u.user_gender,
                ud.user_details_food_allergies,
                ud.user_details_medicine_allergies,
                ud.user_details_other_allergies,
                (SELECT COUNT(*) FROM me_family_medical_history WHERE family_medical_history_user_id = u.user_id AND family_medical_history_status=1) as family_medical_history_data,
                ud.user_details_chronic_diseases,
                ud.user_details_injuries,
                ud.user_details_surgeries,
                ud.user_details_blood_group,
                ud.user_details_smoking_habbit,
                ud.user_details_alcohol,
                ud.user_details_food_preference,
                ud.user_details_occupation,
                ud.user_details_activity_level
            ");
        $this->db->from('me_users u');
        $this->db->join("me_appointments appo", "u.user_id = appo.appointment_user_id AND appo.appointment_status != 9", "LEFT");
        $this->db->join("me_address a", "
                a.address_user_id=u.user_id AND 
                a.address_type=1 AND 
                a.address_status=1", "LEFT");
        $this->db->join("me_city c", "c.city_id = a.address_city_id", "LEFT");
        $this->db->join("me_user_details ud", "ud.user_details_user_id = u.user_id", "LEFT");
        $this->db->join("me_languages l", "FIND_IN_SET(l.language_id, ud.user_details_languages_known) > 0", "LEFT");
        $this->db->join("users_used_size_vw usv", "usv.user_id = u.user_id", "LEFT");
        
        $this->db->group_start();
        $this->db->where_in("u.user_source_id", $where['user_source_id_arr']);
        $this->db->or_where("appo.appointment_doctor_user_id", $where['doctor_id']);
        $this->db->group_end();
        $this->db->where("u.user_type", 1);
        $this->db->where("u.user_status !=9");
        if(!empty($where['city_id']) && count($where['city_id']) > 0) {
            $this->db->where_in("c.city_id", $where['city_id']);
        }
        if(!empty($where['clinic_id']) && count($where['clinic_id']) > 0) {
            $this->db->where_in("appo.appointment_clinic_id", $where['clinic_id']);
        }
        if(!empty($where['language_id']) && count($where['language_id']) > 0) {
            $find_where = '';
            foreach ($where['language_id'] as $value) {
                $find_where .= "FIND_IN_SET('".$value."', ud.user_details_languages_known) OR ";
            }
            if($find_where != '')
                $this->db->where("(".trim($find_where, ' OR ').")");
        }
        if(!empty($where['search_str'])) {
            $this->db->group_start();
            $this->db->like('CONCAT(u.user_first_name, " ", u.user_last_name)', $where['search_str']);
            $this->db->or_like('u.user_email', $where['search_str']);
            $this->db->or_like('u.user_phone_number', $where['search_str']);
            $this->db->or_like('u.user_unique_id', $where['search_str']);
            $this->db->group_end();
        }
        if(!empty($where['start_date']))
            $this->db->where("DATE(u.user_created_at) >=", $where['start_date']);
        if(!empty($where['end_date']))
            $this->db->where("DATE(u.user_created_at) <=", $where['end_date']);
        $this->db->group_by('u.user_id');
        $this->db->order_by('u.user_id', 'DESC');
        if(!$count)
            $this->db->limit($where['per_page'], (($where['page'] - 1) * $where['per_page']));
        $query = $this->db->get();
        // echo $this->db->last_query();die;
        if($count)
            return $query->num_rows();
        else
            return $query->result();
    }

    public function get_mob_summary($where, $count=false) {
        $this->db->select("
            (SELECT GROUP_CONCAT(dg.drug_generic_title) FROM me_drug_generic dg WHERE FIND_IN_SET(dg.drug_generic_id,pr.prescription_generic_id)) as drug_generic_title,
            CONCAT(du.drug_unit_name , ' ', d.drug_strength) as sku,
            IFNULL(d.drug_name_with_unit, pr.prescription_drug_name) as drug_name_with_unit,
            0 as drug_prescribed_percent,
            0 as molecules_percent,
            (SELECT COUNT(DISTINCT prescription_appointment_id) FROM me_prescription_reports pr1 WHERE pr1.prescription_doctor_user_id=".$where['doctor_id']." AND prescription_status=1) as total_appointments,
            (SELECT COUNT(DISTINCT prescription_user_id) FROM me_prescription_reports pr4 WHERE pr4.prescription_doctor_user_id=".$where['doctor_id']." AND pr4.prescription_status=1) as total_patients,
            (SELECT COUNT(DISTINCT prescription_appointment_id,prescription_drug_id) FROM me_prescription_reports pr2 WHERE pr2.prescription_doctor_user_id=".$where['doctor_id']." AND pr2.prescription_drug_id=pr.prescription_drug_id AND prescription_status=1) as total_drug,
            (SELECT COUNT(DISTINCT prescription_appointment_id,prescription_drug_id) FROM me_prescription_reports pr3 WHERE pr3.prescription_doctor_user_id=".$where['doctor_id']." AND pr3.prescription_generic_id=pr.prescription_generic_id AND prescription_status=1) as total_generic,
            (SELECT GROUP_CONCAT(clinical_notes_reports_diagnoses) FROM me_clinical_notes_reports cnr WHERE cnr.clinical_notes_reports_doctor_user_id=pr.prescription_doctor_user_id AND FIND_IN_SET(cnr.clinical_notes_reports_appointment_id, GROUP_CONCAT(pr.prescription_appointment_id)) AND cnr.clinical_notes_reports_status=1) as diagonisis_list,
            pr.prescription_id
        ");

        $this->db->from('me_prescription_reports pr');      
        $this->db->join("me_drugs d", "d.drug_id=pr.prescription_drug_id", 'LEFT');
        $this->db->join("me_drug_units du", "du.drug_unit_id=d.drug_drug_unit_id", "LEFT");
        $this->db->where("pr.prescription_status", 1);
        $this->db->where_in("pr.prescription_doctor_user_id", $where['doctor_id']);
        if(!empty($where['clinic_id']) && count($where['clinic_id']) > 0) {
            $this->db->where_in("pr.prescription_clinic_id", $where['clinic_id']);
        }
        if(!empty($where['drug_generic_id'])) {
            $find_where = '';
            foreach ($where['drug_generic_id'] as $generic_id) {
                $find_where .= "find_in_set(".$generic_id.", pr.prescription_generic_id) OR ";
            }
            $this->db->where("(".trim($find_where, ' OR ').")");
        }
        if(!empty($where['search_indication'])) {
            $this->db->like("(SELECT GROUP_CONCAT(clinical_notes_reports_diagnoses) FROM me_clinical_notes_reports cnr WHERE cnr.clinical_notes_reports_doctor_user_id=pr.prescription_doctor_user_id AND cnr.clinical_notes_reports_appointment_id=pr.prescription_appointment_id AND cnr.clinical_notes_reports_status=1)", $where['search_indication']);
        }
        if(!empty($where['search_sku'])) 
            $this->db->like("CONCAT(du.drug_unit_name , ' ', d.drug_strength)", $where['search_sku']);
        if(!empty($where['search_brands'])) 
            $this->db->like('d.drug_name_with_unit', $where['search_brands']);
        $this->db->where("(SELECT GROUP_CONCAT(dg.drug_generic_title) FROM me_drug_generic dg WHERE FIND_IN_SET(dg.drug_generic_id,pr.prescription_generic_id)) IS NOT NULL");
        $this->db->group_by(array('d.drug_drug_unit_id','d.drug_name_with_unit'));
        $this->db->order_by("drug_generic_title");
        if(!$count)
            $this->db->limit($where['per_page'], (($where['page'] - 1) * $where['per_page']));
        $query = $this->db->get();
        if($count)
            return $query->num_rows();
        else
            return $query->result();
    }

    public function get_patients_city($user_source_id_arr, $doctor_id) {
        $this->db->select("
                c.city_id,
                c.city_name
            ");
        $this->db->from('me_users u');
        $this->db->join("me_appointments appo", "u.user_id = appo.appointment_user_id AND appo.appointment_status != 9", "LEFT");
        $this->db->join("me_address a", "
                a.address_user_id=u.user_id AND 
                a.address_type=1 AND 
                a.address_status=1", "LEFT");
        $this->db->join("me_city c", "c.city_id = a.address_city_id", "LEFT");
        $this->db->group_start();
        $this->db->where_in("u.user_source_id", $user_source_id_arr);
        $this->db->or_where("appo.appointment_doctor_user_id", $doctor_id);
        $this->db->group_end();
        $this->db->where("u.user_type", 1);
        $this->db->where("u.user_status !=9");
        $this->db->where("c.city_id IS NOT NULL");
        $this->db->group_by('c.city_id');
        $this->db->order_by('c.city_id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_lost_patient($where, $count=false) {
        $start_date = $where['start_date'];
        $end_date = $where['end_date'];
        $this->db->select("
            d.disease_name,
            d.disease_id,
            COUNT(DISTINCT cnr.clinical_notes_reports_user_id) as total,
            (SELECT group_concat(distinct appointment_user_id)
            FROM `me_appointments`
            WHERE find_in_set(appointment_user_id, group_concat(DISTINCT cnr.clinical_notes_reports_user_id)) 
            AND appointment_doctor_user_id = ".$where['doctor_id']." 
            AND appointment_status = '1' 
            AND appointment_date >= '".$start_date."' 
            AND appointment_date <= '".$end_date."') as visited_ids, 
            count(distinct (CASE WHEN appointment_date >= '".$start_date."' AND appointment_date <= '".$end_date."' AND appointment_doctor_user_id = ".$where['doctor_id']." THEN appointment_user_id END)) AS last_6_month,
            count(distinct (CASE WHEN appointment_date < '".$start_date."' AND `appointment_doctor_user_id` = ".$where['doctor_id']." THEN appointment_user_id END)) AS last_6_month_not_visited,
            (SELECT group_concat(distinct appointment_user_id)
            FROM `me_appointments`
            WHERE find_in_set(appointment_user_id, group_concat(DISTINCT cnr.clinical_notes_reports_user_id)) 
            AND appointment_doctor_user_id = ".$where['doctor_id']." 
            AND appointment_status = '1' 
            AND appointment_date < '".$start_date."') as not_visited_ids
            ");
        $this->db->from('me_clinical_notes_reports cnr');
        $this->db->join("me_appointments", "appointment_user_id=cnr.clinical_notes_reports_user_id");
        $this->db->join("me_users u", "u.user_id=cnr.clinical_notes_reports_user_id");
        $this->db->join("me_disease d", "FIND_IN_SET(d.disease_name,REPLACE(REPLACE(REPLACE(cnr.clinical_notes_reports_kco,'[',''),']',''),'\"',''))  != 0");
        $this->db->where('cnr.clinical_notes_reports_status',1);
        $this->db->where('u.user_status',1);
        $this->db->where('cnr.clinical_notes_reports_doctor_user_id', $where['doctor_id']);
        if(!empty($where['clinic_id']) && count($where['clinic_id']) > 0) {
            $this->db->where_in("cnr.clinical_notes_reports_clinic_id", $where['clinic_id']);
        }
        if(!empty($where['search_kco'])) {
            $this->db->like('d.disease_name', $where['search_kco']);
        }
        $this->db->group_by(array('d.disease_name'));
        if(!$count)
            $this->db->limit($where['per_page'], (($where['page'] - 1) * $where['per_page']));
        $query = $this->db->get();
        // echo $this->db->last_query();die;
        if($count)
            return $query->num_rows();
        else
            return $query->result();
    }

    public function get_lost_patient_details($where, $count=false, $is_all = false) {
        $start_date = $where['start_date'];
        $end_date = $where['end_date'];
        if($is_all) {
            $this->db->select("
                cnr.clinical_notes_reports_user_id,
                ");
        } else {
            $this->db->select("
                d.disease_name,
                d.disease_id,
                cnr.clinical_notes_reports_user_id,
                appointment_user_id,
                CONCAT(u.user_first_name,' ',u.user_last_name) as patient_name,
                u.user_phone_number,
                u.user_email,
                u.user_unique_id,
                u.user_gender,
                u.user_photo_filepath,
                ud.user_details_dob,
                ud.user_details_weight,
                ud.user_details_height,
                ud.user_details_alcohol,
                ud.user_details_surgeries,
                ud.user_details_smoking_habbit,
                ud.user_details_food_allergies,
                ud.user_details_medicine_allergies,
                ud.user_details_other_allergies,
                user_details_emergency_contact_person,
                user_details_emergency_contact_number,
                (SELECT vital_report_weight FROM me_vital_reports WHERE vital_report_user_id=u.user_id AND vital_report_status=1 order by vital_report_date DESC LIMIT 1) AS  vital_report_weight,
                ud.user_details_blood_group,
                DATE_FORMAT(CONVERT_TZ(u.user_created_at,'+00:00','+05:30'), '%d/%m/%Y') as user_created_at,
                ");
        }
        $this->db->from('me_clinical_notes_reports cnr');
        $this->db->join("me_appointments", "appointment_user_id=cnr.clinical_notes_reports_user_id");
        $this->db->join("me_users u", "u.user_id=cnr.clinical_notes_reports_user_id");
        $this->db->join("me_user_details ud", "u.user_id=ud.user_details_user_id", "LEFT");
        $this->db->join("me_disease d", "FIND_IN_SET(d.disease_name,REPLACE(REPLACE(REPLACE(cnr.clinical_notes_reports_kco,'[',''),']',''),'\"',''))  != 0");
        $this->db->where('cnr.clinical_notes_reports_status',1);
        $this->db->where('u.user_status',1);
        $this->db->where('cnr.clinical_notes_reports_doctor_user_id', $where['doctor_id']);
        if(!empty($where['clinic_id']) && count($where['clinic_id']) > 0) {
            $this->db->where_in("cnr.clinical_notes_reports_clinic_id", $where['clinic_id']);
        }
        $this->db->where('d.disease_id', $where['disease_id']);
        if($where['row_no'] == 2) {
            $this->db->where('appointment_date >=', $start_date);
            $this->db->where('appointment_date <=', $end_date);
        } elseif($where['row_no'] == 3) {
            $this->db->where('appointment_date <', $start_date);
        }
        if(!empty($where['not_in_id']) && count($where['not_in_id']) > 0)
            $this->db->where_not_in('cnr.clinical_notes_reports_user_id',$where['not_in_id']);
        $this->db->group_by(array('appointment_user_id'));
        if(!$count)
            $this->db->limit($where['per_page'], (($where['page'] - 1) * $where['per_page']));
        $query = $this->db->get();
        // echo $this->db->last_query();die;
        if($count && !$is_all)
            return $query->num_rows();
        else
            return $query->result();
    }

    public function get_languages($id) {
        $this->db->select("language_name");
        $this->db->from('me_languages');
        $this->db->where_in("language_id", $id);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_clinics($id) {
        $this->db->select("clinic_name");
        $this->db->from('me_clinic');
        $this->db->where_in("clinic_id", $id);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_drug_generic($doctor_id) {
        $this->db->select("drug_generic_id,drug_generic_title");
        $this->db->from('me_drug_generic');
        $this->db->join('me_prescription_reports','find_in_set(drug_generic_id,prescription_generic_id) !=0');
        $this->db->where('drug_generic_status', 1);
        $this->db->where('prescription_status', 1);
        $this->db->where('prescription_doctor_user_id', $doctor_id);
        $this->db->group_by('drug_generic_id');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_drug($drug_generic_id) {
        $this->db->select("drug_id,drug_name");
        $this->db->from('me_drugs');
        $this->db->where('drug_status', 1);
        if($drug_generic_id) {
            $find_where = '';
            foreach ($drug_generic_id as $generic_id) {
                $find_where .= "FIND_IN_SET(".$generic_id.", drug_drug_generic_id) OR ";
            }
            $this->db->where("(".trim($find_where, ' OR ').")");
        }
        $query = $this->db->get();
        return $query->result();
    }

    public function get_kco($doctor_id) {
        $this->db->select("
            d.disease_id,
            d.disease_name
            ");
        $this->db->from('me_clinical_notes_reports cnr');
        $this->db->join("me_users u", "u.user_id=cnr.clinical_notes_reports_doctor_user_id");
        $this->db->join("me_disease d", "FIND_IN_SET(d.disease_name,REPLACE(REPLACE(REPLACE(cnr.clinical_notes_reports_kco,'[',''),']',''),'\"',''))  != 0");
        $this->db->where('cnr.clinical_notes_reports_status',1);
        $this->db->where('cnr.clinical_notes_reports_doctor_user_id', $doctor_id);
        $this->db->group_by(array('d.disease_name'));
        $query = $this->db->get();
        return $result = $query->result_array();
    }

    public function get_patient_progress_data($where, $count = false) {
        $this->db->simple_query('SET SESSION group_concat_max_len=1000000');
        if($where['report_from'] == 1) {
            $columns = "d.disease_id,d.disease_name,";
        } elseif($where['report_from'] == 2) {
            $columns = "cnc.clinical_notes_catalog_id as disease_id,cnc.clinical_notes_catalog_title as disease_name,";
        }
        $this->db->select($columns . "
            COUNT(DISTINCT cnr.clinical_notes_reports_user_id) as total,
            GROUP_CONCAT(DISTINCT cnr.clinical_notes_reports_user_id) as patient_ids, 
            u.user_id as doctor_id
            ");
        $this->db->from('me_clinical_notes_reports cnr');
        $this->db->join("me_users u", "u.user_id=cnr.clinical_notes_reports_doctor_user_id");
        if($where['report_from'] == 1) {
            $this->db->join("me_disease d", "FIND_IN_SET(d.disease_name,REPLACE(REPLACE(REPLACE(cnr.clinical_notes_reports_kco,'[',''),']',''),'\"',''))  != 0");
        } elseif($where['report_from'] == 2) {
            $this->db->join("me_clinical_notes_catalog cnc", "cnc.clinical_notes_catalog_type=3 AND FIND_IN_SET(cnc.clinical_notes_catalog_title,REPLACE(REPLACE(REPLACE(cnr.clinical_notes_reports_diagnoses,'[',''),']',''),'\"',''))  != 0");
        }
        $this->db->where('cnr.clinical_notes_reports_status',1);
        $this->db->where('cnr.clinical_notes_reports_doctor_user_id',$where['doctor_id']);
        if(!empty($where['clinic_id']) && count($where['clinic_id']) > 0) {
            $this->db->where_in("cnr.clinical_notes_reports_clinic_id", $where['clinic_id']);
        }
        if($where['search_disease_id']) {
            if($where['report_from'] == 1) {
                $this->db->where_in('d.disease_id', $where['search_disease_id']);
            } elseif($where['report_from'] == 2) {
                $this->db->where_in('cnc.clinical_notes_catalog_id', $where['search_disease_id']);
            }
        }
        if($where['report_from'] == 1) {
            $this->db->group_by(array('d.disease_name'));
        } elseif($where['report_from'] == 2) {
            $this->db->group_by(array('cnc.clinical_notes_catalog_title'));
        }
        if(!$count)
            $this->db->limit($where['per_page'], (($where['page'] - 1) * $where['per_page']));
        $query = $this->db->get();
        if($count) {
            return $query->num_rows();
        }
        // echo $this->db->last_query();die;
        return $result = $query->result();
    }

    public function get_health_analytics_report($start_date,$end_date,$search_arr,$where) {
        $this->db->select("
            har.health_analytics_report_id,
            har.health_analytics_report_user_id,
            har.health_analytics_report_date,
            har.health_analytics_report_data,
            ");
        $this->db->from('me_health_analytics_report har');
        $this->db->join('me_prescription_reports pr','pr.prescription_user_id=har.health_analytics_report_user_id','LEFT');
        if(count($search_arr) > 0) {
            $this->db->group_start();
            foreach ($search_arr as $key => $value) {
                if(!empty($value['analytics_id'])) {
                    $this->db->or_group_start();
                    $this->db->or_like('har.health_analytics_report_data', '"id":"'.$value['analytics_id'].'"');
                    $this->db->where_in('har.health_analytics_report_user_id',$value['patient_ids']);
                    $this->db->group_end();
                }
            }
            $this->db->group_end();
        }
        if(!empty($where['drug_id'])) {
            $this->db->where_in('prescription_drug_id', $where['drug_id']);
        }
        if(!empty($where['drug_generic_id'])) {
            $find_where = '';
            foreach ($where['drug_generic_id'] as $generic_id) {
                $find_where .= "find_in_set(".$generic_id.", prescription_generic_id) OR ";
            }
            $this->db->where("(".trim($find_where, ' OR ').")");
        }
        $this->db->where('har.health_analytics_report_status',1);
        $this->db->where("har.health_analytics_report_date >=", $start_date);
        $this->db->where("har.health_analytics_report_date <=", $end_date);
        $this->db->group_by(['har.health_analytics_report_user_id','har.health_analytics_report_id']);
        $this->db->order_by('har.health_analytics_report_date','DESC');
        $query = $this->db->get();
        // echo $this->db->last_query();die;    
        return $query->result();
    }

    public function get_diagnoses($doctor_id) {
        $this->db->select("
            cnc.clinical_notes_catalog_id as disease_id,
            cnc.clinical_notes_catalog_title as disease_name
            ");
        $this->db->from('me_clinical_notes_reports cnr');
        $this->db->join("me_users u", "u.user_id=cnr.clinical_notes_reports_doctor_user_id");
        $this->db->join("me_clinical_notes_catalog cnc", "cnc.clinical_notes_catalog_type=3 AND FIND_IN_SET(cnc.clinical_notes_catalog_title,REPLACE(REPLACE(REPLACE(cnr.clinical_notes_reports_diagnoses,'[',''),']',''),'\"',''))  != 0");
        $this->db->where('cnr.clinical_notes_reports_status',1);
        $this->db->where('cnr.clinical_notes_reports_doctor_user_id', $doctor_id);
        $this->db->group_by(array('cnc.clinical_notes_catalog_title'));
        $query = $this->db->get();
        return $result = $query->result_array();
    }

    public function get_patient_analytics() {
        $this->db->select("
            pa.patient_analytics_analytics_id,
            pa.patient_analytics_name,
            ha.health_analytics_test_parent_id,
            ha.health_analytics_test_validation,
            (SELECT health_analytics_test_name FROM me_health_analytics_test WHERE health_analytics_test_id = ha.health_analytics_test_parent_id) as profile_name
            ");
        $this->db->from('me_patient_analytics pa');
        $this->db->join('me_health_analytics_test ha', 'ha.health_analytics_test_id=pa.patient_analytics_analytics_id');
        $this->db->where('pa.patient_analytics_status',1);
        $this->db->where_not_in('ha.health_analytics_test_id',EXCLUDE_ANALYTICS_TEST_ID);
        $this->db->group_by('pa.patient_analytics_analytics_id');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_analytics($analytics_id) {
        $this->db->select("
                health_analytics_test_id,
                health_analytics_test_name,
                health_analytics_test_validation
                ");
            $this->db->from('me_health_analytics_test');
            $this->db->where_in('health_analytics_test_id',$analytics_id);
            $query = $this->db->get();
            return $query->result();
    }

    public function get_invoice_summary($where, $count=false, $sum=false) {
        if($sum) {
            $this->db->select("
                    b.billing_discount AS discount,
                    b.billing_tax AS billing_tax,
                    (SELECT SUM(billing_detail_basic_cost*billing_detail_unit) FROM me_billing_details WHERE billing_detail_billing_id=b.billing_id AND billing_detail_status=1) AS grand_total,
                    b.billing_total_payable AS total_payable,
                    b.billing_advance_amount AS advance_amount,
                    b.billing_paid_amount AS paid_amount
                ");
        } else {
            $this->db->select("
                    u.user_unique_id,
                    CONCAT(u.user_first_name,' ',u.user_last_name) AS user_name,
                    u.user_email,
                    u.user_phone_number,
                    b.billing_id,
                    b.invoice_number,
                    b.billing_appointment_id,
                    b.billing_user_id,
                    b.billing_discount,
                    b.billing_tax,
                    (SELECT SUM(billing_detail_basic_cost*billing_detail_unit) FROM me_billing_details WHERE billing_detail_billing_id=b.billing_id AND billing_detail_status=1) AS billing_grand_total,
                    b.billing_total_payable,
                    b.billing_advance_amount,
                    b.billing_paid_amount,
                    DATE_FORMAT(b.billing_invoice_date, '%d/%m/%Y') AS billing_invoice_date,
                    DATE_FORMAT(CONVERT_TZ(b.billing_created_at,'+00:00','+05:30'), '%d/%m/%Y') AS billing_created_at
                ");
        }
        $this->db->from('me_billing b');
        $this->db->join("me_users u", "u.user_id = b.billing_user_id");
        $this->db->where("b.billing_doctor_user_id", $where['doctor_id']);
        $this->db->where("u.user_status !=", 9);
        $this->db->where("b.billing_status", 1);
        if(!empty($where['search_str'])) {
            $this->db->group_start();
            $this->db->like('CONCAT(u.user_first_name, " ", u.user_last_name)', $where['search_str']);
            $this->db->or_like('u.user_email', $where['search_str']);
            $this->db->or_like('u.user_phone_number', $where['search_str']);
            $this->db->or_like('u.user_unique_id', $where['search_str']);
            $this->db->group_end();
        }
        if(!empty($where['clinic_id']) && count($where['clinic_id']) > 0)
            $this->db->where_in("b.billing_clinic_id", $where['clinic_id']);
        if(!empty($where['start_date']))
            $this->db->where("DATE(b.billing_invoice_date) >=", $where['start_date']);
        if(!empty($where['end_date']))
            $this->db->where("DATE(b.billing_invoice_date) <=", $where['end_date']);
        
        $this->db->order_by('b.billing_id', 'DESC');
        if(!$count)
            $this->db->limit($where['per_page'], (($where['page'] - 1) * $where['per_page']));
        $query = $this->db->get();
        // echo $this->db->last_query();die;
        if($sum)
            return $query->result();
        elseif($count)
            return $query->num_rows();
        else
            return $query->result();
    }

    public function get_cancel_appointment($where, $count=false) {
        $this->db->select("
                IF(u.user_patient_id IS NULL,u.user_unique_id,u.user_patient_id) AS user_unique_id,
                CONCAT(u.user_first_name,' ',u.user_last_name) AS user_name,
                u.user_email,
                u.user_phone_number,
                DATE_FORMAT(a.appointment_date, '%d/%m/%Y') AS appointment_date,
                DATE_FORMAT(a.appointment_from_time, '%h:%i %p') AS appointment_from_time,
                DATE_FORMAT(CONVERT_TZ(a.appointment_updated_at,'+00:00','+05:30'), '%d/%m/%Y %h:%i %p') AS appointment_updated_at
            ");
        $this->db->from('me_appointments a');
        $this->db->join("me_users u", "u.user_id = a.appointment_user_id");
        $this->db->where("a.appointment_doctor_user_id", $where['doctor_id']);
        $this->db->where("u.user_status !=", 9);
        $this->db->where("a.appointment_status", 9);
        if(!empty($where['search_str'])) {
            $this->db->group_start();
            $this->db->like('CONCAT(u.user_first_name, " ", u.user_last_name)', $where['search_str']);
            $this->db->or_like('u.user_email', $where['search_str']);
            $this->db->or_like('u.user_phone_number', $where['search_str']);
            $this->db->or_like('u.user_unique_id', $where['search_str']);
            $this->db->group_end();
        }
        if(!empty($where['clinic_id']) && count($where['clinic_id']) > 0)
            $this->db->where_in("a.appointment_clinic_id", $where['clinic_id']);
        if(!empty($where['start_date']))
            $this->db->where("DATE(a.appointment_date) >=", $where['start_date']);
        if(!empty($where['end_date']))
            $this->db->where("DATE(a.appointment_date) <=", $where['end_date']);
        
        $this->db->order_by('a.appointment_date', 'DESC');
        if(!$count)
            $this->db->limit($where['per_page'], (($where['page'] - 1) * $where['per_page']));
        $query = $this->db->get();
        // echo $this->db->last_query();die;
        if($count)
            return $query->num_rows();
        else
            return $query->result();
    }

    public function get_family_medical_history($user_id) {
        $this->db->select("
                family_medical_history_user_id,
                family_medical_history_medical_condition_id,
                family_medical_history_relation,
                family_medical_history_date
                ");
            $this->db->from('me_family_medical_history');
            $this->db->where_in('family_medical_history_user_id',$user_id);
            $this->db->where('family_medical_history_status',1);
            $query = $this->db->get();
            return $query->result();
    }

    public function get_patient_kco($user_id) {
        $this->db->select("
                clinical_notes_reports_kco,
                clinical_notes_reports_user_id
                ");
            $this->db->from('me_clinical_notes_reports');
            $this->db->where_in('clinical_notes_reports_user_id',$user_id);
            $this->db->where('clinical_notes_reports_status',1);
            $query = $this->db->get();
            return $query->result();
    }

}