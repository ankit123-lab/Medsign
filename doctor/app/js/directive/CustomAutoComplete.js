/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function (angular) {
    'use strict';
    var appName = 'app';
    var app = angular.module(appName, []);
    app.directive('clientAutoComplete', ['$filter', clientAutoCompleteDir]);
    app.controller('customerSearchCtrl', ['$scope', customerSearchCtrl]);
    //controllers
    function customerSearchCtrl($scope) {
        var ctrl = this;
        ctrl.client = {name: '', id: ''};
        $scope.dataSource = [{name: 'Oscar', id: 1000}, {name: 'Olgina', id: 2000}, {name: 'Oliver', id: 3000}, {name: 'Orlando', id: 4000}, {name: 'Osark', id: 5000}, {name: 'Osos', id: 5000}, {name: 'Oscarlos', id: 5000}];

        $scope.setClientData = function (item) {
            if (item) {
                ctrl.client = item;
            }
        }
    }
    function clientAutoCompleteDir($filter) {
        return {
            restrict: 'A',
            link: function (scope, elem, attrs) {
                elem.autocomplete({
                    source: function (request, response) {
                        //term has the data typed by the user
                        var params = request.term;
                        //simulates api call with odata $filter
                        var data = scope.dataSource;
                        if (data) {
                            var result = $filter('filter')(data, {name: params});
                            angular.forEach(result, function (item) {
                                item['value'] = item['name'];
                            });
                        }
                        response(result);
                    },
                    minLength: 1,
                    select: function (event, ui) {
                        //force a digest cycle to update the views
                        scope.$apply(function () {
                            scope.setClientData(ui.item);
                        });
                    },
                });
            }
        };
    }
})(angular);