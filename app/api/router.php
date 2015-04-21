<?php



Platin\Util\Router::route('/test', function($App){
  
  
  
});

Platin\Util\Router::route('/', function(){
  header("Content-Type: text/plain");
  echo "Yandex Mail Api";
});

Platin\Util\Router::route('/(.*?)', function($App, $file = null){
  pr($_SERVER);
  throw new Platin\Exception\NotFoundException($file);
});



