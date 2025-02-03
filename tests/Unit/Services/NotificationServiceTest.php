<?php

namespace Tests\Unit\Services;

use App\Domain\Entities\User;
use App\Events\NewNotification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    private NotificationService $service;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->service = new NotificationService();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_send_notification()
    {
        Event::fake();

        $notification = $this->service->send(
            $this->user,
            'test',
            'Test Title',
            'Test Message',
            ['key' => 'value']
        );

        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'user_id' => $this->user->id,
            'type' => 'test',
            'title' => 'Test Title',
            'message' => 'Test Message'
        ]);

        Event::assertDispatched(NewNotification::class);
    }

    /** @test */
    public function it_can_send_bulk_notifications()
    {
        Event::fake();

        $users = User::factory()->count(3)->create();
        $userIds = $users->pluck('id')->toArray();

        $this->service->sendBulk(
            $userIds,
            'test',
            'Bulk Test',
            'Bulk Message',
            ['test' => 'data']
        );

        $this->assertDatabaseCount('notifications', 3);

        foreach ($userIds as $userId) {
            $this->assertDatabaseHas('notifications', [
                'user_id' => $userId,
                'type' => 'test',
                'title' => 'Bulk Test',
                'message' => 'Bulk Message'
            ]);
        }

        Event::assertDispatched(NewNotification::class, 3);
    }
}