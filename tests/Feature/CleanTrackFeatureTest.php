<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\CleaningMonitoring;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
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

    public function test_interactive_floorplan_and_api_are_accessible(): void
    {
        $admin = User::where('role', 'admin')->first();

        // Floor plan page
        $response = $this->actingAs($admin)->get(route('floorplan.index'));
        $response->assertStatus(200);
        $response->assertSee('Denah Fasilitas Interaktif');
        $response->assertSee('Lantai 1');

        // Floor plan JSON API
        $responseApi = $this->actingAs($admin)->get(route('floorplan.data'));
        $responseApi->assertStatus(200);
        $responseApi->assertJsonStructure([
            'floor',
            'areas',
            'stats' => ['bersih', 'perlu_tindakan', 'kotor', 'belum_dimonitor'],
        ]);
    }

    public function test_facility_gallery_and_360_are_accessible_with_filters(): void
    {
        $petugas = User::where('email', 'andi@cleantrack.test')->first();

        $response = $this->actingAs($petugas)->get(route('gallery.index'));
        $response->assertStatus(200);
        $response->assertSee('Galeri Keseluruhan Ruangan & 360°');
        $response->assertSee('360° Virtual Tour');
        $response->assertSee('Sebelum vs Sesudah');

        // Filter 360 tab
        $response360 = $this->actingAs($petugas)->get(route('gallery.index', ['type' => '360']));
        $response360->assertStatus(200);

        // Filter comparison tab
        $responseComparison = $this->actingAs($petugas)->get(route('gallery.index', ['type' => 'comparison']));
        $responseComparison->assertStatus(200);
    }

    public function test_admin_can_upload_a_360_photo_for_an_area(): void
    {
        Storage::fake('public');
        $admin = User::where('role', 'admin')->first();
        $area = Area::first();

        $response = $this->actingAs($admin)
            ->from(route('gallery.index'))
            ->post(route('admin.areas.update-360', $area), [
                'photo_360' => UploadedFile::fake()->image('panorama.jpg'),
            ]);

        $response->assertRedirect(route('gallery.index'));
        $area->refresh();
        $this->assertStringStartsWith('areas_360/', $area->photo_360);
        Storage::disk('public')->assertExists($area->photo_360);
    }

    public function test_admin_can_upload_360_via_general_endpoint(): void
    {
        Storage::fake('public');
        $admin = User::where('role', 'admin')->first();
        $area = Area::where('floor', 3)->first() ?? Area::first();

        $response = $this->actingAs($admin)
            ->from(route('gallery.index'))
            ->post(route('admin.areas.upload-360-general'), [
                'area_id' => $area->id,
                'photo_360' => UploadedFile::fake()->image('panorama360.jpg'),
            ]);

        $response->assertRedirect(route('gallery.index'));
        $area->refresh();
        $this->assertStringStartsWith('areas_360/', $area->photo_360);
        Storage::disk('public')->assertExists($area->photo_360);
    }

    public function test_upload_360_rejects_file_exceeding_50mb(): void
    {
        Storage::fake('public');
        $admin = User::where('role', 'admin')->first();
        $area = Area::first();

        // Create a fake file that exceeds 50MB (51201 KB)
        $oversizedFile = UploadedFile::fake()->create('huge_360.jpg', 51201, 'image/jpeg');

        $response = $this->actingAs($admin)
            ->from(route('gallery.index'))
            ->post(route('admin.areas.update-360', $area), [
                'photo_360' => $oversizedFile,
            ]);

        $response->assertSessionHasErrors(['photo_360']);
        $errors = session('errors');
        $this->assertStringContainsString('50 MB', $errors->first('photo_360'));
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

    public function test_floorplan_supports_floors_up_to_floor_4(): void
    {
        $admin = User::where('role', 'admin')->first();

        // Lantai 3
        $responseFloor3 = $this->actingAs($admin)->get(route('floorplan.index', ['floor' => 3]));
        $responseFloor3->assertStatus(200);
        $responseFloor3->assertSee('Lantai 3');

        // Lantai 4
        $responseFloor4 = $this->actingAs($admin)->get(route('floorplan.index', ['floor' => 4]));
        $responseFloor4->assertStatus(200);
        $responseFloor4->assertSee('Lantai 4');
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

    public function test_area_photo_type_defaults_to_panorama_360_for_backward_compatibility(): void
    {
        $area = Area::first();
        $this->assertNotNull($area);
        $this->assertEquals('panorama_360', $area->photo_type);
        $this->assertTrue($area->is_360);
        $this->assertFalse($area->is_standard);
        $this->assertEquals('Panorama 360° Virtual', $area->photo_type_label);
    }

    public function test_admin_can_upload_standard_room_photo_and_switch_types(): void
    {
        Storage::fake('public');
        $admin = User::where('role', 'admin')->first();
        $area = Area::first();

        // 1. Upload as standard room photo
        $response = $this->actingAs($admin)->post(route('admin.areas.update-360', $area->id), [
            'photo_360' => UploadedFile::fake()->image('standard_room.jpg', 1920, 1080),
            'photo_type' => 'standard',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $area->refresh();
        $this->assertEquals('standard', $area->photo_type);
        $this->assertTrue($area->is_standard);
        $this->assertFalse($area->is_360);
        $this->assertEquals('Foto Ruangan Standar', $area->photo_type_label);
        Storage::disk('public')->assertExists($area->photo_360);

        // 2. Upload as true 360 panorama via general endpoint
        $response2 = $this->actingAs($admin)->post(route('admin.areas.upload-360-general'), [
            'area_id' => $area->id,
            'photo_360' => UploadedFile::fake()->image('equirectangular_360.jpg', 4000, 2000),
            'photo_type' => 'panorama_360',
        ]);

        $response2->assertRedirect();
        $area->refresh();
        $this->assertEquals('panorama_360', $area->photo_type);
        $this->assertTrue($area->is_360);
        $this->assertEquals('Panorama 360° Virtual', $area->photo_type_label);
    }

    public function test_gallery_displays_separated_tabs_and_filters(): void
    {
        $admin = User::where('role', 'admin')->first();

        // 1. Default gallery page
        $response = $this->actingAs($admin)->get(route('gallery.index'));
        $response->assertStatus(200);
        $response->assertSee('Panorama 360°');
        $response->assertSee('Foto Standar HD');
        $response->assertSee('Semua Foto');

        // 2. Filter for 360 only
        $response360 = $this->actingAs($admin)->get(route('gallery.index', ['type' => '360']));
        $response360->assertStatus(200);

        // 3. Filter for standard photos only
        $responseStandard = $this->actingAs($admin)->get(route('gallery.index', ['type' => 'standard']));
        $responseStandard->assertStatus(200);
    }

    public function test_monitoring_supports_standard_and_360_room_photos(): void
    {
        Storage::fake('public');
        $petugas = User::where('email', 'andi@cleantrack.test')->first();
        $area = Area::first();

        $response = $this->actingAs($petugas)->post(route('monitoring.store'), [
            'area_id' => $area->id,
            'cleanliness_condition' => 'Bersih',
            'status' => 'Sudah Dibersihkan',
            'start_time' => '09:00',
            'end_time' => '09:30',
            'notes' => 'Pemeriksaan dengan foto standar.',
            'photo_before' => UploadedFile::fake()->image('before.jpg'),
            'photo_after' => UploadedFile::fake()->image('after.jpg'),
            'photo_360' => UploadedFile::fake()->image('room_detail.jpg', 1600, 1200),
            'photo_360_type' => 'standard',
        ]);

        $response->assertRedirect(route('petugas.my-monitoring'));

        $monitoring = CleaningMonitoring::where('area_id', $area->id)->latest('id')->first();
        $this->assertNotNull($monitoring);
        $this->assertEquals('standard', $monitoring->photo_360_type);
        $this->assertTrue($monitoring->is_standard);
        $this->assertFalse($monitoring->is_360);

        // Check show page renders without Pannellum 360 initialization for standard photo
        $showResponse = $this->actingAs($petugas)->get(route('monitoring.show', $monitoring->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Foto Ruangan Standar HD');
        $showResponse->assertDontSee('initialPannellum');
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
}
