<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk'; // Nama tabel
    protected $primaryKey = 'produk_id'; // Primary key

    protected $fillable = [
        'nama_produk',
        'deskripsi',
        'harga',
        'stok',
        'gambar',
        'id',
    ];

    // Relasi ke tabel kategori_produk
    public function kategori()
    {
        return $this->belongsTo(KategoriProduk::class, 'id', 'id');
    }
}
