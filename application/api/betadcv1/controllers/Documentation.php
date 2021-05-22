<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * This function use for generate dynamic documentation
 * 
 * @author Dipesh Shihora
 * 
 * Modified Date :- 2016-09-15
 * 
 */
class Documentation extends CI_Controller {

    /**
     * 
     * This function use for get tada from api table, process data and display
     * 
     * @author Dipesh Shihora
     * 
     * Modified Date :- 2016-09-15
     * 
     */
    public function index() {
        $query = "SELECT * FROM " . TBL_API . " where ap_status=1 order by ap_group_id,ap_id";
        $documentDataObj = $this->db->query($query);
        $documentDataArr = $documentDataObj->result_array();

        //foreach use for prepare array group wise for display group header 
        $documentDataArray = array();
        foreach ($documentDataArr as $doc) {
            $documentDataArray[$doc['ap_group_id']]['name'] = $doc['ap_group_name'];
            $documentDataArray[$doc['ap_group_id']]['doc'][] = $doc;
        }


        $data['doc'] = $documentDataArray;
        $data['url'] = BASE_URL;
        $this->load->view('doc/index', $data);
    }

}

?>
