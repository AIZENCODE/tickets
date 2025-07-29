<?php

namespace App\Filament\Resources\TicketAssignmentResource\Pages;

use App\Filament\Resources\TicketAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTicketAssignment extends EditRecord
{
    protected static string $resource = TicketAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
