<?php
class Drug_update extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->helper('download');
    }
    
    public function create_drug_company_name() {
        $sql_query = "SELECT id,drug_company_name,drug_company_id FROM drugs_data 
        WHERE drug_company_id=0
        ";
        $drug_company_next_id = $this->get_next_auto_id("me_drug_company_name");
        $result = $this->Common_model->get_all_rows_by_query($sql_query);
        // echo "<pre>";
        // print_r($result);die;
        $company_name_with_id_arr = [];
        $drug_data_update_query = "";
        $insert_drug_company_data_query = "";
        $created_at = date('Y-m-d H:i:s');
        foreach ($result as $key => $value) {
            if(empty($company_name_with_id_arr[strtolower(trim($value['drug_company_name']))])){

                $drug_data_update_query .= "UPDATE drugs_data SET drug_company_id=".$drug_company_next_id." WHERE id=".$value['id'].";\n";
                
                $insert_drug_company_data_query .= "INSERT INTO me_drug_company_name (drug_company_name,created_at) values('".trim($value['drug_company_name'])."','".$created_at."');\n";
                $company_name_with_id_arr[strtolower(trim($value['drug_company_name']))] = $drug_company_next_id;
                $drug_company_next_id++;                
            } elseif(!empty($company_name_with_id_arr[strtolower(trim($value['drug_company_name']))])){
                $company_id = $company_name_with_id_arr[strtolower(trim($value['drug_company_name']))];
                $drug_data_update_query .= "UPDATE drugs_data SET drug_company_id=".$company_id." WHERE id=".$value['id'].";\n";
            }
        }
        force_download('me_drug_company_name.sql', "START TRANSACTION;\n".$insert_drug_company_data_query.$drug_data_update_query."COMMIT;");
        die();
    }

    public function update_drug_company_name() {
        $sql_query = "SELECT dd.id,dd.drug_company_name,dd.drug_company_id,dc.drug_company_name AS old_company_name FROM drugs_data dd JOIN me_drug_company_name dc ON dc.drug_company_id=dd.drug_company_id AND dc.drug_company_name!=dd.drug_company_name 
        WHERE dd.drug_company_id>0 group by dd.drug_company_id
        ";
        $result = $this->Common_model->get_all_rows_by_query($sql_query);
        // echo "<pre>";
        // print_r($result);die;
        $update_drug_company_data_query = "";
        $updated_at = date('Y-m-d H:i:s');
        foreach ($result as $key => $value) {
            $update_drug_company_data_query .= "UPDATE me_drug_company_name SET drug_company_name='".trim($value['drug_company_name'])."',updated_at='".$updated_at."' WHERE drug_company_id=".$value['drug_company_id']."\n";
        }
        force_download('me_drug_company_name_update.sql', "START TRANSACTION;\n".$update_drug_company_data_query."COMMIT;");
        die();
    }

    public function create_drug_generic() {
        // echo "<pre>";
        $sql_query = "SELECT id,Drug_1,Drug_2,Drug_3,Drug_4 FROM drugs_data";
        $result = $this->Common_model->get_all_rows_by_query($sql_query);
        $generic_query = "SELECT drug_generic_id,LOWER(drug_generic_title) AS drug_generic_title FROM me_drug_generic
        WHERE drug_generic_status=1";
        $generic_data = $this->Common_model->get_all_rows_by_query($generic_query);
        $generic_data_arr = array_column($generic_data,'drug_generic_id','drug_generic_title');
        // print_r($generic_data_arr);die;
        $drug_company_next_id = $this->get_next_auto_id("me_drug_generic");
        // echo $drug_company_next_id;die;
        // print_r($result);die;
        $drug_data_update_query = "";
        $insert_drug_generic_data_query = "";
        $drug_generic_created_at = date('Y-m-d H:i:s');
        foreach ($result as $key => $value) {
            $drug_generic_ids = "";
            if(!empty(trim($value['Drug_1']))) {
                if(empty($generic_data_arr[strtolower(trim($value['Drug_1']))])) {
                    $insert_drug_generic_data_query .= "INSERT INTO me_drug_generic (drug_generic_title,drug_generic_created_at) values('".trim($value['Drug_1'])."','".$drug_generic_created_at."');\n";
                    $generic_data_arr[strtolower(trim($value['Drug_1']))] = $drug_company_next_id;
                    $drug_generic_ids .= $drug_company_next_id.',';
                    $drug_company_next_id++;                
                } elseif(!empty($generic_data_arr[strtolower(trim($value['Drug_1']))])){
                    $drug_generic_ids .= $generic_data_arr[strtolower(trim($value['Drug_1']))].',';
                }
            }

            if(!empty(trim($value['Drug_2']))) {
                if(empty($generic_data_arr[strtolower(trim($value['Drug_2']))])) {
                    $insert_drug_generic_data_query .= "INSERT INTO me_drug_generic (drug_generic_title,drug_generic_created_at) values('".trim($value['Drug_2'])."','".$drug_generic_created_at."');\n";
                    $generic_data_arr[strtolower(trim($value['Drug_2']))] = $drug_company_next_id;
                    $drug_generic_ids .= $drug_company_next_id.',';
                    $drug_company_next_id++;                
                } elseif(!empty($generic_data_arr[strtolower(trim($value['Drug_2']))])){
                    $drug_generic_ids .= $generic_data_arr[strtolower(trim($value['Drug_2']))].',';
                }
            }
            if(!empty(trim($value['Drug_3']))) {
                if(empty($generic_data_arr[strtolower(trim($value['Drug_3']))])) {
                    $insert_drug_generic_data_query .= "INSERT INTO me_drug_generic (drug_generic_title,drug_generic_created_at) values('".trim($value['Drug_3'])."','".$drug_generic_created_at."');\n";
                    $generic_data_arr[strtolower(trim($value['Drug_3']))] = $drug_company_next_id;
                    $drug_generic_ids .= $drug_company_next_id.',';
                    $drug_company_next_id++;                
                } elseif(!empty($generic_data_arr[strtolower(trim($value['Drug_3']))])){
                    $drug_generic_ids .= $generic_data_arr[strtolower(trim($value['Drug_3']))].',';
                }
            }
            if(!empty(trim($value['Drug_4']))) {
                if(empty($generic_data_arr[strtolower(trim($value['Drug_4']))])) {
                    $insert_drug_generic_data_query .= "INSERT INTO me_drug_generic (drug_generic_title,drug_generic_created_at) values('".trim($value['Drug_4'])."','".$drug_generic_created_at."');\n";
                    $generic_data_arr[strtolower(trim($value['Drug_4']))] = $drug_company_next_id;
                    $drug_generic_ids .= $drug_company_next_id.',';
                    $drug_company_next_id++;                
                } elseif(!empty($generic_data_arr[strtolower(trim($value['Drug_4']))])){
                    $drug_generic_ids .= $generic_data_arr[strtolower(trim($value['Drug_4']))].',';
                }
            }
            if(!empty($drug_generic_ids)){
                $drug_data_update_query .= "UPDATE drugs_data SET drug_generic_generic_id='".trim($drug_generic_ids,',')."' WHERE id=".$value['id'].";\n";
            }
        }
        force_download('me_drug_generic.sql', "START TRANSACTION;\n".$insert_drug_generic_data_query."\n".$drug_data_update_query."COMMIT;");
        die();
    }
    public function map_drug_unit_id() {
        $sql_query = "SELECT id,drug_unit_medicine_type,drug_unit_name,drug_drug_unit_id FROM drugs_data WHERE drug_drug_unit_id>0";
        $result = $this->Common_model->get_all_rows_by_query($sql_query);
        $drug_units_query = "SELECT drug_unit_id,drug_unit_medicine_type,drug_unit_name FROM me_drug_units WHERE drug_unit_status=1";
        $drug_units_data = $this->Common_model->get_all_rows_by_query($drug_units_query);
        // echo "<pre>";
        // print_r($drug_units_data);
        $drug_units_name_with_id = [];
        foreach ($drug_units_data as $key => $value) {
            $drug_units_name_with_id[strtolower(trim($value['drug_unit_medicine_type'])).'_'.strtolower(trim($value['drug_unit_name']))] = $value['drug_unit_id'];
        }
        $map_drug_units_id_query = "";
        $insert_drug_units_query = "";
        $drug_units_next_id = $this->get_next_auto_id("me_drug_units");
        $created_at = date('Y-m-d H:i:s');
        foreach ($result as $key => $value) {
            if(!empty($drug_units_name_with_id[strtolower(trim($value['drug_unit_medicine_type'])).'_'.strtolower(trim($value['drug_unit_name']))]) && $drug_units_name_with_id[strtolower(trim($value['drug_unit_medicine_type'])).'_'.strtolower(trim($value['drug_unit_name']))] != trim($value['drug_drug_unit_id'])){
                $drug_drug_unit_id = $drug_units_name_with_id[strtolower(trim($value['drug_unit_medicine_type'])).'_'.strtolower(trim($value['drug_unit_name']))];
                // $map_drug_units_id_query .= "";
                $map_drug_units_id_query .= "UPDATE drugs_data SET drug_drug_unit_id='".trim($drug_drug_unit_id)."' WHERE id=".$value['id'].";\n";
            }
            if(empty($drug_units_name_with_id[strtolower(trim($value['drug_unit_medicine_type'])).'_'.strtolower(trim($value['drug_unit_name']))])){
                $insert_drug_units_query .= "INSERT INTO me_drug_units (drug_unit_medicine_type,drug_unit_name,drug_unit_created_at) values('".trim($value['drug_unit_medicine_type'])."','".trim($value['drug_unit_name'])."','".$created_at."');\n";

                $map_drug_units_id_query .= "UPDATE drugs_data SET drug_drug_unit_id='".$drug_units_next_id."' WHERE id=".$value['id'].";\n";
                $drug_units_name_with_id[strtolower(trim($value['drug_unit_medicine_type'])).'_'.strtolower(trim($value['drug_unit_name']))] = $drug_units_next_id;
                $drug_units_next_id++;
            }
        }
        force_download('drug_units_id_map.sql', "START TRANSACTION;\n".$map_drug_units_id_query."\n".$insert_drug_units_query."COMMIT;");
        // print_r($drug_units_name_with_id);die;
        die;
    }
    public function create_drug_unit() {
        $sql_query = "SELECT id,drug_unit_medicine_type,drug_unit_name,drug_drug_unit_id FROM drugs_data WHERE drug_drug_unit_id=0";
        $result = $this->Common_model->get_all_rows_by_query($sql_query);
        // echo "<pre>";
        // print_r($result);die;
        $drug_units_next_id = $this->get_next_auto_id("me_drug_units");
        $drug_units_data = [];
        $insert_drug_units_data_query = "";
        $created_at = date('Y-m-d H:i:s');
        foreach ($result as $key => $value) {
            if(empty($value['drug_unit_medicine_type']) || empty($value['drug_unit_name']))
                continue;

            if(empty($drug_units_data[$value['drug_unit_medicine_type'].$value['drug_unit_name']])){
                $insert_drug_units_data_query .= "INSERT INTO me_drug_units (drug_unit_medicine_type,drug_unit_name,drug_unit_created_at) values('".trim($value['drug_unit_medicine_type'])."','".trim($value['drug_unit_name'])."','".$created_at."');\n";
                $drug_units_data[$value['drug_unit_medicine_type'].$value['drug_unit_name']] = $drug_units_next_id;
                $drug_data_update_query .= "UPDATE drugs_data SET drug_drug_unit_id=".$drug_units_next_id." WHERE id=".$value['id'].";\n";
                $drug_units_next_id++;  
            } elseif(!empty($drug_units_data[$value['drug_unit_medicine_type'].$value['drug_unit_name']])){
                $drug_drug_unit_id = $drug_units_data[$value['drug_unit_medicine_type'].$value['drug_unit_name']];
                $drug_data_update_query .= "UPDATE drugs_data SET drug_drug_unit_id=".$drug_drug_unit_id." WHERE id=".$value['id'].";\n";
            }
        }
        force_download('me_drug_units.sql', "START TRANSACTION;\n".$insert_drug_units_data_query."\n".$drug_data_update_query."COMMIT;");
        die();
    }
    public function update_drug_unit() {
        $sql_query = "SELECT dd.id,dd.drug_unit_medicine_type,dd.drug_unit_name,dd.drug_drug_unit_id,du.drug_unit_name as old_drug_unit_name FROM drugs_data dd JOIN me_drug_units du ON du.drug_unit_id=dd.drug_drug_unit_id AND (du.drug_unit_medicine_type != dd.drug_unit_medicine_type OR du.drug_unit_name != dd.drug_unit_name) WHERE dd.drug_drug_unit_id>0 group by dd.drug_drug_unit_id";
        $result = $this->Common_model->get_all_rows_by_query($sql_query);
        echo "<pre>";
        print_r($result);die;
        $drug_units_data = [];
        $insert_drug_units_data_query = "";
        $created_at = date('Y-m-d H:i:s');
        foreach ($result as $key => $value) {
            if(empty($value['drug_unit_medicine_type']) || empty($value['drug_unit_name']))
                continue;

            if(empty($drug_units_data[$value['drug_unit_medicine_type'].$value['drug_unit_name']])){
                $insert_drug_units_data_query .= "INSERT INTO me_drug_units (drug_unit_medicine_type,drug_unit_name,drug_unit_created_at) values('".trim($value['drug_unit_medicine_type'])."','".trim($value['drug_unit_name'])."','".$created_at."');\n";
                $drug_units_data[$value['drug_unit_medicine_type'].$value['drug_unit_name']] = $drug_units_next_id;
                $drug_data_update_query .= "UPDATE drugs_data SET drug_drug_unit_id=".$drug_units_next_id." WHERE id=".$value['id'].";\n";
                $drug_units_next_id++;  
            } elseif(!empty($drug_units_data[$value['drug_unit_medicine_type'].$value['drug_unit_name']])){
                $drug_drug_unit_id = $drug_units_data[$value['drug_unit_medicine_type'].$value['drug_unit_name']];
                $drug_data_update_query .= "UPDATE drugs_data SET drug_drug_unit_id=".$drug_drug_unit_id." WHERE id=".$value['id'].";\n";
            }
        }
        force_download('me_drug_units.sql', "START TRANSACTION;\n".$insert_drug_units_data_query."\n".$drug_data_update_query."COMMIT;");
        die();
    }

    public function create_new_drugs() {
        $sql_query = "SELECT id,drug_code,drug_id,drug_name,drug_name_with_unit,drug_company_name,drug_company_id,drug_strength,drug_unit_medicine_type,drug_unit_name,drug_drug_unit_id,drug_drug_unit_value,drug_generic_generic_id FROM drugs_data WHERE drug_id=0 
        GROUP BY drug_name,
                  drug_name_with_unit,
                  drug_company_id,
                  drug_strength,
                  drug_drug_unit_id
        ";
        $result = $this->Common_model->get_all_rows_by_query($sql_query);
        // echo count($result);die;
        // echo "<pre>";
        // print_r($result);die;
        $created_at = date('Y-m-d H:i:s');
        $insert_drugs_data_query = "";
        foreach ($result as $key => $value) {
                $insert_drugs_data_query .= "INSERT INTO me_drugs (drug_name,drug_name_with_unit,drug_code,drug_company_name,drug_company_id,drug_strength,drug_drug_generic_id,drug_drug_unit_id,drug_drug_unit_value,drug_created_at) values('".trim($value['drug_name'])."','".trim($value['drug_name_with_unit'])."','".trim($value['drug_code'])."','".trim($value['drug_company_name'])."','".trim($value['drug_company_id'])."','".trim($value['drug_strength'])."','".trim($value['drug_generic_generic_id'])."','".trim($value['drug_drug_unit_id'])."','".trim($value['drug_drug_unit_value'])."','".$created_at."');\n";
        }
        force_download('me_drugs_creates.sql', "START TRANSACTION;\n".$insert_drugs_data_query."COMMIT;");
        die();
    }

    public function update_drugs($page) {
        if(empty($page)){
            die("invalid page");
        }
        $page1 = $page;
        $per_page = 30000;
        $page = $page - 1;
        $page = $page*$per_page;
        $sql_query = "SELECT id,drug_code,drug_id,drug_name,drug_name_with_unit,drug_company_name,drug_company_id,drug_strength,drug_unit_medicine_type,drug_unit_name,drug_drug_unit_id,drug_drug_unit_value,drug_generic_generic_id FROM drugs_data WHERE drug_id > 0
        LIMIT $page , $per_page
        ";
        // echo $sql_query;die;
        $result = $this->Common_model->get_all_rows_by_query($sql_query);
        // echo "<pre>";
        // print_r($result);die;
        $update_drugs_data_query = "";
        foreach ($result as $key => $value) {
                $update_drugs_data_query .= "UPDATE me_drugs SET drug_name='".trim($value['drug_name'])."',drug_name_with_unit='".trim($value['drug_name_with_unit'])."',drug_code='".trim($value['drug_code'])."',drug_company_name='".trim($value['drug_company_name'])."',drug_company_id='".trim($value['drug_company_id'])."',drug_strength='".trim($value['drug_strength'])."',drug_drug_generic_id='".trim($value['drug_generic_generic_id'])."',drug_drug_unit_id='".trim($value['drug_drug_unit_id'])."',drug_drug_unit_value='".trim($value['drug_drug_unit_value'])."' WHERE drug_id=".trim($value['drug_id']).";\n";
        }
        force_download('me_drugs_updates_'.$page1.'.sql', "START TRANSACTION;\n".$update_drugs_data_query."COMMIT;");
        die();
    }

    //Remove local drug added by doctor
    public function remove_local_drug() {
        $sql_local_drugs = "SELECT drug_id, LOWER(drug_name) AS drug_name,drug_user_id 
                FROM 
                `me_drugs` 
                WHERE drug_status=1 AND drug_user_id IS NOT NULL";
        $result = $this->Common_model->get_all_rows_by_query($sql_local_drugs);
        // echo "<pre>";
        if(!empty($result) && count($result) > 0) {
            $doctor_ids = array_values(array_unique(array_column($result, 'drug_user_id')));
            $sql_doctor_template = "SELECT t.template_doctor_id,db.template_drug_binding_drug_id FROM me_template_drug_binding db JOIN me_template t ON t.template_id=db.template_drug_binding_template_id WHERE db.template_drug_binding_status = 1 AND t.template_status=1 AND t.template_doctor_id IN(".implode(',', $doctor_ids).")";
            $doctors_template = $this->Common_model->get_all_rows_by_query($sql_doctor_template);
            $doctors_temp_data = [];
            foreach ($doctors_template as $key => $value) {
                $k = $value['template_doctor_id'].'_'.$value['template_drug_binding_drug_id'];
                $doctors_temp_data[$k] = $value;
            }
            $sql_drugs = "SELECT drug_id, LOWER(drug_name) AS drug_name 
                    FROM 
                    `me_drugs` 
                    WHERE drug_status=1 AND drug_user_id IS NULL";
            $drugs_data = $this->Common_model->get_all_rows_by_query($sql_drugs);
            $drugs_name = array_column($drugs_data, 'drug_id','drug_name');
            $delete_drugs_data_query = "";
            $sql_doctor_template_update = "";
            foreach ($result as $key => $value) {
                if(!empty($drugs_name[$value['drug_name']])) {
                    $delete_drugs_data_query .= "UPDATE me_drugs SET drug_status=9 WHERE drug_id=".$value['drug_id'].";\n";
                    $k = $value['drug_user_id'].'_'.$value['drug_id'];
                    if(!empty($doctors_temp_data[$k])) {
                        $sql_doctor_template_update = "UPDATE me_template_drug_binding db JOIN me_template t ON t.template_id=db.template_drug_binding_template_id SET db.template_drug_binding_drug_id=".$drugs_name[$value['drug_name']]." WHERE db.template_drug_binding_status = 1 AND t.template_status=1 AND db.template_drug_binding_drug_id=".$value['drug_id']." AND t.template_doctor_id = ".$value['drug_user_id'].";\n";
                    }
                }
            }
            force_download('remove_local_drugs.sql', "START TRANSACTION;\n".$delete_drugs_data_query."\n".$sql_doctor_template_update."COMMIT;");
            die();
        } else {
            die("No found local drug");
        }
    }

    public function delete_duplicate_drugs() {
        $sql_query = "SELECT 
                  COUNT(drug_id) AS totals,
                  GROUP_CONCAT(drug_id) AS ids, 
                  drug_name,
                  drug_name_with_unit,
                  drug_company_id,
                  drug_strength,
                  drug_drug_unit_id 
                FROM
                  `me_drugs` 
                  WHERE drug_status=1 AND drug_user_id IS NULL
                GROUP BY drug_name,
                  drug_name_with_unit,
                  drug_company_id,
                  drug_strength,
                  drug_drug_unit_id 
                HAVING totals > 1";

        $result = $this->Common_model->get_all_rows_by_query($sql_query);
        // echo "<pre>";
        // print_r($result);die;
        $delete_drugs_data_query = "";
        foreach ($result as $key => $value) {
            $ids_arr = explode(',', $value['ids']);
            // print_r($ids_arr);
            $min_drug_id = min($ids_arr);
            foreach ($ids_arr as $drug_id) {
                if($min_drug_id != $drug_id)
                    $delete_drugs_data_query .= "DELETE FROM me_drugs WHERE drug_id=".$drug_id.";\n";
            }
        }
        force_download('me_drugs_delete.sql', "START TRANSACTION;\n".$delete_drugs_data_query."COMMIT;");
        die();
    }

    function get_next_auto_id($table_name) {
        $next = $this->db->query("SHOW TABLE STATUS LIKE '" . $table_name . "'");
        $next = $next->row(0);
        $next->Auto_increment;
        return $next->Auto_increment;
    }

    public function create_qua() {
        $sql_query = "SELECT qua_title FROM qualification_txt GROUP BY qua_title";
        $result = $this->Common_model->get_all_rows_by_query($sql_query);
        // echo "<pre>";
        // print_r($result);die;

        $sql_query = "SELECT qualification_id,LOWER(TRIM(qualification_name)) AS qualification_name FROM me_qualification GROUP BY qualification_name";
        $qualification = $this->Common_model->get_all_rows_by_query($sql_query);
        $qualification_arr = array_column($qualification, 'qualification_id','qualification_name');
        // echo "<pre>";
        // print_r($qualification_arr);die;
        $query = "";
        $created_at = date('Y-m-d H:i:s');
        foreach ($result as $key => $value) {
            if(empty($qualification_arr[strtolower(trim($value['qua_title']))])){
                // echo $value['qua_title']. "<br>";
                $query .= "INSERT INTO me_qualification (qualification_name,qualification_created_at) values('".trim($value['qua_title'])."','".$created_at."');\n";
            } else {
                // echo $qualification_arr[strtolower($value['qua_title'])];
                // echo $value['qua_title']."<br>";
            }
        }
        force_download('me_qualification_more_added.sql', $query);
        die();
    }

}