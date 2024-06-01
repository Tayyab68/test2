<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Inserting a single admin
        DB::table('admins')->insert([
            'user_name' => 'admin',
            'user_password' => Hash::make('admin6489'), // Encrypting the password
        ]);
    }
}
