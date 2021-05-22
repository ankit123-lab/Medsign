<?php

defined('BASEPATH') OR exit('No direct script access allowed');

//header menu
$lang['header_home']                                    = "Home";
$lang['header_about_us']                                = "About Us";
$lang['header_plan']                                    = "Plan";
$lang['header_contact_us']                              = "Contact Us";
$lang['header_login']                                   = "Login";
$lang['header_register']                                = "Register";

//my controller message
$lang['mycontroller_invalid_request']                   = "Invalid request data!";
$lang['mycontroller_invalid_token']                     = "Invalid security token";
$lang['mycontroller_problem_request']                   = "Problem in processing request!";
$lang['mycontroller_bad_request']                       = "Invalid request parameters!";

//login controller
$lang['login_login_with_email']                         = "Login with email ID?";
$lang['login_login_with_mobile']                        = "Login with mobile number?";
$lang['login_forgot_password']                          = "Forgot Password?";
$lang['login_successful']                               = "Login Successfully.";

// Send otp
$lang['problem_in_action_to_send_sms']                  = "Problem in sending sms.";
$lang['problem_in_action_to_verify_otp']                = "Problem while verfiy OTP. Please try again...!";
$lang['otp_sent_to_mobile']                             = "OTP code sent to your mobile number";
$lang['otp_token_expire']                               = "Your OTP code has been expired.";
$lang['otp_resend_success']                             = "OTP sent successfully.";
$lang['otp_verified_successful']                        = "Your OTP code verified";

//login with email and password
$lang['email_password_mendentory']                      = "Email & Password is mendentory.";
$lang['email_password_combination_not_match']           = "Email & Password combination is not matched with system.";
$lang['email_not_found']                                = "This email address is not registered with us.";
$lang['email_problem_in_verify_email']                  = "Problem in verify email address. please try again.";

//resetpassword controller
$lang['forgot_email_placeholder']                       = "Please enter your registered email ID";
$lang['forgot_to_login']                                = "Login ?";
$lang['resetpassword_token_empty']                      = "Oops, something went wrong. Missing security token.";
$lang['resetpassword_token_invalid']                    = "Invalid token!";
$lang['resetpassword_reset_empty']                      = "Invalid token!";
$lang['resetpassword_reset_success']                    = "Password reset successfully.Use new password for login";
$lang['resetpassword_reset_fail']                       = "Problem while reset password";
$lang['resetpassword_token_expire']                     = "Token expired";

//User controller
$lang['user_verfication_pending']                       = "Provide few details and create your account now.";
$lang['user_reject_from_our_system']                    = "This mobile number is rejected from our system";
$lang['user_inactive_from_our_system']                  = "User deactivated by system";
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

$lang['common_items_selected']                          = "Selected records";
$lang['common_no_country_found']                        = "No counry found";
$lang['common_no_state_found']                          = "No state found";
$lang['common_no_city_found']                           = "No city found";
$lang['common_select_country']                          = "Select country";
$lang['common_select_state']                            = "Select state";
$lang['common_select_city']                             = "Select city";
$lang['common_mobile_number']                           = "Mobile number";
$lang['common_send_otp_btn']                            = "Send OTP";
$lang['common_verify_otp']                              = "Verify OTP";
$lang['common_send_btn']                                = "Send";
$lang['common_login_btn']                               = "Login";
$lang['common_email_id']                                = "Email ID";
$lang['common_password']                                = "Password";
$lang['common_create_password']                         = "Create Password";
$lang['common_country']                                 = "Country";
$lang['common_state']                                   = "State";
$lang['common_city']                                    = "City";

$lang['common_verify_otp_placeholder']                  = "Enter OTP";
$lang['common_mobile_number_placeholder']               = "Enter mobile number";
$lang['common_email_id_placeholder']                    = "Enter email id";
$lang['common_password_placeholder']                    = "Enter password";
$lang['common_search_placeholder']                      = "Search...";

$lang['common_select_all']                              = "Select All";
$lang['common_deselect_all']                            = "Deselect All";
$lang['common_no_result_matched']                       = "No results matched";
$lang['common_error_in_change_language']                = "Error while change language";
$lang['common_invalid_number']                          = "Please enter valid mobile number";
$lang['common_invalid_otp']                             = "Provided OTP is invalid";
$lang['common_email_required']                          = "Please enter valid email address.";
$lang['common_password_required']                       = "Please enter your password.";
$lang['common_otp_required']                            = "Please enter valid OTP.";
$lang['common_number_already_registered']               = "This mobile number is already registered with us.";
$lang['common_number_not_register_with_us']             = "This number is not registered with us.";
$lang['common_otp_send_fail']                           = "Problem while sending SMS. Please try again...!";
$lang['failure']                                        = "Something went wrong";
$lang['email_verify']                                   = "Your account is verified";
$lang['shared_prescription'] = "Dear %s your prescription from %s at %s on %s is shared with you.
Click %s";
$lang['shared_whatsapp_prescription'] = "Dear %s your prescription form %s at %s on %s is shared with you.";
/*$lang['doctor_sms_after_one_hour'] = "Hello %s,
Send below text msg thru SMS/WA to your patients informing your availability on MedSign for tele-consultation.

Greetings from %s Hope you are in best of health.
Due to Covid-19 pandemic and lockdown restrictions, I am available for tele-consultation on MedSign digital health platform.
Now you can book appointments, seek online video/tele-consultation, upload reports, receive e-prescription, get sms/mail reminders and pay online - all securely.
Click here %s to Register yourself on MedSign Patient Platform and avail all benefits
Stay Home, Stay Safe, Stay connected thru MedSign.
Take Care,
Best wishes.";*/
$lang['doctor_sms_after_one_hour'] = "Hello %s,
Send below text msg thru SMS/WA to your patients informing your availability on Medsign for teleconsultation.

Greetings from %s Hope you are in best of health.
Due to Covid-19 pandemic and lockdown restrictions, I am available for tele-consultation on MedSign digital health platform.
Now you can book appointments, seek online video/teleconsultation, upload reports, receive e-prescription, get sms/mail reminders and pay online - all securely.
Click here %s to Register yourself on MedSign Patient Platform and avail all benefits
Stay Home, Stay Safe, Stay connected thru Medsign.
Take Care,
Best wishes.";
$lang['forgot_password_sms_link'] = "Hello %s, Forgot you password?
Don't worry, we are here to help you! Just click on below link and follow instructions.
%s
Wishing you best health.
Team MedSign.";
$lang['wa_template_32_forgot_password_sms_link'] = "Hello %s, Forgot you password?\nDon't worry, we are here to help you! Just click on below link and follow instructions.\n%s\nWishing you best health.\nTeam MedSign.";
$lang['appointment_book_notification_patient'] = "Dear %s your appointment booked successfully with %s at %s on %s ";
$lang['doctor_not_available'] = "Sorry..Doctor not available in this time slot";
$lang['not_able_to_cancel'] = "You can't able to cancel the appointment now";
$lang['appointment_cancel_message'] = "Appointment cancel succesfully";
$lang['appointment_cancel'] = "Dear %s your appointment with %s at %s on %s is cancelled."; //SMS/N
$lang['patient_join_video_call_link_sms'] = "Dear %s, find enclosed link for your tele-consultation with %s on %s. The tele-consultation will start in next %s min. By clicking on the link you are giving your consent for tele-consultation and agree with the MedSign Privacy Policy and T and C. 
Link: %s";
$lang['doctor_push_message'] = "Patient waiting for tele-consultation video call. \nPatient name: %s \nAppointment time: %s";
$lang['no_video_call_credit_message'] = "Your call disconnected due to no enough credit.";
$lang['patient_call_disconnect_message'] = "Your Patient disconnected the call as you did not join the call.";
$lang['doctor_call_disconnect_message'] = "Your call disconnected due to patient not joined the call.";
$lang['template_2'] = "During normal times or Pandemic times, MedSign supports your clinic management needs. MedSign for all Times.
{{1}}";
$lang['template_4'] = " MedSign video consult. The only solution that allows to receive patient payment directly into doctor account. No commission. No deduction. No internet handling charges.
{{1}}";
$lang['template_5'] = "Pandemic will come and go and might come again. Build your patient database and connect with them anytime through MedSign Bulk SMS facility.
{{1}}";
$lang['template_8'] = "Does your current clinic management platform decide Pharmacy and path lab for patient? MedSign leaves that decision to you & your patients.
{{1}}";
$lang['template_11'] = "Compare patient records while deciding course of action. MedSign patient records and Compare functionality makes it effective and easy. {{1}}";
$lang['template_12'] = "MedSign supports building electronic medical records. Medico legal necessity is one more reason to use MedSign. {{1}}";
$lang['template_17'] = "Convert the drug dosage instructions in your Rx to regional language which patient understands at a click. MedSign makes it possible in 11 regional languages. {{1}}";
$lang['template_19'] = "MedSign video-consult. All patient records at fingertips during remote consultation. {{1}}";
$lang['appointment_book_patient'] = "Dear %s, Your appointment with %s is confirmed for %s Wishing you good health! Team MedSign! 
Visit- %s 
Email- %s";
$lang['tele_appointment_book_patient'] = "Dear %s your tele-consultation appointment with %s is confirmed on %s at %s. 
Kindly make payment of Rs. %s towards the tele-consultation fee using any of the UPI ID given below. Payment needs to be made prior to the consultation.
 %s recommends you to visit MedSign to book appointments, upload medical records and view prescriptions.
Link: %s

Stay Indoor, Stay Safe. 
Thank You.";
$lang['video_appointment_book_patient'] = "Dear %s your tele-consultation appointment with %s is confirmed on %s. 
Kindly make payment of Rs. %s towards the consultation fee using any of the UPI ID given below. Payment needs to be made prior to the consultation. 
%s You will receive a link of tele-consultation %s min prior to the appointment time. 
Wishing you good health! 
Team MedSign! 
Visit- %s 
Email- %s";
$lang['template_20'] = "Explaining a procedure to the patient is always time consuming. MedSign patient tools make it easy. {{1}}";
$lang['wa_template_22_after_one_hour'] = "Hello %s,\nSend below text msg thru SMS/WA to your patients informing your availability on Medsign for teleconsultation.\n\nGreetings from %s Hope you are in best of health.\nDue to Covid-19 pandemic and lockdown restrictions, I am available for tele-consultation on MedSign digital health platform.\nNow you can book appointments, seek online video/teleconsultation, upload reports, receive e-prescription, get sms/mail reminders and pay online - all securely.\nClick here %s to Register yourself on MedSign Patient Platform and avail all benefits\nStay Home, Stay Safe, Stay connected thru Medsign.\nTake Care,\nBest wishes.";
$lang['template_31_shared_prescription'] = "Dear %s your prescription from %s at %s on %s is shared with you. Click %s";
$lang['wa_template_23_book_patient_appoint'] = "Dear %s, Your appointment with %s is confirmed for %s\nWishing you good health!\nTeam MedSign!\nVisit- %s\nEmail- %s";
$lang['wa_template_25_book_patient_tele_appoint'] = "Dear %s your tele-consultation appointment with %s is confirmed on %s.\nKindly make payment of Rs. %s towards the tele-consultation fee using any of the UPI ID given below. Payment needs to be made prior to the consultation.\n%s\n\n%s recommends you to visit MedSign to book appointments, upload medical records and view prescriptions.\nLink: %s\n\nStay Indoor, Stay Safe.\nThank You.";
$lang['wa_template_26_book_patient_video_appoint'] = "Dear %s your tele-consultation appointment with %s is confirmed on %s.\nKindly make payment of Rs. %s towards the consultation fee using any of the UPI ID given below. Payment needs to be made prior to the consultation.\n%s\n\nYou will receive a link of tele-consultation %s min prior to the appointment time.\nWishing you good health!\nTeam MedSign!\nVisit- %s\nEmail- %s";
$lang['wa_template_24_patient_join_video_call_link']  = "Dear %s, find enclosed link for your tele-consultation with %s on %s. The tele-consultation will start in next %s min. By clicking on the link you are giving your consent for tele-consultation and agree with the MedSign Privacy Policy and T and C.\nLink: %s";
$lang['wa_patient_invoice_shared_by_doctor_35'] = "Dear %s your invoice from %s at %s on %s is shared with you.\nClick %s";
$lang['patient_invoice_shared_by_doctor'] = "Dear %s your invoice from %s at %s on %s is shared with you.
Click %s";
$lang['caregiver_notification_sms'] = "Hello %s,
You have been successfully registered on MedSign as a Caregiver for %s.
Alerts from MedSign will be sent on your registered contact details.
Wishing good Health!
Team MedSign 
website: %s
email: %s";
$lang['wa_template_caregiver_has_shared_contacts_37'] = "Hello %s,\nYou have been successfully registered on MedSign as a Caregiver for %s.\nAlerts from MedSign will be sent on your registered contact details.\nWishing good Health!\nTeam MedSign \nwebsite: %s\nemail: %s";
$lang['patient_has_shared_contacts'] = "Hello %s,
You have been successfully registered on MedSign and %s is registered as your Caregiver.
Alerts from MedSign will be sent on both patient and Caregiver's registered contact details.
Wishing good Health!
Team MedSign
website: %s
email: %s";
$lang['wa_template_patient_has_shared_contacts_38'] = "Hello %s,\nYou have been successfully registered on MedSign and %s is registered as your Caregiver.\nAlerts from MedSign will be sent on both patient and Caregiver's registered contact details.\nWishing good Health!\nTeam MedSign\nwebsite: %s\nemail: %s";

$lang['template_drs_40'] = "Anytime, Anywhere, multiple device access of patient records with MedSign {{1}}";

/*MedSign Update Serise template */
$lang['medsign_update_series_01'] = "Dear Doctor, you can now add your 2nd clinic or even Multiple clinic details on your\n
MedSign account. For a live demo or any help and support: {{1}} \n
Regards, Team MedSign!";

$lang['medsign_update_series_02'] = "Dear Doctor, New Brand not found in Medsign drug database can now be added while\n
generating Rx on your Medsign account. For a live demo or any help and support: {{1}} \n
Regards, Team MedSign!";

$lang['medsign_update_series_03'] = "Dear Doctor, Different time slots for your in-clinic and tele-consultation can now be set in the calendar section of your MedSign account. For a live demo or any help and support: {{1}} \n
Regards, Team MedSign!";

$lang['medsign_update_series_04'] = "Dear Doctor, Registration of a patient not having a personal mobile no can now be done easily through a Caregiver's mobile no. For a live demo or any help and support: {{1}} \n
Regards, Team MedSign!";

$lang['medsign_update_series_05'] = "Dear Doctor, Teleconsultation fee payment can now be done by your patient's through online bank transfer in addition to UPI payment option already available on your MedSign account. For a live demo or any help and support: {{1}} \n
Regards, Team MedSign!";

$lang['medsign_update_series_06'] = "Dear Doctor, half tablet dosing and customised frequency dosing can now be recommended to patients while prescribing drugs on MedSign. For a live demo or any help and support: {{1}} \nRegards, Team MedSign!";

$lang['medsign_update_series_07'] = "Dear Doctor, Multiple Receptionist's and Assistant Doctors can be added and linked to your Medsign account in the 'Add Staff' section. For a live demo or any help and support: {{1}} \n
Regards, Team MedSign!";

$lang['medsign_update_series_08'] = "Dear Doctor, Report uploading by a patient is possible irrespective of appointment booking and even on a date prior to the actual appointment date. For a live demo or any help and support: {{1}} \n
Regards, Team MedSign!";

$lang['medsign_update_series_09'] = "Dear Doctor, Medicine allergy alert will be displayed now while you generate a prescription on MedSign. For a live demo or any help and support: {{1}} \n
Regards, Team MedSign!";

$lang['medsign_update_series_10'] = "Dear Doctor, Leave the mundane task of receipt generation with the receptionist. For a live demo or any help and support: {{1}} \n
Regards, Team MedSign!";

$lang['medsign_update_series_11'] = "Dear Doctor, Print setting of Left and Right margins can now be done on MedSign in addition to top and bottom margin setting. For a live demo or any help and support: {{1}} \n
Regards, Team MedSign!";

$lang['medsign_update_series_12'] = "Dear Doctor, create a patient id of your choice during patient registration and also enter old patient id if any to keep continuity. For a live demo or any help and support: {{1}} \n
Regards, Team MedSign!";

$lang['patient_followup_sms'] = "Dear %s, Your follow up visit with %s is due on %s. Kindly book time/date of appointment. 
Wishing you good health! 
Team MedSign! 
Visit- %s 
Email- %s";
$lang['wa_template_patient_followup_sms'] = "Dear %s, Your follow up visit with %s is due on %s. Kindly book time/date of appointment.\nWishing you good health!\nTeam MedSign!\nVisit- %s\nEmail- %s";
$lang['template_drs_41'] = "Highly secure platform, Transparent data privacy policy – Data safety guaranteed with MedSign {{1}}";
$lang['template_drs_42'] = "MedSign- a comprehensive yet simple & user-friendly patient management platform. {{1}}";
$lang['template_drs_43'] = "With MedSign patient data infographics is on a click. Auto generated Tables, Charts, Graphs for meaningful view of data facilitates quick analysis & clinical decision making. {{1}}";
$lang['template_drs_44'] = "Exhaustive catalogue of Brands, Clinical notes, Investigation’s, Procedures with provision for self-customization only with MedSign. {{1}}";
$lang['template_drs_45'] = "Digitize all patient reports with MedSign – Upload, Compare, Analyze, Store & access patient reports anytime, anywhere. {{1}}";
$lang['sms_medicine_reminder_to_patient'] = "Hello %s, This is reminder for your medicine administration of %s in the %s as advised by your Doctor. %s Wishing Good Health! (A MedSign Care initiative)";
$lang['medicine_reminder_to_patient_59'] = "Hello %s, This is reminder for your medicine administration of %s in the %s as advised by your Doctor. %s Wishing Good Health! (A MedSign Care initiative)";
$lang['sms_investigation_reminder_to_patient'] = "Hello %s, This is reminder for Investigations to be done as advised by your Doctor. %s Take Care! (A MedSign Care initiative)";
$lang['investigation_reminder_to_patient_60'] = "Hello %s, This is reminder for Investigations to be done as advised by your Doctor. %s Take Care! (A MedSign Care initiative)";
$lang['sms_appointment_reminder_to_patient'] = "Hello %s, This is reminder for your appointment booked with %s for %s. Wishing Good Health! (A MedSign Care initiative)";
$lang['appointment_reminder_to_patient_58'] = "Hello %s, This is reminder for your appointment booked with %s for %s. Wishing Good Health! (A MedSign Care initiative)";