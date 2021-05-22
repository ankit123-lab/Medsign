<?php

defined('BASEPATH') OR exit('No direct script access allowed');

//my controller message
$lang['mycontroller_invalid_request']                   = "Invalid request data!";
$lang['mycontroller_invalid_token']                     = "Invalid security token";
$lang['mycontroller_problem_request']                   = "Problem in processing request!";
$lang['mycontroller_bad_request']                       = "Invalid request parameters!";

//login controller
$lang['common_invalid_number']                          = "Please enter valid mobile number";
$lang['common_invalid_otp']                             = "Provided OTP is invalid";
$lang['common_number_not_register_with_us']             = "This number is not registered with us.";
$lang['user_verfication_pending']                       = "Provide few details and create your account now.";
$lang['user_reject_from_our_system']                    = "This mobile number is rejected from our system";
$lang['user_inactive_from_our_system']                  = "User deactivated by system";
$lang['otp_sent_to_mobile']                             = "OTP code sent to your mobile number";
$lang['sms_otp_send_fail']                              = "Problem while sending SMS. Please try again...!";
$lang['problem_in_action_to_send_sms']                  = "Problem in sending sms.";
$lang['problem_in_action_to_verify_otp']                = "Problem while verfiy OTP. Please try again...!";
$lang['please_enter_otp']                               = "Please enter valid OTP.";
$lang['otp_token_expire']                               = "Your OTP code has been expired.";
$lang['otp_wrong']                                      = "You have entered wrong OTP.";
$lang['login_successful']                               = "Login Successfully.";
$lang['otp_resend_success']                             = "OTP sent successfully.";
$lang['otp_verified_successful']                        = "Your OTP code verified";

//login with email and password
$lang['email_password_mendentory']                      = "Email & Password is mendentory.";
$lang['email_password_combination_not_match']           = "Email & Password combination is not matched with system.";
$lang['email_not_found']                                = "This email address is not registered with us.";
$lang['problem_in_action_to_verify_email']              = "Problem in verify email address. please try again.";
$lang['enter_valid_email_address']                      = "Please enter valid email address.";
$lang['enter_valid_password']                           = "Please enter your password.";

// reset password 
$lang['email_mendentory']                               = "Email is mendentory.";
$lang['user_forgotpassword_success']                    = "Reset password mail send successfully.";

//create account
$lang['mobile_already_registered']                      = "This mobile no is already register with out system.";

//resetpassword controller
$lang['resetpassword_token_empty']                      = "Oops, something went wrong. Missing security token.";
$lang['resetpassword_token_invalid']                    = "Oops, something went wrong. Invalid security code!";
$lang['resetpassword_reset_empty']                      = "Invalid request parameters!";
$lang['resetpassword_reset_success']                    = "Password reset successfully.Use new password for login";
$lang['resetpassword_reset_fail']                       = "Problem while reset password";

//User controller
$lang['user_login_success']                             = "Successfully logged in";
$lang['user_login_token_generate_error']                = "Error in generate access token";
$lang['user_login_fail']                                = "Invalid email or password";
$lang['user_login_block']                               = "Your account is block by admin";
$lang['user_register_email_exist']                      = "This email address already register with us.";
$lang['user_register_success']                          = "Registration done successfully.";
$lang['user_register_fail']                             = "Error while registering user.";
$lang['user_logout_success']                            = "User is successfully logged out.";
$lang['user_logout_fail']                               = "Error while perform logout.";
$lang['user_forgotpassword_success']                    = "Reset password mail send successfully.";
$lang['user_forgotpassword_fail']                       = "This email address is not registered with us.";

/* Common */
$lang['common_search_placeholder']                      = "Search...";
$lang['common_items_selected']                          = "Selected records";
$lang['common_no_country_found']                        = "No counry found";
$lang['common_no_state_found']                          = "No state found";
$lang['common_no_city_found']                           = "No city found";
$lang['common_select_country']                          = "Select country";
$lang['common_select_state']                            = "Select state";
$lang['common_select_city']                             = "Select city";
$lang['common_select_all']                              = "Select All";
$lang['common_deselect_all']                            = "Deselect All";
$lang['common_no_result_matched']                       = "No results matched";
$lang['common_error_in_change_language']                = "Error while change language";
$lang['failure']                                        = "Something went wrong";
$lang['email_verify']                                   = "Your account is verified";