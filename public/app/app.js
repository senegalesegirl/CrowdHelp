
var CrowdApp = angular.module('CrowdApp', ['ngRoute', 'ngCookies']);

CrowdApp
.config(config)
.run(run);

config.$inject = ['$routeProvider', '$locationProvider'];
function config($routeProvider, $locationProvider) {

    var views = 'public/app/views';

    $routeProvider
        .when('/', {
            controller: 'HomeController',
            templateUrl: views + '/home.view.html'
        })

        .when('/login', {
            controller: 'LoginController',
            templateUrl: views + '/login.view.html'
        })

        .when('/register', {
            controller: 'RegisterController',
            templateUrl: views + '/register.view.html'
        })

        .when('/tasks', {
            controller: 'TasksController',
            templateUrl: views + '/tasks.view.html'
        })

        .when('/tasks/:taskId', {
            controller: 'TasksDetailController',
            templateUrl: views + '/tasks-detail.view.html'
        })

        .when('/user', {
            controller: 'UserController',
            templateUrl: views + '/user.view.html'
        })

        .otherwise({ redirectTo: '/' });

}

run.$inject = ['$rootScope', '$location', '$cookieStore', '$http', 'AuthenticationService'];
function run($rootScope, $location, $cookieStore, $http, AuthenticationService) {
    
    // keep user logged in after page refresh
    $rootScope.globals = $cookieStore.get('globals') || {};
    $rootScope.connected = false;

    if ($rootScope.globals.currentUser) {

        $http.defaults.headers.common['Authorization'] = 'Bearer ' + $rootScope.globals.currentUser.token; // jshint ignore:line
        
        $http.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';

        $rootScope.connected = true;

    }

    $rootScope.$on('$locationChangeStart', function (event, next, current) {


        var loggedIn = $rootScope.globals.currentUser;

        switch($location.path()) {
            case '/logout':
                AuthenticationService.ClearCredentials();
                document.location = '/';
                break;
            case '/login':
                if(loggedIn)
                    document.location = '/';
            default :
                // redirect to login page if not logged in and trying to access a restricted page
                var restrictedPage = $.inArray($location.path(), ['/', '/login', '/register']) === -1;
                var loggedIn = $rootScope.globals.currentUser;

                if (restrictedPage && !loggedIn) {
                    $location.path('/login');
                }
                break;
        }

    });
}