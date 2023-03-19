<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RelationManagers\CommentsRelationManager;
use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers\SessionsRelationManager;
use App\Models\Session;
use App\Models\Task;
use Filament\Notifications\Notification;
use Filament\Resources\Form;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class TaskResource extends Resource
{
    protected static ?string $navigationGroup = 'Suivi de projet';

    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'tâche';

    protected static ?string $pluralModelLabel = 'tâches';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make([
                    'default' => 1,
                    'xl' => 2,
                ])
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->columnSpan('full')
                            ->required()
                            ->maxLength(255)
                        ,
                        Forms\Components\Select::make('project_id')
                            ->relationship('project', 'name')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options([
                                'WAITING' => 'En attente',
                                'IN_PROGRESS' => 'En cours',
                                'TERMINATED' => 'Terminé',
                                'PAID' => 'Payé',
                            ]),
                        Forms\Components\Textarea::make('description')
                            ->columnSpan('full')
                            ->maxLength(65535),
                        Forms\Components\TextInput::make('estimated_duration')
                            ->numeric()
                            ->suffix('heure(s)')
                            ->label('Durée estimée')
                            ->minValue(1),
                        Forms\Components\TextInput::make('realDuration')
                            ->numeric()
                            ->suffix('heure(s)')
                            ->label('Durée réelle')
                            ->minValue(1),
                        Forms\Components\DateTimePicker::make('started_at')->label('Date de début'),
                        Forms\Components\DateTimePicker::make('ended_at')->label('Date de fin'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom de la tâche')
                ,
                Tables\Columns\TextColumn::make('project.name')
                    ->label('Projet'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut de la tâche')
                    ->enum([
                        'WAITING' => 'En attente',
                        'IN_PROGRESS' => 'En cours',
                        'TERMINATED' => 'Terminé',
                        'PAID' => 'Payé'
                    ])
                ,
                Tables\Columns\TextColumn::make('estimated_duration')
                    ->formatStateUsing(function (?string $state): ?string {
                        if ($state === null) {
                            return null;
                        }

                        return sprintf('%s heure(s)', $state);
                    })
                    ->label('Durée estimée'),
                Tables\Columns\TextColumn::make('realDuration')
                    ->getStateUsing(function (Task $record) {
                        return ceil($record->sessions
                            ->filter(fn(Session $session) => $session->started_at !== null && $session->ended_at !== null)
                            ->map(fn(Session $session) => $session->ended_at->diffInSeconds($session->started_at))
                            ->sum() / 3600);
                    })
                    ->formatStateUsing(function (?string $state): ?string {
                        if ($state === null) {
                            return null;
                        }

                        return sprintf('%s heure(s)', $state);
                    })
                    ->label('Durée réelle'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('startSession')
                    ->label('Démarrer une session')
                    ->action(function (Task $record) {
                        $session = new Session();

                        $session->task()->associate($record);
                        $session->started_at = now();

                        $session->save();

                        Notification::make()
                            ->title('Session générée')
                            ->success()
                            ->send();
                    })
                    ->visible(function () {
                        return Session::where('ended_at', '=', null)->first() === null;
                    })
                    ->icon('heroicon-o-play')
                    ->color('success'),
                Tables\Actions\Action::make('endSession')
                    ->label('Stopper la session')
                    ->action(function (Task $record) {
                        $session = Session::where('task_id', '=', $record->id)
                            ->whereNull('ended_at')
                            ->first();
                        $session->ended_at = now();

                        $session->save();

                        Notification::make()
                            ->title('Session stoppée')
                            ->success()
                            ->send();
                    })
                    ->visible(function (Task $record) {
                        return Session::where('task_id', '=', $record->id)->whereNull('ended_at')->first() !== null;
                    })
                    ->icon('heroicon-o-stop')
                    ->color('danger'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            SessionsRelationManager::class,
            CommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
            'view' => Pages\ViewTask::route('/{record}'),
        ];
    }
}
