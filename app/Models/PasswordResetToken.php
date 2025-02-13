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

    public $timestamps = ["created_at"];

    const UPDATED_AT = null;
}
