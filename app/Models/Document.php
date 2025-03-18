<?php

namespace App\Models;

use App\Enum\Language;
use App\Enum\DocumentVisibility;
use Database\Factories\DocumentFactory;
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
    /** @use HasFactory<DocumentFactory> */
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

    /**
     * @return BelongsToMany<Tag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<Access, $this>
     */
    public function accesses(): HasMany
    {
        return $this->hasMany(Access::class);
    }
}
