<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\SearchKeyword;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminInboxTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_search_is_listed_for_an_admin(): void
    {
        $this->get(route('search', ['q' => 'Synthetic Keyword School']))->assertOk();

        $this->assertDatabaseHas('search_keywords', [
            'term' => 'Synthetic Keyword School',
        ]);

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.keywords.index'))
            ->assertOk()
            ->assertSee('Synthetic Keyword School');
    }

    public function test_contact_form_message_is_listed_for_an_admin(): void
    {
        $this->post(route('contact.store'), [
            'name' => 'Synthetic Sender',
            'email' => 'sender@example.test',
            'subject' => 'enquiry',
            'message' => 'Synthetic contact message for the directory team.',
        ])->assertRedirect(route('contact'));

        $this->assertSame(1, ContactMessage::query()->count());

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.messages.index'))
            ->assertOk()
            ->assertSee('Synthetic contact message');
    }

    public function test_admin_can_change_password_and_see_when_it_changed(): void
    {
        $admin = User::factory()->admin()->create([
            'password' => 'password',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.settings.password'), [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertRedirect(route('admin.settings.edit'));

        $admin->refresh();
        $this->assertTrue(Hash::check('new-password', $admin->password));
        $this->assertNotNull($admin->password_changed_at);

        $this->actingAs($admin)
            ->get(route('admin.settings.edit'))
            ->assertOk()
            ->assertSee($admin->password_changed_at->format('d M Y, H:i'));
    }

    public function test_cloudflare_page_states_information_is_free(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.cloudflare'))
            ->assertOk()
            ->assertSee('freely provides all published institution information');
    }

    public function test_empty_search_is_not_stored(): void
    {
        $this->get(route('search'))->assertOk();

        $this->assertSame(0, SearchKeyword::query()->count());
    }
}
