<html>
    <head>
        <style>
            .uas7-form-sm {
                border: 0;
                border-radius: 0;
                box-shadow: none;
            }

            .table-uas7 {
                margin-bottom: 0;
                border: 0;
            }

            .table-uas7 thead th {
                vertical-align: top;
                padding: 18px 5px;
                border-top-color: #dee2e6 !important;
            }

            .table-uas7 thead th,
            .table-uas7 tbody td,
            .table-uas7 tfoot td {
                /* width: 10%; */
                /* width: calc((100% - 7px) / 11); */
                min-width: 98px;
                border-width: 3px;
                text-align: center;
            }

            .table-uas7 thead th.bg1,
            .table-uas7 tbody td.bg1 {
                background-color: #30aca5;
                color: #ffffff;
                border-left: 0;
                border-color: #fff;
            }

            .table-uas7 thead th.bg2,
            .table-uas7 tbody td.bg2 {
                background-color: #29908a;
                color: #ffffff;
                border-color: #fff;
            }

            .table-uas7 thead th.bg3,
            .table-uas7 tbody td.bg3 {
                background-color: #237570;
                color: #ffffff;
                border-color: #fff;
            }

            .table-uas7 thead th.bg4,
            .table-uas7 tbody td.bg4 {
                background-color: #154340;
                color: #ffffff;
                border-right: 0;
                border-color: #fff;
            }

            .table-uas7 thead th.sep,
            .table-uas7 tbody td.sep {
                width: 7px;
                min-width: inherit;
                background-color: #595959;
                padding: 0;
                border: 0;
                /* border-color: #59595c; */
            }

            .table-uas7 thead th span {
                font-weight: normal;
                display: block;
                font-size: 12px;
                line-height: 1.4;
            }

            .table-uas7 tbody td {
                padding: 10px 0;
            }

            .table-uas7 tbody td.human-body_container {
                padding-bottom: 3px;
                max-height: 150px;
            }
            td.human-body_container img{height: 150px;}
            .human-body {
                width: 50px;
            }

            .table-uas7 tbody tr:last-child td {
                border-bottom-color: #dee2e6 !important;
            }

            .table-uas7 tfoot td.total-label {
                /* background-color: #e9eaeb; */
                /* border-left-color: #e9eaeb; */
                /* border-bottom-color: #e9eaeb; */
                color: #154340;
                text-align: right;
                font-weight: bold;
                font-size: 18px;
                border-left: 0;
                border-bottom: 0;
            }

            .table-uas7 tfoot td.total-value {
                font-weight: bold;
                font-size: 16px;
            }

            .btn-margin-top {
                margin-top: 28px;
            }
            .table-bordered td, .table-bordered th {
                border: 1px solid #dee2e6;
            }
            .table-uas7 thead th {
                vertical-align: top;
                padding: 18px 5px;
                border-top-color: #dee2e6 !important;
            }
            .right-icon {width: 16px;}
            .range-table td {padding: 10px 5px}
        </style>
    </head>
    <body>
        <?php 
        if(!empty($graph_image_url)) {
        ?>
        <div style="float: left; width: 100%;margin-top: 100px;">&nbsp;</div>
        <div style="position: absolute;text-align: center;">
            <img style="position:relative;margin-top:150px;margin-bottom:150px;height:450px; transform:rotate(270deg);" src="<?= $graph_image_url; ?>" />
            <img style="position: fixed;float:right;" src="<?=site_url(); ?>assets/web/img/uas7/uas-ins.jpg" />
        </div>
        <pagebreak>
        <?php } ?>
        <?php 
        if(!empty($daily_graph_image_url)) {
        ?>
        <div style="float: left; width: 100%;margin-top: 100px;">&nbsp;</div>
        <div style="width: 100%;margin-top: 50px;height: 900px;text-align: center;">
            <img style="transform: rotate(270deg);" src="<?= $daily_graph_image_url; ?>">
        </div>
        <?php } ?>
        <pagebreak>
        <?php
        foreach($uas7_result as $usa7Key => $uas7_data) {
            if($usa7Key > 1)
                break;
        ?>
        <table class="table table-bordered table-uas7" style="margin-bottom: 30px;">
            <thead>
                <tr>
                    <th width="10%" scope="col" class="field-white">
                        DATE
                    </th>
                    <th width="10%" scope="col" class="bg1" style="border-top-color: #dee2e6;">
                        WHEAL COUNT
                        <span>
                            NONE
                        </span>
                    </th>
                    <th width="10%" scope="col" class="bg2" style="border-top-color: #dee2e6;">
                        WHEAL COUNT
                        <span>
                            MILD - &lt;20 Wheals/24h
                        </span>
                    </th>
                    <th width="10%" scope="col" class="bg3" style="border-top-color: #dee2e6;">
                        WHEAL COUNT
                        <span>
                            MODERATE - 20-50 Wheals/24h
                        </span>
                    </th>
                    <th width="10%" scope="col" class="bg4" style="border-top-color: #dee2e6;">
                        WHEAL COUNT
                        <span>
                            INTENSE - &gt;50 Wheals/24h
                        </span>
                    </th>
                    <th width="2%" scope="col" class="sep">
                        &nbsp;
                    </th>
                    <th width="10%" scope="col" class="bg1" style="border-top-color: #dee2e6;">
                        PRURITUS
                        <span>
                            No prutitus
                        </span>
                    </th>
                    <th width="10%" scope="col" class="bg2" style="border-top-color: #dee2e6;">
                        PRURITUS
                        <span>
                            MILD - Present but not annoying or troublesome
                        </span>
                    </th>
                    <th width="10%" scope="col" class="bg3" style="border-top-color: #dee2e6;">
                        PRURITUS
                        <span>
                            MODERATE - Troublesome but does not interfere with normal daily activity or sleep
                        </span>
                    </th>
                    <th width="10%" scope="col" class="bg4" style="border-top-color: #dee2e6;">
                        PRURITUS
                        <span>
                            SEVERE - Troublesome to interfere with normal daily activity or sleep
                        </span>
                    </th>
                    <th width="10%" scope="col" class="field-white field-total">
                        TOTAL
                    </th>
                </tr>
            </thead>
            <tbody class="uas7_para_list">
                <tr>
                    <td></td>
                    <td class="bg1 human-body_container">
                        <img src="<?= ASSETS_PATH ?>web/img/wc1.png" alt="" class="human-body">
                    </td>
                    <td class="bg2 human-body_container">
                        <img src="<?= ASSETS_PATH ?>web/img/wc2.png" alt="" class="human-body">
                    </td>
                    <td class="bg3 human-body_container">
                        <img src="<?= ASSETS_PATH ?>web/img/wc3.png" alt="" class="human-body">
                    </td>
                    <td class="bg4 human-body_container">
                        <img src="<?= ASSETS_PATH ?>web/img/wc4.png" alt="" class="human-body">
                    </td>
                    <td class="sep">&nbsp;</td>
                    <td class="bg1 human-body_container">
                        <img src="<?= ASSETS_PATH ?>web/img/pr5.png" alt="" class="human-body">
                    </td>
                    <td class="bg2 human-body_container">
                        <img src="<?= ASSETS_PATH ?>web/img/pr6.png" alt="" class="human-body">
                    </td>
                    <td class="bg3 human-body_container">
                        <img src="<?= ASSETS_PATH ?>web/img/pr7.png" alt="" class="human-body">
                    </td>
                    <td class="bg4 human-body_container">
                        <img src="<?= ASSETS_PATH ?>web/img/pr8.png" alt="" class="human-body">
                    </td>
                    <td></td>
                </tr>
                <?php 
                $uas7_score = 0;
                foreach ($uas7_data as $key => $value) {
                    if(($key+1) == count($uas7_data)){
                        $style = "border-bottom-color: #dee2e6";
                    } else {
                        $style = "";
                    }
                ?>
                <tr>
                    <td>
                        <?= date("d/m/Y", strtotime($value['patient_diary_date'])); ?>
                    </td>
                    <td class="bg1" style="<?= $style; ?>">
                        <?php
                        if($value['wheal_value'] == 0){ ?>
                        <img src="<?= ASSETS_PATH ?>web/img/right-icon.png" alt="" class="right-icon">
                        <?php }
                        ?>
                    </td>
                    <td class="bg2" style="<?= $style; ?>">
                        <?php
                        if($value['wheal_value'] == 1){ ?>
                        <img src="<?= ASSETS_PATH ?>web/img/right-icon.png" alt="" class="right-icon">
                        <?php }
                        ?>
                    </td>
                    <td class="bg3" style="<?= $style; ?>">
                        <?php
                        if($value['wheal_value'] == 2){ ?>
                        <img src="<?= ASSETS_PATH ?>web/img/right-icon.png" alt="" class="right-icon">
                        <?php }
                        ?>
                    </td>
                    <td class="bg4" style="<?= $style; ?>">
                        <?php
                        if($value['wheal_value'] == 3){ ?>
                        <img src="<?= ASSETS_PATH ?>web/img/right-icon.png" alt="" class="right-icon">
                        <?php }
                        ?>
                    </td>
                    <td class="sep">&nbsp;</td>
                    <td class="bg1" style="<?= $style; ?>">
                        <?php
                        if($value['pruritus_value'] == 0){ ?>
                        <img src="<?= ASSETS_PATH ?>web/img/right-icon.png" alt="" class="right-icon">
                        <?php }
                        ?>
                    </td>
                    <td class="bg2" style="<?= $style; ?>">
                        <?php
                        if($value['pruritus_value'] == 1){ ?>
                        <img src="<?= ASSETS_PATH ?>web/img/right-icon.png" alt="" class="right-icon">
                        <?php }
                        ?>
                    </td>
                    <td class="bg3" style="<?= $style; ?>">
                        <?php
                        if($value['pruritus_value'] == 2){ ?>
                        <img src="<?= ASSETS_PATH ?>web/img/right-icon.png" alt="" class="right-icon">
                        <?php }
                        ?>
                    </td>
                    <td class="bg4" style="<?= $style; ?>">
                        <?php
                        if($value['pruritus_value'] == 3){ ?>
                        <img src="<?= ASSETS_PATH ?>web/img/right-icon.png" alt="" class="right-icon">
                        <?php }
                        ?>
                    </td>
                    <td><?= ($value['wheal_value'] + $value['pruritus_value']); ?></td>
                </tr>
                <?php 
                $uas7_score += ($value['wheal_value'] + $value['pruritus_value']);
                if(($key+1) % 7 == 0) { ?>
                <tr>
                    <td colspan="10" align="right">UAS7 =</td>
                    <td><?= $uas7_score ?></td>
                </tr>
                <?php 
                    $uas7_score=0;
                }

            } ?>
            </tbody>
        </table>
        <?php   
         } ?>
    </body>
</html>