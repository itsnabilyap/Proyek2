<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminOwnerSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'Nitsi'],
            [
                'name' => 'Nitsi',
                'email' => 'admin@bucketimy.local',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['username' => 'owner'],
            [
                'name' => 'Owner Bucket Imy',
                'email' => 'owner@bucketimy.local',
                'password' => Hash::make('Owner123'),
                'role' => 'owner',
            ]
        );
    }
}