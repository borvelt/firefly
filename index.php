<?php
session_start();
$_SESSION["proxy"] = null;
set_time_limit(0);
$GLOBALS['microtime_start'] = microtime(true);

require __DIR__.DIRECTORY_SEPARATOR.'vendor/autoload.php';

$slim = new \Slim\Slim(Config::app());

$slim->error(function (Exception $e) use ($slim) { error_exception_handler($e); });

$slim->group('/users', 'Auth::check', function () use ($slim)
{
    $slim->POST('/login', function () use ($slim)
    {
        Controller::call('UsersController', 'login', 'Validation', 'Translator');
        $slim->render([]);
    })->name('login');

    $slim->POST('/register', function () use ($slim)
    {
        Controller::call('UsersController', 'register', 'Validation', 'Translator');
        $slim->render([]);
    })->name('register');
});

$slim->GET('/download/:uid', 'Downloader::limitByLink', function ($uid) use ($slim)
{
    $slim->uid = $uid;
    Controller::call('DownloadBookController', 'downloadBook', 'Translator');
    $slim->render([]);
})->name('downloadBookByUID');

$slim->group('/books', 'Auth::check', function () use ($slim)
{
    $slim->GET('/by-link', function () use ($slim)
    {
        Controller::call('BooksController', 'getBookByLink');
        $slim->render([]);
    })->name('getBookByLink');

    $slim->GET('/downloaded', function () use ($slim)
    {
        Controller::call('BooksController', 'downloadedBooks');
        $slim->render([]);
    })->name('getReport');

    $slim->POST('/report', function () use ($slim)
    {
        Controller::call('BooksController', 'reportBook');
        $slim->render([]);
    })->name('reportBook');

    $slim->POST('/by-link', function () use ($slim)
    {
        Controller::call('BooksController', 'getBookByLinkNeedCaptcha');
        $slim->render([]);
    })->name('getBookByLinkNeedCaptcha');

    $slim->POST ('/search', function () use ($slim) {
        Controller::call('BooksController', 'searchBook');
        $slim->render([]);
    })->name('searchBook');
});

$slim->run();

$GLOBALS['microtime_end'] = microtime(true);
