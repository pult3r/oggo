<?php

namespace App\Filament\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;
 
enum TaskStatus: string implements HasLabel, HasIcon, HasColor
{
    case todo = 'todo';
    case inprogress = 'inprogress';
    case done = 'done';
   
    public function getLabel(): ?string
    {
        return match ($this) {
            self::todo => __('enums.task-status.todo'),
            self::inprogress =>  __('enums.task-status.inprogress'),
            self::done =>  __('enums.task-status.done'),
        };
    }
    
    public function getIcon(): ?string
    {
        return match ($this) {
            self::todo => 'heroicon-m-pencil',
            self::inprogress => 'heroicon-m-eye',
            self::done => 'heroicon-m-check',
        };
    } 

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::todo => 'warning',
            self::inprogress => 'gray',
            self::done => 'success',
        };
    }
}