<?php

namespace App\DTO;

use App\Models\Availability;

class AvailabilityDataDTO
{
    public Availability $availability;
    public array $prices;

    public function __construct(Availability $availability, ?array $prices = [])
    {
        $this->availability = $availability;
        $this->prices = $prices;
    }
}
