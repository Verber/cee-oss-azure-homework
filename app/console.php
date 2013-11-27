#!/usr/bin/env php
<?php
$app = require_once realpath(__DIR__ . DIRECTORY_SEPARATOR . 'bootstrap.php');

use Symfony\Component\Process\ProcessBuilder;
use Verber\Console\SilexAwareApplication as ConsoleApp;
use Verber\Console\Command\WindowsAzure\Publish;
use Verber\Console\Command\WindowsAzure\ImportPublishSettings;
use Verber\Console\Command\WindowsAzure\Delete;

$app['process_builder'] = function() {
    return new ProcessBuilder();
};

$console = new ConsoleApp();
$console->setSilex($app);
$console->add(new Publish());
$console->add(new ImportPublishSettings());
$console->add(new Delete());
$console->run();