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
<?php foreach ($uas7_result as $key => $value) { ?>
<tr>
    <td>
        <?= date("d/m/Y", strtotime($value['patient_diary_date'])); ?>
        <?php if(get_display_date_time("Y-m-d") == get_display_date_time("Y-m-d", $value['patient_diary_created_at']) && $value['patient_diary_patient_id'] == $value['patient_diary_added_by']) { ?>
        <a href="<?= site_url('patient/edit_uas7_para/'.encrypt_decrypt($value['patient_diary_id'],'encrypt')); ?>" title="Edit">
            <i class="fa fa-pencil-square-o"></i>
        </a>
        <?php } ?>
    </td>
    <td class="bg1">
        <?php
        if($value['wheal_value'] == 0){
            echo '<i class="fa fa-check"></i>';
        }
        ?>
    </td>
    <td class="bg2">
        <?php
        if($value['wheal_value'] == 1){
            echo '<i class="fa fa-check"></i>';
        }
        ?>
    </td>
    <td class="bg3">
        <?php
        if($value['wheal_value'] == 2){
            echo '<i class="fa fa-check"></i>';
        }
        ?>
    </td>
    <td class="bg4">
        <?php
        if($value['wheal_value'] == 3){
            echo '<i class="fa fa-check"></i>';
        }
        ?>
    </td>
    <td class="sep">&nbsp;</td>
    <td class="bg1">
        <?php
        if($value['pruritus_value'] == 0){
            echo '<i class="fa fa-check"></i>';
        }
        ?>
    </td>
    <td class="bg2">
        <?php
        if($value['pruritus_value'] == 1){
            echo '<i class="fa fa-check"></i>';
        }
        ?>
    </td>
    <td class="bg3">
        <?php
        if($value['pruritus_value'] == 2){
            echo '<i class="fa fa-check"></i>';
        }
        ?>
    </td>
    <td class="bg4">
        <?php
        if($value['pruritus_value'] == 3){
            echo '<i class="fa fa-check"></i>';
        }
        ?>
    </td>
    <td><?= ($value['wheal_value'] + $value['pruritus_value']); ?></td>
</tr>
<?php } ?>
