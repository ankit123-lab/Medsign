<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
    #static_content .panel-heading .accordion-toggle:after{
        font-family:'Glyphicons Halflings';
        content:"\e114";
        float:right;
        color:#000
    }
    #static_content .panel-heading .accordion-toggle.collapsed:after{
        content:"\e080"
    }
    .default-theme a:focus, .default-theme a:active, .default-theme a.active {
        text-decoration: none;
        color: #000;
    }
</style>
<div class="container login_reigstration default_section">
    <!-- Example row of columns -->
    <div class="row">
        <div class="col-md-12 col-xs-12 ol-md-offset-3">
            <div class="login_panel_heading panel-heading no_padding">
                <div class="row m_top_20 m_bottom_40 col-md-12" id="static_content">
                    <h2 class="title">FAQ</h2>
                    <div class="panel-group" id="accordion">
                        <?php
                        foreach ($faq_array as $faq) {
                            ?>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#<?=$faq['id']?>" aria-expanded="false">
                                            <?=$faq['question']?>
                                        </a>
                                    </h4>
                                </div>
                                <div id="<?=$faq['id']?>" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                    <div class="panel-body">
                                        <p>
                                            <?=$faq['answer']?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> <!-- /container -->
<script type="text/javascript">
    var pad = jQuery(".navbar").outerHeight() + 50;
    jQuery(".login_reigstration").css('padding-top', pad + 'px');
</script>
