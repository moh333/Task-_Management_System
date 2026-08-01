<?php

namespace App\Enums;

enum TaskStatus: string
{
    case Todo       = 'Todo';
    case InProgress = 'In Progress';
    case Done       = 'Done';
}
