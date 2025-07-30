<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Filament\Resources\TicketResource\RelationManagers;
use App\Models\Client;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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
                            ->hidden(!\Illuminate\Support\Facades\Auth::user()->hasRole('super_admin'))
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

                Forms\Components\Section::make('Fechas')
                    ->visible(function (?Model $record): bool {
                        // Habilitar solo si el estado NO es "En Progreso"
                        return $record?->status !== 'En Progreso'; // Ajusta según tu campo de estado
                    })
                    ->disabled(function () {
                        // Deshabilitar para todos excepto para super_admin, Administrador y Asignador
                        return !auth()->user()->hasAnyRole(['super_admin', 'Administrador', 'Asignador']);
                    })
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')

                            ->label('Fecha de Inicio'),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Fecha estimada de Finalización'),
                        Forms\Components\DateTimePicker::make('closed_at')
                            ->label('Cerrado el'),
                    ])->columns(3)
                    ->hidden(fn(string $operation): bool => $operation !== 'edit'), // Solo visible en edición


                Forms\Components\Section::make('Información Básica')
                    ->disabled(function (?Model $record): bool {
                        // Deshabilitar si el estado es "En Progreso" (o cualquier otro estado que definas)
                        return $record?->status === 'En Progreso'; // Ajusta según tu campo de estado
                    })
                    ->schema([

                        Forms\Components\Select::make('client_id')
                            ->label('Cliente')
                            ->relationship('client', 'name')
                            ->required()
                            ->live()
                            ->hidden(fn() => auth()->user()->hasRole('Cliente')) // Ocultar para clientes
                            ->disabled(fn() => auth()->user()->hasRole('Cliente')) // Deshabilitar para clientes
                            ->default(function () {
                                if (auth()->user()->hasRole('Cliente')) {
                                    $client = Client::where('user_count_id', auth()->id())->first();
                                    return $client?->id;
                                }
                                return null;
                            })
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
                            ->options(function (Forms\Get $get, $state) {
                                // Si estamos en modo edición (ya hay un state)
                                if ($state) {
                                    $type = $get('designation_type');
                                    if (!$type) return [];

                                    // Obtener el modelo actual para mostrar su nombre
                                    $model = $type::find($state);
                                    if ($model) {
                                        if ($type === 'App\Models\Subproject') {
                                            return [$model->id => "{$model->project->name} > {$model->name}"];
                                        }
                                        return [$model->id => $model->name];
                                    }
                                }

                                // Lógica normal para creación
                                $type = $get('designation_type');
                                $clientId = auth()->user()->hasRole('Cliente')
                                    ? Client::where('user_count_id', auth()->id())->first()?->id
                                    : $get('client_id');

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
                            ->preload()
                            ->getOptionLabelFromRecordUsing(function ($record) {
                                // Esto asegura que se muestre correctamente al cargar el formulario de edición
                                if ($record instanceof \App\Models\Subproject) {
                                    return "{$record->project->name} > {$record->name}";
                                }
                                return $record->name;
                            }),

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
                            ->itemLabel(fn(array $state): ?string => $state['title'] ?? null),
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
                    ->label('Tipo')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'bug' => 'danger',
                        'feature_request' => 'primary',
                        'support' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'bug' => 'Bug',
                        'feature_request' => 'Solicitud de Característica',
                        'support' => 'Soporte',
                        default => 'Desconocido',
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->visible(fn() => auth()->user()->hasAnyRole(['super_admin', 'Administrador', 'Asignador']))  // Solo visible para super_admin, Administrador y Asignador
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'open' => 'primary',
                        'in_progress' => 'warning',
                        'stop' => 'danger',
                        'closed' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'open' => 'Abierto',
                        'in_progress' => 'En Progreso',
                        'stop' => 'Detenido',
                        'closed' => 'Cerrado',
                        default => 'Desconocido',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioridad')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'low' => 'success',
                        'medium' => 'warning',
                        'high' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'low' => 'Baja',
                        'medium' => 'Media',
                        'high' => 'Alta',
                        default => 'Desconocida',
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha Creación')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('F. Inicio')
                    ->placeholder('No definida') // Texto que aparecerá cuando el valor sea null/empty
                    ->color(fn($state) => $state ? null : 'gray') // Opcional: cambiar color cuando no hay fecha
                    ->date()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('users')
                    ->label('Responsable')
                    ->visible(fn() => auth()->user()->hasAnyRole(['super_admin', 'Administrador', 'Asignador']))
                    ->formatStateUsing(function ($state, $record) {
                        // Buscar el primer usuario responsable del ticket
                        $responsibleUser = $record->users()
                            ->wherePivot('is_responsible', true)
                            ->first();

                        return $responsibleUser ? $responsibleUser->name : 'Sin asignar';
                    })
                    ->placeholder('Sin asignar')
                    ->color(fn($state) => $state === 'Sin asignar' ? 'gray' : null)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('F. Estimada')
                    ->placeholder('No definida')
                    ->color(fn($state) => $state ? null : 'gray')
                    ->date()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('closed_at')
                    ->label('F. de Cierre')
                    ->placeholder('No cerrado')
                    ->color(fn($state) => $state ? 'success' : 'gray')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->toggleColumnsTriggerAction(
                fn (Tables\Actions\Action $action) => $action
                    ->button()
                    ->label('Columnas'),
            )
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'open' => 'Abierto',
                        'in_progress' => 'En Progreso',
                        'stop' => 'Detenido',
                        'closed' => 'Cerrado',
                    ]),

                Tables\Filters\SelectFilter::make('priority')
                    ->label('Prioridad')
                    ->options([
                        'low' => 'Baja',
                        'medium' => 'Media',
                        'high' => 'Alta',
                    ]),

                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'bug' => 'Bug',
                        'feature_request' => 'Solicitud de Característica',
                        'support' => 'Soporte',
                    ]),

                Tables\Filters\Filter::make('has_start_date')
                    ->label('Con Fecha de Inicio')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('start_date')),

                Tables\Filters\Filter::make('has_end_date')
                    ->label('Con Fecha Estimada')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('end_date')),

                Tables\Filters\Filter::make('is_closed')
                    ->label('Cerrados')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('closed_at')),

                Tables\Filters\Filter::make('is_open')
                    ->label('Abiertos')
                    ->query(fn (Builder $query): Builder => $query->whereNull('closed_at')),

                Tables\Filters\Filter::make('overdue')
                    ->label('Vencidos')
                    ->query(fn (Builder $query): Builder => $query->where('end_date', '<', now()->toDateString())
                        ->whereNull('closed_at')),

                Tables\Filters\Filter::make('due_soon')
                    ->label('Por Vencer (7 días)')
                    ->query(fn (Builder $query): Builder => $query->where('end_date', '>=', now()->toDateString())
                        ->where('end_date', '<=', now()->addDays(7)->toDateString())
                        ->whereNull('closed_at')),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Fecha desde'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Fecha hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Creado desde: ' . \Carbon\Carbon::parse($data['created_from'])->format('d/m/Y');
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Creado hasta: ' . \Carbon\Carbon::parse($data['created_until'])->format('d/m/Y');
                        }

                        return $indicators;
                    }),
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
            RelationManagers\UserRelationManager::class,
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

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $client = Client::where('user_count_id', auth()->id())->first();

        // Si el usuario es Cliente, filtrar solo sus proyectos
        if (auth()->check() && auth()->user()->hasRole('Cliente')) {
            return $query->where('client_id', $client?->id);
        }

        return $query;
    }
}
