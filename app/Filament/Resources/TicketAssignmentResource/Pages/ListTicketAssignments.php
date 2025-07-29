<?php

namespace App\Filament\Resources\TicketAssignmentResource\Pages;

use App\Filament\Resources\TicketAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTicketAssignments extends ListRecords
{
    protected static string $resource = TicketAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
