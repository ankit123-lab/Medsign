<style>
    td{font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;}
    .payment {
      font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
      border-collapse: collapse;
      width: 100%;
    }
    .payment td, .payment th {
      border: 1px solid #000;
      padding: 8px;
    }
    /*.payment tr:nth-child(even){background-color: #f2f2f2;}*/
    .payment th {
      padding-top: 12px;
      padding-bottom: 12px;
      text-align: left;
      background-color: #4CAF50;
      color: white;
    }
</style>
<table style="width: 100%;" class="table_width_100 invoice-margin-top">
    <tr>
        <td width="100%" style="line-height:22px;">
            <b>Bill to</b><br>
            <?=  DOCTOR . ' ' . $paymet_detail->user_first_name . ' ' . $paymet_detail->user_last_name; ?><br>
            Clinic name: <?= $paymet_detail->clinic_name; ?><br>
            <?= $paymet_detail->address_name_one; ?>,<br>
            <?= $paymet_detail->city_name .', ' . $paymet_detail->state_name .', ' . $paymet_detail->address_pincode; ?><br>
            <?= $paymet_detail->country_name; ?>
        </td>
    </tr>
</table>
<table  class="payment table_width_100 invoice-margin-top" style="width: 100%;margin-top:20px;">
    <?php if($paymet_detail->detail_type == 'sms' || $paymet_detail->detail_type == 'teleconsult_minutes'){ ?>
        <tr>
            <td width="80%"><b>Description</b></td>
            <td width="20%" align="right"><b>Amount Rs.</b></td>
        </tr>
        <tr>
            <td><?= $paymet_detail->sub_plan_name; ?> <?= $paymet_detail->detail_type == 'sms' ? 'SMS Credits' : 'Tele-Consultation minutes'; ?></td>
            <td align="right"><?= number_format($paymet_detail->sub_total, 2); ?></td>
        </tr>
    <?php } else { ?>
        <tr>
            <td width="30%"><b>Subscription</b></td>
            <td width="16%"><b>Plan</b></td>
            <td width="17%"><b>Start</b></td>
            <td width="17%"><b>Expiry</b></td>
            <td width="20%" align="right"><b>Amount Rs.</b></td>
        </tr>
        <tr>
            <td><?= $paymet_detail->sub_plan_name; ?></td>
            <td><?= $paymet_detail->sub_plan_validity; ?></td>
            <td><?= get_display_date_time('d/m/Y', $paymet_detail->plan_start_date); ?></td>
            <td><?= get_display_date_time('d/m/Y', $paymet_detail->plan_end_date); ?></td>
            <td align="right"><?= number_format($paymet_detail->sub_total, 2); ?></td>
        </tr>
    <?php
    }
    $showDesc = true;
    if($paymet_detail->detail_type == 'sms' || $paymet_detail->detail_type == 'teleconsult_minutes'){
        $rowspan = 0;
        $showDesc = false;
    } else {
        $rowspan = 4;
        if($is_apply_igst) {
            $rowspan = 3;
        }
    }
    if(!empty($paymet_detail->discount_amount) && $paymet_detail->discount_amount > 0) {
        $rowspan++;
    }
    if(!empty($paymet_detail->settlement_discount) && $paymet_detail->settlement_discount > 0) {
        $rowspan++;
    ?>
    <tr>
        <td rowspan="<?= $rowspan; ?>" colspan="3" style="vertical-align:top;"><?= html_entity_decode($paymet_detail->sub_description); ?></td>
        <td align="right">Settlement discount</td>
        <td align="right">-<?= number_format($paymet_detail->settlement_discount,2); ?></td>
    </tr>
    <?php 
        $showDesc = false;
    }
    if(!empty($paymet_detail->discount_amount) && $paymet_detail->discount_amount > 0) {
    ?>
    <tr>
        <?php if($showDesc) { ?>
        <td rowspan="<?= $rowspan; ?>" colspan="3" style="vertical-align:top;"><?= html_entity_decode($paymet_detail->sub_description); ?></td>
        <?php } ?>
        <td align="right">Discount</td>
        <td align="right">-<?= number_format($paymet_detail->discount_amount,2); ?></td>
    </tr>
    <?php 
        $showDesc = false;
    } ?>
    <tr>
        <?php if($showDesc) { ?>
        <td rowspan="<?= $rowspan; ?>" colspan="3" style="vertical-align:top;"><?= html_entity_decode($paymet_detail->sub_description); ?></td>
        <?php } ?>
        <td align="right">Sub Total</td>
        <td align="right"><?= (($paymet_detail->sub_total - $paymet_detail->settlement_discount - $paymet_detail->discount_amount) > 0) ? number_format(($paymet_detail->sub_total - $paymet_detail->settlement_discount - $paymet_detail->discount_amount),2) : '0.00'; ?></td>
    </tr>
    <?php if($is_apply_igst) { ?>
    <tr>
        <td align="right">IGST (<?= $paymet_detail->tax_igst_percent; ?>%)</td>
        <td align="right"><?= number_format($paymet_detail->tax_igst_amount,2); ?></td>
    </tr>
    <?php } ?>
    <?php if(!$is_apply_igst) { ?>
    <tr>
        <td align="right">SGST (<?= $paymet_detail->tax_sgst_percent; ?>%)</td>
        <td align="right"><?= number_format($paymet_detail->tax_sgst_amount,2); ?></td>
    </tr>
    <tr>
        <td align="right">CGST (<?= $paymet_detail->tax_cgst_percent; ?>%)</td>
        <td align="right"><?= number_format($paymet_detail->tax_cgst_amount,2); ?></td>
    </tr>
    <?php } ?>
    <tr>
        <td align="right">Total Rs.</td>
        <td align="right"><?= number_format($paymet_detail->paid_amount,2); ?></td>
    </tr>
</table>
<table style="width: 100%;margin-top:20px;" class="table_width_100 invoice-margin-top">
    <tr>
        <td width="50%">Payment ID: <?= (!empty($paymet_detail->razorpay_payment_id)) ? $paymet_detail->razorpay_payment_id : 'N/A'; ?></td>
        <td width="50%" align="right">Date: <?= get_display_date_time('d/m/Y', $paymet_detail->created_at); ?></td>
    </tr>
    <tr>
        <td width="50%">Order ID: <?= (!empty($paymet_detail->razorpay_order_id)) ? $paymet_detail->razorpay_order_id : 'N/A'; ?></td>
        <td width="50%" align="right">Status: <?= get_payment_status($paymet_detail->payment_status); ?></td>
    </tr>
</table>
<table style="width: 100%;margin-top:20px;" class="table_width_100 invoice-margin-top">
    <tr>
        <td width="100%" style="font-size: 12px;"><b>Note:</b> <?= $global_setting['payment_receipt_note'];?></td>
    </tr>
</table>
