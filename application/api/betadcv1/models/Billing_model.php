<?php

class Billing_model extends MY_Model {

    function __construct() {
        parent::__construct();
    }

    public function get_billing_information_for_doctor($requested_data) {
        $columns = 'billing_id,
                    billing_advance_amount,
                    billing_created_at,
                    billing_invoice_date,
                    billing_is_import,
                    billing_appointment_id,
                    billing_user_id,
                    billing_doctor_user_id,
                    billing_clinic_id,
                    billing_payment_mode_id,
                    billing_discount,
                    billing_tax,
                    billing_grand_total,
                    billing_total_payable,
                    billing_paid_amount,
                    invoice_number,
                    billing_detail_name,
                    billing_detail_unit,
                    billing_detail_basic_cost,
                    billing_detail_discount,
                    billing_detail_tax,
                    billing_detail_discount_type,
                    billing_detail_tax_id,
                    billing_detail_total,
                    billing_detail_id,
                    billing_detail_created_at,
                    payment_mode_name,
                    payment_mode_vendor_fee';

        $get_billing_sql = "    SELECT 
                                    " . $columns . " 
                                FROM 
                                    " . TBL_BILLING . " 
                                LEFT JOIN 
                                    " . TBL_BILLING_DETAILS . " 
                                ON 
                                    billing_id = billing_detail_billing_id AND billing_detail_status = 1
                                LEFT JOIN    
                                    " . TBL_PAYMENT_MODE . "
                                ON
                                    billing_payment_mode_id = payment_mode_id    
                                WHERE 
                                    billing_appointment_id = '" . $requested_data['appointment_id'] . "'
                                AND 
                                    billing_user_id = '" . $requested_data['patient_id'] . "'
                                AND
                                    billing_doctor_user_id = '" . $requested_data['doctor_id'] . "'
                                AND
                                    billing_id = '" . $requested_data['billing_id'] . "' 
                                AND 
                                    billing_status = 1 ";

        $get_billing_data = $this->get_all_rows_by_query($get_billing_sql);
        return $get_billing_data;
    }

    public function get_invoices($requested_data) {
        $this->db->select("
                b.billing_id,
                b.invoice_number,
                b.billing_appointment_id,
                b.billing_user_id,
                b.billing_discount,
                b.billing_tax,
                (SELECT SUM(billing_detail_basic_cost*billing_detail_unit) FROM me_billing_details WHERE billing_detail_billing_id=b.billing_id AND billing_detail_status=1) AS billing_grand_total,
                b.billing_total_payable,
                b.billing_advance_amount,
                b.billing_paid_amount,
                b.billing_is_import,
                DATE_FORMAT(b.billing_invoice_date, '%d/%m/%Y') AS billing_invoice_date,
                DATE_FORMAT(CONVERT_TZ(b.billing_created_at,'+00:00','+05:30'), '%d/%m/%Y') AS billing_created_at
            ");
        $this->db->from('me_billing b');
        $this->db->where("b.billing_doctor_user_id", $requested_data['doctor_id']);
        $this->db->where("b.billing_user_id", $requested_data['patient_id']);
        $this->db->where("b.billing_appointment_id", $requested_data['appointment_id']);
        $this->db->where("b.billing_status", 1);
        $this->db->order_by('b.billing_id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_billing_information_for_patient($requested_data) {

        $columns = '    billing_id,
                        appointment_date, 
                        clinic_name,
                        clinic_contact_number,
                        user_first_name AS doctor_first_name,
                        user_last_name AS doctor_last_name,
                        GROUP_CONCAT(DISTINCT(doctor_qualification_degree)) as doctor_qualification';

        $get_patient_billing_sql = "SELECT 
                                        " . $columns . " 
                                    FROM 
                                        " . TBL_BILLING . "
                                    LEFT JOIN
                                        " . TBL_APPOINTMENTS . " 
                                    ON 
                                        billing_appointment_id = appointment_id
                                    LEFT JOIN 
                                        " . TBL_CLINICS . " 
                                    ON 
                                        appointment_clinic_id = clinic_id
                                    LEFT JOIN 
                                        " . TBL_USERS . " 
                                    ON 
                                        appointment_doctor_user_id = user_id
                                    LEFT JOIN 
                                        " . TBL_DOCTOR_EDUCATIONS . " 
                                    ON  
                                        doctor_qualification_user_id=user_id AND doctor_qualification_status=1
                                    WHERE 
                                        billing_user_id = '" . $requested_data['patient_id'] . "' 
                                    AND 
                                        billing_status != 9  
                                    GROUP BY appointment_date
                                    ORDER BY appointment_date DESC ";

        $get_patient_billing_data = $this->get_all_rows_by_query($get_patient_billing_sql);

        return $get_patient_billing_data;
    }

    public function get_tax_value($requested_data) {
        $get_tax_sql = "SELECT 
                            GROUP_CONCAT(tax_value) as tax_value
                        FROM 
                            " . TBL_TAXES . " 
                        WHERE 
                            tax_doctor_id = '" . $requested_data['doctor_id'] . "'
                        AND 
                            tax_id IN (" . $requested_data['tax_id'] . ")
                        AND
                            tax_status = 1 ";
        $get_tax_data = $this->get_single_row_by_query($get_tax_sql);
        return $get_tax_data;
    }

    public function get_pricing($requested_data) {
        $get_prcing_sql = " SELECT 
                                pricing_catalog_id,
                                pricing_catalog_name,
                                pricing_catalog_cost,
                                pricing_catalog_tax_id,
                                pricing_catalog_instructions,
                                GROUP_CONCAT(tax_name) AS tax_name,
                                GROUP_CONCAT(tax_value) AS tax_value,
                                GROUP_CONCAT(tax_id) AS tax_id
                            FROM
                                " . TBL_PRICING_CATALOG . "
                            LEFT JOIN
                                " . TBL_TAXES . " ON FIND_IN_SET(tax_id, pricing_catalog_tax_id)
                            WHERE
                                pricing_catalog_doctor_id = '" . $requested_data['doctor_id'] . "'
                            AND
                                pricing_catalog_clinic_id = '" . $requested_data['clinic_id'] . "'
                            AND 
                                pricing_catalog_status = 1 ";
        if (!empty($requested_data['search'])) {
            $get_prcing_sql .= " AND pricing_catalog_name LIKE '%" . $requested_data['search'] . "%'  ";
        }
        $get_prcing_sql .= " GROUP BY pricing_catalog_id ";
        $get_pricing_data = $this->get_all_rows_by_query($get_prcing_sql);
        return $get_pricing_data;
    }
	
	public function get_user_invoiceno_details($doctor_id){
		$get_invoiceno_sql = "SELECT inv_prefix, inv_counter FROM " . TBL_INVOICE_DETAIL . " WHERE doctor_id = '" . $doctor_id. "'";
        $user_invoice_details = $this->get_single_row_by_query($get_invoiceno_sql);
        return $user_invoice_details;
	}
}