<?php
// ketika asesi update datanya
namespace App\Notifications;

use App\Models\Asesi;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AsesiUpdateData extends Notification
{
    use Queueable;
    protected $asesi;

    public function __construct(Asesi $asesi)
    {
        $this->asesi = $asesi;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $notificationId = $this->id; 
        return [
            'message' => 'Asesi ' . $this->asesi->student->name . ' mengupdate data asesinya.',
            'link' => route('admin.applicants.show', [$this->asesi->id, 'notification_id' => $notificationId]),
        ];
    }
}