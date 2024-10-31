<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiswaResource\Pages;
use App\Models\Siswa;
use App\Imports\SiswaImport;
use App\Models\Keterlambatan;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\DatePicker;

class SiswaResource extends Resource
{
    protected static ?string $model = Siswa::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')->label('Nama')->required(),
                Forms\Components\TextInput::make('nis')->label('NIS')->required()->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('kelas')->label('Kelas')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')->label('Nama')->searchable(),
                Tables\Columns\TextColumn::make('nis')->label('NIS')->searchable(),
                Tables\Columns\TextColumn::make('kelas')->label('Kelas'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('importSiswa')
                    ->label('Impor Data Siswa')
                    ->requiresConfirmation()
                    ->modalHeading('Impor Data Siswa')
                    ->form([
                        Forms\Components\FileUpload::make('file')
                            ->label('Pilih File Excel')
                            ->required()
                            ->acceptedFileTypes(['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                            ->directory('uploads')
                            ->preserveFilenames(),
                    ])
                    ->action(function (array $data) {
                        if (empty($data['file'])) {
                            Notification::make()
                                ->title('Gagal')
                                ->body('File tidak ditemukan!')
                                ->danger()
                                ->send();
                            return;
                        }

                        try {
                            $filePath = Storage::disk('public')->path('uploads/' . basename($data['file']));
                            Excel::import(new SiswaImport, $filePath);

                            Notification::make()
                                ->title('Sukses')
                                ->body('Data siswa berhasil diimpor!')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Log::error('Gagal mengimpor data: ' . $e->getMessage());
                            Notification::make()
                                ->title('Gagal')
                                ->body('Gagal mengimpor data siswa: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('inputKeterlambatan')
                    ->label('Input Keterlambatan')
                    ->form([
                        Forms\Components\Hidden::make('siswa_id')->default(fn ($record) => $record->id), // Add this line
                        Forms\Components\DatePicker::make('tanggal')
                            ->label('Tanggal Keterlambatan')
                            ->native(false)
                            ->required(),
                        Forms\Components\TimePicker::make('waktu')
                            ->label('Waktu Keterlambatan')
                            ->required(),
                        Forms\Components\Textarea::make('alasan')
                            ->label('Alasan')
                            ->nullable(),
                    ])
                    ->action(function (array $data) {
                        if (empty($data['siswa_id'])) {
                            Notification::make()
                                ->title('Gagal')
                                ->body('ID siswa tidak ditemukan!')
                                ->danger()
                                ->send();
                            return;
                        }

                        Keterlambatan::create([
                            'siswa_id' => $data['siswa_id'],
                            'tanggal' => $data['tanggal'],
                            'waktu' => $data['waktu'],
                            'alasan' => $data['alasan'] ?? null,
                        ]);

                        Notification::make()
                            ->title('Berhasil')
                            ->body('Data keterlambatan berhasil disimpan!')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->filters([]); // Jika ada filter, tambahkan di sini
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSiswas::route('/'),
            'create' => Pages\CreateSiswa::route('/create'),
            'edit' => Pages\EditSiswa::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Data Keterlambatan';
    }
}
