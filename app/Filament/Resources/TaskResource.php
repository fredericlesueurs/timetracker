<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Models\Task;
use Filament\Resources\Form;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class TaskResource extends Resource
{
    protected static ?string $navigationGroup = 'Suivi de projet';

    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'tâche';

    protected static ?string $pluralModelLabel = 'tâches';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make([
                    'default' => 1,
                    'sm' => 1,
                    'xl' => 2,
                ])
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
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
                            ->maxLength(65535)
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('estimated_duration')
                            ->numeric()
                            ->suffix('mins')
                            ->label('Durée estimée'),
                        Forms\Components\TextInput::make('duration')
                            ->numeric()
                            ->suffix('mins')
                            ->label('Durée réelle'),
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
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut de la tâche')
                    ->enum([
                        'WAITING' => 'En attente',
                        'IN_PROGRESS' => 'En cours',
                        'TERMINATED' => 'Terminé',
                        'PAID' => 'Payé'
                    ])
                ,
                Tables\Columns\TextColumn::make('project.name')
                    ->label('Projet')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
