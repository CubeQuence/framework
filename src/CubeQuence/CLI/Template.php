<?php

namespace CQ\CLI;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use CQ\Helpers\App;

class Template
{
    /**
     * Check environment
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param SymfonyStyle    $io
     *
     * @return bool
     */
    public static function envCheck(InputInterface $input, OutputInterface $output, SymfonyStyle $io) : bool
    {
        if (App::environment(check: 'production')) {
            $io->note(message: 'Application In Production!');
            if (!$io->confirm(
                question: 'Do you really wish to run this command?',
                default: false
            )) {
                $io->note(message: 'Command Canceled!');

                return false;
            }
        }

        return true;
    }
}
