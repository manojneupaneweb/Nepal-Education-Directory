<?php

namespace Tests\Feature;

use App\Models\ProjectError;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class DeveloperErrorTest extends TestCase
{
    use RefreshDatabase;

    public function test_reported_project_errors_can_be_marked_resolved(): void
    {
        report(new RuntimeException('Synthetic developer failure'));

        $error = ProjectError::query()->where('message', 'Synthetic developer failure')->first();
        $this->assertNotNull($error);

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.developer.index'))
            ->assertOk()
            ->assertSee('Synthetic developer failure')
            ->assertSee('Resolved');

        $this->actingAs($admin)
            ->post(route('admin.developer.resolve', $error))
            ->assertRedirect(route('admin.developer.index'));

        $this->assertNotNull($error->fresh()->resolved_at);
    }
}
