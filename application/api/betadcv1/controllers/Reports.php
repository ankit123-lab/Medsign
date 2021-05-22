<?php
class Reports extends MY_Controller {

    public function __construct() {
		parent::__construct();
        $this->load->model("report_model");
        $this->load->model("User_model");
    }

    public function member_summary_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $search_clinic_id = !empty($this->post_data['search_clinic_id']) ? $this->post_data['search_clinic_id'] : '';
            $start_date = !empty($this->post_data['from_date']) ? trim($this->Common_model->escape_data($this->post_data['from_date'])) : '';
            $end_date = !empty($this->post_data['to_date']) ? trim($this->Common_model->escape_data($this->post_data['to_date'])) : '';
            $search_str = !empty($this->post_data['search_str']) ? trim($this->Common_model->escape_data($this->post_data['search_str'])) : '';
            $language_id = !empty($this->post_data['language_id']) ? $this->post_data['language_id'] : '';
            $city_id = !empty($this->post_data['city_id']) ? $this->post_data['city_id'] : '';
            $page = !empty($this->Common_model->escape_data($this->post_data['page'])) ? trim($this->Common_model->escape_data($this->post_data['page'])) : '1';
            $per_page = !empty($this->Common_model->escape_data($this->post_data['per_page'])) ? trim($this->Common_model->escape_data($this->post_data['per_page'])) : '10';
            if (empty($doctor_id)) {
                $this->bad_request();
                exit;
            }
            $where = array();
            $where['search_str'] = $search_str;
            $where['language_id'] = $language_id;
            $where['city_id'] = $city_id;
            $where['doctor_id'] = $doctor_id;
            $where['clinic_id'] = $search_clinic_id;
            $where['page'] = $page;
            $where['per_page'] = $per_page;
            $where['start_date'] = $start_date;
            $where['end_date'] = $end_date;
            $rece = $this->User_model->get_doctor_receptionist($doctor_id, 'user_id');
            $user_source_id_arr = array_column($rece, 'user_id');
            array_push($user_source_id_arr, $doctor_id);
            
            $where['user_source_id_arr'] = $user_source_id_arr;
            $result = $this->report_model->get_member_summary($where);
            $count = $this->report_model->get_member_summary($where, true);
            foreach ($result as $key => $value) {
                $result[$key]->status = get_status($value->user_status);
                $result[$key]->profile_completion = patient_profile_completion($value->user_name,$value->user_phone_number,$value->user_photo_filepath,$value->user_details_emergency_contact_person,$value->user_email,$value->user_details_dob,$value->user_details_marital_status,$value->address_name,$value->user_details_weight,$value->user_details_height,$value->user_gender,$value->user_details_food_allergies,$value->user_details_medicine_allergies,$value->user_details_other_allergies,$value->family_medical_history_data,$value->user_details_chronic_diseases,$value->user_details_injuries,$value->user_details_surgeries,$value->user_details_blood_group,$value->user_details_smoking_habbit,$value->user_details_alcohol,$value->user_details_food_preference,$value->user_details_occupation,$value->user_details_activity_level);
            }
            $this->my_response['status'] = true;
            $this->my_response['count'] = $count;
            $this->my_response['data'] = $result;
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }  

    public function mob_summary_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $search_clinic_id = !empty($this->post_data['search_clinic_id']) ? $this->post_data['search_clinic_id'] : '';
            $search_indication = !empty($this->post_data['search_indication']) ? trim($this->Common_model->escape_data($this->post_data['search_indication'])) : '';
            $search_sku = !empty($this->post_data['search_sku']) ? trim($this->Common_model->escape_data($this->post_data['search_sku'])) : '';
            $search_brands = !empty($this->post_data['search_brands']) ? trim($this->Common_model->escape_data($this->post_data['search_brands'])) : '';
            $drug_generic_id = !empty($this->post_data['drug_generic_id']) ? $this->post_data['drug_generic_id'] : '';
            $page = !empty($this->Common_model->escape_data($this->post_data['page'])) ? trim($this->Common_model->escape_data($this->post_data['page'])) : '1';
            $per_page = !empty($this->Common_model->escape_data($this->post_data['per_page'])) ? trim($this->Common_model->escape_data($this->post_data['per_page'])) : '10';
            if (empty($doctor_id)) {
                $this->bad_request();
                exit;
            }
            $where = array();
            $where['doctor_id'] = $doctor_id;
            $where['clinic_id'] = $search_clinic_id;
            $where['page'] = $page;
            $where['per_page'] = $per_page;
            $where['drug_generic_id'] = $drug_generic_id;
            $where['search_indication'] = $search_indication;
            $where['search_sku'] = $search_sku;
            $where['search_brands'] = $search_brands;
            $result = $this->report_model->get_mob_summary($where);
            $count = $this->report_model->get_mob_summary($where, true);
            $mob_summary = array();
            foreach ($result as $key => $value) {
                $value->diagonisis_list = get_diagonisis_list($value->diagonisis_list);
                if(empty($mob_summary[$value->drug_generic_title])) {
                    $mob_summary[$value->drug_generic_title] = array();
                    $mob_summary[$value->drug_generic_title]['drug_generic_title'] = $value->drug_generic_title;
                    $mob_summary[$value->drug_generic_title]['total_appointments'] = $value->total_appointments;
                    $mob_summary[$value->drug_generic_title]['total_patients'] = $value->total_patients;
                    $mob_summary[$value->drug_generic_title]['total_generic'] = $value->total_generic;
                    $mob_summary[$value->drug_generic_title]['molecules_prescribed_percent'] = number_format((100 * $value->total_generic) / $value->total_appointments, 2);
                }
                $value->drug_percent = number_format((100 * $value->total_drug) / $value->total_generic, 2);
                $mob_summary[$value->drug_generic_title]['molecules_data'][] = $value;
            }
            $this->my_response['status'] = true;
            $this->my_response['count'] = $count;
            $this->my_response['data'] = array_values($mob_summary);
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function lost_patient_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $search_clinic_id = !empty($this->post_data['search_clinic_id']) ? $this->post_data['search_clinic_id'] : '';
            $start_date = !empty($this->post_data['from_date']) ? trim($this->Common_model->escape_data($this->post_data['from_date'])) : date('Y-m-d', strtotime("-6 month"));
            $end_date = !empty($this->post_data['to_date']) ? trim($this->Common_model->escape_data($this->post_data['to_date'])) : date('Y-m-d');
            $search_kco = !empty($this->post_data['search_kco']) ? trim($this->Common_model->escape_data($this->post_data['search_kco'])) : '';
            $page = !empty($this->Common_model->escape_data($this->post_data['page'])) ? trim($this->Common_model->escape_data($this->post_data['page'])) : '1';
            $per_page = !empty($this->Common_model->escape_data($this->post_data['per_page'])) ? trim($this->Common_model->escape_data($this->post_data['per_page'])) : '10';
            if (empty($doctor_id) || empty($start_date) || empty($end_date)) {
                $this->bad_request();
                exit;
            }
            $where = array();
            $where['search_kco'] = $search_kco;
            $where['doctor_id'] = $doctor_id;
            $where['clinic_id'] = $search_clinic_id;
            $where['page'] = $page;
            $where['per_page'] = $per_page;
            $where['start_date'] = $start_date;
            $where['end_date'] = $end_date;
            $result = $this->report_model->get_lost_patient($where);
            $count = $this->report_model->get_lost_patient($where, true);
            foreach ($result as $key => $value) {
                if(!empty($value->visited_ids) && !empty($value->not_visited_ids)) {
                    $visited_ids = explode(',', $value->visited_ids);
                    $not_visited_ids = explode(',', $value->not_visited_ids);
                    $match_value = array_intersect($visited_ids,$not_visited_ids);
                    $result[$key]->last_6_month_not_visited = $value->last_6_month_not_visited - count($match_value);
                }
                unset($result[$key]->visited_ids);
                unset($result[$key]->not_visited_ids);
                $result[$key]->percentage = number_format(100 * $value->last_6_month / $value->total,2);
                $result[$key]->not_visited_percentage = number_format(100 * $result[$key]->last_6_month_not_visited / $value->total,2);
                $result[$key]->last_6_month_not_visited = (int) $result[$key]->last_6_month_not_visited;
                $result[$key]->last_6_month = (int) $result[$key]->last_6_month;
            }
            $this->my_response['status'] = true;
            $this->my_response['count'] = $count;
            $this->my_response['data'] = $result;
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_lost_patient_details_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $search_clinic_id = !empty($this->post_data['search_clinic_id']) ? $this->post_data['search_clinic_id'] : '';
            $start_date = !empty($this->post_data['from_date']) ? trim($this->Common_model->escape_data($this->post_data['from_date'])) : date('Y-m-d', strtotime("-6 month"));
            $end_date = !empty($this->post_data['to_date']) ? trim($this->Common_model->escape_data($this->post_data['to_date'])) : date('Y-m-d');
            $disease_id = !empty($this->post_data['disease_id']) ? trim($this->Common_model->escape_data($this->post_data['disease_id'])) : '';
            $row_no = !empty($this->post_data['row_no']) ? trim($this->Common_model->escape_data($this->post_data['row_no'])) : '';
            $page = !empty($this->Common_model->escape_data($this->post_data['page'])) ? trim($this->Common_model->escape_data($this->post_data['page'])) : '1';
            $per_page = !empty($this->Common_model->escape_data($this->post_data['per_page'])) ? trim($this->Common_model->escape_data($this->post_data['per_page'])) : '10';
            if (empty($doctor_id) || empty($start_date) || empty($end_date)) {
                $this->bad_request();
                exit;
            }
            $where = array();
            $where['disease_id'] = $disease_id;
            $where['row_no'] = $row_no;
            $where['doctor_id'] = $doctor_id;
            $where['clinic_id'] = $search_clinic_id;
            $where['page'] = $page;
            $where['per_page'] = $per_page;
            $where['start_date'] = $start_date;
            $where['end_date'] = $end_date;
            if($row_no == 3) {
                $where['row_no'] = 2;
                $visited_ids = $this->report_model->get_lost_patient_details($where, false, true);
                $where['not_in_id'] = array_column($visited_ids, 'clinical_notes_reports_user_id');
            }
            $where['row_no'] = $row_no;
            $result = $this->report_model->get_lost_patient_details($where);
            $count = $this->report_model->get_lost_patient_details($where, true);
            $user_id_arr = array_column($result, 'clinical_notes_reports_user_id');
            $family_medical_history = $this->report_model->get_family_medical_history($user_id_arr);
            $patient_kco = $this->report_model->get_patient_kco($user_id_arr);
            $patient_kco_arr = array();
            foreach ($patient_kco as $key => $value) {
                $kco = get_diagonisis_list($value->clinical_notes_reports_kco);
                if(empty($patient_kco_arr[$value->clinical_notes_reports_user_id]) && !empty($kco))
                    $patient_kco_arr[$value->clinical_notes_reports_user_id] = $kco . ',';
                elseif(!empty($kco))
                    $patient_kco_arr[$value->clinical_notes_reports_user_id] .= $kco . ',';
            }
            $family_history = array();
            foreach ($family_medical_history as $key => $value) {
                $value->relation = relation_map($value->family_medical_history_relation);
                if(!empty($value->family_medical_history_date))
                    $value->family_medical_history_date = date('d-M-Y', strtotime($value->family_medical_history_date));
                else
                    $value->family_medical_history_date = '';
                $family_history[$value->family_medical_history_user_id][] = $value;
            }
            foreach ($result as $key => $value) {
                if(!empty($value->vital_report_weight))
                    $result[$key]->user_details_weight = $value->vital_report_weight;
                unset($result[$key]->vital_report_weight);
                $result[$key]->family_medical_history = array();
                if(!empty($family_history[$value->clinical_notes_reports_user_id]))
                     $result[$key]->family_medical_history = $family_history[$value->clinical_notes_reports_user_id];
                 if(!empty($patient_kco_arr[$value->clinical_notes_reports_user_id]))
                     $result[$key]->kco = $patient_kco_arr[$value->clinical_notes_reports_user_id];
                 $result[$key]->emergency_contact = '';
                 if(!empty($value->user_details_emergency_contact_person))
                    $result[$key]->emergency_contact = $value->user_details_emergency_contact_person . ', ' . $value->user_details_emergency_contact_number;
            }
            $this->my_response['status'] = true;
            $this->my_response['count'] = $count;
            $this->my_response['data'] = $result;
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function patient_progress_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $search_clinic_id = !empty($this->post_data['search_clinic_id']) ? $this->post_data['search_clinic_id'] : '';
            $start_date = !empty($this->post_data['from_date']) ? trim($this->Common_model->escape_data($this->post_data['from_date'])) : date('Y-m-d', strtotime("-6 month"));
            $end_date = !empty($this->post_data['to_date']) ? trim($this->Common_model->escape_data($this->post_data['to_date'])) : date('Y-m-d');
            $drug_id = !empty($this->post_data['drug_id']) ? $this->post_data['drug_id'] : '';
            $drug_generic_id = !empty($this->post_data['drug_generic_id']) ? $this->post_data['drug_generic_id'] : '';
            $report_from = !empty($this->post_data['report_from']) ? trim($this->Common_model->escape_data($this->post_data['report_from'])) : '';
            $search_disease_id = !empty($this->post_data['search_disease_id']) ? $this->post_data['search_disease_id'] : '';
            $search_analytics_id = !empty($this->post_data['search_analytics_id']) ? $this->post_data['search_analytics_id'] : '';
            $page = !empty($this->Common_model->escape_data($this->post_data['page'])) ? trim($this->Common_model->escape_data($this->post_data['page'])) : '1';
            $per_page = !empty($this->Common_model->escape_data($this->post_data['per_page'])) ? trim($this->Common_model->escape_data($this->post_data['per_page'])) : '10';
            if (empty($doctor_id) || empty($start_date) || empty($end_date)) {
                $this->bad_request();
                exit;
            }
            $search_analytics_arr = [];
            foreach ($search_disease_id as $key => $value) {
                $search_analytics_arr[$value] = $search_analytics_id[$key];
            }
            $analytics_list =  $this->report_model->get_analytics($search_analytics_id);
            $analytics_list = array_column($analytics_list, 'health_analytics_test_validation', 'health_analytics_test_id');
            $where = array();
            $where['drug_id'] = $drug_id;
            $where['drug_generic_id'] = $drug_generic_id;
            $where['report_from'] = $report_from;
            $where['search_disease_id'] = $search_disease_id;
            $where['search_analytics_id'] = $search_analytics_id;
            $where['doctor_id'] = $doctor_id;
            $where['clinic_id'] = $search_clinic_id;
            $where['page'] = $page;
            $where['per_page'] = $per_page;
            $where['start_date'] = $start_date;
            $where['end_date'] = $end_date;
            $result = $this->report_model->get_patient_progress_data($where);
            $count = $this->report_model->get_patient_progress_data($where, true);
            $search_arr = array();
            foreach ($result as $key => $value) {
                $patient_ids = explode(",", $value->patient_ids);
                if(!empty($search_arr[$value->disease_id]['patient_ids'])){
                    $patient_ids = array_merge($patient_ids,$search_arr[$value->disease_id]['patient_ids']);
                }
                $search_arr[$value->disease_id]['patient_ids'] = $patient_ids;
                $search_arr[$value->disease_id]['analytics_id'] = $search_analytics_arr[$value->disease_id];
                $result[$key]->analytics_id = $search_analytics_arr[$value->disease_id];
            }
            $health_analytics_report = $this->report_model->get_health_analytics_report($start_date, $end_date, $search_arr, $where);
            $progress_data = array();
            foreach ($search_arr as $disease_id => $search) {
                foreach($health_analytics_report as $analytics_report) {
                    $analytic_value = $this->get_analytic_value($analytics_report->health_analytics_report_data, $search['analytics_id']);
                    if(count($analytic_value) > 0 && in_array($analytics_report->health_analytics_report_user_id, $search['patient_ids'])) {
                        $analytic_value['report_date'] = $analytics_report->health_analytics_report_date;
                        $progress_data[$disease_id][$analytics_report->health_analytics_report_user_id][$search['analytics_id']][] = $analytic_value;
                    }
                }
            }
            $progress_deseases = array();
            foreach ($search_analytics_arr as $desease_id => $analytics_id) {
                $validation = json_decode($analytics_list[$analytics_id],true);
                $trend_high = "";
                if(!empty($validation['trend'][0]['trend_high']) && !empty(KCO_DIAGNOSES[$desease_id])) {
                    if(KCO_DIAGNOSES[$desease_id] == 'Hyperthyroidism') {
                        $trend_high = $validation['trend'][0]['trend_high'];
                    } else {
                        $trend_high = $validation['trend'][1]['trend_high'];
                    }
                } elseif(!empty($validation['trend_high'])) {
                    $trend_high = $validation['trend_high'];
                }
                $improving = 0;
                $deteriorating = 0;
                $maintained = 0;
                $not_tracked = 0;
                if(!empty($progress_data[$desease_id])) {
                    foreach ($progress_data[$desease_id] as $analytics_arr) {
                        foreach ($analytics_arr as $key => $analytics) {
                            if($key != $analytics_id)
                                continue;
                            if(count($analytics) > 1) {
                                $last_value = 0;
                                $total_value = 0;
                                $i = 0;
                                foreach ($analytics as $k => $value) {
                                    if($k == 0) {
                                        $last_value = $value['value'];
                                    } else {
                                        $total_value = $total_value + $value['value'];
                                        $i++;
                                    }
                                    if($k == 3)
                                        break;
                                }
                                if($last_value > ($total_value / $i)) {
                                    if($trend_high == 'Deteriorating')
                                        $deteriorating++;
                                    else
                                        $improving++;
                                } elseif($last_value == ($total_value / $i)) {
                                    $maintained++;
                                } elseif($last_value < ($total_value / $i)) {
                                    if($trend_high == 'Deteriorating')
                                        $improving++;
                                    else
                                        $deteriorating++;
                                }
                            }
                        }
                    }
                }
                $progress_deseases[$desease_id] = array(
                    'improving' => $improving,
                    'deteriorating' => $deteriorating,
                    'maintained' => $maintained,
                    'not_tracked' => $not_tracked,
                );
            }
            $data = array();
            foreach ($result as $key => $value) {
                $data[$key] = $value;
                $data[$key]->improving = $progress_deseases[$value->disease_id]['improving'];
                $data[$key]->deteriorating = $progress_deseases[$value->disease_id]['deteriorating'];
                $data[$key]->maintained = $progress_deseases[$value->disease_id]['maintained'];
                $data[$key]->not_tracked = $progress_deseases[$value->disease_id]['not_tracked'];
                $not_tracked = $value->total - ($data[$key]->improving+$data[$key]->deteriorating+$data[$key]->maintained);
                $data[$key]->not_tracked = $not_tracked;
                $data[$key]->improving_percent = round(100 * $data[$key]->improving / $value->total);
                $data[$key]->deteriorating_percent = round(100 * $data[$key]->deteriorating / $value->total);
                $data[$key]->maintained_percent = round(100 * $data[$key]->maintained / $value->total);
                $data[$key]->not_tracked_percent = round(100 * $data[$key]->not_tracked / $value->total);
            }
            $this->my_response['status'] = true;
            $this->my_response['count'] = $count;
            $this->my_response['data'] = $data;
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function patients_city_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $clinic_id = !empty($this->post_data['clinic_id']) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            if (empty($doctor_id) || empty($clinic_id)) {
                $this->bad_request();
                exit;
            }
            $rece = $this->User_model->get_doctor_receptionist($doctor_id, 'user_id');
            $user_source_id_arr = array_column($rece, 'user_id');
            array_push($user_source_id_arr, $doctor_id);
            $where['user_source_id_arr'] = $user_source_id_arr;
            $result = $this->report_model->get_patients_city($user_source_id_arr, $doctor_id);
            $this->my_response['status'] = true;
            $this->my_response['data'] = $result;
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    } 

    public function drug_generic_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $clinic_id = !empty($this->post_data['clinic_id']) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            if (empty($doctor_id) || empty($clinic_id)) {
                $this->bad_request();
                exit;
            }
            
            $result = $this->report_model->get_drug_generic($doctor_id);
            $this->my_response['status'] = true;
            $this->my_response['data'] = $result;
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }  

    public function get_drugs_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $clinic_id = !empty($this->post_data['clinic_id']) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $drug_generic_id = !empty($this->post_data['drug_generic_id']) ? $this->post_data['drug_generic_id'] : '';
            if (empty($doctor_id) || empty($clinic_id)) {
                $this->bad_request();
                exit;
            }
            if(!empty($drug_generic_id) && is_array($drug_generic_id) && count($drug_generic_id) > 0)
                $result = $this->report_model->get_drug($drug_generic_id);
            else
                $result = array();

            $this->my_response['status'] = true;
            $this->my_response['data'] = $result;
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }  

    public function get_kco_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $clinic_id = !empty($this->post_data['clinic_id']) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            if (empty($doctor_id) || empty($clinic_id)) {
                $this->bad_request();
                exit;
            }
            $result = $this->report_model->get_kco($doctor_id);
            $analytics = $this->report_model->get_patient_analytics();
            $patient_analytics = array();
            foreach ($analytics as $key => $value) {
                if(!empty($patient_analytics[$value->health_analytics_test_parent_id])) {
                    $patient_analytics[$value->health_analytics_test_parent_id]['profile_name'] = $value->profile_name;
                }
                $patient_analytics[$value->health_analytics_test_parent_id]['analytics_name'][] = $value;
            }
            $this->my_response['status'] = true;
            $this->my_response['kco'] = $result;
            $this->my_response['patient_analytics'] = array_values($patient_analytics);
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }  

    public function get_diagnoses_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $clinic_id = !empty($this->post_data['clinic_id']) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            if (empty($doctor_id) || empty($clinic_id)) {
                $this->bad_request();
                exit;
            }
            
            $result = $this->report_model->get_diagnoses($doctor_id);
            $this->my_response['status'] = true;
            $this->my_response['data'] = $result;
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }  

    private function get_analytic_value($data, $analytics_id) {
        $analytic_data = json_decode($data, true);
        $filter_arr = array_filter($analytic_data, function ($var) use ($analytics_id) {
            return ($var['id'] == $analytics_id);
        });
        $filter_arr = array_values($filter_arr);
        if(!empty($filter_arr[0]) && !empty($filter_arr[0]['value']))
            return $filter_arr[0];
        return array();
    }

    public function invoice_summary_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $search_clinic_id = !empty($this->post_data['search_clinic_id']) ? $this->post_data['search_clinic_id'] : '';
            $start_date = !empty($this->post_data['from_date']) ? trim($this->Common_model->escape_data($this->post_data['from_date'])) : '';
            $end_date = !empty($this->post_data['to_date']) ? trim($this->Common_model->escape_data($this->post_data['to_date'])) : '';
            $search_str = !empty($this->post_data['search_str']) ? trim($this->Common_model->escape_data($this->post_data['search_str'])) : '';
            $page = !empty($this->Common_model->escape_data($this->post_data['page'])) ? trim($this->Common_model->escape_data($this->post_data['page'])) : '1';
            $per_page = !empty($this->Common_model->escape_data($this->post_data['per_page'])) ? trim($this->Common_model->escape_data($this->post_data['per_page'])) : '10';
            if (empty($doctor_id)) {
                $this->bad_request();
                exit;
            }
            $where['doctor_id'] = $doctor_id;
            $where['clinic_id'] = $search_clinic_id;
            $where['search_str'] = $search_str;
            $where['page'] = $page;
            $where['per_page'] = $per_page;
            $where['start_date'] = $start_date;
            $where['end_date'] = $end_date;
            $result = $this->report_model->get_invoice_summary($where);
            $count = $this->report_model->get_invoice_summary($where, true);
            $all_result = $this->report_model->get_invoice_summary($where, true, true);
            $sum = array();
            $sum['discount'] = array_sum(array_column($all_result, 'discount'));
            $sum['billing_tax'] = array_sum(array_column($all_result, 'billing_tax'));
            $sum['grand_total'] = array_sum(array_column($all_result, 'grand_total'));
            $sum['total_payable'] = array_sum(array_column($all_result, 'total_payable'));
            $sum['advance_amount'] = array_sum(array_column($all_result, 'advance_amount'));
            $sum['paid_amount'] = array_sum(array_column($all_result, 'paid_amount'));
            $footer_sum = array();
            $footer_sum['discount'] = array_sum(array_column($result, 'billing_discount'));
            $footer_sum['billing_tax'] = array_sum(array_column($result, 'billing_tax'));
            $footer_sum['grand_total'] = array_sum(array_column($result, 'billing_grand_total'));
            $footer_sum['total_payable'] = array_sum(array_column($result, 'billing_total_payable'));
            $footer_sum['advance_amount'] = array_sum(array_column($result, 'billing_advance_amount'));
            $footer_sum['paid_amount'] = array_sum(array_column($result, 'billing_paid_amount'));
            $this->my_response['status'] = true;
            $this->my_response['count'] = $count;
            $this->my_response['sum'] = $sum;
            $this->my_response['footer_sum'] = $footer_sum;
            $this->my_response['data'] = $result;
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function cancel_appointment_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $search_clinic_id = !empty($this->post_data['search_clinic_id']) ? $this->post_data['search_clinic_id'] : '';
            $start_date = !empty($this->post_data['from_date']) ? trim($this->Common_model->escape_data($this->post_data['from_date'])) : '';
            $end_date = !empty($this->post_data['to_date']) ? trim($this->Common_model->escape_data($this->post_data['to_date'])) : '';
            $search_str = !empty($this->post_data['search_str']) ? trim($this->Common_model->escape_data($this->post_data['search_str'])) : '';
            $page = !empty($this->Common_model->escape_data($this->post_data['page'])) ? trim($this->Common_model->escape_data($this->post_data['page'])) : '1';
            $per_page = !empty($this->Common_model->escape_data($this->post_data['per_page'])) ? trim($this->Common_model->escape_data($this->post_data['per_page'])) : '10';
            if (empty($doctor_id)) {
                $this->bad_request();
                exit;
            }
            $where['doctor_id'] = $doctor_id;
            $where['clinic_id'] = $search_clinic_id;
            $where['search_str'] = $search_str;
            $where['page'] = $page;
            $where['per_page'] = $per_page;
            $where['start_date'] = $start_date;
            $where['end_date'] = $end_date;
            $result = $this->report_model->get_cancel_appointment($where);
            $count = $this->report_model->get_cancel_appointment($where, true);
            $this->my_response['status'] = true;
            $this->my_response['count'] = $count;
            $this->my_response['data'] = $result;
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

}