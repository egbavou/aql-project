<?php

namespace App\Models;

use App\Enum\DocumentVisibility;
use App\Enum\Language;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin IdeHelperDocument
 */
class Document extends Model
{
    protected $fillable = [
        'author',
        'path',
        'title',
        'downloads',
        'size',
        'pages',
        'language',
        'visibility',
        'token',
        'user_id'
    ];

    protected $casts = [
        'downloads'  => 'integer',
        'size'       => 'integer',
        'pages'      => 'integer',
        'language'   => Language::class,
        'visibility' => DocumentVisibility::class
    ];

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
