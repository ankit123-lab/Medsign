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
<section class="blog-detail" id="blog" style="padding: 40px 0 115px;">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <video controls="" controlslist="nodownload" style="width:100%;">
                    <source src="https://medeasys3mock.s3.ap-south-1.amazonaws.com/help_av/DrRegistrationProfilecompletionAV.mp4">                        
                        Your browser does not support the video tag.
                    </video>
                </div>
            </div>
        </div>
    </section>
    <?php  $this->load->view('web/footer'); ?>
    <?php $this->load->view('web/footer_layout');?>