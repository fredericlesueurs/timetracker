<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'tâche';

    protected static ?string $pluralModelLabel = 'tâches';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->columnSpan('full')
                    ->required()
                    ->maxLength(255)
                ,
                Forms\Components\Select::make('status')
                    ->columnSpan('full')
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
                Forms\Components\TextInput::make('duration')
                    ->numeric()
                    ->suffix('heure(s)')
                    ->label('Durée réelle')
                    ->minValue(1),
                Forms\Components\DateTimePicker::make('started_at')->label('Date de début'),
                Forms\Components\DateTimePicker::make('ended_at')->label('Date de fin'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')-> label('Nom de la tâche'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut de la tâche')
                    ->enum([
                        'WAITING' => 'En attente',
                        'IN_PROGRESS' => 'En cours',
                        'TERMINATED' => 'Terminé',
                        'PAID' => 'Payé'
                    ]),
                Tables\Columns\TextColumn::make('estimated_duration')
                    ->formatStateUsing(function (?string $state): ?string {
                        if ($state === null) {
                            return null;
                        }

                        return sprintf('%s heure(s)', $state);
                    })
                    ->label('Durée estimée'),
                Tables\Columns\TextColumn::make('duration')
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
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    protected function getTableQuery(): Builder|Relation
    {
        return parent::getTableQuery()->orderBy('created_at', 'desc');
    }
}
