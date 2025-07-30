<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    // Grupo
    protected static ?string $navigationGroup = 'Gestion';
    // Fin grupo

    // Textos
    protected static ?string $label = 'Cliente'; // Nombre en singular
    protected static ?string $pluralLabel = 'Clientes'; // Nombre en plural
    protected static ?string $navigationLabel = 'Clientes'; // Nombre en la barra lateral

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Cliente')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre del Cliente')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ingrese el nombre del cliente'),

                        Forms\Components\Select::make('type')
                            ->label('Tipo de Cliente')
                            ->options([
                                'empresa' => 'Empresa',
                                'individual' => 'Individual',
                                'gobierno' => 'Gobierno',
                                'ong' => 'ONG',
                            ])
                            ->required()
                            ->default('empresa'),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->placeholder('cliente@ejemplo.com'),

                        Forms\Components\TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(255)
                            ->placeholder('+1234567890'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Información del Usuario')
                    ->schema([
                        Forms\Components\TextInput::make('user_name')
                            ->label('Nombre del Usuario')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Nombre del usuario del cliente')
                            ->dehydrated(false), // No guardar en la base de datos

                        Forms\Components\TextInput::make('user_email')
                            ->label('Email del Usuario')
                            // ->email()
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Usuario o email del cliente')
                            ->dehydrated(false), // No guardar en la base de datos

                        Forms\Components\TextInput::make('user_password')
                            ->label('Contraseña')
                            ->password()
                            ->required()
                            ->minLength(8)
                            ->confirmed()
                            ->dehydrated(false), // No guardar en la base de datos

                        Forms\Components\TextInput::make('user_password_confirmation')
                            ->label('Confirmar Contraseña')
                            ->password()
                            ->required()
                            ->minLength(8)
                            ->dehydrated(false), // No guardar en la base de datos
                    ])
                    ->columns(2)
                    ->visible(fn (string $operation): bool => $operation === 'create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre del Cliente')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'empresa' => 'primary',
                        'individual' => 'success',
                        'gobierno' => 'warning',
                        'ong' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'empresa' => 'Empresa',
                        'individual' => 'Individual',
                        'gobierno' => 'Gobierno',
                        'ong' => 'ONG',
                        default => 'Desconocido',
                    }),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario Asociado')
                    ->placeholder('Sin usuario')
                    ->color(fn($state) => $state ? null : 'gray'),

                Tables\Columns\TextColumn::make('projects_count')
                    ->label('Proyectos')
                    ->counts('projects')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha Creación')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo de Cliente')
                    ->options([
                        'empresa' => 'Empresa',
                        'individual' => 'Individual',
                        'gobierno' => 'Gobierno',
                        'ong' => 'ONG',
                    ]),

                Tables\Filters\Filter::make('has_user')
                    ->label('Con Usuario Asociado')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('user_count_id')),

                Tables\Filters\Filter::make('has_projects')
                    ->label('Con Proyectos')
                    ->query(fn (Builder $query): Builder => $query->has('projects')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver'),
                Tables\Actions\EditAction::make()
                    ->label('Editar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar seleccionados'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'view' => Pages\ViewClient::route('/{record}'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
