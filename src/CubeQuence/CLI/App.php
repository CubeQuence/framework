<?php

namespace CQ\CLI;

use CQ\Helpers\File;
use CQ\Crypto\Symmetric;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class App extends Template
{
    /**
     * Generate key command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param SymfonyStyle    $io
     */
    public function key(InputInterface $input, OutputInterface $output, SymfonyStyle $io)
    {
        if (!self::envCheck($input, $output, $io)) {
            return;
        }

        try {
            $key = Symmetric::genKey();
            $path = __DIR__.'/../../../../../../.env';

            if (!file_exists($path)) {
                $io->warning('.env file not found, please set key manually');
                $io->text("APP_KEY=\"{$key}\"");

                return;
            }

            $env_file = new File($path);
            $env_file->write(str_replace(
                'APP_KEY="'.env('APP_KEY').'"',
                'APP_KEY="'.$key.'"',
                $env_file->read()
            ));
        } catch (\Throwable $th) {
            $io->error($th->getMessage());

            return;
        }

        $io->success('Key set successfully');
    }
}
