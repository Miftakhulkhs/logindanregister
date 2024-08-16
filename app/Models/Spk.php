<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spk extends Model
{
    use HasFactory;

    protected $table = 'spk';
    protected $primaryKey = 'kode_produksi';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'kode_produksi', 'katalog_produk', 'material', 'warna', 'jumlah', 'tanggal_masuk', 'deadline', 
        'ukuran_s', 'ukuran_m', 'ukuran_l', 'ukuran_xl', 'ukuran_xxl', 'detail', 'desain', 'id_pengguna', 
        'id_vendor'
    ];
}
