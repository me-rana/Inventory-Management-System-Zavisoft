<?php

namespace Database\Seeders;

use Database\Seeders\AdminSeeder;
use Database\Seeders\ProductSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            ProductSeeder::class
        ]);
    }
}
