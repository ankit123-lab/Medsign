angular.module('app.dashboard').controller('HealthAdviceController', function ($scope, $rootScope, $filter, $uibModalInstance, items, HealthAdviceService, CommonService, ngToast) {
	$rootScope.updateBackDropModalHeight('health-advice-modal');
	var request = {
        user_id: $rootScope.currentUser.user_id,
        doctor_id: items.doctor_id,
        patient_id: items.patient_id,
        access_token: $rootScope.currentUser.access_token,
	}
    $scope.patients_health_advice_data = {
        health_advice_group_id : '',
        patient_health_advice_schedule : '',
        patient_health_advice_send_day : '',
        is_send_email : '1',
        is_send_sms : ''
    }
    $scope.health_advice_groups = [];
    $scope.health_advice = {};
    $scope.health_advice_assigned = '';
	HealthAdviceService
        .getHealthAdviceGroups(request, function (response) {
        	$scope.health_advice_groups = response.data;
            $scope.health_advice_assigned = response.health_advice_assigned;
    });
	$scope.getHealthAdvice = function (group_id) {
        var request = {
            user_id: $rootScope.currentUser.user_id,
            access_token: $rootScope.currentUser.access_token,
            health_advice_group_id: group_id,
            doctor_id: items.doctor_id
        }
        HealthAdviceService
            .getHealthAdvice(request, function (response) {
                $scope.health_advice = response.data;
        });
    }
    $scope.addPatientHealthAdvice = function (form) {
        $scope.submitted = true;
        if (form.$valid) {
            $scope.patients_health_advice_data.user_id = $rootScope.currentUser.user_id;
            $scope.patients_health_advice_data.access_token = $rootScope.currentUser.access_token;
            $scope.patients_health_advice_data.doctor_id = items.doctor_id;
            $scope.patients_health_advice_data.patient_id = items.patient_id;
            HealthAdviceService
                .addPatientHealthAdvice($scope.patients_health_advice_data, function (response) {
                    $scope.submitted = false;
                    if(response.status) {
                        ngToast.success({
                            content: response.message,
                            className: '',
                            dismissOnTimeout: true,
                            timeout: 5000
                        });
                        $scope.close(response.message);
                    } else {
                        ngToast.danger({
                            content: response.message
                        });
                    }
            });
        } else {
            console.log("Error");
        }
    }
    $scope.cancel = function () {
        $uibModalInstance.dismiss('cancel');
    };
    $scope.close = function (health_advice_data) {
        $uibModalInstance.close(health_advice_data);
    };
});