<?php

namespace CQ\DB;

use Faker\Factory;
use Phinx\Seed\AbstractSeed;

class Seeder extends AbstractSeed
{
    /**
     * Create faker instance
     */
    public static function faker()
    {
        return Factory::create();
    }
}
