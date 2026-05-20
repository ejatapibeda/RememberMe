<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    use HasFactory;

    // Nama tabel di database
   protected $table = 'tugas';

    // Field yang boleh diisi
    protected $fillable = [
        'id_user',
        'id_kategori',
        'tanggal',
        'prioritas',
        'tugas',
        'is_done',
        'its_over',
        'notified_1_hour',
        'notified_5_min',
        'notified_deadline',
    ];

    // app/Models/Tugas.php
protected $casts = [
    'tanggal' => 'datetime',
    'is_done' => 'boolean',
    'its_over' => 'boolean',
];

    public function user()
    {
        // Hubungkan id_user di tabel tugas ke id di tabel users
        return $this->belongsTo(User::class, 'id_user');
    }

   
public function kategori()
{
    // Sesuaikan 'id_kategori' dengan nama kolom foreign key di tabel tugas Anda
    return $this->belongsTo(Kategori::class, 'id_kategori');
}
}