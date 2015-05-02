'use strict';

/* App Module */
var YandexMailApi = angular.module('YandexMailApi', [
  'YandexMailApiService'
]);

moment.locale('tr');

swal.setDefaults({
  confirmButtonText: 'Tamam',
  cancelButtonText: 'İptal'
});

YandexMailApi.directive('errSrc', function() {
  return {
    link: function(scope, element, attrs) {
      element.bind('error', function() {
        if (attrs.src != attrs.errSrc) {
          attrs.$set('src', attrs.errSrc);
        }
      });
    }
  }
});

YandexMailApi.directive('ngEnter', function () {
    return function (scope, element, attrs) {
        element.bind("keydown keypress", function (event) {
            if(event.which === 13) {
                scope.$apply(function (){
                    scope.$eval(attrs.ngEnter);
                });
                event.preventDefault();
            }
        });
    };
});

YandexMailApi.directive('scrollIf', function () {
  return function(scope, element, attrs) {
    scope.$watch(attrs.scrollIf, function(value) {
      if (value) {
        var pos = ($(element).position().top - $(element).parent().position().top) + $(element).parent().scrollTop();
        $(element).parent().animate({
            scrollTop : pos
        }, 500);
      }
    });
  }
});