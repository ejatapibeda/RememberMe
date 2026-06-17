<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'telegram_chat_id',
        'telegram_username',
        'telegram_bot_token',
        'telegram_link_code',
        'telegram_link_expires_at',
        'telegram_linked_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'telegram_bot_token',
        'telegram_link_code',
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
            'telegram_bot_token' => 'encrypted',
            'telegram_link_expires_at' => 'datetime',
            'telegram_linked_at' => 'datetime',
        ];
    }

    public function notifications()
    {
        return $this->hasMany(\App\Models\AppNotification::class)->latest();
    }

    public function hasTelegramLinked(): bool
    {
        return ! empty($this->telegram_chat_id);
    }

    // Tambahkan di dalam class User
public function kategori()
{
    // Parameter ke-2 'id_user' wajib ditulis karena kamu tidak pakai default 'user_id'
    return $this->hasMany(kategori::class, 'id_user');
}

public function tugas()
{
    // Parameter ke-2 'id_user' wajib ditulis karena kamu tidak pakai default 'user_id'
    return $this->hasMany(tugas::class, 'id_tugas');
}

}
