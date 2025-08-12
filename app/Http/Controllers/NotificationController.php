<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications()->paginate(15);

        // Tandai notifikasi yang belum dibaca sebagai sudah dibaca
        $user->unreadNotifications->markAsRead();

        return view('notifications.index', compact('notifications'));
    }
}