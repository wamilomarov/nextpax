<?php

namespace Tests\Feature;

use App\Models\Availability;
use App\Models\Price;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ReflectionClass;
use ReflectionException;
use Tests\TestCase;

class AvailabilityServiceTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @throws ReflectionException
     */
    public function test_two_prices()
    {
//        2 prices:
//        1. minimum 1 day in a row - costs 250 eur + 120 eur per extra person
//        2. minimum 4 days in a row - costs 800 eur + 400 eur per extra person for 4 nights (200 + 100 a night)

        $class = new ReflectionClass(AvailabilityService::class);
        $method = $class->getMethod('calculatePrice');
        $method->setAccessible(true);

        $service = new AvailabilityService();

        /** @var Availability $availability */
        $availability = Availability::factory()
            ->createOne([
                'date' => Carbon::parse('2022-12-05'),
                'arrival_allowed' => true,
                'departure_allowed' => true,
                'minimum_stay' => 1,
                'maximum_stay' => 21,

            ]);

        Price::factory()
            ->createOne([
                'property_id' => $availability->property_id,
                'minimum_stay' => 4,
                'maximum_stay' => 21,
                'persons' => "1|2|3",
                'weekdays' => "0|1|2|3|4|5|6",
                'duration' => 4,
                'period_from' => Carbon::parse('2022-12-05'),
                'period_till' => Carbon::parse('2022-12-15'),
                'extra_person_price' => 40000,
                'amount' => 80000,
                'currency' => 'EUR',
                'extra_person_price_currency' => 'EUR',
            ]);

        Price::factory()
            ->createOne([
                'property_id' => $availability->property_id,
                'minimum_stay' => 1,
                'maximum_stay' => 21,
                'persons' => "1|2|3",
                'weekdays' => "0|1|2|3|4|5|6",
                'duration' => 1,
                'period_from' => Carbon::parse('2022-12-05'),
                'period_till' => Carbon::parse('2022-12-30'),
                'extra_person_price' => 12000,
                'amount' => 25000,
                'currency' => 'EUR',
                'extra_person_price_currency' => 'EUR',
            ]);

        $arrivalDate = CarbonImmutable::parse('2022-12-05');

        $persons = 2;

        $result = $method->invokeArgs($service, [$arrivalDate, $availability->prices, 6, $persons]);

        // 1 X 4 nights price + 2 X 1 night price
        $this->assertEquals(194000, $result);

        $result = $method->invokeArgs($service, [$arrivalDate, $availability->prices, 8, $persons]);

        // 2 X 4 nights price
        $this->assertEquals(240000, $result);

        $arrivalDate = CarbonImmutable::parse('2022-12-16');

        $result = $method->invokeArgs($service, [$arrivalDate, $availability->prices, 8, $persons]);

        // 8 X 1 night price, because its after 4 night price period
        $this->assertEquals(296000, $result);

        $arrivalDate = CarbonImmutable::parse('2022-12-04');

        $result = $method->invokeArgs($service, [$arrivalDate, $availability->prices, 8, $persons]);

        // not fitting any price period
        $this->assertEquals(0, $result);

        $arrivalDate = CarbonImmutable::parse('2023-01-04');

        $result = $method->invokeArgs($service, [$arrivalDate, $availability->prices, 8, $persons]);

        // not fitting any price period
        $this->assertEquals(0, $result);

        $arrivalDate = CarbonImmutable::parse('2022-12-05');
        $persons = 7;

        $result = $method->invokeArgs($service, [$arrivalDate, $availability->prices, 8, $persons]);

        // not fitting any price persons count
        $this->assertEquals(0, $result);
    }

    /**
     * @throws ReflectionException
     */
    public function test_three_prices()
    {
//        2 prices:
//        1. minimum 1 day in a row - costs 250 eur + 120 eur per extra person
//        2. minimum 4 days in a row - costs 800 eur + 400 eur per extra person for 4 nights (200 + 100 a night)
//        2. minimum 7 days in a row - costs 190 eur + 70 eur per extra person for 1 night

        $class = new ReflectionClass(AvailabilityService::class);
        $method = $class->getMethod('calculatePrice');
        $method->setAccessible(true);

        $service = new AvailabilityService();

        /** @var Availability $availability */
        $availability = Availability::factory()
            ->createOne([
                'date' => Carbon::parse('2022-12-05'),
                'arrival_allowed' => true,
                'departure_allowed' => true,
                'minimum_stay' => 1,
                'maximum_stay' => 21,

            ]);

        Price::factory()
            ->createOne([
                'property_id' => $availability->property_id,
                'minimum_stay' => 4,
                'maximum_stay' => 21,
                'persons' => "1|2|3",
                'weekdays' => "0|1|2|3|4|5|6",
                'duration' => 4,
                'period_from' => Carbon::parse('2022-12-05'),
                'period_till' => Carbon::parse('2022-12-30'),
                'extra_person_price' => 40000,
                'amount' => 80000,
                'currency' => 'EUR',
                'extra_person_price_currency' => 'EUR',
            ]);

        Price::factory()
            ->createOne([
                'property_id' => $availability->property_id,
                'minimum_stay' => 7,
                'maximum_stay' => 21,
                'persons' => "1|2|3",
                'weekdays' => "0|1|2|3|4|5|6",
                'duration' => 1,
                'period_from' => Carbon::parse('2022-12-05'),
                'period_till' => Carbon::parse('2022-12-30'),
                'extra_person_price' => 7000,
                'amount' => 19000,
                'currency' => 'EUR',
                'extra_person_price_currency' => 'EUR',
            ]);

        Price::factory()
            ->createOne([
                'property_id' => $availability->property_id,
                'minimum_stay' => 1,
                'maximum_stay' => 21,
                'persons' => "1|2|3",
                'weekdays' => "0|1|2|3|4|5|6",
                'duration' => 1,
                'period_from' => Carbon::parse('2022-12-05'),
                'period_till' => Carbon::parse('2022-12-30'),
                'extra_person_price' => 12000,
                'amount' => 25000,
                'currency' => 'EUR',
                'extra_person_price_currency' => 'EUR',
            ]);

        $arrivalDate = CarbonImmutable::parse('2022-12-05');

        $persons = 2;

        $result = $method->invokeArgs($service, [$arrivalDate, $availability->prices, 8, $persons]);

        // 8 X 7 nights rate
        $this->assertEquals(208000, $result);
    }

    /**
     * @throws ReflectionException
     */
    public function test_weekdays_check()
    {
//        2 prices:
//        1. minimum 1 day in a row - costs 250 eur + 120 eur per extra person
//        2. minimum 4 days in a row - costs 800 eur + 400 eur per extra person for 4 nights (200 + 100 a night)

        $class = new ReflectionClass(AvailabilityService::class);
        $method = $class->getMethod('calculatePrice');
        $method->setAccessible(true);

        $service = new AvailabilityService();

        /** @var Availability $availability */
        $availability = Availability::factory()
            ->createOne([
                'date' => Carbon::parse('2022-12-05'),
                'arrival_allowed' => true,
                'departure_allowed' => true,
                'minimum_stay' => 1,
                'maximum_stay' => 21,

            ]);

        Price::factory()
            ->createOne([
                'property_id' => $availability->property_id,
                'minimum_stay' => 4,
                'maximum_stay' => 21,
                'persons' => "1|2|3",
                'weekdays' => "2|3|4|5",
                'duration' => 4,
                'period_from' => Carbon::parse('2022-12-05'),
                'period_till' => Carbon::parse('2022-12-30'),
                'extra_person_price' => 40000,
                'amount' => 80000,
                'currency' => 'EUR',
                'extra_person_price_currency' => 'EUR',
            ]);

        Price::factory()
            ->createOne([
                'property_id' => $availability->property_id,
                'minimum_stay' => 1,
                'maximum_stay' => 21,
                'persons' => "1|2|3",
                'weekdays' => "0|1|2|3|4|5|6",
                'duration' => 1,
                'period_from' => Carbon::parse('2022-12-05'),
                'period_till' => Carbon::parse('2022-12-30'),
                'extra_person_price' => 12000,
                'amount' => 25000,
                'currency' => 'EUR',
                'extra_person_price_currency' => 'EUR',
            ]);

        //Monday
        $arrivalDate = CarbonImmutable::parse('2022-12-05');

        $persons = 2;

        $result = $method->invokeArgs($service, [$arrivalDate, $availability->prices, 6, $persons]);

        // 1 X 1 night price + 1 X 4 nights price + 1 X 1 night price (correct order)
        $this->assertEquals(194000, $result);
    }
}
