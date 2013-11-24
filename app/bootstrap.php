<?php
require_once realpath(
    __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php'
);

$config = require_once realpath(
    __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'conf.php'
);

$app = new Silex\Application();

foreach ($config as $parameterName => $parameter) {
    $app[$parameterName] = $parameter;
}

$app['appdir'] = realpath(__DIR__);


return $app;