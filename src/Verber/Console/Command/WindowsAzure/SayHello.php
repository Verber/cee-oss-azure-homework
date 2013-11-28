<?php
/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 28.11.13
 * Time: 11:32
 */

namespace Verber\Console\Command\WindowsAzure;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Guzzle\Http\Client;

class SayHello extends Command
{
    protected function configure()
    {
        $this
            ->setName('azure:say_hello')
            ->setDescription('Says hello to Azure')
            ->addArgument(
                'dns_name',
                InputArgument::REQUIRED,
                'Name of virtual machine, this name will be subdomain'
            )
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Who are you?',
                'azure'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Sending request to VM');
        $client = new Client('http://' . $input->getArgument('dns_name') . '.cloudapp.net');
        $request = $client->get('/hello/' . $input->getArgument('name'));
        $response = $client->send($request);
        //$output->writeln($response);
        var_dump($response);
    }
} 