<?php

use Phalcon\Mvc\Application;
use Phalcon\DI\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\Url;
use Phalcon\Mvc\View;

error_reporting(E_ALL);

try {
	require __DIR__ . '/../app/config/config.php';

	$loader = new Loader();
	$loader->registerDirs(
		array(
			__DIR__ . $config->phalcon->controllersDir,
			__DIR__ . $config->phalcon->libraryDir,
			__DIR__ . $config->phalcon->modelsDir
		)
	)->register();

	$di = new FactoryDefault();

	$di->set('router', function () {
		require __DIR__ . '/../app/config/routes.php';
		return $router;
	});

	$di->set('url', function () use ($config) {
		$url = new Url();
		$url->setBaseUri($config->phalcon->baseUri);
		return $url;
	});

	$di->set('view', function () use ($config) {
		$view = new View();
		$view->setViewsDir(__DIR__ . $config->phalcon->viewsDir);
		return $view;
	});

	$di->set('viewCache', function () {

		//Cache data for one day by default
		$frontCache = new Phalcon\Cache\Frontend\Output(array(
			"lifetime" => 2592000
		));

		//File backend settings
		$cache = new Phalcon\Cache\Backend\File($frontCache, array(
			"cacheDir" => __DIR__ . "/../app/cache/",
			"prefix" => "php"
		));

		return $cache;
	});

	$di->set('db', function () use ($config) {
		return new \Phalcon\Db\Adapter\Pdo\Mysql(array(
			'host' => $config->database->host,
			'username' => $config->database->username,
			'password' => $config->database->password,
			'dbname' => $config->database->dbname,
			'options' => [PDO::ATTR_CASE => PDO::CASE_LOWER,
			              PDO::ATTR_PERSISTENT => TRUE,
			              PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]
		));
	});

	$di->set('session', function () {
		$session = new Phalcon\Session\Adapter\Files();
		$session->start();
		return $session;
	});

	$di->set('flash', function () {
		$flash = new Phalcon\Flash\Direct(array(
			'error' => 'alert alert-danger',
			'success' => 'alert alert-success',
			'notice' => 'alert alert-info',
			'warning' => 'alert alert-warning',
		));
		return $flash;
	});

	$application = new Application();
	$application->setDI($di);
	echo $application->handle()->getContent();

} catch (Phalcon\Exception $e) {
	echo $e->getMessage();
} catch (PDOException $e) {
	echo $e->getMessage();
}
