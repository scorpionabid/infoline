<?php

namespace Tests\Feature\API\V1;

use App\Domain\Entities\User;
use App\Domain\Entities\Notification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    /** @test */
    public function user_can_get_their_notifications()
    {
        // Test user üçün bildirişlər yaradaq
        Notification::factory()->count(5)->create([
            'user_id' => $this->user->id
        ]);

        // Başqa user üçün də bildiriş yaradaq
        Notification::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/notifications');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'type',
                        'title',
                        'message',
                        'data',
                        'read_at',
                        'created_at'
                    ]
                ],
                'message'
            ]);
    }

    /** @test */
    public function user_can_mark_notification_as_read()
    {
        $notification = Notification::factory()->create([
            'user_id' => $this->user->id,
            'read_at' => null
        ]);

        $response = $this->postJson("/api/v1/notifications/{$notification->id}/read");

        $response->assertStatus(200);
        $this->assertNotNull($notification->fresh()->read_at);
    }

    /** @test */
    public function user_can_mark_all_notifications_as_read()
    {
        // Oxunmamış bildirişlər yaradaq
        Notification::factory()->count(5)->create([
            'user_id' => $this->user->id,
            'read_at' => null
        ]);

        $response = $this->postJson('/api/v1/notifications/read-all');

        $response->assertStatus(200);
        
        // Bütün bildirişlərin oxunmuş olduğunu yoxlayaq
        $this->assertEquals(
            0,
            Notification::where('user_id', $this->user->id)
                ->whereNull('read_at')
                ->count()
        );
    }

    /** @test */
    public function user_cannot_mark_others_notification_as_read()
    {
        $otherUser = User::factory()->create();
        $notification = Notification::factory()->create([
            'user_id' => $otherUser->id,
            'read_at' => null
        ]);

        $response = $this->postJson("/api/v1/notifications/{$notification->id}/read");

        $response->assertStatus(403);
        $this->assertNull($notification->fresh()->read_at);
    }
}