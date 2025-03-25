<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperPasswordResetToken
 */
class PasswordResetToken extends Model
{
    protected $fillable = [
        'email',
        'token'
    ];

    public $timestamps = false;

    public static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }

    const UPDATED_AT = null;
}
