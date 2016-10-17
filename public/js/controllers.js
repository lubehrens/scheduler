var schedulerAppControllers = angular.module('schedulerAppControllers', []);

schedulerAppControllers.controller('LoginController', ['$scope', '$http', '$location', 'userService', function ($scope, $http, $location, userService) {
	$scope.login = function() {
        userService.login(
            $scope.email, $scope.password,
            function(response){
                $location.path('/');
            },
            function(response){
                alert('Something went wrong with the login process. Try again later!');
            }
        );
    }

    $scope.email = '';
    $scope.password = '';

    if(userService.checkIfLoggedIn())
        $location.path('/');
}]);

schedulerAppControllers.controller('SignupController', ['$scope', '$http', function ($scope, $http) {

}]);

schedulerAppControllers.controller('MainController', ['$scope', '$http', '$location', 'userService', function ($scope, $http, $location, userService) {
	$scope.logout = function(){
        userService.logout();
        $location.path('/login');
    }

    $scope.refresh = function(){

        bookService.getAll(function(response){
            
            $scope.books = response;
        
        }, function(){
            
            alert('Some errors occurred while communicating with the service. Try again later.');
        
        });

    }

    if(!userService.checkIfLoggedIn())
        $location.path('/login');

    $scope.books = [];

    $scope.refresh();
    
}]);
