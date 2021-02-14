<?php

namespace CQ\CLI;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use CQ\Helpers\App as AppHelper;
use CQ\Helpers\File;
use CQ\Crypto\Password;
use CQ\Crypto\Symmetric;

class App extends Template
{
    /**
     * Generate key command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param SymfonyStyle    $io
     *
     * @return void
     */
    public function key(InputInterface $input, OutputInterface $output, SymfonyStyle $io) : void
    {
        if (!self::envCheck(input: $input, output: $output, io: $io)) {
            return;
        }

        try {
            $key = Symmetric::genKey();
            $path = AppHelper::getRootPath() .'/.env';

            if (!file_exists(filename: $path)) {
                $io->warning(message: '.env file not found, please set key manually');
                $io->text(message: "APP_KEY=\"{$key}\"");

                return;
            }

            $env_file = new File(path: $path);
            $env_file->write(
                data: str_replace(
                    search: 'APP_KEY="' . env(key: 'APP_KEY') . '"',
                    replace: 'APP_KEY="' . $key . '"',
                    subject: $env_file->read()
                )
            );
        } catch (\Throwable $th) {
            $io->error(message: $th->getMessage());

            return;
        }

        $io->success(message: 'Key set successfully');
    }

    /**
     * Generate derived key command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param SymfonyStyle    $io
     *
     * @return void
     */
    public function contextKey(InputInterface $input, OutputInterface $output, SymfonyStyle $io) : void
    {
        try {
            $plaintext_key = Symmetric::getKey(type: 'encryption');
            $context_key = Password::hash(
                plaintext_password: $plaintext_key,
                context: $input->getArgument(name: 'context')
            );
        } catch (\Throwable $th) {
            $io->error(message: $th->getMessage());

            return;
        }

        $io->success(message: 'Context key created successfully');
        $io->text(message: $context_key);
    }
}
