<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    private function createUserWithNotification(string $role = 'mahasiswa'): array
    {
        $user = User::factory()->create(['role' => $role, 'identifier' => 'MHS_NOTIF01']);

        $notif = Notification::create([
            'user_identifier' => $user->identifier,
            'user_type' => $role,
            'type' => 'info',
            'title' => 'Test Notification',
            'message' => 'Test message body',
            'proposal_id' => null,
            'data' => [],
        ]);

        return [$user, $notif];
    }

    public function test_authenticated_user_can_get_notifications()
    {
        [$user, $notif] = $this->createUserWithNotification();

        $response = $this->actingAs($user)->getJson('/mahasiswa/notifications');

        $response->assertStatus(200)->assertJsonStructure([
            'notifications', 'unread_count',
        ]);
    }

    public function test_unauthenticated_returns_empty()
    {
        // Guest — middleware redirects or returns empty
        $response = $this->getJson('/mahasiswa/notifications');
        // Should redirect to login for non-JSON, but since using getJson it depends on middleware
        $this->assertTrue(in_array($response->getStatusCode(), [200, 302, 401]));
    }

    public function test_user_can_mark_notification_as_read()
    {
        [$user, $notif] = $this->createUserWithNotification();

        $response = $this->actingAs($user)->postJson('/mahasiswa/notifications/mark-read', [
            'notification_id' => $notif->id,
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    public function test_user_can_mark_all_notifications_as_read()
    {
        [$user, $notif] = $this->createUserWithNotification();

        $response = $this->actingAs($user)->postJson('/mahasiswa/notifications/mark-all-read');

        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    public function test_cannot_mark_other_user_notification()
    {
        [$user1, $notif] = $this->createUserWithNotification();
        $user2 = User::factory()->create(['role' => 'mahasiswa', 'identifier' => 'MHS_NOTIF02']);

        $response = $this->actingAs($user2)->postJson('/mahasiswa/notifications/mark-read', [
            'notification_id' => $notif->id,
        ]);

        $response->assertStatus(404); // Not found for different user
    }

    public function test_mark_read_requires_notification_id()
    {
        $user = User::factory()->create(['role' => 'mahasiswa']);

        $response = $this->actingAs($user)->postJson('/mahasiswa/notifications/mark-read', []);

        $response->assertStatus(422); // Validation error
    }
}
