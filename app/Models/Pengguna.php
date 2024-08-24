<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pengguna extends Authenticatable
{
    use HasFactory;
    protected $table = 'pengguna';
    protected $primaryKey = 'id_pengguna';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pengguna',
        'nama',
        'username',
        'no_hp',
        'password',
        'level',
        'otp',
        'otp_created_at',
        'otp_verified_at', 
    ];

    protected $hidden = [
        'password',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($pengguna) {
            if (empty($pengguna->id_pengguna)) {
                $pengguna->id_pengguna = self::generateId();
            }
        });
    }

   public static function generateId()
    {
        // Ambil ID terbaru dengan urutan descending dan ambil yang pertama
        $latest = self::orderBy('id_pengguna', 'desc')->first();
        $latestId = $latest ? (int) substr($latest->id_pengguna, 1) : 0;

        // Format ID baru dengan prefix 'P' dan increment numerik
        $newId = sprintf('P%03d', $latestId + 1);

        // Pastikan ID unik di tabel
        while (self::where('id_pengguna', $newId)->exists()) {
            $latestId++;
            $newId = sprintf('P%03d', $latestId + 1);
        }
        return $newId;
    }
}