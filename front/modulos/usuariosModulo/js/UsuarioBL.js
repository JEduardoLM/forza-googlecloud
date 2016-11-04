/*jslint white:true*/
/*global angular*/
myApplication.controller('UsuariosCommand', ['$scope', '$http', '$cookies', function($scope, $http, $cookies){
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

    console.log("starting user module...");

    $http.post('/bl/GimnasioBL.php', {metodo:'getSucursalesByGym', id_Gym: $scope.gimnasioId, id_Usuario: $scope.usuarioAutenticadoId})
        .success(function (data) {
            $scope.aSucursal = data.sucursales;
    })
    .error(function(data){
        console.log('Error: ' + data);
    });

    $scope.getUserByCode = function(){
        if($scope.codigoForza.length === 7)
        {
            $http.post('/bl/UsuarioEnformaBL.php', {metodo:'getUsuarioEnformaByCodigo', codigoEnforma: $scope.codigoForza, gimansio: $scope.gimnasioId, sucursal: $scope.selectedItem.S_Id})
                .success(function(data){
                        if(data.success === 0 || data.success === 5 || data.success === 6 || data.success === 9)
                        {
                            $scope.usuarioConsultado = data.Usuario;
                            $scope.setButtonsVisibility(data.success);
                        }else if(data.success === 1){
                            $scope.usuarioConsultado = {Nombre:"Socio no encontrado, verifique el código.", Apellidos:""};
                            $scope.setButtonsVisibility(data.success);
                        }else
                        {
                            console.log(data);
                        }
                })
                .error(function(data){
                    console.log(data);
                });
        }
        else{
            $scope.usuarioConsultado = null;
            $scope.setButtonsVisibility(-1);
        }
    };

    $scope.getUserBySucursal = function(){
        $http.post('/bl/SocioBL.php', {metodo:'obtenerSociosBySucursal', idSucursal: $scope.selectedItem.S_Id})
            .success(function(data){
                    if(data.success === 0)
                    {
                        $scope.aSocios = data.socios;
                    }else
                    {
                        console.log(data);
                    }
            })
            .error(function(data){
                console.log('Error: ' + data);
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
        }
    };

    //Asociar usuario a gym
    $scope.asociarUsuario = function(){
        $http.post('/bl/SocioBL.php', {metodo:'asociarUsuarioAGimnasio', idGimnasio:$scope.gimnasioId, idUsuario:parseInt($scope.usuarioConsultado.UsuarioEnformaId), idSucursal:$scope.selectedItem.S_Id})
            .success(function(data){
                    if(data.success === 0)
                    {
                        $scope.cleanAndRefresh();
                    }else
                    {
                        console.log(data);
                    }
            })
            .error(function(data){
                console.log('Error: ' + data);
            });
    };
    //Reingresar y dar de baja
    $scope.actualizarEstatus = function(estatus){
        $http.post('/bl/SocioBL.php', {metodo:'actualizarEstatusSocio', idUsuarioGimnasio: $scope.usuarioConsultado.UsuarioGymId, estatus: estatus, idSucursal: $scope.selectedItem.S_Id})
            .success(function(data){
                    if(data.success === 0)
                    {
                        $scope.cleanAndRefresh();
                    }else
                    {
                        console.log(data);
                    }
            })
            .error(function(data){
                console.log('Error: ' + data);
            });
    };
    //Cambiar de sucursar
    $scope.actualizarSucursal = function(){
        $http.post('/bl/SocioBL.php', {metodo:'actualizarSucursalSocio', idSocio:parseInt($scope.usuarioConsultado.UsuarioEnformaId), idSucursal: $scope.selectedItem.S_Id})
            .success(function(data){
                    if(data.success === 0)
                    {
                        $scope.cleanAndRefresh();
                    }else
                    {
                        console.log(data);
                    }
            })
            .error(function(data){
                console.log('Error: ' + data);
            });
    };
    //Limpiar datos y refrescar grid
    $scope.cleanAndRefresh = function()
    {
        $scope.usuarioConsultado = null;
        $scope.codigoForza = "";
        $scope.getUserBySucursal();
        $scope.setButtonsVisibility(-1);
    };
}]);
