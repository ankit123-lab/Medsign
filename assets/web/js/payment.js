$(document).ready(function () {
	$(".payment_popup").click(function(){
		$.ajax({
            type: 'POST',
            dataType: 'json',
            data: {"utility_name": $(this).attr("utility_name")},
            url: site_url + "patient/payment_popup",
            beforeSend: function() {
				$("#paymentModal > .modal-dialog").html("");
				$("#paymentModal").modal("show");
            },
            success: function(data) {
                $("#paymentModal > .modal-dialog").html(data.html);            	
            }
        });
	});

	$("body").on("click", "#pay_now_btn", function() {
		$.ajax({
            type: 'POST',
            dataType: 'json',
            data: $("#create_payment_order").serialize(),
            url: site_url + "patient/create_payment_order",
            beforeSend: function() {
				$("#pay_now_btn").attr('disabled', true);
            },
            success: function(data) {
                var options = {
                    "key": data.key,
                    "currency": "INR",
                    "name": "MedSign",
                    "description": data.description,
                    "image": site_url + "assets/images/logo_dashoboard.png",
                    "order_id": data.order_id,
                    "handler": function (razorpay_response) {
                        if(razorpay_response.razorpay_payment_id != undefined && razorpay_response.razorpay_payment_id != '') {
                            razorpay_response.paid_amount = data.paid_amount;
                            paymentCreditsCapture(razorpay_response);
                        }
                    },
                    "prefill": {
                        "name": data.name,
                        "email": data.email,
                        "contact": data.contact
                    },
                    "notes": {
                        "address": data.address
                    },
                    "theme": {
                        "color": "#30aca5"
                    }
                };
                var rzp1 = new Razorpay(options);
                rzp1.open();
                $("#pay_now_btn").attr('disabled', false);
            }
        });
	});
	function paymentCreditsCapture(razorpay_response) {
		$.ajax({
            type: 'POST',
            dataType: 'json',
            data: {"paid_amount": razorpay_response.paid_amount,"payment_id": razorpay_response.razorpay_payment_id,"order_id": razorpay_response.razorpay_order_id,"signature": razorpay_response.razorpay_signature},
            url: site_url + "patient/payment_capture",
            beforeSend: function() {
				
            },
            success: function(data) {
                if(data.status) {
                	window.location.href = site_url + "patient/payment_success?payment_id=" + data.payment_id;
                }
            }
        });
	}
});