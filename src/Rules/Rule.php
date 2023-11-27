<?php

namespace Farmit\RrrbacForLaravel\Rules;

use Illuminate\Contracts\Auth\Access\Authorizable;

abstract class Rule
{
    public string $description;

    abstract public static function execute(Authorizable $user, string $gate_name, bool $previous_result, array $args): bool;
}
