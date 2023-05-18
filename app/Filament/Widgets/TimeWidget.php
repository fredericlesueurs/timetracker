<?php

namespace App\Filament\Widgets;

use App\Models\Session;
use Filament\Widgets\Widget;

class TimeWidget extends Widget
{
    protected static string $view = 'filament.widgets.time-widget';

    protected function getViewData(): array
    {
        return [
            'hours' => ceil(Session::activeMonth()->get()->sum(function (Session $session): int {
                return $session->ended_at->timestamp - $session->started_at->timestamp;
            }) / 3600),
        ];
    }
}
