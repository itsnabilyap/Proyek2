<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'name' => 'Nitsi', 'username' => 'Nitsi', 'email' => 'admin@bucketimy.id',
            'password' => 'admin1', 'role' => 'admin',
        ]);

        User::create([
            'name' => 'Nabil', 'username' => 'Nabil', 'email' => 'owner@bucketimy.id',
            'password' => 'owner1', 'role' => 'owner',
        ]);
        
        $this->call([
            KatalogSeeder::class,
            UkuranProdukSeeder::class,
        ]);
    }
}
