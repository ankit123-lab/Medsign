/*
 * Controller Name: SubscriptionController
 * Use: This controller is used for Subscriptions activity
 */
angular.module("app.subscription")
        .controller("SubscriptionController", function ($scope, AuthService, SubscriptionService, $timeout, $state, CommonService, $rootScope, $localStorage, ngToast, $location, uiCalendarConfig, $filter, SettingService, minutesToHourFilter, SweetAlert, fileReader, $interval) {
			$scope.isShowPlan = false;
			$scope.isShowPaymentSummery = false;
			$scope.is_apply_igst = false;
			$scope.invoice_url = '';
			$scope.plan_id = '';
			$scope.is_change = false;
			$scope.promo_data = {
				promo_code: ''
			};
			$scope.promo_applied = {};
			$scope.order_summery_data = {};
			$scope.submitted = false;
			$scope.showPlans = function(val) {
				if(val) {
					$scope.getSubscriptionPlan();
					$scope.isShowPlan = true;
				} else {
					$scope.isShowPlan = false;
				}
			}
			$scope.getSubscriptionPlan = function () {
				var request = {
					doctor_id: ($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id,
					user_id: $rootScope.currentUser.user_id,
					access_token: $rootScope.currentUser.access_token
				}
				SubscriptionService.getSubscriptionPlan(request, function (response) {
					if (response.status == true) {
						$scope.subscription_plan_data = response.data;
					}
				});
			}
			$scope.getDoctorSubscription = function () {
				var request = {
					doctor_id: ($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id,
				}
				SubscriptionService.getDoctorSubscription(request, function (response) {
					if (response.status == true) {
						$scope.doctor_subscription_data = response.data.sub_details;
						$scope.is_apply_igst = response.data.is_apply_igst;
					}
				});
			}
			$scope.buyNow = function (plan_id, is_change) {
				$scope.promo_applied = {};
				$scope.promo_data.promo_code = '';
				$scope.isShowPaymentSummery = true;
				if(is_change) {
					var selectedPlanObj = $filter('filter')($scope.subscription_plan_data, {'sub_plan_id':plan_id},true);
                    $scope.plan_detail = selectedPlanObj[0];
				} else {
					$scope.plan_detail = $scope.doctor_subscription_data;
				}
				$scope.plan_id = plan_id;
				$scope.is_change = is_change;
				$scope.calculation();
			}

			$scope.calculation = function () {
				var paid_amount = $scope.plan_detail.sub_price;
				var settlement_discount = 0;
				if($scope.plan_detail.is_upgrade_plan != undefined && $scope.plan_detail.is_upgrade_plan) {
					if($scope.doctor_subscription_data.settlement_amount != undefined){
						paid_amount = $scope.plan_detail.sub_price - $scope.doctor_subscription_data.settlement_amount;
						settlement_discount = $scope.doctor_subscription_data.settlement_amount;
					}
				}
				var discount = 0;
				if($scope.promo_applied.promo_id != undefined) {
					if($scope.promo_applied.promo_discount_type == "1") {
						paid_amount = paid_amount - $scope.promo_applied.promo_discount;
						discount = $scope.promo_applied.promo_discount;
					} else {
						discount = paid_amount * $scope.promo_applied.promo_discount / 100;
						paid_amount = paid_amount - discount;
					}
				}
				var sub_total = $scope.plan_detail.sub_price-settlement_discount-discount;
				if(sub_total <= 0)
					sub_total = 0.00;

				var cgst_amount = 0.00;
				var sgst_amount = 0.00;
				var igst_amount = 0.00;
				var total_amount = 0.00;
				if(paid_amount > 0) {
					if($scope.plan_detail.sub_tax_cgst > 0) {
						cgst_amount = paid_amount * $scope.plan_detail.sub_tax_cgst / 100;
					}
					if($scope.plan_detail.sub_tax_sgst > 0) {
						sgst_amount = paid_amount * $scope.plan_detail.sub_tax_sgst / 100;
					}
					if($scope.plan_detail.sub_tax_igst > 0) {
						igst_amount = paid_amount * $scope.plan_detail.sub_tax_igst / 100;
					}
					if($scope.is_apply_igst) {
						total_amount = parseFloat(paid_amount) + igst_amount;
					} else {
						total_amount = parseFloat(paid_amount) + sgst_amount + cgst_amount;
					}
				}

				$scope.order_summery_data = {
					'sub_plan_id' : $scope.plan_id,
					'sub_plan_name' : $scope.plan_detail.sub_plan_name,
					'sub_period' : $scope.plan_detail.sub_period,
					'sub_description' : $scope.plan_detail.sub_description,
					'sub_amount' : $scope.plan_detail.sub_price,
					'sub_total' : sub_total,
					'settlement_discount' : settlement_discount,
					'discount' : discount,
					'cgst_percent' : $scope.plan_detail.sub_tax_cgst,
					'sgst_percent' : $scope.plan_detail.sub_tax_sgst,
					'igst_percent' : $scope.plan_detail.sub_tax_igst,
					'cgst_amount' : cgst_amount.toFixed(2),
					'sgst_amount' : sgst_amount.toFixed(2),
					'igst_amount' : igst_amount.toFixed(2),
					'total_amount' : total_amount.toFixed(2),
					'is_change' : $scope.is_change,
					'promo_id' : 0,
				}
				if($scope.promo_applied.promo_id != undefined) {
					$scope.order_summery_data.promo_id = $scope.promo_applied.promo_id;
				}
			}

			$scope.planUpgrade = function (sub_plan_price) {
				var request = {
					doctor_id: ($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id,
					sub_plan_price: sub_plan_price,
					user_id: $rootScope.currentUser.user_id,
					access_token: $rootScope.currentUser.access_token
				}
				$('.upgrade-btn').blur();
				SubscriptionService.getSubscriptionPlan(request, function (response) {
					if (response.status == true) {
						$scope.subscription_plan_data = response.data;
						$scope.isShowPlan = true;
					}
				});
			}
			$scope.back = function () {
				$scope.isShowPaymentSummery = false;
			}
			$scope.placeOrder = function () {
				var request = {
					doctor_id: ($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id,
					sub_plan_id: $scope.order_summery_data.sub_plan_id,
					sub_amount: $scope.order_summery_data.sub_amount,
					settlement_discount: $scope.order_summery_data.settlement_discount,
					discount: $scope.order_summery_data.discount,
					promo_id: $scope.order_summery_data.promo_id,
					is_apply_igst: $scope.is_apply_igst,
					access_token: $rootScope.currentUser.access_token,
                    user_id: $rootScope.currentUser.user_id,
				}
				if($scope.order_summery_data.total_amount > 0) {
					SubscriptionService.createPaymentOrder(request, function (response) {
						if (response.status == true) {
							var options = {
							    "key": response.data.key,
							    "currency": "INR",
							    "name": "MedSign",
							    "description": response.data.description,
							    "image": $rootScope.app.app_url + "app/images/logo_dashoboard.png",
							    "order_id": response.data.order_id,
							    "handler": function (razorpay_response) {
							    	if(razorpay_response.razorpay_payment_id != undefined && razorpay_response.razorpay_payment_id != '') {
							    		razorpay_response.paid_amount = response.data.paid_amount;
							        	$scope.paymentCapture(razorpay_response);
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
				} else {
					request.is_change_plan = $scope.order_summery_data.is_change;
					SubscriptionService.orderWithoutPayment(request, function (response) {
						if (response.status == true) {
							ngToast.success({
	                            content: response.message,
	                            timeout: 5000
	                        });
	                        $scope.isShowPaymentSummery = false;
	                        $localStorage.currentUser.is_sub_active = true;
	                        $localStorage.currentUser.sub_plan_setting = response.data.sub_plan_setting;
	                        $scope.isShowPlan = false;
						} else {
							ngToast.danger(response.message);
						}
					});
				}
			}
			$scope.paymentCapture = function (data) {
				var request = {
					doctor_id: ($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id,
					order_id: data.razorpay_order_id,
					payment_id: data.razorpay_payment_id,
					signature: data.razorpay_signature,
					sub_plan_id: $scope.order_summery_data.sub_plan_id,
					paid_amount: data.paid_amount,
					is_change_plan: $scope.order_summery_data.is_change,
					access_token: $rootScope.currentUser.access_token,
                    user_id: $rootScope.currentUser.user_id,

				}
				SubscriptionService.paymentCapture(request, function (response) {
					if (response.status == true) {
						ngToast.success({
                            content: response.message,
                            timeout: 5000
                        });
                        $scope.isShowPaymentSummery = false;
                        $localStorage.currentUser.is_sub_active = true;
                        $localStorage.currentUser.sub_plan_setting = response.data.sub_plan_setting;
                        $scope.isShowPlan = false;
					} else {
						ngToast.danger(response.message);
					}
				});
			}

			$scope.getSubscriptionHistory = function (data) {
				var request = {
					doctor_id: ($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id,
					access_token: $rootScope.currentUser.access_token,
                    user_id: $rootScope.currentUser.user_id,
				}
				SubscriptionService.getSubscriptionHistory(request, function (response) {
					if (response.status == true) {
						$scope.subscription_history_data = response.data;
					} else {
						ngToast.danger(response.message);
					}
				});
			}

			$scope.viewInvoice = function (invoice_url) {
				// $scope.invoice_url = invoice_url;
				var pdf_url = btoa(encodeURI($rootScope.app.base_url + 'pdf_preview/web/view_pdf.php?file_url=' + btoa(encodeURI(invoice_url))));
				$scope.invoice_url = $rootScope.app.base_url + "pdf_preview/web/pdf_preview.php?charting_url=" + pdf_url; 
				$("#modal_invoice_view").modal("show");
			}

			$scope.invoiceDownload = function (payment_id) {
				window.location.href = $rootScope.app.apiUrl + "/cron/download_invoice/" + payment_id;
			}

			$scope.getClassActive = function (route) {
                return ($location.path() === route) ? 'active' : '';
            }

            $scope.promoApply = function (form) {
            	$scope.submitted = true;
            	$scope.promo_applied = {};
            	if (form.$valid) {
	            	var request = {
						doctor_id: ($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id,
						access_token: $rootScope.currentUser.access_token,
	                    user_id: $rootScope.currentUser.user_id,
	                    promo_code: $scope.promo_data.promo_code,
					}
					SubscriptionService.promoApply(request, function (response) {
						if (response.status == true) {
							$scope.promo_applied = response.data;
							ngToast.success({
	                            content: response.message,
	                            timeout: 5000
	                        });
	                        $scope.submitted = false;
						} else {
							$scope.promo_applied = {};
							ngToast.danger(response.message);
						}
	                    $scope.calculation();
					});
				}
			}

});