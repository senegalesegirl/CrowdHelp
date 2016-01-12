
var app = angular.module('CrowdApp')

app.controller('LoginController', LoginController);

LoginController.$inject = ['$scope','$location', 'AuthenticationService', 'FlashService'];
    function LoginController($scope, $location, AuthenticationService, FlashService) {
        
        $scope.username, $scope.password = "";

        $scope.login = login;
        
        function login() {
            $scope.dataLoading = true;

            AuthenticationService.Login($scope.username, $scope.password, function (response) {

                if (response.success) {

                    console.log(response);

                    AuthenticationService.SetCredentials(response.user);
                    
                    //redirection
                    document.location = '/';

                } else {
                    FlashService.Error(response.message);
                    $scope.dataLoading = false;
                }

            });

            return false;
        };
    }