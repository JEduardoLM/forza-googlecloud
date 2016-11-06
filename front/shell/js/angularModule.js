/*jslint white:true*/
/*global angular*/
/*var myApplication = angular.module('demoGym', ['anguFixedHeaderTable', 'ngTable', 'ngAnimate', 'ngRoute', 'ngMessages', 'ngCookies']);*/

myApplication.controller('loginCommand', ['$scope', '$http', '$window', '$cookies', '$rootScope', function($scope, $http, $window, $cookies, $rootScope){
    "use strict";

    $scope.messageLogin = "Error";

    $scope.isInGyms = false;

    $scope.showModal = false;
    $scope.toggleModal = function()
    {
        $scope.showModal = !$scope.showModal;
    }

    //$scope.aGym = [];
    //$scope.selectedItem = null;

    $scope.showHideLogin = function(ruta)
    {
        console.log('showHide...');

        console.log($scope.aGym);
        $window.location.href = ruta;
    };

    /*$scope.showAlert = function(msg){
        $scope.messageLogin = msg;
        $("#dialog").dialog({
            show:{
                effect: "shake",
                duration: 300
            },
            hide:{
                effect: "explode",
                duration: 300
            }
        });
    }*/

    $scope.loginHandler = function(){
        $http({method: 'POST', url: $rootScope.SERVER_URL+"/bl/UsuarioEnformaBL.php",
            data: {metodo:'logueoCorreoPassword', Correo: $scope.email_login, Password: $scope.pass_login},
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
            .then(function (response) {
                console.log(response);
                console.log(response.data.success);
                switch(response.data.success){
                    case 0:{
                        $cookies.put('usuarioAutenticadoId', response.data.Usuario.Id);
                        $cookies.put('usuarioAutenticadoNombre', response.data.Usuario.Nombre + " " + response.data.Usuario.Apellidos);
                        $scope.getGymByUsuarioId(response.data.Usuario.Id);
                        break;
                    }
                    case 5:{ //5 = email not exist
                        $rootScope.showAlert('El correo no se encuentra registrado.');
                        break;
                    }
                    case 6:{ //6 = pass incorrect
                        $rootScope.showAlert('La contraseña es incorrecta.');
                        break;
                    }
                    default:{
                        $rootScope.showAlert(response.data.message);
                        break;
                    }
                }
            }, function (error) {
                $rootScope.showAlert('Problemas en el servidor, intente de nuevo.');
            });
    };

    $scope.getGymByUsuarioId = function(userId){
        $http({method: 'POST', url: $rootScope.SERVER_URL+"/bl/SocioBL.php",
            data: {metodo:'obtenerGimnasiosDeUsuario',  idUsuario: userId},
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
            .then(function (response) {
                console.log(response.data);
                switch(response.data.success){
                    case 0:{
                        $scope.aGym = response.data.usuarioGyms;
                        if($scope.aGym.length > 1)
                        {
                            console.log($scope.aGym);
                            $scope.toggleModal();
                        }
                        else{
                            //$scope.setGymRootScope($scope.aGym[0]);
                            $cookies.put('GymId', $scope.aGym[0].IdGym);
                            $cookies.put('nombreGym', $scope.aGym[0].NombreGimnasio);
                            $cookies.put('colorPrimary', $scope.aGym[0].Configuracion.configuracion["0"].ColorFondo);
                            $cookies.put('ColorComplementario', $scope.aGym[0].Configuracion.configuracion["0"].ColorComplementario);
                            $window.location = "/front/shell/menu.html";
                        }
                        break;
                    }
                    default:{
                        $rootScope.showAlert(response.data.message);
                        break;
                    }
                }
            }, function (error) {
                $rootScope.showAlert('Problemas en el servidor, intente de nuevo.');
        });
    }

    $scope.setGymRootScope = function(gym){
        $rootScope.colorPrincipal = gym.Configuracion.configuracion["0"].ColorFondo;
        $rootScope.colorSecundario = gym.Configuracion.configuracion["0"].ColorComplementario;
    }

    $scope.goToMenu = function(gym)
    {
        $scope.setGymRootScope(gym);
        console.log(gym);
        $cookies.put('GymId', gym.IdGym);
        $cookies.put('nombreGym', gym.NombreGimnasio);
        $cookies.put('colorPrimary', gym.Configuracion.configuracion["0"].ColorFondo);
        $cookies.put('ColorComplementario', gym.Configuracion.configuracion["0"].ColorComplementario);
        //console.log($scope.selectedItem);
        //$cookies.put('GymId', $scope.selectedItem.IdGym);
        $window.location = "/front/shell/menu.html";
    };


    /*$scope.backToMenu = function(){
        $window.location = "/front/shell/menu.html";
    };*/

    /*$scope.names = [{Id: '1', name: 'Alexandra', phone:'83846284357'},
                    {Id: '2', name: 'Daddario', phone:'75473946235'},
                    {Id: '3', name: 'McAdams', phone: '4568523677'}];*/
    $scope.aGym = [/*{IdGym: '1', NombreGimnasio: 'Juarez', Direccion: 'AV. TEZIUTLAN NORTE # 95', Ciudad: 'PUEBLA', Estado: 'PUEBLA', Pais: 'MEXICO', C_Latitud: '19.058065', C_Longitud: '-98.23065', Id_Gimnasio: '2'},
                   {IdGym: '5', NombreGimnasio: 'Animas', Direccion: 'Juan Pablo II #3124', Ciudad: 'PUEBLA', Estado: 'PUEBLA', Pais: 'MEXICO', C_Latitud: '19.058063', C_Longitud: '-98.23063', Id_Gimnasio: '3'}*/];
    $scope.selectedItem = null;

    $scope.changeViewMain = function(ruta)
    {
        $window.location.href = ruta;
        console.log($scope.aGym);
    };

    myApplication.factory('serviceMain',function(){
        return $scope.usuarioAutenticado;
    })
}]);

myApplication.directive('modal', function(){
    return{
        template: '<div class="modal" id="modalEx" style="width:400px;left: calc(100%/2 - 200px);top: calc(50% - 64px);" data-keyboard="false" data-backdrop="static">'+
                    '<div>'+
                      '<div class="modal-content">'+
                        '<div class="modal-header">'+
                          '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>'+
                          '<h4 class="modal-title">Seleccione un gimnasio</h4>'+
                        '</div>'+
                        '<div class="modal-body" ng-transclude>'+
                        '</div>'+
                      '</div>'+
                    '</div>',
        /*template: '<div style="max-width:400px;left: calc(100%/2 - 200px);top: calc(50% - 64px);" class="modal fade" data-keyboard="false" data-backdrop="static">' +
          '<div class="modal-dialog">' +
            '<div class="modal-content">' +
              '<div class="modal-header">' +
                '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>' +
                '<h4 class="modal-title">{{ title }}</h4>' +
              '</div>' +
              '<div class="modal-body" ng-transclude style="width:400px;height:100px;"></div>' +
            '</div>' +
          '</div>' +
        '</div>',*/
        restrict: 'E',
        transclude: true,
        replace: true,
        scope: true,
        link: function postLink(scope, element, attrs){
            scope.title = attrs.title;

            scope.$watch(attrs.visible, function(value){
                if(value == true)
                {
                    $(element).modal('show');
                }
                else{
                    $(element).modal('hide');
                }
            });

            $(element).on('shown.bs.modal', function(){
                scope.$apply(function(){
                    scope.$parent[attrs.visible] = true;
                });
            });

            $(element).on('hidden.bs.modal', function(){
                scope.$apply(function(){
                    scope.$parent[attrs.visible] = true;
                    console.log('hidden');
                });
            });
        }
    };
});


/*myApplication.config(['$routeProvider', function($routeProvider){
    $routeProvider.when('/login',{
        templateUrl: '/front/shell/view/login.html',
    }).
    when('/selectGym',{
        templateUrl: '/front/shell/view/selectGym.html',
    }).
    otherwise({
        redirectTo: '/login',
    });
}]);*/
