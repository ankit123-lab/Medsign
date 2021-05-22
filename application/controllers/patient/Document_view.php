<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Document_view extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('patient_auth');
    }

    public function view($id) {
        if(empty($id))
            redirect(site_url());
        $id = encrypt_decrypt($id,'decrypt');
        $document = $this->Common_model->get_single_row('me_patient_documents_shared','document_url,document_type', ['id'=> $id]);
        if(empty($document))
            redirect(site_url());

        $view_data = array();
        $view_data['breadcrumbs'] = "Document";
        $view_data['page_title'] = "Document";
        $view_data['document'] = $document;
        if($document['document_type'] == 2) {
            $view_data['view_pdf_url'] = DOMAIN_URL . 'prescription/' . base64_encode(urldecode(DOMAIN_URL . 'pdf_preview/web/view_pdf.php?file_url=' . base64_encode(urldecode($document['document_url']))));
        }
        $this->load->view('patient/document_view', $view_data);
    }
}