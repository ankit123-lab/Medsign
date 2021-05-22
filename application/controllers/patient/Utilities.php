<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
use Razorpay\Api\Api;
class Utilities extends MY_Controller {

    public function __construct() {

        parent::__construct();
        $this->load->library('patient_auth');
        $this->load->model('patient_model','patient');
        $this->load->library('form_validation');
        $this->api_key = $GLOBALS['ENV_VARS']['RAZORPAY_KEY'];
        $this->api_secret = $GLOBALS['ENV_VARS']['RAZORPAY_SECRET'];
        if (!$this->patient_auth->is_logged_in()) {
            redirect('patient/login');
        }
    }

    public function list() {
        $view_data = array();
        $view_data['breadcrumbs'] = "Health Tracker";
        $view_data['page_title'] = "Health Tracker";
        $view_data['utilities_list'] = [];
        $uas7 = get_assign_uas7($this->patient_auth->get_user_id());
        if(!empty($uas7)){
            $where = ['user_id' => $this->patient_auth->get_logged_user_id(), 'detail_type' => 'diary'];
            $columns = "user_id";
            $last_payment = $this->patient->get_patient_last_payment($columns, $where);
            $uas7_title = 'UAS7 Diary';
            $uas7_desc = '<ul class="text-left" style="list-style:disc;"><li>The Urticaria Activity Score (UAS) is a commonly used diary-based patient-reported outcome measure that assesses itch (Pruritus)  severity and hive (Wheals)  count in chronic spontaneous urticaria (CSU).</li> <li>Chronic Idiopathic Urticaria (CIU) is sometimes referred to as chronic spontaneous urticaria.</li> <li>The UAS7 score is obtained as the sum of the daily average itch (pruritus)  and hive (Wheals)  scores over 7 days.</li> <li><small>Adapted from Reference: Indian J Dermatol Venereol Leprol | July-August-2006 | Vol72 | Issue4</small></li></ul>';
            if(empty($last_payment)){
                $view_data['utilities_list'][] = ['utility_name' => 'diary', 'utility_label' => $uas7_title, 'utility_desc' => $uas7_desc, 'utility_url' => "javascript:void(0);", 'is_paid' => false];
            } else {
                $view_data['utilities_list'][] = ['utility_name' => 'diary', 'utility_label' => $uas7_title, 'utility_desc' => $uas7_desc, 'utility_url' => site_url('patient/uas7diary'), 'is_paid' => true];
            }
        }
        $this->load->view('patient/utilities_list_view', $view_data);
    }

    public function payment_success() {
        $view_data = array();
        $view_data['breadcrumbs'] = "Payment Success";
        $view_data['page_title'] = "Payment Success";
        if(!empty($this->input->get('payment_id'))) {
            $view_data['download_url'] = site_url('patient/download_invoice/' . $this->input->get('payment_id'));
            $payment_id = encrypt_decrypt($this->input->get('payment_id'), 'decrypt');
            $payment_row = $this->Common_model->get_single_row('me_user_payment_details', 'paid_amount', array('payment_id' => $payment_id, 'user_id' => $this->patient_auth->get_user_id()));
            $view_data['paid_amount'] = $payment_row['paid_amount'];
        } else {
            $view_data['paid_amount'] = 0;
            $view_data['download_url'] = "javascript:void(0);";
        }
        $this->load->view('patient/payment_success_view', $view_data);
    }

    public function payment_popup() {
        $view_data = [];
        $response = [];
        $this->form_validation->set_rules('utility_name', 'utility name', 'required|trim');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $response['status'] = false;
            $view_data['errors'] = nl2br(strip_tags($errors));
            $response['html'] = $this->load->view('patient/payment_popup_view', $view_data, true);
        } else {
            $utilities_price = $this->Common_model->get_single_row('me_global_settings', 'global_setting_value', array('global_setting_key' => 'utilities_price', 'global_setting_status' => 1));
            $utilities_price_arr = json_decode($utilities_price['global_setting_value'], true);
            $utility_price = $utilities_price_arr[set_value('utility_name')];
            $view_data['utility_name'] = set_value('utility_name');
            $view_data['utility_price'] = $utility_price;
            $view_data['gst_pecent'] = UAS7_GST_PERCENT;
            $view_data['gst_amount'] = $utility_price*$view_data['gst_pecent']/100;
            $view_data['total_price'] = $utility_price+$view_data['gst_amount'];
            $view_data['sgst_amount'] = 0;
            $view_data['igst_amount'] = 0;
            $view_data['is_apply_igst'] = false;
            $response['status'] = true;
            $response['html'] = $this->load->view('patient/payment_popup_view', $view_data, true);
        }
        echo json_encode($response);
    }

    public function create_payment_credits_order() {
        $this->form_validation->set_rules('utility_name', 'utility_name', 'required|trim');
        $this->form_validation->set_rules('is_apply_igst', 'is_apply_igst', 'trim');
        $response = [];
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $response['status'] = false;
            $response['errors'] = nl2br(strip_tags($errors));
        } else {
            $get_global_setting_data = $this->patient->get_global_setting_by_key_arr(['gst_percentage','utilities_price']);
            $global_setting_data = array_column($get_global_setting_data, 'global_setting_value', 'global_setting_key');
            $utilities_price_arr = json_decode($global_setting_data['utilities_price'], true);
            $utility_price = $utilities_price_arr[set_value('utility_name')];
            $sub_amount = number_format($utility_price, 2, '.', '');
            $igst_amount = $sub_amount*UAS7_GST_PERCENT/100;
            $paid_amount = number_format(($utility_price+$igst_amount), 2, '.', '');
            $api = new Api($this->api_key, $this->api_secret);
            $receipt_no = time();
            $order  = $api->order->create(array('receipt' => $receipt_no, 'amount' => str_replace('.', '', $paid_amount), 'currency' => 'INR'));
            if(!empty($order->id)) {
                $gst_percentage = json_decode($global_setting_data['gst_percentage'], true);
                $payment_data = array(
                    'user_id' => $this->patient_auth->get_user_id(),
                    'sub_plan_name' => "UAS7 Diary annual subscription.",
                    'sub_plan_validity' => "1 Year",
                    'plan_start_date' => get_display_date_time("Y-m-d"),
                    'plan_end_date' => date("Y-m-d", strtotime("+1 years", strtotime( get_display_date_time("Y-m-d H:i:s")))),
                    'payment_type' => 3, //3=Online
                    'receipt_no' => $receipt_no,
                    'sub_total' => $sub_amount,
                    'tax_cgst_percent' => 0.00,
                    'tax_sgst_percent' => 0.00,
                    'tax_igst_percent' => UAS7_GST_PERCENT,
                    'tax_cgst_amount' => 0.00,
                    'tax_sgst_amount' => 0.00,
                    'tax_igst_amount' => $igst_amount,
                    'paid_amount' => $paid_amount,
                    'razorpay_order_id' => $order->id,
                    'payment_status' => 2, //created,
                    'created_at' => date("Y-m-d H:i:s"),
                    'detail_type' => set_value('utility_name')
                );
                $this->Common_model->insert('me_user_payment_details', $payment_data);
                $patient_detail = $this->Common_model->get_single_row('me_users', 
                    'user_first_name,user_last_name,user_email,user_phone_number', 
                    array('user_id' => $this->patient_auth->get_user_id())
                );
                $response['order_id'] = $order->id;
                $response['paid_amount'] = $paid_amount;
                $response['name'] = $patient_detail['user_first_name'] . ' ' . $patient_detail['user_last_name'];
                $response['email'] = $patient_detail['user_email'];
                $response['contact'] = $patient_detail['user_phone_number'];
                $response['address'] = "";
                $response['key'] = $this->api_key;
                $response['description'] = "UAS7 Diary annual subscription.";
                $response['status'] = true;
            } else {
                $response['status'] = false;
                $response['errors'] = "Unable to process request please try again.";
            }
        }
        echo json_encode($response);
    }

    public function payment_capture() {
        $this->form_validation->set_rules('paid_amount', 'paid_amount', 'required|trim');
        $this->form_validation->set_rules('payment_id', 'payment_id', 'required|trim');
        $this->form_validation->set_rules('order_id', 'order_id', 'required|trim');
        $this->form_validation->set_rules('signature', 'signature', 'required|trim');
        $response = [];
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $response['status'] = false;
            $response['errors'] = nl2br(strip_tags($errors));
        } else {
            $paid_amount = set_value('paid_amount');
            $payment_id = set_value('payment_id');
            $order_id = set_value('order_id');
            $signature = set_value('signature');
            $api = new Api($this->api_key, $this->api_secret);
            $payment  = $api->payment->fetch($payment_id)->capture(array('amount' => str_replace('.', '', $paid_amount)));
            if(!empty($payment)) {
                $get_global_setting_data = $this->patient->get_global_setting_by_key_arr(['auto_increment_payment_invoice_no']);
                $global_setting_data = array_column($get_global_setting_data, 'global_setting_value', 'global_setting_key');
                $invoice_no = "MEDUAS7DIARY" . $global_setting_data['auto_increment_payment_invoice_no'];
                $update_payment_detail = array(
                    'razorpay_payment_id' => $payment_id,
                    'razorpay_signature' => $signature,
                    'invoice_no' => $invoice_no,
                    'payment_status' => 1,
                    'updated_at' => date("Y-m-d H:i:s")
                );
                $update_where = array(
                    'razorpay_order_id' => $order_id
                );
                $this->Common_model->update('me_user_payment_details', $update_payment_detail, $update_where);
                $this->patient->update_global_setting_by_key('auto_increment_payment_invoice_no',['global_setting_value' => $global_setting_data['auto_increment_payment_invoice_no'] + 1]);
                $where_array = array(
                    'razorpay_order_id' => $order_id
                );
                $user_payment = $this->Common_model->get_single_row('me_user_payment_details', 'payment_id', $where_array);
                $cron_job_path = DOCROOT_PATH . "index.php cron/send_payment_receipt_email/" . $user_payment['payment_id'];
                exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");
                $response['status'] = true;
                $response['payment_id'] = encrypt_decrypt($user_payment['payment_id'], 'encrypt');;
            } else {
                $response['status'] = false;
                $response['errors'] = "Unable to process request please try again.";
            }
        }
        echo json_encode($response);
        die;
    }

    public function download_invoice($payment_id) {
        $payment_id = encrypt_decrypt($payment_id, 'decrypt');
        $paymet_detail = $this->patient->get_payment_details_by_payment_id($payment_id);
        if(empty($paymet_detail->receipt_url)) {
            require_once MPDF_PATH;
            $address_data = [
                'address_name' => $paymet_detail->address_name,
                'address_name_one' => $paymet_detail->address_name_one,
                'address_locality' => $paymet_detail->address_locality,
                'city_name' => $paymet_detail->city_name,
                'state_name' => $paymet_detail->state_name,
                'address_pincode' => $paymet_detail->address_pincode
            ];
            $paymet_detail->patient_address = patient_address($address_data);
            $view_data = array();
            $view_data['paymet_detail'] = $paymet_detail;
            $get_global_setting_data = $this->patient->get_global_setting_by_key_arr(['contact_number','Email Address','Company Name','invoice_address','gujarat_branch_gst']);
            $global_setting_data = array_column($get_global_setting_data, 'global_setting_value', 'global_setting_key');        
            $view_data['global_setting'] = $global_setting_data;
            $view_data['global_setting']['payment_receipt_note'] = "For more details please contact us on " . $global_setting_data['Email Address'] . " or " . $global_setting_data['contact_number'] . ".";
            
            $lang_code = 'en-GB';
            $mpdf = new MPDF(
                    $lang_code, 'A4', 0, 'arial', 8, 8, 55, 8, 8, 5, 'P'
            );
            $mpdf->useOnlyCoreFonts = true;
            $mpdf->SetDisplayMode('real');
            $mpdf->list_indent_first_level = 0;
            $mpdf->setAutoBottomMargin = 'stretch';
            $header_html = $this->load->view("prints/payment_receipt_header", $view_data, true);
            $mpdf->SetHTMLHeader($header_html);
            $mpdf->SetHTMLFooter('
                <table width="100%">
                    <tr>
                        <td width="33%" style="font-size:10px">
                            Generated On: {DATE d/m/Y}
                        </td>
                        <td width="33%" style="font-size:10px" align="center">Page No. {PAGENO}/{nbpg}</td>
                        <td width="33%" align="right" style="font-size:10px">Powerd by Medsign</td>
                    </tr>
                </table>
            ');
            $view_html = $this->load->view("prints/payment_receipt", $view_data, true);
            $mpdf->WriteHTML($view_html);
            // $mpdf->Output('payment_receipt_'.$paymet_detail->invoice_no.'.pdf', 'D');
            // echo $mpdf->Output();die;
            $upload_path = DOCROOT_PATH . 'uploads/' . PDF_FOLDER . '/';
            if (!file_exists($upload_path)) {
                mkdir($upload_path, 0777, TRUE);
                chmod($upload_path, 0777);
            }
            $file_name = 'payment_receipt_'.$paymet_detail->invoice_no.'.pdf';
            $mpdf->Output($upload_path . $file_name, 'F');
            $attachment_path = $upload_path . $file_name;
            upload_to_s3($attachment_path, PAYMENT_RECEIPT_FOLDER.'/'.$paymet_detail->user_id.'/'.$file_name);
            $receipt_url = IMAGE_MANIPULATION_URL.PAYMENT_RECEIPT_FOLDER.'/'.$paymet_detail->user_id.'/'.$file_name;
            $update_where = array(
                'payment_id' => $payment_id
            );
            $this->Common_model->update('me_user_payment_details', ['receipt_url' => $receipt_url], $update_where);
            unlink($attachment_path);
        } else {
            $receipt_url = $paymet_detail->receipt_url;
        }
        if(!empty($receipt_url)) {
            $arr = explode('/', $receipt_url);
            $file_name = end($arr);
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary"); 
            header("Content-disposition: attachment; filename=\"".$file_name."\""); 
            readfile($receipt_url);
        }
    }
}