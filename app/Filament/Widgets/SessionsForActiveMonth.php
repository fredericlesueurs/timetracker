<?php

namespace App\Filament\Widgets;

use App\Models\Session;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class SessionsForActiveMonth extends BaseWidget
{
    protected int | string | array $columnSpan = 2;

    protected function getTableHeading(): string
    {
        return 'Sessions du mois';
    }

    protected function getTableQuery(): Builder
    {
        return Session::activeMonth();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('task.project.name')->label('Nom du projet'),
            Tables\Columns\TextColumn::make('task.name')->label('Nom de la tâche'),
            Tables\Columns\TextColumn::make('started_at')->dateTime()->label('Début'),
            Tables\Columns\TextColumn::make('ended_at')->dateTime()->label('Fin'),
        ];
    }
}
