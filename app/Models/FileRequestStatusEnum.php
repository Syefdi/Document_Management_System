<?php

namespace App\Models;

enum FileRequestStatusEnum: int
{
    case CREATED = 0;
    case UPLOADED = 1;
}
