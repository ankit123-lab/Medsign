<?php
/*** This controller use for Import Doctor's data */
class Doctor_data_import extends MY_Controller {

    public function __construct() {
		parent::__construct();
        $this->load->model(array("Auditlog_model", "User_model", "Doctor_import", "Import_file_mapping"));
    }

    public function upload_doctor_import_file_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? $this->post_data['doctor_id'] : "";
            $clinic_id = !empty($this->post_data['clinic_id']) ? $this->post_data['clinic_id'] : "";
            $import_file_type_id = !empty($this->post_data['import_file_type_id']) ? $this->post_data['import_file_type_id'] : "";
            if (empty(trim($doctor_id)) || empty(trim($import_file_type_id)) || empty(trim($clinic_id)) || empty(trim($_FILES['import_file']['name']))) {
                $this->bad_request();
                exit;
            }
            $upload_path = DOCTOR_IMPORT_FILE_PATH . $doctor_id . '/';
            if(!file_exists(DOCTOR_IMPORT_FILE_PATH)) {
                mkdir(DOCTOR_IMPORT_FILE_PATH, 0777, true);
                chmod(DOCTOR_IMPORT_FILE_PATH, 0777);
            }
            if(!file_exists($upload_path)) {
                mkdir($upload_path, 0777, true);
                chmod($upload_path, 0777);
            }
            $config['upload_path'] = $upload_path;
            if($import_file_type_id == 1) {
                $config['allowed_types'] = 'zip';
            } else {
                $config['allowed_types'] = 'xlsx';
            }
            $config['file_name'] = uniqid();
            $this->load->library('upload', $config);
            $is_error = false;
            if (!$this->upload->do_upload('import_file')) {
                $error = $this->upload->display_errors('', '');
                $is_error = true;
                $this->my_response['status'] = false;
                $this->my_response['message'] = $error;
                $this->send_response();
            } else {
                $upload_data = $this->upload->data();
            }
            if(!empty($_FILES['reports_file']['name']) && $import_file_type_id == 2) {
                $report_config['upload_path'] = $upload_path;
                $report_config['allowed_types'] = 'zip';
                $report_config['file_name'] = uniqid();
                $this->upload->initialize($report_config);
                $this->load->library('upload', $report_config);
                if (!$this->upload->do_upload('reports_file')) {
                    $error = $this->upload->display_errors('', '');
                    $is_error = true;
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = 'Report File: '.$error;
                    $this->send_response();
                } else {
                    $report_upload_data = $this->upload->data();
                }
            }


            if(!$is_error) {
                $insertData = array(
                    'import_file_doctor_id' => $doctor_id,
                    'import_file_clinic_id' => $clinic_id,
                    'import_file_name' => $upload_data['client_name'],
                    'import_file_path' => $upload_data['file_name'],
                    'import_file_type_id' => $import_file_type_id,
                    'import_file_size' => $upload_data['file_size'],
                    'import_file_status' => 1,
                    'import_created_at' => $this->utc_time_formated,
                    'import_created_by' => $this->user_id,
                );
                if(!empty($report_upload_data['file_name'])) {
                    $insertData['reports_zip_file_name'] = $report_upload_data['file_name'];
                }
                if(!empty($report_upload_data['file_size'])) {
                    $insertData['reports_zip_file_size'] = $report_upload_data['file_size'];
                }
                $this->Doctor_import->create_doctor_import($insertData);
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('doctor_import_file');
                $this->send_response();
            }
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->store_error();
        }
    }

    public function get_doctor_import_files_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? $this->post_data['doctor_id'] : "";
            $clinic_id = !empty($this->post_data['clinic_id']) ? $this->post_data['clinic_id'] : "";
            
            if (empty(trim($doctor_id)) || empty(trim($clinic_id))) {
                $this->bad_request();
                exit;
            }
            $where = array('import_file_doctor_id' => $doctor_id, 'import_file_clinic_id' => $clinic_id, 'status' => 1);
            $columns = 'd.import_file_id,d.import_file_name,d.import_file_status,ift.import_file_type_name';
            $file_data = $this->Doctor_import->get_doctor_import_file_data($where, $columns);
            $this->my_response['status'] = true;
            $this->my_response['data'] = $file_data;
            $this->send_response();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->store_error();
        }
    }

    public function get_import_file_type_post() {
        try {
            
            $where = array('import_file_type_status' => 1);
            $columns = 'import_file_type_id,import_file_type_name';
            $import_file_type = $this->Doctor_import->get_import_file_type($where, $columns);
            
            $this->my_response['status'] = true;
            $this->my_response['data'] = $import_file_type;
            $this->send_response();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->store_error();
        }
    }

    public function validate_doctor_import_file_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? $this->post_data['doctor_id'] : "";
            $user_id = !empty($this->post_data['user_id']) ? $this->post_data['user_id'] : "";
            $import_file_id = !empty($this->post_data['import_file_id']) ? $this->post_data['import_file_id'] : "";
            if (empty(trim($doctor_id)) || empty($user_id) || empty($user_id)) {
                $this->bad_request();
                exit;
            }

            $doctor_import_data = $this->Doctor_import->get_doctor_import_file_data_by_id($import_file_id);
            if(!empty($doctor_import_data) && $doctor_import_data->import_file_type_id == 1) {
	            if(file_exists(DOCTOR_IMPORT_FILE_PATH . $doctor_id . '/' . $doctor_import_data->import_file_path)) {
	            	$zip = new ZipArchive;
	            	$res = $zip->open(DOCTOR_IMPORT_FILE_PATH . $doctor_id . '/' . $doctor_import_data->import_file_path);
	            	if ($res === TRUE) {
                        $file_path = DOCTOR_IMPORT_FILE_PATH . $doctor_id . '/' . $import_file_id;
	            		if(!file_exists($file_path))
	            			mkdir($file_path, 0777, true);

			            chmod($file_path, 0777);
                		$zip->extractTo($file_path);
                        $columns = 'import_mapping_file_name,import_mapping_fields,import_file_is_optional,import_file_required_columns,import_file_extension';
	            		$import_mapping_files = $this->Import_file_mapping->get_import_file_mapping_by_type_id($doctor_import_data->import_file_type_id, $columns);

                        $scanned_dir = array_diff(scandir($file_path), array('..', '.'));
                        $scanned_dir_without_ext = array();
                        $scanned_dir_with_ext = array();
                        foreach ($scanned_dir as $key => $scanned_file) {
                            $scanned_dir_without_ext[$key] = pathinfo($scanned_file, PATHINFO_FILENAME);
                            $scanned_dir_with_ext[$scanned_dir_without_ext[$key]] = $scanned_file;
                        }
                        
                        $validate_files = array();
                        $is_all_files_validated = true;
                        foreach ($import_mapping_files as $key => $file) {

                            $validate_files[$key]['file_name'] = $file->import_mapping_file_name;
                            $validate_files[$key]['is_optional'] = $file->import_file_is_optional;
                            $validate_files[$key]['is_validated'] = true;
                            $filename = $scanned_dir_with_ext[$file->import_mapping_file_name];

                            /*Check required files exist or not*/
                            if(pathinfo($filename, PATHINFO_EXTENSION) != $file->import_file_extension) {
                                $validate_files[$key]['is_validated'] = false;
                                $validate_files[$key]['msg'] = lang('invalid_file');
                                $is_all_files_validated = false;
                            } elseif(!in_array($file->import_mapping_file_name, $scanned_dir_without_ext) && $file->import_file_is_optional != 1) {
                                $validate_files[$key]['is_validated'] = false;
                                $validate_files[$key]['msg'] = 'File not exist';
                                $is_all_files_validated = false;
                            } else {
                                
                                if(!empty($filename) && file_exists($file_path . '/' . $filename)) {
                                    /*Read data from CSV*/
                                    $file_data = read_xlsx($filename,$file_path);

                                    if($file->import_mapping_file_name == 'Appointments') {
                                        $doctor_name_arr = array();
                                        foreach ($file_data as $dataKey => $file_data_row) {
                                            if($dataKey > 0)
                                                $doctor_name_arr[] = trim(str_replace("'", "", $file_data_row['H']));;
                                        }
                                    }
                                    /*Validate fields*/
                                    if(count($file_data) > 0) {
                                        $header_row = $file_data[0];
                                        if(!empty($file->import_file_required_columns)) {
                                            foreach (json_decode($file->import_file_required_columns) as $field_name) {
                                                if(!in_array($field_name, $header_row)) {
                                                    $validate_files[$key]['is_validated'] = false;
                                                    $validate_files[$key]['msg'] = lang('invalid_file');
                                                    $is_all_files_validated = false;
                                                    break;
                                                }
                                            }

                                            /*Check rows exist in file*/
                                            if(count($file_data) < 2) {
                                                $validate_files[$key]['is_validated'] = false;
                                                $validate_files[$key]['msg'] = lang('invalid_file');
                                                $is_all_files_validated = false;
                                            }

                                        }
                                    } else {
                                        $validate_files[$key]['is_validated'] = false;
                                        $validate_files[$key]['msg'] = lang('invalid_file');
                                        $is_all_files_validated = false;
                                    }
                                    /*END Validate fields*/
                                }
                            }

                        }
                        $other_data = array('status_id' => 1, 'status' => 'Validate');
                        $this->Auditlog_model->create_audit_log($doctor_id, 2, AUDIT_SLUG_ARR['IMPORT_DOCTOR_DATA'], array(), $validate_files, TBL_DOCTOR_IMPORT_TABLE, 'import_file_id', $import_file_id, $other_data);
                        if($is_all_files_validated) {
                            $updateData = array();
                            $doctor_name_arr = array_values(array_unique($doctor_name_arr));
                            $updateData['file_doctor_name_data'] = json_encode($doctor_name_arr);
                            if(count($doctor_name_arr) == 1) {
                                $updateData['selected_doctor_name'] = $doctor_name_arr[0];
                                $updateData['import_file_status'] = 2;
                                $this->my_response['is_doctor_selection'] = false;
                            } else {
                                $this->my_response['doctor_name_arr'] = $doctor_name_arr;
                                $this->my_response['is_doctor_selection'] = true;
                            }

                        } else {
                            $updateData = array(
                                'import_file_status' => 6
                            );
                        }
                        $updateData['import_updated_at'] = $this->utc_time_formated;
                        $updateData['import_updated_by'] = $this->user_id;
                        $this->Doctor_import->update_doctor_import($import_file_id,$updateData);

                        $this->my_response['status'] = true;
                        $this->my_response['import_file_id'] = $import_file_id;
                        $this->my_response['message'] = lang('common_detail_found');
                        $this->my_response['data'] = $validate_files;
                        $this->send_response();
            		} else {
            			$this->my_response['status'] = false;
                        $this->my_response['message'] = lang('zip_file_not_open');
                        $this->send_response();
            		}
	            } else {
	            	$this->my_response['status'] = false;
                    $this->my_response['message'] = lang('missing_import_file');
                    $this->send_response();
	            }
	        } elseif(!empty($doctor_import_data) && $doctor_import_data->import_file_type_id == 2) {
                $file_path = DOCTOR_IMPORT_FILE_PATH . $doctor_id;
                $filename = $doctor_import_data->import_file_path;
                $columns = 'import_mapping_file_name,import_mapping_fields,import_file_is_optional,import_file_required_columns,import_file_extension';
                $import_mapping_files = $this->Import_file_mapping->get_import_file_mapping_by_type_id($doctor_import_data->import_file_type_id, $columns);

                
                $validate_files = array();
                $is_all_files_validated = true;
                foreach ($import_mapping_files as $key => $file) {

                    $validate_files[$key]['file_name'] = $file->import_mapping_file_name;
                    $validate_files[$key]['is_optional'] = $file->import_file_is_optional;
                    $validate_files[$key]['is_validated'] = true;
                    // $filename = $scanned_dir_with_ext[$file->import_mapping_file_name];

                    /*Check required files exist or not*/
                    $file_data = read_xlsx($filename,$file_path, $file->import_mapping_file_name);
                    // print_r($file_data);die;
                    if(empty($file_data[0]['A']) && $file->import_file_is_optional != 1) {
                        $validate_files[$key]['is_validated'] = false;
                        $validate_files[$key]['msg'] = 'Sheet not exist';
                        $is_all_files_validated = false;
                    } else {
                        /*Validate fields*/
                        if(count($file_data) > 0) {
                            if($file->import_mapping_file_name == 'Appointments') {
                                $header_row = $file_data[1];
                            } else {
                                $header_row = $file_data[0];
                            }
                            if(!empty($file->import_file_required_columns)) {
                                foreach (json_decode($file->import_file_required_columns) as $field_name) {
                                    if(!in_array($field_name, $header_row)) {
                                        $validate_files[$key]['is_validated'] = false;
                                        $validate_files[$key]['msg'] = lang('invalid_file');
                                        $is_all_files_validated = false;
                                        break;
                                    }
                                }

                                /*Check rows exist in file*/
                                if(count($file_data) < 2 && $file->import_file_is_optional != 1) {
                                    $validate_files[$key]['is_validated'] = false;
                                    $validate_files[$key]['msg'] = lang('invalid_file');
                                    $is_all_files_validated = false;
                                }

                            }
                        } elseif($file->import_file_is_optional != 1) {
                            $validate_files[$key]['is_validated'] = false;
                            $validate_files[$key]['msg'] = lang('invalid_file');
                            $is_all_files_validated = false;
                        }
                        /*END Validate fields*/
                    }

                }
                $other_data = array('status_id' => 1, 'status' => 'Validate');
                $this->Auditlog_model->create_audit_log($doctor_id, 2, AUDIT_SLUG_ARR['IMPORT_DOCTOR_DATA'], array(), $validate_files, TBL_DOCTOR_IMPORT_TABLE, 'import_file_id', $import_file_id, $other_data);
                if($is_all_files_validated) {
                    $updateData = array(
                        'import_file_status' => 2
                    );
                    $this->my_response['is_doctor_selection'] = false;
                } else {
                    $updateData = array(
                        'import_file_status' => 6
                    );
                }
                $updateData['import_updated_at'] = $this->utc_time_formated;
                $updateData['import_updated_by'] = $this->user_id;
                $this->Doctor_import->update_doctor_import($import_file_id,$updateData);

                $this->my_response['status'] = true;
                $this->my_response['import_file_id'] = $import_file_id;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $validate_files;
                $this->send_response();
                // print_r($file_data);
                // die('Medsign');
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('invalid_import_file_id');
                $this->send_response();
	        }
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->store_error();
        }
    }

    public function doctor_name_selection_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? $this->post_data['doctor_id'] : "";
            $clinic_id = !empty($this->post_data['clinic_id']) ? $this->post_data['clinic_id'] : "";
            $import_file_id = !empty($this->post_data['import_file_id']) ? $this->post_data['import_file_id'] : "";
            $selected_doctor_name = !empty($this->post_data['selected_doctor_name']) ? $this->post_data['selected_doctor_name'] : "";
            if (empty(trim($doctor_id)) || empty(trim($import_file_id)) || empty(trim($clinic_id)) || empty(trim($selected_doctor_name))) {
                $this->bad_request();
                exit;
            }
            $updateData = array(
                'selected_doctor_name' => $selected_doctor_name,
                'import_file_status' => 2
            );

            $this->Doctor_import->update_doctor_import($import_file_id,$updateData);
            $this->my_response['status'] = true;
            $this->send_response();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->store_error();
        }
    }

    public function ready_for_import_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? $this->post_data['doctor_id'] : "";
            $clinic_id = !empty($this->post_data['clinic_id']) ? $this->post_data['clinic_id'] : "";
            $import_file_id = !empty($this->post_data['import_file_id']) ? $this->post_data['import_file_id'] : "";
            
            if (empty(trim($doctor_id)) || empty(trim($import_file_id)) || empty(trim($clinic_id))) {
                $this->bad_request();
                exit;
            }
            $updateData = array(
                'import_file_status' => 4 // 4-Importing data
            );
            $other_data = array('status_id' => 3, 'status' => 'Ready for import');
            $this->Auditlog_model->create_audit_log($doctor_id, 2, AUDIT_SLUG_ARR['IMPORT_DOCTOR_DATA'], array(), array(), TBL_DOCTOR_IMPORT_TABLE, 'import_file_id', $import_file_id, $other_data);

            $this->Doctor_import->update_doctor_import($import_file_id,$updateData);
            $this->my_response['status'] = true;
            $this->send_response();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->store_error();
        }
    }

    public function get_import_log_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? $this->post_data['doctor_id'] : "";
            $import_file_id = !empty($this->post_data['import_file_id']) ? $this->post_data['import_file_id'] : "";
            $search = !empty($this->post_data['search']) ? $this->post_data['search'] : "";
            
            if (empty(trim($doctor_id)) || empty(trim($import_file_id))) {
                $this->bad_request();
                exit;
            }
            $where = array(
                'action_slug_name' => 'import_doctor_data',
                'user_id' => $doctor_id,
                'user_type' => 2,
                'table_name' => 'me_doctor_import',
                'table_primary_key_value' => $import_file_id,
            );
            $columns = 'table_new_value';
            $result = $this->Auditlog_model->get_audit_log($where,$columns, $search);
            if(count($result) > 0) {
                $this->my_response['data'] = json_decode($result[0]->table_new_value);
            } else {
                $this->my_response['data'] = array();
            }
            // die('asd');
            $this->my_response['status'] = true;
            $this->send_response();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->store_error();
        }
    }

}