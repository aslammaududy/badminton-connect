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
        $requests = PartnerRequest::query()->with(['requester','responder'])->latest()->paginate(15);
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
