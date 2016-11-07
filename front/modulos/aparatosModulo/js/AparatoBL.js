/*jslint white:true*/
/*global angular*/
myApplication.controller('AparatosCommand', ['$scope', '$http', '$rootScope', function ($scope, $http, $rootScope) {
    "use strict";

    $scope.names = [];
    /*$scope.aAparato = [{Id: 1, Nombre: 'Rachel', Descripcion: 'McAdams', estatus: '1'},
                   {Id: 2, Nombre: 'Chloe', Descripcion: 'Grace Moretz', estatus: '0'},
                   {Id: 3, Nombre: 'Jessica', Descripcion: 'Chastain', estatus: '1'},
                   {Id: 4, Nombre: 'Zooey', Descripcion: 'Deschanel', estatus: '1'},
                   {Id: 5, Nombre: 'Chloe', Descripcion: 'Grace Moretz', estatus: '0'},
                   {Id: 6, Nombre: 'Jessica', Descripcion: 'Chastain', estatus: '1'},
                   {Id: 7, Nombre: 'Zooey', Descripcion: 'Deschanel', estatus: '1'},
                   {Id: 8, Nombre: 'Chloe', Descripcion: 'Grace Moretz', estatus: '0'},
                   {Id: 9, Nombre: 'Jessica', Descripcion: 'Chastain', estatus: '1'},
                   {Id: 10, Nombre: 'Zooey', Descripcion: 'Deschanel', estatus: '1'},
                   {Id: 11, Nombre: 'Chloe', Descripcion: 'Grace Moretz', estatus: '0'},
                   {Id: 12, Nombre: 'Jessica', Descripcion: 'Chastain', estatus: '1'},
                   {Id: 13, Nombre: 'Zooey', Descripcion: 'Deschanel', estatus: '1'},
                   {Id: 14, Nombre: 'Chloe', Descripcion: 'Grace Moretz', estatus: '0'},
                   {Id: 15, Nombre: 'Jessica', Descripcion: 'Chastain', estatus: '1'},
                   {Id: 16, Nombre: 'Zooey', Descripcion: 'Deschanel', estatus: '1'}];*/

    $scope.aparatoSelected = null;
    $scope.aparatoID = 0;
    /*$scope.name = "n";
    $scope.descripcion = "dd";*/
    $scope.status = true;
    $scope.isEdit = false;


    $scope.selectedRow = null;
    $scope.setClickedRow = function(index, aparato){
        $scope.selectedRow = index;
        $scope.aparatoSelected = aparato;
        console.log(aparato);
        console.log($scope.aparatoSelected);
    };


    /*$http.post($rootScope.SERVER_URL+'/bl/AparatoBL.php', {metodo:'getAparato', id:0})
        .success(function (data) {
            $scope.aAparato = data.aparatos;
    })
    .error(function(data){
        console.log('Error: ' + data);
    });*/

    $http({method: 'POST', url: $rootScope.SERVER_URL+"/bl/AparatoBL.php",
        data: {metodo:'getAparato', id:0},
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
        .then(function (response) {
            console.log(response);
            console.log(response.data.success);
            switch(response.data.success){
                case 1:{
                    $scope.aAparato = response.data.aparatos;
                    break;
                }
                default:{
                    console.log('Error: ' + response.data);
                    //$rootScope.showAlert(response.data.message);
                    break;
                }
            }
        }, function (error) {
            console.log(error);
            //$rootScope.showAlert('Problemas en el servidor, intente de nuevo.');
        });



    $scope.saveAparato = function(){
        console.log($scope.aparatoID + " -name " + $scope.name + " -descripcion " + $scope.descripcion + " -status "+$scope.status);
        /*$http.post($rootScope.SERVER_URL+'/bl/AparatoBL.php', {metodo:'saveAparato', id: $scope.aparatoID, nombre: $scope.name, descripcion: $scope.descripcion, estatus: $scope.status})
            .success(function(data){
            $scope.aAparato = data.aparatos;
        })
        .error(function(data){
            console.log('Error: ' + data);
        });*/
        $http({method: 'POST', url: $rootScope.SERVER_URL+"/bl/AparatoBL.php",
        data: {metodo:'saveAparato', IdAparato: $scope.aparatoID, Nombre: $scope.name, Descripcion: $scope.descripcion, Estatus: $scope.status},
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
        .then(function (response) {
            console.log(response);
            console.log(response.data.success);
            switch(response.data.success){
                case 1:{
                    $scope.aAparato = response.data.aparatos;
                    break;
                }
                default:{
                    console.log('Error: ' + response.data);
                    //$rootScope.showAlert(response.data.message);
                    break;
                }
            }
        }, function (error) {
            console.log(error);
            //$rootScope.showAlert('Problemas en el servidor, intente de nuevo.');
        });


        $scope.isSmall = false;
        $scope.name = "";
        $scope.descripcion = "";
        $scope.aparatoID = 0;
        $scope.isEdit = false;
    };

    $scope.editAparato = function(aparato){
        if(!$scope.isEdit){
            $scope.isEdit = true;
            $scope.aparatoID = aparato.Id;
            $scope.name = aparato.Nombre;
            $scope.descripcion = aparato.Descripcion;
            $scope.status = (aparato.estatus === '1');
            $scope.isSmall = true;
        }
    };

    $scope.editAparatoHandler = function(){
        if($scope.aparatoSelected !== null)
        {
            if(!$scope.isEdit){
                $scope.isEdit = true;
                $scope.aparatoID = $scope.aparatoSelected.Id;
                $scope.name = $scope.aparatoSelected.Nombre;
                $scope.descripcion = $scope.aparatoSelected.Descripcion;
                $scope.status = ($scope.aparatoSelected.estatus === '1');
                $scope.isSmall = true;
            }
        }
        else
        {
            alert('Seleccione un elemento para continuar.');
        }
    };
}]);
