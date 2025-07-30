<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class CreateClient extends CreateRecord
{
    protected static string $resource = ClientResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Extraer los datos del usuario del array de datos
        $userData = [
            'name' => $data['user_name'],
            'email' => $data['user_email'],
            'password' => Hash::make($data['user_password']),
        ];

        // Crear el usuario
        $user = User::create($userData);

        // Asignar el rol de Cliente al usuario
        $user->assignRole('Cliente');

        // Remover los campos del usuario del array de datos del cliente
        unset($data['user_name'], $data['user_email'], $data['user_password'], $data['user_password_confirmation']);

        // Agregar el ID del usuario creado al cliente
        $data['user_count_id'] = $user->id;
        $data['user_id'] = auth()->id(); // Usuario que crea el cliente

        // Crear el cliente
        $client = static::getModel()::create($data);

        return $client;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
