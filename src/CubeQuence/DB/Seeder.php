<?php

declare(strict_types=1);

namespace CQ\DB;

use Faker\Factory;
use Faker\Generator;
use Phinx\Seed\AbstractSeed;

abstract class Seeder extends AbstractSeed
{
    /**
     * Create faker instance.
     */
    public static function faker(): Generator
    {
        return Factory::create();
    }
}
