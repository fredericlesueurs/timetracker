<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RelationManagers\CommentsRelationManager;
use App\Filament\Resources\SessionResource\Pages;
use App\Filament\Resources\SessionResource\RelationManagers;
use App\Models\Session;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SessionResource extends Resource
{
    protected static ?string $navigationGroup = 'Suivi de projet';

    protected static ?string $model = Session::class;

    protected static ?string $navigationIcon = 'heroicon-o-play';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'task.name';

    protected static ?string $modelLabel = 'session';

    protected static ?string $pluralModelLabel = 'sessions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make([
                    'default' => 1,
                    'xl' => 2,
                ])
                    ->schema([
                        Forms\Components\Select::make('task_id')
                            ->columnSpan('full')
                            ->relationship('task', 'name')
                            ->required()
                            ->label('Tâche'),
                        Forms\Components\DateTimePicker::make('started_at')->required()->label('Début'),
                        Forms\Components\DateTimePicker::make('ended_at')->label('Fin'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('task.project.name')->label('Nom du projet'),
                Tables\Columns\TextColumn::make('task.name')->label('Nom de la tâche'),
                Tables\Columns\TextColumn::make('started_at')->dateTime()->label('Début'),
                Tables\Columns\TextColumn::make('ended_at')->dateTime()->label('Fin'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSessions::route('/'),
            'create' => Pages\CreateSession::route('/create'),
            'view' => Pages\ViewSession::route('/{record}'),
            'edit' => Pages\EditSession::route('/{record}/edit'),
        ];
    }
}
