<?php $this->load->view('web/header_layout'); ?>
    <body data-spy="scroll" data-target=".header" data-offset="50">
       <?php 
            $this->load->view('web/header');
        ?>
        <!-- breadcrumb area start -->
        <section class="hero-area breadcrumb-area">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="hero-area-content">
                            <h1><?php echo $page_title; ?></h1>
                            <ul>
                                <li><a href="index.html">Home </a></li>
                                <li><?php echo '&nbsp'.$breadcrumbs; ?></li>
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
                    <div class="col-lg-8">
                        <div class="blog-details">
                            <?php echo $data; ?>
                        </div>
                    </div>
                    <?php  $this->load->view('web/right_side_sub_links'); ?>
                </div>
            </div>
        </section>
        <?php  $this->load->view('web/footer'); ?>
        <?php $this->load->view('web/footer_layout');?>