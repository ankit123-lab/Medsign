<?php $this->load->view('patient/header_layout'); ?>

<body data-spy="scroll" data-target=".header" data-offset="50">
    <!-- Page loader -->
    <div id="preloader"></div>
    <!-- header section start -->
    <?php
    $this->load->view('patient/header');
    ?>
    <section class="ptb-90 main-section report-page">
        <div class="container">
            <?php
                $this->load->view('patient/page_title',['title' => $breadcrumbs]);
            ?>
            <div class="uas7-form d-none d-lg-block">
                <table class="table table-bordered table-uas7">
                    <thead>
                        <tr>
                            <th width="10%" scope="col" class="field-white">
                                DATE
                            </th>
                            <th width="10%" scope="col" class="bg1">
                                WHEAL COUNT
                                <span>
                                    NONE
                                </span>
                            </th>
                            <th width="10%" scope="col" class="bg2">
                                WHEAL COUNT
                                <span>
                                    MILD - &lt;20 Wheals/24h
                                </span>
                            </th>
                            <th width="10%" scope="col" class="bg3">
                                WHEAL COUNT
                                <span>
                                    MODERATE - 20-50 Wheals/24h
                                </span>
                            </th>
                            <th width="10%" scope="col" class="bg4">
                                WHEAL COUNT
                                <span>
                                    INTENSE - &gt;50 Wheals/24h
                                </span>
                            </th>
                            <th width="2%" scope="col" class="sep">
                                &nbsp;
                            </th>
                            <th width="10%" scope="col" class="bg1">
                                PRURITUS
                                <span>
                                    No prutitus
                                </span>
                            </th>
                            <th width="10%" scope="col" class="bg2">
                                PRURITUS
                                <span>
                                    MILD - Present but not annoying or troublesome
                                </span>
                            </th>
                            <th width="10%" scope="col" class="bg3">
                                PRURITUS
                                <span>
                                    MODERATE - Troublesome but does not interfere with normal daily activity or sleep
                                </span>
                            </th>
                            <th width="10%" scope="col" class="bg4">
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
                    <tbody class="desktop_version">
                        <?php for ($i = $diff_days; $i >= 0; $i--) { ?>
                        <tr row="<?= $i; ?>">
                            <td>
                                <?= date("d/m/Y", strtotime("+ " . $i . " days", strtotime($start_date))); ?>
                                <input type="hidden" name="uas7_date[]" value="<?= date("Y-m-d", strtotime("+ " . $i . " days", strtotime($start_date))); ?>">
                            </td>
                            <td class="bg1 human-body_container wheal_count" wheal_count="0">
                                <img src="<?= ASSETS_PATH ?>web/img/wc1.png" alt="" class="human-body <?= (!empty($uas7_para_data[$i]) && $uas7_para_data[$i]['wheal_value']==0) ? 'active' : ''; ?>">
                            </td>
                            <td class="bg2 human-body_container wheal_count" wheal_count="1">
                                <img src="<?= ASSETS_PATH ?>web/img/wc2.png" alt="" class="human-body <?= (!empty($uas7_para_data[$i]) && $uas7_para_data[$i]['wheal_value']==1) ? 'active' : ''; ?>">
                            </td>
                            <td class="bg3 human-body_container wheal_count" wheal_count="2">
                                <img src="<?= ASSETS_PATH ?>web/img/wc3.png" alt="" class="human-body <?= (!empty($uas7_para_data[$i]) && $uas7_para_data[$i]['wheal_value']==2) ? 'active' : ''; ?>">
                            </td>
                            <td class="bg4 human-body_container wheal_count" wheal_count="3">
                                <img src="<?= ASSETS_PATH ?>web/img/wc4.png" alt="" class="human-body <?= (!empty($uas7_para_data[$i]) && $uas7_para_data[$i]['wheal_value']==3) ? 'active' : ''; ?>">
                            </td>
                            <td class="sep">&nbsp;</td>
                            <td class="bg1 human-body_container pruritus_val" pruritus_val="0">
                                <img src="<?= ASSETS_PATH ?>web/img/pr5.png" alt="" class="human-body <?= (!empty($uas7_para_data[$i]) && $uas7_para_data[$i]['pruritus_value']==0) ? 'active' : ''; ?>">
                            </td>
                            <td class="bg2 human-body_container pruritus_val" pruritus_val="1">
                                <img src="<?= ASSETS_PATH ?>web/img/pr6.png" alt="" class="human-body <?= (!empty($uas7_para_data[$i]) && $uas7_para_data[$i]['pruritus_value']==1) ? 'active' : ''; ?>">
                            </td>
                            <td class="bg3 human-body_container pruritus_val" pruritus_val="2">
                                <img src="<?= ASSETS_PATH ?>web/img/pr7.png" alt="" class="human-body <?= (!empty($uas7_para_data[$i]) && $uas7_para_data[$i]['pruritus_value']==2) ? 'active' : ''; ?>">
                            </td>
                            <td class="bg4 human-body_container pruritus_val" pruritus_val="3">
                                <img src="<?= ASSETS_PATH ?>web/img/pr8.png" alt="" class="human-body <?= (!empty($uas7_para_data[$i]) && $uas7_para_data[$i]['pruritus_value']==3) ? 'active' : ''; ?>">
                            </td>
                            <td class="total_count"><?= (!empty($uas7_para_data[$i])) ? ($uas7_para_data[$i]['pruritus_value']+$uas7_para_data[$i]['wheal_value']) : ''; ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="uas7-form uas7-form-sm d-lg-none">
                <ul class="nav nav-tabs uas7-form-tabs" id="uas7-form-tabs" role="tablist">
                    <?php for ($i = $diff_days; $i >= 0; $i--) { ?>
                    <li class="nav-item">
                        <a class="date-tab-<?= $i; ?> nav-link <?=($diff_days==$i)?'active':''; ?>" id="tab<?= $i; ?>-tab" data-toggle="tab" href="#tab<?= $i; ?>" role="tab" aria-controls="tab<?= $i; ?>" aria-selected="<?=($diff_days==$i)?'true':'false'; ?>"><?= date("d/m/Y", strtotime("+ " . $i . " days", strtotime($start_date))); ?></a>
                    </li>
                    <?php } ?>
                </ul>
                <div class="tab-content" id="uas7-form-tab-content">
                    <?php for ($i = $diff_days; $i >= 0; $i--) { ?>
                    <div class="tab-pane fade <?=($diff_days==$i)?' show active':''; ?>" id="tab<?= $i; ?>" role="tabpanel" aria-labelledby="tab<?= $i; ?>-tab">
                        <div class="urticas-content">
                            <div id="accordion<?= $i; ?>" class="urticas-accordion">
                                <div class="card">
                                    <div class="card-header" id="headingOne<?= $i; ?>">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link" data-toggle="collapse" data-target="#urticas-one<?= $i; ?>" aria-expanded="true" aria-controls="urticas-one<?= $i; ?>">
                                                WHEAL COUNT <small>(Select image)</small>
                                            </button>
                                        </h5>
                                    </div>

                                    <div id="urticas-one<?= $i; ?>" class="collapse show" aria-labelledby="headingOne<?= $i; ?>" data-parent="#accordion<?= $i; ?>">
                                        <div class="card-body">
                                            <div class="row no-gutters" row="<?= $i; ?>">
                                                <div class="col-6 col-sm-3 text-center">
                                                    <button class="wheal-body btn btn-link btn-nopadding <?= (!empty($uas7_para_data[$i]) && $uas7_para_data[$i]['wheal_value']==0) ? 'uas7-form-sm-active' : ''; ?>" wheal_count="0">
                                                        <img src="<?= ASSETS_PATH ?>web/img/wc1.png" alt="" class="human-body">
                                                        <span>NONE</span>
                                                    </button>
                                                </div>
                                                <div class="col-6 col-sm-3 text-center">
                                                    <button class="wheal-body btn btn-link btn-nopadding <?= (!empty($uas7_para_data[$i]) && $uas7_para_data[$i]['wheal_value']==1) ? 'uas7-form-sm-active' : ''; ?>" wheal_count="1">
                                                        <img src="<?= ASSETS_PATH ?>web/img/wc2.png" alt="" class="human-body">
                                                        <span>MILD - &lt;20<br>Wheals/24h</span>
                                                    </button>
                                                </div>
                                                <div class="col-6 col-sm-3 text-center">
                                                    <button class="wheal-body btn btn-link btn-nopadding <?= (!empty($uas7_para_data[$i]) && $uas7_para_data[$i]['wheal_value']==2) ? 'uas7-form-sm-active' : ''; ?>" wheal_count="2">
                                                        <img src="<?= ASSETS_PATH ?>web/img/wc3.png" alt="" class="human-body">
                                                        <span>MODERATE - 20-50<br>Wheals/24h</span>
                                                    </button>
                                                </div>
                                                <div class="col-6 col-sm-3 text-center">
                                                    <button class="wheal-body btn btn-link btn-nopadding <?= (!empty($uas7_para_data[$i]) && $uas7_para_data[$i]['wheal_value']==3) ? 'uas7-form-sm-active' : ''; ?>" wheal_count="3">
                                                        <img src="<?= ASSETS_PATH ?>web/img/wc4.png" alt="" class="human-body">
                                                        <span>INTENSE - &gt;50<br>Wheals/24h</span>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="uas7-errors element-hide justify-content-end mt-3">
                                                Please select one from above
                                            </div>
                                            <div class="d-flex justify-content-end mt-3">
                                                <button class="element-hide btn btn-primary" type="button" data-toggle="collapse" data-target="#urticas-two<?= $i; ?>" aria-controls="urticas-two<?= $i; ?>">
                                                    Next
                                                </button>
                                                <button class="btn btn-primary wheal-next-btn" type="button">
                                                    Next
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header" id="headingTwo<?= $i; ?>">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#urticas-two<?= $i; ?>" aria-expanded="false" aria-controls="urticas-two<?= $i; ?>">
                                                PRURITUS <small>(Select image)</small>
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="urticas-two<?= $i; ?>" class="collapse" aria-labelledby="headingTwo<?= $i; ?>" data-parent="#accordion<?= $i; ?>">
                                        <div class="card-body">
                                            <div class="row no-gutters" row="<?= $i; ?>">
                                                <div class="col-6 col-sm-3 text-center">
                                                    <button class="pruritus-body btn btn-link btn-nopadding <?= (!empty($uas7_para_data[$i]) && $uas7_para_data[$i]['pruritus_value']==0) ? 'uas7-form-sm-active' : ''; ?>" pruritus_val="0">
                                                        <img src="<?= ASSETS_PATH ?>web/img/pr5.png" alt="" class="human-body">
                                                        <span>No prutitus</span>
                                                    </button>
                                                </div>
                                                <div class="col-6 col-sm-3 text-center">
                                                    <button class="pruritus-body btn btn-link btn-nopadding <?= (!empty($uas7_para_data[$i]) && $uas7_para_data[$i]['pruritus_value']==1) ? 'uas7-form-sm-active' : ''; ?>" pruritus_val="1">
                                                        <img src="<?= ASSETS_PATH ?>web/img/pr6.png" alt="" class="human-body">
                                                        <span>MILD <!-- - Present<br>but not<br>annoying or<br>troublesome --></span>
                                                    </button>
                                                </div>
                                                <div class="col-6 col-sm-3 text-center">
                                                    <button class="pruritus-body btn btn-link btn-nopadding <?= (!empty($uas7_para_data[$i]) && $uas7_para_data[$i]['pruritus_value']==2) ? 'uas7-form-sm-active' : ''; ?>" pruritus_val="2">
                                                        <img src="<?= ASSETS_PATH ?>web/img/pr7.png" alt="" class="human-body">
                                                        <span>MODERATE <!-- - <br>Troublesome <br>but does <br>not interfere <br>with normal daily <br>activity or sleep --></span>
                                                    </button>
                                                </div>
                                                <div class="col-6 col-sm-3 text-center">
                                                    <button class="pruritus-body btn btn-link btn-nopadding <?= (!empty($uas7_para_data[$i]) && $uas7_para_data[$i]['pruritus_value']==3) ? 'uas7-form-sm-active' : ''; ?>" pruritus_val="3">
                                                        <img src="<?= ASSETS_PATH ?>web/img/pr8.png" alt="" class="human-body">
                                                        <span>SEVERE <!-- - <br>Troublesome to <br>interfere with <br>normal daily <br>activity <br>or sleep --></span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="uas7-errors element-hide justify-content-end mt-3">Please select one from above</div>
                                            <div class="d-flex justify-content-end mt-3">
                                                <?php if($i > 0) { ?>
                                                    <button class="btn btn-primary next-tab" type="button" next_tab="<?= ($i-1); ?>" date="<?= date("Y-m-d", strtotime("+ " . $i . " days", strtotime($start_date))); ?>">
                                                    Save & Next
                                                    </button>
                                                <?php } else { ?>
                                                    <button class="btn btn-primary last-tab-btn" type="button" date="<?= date("Y-m-d", strtotime("+ " . $i . " days", strtotime($start_date))); ?>">
                                                    Save
                                                </button>
                                                <?php } ?>
                                            </div>
                                            <div class="col-12 col-sm-12 text-left" style="margin-top: 10px;">
                                                <table class="table table-bordered">
                                                    <tr><td>MILD</td><td>Present but not annoying or troublesome</td></tr>
                                                    <tr><td>MODERATE</td><td>Troublesome but does not interfere with normal daily activity or sleep</td></tr>
                                                    <tr><td>SEVERE</td><td>Troublesome to interfere with normal daily activity or sleep</td></tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>

            <?php  ?>
            <div class="row">
                <div class="col-lg-12 contact-form">
                    <form name="save-uas7-form" id="save-uas7-form" method="post">
                        <input type="hidden" name="doctor_id" id="doctor_id" value="<?= $doctor_id; ?>">
                        <input type="hidden" name="is_update" id="is_update" value="<?= !empty($is_update) ? 1 : 0; ?>">
                        <input type="hidden" name="is_medsign_doctor" id="is_medsign_doctor" value="<?= $is_medsign_doctor; ?>">
                        <input type="hidden" name="doctor_name" id="doctor_name" value="<?= !empty($doctor_name) ? $doctor_name : ''; ?>">
                        <input type="hidden" name="doctor_email" id="doctor_email" value="<?= !empty($doctor_email) ? $doctor_email : ''; ?>">
                        <input type="hidden" name="doctor_phone_number" id="doctor_phone_number" value="<?= !empty($doctor_phone_number) ? $doctor_phone_number : ''; ?>">
                        <textarea name="doctor_address" style="display: none;"><?= !empty($doctor_address) ? $doctor_address : ''; ?></textarea>
                        <input type="hidden" name="single_add_date" id="single_add_date" value="">
                        <input type="hidden" name="is_redirect" id="is_redirect" value="1">
                        <?php for ($i = $diff_days; $i >= 0; $i--) { ?>
                            <input type="hidden" name="uas7_date[]" value="<?= date("Y-m-d", strtotime("+ " . $i . " days", strtotime($start_date))); ?>">
                            <input type="hidden" name="wheal_count[]" id="wheal_count_<?= $i; ?>" value="<?= (!empty($uas7_para_data[$i])) ? $uas7_para_data[$i]['wheal_value'] : '0'; ?>">
                            <input type="hidden" name="pruritus_count[]" id="pruritus_count_<?= $i; ?>" value="<?= (!empty($uas7_para_data[$i])) ? $uas7_para_data[$i]['pruritus_value'] : '0'; ?>">
                        <?php } ?>
                        <div class="col-lg-12 form-group m_top_15 d-none d-lg-block">
                            <button type="button" class="btns-hide save-uas7-btn" value="Save" id="save_uas7_para_btn" name="Start">Save</button>
                            <a href="<?= site_url('patient/uas7diary'); ?>" class="btns btns-hide">Cancel</a>
                            <img class="loader-img" style="display: none;" src="<?= site_url(); ?>assets/admin/images/ajax-loader.gif">
                        </div>
                    </form>
                </div>
            </div>
                    
            <?php  ?>

        </div>
    </section>
    <?php $this->load->view('patient/footer'); ?>
    <?php $this->load->view('patient/footer_layout'); ?>
    <script type="text/javascript">
        $("#message").delay(5000).slideUp(300);
    </script>
