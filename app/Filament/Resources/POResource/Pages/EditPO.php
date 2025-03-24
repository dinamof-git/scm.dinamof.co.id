<?php

namespace App\Filament\Resources\POResource\Pages;

use App\Filament\Resources\POResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPO extends EditRecord
{
    protected static string $resource = POResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
