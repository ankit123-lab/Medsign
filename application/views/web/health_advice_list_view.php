<?php $this->load->view('web/header_layout'); ?>
<body data-spy="scroll" data-target=".header" data-offset="50">
    <div id="preloader"></div>
    <?php
    $this->load->view('web/header');
    ?>
    <!-- breadcrumb area start -->
    <section class="hero-area breadcrumb-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="hero-area-content">
                        <h1><?php echo $breadcrumbs; ?></h1>
                        <ul>
                            <li><a href="index.html">Home </a></li>
                            <li><?php echo '&nbsp' . $breadcrumbs; ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- breadcrumb area end -->
    <!-- blog section start -->
    <section class="blog-detail" id="blog">
        <div class="container">
            <div class="row">
                <?php foreach ($health_advice as $key => $value) {
                    $req = ['p_id' => $patient_id, 'p_h_id' => $patient_health_advice_id, 'h_a_id' => $value->health_advice_id];
                ?>
                <div class="col-lg-4 col-md-6">
                    <div class="single-post">
                        <div class="post-thumbnail">
                            <?php if(!empty($value->health_advice_image)) { ?>
                            <a href="<?= site_url('health_advice/' . base64_encode(json_encode($req))); ?>"><img src="<?= $value->health_advice_image; ?>" alt="<?= $value->health_advice_name; ?>"></a>
                            <?php } ?>
                        </div>
                        <div class="post-details">
                            <div class="post-author">
                                <a href="<?= site_url('health_advice/' . base64_encode(json_encode($req))); ?>"><i class="icofont icofont-user"></i>Dr. <?= $value->doctor_name;?></a>
                                <a href="<?= site_url('health_advice/' . base64_encode(json_encode($req))); ?>"><i class="icofont icofont-speech-comments"></i>Comments</a>
                                <a href="<?= site_url('health_advice/' . base64_encode(json_encode($req))); ?>"><i class="icofont icofont-calendar"></i><?= get_display_date_time('d M Y',$value->audit_created_at); ?></a>
                            </div>
                            <h4 class="post-title"><a href="<?= site_url('health_advice/' . base64_encode(json_encode($req))); ?>"><?= $value->health_advice_name; ?></a></h4>
                            <p><?= substr($value->health_advice_desc, 0, 100); ?>...</p>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>
    <?php $this->load->view('web/footer'); ?>

    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/resetpassword.css">

    <?php $this->load->view('web/footer_layout'); ?>
 