<?php $this->load->view('patient/header_layout'); ?>

<body data-spy="scroll" data-target=".header" data-offset="50">
    <!-- Page loader -->
    <div id="preloader"></div>
    <!-- header section start -->
    <?php
    $this->load->view('patient/header');
    ?>
    <section class="ptb-90 main-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 offset-lg-3 col-md-8 offset-md-2">
                    <h1 class="page-title text-center">Dashboard</h1>
                    <div class="landing-page">
                        <div class="row">
                            <div class="col-6">
                                <a href="<?= site_url('patient/profile/update'); ?>" class="landing-page__item">
                                    <span class="icon">
                                        <img src="<?= site_url('assets/web/img/landing-icons/profile.svg'); ?>" alt="" width="64" height="64">
                                    </span>
                                    <span class="text">Profile</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="<?= site_url('patient/report'); ?>" class="landing-page__item">
                                    <span class="icon">
                                        <img src="<?= site_url('assets/web/img/landing-icons/history.svg'); ?>" alt="" width="64" height="64">
                                    </span>
                                    <span class="text">Records</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="<?= site_url('patient/appointment_list'); ?>" class="landing-page__item">
                                    <span class="icon">
                                        <img src="<?= site_url('assets/web/img/landing-icons/my-appointment.svg'); ?>" alt="" width="64" height="64">
                                    </span>
                                    <span class="text">Appointments</span>
                                </a>
                            </div>
                            <!-- <div class="col-6">
                                <a href="<?= site_url('patient/appointment_book'); ?>" class="landing-page__item">
                                    <span class="icon">
                                        <img src="<?= site_url('assets/web/img/landing-icons/book-appointment.svg'); ?>" alt="" width="64" height="64">
                                    </span>
                                    <span class="text">Book Appointment</span>
                                </a>
                            </div> -->
                            <!-- <div class="col-6">
                                <a href="<?= site_url('patient/vitals'); ?>" class="landing-page__item">
                                    <span class="icon">
                                        <img src="<?= site_url('assets/web/img/landing-icons/heartbeat-solid.svg'); ?>" alt="" width="64" height="64">
                                    </span>
                                    <span class="text">Vital Measurement</span>
                                </a>
                            </div> -->
                            <div class="col-6">
                                <a href="<?= site_url('patient/analytics_list'); ?>" class="landing-page__item">
                                    <span class="icon">
                                        <img src="<?= site_url('assets/web/img/landing-icons/heartbeat-solid.svg'); ?>" alt="" width="64" height="64">
                                    </span>
                                    <span class="text">Health Tracker</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="<?= site_url('patient/utilities_list'); ?>" class="landing-page__item">
                                    <span class="icon">
                                        <img src="<?= site_url('assets/web/img/landing-icons/chart-line-solid.svg'); ?>" alt="" width="64" height="64">
                                    </span>
                                    <span class="text">Health Diary</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="<?= site_url('patient/share_data'); ?>" class="landing-page__item">
                                    <span class="icon">
                                        <img src="<?= site_url('assets/web/img/landing-icons/share-square-solid.svg'); ?>" alt="" width="64" height="64">
                                    </span>
                                    <span class="text">Share Records</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php $this->load->view('patient/footer'); ?>
    <?php $this->load->view('patient/footer_layout'); ?>
