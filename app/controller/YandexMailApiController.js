'use strict';

/* Applications Controller */

YandexMailApi.controller('YandexMailApiController', function ($scope, $sce, YandexMailApiService) {

  $scope.domains = [];
  $scope.domainsLoading = true;
  $scope.loginState = false;
  $scope.user = YandexMailApiService.logged_user();

  $scope.highlight = function(text, search) {
    if (!search) return $sce.trustAsHtml(text);
    return $sce.trustAsHtml(text.replace(new RegExp(search, 'gi'), '<span class="highlight">$&</span>'));
  };

  $scope.domain = null;
  $scope.selectDomain = function(domain) {
    if ($scope.domain != null && $scope.domain.name == domain.name) return;
    if ($scope.domain != null) $scope.domain.selected = false;
    domain.selected = true;
    $scope.domain = domain;
    $scope.account = null;
    $scope.domain.accounts = [];
    $scope.domain.accountsLoading = true;
    domain.queue = 2;
    YandexMailApiService.get_domain_registration_status({domain: domain.name}, function(result){ domain.queue = domain.queue - 1; $scope.domain.remote_status = result;});
    YandexMailApiService.get_domain_details({domain: domain.name}, function(result){ domain.queue = domain.queue - 1; $scope.domain.remote_details = result;});
    if (domain.stage != "added" || domain.status != "added") return;
    getMailbox(1, domain.name);
  };

  $scope.account = null;
  $scope.selectAccount = function(account){
    $scope.pass_new = "";
    $scope.pass_new_re = "";
    $scope.login_name = "";
    if ($scope.account != null && $scope.account.login == account.login) return;
    $scope.account = account;
    YandexMailApiService.get_mailbox_counters({domain: $scope.domain.name, login: account.login, uid: account.uid}, function(result){ $scope.account.counters = result;});
  };

  $scope.login_name = "";
  $scope.addAccount = function(domain){
    if (!$scope.isValidPasswords() || !validateEmail($scope.login_name + "@" + domain.name) || $scope.login_name.trim() == "" || _.contains(_.pluck($scope.domain.accounts, 'login'), $scope.login_name + "@" + domain.name)) return;
    $scope.domain.accountsLoading = true;
    YandexMailApiService.add_mailbox({domain: domain.name, login: $scope.login_name, password: $scope.pass_new}, function(result){ 
      $scope.domain.accountsLoading = false;
      if (result.success != "ok") {
        sweetAlert("Error", ParseError(result.error), "error");
        $scope.domainAdding = false;
        return;
      }
      $scope.pass_new = "";
      $scope.pass_new_re = "";
      $scope.login_name = "";
      var account = new Account(result);
      $scope.domain.accounts.push(account);
      $scope.account = null;
      domain["emails-count"] = domain["emails-count"] + 1;
    });
  };

  $scope.changeAccountState = function(account, new_state) {
    account.changing_state = true;
    YandexMailApiService.set_mailbox({domain: $scope.domain.name, uid: account.uid, params: {enabled: new_state}}, function(result){
      account.changing_state = false;
      if (result.success != "ok") {
        sweetAlert("Error", ParseError(result.error), "error");
        $scope.domainAdding = false;
        return;
      }
      for (var key in result.account) account[key] = result.account[key];
    });
  };

  $scope.logout = function(){
    window.location = "api/logout";
  };

  function getMailbox(page, domain, on_page){
    if (!on_page) on_page = 20;
    if (!page) page = 1;
    YandexMailApiService.get_domain_mailbox({domain: domain, page: page, on_page: on_page}, function(result){
      $scope.domain.accounts.push.apply($scope.domain.accounts, result.accounts);
      if ($scope.domain.accounts.length < result.total) return getMailbox(page + 1, domain);
      $scope.domain.accountsLoading = false;
    });
  }

  $scope.new_domain = "";
  $scope.domainAdding = false;
  $scope.register_domain = function(){
    if (!$scope.isValidDomain() || $scope.domainAdding) return;
    $scope.domainAdding = true;
    $scope.domainsLoading = true;
    YandexMailApiService.register_domain({domain: $scope.new_domain}, function(result){
      $scope.domainsLoading = false;
      if (result.success != "ok") {
        sweetAlert("Error", ParseError(result.error), "error");
        $scope.domainAdding = false;
        return;
      }
      var domain = new Domain(result);
      $scope.domains.push(domain);
      $scope.selectDomain(domain);
      $scope.domainAdding = false;
      $scope.new_domain = "";
      $scope.active_domain_name = domain.name;
    });
  };

  $scope.deleteDomain = function(domain){
    var index = $scope.domains.indexOf(domain);
    if (index == -1) return;
    swal({title: "Emin misiniz?", text: "Domain \'" + domain.name + "\' tamamen silinecek!", type: "warning", showCancelButton: true, confirmButtonColor: "#DD6B55", confirmButtonText: "Evet, sil!", closeOnConfirm: false}, function(){
      $scope.domainsLoading = true;
      var $button = $(".confirm.btn.btn-lg.btn-primary");
      $button.attr("disabled", "disabled");
      
      YandexMailApiService.del_domain({domain: domain.name}, function(result){
        $button.removeAttr("disabled");

        $scope.domainsLoading = false;
        if (result.success != "ok") {
          sweetAlert("Error", ParseError(result.error), "error");
          return;
        }
        swal({title: "Silindi!", text: "Domain \'" + domain.name + "\' başarıyla silindi!", type: "success", confirmButtonText: "Tamam"}); 
        if ($scope.domain.name == domain.name) $scope.removeDomain();
        $scope.domains.splice(index, 1);
      });
    });
  };

  $scope.deleteAccount = function(account){
    var index = $scope.domain.accounts.indexOf(account);
    if (index == -1) return;
    swal({title: "Emin misiniz?", text: "Account \'" + account.login + "\' tamamen silinecek!", type: "warning", showCancelButton: true, confirmButtonColor: "#DD6B55", confirmButtonText: "Evet, sil!", closeOnConfirm: false}, function(){
      $scope.domain.accountsLoading = true;
      var $button = $(".confirm.btn.btn-lg.btn-primary");
      $button.attr("disabled", "disabled");
      
      YandexMailApiService.del_mailbox({domain: $scope.domain.name, uid: account.uid}, function(result){
        $button.removeAttr("disabled");
        $scope.domain.accountsLoading = false;
        if (result.success != "ok") {
          sweetAlert("Error", ParseError(result.error), "error");
          return;
        }
        swal({title: "Silindi!", text: "Domain \'" + account.login + "\' başarıyla silindi!", type: "success", confirmButtonText: "Tamam"}); 
        if ($scope.account && $scope.account.login == account.login) {
          $scope.account = undefined;
          $scope.pass_new = "";
          $scope.pass_new_re = "";
        }
        $scope.domain.accounts.splice(index, 1);
        $scope.domain["emails-count"] = $scope.domain["emails-count"] - 1;
      });
    });
  };

  $scope.removeDomain = function() {
    $scope.domain = undefined;
    $scope.account = undefined;
    $scope.pass_new = "";
    $scope.pass_new_re = "";
  };

  $scope.changeAccountPassword = function(account){
    if (!$scope.isValidPasswords()) return;
    account.changing_password = true;
    YandexMailApiService.set_mailbox({domain: $scope.domain.name, uid: account.uid, params: {password: $scope.pass_new}}, function(result){
      account.changing_password = false;
      if (result.success != "ok") {
        sweetAlert("Error", ParseError(result.error), "error");
        $scope.domainAdding = false;
        return;
      }
      for (var key in result.account) account[key] = result.account[key];
      sweetAlert("Başarılı", "Şifre başarıyla değiştirildi", "success");
      $scope.pass_new = "";
      $scope.pass_new_re = "";
      $scope.account = null;
    });
  };

  $scope.pass_new = "";
  $scope.pass_new_re = "";
  $scope.isValidPasswords = function() {
    return $scope.pass_new.trim() != "" && $scope.pass_new_re.trim() != "" && $scope.pass_new == $scope.pass_new_re;
  };

  $scope.isValidDomain = function() { 
    var domain = $scope.new_domain;
    var re = new RegExp(/^((?:(?:(?:\w[\.\-\+]?)*)\w)+)((?:(?:(?:\w[\.\-\+]?){0,62})\w)+)\.(\w{2,6})$/); 
    return domain.match(re) && !_.contains(_.pluck($scope.domains, 'name'), domain);
  };

  function validateEmail(email) {
    var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    return re.test(email);
  }

  function getDomains(page, on_page) {
    if (!on_page) on_page = 20;
    if (!page) page = 1;
    YandexMailApiService.get_domains({page: page, on_page: on_page}, function(result){
      $scope.loginState = true;
      $scope.domains.push.apply($scope.domains, result.domains);
      if ($scope.domains.length < result.total) return getDomains(page + 1);
      $scope.domainsLoading = false;
    });
  }
  getDomains(1);

  function Domain(register_response) {
    this.aliases = [];
    this["emails-count"] = 0;
    this["emails-max-count"] = 1000;
    this.logo_enabled = false;
    this.logo_url = "";
    this.master_admin = false;
    this.name = register_response.domain;
    this.nsdelegated = false;
    this.stage = register_response.stage ? register_response.stage : "owner-check";
    this.status = register_response.status ? register_response.status : "domain-activate";
  }

  function Account(register_response) {
    this.aliases = [];
    this.birth_date = "";
    this.enabled = "yes";
    this.fio = "";
    this.fname = "";
    this.hintq = "";
    this.iname = "";
    this.login = register_response.login;
    this.maillist = "no";
    this.ready = "yes";
    this.sex = 0;
    this.uid = register_response.uid;
  }

  function ParseError(errStr) {
    switch (errStr) {
      case "domains_limit":
        return "Domain limit reached";
        break;
      default:
        return "Undefined error: \"" + errStr + "\"";
    }
  }
  
});

