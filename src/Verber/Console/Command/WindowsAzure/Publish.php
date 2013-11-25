<?php
namespace Verber\Console\Command\WindowsAzure;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ProcessBuilder;

class Publish extends Command
{
    protected function configure()
    {
        $this
            ->setName('azure:publish')
            ->setDescription('Setup Ubuntu virtual machine on Windows Azure')
            ->addArgument(
                'dns_name',
                InputArgument::REQUIRED,
                'Name of virtual machine, this name will be subdomain'
            )
            ->addArgument(
                'region',
                InputArgument::OPTIONAL,
                'What region are you going to deploy your application?',
                'West Europe'
            )
            ->addArgument(
                'size',
                InputArgument::OPTIONAL,
                'What size of virtual machine are you going to use?',
                'small'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        /** @var ProcessBuilder $azureProcessBuilder */
        $azureProcessBuilder = $this->getApplication()->getSilex()['process_builder'];
        $azureProcessBuilder->setPrefix('azure')
            ->setArguments(array(
                'vm', 'create',
                '--location', $input->getArgument('region'),
                '--vm-size', $input->getArgument('size'),
                '--ssh', '22',
                $input->getArgument('dns_name'),
                $this->getApplication()->getSilex()['azure']['image'],
                $this->getApplication()->getSilex()['azure']['default_user_name'],
                $this->generatePassword()
            ))
            ->setTimeout(null);
        $azureProcess = $azureProcessBuilder->getProcess();
        $output->writeln('Running Azure command:');
        $output->writeln($azureProcess->getCommandLine());
        $azureProcess->run();
        while ($azureOut = $azureProcess->getIncrementalOutput()) {
            $output->write($azureOut);
            sleep(1);
        }
        if ($azureProcess->getExitCode() > 0) {
            throw new Exception($azureProcess->getErrorOutput());
        } else {
            //$output->writeln($azureProcess->getOutput());
        }

    }

    private function generatePassword()
    {
        /** @var ProcessBuilder $processBuilder */
        $processBuilder = $this->getApplication()->getSilex()['process_builder'];
        $processBuilder->setPrefix('pwgen');
        $process = $processBuilder->getProcess();
        $process->run();
        if ($process->getExitCode() > 0) {
            throw new Exception($process->getErrorOutput());
        } else {
            $password = trim($process->getOutput());
            $password = str_replace(array('o', 'a', 'e', 's'), array('0', '4', '3', '5'), $password);
            $password = ucfirst($password) . '!';
            return $password;
        }
    }
} 