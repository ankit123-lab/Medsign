<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Twilio\Rest\Client;
class Whatsapp {
	protected $_sms_send_from = 1; // Twilio
	public function __construct() {
		$this->CI =& get_instance();
	}

	public function send_message($data) {
		if($this->_sms_send_from == 1 && $GLOBALS['ENV_VARS']['IS_SEND_WHATSAPP_SMS']) { //1=Send whatsapp message from Twilio
			$this->twilio($data);
		}
	}

	private function twilio($data) {
		$sid = $GLOBALS['ENV_VARS']['TWILIO_SID'];
        $token = $GLOBALS['ENV_VARS']['TWILIO_TOKEN'];
        $twilio = new Client($sid, $token);
        $request = array(
                        "from" => "whatsapp:+" . $GLOBALS['ENV_VARS']['TWILIO_WHATSAPP_NUMBER'],
                        "body" => $data['body']
                    );
        if(!empty($data['mediaUrl']))
        	$request['mediaUrl'] = [$data['mediaUrl']];
        $message = $twilio->messages
                    ->create("whatsapp:+91" . $data['mobile'], // to
                        $request
                    );
        $data['sms_sid'] = $message->sid;
        $this->insert_log($data);
        if(!empty($data['doctor_id']))
        	$this->update_credit($data);
	}

	private function update_credit($data) {
		$setting_where = array(
            'setting_user_id' => $data['doctor_id'],
            'setting_type' => 8
        );
        $get_setting_data = $this->CI->Common_model->get_single_row('me_settings', '', $setting_where);
        if(!empty($get_setting_data)) {
        	$update_setting_data = array(
                'setting_data' => $get_setting_data['setting_data'] - 1,
                'setting_updated_at' => date("Y-m-d H:i:s")
            );
            $update_setting_where = array(
                'setting_id' => $get_setting_data['setting_id']
            );
            $this->CI->Common_model->update('me_settings', $update_setting_data, $update_setting_where);
        }
	}
	private function insert_log($data) {
		$log_data = array(
			'sms_type' => 2,
			'created_at' => date("Y-m-d H:i:s")
		);
		if(!empty($data['doctor_id']))
			$log_data['doctor_id'] = $data['doctor_id'];
		if(!empty($data['patient_id']))
			$log_data['patient_id'] = $data['patient_id'];
		if(!empty($data['user_type']))
			$log_data['user_type'] = $data['user_type'];
		if(!empty($data['sms_sid']))
			$log_data['sms_sid'] = $data['sms_sid'];
		$this->CI->Common_model->insert('me_message_tracking', $log_data);
	}

	public function update_log($data) {
		if(!empty($data['SmsSid'])) {
			$where = ['sms_sid' => $data['SmsSid']];
			$update_data = [];
			$update_data['json_data'] = json_encode($data);
			$update_data['updated_at'] = date("Y-m-d H:i:s");
			$this->CI->Common_model->update('me_message_tracking', $update_data, $where);
		}
	}
}