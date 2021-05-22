        <script src="<?= ASSETS_PATH ?>/web/js/website.min.js"></script>
        <?php /* ?>
		<!-- jquery main JS -->
        <script src="<?= ASSETS_PATH ?>/web/js/jquery.min.js"></script>
        <!-- Bootstrap JS -->
        <script src="<?= ASSETS_PATH ?>/web/js/bootstrap.min.js"></script>
        <!-- Slick nav JS -->
        <script src="<?= ASSETS_PATH ?>/web/js/jquery.slicknav.min.js"></script>
        <!-- Slick JS -->
        <script src="<?= ASSETS_PATH ?>/web/js/slick.min.js"></script>
        <!-- owl carousel JS -->
        <script src="<?= ASSETS_PATH ?>/web/js/owl.carousel.min.js"></script>
        <!-- Popup JS -->
        <script src="<?= ASSETS_PATH ?>/web/js/jquery.magnific-popup.min.js"></script>
        <!-- Counterup waypoints JS -->
        <script src="<?= ASSETS_PATH ?>/web/js/waypoints.min.js"></script>
        <!-- YTPlayer JS -->
        <script src="<?= ASSETS_PATH ?>/web/js/jquery.mb.YTPlayer.min.js"></script>
        <!-- WOW JS -->
        <script src="<?= ASSETS_PATH ?>/web/js/wow-1.3.0.min.js"></script>
        <script src="<?= ASSETS_PATH ?>/web/js/jquery.validate.js"></script>
        <?php */ ?>
        <!-- main JS -->
        <script src="<?= ASSETS_PATH ?>/web/js/main.js?<?= WEB_VERSION; ?>"></script>
        <?php if(isset($is_load_reset_password_js) && $is_load_reset_password_js == true){?>
            <script src="<?= ASSETS_PATH ?>/web/js/reset_password.js?<?= WEB_VERSION; ?>"></script>
        <?php }else{ ?>
        <!-- Gmap JS -->
		<script type="text/javascript">
		  function initMap(){var e={lat:13.0515405,lng:77.59540449999997},a=new google.maps.Map(document.getElementById("map"),{zoom:15,center:e});new google.maps.Marker({position:e,map:a,title:"Godrej Woodsman Estate, Tower 4, B-401, Next to Kirloskar Business Park, Hebbal, Bengaluru KA 560024, India."})}
		</script> 
		<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLEMAPAPIKEY; ?>&callback=initMap"></script>
	<?php } ?>
        <script type="text/javascript">
            function contact_captcha(){$.ajax({type:"POST",dataType:"json",url:"<?php echo site_url('web/contact_form_captcha'); ?>",success:function(e){$(".captcha-img").html(e.image)}})}$(document).ready(function(){$("#contact-form").validate({rules:{name:{required:!0},email:{required:!0,email:!0},phone_number:{required:!0,minlength:10,digits:!0},subject:{required:!0},comment_captcha:{required:!0},message:{required:!0}},messages:{name:"Please enter your name",email:"Please enter valid email",phone_number:{required:"Please enter mobile number",digits:"Please enter valid number",minlength:"Please enter valid number"},subject:"Please enter subject",comment_captcha:"Please enter captcha",message:"Please enter message"},submitHandler:function(e){$.ajax({type:"POST",dataType:"json",data:$("#contact-form").serialize(),url:"<?php echo site_url('Web/save_getintouch_post'); ?>",success:function(e){e.error&&($("#server_side_error").html(e.error),$("#server_side_error").addClass("alert alert-danger"),$("html, body").animate({scrollTop:$("#server_side_error").offset().top-100},500)),1==e&&($("#server_side_error").hide(),$("#contact-form").hide(),$("#result").html("Your request send successfully!"),$("#result").addClass("alert alert-success"),$("html, body").animate({scrollTop:$("#result").offset().top-100},500))}})},highlight:function(e){},unhighlight:function(e){}}),jQuery("#subscriber").validate({rules:{sub_email:{required:!0,email:!0}},messages:{sub_email:""},highlight:function(e){jQuery(e).parent().addClass("error")},unhighlight:function(e){jQuery(e).parent().removeClass("error")}}),$(function(){jQuery("#subscriber").on("submit",function(e){e.preventDefault(),$(".sub_email").val()&&jQuery.ajax({type:"POST",dataType:"json",data:jQuery("#subscriber").serialize(),url:"<?php echo site_url('Web/save_subscriber_post'); ?>",success:function(e){e.error&&($(".hide").hide(),$("#subscriber_err_msg").html(e.error),$("#subscriber_err_msg").addClass("alert alert-danger")),1==e&&($(".hide").hide(),$("#subscriber_suc_msg").html("Successfully subscribed!"),$("#subscriber_suc_msg").addClass("alert alert-success"))}})})})}),contact_captcha();
        </script>
    </body>
</html>