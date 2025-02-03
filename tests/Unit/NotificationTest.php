<?php

namespace Tests\Unit;

use App\Domain\Entities\User;
use App\Domain\Entities\Notification;
use App\Services\NotificationService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->service = new NotificationService();
    }

    /** @test */
    public function it_can_create_notification()
    {
        $notification = $this->service->send(
            $this->user,
            'system',
            'Test Title',
            'Test Message',
            ['key' => 'value']
        );

        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->user->id,
            'type' => 'system',
            'title' => 'Test Title',
            'message' => 'Test Message'
        ]);

        $this->assertEquals(['key' => 'value'], $notification->data);
    }

    /** @test */
    public function it_can_create_bulk_notifications()
    {
        $users = User::factory()->count(3)->create();
        $userIds = $users->pluck('id')->toArray();

        $this->service->sendBulk(
            $userIds,
            'system',
            'Bulk Test',
            'Bulk Message',
            ['test' => 'data']
        );

        foreach ($userIds as $userId) {
            $this->assertDatabaseHas('notifications', [
                'user_id' => $userId,
                'type' => 'system',
                'title' => 'Bulk Test',
                'message' => 'Bulk Message'
            ]);
        }
    }

    /** @test */
    public function it_can_mark_notification_as_read()
    {
        $notification = Notification::factory()->create([
            'user_id' => $this->user->id,
            'read_at' => null
        ]);

        $this->assertNull($notification->read_at);
        
        $notification->markAsRead();
        
        $this->assertNotNull($notification->fresh()->read_at);
    }

    /** @test */
    public function it_can_mark_notification_as_unread()
    {
        $notification = Notification::factory()->create([
            'user_id' => $this->user->id,
            'read_at' => now()
        ]);

        $this->assertNotNull($notification->read_at);
        
        $notification->markAsUnread();
        
        $this->assertNull($notification->fresh()->read_at);
    }

    /** @test */
    public function it_can_check_if_notification_is_read()
    {
        $notification = Notification::factory()->create([
            'user_id' => $this->user->id,
            'read_at' => null
        ]);

        $this->assertFalse($notification->isRead());
        
        $notification->markAsRead();
        
        $this->assertTrue($notification->fresh()->isRead());
    }
}