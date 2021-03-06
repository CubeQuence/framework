<?php

declare(strict_types=1);

namespace CQ\CLI;

use CQ\Helpers\AppHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class Template
{
    /**
     * Check environment
     */
    public static function envCheck(InputInterface $input, OutputInterface $output, SymfonyStyle $io): bool
    {
        if (AppHelper::isEnvironment(check: 'production')) {
            $io->note(message: 'Application In Production!');

            if (! $io->confirm(question: 'Do you really wish to run this command?', default: false)) {
                $io->note(message: 'Command Canceled!');

                return false;
            }
        }

        return true;
    }
}
