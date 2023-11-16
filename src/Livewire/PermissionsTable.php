<?php

namespace Farmit\RrrbacForLaravel\Livewire;

use Farmit\RrrbacForLaravel\Models\Permission;
use Farmit\RrrbacForLaravel\Models\Role;
use Farmit\RrrbacForLaravel\Models\Rule;
use Farmit\RrrbacForLaravel\Models\SushiRule;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Route;
use Livewire\Component;

class PermissionsTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public Role|null $role;
    public string|null $type;

    public function mount($role = null, $type = null): void
    {
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Permission::query()
                    ->selectRaw("*, REGEXP_SUBSTR(name, '^[a-z]+(?=::)') as type")
            )
            ->defaultGroup('type')
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        default => 'gray',
                        'route' => 'success',
                    }),
                TextColumn::make('rules.rule')
                    ->listWithLineBreaks()
                    ->bulleted(),
                ViewColumn::make('rules.rule')
                    ->view('rrrbac::livewire.filament.table-badge-list')
            ])
            ->headerActions([
                CreateAction::make()
                    ->form([
                        TextInput::make('name')
                    ]),
                ActionGroup::make([
                    Action::make('delete_orphan_route')
                        ->requiresConfirmation()
                        ->form(function () {
                            $routes_collection = collect(Route::getRoutes()->getRoutesByName())
                                ->keys()
                                ->filter(fn($route) => !in_array($route, config('rrrbac.authorized_routes'), true))
                                ->map(fn($route) => "route::$route");

                            $orphan_routes = Permission::where('name', 'like', 'route::%')
                                ->get()
                                ->pluck('name')
                                ->diff($routes_collection)
                                ->mapWithKeys(fn($route_name) => [$route_name => $route_name])
                                ->toArray();

                            return [
                                CheckboxList::make('orphan_routes')
                                    ->label('Orphan routes')
                                    ->options($orphan_routes)
                            ];
                        })
                        ->action(function (array $data) {
                            collect($data['orphan_routes'])
                                ->each(fn($route) => Permission::where('name', $route)->delete());
                        })
                ])
            ])
            ->actions([
                Action::make('Add rule')
                    ->icon('heroicon-m-hand-raised')
                    ->form([
                        Select::make('rules')
                            ->multiple()
                            ->afterStateHydrated(function (\Filament\Forms\Components\Component $component, $record) {
                                $component->state($record->rules->pluck('rule')->toArray());
                            })
                            ->options(SushiRule::all()->pluck('description', 'name'))
                    ])
                    ->action(function (array $data, $record) {
                        $data_rules = collect($data['rules']);

                        $to_remove = $record->rules->pluck('rule')
                            ->diff($data_rules)
                            ->toArray();

                        Rule::whereIn('rule', $to_remove)->delete();

                        $to_add = $data_rules->diff($record->rules->pluck('rule'))
                            ->map(fn($rule_class) => [
                                'applicable_type' => $record::class,
                                'applicable_id' => $record->id,
                                'rule' => $rule_class,
                            ])
                            ->toArray();

                        Rule::insert($to_add);
                    }),
                EditAction::make()
                    ->form([
                        TextInput::make('name')
                    ])
            ]);

    }

    public function render()
    {
        return view('rrrbac::livewire.filament.table-render');
    }
}
