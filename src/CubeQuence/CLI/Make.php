<?php

namespace CQ\CLI;

use Phinx\Console\PhinxApplication;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Make extends Template
{
    /**
     * Make migration.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param SymfonyStyle    $io
     */
    public function migration(InputInterface $input, OutputInterface $output, SymfonyStyle $io)
    {
        if (!self::envCheck($input, $output, $io)) {
            return;
        }

        try {
            $name = $input->getArgument('name');
            $phinx = new PhinxApplication();
            $command = $phinx->find('create');

            $arguments = [
                'command' => 'create',
                'name' => $name,
                '--template' => __DIR__.'/../DB/Template/Migration.php',
                '--configuration' => __DIR__.'/../../../../../../phinx.php',
            ];

            $command->run(new ArrayInput($arguments), $output);
        } catch (\Throwable $th) {
            $io->error($th->getMessage());

            return;
        }

        $io->success('Migration created');
    }

    /**
     * Make seed.
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
            $name = $input->getArgument('name');
            $phinx = new PhinxApplication();
            $command = $phinx->find('seed:create');

            $arguments = [
                'command' => "seed:create {$name}",
                'name' => $name,
                '--configuration' => __DIR__.'/../../../../../../phinx.php',
            ];

            $command->run(new ArrayInput($arguments), $output);
        } catch (\Throwable $th) {
            $io->error($th->getMessage());

            return;
        }

        $io->success('Seed created');
    }
}
