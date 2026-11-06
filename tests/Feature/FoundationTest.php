<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_is_available(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('ShikshaKhoj');
    }

    public function test_search_placeholder_is_available(): void
    {
        $this->get(route('search'))
            ->assertOk()
            ->assertSee('Search institutions');
    }

    public function test_guests_cannot_open_the_admin_dashboard(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_open_the_admin_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Administration');
    }

    public function test_api_status_does_not_expose_configuration(): void
    {
        $this->getJson(route('api.status'))
            ->assertOk()
            ->assertExactJson([
                'application' => 'ShikshaKhoj',
                'status' => 'ok',
            ]);
    }
}
