<?php

namespace CQ\DB;

use Faker\Factory;

class Seeder
{
    /**
     * Create faker instance
     */
    public static function create()
    {
        return Factory::create();
    }
}
