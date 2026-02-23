<?php

namespace Database\Seeders;

use Database\Seeders\AdminSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(AdminSeeder::class);
    }
}
