#!/usr/bin/env php
<?php
$app = require_once realpath(__DIR__ . DIRECTORY_SEPARATOR . 'bootstrap.php');

use Symfony\Component\Console\Application as ConsoleApp;

$console = new ConsoleApp();
$console->run();