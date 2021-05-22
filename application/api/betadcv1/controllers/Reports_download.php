<?php
class Reports_download extends CI_Controller {

    public function __construct() {
		parent::__construct();
        $this->load->model("report_model");
        $this->load->model("User_model");
    }

    public function member_summary($request_data) {
        $request_data = json_decode(base64_decode($request_data), true);
        if(empty($_SERVER['HTTP_REFERER'])){
            die;
        }
        if(!$this->check_token($request_data))
            die('Invalid token');
        $where = array();
        $start_date = "";
        $end_date = "";
        $where['search_str'] = $request_data['search_str'];
        $where['start_date'] = $request_data['from_date'];
        $where['end_date'] = $request_data['to_date'];
        $where['doctor_id'] = $request_data['doctor_id'];
        $where['clinic_id'] = $request_data['search_clinic_id'];
        $where['language_id'] = $request_data['language_id'];
        $where['city_id'] = $request_data['city_id'];
        $rece = $this->User_model->get_doctor_receptionist($request_data['doctor_id'], 'user_id');
        $user_source_id_arr = array_column($rece, 'user_id');
        array_push($user_source_id_arr, $request_data['doctor_id']);
        
        $where['user_source_id_arr'] = $user_source_id_arr;
        $result = $this->report_model->get_member_summary($where);
        $language_arr = array();
        if(!empty($where['language_id']) && count($where['language_id']) > 0) {
            $language = $this->report_model->get_languages($where['language_id']);
            $language_arr = array_column($language, 'language_name');
        }
        $clinic_arr = array();
        if(!empty($where['clinic_id']) && count($where['clinic_id']) > 0) {
            $clinicList = $this->report_model->get_clinics($where['clinic_id']);
            $clinic_arr = array_column($clinicList, 'clinic_name');
        }
        foreach ($result as $key => $value) {
            $result[$key]->status = get_status($value->user_status);
            $result[$key]->profile_completion = patient_profile_completion($value->user_name,$value->user_phone_number,$value->user_photo_filepath,$value->user_details_emergency_contact_person,$value->user_email,$value->user_details_dob,$value->user_details_marital_status,$value->address_name,$value->user_details_weight,$value->user_details_height,$value->user_gender,$value->user_details_food_allergies,$value->user_details_medicine_allergies,$value->user_details_other_allergies,$value->family_medical_history_data,$value->user_details_chronic_diseases,$value->user_details_injuries,$value->user_details_surgeries,$value->user_details_blood_group,$value->user_details_smoking_habbit,$value->user_details_alcohol,$value->user_details_food_preference,$value->user_details_occupation,$value->user_details_activity_level);
        }
        $this->load->library('excel');
        $object = new PHPExcel();
        $object->setActiveSheetIndex(0);

        $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
        $object = report_header($object,$objDrawing);
        $table_columns = array();
        $table_columns[] = ["label" => "Unique ID", "width" => 15];
        $table_columns[] = ["label" => "Name", "width" => 20];
        $table_columns[] = ["label" => "Mobile No", "width" => 21];
        $table_columns[] = ["label" => "Mail ID", "width" => 40];
        $table_columns[] = ["label" => "Date of Birth", "width" => 20];
        $table_columns[] = ["label" => "Date of Enrolment", "width" => 20];
        $table_columns[] = ["label" => "Town", "width" => 20];
        $table_columns[] = ["label" => "Language Preference", "width" => 25];
        $table_columns[] = ["label" => "Storage Space Used (GB)", "width" => 25];
        $table_columns[] = ["label" => "Profile Completion (%)", "width" => 25];
        $table_columns[] = ["label" => "Family Members", "width" => 20];
        $table_columns[] = ["label" => "Status", "width" => 15];
        $column = 0;
        $excel_row = 5;
        $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'Filters:');
        $excel_row++;
        if(!empty($where['search_str'])) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'Search:');
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $where['search_str']);
            $excel_row++;
        }
        if(!empty($where['start_date'])) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'From Date:');
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, date("d/m/Y", strtotime($where['start_date'])));
            $excel_row++;
        }
        if(!empty($where['end_date'])) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'To Date:');
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, date("d/m/Y", strtotime($where['end_date'])));
            $excel_row++;
        }
        if(count($language_arr) > 0) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'Language:');
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, implode(', ', $language_arr));
            $excel_row++;
        }
        if(count($clinic_arr) > 0) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'Clinic:');
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, implode(', ', $clinic_arr));
            $excel_row++;
        }
        if(!empty($request_data['city_name'])) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'City:');
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, trim($request_data['city_name'], ', '));
            $excel_row++;
        }

        $excel_row++;
        foreach($table_columns as $key => $field) {
            $object = report_header_row($object,$column, $excel_row, $field['label'],$field['width']);
            $column++;
        }
        $excel_row++;
        foreach ($result as $row) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $row->user_unique_id);
            $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $row->user_name);
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $row->user_phone_number);
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $row->user_email);
            $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $row->user_details_dob);
            $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $row->user_created_at);
            $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $row->city_name);
            $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, $row->language_name);
            $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $row->total_size);
            $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, $row->profile_completion);
            $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, $row->total_members);
            $object->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row, $row->status);
            
            $excel_row++;
        }
        $object_writer = new PHPExcel_Writer_Excel2007($object);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Member summary.xls"');
        $object_writer->save('php://output');
        die;
    } 

    public function mob_summary($request_data) {
        $request_data = json_decode(base64_decode($request_data), true);
        if(empty($_SERVER['HTTP_REFERER'])){
            die;
        }
        if(!$this->check_token($request_data))
            die('Invalid token');
        $where = array();
        $start_date = "";
        $end_date = "";
        $where['search_str'] = $request_data['search_str'];
        
        $where['doctor_id'] = $request_data['doctor_id'];
        $where['clinic_id'] = $request_data['search_clinic_id'];
        $where['drug_generic_id'] = $request_data['drug_generic_id'];
        $where['search_indication'] = $request_data['search_indication'];
        $where['search_sku'] = $request_data['search_sku'];
        $where['search_brands'] = $request_data['search_brands'];
        $clinic_arr = array();
        if(!empty($where['clinic_id']) && count($where['clinic_id']) > 0) {
            $clinicList = $this->report_model->get_clinics($where['clinic_id']);
            $clinic_arr = array_column($clinicList, 'clinic_name');
        }
        $result = $this->report_model->get_mob_summary($where);
        $mob_summary = array();
        foreach ($result as $key => $value) {
            $value->diagonisis_list = get_diagonisis_list($value->diagonisis_list);
            if(empty($mob_summary[$value->drug_generic_title])) {
                $mob_summary[$value->drug_generic_title] = array();
                $mob_summary[$value->drug_generic_title]['drug_generic_title'] = $value->drug_generic_title;
                $mob_summary[$value->drug_generic_title]['total_appointments'] = $value->total_appointments;
                $mob_summary[$value->drug_generic_title]['total_patients'] = $value->total_patients;
                $mob_summary[$value->drug_generic_title]['total_generic'] = $value->total_generic;
            }
            $value->drug_percent = number_format((100 * $value->total_drug) / $value->total_generic, 2);
            $mob_summary[$value->drug_generic_title]['molecules_data'][] = $value;
        }
        $mob_summary = array_values($mob_summary);
        $this->load->library('excel');
        $object = new PHPExcel();
        $object->setActiveSheetIndex(0);

        $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
        $object = report_header($object,$objDrawing);
        $table_columns = array();
        $table_columns[] = ["label" => "Molecules", "width" => 40];
        $table_columns[] = ["label" => "SKU", "width" => 25];
        $table_columns[] = ["label" => "Brands", "width" => 30];
        $table_columns[] = ["label" => "Percentage(%)", "width" => 20];
        $table_columns[] = ["label" => "Indication", "width" => 40];
        $table_columns[] = ["label" => "Molecules Prescribed (%)", "width" => 20];
        $column = 0;
        $excel_row = 5;
        $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'Filters:');
        $excel_row++;
        if(!empty($request_data['search_sku'])) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'SKU:');
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $request_data['search_sku']);
            $excel_row++;
        }

        if(!empty($request_data['search_brands'])) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'Brands:');
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $request_data['search_brands']);
            $excel_row++;
        }

        if(!empty($request_data['search_indication'])) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'Indication:');
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $request_data['search_indication']);
            $excel_row++;
        }
        
        if(!empty($request_data['drug_generic_name'])) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'Molecules:');
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, trim($request_data['drug_generic_name'], ', '));
            $excel_row++;
        }
        if(count($clinic_arr) > 0) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'Clinic:');
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, implode(', ', $clinic_arr));
            $excel_row++;
        }

        $excel_row++;
        foreach($table_columns as $key => $field) {
            $object = report_header_row($object,$column, $excel_row, $field['label'],$field['width']);
            $column++;
        }
        $excel_row++;
        foreach ($mob_summary as $row) {
            $object = report_header_sub_row($object,0, $excel_row, $row['drug_generic_title'], false);
            $object = report_header_sub_row($object,1, $excel_row, '', false);
            $object = report_header_sub_row($object,2, $excel_row, '', false);
            $object = report_header_sub_row($object,3, $excel_row, '', false);
            $object = report_header_sub_row($object,4, $excel_row, '', false);
            $percent = 0;
            if(isset($row['total_appointments']) && isset($row['total_generic'])) {
                $percent = (100 * $row['total_generic']) / $row['total_appointments'];
            }
            $object = report_header_sub_row($object,5, $excel_row, number_format($percent,2), false);
            $excel_row++;
            foreach ($row['molecules_data'] as $value) {
                $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $value->sku);
                $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $value->drug_name_with_unit);
                $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $value->drug_percent);
                $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $value->diagonisis_list);

                $excel_row++;
            }
        }
        $object_writer = new PHPExcel_Writer_Excel2007($object);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="MOB summary.xls"');
        $object_writer->save('php://output');
        die;
    }  

    public function lost_patient($request_data) {
        $request_data = json_decode(base64_decode($request_data), true);
        if(empty($_SERVER['HTTP_REFERER'])){
            // die;
        }
        if(!$this->check_token($request_data))
            die('Invalid token');
        $where = array();
        $start_date = "";
        $end_date = "";
        $where['search_kco'] = $request_data['search_kco'];
        $where['start_date'] = $request_data['from_date'];
        $where['end_date'] = $request_data['to_date'];
        $where['doctor_id'] = $request_data['doctor_id'];
        $where['clinic_id'] = $request_data['search_clinic_id'];
        $clinic_arr = array();
        if(!empty($where['clinic_id']) && count($where['clinic_id']) > 0) {
            $clinicList = $this->report_model->get_clinics($where['clinic_id']);
            $clinic_arr = array_column($clinicList, 'clinic_name');
        }
        $result = $this->report_model->get_lost_patient($where);
        foreach ($result as $key => $value) {
            if(!empty($value->visited_ids) && !empty($value->not_visited_ids)) {
                $visited_ids = explode(',', $value->visited_ids);
                $not_visited_ids = explode(',', $value->not_visited_ids);
                $match_value = array_intersect($visited_ids,$not_visited_ids);
                $result[$key]->last_6_month_not_visited = $value->last_6_month_not_visited - count($match_value);
            }
            $result[$key]->percentage = number_format(100 * $value->last_6_month / $value->total,2);
            $result[$key]->not_visited_percentage = number_format(100 * $result[$key]->last_6_month_not_visited / $value->total,2);
        }
        $this->load->library('excel');
        $object = new PHPExcel();
        $object->setActiveSheetIndex(0);

        $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
        $object = report_header($object,$objDrawing);
        $table_columns = array();
        $table_columns[] = ["label" => "K/C/O", "width" => 40];
        $table_columns[] = ["label" => "Total Number of patients", "width" => 30];
        $table_columns[] = ["label" => "Visited patients applied range", "width" => 30];
        $table_columns[] = ["label" => "Visited Percentage (%)", "width" => 30];
        $table_columns[] = ["label" => "Not visited patients applied range", "width" => 35];
        $table_columns[] = ["label" => "Not visited Percentage (%)", "width" => 30];
        $column = 0;
        $excel_row = 5;
        $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'Filters:');
        $excel_row++;
        
        if(!empty($where['start_date'])) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'From Date:');
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, date("d/m/Y", strtotime($where['start_date'])));
            $excel_row++;
        }
        if(!empty($where['end_date'])) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'To Date:');
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, date("d/m/Y", strtotime($where['end_date'])));
            $excel_row++;
        }

        if(!empty($where['search_kco'])) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'K/C/O:');
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $where['search_kco']);
            $excel_row++;
        }

        if(count($clinic_arr) > 0) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'Clinic:');
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, implode(', ', $clinic_arr));
            $excel_row++;
        }

        $excel_row++;
        foreach($table_columns as $key => $field) {
            $object = report_header_row($object,$column, $excel_row, $field['label'],$field['width']);
            $column++;
        }
        $excel_row++;
        foreach ($result as $row) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $row->disease_name);
            $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $row->total);
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $row->last_6_month);
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $row->percentage);
            $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $row->last_6_month_not_visited);
            $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $row->not_visited_percentage);
            $excel_row++;
        }
        $object_writer = new PHPExcel_Writer_Excel2007($object);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Lost patients.xls"');
        $object_writer->save('php://output');
        die;
    }

    public function patient_progress($request_data) {
        $request_data = json_decode(base64_decode($request_data), true);
        if(empty($_SERVER['HTTP_REFERER'])){
            die;
        }
        if(!$this->check_token($request_data))
            die('Invalid token');

        $search_analytics_arr = [];
        foreach ($request_data['search_disease_id'] as $key => $value) {
            $search_analytics_arr[$value] = $request_data['search_analytics_id'][$key];
        }
        $analytics_list =  $this->report_model->get_analytics($request_data['search_analytics_id']);
        $analytics_list = array_column($analytics_list, 'health_analytics_test_validation', 'health_analytics_test_id');
        $where = array();
        $where['drug_id'] = $request_data['drug_id'];
        $where['drug_generic_id'] = $request_data['drug_generic_id'];
        $where['report_from'] = $request_data['report_from'];
        $where['search_disease_id'] = $request_data['search_disease_id'];
        $where['search_analytics_id'] = $request_data['search_analytics_id'];
        $where['doctor_id'] = $request_data['doctor_id'];
        $where['clinic_id'] = $request_data['search_clinic_id'];
        $where['start_date'] = $request_data['from_date'];
        $where['end_date'] = $request_data['to_date'];
        
        $result = $this->report_model->get_patient_progress_data($where);
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

        $health_analytics_report = $this->report_model->get_health_analytics_report($request_data['from_date'], $request_data['to_date'], $search_arr, $where);
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
        $analytics_list =  $this->report_model->get_analytics($request_data['search_analytics_id']);
        $analytics_list = array_column($analytics_list, 'health_analytics_test_name', 'health_analytics_test_id');
        
        $this->load->library('excel');
        $object = new PHPExcel();
        $object->setActiveSheetIndex(0);

        $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
        $object = report_header($object,$objDrawing);
        $table_columns = array();
        $table_columns[] = ["label" => (($request_data['report_from']==1)?"K/C/O":"Diagnoses"), "width" => 35];
        $table_columns[] = ["label" => "Analytics Test Name", "width" => 35];
        $table_columns[] = ["label" => "Total Number Of Patients", "width" => 30];
        $table_columns[] = ["label" => "Number Of Patients Improving", "width" => 30];
        $table_columns[] = ["label" => "Improving Percentage (%)", "width" => 30];
        $table_columns[] = ["label" => "Number Of Patients Deteriorating", "width" => 35];
        $table_columns[] = ["label" => "Deteriorating Percentage (%)", "width" => 30];
        $table_columns[] = ["label" => "Number Of Patients Maintained", "width" => 30];
        $table_columns[] = ["label" => "Maintained Percentage (%)", "width" => 30];
        $table_columns[] = ["label" => "Number Of Patients Not Tracked", "width" => 35];
        $table_columns[] = ["label" => "Not Tracked Percentage (%)", "width" => 30];

        $clinic_arr = array();
        if(!empty($where['clinic_id']) && count($where['clinic_id']) > 0) {
            $clinicList = $this->report_model->get_clinics($where['clinic_id']);
            $clinic_arr = array_column($clinicList, 'clinic_name');
        }
        $column = 0;
        $excel_row = 5;
        $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'Filters:');
        $excel_row++;
        if(!empty($request_data['drug_generic_name'])) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'Generic name:');
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, trim($request_data['drug_generic_name'], ', '));
            $excel_row++;
        }
        if(!empty($request_data['drug_name'])) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'Drug name:');
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, trim($request_data['drug_name'], ', '));
            $excel_row++;
        }
        if(!empty($where['start_date'])) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'From Date:');
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, date("d/m/Y", strtotime($where['start_date'])));
            $excel_row++;
        }
        if(!empty($where['end_date'])) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'To Date:');
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, date("d/m/Y", strtotime($where['end_date'])));
            $excel_row++;
        }
        if(count($clinic_arr) > 0) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'Clinic:');
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, implode(', ', $clinic_arr));
            $excel_row++;
        }

        $excel_row++;
        foreach($table_columns as $key => $field) {
            $object = report_header_row($object,$column, $excel_row, $field['label'],$field['width']);
            $column++;
        }
        $excel_row++;
        foreach ($data as $row) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $row->disease_name);
            $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $analytics_list[$row->analytics_id]);
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $row->total);
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $row->improving);
            $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $row->improving_percent);
            $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $row->deteriorating);
            $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $row->deteriorating_percent);
            $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, $row->maintained);
            $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $row->maintained_percent);
            $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, $row->not_tracked);
            $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, $row->not_tracked_percent);
            
            $excel_row++;
        }
        $object_writer = new PHPExcel_Writer_Excel2007($object);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Patients progress.xls"');
        $object_writer->save('php://output');
        die;
    } 

    public function invoice_summary($request_data) {
        $request_data = json_decode(base64_decode($request_data), true);
        if(empty($_SERVER['HTTP_REFERER'])){
            die;
        }
        if(!$this->check_token($request_data))
            die('Invalid token');
        $where = array();
        $start_date = "";
        $end_date = "";
        $where['search_str'] = $request_data['search_str'];
        $where['start_date'] = $request_data['from_date'];
        $where['end_date'] = $request_data['to_date'];
        $where['doctor_id'] = $request_data['doctor_id'];
        $where['clinic_id'] = $request_data['search_clinic_id'];
        $clinic_arr = array();
        if(!empty($where['clinic_id']) && count($where['clinic_id']) > 0) {
            $clinicList = $this->report_model->get_clinics($where['clinic_id']);
            $clinic_arr = array_column($clinicList, 'clinic_name');
        }
        $result = $this->report_model->get_invoice_summary($where);
        $footer_sum = array();
        $footer_sum['discount'] = array_sum(array_column($result, 'billing_discount'));
        $footer_sum['billing_tax'] = array_sum(array_column($result, 'billing_tax'));
        $footer_sum['grand_total'] = array_sum(array_column($result, 'billing_grand_total'));
        $footer_sum['total_payable'] = array_sum(array_column($result, 'billing_total_payable'));
        $footer_sum['advance_amount'] = array_sum(array_column($result, 'billing_advance_amount'));
        $footer_sum['paid_amount'] = array_sum(array_column($result, 'billing_paid_amount'));
        $this->load->library('excel');
        $object = new PHPExcel();
        $object->setActiveSheetIndex(0);

        $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
        $object = report_header($object,$objDrawing);
        $table_columns = array();
        $table_columns[] = ["label" => "Name", "width" => 20];
        $table_columns[] = ["label" => "Mobile No", "width" => 21];
        $table_columns[] = ["label" => "Invoice No", "width" => 20];
        $table_columns[] = ["label" => "INV Date", "width" => 20];
        $table_columns[] = ["label" => "Total", "width" => 20];
        $table_columns[] = ["label" => "Discount", "width" => 20];
        $table_columns[] = ["label" => "Tax", "width" => 20];
        $table_columns[] = ["label" => "Payable Amount", "width" => 20];
        $table_columns[] = ["label" => "Advance Pay", "width" => 20];
        $table_columns[] = ["label" => "Paid Amount", "width" => 20];
        
        $column = 0;
        $excel_row = 5;
        $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'Filters:');
        $excel_row++;
        if(!empty($where['search_str'])) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'Search:');
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $where['search_str']);
            $excel_row++;
        }
        if(!empty($where['start_date'])) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'From Date:');
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, date("d/m/Y", strtotime($where['start_date'])));
            $excel_row++;
        }
        if(!empty($where['end_date'])) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'To Date:');
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, date("d/m/Y", strtotime($where['end_date'])));
            $excel_row++;
        }
         if(count($clinic_arr) > 0) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'Clinic:');
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, implode(', ', $clinic_arr));
            $excel_row++;
        }

        $excel_row++;
        foreach($table_columns as $key => $field) {
            $object = report_header_row($object,$column, $excel_row, $field['label'],$field['width']);
            $column++;
        }
        $excel_row++;
        foreach ($result as $row) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $row->user_name);
            $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $row->user_phone_number);
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $row->invoice_number);
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $row->billing_invoice_date);
            $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, number_format($row->billing_grand_total,2));
            $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, number_format($row->billing_discount,2));
            $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, number_format($row->billing_tax,2));
            $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, number_format($row->billing_total_payable,2));
            $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, ($row->billing_advance_amount > 0) ? number_format($row->billing_advance_amount,2) : '');
            $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, ($row->billing_paid_amount > 0) ? number_format($row->billing_paid_amount,2) : 0);
            $excel_row++;
        }
        $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, "Total");
        $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, number_format($footer_sum['grand_total'],2));
        $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, number_format($footer_sum['discount'],2));
        $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, number_format($footer_sum['billing_tax'],2));
        $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, number_format($footer_sum['total_payable'],2));
        $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, number_format($footer_sum['advance_amount'],2));
        $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, number_format($footer_sum['paid_amount'],2));
        $object_writer = new PHPExcel_Writer_Excel2007($object);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Invoice summary.xls"');
        $object_writer->save('php://output');
        die;
    }

    public function cancel_appointment($request_data) {
        $request_data = json_decode(base64_decode($request_data), true);
        if(empty($_SERVER['HTTP_REFERER'])){
            die;
        }
        if(!$this->check_token($request_data))
            die('Invalid token');
        $where = array();
        $start_date = "";
        $end_date = "";
        $where['search_str'] = $request_data['search_str'];
        $where['start_date'] = $request_data['from_date'];
        $where['end_date'] = $request_data['to_date'];
        $where['doctor_id'] = $request_data['doctor_id'];
        $where['clinic_id'] = $request_data['search_clinic_id'];
        $clinic_arr = array();
        if(!empty($where['clinic_id']) && count($where['clinic_id']) > 0) {
            $clinicList = $this->report_model->get_clinics($where['clinic_id']);
            $clinic_arr = array_column($clinicList, 'clinic_name');
        }
        $result = $this->report_model->get_cancel_appointment($where);
        $this->load->library('excel');
        $object = new PHPExcel();
        $object->setActiveSheetIndex(0);

        $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
        $object = report_header($object,$objDrawing);
        $table_columns = array();
        $table_columns[] = ["label" => "Unique ID", "width" => 15];
        $table_columns[] = ["label" => "Name", "width" => 20];
        $table_columns[] = ["label" => "Mobile No", "width" => 21];
        $table_columns[] = ["label" => "Appointment Date Time", "width" => 25];
        $table_columns[] = ["label" => "Cancel Date Time", "width" => 25];
        
        $column = 0;
        $excel_row = 5;
        $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'Filters:');
        $excel_row++;
        if(!empty($where['search_str'])) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'Search:');
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $where['search_str']);
            $excel_row++;
        }
        if(!empty($where['start_date'])) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'From Date:');
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, date("d/m/Y", strtotime($where['start_date'])));
            $excel_row++;
        }
        if(!empty($where['end_date'])) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'To Date:');
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, date("d/m/Y", strtotime($where['end_date'])));
            $excel_row++;
        }
        if(count($clinic_arr) > 0) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'Clinic:');
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, implode(', ', $clinic_arr));
            $excel_row++;
        }

        $excel_row++;
        foreach($table_columns as $key => $field) {
            $object = report_header_row($object,$column, $excel_row, $field['label'],$field['width']);
            $column++;
        }
        $excel_row++;
        foreach ($result as $row) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $row->user_unique_id);
            $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $row->user_name);
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $row->user_phone_number);
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $row->appointment_date . ' ' . $row->appointment_from_time);
            $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $row->appointment_updated_at);
            $excel_row++;
        }
        $object_writer = new PHPExcel_Writer_Excel2007($object);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Cancel Appointments.xls"');
        $object_writer->save('php://output');
        die;
    }

    private function check_token($request_data) {
        $token_where = array(
            "udt_u_id" => $request_data['user_id'],
            "udt_security_token" => $request_data['access_token']
        );
        $device_token_data = $this->Common_model->get_single_row(TBL_USER_DEVICE_TOKENS, "udt_u_id", $token_where);
        if (is_array($device_token_data) && count($device_token_data) > 0 && !empty($device_token_data['udt_u_id'])) {
            $this->user_id = $user_id;
            return TRUE;
        }
        return false;
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

}