<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\CleaningMonitoring;
use App\Models\FloorPlan;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CleanTrackFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_login_page_is_accessible_and_shows_cleantrack_brand(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('CLEAN');
        $response->assertSee('TRACK');
        $response->assertSee('Sistem Monitoring');
        $response->assertSee('Mhdiadyn_');
        $response->assertSee('Light');
        $response->assertSee('Dark');
        $response->assertSee('placeholder="Masukkan alamat email"', false);
        $response->assertDontSee('nama@cleantrack.test');
        $response->assertDontSee('Mode Light');
        $response->assertDontSee('Mode Dark');
    }

    public function test_admin_can_login_with_correct_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@cleantrack.test',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_all_three_officers_can_login_individually_with_their_own_credentials(): void
    {
        // 1. Petugas Andi
        $responseAndi = $this->post('/login', [
            'email' => 'andi@cleantrack.test',
            'password' => 'password123',
        ]);
        $responseAndi->assertRedirect(route('petugas.dashboard'));
        $this->assertAuthenticatedAs(User::where('email', 'andi@cleantrack.test')->first());

        // Logout
        $this->post('/logout');
        $this->assertGuest();

        // 2. Petugas Budi
        $responseBudi = $this->post('/login', [
            'email' => 'budi@cleantrack.test',
            'password' => 'password123',
        ]);
        $responseBudi->assertRedirect(route('petugas.dashboard'));
        $this->assertAuthenticatedAs(User::where('email', 'budi@cleantrack.test')->first());

        // Logout
        $this->post('/logout');
        $this->assertGuest();

        // 3. Petugas Citra
        $responseCitra = $this->post('/login', [
            'email' => 'citra@cleantrack.test',
            'password' => 'password123',
        ]);
        $responseCitra->assertRedirect(route('petugas.dashboard'));
        $this->assertAuthenticatedAs(User::where('email', 'citra@cleantrack.test')->first());
    }

    public function test_inactive_user_cannot_login(): void
    {
        $inactiveUser = User::create([
            'name' => 'Petugas Nonaktif',
            'email' => 'nonaktif@cleantrack.test',
            'password' => Hash::make('password123'),
            'role' => 'petugas',
            'status' => 'nonaktif',
        ]);

        $response = $this->post('/login', [
            'email' => 'nonaktif@cleantrack.test',
            'password' => 'password123',
        ]);

        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_petugas_cannot_access_admin_dashboard_or_admin_users(): void
    {
        $petugas = User::where('email', 'andi@cleantrack.test')->first();

        $responseDashboard = $this->actingAs($petugas)->get(route('admin.dashboard'));
        $responseDashboard->assertRedirect(route('petugas.dashboard'));

        $responseUsers = $this->actingAs($petugas)->get(route('admin.users.index'));
        $responseUsers->assertRedirect(route('petugas.dashboard'));
    }

    public function test_admin_can_create_new_petugas_and_petugas_can_login_immediately(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Petugas Baru Doni',
            'email' => 'doni@cleantrack.test',
            'password' => 'password123',
            'role' => 'petugas',
            'status' => 'aktif',
            'phone' => '081234567899',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'doni@cleantrack.test',
            'role' => 'petugas',
            'status' => 'aktif',
        ]);

        // Logout admin and test newly created user login
        $this->post('/logout');

        $loginResponse = $this->post('/login', [
            'email' => 'doni@cleantrack.test',
            'password' => 'password123',
        ]);

        $loginResponse->assertRedirect(route('petugas.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_monitoring_records_automatically_assign_authenticated_officer_id(): void
    {
        Storage::fake('public');
        $petugas = User::where('email', 'andi@cleantrack.test')->first();
        $area = Area::first();

        $photoBefore = UploadedFile::fake()->image('before.jpg', 600, 400);
        $photoAfter = UploadedFile::fake()->image('after.jpg', 600, 400);

        $response = $this->actingAs($petugas)->post(route('monitoring.store'), [
            'area_id' => $area->id,
            'cleanliness_condition' => 'Bersih',
            'status' => 'Sudah Dibersihkan',
            'notes' => 'Pemeriksaan kebersihan selesai dengan foto kamera.',
            'photo_before' => $photoBefore,
            'photo_after' => $photoAfter,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('cleaning_monitorings', [
            'user_id' => $petugas->id,
            'area_id' => $area->id,
            'cleanliness_condition' => 'Bersih',
            'status' => 'Sudah Dibersihkan',
            'notes' => 'Pemeriksaan kebersihan selesai dengan foto kamera.',
        ]);

        $monitoring = CleaningMonitoring::where('user_id', $petugas->id)->latest('id')->first();
        $this->assertNotNull($monitoring->photo_before);
        $this->assertNotNull($monitoring->photo_after);
        Storage::disk('public')->assertExists($monitoring->photo_before);
        Storage::disk('public')->assertExists($monitoring->photo_after);
    }

    public function test_admin_can_save_a_custom_area_name_for_one_inspection(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $response = $this->actingAs($admin)->post(route('monitoring.store'), [
            'custom_area_name' => 'Area Parkir Samping',
            'cleanliness_condition' => 'Bersih',
            'status' => 'Sudah Dibersihkan',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('cleaning_monitorings', [
            'user_id' => $admin->id,
            'area_id' => null,
            'custom_area_name' => 'Area Parkir Samping',
        ]);
        $this->assertDatabaseMissing('areas', ['name' => 'Area Parkir Samping']);

        $monitoring = CleaningMonitoring::where('user_id', $admin->id)->where('custom_area_name', 'Area Parkir Samping')->firstOrFail();
        $this->get(route('monitoring.show', $monitoring))->assertSee('Area Parkir Samping');
    }

    public function test_petugas_can_save_a_custom_area_name_for_one_inspection(): void
    {
        $petugas = User::where('role', 'petugas')->firstOrFail();

        $response = $this->actingAs($petugas)->post(route('monitoring.store'), [
            'custom_area_name' => 'Gudang Peralatan Barat',
            'cleanliness_condition' => 'Bersih',
            'status' => 'Sudah Dibersihkan',
        ]);

        $response->assertRedirect(route('petugas.my-monitoring'));
        $this->assertDatabaseHas('cleaning_monitorings', [
            'user_id' => $petugas->id,
            'area_id' => null,
            'custom_area_name' => 'Gudang Peralatan Barat',
        ]);
        $this->assertDatabaseMissing('areas', ['name' => 'Gudang Peralatan Barat']);

        $this->get(route('petugas.my-monitoring'))->assertSee('Gudang Peralatan Barat');
    }

    public function test_monitoring_requires_an_existing_or_custom_area_name(): void
    {
        $petugas = User::where('role', 'petugas')->firstOrFail();
        $monitoringCount = CleaningMonitoring::count();

        $response = $this->actingAs($petugas)->post(route('monitoring.store'), [
            'cleanliness_condition' => 'Bersih',
            'status' => 'Sudah Dibersihkan',
        ]);

        $response->assertSessionHasErrors('custom_area_name');
        $this->assertSame($monitoringCount, CleaningMonitoring::count());
    }

    public function test_petugas_only_sees_their_own_history_in_my_monitoring(): void
    {
        $andi = User::where('email', 'andi@cleantrack.test')->first();
        $budi = User::where('email', 'budi@cleantrack.test')->first();
        $area = Area::first();

        // Create specific entry for Andi
        $monitoringAndi = CleaningMonitoring::create([
            'user_id' => $andi->id,
            'area_id' => $area->id,
            'cleanliness_condition' => 'Bersih',
            'status' => 'Sudah Dibersihkan',
            'notes' => 'Catatan Khusus Andi',
            'check_date' => now()->toDateString(),
            'check_time' => '08:00:00',
        ]);

        // Create specific entry for Budi
        $monitoringBudi = CleaningMonitoring::create([
            'user_id' => $budi->id,
            'area_id' => $area->id,
            'cleanliness_condition' => 'Kotor',
            'status' => 'Perlu Dibersihkan',
            'notes' => 'Catatan Khusus Budi',
            'check_date' => now()->toDateString(),
            'check_time' => '08:30:00',
        ]);

        // Andi accesses my-monitoring
        $responseAndi = $this->actingAs($andi)->get(route('petugas.my-monitoring'));
        $responseAndi->assertStatus(200);
        $responseAndi->assertSee('Catatan Khusus Andi');
        $responseAndi->assertDontSee('Catatan Khusus Budi');

        // Budi accesses my-monitoring
        $responseBudi = $this->actingAs($budi)->get(route('petugas.my-monitoring'));
        $responseBudi->assertStatus(200);
        $responseBudi->assertSee('Catatan Khusus Budi');
        $responseBudi->assertDontSee('Catatan Khusus Andi');
    }

    public function test_admin_can_view_all_monitoring_records_and_reports(): void
    {
        $admin = User::where('role', 'admin')->first();

        $responseIndex = $this->actingAs($admin)->get(route('admin.monitoring.index'));
        $responseIndex->assertStatus(200);
        $responseIndex->assertSee('Riwayat Seluruh Monitoring');

        $responseReport = $this->actingAs($admin)->get(route('admin.reports.index'));
        $responseReport->assertStatus(200);
        $responseReport->assertSee('Laporan & Rekapitulasi Kebersihan');

        $responsePrint = $this->actingAs($admin)->get(route('admin.reports.print'));
        $responsePrint->assertStatus(200);
        $responsePrint->assertSee('Laporan Rekapitulasi Monitoring Kebersihan');
    }

    public function test_facility_gallery_is_available_to_admin_and_petugas_for_four_floors(): void
    {
        $admin = User::where('role', 'admin')->first();
        $petugas = User::where('email', 'andi@cleantrack.test')->first();

        foreach ([$admin, $petugas] as $user) {
            $response = $this->actingAs($user)->get(route('facility.index'));
            $response->assertOk();
            $response->assertSee('Denah & Galeri Ruangan');
            $response->assertSee('Lantai 1');
            $response->assertSee('Lantai 2');
            $response->assertSee('Lantai 3');
            $response->assertSee('Lantai 4');
            $response->assertDontSee('360°');
            $response->assertDontSee('floorplan.data');
        }

        foreach ([1, 2, 3, 4] as $floor) {
            $response = $this->actingAs($petugas)->get(route('facility.index', ['floor' => $floor]));
            $response->assertOk();
            $response->assertSee("Lantai {$floor}");
            $response->assertSee('DEMO / SEMENTARA');
            $response->assertSee('role="button" tabindex="0"', false);

            foreach (config("facility_demo.floors.{$floor}") as $room) {
                $response->assertSee($room['name']);
            }
        }

        $this->actingAs($petugas)->get(route('facility.index', ['floor' => 5]))->assertNotFound();
    }

    public function test_interactive_room_details_use_the_latest_monitoring_and_keep_photos(): void
    {
        Storage::fake('public');
        $petugas = User::where('email', 'andi@cleantrack.test')->firstOrFail();
        $area = Area::create([
            'name' => 'Area Uji Denah Interaktif',
            'location' => 'Gedung DEMO',
            'floor' => '3',
            'description' => 'Deskripsi area uji yang tampil pada detail.',
        ]);
        $needsCleaningArea = Area::create([
            'name' => 'Area Perlu Dibersihkan',
            'location' => 'Gedung DEMO',
            'floor' => '3',
        ]);
        $uninspectedArea = Area::create([
            'name' => 'Area Belum Diperiksa',
            'location' => 'Gedung DEMO',
            'floor' => '3',
        ]);
        $paths = [
            'monitoring/test-before.jpg',
            'monitoring/test-after.jpg',
            'monitoring/test-room.jpg',
        ];

        foreach ($paths as $path) {
            Storage::disk('public')->put($path, 'photo data');
        }

        CleaningMonitoring::create([
            'user_id' => $petugas->id,
            'area_id' => $area->id,
            'cleanliness_condition' => 'Bersih',
            'status' => 'Sudah Dibersihkan',
            'check_date' => '2026-11-17',
            'check_time' => '10:00:00',
            'photo_before' => $paths[0],
            'photo_after' => $paths[1],
            'room_photo' => $paths[2],
        ]);
        CleaningMonitoring::create([
            'user_id' => $petugas->id,
            'area_id' => $area->id,
            'cleanliness_condition' => 'Kotor',
            'status' => 'Perlu Dibersihkan',
            'check_date' => '2026-11-16',
            'check_time' => '11:00:00',
        ]);
        CleaningMonitoring::create([
            'user_id' => $petugas->id,
            'area_id' => $needsCleaningArea->id,
            'cleanliness_condition' => 'Kotor',
            'status' => 'Perlu Dibersihkan',
            'check_date' => '2026-11-17',
            'check_time' => '09:00:00',
        ]);

        $response = $this->actingAs($petugas)->get(route('facility.index', ['floor' => 3]));

        $response->assertOk();
        $response->assertSee('Area Uji Denah Interaktif');
        $response->assertSee('Deskripsi area uji yang tampil pada detail.');
        $response->assertSee('Bersih');
        $response->assertSee('<title>Area Uji Denah Interaktif — Bersih</title>', false);
        $response->assertSee('<title>Area Perlu Dibersihkan — Perlu dibersihkan</title>', false);
        $response->assertSee('<title>Area Belum Diperiksa — Belum diperiksa</title>', false);
        $response->assertSee('17/11/2026');
        $response->assertSee($petugas->name);
        $response->assertSee('role="button" tabindex="0"', false);

        foreach ($paths as $path) {
            $response->assertSee(Storage::disk('public')->url($path), false);
        }

        foreach ($paths as $path) {
            Storage::disk('public')->assertExists($path);
        }
    }

    public function test_admin_can_upload_and_replace_independent_floor_plans(): void
    {
        Storage::fake('public');
        $admin = User::where('role', 'admin')->first();

        $this->actingAs($admin)->post(route('admin.floor-plans.store', ['floor' => 1]), [
            'floor_plan' => UploadedFile::fake()->image('floor-one.jpg'),
        ])->assertRedirect(route('facility.index', ['floor' => 1]));

        $this->actingAs($admin)->post(route('admin.floor-plans.store', ['floor' => 2]), [
            'floor_plan' => UploadedFile::fake()->createWithContent('floor-two.pdf', '%PDF-1.4 test document'),
        ])->assertRedirect(route('facility.index', ['floor' => 2]));

        $floorOne = FloorPlan::where('floor', 1)->firstOrFail();
        $floorTwo = FloorPlan::where('floor', 2)->firstOrFail();
        $this->assertNotSame($floorOne->file_path, $floorTwo->file_path);
        Storage::disk('public')->assertExists($floorOne->file_path);
        Storage::disk('public')->assertExists($floorTwo->file_path);

        $oldFloorOnePath = $floorOne->file_path;
        $this->actingAs($admin)->post(route('admin.floor-plans.store', ['floor' => 1]), [
            'floor_plan' => UploadedFile::fake()->image('replacement.jpg'),
        ])->assertRedirect(route('facility.index', ['floor' => 1]));

        $this->assertSame(1, FloorPlan::where('floor', 1)->count());
        $this->assertSame(2, FloorPlan::count());
        $this->assertNotSame($oldFloorOnePath, $floorOne->fresh()->file_path);
        Storage::disk('public')->assertMissing($oldFloorOnePath);
        Storage::disk('public')->assertExists($floorTwo->fresh()->file_path);
    }

    public function test_petugas_cannot_upload_floor_plans_or_manage_room_photos(): void
    {
        Storage::fake('public');
        $petugas = User::where('email', 'andi@cleantrack.test')->first();
        $area = Area::first();

        $this->actingAs($petugas)->post(route('admin.floor-plans.store', ['floor' => 1]), [
            'floor_plan' => UploadedFile::fake()->image('unauthorized.jpg'),
        ])->assertRedirect(route('petugas.dashboard'));

        $this->actingAs($petugas)->post(route('admin.areas.photo.store', $area), [
            'photo' => UploadedFile::fake()->image('unauthorized.jpg'),
        ])->assertRedirect(route('petugas.dashboard'));

        $this->actingAs($petugas)->delete(route('admin.areas.photo.destroy', $area))
            ->assertRedirect(route('petugas.dashboard'));
        $this->assertNull($area->fresh()->photo);
    }

    public function test_admin_can_upload_replace_and_delete_a_standard_room_photo(): void
    {
        Storage::fake('public');
        $admin = User::where('role', 'admin')->first();
        $area = Area::firstOrFail();

        $this->actingAs($admin)->post(route('admin.areas.photo.store', $area), [
            'photo' => UploadedFile::fake()->image('room-photo.jpg'),
        ])->assertRedirect();

        $area->refresh();
        $firstPhoto = $area->photo;
        $this->assertNotNull($firstPhoto);
        Storage::disk('public')->assertExists($firstPhoto);

        $this->actingAs($admin)->post(route('admin.areas.photo.store', $area), [
            'photo' => UploadedFile::fake()->image('replacement-room-photo.png'),
        ])->assertRedirect();

        $area->refresh();
        $this->assertNotSame($firstPhoto, $area->photo);
        Storage::disk('public')->assertMissing($firstPhoto);
        $replacementPhoto = $area->photo;
        Storage::disk('public')->assertExists($replacementPhoto);

        $this->actingAs($admin)->delete(route('admin.areas.photo.destroy', $area))
            ->assertRedirect();
        $this->assertNull($area->fresh()->photo);
        Storage::disk('public')->assertMissing($replacementPhoto);
    }

    public function test_admin_can_export_report_to_excel_and_word(): void
    {
        $admin = User::where('role', 'admin')->first();

        // Excel export
        $excelResponse = $this->actingAs($admin)->get(route('admin.reports.excel'));
        $excelResponse->assertStatus(200);
        $this->assertStringContainsString('vnd.ms-excel', $excelResponse->headers->get('content-type'));
        $excelResponse->assertSee('CLEANTRACK - SISTEM MONITORING KEBERSIHAN GEDUNG');

        // Word export
        $wordResponse = $this->actingAs($admin)->get(route('admin.reports.word'));
        $wordResponse->assertStatus(200);
        $this->assertStringContainsString('application/msword', $wordResponse->headers->get('content-type'));
        $wordResponse->assertSee('CLEANTRACK FACILITY MANAGEMENT');
    }

    public function test_floor_plan_pdf_is_previewed_for_the_selected_floor(): void
    {
        $admin = User::where('role', 'admin')->first();
        Storage::fake('public');

        $this->actingAs($admin)->post(route('admin.floor-plans.store', ['floor' => 3]), [
            'floor_plan' => UploadedFile::fake()->createWithContent('floor-three.pdf', '%PDF-1.4 test document'),
        ])->assertRedirect(route('facility.index', ['floor' => 3]));

        $floorPlan = FloorPlan::where('floor', 3)->firstOrFail();
        $response = $this->actingAs($admin)->get(route('facility.index', ['floor' => 3]));
        $response->assertOk();
        $response->assertSee('Dokumen PDF');
        $response->assertSee(Storage::disk('public')->url($floorPlan->file_path), false);
        $response->assertSee('Lantai 3');
    }

    public function test_legacy_360_columns_are_removed_without_dropping_room_photo_paths(): void
    {
        $area = Area::firstOrFail();
        $monitoring = CleaningMonitoring::firstOrFail();
        $legacyAreaPhotoPath = 'areas_360/legacy-area.jpg';
        $legacyMonitoringPhotoPath = 'monitoring_360/legacy-monitoring.jpg';

        $area->update(['photo' => $legacyAreaPhotoPath]);
        $monitoring->update(['room_photo' => $legacyMonitoringPhotoPath]);

        $this->assertFalse(Schema::hasColumn('areas', 'photo_360'));
        $this->assertFalse(Schema::hasColumn('areas', 'photo_type'));
        $this->assertFalse(Schema::hasColumn('cleaning_monitorings', 'photo_360'));
        $this->assertFalse(Schema::hasColumn('cleaning_monitorings', 'photo_360_type'));
        $this->assertSame($legacyAreaPhotoPath, $area->fresh()->photo);
        $this->assertSame($legacyMonitoringPhotoPath, $monitoring->fresh()->room_photo);
    }

    public function test_monitoring_records_start_and_end_time_and_calculates_duration(): void
    {
        Storage::fake('public');
        $petugas = User::where('email', 'andi@cleantrack.test')->first();
        $area = Area::first();

        $response = $this->actingAs($petugas)->post(route('monitoring.store'), [
            'area_id' => $area->id,
            'cleanliness_condition' => 'Bersih',
            'status' => 'Sudah Dibersihkan',
            'start_time' => '08:00',
            'end_time' => '08:35',
            'notes' => 'Pembersihan pagi hari selesai tepat waktu.',
            'photo_before' => UploadedFile::fake()->image('before.jpg'),
            'photo_after' => UploadedFile::fake()->image('after.jpg'),
        ]);

        $response->assertRedirect(route('petugas.my-monitoring'));

        $monitoring = CleaningMonitoring::where('area_id', $area->id)->latest('id')->first();
        $this->assertNotNull($monitoring);
        $this->assertEquals('08:00:00', $monitoring->start_time);
        $this->assertEquals('08:35:00', $monitoring->end_time);
        $this->assertEquals('35 Menit', $monitoring->duration_text);
        $this->assertNotNull($monitoring->photo_before);
        $this->assertNotNull($monitoring->photo_after);
        Storage::disk('public')->assertExists($monitoring->photo_before);
        Storage::disk('public')->assertExists($monitoring->photo_after);
    }

    public function test_room_gallery_displays_room_and_monitoring_photos_for_both_roles(): void
    {
        Storage::fake('public');
        $admin = User::where('role', 'admin')->first();
        $petugas = User::where('email', 'andi@cleantrack.test')->first();
        $area = Area::firstOrFail();
        $monitoring = CleaningMonitoring::where('area_id', $area->id)->firstOrFail();
        $areaPhoto = UploadedFile::fake()->image('existing-room-photo.jpg')->store('room-photos', 'public');
        $roomPhoto = UploadedFile::fake()->image('legacy-room-photo.jpg')->store('monitoring-photos', 'public');
        $photoBefore = UploadedFile::fake()->image('before.jpg')->store('monitoring-photos', 'public');
        $photoAfter = UploadedFile::fake()->image('after.jpg')->store('monitoring-photos', 'public');
        $area->update(['photo' => $areaPhoto]);
        $monitoring->update([
            'room_photo' => $roomPhoto,
            'photo_before' => $photoBefore,
            'photo_after' => $photoAfter,
        ]);

        foreach ([$admin, $petugas] as $user) {
            $response = $this->actingAs($user)->get(route('facility.index', ['floor' => $area->floor]));
            $response->assertOk();
            $response->assertSee(Storage::disk('public')->url($areaPhoto), false);
            $response->assertSee(Storage::disk('public')->url($roomPhoto), false);
            $response->assertSee(Storage::disk('public')->url($photoBefore), false);
            $response->assertSee(Storage::disk('public')->url($photoAfter), false);
        }
    }

    public function test_user_can_view_profile_page(): void
    {
        $petugas = User::where('email', 'andi@cleantrack.test')->first();

        $response = $this->actingAs($petugas)->get(route('profile.edit'));
        $response->assertStatus(200);
        $response->assertSee($petugas->name);
        $response->assertSee($petugas->email);
        $response->assertSee('Pengaturan Profil');
    }

    public function test_user_can_update_profile_information(): void
    {
        $petugas = User::where('email', 'andi@cleantrack.test')->first();

        $response = $this->actingAs($petugas)->patch(route('profile.update'), [
            'name' => 'Andi Pratama Update',
            'email' => 'andi.baru@cleantrack.test',
            'phone' => '089988776655',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $petugas->refresh();
        $this->assertEquals('Andi Pratama Update', $petugas->name);
        $this->assertEquals('andi.baru@cleantrack.test', $petugas->email);
        $this->assertEquals('089988776655', $petugas->phone);
    }

    public function test_user_can_change_password_with_correct_current_password(): void
    {
        $petugas = User::where('email', 'andi@cleantrack.test')->first();

        $response = $this->actingAs($petugas)->patch(route('profile.update'), [
            'name' => $petugas->name,
            'email' => $petugas->email,
            'current_password' => 'password123',
            'new_password' => 'secretbaru123',
            'new_password_confirmation' => 'secretbaru123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $petugas->refresh();
        $this->assertTrue(Hash::check('secretbaru123', $petugas->password));
    }

    public function test_user_cannot_change_password_with_wrong_current_password(): void
    {
        $petugas = User::where('email', 'andi@cleantrack.test')->first();

        $response = $this->actingAs($petugas)->patch(route('profile.update'), [
            'name' => $petugas->name,
            'email' => $petugas->email,
            'current_password' => 'salah_password',
            'new_password' => 'secretbaru123',
            'new_password_confirmation' => 'secretbaru123',
        ]);

        $response->assertSessionHasErrors('current_password');
        $petugas->refresh();
        $this->assertTrue(Hash::check('password123', $petugas->password));
    }

    public function test_admin_users_show_route_redirects_to_edit(): void
    {
        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'petugas')->first();

        $response = $this->actingAs($admin)->get(route('admin.users.show', $user));
        $response->assertRedirect(route('admin.users.edit', $user));
    }

    public function test_admin_delete_monitoring_redirects_to_admin_monitoring_index(): void
    {
        $admin = User::where('role', 'admin')->first();
        $monitoring = CleaningMonitoring::first();

        $response = $this->actingAs($admin)->delete(route('admin.monitoring.destroy', $monitoring));
        $response->assertRedirect(route('admin.monitoring.index'));
        $this->assertDatabaseMissing('cleaning_monitorings', ['id' => $monitoring->id]);
    }

    public function test_monitoring_create_form_keeps_before_and_after_photo_uploads(): void
    {
        $petugas = User::where('role', 'petugas')->first();

        $response = $this->actingAs($petugas)->get(route('monitoring.create'));
        $response->assertStatus(200);
        $response->assertSee('photo_before');
        $response->assertSee('photo_after');
        $response->assertSee('Foto Sebelum');
        $response->assertSee('Foto Sesudah');
        $response->assertDontSee('room_photo');
    }

    public function test_monitoring_edit_form_keeps_before_and_after_photo_uploads(): void
    {
        $petugas = User::where('role', 'petugas')->first();
        $monitoring = CleaningMonitoring::where('user_id', $petugas->id)->first();

        $response = $this->actingAs($petugas)->get(route('monitoring.edit', $monitoring));
        $response->assertStatus(200);
        $response->assertSee('photo_before');
        $response->assertSee('photo_after');
        $response->assertSee('Foto Sebelum');
        $response->assertSee('Foto Sesudah');
        $response->assertDontSee('room_photo');
    }

    public function test_petugas_can_access_laporan_kebersihan_with_metrics_and_own_data(): void
    {
        $petugas = User::where('email', 'andi@cleantrack.test')->first();

        $response = $this->actingAs($petugas)->get(route('petugas.reports.index'));
        $response->assertStatus(200);
        $response->assertSee('Laporan Kebersihan Saya');
        $response->assertSee('Total Pemeriksaan');
        $response->assertSee('Pekerjaan Selesai');
        $response->assertSee('Dalam Proses');
        $response->assertSee('Perlu Tindakan');
        $response->assertSee('Terapkan Filter');
    }

    public function test_petugas_only_sees_their_own_records_in_laporan(): void
    {
        $andi = User::where('email', 'andi@cleantrack.test')->first();
        $budi = User::where('email', 'budi@cleantrack.test')->first();
        $area = Area::first();

        // Create monitoring for Budi
        CleaningMonitoring::create([
            'user_id' => $budi->id,
            'area_id' => $area->id,
            'cleanliness_condition' => 'Bersih',
            'status' => 'Sudah Dibersihkan',
            'notes' => 'Catatan Khusus Budi Yang Unik Sekali',
            'check_date' => now()->toDateString(),
            'check_time' => '10:00:00',
        ]);

        $response = $this->actingAs($andi)->get(route('petugas.reports.index'));
        $response->assertStatus(200);
        $response->assertDontSee('Catatan Khusus Budi Yang Unik Sekali');
    }

    public function test_petugas_laporan_filters_by_status_and_area(): void
    {
        $petugas = User::where('email', 'andi@cleantrack.test')->first();
        $area1 = Area::first();
        $area2 = Area::skip(1)->first();

        CleaningMonitoring::create([
            'user_id' => $petugas->id,
            'area_id' => $area1->id,
            'cleanliness_condition' => 'Bersih',
            'status' => 'Sudah Dibersihkan',
            'notes' => 'Tugas Selesai Area 1 Unik',
            'check_date' => '2026-05-01',
            'check_time' => '09:00:00',
        ]);

        CleaningMonitoring::create([
            'user_id' => $petugas->id,
            'area_id' => $area2->id,
            'cleanliness_condition' => 'Kotor',
            'status' => 'Perlu Dibersihkan',
            'notes' => 'Tugas Belum Area 2 Unik',
            'check_date' => '2026-05-02',
            'check_time' => '10:00:00',
        ]);

        // Filter by status 'Sudah Dibersihkan'
        $response = $this->actingAs($petugas)->get(route('petugas.reports.index', ['status' => 'Sudah Dibersihkan']));
        $response->assertStatus(200);
        $response->assertSee('Tugas Selesai Area 1 Unik');
        $response->assertDontSee('Tugas Belum Area 2 Unik');

        // Filter by area2
        $responseArea = $this->actingAs($petugas)->get(route('petugas.reports.index', ['area_id' => $area2->id]));
        $responseArea->assertStatus(200);
        $responseArea->assertSee('Tugas Belum Area 2 Unik');
        $responseArea->assertDontSee('Tugas Selesai Area 1 Unik');
    }

    public function test_facility_route_remains_functional_for_both_roles(): void
    {
        $admin = User::where('role', 'admin')->first();
        $petugas = User::where('role', 'petugas')->first();

        $responseAdmin = $this->actingAs($admin)->get(route('facility.index'));
        $responseAdmin->assertOk();
        $responseAdmin->assertSee('Denah & Galeri Ruangan');

        $responsePetugas = $this->actingAs($petugas)->get(route('facility.index'));
        $responsePetugas->assertOk();
        $responsePetugas->assertSee('Denah & Galeri Ruangan');
    }

    public function test_ui_profile_menu_in_sidebar_above_logout_for_both_admin_and_petugas(): void
    {
        $admin = User::where('role', 'admin')->first();
        $petugas = User::where('role', 'petugas')->first();

        foreach ([$admin, $petugas] as $user) {
            $dashboardRoute = $user->role === 'admin' ? route('admin.dashboard') : route('petugas.dashboard');
            $response = $this->actingAs($user)->get($dashboardRoute);
            $response->assertStatus(200);

            // User Profile Menu Dropup is present in sidebar above logout
            $response->assertSee('Menu Profil');
            $response->assertSee(strtoupper(substr($user->name, 0, 2)));
            $response->assertSee($user->name);
            $response->assertSee(route('profile.edit'));
            $response->assertSee('Edit Profil');

            // Logout button is present
            $response->assertSee(route('logout'));
            $response->assertSee('Keluar (Logout)');
        }
    }

    public function test_ui_theme_toggle_uses_light_and_dark_labels_for_both_roles(): void
    {
        $admin = User::where('role', 'admin')->first();
        $petugas = User::where('role', 'petugas')->first();

        foreach ([$admin, $petugas] as $user) {
            $dashboardRoute = $user->role === 'admin' ? route('admin.dashboard') : route('petugas.dashboard');
            $response = $this->actingAs($user)->get($dashboardRoute);
            $response->assertStatus(200);
            $response->assertSee('>Light</span>', false);
            $response->assertSee('>Dark</span>', false);
            $response->assertDontSee('Mode Light');
            $response->assertDontSee('Mode Dark');
            $response->assertSee('toggleTheme()', false);
        }
    }

    public function test_ui_cleantrack_logo_has_dark_mode_contrast_styling_for_both_roles(): void
    {
        $admin = User::where('role', 'admin')->first();
        $petugas = User::where('role', 'petugas')->first();

        foreach ([$admin, $petugas] as $user) {
            $dashboardRoute = $user->role === 'admin' ? route('admin.dashboard') : route('petugas.dashboard');
            $response = $this->actingAs($user)->get($dashboardRoute);
            $response->assertStatus(200);

            // Logo contains CLEAN with contrast class and TRACK with crimson color
            $response->assertSee('<span class="text-[#1b1c1c] dark:text-white">CLEAN</span><span class="text-[#dc143c]">TRACK</span>', false);
        }
    }
}
