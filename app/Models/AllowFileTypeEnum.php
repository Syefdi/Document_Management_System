<?php

namespace App\Models;

enum AllowFileTypeEnum: int
{
    case Office = 0;
    case Pdf = 1;
    case Image = 2;
    case Text = 3;
    case Audio = 4;
    case Video = 5;
    case Other = 6;

    public static function getName(int $value): string
    {
        return self::tryFrom($value)?->name ?? 'Unknown';
    }
}
