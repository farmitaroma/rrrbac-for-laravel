<?php

namespace Farmit\RrrbacForLaravel\Livewire;

use Farmit\RrrbacForLaravel\Models\Role;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\Factory;
use Illuminate\View\View;
use Livewire\Component;

class UsersTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        $auth_class = config('rrrbac.auth_class');

        return $table
            ->query($auth_class::query())
            ->columns([
                TextColumn::make('username')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->listWithLineBreaks()
                    ->bulleted()
            ])
            ->actions([
                Action::make('roles')
                    ->form([
                        Select::make('roles')
                            ->afterStateHydrated(function (\Filament\Forms\Components\Component $component, $record) {
                                $component->state($record->roles->pluck('name')->toArray());
                            })
                            ->multiple()
                            ->options(Role::all()->pluck('name', 'name'))
                            ->preload(),
                    ])
                    ->action(function (array $data, Model $record) {
                        $record->syncRoles($data['roles']);
                    }),
            ]);
    }

    public function render(): Factory|View
    {
        return view('rrrbac::livewire.filament.table-render');
    }
}
