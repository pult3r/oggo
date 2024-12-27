<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Resources\ProjectResource\RelationManagers\TasksRelationManager;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Validator;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-wallet';

    public static function getPluralLabel(): string
    {
        return __('project.list.table.module.name');
    }

    public static function getNavigationLabel(): string
    {
        return __('project.list.table.module.name');
    }

    public static function getModelLabel(): string
    {
        return __('project.list.table.module.name');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                ->schema([
                    TextInput::make('name')
                        ->label(__('project.list.table.label.name'))
                        ->required()
                        ->maxLength(255),

                    DatePicker::make('startdate')
                        ->label(__('project.list.table.label.start-date'))
                        ->required()
                        ->rules([
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
                    
                    DatePicker::make('enddate')
                        ->label(__('project.list.table.label.end-date'))
                        ->afterOrEqual('startdate')
                        ->nullable(), 

                ])->columnSpan(2)->columns(2),

                Section::make()
                    ->schema([
                        Textarea::make('description')
                            ->label(__('project.list.table.label.description'))
                            ->rows(10)
                            ->columnSpan('full'),
                    ])->columnSpan(1)
                
            ])->columns([
                'default' => 1,
                'md' => 2,
                'lg' => 3,
                'xl' => 4,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('project.list.table.label.name'))
                    ->limit(config('filament.default_table_name_length', 50)) 
                    ->sortable()
                    ->searchable(),

                TextColumn::make('description')
                    ->label(__('project.list.table.label.description'))
                    ->limit(config('filament.default_table_text_length', 80)) 
                    ->sortable()
                    ->searchable(), 

                TextColumn::make('tasks_count')
                    ->label(__('project.list.table.label.task-count'))
                    ->counts('tasks'),

                TextColumn::make('startdate')
                    ->label(__('project.list.table.label.start-date'))
                    ->sortable()
                    ->searchable()
                    ->date('d.m.Y'), 

                TextColumn::make('enddate') 
                    ->label(__('project.list.table.label.end-date'))
                    ->sortable()
                    ->searchable()
                    ->date('d.m.Y'), 
            ])
            ->defaultSort('startdate', 'desc')
            ->filters([
                Filter::make('startdate')
                    ->translateLabel(__('project.list.table.label.start-date'))
                    ->form([
                        Forms\Components\DatePicker::make('startdate'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['startdate'],
                                fn (Builder $query, $date): Builder => $query->whereDate('startdate', '=', $date),
                            );
                    }),

                Filter::make('enddate')
                    ->translateLabel(__('project.list.table.label.ends-date'))
                    ->form([
                        Forms\Components\DatePicker::make('enddate'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['enddate'],
                                fn (Builder $query, $date): Builder => $query->whereDate('enddate', '=', $date),
                            );
                    })
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->action(function($data, $record) {
                        if($record->tasks->count() > 0) {
                            Notification::make()
                                ->danger()
                                ->title(__('project.list.table.error.cannot-delete-project', ['project' => $record->name]))
                                ->body(__('project.list.table.error.project-contain-tasks', ['taskCount' => $record->tasks->count()]))
                                ->send();

                            return false ; 
                        }
                        $record->delete();
                    })
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                    ->action( function (Collection $records) { 
                        foreach($records as $record) {
                            if($record->tasks->count() > 0) {
                                Notification::make()
                                    ->danger()
                                    ->title( __('project.list.table.error.cannot-delete-project', ['project' => $record->name]))
                                    ->body(__('project.list.table.error.project-contain-tasks', ['taskCount' => $record->tasks->count()]))
                                    ->send();
                            } else {
                                $record->delete();
                            }
                        }
                    })
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            TasksRelationManager::class
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
