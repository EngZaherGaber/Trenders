<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchTenderRequest;
use App\Http\Requests\StoreTenderRequest;
use App\Http\Requests\UpdateTenderRequest;
use App\Http\Resources\TrenderHomeResource;
use App\Http\Resources\TrenderResource;
use App\Models\Category;
use App\Models\Tender;
use Illuminate\Support\Facades\Validator;

/**
 * 
 * @group Trender
 */
class TenderController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @queryParam q string
     * @queryParam category_ids object
     * @queryParam cities object
     */
    public function index(SearchTenderRequest $request)
    {
        $tenders = Tender::where(fn ($query) => $query->whereRaw('LOWER(title) LIKE ? ', ['%' . strtolower($request->q) . '%'])
            ->orWhereRaw('LOWER(description) LIKE ? ', ['%' . strtolower($request->q) . '%']));

        if ($request->category_ids) {
            $tenders = $tenders->whereHas('institution', fn ($query) => $query->whereHas('categories', fn ($query) => $query->whereIn('category_id', $request->category_ids)));
        }

        if ($request->cities) {
            $tenders = $tenders->whereHas('institution', fn ($query) => $query->whereIn('city', $request->cities));
        }

        $tenders = $tenders->get();

        return TrenderHomeResource::collection($tenders);
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @bodyParam details object. Example: [
     *  {
     *     "name": "text",
     *     "data": {
     *      "validator": {
     *  "required": true,
     * "error_message": "Invalid parameters"
     * }
     * }
     *  }
     * ]
     */
    public function store(StoreTenderRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $photoPath = $request->file('image')->store('public/ProfileImage');
            $data['image'] = $photoPath;
        }
        $tender = auth()->user()->institution->tenders()->create($data);

        foreach ($data['details'] as $tenderDetails) {

            if (is_array($tenderDetails['data'])) {
                $tenderDetails['data'] = json_encode($tenderDetails['data']);
            }

            $vaildator = Validator::make($tenderDetails, [
                'name' => ['required', 'string'],
                'data' => ['present'],
            ]);

            $tender->tenderDetails()->create($vaildator->validated());
        }

        return $tender;
    }

    /**
     */
    public function show(Tender $trender)
    {
        $trender->load('tenderDetails');

        return new TrenderResource($trender);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTenderRequest $request, Tender $tender)
    {
        //
    }

    /**
     * @response [
    {
        "id": 25,
        "title": "Ut voluptates nesciunt nostrum temporibus.",
        "image": null,
        "description": "Magni velit quia sequi minus qui occaecati nihil. Consequatur consequatur soluta molestias rerum eligendi sequi error. Rerum in excepturi ab eum. Error laborum rerum mollitia rerum incidunt.",
        "columns_number": 1,
        "created_at": "2023-09-28T12:02:54.000000Z",
        "ended_at": null,
        "offers_count": 1,
        "best_offer_from": "Shawn Bechtelar PhD",
        "offers": [
            {
                "id": 11,
                "trender_name": "Ut voluptates nesciunt nostrum temporibus.",
                "for": "Elta Crooks Jr.",
                "work_on_it": "2023-10-01T06:31:20.000000Z",
                "work_ended_it": null,
                "company_name": "Shawn Bechtelar PhD"
            }
        ]
    }
]
     */

    public function myTrenders()
    {
        $trenders = auth()->user()->institution->tenders()->with('offers')->get();

        return TrenderResource::collection($trenders);
    }

    public function close(Tender $tender)
    {
        $tender->ended_at = now();
        $tender->save();

        return response()->noContent();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tender $tender)
    {
        $tender->delete();

        return response()->noContent();
    }
}
