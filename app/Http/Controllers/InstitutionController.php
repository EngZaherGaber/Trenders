<?php

namespace App\Http\Controllers;

use App\Http\Resources\OfferResource;
use App\Http\Resources\TrenderResource;
use App\Models\Institution;
use App\Models\Offer;
use Illuminate\Http\Request;

class InstitutionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Institution $institution)
    {
        $institution->load(['user', 'tenders']);
        return $institution;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Institution $institution)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Institution $institution)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @response [
    {
        "id": 11,
        "trender_name": "Ut voluptates nesciunt nostrum temporibus.",
        "for": "Elta Crooks Jr.",
        "work_on_it": "2023-10-01T06:31:20.000000Z",
        "work_ended_it": null,
        "company_name": "Shawn Bechtelar PhD",
        "company_id": 22
    }
]
     */
    public function acceptedOffers()
    {
        $institution = auth()->user()->institution;
        $offers = Offer::where('accepted', true)
            ->whereHas('tender', fn ($query) => $query->where('institution_id', $institution->id))->get();

        return OfferResource::collection($offers);
    }
}
