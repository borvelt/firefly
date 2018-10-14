<?php
function isJson($string)
{
    if (!is_string($string)) {
        return false;
    }
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}
function curl_get_file_size($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    $data = curl_exec($ch);
    $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
    curl_close($ch);
    return (round($size / 1024 / 1024) . 'MB');
}
function get_tag($xml)
{
    $tag_regex = '/<file[^>]*>(.*?)<\\/file>/si';
    preg_match($tag_regex, $xml, $matches);
    return $matches[1];
}
function arrayToObject($array, $reverse = false)
{
    return json_decode(json_encode($array), $reverse);
}
function massAssignment(&$object, array $inputs, &$user = null)
{
    foreach ($inputs as $key => $value) {
        if ($key == 'password') {
            $object->$key = password_hash($value, PASSWORD_DEFAULT);
        } else if ($key != 'confirmation_password') {
            $object->$key = $value;
        }
    }
    $user = clone $object;
    return $user;
}

function getRequest($url)
{
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $url,
    ]);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}

function IpToState($ip)
{
    if (is_null($ip)) {
        $ip = $_SERVER['SERVER_ADDR'];
    } else {
        $ip = '151.246.165.171';
    }
    $api_response_json = getRequest(Config::app('IpLocationApiAddress') . $ip);
    $api_response = json_decode($api_response_json, true);
    $state = strtolower(end(explode('/', $api_response['timezone'])));
    return $state;
}

function encrypt($input)
{
    $mcrypt = new Mcrypt();
    return $mcrypt->encrypt($input);
}

function decrypt($encrypted)
{
    $mcrypt = new Mcrypt();
    return $mcrypt->decrypt($encrypted);
}

function halt_app($code = null, $headers = null, $body = null)
{
    $slim = \Slim\Slim::getInstance();
    $slim->view()->render([], $code);
    list($slim_code, $slim_headers, $slim_body) = $slim->response->finalize();
    if (is_null($code)) {
        $code = $slim_code;
    }
    if (is_null($headers)) {
        $headers = $slim_headers;
    }
    if (is_null($body)) {
        $body = $slim_body;
    }
    http_response_code($code);
    foreach ($headers as $name => $value) {
        $hValues = explode("\n", $value);
        foreach ($hValues as $hVal) {
            header("$name: $hVal", false);
        }
    }
    ob_clean();
    if (!$slim->request->isHead()) {
        if (isJson($body)) {
            echo $body;
        } else {
            echo json_encode(['body' => $body]);
        }
    }
    exit;
}
