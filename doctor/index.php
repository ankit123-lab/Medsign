<?php 
    //Beta version
    $appVer          = 'V3.74';         //Used for the static plugins JS
    $appResourceVer  = 'V3.74';         //Used for the JS developed ng resources 
    $appCssVer       = 'V3.74';         //Used for the JS developed ng resources 
    $GOOGLEMAPAPIKEY = 'AIzaSyCPpY80x531ffD-g4QHrtRm75lAOVO_RBk'; //DEVELOPMENT KEY
    //$GOOGLEMAPAPIKEY = 'AIzaSyADXbXVfpHCD3gsyu2sGrUWeMW4sH6zUjc'; //PRODUCTION KEY
    $GLOBALS['ENV_VARS'] = parse_ini_file('../env.ini',true,INI_SCANNER_TYPED);
?>
<!DOCTYPE html>
<html ng-app="medeasy">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
        <meta name="description" content="MedSign" />
        <meta name="keywords" content="MedSign" />
        <script src="app/js/jquery.js?<?php echo $appVer; ?>"></script>
        <script src="app/plugins/opentok/opentok.min.js?<?php echo $appVer; ?>"></script>
        <script src="app/plugins/opentok/kit.fontawesome.b155a71cdd.js?<?php echo $appVer; ?>" crossorigin="anonymous"></script>
        <script src="app/js/angular.js?<?php echo $appVer; ?>"></script>
        <script src="app/plugins/route.js?<?php echo $appVer; ?>"></script>
        <script src="app/js/angular-route.min.js?<?php echo $appVer; ?>"></script>
        <script src="app/js/angular-sanitize.min.js?<?php echo $appVer; ?>"></script>
        <noscript>You must have JavaScript enabled to use this app.</noscript>
        <title>MedSign</title>
        <link rel="icon" href="app/images/ic_launcher.png?<?php echo $appVer; ?>" type="image/png" sizes="16x16" /> 
        <link rel="manifest" href="manifest.json?<?php echo $appVer; ?>" />
        <?php /* <script src="app/js/pushwoosh.js?<?php echo $appVer; ?>" async></script> */ ?>
        <!-- <script type="text/javascript" src="//cdn.pushwoosh.com/webpush/v3/pushwoosh-web-notifications.js" async></script> -->
        <script src="https://www.gstatic.com/firebasejs/3.7.2/firebase.js"></script>
        <script>
        var appVer = '<?php echo $appVer; ?>'; var appResourceVer = '<?php echo $appResourceVer; ?>';var appCssVer = '<?php echo $appCssVer; ?>';
            var $body = '';
            var $rootScope = '';
            var $base_url = "<?= $GLOBALS['ENV_VARS']['DOMAIN_URL'] ?>";
            var firebase_api_access_key = "<?= $GLOBALS['ENV_VARS']['FIREBASE_API_ACCESS_KEY'] ?>";
            var firebase_sender_id = "<?= $GLOBALS['ENV_VARS']['FIREBASE_SENDER_ID'] ?>";
            function jsNotification(params) {
                $body = angular.element(document.body);
                $rootScope = $body.injector().get('$rootScope');
                $rootScope.storeDeviceTokens(params);
            }
            // Initialize Firebase
            var config = {
                apiKey: firebase_api_access_key,
                messagingSenderId: firebase_sender_id
            };
            firebase.initializeApp(config);
            const messaging = firebase.messaging();
            messaging.requestPermission()
            .then(function() {
                // console.log('Notification permission granted.');
                return messaging.getToken();
            })
            .then(function(token) {
                // console.log(token); // Display user token
                var params = {};
                params.pushToken = token;
                // Store device token 
                jsNotification(params);
            })
            .catch(function(err) { // Happen if user deney permission
                // console.log('Unable to get permission to notify.', err);
                // save_device_token('');
            });
            messaging.onMessage(function(payload) {
                // console.log('onMessage',payload);
                var options = {
                    body: payload.notification.body,
                    icon: payload.notification.icon
                };
                var n = new Notification(payload.notification.title, options);
                setTimeout(n.close.bind(n), 5000);
            });
            /*var Pushwoosh = Pushwoosh || [];
            Pushwoosh.push(["init", {
                    logLevel: 'info', // possible values: error, info, debug
                    applicationCode: 'EC0D3-F8E8B',
                    safariWebsitePushID: 'MedSign',
                    defaultNotificationTitle: 'MedSign',
                    serviceWorkerUrl: "pushwoosh-service-worker.js",
                    defaultNotificationImage: 'https://www.medsign.in/doctor/app/images/logo.png',
                    autoSubscribe: true
                }]);
            Pushwoosh.push(['onSubscribe', function (api, payload) {
                    Pushwoosh.getParams().then(function (params) {
                        params = params || {};
                        var hwid = params.hwid;
                        var pushToken = params.pushToken;
                        var userId = params.userId;
                        jsNotification(params);
                    });
                }]);
            Pushwoosh.push(['onPushDelivery', function (api, payload) {
                    if (payload.customData.email_verified == 1) {
                        $body = angular.element(document.body);
                        $rootScope = $body.injector().get('$rootScope');
                        console.log('pushwoosh publish delivery');
                        //$rootScope.updateEmailVerification(payload.customData.email_flag);
                    }
            }]);
            Pushwoosh.push(['onNotificationClick', function (api, payload) {
                    console.log('onNotificationClick');
                }]);
            Pushwoosh.push(['onNotificationClose', function (api, payload) {
                    console.log('onNotificationClose');
                }]);*/
        </script>
        <script src="app/js/vendor/ng-load-master/angular-load.min.js?<?php echo $appCssVer; ?>"></script>
        <link rel="stylesheet" href="app/css/bootstrap.min.css?<?php echo $appCssVer; ?>">
        <link rel="stylesheet" href="app/css/bootstrap-theme.min.css?<?php echo $appCssVer; ?>">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet">
        <link rel="stylesheet" href="app/css/main.css?<?php echo $appCssVer; ?>">
        <link rel="stylesheet" href="app/css/font-awesome.min.css?<?php echo $appCssVer; ?>">
        <link rel="stylesheet" href="app/css/patient.css?<?php echo $appCssVer; ?>">
        <script src="app/js/vendor/modernizr-2.8.3-respond-1.4.2.min.js?<?php echo $appVer; ?>"></script>
        <script src="app/js/vendor/bootstrap.min.js?<?php echo $appVer; ?>"></script>
        <script src="app/plugins/sha1/sha1.min.js?<?php echo $appVer; ?>"></script>
        <script src="app/js/angular-translate.js?<?php echo $appVer; ?>"></script>
        <script src="app/plugins/toaster/angular-sanitize.js?<?php echo $appVer; ?>"></script>          
        <script src="app/plugins/toaster/ngToast.min.js?<?php echo $appVer; ?>"></script>
        <link data-require="animate.css@*" data-semver="3.2.0" rel="stylesheet" href="app/css/animate.min.css?<?php echo $appCssVer; ?>" />
        <link rel="stylesheet" href="app/plugins/toaster/ngToast.min.css?<?php echo $appCssVer; ?>">
        <link rel="stylesheet" href="app/plugins/toaster/ngToast-animations.min.css?<?php echo $appCssVer; ?>">
        <link rel="stylesheet" href="app/plugins/fullcalendar/fullcalendar.min.css?<?php echo $appCssVer; ?>">
        <link rel="stylesheet" href="app/css/fullcalendar_custom.css?<?php echo $appCssVer; ?>">
        <link rel="stylesheet" href="app/plugins/fullcalendar/scheduler.css?<?php echo $appCssVer; ?>">
        <script type="text/javascript" src='app/plugins/ngstorage.js?<?php echo $appVer; ?>'></script>
        <script src="app/js/chosen.jquery.min.js?<?php echo $appVer; ?>"></script>
        <script src="app/plugins/angular-chosen-1.4.0/dist/angular-chosen.min.js?<?php echo $appVer; ?>"></script>
        <script src="app/plugins/angular-chosen-1.4.0/dist/chosen_add.js?<?php echo $appVer; ?>"></script>
        <script src="app/plugins/angular-ui-mask/mask.min.js?<?php echo $appVer; ?>"></script>
        <link rel="stylesheet" type="text/css" href="app/css/chosen.css?<?php echo $appCssVer; ?>" />
        <link rel="stylesheet" type="text/css" href="app/plugins/angular-chosen-1.4.0/chosen-spinner.css?<?php echo $appCssVer; ?>" />
        <script src="app/js/jquery-ui.min.js?<?php echo $appVer; ?>"></script>
        <link rel="stylesheet" href="app/css/jquery-ui.css?<?php echo $appCssVer; ?>" />
        <script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=<?php echo $GOOGLEMAPAPIKEY; ?>"></script>
        <script src="app/plugins/angularjs-google-maps/dist/angularjs-google-maps.js?<?php echo $appVer; ?>"></script>
        <script src="app/js/ui-bootstrap-tpls.js?<?php echo $appVer; ?>"></script>
        <script src="app/plugins/bootstrap-ui-datetime-picker-master/dist/datetime-picker.js?<?php echo $appVer; ?>"></script>
        <link rel="stylesheet" href="app/css/custom.css?<?php echo $appCssVer; ?>" />
        <link rel="stylesheet" href="app/plugins/slimscroll/ng-slim-scroll.css?<?php echo $appCssVer; ?>" />
        <script src="app/plugins/angular-recaptcha/release/angular-recaptcha.js?<?php echo $appVer; ?>"></script>
        <script src="app/plugins/crypto-js-develop/angularjs-crypto.js?<?php echo $appVer; ?>"></script>
        <script src="app/plugins/crypto-js-develop/CryptoJSCipher.js?<?php echo $appVer; ?>"></script>
        <script src="app/plugins/crypto-js-develop/aes.js?<?php echo $appVer; ?>"></script>
        <script src="app/plugins/crypto-js-develop/pad-zeropadding.js?<?php echo $appVer; ?>"></script>
        <script src="app/plugins/crypto-js-develop/pad-nopadding.js?<?php echo $appVer; ?>"></script>
        <script src="app/js/moment.js?<?php echo $appVer; ?>"></script>
        <script src="app/js/fullcalendar.min.js?<?php echo $appVer; ?>"></script>
        <script src="app/plugins/fullcalendar/gcal.js?<?php echo $appVer; ?>"></script>
        <script src="app/plugins/fullcalendar/calendar.js?<?php echo $appVer; ?>"></script>
        <script src="app/plugins/fullcalendar/scheduler.js?<?php echo $appVer; ?>"></script>
        <link type="text/css" href="app/plugins/sweetAlert/sweetalert.css?<?php echo $appCssVer; ?>" rel="stylesheet" />
        <script src="app/plugins/sweetAlert/sweetalert.min.js?<?php echo $appVer; ?>"></script>
        <script src="app/plugins/angular-cookies/angular-cookies.js?<?php echo $appVer; ?>"></script>
        <script src="app/plugins/slimscroll/ng-slim-scroll.min.js?<?php echo $appVer; ?>"></script>
        <script src="app/plugins/jquery-fullscreen/jquery.fullscreen.min.js?<?php echo $appVer; ?>"></script>
        <script src="app/js/main.js?<?php echo $appVer; ?>"></script>
        <link rel="stylesheet" type="text/css" href="app/plugins/imageCompare/images-compare.min.css?<?php echo $appCssVer; ?>" />
        <script src="app/plugins/imageCompare/hammer_imagecompare.min.js?<?php echo $appVer; ?>"></script>
        <script src="app/plugins/chart/chart.min.js?<?php echo $appCssVer; ?>"></script>
        <script src="app/plugins/chart/angular-chart.min.js?<?php echo $appCssVer; ?>"></script>
        <script src="app/plugins/chart/chartjs-plugin-annotation.min.js?<?php echo $appCssVer; ?>"></script>
        <script src="app/plugins/razorpay/razorpay_checkout.js?<?php echo $appCssVer; ?>"></script>
        <script src="app/plugins/angular-ckeditor/ckeditor.js?<?php echo $appCssVer; ?>"></script>
        <script src="app/plugins/angular-ckeditor/ng-ckeditor.min.js?<?php echo $appCssVer; ?>"></script>
    </head>
    <body scroll id="main_body">
        <div class="overlay-white hide resolution-overlay">
            <div class="box-design-white title1">MedSign web application can be best viewed in Laptop, Desktop & Tablet (landscape view).</div>
        </div>
        <div id="overlay" ng-if="app.isLoader"></div>
        <div data-ui-view="" data-autoscroll="false" class="wrapper"></div>
        <?php //<script>document.write("<script src='app/js/app.js?time=" + $.now() + "'><\/script>");</script> ?>
        <!-- google analytics JS -->
        <script src="../assets/web/js/google_analytics.js?<?php echo $appResourceVer; ?>"></script>
        <script src="app/js/app.js?<?php echo $appResourceVer; ?>" type="text/javascript"></script>
        <script src="app/js/language.js?<?php echo $appResourceVer; ?>" type="text/javascript"></script>
        <script src="app/js/controllers/LoginController.js?<?php echo $appResourceVer; ?>" type="text/javascript"></script>
        <script src="app/js/controllers/DashboardController.js?<?php echo $appResourceVer; ?>" type="text/javascript"></script>
        <script src="app/js/controllers/DashboardController.js?<?php echo $appResourceVer; ?>" type="text/javascript"></script>
        <script src="app/js/controllers/PatientController.js?<?php echo $appResourceVer; ?>" type="text/javascript"></script>
        <script src="app/js/controllers/UserController.js?<?php echo $appResourceVer; ?>" type="text/javascript"></script>
        <script src="app/js/controllers/CalenderController.js?<?php echo $appResourceVer; ?>" type="text/javascript"></script>
        <script src="app/js/controllers/SettingController.js?<?php echo $appResourceVer; ?>" type="text/javascript"></script>
        <script src="app/js/controllers/SurveyController.js?<?php echo $appResourceVer; ?>" type="text/javascript"></script>
        <script src="app/js/controllers/ImportController.js?<?php echo $appResourceVer; ?>" type="text/javascript"></script>
        <script src="app/js/controllers/SubscriptionController.js?<?php echo $appResourceVer; ?>" type="text/javascript"></script>
        <script src="app/js/controllers/ReportController.js?<?php echo $appResourceVer; ?>" type="text/javascript"></script>
        <script src="app/js/controllers/CommunicationsController.js?<?php echo $appResourceVer; ?>" type="text/javascript"></script>
        <script src="app/js/controllers/TeleconsultationController.js?<?php echo $appResourceVer; ?>" type="text/javascript"></script>
        <!--- service js--->
        <script src="app/js/services/AuthService.js?<?php echo $appResourceVer; ?>" type="text/javascript" id="auth"></script>
        <script src="app/js/services/LoginService.js?<?php echo $appResourceVer; ?>" type="text/javascript"></script>        
        <script src="app/js/services/CommonService.js?<?php echo $appResourceVer; ?>" type="text/javascript"></script>      
        <script src="app/js/services/ClinicService.js?<?php echo $appResourceVer; ?>" type="text/javascript"></script>      
        <script src="app/js/services/UserService.js?<?php echo $appResourceVer; ?>" type="text/javascript"></script>      
        <script src="app/js/services/PatientService.js?<?php echo $appResourceVer; ?>" type="text/javascript"></script>      
        <script src="app/js/services/CalenderService.js?<?php echo $appResourceVer; ?>" type="text/javascript"></script>      
        <script src="app/js/services/EncryptDecrypt.js?<?php echo $appResourceVer; ?>" type="text/javascript"></script>              
        <script src="app/js/services/SettingService.js?<?php echo $appResourceVer; ?>" type="text/javascript"></script>
        <script src="app/js/services/SurveyService.js?<?php echo $appResourceVer; ?>" type="text/javascript"></script>
        <script src="app/js/services/ImportService.js?<?php echo $appResourceVer; ?>" type="text/javascript"></script>
        <script src="app/js/directive/ngImagesCompare.js?<?php echo $appResourceVer; ?>" type="text/javascript"></script>
        <script src="app/js/directive/ngAnatomicalDiagrams.js?<?php echo $appResourceVer; ?>" type="text/javascript"></script>
        <script src="app/js/services/SubscriptionService.js?<?php echo $appResourceVer; ?>" type="text/javascript"></script>
        <script src="app/js/services/ReportService.js?<?php echo $appResourceVer; ?>" type="text/javascript"></script>
        <script src="app/js/services/CommunicationService.js?<?php echo $appResourceVer; ?>" type="text/javascript"></script>
        <script src="app/js/services/TeleconsultationService.js?<?php echo $appResourceVer; ?>" type="text/javascript"></script>
        <script>
            document.onkeydown = function (e) {
                if (e.keyCode == 123) {
                    return false;
                } else if (e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) {
                    return false;
                } else if (e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) {
                    return false;
                } else if (e.keyCode == 123) {
                    return false;
                } else if (e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) {
                    return false;
                }
            }
            window.addEventListener("DOMContentLoaded", function () {
                var body = document.getElementById("main_body");
                body.addEventListener("paste", function (evt) {
                    evt.preventDefault();
                    evt.stopPropagation();
                });
            });
            function doOnOrientationChange() {
                var body = document.body;
                if(body.scrollWidth < 1024) {
                    $('.wrapper').addClass('hide');
                    $('.resolution-overlay').removeClass('hide');
                } else {
                    $('.wrapper').removeClass('hide');
                    $('.resolution-overlay').addClass('hide');
                }
            }
            window.addEventListener('orientationchange', doOnOrientationChange);
            doOnOrientationChange();
        </script>
    </body>
</html>