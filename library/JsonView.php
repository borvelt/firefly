<?php
class JsonView extends \Slim\View
{
  public function render($data = null, $status = null, $message = null)
  {
      $slim = \Slim\Slim::getInstance();
      if (isJson($slim->responseBody)) {
          $slim->responseBody = json_decode($slim->responseBody,true);
      } else {
          $slim->responseBody = (array) $slim->responseBody;
      }
      if(is_array($data)) {
          $slim->responseBody = array_merge((array)$slim->responseBody, $data);
      }
      $this->appendData($slim->responseBody);
      $all_data = $this->data->all();
      array_shift($all_data);
      $slim->responseBody = $all_data;
      if(!is_null($status))
      {
        $slim->responseCode = $status;
      }
      else if(!isset($slim->responseCode))
      {
        $slim->responseCode = 200;
      }
      if(!isset($slim->responseStatus))
      {
        if(substr($slim->responseCode, 0, 1) == '2')
        {
          $slim->responseStatus = 'ok';
        }
        else
        {
          $slim->responseStatus = 'error';
        }
      }
      if(!is_null($message))
      {
        $slim->responseMessage = $message;
      }

      $body = $slim->responseBody;
      $message = $slim->responseMessage;
      $status = $slim->responseStatus;
      $code = $slim->responseCode;
      $redirect = $slim->redirect;

      if(Config::app('Cross-Domain-Request'))
      {
        $slim->response->headers->set('Access-Control-Allow-Origin', Config::app('Access-Control-Allow-Origin'));
        $slim->response->headers->set('Access-Control-Allow-Methods', Config::app('Access-Control-Allow-Methods'));
        $slim->response->headers->set('Access-Control-Allow-Headers', Config::app('Access-Control-Allow-Headers'));
        $slim->response->headers->set('Access-Control-Allow-Credentials', Config::app('Access-Control-Allow-Credentials'));
      }

      if(isset($slim->apiKey) && !is_null($slim->apiKey))
      {
        $slim->response->headers->set('Api-Key', $slim->apiKey);
      }

      $slim->response->setStatus($code);
      $slim->response->headers->set('Content-Type', Config::app('JSON-Content-Type'));
      $json_response = json_encode(compact("body", "message", "status", "code","redirect"));
      $slim->response->setBody($json_response);
  }
}
