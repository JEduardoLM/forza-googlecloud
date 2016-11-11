myApplication.controller('MenuCommand', ['$scope', '$cookies', '$window', '$rootScope', function($scope, $cookies, $window, $rootScope){

    console.log($cookies.getAll());

    $scope.changeMenuHandler = function(){
        $window.location = "/front/modulos/configuracionModulo/configuracion.html";
    };

    $scope.goToUsuarioModule = function()
    {
        $window.location = "/front/modulos/usuariosModulo/view/users.html";
    };

    $rootScope.goToMenu = function(gym)
    {
        $rootScope.setGymRootScope(gym);
    };
}]);
