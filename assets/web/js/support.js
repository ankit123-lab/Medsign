$(document).ready(function () {
	$("#support-report-form").validate({
        rules: {
            issue_message: {
                required: true
            },
            comment_captcha: {
                required: true
            },
            issue_email: {
                email: true
            }
        },
        messages: {
            issue_message: "Please enter message",
            comment_captcha: "Please enter captcha",
            issue_email: {email: "Your email address is invalid"}
        },
        submitHandler: function(form) { 
            $("#add_issue_btn").attr('disabled', true);
            form.submit();
        },
        highlight: function (element) {
            
        },
        unhighlight: function (element) {
            
        }
    });
});