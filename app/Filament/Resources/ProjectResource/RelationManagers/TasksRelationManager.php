<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;

use App\Filament\Enums\TaskStatus;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make()
                ->schema([
                    TextInput::make('name')
                        ->label(__('task.list.table.label.name'))
                        ->required()
                        ->maxLength(255),
                    
                    Select::make('status')
                        ->label(__('task.list.table.label.status'))
                        ->required()
                        ->options(TaskStatus::class),

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

                ])->columnSpan(2)->columns(2),

                Textarea::make('description')
                    ->label(__('task.list.table.label.description'))
                    ->rows(10)
                    ->columnSpan(2),                            

        ])->columns([
            'default' => 2,
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('task.list.table.module.name'))
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
                
                TextColumn::make('workers_count')
                    ->label(__('task.list.table.label.worker-count'))
                    ->counts('workers'),

                TextColumn::make('status')
                    ->label(__('task.list.table.label.status'))
                    ->sortable()
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->translateLabel(__('project.list.table.label.status'))
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
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
