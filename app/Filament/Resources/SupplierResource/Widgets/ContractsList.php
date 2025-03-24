<?php

namespace App\Filament\Resources\SupplierResource\Widgets;

use App\Models\SupplierContract;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

class ContractsList extends BaseWidget
{
    public ?Model $record = null;

    protected int | string | array $columnSpan = 'full';

    protected static null|string $heading = 'Daftar Kontrak Aktif';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                SupplierContract::query()
                    ->where('supplier_id', $this->record->id)
                    ->where('tanggal_berakhir_kontrak', '>=', now())  // Filter hanya kontrak yang aktif
            )
            ->columns([
                TextColumn::make('nomor_kontrak')
                    ->label('Nomor Kontrak')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('jenis_barang')
                    ->label('Jenis Barang')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('tanggal_kontrak')
                    ->label('Tanggal Kontrak')
                    ->date()
                    ->sortable(),

                TextColumn::make('tanggal_berakhir_kontrak')
                    ->label('Tanggal Berakhir')
                    ->date()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->state(fn ($record) => now()->greaterThan($record->tanggal_berakhir_kontrak) ? 'Expired' : 'Aktif')
                    ->badge()
                    ->color(fn ($record) => now()->greaterThan($record->tanggal_berakhir_kontrak) ? 'danger' : 'success'),
            ]);
    }
}
