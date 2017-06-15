<?php

$router = new Phalcon\Mvc\Router();

$router->add("/news/{year:[0-9]+}/{month:[0-9]+}/{title:[a-zA-Z0-9\-]+}", array(
    'controller' => 'news',
    'action' => 'show'
));

$router->add("/news/([0-9]{4})", array(
    'controller' => 'news',
    'action' => 'showYear',
    'year' => 1
));

$router->add("/set-language/{language:[a-z]+}", array(
    'controller' => 'index',
    'action' => 'setLanguage'
));

