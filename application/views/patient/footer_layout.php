        <script src="<?= ASSETS_PATH ?>web/js/patient-web.min.js"></script>
        <?php /* ?>
		<!-- jquery main JS -->
        <script src="<?= ASSETS_PATH ?>web/js/jquery.min.js"></script>
        <!-- Bootstrap JS -->
        <script src="<?= ASSETS_PATH ?>web/js/popper.min.js"></script>
        <script src="<?= ASSETS_PATH ?>web/js/bootstrap.min.js"></script>
        <!-- Slick nav JS -->
        <script src="<?= ASSETS_PATH ?>web/js/jquery.slicknav.min.js"></script>
        <!-- Slick JS -->
        <script src="<?= ASSETS_PATH ?>web/js/slick.min.js"></script>
        <script src="<?= ASSETS_PATH ?>web/js/jquery.mask.min.js"></script>
        <script src="<?= ASSETS_PATH ?>web/js/bootstrap-datetimepicker.min.js"></script>
        <script src="<?= ASSETS_PATH ?>web/js/jquery.validate.js"></script>
        <script src="<?= ASSETS_PATH ?>web/js/additional-methods.min.js"></script>
        <script src="<?= ASSETS_PATH ?>web/js/select2.full.min.js"></script>
        <?php */ ?>
        <script src="<?= ASSETS_PATH ?>web/js/patient_main.js?<?= WEB_VERSION; ?>"></script>
        <?php
        $page_name = $this->uri->segment(2);
        if($page_name == 'profile') { ?>
            <script src="<?= ASSETS_PATH ?>web/js/aes.js"></script>
            <script src="<?= ASSETS_PATH ?>web/js/encrypt_decrypt.js"></script>
        <?php }
        if(!empty($page_name) && in_array($page_name, ['add_report'])) {
            $js_file_name = 'report.js';
        } elseif(!empty($page_name) && in_array($page_name, ['vitals','add_vital','edit_vital'])) { ?>
            <script src="<?= ASSETS_PATH ?>web/js/bootstrap-dropdown.js"></script>
            <script src="<?= ASSETS_PATH ?>web/jquery-datatable/jquery.dataTables.js"></script>
            <script src="<?= ASSETS_PATH ?>web/js/google_chart_loader.js"></script>
        <?php 
            $js_file_name = 'vitals.js';
        } elseif(!empty($page_name) && in_array($page_name, ['appointment_list','appointment_book','book_now'])) {
            $js_file_name = 'appointment.js';
        } elseif(!empty($page_name) && in_array($page_name, ['add_member'])) {
            $js_file_name = 'family_member.js';
        } elseif(!empty($page_name) && in_array($page_name, ['forgot'])) {
            $js_file_name = 'login.js';
        } elseif(!empty($page_name) && in_array($page_name, ['add_issue'])) {
            $js_file_name = 'support.js';
        } elseif(!empty($page_name) && in_array($page_name, ['add_uas7_para','uas7diary','edit_uas7_para'])) {
            if($page_name == "uas7diary") { ?>
                <script src="<?= ASSETS_PATH ?>web/js/google_chart_loader.js"></script>
                <script src="<?= ASSETS_PATH ?>web/js/razorpay_checkout.js"></script>
                <script src="<?= ASSETS_PATH ?>web/js/payment.js"></script>
            <?php }
            $js_file_name = 'uas7diary.js';
            ?>
            <script src="<?= ASSETS_PATH ?>web/js/typeahead.js"></script>
        <?php
        } elseif(!empty($page_name) && in_array($page_name, ['utilities_list'])) { ?>
            <script src="<?= ASSETS_PATH ?>web/js/razorpay_checkout.js"></script>
            <script src="<?= ASSETS_PATH ?>web/js/payment.js"></script>
        <?php
            $js_file_name = 'utilities.js';
        } elseif(!empty($page_name) && in_array($page_name, ['upgrade'])) { ?>
            <script src="<?= ASSETS_PATH ?>web/js/razorpay_checkout.js"></script>
        <?php
            $js_file_name = 'upgrade.js';
        } elseif(!empty($page_name) && in_array($page_name, ['analytics_list'])) { ?>
            <script src="<?= ASSETS_PATH ?>web/js/google_chart_loader.js"></script>
        <?php
            $js_file_name = 'analytics_list.js';
        } elseif(!empty($page_name)){
            $js_file_name = $page_name . '.js';
        } else {
            $js_file_name = "";
        }
        if($js_file_name != "" && file_exists(DOCROOT_PATH."assets/web/js/".$js_file_name)) {
        ?>
        <script src="<?= ASSETS_PATH ?>web/js/<?= $js_file_name.'?'.WEB_VERSION; ?>"></script>
        <?php } ?>
    </body>
</html>