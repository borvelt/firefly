<?php

define ("DEBUG", true);
define ("TRACE_BACK", true);
define ("ERROR_STRING", 'با عرض پوزش اختلالی در سیستم به وجود آمده است، خطای رخ داده شده به اطلاع مدیر خواهد رسید.');

error_reporting(0);
register_shutdown_function("error_exception_handler");
set_exception_handler("error_exception_handler");
set_error_handler("exception_error_handler");

function error_exception_handler($exception=null)
{
  $response = [];
  $error = error_get_last();
  if(!is_null($error))
  {
    $response = $error;
  }
  if(!is_null($exception))
  {
    $response =
    [
        'type' => method_exists($exception, 'getType') ? $exception->getType() : '0',
        'message' => $exception->getMessage(),
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => TRACE_BACK ? $exception->getTrace() : null,
    ];
  }
  if(count($response))
  {
    $file=fopen (__DIR__.DIRECTORY_SEPARATOR."../app/logs/".strtotime(date("Y-m-d")).".log", "a+");
    if(isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    } else {
        $ip = "From CLI";
    }
    $log="[".date("D, d M y h:i:s O")."][ERROR] ".
    $response['message']." file:".$response['file']." line:".$response['line']." IP:".$ip."\n";
    fwrite($file, $log);
    fclose($file);
    $body = $response;
    $status = 'error';
    $code = 503;
    $message = ERROR_STRING;
    $redirect = null;
    http_response_code(503);
    header('Content-Type:application/json; charset=utf-8');
    if(defined('DEBUG') && DEBUG)
    {
      echo json_encode(compact("body", "message", "status", "code","redirect"));
    }
    else
    {
      echo json_encode(compact("message", "status", "code","redirect"));
    }
  }
  exit;
}

function exception_error_handler($severity, $message, $file, $line)
{
  throw new ErrorException($message, 0, $severity, $file, $line);
}
