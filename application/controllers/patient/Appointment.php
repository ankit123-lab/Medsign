<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Appointment extends MY_Controller {

    public function __construct() {

        parent::__construct();
        $this->load->library('patient_auth');
        $this->load->model('patient_model','patient');
        $this->load->library('form_validation');
        $this->load->library("pagination");
        if (!$this->patient_auth->is_logged_in()) {
            redirect('patient/login');
        }
    }

    public function appointment_list() {
        $view_data = array();
        $view_data['breadcrumbs'] = "Appointments";
        $view_data['page_title'] = "Appointments";
        $config = array();
        $config["base_url"] = site_url() . "patient/appointment_list/";
        $config["total_rows"] = $this->patient->get_appointments_list($this->patient_auth->get_user_id(), true);
        $config["per_page"] = 10;
        $config["uri_segment"] = 3;
        $config['reuse_query_string'] = true;
        $config['attributes'] = array('class' => 'page-link');
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(3))? $this->uri->segment(3) : 0;
        $view_data["links"] = $this->pagination->create_links();
        $view_data['appointments'] = $this->patient->get_appointments_list($this->patient_auth->get_user_id(), false, $config["per_page"], $page);
        $view_data['doctors'] = $this->patient->get_patient_doctors($this->patient_auth->get_user_id());
        $view_data['appointment_types'] = $this->Common_model->get_all_rows('me_appointment_type', 'appointment_type_id,appointment_type_name_en', ['appointment_type_status' => 1]);
        $this->load->view('patient/appointments_view', $view_data);
    }

    public function appointment_book() {
        $view_data = array();
        $view_data['breadcrumbs'] = "Book Appointment";
        $view_data['page_title'] = "Book Appointment";
        $view_data['speciality'] = $this->patient->get_speciality();
        $search_params = array(
            'fees' => $this->input->get('fees'),
            'year_of_experience' => $this->input->get('year_of_experience'),
            'sex' => $this->input->get('sex'),
            'speciality' => $this->input->get('speciality'),
            'search_txt' => $this->input->get('search_txt'),
            'available_today' => $this->input->get('available_today'),
        );
        $page = ($this->uri->segment(3))? $this->uri->segment(3) : 0;
        $primary_doctor = [];
        if($page == 0 && empty($search_params['fees']) && empty($search_params['year_of_experience']) && empty($search_params['sex']) && empty($search_params['speciality']) && empty($search_params['search_txt']) && empty($search_params['available_today']))
            $primary_doctor = $this->patient->get_primary_doctor($this->patient_auth->get_user_id());

        if(!empty($primary_doctor->doctor_id))
            $search_params['primary_doctor_id'] = $primary_doctor->doctor_id;

        $config = array();
        $config["base_url"] = site_url() . "patient/appointment_book/";
        $config["total_rows"] = $this->patient->search_doctors($search_params, true);
        $config["per_page"] = 10;
        $config["uri_segment"] = 3;
        $config['reuse_query_string'] = true;
        $config['attributes'] = array('class' => 'page-link');
        $this->pagination->initialize($config);
        $view_data["links"] = $this->pagination->create_links();
        $doctors = $this->patient->search_doctors($search_params, false, $config["per_page"], $page);
        $view_data['doctors'] = array();
        if(!empty($primary_doctor))
            $view_data['doctors'][] = $primary_doctor;
        $view_data['sex_array'] = ['male' => 'Male', 'female' => 'Female', 'any' => 'Any'];
        $view_data['fees_array'] = [
            '0-100' => '<100',
            '101-200' => '101-200',
            '201-300' => '201-300',
            '301-500' => '301-500',
            '501-750' => '501-750',
            '751-1000' => '751-1000',
            '1000-<' => '1001<',
        ];
        if(!empty($doctors))
            $view_data['doctors'] = array_merge($view_data['doctors'], $doctors);
        $this->load->view('patient/appointment_book_view', $view_data);
    }

    public function book_now($id) {
        $ids = encrypt_decrypt($id,'decrypt');
        $ids_arr = explode('_', $ids);
        $doctor_id = $ids_arr[0];
        $clinic_id = $ids_arr[1];
        $view_data = array();
        $this->form_validation->set_rules('doctor_id', 'doctor id', 'required|trim');
        $this->form_validation->set_rules('clinic_id', 'clinic id', 'required|trim');
        $this->form_validation->set_rules('appointment_date', 'appointment date', 'required|trim');
        $this->form_validation->set_rules('appointment_type', 'appointment type', 'required|trim');
        $this->form_validation->set_rules('appointment_from_time', 'appointment time', 'required|trim');
        $this->form_validation->set_rules('appointment_to_time', 'appointment to time', 'required|trim');
        $this->form_validation->set_rules('doctor_availability_id', 'doctor availability id', 'required|trim');
        if ($this->form_validation->run() !== FALSE) {
            $doctor_id = set_value('doctor_id');
            $patient_id = $this->patient_auth->get_user_id();
            $clinic_id = set_value('clinic_id');
            $clinic_id = set_value('clinic_id');
            $date_arr = explode('/', set_value('appointment_date'));
            $date = $date_arr[2].'-'.$date_arr[1].'-'.$date_arr[0];
            $start_time = set_value('appointment_from_time');
            $end_time = set_value('appointment_to_time');
            $doctor_avialibility_id = set_value('doctor_availability_id');
            $appointment_type = set_value('appointment_type');
            if($appointment_type == 5) {
                $doctor_details = $this->patient->doctor_details($doctor_id, $clinic_id);
                $global_settings = $this->Common_model->get_single_row('me_global_settings','global_setting_value', ['global_setting_key'=> 'minimum_minutes_video_call_invitation']);
                $minimum_minutes = $global_settings['global_setting_value'];
                $is_booked_video_appointment = true;
                if(empty($doctor_details->setting_data) || ($minimum_minutes > ($doctor_details->setting_data/60))) {
                    $this->session->set_flashdata('error', 'Video consultation appointment not available for this doctor');
                    redirect(site_url('patient/book_now/'.encrypt_decrypt($doctor_id,'encrypt')));
                    exit;
                }
            }
            $user_type = 1;
            $start_timestamp = strtotime($date . " " . $start_time . ":00");
            $end_timestamp = strtotime($date . " " . $end_time . ":00");
           //check doctor available or not
            $request_data = array(
                "doctor_id" => $doctor_id,
                "date" => $date,
                "start_time" => $start_time,
                "end_time" => $end_time,
            );
            $is_doctor_available = $this->patient->check_doctor_available($request_data);
            if ($is_doctor_available) {
                $insert_array = array(
                    "appointment_user_id" => $patient_id,
                    "appointment_doctor_user_id" => $doctor_id,
                    "appointment_clinic_id" => $clinic_id,
                    "appointment_type" => $appointment_type,
                    "appointment_doctor_availability_id" => $doctor_avialibility_id,
                    "appointment_from_time" => $start_time,
                    "appointment_to_time" => $end_time,
                    "appointment_date" => $date,
                    "appointment_booked_by" => $this->patient_auth->get_logged_user_id(),
                    "appointment_created_at" => $this->utc_time_formated,
                );
                $is_patient_appointments_exist = $this->patient->is_patient_appointments_exist(array('appointment_user_id' => $patient_id));
                $is_booked = $this->Common_model->insert(TBL_APPOINTMENTS, $insert_array);
                if ($is_booked > 0) {
                    //set the reminder before 1 hour after booking the appointment
                    $get_doctor_details = $this->patient->doctor_detail($doctor_id, $clinic_id, 1);
                    $doctor_name = '';
                    $doctor_address = '';

                    if (!empty($get_doctor_details[0]) && !empty($get_doctor_details[0]['user_first_name'])) {
                        $doctor_name = DOCTOR . $get_doctor_details[0]['user_first_name'] . " " . $get_doctor_details[0]['user_last_name'];
                    }

                    if (!empty($get_doctor_details[0]) && !empty($get_doctor_details[0]['address_name'])) {
                        $doctor_address = $get_doctor_details[0]['address_name'] . ', ' . $get_doctor_details[0]['city_name'] . ', ' . $get_doctor_details[0]['state_name'] . ', ' . $get_doctor_details[0]['country_name'];
                    }

                    $doctor_fees = !empty($get_doctor_details[0]['doctor_clinic_mapping_fees']) ? $get_doctor_details[0]['doctor_clinic_mapping_fees'] : '';
                    $clinic_name = !empty($get_doctor_details[0]['clinic_name']) ? $get_doctor_details[0]['clinic_name'] : '';
                    $notification_array = array();
                    // if appointment is booked by some one else instead of the patient then send the notification
                    // to who booked the appointment and to the patient
                    //get the user detail
                    $patient_user_detail = $this->patient->get_details_by_id($patient_id);
                    if(empty($patient_user_detail['user_source_id']) && !$is_patient_appointments_exist) {
                        $this->Common_model->update('me_users', array('user_source_id' => $doctor_id), array('user_id' => $patient_id));
                    }
                    $patient_user_name = '';
                    if (!empty($patient_user_detail) && !empty($patient_user_detail['user_first_name'])) {
                        $patient_user_name = $patient_user_detail['user_first_name'] . ' ' . $patient_user_detail['user_last_name'];
                    }
                    $appointment_time = date("d/m/Y", strtotime($date)) . ' ' . date('h:i A', $start_timestamp);
                    $send_message = sprintf(lang('appointment_book_notification_patient'), $patient_user_name, $doctor_name, $clinic_name, $appointment_time);
                        //send notification to the patient    
                        $notification_array[] = array(
                            'notification_list_user_id' => $patient_id,
                            'notification_list_user_type' => 1,
                            'notification_list_type' => 2,
                            'notification_list_message' => $send_message,
                            'notification_list_created_at' => $this->utc_time_formated
                        );
                        //send notification to the doctor
                        $notification_array[] = array(
                            'notification_list_user_id' => $doctor_id,
                            'notification_list_user_type' => 2,
                            'notification_list_type' => 2,
                            'notification_list_message' => $send_message,
                            'notification_list_created_at' => $this->utc_time_formated
                        );
                    //set reminder time before the 24 hour of the appointment
                    //if time is less than 24 hour then set the alarm before 2 hour
                    //if time is less than 2 hour then set no alarm
                    $reminder_time_24_hour = date('Y-m-d H:i:s', $start_timestamp - 86400);
                    $reminder_time_2_hour = date('Y-m-d H:i:s', $start_timestamp - 7200);
                    $reminder_time_stamp = get_display_date_time("Y-m-d H:i:s");
                    $reminder_array = array(
                        'reminder_type' => 2,
                        'reminder_user_id' => $patient_id,
                        'reminder_created_by' => $patient_id,
                        'reminder_start_date' => $date,
                        'reminder_doctor_id' => $doctor_id,
                        'reminder_doctor_address' => $doctor_address,
                        'reminder_doctor_name' => $doctor_name,
                        'reminder_doctor_fee' => $doctor_fees,
                        'reminder_note' => "Booked appointment",
                        'reminder_created_at' => $this->utc_time_formated,
                        'reminder_appointment_id' => $is_booked
                    );

                    if (strtotime($reminder_time_24_hour) > strtotime($reminder_time_stamp)) {
                        $reminder_array['reminder_timing'] = date('H:i', $start_timestamp - 86400);
                        $reminder_array['reminder_start_date'] = date('Y-m-d', $start_timestamp - 86400);
                        $this->Common_model->insert(TBL_REMINDERS, $reminder_array);
                    } else {

                        if (strtotime($reminder_time_2_hour) > strtotime($reminder_time_stamp)) {
                            $reminder_array['reminder_timing'] = date('H:i', $start_timestamp - 7200);
                            $reminder_array['reminder_start_date'] = date('Y-m-d', $start_timestamp - 7200);
                            $this->Common_model->insert(TBL_REMINDERS, $reminder_array);
                        }
                    }

                    $this->Common_model->insert_multiple(TBL_NOTIFICATION, $notification_array);

                    $send_notification_data = array(
                        'notification_list_type' => 2,
                        'action' => 'book'
                    );
                    $cron_job_path = APP_CRON_PATH . " notification/send_notification/" . base64_encode(json_encode($send_notification_data));
                    exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");
                    //send sms and email to the doctor based on the status
                    $send_doctor_data = array(
                        'doctor_id' => $doctor_id,
                        'patient_name' => $patient_user_name,
                        'doctor_name' => $doctor_name,
                        'doctor_phone_number' => $get_doctor_details[0]['user_phone_number'],
                        'doctor_email' => ($get_doctor_details[0]['user_email_verified'] == 1 ? $get_doctor_details[0]['user_email'] : ""),
                        'patient_email' => ($patient_user_detail['user_email_verified'] == 1 ? $patient_user_detail['user_email'] : ""),
                        'patient_phone_number' => $patient_user_detail['user_phone_number'],
                        'patient_user_id' => $patient_user_detail['user_id'],
                        'appointment_date' => $date,
                        'address' => $doctor_address,
                        'appointment_time' => date('h:i A', $start_timestamp),
                        'start_timestamp' => $start_timestamp,
                        'end_timestamp' => $end_timestamp,
                        'appointment_id' => $is_booked,
                        'clinic_name' => $clinic_name,
                        'clinic_id' => $clinic_id,
                        'appointment_type' => $appointment_type,
                        'user_type' => $user_type,
                        'email_action' => 'book'
                    );

                    $cron_job_path = APP_CRON_PATH . " notification/notify_doctor/" . base64_encode(json_encode($send_doctor_data));
                    exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");
                    
                    //insert into the track report
                    $insert_track_array = array(
                        'patient_report_track_appointment_id' => $is_booked,
                        'patient_report_track_created_at' => $this->utc_time_formated
                    );
                    $this->Common_model->insert(TBL_PATIENT_REPORT_TRACK, $insert_track_array);
                    // echo $cron_job_path;die;
                    $this->session->set_flashdata('message', 'Appointment booked successfully');
                    redirect(site_url('patient/appointment_list'));
                } else {
                    $view_data['errors'] = lang('failure');
                }
            } else {
                $view_data['errors'] = lang('doctor_not_available');
            }
        }
        $view_data['breadcrumbs'] = "Book Now";
        $view_data['page_title'] = "Book Now";
        $view_data['doctor_details'] = $this->patient->doctor_details($doctor_id,$clinic_id);
        $global_settings = $this->Common_model->get_single_row('me_global_settings','global_setting_value', ['global_setting_key'=> 'minimum_minutes_video_call_invitation']);
        $minimum_minutes = $global_settings['global_setting_value'];
        $is_booked_video_appointment = true;
        if(empty($view_data['doctor_details']->setting_data) || ($minimum_minutes > ($view_data['doctor_details']->setting_data/60))) {
            $is_booked_video_appointment = false;
        }
        $view_data['is_booked_video_appointment'] = $is_booked_video_appointment;
        // echo "<pre>";
        // print_r($view_data['doctor_details']);die;
        $this->load->view('patient/appointment_book_now', $view_data);
    }

    public function appointment_delete($id) {
        $appointment_id = encrypt_decrypt($id,'decrypt');
        $patient_id = $this->patient_auth->get_user_id();
        //get the appointment details
        $appointment_data = array(
            'appointment_id' => $appointment_id,
            'appointment_user_id' => $patient_id
        );
        $get_appointment_detail = $this->patient->get_appointment_detail($appointment_id);
        if (!empty($get_appointment_detail)) {
            $appointment_time = (strtotime($get_appointment_detail['appointment_date'] . ' ' . $get_appointment_detail['appointment_from_time']) - 60);
            $current_timestamp = strtotime(get_display_date_time("Y-m-d H:i:s"));
            if ($appointment_time < $current_timestamp) {
                $this->session->set_flashdata('errors', lang('not_able_to_cancel'));
                redirect(site_url('patient/appointment_list'));
            }
            $date = $get_appointment_detail['appointment_date'];
            $start_time = $get_appointment_detail['appointment_from_time'];
            $doctor_id = $get_appointment_detail['appointment_doctor_user_id'];
            $clinic_id = $get_appointment_detail['appointment_clinic_id'];
            $start_timestamp = strtotime($date . " " . $start_time);
            //if appointment taken then not be reschedule again
            $patient_detail_params = array(
                'appointment_id' => $appointment_id,
                'clinic_id' => $clinic_id,
                'doctor_id' => $doctor_id,
                'date' => $date,
                'patient_id' => $patient_id,
                'user_type' => $user_type
            );
            $update_array = array(
                "appointment_updated_at" => $this->utc_time_formated,
                "appointment_status" => 9
            );
            $where = array(
                "appointment_id" => $appointment_id
            );
            $is_deleted =$this->Common_model->update(TBL_APPOINTMENTS, $update_array, $where);
            if ($is_deleted > 0) {
                //delete the reminder if exists
                $update_where = array(
                    'reminder_appointment_id' => $appointment_id
                );
                $update_data = array(
                    'reminder_modified_at' => $this->utc_time_formated,
                    'reminder_status' => 9
                );
                $this->Common_model->update(TBL_REMINDERS, $update_data, $update_where);
                $doctor_name = '';
                $doctor_address = '';
                if (!empty($doctor_id)) {
                    $get_doctor_details = $this->patient->doctor_detail($doctor_id, $clinic_id, 1);
                    if (!empty($get_doctor_details[0]) && !empty($get_doctor_details[0]['user_first_name'])) {
                        $doctor_name = DOCTOR . $get_doctor_details[0]['user_first_name'] . " " . $get_doctor_details[0]['user_last_name'];
                    }

                    if (!empty($get_doctor_details[0]) && !empty($get_doctor_details[0]['address_name'])) {
                        $doctor_address = $get_doctor_details[0]['address_name'] . ', ' . $get_doctor_details[0]['city_name'] . ', ' . $get_doctor_details[0]['state_name'] . ', ' . $get_doctor_details[0]['country_name'];
                    }

                    $doctor_fees = !empty($get_doctor_details[0]['doctor_clinic_mapping_fees']) ? $get_doctor_details[0]['doctor_clinic_mapping_fees'] : '';
                    $clinic_name = !empty($get_doctor_details[0]['clinic_name']) ? $get_doctor_details[0]['clinic_name'] : '';
                }
                $patient_user_detail = $this->patient->get_details_by_id($patient_id);
                $patient_user_name = '';
                if (!empty($patient_user_detail) && !empty($patient_user_detail['user_first_name'])) {
                    $patient_user_name = $patient_user_detail['user_first_name'] . ' ' . $patient_user_detail['user_last_name'];
                }
                $notification_array = array();
                $appointment_time = date(DATE_FORMAT, strtotime($date)) . ' ' . date('h:i A', $start_timestamp);
                $send_message = sprintf(lang('appointment_cancel'), $patient_user_name, $doctor_name, $clinic_name, $appointment_time);
                //send notification to user
                $notification_array[] = array(
                    'notification_list_user_id' => $patient_id,
                    'notification_list_user_type' => 1,
                    'notification_list_type' => 2,
                    'notification_list_message' => $send_message,
                    'notification_list_created_at' => $this->utc_time_formated
                );
                $columns = 'u.user_id';
                $parent_members = $this->patient->get_linked_family_members($patient_id, $columns);
                foreach ($parent_members as $parent_member) {
                    $notification_array[] = array(
                        'notification_list_user_id' => $parent_member->user_id,
                        'notification_list_user_type' => 1,
                        'notification_list_type' => 2,
                        'notification_list_message' => $send_message,
                        'notification_list_created_at' => $this->utc_time_formated
                    );
                }
                //send notification to the doctor
                $notification_array[] = array(
                    'notification_list_user_id' => $doctor_id,
                    'notification_list_user_type' => 2,
                    'notification_list_type' => 2,
                    'notification_list_message' => $send_message,
                    'notification_list_created_at' => $this->utc_time_formated
                );
                $this->Common_model->insert_multiple(TBL_NOTIFICATION, $notification_array);
                $send_notification_data = array(
                    'notification_list_type' => 2,
                    'action' => 'cancel'
                );
                $cron_job_path = APP_CRON_PATH . " notification/send_notification/" . base64_encode(json_encode($send_notification_data));
                exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");
                //send sms and email to the doctor based on the status
                $send_doctor_data = array(
                    'doctor_id' => $doctor_id,
                    'patient_name' => $patient_user_name,
                    'doctor_name' => $doctor_name,
                    'doctor_phone_number' => $get_doctor_details[0]['user_phone_number'],
                    'doctor_email' => ($get_doctor_details[0]['user_email_verified'] == 1 ? $get_doctor_details[0]['user_email'] : ""),
                    'patient_email' => ($patient_user_detail['user_email_verified'] == 1 ? $patient_user_detail['user_email'] : ""),
                    'patient_phone_number' => $patient_user_detail['user_phone_number'],
                    'patient_user_id' => $patient_user_detail['user_id'],
                    'appointment_date' => $date,
                    'address' => $doctor_address,
                    'appointment_time' => date('h:i A', $start_timestamp),
                    'appointment_id' => $appointment_id,
                    'clinic_name' => $clinic_name,
                    'clinic_id' => $clinic_id,
                    'user_type' => $user_type,
                    'email_action' => 'cancel'
                );
                $cron_job_path = APP_CRON_PATH . " notification/notify_doctor/" . base64_encode(json_encode($send_doctor_data));
                exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");
                $this->session->set_flashdata('message', lang('appointment_cancel_message'));
                redirect(site_url('patient/appointment_list'));
            } else {
                $this->session->set_flashdata('errors', lang('failure'));
                redirect(site_url('patient/appointment_list'));
            }
        } else {
            $this->session->set_flashdata('errors', lang('failure'));
            redirect(site_url('patient/appointment_list'));
        }
    }

    public function date_validate($date) {
        $date_arr = explode('/', $date);
        if(strlen(set_value('date')) == 10 && count($date_arr) == 3) {
            return true;
        } else {
            $this->form_validation->set_message('email_exist', 'The {field} is invalid');
            return false;
        }
    }

    public function get_availability() {
        $this->form_validation->set_rules('doctor_id', 'doctor id', 'required|trim');
        $this->form_validation->set_rules('clinic_id', 'clinic id', 'required|trim');
        $this->form_validation->set_rules('appointment_type', 'appointment type', 'required|trim');
        $this->form_validation->set_rules('date', 'date', 'required|trim');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $response['errors'] = $errors;
            $response['status'] = false;
        } else {
            $date_arr = explode('/', set_value('date'));
            if(strlen(set_value('date')) == 10 && count($date_arr) == 3) {
                $date = $date_arr[2] . '-' . $date_arr[1] . '-' . $date_arr[0];
                $request_data = array(
                    "doctor_id" => set_value('doctor_id'),
                    "clinic_id" => set_value('clinic_id'),
                    "date" => $date,
                    "appointment_type" => set_value('appointment_type')
                );
                $timeslots_data = $this->patient->get_doctor_availibility($request_data);
                $final_time_slot_array = array();
                if (!empty($timeslots_data)) {
                    /* check doctor availibity for that time */
                    $doctor_data = $this->patient->get_availibity($request_data);
                    /* check doctor block calender for that time */
                    $doctor_block_data = $this->patient->check_block_calender($request_data);
                    /* For first time slots */
                    $timeslots_data['start_session'] = $timeslots_data['doctor_availability_session_1_start_time'];
                    $timeslots_data['end_session'] = $timeslots_data['doctor_availability_session_1_end_time'];
                    $first_time_slot_array = $this->get_timeslot_availibity($timeslots_data, $doctor_data, $doctor_block_data);
                    /* For second time slots */
                    if (!empty($timeslots_data['doctor_availability_session_2_start_time'])) {
                        $timeslots_data['start_session'] = $timeslots_data['doctor_availability_session_2_start_time'];
                        $timeslots_data['end_session'] = $timeslots_data['doctor_availability_session_2_end_time'];
                        $second_time_slot_array = $this->get_timeslot_availibity($timeslots_data, $doctor_data, $doctor_block_data);
                    } else {
                        $second_time_slot_array = array();
                    }
                    $final_time_slot_array = array_merge($first_time_slot_array, $second_time_slot_array);
                }
                $time = get_display_date_time("Y-m-d H:i");
                foreach ($final_time_slot_array as $key => $value) {
                    $final_time_slot_array[$key]['slot_time'] = date('h:i A', strtotime($value['start_time'])) . ' - ' . date('h:i A', strtotime($value['end_time']));
                    $start_time = $date . ' ' . $value['start_time'];

                    if(!$value['is_available'] || ($value['is_available'] && strtotime($time) > strtotime($start_time))) {
                        unset($final_time_slot_array[$key]);
                    }
                }
                $response['data'] = array_values($final_time_slot_array);
                $response['status'] = true;   
            } else {
                $response['errors'] = ['date' => 'Invalid date'];
                $response['status'] = false;
            } 
        }
        echo json_encode($response);
        exit;
    }

    public function get_timeslot_availibity($timeslots_data, $doctor_data, $doctor_block_data) {
        $final_time_slot_array = array();
        /* For first time slots */
        $availibility_id = $timeslots_data['doctor_availability_id'];
        for ($i = $timeslots_data['start_session']; strtotime($i) < strtotime($timeslots_data['end_session']);
        ) {
            $time = strtotime($i);
            $new_duration_time = date("H:i", strtotime($timeslots_data['doctor_clinic_mapping_duration'] . ' minutes', $time));
            $start_time = date("H:i", $time);
            $is_available = true;
            foreach ($doctor_data as $doctor_availability) {
                $already_appoint_start_date = date('H:i', strtotime($doctor_availability['appointment_from_time']));
                $already_appoint_end_date = date('H:i', strtotime($doctor_availability['appointment_to_time']));
                if (
                        ($already_appoint_start_date >= $start_time && $already_appoint_start_date < $new_duration_time) ||
                        ($already_appoint_end_date > $start_time && $already_appoint_end_date < $new_duration_time)
                ) {
                    $is_available = false;
                    break;
                }
                if (
                        ($already_appoint_start_date <= $start_time && $start_time < $already_appoint_end_date) ||
                        ($already_appoint_start_date < $new_duration_time && $new_duration_time < $already_appoint_end_date)
                ) {
                    $is_available = false;
                    break;
                }
            }
            /* check block data */
            if ($is_available) {
                foreach ($doctor_block_data as $block_data) {
                    $new_block_start_date = date("H:i", strtotime($block_data['calender_block_start_time']));
                    $new_block_end_date = date("H:i", strtotime($block_data['calender_block_end_time']));
                    if ($block_data['calender_block_duration_type'] == 1) {
                        $is_available = FALSE;
                        break;
                    }
                    if (
                            ($start_time >= $new_block_start_date && $start_time < $new_block_end_date) ||
                            ($new_duration_time > $new_block_start_date && $new_duration_time <= $new_block_end_date) ||
                            ($new_block_start_date >= $start_time && $new_block_start_date < $new_duration_time) ||
                            ($new_block_end_date > $start_time && $new_block_end_date <= $new_duration_time)
                    ) {
                        $is_available = false;
                        break;
                    }
                }
            }
            $i = date("Y-m-d H:i:s", strtotime($timeslots_data['doctor_clinic_mapping_duration'] . ' minutes', $time));
            if (strtotime($i) > strtotime($timeslots_data['end_session'])) {
                break;
            }
            $final_time_slot_array[] = array(
                "start_time" => $start_time,
                "end_time" => $new_duration_time,
                'is_available' => $is_available,
                'doctor_availability_id' => $availibility_id
            );
        }
        return $final_time_slot_array;
    }

    public function prescription($id) {
        $appointment_id = encrypt_decrypt($id,'decrypt');
        $appointment_whare = [
            'appointment_id' => $appointment_id
        ];
        $patient_appointment = $this->Common_model->check_paient_appointment($appointment_whare);
        if(!empty($patient_appointment['share_status_json'])) {
            $share_status = json_decode($patient_appointment['share_status_json']);
        } else {
            $share_status = [];
        }
        // print_r($share_status);die;
            $get_patient_column = "user_first_name,
                               user_last_name,
                               user_email,
                               user_phone_number, 
                               user_gender,
                               user_unique_id,
                               user_patient_id,
                               user_details_dob ";
        $left_join = array(
            TBL_USER_DETAILS => 'user_id = user_details_user_id'
        );
        $patient_id = $patient_appointment['appointment_user_id'];
        $get_patient_data = $this->Common_model->get_single_row(TBL_USERS, $get_patient_column, array("user_id" => $patient_id), $left_join);

        $view_data = array(
            "doctor_data" => $patient_appointment,
            "patient_data" => $get_patient_data,
            "vitalsign_data" => array(),
            "clinicnote_data" => array(),
            "prescription_data" => array(),
            "patient_lab_orders_data" => array(),
            "files_data" => array()
        );
        if(!empty($share_status->language_id)){
            $languages = $this->Common_model->get_single_row('me_languages', 'LOWER(language_code) AS language_code', ['language_id' => $share_status->language_id]);
            $view_data['language_id'] = $share_status->language_id;
        } else {
            $view_data['language_id'] = 1;
        }

        if(!empty($languages) && !empty($languages['language_code'])) {
            $language_code = $languages['language_code'];
            if(!empty($language_code) && $language_code == 'hi')
                $language_code = 'hn';
            $view_data['language_code'] = $language_code;        
        } else {
            $view_data['language_code'] = 'en';
        }
        if(empty($share_status) || (!empty($share_status->vital) && $share_status->vital == 1)) {
            $get_vitalsign_data_sql = "
                SELECT 
                    vital_report_spo2,
                    vital_report_weight,
                    vital_report_bloodpressure_systolic,
                    vital_report_bloodpressure_diastolic,
                    vital_report_bloodpressure_type,
                    vital_report_pulse,
                    vital_report_temperature,
                    vital_report_temperature_type,
                    vital_report_temperature_taken,
                    vital_report_resp_rate
                FROM " . TBL_VITAL_REPORTS . "
                WHERE 
                    vital_report_appointment_id='" . $appointment_id . "' AND 
                    vital_report_status=1
            ";
            if(empty($share_status)) {
                $get_vitalsign_data_sql .= " AND vital_report_share_status = 1";
            }
            $get_vitalsign_data = $this->Common_model->get_single_row_by_query($get_vitalsign_data_sql);
            $view_data['vitalsign_data'] = $get_vitalsign_data;
        } else {
            $view_data['vitalsign_data'] = [];
        }
        if(empty($share_status) || ((!empty($share_status->clinical_note) && $share_status->clinical_note == 1) || (!empty($share_status->only_diagnosis) && $share_status->only_diagnosis == 1))) {
            $get_clinicnote_data_sql = "
                SELECT 
                    clinical_notes_reports_kco,
                    clinical_notes_reports_complaints,
                    clinical_notes_reports_observation,
                    clinical_notes_reports_diagnoses,
                    clinical_notes_reports_add_notes
                FROM " . TBL_CLINICAL_REPORTS . "
                WHERE 
                    clinical_notes_reports_appointment_id='" . $appointment_id . "' AND 
                    clinical_notes_reports_status=1
            ";
            if(empty($share_status)) {
                $get_clinicnote_data_sql .= " AND clinical_notes_reports_share_status = 1";
            }
            $get_clinicnote_data = $this->Common_model->get_single_row_by_query($get_clinicnote_data_sql);
            $view_data['clinicnote_data'] = $get_clinicnote_data;
        } else {
            $view_data['clinicnote_data'] = [];
        }
        $view_data['with_clinicnote'] = (!empty($share_status->clinical_note) && $share_status->clinical_note == 1) ? 'true' : '';
        $view_data['with_only_diagnosis'] = (!empty($share_status->only_diagnosis) && $share_status->only_diagnosis == 1) ? 'true' : '';
        $view_data['with_generic'] = (empty($share_status) || (!empty($share_status->generic) && $share_status->generic == 1)) ? 'true' : '';
        
        if(empty($share_status) || (!empty($share_status->prescriptions) && $share_status->prescriptions == 1)) {
            $pres_share_status = "";
            if(empty($share_status)) {
                $pres_share_status = " AND prescription_share_status = 1 ";
            }
            $patient_prescription_sql = "
                SELECT 
                    prescription_drug_name, 
                    drug_frequency_name, 
                    prescription_duration, 
                    prescription_duration_value, 
                    prescription_intake, 
                    prescription_dosage,
                    prescription_frequency_id,
                    drug_unit_is_qty_calculate,
                    drug_unit_medicine_type,
                    drug_unit_name,
                    GROUP_CONCAT(drug_generic_title) as drug_generic_title,
                    prescription_intake_instruction,
                    prescription_frequency_instruction,
                    prescription_frequency_instruction_json,
                    prescription_intake_instruction_json,
                    follow_up_followup_date,
                    follow_up_instruction,
                    prescription_frequency_value as freq
                FROM 
                    " . TBL_PRESCRIPTION_REPORTS . " 
                LEFT JOIN
                    ".TBL_PRESCRIPTION_FOLLOUP." ON prescription_appointment_id = follow_up_appointment_id
                LEFT JOIN 
                    " . TBL_DRUG_FREQUENCY . " ON prescription_frequency_id=drug_frequency_id 
                LEFT JOIN 
                    " . TBL_DRUG_UNIT . " ON prescription_unit_id = drug_unit_id
                LEFT JOIN 
                    " . TBL_DRUG_GENERIC . " ON FIND_IN_SET(drug_generic_id, prescription_generic_id)  AND  drug_generic_status = 1
                WHERE 
                    prescription_appointment_id='" . $appointment_id . "' AND 
                    prescription_status=1 ".$pres_share_status."
                GROUP BY 
                    prescription_id
            ";
            // echo $patient_prescription_sql;exit;
            $patient_prescription = $this->Common_model->get_all_rows_by_query($patient_prescription_sql);
            $view_data['prescription_data'] = $patient_prescription;
        } else {
            $view_data['prescription_data'] = [];
        }
        
        if(empty($share_status) || (!empty($share_status->investigations) && $share_status->investigations == 1)) {
            $get_lab_orders_sql = "
                SELECT 
                    lab_report_test_name
                FROM " . TBL_LAB_REPORTS . "
                WHERE 
                    lab_report_appointment_id='" . $appointment_id . "' AND 
                    lab_report_status=1
            ";
            if(empty($share_status)) {
                $get_lab_orders_sql .= " AND lab_report_share_status=1";
            }
            $get_lab_orders = $this->Common_model->get_single_row_by_query($get_lab_orders_sql);
            $view_data['patient_lab_orders_data'] = $get_lab_orders;
        } else {
            $view_data['patient_lab_orders_data'] = [];
        }
        if(empty($share_status) || (!empty($share_status->procedures) && $share_status->procedures == 1)) {
            $get_procedure_sql = "
                                    SELECT
                                        procedure_report_procedure_text,
                                        procedure_report_note
                                    FROM
                                        " . TBL_PROCEDURE_REPORTS . "
                                    WHERE
                                        procedure_report_appointment_id = '" . $appointment_id . "' AND
                                        procedure_report_status = 1 
                ";
            if(empty($share_status)) {
                $get_procedure_sql .= " AND procedure_report_share_status=1 ";
            }
            $get_procedure_report = $this->Common_model->get_single_row_by_query($get_procedure_sql);
            $view_data['procedure_data'] = $get_procedure_report;
        } else {
            $view_data['procedure_data'] = [];
        }
        $view_data['billing_data'] = array();
        $with_teleCunsultation = (!empty($patient_appointment['appointment_type']) && in_array($patient_appointment['appointment_type'], [4,5])) ? true : false;
        if($with_teleCunsultation) {
            $view_data['teleConsultationMsg'] = 'The prescription is given on telephonic consultation.';
        }
        $uas7_assign = $this->Common_model->get_single_row('me_patient_analytics','patient_analytics_id', ['patient_analytics_analytics_id' => 308, 'patient_analytics_user_id' => $patient_id, 'patient_analytics_status' => 1]);
        if(!empty($uas7_assign)) {
            $share_link_row = $this->Common_model->get_single_row('me_patient_share_link_log', 'id,unique_code,doctor_id', ['patient_id' => $patient_id]);
            $reset_token = str_rand_access_token(20);
            if(empty($share_link_row['id'])) {
                $share_link_data = array(
                    'patient_id' => $patient_id,
                    'doctor_id' => $doctor_id,
                    'share_clinic_id' => $patient_appointment['appointment_clinic_id'],
                    'unique_code' => $reset_token,
                    'is_set_password' => 0,
                    'created_at' => date("Y-m-d H:i:s")
                );
                $this->Common_model->insert('me_patient_share_link_log', $share_link_data);
            } else {
                if($share_link_row['doctor_id'] == $doctor_id) {
                    $reset_token = $share_link_row['unique_code'];
                } else {
                    $share_link_data = array(
                        'doctor_id' => $doctor_id,
                        'share_clinic_id' => $patient_appointment['appointment_clinic_id'],
                        'unique_code' => $reset_token,
                        'status' => 1,
                        'updated_at' => date("Y-m-d H:i:s")
                    );
                    $this->Common_model->update('me_patient_share_link_log', $share_link_data, array('id' => $share_link_row['id']));
                }
            }
            $view_data['patient_share_link'] = DOMAIN_URL . 'pt/'. $reset_token.'_uas7';
        }
        $view_data['with_signature'] = 'true';
        $patient_link_enable = $this->Common_model->get_single_row('me_global_settings','global_setting_value', ['global_setting_key'=> 'patient_link_enable']);
        if(!empty($share_status->patient_tools) && $share_status->patient_tools == 1) {
            $patient_tool_document = $this->Common_model->get_all_rows('me_patient_documents_shared','id', ['appointment_id'=> $appointment_id]);
            $report_columns = "
                                file_report_name,
                                file_report_image_url";

            $get_report_query = "SELECT
                                        " . $report_columns . " 
                                    FROM 
                                        me_files_reports 
                                    LEFT JOIN
                                         me_files_reports_images ON file_report_image_file_report_id = file_report_id AND file_report_image_status=1 
                                    WHERE
                                        file_report_appointment_id = '" . $appointment_id . "' 
                                    AND 
                                        file_report_status = 1
                                    AND 
                                        file_report_report_type_id = 11";
            $view_data['reports'] = $this->Common_model->get_all_rows_by_query($get_report_query);
        } else {
            $view_data['reports'] = [];
            $patient_tool_document = [];
        }
        $view_data['patient_tool_document'] = $patient_tool_document;
        $view_html = $this->load->view("prints/charting", $view_data, true);
        $view_data['doctor_data']['doctor_detail_speciality'] = str_replace(',', ', ', $view_data['doctor_data']['doctor_detail_speciality']);
        $view_data['doctor_data']['doctor_qualification'] = str_replace(',', ', ', $view_data['doctor_data']['doctor_qualification']);
        $doctor_data = $view_data['doctor_data'];
         $address_data = [
            'address_name' => $doctor_data['address_name'],
            'address_name_one' => $doctor_data['address_name_one'],
            'address_locality' => $doctor_data['address_locality'],
            'city_name' => $doctor_data['city_name'],
            'state_name' => $doctor_data['state_name'],
            'address_pincode' => $doctor_data['address_pincode']
        ];
        // require_once MPDF_PATH;
        // $lang_code = 'en-GB';
        // $mpdf = new MPDF(
        //         $lang_code, // mode - default '' //sd
        //         'A4', // format - A4, for example, default ''
        //         0, // font size - default 0
        //         'arial', // default font family
        //         8, // margin_left
        //         8, // margin right
        //         $header_margin, // margin top
        //         8, // margin bottom
        //         8, // margin header
        //         5, // margin footer
        //         'P'   // L - landscape, P - portrait
        // );

        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];
        $mpdf = new \Mpdf\Mpdf([
                'tempDir' => DOCROOT_PATH . 'uploads',
                'fontDir' => array_merge($fontDirs, [
                DOCROOT_PATH.'assets/fonts'
            ]),
            'fontdata' => $fontData + [
                'gotham_book' => [
                    'R' => 'Gotham-Book.ttf',
                    'I' => 'Gotham-Book.ttf',
                ],
            ],
            'default_font' => 'gotham_book',
            'mode' => 'en-GB',
            'format' => 'A4',
            'margin_top' => 0,
            'margin_bottom' => 8,
            'margin_left' => 8,
            'margin_right' => 8,
            'margin_header' => 8,
            'margin_footer' => 5
        ]);
        
        $mpdf->useOnlyCoreFonts = true;
        $mpdf->setAutoTopMargin = true;
        $mpdf->SetDisplayMode('real');
        $mpdf->list_indent_first_level = 0;
        $mpdf->setAutoBottomMargin = 'stretch';
        $date_and_time = date('m/d/Y h:i:s a', time());
        $patient_name = $view_data['patient_data']['user_first_name'];
        $mpdf->SetTitle('Rx_'.$patient_name.'_'.$date_and_time);
        $mpdf->SetHTMLHeader('
            <table style="width:100%;border-bottom:1px solid #000">
                <tr>
                    <td width="50%" style="text-align:left;vertical-align:top">
                     ' . DOCTOR. " ".$doctor_data['user_first_name'] . " " . $doctor_data['user_last_name'] . "<br>" . '
                     ' . 'Reg. No. '.$doctor_data['doctor_regno'] . "<br>" . '
                     ' . $doctor_data['doctor_detail_speciality'] . "<br>" . '
                     ' . $doctor_data['doctor_qualification'] . "<br>" . '    
                    </td>
                    <td width="50%" style="text-align:right;vertical-align:top">
                        ' . $doctor_data['clinic_name'] . "<br>" . '
                        ' . clinic_address($address_data) . "<br>" . '
                        ' . $doctor_data['clinic_contact_number'] . ", " . '
                        ' . $doctor_data['clinic_email'] . "<br>" . '
                    </td>
                </tr>
            </table>
        ');
        $patient_link_data = "";
        if(!empty($patient_link_enable) && $patient_link_enable['global_setting_value'] == "1"){
            $patient_link_data = '<tr>
                <td align="center" colspan="3" width="100%" style="font-size:10px">
                    <b>Please Visit MedSign Patient: </b> <a target="_blank" href="'.DOMAIN_URL . 'patient">' . DOMAIN_URL . 'patient</a>
                </td>
                </tr>';
        }
        $mpdf->SetHTMLFooter('
            <table width="100%">
                ' . $patient_link_data . '
                <tr>
                    <td width="33%" style="font-size:10px">
                        Generated On: {DATE d/m/Y}
                    </td>
                    <td width="33%" style="font-size:10px" align="center">Page No. {PAGENO}/{nbpg}</td>
                    <td width="33%" align="right" style="font-size:10px">Powerd by Medsign</td>
                </tr>
            </table>
        ');
        $fileName = 'Rx_'.$patient_name.'_'.$date_and_time;
        $mpdf->WriteHTML($view_html);
        $mpdf->Output($fileName.'.pdf', 'I');
    }

}