<table style="width:100%;" class="table_width_100">
    <tr>
        <td width="50%" align="left" style="text-align:left;vertical-align:top;">
            <table style="width:100%;float:left;">
                <tr>
                    <td width="100%" style="text-align:left;vertical-align:top;">
                        <img width="200" src="http://35.154.236.38/staging/app/images/logo.png?V2.171"/>
                    </td>
                </tr>
                <tr>
                    <td width="100%" style="text-align:left;font-size:30px;" class="invoice-title-size">
                        <b>Invoice</b>
                    </td>
                </tr>
                <tr>
                    <td width="100%" style="text-align:left;">
                        Invoice number: <?= $paymet_detail->invoice_no; ?>
                    </td>
                </tr>
                <tr>
                    <td width="100%" style="text-align:left;">
                        Date: <?= get_display_date_time('d/m/Y', $paymet_detail->created_at); ?>
                    </td>
                </tr>
            </table>
        </td>
        <td width="50%" align="right">
            <table style="width:100%;float:right;">
                <tr>
                    <td width="100%" style="text-align:right;line-height:22px;">
                        <b><?= $global_setting['Company Name']; ?></b><br>
                        <?= nl2br($global_setting['invoice_address']); ?><br>
                        GSTIN: <?= $global_setting['gujarat_branch_gst']; ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>