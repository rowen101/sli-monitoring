<?php

namespace App\Enums;

enum TechRecomStatus: int
{
    case PENDING = 1;
    case APPROVED = 2;
    case Disapproved = 3;

    public function color(): string
    {
        return match ($this) {
            TechRecomStatus::PENDING => 'primary',
            TechRecomStatus::APPROVED => 'success',
            TechRecomStatus::Disapproved => 'danger',
        };
    }
}
