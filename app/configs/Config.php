<?php

if(!defined('MCRYPT_RIJNDAEL_256'))
{
    define('MCRYPT_RIJNDAEL_256',0);
}

if(!defined('MCRYPT_MODE_CBC'))
{
    define('MCRYPT_MODE_CBC',0);
}

define ("GENERATE_NEW_TOKEN", 2675);

define ("DEFAULT_ALLOWED_REQUEST_METHOD", "GET");

define ("UNDEFINED", null);

class Config
{
    private static $app = array
    (
        'templates.path' => './app/view',
        'mode' => 'development',
        'debug' => false,
        'log.level' => \Slim\Log::ERROR,
        'log.enabled' => false,
        'cookies.encrypt' => true,
        'cookies.lifetime' => '20 minutes',
        'cookies.path' => '/',
        'cookies.httponly' => true,
        'cookies.secret_key' => 'hehehewoooow',
        'cookies.cipher' => MCRYPT_RIJNDAEL_256,
        'cookies.cipher_mode' => MCRYPT_MODE_CBC,
        'http.version' => '1.1',
        'language' => 'persian',
        'IpLocationApiAddress' => 'http://ip-api.com/json/',
        'encryptKeySize' => 32,
        'encryptSecretKey' => 'bc5b9275bd794ac47581114b66d14d81076',
        'api_key_regenerate_time' => 86400,
        //CROSS DOMAIN REQUEST CONFIGURATION;
        'Cross-Domain-Request' => false,
        'Access-Control-Allow-Origin' => 'http://localhost:9000',
        'Access-Control-Allow-Methods'=>'GET, POST, PUT, DELETE, OPTIONS',
        'Access-Control-Allow-Headers'=>'X-Requested-With, X-HTTP-Method-Override, Content-Type, Accept, Token',
        'Access-Control-Allow-Credentials'=>'true',
        'JSON-Content-Type'=>'application/json; charset=utf-8',
    );
    private static $database = array
    (
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'database' => 'science_access',
        'username' => 'root',
        'password' => 'root',
        'prefix'    => '',
        'charset'   => "utf8",
        'collation' => "utf8_unicode_ci"
    );
    public static function app($key = null)
    {
        self::$app['view'] = new \JsonView();
        self::$app['webDirectory'] = __DIR__.DIRECTORY_SEPARATOR.'../../web/';
        self::$app['rootDirectory'] = __DIR__.DIRECTORY_SEPARATOR.'../../';
        self::$app['uploadStorage'] = __DIR__.DIRECTORY_SEPARATOR.'../../web/uploads/';
        self::$app['languageDirectory'] = __DIR__.DIRECTORY_SEPARATOR.'../languages/';
        self::$app['rulesDirectory'] = __DIR__.DIRECTORY_SEPARATOR.'../rules/';
        self::$app['seedsDirectory'] = __DIR__.DIRECTORY_SEPARATOR.'../seeds/';
        self::$app['fakerAutoloader'] = __DIR__.DIRECTORY_SEPARATOR.'../../vendor/fzaninotto/faker/src/autoload.php';
        if(!is_null($key))
        {
            return self::$app[$key];
        }
        return self::$app;
    }
    public static function database($key = null)
    {
        if(!is_null($key))
        {
            return self::$database[$key];
        }
        return self::$database;
    }
}
