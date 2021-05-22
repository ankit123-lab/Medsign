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
        $laboratory_name = !empty($this->post_data['laboratory_name']) ? trim($this->Common_model->escape_data($this->post_data['laboratory_name'])) : '';
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
            
            if(empty($laboratory_id)){
                $insert_array = array(
                    "laboratary_name" => $laboratory_name,
                    "laboratary_created_at" => $this->utc_time_formated,
                    "laboratary_status" => 1,
                    "user_type" => 1,
                    "created_by" => $this->user_id
                );
                $laboratory_id = $this->Common_model->add_laboratory($insert_array);
            }
            
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
                //"reminder_status" => 1
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
                    $reminder['reminder_type'] = (int) $reminder['reminder_type'];
                    $reminder['reminder_created_by'] = (int) $reminder['reminder_created_by'];
                    $reminder['reminder_user_id'] = (int) $reminder['reminder_user_id'];
                    $reminder['reminder_id'] = (int) $reminder['reminder_id'];
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
			$reminder_record_data = !empty($this->post_data['reminder_record_data']) ? $this->post_data['reminder_record_data'] : '';
            //$status = $this->post_data['status'];
            $date = $this->post_data['date'];
			
			if (empty($reminder_record_data) || empty($date)) {
                $this->bad_request();
                exit;
            }
			$reminder_record_data_list = json_decode($reminder_record_data, true);
			if(!empty($reminder_record_data_list)){

                $reminder_id_arr = array_column($reminder_record_data_list,'reminder_id');
                $columns = 'reminder_record_reminder_id,reminder_record_date,reminder_time';
                $where = array('reminder_record_reminder_id' => $reminder_id_arr);
                $get_reminder_records = $this->Reminder->get_reminder_records($where, $columns);
                $reminder_records_arr = array();
                foreach ($get_reminder_records as $key => $value) {
                    $k = $value['reminder_record_reminder_id'].'_'.date('Ymd',strtotime($value['reminder_record_date'])).'_'.$value['reminder_time'];
                    $reminder_records_arr[] = $k;
                }
                
				foreach($reminder_record_data_list as $rrd){
					$status 	 = $rrd['status'];
					$reminder_id = $rrd['reminder_id'];
                    if(!empty($rrd['reminder_record_date'])) {
                        $date = $rrd['reminder_record_date'];
                    }
					if(empty($status)){ $status = 1; }
                    $k = $reminder_id.'_'.date('Ymd',strtotime($date)).'_'.$rrd['reminder_time'];
                    $reminder_record_status = 1;
                    if(in_array($k, $reminder_records_arr)) {
                        $reminder_record_status = 9;
                    }
					$insert_array[] = array(
						"reminder_record_reminder_id" 	=> $reminder_id,
						"reminder_record_taken_status" 	=> $status,
						"reminder_record_date" 			=> $date,
                        "reminder_time"                 => $rrd['reminder_time'],
						"reminder_record_created_at" 	=> $this->utc_time_formated,
                        "reminder_record_created_by"    => $this->user_id,
						"reminder_record_status" 		=> $reminder_record_status
					);
				}
				//$is_added = $this->Reminder->insert(TBL_REMINDER_RECORDS, $insert_array);
				if (!empty($insert_array)) {
					$this->db->trans_start();
                    $this->Common_model->insert_multiple(TBL_REMINDER_RECORDS, $insert_array);
                }
			}
			if ($this->db->trans_status() !== FALSE) {
				$this->db->trans_commit();
				$this->my_response['status'] = true;
                $this->my_response['message'] = lang('reminder_record_add');
                $this->my_response['reminder_record_data'] = $reminder_record_data;
                $this->my_response['date'] = $date;
			} else {
				$this->db->trans_rollback();
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

				//  $prev_date = date('Y-m-d', strtotime($date . '-30 days'));
				//  $end = new DateTime($date);
				//  $start = new DateTime($prev_date);
				//  $interval = DateInterval::createFromDateString('1 day');
				//  $period = new DatePeriod($start, $interval, $end);
				//  $final_array=array();
				//  foreach ($period as $dt) {
				//      $between_date = $dt->format("Y-m-d");
				//      //check date exist in DB or not                
				//      $is_exist = in_array($between_date, array_column($chart_data, 'reminder_record_date'));
				//      
				//  }


            $this->my_response['status'] = true;
            $this->my_response['chart_data'] = $chart_data;
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_all_reminder_chart_post() {
        try {
            $date = $this->post_data['date'];
            $other_user_id = $this->post_data['other_user_id'];
            $chart_data = $this->Reminder->get_chart_data($other_user_id, $date);

            $this->my_response['status'] = true;
            $this->my_response['chart_data'] = $chart_data;
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

}
