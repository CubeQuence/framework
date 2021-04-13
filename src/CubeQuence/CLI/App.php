<?php

declare(strict_types=1);

namespace CQ\CLI;

use CQ\Config\Config;
use CQ\Crypto\Models\SymmetricKey;
use CQ\Crypto\Password;
use CQ\Crypto\Symmetric;
use CQ\Helpers\AppHelper;
use CQ\File\File;
use CQ\File\Adapters\Providers\Local;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class App extends Template
{
    /**
     * Generate key command.
     */
    public function key(InputInterface $input, OutputInterface $output, SymfonyStyle $io): void
    {
        if (! self::envCheck(input: $input, output: $output, io: $io)) {
            return;
        }

        try {
            $localProvider = new Local(
                rootPath: AppHelper::getRootPath()
            );

            $fileHandler = new File(
                adapterProvider: $localProvider
            );

            $envPath = '/.env';
            $envKey = new SymmetricKey();
            $exportedEnvKey = $envKey->export();

            if (! $fileHandler->exists($envPath)) {
                $io->warning(message: '.env file not found, please set key manually');
                $io->text(message: "APP_KEY=\"{$exportedEnvKey}\"");

                return;
            }

            $envContent = $fileHandler->read(path: $envPath);
            $envUpdatedContent = str_replace(
                search: 'APP_KEY="' . Config::get('app.key') . '"',
                replace: 'APP_KEY="' . $exportedEnvKey . '"',
                subject: $envContent
            );

            $fileHandler->write(
                path: $envPath,
                contents: $envUpdatedContent
            );
        } catch (\Throwable $th) {
            $io->error(message: $th->getMessage());

            return;
        }

        $io->success(message: 'Key set successfully');
    }

    /**
     * Generate derived key command.
     */
    public function contextKey(InputInterface $input, OutputInterface $output, SymfonyStyle $io): void
    {
        try {
            $appKey = new SymmetricKey(
                encodedKey: Config::get('app.key')
            );

            $password = new Password(
                key: $appKey
            );

            $envKeyContext = $password->hash(
                plaintextPassword: Config::get('app.key'),
                context: $input->getArgument(name: 'context')
            );
        } catch (\Throwable $th) {
            $io->error(message: $th->getMessage());

            return;
        }

        $io->success(message: 'Context key created successfully');
        $io->text(message: $envKeyContext);
    }
}
