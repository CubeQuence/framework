<?php

namespace CQ\DB;

use Faker\Factory;
use Faker\Generator;
use Phinx\Seed\AbstractSeed;

class Seeder extends AbstractSeed
{
    /**
     * Create faker instance.
     *
     * @return Generator
     */
    public static function faker() : Generator
    {
        return Factory::create();
    }
}
