<?php

namespace App\Filament\Resources\CompetitionRegistrations\Pages;

use App\Filament\Resources\CompetitionRegistrations\CompetitionRegistrationResource;
use App\Models\CompetitionRegistration;
use Filament\Resources\Pages\CreateRecord;

class CreateCompetitionRegistration extends CreateRecord
{
    protected static string $resource = CompetitionRegistrationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $nextId = CompetitionRegistration::max('id') + 1;

        $data['registration_number'] = 'CV' . date('Y') . str_pad(
            $nextId,
            5,
            '0',
            STR_PAD_LEFT
        );

        return $data;
    }
}