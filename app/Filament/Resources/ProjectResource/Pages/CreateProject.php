<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['id'] = Str::uuid()->toString();
        return $data;
    }

    protected function getRedirectUrl(): string 
    { 
        return $this->getResource()::getUrl('index'); 
    } 
}
