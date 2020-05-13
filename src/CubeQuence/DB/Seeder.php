<?php

namespace CQ\DB;

use Faker\Factory;

class Seeder
{
    /**
     * Create faker instance
     */
    public function create()
    {
        return Factory::create();
    }
}
