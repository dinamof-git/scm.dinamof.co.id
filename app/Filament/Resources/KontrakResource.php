<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KontrakResource\Pages;
use App\Filament\Resources\KontrakResource\RelationManagers;
use App\Models\SupplierContract;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KontrakResource extends Resource
{
    protected static ?string $model = SupplierContract::class;

    protected static ?string $navigationIcon = 'fas-file-signature';

    protected static ?string $slug = 'kontrak'; //URL

    protected static ?string $label = 'kontrak'; //nama menu

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Kontrak Supplier')
                ->columns([
                    'default' => 1,
                    'xl' => 2,
                    '2xl' => 2,
                ])
                ->schema([
                    Forms\Components\Select::make('supplier_id')
                        ->label('Supplier')
                        ->options(Supplier::pluck('nama_supplier', 'id'))
                        ->searchable()
                        ->required(),
                    Forms\Components\TextInput::make('nomor_kontrak')
                        ->label('Nomor Kontrak')
                        ->unique(ignoreRecord: true)
                        ->required(),
                    Forms\Components\DatePicker::make('tanggal_kontrak')
                        ->label('Tanggal Kontrak')
                        ->required(),
                    Forms\Components\DatePicker::make('tanggal_mulai_kontrak')
                        ->label('Tanggal Mulai Kontrak')
                        ->required(),
                    Forms\Components\DatePicker::make('tanggal_berakhir_kontrak')
                        ->label('Tanggal Berakhir Kontrak')
                        ->required(),
                    Forms\Components\TextInput::make('jenis_barang')
                        ->label('Jenis Barang')
                        ->required(),
                        
                    Forms\Components\Textarea::make('sanksi_penalti')
                        ->label('Sanksi & Penalti')
                        ->nullable(),
                    Forms\Components\Textarea::make('ketentuan_pembayaran')
                        ->label('Ketentuan Pembayaran')
                        ->nullable(),
                    Forms\Components\Textarea::make('hak_kewajiban')
                        ->label('Hak & Kewajiban')
                        ->nullable(),
                ]),
            Forms\Components\Section::make('File')
                ->columns([
                    'default' => 1,
                    'xl' => 2,
                    '2xl' => 2,
                ])
                ->schema([
                    Forms\Components\FileUpload::make('copy_kontrak_path')
                        ->label('Salinan Kontrak')
                        ->nullable(),
                    Forms\Components\FileUpload::make('photo_kantor_path')
                        ->label('Foto Kantor')
                        ->nullable(),
                    Forms\Components\FileUpload::make('photo_gudang_path')
                        ->label('Foto Gudang')
                        ->nullable(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_kontrak')
                    ->label('Nomor Kontrak')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('supplier.nama_supplier')
                    ->label('Nama Supplier')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis_barang')
                    ->label('Jenis Barang')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_mulai_kontrak')
                    ->label('Tanggal Mulai')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_berakhir_kontrak')
                    ->label('Tanggal Berakhir')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListKontraks::route('/'),
            'create' => Pages\CreateKontrak::route('/create'),
            'edit' => Pages\EditKontrak::route('/{record}/edit'),
        ];
    }
}
