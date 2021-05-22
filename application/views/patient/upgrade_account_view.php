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
                    <div class="row">
                        <div class="col-lg-12 btn-m-0">
                            <h1 class="page-title"><?= $breadcrumbs; ?></h1>
                        </div>
                    </div>
                </div>
                <?php if(!empty($expiry_date) && $expiry_date < date("Y-m-d")) : ?>
                    <div class="alert alert-danger">
                        <strong>Payment!</strong> Your plan is expired on <?= date("d/m/Y", strtotime($expiry_date)) ?>. Please make payment.
                    </div>
                <?php endif; ?>
                <div class="col-lg-12 btn-m-0">
                    <div class="contact-form">
                        <div class="row">
                            <div class="col-lg-12">
                                <form name="payment_order" id="payment_order">
                                    <input type="hidden" name="sub_plan_id" value="<?= $subscription['sub_plan_id'] ?>">
                                    <table class="payment-table" style="width: 100%;">
                                        <tbody>
                                            <tr>
                                                <td align="right">Amount</td>
                                                <td align="right"><?= number_format($subscription['sub_price'],2) ?></td>
                                            </tr>
                                            <tr>
                                                <td align="right">GST(<?= $subscription['sub_tax_igst'] ?>%)</td>
                                                <td align="right"><?= number_format($gst_amount,2); ?></td>
                                            </tr>
                                            <tr>
                                                <td align="right">Total</td>
                                                <td align="right"><?= number_format($total_price,2); ?></td>
                                            </tr>
                                            <tr>
                                                <td align="right" colspan="2">
                                                    <button type="button" class="btns-hide" id="pay_now_btn">Pay Now</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
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
