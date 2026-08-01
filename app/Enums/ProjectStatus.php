<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case Active = 'Active';
    case Completed = 'Completed';
    case Archived = 'Archived';
}
