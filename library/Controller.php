<?php
class Controller {

  public static function call()
  {
    $params = [];
    $params[] = \Slim\Slim::getInstance();
    $args = func_get_args();
    $controller = array_shift($args);
    $method = array_shift($args);
    foreach ($args as $dependency) {
      $params[] = new $dependency;
    }
    $response = call_user_func_array([$controller, $method], $params);
    return $response;
  }
}
