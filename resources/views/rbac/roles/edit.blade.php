<x-rrrbac::layouts.app>
    <div class="grid gap-4">
        <div>
            <x-filament::card>
                @livewire('rrrbac.auth-item-infolist', ['authItem' => $authItem])
            </x-filament::card>
        </div>

        <x-filament::tabs label="Content tabs" class="mb-4">
            <x-filament::tabs.item
                :active="!$tab || $tab === '1'"
                :href="route('roles.edit', [
                    'tab' => 1,
                    'role' => $authItem
                ])"
                tag="a"
                icon="heroicon-m-key"
            >
                Permissions
            </x-filament::tabs.item>

            <x-filament::tabs.item
                :active="$tab === '2'"
                :href="route('roles.edit', [
                    'tab' => 2,
                    'role' => $authItem
                ])"
                tag="a"
                icon="heroicon-m-hand-raised"
            >
                Routes
            </x-filament::tabs.item>
        </x-filament::tabs>
    </div>

    <div class="grid gap-3 grid-cols-2">
        @if(!$tab || $tab === '1')
            <div class="col-span-1">
                @livewire('rrrbac::permission-assigned', ['authItem' => $authItem, 'type' => ''])
            </div>

            <div class="col-span-1">
                @livewire('rrrbac::permission-available', ['authItem' => $authItem, 'type' => ''])
            </div>
        @elseif($tab === '2')
            <div class="col-span-1">
                @livewire('rrrbac::permission-assigned', ['authItem' => $authItem, 'type' => 'route'])
            </div>

            <div class="col-span-1">
                @livewire('rrrbac::permission-available', ['authItem' => $authItem, 'type' => 'route'])
            </div>
        @endif
    </div>

</x-rrrbac::layouts.app>

