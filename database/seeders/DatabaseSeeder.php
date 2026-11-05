<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * `php artisan migrate:fresh --seed` runs this class, which in turn runs
     * the Campus Events seeder that builds the whole dataset.
     */
    public function run(): void
    {
        $this->call(CampusEventSeeder::class);
    }
}
