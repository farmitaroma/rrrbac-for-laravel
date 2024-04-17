<?php

namespace Farmit\RrrbacForLaravel\Providers;

use Farmit\RrrbacForLaravel\Livewire\AuthItemInfolist;
use Farmit\RrrbacForLaravel\Livewire\MenuItemsTable;
use Farmit\RrrbacForLaravel\Livewire\PermissionAssigned;
use Farmit\RrrbacForLaravel\Livewire\PermissionAvailable;
use Farmit\RrrbacForLaravel\Livewire\PermissionsTable;
use Farmit\RrrbacForLaravel\Livewire\RolesTable;
use Farmit\RrrbacForLaravel\Livewire\UsersTable;
use Farmit\RrrbacForLaravel\Models\MenuItem;
use Farmit\RrrbacForLaravel\Models\Permission;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class RrrbacServiceProvider extends ServiceProvider
{
    public function boot(\Illuminate\Contracts\Auth\Access\Gate $gate): void
    {
        $this->publishes([
            __DIR__ . '/../../config/rrrbac.php' => config_path('rrrbac.php'),
        ]);

        $this->publishes([
            __DIR__ . '/../../dist/rrrbac.css' => public_path('css/rrrbac/rrrbac.css'),
        ]);

        $this->publishes([
            __DIR__ . '/../../database/migrations' => database_path('migrations'),
        ]);

        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'rrrbac');

        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');

        Livewire::component('rrrbac::users-table', UsersTable::class);
        Livewire::component('rrrbac::roles-table', RolesTable::class);
        Livewire::component('rrrbac.auth-item-infolist', AuthItemInfolist::class);
        Livewire::component('rrrbac::permission-assigned', PermissionAssigned::class);
        Livewire::component('rrrbac::permission-available', PermissionAvailable::class);
        Livewire::component('rrrbac::permission-table', PermissionsTable::class);
        Livewire::component('rrrbac::menu-items-table', MenuItemsTable::class);

        $gate->after(function (Authorizable $user, string $gate_name, bool|null $previous_result, array $args) {
            if ($previous_result) {
                $permission = Permission::where('name', $gate_name)->first();

                if ($permission) {
                    $rules = $permission->rules;

                    foreach ($rules as $rule) {
                        $rule_class = $rule->rule;

                        Gate::denyIf(!$rule_class::execute($user, $gate_name, $previous_result, $args));
                    }
                }

                return true;
            }

            return null;
        });

        $this->app->bind('menu', function () {

            if(!auth()->user()) {
                return [];
            }

            $items = MenuItem::all()->sortBy('order');

            $main_items = $items->where('menu_item_id', null)
                ->filter(function ($item) {
                    if (isset($item['link'])) {
                        return auth()->user()->can('route::' . $item['link']);
                    }

                    return true;
                })
                ->toArray();

            $children_items = $items->where('menu_item_id', '!=', null)->toArray();

            $nest = function (&$item, $children, $nest) {
                $item_children =
                    collect($children)
                        ->where('menu_item_id', $item['id'])
                        ->filter(fn($child) => auth()->user()->can('route::' . $child['link']));

                $item['children'] = $item_children->toArray();

                foreach ($item['children'] as $key => $child) {
                    if ($item_children->where('menu_item_id', $child['id'])) {
                        $nest($item['children'][$key], $children, $nest);
                    }
                }
            };

            foreach ($main_items as $key => $main_item) {
                $nest($main_items[$key], $children_items, $nest);
            }

            $main_items = array_filter($main_items, fn ($item) => isset($item['link']) || count($item['children']));

            return $main_items;
        });
    }
}