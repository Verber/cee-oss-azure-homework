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
    private $privateKey, $sshConnectString;

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
                'key',
                InputArgument::OPTIONAL,
                'Path to pem key',
                false
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
            )
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->privateKey = $input->getArgument('key');
        if (!$this->privateKey) {
            $this->privateKey = realpath(
                $this->getApplication()->getSilex()['appdir'] . DS . 'config' . DS . 'mycert.key'
            );
        }

        $this->publicKey = preg_replace('/\.key$/', '.pem', $this->privateKey);

        $this->sshConnectString = $this->getApplication()->getSilex()['azure']['default_user_name']
            . '@' . $input->getArgument('dns_name') . '.cloudapp.net';

//        $output->writeln('Creating Azure VM');
//        $this->createVM($input, $output);
//        $output->writeln("\tdone");
//        $output->writeln('Waiting for start:');
//        while (!$this->isVMUp($input)) {
//            sleep(10);
//            $output->write('.');
//        }
//        $output->writeln('OK');
//        $output->writeln('Azure VM is up and running');
//
//
//        $output->writeln('Uploading setup file');
//        $this->uploadSetupFile();
//        $output->writeln("\tdone");
//
//        $output->writeln('Set executable flag fro setup file');
//        $this->setExecFlagOnSetupFile();
//        $output->writeln("\tdone");
//
//        $output->writeln('Runing setup file');
//        $this->runSetup();
//        $output->writeln("\tdone");

        $output->writeln('Opening web port');
        $this->openWebEndpoint($input, $output);
        $output->writeln("\tdone");


    }

    private function runSetup()
    {
        /** @var ProcessBuilder $sshProcessBuilder */
        $sshProcessBuilder = $this->getApplication()->getSilex()['process_builder'];
        $sshProcessBuilder->setPrefix('ssh')->setArguments(array());
        $process = $sshProcessBuilder->getProcess();
        $process->setCommandLine(
            'ssh -oStrictHostKeyChecking=no -i'
            . ' ' . $this->privateKey
            . ' ' . $this->sshConnectString
            . " '/home/azure/setup.sh'"
        );
        $process->run();
        if ($process->getExitCode() > 0) {
            throw new Exception($process->getErrorOutput());
        }
    }

    private function setExecFlagOnSetupFile()
    {
        /** @var ProcessBuilder $sshProcessBuilder */
        $sshProcessBuilder = $this->getApplication()->getSilex()['process_builder'];
        $sshProcessBuilder->setPrefix('ssh')->setArguments(array());
        $process = $sshProcessBuilder->getProcess();
        $process->setCommandLine(
           'ssh -oStrictHostKeyChecking=no -i'
            . ' ' . $this->privateKey
            . ' ' . $this->sshConnectString
            . " 'chmod +x /home/azure/setup.sh'"
        );
        $process->run();
        if ($process->getExitCode() > 0) {
            throw new Exception($process->getErrorOutput());
        }
    }

    private function uploadSetupFile()
    {
        /** @var ProcessBuilder $scpProcessBuilder */
        $scpProcessBuilder = $this->getApplication()->getSilex()['process_builder'];
        $scpProcessBuilder->setPrefix('scp')
            ->setArguments(array(
                '-oStrictHostKeyChecking=no',
                '-i', $this->privateKey,
                './scripts/setup.sh',
                $this->sshConnectString . ':~'
            ));
        $scpProcess = $scpProcessBuilder->getProcess();
        $scpProcess->run();
        if ($scpProcess->getExitCode() > 0) {
            throw new Exception($scpProcess->getErrorOutput());
        }
    }

    private function isVMUp(InputInterface $input)
    {
        /** @var ProcessBuilder $azureProcessBuilder */
        $azureProcessBuilder = $this->getApplication()->getSilex()['process_builder'];
        $azureProcessBuilder->setPrefix('azure')
            ->setArguments(array(
                'vm', 'show',
                $input->getArgument('dns_name'),
                '--json'
            ));
        $azureProcess = $azureProcessBuilder->getProcess();
        $azureProcess->run();
        while ($azureProcess->isRunning()) {
            sleep(1);
        }
        if ($azureProcess->getExitCode() > 1) {
            return false;
        } else {
            $response = json_decode($azureProcess->getOutput());
            if (is_object($response) && $response->InstanceStatus == 'ReadyRole') {
                return true;
            } else {
                return false;
            }
        }
    }

    private function openWebEndpoint(InputInterface $input, OutputInterface $output)
    {
        /** @var ProcessBuilder $azureProcessBuilder */
        $azureProcessBuilder = $this->getApplication()->getSilex()['process_builder'];
        $azureProcessBuilder->setPrefix('azure')
            ->setArguments(array(
                'vm', 'endpoint', 'create',
                $input->getArgument('dns_name'),
                '80'
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
    }

    private function createVM(InputInterface $input, OutputInterface $output)
    {
        /** @var ProcessBuilder $azureProcessBuilder */
        $azureProcessBuilder = $this->getApplication()->getSilex()['process_builder'];
        $azureProcessBuilder->setPrefix('azure')
            ->setArguments(array(
                'vm', 'create',
                '--location', $input->getArgument('region'),
                '--vm-size', $input->getArgument('size'),
                '--ssh', '22',
                '--ssh-cert', $this->publicKey,
                '--no-ssh-password',
                $input->getArgument('dns_name'),
                $this->getApplication()->getSilex()['azure']['image'],
                $this->getApplication()->getSilex()['azure']['default_user_name']
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