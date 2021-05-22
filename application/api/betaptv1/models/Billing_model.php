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
                    billing_appointment_id,
                    billing_user_id,
                    billing_doctor_user_id,
                    billing_clinic_id,
                    billing_payment_mode_id,
                    billing_discount,
                    billing_tax,
                    billing_grand_total,
                    billing_total_payable,
                    billing_advance_amount,
                    billing_paid_amount,
                    
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
                                    billing_status = 1 ";

        $get_billing_data = $this->get_all_rows_by_query($get_billing_sql);

        return $get_billing_data;
    }

    public function get_billing_information_for_patient($requested_data) {

        $columns = "	'' AS patient_invoice_id,
						billing_id,
                        appointment_date, 
                        clinic_name,
                        clinic_contact_number,
                        CONCAT('".DOCTOR."',' ',user_first_name) AS doctor_first_name,
                        user_last_name AS doctor_last_name,
                        GROUP_CONCAT(DISTINCT(doctor_qualification_degree)) as doctor_qualification,
						'' AS invoiceURL";
						
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
	
	public function get_billing_created_by_patient($requested_data) {

        $columns = "patient_invoice_id,
						'' AS billing_id,
						patient_invoice_date AS appointment_date, 
                        patient_invoice_clinic_name AS clinic_name,
						'' AS clinic_contact_number,
                        CONCAT('".DOCTOR." ', patient_invoice_doctor_name) AS doctor_first_name,
						'' AS doctor_last_name,
						'' AS doctor_qualification,
						GROUP_CONCAT(patient_invoice_photo_filepath SEPARATOR '###') AS invoiceURL
                        ";
        $get_patient_billing_sql = "SELECT 
                                        " . $columns . " 
                                    FROM 
                                        " . TBL_PATIENT_INVOICE . " 
                                    LEFT JOIN
                                        " . TBL_PATIENT_INVOICE_IMAGES . " 
                                    ON 
                                        patient_invoice_id = patient_invoice_photo_invoice_id 
                                    WHERE 
                                        patient_invoice_user_id = '" . $requested_data['patient_id'] . "' 
                                    AND 
										patient_invoice_status != 9 
									AND 
										patient_invoice_photo_status != 9
									GROUP BY patient_invoice_id 
                                    ORDER BY patient_invoice_date DESC ";

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
                                pricing_catalog_status = 1 ";

        if (!empty($requested_data['search'])) {
            $get_prcing_sql .= " AND pricing_catalog_name LIKE '%" . $requested_data['search'] . "%'  ";
        }

        $get_prcing_sql .= " GROUP BY pricing_catalog_id ";

        $get_pricing_data = $this->get_all_rows_by_query($get_prcing_sql);

        return $get_pricing_data;
    }

}
