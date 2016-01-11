var app = angular.module('CrowdApp')

app.controller('TasksController', ['$scope', '$http','TasksService','$rootScope',
function ($scope, $http, TasksService, $rootScope) {

    $scope.task_list = [];

    initController();

    function initController() {
        loadTasks()
    }

    function loadTasks() {
        TasksService.GetAllTasks(function(data){
            if(data.success)
                $scope.task_list = data.tasks;
        })
    }
}])

app.controller('TasksDetailController', ['$scope', '$http','TasksService','$rootScope','$routeParams','UserService',
function ($scope, $http, TasksService, $rootScope, $routeParams, UserService) {

    $scope.taskId = $routeParams.taskId;

    $scope.task = {};

    $scope.loading = false;

    $scope.progress = false;

    initController();

    function initController() {
        $scope.loading = true;

        TasksService.GetTaskByID($scope.taskId, function(data){

            if(data.success && data.task){
                $scope.task = data.task;
            }else{
                $scope.error = "La tache demandée n'existe pas ou a été supprimée";
            }
            $scope.loading = false;
        });
    }

    $scope.acceptTask = function(){
        console.log("Checking autorisation ...")
        $scope.progress = true;
        
        var userid = UserService.GetCurrentUser();

        TasksService.UserAcceptTask(  $scope.taskId, userid, function(data){
            console.log(data);
        });
    }

    $scope.completeTask = function(){
        console.log("Completting ...")
        
        $scope.progress = true;

        TasksService.UserCompleteTask( $scope.taskId, function(data){
            console.log(data);
            $scope.progress = false;
        });
    }


}])