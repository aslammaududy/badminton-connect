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
        $courts = Court::query()->latest()->paginate(15);
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
