<?php

namespace Farmit\RrrbacForLaravel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    protected $fillable = [
        'label',
        'link',
        'icon',
        'menu_item_id',
        'order',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'menu_item_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class);
    }
}
