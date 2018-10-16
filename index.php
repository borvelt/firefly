<?php
session_start();
$_SESSION["proxy"] = null;
set_time_limit(0);
$GLOBALS['microtime_start'] = microtime(true);

require __DIR__ . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

$slim = new \Slim\Slim(Config::app());

$slim->error(function (Exception $e) use ($slim) {error_exception_handler($e);});

$slim->group('/users', 'Auth::check', function () use ($slim) {
    $slim->POST('/login', function () use ($slim) {
        Controller::call('UsersController', 'login', 'Validation', 'Translator');
        $slim->render([]);
    })->name('login');

    $slim->POST('/register', function () use ($slim) {
        Controller::call('UsersController', 'register', 'Validation', 'Translator');
        $slim->render([]);
    })->name('register');
});

$slim->run();

$GLOBALS['microtime_end'] = microtime(true);
