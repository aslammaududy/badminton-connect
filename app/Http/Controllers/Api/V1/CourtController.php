<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourtRequest;
use App\Http\Requests\UpdateCourtRequest;
use App\Http\Resources\CourtResource;
use App\Models\Court;
use Illuminate\Http\Request;

class CourtController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $q = Court::query();

        $lat = request('lat');
        $lng = request('lng');
        $radius = request('radius'); // in km

        if ($lat !== null && $lng !== null) {
            $q->select('*')
              ->selectRaw('(
                    6371 * acos(
                        cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) +
                        sin(radians(?)) * sin(radians(latitude))
                    )
                ) as distance_km', [$lat, $lng, $lat])
              ->orderBy('distance_km');

            if ($radius !== null) {
                $q->having('distance_km', '<=', (float) $radius);
            }
        } else {
            $q->latest();
        }

        $courts = $q->paginate(15);
        return CourtResource::collection($courts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourtRequest $request)
    {
        $court = Court::create($request->validated());
        return new CourtResource($court);
    }

    /**
     * Display the specified resource.
     */
    public function show(Court $court)
    {
        return new CourtResource($court);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourtRequest $request, Court $court)
    {
        $court->update($request->validated());
        return new CourtResource($court);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Court $court)
    {
        $court->delete();
        return response()->noContent();
    }
}
