<?php

namespace Farmit\RrrbacForLaravel\Livewire;

use Farmit\RrrbacForLaravel\Models\Role;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class RolesTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Role::query())
            ->columns([
                TextColumn::make('name')
            ])
            ->headerActions([
                CreateAction::make()
                    ->form([
                        TextInput::make('name'),
                    ]),
            ])
            ->actions([
                EditAction::make()
                    ->url(fn(Role $record) => route('roles.edit', $record)),
                DeleteAction::make()
                    ->action(function ($record) {
                        $record->rules()->delete();
                        $record->routes()->delete();

                        $record->delete();
                    })
            ]);
    }

    public function render()
    {
        return view('rrrbac::livewire.filament.table-render');
    }
}
