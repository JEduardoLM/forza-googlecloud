/*jslint white:true*/
/*global angular*/
var myApplication = angular.module('demoGym', ['anguFixedHeaderTable', 'ngTable', 'ngAnimate', 'ngRoute', 'ngCookies', 'ngMaterial'])
.config(function($mdThemingProvider){
    var forzaTheme = $mdThemingProvider.extendPalette('indigo', {
            '50': '#FAFAFA',
            '100': '#E0E0E0',
            '200': '#BDBDBD',
            '300': '#9E9E9E',
            '400': '#757575',
            '500': '#616161',
            '600': '#424242',
            '700': '#424242',
            '800': '#212121',
            '900': '#212121',
            'A100': '#A7FFEB',
            'A200': '#64FFDA',
            'A400': '#1DE9B6',
            'A700': '#00BFA5',
            'contrastDefaultColor': 'light'
        });

        // Register the new color palette map with the name <code>neonRed</code>
        $mdThemingProvider.definePalette('forzaTheme', forzaTheme);

        // Use that theme for the primary intentions
        $mdThemingProvider.theme('default').primaryPalette('forzaTheme');

        $mdThemingProvider.theme('default')
            .accentPalette('pink');
    });;

myApplication.controller('baseCommand', ['$scope', '$http', '$window', '$cookies', '$rootScope', '$mdDialog', function($scope, $http, $window, $cookies, $rootScope, $mdDialog){
    "use strict";

    console.log(window.location);
    console.log($cookies.get('usuarioAutenticadoId'));
    if (window.location.pathname !== '/' && window.location.pathname !== '/index.html'){
        if ($cookies.get('usuarioAutenticadoId') == undefined){
            $window.location = "/index.html";
        }
    }

    //config
    /*************---- PRODUCCION(Google Cloud) ----**************/
    //$rootScope.SERVER_URL = "http://forza-1355.appspot.com";
    /*************---- DESARROLLO(Hostinger) ----**************/
    $rootScope.SERVER_URL = "http://enformadesarrollo.esy.es";
    /*************---- Raiz(Local) ----**************/
    /*$rootScope.SERVER_URL = "";*/

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
    };

    $rootScope.backToMenu = function(){
        $window.location = "/front/shell/menu.html";
    };

    $rootScope.status = '  ';
    $rootScope.customFullscreen = false;

    $rootScope.showAlert = function(ev, msg, title) {
        $mdDialog.show(
          $mdDialog.alert()
            .parent(angular.element(document.querySelector('#popupContainer')))
            .clickOutsideToClose(true)
            .title(title)
            .textContent(msg)
            .ariaLabel('Alert')
            .ok('OK')
            .targetEvent(ev)
        );
    };

    $rootScope.showConfirm = function(ev, msg, title) {
    // Appending dialog to document.body to cover sidenav in docs app
        var confirm = $mdDialog.confirm()
              .title(title)
              .textContent(msg)
              .ariaLabel('Confirm')
              .targetEvent(ev)
              .ok('Aceptar')
              .cancel('Cancelar');

        $mdDialog.show(confirm).then(function() {
          $rootScope.status = 'You decided to get rid of your debt.';
        }, function() {
          $rootScope.status = 'You decided to keep your debt.';
        });
    };

    $rootScope.showAdvanced = function(ev) {
        $mdDialog.show({
          controller: DialogController,
          templateUrl: 'dialog1.tmpl.html',
          parent: angular.element(document.body),
          targetEvent: ev,
          clickOutsideToClose:true,
          fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
        })
        .then(function(answer) {
          $rootScope.status = 'You said the information was "' + answer + '".';
        }, function() {
          $rootScope.status = 'You cancelled the dialog.';
        });
    };

    function DialogController($scope, $mdDialog) {
        $scope.aGym = $rootScope.aGym;
        $scope.hide = function() {
          $mdDialog.hide();
        };

        $scope.cancel = function() {
          $mdDialog.cancel();
        };

        $scope.answer = function(gym) {
            $rootScope.goToMenu(gym);
          $mdDialog.hide(gym);
        };
    }

    /***********---------____ CERRAR SESION ____---------*********/
    $scope.cerrarSesion = function(){
        console.log($cookies.getAll());
        $cookies.remove('GymId', { path: '/' });
        $cookies.remove('nombreGym', { path: '/' });
        $cookies.remove('colorPrimary', { path: '/' });
        $cookies.remove('ColorComplementario', { path: '/' });
        $cookies.remove('usuarioAutenticadoId', { path: '/' });
        $cookies.remove('usuarioAutenticadoNombre', { path: '/' });
        console.log($cookies.getAll());
        $window.location = "/index.html";
    };
}]);
