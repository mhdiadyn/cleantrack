<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = now();

        DB::table('areas')
            ->where('name', 'Rooftop Lounge & Cafetaria')
            ->where('floor', '4')
            ->update([
                'name' => 'Rooftop',
                'location' => 'Gedung A Lantai 4 (Rooftop)',
                'description' => 'Area rooftop lantai 4.',
                'updated_at' => $now,
            ]);

        DB::table('areas')
            ->whereIn('name', [
                'Ruang Pusat Kontrol & CCTV',
                'Ruang Arsip & Dokumen',
                'Toilet & Shower Lantai 4',
            ])
            ->where('floor', '4')
            ->update([
                'is_active' => false,
                'updated_at' => $now,
            ]);

        $rooftop = DB::table('areas')->where('name', 'Rooftop')->first();
        if ($rooftop) {
            DB::table('areas')
                ->where('id', $rooftop->id)
                ->update([
                    'location' => 'Gedung A Lantai 4 (Rooftop)',
                    'description' => 'Area rooftop lantai 4.',
                    'floor' => '4',
                    'is_active' => true,
                    'updated_at' => $now,
                ]);
        } else {
            DB::table('areas')->insert([
                'name' => 'Rooftop',
                'location' => 'Gedung A Lantai 4 (Rooftop)',
                'description' => 'Area rooftop lantai 4.',
                'floor' => '4',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        foreach ([
            [
                'name' => 'Parkiran Basement',
                'location' => 'Gedung A Basement (Area Parkir)',
                'description' => 'Area parkir kendaraan di basement.',
            ],
            [
                'name' => 'Dapur Basement',
                'location' => 'Gedung A Basement',
                'description' => 'Dapur di area basement.',
            ],
            [
                'name' => 'WC Basement',
                'location' => 'Gedung A Basement (Di samping dapur)',
                'description' => 'WC basement yang berada di samping dapur.',
            ],
            [
                'name' => 'Ruang Billiard Basement',
                'location' => 'Gedung A Basement',
                'description' => 'Ruang billiard di area basement.',
            ],
            [
                'name' => 'Gudang Basement',
                'location' => 'Gedung A Basement',
                'description' => 'Gudang penyimpanan di area basement.',
            ],
        ] as $area) {
            DB::table('areas')->updateOrInsert(
                ['name' => $area['name']],
                [
                    ...$area,
                    'floor' => '0',
                    'is_active' => true,
                    'updated_at' => $now,
                    'created_at' => $now,
                ],
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('areas')
            ->whereIn('name', [
                'Parkiran Basement',
                'Dapur Basement',
                'WC Basement',
                'Ruang Billiard Basement',
                'Gudang Basement',
            ])
            ->where('floor', '0')
            ->update([
                'is_active' => false,
                'updated_at' => now(),
            ]);

        DB::table('areas')
            ->where('name', 'Rooftop')
            ->where('floor', '4')
            ->update([
                'name' => 'Rooftop Lounge & Cafetaria',
                'location' => 'Gedung A Lantai 4 (Area Terbuka)',
                'description' => 'Area rooftop terbuka semi-outdoor untuk istirahat dan coffee break.',
                'updated_at' => now(),
            ]);

        DB::table('areas')
            ->whereIn('name', [
                'Ruang Pusat Kontrol & CCTV',
                'Ruang Arsip & Dokumen',
                'Toilet & Shower Lantai 4',
            ])
            ->where('floor', '4')
            ->update([
                'is_active' => true,
                'updated_at' => now(),
            ]);
    }
};
