<?php

namespace Farmit\RrrbacForLaravel\Models;

use Illuminate\Database\Eloquent\Model;
use ReflectionClass;
use Sushi\Sushi;

class SushiRule extends Model
{
    use Sushi;

    public function getRows()
    {
        return collect(scandir(app_path() . '/Rules'))
            ->filter(fn($path) => $path !== '.' && $path !== '..')
            ->map(fn($class_filename) => preg_replace('/\.php$/', '', $class_filename))
            ->map(function ($class) {
                return new ReflectionClass("App\\Rules\\$class");
            })
            ->filter(fn($reflection_class) => !$reflection_class->isAbstract())
            ->map(fn($reflection_class) => new $reflection_class->name)
            ->map(fn($rule) => [
                'name' => $rule::class,
                'description' => $rule->description,
            ])
            ->values()
            ->all();
    }
}
