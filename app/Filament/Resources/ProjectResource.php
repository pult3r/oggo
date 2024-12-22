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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\DatePicker;
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
                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                Forms\Components\Textarea::make('description')->rows(10), 
                Forms\Components\DatePicker::make('startdate')->required()->rules([
                    function (Get $get) {
                        return function () use ($get) {
                            if($get('enddate') === null)  
                                return true ; 
                            else {
                                Validator::make([
                                    'enddate'=>$get('enddate'), 
                                    'startdate' => $get('startdate')
                                    ] ,
                                    ['enddate' => 'before_or_equal:' .  $get('startdate')]);
                            }
                        };
                    },
                ]),
                Forms\Components\DatePicker::make('enddate')->afterOrEqual('startdate')->nullable(), 
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->limit(config('filament.default_table_name_length', 50)), 
                Tables\Columns\TextColumn::make('description')->limit(config('filament.default_table_text_length', 100)), 
                Tables\Columns\TextColumn::make('startdate'), 
                Tables\Columns\TextColumn::make('enddate'), 
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
