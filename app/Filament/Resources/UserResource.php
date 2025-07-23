<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;


    protected static ?string $navigationIcon = 'heroicon-o-users';


    // Grupo
    protected static ?string $navigationGroup = 'Seguridad';
    // Fin grupo

    // Textos
    protected static ?string $label = 'Usuario'; // Nombre en singular
    protected static ?string $pluralLabel = 'Usuarios'; // Nombre en plural
    protected static ?string $navigationLabel = 'Usuarios'; // Nombre en la barra lateral

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Información del Usuario')
                    ->description('Información básica del usuario')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required(),
                        Forms\Components\TextInput::make('email')

                            ->label('Email o usuario')
                            ->required(),
                        Forms\Components\Select::make('roles')
                            ->label('Roles')
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->options(function () {
                                $user = auth()->user();

                                // Si el usuario logueado es super_admin, mostrar todos los roles
                                if ($user && $user->hasRole('super_admin')) {
                                    return \Spatie\Permission\Models\Role::pluck('name', 'id');
                                }

                                // Si no es super_admin, excluir ese rol
                                return \Spatie\Permission\Models\Role::where('name', '!=', 'super_admin')->pluck('name', 'id');
                            })
                            ->preload()
                            ->searchable(),
                        // Forms\Components\DateTimePicker::make('email_verified_at'),
                        Forms\Components\TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->required(),
                    ])->columns(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Roles'),



            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
