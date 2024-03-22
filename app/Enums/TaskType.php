<?php

namespace App\Enums;

enum TaskType: int
{
    case LEAVE = 1;
    case OBT = 2;
    case SV = 3;
    case COA = 4;
    case WFH = 5;

    public function listtask(): string
    {
        return match ($this) {
            TaskType::LEAVE => 'LEAVE',
            TaskType::OBT => 'OBT',
            TaskType::SV => 'SITE VISIT',
            TaskType::COA => 'COA',
            TaskType::WFH => 'WORK FROM HOME',
        };
    }
}
