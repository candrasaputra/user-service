<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Testing user',
            'username' => 'testing1user',
            'password' => Hash::make('testing1user')
        ]);
        User::create([
            'name' => 'Testing2 user',
            'username' => 'testing2user',
            'password' => Hash::make('testing2user'),
        ]);
    }
}
