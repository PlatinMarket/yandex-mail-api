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