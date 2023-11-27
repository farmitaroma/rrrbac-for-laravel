<?php

namespace Farmit\RrrbacForLaravel\Livewire\Trait;


trait CanView
{
    public function boot()
    {
        if(auth()->user()->cannot('component::' . $this::class)) {
            $this->skipRender();
        }
    }
}