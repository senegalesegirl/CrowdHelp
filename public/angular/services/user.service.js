(function () {
    'use strict';

    angular
        .module('CrowdApp')
        .factory('UserService', UserService);

    UserService.$inject = ['$http'];
    function UserService($http) {
        var service = {};

        service.GetAll = GetAll;
        service.GetById = GetById;
        service.GetByUsername = GetByUsername;
        service.Create = Create;
        service.Update = Update;
        service.Delete = Delete;

        service.GetCurrentUser  = _GetCurrentUser;
        service.GetUserInfo  = _GetUserInfo;

        return service;

        function GetAll() {
            return $http.get('/api/user').then(handleSuccess, handleError('Error getting all users'));
        }

        function GetById(id) {
            return $http.get('/api/user/' + id).then(handleSuccess, handleError('Error getting user by id'));
        }

        function GetByUsername(username) {
            return $http.get('/api/user/' + username).then(handleSuccess, handleError('Error getting user by username'));
        }

        function Create(user) {
            return $http.post('/api/user', user).then(handleSuccess, handleError('Error creating user'));
        }

        function Update(user) {
            return $http.put('/api/user/' + user.id, user).then(handleSuccess, handleError('Error updating user'));
        }

        function Delete(id) {
            return $http.delete('/api/user/' + id).then(handleSuccess, handleError('Error deleting user'));
        }


        function _GetUserInfo(id, callback, handleerror) {
            return $http.get('/api/user/' + id).then(callback, handleError);
        }

        function _GetCurrentUser() {
            return 1;//return $http.delete('/api/user/' + id).then(handleSuccess, handleError('Error deleting user'));
        }

        // private functions

        function handleSuccess(res) {
            return res.data;
        }

        function handleError(error) {
            return function () {
                return { success: false, message: error };
            };
        }
    }

})();
