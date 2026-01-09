<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
                'roles' => $request->user()?->roles->pluck('name')->toArray() ?? [],
            ],
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'notifications' => function () {
                /** @var App\Models\User $user*/
                $user = Auth::user();

                if ($user) {
                    return [
                        'unreadCount' => $user->notificationLogs()->whereNull('read_at')->count(),
                        'latest' => $user->notificationLogs()->latest()->take(5)->get()->map(function ($notif) {
                            return [
                                'id'=> $notif->id,
                                'message'=>$notif->message,
                                'url' => $notif->url,
                                'read_at' => $notif->read_at,
                                'created_at' => $notif->created_at->diffForHumans(),
                            ];
                        }),
                    ];
                }
                return null;
            },
        ]);
    }
}
