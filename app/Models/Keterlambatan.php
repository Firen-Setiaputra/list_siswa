<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keterlambatan extends Model
{
    protected $fillable = [

        'siswa_id',
        'tanggal',
        'waktu',
        'alasan',
    ];
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
