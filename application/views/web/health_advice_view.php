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
                <div class="col-lg-8">
                    <div class="blog-details">
                        <?php if(!empty($health_advice->health_advice_image)) { ?>
                            <p style="text-align: center;"><img src="<?= $health_advice->health_advice_image;?>" alt="<?= $health_advice->health_advice_name;?>"></p>
                        <?php } ?>
                        <div class="post-author">
                            <a href="javascript:void(0);"><i class="icofont icofont-user"></i>Dr. <?= $health_advice->doctor_name;?></a>
                            <a href="javascript:void(0);"><i class="icofont icofont-speech-comments"></i>Comments</a>
                            <a href="javascript:void(0);"><i class="icofont icofont-calendar"></i><?= get_display_date_time('d M Y',$health_advice->audit_created_at);?></a>
                            <a href="javascript:void(0);" style="float: right;" is_like="1" class="like-count" health_advice_id="<?= $health_advice_id; ?>" likes="<?= $health_advice->health_advice_likes;?>"><i class="fa fa-thumbs-up" aria-hidden="true"></i><span><?= $health_advice->health_advice_likes;?></span></a>
                        </div>
                        <p><?= nl2br($health_advice->health_advice_desc);?></p>
                    </div>
                    <div class="blog-reply">
                        <h4><?= count($comments) > 0 ? count($comments).' ' : ''?>Comments</h4>
                        <?php
                        if(count($comments) > 0) {
                            foreach ($comments as $key => $value) {     
                        ?>
                        <ul class="commentlist">
                            <li class="comment even thread-even depth-1" id="li-comment-409476">
                                <article class="comment-body">
                                    <div class="comment-meta commentmetadata">
                                        <h5><?= $value->comment_name;?></h5>
                                        <span class="comment_date"><b><?= get_display_date_time('d M Y',$value->created_at);?></b></span>
                                    </div>
                                    <div class="comment-content clearfix">
                                        <p><?= nl2br($value->comment);?></p>
                                    </div> 
                                </article> 
                            </li>
                        </ul>
                        <br>
                        <?php } } ?>
                        <center><div id="comment_server_side_error"></div></center>
                        <center><div id="comment_result"></div></center>
                        <form id="comment-form" method="POST">
                            <input type="hidden" name="health_advice_id" value="<?= $health_advice_id; ?>">
                            <input type="hidden" name="patient_health_advice_id" value="<?= $patient_health_advice_id; ?>">
                            <input type="hidden" name="patient_id" value="<?= $patient_id; ?>">
                            <div class="row">
                                <div class="col-lg-6">
                                    <input type="text" name="comment_name" maxlength="100" placeholder="Enter Your Name">
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" name="comment_phone" maxlength="10" placeholder="Enter Your Phone No.">
                                </div>
                                <div class="col-lg-12">
                                    <input type="email" name="comment_email" maxlength="150" placeholder="Enter Your Email">
                                </div>
                                <div class="col-lg-12">
                                    <textarea placeholder="Messege" class="comment-message" name="message"></textarea>
                                </div>
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="captcha-img col-lg-3">
                                            
                                        </div>
                                        <div class="col-lg-1" style="margin-top: 6px;font-size: 23px;">
                                            <a href="javascript:void(0);" title="Refresh Captcha" onclick="captcha();"><i class="fa fa-refresh"></i></a>
                                        </div>
                                        <div class="col-lg-4">
                                            <input type="text" name="comment_captcha" maxlength="6" placeholder="Captcha">
                                            <label id="invalid_comment_captcha_error" class="error" for="comment_captcha" style="display: none;"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <button type="submit" name="replysubmit">Post Comment</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="sidebar">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="share-socila-link">
                                    <div class="social-hading">
                                        <h4>Share This Health Advice</h4>
                                    </div>
                                    <div class="social-icon">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <ul class="social-icons list-unstyled">
                                                    <li>
                                                        <a target='_blank' href="<?=str_replace('{{TEXT}}',urlencode($health_advice->health_advice_name),str_replace('{{URL}}',urlencode(current_url()),SOCIAL_MEDIA_SHARING_BUTTON_LINKS['FACEBOOK']));?>" class="facebook"><i class="fa fa-facebook"></i></a>
                                                    </li>
                                                    <li>
                                                        <a target='_blank' href="<?=str_replace('{{TEXT}}',urlencode($health_advice->health_advice_name),str_replace('{{URL}}',urlencode(current_url()),SOCIAL_MEDIA_SHARING_BUTTON_LINKS['INSTAGRAM']));?>" class="instagram"><i class="fa fa-instagram"></i></a>
                                                    </li>
                                                    <li>
                                                        <a target='_blank' href="<?=str_replace('{{TEXT}}',urlencode($health_advice->health_advice_name),str_replace('{{URL}}',urlencode(current_url()),SOCIAL_MEDIA_SHARING_BUTTON_LINKS['TWITTER']));?>" class="twitter"><i class="fa fa-twitter"></i></a>
                                                    </li>
                                                    <li>
                                                        <a target='_blank' href="<?=str_replace('{{TEXT}}',urlencode($health_advice->health_advice_name),str_replace('{{URL}}',urlencode(current_url()),SOCIAL_MEDIA_SHARING_BUTTON_LINKS['WHATSAPP']));?>" class="whatsapp"><i class="fa fa-whatsapp"></i></a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="sidebar">
                        <div class="widget">
                            <h4>Categories</h4>
                            <ul>
                                <?php foreach ($health_advice_groups as $key => $value) { 
                                    $req = ['p_id' => $patient_id, 'p_h_id' => $value->patient_health_advice_id, 'h_g_id' => $value->patient_health_advice_group_id, 'order' => $value->patient_health_advice_last_send_order];
                                    ?>
                                <li><a class="nav-link" href="<?= site_url('health_advice_list/' . base64_encode(json_encode($req))); ?>"><?= $value->health_advice_group_name . ' (' . $value->patient_health_advice_last_send_order . ')'; ?></a></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php $this->load->view('web/footer'); ?>

    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/resetpassword.css">

    <?php $this->load->view('web/footer_layout'); ?>
 
<script>
    $(document).ready(function () {
        $("#comment-form").validate({
            rules: {
                comment_name: {
                    required: true
                },
                comment_phone: {
                    digits: true,
                    minlength: 10,
                    required: true
                },
                comment_email: {
                    required: true,
                    email: true
                },
                message: {
                    required: true
                },
                comment_captcha: {
                    required: true
                }
            },
            messages: {
                comment_name: "Please enter name",
                message: "Please enter message",
                comment_captcha: "Please enter captcha",
                comment_email: {required: "Please enter email", email: "Your email address is invalid"},
                comment_phone: {required: "Please enter phone number", digits: "Your phone number is invalid", minlength: "Your phone number is invalid"}
            },
            submitHandler: function() { 
                $("#invalid_comment_captcha_error").hide();
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    data: $("#comment-form").serialize(),
                    url: "<?php echo site_url('web/save_comment_post'); ?>",
                    success: function (data) {
                        if (data.error) {
                            $("#comment_server_side_error").html(data.error);
                            $("#comment_server_side_error").addClass("alert alert-danger");
                        }
                        if(data.invalid_captcha == true) {
                            $("#invalid_comment_captcha_error").html("Invalid captcha").show();
                        }
                        if (data == true) {
                            $("#comment-form").hide();
                            $("#comment_result").html('Your comment submitted successfully!');
                            $("#comment_result").addClass("alert alert-success");
                        }
                    }
                });
            },
            highlight: function (element) {
                $(element).parent().addClass('error')
            },
            unhighlight: function (element) {
                $(element).parent().removeClass('error')
            }
        });  
        $(".like-count").click(function() {
            if($(".like-count").attr('is_like') == "1") {
                $(".like-count").attr('is_like', "0");
                $.ajax({
                    type: 'POST',
                    data: {like_count: $(".like-count").attr('likes'), health_advice_id: $(".like-count").attr('health_advice_id')},
                    dataType: 'json',
                    url: "<?php echo site_url('web/likes'); ?>",
                    success: function (data) {
                        $(".like-count").find("span").html(data);
                    }
                });
            }
        });
    });
    captcha();
    function captcha() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('web/captcha_code'); ?>",
            success: function (data) {
                $(".captcha-img").html(data.image);
            }
        });
    }
</script>