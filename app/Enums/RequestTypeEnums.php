<?php

namespace App\Enums;

enum RequestTypeEnums: string
{
    case create = 'create';
    case update = 'update';
    case delete = 'delete';
}