angular.module("medeasy")
        .service('SubscriptionService', function ($rootScope, $http) {
            
            this.getSubscriptionPlan = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_subscription_plan",
                    data: request
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    //console.log(error);
                });
            };

            this.getDoctorSubscription = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_doctor_subscription",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        doctor_id: request.doctor_id
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    //console.log(error);
                });
            };
			
            this.createPaymentOrder = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/create_payment_order",
                    data: request
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    //console.log(error);
                });
            };

            this.paymentCapture = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/payment_capture",
                    data: request
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    //console.log(error);
                });
            };

            this.getSubscriptionHistory = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_doctor_subscription_history",
                    data: request
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    //console.log(error);
                });
            };

            this.promoApply = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/apply_promo_code",
                    data: request
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    //console.log(error);
                });
            };

            this.orderWithoutPayment = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/order_without_payment",
                    data: request
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    //console.log(error);
                });
            };

		});