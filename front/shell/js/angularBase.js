/*jslint white:true*/
/*global angular*/
var myApplication = angular.module('demoGym', ['anguFixedHeaderTable', 'ngTable', 'ngAnimate', 'ngRoute', 'ngCookies', 'ngMaterial']).config(function($mdThemingProvider){
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

    //config
    /*************---- PRODUCCION(Google Cloud) ----**************/
    //$rootScope.SERVER_URL = "http://forza-1355.appspot.com";
    /*************---- DESARROLLO(Hostinger) ----**************/
    $rootScope.SERVER_URL = "http://enformadesarrollo.esy.es";

    $scope.usuarioAutenticadoId = $cookies.get('usuarioAutenticadoId');
    $scope.usuarioAutenticadoNombre =  $cookies.get('usuarioAutenticadoNombre');
    $scope.gimnasioId = parseInt($cookies.get('GymId'), 10);
    $scope.nombreGym = $cookies.get('nombreGym');
    $rootScope.colorPrincipal = $cookies.get('colorPrimary');
    $rootScope.colorSecundario = $cookies.get('ColorComplementario');
    console.log($rootScope.colorPrincipal);
    $rootScope.colorAccent = '00bfa5';

    /*myApplication.config(function($mdThemingProvider){
        var neonRedMap = $mdThemingProvider.extendPalette('indigo', {
            '50': '#00bfa5',
            '100': '#00bfa5',
            '200': '#00bfa5',
            '300': '#00bfa5',
            '400': '#00bfa5',
            '500': '#00bfa5',
            '600': '#00bfa5',
            '700': '#00bfa5',
            '800': '#00bfa5',
            '900': '#00bfa5',
            'A100': '#00bfa5',
            'A200': '#00bfa5',
            'A400': '#00bfa5',
            'A700': '#00bfa5',
            'contrastDefaultColor': 'light'
        });

        // Register the new color palette map with the name <code>neonRed</code>
        $mdThemingProvider.definePalette('neonRed', neonRedMap);

        // Use that theme for the primary intentions
        $mdThemingProvider.theme('default').primaryPalette('neonRed');
    });*/

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

    /*$rootScope.showAlert = function(msg){
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
    };*/

    $rootScope.backToMenu = function(){
        $window.location = "/front/shell/menu.html";
    };

    $rootScope.status = '  ';
    $rootScope.customFullscreen = false;

    $rootScope.showAlert = function(ev) {
    // Appending dialog to document.body to cover sidenav in docs app
    // Modal dialogs should fully cover application
    // to prevent interaction outside of dialog
    $mdDialog.show(
      $mdDialog.alert()
        .parent(angular.element(document.querySelector('#popupContainer')))
        .clickOutsideToClose(true)
        .title('This is an alert title')
        .textContent('You can specify some description text in here.')
        .ariaLabel('Alert Dialog Demo')
        .ok('Got it!')
        .targetEvent(ev)
    );
    };

    $rootScope.showConfirm = function(ev) {
    // Appending dialog to document.body to cover sidenav in docs app
    var confirm = $mdDialog.confirm()
          .title('Would you like to delete your debt?')
          .textContent('All of the banks have agreed to forgive you your debts.')
          .ariaLabel('Lucky day')
          .targetEvent(ev)
          .ok('Please do it!')
          .cancel('Sounds like a scam');

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

    /*$rootScope.aGym = [{IdGym: '1', NombreGimnasio: 'Juarez', Direccion: 'AV. TEZIUTLAN NORTE # 95', Ciudad: 'PUEBLA', Estado: 'PUEBLA', Pais: 'MEXICO', C_Latitud: '19.058065', C_Longitud: '-98.23065', Id_Gimnasio: '2'},
                   {IdGym: '5', NombreGimnasio: 'Animas', Direccion: 'Juan Pablo II #3124', Ciudad: 'PUEBLA', Estado: 'PUEBLA', Pais: 'MEXICO', C_Latitud: '19.058063', C_Longitud: '-98.23063', Id_Gimnasio: '3'}];*/
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
}]);
