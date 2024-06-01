<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Seed the Admin table
        \App\Models\Admin::factory(1)->create();

        // Seed the User table
        $this->call(AdminSeeder::class);

    }
}
