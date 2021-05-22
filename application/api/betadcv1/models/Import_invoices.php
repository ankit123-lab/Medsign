<?php

class Import_invoices extends MY_Model {

    protected $import_invoices_table;
    protected $import_payments_table;
    protected $import_pricing_catalog;
    protected $doctor_import_table;
    protected $pricing_catalog;
    protected $payment_mode;
    protected $payment_types;

    function __construct() {
        parent::__construct();
        $this->import_invoices_table = 'me_import_invoices';
        $this->import_payments_table = 'me_import_payments';
        $this->import_pricing_catalog = 'me_import_pricing_catalog';
        $this->doctor_import_table = 'me_doctor_import';
        $this->pricing_catalog = 'me_pricing_catalog';
        $this->payment_mode = 'me_payment_mode';
        $this->payment_types = 'me_payment_types';
    }


    public function get_import_invoices($where, $columns = '*', $grouping = false) {
        if(!empty($where) && count($where) > 0) {
            $this->db->select($columns)->from($this->import_invoices_table . ' i');
            $this->db->join($this->doctor_import_table . ' d', 'd.import_file_id=i.import_file_id');
            foreach ($where as $key => $value) {
                $this->db->where($key, $value);
            }
            if($grouping) {
                $this->db->group_by(array('invoice_date','import_patient_unique_id','invoice_number','treatment_name'));
            }
            $query = $this->db->get();
            return $query->result();
        } else {
            return array();
        }
    }

    public function get_import_payments($where, $columns = '*') {
        if(!empty($where) && count($where) > 0) {
            $this->db->select($columns)->from($this->import_payments_table . ' p');
            $this->db->join($this->doctor_import_table . ' d', 'd.import_file_id=p.import_file_id');
            foreach ($where as $key => $value) {
                $this->db->where($key, $value);
            }
            $this->db->group_by(array('payment_date','import_patient_unique_id','receipt_number','invoice_number'));
            $query = $this->db->get();
            return $query->result();
        } else {
            return array();
        }
    }

    public function get_import_pricing_catalog($where, $columns = '*') {
        if(!empty($where) && count($where) > 0) {
            $this->db->select($columns)->from($this->import_pricing_catalog . ' pc');
            $this->db->join($this->doctor_import_table . ' d', 'd.import_file_id=pc.import_file_id');
            foreach ($where as $key => $value) {
                $this->db->where($key, $value);
            }
            $query = $this->db->get();
            return $query->result();
        } else {
            return array();
        }
    }

    public function get_pricing_catalog($where, $columns = '*') {
        if(!empty($where) && count($where) > 0) {
            $this->db->select($columns)->from($this->pricing_catalog);
            foreach ($where as $key => $value) {
                $this->db->where($key, $value);
            }
            $query = $this->db->get();
            return $query->result_array();
        } else {
            return array();
        }
    }

    public function get_payment_mode($where, $columns = '*') {
        if(!empty($where) && count($where) > 0) {
            $this->db->select($columns)->from($this->payment_mode);
            foreach ($where as $key => $value) {
                $this->db->where($key, $value);
            }
            $query = $this->db->get();
            return $query->result_array();
        } else {
            return array();
        }
    }

    public function get_payment_types($where, $columns = '*') {
        if(!empty($where) && count($where) > 0) {
            $this->db->select($columns)->from($this->payment_types);
            foreach ($where as $key => $value) {
                $this->db->where($key, $value);
            }
            $query = $this->db->get();
            return $query->result_array();
        } else {
            return array();
        }
    }

}