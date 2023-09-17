<?php

namespace App\Http\Controllers;

use App\Http\Requests\OfferIndexRequest;
use App\Http\Resources\OfferResource;
use App\Models\Offer;
use Illuminate\Http\Request;

/**
 * 
 * @group Offer
 */
class OfferController extends Controller
{
    /**
     * @response {
    "data": [
        {
            "id": 10,
            "trender_name": "Reprehenderit est esse et.",
            "for": "Mr. Damon Lockman",
            "work_on_it": "2023-09-17T08:12:58.000000Z",
            "work_ended_it": null
        }
    ]
}
     */
    public function index(OfferIndexRequest $request)
    {
        if ($request->type == 'ended') {
            $offers = auth()->user()->company->offers()->with('tender')->whereHas('tender', fn ($query) => $query->whereNotNull('ended_at'))->get();
        } else {
            $offers =  auth()->user()->company->offers()->with('tender')->where('is_draft', true)->get();
        }

        return OfferResource::collection($offers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * @response {
    "data": {
        "id": 10,
        "trender_name": "Reprehenderit est esse et.",
        "for": "Mr. Damon Lockman",
        "work_on_it": "2023-09-17T08:12:58.000000Z",
        "work_ended_it": null,
        "offer_details": [
            {
                "id": 5,
                "offer_id": 10,
                "tender_detail_id": 10,
                "answer": "quo",
                "created_at": "2023-09-17T08:12:58.000000Z",
                "updated_at": "2023-09-17T08:12:58.000000Z"
            }
        ]
    }
}
     */
    public function show(Offer $offer)
    {
        $offer->load(['tender', 'offerDetails']);
        return new OfferResource($offer);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Offer $offer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Offer $offer)
    {
        //
    }
}
