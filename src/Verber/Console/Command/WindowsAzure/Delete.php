<?php
/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 27.11.13
 * Time: 18:01
 */

namespace Verber\Console\Command\WindowsAzure;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class Delete extends Command
{
    protected function configure()
    {
        $this
            ->setName('azure:delete')
            ->setDescription('Delete VM from azure')
            ->addArgument(
                'dns_name',
                InputArgument::REQUIRED,
                'Name of virtual machine, this name will be subdomain'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Deleting VM');
        /** @var ProcessBuilder $azureProcessBuilder */
        $azureProcessBuilder = $this->getApplication()->getSilex()['process_builder'];
        $azureProcessBuilder->setPrefix('azure')
            ->setArguments(array(
                'vm', 'delete', '-b', '-q',
                $input->getArgument('dns_name')
            ))
            ->setTimeout(null);
        $azureProcess = $azureProcessBuilder->getProcess();

        $output->writeln($azureProcess->getCommandLine());
        $azureProcess->run();
        while ($azureOut = $azureProcess->getIncrementalOutput() || $azureProcess->isRunning()) {
            $output->write($azureOut);
            sleep(1);
        }
        if ($azureProcess->getExitCode() > 0) {
            throw new Exception($azureProcess->getErrorOutput());
        }

        /** @var ProcessBuilder $azureProcessBuilder */
        $azureProcessBuilder = $this->getApplication()->getSilex()['process_builder'];
        $azureProcessBuilder->setPrefix('azure')
            ->setArguments(array(
                'service', 'delete', '-q',
                $input->getArgument('dns_name')
            ))
            ->setTimeout(null);
        $azureProcess = $azureProcessBuilder->getProcess();

        $output->writeln($azureProcess->getCommandLine());
        $azureProcess->run();
        while ($azureOut = $azureProcess->getIncrementalOutput() || $azureProcess->isRunning()) {
            $output->write($azureOut);
            sleep(1);
        }
        if ($azureProcess->getExitCode() > 0) {
            throw new Exception($azureProcess->getErrorOutput());
        }

        $output->writeln("\tdone");
    }


} 