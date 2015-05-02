<?php

PlatinBox\OpenId::SetOpenId("https://openid.platinbox.org");
PlatinBox\OpenId::setRequiredMember("Super Admin");

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
  echo json_encode(PlatinBox\OpenId::user());
});

// Logout Router
Platin\Util\Router::route('/(.*?)', function($App){
  if (PlatinBox\OpenId::logged()) return false; 
  
  http_response_code(401);

  if ($App->Request->isAjax()){
    header('Content-Type: application/json');
    echo json_encode(
      array(
        "code" => 401,
        "message" => "Unauthorized"
      )
    );
    return;
  }
  
  header("Content-Type: text/plain");
  echo "401 - Unauthorized";
});

// Yandex Mail Interface
Platin\Util\Router::route('/(.*?)/(.*?)', function($App, $command, $action){

  $action = substr($action, -1) == '/' ? substr($action, 0, strlen($action) - 1) : $action;
  $action = strtolower($action);
  $command = ucfirst(strtolower($command));
  $command_class = 'YandexMail\\' . $command;
  if (strrpos($action, '/') !== false || !class_exists($command_class)) return false;
  $command_class = new $command_class($App->Configure->read("YandexMail.Token"));
  if (!method_exists($command_class, $action)) return false;
  
  header('Content-Type: application/json');

  $params = json_decode(file_get_contents('php://input'), true);
  $params = is_array($params) ? $params : array();

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
  echo "Yandex Mail Apia";
  return "asdasd";
});

Platin\Util\Router::route('.*', function($App, $file = null){
  throw new Platin\Exception\NotFoundException($file);
});



