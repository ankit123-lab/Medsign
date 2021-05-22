angular.module("app.dashboard.teleconsultation")
    .controller("TeleconsultationController", function ($scope, AuthService, $timeout, $state, CommonService, TeleconsultationService, $rootScope, $localStorage, ngToast, $location, uiCalendarConfig, $filter, minutesToHourFilter, SweetAlert, fileReader, $interval, $uibModal) {
	$scope.date_current_page = 1;
    $scope.date_total_records = 0;
    $scope.teleconsultation = {
        selected_credits: 1
    }
    $scope.creditsPlan = [
        {value:1, label: 1},
        {value:2, label: 2},
        {value:3, label: 3},
        {value:4, label: 4},
        {value:5, label: 5},
        {value:6, label: 6},
        {value:7, label: 7},
        {value:8, label: 8},
        {value:9, label: 9},
        {value:10, label: 10}
    ]
    $scope.changeCurrentClinic = function (clinic) {
        $rootScope.current_clinic = clinic;
        $scope.date_current_page = 1;
        $scope.teleconsultation_list = [];
        $scope.getTeleconsultationDate();
    };
    $scope.getTeleconsultationDate = function () {
        var request = {
            doctor_id: ($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id,
            clinic_id: $rootScope.current_clinic.clinic_id,
            user_id: $rootScope.currentUser.user_id,
            access_token: $rootScope.currentUser.access_token,
            page: $scope.date_current_page,
            per_page: 10,
        }
        TeleconsultationService.getTeleconsultationDate(request, function (response) {
            if (response.status == true) {
                $scope.teleconsultation_date = response.data;
                $scope.date_total_records = response.count;
                $scope.teleconsultation.date = $scope.teleconsultation_date[0].created_at;
                $scope.teleconsultation.current_date_key = 0;
                $scope.getTeleconsultationList(1);
            } else {
                $scope.teleconsultation_date = [];
            }
        });
    }

    $scope.getTeleGlobalData = function () {
        var request = {
            doctor_id: ($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id,
            user_id: $rootScope.currentUser.user_id,
            access_token: $rootScope.currentUser.access_token
        }
        TeleconsultationService.getTeleGlobalData(request, function (response) {
            $scope.teleconsultation.available_minutes = response.data.available_minutes;
            $scope.teleconsultation.per_minute_price = response.data.per_minute_price;
            $scope.teleconsultation.gst_percent = response.data.gst_percent;
            $scope.teleconsultation.is_apply_igst = response.data.is_apply_igst;
        });
    }

    $scope.teleCurrentPage = 0;
    $scope.tele_per_page = 10;
    $scope.getTeleconsultationList = function (number) {
        $scope.teleCurrentPage = number
        var request = {
            doctor_id: ($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id,
            clinic_id: $rootScope.current_clinic.clinic_id,
            user_id: $rootScope.currentUser.user_id,
            access_token: $rootScope.currentUser.access_token,
            date: $scope.teleconsultation.date,
            page: $scope.teleCurrentPage,
            per_page: $scope.tele_per_page
        }
        TeleconsultationService.getTeleconsultationList(request, function (response) {
            if (response.status == true) {
                $scope.teleconsultation_list = response.data;
                $scope.tele_total_rows = response.count;
                $scope.tele_total_page = Math.ceil(response.count/$scope.tele_per_page);
                $scope.tele_last_rows = $scope.teleCurrentPage*$scope.tele_per_page;
                if($scope.tele_last_rows > $scope.tele_total_rows)
                    $scope.tele_last_rows = $scope.tele_total_rows;
            } else {
                $scope.teleconsultation_list = [];
            }
        });
    }

    $scope.getTeleNextPrev = function (val) {
        if(val == 'next') {
            if($scope.teleCurrentPage >= $scope.tele_total_page)
                return false;
            var number = $scope.teleCurrentPage+1;
        }
        if(val == 'prev') {
            if($scope.teleCurrentPage <= 1)
                return false;
            var number = $scope.teleCurrentPage-1;
        }
        $scope.getTeleconsultationList(number);
    }

    $scope.changeTeleconsultationTab = function (key) {
        $scope.teleconsultation.date = $scope.teleconsultation_date[key].created_at
        $scope.teleconsultation.current_date_key = key
        $scope.getTeleconsultationList(1);
    }

    $scope.getTeleconsultationDates = function (flag) {
        var temp_page = '';
        if (flag == 'next') {
            var total_pages = Math.ceil($scope.date_total_records / 10);
            if (total_pages <= $scope.date_current_page) {
                return;
            }
            temp_page = $scope.date_current_page + 1;
        } else if (flag == 'prev') {
            temp_page = $scope.date_current_page - 1;
        }
        if (flag) {
            if (temp_page <= 0) {
                return;
            } else {
                $scope.date_current_page = temp_page;
            }
        }
        $scope.getTeleconsultationDate();
    }

    $scope.minutes_buy_popup = function () {
        $scope.changeCredits();
        $("#modal_buy_more_minutes").modal("show");
    }

    $scope.changeCredits = function () {
        $scope.teleconsultation.total_buy_credits = 100*$scope.teleconsultation.selected_credits;
        $scope.teleconsultation.total_price = $scope.teleconsultation.total_buy_credits*$scope.teleconsultation.per_minute_price;
        var cgst_amount = 0.00;
        var sgst_amount = 0.00;
        var igst_amount = 0.00;
        var total_amount = 0.00;
        if($scope.teleconsultation.total_price > 0) {
            if($scope.teleconsultation.gst_percent.cgst > 0 && !$scope.teleconsultation.is_apply_igst) {
                cgst_amount = $scope.teleconsultation.total_price * $scope.teleconsultation.gst_percent.cgst / 100;
            }
            if($scope.teleconsultation.gst_percent.sgst > 0 && !$scope.teleconsultation.is_apply_igst) {
                sgst_amount = $scope.teleconsultation.total_price * $scope.teleconsultation.gst_percent.sgst / 100;
            }
            if($scope.teleconsultation.gst_percent.igst > 0 && $scope.teleconsultation.is_apply_igst) {
                igst_amount = $scope.teleconsultation.total_price * $scope.teleconsultation.gst_percent.igst / 100;
            }
            if($scope.teleconsultation.is_apply_igst) {
                total_amount = parseFloat($scope.teleconsultation.total_price) + igst_amount;
            } else {
                total_amount = parseFloat($scope.teleconsultation.total_price) + sgst_amount + cgst_amount;
            }
        }
        $scope.teleconsultation.total_price = $scope.teleconsultation.total_price.toFixed(2);
        $scope.order_summery_data = {
            cgst_amount: cgst_amount.toFixed(2),
            sgst_amount: sgst_amount.toFixed(2),
            igst_amount: igst_amount.toFixed(2),
            total_amount: total_amount.toFixed(2)
        }
    }

    $scope.placeCreditsOrder = function () {
        var request = {
            doctor_id: ($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id,
            access_token: $rootScope.currentUser.access_token,
            user_id: $rootScope.currentUser.user_id,
            sub_amount: $scope.teleconsultation.total_price,
            paid_amount: $scope.order_summery_data.total_amount,
            cgst_amount: $scope.order_summery_data.cgst_amount,
            sgst_amount: $scope.order_summery_data.sgst_amount,
            igst_amount: $scope.order_summery_data.igst_amount,
            is_apply_igst: $scope.teleconsultation.is_apply_igst,
            total_credits: $scope.teleconsultation.total_buy_credits,
            gst_percentage: $scope.teleconsultation.gst_percent
        }
        TeleconsultationService.createPaymentCreditsOrder(request, function (response) {
            if (response.status == true) {
                var options = {
                    "key": response.data.key,
                    "currency": "INR",
                    "name": "MedSign",
                    "description": "Buy " + $scope.teleconsultation.total_buy_credits + " Minutes",
                    "image": $rootScope.app.app_url + "app/images/logo_dashoboard.png",
                    "order_id": response.data.order_id,
                    "handler": function (razorpay_response) {
                        if(razorpay_response.razorpay_payment_id != undefined && razorpay_response.razorpay_payment_id != '') {
                            razorpay_response.paid_amount = response.data.paid_amount;
                            $scope.paymentCreditsCapture(razorpay_response);
                        }
                    },
                    "prefill": {
                        "name": response.data.name,
                        "email": response.data.email,
                        "contact": response.data.contact
                    },
                    "notes": {
                        "address": response.data.address
                    },
                    "theme": {
                        "color": "#30aca5"
                    }
                };
                var rzp1 = new Razorpay(options);
                rzp1.open();
            } else {
                ngToast.danger(response.message);
            }
        });
    }
    $scope.paymentCreditsCapture = function (data) {
        var request = {
            doctor_id: ($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id,
            order_id: data.razorpay_order_id,
            payment_id: data.razorpay_payment_id,
            signature: data.razorpay_signature,
            paid_amount: data.paid_amount,
            access_token: $rootScope.currentUser.access_token,
            user_id: $rootScope.currentUser.user_id,
            total_buy_credits: $scope.teleconsultation.total_buy_credits
        }
        TeleconsultationService.paymentCreditsCapture(request, function (response) {
            if (response.status == true) {
                ngToast.success({
                    content: response.message,
                    timeout: 5000
                });
                $scope.teleconsultation.available_minutes = response.total_credits;
                $("#modal_buy_more_minutes").modal("hide");
            } else {
                ngToast.danger(response.message);
            }
        });
    }

});