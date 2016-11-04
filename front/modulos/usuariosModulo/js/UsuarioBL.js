/*jslint white:true*/
/*global angular*/
myApplication.controller('UsuariosCommand', ['$scope', '$http', '$cookies', '$rootScope', function($scope, $http, $cookies, $rootScope){
    "use strict";

    $scope.usuarioAutenticadoId = $cookies.get('usuarioAutenticadoId');
    $scope.usuarioAutenticadoNombre =  $cookies.get('usuarioAutenticadoNombre');
    $scope.gimnasioId = parseInt($cookies.get('GymId'), 10);
    $scope.nombreGym = $cookies.get('nombreGym');

    $scope.usuarioConsultado = null;

    //Booleans for butoons
    $scope.isAsociar = false;
    $scope.isCambioSuc = false;
    $scope.isBaja = false;
    $scope.isReingresar = false;

    $scope.styleStr = "";
    $scope.selectedItem = null;

    $http({method: 'POST', url: $rootScope.SERVER_URL+"/bl/GimnasioBL.php",
        data: {metodo:'getSucursalesByGym', id_Gym: $scope.gimnasioId, id_Usuario: $scope.usuarioAutenticadoId},
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
        .then(function (response) {
            console.log(response);
            console.log(response.data.success);
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
            $rootScope.showAlert('Problemas en el servidor, intente de nuevo.');
        });

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
            $http({method: 'POST', url: $rootScope.SERVER_URL+"/bl/UsuarioEnformaBL.php",
                data: {metodo:'getUsuarioEnformaByCodigo', codigoEnforma: $scope.codigoForza, gimansio: $scope.gimnasioId, sucursal: $scope.selectedItem.S_Id},
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
                .then(function (response) {
                    console.log(response);
                    console.log(response.data.success);
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
                    $rootScope.showAlert('Problemas en el servidor, intente de nuevo.');
                });
        }
        else{
            $scope.usuarioConsultado = null;
            $scope.setButtonsVisibility(-1);
        }
    };

    $scope.getUserBySucursal = function(){
        $http({method: 'POST', url: $rootScope.SERVER_URL+"/bl/SocioBL.php",
            data: {metodo:'obtenerSociosBySucursal', idSucursal: $scope.selectedItem.S_Id},
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
            .then(function (response) {
                console.log(response);
                console.log(response.data.success);
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
                $rootScope.showAlert('Problemas en el servidor, intente de nuevo.');
            });
    };

    $scope.aSucursal = [];
    $scope.aSocios = [];
    /*$scope.aSucursal = [{S_Id: '1', Nombre: 'Juarez', Direccion: 'AV. TEZIUTLAN NORTE # 95', Ciudad: 'PUEBLA', Estado: 'PUEBLA', Pais: 'MEXICO', C_Latitud: '19.058065', C_Longitud: '-98.23065', Id_Gimnasio: '2'},
       {S_Id: '5', Nombre: 'Animas', Direccion: 'Juan Pablo II #3124', Ciudad: 'PUEBLA', Estado: 'PUEBLA', Pais: 'MEXICO', C_Latitud: '19.058063', C_Longitud: '-98.23063', Id_Gimnasio: '3'}];

    $scope.aSocios = [{"Id":"84","CodigoEnforma":"TES0005","Nombre":"TEST BL", "Apellidos":"Apellido TEST Bl",                      "Correo":"scorres5o@correo.com", "IdFacebook":"543435465465415", "Estatus":"1"},                                            {"Id":"85","CodigoEnforma":"ALA0001","Nombre":"Alain","Apellidos":"Nicolás Tello", "Correo":"Alhayn21@gmail.com",            "IdFacebook":"", "Estatus":"1"},
       {"Id":"86","CodigoEnforma":"EDU0002","Nombre":"Jose Eduardo","Apellidos":"Lopez Montero", "Correo":"", "IdFacebook":"666", "Estatus":"1"},                                            {"Id":"85","CodigoEnforma":"ALA0001","Nombre":"Alain","Apellidos":"Nicolás Tello", "Correo":"Alhayn21@gmail.com",            "IdFacebook":"", "Estatus":"1"},
       {"Id":"86","CodigoEnforma":"EDU0002","Nombre":"Jose Eduardo","Apellidos":"Lopez Montero", "Correo":"", "IdFacebook":"666", "Estatus":"1"},                                            {"Id":"85","CodigoEnforma":"ALA0001","Nombre":"Alain","Apellidos":"Nicolás Tello", "Correo":"Alhayn21@gmail.com",            "IdFacebook":"", "Estatus":"1"},
       {"Id":"86","CodigoEnforma":"EDU0002","Nombre":"Jose Eduardo","Apellidos":"Lopez Montero", "Correo":"", "IdFacebook":"666", "Estatus":"1"},                                            {"Id":"85","CodigoEnforma":"ALA0001","Nombre":"Alain","Apellidos":"Nicolás Tello", "Correo":"Alhayn21@gmail.com",            "IdFacebook":"", "Estatus":"1"},
       {"Id":"86","CodigoEnforma":"EDU0002","Nombre":"Jose Eduardo","Apellidos":"Lopez Montero", "Correo":"", "IdFacebook":"666", "Estatus":"1"},                                            {"Id":"85","CodigoEnforma":"ALA0001","Nombre":"Alain","Apellidos":"Nicolás Tello", "Correo":"Alhayn21@gmail.com",            "IdFacebook":"", "Estatus":"1"},
       {"Id":"86","CodigoEnforma":"EDU0002","Nombre":"Jose Eduardo","Apellidos":"Lopez Montero", "Correo":"", "IdFacebook":"666", "Estatus":"1"},                                            {"Id":"85","CodigoEnforma":"ALA0001","Nombre":"Alain","Apellidos":"Nicolás Tello", "Correo":"Alhayn21@gmail.com",            "IdFacebook":"", "Estatus":"1"},
       {"Id":"86","CodigoEnforma":"EDU0002","Nombre":"Jose Eduardo","Apellidos":"Lopez Montero", "Correo":"", "IdFacebook":"666", "Estatus":"1"}];*/

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

    //Asociar usuario a gym
    $scope.asociarUsuario = function(){
        $http({method: 'POST', url: $rootScope.SERVER_URL+"/bl/SocioBL.php",
            data: {metodo:'asociarUsuarioAGimnasio', idGimnasio:$scope.gimnasioId, idUsuario:parseInt($scope.usuarioConsultado.UsuarioEnformaId), idSucursal:$scope.selectedItem.S_Id},
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
            .then(function (response) {
                console.log(response);
                console.log(response.data.success);
                switch(response.data.success){
                    case 0:{
                        $scope.cleanAndRefresh();
                        $scope.aSocios = response.data.socios.socios;
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
    //Reingresar y dar de baja
    $scope.actualizarEstatus = function(estatus){
        $http({method: 'POST', url: $rootScope.SERVER_URL+"/bl/SocioBL.php",
            data: {metodo:'actualizarEstatusSocio', idUsuarioGimnasio: $scope.usuarioConsultado.UsuarioGymId, estatus: estatus, idSucursal: $scope.selectedItem.S_Id},
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
            .then(function (response) {
                console.log(response);
                console.log(response.data.success);
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
                $rootScope.showAlert('Problemas en el servidor, intente de nuevo.');
            });
    };
    //Cambiar de sucursar
    $scope.actualizarSucursal = function(){
        console.log($scope.usuarioConsultado.UsuarioEnformaId);
        console.log($scope.selectedItem.S_Id);
        $http({method: 'POST', url: $rootScope.SERVER_URL+"/bl/SocioBL.php",
            data: {metodo:'actualizarSucursalSocio', idSocio:parseInt($scope.usuarioConsultado.SocioId), idSucursal: $scope.selectedItem.S_Id},
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
            .then(function (response) {
                console.log(response);
                console.log(response.data.success);
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
                $rootScope.showAlert('Problemas en el servidor, intente de nuevo.');
            });
    };
    //Limpiar datos y refrescar grid
    $scope.cleanAndRefresh = function()
    {
        $scope.usuarioConsultado = null;
        $scope.codigoForza = "";
        //$scope.getUserBySucursal();
        $scope.setButtonsVisibility(-1);
    };
}]);
