<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DocumentRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $title = 'Documentos';
    protected static ?string $label = 'Documento';
    protected static ?string $pluralLabel = 'Documentos';
    protected static ?string $navigationLabel = 'Documentos';

    public function form(Form $form): Form
    {
        return $form
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
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('file_path')
                    ->label('Archivo')
                    ->formatStateUsing(fn (string $state): string => basename($state))
                    ->searchable(),




            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Agregar Documento'),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Descargar')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn ($record) => asset('storage/' . $record->file_path))
                    ->openUrlInNewTab(),


                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar seleccionados'),
                ]),
            ]);
    }
}
