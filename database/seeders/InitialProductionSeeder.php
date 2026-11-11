<?php

namespace Database\Seeders;

use App\Models\Cabang;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InitialProductionSeeder extends Seeder
{
    public function run(): void
    {
        $pusat = Cabang::firstOrCreate(
            ['kode_cabang' => 'BJN01'],
            [
                'nama_cabang' => 'Gresik (Pusat)',
                'alamat'      => 'Jl. Cerme kidul 01/01 No.52, Gresik',
                'no_telp'     => '+62 822-2996-5426',
                'is_active'   => true,
            ]
        );

        // Update user yang sudah ada ke cabang pusat
        User::query()->update([
            'cabang_id' => $pusat->id,
            'role'      => 'superadmin',
        ]);
    }
}