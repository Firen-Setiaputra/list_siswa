<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanResource\Pages;
use App\Models\laporan; // Model laporan menggunakan huruf kecil
use App\Models\Keterlambatan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Carbon\Carbon;

class LaporanResource extends Resource
{
    protected static ?string $model = laporan::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Laporan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([ /* Tambahkan skema form jika diperlukan */ ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal Keterlambatan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah_terlambat')
                    ->label('Jumlah Siswa Terlambat')
                    ->sortable(),
            ])
            ->filters([ /* Tambahkan filter jika perlu */ ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [ /* Tambahkan relasi jika perlu */ ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporans::route('/'),
            'create' => Pages\CreateLaporan::route('/create'),
            'edit' => Pages\EditLaporan::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'laporan';
    }

    // Method untuk menyimpan rekap keterlambatan mingguan
    public static function simpanRekapMingguan()
    {
        // Tentukan tanggal awal minggu ini
        $tanggalMingguIni = Carbon::now()->startOfWeek();

        // Hitung jumlah siswa yang terlambat di minggu ini, sambil mengabaikan siswa yang tidak relevan
        $jumlahSiswaTerlambat = Keterlambatan::whereBetween('tanggal', [
            $tanggalMingguIni,
            Carbon::now()->endOfWeek(),
        ])
        ->where('siswa_id', '!=', 'ID_SISWA_TIDAK_RELEVAN') // Ganti dengan ID siswa yang tidak relevan
        ->distinct('siswa_id')
        ->count('siswa_id');

        // Periksa apakah laporan untuk tanggal ini sudah ada
        $laporan = laporan::where('tanggal', $tanggalMingguIni)->first();

        // Buat atau update laporan mingguan di tabel laporan
        if ($laporan) {
            // Jika laporan sudah ada, update data
            $laporan->update(['jumlah_terlambat' => $jumlahSiswaTerlambat]);
        } else {
            // Jika belum ada, buat entri baru
            laporan::create([
                'jumlah_terlambat' => $jumlahSiswaTerlambat,
                'tanggal' => $tanggalMingguIni,
            ]);
        }
    }
}
