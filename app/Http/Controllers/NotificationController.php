<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications()->paginate(15);

        // Tandai notifikasi yang belum dibaca sebagai sudah dibaca
        $user->unreadNotifications->markAsRead();

        return view('admin.notifikasi.notifikasi', compact('notifications'));
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->unreadNotifications->markAsRead();

        return response()->json(['status' => 'ok']);
    }

    public static function markAsRead(Request $request)
    {
        if ($request->has('notification_id')) {
            $notification = $request->user()->notifications()->where('id', $request->notification_id)->first();
            if ($notification) {
                $notification->markAsRead();
            }
        }
    }
}