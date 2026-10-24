<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pasien extends Model
{
    protected $table = 'pasien';

    protected $fillable = [
        'nama_lengkap', 'jenis_kelamin', 'tempat_lahir',
        'tanggal_lahir', 'alamat', 'no_telepon',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function dataPasien(): HasMany
    {
        return $this->hasMany(DataPasien::class, 'id_pasien');
    }

    // Usia otomatis, contoh: "3 th 2 bln"
    protected function usia(): Attribute
    {
        return Attribute::get(function () {
            $diff = $this->tanggal_lahir->diff(now());

            return "{$diff->y} th {$diff->m} bln";
        });
    }
}