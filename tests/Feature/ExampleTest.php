<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test root url redirects to login.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    public function test_welcome_page_links_admin_to_admin_dashboard(): void
    {
        $admin = User::factory()->make(['role' => 'admin']);

        $this->actingAs($admin)
            ->view('welcome')
            ->assertSee(route('admin.dashboard'), false);
    }

    public function test_welcome_page_links_petugas_to_petugas_dashboard(): void
    {
        $petugas = User::factory()->make(['role' => 'petugas']);

        $this->actingAs($petugas)
            ->view('welcome')
            ->assertSee(route('petugas.dashboard'), false);
    }
}
