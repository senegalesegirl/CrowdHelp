(function () {
    'use strict';

    angular
        .module('CrowdApp')
        .controller('RegisterController', RegisterController);

    RegisterController.$inject = ['$scope','UserService', '$location', '$rootScope', 'FlashService'];
    function RegisterController($scope, UserService, $location, $rootScope, FlashService) {
        $scope.register = register;

        $scope.user = {};

        function register() {
            $scope.dataLoading = true;
            UserService.Create($scope.user)
                .then(function (response) {
                    if (response.success) {
                        FlashService.Success('Registration successful', true);
                        $location.path('/login');
                    } else {
                        FlashService.Error(response.message);
                        $scope.dataLoading = false;
                    }
                });
        }
    }

})();
