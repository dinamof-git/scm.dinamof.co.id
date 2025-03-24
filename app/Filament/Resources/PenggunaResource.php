<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenggunaResource\Pages;
use App\Filament\Resources\PenggunaResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Forms\Components\Password;
use Filament\Resources\Pages\CreateRecord;
use PhpParser\Node\Stmt\Label;
use Illuminate\Support\Facades\DB;

class PenggunaResource extends Resource
{
    protected static ?string $model = User::class;

    // menu navigasi
    protected static ?string $navigationIcon = 'fas-users'; //icon

    protected static ?string $navigationGroup = 'Pengaturan'; //nama group

    protected static ?int $navigationSort = 1; //urutan

    protected static ?string $slug = 'pengguna'; //URL

    protected static ?string $label = 'Pengguna'; //nama menu
    // end navigasi

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make()
            ->columns([
                'sm' => 1,
                'xl' => 2,
                '2xl' => 5,
            ])
            ->schema(function ($livewire) {
                    // Mengecek apakah objek adalah instance dari CreateRecord
                if ($livewire instanceof CreateRecord) {
                    return self::createSchema();
                }

                return self::editSchema();
            }),
        ]);
    }

    protected static function createSchema(): array
    {
        return [
            TextInput::make('name')
                ->required()
                ->maxLength(255),

            TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(255),

            TextInput::make('password')
                ->label('Password')
                ->password()
                ->required()
                ->maxLength(255)
                ->dehydrateStateUsing(fn ($state) => bcrypt($state)),

            TextInput::make('password_confirmation')
                ->password()
                ->required()
                ->same('password')
                ->label('Confirm Password'),

            Select::make('roles')
                ->label('Grup')
                ->relationship('roles', 'name')
                ->live()
                ->reactive(),

                Select::make('kode_provinsi')
                ->required()
                ->label('Provinsi')
                ->required()
                ->relationship('provinsi', 'nama_provinsi')
                ->multiple()
                ->preload()
                ->hidden(fn ($get) => !array_intersect((array) $get('roles'), 
                    DB::table('roles')
                        ->whereNotIn('name', ['Admin Sistem'])
                        ->pluck('id')
                        ->toArray()
                ))
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
                ->required()
                ->multiple()
                ->preload()
                ->hidden(fn ($get) => !array_intersect((array) $get('roles'), 
                    DB::table('roles')
                        ->whereNotIn('name', ['Admin Sistem', 'Koordinator Provinsi'])
                        ->pluck('id')
                        ->toArray()
                ))
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
                ->required()
                ->multiple()
                ->preload()
                ->hidden(fn ($get) => !array_intersect((array) $get('roles'), 
                    DB::table('roles')
                    ->whereIn('name', ['Koordinator Wilayah', 'Staff Wilayah', 'SPPG'])
                        ->pluck('id')
                        ->toArray()
                ))
                ->live(),
        ];
    }

    protected static function editSchema(): array
    {
        return [
            TextInput::make('name')
                ->required()
                ->maxLength(255),

            TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(255),

            TextInput::make('password')
                ->password()
                ->maxLength(255)
                ->dehydrateStateUsing(function ($state, $livewire) {
                    if ($state) {
                        return bcrypt($state);
                    }
                    return $livewire->getRecord()->password;
                })
                ->label('New Password'),

            TextInput::make('password_confirmation')
                ->password()
                ->same('password')
                ->label('Confirm New Password'),

            Select::make('roles')
                ->required()
                ->label('Grup')
                ->relationship('roles', 'name')
                ->reactive(),

            Select::make('kode_provinsi')
                ->required()
                ->label('Provinsi')
                ->required()
                ->relationship('provinsi', 'nama_provinsi')
                ->multiple()
                ->preload()
                ->hidden(fn ($get) => !array_intersect((array) $get('roles'), 
                    DB::table('roles')
                        ->whereNotIn('name', ['Admin Sistem'])
                        ->pluck('id')
                        ->toArray()
                ))
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
                ->required()
                ->multiple()
                ->preload()
                ->hidden(fn ($get) => !array_intersect((array) $get('roles'), 
                    DB::table('roles')
                        ->whereNotIn('name', ['Admin Sistem', 'Koordinator Provinsi'])
                        ->pluck('id')
                        ->toArray()
                ))
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
                ->required()
                ->multiple()
                ->preload()
                ->hidden(fn ($get) => !array_intersect((array) $get('roles'), 
                    DB::table('roles')
                    ->whereIn('name', ['Koordinator Wilayah', 'Staff Wilayah', 'SPPG'])
                        ->pluck('id')
                        ->toArray()
                ))
                ->live(),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')
                ->label('Nama')
                ->sortable()
                ->searchable(),
            TextColumn::make('email')
                ->sortable()
                ->searchable(),
            TextColumn::make('peran')
                ->label('Grup')
                ->badge()
                ->getStateUsing(function ($record) {
                    $roles = $record->roles->pluck('name');
                    return $roles;
                }),
            TextColumn::make('wilayah')
                ->label('Wilayah')
                ->getStateUsing(function ($record) {
                    $roles = $record->roles->pluck('name')->toArray();
                    $wilayah = [];
            
                    if (in_array('Koordinator Provinsi', $roles) && !empty($record->kode_provinsi)) {
                        $provinsiIds = is_array($record->kode_provinsi) ? $record->kode_provinsi : explode(',', $record->kode_provinsi);
                        $provinsiNames = \App\Models\Provinsi::whereIn('kode_provinsi', $provinsiIds)->pluck('nama_provinsi')->toArray();
                        $wilayah = array_unique(array_merge($wilayah, $provinsiNames));
                    }
                    
                    if (in_array('Koordinator Kabupaten', $roles) && !empty($record->kode_kabupaten)) {
                        $kabupatenIds = is_array($record->kode_kabupaten) ? $record->kode_kabupaten : explode(',', $record->kode_kabupaten);
                        $kabupatenNames = \App\Models\Kabupaten::whereIn('kode_kabupaten', $kabupatenIds)->pluck('nama_kabupaten')->toArray();
                        $wilayah = array_unique(array_merge($wilayah, $kabupatenNames));
                    }
                    
                    if (in_array('Koordinator Wilayah', $roles) && !empty($record->kode_kecamatan)) {
                        $kecamatanIds = is_array($record->kode_kecamatan) ? $record->kode_kecamatan : explode(',', $record->kode_kecamatan);
                        $kecamatanNames = \App\Models\Kecamatan::whereIn('kode_kecamatan', $kecamatanIds)->pluck('nama_kecamatan')->toArray();
                        $wilayah = array_unique(array_merge($wilayah, $kecamatanNames));
                    }
                    
                    if (array_intersect(['Koordinator Wilayah', 'Staff Wilayah', 'SPPG'], $roles) && !empty($record->kode_kecamatan)) {
                        $kecamatanIds = is_array($record->kode_kecamatan) ? $record->kode_kecamatan : explode(',', $record->kode_kecamatan);
                        $kecamatanNames = \App\Models\Kecamatan::whereIn('kode_kecamatan', $kecamatanIds)->pluck('nama_kecamatan')->toArray();
                        $wilayah = array_unique(array_merge($wilayah, $kecamatanNames));
                    }
                    

                    return implode(', ', $wilayah);
                }),
        ])
        ->actions([
            EditAction::make(),
            DeleteAction::make(),
        ])
        ->bulkActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengguna::route('/'),
            'create' => Pages\CreatePengguna::route('/create'),
            'edit' => Pages\EditPengguna::route('/{record}/edit'),
        ];
    }
}
