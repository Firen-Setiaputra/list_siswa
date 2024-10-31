<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanResource\Pages;
use App\Models\Keterlambatan;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;


class LaporanResource extends Resource
{
    public static function getModel(): string
    {
        return Keterlambatan::class; // Tentukan model di method ini
    }

    public static function form(Forms\Form $form): Forms\Form // Menggunakan Forms\Form di sini
    {
        return $form->schema([
            // Tambahkan form input di sini jika diperlukan
        ]);
    }

    public static function table(Tables\table $table): Tables\table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('minggu')
                    ->label('Minggu ke')
                    ->sortable(),

                Tables\Columns\TextColumn::make('bulan')
                    ->label('Bulan')
                    ->sortable(),

                Tables\Columns\TextColumn::make('jumlah_siswa_telat')
                    ->label('Jumlah Siswa Yang Telat')
                    ->getStateUsing(function ($record) {
                        return $record->jumlah_siswa_telat;
                    }),
            ])
            ->query(function (Builder $query) {
                return $query->select([
                        DB::raw('MONTH(tanggal_keterlambatan) as bulan'),
                        DB::raw('WEEK(tanggal_keterlambatan, 1) - WEEK(DATE_SUB(tanggal_keterlambatan, INTERVAL DAYOFMONTH(tanggal_keterlambatan) - 1 DAY), 1) + 1 as minggu'),
                        DB::raw('COUNT(id) as jumlah_siswa_telat')
                    ])
                    ->groupBy('bulan', 'minggu')
                    ->orderBy('bulan')
                    ->orderBy('minggu');
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporans::route('/'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Laporan';
    }
}
