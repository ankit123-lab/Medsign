<script type="text/javascript">
  function initMap() { 
    var uluru = {lat: 13.0515405, lng: 77.59540449999997}; 
    var map = new google.maps.Map(document.getElementById('map'), { 
      zoom: 15, 
      center: uluru 
    }); 
    var marker = new google.maps.Marker({ 
      position: uluru, 
      map: map,
      title:"Godrej Woodsman Estate, Tower 4, B-401, Next to Kirloskar Business Park, Hebbal, Bengaluru KA 560024, India."
    }); 
  }
</script> 
<script type="text/javascript">
        $(document).ready(function () {
            $("#contact-form").validate({
                rules: {
                    name: {
                        required: true
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    phone_number: {
                        required: true,
                        minlength: 10,
                        digits: true
                    },
                    subject: {
                        required: true
                    },
                    message: {
                        required: true
                    }
                },
                messages: {
                    name: 'Please enter your name',
                    email: "Please enter valid email",
                    phone_number: {required:"Please enter mobile number",digits:"Please enter valid number",minlength: "Please enter valid number"},
                    subject: "Please enter subject",
                    message: "Please enter message"
                },
                highlight: function (element) {
                    // $(element).parent().addClass('error')
                },
                unhighlight: function (element) {
                    // $(element).parent().removeClass('error')
                }
            });
            /*form submit through ajax*/
            $("#contact-form").on('submit', function (e) {
                e.preventDefault();
                var name = $(".name").val();
                var email = $(".email").val();
                var subject = $(".subject").val();
                var message = $(".message").val();
                if (name && email && subject && message) {
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        data: $("#contact-form").serialize(),
                        url: "<?php echo site_url('Web/save_getintouch_post'); ?>",
                        success: function (data) {
                            if (data.error) {
                                $("#server_side_error").html(data.error);
                                $("#server_side_error").addClass("alert alert-danger");
                            }
                            if (data == true) {
                                $("#contact-form").hide();
                                $("#result").html('Your request send successfully!');
                                $("#result").addClass("alert alert-success");
                            }
                        }
                    });
                }
            });
            /*subscriber*/
            jQuery("#subscriber").validate({
                rules: {
                    sub_email: {
                        required: true,
                        email: true
                    }
                },
                messages: {
                    sub_email: ""
                },
                highlight: function (element) {
                    jQuery(element).parent().addClass('error')
                },
                unhighlight: function (element) {
                    jQuery(element).parent().removeClass('error')
                }
            });
             $(function () {
                jQuery("#subscriber").on('submit', function (e) {
                    e.preventDefault();
                    var sub_email = $('.sub_email').val();
                    if (sub_email) {
                        jQuery.ajax({
                            type: 'POST',
                            dataType: 'json',
                            data: jQuery("#subscriber").serialize(),
                            url: "<?php echo site_url('Web/save_subscriber_post'); ?>",
                            success: function (data) {
                                if (data.error) {
                                    $(".hide").hide();
									$("#subscriber_err_msg").html(data.error);
                                    $("#subscriber_err_msg").addClass("alert alert-danger");
                                }
                                if (data == true) {
                                    $(".hide").hide();
                                    $("#subscriber_suc_msg").html('Successfully subscribed!');
                                    $("#subscriber_suc_msg").addClass("alert alert-success");
                                }
                            }
                        });
                    }
                });
            });
        });
        </script>