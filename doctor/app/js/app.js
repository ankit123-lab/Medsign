/* Modules declartion */
(function () {
    angular.module("medeasy",
            [
                'app.routes',
                'app.login',
                'app.dashboard',
                'app.dashboard.setting',
                'app.profile',
                'app.survey',
                'app.subscription',
                'app.import',
                'app.dashboard.reports',
                'app.dashboard.communications',
                'app.dashboard.teleconsultation',
                'ngSanitize',
                'ngToast',
                'ngRoute',
                'ngStorage',
                'localytics.directives',
                'gm',
                'ui.bootstrap', 
                'ui.bootstrap.datetimepicker',
                'ui.calendar',
                'oitozero.ngSweetAlert',
                'pascalprecht.translate',
                'ngCookies',
                'ngSlimScroll',
                'ngSanitize',
                'vcRecaptcha',
                'chart.js',
                'angularLoad',
                'ui.mask'
            ])
            .run(basicSetting);
    basicSetting.$inject = ['$rootScope'];
    
    function basicSetting($rootScope) {
        $rootScope.ENV = 'PROD';
        if ($rootScope.ENV == 'PROD') {
            $rootScope.app = {
                title: 'MedSign',
                apiUrl: $base_url + "api/betadcv1",
                uploadsPath: $base_url + "uploads",
                basicAuth: "medeasy" + ":" + "api@medeasy",
                isLoader: true,
                secretKey: CryptoJS.enc.Hex.parse('253D3FB468A0E24677C28A624BE0F939'),
                iv: CryptoJS.enc.Hex.parse('0'),
                base_url: $base_url,
                app_url: $base_url + 'doctor/',
            };
        }
        $rootScope.app.is_payment_active = true;
        $rootScope.autoCompleteMinLength = 1;
        $rootScope.app.devMsg = 'This page is currently under development';
        $rootScope.app.common_error = 'Please Complete All Required Fields';
        $rootScope.app.logout_error = 'We notice, you are login in to another devices';
        $rootScope.app.landmark_placeholder = 'Landmark';
        $rootScope.app.address_placeholder = 'Door / Flat no / Building name';
        $rootScope.app.landmark_label = 'Landmark';
        $rootScope.app.address_label = 'Address';
        $rootScope.app.all_clinic_label = 'For All Clinic';
        
        $rootScope.app.date = new Date();
        $rootScope.app.unauthorised_msg          = "You are unauthorised to access this module";
        $rootScope.app.clinic_edit_alert         = "Are you sure want to change clinic details, it may affect on your calendar. Also please update the Doctor Practice Timings aligned with Clinic Timing.";
        $rootScope.app.doctor_practice_edit_alert = "Are you sure want to change Practice Timings, it may affect on your current booked appointments. All your booked appointments will be cancelled immediatly and you will never recover it.";
        $rootScope.app.appoinment_add_rx_alert   = "Before saving and sharing prescription with patient, please do verify once again.";
        $rootScope.app.clinic_staff_delete_alert = "Are you sure want to delete staff?";
        $rootScope.app.clinic_staff_status_alert = "Are you sure want to change staff status?";
        $rootScope.pages_number = [{
                id: 39,
                name: 'Calendar',
                value: 'app.dashboard.calendar'
            }, {
                id: 40,
                name: 'Patient',
                value: 'app.dashboard.patient'
            },
            //  {
            //     id: 3,
            //     name: 'refer',
            //     value: 'app.dashboard.refer'
            // }, {
            //     id: 4,
            //     name: 'communication',
            //     value: 'app.dashboard.communication'
            // }, {
            //     id: 5,
            //     name: 'report',
            //     value: 'app.dashboard.report'
            // }, {
            //     id: 6,
            //     name: 'setting',
            //     value: 'app.dashboard.setting'
            // }, {
            //     id: 7,
            //     name: 'integration',
            //     value: 'app.dashboard.integration'
            // }, {
            //     id: 8,
            //     name: 'back',
            //     value: 'app.dashboard.back'
            // },
             {
                id: 11,
                name: 'Clinic Details',
                value: 'app.dashboard.setting.clinic_details'
            }, {
                id: 12,
                name: 'Clinic Staff',
                value: 'app.dashboard.setting.clinic_staff'
            }, {
                id: 13,
                name: 'Calendar',
                value: 'app.dashboard.setting.calendar'
            }, {
                id: 14,
                name: 'Alert',
                value: 'app.dashboard.setting.alert'
            }, {
                id: 15,
                name: 'Fee\'s Structure',
                value: 'app.dashboard.setting.fees'
            }, {
                id: 16,
                name: 'Billing',
                value: 'app.dashboard.setting.billing'
            }, {
                id: 17,
                name: 'Data Security',
                value: 'app.dashboard.setting.security'
            }, {
                id: 18,
                name: 'Clinical Notes Catalog',
                value: 'app.dashboard.setting.clinical'
            }, {
                id: 19,
                name: 'Brand Catalog',
                value: 'app.dashboard.setting.brand'
            }, {
                id: 20,
                name: 'Diseases Template',
                value: 'app.dashboard.setting.template'
            }, {
                id: 21,
                name: 'Share Record',
                value: 'app.dashboard.setting.share'
            }, {
                id: 22,
                name: 'Favorite Doctors',
                value: 'app.dashboard.setting.favdoctor'
            }, {
                id: 38,
                name: 'Import',
                value: 'app.dashboard.setting.import'
            }, {
                id: 37,
                name: 'Survey',
                value: 'app.survey'
            }, {
                id: 23,
                name: 'My Account',
                value: 'app.profile.my_profile_view'
            }, {
                id: 23,
                name: 'My Account',
                value: 'app.profile.clinic_view'
            }, {
                id: 23,
                name: 'My Account',
                value: 'app.profile.setting_view'
            }, {
                id: 23,
                name: 'My Account',
                value: 'app.dashboard.staff'
            }, {
                id: 23,
                name: 'My Account',
                value: 'app.whats'
            }, {
                id: 24,
                name: 'Subscription',
                value: 'app.subscription'
            }, {
                id: 24,
                name: 'Subscription History',
                value: 'app.subscription_history'
            }, {
                id: 43,
                name: 'Diet Instruction',
                value: 'app.dashboard.setting.diet_instruction'
            }, {
                id: 45,
                name: 'Payment Mode',
                value: 'app.dashboard.setting.payment_mode'
            }, {
                id: 48,
                name: 'Share Link',
                value: 'app.dashboard.setting.share_link'
            }, {
                id: 49,
                name: 'Printouts',
                value: 'app.dashboard.setting.printouts'
            }];

        $rootScope.terms_condition_contain = '';
        $rootScope.PATIENT_MODULE = 1;
        $rootScope.APPOINTMENT_MODULE = 2;
        $rootScope.VITAL_MODULE = 3;
        $rootScope.CLINICAL_NOTES_MODULE = 4;
        $rootScope.RX_MODULE = 5;
        $rootScope.INVESTIGATION_MODULE = 6;
        $rootScope.PROC_MODULE = 7;
        $rootScope.REPORT_MODULE = 8;
        $rootScope.ANALYTICS_MODULE = 9;
        $rootScope.INVOICE_MODULE = 10;
        $rootScope.SETTING_CLINIC = 11;
        $rootScope.SETTING_STAFF = 12;
        $rootScope.SETTING_CALENDAR = 13;
        $rootScope.SETTING_ALERT = 14;
        $rootScope.SETTING_FEE = 15;
        $rootScope.SETTING_BILLING = 16;
        $rootScope.SETTING_DATA = 17;
        $rootScope.SETTING_CLINICAL_NOTES = 18;
        $rootScope.SETTING_BRAND = 19;
        $rootScope.SETTING_DS = 20;
        $rootScope.SETTING_SHARE = 21;
        $rootScope.SETTING_FAV = 22;
        $rootScope.MY_ACCOUNT = 23;
        $rootScope.SUBSCRIPTION = 24;
        $rootScope.PATIENT_KCO = 25;
        $rootScope.PATIENT_RIGHT_PANEL = 26;
        $rootScope.PATIENT_PDF = 27;
        $rootScope.PATIENT_SHARE = 28;
        $rootScope.PATIENT_FU_TEMPLATE = 29;
        $rootScope.PATIENT_DS_TEMPLATE = 30;
        $rootScope.PATIENT_SAVE_TEMPLATE = 31;
        $rootScope.PATIENT_HEALTH_ANYLYTICS = 32;
        $rootScope.PATIENT_HEALTH_ADVISE = 33;
        $rootScope.PATIENT_REFER = 34;
        $rootScope.DOCTOR_AVAILIBILITY = 35;
        $rootScope.BLOCK_CALENDAR_MODULE = 36;
        $rootScope.SURVEY_MODULE = 37;
        $rootScope.IMPORT_MODULE = 38;
        $rootScope.CALENDAR = 39;
        $rootScope.PATIENT = 40;
        $rootScope.DOCTOR_WEB_REPORTS = 41;
        $rootScope.RX_UPLOAD_MODULE = 42;
        $rootScope.SETTING_DIET_INSTRUCTION = 43;
        $rootScope.COMMUNICATIONS = 44;
        $rootScope.SETTING_PAYMENT_MODE = 45;
        $rootScope.SETTING_PATIENT_GROUPS = 46;
        $rootScope.TELECONSULTATION = 47;
        $rootScope.SHARE_LINK = 48;
        $rootScope.PRINTOUTS = 49;

        $rootScope.ADD = 1;
        $rootScope.EDIT = 2;
        $rootScope.VIEW = 3;
        $rootScope.DELETE = 4;
        $rootScope.SHARE = 5;

        /* Loading message constant */
        $rootScope.calendar_startup_load_msg = "Have a great day at work today. <br/> Happy using MedSign.";
        $rootScope.patient_startup_load_msg = "Start getting appointments. <br/> Happy using MedSign.";
        $rootScope.patient_startup_no_patient_pending_msg = "Today you don't have any patient appointment in Pending queue.";
        $rootScope.patient_startup_no_patient_today_msg = "You dont't have any patient appointment for Today.";
        $rootScope.patient_startup_click_to_view_all = "Click to view all patients";
        $rootScope.under_developement_msg = "At MedSign we are constantly innovating to ensure your healthcare experience is seamless & easy!";
        $rootScope.docPrefix = "Dr. ";
        $rootScope.no_data_found = "No data found";
        $rootScope.sub_history_no_data = "You have no any subscription payment history details.";
        
        /* validation msg */
        $rootScope.email_phone_unique_required = "* Please Enter Email/ Unique ID/ Number";
        $rootScope.password_required = "* Please Enter Password";
        $rootScope.cpassword_required = "* Please Enter Confirm Password";
        $rootScope.terms_condition = "Accept terms & conditons";
        $rootScope.email_required = "* Please Enter Email";
        $rootScope.phone_required = "* Please Enter Mobile Number";
        $rootScope.otp_required = "* Please Enter OTP";
        $rootScope.image_required = "* Please Select Image";
        $rootScope.fname_required = "* Please Enter First Name";
        $rootScope.lname_required = "* Please Enter Last Name";
        $rootScope.lan_required = "* Please Select Language";
        $rootScope.address_required = "* Please Enter Address";
        $rootScope.landmark_required = "* Please Enter Landmark";
        $rootScope.country_required = "* Please Select Country";
        $rootScope.state_required = "* Please Select State";
        $rootScope.city_required = "* Please Select City";
        $rootScope.locality_required = "* Please Enter Locality";
        $rootScope.pincode_required = "* Please Enter Pin Code";
        $rootScope.degree_required = "* Please Enter Degree";
        $rootScope.college_required = "* Please Enter College/University";
        $rootScope.edu_year_required = "* Please Select Year";
        $rootScope.reg_required = "* Please Enter Registration Details";
        $rootScope.counsil_required = "* Please Select Council";
        $rootScope.specialisation_required = "* Please Select Specialization";
        $rootScope.speciality_required = "* Please Select Speciality";
        $rootScope.exp_required = "* Please Select Year Of Experience";
        $rootScope.clinic_name_required = "* Please Enter Clinic Name";
        $rootScope.clinic_no_required = "* Please Enter Clinic Number";
        $rootScope.clinic_email_required = "* Please Enter Clinic Email";
        $rootScope.clinic_address_required = "* Please Enter Clinic Address";
        $rootScope.clinic_landmark_required = "* Please Enter Clinic Landmark";
        $rootScope.clinic_service_required = "* Please Enter Clinic Service";
        $rootScope.clinic_duration_required = "* Please Select Duration";
        $rootScope.clinic_charge_required = "* Please Enter Charges";
        $rootScope.clinic_session_required = "* Please Enter Session Timing";
        $rootScope.dob_required = "* Please Select Date Of Birth";
        $rootScope.blood_required = "* Please Select Blood Group";
        $rootScope.height_required = "* Please Enter Height";
        $rootScope.weight_required = "* Please Enter Weight";
        $rootScope.occ_required = "* Please Select Occupation";
        $rootScope.award_details = "* Please Enter Awards/Recognition";
        $rootScope.service_required = "* Please Enter Service Name";
        $rootScope.basic_cost_required = "* Please Enter Basic Cost";
        $rootScope.name_required = "* Please Enter Name";
        $rootScope.value_required = "* Please Enter Value";
        $rootScope.payment_type_required = "* Please Select Payment Type";
        $rootScope.payment_fee_required = "* Please Enter Fee";
        $rootScope.similar_brand_required = "* Please Select Similar Brand";
        $rootScope.brand_type_required = "* Please Select Dosage Form";
        $rootScope.brand_value_required = "* Please Enter Measure Value";
        $rootScope.brand_intake_required = "* Please Select Intake";
        $rootScope.brand_freq_required = "* Please Select Frequency";
        $rootScope.brand_duration_required = "* Please Enter Duration";
        $rootScope.brand_duration_type_required = "* Please Select Duration Type";
        $rootScope.brand_strength_required = "* Please Enter Strength Value";
        $rootScope.brand_generic_required = "* Please Select Generic";
        $rootScope.dignosis_name_required = "* Please Enter Diagnose Name";
        $rootScope.dosage_required = "* Please Enter Unit";
        $rootScope.doctor_required = "* Please Select Doctor";
        $rootScope.patient_required = "* Please Select Patient";
        $rootScope.date_required = "* Please Select Date";
        $rootScope.medical_condition_required = "* Please Select Medical Condition";
        $rootScope.relation_required = "* Please Select Relation";
        $rootScope.activity_required = "* Please Select Activity";
        $rootScope.days_required = "* Please Select Days";
        $rootScope.minutes_required = "* Please Select Minutes";
        $rootScope.appointment_type_required = "* Please Select Appointment Type";
        $rootScope.appointment_time_required = "* Please Select Appointment Time";
        $rootScope.issue_required = "* Please Enter Details";
        $rootScope.invalid_date = "* Invalid Date";
        
        $rootScope.patient_health_analytic_parameter_required = "* Please Select Health Analytic Parameter";
        $rootScope.patient_health_analytic_reading_required = "* Please Enter Health Analytic Reading";
        $rootScope.patient_health_analytic_reading_date_required = "* Please Enter Health Analytic Reading Date";
        $rootScope.promo_required = "* Please Enter Promo Code";
        $rootScope.only_alphabet = "* Allows only alphabet characters";

        /* tooltip constant */
        $rootScope.add_patient_tooltip = "Add New Patient";
        $rootScope.watch_video_tooltip = "Watch A Video";
        $rootScope.help_support_tooltip = "Help & Support";
        $rootScope.whats_new_tooltip = "What's New?";
        $rootScope.my_account_tooltip = "My Account";
        $rootScope.refresh_tooltip = "Refresh Calendar";
        $rootScope.add_appointment_tooltip = "Add New Appointment";
        $rootScope.call_tooltip = "Call Doctor";
        $rootScope.pdf_tooltip = "Print/Share with Patient";
        $rootScope.pdf_share_tooltip = "Share with others";
        $rootScope.invoice_tooltip = "View Invoice";
        $rootScope.invoice_tooltip = "View Invoice";
        $rootScope.procedure_required = "* Please Enter Procedure Name";
        $rootScope.incomplete_profile = "Please Complete Your Profile First";
        $rootScope.account_pending_for_approval = "Thank you for registring with MedSign!<br/>Your account is pending for approval.";
        $rootScope.unverified_email = "Please Verify Your Email First";
        $rootScope.add_clinic_first = "Please Add Clinic First";
        $rootScope.DOCTOR_ICON = "<i class='fa fa-user-md'></i>";
        $rootScope.DOCTOR_ASSISTANT_ICON = '<i class="fa fa-user-circle-o assistant-doctor-icon"></i>';
        $rootScope.RECEPTIONIST_ICON = '<i class="fa fa-user receptionist-icon"></i>';
        $rootScope.PATIENT_ICON = '<i class="fa fa-wheelchair patient-icon"></i>';
    }
    
    /* smoking constant
        //I don\'t smoke
        //I use to, but I\'hv quit
        //1–2/day
        //3–5/day
        //5–10/day
        //>10/day
    */
    angular
            .module('medeasy').constant("SMOKE", [
        {"id": "1", "name": "Non smoker"}, 
        {"id": "2", "name": "Quit smoking"},
        {"id": "3", "name": "Chain Smoker"},
        {"id": "4", "name": "Heavy Smoker"},
        {"id": "5", "name": "Moderate Smoker"},
        {"id": "6", "name": "Light Smoker"},
        {"id": "7", "name": "Casual Smoker"}
    ]);

    /* alcohol constant */
    angular
            .module('medeasy').constant("ALCOHOL", [
        {"id": "1", "name": "Non Drinker"},
        {"id": "2", "name": "Occasional Drinker"},
        {"id": "3", "name": "Social Drinker"},
        {"id": "4", "name": "Casual Drinker"},
        {"id": "5", "name": "Quit Drinking"}
    ]);

    /* appointment type constant */
    angular.module("medeasy").constant("APPOINTMENT_TYPE", [{"id": "1", "name": "Doctor Visit"},{"id": "5", "name": "Tele Consultation"}]);

    /* user role constant */
    angular
            .module("medeasy").constant("ROLE", [
        {"id": "1", "name": "Doctor"},
        {"id": "2", "name": "Assistant"},
        {"id": "3", "name": "Receptionist"},
        {"id": "4", "name": "Home pharmacy"}
    ]);

    /* week days constants */
    angular.module("medeasy").constant("DAYS", [
        {"id": "1", "name": "Mon"},
        {"id": "2", "name": "Tue"},
        {"id": "3", "name": "Wed"},
        {"id": "4", "name": "Thurs"},
        {"id": "5", "name": "Fri"},
        {"id": "6", "name": "Sat"},
        {"id": "7", "name": "Sun"}

    ]);

    angular.module("medeasy").constant("RECAPTCHKEY",{key: '6LdK5owUAAAAAPhqC8_cVQ8SqQEHQuJ5qx8fHzGu'});
})();

/* Module: app.routes */
(function () {
    angular
            .module('app.routes', ['ui.router'])
            .constant("VERSIONCONTROL",{'appVer':'V3.74','appResourceVer':'V3.74','appCssVer':'V3.74'})
            .config(function ($stateProvider, $urlRouterProvider, VERSIONCONTROL) {
                $urlRouterProvider.otherwise('/app/login');
                $stateProvider
                        .state('app', {
                            url: '/app',
                            templateUrl: 'app/views/app.html?'+VERSIONCONTROL.appResourceVer,
                        })
                        /* login routes */
                        .state('app.login', {
                            url: '/login',
                            title: 'Login',
                            templateUrl: 'app/views/login/login.html?'+VERSIONCONTROL.appResourceVer,
                            controller: 'LoginController'
                        })
                        /* dashboard routes */
                        .state('app.dashboard', {
                            url: '/dashboard',
                            title: 'Dashboard',
                            templateUrl: 'app/views/dashboard.html?'+VERSIONCONTROL.appResourceVer,
                            controller: 'DashboardController',
                            abstract: true
                        })
                        .state('app.dashboard.calendar', {
                            url: '/calendar',
                            title: 'calendar',
                            templateUrl: 'app/views/calendar_menu/calendar.html?'+VERSIONCONTROL.appResourceVer,
                        })
                        /* setting routes */
                        .state('app.dashboard.setting', {
                            url: '/setting',
                            title: 'Setting',
                            templateUrl: 'app/views/setting/setting.html?'+VERSIONCONTROL.appResourceVer,
                            controller: 'SettingController',
                            abstract: true
                        })
                        .state('app.dashboard.setting.clinic_details', {
                            url: '/clinic_details',
                            title: 'clinic_details',
                            templateUrl: 'app/views/setting/clinic_details.html?'+VERSIONCONTROL.appResourceVer,
                        })
                        .state('app.dashboard.setting.clinic_staff', {
                            url: '/clinic_staff',
                            title: 'clinic_staff',
                            templateUrl: 'app/views/setting/clinic_staff.html?'+VERSIONCONTROL.appResourceVer,
                        })
                        .state('app.dashboard.setting.diet_instruction', {
                            url: '/diet_instruction',
                            title: 'diet_instruction',
                            templateUrl: 'app/views/setting/diet_instruction.html?'+VERSIONCONTROL.appResourceVer,
                        })
                        .state('app.dashboard.setting.calendar', {
                            url: '/calendar',
                            title: 'calendar',
                            templateUrl: 'app/views/setting/calendar.html?'+VERSIONCONTROL.appResourceVer,
                        })
                        .state('app.dashboard.setting.alert', {
                            url: '/alert',
                            title: 'alert',
                            templateUrl: 'app/views/setting/alert.html?'+VERSIONCONTROL.appResourceVer,
                        })
                        .state('app.dashboard.setting.billing', {
                            url: '/billing',
                            title: 'billing',
                            templateUrl: 'app/views/setting/billing.html?'+VERSIONCONTROL.appResourceVer,
                        })
                        .state('app.dashboard.setting.payment_mode', {
                            url: '/payment_mode',
                            title: 'payment_mode',
                            templateUrl: 'app/views/setting/payment_mode.html?'+VERSIONCONTROL.appResourceVer,
                        })
                        .state('app.dashboard.setting.share_link', {
                            url: '/share_link',
                            title: 'share_link',
                            templateUrl: 'app/views/setting/share_link.html?'+VERSIONCONTROL.appResourceVer,
                        })
                        .state('app.dashboard.setting.printouts', {
                            url: '/printouts',
                            title: 'printouts',
                            templateUrl: 'app/views/setting/printouts.html?'+VERSIONCONTROL.appResourceVer,
                        })
                        .state('app.dashboard.setting.fees', {
                            url: '/fees',
                            title: 'fees',
                            templateUrl: 'app/views/setting/fee_structure.html?'+VERSIONCONTROL.appResourceVer,
                        })
                        .state('app.dashboard.setting.clinical', {
                            url: '/clinical',
                            title: 'clinical',
                            templateUrl: 'app/views/setting/clinical.html?'+VERSIONCONTROL.appResourceVer,
                        })
                        .state('app.dashboard.setting.brand', {
                            url: '/brand',
                            title: 'brand',
                            templateUrl: 'app/views/setting/brand.html?'+VERSIONCONTROL.appResourceVer,
                        })
                        .state('app.dashboard.setting.template', {
                            url: '/template',
                            title: 'template',
                            templateUrl: 'app/views/setting/template.html?'+VERSIONCONTROL.appResourceVer,
                        })
                        .state('app.dashboard.setting.share', {
                            url: '/share',
                            title: 'share',
                            templateUrl: 'app/views/setting/share.html?'+VERSIONCONTROL.appResourceVer,
                        })
                        .state('app.dashboard.setting.security', {
                            url: '/security',
                            title: 'security',
                            templateUrl: 'app/views/setting/security.html?'+VERSIONCONTROL.appResourceVer,
                        })
                        .state('app.dashboard.setting.favdoctor', {
                            url: '/favdoctor',
                            title: 'favdoctor',
                            templateUrl: 'app/views/setting/my_fav_doctor.html?'+VERSIONCONTROL.appResourceVer,
                        })
                        .state('app.dashboard.setting.patient_groups', {
                            url: '/patient_groups',
                            title: 'patient_groups',
                            templateUrl: 'app/views/setting/patient_groups.html?'+VERSIONCONTROL.appResourceVer,
                        })
                        
                        /* survey page route */
                        .state('app.survey', {
                            url: '/survey',
                            title: 'Survey',
                            templateUrl: 'app/views/survey/survey.html?'+VERSIONCONTROL.appResourceVer,
                            controller: 'SurveyController'
                        })

                        /* subscription page route */
                        .state('app.subscription', {
                            url: '/subscription',
                            title: 'Subscription',
                            templateUrl: 'app/views/subscription/subscription.html?'+VERSIONCONTROL.appResourceVer,
                            controller: 'SubscriptionController'
                        })

                        .state('app.subscription_history', {
                            url: '/subscription_history',
                            title: 'subscription_history',
                            templateUrl: 'app/views/subscription/sub_history.html?'+VERSIONCONTROL.appResourceVer,
                            controller: 'SubscriptionController'
                        })

                        /* import page route */
                        .state('app.dashboard.setting.import', {
                            url: '/import',
                            title: 'Import',
                            templateUrl: 'app/views/import/import.html?'+VERSIONCONTROL.appResourceVer,
                            controller: 'ImportController'
                        })

                        /* whats new page route */
                        .state('app.whats', {
                            url: '/whats',
                            title: 'Whats',
                            templateUrl: 'app/views/whats_new.html?'+VERSIONCONTROL.appResourceVer,
                            controller: 'DashboardController'
                        })

                        /* my profile routes */
                        .state('app.profile', {
                            url: '/profile',
                            title: 'Profile',
                            templateUrl: 'app/views/profile/profile.html?'+VERSIONCONTROL.appResourceVer,
                            controller: 'UserController'
                        })
                        .state('app.profile.my_profile_view', {
                            url: '/my_profile_view',
                            title: 'Profile',
                            templateUrl: 'app/views/profile/my_profile_view.html?'+VERSIONCONTROL.appResourceVer,
                        })
                        .state('app.profile.clinic_view', {
                            url: '/clinic_view',
                            title: 'Profile',
                            templateUrl: 'app/views/profile/clinic_view.html?'+VERSIONCONTROL.appResourceVer,
                        })
                        .state('app.profile.setting_view', {
                            url: '/setting_view',
                            title: 'Profile',
                            templateUrl: 'app/views/profile/setting_view.html?'+VERSIONCONTROL.appResourceVer,
                        })
                        /* complete profile routes */
                        .state('app.complete_profile_view', {
                            url: '/complete_profile_view',
                            title: 'Profile',
                            templateUrl: 'app/views/profile/complete_profile_view.html?'+VERSIONCONTROL.appResourceVer,
                        })
                        .state('app.complete_profile_view.personal', {
                            url: '/personal',
                            title: 'Profile',
                            templateUrl: 'app/views/profile/personal_view.html?'+VERSIONCONTROL.appResourceVer
                        })
                        .state('app.complete_profile_view.edu', {
                            url: '/edu',
                            title: 'Edu',
                            templateUrl: 'app/views/profile/edu_view.html?'+VERSIONCONTROL.appResourceVer

                        })
                        .state('app.complete_profile_view.reg', {
                            url: '/reg',
                            title: 'reg',
                            templateUrl: 'app/views/profile/reg_view.html?'+VERSIONCONTROL.appResourceVer
                        })
                        .state('app.complete_profile_view.award', {
                            url: '/award',
                            title: 'award',
                            templateUrl: 'app/views/profile/award_view.html?'+VERSIONCONTROL.appResourceVer
                        })
                        /* patient routes */
                        .state('app.dashboard.patient', {
                            url: '/patient',
                            title: 'patient',
                            templateUrl: 'app/views/patient/patient.html?'+VERSIONCONTROL.appResourceVer,
                            controller: 'PatientController'
                        })
                        /* staff routes */
                        .state('app.dashboard.staff', {
                            url: '/staff',
                            title: 'staff',
                            templateUrl: 'app/views/staff/staff.html?'+VERSIONCONTROL.appResourceVer,
                            controller: 'UserController'
                        })
                        /* refer routes */
                        .state('app.dashboard.refer', {
                            url: '/refer',
                            title: 'refer',
                            templateUrl: 'app/views/refer/refer.html?'+VERSIONCONTROL.appResourceVer
                        })
                        /* communications routes */
                        .state('app.dashboard.communications', {
                            url: '/communications',
                            title: 'communications',
                            templateUrl: 'app/views/communications/communications.html?'+VERSIONCONTROL.appResourceVer,
                            controller: 'CommunicationsController',
                            abstract: true
                        })
                        /* communications list routes */
                        .state('app.dashboard.communications.list', {
                            url: '/list',
                            title: 'list',
                            templateUrl: 'app/views/communications/list.html?'+VERSIONCONTROL.appResourceVer
                        })
                        /* teleconsultation routes */
                        .state('app.dashboard.teleconsultation', {
                            url: '/teleconsultation',
                            title: 'teleconsultation',
                            templateUrl: 'app/views/teleconsultation/teleconsultation.html?'+VERSIONCONTROL.appResourceVer,
                            controller: 'TeleconsultationController',
                            abstract: true
                        })
                        /* teleconsultation list routes */
                        .state('app.dashboard.teleconsultation.list', {
                            url: '/list',
                            title: 'list',
                            templateUrl: 'app/views/teleconsultation/list.html?'+VERSIONCONTROL.appResourceVer
                        })
                        /* reports routes */
                        .state('app.dashboard.reports', {
                            url: '/reports',
                            title: 'reports',
                            templateUrl: 'app/views/report/report.html?'+VERSIONCONTROL.appResourceVer,
                            controller: 'ReportController',
                            abstract: true
                        })
                        /* report member summary routes */
                        .state('app.dashboard.reports.member_summary', {
                            url: '/member_summary',
                            title: 'member_summary',
                            templateUrl: 'app/views/report/member_summary.html?'+VERSIONCONTROL.appResourceVer
                        })
                        /* report mob summary routes */
                        .state('app.dashboard.reports.mob_summary', {
                            url: '/mob_summary',
                            title: 'mob_summary',
                            templateUrl: 'app/views/report/mob_summary.html?'+VERSIONCONTROL.appResourceVer
                        })
                        /* report lost patients routes */
                        .state('app.dashboard.reports.lost_patient', {
                            url: '/lost_patient',
                            title: 'lost_patient',
                            templateUrl: 'app/views/report/lost_patient.html?'+VERSIONCONTROL.appResourceVer
                        })
                        /* report patients progress routes */
                        .state('app.dashboard.reports.patient_progress', {
                            url: '/patient_progress',
                            title: 'patient_progress',
                            templateUrl: 'app/views/report/patient_progress.html?'+VERSIONCONTROL.appResourceVer
                        })
                        /* report invoice summary routes */
                        .state('app.dashboard.reports.invoice_summary', {
                            url: '/invoice_summary',
                            title: 'invoice_summary',
                            templateUrl: 'app/views/report/invoice_summary.html?'+VERSIONCONTROL.appResourceVer
                        })
                        /* report cancel_appointment routes */
                        .state('app.dashboard.reports.cancel_appointment', {
                            url: '/cancel_appointment',
                            title: 'cancel_appointment',
                            templateUrl: 'app/views/report/cancel_appointment.html?'+VERSIONCONTROL.appResourceVer
                        })
                        /* integration routes */
                        .state('app.dashboard.integration', {
                            url: '/integration',
                            title: 'integration',
                            templateUrl: 'app/views/integration/integration.html?'+VERSIONCONTROL.appResourceVer
                        })
                        /* back office routes */
                        .state('app.dashboard.back', {
                            url: '/back',
                            title: 'back',
                            templateUrl: 'app/views/back/back.html?'+VERSIONCONTROL.appResourceVer
                        });
            });
})();
/* Module: app.login */
(function () {
    angular.module("app.login", []);
})();
/* Module: app.dashboard */
(function () {
    angular.module("app.dashboard", ['angularLoad']);
})();
/* Module: app.survey */
(function () {
    angular.module("app.survey", []);
})();
/* Module: app.subscription */
(function () {
    angular.module("app.subscription", []);
})();
/* Module: app.import */
(function () {
    angular.module("app.import", []);
})();
/* Module: app.reports */
(function () {
    angular.module("app.dashboard.reports", []);
})();
/* Module: app.communications */
(function () {
    angular.module("app.dashboard.communications", []);
})();
/* Module: app.teleconsultation */
(function () {
    angular.module("app.dashboard.teleconsultation", []);
})();
/* Module: app.dashboard.setting */
(function () {
    angular.module("app.dashboard.setting", []);
})();
/* Module: app.profile */
(function () {
    angular.module("app.profile", []);
})();

angular.module("medeasy")
        .run(function ($http, $rootScope, SettingService, CommonService, SweetAlert, $location, $timeout, $state, LoginService, AuthService, $window, $localStorage, $interval, $templateCache, APPOINTMENT_TYPE, VERSIONCONTROL, CommonService) {
            $rootScope.stopScrollAutoHideHide = false;
            $rootScope.flg_to_set_advertisement_data = false;
            $rootScope.current_advertisement_url = '';
            
            $rootScope.getVer = function(verType){
                if(verType == 1) /*  APP Version */
                    return VERSIONCONTROL.appVer;
                else if(verType == 2) /*  APP Resources Version */
                    return VERSIONCONTROL.appResourceVer;
                else if(verType == 3) /* APP CSS Version */
                    return VERSIONCONTROL.appCssVer;
            };
            $rootScope.brandDurationTypeList = [
                {id: '1',name: 'Days'},
                {id: '2',name: "Weeks"},
                {id: '3',name: 'Months'}
            ];
            $rootScope.brandIntakeList = [
                {id: '1',name: 'Before Food'}, 
                {id: '2',name: 'After Food'}, 
                {id: '3',name: 'Along with Food'}, 
                {id: '4',name: 'Empty Stomach'}, 
                {id: '5',name: 'As Directed'}
            ];
            $rootScope.calendarDuration = [
                {value: '5',name: '05 MINUTES'}, 
                {value: '10',name: '10 MINUTES'}, 
                {value: '15',name: '15 MINUTES'}, 
                {value: '20',name: '20 MINUTES'}, 
                {value: '25',name: '25 MINUTES'}, 
                {value: '30',name: '30 MINUTES'}, 
                {value: '40',name: '40 MINUTES'}, 
                {value: '50',name: '50 MINUTES'}, 
                {value: '55',name: '55 MINUTES'}, 
                {value: '60',name: '1 HOUR'}, 
                {value: '120',name: '2 HOURS'}, 
                {value: '180',name: '3 HOURS'}, 
                {value: '240',name: '4 HOURS'}, 
                {value: '300',name: '5 HOURS'}
            ];
            $rootScope.hoursFormat = [
                {value: '1',name: '12 Hours'}, 
                {value: '2',name: '24 Hours'}
            ];
            $rootScope.healthAdviceSchedule = [
                {value: '1',name: 'Day'}, 
                {value: '2',name: 'Week'},
                {value: '3',name: 'Month'}
            ];
            $rootScope.healthAdviceSendDay = [
                {value: '1',name: 'Mon'}, 
                {value: '2',name: 'Tue'},
                {value: '3',name: 'Wed'},
                {value: '4',name: 'Thu'},
                {value: '5',name: 'Fri'},
                {value: '6',name: 'Sat'},
                {value: '7',name: 'Sun'}
            ];
            $rootScope.familyRelation = [
                {id: 1,name: 'Mother'},
                {id: 2,name: "Father"},
                {id: 3,name: 'Brother'},
                {id: 4,name: 'Sister'},
                {id: 5,name: 'Wife'},
                {id: 6,name: 'Son'},
                {id: 7,name: 'Daughter'},
                {id: 8,name: 'Husband'},
                {id: 9,name: 'Grandparent'},
                {id: 10,name: 'Grandchild'},
                {id: 11,name: 'Other Relative'},
                {id: 12,name: 'Others'}
            ];
            $rootScope.foodPreference = [
                {id: '1',name: 'Vegetarian'},
                {id: '2',name: "Non-Vegetarian"},
                {id: '3',name: 'Eggetarian'},
                {id: '4',name: 'Vegan'}
            ];
            $rootScope.activityLevels = [
                {id: '1',name: 'Cycling'},
                {id: '2',name: "Walking"},
                {id: '3',name: 'Running'},
                {id: '4',name: 'cardio'},
                {id: '5',name: 'exercise/yoga'},
                {id: '6',name: 'Others'}
            ];
            $rootScope.sidebarMenu = [];
            $rootScope.isObjectEmpty = function(varObj){
               return Object.keys(varObj).length === 0;
            };
            
            $rootScope.handleError = function (error) {
                $rootScope.app.isLoader = false;
                if (error.status == 401) {
                    $rootScope.app.is_not_valid = false;
                    $rootScope.error_message = '';
                    SweetAlert.swal($rootScope.app.logout_error);
                }
            };
            $rootScope.storeDeviceTokens = function (params) {
                var hwid = params.hwid;
                var pushToken = params.pushToken;
                var userId = params.userId;
                $localStorage.hwid = hwid;
                $localStorage.pushToken = pushToken;
                $localStorage.userId = userId;
                var request = {};
                $timeout(function () {
                    if (AuthService.currentUser() != undefined && AuthService.currentUser().user_id) {
                        request.device_token = $localStorage.pushToken;
                        request.device_type = 'web';
                        LoginService.updateDeviceToken(request, function (response) {
                        });
                    }else{
                        // console.log($localStorage.pushToken);
                        // console.log($localStorage.userId);
                    }
                }, 100);
            }
            $rootScope.getSidebarMenu = function () {
                $timeout(function () {
                    if (($rootScope.sidebarMenu == undefined || $rootScope.sidebarMenu.length <= 0) && $rootScope.currentUser) {
                        SettingService
                                .getSidebarMenu('', function (response) {
                                    if (response.data) {
                                        $rootScope.sidebarMenu = angular.copy(JSON.parse(response.data.sidebar_menu));
                                        $rootScope.role = JSON.parse(response.data.role);
                                        $rootScope.role_type = JSON.parse(response.role_type);
                                        AuthService
                                                .setSideMenu(angular.copy($rootScope.sidebarMenu), $rootScope.role, $rootScope.role_type);
                                    }
                                }, function (error) {
                                    $rootScope.handleError(error);
                                    //$rootScope.getSidebarMenu();
                                    if (error.status == 403) {
                                        $rootScope.getSidebarMenu();
                                    }
                                });
                    }
                }, 100);
            };
            $rootScope.getTermsCondition = function () {
                SettingService
                        .getTermsCondition('', function (response){
                            if (response.status == true){
                                $rootScope.terms_condition_contain = response.data.static_page_content;
                            }
                        }, function (error) {
                            $rootScope.handleError(error);
                        });
            };
            $rootScope.checkPermission = function (module, type) {
                if ($rootScope.role != undefined && $rootScope.role[module] != undefined && !!$rootScope.role[module] && $rootScope.role[module][type] == 'on') {
                    return true;
                }
                return false;
            };
            $rootScope.updateBackDropModalHeight = function(className) {
                setTimeout( function () {
                    var modal_height = $('.' + className).height();
                    var window_hieght = $(window).height();
                    if(window_hieght > modal_height)
                        modal_height = window_hieght;
                    var backdrop_height = $('.modal-backdrop').height();
                    if($('.' + className) && modal_height > backdrop_height)
                        $('.modal-backdrop').height(modal_height + 300);
                },10);
            }
            var flag = 1;
            $rootScope.lastAPICallTime = $.now();
            $rootScope.APPOINTMENT_TYPE = APPOINTMENT_TYPE;
            $interval(function () {
                var currentTime = $.now();
                var diff = (currentTime - $localStorage.lastAPICallTime) / 600000;
                if (diff > 150 && flag == 1 && $location.path() != '/app/login'){
                    $("#term_condition_modal").modal("hide");
                    $("#autologout_modal").modal("show");
                    $rootScope.phone_number = $rootScope.currentUser.user_phone_number;
                    flag = 2;
                    
                    if($rootScope.addvertTimer != undefined)
                        $interval.cancel($rootScope.addvertTimer);
                    if($rootScope.mainAddvertTimer != undefined)
                        $interval.cancel($rootScope.mainAddvertTimer);
                    if($rootScope.addvertTimerRefresh != undefined)
                        $timeout.cancel($rootScope.addvertTimerRefresh);
                    
                    $timeout(function(){
                        if($rootScope.advertisement_data != undefined)
                            delete $rootScope.advertisement_data;
                        if($rootScope.advertisement_next_request_time != undefined)
                            delete $rootScope.advertisement_next_request_time;
                        if($rootScope.currentad != undefined)
                            delete $rootScope.currentad;
                        if($rootScope.currentAdvtData != undefined)
                            delete $rootScope.currentAdvtData;
                    });
                    
                    AuthService.logout();
                } else {
                    $localStorage.lastAPICallTime = $rootScope.lastAPICallTime;
                }
            }, 1000);
            $http.defaults.headers.common.Authorization = 'Basic ' + btoa($rootScope.app.basicAuth);
            /* stop loading after sometime */
            $timeout(function () { // simulate long page loading             
                $rootScope.app.isLoader = false; // turn "off" the flag 
            }, 5000)

            /* check logged in user detail from localstorage */
            $rootScope.$watch(AuthService.isLoggedIn, function (isLoggedIn) {
                $rootScope.isLoggedIn = isLoggedIn;
                $rootScope.currentUser = AuthService.currentUser();
                var sdmenu = angular.copy(AuthService.getSideMenuFromLocal());
                if(sdmenu){
                    $rootScope.sidebarMenu = sdmenu;
                }else{
                    $rootScope.sidebarMenu = [];
                }
                $rootScope.role = AuthService.getRoleFromLocal();
            });

            /* $rootScope.$watch($rootScope.sidebarMenu, function () {
                console.log('sidebarMenu',$rootScope.currentUser);
                if ($rootScope.currentUser != undefined && $rootScope.currentUser.doctor_detail_is_term_accepted != undefined && $rootScope.currentUser.doctor_detail_is_term_accepted != 1) {
                    $timeout(function () {
                        $("#term_condition_modal").modal("show");
                    }, 500);
                }
                $rootScope.sidebarMenu = angular.copy(AuthService.getSideMenuFromLocal());
            }); */

            $rootScope.$watch(
                function(){
                    return $rootScope.sidebarMenu; 
                },
                function(){
                    if ($rootScope.currentUser != undefined && $rootScope.currentUser.doctor_detail_is_term_accepted != undefined && $rootScope.currentUser.doctor_detail_is_term_accepted != 1) {
                        $timeout(function () {
                            $("#term_condition_modal").modal("show");
                        }, 500);
                    }
                }
            ,true);
            $rootScope.$watch($rootScope.role, function (role) {
                $rootScope.role = AuthService.getRoleFromLocal();
            });
            $rootScope.$watch($rootScope.terms_condition_contain, function () {
                if ($rootScope.terms_condition_contain == '') {
                    $rootScope.getTermsCondition();
                }
            });

            //$rootScope.$watch($rootScope.currentUser, function (currentUser) {
            //console.log($rootScope.currentUser);
                /*if (currentUser.doctor_detail_is_term_accepted != 1) {
                    //$rootScope.getTermsCondition();
                    //console.log($rootScope.currentUser)
                    $timeout(function () {
                        $("#term_condition_modal").modal("show");
                    }, 500);
                }*/
            //});

            /* ajax start loader off */
            $http.defaults.transformResponse.push(function (data) {
                if (data.authentication == false) {
                    $rootScope.app.is_not_valid = false;
                    $rootScope.error_message = '';
                    
                    if($rootScope.addvertTimer != undefined)
                        $interval.cancel($rootScope.addvertTimer);
                    if($rootScope.mainAddvertTimer != undefined)
                        $interval.cancel($rootScope.mainAddvertTimer);
                    if($rootScope.addvertTimerRefresh != undefined)
                        $timeout.cancel($rootScope.addvertTimerRefresh);
                    
                    $timeout(function(){
                        if($rootScope.advertisement_data != undefined)
                            delete $rootScope.advertisement_data;
                        if($rootScope.advertisement_next_request_time != undefined)
                            delete $rootScope.advertisement_next_request_time;
                        if($rootScope.currentad != undefined)
                            delete $rootScope.currentad;
                        if($rootScope.currentAdvtData != undefined)
                            delete $rootScope.currentAdvtData;
                    });
                    
                    AuthService.logout();
                    $state.go("app.login");
                }
                $rootScope.lastAPICallTime = $.now();
                $rootScope.app.isLoader = false;
                return data;
            });

            /* route change loading */
            $rootScope.$on('$stateChangeStart', function ($event, next, current) {
                $timeout(function () {
                    $rootScope.app.isLoader = false; // turn "off" the flag 
                }, 5000);
                $rootScope.app.isLoader = true;
                if (AuthService.isLoggedIn() != true && next.name != "app.login") {
                    $event.preventDefault();
                    $state.go("app.login");
                } else if (AuthService.isLoggedIn() == true && next.name == "app.login") {
                    $event.preventDefault();
                    
                    if($rootScope.addvertTimer != undefined)
                        $interval.cancel($rootScope.addvertTimer);
                    if($rootScope.mainAddvertTimer != undefined)
                        $interval.cancel($rootScope.mainAddvertTimer);
                    if($rootScope.addvertTimerRefresh != undefined)
                        $timeout.cancel($rootScope.addvertTimerRefresh);
                    
                    $timeout(function(){
                        if($rootScope.advertisement_data != undefined)
                            delete $rootScope.advertisement_data;
                        if($rootScope.advertisement_next_request_time != undefined)
                            delete $rootScope.advertisement_next_request_time;
                        if($rootScope.currentad != undefined)
                            delete $rootScope.currentad;
                        if($rootScope.currentAdvtData != undefined)
                            delete $rootScope.currentAdvtData;
                    });
                    AuthService.logout();
                }
                $rootScope.page_number = 1;
                $rootScope.page_name = '';
                angular.forEach($rootScope.pages_number, function (value, key) {
                    if (value.value == next.name) {
                        $rootScope.page_number = value.id;
                        $rootScope.page_name = value.name;
                    }
                });
                    //  LoginService
                    //          .getVideoList(page_number, function (response) {
                    //              if (response.data) {
                    //                  $rootScope.help_video_url = 'app/videos/' + response.data.me_video_url;
                    //              }
                    //          });
            });
            $rootScope.videos_list = [];
            $rootScope.isShowHelpVideo = false;
            $rootScope.video_title = '';
            $rootScope.getVideosList = function() {
                $rootScope.help_video_url = '';
                CommonService
                        .getVideoList($rootScope.page_number, function (response) {
                            if (response.data) {
                                $rootScope.videos_list = response.data;
                                if($rootScope.videos_list.length == 1) {
                                    $rootScope.video_title = $rootScope.videos_list[0].me_video_title;
                                    // console.log($rootScope.app.apiUrl + '/help/av/' + $rootScope.videos_list[0].key);
                                    $rootScope.help_video_url = $rootScope.app.apiUrl + '/help/av/' + $rootScope.videos_list[0].key;
                                    $rootScope.isShowHelpVideo = true;
                                    $(".help-video-body video").load();
                                } else {
                                    $rootScope.isShowHelpVideo = false;
                                }
                                // $rootScope.help_video_url = 'app/videos/' + response.data.me_video_url;
                            } else {
                                $rootScope.videos_list = [];
                            }
                        });
            };
            /* Google Analytics */
            $rootScope.gTrack = function(name){
                // if($rootScope.ENV == 'PROD')
                //     $window.ga('send', 'pageview', $location.path()+'/'+name);
            }
            $rootScope.$on('$stateChangeSuccess', function ($event, next, current) {
                $rootScope.app.isLoader = false; // turn "off" the flag 
                /* Google Analytics */
                // if($rootScope.ENV == 'PROD')
                //     $window.ga('send', 'pageview', $location.path());
                if(next.name == "app.dashboard.calendar"){
                    $rootScope.flg_to_force_calendar_refresh = true;
                }
            });
            $rootScope.$on('$stateChangeError', function ($event, next, current) {
                $rootScope.app.isLoader = false; // turn "off" the flag 
            });

            /* [START] Addvertisement functions all */
            $rootScope.getAdvertisementData = function(){
                var request = {doctor_id: $rootScope.current_doctor.user_id}
                CommonService.getAdvertisementData(request, function (response) {
                    if(response.status == true){
                        if(response.advertisement_data != undefined){
                            $rootScope.advertisement_data = response.advertisement_data;
                            if(response.next_request_time != undefined)
                                $rootScope.advertisement_next_request_time = response.next_request_time;
                        }
                        $rootScope.setAdvertisementData();
                        $rootScope.flg_to_set_advertisement_data = true;
                    }else{
                        $timeout(function(){
                            delete $rootScope.advertisement_data;
                            delete $rootScope.advertisement_next_request_time;
                            delete $rootScope.currentad;
                            delete $rootScope.currentAdvtData;
                        });
                        if($rootScope.addvertTimer != undefined)
                            $interval.cancel($rootScope.addvertTimer);
                        if($rootScope.mainAddvertTimer != undefined)
                            $interval.cancel($rootScope.mainAddvertTimer);
                        if($rootScope.addvertTimerRefresh != undefined)
                            $timeout.cancel($rootScope.addvertTimerRefresh);
                    }
                });
            }
            $rootScope.setAdvertisementData = function(){
                $rootScope.currentad = null; $rootScope.currentAdvtData = null;
                if($rootScope.advertisement_data != undefined){
                    
                    if($rootScope.addvertTimer != undefined)
                        $interval.cancel($rootScope.addvertTimer);
                    if($rootScope.mainAddvertTimer != undefined)
                        $interval.cancel($rootScope.mainAddvertTimer);
                    
                    if($rootScope.advertisement_data.length > 1){
                        $rootScope.currentAdvtData = $rootScope.advertisement_data[0];
                        $rootScope.setMainAdvertisementSlots(0);
                        $rootScope.setAdvertisement();
                    }else{
                        $rootScope.currentAdvtData = $rootScope.advertisement_data[0];
                        $rootScope.setAdvertisement();
                    }
                }
                
                if($rootScope.advertisement_next_request_time != undefined && $rootScope.advertisement_next_request_time > 0){
                    if($rootScope.addvertTimerRefresh != undefined)
                        $timeout.cancel($rootScope.addvertTimerRefresh);
                    
                    $rootScope.addvertTimerRefresh = $timeout(function(){ $rootScope.getAdvertisementData(); }, $rootScope.advertisement_next_request_time);
                }
            }
            $rootScope.setMainAdvertisementSlots = function(mainAdvtIdx){
                $rootScope.mainAddvertTimer = $interval(function () {
                    mainAdvtIdx++;
                    if (mainAdvtIdx > $rootScope.advertisement_data.length - 1) {
                        mainAdvtIdx = 0
                    }
                    $timeout(function(){
                        $rootScope.currentAdvtData = $rootScope.advertisement_data[mainAdvtIdx];
                        $interval.cancel($rootScope.addvertTimer);
                        $interval.cancel($rootScope.mainAddvertTimer);
                        $rootScope.setAdvertisement();
                        $rootScope.setMainAdvertisementSlots(mainAdvtIdx);
                    }); 
                }, $rootScope.currentAdvtData.advertisement_rotate_time);
            }
            $rootScope.setAdvertisement= function(){
                $interval.cancel($rootScope.addvertTimer);
                if($rootScope.currentAdvtData.advertisement_type_data == undefined)
                    return;
                if($rootScope.currentAdvtData.advertisement_type_data.length > 1){
                    $timeout(function(){ $rootScope.currentad = $rootScope.currentAdvtData.advertisement_type_data[0];});
                    var index = 0;
                    $rootScope.addvertTimer = $interval(function () {
                        index++;
                        if (index > $rootScope.currentAdvtData.advertisement_type_data.length - 1) {
                            index = 0;
                        }
                        $timeout(function(){ $rootScope.currentad = $rootScope.currentAdvtData.advertisement_type_data[index]; });
                    }, $rootScope.currentAdvtData.advertisement_image_rotate_timing);
                }else{
                    $timeout(function(){$rootScope.currentad = $rootScope.currentAdvtData.advertisement_type_data[0];});
                }
            }
            $rootScope.setAdvertisementVideoUrls= function(curAdd){
                $rootScope.current_advertisement_url = curAdd.advertisement_videourl;
            }
            $rootScope.unSetAdvertisementVideoUrls= function(){
                $timeout(function(){
                    $rootScope.current_advertisement_url = '';
                    $('#advertisement_video_modal').find("iframe").attr("src", "");
                });
            }
            /* [END] */
        });

angular.module("medeasy")
        .config(['ngToastProvider',
            function (ngToastProvider) {
                ngToastProvider.configure({
                    additionalClasses: 'my-animation'
                });
            }
        ]);

angular.module("medeasy").directive('clientAutoComplete', ['$filter', '$rootScope', clientAutoCompleteDir]);

/* CSRF token code */
angular.module("medeasy")
        .config(['$httpProvider', function ($httpProvider) {
                $httpProvider.interceptors.push(function ($q, $cookies, $rootScope, $injector, AuthService, $location, SweetAlert) {
                    var incrementalTimeout = 1000;
                    function retryRequest(httpConfig) {
                        var $timeout = $injector.get('$timeout');
                        var thisTimeout = incrementalTimeout;
                        incrementalTimeout *= 2;
                        return $timeout(function () {
                            var $http = $injector.get('$http');
                            //incrementalTimeout = 1000;
                            return $http(httpConfig);
                        }, thisTimeout);
                    };
                    return {
                        request: function (req) {
                            if (req.method != 'get') {
                                req.data = req.data || {};
                                req.data['csrf_md_name']    = $cookies.get('80d12dac0e22db804aa89d9889a95c16014e5178');
                                req.data['device_type']     = 'web';
                                req.data['user_type']       = '2';
                                req.data['csrf_md_name']    = $cookies.get('80d12dac0e22db804aa89d9889a95c16014e5178');
                                req.headers['X-Csrf-Token'] = $cookies.get('80d12dac0e22db804aa89d9889a95c16014e5178');
                                if (req.data.is_pagination) {
                                    $rootScope.app.isLoader = false;
                                } else {
                                    $rootScope.app.isLoader = true;
                                }
                            }
                            return req;
                        },
                        responseError: function (response) {
                            if (response.status == 403) {
                                //csrf invalid code
                                if (incrementalTimeout < 5000) {
                                    //return retryRequest(response.config);
                                }
                            } else if (response.status == 401) {}
                            return $q.reject(response);
                        },
                        response: function (response) {
                            if (response.data.csrf_token) {
                                //$cookies.put('80d12dac0e22db804aa89d9889a95c16014e5178', response.data.csrf_token);
                            }
                            return response;
                        }
                    }
                });
            }]);
function clientAutoCompleteDir($filter, $rootScope) {
    return {
        restrict: 'A',
        link: function (scope, elem, attrs) {
            elem.autocomplete({
                source: function (request, response) {
                    //term has the data typed by the user
                    var params = request.term;
                    //simulates api call with odata $filter
                    var data = '';
                    if (attrs.dirtype == 1) {
                        var data = scope.other.qualification;
                        if (data) {
                            var result = $filter('filter')(data, {qualification_name: params});
                            angular.forEach(result, function (item) {
                                item['value'] = item['qualification_name'];
                            });
                        }
                    } else if (attrs.dirtype == 2) {
                        var data = scope.other.colleges;
                        if (data) {
                            var result = $filter('filter')(data, {college_name: params});
                            angular.forEach(result, function (item) {
                                item['value'] = item['college_name'];
                            });
                        }
                    } else if (attrs.dirtype == 3) {
                        var data = scope.other.languages;
                        if (data) {
                            var result = $filter('filter')(data, {language_name: params});
                            angular.forEach(result, function (item) {
                                item['value'] = item['language_name'];
                            });
                        }
                    } else if (attrs.dirtype == 4) {
                        var data = scope.search_result;
                        if (data) {
                            var result = $filter('filter')(data, {user_search: params});
                            angular.forEach(result, function (item) {
                                var user_phone_number = "";
                                if(item['user_phone_number'] != undefined && item['user_phone_number'] != ''){
                                    user_phone_number = " (" + item['user_phone_number'] + ")";
                                }
                                item['value'] = item['user_name'] + user_phone_number;
                                if(item['parent_user_name'] != undefined && item['parent_user_name'] != '') {
                                    item['value'] += " (" + item['parent_user_phone_number'] + " > " + item['parent_user_name'] + " - " + item['relation'] + ")";
                                }
                                item['key'] = item['user_id'];
                            });
                        }
                    } else if (attrs.dirtype == 5) {
                        var data = scope.other.specializations;
                        if (data) {
                            var result = $filter('filter')(data, {specialization_title: params});
                            angular.forEach(result, function (item) {
                                item['value'] = item['specialization_title'];
                            });
                        }
                    } else if (attrs.dirtype == 6) {
                        var data = scope.search_patient_result;
                        if (data) {
                            var result = $filter('filter')(data, {user_search: params});
                            angular.forEach(result, function (item) {
                                var user_phone_number = "";
                                if(item['user_phone_number'] != undefined && item['user_phone_number'] != ''){
                                    user_phone_number = " (" + item['user_phone_number'] + ")";
                                }
                                item['value'] = item['user_name'] + user_phone_number;
                                if(item['parent_user_name'] != undefined && item['parent_user_name'] != '') {
                                    item['value'] += " (" + item['parent_user_phone_number'] + " > " + item['parent_user_name'] + " - " + item['relation'] + ")";
                                }
                                item['key'] = item['user_id'];
                            });
                        }
                    } else if (attrs.dirtype == 7) {
                        var data = scope.search_similar_brand_result;
                        if (data) {
                            var result = $filter('filter')(data, {drug_name: params});
                            angular.forEach(result, function (item) {
                                item['value'] = item['drug_name'];
                                item['key'] = item['drug_id'];
                            });
                        }

                    } else if (attrs.dirtype == 8 || attrs.dirtype == 9 || attrs.dirtype == 10 || attrs.dirtype == 11) {
                        var data = scope.search_notes;
                        if (data) {
                            var result = $filter('filter')(data, {clinical_notes_catalog_title: params});
                            angular.forEach(result, function (item) {
                                item['value'] = item['clinical_notes_catalog_title'];
                                item['key'] = item['clinical_notes_catalog_id'];
                            });
                        }
                    } else if (attrs.dirtype == 12) {
                        var data = scope.search_labs;
                        if (data) {
                            var result = $filter('filter')(data, {health_analytics_test_name: params});
                            angular.forEach(result, function (item) {
                                item['value'] = item['health_analytics_test_name'];
                                item['key'] = item['health_analytics_test_id'];
                            });
                        }
                    } else if (attrs.dirtype == 13) {
                        var data = scope.search_kco;
                        if (data) {
                            var result = $filter('filter')(data, {disease_name: params});
                            angular.forEach(result, function (item) {
                                item['value'] = item['disease_name'];
                                item['key'] = item['disease_id'];
                            });
                        }
                    } else if (attrs.dirtype == 14) {
                        var data = scope.search_doctors;
                        if (data) {
                            var result = $filter('filter')(data, {user_name: params});
                            angular.forEach(result, function (item) {
                                item['value'] = item['user_name'];
                                item['key'] = item['user_id'];
                            });
                        }
                    } else if (attrs.dirtype == 15) {
                        var data = scope.search_proclist;
                        if (data) {
                            var result = $filter('filter')(data, {procedure_title: params});
                            angular.forEach(result, function (item) {
                                item['value'] = item['procedure_title'];
                                item['key'] = item['procedure_id'];
                            });
                        }
                    } else if (attrs.dirtype == 16) {
                        var data = scope.search_treatment;
                        if (data) {
                            var result = $filter('filter')(data, {pricing_catalog_name: params});
                            angular.forEach(result, function (item) {
                                item['value'] = item['pricing_catalog_name'];
                                item['key'] = item['pricing_catalog_id'];
                            });
                        }
                    } else if (attrs.dirtype == 17) {
                        var data = scope.search_labs;
                        if (data) {
                            var result = $filter('filter')(data, {health_analytics_test_name: params});
                            angular.forEach(result, function (item) {
                                item['value'] = item['health_analytics_test_name'];
                                item['key'] = item['health_analytics_test_id'];
                            });
                        }
                    } else if (attrs.dirtype == 18) {
                        var data = scope.search_similar_generic_result;
                        if (data) {
                            var result = $filter('filter')(data, {generic_title: params});
                            angular.forEach(result, function (item) {
                                item['value'] = item['generic_title'];
                                item['key'] = item['drug_drug_generic_id'];
                            });
                        }

                    } else if (attrs.dirtype == 19) {
                        var data = scope.search_instruction_result;
                        if (data) {
                            var result = $filter('filter')(data, {diet_instruction: params});
                            angular.forEach(result, function (item) {
                                item['value'] = item['diet_instruction'];
                                item['key'] = item['id'];
                            });
                        }

                    } else if (attrs.dirtype == 20) {
                        var data = scope.investigation_instructions_data;
                        if (data) {
                            var result = $filter('filter')(data, {instruction: params});
                            angular.forEach(result, function (item) {
                                item['value'] = item['instruction'];
                                item['key'] = item['instruction'];
                            });
                        }

                    } else if (attrs.dirtype == 21) {
                        var data = scope.search_caretaker_result;
                        if (data) {
                            var result = $filter('filter')(data, {user_phone_number: params});
                            angular.forEach(result, function (item) {
                                // item['value'] = item['user_name'] + " (" + item['user_phone_number'] + ")";
                                item['value'] = item['user_phone_number'];
                                item['key'] = item['user_id'];
                            });
                        }
                    } else if (attrs.dirtype == 22) {
                        var data = scope.rx_instruction_result;
                        if (data) {
                            var result = $filter('filter')(data, {diet_instruction: params});
                            angular.forEach(result, function (item) {
                                item['value'] = item['diet_instruction'];
                                item['key'] = item['id'];
                            });
                    }

                    }
                    response(result);
                    //event.preventDefault();
                },
                //appendTo: '#autocomplete_div',
                minLength: $rootScope.autoCompleteMinLength,
                create: function( event, ui ) {
                },
                select: function (event, ui) {
                    //force a digest cycle to update the views
                    scope.$apply(function () {
                        scope.setClientData(ui.item, attrs.dirtype, attrs.key, elem);
                    });
                },
                close: function (event, ui) {
                    $rootScope.stopScrollAutoHideHide = false;
                },
                /* focus: function(event, ui) {
                    
                }, */
                search: function( event, ui ) {
					var allowedUiAttrList = ['7','8','9','10','11','12','13','14','15','16','18','20','21','22'];
                    if(attrs.dirtype != undefined && allowedUiAttrList.indexOf(attrs.dirtype) !== -1) {
                        $rootScope.stopScrollAutoHideHide = true;
                    }
                }
            }).focus(function(){
				var allowedUiAttrList = ['7','8','9','10','11','12','13','14','15','16','18','20','21','22'];
                if(attrs.dirtype != undefined && allowedUiAttrList.indexOf(attrs.dirtype) !== -1) {
                    $rootScope.stopScrollAutoHideHide = true;
                    $(this).data("uiAutocomplete").search($(this).val());
                }
            });
        }
    };
};

/* file upload directive code */
(function (module) {
    var fileReader = function ($q, $log) {
        var onLoad = function (reader, deferred, scope) {
            return function () {
                scope.$apply(function () {
                    deferred.resolve(reader.result);
                });
            };
        };
        var onError = function (reader, deferred, scope) {
            return function () {
                scope.$apply(function () {
                    deferred.reject(reader.result);
                });
            };
        };
        var onProgress = function (reader, scope) {
            return function (event) {
                scope.$broadcast("fileProgress",
                        {
                            total: event.total,
                            loaded: event.loaded
                        });
            };
        };
        var getReader = function (deferred, scope) {
            var reader = new FileReader();
            reader.onload = onLoad(reader, deferred, scope);
            reader.onerror = onError(reader, deferred, scope);
            reader.onprogress = onProgress(reader, scope);
            return reader;
        };
        var readAsDataURL = function (file, scope, is_pdf) {
            var deferred = $q.defer();
            var reader = getReader(deferred, scope);
            if (is_pdf == 1) {
                reader.readAsBinaryString(file);
            } else {
                reader.readAsDataURL(file);
            }
            return deferred.promise;
        };
        return {
            readAsDataUrl: readAsDataURL
        };
    };
    module.factory("fileReader",["$q", "$log", fileReader]);
}(angular.module("medeasy")));

/* Sidebar hover effect directive */
angular.module("medeasy")
        .directive('customHover', function () {
            return {
                link: function (scope, element, attr) {
                    element.hover(
                            function () {
                                //$(".common_padding_all").css('padding-left', '191px');
                                //$(".clinic_listing").css('width', '223px');
                            },
                            function () {
                                //$(".common_padding_all").css('padding-left', '67px');
                                //$(".clinic_listing").css('width', '243px');
                            }
                    );
                }
            }
        });
angular.module("medeasy")
        .config(['$compileProvider', function ($compileProvider) {
                $compileProvider.debugInfoEnabled(false);
            }]);

/* custom filter for converting 24 hours */
angular.module("medeasy")
        .filter('timeTo24', function () {
            return function (time) {
                time = time.slice(0, -3);
                time = time.toString().match(/^([01]\d|2[0-3])(:)([0-5]\d)(:[0-5]\d)?$/) || [time];
                if (time.length > 1) { // If time format correct
                    time = time.slice(1);  // Remove full string match value
                    time[5] = +time[0] < 12 ? 'AM' : 'PM'; // Set AM/PM
                    time[0] = +time[0] % 12 || 12; // Adjust hours
                }
                return time.join('');
            };
        });
/*nl2br*/
angular.module("medeasy").filter('nl2br', function($sce) {
  return function(msg,is_xhtml) { 
      var is_xhtml = is_xhtml || true;
      var breakTag = (is_xhtml) ? '<br />' : '<br>';
      var msg = (msg + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag +'$2');
      return $sce.trustAsHtml(msg);
    }
});
/* custom filter for converting minutes into hh:mm format*/
angular.module("medeasy")
        .filter('minutesToHour', function () {
            return function (minutes) {
                var realmin = minutes % 60
                var hours = Math.floor(minutes / 60)
                return hours + ':' + realmin + ':00';
            };
        });
/* custom filter for converting minutes into hh:mm format*/
angular.module("medeasy")
        .filter('timeToMinutes', function () {
            return function (time) {
                var a = time.split(':'); // split it at the colons
                var minutes = (+a[0]) * 60 + (+a[1]);
                return minutes;
            };
        });
/* custom filter for BMI calculation */
angular.module("medeasy")
        .filter('bmi', function () {
            return function (height, weight) {
                //var BMI = weight / ((height / 100) * (height / 100));
                if(weight != undefined && weight != 0 && height != '' && height != undefined && height != 0 && height != '') {
                    var BMI = (weight * 703) / ((height * 0.393701) * (height * 0.393701));
                    if (isNaN(BMI)) {
                        return '';
                    }
                    return BMI.toFixed(2);
                } else {
                    return '';
                }
            }
        });
/* custom filter for KG to POUND calculation */
angular.module("medeasy")
        .filter('kgToPound', function () {
            return function (value) {
                var lbs = 2.20462 * value;
                if (isNaN(lbs)) {
                    return '';
                }
                return lbs;
            }
        });
/* custom filter for POUND to KG calculation */
angular.module("medeasy")
        .filter('PoundToKG', function () {
            return function (value) {
                var kg = value / 2.20462;
                if (isNaN(kg) || kg == 0) {
                    return '';
                }
                return kg.toFixed(2);
            }
        });
angular.module("medeasy")
        .directive('ngRightClick', function ($parse) {
            return function (scope, element, attrs) {
                element.bind('contextmenu', function (event) {
                    scope.$apply(function () {
                        event.preventDefault();
                    });
                });
            };
        });

/* custom filter for converting minutes into text*/
angular.module("medeasy")
        .filter('minutesToHourText', function () {
            return function (minutes) {
                var hours = Math.floor(minutes / 60)
                if (hours == 0) {
                    return minutes + ' MINUTES';
                }
                if (hours == 1) {
                    return hours + ' HOUR';
                }
                return hours + ' HOURS';
            };
        });
angular.module("medeasy")
        .filter('capitalize', function () {
            return function (input) {
                //return (!!input) ? input.charAt(0).toUpperCase() + input.substr(1).toLowerCase() : '';
                  return (!!input) ? input.toLowerCase().replace(/\b./g, function(a){ return a.toUpperCase(); }) : '';
            }
        });
angular.module("medeasy")
        .filter('safe', function($sce) {
            return function(val) {
                return $sce.trustAsHtml(val);
            };
        });
angular.module("medeasy").filter('trustThisUrl', ["$sce", function ($sce) {
        return function (val) {
            return $sce.trustAsResourceUrl(val);
        };
    }]);
angular.module("medeasy")
        .filter('slice', function() {
            return function(arr, start, end) {
                return arr.slice(start, end);
            };
        });
angular.module("medeasy")
        .filter('trimString', function () {
            return function (input, customLength) {
                var length = 30;
                if (customLength) {
                    length = customLength;
                }
                if (!input || input.length <= length) {
                    return input;
                }
                var trimmedString = input.substring(0, length);
                return trimmedString + '...';
            }
        });
angular.module("medeasy")
        .directive('chosen', function () {
            var linker = function (scope, element, attrs) {
                var list = attrs['chosen']; 
                scope.$watch(list, function () {
                    element.trigger('chosen:updated');
                });
                scope.$watch(attrs['ngModel'], function () {
                    element.trigger('chosen:updated');
                });
            };
            return {
                restrict: 'A',
                link: linker
            };
        });
angular.module('medeasy')
        .directive('customAutofocus', function () {
            return{
                restrict: 'A',
                link: function (scope, element, attrs) {
                    scope.$watch(function () {
                        return scope.$eval(attrs.customAutofocus);
                    }, function (newValue) {
                        if (newValue === true) {
                            element[0].focus();//use focus function instead of autofocus attribute to avoid cross browser problem. And autofocus should only be used to mark an element to be focused when page loads.
                        }
                    });
                }
            };
        })

angular.module('medeasy')
        .directive("modal", function ($window, $rootScope) {
            return{
                restrict: 'C',
                link: function (scope, element, attrs) {
                    element.bind("scroll", function (e) {
                        $(".ui-autocomplete").css("display", "none");
                        scope.$apply();
                    });
                    angular.element($window).bind("scroll", function (e) {
                        if($rootScope.stopScrollAutoHideHide != undefined && $rootScope.stopScrollAutoHideHide == false){
                            $(".ui-autocomplete").css("display", "none");
                        }
                        scope.$apply();
                    });
                }
            };
});

/* patient module hover effect */
angular.module("medeasy")
        .directive('customHoverPatient', function () {
            return {
                link: function (scope, element, attr) {
                    element.hover(
                            function () {
                                $(".patient_list_width .nav-text").css('padding-left', '20px');
                                $(".recent_list_single").hide();
                                $(".recent_list_all").show();
                            },
                            function () {
                                $(".patient_list_width .nav-text").css('padding-left', '60px');
                                $(".recent_list_single").show();
                                $(".recent_list_all").hide();
                            }
                    );
                }
            }
        });
angular.module("medeasy")
        .filter('ageFilter', function () {
            function calculateAge(birthday) { // birthday is a date                
                var now = new Date();
                var today = new Date(now.getYear(), now.getMonth(), now.getDate());
                var yearNow = now.getYear();
                var monthNow = now.getMonth();
                var dateNow = now.getDate();
                var dob = birthday;
                var yearDob = dob.getYear();
                var monthDob = dob.getMonth();
                var dateDob = dob.getDate();
                var age = {};
                var ageString = "";
                var yearString = "";
                var monthString = "";
                var dayString = "";
                var yearAge = yearNow - yearDob;
                if (monthNow >= monthDob)
                    var monthAge = monthNow - monthDob;
                else {
                    yearAge--;
                    var monthAge = 12 + monthNow - monthDob;
                }
                if (dateNow >= dateDob)
                    var dateAge = dateNow - dateDob;
                else {
                    monthAge--;
                    var dateAge = 31 + dateNow - dateDob;
                    if (monthAge < 0) {
                        monthAge = 11;
                        yearAge--;
                    }
                }

                var age = {
                    years: yearAge,
                    months: monthAge,
                    days: dateAge
                };
                var yearString = '';
                var dayString = '';
                if (age.years > 1)
                    yearString = "yr";
                else
                    yearString = "yr";
                if (age.months > 1)
                    monthString = "m";
                else
                    monthString = "m";
                if (age.days > 1)
                    dayString = "d";
                else
                    dayString = "d";
                //dayString = '';
                var ageString = '';

                if ((age.years > 0) && (age.months > 0) && (age.days > 0))
                    ageString = age.years + yearString + "-" + age.months + monthString;
                else if ((age.years == 0) && (age.months == 0) && (age.days > 0))
                    ageString = age.days + dayString;
                else if ((age.years > 0) && (age.months == 0) && (age.days == 0))
                    ageString = age.years + yearString;
                else if ((age.years > 0) && (age.months > 0) && (age.days == 0))
                    ageString = age.years + yearString;
                else if ((age.years == 0) && (age.months > 0) && (age.days > 0))
                    ageString = age.months + monthString;
                else if ((age.years > 0) && (age.months == 0) && (age.days > 0))
                    ageString = age.years + yearString;
                else if ((age.years == 0) && (age.months > 0) && (age.days == 0))
                    ageString = age.months + monthString;
                else
                    ageString = "";

                return ageString;
            }

            return function (birthdate) {
                if (birthdate) {
                    var birth_arr = birthdate.split('-');
                    birthdate = new Date(birth_arr[0], birth_arr[1] - 1, birth_arr[2]);
                    var age = calculateAge(birthdate);
                    return age;
                }
                return birthdate;
            };
        });
angular.module("medeasy")
        .filter('FCConvert', function () {
            return function (input, unitType) {
                var temperature = input;
                if (unitType == 1) {
                    //celsius code
                    var result = ((temperature - 32) / 1.8).toFixed(2);
                    if (!isNaN(result)) {
                        return result;
                    }
                    return '';

                } else {
                    //fahrenheit code                 
                    var result = ((temperature * 1.8) + 32).toFixed(2);
                    if (!isNaN(result)) {
                        return result;
                    }
                    return '';
                }
            }
        });
angular.module("medeasy")
        .directive('isNumber', function () {
            return {
                require: 'ngModel',
                link: function (scope, element) {

                    scope.$watch(function () {
                        return scope.paymentObjList[element.attr("key")].basic_cost;
                    }, function (newValue, oldValue) {
                        var arr = String(newValue).split("");
                        if (arr.length === 0)
                            return;
                        if (arr.length === 1 && (arr[0] == '-' || arr[0] === '.'))
                            return;
                        if (arr.length === 2 && newValue === '-.')
                            return;
                        if (isNaN(newValue) && newValue != undefined) {
                            scope.paymentObjList[element.attr("key")].basic_cost = oldValue;
                        } else {
                            return;
                        }
                    });

                }
            };
        });
angular.module("medeasy")
        .directive('isNumberPercentage', function () {
            return {
                require: 'ngModel',
                link: function (scope, element) {

                    scope.$watch(function () {
                        return scope.paymentObjList[element.attr("key")].discount;
                    }, function (newValue, oldValue) {

                        var arr = String(newValue).split("");
                        if (arr.length === 0)
                            return;
                        if (arr.length === 1 && (arr[0] == '-' || arr[0] === '.'))
                            return;
                        if (arr.length === 2 && newValue === '-.')
                            return;
                        if (isNaN(newValue) && newValue != undefined) {
                            scope.paymentObjList[element.attr("key")].discount = oldValue;
                        } else {
                            return;
                        }
                    });

                }
            };
        });

angular.module("medeasy")
.directive('numberMask', function() {
    return {
        restrict: 'A',
        link: function(scope, element, attrs) {
            $(element).numeric();
        }
    }
});

angular.module("medeasy")
        .directive('movable', function ($document) {
            return {
                restrict: 'A',
                link: function postLink(scope, element, attrs) {
                    var startX = 0,
                            startY = 0,
                            x = 0,
                            y = 0;
                    element.css({
                        position: 'relative'
                    });
                    function bindElementMove() {
                        element.bind('mousedown', function (event) {
                            // Prevent default dragging of selected content
                            event.preventDefault();
                            startX = event.screenX - x;
                            startY = event.screenY - y;
                            $document.bind('mousemove', moveDiv);
                            $document.bind('mouseup', mouseup);
                        });
                    }
                    bindElementMove();
                    function moveDiv(event) {
                        event.preventDefault();
                        y = event.screenY - startY;
                        x = event.screenX - startX;
                        element.css({
                            top: y + 'px',
                            left: x + 'px'
                        });
                    }
                    function mouseup() {
                        $document.unbind('mousemove', moveDiv);
                        $document.unbind('mouseup', mouseup);
                    }
                }
            }
        });

angular.module("medeasy").directive('pauseOnClose', function () {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            $('#video_help_modal').on('hide.bs.modal', function (e) {
                var nodesArray = [].slice.call(document.querySelectorAll("video"));
                angular.forEach(nodesArray, function (obj) {
                    // Apply pause to the object
                    obj.pause();
                });
            })
        }
    }
});
/* custom filter for KG to POUND calculation */
angular.module("medeasy")
        .filter('timeFormat', function () {
            return function (value, hour_format) {
                if (value == undefined || value == '') { return ''; }
                if(hour_format == '2') { return value; }
                var array = value.split(":");
                var hours = parseInt(array[0]);
                var minutes = parseInt(array[1]);
                var ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12;
                hours = hours ? hours : 12; // the hour '0' should be '12'
                minutes = minutes < 10 ? '0' + minutes : minutes;
                hours = hours < 10 ? '0' + hours : hours;
                var strTime = hours + ':' + minutes + ' ' + ampm;
                return strTime;
            }
        });