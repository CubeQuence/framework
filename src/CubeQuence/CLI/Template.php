<?php

namespace CQ\CLI;

use CQ\Helpers\App;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/*

    TODO: implement in DB/Make/App

    replace this code stuff with this below

*/

class Template
{
    /**
     * Check environment
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param SymfonyStyle    $io
     */
    public function envCheck(InputInterface $input, OutputInterface $output, SymfonyStyle $io)
    {
        if (App::environment('production')) {
            $io->note('Application In Production!');
            if (!$io->confirm('Do you really wish to run this command?', false)) {
                $io->note('Command Canceled!');

                return;
            }
        }
    }
}
