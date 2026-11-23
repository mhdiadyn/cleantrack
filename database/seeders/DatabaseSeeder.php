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

        // 2. Create room records used by the facility gallery.

        $areasData = [
            // Lantai 1
            [
                'name' => 'Lobby Utama',
                'location' => 'Gedung A Lantai 1',
                'description' => 'Area pintu masuk utama, ruang tunggu tamu, dan meja resepsionis.',
                'floor' => 1,
                'pos_x' => 18,
                'pos_y' => 25,
                'photo' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Toilet Lantai 1',
                'location' => 'Gedung A Lantai 1 (Dekat Tangga)',
                'description' => 'Toilet pria dan wanita lantai 1 serta wastafel.',
                'floor' => 1,
                'pos_x' => 82,
                'pos_y' => 25,
                'photo' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Ruang Kerja Utama',
                'location' => 'Gedung A Lantai 1 (Tengah)',
                'description' => 'Open space workstation karyawan dan area printer.',
                'floor' => 1,
                'pos_x' => 50,
                'pos_y' => 25,
                'photo' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Pantry & Ruang Makan',
                'location' => 'Gedung A Lantai 1 (Belakang)',
                'description' => 'Dapur bersih, lemari es, microwave, dan meja makan.',
                'floor' => 1,
                'pos_x' => 18,
                'pos_y' => 75,
                'photo' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Gudang Logistik',
                'location' => 'Gedung A Lantai 1 (Sayap Kanan)',
                'description' => 'Penyimpanan stok barang perlengkapan kantor dan alat kebersihan.',
                'floor' => 1,
                'pos_x' => 82,
                'pos_y' => 75,
                'photo' => null,
                'is_active' => true,
            ],

            // Lantai 2
            [
                'name' => 'Ruang Meeting VIP',
                'location' => 'Gedung A Lantai 2 (Ruang 201)',
                'description' => 'Ruang rapat kapasitas 12 orang dengan proyektor & karpet.',
                'floor' => 2,
                'pos_x' => 20,
                'pos_y' => 30,
                'photo' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Toilet Lantai 2',
                'location' => 'Gedung A Lantai 2 (Sayap Kiri)',
                'description' => 'Toilet pria dan wanita lantai 2.',
                'floor' => 2,
                'pos_x' => 80,
                'pos_y' => 30,
                'photo' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Ruang Server & IT',
                'location' => 'Gedung A Lantai 2 (Ruang 202)',
                'description' => 'Pusat rack server, pendingin khusus, dan perangkat jaringan.',
                'floor' => 2,
                'pos_x' => 50,
                'pos_y' => 70,
                'photo' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Ruang Direktur',
                'location' => 'Gedung A Lantai 2 (Ruang 203)',
                'description' => 'Ruang kerja pimpinan utama dan ruang tamu privat.',
                'floor' => 2,
                'pos_x' => 80,
                'pos_y' => 70,
                'photo' => null,
                'is_active' => true,
            ],

            // Lantai 3
            [
                'name' => 'Auditorium & Hall Serbaguna',
                'location' => 'Gedung A Lantai 3 (Ruang 301)',
                'description' => 'Aula besar untuk seminar, training karyawan, dan presentasi gabungan.',
                'floor' => 3,
                'pos_x' => 25,
                'pos_y' => 35,
                'photo' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Ruang Rapat Eksekutif',
                'location' => 'Gedung A Lantai 3 (Ruang 302)',
                'description' => 'Ruang rapat dewan komisaris dan telekonferensi internasional.',
                'floor' => 3,
                'pos_x' => 75,
                'pos_y' => 35,
                'photo' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Ruang Kreatif & Studio',
                'location' => 'Gedung A Lantai 3 (Ruang 303)',
                'description' => 'Studio multimedia, podcast, editing, dan photoshoot produk.',
                'floor' => 3,
                'pos_x' => 30,
                'pos_y' => 75,
                'photo' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Toilet Lantai 3',
                'location' => 'Gedung A Lantai 3 (Sayap Timur)',
                'description' => 'Toilet pria/wanita lantai 3 dengan fasilitas disabilitas.',
                'floor' => 3,
                'pos_x' => 80,
                'pos_y' => 75,
                'photo' => null,
                'is_active' => true,
            ],

            // Lantai 4
            [
                'name' => 'Rooftop Lounge & Cafetaria',
                'location' => 'Gedung A Lantai 4 (Area Terbuka)',
                'description' => 'Area rooftop terbuka semi-outdoor untuk istirahat dan coffee break.',
                'floor' => 4,
                'pos_x' => 25,
                'pos_y' => 35,
                'photo' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Ruang Pusat Kontrol & CCTV',
                'location' => 'Gedung A Lantai 4 (Ruang 401)',
                'description' => 'Pusat pemantauan keamanan gedung dan alarm terpusat.',
                'floor' => 4,
                'pos_x' => 75,
                'pos_y' => 35,
                'photo' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Ruang Arsip & Dokumen',
                'location' => 'Gedung A Lantai 4 (Ruang 402)',
                'description' => 'Ruangan penyimpanan berkas fisik legal dan dokumen akuntansi.',
                'floor' => 4,
                'pos_x' => 30,
                'pos_y' => 75,
                'photo' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Toilet & Shower Lantai 4',
                'location' => 'Gedung A Lantai 4 (Sayap Barat)',
                'description' => 'Toilet dan kamar mandi shower lantai 4.',
                'floor' => 4,
                'pos_x' => 80,
                'pos_y' => 75,
                'photo' => null,
                'is_active' => true,
            ],
        ];

        $areas = [];
        foreach ($areasData as $data) {
            unset($data['photo']);
            $areas[] = Area::updateOrCreate(['name' => $data['name']], $data);
        }

        // 3. Create Sample Photos in Storage if not exist
        if (! Storage::disk('public')->exists('cleaning_photos')) {
            Storage::disk('public')->makeDirectory('cleaning_photos');
        }

        // Create sample placeholder images for realistic demo
        $dummyBeforeSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="600" height="400" viewBox="0 0 600 400"><rect width="600" height="400" fill="#fee2e2"/><text x="50%" y="45%" dominant-baseline="middle" text-anchor="middle" font-family="sans-serif" font-size="24" font-weight="bold" fill="#991b1b">KONDISI SEBELUM DIBERSIHKAN</text><text x="50%" y="60%" dominant-baseline="middle" text-anchor="middle" font-family="sans-serif" font-size="16" fill="#b91c1c">Debu, noda lantai &amp; sampah belum dibersihkan</text></svg>';
        $dummyAfterSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="600" height="400" viewBox="0 0 600 400"><rect width="600" height="400" fill="#dcfce7"/><text x="50%" y="45%" dominant-baseline="middle" text-anchor="middle" font-family="sans-serif" font-size="24" font-weight="bold" fill="#166534">KONDISI SESUDAH DIBERSIHKAN</text><text x="50%" y="60%" dominant-baseline="middle" text-anchor="middle" font-family="sans-serif" font-size="16" fill="#15803d">Lantai telah dipel, wangi dan rapi</text></svg>';

        Storage::disk('public')->put('cleaning_photos/sample_before.svg', $dummyBeforeSvg);
        Storage::disk('public')->put('cleaning_photos/sample_after.svg', $dummyAfterSvg);

        // 4. Create Monitoring Data for today and previous days
        $today = Carbon::today();

        $monitoringEntries = [
            [
                'area_idx' => 0, // Lobby Lt 1
                'user' => $petugas1,
                'condition' => 'Bersih',
                'status' => 'Sudah Dibersihkan',
                'start_time' => '07:00:00',
                'end_time' => '07:30:00',
                'notes' => 'Lantai lobby sudah dipel dan diberi pewangi ruangan aroma lemon. Kaca pintu utama sudah dibersihkan.',
                'photo_before' => 'cleaning_photos/sample_before.svg',
                'photo_after' => 'cleaning_photos/sample_after.svg',
                'photo' => null,
                'date' => $today->toDateString(),
                'time' => '07:30:00',
            ],
            [
                'area_idx' => 1, // Toilet Lt 1
                'user' => $petugas2,
                'condition' => 'Bersih',
                'status' => 'Sudah Dibersihkan',
                'start_time' => '07:45:00',
                'end_time' => '08:15:00',
                'notes' => 'Wastafel dan kloset sudah disikat dengan desinfektan. Sabun cuci tangan dan tisu telah diisi ulang.',
                'photo_before' => 'cleaning_photos/sample_before.svg',
                'photo_after' => 'cleaning_photos/sample_after.svg',
                'photo' => null,
                'date' => $today->toDateString(),
                'time' => '08:15:00',
            ],
            [
                'area_idx' => 2, // Ruang Kerja Utama Lt 1
                'user' => $petugas1,
                'condition' => 'Cukup Bersih',
                'status' => 'Perlu Dibersihkan',
                'start_time' => '09:30:00',
                'end_time' => '10:00:00',
                'notes' => 'Ada tumpahan teh di lorong meja tim marketing, perlu segera dipel basah.',
                'photo_before' => 'cleaning_photos/sample_before.svg',
                'photo_after' => null,
                'photo' => null,
                'date' => $today->toDateString(),
                'time' => '10:00:00',
            ],
            [
                'area_idx' => 3, // Pantry Lt 1
                'user' => $petugas3,
                'condition' => 'Bersih',
                'status' => 'Sudah Dibersihkan',
                'start_time' => '07:15:00',
                'end_time' => '07:45:00',
                'notes' => 'Sink cuci piring dibersihkan, kulkas dan microwave bagian luar telah dilap bersih.',
                'photo_before' => 'cleaning_photos/sample_before.svg',
                'photo_after' => 'cleaning_photos/sample_after.svg',
                'photo' => null,
                'date' => $today->toDateString(),
                'time' => '07:45:00',
            ],
            [
                'area_idx' => 5, // Ruang Meeting VIP Lt 2
                'user' => $petugas3,
                'condition' => 'Bersih',
                'status' => 'Sudah Dibersihkan',
                'start_time' => '08:10:00',
                'end_time' => '08:45:00',
                'notes' => 'Karpet divacuum, meja rapat dilap, dan tempat sampah telah dikosongkan untuk persiapan rapat direksi.',
                'photo_before' => 'cleaning_photos/sample_before.svg',
                'photo_after' => 'cleaning_photos/sample_after.svg',
                'photo' => null,
                'date' => $today->toDateString(),
                'time' => '08:45:00',
            ],
            [
                'area_idx' => 6, // Toilet Lantai 2
                'user' => $petugas2,
                'condition' => 'Kotor',
                'status' => 'Sedang Dibersihkan',
                'start_time' => '08:40:00',
                'end_time' => '09:10:00',
                'notes' => 'Sedang proses pengepelan lantai dan pembersihan kerak wastafel.',
                'photo_before' => 'cleaning_photos/sample_before.svg',
                'photo_after' => null,
                'photo' => null,
                'date' => $today->toDateString(),
                'time' => '09:00:00',
            ],
            [
                'area_idx' => 7, // Ruang Server Lt 2
                'user' => $petugas1,
                'condition' => 'Bersih',
                'status' => 'Sudah Dibersihkan',
                'start_time' => '10:30:00',
                'end_time' => '11:00:00',
                'notes' => 'Debu pada panel filter AC dibersihkan hati-hati.',
                'photo_before' => 'cleaning_photos/sample_before.svg',
                'photo_after' => 'cleaning_photos/sample_after.svg',
                'photo' => null,
                'date' => $today->toDateString(),
                'time' => '11:00:00',
            ],
            [
                'area_idx' => 8, // Ruang Direktur Lt 2
                'user' => $petugas3,
                'condition' => 'Bersih',
                'status' => 'Sudah Dibersihkan',
                'start_time' => '07:30:00',
                'end_time' => '08:00:00',
                'notes' => 'Selesai pembersihan pagi hari, meja dan kursi disterilisasi.',
                'photo_before' => 'cleaning_photos/sample_before.svg',
                'photo_after' => 'cleaning_photos/sample_after.svg',
                'photo' => null,
                'date' => $today->toDateString(),
                'time' => '08:00:00',
            ],
            [
                'area_idx' => 9, // Auditorium Lt 3
                'user' => $petugas1,
                'condition' => 'Bersih',
                'status' => 'Sudah Dibersihkan',
                'start_time' => '08:30:00',
                'end_time' => '09:15:00',
                'notes' => 'Panggung dan barisan kursi telah disapu dan dirapikan, sound system bebas debu.',
                'photo_before' => 'cleaning_photos/sample_before.svg',
                'photo_after' => 'cleaning_photos/sample_after.svg',
                'photo' => null,
                'date' => $today->toDateString(),
                'time' => '09:15:00',
            ],
            [
                'area_idx' => 13, // Rooftop Lounge Lt 4
                'user' => $petugas2,
                'condition' => 'Bersih',
                'status' => 'Sudah Dibersihkan',
                'start_time' => '09:00:00',
                'end_time' => '09:40:00',
                'notes' => 'Area tanaman disiram, meja santai dilap bersih dari debu luar ruangan.',
                'photo_before' => 'cleaning_photos/sample_before.svg',
                'photo_after' => 'cleaning_photos/sample_after.svg',
                'photo' => null,
                'date' => $today->toDateString(),
                'time' => '09:40:00',
            ],
        ];

        foreach ($monitoringEntries as $entry) {
            CleaningMonitoring::create([
                'user_id' => $entry['user']->id,
                'area_id' => $areas[$entry['area_idx']]->id,
                'cleanliness_condition' => $entry['condition'],
                'status' => $entry['status'],
                'start_time' => $entry['start_time'] ?? null,
                'end_time' => $entry['end_time'] ?? null,
                'notes' => $entry['notes'],
                'photo_before' => $entry['photo_before'],
                'photo_after' => $entry['photo_after'],
                'room_photo' => $entry['photo'],
                'check_date' => $entry['date'],
                'check_time' => $entry['time'],
            ]);
        }
    }
}
