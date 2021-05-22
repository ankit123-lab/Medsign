<?php

class Cron extends CI_Controller {

    public function copy_clinical_report_images($requested_data) {

        $requested_data = json_decode(base64_decode($requested_data), true);
        //get the images of the clinical notes
        $column = 'clinic_notes_reports_images_url,
                   clinic_notes_reports_images_type';
        $where = array(
            'clinic_notes_reports_images_reports_id' => $requested_data['existing_id'],
            'clinic_notes_reports_images_status' => 1
        );
        $get_images = $this->Common_model->get_all_rows(TBL_CLINICAL_NOTES_REPORT_IMAGE, $column, $where);
        
        $inserted_id = $requested_data['inserted_id'];
        $folder_path = UPLOAD_REL_PATH . "/" . CILINICAL_REPORT . "/" . $inserted_id;

        if (!file_exists($folder_path)) {
            mkdir($folder_path, 0777, TRUE);
            chmod($folder_path, 0777);
        }

        if (!empty($get_images)) {
            foreach ($get_images as $image) {
                if (!empty($image['clinic_notes_reports_images_url'])) {
                    $file_get_contents = file_get_contents($image['clinic_notes_reports_images_url']);
                    $get_extension = pathinfo(parse_url($image['clinic_notes_reports_images_url'], PHP_URL_PATH), PATHINFO_EXTENSION);
                    $file_name = $inserted_id . "_" . uniqid() . "." . $get_extension;
                    file_put_contents($folder_path . "/" . $file_name, $file_get_contents);
                    $source = $folder_path . "/" . $file_name;
                    chmod($source, 0777);
                    include_once BUCKET_HELPER_PATH;
                    uploadimage($source, CILINICAL_REPORT . "/" . $inserted_id . "/" . $file_name);
                    $report_image[] = array(
                        'clinic_notes_reports_images_reports_id' => $inserted_id,
                        'clinic_notes_reports_images_url' => IMAGE_MANIPULATION_URL . CILINICAL_REPORT . "/" . $inserted_id . "/" . $file_name,
                        'clinic_notes_reports_images_type' => $image['clinic_notes_reports_images_type'],
                        'clinic_notes_reports_images_created_at' => date('Y-m-d H:i:s', time())
                    );
                }
            }
            
            exec("rm -rf " . DOCROOT_PATH . 'uploads/' . CILINICAL_REPORT . "/" . $inserted_id);
            $this->Common_model->insert_multiple(TBL_CLINICAL_NOTES_REPORT_IMAGE, $report_image);
        }
    }

}
