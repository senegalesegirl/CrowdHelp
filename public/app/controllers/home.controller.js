var app = angular.module('CrowdApp')

app.controller('HomeController', ['$scope', '$http','UserService','$rootScope',
    function ($scope, $http, UserService, $rootScope) {

        initController();

        function initController() {
            //loadCurrentUser();
        }

        function loadCurrentUser() {
            UserService.GetByUsername($rootScope.globals.currentUser.username)
                .then(function (user) {
                    $scope.user = user;
                });
        }
    }
]);