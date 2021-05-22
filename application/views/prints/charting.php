<html>
    <head>
        <style>
        <?php
        $font_family = 'Arial';
        if(!empty($printSettingData->font_family)) {
            $font_family = $printSettingData->font_family;
        }
        $font_size_1 = 14;
        if(!empty($printSettingData->font_size_1)) {
            $font_size_1 = $printSettingData->font_size_1;
        }
        $font_size_2 = 12;
        if(!empty($printSettingData->font_size_2)) {
            $font_size_2 = $printSettingData->font_size_2;
        }
        $font_size_3 = 10;
        if(!empty($printSettingData->font_size_3)) {
            $font_size_3 = $printSettingData->font_size_3;
        }
        ?>
        body{ font-family: <?= $font_family; ?>; color: #000000;}
        <?php if($language_code == 'en') { ?>
            .free-serif-font { font-family: <?= $font_family; ?>; }
        <?php } else if(in_array($language_code, ['kn'])) { ?>
            .free-serif-font { font-family: LohitKannada; }
        <?php } else if(in_array($language_code, ['te'])) { ?>
            .free-serif-font { font-family: Pothana2000; }
        <?php } else{ ?>
            .free-serif-font { font-family: FreeSerif; }
        <?php } ?>
            .font_size_1 {font-size: <?= $font_size_1 ?>px;}
            .font_size_2 {font-size: <?= $font_size_2 ?>px;}
            .font_size_3 {font-size: <?= $font_size_3 ?>px;}
            .sub_title{
                font-weight: bold;
                font-size: <?= $font_size_1 ?>px;
                margin: 5px 0px 0px;
            }
            .text_center{
                text-align: center;
            }
            .text_right{
                text-align: right;
            }
            .text_left{
                text-align: left;
            }
            .pull_left{
                float: left;    
            }
            .pull_right{
                float: right;
            }
            .width_100{
                width: 100%;
            }
            .width_50{
                width: 50%;
            }
            .width_20{
                width: 20%;
            }
            .border_top_bottom{
                border-top: 1px solid #d1d1d1;
                border-bottom: 1px solid #d1d1d1;
            }
            .border_bottom{
                border-bottom: 1px solid #d1d1d1;
            }
            .p_15_top_bottom{
                padding: 0 0 5px 0;
            }
            .p_15_left_right{
                padding: 0xp 15px;
            }
            .patient_info p{
                margin: 0px;
            }
            .custom_table tr td {
                border-bottom:1pt solid #d1d1d1;
                padding: 5px 0px;
            }
            .sign_text_table tr td {
                padding: 0px 0px;
            }
            .pre_custom_table tr td {
                padding: 3px 0px;
            }
            .custom_table, .pre_custom_table { 
                border-collapse: collapse; 
                width: 100%
            }
            .custom_td_width .heading td {
                width: 20%;
                word-break: break-all;
                word-wrap: break-word;
                hyphens: auto;
            }
            .border-bottom-0{
                border-bottom:0px !important;
            }
            .no_margin{
                margin: 0px;
            }
            .rx_border_bottom {border-bottom: 1px solid #d1d1d1;}
            .instrucRow > td{padding-bottom: 6px !important;}
            @page {
                header: html_myHeader;
                footer: html_myFooter;
            }
        </style>
    </head>
    <body>
        <!-- PDF Header  -->
        <htmlpageheader name="myHeader">
            <?= $prescriptionHeader; ?>
        </htmlpageheader>
       <!-- PDF Header END -->

       <!-- PDF Footer  -->
        <htmlpagefooter name="myFooter">
            <?= $prescriptionFooter; ?>
        </htmlpagefooter>
        <!-- PDF Footer END -->
        <?php if(isset($teleConsultationMsg)){ ?>
        <div class="border_bottom p_15_top_bottom font_size_2">
            <div class="text_center">
                <b><?php echo $teleConsultationMsg; ?></b>
            </div>
        </div>
        <?php } ?>
        <div class="border_bottom p_15_top_bottom font_size_2">
            <div class="pull_left width_50">
                Patient Name: <?= $patient_data['user_first_name'] . " " . $patient_data['user_last_name'] ?>
                <br/>
                <?php
                if (!empty($patient_data['user_details_dob'])) {
					$objCurAge = date_diff(date_create($patient_data['user_details_dob']), date_create('today'));
					$currAgeYR = $objCurAge->y;
					$currAgeM = $objCurAge->m;
					$currAgeD = $objCurAge->d;
					$ageString = '';
					if (($currAgeYR > 0) && ($currAgeM > 0) && ($currAgeD > 0))
						$ageString = $currAgeYR.'yr-'.$currAgeM.'m';
					else if (($currAgeYR == 0) && ($currAgeM == 0) && ($currAgeD > 0))
						$ageString = $currAgeD.'d';
					else if (($currAgeYR > 0) && ($currAgeM == 0) && ($currAgeD == 0))
						$ageString = $currAgeYR.'yr';
					else if (($currAgeYR > 0) && ($currAgeM > 0) && ($currAgeD == 0))
						$ageString = $currAgeYR.'yr';
					else if (($currAgeYR == 0) && ($currAgeM > 0) && ($currAgeD > 0))
						$ageString = $currAgeM.'m';
					else if (($currAgeYR > 0) && ($currAgeM == 0) && ($currAgeD > 0))
						$ageString = $currAgeYR.'yr';
					else if (($currAgeYR == 0) && ($currAgeM > 0) && ($currAgeD == 0))
						$ageString = $currAgeM.'m';
					else
						$ageString = "";
					
					if(!empty($ageString))
						echo 'Age: '.$ageString;
                    //echo "-" . date_diff(date_create($patient_data['user_details_dob']), date_create('today'))->y;
                }
                if (!empty($patient_data['user_gender'])) {
                    $gender = '';
                    if ($patient_data['user_gender'] == 'male')
                        $gender = 'Male';
                    else if ($patient_data['user_gender'] == 'female')
                        $gender = 'Female';
                    else if ($patient_data['user_gender'] == 'other')
                        $gender = 'Other';
                    if(!empty($gender))
                        echo ', Gender: ' . $gender;
                }
                ?>
            </div>
            <div class="text_right">
                Appointment On : <?= date("d/m/Y", strtotime($doctor_data['appointment_date'])); ?>
                <br/><?= (!empty($patient_data['user_phone_number'])) ? 'Mobile: ' . $patient_data['user_phone_number'] . ' , ' : ''; ?>UID: <?= (!empty($patient_data['user_patient_id'])) ? $patient_data['user_patient_id'] : $patient_data['user_unique_id']; ?>
            </div>        
        </div>
        <?php
        if (!empty($vitalsign_data)) {
            ?>
            <div>
                <p class="sub_title">Vitals</p>  
                <table class="custom_table custom_td_width font_size_2">
                    <tr class="heading">
                        <td>Weight (kg)</td>
                        <td>B.P (mm Hg)</td>
                        <td>Pulse Rate/min</td>
                        <td>Temperature (<?= $vitalsign_data['vital_report_temperature_type'] == 1 ? "F" : "C" ?>)</td>
                        <td>Resp. Rate/Min</td>
                    </tr>
                    <tr>
                        <td><?= number_format(($vitalsign_data['vital_report_weight'] / 2.20462), 2, ".", "") ?></td>
                        <td><?= $vitalsign_data['vital_report_bloodpressure_systolic'] . "/" . $vitalsign_data['vital_report_bloodpressure_diastolic'] ?></td>
                        <td><?= $vitalsign_data['vital_report_pulse'] ?></td>
                        <td><?= $vitalsign_data['vital_report_temperature'] ?></td>
                        <td><?= $vitalsign_data['vital_report_resp_rate'] ?></td>
                    </tr>
                </table>
            </div>
            <?php
        }
        if (!empty($clinicnote_data)) {
            ?>
            <div>
                <?php if($with_only_diagnosis != 'true') { ?>
                <p class="sub_title" style="margin-top: 15px;">Clinical notes</p>  
                <table class="custom_table custom_td_width font_size_2">
                    <?php
                        $clinical_notes_reports_kco = json_decode($clinicnote_data['clinical_notes_reports_kco'], true);
                        if (!empty($clinical_notes_reports_kco)) {
                            ?>
                            <tr class="heading">
                                <td>K/C/O</td>
                                <td>
                                    <?= implode(", ", $clinical_notes_reports_kco) ?>
                                </td>
                            </tr>
                            <?php
                        }
                        $clinical_notes_reports_complaints = json_decode($clinicnote_data['clinical_notes_reports_complaints'], true);
                        if (!empty($clinical_notes_reports_complaints)) {
                            ?>
                            <tr class="heading">
                                <td>Complaints</td>
                                <td>
                                    <?= implode(", ", $clinical_notes_reports_complaints) ?>
                                </td>
                            </tr>
                            <?php
                        }
                        $clinical_notes_reports_observation = json_decode($clinicnote_data['clinical_notes_reports_observation'], true);
                        if (!empty($clinical_notes_reports_observation)) {
                            ?>
                            <tr class="heading">
                                <td>Observation</td>
                                <td>
                                    <?= implode(", ", $clinical_notes_reports_observation) ?>
                                </td>
                            </tr>
                            <?php
                        }
                    
                    $clinical_notes_reports_diagnoses = json_decode($clinicnote_data['clinical_notes_reports_diagnoses'], true);
                    if (!empty($clinical_notes_reports_diagnoses)) {
                        ?>
                        <tr class="heading">
                            <td>Diagnosis</td>
                            <td>
                                <?= implode(", ", $clinical_notes_reports_diagnoses) ?>
                            </td>
                        </tr>
                        <?php
                    }
                    /*$clinical_notes_reports_add_notes = json_decode($clinicnote_data['clinical_notes_reports_add_notes'], true);
                    if (!empty($clinical_notes_reports_add_notes)) {
                        ?>
                        <tr class="heading">
                            <td>Notes</td>
                            <td>
                                <?= implode(", ", $clinical_notes_reports_add_notes) ?>
                            </td>
                        </tr>
                        <?php
                    }*/
                    ?>
                </table>
                <?php } elseif(!empty(json_decode($clinicnote_data['clinical_notes_reports_diagnoses']))) { ?>
                    <p class="sub_title" style="margin-top: 15px;">Diagnosis</p>  
                    <table class="custom_table custom_td_width font_size_2">
                        <?php 
                        $clinical_notes_reports_diagnoses = json_decode($clinicnote_data['clinical_notes_reports_diagnoses'], true);
                        if (!empty($clinical_notes_reports_diagnoses)) {
                            ?>
                            <tr class="heading">
                                <td>
                                    <?= implode(", ", $clinical_notes_reports_diagnoses) ?>
                                </td>
                            </tr>
                            <?php
                        } ?>
                    </table>
                <?php } ?>
            </div>
            <?php
        }
        $this->lang->load('prescription', $language_code);
        if (!empty($prescription_data)) {
            ?>
            <div>
                <p class="sub_title" style="margin-top: 15px;">Rx</p>  
                <table class="pre_custom_table custom_td_width" cellspacing="0">
                    <tr class="heading">
                        <td colspan="2" class="font_size_1" style="width: 53%;font-weight: bold;">Brand & Strength</td>
                        <td class="font_size_1" style="width: 13%;font-weight: bold;">Unit(s)</td>
                        <td class="font_size_1" style="width: 20%;font-weight: bold;" align="center">Frequency</td>
                        <td class="font_size_1" style="width: 14%;font-weight: bold;">Duration</td>
                        <!-- <td style="width: 10%">QTY</td> -->
                    </tr>
                    <?php
                    foreach ($prescription_data as $key => $data) {
                        if($data['prescription_is_import'] == 0 || !empty($data['drug_generic_title'])) {
                        ?>
                        <tr>
                            <td width="3%" class="font_size_2" style="border-top:1px solid #d1d1d1; padding-top: 2px;">
                                <?= ($key+1)."."; ?>
                            </td>
                            <td class="font_size_2" style="border-top:1px solid #d1d1d1">
                                <b><?= ucfirst(strtolower($data['drug_unit_medicine_type'])) . ' ' . ucfirst(strtolower($data['prescription_drug_name'])); ?></b>
                            </td>
                            <td class="font_size_2" style="border-top:1px solid #d1d1d1"></td>
                            <td class="font_size_2" align="center" style="border-top:1px solid #d1d1d1;">
                                <?php
                                $space = "&nbsp;&nbsp;";
                                if($data['prescription_frequency_id'] == 6) {
                                    echo "<span style='font-family: FreeSerif;'>&#10003;" . $space . "-" . $space . "&#10003;" . $space . "-" . $space . "&#10003;" . $space . "-" . $space . "&#10003;</span>";
                                } else {
                                    if(empty($data['prescription_dosage']) && ($data['drug_unit_name'] == 'Tablets' || $data['drug_unit_name'] == 'IU')) {
                                        if($data['drug_unit_name'] == 'IU')
                                            echo ucwords(str_replace('-', ' IU - ', $data['freq'])) . " IU";
                                        else
                                            echo "<span style='font-family: FreeSerif;font-weight: bold;'>" . ucwords(str_replace('-', $space . "-" . $space, $data['freq'])) . "</span>";
                                    } else {
                                        $freq_arr = explode('-', $data['freq']);
                                        $freq_data = "";
                                        foreach ($freq_arr as $f_key => $freq_val) {
                                            if($f_key > 0)
                                                $freq_data .= $space . "-" . $space;
                                            if(trim($freq_val) =="1") {
                                                $freq_data .= "&#10003;";
                                            } elseif(trim($freq_val) =="0") {
                                                $freq_data .= "&#10005;";
                                            }
                                        }
                                        echo "<span style='font-family: FreeSerif;'>" . $freq_data . "</span>";
                                    }
                                }
                                ?>
                                <!-- <span>&#10003; - &#10005; - &#10003;</span> -->
                            </td>
                            <td style="border-top:1px solid #d1d1d1" class="free-serif-font font_size_2">
                                <?php
                                echo $data['prescription_duration_value'] . " ";
                                //if($data['prescription_frequency_id'] != 9 && $data['prescription_frequency_id'] != 10)
                                // {
                                    switch ($data['prescription_duration']) {
                                        case 2:
                                            echo $this->lang->line('week');
                                            break;
                                        case 3:
                                            echo $this->lang->line('month');
                                            break;
                                        default :
                                            echo $this->lang->line('days');
                                            break;
                                    }
                                // }
                                ?>
                            </td>
                        </tr>
                        <?php } else { ?>
                            <tr>
                                <td class="font_size_2" colspan="5" style="border-top:1px solid #d1d1d1;padding: 0;margin: 0;"></td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <?php if (!empty($data['drug_generic_title']) && $with_generic == "true") { ?>
                                    <td class="font_size_2" style="padding-top:0px;"></td>
                                    <td class="font_size_2" style="padding-top:0px;" colspan="4"><?= ucfirst(strtolower($data['drug_generic_title'])) ?></td>
                            <?php } else { ?>
                                    <td class="font_size_2" style="padding-top:0px;"></td>
                                    <td class="font_size_2" style="padding-top:0px;" colspan="4">
                                        <?php
                                        if($data['prescription_is_import'] > 0) {
                                            echo ' ' . $data['prescription_unit_value'];
                                        }
                                        ?>
                                    </td>
                            <?php } ?>
                        </tr>
                        <?php
                        if (!empty($data['prescription_intake']) || !empty($data['prescription_intake_instruction']) || !empty($data['prescription_frequency_instruction'])) {
                            $rx_border_bottom = (count($prescription_data) == ($key+1)) ? 'rx_border_bottom' : '';
                            ?>
                            <tr>
                                <td class="<?= $rx_border_bottom; ?> font_size_2" style="padding-top:0px;"></td>
                                <td class="<?= $rx_border_bottom; ?> font_size_2" style="padding-top:0px; padding-right: 5px;padding-bottom: 5px;">
                                    Instruction : 
                                    <span class="free-serif-font">
                                    <?php
                                    if (!empty($data['prescription_intake'])) {
                                        switch ($data['prescription_intake']) {
                                            case 1:
                                                echo $this->lang->line('before_food') . ', ';
                                                break;
                                            case 2:
                                                echo $this->lang->line('after_food') . ', ';
                                                break;
                                            case 3:
                                                echo $this->lang->line('along_with_food') . ', ';
                                                break;
                                            case 4:
                                                echo $this->lang->line('empty_stomach') . ', ';
                                                break;
                                            case 5:
                                                echo $this->lang->line('as_directed') . ', ';
                                                break;
                                            default :
                                                echo "";
                                                break;
                                        }
                                    }
                                    ?>
                                    </span>
                                    <?php
                                    $instruction_translate_data = [];
                                    if (!empty($data['prescription_intake_instruction'])) {
                                        if(!empty($data['prescription_intake_instruction_json'])) {
                                            $instruction_translate_data = json_decode($data['prescription_intake_instruction_json']);
                                            $instruction_translate_data = array_column($instruction_translate_data, 'translation_text', 'language_id');
                                        }
                                        if(!empty($instruction_translate_data) && !empty($instruction_translate_data[$language_id])) {
                                            echo '<span class="free-serif-font">' . $instruction_translate_data[$language_id] . '</span>, ';
                                        } else {
                                            echo $data['prescription_intake_instruction'] . ", ";
                                        }
                                    }
                                    $instruction_translate_data = [];
                                    if (!empty($data['prescription_frequency_instruction'])) {
                                        if(!empty($data['prescription_frequency_instruction_json'])) {
                                            $instruction_translate_data = json_decode($data['prescription_frequency_instruction_json']);
                                            $instruction_translate_data = array_column($instruction_translate_data, 'translation_text', 'language_id');
                                        }
                                         if(!empty($instruction_translate_data) && !empty($instruction_translate_data[$language_id])) {
                                            echo '<span class="free-serif-font">' . $instruction_translate_data[$language_id] . '</span>';
                                        } else {
                                            echo $data['prescription_frequency_instruction'];
                                        }
                                    }
                                    ?>
                                    
                                </td>
                                <td class="<?= $rx_border_bottom; ?> font_size_2" style="padding-top:0px;">
                                <?php
                                    if($data['drug_unit_name'] == 'Tablets')
                                        $drug_unit_name = 'Tablet';
                                    elseif($data['drug_unit_name'] == 'Capsules')
                                        $drug_unit_name = 'Capsule';
                                    else
                                        $drug_unit_name = $data['drug_unit_name'];
                                    echo (($data['prescription_dosage'] != '-') ? "<span style='font-family: FreeSerif;'>".$data['prescription_dosage']."</span>" : '') . ' ' . $drug_unit_name;
                                    if($data['prescription_is_import'] > 0) {
                                        echo ' ' . $data['prescription_unit_value'];
                                    }
                                ?>
                                </td>
                                <td align="center" style="padding-top:0px;" class="font_size_2 free-serif-font <?= $rx_border_bottom; ?>">
                                    <?php
                                    if (!empty($data['prescription_frequency_id'])) {
                                        switch ($data['prescription_frequency_id']) {
                                            case 1:
                                                echo $this->lang->line('morning');
                                                break;
                                            case 2:
                                                echo $this->lang->line('afternoon');
                                                break;
                                            case 3:
                                                echo $this->lang->line('evening');
                                                break;
                                            case 4:
                                                echo $this->lang->line('twice_in_a_day');
                                                break;
                                            case 5:
                                                echo $this->lang->line('thrice_in_a_day');
                                                break;
                                            case 6:
                                                echo $this->lang->line('four_times_in_a_day');
                                                break;
                                            case 7:
                                                echo $this->lang->line('once_in_week');
                                                break;
                                            case 8:
                                                echo $this->lang->line('once_in_month');
                                                break;
                                            case 9:
                                                echo $this->lang->line('sos');
                                                break;
                                            case 10:
                                                echo $this->lang->line('other');
                                                break;
                                            case 11:
                                                echo $this->lang->line('one_at_bedtime');
                                                break;
                                            default :
                                                echo "";
                                                break;
                                        }
                                    }
                                    ?>
                                </td>
                                <td style="padding-top:0px;" class="font_size_2 free-serif-font <?= $rx_border_bottom; ?>">
                            </tr>
                            <?php
                        }
                        ?>

                        <?php
                    }
                    ?>
                </table>
            </div>
            <?php
        }
        if (!empty($patient_lab_orders_data)) {
            ?>
            <div>
                <p class="sub_title" style="margin-top: 15px;">Investigations</p>  
                <table class="custom_table custom_td_width font_size_2">
                    <tr class="heading">
                        <td style="width: 53%;">Test Name</td>
                        <td style="width: 47%;">Instructions</td>
                    </tr>
                    <?php
                    $lab_report_test_name = json_decode($patient_lab_orders_data['lab_report_test_name'], true);
                    $no = 1;
                    foreach ($lab_report_test_name as $test_name => $desc) {
                        ?>
                        <tr>
                            <td><?= $no . ". " . ucfirst(strtolower($test_name)); ?></td>
                            <td><?= $desc ?></td>
                        </tr>
                        <?php
                        $no++;
                    }
                    ?>
                </table>
            </div>
            <?php
        }
        ?>
        <?php if (!empty($procedure_data)) {
                $instruction = $procedure_data['procedure_report_note'];
        ?>
            <div>
                <p class="sub_title" style="margin-top: 15px;">Procedure</p>  
                <table class="custom_table custom_td_width font_size_2">
                    <tr class="heading">
                        <td style="width: 53%;">Procedure Name</td>
                        <?php if(!empty($instruction)) { ?>
                        <td style="width: 47%;">Instruction</td>
                        <?php } ?>
                    </tr>
                    <?php
                    $procedure = json_decode($procedure_data['procedure_report_procedure_text'], true);
                    if (!empty($procedure)) {
                        ?>
                        <tr class="heading">
                            <td>
                                <?php 
                                foreach ($procedure as $key => $value) {
                                    echo  $key+1 . '.' . ucfirst(strtolower($value)) . ' ';
                                }
                                ?>
                            </td>    
                            <?php if(!empty($instruction)) { ?>
                            <td>
                                <?= $instruction; ?>
                            </td>                        
                            <?php } ?>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>
        <?php } ?>
        <?php
        if (!empty($prescription_data[0]['follow_up_instruction'])) {
            ?>
            <div style="margin-top: 30px;" class="font_size_2">
                <p class="no_margin"><b>Diet/Specific Instruction: </b> 
                <?php
                    $instruction_translate_data = [];
                    if(!empty($prescription_data[0]['follow_up_instruction_json'])) {
                        $instruction_translate_data = json_decode($prescription_data[0]['follow_up_instruction_json']);
                        $instruction_translate_data = array_column($instruction_translate_data, 'translation_text', 'language_id');
                    }
                     if(!empty($instruction_translate_data) && !empty($instruction_translate_data[$language_id])) {
                        echo '<span class="free-serif-font">' . nl2br($instruction_translate_data[$language_id]) . '</span>';
                    } else { 
                        echo nl2br($prescription_data[0]['follow_up_instruction']);
                    }
                ?>
                </p>  
            </div>
            <?php
        }
        if (!empty($prescription_data[0]['follow_up_followup_date'])) {
            ?>
            <div class="font_size_2" style="<?= (empty($prescription_data[0]['follow_up_instruction'])) ? 'margin-top: 30px;' : ''; ?>"><p class="no_margin">
                <?php if($language_code == 'en') { ?>
                <b><?= $this->lang->line('next_followup'); ?>: </b>
            <?php } else { ?>
                <span class="free-serif-font"><?= $this->lang->line('next_followup'); ?>: </span>
            <?php } ?>
                <?= date("d/m/Y", strtotime($prescription_data[0]['follow_up_followup_date'])); ?></p></div>
            <?php
        }
        ?>
        <div style="margin-top: 100px;" class="font_size_2">
            <?php if(!empty($doctor_data['user_sign_filepath']) && !empty($with_signature) && $with_signature == 'true') { ?>
                <div style="text-align: <?= !empty($printSettingData->sign_position) ? $printSettingData->sign_position : 'right';?>;">
                    <img style="border-left: 0px !important;border-right: 0px !important;" src="<?= get_image_thumb($doctor_data['user_sign_filepath']); ?>">
                </div>  
            <?php } ?>
        </div>
        <table style="width: 100%;" class="font_size_2 sign_text_table">
            <tr>
                <td width="50%" align="left">
                    <?php if(!empty($printSettingData->left_signature_check) && !empty($printSettingData->footer_left_signature)){
                        echo $printSettingData->footer_left_signature;
                    } ?>
                </td>
                <td width="50%" align="right">
                    <?php if(!empty($printSettingData->right_signature_check)){
                            echo $printSettingData->footer_right_signature;
                        } else {
                            echo DOCTOR." ". $doctor_data['user_first_name'] . " " . $doctor_data['user_last_name'];
                        }
                    ?>
                </td>
            </tr>
        </table>

        <?php
        if(!empty($reports) && count($reports) > 0) {
            foreach ($reports as $report) { 
            ?>
                <div style="margin-top: 30px;">
                    <img style="border-left: 0px !important;border-right: 0px !important;" src="<?= get_file_full_path($report['file_report_image_url']); ?>">  
                </div>
            <?php
            }
        }

        if(!empty($patient_tool_document) && count($patient_tool_document) > 0) {
            ?>
            <div style="margin-top: 5px;">
                <span style="font-size: <?= $font_size_1 ?>px;"><b>Patient documents link: </b></span>
            </div>
            <?php
            foreach ($patient_tool_document as $document) { 
            ?>
                <div style="margin-top: 5px;">
                    <a target="_blank" href="<?= MEDSIGN_WEB_CARE_URL;?>document/<?= encrypt_decrypt($document['id'],'encrypt'); ?>"><?= MEDSIGN_WEB_CARE_URL;?>document/<?= encrypt_decrypt($document['id'],'encrypt'); ?></a> 
                </div>
            <?php
            }
        }

        if(!empty($patient_share_link)) { ?>
            <div style="margin-top: 10px;font-size: <?= $font_size_3 ?>px;">
                <span>Urticaria Activity Score (UAS7) is an established and effective clinical scoring system, which is based on the daily assessment of the key urticaria symptoms, wheals, and pruritus over 7 days.  UAS7 allows clinicians to monitor levels of disease activity in response to treatment in CSU and may thus be used in a treat to target approach.  Thus,  documentation of symptoms for several days in a row is critical to ensure UAS results that are truly representative for the disease activity because urticaria symptoms can vary considerably from day to day.</span><br>
                <a target="_blank" href="<?= $patient_share_link;?>"><?= $patient_share_link; ?></a> 
            </div>
        <?php }

        ?>

        <?php if (!empty($billing_data)) { ?>
            <div>
                <p class="sub_title">Treatment plans</p>  
                <table class="custom_table custom_td_width">
                    <tr class="heading">
                        <td>Treatment</td>
                        <td>Cost (inr)</td>
                        <td>Discount (inr)</td>
                        <td>Total (inr)</td>
                    </tr>
                    <?php
                    $basic_cost = 0;
                    foreach ($billing_data as $data) {
                        $basic_cost = $basic_cost + $data['billing_detail_basic_cost'];
                        $discount = $data['billing_discount'];
                        $grand_total = $data['billing_grand_total'];
                        $payable_amount = $data['billing_total_payable'];
                        ?>
                        <tr>
                            <td><?= ucfirst($data['billing_detail_name']); ?></td>
                            <td><?= $data['billing_detail_basic_cost']; ?></td>
                            <td><?= $data['billing_detail_discount']; ?></td>
                            <td><?= $data['billing_detail_total']; ?></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td>Total</td>
                        <td><?= $basic_cost ?></td>
                        <td><?= $discount ?></td>
                        <td><?= $grand_total ?></td>
                    </tr>
                    <tr>
                        <td>Total payable amount</td>
                        <td></td>
                        <td></td>
                        <td><?= $payable_amount; ?></td>
                    </tr>
                </table>
            </div>
        <?php } ?>
    </body>
</html>
