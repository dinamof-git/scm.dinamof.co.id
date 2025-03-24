<?php

namespace App\Filament\Resources;

use App\Filament\Resources\POResource\Pages;
use App\Models\PO;
use App\Models\SupplierContract;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class POResource extends Resource
{
    protected static ?string $model = PO::class;

    protected static ?string $navigationIcon = 'fas-boxes-packing';

    protected static ?string $label = 'Purchase Order';

    protected static ?string $slug = 'po'; //URL

    protected static ?int $navigationSort = 4;

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
                        ->relationship('supplier', 'nama_supplier')
                        ->required()
                        ->reactive(),
                    Forms\Components\DatePicker::make('tanggal_po')
                        ->label('Tanggal PO')
                        ->required(),
                    Forms\Components\DatePicker::make('batas_pengiriman')
                        ->label('Batas Pengiriman')
                        ->required(),
                    Forms\Components\Textarea::make('catatan')
                        ->label('Catatan')
                        ->nullable(),
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            1 => 'Menunggu Konfirmasi',
                            2 => 'Diproses',
                            3 => 'Barang Disiapkan',
                            4 => 'Siap Kirim',
                        ])
                        ->default(1)
                        ->required(),
                ]),
            Forms\Components\Section::make('Barang')
                ->schema([
                    Forms\Components\Repeater::make('barang')
                        ->relationship('barang')
                        ->schema([
                            
                            Forms\Components\Select::make('nama_barang')
                                ->label('Barang')
                                ->options(function (callable $get) {
                                    $supplierId = $get('../../supplier_id');
                                    if ($supplierId) {
                                        return SupplierContract::where('supplier_id', $supplierId)
                                            ->pluck('jenis_barang', 'jenis_barang')
                                            ->toArray();
                                    }
                                    return [];
                                })
                                ->required()
                                ->searchable(),

                            Forms\Components\TextInput::make('harga_satuan')
                                ->label('Harga Satuan')
                                ->numeric()
                                ->required(),
                            Forms\Components\TextInput::make('jumlah')
                                ->label('Jumlah')
                                ->numeric()
                                ->required(),
                        ])
                        ->minItems(1)
                        ->addActionLabel('Tambah Barang'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('supplier.nama_supplier')
                    ->label('Nama Supplier')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('preorder.barang')
                    ->label('Barang')
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        return $record->barang->pluck('nama_barang')->implode(', ');
                    }),
                Tables\Columns\TextColumn::make('pemesan')
                    ->label('Pemesan')
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(fn ($record) => DB::table('users')
                    ->where('id', $record->user_id)
                    ->value('name') ?? 'Tidak diketahui'),
                Tables\Columns\TextColumn::make('tanggal_po')
                    ->label('Tanggal PO')
                    ->date()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('batas_pengiriman')
                    ->label('Batas Pengiriman')
                    ->date()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable()
                    ->getStateUsing(fn ($record) => match ($record->status) { 
                        1 => 'Menunggu Konfirmasi',
                        2 => 'Diproses',
                        3 => 'Barang Disiapkan',
                        4 => 'Siap Kirim',
                    })
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
            'index' => Pages\ListPO::route('/'),
            'create' => Pages\CreatePO::route('/create'),
            'edit' => Pages\EditPO::route('/{record}/edit'),
        ];
    }
}
