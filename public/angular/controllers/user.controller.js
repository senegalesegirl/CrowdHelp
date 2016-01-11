var app = angular.module('CrowdApp')

app.controller('UserController', ['$scope','UserService','$rootScope',
function ($scope, UserService, $rootScope) {

    $scope.user = {};
    $scope.task_list = {};

    initController();

    function initController() {
        loadUser()
    }

    function loadUser() {

        //get current user
        var user = $rootScope.globals.currentUser;
        

        user.id = 1;

        UserService.GetUserInfo(user.id, function(res){
            var response = res.data

            if(response.success){
                
                $scope.user = response.user
                $scope.task_list = response.task_list
            }
        })
    }
}])