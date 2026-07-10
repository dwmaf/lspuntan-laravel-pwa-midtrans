<?php

namespace App\Observers;

use App\Models\Asesi;
use App\Models\Asesmen;
use App\Models\News;
use App\Models\NotificationLog;
use App\Traits\SendsPushNotifications;

class AsesiObserver
{
    use SendsPushNotifications;

    public function created(Asesi $asesi): void
    {
        // pas asesi daftar dan rupanya udh ada pengumuman yg dibuat sebelumnya, jadi masih kena notif kalau udh ada pengumuman
        $user = $asesi->student?->user;
        if (!$user) return;

        $pengumumans = News::where('sertification_id', $asesi->sertification_id)->get();

        foreach ($pengumumans as $pengumuman) {
            $alreadyNotified = $user->notificationLogs()
                ->where('type', 'PengumumanBaru')
                ->where('link->news_id', $pengumuman->id)
                ->exists();

            if (!$alreadyNotified) {
                $title = 'Pengumuman';
                $body = $pengumuman->rincian;
                $url = route('asesi.pengumuman.index', [$asesi->sertification_id, $asesi->id, 'news_id' => $pengumuman->id]);
                $this->sendPushNotification($user, $title, $body, $url, 'PengumumanBaru');

                NotificationLog::create([
                    'user_id' => $user->id,
                    'type' => 'PengumumanBaru',
                    'message' => $body,
                    'link' => $url,
                ]);
            }
        }
    }

    public function updated(Asesi $asesi): void
    {
        if ($asesi->isDirty('asesor_id') && $asesi->asesor_id) {
            $asesor = $asesi->asesor;
            if (!$asesor) return;

            $hasAsesmen = Asesmen::where('sertification_id', $asesi->sertification_id)
                ->where('user_id', $asesor->user_id)
                ->exists();

            if ($hasAsesmen) {
                $user = $asesi->student->user ?? null;
                if (!$user) return;

                $url = route('asesi.assessmen.index', [$asesi->sertification_id, $asesi]);
                $alreadyNotified = $user->notificationLogs()
                    ->where('type', 'TugasAsesmenBaru')
                    ->where('link', $url)
                    ->exists();

                if (!$alreadyNotified) {
                    $title = 'Tugas Asesmen';
                    $body = 'Anda memiliki tugas asesmen dari asesor Anda.';
                    $this->sendPushNotification($user, $title, $body, $url, 'TugasAsesmenBaru');

                    NotificationLog::create([
                        'user_id' => $user->id,
                        'type' => 'TugasAsesmenBaru',
                        'message' => $body,
                        'link' => $url,
                    ]);
                }
            }
        }
    }

    public function deleted(Asesi $asesi): void
    {
        //
    }

    public function restored(Asesi $asesi): void
    {
        //
    }

    public function forceDeleted(Asesi $asesi): void
    {
        //
    }
}
