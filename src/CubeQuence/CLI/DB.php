<?php

declare(strict_types=1);

namespace CQ\CLI;

use CQ\Helpers\AppHelper;
use Phinx\Console\PhinxApplication;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DB extends Template
{
    /**
     * Migrate command.
     */
    public function migrate(InputInterface $input, OutputInterface $output, SymfonyStyle $io): void
    {
        if (! self::envCheck(input: $input, output: $output, io: $io)) {
            return;
        }

        $phinx = new PhinxApplication();

        try {
            $fresh = $input->getOption(name: 'fresh');
            $command = $phinx->find(name: 'rollback');

            $arguments = [
                'command' => 'rollback',
                '--environment' => AppHelper::getEnvoironment(),
                '--target' => '0',
                '--force',
            ];

            if ($fresh) {
                $command->run(
                    input: new ArrayInput(parameters: $arguments),
                    output: $output
                );

                $io->success(message: 'Reset successful');
            }
        } catch (\Throwable $th) {
            $io->error(message: $th->getMessage());

            return;
        }

        try {
            $command = $phinx->find(name: 'migrate');

            $arguments = [
                'command' => 'migrate',
                '--environment' => AppHelper::getEnvoironment(),
                '--configuration' => AppHelper::getRootPath() . '/phinx.php',
            ];

            $command->run(
                input: new ArrayInput(parameters: $arguments),
                output: $output
            );
        } catch (\Throwable $th) {
            $io->error(message: $th->getMessage());

            return;
        }

        $io->success(message: 'Migration successful');
    }

    /**
     * Seed command.
     */
    public function seed(InputInterface $input, OutputInterface $output, SymfonyStyle $io): void
    {
        if (! self::envCheck(input: $input, output: $output, io: $io)) {
            return;
        }

        $phinx = new PhinxApplication();

        try {
            $command = $phinx->find(name: 'seed:run');

            $arguments = [
                'command' => 'seed:run',
                '--environment' => AppHelper::getEnvoironment(),
                '--configuration' => AppHelper::getRootPath() . '/phinx.php',
            ];

            $command->run(
                input: new ArrayInput(parameters: $arguments),
                output: $output
            );
        } catch (\Throwable $th) {
            $io->error(message: $th->getMessage());

            return;
        }

        $io->success(message: 'Seeding successful');
    }
}
