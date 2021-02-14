<?php

namespace CQ\CLI;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use CQ\Helpers\App;
use CQ\Helpers\File;
use CQ\Helpers\Secrets as SecretsHelper;
use CQ\Config\Config;

class Secrets extends Template
{
    /**
     * Generate secret store.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param SymfonyStyle    $io
     *
     * @return void
     */
    public function createStore(InputInterface $input, OutputInterface $output, SymfonyStyle $io) : void
    {
        if (!self::envCheck(input: $input, output: $output, io: $io)) {
            return;
        }

        try {
            $store = SecretsHelper::createStore(
                name: Config::get(key: 'app.name'),
                data: []
            );

            $path = App::getRootPath() .'/.env';

            if (!file_exists(filename: $path)) {
                $io->warning(message: '.env file not found, please set manually');
                $io->text(message: "SECRETS_ID=\"{$store->id}\"");
                $io->text(message: "SECRETS_KEY=\"{$store->key}\"");

                return;
            }

            $env_file = new File(path: $path);

            $env_content = str_replace(
                search: 'SECRETS_ID="' . Config::get(key: 'secrets.id') . '"',
                replace: 'SECRETS_ID="' . $store->id . '"',
                subject: $env_file->read()
            );
            $env_content = str_replace(
                search: 'SECRETS_KEY="' . Config::get(key: 'secrets.key') . '"',
                replace: 'SECRETS_KEY="' . $store->key . '"',
                subject: $env_content
            );

            $env_file->write(data: $env_content);
        } catch (\Throwable) {
            $io->error(message: 'Check API key');

            return;
        }

        $io->success(message: 'Store created: ' . $store->id);
    }

    /**
     * Delete secret store.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param SymfonyStyle    $io
     *
     * @return void
     */
    public function deleteStore(InputInterface $input, OutputInterface $output, SymfonyStyle $io) : void
    {
        if (!self::envCheck(input: $input, output: $output, io: $io)) {
            return;
        }

        try {
            SecretsHelper::deleteStore();

            $path = App::getRootPath() .'/.env';

            if (!file_exists(filename: $path)) {
                $io->warning(message: '.env file not found, please set manually');
                $io->text(message: 'SECRETS_ID=""');
                $io->text(message: 'SECRETS_KEY=""');

                return;
            }

            $env_file = new File(path: $path);

            $env_content = str_replace(
                search: 'SECRETS_ID="' . Config::get(key: 'secrets.id') . '"',
                replace: 'SECRETS_ID=""',
                subject: $env_file->read()
            );
            $env_content = str_replace(
                search: 'SECRETS_KEY="' . Config::get(key: 'secrets.key') . '"',
                replace: 'SECRETS_KEY=""',
                subject: $env_content
            );

            $env_file->write(data: $env_content);
        } catch (\Throwable) {
            $io->error(message: 'Check API key');

            return;
        }

        $io->success(message: 'Store deleted');
    }
}
