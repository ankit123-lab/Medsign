<?php
use Razorpay\Api\Api;
use Razorpay\Api\Errors;
class Razorpay_webhook extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
    }

    public function index() {
    	$post = file_get_contents('php://input');
        $data = json_decode($post, true);
        $api = new Api($GLOBALS['ENV_VARS']['RAZORPAY_KEY'], $GLOBALS['ENV_VARS']['RAZORPAY_SECRET']);
        if (!empty($_SERVER['HTTP_X_RAZORPAY_SIGNATURE'])) {
            try {
                $razorpayWebhookSecret = $GLOBALS['ENV_VARS']['RAZORPAY_WEBHOOK_SECRET'];
                $api->utility->verifyWebhookSignature($post,$_SERVER['HTTP_X_RAZORPAY_SIGNATURE'],$razorpayWebhookSecret);
                $event = $data['event'];
                $order_id = $data['payload']['payment']['entity']['order_id'];
                $payment_id = $data['payload']['payment']['entity']['id'];
                if($event == 'payment.failed') {
                    $updateData = array('payment_status' => 3, 'razorpay_payment_id' => $payment_id, 'transaction_details_json' => $post, 'updated_at' => date('Y-m-d H:i:s'));
                    $where = array('razorpay_order_id' => $order_id);
                    $this->Common_model->update('me_user_payment_details', $updateData, $where);
                } elseif($event == 'payment.captured') {
                    $updateData = array('transaction_details_json' => $post, 'updated_at' => date('Y-m-d H:i:s'));
                    $where = array('razorpay_order_id' => $order_id);
                    $this->Common_model->update('me_user_payment_details', $updateData, $where);
                }
            }
            catch (Errors\SignatureVerificationError $e) {
                $error_log = array(
                    'message'   => $e->getMessage(),
                    'data'      => $data,
                    'event'     => 'razorpay.wc.signature.verify_failed'
                );
                $log = './application/logs/razorpay' . date('d-m-Y') . ".txt";
                file_put_contents($log, "\n  ================ START ".date('d-m-Y h:i:s')." =====================    \n\n", FILE_APPEND);
                file_put_contents($log, json_encode($error_log), FILE_APPEND);
                return;
            }
            
        }
    	die();
    }
}
