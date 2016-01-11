(function () {
    'use strict';

    angular
        .module('CrowdApp')
        .factory('TasksService', TasksService);

    TasksService.$inject = ['$http', '$cookieStore', '$rootScope', '$timeout', 'UserService'];
    function TasksService($http, $cookieStore, $rootScope, $timeout, UserService) {

        //$http.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
        
        var service = {};

        //public functions
        service.GetAllTasks = _GetAllTasks;
        service.GetTaskByID = _GetTaskByID;
        service.UserAcceptTask = _UserAcceptTask;
        service.UserCompleteTask = _UserCompleteTask;

        return service;


        //private functions

        function _GetAllTasks(callback) {

            $http.get('/api/task', { "token": 'XXXXXX' })
                .success(function (response) {
                    callback(response);
                });
        }

        function _GetTaskByID(id, callback) {
            $http.get('/api/task/'+parseInt(id))
                .success(function (response) {
                    callback(response);
                });
        }

        function _UserAcceptTask(taskId, userId, callback) {

            $http.post('/api/task/accept', { "token": 'XXXXXX', "taskId": taskId, "userId": userId })
                .success(function (response) {
                    callback(response);
                });
        }

        function _UserCompleteTask(taskId, callback) {

            $http.post('/api/task/complete', { "taskId": taskId })
                .success(function (response) {
                    callback(response);
                });
        }
    }


})();