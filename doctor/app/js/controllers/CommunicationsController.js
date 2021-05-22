angular.module("app.dashboard.communications")
    .controller("CommunicationsController", function ($scope, AuthService, $timeout, $state, CommonService, CommunicationService, $rootScope, $localStorage, ngToast, $location, uiCalendarConfig, $filter, minutesToHourFilter, SweetAlert, fileReader, $interval, $uibModal) {
	$scope.communication_list = [];
    $scope.communication = {
        date: '',
        current_date_key: '',
        available_sms_credit: 0,
        per_sms_price: '',
        sms_credit_calculation: [],
        gst_percent: {},
        is_apply_igst: false
    }
    $scope.commuCurrentPage = 0;
    $scope.commu_per_page = 10;
    $scope.getCommunicationList = function (number) {
        $scope.commuCurrentPage = number
        var request = {
            doctor_id: ($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id,
            user_id: $rootScope.currentUser.user_id,
            access_token: $rootScope.currentUser.access_token,
            date: $scope.communication.date,
            page: $scope.commuCurrentPage,
            per_page: $scope.commu_per_page
        }
        CommunicationService.getCommunicationList(request, function (response) {
            if (response.status == true) {
                $scope.communication_list = response.data;
                $scope.commu_total_rows = response.count;
                $scope.commu_total_page = Math.ceil(response.count/$scope.commu_per_page);
                $scope.commu_last_rows = $scope.commuCurrentPage*$scope.commu_per_page;
                if($scope.commu_last_rows > $scope.commu_total_rows)
                    $scope.commu_last_rows = $scope.commu_total_rows;
            } else {
                $scope.communication_list = [];
            }
        });
    }
    $scope.getCommuNextPrev = function (val) {
        if(val == 'next') {
            if($scope.commuCurrentPage >= $scope.commu_total_page)
                return false;
            var number = $scope.commuCurrentPage+1;
        }
        if(val == 'prev') {
            if($scope.commuCurrentPage <= 1)
                return false;
            var number = $scope.commuCurrentPage-1;
        }
        $scope.getCommunicationList(number);
    }
    $scope.changeCommunicationTab = function (key) {
        $scope.communication.date = $scope.communication_date[key].created_at
        $scope.communication.current_date_key = key
        $scope.getCommunicationList(1);
    }
    $scope.date_current_page = 1;
    $scope.date_total_records = 0;
    $scope.getCommunicationDates = function (flag) {
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
        $scope.getCommunicationDate();
    }
    $scope.getCommunicationDate = function (number, is_clear) {
        var request = {
            doctor_id: ($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id,
            user_id: $rootScope.currentUser.user_id,
            access_token: $rootScope.currentUser.access_token,
            page: $scope.date_current_page,
            per_page: 10,
        }
        CommunicationService.getCommunicationDate(request, function (response) {
            $scope.communication.available_sms_credit = response.available_sms_credit;
            $scope.communication.sms_credit_calculation = response.sms_credit_calculation;
            $scope.communication.per_sms_price = response.per_sms_price;
            $scope.communication.gst_percent = response.gst_percent;
            $scope.communication.is_apply_igst = response.is_apply_igst;
            if (response.status == true) {
                $scope.communication_date = response.data;
                $scope.date_total_records = response.count;
                $scope.communication.date = $scope.communication_date[0].created_at;
                $scope.communication.current_date_key = 0;
                $scope.getCommunicationList(1);
            } else {
                $scope.communication_date = [];
            }
        });
    }

    $scope.add_message_popup = function() {
    	$scope.modal_data = {
    		available_sms_credit: $scope.communication.available_sms_credit,
            sms_credit_calculation: $scope.communication.sms_credit_calculation,
            per_sms_price: $scope.communication.per_sms_price,
            gst_percent: $scope.communication.gst_percent,
            is_apply_igst: $scope.communication.is_apply_igst
    	};
        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'app/views/communications/add_new_message_modal.html?' + $rootScope.getVer(2),
            controller: 'ModalAddMessageCtrl',
            size: 'lg',
            backdrop: 'static',
            keyboard: false,
            resolve: {
                items: function () {
                    return $scope.modal_data;
                }
            }
        });
        modalInstance.result.then(function (modal_data) {
            $scope.date_current_page = 1;
            $scope.getCommunicationDate();
        }, function () {
            $scope.getCommunicationDate();
        });
    }    

});
angular.module('app.dashboard.communications').controller('ModalAddMessageCtrl', function ($scope, $rootScope, $filter, $uibModalInstance, items, SweetAlert, PatientService, CommonService, CommunicationService, ngToast, dateFilter, $filter) {
	$rootScope.updateBackDropModalHeight('add-message-modal');
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
    $scope.patients_list = [];
    $scope.patient_group_member_list = [];
    $scope.communication = {
        available_sms_credit: items.available_sms_credit,
        sms_credit_calculation: items.sms_credit_calculation,
        sms_price: items.per_sms_price,
        gst_percent: items.gst_percent,
        is_apply_igst: items.is_apply_igst,
        selected_credits: 1,
        is_buy_box_show: false
    }
    $scope.patient = {
        search_patient: '',
        patient_message: '',
        template_id: '',
        selected_list: [],
        patient_id: [],
        patient_grp_id: []
    };
    $scope.recipients_list = undefined;
    $scope.buyMoreBox = function () {
        $scope.changeCredits();
        $scope.communication.is_buy_box_show = true;
    }
    $scope.changeCredits = function () {
        $scope.communication.total_buy_credits = 100*$scope.communication.selected_credits;
        $scope.communication.total_price = $scope.communication.total_buy_credits*$scope.communication.sms_price;
        var cgst_amount = 0.00;
        var sgst_amount = 0.00;
        var igst_amount = 0.00;
        var total_amount = 0.00;
        if($scope.communication.total_price > 0) {
            if($scope.communication.gst_percent.cgst > 0 && !$scope.communication.is_apply_igst) {
                cgst_amount = $scope.communication.total_price * $scope.communication.gst_percent.cgst / 100;
            }
            if($scope.communication.gst_percent.sgst > 0 && !$scope.communication.is_apply_igst) {
                sgst_amount = $scope.communication.total_price * $scope.communication.gst_percent.sgst / 100;
            }
            if($scope.communication.gst_percent.igst > 0 && $scope.communication.is_apply_igst) {
                igst_amount = $scope.communication.total_price * $scope.communication.gst_percent.igst / 100;
            }
            if($scope.communication.is_apply_igst) {
                total_amount = parseFloat($scope.communication.total_price) + igst_amount;
            } else {
                total_amount = parseFloat($scope.communication.total_price) + sgst_amount + cgst_amount;
            }
        }
        $scope.communication.total_price = $scope.communication.total_price.toFixed(2);
        $scope.order_summery_data = {
            cgst_amount: cgst_amount.toFixed(2),
            sgst_amount: sgst_amount.toFixed(2),
            igst_amount: igst_amount.toFixed(2),
            total_amount: total_amount.toFixed(2)
        }
    }
    $scope.testTemplateSms = function () {
        var isError = false;
        var request = {
            doctor_id: ($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id,
            user_id: $rootScope.currentUser.user_id,
            access_token: $rootScope.currentUser.access_token,
            template_id: $scope.patient.template_id,
            message: $scope.patient.patient_message
        }
        if($scope.patient.template_id == undefined || $scope.patient.template_id == '' || $scope.patient.patient_message == undefined || $scope.patient.patient_message == ''){
            ngToast.danger("Please select template and fill all textbox value.");
            return false;
        }
        if($scope.sms_template_text_arr.length != ($scope.sms_dynamic_text.length+1))
            isError = true;
        angular.forEach($scope.sms_dynamic_text, function(val,key) { 
            if(val.text == undefined || val.text == ''){
                isError = true;
            }
        });
        if(isError){
            ngToast.danger("Please fill all textbox value.");
            return false;
        }
        CommunicationService.testTemplateSms(request, function (response) {
            if (response.status == true) {
                    ngToast.success({
                        content: response.message
                    });
                } else {
                    ngToast.danger(response.message);
                }
        });
    }
    $scope.getSmsTemplate = function () {
        var request = {
            doctor_id: ($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id,
            user_id: $rootScope.currentUser.user_id,
            access_token: $rootScope.currentUser.access_token
        }
        CommunicationService.getSmsTemplate(request, function (response) {
            if (response.status == true) {
                $scope.sms_template_list = response.data;
            } else {
                $scope.sms_template_list = [];
            }
        });
    }
    $scope.getSmsTemplate();
    $scope.sms_dynamic_text = [];
    $scope.changeSmsTemplate = function (key) {
        $scope.submitted = false;
        $scope.sms_dynamic_text = [];
        $scope.sms_template_placeholder = [];
        var doctor_name = "Dr. " + $rootScope.currentUser.user_first_name + ' ' + $rootScope.currentUser.user_last_name;
        var sms_template_text = $scope.sms_template_list[key].communication_sms_template_title;
        if($scope.sms_template_list[key].communication_sms_placeholder_json != null)
            $scope.sms_template_placeholder = JSON.parse($scope.sms_template_list[key].communication_sms_placeholder_json);
        sms_template_text = sms_template_text.replace("{#drName#}", doctor_name);
        $scope.sms_template_text_arr = sms_template_text.split("{#var#}");
        $scope.dynamicTextUpdate();
    }
    $scope.dynamicTextUpdate = function(){
        $scope.patient.patient_message = '';
        angular.forEach($scope.sms_template_text_arr, function(val,key) { 
            $scope.patient.patient_message += val;
            if($scope.sms_dynamic_text[key] != undefined && $scope.sms_dynamic_text[key].text != undefined){
                $scope.patient.patient_message += $scope.sms_dynamic_text[key].text;
            }
        });
        $scope.smsCheckLength();
    }
    $scope.addRecipients = function (id) {
        if($scope.recipients_list == undefined)
            $scope.recipients_list = [];
        if($scope.patient.patient_id[id]) {
            var patientRow = $filter('filter')($scope.patients_list, {user_id: id});
            var recipientRow = $filter('filter')($scope.recipients_list, {user_id: id});
            if(patientRow[0] != undefined && recipientRow[0] == undefined){
                $scope.recipients_list.push({
                    user_id: patientRow[0].user_id,
                    patient_name: patientRow[0].patient_name
                });
            }
            if($scope.patient.selected_list == undefined)
                $scope.patient.selected_list = [];
            $scope.patient.selected_list.push(id);
        } else {
            $scope.remove_selected_list(id);
        }
        $scope.patient.pt_selected_all = $scope.patient.patient_id.every(function(itm, val){ 
            return itm; 
        });
    }
    $scope.addGroupsToRecipients = function(group_id) {
        $scope.patient.selected_list = [];
        $scope.recipients_list = [];
        /*if(is_all !=undefined && is_all == 1) {
            var toggleStatus = $scope.patient.pt_selected_all ? true : false;
            angular.forEach($scope.patient_groups_list, function(val,key) { 
                $scope.patient.patient_grp_id[val.patient_group_id] = toggleStatus;
            });
        }*/
        var groupRow = $filter('filter')($scope.patient_groups_list, {patient_group_id: group_id});
        if($scope.patient_group_member_list[group_id] == undefined && groupRow[0].patient_group_auto_added == "1") {
            $scope.getAutoAddedGroupsMember(group_id);
            return false;
        }

        angular.forEach($scope.patient.patient_grp_id, function(is_checked, grp_id) {
            if(is_checked && $scope.patient_group_member_list[grp_id] != undefined && $scope.patient_group_member_list[grp_id].length > 0) {
                angular.forEach($scope.patient_group_member_list[grp_id], function(val,key) { 
                    var recipientRow = $filter('filter')($scope.recipients_list, {user_id: val.user_id});
                    if(recipientRow[0] == undefined) {
                        $scope.recipients_list.push({
                            user_id: val.user_id,
                            patient_name: val.patient_name
                        });
                    }
                    $scope.patient.selected_list.push(val.user_id);
                });
            }
        });
        $scope.patient.pt_selected_all = $scope.patient.patient_grp_id.every(function(itm, v){ 
            return itm; 
        });
    } 
    $scope.getAutoAddedGroupsMember = function(group_id) {
        var groupRow = $filter('filter')($scope.patient_groups_list, {patient_group_id: group_id});
        if(groupRow[0] != undefined) {
            var request = {
                doctor_id: ($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id,
                user_id: $rootScope.currentUser.user_id,
                access_token: $rootScope.currentUser.access_token,
                group_age: groupRow[0].patient_group_age,
                group_disease_id: groupRow[0].patient_group_disease_id,
                group_gender: groupRow[0].patient_group_gender,
            }
            CommunicationService.getAutoAddedGroupsMember(request, function (response) {
                $scope.patient_group_member_list[group_id] = [];
                if (response.status == true) {
                    $scope.patient_group_member_list[group_id] = response.data;
                }
                $scope.addGroupsToRecipients(group_id);
            });
        }
    }
    $scope.remove_selected_list = function(item) {
        if($scope.patient.selected_list != undefined) {
            var index = $scope.patient.selected_list.indexOf(item);
            $scope.patient.selected_list.splice(index, 1);
        } 
        if($scope.patient.selected_list != undefined && $scope.patient.selected_list.length == 0) {
            $scope.patient.selected_list = undefined;
        }
    }
    $scope.currentPage = 0;
    $scope.per_page = 10;
    $scope.getPatients = function (number, is_clear) {
        $scope.patients_list = [];
        $scope.patient_groups_list = [];
        $scope.user_list_tab = 1;
        if(is_clear != undefined && is_clear == 1) {
            $scope.patient.search_patient = '';
            $scope.patient.selected_list = [];
            $scope.patient.patient_id = [];
            $scope.recipients_list = undefined;
        }
        $scope.currentPage = number;
        var request = {
            doctor_id: ($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id,
            user_id: $rootScope.currentUser.user_id,
            search_str: $scope.patient.search_patient,
            access_token: $rootScope.currentUser.access_token,
            page: $scope.currentPage,
            per_page: $scope.per_page
        }
        CommunicationService.getPatients(request, function (response) {
            if (response.status == true) {
                $scope.patient.pt_selected_all = false;
                var i = 0;
                angular.forEach(response.data, function(val,k) { 
                    if($scope.patient.patient_id[val.user_id] == true)
                        i++;
                    if($scope.patient.patient_id[val.user_id] == undefined)
                        $scope.patient.patient_id[val.user_id] = false;
                });
                if(response.data.length == i)
                    $scope.patient.pt_selected_all = true;

                $scope.patients_list = response.data;
                $scope.total_rows = response.count;
                $scope.total_page = Math.ceil(response.count/$scope.per_page);
                $scope.last_rows = $scope.currentPage*$scope.per_page;
                if($scope.last_rows > $scope.total_rows)
                    $scope.last_rows = $scope.total_rows;
                // $rootScope.app.isLoader = true;
            }
        });
    }
    $scope.getPatientGroups = function (number, is_clear) {
        $scope.user_list_tab = 2;
        if(is_clear != undefined && is_clear == 1) {
            $scope.patient_group_member_list = [];
            $scope.patient.search_patient = '';
            $scope.patient.selected_list = [];
            $scope.patient.patient_id = [];
            $scope.patient.patient_grp_id = [];
            $scope.recipients_list = undefined;
        }
        $scope.currentPage = number;
        var request = {
            doctor_id: ($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id,
            user_id: $rootScope.currentUser.user_id,
            search_str: $scope.patient.search_patient,
            access_token: $rootScope.currentUser.access_token,
            page: $scope.currentPage,
            per_page: $scope.per_page
        }
        CommunicationService.getPatientGroups(request, function (response) {
            $scope.patient_groups_list = [];
            $scope.patients_list = [];
            if (response.status == true) {
                $scope.patient.pt_selected_all = false;
                var i = 0;
                angular.forEach(response.data, function(val,k) { 
                    if($scope.patient.patient_grp_id[val.patient_group_id] == true)
                        i++;
                    if($scope.patient.patient_grp_id[val.patient_group_id] == undefined)
                        $scope.patient.patient_grp_id[val.patient_group_id] = false;
                });
                if(response.data.length == i)
                    $scope.patient.pt_selected_all = true;

                $scope.patient_groups_list = response.data;
                angular.forEach(response.group_members, function(val,k) { 
                    $scope.patient_group_member_list[k] = val;
                });
                $scope.total_rows = response.count;
                $scope.total_page = Math.ceil(response.count/$scope.per_page);
                $scope.last_rows = $scope.currentPage*$scope.per_page;
                if($scope.last_rows > $scope.total_rows)
                    $scope.last_rows = $scope.total_rows;
            }
        });
    }
    $scope.togglePatientSelectAll = function() {
        if($scope.recipients_list == undefined)
            $scope.recipients_list = [];
        var toggleStatus = $scope.patient.pt_selected_all ? true : false;
        angular.forEach($scope.patients_list, function(val,key) { 
            if(toggleStatus) {
                $scope.patient.patient_id[val.user_id] = toggleStatus; 
                var patientRow = $filter('filter')($scope.patients_list, {user_id: val.user_id});
                var recipientRow = $filter('filter')($scope.recipients_list, {user_id: val.user_id});
                if(patientRow[0] != undefined && recipientRow[0] == undefined){
                    $scope.recipients_list.push({
                        user_id: patientRow[0].user_id,
                        patient_name: patientRow[0].patient_name
                    });
                }
                if($scope.patient.selected_list == undefined)
                    $scope.patient.selected_list = [];

                var index = $scope.patient.selected_list.indexOf(val.user_id);
                if(index < 0)
                    $scope.patient.selected_list.push(val.user_id);
            } else {
                $scope.remove_selected_list(val.user_id);
                $scope.patient.patient_id[val.user_id] = toggleStatus; 
            }
        });

    }
    $scope.patient.smsCharLimit = 600;
    $scope.patient.remainingChars = $scope.patient.smsCharLimit;
    $scope.patient.per_sms_credit_used = 1;
    $scope.smsCheckLength = function() {
        var message = $scope.patient.patient_message;
        if(message != undefined) {
            $scope.patient.remainingChars = $scope.patient.smsCharLimit - message.length;
            var tmp = true;
            angular.forEach($scope.communication.sms_credit_calculation, function (val, k) {
                if(tmp && parseInt(val) >= message.length){
                    $scope.patient.per_sms_credit_used = k;
                    tmp = false;
                }
            });
        } else {
            $scope.patient.remainingChars = $scope.patient.smsCharLimit;
            $scope.patient.per_sms_credit_used = 1;
        }
    }
    $scope.someSelected = function () {
        if($scope.patient.selected_list != undefined && $scope.patient.selected_list.length > 0)
            return true;
        else
            return false;
    }
    $scope.addCommunication = function(form) {
        $scope.submitted = true;
        if (form.$valid) {
            var grp_id = [];
            angular.forEach($scope.patient.patient_grp_id, function (selected, id) {
                if (selected) {
                   grp_id.push(id);
                }
            });
            var request = {
                doctor_id: ($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id,
                user_id: $rootScope.currentUser.user_id,
                access_token: $rootScope.currentUser.access_token,
                communication_message: $scope.patient.patient_message,
                per_sms_credit_used: $scope.patient.per_sms_credit_used,
                communication_by: 1,
                communication_type: $scope.user_list_tab,
                patient_ids: $scope.patient.selected_list,
                template_id: $scope.patient.template_id,
                group_ids: grp_id
            }
            CommunicationService.addCommunication(request, function (response) {
                if (response.status == true) {
                    ngToast.success({
                        content: response.message
                    });
                    $scope.close(1);
                } else {
                    ngToast.danger(response.message);
                }
            });
        } else {
            ngToast.danger("Please fill all required data");
        }
    }
    $scope.placeCreditsOrder = function () {
        var request = {
            doctor_id: ($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id,
            access_token: $rootScope.currentUser.access_token,
            user_id: $rootScope.currentUser.user_id,
            sub_amount: $scope.communication.total_price,
            paid_amount: $scope.order_summery_data.total_amount,
            cgst_amount: $scope.order_summery_data.cgst_amount,
            sgst_amount: $scope.order_summery_data.sgst_amount,
            igst_amount: $scope.order_summery_data.igst_amount,
            is_apply_igst: $scope.communication.is_apply_igst,
            total_credits: $scope.communication.total_buy_credits
        }
        CommunicationService.createPaymentCreditsOrder(request, function (response) {
            if (response.status == true) {
                var options = {
                    "key": response.data.key,
                    "currency": "INR",
                    "name": "MedSign",
                    "description": "Buy " + $scope.communication.total_buy_credits + " SMS Credits",
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
            total_buy_credits: $scope.communication.total_buy_credits
        }
        CommunicationService.paymentCreditsCapture(request, function (response) {
            if (response.status == true) {
                ngToast.success({
                    content: response.message,
                    timeout: 5000
                });
                $scope.communication.is_buy_box_show = false;
                $scope.communication.available_sms_credit = response.total_credits;
            } else {
                ngToast.danger(response.message);
            }
        });
    }
    $scope.getNextPrev = function (val) {
        if(val == 'next') {
            if($scope.currentPage >= $scope.total_page)
                return false;
            var number = $scope.currentPage+1;
        }
        if(val == 'prev') {
            if($scope.currentPage <= 1)
                return false;
            var number = $scope.currentPage-1;
        }
        if($scope.user_list_tab == 1)
            $scope.getPatients(number);
        else if($scope.user_list_tab == 2)
            $scope.getPatientGroups(number);
    }
    $scope.getPatients(1);
	$scope.close = function (data) {
        $uibModalInstance.close(data);
    };
    $scope.cancel = function () {
        $uibModalInstance.dismiss('cancel');
    };
});