<?php

namespace App\Http\Controllers\API\V1;

use App\Domain\Entities\Notification;
use App\Http\Controllers\API\V1\BaseController;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->paginate($request->per_page ?? 15);

        return $this->sendResponse(
            NotificationResource::collection($notifications),
            'Bildirişlər uğurla alındı'
        );
    }

    public function markAsRead(Notification $notification): JsonResponse
    {
        if ($notification->user_id !== auth()->id()) {
            return $this->sendError('İcazə yoxdur', [], 403);
        }

        $notification->markAsRead();

        return $this->sendResponse(
            new NotificationResource($notification),
            'Bildiriş oxunmuş kimi qeyd edildi'
        );
    }

    public function markAllAsRead(): JsonResponse
    {
        Notification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return $this->sendResponse(
            [],
            'Bütün bildirişlər oxunmuş kimi qeyd edildi'
        );
    }
}