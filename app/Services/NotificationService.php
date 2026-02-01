<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;

class NotificationService
{
    public function sendToUser(string $userId, string $title, string $message): void
    {
        // Avoid duplicate unread notifications with same title+message for the user
        $exists = DB::table('notifications')
            ->where('user_id', $userId)
            ->where('is_read', false)
            ->where('title', $title)
            ->where('message', $message)
            ->exists();
        if ($exists) return;

        DB::table('notifications')->insert([
            'id' => (string) Str::uuid(),
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'is_read' => false,
            'created_at' => now(),
        ]);
    }

    public function sendToPermission(string $permissionKey, string $title, string $message): void
    {
        // Get all users who have this permission allowed
        $users = User::all()->filter(function ($u) use ($permissionKey) {
            return $u->can($permissionKey);
        });
        foreach ($users as $user) {
            $this->sendToUser($user->id, $title, $message);
        }
    }

    public function markAsRead(string $notificationId): void
    {
        DB::table('notifications')
            ->where('id', $notificationId)
            ->update(['is_read' => true]);
    }

    public function markAllAsRead(string $userId): void
    {
        DB::table('notifications')
            ->where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }
}
