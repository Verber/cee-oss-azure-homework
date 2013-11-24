<?php
namespace Verber\Command\WindowsAzure;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Publish extends Command
{
    protected function configure()
    {
        $this
            ->setName('azure:publish')
            ->setDescription('Setup Ubuntu virtual machine on Windows Azure')
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
        $region = $input->getArgument('region');
        $size = $input->getArgument('size');
        $text = 'Creating VM in  ' . $region . ' size ' . $size;

        $output->writeln($text);
    }
} 