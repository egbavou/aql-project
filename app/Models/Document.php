<?php

namespace App\Models;

use App\Enum\Language;
use App\Enum\DocumentVisibility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin IdeHelperDocument
 */
class Document extends Model
{
    use HasFactory;

    const FOLDER = 'documents';

    protected $hidden = ['path'];

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

    public function accesses(): HasMany
    {
        return $this->hasMany(Access::class);
    }
}
