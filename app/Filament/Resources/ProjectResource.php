<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Get;
use Closure;
use Illuminate\Support\Facades\Validator;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required()->maxLength(255),
                Textarea::make('description')->rows(10), 
                DatePicker::make('startdate')->required()->rules([
                    function (Get $get) {
                        return function () use ($get) {
                            if($get('enddate') === null)  
                                return true ; 
                            else {
                                Validator::make([
                                        'enddate' => $get('enddate'), 
                                        'startdate' => $get('startdate')
                                    ],
                                    ['enddate' => 'before_or_equal:' .  $get('startdate')]
                                );
                            }
                        };
                    },
                ]),
                DatePicker::make('enddate')->afterOrEqual('startdate')->nullable(), 
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->limit(config('filament.default_table_name_length', 50)) ->sortable()->searchable(isIndividual: true), 
                TextColumn::make('description')->limit(config('filament.default_table_text_length', 80)) ->sortable()->searchable(isIndividual: true), 
                TextColumn::make('startdate')->sortable()->searchable(isIndividual: true)->date('d.m.Y'), 
                TextColumn::make('enddate') ->sortable()->searchable(isIndividual: true)->date('d.m.Y'), 
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(), 
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
