<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\CleaningMonitoring;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Users
        $admin = User::updateOrCreate(
            ['email' => 'admin@cleantrack.test'],
            [
                'name' => 'Administrator CleanTrack',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'aktif',
                'phone' => '081234567890',
            ]
        );

        $petugas1 = User::updateOrCreate(
            ['email' => 'andi@cleantrack.test'],
            [
                'name' => 'Andi Saputra',
                'password' => Hash::make('password123'),
                'role' => 'petugas',
                'status' => 'aktif',
                'phone' => '081298765432',
            ]
        );

        $petugas2 = User::updateOrCreate(
            ['email' => 'budi@cleantrack.test'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password123'),
                'role' => 'petugas',
                'status' => 'aktif',
                'phone' => '081345678901',
            ]
        );

        $petugas3 = User::updateOrCreate(
            ['email' => 'citra@cleantrack.test'],
            [
                'name' => 'Citra Lestari',
                'password' => Hash::make('password123'),
                'role' => 'petugas',
                'status' => 'aktif',
                'phone' => '081567890123',
            ]
        );

        // 2. Create Areas
        $areasData = [
            [
                'name' => 'Lobby Utama',
                'location' => 'Gedung A Lantai 1',
                'description' => 'Area pintu masuk utama, ruang tunggu tamu, dan meja resepsionis.',
                'is_active' => true,
            ],
            [
                'name' => 'Toilet Lantai 1',
                'location' => 'Gedung A Lantai 1 (Dekat Tangga)',
                'description' => 'Toilet pria dan wanita lantai 1 serta wastafel.',
                'is_active' => true,
            ],
            [
                'name' => 'Toilet Lantai 2',
                'location' => 'Gedung A Lantai 2 (Sayap Kiri)',
                'description' => 'Toilet pria dan wanita lantai 2.',
                'is_active' => true,
            ],
            [
                'name' => 'Ruang Meeting VIP',
                'location' => 'Gedung A Lantai 2 (Ruang 201)',
                'description' => 'Ruang rapat kapasitas 12 orang dengan proyektor & karpet.',
                'is_active' => true,
            ],
            [
                'name' => 'Ruang Kerja Utama',
                'location' => 'Gedung B Lantai 1',
                'description' => 'Open space workstation karyawan dan area printer.',
                'is_active' => true,
            ],
            [
                'name' => 'Pantry & Ruang Makan',
                'location' => 'Gedung B Lantai 1 (Belakang)',
                'description' => 'Dapur bersih, lemari es, microwave, dan meja makan.',
                'is_active' => true,
            ],
            [
                'name' => 'Area Parkir Depan',
                'location' => 'Halaman Depan Gedung A',
                'description' => 'Area parkir mobil dan motor karyawan & tamu.',
                'is_active' => true,
            ],
            [
                'name' => 'Koridor Lantai 1 & 2',
                'location' => 'Gedung A & B Penghubung',
                'description' => 'Seluruh lorong penghubung antar gedung dan tangga darurat.',
                'is_active' => true,
            ],
            [
                'name' => 'Gudang Logistik',
                'location' => 'Basement Gedung B',
                'description' => 'Penyimpanan stok barang perlengkapan kantor dan alat kebersihan.',
                'is_active' => true,
            ],
        ];

        $areas = [];
        foreach ($areasData as $data) {
            $areas[] = Area::updateOrCreate(['name' => $data['name']], $data);
        }

        // 3. Create Sample Photos in Storage if not exist
        if (! Storage::disk('public')->exists('cleaning_photos')) {
            Storage::disk('public')->makeDirectory('cleaning_photos');
        }

        // Create sample placeholder images for realistic demo
        $dummyBeforeSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="600" height="400" viewBox="0 0 600 400"><rect width="600" height="400" fill="#fee2e2"/><text x="50%" y="45%" dominant-baseline="middle" text-anchor="middle" font-family="sans-serif" font-size="24" font-weight="bold" fill="#991b1b">KONDISI SEBELUM DIBERSIHKAN</text><text x="50%" y="60%" dominant-baseline="middle" text-anchor="middle" font-family="sans-serif" font-size="16" fill="#b91c1c">Debu, noda lantai & sampah belum dibersihkan</text></svg>';
        $dummyAfterSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="600" height="400" viewBox="0 0 600 400"><rect width="600" height="400" fill="#dcfce7"/><text x="50%" y="45%" dominant-baseline="middle" text-anchor="middle" font-family="sans-serif" font-size="24" font-weight="bold" fill="#166534">KONDISI SESUDAH DIBERSIHKAN</text><text x="50%" y="60%" dominant-baseline="middle" text-anchor="middle" font-family="sans-serif" font-size="16" fill="#15803d">Lantai telah dipel, wangi dan rapi</text></svg>';

        Storage::disk('public')->put('cleaning_photos/sample_before.svg', $dummyBeforeSvg);
        Storage::disk('public')->put('cleaning_photos/sample_after.svg', $dummyAfterSvg);

        // 4. Create Monitoring Data for today and previous days
        $today = Carbon::today();
        $petugasList = [$petugas1, $petugas2, $petugas3];

        $monitoringEntries = [
            [
                'area_idx' => 0, // Lobby
                'user' => $petugas1,
                'condition' => 'Bersih',
                'status' => 'Sudah Dibersihkan',
                'notes' => 'Lantai lobby sudah dipel dan diberi pewangi ruangan aroma lemon. Kaca pintu utama sudah dibersihkan.',
                'photo_before' => 'cleaning_photos/sample_before.svg',
                'photo_after' => 'cleaning_photos/sample_after.svg',
                'date' => $today->toDateString(),
                'time' => '07:30:00',
            ],
            [
                'area_idx' => 1, // Toilet Lantai 1
                'user' => $petugas2,
                'condition' => 'Bersih',
                'status' => 'Sudah Dibersihkan',
                'notes' => 'Wastafel dan kloset sudah disikat dengan desinfektan. Sabun cuci tangan dan tisu telah diisi ulang.',
                'photo_before' => 'cleaning_photos/sample_before.svg',
                'photo_after' => 'cleaning_photos/sample_after.svg',
                'date' => $today->toDateString(),
                'time' => '08:15:00',
            ],
            [
                'area_idx' => 2, // Toilet Lantai 2
                'user' => $petugas2,
                'condition' => 'Kotor',
                'status' => 'Sedang Dibersihkan',
                'notes' => 'Sedang proses pengepelan lantai dan pembersihan kerak wastafel.',
                'photo_before' => 'cleaning_photos/sample_before.svg',
                'photo_after' => null,
                'date' => $today->toDateString(),
                'time' => '09:00:00',
            ],
            [
                'area_idx' => 3, // Ruang Meeting VIP
                'user' => $petugas3,
                'condition' => 'Cukup Bersih',
                'status' => 'Sudah Dibersihkan',
                'notes' => 'Karpet divacuum, meja rapat dilap, dan tempat sampah telah dikosongkan untuk persiapan rapat direksi.',
                'photo_before' => 'cleaning_photos/sample_before.svg',
                'photo_after' => 'cleaning_photos/sample_after.svg',
                'date' => $today->toDateString(),
                'time' => '08:45:00',
            ],
            [
                'area_idx' => 4, // Ruang Kerja Utama
                'user' => $petugas1,
                'condition' => 'Cukup Bersih',
                'status' => 'Perlu Dibersihkan',
                'notes' => 'Ada tumpahan kopi di lorong meja tim marketing, perlu segera dipel basah.',
                'photo_before' => 'cleaning_photos/sample_before.svg',
                'photo_after' => null,
                'date' => $today->toDateString(),
                'time' => '10:00:00',
            ],
            [
                'area_idx' => 5, // Pantry
                'user' => $petugas3,
                'condition' => 'Bersih',
                'status' => 'Sudah Dibersihkan',
                'notes' => 'Sink cuci piring dibersihkan, kulkas dan microwave bagian luar telah dilap bersih.',
                'photo_before' => 'cleaning_photos/sample_before.svg',
                'photo_after' => 'cleaning_photos/sample_after.svg',
                'date' => $today->toDateString(),
                'time' => '07:45:00',
            ],
            [
                'area_idx' => 6, // Area Parkir
                'user' => $petugas1,
                'condition' => 'Kotor',
                'status' => 'Perlu Dibersihkan',
                'notes' => 'Banyak guguran daun kering akibat angin kencang semalam, perlu disapu.',
                'photo_before' => 'cleaning_photos/sample_before.svg',
                'photo_after' => null,
                'date' => $today->toDateString(),
                'time' => '06:45:00',
            ],
            [
                'area_idx' => 7, // Koridor
                'user' => $petugas2,
                'condition' => 'Bersih',
                'status' => 'Sudah Dibersihkan',
                'notes' => 'Koridor lantai 1 dan 2 selesai disapu dan dipel.',
                'photo_before' => 'cleaning_photos/sample_before.svg',
                'photo_after' => 'cleaning_photos/sample_after.svg',
                'date' => $today->toDateString(),
                'time' => '10:30:00',
            ],
            // Previous day entries for rich history and reports
            [
                'area_idx' => 0, // Lobby Kemarin
                'user' => $petugas1,
                'condition' => 'Bersih',
                'status' => 'Sudah Dibersihkan',
                'notes' => 'Pembersihan rutin sore hari selesai.',
                'photo_before' => 'cleaning_photos/sample_before.svg',
                'photo_after' => 'cleaning_photos/sample_after.svg',
                'date' => $today->copy()->subDay()->toDateString(),
                'time' => '16:00:00',
            ],
            [
                'area_idx' => 1, // Toilet Lantai 1 Kemarin
                'user' => $petugas2,
                'condition' => 'Bersih',
                'status' => 'Sudah Dibersihkan',
                'notes' => 'Pengecekan dan pembersihan sore hari.',
                'photo_before' => 'cleaning_photos/sample_before.svg',
                'photo_after' => 'cleaning_photos/sample_after.svg',
                'date' => $today->copy()->subDay()->toDateString(),
                'time' => '16:30:00',
            ],
            [
                'area_idx' => 5, // Pantry Kemarin
                'user' => $petugas3,
                'condition' => 'Sangat Kotor',
                'status' => 'Sudah Dibersihkan',
                'notes' => 'Sisa makanan di wastafel dibersihkan tuntas, bak sampah diganti kantong baru.',
                'photo_before' => 'cleaning_photos/sample_before.svg',
                'photo_after' => 'cleaning_photos/sample_after.svg',
                'date' => $today->copy()->subDays(2)->toDateString(),
                'time' => '17:15:00',
            ],
        ];

        foreach ($monitoringEntries as $entry) {
            CleaningMonitoring::create([
                'user_id' => $entry['user']->id,
                'area_id' => $areas[$entry['area_idx']]->id,
                'cleanliness_condition' => $entry['condition'],
                'status' => $entry['status'],
                'notes' => $entry['notes'],
                'photo_before' => $entry['photo_before'],
                'photo_after' => $entry['photo_after'],
                'check_date' => $entry['date'],
                'check_time' => $entry['time'],
            ]);
        }
    }
}
