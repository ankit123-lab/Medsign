<?php
/*** This controller use for Import Doctor's data */
class Doctor_data_import_part2 extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array("Auditlog_model", "User_model", "Doctor_import", "Import_file_mapping","Import_patients","Import_appointment","Import_clinical_notes","Import_prescription_reports","Import_invoices"));
    }

    public function index() {
        $where = array();
        // $where['import_file_id'] = 1;
        $where['import_file_status'] = 4; // 4-Importing data
        $doctor_import_data = $this->Doctor_import->get_doctor_import_file_data_by_where($where);
        echo "<pre>";
        // print_r($doctor_import_data);
        foreach ($doctor_import_data as $key => $import_data) {
            $this->_import_patients($import_data->import_file_id);
            $this->_import_appointments($import_data->import_file_id);
            $this->_import_investigations($import_data->import_file_id);
            $this->_import_clinical_notes($import_data->import_file_id);
            $this->_import_prescription_reports($import_data->import_file_id);
            $this->_import_analytics_values($import_data->import_file_id);
            $this->_import_pricing_catalog($import_data->import_file_id);
            $this->_import_invoices($import_data->import_file_id);    
            $this->_import_reports($import_data->import_file_id);    

            /*Update status to Complete*/
            $this->Doctor_import->update_doctor_import($import_data->import_file_id, array('import_file_status' => 5));
        }

        die('Done');
    }

    public function import_data_count_log($import_file_id = '') {
        if(empty($import_file_id)) {
            die('Invalid file id');
        }
        $where = array();
        $where['import_file_id'] = $import_file_id;
        
        $doctor_import_data = $this->Doctor_import->get_doctor_import_file_data_by_id($import_file_id, 'import_file_id,import_file_doctor_id,import_file_type_id');
        echo "<pre>";
        // print_r($doctor_import_data);
        

        $users = $this->_import_patients($doctor_import_data->import_file_id, true);
        $appointments = $this->_import_appointments($doctor_import_data->import_file_id, true);
        if($doctor_import_data->import_file_type_id == 1)
            $clinical_notes_count = $this->_import_clinical_notes($doctor_import_data->import_file_id, true);

        $prescription_reports_count = $this->_import_prescription_reports($doctor_import_data->import_file_id, true);
        $invoices_count = $this->_import_invoices($doctor_import_data->import_file_id, true);    
       
        $log_data = array();
        $log_data['new_users'] = $users['new_users'];
        $log_data['existing_users'] = $users['existing_users'];
        $log_data['appointments'] = $appointments['total_appointment'];
        if($doctor_import_data->import_file_type_id == 2) {
            $log_data['vitals'] = $appointments['total_vital'];
            $log_data['procedure'] = $appointments['total_procedure'];
            $log_data['clinical_notes'] = $appointments['total_clinical_notes'];
            $log_data['investigations'] = $this->_import_investigations($doctor_import_data->import_file_id, true);    
            $log_data['analytics_values'] = $this->_import_analytics_values($doctor_import_data->import_file_id, true);    
            $log_data['reports'] = $this->_import_reports($doctor_import_data->import_file_id, true);    
            
        } elseif($doctor_import_data->import_file_type_id == 1) {
            $log_data['clinical_notes'] = $clinical_notes_count;
        }
        $log_data['prescription_reports'] = $prescription_reports_count;
        $log_data['invoices_count'] = $invoices_count;

        $other_data = array('import' => 'all_data_count_log');

        $this->Auditlog_model->create_audit_log($doctor_import_data->import_file_doctor_id, 2, AUDIT_SLUG_ARR['IMPORT_DOCTOR_DATA'], array(), $log_data, TBL_DOCTOR_IMPORT_TABLE, 'import_file_id', $doctor_import_data->import_file_id, $other_data);
        // print_r($log_data);        
        die('Done');
    }


    public function _import_reports($import_file_id, $count = false) {
        $where = array('r.import_file_id' => $import_file_id);
        $columns = 'r.*,d.import_file_doctor_id,d.import_file_clinic_id,d.import_file_type_id,d.reports_zip_file_name,p.patient_original_id';
        $result = $this->Import_prescription_reports->get_reports($where, $columns);
        // print_r($result);
        if(!empty($result[0]->reports_zip_file_name) && file_exists(DOCTOR_IMPORT_FILE_PATH . $result[0]->import_file_doctor_id . '/' . $result[0]->reports_zip_file_name)) {
            $zip = new ZipArchive;
            $res = $zip->open(DOCTOR_IMPORT_FILE_PATH . $result[0]->import_file_doctor_id . '/' . $result[0]->reports_zip_file_name);
            if ($res === TRUE) {
                $file_path = DOCTOR_IMPORT_FILE_PATH . $result[0]->import_file_doctor_id . '/' . $import_file_id;
                if(!file_exists($file_path)){
                    mkdir($file_path, 0777, true);
                    chmod($file_path, 0777);
                }
                $zip->extractTo($file_path);
                $report_data = array();
                $insert_image_array = array();
                $report_next_id = $this->Import_patients->get_next_auto_id('me_files_reports');
                foreach ($result as $key => $value) {
                    if($file_source = $this->_is_file_exist($file_path, $value->report_file_name)) {
                        $is_upload = true;
                        if(!$count) {
                            $is_upload = upload_import_report($file_source, REPORT_FOLDER . "/" . $report_next_id. "/" . $value->report_file_name);
                        }
                        if($is_upload) {
                            $file_report_image_url = IMAGE_MANIPULATION_URL . REPORT_FOLDER . "/" . $report_next_id. "/" . $value->report_file_name;
                            $date_of_report = '';
                            $date_arr = explode("-", $value->date_of_report);
                            if(count($date_arr) == 3) {
                                $date_of_report = $date_arr[2].'-'.$date_arr[1].'-'.$date_arr[0];
                            }
                            $report_data[] = array(
                                'file_report_user_id' => $value->patient_original_id,
                                'file_report_doctor_user_id' => $value->import_file_doctor_id,
                                'file_report_added_by_user_id' => $value->import_file_doctor_id,
                                'file_report_clinic_id' => $value->import_file_clinic_id,
                                'file_report_report_type_id' => $this->_report_type_map(strtolower($value->type_of_report)),
                                'file_report_name' => $value->report_name,
                                'file_report_date' => $date_of_report,
                                'file_report_created_at' => date('Y-m-d H:i:s'),
                                );
                            $insert_image_array[] = array(
                                'file_report_image_file_report_id' => $report_next_id,
                                'file_report_image_url' => $file_report_image_url,
                                'report_file_size' => get_file_size($file_report_image_url),
                                'file_report_image_created_at' => date('Y-m-d H:i:s')
                            );
                            $report_next_id++;
                        }
                    }
                }
                $this->load->helper('file');
                delete_files($file_path, TRUE , false, 1);
                // print_r($report_data);
                // print_r($insert_image_array);
                if($count) {
                    return count($report_data);
                } else {
                    $this->Common_model->create_bulk('me_files_reports', $report_data);
                    $this->Common_model->create_bulk('me_files_reports_images', $insert_image_array);
                }
            }
        }
    }

    function _is_file_exist($dir, $file_name) {
        $ffs = scandir($dir);
        unset($ffs[array_search('.', $ffs, true)]);
        unset($ffs[array_search('..', $ffs, true)]);
        if (count($ffs) < 1)
            return false;
        if(file_exists($dir.'/'.$file_name))
            return $dir.'/'.$file_name;

        foreach($ffs as $ff){
            if(file_exists($dir.'/'.$ff.'/'.$file_name)){
                return $dir.'/'.$ff.'/'.$file_name;
            }
            if(is_dir($dir.'/'.$ff)) 
                $this->_is_file_exist($dir.'/'.$ff, $file_name);
            
        }
        return false;
    }
    public function _report_type_map($type) {
        $type_arr = array('pathlab' => 1,'ecg' => 2, 'xray' => 3, 'ct scan' => 4, 'mri' => 5, 'usg' => 6, 'other' => 7);
        if(!empty($type_arr[$type])) {
            return $type_arr[$type];
        }
        return 7;
    }
    public function _import_analytics_values($import_file_id, $count = false) {
        $where = array('a.import_file_id' => $import_file_id);
        $columns = 'a.*,d.import_file_doctor_id,d.import_file_clinic_id,d.import_file_type_id,p.patient_original_id';
        $result = $this->Import_prescription_reports->get_analytics_values($where, $columns);
        // print_r($result);
        $health_analytics_report = array();
        $patient_analytics = array();
        foreach($result as $key => $value) {
            $health_analytics_test = $this->Import_prescription_reports->get_health_analytics_test($value->lab_test);
            if(count($health_analytics_test) > 0) {
                $report_date = NULL;
                if(!empty($value->report_date)) {
                    $arr = explode('-', $value->report_date);
                    if(count($arr) == 3) {
                        $report_date = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
                    }
                }
                $health_analytics_report_data = array();
                $health_analytics_report_data[] = array(
                    'id' => $health_analytics_test->health_analytics_test_id,
                    'name' => $health_analytics_test->health_analytics_test_name,
                    'precise_name' => $health_analytics_test->health_analytics_test_name_precise,
                    'doctor_id' => $value->import_file_doctor_id,
                    'value' => number_format($value->reading, 2),
                    'patient_previous_health_analytic' => true
                );
                $health_analytics_report[] = array(
                    'health_analytics_report_user_id' => $value->patient_original_id,
                    'health_analytics_report_doctor_user_id' => $value->import_file_doctor_id,
                    'health_analytics_report_clinic_id' => $value->import_file_clinic_id,
                    'health_analytics_report_date' => $report_date,
                    'health_analytics_report_data' => json_encode($health_analytics_report_data),
                    'health_analytics_report_created_at' => date('Y-m-d H:i:s')
                );

                $patient_analytics[$health_analytics_test->health_analytics_test_id.'::'.$value->patient_original_id] = array(
                    'patient_analytics_user_id' => $value->patient_original_id,
                    'patient_analytics_analytics_id' => $health_analytics_test->health_analytics_test_id,
                    'patient_analytics_doctor_id' => $value->import_file_doctor_id,
                    'patient_analytics_name' => $health_analytics_test->health_analytics_test_name,
                    'patient_analytics_name_precise' => $health_analytics_test->health_analytics_test_name_precise,
                    'patient_analytics_created_at' => date('Y-m-d H:i:s'),
                    'patient_analytics_status' => 9
                );
            }
        }
        $patient_analytics = array_values($patient_analytics);
        // print_r($health_analytics_report);
        // print_r($patient_analytics);
        if($count) {
            return count($health_analytics_report);
        } else {
            $this->Common_model->create_bulk('me_health_analytics_report', $health_analytics_report);
            $this->Common_model->create_bulk('me_patient_analytics', $patient_analytics);
        }
    }
    public function _import_invoices($import_file_id, $count = false) {
        $where = array('i.import_file_id' => $import_file_id);
        $columns = 'i.*,d.import_file_doctor_id,d.import_file_clinic_id,d.selected_doctor_name,d.import_file_type_id';
        $invoices = $this->Import_invoices->get_import_invoices($where, $columns);
        /*Practo invoices code*/
        if(!empty($invoices[0]->import_file_type_id) && $invoices[0]->import_file_type_id == 1) {
            $insert_tax_catalog_arr = array();
            foreach ($invoices as $key => $value) {
                if(empty($value->tax_name)) {
                    continue;
                }
                if($value->selected_doctor_name != $value->doctor_name) {
                    continue;
                }
                $insert_tax_catalog_arr[$value->tax_name] = array(
                    'tax_name' => $value->tax_name,
                    'tax_value' => $value->tax_percent,
                    'tax_doctor_id' => $value->import_file_doctor_id,
                    'tax_created_at' => date('Y-m-d H:i:s'),
                );
            }
            $insert_tax_catalog_arr = array_values($insert_tax_catalog_arr);

            $where = array('p.import_file_id' => $import_file_id);
            $columns = 'p.*,d.import_file_doctor_id,d.import_file_clinic_id,d.selected_doctor_name';
            $payments = $this->Import_invoices->get_import_payments($where, $columns);


            if(count($invoices) == 0 || count($payments) == 0) {
                return false;
            }

            $where = array('payment_mode_doctor_id' => $invoices[0]->import_file_doctor_id,'payment_mode_status' => 1);
            $columns = 'payment_mode_id,payment_mode_name';
            $payment_mode = $this->Import_invoices->get_payment_mode($where, $columns);
            $payment_mode_name_with_id = array_column($payment_mode, 'payment_mode_id', 'payment_mode_name');

            $where = array('payment_type_status' => 1);
            $columns = 'payment_type_id,LOWER(payment_type_name) AS payment_type_name';
            $payment_types = $this->Import_invoices->get_payment_types($where, $columns);
            $payment_types_name_with_id = array_column($payment_types, 'payment_type_id', 'payment_type_name');
            
            $where = array('a.import_file_id' => $import_file_id,'a.status' => 'Scheduled');
            $columns = 'a.original_appointment_id,a.patient_unique_id,a.appointment_date,p.patient_original_id,d.import_file_doctor_id,d.import_file_clinic_id,d.selected_doctor_name';
            $appointments = $this->Import_appointment->get_import_appointment($where, $columns);
            $appointments_arr = array();
            foreach ($appointments as $key => $value) {
                $k = $value->patient_unique_id.'::'.date('Y-m-d', strtotime($value->appointment_date));
                $appointments_arr[$k] = $value;
            }

            $payments_arr = array();
            $insert_payment_mode_arr = array();
            $billing_next_id = $this->Import_patients->get_next_auto_id('me_billing');
            $payment_mode_next_id = $this->Import_patients->get_next_auto_id('me_payment_mode');
            foreach ($payments as $key => $value) {
                $k = $value->import_patient_unique_id.'::'.date('Y-m-d', strtotime($value->payment_date));
                if(empty($appointments_arr[$k])) {
                    continue;
                }
                if(array_key_exists($value->vendor_name, $payment_mode_name_with_id)) {
                    $billing_payment_mode_id = $payment_mode_name_with_id[$value->vendor_name];
                } else {
                    $billing_payment_mode_id = $payment_mode_next_id;
                    $payment_mode_name_with_id[$value->vendor_name] = $payment_mode_next_id;
                    $insert_payment_mode_arr[] = array(
                        'payment_mode_name' => $value->vendor_name,
                        'payment_mode_doctor_id' => $appointments_arr[$k]->import_file_doctor_id,
                        'payment_mode_vendor_fee' => $value->vendor_fees_percent,
                        'payment_mode_payment_type_id' => $payment_types_name_with_id[strtolower($value->payment_mode)],
                        'payment_mode_created_at' => date('Y-m-d H:i:s'),
                    );
                    $payment_mode_next_id++;
                }
                $payments_arr[$value->invoice_number] = array(
                    'billing_id' => $billing_next_id,
                    'billing_appointment_id' => $appointments_arr[$k]->original_appointment_id,
                    'billing_user_id' => $appointments_arr[$k]->patient_original_id,
                    'billing_doctor_user_id' => $appointments_arr[$k]->import_file_doctor_id,
                    'billing_clinic_id' => $appointments_arr[$k]->import_file_clinic_id,
                    'invoice_number' => $value->invoice_number,
                    'receipt_number' => $value->receipt_number,
                    'billing_payment_mode_id' => $billing_payment_mode_id,
                    'vendor_fees_percent' => $value->vendor_fees_percent,
                    'billing_created_at' => date('Y-m-d H:i:s'),
                    'billing_invoice_date' => date('Y-m-d'),
                    'billing_is_import' => $value->import_file_id,
                    'other_data' => json_encode($value)
                );
                $billing_next_id++;
            }
            
            $where = array('i.import_file_id' => $import_file_id);
            $columns = 'i.*,GROUP_CONCAT(i.tax_percent SEPARATOR ",") AS tax_group,d.import_file_doctor_id,d.import_file_clinic_id,d.selected_doctor_name';
            $invoices = $this->Import_invoices->get_import_invoices($where, $columns, true);

            $invoices_details = array();
            $billing_paid_amount_arr = array();
            $billing_grand_total_arr = array();
            $billing_detail_discount_arr = array();
            $billing_detail_tax_arr = array();
            foreach ($invoices as $key => $value) {

                if(empty($payments_arr[$value->invoice_number])) {
                    continue;
                }
                if($value->selected_doctor_name != $value->doctor_name) {
                    continue;
                }

                $billing_detail_tax = '';
                if(!empty(trim($value->tax_group))) {
                    $tax_group_arr = explode(',', $value->tax_group);
                    $billing_detail_tax = array_sum($tax_group_arr);
                }
                $billing_detail_total = $value->unit_cost;
                $billing_detail_discount_amt = 0;
                if(!empty($value->discount) && $value->discount > 0) {
                    if($value->discount_type == 'PERCENT') {
                        $billing_detail_discount_amt = $billing_detail_total * $value->discount / 100;
                        $billing_detail_total = $billing_detail_total - ($billing_detail_total * $value->discount / 100);
                    } else {
                        $billing_detail_discount_amt = $value->discount;
                        $billing_detail_total = $billing_detail_total - $value->discount;
                    }
                }
                $billing_detail_tax_amt = '';
                if(!empty($billing_detail_tax) && is_numeric($billing_detail_tax)) {
                    $billing_detail_tax_amt = $billing_detail_total * $billing_detail_tax / 100;
                    $billing_detail_total = $billing_detail_total + $billing_detail_tax_amt;
                }
                $invoices_details[] = array(
                    'billing_detail_billing_id' => $payments_arr[$value->invoice_number]['billing_id'],
                    'billing_detail_name' => $value->treatment_name,
                    'billing_detail_unit' => 1,
                    'billing_detail_basic_cost' => $value->unit_cost,
                    'billing_detail_discount' => $value->discount,
                    'billing_detail_discount_type' => ($value->discount_type == 'PERCENT') ? 1 : 2,
                    'billing_detail_total' => $billing_detail_total,
                    'billing_detail_tax' => $billing_detail_tax_amt,
                    'billing_detail_created_at' => date('Y-m-d H:i:s'),
                    'other_data' => json_encode($value)
                );
                $billing_paid_amount_arr[$payments_arr[$value->invoice_number]['billing_id']][] = $billing_detail_total;
                $billing_grand_total_arr[$payments_arr[$value->invoice_number]['billing_id']][] = $value->unit_cost;
                if(!empty($billing_detail_tax_amt)) {
                    $billing_detail_tax_arr[$payments_arr[$value->invoice_number]['billing_id']][] = $billing_detail_tax_amt;
                }

                $billing_detail_discount_arr[$payments_arr[$value->invoice_number]['billing_id']][] = $billing_detail_discount_amt;
            }

            foreach ($payments_arr as $key => $value) {
                $payments_arr[$key]['billing_grand_total'] = array_sum($billing_paid_amount_arr[$value['billing_id']]);
                $payments_arr[$key]['billing_paid_amount'] = array_sum($billing_paid_amount_arr[$value['billing_id']]);
                $payments_arr[$key]['billing_total_payable'] = array_sum($billing_paid_amount_arr[$value['billing_id']]);
                $payments_arr[$key]['billing_discount'] = array_sum($billing_detail_discount_arr[$value['billing_id']]);
                
                if($value['vendor_fees_percent']  > 0) {
                    $total_amt = $payments_arr[$key]['billing_grand_total'] + ($payments_arr[$key]['billing_grand_total'] * $value['vendor_fees_percent'] / 100);
                    
                    $payments_arr[$key]['billing_paid_amount'] = $total_amt;
                    $payments_arr[$key]['billing_total_payable'] = $total_amt;
                }
                unset($payments_arr[$key]['vendor_fees_percent']);

                $payments_arr[$key]['billing_tax'] = 0.00;
                if(is_array($billing_detail_tax_arr[$value['billing_id']]))
                    $payments_arr[$key]['billing_tax'] = array_sum($billing_detail_tax_arr[$value['billing_id']]);
            }
            /*END Practo invoices code*/
        } else {
            /*Medsign invoices code*/
            $where = array('payment_mode_doctor_id' => $invoices[0]->import_file_doctor_id,'payment_mode_status' => 1);
            $columns = 'payment_mode_id,payment_mode_name';
            $payment_mode = $this->Import_invoices->get_payment_mode($where, $columns);
            $payment_mode_name_with_id = array_column($payment_mode, 'payment_mode_id', 'payment_mode_name');

            $where = array('payment_type_status' => 1);
            $columns = 'payment_type_id,LOWER(payment_type_name) AS payment_type_name';
            $payment_types = $this->Import_invoices->get_payment_types($where, $columns);
            $payment_types_name_with_id = array_column($payment_types, 'payment_type_id', 'payment_type_name');

            $where = array('a.import_file_id' => $import_file_id);
            $columns = 'a.original_appointment_id,a.patient_unique_id,a.appointment_date,p.patient_original_id,a.appointment_from_time,p.patient_original_id';
            $appointments = $this->Import_appointment->get_import_appointment($where, $columns);
            $patient_original_id_arr = array_column($appointments, 'patient_original_id', 'original_appointment_id');
            $payments_arr = array();
            $invoices_details = array();
            $insert_payment_mode_arr = array();
            $insert_tax_catalog_arr = array();
            
            $new_invoice_arr = array();
            foreach ($invoices as $key => $value) {
                $new_invoice_arr[$value->invoice_number][] = $value;
            }
            
            $billing_next_id = $this->Import_patients->get_next_auto_id('me_billing');
            $payment_mode_next_id = $this->Import_patients->get_next_auto_id('me_payment_mode');
            foreach ($new_invoice_arr as $key => $value) {
                $appointment_id = false;
                $billing_discount = 0;
                $billing_tax = 0;
                $billing_grand_total = 0;
                $total_amount = 0;
                $invoices_details_arr = array();
                foreach ($value as $invoice_val) {
                    if(!$appointment_id) {
                        $date_arr = explode(' ',$invoice_val->appointment_date_time);
                        $appointment_date = trim($date_arr[0]);
                        $appointment_time = '';
                        if(!empty(trim($date_arr[1]))) {
                            $appointment_time = trim($date_arr[1]);
                        }
                        $appointment_id = $this->_appointment_id_map($appointments,$appointment_date,$appointment_time,$invoice_val->import_patient_unique_id);
                    }
                    if(!empty($invoice_val->discount))
                        $billing_discount += $invoice_val->discount;
                    if(!empty($invoice_val->tax_amount))
                        $billing_tax += $invoice_val->tax_amount;
                    if(!empty($invoice_val->unit_cost)) {
                        if(!empty($invoice_val->quantity)) {
                            $billing_grand_total += ($invoice_val->unit_cost * $invoice_val->quantity);
                            $billing_detail_unit = $invoice_val->quantity;
                        } else {
                            $billing_grand_total += $invoice_val->unit_cost;
                            $billing_detail_unit = 1;
                        }
                    }
                    if(!empty($invoice_val->total_amount)) {
                        $total_amount += $invoice_val->total_amount;
                    }

                    $invoices_details_arr[] = array(
                        'billing_detail_billing_id' => $billing_next_id,
                        'billing_detail_name' => $invoice_val->treatment_name,
                        'billing_detail_unit' => $billing_detail_unit,
                        'billing_detail_basic_cost' => $invoice_val->unit_cost,
                        'billing_detail_discount' => $invoice_val->discount,
                        'billing_detail_discount_type' => 2,
                        'billing_detail_total' => $invoice_val->total_amount,
                        'billing_detail_tax' => $invoice_val->tax_amount,
                        'billing_detail_created_at' => date('Y-m-d H:i:s'),
                        'other_data' => json_encode($invoice_val)
                    );
                }
                if(array_key_exists($invoice_val->payment_type, $payment_mode_name_with_id)) {
                    $billing_payment_mode_id = $payment_mode_name_with_id[$invoice_val->payment_type];
                } else {
                    $billing_payment_mode_id = $payment_mode_next_id;
                    $payment_mode_name_with_id[$invoice_val->payment_type] = $payment_mode_next_id;
                    $insert_payment_mode_arr[] = array(
                        'payment_mode_name' => $invoice_val->payment_type,
                        'payment_mode_doctor_id' => $invoice_val->import_file_doctor_id,
                        'payment_mode_vendor_fee' => '0',
                        'payment_mode_payment_type_id' => $payment_types_name_with_id[strtolower($invoice_val->payment_mode)],
                        'payment_mode_created_at' => date('Y-m-d H:i:s'),
                    );
                    $payment_mode_next_id++;
                }
                if($appointment_id && !empty($patient_original_id_arr[$appointment_id])) {
                    $invoices_details = array_merge($invoices_details, $invoices_details_arr);
                    $payments_arr[] = array(
                        'billing_appointment_id' => $appointment_id,
                        'billing_user_id' => $patient_original_id_arr[$appointment_id],
                        'billing_doctor_user_id' => $invoice_val->import_file_doctor_id,
                        'billing_clinic_id' => $invoice_val->import_file_clinic_id,
                        'billing_payment_mode_id' => $billing_payment_mode_id,
                        'billing_discount' => $billing_discount,
                        'billing_tax' => $billing_tax,
                        'billing_grand_total' => $billing_grand_total,
                        'billing_total_payable' => $total_amount,
                        'billing_paid_amount' => $total_amount,
                        'billing_created_at' => date('Y-m-d H:i:s'),
                        'billing_invoice_date' => date('Y-m-d'),
                        'invoice_number' => $key,
                        'billing_is_import' => $invoice_val->import_file_id,
                    );
                    $billing_next_id++;
                }
            }
            /*END Medsign invoices code*/
        }
        $payments_arr = array_values($payments_arr);

        // print_r($insert_payment_mode_arr);
        // print_r($insert_tax_catalog_arr);
        // print_r($payments_arr);
        // print_r($invoices_details);
        if($count) {
            return count($payments_arr);
        } else {
            $this->Common_model->create_bulk('me_payment_mode', $insert_payment_mode_arr);
            $this->Common_model->create_bulk('me_taxes', $insert_tax_catalog_arr);
            $this->Common_model->create_bulk('me_billing', $payments_arr);
            $this->Common_model->create_bulk('me_billing_details', $invoices_details);
        }
    }
    public function _import_pricing_catalog($import_file_id) {
        $where = array('pc.import_file_id' => $import_file_id);
        $columns = 'pc.*,d.import_file_doctor_id,d.import_file_clinic_id,d.selected_doctor_name';
        $result = $this->Import_invoices->get_import_pricing_catalog($where, $columns);
        if(count($result) == 0) {
            return false;
        }
        $where = array('pricing_catalog_doctor_id' => $result[0]->import_file_doctor_id);
        $columns = 'pricing_catalog_id,pricing_catalog_name';
        $pricing_catalog = $this->Import_invoices->get_pricing_catalog($where, $columns);
        $pricing_catalog_name_arr = array_column($pricing_catalog, 'pricing_catalog_name');
        $insert_pricing_catalog_array = array();
        foreach ($result as $key => $value) {
            if(in_array($value->treatment_name, $pricing_catalog_name_arr)) {
                continue;
            }
            $insert_pricing_catalog_array[] = array(
                'pricing_catalog_doctor_id' => $value->import_file_doctor_id,
                'pricing_catalog_name' => $value->treatment_name,
                'pricing_catalog_cost' => $value->treatment_cost,
                'pricing_catalog_instructions' => $value->treatment_notes,
                'pricing_catalog_created_at' => date('Y-m-d H:i:s')
            );
        }
        //print_r($insert_pricing_catalog_array);
        $this->Common_model->create_bulk('me_pricing_catalog', $insert_pricing_catalog_array);
    }

    public function _import_prescription_reports($import_file_id, $count = false) {
        $where = array('pr.import_file_id' => $import_file_id);
        $columns = 'pr.*,d.import_file_doctor_id,d.import_file_clinic_id,d.import_file_type_id,p.patient_original_id';
        $result = $this->Import_prescription_reports->get_prescription_reports($where, $columns);
        if(count($result) == 0) {
            return false;
        }

        $where = array('a.import_file_id' => $import_file_id);
        if($result[0]->import_file_type_id == 1) {
            $where['a.status'] = 'Scheduled';
        }
        $columns = 'a.original_appointment_id,a.patient_unique_id,a.appointment_from_time,a.appointment_date,p.patient_original_id,d.selected_doctor_name';
        $appointments = $this->Import_appointment->get_import_appointment($where, $columns);
        $new_appointments = array();
        foreach ($appointments as $key => $value) {
            $k = $value->patient_unique_id.'::'.date('Y-m-d', strtotime($value->appointment_date));
            $new_appointments[$k] = $value;
        }

        $setting_where = array(
            'setting_type' => 1,
            'setting_user_id' => $result[0]->import_file_doctor_id,
            'setting_clinic_id' => $result[0]->import_file_clinic_id
        );
        $get_setting = $this->Common_model->get_setting($setting_where);
        $report_share_status = 2;
        if (!empty($get_setting)) {
            $setting_array = json_decode($get_setting['setting_data'], true);
            if (!empty($setting_array) && is_array($setting_array)) {
                foreach ($setting_array as $setting) {
                    if ($setting['id'] == 3) {
                        $report_share_status = $setting['status'];
                        break;
                    }
                }
            }
        }

        $prescription_duration_arr = array('day(s)' => 1, 'week(s)' => 2, 'month(s)' => 3);
        $prescription_reports_array = array();
        foreach ($result as $key => $value) {
            /*Practo prescription code*/
            if($value->import_file_type_id == 1) {
                $k = $value->patient_unique_id.'::'.date('Y-m-d', strtotime($value->prescription_date));
                if(empty($new_appointments[$k])) {
                    continue;
                }
                if(!empty($value->before_food)) {
                    $prescription_intake = 1;
                } elseif(empty($value->after_food)) {
                    $prescription_intake = 2;
                } else {
                    $prescription_intake = 5; //As Directed
                }

                $prescription_frequency_id = 9; // SOS
                $prescription_frequency_value = '';
                if(!empty($value->morning) && !empty($value->afternoon) && !empty($value->night)) {
                    $prescription_frequency_id = 5;
                    $prescription_frequency_value = '1-1-1';
                } elseif((!empty($value->morning) && !empty($value->afternoon)) || (!empty($value->morning) && !empty($value->night)) ||  (!empty($value->afternoon) && !empty($value->night))) {
                    $prescription_frequency_id = 4;
                    $prescription_frequency_value = '1-0-1';
                } elseif(!empty($value->morning)) {
                    $prescription_frequency_value = '1-0-0';
                    $prescription_frequency_id = 1;
                } elseif(!empty($value->afternoon)) {
                    $prescription_frequency_value = '0-1-0';
                    $prescription_frequency_id = 2;
                } elseif(!empty($value->night)) {
                    $prescription_frequency_value = '0-0-1';
                    $prescription_frequency_id = 3;
                }

                $prescription_reports_array[] = array(
                    'prescription_user_id' => $new_appointments[$k]->patient_original_id,
                    'prescription_doctor_user_id' => $value->import_file_doctor_id,
                    'prescription_appointment_id' => $new_appointments[$k]->original_appointment_id,
                    'prescription_clinic_id' => $value->import_file_clinic_id,
                    'prescription_date' => date('Y-m-d', strtotime($value->prescription_date)),
                    'prescription_drug_name' => $value->drug_name,
                    'prescription_drug_name_with_unit' => $value->drug_name . ' (' . $value->drug_type . ')',
                    'prescription_unit_value' => $value->dosage . ' ' . $value->dosage_unit,
                    'prescription_intake_instruction' => $value->instruction,
                    'prescription_intake' => $prescription_intake,
                    'prescription_frequency_id' => $prescription_frequency_id,
                    'prescription_frequency_value' => $prescription_frequency_value,
                    'prescription_dosage' => $value->tablet_per_day,
                    'prescription_duration' => $prescription_duration_arr[$value->duration_unit],
                    'prescription_duration_value' => preg_replace('~\.0+$~','',$value->duration),
                    'prescription_share_status' => $report_share_status,
                    'prescription_created_at' => date('Y-m-d H:i:s'),
                    'prescription_is_import' => $value->import_file_id,
                    'prescription_status' => 1,
                    'prescription_other_data' => json_encode($value)
                );
                /*END Practo prescription code*/
            } else {
                /*MedSign prescription code*/
                // print_r($value);
                $prescription_date_arr = explode(' ',$value->prescription_date);
                $appointment_date = trim($prescription_date_arr[0]);
                $appointment_time = '';
                if(!empty(trim($prescription_date_arr[1]))) {
                    $appointment_time = trim($prescription_date_arr[1]);
                }
                
                $appointment_id = $this->_appointment_id_map($appointments,$appointment_date,$appointment_time,$value->patient_unique_id);
                $appointment_date_db = '';
                if(!empty($appointment_date)) {
                    $date_arr = explode('-', $appointment_date);
                    if(count($date_arr) == 3) {
                        $appointment_date_db = $date_arr[2] . '-' . $date_arr[1] . '-' . $date_arr[0];
                    }
                }
                $drug_data = $this->Import_prescription_reports->search_drug($value->drug_name,$value->drug_type);
                if(count($drug_data) == 0) {
                    $drug_data = $this->Import_prescription_reports->create_drug($value);
                }
                $prescription_reports_array[] = array(
                    'prescription_user_id' => $value->patient_original_id,
                    'prescription_doctor_user_id' => $value->import_file_doctor_id,
                    'prescription_appointment_id' => $appointment_id,
                    'prescription_clinic_id' => $value->import_file_clinic_id,
                    'prescription_drug_id' => !empty($drug_data[0]->drug_id) ? $drug_data[0]->drug_id : NULL,
                    'prescription_date' => $appointment_date_db,
                    'prescription_drug_name' => $value->drug_name,
                    'prescription_drug_name_with_unit' => !empty($drug_data[0]->drug_name_with_unit) ? $drug_data[0]->drug_name_with_unit : NULL,
                    'prescription_generic_id' => !empty($drug_data[0]->drug_drug_generic_id) ? $drug_data[0]->drug_drug_generic_id : NULL,
                    'prescription_unit_id' => !empty($drug_data[0]->drug_drug_unit_id) ? $drug_data[0]->drug_drug_unit_id : NULL,
                    'prescription_unit_value' => !empty($drug_data[0]->drug_unit_name) ? $drug_data[0]->drug_unit_name : NULL,
                    'prescription_dosage' => (empty($value->dosage) || $value->dosage == '0') ? '-' : $value->dosage,
                    'prescription_frequency_id' => $this->_get_frequency_id(strtolower($value->frequency)),
                    'prescription_frequency_value' => $this->_get_frequency_value(strtolower($value->frequency)),
                    'prescription_frequency_instruction' => $value->frequency_instruction,
                    'prescription_intake' => $this->_get_prescription_intake(strtolower($value->intake)),
                    'prescription_intake_instruction' => $value->instruction,
                    'prescription_duration' => 1,
                    'prescription_duration_value' => $value->duration,
                    'prescription_diet_instruction' => $value->diet_instruction,
                    'prescription_share_status' => $report_share_status,
                    'prescription_created_at' => date('Y-m-d H:i:s'),
                    'prescription_is_import' => $value->import_file_id,
                    'prescription_status' => 1,
                    'prescription_other_data' => json_encode($value)
                );
                /*END MedSign prescription code*/
            }
        }
        // print_r($prescription_reports_array);
        if($count) {
            return count($prescription_reports_array);
        } else {
            $this->Common_model->create_bulk('me_prescription_reports', $prescription_reports_array);
        }

    }
    function _get_prescription_intake($val) {
        $arr = array('before food'=>1,'after food'=>2,'along with food'=>3,'empty stomach'=>4,'as directed'=>5);
        if(!empty($arr[$val])) {
            return $arr[$val];
        }
        return 10;
    }
    function _get_frequency_id($val) {
        $arr = array('once in morning'=>1,'once in afternoon'=>2,'once in evening'=>3,'twice in a day'=>4,'thrice in a day'=>5,'four times in a day'=>6,'once in week'=>7,'once in month'=>8,'sos'=>9,'other'=>10);
        if(!empty($arr[$val])) {
            return $arr[$val];
        }
        return 10;
    }
    function _get_frequency_value($val) {
        $frequency_id = $this->_get_frequency_id($val);
        $frequency_value = '';
        if($frequency_id == 1) {
            $frequency_value = '1-0-0';
        }elseif($frequency_id == 2) {
            $frequency_value = '0-1-0';
        }elseif($frequency_id == 3) {
            $frequency_value = '0-0-1';
        }elseif($frequency_id == 4) {
            $frequency_value = '1-0-1';
        }elseif($frequency_id == 5) {
            $frequency_value = '1-1-1';
        }
        return $frequency_value;
    }
    public function _import_clinical_notes($import_file_id, $count = false) {

        $where = array('cn.import_file_id' => $import_file_id);
        $columns = 'cn.patient_unique_id,cn.clinical_notes_date,cn.patient_name,cn.doctor_name,cn.notes_type,cn.description,d.import_file_doctor_id,d.import_file_clinic_id,d.selected_doctor_name';
        $result = $this->Import_clinical_notes->get_clinical_notes($where, $columns);
        if(count($result) == 0) {
            return false;
        }

        $setting_where = array(
            'setting_type' => 1,
            'setting_user_id' => $result[0]->import_file_doctor_id,
            'setting_clinic_id' => $result[0]->import_file_clinic_id
        );
        $get_setting = $this->Common_model->get_setting($setting_where);

        $report_share_status = 2;

        if (!empty($get_setting)) {
            $setting_array = json_decode($get_setting['setting_data'], true);
            if (!empty($setting_array) && is_array($setting_array)) {
                foreach ($setting_array as $setting) {
                    if ($setting['id'] == 2) {
                        $report_share_status = $setting['status'];
                        break;
                    }
                }
            }
        }

        $where = array('a.import_file_id' => $import_file_id,'a.status' => 'Scheduled');
        $columns = 'a.original_appointment_id,a.patient_unique_id,a.doctor_name,a.appointment_date,p.patient_original_id,d.selected_doctor_name';
        $appointments = $this->Import_appointment->get_import_appointment($where, $columns);
        $new_appointments = array();
        foreach ($appointments as $key => $value) {
            if($value->selected_doctor_name == $value->doctor_name) {
                $k = $value->patient_unique_id.'::'.date('Y-m-d', strtotime($value->appointment_date));
                $new_appointments[$k] = $value;
            }
        }

        $clinical_notes_array = array();
        $type_map_field_arr = array('complaints' => 'clinical_notes_reports_complaints', 'observations' => 'clinical_notes_reports_observation', 'diagnoses' => 'clinical_notes_reports_diagnoses', 'treatmentnotes' => 'clinical_notes_reports_add_notes');
        foreach ($result as $key => $value) {
            $k = $value->patient_unique_id.'::'.date('Y-m-d', strtotime($value->clinical_notes_date));
            if(empty($new_appointments[$k])) {
                continue;
            }
            if($value->selected_doctor_name != $value->doctor_name) {
                continue;
            }
            $clinical_notes_array[$k]['clinical_notes_reports_user_id'] = $new_appointments[$k]->patient_original_id;
            $clinical_notes_array[$k]['clinical_notes_reports_date'] = date('Y-m-d', strtotime($value->clinical_notes_date));
            $clinical_notes_array[$k]['clinical_notes_reports_share_status'] = $report_share_status;
            $clinical_notes_array[$k]['clinical_notes_reports_doctor_user_id'] = $value->import_file_doctor_id;
            $clinical_notes_array[$k]['clinical_notes_reports_appointment_id'] = $new_appointments[$k]->original_appointment_id;
            $clinical_notes_array[$k]['clinical_notes_reports_clinic_id'] = $value->import_file_clinic_id;
            $clinical_notes_array[$k]['clinical_notes_reports_created_at'] = date('Y-m-d H:i:s');

            if(empty($clinical_notes_array[$k][$type_map_field_arr[$value->notes_type]])) {

                $descArr = explode('\n', $value->description);
                $clinical_notes_array[$k][$type_map_field_arr[$value->notes_type]] = json_encode(array_filter($descArr,'strlen'));
            } else {
                $descArr = explode('\n', $value->description);
                $descArr = array_merge(json_decode($clinical_notes_array[$k][$type_map_field_arr[$value->notes_type]]),$descArr);
                $clinical_notes_array[$k][$type_map_field_arr[$value->notes_type]] = json_encode(array_filter($descArr,'strlen'));
            }

        }

        $clinical_notes_array = array_values($clinical_notes_array);

        foreach ($clinical_notes_array as $key => $value) {
            if(empty($value['clinical_notes_reports_kco'])) {
                $clinical_notes_array[$key]['clinical_notes_reports_kco'] = '[]';
            }
            if(empty($value['clinical_notes_reports_complaints'])) {
                $clinical_notes_array[$key]['clinical_notes_reports_complaints'] = '[]';
            }
            if(empty($value['clinical_notes_reports_observation'])) {
                $clinical_notes_array[$key]['clinical_notes_reports_observation'] = '[]';
            }
            if(empty($value['clinical_notes_reports_diagnoses'])) {
                $clinical_notes_array[$key]['clinical_notes_reports_diagnoses'] = '[]';
            }
            if(empty($value['clinical_notes_reports_add_notes'])) {
                $clinical_notes_array[$key]['clinical_notes_reports_add_notes'] = '[]';
            }
        }

        if($count) {
            return count($clinical_notes_array);
        } else {
            $this->Common_model->create_bulk('me_clinical_notes_reports', $clinical_notes_array);
        }

    }

    public function _import_investigations($import_file_id, $count = false) {
        $where = array('i.import_file_id' => $import_file_id);
        $columns = 'i.*,p.patient_original_id,d.import_file_doctor_id,d.import_file_clinic_id,d.import_file_type_id';
        $result = $this->Import_appointment->get_import_investigations($where, $columns);
        // print_r($result);
        $investigationsData = array();
        if(count($result) > 0) {
            $new_array = array();
            foreach ($result as $key => $value) {
                $arr_key = $value->patient_unique_id.'::'.$value->appointment_date;
                if(!empty($value->appointment_time)) {
                    $arr_key .= '::' . $value->appointment_time;
                }
                $new_array[$arr_key]['import_file_id'] = $value->import_file_id;
                $new_array[$arr_key]['patient_unique_id'] = $value->patient_unique_id;
                $new_array[$arr_key]['appointment_date'] = $value->appointment_date;
                $new_array[$arr_key]['appointment_time'] = $value->appointment_time;
                $new_array[$arr_key]['patient_original_id'] = $value->patient_original_id;
                $new_array[$arr_key]['import_file_doctor_id'] = $value->import_file_doctor_id;
                $new_array[$arr_key]['import_file_clinic_id'] = $value->import_file_clinic_id;
                $new_array[$arr_key]['import_file_type_id'] = $value->import_file_type_id;
                $new_array[$arr_key]['lab_report_test_name'][$value->lab_test] = $value->instructions;
                
            }
            $where = array('a.import_file_id' => $import_file_id);
            $columns = 'a.original_appointment_id,a.patient_unique_id,a.appointment_date,a.appointment_from_time,p.patient_original_id,d.import_file_type_id';
            $appointments = $this->Import_appointment->get_import_appointment($where, $columns);
            foreach ($new_array as $key => $value) {
                $appointment_date = '';
                if(!empty($value['appointment_date'])) {
                    $date_arr = explode('-', $value['appointment_date']);
                    if(count($date_arr) == 3) {
                        $appointment_date = $date_arr[2] . '-' . $date_arr[1] . '-' . $date_arr[0];
                    }
                }
                $appointment_id = $this->_appointment_id_map($appointments,$value['appointment_date'],$value['appointment_time'],$value['patient_unique_id']);
                $investigationsData[] = array(
                    'lab_report_user_id' => $value['patient_original_id'],
                    'lab_report_doctor_user_id' => $value['import_file_doctor_id'],
                    'lab_report_appointment_id' => $appointment_id,
                    'lab_report_clinic_id' => $value['import_file_clinic_id'],
                    'lab_report_date' => $appointment_date,
                    'lab_report_test_name' => json_encode($value['lab_report_test_name']),
                    'lab_report_created_at' => date('Y-m-d H:i:s')
                );
            }
            /*Merge lab test data if appointment id same*/
            $final_arr = array();
            foreach ($investigationsData as $key => $value) {
                if(!empty($final_arr[$value['lab_report_appointment_id']])) {
                    $merge_arr = array_merge(json_decode($final_arr[$value['lab_report_appointment_id']]['lab_report_test_name'],true), json_decode($value['lab_report_test_name'], true));
                    $final_arr[$value['lab_report_appointment_id']]['lab_report_test_name'] = json_encode($merge_arr);
                } else {
                    $final_arr[$value['lab_report_appointment_id']] = $value;
                }
            }
        }
        $final_arr = array_values($final_arr);
        // print_r($final_arr);
        if($count) {
            return count($final_arr);
        } else {
            $this->Common_model->create_bulk('me_lab_reports', $final_arr);
        }
    }

    function _appointment_id_map($appointments,$appointment_date,$appointment_time,$patient_unique_id) {
        $appointment_id = 0;
        $first_match_appointment_id = 0;
        foreach ($appointments as $key => $value) {

            if($value->appointment_date == $appointment_date && $value->patient_unique_id == $patient_unique_id && $first_match_appointment_id == 0) {
                $first_match_appointment_id = $value->original_appointment_id;
            }
            if($value->appointment_date == $appointment_date && $value->patient_unique_id == $patient_unique_id && $appointment_time == $value->appointment_from_time) {
                $appointment_id = $value->original_appointment_id;
                break;
            }
        }
        if($appointment_id > 0) {
            return $appointment_id;
        } else {
            return $first_match_appointment_id;
        }
    }

    public function _import_appointments($import_file_id, $count = false) {
        $where = array('a.import_file_id' => $import_file_id);
        $columns = 'a.import_file_id,a.patient_appointment_id,a.patient_unique_id,a.appointment_date,a.patient_name,a.doctor_name,a.status,a.other_data,a.appointment_from_time,a.appointment_to_time,p.patient_original_id,d.import_file_doctor_id,d.import_file_clinic_id,d.selected_doctor_name,d.import_file_type_id';
        $result = $this->Import_appointment->get_import_appointment($where, $columns);
        if(count($result) > 0) {
            $appointment_next_id = $this->Import_patients->get_next_auto_id('me_appointments');
            $duration = 0;
            if(!empty($result[0]->import_file_doctor_id) && !empty($result[0]->import_file_clinic_id)) {
                $where = array('doctor_clinic_mapping_user_id' => $result[0]->import_file_doctor_id , 'doctor_clinic_mapping_clinic_id' => $result[0]->import_file_clinic_id);
                $doctor_clinic_mapping = $this->Import_appointment->get_doctor_clinic_mapping($where, 'doctor_clinic_mapping_duration');
                if(!empty($doctor_clinic_mapping->doctor_clinic_mapping_duration)) {
                    $duration = $doctor_clinic_mapping->doctor_clinic_mapping_duration;
                }
            }

            $appointmentData = array();
            $vitalData = array();
            $proceduresData = array();
            $clinicalNotesReportsData = array();
            $updateImportAppointment = array();
            $appointment_id_date_arr = array();
            foreach ($result as $key => $value) {
                if($value->import_file_type_id == 1) {
                    /*Practo appointment code*/
                    if($value->status == 'Scheduled') {
                        $appointment_date = date('Y-m-d H:i:s', strtotime($value->appointment_date));
                        $appointment_id_date = $value->patient_unique_id . '_' . date('Y-m-d', strtotime($appointment_date));
                        if(in_array($appointment_id_date, $appointment_id_date_arr)) {
                            continue;
                        }
                        if($value->selected_doctor_name != $value->doctor_name) {
                            continue;
                        }
                        $appointment_id_date_arr[] = $appointment_id_date;
                        $appointmentData[] = array(
                            "appointment_user_id" => $value->patient_original_id,
                            "appointment_doctor_user_id" => $value->import_file_doctor_id,
                            "appointment_clinic_id" => $value->import_file_clinic_id,
                            "appointment_type" => 1,
                            "appointment_from_time" => date('H:i', strtotime($appointment_date)),
                            "appointment_to_time" => date('H:i', strtotime('+' . $duration . ' minutes', strtotime($appointment_date))),
                            "appointment_date" => date('Y-m-d', strtotime($appointment_date)),
                            "appointment_booked_by" => $value->import_file_doctor_id,
                            "appointment_created_at" => date('Y-m-d H:i:s'),
                            'appointment_is_import' => $value->import_file_id
                        );
                        $updateImportAppointment[] = array(
                            'patient_appointment_id' => $value->patient_appointment_id,
                            'original_appointment_id' => $appointment_next_id
                        );
                        $appointment_next_id++;
                    }
                    /*END Practo appointment code*/
                } else {
                    /*MedSign appointment code*/
                    $other_data = array();
                    if(!empty($value->other_data)) {
                        $other_data = json_decode($value->other_data, true);
                    }
                    $appointment_date = '';
                    if(!empty($value->appointment_date)) {
                        $date_arr = explode('-', $value->appointment_date);
                        if(count($date_arr) == 3) {
                            $appointment_date = $date_arr[2] . '-' . $date_arr[1] . '-' . $date_arr[0];
                        }
                    }
                    $appointmentData[] = array(
                        "appointment_user_id" => $value->patient_original_id,
                        "appointment_doctor_user_id" => $value->import_file_doctor_id,
                        "appointment_clinic_id" => $value->import_file_clinic_id,
                        "appointment_type" => 1,
                        "appointment_from_time" => $value->appointment_from_time,
                        "appointment_to_time" => $value->appointment_to_time,
                        "appointment_date" => $appointment_date,
                        "appointment_booked_by" => $value->import_file_doctor_id,
                        "appointment_created_at" => date('Y-m-d H:i:s'),
                        'appointment_is_import' => $value->import_file_id
                    );
                    /*Vital Data insert code*/
                    if(!empty($other_data['Weight(kg)']) || !empty($other_data['Pulse(Rate/Min)']) || !empty($other_data['Resp.(Rate/Min)']) || !empty($other_data['Sp02(%)']) || !empty($other_data['Bloodpressure systolic(mm Hg)']) || !empty($other_data['Bloodpressure diastolic(mm Hg)']) || !empty($other_data['Temperature'])) {
                        $bloodpressure_type = 1;
                        if(!empty($other_data['Bloodpressure type'])) {
                            $bloodpressure_type = $this->_get_blood_pressure_type(strtolower($other_data['Bloodpressure type']));
                        }
                        $temperature_type = 1;
                        if(!empty($other_data['Temperature type'])) {
                            $temperature_type = $this->_get_temperature_type(strtolower($other_data['Temperature type']));
                        }
                        $temperature_taken = 6;
                        if(!empty($other_data['Temperature taken'])) {
                            $temperature_taken = $this->_get_temperature_taken(strtolower($other_data['Temperature taken']));
                        }
                        $temperature = NULL;
                        if($temperature_type == 2 && !empty($other_data['Temperature'])) {
                            $temperature = number_format(($other_data['Temperature'] * 1.8) + 32, 2);
                        } elseif(!empty($other_data['Temperature'])) {
                            $temperature = number_format($other_data['Temperature'], 2);
                        }
                        $vitalData[] = array(
                            'vital_report_user_id' => $value->patient_original_id,
                            'vital_report_doctor_id' => $value->import_file_doctor_id,
                            'vital_report_appointment_id' => $appointment_next_id,
                            'vital_report_clinic_id' => $value->import_file_clinic_id,
                            'vital_report_date' => $appointment_date,
                            'vital_report_weight' => !empty($other_data['Weight(kg)']) ? $other_data['Weight(kg)'] * 2.20462 : '', //kgToPound
                            'vital_report_pulse' => !empty($other_data['Pulse(Rate/Min)']) ? $other_data['Pulse(Rate/Min)'] : '',
                            'vital_report_resp_rate' => !empty($other_data['Resp.(Rate/Min)']) ? $other_data['Resp.(Rate/Min)'] : '',
                            'vital_report_spo2' => !empty($other_data['Sp02(%)']) ? $other_data['Sp02(%)'] : '',
                            'vital_report_bloodpressure_systolic' => !empty($other_data['Bloodpressure systolic(mm Hg)']) ? $other_data['Bloodpressure systolic(mm Hg)'] : '',
                            'vital_report_bloodpressure_diastolic' => !empty($other_data['Bloodpressure diastolic(mm Hg)']) ? $other_data['Bloodpressure diastolic(mm Hg)'] : '',
                            'vital_report_bloodpressure_type' => $bloodpressure_type,
                            'vital_report_temperature' => $temperature,
                            'vital_report_temperature_type' => $temperature_type,
                            'vital_report_temperature_taken' => $temperature_taken,
                            'vital_report_created_at' => date('Y-m-d H:i:s')
                        );
                    }
                    /*END Vital Data insert code*/
                    /*Procedure Data insert code*/
                    if(!empty($other_data['Procedures (Required)'])) {
                        $proceduresData[] = array(
                            'procedure_report_user_id' => $value->patient_original_id,
                            'procedure_report_doctor_user_id' => $value->import_file_doctor_id,
                            'procedure_report_appointment_id' => $appointment_next_id,
                            'procedure_report_clinic_id' => $value->import_file_clinic_id,
                            'procedure_report_date' => $appointment_date,
                            'procedure_report_procedure_text' => json_encode(explode(",", $other_data['Procedures (Required)'])),
                            'procedure_report_note' => !empty($other_data['Procedures Note']) ? $other_data['Procedures Note'] : '',
                            'procedure_report_created_at' => date('Y-m-d H:i:s')
                        );
                    }
                    /*END Procedure Data insert code*/
                    /*Clinical notes data insert code*/
                    if(!empty($other_data['K/C/O']) || !empty($other_data['Chief Complaints']) || !empty($other_data['Add Observations']) || !empty($other_data['Add Diagnosis'])) {
                        $clinicalNotesReportsData[] = array(
                            'clinical_notes_reports_user_id' => $value->patient_original_id,
                            'clinical_notes_reports_doctor_user_id' => $value->import_file_doctor_id,
                            'clinical_notes_reports_appointment_id' => $appointment_next_id,
                            'clinical_notes_reports_clinic_id' => $value->import_file_clinic_id,
                            'clinical_notes_reports_date' => $appointment_date,
                            'clinical_notes_reports_kco' => !empty($other_data['K/C/O']) ? json_encode(explode(",", $other_data['K/C/O'])) : '[]',
                            'clinical_notes_reports_complaints' => !empty($other_data['Chief Complaints']) ? json_encode(explode(",", $other_data['Chief Complaints'])) : '[]',
                            'clinical_notes_reports_observation' => !empty($other_data['Add Observations']) ? json_encode(explode(",", $other_data['Add Observations'])) : '[]',
                            'clinical_notes_reports_diagnoses' => !empty($other_data['Add Diagnosis']) ? json_encode(explode(",", $other_data['Add Diagnosis'])) : '[]',
                            'clinical_notes_reports_add_notes' => !empty($other_data['Add Notes']) ? json_encode(explode(",", $other_data['Add Notes'])) : '[]',
                            'clinical_notes_reports_created_at' => date('Y-m-d H:i:s')
                        );
                    }
                    /*END Clinical notes data insert code*/
                    $updateImportAppointment[] = array(
                        'patient_appointment_id' => $value->patient_appointment_id,
                        'original_appointment_id' => $appointment_next_id
                    );
                    $appointment_next_id++;
                    /*END MedSign appointment code*/
                }
            }
            /*print_r($appointmentData);
            print_r($vitalData);
            print_r($proceduresData);
            print_r($clinicalNotesReportsData);
            print_r($updateImportAppointment);*/
            $this->Doctor_import->bulk_update('me_import_appointment', $updateImportAppointment,'patient_appointment_id');
            if($count) {
                return array(
                            'total_appointment' => count($appointmentData),
                            'total_vital' => count($vitalData),
                            'total_procedure' => count($proceduresData),
                            'total_clinical_notes' => count($clinicalNotesReportsData),
                        );
            } else {
               $this->Common_model->create_bulk('me_appointments', $appointmentData);
               $this->Common_model->create_bulk('me_vital_reports', $vitalData);
               $this->Common_model->create_bulk('me_procedure_reports', $proceduresData);
               $this->Common_model->create_bulk('me_clinical_notes_reports', $clinicalNotesReportsData);
            }
        }

    }

    function _get_blood_pressure_type($val) {
        $arr = array('sitting' => 1, 'standing' => 2);
        if(!empty($arr[$val])) {
            return $arr[$val];
        }
        return 1;
    }
    function _get_temperature_type($val) {
        $arr = array('fahrenhite' => 1, 'celcius' => 2);
        if(!empty($arr[$val])) {
            return $arr[$val];
        }
        return 1;
    }
    function _get_temperature_taken($val) {
        $arr = array('axillary' => 1, 'oral' => 2, 'tympanic' => 3, 'temporal' => 4, 'rectal' => 5, 'digital' => 6);
        if(!empty($arr[$val])) {
            return $arr[$val];
        }
        return 6;
    }

    public function _import_patients($import_file_id, $count = false) {
        $where = array('p.import_file_id' => $import_file_id);
        $columns = 'p.*,d.import_file_doctor_id,d.selected_doctor_name,d.import_file_type_id,count(patient_id) as total';
        $result = $this->Import_patients->get_import_patients($where, $columns);
        $mobile_number_arr = array();
        $email_arr = array();
        foreach ($result as $key => $value) {
            if(!empty($value->mobile_number))
                $mobile_number_arr[] = substr($value->mobile_number,-10);

            if(!empty($value->email_address))
                $email_arr[] = $value->email_address;

        }
        $languages = $this->Import_patients->get_languages();
        $languages_arr = array_column($languages, 'language_id', 'language_name');
        $existing_users = $this->Import_patients->get_existing_users($mobile_number_arr, $email_arr, 'user_id,user_phone_number,user_email');
        $mobile_number_key_with_id = array_column($existing_users, 'user_id','user_phone_number');
        $email_key_with_id = array_column($existing_users, 'user_id','user_email');

        $userData = array();
        $userAddress = array();
        $userDetails = array();
        $insert_setting_array = array();
        $insert_family_history_array = array();
        $user_auth_insert_array = array();
        $update_import_patient_array = array();
        $mobile_with_name_arr = array();
        $email_with_name_arr = array();
        $family_member_mapping_array = array();
        $user_next_id = $this->Import_patients->get_next_auto_id('me_users');
        $cities = $this->Import_patients->get_city();
        $existing_users_arr = array();

        foreach ($result as $key => $value) {
            $mobile_number = substr($value->mobile_number,-10);
            $other_data = array();
            if(!empty($value->other_data)) {
                $other_data = json_decode($value->other_data, true);
            }

            if((empty($mobile_number_key_with_id[$mobile_number]) && empty($email_key_with_id[$value->email_address])) || array_key_exists($mobile_number, $mobile_with_name_arr) || array_key_exists($value->email_address, $email_with_name_arr)) {

                $nameArr = explode(' ', $value->patient_name, 2);
                $user_first_name = '';
                $user_last_name = '';
                if(!empty($nameArr[0])) {
                    $user_first_name = $nameArr[0];
                }

                if(!empty($nameArr[1])) {
                    $user_last_name = $nameArr[1];
                }

                if(empty($value->gender)) {
                    $gender = 'undisclosed';
                } else {
                    if($value->gender == 'F') {
                        $gender = 'female';
                    } elseif ($value->gender == 'M') {
                        $gender = 'male';
                    }
                }
                $user_phone_number = $mobile_number; 
                $user_email = $value->email_address; 
                if(array_key_exists($mobile_number, $mobile_with_name_arr)) {
                    
                    if(strtolower($mobile_with_name_arr[$mobile_number]['pname']) == strtolower($value->patient_name)) {
                        continue;
                    } else {
                        $user_phone_number = '';
                        if(strtolower($mobile_with_name_arr[$mobile_number]['email']) == strtolower($value->email_address)) {
                            $user_email = '';
                        }

                        /*Family Member mapping array*/
                        $family_member_mapping_array[] = array(
                            'parent_patient_id' => $mobile_with_name_arr[$mobile_number]['user_id'],
                            'patient_id' => $user_next_id,
                            // 'mapping_relation' => 1,
                            'mapping_status' => 1,
                            'created_at' => date('Y-m-d H:i:s')
                        );
                    }
                } elseif(array_key_exists($value->email_address, $email_with_name_arr)){
                    if(strtolower($email_with_name_arr[$value->email_address]['pname']) == strtolower($value->patient_name)) {
                        continue;
                    } else {
                        $user_email = '';
                        if($email_with_name_arr[$value->email_address]['mobile'] == $mobile_number) {
                            $user_phone_number = '';
                        }

                        /*Family Member mapping array*/
                        $family_member_mapping_array[] = array(
                            'parent_patient_id' => $email_with_name_arr[$value->email_address]['user_id'],
                            'patient_id' => $user_next_id,
                            // 'mapping_relation' => 1,
                            'mapping_status' => 1,
                            'created_at' => date('Y-m-d H:i:s')
                        );
                    }
                }
                $unique_id = strtoupper($this->Common_model->escape_data(str_rand_access_token(8)));
                $userData[] = array(
                    'user_first_name' => $user_first_name,
                    'user_last_name' => $user_last_name,
                    'user_phone_number' => $user_phone_number,
                    'user_email' => $user_email,
                    'user_unique_id' => $unique_id,
                    'user_patient_id' => (!empty($value->patient_unique_id)) ? $value->patient_unique_id : NULL,
                    'user_gender' => $gender,
                    'user_created_at' => date('Y-m-d H:i:s'),
                    'user_is_import' => $value->import_file_id,
                    'user_source_id' => $value->import_file_doctor_id,
                );

                $city_key = array_search($value->city_name, array_column($cities, 'city_name'));
                $city_id = '';
                $city_state_id = '';
                if(!empty($city_key)) {
                    $city_id = $cities[$city_key]['city_id'];
                    $city_state_id = $cities[$city_key]['city_state_id'];
                }
                $landmark = NULL;
                if(!empty($other_data['Landmark'])) {
                    $landmark = $other_data['Landmark'];
                }
                $userAddress[] = array(
                    'address_user_id' => $user_next_id, 
                    'address_type' => 1,
                    'address_name_one' => $value->address, 
                    'address_name' => $landmark, 
                    'address_city_id' => $city_id,
                    'address_state_id' => $city_state_id,
                    'address_country_id' => 101,
                    'address_pincode' => $value->pincode,
                    'address_locality' => $value->locality,
                    'address_created_at' => date('Y-m-d H:i:s'),
                );
                $emergency_contact_number = '';
                if(!empty($value->contact_number)) {
                    $emergency_contact_number = $value->contact_number;
                } elseif(!empty($value->secondary_mobile)) {
                    $emergency_contact_number = $value->secondary_mobile;
                }
                $user_details_languages_known = NULL;
                if(!empty($other_data['Language Preference'])) {
                    $lan_arr = explode(",", $other_data['Language Preference']);
                    $lan_id_arr = array();
                    foreach ($lan_arr as $key => $lan_val) {
                        if(!empty($languages_arr[$lan_val])) {
                            $lan_id_arr[] = $languages_arr[$lan_val];
                        }
                    }
                    if(count($lan_id_arr) > 0) {
                        $user_details_languages_known = implode(',', $lan_id_arr);
                    } else {
                        $user_details_languages_known = '1';
                    }
                }
                $user_details_height = NULL;
                if(!empty($other_data['Height (cm)'])) {
                    $user_details_height = $other_data['Height (cm)'];
                }
                $user_details_weight = NULL;
                if(!empty($other_data['Weight (kg)'])) {
                    $user_details_weight = $other_data['Weight (kg)'];
                }
                $user_details_occupation = NULL;
                if(!empty($other_data['Occupation'])) {
                    $user_details_occupation = $other_data['Occupation'];
                }
                $food_allergies = NULL;
                if(!empty($other_data['Food allergies'])) {
                    $food_allergies = $other_data['Food allergies'];
                }
                $medicine_allergies = NULL;
                if(!empty($other_data['Medical allergies'])) {
                    $medicine_allergies = $other_data['Medical allergies'];
                }
                $other_allergies = NULL;
                if(!empty($other_data['Others allergies'])) {
                    $other_allergies = $other_data['Others allergies'];
                }
                $user_details_injuries = NULL;
                if(!empty($other_data['Injuries'])) {
                    $user_details_injuries = $other_data['Injuries'];
                }
                $user_details_surgeries = NULL;
                if(!empty($other_data['Surgeries'])) {
                    $user_details_surgeries = $other_data['Surgeries'];
                }
                $user_details_smoking_habbit = NULL;
                if(!empty($other_data['Smoking habits'])) {
                    $user_details_smoking_habbit = $this->_get_smoking_habits(strtolower($other_data['Smoking habits']));
                }
                $user_details_alcohol = NULL;
                if(!empty($other_data['Alcohol consumption'])) {
                    $user_details_alcohol = $this->_get_alcohol(strtolower($other_data['Alcohol consumption']));
                }
                $user_details_activity_level = NULL;
                if(!empty($other_data['Activity Level'])) {
                    $user_details_activity_level = $this->_get_activity_level(strtolower($other_data['Activity Level']));
                }
                $food_preference = NULL;
                if(!empty($other_data['Food preference'])) {
                    $food_preference = $this->_get_food_preference(strtolower($other_data['Food preference']));
                }
                $userDetails[] = array(
                    'user_details_user_id' => $user_next_id,
                    'user_details_dob' => (!empty($value->dob)) ? date("Y-m-d", strtotime($value->dob)) : '1988-01-01',
                    'user_details_agree_medical_share' => ($gender == 'undisclosed') ? 1 : 2,
                    'user_details_blood_group' => $value->blood_group,
                    'user_details_languages_known' => $user_details_languages_known,
                    'user_details_height' => $user_details_height,
                    'user_details_weight' => $user_details_weight,
                    'user_details_occupation' => $user_details_occupation,
                    'user_details_emergency_contact_number' => $emergency_contact_number,
                    'user_details_chronic_diseases' => $value->chronic_diseases,
                    'user_details_food_allergies' => $food_allergies,
                    'user_details_medicine_allergies' => $medicine_allergies,
                    'user_details_other_allergies' => $other_allergies,
                    'user_details_injuries' => $user_details_injuries,
                    'user_details_surgeries' => $user_details_surgeries,
                    'user_details_smoking_habbit' => $user_details_smoking_habbit,
                    'user_details_alcohol' => $user_details_alcohol,
                    'user_details_activity_level' => $user_details_activity_level,
                    'user_details_food_preference' => $food_preference,
                    'user_details_created_at' => date('Y-m-d H:i:s')
                );
                if(!empty($other_data['Family health history'])) {
                    try {
                        $family_health_history = json_decode($other_data['Family health history'], true);
                        foreach ($family_health_history as $relation => $health_value) {
                            $insert_family_history_array[] = array(
                                'family_medical_history_user_id' => $user_next_id,
                                'family_medical_history_medical_condition_id' => $health_value,
                                'family_medical_history_relation' => $this->_relation_map(strtolower($relation)),
                                'family_medical_history_created_at' => date('Y-m-d H:i:s')
                            );                        
                        }
                    } catch (Exception $e) {

                    }
                }
                $setting_array = array();
                $setting_array[] = array(
                    'id' => "1",
                    'name' => "data security",
                    'status' => "1"
                );
                $insert_setting_array[] = array(
                    'setting_user_id' => $user_next_id,
                    'setting_clinic_id' => '',
                    'setting_data' => json_encode($setting_array),
                    'setting_type' => 2,
                    'setting_data_type' => 1,
                    'setting_created_at' => date("Y-m-d H:i:s")
                );

                $user_auth_insert_array[] = array(
                    'auth_user_id' => $user_next_id,
                    'auth_type' => 2,
                    'auth_code' => '',
                    'auth_otp_expiry_time' => '',
                    'auth_created_at' => date("Y-m-d H:i:s")
                );
                $update_import_patient_array[] = array(
                    'patient_id' => $value->patient_id,
                    'patient_original_id' => $user_next_id,
                );
                $user_id = $user_next_id;
                $user_next_id++;
            } else {
                $user_id = '';
                if(!empty($mobile_number_key_with_id[$mobile_number])){
                    $update_import_patient_array[] = array(
                        'patient_id' => $value->patient_id,
                        'patient_original_id' => $mobile_number_key_with_id[$mobile_number],
                    );
                    $user_id = $mobile_number_key_with_id[$mobile_number];
                } elseif(!empty($email_key_with_id[$value->email_address])){
                    $update_import_patient_array[] = array(
                        'patient_id' => $value->patient_id,
                        'patient_original_id' => $email_key_with_id[$value->email_address],
                    );
                    $user_id = $email_key_with_id[$value->email_address];
                }
                $existing_users_arr[] = $user_id;
            }
            if(!empty($mobile_number) && !array_key_exists($mobile_number, $mobile_with_name_arr)) {
                $mobile_with_name_arr[$mobile_number]['pname'] = $value->patient_name;
                $mobile_with_name_arr[$mobile_number]['email'] = $value->email_address;
                $mobile_with_name_arr[$mobile_number]['user_id'] = $user_id;
            } 
            if(!empty($value->email_address) && !array_key_exists($value->email_address, $email_with_name_arr)) {
                $email_with_name_arr[$value->email_address]['pname'] = $value->patient_name;
                $email_with_name_arr[$value->email_address]['mobile'] = $mobile_number;
                $email_with_name_arr[$value->email_address]['user_id'] = $user_id;
            }
        }
        // echo "<br>User data<br>";
        // print_r($userData);  
        // echo "<br>Family member<br>";
        // print_r($family_member_mapping_array);   
  //       echo "<br>Family history<br>";
  //       print_r($insert_family_history_array);  
        // echo "<br>Update patient data<br>";
        // print_r($update_import_patient_array);   
        // echo "<br>User Address<br>";
        // print_r($userAddress);
        // echo "<br>User details<br>"; 
        // print_r($userDetails);
        // echo "<br>User setting<br>"; 
        // print_r($insert_setting_array);  
        // echo "<br>User auth<br>";
        // print_r($user_auth_insert_array);    

        $this->Doctor_import->bulk_update('me_import_patients', $update_import_patient_array,'patient_id');
        if($count) {
            return array('new_users' => count($userData) , 'existing_users' => count($existing_users_arr));
        } else {
            $this->Common_model->create_bulk('me_users', $userData);

            $this->Common_model->create_bulk('me_patient_family_member_mapping', $family_member_mapping_array);
            $this->Common_model->create_bulk('me_address', $userAddress);
            $this->Common_model->create_bulk('me_user_details', $userDetails);
            $this->Common_model->create_bulk('me_settings', $insert_setting_array);
            $this->Common_model->create_bulk('me_auth', $user_auth_insert_array);
            $this->Common_model->create_bulk('me_family_medical_history', $insert_family_history_array);
        }
    }

    function _relation_map($val) {
        $relation_arr = array(
            'mother' => 1,
            'father' => 2,
            'brother' => 3,
            'sister' => 4,
            'wife' => 5,
            'son' => 6,
            'daughter' => 7,
            'husband' => 8
        );
        if(!empty($relation_arr[$val])) {
            return $relation_arr[$val];
        }
        return 0;
    }
    function _get_smoking_habits($val) {
        $smoking_habits_arr = array(
            "don't smoke" => 1,
            "quit" => 2,
            "1/2 day" => 3,
            "3/5 day" => 4,
            "5/10 Day" => 5,
            "greater than 10" => 6
        );
        if(!empty($smoking_habits_arr[$val])) {
            return $smoking_habits_arr[$val];
        }
        return 0;
    }
    function _get_alcohol($val) {
        $alcohol_arr = array(
            "non drinker" => 1,
            "rare case drink" => 2,
            "social" => 3,
            "regular" => 4,
            "heavy" => 5,
            "quit drinking" => 6
        );
        if(!empty($alcohol_arr[$val])) {
            return $alcohol_arr[$val];
        }
        return 0;
    }
    function _get_activity_level($val) {
        $activity_level_arr = array(
            "cycling" => 1,
            "walking" => 2,
            "running" => 3,
            "cardio" => 4,
            "exercise/yoga" => 5,
            "other" => 6
        );
        if(!empty($activity_level_arr[$val])) {
            return $activity_level_arr[$val];
        }
        return 0;
    }
    function _get_food_preference($val) {
        $food_preference_arr = array(
            "vegetrian" => 1,
            "non vegetrian" => 2,
            "eggetrian" => 3,
            "vegan" => 4
        );
        if(!empty($food_preference_arr[$val])) {
            return $food_preference_arr[$val];
        }
        return 0;
    }

}