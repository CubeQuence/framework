<?php

namespace CQ\CLI;

use CQ\Config\Config;
use CQ\Helpers\File;
use CQ\Helpers\Secrets as SecretsHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Secrets extends Template
{
    /**
     * Generate secret store.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param SymfonyStyle    $io
     */
    public function createStore(InputInterface $input, OutputInterface $output, SymfonyStyle $io)
    {
        if (!self::envCheck($input, $output, $io)) {
            return;
        }

        try {
            $store = SecretsHelper::createStore(
                Config::get('app.name'),
                []
            );

            $path = __DIR__.'/../../../../../../.env';

            if (!file_exists($path)) {
                $io->warning('.env file not found, please set manually');
                $io->text("SECRETS_ID=\"{$store->id}\"");
                $io->text("SECRETS_KEY=\"{$store->key}\"");

                return;
            }

            $env_file = new File($path);

            $env_file->write(str_replace(
                'SECRETS_ID="'.env('SECRETS_ID').'"',
                'SECRETS_ID="'.$store->id.'"',
                $env_file->read()
            ));

            $env_file->write(str_replace(
                'SECRETS_KEY="'.env('SECRETS_KEY').'"',
                'SECRETS_KEY="'.$store->key.'"',
                $env_file->read()
            ));
        } catch (\Throwable $th) {
            $io->error($th->getMessage());

            return;
        }

        $io->success('Store created');
    }

    /**
     * Delete secret store.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param SymfonyStyle    $io
     */
    public function deleteStore(InputInterface $input, OutputInterface $output, SymfonyStyle $io)
    {
        if (!self::envCheck($input, $output, $io)) {
            return;
        }

        try {
            SecretsHelper::deleteStore();

            $path = __DIR__.'/../../../../../../.env';

            if (!file_exists($path)) {
                $io->warning('.env file not found, please set manually');
                $io->text('SECRETS_ID=""');
                $io->text('SECRETS_KEY=""');

                return;
            }

            $env_file = new File($path);

            $env_file->write(str_replace(
                'SECRETS_ID="'.env('SECRETS_ID').'"',
                'SECRETS_ID=""',
                $env_file->read()
            ));

            $env_file->write(str_replace(
                'SECRETS_KEY="'.env('SECRETS_KEY').'"',
                'SECRETS_KEY=""',
                $env_file->read()
            ));
        } catch (\Throwable $th) {
            $io->error($th->getMessage());

            return;
        }

        $io->success('Store deleted');
    }
}
