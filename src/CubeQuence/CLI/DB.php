<?php

namespace CQ\CLI;

use CQ\Helpers\App;
use Phinx\Console\PhinxApplication;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DB extends Template
{
    /**
     * Migrate command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param SymfonyStyle    $io
     */
    public function migrate(InputInterface $input, OutputInterface $output, SymfonyStyle $io)
    {
        if (!self::envCheck($input, $output, $io)) {
            return;
        }

        try {
            $fresh = $input->getOption('fresh');
            $phinx = new PhinxApplication();
            $command = $phinx->find('rollback');

            $arguments = [
                'command' => 'rollback',
                '--environment' => App::environment(),
                '--target' => '0',
                '--force',
            ];

            if ($fresh) {
                $command->run(new ArrayInput($arguments), $output);
                $io->success('Reset successful');
            }
        } catch (\Throwable $th) {
            $io->error($th->getMessage());

            return;
        }

        try {
            $phinx = new PhinxApplication();
            $command = $phinx->find('migrate');

            $arguments = [
                'command' => 'migrate',
                '--environment' => App::environment(),
                '--configuration' => __DIR__.'/../../../../../../phinx.php',
            ];

            $command->run(new ArrayInput($arguments), $output);
        } catch (\Throwable $th) {
            $io->error($th->getMessage());

            return;
        }

        $io->success('Migration successful');
    }

    /**
     * Seed command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param SymfonyStyle    $io
     */
    public function seed(InputInterface $input, OutputInterface $output, SymfonyStyle $io)
    {
        if (!self::envCheck($input, $output, $io)) {
            return;
        }

        try {
            $phinx = new PhinxApplication();
            $command = $phinx->find('seed:run');

            $arguments = [
                'command' => 'seed:run',
                '--environment' => App::environment(),
                '--configuration' => __DIR__.'/../../../../../../phinx.php',
            ];

            $command->run(new ArrayInput($arguments), $output);
        } catch (\Throwable $th) {
            $io->error($th->getMessage());

            return;
        }

        $io->success('Seeding successful');
    }
}
