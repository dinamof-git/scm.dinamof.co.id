<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Filament\Resources\SupplierResource\RelationManagers;
use App\Models\Supplier;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;

use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use App\Filament\Resources\SupplierResource\Widgets\ContractsList;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'fas-truck';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Informasi Umum Supplier')
                ->columns([
                    'default' => 1,
                    'xl' => 2,
                    '2xl' => 2,
                ])
                ->schema([
                    TextInput::make('nama_supplier')
                    ->label('Nama Supplier/Nama Perusahaan')
                    ->placeholder('Masukkan Nama Supplier/Nama Perusahaan')
                    ->required(),

                    TextInput::make('nama_kontak')
                    ->label('Nama Kontak')
                    ->placeholder('Masukkan Nama Kontak')
                    ->required(),

                    TextInput::make('nomor_telepon')
                    ->label('Nomor Telepon')
                    ->placeholder('Masukkan Nomor Telepon')
                    ->tel()
                    ->required(),

                    TextInput::make('email')
                    ->label('Email')
                    ->placeholder('Masukkan Email')
                    ->email()
                    ->required(),

                    TextInput::make('nomor_izin_usaha')
                    ->label('Nomor Izin Usaha')
                    ->placeholder('Masukkan Nomor Izin Usaha')
                    ->nullable(),
                ]),
                
                Section::make('Alamat Supplier')
                ->columns([
                    'default' => 1,
                    'xl' => 2,
                    '2xl' => 2,
                ])
                ->schema([
                    Select::make('kode_provinsi')
                    ->label('Provinsi')
                    ->relationship('provinsi', 'nama_provinsi')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live(),

                    Select::make('kode_kabupaten')
                    ->relationship('kabupaten', 'nama_kabupaten', function ($query, $get) {
                        $kodeProvinsi = (array) $get('kode_provinsi');
                        if (!empty($kodeProvinsi)) {
                            return $query->where(function ($q) use ($kodeProvinsi) {
                                foreach ($kodeProvinsi as $kode) {
                                    $q->orWhere('kode_kabupaten', 'like', $kode . '%');
                                }
                            })->orderBy('kode_kabupaten', 'asc');
                        }
                        return $query->whereRaw('1 = 0')->orderBy('kode_kabupaten', 'asc');
                    })
                    ->label('Kabupaten/Kota')
                    ->preload()
                    ->live(),

                    Select::make('kode_kecamatan')
                    ->relationship('kecamatan', 'nama_kecamatan', function ($query, $get) {
                        $kodeKabupaten = (array) $get('kode_kabupaten');
                        if (!empty($kodeKabupaten)) {
                            return $query->where(function ($q) use ($kodeKabupaten) {
                                foreach ($kodeKabupaten as $kode) {
                                    $q->orWhere('kode_kecamatan', 'like', $kode . '%');
                                }
                            })->orderBy('kode_kecamatan', 'asc');
                        }
                        return $query->whereRaw('1 = 0')->orderBy('kode_kecamatan', 'asc');
                    })
                    ->label('Kecamatan')
                    ->preload()
                    ->live(),

                    Select::make('kode_desa')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Desa/Kelurahan')
                    ->options([
                        '001' => 'Desa Dummy 1',
                        '002' => 'Desa Dummy 2',
                        '003' => 'Desa Dummy 3',
                        '004' => 'Kelurahan Dummy 4',
                        '005' => 'Kelurahan Dummy 5',
                    ])
                    ->placeholder('Pilih Desa/Kelurahan')
                    ->live(),

                    Textarea::make('alamat')
                    ->label('Alamat')
                    ->placeholder('Masukkan Alamat')
                    ->required(),

                    TextInput::make('lokasi_supplier')
                    ->label('Lokasi Supplier')
                    ->placeholder('Url Google Maps')
                    ->nullable(),
                    
                    Textarea::make('alamat_gudang')
                    ->label('Alamat Gudang')
                    ->placeholder('Masukkan Alamat Gudang')
                    ->required(),

                    TextInput::make('lokasi_gudang')
                    ->label('Lokasi Gudang')
                    ->placeholder('Url Google Maps')
                    ->nullable(),
                ]),
                Section::make('Wilayah Koordinator Supplier')
                ->columns([
                    'default' => 1,
                    'xl' => 2,
                    '2xl' => 2,
                ])
                ->schema([
                    Select::make('kabupaten_koordinator')
                    ->label('Kabupaten Koordinator')
                    ->relationship('kabupaten', 'nama_kabupaten', function ($query, $get) {
                        $kodeProvinsi = is_array($get('kode_provinsi')) ? ($get('kode_provinsi')[0] ?? null) : $get('kode_provinsi');
                    
                        if ($kodeProvinsi) {
                            return $query->where('kode_kabupaten', 'like', $kodeProvinsi . '%');
                        }
                    
                        return $query->whereRaw('1 = 0');
                    })
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live(),

                    Select::make('wilayah_koordinator')
                        ->label('Wilayah Koordinator')
                        ->options(fn ($get) => 
                            $get('kabupaten_koordinator') 
                                ? User::whereHas('roles', fn ($query) => 
                                        $query->where('name', 'Koordinator Wilayah')
                                    )
                                    ->whereRaw("FIND_IN_SET(?, kode_kabupaten)", [$get('kabupaten_koordinator')])
                                    ->pluck('name', 'id')
                                    ->toArray()
                                : []
                        ),

                    TextInput::make('nama_koordinator_wilayah')
                    ->label('Nama Koordinator Wilayah')
                    ->placeholder('Masukkan Nama Koordinator Wilayah')
                    ->required(),
                ]),

                Section::make('Data Mitra SPPG yang Disupply')
                ->columns([
                    'default' => 1,
                    'xl' => 2,
                    '2xl' => 2,
                ])
                ->schema([
                    Select::make('kabupaten_sppg')
                    ->label('Kabupaten SPPG')
                    ->relationship('kabupaten', 'nama_kabupaten', function ($query, $get) {
                        $kodeProvinsi = is_array($get('kode_provinsi')) ? ($get('kode_provinsi')[0] ?? null) : $get('kode_provinsi');
                    
                        if ($kodeProvinsi) {
                            return $query->where('kode_kabupaten', 'like', $kodeProvinsi . '%');
                        }
                    
                        return $query->whereRaw('1 = 0');
                    })
                    // ->multiple()
                    // ->maxItems(1)
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live(),

                    Select::make('wilayah_koordinator_sppg')
                        ->label('Wilayah Koordinator SPPG')
                        ->options(fn ($get) => 
                            $get('kabupaten_sppg') 
                            ? User::whereHas('roles', fn ($query) => 
                                $query->where('name', 'SPPG')
                            )
                            ->whereRaw("FIND_IN_SET(?, kode_kabupaten)", [$get('kabupaten_koordinator')])
                            ->pluck('name', 'id')
                            ->toArray()
                        : []
                        )
                        ->placeholder('Pilih Wilayah Koordinator SPPG')
                        ->required()
                        ->live(),

                    TextInput::make('sppg')
                    ->label('SPPG 1')
                    ->placeholder('Masukkan SPPG 1')
                    ->nullable(),

                    TextInput::make('sppg2')
                    ->label('SPPG 2')
                    ->placeholder('Masukkan SPPG 2')
                    ->nullable(),

                    TextInput::make('sppg3')
                    ->label('SPPG 3')
                    ->placeholder('Masukkan SPPG 3')
                    ->nullable(),

                    TextInput::make('sppg4')
                    ->label('SPPG 4')
                    ->placeholder('Masukkan SPPG 4')
                    ->nullable(),

                    TextInput::make('sppg5')
                    ->label('SPPG 5')
                    ->placeholder('Masukkan SPPG 5')
                    ->nullable(),

                    TextInput::make('sppg6')
                    ->label('SPPG 6')
                    ->placeholder('Masukkan SPPG 6')
                    ->nullable(),

                    TextInput::make('sppg7')
                    ->label('SPPG 7')
                    ->placeholder('Masukkan SPPG 7')
                    ->nullable(),

                    TextInput::make('sppg8')
                    ->label('SPPG 8')
                    ->placeholder('Masukkan SPPG 8')
                    ->nullable(),

                    TextInput::make('sppg9')
                    ->label('SPPG 9')
                    ->placeholder('Masukkan SPPG 9')
                    ->nullable(),
                ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_supplier')
                    ->searchable(),

                Tables\Columns\TextColumn::make('nama_kontak')
                    ->searchable(),

                Tables\Columns\TextColumn::make('nomor_telepon')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('wilayah')
                    ->label('Wilayah')
                    ->getStateUsing(function ($record) {
                        $kodeProvinsi = $record->kode_provinsi ?? null;
                        $kodeKabupaten = $record->kode_kabupaten ?? null;
                        $kodeKecamatan = $record->kode_kecamatan ?? null;
                
                        $namaProvinsi = $kodeProvinsi ? Provinsi::where('kode_provinsi', $kodeProvinsi)->value('nama_provinsi') : '-';
                        $namaKabupaten = $kodeKabupaten ? Kabupaten::where('kode_kabupaten', $kodeKabupaten)->value('nama_kabupaten') : '-';
                        $namaKecamatan = $kodeKecamatan ? Kecamatan::where('kode_kecamatan', $kodeKecamatan)->value('nama_kecamatan') : '-';
                
                        return "$namaProvinsi/$namaKabupaten/$namaKecamatan";
                    }),
                
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                tables\Actions\DeleteAction::make(),
                
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getWidgets(): array
    {
        return [
            ContractsList::class, 
        ];
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
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
