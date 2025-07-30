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

        // Si el estado es "in_progress" y no hay fecha de inicio, establecer la fecha actual
        if (($data['status'] ?? '') === 'in_progress' && empty($data['start_date'])) {
            $data['start_date'] = now()->toDateString();
        }

        // Si el usuario es Cliente y no hay client_id, establecer el cliente del usuario
        if (auth()->user()->hasRole('Cliente') && empty($data['client_id'])) {
            $client = \App\Models\Client::where('user_count_id', auth()->id())->first();
            $data['client_id'] = $client?->id;
        }

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
