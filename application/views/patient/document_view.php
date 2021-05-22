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
                <?php if(!empty($document['document_url']) && $document['document_type'] == 1){ ?>
                <div class="col-lg-12">
                    <div class="row" style="margin: 0 auto;">
                        <iframe src="<?= $document['document_url']; ?>?autoplay=1&amp;rel=0?autoplay=1" frameborder="0" width="500" height="400" allowfullscreen=""></iframe>
                    </div>
                </div>
                <div class="col-lg-12">
                    <p style="text-align: center;margin-top: 10px;">
                        This video is displayed from YouTube for education purpose.
                    </p>
                </div>
                <?php } ?>

                <?php if(!empty($view_pdf_url) && $document['document_type'] == 2) { ?>
                <div class="col-lg-12">
                    <div class="row" style="margin: 0 auto;">
                        <iframe src="<?= $view_pdf_url; ?>" frameborder="0" width="500" height="600" allowfullscreen=""></iframe>
                    </div>
                </div>
                <?php } ?>

            </div>
        </div>
    </section>

    <?php $this->load->view('patient/footer'); ?>
    <?php $this->load->view('patient/footer_layout'); ?>
