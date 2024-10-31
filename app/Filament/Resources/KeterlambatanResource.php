<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KeterlambatanResource\Pages;
use App\Models\Keterlambatan;
use App\Models\Siswa; // pastikan Siswa model diimpor
use App\Models\Laporan; // impor model Laporan
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB; // impor DB jika perlu



class KeterlambatanResource extends Resource
{
    protected static ?string $model = Keterlambatan::class;


    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('siswa_id')
                    ->label('Nama Siswa')
                    ->options(Siswa::all()->pluck('nama', 'id'))
                    ->required()
                    ->searchable(),
                Forms\Components\DatePicker::make('tanggal')
                    ->label('Tanggal Keterlambatan')
                    ->required(),
                Forms\Components\TimePicker::make('waktu')
                    ->label('Waktu Keterlambatan')
                    ->required(),
                Forms\Components\Textarea::make('alasan')
                    ->label('Alasan')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('siswa.nama')->label('Nama Siswa'),
                Tables\Columns\TextColumn::make('tanggal')->label('Tanggal Keterlambatan'),
                Tables\Columns\TextColumn::make('waktu')->label('Waktu Keterlambatan'),
                Tables\Columns\TextColumn::make('alasan')->label('Alasan')->limit(50),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKeterlambatans::route('/'),
            'create' => Pages\CreateKeterlambatan::route('/create'),
            'edit' => Pages\EditKeterlambatan::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Data Keterlambatan';
    }

    public static function afterCreate($record): void
    {
        // Update atau buat entri laporan
        $week = $record->tanggal->format('W'); // Ambil minggu dari tanggal

        Laporan::updateOrCreate(
            ['week' => $week],
            ['jumlah_terlambat' => Laporan::where('week', $week)->sum('jumlah_terlambat') + 1] // Hitung jumlah
        );
    }
}
