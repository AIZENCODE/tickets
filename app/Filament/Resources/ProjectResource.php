<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    // Grupo
    protected static ?string $navigationGroup = 'Gestion';
    // Fin grupo

    // Textos
    protected static ?string $label = 'Proyecto '; // Nombre en singular
    protected static ?string $pluralLabel = 'Proyectos'; // Nombre en plural
    protected static ?string $navigationLabel = 'Proyectos'; // Nombre en la barra lateral


    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Información del Proyecto')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->columnSpanFull()
                            ->required(),

                        Select::make('client_id')
                            ->label('Cliente')
                            ->relationship('client', 'name')
                            ->preload()
                            ->searchable()
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->columnSpanFull(),


                    ]),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('client.name')
                    ->label('Cliente')
                    ->sortable()
                    ->searchable()
                    ->visible(fn() => !auth()->user()->hasRole('Cliente'))  // Solo visible si NO es Cliente
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('subprojects_count')
                    ->label('Subproyectos')
                    ->counts('subprojects')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('users_count')
                    ->label('Usuarios')
                    ->counts('users')
                    ->badge()
                    ->visible(fn() => !auth()->user()->hasRole('Cliente'))  // Solo visible si NO es Cliente
                    ->color('info'),

                // Tables\Columns\TextColumn::make('created_at')
                //     ->label('Creado')
                //     ->dateTime('d/m/Y')
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),

                // Tables\Columns\TextColumn::make('updated_at')
                //     ->label('Actualizado')
                //     ->dateTime('d/m/Y')
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            RelationManagers\SubprojectRelationManager::class,
            RelationManagers\UserRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'view' => Pages\ViewProject::route('/{record}'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Si el usuario es Cliente, filtrar solo sus proyectos
        if (auth()->check() && auth()->user()->hasRole('Cliente')) {
            return $query->whereHas('client', function ($q) {
                $q->where('user_count_id', auth()->id());
            });
        }

        return $query;
    }
}
