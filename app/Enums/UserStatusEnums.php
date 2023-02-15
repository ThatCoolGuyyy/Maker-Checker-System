<?php

namespace App\Enums;

enum UserStatusEnums: string
{
    case pending = 'pending';
    case approved = 'approved';
    case rejected = 'rejected';
}