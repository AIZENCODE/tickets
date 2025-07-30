<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Actions\Action;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Información del Proyecto')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nombre del Proyecto')
                            ->size(TextEntry\TextEntrySize::Large)
                            ->weight('bold')
                            ->columnSpanFull(),

                        // TextEntry::make('client.name')
                        //     ->label('Cliente')
                        //     ->badge()
                        //     ->color('primary'),

                        TextEntry::make('created_at')
                            ->label('Fecha de Creación')
                            ->dateTime('d/m/Y H:i')
                            ->color('gray'),

                        // TextEntry::make('updated_at')
                        //     ->label('Última Actualización')
                        //     ->dateTime('d/m/Y H:i')
                        //     ->color('gray'),

                        TextEntry::make('description')
                            ->label('Descripción')
                            ->markdown()
                            ->columnSpanFull(),
                    ]),

                // Section::make('Subproyectos')
                //     ->description('Lista de subproyectos asociados a este proyecto')
                //     ->schema([
                //         TextEntry::make('subprojects_count')
                //             ->label('Total de Subproyectos')
                //             ->state(fn ($record) => $record->subprojects->count() . ' subproyectos')
                //             ->badge()
                //             ->color('success'),

                //         TextEntry::make('subprojects_list')
                //             ->label('Lista de Subproyectos')
                //             ->state(fn ($record) => $record->subprojects->map(fn ($subproject) =>
                //                 "• {$subproject->name} - {$subproject->description}"
                //             )->join("\n"))
                //             ->columnSpanFull(),
                //     ])
                //     ->collapsible()
                //     ->collapsed(false),

                // Section::make('Usuarios Asignados')
                //     ->description('Usuarios que trabajan en este proyecto')
                //     ->schema([
                //         TextEntry::make('users_count')
                //             ->label('Total de Usuarios')
                //             ->state(fn ($record) => $record->users->count() . ' usuarios')
                //             ->badge()
                //             ->color('info'),

                //         TextEntry::make('users_list')
                //             ->label('Lista de Usuarios')
                //             ->state(fn ($record) => $record->users->map(fn ($user) =>
                //                 "• {$user->name} ({$user->email})"
                //             )->join("\n"))
                //             ->columnSpanFull(),
                //     ])
                //     ->collapsible()
                //     ->collapsed(false),
            ]);
    }
}
