<?php

namespace App\Enum;

enum DocumentVisibility: string
{
    case publicFile = 'public';
    case privateFile = 'private';
    case linkSharedFile = 'link_shared';
    case accountSharedFile = 'account_shared';

    public static function values(): array
    {
        return ['public', 'private', 'link_shared', 'account_shared'];
    }
}
