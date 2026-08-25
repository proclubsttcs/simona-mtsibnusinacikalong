<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder akun user awal:
 * - 1 akun Admin/BK
 * - Beberapa akun Wali Kelas (semua kelas VII, VIII, IX)
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Admin / BK ─────────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'admin@simon.sch.id'],
            [
                'name'                 => 'Admin BK',
                'email'                => 'admin@simon.sch.id',
                'password'             => Hash::make('password'),
                'role'                 => 'admin',
                'kelas'                => null,
                'is_active'            => true,
                'must_change_password' => false,
            ]
        );

        // ─── Wali Kelas (satu per kelas) ─────────────────────────
        $waliKelas = [
            ['name' => 'Hj. Tuti Rohayati, S.Pd',  'email' => 'wk7a@simon.sch.id',  'kelas' => 'VII-A'],
            ['name' => 'Dra. Sari Dewi Lestari',    'email' => 'wk7b@simon.sch.id',  'kelas' => 'VII-B'],
            ['name' => 'H. Asep Kurniawan, S.Pd',   'email' => 'wk7c@simon.sch.id',  'kelas' => 'VII-C'],
            ['name' => 'Neni Fitriani, S.Pd.I',     'email' => 'wk8a@simon.sch.id',  'kelas' => 'VIII-A'],
            ['name' => 'Dedi Supriadi, S.Pd',       'email' => 'wk8b@simon.sch.id',  'kelas' => 'VIII-B'],
            ['name' => 'Sri Wahyuni, S.Ag',         'email' => 'wk8c@simon.sch.id',  'kelas' => 'VIII-C'],
            ['name' => 'Iis Nurhayati, S.Pd',       'email' => 'wk9a@simon.sch.id',  'kelas' => 'IX-A'],
            ['name' => 'Achmad Fauzi, S.Pd.I',      'email' => 'wk9b@simon.sch.id',  'kelas' => 'IX-B'],
            ['name' => 'Yayah Rokayah, S.Pd',       'email' => 'wk9c@simon.sch.id',  'kelas' => 'IX-C'],
        ];

        foreach ($waliKelas as $wk) {
            User::updateOrCreate(
                ['email' => $wk['email']],
                [
                    'name'                 => $wk['name'],
                    'email'                => $wk['email'],
                    'password'             => Hash::make('password123'),
                    'role'                 => 'wali_kelas',
                    'kelas'                => $wk['kelas'],
                    'is_active'            => true,
                    'must_change_password' => true, // wajib ganti password saat login pertama
                ]
            );
        }

        $this->command->info('✅ Seeder User: 1 admin + ' . count($waliKelas) . ' wali kelas berhasil ditanam.');
        $this->command->line('   Admin    : admin@simon.sch.id / password');
        $this->command->line('   WaliKelas: wk7a@simon.sch.id / password123 (dll)');
    }
}
