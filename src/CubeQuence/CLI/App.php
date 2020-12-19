<?php

namespace CQ\CLI;

use CQ\Helpers\File;
use CQ\Crypto\Random;
use Exception;
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
        return self::envCheck($input, $output, $io);

        try {
            $length = $io->ask('Key length', 64, function ($number) {
                if (!is_numeric($number)) {
                    throw new \RuntimeException('You must type a number.');
                }

                return (int) $number;
            });

            $key = Random::string($length);
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
        } catch (Exception $e) {
            $io->error($e->getMessage());

            return;
        }

        $io->success('Key set successfully');
    }
}
