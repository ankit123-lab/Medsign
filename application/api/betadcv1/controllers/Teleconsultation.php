<?php
use Razorpay\Api\Api;
class Teleconsultation extends MY_Controller {
    private $api_key = '';
    private $api_secret = '';
    public function __construct() {
		parent::__construct();
        $this->api_key = $GLOBALS['ENV_VARS']['RAZORPAY_KEY'];
        $this->api_secret = $GLOBALS['ENV_VARS']['RAZORPAY_SECRET'];
        $this->load->model("Teleconsultation_model", "teleconsultation");
        $this->load->model("Communication_model", "communication");
        $this->load->model("User_model");
    }

    public function get_teleconsultation_date_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $clinic_id = !empty($this->post_data['clinic_id']) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $page = !empty($this->Common_model->escape_data($this->post_data['page'])) ? trim($this->Common_model->escape_data($this->post_data['page'])) : '1';
            $per_page = !empty($this->Common_model->escape_data($this->post_data['per_page'])) ? trim($this->Common_model->escape_data($this->post_data['per_page'])) : '10';
            if(empty($doctor_id)
            ) {
                $this->bad_request();
                exit;
            }
            $where['doctor_id'] = $doctor_id;
            $where['clinic_id'] = $clinic_id;
            $where['page'] = $page;
            $where['per_page'] = $per_page;
            $result = $this->teleconsultation->get_teleconsultation_date($where);
            $count = $this->teleconsultation->get_teleconsultation_date($where, true);
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

    public function get_teleconsultation_list_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $clinic_id = !empty($this->post_data['clinic_id']) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
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
            $where['clinic_id'] = $clinic_id;
            $where['date'] = $date;
            $where['page'] = $page;
            $where['per_page'] = $per_page;
            $result = $this->teleconsultation->get_teleconsultation_list($where);
            foreach ($result as $key => $value) {
                $result[$key]->call_duration_time = floor($value->call_duration_time/60);
            }
            $count = $this->teleconsultation->get_teleconsultation_list($where, true);
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

    public function get_tele_global_data_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $page = !empty($this->Common_model->escape_data($this->post_data['page'])) ? trim($this->Common_model->escape_data($this->post_data['page'])) : '1';
            $per_page = !empty($this->Common_model->escape_data($this->post_data['per_page'])) ? trim($this->Common_model->escape_data($this->post_data['per_page'])) : '10';
            if(empty($doctor_id)
            ) {
                $this->bad_request();
                exit;
            }
            $where_array = array(
                'setting_user_id' => $doctor_id,
                'setting_type' => 10,
                'setting_status' => 1
            );
            $get_setting_data = $this->Common_model->get_single_row(TBL_SETTING, 'setting_data', $where_array);
            $get_global_setting_data = $this->teleconsultation->get_global_setting_by_key_arr(['teleconsult_per_minute_price','gst_percentage']);
            $global_setting_data = array_column($get_global_setting_data, 'global_setting_value', 'global_setting_key');
            $responseData['available_minutes'] = !empty($get_setting_data) ? floor($get_setting_data['setting_data']/60) : 0;
            $responseData['per_minute_price'] = $global_setting_data['teleconsult_per_minute_price'];
            $responseData['gst_percent'] = json_decode($global_setting_data['gst_percentage']);
            $clinic_details = $this->communication->get_doctor_clinic_detail($doctor_id);
            $global_setting = $this->communication->get_global_setting_by_key('gst_registration_state_id');
            $is_apply_igst = true;
            if(!empty($clinic_details->address_state_id) && $clinic_details->address_state_id == $global_setting->global_setting_value) {
                $is_apply_igst = false;
            }
            $responseData['is_apply_igst'] = $is_apply_igst;
            $this->my_response['data'] = $responseData;
            $this->my_response['status'] = true;
            $this->my_response['message'] = lang('common_detail_found');
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function create_payment_minutes_order_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $sub_amount = !empty($this->post_data['sub_amount']) ? trim($this->Common_model->escape_data($this->post_data['sub_amount'])) : '';
            $paid_amount = !empty($this->post_data['paid_amount']) ? trim($this->Common_model->escape_data($this->post_data['paid_amount'])) : '';
            $cgst_amount = !empty($this->post_data['cgst_amount']) ? trim($this->Common_model->escape_data($this->post_data['cgst_amount'])) : '';
            $sgst_amount = !empty($this->post_data['sgst_amount']) ? trim($this->Common_model->escape_data($this->post_data['sgst_amount'])) : '';
            $igst_amount = !empty($this->post_data['igst_amount']) ? trim($this->Common_model->escape_data($this->post_data['igst_amount'])) : '';
            $is_apply_igst = !empty($this->post_data['is_apply_igst']) ? trim($this->Common_model->escape_data($this->post_data['is_apply_igst'])) : '';
            $total_credits = !empty($this->post_data['total_credits']) ? trim($this->Common_model->escape_data($this->post_data['total_credits'])) : '';
            $gst_percentage = !empty($this->post_data['gst_percentage']) ? $this->post_data['gst_percentage'] : '';
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
                        'detail_type' => 'teleconsult_minutes'
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

    public function payment_minutes_capture_post() {
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
                    'setting_type' => 10,
                    'setting_status' => 1
                );
                $get_setting_data = $this->Common_model->get_single_row(TBL_SETTING, 'setting_id, setting_data', $where_array);
                if(!empty($get_setting_data)) {
                    $update_setting_data = array(
                        'setting_data' => $get_setting_data['setting_data'] + ($total_buy_credits*60),
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
                        'setting_data' => ($total_buy_credits*60),
                        'setting_type' => 10,
                        'setting_data_type' => 2,
                        'setting_created_at' => date("Y-m-d H:i:s")
                    );
                    $this->Common_model->insert(TBL_SETTING, $insert_setting_array);
                    $total_credits = ($total_buy_credits*60);
                }
                $setting = $this->communication->get_global_setting_by_key('auto_increment_payment_invoice_no');
                
                $invoice_no = "MEDTELEMINUTES" . $setting->global_setting_value;
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
                $this->my_response['total_credits'] = floor($total_credits/60);
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
}