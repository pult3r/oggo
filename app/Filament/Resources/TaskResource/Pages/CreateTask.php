<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;

    protected function getRedirectUrl(): string 
    { 
        return $this->getResource()::getUrl('index'); 
    } 

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['id'] = Str::uuid()->toString();
        return $data;
    }
}
