<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\NotificationService;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        $userId = Auth::id();
        $items = DB::table('notifications')
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get(['id','title','message','is_read','created_at']);

        return view('notifications.index', [
            'items' => $items,
        ]);
    }

    public function markRead(string $id)
    {
        $userId = Auth::id();
        $n = DB::table('notifications')->where('id', $id)->first();
        if (!$n || $n->user_id !== $userId) {
            return back()->with('error', 'Notifikasi tidak ditemukan.');
        }
        app(NotificationService::class)->markAsRead($id);
        return back()->with('success', 'Notifikasi ditandai terbaca');
    }

    public function markAllRead()
    {
        $userId = Auth::id();
        app(NotificationService::class)->markAllAsRead($userId);
        return back()->with('success', 'Semua notifikasi ditandai terbaca');
    }
}
