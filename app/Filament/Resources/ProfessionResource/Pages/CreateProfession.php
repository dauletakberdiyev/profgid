<?php

namespace App\Filament\Resources\ProfessionResource\Pages;

use App\Filament\Resources\ProfessionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProfession extends CreateRecord
{
    protected static string $resource = ProfessionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Extract talent coefficients from form data
        $talentCoefficients = $data['talent_coefficients'] ?? [];
        unset($data['talent_coefficients']);
        
        // Store talent coefficients for after create
        $this->talentCoefficients = $talentCoefficients;
        
        return $data;
    }

    protected function afterCreate(): void
    {
        if (isset($this->talentCoefficients)) {
            foreach ($this->talentCoefficients as $item) {
                if (isset($item['talent_id']) && isset($item['coefficient'])) {
                    $this->record->talents()->attach($item['talent_id'], [
                        'coefficient' => $item['coefficient']
                    ]);
                }
            }
        }
    }
}
