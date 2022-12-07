<?php

namespace App\Http\Controllers;

use App\Models\Availability;
use App\Services\AvailabilityService;

class AvailabilityController extends Controller
{
    public function __construct(protected AvailabilityService $availabilityService)
    {
    }

    public function index()
    {
         $dates = $this->availabilityService->getAvailabilities();
         return view('availabilities')->with(compact('dates'));
    }
}
