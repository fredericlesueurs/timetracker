<?php

namespace App\Filament\Widgets;

use App\Models\Session;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Collection;

class TimeWidget extends Widget
{
    protected static string $view = 'filament.widgets.time-widget';

    protected function getViewData(): array
    {
        return [
            'hours' => Session::activeMonth()->get()->groupBy('task_id')->sum(function (Collection $collection): int {
                return ceil($collection->sum(fn(Session $session): int => $session->ended_at->timestamp - $session->started_at->timestamp) / 3600);
            }),
        ];
    }
}
