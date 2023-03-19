<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use App\Models\Session;
use App\Models\Task;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewTask extends ViewRecord
{
    protected static string $resource = TaskResource::class;

    public function getActions(): array
    {
        return array_merge([
            Action::make('startSession')->label('Démarrer une session')->action(function () {
                $session = new Session();
                $session->task = $this->getRecord();
                $session->started_at = now();

                Notification::make()
                    ->title('Session générée')
                    ->success()
                    ->send();
            })
            ->disabled(function () {
                /** @var Task $task */
                $task = $this->getRecord();
                return $task->sessions->filter(fn(Session $session) => $session->ended_at === null)->count() !== 0;
            }),
        ], parent::getActions());
    }
}
