(function ($) {
	"use strict";
	
	/*----------------------------
    Responsive menu Active
    ------------------------------ */
	$(".mainmenu ul#primary-menu").slicknav({
		allowParentLinks: true,
		prependTo: '.responsive-menu',
	});
	
	/*----------------------------
    START - Menubar scroll animation
    ------------------------------ */
	jQuery(window).on('scroll', function() {
		if ($(this).scrollTop() > 10) {
			$('.header').addClass("sticky");
		} else {
			$('.header').removeClass("sticky");
		}
	});
	
	/*----------------------------
    START - Smooth scroll animation
    ------------------------------ */
	$('.mainmenu li a, .logo a,.slicknav_nav li a').on('click', function () {
		if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'')
		&& location.hostname == this.hostname) {
		  var $target = $(this.hash);
		  $target = $target.length && $target
		  || $('[name=' + this.hash.slice(1) +']');
		  if ($target.length) {
			var targetOffset = $target.offset().top;
			$('html,body')
			.animate({scrollTop: targetOffset}, 2000);
		   return false;
		  }
		}
	});
	
	/*----------------------------
    START - Scroll to Top
    ------------------------------ */
	$(window).on('scroll', function() {
		if ($(this).scrollTop() > 600) {
			$('.scrollToTop').fadeIn();
		} else {
			$('.scrollToTop').fadeOut();
		}
	});
	$('.scrollToTop').on('click', function () {
		$('html, body').animate({scrollTop : 0},2000);
		return false;
	});
		
	/*----------------------------
    START - Preloader
    ------------------------------ */
	jQuery(window).on('load', function(){
		jQuery("#preloader").fadeOut(100);
	});

	$('.onlyNumbers').keypress(function(event){
	    if(event.which != 8 && isNaN(String.fromCharCode(event.which))){
	        event.preventDefault();
	}});
	
	$('.uas7-form-btns .btn').on('click', function () {
		$('.uas7-form-btns .btn').removeClass('active');
		$(this).addClass('active');
	});

	$(".change_patient").click(function() {
		$.ajax({
            type: 'POST',
            dataType: 'json',
            url: site_url + "patient/patient_list",
            beforeSend: function() {
				$("#patientListModal > .modal-dialog").html("");
				$("#patientListModal").modal("show");
            },
            success: function(data) {
                $("#patientListModal > .modal-dialog").html(data.html);            	
            }
        });
	});
	$("body").on("click","#change_patient_btn", function() {
		$.ajax({
            type: 'POST',
            dataType: 'json',
            data: $("#change_patient_frm").serialize(),
            url: site_url + "patient/change_patient",
            beforeSend: function() {
				
            },
            success: function(data) {
                location.reload();
            }
        });
	});
	
}(jQuery));
