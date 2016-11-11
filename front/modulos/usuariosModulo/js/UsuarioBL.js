/*jslint white:true*/
/*global angular*/
myApplication.controller('UsuariosCommand', ['$scope', '$http', '$cookies', '$rootScope', function($scope, $http, $cookies, $rootScope){
    "use strict";

    $scope.usuarioConsultado = null;

    //Booleans for butoons
    $scope.isAsociar = false;
    $scope.isCambioSuc = false;
    $scope.isBaja = false;
    $scope.isReingresar = false;

    $scope.styleStr = "";
    $scope.selectedItem = null;

    $scope.getSucursalByGym = function(){
        $rootScope.showProgress = true;
        console.log("gym:"+$rootScope.gimnasioId+" usuario:"+$rootScope.usuarioAutenticadoId);
        $http({method: 'POST', url: $rootScope.SERVER_URL+"/bl/GimnasioBL.php",
            data: {metodo:'getSucursalesByGym', id_Gym: parseInt($rootScope.gimnasioId), id_Usuario: parseInt($rootScope.usuarioAutenticadoId)},
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
            .then(function (response) {
                console.log(response);
                $rootScope.showProgress = false;
                switch(response.data.success){
                    case 0:{
                        $scope.aSucursal = response.data.sucursales;
                        if ($scope.aSucursal.length == 1){
                            $scope.selectedItem = $scope.aSucursal[0];
                            $scope.getUserBySucursal();
                        }
                        break;
                    }
                    default:{
                        $rootScope.showAlert(response.data.message);
                        break;
                    }
                }
            }, function (error) {
                $rootScope.showProgress = false;
                $rootScope.showAlert('Problemas en el servidor, intente de nuevo.');
            });
    }

    $scope.selectUser = function(socio, index){
        $scope.codigoForza = '';
        $scope.usuarioConsultado = socio;
        $scope.setButtonsVisibility(socio.Estatus=='0'? 6 : 5);
    }

    /*******
    * Metodos a la base de datos
    ********/

    $scope.getUserByCode = function(){
        if($scope.codigoForza.length === 7)
        {
            $rootScope.showProgress = true;
            console.log("gym:"+$scope.gimnasioId+" sucursal:"+$scope.selectedItem.S_Id);
            $http({method: 'POST', url: $rootScope.SERVER_URL+"/bl/UsuarioEnformaBL.php",
                data: {metodo:'getUsuarioEnformaByCodigo', codigoEnforma: $scope.codigoForza, gimansio: $scope.gimnasioId, sucursal: $scope.selectedItem.S_Id},
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
                .then(function (response) {
                    console.log(response);
                    $rootScope.showProgress = false;
                    switch(response.data.success){
                        case 0:
                        case 5:
                        case 6:
                        case 9:{
                            $scope.usuarioConsultado = response.data.Usuario;
                            $scope.setButtonsVisibility(response.data.success);
                            break;
                        }
                        case 1:{
                            $scope.usuarioConsultado = {Nombre:"Socio no encontrado, verifique el código.", Apellidos:""};
                            $scope.setButtonsVisibility(response.data.success);
                            break;
                        }
                        default:{
                            $rootScope.showAlert(response.data.message);
                            break;
                        }
                    }
                }, function (error) {
                    $rootScope.showProgress = false;
                    $rootScope.showAlert('Problemas en el servidor, intente de nuevo.');
                });
        }
        else{
            $scope.usuarioConsultado = null;
            $scope.setButtonsVisibility(-1);
        }
    };

    $scope.getUserBySucursal = function(){
        $rootScope.showProgress = true;
        $http({method: 'POST', url: $rootScope.SERVER_URL+"/bl/SocioBL.php",
            data: {metodo:'obtenerSociosBySucursal', idSucursal: $scope.selectedItem.S_Id},
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
            .then(function (response) {
                $rootScope.showProgress = false;
                console.log(response);
                switch(response.data.success){
                    case 0:{
                        $scope.aSocios = response.data.socios;
                        break;
                    }
                    default:{
                        $rootScope.showAlert(response.data.message);
                        break;
                    }
                }
            }, function (error) {
                $rootScope.showProgress = false;
                $rootScope.showAlert('Problemas en el servidor, intente de nuevo.');
            });
    };

    $scope.aSucursal = [];
    $scope.aSocios = [];

    $scope.setButtonsVisibility = function(estatusDisposicion)
    {
        switch(estatusDisposicion){
            case 0:{
                $scope.isAsociar = true;
                $scope.isCambioSuc = false;
                $scope.isBaja = false;
                $scope.isReingresar = false;
                $scope.styleStr = "color: #000;";
                break;
            }
            case 1:{
                $scope.isAsociar = false;
                $scope.isCambioSuc = false;
                $scope.isBaja = false;
                $scope.isReingresar = false;
                $scope.styleStr = "color: #c62828;";
                break;
            }
            case 5:{
                $scope.isAsociar = false;
                $scope.isCambioSuc = false;
                $scope.isBaja = true;
                $scope.isReingresar = false;
                $scope.styleStr = "color: #2e7d32;";
                break;
            }
            case 6:{
                $scope.isAsociar = false;
                $scope.isCambioSuc = false;
                $scope.isBaja = false;
                $scope.isReingresar = true;
                $scope.styleStr = "color: #c62828;";
                break;
            }
            case 9:{
                $scope.isAsociar = false;
                $scope.isCambioSuc = true;
                $scope.isBaja = false;
                $scope.isReingresar = false;
                $scope.styleStr = "color: #ff6e40;";
                break;
            }
            default:{
                $scope.isAsociar = false;
                $scope.isCambioSuc = false;
                $scope.isBaja = false;
                $scope.isReingresar = false;
                $scope.styleStr = "color: #000;";
                break;
            }
        }
    };

    $rootScope.responsePositive = function(evt){
        console.log(evt);
        switch(evt.target.name){
            case 'btnAsociar':{
                $scope.asociarUsuario(evt)
                break;
            }
            case 'btnBaja':{
                $scope.actualizarEstatus(0);
                break;
            }
        }
    }

    //Asociar usuario a gym
    $scope.asociarUsuario = function(evt){
        console.log(evt);
        console.log(parseInt($scope.usuarioConsultado.UsuarioEnformaId !== undefined? $scope.usuarioConsultado.UsuarioEnformaId : $scope.usuarioConsultado.Id));
        $rootScope.showProgress = true;
        console.log("idUsuario:" + parseInt($scope.usuarioConsultado.UsuarioEnformaId !== undefined? $scope.usuarioConsultado.UsuarioEnformaId : $scope.usuarioConsultado.Id));

        $http({method: 'POST', url: $rootScope.SERVER_URL+"/bl/SocioBL.php",
            data: {metodo:'asociarUsuarioAGimnasio', idGimnasio:$scope.gimnasioId, idUsuario:parseInt($scope.usuarioConsultado.UsuarioEnformaId !== undefined? $scope.usuarioConsultado.UsuarioEnformaId : $scope.usuarioConsultado.Id), idSucursal:$scope.selectedItem.S_Id},
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
            .then(function (response) {
                console.log(response);
                $rootScope.showProgress = false;
                switch(response.data.success){
                    case 0:{
                        $scope.cleanAndRefresh();
                        $scope.aSocios = response.data.socios.socios;
                        break;
                    }
                    default:{
                        $rootScope.showAlert(evt, response.data.message, 'Error');
                        break;
                    }
                }
            }, function (error) {
                $rootScope.showProgress = false;
                $rootScope.showAlert(evt, 'Problemas en el servidor, intente de nuevo.', 'Error');
            });
    };
    //Reingresar y dar de baja
    $scope.actualizarEstatus = function(estatus){
        $rootScope.showProgress = true;
        $http({method: 'POST', url: $rootScope.SERVER_URL+"/bl/SocioBL.php",
            data: {metodo:'actualizarEstatusSocio', idUsuarioGimnasio: $scope.usuarioConsultado.UsuarioGymId, estatus: estatus, idSucursal: $scope.selectedItem.S_Id},
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
            .then(function (response) {
                console.log(response);
                $rootScope.showProgress = false;
                switch(response.data.success){
                    case 0:{
                        $scope.cleanAndRefresh();
                        $scope.aSocios = response.data.Socios.socios;
                        break;
                    }
                    default:{
                        $rootScope.showAlert(response.data.message);
                        break;
                    }
                }
            }, function (error) {
                $rootScope.showProgress = false;
                $rootScope.showAlert('Problemas en el servidor, intente de nuevo.');
            });
    };
    //Cambiar de sucursar
    $scope.actualizarSucursal = function(){
        $rootScope.showProgress = true;
        $http({method: 'POST', url: $rootScope.SERVER_URL+"/bl/SocioBL.php",
            data: {metodo:'actualizarSucursalSocio', idSocio:parseInt($scope.usuarioConsultado.SocioId), idSucursal: $scope.selectedItem.S_Id},
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
            .then(function (response) {
                console.log(response);
                $rootScope.showProgress = false;
                switch(response.data.success){
                    case 0:{
                        $scope.cleanAndRefresh();
                        $scope.aSocios = response.data.Socios.socios;
                        break;
                    }
                    default:{
                        $rootScope.showAlert(response.data.message);
                        break;
                    }
                }
            }, function (error) {
                $rootScope.showProgress = false;
                $rootScope.showAlert('Problemas en el servidor, intente de nuevo.');
            });
    };
    //Limpiar datos y refrescar grid
    $scope.cleanAndRefresh = function()
    {
        $scope.aSocios = [];
        $scope.usuarioConsultado = null;
        $scope.codigoForza = "";
        $scope.setButtonsVisibility(-1);
    };

    $rootScope.goToMenu = function(gym)
    {
        $scope.aSucursal = [];
        $scope.cleanAndRefresh();
        $rootScope.setGymRootScope(gym);
        $scope.getSucursalByGym();
    };
}]);
