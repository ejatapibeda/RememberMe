<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    protected $table = 'app_notifications';

    protected $fillable = [
        'user_id',
        'tugas_id',
        'jenis',
        'title',
        'body',
        'meta',
        'read_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tugas()
    {
        return $this->belongsTo(Tugas::class, 'tugas_id');
    }
}
