<?php
class Authorize
{

    public static function token ($token = null)
    {
        if(is_null($token))
        {
            $slim = \Slim\Slim::getInstance();
            $token = $slim->apiKey;
        }
        $roles = null;
        $authenticate = Authentication::where('api_key', $token)->first();
        if($authenticate)
        {
            $roles = $authenticate->user()->first()->authorize()->get()->toArray();
        }
        if(isset($slim))
        {
            $slim->authorization = $roles;
        }
        return $roles;
    }

    public static function user ($uri_pattern = null, $roles = null)
    {
        $slim = \Slim\Slim::getInstance();
        if(is_null ($roles))
        {
            $roles = $slim->authorization;
        }
        if(is_null($uri_pattern))
        {
            $uri_pattern = $slim->router->getCurrentRoute()->getPattern();
        }
        if(substr($uri_pattern, -1,1) === '/') {
            $uri_pattern = substr($uri_pattern, 0, -1);
        }
        if(is_null($uri_pattern) || is_null($roles) || !is_array($roles))
        {
            return false;
        }
        $roles = array_column($roles, 'uri_pattern');
        $permitted_array = array_filter($roles, function($element) use($uri_pattern, $slim)
        {
            $elem = reset(explode("|", $element));
            if (substr($element, 0, strlen($elem)) == $uri_pattern)
            {
                $allowed_request_method = DEFAULT_ALLOWED_REQUEST_METHOD;
                if (strpos($element, "|") !== FALSE)
                {
                    $allowed_request_method = end(explode("|", $element));
                }
                if ($allowed_request_method == $slim->request->getMethod())
                {
                    return true;
                }
            }
        });
        if(count($permitted_array))
        {
            return true;
        }
        return false;
    }

}
