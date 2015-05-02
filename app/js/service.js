'use strict';

/* Services */
var YandexMailApiService = angular.module('YandexMailApiService', ['ngResource']).config(function ($httpProvider) {
    $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';

    var old_transformResponse = $httpProvider.defaults.transformResponse;
    $httpProvider.defaults.transformResponse = function(data, parser, statusCode) {
        var retVal = old_transformResponse[0].apply(this, arguments);
        if (statusCode == 401) {
          swal({title: "Hoşgeldiniz", text: "Yandex Mail", confirmButtonColor: "#DD6B55", confirmButtonText: "Giriş", closeOnConfirm: false}, function(){ window.location = "api/login"; });
          return undefined;
        }
        if (statusCode != 200) {
          sweetAlert(retVal.code.toString(), retVal.message, "error");
          return undefined;
        }
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
        del_domain: {method:'POST', params: {command: "domain", action: "delete"}, isArray: false},
        get_domain_logo: {method:'POST', params: {command: "domain", action: "check_logo"}, isArray: false},
        get_domain_mailbox: {method:'POST', params: {command: "mailbox", action: "get_list"}, isArray: false},
        get_mailbox_counters: {method:'POST', params: {command: "mailbox", action: "counters"}, isArray: false},
        set_mailbox: {method:'POST', params: {command: "mailbox", action: "edit"}, isArray: false},
        add_mailbox: {method:'POST', params: {command: "mailbox", action: "add"}, isArray: false},
        del_mailbox: {method:'POST', params: {command: "mailbox", action: "del"}, isArray: false},
        logged_user: {method:'GET', params: {command: "logged", action: "user"}, isArray: false}
      });
    }
]);