<?php
/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 24.11.13
 * Time: 23:49
 */

namespace Verber\Console\Command\WindowsAzure;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Process\Process;

class ImportPublishSettings extends Command
{
    protected function configure()
    {
        $this
            ->setName('azure:import-publish-settings')
            ->setDescription('Import publish settings credentials from Azure')
            ->addArgument(
                'credentials_file',
                InputArgument::REQUIRED,
                'Path to credentials file'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commandLine = 'azure account import ' . realpath($input->getArgument('credentials_file'));
        $azureProcess = new Process($commandLine);
        $azureProcess->run();
        if ($azureProcess->getExitCode() > 0) {
            throw new Exception($azureProcess->getErrorOutput());
        }
    }


} 