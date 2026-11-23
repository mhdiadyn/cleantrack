<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QRCodeFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $petugas;

    protected Area $area;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->admin = User::where('role', 'admin')->first();
        $this->petugas = User::where('role', 'petugas')->first();
        $this->area = Area::first();
    }

    public function test_admin_can_view_area_details_with_qr_code_panel(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.areas.show', $this->area->id));

        $response->assertStatus(200);
        $response->assertSee('QR Code Pintu Ruangan');
        $response->assertSee('Gunakan untuk dicetak dan ditempel di dekat pintu ruangan');
    }

    public function test_admin_areas_index_displays_qr_action_button(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.areas.index'));

        $response->assertStatus(200);
        $response->assertSee(route('admin.areas.qr.print', $this->area->id));
        $response->assertSee('Cetak QR Code');
    }

    public function test_admin_can_generate_qr_code_for_single_area(): void
    {
        $this->area->update(['qr_code' => null]);
        $this->assertNull($this->area->fresh()->qr_code);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.areas.qr.generate', $this->area->id));

        $response->assertRedirect();
        $freshArea = $this->area->fresh();
        $this->assertNotNull($freshArea->qr_code);
        $this->assertEquals('AR-'.str_pad((string) $this->area->id, 3, '0', STR_PAD_LEFT), $freshArea->qr_code);

        // Regenerating should be stable and not change the code
        $existingCode = $freshArea->qr_code;
        $this->actingAs($this->admin)->post(route('admin.areas.qr.generate', $this->area->id));
        $this->assertEquals($existingCode, $this->area->fresh()->qr_code);
    }

    public function test_admin_can_generate_all_qr_codes_in_bulk(): void
    {
        Area::query()->update(['qr_code' => null]);
        $this->assertEquals(0, Area::whereNotNull('qr_code')->count());

        $response = $this->actingAs($this->admin)
            ->post(route('admin.areas.qr.generate-all'));

        $response->assertRedirect();
        $totalAreas = Area::count();
        $this->assertEquals($totalAreas, Area::whereNotNull('qr_code')->count());
    }

    public function test_admin_can_view_printable_door_label(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.areas.qr.print', $this->area->id));

        $response->assertStatus(200);
        $response->assertSee($this->area->name);
        $response->assertSee('CLEAN');
        $response->assertSee('TRACK');
        $response->assertSee('Sistem Monitoring Kebersihan');
        $response->assertSee('Pindai QR untuk Monitoring');
        $response->assertSee('</svg>', false);
    }

    public function test_admin_can_download_qr_code_as_svg(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.areas.qr.download', $this->area->id));

        $response->assertStatus(200);
        $this->assertEquals('image/svg+xml', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment;', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('<svg', $response->getContent());
    }

    public function test_petugas_cannot_access_admin_qr_actions(): void
    {
        // Try generating
        $generateResponse = $this->actingAs($this->petugas)
            ->post(route('admin.areas.qr.generate', $this->area->id));
        $generateResponse->assertRedirect(route('petugas.dashboard'));

        // Try printing
        $printResponse = $this->actingAs($this->petugas)
            ->get(route('admin.areas.qr.print', $this->area->id));
        $printResponse->assertRedirect(route('petugas.dashboard'));

        // Try downloading
        $downloadResponse = $this->actingAs($this->petugas)
            ->get(route('admin.areas.qr.download', $this->area->id));
        $downloadResponse->assertRedirect(route('petugas.dashboard'));
    }

    public function test_petugas_sidebar_displays_scan_qr_menu_link(): void
    {
        $response = $this->actingAs($this->petugas)->get(route('petugas.dashboard'));

        $response->assertStatus(200);
        $response->assertSee(route('petugas.scan'));
        $response->assertSee('Scan QR Ruangan');
    }

    public function test_petugas_can_access_mobile_scanner_page(): void
    {
        $response = $this->actingAs($this->petugas)->get(route('petugas.scan'));

        $response->assertStatus(200);
        $response->assertSee('Pindai QR Code Ruangan');
        $response->assertSee('Kamera Pemindai QR');
        $response->assertSee('Alternatif: Input Kode Ruangan Manual');
        $response->assertSee('html5-qrcode');
    }

    public function test_petugas_can_resolve_room_by_scanning_qr_code(): void
    {
        $this->area->generateQrCode();

        $response = $this->actingAs($this->petugas)
            ->get(route('scan.room', ['code' => $this->area->qr_code]));

        $response->assertStatus(200);
        $response->assertSee($this->area->name);
        $response->assertSee($this->area->code);
        $response->assertSee('Catat Monitoring');
        $response->assertSee(route('monitoring.create', ['area_id' => $this->area->id]));
    }

    public function test_petugas_can_resolve_room_by_manual_code_input(): void
    {
        $this->area->generateQrCode();

        // Test with lowercase input
        $response = $this->actingAs($this->petugas)
            ->get(route('petugas.room.resolve', ['code' => strtolower($this->area->qr_code)]));

        $response->assertStatus(200);
        $response->assertSee($this->area->name);
    }

    public function test_petugas_can_resolve_room_when_full_url_is_passed(): void
    {
        $this->area->generateQrCode();
        $fullUrl = 'http://192.168.1.100:8000/scan/room/'.$this->area->qr_code;

        $response = $this->actingAs($this->petugas)
            ->get(route('petugas.room.resolve', ['code' => $fullUrl]));

        $response->assertStatus(200);
        $response->assertSee($this->area->name);
    }

    public function test_resolve_inactive_room_is_rejected_with_error(): void
    {
        $inactiveArea = Area::create([
            'name' => 'Ruangan Gudang Tertutup',
            'location' => 'Lantai Basement',
            'is_active' => false,
        ]);
        $inactiveArea->generateQrCode();

        $response = $this->actingAs($this->petugas)
            ->get(route('scan.room', ['code' => $inactiveArea->qr_code]));

        $response->assertRedirect(route('petugas.scan'));
        $response->assertSessionHas('error');
    }

    public function test_resolve_nonexistent_room_redirects_with_error(): void
    {
        $response = $this->actingAs($this->petugas)
            ->get(route('scan.room', ['code' => 'KODE-TIDAK-VALID-999']));

        $response->assertRedirect(route('petugas.scan'));
        $response->assertSessionHas('error');
    }

    public function test_monitoring_create_preselects_active_area_from_qr(): void
    {
        $response = $this->actingAs($this->petugas)
            ->get(route('monitoring.create', ['area_id' => $this->area->id]));

        $response->assertStatus(200);
        // The area ID is passed as initAreaId to the Alpine.js combobox
        $response->assertSee('initAreaId:    \''.$this->area->id.'\'', false);
    }

    public function test_monitoring_create_ignores_inactive_area_id_parameter(): void
    {
        $inactiveArea = Area::create([
            'name' => 'Ruangan Nonaktif Sementara',
            'location' => 'Lantai Basement',
            'is_active' => false,
        ]);

        $response = $this->actingAs($this->petugas)
            ->get(route('monitoring.create', ['area_id' => $inactiveArea->id]));

        $response->assertStatus(200);
        // Inactive area should not be passed as initAreaId to the combobox
        $response->assertDontSee('initAreaId:    \''.$inactiveArea->id.'\'', false);
    }

    public function test_monitoring_store_rejects_inactive_area_id(): void
    {
        $inactiveArea = Area::create([
            'name' => 'Ruangan Rusak',
            'location' => 'Lantai Basement',
            'is_active' => false,
        ]);

        $response = $this->actingAs($this->petugas)->post(route('monitoring.store'), [
            'area_id' => $inactiveArea->id,
            'cleanliness_condition' => 'Bersih',
            'status' => 'Sudah Dibersihkan',
        ]);

        $response->assertSessionHasErrors('area_id');
    }

    public function test_unauthenticated_user_scanning_qr_code_is_redirected_to_login(): void
    {
        $this->area->generateQrCode();

        $response = $this->get(route('scan.room', ['code' => $this->area->qr_code]));

        $response->assertRedirect(route('login'));
    }
}
