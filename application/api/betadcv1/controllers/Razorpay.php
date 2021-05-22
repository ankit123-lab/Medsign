<?php
use Razorpay\Api\Api;
class Razorpay extends MY_Controller {
    private $api_key = '';
    private $api_secret = '';
    public function __construct() {
        parent::__construct();
        $this->api_key = $GLOBALS['ENV_VARS']['RAZORPAY_KEY'];
        $this->api_secret = $GLOBALS['ENV_VARS']['RAZORPAY_SECRET'];
        $this->load->model("subscription_model");
    }

    public function create_payment_order_post() {
    	try {
    		$doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $sub_plan_id = !empty($this->post_data['sub_plan_id']) ? trim($this->Common_model->escape_data($this->post_data['sub_plan_id'])) : '';
            $sub_amount = !empty($this->post_data['sub_amount']) ? trim($this->Common_model->escape_data($this->post_data['sub_amount'])) : '';
            $settlement_discount = !empty($this->post_data['settlement_discount']) ? trim($this->Common_model->escape_data($this->post_data['settlement_discount'])) : 0;
            $discount = !empty($this->post_data['discount']) ? trim($this->Common_model->escape_data($this->post_data['discount'])) : 0;
            $promo_id = !empty($this->post_data['promo_id']) ? trim($this->Common_model->escape_data($this->post_data['promo_id'])) : 0;
            $is_apply_igst = !empty($this->post_data['is_apply_igst']) ? true : false;
            if (empty($doctor_id) || empty($sub_plan_id) || empty($sub_amount)) {
                $this->bad_request();
                exit;
            }
            $plan_detail = $this->subscription_model->get_sub_plan_by_id($sub_plan_id);
            if(!empty($plan_detail->sub_plan_id)) {
                if($plan_detail->sub_plan_type == 2) {
                    $time_add = "+ " . $plan_detail->sub_plan_validity . " years";
                } else {
                    $time_add = "+ " . $plan_detail->sub_plan_validity . " months";
                }
                $expiry_date = date('Y-m-d', strtotime($time_add));
                $tax_cgst_amount = 0.00;
                $tax_sgst_amount = 0.00;
                $tax_igst_amount = 0.00;
                $paid_sub_amount = $sub_amount;
                if($settlement_discount > 0) {
                    $paid_sub_amount = $sub_amount - $settlement_discount;
                }
                if($discount > 0) {
                    $paid_sub_amount = $paid_sub_amount - $discount;
                }
                if($is_apply_igst) {
                    if($plan_detail->sub_tax_igst > 0) {
                        $tax_igst_amount = number_format(($paid_sub_amount * $plan_detail->sub_tax_igst / 100), 2, '.', '');
                    }
                    $paid_amount = number_format(($paid_sub_amount + $tax_igst_amount), 2, '.', '');
                } else {
                    if($plan_detail->sub_tax_cgst > 0) {
                        $tax_cgst_amount = number_format(($paid_sub_amount * $plan_detail->sub_tax_cgst / 100), 2, '.', '');
                    }
                    if($plan_detail->sub_tax_sgst > 0) {
                        $tax_sgst_amount = number_format(($paid_sub_amount * $plan_detail->sub_tax_sgst / 100), 2, '.', '');
                    }
                    $paid_amount = number_format(($paid_sub_amount + $tax_cgst_amount + $tax_sgst_amount), 2, '.', '');
                }
                $api = new Api($this->api_key, $this->api_secret);
                $receipt_no = time();
                $order  = $api->order->create(array('receipt' => $receipt_no, 'amount' => str_replace('.', '', $paid_amount), 'currency' => 'INR'));
                if(!empty($order->id)) {
                    $payment_data = array(
                        'user_id' => $doctor_id,
                        'sub_plan_id' => $sub_plan_id,
                        'sub_plan_name' => $plan_detail->sub_plan_name,
                        'sub_plan_validity' => get_sub_plan_validity($plan_detail->sub_plan_type, $plan_detail->sub_plan_validity),
                        'plan_start_date' => date('Y-m-d'),
                        'plan_end_date' => $expiry_date,
                        'payment_type' => 3, //3=Online
                        'receipt_no' => $receipt_no,
                        'sub_total' => $sub_amount,
                        'settlement_discount' => $settlement_discount,
                        'tax_cgst_percent' => (!$is_apply_igst) ? $plan_detail->sub_tax_cgst : 0.00,
                        'tax_sgst_percent' => (!$is_apply_igst) ? $plan_detail->sub_tax_sgst : 0.00,
                        'tax_igst_percent' => ($is_apply_igst) ? $plan_detail->sub_tax_igst : 0.00,
                        'tax_cgst_amount' => $tax_cgst_amount,
                        'tax_sgst_amount' => $tax_sgst_amount,
                        'tax_igst_amount' => $tax_igst_amount,
                        'discount_amount' => $discount,
                        'paid_amount' => $paid_amount,
                        'razorpay_order_id' => $order->id,
                        'payment_status' => 2, //created,
                        'created_at' => $this->utc_time_formated

                    );
                    $payment_id = $this->subscription_model->create_doctor_payment_details($payment_data);
                    if($promo_id > 0) {
                        $promo_detail = $this->subscription_model->get_promo_by_id($promo_id);
                        if(!empty($promo_detail->promo_id)) {
                            $promo_user_data = array(
                                'payment_id' => $payment_id,
                                'user_id' => $doctor_id,
                                'promo_id' => $promo_detail->promo_id,
                                'promo_code' => $promo_detail->promo_code,
                                'promo_discount_type' => $promo_detail->promo_discount_type,
                                'promo_discount' => $promo_detail->promo_discount,
                                'promo_assign_user_id' => $promo_detail->promo_assign_user_id,
                                'promo_start_date' => $promo_detail->promo_start_date,
                                'promo_expiry_date' => $promo_detail->promo_expiry_date,
                                'created_at' => $this->utc_time_formated
                            );
                            $this->subscription_model->create_promo_user($promo_user_data);
                        }
                    }
                    $doctor_detail = $this->subscription_model->doctor_details($doctor_id);
                    $return['order_id'] = $order->id;
                    $return['paid_amount'] = $paid_amount;
                    $return['name'] = DOCTOR. ' ' . $doctor_detail->user_first_name . ' ' . $doctor_detail->user_last_name;
                    $return['email'] = $doctor_detail->user_email;
                    $return['contact'] = $doctor_detail->user_phone_number;
                    $return['address'] = $doctor_detail->address_name_one;
                    $return['key'] = $this->api_key;
                    $return['description'] = $plan_detail->sub_plan_name;
                    $this->my_response['status'] = true;
                    $this->my_response['message'] = lang('common_detail_found');
                    $this->my_response['data'] = $return;
                } else {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('order_error');
                }
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('invalid_sub_plan');
            }
            $this->send_response();
		} catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function payment_capture_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $sub_plan_id = !empty($this->post_data['sub_plan_id']) ? trim($this->Common_model->escape_data($this->post_data['sub_plan_id'])) : '';
            $paid_amount = !empty($this->post_data['paid_amount']) ? trim($this->Common_model->escape_data($this->post_data['paid_amount'])) : '';
            $payment_id = !empty($this->post_data['payment_id']) ? trim($this->Common_model->escape_data($this->post_data['payment_id'])) : '';
            $order_id = !empty($this->post_data['order_id']) ? trim($this->Common_model->escape_data($this->post_data['order_id'])) : '';
            $signature = !empty($this->post_data['signature']) ? trim($this->Common_model->escape_data($this->post_data['signature'])) : '';
            $is_change_plan = !empty($this->post_data['is_change_plan']) ? true : false;
            if (empty($doctor_id) || empty($sub_plan_id) || empty($paid_amount) || empty($payment_id) || empty($order_id) || empty($signature)) {
                $this->bad_request();
                exit;
            }
            $api = new Api($this->api_key, $this->api_secret);
            $payment  = $api->payment->fetch($payment_id)->capture(array('amount' => str_replace('.', '', $paid_amount)));
            if(!empty($payment)) {
                $columns = 'ds.doctor_plan_type,spm.sub_plan_type,spm.sub_plan_validity,spm.sub_invoice_prefix';
                $doctor_sub_plan = $this->subscription_model->get_doctor_subscription($doctor_id,$columns);
                $sub_plan_detail = $this->subscription_model->get_sub_plan_by_id($sub_plan_id);
                $doctor_payment = $this->subscription_model->get_doctor_payment_by_id($order_id,'payment_id');

                if($sub_plan_detail->sub_plan_type == 2) {
                    $time_add = "+ " . $sub_plan_detail->sub_plan_validity . " years";
                } else {
                    $time_add = "+ " . $sub_plan_detail->sub_plan_validity . " months";
                }
                $expiry_date = date('Y-m-d', strtotime($time_add));
                if($doctor_sub_plan->doctor_plan_type == 1 && !$is_change_plan) { //1=Free plan
                    $update_doctor_sub = array(
                        'plan_expiry_date' => $expiry_date,
                        'sub_plan_amount_override' => null,
                        'doctor_plan_type' => 2,
                        'updated_at' => $this->utc_time_formated
                    );
                    $this->subscription_model->update_doctor_subscription_by_id($doctor_id, $update_doctor_sub);
                } else {
                    assign_sub_plan($doctor_id,$sub_plan_id,2);
                }
                $setting = $this->subscription_model->get_global_setting_by_key('auto_increment_payment_invoice_no');
                if(!empty($sub_plan_detail->sub_invoice_prefix))
                    $invoice_no = $sub_plan_detail->sub_invoice_prefix.$setting->global_setting_value;
                else
                    $invoice_no = $setting->global_setting_value;
                $update_payment_detail = array(
                    'razorpay_payment_id' => $payment_id,
                    'razorpay_signature' => $signature,
                    'invoice_no' => $invoice_no,
                    'payment_status' => 1,
                    'updated_at' => $this->utc_time_formated
                );
                $this->subscription_model->update_doctor_payment_details($order_id,$update_payment_detail);
                $update_promo_users = ['promo_users_status' => 1, 'updated_at' => $this->utc_time_formated];
                $this->subscription_model->update_promo_users($doctor_payment->payment_id,$update_promo_users);
                $this->subscription_model->update_global_setting_by_key('auto_increment_payment_invoice_no',['global_setting_value' => $setting->global_setting_value + 1]);

                $cron_job_path = CRON_PATH . " cron/generate_payment_receipt/" . $doctor_payment->payment_id;
                exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('payment_success');
                $this->my_response['data']['sub_plan_setting'] = doctor_sub_plan_setting($doctor_id);
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
