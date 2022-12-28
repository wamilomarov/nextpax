<?php

namespace App\Services;

use App\DTO\AvailabilityDataDTO;
use App\DTO\CompatibilityDTO;
use App\Models\Availability;
use App\Models\Price;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class AvailabilityService
{
    const DEFAULT_PRICE = 0;

    public function getAvailabilities(): array
    {
        $data = [];
        $availabilities = Availability::query()
            ->with(['departureAvailabilities'])
            ->where('arrival_allowed', true)
            ->where('quantity', '>', 0)
            ->get();

        $minDate = $availabilities->min('date');
        $maxDate = $availabilities->max('date');

        $availabilities->load([
            'prices' => fn(HasMany $query) => $query->whereDate('period_from', '>=', $minDate)
                ->whereDate('period_till', '<=', $maxDate)
        ]);

        /** @var Availability $availability */
        foreach ($availabilities as $availability) {
            $maxPersons = $availability
                ->prices
                ->where('period_from', '<=', $availability->date)
                ->where('period_till', '>=', $availability->date)
                ->max('max_persons_number');
            $item = new AvailabilityDataDTO($availability);
            for ($i = 1; $i <= $maxPersons; $i++) {
                $item->prices[$i] = $this->getPricesByNumberOfPersons($availability, $i);
            }
            $data[] = $item;
        }

        return $data;
    }

    public function getPricesByNumberOfPersons(Availability $availability, int $persons): array
    {
        $prices = [];
        for ($i = 1; $i <= 21; $i++) {
            $prices[$i] = $this->getPriceByDurationAndNumberOfPersons($availability, $persons, $i) / 100;
        }
        return $prices;
    }

    private function getPriceByDurationAndNumberOfPersons(
        Availability $availability,
        int $persons,
        int $duration
    ): float|int {
        if (!$availability->arrival_allowed) {
            return self::DEFAULT_PRICE;
        }

        if ($availability->minimum_stay > $duration) {
            return self::DEFAULT_PRICE;
        }

        if ($availability->maximum_stay < $duration) {
            return self::DEFAULT_PRICE;
        }

        $diff = $duration - 1;
        $arrivalDate = $availability->date->toImmutable();
        $departureDate = $arrivalDate->addDays($diff);

        if (!$this->departureAllowed($availability, $departureDate)) {
            return self::DEFAULT_PRICE;
        }

        $prices = $availability
            ->prices
            ->where('duration', '<=', $duration)
            ->where('maximum_stay', '>=', $duration)
            ->where('minimum_stay', '<=', $duration)
            ->filter(fn(Price $price) => $price->period_from <= $departureDate && $price->period_till >= $arrivalDate);

        if ($prices->isEmpty()) {
            return self::DEFAULT_PRICE;
        }

        // filter by number of persons
        $prices = $prices
            ->filter(function (Price $value, $key) use ($persons) {
                return in_array($persons, $value->persons_list);
            });

        if ($prices->isEmpty()) {
            return self::DEFAULT_PRICE;
        }

        return $this->calculatePrice($arrivalDate, $prices, $duration, $persons);
    }

    private function calculatePrice(
        CarbonImmutable $arrivalDate,
        Collection $prices,
        int $duration,
        int $persons,
    ): float|int {
        $departureDate = $arrivalDate->addDays($duration - 1);

        // Prioritize lower rate prices
        $prices = $prices->sortBy([
            fn(Price $priceA, Price $priceB) => $priceA->period_from <=> $priceB->period_from,
            fn(
                Price $priceA,
                Price $priceB
            ) => $priceA->getRatePerNight($persons) <=> $priceB->getRatePerNight($persons),
        ])
            ->values();

        $period = CarbonPeriod::create($arrivalDate, $departureDate);

        return $this->optimizeAmount($prices, $period, $persons);
    }

    private function compatibleWithPeriod(CarbonPeriod $period, Price $price, int $persons): CompatibilityDTO
    {
        $compatibility = new CompatibilityDTO();
        $compatibility->compatible = false;
        $compatibility->cost = 0;
        $compatibility->remainingPeriod = $period;

        if ($price->duration > $period->count()) {
            return $compatibility;
        }

        if ($price->period_from > $period->end || $price->period_till < $period->start) {
            return $compatibility;
        }

        if (!in_array($persons, $price->persons_list)) {
            return $compatibility;
        }

        $pricePeriod = CarbonPeriod::create($period->start, $period->start->addDays($price->duration - 1));
        /** @var Carbon $day */
        foreach ($pricePeriod as $day) {
            if (!in_array($day->dayOfWeek, $price->weekdays_list)) {
                return $compatibility;
            }
        }
        $compatibility->compatible = true;
        $compatibility->cost = $this->calculateAmountByPersons($price, $persons);
        $compatibility->remainingPeriod = CarbonPeriod::create(
            $period->start->addDays($price->duration),
            $period->end
        );
        return $compatibility;
    }

    private function calculateAmountByPersons(Price $price, int $persons): float|int
    {
        $extraPeoplePrice = ($persons - $price->base_persons_count) * $price->extra_person_price;

        return $price->amount + $extraPeoplePrice;
    }

    private function optimizeAmount(Collection $prices, CarbonPeriod $period, int $persons): float|int
    {
        $amount = 0;

        foreach ($period as $day) {
            $coveringPrice = $prices
                ->where('period_from', '<=', $day)
                ->where('period_till', '>=', $day)
                ->count();
            if ($coveringPrice === 0) {
                return self::DEFAULT_PRICE;
            }
        }

        foreach ($prices as $price) {
            $result = $this->compatibleWithPeriod($period, $price, $persons);
            if ($result->compatible) {
                $amount += $result->cost;
                if ($period->count() === 1) {
                    return $amount;
                }
                $amount += $this->optimizeAmount($prices, $result->remainingPeriod, $persons);
                return $amount;
            }
        }

        return $amount;
    }

    private function departureAllowed(Availability $availability, CarbonInterface $date): bool
    {
        return $availability->departureAvailabilities
                ->where('date', $date)
                ->count() > 0;
    }
}
