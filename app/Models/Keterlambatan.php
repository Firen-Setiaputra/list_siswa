<?php

namespace App\Models;
use App\Models\Laporan;

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

protected static function booted()
{
    static::created(function ($keterlambatan) {
        $tanggal = $keterlambatan->tanggal;

        Laporan::updateOrCreate(
            ['tanggal' => $tanggal],
            ['jumlah_terlambat' => Laporan::where('tanggal', $tanggal)->count() + 1]
        );
    });
}

}
