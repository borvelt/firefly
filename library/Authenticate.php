<?php
class Authenticate
{

    public static function validateCredential ($email, $password)
    {
        $user = User::whereEmail($email)->first();
        if(!$user)
        {
            return false;
        }
        return password_verify($password, $user->password);
    }

    public static function checkApiKey ($api_key = null)
    {
        $slim = \Slim\Slim::getInstance();
        if(is_null($api_key))
        {
            $api_key = $slim->request->headers->get('Api-Key');
        }
        if(is_null($api_key))
        {
            $api_key = $slim->apiKey;
        }
        if(is_null($api_key))
        {
            return false;
        }
        $authentication = Authentication::where('api_key', $api_key)->first();
        if(!$authentication || $authentication->expired)
        {
            return false;
        }
        list($ip, $time) = explode('|', decrypt($api_key));
        $regenerateTime = Config::app('api_key_regenerate_time');
        if($slim->request->getIp() != $ip)
        {
            return false;
        }
        if((time()-$time) > $regenerateTime)
        {
            $authentication->expired = true;
            $authentication->save();
            return [GENERATE_NEW_TOKEN, $authentication->user()->first()];
        }
        if(isset($slim))
        {
            $slim->apiKey = $api_key;
        }
        return true;
    }

    public static function generateNewToken ($ip = null)
    {
        if(is_null($ip))
        {
            $slim = \Slim\Slim::getInstance();
            $ip = $slim->request->getIp();
        }
        if(is_null($ip))
        {
            return null;
        }
        $time = time();
        $token_string = $ip.'|'.$time;
        $api_key = encrypt($token_string);
        if(isset($slim))
        {
            $slim->apiKey = $api_key;
        }
        return $api_key;
    }

}
