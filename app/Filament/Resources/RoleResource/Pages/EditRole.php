<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    public Collection $permissions;

    protected function getActions(): array
    {
       return [
        Actions\DeleteAction::make()
            ->visible(function ($record) {
                $protectedRoles = ['super_admin', 'Cliente', 'Desarrollador', 'Administrador', 'Asignador'];
                return !in_array($record->name, $protectedRoles);
            })
            ->before(function ($record) {
                $protectedRoles = ['super_admin', 'Cliente', 'Desarrollador', 'Administrador', 'Asignador'];

                if (in_array($record->name, $protectedRoles)) {
                    Notification::make()
                        ->title('Acción no permitida')
                        ->body('No puedes eliminar roles del sistema')
                        ->danger()
                        ->send();

                    $this->halt();
                }
            })
    ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->permissions = collect($data)
            ->filter(function ($permission, $key) {
                return ! in_array($key, ['name', 'guard_name', 'select_all', Utils::getTenantModelForeignKey()]);
            })
            ->values()
            ->flatten()
            ->unique();

        if (Arr::has($data, Utils::getTenantModelForeignKey())) {
            return Arr::only($data, ['name', 'guard_name', Utils::getTenantModelForeignKey()]);
        }

        return Arr::only($data, ['name', 'guard_name']);
    }

    protected function afterSave(): void
    {
        $permissionModels = collect();
        $this->permissions->each(function ($permission) use ($permissionModels) {
            $permissionModels->push(Utils::getPermissionModel()::firstOrCreate([
                'name' => $permission,
                'guard_name' => $this->data['guard_name'],
            ]));
        });

        $this->record->syncPermissions($permissionModels);
    }
}
