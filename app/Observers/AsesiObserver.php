<?php

namespace App\Observers;

use App\Models\Asesi;
use App\Models\News;
use App\Models\NotificationLog;
use App\Notifications\PengumumanBaru;
use Illuminate\Support\Facades\Notification;

class AsesiObserver
{
    /**
     * Handle the Asesi "created" event.
     */
    public function created(Asesi $asesi): void
    {
        //
    }

    /**
     * Handle the Asesi "updated" event.
     */
    public function updated(Asesi $asesi): void
    {
        if ($asesi->isDirty('status') && $asesi->status === 'dilanjutkan_asesmen') {

            $user = $asesi->student->user ?? null;

            if ($user) {
                $pengumumans = News::where('sertification_id', $asesi->sertification_id)->get();

                if ($pengumumans->isNotEmpty()) {
                    foreach ($pengumumans as $pengumuman) {
                        $alreadyNotified = $user->notificationLogs()
                            ->where('type', PengumumanBaru::class)
                            ->where('link->news_id', $pengumuman->id)
                            ->exists();

                        //Hanya kirim notifikasi jika BELUM pernah dikirim.
                        if (!$alreadyNotified) {
                            $body = 'Pengumuman Updated: ' . $pengumuman->rincian;
                            $url = route('asesi.pengumuman.index', [$asesi->sertification_id, $asesi->id, 'news_id' => $pengumuman->news_id]);
                            NotificationLog::create([
                                'user_id' => $user->id,
                                'type' => 'PengumumanBaru',
                                'message' => $body,
                                'link' => $url,
                            ]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Handle the Asesi "deleted" event.
     */
    public function deleted(Asesi $asesi): void
    {
        //
    }

    /**
     * Handle the Asesi "restored" event.
     */
    public function restored(Asesi $asesi): void
    {
        //
    }

    /**
     * Handle the Asesi "force deleted" event.
     */
    public function forceDeleted(Asesi $asesi): void
    {
        //
    }
}
