var schedulerApp = angular.module('schedulerApp', [
  'ngRoute',
  'schedulerAppControllers',
  'schedulerAppServices'
]);

schedulerApp.config(['$routeProvider', function($routeProvider) {
    
    $routeProvider.
    when('/login', {
        templateUrl: 'partials/login.html',
        controller: 'LoginController'
    }).
    when('/signup', {
        templateUrl: 'partials/signup.html',
        controller: 'SignupController'
    }).
    when('/', {
        templateUrl: 'partials/index.html',
        controller: 'MainController'
    }).
    otherwise({
        redirectTo: '/'
    });

}]);
