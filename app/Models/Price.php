<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * @property int $id
 * @property int $property_id
 * @property int $duration
 * @property int $amount
 * @property string $currency
 * @property string $persons
 * @property array $persons_list
 * @property int $base_persons_count
 * @property string $weekdays
 * @property array $weekdays_list
 * @property int $minimum_stay
 * @property int $maximum_stay
 * @property int $extra_person_price
 * @property string $extra_person_price_currency
 * @property Carbon $period_from
 * @property Carbon $period_till
 * @property int $version
 * @property int $max_persons_number
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Price extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'property_id',
        'duration',
        'amount',
        'currency',
        'persons',
        'weekdays',
        'minimum_stay',
        'maximum_stay',
        'extra_person_price',
        'extra_person_price_currency',
        'period_from',
        'period_till',
        'version',
    ];

    protected $dates = [
        'period_from',
        'period_till',
    ];

    public function getPersonsListAttribute(): array
    {
        return explode('|', $this->persons);
    }

    public function getWeekdaysListAttribute(): array
    {
        return explode('|', $this->weekdays);
    }

    public function getBasePersonsCountAttribute()
    {
        return Arr::get($this->persons_list, 0, 1);
    }

    public function getMaxPersonsNumberAttribute()
    {
        return max($this->persons_list);
    }

    public function getRatePerNight(int $persons): float|int
    {
        $extraPeoplePrice = ($persons - $this->base_persons_count) * $this->extra_person_price;

        return ($this->amount + $extraPeoplePrice) / $this->duration;
    }
}
