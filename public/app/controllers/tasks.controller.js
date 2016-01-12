var app = angular.module('CrowdApp')

app.controller('TasksController', ['$scope', '$http','TasksService','$rootScope',
function ($scope, $http, TasksService, $rootScope) {

    $scope.task_list = [];

    initController();

    function initController() {
        loadTasks()
    }

    function loadTasks() {
        TasksService.GetAllTasks(function(response){
            if(response.success)
                $scope.task_list = response.tasks;
        })
    }
}])

app.controller('TasksDetailController', ['$scope', '$http','TasksService','$rootScope','$routeParams','UserService','FlashService',
function ($scope, $http, TasksService, $rootScope, $routeParams, UserService, FlashService) {

    $scope.taskId = $routeParams.taskId;

    $scope.task = {};

    $scope.loading = false;

    $scope.progress = false;

    initController();

    function initController() {
        $scope.loading = true;

        TasksService.GetTaskByID($scope.taskId, function(response){

            if(response.success && response.task){
                $scope.task = response.task;
            }else{
                $scope.error = "La tache demandée n'existe pas ou a été supprimée";
            }
            $scope.loading = false;
        });
    }

    $scope.acceptTask = function(){
        $scope.progress = true;
        
        var userid = UserService.GetCurrentUser();

        TasksService.UserAcceptTask(  $scope.taskId, userid, function(response){

            $scope.progress = false;
            
            if(response.success){
                FlashService.Success('La tache a bien été sélectionnée');

            }else{
                FlashService.Error(response.message);
            }
        });
    }

    $scope.completeTask = function(){
        console.log("Completting ...")
        
        $scope.progress = true;

        TasksService.UserCompleteTask( $scope.taskId, function(response){
            console.log(response);
            $scope.progress = false;
        });
    }


}])