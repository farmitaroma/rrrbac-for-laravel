<?php

namespace Farmit\RrrbacForLaravel\Livewire;

use Farmit\RrrbacForLaravel\Models\Permission;
use Farmit\RrrbacForLaravel\Models\Role;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\DetachBulkAction;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;

class PermissionAssigned extends BaseAssignmentTable
{

    public Role $authItem;
    public string $type;

    #[On('assigned-child')]
    public function mount($authItem, $type = null): void
    {
    }

    public function table(Table $table): Table
    {
        $actions = [
            ...parent::table($table)->getActions(),
            DetachAction::make()
                ->action(function (Permission $record) {
                    $this->authItem->revokePermissionTo($record->name);
                    $this->dispatch('removed-child', authItem: $this->authItem);
                })
        ];

        return parent::table($table)
            ->actions($actions)
            ->bulkActions([
                DetachBulkAction::make()
                    ->action(function (Collection $records) {
                        $records->each(function ($record) {
                            $this->authItem->revokePermissionTo($record->name);
                        });

                        $this->dispatch('removed-child', authItem: $this->authItem);
                    })
            ]);
    }
}
