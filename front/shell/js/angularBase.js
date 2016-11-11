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

myApplication.controller('baseCommand', ['$scope', '$http', '$window', '$cookies', '$rootScope', '$mdDialog', '$interval', function($scope, $http, $window, $cookies, $rootScope, $mdDialog, $interval){
    "use strict";

    if (window.location.pathname !== '/' && window.location.pathname !== '/index.html'){
        if ($cookies.get('usuarioAutenticadoId', { path: '/' }) == undefined){
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

    $rootScope.usuarioAutenticadoId = $cookies.get('usuarioAutenticadoId', { path: '/' });
    $rootScope.usuarioAutenticadoNombre =  $cookies.get('usuarioAutenticadoNombre', { path: '/' });
    $rootScope.gimnasioId = parseInt($cookies.get('GymId', { path: '/' }), 10);
    $rootScope.nombreGym = $cookies.get('nombreGym', { path: '/' });
    $rootScope.colorPrincipal = $cookies.get('colorPrimary', { path: '/' });
    $rootScope.colorSecundario = $cookies.get('ColorComplementario', { path: '/' });
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

    try{
        $rootScope.aGym = JSON.parse($cookies.get('gyms', { path: '/' }));
    } catch (e){
        console.log(e);
    }

    $rootScope.setGymRootScope = function(gym){
        $cookies.put('GymId', gym.IdGym, { path: '/' });
        $cookies.put('nombreGym', gym.NombreGimnasio, { path: '/' });
        $cookies.put('colorPrimary', gym.Configuracion.configuracion["0"].ColorFondo, { path: '/' });
        $cookies.put('ColorComplementario', gym.Configuracion.configuracion["0"].ColorComplementario, { path: '/' });

        $rootScope.gimnasioId = gym.IdGym;
        $rootScope.nombreGym = gym.NombreGimnasio;
        $rootScope.colorPrincipal = gym.Configuracion.configuracion["0"].ColorFondo;
        $rootScope.colorSecundario = gym.Configuracion.configuracion["0"].ColorComplementario;
    }

    console.log($cookies.getAll());

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
            $rootScope.responsePositive(ev);
        }, function() {
            //answer negative
        });
    };

    $rootScope.dialogGimnasios = function(ev) {
        $mdDialog.show({
            controller: DialogController,
            templateUrl: '/front/shell/view/dialogGyms.html',
            parent: angular.element(document.body),
            targetEvent: ev,
            clickOutsideToClose:true,
            fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
        })
        .then(function(answer) {
            //make something
        }, function() {
            //another thing
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
    };

    /**************---------____ PROGRESS ____---------************/
    $rootScope.showProgress = false;
    /*$rootScope.determinateValue = 30;
    $interval(function() {

        self.determinateValue += 1;
        if (self.determinateValue > 100) {
          self.determinateValue = 30;
        }

      }, 100);*/

    /**************---------____ CERRAR SESION ____---------************/
    $scope.cerrarSesion = function(){
        console.log($cookies.getAll());
        $cookies.remove('GymId', { path: '/' });
        $cookies.remove('nombreGym', { path: '/' });
        $cookies.remove('colorPrimary', { path: '/' });
        $cookies.remove('ColorComplementario', { path: '/' });
        $cookies.remove('usuarioAutenticadoId', { path: '/' });
        $cookies.remove('usuarioAutenticadoNombre', { path: '/' });
        $cookies.put('gyms', '[]');
        console.log($cookies.getAll());
        $window.location = "/index.html";
    };
}]);
