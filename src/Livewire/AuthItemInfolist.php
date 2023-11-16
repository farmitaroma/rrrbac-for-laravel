<?php

namespace Farmit\RrrbacForLaravel\Livewire;

use Farmit\RrrbacForLaravel\Models\Permission;
use Farmit\RrrbacForLaravel\Models\Role;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Livewire\Component;

class AuthItemInfolist extends Component implements HasForms, HasInfolists
{
    use InteractsWithInfolists;
    use InteractsWithForms;

    public Permission|Role $authItem;

    public function mount($authItem): void
    {
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->authItem)
            ->schema([
                TextEntry::make('name')
            ]);
    }

    public function render()
    {
        return view('rrrbac::livewire.filament.infolist-render');
    }
}
