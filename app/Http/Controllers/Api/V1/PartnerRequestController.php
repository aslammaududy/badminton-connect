<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePartnerRequestRequest;
use App\Http\Requests\UpdatePartnerRequestRequest;
use App\Http\Resources\PartnerRequestResource;
use App\Models\PartnerRequest;
use Illuminate\Http\Request;

class PartnerRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $q = PartnerRequest::query()->with(['requester','responder']);

        $lat = request('lat');
        $lng = request('lng');
        $radius = request('radius'); // in km

        if ($lat !== null && $lng !== null) {
            $q->withDistanceFrom((float) $lat, (float) $lng)
              ->nearestTo((float) $lat, (float) $lng);

            if ($radius !== null) {
                $q->withinRadius((float) $lat, (float) $lng, (float) $radius * 1000);
            }
        } else {
            $q->latest();
        }

        $requests = $q->paginate(15);
        return PartnerRequestResource::collection($requests);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePartnerRequestRequest $request)
    {
        $partnerRequest = PartnerRequest::create($request->validated());
        return new PartnerRequestResource($partnerRequest->load(['requester','responder']));
    }

    /**
     * Display the specified resource.
     */
    public function show(PartnerRequest $partnerRequest)
    {
        return new PartnerRequestResource($partnerRequest->load(['requester','responder']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePartnerRequestRequest $request, PartnerRequest $partnerRequest)
    {
        $partnerRequest->update($request->validated());
        return new PartnerRequestResource($partnerRequest->load(['requester','responder']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PartnerRequest $partnerRequest)
    {
        $partnerRequest->delete();
        return response()->noContent();
    }
}
