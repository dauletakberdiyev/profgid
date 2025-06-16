<?php

namespace App\Filament\Resources\SphereResource\Pages;

use App\Filament\Resources\SphereResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSphere extends EditRecord
{
    protected static string $resource = SphereResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
