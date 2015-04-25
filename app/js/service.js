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
  return $resource('api/:command/:action', {command: "@command", action: "@action"}, {
    register_domain: {method:'POST', params: {command: "domain", action: "register"}, isArray: false},
    get_domains: {method:'POST', params: {command: "domain", action: "get_list"}, isArray: false},
    get_domain_registration_status: {method:'POST', params: {command: "domain", action: "status"}, isArray: false},
    get_domain_details: {method:'POST', params: {command: "domain", action: "details"}, isArray: false},
    get_domain_logo: {method:'POST', params: {command: "domain", action: "check_logo"}, isArray: false},
    get_domain_mailbox: {method:'POST', params: {command: "email", action: "list"}, isArray: false}
  });
}]);