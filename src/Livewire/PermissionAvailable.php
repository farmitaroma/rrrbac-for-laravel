<?php

namespace Farmit\RrrbacForLaravel\Livewire;

use Farmit\RrrbacForLaravel\Models\Permission;
use Farmit\RrrbacForLaravel\Models\Role;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Route;
use Livewire\Attributes\On;
use Illuminate\Database\Eloquent\Builder;

class PermissionAvailable extends BaseAssignmentTable
{

    public Role $authItem;
    public string $type;

    #[On('assigned-child'), On('removed-child')]
    public function mount($authItem, $type = null): void
    {
        //CHECK FOR NEW ROUTE TO SAVE AS PERMISSION
        if ($type === 'route') {
            $routes_collection = collect(Route::getRoutes()->getRoutesByName())
                ->keys()
                ->filter(fn($route) => !in_array($route, config('rrrbac.authorized_routes'), true))
                ->map(fn($route) => "route::$route");

            $new_routes = $routes_collection->diff(
                Permission::whereIn('name', $routes_collection)->get()
                    ->pluck('name')
            )
                ->map(fn($route) => ['name' => (string)$route, 'guard_name' => 'web'])
                ->toArray();

            Permission::insert($new_routes);
        }
    }

    public function table(Table $table): Table
    {
        $actions = [
            ...parent::table($table)->getActions(),
            Action::make('attach')
                ->icon('heroicon-m-plus')
                ->action(function (Permission $record) {
                    $this->authItem->givePermissionTo($record->name);

                    $this->dispatch('assigned-child', authItem: $this->authItem);
                })
        ];

        return parent::table($table)
            ->actions($actions)
            ->bulkActions([
                BulkAction::make('attach')
                    ->action(function ($records) {
                        $records->each(function ($record) {
                            $this->authItem->givePermissionTo($record->name);
                        });

                        $this->dispatch('assigned-child', authItem: $this->authItem);
                    })
            ])
            ->queryStringIdentifier('available');
    }

    protected function getQuery(): Builder
    {
        return parent::getQuery()->whereNotIn('name', $this->authItem->permissions->pluck('name'));
    }
}
