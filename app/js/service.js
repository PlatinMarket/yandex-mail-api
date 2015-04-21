'use strict';

/* Services */
var YandexMailApiService = angular.module('YandexMailApiService', ['ngResource']).config(function ($httpProvider) {
    $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';

    var old_transformResponse = $httpProvider.defaults.transformResponse;
    $httpProvider.defaults.transformResponse = function(data, parser, statusCode) {
        var retVal = old_transformResponse[0].apply(this, arguments);
        return retVal;
    };
});

YandexMailApiService.factory('YandexMailApiService', ['$resource',
function($resource){
  return $resource('logic/:command/:action', {}, {
    domains: {method:'GET', params: { command: 'domain', action: 'index.php' }, isArray: true},
    add_domain: {method:'POST', params: { command: 'domain', action: 'add.php' }, isArray: false}
  });
}]);