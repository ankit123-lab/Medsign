<?php

defined('BASEPATH') OR exit('No direct script access allowed');

//my controller message
$lang['mycontroller_invalid_request'] = "Invalid request data";
$lang['mycontroller_invalid_token'] = "Invalid security token";
$lang['mycontroller_problem_request'] = "Problem in processing request";
$lang['mycontroller_bad_request'] = "Invalid request parameters";

//common controller
$lang['common_api_log_success'] = "Api log history";
$lang['common_push_send_success'] = "Push notification send successfully";
$lang['common_push_send_fail'] = "Problem in send push notification";
$lang['get_country_code_fail'] = "Problem in featching country code.";
$lang['get_country_code_success'] = "Country code found successfully.";

$lang['get_state_code_fail'] = "Problem in fetching state.";
$lang['get_state_code_success'] = "State found successfully.";
$lang['common_detail_found'] = "Detail found succesfully.";
$lang['common_detail_not_found'] = "Detail not found.";
$lang['common_detail_update'] = "Detail update succesfully";
$lang['common_detail_added'] = "Detail added succesfully";
$lang['common_detail_deleted'] = "Detail deleted succesfully";
$lang['common_college_found'] = "College found succesfully.";
$lang['common_college_not_found'] = "College not found.";
$lang['common_qualification_found'] = "Qualification found succesfully.";
$lang['common_qualification_not_found'] = "Qualification not found.";
$lang['common_councils_found'] = "Councils found succesfully.";
$lang['common_councils_not_found'] = "Councils not found.";
$lang['common_drug_frequency_found'] = "Drug frequency found succesfully.";
$lang['common_drug_frequency_not_found'] = "Drug frequency not found.";
$lang['common_drug_generic_found'] = "Drug generic found succesfully.";
$lang['common_drug_generic_not_found'] = "Drug generic not found.";

$lang['common_language_found'] = "Language found succesfully.";
$lang['common_language_not_found'] = "Language not found.";
$lang['common_specialization_found'] = "Specialization found succesfully.";
$lang['common_specialization_not_found'] = "Specialization not found.";



//user controller :: login.
$lang['register_successfully'] = "Registration done successfully";
$lang['login_with_phone_number_success'] = "Successful Login";

$lang['login_with_phone_number_fail']    = "Invalid login credentials please try again";
$lang['login_email_not_found'] 		     = "Invalid login credentials please try again";
$lang['phone_number_not_found']          = "Invalid login credentials please try again";
$lang['unique_number_not_found']         = "Invalid login credentials please try again";
$lang['login_email_password_not_match']  = "Invalid login credentials please try again";
$lang['login_number_password_not_match'] = "Invalid login credentials please try again";
$lang['login_unique_password_not_match'] = "Invalid login credentials please try again";

$lang['login_email_password_login_success'] = "Successful Login";
$lang['user_login_block'] = "Your account is deactivated by administrator";
$lang['user_login_token_generate_error'] = "Unable to process request please try again.";
$lang['invalid_otp_for_login'] = "Incorrect OTP, %s attempts remaining.";
$lang['failure'] = "Unable to process request please try again.";

//resetpassword controller
$lang['resetpassword_token_empty'] = "Unable to process request please try again.";   //"Oops, something went wrong. Missing security token.";
$lang['resetpassword_token_invalid'] = "Unable to process request please try again."; //"Oops, something went wrong. Invalid security code!";
$lang['resetpassword_token_expire'] = "Unable to process request please try again.";  //"Token expired. Please generate new token";

$lang['resetpassword_reset_empty'] = "Unable to process request please try again.";
$lang['resetpassword_reset_success'] = "Password reset successful !";
$lang['resetpassword_reset_fail'] = "Password reset unsuccessful. Please try again or contact Support !";
$lang['old_pass_new_pass_same'] = "Old password and new password are same. please try again";

//User controller
$lang['user_login_success'] = "Successfully logged in";
$lang['user_login_fail'] = "Invalid email or password";
$lang['sms_otp_send_fail'] = "Unable to process request please try again.";
$lang['mobile_number_not_register_with_us'] = "Invalid login credentials please try again";

// regsitration

//below message is used for the add family member and update family member
$lang['user_register_phone_number_exist'] = "This phone number is already registered with us";
$lang['user_register_phone_number_exist_but_not_verified'] = "Invalid login credentials please try again";
$lang['user_register_phone_number_already_verfied'] = "This mobile number is registered & verified.";

//this message is used for the registration time
$lang['user_number_register'] = "Please, Try to login with '%s', if password is lost, Please use forgot password !!!";

$lang['user_register_email_exist'] = "This email is already registered with us";
$lang['user_register_email_exist_but_not_verified'] = "Email is register but not verified. Please verify.";
$lang['user_register_email_already_verfied'] = "Email verified succesully."; //"This email is already verified";

$lang['otp_verfication_fail'] = "Incorrect OTP"; //"OTP verification failed";
$lang['otp_token_expire'] 	  = "Incorrect OTP"; //"OTP token expire.";

$lang['otp_verfication_success'] = "OTP verification successful."; //"OTP verification success";
$lang['otp_verfication_limit_reach'] = "Incorrect OTP";
$lang['otp_resend_limit_reach'] = "You have reached OTP resend limit. Please contact MedEasy support team";
$lang['otp_resend_time_limit'] = "You need to wait for %s seconds for resend OTP.";

$lang['user_register_fail'] = "Unable to process request please try again.";
$lang['otp_sent_to_mobile'] = "Please verify OTP sent to the given number.";
$lang['user_not_found'] = "User details not found. Please try again.";
$lang['user_register_success'] = "Registration successful !";
$lang['user_profile_update_success'] = "Profile updated successfully !";
$lang['user_profile_update_success_email'] 		 = "Profile updated successfully. Please verify email.";
$lang['user_profile_update_success_phone'] 		 = "Profile updated successfully. Please verify phone number.";
$lang['user_profile_update_success_phone_email'] = "Profile updated successfully. Please verify phone number & email.";
$lang['user_profile_update_fail'] 				 = "Unable to process request please try again."; //"Problem while updating user profile";

$lang['user_confirm_password_notmatch'] = "Password and confirm password does not match.";
$lang['user_password_update_success']   = "Password reset successful !";//"Password is updated successfully";
$lang['user_password_update_problem']   = "Password reset unsuccessful. Please try again or contact Support !"; //"Problem while updating password";
$lang['user_password_enter_current_password'] = "Please enter correct password !"; //"Current password is wrong";
$lang['user_photo_upload_success'] 		= "Photo updated successfully !";//"User photo updated successfully";
$lang['user_photo_upload_fail'] 		= "Unable to process request please try again."; //"Problem while uploading photo";
$lang['file_size_does_not_exist'] 		= "You File Size is More than 5 MB";


$lang['user_logout_success'] = "You have logged out successfully !";
$lang['user_logout_fail'] = "Unable to process request please try again.";

$lang['user_updatedevicetoken_success'] = "Device token updated successfully";
$lang['user_updatedevicetoken_fail'] = "Error in update device token";

$lang['user_forgotpassword_success'] = "An email with password reset instuctions has been sent to your registered email address.";
$lang['user_forgotpassword_fail'] = "Invalid login credintials please try again.";

$lang['user_detail_found'] = "User detail found succesfully.";
$lang['user_detail_not_found'] = "User detail not found";

$lang['common_college_found'] = "College found succesfully.";
$lang['common_college_not_found'] = "College not found.";

$lang['common_qualification_found'] = "Qualification found succesfully.";
$lang['common_qualification_not_found'] = "Qualification not found.";
$lang['common_councils_found'] = "Councils found succesfully.";
$lang['common_councils_not_found'] = "Councils not found.";

$lang['user_caregiver_message'] = "Dear %s, %s has added you as a Caregiver in MedSign! As a caregiver you shall have access to all health related information of %s available on MedSign."; //SMS/N

$lang['user_address_exist'] = "Address already exists";


//reminder lanugages 
$lang['reminder_add_success'] = 'Reminder added successfully';
$lang['reminder_add_failure'] = 'Error to add Reminder';
$lang['reminder_edit_success'] = 'Reminder updated successfully';
$lang['reminder_edit_failure'] = 'Error to update Reminder';
$lang['reminder_delete_success'] = 'Reminder deleted successfully';
$lang['reminder_delete_failure'] = 'Error to delete Reminder';


//drug languages
$lang['drug_add'] = "Medicine added successfully !";
$lang['drug_add_failure'] = "Unable to process request please try again.";
$lang['drug_already_added'] = "This medicine is already exists !";

$lang['member_add'] = "Family member added successfully";
$lang['member_found'] = "Family member found succesfully";
$lang['member_not_found'] = "No family member found";




//clinic languages
$lang['mendatory_filled_missing'] = "Mandatory fileds are missing.";
$lang['clinic_unable_to_add_clinic_address'] = "Unable to add clinic address. please try again.";
$lang['clinic_unable_to_add_clinic'] = "Unable to add clinic. please try again.";
$lang['clinic_added_successfully'] = "Clinic added successfully.";
$lang['clinic_unable_to_edit_clinic_address'] = "Unable to edit clinic address. please try again.";
$lang['clinic_unable_to_edit_clinic'] = "Unable to edit clinic. please try again.";
$lang['clinic_edit_successfully'] = "Clinic editied successfully.";
$lang['clinic_edit_successfully_email'] = "Clinic editied successfully please verify email";
$lang['clinic_edit_successfully_phone'] = "Clinic editied successfully please verify phone number";
$lang['clinic_edit_successfully_phone_email'] = "Clinic editied successfully please verify phone number and email";
$lang['clinic_uable_to_update_image'] = "Image update failure.";
$lang['clinic_staff_delete_success'] = "staff deleted successfully";
$lang['clinic_staff_delete_error'] = "staff delete error";
$lang['clinic_staff_status_success'] = "staff status has been changed";
$lang['clinic_staff_status_error'] = "staff status change error";

//other messages
$lang['issue_send'] = "Issue send succesfully";
$lang['news_not_found'] = "No news found";

$lang['block_calendar_already_added'] = "This date or time already exists in block calendar";
$lang['invalid_date_time'] = "Enter valid date or time";
$lang['block_calendar_added'] = "Block calendar added succesfully";
$lang['block_calendar_update'] = "Block calendar updated succesfully";
$lang['block_calendar_delete'] = "Block calendar deleted succesfully";
$lang['have_appointment'] = "You have an appointement, your appointment will cancel if you block the calendar";


$lang['invalid_email'] = "Invalid email id";
$lang['invalid_phone_number'] = "Invalid mobile number";
$lang['invalid_dob'] = "Invalid date of birth";
$lang['invalid_pincode'] = "Invalid pincode";
$lang['invalid_date'] = "Invalid date";
$lang['invalid_date_time'] = "Invalid date or time";
$lang['invalid_time'] = "Invalid time";
$lang['reschedule_time_pass'] = "You can't reschedule the appointment because it is already taken";

$lang['doctor_not_available'] = "Sorry..Doctor not available in this time slot";
$lang['appointment_booked'] = "Appointment booked successfully";
$lang['my_appointment'] = "Appointment data";
$lang['appointment_cancel'] = "Appointment has been cancelled";
$lang['not_able_to_cancel'] = "You can't able to cancel the appointment now";
$lang['not_able_to_reschedule'] = "You can't able to reschedule the appointment now";
$lang['appointment_reschedule'] = "Appointment has been rescheduled";

$lang['patient_added'] = "Patient added successfully";
$lang['reminder_sync'] = "Reminder Sync Data";
$lang['reminder_record_add'] = "Record added successfully";
$lang['staff_added'] = "Staff added succesfully";
$lang['availability_set'] = "Availability set succesfully";
$lang['number_not_exists'] = "Phone number not exists";
$lang['number_not_exists_unverified'] = "Your phone number is not verified. Kindly Use Login with OTP";
$lang['verification_link'] = "Verification link send to your account";
$lang['setting_set'] = "Settings set succesfully";
$lang['tax_name_already_exists'] = "Tax name already exists";
$lang['tax_added'] = "Tax added successfully";
$lang['tax_deleted'] = "Tax deleted successfully";
$lang['tax_updated'] = "Tax updated successfully";
$lang['payment_name_already_exists'] = "Payment mode already exists";
$lang['payment_added'] = "Payment mode added successfully";
$lang['payment_deleted'] = "Payment mode deleted successfully";
$lang['payment_updated'] = "Payment mode updated successfully";
$lang['pricing_name_already_exists'] = "Pricing name already exists";
$lang['pricing_added'] = "Pricing added successfully";
$lang['pricing_deleted'] = "Pricing deleted successfully";
$lang['pricing_updated'] = "Pricing updated successfully";
$lang['clinical_notes_already_exists'] = "Clinical note name already exists";
$lang['clinical_notes_added'] = "Clinical note added successfully";
$lang['clinical_notes_deleted'] = "Clinical note deleted successfully";
$lang['clinical_notes_updated'] = "Clinical note updated successfully";
$lang['drug_delete'] = "Brand deleted succesfully";
$lang['template_added'] = "Template added succesfully";
$lang['template_deleted'] = "Template deleted succesfully";
$lang['template_updated'] = "Template updated succesfully";
$lang['template_name_exists'] = "Template name already exists";
$lang['patient_group_exists'] = "Patient group name already exists";
$lang['patient_group_added'] = "Patient group added successfully";
$lang['patient_group_deleted'] = "Patient group deleted successfully";
$lang['patient_group_updated'] = "Patient group updated successfully";
$lang['setting_set'] = "Settings update successfully";

$lang['vital_added'] = "Vital added succesfully";
$lang['vital_updated'] = "Vital updated succesfully";
$lang['data_exists'] = "Data already exists";
$lang['report_added'] = "Report added succesfully";
$lang['report_deleted'] = "Report deleted succesfully";
$lang['prescription_added'] = "Prescription modified succesfully";
$lang['invoice_added'] = "Invoice added succesfully";
$lang['prescription_updated'] = "Prescription updated succesfully";
$lang['prescription_deleted'] = "Prescription deleted succesfully";
$lang['lab_report_added'] = "Investigation added succesfully";
$lang['lab_report_updated'] = "Investigation updated succesfully";
$lang['procedure_added'] = "Procedure added succesfully";
$lang['procedure_updated'] = "Procedure updated succesfully";
$lang['image_deleted'] = "Image deleted succesfully";

$lang['analytics_added'] = "Health analytics added succesfully";
$lang['analytics_updated'] = "Health analytics updated succesfully";

$lang['already_refer'] = "You had already refered this patient to the doctor";
$lang['refer_added'] = "Referred added succesfully";

$lang['common_data_added'] = "Data added succesfully";
$lang['no_data_contain'] = "No data contains";

$lang['clinic_number_update'] = "Clinic number update succesfully";
$lang['appointment_not_cancel'] = "You can't cancel the appointment now";

//notification message
$lang['reminder_notification'] = "You have an reminder";

$lang['appointment_book_doctor'] 		 = "Dear %s your appointment booked successfully with %s at %s on %s "; //SMS/N
$lang['appointment_book_patient'] 		 = "Dear %s your appointment booked successfully with %s on %s recommend you to visit MedSign to book appointments, upload medical records and view prescriptions. 
Link: %s
Stay Indoor, Stay Safe. 
Thank You."; //SMS/N
$lang['appointment_cancel']       		 = "Dear %s your appointment with %s at %s on %s is cancelled."; //SMS/N
$lang['appointment_cancel_patient']      = "Dear %s your appointment with %s at %s on %s is cancelled. Please rebook your appointment at MedSign";//SMS/N
$lang['appointment_reschedule'] 		 = "Dear %s your appointment with %s at %s on %s is rescheduled.";//SMS/N
$lang['appointment_reschedule_patient']  = "Dear %s your appointment with %s at %s on %s is rescheduled.";//SMS/N
$lang['appointment_reschedule_message']  = "Your appointment has been reschedule";
$lang['emergency_message'] 				 = "Dear %s, %s has added you as an Emergency Contact in MedSign";//SMS/N
$lang['added_family_member'] 			 = "Dear %s, %s has added you as a Family Member in MedEasy App";//SMS/N

$lang['appointment_cancel_message'] 	 = "Appointment cancel succesfully";
$lang['appointment_book_notify'] 		 = "%s book appointment with you on %s at %s";
$lang['appointment_reschedule_notify'] 	 = "%s reschedule appointment with you on %s at %s";
$lang['appointment_cancel_notify'] 		 = "%s cancel appointment with you on %s at %s";
$lang['appointment_book_notify_patient'] = "Your appointment has fixed with %s on %s at %s";
$lang['appointment_reschedule_notify_patient'] = "Your appointment has reschedule with %s on %s at %s";
$lang['appointment_cancel_notify_patient'] = "Your appointment has cancelled with %s on %s at %s";


//fav doctor message
$lang['fav_already_added'] = "Already added in favorite";
$lang['fav_added_success'] = "Added into favorite";
$lang['fav_added_failure'] = "Error to add favorite";
$lang['fav_remove_success'] = "Succesfully remove from favourite";
$lang['2_way_enabled'] = "To ensure your data safety,MedEasy is enable with 2-factor authentication";
$lang['bill_added'] = "Bill added/updated succesfully";
$lang['price_mismatch'] = "Calculate price mismatch";
$lang['record_share'] = "Record shared succesfully";
$lang['kco_exists'] = "Kco already exists";

$lang['permission_error'] = "You don't have a permission";
$lang['email_already_verified'] = "Your email is verified";

$lang['login_ip_blocked'] = "Your account has been blocked temporarily due to repeated failed login attempts. Please login after 1 hour.";
$lang['appointment_alreay_taken'] = "Modification in appointment denied because you are already taken an appointment";
$lang['invalid_availability'] = "Please enter availabilty time between clinic time";

$lang['family_member_delete'] = "Family member deleted successfully";
$lang['vital_delete'] = "Vital deleted successfully";
$lang['prescription_delete'] = "Prescription deleted successfully";
$lang['invoice_delete'] = "Invoice deleted successfully";
$lang['report_delete'] = "Report deleted successfully";
$lang['patient_add_report'] = "%s added a report given by %s";
$lang['user_family_member_otp'] = "Dear %s, %s want to add you as a family member. Please share %s OTP with him.";
$lang['otp_sent_to_member'] = "OTP sent successfully";
$lang['request_accept_sms'] = "Dear %s, Your family member request has been accepted by %s. Now you can access all the details of him.";
$lang['request_decline_sms'] = "Dear %s, Your family member request has been decline by %s.";
$lang['request_accept_success'] = "Request accepted successfully";
$lang['request_decline_success'] = "Request declined successfully";
$lang['already_mapped'] = "User already mapped";
$lang['reuqest_send_sms'] = "Dear %s, %s want to add you as a family member. Please accept request from setting.";
$lang['request_send'] = "Request send successfully";
$lang['member_removed'] = "Member removed successfully";
$lang['your_own_detail'] = "You can not add your own details.";
$lang['doctor_event_summary'] = "Doctor Appointment booked successfully!";
$lang['patient_event_summary'] = "Patient appointment booked successfully!";
$lang['app_consultation_patient_app_book'] = "Dear %s your appointment booked successfully with %s at %s on %s at %s.
For in-clinic consultation, pl reconfirm Dr's availability at the clinic.
For tele-consultation, kindly make payment of Rs. %s towards the tele-consultation fee using any of the UPI ID given prior to the consultation.
 %s";
 $lang['app_consultation_doctor_app_book'] = "Dear %s your appointment with %s is confirmed on %s at %s. In case of tele-consultation, below UPI IDs are shared with the patient to receive tele-consultation fee of Rs. %s
 %s
Mobile number of patient for tele-consultation is %s.

Kindly check payment receipt before tele-consultation.";
$lang['tele_appointment_book_patient'] = "Dear %s your tele-consultation appointment with %s is confirmed on %s at %s. 
Kindly make payment of Rs. %s towards the tele-consultation fee using any of the UPI ID given below. Payment needs to be made prior to the consultation.
 %s recommends you to visit MedSign to book appointments, upload medical records and view prescriptions.
Link: %s

Stay Indoor, Stay Safe. 
Thank You.";
$lang['tele_appointment_book_doctor'] = "Dear %s your tele-consultation appointment with %s is confirmed on %s at %s. Mobile number of patient for tele-consultation is %s.
Following UPI ID are shared with the patient to receive tele-consultation fee of Rs. %s
 %s
Kindly check payment receipt before tele-consultation.";
$lang['video_appointment_book_patient'] = "Dear %s your tele-consultation appointment with %s is confirmed on %s. 
Kindly make payment of Rs. %s towards the consultation fee using any of the UPI ID given below. Payment needs to be made prior to the consultation.
 %s recommend you to visit MedSign to book appointments, upload medical records and view prescriptions at %s

You will receive a link of tele-consultation %s min prior to the appointment time.

Stay Indoor, Stay Safe! 
Thank You.";
$lang['video_appointment_book_doctor'] = "Dear %s, your appointment with %s is confirmed on %s. For tele-consultation, below UPI IDs are shared with the patient to receive tele-consultation fee of Rs. %s.
A tele-consultation link will be shared with the patient %s min prior to the consultation time. Kindly check payment receipt before the consultation.";
$lang['patient_join_video_call_link_sms'] = "Dear %s, find enclosed link for your tele-consultation with %s on %s. The tele-consultation will start in next %s min. By clicking on the link you are giving your consent for tele-consultation and agree with the MedSign Privacy Policy and T and C. 
Link: %s";
$lang['wa_template_23_book_patient_appoint'] = "Dear %s your appointment booked successfully with %s on %s\n\n%s recommend you to visit MedSign to book appointments, upload medical records and view prescriptions.\nLink: %s\n\nStay Indoor, Stay Safe.\n\nThank You.";
$lang['wa_template_25_book_patient_tele_appoint'] = "Dear %s your tele-consultation appointment with %s is confirmed on %s.\nKindly make payment of Rs. %s towards the tele-consultation fee using any of the UPI ID given below. Payment needs to be made prior to the consultation.\n%s\n\n%s recommends you to visit MedSign to book appointments, upload medical records and view prescriptions.\nLink: %s\n\nStay Indoor, Stay Safe.\nThank You.";
$lang['wa_template_26_book_patient_video_appoint'] = "Dear %s your tele-consultation appointment with %s is confirmed on %s. \nKindly make payment of Rs. %s towards the consultation fee using any of the UPI ID given below. Payment needs to be made prior to the consultation.\n%s\n\n%s recommend you to visit MedSign to book appointments, upload medical records and view prescriptions at %s\n\nYou will receive a link of tele-consultation %s min prior to the appointment time.\n\nStay Indoor, Stay Safe!\nThank You.";
$lang['wa_template_29_appoint_cancel_patient'] = "Dear %s your appointment with %s at %s on %s is cancelled. Please rebook your appointment at MedSign";
$lang['wa_template_29_appoint_reschedule_patient']  = "Dear %s your appointment with %s at %s on %s is rescheduled.";
$lang['wa_template_24_patient_join_video_call_link']  = "Dear %s, find enclosed link for your tele-consultation with %s on %s. The tele-consultation will start in next %s min. By clicking on the link you are giving your consent for tele-consultation and agree with the MedSign Privacy Policy and T and C.\nLink: %s";