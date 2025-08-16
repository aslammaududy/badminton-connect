<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMatchRequest;
use App\Http\Requests\UpdateMatchRequest;
use App\Http\Resources\MatchResource;
use App\Models\GameMatch;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $matches = GameMatch::query()->with(['organizer','tournament','court'])->latest()->paginate(15);
        return MatchResource::collection($matches);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMatchRequest $request)
    {
        $match = GameMatch::create($request->validated());
        return new MatchResource($match->load(['organizer','tournament','court']));
    }

    /**
     * Display the specified resource.
     */
    public function show(GameMatch $match)
    {
        return new MatchResource($match->load(['organizer','tournament','court']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMatchRequest $request, GameMatch $match)
    {
        $match->update($request->validated());
        return new MatchResource($match->load(['organizer','tournament','court']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GameMatch $match)
    {
        $match->delete();
        return response()->noContent();
    }
}
