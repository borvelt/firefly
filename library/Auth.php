<?php
class Auth
{

    private static $exceptions = ['/users/login', '/users/register'];

    public static function check ($route = null)
    {
        $translator = new Translator();
        $slim = \Slim\Slim::getInstance();
        $uri = $slim->router->getCurrentRoute()->getPattern();

        if (in_array($uri, self::$exceptions))
        {
            return true;
        }
        $checkApiKey = Authenticate::checkApiKey();
        if(!$checkApiKey)
        {
            $slim->redirect = $slim->urlFor('login');
            $slim->responseMessage = $translator->get('user_login_required');
            $slim->responseCode = 301;
            halt_app();
        }
        else if (is_array($checkApiKey) && reset($checkApiKey) == GENERATE_NEW_TOKEN)
        {
            $slim->responseMessage = $translator->get('user.login.re_login');
            $slim->responseCode = 400;
            halt_app();
//            $api_key = Authenticate::generateNewToken();
//            Authentication::create(['api_key'=>$api_key, 'user'=>end($checkApiKey)->id]);
        }
        Authorize::token();
        $permitted = Authorize::user($uri);
        if(!$permitted)
        {
            $slim->responseMessage = $translator->get('user_access_denied');
            $slim->responseCode = 403;
            $slim->render([]);
            halt_app();
        }
    }

}
