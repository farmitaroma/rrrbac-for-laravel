<?php

namespace Farmit\RrrbacForLaravel\Models;

use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    protected $table = 'model_has_rules';

    protected $fillable = [
        'applicable_type',
        'applicable_id',
        'rule'
    ];
}
