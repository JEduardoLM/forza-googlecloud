myApplication.controller('MenuCommand', ['$scope', '$cookies', '$window', '$rootScope', function($scope, $cookies, $window, $rootScope){

    /*$scope.usuarioAutenticadoId = $cookies.get('usuarioAutenticadoId');
    $scope.usuarioAutenticadoNombre =  $cookies.get('usuarioAutenticadoNombre');
    $scope.gimnasioId = parseInt($cookies.get('GymId'), 10);
    $scope.nombreGym = $cookies.get('nombreGym');
    $rootScope.colorPrincipal = $cookies.get('colorPrimary');
    $rootScope.colorSecundario = $cookies.get('ColorComplementario');*/

    $scope.changeMenuHandler = function(){
        $window.location = "/DemoGym/front/modulos/configuracionModulo/configuracion.html";
    };

    $scope.goToUsuarioModule = function()
    {
        $window.location = "/DemoGym/front/modulos/usuariosModulo/view/users.html";
    };

}]);
