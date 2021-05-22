<?php

Class Share_link extends MY_Controller {

	public function __construct() {
		parent::__construct();
	}

	public function get_share_link_post() {
		try {
			$doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';

			if (empty($doctor_id)) {
				$this->bad_request();
			}
			$get_role_details = $this->Common_model->get_the_role($this->user_id);
			if (!empty($get_role_details['user_role_data'])) {
				$permission_data = array(
					'role_data' => $get_role_details['user_role_data'],
					'module' => 48,
					'key' => 3
				);
				$check_module_permission = $this->check_module_permission($permission_data);
				if ($check_module_permission == 2) {
					$this->my_response['status'] = false;
					$this->my_response['message'] = lang('permission_error');
					$this->send_response();
				}
			}
			$where = ['doctor_id' => $doctor_id];
			$share_links = $this->Common_model->get_share_link($where);
			foreach ($share_links as $key => $value) {
				$share_links[$key]->encrp_id = encrypt_decrypt($value->registration_share_id, 'encrypt');
                $share_links[$key]->share_link = MEDSIGN_WEB_CARE_URL . 'register?id=' . encrypt_decrypt($value->registration_share_id, 'encrypt');
			}
			if (!empty($share_links)) {
				$this->my_response['status'] = true;
				$this->my_response['message'] = lang('common_detail_found');
				$this->my_response['data'] = $share_links;
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

	public function get_social_media_master_post() {
		try {
			$where = ['social_media_status' => 1];
			$social_media = $this->Common_model->get_all_rows('me_social_media_master', "social_media_id,social_media_name", $where);
			if (!empty($social_media)) {
				$this->my_response['status'] = true;
				$this->my_response['message'] = lang('common_detail_found');
				$this->my_response['data'] = $social_media;
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

	public function create_reg_link_post() {
		try {
            $registration_share_id = !empty($this->Common_model->escape_data($this->post_data['registration_share_id'])) ? $this->Common_model->escape_data($this->post_data['registration_share_id']) : '';
            $expiry_date = !empty($this->Common_model->escape_data($this->post_data['expiry_date'])) ? $this->Common_model->escape_data($this->post_data['expiry_date']) : '';
            $social_media_id = !empty($this->Common_model->escape_data($this->post_data['social_media_id'])) ? $this->Common_model->escape_data($this->post_data['social_media_id']) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? $this->Common_model->escape_data($this->post_data['clinic_id']) : '';
            
            if (empty($doctor_id) ||
                    empty($clinic_id) || empty($expiry_date)
            ) {
                $this->bad_request();
            }
            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 48,
                    'key' => !empty($registration_share_id) ? 2 : 1
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }
            
            if(!empty($registration_share_id)) {
                $update_data = array(
                    'registration_share_clinic_id' => $clinic_id,
                    'registration_share_expiry_date' => $expiry_date,
                    'registration_share_social_media_id' => (!empty($social_media_id)) ? $social_media_id : NULL,
                    'registration_share_updated_at' => $this->utc_time_formated
                );
                $update_where = array(
                    'registration_share_id' => $registration_share_id
                );
                $is_update = $this->Common_model->update('me_registration_share_link', $update_data, $update_where);
                $this->my_response['message'] = lang('share_link_updated');
            } else {
                $insert_data = array(
                    'registration_share_doctor_id' => $doctor_id,
                    'registration_share_clinic_id' => $clinic_id,
                    'registration_share_expiry_date' => $expiry_date,
                    'registration_share_social_media_id' => (!empty($social_media_id)) ? $social_media_id : NULL,
                    'registration_share_created_at' => $this->utc_time_formated
                );
                $this->Common_model->insert('me_registration_share_link', $insert_data);
                $this->my_response['message'] = lang('share_link_added');
            }
            $this->my_response['status'] = true;
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
	}

	public function delete_share_link_post() {
        try {
            $registration_share_id = !empty($this->Common_model->escape_data($this->post_data['registration_share_id'])) ? $this->Common_model->escape_data($this->post_data['registration_share_id']) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            if (empty($registration_share_id) ||
                    empty($doctor_id)
            ) {
                $this->bad_request();
            }
            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 48,
                    'key' => 4
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }
            
            $update_data = array(
                'registration_share_status' => 9,
                'registration_share_updated_at' => $this->utc_time_formated
            );
            $update_where = array(
                'registration_share_id' => $registration_share_id
            );
            $is_update = $this->Common_model->update('me_registration_share_link', $update_data, $update_where);
            if ($is_update > 0) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('share_link_deleted');
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function share_qrcode_link_post() {
        try {
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            $mobile_no = !empty($this->Common_model->escape_data($this->post_data['mobile_no'])) ? $this->Common_model->escape_data($this->post_data['mobile_no']) : '';
            $email = !empty($this->Common_model->escape_data($this->post_data['email'])) ? $this->Common_model->escape_data($this->post_data['email']) : '';
            $patient_name = !empty($this->Common_model->escape_data($this->post_data['patient_name'])) ? $this->Common_model->escape_data($this->post_data['patient_name']) : '';
            $share_type = !empty($this->Common_model->escape_data($this->post_data['share_type'])) ? $this->Common_model->escape_data($this->post_data['share_type']) : '';
            $encrp_id = !empty($this->Common_model->escape_data($this->post_data['encrp_id'])) ? $this->Common_model->escape_data($this->post_data['encrp_id']) : '';
            if ((empty($mobile_no) && empty($email)) ||
                    empty($doctor_id) ||
                    empty($encrp_id) ||
                    empty($patient_name) ||
                    empty($share_type)
            ) {
                $this->bad_request();
            }
            $send_request_data = array(
                'email' => $email,
                'encrp_id' => $encrp_id,
                'doctor_id' => $doctor_id,
                'patient_name' => $patient_name,
                'share_type' => $share_type,
                'mobile_no' => $mobile_no
            );
            $cron_job_path = CRON_PATH . " notification/share_qr_code_link/" . base64_encode(json_encode($send_request_data));
            exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");
            $log = LOG_FILE_PATH . 'qrcode_share_log_' . date('d-m-Y') . ".txt";
            file_put_contents($log, "\n  ================ START ".date('d-m-Y H:i:s')." =========== doctor_id: ". $doctor_id, FILE_APPEND);
            file_put_contents($log, "\n" .$cron_job_path."\n", FILE_APPEND);
            file_put_contents($log, "================ END =====================\n", FILE_APPEND);
            $this->my_response['status'] = true;
            $this->my_response['message'] = lang('share_qrcode_link');
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }
}