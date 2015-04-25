'use strict';

/* Applications Controller */

YandexMailApi.controller('YandexMailApiController', function ($scope, $sce, YandexMailApiService) {

  $scope.domains = [];
  $scope.domainsLoading = true;

  $scope.highlight = function(text, search) {
    if (!search) return $sce.trustAsHtml(text);
    return $sce.trustAsHtml(text.replace(new RegExp(search, 'gi'), '<span class="highlight">$&</span>'));
  };

  $scope.domain = null;
  $scope.selectDomain = function(domain) {
    if ($scope.domain != null && $scope.domain.name == domain.name) return;
    $scope.domain = domain;
    domain.queue = 3;
    domain.logo_url = undefined;
    YandexMailApiService.get_domain_registration_status({domain: domain.name}, function(result){ domain.queue = domain.queue - 1; $scope.domain.status = result;});
    YandexMailApiService.get_domain_details({domain: domain.name}, function(result){ domain.queue = domain.queue - 1; $scope.domain.details = result;});
    YandexMailApiService.get_domain_logo({domain: domain.name}, function(result){ domain.queue = domain.queue - 1; $scope.domain.logo_url = result["logo-url"]; });
  };

  function getDomains(page, on_page) {
    if (!on_page) on_page = 20;
    if (!page) page = 1;
    YandexMailApiService.get_domains({page: page, on_page: on_page}, function(result){
      $scope.domains.push.apply($scope.domains, result.domains);
      if ($scope.domains.length < result.total) return getDomains(page + 1);
      $scope.domainsLoading = false;
    });
  }
  getDomains(1);
  
});

