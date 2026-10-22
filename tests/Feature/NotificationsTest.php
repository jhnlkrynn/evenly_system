<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\InAppNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_and_mark_database_notifications_as_read(): void
    {
        $user = User::factory()->create();
        $user->notify(new InAppNotification('A new booking needs your attention.'));
        $notification = $user->notifications()->firstOrFail();

        $this->actingAs($user)->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('A new booking needs your attention.');
        $this->actingAs($user)->get(route('notifications.unread-count'))
            ->assertOk()->assertJson(['count' => 1]);
        $this->actingAs($user)->get(route('notifications.read', $notification->id))
            ->assertRedirect(route('notifications.index'));

        $this->assertNotNull($notification->refresh()->read_at);
        $this->actingAs($user)->get(route('notifications.unread-count'))
            ->assertJson(['count' => 0]);
    }

    public function test_a_user_cannot_mark_another_users_notification_as_read(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $owner->notify(new InAppNotification('Private notification.'));
        $notification = $owner->notifications()->firstOrFail();

        $this->actingAs($other)->get(route('notifications.read', $notification->id))->assertNotFound();
    }
}
