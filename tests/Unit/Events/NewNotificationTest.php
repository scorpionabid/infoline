<?php

namespace Tests\Unit\Events;

use App\Domain\Entities\User;
use App\Domain\Entities\Notification;
use App\Events\NewNotification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NewNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_broadcasts_on_private_channel()
    {
        $user = User::factory()->create();
        $notification = Notification::factory()->create([
            'user_id' => $user->id
        ]);

        $event = new NewNotification($notification);

        $this->assertEquals(
            'private-notifications.' . $user->id,
            $event->broadcastOn()->name
        );
    }

    /** @test */
    public function it_broadcasts_correct_data()
    {
        $notification = Notification::factory()->create();
        $event = new NewNotification($notification);

        $broadcastData = $event->broadcastWith();

        $this->assertEquals($notification->id, $broadcastData['id']);
        $this->assertEquals($notification->type, $broadcastData['type']);
        $this->assertEquals($notification->title, $broadcastData['title']);
        $this->assertEquals($notification->message, $broadcastData['message']);
    }
}
