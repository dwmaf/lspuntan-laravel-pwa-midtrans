<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
// nanti harus dikasih, implements MustVerifyEmail

class User extends Authenticatable 
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'no_tlp_hp',
        'fcm_token'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function asesor()
    {
        return $this->hasOne(Asesor::class);
    }
    public function student()
    {
        return $this->hasOne(Student::class);
    }
    public function notificationLogs()
    {
        return $this->hasMany(NotificationLog::class);
    }
    /**
     * Route notifications for the FCM channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string|array
     */
    public function routeNotificationForFcm($notification)
    {
        return $this->fcm_token;
    }

    /**
     * Route notifications for the Fonnte channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    // public function routeNotificationForFonnte($notification)
    // {
    //     return $this->no_tlp_hp; 
    // }
}
