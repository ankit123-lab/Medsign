<?php
class Sample_sheet extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
    }

    public function download() {
    	if(!empty($_SERVER['HTTP_REFERER'])) {  
	    	$this->load->helper('download');
	    	force_download(DOCTOR_IMPORT_FILE_PATH.'Import sample sheet.xlsx', NULL);
    	}
    	die();
    }
}
