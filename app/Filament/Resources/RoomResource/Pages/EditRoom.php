<?php

namespace App\Filament\Resources\RoomResource\Pages;

use App\Filament\Resources\RoomResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditRoom extends EditRecord
{
    protected static string $resource = RoomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }protected function afterSave(): void
    {
        Notification::make()
            ->title('Room Type Updated')
            ->success()
            ->send();

        $this->redirect($this->getResource()::getUrl('index'));
    }
}

