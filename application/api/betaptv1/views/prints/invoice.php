<html>
    <head>
        <style>
            body{
                font-family: "gotham_book";
            }
            .gotham_light{
                font-family: 'gotham_light';
            }
            .gotham_book{
                font-family: 'gotham_book';
            }
            .gotham_medium{
                font-family: 'gotham_medium';
            }
            .title{
                font-family: "gotham_medium";
                font-size: 24px;
            }
            .sub_title{
                font-family: "gotham_medium";
                font-size: 20px;
                margin: 25px 0px 0px;
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
            .width_60{
                width: 60%;
            }
            .width_70{
                width: 70%;
            }
            .width_20{
                width: 20%;
            }
            .width_30{
                width: 30%;
            }
            .width_40{
                width: 40%;
            }
            .border_top_bottom{
                border-top: 1px solid #d1d1d1;
                border-bottom: 1px solid #d1d1d1;
            }
            .border_bottom{
                border-bottom: 1px solid #d1d1d1;
            }
            .p_15_top_bottom{
                padding: 15px 0px;
            }
            .p_15_left_right{
                padding: 0xp 15px;
            }
            .patient_info p{
                margin: 0px;
            }
            .custom_table tr td {
                border-bottom:1pt solid #d1d1d1;
                padding: 10px 0px;
            }
            .custom_table { 
                border-collapse: collapse; 
                width: 100%;
                margin-top: 10px;

            }
            .custom_td_width .heading td {
                width: 20%;
                word-break: break-all;
                word-wrap: break-word;
                hyphens: auto;
            }
            .text_upper{
                text-transform: uppercase;
            }
            .font_24{
                font-size: 24px;
            }
            .color_green{
                color: green;
            }
            .no_margin{
                margin: 0px;
            }
            .background_heading{
                background: #d1d1d1;
            }
            .background_heading td{
                font-weight: bold;
            }
            .clearfix{
                clear: both;
            }
            .bill_container p{
                margin: 5px;
            }
            .bill_container{
                margin-top: 20px;                
            }
        </style>
    </head>
    <body>
        <div class="border_top_bottom p_15_top_bottom patient_info">
            <p><?= $patient_detail['patient_name'] ?></p>
            <p style="display: none;">Patient Id: <?= $patient_detail['patient_id'] ?></p>
            <p><?= $patient_detail['patient_number'] ?></p>
            <p><?= $patient_detail['patient_email'] ?></p>
            <?php if (!empty($patient_detail['kco'])) { ?>
                <p>Medical History: <?= $patient_detail['kco'] ?></p>
            <?php } ?>
        </div>
        <div>
            <p>By: <b>Dr. <?= $doctor_name; ?></b></p>
        </div>

        <?php if (!empty($billing_data)) { ?>
            <div>
                <div class="width_50 pull_left">
                    <p class="font_24 color_green no_margin">Invoices</p>
                </div>
                <div class="width_50 pull_left text_right">
                    <p class="no_margin">Invoice Number: <b><?php echo $billing_data[0]['invoice_number'] ?></b></p>
                    <p class="no_margin">Date: <b><?php echo get_display_date_time('d/m/Y', $billing_data[0]['billing_created_at']); ?></b></p>
                </div>
            </div>
            <div>
                <table class="custom_table"> 
                    <tr class="heading background_heading">
                        <td>No</td>
                        <td>Treatments & Products</td>
                        <td>Unit Cost <br />INR</td>
                        <td>Qty</td>
                        <td>Discount <br />INR</td>
                        <td>Tax <br />INR</td>
                        <td>Total cost <br />INR</td>
                    </tr>
                    <?php
                    $i = 1;
                    $total_cost = 0;
                    $total_discount = 0;
                    $total_tax = 0;
                    $total_unit = 0;
                    foreach ($billing_data as $billing) {
                        $cost = $billing['billing_detail_basic_cost'] * $billing['billing_detail_unit'];
                        ?>
                        <tr class="heading">
                            <td><?= $i; ?></td>
                            <td>
                                <?= ucfirst($billing['billing_detail_name']); ?>
                                <br />
                                <?= get_display_date_time('d/m/Y', $billing['billing_detail_created_at']); ?>
                            </td>
                            <td><?= $billing['billing_detail_basic_cost'] ?></td>
                            <td><?= $billing['billing_detail_unit']; ?></td>
                            <td>
                                <?php
                                if ($billing['billing_detail_discount_type'] == 1) {
                                    $discount = number_format(($cost * $billing['billing_detail_discount']) / 100, 2, '.', '');
                                } else {
                                    $discount = $billing['billing_detail_discount'];
                                }
                                echo $discount;
                                ?>
                            </td>
                            <td>
                                <?php
                                echo
                                !empty($billing['billing_detail_tax']) ? number_format($billing['billing_detail_tax'], 2, '.', '') : '-';
                                ?>
                            </td>
                            <td><?= number_format($billing['billing_detail_total'], 2, '.', '') ?></td>
                        </tr>
                        <?php
                        $total_cost = $total_cost + ($cost);
                        $total_discount = $total_discount + $discount;
                        $total_tax = $total_tax + $billing['billing_detail_tax'];
                        $total_unit = $total_unit + $billing['billing_detail_unit'];
                        $i++;
                    }

                    $grand_total = ($total_cost + $total_tax) - $total_discount;
                    $advance_amount = $billing_data[0]['billing_advance_amount'];
                    $payable_amount_tax = ($grand_total * $billing_data[0]['payment_mode_vendor_fee']) / 100;
                    $total_payable = round($payable_amount_tax + $grand_total);
                    $remaining_amount = $total_payable - $advance_amount;
                    ?>
                    <tr class="heading">
                        <td>Total</td>
                        <td></td>
                        <td><?= number_format($total_cost, 2, '.', ''); ?></td>
                        <td><?= $total_unit; ?></td>
                        <td><?= number_format($total_discount, 2, '.', ''); ?></td>
                        <td><?= number_format($total_tax, 2, '.', ''); ?></td>
                        <td><?= number_format($grand_total, 2, '.', ''); ?></td>
                    </tr>

                </table>
            </div>

            <div class="bill_container" style="margin-top: 50px;">
                <div class="pull_left" style="width: 60%">
                    <p class="sub_title">Payment details</p>
                    <table class="custom_table text_center"> 
                        <tr class="heading background_heading">
                            <td>Date</td>
                            <td>Reciept Number</td>
                            <td>Mode of payment</td>
                            <td>Amount to paid</td>
                        </tr>
                        <tr class="heading">
                            <td><?php echo get_display_date_time('d/m/Y', $billing_data[0]['billing_created_at']); ?></td>
                            <td>RCPT<?= $billing_data[0]['billing_id']; ?></td>
                            <td><?= $billing_data[0]['payment_mode_name'] . '(' . $billing_data[0]['payment_mode_vendor_fee'] . '%)'; ?></td>
                            <td><?= $billing_data[0]['billing_paid_amount']; ?></td>
                        </tr>
                    </table>
                </div>
                <div class="pull_left" style="width: 35%;margin-left: 5%;">
                    <div class="border_bottom" style="display: none;">
                        <div class="pull_left width_60 text_right">
                            <p>Total Cost: </p>
                            <p>Total Discount:</p>
                            <p>Total Tax:</p>
                        </div>
                        <div class="pull_left text-left">
                            <p><?= number_format($total_cost, 2, '.', ''); ?></p>
                            <p><?= number_format($total_discount, 2, '.', ''); ?></p>
                            <p><?= number_format($total_tax, 2, '.', ''); ?></p>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="border_bottom">
                        <div class="pull_left width_60 text_right">
                            <p>Total: </p>
                            <p>Mode of Payment <br /> <?= $billing_data[0]['payment_mode_name'] . '(' . $billing_data[0]['payment_mode_vendor_fee'] . '%)'; ?>: </p>
                            <p>Advance Received Amount:</p>
                        </div>
                        <div class="pull_left text_left">
                            <p><?= number_format($grand_total, 2, '.', ''); ?></p>
                            <p>
                                <br />
                                <?= number_format($payable_amount_tax, 2, '.', ''); ?>
                            </p>
                            <p>
                                <br />
                                <?= '-' . number_format($advance_amount, 2, '.', ''); ?>
                            </p>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div>
                        <div class="pull_left width_60 text_right">
                            <p>Amount to Paid: </p>
                        </div>
                        <div class="pull_left text_left">
                            <p><?= number_format($remaining_amount, 2, '.', ''); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </body>
</html>
