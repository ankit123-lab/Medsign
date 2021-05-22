<?php
use Razorpay\Api\Api;
class Communications extends MY_Controller {
    private $api_key = '';
    private $api_secret = '';
    public function __construct() {
		parent::__construct();
        $this->api_key = $GLOBALS['ENV_VARS']['RAZORPAY_KEY'];
        $this->api_secret = $GLOBALS['ENV_VARS']['RAZORPAY_SECRET'];
        $this->load->model("Communication_model", "communication");
        $this->load->model("User_model");
    }

    public function get_communication_date_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $page = !empty($this->Common_model->escape_data($this->post_data['page'])) ? trim($this->Common_model->escape_data($this->post_data['page'])) : '1';
            $per_page = !empty($this->Common_model->escape_data($this->post_data['per_page'])) ? trim($this->Common_model->escape_data($this->post_data['per_page'])) : '10';
            if(empty($doctor_id)
            ) {
                $this->bad_request();
                exit;
            }
            $where['doctor_id'] = $doctor_id;
            $where['page'] = $page;
            $where['per_page'] = $per_page;
            $result = $this->communication->get_communication_date($where);
            $count = $this->communication->get_communication_date($where, true);
            $where_array = array(
                'setting_user_id' => $doctor_id,
                'setting_type' => 9,
                'setting_status' => 1
            );
            $get_setting_data = $this->Common_model->get_single_row(TBL_SETTING, 'setting_data', $where_array);
            $get_global_setting_data = $this->communication->get_global_setting_by_key_arr(['sms_credit_calculation','gst_percentage','per_sms_price']);
            $global_setting_data = array_column($get_global_setting_data, 'global_setting_value', 'global_setting_key');
            $this->my_response['available_sms_credit'] = !empty($get_setting_data) ? $get_setting_data['setting_data'] : 0;
            $this->my_response['per_sms_price'] = $global_setting_data['per_sms_price'];
            $this->my_response['gst_percent'] = json_decode($global_setting_data['gst_percentage']);
            $this->my_response['sms_credit_calculation'] = json_decode($global_setting_data['sms_credit_calculation']);
            $clinic_details = $this->communication->get_doctor_clinic_detail($doctor_id);
            $global_setting = $this->communication->get_global_setting_by_key('gst_registration_state_id');
            $is_apply_igst = true;
            if(!empty($clinic_details->address_state_id) && $clinic_details->address_state_id == $global_setting->global_setting_value) {
                $is_apply_igst = false;
            }
            $this->my_response['is_apply_igst'] = $is_apply_igst;
            if(!empty($result)) {
                $this->my_response['status'] = true;
                $this->my_response['count'] = $count;
                $this->my_response['data'] = $result;
                $this->my_response['message'] = lang('common_detail_found');
            } else {
                $this->my_response['status'] = false;
                $this->my_response['data'] = [];
                $this->my_response['message'] = lang('common_detail_not_found');
            }
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_sms_template_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $page = !empty($this->Common_model->escape_data($this->post_data['page'])) ? trim($this->Common_model->escape_data($this->post_data['page'])) : '1';
            $per_page = !empty($this->Common_model->escape_data($this->post_data['per_page'])) ? trim($this->Common_model->escape_data($this->post_data['per_page'])) : '10';
            if(empty($doctor_id)
            ) {
                $this->bad_request();
                exit;
            }
            $where['doctor_id'] = $doctor_id;
            // $where['page'] = $page;
            // $where['per_page'] = $per_page;
            $result = $this->communication->get_sms_template($where);
            // $count = $this->communication->get_communication_date($where, true);
            if(!empty($result)) {
                $this->my_response['status'] = true;
                // $this->my_response['count'] = $count;
                $this->my_response['data'] = $result;
                $this->my_response['message'] = lang('common_detail_found');
            } else {
                $this->my_response['status'] = false;
                $this->my_response['data'] = [];
                $this->my_response['message'] = lang('common_detail_not_found');
            }
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_communication_list_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $date = !empty($this->post_data['date']) ? trim($this->Common_model->escape_data($this->post_data['date'])) : '';
            $page = !empty($this->Common_model->escape_data($this->post_data['page'])) ? trim($this->Common_model->escape_data($this->post_data['page'])) : '1';
            $per_page = !empty($this->Common_model->escape_data($this->post_data['per_page'])) ? trim($this->Common_model->escape_data($this->post_data['per_page'])) : '10';

            if(empty($doctor_id) 
                || empty($date) 
            ) {
                $this->bad_request();
                exit;
            }
            $where['doctor_id'] = $doctor_id;
            $where['date'] = $date;
            $where['page'] = $page;
            $where['per_page'] = $per_page;
            $result = $this->communication->get_communication_list($where);
            $count = $this->communication->get_communication_list($where, true);
            if(!empty($result)) {
                $this->my_response['status'] = true;
                $this->my_response['count'] = $count;
                $this->my_response['data'] = $result;
                $this->my_response['message'] = lang('common_detail_found');
            } else {
                $this->my_response['status'] = false;
                $this->my_response['data'] = [];
                $this->my_response['message'] = lang('common_detail_not_found');
            }
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_patients_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $search_str = !empty($this->post_data['search_str']) ? trim($this->Common_model->escape_data($this->post_data['search_str'])) : '';
            $page = !empty($this->Common_model->escape_data($this->post_data['page'])) ? trim($this->Common_model->escape_data($this->post_data['page'])) : '1';
            $per_page = !empty($this->Common_model->escape_data($this->post_data['per_page'])) ? trim($this->Common_model->escape_data($this->post_data['per_page'])) : '10';
            if(empty($doctor_id)
            ) {
                $this->bad_request();
                exit;
            }
            $rece = $this->User_model->get_doctor_receptionist($doctor_id, 'user_id');
            $user_source_id_arr = array_column($rece, 'user_id');
            array_push($user_source_id_arr, $doctor_id);
            $where['doctor_id'] = $doctor_id;
            $where['user_source_id_arr'] = $user_source_id_arr;
            $where['search_str'] = $search_str;
            $where['page'] = $page;
            $where['per_page'] = $per_page;
            $result = $this->communication->get_patients($where);
            $count = $this->communication->get_patients($where, true);
            if(!empty($result)) {
                $this->my_response['count'] = $count;
                $this->my_response['status'] = true;
                $this->my_response['data'] = $result;
                $this->my_response['message'] = lang('common_detail_found');
            } else {
                $this->my_response['status'] = false;
                $this->my_response['data'] = [];
                $this->my_response['message'] = lang('common_detail_not_found');
            }
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_commu_patient_groups_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $search_str = !empty($this->post_data['search_str']) ? trim($this->Common_model->escape_data($this->post_data['search_str'])) : '';
            $page = !empty($this->Common_model->escape_data($this->post_data['page'])) ? trim($this->Common_model->escape_data($this->post_data['page'])) : '1';
            $per_page = !empty($this->Common_model->escape_data($this->post_data['per_page'])) ? trim($this->Common_model->escape_data($this->post_data['per_page'])) : '10';
            if(empty($doctor_id)
            ) {
                $this->bad_request();
                exit;
            }
            $where = [];
            $where['doctor_id'] = $doctor_id;
            $where['search_str'] = $search_str;
            $where['page'] = $page;
            $where['per_page'] = $per_page;
            $rece = $this->User_model->get_doctor_receptionist($doctor_id, 'user_id');
            $user_source_id_arr = array_column($rece, 'user_id');
            array_push($user_source_id_arr, $doctor_id);
            $where['user_source_id_arr'] = $user_source_id_arr;
            $result = $this->communication->get_commu_patient_groups($where);
            $patient_group_ids = array_column($result, 'patient_group_id');
            $members = [];
            if(!empty($patient_group_ids))
                $members = $this->communication->get_patient_group_members($patient_group_ids);
            $group_members = [];
            foreach ($members as $key => $value) {
                $group_members[$value->patient_group_id][] = $value;
            }
            $count = $this->communication->get_commu_patient_groups($where, true);
            if(!empty($result)) {
                $this->my_response['count'] = $count;
                $this->my_response['status'] = true;
                $this->my_response['data'] = $result;
                $this->my_response['group_members'] = $group_members;
                $this->my_response['message'] = lang('common_detail_found');
            } else {
                $this->my_response['status'] = false;
                $this->my_response['data'] = [];
                $this->my_response['message'] = lang('common_detail_not_found');
            }
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function test_template_sms_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $template_id = !empty($this->post_data['template_id']) ? trim($this->Common_model->escape_data($this->post_data['template_id'])) : '';
            $message = !empty($this->post_data['message']) ? trim($this->post_data['message']) : '';
            if(empty($doctor_id) 
                || empty($template_id) 
                || empty($message) 
            ) {
                $this->bad_request();
                exit;
            }
            $where_array = array(
                'user_id' => $doctor_id,
                'user_type' => 2,
                'user_status' => 1
            );
            $user_row = $this->Common_model->get_single_row('me_users', 'user_first_name, user_last_name, user_phone_number', $where_array);
            $templates_row = $this->Common_model->get_single_row('me_communication_sms_templates','communication_sms_template_is_promotional', ['communication_sms_template_id' => $template_id]);
            if(!empty($user_row)) {
                $send_message['phone_number'] = DEFAULT_COUNTRY_CODE . $user_row['user_phone_number'];
                $send_message['is_promotional'] = false;
                $send_message['message'] = $message;
                if(!empty($templates_row['communication_sms_template_is_promotional']) && $templates_row['communication_sms_template_is_promotional'] == 1)
                    $send_message['is_promotional'] = true;
                send_message_by_vibgyortel($send_message);
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('test_sms_send_success');
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

    public function add_communication_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $template_id = !empty($this->post_data['template_id']) ? trim($this->Common_model->escape_data($this->post_data['template_id'])) : '';
            $communication_name = !empty($this->post_data['communication_name']) ? trim($this->Common_model->escape_data($this->post_data['communication_name'])) : '';
            $communication_message = !empty($this->post_data['communication_message']) ? trim($this->post_data['communication_message']) : '';
            $communication_by = !empty($this->post_data['communication_by']) ? trim($this->Common_model->escape_data($this->post_data['communication_by'])) : '';
            $communication_type = !empty($this->post_data['communication_type']) ? trim($this->Common_model->escape_data($this->post_data['communication_type'])) : '';
            $communication_type_id = !empty($this->post_data['patient_ids']) ? $this->post_data['patient_ids'] : [];
            $group_id = !empty($this->post_data['group_ids']) ? $this->post_data['group_ids'] : [];
            $per_sms_credit_used = !empty($this->post_data['per_sms_credit_used']) ? $this->post_data['per_sms_credit_used'] : 1;
            
            if(empty($doctor_id)
                || empty($communication_message)
                || empty($communication_by)
                || empty($communication_type)
                || empty($template_id)
                || empty($communication_type_id)
            ) {
                $this->bad_request();
                exit;
            }
            if(strlen($communication_message) > 600) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('characters_limit_over');
                $this->send_response();
            }
            $where_array = array(
                'setting_user_id' => $doctor_id,
                'setting_type' => 9,
                'setting_status' => 1
            );
            $get_setting_data = $this->Common_model->get_single_row(TBL_SETTING, 'setting_id,setting_data', $where_array);
            $total_credits = !empty($get_setting_data) ? $get_setting_data['setting_data'] : 0;
            $total_credit_used = count($communication_type_id) * $per_sms_credit_used;
            if($total_credit_used > $total_credits) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('no_enough_sms_credit');
                $this->send_response();
            }
            $insert_data = array(
                'patient_communication_name' => ($communication_by == 1) ? 'SMS' : $communication_name,
                'patient_communication_doctor_id' => $doctor_id,
                'patient_communication_message' => $communication_message,
                'patient_communication_by' => $communication_by,
                'patient_communication_type' => $communication_type,
                'patient_communication_type_id' => implode(',', $communication_type_id),
                'patient_communication_group_id' => implode(',', $group_id),
                'patient_communication_created_at' => $this->utc_time_formated,
                'patient_communication_status' => 1,
                'patient_communication_template_id' => $template_id,
                'patient_communication_credit_used' => $total_credit_used
            );
            $is_insert = $this->Common_model->insert('me_patients_communication', $insert_data);
            if(!empty($is_insert)) {
                $cron_job_path = CRON_PATH . " cron/send_promotional_sms/" . $is_insert;
                exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");
                $update_setting_data = array(
                    'setting_data' => $total_credits - $total_credit_used,
                    'setting_updated_at' => $this->utc_time_formated
                );
                $update_setting_where = array(
                    'setting_id' => $get_setting_data['setting_id']
                );
                $this->Common_model->update(TBL_SETTING, $update_setting_data, $update_setting_where);
                /*if($communication_by == 1) {
                    $where_array = array(
                        'communication_sms_template_doctor_user_id' => $doctor_id,
                        'communication_sms_template_title' => $communication_message,
                        'communication_sms_template_status' => 1
                    );
                    $get_template_data = $this->Common_model->get_single_row('me_communication_sms_templates', 'communication_sms_template_id', $where_array);
                    if(empty($get_template_data)) {
                        $template_data = array(
                            'communication_sms_template_doctor_user_id' => $doctor_id,
                            'communication_sms_template_title' => $communication_message,
                            'communication_sms_template_created_at' => $this->utc_time_formated,
                        );
                        $this->Common_model->insert('me_communication_sms_templates', $template_data);
                    }
                }*/
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('patient_communications_sms');
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

    public function create_payment_credits_order_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $sub_amount = !empty($this->post_data['sub_amount']) ? trim($this->Common_model->escape_data($this->post_data['sub_amount'])) : '';
            $paid_amount = !empty($this->post_data['paid_amount']) ? trim($this->Common_model->escape_data($this->post_data['paid_amount'])) : '';
            $cgst_amount = !empty($this->post_data['cgst_amount']) ? trim($this->Common_model->escape_data($this->post_data['cgst_amount'])) : '';
            $sgst_amount = !empty($this->post_data['sgst_amount']) ? trim($this->Common_model->escape_data($this->post_data['sgst_amount'])) : '';
            $igst_amount = !empty($this->post_data['igst_amount']) ? trim($this->Common_model->escape_data($this->post_data['igst_amount'])) : '';
            $is_apply_igst = !empty($this->post_data['is_apply_igst']) ? trim($this->Common_model->escape_data($this->post_data['is_apply_igst'])) : '';
            $total_credits = !empty($this->post_data['total_credits']) ? trim($this->Common_model->escape_data($this->post_data['total_credits'])) : '';
            if (empty($doctor_id) || empty($sub_amount)
                || empty($paid_amount) 
                || empty($total_credits)
            ) {
                $this->bad_request();
                exit;
            }
            
            if(!empty($sub_amount) && $sub_amount > 0) {
                $api = new Api($this->api_key, $this->api_secret);
                $receipt_no = time();
                $order  = $api->order->create(array('receipt' => $receipt_no, 'amount' => str_replace('.', '', $paid_amount), 'currency' => 'INR'));
                if(!empty($order->id)) {
                    $get_global_setting_data = $this->communication->get_global_setting_by_key_arr(['gst_percentage']);
                    $global_setting_data = array_column($get_global_setting_data, 'global_setting_value', 'global_setting_key');
                    $gst_percentage = json_decode($global_setting_data['gst_percentage'], true);
                    $payment_data = array(
                        'user_id' => $doctor_id,
                        'sub_plan_name' => $total_credits,
                        'payment_type' => 3, //3=Online
                        'receipt_no' => $receipt_no,
                        'sub_total' => $sub_amount,
                        'tax_cgst_percent' => (!$is_apply_igst) ? $gst_percentage['cgst'] : 0.00,
                        'tax_sgst_percent' => (!$is_apply_igst) ? $gst_percentage['sgst'] : 0.00,
                        'tax_igst_percent' => ($is_apply_igst) ? $gst_percentage['igst'] : 0.00,
                        'tax_cgst_amount' => $cgst_amount,
                        'tax_sgst_amount' => $sgst_amount,
                        'tax_igst_amount' => $igst_amount,
                        'paid_amount' => $paid_amount,
                        'razorpay_order_id' => $order->id,
                        'payment_status' => 2, //created,
                        'created_at' => $this->utc_time_formated,
                        'detail_type' => 'sms'
                    );
                    $this->Common_model->insert('me_user_payment_details', $payment_data);
                    $doctor_detail = $this->communication->doctor_details($doctor_id);
                    $return['order_id'] = $order->id;
                    $return['paid_amount'] = $paid_amount;
                    $return['name'] = DOCTOR. ' ' . $doctor_detail->user_first_name . ' ' . $doctor_detail->user_last_name;
                    $return['email'] = $doctor_detail->user_email;
                    $return['contact'] = $doctor_detail->user_phone_number;
                    $return['address'] = $doctor_detail->address_name_one;
                    $return['key'] = $this->api_key;
                    $return['description'] = "";
                    $this->my_response['status'] = true;
                    $this->my_response['message'] = lang('common_detail_found');
                    $this->my_response['data'] = $return;
                } else {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('order_error');
                }
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('order_error');
            }
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function payment_credits_capture_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $paid_amount = !empty($this->post_data['paid_amount']) ? trim($this->Common_model->escape_data($this->post_data['paid_amount'])) : '';
            $payment_id = !empty($this->post_data['payment_id']) ? trim($this->Common_model->escape_data($this->post_data['payment_id'])) : '';
            $order_id = !empty($this->post_data['order_id']) ? trim($this->Common_model->escape_data($this->post_data['order_id'])) : '';
            $signature = !empty($this->post_data['signature']) ? trim($this->Common_model->escape_data($this->post_data['signature'])) : '';
            $total_buy_credits = !empty($this->post_data['total_buy_credits']) ? trim($this->Common_model->escape_data($this->post_data['total_buy_credits'])) : '';
            if (empty($doctor_id) || empty($paid_amount) || empty($total_buy_credits) || empty($payment_id) || empty($order_id) || empty($signature)) {
                $this->bad_request();
                exit;
            }
            $api = new Api($this->api_key, $this->api_secret);
            $payment  = $api->payment->fetch($payment_id)->capture(array('amount' => str_replace('.', '', $paid_amount)));
            if(!empty($payment)) {
                $where_array = array(
                    'setting_user_id' => $doctor_id,
                    'setting_type' => 9,
                    'setting_status' => 1
                );
                $get_setting_data = $this->Common_model->get_single_row(TBL_SETTING, 'setting_id, setting_data', $where_array);
                if(!empty($get_setting_data)) {
                    $update_setting_data = array(
                        'setting_data' => $get_setting_data['setting_data'] + $total_buy_credits,
                        'setting_updated_at' => $this->utc_time_formated
                    );
                    $update_setting_where = array(
                        'setting_id' => $get_setting_data['setting_id']
                    );
                    $this->Common_model->update(TBL_SETTING, $update_setting_data, $update_setting_where);
                    $total_credits = $update_setting_data['setting_data'];
                } else {
                    $insert_setting_array = array(
                        'setting_user_id' => $doctor_id,
                        'setting_clinic_id' => 0,
                        'setting_data' => $total_buy_credits,
                        'setting_type' => 9,
                        'setting_data_type' => 2,
                        'setting_created_at' => date("Y-m-d H:i:s")
                    );
                    $this->Common_model->insert(TBL_SETTING, $insert_setting_array);
                    $total_credits = $total_buy_credits;
                }
                $setting = $this->communication->get_global_setting_by_key('auto_increment_payment_invoice_no');
                
                $invoice_no = "MEDSMS" . $setting->global_setting_value;
                $update_payment_detail = array(
                    'razorpay_payment_id' => $payment_id,
                    'razorpay_signature' => $signature,
                    'invoice_no' => $invoice_no,
                    'payment_status' => 1,
                    'updated_at' => $this->utc_time_formated
                );
                $update_where = array(
                    'razorpay_order_id' => $order_id
                );
                $this->Common_model->update('me_user_payment_details', $update_payment_detail, $update_where);
                $this->communication->update_global_setting_by_key('auto_increment_payment_invoice_no',['global_setting_value' => $setting->global_setting_value + 1]);
                $where_array = array(
                    'razorpay_order_id' => $order_id
                );
                $doctor_payment = $this->Common_model->get_single_row('me_user_payment_details', 'payment_id', $where_array);
                $cron_job_path = CRON_PATH . " cron/generate_payment_receipt/" . $doctor_payment['payment_id'];
                exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('payment_success');
                $this->my_response['total_credits'] = $total_credits;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('payment_error');
            }
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_auto_added_groups_member_post() {
        try {
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            $patient_gender = !empty($this->Common_model->escape_data($this->post_data['group_gender'])) ? $this->Common_model->escape_data($this->post_data['group_gender']) : '';
            $patient_age_group = !empty($this->Common_model->escape_data($this->post_data['group_age'])) ? $this->Common_model->escape_data($this->post_data['group_age']) : '';
            $patient_disease_ids = !empty($this->Common_model->escape_data($this->post_data['group_disease_id'])) ? $this->Common_model->escape_data($this->post_data['group_disease_id']) : '';

            if (empty($doctor_id) || (empty($patient_gender) && empty($patient_age_group) && empty($patient_disease_ids))) {
                $this->bad_request();
            }
            $rece = $this->User_model->get_doctor_receptionist($doctor_id, 'user_id');
            $user_source_id_arr = array_column($rece, 'user_id');
            array_push($user_source_id_arr, $doctor_id);
            if(!empty($patient_disease_ids))
                $patient_disease_ids = explode(',', $patient_disease_ids);
            $where = [
                'doctor_id' => $doctor_id,
                'user_source_id_arr' => $user_source_id_arr,
                'patient_gender' => $patient_gender,
                'patient_disease_ids' => $patient_disease_ids,
                'patient_age_group' => $patient_age_group
            ];
            $group_members = $this->communication->get_auto_added_group_members($where);
            if (!empty($group_members)) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $group_members;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('common_detail_not_found');
                $this->my_response['data'] = array();
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }
}