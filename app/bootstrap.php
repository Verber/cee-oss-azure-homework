<?php

define('DS', DIRECTORY_SEPARATOR);

require_once realpath(
    __DIR__ . DS . '..' . DS . 'vendor' . DS . 'autoload.php'
);

$config = require_once realpath(
    __DIR__ . DS . 'config' . DS . 'conf.php'
);

$app = new Silex\Application();

foreach ($config as $parameterName => $parameter) {
    $app[$parameterName] = $parameter;
}

$app['appdir'] = realpath(__DIR__);


return $app;