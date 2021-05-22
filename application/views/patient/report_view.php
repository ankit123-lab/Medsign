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
            <div class="row">
                <div class="col-lg-12">
                <?php
                    $this->load->view('patient/page_title',['title' => $breadcrumbs]);
                ?>
                </div>
                <div class="col-lg-12 btn-m-0">
                    <div class="contact-form">
                        <?php if ($this->session->userdata('message') != '') : ?>
                            <div class="alert alert-success" id="message">
                                <strong>Success!</strong> <?php echo $this->session->userdata('message'); ?>
                            </div>
                        <?php endif; ?>
                        <form method="get" action="<?= site_url('patient/report'); ?>">
                            <div class="row btns-row"> 
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <input type="text" value="<?= ($this->input->get('search_txt')) ? $this->input->get('search_txt') : ''; ?>" placeholder="Search" name="search_txt" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <button type="submit" name="submit">Search</button>
                                    <a href="<?= site_url('patient/report'); ?>" class="btns">Clear</a>
                                </div>
                                <div class="col-lg-3 col-6 text-right">
                                    <a href="<?= site_url('patient/add_report'); ?>" class="btns add-report-btn">Add Report</a>
                                </div>
                            </div>
                        </form>

                        <table class="table table-striped table-responsive-grid">
                            <thead>
                                <tr>
                                    <th>Report Name</th>
                                    <th>Type Of Report</th>
                                    <th>Date Of Report</th>
                                    <th>Added By</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (count($reports) > 0) {
                                foreach ($reports as $key => $value) {
                                ?>
                                    <tr>
                                        <td data-title="Report Name"><?= $value->file_report_name; ?></td>
                                        <td data-title="Type Of Report"><?= $value->report_type_name; ?></td>
                                        <td data-title="Date Of Report"><?= date("d/m/Y", strtotime($value->file_report_date)); ?></td>
                                        <td data-title="Added By"><?= ($value->user_type == 2 ? DOCTOR . ' ' : '') . $value->user_name; ?></td>
                                        <td>
                                            <span class="icon view-icon">
                                                <a href="<?= site_url("patient/view_report/" . encrypt_decrypt($value->file_report_id, 'encrypt')) ?>" title="View">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </span>
                                            <?php if ($value->user_type == 1) { ?>
                                                <span class="icon delete-icon">
                                                    <a href="<?= site_url("patient/delete_report/" . encrypt_decrypt($value->file_report_id, 'encrypt')) ?>" title="Delete" onclick="return confirm('Are you sure to delete this report?')">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </span>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php }
                                } else { ?>
                                    <td colspan="5" class="text-center no-record">No record found</td>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div class="pagination"><?php echo $links; ?></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php $this->load->view('patient/footer'); ?>
    <?php $this->load->view('patient/footer_layout'); ?>
<script type="text/javascript">
    $("#message").delay(5000).slideUp(300);
</script>
