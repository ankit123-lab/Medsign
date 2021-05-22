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
        <td width="50%" style="line-height:22px;">
            <b>Bill to</b><br>
            <b>Name: </b><?= $paymet_detail->user_first_name . ' ' . $paymet_detail->user_last_name; ?><br>
            <?= !empty($paymet_detail->patient_address) ? "<b>Address:</b> " . $paymet_detail->patient_address : ''; ?>
        </td>
    </tr>
</table>
<table class="payment table_width_100 invoice-margin-top" style="width: 100%;margin-top:20px;">
    <tr>
        <td width="80%"><b>Description</b></td>
        <td width="20%" align="right"><b>Amount Rs.</b></td>
    </tr>
    <tr>
        <td><?= $paymet_detail->sub_plan_name; ?></td>
        <td align="right"><?= number_format($paymet_detail->sub_total, 2); ?></td>
    </tr>
    <tr>
        <td align="right">Sub Total</td>
        <td align="right"><?= number_format($paymet_detail->sub_total, 2); ?></td>
    </tr>
    <?php if($paymet_detail->tax_igst_amount > 0) { ?>
    <tr>
        <td align="right">GST (<?= $paymet_detail->tax_igst_percent; ?>%)</td>
        <td align="right"><?= number_format($paymet_detail->tax_igst_amount,2); ?></td>
    </tr>
    <?php } elseif($paymet_detail->tax_sgst_amount > 0) { ?>
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