<?php

namespace App\Enums\Entry;

enum Status: string
{
    case Allowed = 'allowed';
    case Prohibited = 'prohibited';
}
