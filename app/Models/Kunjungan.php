<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kunjungan extends Model
{
    protected $table = 'kunjungan';

    protected $fillable = [
        'barcode',
        'user_id',
        'latitude_sales',
        'longitude_sales',
        'accuracy_sales',
        'jarak_meter',
        'threshold_efektif',
        'status',
        'waktu_kunjungan',
    ];

    public function toko()
    {
        return $this->belongsTo(LokasiToko::class, 'barcode', 'barcode');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}