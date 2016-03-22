<?php

use Illuminate\Database\Capsule\Manager as Capsule;
#use Illuminate\Events\Dispatcher;
#use Illuminate\Container\Container;
use Codesleeve\Stapler\Stapler as Stapler;

class Bootstrap extends Seeder {
	public static function Eloquent() {
		try {
			$capsule = new Capsule();
			$capsule->addConnection(Config::database());
			#$capsule->setEventDispatcher(new Dispatcher(new Container));
			$capsule->setAsGlobal();
			$capsule->bootEloquent();
			return $capsule;
		} catch (Exception $exception) {
			throw new Exception($exception->getMessage());
		}
	}
	public static function Stapler () {
		try {
			Stapler::boot();
			$config = new Codesleeve\Stapler\Config\NativeConfig;
			Stapler::setConfigInstance($config);
			$config->set('stapler.base_path', __DIR__.DIRECTORY_SEPARATOR.'../../');
			$config->set('filesystem.path',':app_root/:url');
		} catch (Exception $exception) {
			throw new Exception($exception->getMessage());
		}
	}
}
