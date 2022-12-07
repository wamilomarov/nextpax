<?php

namespace App\DTO;

use Carbon\CarbonPeriod;

class CompatibilityDTO
{
    public bool $compatible;
    public CarbonPeriod $remainingPeriod;
    public float|int $cost;
}
