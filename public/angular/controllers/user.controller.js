var app = angular.module('CrowdApp')

app.controller('UserController', ['$scope','UserService','$rootScope',
function ($scope, UserService, $rootScope) {

    $scope.user_data = {};
    $scope.task_list = {};

    initController();

    function initController() {
        loadUser()
    }

    function loadUser() {

        var user = $rootScope.globals.currentUser;

        console.log(user);
        
        user.id = 1;

        UserService.GetUserInfo(user.id, function(res){
            var response = res.data

            if(response.success){
                $scope.user_data = response.user_data
                $scope.task_list = response.task_list
            }
        })
    }
}])