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
}
