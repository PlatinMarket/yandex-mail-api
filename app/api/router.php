<?php


// Yandex Mail Interface
Platin\Util\Router::route('/(.*?)/(.*?)', function($App, $command, $action){

  $action = substr($action, -1) == '/' ? substr($action, 0, strlen($action) - 1) : $action;
  $command = ucfirst(strtolower($command));
  $command_class = 'YandexMail\\' . $command;
  if (strrpos($action, '/') !== false || !class_exists($command_class)) return false;
  $command_class = new $command_class($App->Configure->read("YandexMail.Token"));
  if (!method_exists($command_class, $action)) return false;
  
  header('Content-Type: application/json');

  $params = json_decode(file_get_contents('php://input'), true);
  $params = is_array($params) ? $params : array();

  try {
    $results = @call_user_method_array($action, $command_class, $params);
    echo json_encode($results);
  } catch (Exception $e) {
    json_encode(array('success' => 'error', 'error' => $e->getMessage()));
  }

});

// Default Page
Platin\Util\Router::route('/', function(){
  header("Content-Type: text/plain");
  echo "Yandex Mail Api";
});

Platin\Util\Router::route('/(.*?)', function($App, $file = null){
  throw new Platin\Exception\NotFoundException($file);
});



