<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Si el estado cambia a "in_progress" y no hay fecha de inicio, establecer la fecha actual
        if (($data['status'] ?? '') === 'in_progress' && empty($data['start_date'])) {
            $data['start_date'] = now()->toDateString();
        }


        // Actualizar el ticket
        $record->update($data);

        return $record;
    }
}
