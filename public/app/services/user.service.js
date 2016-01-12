(function () {
    'use strict';

    angular
        .module('CrowdApp')
        .factory('UserService', UserService);

    UserService.$inject = ['$http'];
    function UserService($http) {
        var service = {};

        service.GetCurrentUser  = _GetCurrentUser;
        service.GetUserInfo  = _GetUserInfo;

        return service;

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
