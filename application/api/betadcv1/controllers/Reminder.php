<?php

/**
 * 
 * This controller use for reminder related activity
 * 
 * 
 * Modified Data :- 2018-03-07
 */
class Reminder extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("Reminder_model", 'Reminder');
    }

    public function add_reminder_post() {

        //post values
        $reminder_type = trim($this->Common_model->escape_data($this->post_data['reminder_type']));
        $other_user_id = trim($this->Common_model->escape_data($this->post_data['other_user_id']));
        $medicine_id = !empty($this->post_data['medicine_id']) ? trim($this->Common_model->escape_data($this->post_data['medicine_id'])) : '';
        $medicine_name = trim($this->Common_model->escape_data($this->post_data['medicine_name']));
        $time_slots = trim($this->Common_model->escape_data($this->post_data['time_slots']));
        $durations = trim($this->Common_model->escape_data($this->post_data['durations']));
        $days = trim($this->Common_model->escape_data($this->post_data['days']));
        $week_days = trim($this->Common_model->escape_data($this->post_data['week_days']));
        $reminder_date = trim($this->Common_model->escape_data($this->post_data['reminder_date']));
        $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
        $doctor_name = trim($this->Common_model->escape_data($this->post_data['doctor_name']));
        $doctor_location = trim($this->Common_model->escape_data($this->post_data['doctor_location']));
        $doctor_fees = trim($this->Common_model->escape_data($this->post_data['doctor_fees']));
        $reminder_notes = trim($this->Common_model->escape_data($this->post_data['reminder_notes']));
        $lab_test_id = !empty($this->post_data['lab_test_id']) ? trim($this->Common_model->escape_data($this->post_data['lab_test_id'])) : '';
        $lab_test_name = trim($this->Common_model->escape_data($this->post_data['lab_test_name']));

        $laboratory_id = !empty($this->post_data['laboratory_id']) ? trim($this->Common_model->escape_data($this->post_data['laboratory_id'])) : '';
        $general_title = trim($this->Common_model->escape_data($this->post_data['general_title']));


        if (empty($reminder_type) || empty($reminder_date)) {
            $this->bad_request();
            exit;
        }
        if (
                $reminder_type == 1 &&
                (empty($medicine_name) ||
                empty($time_slots) ||
                empty($durations) ||
                empty($days)

                )
        ) {
            $this->bad_request();
            exit;
        }
        if (
                $reminder_type == 2 &&
                (
                empty($doctor_name)
                )
        ) {
            $this->bad_request();
            exit;
        }
        if (
                $reminder_type == 3 &&
                (
                empty($doctor_name) 
                )
        ) {
            $this->bad_request();
            exit;
        }
        if ($reminder_type == 4 && empty($general_title)) {
            $this->bad_request();
            exit;
        }

        try {
            //insert into reminder table
            $created_time = $this->utc_time_formated;
            $insert_array = array(
                "reminder_type" => $reminder_type,
                "reminder_created_by" => $this->user_id,
                "reminder_user_id" => $other_user_id,
                "reminder_drug_id" => $medicine_id,
                "reminder_drug_name" => $medicine_name,
                "reminder_timing" => $time_slots,
                "reminder_duration" => $durations,
                "reminder_day" => $days,
                "reminder_start_date" => $reminder_date,
                "reminder_week_day" => $week_days,
                "reminder_doctor_id" => $doctor_id,
                "reminder_doctor_name" => $doctor_name,
                "reminder_doctor_address" => $doctor_location,
                "reminder_doctor_fee" => $doctor_fees,
                "reminder_note" => $reminder_notes,
                "reminder_lab_report_id" => $lab_test_id,
                "reminder_lab_report_name" => $lab_test_name,
                "reminder_laboratory_id" => $laboratory_id,
                "reminder_general_title" => $general_title,
                "reminder_created_at" => $created_time
            );
            $reminder_id = $this->Reminder->add_reminder($insert_array);

            if ($reminder_id > 0) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang("reminder_add_success");
                $reminder_array = array(
                    "reminder_id" => $reminder_id,
                    "reminder_type" => $reminder_type,
                    "reminder_created_by" => $this->user_id,
                    "reminder_user_id" => $other_user_id,
                    "reminder_drug_id" => $medicine_id,
                    "reminder_drug_name" => $medicine_name,
                    "reminder_timing" => $time_slots,
                    "reminder_duration" => $durations,
                    "reminder_day" => $days,
                    "reminder_start_date" => $reminder_date,
                    "reminder_week_day" => $week_days,
                    "reminder_doctor_id" => $doctor_id,
                    "reminder_doctor_name" => $doctor_name,
                    "reminder_doctor_address" => $doctor_location,
                    "reminder_doctor_fee" => $doctor_fees,
                    "reminder_note" => $reminder_notes,
                    "reminder_lab_report_id" => $lab_test_id,
                    "reminder_lab_report_name" => $lab_test_name,
                    "reminder_laboratory_id" => $laboratory_id,
                    "reminder_general_title" => $general_title,
                    "reminder_created_at" => $created_time
                );

                $this->my_response['data'] = $reminder_array;
            } else {
                $this->my_response['status'] = FALSE;
                $this->my_response['message'] = lang("reminder_add_failure");
            }
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function edit_reminder_post() {
        //post values
        $reminder_id = trim($this->Common_model->escape_data($this->post_data['reminder_id']));
        $reminder_type = trim($this->Common_model->escape_data($this->post_data['reminder_type']));
        $other_user_id = trim($this->Common_model->escape_data($this->post_data['other_user_id']));
        $medicine_id = !empty($this->post_data['medicine_id']) ? trim($this->Common_model->escape_data($this->post_data['medicine_id'])) : '';
        $medicine_name = trim($this->Common_model->escape_data($this->post_data['medicine_name']));
        $time_slots = trim($this->Common_model->escape_data($this->post_data['time_slots']));
        $durations = trim($this->Common_model->escape_data($this->post_data['durations']));
        $days = trim($this->Common_model->escape_data($this->post_data['days']));
        $week_days = trim($this->Common_model->escape_data($this->post_data['week_days']));
        $reminder_date = trim($this->Common_model->escape_data($this->post_data['reminder_date']));
        $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
        $doctor_name = trim($this->Common_model->escape_data($this->post_data['doctor_name']));
        $doctor_location = trim($this->Common_model->escape_data($this->post_data['doctor_location']));
        $doctor_fees = trim($this->Common_model->escape_data($this->post_data['doctor_fees']));
        $reminder_notes = trim($this->Common_model->escape_data($this->post_data['reminder_notes']));
        $lab_test_id = !empty($this->post_data['lab_test_id']) ? trim($this->Common_model->escape_data($this->post_data['lab_test_id'])) : '';
        $lab_test_name = trim($this->Common_model->escape_data($this->post_data['lab_test_name']));

        $laboratory_id = !empty($this->post_data['laboratory_id']) ? trim($this->Common_model->escape_data($this->post_data['laboratory_id'])) : '';
        $general_title = trim($this->Common_model->escape_data($this->post_data['general_title']));

        if (empty($reminder_type) || empty($reminder_date) || empty($reminder_id)) {
            $this->bad_request();
            exit;
        }
        if (
                $reminder_type == 1 &&
                (empty($medicine_name) ||
                empty($time_slots) ||
                empty($durations) ||
                empty($days)
                )
        ) {
            $this->bad_request();
            exit;
        }
        if (
                $reminder_type == 2 &&
                (
                empty($doctor_name)
                )
        ) {
            $this->bad_request();
            exit;
        }
        if (
                $reminder_type == 3 &&
                (
                empty($doctor_name) 
                )
        ) {
            $this->bad_request();
            exit;
        }
        if ($reminder_type == 4 && empty($general_title)) {
            $this->bad_request();
            exit;
        }
        try {
            //insert into reminder table
            $udpate_array = array(
                "reminder_type" => $reminder_type,
                "reminder_user_id" => $other_user_id,
                "reminder_drug_id" => $medicine_id,
                "reminder_drug_name" => $medicine_name,
                "reminder_timing" => $time_slots,
                "reminder_duration" => $durations,
                "reminder_day" => $days,
                "reminder_start_date" => $reminder_date,
                "reminder_week_day" => $week_days,
                "reminder_doctor_id" => $doctor_id,
                "reminder_doctor_name" => $doctor_name,
                "reminder_doctor_address" => $doctor_location,
                "reminder_doctor_fee" => $doctor_fees,
                "reminder_note" => $reminder_notes,
                "reminder_lab_report_id" => $lab_test_id,
                "reminder_lab_report_name" => $lab_test_name,
                "reminder_laboratory_id" => $laboratory_id,
                "reminder_general_title" => $general_title,
                "reminder_modified_at" => $this->utc_time_formated
            );
            $reminder_where = array(
                "reminder_id" => $reminder_id,
                "reminder_status" => 1
            );
            $reminder_is_updated = $this->Reminder->edit_reminder($udpate_array, $reminder_where);
            if ($reminder_is_updated > 0) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang("reminder_edit_success");
            } else {
                $this->my_response['status'] = FALSE;
                $this->my_response['message'] = lang("reminder_edit_failure");
            }
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function delete_reminder_post() {
        try {
            $reminder_id = trim($this->Common_model->escape_data($this->post_data['reminder_id']));

            $reminder_where = array(
                "reminder_id" => $reminder_id,
                "reminder_status" => 1
            );
            $delete_array = array(
                "reminder_status" => 9,
                "reminder_modified_at" => $this->utc_time_formated
            );
            $reminder_is_deleted = $this->Reminder->edit_reminder($delete_array, $reminder_where);
            if ($reminder_is_deleted > 0) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang("reminder_delete_success");
            } else {
                $this->my_response['status'] = FALSE;
                $this->my_response['message'] = lang("reminder_delete_failure");
            }
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function sync_reminder_post() {
        try {

            $other_user_id = !empty($this->Common_model->escape_data($this->post_data['other_user_id'])) ? trim($this->Common_model->escape_data($this->post_data['other_user_id'])) : '';

            if (empty($other_user_id)) {
                $this->bad_request();
            }

            //get reminders from db
            $sync_date = $this->post_data['sync_date'];
            $reminder_type = $this->post_data['reminder_type'];

            $reminder_data = $this->Reminder->get_sync_reminders($other_user_id, $sync_date, $reminder_type);

            $inserted = array();
            $updated = array();
            $deleted = array();
            if (!empty($reminder_data)) {
                foreach ($reminder_data as $reminder) {
                    if (empty($sync_date) && $reminder['reminder_status'] == 1) {
                        $inserted[] = $reminder;
                    } else if ($reminder['reminder_status'] == 9) {
                        $deleted[] = $reminder;
                    } else {
                        $updated[] = $reminder;
                    }
                }
            }
            $this->my_response['status'] = true;
            $this->my_response['message'] = lang('reminder_sync');
            $this->my_response['data'] = array();
            $this->my_response['data']['inserted'] = $inserted;
            $this->my_response['data']['updated'] = $updated;
            $this->my_response['data']['deleted'] = $deleted;
            $this->my_response['sync_date'] = $this->utc_time_formated;
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function add_reminder_record_post() {
        try {
            $reminder_id = $this->post_data['reminder_id'];
            $status = $this->post_data['status'];
            $date = $this->post_data['date'];

            if (empty($reminder_id) || empty($date) || empty($status)) {
                $this->bad_request();
                exit;
            }
            if (empty($status)) {
                $status = 1;
            }

            $insert_array = array(
                "reminder_record_reminder_id" => $reminder_id,
                "reminder_record_taken_status" => $status,
                "reminder_record_date" => $date,
                "reminder_record_created_at" => $this->utc_time_formated,
                "reminder_record_status" => 1
            );

            $is_added = $this->Reminder->insert(TBL_REMINDER_RECORDS, $insert_array);
            if ($is_added) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('reminder_record_add');
                $this->my_response['reminder_id'] = $reminder_id;
                $this->my_response['date'] = $date;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_reminder_chart_post() {
        try {
            $date = $this->post_data['date'];
            $chart_data = $this->Reminder->get_chart_data($this->user_id, $date);   
            $all_dates = array();  
            $drug_array = array();       
            if(!empty($chart_data)){
                $colorarray1 = [];
                $cnt1 = 0;
                $clrCodeArr = array_unique(array_column($chart_data,'drug_id'));
                foreach ($clrCodeArr as $key => $val) {
                    $colorarray1[$val] = isset(COLOR_CODE[$cnt1]) ? COLOR_CODE[$cnt1] : '';
                    $cnt1++;
                }
                // $colorarray = [];
                $all_dates = array_values(array_unique(array_column($chart_data, 'reminder_record_date')));
                /*
                foreach ($chart_data as $key => $value) {
                    $colorarray['take_count']           = $value['take_count'];
                    $colorarray['reminder_record_id']           = $value['reminder_record_id'];
                    $colorarray['reminder_day']         = $value['reminder_day'];
                    $colorarray['reminder_record_date'] = $value['reminder_record_date'];
                    $colorarray['drug_id']              = $value['drug_id'];
                    $colorarray['drug_name']            = $value['drug_name'];
                    $colorarray['reminder_timing']      = $value['reminder_timing'];
                    $colorarray['reminder_record_date_group']      = $value['reminder_record_date_group'];
                    $colorarray['drug_color_code']      = $colorarray1[$value['drug_id']];
                     if(empty($finalArray[$value['reminder_record_date']]))
                        $finalArray[$value['reminder_record_date']] = array();
                    
                    $finalArray[$value['reminder_record_date']]['reminder_record_date'] = $value['reminder_record_date'];
                    $finalArray[$value['reminder_record_date']]['reminder_record_data'][] = $colorarray;

                }
                */
                foreach ($chart_data as $value) {
                    if(empty($drug_array[$value['drug_id']])) {
                            $drug_array[$value['drug_id']] = array('drug_id' => $value['drug_id'],'drug_name' => $value['drug_name'], 'drug_color_code' => $colorarray1[$value['drug_id']]);
                            foreach ($all_dates as $reminder_record_date) {
                                $drug_array[$value['drug_id']]['reminder_record_date'][$reminder_record_date] = array();
                            }
                        }

                        $reminder_timing_arr = explode(',', $value['reminder_timing']);
                        $reminder_record_date_group_arr = explode(',', $value['reminder_record_date_group']);
                        $take_count_arr = array();
                        if($value['take_count'] >= count($reminder_timing_arr) || count($reminder_record_date_group_arr) >= count($reminder_timing_arr)) {
                            for($i=0; $i<$value['take_count'];$i++) {
                                $take_count_arr[] = array(
                                    'is_taken' => true,
                                    'time' => get_display_date_time('h:i A', $reminder_record_date_group_arr[$i])
                                );
                            }
                        } else {
                            for($j=0; $j<count($reminder_record_date_group_arr);$j++) {
                                $taken_time = get_display_date_time('H:i:s', $reminder_record_date_group_arr[$j]);
                                for ($i=0; $i < count($reminder_timing_arr); $i++) {
                                    $flgToUpd = true;
                                    $time = $reminder_timing_arr[$i].':00';
                                    $time2 = $reminder_timing_arr[$i+1].':00';
                                    if(isset($reminder_timing_arr[$i+1])){
                                        if(strtotime($taken_time) > strtotime($time) && strtotime($taken_time) < strtotime($time2)) {
                                            $is_taken = true;
                                            $take_time = get_display_date_time('h:i A', $reminder_record_date_group_arr[$j]);
                                        }elseif(!isset($take_count_arr[$take_time])){
                                            $is_taken = false;
                                            $take_time = date('h:i A', strtotime($time));
                                        }else{
                                            $flgToUpd = false;
                                        }
                                    }elseif(strtotime($taken_time) > strtotime($time)){
                                        $is_taken = true;
                                        $take_time = get_display_date_time('h:i A', $reminder_record_date_group_arr[$j]);
                                    }else{
                                        $is_taken = false;
                                        $take_time = date('h:i A', strtotime($time));
                                    }
                                    if($flgToUpd == true){
                                        $take_count_arr[$time] = array('is_taken' => $is_taken,'time' => $take_time);
                                    }
                                }
                            }
                        }

                        $drug_array[$value['drug_id']]['reminder_record_date'][$value['reminder_record_date']] = array(
                            'take_count' => $value['take_count'],
                            'reminder_record_id' => $value['reminder_record_id'],
                            'reminder_record_date' => $value['reminder_record_date'],
                            'drug_color_code' => $value['drug_color_code'],
                            'reminder_timing' => $value['reminder_timing'],
                            'reminder_record_date_group' => $value['reminder_record_date_group'],
                            'no_take_count' => count($reminder_timing_arr) - $value['take_count'],
                            'take_count_arr' => $take_count_arr,
                        );
                }
                
            } 
            // die;
            //pr($chart_data);exit;
            //$prev_date = date('Y-m-d', strtotime($date . '-30 days'));
            //$end = new DateTime($date);
            //$start = new DateTime($prev_date);
            //$interval = DateInterval::createFromDateString('1 day');
            //$period = new DatePeriod($start, $interval, $end);
            //$final_array=array();
            //foreach ($period as $dt) {
            //    $between_date = $dt->format("Y-m-d");
            //    //check date exist in DB or not                
            //    $is_exist = in_array($between_date, array_column($chart_data, 'reminder_record_date'));
            //    
            //}
            $this->my_response['status'] = true;
            $this->my_response['chart_data'] = array('reminder_record_all_dates' => $all_dates, 'drug_data' => array_values($drug_array));
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }
}