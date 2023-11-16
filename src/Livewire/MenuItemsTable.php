<?php

namespace Farmit\RrrbacForLaravel\Livewire;

use Farmit\RrrbacForLaravel\Models\MenuItem;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;
use Livewire\Attributes\Layout;
use Livewire\Component;

class MenuItemsTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(MenuItem::query())
            ->columns([
                TextColumn::make('label'),
                TextColumn::make('parent.label'),
                IconColumn::make('icon')
                    ->icon(fn($record) => $record->icon),
                TextColumn::make('link'),
                TextColumn::make('order'),
            ])
            ->headerActions([
                CreateAction::make()->label('New Item')
                    ->form($this->formSchema())
            ])
            ->actions([
                EditAction::make()
                    ->form($this->formSchema()),
                DeleteAction::make()
            ])
            ->reorderable('order');
    }

    private function formSchema(): array
    {
        return [
            TextInput::make('label'),
            Select::make('link')
                ->searchable()
                ->options(
                    collect(Route::getRoutes()->getRoutesByName())
                        ->keys()
                        ->mapWithKeys(fn($route) => [$route => $route])
                ),
            TextInput::make('icon'),
            Select::make('menu_item_id')
                ->relationship(
                    name: 'parent',
                    titleAttribute: 'label',
                    modifyQueryUsing: fn(Builder $query) => $query->whereNull('link')
                )
        ];
    }

    #[Layout('rrrbac::components.layouts.app')]
    public function render()
    {
        return view('rrrbac::livewire.filament.table-render');
    }
}
