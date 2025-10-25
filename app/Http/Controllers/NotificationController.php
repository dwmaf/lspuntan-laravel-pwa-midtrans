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
        $notifications = $user->notificationLogs()->paginate(15);

        return view('admin.notifikasi.notifikasi', compact('notifications'));
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->notificationLogs()->whereNull('read_at')->update(['read_at' => now()]);

        return response()->json(['status' => 'ok']);
    }

    public static function markAsRead(Request $request)
    {
        if ($request->has('notification_id')) {
            // dd($request->notification_id);
            $notification = $request->user()->notificationLogs()->where('id', $request->notification_id)->first();
            if ($notification) {
                // dd('notif found');
                $notification->update(['read_at' => now()]);
            }
        }
    }
}