<?php

declare(strict_types=1);

namespace CQ\CLI;

use CQ\Helpers\AppHelper;
use Phinx\Console\PhinxApplication;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class Make extends Template
{
    /**
     * Make migration.
     */
    public function migration(InputInterface $input, OutputInterface $output, SymfonyStyle $io): void
    {
        if (! self::envCheck(input: $input, output: $output, io: $io)) {
            return;
        }

        $phinx = new PhinxApplication();

        try {
            $name = $input->getArgument(name: 'name');
            $command = $phinx->find(name: 'create');

            $arguments = [
                'command' => 'create',
                'name' => $name,
                '--template' => __DIR__ . '/../DB/Template/Migration.php',
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

        $io->success(message: 'Migration created');
    }

    /**
     * Make seed.
     */
    public function seed(InputInterface $input, OutputInterface $output, SymfonyStyle $io): void
    {
        if (! self::envCheck(input: $input, output: $output, io: $io)) {
            return;
        }

        $phinx = new PhinxApplication();

        try {
            $name = $input->getArgument(name: 'name');
            $command = $phinx->find(name: 'seed:create');

            $arguments = [
                'command' => "seed:create {$name}",
                'name' => $name,
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

        $io->success(message: 'Seed created');
    }
}
