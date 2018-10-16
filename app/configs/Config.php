<?php
if (!defined('MCRYPT_RIJNDAEL_256')) {
    define('MCRYPT_RIJNDAEL_256', 0);
}

if (!defined('MCRYPT_MODE_CBC')) {
    define('MCRYPT_MODE_CBC', 0);
}

define("GENERATE_NEW_TOKEN", 2675);

define("DEFAULT_ALLOWED_REQUEST_METHOD", "GET");

define("UNDEFINED", null);

class Config
{
    private static $app = array
        (
        'templates.path' => './app/views',
        'mode' => 'development',
        'debug' => false,
        'log.level' => \Slim\Log::ERROR,
        'log.enabled' => false,
        'cookies.encrypt' => true,
        'cookies.lifetime' => '20 minutes',
        'cookies.path' => '/',
        'cookies.httponly' => true,
        'cookies.secret_key' => '94ac475mj81cy114b6',
        'cookies.cipher' => MCRYPT_RIJNDAEL_256,
        'cookies.cipher_mode' => MCRYPT_MODE_CBC,
        'http.version' => '1.1',
        'language' => 'english',
        'IpLocationApiAddress' => 'http://ip-api.com/json/',
        'cipher' => 'aes-128-cbc',
        'encryptSecretKey' => 'f080dd2e74286c45953d934380ebecbaf7708a9dd5580c1b70837106c5380915',
        'api_key_regenerate_time' => 60000,
        //CROSS DOMAIN REQUEST CONFIGURATION;
        'Cross-Domain-Request' => false,
        'Access-Control-Allow-Origin' => 'http://localhost:2000',
        'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
        'Access-Control-Allow-Headers' => 'X-Requested-With, X-HTTP-Method-Override, Content-Type, Accept, Token',
        'Access-Control-Allow-Credentials' => 'true',
        'JSON-Content-Type' => 'application/json; charset=utf-8',
    );

    private static $database = [
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'database' => '',
        'username' => '',
        'password' => '',
        'prefix' => '',
        'charset' => "utf8",
        'collation' => "utf8_unicode_ci",
    ];

    public static function app($key = null)
    {
        self::$app['view'] = new \JsonView();
        self::$app['webDirectory'] = __DIR__ . DIRECTORY_SEPARATOR . '../../web/';
        self::$app['rootDirectory'] = __DIR__ . DIRECTORY_SEPARATOR . '../../';
        self::$app['uploadStorage'] = __DIR__ . DIRECTORY_SEPARATOR . '../../web/uploads/';
        self::$app['languageDirectory'] = __DIR__ . DIRECTORY_SEPARATOR . '../languages/';
        self::$app['rulesDirectory'] = __DIR__ . DIRECTORY_SEPARATOR . '../rules/';
        self::$app['seedsDirectory'] = __DIR__ . DIRECTORY_SEPARATOR . '../seeds/';
        self::$app['fakerAutoloader'] = __DIR__ . DIRECTORY_SEPARATOR . '../../vendor/fzaninotto/faker/src/autoload.php';
        if (!is_null($key)) {
            return self::$app[$key];
        }
        return self::$app;
    }
    public static function database($key = null)
    {
        self::$database = require __DIR__ . DIRECTORY_SEPARATOR . "Database.php";
        if (!is_null($key)) {
            return self::$database[$key];
        }
        return self::$database;
    }
}
