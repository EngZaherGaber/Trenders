<?php

namespace App\Http\Controllers;

use App\Http\Requests\OfferIndexRequest;
use App\Http\Requests\StoreOfferRequest;
use App\Http\Resources\OfferResource;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
     * 
     * @bodyParam details object. Example: [
      {
        "tender_detail_id": 1,
        "answer": "dasdsad",
      }
     * ]
     */
    public function store(StoreOfferRequest $request)
    {
        $data = $request->validated();

        $offer = auth()->user()->company->offers()->create($data);

        foreach ($data['details'] as $offerDetails) {

            if (is_array($offerDetails['data'])) {
                $offerDetails['data'] = json_encode($offerDetails['data']);
            }

            $vaildator = Validator::make($offerDetails, [
                'answer' => ['required'],
                'tender_detail_id' => ['required', 'exists:tender_details,id'],
            ]);

            $offer->offerDetails()->create($vaildator->validated());
        }

        return $offer;
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
     * 
     * @bodyParam details object. Example: [
            {
              "tender_detail_id": 1,
              "answer": "dasdsad",
            }
     * ]
     */
    public function update(StoreOfferRequest $request, Offer $offer)
    {
        $data = $request->validated();

        $offer->update($data);

        $offer->offerDetails()->delete();

        foreach ($data['details'] as $offerDetails) {

            if (is_array($offerDetails['data'])) {
                $offerDetails['data'] = json_encode($offerDetails['data']);
            }

            $vaildator = Validator::make($offerDetails, [
                'answer' => ['required'],
                'tender_detail_id' => ['required', 'exists:tender_details,id'],
            ]);

            $offer->offerDetails()->create($vaildator->validated());
        }
        $offer->refresh();

        return $offer;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Offer $offer)
    {
        $offer->delete();

        return response()->noContent();
    }
}
