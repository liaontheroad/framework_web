<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LokasiToko extends Model
{
    protected $table      = 'lokasi_toko';
    protected $primaryKey = 'barcode';
    protected $keyType    = 'string';
    public $incrementing  = false;
    public $timestamps    = false;

    protected $fillable = [
        'barcode',
        'nama_toko',
        'latitude',
        'longitude',
        'accuracy',
    ];

    public function kunjungan()
    {
        return $this->hasMany(Kunjungan::class, 'barcode', 'barcode');
    }
}