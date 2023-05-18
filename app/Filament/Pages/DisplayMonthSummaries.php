<?php

namespace App\Filament\Pages;

use App\Models\Session;
use App\Models\Task;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Resources\Form;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Filament\Tables;

class DisplayMonthSummaries extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.display-month-summaries';

    protected static ?string $title = 'Récapitulatif des heures du mois';

    protected function getTableQuery(): Builder|Relation
    {
        return Task::query();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('project.name')->label('Nom du projet'),
            Tables\Columns\TextColumn::make('name')->label('Nom de la tâche'),
            Tables\Columns\TextColumn::make('hourNbr')->label('Nombre d\'heure sur la tache')
                ->getStateUsing(function (Task $record, $livewire): string {
                    $startFilter = $livewire->tableFilters['started_at']['started_from'];
                    $endFilter = $livewire->tableFilters['started_at']['started_until'];

                    return ceil(Session::where('task_id', '=', $record->id)
                            ->whereDate('started_at', '>=', $startFilter)
                            ->whereDate('started_at', '<=', $endFilter)
                            ->get()
                            ->sum(fn(Session $session): int => $session->ended_at->timestamp - $session->started_at->timestamp)
                        / 3600).' heure(s)';
                })
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Tables\Filters\Filter::make('started_at')
                ->form([
                    DatePicker::make('started_from')->default(now()->startOfMonth()),
                    DatePicker::make('started_until')->default(now()->endOfMonth()),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['started_from'],
                            fn (Builder $query, $date): Builder => $query->whereRelation('sessions', 'started_at', '>=', $date),
                        )
                        ->when(
                            $data['started_until'],
                            fn (Builder $query, $date): Builder => $query->whereRelation('sessions', 'started_at', '<=', $date),
                        );
                })
        ];
    }
}
