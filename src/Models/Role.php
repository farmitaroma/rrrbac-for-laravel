<?php

namespace Farmit\RrrbacForLaravel\Models;

use Illuminate\Database\Eloquent\Relations\MorphMany;

class Role extends \Spatie\Permission\Models\Role
{
    public function rules(): MorphMany
    {
        return $this->morphMany(Rule::class, 'applicable');
    }
}
