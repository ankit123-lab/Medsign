<?php
/*** This controller use for Import Doctor's data */
class Doctor_data_import_part1 extends CI_Controller {

    public function __construct() {
		parent::__construct();
        $this->load->model(array("Auditlog_model", "User_model", "Doctor_import", "Import_file_mapping"));
    }

    public function index() {
    	$where = array();
    	//$where['import_file_id'] = 1;
    	$where['import_file_status'] = 2; // 2=In-progress
    	$where['status'] = 1;
    	$doctor_import_data = $this->Doctor_import->get_doctor_import_file_data_by_where($where);
    	echo "<pre>";

    	foreach ($doctor_import_data as $doctor_data) {

    		$import_file_id = $doctor_data->import_file_id;
    		$doctor_id = $doctor_data->import_file_doctor_id;
    		$columns = 'import_mapping_file_name,import_mapping_fields,import_file_is_optional,import_file_required_columns,import_file_extension';
    		$import_mapping_files = $this->Import_file_mapping->get_import_file_mapping_by_type_id($doctor_data->import_file_type_id, $columns);
    		if($doctor_data->import_file_type_id == 1) {
	    		$file_path = DOCTOR_IMPORT_FILE_PATH . $doctor_id . '/' . $import_file_id;
		    	$scanned_dir = array_diff(scandir($file_path), array('..', '.'));
		        $scanned_dir_without_ext = array();
		        $scanned_dir_with_ext = array();
		        foreach ($scanned_dir as $key => $scanned_file) {
		            $scanned_dir_without_ext[$key] = pathinfo($scanned_file, PATHINFO_FILENAME);
		            $scanned_dir_with_ext[$scanned_dir_without_ext[$key]] = $scanned_file;
		        }

		        foreach ($import_mapping_files as $key => $file) {
		        	if(empty($file->import_mapping_fields)) // || $file->import_mapping_file_name != 'Payments'
		        		continue;

		        	$import_mapping_fields = json_decode($file->import_mapping_fields);
		        	// print_r($import_mapping_fields);die;
			        $filename = $scanned_dir_with_ext[$file->import_mapping_file_name];
		        	if(!empty($filename) && file_exists($file_path . '/' . $filename)) {
		                $files_data = read_xlsx($filename,$file_path);
		                $files_header_data_key = array_flip($files_data[0]);
		                $insertData = array();
		                foreach ($files_data as $data_key => $file_data) {
		                	if($data_key == 0) //Header row skip
		                		continue;

		                	$insertArr = array();
		                	$insertArr['import_file_id'] = $doctor_data->import_file_id;
		                	$insertArr['created_at'] = date("Y-m-d H:i:s");
		            		foreach ($import_mapping_fields as $table_name => $fields) {
		            			foreach ($fields as $field_key => $field) {
		            				$column_txt = '';
		            				$column_txt_arr = array();
		            				foreach ($field->file_field as $column) {
		            					if(!empty($field->field_format) && $field->field_format == 'json') {
		            						$column_txt_arr[$column] = trim(str_replace("'", "", $file_data[$files_header_data_key[$column]]));
		            					} else {
		            						$column_txt .= ' ' .$file_data[$files_header_data_key[$column]];
		            					}
		            				}
		            				if(!empty($field->field_format) && $field->field_format == 'json') {
		            					$insertArr[$field_key] = json_encode($column_txt_arr);
		            				} else {
			            				$insertArr[$field_key] = trim(str_replace("'", "", $column_txt));
		            				}
		            			}
		            			$insertData[] = $insertArr;
		            		}
		                }
	            		// print_r($insertData);
	            		// echo "====================================><br>";
	            		// print_r($table_name);
	            		if(count($insertData) > 0 && !empty($table_name)) {
	            			$this->Common_model->create_bulk($table_name, $insertData);
	            		}
	                }
		        }
	    	} else {
	    		foreach ($import_mapping_files as $key => $file) {
		        	if(empty($file->import_mapping_fields))
		        		continue;
		        	$import_mapping_fields = json_decode($file->import_mapping_fields);
		        	// print_r($import_mapping_fields);die;
		        	$file_path = DOCTOR_IMPORT_FILE_PATH . $doctor_id;
			        $filename = $doctor_data->import_file_path;
		        	if(!empty($filename) && file_exists($file_path . '/' . $filename)) {
		                $files_data = read_xlsx($filename,$file_path, $file->import_mapping_file_name);
		                if($file->import_mapping_file_name == 'Appointments') {
		                	$files_header_data_key = array_flip($files_data[1]);
		                } else {
		                	$files_header_data_key = array_flip($files_data[0]);
		            	}
		                $insertData = array();
		                foreach ($files_data as $data_key => $file_data) {
		                	if(empty($file_data['A']) || $data_key == 0 || ($file->import_mapping_file_name == 'Appointments' && $data_key == 1)) //Header row skip
		                		continue;

		                	$insertArr = array();
		                	$insertArr['import_file_id'] = $doctor_data->import_file_id;
		                	$insertArr['created_at'] = date("Y-m-d H:i:s");
		            		foreach ($import_mapping_fields as $table_name => $fields) {
		            			foreach ($fields as $field_key => $field) {
		            				$column_txt = '';
		            				$column_txt_arr = array();
		            				foreach ($field->file_field as $column) {
		            					if(!empty($field->field_format) && $field->field_format == 'json') {
		            						$column_txt_arr[$column] = trim(str_replace("'", "", $file_data[$files_header_data_key[$column]]));
		            					} else {
		            						$column_txt .= ' ' .$file_data[$files_header_data_key[$column]];
		            					}
		            				}
		            				if(!empty($field->field_format) && $field->field_format == 'json') {
		            					$insertArr[$field_key] = json_encode($column_txt_arr);
		            				} else {
			            				$insertArr[$field_key] = trim(str_replace("'", "", $column_txt));
		            				}
		            				if($field_key == 'dob' && !empty($insertArr[$field_key])) {
		            					$dobArr = explode('-', $insertArr[$field_key]);
		            					if(count($dobArr) == 3) {
		            						$insertArr[$field_key] = $dobArr[2] . "-" . $dobArr[1] . "-" . $dobArr[0];
		            					}
		            				}
		            			}
		            			$insertData[] = $insertArr;
		            		}
		                }
	            		// print_r($insertData);
	            		// print_r($table_name);
	            		// echo "<br>====================================><br>";
	            		if(count($insertData) > 0 && !empty($table_name)) {
	            			$this->Common_model->create_bulk($table_name, $insertData);
	            		}
	                }
		        }
	    	}
	        /*Update status to Ready for import*/
            $this->Doctor_import->update_doctor_import($doctor_data->import_file_id, array('import_file_status' => 3));

            $cron_job_path = CRON_PATH . " doctor_data_import_part2/import_data_count_log/". $doctor_data->import_file_id;
            exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");
    	}

    	die('Done');
    }

}