<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property int $property_id
 * @property Carbon $date
 * @property int $quantity
 * @property boolean $arrival_allowed
 * @property boolean $departure_allowed
 * @property int $minimum_stay
 * @property int $maximum_stay
 * @property int $version
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Collection|Price[] $prices
 * @property Collection|Availability[] $departureAvailabilities
 */
class Availability extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'property_id',
        'date',
        'quantity',
        'arrival_allowed',
        'departure_allowed',
        'minimum_stay',
        'maximum_stay',
        'version',
    ];

    protected $dates = [
        'date',
    ];

    public function prices(): HasMany
    {
        return $this->hasMany(Price::class, 'property_id', 'property_id');
    }

    public function departureAvailabilities(): HasMany
    {
        return $this
            ->hasMany(Availability::class, 'property_id', 'property_id')
            ->where('departure_allowed', true);
    }
}
