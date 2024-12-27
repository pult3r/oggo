<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Models\Task;
use App\Models\Worker;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Get;
use Illuminate\Support\Facades\Validator;
use App\Filament\Enums\TaskStatus;
use App\Filament\Resources\TeskResource\RelationManagers\WorkersRelationManager;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';

    public static function getPluralLabel(): string
    {
        return __('task.list.table.module.name');
    }

    public static function getNavigationLabel(): string
    {
        return __('task.list.table.module.name');
    }

    public static function getModelLabel(): string
    {
        return __('task.list.table.module.name');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('task.list.table.label.name'))
                            ->required()
                            ->maxLength(255),

                        Select::make('project_id')
                            ->label(__('task.list.table.label.project'))
                            ->relationship('project','name')
                            ->searchable()
                            ->required(),

                        DatePicker::make('startdate')
                            ->label(__('task.list.table.label.start-date'))
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
                            ->label(__('task.list.table.label.end-date'))
                            ->afterOrEqual('startdate')
                            ->nullable(), 

                        Select::make('status')
                            ->label(__('task.list.table.label.status'))
                            ->required()
                            ->options(TaskStatus::class),

                        Select::make('worker_id')
                            ->label(__('task.list.table.label.workers'))
                            ->relationship('workers','name')
                            ->multiple(),

                        ])->columnSpan(2)->columns(2),

                    Section::make()
                        ->schema([
                            Textarea::make('description')
                                ->label(__('task.list.table.label.description'))
                                ->rows(10)
                                ->columnSpan('full'),
                        ])->columnSpan(1),
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
                    ->label(__('task.list.table.label.name'))
                    ->limit(config('filament.default_table_name_length', 50))
                    ->sortable(),

                TextColumn::make('description')
                    ->label(__('task.list.table.label.description'))
                    ->limit(config('filament.default_table_text_length', 80))
                    ->sortable(),

                TextColumn::make('startdate')
                    ->label(__('task.list.table.label.start-date'))
                    ->sortable()
                    ->date('d.m.Y'), 

                TextColumn::make('enddate')
                    ->label(__('task.list.table.label.end-date'))
                    ->sortable()
                    ->date('d.m.Y'), 

                TextColumn::make('workers.name')
                    ->label(__('task.list.table.label.workers'))
                    ->limit(config('filament.default_table_name_length', 50))
                    ->formatStateUsing(function ($state, Task $task) {
                        $workers = collect($task->workers)->map(function (?Worker $worker) {
                            return $worker->name;
                        }) ; 
                        return '('.$task->workers->count() . ') ' . $workers->implode(", ");
                    }),

                TextColumn::make('project.name')
                    ->label(__('task.list.table.label.project')),

                TextColumn::make('status')
                    ->label(__('task.list.table.label.status'))
                    ->sortable()
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('project')
                    ->translateLabel(__('task.list.table.label.project'))
                    ->relationship('project', 'name'),

                SelectFilter::make('workers')
                    ->translateLabel(__('task.list.table.label.workers'))
                    ->relationship('workers', 'name'),

                SelectFilter::make('status')
                    ->translateLabel(__('task.list.table.label.status'))
                    ->options(TaskStatus::class),

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
                    ->translateLabel(__('project.list.table.label.end-date'))
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->action(function($data, $record) {
                        if($record->workers->count() > 0) {
                            Notification::make()
                                ->danger()
                                ->title(__('task.list.table.error.cannot-delete-project', ['task' => $record->name]))
                                ->body(__('task.list.table.error.project-contain-tasks', ['workersCount' => $record->workers->count()]))
                                ->send();

                            return false ; 
                        }
                        $record->delete();
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                    ->action( function (Collection $records) { 
                        foreach($records as $record) {
                            if($record->workers->count() > 0) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('task.list.table.error.cannot-delete-project', ['task' => $record->name]))
                                    ->body(__('task.list.table.error.project-contain-tasks', ['workersCount' => $record->workers->count()]))
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
            WorkersRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
