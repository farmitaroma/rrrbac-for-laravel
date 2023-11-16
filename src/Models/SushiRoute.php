<?php

namespace Farmit\RrrbacForLaravel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Sushi\Sushi;

class SushiRoute extends Model
{
    use Sushi;

    public function getRows(): array
    {
        return collect(Route::getRoutes()->getRoutesByName())
            ->keys()
            ->map(fn($name) => ['name' => $name])
            ->toArray();
    }
}
