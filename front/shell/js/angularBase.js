/*jslint white:true*/
/*global angular*/
var myApplication = angular.module('demoGym', ['anguFixedHeaderTable', 'ngTable', 'ngAnimate', 'ngRoute', 'ngCookies']);

myApplication.controller('baseCommand', ['$scope', '$http', '$window', '$cookies', '$rootScope', function($scope, $http, $window, $cookies, $rootScope){
    "use strict";

    //config
    /*************---- PRODUCCION(Google Cloud) ----**************/
    //$rootScope.SERVER_URL = "http://forza-1355.appspot.com";
    /*************---- DESARROLLO(Hostinger) ----**************/
    $rootScope.SERVER_URL = "http://enformadesarrollo.esy.es/DemoGym";

    $scope.usuarioAutenticadoId = $cookies.get('usuarioAutenticadoId');
    $scope.usuarioAutenticadoNombre =  $cookies.get('usuarioAutenticadoNombre');
    $scope.gimnasioId = parseInt($cookies.get('GymId'), 10);
    $scope.nombreGym = $cookies.get('nombreGym');
    $rootScope.colorPrincipal = $cookies.get('colorPrimary');
    $rootScope.colorSecundario = $cookies.get('ColorComplementario');
    console.log($rootScope.colorPrincipal);

    $rootScope.colorAccent = '00bfa5';

    $rootScope.colorToRgba = function(hex, alp){
        var c;
        hex = '#'+hex;
        if(/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)){
            c= hex.substring(1).split('');
            if(c.length== 3){
                c= [c[0], c[0], c[1], c[1], c[2], c[2]];
            }
            c= '0x'+c.join('');
            return 'background-color: rgba('+[(c>>16)&255, (c>>8)&255, c&255].join(',')+','+alp+')';
        }
        throw new Error('Bad Hex');
    }

    $rootScope.showAlert = function(msg){
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
    }

    $rootScope.backToMenu = function(){
        $window.location = "/DemoGym/front/shell/menu.html";
    };
}]);
