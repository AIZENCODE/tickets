<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Filament\Resources\TicketResource\RelationManagers;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    // Grupo
    protected static ?string $navigationGroup = 'Gestion';
    // Fin grupo

    // Textos
    protected static ?string $label = 'Ticket'; // Nombre en singular
    protected static ?string $pluralLabel = 'Tickets'; // Nombre en plural
    protected static ?string $navigationLabel = 'Tickets'; // Nombre en la barra lateral

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Section::make('Clasificación')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Tipo')
                            ->options([
                                'bug' => 'Bug',
                                'feature_request' => 'Solicitud de Característica',
                                'support' => 'Soporte',
                            ])
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'open' => 'Abierto',
                                'in_progress' => 'En Progreso',
                                'stop' => 'Detenido',
                                'closed' => 'Cerrado',
                            ])
                            ->required(),
                        Forms\Components\Select::make('priority')
                            ->label('Prioridad')
                            ->options([
                                'low' => 'Baja',
                                'medium' => 'Media',
                                'high' => 'Alta',
                            ])
                            ->required(),
                    ])->columns(2),


                Forms\Components\Section::make('Información Básica')
                    ->schema([

                        Forms\Components\Select::make('client_id')
                            ->label('Cliente')
                            ->relationship('client', 'name')
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn(Forms\Set $set) => $set('designation_id', null)),

                        Forms\Components\Select::make('designation_type')
                            ->label('Tipo de Designación')
                            ->options([
                                'App\Models\Project' => 'Proyecto',
                                'App\Models\Subproject' => 'Subproyecto',
                            ])
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn(Forms\Set $set) => $set('designation_id', null)),

                        Forms\Components\Select::make('designation_id')
                            ->label('Designación')
                            ->options(function (Forms\Get $get) {
                                $type = $get('designation_type');
                                $clientId = $get('client_id');

                                if (!$type || !$clientId) {
                                    return [];
                                }

                                $query = $type::query();

                                if ($type === 'App\Models\Project') {
                                    $query->where('client_id', $clientId)
                                        ->orderBy('name');
                                }

                                if ($type === 'App\Models\Subproject') {
                                    $query->whereHas('project', function ($q) use ($clientId) {
                                        $q->where('client_id', $clientId);
                                    })
                                        ->with('project')
                                        ->orderBy('name');
                                }

                                return $query->get()
                                    ->mapWithKeys(function ($item) use ($type) {
                                        if ($type === 'App\Models\Subproject') {
                                            return [$item->id => "{$item->project->name} > {$item->name}"];
                                        }
                                        return [$item->id => $item->name];
                                    });
                            })
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('description')
                            ->label('Descripción')
                            ->required()
                            ->toolbarButtons([
                                'bold',          // Negrita (**texto**)
                                'italic',       // Cursiva (*texto*)
                                'underline',    // Subrayado
                                'strike',       // Tachado (~texto~)
                                'blockquote',   // Cita (> cita)
                                'bulletList',   // Lista no ordenada
                                'orderedList',  // Lista numerada
                                'link',         // Enlace
                                'undo',        // Deshacer (Ctrl+Z)
                                'redo',        // Rehacer (Ctrl+Y)
                            ])
                            ->columnSpanFull()
                    ]),



                Forms\Components\Section::make('Fechas')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Fecha de Inicio'),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Fecha de Finalización'),
                        Forms\Components\DateTimePicker::make('closed_at')
                            ->label('Cerrado el'),
                    ])->columns(3),

                Forms\Components\Section::make('Documentos')
                    ->schema([
                        Forms\Components\Repeater::make('documents')
                            ->label('Documentos')
                            ->relationship('documents')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Título del documento')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\FileUpload::make('file_path')
                                    ->label('Archivo')
                                    ->directory('documents/tickets')
                                    ->acceptedFileTypes([
                                        'application/pdf',
                                        'application/msword',
                                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                        'application/vnd.ms-excel',
                                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                        'text/plain',
                                        'image/jpeg',
                                        'image/jpg',
                                        'image/png'
                                    ])
                                    ->maxSize(10240) // 10MB
                                    ->required()
                                    ->helperText('Formatos permitidos: PDF, DOC, DOCX, XLS, XLSX, TXT, JPG, JPEG, PNG. Máximo 10MB.'),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            // ->reorderable(false)
                            // ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                    ])
                    ->collapsed(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado'),
                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioridad'),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Fecha Inicio')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Fecha Fin')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('closed_at')
                    ->label('Cerrado el')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            // RelationManagers\DocumentRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }
}
