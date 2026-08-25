<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Urutan seeder penting karena ada foreign key:
     * 1. UserSeeder         → harus ada dulu sebelum SiswaSeeder
     * 2. JenisPelanggaranSeeder
     * 3. SiswaSeeder        → butuh user_id dari UserSeeder
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            JenisPelanggaranSeeder::class,
        ]);
    }
}
