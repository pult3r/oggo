<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkerResource\Pages;
use App\Models\Worker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Components\Section;

class WorkerResource extends Resource
{
    protected static ?string $model = Worker::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    public static function getPluralLabel(): string
    {
        return __('worker.list.table.module.name');
    }

    public static function getNavigationLabel(): string
    {
        return __('worker.list.table.module.name');
    }

    public static function getModelLabel(): string
    {
        return __('worker.list.table.module.name');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('name')->required()
                            ->maxLength(255),
                        Textarea::make('role')
                            ->rows(10), 
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('worker.list.table.label.name'))
                    ->limit(config('filament.default_table_name_length', 50)) 
                    ->sortable(),
                
                TextColumn::make('role')
                    ->label(__('worker.list.table.label.role'))
                    ->limit(config('filament.default_table_text_length', 80)) 
                    ->sortable(),

                TextColumn::make('tasks_count')
                    ->label(__('worker.list.table.label.tasks-count'))
                    ->counts('tasks'),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->action(function($data, $record) {
                        if($record->tasks->count() > 0) {
                            Notification::make()
                                ->danger()
                                ->title(__('worker.list.table.error.cannot-delete-worker', ['worker' => $record->name]))
                                ->body(__('worker.list.table.error.worker-contain-tasks', ['tasksCount' => $record->tasks->count()]))
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
                                    ->title(__('worker.list.table.error.cannot-delete-worker', ['worker' => $record->name]))
                                    ->body(__('worker.list.table.error.worker-contain-tasks', ['tasksCount' => $record->tasks->count()]))
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkers::route('/'),
            'create' => Pages\CreateWorker::route('/create'),
            'edit' => Pages\EditWorker::route('/{record}/edit'),
        ];
    }
}
