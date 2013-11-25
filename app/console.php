#!/usr/bin/env php
<?php
$app = require_once realpath(__DIR__ . DIRECTORY_SEPARATOR . 'bootstrap.php');

//use Symfony\Component\Console\Application as ConsoleApp;
use Verber\Console\SilexAwareApplication as ConsoleApp;
use Verber\Console\Command\WindowsAzure\Publish;
use Verber\Console\Command\WindowsAzure\ImportPublishSettings;

$console = new ConsoleApp();
$console->setSilex($app);
$console->add(new Publish());
$console->add(new ImportPublishSettings());
$console->run();