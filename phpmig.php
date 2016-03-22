<?php
use \Phpmig\Adapter;
use \Pimple as Pimple;
use \Illuminate\Database\Capsule\Manager as Capsule;
$container = new Pimple();
$container['db'] = $container->share(function() {
    $dbh = new PDO(Config::database('driver').':dbname='.Config::database('database').';host='.Config::database('host'),Config::database('username'),Config::database('password'));
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
});
$container['schema'] = $container->share(function() {
    $capsule = new Capsule;
    $capsule->addConnection(Config::database());
    $capsule->setAsGlobal();
    return Capsule::schema();
});
$container['phpmig.adapter'] = $container->share(function() use ($container) {
    return new Adapter\PDO\Sql($container['db'], 'migrations');
});
$container['phpmig.migrations_path'] = __DIR__ . DIRECTORY_SEPARATOR . 'app/migrations';
$container['phpmig.migrations_template_path'] = __DIR__ . DIRECTORY_SEPARATOR . 'app/migrations/template/migration.php';
return $container;
