<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanResource\Pages;
use App\Models\Laporan; // Menggunakan model Laporan
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LaporanResource extends Resource
{
    protected static ?string $model = Laporan::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Laporan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Jika ada form yang perlu ditambahkan, silakan di sini
            ]);
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
            ->filters([
                // Tambahkan filter jika perlu
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Tambahkan relasi jika perlu
        ];
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
        return 'Laporan';
    }
}
