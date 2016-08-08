/*jslint white:true*/
/*global angular*/
myApplication.controller('menuCommand', ['$scope', '$window', function($scope, $window){
    "use strict";
    $scope.changeMenuHandler = function(){
        $window.location = "";
    };
}]);
