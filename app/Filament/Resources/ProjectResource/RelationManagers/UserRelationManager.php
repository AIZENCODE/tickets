<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

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
                    ->options(User::pluck('name', 'id'))
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

                Tables\Columns\IconColumn::make('pivot.is_responsible')
                    ->label('Responsable')
                    ->boolean(),

                Tables\Columns\TextColumn::make('pivot.created_at')
                    ->label('Asignado el')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->beforeFormFilled(function (array $data): void {
                        // Validación adicional antes de llenar el formulario
                        if (isset($data['recordId'])) {
                            $existingUser = $this->getOwnerRecord()->users()->where('user_id', $data['recordId'])->exists();
                            if ($existingUser) {
                                throw new \Exception('Este usuario ya está asociado al proyecto.');
                            }
                        }
                    })
                    ->form(fn(Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label('Usuario')
                            ->options(
                                User::whereNotIn('id', $this->getOwnerRecord()->users->pluck('id'))
                                    ->whereHas('roles', function ($query) {
                                        $query->where('name', 'desarrollador');
                                    })->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Toggle::make('is_responsible')
                            ->label('¿Es responsable?'),
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('toggle_responsible')
                    ->label(fn ($record) => $record->pivot->is_responsible ? 'Quitar Responsable' : 'Hacer Responsable')
                    ->icon(fn ($record) => $record->pivot->is_responsible ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn ($record) => $record->pivot->is_responsible ? 'danger' : 'success')
                    ->action(function ($record) {
                        $record->pivot->update([
                            'is_responsible' => !$record->pivot->is_responsible
                        ]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading(fn ($record) => $record->pivot->is_responsible ? 'Quitar Responsable' : 'Hacer Responsable')
                    ->modalDescription(fn ($record) => $record->pivot->is_responsible 
                        ? "¿Estás seguro de que quieres quitar a {$record->name} como responsable del proyecto?"
                        : "¿Estás seguro de que quieres hacer a {$record->name} responsable del proyecto?"
                    ),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
