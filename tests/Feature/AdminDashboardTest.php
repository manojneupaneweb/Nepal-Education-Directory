<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_shows_charts_and_sections(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Status donut')
            ->assertSee('Verification pie')
            ->assertSee('Ownership donut')
            ->assertSee('Institutions by type')
            ->assertSee('Institutions by province')
            ->assertSee('Searches this week')
            ->assertSee('New records by month')
            ->assertSee('Inbox columns')
            ->assertSee('Recent searches')
            ->assertSee('Recent messages')
            ->assertSee('Open errors')
            ->assertSee('Latest suggestions')
            ->assertSee('Newest institutions')
            ->assertSee('Directory coverage');
    }
}
