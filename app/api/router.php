<?php

PlatinBox\OpenId::SetOpenId(Platin\Lib\Configure::read("Openid.Server"));
PlatinBox\OpenId::setRequiredMember(Platin\Lib\Configure::read("Openid.MemberOf"));

// Login Router
Platin\Util\Router::route('/login', function($App){
  if (PlatinBox\OpenId::login() === false || PlatinBox\OpenId::logged() === true) header('Location: ../index.html');
});

// Logout Router
Platin\Util\Router::route('/logout', function($App){
  PlatinBox\OpenId::logout();
  header('Location: ../index.html');
});

// Logout Router
Platin\Util\Router::route('/logged/user', function($App){
  header('Content-Type: application/json');
  if (strpos($App->Request->clientIp(), Platin\Lib\Configure::read("Security.Allow")) !== false || $App->Request->fromLocal()) {
    echo json_encode(array("email" => "", "fullname" => "Güvenilir Kullanıcı", "memberof" => "", "nickname" => "trusted_user", "uid" => "trusted_user"));
  } else {
    echo json_encode(PlatinBox\OpenId::user());
  }
});

// Logout Router
Platin\Util\Router::route('/(.*?)', function($App){
  if (!Platin\Lib\Configure::read("Security.Allow")) Platin\Lib\Configure::write("Security.Allow", "@@@");
  if ($App->Request->fromLocal()) return false;
  if (PlatinBox\OpenId::logged() || strpos($App->Request->clientIp(), Platin\Lib\Configure::read("Security.Allow")) !== false) return false;

  http_response_code(401);

  if ($App->Request->isAjax()){
    header('Content-Type: application/json');
    echo json_encode(array("code" => 401, "message" => "Unauthorized"));
    return;
  }

  header("Content-Type: text/plain");
  echo "401 - Unauthorized";
});

// Yandex TOKEN List
Platin\Util\Router::route("/token/list", function($app) {
	header("Content-Type: application/json");
	echo json_encode($app->Configure->read("YandexMail.Token"));
	return;
});


// Yandex Mail Interface
Platin\Util\Router::route('/(.*?)/(.*?)', function($App, $command, $action){
  $action = substr($action, -1) == '/' ? substr($action, 0, strlen($action) - 1) : $action;
  $action = strtolower($action);
  $command = ucfirst(strtolower($command));
  $command_class = 'YandexMail\\' . $command;
  if (strrpos($action, '/') !== false || !class_exists($command_class)) return false;
  $params = json_decode(file_get_contents('php://input'), true);
  $params = is_array($params) ? $params : array();

  if(!isset($params["token"])) $params["token"] = "TODO_DEFAULT_TOKEN";

  $command_class = new $command_class($params["token"]);
  unset($params["token"]);

  if (!method_exists($command_class, $action)) return false;

  header('Content-Type: application/json');

  if (strtolower($command) == "domain" && strtolower($action) == "delete" && isset($params['domain']) && $params['domain'] == $App->Configure->read("YandexMail.Base")) {
    echo json_encode(array('success' => 'error', 'error' => 'Base domain cannot be delete!'));
    return;
  }

  try {
    $results = @call_user_method_array($action, $command_class, $params);
    echo json_encode($results);
  } catch (Exception $e) {
    echo json_encode(array('success' => 'error', 'error' => $e->getMessage()));
  }

});

// Default Page
Platin\Util\Router::route('/', function(){
  header("Content-Type: text/plain");
  echo "Yandex Mail Api";
  return "asdasd";
});

Platin\Util\Router::route('.*', function($App, $file = null){
  throw new Platin\Exception\NotFoundException($file);
});



