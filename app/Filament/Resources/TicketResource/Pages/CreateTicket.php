<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Extraer los documentos del array de datos
        $documents = $data['documents'] ?? [];
        unset($data['documents']);

        // Crear el ticket
        $ticket = static::getModel()::create($data);

        // Crear los documentos asociados
        foreach ($documents as $documentData) {
            $ticket->documents()->create([
                'title' => $documentData['title'],
                'file_path' => $documentData['file_path'],
                'user_id' => auth()->id(),
            ]);
        }

        return $ticket;
    }
}
