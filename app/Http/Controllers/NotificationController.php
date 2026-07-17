<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notificationLogs()->paginate(15);
        return Inertia::render('Asesi/NotifikasiList', [
            'allNotifications' => $notifications, 
        ]);
    }

    public function markAllRead(Request $request)
    {
        $user = $request->user();
        $user->notificationLogs()->whereNull('read_at')->update(['read_at' => now()]);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'ok']);
        }

        return redirect()->back()->with('message', 'Semua notifikasi ditandai dibaca.');
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