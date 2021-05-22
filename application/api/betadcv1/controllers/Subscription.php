<?php
/*** This controller use for Doctor related activity*/
class Subscription extends MY_Controller {

    public function __construct() {
		parent::__construct();
        $this->load->model("subscription_model");
    }

    public function get_doctor_subscription_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            if (empty($doctor_id)) {
                $this->bad_request();
                exit;
            }
            $columns = 'ds.sub_plan_id,ds.plan_start_date,ds.plan_expiry_date,ds.sub_plan_amount_override,ds.doctor_plan_type,spm.sub_plan_name,spm.sub_price,spm.sub_tax_sgst,spm.sub_tax_cgst,spm.sub_tax_igst,spm.sub_description,spm.sub_plan_type,spm.sub_plan_validity';
            $sub_details = $this->subscription_model->get_doctor_subscription($doctor_id, $columns);
            $clinic_details = $this->subscription_model->get_doctor_clinic_detail($doctor_id);
            $global_setting = $this->subscription_model->get_global_setting_by_key('gst_registration_state_id');
            if(!empty($sub_details->sub_plan_id)) {
                $sub_details->sub_description = html_entity_decode($sub_details->sub_description);
                $sub_details->sub_period = get_sub_plan_validity($sub_details->sub_plan_type, $sub_details->sub_plan_validity);
                $columns = 'sub_plan_id';
                $sub_plan = $this->subscription_model->get_subscription_plan($columns,['sub_price' => $sub_details->sub_price]);
                if(!empty($sub_details->plan_expiry_date)) {
                    if($sub_details->doctor_plan_type == 1) {
                        $sub_details->expiry_txt = 'This free trial expired on ' . date('d/m/Y', strtotime($sub_details->plan_expiry_date)) . '.';
                    } else {
                        $sub_details->expiry_txt = 'This subscription plan expired on ' . date('d/m/Y', strtotime($sub_details->plan_expiry_date)) . '.';
                    }
                }
                if(empty($sub_details->sub_plan_amount_override) || $sub_details->sub_plan_amount_override == '0.00') {
                    $sub_details->sub_price = $sub_details->sub_price;
                } else {
                    $sub_details->sub_price = $sub_details->sub_plan_amount_override;
                }
                $sub_details->isShowRenewButton = false;
                if($sub_details->doctor_plan_type == 1 || $sub_details->plan_expiry_date < date('Y-m-d', strtotime("+15 days"))) {
                    $sub_details->isShowRenewButton = true;
                }
                $sub_details->isShowUpgradeButton = false;
                if(count($sub_plan) > 0) {
                    $payment_details = $this->subscription_model->get_doctor_last_payment_details(['user_id' => $doctor_id, 'sub_plan_id' => $sub_details->sub_plan_id] , 'paid_amount');
                    if(!empty($payment_details->paid_amount)) {
                        $paid_amount = $payment_details->paid_amount;
                        $diff = strtotime(date('Y-m-d')) - strtotime($sub_details->plan_start_date);
                        // 1 day = 24 hours 
                        // 24 * 60 * 60 = 86400 seconds 
                        $days = abs(round($diff / 86400));
                        $days++;
                        $diff_validity = strtotime($sub_details->plan_expiry_date) - strtotime($sub_details->plan_start_date);
                        $days_validity = abs(round($diff_validity / 86400));
                        $days_validity = $days_validity-1;
                        $settlement_amount = $payment_details->paid_amount - (($payment_details->paid_amount / $days_validity) * $days);

                        $sub_details->days = $days;
                        $sub_details->days_validity = $days_validity;
                        $sub_details->settlement_amount = ceil($settlement_amount);
                    }
                    $sub_details->isShowUpgradeButton = true;

                }

            } else {
                $sub_details = array();
            }
            $is_apply_igst = true;
            if(!empty($clinic_details->address_state_id) && $clinic_details->address_state_id == $global_setting->global_setting_value) {
                $is_apply_igst = false;
            }
            $this->my_response['status'] = true;
            $this->my_response['data']['sub_details'] = $sub_details;
            $this->my_response['data']['is_apply_igst'] = $is_apply_igst;
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_subscription_plan_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $sub_plan_price = !empty($this->post_data['sub_plan_price']) ? trim($this->Common_model->escape_data($this->post_data['sub_plan_price'])) : '';
            if (empty($doctor_id)) {
                $this->bad_request();
                exit;
            }
            $columns = 'sub_plan_id,sub_plan_name,sub_description,sub_plan_type,sub_plan_validity,sub_price,sub_tax_sgst,sub_tax_cgst,sub_tax_igst';
            $sub_plan = $this->subscription_model->get_subscription_plan($columns,['sub_price' => $sub_plan_price]);
            foreach ($sub_plan as $key => $value) {
                $sub_plan[$key]->sub_description = html_entity_decode($value->sub_description);
                $sub_plan[$key]->sub_period = get_sub_plan_validity($value->sub_plan_type, $value->sub_plan_validity);
                $sub_plan[$key]->is_upgrade_plan = false;
                if(!empty($sub_plan_price))
                    $sub_plan[$key]->is_upgrade_plan = true;
            }
            $this->my_response['status'] = true;
            $this->my_response['data'] = $sub_plan;
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }   

    public function get_doctor_subscription_history_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            if (empty($doctor_id)) {
                $this->bad_request();
                exit;
            }
            $subscription_history = $this->subscription_model->get_doctor_subscription_history($doctor_id);
            foreach($subscription_history as $key => $value) {
                $subscription_history[$key]->plan_end_date = date('d/m/Y', strtotime($value->plan_end_date));
                $subscription_history[$key]->plan_start_date = date('d/m/Y', strtotime($value->plan_start_date));
                $subscription_history[$key]->receipt_url = get_file_full_path($value->receipt_url);
            }
            $this->my_response['status'] = true;
            $this->my_response['data'] = $subscription_history;
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    } 

    public function apply_promo_code_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $promo_code = !empty($this->post_data['promo_code']) ? trim($this->Common_model->escape_data($this->post_data['promo_code'])) : '';
            if (empty($doctor_id) || empty($promo_code)) {
                $this->bad_request();
                exit;
            }
            $promo_details = $this->subscription_model->get_promo_code($promo_code, $doctor_id);
            if(!empty($promo_details->promo_id)) {
                if(!empty($promo_details->promo_users_status)) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('promo_code_used');
                    $this->send_response();
                } else {
                    $this->my_response['status'] = true;
                    $this->my_response['data'] = $promo_details;
                    $this->my_response['message'] = lang('promo_code_success');
                    $this->send_response();
                }
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('promo_code_invalid');
                $this->send_response();
            }
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    } 

    public function order_without_payment_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $sub_plan_id = !empty($this->post_data['sub_plan_id']) ? trim($this->Common_model->escape_data($this->post_data['sub_plan_id'])) : '';
            $sub_amount = !empty($this->post_data['sub_amount']) ? trim($this->Common_model->escape_data($this->post_data['sub_amount'])) : '';
            $settlement_discount = !empty($this->post_data['settlement_discount']) ? trim($this->Common_model->escape_data($this->post_data['settlement_discount'])) : 0;
            $discount = !empty($this->post_data['discount']) ? trim($this->Common_model->escape_data($this->post_data['discount'])) : 0;
            $promo_id = !empty($this->post_data['promo_id']) ? trim($this->Common_model->escape_data($this->post_data['promo_id'])) : 0;
            $is_change_plan = !empty($this->post_data['is_change_plan']) ? true : false;
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
                $receipt_no = time();
                $setting = $this->subscription_model->get_global_setting_by_key('auto_increment_payment_invoice_no');
                if(!empty($plan_detail->sub_invoice_prefix))
                    $invoice_no = $plan_detail->sub_invoice_prefix.$setting->global_setting_value;
                else
                    $invoice_no = $setting->global_setting_value;

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
                    'tax_cgst_percent' => 0.00,
                    'tax_sgst_percent' => 0.00,
                    'tax_igst_percent' => 0.00,
                    'tax_cgst_amount' => 0.00,
                    'tax_sgst_amount' => 0.00,
                    'tax_igst_amount' => 0.00,
                    'discount_amount' => $discount,
                    'invoice_no' => $invoice_no,
                    'paid_amount' => 0.00,
                    'payment_status' => 1, //created,
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
                            'created_at' => $this->utc_time_formated,
                            'promo_users_status' => 1
                        );
                        $this->subscription_model->create_promo_user($promo_user_data);
                    }
                }
                $columns = 'ds.doctor_plan_type,spm.sub_plan_type,spm.sub_plan_validity,spm.sub_invoice_prefix';
                $doctor_sub_plan = $this->subscription_model->get_doctor_subscription($doctor_id,$columns);
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

                $this->subscription_model->update_global_setting_by_key('auto_increment_payment_invoice_no',['global_setting_value' => $setting->global_setting_value + 1]);

                $cron_job_path = CRON_PATH . " cron/generate_payment_receipt/" . $payment_id;
                exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('payment_success');
                $this->my_response['data']['sub_plan_setting'] = doctor_sub_plan_setting($doctor_id);
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

}