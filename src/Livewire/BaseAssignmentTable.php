<?php

namespace Farmit\RrrbacForLaravel\Livewire;

use Farmit\RrrbacForLaravel\Models\Permission;
use Farmit\RrrbacForLaravel\Models\Role;
use Farmit\RrrbacForLaravel\Models\Rule;
use Farmit\RrrbacForLaravel\Models\SushiRule;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Set;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Factory;
use Illuminate\View\View;
use Livewire\Component;

abstract class BaseAssignmentTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public Role $authItem;
    public string $type;

    abstract public function mount($authItem, $type = null): void;

    public function render(): Factory|View
    {
        return view('rrrbac::livewire.filament.table-render');
    }

    public function table(Table $table): Table
    {
        $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->formatStateUsing(fn(string $state) => preg_replace('/^\w+::/', '', $state)),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        default => 'gray',
                        'route' => 'success',
                    }),
                TextColumn::make('rules.rule')
                    ->listWithLineBreaks()
                    ->bulleted()
            ])
            ->actions([
                Action::make('Add rule')
                    ->icon('heroicon-m-hand-raised')
                    ->form([
                        Select::make('rules')
                            ->multiple()
                            ->afterStateHydrated(function (Set $set, \Filament\Forms\Components\Component $component, $record) {
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
            ])
            ->query($this->getQuery())
            ->queryStringIdentifier('assigned');

        return $table;
    }

    protected function getQuery(): Builder
    {
        $query = match ($this::class) {
            PermissionAssigned::class => $this->authItem->permissions()->getQuery(),
            PermissionAvailable::class => Permission::query(),
        };

        $db_connection_driver = DB::getDriverName();

        $db_regexp_functions = [
            'mysql' => 'REGEXP',
            'pgsql' => '~'
        ];

        if ($this->type !== '') {
            $query->where('name', $db_regexp_functions[$db_connection_driver], "^$this->type::");

            if ($this->type === 'route') {
                $type_pattern = '^[a-z]+(?=::)';

                $select_type = "REGEXP_SUBSTR(name, '$type_pattern') as type";

                $name_less_type = "REGEXP_REPLACE(name, '$type_pattern', '')";

                $select_grouped_by = "REGEXP_SUBSTR($name_less_type, '[A-Za-z]+') as grouped_by";

                $query->selectRaw("*, $select_type, $select_grouped_by");
            }
        } else {
            $query->whereNot('name', $db_regexp_functions[$db_connection_driver], '^[a-z]+::');
        }

        return $query;
    }
}
