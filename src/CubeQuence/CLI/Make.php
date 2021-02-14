<?php

namespace CQ\CLI;

use Phinx\Console\PhinxApplication;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use CQ\Helpers\App;

class Make extends Template
{
    /**
     * Make migration.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param SymfonyStyle    $io
     *
     * @return void
     */
    public function migration(InputInterface $input, OutputInterface $output, SymfonyStyle $io) : void
    {
        if (!self::envCheck(input: $input, output: $output, io: $io)) {
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
                '--configuration' => App::getRootPath() . '/phinx.php',
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
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param SymfonyStyle    $io
     *
     * @return void
     */
    public function seed(InputInterface $input, OutputInterface $output, SymfonyStyle $io) : void
    {
        if (!self::envCheck(input: $input, output: $output, io: $io)) {
            return;
        }

        $phinx = new PhinxApplication();

        try {
            $name = $input->getArgument(name: 'name');
            $command = $phinx->find(name: 'seed:create');

            $arguments = [
                'command' => "seed:create {$name}",
                'name' => $name,
                '--configuration' => App::getRootPath() . '/phinx.php',
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
