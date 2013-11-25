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
use Symfony\Component\Process\ProcessBuilder;

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
        /** @var ProcessBuilder $azureProcessBuilder */
        $azureProcessBuilder = $this->getApplication()->getSilex()['process_builder'];
        $azureProcessBuilder->setPrefix('azure')
            ->setArguments(array('account', 'import', realpath($input->getArgument('credentials_file'))));
        $azureProcess = $azureProcessBuilder->getProcess();
        $azureProcess->run();
        if ($azureProcess->getExitCode() > 0) {
            throw new Exception($azureProcess->getErrorOutput());
        } else {
            $output->writeln($azureProcess->getOutput());
        }
    }


} 