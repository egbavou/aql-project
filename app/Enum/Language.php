<?php

namespace App\Enum;

enum Language: string
{
    case French = 'fr';
    case English = 'en';
    case Spanish = 'es';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return ['fr', 'en', 'es'];
    }
}
