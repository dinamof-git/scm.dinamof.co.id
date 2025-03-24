<?php

namespace App\Filament\Resources\KontrakResource\Pages;

use App\Filament\Resources\KontrakResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKontrak extends EditRecord
{
    protected static string $resource = KontrakResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
