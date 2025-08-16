<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTournamentRequest;
use App\Http\Requests\UpdateTournamentRequest;
use App\Http\Resources\TournamentResource;
use App\Models\Tournament;
use Illuminate\Http\Request;

class TournamentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tournaments = Tournament::query()->latest()->paginate(15);
        return TournamentResource::collection($tournaments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTournamentRequest $request)
    {
        $tournament = Tournament::create($request->validated());
        return new TournamentResource($tournament);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tournament $tournament)
    {
        return new TournamentResource($tournament);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTournamentRequest $request, Tournament $tournament)
    {
        $tournament->update($request->validated());
        return new TournamentResource($tournament);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tournament $tournament)
    {
        $tournament->delete();
        return response()->noContent();
    }
}
