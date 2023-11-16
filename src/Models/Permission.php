<?php

namespace Farmit\RrrbacForLaravel\Models;

use Illuminate\Database\Eloquent\Relations\MorphMany;

class Permission extends \Spatie\Permission\Models\Permission
{
    public function rules(): MorphMany
    {
        return $this->morphMany(Rule::class, 'applicable');
    }

    public function sushiRules()
    {
        return $this->hasMany(SushiRule::class);
    }
}
