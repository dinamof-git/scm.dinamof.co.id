<?php

namespace App\Filament\Resources\POResource\Pages;

use App\Filament\Resources\POResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPO extends ListRecords
{
    protected static string $resource = POResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua'),
            'menunggu_konfirmasi' => Tab::make('Menunggu Konfirmasi')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 1)),
            'diproses' => Tab::make('Diproses')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 2)),
            'barang_disiapkan' => Tab::make('Barang Disiapkan')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 3)),
            'siap_kirim' => Tab::make('Siap Kirim')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 4)),
        ];
    }
}
