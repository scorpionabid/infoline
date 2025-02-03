<?php

namespace App\Services;

use App\Domain\Entities\Notification;
use App\Domain\Entities\User;
use App\Events\NewNotification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function send(
        User $user,
        string $type,
        string $title,
        string $message,
        array $data = []
    ): Notification {
        try {
            $notification = Notification::create([
                'user_id' => $user->id,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => $data
            ]);

            // Event-i yayımlayırıq
            event(new NewNotification($notification));

            return $notification;
        } catch (\Exception $e) {
            Log::error('Notification sending failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);
            throw $e;
        }
    }

    public function sendBulk(
        array $userIds,
        string $type,
        string $title,
        string $message,
        array $data = []
    ): void {
        try {
            foreach ($userIds as $userId) {
                $notification = Notification::create([
                    'user_id' => $userId,
                    'type' => $type,
                    'title' => $title,
                    'message' => $message,
                    'data' => $data
                ]);

                event(new NewNotification($notification));
            }
        } catch (\Exception $e) {
            Log::error('Bulk notification sending failed', [
                'error' => $e->getMessage(),
                'user_ids' => $userIds
            ]);
            throw $e;
        }
    }
}