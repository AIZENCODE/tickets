<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\User;

class UserRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $title = 'Usuarios';
    protected static ?string $label = 'Usuario';
    protected static ?string $pluralLabel = 'Usuarios';
    protected static ?string $navigationLabel = 'Usuarios';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Usuario')
                    ->options(function () {
                        $ticket = $this->getOwnerRecord();
                        $designation = $ticket->designation;
                        
                        if (!$designation) {
                            return User::pluck('name', 'id');
                        }

                        // Si es un proyecto, obtener usuarios del proyecto
                        if ($designation instanceof \App\Models\Project) {
                            return $designation->users()->pluck('users.name', 'users.id');
                        }

                        // Si es un subproyecto, obtener usuarios del subproyecto
                        if ($designation instanceof \App\Models\Subproject) {
                            return $designation->users()->pluck('users.name', 'users.id');
                        }

                        return User::pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Toggle::make('is_responsible')
                    ->label('¿Es responsable?'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_responsible')
                    ->label('Responsable')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('Asignar Usuario')
                    ->form(fn(Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label('Usuario')
                            ->options(function () {
                                $ticket = $this->getOwnerRecord();
                                $designation = $ticket->designation;
                                
                                if (!$designation) {
                                    return User::whereNotIn('id', $this->getOwnerRecord()->users->pluck('id'))
                                        ->pluck('name', 'id');
                                }

                                // Si es un proyecto, obtener usuarios del proyecto que no estén ya asignados al ticket
                                if ($designation instanceof \App\Models\Project) {
                                    return $designation->users()
                                        ->whereNotIn('users.id', $this->getOwnerRecord()->users->pluck('id'))
                                        ->pluck('users.name', 'users.id');
                                }

                                // Si es un subproyecto, obtener usuarios del subproyecto que no estén ya asignados al ticket
                                if ($designation instanceof \App\Models\Subproject) {
                                    return $designation->users()
                                        ->whereNotIn('users.id', $this->getOwnerRecord()->users->pluck('id'))
                                        ->pluck('users.name', 'users.id');
                                }

                                return User::whereNotIn('id', $this->getOwnerRecord()->users->pluck('id'))
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Toggle::make('is_responsible')
                            ->label('¿Es responsable?'),
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('toggle_responsible')
                    ->label('Cambiar Responsable')
                    ->icon('heroicon-o-user-circle')
                    ->action(function ($record) {
                        $record->pivot->update([
                            'is_responsible' => !$record->pivot->is_responsible
                        ]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Cambiar estado de responsable')
                    ->modalDescription('¿Estás seguro de que quieres cambiar el estado de responsable para este usuario?')
                    ->modalSubmitActionLabel('Sí, cambiar')
                    ->modalCancelActionLabel('Cancelar'),

                Tables\Actions\DetachAction::make()
                    ->label('Desasignar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make()
                        ->label('Desasignar seleccionados'),
                ]),
            ]);
    }
}
