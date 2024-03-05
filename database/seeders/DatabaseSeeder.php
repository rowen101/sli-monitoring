<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\Admin\MenuSeeder;
use Database\Seeders\Admin\UserSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            MenuSeeder::class,
            UserSeeder::class
        ]);

       
    }
}
