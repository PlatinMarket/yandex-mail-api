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