<?php

$config = new Phalcon\Config(array(
	'database' => array(
		'adapter' => 'mysql',
		'host' => 'localhost',
		'username' => 'excel1',
		'password' => 'mypass',
		'dbname' => 'invo',
	),
	'phalcon' => array(
		'controllersDir' => '/../app/controllers/',
		'modelsDir' => '/../app/models/',
		'libraryDir' => '/../app/library/',
		'viewsDir' => '/../app/views/',
		'forms' => '/../app/forms/',
		'baseUri' => '/'
	),
	/*'models' => array(
		'metadata' => array(
			'adapter' => 'Apc',
			'lifetime' => 86400
		)
	)*/
));