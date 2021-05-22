<?php
class Anatomical extends MY_Controller {

    public function __construct() {
		parent::__construct();
        $this->load->model("Anatomical_model", "anatomical");
        $this->load->model(array("Doctor_model", "User_model"));
    }

    public function get_diagrams_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $search = !empty($this->post_data['search']) ? trim($this->post_data['search']) : '';
            $category_id = !empty($this->post_data['category_id']) ? $this->post_data['category_id'] : '';
            $sub_category_id = !empty($this->post_data['sub_category_id']) ? $this->post_data['sub_category_id'] : '';
            $medsign_speciality_id = !empty($this->post_data['medsign_speciality_id']) ? $this->post_data['medsign_speciality_id'] : '';
            $page = !empty($this->post_data['page']) ? $this->post_data['page'] : 1;
            $per_page = !empty($this->post_data['per_page']) ? $this->post_data['per_page'] : 20;
            if (empty($doctor_id)) {
                $this->bad_request();
                exit;
            }
            $doctor_specialisation_edu_ids = $this->Doctor_model->get_doctor_specialisation_edu_ids($doctor_id);
            $doctor_specialisation_ids     = $doctor_specialisation_edu_ids['doctor_specialization_specialization_ids'];
            $doctor_qua_ids                = $doctor_specialisation_edu_ids['doctor_qualification_qualification_ids'];
            $where_data = array(
                'doctor_id' => $doctor_id, 
                'doctor_specialisation_ids' => $doctor_specialisation_ids, 
                'doctor_qua_ids' => $doctor_qua_ids,
                'search' => $search,
                'category_id' => $category_id,
                'medsign_speciality_id' => $medsign_speciality_id,
                'sub_category_id' => $sub_category_id,
            );
            $start = ($page - 1) * $per_page;
            $limit = $per_page;
            $diagrams = $this->anatomical->get_anatomical_diagrams($where_data, $start, $limit);
            $totals = $this->anatomical->get_anatomical_diagrams($where_data, $start, $limit, true);
            $diagrams_data = array();
            foreach ($diagrams as $key => $value) {
                $value->categories_arr = array();
                if(!empty($value->categories)) {
                    foreach (explode(',', $value->categories) as $category) {
                        list($category_name, $cat_id) = explode('##', $category);
                        $value->categories_arr[] = array('category_id' => $cat_id, 'category_name' => $category_name);
                    }
                }
                $value->is_show_image = false;
                $value->is_show_video = false;
                $value->is_show_pdf = false;
                $value->anatomical_diagrams_image_thumb_path = '';
                if(!empty($value->anatomical_diagrams_image_path)) {
                    $value->anatomical_diagrams_image_thumb_path = get_image_thumb($value->anatomical_diagrams_image_path);
                    $value->anatomical_diagrams_image_path = get_file_full_path($value->anatomical_diagrams_image_path);
                    $value->is_show_image = true;
                } elseif (!empty($value->anatomical_diagrams_video_url)) {
                    $value->is_show_video = true;
                } elseif (!empty($value->anatomical_diagrams_file_path)) {
                    $value->is_show_pdf = true;
                }
                if (!empty($value->anatomical_diagrams_file_path)) {
                    $value->anatomical_diagrams_file_path = get_file_full_path($value->anatomical_diagrams_file_path);
                }
                $diagrams_data[$key] = $value;
            }
            $this->my_response['status'] = true;
            $this->my_response['data'] = $diagrams_data;
            $this->my_response['totals'] = $totals;
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_diagrams_category_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $medsign_speciality_id = !empty($this->post_data['medsign_speciality_id']) ? $this->post_data['medsign_speciality_id'] : '';
            if (empty($doctor_id)) {
                $this->bad_request();
                exit;
            }
            $doctor_specialisation_edu_ids = $this->Doctor_model->get_doctor_specialisation_edu_ids($doctor_id);
            $doctor_specialisation_ids     = $doctor_specialisation_edu_ids['doctor_specialization_specialization_ids'];
            $doctor_qua_ids                = $doctor_specialisation_edu_ids['doctor_qualification_qualification_ids'];
            $where_data = array(
                'doctor_id' => $doctor_id, 
                'doctor_specialisation_ids' => $doctor_specialisation_ids, 
                'doctor_qua_ids' => $doctor_qua_ids,
                'medsign_speciality_id' => $medsign_speciality_id,
            );
            $categories = $this->anatomical->get_category($where_data);
            $this->my_response['status'] = true;
            $this->my_response['data'] = $categories;
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_diagrams_sub_category_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $category_id = !empty($this->post_data['category_id']) ? $this->post_data['category_id'] : '';
            $medsign_speciality_id = !empty($this->post_data['medsign_speciality_id']) ? $this->post_data['medsign_speciality_id'] : '';
            if (empty($doctor_id) || empty($category_id) || count($category_id) == 0) {
                $this->bad_request();
                exit;
            }
            $where_data = array(
                'category_id' => $category_id,
                'medsign_speciality_id' => $medsign_speciality_id
            );
            $categories = $this->anatomical->get_sub_category($where_data);
            $this->my_response['status'] = true;
            $this->my_response['data'] = $categories;
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

	public function upload_diagrams_image_post() {
        try {
            $update_data = array();
            $patient_user_id = !empty($this->post_data['patient_id']) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $clinic_id = !empty($this->post_data['clinic_id']) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $appointment_date = !empty($this->post_data['appointment_date']) ? trim($this->Common_model->escape_data($this->post_data['appointment_date'])) : '';
            $appointment_id = !empty($this->post_data['appointment_id']) ? trim($this->Common_model->escape_data($this->post_data['appointment_id'])) : '';
            $diagrams_title = !empty($this->post_data['diagrams_title']) ? trim($this->Common_model->escape_data($this->post_data['diagrams_title'])) : '';
			$user_id = $this->user_id;
            if (!empty($this->post_data['photo']) && !empty($patient_user_id) && !empty($user_id)) {
                //get the share settings for the vital
                $setting_where = array(
                    'setting_type' => 1,
                    'setting_user_id' => $doctor_id,
                    'setting_clinic_id' => $clinic_id
                );
                $get_setting = $this->Common_model->get_setting($setting_where);
                $report_share_status = 2;
                if (!empty($get_setting)) {
                    $setting_array = json_decode($get_setting['setting_data'], true);
                    if (!empty($setting_array) && is_array($setting_array)) {
                        foreach ($setting_array as $setting) {
                            if ($setting['id'] == 7) {
                                $report_share_status = $setting['status'];
                                break;
                            }
                        }
                    }
                }
                $report_data = array(
                    'file_report_user_id' => $patient_user_id,
                    'file_report_doctor_user_id' => $doctor_id,
                    'file_report_added_by_user_id' => $user_id,
                    'file_report_clinic_id' => $clinic_id,
                    'file_report_appointment_id' => $appointment_id,
                    'file_report_share_status' => $report_share_status,
                    'file_report_report_type_id' => 11, // Health Advice
                    'file_report_name' => $diagrams_title,
                    'file_report_date' => $appointment_date,
                    'file_report_created_at' => date('Y-m-d H:i:s'),
                    );
                $report_id = $this->Common_model->insert(TBL_FILE_REPORTS, $report_data);
                $objImg = imagecreatefrompng($this->post_data['photo']);
				imagealphablending($objImg, false);
				imagesavealpha($objImg, true);
				$upload_path = UPLOAD_REL_PATH . "/" . REPORT_FOLDER . "/" . $report_id ."/";
				if (!file_exists($upload_path)) {
                    mkdir($upload_path, 0777, true);
                    chmod($upload_path, 0777);
                }
				$imgFileNm = 'rx-health-advice-'.$doctor_id.'-'.$patient_user_id.'-'.date('dmYhis').'.png';
				if(imagepng($objImg, $upload_path.$imgFileNm)) {
					$new_profile_img = $imgFileNm;
                    $is_upload = upload_import_report($upload_path.$imgFileNm, REPORT_FOLDER . "/" . $report_id. "/" . $new_profile_img, 300, 170);
                    if(!IS_SERVER_UPLOAD){
                        unlink($upload_path.$imgFileNm);
                        unlink($upload_path.get_thumb_filename($imgFileNm));
                    }
                    $file_report_image_url = get_file_json_detail(REPORT_FOLDER . "/" . $report_id. "/" . $new_profile_img);
                    
                    $insert_image_array = array(
                        'file_report_image_file_report_id' => $report_id,
                        'file_report_image_url' => $file_report_image_url,
                        'report_file_size' => get_file_size(get_file_full_path($file_report_image_url)),
                        'file_report_image_created_at' => date('Y-m-d H:i:s')
                    );
                    $this->Common_model->insert(TBL_FILE_REPORTS_IMAGES, $insert_image_array);
                    delete_past_prescription($appointment_id);
					$this->my_response['status'] = true;
					$this->my_response['message'] = $new_profile_img;
				}else{
					$this->my_response['status'] = false;
                    $this->my_response['message'] = lang("user_photo_upload_fail");
				}
                
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("user_photo_upload_fail");
            }
            $this->send_response();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->store_error();
        }
    }

    public function permanent_delete_report_post() {
        try {
            $report_id = !empty($this->post_data['report_id']) ? trim($this->Common_model->escape_data($this->post_data['report_id'])) : '';
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            if (empty($doctor_id) || empty($report_id)) {
                $this->bad_request();
                exit;
            }
            $file_report_data = $this->Common_model->get_single_row(TBL_FILE_REPORTS, 'file_report_appointment_id', ['file_report_id' => $report_id]);
            $report_folder = '/'.REPORT_FOLDER.'/';
            $sql_query = "SELECT file_report_image_url FROM " . TBL_FILE_REPORTS_IMAGES . " WHERE file_report_image_status = 1 AND file_report_image_file_report_id = " . $report_id;
            $report_images = $this->Common_model->get_all_rows_by_query($sql_query);
            foreach ($report_images as $key => $value) {
                $arr = explode($report_folder, $value['file_report_image_url']);
                if(!empty($arr[1])) {
                    $image_path = REPORT_FOLDER.'/'.$arr[1];
                    $image_thumb_path = get_thumb_filename($image_path);
                    delete_file_from_s3($image_path);
                    delete_file_from_s3($image_thumb_path);
                }
            }
            $where = array(
                "file_report_image_file_report_id" => $report_id
            );
            $this->Common_model->delete_data(TBL_FILE_REPORTS_IMAGES, $where);
            $where = array(
                "file_report_id" => $report_id
            );
            $this->Common_model->delete_data(TBL_FILE_REPORTS, $where);
            if(!empty($file_report_data['file_report_appointment_id']))
                delete_past_prescription($file_report_data['file_report_appointment_id']);
            $this->my_response['status'] = true;
            $this->my_response['message'] = lang('report_deleted');;
            $this->send_response();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->store_error();
        }
    }

    public function daigrams_add_to_prescription_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $patient_id = !empty($this->post_data['patient_id']) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $appointment_id = !empty($this->post_data['appointment_id']) ? trim($this->Common_model->escape_data($this->post_data['appointment_id'])) : '';
            $document_type = !empty($this->post_data['document_type']) ? trim($this->Common_model->escape_data($this->post_data['document_type'])) : '';
            $document_url = !empty($this->post_data['document_url']) ? trim($this->Common_model->escape_data($this->post_data['document_url'])) : '';
            $document_id = !empty($this->post_data['document_id']) ? trim($this->Common_model->escape_data($this->post_data['document_id'])) : '';
            $document_is_add = !empty($this->post_data['document_is_add']) ? trim($this->Common_model->escape_data($this->post_data['document_is_add'])) : '';
            if (empty($doctor_id) || empty($patient_id) || empty($appointment_id) || empty($document_type) || empty($document_url) || empty($document_id)) {
                $this->bad_request();
                exit;
            }
            if($document_type == 2) {
                $where = [
                    'anatomical_diagrams_id' => $document_id
                ];
                $anatomical_diagram_row = $this->Common_model->get_single_row('me_anatomical_diagrams', 'anatomical_diagrams_file_path', $where);
                if($anatomical_diagram_row['anatomical_diagrams_file_path'])
                    $document_url = $anatomical_diagram_row['anatomical_diagrams_file_path'];
            }
            if(!empty($document_is_add)) {
                $insert_data = [
                    'patient_id' => $patient_id,
                    'doctor_id' => $doctor_id,
                    'appointment_id' => $appointment_id,
                    'document_id' => $document_id,
                    'document_url' => $document_url,
                    'document_type' => $document_type,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $this->Common_model->insert('me_patient_documents_shared', $insert_data);
                $message = "Document added to prescription";
            } else {
                $where = [
                    'patient_id' => $patient_id,
                    'doctor_id' => $doctor_id,
                    'appointment_id' => $appointment_id,
                    'document_id' => $document_id,
                    'document_type' => $document_type,
                ];
                $this->Common_model->delete_data('me_patient_documents_shared', $where);
                $message = "Document removed from prescription";
            }
            $this->my_response['status'] = true;
            $this->my_response['message'] = $message;
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_document_from_share_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $patient_id = !empty($this->post_data['patient_id']) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $appointment_id = !empty($this->post_data['appointment_id']) ? trim($this->Common_model->escape_data($this->post_data['appointment_id'])) : '';
            $document_type = !empty($this->post_data['document_type']) ? trim($this->Common_model->escape_data($this->post_data['document_type'])) : '';
            $document_id = !empty($this->post_data['document_id']) ? trim($this->Common_model->escape_data($this->post_data['document_id'])) : '';
            if (empty($doctor_id) || empty($patient_id) || empty($appointment_id) || empty($document_type) || empty($document_id)) {
                $this->bad_request();
                exit;
            }
            $where = [
                'patient_id' => $patient_id,
                'doctor_id' => $doctor_id,
                'appointment_id' => $appointment_id,
                'document_id' => $document_id,
                'document_type' => $document_type,
            ];
            $result = $this->Common_model->get_single_row('me_patient_documents_shared', 'id', $where);
            if(!empty($result)){
                $this->my_response['status'] = true;
                $this->my_response['data'] = $result;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['data'] = [];
            }
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }
}