<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';

    // PERHATIKAN BAGIAN INI
    // Pastikan diawali '[' dan diakhiri '];' (bukan '}')
    protected $fillable = [
        'nama_kategori',
        'id_user',
    ]; 

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}