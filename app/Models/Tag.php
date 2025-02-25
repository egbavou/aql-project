<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin IdeHelperTag
 */
class Tag extends Model
{

    protected $fillable = [
        'name'
    ];

    protected $hidden = [
        'pivot'
    ];

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class);
    }
}
