<?php

namespace App\Models;

enum FileRequestDocumentStatusEnum: int
{
    case PENDING = 0;
    case APPROVED = 1;
    case REJECTED = 2;
}
